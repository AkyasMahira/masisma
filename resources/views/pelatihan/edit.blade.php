@extends('layouts.app')

@section('title', 'Edit Data')
@section('page-title', 'Edit Data Pegawai & Pelatihan')

@section('content')
    <style>
        /* ... (CSS Anda TETAP SAMA) ... */
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #95a5a6;
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
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
            color: var(--custom-maroon);
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .form-control,
        .form-select {
            border-left: none;
            border-radius: 0 10px 10px 0;
            padding: 0.7rem 1rem;
            border-color: #dee2e6;
            box-shadow: none !important;
            transition: border-color 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--custom-maroon-light);
            box-shadow: 0 0 0 0.2rem rgba(124, 19, 22, 0.1) !important;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.3rem;
        }

        .form-row-custom {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .form-row-custom {
                grid-template-columns: 1fr;
            }
        }

        .btn-maroon {
            background-color: var(--custom-maroon);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(124, 19, 22, 0.2);
        }

        .btn-maroon:hover {
            background-color: var(--custom-maroon-light);
            transform: translateY(-2px);
            color: white;
        }

        .btn-secondary-custom {
            background: #e9ecef;
            color: var(--text-dark);
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transition);
            display: inline-block;
            text-decoration: none;
        }

        .btn-secondary-custom:hover {
            background: #dee2e6;
            color: var(--text-dark);
        }

        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: flex-start;
            margin-top: 2rem;
        }

        .card-body-custom {
            padding: 2rem;
        }

        .info-box {
            background: var(--custom-maroon-subtle);
            border: 1px dashed var(--custom-maroon-light);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .info-box i {
            margin-right: 0.5rem;
            color: var(--custom-maroon);
        }

        .pelatihan-section {
            background: #f8f9fa;
            border: 2px dashed var(--custom-maroon);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .pelatihan-title {
            font-weight: 700;
            color: var(--custom-maroon);
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        /* CSS Updated for Items */
        .pelatihan-item {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.8rem;
            align-items: flex-start;
        }

        .pelatihan-item input[type="text"] {
            flex: 2;
        }

        .pelatihan-item input[type="number"] {
            flex: 0 0 120px;
        }

        .pdf-wrapper {
            flex: 1.5;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .pelatihan-item input[type="file"] {
            font-size: 0.85rem;
            padding: 0.6rem;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            width: 100%;
        }

        .current-file {
            font-size: 0.8rem;
            background: #eee;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .current-file a {
            text-decoration: none;
            color: var(--custom-maroon);
            font-weight: bold;
        }

        .pelatihan-item .btn-remove {
            margin-top: 5px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            width: 38px;
            height: 38px;
            line-height: 38px;
            text-align: center;
            padding: 0;
        }

        .pelatihan-item .btn-remove:hover {
            background: #c0392b;
        }

        .btn-add-pelatihan {
            background: var(--custom-maroon);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .btn-add-pelatihan:hover {
            background: var(--custom-maroon-light);
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card">
                <div class="card-header-custom">
                    <h2 class="card-header-title">
                        <i class="fas fa-edit"></i>
                        Edit Data Pegawai & Pelatihan
                    </h2>
                </div>

                <div class="card-body-custom">
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <strong>Terjadi kesalahan!</strong>
                            <ul style="margin: 0.5rem 0 0 0;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        Perbarui data pegawai. Upload file PDF baru hanya jika ingin mengganti file yang lama.
                    </div>

                    <form action="{{ route('pelatihan.update', $pelatihan->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-row-custom">
                            <div class="form-group">
                                <label for="nama" class="form-label"><i class="fas fa-user"></i> Nama</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                        id="nama" name="nama" placeholder="Masukkan nama"
                                        value="{{ old('nama', $pelatihan->nama) }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="jabatan" class="form-label"><i class="fas fa-briefcase"></i> Jabatan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                    <input type="text" class="form-control" id="jabatan" name="jabatan"
                                        placeholder="Masukkan jabatan" value="{{ old('jabatan', $pelatihan->jabatan) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-row-custom">
                            <div class="form-group">
                                <label for="unit" class="form-label"><i class="fas fa-building"></i> Unit/Ruang</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    <input type="text" class="form-control" id="unit" name="unit"
                                        placeholder="Masukkan unit" value="{{ old('unit', $pelatihan->unit) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="bidang" class="form-label"><i class="fas fa-layer-group"></i> Bidang</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                    <select id="bidang" name="bidang"
                                        class="form-select @error('bidang') is-invalid @enderror" required>
                                        <option value="">-- Pilih Bidang --</option>
                                        @php $bidang = old('bidang', $pelatihan->bidang); @endphp
                                        <option value="Keperawatan" {{ $bidang == 'Keperawatan' ? 'selected' : '' }}>
                                            Keperawatan</option>
                                        <option value="Pelayanan Medik"
                                            {{ $bidang == 'Pelayanan Medik' ? 'selected' : '' }}>Pelayanan Medik</option>
                                        <option value="Penunjang Klinik"
                                            {{ $bidang == 'Penunjang Klinik' ? 'selected' : '' }}>Penunjang Klinik</option>
                                        <option value="Penunjang Non Klinik"
                                            {{ $bidang == 'Penunjang Non Klinik' ? 'selected' : '' }}>Penunjang Non Klinik
                                        </option>
                                        <option value="Kepegawaian" {{ $bidang == 'Kepegawaian' ? 'selected' : '' }}>
                                            Kepegawaian</option>
                                        <option value="Perencanaan" {{ $bidang == 'Perencanaan' ? 'selected' : '' }}>
                                            Perencanaan</option>
                                        <option value="Keuangan" {{ $bidang == 'Keuangan' ? 'selected' : '' }}>Keuangan
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row-custom">
                            <div class="form-group">
                                <label for="status_pegawai" class="form-label"><i class="fas fa-id-card"></i> Status
                                    Pegawai</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                                    <select id="status_pegawai" name="status_pegawai"
                                        class="form-select @error('status_pegawai') is-invalid @enderror" required>
                                        <option value="">-- Pilih Status --</option>
                                        @php $status = old('status_pegawai', $pelatihan->status_pegawai); @endphp
                                        <option value="ASN" {{ $status == 'ASN' ? 'selected' : '' }}>ASN</option>
                                        <option value="KARYAWAN TETAP" {{ $status == 'KARYAWAN TETAP' ? 'selected' : '' }}>KARYAWAN TETAP</option>
                                        <option value="NON ASN" {{ $status == 'NON ASN' ? 'selected' : '' }}>NON ASN
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group conditional-fields">

                                <div id="pnsFields" style="display: none;">
                                    <label class="form-label"><i class="fas fa-key"></i> Detail Kepegawaian</label>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <input type="text" id="nip" name="nip" class="form-control"
                                            placeholder="NIP" value="{{ old('nip', $pelatihan->nip) }}">
                                        <input type="text" id="golongan" name="golongan" class="form-control"
                                            placeholder="Golongan" value="{{ old('golongan', $pelatihan->golongan) }}">

                                        <div id="wrapper_pangkat" style="width: 100%;">
                                            <input type="text" id="pangkat" name="pangkat" class="form-control"
                                                placeholder="Pangkat" value="{{ old('pangkat', $pelatihan->pangkat) }}">
                                        </div>
                                    </div>
                                </div>

                                <div id="nonPnsFields" style="display: none;">
                                    <label class="form-label"><i class="fas fa-key"></i> NIRP</label>
                                    <div class="input-group">
                                        <input type="text" id="nirp" name="nirp" class="form-control"
                                            placeholder="Masukkan NIRP" value="{{ old('nirp', $pelatihan->nirp) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pelatihan-section">
                            <div class="pelatihan-title"><i class="fas fa-graduation-cap"></i> Pelatihan Dasar</div>
                            <div id="pelatihanDasarContainer">
                                @php
                                    $oldDasar = old('pelatihan_dasar'); // Cek jika ada old input (validasi gagal)
                                    $dataDasar = $oldDasar ? [] : $pelatihan->pelatihan_dasar ?? [];

                                    // Jika validasi gagal, kita harus reconstruct array dari old inputs
                                    if ($oldDasar) {
                                        foreach ($oldDasar as $key => $val) {
                                            $dataDasar[] = [
                                                'nama' => $val,
                                                'tahun' => old('pelatihan_tahun_dasar')[$key] ?? '',
                                                'file' => old('pelatihan_existing_file_dasar')[$key] ?? null,
                                            ];
                                        }
                                    }
                                @endphp

                                @if (empty($dataDasar))
                                    {{-- Tampilkan 1 baris kosong jika data kosong --}}
                                    <div class="pelatihan-item">
                                        <input type="text" class="form-control" name="pelatihan_dasar[]"
                                            placeholder="Nama Pelatihan">
                                        <input type="number" class="form-control" name="pelatihan_tahun_dasar[]"
                                            placeholder="Tahun">
                                        <div class="pdf-wrapper">
                                            <input type="file" class="form-control" name="pelatihan_file_dasar[]"
                                                accept=".pdf">
                                            <input type="hidden" name="pelatihan_existing_file_dasar[]" value="">
                                        </div>
                                        <button type="button" class="btn-remove" onclick="removeRow(this)"><i
                                                class="fas fa-trash"></i></button>
                                    </div>
                                @else
                                    @foreach ($dataDasar as $item)
                                        <div class="pelatihan-item">
                                            <input type="text" class="form-control" name="pelatihan_dasar[]"
                                                placeholder="Nama Pelatihan" value="{{ $item['nama'] ?? '' }}">

                                            <input type="number" class="form-control" name="pelatihan_tahun_dasar[]"
                                                placeholder="Tahun" value="{{ $item['tahun'] ?? '' }}">

                                            <div class="pdf-wrapper">
                                                <input type="file" class="form-control" name="pelatihan_file_dasar[]"
                                                    accept=".pdf">

                                                @php $existingFile = $item['file'] ?? null; @endphp
                                                <input type="hidden" name="pelatihan_existing_file_dasar[]"
                                                    value="{{ $existingFile }}">

                                                @if ($existingFile)
                                                    <div class="current-file">
                                                        <i class="fas fa-check-circle"></i> File saat ini:
                                                        <a href="{{ Storage::url($existingFile) }}" target="_blank">Lihat
                                                            PDF</a>
                                                    </div>
                                                @endif
                                            </div>

                                            <button type="button" class="btn-remove" onclick="removeRow(this)"><i
                                                    class="fas fa-trash"></i></button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn-add-pelatihan mt-2"
                                onclick="addPelatihan('pelatihanDasarContainer', 'dasar')"><i class="fas fa-plus"></i>
                                Tambah</button>
                        </div>

                        <div class="pelatihan-section">
                            <div class="pelatihan-title"><i class="fas fa-chart-line"></i> Pelatihan Peningkatan
                                Kompetensi</div>
                            <div id="pelatihanKompetensiContainer">
                                @php
                                    $oldKomp = old('pelatihan_kompetensi');
                                    $dataKomp = $oldKomp ? [] : $pelatihan->pelatihan_peningkatan_kompetensi ?? [];

                                    if ($oldKomp) {
                                        foreach ($oldKomp as $key => $val) {
                                            $dataKomp[] = [
                                                'nama' => $val,
                                                'tahun' => old('pelatihan_tahun_kompetensi')[$key] ?? '',
                                                'file' => old('pelatihan_existing_file_kompetensi')[$key] ?? null,
                                            ];
                                        }
                                    }
                                @endphp

                                @if (empty($dataKomp))
                                    <div class="pelatihan-item">
                                        <input type="text" class="form-control" name="pelatihan_kompetensi[]"
                                            placeholder="Nama Pelatihan">
                                        <input type="number" class="form-control" name="pelatihan_tahun_kompetensi[]"
                                            placeholder="Tahun">
                                        <div class="pdf-wrapper">
                                            <input type="file" class="form-control" name="pelatihan_file_kompetensi[]"
                                                accept=".pdf">
                                            <input type="hidden" name="pelatihan_existing_file_kompetensi[]"
                                                value="">
                                        </div>
                                        <button type="button" class="btn-remove" onclick="removeRow(this)"><i
                                                class="fas fa-trash"></i></button>
                                    </div>
                                @else
                                    @foreach ($dataKomp as $item)
                                        <div class="pelatihan-item">
                                            <input type="text" class="form-control" name="pelatihan_kompetensi[]"
                                                placeholder="Nama Pelatihan" value="{{ $item['nama'] ?? '' }}">

                                            <input type="number" class="form-control"
                                                name="pelatihan_tahun_kompetensi[]" placeholder="Tahun"
                                                value="{{ $item['tahun'] ?? '' }}">

                                            <div class="pdf-wrapper">
                                                <input type="file" class="form-control"
                                                    name="pelatihan_file_kompetensi[]" accept=".pdf">

                                                @php $existingFile = $item['file'] ?? null; @endphp
                                                <input type="hidden" name="pelatihan_existing_file_kompetensi[]"
                                                    value="{{ $existingFile }}">

                                                @if ($existingFile)
                                                    <div class="current-file">
                                                        <i class="fas fa-check-circle"></i> File saat ini:
                                                        <a href="{{ Storage::url($existingFile) }}" target="_blank">Lihat
                                                            PDF</a>
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="button" class="btn-remove" onclick="removeRow(this)"><i
                                                    class="fas fa-trash"></i></button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn-add-pelatihan mt-2"
                                onclick="addPelatihan('pelatihanKompetensiContainer', 'kompetensi')"><i
                                    class="fas fa-plus"></i> Tambah</button>
                        </div>

                        <div class="button-group">
                            <button type="submit" class="btn-maroon"><i class="fas fa-save"></i> Perbarui Data</button>
                            <a href="{{ route('pelatihan.index') }}" class="btn-secondary-custom"><i
                                    class="fas fa-arrow-left"></i> Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addPelatihan(containerId, type) {
            const container = document.getElementById(containerId);
            const item = document.createElement('div');
            item.className = 'pelatihan-item';

            // Note: hidden input name disesuaikan agar update controller bisa baca (pelatihan_existing_file_dasar / kompetensi)
            const existingFileName = type === 'dasar' ? 'pelatihan_existing_file_dasar[]' :
                'pelatihan_existing_file_kompetensi[]';

            item.innerHTML = `
                <input type="text" class="form-control" name="pelatihan_${type}[]" placeholder="Nama Pelatihan">
                <input type="number" class="form-control" name="pelatihan_tahun_${type}[]" placeholder="Tahun">
                <div class="pdf-wrapper">
                    <input type="file" class="form-control" name="pelatihan_file_${type}[]" accept=".pdf">
                    <input type="hidden" name="${existingFileName}" value="">
                </div>
                <button type="button" class="btn-remove" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
            `;
            container.appendChild(item);
        }

        function removeRow(button) {
            button.parentElement.remove();
        }

        // Logic Toggle Status (Sama seperti Create)
        function toggleStatusFields() {
            const sel = document.getElementById('status_pegawai');
            const pnsContainer = document.getElementById('pnsFields');
            const nonPnsContainer = document.getElementById('nonPnsFields');
            const wrapperPangkat = document.getElementById('wrapper_pangkat');

            const nip = document.getElementById('nip');
            const golongan = document.getElementById('golongan');
            const pangkat = document.getElementById('pangkat');
            const nirp = document.getElementById('nirp');

            if (!sel) return;
            const val = sel.value;

            pnsContainer.style.display = 'none';
            nonPnsContainer.style.display = 'none';
            wrapperPangkat.style.display = 'none';

            if (nip) nip.removeAttribute('required');
            if (golongan) golongan.removeAttribute('required');
            if (pangkat) pangkat.removeAttribute('required');
            if (nirp) nirp.removeAttribute('required');

            if (val === 'ASN') {
                pnsContainer.style.display = 'block';
                wrapperPangkat.style.display = 'block';
                if (nip) nip.setAttribute('required', 'required');
                if (golongan) golongan.setAttribute('required', 'required');
                if (pangkat) pangkat.setAttribute('required', 'required');
            } else if (val === 'KARYAWAN TETAP') {
                pnsContainer.style.display = 'block';
                wrapperPangkat.style.display = 'none';
                if (nip) nip.setAttribute('required', 'required');
                if (golongan) golongan.setAttribute('required', 'required');
            } else if (val === 'NON ASN') {
                nonPnsContainer.style.display = 'block';
                if (nirp) nirp.setAttribute('required', 'required');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const sel = document.getElementById('status_pegawai');
            if (sel) {
                sel.addEventListener('change', toggleStatusFields);
                toggleStatusFields();
            }
        });
    </script>
@endsection
