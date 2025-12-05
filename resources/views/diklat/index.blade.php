@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --primary-color: #7c1316;
        --primary-light: #fcebeb; /* Versi sangat muda dari 7c1316 untuk background */
        --primary-hover: #9c1c20;
        --text-dark: #2c3e50;
        --text-muted: #6c757d;
        --shadow-soft: 0 10px 40px -10px rgba(0,0,0,0.08);
        --shadow-hover: 0 20px 40px -10px rgba(124, 19, 22, 0.15);
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
    }

    .btn-ultra:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(124, 19, 22, 0.4);
        color: white;
        background: linear-gradient(135deg, #94171b 0%, #c42328 100%);
    }

    /* Card Dashboard Pro */
    .card-dashboard {
        border: none;
        border-radius: 20px;
        background: white;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        position: relative;
        /* Aksen garis atas */
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
        background-color: #fdf6f6; /* Hint merah sangat tipis saat hover */
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

    /* Typography Content */
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

    /* Date Badge */
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

    /* Input Link Stylish */
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

    /* Action Buttons (Soft UI) */
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
    }

    .btn-soft-success { background: #e0f2f1; color: #009688; }
    .btn-soft-success:hover { background: #009688; color: white; transform: translateY(-2px); }

    .btn-soft-warning { background: #fff8e1; color: #ffc107; }
    .btn-soft-warning:hover { background: #ffc107; color: white; transform: translateY(-2px); }

    .btn-soft-info { background: #e3f2fd; color: #2196f3; }
    .btn-soft-info:hover { background: #2196f3; color: white; transform: translateY(-2px); }

    .btn-soft-danger { background: #ffebee; color: #ef5350; }
    .btn-soft-danger:hover { background: #ef5350; color: white; transform: translateY(-2px); }

    /* Empty State */
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
</style>

<div class="container py-5">

    <div class="page-header">
        <div>
            <h2 class="page-title">Kelola Diklat</h2>
            <p class="page-subtitle">Pusat manajemen formulir pelatihan dan pendaftaran pegawai.</p>
        </div>
        <a href="{{ route('diklat.create') }}" class="btn-ultra">
            <i class="fas fa-plus-circle"></i> Buat Form Baru
        </a>
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
                                <button class="btn-copy" onclick="copyLink('link-{{ $form->id }}')" id="btn-copy-{{ $form->id }}">
                                    Copy
                                </button>
                            </div>
                            <a href="{{ route('diklat.public.form', $form->public_link) }}" target="_blank" style="font-size: 11px; margin-left: 15px; color: var(--primary-color); font-weight: 500; text-decoration: none; display: inline-block; margin-top: 4px;">
                                <i class="fas fa-external-link-alt"></i> Buka Link
                            </a>
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
        @else
            <div class="empty-state">
                <div class="empty-icon-bg">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h4 style="color: var(--text-dark); font-weight: 600;">Belum Ada Data Diklat</h4>
                <p class="text-muted mb-4">Mulai kelola pelatihan dengan membuat formulir baru pertama Anda.</p>
                <a href="{{ route('diklat.create') }}" class="btn-ultra">
                    <i class="fas fa-plus"></i> &nbsp;Buat Form Sekarang
                </a>
            </div>
        @endif
    </div>
</div>

<script>
    // Script Copy Link dengan efek feedback yang lebih halus
    function copyLink(elementId) {
        var copyText = document.getElementById(elementId);
        var btnId = elementId.replace('link-', 'btn-copy-');
        var btn = document.getElementById(btnId);
        var originalText = btn.innerHTML;

        copyText.select();
        copyText.setSelectionRange(0, 99999);

        navigator.clipboard.writeText(copyText.value).then(function() {
            // Visual Feedback
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

    // Inisialisasi Tooltip Bootstrap (jika menggunakan Bootstrap 4/5)
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection
