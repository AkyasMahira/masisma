@extends('layouts.app')



@section('content')
    {{--
      =====================================================
      STYLE KUSTOM
      =====================================================
    --}}
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #95a5a6;
            --card-radius: 12px;
            --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.05);
            --transition: 0.3s ease;
        }

        .page-header-wrapper {
            background: #fff;
            border-radius: var(--card-radius);
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 2rem;
            border-left: 5px solid var(--custom-maroon);
            position: relative;
            z-index: 99;
            overflow: visible;
        }

        .filter-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            margin-bottom: 1.5rem;
            border: 1px solid #f0f0f0;
            position: relative;
            z-index: 50;
        }

        .filter-header {
            background: var(--custom-maroon-subtle);
            color: var(--custom-maroon);
            padding: 1rem 1.5rem;
            border-radius: var(--card-radius) var(--card-radius) 0 0;
            font-weight: 600;
        }

        .btn-maroon {
            background-color: var(--custom-maroon);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-maroon:hover {
            background-color: var(--custom-maroon-light);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(124, 19, 22, 0.3);
        }

        .btn-tool {
            background: #fff;
            border: 1px solid #e0e0e0;
            color: var(--text-dark);
            border-radius: 8px;
            padding: 8px 12px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-tool:hover,
        .btn-tool:focus {
            background: #f8f9fa;
            border-color: var(--custom-maroon);
            outline: none;
        }

        .custom-table-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            border: none;
        }

        .table {
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .table thead th {
            background-color: var(--custom-maroon);
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            white-space: nowrap; /* Mencegah header turun baris */
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: #555;
            border-bottom: 1px solid #f0f0f0;
        }

        .table-hover tbody tr:hover {
            background-color: #fff5f6;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            transition: var(--transition);
            background: transparent;
            border: 1px solid transparent;
        }

        .action-btn:hover {
            background: var(--custom-maroon-subtle);
            color: var(--custom-maroon);
        }

        .action-btn.delete:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        .animate-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>


@if(session('akun_instansi'))
    @php $ak = session('akun_instansi'); $pesanAk = "Akun portal Sindikat RSUD SLG untuk " . $ak['nama'] . "\nLogin: " . url('/login') . "\nUsername: " . $ak['username'] . "\nPassword: " . $ak['password']; @endphp
    <div class="alert alert-success border-0 shadow-sm" style="border-left:5px solid #15803d !important; border-radius:10px;">
        <h6 class="fw-bold mb-2"><i class="bi bi-check-circle me-1"></i> {{ $ak['judul'] ?? 'Akun Instansi' }} — {{ $ak['nama'] }}</h6>
        <div class="d-flex flex-wrap gap-4 align-items-end">
            <div><span class="text-muted small d-block">Username (login)</span><code style="font-size:.95rem;">{{ $ak['username'] }}</code></div>
            <div><span class="text-muted small d-block">Password</span><code style="font-size:.95rem;">{{ $ak['password'] }}</code></div>
            <button type="button" class="btn btn-sm btn-success" data-pesan="{{ $pesanAk }}" onclick="salinPesanAkun(this)"><i class="bi bi-clipboard me-1"></i>Salin pesan untuk PIC</button>
        </div>
        <div class="small text-muted mt-2">Kredensial ini juga bisa dilihat lagi kapan saja di kolom <b>Akun Portal</b>.</div>
    </div>
@endif

@php
    $mintaReset = \App\Models\User::where('role', 'instansi')->whereNotNull('reset_diminta_at')->with('mou')->orderByDesc('reset_diminta_at')->get();
@endphp
@if($mintaReset->count())
    <div class="alert border-0 shadow-sm" style="background:#fffbeb;border-left:5px solid #f59e0b !important;border-radius:10px;">
        <h6 class="fw-bold mb-2" style="color:#92400e;"><i class="bi bi-key me-1"></i> {{ $mintaReset->count() }} instansi meminta reset password</h6>
        @foreach($mintaReset as $mr)
            <div class="d-flex flex-wrap align-items-center gap-2 small mb-1">
                <b>{{ optional($mr->mou)->nama_instansi ?: $mr->name }}</b> <code>{{ $mr->email }}</code>
                <span class="text-muted">· {{ \Carbon\Carbon::parse($mr->reset_diminta_at)->diffForHumans() }}</span>
                @if($mr->mou_id)
                <form action="{{ route('mou.reset_akun', $mr->mou_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Buat password baru untuk {{ $mr->email }}?');">
                    @csrf<button class="btn btn-sm btn-warning py-0 px-2 fw-semibold"><i class="bi bi-arrow-repeat me-1"></i>Reset sekarang</button>
                </form>
                @endif
            </div>
        @endforeach
    </div>
@endif

<div class="d-flex justify-content-between align-items-center"
     style="background: white; border-radius: 8px; border-left: 5px solid #7c1316; padding: 20px; margin-bottom: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <div>
        <h4 class="fw-bold mb-0 text-dark">Data MOU</h4>
        <small class="text-muted">Kelola data Memorandum of Understanding dengan universitas.</small>
    </div>
       <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Tombol Tools (Dropdown) --}}
            <div class="dropdown position-relative">
                <button class="btn btn-tool shadow-sm" type="button" id="toolsBtn" onclick="toggleTools(event)">
                    <i class="bi bi-gear-fill text-secondary"></i> Tools
                </button>
                <div id="toolsDropdownMenu" class="dropdown-menu dropdown-menu-right shadow-sm border-0"
                    style="border-radius: 12px; position: absolute; right: 0; top: 110%; z-index: 2000; display: none; min-width: 200px; background: white;">
                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="copyPublicMouUrl(); closeTools();">
                        <i class="bi bi-link-45deg text-info me-2"></i> Salin URL
                    </a>
                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="copyMOUUrl(); closeTools();">
    <i class="bi bi-clipboard text-primary me-2"></i> Salin URL MOU
