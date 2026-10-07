<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Dispensasi;
use App\Models\Absensi;
use App\Models\Mahasiswa;
use App\Models\Ruangan;
use App\Models\RoomSequence;
use App\Models\ShiftSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Carbon\CarbonPeriod;
class AbsensiController extends Controller
{
    // Koordinat Pusat RSUD SLG
    protected $rsudLat = -7.82159559;
    protected $rsudLng = 112.05786417;

   /**
    * Sesi masuk dianggap "hangus" (lupa absen pulang) sehingga mahasiswa boleh
    * absen masuk lagi di hari berikutnya. Diberi kelonggaran shift malam untuk
    * checkout pagi hari sampai jam 10. Sesi hangus TIDAK dapat kredit (jadi Alfa).
    */
   private function sesiHangus($jamMasukStr, Carbon $now = null)
   {
       if (!$jamMasukStr) return false;
       $now = $now ?: Carbon::now();
       try { $masuk = Carbon::parse($jamMasukStr); } catch (\Exception $e) { return false; }
       $bedaHari = $masuk->toDateString() !== $now->toDateString();
       $elapsed  = $masuk->diffInHours($now);
       return $elapsed > 16 || ($bedaHari && $now->hour >= 10);
   }

   public function card($token)
    {
        try {
            // if (!auth()->check()) return redirect()->route('login');

            // $currentUser = auth()->user(); 
            
            // Tambahkan relasi 'user' agar kita bisa baca device_id mahasiswanya
            $mahasiswa = Mahasiswa::with(['mou', 'user'])->where('share_token', $token)->firstOrFail();

            // FIX KEAMANAN: Izinkan jika yang buka adalah pemiliknya ATAU dia adalah Admin
            // if ($mahasiswa->user_id !== $currentUser->id && $currentUser->role !== 'admin') {
            //     abort(403, 'Unauthorized action.');
            // }

            $now = Carbon::now();
            $today = $now->toDateString();
            
            // FIX TAMPILAN: Gunakan data akun mahasiswa, BUKAN akun yang sedang login (jika admin)
            $mhsUser = $mahasiswa->user;
            $deviceStatus = is_null($mhsUser->device_id) ? 'need_register' : 'registered';

            $data = [
                'mahasiswa' => $mahasiswa,
                'user' => $mhsUser, // Pass data mahasiswa
                'deviceStatus' => $deviceStatus,
                'absenHariIni' => null,
                'riwayat' => [],
                'dispensasiAktif' => null,
                'ruangan' => null,
                'todayShift' => null,
                'scheduleInfo' => 'Memuat...',
                'shiftCalendar' => [],
                'error_state' => null,
            ];

            if ($mahasiswa->status !== 'aktif') {
                $data['error_state'] = 'Akun Nonaktif / Masa Magang Selesai.';
                return view('absensi.card', $data);
            }

            // Sequence Ruangan
            $sequence = RoomSequence::where('mahasiswa_id', $mahasiswa->id)
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->with('ruangan')->first();

            if (!$sequence) {
                $data['error_state'] = 'Tidak ada jadwal ruangan hari ini.';
                return view('absensi.card', $data);
            }

            $data['ruangan'] = $sequence->ruangan;

            // Logic Tampilan Jadwal (Shift/Non-Shift)
            if ($sequence->ruangan->kategori === 'shift') {
                $schedule = ShiftSchedule::where('mahasiswa_id', $mahasiswa->id)->where('tanggal', $today)->first();
                if (!$schedule) {
                    $data['error_state'] = 'Jadwal Shift belum diisi.';
                    $data['scheduleInfo'] = 'Jadwal Kosong';
                } elseif (strtolower($schedule->shift_type) === 'libur') {
                    $data['error_state'] = 'Jadwal: LIBUR.';
                    $data['scheduleInfo'] = 'LIBUR';
                    $data['todayShift'] = 'Libur';
                } else {
                    $data['todayShift'] = $schedule->shift_type;
                    $times = $this->getShiftTimes($schedule->shift_type, $now, $sequence->ruangan->id);
                    if(isset($times['start']) && isset($times['end'])) {
                        $data['scheduleInfo'] = strtoupper($schedule->shift_type) . " (" . $times['start']->format('H:i') . " - " . $times['end']->format('H:i') . ")";
                    } else {
                        $data['scheduleInfo'] = strtoupper($schedule->shift_type);
                    }
                }
            } else {
                // Non-Shift Logic
                $day = $now->dayOfWeekIso;
                if ($day >= 1 && $day <= 4) { // Senin-Kamis
                    $data['todayShift'] = 'Reguler';
                    $data['scheduleInfo'] = 'Regular (07:15 - 15:30)';
                } elseif ($day == 5) { // Jumat
                    $data['todayShift'] = 'Jumat';
                    $data['scheduleInfo'] = 'Jumat (07:00 - 14:30)';
                } else {
                    $data['error_state'] = 'Hari Libur.';
                    $data['scheduleInfo'] = 'Akhir Pekan';
                }
            }

            // Load Dispensasi
           // Load Dispensasi
            $data['dispensasiAktif'] = Dispensasi::where('mahasiswa_id', $mahasiswa->id)
                ->where('status', 'approved')
                ->where('kategori', 'biasa') // <--- TAMBAHKAN BARIS INI
                ->whereDate('tanggal_mulai', '<=', $today)
                ->whereDate('tanggal_selesai', '>=', $today)->first();

            // Ambil absen terbaru
            $lastAbsen = Absensi::where('mahasiswa_id', $mahasiswa->id)->latest()->first();

            if ($lastAbsen && $lastAbsen->type === 'masuk') {
                // Sesi masuk yang belum checkout -> hangus bila sudah ganti hari / lewat window.
                if ($this->sesiHangus($lastAbsen->jam_masuk, $now)) {
                    $data['absenHariIni'] = null;
                    $data['error_state'] = 'Sesi sebelumnya hangus karena lupa absen pulang (dihitung Alfa). Silakan absen masuk untuk hari ini. Jika ingin dinilai, ajukan Dispensasi Lupa Pulang dari Dashboard Magang (maks 2 hari).';
                } else {
                    $data['absenHariIni'] = $lastAbsen;
                    if($data['error_state']) $data['error_state'] = null;
                }
            }

            // Load Riwayat
            $data['riwayat'] = Absensi::where('mahasiswa_id', $mahasiswa->id)
                ->orderBy('created_at', 'desc')->limit(10)->get();

            return view('absensi.card', $data);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error Buka ID Card: ' . $e->getMessage());
            return abort(404, 'ID Card tidak ditemukan.');
        }
    }

