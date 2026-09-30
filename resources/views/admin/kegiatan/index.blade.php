@extends('layouts.app')
@section('title', 'Data Pelatihan')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <style>
        :root { 
            --custom-maroon: #7c1316; 
            --custom-maroon-light: #a3191d; 
            --bg-color: #f8fafc;
            --text-dark: #1e293b; 
            --text-muted: #64748b; 
            --card-radius: 16px; 
            --transition: 0.3s ease; 
        }
        
        /* Header Card */
        .header-card { 
            background: linear-gradient(135deg, var(--custom-maroon) 0%, var(--custom-maroon-light) 100%);
            border-radius: var(--card-radius); 
            padding: 25px 30px; 
            margin-bottom: 25px; 
            box-shadow: 0 10px 20px rgba(124, 19, 22, 0.15); 
            color: white;
        }
        
        /* Main Cards */
        .custom-card { 
            background: #fff; 
            border-radius: var(--card-radius); 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04); 
            border: 1px solid #f1f5f9; 
            margin-bottom: 1.5rem; 
            overflow: hidden;
        }
        
        .filter-header { background-color: #f8fafc; padding: 15px 20px; border-bottom: 1px solid #f1f5f9; color: var(--text-dark); font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; }
        .filter-body { padding: 20px; }
        
        /* Table Styling */
        .table thead th { background-color: #f8fafc; color: var(--text-muted); border-bottom: 2px solid #e2e8f0; border-top: none; padding: 1rem 1.2rem; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; white-space: nowrap; }
        .table tbody td { padding: 1.2rem; vertical-align: middle; color: var(--text-dark); border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; font-weight: 500; }
        .table-hover tbody tr:hover { background-color: #f8fafc; transition: var(--transition); }
        
        /* Buttons & Badges */
        .btn-white { background-color: #fff; color: var(--custom-maroon); border: none; border-radius: 8px; padding: 0.6rem 1.2rem; font-weight: 600; transition: var(--transition); display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); text-decoration: none;}
        .btn-white:hover { background-color: #f1f5f9; transform: translateY(-1px); color: var(--custom-maroon-light); }
        
        .badge-internal { background-color: #dbeafe; color: #1e40af; padding: 5px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700;}
        .badge-eksternal { background-color: #fce7f3; color: #9d174d; padding: 5px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700;}
        
        /* Action Buttons */
        .action-group { display: flex; justify-content: center; gap: 6px; flex-wrap: wrap; }
        .action-btn { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; border: none; text-decoration: none; transition: var(--transition); font-size: 1rem;}
        .btn-detail { background: #f3e8ff; color: #9333ea; } .btn-detail:hover { background: #9333ea; color: white; transform: translateY(-2px);}
        .btn-peserta { background: #e0e7ff; color: #4f46e5; } .btn-peserta:hover { background: #4f46e5; color: white; transform: translateY(-2px);}
        .btn-info-custom { background: #e0f2fe; color: #0284c7; } .btn-info-custom:hover { background: #0284c7; color: white; transform: translateY(-2px);}
        .btn-edit { background: #fff7ed; color: #ea580c; } .btn-edit:hover { background: #ea580c; color: white; transform: translateY(-2px);}
        .btn-delete { background: #fef2f2; color: #dc2626; } .btn-delete:hover { background: #dc2626; color: white; transform: translateY(-2px);}
        
        /* Link Copy Box */
        .link-box { display: flex; align-items: center; background: #f1f5f9; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; }
        .link-box input { border: none; background: transparent; padding: 6px 12px; font-size: 0.8rem; color: var(--text-muted); width: 120px; outline: none; }
        .link-box button { border: none; background: #e2e8f0; color: var(--text-dark); padding: 6px 12px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .link-box button:hover { background: #cbd5e1; }

        /* Select Status Badge */
        .status-select { font-size: 0.75rem; font-weight: 700; border-radius: 50px; padding: 4px 25px 4px 12px; border: none; cursor: pointer; appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='currentColor' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; background-size: 10px; }
        .status-select:focus { outline: none; box-shadow: 0 0 0 3px rgba(0,0,0,0.1); }
        .status-buka { background-color: #dcfce7; color: #166534; }
        .status-tutup { background-color: #fee2e2; color: #991b1b; }
        .status-otomatis { background-color: #fef9c3; color: #854d0e; }

        .animate-up { animation: fadeInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; transform: translateY(15px); }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="row animate-up">
        <div class="col-12">
            
            {{-- Header Mewah --}}
            <div class="header-card d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-calendar2-check-fill me-2"></i> Data Pelatihan</h3>
                    <p class="mb-0 opacity-75 small">Kelola seluruh jadwal pelatihan, target peserta, dan kontrol absensi terpadu.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn-white text-dark" onclick="downloadTemplateCSV()"><i class="bi bi-file-earmark-arrow-down"></i> Template</button>
                    <button class="btn-white text-dark" data-bs-toggle="modal" data-bs-target="#importModal"><i class="bi bi-file-earmark-excel"></i> Import</button>
                    <button class="btn-white text-dark" onclick="exportTableToExcel()"><i class="bi bi-box-arrow-up-right"></i> Export</button>
                    <a href="{{ route('admin.kegiatan.create') }}" class="btn-white"><i class="bi bi-plus-circle-fill"></i> Buat Kegiatan Baru</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success shadow-sm border-0 mb-4 rounded-3"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</div>
            @endif

            {{-- Pencarian --}}
            <div class="custom-card">
                <div class="filter-header"><i class="bi bi-search"></i> Pencarian Kegiatan</div>
                <div class="filter-body">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-funnel"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Ketik nama kegiatan atau penyelenggara untuk memfilter tabel secara instan...">
                    </div>
                </div>
            </div>

            {{-- Tabel Data --}}
            <div class="custom-card mb-4">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="kegiatanTable">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Informasi Kegiatan</th>
                                <th>Waktu & Platform</th>
                                <th class="text-center">Status Absen</th>
                                <th class="text-center">Link Form</th>
                                <th class="text-center" width="22%" data-exclude="true">Aksi Panel</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @forelse ($kegiatan as $index => $item)
                                <tr class="data-row">
                                    <td class="text-center text-muted fw-bold row-no">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark search-target mb-1">{{ $item->nama_kegiatan }}</div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge {{ $item->jenis_kegiatan == 'internal' ? 'badge-internal' : 'badge-eksternal' }}">
                                                {{ strtoupper($item->jenis_kegiatan) }}
                                            </span>
                                            <span class="small text-muted search-target"><i class="bi bi-building"></i> {{ $item->penyelenggara->nama_instansi ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-dark mb-1"><i class="bi bi-calendar-event text-danger me-1"></i> {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y, H:i') }}</div>
                                        <div class="small text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $item->platform }}</div>
                                    </td>
                                    
                                    <td class="text-center">
                                        <form action="{{ route('admin.kegiatan.update_status', $item->id) }}" method="POST" class="m-0">
                                            @csrf @method('PATCH')
                                            <select name="status_absen" onchange="this.form.submit()" 
                                                class="status-select shadow-sm {{ $item->status_absen == 'buka' ? 'status-buka' : ($item->status_absen == 'tutup' ? 'status-tutup' : 'status-otomatis') }}">
                                                <option value="otomatis" {{ $item->status_absen == 'otomatis' ? 'selected' : '' }}>Otomatis</option>
                                                <option value="buka" {{ $item->status_absen == 'buka' ? 'selected' : '' }}>Buka</option>
                                                <option value="tutup" {{ $item->status_absen == 'tutup' ? 'selected' : '' }}>Tutup</option>
                                            </select>
                                        </form>
                                    </td>

                                    <td class="text-center">
                                        <div class="link-box mx-auto shadow-sm" style="width: max-content;">
                                            <input type="text" value="{{ route('public.kegiatan.absen', $item->token_absensi) }}" readonly id="link-{{ $item->id }}">
                                            <button type="button" onclick="copyLink('link-{{ $item->id }}')" title="Salin Link"><i class="bi bi-clipboard-check"></i> Copy</button>
                                        </div>
                                    </td>

                                    <td class="text-center" data-exclude="true">
                                        <div class="action-group">
    {{-- Tombol Lihat Detail Modal --}}
    <button type="button" class="action-btn btn-detail" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}" title="Detail Kegiatan"><i class="bi bi-eye-fill"></i></button>
    
    {{-- TOMBOL BARU: SETTING PENILAIAN FASILITATOR --}}
    <a href="{{ route('admin.kegiatan.penilaian.setting', $item->id) }}" class="action-btn" style="background: #fef08a; color: #a16207;" title="Setting Penilaian (Fasilitator & Checklist)"><i class="bi bi-gear-fill"></i></a>
    <a href="{{ route('admin.kegiatan.penilaian.rekap', $item->id) }}" class="action-btn" style="background: #bbf7d0; color: #15803d;" title="Rekap Nilai & Kelulusan"><i class="bi bi-clipboard2-check-fill"></i></a>
    
    <a href="{{ route('admin.kegiatan.peserta.index', $item->id) }}" class="action-btn btn-peserta" title="Kelola Peserta"><i class="bi bi-people-fill"></i></a>
    <a href="{{ route('admin.kegiatan.rekap', $item->id) }}" class="action-btn btn-info-custom" title="Lihat Rekap"><i class="bi bi-bar-chart-fill"></i></a>
    <a href="{{ route('admin.kegiatan.edit', $item->id) }}" class="action-btn btn-edit" title="Edit Data"><i class="bi bi-pencil-square"></i></a>
    <form action="{{ route('admin.kegiatan.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
        @csrf @method('DELETE')
        <button type="button" class="action-btn btn-delete btn-submit-delete" title="Hapus Kegiatan"><i class="bi bi-trash3-fill"></i></button>
    </form>
</div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyRow">
                                    <td colspan="6" class="text-center py-5">
                                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="mb-3 opacity-50">
                                        <h6 class="text-muted fw-bold">Belum ada data kegiatan</h6>
                                        <p class="small text-muted">Silakan buat kegiatan baru untuk memulai.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL KEGIATAN (Dikeluarkan dari dalam table HTML) --}}
    @foreach ($kegiatan as $item)
        <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content" style="border-radius: var(--card-radius); border: none;">
                    <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--custom-maroon) 0%, var(--custom-maroon-light) 100%);">
                        <h5 class="modal-title fw-bold" id="detailModalLabel{{ $item->id }}">
                            <i class="bi bi-info-circle-fill me-2"></i> Detail Informasi Kegiatan
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        
                        <h4 class="fw-bold text-dark mb-2">{{ $item->nama_kegiatan }}</h4>
                        <div class="mb-4 d-flex gap-2 flex-wrap">
                            <span class="badge {{ $item->jenis_kegiatan == 'internal' ? 'badge-internal' : 'badge-eksternal' }}">
                                {{ strtoupper($item->jenis_kegiatan) }}
                            </span>
                            <span class="badge bg-light text-dark border"><i class="bi bi-building me-1"></i> {{ $item->penyelenggara->nama_instansi ?? '-' }}</span>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark border-bottom pb-2">Deskripsi & Latar Belakang</h6>
                            <p class="text-muted small" style="white-space: pre-line;">{{ $item->deskripsi ?? 'Tidak ada deskripsi tertulis.' }}</p>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded-3 h-100 border">
                                    <h6 class="fw-bold text-dark mb-3">Waktu & Tempat</h6>
                                    <ul class="list-unstyled small text-muted mb-0">
                                        <li class="mb-2"><i class="bi bi-calendar-check text-danger me-2"></i> <strong>Mulai:</strong> {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d F Y, H:i') }}</li>
                                        <li class="mb-2"><i class="bi bi-calendar-x text-danger me-2"></i> <strong>Selesai:</strong> {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d F Y, H:i') }}</li>
                                        <li class="mb-2"><i class="bi bi-geo-alt-fill text-danger me-2"></i> <strong>Lokasi:</strong> {{ $item->platform }}</li>
                                        <li class="mb-2"><i class="bi bi-clock-fill text-danger me-2"></i> <strong>Total JPL:</strong> {{ $item->jpl ?? '-' }} JPL</li>
                                        <li><i class="bi bi-person-lines-fill text-danger me-2"></i> <strong>Sistem Absen:</strong> {{ $item->tipe_absen == 'masuk_keluar' ? 'Datang & Pulang (2x)' : 'Hanya Datang (1x)' }}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded-3 h-100 border">
                                    <h6 class="fw-bold text-dark mb-3">Tujuan Kegiatan</h6>
                                    @if(isset($item->tujuan) && $item->tujuan->count() > 0)
                                        <ul class="small text-muted ps-3 mb-0">
                                            @foreach($item->tujuan as $t)
                                                <li class="mb-1">{{ $t->tujuan }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted small mb-0"><em>Tidak ada daftar tujuan spesifik.</em></p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <h6 class="fw-bold text-dark border-bottom pb-2">Kompetensi & Indikator Keberhasilan</h6>
                            {{-- BLOK LINK FASILITATOR --}}
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark border-bottom pb-2">Link Penilaian Fasilitator</h6>
                            @if(isset($item->fasilitator) && $item->fasilitator->count() > 0)
                                <div class="d-flex flex-column gap-2">
                                    @foreach($item->fasilitator as $fasil)
                                        <div class="d-flex align-items-center justify-content-between bg-white p-2 rounded border shadow-sm">
                                            <div>
                                                <div class="small fw-bold text-dark"><i class="bi bi-person-badge text-primary me-1"></i> {{ $fasil->nama_fasilitator }}</div>
                                            </div>
                                            <div class="link-box" style="width: max-content; background: #f8fafc;">
                                                <input type="text" 
                                                       value="{{ route('fasilitator.penilaian.index', $fasil->token_fasilitator) }}" 
                                                       readonly 
                                                       id="link-fasil-{{ $fasil->id }}">
                                                <button type="button" onclick="copyLink('link-fasil-{{ $fasil->id }}')" title="Salin Link">
                                                    <i class="bi bi-clipboard-check"></i> Copy
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted small mb-0"><em>Belum ada fasilitator yang ditambahkan untuk kegiatan ini.</em></p>
                            @endif
                        </div>
                        {{-- END BLOK LINK FASILITATOR --}}
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="small text-dark" width="35%">Kompetensi Dasar</th>
                                            <th class="small text-dark">Indikator Pencapaian (Target)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($item->kompetensi) && $item->kompetensi->count() > 0)
                                            @foreach($item->kompetensi as $k)
                                                <tr>
                                                    <td class="small fw-bold text-dark">{{ $k->nama_kompetensi }}</td>
                                                    <td class="small text-muted">{{ $k->pivot->indikator_keberhasilan ?? 'Sesuai standar umum.' }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="2" class="text-center small text-muted py-3"><em>Belum ada kompetensi yang dilampirkan pada kegiatan ini.</em></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer bg-light p-3 border-top-0">
                        <button type="button" class="btn btn-secondary border px-4" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modal Import Excel --}}
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: var(--card-radius); border: none;">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--custom-maroon) 0%, var(--custom-maroon-light) 100%);">
                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-excel me-2"></i> Import Kegiatan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="importForm">
                    <div class="modal-body p-4">
                        <label class="form-label fw-bold text-dark small">PILIH FILE EXCEL/CSV</label>
                        <input type="file" id="excel_file" class="form-control mb-3" accept=".xlsx, .csv" required>
                        <div class="alert alert-warning small border-0 shadow-sm mb-0">
                            <strong>Format Header Wajib:</strong> <br>
                            Nama Kegiatan, Jenis Kegiatan, Tanggal Mulai, Tanggal Selesai, Penyelenggara, Platform, Tipe Absen
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-3 border-top-0">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn text-white px-4" style="background-color: var(--custom-maroon);" id="btnSubmitImport">Proses Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete Confirm
            document.querySelectorAll('.btn-submit-delete').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault(); 
                    Swal.fire({ 
                        title: 'Hapus Kegiatan?', 
                        text: "Data absensi & peserta terkait akan hilang permanen!", 
                        icon: 'warning', 
                        showCancelButton: true, 
                        confirmButtonColor: '#dc2626', 
                        cancelButtonColor: '#64748b',
                        confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus' 
                    }).then((r) => { 
                        if (r.isConfirmed) this.closest('form').submit(); 
                    });
                });
            });

            // Live Search Sederhana
            const searchInput = document.getElementById('searchInput');
            const allRows = Array.from(document.querySelectorAll('#tableBody .data-row'));
            
            searchInput.addEventListener('input', function() {
                let query = this.value.toLowerCase().trim();
                let visibleCount = 0;
                
                allRows.forEach(r => {
                    let text = Array.from(r.querySelectorAll('.search-target')).map(el => el.innerText.toLowerCase()).join(' ');
                    if(text.includes(query)) {
                        r.style.display = '';
                        visibleCount++;
                        r.querySelector('.row-no').innerText = visibleCount;
                    } else {
                        r.style.display = 'none';
                    }
                });

                if(visibleCount === 0 && query !== '') {
                    document.getElementById('emptyRow').style.display = '';
                } else {
                    document.getElementById('emptyRow').style.display = 'none';
                }
            });

            // Import Handler
            document.getElementById('importForm').addEventListener('submit', function(e) {
                e.preventDefault(); const file = document.getElementById('excel_file').files[0]; if (!file) return;
                const btn = document.getElementById('btnSubmitImport'); btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';
                const reader = new FileReader();
                reader.onload = function(e) {
                    const workbook = XLSX.read(new Uint8Array(e.target.result), { type: 'array' });
                    const jsonData = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]]);
                    const formattedData = jsonData.map(row => ({
                        nama_kegiatan: row['Nama Kegiatan'], jenis_kegiatan: row['Jenis Kegiatan'], tanggal_mulai: row['Tanggal Mulai'], tanggal_selesai: row['Tanggal Selesai'], penyelenggara: row['Penyelenggara'], platform: row['Platform'], tipe_absen: row['Tipe Absen']
                    })).filter(row => row.nama_kegiatan);
                    fetch("{{ route('admin.kegiatan.import') }}", { method: "POST", headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" }, body: JSON.stringify({ data: formattedData }) })
                    .then(r => r.json()).then(r => { if (r.success) Swal.fire('Sukses!', r.message, 'success').then(() => location.reload()); })
                    .catch(e => Swal.fire('Error', 'Gagal memproses ke server.', 'error')).finally(() => { btn.disabled = false; btn.innerText = 'Proses Import'; });
                }; reader.readAsArrayBuffer(file);
            });
        });

        // Copy Link
        function copyLink(inputId) { 
            const copyText = document.getElementById(inputId);
            copyText.select();
            copyText.setSelectionRange(0, 99999); // Untuk Mobile
            navigator.clipboard.writeText(copyText.value).then(() => { 
                Swal.fire({toast: true, position: 'top-end', icon: 'success', title: 'Tersalin ke Clipboard!', showConfirmButton: false, timer: 1500}); 
            }); 
        }

        // Export Excel
        function exportTableToExcel() { let table = document.getElementById('kegiatanTable').cloneNode(true); table.querySelectorAll('[data-exclude="true"]').forEach(el => el.remove()); XLSX.writeFile(XLSX.utils.table_to_book(table), "Data_Kegiatan_RSUD_SLG.xlsx"); }
        
        // Template CSV
        function downloadTemplateCSV() { const csvContent = "data:text/csv;charset=utf-8,Nama Kegiatan,Jenis Kegiatan,Tanggal Mulai,Tanggal Selesai,Penyelenggara,Platform,Tipe Absen\nRapat Rutin,Internal,2026-06-15 08:00,2026-06-15 12:00,RSUD Simpang Lima Gumul,Ruang Rapat Utama,Masuk Saja\n"; const link = document.createElement("a"); link.setAttribute("href", encodeURI(csvContent)); link.setAttribute("download", "Template_Kegiatan.csv"); document.body.appendChild(link); link.click(); document.body.removeChild(link); }
    </script>
@endsection