</a>

<script>
    function copyMOUUrl() {
        const url = 'https://sindikat-rsudslg.kedirikab.go.id/mou-publik';

        // --- Cara 1: Clipboard API (browser modern) ---
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url)
                .then(() => {
                    alert('URL berhasil disalin!');
                })
                .catch(() => {
                    fallbackCopy(url);
                });
        } 
        // --- Cara 2: Fallback (pasti jalan) ---
        else {
            fallbackCopy(url);
        }
    }

    function fallbackCopy(text) {
        const tempInput = document.createElement('input');
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); 
        document.execCommand('copy');
        document.body.removeChild(tempInput);

        alert('URL berhasil disalin!');
    }
</script>

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="exportMOU(); closeTools();">
                        <i class="bi bi-file-earmark-excel text-success me-2"></i> Export Excel
                    </a>
                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="exportAkunInstansi(); closeTools();">
                        <i class="bi bi-person-lines-fill text-danger me-2"></i> Tarik Data Akun Instansi
                    </a>
                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="downloadTemplateMOU(); closeTools();">
                        <i class="bi bi-download text-primary me-2"></i> Template
                    </a>
                    <div class="dropdown-divider"></div>
                    <label class="dropdown-item py-2 mb-0" style="cursor: pointer;">
                        <i class="bi bi-upload text-warning me-2"></i> Import Excel
                        <input type="file" id="fileImportMOU" style="display: none" accept=".xlsx,.xls"
                            onchange="closeTools(); importMOU(this);">
                    </label>
                </div>
            </div>
            {{-- Tombol Tambah Baru --}}
            <a href="{{ route('mou.create') }}" class="btn btn-maroon shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i> MOU Baru
            </a>
        </div>
