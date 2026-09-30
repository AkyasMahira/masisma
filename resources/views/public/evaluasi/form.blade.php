<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi & Kepuasan Layanan Diklat - RSUD Simpang Lima Gumul</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #7c1316;
            --primary-dark: #5f0f12;
            --primary-subtle: #fcf0f1;
            --bg-body: #f0f4f8;
            --text-main: #1a202c;
            --text-muted: #64748b;
        }
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: var(--bg-body); color: var(--text-main); }
        .wrap { max-width: 820px; margin: 0 auto; padding: 24px 16px 64px; }
        .hero {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff; border-radius: 20px; padding: 32px 28px; margin-bottom: 24px;
            box-shadow: 0 14px 40px rgba(124,19,22,.25);
        }
        .hero h1 { font-weight: 800; font-size: 1.6rem; margin: 0 0 6px; }
        .hero p { margin: 0; opacity: .9; font-size: .95rem; }
        .card-soft {
            background: #fff; border-radius: 16px; padding: 24px; margin-bottom: 20px;
            box-shadow: 0 4px 24px rgba(0,0,0,.05); border: 1px solid #eef2f7;
        }
        .card-soft h2 { font-size: 1.05rem; font-weight: 700; color: var(--primary); margin: 0 0 4px; }
        .card-soft .sub { color: var(--text-muted); font-size: .85rem; margin-bottom: 18px; }
        .form-label { font-weight: 600; font-size: .85rem; color: var(--text-main); }
        .form-control, .form-select { border-radius: 10px; padding: .6rem .8rem; border: 1px solid #e2e8f0; }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 .2rem rgba(124,19,22,.12); }
        .unsur { padding: 16px 0; border-bottom: 1px dashed #e2e8f0; }
        .unsur:last-child { border-bottom: none; }
        .unsur-q { font-weight: 600; font-size: .92rem; margin-bottom: 12px; }
        .unsur-q .kode { color: var(--primary); font-weight: 700; margin-right: 6px; }
        .rating { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
        .rating input { display: none; }
        .rating label {
            cursor: pointer; text-align: center; border: 1.5px solid #e2e8f0; border-radius: 10px;
            padding: 10px 6px; font-size: .78rem; font-weight: 600; color: var(--text-muted);
            transition: .15s; line-height: 1.2;
        }
        .rating label .n { display: block; font-size: 1.1rem; font-weight: 800; }
        .rating input:checked + label { background: var(--primary); color: #fff; border-color: var(--primary); }
        .rating label:hover { border-color: var(--primary); }
        .btn-primary-soft {
            background: var(--primary); color: #fff; border: none; border-radius: 12px;
            padding: 12px 20px; font-weight: 700; width: 100%;
        }
        .btn-primary-soft:hover { background: var(--primary-dark); color: #fff; }
        .back-link { color: var(--text-muted); text-decoration: none; font-weight: 600; font-size: .85rem; }
        .back-link:hover { color: var(--primary); }
        @media (max-width: 520px){ .rating { grid-template-columns: repeat(2, 1fr); } }
    </style>
</head>
<body>
    <div class="wrap">
        <a href="{{ route('landing') }}" class="back-link"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>

        <div class="hero mt-3">
            <h1><i class="bi bi-clipboard2-check me-2"></i>Evaluasi Layanan Diklat</h1>
            <p>Sampaikan penilaian, kritik, dan saran Anda terhadap penyelenggaraan diklat RSUD Simpang Lima Gumul. Masukan Anda membantu kami meningkatkan mutu pelayanan.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger card-soft" style="border-left:5px solid var(--primary);">
                <strong><i class="bi bi-exclamation-triangle me-1"></i> Mohon periksa kembali:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('evaluasi.public.store') }}" method="POST">
            @csrf

            {{-- Identitas responden --}}
            <div class="card-soft">
                <h2>Data Responden</h2>
                <div class="sub">Opsional — boleh dikosongkan jika ingin anonim.</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" placeholder="Nama Anda">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Instansi / Asal</label>
                        <input type="text" name="instansi" class="form-control" value="{{ old('instansi') }}" placeholder="Instansi / unit kerja">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. HP / Email</label>
                        <input type="text" name="kontak" class="form-control" value="{{ old('kontak') }}" placeholder="Kontak (opsional)">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Kegiatan / Diklat</label>
                        <input type="text" name="nama_kegiatan" class="form-control" value="{{ old('nama_kegiatan') }}" placeholder="Diklat yang diikuti">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select">
                            <option value="">-</option>
                            <option value="L" {{ old('jenis_kelamin')==='L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin')==='P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pendidikan</label>
                        <input type="text" name="pendidikan" class="form-control" value="{{ old('pendidikan') }}" placeholder="mis. S1, D3">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Umur</label>
                        <input type="text" name="umur" class="form-control" value="{{ old('umur') }}" placeholder="mis. 27">
                    </div>
                </div>
            </div>

            {{-- Penilaian unsur --}}
            <div class="card-soft">
                <h2>Penilaian Unsur Pelayanan</h2>
                <div class="sub">Skala: 1 = Tidak Baik, 2 = Kurang Baik, 3 = Baik, 4 = Sangat Baik.</div>

                @php $labels = [1=>'Tidak Baik', 2=>'Kurang Baik', 3=>'Baik', 4=>'Sangat Baik']; @endphp
                @foreach ($unsur as $u)
                    <div class="unsur">
                        <div class="unsur-q">
                            @if($u->kode)<span class="kode">{{ $u->kode }}.</span>@endif {{ $u->pertanyaan }}
                        </div>
                        @if ($u->tipe === 'rating')
                            <div class="rating">
                                @foreach ($labels as $n => $txt)
                                    <div>
                                        <input type="radio" id="u{{ $u->id }}_{{ $n }}" name="jawaban[{{ $u->id }}]" value="{{ $n }}" {{ (string)old('jawaban.'.$u->id)===(string)$n ? 'checked' : '' }}>
                                        <label for="u{{ $u->id }}_{{ $n }}"><span class="n">{{ $n }}</span>{{ $txt }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <textarea name="jawaban[{{ $u->id }}]" class="form-control" rows="2" placeholder="Tuliskan jawaban Anda...">{{ old('jawaban.'.$u->id) }}</textarea>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Kritik & saran --}}
            <div class="card-soft">
                <h2>Kritik & Saran</h2>
                <div class="sub">Sampaikan hal yang perlu diperbaiki maupun apresiasi Anda.</div>
                <div class="mb-3">
                    <label class="form-label">Kritik</label>
                    <textarea name="kritik" class="form-control" rows="3" placeholder="Hal yang perlu diperbaiki...">{{ old('kritik') }}</textarea>
                </div>
                <div>
                    <label class="form-label">Saran</label>
                    <textarea name="saran" class="form-control" rows="3" placeholder="Saran untuk peningkatan layanan...">{{ old('saran') }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn-primary-soft"><i class="bi bi-send me-1"></i> Kirim Evaluasi</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
