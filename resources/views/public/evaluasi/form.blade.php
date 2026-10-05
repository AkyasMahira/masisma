<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi & Kepuasan Layanan Diklat - RSUD Simpang Lima Gumul</title>
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">

    {{-- FONTS & ICONS --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        :root { --primary-color: #7c1316; --primary-light: #fcebeb; --primary-hover: #5e0e10; --text-dark: #2c3e50; --text-muted: #64748b; --border-color: #e0e0e0; }
        body { background-color: #f0f2f5; font-family: 'Poppins', sans-serif; color: var(--text-dark); padding-top: 20px; }
        .form-container { max-width: 850px; margin: 0 auto; padding-bottom: 80px; }

        .back-link { color: var(--text-muted); text-decoration: none; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 14px; }
        .back-link:hover { color: var(--primary-color); }

        .header-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); margin-bottom: 25px; border-top: 5px solid var(--primary-color); }
        .header-inner { padding: 30px; }
        .form-title { font-size: 26px; font-weight: 700; margin-bottom: 8px; color: var(--primary-color); }
        .form-desc { color: var(--text-muted); font-size: 14px; margin: 0; }

        .section-title { font-size: 18px; font-weight: 700; color: var(--text-dark); margin-top: 34px; margin-bottom: 18px; border-bottom: 2px solid #e2e8f0; display: flex; align-items: center; padding-bottom: 10px; }
        .section-title i { color: var(--primary-color); margin-right: 10px; }

        .input-card { background: white; border-radius: 16px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); transition: 0.3s; border: 1px solid transparent; }
        .input-card:focus-within { border-color: var(--primary-color); box-shadow: 0 0 0 4px rgba(124, 19, 22, 0.05); }
        .field-label { font-weight: 600; font-size: 13px; color: var(--text-dark); margin-bottom: 8px; display: block; }
        .form-control-ultra { width: 100%; padding: 12px 15px; border: 1px solid #cbd5e0; border-radius: 10px; font-size: 14px; transition: 0.3s; background:#fff; }
        .form-control-ultra:focus { border-color: var(--primary-color); outline: none; box-shadow: 0 0 0 3px rgba(124,19,22,.08); }

        /* Kartu unsur penilaian */
        .unsur-card { background: white; border-radius: 16px; padding: 20px 22px; margin-bottom: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border-left: 5px solid var(--primary-color); }
        .unsur-q { font-weight: 600; font-size: 14.5px; margin-bottom: 14px; color: var(--text-dark); }
        .unsur-q .kode { color: var(--primary-color); font-weight: 700; margin-right: 4px; }
        .rating { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
        .rating input { position: absolute; opacity: 0; }
        .rating label { cursor: pointer; text-align: center; border: 1px solid #cbd5e0; border-radius: 12px; padding: 12px 6px; font-size: 12.5px; font-weight: 600; color: var(--text-muted); transition: 0.2s; line-height: 1.25; background:#fff; }
        .rating label .n { display: block; font-size: 20px; font-weight: 700; margin-bottom: 2px; }
        .rating label:hover { border-color: var(--primary-color); background: var(--primary-light); }
        .rating input:checked + label { background: var(--primary-color); color: #fff; border-color: var(--primary-color); box-shadow: 0 4px 12px rgba(124,19,22,.25); }

        .btn-submit-ultra { background: linear-gradient(135deg, #7c1316 0%, #a31d21 100%); color: white; border: none; border-radius: 12px; padding: 16px 40px; font-weight: 600; font-size: 16px; width: 100%; box-shadow: 0 4px 15px rgba(124, 19, 22, 0.3); cursor: pointer; letter-spacing: .5px; transition: 0.3s; margin-top: 10px; }
        .btn-submit-ultra:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(124, 19, 22, 0.4); }

        .alert-ultra { background:#fef2f2; border:1px solid #fee2e2; border-left:5px solid var(--primary-color); border-radius:12px; padding:16px 20px; margin-bottom:20px; }
        @media (max-width: 520px){ .rating { grid-template-columns: repeat(2, 1fr); } .header-inner { padding: 22px; } }
    </style>
</head>
<body>
    <div class="form-container">
        <a href="{{ route('landing') }}" class="back-link"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>

        <div class="header-card animate__animated animate__fadeInDown">
            <div class="header-inner">
                <h1 class="form-title"><i class="fas fa-clipboard-check mr-2"></i>Evaluasi Layanan Diklat</h1>
                <p class="form-desc">Sampaikan penilaian, kritik, dan saran Anda terhadap penyelenggaraan diklat RSUD Simpang Lima Gumul. Masukan Anda membantu kami meningkatkan mutu pelayanan.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert-ultra">
                <strong><i class="fas fa-exclamation-triangle mr-1"></i> Mohon periksa kembali:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        @if(!empty($gateNote))
            <div style="background:#fffbeb; border:1px solid #fde68a; border-left:5px solid #f59e0b; border-radius:14px; padding:14px 18px; margin-bottom:18px; color:#92400e; font-size:.9rem;">
                <strong><i class="fas fa-lock me-1"></i> Wajib diisi:</strong> {{ $gateNote }}
            </div>
        @endif

        <form action="{{ $actionUrl ?? route('evaluasi.public.store') }}" method="POST">
            @csrf

            {{-- Identitas responden --}}
            <div class="section-title"><i class="fas fa-user"></i> Data Responden <span class="ml-2" style="font-size:12px;font-weight:500;color:var(--text-muted);">(opsional / boleh anonim)</span></div>
            <div class="input-card">
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label class="field-label">Nama</label>
                        <input type="text" name="nama" class="form-control-ultra" value="{{ old('nama', $prefill['nama'] ?? '') }}" placeholder="Nama Anda">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="field-label">Instansi / Asal</label>
                        <input type="text" name="instansi" class="form-control-ultra" value="{{ old('instansi', $prefill['instansi'] ?? '') }}" placeholder="Instansi / unit kerja">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="field-label">No. HP / Email</label>
                        <input type="text" name="kontak" class="form-control-ultra" value="{{ old('kontak') }}" placeholder="Kontak (opsional)">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="field-label">Nama Kegiatan / Diklat</label>
                        <input type="text" name="nama_kegiatan" class="form-control-ultra" value="{{ old('nama_kegiatan', $prefill['nama_kegiatan'] ?? '') }}" placeholder="Diklat yang diikuti">
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="field-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control-ultra">
                            <option value="">-</option>
                            <option value="L" {{ old('jenis_kelamin')==='L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin')==='P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="field-label">Pendidikan</label>
                        <input type="text" name="pendidikan" class="form-control-ultra" value="{{ old('pendidikan') }}" placeholder="mis. S1, D3">
                    </div>
                    <div class="col-md-4">
                        <label class="field-label">Umur</label>
                        <input type="text" name="umur" class="form-control-ultra" value="{{ old('umur') }}" placeholder="mis. 27">
                    </div>
                </div>
            </div>

            {{-- Penilaian unsur --}}
            <div class="section-title"><i class="fas fa-star-half-alt"></i> Penilaian Unsur Pelayanan</div>
            <p style="font-size:13px;color:var(--text-muted);margin-top:-8px;margin-bottom:18px;">Skala: 1 = Tidak Baik, 2 = Kurang Baik, 3 = Baik, 4 = Sangat Baik.</p>

            @php $labels = [1=>'Tidak Baik', 2=>'Kurang Baik', 3=>'Baik', 4=>'Sangat Baik']; @endphp
            @foreach ($unsur as $u)
                <div class="unsur-card">
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
                        <textarea name="jawaban[{{ $u->id }}]" class="form-control-ultra" rows="2" placeholder="Tuliskan jawaban Anda...">{{ old('jawaban.'.$u->id) }}</textarea>
                    @endif
                </div>
            @endforeach

            {{-- Kritik & saran --}}
            <div class="section-title"><i class="fas fa-comment-dots"></i> Kritik & Saran</div>
            <div class="input-card">
                <label class="field-label">Kritik</label>
                <textarea name="kritik" class="form-control-ultra mb-3" rows="3" placeholder="Hal yang perlu diperbaiki...">{{ old('kritik') }}</textarea>
                <label class="field-label">Saran</label>
                <textarea name="saran" class="form-control-ultra" rows="3" placeholder="Saran untuk peningkatan layanan...">{{ old('saran') }}</textarea>
            </div>

            <button type="submit" class="btn-submit-ultra"><i class="fas fa-paper-plane mr-2"></i> Kirim Evaluasi</button>
        </form>
    </div>
</body>
</html>
