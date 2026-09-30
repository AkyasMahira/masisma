@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

<style>
    :root {
        --custom-maroon: #7c1316;
        --custom-maroon-light: #a3191d;
        --custom-maroon-subtle: #fcf0f1;
        --card-radius: 16px;
        --shadow-soft: 0 10px 30px rgba(0, 0, 0, 0.04);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body { background-color: #f8f9fa; }

    .page-header {
        background: #fff; border-radius: var(--card-radius); padding: 1.5rem 2rem;
        box-shadow: var(--shadow-soft); margin-bottom: 2rem; border-left: 6px solid var(--custom-maroon);
    }

    .filter-card {
        background: #fff; border-radius: var(--card-radius); box-shadow: var(--shadow-soft);
        border: none; margin-bottom: 1.5rem;
    }

    .custom-table-card {
        background: #fff; border-radius: var(--card-radius); box-shadow: var(--shadow-soft);
        border: none; overflow: hidden;
    }

    .table thead th {
        background-color: #fcfcfc; color: #8898aa; padding: 1.2rem 1rem;
        font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;
        border-bottom: 1px solid #eee;
    }

    /* Status Badges */
    .status-select {
        border-radius: 50px; font-size: 0.65rem; font-weight: 800; padding: 0.45rem 1rem;
        border: 1px solid transparent; cursor: pointer; text-align: center; width: 155px;
        appearance: none; transition: var(--transition);
    }
    .st-ttd { background: #fff5f5; color: #7c1316; border-color: #feb2b2; } /* Perlu TTD */
    .st-menunggu { background: #ebf8ff; color: #2b6cb0; border-color: #bee3f8; } /* Menunggu Bayar */
    .st-proses { background: #fffaf0; color: #975a16; border-color: #fbd38d; animation: pulse-warn 2s infinite; }
    .st-selesai { background: #f0fff4; color: #276749; border-color: #9ae6b4; }
    .st-batal { background: #edf2f7; color: #4a5568; border-color: #cbd5e0; }

    @keyframes pulse-warn { 0% { box-shadow: 0 0 0 0 rgba(246, 173, 85, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(246, 173, 85, 0); } 100% { box-shadow: 0 0 0 0 rgba(246, 173, 85, 0); } }

    .btn-circle {
        width: 38px; height: 38px; border-radius: 50%; display: inline-flex;
        align-items: center; justify-content: center; background: #fff;
        color: #525f7f; border: 1px solid #e9ecef; transition: var(--transition);
    }
    .btn-circle:hover { background: var(--custom-maroon); color: #fff; transform: translateY(-3px); }

    .signature-wrapper {
        border: 2px dashed #dee2e6; border-radius: 12px; background: #fafafa;
        position: relative; width: 100%; height: 200px;
    }
    #signature-pad { position: absolute; left: 0; top: 0; width: 100%; height: 100%; cursor: crosshair; }

    .animate-up { animation: fadeInUp 0.6s ease forwards; opacity: 0; transform: translateY(20px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
</style>

<div class="container-fluid py-4">
    
    {{-- 1. HEADER --}}
    <div class="page-header d-flex justify-content-between align-items-center animate-up">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--custom-maroon);">Billing Invoices</h3>
            <p class="text-muted small mb-0">Sistem penagihan & verifikasi RSUD Simpang Lima Gumul</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.invoices.report') }}" class="btn btn-outline-dark px-4 py-2 fw-bold" style="border-radius:10px;">
                <i class="bi bi-graph-up-arrow me-2"></i> Rekap Laporan
            </a>
            <a href="{{ route('admin.invoices.create') }}" class="btn px-4 py-2 fw-bold" style="background-color:#7c1316; color:white; border-radius:10px;">
                <i class="bi bi-plus-lg me-2"></i> Buat Invoice
            </a>
        </div>
    </div>

    {{-- 2. FILTER --}}
{{-- 2. FILTER & SORTING --}}
    <div class="filter-card animate-up" style="animation-delay: 0.1s;">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.invoices.index') }}" id="filterForm">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="input-group bg-light rounded-3 px-3 py-1 h-100">
                            <span class="input-group-text bg-transparent border-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control bg-transparent border-0 shadow-none" placeholder="Cari No. Invoice / Instansi..." value="{{ $search }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select border-0 bg-light rounded-3 py-2 shadow-none h-100">
                            <option value="">Semua Status</option>
                            <option value="Perlu TTD" {{ $status == 'Perlu TTD' ? 'selected' : '' }}>PERLU TTD</option>
                            <option value="Menunggu Pembayaran" {{ $status == 'Menunggu Pembayaran' ? 'selected' : '' }}>MENUNGGU BAYAR</option>
                            <option value="Proses" {{ $status == 'Proses' ? 'selected' : '' }}>PROSES (VERIFY)</option>
                            <option value="Selesai" {{ $status == 'Selesai' ? 'selected' : '' }}>SELESAI</option>
                            <option value="Batal" {{ $status == 'Batal' ? 'selected' : '' }}>BATAL</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="jenis" class="form-select border-0 bg-light rounded-3 py-2 shadow-none h-100">
                            <option value="">Semua Kegiatan</option>
                            @foreach(['Praktek Kerja Lapangan', 'Penelitian', 'Magang', 'Diklat'] as $j)
                                <option value="{{ $j }}" {{ $jenis == $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="sort_by" class="form-select border-0 bg-light rounded-3 py-2 shadow-none h-100">
                            <option value="created_at" {{ $sortBy == 'created_at' ? 'selected' : '' }}>Urutkan: Tanggal Dibuat</option>
                            <option value="no_invoice" {{ $sortBy == 'no_invoice' ? 'selected' : '' }}>Urutkan: No Invoice</option>
                            <option value="jumlah_dibayarkan" {{ $sortBy == 'jumlah_dibayarkan' ? 'selected' : '' }}>Urutkan: Nominal Tagihan</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-3">
                        <select name="bulan" class="form-select border-0 bg-light rounded-3 py-2 shadow-none">
                            <option value="">Semua Bulan</option>
                            @for($m=1; $m<=12; ++$m)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="tahun" class="form-select border-0 bg-light rounded-3 py-2 shadow-none">
                            <option value="">Semua Tahun</option>
                            @php $currentYear = date('Y'); @endphp
                            @for($y=$currentYear; $y>=$currentYear-3; $y--)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex bg-light rounded-3 p-1">
                            <input type="radio" class="btn-check" name="sort_dir" id="sortDesc" value="desc" {{ $sortDir == 'desc' ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary border-0 w-50" for="sortDesc"><i class="bi bi-sort-down"></i> Descending (Z-A)</label>
                            
                            <input type="radio" class="btn-check" name="sort_dir" id="sortAsc" value="asc" {{ $sortDir == 'asc' ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary border-0 w-50" for="sortAsc"><i class="bi bi-sort-up"></i> Ascending (A-Z)</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn w-100 fw-bold rounded-3 h-100" style="background-color:#7c1316; color:white;">
                            <i class="bi bi-funnel-fill me-1"></i> Terapkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- 3. TABLE --}}
    <div class="custom-table-card animate-up" style="animation-delay: 0.2s;">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Invoice & Instansi</th>
                        <th>Tagihan</th>
                        <th class="text-center">Bukti</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $index => $item)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ ($page - 1) * 10 + $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->no_invoice }}</div>
                                <div class="small text-muted">{{ $item->instansi }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">Rp {{ number_format($item->jumlah_dibayarkan, 0, ',', '.') }}</div>
                                <div class="badge bg-light text-muted border-0 fw-normal" style="font-size: 0.6rem;">{{ $item->jenis_kegiatan }}</div>
                            </td>
                            <td class="text-center">
                                @if($item->bukti_bayar)
                                    <a href="{{ asset('storage/' . $item->bukti_bayar) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3" style="font-size: 0.65rem;">
                                        <i class="bi bi-image me-1"></i> BUKTI
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.invoices.updateStatus', $item->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    @php
                                        $stClass = 'st-ttd';
                                        if($item->status == 'Menunggu Pembayaran') $stClass = 'st-menunggu';
                                        if($item->status == 'Proses') $stClass = 'st-proses';
                                        if($item->status == 'Selesai') $stClass = 'st-selesai';
                                        if($item->status == 'Batal') $stClass = 'st-batal';
                                    @endphp
                                    <select name="status" onchange="this.form.submit()" class="status-select {{ $stClass }}">
                                        <option value="Perlu TTD" {{ $item->status == 'Perlu TTD' ? 'selected' : '' }}>PERLU TTD</option>
                                        <option value="Menunggu Pembayaran" {{ $item->status == 'Menunggu Pembayaran' ? 'selected' : '' }}>MENUNGGU BAYAR</option>
                                        <option value="Proses" {{ $item->status == 'Proses' ? 'selected' : '' }}>VERIFIKASI BAYAR</option>
                                        <option value="Selesai" {{ $item->status == 'Selesai' ? 'selected' : '' }}>SELESAI / LUNAS</option>
                                        <option value="Batal" {{ $item->status == 'Batal' ? 'selected' : '' }}>BATAL</option>
                                    </select>
                                </form>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    {{-- REVISI: Tombol TTD atau Tombol Verifikasi --}}
                                    @if($item->status == 'Perlu TTD' || $item->status == 'Proses')
                                        <button onclick="openVerifyModal({{ json_encode($item) }})" class="btn btn-sm {{ $item->status == 'Perlu TTD' ? 'btn-danger' : 'btn-warning' }} fw-bold px-3 shadow-sm border-0" style="border-radius: 8px; font-size: 0.65rem;">
                                            {{ $item->status == 'Perlu TTD' ? 'TTD SEKARANG' : 'VERIFIKASI' }}
                                        </button>
                                    @endif

                                    <button onclick="viewDetail({{ json_encode($item) }})" class="btn-circle" title="Detail"><i class="bi bi-eye"></i></button>
                                    
                                    {{-- REVISI: Link hanya muncul jika sudah TTD --}}
                                    @if($item->status != 'Perlu TTD')
                                        <button onclick="copyLink('{{ route('public.invoice.pay', $item->payment_token) }}')" class="btn-circle text-primary" title="Salin Link Tagihan"><i class="bi bi-link-45deg"></i></button>
                                    @endif

                                    <a href="{{ route('admin.invoices.print', $item->id) }}" target="_blank" class="btn-circle text-info" title="Cetak PDF"><i class="bi bi-printer"></i></a>
                                    <a href="{{ route('admin.invoices.edit', $item->id) }}" class="btn-circle text-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    
                                    <form action="{{ route('admin.invoices.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-circle text-danger border-0 btn-delete-trigger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data invoice.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($totalPages > 1)
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light">
                <div class="text-muted small fw-bold">
                    Menampilkan Halaman {{ $page }} dari {{ $totalPages }} (Total: {{ $totalData }} Data)
                </div>
                <ul class="pagination pagination-sm mb-0 custom-pagination">
                    {{-- Tombol Previous --}}
                    <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>

                    {{-- Logic Angka Halaman (Menampilkan max 5 angka berdekatan) --}}
                    @php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                    @endphp

                    @if($startPage > 1)
                        <li class="page-item"><a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => 1]) }}">1</a></li>
                        @if($startPage > 2)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                    @endif

                    @for ($i = $startPage; $i <= $endPage; $i++)
                        <li class="page-item {{ $page == $i ? 'active' : '' }}">
                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                        </li>
                    @endfor

                    @if($endPage < $totalPages)
                        @if($endPage < $totalPages - 1)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                        <li class="page-item"><a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $totalPages]) }}">{{ $totalPages }}</a></li>
                    @endif

                    {{-- Tombol Next --}}
                    <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL VERIFIKASI / TTD --}}
<div class="modal fade" id="modalVerify" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-dark text-white border-0 p-4">
                <h5 class="modal-title fw-bold" id="verifyTitle">Verifikasi Dokumen</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="verifyForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body p-4">
                    <div id="buktiSection" class="text-center mb-4" style="display:none;">
                        <p class="text-muted small fw-bold text-uppercase">Bukti Transfer Kampus:</p>
                        <img id="verifyImg" src="" class="img-fluid rounded-3 border shadow-sm" style="max-height: 200px;">
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase d-block mb-2">Metode Tanda Tangan</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="ttd_method" id="v_manual" value="manual" checked onchange="toggleSignPad(false)">
                                <label class="btn btn-outline-dark w-100 py-3 rounded-4" for="v_manual">
                                    <i class="bi bi-pen-fill d-block h4 mb-1"></i><span class="small fw-bold">BASAH</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="ttd_method" id="v_digital" value="digital" onchange="toggleSignPad(true)">
                                <label class="btn btn-outline-dark w-100 py-3 rounded-4" for="v_digital">
                                    <i class="bi bi-fingerprint d-block h4 mb-1"></i><span class="small fw-bold">E-SIGN</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="signature-area" style="display: none;">
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <label class="fw-bold small text-maroon">Goreskan Tanda Tangan:</label>
                            <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none p-0" onclick="clearSignature()">Hapus Goresan</button>
                        </div>
                        <div class="signature-wrapper">
                            <canvas id="signature-pad"></canvas>
                        </div>
                        <input type="hidden" name="signature" id="signature-input">
                    </div>

                    <div class="alert alert-warning mt-3 border-0 rounded-4 small">
                        <i class="bi bi-info-circle-fill me-2"></i> Konfirmasi ini akan mengesahkan dokumen dan memperbarui status invoice.
                    </div>
                </div>
                <div class="p-4 border-0">
                    <button type="submit" class="btn btn-dark w-100 py-3 fw-bold rounded-4 shadow">KONFIRMASI SEKARANG</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Rincian Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6 border-end">
                        <div class="detail-label">Institusi</div>
                        <div class="detail-value h5" id="detInstansi"></div>
                        <div class="detail-label">Ruangan</div>
                        <div class="detail-value" id="detRuangan"></div>
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <div class="detail-label">Jatuh Tempo</div>
                        <div class="detail-value text-danger fw-bold" id="detTempo"></div>
                        <div class="detail-label">Total Tagihan</div>
                        <div class="detail-value h4 text-maroon" id="detTotal"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let signaturePad;
    const canvas = document.getElementById('signature-pad');

    function initSignaturePad() {
        if (!signaturePad) {
            signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: 'rgb(0, 0, 128)'
            });
            resizeCanvas();
        }
    }

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear();
    }

    function toggleSignPad(show) {
        document.getElementById('signature-area').style.display = show ? 'block' : 'none';
        if (show) {
            initSignaturePad();
        }
    }

    function clearSignature() { if (signaturePad) signaturePad.clear(); }

    function openVerifyModal(data) {
        document.getElementById('verifyForm').action = `/admin/invoices/${data.id}/verify`;
        
        // Atur UI berdasarkan status
        if (data.status === 'Proses') {
            document.getElementById('verifyTitle').innerText = 'Verifikasi Pembayaran';
            document.getElementById('buktiSection').style.display = 'block';
            document.getElementById('verifyImg').src = `/storage/${data.bukti_bayar}`;
        } else {
            document.getElementById('verifyTitle').innerText = 'Tanda Tangani Invoice';
            document.getElementById('buktiSection').style.display = 'none';
        }

        new bootstrap.Modal(document.getElementById('modalVerify')).show();
    }

    document.getElementById('verifyForm').addEventListener('submit', function(e) {
        const isDigital = document.getElementById('v_digital').checked;
        if (isDigital) {
            if (signaturePad.isEmpty()) {
                e.preventDefault();
                Swal.fire('E-Sign Kosong', 'Harap masukkan tanda tangan.', 'error');
                return;
            }
            document.getElementById('signature-input').value = signaturePad.toDataURL();
        }
    });

    function viewDetail(data) {
        document.getElementById('detInstansi').innerText = data.instansi;
        document.getElementById('detRuangan').innerText = data.ruangan_ci;
        document.getElementById('detTempo').innerText = data.tgl_jatuh_tempo;
        document.getElementById('detTotal').innerText = 'Rp ' + parseInt(data.jumlah_dibayarkan).toLocaleString('id-ID');
        new bootstrap.Modal(document.getElementById('modalDetail')).show();
    }

    function copyLink(url) {
        navigator.clipboard.writeText(url);
        Swal.fire({ icon: 'success', title: 'Link Disalin!', timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
    }

    // Alert Hapus
    document.querySelectorAll('.btn-delete-trigger').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus data?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#7c1316',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) this.closest('form').submit();
            });
        });
    });
</script>
@endsection