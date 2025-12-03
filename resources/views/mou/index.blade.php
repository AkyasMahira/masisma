@extends('layouts.app')

@section('title', 'Data MOU')
@section('page-title', 'Data MOU') {{-- Ini mungkin tidak terpakai oleh style baru --}}

@section('content')
    {{--
      =====================================================
      STYLE KUSTOM (DARI CONTOH 'Pelatihan Dasar')
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
            z-index: 1050; /* Pastikan dropdown di atas */
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
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: #555;
            border-bottom: 1px solid #f0f0f0;
        }

        .table-hover tbody tr:hover {
            background-color: #fff5f6; /* custom-maroon-subtle */
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
    {{-- ================= END STYLE ================= --}}


    {{--
      =====================================================
      STRUKTUR HTML BARU UNTUK LIST MOU
      =====================================================
    --}}

    {{-- 1. HEADER HALAMAN --}}
    <div class="page-header-wrapper d-flex flex-wrap justify-content-between align-items-center gap-3 animate-up">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--custom-maroon);">Data MOU</h4>
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
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item py-2" href="javascript:void(0)" onclick="exportMOU(); closeTools();">
                        <i class="bi bi-file-earmark-excel text-success me-2"></i> Export Excel
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
            {{-- Form Filter --}}
            <form id="filterForm" method="GET" action="{{ route('mou.index') }}">
                <div class="row">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="small text-muted fw-bold text-uppercase">Cari Nama Instansi</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light border-0 border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-0" name="search"
                                placeholder="Nama instansi..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="small text-muted fw-bold text-uppercase">Dari Tanggal</label>
                         <div class="input-group shadow-sm">
                            <span class="input-group-text bg-light border-0 border-end-0"><i class="bi bi-calendar-plus"></i></span>
                            <input type="date" class="form-control bg-light border-0" name="tanggal_mulai"
                                value="{{ request('tanggal_mulai') }}">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="small text-muted fw-bold text-uppercase">Sampai Tanggal</label>
                         <div class="input-group shadow-sm">
                             <span class="input-group-text bg-light border-0 border-end-0"><i class="bi bi-calendar-check"></i></span>
                            <input type="date" class="form-control bg-light border-0" name="tanggal_selesai"
                                value="{{ request('tanggal_selesai') }}">
                        </div>
                    </div>
                    <div class="col-md-12 d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-maroon shadow-sm">Terapkan</button>
                        <a href="{{ route('mou.index') }}" class="btn btn-light border shadow-sm"
                            title="Reset Filter">
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
                        <th>Jenis</th>
                        <th>Durasi</th>
                        <th>Dokumen</th>
                        <th>Rencana Kerja Sama</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mous as $index => $mou)
                        <tr>
                            <td class="text-center text-muted fw-bold">
                                {{-- Nomor urut berdasarkan pagination --}}
                                {{ ($mous->currentPage() - 1) * $mous->perPage() + $index + 1 }}
                            </td>
                            <td>
                                <span class="fw-bold text-dark d-block">{{ $mou->nama_instansi }}</span>
                                @if($mou->alamat_instansi)
                                    <small class="d-block text-muted">{{ $mou->alamat_instansi }}</small>
                                @endif
                                @if($mou->nama_pic_instansi)
                                    <small class="d-block text-muted">PIC: {{ $mou->nama_pic_instansi }} @if($mou->nomor_kontak_pic) &middot; {{ $mou->nomor_kontak_pic }} @endif</small>
                                @endif
                            </td>
                            <td>
                                <span class="small text-dark d-block">{{ $mou->jenis_instansi ?? '-' }}
                                @if($mou->jenis_instansi_lainnya && $mou->jenis_instansi === 'Lainnya')
                                    <small class="text-muted d-block">( {{ $mou->jenis_instansi_lainnya }} )</small>
                                @endif
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border p-2">
                                    {{ $mou->tanggal_masuk ? $mou->tanggal_masuk->format('d M Y') : '-' }}
                                </span>
                                <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                <span class="badge bg-light text-dark border p-2">
                                    {{ $mou->tanggal_keluar ? $mou->tanggal_keluar->format('d M Y') : '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    @if($mou->surat_permohonan)
                                        <a href="{{ Storage::url($mou->surat_permohonan) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-2" title="Surat Permohonan">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </a>
                                    @endif
                                    @if($mou->sk_pengangkatan_pimpinan)
                                        <a href="{{ Storage::url($mou->sk_pengangkatan_pimpinan) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-2" title="SK Pengangkatan">
                                            <i class="bi bi-journal-richtext"></i>
                                        </a>
                                    @endif
                                    @if($mou->sertifikat_akreditasi_prodi)
                                        <a href="{{ Storage::url($mou->sertifikat_akreditasi_prodi) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-2" title="Sertifikat Akreditasi">
                                            <i class="bi bi-award"></i>
                                        </a>
                                    @endif
                                    @if($mou->draft_mou)
                                        <a href="{{ Storage::url($mou->draft_mou) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-2" title="Draft MoU">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                    @endif
                                    @if(!$mou->surat_permohonan && !$mou->sk_pengangkatan_pimpinan && !$mou->sertifikat_akreditasi_prodi && !$mou->draft_mou)
                                        -
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="max-width: 18rem;">
                                    {{ \Illuminate\Support\Str::limit($mou->rencana_kerja_sama ?? '', 80) }}
                                </div>
                            </td>
                            {{-- Keterangan column intentionally removed --}}
                            <td class="text-center">
                                {{-- Tombol Aksi (Style Baru) --}}
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
                        {{-- Tampilan Data Kosong --}}
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-folder-x display-4 text-muted mb-3" style="opacity: 0.5;"></i>
                                    <h5 class="text-muted fw-bold">Tidak ada data ditemukan</h5>
                                    <p class="text-muted small">
                                        @if(request()->has('search') || request()->has('tanggal_mulai'))
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
        {{-- Pastikan Anda mengirim data $mous dengan ->paginate() dari controller --}}
        {{ $mous->links() }}
    </div>


    {{--
      =====================================================
      SCRIPT UNTUK SWEETALERT (Menggantikan alert biasa)
      =====================================================
    --}}
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
    {{--
      =====================================================
      SEMUA SCRIPT DARI 'Pelatihan Dasar'
      =====================================================
    --}}
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


        // 3. Fungsi Export (Diadaptasi untuk MOU)
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
                        mou.tanggal_masuk ? mou.tanggal_masuk.split('T')[0] : '', // Format YYYY-MM-DD
                        mou.tanggal_keluar ? mou.tanggal_keluar.split('T')[0] : '', // Format YYYY-MM-DD
                        mou.surat_permohonan || '',
                        mou.sk_pengangkatan_pimpinan || '',
                        mou.sertifikat_akreditasi_prodi || '',
                        mou.draft_mou || '',
                        mou.keterangan || ''
                    ];
                });

                // Buat worksheet
                const ws = XLSX.utils.aoa_to_sheet([
                    ['Nama Instansi', 'Tanggal Masuk', 'Tanggal Keluar', 'Surat Permohonan', 'SK Pengangkatan Pimpinan', 'Sertifikat Akreditasi Prodi', 'Draft MoU', 'Keterangan']
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

        // 4. Fungsi Download Template (Diadaptasi untuk MOU)
        function downloadTemplateMOU() {
            const ws = XLSX.utils.aoa_to_sheet([
                ['Nama Instansi', 'Tanggal Masuk (YYYY-MM-DD)', 'Tanggal Keluar (YYYY-MM-DD)', 'Keterangan'],
                    ['Universitas Contoh', '2025-01-01', '2025-12-31', 'Kerjasama penelitian', 'Surat Permohonan', 'SK Pengangkatan Pimpinan', 'Sertifikat Akreditasi Prodi', 'Draft MoU'],
                ['Institut Teknologi Kedua', '2024-06-15', '2025-06-14', '']
            ]);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Template');
            XLSX.writeFile(wb, 'Template_Import_MOU.xlsx');
            showToast("Template berhasil diunduh!", "success");
        }

        // 5. Fungsi Import (Diadaptasi untuk MOU)
        function importMOU(input) {
            const file = input.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array', cellDates: true });
                    const firstSheetName = workbook.SheetNames[0];
                    const json = XLSX.utils.sheet_to_json(workbook.Sheets[firstSheetName], { defval: '', raw: false });

                    if (json.length === 0) {
                        showToast('File Excel kosong atau format salah.', 'error');
                        return;
                    }

                    const fd = new FormData();
                    fd.append('_token', '{{ csrf_token() }}');
                    fd.append('data', JSON.stringify(json));

                    showToast("Sedang memproses data...", "info");

                    // Pastikan route 'mou.import_excel' ada di web.php
                    fetch('{{ route('mou.import_excel') }}', {
                        method: 'POST',
                        body: fd,
                        headers: { 'Accept': 'application/json' }
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
            input.value = ''; // Reset file input
        }

        // 6. Fungsi Copy URL Publik MOU
        function copyPublicMouUrl() {
            const publicUrl = '{{ route('public.mou.create') }}';

            navigator.clipboard.writeText(publicUrl).then(() => {
                showToast('URL publik berhasil disalin ke clipboard!', 'success');
            }).catch(() => {
                // Fallback untuk browser lama
                const textarea = document.createElement('textarea');
                textarea.value = publicUrl;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                showToast('URL publik berhasil disalin ke clipboard!', 'success');
            });
        }

        // 7. Fungsi Delete Confirmation (SweetAlert)
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
                        confirmButtonColor: '#7c1316', // --custom-maroon
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

            // Hapus inisialisasi Popover karena tidak dipakai di tabel MOU
        });
    </script>
@endsection