    // =========================================================================
    // 2. TOGGLE ABSEN (INTI SISTEM)
    // =========================================================================
    public function toggle(Request $request, $token)
    {
        // if (!auth()->check()) return redirect()->route('login');
        
$mahasiswa = Mahasiswa::with('user')->where('share_token', $token)->first();

        // 1. CEK: Pastikan data mahasiswa ada (cegah error "property on null" saat token tidak valid)
        if (!$mahasiswa) {
            return back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        // 2. CEK: Pastikan masa magang masih aktif
        if ($mahasiswa->status !== 'aktif') {
            return back()->with('error', 'Gagal: Masa magang untuk ID Card ini sudah tidak aktif. Silakan gunakan ID Card periode terbaru Anda dari Dashboard.');
        }

    // 3. CEK: Pastikan relasi user ada
    if (!$mahasiswa->user) {
        return back()->with('error', 'Akun user untuk mahasiswa ini tidak terdaftar.');
    }

    $user = $mahasiswa->user; 
        
// if ($user->id != 120) { 
//             // Validasi Device (Hanya dijalankan jika BUKAN user 120)
//             $clientDeviceId = $request->input('device_id');
//             if (!$user->device_id) return back()->with('error', 'Perangkat belum didaftarkan.');
//             if ($user->device_id !== $clientDeviceId) return back()->with('error', 'Perangkat tidak dikenali.');
//         }
        // Validasi Device
        $clientDeviceId = $request->input('device_id');
        if (!$user->device_id) return back()->with('error', 'Perangkat belum didaftarkan.');
        if ($user->device_id !== $clientDeviceId) return back()->with('error', 'Perangkat tidak dikenali.');
// Wajibkan koordinat terisi
if (!$request->filled('lat') || !$request->filled('lng')) {
    return back()->with('error', 'Gagal: Lokasi GPS tidak terdeteksi. Pastikan GPS aktif, izin lokasi diberikan, dan akses web menggunakan HTTPS.');
}

// Validasi ketat titik koordinat radius 300 meter
if (!$this->isInRsudArea($request->lat, $request->lng, 300)) {
    return back()->with('error', 'Gagal: Anda berada di luar jangkauan RSUD SLG. Silakan mendekat ke area rumah sakit.');
}
        $now = Carbon::now();
        $today = $now->toDateString();
     $lastAbsen = Absensi::where('mahasiswa_id', $mahasiswa->id)->latest()->first();

$isCheckout = false;
if ($lastAbsen && $lastAbsen->type === 'masuk') {
    // Proses PULANG hanya jika sesi masuknya BELUM hangus (belum ganti hari / masih di window).
    // Jika sudah hangus -> bukan checkout, melainkan mulai sesi masuk baru (sesi lama jadi Alfa).
    if (!$this->sesiHangus($lastAbsen->jam_masuk, $now)) {
        $isCheckout = true;
    }
}

        // =====================================================================
        // SKENARIO KELUAR (PULANG)
        // =====================================================================
        if ($isCheckout) {
            // 1. Ambil Jam Masuk (Safe Carbon)
            $rawJamMasuk = $lastAbsen->jam_masuk;
            try {
                $jamMasuk = $rawJamMasuk ? Carbon::parse($rawJamMasuk) : $now->copy();
            } catch (\Exception $e) {
                $jamMasuk = $now->copy();
            }

            // 2. Ambil Ruangan
            $sequence = RoomSequence::where('mahasiswa_id', $mahasiswa->id)
                ->where('start_date', '<=', $today)->where('end_date', '>=', $today)
                ->with('ruangan')->first();
            
            // Default General jika tidak ada sequence (jarang terjadi)
            $roomId = $sequence ? $sequence->ruangan->id : 0; 

            // 3. [FIX UTAMA] CEK SHIFT DARI DATABASE JADWAL
            // Kita cari jadwal PADA TANGGAL DIA MASUK (bukan tanggal hari ini, jaga2 shift malam lintas hari)
            $tanggalMasuk = $jamMasuk->toDateString();
            
            $jadwalDb = ShiftSchedule::where('mahasiswa_id', $mahasiswa->id)
                ->where('tanggal', $tanggalMasuk)
                ->first();

            if ($jadwalDb) {
                // Jika ada di tabel shift, pakai itu (Pagi/Siang/Malam)
                $shiftSaatMasuk = $jadwalDb->shift_type;
            } else {
                // Jika tidak ada di tabel shift (berarti Non-Shift / Reguler)
                $dayIso = $jamMasuk->dayOfWeekIso;
                if ($dayIso == 5) $shiftSaatMasuk = 'Jumat';
                elseif ($dayIso >= 1 && $dayIso <= 4) $shiftSaatMasuk = 'Reguler';
                else $shiftSaatMasuk = 'Libur';
            }

            // 4. VALIDASI WAKTU
            // Kirim shift yang sudah didapat dari DB
            $cekPulang = $this->validateStrictExit($shiftSaatMasuk, $jamMasuk, $now, $roomId);
            
            if (!$cekPulang['status']) {
                return back()->with('error', $cekPulang['message']); 
            }

            // 5. VALIDASI LOKASI
            if ($request->filled('lat') && $request->lat != null) {
                if (!$this->isInRsudArea($request->lat, $request->lng, 300)) {
                    return back()->with('error', 'Gagal: Checkout harus di area RSUD.');
                }
            }

            // 6. SIMPAN
            $durasi = $jamMasuk->diffInMinutes($now);
            Absensi::create([
                'mahasiswa_id' => $mahasiswa->id,
                'jam_masuk' => $jamMasuk,
                'jam_keluar' => $now,
                'type' => 'keluar',
                'durasi_menit' => $durasi,
                'keterangan' => $shiftSaatMasuk, // Simpan agar data kedepannya rapi
                'latitude' => $request->lat,
                'longitude' => $request->lng,
                'location_accuracy' => $request->acc,
            ]);

            return back()->with('success', 'Absen PULANG Berhasil.');
        }

        // =====================================================================
        // SKENARIO MASUK
        // =====================================================================

        // Jika sesi MASUK sebelumnya tidak pernah checkout (>14 jam / beda hari),
        // tandai record lama agar terlihat di laporan (tanpa memalsukan jam pulang).
        if ($lastAbsen && $lastAbsen->type === 'masuk' && !$isCheckout) {
            if (strpos((string) $lastAbsen->keterangan, 'Tanpa Checkout') === false) {
                $lastAbsen->keterangan = trim(($lastAbsen->keterangan ?? '') . ' (Tanpa Checkout)');
                $lastAbsen->save();
            }
        }

        $sequence = RoomSequence::where('mahasiswa_id', $mahasiswa->id)
            ->where('start_date', '<=', $today)->where('end_date', '>=', $today)
            ->with('ruangan')->first();

        if (!$sequence) return back()->with('error', 'Tidak ada jadwal ruangan.');
        
        $targetShift = 'Reguler'; 
        if ($sequence->ruangan->kategori === 'shift') {
            $schedule = ShiftSchedule::where('mahasiswa_id', $mahasiswa->id)->where('tanggal', $today)->first();
            if (!$schedule) return back()->with('error', 'Shift belum diisi.');
            if ($schedule->shift_type === 'Libur') return back()->with('error', 'Hari ini LIBUR.');
            $targetShift = $schedule->shift_type;
        } else {
            $day = $now->dayOfWeekIso;
            if ($day == 5) $targetShift = 'Jumat';
            elseif ($day > 5) return back()->with('error', 'Hari Libur.');
        }

        // FIX: Hanya blokir scan absen manual HARI INI jika dia sedang Izin Biasa (Full Day)
        $isIzinBiasa = Dispensasi::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'approved')
            ->where('kategori', 'biasa') // Tambahkan baris ini
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->exists();
            
        if ($isIzinBiasa) return back()->with('error', 'Anda sedang izin (Dispensasi Biasa).');

        // Validasi Masuk (Kirim ID Ruangan untuk Merak Check)
        $cekMasuk = $this->validateStrictEntry($targetShift, $now, $sequence->ruangan->id);
        if (!$cekMasuk['status']) return back()->with('error', $cekMasuk['message']);

        if ($request->filled('lat') && !$this->isInRsudArea($request->lat, $request->lng, 300)) {
            return back()->with('error', 'Gagal: Di luar jangkauan RSUD.');
        }

        Absensi::create([
            'mahasiswa_id' => $mahasiswa->id,
            'jam_masuk' => $now,
            'type' => 'masuk',
            'keterangan' => $targetShift,
            'latitude' => $request->lat,
            'longitude' => $request->lng,
            'location_accuracy' => $request->acc,
        ]);

        return back()->with('success', 'Absen MASUK Berhasil.');
    }