</div>
    {{-- 2. FILTER CARD --}}
    <div class="filter-card animate-up" style="animation-delay: 0.1s;">
        <div class="filter-header">
            <i class="bi bi-funnel-fill me-2"></i> Filter & Pencarian
        </div>
        <div class="card-body p-4">
            <form id="filterForm" method="GET" action="{{ route('mou.index') }}">
                <div class="row">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="small text-muted fw-bold text-uppercase">Cari Nama Instansi</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light border-0 border-end-0"><i
                                    class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-0" name="search"
                                placeholder="Nama instansi..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="small text-muted fw-bold text-uppercase">Dari Tanggal</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light border-0 border-end-0"><i
                                    class="bi bi-calendar-plus"></i></span>
                            <input type="date" class="form-control bg-light border-0" name="tanggal_mulai"
                                value="{{ request('tanggal_mulai') }}">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="small text-muted fw-bold text-uppercase">Sampai Tanggal</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light border-0 border-end-0"><i
                                    class="bi bi-calendar-check"></i></span>
                            <input type="date" class="form-control bg-light border-0" name="tanggal_selesai"
                                value="{{ request('tanggal_selesai') }}">
                        </div>
                    </div>
                    <div class="col-md-12 d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-maroon shadow-sm">Terapkan</button>
                        <a href="{{ route('mou.index') }}" class="btn btn-light border shadow-sm" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. TABEL DATA --}}
    <div class="custom-table-card animate-up" style="animation-delay: 0.2s;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th>Instansi</th>
                        {{-- KOLOM BARU KHUSUS NOMOR PKS --}}
                        <th style="min-width: 180px;">Nomor PKS</th>
                        <th>Jenis</th>
                        <th>Durasi</th>
                        <th>Dokumen</th>
                        <th style="min-width: 200px;">Rencana Kerja Sama</th>
                        <th style="min-width: 230px;">Akun Portal</th>
                        <th class="text-center" width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mous as $index => $mou)
                        <tr>
                            <td class="text-center text-muted fw-bold">
                                {{ ($mous->currentPage() - 1) * $mous->perPage() + $index + 1 }}
                            </td>
                            <td>
                                <span class="fw-bold text-dark d-block">{{ $mou->nama_instansi }}</span>
                                @if ($mou->alamat_instansi)
                                    <small class="d-block text-muted mt-1">{{ $mou->alamat_instansi }}</small>
                                @endif
                                @if ($mou->nama_pic_instansi)
                                    <small class="d-block text-muted text-nowrap mt-1">
                                        <i class="bi bi-person-circle me-1"></i> {{ $mou->nama_pic_instansi }}
                                        @if ($mou->nomor_kontak_pic)
                                            <br><i class="bi bi-telephone me-1"></i> {{ $mou->nomor_kontak_pic }}
                                        @endif
                                    </small>
                                @endif
                            </td>

                            {{-- ==========================================
                                 KOLOM BARU: NOMOR PKS
                                 ========================================== --}}
                            <td class="align-top">
                                @if ($mou->no_pks)
                                    <div class="mb-2">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill mb-1" style="font-size: 0.65rem;">INTERNAL</span>
                                        <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                            {{ $mou->no_pks }}
                                        </div>
                                    </div>
                                @endif
                                @if ($mou->no_pks_instansi)
                                    <div class="mb-1">
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill mb-1" style="font-size: 0.65rem;">INSTANSI</span>
                                        <div class="text-muted" style="font-size: 0.85rem;">
                                            {{ $mou->no_pks_instansi }}
                                        </div>
                                    </div>
                                @endif
                                @if (!$mou->no_pks && !$mou->no_pks_instansi)
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                          <td>
    <span class="small text-dark d-block text-nowrap" title="{{ $mou->jenis_instansi ?? '-' }}">
        {{ \Illuminate\Support\Str::limit(($mou->jenis_instansi ?? '-'), 20) }}
    </span>
    
    @if ($mou->jenis_instansi_lainnya && $mou->jenis_instansi === 'Lainnya')
        <small class="text-muted d-block text-nowrap" title="{{ $mou->jenis_instansi_lainnya }}">
            ( {{ \Illuminate\Support\Str::limit($mou->jenis_instansi_lainnya, 30) }} )
        </small>
    @endif
