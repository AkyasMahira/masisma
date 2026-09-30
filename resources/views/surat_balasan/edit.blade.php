@extends('layouts.app')

@section('title', 'Edit Surat Balasan')
@section('page-title', 'Edit Surat Balasan')

@section('content')

    {{-- 1. PHP Logic: Tetap dipertahankan --}}
    @php
        $dates = explode(' s/d ', $suratBalasan->lama_berlaku);
        $startDate = $dates[0] ?? date('Y-m-d');
        $endDate = $dates[1] ?? date('Y-m-d');
        
        $selectedData = $suratBalasan->data_dibutuhkan ?? [];
        // $selectedData = is_string($selectedData) ? json_decode($selectedData, true) : $selectedData;
    @endphp

    {{-- Library Choices.js --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    {{-- 2. Style CSS: Disamakan dengan Kode 2 --}}
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #95a5a6;
            --card-radius: 16px;
            --transition: 0.3s ease;
        }

        /* --- Card Styling --- */
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

        /* --- Form Styling --- */
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
            border-color: #dee2e6;
        }

        .form-control, .form-select {
            border-left: none;
            border-radius: 0 10px 10px 0;
            padding: 0.7rem 1rem;
            border-color: #dee2e6;
            box-shadow: none !important;
            transition: border-color 0.2s;
            color: var(--text-dark);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--custom-maroon-light);
        }

        /* --- Choices JS Custom Overrides agar seragam --- */
        .choices__inner {
            background-color: #fff;
            border-radius: 10px; /* Samakan radius */
            border: 1px solid #dee2e6;
            min-height: 44px;
        }
        .choices__list--multiple .choices__item {
            background-color: var(--custom-maroon);
            border: 1px solid var(--custom-maroon-light);
        }
        .choices:focus-within .choices__inner {
            border-color: var(--custom-maroon-light);
        }

        /* --- Buttons --- */
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

        .btn-light-custom {
            background: #fff;
            border: 1px solid #dee2e6;
            color: var(--text-dark);
            border-radius: 50px;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
        }
        .btn-light-custom:hover {
            background: #f8f9fa;
            color: var(--custom-maroon);
        }

        /* Animation */
        .animate-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0; transform: translateY(20px);
        }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="row justify-content-center animate-up">
        <div class="col-lg-10">
            <div class="form-card">
                
                {{-- Header Baru (Seragam Kode 2) --}}
                <div class="card-header-custom">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Surat Balasan</h4>
                    <p class="mb-0 small opacity-75">Formulir pembaruan data surat balasan mahasiswa.</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    
                    {{-- Alert Error --}}
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 shadow-sm mb-4">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('surat-balasan.update', $suratBalasan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Bagian 1: Identitas --}}
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <h6 class="text-muted fw-bold border-bottom pb-2 mb-3" style="font-size: 0.85rem;">
                                    IDENTITAS MAHASISWA
                                </h6>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Nama Mahasiswa <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" name="nama_mahasiswa" class="form-control" 
                                           value="{{ old('nama_mahasiswa', $suratBalasan->nama_mahasiswa) }}" placeholder="Nama Lengkap" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">NIM <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                                    <input type="text" name="nim" class="form-control" 
                                           value="{{ old('nim', $suratBalasan->nim) }}" placeholder="Nomor Induk" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">No WA / Telepon <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                                    <input type="text" name="wa_mahasiswa" class="form-control" 
                                           value="{{ old('wa_mahasiswa', $suratBalasan->wa_mahasiswa) }}" placeholder="08..." required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-mortarboard"></i></span>
                                    <input type="text" name="prodi" class="form-control" 
                                           value="{{ old('prodi', $suratBalasan->prodi) }}" placeholder="Informatika" required>
                                </div>
                            </div>
                        </div>

                        {{-- Bagian 2: Detail Surat --}}
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <h6 class="text-muted fw-bold border-bottom pb-2 mb-3" style="font-size: 0.85rem;">
                                    DETAIL KEPERLUAN
                                </h6>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Keperluan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-text-paragraph"></i></span>
                                    <input type="text" name="keperluan" class="form-control" 
                                           value="{{ old('keperluan', $suratBalasan->keperluan) }}" placeholder="Contoh: Penelitian Skripsi..." required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Universitas / Instansi Tujuan (MOU) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                                    <select name="mou_id" class="form-select" required>
                                        <option value="">-- Pilih Instansi --</option>
                                        @foreach ($mous as $m)
                                            <option value="{{ $m->id }}" {{ (old('mou_id', $suratBalasan->mou_id) == $m->id) ? 'selected' : '' }}>
                                                {{ $m->nama_instansi ?? $m->nama_universitas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                    <input type="date" class="form-control" name="tanggal_mulai" 
                                           value="{{ old('tanggal_mulai', $startDate) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                    <input type="date" class="form-control" name="tanggal_selesai" 
                                           value="{{ old('tanggal_selesai', $endDate) }}" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Data Yang Dibutuhkan <span class="text-danger">*</span></label>
                                {{-- Choices.js tidak menggunakan input-group wrapper agar style library tidak rusak, tapi CSS di-override di atas --}}
                                <select id="data_dibutuhkan" name="data_dibutuhkan[]" class="form-select" multiple required>
                                    <option value="OBAT" {{ (is_array($selectedData) && in_array('OBAT', $selectedData)) ? 'selected' : '' }}>OBAT</option>
                                    <option value="SOAP (RANAP)" {{ (is_array($selectedData) && in_array('SOAP (RANAP)', $selectedData)) ? 'selected' : '' }}>SOAP (RANAP)</option>
                                    <option value="RADIOLOGI" {{ (is_array($selectedData) && in_array('RADIOLOGI', $selectedData)) ? 'selected' : '' }}>RADIOLOGI</option>
                                    <option value="SOAP (RALAN)" {{ (is_array($selectedData) && in_array('SOAP (RALAN)', $selectedData)) ? 'selected' : '' }}>SOAP (RALAN)</option>
                                    <option value="LAB" {{ (is_array($selectedData) && in_array('LAB', $selectedData)) ? 'selected' : '' }}>LAB</option>
                                </select>
                                <small class="text-muted fst-italic">*Dapat memilih lebih dari satu.</small>
                            </div>
                        </div>

                        {{-- Footer Buttons (Seragam Kode 2) --}}
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                            <a href="{{ route('surat-balasan.index') }}" class="btn btn-light-custom shadow-sm">
                                <i class="bi bi-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-maroon">
                                Simpan Perubahan <i class="bi bi-check-lg ms-2"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Inisialisasi Choices.js --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.getElementById('data_dibutuhkan');
            const choices = new Choices(element, {
                removeItemButton: true,
                searchEnabled: true,
                shouldSort: false,
                placeholder: true,
                placeholderValue: 'Pilih Data...',
                itemSelectText: 'Tekan untuk memilih',
            });
        });
    </script>
@endsection