    /**
     * Absen Pulang untuk sesi yang LUPA CHECKOUT (backdate maksimal 2 hari).
     * Jam pulang = jam selesai shift/jadwal pada tanggal masuk sesi tsb.
     */
    public function backdatePulang(Request $request, $token)
    {
        $mahasiswa = Mahasiswa::where('share_token', $token)->first();
        if (!$mahasiswa) return back()->with('error', 'Data mahasiswa tidak ditemukan.');

        $batasBawah = Carbon::today()->subDays(2)->startOfDay();   // 2 hari ke belakang
        $batasAtas  = Carbon::today()->startOfDay();                // sebelum hari ini

        // Cari sesi MASUK yang belum ada checkout (dalam 2 hari terakhir, bukan hari ini)
        $target = null;
        $masukRecs = Absensi::where('mahasiswa_id', $mahasiswa->id)
            ->where('type', 'masuk')
            ->whereBetween('jam_masuk', [$batasBawah, $batasAtas])
            ->orderBy('jam_masuk', 'desc')->get();
        foreach ($masukRecs as $mk) {
            $sudahKeluar = Absensi::where('mahasiswa_id', $mahasiswa->id)
                ->where('type', 'keluar')->where('jam_masuk', $mk->jam_masuk)->exists();
            if (!$sudahKeluar) { $target = $mk; break; }
        }

        if (!$target) {
            return back()->with('error', 'Tidak ada sesi masuk yang lupa checkout dalam 2 hari terakhir.');
        }

        $jamMasuk = Carbon::parse($target->jam_masuk);
        $tglMasuk = $jamMasuk->toDateString();

        // Tentukan ruangan & shift pada tanggal masuk tsb
        $sequence = RoomSequence::where('mahasiswa_id', $mahasiswa->id)
            ->where('start_date', '<=', $tglMasuk)->where('end_date', '>=', $tglMasuk)
            ->with('ruangan')->first();
        $roomId = $sequence && $sequence->ruangan ? $sequence->ruangan->id : 0;

        $jadwalDb = ShiftSchedule::where('mahasiswa_id', $mahasiswa->id)->where('tanggal', $tglMasuk)->first();
        if ($jadwalDb) {
            $shift = $jadwalDb->shift_type;
        } else {
            $dayIso = $jamMasuk->dayOfWeekIso;
            $shift = $dayIso == 5 ? 'Jumat' : (($dayIso >= 1 && $dayIso <= 4) ? 'Reguler' : 'Libur');
        }

        // Jam selesai shift
        $times = $this->getShiftTimes($shift, $jamMasuk, $roomId);
        $jamKeluar = ($times && !empty($times['end'])) ? $times['end']->copy() : $jamMasuk->copy()->addHours(7);
        if ($jamKeluar->lte($jamMasuk)) $jamKeluar = $jamMasuk->copy()->addHours(7);

        Absensi::create([
            'mahasiswa_id' => $mahasiswa->id,
            'jam_masuk'    => $jamMasuk,
            'jam_keluar'   => $jamKeluar,
            'type'         => 'keluar',
            'durasi_menit' => $jamMasuk->diffInMinutes($jamKeluar),
            'keterangan'   => 'Backdate Lupa Checkout (' . $shift . ')',
            // Catat di TANGGAL sesi yang lupa pulang, bukan tanggal hari ini (tanggal input/ACC)
            'created_at'   => $jamKeluar,
            'updated_at'   => $jamKeluar,
        ]);

        return back()->with('success', 'Absen pulang (lupa checkout) untuk ' . $jamMasuk->isoFormat('D MMM') . ' berhasil dicatat (jam pulang: ' . $jamKeluar->format('H:i') . ').');
    }

