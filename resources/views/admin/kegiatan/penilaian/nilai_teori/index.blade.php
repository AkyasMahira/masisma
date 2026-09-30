@extends('layouts.app')
@section('title', 'Input Nilai Teori')

@section('content')
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-dark: #5c0d10;
            --custom-maroon-light: #fef1f2;
            --card-radius: 16px;
            --border-color: #e2e8f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --bg-surface: #ffffff;
            --bg-body: #f8fafc;
        }

        /* Hilangkan panah (spinner) pada input number agar lebih bersih */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }

        .page-header { background: var(--bg-surface); border-radius: var(--card-radius); padding: 24px 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 24px; border-left: 5px solid var(--custom-maroon); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .custom-card { background: var(--bg-surface); border-radius: var(--card-radius); box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid var(--border-color); overflow: hidden; margin-bottom: 24px; }
        
        /* Filter Section */
        .filter-section { background: var(--bg-surface); padding: 20px 24px; border-bottom: 1px solid var(--border-color); }
        .filter-input-group { position: relative; }
        .filter-input-group i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
        .form-control, .form-select { border-radius: 12px; border: 1px solid var(--border-color); font-size: 0.95rem; color: var(--text-dark); background: var(--bg-body); padding: 12px 16px; font-weight: 500; transition: 0.3s; }
        .filter-input-group .form-control { padding-left: 45px; }
        .form-control:focus, .form-select:focus { border-color: var(--custom-maroon); box-shadow: 0 0 0 4px var(--custom-maroon-light); background: #fff; outline: none; }

        /* Buttons */
        .btn-theme { background-color: var(--custom-maroon); color: white; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 700; transition: 0.2s; font-size: 0.95rem; display: inline-flex; justify-content: center; align-items: center; gap: 8px; }
        .btn-theme:hover { background-color: var(--custom-maroon-dark); color: white; transform: translateY(-1px); }
        .btn-outline-theme { background-color: #fff; color: var(--text-dark); border: 1px solid var(--border-color); padding: 12px 24px; border-radius: 12px; font-weight: 700; transition: 0.2s; font-size: 0.95rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-outline-theme:hover { background-color: var(--bg-body); border-color: var(--text-muted); color: var(--text-dark); }
        .btn-icon-only { padding: 12px 16px; }

        /* Table */
        .table-custom { margin-bottom: 0; }
        .table-custom thead th { background-color: var(--bg-body); color: var(--text-muted); font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 16px 24px; border-bottom: 1px solid var(--border-color); border-top: none; white-space: nowrap; }
        .table-custom tbody td { padding: 16px 24px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .table-custom tbody tr { transition: 0.2s; }
        .table-custom tbody tr:hover { background-color: var(--custom-maroon-light); }

        .peserta-name { font-weight: 700; font-size: 1rem; color: var(--text-dark); margin-bottom: 4px; }
        .badge-instansi { background-color: var(--bg-body); border: 1px solid var(--border-color); color: var(--text-muted); font-weight: 600; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; }

        /* Row Inputs & Save Button */
        .input-nilai { width: 100px; text-align: center; font-weight: 800; font-size: 1.1rem; color: var(--custom-maroon); background: #fff; margin: 0 auto; }
        .input-nilai::placeholder { font-weight: 500; color: #cbd5e1; font-size: 0.95rem; }
        
        .btn-save-row { background: var(--custom-maroon); color: #fff; border: none; border-radius: 10px; padding: 10px 20px; font-weight: 700; font-size: 0.85rem; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; width: 100%; justify-content: center; }
        .btn-save-row:hover { background: var(--custom-maroon-dark); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(124, 19, 22, 0.15); }
    </style>

    <div class="row">
        <div class="col-12">

            <!-- Header -->
            <div class="page-header">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-journal-check me-2" style="color: var(--custom-maroon);"></i> Input Nilai Teori</h3>
                    <p class="mb-0 text-muted fw-semibold">{{ $kegiatan->nama_kegiatan }} &middot; Evaluasi Pre-Test &amp; Post-Test</p>
                </div>
                <a href="{{ route('admin.kegiatan.peserta.index', $kegiatan->id) }}" class="btn-outline-theme shadow-sm">
                    <i class="bi bi-arrow-left"></i> Kembali ke Peserta
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success shadow-sm border-0 mb-4 rounded-4 d-flex align-items-center p-3">
                    <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                    <div class="fw-bold">{{ session('success') }}</div>
                </div>
            @endif

            <div class="custom-card">
                <!-- Filter Section -->
                <div class="filter-section">
                    <form method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Cari Peserta</label>
                            <div class="filter-input-group">
                                <i class="bi bi-search"></i>
                                <input type="text" name="search" class="form-control" placeholder="Ketik nama peserta..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Filter Instansi</label>
                            <select name="instansi_id" class="form-select">
                                <option value="">Semua Instansi</option>
                                @foreach($instansi as $ins)
                                    <option value="{{ $ins->id }}" {{ request('instansi_id') == $ins->id ? 'selected' : '' }}>{{ $ins->nama_instansi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn-theme flex-grow-1"><i class="bi bi-funnel-fill"></i> Terapkan</button>
                            @if(request('search') || request('instansi_id'))
                                <a href="{{ route('admin.kegiatan.penilaian.teori.index', $kegiatan->id) }}" class="btn-outline-theme btn-icon-only text-danger border-danger-subtle bg-danger-subtle" title="Reset Filter">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Table Section -->
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th>Informasi Peserta</th>
                                <th width="150" class="text-center">Pre-Test</th>
                                <th width="150" class="text-center">Post-Test</th>
                                <th width="140" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peserta as $p)
                            <tr>
                                <td>
                                    <div class="peserta-name">{{ $p->nama_lengkap_gelar }}</div>
                                    <span class="badge-instansi"><i class="bi bi-building me-1"></i> {{ $p->instansi->nama_instansi ?? 'Internal RS' }}</span>
                                    
                                    {{-- FORM TERSEMBUNYI UNTUK ROW INI --}}
                                    <form id="form-nilai-{{ $p->id }}" action="{{ route('admin.kegiatan.penilaian.teori.update', [$kegiatan->id, $p->id]) }}" method="POST">
                                        @csrf
                                    </form>
                                </td>
                                
                                <td class="text-center">
                                    <input form="form-nilai-{{ $p->id }}" type="number" name="nilai_pretest" class="form-control input-nilai shadow-sm" value="{{ optional($p->nilaiTeori)->nilai_pretest }}" min="0" max="100" placeholder="Belum">
                                </td>
                                
                                <td class="text-center">
                                    <input form="form-nilai-{{ $p->id }}" type="number" name="nilai_posttest" class="form-control input-nilai shadow-sm" value="{{ optional($p->nilaiTeori)->nilai_posttest }}" min="0" max="100" placeholder="Belum">
                                </td>
                                
                                <td>
                                    <button form="form-nilai-{{ $p->id }}" type="submit" class="btn-save-row">
                                        <i class="bi bi-cloud-arrow-up-fill"></i> Simpan
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="bi bi-people fs-1 text-muted opacity-25 d-block mb-3"></i>
                                    <span class="text-muted fw-medium">Tidak ada peserta yang ditemukan.</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($peserta->hasPages())
                    <div class="p-4 border-top">
                        {{ $peserta->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection