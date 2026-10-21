@extends('layouts.app')

@section('title', 'Absensi Manual')

@section('content')
<style>
    :root { --maroon:#7c1316; --maroon-light:#a3191d; --maroon-subtle:#fcf0f1; }
    .bk-head { background:linear-gradient(135deg,#7c1316,#5f0f12); color:#fff; border-radius:18px; padding:20px 24px; box-shadow:0 12px 30px rgba(124,19,22,.26); position:relative; overflow:hidden; }
    .bk-head::after { content:''; position:absolute; right:-40px; top:-40px; width:170px; height:170px; background:rgba(255,255,255,.07); border-radius:50%; pointer-events:none; }
    .bk-head h4 { font-weight:800; margin:0 0 3px; font-size:1.3rem; }
    .bk-head p { opacity:.88; margin:0; font-size:.84rem; }
    .card-x { background:#fff; border:1px solid #eef2f7; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,.05); }
    .filter-sel { border-radius:10px; border:1.5px solid #e2e8f0; font-size:.86rem; }
    .filter-sel:focus { border-color:var(--maroon); box-shadow:0 0 0 .2rem rgba(124,19,22,.1); }
    .lbl { font-size:.74rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.3px; margin-bottom:6px; }
    .seg { display:flex; gap:6px; flex-wrap:wrap; }
    .seg input[type=radio] { display:none; }
    .seg label { border:1.5px solid #e2e8f0; border-radius:10px; padding:6px 12px; font-size:.82rem; font-weight:700; color:#475569; cursor:pointer; background:#fff; }
    .seg input:checked + label { border-color:var(--maroon); background:var(--maroon-subtle); color:var(--maroon); }
    .t-x thead th { background:var(--maroon-subtle); color:var(--maroon); font-size:.7rem; text-transform:uppercase; letter-spacing:.3px; border:none; padding:10px; }
    .t-x td { padding:9px 10px; vertical-align:middle; border-color:#f1f3f5; font-size:.86rem; }
    .t-x tr.sel td { background:#fffafa; }
    .pill { font-size:.7rem; font-weight:800; border-radius:20px; padding:2px 9px; white-space:nowrap; }
    .p-ok { background:#dcfce7; color:#15803d; } .p-warn { background:#fef3c7; color:#b45309; } .p-no { background:#f1f5f9; color:#64748b; } .p-libur { background:#e0e7ff; color:#4338ca; }
    .ic-btn { width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid #e5e7eb; background:#fff; color:#64748b; }
    .ic-btn:hover { background:var(--maroon-subtle); color:var(--maroon); border-color:#ecd3d5; }
    .sticky-act { position:sticky; bottom:0; background:#fff; border-top:1px solid #eef2f7; border-radius:0 0 16px 16px; padding:12px 16px; z-index:5; }
</style>

<div class="container-fluid py-3">
    <div class="bk-head mb-3">
        <h4><i class="bi bi-person-check me-2"></i>Absensi Manual</h4>
        <p>Absenkan mahasiswa oleh admin — atur jam masuk & pulang, atau ikuti jam shift. Bisa banyak mahasiswa sekaligus.</p>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if(session('error'))<div class="alert alert-warning alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    {{-- FILTER --}}
    <form method="GET" class="card-x p-3 mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3"><div class="lbl">Tanggal</div><input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control form-control-sm filter-sel" onchange="this.form.submit()"></div>
            <div class="col-md-4"><div class="lbl">Ruangan</div>
                <select name="ruangan" class="form-select form-select-sm filter-sel" onchange="this.form.submit()">
                    <option value="">Semua ruangan</option>
                    @foreach($ruangans as $id => $nm)<option value="{{ $id }}" {{ (string) $fRuangan === (string) $id ? 'selected' : '' }}>{{ $nm }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3"><div class="lbl">Cari nama</div><input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm filter-sel" placeholder="Nama mahasiswa"></div>
            <div class="col-md-2 d-grid"><button class="btn btn-sm text-white fw-semibold" style="background:var(--maroon);"><i class="bi bi-search me-1"></i>Tampilkan</button></div>
        </div>
    </form>

    <form method="POST" action="{{ route('admin.absen_manual.store') }}" class="card-x" id="fAbsen">
        @csrf
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
        <input type="hidden" name="ruangan" value="{{ $fRuangan }}">
        <input type="hidden" name="q" value="{{ $q }}">

        {{-- PENGATURAN JAM --}}
        <div class="p-3 border-bottom">
            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="lbl">Jam masuk</div>
                    <div class="seg mb-2">
                        <input type="radio" name="mode_masuk" id="mm1" value="shift" {{ old('mode_masuk', 'shift') === 'shift' ? 'checked' : '' }}><label for="mm1">Ikuti jam mulai shift</label>
                        <input type="radio" name="mode_masuk" id="mm2" value="jam" {{ old('mode_masuk') === 'jam' ? 'checked' : '' }}><label for="mm2">Atur jam</label>
                    </div>
                    <input type="time" name="jam_masuk" value="{{ old('jam_masuk', '07:00') }}" class="form-control form-control-sm filter-sel" style="max-width:150px;" id="jamMasuk">
                </div>
                <div class="col-lg-5">
                    <div class="lbl">Jam pulang</div>
                    <div class="seg mb-2">
                        <input type="radio" name="mode_pulang" id="mp1" value="kosong" {{ old('mode_pulang', 'kosong') === 'kosong' ? 'checked' : '' }}><label for="mp1">Mahasiswa absen sendiri</label>
                        <input type="radio" name="mode_pulang" id="mp2" value="shift" {{ old('mode_pulang') === 'shift' ? 'checked' : '' }}><label for="mp2">Ikuti jam selesai shift</label>
                        <input type="radio" name="mode_pulang" id="mp3" value="jam" {{ old('mode_pulang') === 'jam' ? 'checked' : '' }}><label for="mp3">Atur jam</label>
                    </div>
                    <input type="time" name="jam_pulang" value="{{ old('jam_pulang', '14:00') }}" class="form-control form-control-sm filter-sel" style="max-width:150px;" id="jamPulang">
                    <div class="small text-muted mt-1">Jam pulang lebih kecil dari jam masuk = pulang keesokan hari (shift malam).</div>
                </div>
                <div class="col-lg-3">
                    <div class="lbl">Alasan (opsional)</div>
                    <input type="text" name="alasan" value="{{ old('alasan') }}" class="form-control form-control-sm filter-sel" placeholder="mis. jadwal shift belum diisi" maxlength="200">
                    <div class="small text-muted mt-1">Tercatat di keterangan absen.</div>
                </div>
            </div>
        </div>

        {{-- DAFTAR MAHASISWA --}}
        <div class="table-responsive">
            <table class="table t-x mb-0">
                <thead><tr>
                    <th style="width:36px;"><input type="checkbox" class="form-check-input" id="cekSemua" title="Centang semua"></th>
                    <th>Mahasiswa</th><th>Ruangan</th><th>Shift {{ \Carbon\Carbon::parse($tanggal)->isoFormat('D MMM') }}</th><th>Masuk</th><th>Pulang</th><th class="text-end"></th>
                </tr></thead>
                <tbody>
                @forelse($rows as $r)
                    @php $m = $r['m']; $libur = $r['shift'] && strtolower($r['shift']) === 'libur'; @endphp
                    <tr>
                        <td><input type="checkbox" class="form-check-input cek" name="mahasiswa_ids[]" value="{{ $m->id }}"></td>
                        <td><div class="fw-semibold">{{ $m->nm_mahasiswa }}</div><div class="small text-muted">{{ optional($m->mou)->nama_instansi ?: '-' }}</div></td>
                        <td class="small">{{ $r['ruangan'] ?: '-' }}</td>
                        <td>
                            @if($libur)<span class="pill p-libur">Libur</span>
                            @elseif($r['shift'])<span class="fw-semibold small">{{ $r['shift'] }}</span> <span class="small text-muted">{{ $r['jam_shift'] }}</span>
                            @else<span class="pill p-warn">Jadwal kosong</span>@endif
                        </td>
                        <td>@if($r['masuk'])<span class="pill p-ok">{{ $r['masuk']->format('H:i') }}</span>@else<span class="pill p-no">belum</span>@endif</td>
                        <td>
                            @if($r['pulang'])<span class="pill p-ok">{{ $r['pulang']->format('H:i') }}{{ $r['pulang']->toDateString() !== $tanggal ? ' +1' : '' }}</span>
                            @elseif($r['masuk'])<span class="pill p-warn">belum pulang</span>
                            @else<span class="pill p-no">-</span>@endif
                            @if($r['ket'] && strpos($r['ket'], 'diinput admin') !== false)<div class="small text-muted" style="font-size:.68rem;">{{ \Illuminate\Support\Str::limit($r['ket'], 50) }}</div>@endif
                        </td>
                        <td class="text-end text-nowrap">
                            @if($r['masuk'])
                                <button type="button" class="ic-btn" title="Hapus absen tanggal ini" onclick="hapusAbsen({{ $m->id }}, @json($m->nm_mahasiswa))"><i class="bi bi-trash"></i></button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-5"><i class="bi bi-people d-block fs-3 mb-1"></i>Tidak ada mahasiswa aktif yang dijadwalkan di ruangan pada tanggal ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="sticky-act d-flex flex-wrap gap-2 align-items-center">
            <span class="small text-muted" id="jmlCek">0 mahasiswa dicentang</span>
            <button type="submit" class="btn text-white fw-semibold ms-auto" style="background:var(--maroon);" id="btnAbsen" disabled><i class="bi bi-check2-circle me-1"></i>Absenkan yang dicentang — {{ \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMM YYYY') }}</button>
        </div>
    </form>
</div>

<form method="POST" id="fHapus" style="display:none;">@csrf @method('DELETE')<input type="hidden" name="tanggal" value="{{ $tanggal }}"></form>

<script>
    (function () {
        var cek = document.querySelectorAll('.cek'), semua = document.getElementById('cekSemua'), btn = document.getElementById('btnAbsen');
        function upd() {
            var n = document.querySelectorAll('.cek:checked').length;
            document.getElementById('jmlCek').innerText = n + ' mahasiswa dicentang';
            btn.disabled = n === 0;
            cek.forEach(function (c) { c.closest('tr').classList.toggle('sel', c.checked); });
        }
        cek.forEach(function (c) { c.addEventListener('change', upd); });
        if (semua) semua.addEventListener('change', function () { cek.forEach(function (c) { c.checked = semua.checked; }); upd(); });
        function sinkron() {
            document.getElementById('jamMasuk').disabled = !document.getElementById('mm2').checked;
            document.getElementById('jamPulang').disabled = !document.getElementById('mp3').checked;
        }
        document.querySelectorAll('[name=mode_masuk],[name=mode_pulang]').forEach(function (r) { r.addEventListener('change', sinkron); });
        sinkron(); upd();
        document.getElementById('fAbsen').addEventListener('submit', function (e) {
            var n = document.querySelectorAll('.cek:checked').length;
            if (!confirm('Absenkan ' + n + ' mahasiswa? Absen yang sudah ada di tanggal ini akan diperbarui jamnya.')) e.preventDefault();
        });
    })();
    function hapusAbsen(id, nama) {
        if (!confirm('Hapus absen masuk & pulang ' + nama + ' pada tanggal ini?')) return;
        var f = document.getElementById('fHapus');
        f.action = @json(route('admin.absen_manual.destroy', '__ID__')).replace('__ID__', id);
        f.submit();
    }
</script>
@endsection
