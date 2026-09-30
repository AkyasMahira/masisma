@extends('layouts.app')

@section('title', 'Edit Dispensasi')

@section('content')
<style>
    :root { --maroon: #7c1316; }
    .bg-maroon { background-color: var(--maroon); color: white; }
    .btn-maroon { background-color: var(--maroon); color: white; border: none; transition: 0.3s; }
    .btn-maroon:hover { background-color: #5a0e10; color: white; transform: translateY(-2px); }
    .form-label-custom { font-weight: 700; font-size: 0.75rem; color: #666; text-transform: uppercase; letter-spacing: 1px; }
</style>

<div class="">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-maroon py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i> Review Pengajuan Dispensasi</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.dispensasi.update', $dispensasi->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            {{-- Info Mahasiswa --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label-custom">Nama Mahasiswa</label>
                                <input type="text" class="form-control bg-light border-0" value="{{ $dispensasi->mahasiswa->nm_mahasiswa }}" readonly>
                            </div>

                            {{-- Kategori Dispensasi --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label-custom">Kategori Izin</label>
                                <select name="kategori" id="kategori_select" class="form-select border-2" required>
                                    <option value="biasa" {{ $dispensasi->kategori == 'biasa' ? 'selected' : '' }}>Izin / Sakit (Biasa)</option>
                                    <option value="terlambat" {{ $dispensasi->kategori == 'terlambat' ? 'selected' : '' }}>Dispensasi Terlambat</option>
                                </select>
                            </div>

                            {{-- ========================================== --}}
                            {{-- AREA NOTIFIKASI OTOMATIS (JS DRIVEN) --}}
                            {{-- ========================================== --}}
                            
                            {{-- Notifikasi Shift Malam --}}
                            <div class="col-12" id="notif-shift-malam" style="display: none;">
                                <div class="alert alert-info border-info shadow-sm d-flex align-items-start p-3 mb-1">
                                    <i class="bi bi-moon-stars-fill fs-4 me-3 text-info mt-1"></i>
                                    <div>
                                        <strong class="text-dark">Info Auto-Generate Absen:</strong><br>
                                        <small class="text-dark">Sistem akan memaksa pembuatan absen sesuai kata kunci keterangan (Pagi/Siang/Malam). Jika ada kata "malam", sistem otomatis mengatur jam menjadi 21:00 s/d 07:00 (lintas hari).</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Notifikasi Backdate --}}
                            <div class="col-12" id="notif-backdate" style="display: none;">
                                <div class="alert alert-warning border-warning shadow-sm d-flex align-items-start p-3 mb-1">
                                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning mt-1"></i>
                                    <div>
                                        <strong class="text-dark">Peringatan: Backdate Aktif!</strong><br>
                                        <small class="text-dark">Anda mengatur tanggal mundur ke masa lalu. Jika disetujui, sistem akan <b>memaksa/menyisipkan</b> rekam jejak absensi pada tanggal tersebut secara otomatis.</small>
                                    </div>
                                </div>
                            </div>
                            {{-- ========================================== --}}

                            {{-- Tanggal --}}
                            <div class="col-md-6 mb-3 mt-3">
                                <label class="form-label-custom">Tgl Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ $dispensasi->tanggal_mulai }}" required>
                            </div>
                            <div class="col-md-6 mb-3 mt-3">
                                <label class="form-label-custom">Tgl Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-control" value="{{ $dispensasi->tanggal_selesai }}" required>
                            </div>

                            {{-- Status --}}
                            <div class="col-12 mb-3">
                                <label class="form-label-custom">Keputusan Admin</label>
                                <select name="status" class="form-select border-2 fw-bold {{ $dispensasi->status == 'rejected' ? 'text-danger' : ($dispensasi->status == 'approved' ? 'text-success' : 'text-warning') }}">
                                    <option value="pending" {{ $dispensasi->status == 'pending' ? 'selected' : '' }}>🟡 PENDING (Menunggu)</option>
                                    <option value="approved" {{ $dispensasi->status == 'approved' ? 'selected' : '' }}>🟢 APPROVED (Setujui & Paksa Isi Absen)</option>
                                    <option value="rejected" {{ $dispensasi->status == 'rejected' ? 'selected' : '' }}>🔴 REJECTED (Tolak Pengajuan)</option>
                                </select>
                            </div>

                            {{-- Catatan --}}
                            <div class="col-12 mb-4">
                                <label class="form-label-custom">Catatan Admin / Alasan Penolakan</label>
                                <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Contoh: Lampiran foto absensi ruangan tidak ada...">{{ $dispensasi->catatan_admin }}</textarea>
                                <small class="text-muted fst-italic">Wajib diisi jika status ditolak agar mahasiswa tahu alasan penolakannya.</small>
                            </div>

                            {{-- Preview File --}}
                            @if($dispensasi->file_path)
                            <div class="col-12 mb-4">
                                <label class="form-label-custom">Preview Berkas PDF</label>
                                <div class="border rounded p-2 bg-light">
                                    <a href="{{ asset('storage/' . $dispensasi->file_path) }}" target="_blank" class="btn btn-sm btn-outline-dark mb-2">
                                        <i class="bi bi-fullscreen me-1"></i> Buka Fullscreen
                                    </a>
                                    <iframe src="{{ asset('storage/' . $dispensasi->file_path) }}" width="100%" height="400px"></iframe>
                                </div>
                            </div>
                            @endif

                            <div class="col-12">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-maroon py-3 rounded-pill fw-bold shadow-sm">
                                        <i class="bi bi-check-all me-1"></i> SIMPAN PERUBAHAN DATA
                                    </button>
                                    <a href="{{ route('admin.dispensasi.index') }}" class="btn btn-link text-muted text-decoration-none small text-center">Batal dan Kembali</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elemen-elemen DOM
        const kategoriSelect = document.getElementById('kategori_select');
        const notifShiftMalam = document.getElementById('notif-shift-malam');
        
        const tanggalMulai = document.getElementById('tanggal_mulai');
        const notifBackdate = document.getElementById('notif-backdate');

        // Fungsi Deteksi Shift Malam (Dari Dropdown Kategori)
        function checkKategori() {
            if (kategoriSelect.value === 'terlambat') {
                notifShiftMalam.style.display = 'block';
            } else {
                notifShiftMalam.style.display = 'none';
            }
        }

        // Fungsi Deteksi Backdate (Dari Input Tanggal)
        function checkBackdate() {
            if (!tanggalMulai.value) return;

            // Ambil tanggal hari ini (reset jam menjadi 00:00:00 untuk perbandingan presisi)
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // Ambil tanggal dari form input
            const inputDate = new Date(tanggalMulai.value);
            inputDate.setHours(0, 0, 0, 0);

            // Jika tanggal input lebih kecil dari hari ini, munculkan notif kuning
            if (inputDate < today) {
                notifBackdate.style.display = 'block';
            } else {
                notifBackdate.style.display = 'none';
            }
        }

        // Tambahkan Event Listener (Setiap input diubah, jalankan fungsi)
        kategoriSelect.addEventListener('change', checkKategori);
        tanggalMulai.addEventListener('change', checkBackdate);

        // Jalankan Pengecekan saat halaman pertama kali dibuka (Load)
        checkKategori();
        checkBackdate();
    });
</script>
@endsection