    // =========================================================================
    // 3. REGISTER DEVICE & HELPER
    // =========================================================================
 // Tambahkan parameter $token
    public function registerDevice(Request $request, $token) {
        $request->validate(['device_id' => 'required']);
        
        // Cari user berdasarkan token QR-nya
        $mahasiswa = Mahasiswa::where('share_token', $token)->firstOrFail();
        $user = $mahasiswa->user;
        
        if ($user->device_id) return response()->json(['status'=>'error', 'message'=>'Akun sudah terkunci.'], 400);
        $user->update(['device_id' => $request->device_id]);
        return response()->json(['status'=>'success']);
    }

    // --- HELPER TIME LOGIC ---
    // private function getShiftTimes($shift, $refDate, $roomId) {
    //     // $refDate harus Carbon object
    //     $d = $refDate->copy()->startOfDay();
    //     $t = ['start' => null, 'end' => null];

    //     // 1. Non-Shift
    //     if ($shift === 'Reguler') { 
    //         $t['start'] = $d->copy()->setTime(7, 15); 
    //         $t['end']   = $d->copy()->setTime(15, 30); 
    //     }
    //     elseif ($shift === 'Jumat') { 
    //         $t['start'] = $d->copy()->setTime(7, 0); 
    //         $t['end']   = $d->copy()->setTime(14, 30); 
    //     }
    //     // 2. Shift Normal
    //     elseif ($shift === 'Pagi') { 
    //         $t['start'] = $d->copy()->setTime(7, 0); 
    //         $t['end']   = $d->copy()->setTime(14, 0); 
    //     }
    //     elseif ($shift === 'Siang') { 
    //         $t['start'] = $d->copy()->setTime(14, 0); 
    //         $t['end']   = $d->copy()->setTime(21, 0); 
    //     }
    //     // 3. Shift Malam (Cek Ruang Merak ID 6)
    //     elseif ($shift === 'Malam') { 
    //         if ($roomId == 6) {
    //             // Merak: 20:00 - 07:00
    //             $t['start'] = $d->copy()->setTime(20, 0); 
    //         } else {
    //             // Normal: 21:00 - 07:00
    //             $t['start'] = $d->copy()->setTime(21, 0); 
    //         }
    //         $t['end'] = $d->copy()->addDay()->setTime(7, 0); 
    //     }
    //     // Fallback
    //     else {
    //         $t['start'] = $d->copy()->setTime(7, 0);
    //         $t['end']   = $d->copy()->setTime(14, 0);
    //     }
    //     return $t;
    // }
// private function getShiftTimes($shift, $refDate, $roomId) {
//         // $refDate harus Carbon object
//         $d = $refDate->copy()->startOfDay();
//         $t = ['start' => null, 'end' => null];

//         // --- TAMBAHAN: CEK RUANG RR (Berdasarkan ID) ---
//         if ($roomId == 57) {
//             // Cek apakah hari Sabtu atau Minggu (Libur)
//             if ($d->isWeekend()) {
//                 // Kembalikan null untuk menandakan libur
//                 $t['start'] = null;
//                 $t['end']   = null;
//                 return $t; 
//             }

//             // Jika Senin - Jumat, shift selalu 07:00 - 15:30
//             $t['start'] = $d->copy()->setTime(7, 0);
//             $t['end']   = $d->copy()->setTime(15, 30);
            
//             return $t; // Return langsung agar logika bawah tidak jalan
//         }
//         // --- END TAMBAHAN RR ---


//         // --- TAMBAHAN: CEK INSTALASI GIZI ---
//         // Cari nama ruangan untuk mengecek Gizi
//         $ruangan = \App\Models\Ruangan::find($roomId);
//         // Pastikan nama ruangan di database mengandung kata "Gizi" (Case insensitive)
//         $isGizi = $ruangan && \Illuminate\Support\Str::contains(strtolower($ruangan->nm_ruangan), 'gizi');

//         if ($isGizi) {
//             if ($shift === 'Pagi') {
//                 // Pagi 04:30 - 12:40
//                 $t['start'] = $d->copy()->setTime(4, 30);
//                 $t['end']   = $d->copy()->setTime(12, 40);
//             } elseif ($shift === 'Siang') {
//                 // Siang 11:20 - 19:30
//                 $t['start'] = $d->copy()->setTime(11, 20);
//                 $t['end']   = $d->copy()->setTime(19, 30);
//             } elseif ($shift === 'Reguler' || $shift === 'Non-Shift') {
//                 // Reguler 07:15 - 15:25
//                 $t['start'] = $d->copy()->setTime(7, 15);
//                 $t['end']   = $d->copy()->setTime(15, 25);
//             } else {
//                 // Fallback jika ada shift lain (misal Libur)
//                 $t['start'] = $d->copy()->setTime(7, 0);
//                 $t['end']   = $d->copy()->setTime(14, 0);
//             }
//             return $t; // <--- Return langsung biar logika bawah tidak jalan
//         }
//         // --- END TAMBAHAN GIZI ---

//         // 1. Non-Shift (Logic Lama untuk ruangan lain)
//         if ($shift === 'Reguler') { 
//             $t['start'] = $d->copy()->setTime(7, 15);  
//             $t['end']   = $d->copy()->setTime(15, 30); 
//         }
//         elseif ($shift === 'Jumat') { 
//             $t['start'] = $d->copy()->setTime(7, 0); 
//             $t['end']   = $d->copy()->setTime(14, 00); 
//         }
//         // 2. Shift Normal
//         elseif ($shift === 'Pagi') { 
//             $t['start'] = $d->copy()->setTime(7, 0); 
//             $t['end']   = $d->copy()->setTime(14, 0); 
//         }
//         elseif ($shift === 'Siang') { 
//             $t['start'] = $d->copy()->setTime(14, 0); 
//             $t['end']   = $d->copy()->setTime(21, 0); 
//         }
//         // 3. Shift Malam (Cek Ruang Merak ID 6)
//         elseif ($shift === 'Malam') { 
//             if ($roomId == 6) { // Sesuaikan ID Merak jika perlu
//                 $t['start'] = $d->copy()->setTime(20, 0); 
//             } else {
//                 $t['start'] = $d->copy()->setTime(21, 0); 
//             }
//             $t['end'] = $d->copy()->addDay()->setTime(7, 0); 
//         }
//         // Fallback
//         else {
//             $t['start'] = $d->copy()->setTime(7, 0);
//             $t['end']   = $d->copy()->setTime(14, 0);
//         }
        
//         return $t;
//     }
private function getShiftTimes($shift, $refDate, $roomId) {
        $d = $refDate->copy()->startOfDay();
        $t = ['start' => null, 'end' => null];

        // =====================================================================
        // 1. CEK JAM KERJA DINAMIS DARI DATABASE (FITUR BARU)
        // =====================================================================
        // Sistem akan mencari apakah ruangan ini sudah punya custom shift
        $customShift = \App\Models\RoomShift::where('ruangan_id', $roomId)
                                            ->where('nama_shift', $shift)
                                            ->first();

        if ($customShift) {
            // Set jam masuk berdasarkan data di database
            $t['start'] = $d->copy()->setTimeFromTimeString($customShift->jam_masuk);
            
            // Set jam keluar berdasarkan data di database
            $t['end'] = $d->copy()->setTimeFromTimeString($customShift->jam_keluar);

            // Jika shift malam (lintas_hari = true), tambahkan 1 hari ke jam keluar
            if ($customShift->lintas_hari) {
                $t['end']->addDay();
            }

            return $t; // Langsung kembalikan hasil, abaikan kode legacy di bawah
        }

        // =====================================================================
        // 2. FALLBACK LEGACY (JIKA RUANGAN BELUM DIATUR JAMNYA)
        // =====================================================================

        // --- CEK RUANG RR (Berdasarkan ID) ---
        if ($roomId == 57) {
            if ($d->isWeekend()) {
                $t['start'] = null;
                $t['end']   = null;
                return $t; 
            }
            $t['start'] = $d->copy()->setTime(7, 0);
            $t['end']   = $d->copy()->setTime(15, 30);
            return $t; 
        }

        // --- CEK INSTALASI GIZI ---
        $ruangan = \App\Models\Ruangan::find($roomId);
        $isGizi = $ruangan && \Illuminate\Support\Str::contains(strtolower($ruangan->nm_ruangan), 'gizi');

        if ($isGizi) {
            if ($shift === 'Pagi') {
                $t['start'] = $d->copy()->setTime(4, 30);
                $t['end']   = $d->copy()->setTime(12, 40);
            } elseif ($shift === 'Siang') {
                $t['start'] = $d->copy()->setTime(11, 20);
                $t['end']   = $d->copy()->setTime(19, 30);
            } elseif ($shift === 'Reguler' || $shift === 'Non-Shift') {
                $t['start'] = $d->copy()->setTime(7, 15);
                $t['end']   = $d->copy()->setTime(15, 25);
            } else {
                $t['start'] = $d->copy()->setTime(7, 0);
                $t['end']   = $d->copy()->setTime(14, 0);
            }
            return $t; 
        }

        // 1. Non-Shift (Logic Lama untuk ruangan lain)
        if ($shift === 'Reguler') { 
            $t['start'] = $d->copy()->setTime(7, 15);  
            $t['end']   = $d->copy()->setTime(15, 30); 
        }
        elseif ($shift === 'Jumat') { 
            $t['start'] = $d->copy()->setTime(7, 0); 
            $t['end']   = $d->copy()->setTime(14, 00); 
        }
        // 2. Shift Normal
        elseif ($shift === 'Pagi') { 
            $t['start'] = $d->copy()->setTime(7, 0); 
            $t['end']   = $d->copy()->setTime(14, 0); 
        }
        elseif ($shift === 'Siang') { 
            $t['start'] = $d->copy()->setTime(14, 0); 
            $t['end']   = $d->copy()->setTime(21, 0); 
        }
        // 3. Shift Malam (Cek Ruang Merak ID 6)
        elseif ($shift === 'Malam') { 
            if ($roomId == 6) { 
                $t['start'] = $d->copy()->setTime(20, 0); 
            } else {
                $t['start'] = $d->copy()->setTime(21, 0); 
            }
            $t['end'] = $d->copy()->addDay()->setTime(7, 0); 
        }
        // Fallback
        else {
            $t['start'] = $d->copy()->setTime(7, 0);
            $t['end']   = $d->copy()->setTime(14, 0);
        }
        
        return $t;
    }
    // VALIDASI MASUK (30 MENIT TOLERANSI)
// VALIDASI MASUK (30 MENIT TOLERANSI)
    private function validateStrictEntry($shift, $now, $roomId) {
        $times = $this->getShiftTimes($shift, $now, $roomId);
        $start = $times['start'];

        if ($shift === 'Malam' && $now->hour < 5) return ['status'=>false, 'message'=>'Gagal: Shift malam harusnya masuk kemarin.'];
        
        // --- PERBAIKAN: Ubah subHour() menjadi subMinutes(30) ---
        if ($now->lt($start->copy()->subMinutes(30))) return ['status'=>false, 'message'=>'Terlalu awal (Maksimal absen 30 menit sebelum shift).'];
        
        if ($now->gt($start->copy()->addMinutes(15))) {
            $telat = $now->diffInMinutes($start);
            return ['status'=>false, 'message'=>"Terlambat $telat menit. Batas 15 menit."];
        }
        return ['status'=>true];
    }


