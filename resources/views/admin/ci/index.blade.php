@extends('layouts.app')



@section('content')
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-light: #a3191d;
            --custom-maroon-subtle: #fcf0f1;
            --text-dark: #2c3e50;
            --text-muted: #64748b;
            --card-radius: 16px;
            --shadow-soft: 0 4px 20px rgba(0, 0, 0, 0.05);
            --transition: 0.3s ease;
        }

        /* --- Header --- */
        .page-header-wrapper {
            background: #fff;
            border-radius: var(--card-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-soft);
            margin-bottom: 2rem;
            border-left: 5px solid var(--custom-maroon);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        /* --- Table Card --- */
        .custom-table-card {
            background: #fff;
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            border: none;
        }

        .table thead th {
            background-color: var(--custom-maroon);
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            color: #475569;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.95rem;
        }

        .table-hover tbody tr:hover {
            background-color: #fff5f6;
        }

        /* --- Buttons --- */
        .btn-maroon {
            background-color: var(--custom-maroon);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex; align-items: center; gap: 8px;
            text-decoration: none;
        }
        .btn-maroon:hover {
            background-color: var(--custom-maroon-light);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(124, 19, 22, 0.3);
        }

        /* Action Buttons (Icon Only) */
        .action-btn {
            width: 34px; height: 34px;
            border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            transition: var(--transition);
            border: none;
        }

        .btn-edit { background: #fff7ed; color: #ea580c; border: 1px solid #ffedd5; }
        .btn-edit:hover { background: #ea580c; color: white; }

        .btn-delete { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }
        .btn-delete:hover { background: #dc2626; color: white; }

        /* Animation */
        .animate-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0; transform: translateY(20px);
        }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
            .header-card {
        background: white;
        border-radius: 8px;
        border-left: 5px solid #7c1316;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    </style>

    {{-- Header Section --}}
  <div class="header-card d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Data Clinical Instructur</h4>
            <small class="text-muted">Kelola layanan pemebimbing lapang mahasiswa magang dan penelitian.</small>
        </div>
        <div>
            <a href="{{ route('admin.ci.create') }}" class="btn-maroon shadow-sm">
                <i class="bi bi-plus-lg"></i> Tambah CI Baru
            </a>
        </div>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show animate-up shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                <div><strong>Berhasil!</strong> {{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Table Section --}}
    <div class="custom-table-card animate-up" style="animation-delay: 0.1s;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th>Nama Lengkap</th>
                        <th>Bidang / Unit</th>
                        <th>Kontak (HP)</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cis as $index => $ci)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-secondary fw-bold" 
                                         style="width: 35px; height: 35px;">
                                        {{ substr($ci->nama, 0, 1) }}
                                    </div>
                                    <span class="fw-bold text-dark">{{ $ci->nama }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal px-3 py-2">
                                    {{ $ci->bidang }}
                                </span>
                            </td>
                            <td>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ci->no_hp) }}" target="_blank" class="text-decoration-none fw-bold text-success">
                                    <i class="bi bi-whatsapp me-1"></i> {{ $ci->no_hp }}
                                </a>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.ci.edit', $ci->id) }}" class="action-btn btn-edit" title="Edit Data">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.ci.destroy', $ci->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="action-btn btn-delete btn-submit-delete" title="Hapus Data">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bi bi-person-x display-4 text-muted mb-3 opacity-50"></i>
                                    <h5 class="text-muted fw-bold">Belum ada data CI</h5>
                                    <p class="text-muted small">Silakan tambahkan data pembimbing baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Jika pakai pagination --}}
        {{-- <div class="p-4 d-flex justify-content-center">
            {{ $cis->links() }}
        </div> --}}
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Konfirmasi Hapus dengan SweetAlert
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-submit-delete');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Hapus Data CI?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Hapus!',
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
@endsection