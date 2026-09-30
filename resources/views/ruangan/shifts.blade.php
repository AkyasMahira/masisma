@extends('layouts.app')

@section('title', 'Atur Jam Kerja Ruangan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        
        <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded-4 shadow-sm mb-4" style="border-left: 5px solid #7c1316;">
            <div>
                <h5 class="fw-bold mb-1 text-dark">Pengaturan Jam Kerja / Shift</h5>
                <p class="text-muted mb-0 small">Ruangan: <strong>{{ $ruangan->nm_ruangan }}</strong> (Kategori: {{ strtoupper($ruangan->kategori) }})</p>
            </div>
            <a href="{{ route('ruangan.index') }}" class="btn btn-light rounded-pill border shadow-sm px-4">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
        </div>

        <div class="row g-4">
            {{-- FORM TAMBAH SHIFT --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-plus-circle-fill text-danger me-2"></i>Tambah Jam Baru</h6>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('ruangan.shifts.store', $ruangan->id) }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Nama Shift / Jam <span class="text-danger">*</span></label>
                                <input type="text" name="nama_shift" class="form-control" placeholder="Contoh: Pagi, Reguler, Jumat" required>
                                <small class="text-muted" style="font-size: 0.7rem;">Pastikan penamaan mudah dipahami.</small>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Jam Masuk <span class="text-danger">*</span></label>
                                    <input type="time" name="jam_masuk" class="form-control" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">Jam Keluar <span class="text-danger">*</span></label>
                                    <input type="time" name="jam_keluar" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="lintas_hari" id="lintasHari">
                                    <label class="form-check-label small fw-bold text-dark" for="lintasHari">Shift Lintas Hari (Malam)</label>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Aktifkan ini HANYA JIKA jam keluar berada di keesokan harinya (contoh: Masuk 21:00, Keluar 07:00).</small>
                            </div>

                            <button type="submit" class="btn w-100 rounded-pill text-white fw-bold shadow-sm" style="background-color: #7c1316;">
                                Simpan Jam Kerja
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- DAFTAR SHIFT --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-list-task text-danger me-2"></i>Daftar Jam Terdaftar</h6>
                        
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-3">Nama Shift</th>
                                        <th class="px-3 text-center">Jam Kerja</th>
                                        <th class="px-3 text-center">Lintas Hari</th>
                                        <th class="px-3 text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ruangan->roomShifts as $shift)
                                    <tr>
                                        <td class="px-3 fw-bold text-dark">{{ $shift->nama_shift }}</td>
                                        <td class="px-3 text-center text-nowrap">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                {{ \Carbon\Carbon::parse($shift->jam_masuk)->format('H:i') }}
                                            </span>
                                            <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                                {{ \Carbon\Carbon::parse($shift->jam_keluar)->format('H:i') }}
                                            </span>
                                        </td>
                                        <td class="px-3 text-center">
                                            @if($shift->lintas_hari)
                                                <span class="badge bg-dark text-white"><i class="bi bi-moon-stars-fill me-1"></i> Ya</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 text-end">
                                            <form action="{{ route('ruangan.shifts.destroy', $shift->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="bi bi-clock-history display-4 opacity-25 d-block mb-2"></i>
                                            Belum ada jam kerja yang diatur.<br>
                                            <small>(Ruangan akan menggunakan jam default sistem lama jika tidak diisi)</small>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection