@extends('layouts.app')

@section('title', 'Tambah Mahasiswa')
@section('page-title', 'Tambah Mahasiswa')

@section('content')
    <style>
        :root {
            --maroon: #7c1316;
            --maroon-light: #9d2a2e;
            --text-dark: #222;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 10px 12px;
            border: 1px solid #ddd;
            transition: border-color 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--maroon-light);
            box-shadow: 0 0 0 0.15rem rgba(124, 19, 22, 0.25);
        }

        .btn-maroon {
            background-color: var(--maroon);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 18px;
            transition: all 0.2s ease;
        }

        .btn-maroon:hover {
            background-color: var(--maroon-light);
            transform: translateY(-1px);
        }

        .btn-maroon:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .card-body {
            padding: 2rem;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem;
            }
        }
    </style>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4 fw-bold" style="color: var(--maroon);">Form Tambah Mahasiswa</h4>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('mahasiswa.store') }}" method="POST" id="form-mahasiswa">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nama Mahasiswa <span class="text-danger">*</span></label>
                            <input type="text" name="nm_mahasiswa" class="form-control" value="{{ old('nm_mahasiswa') }}"
                                placeholder="Masukkan nama lengkap" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Universitas</label>
                            <input type="text" name="univ_asal" class="form-control" value="{{ old('univ_asal') }}"
                                placeholder="Masukkan universitas asal">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Program Studi</label>
                            <input type="text" name="prodi" class="form-control" value="{{ old('prodi') }}"
                                placeholder="Masukkan program studi">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ruangan</label>
                            <select id="ruangan_id" name="ruangan_id" class="form-select js-choices">
                                <option value="">-- Pilih Ruangan (Opsional) --</option>
                                @foreach ($ruangans as $r)
                                    <option value="{{ $r->id }}" {{ old('ruangan_id') == $r->id ? 'selected' : '' }}>
                                        {{ $r->nm_ruangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Info Kuota Real-time -->
                        <div id="ruangan-info" class="alert alert-info" style="display: none;">
                            <small>
                                <strong id="info-nama"></strong><br>
                                Kuota Total: <span id="info-kuota-total">-</span><br>
                                Tersedia: <span id="info-tersedia" style="font-weight: bold; color: #28a745;">-</span><br>
                                Terisi: <span id="info-terisi">-</span><br>
                                Status: <span id="info-status">-</span>
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" class="form-control"
                                value="{{ old('tanggal_mulai', now()->toDateString()) }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Tanggal Berakhir <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_berakhir" class="form-control"
                                value="{{ old('tanggal_berakhir') }}" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn-maroon" id="submit-btn">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ruanganSelect = document.getElementById('ruangan_id');
            const ruanganInfo = document.getElementById('ruangan-info');
            const submitBtn = document.getElementById('submit-btn');
            let selectedRuanganFull = false;

            ruanganSelect.addEventListener('change', function() {
                const ruanganId = this.value;

                if (!ruanganId) {
                    ruanganInfo.style.display = 'none';
                    selectedRuanganFull = false;
                    submitBtn.disabled = false;
                    return;
                }

                // Fetch kuota tersedia real-time
                fetch(`/mahasiswa/ruangan-info/${ruanganId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('info-nama').textContent = data.nm_ruangan;
                        document.getElementById('info-kuota-total').textContent = data.kuota_total;
                        document.getElementById('info-tersedia').textContent = data.tersedia;
                        document.getElementById('info-terisi').textContent = data.terisi;

                        // Tampilkan status dengan warna
                        const statusEl = document.getElementById('info-status');
                        if (data.tersedia <= 0) {
                            statusEl.textContent = '❌ Penuh';
                            statusEl.style.color = '#dc3545';
                            document.getElementById('info-tersedia').style.color = '#dc3545';
                            selectedRuanganFull = true;
                            submitBtn.disabled = true;
                        } else {
                            statusEl.textContent = '✅ Tersedia';
                            statusEl.style.color = '#28a745';
                            document.getElementById('info-tersedia').style.color = '#28a745';
                            selectedRuanganFull = false;
                            submitBtn.disabled = false;
                        }

                        ruanganInfo.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        ruanganInfo.style.display = 'none';
                        selectedRuanganFull = false;
                        submitBtn.disabled = false;
                    });
            });

            // Validasi form sebelum submit
            document.getElementById('form-mahasiswa').addEventListener('submit', function(e) {
                if (selectedRuanganFull) {
                    e.preventDefault();
                    alert('Ruangan yang dipilih sudah penuh. Silakan pilih ruangan lain.');
                }
            });
        });
    </script>
@endsection