    // VALIDASI PULANG (ANTI ERROR & TOLERANSI 0 MENIT)
    private function validateStrictExit($shift, $jamMasukRaw, $now, $roomId) {
        
        // 1. Safe Date Creation
        try {
            if ($jamMasukRaw instanceof Carbon) {
                $baseDate = $jamMasukRaw->clone(); 
            } elseif (!empty($jamMasukRaw)) {
                $baseDate = Carbon::parse($jamMasukRaw);
            } else {
                $baseDate = Carbon::now();
            }
        } catch (\Exception $e) {
            $baseDate = Carbon::now();
        }

        // 2. Hitung Jadwal Pulang
        $times = $this->getShiftTimes($shift, $baseDate, $roomId);
        $end = $times['end'];

        // 3. Cek Waktu Pulang (Toleransi 0 menit)
        if ($now->lt($end)) {
            $secondsLeft = $now->diffInSeconds($end);
            // Jika kurang dari 60 detik (0 menit), boleh pulang.
            if ($secondsLeft > 60) {
                $menit = ceil($secondsLeft / 60);
                return ['status'=>false, 'message'=>"Belum waktunya pulang ($menit menit lagi)."];
            }
        }

        // 4. Expired 12 Jam
        if ($now->gt($end->copy()->addHours(12))) {
            return ['status'=>false, 'message'=>'Sesi absen kadaluarsa.'];
        }

        return ['status'=>true];
    }

