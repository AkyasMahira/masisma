@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root {
        --primary: #7c1316;
        --primary-light: #fff1f2;
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-400: #94a3b8;
        --slate-600: #475569;
        --slate-900: #0f172a;
    }

    body { background-color: var(--slate-50);}
    .dashboard-container { padding: 20px; margin: 0 auto; }

    /* --- HEADER --- */
    .header-section { display: flex; flex-direction: column; gap: 20px; margin-bottom: 30px; }
    @media (min-width: 768px) { .header-section { flex-direction: row; justify-content: space-between; align-items: flex-end; } }

    .action-group { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
    .search-input { padding: 12px 15px 12px 45px; border-radius: 14px; border: 1px solid var(--slate-200); width: 280px; outline: none; transition: 0.3s; font-size: 14px; }
    .search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-light); }

    /* --- TABLE & CARDS --- */
    .main-card { background: white; border-radius: 20px; border: 1px solid var(--slate-200); overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
    
    .custom-table { width: 100%; border-collapse: collapse; display: none; }
    @media (min-width: 992px) { .custom-table { display: table; } }
    .custom-table th { padding: 18px 24px; text-align: left; font-size: 11px; text-transform: uppercase; color: var(--slate-600); background: var(--slate-50); }
    .custom-table td { padding: 18px 24px; border-bottom: 1px solid var(--slate-50); }

    .mobile-grid { display: grid; grid-template-columns: 1fr; gap: 15px; padding: 15px; }
    @media (min-width: 992px) { .mobile-grid { display: none; } }
    .mobile-card { background: white; border-radius: 18px; padding: 20px; border: 1px solid var(--slate-200); }

    /* --- BUTTONS & BADGES --- */
    .badge-status { padding: 8px 12px; border-radius: 10px; font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer; transition: 0.2s; }
    .badge-active { background: #dcfce7; color: #166534; }
    .badge-inactive { background: #fee2e2; color: #991b1b; }
    .badge-status:hover { transform: scale(1.05); }

    .btn-icon-group { display: flex; gap: 6px; }
    .btn-action { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 10px; border: 1px solid var(--slate-200); color: var(--slate-600); text-decoration: none; transition: 0.2s; background: white; }
    .btn-action:hover { background: var(--slate-100); transform: translateY(-2px); }
    .btn-delete:hover { background: #fef2f2; color: #ef4444; border-color: #fee2e2; }

    .btn-qr { background: var(--slate-100); border: none; padding: 8px 14px; border-radius: 10px; font-weight: 700; font-size: 12px; cursor: pointer; display: flex; align-items: center; gap: 8px; }

    /* --- MODAL --- */
    .qr-modal { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 20px; }
    .qr-content { background: white; width: 100%; max-width: 380px; border-radius: 24px; overflow: hidden; animation: slideUp 0.3s ease; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="dashboard-container">
    <div class="header-section">
        <div>
            <h3 style="font-weight: 800; letter-spacing: -1px;">Formulir <span style="color: var(--primary);">Pelatihan</span></h3>
            <p style="color: var(--slate-400); font-size: 14px; margin: 5px 0 0;">Manajemen Formulir.</p>
        </div>
        <div class="action-group">
            <div style="position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 15px; top: 14px; color: var(--slate-400);"></i>
                <input type="text" id="searchInput" onkeyup="searchHandler()" placeholder="Cari formulir..." class="search-input">
            </div>
            <a href="{{ route('admin.forms.create') }}" style="background: var(--primary); color: white; padding: 12px 20px; border-radius: 12px; font-weight: 700; text-decoration: none; font-size: 14px;">
                <i class="fas fa-plus"></i> Tambah
            </a>
        </div>
    </div>

    <div class="main-card">
        <table class="custom-table" id="formTable">
            <thead>
                <tr>
                    <th>Nama Formulir</th>
                    <th>Status Toggle</th>
                    <th>QR Access</th>
                    <th>Respon</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($forms as $form)
                <tr class="form-item">
                    <td>
                        <div style="font-weight: 700; color: var(--slate-900);">{{ $form->title }}</div>
                        <div style="font-size: 12px; color: var(--slate-400);">{{ Str::limit($form->description, 40) }}</div>
                    </td>
                    <td>
                        <form action="{{ route('admin.forms.toggle', $form->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="badge-status {{ $form->is_active ? 'badge-active' : 'badge-inactive' }}">
                                <i class="fas {{ $form->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                {{ $form->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <button onclick="showQR('{{ route('forms.public.show', $form->slug) }}', '{{ $form->title }}')" class="btn-qr">
                            <i class="fas fa-qrcode"></i> Generate
                        </button>
                    </td>
                    <td style="font-weight: 800;">{{ $form->responses_count }} <i class="fas fa-users" style="color: var(--slate-200);"></i></td>
                    <td>
                        <div class="btn-icon-group" style="justify-content: flex-end;">
                            <a href="{{ route('forms.public.show', $form->slug) }}" target="_blank" class="btn-action" title="Lihat Publik"><i class="fas fa-external-link-alt"></i></a>
                            <a href="{{ route('admin.forms.responses', $form->id) }}" class="btn-action" title="Database" style="color: var(--primary); background: var(--primary-light);"><i class="fas fa-database"></i></a>
                            <a href="{{ route('admin.forms.edit', $form->id) }}" class="btn-action" title="Edit"><i class="fas fa-pen"></i></a>
                           
<form action="{{ route('admin.forms.duplicate', $form->id) }}" method="POST" style="display:inline;">
    @csrf
    <button type="submit" class="btn-action" title="Duplikat Form" style="color: #6366f1; background: #e0e7ff; border-color: #c7d2fe;">
        <i class="fas fa-copy"></i>
    </button>
</form>
                            <form action="{{ route('admin.forms.destroy', $form->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus formulir ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mobile-grid">
            @foreach($forms as $form)
            <div class="mobile-card form-item">
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                    <form action="{{ route('admin.forms.toggle', $form->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="badge-status {{ $form->is_active ? 'badge-active' : 'badge-inactive' }}">
                            <i class="fas {{ $form->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i> {{ $form->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                        </button>
                    </form>
                    <div style="font-weight: 800; font-size: 13px;">{{ $form->responses_count }} <i class="fas fa-users"></i></div>
                </div>
                <h3 style="margin: 0 0 5px; font-size: 16px; font-weight: 800;">{{ $form->title }}</h3>
                <p style="font-size: 13px; color: var(--slate-400); margin-bottom: 20px;">{{ Str::limit($form->description, 60) }}</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    <button onclick="showQR('{{ route('forms.public.show', $form->slug) }}', '{{ $form->title }}')" class="btn-qr" style="grid-column: span 2; justify-content: center; padding: 12px;">
                        <i class="fas fa-qrcode"></i> QR Access
                    </button>
                    <a href="{{ route('forms.public.show', $form->slug) }}" target="_blank" class="btn-action" style="width: 100%;"><i class="fas fa-external-link-alt"></i> Public</a>
                    <a href="{{ route('admin.forms.responses', $form->id) }}" class="btn-action" style="width: 100%; color: var(--primary);"><i class="fas fa-database"></i> Data</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div id="qrModal" class="qr-modal">
    <div class="qr-content">
        <div style="padding: 20px; background: var(--primary); color: white; text-align: center; font-weight: 800;">QR AKSES FORM</div>
        <div style="padding: 30px; display: flex; flex-direction: column; align-items: center; gap: 20px;">
            <div id="qrcode" style="padding: 15px; border: 1px solid var(--slate-100); border-radius: 20px; background: white;"></div>
            <p id="qrLinkText" style="font-size: 11px; color: var(--slate-400); text-align: center; margin: 0; word-break: break-all;"></p>
        </div>
        <div style="padding: 20px; background: var(--slate-50); display: flex; gap: 10px;">
            <button onclick="downloadQR()" style="flex: 1; background: var(--primary); color: white; border: none; padding: 12px; border-radius: 12px; font-weight: 700; cursor: pointer;"><i class="fas fa-download"></i> Simpan</button>
            <button onclick="closeQR()" style="background: var(--slate-200); color: var(--slate-600); border: none; padding: 12px 20px; border-radius: 12px; font-weight: 700; cursor: pointer;">Tutup</button>
        </div>
    </div>
</div>

<script>
    function searchHandler() {
        const q = document.getElementById("searchInput").value.toUpperCase();
        document.querySelectorAll(".form-item").forEach(item => {
            item.style.display = item.innerText.toUpperCase().includes(q) ? "" : "none";
        });
    }

    function showQR(url, title) {
        window.currentTitle = title;
        document.getElementById('qrLinkText').innerText = url;
        document.getElementById('qrcode').innerHTML = "";
        new QRCode(document.getElementById("qrcode"), { text: url, width: 200, height: 200 });
        document.getElementById('qrModal').style.display = 'flex';
    }

    function closeQR() { document.getElementById('qrModal').style.display = 'none'; }
    function downloadQR() {
        const img = document.querySelector('#qrcode img');
        if (img) {
            const link = document.createElement('a');
            link.href = img.src;
            link.download = `QR_${window.currentTitle}.png`;
            link.click();
        }
    }
    window.onclick = (e) => { if(e.target == document.getElementById('qrModal')) closeQR(); }
</script>
@endsection