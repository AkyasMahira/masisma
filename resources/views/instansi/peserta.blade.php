@extends('layouts.app')

@section('title', 'Daftar Anak Magang')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; --radius:14px; --shadow:0 4px 20px rgba(0,0,0,.05); }
    .card-soft { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); }
    .btn-maroon { background:var(--maroon); color:#fff; border:none; border-radius:10px; font-weight:700; }
    .btn-maroon:hover { background:var(--maroon-light); color:#fff; }
    .form-control, .form-select { border-radius:9px; }
    .form-control:focus, .form-select:focus { border-color:var(--maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.12); }
    .table thead th { background:var(--maroon-subtle); color:var(--maroon); font-size:.76rem; text-transform:uppercase; }
    .info-head { background: linear-gradient(135deg,#7c1316,#5f0f12); color:#fff; border-radius:var(--radius); padding:20px 24px; margin-bottom:1.25rem; }
    .info-head .meta { opacity:.9; font-size:.85rem; }
    .cap-pill { background:rgba(255,255,255,.18); border-radius:20px; padding:4px 14px; font-weight:700; font-size:.85rem; }
    .pill { padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:700; }
    .b-pending { background:#fef3c7; color:#b45309; } .b-approved { background:#dcfce7; color:#15803d; } .b-rejected { background:#fee2e2; color:#b91c1c; }
</style>

<div class="container-fluid py-3">
    <a href="{{ route('instansi.dashboard') }}" class="btn btn-light border mb-3"><i class="bi bi-arrow-left me-1"></i> Kembali</a>

    <div class="info-head d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-people-fill me-2"></i>Daftar Anak Magang</h5>
            <div class="meta">
                <i class="bi bi-door-open me-1"></i>{{ optional($booking->ruangan)->nm_ruangan }}
                &nbsp;·&nbsp; <i class="bi bi-calendar-range me-1"></i>{{ optional($booking->tanggal_mulai)->format('d/m/Y') }} - {{ optional($booking->tanggal_selesai)->format('d/m/Y') }}
                &nbsp;·&nbsp; Status: {{ ['pending'=>'Menunggu','approved'=>'Disetujui','rejected'=>'Ditolak'][$booking->status] ?? $booking->status }}
            </div>
        </div>
        <span class="cap-pill">{{ $booking->pesertas->count() }} / {{ $booking->jumlah_peserta }} peserta</span>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <div class="row g-3">
        {{-- Form tambah --}}
        <div class="col-lg-4">
            <div class="card-soft p-3">
                <h6 class="fw-bold mb-3" style="color:var(--maroon);"><i class="bi bi-person-plus me-1"></i> Tambah Peserta</h6>
                @if($booking->batas_pengisian)
                    <div class="alert {{ $booking->pengisianDibuka() ? 'alert-info' : 'alert-secondary' }} small py-2">
                        <i class="bi bi-clock me-1"></i>Batas pengisian: <strong>{{ $booking->batas_pengisian->format('d/m/Y') }}</strong>
                        {{ $booking->pengisianDibuka() ? '' : '(sudah ditutup)' }}
                    </div>
                @endif
                @if(!$booking->pengisianDibuka())
                    <div class="alert alert-secondary small mb-0">Pengisian peserta sudah <strong>ditutup</strong> (melewati batas dari admin). Hubungi admin diklat bila perlu menambah.</div>
                @elseif($booking->pesertas->count() >= $booking->jumlah_peserta)
                    <div class="alert alert-warning small mb-0">Kuota peserta booking ini sudah penuh ({{ $booking->jumlah_peserta }} orang).</div>
                @else
                <form action="{{ route('instansi.booking.peserta.store', $booking->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-2"><label class="form-label small fw-semibold">Nama <span class="text-danger">*</span></label><input type="text" name="nama" class="form-control form-control-sm" value="{{ old('nama') }}" required></div>
                    <div class="row g-2">
                        <div class="col-6 mb-2"><label class="form-label small fw-semibold">NIM</label><input type="text" name="nim" class="form-control form-control-sm" value="{{ old('nim') }}"></div>
                        <div class="col-6 mb-2"><label class="form-label small fw-semibold">JK</label>
                            <select name="jenis_kelamin" class="form-select form-select-sm">
                                <option value="">-</option>
                                <option value="L" {{ old('jenis_kelamin')==='L'?'selected':'' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin')==='P'?'selected':'' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-2"><label class="form-label small fw-semibold">Email <span class="text-muted">(untuk akun login)</span></label><input type="email" name="email" class="form-control form-control-sm" value="{{ old('email') }}" placeholder="opsional"></div>
                    <div class="mb-2"><label class="form-label small fw-semibold">Program Studi</label><input type="text" name="prodi" class="form-control form-control-sm" value="{{ old('prodi') }}"></div>
                    <div class="row g-2">
                        <div class="col-6 mb-2"><label class="form-label small fw-semibold">Tipe <span class="text-danger">*</span></label>
                            <select name="tipe_mahasiswa" class="form-select form-select-sm" required>
                                <option value="magang" {{ old('tipe_mahasiswa','magang')==='magang'?'selected':'' }}>Magang</option>
                                <option value="pkl" {{ old('tipe_mahasiswa')==='pkl'?'selected':'' }}>PKL</option>
                            </select>
                        </div>
                        <div class="col-6 mb-2"><label class="form-label small fw-semibold">No. HP</label><input type="text" name="no_hp" class="form-control form-control-sm" value="{{ old('no_hp') }}"></div>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input type="hidden" name="weekend_aktif" value="0">
                        <input class="form-check-input" type="checkbox" name="weekend_aktif" value="1" id="wk" {{ old('weekend_aktif') ? 'checked' : '' }}>
                        <label class="form-check-label small" for="wk">Aktif di akhir pekan (Sabtu/Minggu)</label>
                    </div>
                    <div class="mb-2"><label class="form-label small fw-semibold">Pas Foto <span class="text-muted">(untuk ID card)</span></label><input type="file" name="foto" class="form-control form-control-sm" accept="image/*"></div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Kompetensi</label>
                        <div id="kompetensiWrap">
                            <input type="text" name="kompetensi[]" class="form-control form-control-sm mb-1" placeholder="mis. Pemasangan infus">
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100" onclick="tambahKompetensi()"><i class="bi bi-plus"></i> Tambah kompetensi</button>
                    </div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Keterangan</label><textarea name="keterangan" class="form-control form-control-sm" rows="2">{{ old('keterangan') }}</textarea></div>
                    <button type="submit" class="btn-maroon btn-sm w-100"><i class="bi bi-plus-lg me-1"></i> Tambah</button>
                </form>
                <script>
                    function tambahKompetensi() {
                        var w = document.getElementById('kompetensiWrap');
                        var i = document.createElement('input');
                        i.type = 'text'; i.name = 'kompetensi[]';
                        i.className = 'form-control form-control-sm mb-1';
                        i.placeholder = 'Kompetensi lain';
                        w.appendChild(i);
                    }
                </script>
                @endif
            </div>
        </div>

        {{-- Daftar --}}
        <div class="col-lg-8">
            <div class="card-soft">
                <div class="p-3 border-bottom"><h6 class="mb-0 fw-bold"><i class="bi bi-list-ul me-1"></i> Peserta Terdaftar</h6></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th width="5%">No</th><th>Nama</th><th>NIM</th><th>Prodi</th><th>JK</th><th>No. HP</th><th class="text-center">Status</th><th class="text-center">Aksi</th></tr></thead>
                        <tbody>
                            @php $stMap = ['pending'=>['b-pending','Menunggu ACC'],'approved'=>['b-approved','Jadi Mahasiswa'],'rejected'=>['b-rejected','Ditolak']]; @endphp
                            @forelse($booking->pesertas as $i => $p)
                            @php $stt = $p->status ?? 'pending'; $sm = $stMap[$stt] ?? ['b-pending', $stt]; @endphp
                            <tr>
                                <td class="text-muted">{{ $i+1 }}</td>
                                <td class="fw-semibold">{{ $p->nama }}</td>
                                <td>{{ $p->nim ?: '-' }}</td>
                                <td>{{ $p->prodi ?: '-' }}</td>
                                <td>{{ $p->jenis_kelamin ?: '-' }}</td>
                                <td>{{ $p->no_hp ?: '-' }}</td>
                                <td class="text-center"><span class="pill {{ $sm[0] }}">{{ $sm[1] }}</span>
                                    @if($stt==='rejected' && $p->catatan_admin)<div class="text-muted" style="font-size:.65rem;">{{ $p->catatan_admin }}</div>@endif
                                </td>
                                <td class="text-center">
                                    @if($stt === 'approved')
                                        <span class="text-muted small"><i class="bi bi-lock"></i></span>
                                    @else
                                    <form action="{{ route('instansi.booking.peserta.destroy', [$booking->id, $p->id]) }}" method="POST" onsubmit="return confirm('Hapus peserta ini?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-5"><i class="bi bi-people fs-3 d-block mb-2"></i>Belum ada peserta. Tambahkan lewat form di samping.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
