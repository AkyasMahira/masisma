<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Kegiatan;
use App\Models\MasterInstansi;
use App\Models\KegiatanPeserta;
use App\Models\MasterKompetensi;
use Carbon\Carbon;

class KegiatanController extends Controller
{
   public function index()
    {
        // Tambahkan relasi 'fasilitator' agar data KegiatanFasilitator ikut terambil
        $kegiatan = Kegiatan::with(['penyelenggara', 'fasilitator'])->orderBy('tanggal_mulai', 'desc')->get();
        return view('admin.kegiatan.index', compact('kegiatan'));
    }

    public function update(Request $request, $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);

        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jenis_kegiatan' => 'required|in:internal,eksternal',
            'tanggal_mulai' => 'required|date',
            'jpl' => 'nullable|integer|min:1',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'penyelenggara_id' => 'required|exists:master_instansi,id',
            'platform' => 'required|string|max:255',
            'tipe_absen' => 'required|in:masuk_saja,masuk_keluar',
            'warna_tema' => 'nullable|string|max:7',
        ]);

        DB::transaction(function () use ($request, $kegiatan) {
          $keahlianArray = $request->keahlian ?? null;
            
            // 1. Update Kegiatan Utama
            $kegiatan->update([
                'nama_kegiatan' => $request->nama_kegiatan,
                'deskripsi' => $request->deskripsi,
                'keahlian' => $keahlianArray,
                'jenis_kegiatan' => $request->jenis_kegiatan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'jpl' => $request->jpl,
                'tanggal_selesai' => $request->tanggal_selesai,
                'penyelenggara_id' => $request->penyelenggara_id,
                'platform' => $request->platform,
                'tipe_absen' => $request->tipe_absen,
                'warna_tema' => $request->warna_tema ?? '#7c1316',
            ]);

            // 2. Update Relasi Tujuan
            $kegiatan->tujuan()->delete();
            if ($request->has('tujuan')) {
                foreach ($request->tujuan as $tujuan_teks) {
                    if (!empty(trim($tujuan_teks))) {
                        $kegiatan->tujuan()->create(['tujuan' => $tujuan_teks]);
                    }
                }
            }

            // 3. Update Relasi Pivot Kompetensi
            $syncData = [];
            if ($request->has('kompetensi_id')) {
                foreach ($request->kompetensi_id as $index => $kompId) {
                    if (!empty($kompId)) {
                        $syncData[$kompId] = [
                            'indikator_keberhasilan' => $request->indikator_keberhasilan[$index] ?? null
                        ];
                    }
                }
            }
            $kegiatan->kompetensi()->sync($syncData);
        });

        return redirect()->route('admin.kegiatan.index')->with('success', 'Data Kegiatan berhasil diperbarui.');
    }

    public function create()
    {
        $instansi = MasterInstansi::orderBy('nama_instansi', 'asc')->get();
        $master_kompetensi = MasterKompetensi::orderBy('nama_kompetensi', 'asc')->get();
        
        // Ambil semua keahlian unik dari database
        $listKeahlian = Kegiatan::whereNotNull('keahlian')->pluck('keahlian')->flatten()->unique()->filter()->values()->toArray();
        
        return view('admin.kegiatan.create', compact('instansi', 'master_kompetensi', 'listKeahlian'));
    }

    public function edit($id)
    {
        $kegiatan = Kegiatan::with(['tujuan', 'kompetensi'])->findOrFail($id);
        $instansi = MasterInstansi::orderBy('nama_instansi', 'asc')->get();
        $master_kompetensi = MasterKompetensi::orderBy('nama_kompetensi', 'asc')->get();
        
        // Ambil semua keahlian unik dari database
        $listKeahlian = Kegiatan::whereNotNull('keahlian')->pluck('keahlian')->flatten()->unique()->filter()->values()->toArray();
        
        return view('admin.kegiatan.edit', compact('kegiatan', 'instansi', 'master_kompetensi', 'listKeahlian'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jenis_kegiatan' => 'required|in:internal,eksternal',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'penyelenggara_id' => 'required|exists:master_instansi,id',
            'platform' => 'required|string|max:255',
            'tipe_absen' => 'required|in:masuk_saja,masuk_keluar',
            'jpl' => 'nullable|integer|min:1', 
            'warna_tema' => 'nullable|string|max:7',
        ]);

        DB::transaction(function () use ($request) {
            $keahlianArray = $request->keahlian ?? null;
            
            // 1. Simpan Kegiatan
            $kegiatan = Kegiatan::create([
                'nama_kegiatan' => $request->nama_kegiatan,
                'deskripsi' => $request->deskripsi,
                'keahlian' => $keahlianArray, 
                'jenis_kegiatan' => $request->jenis_kegiatan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'penyelenggara_id' => $request->penyelenggara_id,
                'platform' => $request->platform,
                'tipe_absen' => $request->tipe_absen,
                'jpl' => $request->jpl,
                'warna_tema' => $request->warna_tema ?? '#7c1316',
                'token_absensi' => Str::uuid()->toString(),
            ]);

            // 2. Simpan Multi-Tujuan
            if ($request->has('tujuan')) {
                foreach ($request->tujuan as $tujuan_teks) {
                    if (!empty(trim($tujuan_teks))) {
                        $kegiatan->tujuan()->create(['tujuan' => $tujuan_teks]);
                    }
                }
            }

            // 3. Simpan Relasi Pivot Multi-Kompetensi & Indikator (Sync)
            $syncData = [];
            if ($request->has('kompetensi_id')) {
                foreach ($request->kompetensi_id as $index => $kompId) {
                    if (!empty($kompId)) {
                        $syncData[$kompId] = [
                            'indikator_keberhasilan' => $request->indikator_keberhasilan[$index] ?? null
                        ];
                    }
                }
            }
            $kegiatan->kompetensi()->sync($syncData);
        });

        return redirect()->route('admin.kegiatan.index')->with('success', 'Data Kegiatan berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        Kegiatan::findOrFail($id)->delete();
        return redirect()->route('admin.kegiatan.index')->with('success', 'Data Kegiatan berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate(['data' => 'required|array']);
        $inserted = 0;

        foreach ($request->data as $row) {
            $instansi = MasterInstansi::firstOrCreate([
                'nama_instansi' => trim($row['penyelenggara'])
            ]);

            Kegiatan::create([
                'nama_kegiatan' => trim($row['nama_kegiatan']),
                'jenis_kegiatan' => strtolower(trim($row['jenis_kegiatan'])) == 'internal' ? 'internal' : 'eksternal',
                'tanggal_mulai' => Carbon::parse($row['tanggal_mulai'])->format('Y-m-d H:i:s'),
                'tanggal_selesai' => Carbon::parse($row['tanggal_selesai'])->format('Y-m-d H:i:s'),
                'penyelenggara_id' => $instansi->id,
                'platform' => trim($row['platform']),
                'tipe_absen' => strtolower(trim($row['tipe_absen'])) == 'masuk_keluar' ? 'masuk_keluar' : 'masuk_saja',
                'token_absensi' => Str::uuid()->toString(),
            ]);
            $inserted++;
        }

        return response()->json(['success' => true, 'message' => "Berhasil mengimpor $inserted data kegiatan."]);
    }

    public function rekap($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);

        $pesertaTerdaftar = \App\Models\KegiatanPeserta::with([
            'ruangan',
            'instansi',
            'absensiAktual'
        ])
        ->where('kegiatan_id', $id)
        ->orderBy('nama_lengkap_gelar', 'asc')
        ->get();

        /* REKAP INSTANSI */
        $rekapInstansi = [];
        foreach ($pesertaTerdaftar->groupBy('instansi_id') as $instansiId => $peserta) {
            $hadir = 0; $izin = 0; $belum = 0;
            foreach ($peserta as $p) {
                if ($p->absensiAktual) {
                    if ($p->absensiAktual->status_kehadiran == 'hadir') {
                        $hadir++;
                    } else {
                        $izin++;
                    }
                } else {
                    $belum++;
                }
            }
            $target = $peserta->count();
            $rekapInstansi[] = [
                'id' => $instansiId,
                'nama' => $peserta->first()->instansi->nama_instansi ?? 'Tanpa Instansi',
                'target' => $target,
                'hadir' => $hadir,
                'izin' => $izin,
                'belum' => $belum,
                'persen_hadir' => $target > 0 ? round(($hadir / $target) * 100, 2) : 0,
                'persen_izin' => $target > 0 ? round(($izin / $target) * 100, 2) : 0,
                'persen_belum' => $target > 0 ? round(($belum / $target) * 100, 2) : 0,
            ];
        }

        /* REKAP RUANGAN */
        $rekapRuangan = [];
        foreach ($pesertaTerdaftar->groupBy('ruangan_id') as $ruanganId => $peserta) {
            $hadir = 0; $izin = 0; $belum = 0;
            foreach ($peserta as $p) {
                if ($p->absensiAktual) {
                    if ($p->absensiAktual->status_kehadiran == 'hadir') {
                        $hadir++;
                    } else {
                        $izin++;
                    }
                } else {
                    $belum++;
                }
            }
            $target = $peserta->count();
            $rekapRuangan[] = [
                'id' => $ruanganId,
                'nama' => $peserta->first()->ruangan->nama_ruangan ?? 'Tanpa Ruangan',
                'target' => $target,
                'hadir' => $hadir,
                'izin' => $izin,
                'belum' => $belum,
                'persen_hadir' => $target > 0 ? round(($hadir / $target) * 100, 2) : 0,
                'persen_izin' => $target > 0 ? round(($izin / $target) * 100, 2) : 0,
                'persen_belum' => $target > 0 ? round(($belum / $target) * 100, 2) : 0,
            ];
        }

        /* TOTAL DASHBOARD */
        $totalTarget = $pesertaTerdaftar->count();
        $totalHadir = 0; $totalIzin = 0; $totalBelum = 0;
        foreach ($pesertaTerdaftar as $p) {
            if ($p->absensiAktual) {
                if ($p->absensiAktual->status_kehadiran == 'hadir') {
                    $totalHadir++;
                } else {
                    $totalIzin++;
                }
            } else {
                $totalBelum++;
            }
        }
        $persentaseHadir = $totalTarget > 0 ? round(($totalHadir / $totalTarget) * 100, 2) : 0;
        $persentaseIzin = $totalTarget > 0 ? round(($totalIzin / $totalTarget) * 100, 2) : 0;
        $persentaseBelum = $totalTarget > 0 ? round(($totalBelum / $totalTarget) * 100, 2) : 0;

        return view('admin.kegiatan.rekap', compact(
            'kegiatan', 'pesertaTerdaftar', 'rekapInstansi', 'rekapRuangan',
            'totalTarget', 'totalHadir', 'totalIzin', 'totalBelum',
            'persentaseHadir', 'persentaseIzin', 'persentaseBelum'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->update(['status_absen' => $request->status_absen]);
        return redirect()->back()->with('success', 'Status absensi berhasil diubah!');
    }

public function submitAbsen(Request $request, $token)
    {
        $kegiatan = Kegiatan::where('token_absensi', $token)->firstOrFail();
        $sekarang = now();
        $tanggal_mulai = \Carbon\Carbon::parse($kegiatan->tanggal_mulai);
        $tanggal_selesai = \Carbon\Carbon::parse($kegiatan->tanggal_selesai);
        $batas_pulang = $tanggal_selesai->copy()->addHour(); 

        if ($kegiatan->status_absen == 'tutup') {
            return redirect()->back()->with('error', 'Mohon maaf, link absensi telah DITUTUP oleh Panitia.');
        }

        if ($kegiatan->status_absen == 'otomatis') {
            if ($sekarang->lessThan($tanggal_mulai)) {
                return redirect()->back()->with('error', 'Sabar ya, event belum dimulai. Absensi belum dibuka.');
            }
            if ($sekarang->greaterThan($batas_pulang)) {
                return redirect()->back()->with('error', 'Mohon maaf, batas waktu absensi keseluruhan telah berakhir.');
            }
        }

        $request->validate([
            'kegiatan_peserta_id' => 'required|exists:kegiatan_peserta,id',
            'status_kehadiran' => 'required|in:hadir,tidak_hadir',
            'foto_bukti' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $peserta_id = $request->kegiatan_peserta_id;

        $fotoPath = null;
        if ($request->hasFile('foto_bukti')) {
            $file = $request->file('foto_bukti');
            $filename = time() . '_' . $peserta_id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/absensi'), $filename);
            $fotoPath = 'uploads/absensi/' . $filename;
        }

        $absensi = \App\Models\AbsensiKegiatan::where('kegiatan_id', $kegiatan->id)
            ->where('kegiatan_peserta_id', $peserta_id)
            ->whereDate('waktu_masuk', $sekarang->toDateString())
            ->first();

        if ($kegiatan->tipe_absen == 'masuk_saja') {
            if ($absensi) {
                return redirect()->back()->with('error', 'Anda sudah melakukan absensi kehadiran hari ini. Tidak perlu absen 2x.');
            }
            
            \App\Models\AbsensiKegiatan::create([
                'kegiatan_id' => $kegiatan->id,
                'kegiatan_peserta_id' => $peserta_id,
                'status_kehadiran' => $request->status_kehadiran,
                'waktu_masuk' => $sekarang, 
                'foto_bukti' => $fotoPath
            ]);
            return redirect()->back()->with('success', 'Absensi HARI INI berhasil! Terima kasih atas kehadiran Anda.');
        } 
        
        else {
            if (!$absensi) {
                \App\Models\AbsensiKegiatan::create([
                    'kegiatan_id' => $kegiatan->id,
                    'kegiatan_peserta_id' => $peserta_id,
                    'status_kehadiran' => $request->status_kehadiran,
                    'waktu_masuk' => $sekarang,
                    'foto_bukti' => $fotoPath
                ]);
                return redirect()->back()->with('success', 'Absensi MASUK HARI INI berhasil tercatat! Selamat mengikuti kegiatan.');

            } elseif (is_null($absensi->waktu_keluar)) {
                $jamSelesaiEvent = $tanggal_selesai->format('H:i:s');
                $waktuBatasPulangHariIni = \Carbon\Carbon::parse($sekarang->format('Y-m-d') . ' ' . $jamSelesaiEvent);

                if ($kegiatan->status_absen == 'otomatis' && $sekarang->lessThan($waktuBatasPulangHariIni)) {
                    $jam_pulang_event = $tanggal_selesai->format('H:i');
                    return redirect()->back()->with('error', "Belum waktunya pulang! Absen PULANG hari ini baru dibuka mulai jam {$jam_pulang_event} WIB.");
                }

                $waktu_masuk = \Carbon\Carbon::parse($absensi->waktu_masuk);
                if ($sekarang->diffInMinutes($waktu_masuk) < 5) {
                    return redirect()->back()->with('error', 'Anda baru saja absen masuk! Harap tunggu beberapa menit sebelum absen pulang.');
                }

                $absensi->update([
                    'waktu_keluar' => $sekarang, 
                ]);
                return redirect()->back()->with('success', 'Absensi PULANG HARI INI berhasil tercatat! Hati-hati di jalan.');

            } else {
                return redirect()->back()->with('error', 'Anda sudah menyelesaikan absensi MASUK dan PULANG untuk hari ini. Terima kasih!');
            }
        }
    }

    public function publicFormAbsen($token)
    {
        $kegiatan = Kegiatan::where('token_absensi', $token)->firstOrFail();
        
        $daftarPeserta = KegiatanPeserta::where('kegiatan_id', $kegiatan->id)
            ->orderBy('nama_lengkap_gelar', 'asc')
            ->get();

        $hariIni = \Carbon\Carbon::now()->toDateString();
        
        $sudahAbsenMasuk = \App\Models\AbsensiKegiatan::where('kegiatan_id', $kegiatan->id)
            ->whereDate('waktu_masuk', $hariIni) 
            ->pluck('kegiatan_peserta_id')
            ->toArray();
            
        $sudahAbsenPulang = \App\Models\AbsensiKegiatan::where('kegiatan_id', $kegiatan->id)
            ->whereDate('waktu_masuk', $hariIni) 
            ->whereNotNull('waktu_keluar') 
            ->pluck('kegiatan_peserta_id')
            ->toArray();
        
        return view('public.kegiatan.form_absen', compact('kegiatan', 'daftarPeserta', 'sudahAbsenMasuk', 'sudahAbsenPulang'));
    }
}