    private function isInRsudArea($lat, $lng, $rad) {
        if (empty($lat) || empty($lng)) return false;
        $earthRadius = 6371000;
        $dLat = deg2rad($this->rsudLat - $lat);
        $dLon = deg2rad($this->rsudLng - $lng);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat)) * cos(deg2rad($this->rsudLat)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return ($earthRadius * $c) <= $rad;
    }
// API Export Presensi Kampus (JSON - Format Matrix)
    public function exportPresensiKampusJson(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $query = \App\Models\Mahasiswa::with([
            'absensis' => function ($q) use ($request) {
                $q->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            },
            'dispensasis' => function ($q) use ($request) {
                $q->where('status', 'approved');
            },
            'mou'
        ]);

        // Filter Kampus & Prodi
        if ($request->filled('mou_id')) {
            $query->where('mou_id', $request->mou_id);
        }
        if ($request->filled('prodi')) {
            $query->where('prodi', $request->prodi);
        }

        $mahasiswas = $query->get();
        $period = \Carbon\CarbonPeriod::create($request->start_date, $request->end_date);
        $result = [];
        $no = 1;

        foreach ($mahasiswas as $mhs) {
            // Struktur Awal Baris (Kiri)
            $row = [
                'No' => $no++,
                'Nama Mahasiswa' => $mhs->nm_mahasiswa,
                'Prodi' => $mhs->prodi ?? '-',
                'Instansi' => $mhs->univ_asal ?? '-',
            ];

            $totalHadir = 0;
            $totalIzin = 0;

            // Generate Kolom Tanggal Dinamis ke Kanan
            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                $colName = $date->format('d/m'); // Nama kolom jadi 01/06, 02/06 dst
$isDispen = $mhs->dispensasis->contains(function ($d) use ($dateStr) {
    // Parsing ke format String (Y-m-d) agar aman saat dibandingkan
    $mulai = $d->tanggal_mulai ? \Carbon\Carbon::parse($d->tanggal_mulai)->format('Y-m-d') : null;
    $selesai = $d->tanggal_selesai ? \Carbon\Carbon::parse($d->tanggal_selesai)->format('Y-m-d') : null;
    
    return $dateStr >= $mulai && $dateStr <= $selesai;
});

                if ($isDispen) {
                    $row[$colName] = 'Izin';
                    $totalIzin++;
                    continue;
                }

                $absenMasuk = $mhs->absensis->first(function ($a) use ($dateStr) {
                    return $a->type === 'masuk' && $a->created_at && $a->created_at->format('Y-m-d') === $dateStr;
                });

                if ($absenMasuk) {
                    $absenKeluar = $mhs->absensis->first(function ($a) use ($dateStr) {
                        return $a->type === 'keluar' && $a->created_at && $a->created_at->format('Y-m-d') === $dateStr;
                    });
                    
                    $jamM = $absenMasuk->created_at->format('H:i');
                    $jamK = $absenKeluar ? $absenKeluar->created_at->format('H:i') : '?';
                    
                    $row[$colName] = "$jamM - $jamK";
                    $totalHadir++;
                } else {
                    $row[$colName] = '-'; // Kosong / Alpha / Libur
                }
            }

            // Struktur Akhir Baris (Kanan)
            $row['Total Hadir'] = $totalHadir;
            $row['Total Izin'] = $totalIzin;

            $result[] = $row;
        }
        
        return response()->json($result);
    }

    // API Export Nilai Akhir (JSON)