</td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge bg-light text-dark border">
                                        {{ $mou->tanggal_masuk ? $mou->tanggal_masuk->format('d M Y') : '-' }}
                                    </span>
                                    <div class="text-center text-muted small"><i class="bi bi-arrow-down"></i></div>
                                    <span class="badge bg-light text-dark border">
                                        {{ $mou->tanggal_keluar ? $mou->tanggal_keluar->format('d M Y') : '-' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2" style="max-width: 120px;">
                                    @if ($mou->surat_permohonan)
                                        <a href="{{ asset('storage/' . $mou->surat_permohonan) }}" target="_blank"
                                            class="btn btn-sm btn-outline-dark rounded-pill px-2"
                                            title="Surat Permohonan">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </a>
                                    @endif
                                    @if ($mou->sk_pengangkatan_pimpinan)
                                        <a href="{{ asset('storage/' . $mou->sk_pengangkatan_pimpinan) }}" target="_blank"
                                            class="btn btn-sm btn-outline-dark rounded-pill px-2"
                                            title="SK Pengangkatan">
                                            <i class="bi bi-journal-richtext"></i>
                                        </a>
                                    @endif
                                    @if ($mou->sertifikat_akreditasi_prodi)
                                        <a href="{{ asset('storage/' . $mou->sertifikat_akreditasi_prodi) }}" target="_blank"
                                            class="btn btn-sm btn-outline-dark rounded-pill px-2"
                                            title="Sertifikat Akreditasi">
                                            <i class="bi bi-award"></i>
                                        </a>
                                    @endif
                                    @if ($mou->draft_mou)
                                        <a href="{{ asset('storage/' . $mou->draft_mou) }}" target="_blank"
                                            class="btn btn-sm btn-outline-dark rounded-pill px-2" title="Draft MoU">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                    @endif
                                    @if (
                                        !$mou->surat_permohonan &&
                                            !$mou->sk_pengangkatan_pimpinan &&
                                            !$mou->sertifikat_akreditasi_prodi &&
                                            !$mou->draft_mou)
                                        <span class="text-muted small">-</span>
                                    @endif
                                </div>
                            </td>
<td>
    <div style="max-width: 250px; white-space: normal;" title="{{ $mou->rencana_kerja_sama ?? '' }}">
        {{ \Illuminate\Support\Str::limit(($mou->rencana_kerja_sama ?? ''), 80) }}
    </div>
</td>
                            <td>
                                @if($mou->akun)
                                    @php
                                        $pw = \App\Support\AkunInstansi::passwordTampil($mou->akun);
                                        $pesan = "Akun portal Sindikat RSUD SLG untuk " . ($mou->nama_instansi ?: $mou->nama_universitas) . "\nLogin: " . url('/login') . "\nUsername: " . $mou->akun->email . ($pw ? "\nPassword: " . $pw : '');
                                    @endphp
                                    <div class="small text-nowrap"><i class="bi bi-person-badge text-muted me-1"></i><code>{{ $mou->akun->email }}</code></div>
                                    <div class="small text-nowrap mt-1 d-flex align-items-center gap-1">
                                        <i class="bi bi-key text-muted me-1"></i>
                                        @if($pw)
                                            <code class="pw-mask" data-pw="{{ $pw }}">••••••••</code>
                                            <button type="button" class="btn btn-link btn-sm p-0 text-secondary" title="Tampilkan / sembunyikan" onclick="togglePw(this)"><i class="bi bi-eye"></i></button>
                                        @else
                                            <span class="text-muted fst-italic">tidak tersimpan — reset untuk melihat</span>
                                        @endif
                                    </div>
                                    @if($mou->akun->reset_diminta_at)
                                        <span class="badge bg-warning text-dark mt-1"><i class="bi bi-exclamation-circle me-1"></i>Minta reset</span>
                                    @endif
                                    <div class="mt-1 d-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" data-pesan="{{ $pesan }}" onclick="salinPesanAkun(this)" title="Salin pesan berisi kredensial untuk PIC"><i class="bi bi-clipboard"></i></button>
                                        <form action="{{ route('mou.reset_akun', $mou->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Buat password baru untuk {{ $mou->akun->email }}? Password lama tidak berlaku lagi.');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning py-0 px-2" title="Reset password"><i class="bi bi-arrow-repeat"></i></button>
                                        </form>
                                        <form action="{{ route('mou.hapus_akun', $mou->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus akun instansi ini? Instansi tidak bisa login lagi.');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus akun"><i class="bi bi-person-dash"></i></button>
                                        </form>
                                    </div>
                                @else
                                    @php $saranU = \App\Support\AkunInstansi::saranUsername($mou->nama_instansi ?: $mou->nama_universitas); @endphp
                                    <form action="{{ route('mou.buat_akun', $mou->id) }}" method="POST" class="d-inline" onsubmit="return mintaUsername(this);">
                                        @csrf
                                        <input type="hidden" name="username" value="{{ $saranU }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" style="color:#7c1316;border-color:#e8b4b9;"><i class="bi bi-person-plus me-1"></i>Buat akun</button>
                                    </form>
                                    <div class="small text-muted mt-1">Saran: {{ $saranU }}@sindikat.com</div>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('mou.edit', $mou->id) }}" class="action-btn" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('mou.destroy', $mou->id) }}" method="POST"
                                    class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="action-btn delete btn-delete" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-folder-x display-4 text-muted mb-3" style="opacity: 0.5;"></i>
                                    <h5 class="text-muted fw-bold">Tidak ada data ditemukan</h5>
                                    <p class="text-muted small">
                                        @if (request()->has('search') || request()->has('tanggal_mulai'))
                                            Coba reset filter atau gunakan kata kunci lain.
                                        @else
                                            Tambahkan data MOU baru terlebih dahulu.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 4. PAGINATION --}}
    <div class="d-flex justify-content-center mt-4 animate-up" style="animation-delay: 0.3s;">
        {{ $mous->links() }}
    </div>

    {{-- SWEETALERT --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 1800,
                    toast: true,
                    position: 'top-end'
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: '{{ session('error') }}',
                    showConfirmButton: true
                });
            });
        </script>
    @endif
