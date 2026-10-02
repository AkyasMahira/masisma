<?php

namespace App\Http\Controllers;

use App\Models\BookingRuangan;
use App\Models\BookingPeserta;
use App\Models\Ruangan;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\OrientasiResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PDF;

class InstansiController extends Controller
{
    /* ======================= PORTAL INSTANSI ======================= */

    private function instansi()
    {
        $user = auth()->user();
        abort_unless($user && $user->role === 'instansi', 403, 'Halaman khusus akun instansi mitra.');
        abort_unless($user->mou, 403, 'Akun instansi ini belum terhubung ke data MOU.');
        return $user;
    }

    public function dashboard()
    {
        $user = $this->instansi();
        $mou = $user->mou;

        $bookings = BookingRuangan::with('ruangan', 'pesertas')
            ->where('mou_id', $mou->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $stat = [
            'pending'  => $bookings->where('status', 'pending')->count(),
            'approved' => $bookings->where('status', 'approved')->count(),
            'rejected' => $bookings->where('status', 'rejected')->count(),
            'peserta'  => $bookings->where('status', 'approved')->sum('jumlah_peserta'),
        ];

        return view('instansi.dashboard', compact('mou', 'bookings', 'stat'));
    }

    public function bookingCreate()
    {
        $user = $this->instansi();
        $today = now()->toDateString();

        // Daftar ruangan + sisa kuota (indikatif, memakai hari ini sebagai acuan)
        $ruangans = Ruangan::orderBy('nm_ruangan')->get()->map(function ($r) use ($today) {
            $r->sisa_kuota = $this->sisaKuota($r, $today, $today);
            return $r;
        });

        return view('instansi.booking_create', [
            'mou'         => $user->mou,
            'ruangans'    => $ruangans,
            'listProdi'   => \App\Models\MasterProdi::grouped(),
            'jenjangList' => \App\Models\MasterProdi::jenjangList(),
        ]);
    }

    public function bookingStore(Request $request)
    {
        $user = $this->instansi();

        $data = $request->validate([
            'ruangan_id'     => 'required|exists:ruangans,id',
            'jenjang'        => 'nullable|string|max:20',
            'prodi'          => 'nullable|string|max:255',
            'semester'       => 'nullable|string|max:20',
            'jumlah_peserta' => 'required|integer|min:1',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai'=> 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'     => 'nullable|string',
            'kompetensi_dimiliki'   => 'nullable|array',
            'kompetensi_dimiliki.*' => 'nullable|string|max:255',
        ], [
            'ruangan_id.required' => 'Pilih ruangan tujuan.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ]);

        $ruangan = Ruangan::findOrFail($data['ruangan_id']);
        $sisa = $this->sisaKuota($ruangan, $data['tanggal_mulai'], $data['tanggal_selesai']);

        // Notifikasi PENUH: tolak jika permintaan melebihi sisa kuota
        if ($data['jumlah_peserta'] > $sisa) {
            return back()->withInput()->with('error',
                "Ruangan {$ruangan->nm_ruangan} tidak cukup kuota untuk periode tersebut. Sisa kuota: {$sisa} orang. " .
                ($sisa == 0 ? 'Ruangan PENUH — silakan pilih ruangan lain atau periode lain.' : ''));
        }

        BookingRuangan::create([
            'mou_id'          => $user->mou->id,
            'user_id'         => $user->id,
            'ruangan_id'      => $ruangan->id,
            'jenjang'         => $data['jenjang'] ?? null,
            'prodi'           => $data['prodi'] ?? null,
            'semester'        => $data['semester'] ?? null,
            'kompetensi_dimiliki_json' => $request->filled('kompetensi_dimiliki') ? array_values(array_filter($request->kompetensi_dimiliki)) : [],
            'jumlah_peserta'  => $data['jumlah_peserta'],
            'tanggal_mulai'   => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'keterangan'      => $data['keterangan'] ?? null,
            'status'          => 'pending',
        ]);

        return redirect()->route('instansi.dashboard')->with('success', 'Permintaan booking ruangan terkirim. Menunggu persetujuan admin diklat.');
    }

    /* ======================= REKAP & LAPORAN ======================= */

    public function rekap(Request $request)
    {
        $user = $this->instansi();
        $mou = $user->mou;
        $today = now()->startOfDay();

        $view = in_array($request->get('view'), ['prodi', 'ruangan', 'periode']) ? $request->get('view') : 'semua';

        $selesaiFn = function ($status, $tglBerakhir) use ($today) {
            return ($status === 'nonaktif')
                || ($tglBerakhir && Carbon::parse($tglBerakhir)->startOfDay()->lt($today));
        };

        // ---- STATISTIK (ringan: tanpa relasi, satu query) ----
        $light = Mahasiswa::where('mou_id', $mou->id)->get(['id', 'status', 'tanggal_berakhir', 'nilai_ruangan_json']);
        $allIds = $light->pluck('id');
        $nilaiRata = $light->map(function ($m) {
            $nr = is_array($m->nilai_ruangan_json) ? $m->nilai_ruangan_json : [];
            $nr = array_filter($nr, 'is_numeric');
            return count($nr) ? array_sum($nr) / count($nr) : null;
        })->filter();
        $lulusOrientasi = $allIds->count()
            ? OrientasiResult::whereIn('mahasiswa_id', $allIds)->where('status', 'lulus_orientasi')->count() : 0;
        $stat = [
            'total'    => $light->count(),
            'selesai'  => $light->filter(fn($m) => $selesaiFn($m->status, $m->tanggal_berakhir))->count(),
            'berjalan' => $light->filter(fn($m) => !$selesaiFn($m->status, $m->tanggal_berakhir))->count(),
            'lulus_orientasi' => $lulusOrientasi,
            'rata_nilai' => $nilaiRata->count() ? round($nilaiRata->avg(), 1) : 0,
        ];

        // ---- DAFTAR (pagination + eager load + batch orientasi) ----
        $query = Mahasiswa::where('mou_id', $mou->id)
            ->with(['roomSequences.ruangan', 'ruangan'])
            ->orderBy('nm_mahasiswa');
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('nm_mahasiswa', 'like', "%$q%")->orWhere('prodi', 'like', "%$q%");
            });
        }
        // Mode grup butuh seluruh data agar grup utuh; mode "semua" dipaginate
        $perPage = ($view === 'semua') ? 20 : 1000;
        $pg = $query->paginate($perPage)->withQueryString();

        $pageIds = collect($pg->items())->pluck('id');
        $oriMap = $pageIds->count()
            ? OrientasiResult::whereIn('mahasiswa_id', $pageIds)->get()->keyBy('mahasiswa_id') : collect();

        $pg->getCollection()->transform(function ($m) use ($selesaiFn, $oriMap) {
            $m->is_selesai = $selesaiFn($m->status, $m->tanggal_berakhir);
            $m->orientasi = $oriMap->get($m->id);
            $m->nilai_akhir = (float) ($m->nilai_karu_final ?? 0);
            return $m;
        });

        $grouped = null;
        if ($view === 'prodi') {
            $grouped = $pg->getCollection()->groupBy(fn($m) => $m->prodi ?: 'Tanpa Prodi')->sortKeys();
        } elseif ($view === 'ruangan') {
            $grouped = $pg->getCollection()->groupBy(fn($m) => optional($m->ruangan)->nm_ruangan ?: 'Tanpa Ruangan')->sortKeys();
        } elseif ($view === 'periode') {
            $grouped = $pg->getCollection()->groupBy(function ($m) {
                return (optional($m->tanggal_mulai)->format('d/m/Y') ?? '?') . ' - ' . (optional($m->tanggal_berakhir)->format('d/m/Y') ?? '?');
            })->sortKeys();
        }

        return view('instansi.rekap', compact('mou', 'pg', 'stat', 'view', 'grouped'));
    }

