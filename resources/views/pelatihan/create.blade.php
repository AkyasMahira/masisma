@extends('layouts.app')

@section('title', 'Tambah Pelatihan')
@section('page-title', 'Tambah Data Pegawai & Pelatihan')

@section('content')
    {{-- Load Choices.js CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --card-radius: 16px;
            --transition: 0.3s ease;
        }

        .form-card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            background: #fff;
            overflow: hidden;
        }

        .card-header-custom {
            background-color: var(--custom-maroon);
            padding: 1.5rem;
            color: white;
            border-bottom: 4px solid var(--custom-maroon-light);
        }

        .card-header-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0;
        }

        .form-label {
            font-weight: 700;
            color: var(--text-dark);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
            color: var(--custom-maroon);
            border-radius: 10px 0 0 10px;
        }

        .form-control, .form-select {
            border-radius: 0 10px 10px 0;
            padding: 0.7rem 1rem;
            border-color: #dee2e6;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--custom-maroon-light);
            box-shadow: 0 0 0 0.2rem rgba(124, 19, 22, 0.1) !important;
        }

        .form-row-custom {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .form-row-custom { grid-template-columns: 1fr; }
        }

        .pelatihan-section {
            background: #f8f9fa;
            border: 2px dashed var(--custom-maroon);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .pelatihan-item {
            display: grid;
            grid-template-columns: 2fr 100px 80px 1.5fr auto;
            gap: 10px;
            margin-bottom: 10px;
            align-items: start;
        }

        .btn-maroon {
            background-color: var(--custom-maroon);
            color: white;
            border-radius: 50px;
            padding: 0.8rem 2rem;
            font-weight: 600;
            border: none;
            transition: var(--transition);
        }

        .btn-maroon:hover {
            background-color: var(--custom-maroon-light);
            transform: translateY(-2px);
        }

        .btn-remove {
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 8px;
            width: 40px;
            height: 40px;
        }

        .btn-add-pelatihan {
            background: var(--custom-maroon);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 15px;
            font-size: 0.9rem;
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="form-card">
                <div class="card-header-custom">
                    <h2 class="card-header-title"><i class="fas fa-user-plus me-2"></i>Tambah Data Pegawai & Pelatihan</h2>
                </div>

                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('pelatihan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-row-custom">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap & Gelar</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="nama" class="form-control" placeholder="Nama lengkap..." value="{{ old('nama') }}" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">NIK (Nomor Induk Kependudukan)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="nik" class="form-control" placeholder="16 Digit NIK" value="{{ old('nik') }}" maxlength="16">
                                </div>
                            </div>
                        </div>

                        <div class="form-row-custom">
                            <div class="form-group">
                                <label class="form-label">Jabatan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                    <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Perawat Terampil" value="{{ old('jabatan') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Unit/Ruang Kerja</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    <input type="text" name="unit" class="form-control" placeholder="Contoh: IGD / Rawat Inap" value="{{ old('unit') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-row-custom">
                            <div class="form-group">
                                <label class="form-label">Bidang</label>
                                <select id="bidang" name="bidang" class="form-control" required>
                                    <option value="">-- Pilih Bidang --</option>
                                    @foreach(['Keperawatan', 'Pelayanan Medik', 'Penunjang Klinik', 'Penunjang Non Klinik', 'Kepegawaian', 'Perencanaan', 'Keuangan'] as $b)
                                        <option value="{{ $b }}" {{ old('bidang') == $b ? 'selected' : '' }}>{{ $b }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Status Pegawai</label>
                                <select id="status_pegawai" name="status_pegawai" class="form-select" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="ASN" {{ old('status_pegawai') == 'ASN' ? 'selected' : '' }}>ASN</option>
                                    <option value="KARYAWAN TETAP" {{ old('status_pegawai') == 'KARYAWAN TETAP' ? 'selected' : '' }}>KARYAWAN TETAP</option>
                                    <option value="NON ASN" {{ old('status_pegawai') == 'NON ASN' ? 'selected' : '' }}>NON ASN</option>
                                </select>
                            </div>
                        </div>

                        {{-- KONDISIONAL FIELDS NIP/NIRP --}}
                        <div class="form-group mb-4" id="conditionalFields">
                            <div id="pnsFields" style="display:none;" class="p-3 bg-light rounded border">
                                <label class="form-label">Detail Kepegawaian</label>
                                <div class="row g-2">
                                    <div class="col-md-4"><input type="text" name="nip" id="nip" class="form-control" placeholder="NIP" value="{{ old('nip') }}"></div>
                                    <div class="col-md-4"><input type="text" name="golongan" id="golongan" class="form-control" placeholder="Golongan" value="{{ old('golongan') }}"></div>
                                    <div class="col-md-4" id="wrapper_pangkat"><input type="text" name="pangkat" id="pangkat" class="form-control" placeholder="Pangkat" value="{{ old('pangkat') }}"></div>
                                </div>
                            </div>
                            <div id="nonPnsFields" style="display:none;" class="p-3 bg-light rounded border">
                                <label class="form-label">NIRP</label>
                                <input type="text" name="nirp" id="nirp" class="form-control" placeholder="Nomor Induk Rumah Sakit" value="{{ old('nirp') }}">
                            </div>
                        </div>

                        {{-- AKUN LMS --}}
                        <div class="form-row-custom bg-light p-3 rounded mb-4 border">
                            <div class="form-group mb-0">
                                <label class="form-label">Akun LMS (Kemenkes/SatuSehat)</label>
                                <select name="lms_status" id="lms_status" class="form-select">
                                    <option value="Tidak" {{ old('lms_status') == 'Tidak' ? 'selected' : '' }}>Tidak Ada</option>
                                    <option value="Ada" {{ old('lms_status') == 'Ada' ? 'selected' : '' }}>Ada</option>
                                </select>
                            </div>
                            <div class="form-group mb-0" id="lms_email_wrapper" style="display:none;">
                                <label class="form-label">Email Akun LMS</label>
                                <input type="email" name="lms_email" id="lms_email" class="form-control" placeholder="email@contoh.com" value="{{ old('lms_email') }}">
                            </div>
                        </div>

                        {{-- PELATIHAN DASAR --}}
                        <div class="pelatihan-section">
                            <div class="pelatihan-title"><i class="fas fa-certificate me-2"></i>Riwayat Pelatihan Dasar</div>
                            <div id="pelatihanDasarContainer">
                                <div class="pelatihan-item">
                                    <input type="text" name="pelatihan_dasar[]" class="form-control" placeholder="Nama Pelatihan">
                                    <input type="number" name="pelatihan_tahun_dasar[]" class="form-control" placeholder="Tahun">
                                    <input type="number" name="pelatihan_jpl_dasar[]" class="form-control" placeholder="JPL">
                                    <input type="file" name="pelatihan_file_dasar[]" class="form-control" accept=".pdf">
                                    <div></div> {{-- Spacer --}}
                                </div>
                            </div>
                            <button type="button" class="btn-add-pelatihan" onclick="addPelatihan('pelatihanDasarContainer', 'dasar')"><i class="fas fa-plus"></i> Tambah Pelatihan Dasar</button>
                        </div>

                        {{-- PENINGKATAN KOMPETENSI --}}
                        <div class="pelatihan-section">
                            <div class="pelatihan-title"><i class="fas fa-medal me-2"></i>Peningkatan Kompetensi</div>
                            <div id="pelatihanKompetensiContainer">
                                <div class="pelatihan-item">
                                    <input type="text" name="pelatihan_kompetensi[]" class="form-control" placeholder="Nama Pelatihan">
                                    <input type="number" name="pelatihan_tahun_kompetensi[]" class="form-control" placeholder="Tahun">
                                    <input type="number" name="pelatihan_jpl_kompetensi[]" class="form-control" placeholder="JPL">
                                    <input type="file" name="pelatihan_file_kompetensi[]" class="form-control" accept=".pdf">
                                    <div></div> {{-- Spacer --}}
                                </div>
                            </div>
                            <button type="button" class="btn-add-pelatihan" onclick="addPelatihan('pelatihanKompetensiContainer', 'kompetensi')"><i class="fas fa-plus"></i> Tambah Pelatihan Kompetensi</button>
                        </div>

                        <div class="button-group">
                            <button type="submit" class="btn-maroon"><i class="fas fa-save me-2"></i>Simpan Data</button>
                            <a href="{{ route('pelatihan.index') }}" class="btn-secondary-custom">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bidang = document.getElementById('bidang');
            if(bidang) new Choices(bidang, { searchEnabled: true, itemSelectText: '', placeholderValue: '-- Pilih Bidang --' });

            const statusPegawai = document.getElementById('status_pegawai');
            statusPegawai.addEventListener('change', toggleStatusFields);

            const lmsStatus = document.getElementById('lms_status');
            lmsStatus.addEventListener('change', toggleLmsField);

            toggleStatusFields();
            toggleLmsField();
        });

        function toggleStatusFields() {
            const val = document.getElementById('status_pegawai').value;
            const pns = document.getElementById('pnsFields');
            const nonPns = document.getElementById('nonPnsFields');
            const wrapperPangkat = document.getElementById('wrapper_pangkat');

            pns.style.display = (val === 'ASN' || val === 'KARYAWAN TETAP') ? 'block' : 'none';
            nonPns.style.display = (val === 'NON ASN') ? 'block' : 'none';
            wrapperPangkat.style.display = (val === 'ASN') ? 'block' : 'none';
        }

        function toggleLmsField() {
            const status = document.getElementById('lms_status').value;
            document.getElementById('lms_email_wrapper').style.display = (status === 'Ada') ? 'block' : 'none';
        }

        function addPelatihan(containerId, type) {
            const container = document.getElementById(containerId);
            const item = document.createElement('div');
            item.className = 'pelatihan-item';
            item.innerHTML = `
                <input type="text" class="form-control" name="pelatihan_${type}[]" placeholder="Nama Pelatihan">
                <input type="number" class="form-control" name="pelatihan_tahun_${type}[]" placeholder="Tahun">
                <input type="number" class="form-control" name="pelatihan_jpl_${type}[]" placeholder="JPL">
                <input type="file" class="form-control" name="pelatihan_file_${type}[]" accept=".pdf">
                <button type="button" class="btn-remove" onclick="this.parentElement.remove()"><i class="fas fa-trash"></i></button>
            `;
            container.appendChild(item);
        }
    </script>
@endsection