public function exportNilaiAkhirJson(Request $request)
{
    $query = \App\Models\Mahasiswa::with(['mou', 'ruangan', 'roomSequences.ruangan', 'absensis']);

    if ($request->filled('mou_id')) {
        $query->where('mou_id', $request->mou_id);
    }
    if ($request->filled('prodi')) {
        $query->where('prodi', $request->prodi); 
    }
    
    // PERBAIKAN 1: Gunakan logika "Overlap" agar mahasiswa yang jadwal magangnya 
    // bersinggungan dengan rentang tanggal tetap ditarik.
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->where(function($q) use ($request) {
            $q->whereDate('tanggal_mulai', '<=', $request->end_date)
              ->whereDate('tanggal_berakhir', '>=', $request->start_date);
        });
    }

    $mahasiswas = $query->get();
    $result = [];

    foreach ($mahasiswas as $mhs) {
        $nilaiAbsensi = (float) $mhs->absensi_percentage;
        
        // PERBAIKAN 2: Tarik nilai dari JSON multi-ruangan, lalu ambil rata-ratanya
        $nilaiJson = is_string($mhs->nilai_ruangan_json) 
            ? json_decode($mhs->nilai_ruangan_json, true) 
            : ($mhs->nilai_ruangan_json ?? []);

        $nilaiKaruAngka = 0;
        
        if (!empty($nilaiJson) && is_array($nilaiJson)) {
            // Jika mahasiswa magang di 2 ruangan (misal IGD dan ICU), kita ambil rata-ratanya
            $nilaiKaruAngka = array_sum($nilaiJson) / count($nilaiJson);
        } else {
            // Fallback: Jika data mahasiswa lama yang nilainya masih tersimpan di kolom nilai_karu
            $rawNilaiKaru = $mhs->nilai_karu ?? 0;
            if (is_numeric($rawNilaiKaru)) {
                $nilaiKaruAngka = (float) $rawNilaiKaru;
            } else {
                switch(strtoupper(trim($rawNilaiKaru))) {
                    case 'A': $nilaiKaruAngka = 90; break;
                    case 'B': $nilaiKaruAngka = 75; break;
                    case 'C': $nilaiKaruAngka = 65; break;
                    case 'D': $nilaiKaruAngka = 50; break;
                }
            }
        }

        $bobotAbsensi = $nilaiAbsensi * 0.60;
        $bobotKaru    = $nilaiKaruAngka * 0.40;
        $totalNilai   = round($bobotAbsensi + $bobotKaru, 1);

        $predikat = 'Kurang';
        if ($totalNilai >= 80) { $predikat = 'Baik Sekali'; }
        elseif ($totalNilai >= 70) { $predikat = 'Baik'; }
        elseif ($totalNilai >= 60) { $predikat = 'Cukup'; }

        $tglMulai = $mhs->tanggal_mulai ? \Carbon\Carbon::parse($mhs->tanggal_mulai)->format('d/m/Y') : '-';
        $tglAkhir = $mhs->tanggal_berakhir ? \Carbon\Carbon::parse($mhs->tanggal_berakhir)->format('d/m/Y') : '-';
        $periode = "$tglMulai s/d $tglAkhir";

        $result[] = [
            'Nama Mahasiswa' => $mhs->nm_mahasiswa,
            'Kampus' => $mhs->univ_asal ?? '-',
            'Program Studi' => $mhs->prodi ?? '-',
            'Ruangan' => $mhs->nama_ruangan_saat_ini ?? '-',
            'Periode Magang' => $periode,
            'Nilai Karu (40%)' => round($nilaiKaruAngka, 1),
            'Nilai Absensi (60%)' => round($nilaiAbsensi, 1),
            'Total Nilai' => $totalNilai,
            'Predikat' => $predikat
        ];
    }
    
    return response()->json($result);
}
public function index(Request $request) {
    if (!auth()->check() || auth()->user()->role !== 'admin') abort(403);

    $query = \App\Models\Absensi::with(['mahasiswa', 'mahasiswa.ruangan']);

    // Filter Pencarian, Ruangan, Tipe, Tanggal... (Bawaan)
    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->whereHas('mahasiswa', function ($q) use ($searchTerm) {
            $q->where('nm_mahasiswa', 'like', "%{$searchTerm}%");
        });
    }
    if ($request->filled('ruangan_id')) {
        $query->whereHas('mahasiswa', function ($q) use ($request) {
            $q->where('ruangan_id', $request->ruangan_id);
        });
    }
    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }
    if ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }
    if ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    $absensi = $query->orderBy('created_at', 'desc')->paginate(15);
    $ruangans = \App\Models\Ruangan::all();
    $mous = \App\Models\Mou::all(); 

    // TAMBAHAN: Ambil daftar Prodi unik langsung dari tabel mahasiswa
    $prodis = \App\Models\Mahasiswa::whereNotNull('prodi')
                ->where('prodi', '!=', '')
                ->distinct()
                ->orderBy('prodi', 'asc')
                ->pluck('prodi');

    $stats = [
        'total_today' => \App\Models\Absensi::whereDate('created_at', today())->count(),
        'masuk_today' => \App\Models\Absensi::whereDate('created_at', today())->where('type', 'masuk')->count(),
        'keluar_today' => \App\Models\Absensi::whereDate('created_at', today())->where('type', 'keluar')->count(),
    ];

    return view('absensi.index', compact('absensi', 'ruangans', 'stats', 'mous', 'prodis'));
}
public function generateSertifikatPublik(Request $request, $identifier)
{
    // =========================================================================
    // 1. Deteksi ID atau Token secara Aman (Mencegah MySQL Type Casting Error)
    // =========================================================================
    $query = \App\Models\Mahasiswa::with(['mou', 'absensis', 'shiftSchedules', 'roomSequences.ruangan']);

    // Jika identifier mengandung huruf atau lebih dari sekadar angka (indikasi Token/UUID)
    if (!is_numeric($identifier) || \Illuminate\Support\Str::isUuid($identifier)) {
        $query->where('share_token', $identifier);
    } else {
        // Jika murni angka, asumsikan itu ID
        $query->where('id', $identifier);
    }

    $mahasiswa = $query->first();

    if (!$mahasiswa) {
        return abort(404, "Data mahasiswa tidak ditemukan.");
    }

    // Refresh model untuk memastikan kita ambil data statistik/relasi paling mutakhir
    $mahasiswa->refresh();

    // GATE: pemilik akun wajib mengisi evaluasi magang dulu sebelum unduh sertifikat.
    // Admin / pihak lain (validasi via token) tidak terkena gate.
    if (auth()->check() && (int) auth()->id() === (int) $mahasiswa->user_id && empty($mahasiswa->evaluasi_at)) {
        return redirect()->route('evaluasi.magang.form')
            ->with('error', 'Silakan isi evaluasi magang terlebih dahulu untuk mengunduh sertifikat.');
    }

    // =========================================================================
    // 2. AMBIL STATISTIK DARI MODEL
    // =========================================================================
    $statistik = $mahasiswa->statistik; 
    
    // Pastikan fallback ke 0 jika statistik belum ada
    $totalActualDays = $statistik ? $statistik->hadir : 0; 

    // Hitung total izin dari tabel Dispensasi
    $totalIzin = \App\Models\Dispensasi::where('mahasiswa_id', $mahasiswa->id)
        ->where('status', 'approved')
        ->count();

    // Tentukan Persentase Absensi
    if ($request->filled('override_percentage')) {
        $percentage = (float) $request->override_percentage;
    } else {
        $percentage = (float) $mahasiswa->absensi_percentage;
    }

    // =========================================================================
    // 3. HITUNG NILAI GABUNGAN (ABSENSI 60% + KARU 40%)
    // =========================================================================
    $nilaiAbsensi = $percentage;

    $rawNilaiKaru = $mahasiswa->nilai_karu ?? 0;

    // Evaluasi nilai Karu
// Konversi nilai Karu jika input berupa huruf
    if (is_numeric($rawNilaiKaru)) {
        $nilaiKaruAngka = (float) $rawNilaiKaru;
    } else {
        switch(strtoupper(trim($rawNilaiKaru))) {
            case 'A': $nilaiKaruAngka = 90; break; // Ambil nilai tengah 80-100
            case 'B': $nilaiKaruAngka = 75; break; // Ambil nilai tengah 70-79
            case 'C': $nilaiKaruAngka = 65; break; // Ambil nilai tengah 60-69
            case 'D': $nilaiKaruAngka = 50; break; // Nilai di bawah 60
            default:  $nilaiKaruAngka = 0;   
        }
    }

    // GANTI BAGIAN INI: Cukup panggil Accessor yang sudah kita buat
    $nilaiKaruAngka = $mahasiswa->nilai_karu_final;

    // Hitung pembobotan
    $bobotAbsensi = $nilaiAbsensi * 0.60;  // 60%
    $bobotKaru    = $nilaiKaruAngka * 0.40; // 40%

    
    // Total Nilai Keseluruhan
    $totalNilai   = round($bobotAbsensi + $bobotKaru, 1);

    // Konversi Total Nilai menjadi Predikat Kelulusan (SESUAI PEDOMAN)
    if ($totalNilai >= 80) {          // 80 - 100
        $predikatAkhir = 'Baik Sekali'; // Boleh ditambahkan " (A)" jika perlu
    } elseif ($totalNilai >= 70) {    // 70 - 79.9
        $predikatAkhir = 'Baik';      
    } elseif ($totalNilai >= 60) {    // 60 - 69.9
        $predikatAkhir = 'Cukup';     
    } else {                          // < 60
        $predikatAkhir = 'Kurang';    
    }
    // =========================================================================
    // 4. GENERATE QR CODE & VALIDASI
    // =========================================================================
    
    // Sinkronisasi Token (Gunakan UUID sesuai Model)
    if (empty($mahasiswa->share_token)) {
        $mahasiswa->share_token = (string) \Illuminate\Support\Str::uuid();
        $mahasiswa->save();
    }

    $linkValidasi = route('sertifikat.validasi', $mahasiswa->share_token);
    $qrUrl = "https://quickchart.io/qr?text=" . urlencode($linkValidasi) . "&size=300";
    $qrBase64 = '';
    
    try {
        $imgData = @file_get_contents($qrUrl);
        if ($imgData) {
            $qrBase64 = 'data:image/png;base64,' . base64_encode($imgData);
        }
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Gagal generate QR Code Sertifikat: ' . $e->getMessage());
        $qrBase64 = ''; 
    }

    // =========================================================================
    // 5. RENDER PDF
    // =========================================================================
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('sertifikat.template', [
        'mahasiswa'      => $mahasiswa,
        'nilaiAbsensi'   => $nilaiAbsensi,
        'nilaiKaruAngka' => $nilaiKaruAngka,
        'bobotAbsensi'   => $bobotAbsensi,
        'bobotKaru'      => $bobotKaru,
        'totalNilai'     => $totalNilai,
        'predikatAkhir'  => $predikatAkhir,
        'total_hadir'    => $totalActualDays, 
        'total_izin'     => $totalIzin,       
        'tanggal_terbit' => ($mahasiswa->tanggal_berakhir ? \Carbon\Carbon::parse($mahasiswa->tanggal_berakhir) : \Carbon\Carbon::now())->isoFormat('D MMMM YYYY'),
        'qr_base64'      => $qrBase64 
    ]);

    $pdf->setPaper('a4', 'landscape');
    
    $fileName = 'Sertifikat-' . \Illuminate\Support\Str::slug($mahasiswa->nm_mahasiswa) . '-' . time() . '.pdf';

    if ($request->has('download')) {
        return $pdf->download($fileName);
    }

    return $pdf->stream($fileName)->withHeaders([
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0',
    ]);
}
    // Helper (Private)
    private function fetchRemoteImage($url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1, CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => 'Mozilla/5.0'
        ]);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data ? 'data:image/png;base64,' . base64_encode($data) : null;
    }
