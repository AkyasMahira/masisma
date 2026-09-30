<?php

namespace App\Http\Controllers;

use App\Models\BookingRuangan;
use App\Models\BookingPeserta;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

        $bookings = BookingRuangan::with('ruangan')
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

        return view('instansi.booking_create', ['mou' => $user->mou, 'ruangans' => $ruangans]);
    }

    public function bookingStore(Request $request)
    {
        $user = $this->instansi();

        $data = $request->validate([
            'ruangan_id'     => 'required|exists:ruangans,id',
            'jumlah_peserta' => 'required|integer|min:1',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai'=> 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'     => 'nullable|string',
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
            'jumlah_peserta'  => $data['jumlah_peserta'],
            'tanggal_mulai'   => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'keterangan'      => $data['keterangan'] ?? null,
            'status'          => 'pending',
        ]);

        return redirect()->route('instansi.dashboard')->with('success', 'Permintaan booking ruangan terkirim. Menunggu persetujuan admin diklat.');
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

        $data = $request->validate([
            'nama'          => 'required|string|max:255',
            'nim'           => 'nullable|string|max:100',
            'prodi'         => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_hp'         => 'nullable|string|max:30',
            'keterangan'    => 'nullable|string',
        ], [
            'nama.required' => 'Nama peserta wajib diisi.',
        ]);

        // Batasi jumlah peserta sesuai kuota booking yang diajukan
        if ($booking->pesertas()->count() >= $booking->jumlah_peserta) {
            return back()->with('error', "Jumlah peserta sudah mencapai batas booking ({$booking->jumlah_peserta} orang). Ajukan booking tambahan bila perlu.");
        }

        $data['booking_ruangan_id'] = $booking->id;
        BookingPeserta::create($data);

        return back()->with('success', 'Peserta magang berhasil ditambahkan.');
    }

    public function pesertaDestroy($bookingId, $pesertaId)
    {
        $booking = $this->ownBooking($bookingId);
        $peserta = BookingPeserta::where('booking_ruangan_id', $booking->id)->findOrFail($pesertaId);
        $peserta->delete();
        return back()->with('success', 'Peserta magang dihapus.');
    }

    /* ======================= SISI ADMIN ======================= */

    public function adminIndex(Request $request)
    {
        $query = BookingRuangan::with(['mou', 'ruangan'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate(15)->withQueryString();
        $jmlPending = BookingRuangan::where('status', 'pending')->count();

        return view('admin.booking.index', compact('bookings', 'jmlPending'));
    }

    public function adminApprove($id)
    {
        $booking = BookingRuangan::with('ruangan')->findOrFail($id);

        // Re-cek kuota saat approve (kondisi bisa berubah sejak diajukan)
        $sisa = $this->sisaKuota($booking->ruangan, $booking->tanggal_mulai, $booking->tanggal_selesai, $booking->id);
        if ($booking->jumlah_peserta > $sisa) {
            return back()->with('error',
                "Tidak bisa menyetujui: sisa kuota {$booking->ruangan->nm_ruangan} tinggal {$sisa} orang untuk periode itu (diminta {$booking->jumlah_peserta}).");
        }

        $booking->update(['status' => 'approved', 'catatan_admin' => 'Disetujui.']);
        return back()->with('success', 'Booking disetujui.');
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
