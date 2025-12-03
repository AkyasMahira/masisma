<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Mahasiswa;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    // === Public card (tanpa login, diakses via token) ===
    public function card($token)
    {
        $mahasiswa = Mahasiswa::where('share_token', $token)->firstOrFail();

        $today = Carbon::today();
        $absenHariIni = Absensi::where('mahasiswa_id', $mahasiswa->id)
            ->whereDate('created_at', $today)
            ->latest()
            ->first();

        return view('absensi.card', compact('mahasiswa', 'absenHariIni'));
    }

    // === Tombol toggle (masuk / keluar otomatis + GEOFENCE) ===
    public function toggle(Request $request, $token)
    {
        $mahasiswa = Mahasiswa::where('share_token', $token)->firstOrFail();

        // ===== VALIDASI LOKASI WAJIB ADA =====
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'acc' => 'nullable|numeric',
        ]);

        $lat = (float) $request->lat;
        $lng = (float) $request->lng;
        $acc = $request->acc !== null ? (int) $request->acc : null;

        // Cegah lokasi "aneh": akurasi terlalu jelek dianggap tidak valid
        if ($acc !== null && $acc > 150) {
            return back()->with(
                'error',
                'Sinyal lokasi kurang akurat. Dekatkan ke area RSUD SLG, nyalakan GPS & internet lalu coba lagi.'
            );
        }

        // Wajib di dalam radius 200m dari RSUD Simpang Lima Gumul
        if (!$this->isInRsudArea($lat, $lng, 200)) {
            return back()->with(
                'error',
                'Absen Harus Di Area RSUD Simpang Lima Gumul'
            );
        }

        $today     = Carbon::today();
        $sekarang  = now();

        // Ambil jadwal khusus IT (kalau bukan IT => null)
        $jadwalIT = $this->getJadwalIT($mahasiswa, $today);

        // Ambil absensi terakhir hari ini
        $lastAbsen = Absensi::where('mahasiswa_id', $mahasiswa->id)
            ->whereDate('created_at', $today)
            ->latest()
            ->first();

        // --- Jika belum ada absen hari ini, buat absen masuk ---
        if (!$lastAbsen) {
            // Simpan absensi masuk
            $absen = Absensi::create([
                'mahasiswa_id'      => $mahasiswa->id,
                'jam_masuk'         => $sekarang,
                'type'              => 'masuk',
                'latitude'          => $lat,
                'longitude'         => $lng,
                'location_accuracy' => $acc,
            ]);

            // Jika dia ruangan IT DAN hari kerja yang ada jadwal => cek telat masuk
            if ($jadwalIT && isset($jadwalIT['masuk'])) {
                $jamMasukIdeal = $jadwalIT['masuk']->copy();

                if ($sekarang->gt($jamMasukIdeal)) {
                    // Telat berapa menit
                    $menitTelat = $sekarang->diffInMinutes($jamMasukIdeal);

                    return back()->with(
                        'success',
                        "Anda telat {$menitTelat} menit. Besok jangan telat ya 😊"
                    );
                }
            }

            // Default kalau tidak telat / bukan ruangan IT
            return back()->with(
                'success',
                'Absensi masuk berhasil direkam! Kamu sedang dalam fase praktik, tunggu 3 jam sebelum absen keluar.'
            );
        }

        // --- Jika terakhir adalah absen masuk ---
        if ($lastAbsen->type === 'masuk') {
            $jamMasuk = Carbon::parse($lastAbsen->jam_masuk);

            // Cek cooldown 3 jam sebelum boleh keluar
            $cooldownBerakhir = $jamMasuk->copy()->addHours(3);
            if ($sekarang->lt($cooldownBerakhir)) {
                $menitTersisa = $sekarang->diffInMinutes($cooldownBerakhir);
                $jamTersisa   = floor($menitTersisa / 60);
                $sisaMenit    = $menitTersisa % 60;

                return back()->with(
                    'error',
                    "Kamu sudah absen masuk hari ini. Tunggu sekitar {$jamTersisa} jam {$sisaMenit} menit lagi sebelum bisa absen keluar."
                );
            }

            // Hitung durasi total menit
            $durasiMenit = $jamMasuk->diffInMinutes($sekarang);

            // Simpan absen keluar
            Absensi::create([
                'mahasiswa_id'      => $mahasiswa->id,
                'jam_masuk'         => $jamMasuk,
                'jam_keluar'        => $sekarang,
                'type'              => 'keluar',
                'durasi_menit'      => $durasiMenit,
                'latitude'          => $lat,
                'longitude'         => $lng,
                'location_accuracy' => $acc,
            ]);

            // Pesan default (kalau bukan IT / tidak telat pulang)
            $defaultMessage = "Absensi keluar berhasil direkam! Durasi: " .
                floor($durasiMenit / 60) . " jam " .
                ($durasiMenit % 60) . " menit. Kamu sedang dalam fase cooldown, tunggu 3 jam sebelum bisa absen lagi.";

            // Kalau ruangan IT + ada jadwal keluar, cek telat pulang
            if ($jadwalIT && isset($jadwalIT['keluar'])) {
                $jamKeluarIdeal = $jadwalIT['keluar']->copy();

                if ($sekarang->gt($jamKeluarIdeal)) {
                    // Telat absen pulang => pakai pesan spesial
                    return back()->with(
                        'success',
                        'Terimakasih atas kerja kerasmu hari ini! 🤝'
                    );
                }
            }

            // Kalau tidak telat / bukan ruangan IT
            return back()->with('success', $defaultMessage);
        }

        // --- Jika terakhir adalah absen keluar ---
        if ($lastAbsen->type === 'keluar') {
            $jamKeluar        = Carbon::parse($lastAbsen->jam_keluar);
            $cooldownBerakhir = $jamKeluar->copy()->addHours(3);

            if ($sekarang->lt($cooldownBerakhir)) {
                $menitTersisa = $sekarang->diffInMinutes($cooldownBerakhir);
                $jamTersisa   = floor($menitTersisa / 60);
                $sisaMenit    = $menitTersisa % 60;

                return back()->with(
                    'error',
                    "Kamu sudah absen keluar hari ini. Tunggu sekitar {$jamTersisa} jam {$sisaMenit} menit lagi sebelum bisa absen masuk kembali."
                );
            }

            // Buat absen masuk baru setelah cooldown
            Absensi::create([
                'mahasiswa_id'      => $mahasiswa->id,
                'jam_masuk'         => $sekarang,
                'type'              => 'masuk',
                'latitude'          => $lat,
                'longitude'         => $lng,
                'location_accuracy' => $acc,
            ]);

            // Kalau ruangan IT + telat masuk (misal setelah jam ideal)
            if ($jadwalIT && isset($jadwalIT['masuk'])) {
                $jamMasukIdeal = $jadwalIT['masuk']->copy();

                if ($sekarang->gt($jamMasukIdeal)) {
                    $menitTelat = $sekarang->diffInMinutes($jamMasukIdeal);

                    return back()->with(
                        'success',
                        "Anda telat {$menitTelat} menit. Besok jangan telat ya 😊"
                    );
                }
            }

            return back()->with(
                'success',
                'Absensi masuk berhasil direkam kembali setelah masa cooldown!'
            );
        }

        // Jika terjadi kondisi tak terduga
        return back()->with('error', 'Terjadi kesalahan pada sistem absensi.');
    }

    // === Halaman admin untuk melihat data absensi ===
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'admin') {
            abort(403);
        }

        $ruangans = Ruangan::all();

        $query = Absensi::with(['mahasiswa', 'mahasiswa.ruangan']);

        // Filter ruangan
        if ($request->filled('ruangan_id')) {
            $query->whereHas('mahasiswa', function ($q) use ($request) {
                $q->where('ruangan_id', $request->ruangan_id);
            });
        }

        // Filter type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Cek apakah user menggunakan filter date range
        $useDateFilter = $request->filled('start_date') || $request->filled('end_date');

        if ($useDateFilter) {
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
        } else {
            // Default: hanya hari ini
            $query->whereDate('created_at', now()->toDateString());
        }

        $absensi = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('absensi.index', compact('absensi', 'ruangans'));
    }

    /**
     * Jadwal khusus ruangan IT:
     * - Senin–Kamis : 07:15–15:30
     * - Jumat       : 07:00–14:30
     * - Sabtu/Minggu: tidak pakai aturan khusus (return null)
     */
    private function getJadwalIT(Mahasiswa $mahasiswa, Carbon $today)
    {
        $namaRuangan = strtolower($mahasiswa->ruangan->nm_ruangan ?? $mahasiswa->nm_ruangan);

        if ($namaRuangan !== 'it') {
            return null;
        }

        // dayOfWeekIso: 1=Senin ... 7=Minggu
        $hari = $today->dayOfWeekIso;

        // Senin–Kamis
        if ($hari >= 1 && $hari <= 4) {
            return [
                'masuk'  => $today->copy()->setTime(7, 15),
                'keluar' => $today->copy()->setTime(15, 30),
            ];
        }

        // Jumat
        if ($hari === 5) {
            return [
                'masuk'  => $today->copy()->setTime(7, 0),
                'keluar' => $today->copy()->setTime(14, 30),
            ];
        }

        // Sabtu / Minggu => tidak ada rule khusus
        return null;
    }

    /**
     * Hitung jarak antar dua titik (meter) pakai rumus Haversine
     */
    private function distanceInMeters($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Cek apakah lokasi masih di dalam radius N meter dari RSUD SLG
     */
    private function isInRsudArea(float $lat, float $lng, int $radiusMeters = 200): bool
    {
        $rsudLat = -7.8215955908167265;
        $rsudLng = 112.0578641768854;

        $distance = $this->distanceInMeters($lat, $lng, $rsudLat, $rsudLng);

        return $distance <= $radiusMeters;
    }

    public function generateSertifikatPublik($token)
    {
        $mahasiswa = Mahasiswa::where('share_token', $token)->firstOrFail();

        if (now()->lt($mahasiswa->tanggal_berakhir)) {
            return abort(403, 'Sertifikat belum dapat diunduh.');
        }

        // --- [LOGIKA PERHITUNGAN YANG DIPERBAIKI] ---
        $startDate = $mahasiswa->tanggal_mulai;
        $endDate = $mahasiswa->tanggal_berakhir;
        $totalExpectedDays = 0;

        if ($startDate && $endDate) {
            $period = CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $date) {
                if ($mahasiswa->weekend_aktif) {
                    $totalExpectedDays++;
                } elseif (!$date->isWeekend()) {
                    $totalExpectedDays++;
                }
            }
        }

        $totalActualDays = $mahasiswa->absensis()
            ->select(DB::raw('DATE(created_at) as date'))
            ->distinct()
            ->count();

        $percentage = 0;
        if ($totalExpectedDays > 0) {
            $percentage = round(($totalActualDays / $totalExpectedDays) * 100, 1);
        }
        // --- [AKHIR PERBAIKAN] ---

        // --- Logika Background ---
        $path = public_path('background.png');
        $base64 = '';
        if (File::exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $fileData = File::get($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($fileData);
        }
        // --- Akhir Logika Background ---

        $data = [
            'mahasiswa'      => $mahasiswa,
            'percentage'     => $percentage,
            'total_hadir'    => $totalActualDays,
            'tanggal_terbit' => Carbon::now()->isoFormat('D MMMM YYYY'),
            'bg_base64'      => $base64,
        ];

        $pdf = Pdf::loadView('sertifikat.template', $data);
        $pdf->setPaper('a4', 'landscape');

        $fileName = 'Sertifikat - ' . $mahasiswa->nm_mahasiswa . '.pdf';
        return $pdf->download($fileName);
    }
}
