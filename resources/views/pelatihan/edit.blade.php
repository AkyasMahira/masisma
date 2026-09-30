@extends('layouts.app')

@section('title', 'Edit Data Pegawai')
@section('page-title', 'Pembaruan Data & Riwayat Pelatihan')

@section('content')
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --border-color: #dee2e6;
            --card-radius: 16px;
            --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            background: #fff;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-header-custom {
            background-color: var(--custom-maroon);
            padding: 1.5rem 2rem;
            color: white;
            border-bottom: 4px solid var(--custom-maroon-light);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-label {
            font-weight: 700;
            color: var(--text-dark);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.6rem;
            display: block;
        }

        .input-group-text {
            background-color: #f8f9fa;
            color: var(--custom-maroon);
            border-right: none;
            border-radius: 10px 0 0 10px;
            padding: 0.75rem 1rem;
        }

        .form-control, .form-select {
            border-radius: 0 10px 10px 0;
            padding: 0.75rem 1rem;
            border-color: var(--border-color);
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--custom-maroon-light);
            box-shadow: 0 0 0 0.25rem rgba(124, 19, 22, 0.1) !important;
        }

        /* Section Styling */
        .section-container {
            padding: 2rem;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--custom-maroon-subtle);
            color: var(--custom-maroon);
        }

        .section-header h4 {
            font-weight: 800;
            margin: 0;
            font-size: 1.1rem;
        }

        /* Pelatihan Item Styling */
        .pelatihan-item {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            display: grid;
            grid-template-columns: 2fr 1fr 0.8fr 2fr auto;
            gap: 12px;
            align-items: end;
            transition: var(--transition);
        }

        .pelatihan-item:hover {
            border-color: var(--custom-maroon-light);
            background: #fffcfc;
        }

        .btn-remove {
            background: #fee2e2;
            color: #dc2626;
            border: none;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .btn-remove:hover {
            background: #dc2626;
            color: white;
        }

        .btn-add-pelatihan {
            background: #fff;
            color: var(--custom-maroon);
            border: 2px dashed var(--custom-maroon);
            width: 100%;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 700;
            transition: var(--transition);
        }

        .btn-add-pelatihan:hover {
            background: var(--custom-maroon-subtle);
            border-style: solid;
        }

        .current-file-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #e0f2fe;
            color: #0369a1;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 8px;
            text-decoration: none;
        }

        /* Floating Button Group */
        .action-bar {
            background: #f8f9fa;
            padding: 1.5rem 2rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-save {
            background: var(--custom-maroon);
            color: white;
            padding: 0.75rem 2.5rem;
            border-radius: 50px;
            font-weight: 700;
            border: none;
            box-shadow: 0 4px 12px rgba(124, 19, 22, 0.2);
            transition: var(--transition);
        }

        .btn-save:hover {
            background: var(--custom-maroon-light);
            transform: translateY(-2px);
        }

        @media (max-width: 992px) {
            .pelatihan-item {
                grid-template-columns: 1fr 1fr;
            }
            .btn-remove { width: 100%; grid-column: span 2; }
        }
    </style>

    <div class="container-fluid">
        <form action="{{ route('pelatihan.update', $pelatihan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="form-card">
                        <div class="card-header-custom">
                            <i class="fas fa-user-edit fa-lg"></i>
                            <h2 class="card-header-title">Identitas Pegawai</h2>
                        </div>

                        <div class="section-container">
                            @if ($errors->any())
                                <div class="alert alert-danger border-0 shadow-sm rounded-3">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Periksa kembali inputan Anda:</strong>
                                    </div>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="row g-4">
                                {{-- Baris 1: Nama & NIK --}}
                                <div class="col-md-7">
                                    <label class="form-label">Nama Lengkap & Gelar</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $pelatihan->nama) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">NIK (No. Induk Kependudukan)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        <input type="text" name="nik" class="form-control" value="{{ old('nik', $pelatihan->nik) }}" maxlength="16">
                                    </div>
                                </div>

                                {{-- Baris 2: Bidang & Status --}}
                                <div class="col-md-6">
                                    <label class="form-label">Bidang / Instalasi</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-sitemap"></i></span>
                                        <select name="bidang" class="form-select" required>
                                            @foreach(['Keperawatan','Pelayanan Medik','Penunjang Klinik','Penunjang Non Klinik','Kepegawaian','Perencanaan','Keuangan'] as $bid)
                                                <option value="{{ $bid }}" {{ old('bidang', $pelatihan->bidang) == $bid ? 'selected' : '' }}>{{ $bid }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status Kepegawaian</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                        <select id="status_pegawai" name="status_pegawai" class="form-select" required>
                                            <option value="ASN" {{ old('status_pegawai', $pelatihan->status_pegawai) == 'ASN' ? 'selected' : '' }}>ASN (PNS/PPPK)</option>
                                            <option value="KARYAWAN TETAP" {{ old('status_pegawai', $pelatihan->status_pegawai) == 'KARYAWAN TETAP' ? 'selected' : '' }}>KARYAWAN TETAP</option>
                                            <option value="NON ASN" {{ old('status_pegawai', $pelatihan->status_pegawai) == 'NON ASN' ? 'selected' : '' }}>NON ASN / HONORER</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Baris 3: Kondisional NIP/NIRP/Pangkat --}}
                                <div class="col-12" id="conditional_container">
                                    <div id="pnsFields" class="row g-4" style="display: none;">
                                        <div class="col-md-4">
                                            <label class="form-label">NIP / NI P3K</label>
                                            <input type="text" name="nip" class="form-control" value="{{ old('nip', $pelatihan->nip) }}" placeholder="Masukkan NIP">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Golongan</label>
                                            <input type="text" name="golongan" class="form-control" value="{{ old('golongan', $pelatihan->golongan) }}" placeholder="Contoh: III/a">
                                        </div>
                                        <div class="col-md-4" id="wrapper_pangkat">
                                            <label class="form-label">Pangkat</label>
                                            <input type="text" name="pangkat" class="form-control" value="{{ old('pangkat', $pelatihan->pangkat) }}" placeholder="Contoh: Penata Muda">
                                        </div>
                                    </div>
                                    <div id="nonPnsFields" style="display: none;">
                                        <label class="form-label">NIRP</label>
                                        <input type="text" name="nirp" class="form-control" value="{{ old('nirp', $pelatihan->nirp) }}" placeholder="Masukkan Nomor Induk Rumah Sakit">
                                    </div>
                                </div>

                                {{-- Baris 4: LMS Kemenkes --}}
                                <div class="col-md-4">
                                    <label class="form-label">Akun LMS (SatuSehat)</label>
                                    <select id="lms_status" name="lms_status" class="form-select">
                                        <option value="Tidak" {{ old('lms_status', $pelatihan->lms_status) == 'Tidak' ? 'selected' : '' }}>Tidak Ada</option>
                                        <option value="Ada" {{ old('lms_status', $pelatihan->lms_status) == 'Ada' ? 'selected' : '' }}>Ada / Terdaftar</option>
                                    </select>
                                </div>
                                <div class="col-md-8" id="lms_email_wrapper" style="display: none;">
                                    <label class="form-label">Email Terdaftar di LMS</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" name="lms_email" class="form-control" value="{{ old('lms_email', $pelatihan->lms_email) }}" placeholder="contoh@kemkes.go.id">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIWAYAT PELATIHAN --}}
                        <div class="section-container bg-light-subtle">
                            {{-- Pelatihan Dasar --}}
                            <div class="section-header">
                                <i class="fas fa-award"></i>
                                <h4>Riwayat Pelatihan Dasar (Wajib)</h4>
                            </div>
                            <div id="pelatihanDasarContainer">
                                @php
                                    $dataDasar = old('pelatihan_dasar') ? [] : $pelatihan->pelatihan_dasar ?? [];
                                    if(old('pelatihan_dasar')){
                                        foreach(old('pelatihan_dasar') as $k => $v){
                                            $dataDasar[] = ['nama' => $v, 'tahun' => old('pelatihan_tahun_dasar')[$k], 'jpl' => old('pelatihan_jpl_dasar')[$k], 'file' => old('pelatihan_existing_file_dasar')[$k]];
                                        }
                                    }
                                @endphp
                                @foreach($dataDasar as $item)
                                    <div class="pelatihan-item">
                                        <div>
                                            <label class="form-label">Nama Pelatihan</label>
                                            <input type="text" name="pelatihan_dasar[]" class="form-control" value="{{ $item['nama'] }}" required>
                                        </div>
                                        <div>
                                            <label class="form-label">Tahun</label>
                                            <input type="number" name="pelatihan_tahun_dasar[]" class="form-control" value="{{ $item['tahun'] }}">
                                        </div>
                                        <div>
                                            <label class="form-label">JPL</label>
                                            <input type="number" name="pelatihan_jpl_dasar[]" class="form-control" value="{{ $item['jpl'] ?? 0 }}">
                                        </div>
                                        <div>
                                            <label class="form-label">Sertifikat (PDF)</label>
                                            <input type="file" name="pelatihan_file_dasar[]" class="form-control" accept=".pdf">
                                            <input type="hidden" name="pelatihan_existing_file_dasar[]" value="{{ $item['file'] }}">
                                            @if($item['file'])
                                                <a href="{{ asset('storage/' . $item['file']) }}" target="_blank" class="current-file-badge">
                                                    <i class="fas fa-file-pdf"></i> Lihat File
                                                </a>
                                            @endif
                                        </div>
                                        <button type="button" class="btn-remove" onclick="removeRow(this)"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn-add-pelatihan mb-5" onclick="addPelatihan('pelatihanDasarContainer', 'dasar')">
                                <i class="fas fa-plus-circle me-1"></i> Tambah Baris Pelatihan Dasar
                            </button>

                            {{-- Pelatihan Kompetensi --}}
                            <div class="section-header">
                                <i class="fas fa-chart-line"></i>
                                <h4>Peningkatan Kompetensi</h4>
                            </div>
                            <div id="pelatihanKompetensiContainer">
                                @php
                                    $dataKomp = old('pelatihan_kompetensi') ? [] : $pelatihan->pelatihan_peningkatan_kompetensi ?? [];
                                    if(old('pelatihan_kompetensi')){
                                        foreach(old('pelatihan_kompetensi') as $k => $v){
                                            $dataKomp[] = ['nama' => $v, 'tahun' => old('pelatihan_tahun_kompetensi')[$k], 'jpl' => old('pelatihan_jpl_kompetensi')[$k], 'file' => old('pelatihan_existing_file_kompetensi')[$k]];
                                        }
                                    }
                                @endphp
                                @foreach($dataKomp as $item)
                                    <div class="pelatihan-item">
                                        <div>
                                            <label class="form-label">Nama Pelatihan</label>
                                            <input type="text" name="pelatihan_kompetensi[]" class="form-control" value="{{ $item['nama'] }}" required>
                                        </div>
                                        <div>
                                            <label class="form-label">Tahun</label>
                                            <input type="number" name="pelatihan_tahun_kompetensi[]" class="form-control" value="{{ $item['tahun'] }}">
                                        </div>
                                        <div>
                                            <label class="form-label">JPL</label>
                                            <input type="number" name="pelatihan_jpl_kompetensi[]" class="form-control" value="{{ $item['jpl'] ?? 0 }}">
                                        </div>
                                        <div>
                                            <label class="form-label">Sertifikat (PDF)</label>
                                            <input type="file" name="pelatihan_file_kompetensi[]" class="form-control" accept=".pdf">
                                            <input type="hidden" name="pelatihan_existing_file_kompetensi[]" value="{{ $item['file'] }}">
                                            @if($item['file'])
                                                <a href="{{ asset('storage/' . $item['file']) }}" target="_blank" class="current-file-badge">
                                                    <i class="fas fa-file-pdf"></i> Lihat File
                                                </a>
                                            @endif
                                        </div>
                                        <button type="button" class="btn-remove" onclick="removeRow(this)"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn-add-pelatihan" onclick="addPelatihan('pelatihanKompetensiContainer', 'kompetensi')">
                                <i class="fas fa-plus-circle me-1"></i> Tambah Baris Kompetensi
                            </button>
                        </div>

                        <div class="action-bar">
                            <a href="{{ route('pelatihan.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">Batal</a>
                            <button type="submit" class="btn-save">
                                <i class="fas fa-check-circle me-1"></i> Perbarui Data Pegawai
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function addPelatihan(containerId, type) {
            const container = document.getElementById(containerId);
            const div = document.createElement('div');
            div.className = 'pelatihan-item animate__animated animate__fadeInUp';
            
            const existingName = type === 'dasar' ? 'pelatihan_existing_file_dasar[]' : 'pelatihan_existing_file_kompetensi[]';

            div.innerHTML = `
                <div>
                    <label class="form-label">Nama Pelatihan</label>
                    <input type="text" name="pelatihan_${type}[]" class="form-control" placeholder="Nama pelatihan..." required>
                </div>
                <div>
                    <label class="form-label">Tahun</label>
                    <input type="number" name="pelatihan_tahun_${type}[]" class="form-control" placeholder="Tahun" value="${new Date().getFullYear()}">
                </div>
                <div>
                    <label class="form-label">JPL</label>
                    <input type="number" name="pelatihan_jpl_${type}[]" class="form-control" placeholder="0" value="0">
                </div>
                <div>
                    <label class="form-label">Sertifikat (PDF)</label>
                    <input type="file" name="pelatihan_file_${type}[]" class="form-control" accept=".pdf">
                    <input type="hidden" name="${existingName}" value="">
                </div>
                <button type="button" class="btn-remove" onclick="removeRow(this)"><i class="fas fa-trash-alt"></i></button>
            `;
            container.appendChild(div);
        }

        function removeRow(btn) {
            const item = btn.closest('.pelatihan-item');
            item.style.opacity = '0';
            item.style.transform = 'translateX(20px)';
            setTimeout(() => item.remove(), 300);
        }

        function handleStatusToggle() {
            const val = document.getElementById('status_pegawai').value;
            const pnsFields = document.getElementById('pnsFields');
            const nonPnsFields = document.getElementById('nonPnsFields');
            const wrapperPangkat = document.getElementById('wrapper_pangkat');

            pnsFields.style.display = (val === 'ASN' || val === 'KARYAWAN TETAP') ? 'flex' : 'none';
            nonPnsFields.style.display = (val === 'NON ASN') ? 'block' : 'none';
            wrapperPangkat.style.display = (val === 'ASN') ? 'block' : 'none';
        }

        function handleLmsToggle() {
            const status = document.getElementById('lms_status').value;
            document.getElementById('lms_email_wrapper').style.display = (status === 'Ada') ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('status_pegawai').addEventListener('change', handleStatusToggle);
            document.getElementById('lms_status').addEventListener('change', handleLmsToggle);
            handleStatusToggle();
            handleLmsToggle();
        });
    </script>
@endsection