    public function sertifikatOrientasi($mahasiswaId)
    {
        $user = $this->instansi();
        $m = Mahasiswa::where('id', $mahasiswaId)->where('mou_id', $user->mou->id)->firstOrFail();

        $result = OrientasiResult::where('mahasiswa_id', $m->id)->first();
        if (!$result || $result->status !== 'lulus_orientasi') {
            return back()->with('error', 'Mahasiswa ini belum lulus orientasi, sertifikat belum tersedia.');
        }

        $akun = User::find($result->user_id) ?? $m->user;
        $pdf = PDF::loadView('orientasi.sertifikat_pdf', [
            'user'       => $akun,
            'date'       => Carbon::parse($result->updated_at)->format('d F Y'),
            'pre_score'  => $result->pre_test_score,
            'post_score' => $result->post_test_score,
        ]);
        return $pdf->stream('Sertifikat-Orientasi-' . ($akun->name ?? $m->nm_mahasiswa) . '.pdf');
    }

    /* ======================= DAFTAR ANAK MAGANG (PESERTA) ======================= */

    private function ownBooking($bookingId)
    {
        $user = $this->instansi();
        $booking = BookingRuangan::with('ruangan', 'pesertas')->findOrFail($bookingId);
        abort_unless((int) $booking->mou_id === (int) $user->mou->id, 403, 'Booking ini bukan milik instansi Anda.');
        return $booking;
    }

