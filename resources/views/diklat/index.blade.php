@extends('layouts.app')

@section('content')
{{-- 1. LOAD LIBRARY PENTING (Wajib ada agar script jalan) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    :root {
        --primary-color: #7c1316;
        --primary-light: #fcebeb;
        --primary-hover: #9c1c20;
        --text-dark: #2c3e50;
        --text-muted: #6c757d;
        --shadow-soft: 0 10px 40px -10px rgba(0,0,0,0.08);
        --shadow-hover: 0 20px 40px -10px rgba(124, 19, 22, 0.15);
    }
    
    /* Menghilangkan kotak/border pada tombol close modal */
.modal-header .close {
    background: transparent;
    border: none;
    font-size: 1.5rem;
    padding: 0.5rem 1rem;
    margin: -1rem -1rem -1rem auto;
    opacity: 0.5;
    outline: none;
    box-shadow: none;
}

.modal-header .close:hover {
    opacity: 0.8;
    background: transparent;
}

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fc;
    }

    /* Header Styling */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 35px;
        position: relative;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-dark);
        letter-spacing: -0.5px;
    }

    .page-subtitle {
        color: var(--text-muted);
        font-weight: 400;
        font-size: 15px;
        margin-top: 5px;
    }

    /* Button Utama Ultra */
    .btn-ultra {
        background: linear-gradient(135deg, #7c1316 0%, #a31d21 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 30px;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(124, 19, 22, 0.3);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        text-decoration: none;
    }

    .btn-ultra:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(124, 19, 22, 0.4);
        color: white;
        background: linear-gradient(135deg, #94171b 0%, #c42328 100%);
        text-decoration: none;
    }

    /* Pencarian Seragam */
    .ultra-search-group {
        display: flex;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow-soft);
        padding: 5px;
        border: 1px solid #eef0f2;
        transition: all 0.3s ease;
        min-width: 320px;
    }

    .ultra-search-group:focus-within {
        border-color: var(--primary-color);
        box-shadow: var(--shadow-hover);
    }

    .ultra-search-input {
        border: none;
        padding: 8px 15px;
        font-size: 14px;
        width: 100%;
        outline: none;
        color: var(--text-dark);
    }

    .btn-ultra-search {
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0 15px;
        transition: all 0.2s;
    }

    .btn-ultra-search:hover {
        background: var(--primary-hover);
    }

    .btn-reset-search {
        background: #f8f9fa;
        color: #dc3545;
        border: none;
        border-radius: 8px;
        width: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 5px;
        transition: all 0.2s;
    }

    .btn-reset-search:hover {
        background: #ffebee;
    }

    /* Card Dashboard */
    .card-dashboard {
        border: none;
        border-radius: 20px;
        background: white;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        position: relative;
        border-top: 4px solid var(--primary-color);
    }

    /* Table Styling */
    .table-custom {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-custom thead th {
        background-color: #fff;
        color: #8898aa;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 20px 25px;
        border-bottom: 2px solid #f1f3f9;
    }

    .table-custom tbody tr {
        transition: all 0.3s ease;
    }

    .table-custom tbody tr:hover {
        background-color: #fdf6f6;
        transform: scale(1.002);
    }

    .table-custom tbody td {
        padding: 20px 25px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f9;
        color: var(--text-dark);
        font-size: 14px;
    }

    .table-custom tbody tr:last-child td {
        border-bottom: none;
    }

    /* Thumbnail Image */
    .thumb-wrapper {
        position: relative;
        width: 70px;
        height: 50px;
        border-radius: 10px;
        overflow: hidden;
        margin-right: 18px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        flex-shrink: 0;
    }

    .thumb-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .table-custom tbody tr:hover .thumb-img {
        transform: scale(1.1);
    }

    .no-img-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, #e9ecef, #dee2e6);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: #adb5bd;
        font-weight: bold;
    }

    .content-title {
        font-weight: 700;
        font-size: 15px;
        color: var(--primary-color);
        margin-bottom: 4px;
    }

    .content-desc {
        color: #8898aa;
        font-size: 12px;
        line-height: 1.4;
    }

    .date-badge {
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-dark);
        border: 1px solid #e9ecef;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .date-badge i { color: var(--primary-color); }

    .link-wrapper {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 6px 6px 6px 15px;
        display: flex;
        align-items: center;
        max-width: 300px;
        transition: border-color 0.3s;
    }

    .link-wrapper:hover {
        border-color: #d1d9e6;
    }

    .link-input {
        border: none;
        background: transparent;
        font-size: 12px;
        color: #6c757d;
        width: 100%;
        outline: none;
    }

    .btn-copy {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 7px;
        padding: 5px 10px;
        font-size: 11px;
        font-weight: 600;
        color: var(--primary-color);
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .btn-copy:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    /* Style untuk Action Link (QR & Open) */
    .action-links {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
        padding-left: 2px;
    }

    .btn-action-sm {
        font-size: 11px;
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
        position: relative; 
        z-index: 2; 
    }

    /* Tombol Buka Link */
    .btn-action-link {
        color: var(--primary-color);
        background: #fff5f5;
        border-color: #ffe0e0;
    }
    .btn-action-link:hover {
        background: #ffe0e0;
        color: #9c1c20;
        text-decoration: none;
    }

    /* Tombol QR Code */
    .btn-action-qr {
        color: #2c3e50;
        background: #f8f9fa;
        border-color: #e9ecef;
    }
    .btn-action-qr:hover {
        background: #e2e6ea;
        border-color: #d6d8db;
        color: #000;
        text-decoration: none;
    }

    /* Tombol Soft (Edit/Delete) */
    .btn-soft {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.2s;
        margin: 0 3px;
        text-decoration: none;
    }
    
    .btn-soft-success { background: #e0f2f1; color: #009688; }
    .btn-soft-success:hover { background: #009688; color: white; transform: translateY(-2px); }
    .btn-soft-warning { background: #fff8e1; color: #ffc107; }
    .btn-soft-warning:hover { background: #ffc107; color: white; transform: translateY(-2px); }
    .btn-soft-info { background: #e3f2fd; color: #2196f3; }
    .btn-soft-info:hover { background: #2196f3; color: white; transform: translateY(-2px); }
    .btn-soft-danger { background: #ffebee; color: #ef5350; }
    .btn-soft-danger:hover { background: #ef5350; color: white; transform: translateY(-2px); }

    .empty-state {
        padding: 80px 20px;
        text-align: center;
    }
    .empty-icon-bg {
        width: 100px;
        height: 100px;
        background: var(--primary-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    .empty-icon-bg i {
        font-size: 40px;
        color: var(--primary-color);
    }

    /* Modal Styling */
    #qrCodeContainer {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 15px auto;
        min-height: 180px;
    }
    
    #qrCodeContainer img {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: white;
        max-width: 100%;
    }

    /* --- PAGINATION CUSTOM STYLE --- */
    .pagination {
        margin-bottom: 0;
        gap: 5px;
    }
    
    .page-item .page-link {
        border: none;
        border-radius: 8px;
        color: var(--text-dark);
        font-weight: 500;
        padding: 8px 16px;
        transition: all 0.2s;
        background: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .page-item.active .page-link {
        background: var(--primary-color) !important;
        color: white !important;
        box-shadow: 0 4px 10px rgba(124, 19, 22, 0.3);
        border: none;
    }

    .page-item.disabled .page-link {
        background: transparent;
        color: #ccc;
        box-shadow: none;
    }

    .page-item .page-link:hover:not(.active) {
        background: var(--primary-light);
        color: var(--primary-color);
        transform: translateY(-2px);
    }
</style>

<div class="">

    <div class="page-header">
        <!--<div>-->
        <!--    <h2 class="page-title">Kelola Diklat</h2>-->
        <!--    <p class="page-subtitle">Pusat manajemen formulir pelatihan dan pendaftaran pegawai.</p>-->
        <!--</div>-->
        <!--<div class="d-flex align-items-center" style="gap: 15px;">-->
            <form action="{{ route('diklat.index') }}" method="GET" class="ultra-search-group d-none d-md-flex">
                @if(request('search'))
                    <a href="{{ route('diklat.index') }}" class="btn-reset-search" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
                <input type="text" name="search" class="ultra-search-input" placeholder="Cari judul pelatihan..." value="{{ request('search') }}">
                <button type="submit" class="btn-ultra-search">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <a href="{{ route('diklat.create') }}" class="btn-ultra">
                <i class="fas fa-plus-circle"></i> Buat Form Baru
            </a>
        <!--</div>-->
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert" style="background: #d4edda; color: #155724; border-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle mr-2" style="font-size: 1.2rem;"></i>
                <strong>Berhasil!</strong> &nbsp; {{ session('success') }}
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card-dashboard animate__animated animate__fadeInUp">
        @if($forms->count() > 0)
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th width="35%">Detail Diklat</th>
                        <th width="15%">Pelaksanaan</th>
                        <th width="25%">Akses Publik</th>
                        <th width="25%" class="text-center">Kontrol</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($forms as $form)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="thumb-wrapper">
                                    @if($form->banner_path)
                                        <img src="{{ asset('storage/' . $form->banner_path) }}" class="thumb-img" alt="Banner">
                                    @else
                                        <div class="no-img-placeholder">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="content-title">{{ $form->judul }}</div>
                                    <div class="content-desc">
                                        {{ Str::limit($form->keterangan, 60) }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="date-badge">
                                <i class="far fa-calendar-alt"></i>
                                {{ \Carbon\Carbon::parse($form->tanggal_pelaksanaan)->isoFormat('D MMM Y') }}
                            </div>
                        </td>
                        <td>
                            <div class="link-wrapper">
                                <input type="text" class="link-input" value="{{ route('diklat.public.form', $form->public_link) }}" id="link-{{ $form->id }}" readonly>
                                <button type="button" class="btn-copy" onclick="copyLink('link-{{ $form->id }}')" id="btn-copy-{{ $form->id }}">
                                    Copy
                                </button>
                            </div>
                            
                            <div class="action-links">
                                <a href="{{ route('diklat.public.form', $form->public_link) }}" target="_blank" class="btn-action-sm btn-action-link">
                                    <i class="fas fa-external-link-alt"></i> Buka
                                </a>
                                
                                <button type="button" 
                                        class="btn-action-sm btn-action-qr btn-show-qr" 
                                        data-url="{{ route('diklat.public.form', $form->public_link) }}" 
                                        data-title="{{ $form->judul }}">
                                    <i class="fas fa-qrcode"></i> QR Code
                                </button>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('diklat.rekap', $form->id) }}" class="btn-soft btn-soft-success" data-toggle="tooltip" title="Lihat Pendaftar">
                                <i class="fas fa-users"></i>
                            </a>
                            <a href="{{ route('diklat.edit', $form->id) }}" class="btn-soft btn-soft-warning" data-toggle="tooltip" title="Edit Data">
                                <i class="fas fa-pen"></i>
                            </a>
                            <a href="{{ route('diklat.show', $form->id) }}" class="btn-soft btn-soft-info" data-toggle="tooltip" title="Detail Lengkap">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{ route('diklat.destroy', $form->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-soft btn-soft-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus form ini beserta seluruh data pendaftarnya?')" data-toggle="tooltip" title="Hapus Permanen">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- BAGIAN PAGINATION --}}
        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top" style="background: #fafbfe;">
            <div class="text-muted small">
                Menampilkan {{ $forms->firstItem() }} s/d {{ $forms->lastItem() }} dari {{ $forms->total() }} data
            </div>
            <div>
                {{ $forms->withQueryString()->links('pagination::bootstrap-4') }}
            </div>
        </div>
        {{-- AKHIR PAGINATION --}}

        @else
            <div class="empty-state">
                <div class="empty-icon-bg">
                    <i class="fas fa-search"></i>
                </div>
                <h4 style="color: var(--text-dark); font-weight: 600;">Data Tidak Ditemukan</h4>
                <p class="text-muted mb-4">Hasil pencarian "{{ request('search') }}" tidak ditemukan. Silakan coba kata kunci lain.</p>
                <a href="{{ route('diklat.index') }}" class="btn-ultra">
                    <i class="fas fa-sync-alt"></i> &nbsp;Tampilkan Semua Data
                </a>
            </div>
        @endif
    </div>
</div>

{{-- MODAL QR CODE --}}
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
            <div class="modal-header" style="border-bottom: 1px solid #f1f3f9;">
                <h5 class="modal-title" id="qrModalLabel" style="font-weight: 700; color: var(--text-dark); font-size: 16px;">
                    <i class="fas fa-qrcode mr-1"></i> Scan QR
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-3">
                <p class="text-muted mb-2" id="qrTitlePreview" style="font-size: 13px; font-weight: 500;"></p>
                
                <div id="qrCodeContainer"></div>
                
                <p style="font-size: 11px; color: #999; margin-top: 10px;">Arahkan kamera HP ke kode di atas.</p>
                
                <a id="downloadQrBtn" href="#" class="btn-ultra mt-2" style="width: 100%; justify-content: center; font-size: 13px; padding: 10px;">
                    <i class="fas fa-download"></i> Simpan Gambar
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Pastikan jQuery sudah dimuat sebelum menjalankan script
    document.addEventListener("DOMContentLoaded", function(event) { 
        
        // --- LOGIKA COPY LINK ---
        window.copyLink = function(elementId) { // Ganti ke window.function agar bisa dipanggil onclick
            var copyText = document.getElementById(elementId);
            var btnId = elementId.replace('link-', 'btn-copy-');
            var btn = document.getElementById(btnId);
            var originalText = btn.innerHTML;

            copyText.select();
            copyText.setSelectionRange(0, 99999);

            navigator.clipboard.writeText(copyText.value).then(function() {
                btn.innerHTML = '<i class="fas fa-check"></i>';
                btn.style.background = '#28a745';
                btn.style.color = '#fff';
                btn.style.borderColor = '#28a745';

                setTimeout(function(){
                    btn.innerHTML = originalText;
                    btn.style.background = 'white';
                    btn.style.color = '#7c1316';
                    btn.style.borderColor = '#e9ecef';
                }, 2000);
            }, function(err) {
                alert('Gagal menyalin link');
            });
        }

        // --- LOGIKA QR CODE ---
        // Cek apakah jQuery tersedia
        if (typeof jQuery == 'undefined') {
            console.error('jQuery belum termuat! Tombol QR tidak akan jalan.');
            return;
        }

        // Event listener untuk tombol Show QR
        $(document).on('click', '.btn-show-qr', function(e) {
            e.preventDefault();
            
            var url = $(this).data('url');
            var title = $(this).data('title');

            // 1. Reset isi kontainer QR
            var container = document.getElementById("qrCodeContainer");
            container.innerHTML = "";
            document.getElementById("qrTitlePreview").innerText = title;
            
            // 2. Buat QR Code baru
            try {
                var qrcode = new QRCode(container, {
                    text: url,
                    width: 180,
                    height: 180,
                    colorDark : "#000000",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H
                });
            } catch(err) {
                console.error("Gagal generate QR", err);
                alert("Library QR Code gagal dimuat.");
                return;
            }

            // 3. Set link download (tunggu rendering selesai)
            setTimeout(function() {
                var qrImg = container.querySelector("img");
                var downloadBtn = document.getElementById("downloadQrBtn");
                
                if(qrImg && qrImg.src) {
                    downloadBtn.href = qrImg.src;
                    downloadBtn.download = "QR-" + title.replace(/[^a-z0-9]/gi, '-').toLowerCase() + ".png";
                } else {
                    // Fallback jika render ke canvas
                    var canvas = container.querySelector("canvas");
                    if(canvas) {
                        downloadBtn.href = canvas.toDataURL("image/png");
                        downloadBtn.download = "QR-" + title.replace(/[^a-z0-9]/gi, '-').toLowerCase() + ".png";
                    }
                }
            }, 300);

            // 4. Tampilkan Modal (Bootstrap)
            $('#qrModal').modal('show');
        });

        // Inisialisasi Tooltip Bootstrap
        if (typeof $().tooltip === 'function') {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
</script>
@endsection