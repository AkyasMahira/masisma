<?php

namespace App\Http\Controllers;

use App\Models\BookingRuangan;
use App\Models\BookingPeserta;
use App\Models\Ruangan;
use App\Models\Mou;
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
        return view('instansi.booking_create', $this->bookingFormData($user->mou->id) + [
            'isAdmin'   => false,
            'mou'       => $user->mou,
            'action'    => route('instansi.booking.store'),
            'cancelUrl' => route('instansi.dashboard'),
        ]);
    }

    /**
     * Data form booking (dipakai portal instansi & admin):
     * ruangan + kuota, master prodi, event kalender okupansi, dan data okupansi
     * (booking approved) untuk cek kuota langsung di browser.
     * $myMouId terisi = mode instansi (hanya lihat booking approved + miliknya).
     */
    private function bookingFormData($myMouId = null)
    {
        $ruangans = Ruangan::orderBy('nm_ruangan')->get(['id', 'nm_ruangan', 'kuota_ruangan']);

        $bookings = BookingRuangan::with(['ruangan:id,nm_ruangan', 'mou:id,nama_instansi,nama_universitas'])
            ->where('status', '!=', 'rejected')
            ->when($myMouId, function ($q) use ($myMouId) {
                $q->where(function ($w) use ($myMouId) {
                    $w->where('status', 'approved')->orWhere('mou_id', $myMouId);
                });
            })
            ->orderBy('tanggal_mulai')
            ->get();

        $palette = ['#7c1316', '#1d4ed8', '#15803d', '#b45309', '#7e22ce', '#0e7490', '#be123c', '#4d7c0f', '#0f766e', '#9333ea'];
        $events = []; $occ = [];
        foreach ($bookings as $b) {
            if (!$b->tanggal_mulai || !$b->tanggal_selesai) continue;
            $mine = $myMouId && (int) $b->mou_id === (int) $myMouId;
            $namaRuangan = optional($b->ruangan)->nm_ruangan ?? 'Ruangan';
            $namaInstansi = optional($b->mou)->nama_instansi ?: (optional($b->mou)->nama_universitas ?: 'Instansi');
            $warna = $b->status === 'pending' ? '#94a3b8' : $palette[($b->ruangan_id ?? 0) % count($palette)];
            if ($myMouId && !$mine) {
                $title = $namaRuangan . ' · terisi ' . $b->jumlah_peserta . ' org';
                $warna = '#cbd5e1';
            } else {
                $title = ($mine ? 'Booking Anda' : $namaInstansi) . ' (' . $b->jumlah_peserta . ') · ' . $namaRuangan;
            }
            $events[] = [
                'title' => $title,
                'start' => $b->tanggal_mulai->format('Y-m-d'),
                'end'   => $b->tanggal_selesai->copy()->addDay()->format('Y-m-d'),
                'color' => $warna,
                'textColor' => ($myMouId && !$mine) ? '#334155' : '#fff',
                'extendedProps' => [
                    'rid'     => (int) $b->ruangan_id,
                    'mine'    => (bool) $mine,
                    'status'  => $b->status,
                    'ruangan' => $namaRuangan,
                    'instansi'=> $myMouId && !$mine ? null : $namaInstansi,
                    'periode' => $b->tanggal_mulai->format('d/m/Y') . ' - ' . $b->tanggal_selesai->format('d/m/Y'),
                    'jumlah'  => $b->jumlah_peserta,
                ],
            ];
            if ($b->status === 'approved') {
                $occ[] = ['r' => (int) $b->ruangan_id, 's' => $b->tanggal_mulai->format('Y-m-d'), 'e' => $b->tanggal_selesai->format('Y-m-d'), 'n' => (int) $b->jumlah_peserta, 'i' => $namaInstansi];
            }
        }

        return [
            'ruangans'    => $ruangans,
            'listProdi'   => \App\Models\MasterProdi::grouped(),
            'jenjangList' => \App\Models\MasterProdi::jenjangList(),
            'events'      => $events,
            'occ'         => $occ,
        ];
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
            ->with(['roomSequences.ruangan', 'ruangan', 'absensis', 'shiftSchedules', 'dispensasis'])
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
            $m->stat = $m->statistik; // absensi: hadir_fisik, dispensasi, alpha, target
            $m->persen_hadir = round($m->absensi_percentage);
            $m->dispensasiApproved = $m->dispensasis->where('status', 'approved')->sortByDesc('tanggal_mulai');
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
        return $pdf->stream(\App\Support\NamaFile::aman('Sertifikat-Orientasi-' . ($akun->name ?? $m->nm_mahasiswa) . '.pdf'));
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
        $q        = trim((string) $request->get('q', ''));
        $fRuangan = $request->get('ruangan');
        $fJenjang = $request->get('jenjang');
        $fStart   = $request->get('start');
        $fEnd     = $request->get('end');

        $query = BookingRuangan::with(['mou', 'ruangan', 'pesertas'])
            ->withCount('pesertas')
            ->select('booking_ruangans.*');

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $request->status);
        }
        if ($fRuangan) $query->where('ruangan_id', $fRuangan);
        if ($fJenjang) $query->where('jenjang', $fJenjang);
        if ($fStart)   $query->whereDate('tanggal_selesai', '>=', $fStart);
        if ($fEnd)     $query->whereDate('tanggal_mulai', '<=', $fEnd);
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('prodi', 'like', "%{$q}%")
                  ->orWhereHas('mou', function ($m) use ($q) {
                      $m->where('nama_instansi', 'like', "%{$q}%")->orWhere('nama_universitas', 'like', "%{$q}%");
                  });
            });
        }

        // ===== Sorting per kolom =====
        $sort = $request->get('sort', 'created');
        $dir  = $request->get('dir', 'desc') === 'asc' ? 'asc' : 'desc';
        switch ($sort) {
            case 'instansi':
                $query->leftJoin('mous', 'mous.id', '=', 'booking_ruangans.mou_id')
                      ->orderByRaw('COALESCE(mous.nama_instansi, mous.nama_universitas) ' . $dir);
                break;
            case 'ruangan':
                $query->leftJoin('ruangans', 'ruangans.id', '=', 'booking_ruangans.ruangan_id')
                      ->orderBy('ruangans.nm_ruangan', $dir);
                break;
            case 'kuota':    $query->orderBy('jumlah_peserta', $dir); break;
            case 'peserta':  $query->orderBy('pesertas_count', $dir); break;
            case 'periode':  $query->orderBy('tanggal_mulai', $dir); break;
            case 'status':   $query->orderBy('status', $dir); break;
            default:         $query->orderBy('created_at', $dir); $sort = 'created';
        }

        $bookings = $query->paginate(15)->withQueryString();
        $jmlPending = BookingRuangan::where('status', 'pending')->count();
        $mhsMap = $this->mahasiswaPerBooking($bookings->getCollection());

        $ruanganOptions = Ruangan::orderBy('nm_ruangan')->pluck('nm_ruangan', 'id');
        $jenjangOptions = BookingRuangan::whereNotNull('jenjang')->where('jenjang', '!=', '')
            ->distinct()->orderBy('jenjang')->pluck('jenjang');

        return view('admin.booking.index', compact('bookings', 'jmlPending', 'ruanganOptions', 'jenjangOptions', 'sort', 'dir', 'mhsMap'));
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

    /** Admin: form tambah booking atas nama instansi. */
    public function adminCreateBooking()
    {
        $mouOptions = Mou::orderBy('nama_universitas')->get(['id', 'nama_instansi', 'nama_universitas'])
            ->mapWithKeys(function ($m) { return [$m->id => ($m->nama_instansi ?: $m->nama_universitas)]; })
            ->sort();

        return view('instansi.booking_create', $this->bookingFormData(null) + [
            'isAdmin'    => true,
            'mou'        => null,
            'mouOptions' => $mouOptions,
            'action'     => route('admin.booking.store'),
            'cancelUrl'  => route('admin.booking.index'),
        ]);
    }

    /** Admin: simpan booking baru (boleh langsung disetujui, dengan cek kuota). */
    public function adminStoreBooking(Request $request)
    {
        $data = $request->validate([
            'mou_id'         => 'required|exists:mous,id',
            'ruangan_id'     => 'required|exists:ruangans,id',
            'jenjang'        => 'nullable|string|max:20',
            'prodi'          => 'nullable|string|max:255',
            'semester'       => 'nullable|string|max:20',
            'jumlah_peserta' => 'required|integer|min:1',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai'=> 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'     => 'nullable|string',
            'status'         => 'required|in:pending,approved',
            'batas_pengisian'=> 'nullable|date',
            'kompetensi_dimiliki'   => 'nullable|array',
            'kompetensi_dimiliki.*' => 'nullable|string|max:255',
        ], [
            'mou_id.required'     => 'Pilih instansi.',
            'ruangan_id.required' => 'Pilih ruangan tujuan.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ]);

        $ruangan = Ruangan::findOrFail($data['ruangan_id']);
        if ($data['status'] === 'approved') {
            $sisa = $this->sisaKuota($ruangan, $data['tanggal_mulai'], $data['tanggal_selesai']);
            if ($data['jumlah_peserta'] > $sisa) {
                return back()->withInput()->with('error',
                    "Sisa kuota {$ruangan->nm_ruangan} untuk periode itu hanya {$sisa} orang. Kurangi jumlah, ganti periode/ruangan, atau simpan sebagai Menunggu.");
            }
        }

        BookingRuangan::create([
            'mou_id'          => $data['mou_id'],
            'user_id'         => auth()->id(),
            'ruangan_id'      => $ruangan->id,
            'jenjang'         => $data['jenjang'] ?? null,
            'prodi'           => $data['prodi'] ?? null,
            'semester'        => $data['semester'] ?? null,
            'kompetensi_dimiliki_json' => $request->filled('kompetensi_dimiliki') ? array_values(array_filter($request->kompetensi_dimiliki)) : [],
            'jumlah_peserta'  => $data['jumlah_peserta'],
            'tanggal_mulai'   => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'keterangan'      => $data['keterangan'] ?? null,
            'status'          => $data['status'],
            'batas_pengisian' => $data['batas_pengisian'] ?? null,
            'catatan_admin'   => 'Dibuat oleh admin.',
        ]);

        return redirect()->route('admin.booking.index')->with('success',
            'Booking ditambahkan' . ($data['status'] === 'approved' ? ' dan langsung disetujui.' : ' (status menunggu).'));
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
    public function adminKalender(Request $request)
    {
        // ===== Filter =====
        $fRuangan  = $request->get('ruangan');
        $fMou      = $request->get('instansi');
        $fJenjang  = $request->get('jenjang');
        $fStatus   = $request->get('status');          // pending | approved | selesai | berjalan | akan
        $fStart    = $request->get('start');
        $fEnd      = $request->get('end');
        $q         = trim((string) $request->get('q', ''));

        $query = BookingRuangan::with(['mou', 'ruangan', 'pesertas'])
            ->where('status', '!=', 'rejected');

        if ($fRuangan) $query->where('ruangan_id', $fRuangan);
        if ($fMou)     $query->where('mou_id', $fMou);
        if ($fJenjang) $query->where('jenjang', $fJenjang);
        if (in_array($fStatus, ['pending', 'approved'], true)) $query->where('status', $fStatus);
        if ($fStart)   $query->whereDate('tanggal_selesai', '>=', $fStart);
        if ($fEnd)     $query->whereDate('tanggal_mulai', '<=', $fEnd);
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('prodi', 'like', "%{$q}%")
                  ->orWhereHas('mou', function ($m) use ($q) {
                      $m->where('nama_instansi', 'like', "%{$q}%")->orWhere('nama_universitas', 'like', "%{$q}%");
                  });
            });
        }

        $bookings = $query->orderBy('tanggal_mulai')->get();

        // Filter turunan (selesai/berjalan/akan) berbasis tanggal
        $today = now()->startOfDay();
        if (in_array($fStatus, ['selesai', 'berjalan', 'akan'], true)) {
            $bookings = $bookings->filter(function ($b) use ($today, $fStatus) {
                $m = optional($b->tanggal_mulai); $s = optional($b->tanggal_selesai);
                if (!$m || !$s) return false;
                if ($fStatus === 'selesai')  return $s->lt($today);
                if ($fStatus === 'berjalan') return $m->lte($today) && $s->gte($today);
                if ($fStatus === 'akan')     return $m->gt($today);
                return true;
            })->values();
        }

        $palette = ['#7c1316', '#1d4ed8', '#15803d', '#b45309', '#7e22ce', '#0e7490', '#be123c', '#4d7c0f', '#0f766e', '#9333ea'];
        $mhsMap = $this->mahasiswaPerBooking($bookings);

        $events = [];
        $perRuangan = [];
        foreach ($bookings as $b) {
            $namaRuangan = optional($b->ruangan)->nm_ruangan ?? 'Tanpa Ruangan';
            $namaInstansi = optional($b->mou)->nama_instansi ?? optional($b->mou)->nama_universitas ?? 'Instansi';
            $m = optional($b->tanggal_mulai); $s = optional($b->tanggal_selesai);
            $fase = (!$m || !$s) ? '-' : ($s->lt($today) ? 'Selesai' : ($m->gt($today) ? 'Akan Datang' : 'Berjalan'));
            $warna = $b->status === 'pending' ? '#94a3b8' : $palette[($b->ruangan_id ?? 0) % count($palette)];

            $events[] = [
                'title' => $namaInstansi . ' (' . $b->jumlah_peserta . ' org) · ' . $namaRuangan,
                'start' => $m ? $m->format('Y-m-d') : null,
                'end'   => $s ? $s->copy()->addDay()->format('Y-m-d') : null,
                'color' => $warna,
                'extendedProps' => [
                    'id'       => $b->id,
                    'instansi' => $namaInstansi,
                    'ruangan'  => $namaRuangan,
                    'rc'       => $palette[($b->ruangan_id ?? 0) % count($palette)],
                    'peserta'  => $b->jumlah_peserta,
                    'status'   => $b->status,
                    'fase'     => $fase,
                    'periode'  => ($m ? $m->format('d/m/Y') : '-') . ' - ' . ($s ? $s->format('d/m/Y') : '-'),
                    'prodi'    => self::prodiLabel($b),
                    'impor'    => Str::contains((string) $b->keterangan, '[IMPOR-MAPPING]'),
                    'ket'      => trim(str_replace('[IMPOR-MAPPING]', '', (string) $b->keterangan)),
                    'pesertaList' => $b->pesertas->map(function ($p) {
                        return ['nama' => $p->nama, 'nim' => $p->nim, 'prodi' => $p->prodi, 'status' => $p->status];
                    })->values(),
                    'mahasiswa' => $mhsMap[$b->id] ?? [],
                ],
            ];
            $perRuangan[$namaRuangan][] = $b;
        }
        ksort($perRuangan);

        // Opsi filter
        $ruanganOptions  = Ruangan::orderBy('nm_ruangan')->pluck('nm_ruangan', 'id');
        $instansiOptions = Mou::orderBy('nama_universitas')->get()
            ->mapWithKeys(function ($m) { return [$m->id => ($m->nama_instansi ?: $m->nama_universitas)]; });
        $jenjangOptions  = BookingRuangan::whereNotNull('jenjang')->where('jenjang', '!=', '')
            ->distinct()->orderBy('jenjang')->pluck('jenjang');

        return view('admin.booking.kalender', [
            'events'     => $events,
            'perRuangan' => $perRuangan,
            'ruanganOptions'  => $ruanganOptions,
            'instansiOptions' => $instansiOptions,
            'jenjangOptions'  => $jenjangOptions,
            'f' => compact('fRuangan', 'fMou', 'fJenjang', 'fStatus', 'fStart', 'fEnd', 'q'),
            'total' => count($events),
        ]);
    }

    /** Label prodi tanpa dobel jenjang ("S1 S1 Keperawatan" -> "S1 Keperawatan"). */
    public static function prodiLabel($b)
    {
        $prodi = trim((string) $b->prodi);
        $label = $prodi !== '' ? $prodi : trim((string) $b->jenjang);
        if ($b->semester) $label .= ($label !== '' ? ' · ' : '') . 'smt ' . $b->semester;
        return $label;
    }

    private static function normInstansi($n)
    {
        $n = mb_strtolower(preg_replace('/\(.*?\)/', '', (string) $n));
        return preg_replace('/[^a-z0-9]/', '', $n);
    }

    /**
     * Cocokkan mahasiswa ke booking: instansi sama (mou_id / nama instansi),
     * ruangan sama, dan rentang tanggal beririsan.
     * @return array [booking_id => [['nama','prodi','status','mulai','selesai'], ...]]
     */
    private function mahasiswaPerBooking($bookings)
    {
        $out = [];
        if (!$bookings || !count($bookings)) return $out;

        $ruanganIds = collect($bookings)->pluck('ruangan_id')->filter()->unique()->values()->all();
        if (!$ruanganIds) return $out;
        $minStart = collect($bookings)->map(function ($b) { return optional($b->tanggal_mulai)->format('Y-m-d'); })->filter()->min();
        $maxEnd   = collect($bookings)->map(function ($b) { return optional($b->tanggal_selesai)->format('Y-m-d'); })->filter()->max();

        // Kandidat: dari room_sequences + kolom legacy ruangan_id di mahasiswa
        $cand = [];
        $seqs = \App\Models\RoomSequence::with('mahasiswa.mou')
            ->whereIn('ruangan_id', $ruanganIds)
            ->when($maxEnd, function ($q) use ($maxEnd) { $q->whereDate('start_date', '<=', $maxEnd); })
            ->when($minStart, function ($q) use ($minStart) { $q->whereDate('end_date', '>=', $minStart); })
            ->get();
        foreach ($seqs as $sq) {
            if (!$sq->mahasiswa) continue;
            $cand[] = [$sq->mahasiswa, $sq->ruangan_id, substr((string) $sq->start_date, 0, 10), substr((string) $sq->end_date, 0, 10)];
        }
        $legacy = Mahasiswa::with('mou')->whereIn('ruangan_id', $ruanganIds)
            ->whereNotNull('tanggal_mulai')->whereNotNull('tanggal_berakhir')
            ->when($maxEnd, function ($q) use ($maxEnd) { $q->whereDate('tanggal_mulai', '<=', $maxEnd); })
            ->when($minStart, function ($q) use ($minStart) { $q->whereDate('tanggal_berakhir', '>=', $minStart); })
            ->get();
        foreach ($legacy as $mh) {
            $cand[] = [$mh, $mh->ruangan_id, substr((string) $mh->tanggal_mulai, 0, 10), substr((string) $mh->tanggal_berakhir, 0, 10)];
        }

        foreach ($bookings as $b) {
            $bs = optional($b->tanggal_mulai)->format('Y-m-d');
            $be = optional($b->tanggal_selesai)->format('Y-m-d');
            if (!$bs || !$be) continue;
            $bNama = self::normInstansi(optional($b->mou)->nama_instansi ?: optional($b->mou)->nama_universitas);
            $seen = [];
            foreach ($cand as $c) {
                list($mh, $rid, $ms, $me) = $c;
                if ((int) $rid !== (int) $b->ruangan_id || isset($seen[$mh->id])) continue;
                if ($ms > $be || $me < $bs) continue;
                $sameMou = $mh->mou_id && (int) $mh->mou_id === (int) $b->mou_id;
                if (!$sameMou) {
                    $mNama = self::normInstansi(optional($mh->mou)->nama_instansi ?: optional($mh->mou)->nama_universitas);
                    if ($mNama === '' || $mNama !== $bNama) continue;
                }
                $seen[$mh->id] = true;
                $out[$b->id][] = [
                    'id'      => $mh->id,
                    'nama'    => $mh->nm_mahasiswa,
                    'prodi'   => $mh->prodi,
                    'status'  => $mh->status,
                    'mulai'   => $ms ? Carbon::parse($ms)->format('d/m') : '',
                    'selesai' => $me ? Carbon::parse($me)->format('d/m/y') : '',
                ];
            }
            if (!empty($out[$b->id])) {
                usort($out[$b->id], function ($x, $y) { return strcmp((string) $x['nama'], (string) $y['nama']); });
            }
        }
        return $out;
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
            // 1. Akun login untuk anak magang — CEGAH AKUN DOBEL.
            //    Jika email peserta sudah terdaftar, REUSE akun itu (periode baru pakai akun sama).
            $existing = $peserta->email ? User::where('email', $peserta->email)->first() : null;
            if ($existing) {
                $user = $existing;
                $password = null; // akun lama, password tidak di-reset
            } else {
                if ($peserta->email) {
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
            }
            $email = $user->email;

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

            return ['username' => $email, 'password' => $password, 'nama' => $peserta->nama, 'reused' => (bool) !$password];
        });

        $msg = $result['reused']
            ? 'Peserta disetujui — memakai akun mahasiswa yang sudah ada (tidak dibuat akun baru).'
            : 'Peserta disetujui — akun mahasiswa dibuat.';
        return back()->with('success', $msg)->with('akun_mahasiswa', $result);
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