@endsection

@section('scripts')
    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        // 1. Fungsi Toast
        function showToast(message, type = 'success') {
            const colors = {
                success: "#00b09b",
                error: "#ff5f6d",
                info: "#2193b0"
            };
            Toastify({
                text: message,
                duration: 3000,
                gravity: "bottom",
                position: "right",
                style: {
                    background: colors[type] || colors.info
                },
                className: "rounded shadow-lg"
            }).showToast();
        }

        // 2. Fungsi Dropdown Tools
        function toggleTools(e) {
            if (e) e.stopPropagation();
            var menu = document.getElementById('toolsDropdownMenu');
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }

        function closeTools() {
            document.getElementById('toolsDropdownMenu').style.display = 'none';
        }

        window.addEventListener('click', function(e) {
            var menu = document.getElementById('toolsDropdownMenu');
            var btn = document.getElementById('toolsBtn');
            if (menu.style.display === 'block' && !menu.contains(e.target) && !btn.contains(e.target)) {
                menu.style.display = 'none';
            }
        });

        // 3. Fungsi Export (DIUPDATE untuk NO PKS)
        function exportMOU() {
            try {
                // Ambil data $mous dari Laravel
                const rawData = @json($mous);
                const dataList = rawData.data ? rawData.data : rawData;

                if (!dataList || dataList.length === 0) {
                    showToast("Tidak ada data untuk diexport", "error");
                    return;
                }

                // Mapping data MOU
                const data = dataList.map(mou => {
                    return [
                        mou.nama_instansi || mou.nama_universitas || '',
                        mou.no_pks || '', // Added
                        mou.no_pks_instansi || '', // Added
                        mou.tanggal_masuk ? mou.tanggal_masuk.split('T')[0] : '',
                        mou.tanggal_keluar ? mou.tanggal_keluar.split('T')[0] : '',
                        mou.surat_permohonan || '',
                        mou.sk_pengangkatan_pimpinan || '',
                        mou.sertifikat_akreditasi_prodi || '',
                        mou.draft_mou || '',
                        mou.keterangan || ''
                    ];
                });

                // Buat worksheet
                const ws = XLSX.utils.aoa_to_sheet([
                    ['Nama Instansi', 'No PKS Internal', 'No PKS Instansi', 'Tanggal Masuk', 'Tanggal Keluar',
                        'Surat Permohonan', 'SK Pengangkatan Pimpinan', 'Sertifikat Akreditasi Prodi',
                        'Draft MoU', 'Keterangan'
                    ]
                ].concat(data));

                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Data MOU');
                XLSX.writeFile(wb, `Data_MOU_${new Date().toISOString().split('T')[0]}.xlsx`);

                showToast("Berhasil export Excel!", "success");
            } catch (e) {
                console.error(e);
                showToast("Gagal export: " + e.message, "error");
            }
        }

        // 4. Fungsi Download Template
        function downloadTemplateMOU() {
            const ws = XLSX.utils.aoa_to_sheet([
                ['Nama Instansi', 'Tanggal Masuk (YYYY-MM-DD)', 'Tanggal Keluar (YYYY-MM-DD)', 'Keterangan'],
                ['Universitas Contoh', '2025-01-01', '2025-12-31', 'Kerjasama penelitian'],
                ['Institut Teknologi Kedua', '2024-06-15', '2025-06-14', '']
            ]);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Template');
            XLSX.writeFile(wb, 'Template_Import_MOU.xlsx');
            showToast("Template berhasil diunduh!", "success");
        }

        // 5. Fungsi Import
        function importMOU(input) {
            const file = input.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {
                        type: 'array',
                        cellDates: true
                    });
                    const firstSheetName = workbook.SheetNames[0];
                    const json = XLSX.utils.sheet_to_json(workbook.Sheets[firstSheetName], {
                        defval: '',
                        raw: false
                    });

                    if (json.length === 0) {
                        showToast('File Excel kosong atau format salah.', 'error');
                        return;
                    }

                    const fd = new FormData();
                    fd.append('_token', '{{ csrf_token() }}');
                    fd.append('data', JSON.stringify(json));

                    showToast("Sedang memproses data...", "info");

                    fetch('{{ route('mou.import_excel') }}', {
                            method: 'POST',
                            body: fd,
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(res => {
                            if (res.success) {
                                showToast(res.message, 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                console.error(res);
                                showToast('Gagal: ' + (res.message || 'Import gagal'), 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            showToast('Terjadi kesalahan server.', 'error');
                        });

                } catch (error) {
                    console.error(error);
                    showToast('Gagal membaca file Excel.', 'error');
                }
            };
            reader.readAsArrayBuffer(file);
            input.value = '';
        }

        // 6. Copy URL
       // 6. Copy URL (Versi Diperbaiki & Lebih Stabil)
        function copyPublicMouUrl() {
            // Pastikan menggunakan kutip dua " agar aman jika URL mengandung karakter aneh
            const publicUrl = "{{ route('public.mou.create') }}";

            // Cek dulu apakah URL valid/tidak kosong
            if (!publicUrl) {
                showToast('URL tidak ditemukan atau Route belum didefinisikan.', 'error');
                return;
            }

            // Cara 1: Modern (Navigator API) - Biasanya butuh HTTPS
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(publicUrl).then(() => {
                    showToast('URL publik berhasil disalin!', 'success');
                }).catch(err => {
                    console.error('Gagal copy clipboard modern:', err);
                    // Jika gagal, lempar ke cara manual
                    copyManual(publicUrl);
                });
            } else {
                // Cara 2: Jika HTTP biasa atau Browser lama
                copyManual(publicUrl);
            }
        }

        // Fungsi bantuan untuk copy manual (Fallback)
        function copyManual(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            
            // Sembunyikan textarea agar tidak mengganggu tampilan
            textArea.style.position = "fixed";
            textArea.style.left = "-9999px";
            textArea.style.top = "0";
            
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showToast('URL publik berhasil disalin!', 'success');
                } else {
                    showToast('Gagal menyalin URL secara otomatis.', 'error');
                }
            } catch (err) {
                console.error('Gagal copy manual:', err);
                showToast('Browser Anda tidak mengizinkan salin otomatis.', 'error');
            }

            document.body.removeChild(textArea);
        }
        // 7. Delete Confirmation
        document.addEventListener('DOMContentLoaded', () => {
            const deleteButtons = document.querySelectorAll('.btn-delete');
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Yakin hapus data?',
                        text: "Data MOU ini akan dihapus permanen.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#7c1316',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
<script>
    function togglePw(btn) {
        var c = btn.parentNode.querySelector('.pw-mask'), i = btn.querySelector('i');
        var tampil = c.textContent.indexOf('•') === -1;
        c.textContent = tampil ? '••••••••' : c.getAttribute('data-pw');
        i.className = tampil ? 'bi bi-eye' : 'bi bi-eye-slash';
    }
    function salinPesanAkun(btn) {
        var t = btn.getAttribute('data-pesan');
        var ok = function () { var i = btn.querySelector('i'); var c = i.className; i.className = 'bi bi-check2'; setTimeout(function () { i.className = c; }, 1500); };
        if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(t).then(ok, function () { prompt('Salin pesan:', t); }); }
        else { prompt('Salin pesan:', t); }
    }
    function mintaUsername(form) {
        var inp = form.querySelector('[name=username]');
        var u = prompt('Username akun instansi (tanpa @sindikat.com). Huruf kecil, angka, titik atau strip:', inp.value);
        if (u === null) return false;
        u = u.trim().toLowerCase().replace(/@.*$/, '');
        if (!u) { alert('Username tidak boleh kosong.'); return false; }
        inp.value = u;
        return true;
    }
</script>
<script>
    function exportAkunInstansi() {
        if (typeof XLSX === 'undefined') { alert('Library Excel belum termuat, coba muat ulang halaman.'); return; }
        fetch(@json(route('mou.export_akun')), { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
            .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(function (res) {
                var head = ['No', 'Nama Instansi', 'Username (login)', 'Password', 'Status Akun', 'Nama PIC', 'No. HP PIC', 'Akun Dibuat'];
                var rows = res.data.map(function (d, i) { return [i + 1, d.instansi, d.username, d.password, d.status, d.pic || '', d.hp_pic || '', d.dibuat]; });
                var aoa = [['DATA AKUN PORTAL INSTANSI — SINDIKAT RSUD SIMPANG LIMA GUMUL'], ['Halaman login: ' + res.login], ['Ditarik: ' + new Date().toLocaleString('id-ID')], [], head].concat(rows);
                var ws = XLSX.utils.aoa_to_sheet(aoa);
                ws['!cols'] = [{ wch: 5 }, { wch: 48 }, { wch: 36 }, { wch: 18 }, { wch: 22 }, { wch: 28 }, { wch: 18 }, { wch: 13 }];
                ws['!merges'] = [{ s: { r: 0, c: 0 }, e: { r: 0, c: 7 } }];
                ws['!autofilter'] = { ref: 'A5:H' + (5 + rows.length) };
                var wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Akun Instansi');
                XLSX.writeFile(wb, 'Akun_Instansi_' + new Date().toISOString().split('T')[0] + '.xlsx');
            })
            .catch(function (e) { alert('Gagal menarik data akun instansi (' + e.message + ').'); });
    }
</script>
@endsection
