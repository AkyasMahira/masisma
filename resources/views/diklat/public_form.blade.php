@extends('layouts.public')

@section('content')
{{-- ASSETS --}}
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    /* --- CSS MODERN STYLE --- */
    :root { --primary-color: #7c1316; --primary-light: #fcebeb; --text-dark: #2c3e50; --border-color: #e0e0e0; }
    body { background-color: #f0f2f5; font-family: 'Poppins', sans-serif; color: var(--text-dark); padding-top: 20px; }
    .form-container { max-width: 850px; margin: 0 auto; padding-bottom: 80px; }
    
    /* Header */
    .header-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); margin-bottom: 25px; border-top: 5px solid var(--primary-color); }
    .header-content { padding: 30px; }
    .form-title { font-size: 28px; font-weight: 700; margin-bottom: 10px; color: var(--primary-color); }
    
    /* Section & Cards */
    .section-title { font-size: 18px; font-weight: 700; color: var(--text-dark); margin-top: 40px; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; display: flex; align-items: center; padding-bottom: 10px; }
    .section-title i { color: var(--primary-color); margin-right: 10px; }
    
    .input-card { background: white; border-radius: 16px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); transition: 0.3s; border: 1px solid transparent; }
    .input-card:focus-within { border-color: var(--primary-color); box-shadow: 0 0 0 4px rgba(124, 19, 22, 0.05); }
    
    .input-label { font-size: 14px; font-weight: 600; margin-bottom: 8px; display: block; color: #4a5568; }
    .req-star { color: #e53e3e; margin-left: 3px; }
    .form-control-ultra { width: 100%; padding: 12px 15px; border: 1px solid #cbd5e0; border-radius: 10px; font-size: 14px; transition: 0.3s; }
    .form-control-ultra:focus { border-color: var(--primary-color); outline: none; }

    /* Repeater Wrapper */
    .participant-wrapper { position: relative; border: 1px solid #e2e8f0; background: #fff; border-radius: 16px; padding: 25px; margin-bottom: 30px; border-left: 5px solid var(--primary-color); box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
    .participant-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px dashed #e2e8f0; }
    .p-title { font-weight: 700; font-size: 16px; color: var(--text-dark); }
    
    .btn-delete-row { color: #ef4444; font-size: 13px; font-weight: 600; cursor: pointer; padding: 6px 12px; background: #fef2f2; border-radius: 8px; border: 1px solid #fee2e2; transition: 0.2s; }
    .btn-delete-row:hover { background: #ef4444; color: white; border-color: #ef4444; }

    .btn-add-row { width: 100%; padding: 15px; background: white; border: 2px dashed var(--primary-color); color: var(--primary-color); font-weight: 600; border-radius: 12px; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; }
    .btn-add-row:hover { background: var(--primary-light); }

    /* Custom Checkbox/Radio */
    .checkbox-group { display: flex; flex-direction: column; gap: 10px; }
    .checkbox-item { position: relative; }
    .checkbox-item input { position: absolute; opacity: 0; cursor: pointer; }
    .checkbox-label { display: flex; align-items: center; padding: 12px 15px; border: 1px solid #cbd5e0; border-radius: 10px; cursor: pointer; background: white; font-size: 14px; transition: 0.2s; }
    .checkbox-label:hover { background-color: #f8fafc; }
    .checkbox-item input:checked + .checkbox-label { border-color: var(--primary-color); background-color: var(--primary-light); color: var(--primary-color); font-weight: 600; }
    .checkbox-icon { width: 20px; height: 20px; border: 2px solid #cbd5e0; border-radius: 4px; margin-right: 12px; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
    .checkbox-item input:checked + .checkbox-label .checkbox-icon { background-color: var(--primary-color); border-color: var(--primary-color); color: white; }

    /* Upload Zone */
    .upload-zone { border: 2px dashed #cbd5e0; border-radius: 12px; padding: 30px; text-align: center; background: #fafafa; cursor: pointer; position: relative; transition: 0.3s; }
    .upload-zone:hover { border-color: var(--primary-color); background: var(--primary-light); }
    .upload-zone input[type="file"] { position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer; }

    .btn-submit-ultra { background: linear-gradient(135deg, #7c1316 0%, #a31d21 100%); color: white; border: none; border-radius: 12px; padding: 16px 40px; font-weight: 600; font-size: 16px; width: 100%; box-shadow: 0 4px 15px rgba(124, 19, 22, 0.3); text-transform: uppercase; cursor: pointer; letter-spacing: 1px; transition: 0.3s; }
    .btn-submit-ultra:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(124, 19, 22, 0.4); }
</style>

<div class="form-container">

    {{-- HEADER --}}
    <div class="header-card animate__animated animate__fadeInDown">
        @if($form->banner_path)
            <img src="{{ asset('storage/' . $form->banner_path) }}" style="width:100%; height:100%; object-fit:cover;">
        @endif
        <div class="header-content">
            <h1 class="form-title">{{ $form->judul }}</h1>
            <!--<div class="text-muted mb-3"><i class="far fa-calendar-alt mr-2"></i> {{ \Carbon\Carbon::parse($form->tanggal_pelaksanaan)->isoFormat('D MMMM Y') }}</div>-->
            <div style="font-size: 14px; line-height: 1.6;">{!! nl2br(e($form->keterangan)) !!}</div>
        </div>
    </div>

    {{-- ERROR ALERT --}}
    @if ($errors->any())
    <div class="alert alert-danger animate__animated animate__shakeX border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #fef2f2; border: 1px solid #fee2e2; color: #991b1b;">
        <div class="d-flex align-items-center mb-2">
            <i class="fas fa-exclamation-triangle mr-2" style="font-size: 1.2rem;"></i>
            <h6 class="font-weight-bold mb-0">Mohon periksa kembali inputan Anda:</h6>
        </div>
        <ul class="mb-0 pl-4 small" style="line-height: 1.6;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('diklat.public.register', $form->public_link) }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- BAGIAN A: DATA INSTANSI / GLOBAL --}}
        <div class="animate__animated animate__fadeInUp">
            <div class="section-title"><i class="fas fa-building"></i> Data Instansi / Penanggung Jawab</div>
            
            <div class="input-card">
                <label class="input-label">Instansi Bekerja/Mandiri <span class="req-star">*</span></label>
                <input type="text" name="instansi" class="form-control-ultra" placeholder="Nama Rumah Sakit / Dinas" value="{{ old('instansi') }}" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="input-card h-100">
                        <label class="input-label">Email Penanggung Jawab <span class="req-star">*</span></label>
                        <input type="email" name="email_kontak" class="form-control-ultra" placeholder="email@instansi.com" value="{{ old('email_kontak') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-card h-100">
                        <label class="input-label">No. HP Kontak Person <span class="req-star">*</span></label>
                        <input type="number" name="no_hp_kontak" class="form-control-ultra" placeholder="08xxxxxxxxxx" value="{{ old('no_hp_kontak') }}" required>
                    </div>
                </div>
            </div>

            <div class="input-card mt-3">
                <label class="input-label">Alamat Instansi <span class="req-star">*</span></label>
                <textarea name="alamat" class="form-control-ultra" rows="2" required>{{ old('alamat') }}</textarea>
            </div>
        </div>

        {{-- BAGIAN B: DATA PESERTA (REPEATER WITH OLD DATA SUPPORT) --}}
        <div class="animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="section-title">
                <i class="fas fa-users"></i> Data Peserta Diklat
                <span class="ml-auto small text-muted font-weight-normal">(Isi data per peserta)</span>
            </div>
            
            <div id="participants-list">
                @php
                    // Ambil data old peserta jika ada (saat error), jika tidak default 1 row kosong
                    $oldPesertas = old('peserta', [0]); 
                @endphp

                @foreach($oldPesertas as $index => $oldData)
                <div class="participant-wrapper" id="row-{{ $index }}">
                    <div class="participant-header">
                        <span class="p-title"><i class="fas fa-user-circle mr-2"></i> Peserta #<span class="p-number">{{ $index + 1 }}</span></span>
                        {{-- Tombol hapus muncul hanya jika bukan peserta pertama --}}
                        <span class="btn-delete-row" onclick="deleteRow('row-{{ $index }}')" style="display: {{ $index == 0 ? 'none' : 'inline-block' }}">
                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                        </span>
                    </div>

                    {{-- Nama & Gelar --}}
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-card p-0 shadow-none border-0 mb-3">
                                <label class="input-label">Nama Lengkap  <span class="req-star">*</span></label>
                                <input type="text" name="peserta[{{ $index }}][nama_lengkap]" class="form-control-ultra" 
                                       value="{{ old("peserta.{$index}.nama_lengkap") }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-card p-0 shadow-none border-0 mb-3">
                                <label class="input-label">Gelar </label>
                                <input type="text" name="peserta[{{ $index }}][gelar]" class="form-control-ultra" 
                                       value="{{ old("peserta.{$index}.gelar") }}">
                            </div>
                        </div>
                    </div>

                    {{-- TTL --}}
                    <div class="row">
                        <div class="col-md-6">
                             <div class="input-card p-0 shadow-none border-0 mb-3">
                                <label class="input-label">Tempat Lahir <span class="req-star">*</span></label>
                                <input type="text" name="peserta[{{ $index }}][tempat_lahir]" class="form-control-ultra" 
                                       value="{{ old("peserta.{$index}.tempat_lahir") }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="input-card p-0 shadow-none border-0 mb-3">
                                <label class="input-label">Tanggal Lahir <span class="req-star">*</span></label>
                                <input type="date" name="peserta[{{ $index }}][tanggal_lahir]" class="form-control-ultra" 
                                       value="{{ old("peserta.{$index}.tanggal_lahir") }}" required>
                            </div>
                        </div>
                    </div>

                    {{-- NIK & Jabatan --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-card p-0 shadow-none border-0 mb-3">
                                <label class="input-label">NIK (KTP) <span class="req-star">*</span></label>
                                <input type="number" name="peserta[{{ $index }}][nik]" class="form-control-ultra" placeholder="16 Digit NIK" 
                                       value="{{ old("peserta.{$index}.nik") }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-card p-0 shadow-none border-0 mb-3">
                                <label class="input-label">Jabatan <span class="req-star">*</span></label>
                                <input type="text" name="peserta[{{ $index }}][jabatan]" class="form-control-ultra" 
                                       value="{{ old("peserta.{$index}.jabatan") }}" required>
                            </div>
                        </div>
                    </div>

                    {{-- Email & WA --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-card p-0 shadow-none border-0 mb-3">
                                <label class="input-label">Email Plataran Sehat <span class="req-star">*</span></label>
                                <input type="email" name="peserta[{{ $index }}][email]" class="form-control-ultra" placeholder="email@kemkes.go.id" 
                                       value="{{ old("peserta.{$index}.email") }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-card p-0 shadow-none border-0 mb-3">
                                <label class="input-label">No. HP (WhatsApp) <span class="req-star">*</span></label>
                                <input type="number" name="peserta[{{ $index }}][no_hp]" class="form-control-ultra" placeholder="08xxxxxxxx" 
                                       value="{{ old("peserta.{$index}.no_hp") }}" required>
                            </div>
                        </div>
                    </div>

                    {{-- Profesi & Pendidikan --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-card p-0 shadow-none border-0 mb-3">
                                <label class="input-label">Profesi <span class="req-star">*</span></label>
                                <input type="text" name="peserta[{{ $index }}][profesi]" class="form-control-ultra" placeholder="Contoh: Perawat" 
                                       value="{{ old("peserta.{$index}.profesi") }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-card p-0 shadow-none border-0 mb-3">
                                <label class="input-label">Pendidikan Terakhir <span class="req-star">*</span></label>
                                <select name="peserta[{{ $index }}][pendidikan_terakhir]" class="form-control-ultra" required>
                                    <option value="">- Pilih -</option>
                                    @foreach(['D3', 'D4', 'S1', 'S2', 'S3', 'Lainnya'] as $p)
                                        <option value="{{ $p }}" {{ old("peserta.{$index}.pendidikan_terakhir") == $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Status Pegawai --}}
                    <div class="input-card p-0 shadow-none border-0 mb-3">
                        <label class="input-label">Status Pegawai <span class="req-star">*</span></label>
                        <div class="row">
                            @foreach(['PNS', 'Non ASN/Swasta'] as $status)
                            <div class="col-6 col-md-3 mb-2">
                                <div class="checkbox-item">
                                    <input type="radio" name="peserta[{{ $index }}][status_pegawai]" value="{{ $status }}" id="p-{{ $index }}-status-{{ $status }}" 
                                           {{ old("peserta.{$index}.status_pegawai") == $status ? 'checked' : '' }} required>
                                    <label class="checkbox-label justify-content-center" for="p-{{ $index }}-status-{{ $status }}">{{ $status }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- NIP & Pangkat --}}
                    <div class="row">
                        <div class="col-md-6">
                             <div class="input-card p-0 shadow-none border-0 mb-3">
                                <label class="input-label">NIP (Opsional)</label>
                                <input type="text" name="peserta[{{ $index }}][nip]" class="form-control-ultra" 
                                       value="{{ old("peserta.{$index}.nip") }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="input-card p-0 shadow-none border-0 mb-3">
                                <label class="input-label">Pangkat/Golongan (Opsional)</label>
                                <input type="text" name="peserta[{{ $index }}][pangkat_golongan]" class="form-control-ultra" 
                                       value="{{ old("peserta.{$index}.pangkat_golongan") }}">
                            </div>
                        </div>
                    </div>

                    {{-- Pilihan Pelatihan --}}
                    <div class="input-card p-0 shadow-none border-0 mb-3">
                        <label class="input-label">Pilih Pelatihan <span class="req-star">*</span></label>
                        <div class="checkbox-group">
                            @foreach($form->opsi_pelatihan as $optIdx => $opsi)
                            <div class="checkbox-item">
                                <input type="checkbox" name="peserta[{{ $index }}][pilihan_pelatihan][]" value="{{ $opsi }}" id="p-{{ $index }}-pel-{{ $optIdx }}"
                                {{ is_array(old("peserta.{$index}.pilihan_pelatihan")) && in_array($opsi, old("peserta.{$index}.pilihan_pelatihan")) ? 'checked' : '' }}>
                                <label class="checkbox-label" for="p-{{ $index }}-pel-{{ $optIdx }}">
                                    <div class="checkbox-icon"><i class="fas " style="font-size: 10px;"></i></div> {{ $opsi }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Pilihan Tempat --}}
                    <div class="input-card p-0 shadow-none border-0 mb-3">
                        <label class="input-label">Pilih Tempat <span class="req-star">*</span></label>
                        <div class="checkbox-group">
                            @foreach($form->opsi_tempat as $optIdx => $opsi)
                            <div class="checkbox-item">
                                <input type="checkbox" name="peserta[{{ $index }}][pilihan_tempat][]" value="{{ $opsi }}" id="p-{{ $index }}-tmp-{{ $optIdx }}"
                                {{ is_array(old("peserta.{$index}.pilihan_tempat")) && in_array($opsi, old("peserta.{$index}.pilihan_tempat")) ? 'checked' : '' }}>
                                <label class="checkbox-label" for="p-{{ $index }}-tmp-{{ $optIdx }}">
                                    <div class="checkbox-icon"><i class="fas " style="font-size: 10px;"></i></div> {{ $opsi }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

              {{-- Ukuran Kaos --}}
                    <div class="input-card p-0 shadow-none border-0 mb-3">
                        <label class="input-label">Ukuran Kaos </label>

                        {{-- PANDUAN UKURAN STYLE BADGE --}}
                        <div style="background-color: #fff5f5; border-radius: 12px; padding: 15px; margin-bottom: 20px; border: 1px solid #f8d7da; border-left: 5px solid #7c1316;">
                            
                            {{-- Judul Keterangan --}}
                            <div style="color: #7c1316; font-weight: 800; font-size: 13px; margin-bottom: 8px; letter-spacing: 0.5px; text-transform: uppercase;">
                                <i class="fas fa-info-circle mr-1"></i> Khusus Pelatihan Berbayar & Blended Learning
                            </div>
                            
                            <p style="font-size: 12px; color: #666; margin-bottom: 10px;">
                                Silakan pilih ukuran berdasarkan <strong>(Lebar x Panjang)</strong> cm:
                            </p>

                            {{-- Badge Items --}}
                            <div class="d-flex flex-wrap" style="gap: 8px;">
                                <span style="background: white; color: #7c1316; border: 1px solid #7c1316; padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; box-shadow: 0 2px 4px rgba(124,19,22,0.1);">
                                    S (44x65)
                                </span>
                                <span style="background: white; color: #7c1316; border: 1px solid #7c1316; padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; box-shadow: 0 2px 4px rgba(124,19,22,0.1);">
                                    M (47x67)
                                </span>
                                <span style="background: white; color: #7c1316; border: 1px solid #7c1316; padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; box-shadow: 0 2px 4px rgba(124,19,22,0.1);">
                                    L (49x69)
                                </span>
                                <span style="background: white; color: #7c1316; border: 1px solid #7c1316; padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; box-shadow: 0 2px 4px rgba(124,19,22,0.1);">
                                    XL (51x71)
                                </span>
                                <span style="background: white; color: #7c1316; border: 1px solid #7c1316; padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; box-shadow: 0 2px 4px rgba(124,19,22,0.1);">
                                    XXL (53x71)
                                </span>
                                    <span style="background: white; color: #7c1316; border: 1px solid #7c1316; padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; box-shadow: 0 2px 4px rgba(124,19,22,0.1);">XXL (58x77)</span>
                            </div>
                        </div>

                        {{-- Radio Button Input --}}
                        <div class="row">
                            @foreach(['S', 'M', 'L', 'XL', 'XXL', 'XXXL'] as $size)
                            <div class="col-4 col-md-2 mb-2">
                                <div class="checkbox-item">
                                    <input type="radio" name="peserta[{{ $index }}][ukuran_kaos]" value="{{ $size }}" id="p-{{ $index }}-size-{{ $size }}" 
                                           {{ old("peserta.{$index}.ukuran_kaos") == $size ? 'checked' : '' }} >
                                    <label class="checkbox-label justify-content-center font-weight-bold" for="p-{{ $index }}-size-{{ $size }}">
                                        {{ $size }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    {{-- Custom Question --}}
                    @if(!empty($form->pertanyaan_custom) && is_array($form->pertanyaan_custom))
                        @foreach($form->pertanyaan_custom as $qIdx => $q)
                        <div class="input-card p-0 shadow-none border-0 mb-3">
                            <label class="input-label">{{ $q['judul'] }} <span class="req-star">*</span></label>
                            @if(isset($q['pilihan']) && count($q['pilihan']) > 0 && $q['pilihan'][0] != "")
                                <div class="checkbox-group">
                                    @foreach($q['pilihan'] as $optIdx => $opt)
                                    <div class="checkbox-item">
                                        <input type="checkbox" name="peserta[{{ $index }}][jawaban_custom][{{ $qIdx }}][]" value="{{ $opt }}" id="p-{{ $index }}-c-{{ $qIdx }}-{{ $optIdx }}"
                                        {{ is_array(old("peserta.{$index}.jawaban_custom.{$qIdx}")) && in_array($opt, old("peserta.{$index}.jawaban_custom.{$qIdx}")) ? 'checked' : '' }}>
                                        <label class="checkbox-label" for="p-{{ $index }}-c-{{ $qIdx }}-{{ $optIdx }}">
                                            <div class="checkbox-icon"><i class="fas " style="font-size: 10px;"></i></div> {{ $opt }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <input type="text" name="peserta[{{ $index }}][jawaban_custom][{{ $qIdx }}]" class="form-control-ultra" 
                                       value="{{ old("peserta.{$index}.jawaban_custom.{$qIdx}") }}" required>
                            @endif
                        </div>
                        @endforeach
                    @endif

                </div> {{-- End Participant Wrapper --}}
                @endforeach
            </div>

            <button type="button" class="btn-add-row" onclick="addRow()">
                <i class="fas fa-plus-circle"></i> TAMBAH PESERTA LAIN
            </button>
        </div>

        {{-- BAGIAN C: PEMBAYARAN --}}
        <div class="animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="section-title"><i class="fas fa-file-invoice-dollar"></i> Pembayaran</div>
            <div class="input-card">
                <label class="input-label">Upload Bukti Pembayaran (Opsional)</label>
                <div class="upload-zone" id="uploadZoneBayar">
                    <input type="file" name="bukti_pembayaran" accept=".jpg,.jpeg,.png,.pdf"  onchange="updateFileBayar(this)">
                    <div id="uploadContentBayar">
                        <i class="fas fa-cloud-upload-alt text-muted mb-3" style="font-size: 32px;"></i>
                        <h6 class="text-dark">Upload Bukti Transfer</h6>
                        <p class="text-muted small mb-0">Format: JPG, PNG, PDF (Max 2MB)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 mb-5 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
            <button type="submit" class="btn-submit-ultra">
                <i class="fas fa-paper-plane mr-2"></i> KIRIM PENDAFTARAN
            </button>
        </div>
    </form>
</div>

{{-- TEMPLATE JAVASCRIPT UNTUK TAMBAH PESERTA --}}
<script type="text/template" id="template-peserta">
    <div class="participant-wrapper animate__animated animate__fadeIn" id="row-__INDEX__">
        <div class="participant-header">
            <span class="p-title"><i class="fas fa-user-circle mr-2"></i> Peserta #<span class="p-number">__NO__</span></span>
            <span class="btn-delete-row" onclick="deleteRow('row-__INDEX__')"><i class="fas fa-trash-alt mr-1"></i> Hapus</span>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="input-card p-0 shadow-none border-0 mb-3">
                    <label class="input-label">Nama Lengkap (Beserta Gelar) <span class="req-star">*</span></label>
                    <input type="text" name="peserta[__INDEX__][nama_lengkap]" class="form-control-ultra" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-card p-0 shadow-none border-0 mb-3">
                    <label class="input-label">Gelar (Opsional)</label>
                    <input type="text" name="peserta[__INDEX__][gelar]" class="form-control-ultra">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                 <div class="input-card p-0 shadow-none border-0 mb-3">
                    <label class="input-label">Tempat Lahir <span class="req-star">*</span></label>
                    <input type="text" name="peserta[__INDEX__][tempat_lahir]" class="form-control-ultra" required>
                </div>
            </div>
            <div class="col-md-6">
                 <div class="input-card p-0 shadow-none border-0 mb-3">
                    <label class="input-label">Tanggal Lahir <span class="req-star">*</span></label>
                    <input type="date" name="peserta[__INDEX__][tanggal_lahir]" class="form-control-ultra" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="input-card p-0 shadow-none border-0 mb-3">
                    <label class="input-label">NIK (KTP) <span class="req-star">*</span></label>
                    <input type="number" name="peserta[__INDEX__][nik]" class="form-control-ultra" placeholder="16 Digit" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-card p-0 shadow-none border-0 mb-3">
                    <label class="input-label">Jabatan <span class="req-star">*</span></label>
                    <input type="text" name="peserta[__INDEX__][jabatan]" class="form-control-ultra" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="input-card p-0 shadow-none border-0 mb-3">
                    <label class="input-label">Email Plataran Sehat <span class="req-star">*</span></label>
                    <input type="email" name="peserta[__INDEX__][email]" class="form-control-ultra" placeholder="email@kemkes.go.id" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-card p-0 shadow-none border-0 mb-3">
                    <label class="input-label">No. HP (WhatsApp) <span class="req-star">*</span></label>
                    <input type="number" name="peserta[__INDEX__][no_hp]" class="form-control-ultra" placeholder="08xxxxxxxx" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="input-card p-0 shadow-none border-0 mb-3">
                    <label class="input-label">Profesi <span class="req-star">*</span></label>
                    <input type="text" name="peserta[__INDEX__][profesi]" class="form-control-ultra" placeholder="Contoh: Perawat" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-card p-0 shadow-none border-0 mb-3">
                    <label class="input-label">Pendidikan Terakhir <span class="req-star">*</span></label>
                    <select name="peserta[__INDEX__][pendidikan_terakhir]" class="form-control-ultra" required>
                        <option value="">- Pilih -</option>
                        <option value="D3">D3</option>
                        <option value="D4">D4</option>
                        <option value="S1">S1</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="input-card p-0 shadow-none border-0 mb-3">
            <label class="input-label">Status Pegawai <span class="req-star">*</span></label>
            <div class="row">
                @foreach(['PNS', 'Non ASN/Swasta'] as $status)
                <div class="col-6 col-md-3 mb-2">
                    <div class="checkbox-item">
                        <input type="radio" name="peserta[__INDEX__][status_pegawai]" value="{{ $status }}" id="p-__INDEX__-status-{{ $status }}" required>
                        <label class="checkbox-label justify-content-center" for="p-__INDEX__-status-{{ $status }}">
                            {{ $status }}
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                 <div class="input-card p-0 shadow-none border-0 mb-3">
                    <label class="input-label">NIP (Opsional)</label>
                    <input type="text" name="peserta[__INDEX__][nip]" class="form-control-ultra">
                </div>
            </div>
            <div class="col-md-6">
                 <div class="input-card p-0 shadow-none border-0 mb-3">
                    <label class="input-label">Pangkat/Golongan (Opsional)</label>
                    <input type="text" name="peserta[__INDEX__][pangkat_golongan]" class="form-control-ultra">
                </div>
            </div>
        </div>

        <div class="input-card p-0 shadow-none border-0 mb-3">
            <label class="input-label">Pilih Pelatihan <span class="req-star">*</span></label>
            <div class="checkbox-group">
                @foreach($form->opsi_pelatihan as $optIdx => $opsi)
                <div class="checkbox-item">
                    <input type="checkbox" name="peserta[__INDEX__][pilihan_pelatihan][]" value="{{ $opsi }}" id="p-__INDEX__-pel-{{ $optIdx }}">
                    <label class="checkbox-label" for="p-__INDEX__-pel-{{ $optIdx }}">
                        <div class="checkbox-icon"><i class="fas " style="font-size: 10px;"></i></div> {{ $opsi }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>

        <div class="input-card p-0 shadow-none border-0 mb-3">
            <label class="input-label">Pilih Tempat <span class="req-star">*</span></label>
            <div class="checkbox-group">
                @foreach($form->opsi_tempat as $optIdx => $opsi)
                <div class="checkbox-item">
                    <input type="checkbox" name="peserta[__INDEX__][pilihan_tempat][]" value="{{ $opsi }}" id="p-__INDEX__-tmp-{{ $optIdx }}">
                    <label class="checkbox-label" for="p-__INDEX__-tmp-{{ $optIdx }}">
                        <div class="checkbox-icon"><i class="fas " style="font-size: 10px;"></i></div> {{ $opsi }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>


     <div class="input-card p-0 shadow-none border-0 mb-3">
            <label class="input-label">Ukuran Kaos </label>
            
            {{-- PANDUAN UKURAN STYLE BADGE (TEMPLATE JS) --}}
            <div style="background-color: #fff5f5; border-radius: 12px; padding: 15px; margin-bottom: 20px; border: 1px solid #f8d7da; border-left: 5px solid #7c1316;">
                
                <div style="color: #7c1316; font-weight: 800; font-size: 13px; margin-bottom: 8px; letter-spacing: 0.5px; text-transform: uppercase;">
                    <i class="fas fa-info-circle mr-1"></i> Khusus Pelatihan Berbayar & Blended Learning
                </div>
                
                <p style="font-size: 12px; color: #666; margin-bottom: 10px;">
                    Silakan pilih ukuran berdasarkan <strong>(Lebar x Panjang)</strong> cm:
                </p>

                <div class="d-flex flex-wrap" style="gap: 8px;">
                    <span style="background: white; color: #7c1316; border: 1px solid #7c1316; padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; box-shadow: 0 2px 4px rgba(124,19,22,0.1);">S (44x65)</span>
                    <span style="background: white; color: #7c1316; border: 1px solid #7c1316; padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; box-shadow: 0 2px 4px rgba(124,19,22,0.1);">M (47x67)</span>
                    <span style="background: white; color: #7c1316; border: 1px solid #7c1316; padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; box-shadow: 0 2px 4px rgba(124,19,22,0.1);">L (49x69)</span>
                    <span style="background: white; color: #7c1316; border: 1px solid #7c1316; padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; box-shadow: 0 2px 4px rgba(124,19,22,0.1);">XL (51x71)</span>
                    <span style="background: white; color: #7c1316; border: 1px solid #7c1316; padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; box-shadow: 0 2px 4px rgba(124,19,22,0.1);">XXL (53x71)</span>
    <span style="background: white; color: #7c1316; border: 1px solid #7c1316; padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; box-shadow: 0 2px 4px rgba(124,19,22,0.1);">XXL (58x77)</span>
                </div>
            </div>

            <div class="row">
                @foreach(['S', 'M', 'L', 'XL', 'XXL', 'XXXL'] as $size)
                <div class="col-4 col-md-2 mb-2">
                    <div class="checkbox-item">
                        <input type="radio" name="peserta[__INDEX__][ukuran_kaos]" value="{{ $size }}" id="p-__INDEX__-size-{{ $size }}" >
                        <label class="checkbox-label justify-content-center" for="p-__INDEX__-size-{{ $size }}">
                            {{ $size }}
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        @if(!empty($form->pertanyaan_custom) && is_array($form->pertanyaan_custom))
            @foreach($form->pertanyaan_custom as $qIdx => $q)
            <div class="input-card p-0 shadow-none border-0 mb-3">
                <label class="input-label">{{ $q['judul'] }} <span class="req-star">*</span></label>
                @if(isset($q['pilihan']) && count($q['pilihan']) > 0 && $q['pilihan'][0] != "")
                    <div class="checkbox-group">
                        @foreach($q['pilihan'] as $optIdx => $opt)
                        <div class="checkbox-item">
                            <input type="checkbox" name="peserta[__INDEX__][jawaban_custom][{{ $qIdx }}][]" value="{{ $opt }}" id="p-__INDEX__-c-{{ $qIdx }}-{{ $optIdx }}">
                            <label class="checkbox-label" for="p-__INDEX__-c-{{ $qIdx }}-{{ $optIdx }}">
                                <div class="checkbox-icon"><i class="fas" style="font-size: 10px;"></i></div> {{ $opt }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                @else
                    <input type="text" name="peserta[__INDEX__][jawaban_custom][{{ $qIdx }}]" class="form-control-ultra" required>
                @endif
            </div>
            @endforeach
        @endif
    </div>
</script>

<script>
    // Index awal mengikuti jumlah data 'old' (agar tidak bentrok jika form gagal validasi)
    let participantIndex = {{ count($oldPesertas) }};

    function addRow() {
        const container = document.getElementById('participants-list');
        let template = document.getElementById('template-peserta').innerHTML;
        
        // Replace Placeholder with Counter
        template = template.replace(/__INDEX__/g, participantIndex);
        template = template.replace(/__NO__/g, participantIndex + 1);

        // Convert String to HTML Node
        const div = document.createElement('div');
        div.innerHTML = template; 
        const newRow = div.firstElementChild; 
        
        container.appendChild(newRow);
        
        // Smooth Scroll ke elemen baru
        newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });

        participantIndex++;
        updateNumbers(); 
    }

    function deleteRow(rowId) {
        if(confirm('Hapus data peserta ini?')) {
            const row = document.getElementById(rowId);
            if(row) {
                row.classList.remove('animate__fadeIn');
                row.classList.add('animate__fadeOut');
                setTimeout(() => {
                    row.remove();
                    updateNumbers();
                }, 300);
            }
        }
    }

    function updateNumbers() {
        const numbers = document.querySelectorAll('.p-number');
        numbers.forEach((span, i) => {
            span.innerText = i + 1;
        });
    }

    function updateFileBayar(input) {
        const zone = document.getElementById('uploadContentBayar');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            zone.innerHTML = `
                <div class="animate__animated animate__fadeIn">
                    <i class="fas fa-check-circle text-success mb-2" style="font-size: 32px;"></i>
                    <h6 class="text-success font-weight-bold">${file.name}</h6>
                    <p class="text-muted small">Siap diupload</p>
                </div>
            `;
            const wrapper = document.getElementById('uploadZoneBayar');
            wrapper.style.borderColor = '#28a745';
            wrapper.style.backgroundColor = '#f0fff4';
        }
    }
</script>
@endsection