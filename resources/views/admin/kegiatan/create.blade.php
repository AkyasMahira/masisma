@extends('layouts.app')
@section('title', 'Buat Kegiatan Baru')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    
    <style>
        :root { 
            --custom-maroon: #7c1316; 
            --custom-maroon-light: #fef1f2; 
            --card-radius: 20px; 
            --border-color: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        .page-header { background: #fff; border-radius: var(--card-radius); padding: 25px 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 25px; border-left: 5px solid var(--custom-maroon); display: flex; justify-content: space-between; align-items: center;}
        
        .form-card { border: none; border-radius: var(--card-radius); box-shadow: 0 10px 40px rgba(0,0,0,0.03); background: #fff; border: 1px solid var(--border-color); overflow: hidden;}
        .form-card-body { padding: 40px; }
        
        .form-label-custom { font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: block; }
        .form-control, .form-select { border-radius: 10px; padding: 12px 15px; border: 1px solid var(--border-color); font-size: 0.95rem; color: var(--text-dark); background: #f8fafc; transition: 0.3s;}
        .form-control:focus, .form-select:focus { border-color: var(--custom-maroon); box-shadow: 0 0 0 4px var(--custom-maroon-light); background: #fff;}
        
        .choices__inner { background-color: #f8fafc !important; border: 1px solid var(--border-color) !important; border-radius: 10px !important; padding: 7px 15px !important; min-height: 48px !important; display: flex; align-items: center; transition: 0.3s;}
        .choices.is-focused .choices__inner { border-color: var(--custom-maroon) !important; box-shadow: 0 0 0 4px var(--custom-maroon-light) !important; background: #fff !important;}

        .color-picker-box { background: #f8fafc; border: 1px solid var(--border-color); border-radius: 10px; padding: 10px; display: flex; align-items: center; gap: 15px;}
        .form-control-color { width: 50px; height: 50px; padding: 2px; border-radius: 8px; cursor: pointer; border: none; background: transparent; }

        .btn-theme { background-color: var(--custom-maroon); color: white; border: none; padding: 12px 30px; border-radius: 10px; font-weight: 700; transition: 0.3s; font-size: 1rem; box-shadow: 0 8px 15px rgba(124, 19, 22, 0.2);}
        .btn-theme:hover { background-color: #5a0d10; color: white; transform: translateY(-2px); box-shadow: 0 12px 20px rgba(124, 19, 22, 0.3);}
        .btn-outline-theme { background-color: #fff; color: var(--text-muted); border: 1px solid var(--border-color); padding: 12px 30px; border-radius: 10px; font-weight: 700; transition: 0.3s; font-size: 1rem;}
        .btn-outline-theme:hover { background-color: #f1f5f9; color: var(--text-dark); }
        
        .section-title { font-size: 1.1rem; font-weight: 800; color: var(--text-dark); margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px dashed var(--border-color);}

        /* Styling Input Dinamis */
        .dynamic-row { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px; margin-bottom: 10px; position: relative;}
        .btn-remove-row { position: absolute; top: 10px; right: 10px; background: #fee2e2; color: #dc2626; border: none; border-radius: 6px; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; transition: 0.2s; z-index: 10;}
        .btn-remove-row:hover { background: #dc2626; color: white;}
    </style>

    <div class="row">
        <div class="col-12">
            
            <div class="page-header">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-calendar-plus-fill me-2 text-danger"></i> Buat Kegiatan Baru</h3>
                    <p class="mb-0 opacity-75 small fw-bold text-muted">Sistem Informasi Diklat Terintegrasi</p>
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-body">
                    <form action="{{ route('admin.kegiatan.store') }}" method="POST">
                        @csrf
                        
                        <div class="section-title"><i class="bi bi-info-circle-fill me-2 text-danger"></i> Informasi Dasar Kegiatan</div>
                        <div class="row g-4 mb-5">
                            <div class="col-md-8">
                                <label class="form-label-custom">Nama Kegiatan <span class="text-danger">*</span></label>
                                <input type="text" name="nama_kegiatan" class="form-control" placeholder="Cth: Inhouse Training Resusitasi Jantung" value="{{ old('nama_kegiatan') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">Jenis Event <span class="text-danger">*</span></label>
                                <select name="jenis_kegiatan" class="form-select" required>
                                    <option value="internal" {{ old('jenis_kegiatan') == 'internal' ? 'selected' : '' }}>Internal RSUD</option>
                                    <option value="eksternal" {{ old('jenis_kegiatan') == 'eksternal' ? 'selected' : '' }}>Eksternal / Publik</option>
                                </select>
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label-custom">Deskripsi Kegiatan</label>
                                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Tuliskan latar belakang atau deskripsi singkat acara ini...">{{ old('deskripsi') }}</textarea>
                            </div>
<div class="col-md-12">
    <label class="form-label-custom">Keahlian / Skill yang Diampu <span class="text-muted fw-normal text-transform-none" style="font-size:0.7rem;">(Pilih dari daftar atau ketik baru lalu tekan Enter)</span></label>
    <select name="keahlian[]" id="keahlianInput" multiple class="form-control bg-white" placeholder="Contoh: Ms Word, Kepemimpinan..."></select>
</div>
                            <div class="col-md-12">
                                <label class="form-label-custom">Instansi Penyelenggara <span class="text-danger">*</span></label>
                                <select name="penyelenggara_id" id="penyelenggara_id" class="form-select" required>
                                    <option value="">Ketik untuk mencari instansi...</option>
                                    @foreach($instansi as $i) <option value="{{ $i->id }}">{{ $i->nama_instansi }}</option> @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- START: SECTION TUJUAN DINAMIS -->
                        <div class="section-title mt-5"><i class="bi bi-bullseye me-2 text-danger"></i> Tujuan Kegiatan</div>
                        <div id="tujuan-container" class="mb-3">
                            <div class="dynamic-row">
                                <button type="button" class="btn-remove-row" onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
                                <label class="form-label-custom">Tujuan</label>
                                <input type="text" name="tujuan[]" class="form-control bg-white" placeholder="Contoh: Peserta mampu memahami..." required>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-theme mb-5" onclick="addTujuanRow()">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Tujuan
                        </button>
                        <!-- END: SECTION TUJUAN -->

                        <!-- START: SECTION KOMPETENSI & INDIKATOR -->
                        <div class="section-title mt-2"><i class="bi bi-award-fill me-2 text-danger"></i> Master Kompetensi & Indikator</div>
                        <div id="kompetensi-container" class="mb-3">
                            <!-- Container Kosong (Baris ditambahkan via JS) -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-theme mb-5" onclick="addKompetensiRow()">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Kompetensi Acara
                        </button>
                        <!-- END: SECTION KOMPETENSI -->

                        <div class="section-title"><i class="bi bi-clock-fill me-2 text-danger"></i> Waktu & Teknis Pelaksanaan</div>
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <label class="form-label-custom">Total JPL (Jam Pelajaran) </label>
                                <div class="input-group">
                                    <input type="number" name="jpl" class="form-control border-end-0" placeholder="Cth: 4" value="{{ old('jpl') }}" min="1">
                                    <span class="input-group-text bg-white border-start-0 text-muted">JPL</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Tanggal & Jam Mulai <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Tanggal & Jam Selesai <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Lokasi / Platform <span class="text-danger">*</span></label>
                                <input type="text" name="platform" class="form-control" placeholder="Cth: Zoom Meeting / Ruang Rapat Lt 2" value="{{ old('platform') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Sistem Presensi <span class="text-danger">*</span></label>
                                <select name="tipe_absen" class="form-select" required>
                                    <option value="masuk_saja" {{ old('tipe_absen') == 'masuk_saja' ? 'selected' : '' }}>Satu Kali (Absen Hadir Saja)</option>
                                    <option value="masuk_keluar" {{ old('tipe_absen') == 'masuk_keluar' ? 'selected' : '' }}>Dua Kali (Absen Datang & Pulang)</option>
                                </select>
                            </div>
                        </div>

                        <div class="section-title"><i class="bi bi-palette-fill me-2 text-danger"></i> Kustomisasi Tampilan (Opsional)</div>
                        <div class="row g-4 mb-5">
                            <div class="col-md-12">
                                <label class="form-label-custom">Warna Tema Utama Kegiatan</label>
                                <div class="color-picker-box">
                                    <input type="color" name="warna_tema" class="form-control form-control-color" value="#7c1316" title="Pilih Warna Tema">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">Pilih Warna Identitas Event</div>
                                        <div class="text-muted small">Warna ini akan mengubah tampilan form absensi dan sertifikat khusus untuk kegiatan ini. Default: Maroon RSUD.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                            <a href="{{ route('admin.kegiatan.index') }}" class="btn-outline-theme" style="text-decoration:none;">Batal & Kembali</a>
                            <button type="submit" class="btn-theme"><i class="bi bi-check-circle-fill me-2"></i> Simpan Kegiatan Baru</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    
    <!-- Data Master Kompetensi untuk Javascript Clone -->
    <script>
        const masterKompetensiData = @json($master_kompetensi);
    </script>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script> 
        document.addEventListener('DOMContentLoaded', () => {
            new Choices(document.getElementById('penyelenggara_id'), {searchEnabled: true});
        });

        // FUNGSI TAMBAH ROW TUJUAN
        function addTujuanRow() {
            const container = document.getElementById('tujuan-container');
            const html = `
                <div class="dynamic-row">
                    <button type="button" class="btn-remove-row" onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
                    <label class="form-label-custom">Tujuan</label>
                    <input type="text" name="tujuan[]" class="form-control bg-white" placeholder="Contoh: Peserta mampu memahami..." required>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }

        // FUNGSI TAMBAH ROW KOMPETENSI
        function addKompetensiRow() {
            const container = document.getElementById('kompetensi-container');
            
            // Generate Option Dropdown
            let optionsHtml = '<option value="">Pilih Master Kompetensi...</option>';
            masterKompetensiData.forEach(mk => {
                optionsHtml += `<option value="${mk.id}">${mk.nama_kompetensi}</option>`;
            });

            // Buat ID unik untuk class agar bisa di init Choices.js secara terpisah
            const uniqueClass = 'komp-' + Math.random().toString(36).substr(2, 9);

            const html = `
                <div class="dynamic-row">
                    <button type="button" class="btn-remove-row" onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label-custom">Pilih Kompetensi</label>
                            <select name="kompetensi_id[]" class="form-select bg-white ${uniqueClass}" required>
                                ${optionsHtml}
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label-custom">Indikator Keberhasilan</label>
                            <textarea name="indikator_keberhasilan[]" class="form-control bg-white" rows="2" placeholder="Contoh: Menyampaikan informasi secara jelas..."></textarea>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', html);

            // Inisialisasi Choices.js hanya untuk dropdown yang baru saja dibuat
            new Choices('.' + uniqueClass, {
                searchEnabled: true,
                itemSelectText: ''
            });
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    
    <script> 
        document.addEventListener('DOMContentLoaded', () => {
            // Init Choices.js untuk select biasa (Instansi)
            new Choices(document.getElementById('penyelenggara_id'), {searchEnabled: true});

            // Init TOM SELECT khusus untuk Autocomplete Tags Keahlian
            const riwayatKeahlian = @json($listKeahlian ?? []);
            const optionsKeahlian = riwayatKeahlian.map(skill => {
                return { value: skill, text: skill };
            });

            new TomSelect("#keahlianInput", {
                plugins: ['remove_button'],
                create: true, // Mengizinkan user mengetik skill baru jika tidak ada di daftar
                options: optionsKeahlian,
                persist: false,
                createOnBlur: true,
                placeholder: 'Klik untuk memilih atau ketik keahlian baru...'
            });
        });
        
        // ... (fungsi addTujuanRow dan addKompetensiRow biarkan seperti sebelumnya) ...
    </script>
@endsection