public function validasiSertifikat($token)
    {
        try {
            // 1. CEK UNTUK PEGAWAI / PESERTA KEGIATAN (Format: PGW-{id}-{hash})
            if (str_starts_with($token, 'PGW-')) {
                $parts = explode('-', $token);
                if (count($parts) === 3) {
                    $id = $parts[1];
                    $hash = $parts[2];
                    
                    $secretKey = 'sindikat_rsud_slg_secret';
                    
                    // Verifikasi Hash untuk keamanan
                    if ($hash === md5($id . $secretKey)) {
                        $peserta = \App\Models\KegiatanPeserta::with(['kegiatan', 'instansi'])->findOrFail($id);
                        return view('sertifikat.validasi', [
                            'tipe' => 'pegawai',
                            'data' => $peserta
                        ]);
                    }
                }
            }

            // 2. CEK UNTUK MAHASISWA MAGANG (Format UUID Biasa)
            $mahasiswa = \App\Models\Mahasiswa::with(['mou'])
                        ->where('share_token', $token)
                        ->first();
                        
            if ($mahasiswa) {
                return view('sertifikat.validasi', [
                    'tipe' => 'mahasiswa',
                    'data' => $mahasiswa
                ]);
            }

            // Jika tidak ditemukan di kedua tabel
            return abort(404, 'Dokumen Sertifikat tidak ditemukan atau QR Code tidak valid.');
            
        } catch (\Exception $e) {
            return abort(404, 'Terjadi kesalahan saat memvalidasi dokumen.');
        }
    }
    
}