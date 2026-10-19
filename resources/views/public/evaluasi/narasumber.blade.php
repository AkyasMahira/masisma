<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Narasumber - RSUD Simpang Lima Gumul</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        :root { --primary:#7c1316; --primary-light:#fcebeb; --text-dark:#2c3e50; --text-muted:#64748b; }
        body { background:#f0f2f5; font-family:'Poppins',sans-serif; color:var(--text-dark); padding:20px 12px 90px; }
        .wrap { max-width:900px; margin:0 auto; }
        .head { background:#fff; border-radius:16px; border-top:5px solid var(--primary); box-shadow:0 10px 30px rgba(0,0,0,.08); padding:24px 26px; margin-bottom:18px; }
        .head h1 { font-size:22px; font-weight:700; color:var(--primary); margin:0 0 4px; }
        .head .keg { font-weight:600; font-size:14.5px; margin-bottom:12px; }
        .petunjuk { background:#f8fafc; border-radius:12px; padding:12px 16px; font-size:13px; color:#475569; }
        .petunjuk ol { margin:6px 0 0; padding-left:18px; }
        .card-x { background:#fff; border-radius:16px; padding:18px 20px; margin-bottom:14px; box-shadow:0 4px 6px rgba(0,0,0,.02); border:1px solid transparent; }
        .card-x.kurang { border-color:#fca5a5; box-shadow:0 0 0 3px rgba(220,38,38,.08); }
        .lbl { font-weight:600; font-size:13px; margin-bottom:6px; display:block; }
        .req { color:#dc2626; }
        .inp { width:100%; padding:11px 14px; border:1px solid #cbd5e0; border-radius:10px; font-size:14px; background:#fff; }
        .inp:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(124,19,22,.08); }
        .aspek-h { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
        .aspek-n { width:30px; height:30px; border-radius:50%; background:var(--primary); color:#fff; font-weight:700; font-size:13px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .aspek-h b { font-size:15px; }
        .grid { width:100%; border-collapse:separate; border-spacing:0 6px; }
        .grid th { font-size:11px; font-weight:700; color:var(--text-muted); text-align:center; padding:0 4px 2px; line-height:1.2; }
        .grid th small { display:block; font-weight:500; color:#94a3b8; }
        .grid td { background:#f8fafc; padding:8px 6px; text-align:center; vertical-align:middle; }
        .grid td:first-child { text-align:left; font-size:13px; font-weight:600; border-radius:10px 0 0 10px; padding-left:12px; min-width:160px; }
        .grid td:last-child { border-radius:0 10px 10px 0; }
        .grid tr.done td { background:#f0fdf4; }
        .opt input { position:absolute; opacity:0; }
        .opt label { display:inline-flex; width:30px; height:30px; border-radius:50%; border:2px solid #cbd5e0; align-items:center; justify-content:center; cursor:pointer; margin:0; background:#fff; transition:.12s; }
        .opt input:checked + label { background:var(--primary); border-color:var(--primary); box-shadow:0 2px 8px rgba(124,19,22,.3); }
        .opt input:checked + label::after { content:''; width:10px; height:10px; border-radius:50%; background:#fff; }
        .opt label:hover { border-color:var(--primary); }
        .bar-prog { position:fixed; left:0; right:0; bottom:0; background:#fff; box-shadow:0 -6px 20px rgba(0,0,0,.08); padding:10px 14px; z-index:10; }
        .bar-prog .in { max-width:900px; margin:0 auto; display:flex; align-items:center; gap:12px; }
        .prog { flex:1; height:8px; background:#eef2f7; border-radius:8px; overflow:hidden; }
        .prog span { display:block; height:100%; background:#16a34a; width:0; transition:width .2s; }
        .btn-kirim { background:linear-gradient(135deg,#7c1316,#a31d21); color:#fff; border:none; border-radius:12px; padding:11px 22px; font-weight:600; white-space:nowrap; }
        .alert-x { background:#fef2f2; border:1px solid #fee2e2; border-left:5px solid var(--primary); border-radius:12px; padding:14px 18px; margin-bottom:16px; font-size:14px; }
        .empty { background:#fff; border-radius:16px; padding:30px; text-align:center; color:var(--text-muted); }
        @media (max-width:600px) {
            .grid td:first-child { min-width:110px; font-size:12px; }
            .opt label { width:26px; height:26px; }
            .grid th { font-size:10px; }
            .grid th small { display:none; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="head">
        <h1><i class="fas fa-chalkboard-teacher mr-2"></i>Evaluasi Narasumber</h1>
        <div class="keg">{{ $kegiatan->nama_kegiatan }}</div>
        <div class="petunjuk">
            <b>Petunjuk pengerjaan</b>
            <ol>
                <li>Bacalah setiap bagian formulir dengan saksama sebelum mengisi.</li>
                <li>Isilah formulir sesuai dengan kondisi dan pengalaman yang sebenarnya.</li>
                <li>Tidak terdapat jawaban benar atau salah. Seluruh isian diharapkan objektif dan jujur.</li>
                <li>Jawaban yang diberikan tidak akan memengaruhi nilai, penilaian kinerja, maupun aspek lainnya.</li>
            </ol>
        </div>
    </div>

    @if($narasumber->isEmpty() || $aspek->isEmpty())
        <div class="empty"><i class="fas fa-user-slash d-block mb-2" style="font-size:24px;"></i>Belum ada narasumber yang terdaftar untuk pelatihan ini.</div>
    @else
    @if ($errors->any())
        <div class="alert-x"><b><i class="fas fa-exclamation-triangle mr-1"></i> Mohon periksa kembali:</b>
            <ul class="mb-0 mt-1">@foreach(collect($errors->all())->unique() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ $actionUrl }}" method="POST" id="fNara">
        @csrf
        <div class="card-x">
            <div class="form-row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label class="lbl">Tanggal Pengisian <span class="req">*</span></label>
                    <input type="date" name="tanggal_pengisian" class="inp" value="{{ old('tanggal_pengisian', now()->toDateString()) }}" required>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <label class="lbl">Profesi <span class="req">*</span></label>
                    <input type="text" name="profesi" class="inp" list="profesiList" value="{{ old('profesi') }}" placeholder="mis. Perawat" required>
                    <datalist id="profesiList">
                        @foreach(['Perawat', 'Bidan', 'Dokter', 'Dokter Spesialis', 'Apoteker', 'Analis Kesehatan', 'Radiografer', 'Fisioterapis', 'Nutrisionis', 'Tenaga Administrasi'] as $p)<option value="{{ $p }}">@endforeach
                    </datalist>
                </div>
                <div class="col-md-4">
                    <label class="lbl">Nama <span class="text-muted" style="font-weight:400;">(opsional)</span></label>
                    <input type="text" name="nama" class="inp" value="{{ old('nama') }}" placeholder="Boleh dikosongkan">
                </div>
            </div>
        </div>

        @php $skala = \App\Models\EvaluasiNarasumber::SKALA; @endphp
        @foreach($aspek as $i => $a)
            <div class="card-x aspek" data-aspek="{{ $a->id }}">
                <div class="aspek-h"><span class="aspek-n">{{ $i + 1 }}</span><b>{{ $a->pertanyaan }}</b></div>
                <div style="overflow-x:auto;">
                    <table class="grid">
                        <thead><tr><th></th>@foreach($skala as $v => $s)<th>{{ $s[0] }}<small>[{{ $s[1] }}]</small></th>@endforeach</tr></thead>
                        <tbody>
                        @foreach($narasumber as $n)
                            <tr>
                                <td>{{ $n->nama_fasilitator }}</td>
                                @foreach($skala as $v => $s)
                                    <td class="opt">
                                        <input type="radio" id="n{{ $a->id }}_{{ $n->id }}_{{ $v }}" name="nilai[{{ $a->id }}][{{ $n->id }}]" value="{{ $v }}" {{ (string) old("nilai.{$a->id}.{$n->id}") === (string) $v ? 'checked' : '' }}>
                                        <label for="n{{ $a->id }}_{{ $n->id }}_{{ $v }}" title="{{ $s[0] }} [{{ $s[1] }}]"></label>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        <div class="card-x">
            <label class="lbl">Kritik & saran untuk narasumber <span class="text-muted" style="font-weight:400;">(opsional)</span></label>
            <textarea name="saran" class="inp" rows="3" placeholder="Tuliskan masukan Anda...">{{ old('saran') }}</textarea>
        </div>

        <div class="bar-prog">
            <div class="in">
                <div class="small text-muted text-nowrap" id="progTxt">0 / 0</div>
                <div class="prog"><span id="progBar"></span></div>
                <button type="submit" class="btn-kirim"><i class="fas fa-paper-plane mr-1"></i> Kirim</button>
            </div>
        </div>
    </form>
    @endif
</div>

<script>
    (function () {
        var f = document.getElementById('fNara'); if (!f) return;
        var rows = f.querySelectorAll('.grid tbody tr');
        function update() {
            var done = 0;
            rows.forEach(function (r) { var ok = !!r.querySelector('input:checked'); r.classList.toggle('done', ok); if (ok) done++; });
            document.getElementById('progTxt').innerText = done + ' / ' + rows.length + ' terisi';
            document.getElementById('progBar').style.width = (rows.length ? done / rows.length * 100 : 0) + '%';
            return done === rows.length;
        }
        f.addEventListener('change', function () {
            update();
            document.querySelectorAll('.aspek.kurang').forEach(function (c) { if (!c.querySelector('tbody tr:not(.done)')) c.classList.remove('kurang'); });
        });
        f.addEventListener('submit', function (e) {
            if (update()) return;
            e.preventDefault();
            var first = null;
            document.querySelectorAll('.aspek').forEach(function (c) {
                var kurang = !!c.querySelector('tbody tr:not(.done)');
                c.classList.toggle('kurang', kurang);
                if (kurang && !first) first = c;
            });
            if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
        update();
    })();
</script>
</body>
</html>