    public function pesertaIndex($bookingId)
    {
        $booking = $this->ownBooking($bookingId);
        return view('instansi.peserta', compact('booking'));
    }

    public function pesertaStore(Request $request, $bookingId)
    {
        $booking = $this->ownBooking($bookingId);

        // Batas pengisian yang ditetapkan admin
        if (!$booking->pengisianDibuka()) {
            return back()->with('error', 'Pengisian peserta sudah ditutup (melewati batas ' . optional($booking->batas_pengisian)->format('d/m/Y') . '). Hubungi admin diklat.');
        }

        $data = $request->validate([
            'nama'          => 'required|string|max:255',
            'nim'           => 'nullable|string|max:100',
            'email'         => 'nullable|email|max:255',
            'prodi'         => 'nullable|string|max:255',
            'tipe_mahasiswa'=> 'required|in:magang,pkl',
            'weekend_aktif' => 'nullable|boolean',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_hp'         => 'nullable|string|max:30',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan'    => 'nullable|string',
        ], [
            'nama.required' => 'Nama peserta wajib diisi.',
        ]);

        // Batasi jumlah peserta sesuai kuota booking yang diajukan
        if ($booking->pesertas()->count() >= $booking->jumlah_peserta) {
            return back()->with('error', "Jumlah peserta sudah mencapai batas booking ({$booking->jumlah_peserta} orang). Ajukan booking tambahan bila perlu.");
        }

        // Foto pas (untuk ID card mahasiswa nanti)
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $namaFile = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('uploads/pas_foto'), $namaFile);
            $data['foto_path'] = 'uploads/pas_foto/' . $namaFile;
        }
        unset($data['foto']);

        $data['weekend_aktif'] = $request->boolean('weekend_aktif');
        $data['booking_ruangan_id'] = $booking->id;
        BookingPeserta::create($data);

        return back()->with('success', 'Peserta magang berhasil ditambahkan.');
    }

    public function pesertaDestroy($bookingId, $pesertaId)
    {
        $booking = $this->ownBooking($bookingId);
        $peserta = BookingPeserta::where('booking_ruangan_id', $booking->id)->findOrFail($pesertaId);
        if ($peserta->status === 'approved') {
            return back()->with('error', 'Peserta yang sudah disetujui (jadi akun mahasiswa) tidak bisa dihapus.');
        }
        $peserta->delete();
        return back()->with('success', 'Peserta magang dihapus.');
    }

    /* ======================= SISI ADMIN ======================= */

    public function adminIndex(Request $request)
    {
        $query = BookingRuangan::with(['mou', 'ruangan', 'pesertas'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate(15)->withQueryString();
        $jmlPending = BookingRuangan::where('status', 'pending')->count();

        return view('admin.booking.index', compact('bookings', 'jmlPending'));
    }

    public function adminApprove(Request $request, $id)
    {
        $request->validate([
            'batas_pengisian' => 'nullable|date',
        ]);

        $booking = BookingRuangan::with('ruangan')->findOrFail($id);

        // Re-cek kuota saat approve (kondisi bisa berubah sejak diajukan)
        $sisa = $this->sisaKuota($booking->ruangan, $booking->tanggal_mulai, $booking->tanggal_selesai, $booking->id);
        if ($booking->jumlah_peserta > $sisa) {
            return back()->with('error',
                "Tidak bisa menyetujui: sisa kuota {$booking->ruangan->nm_ruangan} tinggal {$sisa} orang untuk periode itu (diminta {$booking->jumlah_peserta}).");
        }

        $booking->update([
            'status'          => 'approved',
            'catatan_admin'   => 'Disetujui.',
            'batas_pengisian' => $request->batas_pengisian ?: null,
        ]);
        return back()->with('success', 'Booking disetujui.');
    }

    public function adminEditBooking($id)
    {
        $booking = BookingRuangan::with('mou')->findOrFail($id);
        $ruangans = Ruangan::orderBy('nm_ruangan')->get();
        return view('admin.booking.edit', compact('booking', 'ruangans'));
    }

    public function adminUpdateBooking(Request $request, $id)
    {
        $booking = BookingRuangan::findOrFail($id);

        $data = $request->validate([
            'ruangan_id'      => 'required|exists:ruangans,id',
            'jumlah_peserta'  => 'required|integer|min:1',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status'          => 'required|in:pending,approved,rejected',
            'batas_pengisian' => 'nullable|date',
            'keterangan'      => 'nullable|string',
            'catatan_admin'   => 'nullable|string',
        ]);

        // Cegah overbooking bila status disetujui
        if ($data['status'] === 'approved') {
            $ruangan = Ruangan::find($data['ruangan_id']);
            $sisa = $this->sisaKuota($ruangan, $data['tanggal_mulai'], $data['tanggal_selesai'], $booking->id);
            if ($data['jumlah_peserta'] > $sisa) {
                return back()->withInput()->with('error', "Sisa kuota {$ruangan->nm_ruangan} hanya {$sisa} orang untuk periode itu.");
            }
        }

        $data['batas_pengisian'] = $data['batas_pengisian'] ?? null;
        $booking->update($data);

        return redirect()->route('admin.booking.index')->with('success', 'Booking berhasil diperbarui.');
    }

    public function adminDestroyBooking($id)
    {
        $booking = BookingRuangan::findOrFail($id);
        $booking->delete(); // cascade: peserta ikut terhapus
        return redirect()->route('admin.booking.index')->with('success', 'Booking dihapus.');
    }

    /**
     * Ubah batas tanggal pengisian peserta (khusus admin).
     */
    public function adminSetBatas(Request $request, $id)
    {
        $request->validate(['batas_pengisian' => 'nullable|date']);
        $booking = BookingRuangan::findOrFail($id);
        $booking->update(['batas_pengisian' => $request->batas_pengisian ?: null]);
        return back()->with('success', 'Batas pengisian peserta diperbarui.');
    }

    public function adminReject(Request $request, $id)
    {
        $request->validate(['catatan_admin' => 'required|string'], [
            'catatan_admin.required' => 'Alasan penolakan wajib diisi.',
        ]);
        $booking = BookingRuangan::findOrFail($id);
        $booking->update(['status' => 'rejected', 'catatan_admin' => $request->catatan_admin]);
        return back()->with('success', 'Booking ditolak.');
    }

    /**
     * Kalender booking ruangan (sisi admin) + rekap per ruangan.
     */
    public function adminKalender()
    {
        $bookings = BookingRuangan::with(['mou', 'ruangan', 'pesertas'])
            ->where('status', '!=', 'rejected')
            ->orderBy('tanggal_mulai')
            ->get();

        // Palet warna per ruangan (stabil berdasarkan id)
        $palette = ['#7c1316', '#1d4ed8', '#15803d', '#b45309', '#7e22ce', '#0e7490', '#be123c', '#4d7c0f'];

        $events = [];
        $perRuangan = [];

        foreach ($bookings as $b) {
            $namaRuangan = optional($b->ruangan)->nm_ruangan ?? 'Tanpa Ruangan';
            $namaInstansi = optional($b->mou)->nama_instansi ?? optional($b->mou)->nama_universitas ?? 'Instansi';
            $warna = $palette[($b->ruangan_id ?? 0) % count($palette)];

            $events[] = [
                'title' => $namaInstansi . ' (' . $b->jumlah_peserta . ' org) · ' . $namaRuangan,
                'start' => optional($b->tanggal_mulai)->format('Y-m-d'),
                // FullCalendar: end bersifat eksklusif, +1 hari agar tanggal selesai ikut terwarnai
                'end'   => optional($b->tanggal_selesai)->copy()->addDay()->format('Y-m-d'),
                'color' => $b->status === 'pending' ? '#94a3b8' : $warna,
                'extendedProps' => [
                    'instansi' => $namaInstansi,
                    'ruangan'  => $namaRuangan,
                    'peserta'  => $b->jumlah_peserta,
                    'status'   => $b->status,
                    'periode'  => optional($b->tanggal_mulai)->format('d/m/Y') . ' - ' . optional($b->tanggal_selesai)->format('d/m/Y'),
                    'prodi'    => trim(($b->jenjang ? $b->jenjang . ' ' : '') . ($b->prodi ?? '')) . ($b->semester ? ' · smt ' . $b->semester : ''),
                    'pesertaList' => $b->pesertas->map(function ($p) {
                        return ['nama' => $p->nama, 'nim' => $p->nim, 'prodi' => $p->prodi, 'status' => $p->status];
                    })->values(),
                ],
            ];

            $perRuangan[$namaRuangan][] = $b;
        }

        ksort($perRuangan);

        return view('admin.booking.kalender', [
            'events'     => $events,
            'perRuangan' => $perRuangan,
        ]);
    }

    /**
     * ACC peserta magang oleh admin (mirip approve pengajuan magang):
     * membuat akun User + record Mahasiswa dari data peserta & booking.
     */
    public function pesertaApprove($id)
    {
        $peserta = BookingPeserta::with('booking.ruangan', 'booking.mou')->findOrFail($id);

        if ($peserta->status === 'approved') {
            return back()->with('error', 'Peserta ini sudah disetujui sebelumnya.');
        }

        $booking = $peserta->booking;
        if (!$booking || $booking->status !== 'approved') {
            return back()->with('error', 'Booking peserta ini belum disetujui. Setujui booking-nya dulu.');
        }

        $result = DB::transaction(function () use ($peserta, $booking) {
            // 1. Buat akun login untuk anak magang.
            //    Pakai email dari instansi bila ada & unik; jika tidak, generate.
            if ($peserta->email && !User::where('email', $peserta->email)->exists()) {
                $email = $peserta->email;
            } else {
                $base = $peserta->nim ?: Str::slug($peserta->nama);
                if ($base === '') $base = 'magang';
                $email = $base . '@magang.rsudslg.id';
                $i = 1;
                while (User::where('email', $email)->exists()) {
                    $email = $base . $i . '@magang.rsudslg.id';
                    $i++;
                }
            }
            $password = Str::random(8);

            $user = User::create([
                'name'        => $peserta->nama,
                'email'       => $email,
                'password'    => Hash::make($password),
                'role'        => 'user',
                'mou_id'      => $booking->mou_id,
                'is_approved' => true,
            ]);

            // 2. Buat record Mahasiswa (biodata lengkap dari peserta + ruangan/periode dari booking)
            do {
                $token = (string) Str::uuid();
            } while (Mahasiswa::where('share_token', $token)->exists());

            $mahasiswa = Mahasiswa::create([
                'user_id'          => $user->id,
                'nm_mahasiswa'     => $peserta->nama,
                'mou_id'           => $booking->mou_id,
                'prodi'            => $peserta->prodi,
                'no_hp'            => $peserta->no_hp ?: '-',
                'ruangan_id'       => $booking->ruangan_id,
                'status'           => 'aktif',
                'share_token'      => $token,
                'tanggal_mulai'    => $booking->tanggal_mulai,
                'tanggal_berakhir' => $booking->tanggal_selesai,
                'tipe_mahasiswa'   => in_array($peserta->tipe_mahasiswa, ['magang', 'pkl']) ? $peserta->tipe_mahasiswa : 'magang',
                'weekend_aktif'    => (bool) $peserta->weekend_aktif,
                'foto_path'        => $peserta->foto_path,
                // Kompetensi DIMILIKI auto dari booking (diisi instansi); kompetensi INGIN dikuasai diisi mahasiswa sendiri nanti.
                'kompetensi_dimiliki_json' => $booking->kompetensi_dimiliki_json ?: [],
                'kompetensi_json'  => [],
            ]);

            // 3. Auto-create RoomSequence dari booking (ruangan + periode) agar absensi langsung aktif.
            //    Satu penempatan untuk seluruh periode; admin bisa menambah rotasi ruangan lain nanti.
            if ($booking->ruangan_id && $booking->tanggal_mulai && $booking->tanggal_selesai) {
                \App\Models\RoomSequence::create([
                    'mahasiswa_id' => $mahasiswa->id,
                    'ruangan_id'   => $booking->ruangan_id,
                    'start_date'   => $booking->tanggal_mulai,
                    'end_date'     => $booking->tanggal_selesai,
                ]);
            }

            $peserta->update([
                'status'       => 'approved',
                'catatan_admin'=> 'Disetujui & akun mahasiswa dibuat.',
                'user_id'      => $user->id,
                'mahasiswa_id' => $mahasiswa->id,
            ]);

            return ['username' => $email, 'password' => $password, 'nama' => $peserta->nama];
        });

        return back()->with('success', 'Peserta disetujui — akun mahasiswa dibuat.')->with('akun_mahasiswa', $result);
    }

    public function pesertaReject(Request $request, $id)
    {
        $request->validate(['catatan_admin' => 'required|string'], [
            'catatan_admin.required' => 'Alasan penolakan wajib diisi.',
        ]);
        $peserta = BookingPeserta::findOrFail($id);
        $peserta->update(['status' => 'rejected', 'catatan_admin' => $request->catatan_admin]);
        return back()->with('success', 'Peserta ditolak.');
    }

    /**
     * Sisa kuota ruangan untuk periode tertentu.
     * Basis kapasitas = kuota_ruangan; dikurangi booking approved yang periodenya
     * beririsan dengan periode yang diminta.
     */
    private function sisaKuota(Ruangan $ruangan, $start, $end, $excludeId = null)
    {
        $kapasitas = (int) ($ruangan->kuota_ruangan ?? 0);

        $terpakai = BookingRuangan::where('ruangan_id', $ruangan->id)
            ->where('status', 'approved')
            ->when($excludeId, function ($q) use ($excludeId) {
                $q->where('id', '!=', $excludeId);
            })
            ->where('tanggal_mulai', '<=', $end)
            ->where('tanggal_selesai', '>=', $start)
            ->sum('jumlah_peserta');

        return max(0, $kapasitas - (int) $terpakai);
    }
}
