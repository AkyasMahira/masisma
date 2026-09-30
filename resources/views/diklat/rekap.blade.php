@extends('layouts.app')

@section('content')
{{-- 1. LIBRARY --}}
{{-- jQuery & Bootstrap Bundle (Wajib urutan ini) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>-->

{{-- Select2 (Dropdown Search) --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">-->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{-- Excel & PDF --}}
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

{{-- FontAwesome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --primary: #7c1316;
        --primary-soft: #fdf2f2;
        --text-main: #2d3748;
        --text-muted: #718096;
        --border: #e2e8f0;
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

    body { background-color: #f7fafc; font-family: 'Poppins', sans-serif; color: var(--text-main); }

    /* Card Styling */
    .card-box {
        background: white; border-radius: 12px; border: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px; position: relative; overflow: hidden;
    }
    .card-box::after {
        content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: var(--primary);
    }

    /* Header */
    .page-header-title { font-size: 24px; font-weight: 700; color: var(--primary); margin-bottom: 4px; }
    .page-header-subtitle { font-size: 14px; color: var(--text-muted); }

    /* Button Styling */
    .btn-maroon {
        background-color: var(--primary); color: white; border-radius: 8px; padding: 10px 20px;
        font-weight: 500; font-size: 14px; border: none; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-maroon:hover { background-color: #631113; color: white; text-decoration: none; transform: translateY(-1px); }

    .btn-outline-back {
        background: white; border: 1px solid #cbd5e0; color: var(--text-main); border-radius: 8px; padding: 9px 18px; font-weight: 500; font-size: 14px;
    }
    .btn-outline-back:hover { background: #f7fafc; text-decoration: none; color: var(--text-main); }

    /* Select2 Custom */
    .select2-container--bootstrap4 .select2-selection {
        border: 1px solid #cbd5e0 !important; border-radius: 8px !important; height: 42px !important; padding: 6px 12px !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder { color: #a0aec0; line-height: 28px; }

    /* Switcher (Toggle) */
    .view-switcher {
        background: #edf2f7; padding: 4px; border-radius: 8px; display: inline-flex;
    }
    .view-btn {
        padding: 8px 20px; border-radius: 6px; border: none; background: transparent; color: var(--text-muted);
        font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s;
    }
    .view-btn.active { background: white; color: var(--primary); box-shadow: 0 1px 3px rgba(0,0,0,0.1); }

    /* Table Styling */
    .table-modern { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-modern thead th {
        background: #f8fafc; color: #4a5568; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;
        padding: 16px; border-bottom: 2px solid #edf2f7; font-weight: 700;
    }
    .table-modern tbody td { padding: 16px; border-bottom: 1px solid #edf2f7; font-size: 13px; vertical-align: middle; }
    .table-modern tbody tr:last-child td { border-bottom: none; }
    .table-modern tbody tr:hover { background-color: #fffbfc; }

    /* Badges */
    .badge-capsule { padding: 4px 10px; border-radius: 50px; font-size: 11px; font-weight: 600; }
    .badge-capsule-primary { background: #ebf8ff; color: #2b6cb0; }
    .badge-capsule-success { background: #f0fff4; color: #2f855a; }
    .badge-capsule-secondary { background: #edf2f7; color: #4a5568; }

    /* Modal Detail List */
    .detail-list-item {
        display: flex; justify-content: space-between; align-items: center;
        padding: 12px 15px; border-bottom: 1px solid #f1f1f1;
    }
    .detail-list-item:last-child { border-bottom: none; }
    .detail-avatar {
        width: 35px; height: 35px; background: var(--primary-soft); color: var(--primary);
        border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px; margin-right: 12px;
    }
</style>

<div class="container-fluid py-4">

    {{-- HEADER AREA --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="page-header-title">Rekapitulasi Data</h1>
            <p class="page-header-subtitle">Formulir: <strong>{{ $form->judul }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('diklat.index') }}" class="btn-outline-back" style="text-decoration:none;">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            <button onclick="exportToExcel()" class="btn-maroon" style="background:#217346;">
                <i class="fas fa-file-excel"></i> Excel
            </button>
            <button onclick="exportToPDF()" class="btn-maroon" style="background:#c53030;">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
        </div>
    </div>

    {{-- FILTER AREA --}}
    <div class="card-box p-4">
        <form action="{{ route('diklat.rekap', $form->id) }}" method="GET">
            <div class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label class="small font-weight-bold mb-2">Cari Data</label>
                    <input type="text" name="search" class="form-control" style="height:42px; border-radius:8px;" 
                           value="{{ request('search') }}" placeholder="Nama / NIK / HP...">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="small font-weight-bold mb-2">Filter Instansi</label>
                    <select name="filter_instansi" class="form-control select2">
                        <option value="">Semua Instansi</option>
                        @foreach($listInstansi as $inst)
                            <option value="{{ $inst }}" {{ request('filter_instansi') == $inst ? 'selected' : '' }}>{{ $inst }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="small font-weight-bold mb-2">Filter Pelatihan</label>
                    <select name="filter_pelatihan" class="form-control select2">
                        <option value="">Semua Pelatihan</option>
                        @foreach($form->opsi_pelatihan as $opsi)
                            <option value="{{ $opsi }}" {{ request('filter_pelatihan') == $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-maroon w-100 justify-content-center">
                            <i class="fas fa-filter"></i> Terapkan
                        </button>
                        @if(request()->hasAny(['search', 'filter_instansi', 'filter_pelatihan']))
                            <a href="{{ route('diklat.rekap', $form->id) }}" class="btn btn-light border text-danger d-flex align-items-center justify-content-center" style="width: 42px; border-radius:8px;">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- TOGGLE MODE VIEW --}}
    <div class="text-center mb-4">
        <div class="view-switcher">
            <button class="view-btn active" id="btn-peserta" onclick="switchView('peserta')">
                <i class="fas fa-user mr-1"></i> Data Per Peserta
            </button>
            <button class="view-btn" id="btn-instansi" onclick="switchView('instansi')">
                <i class="fas fa-building mr-1"></i> Rekap Per Instansi
            </button>
        </div>
    </div>

    {{-- VIEW 1: TABEL DATA PESERTA --}}
    <div id="view-peserta" class="card-box animate__animated animate__fadeIn">
        <div class="table-responsive">
            <table class="table-modern" id="tablePeserta">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="20%">Nama Peserta</th>
                        <th width="20%">Instansi</th>
                        <th width="25%">Pelatihan</th>
                        <th width="15%">Kontak</th>
                        <th class="text-center d-print-none" width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesertas as $key => $p)
                    <tr>
                        <td class="text-center font-weight-bold">{{ $pesertas->firstItem() + $key }}</td>
                        <td>
                            <div class="font-weight-bold text-dark">{{ $p->nama_lengkap }}</div>
                            <div class="small text-muted">{{ $p->nik }}</div>
                        </td>
                        <td>
                            <div class="font-weight-bold">{{ $p->instansi }}</div>
                            <div class="small text-muted">{{ $p->jabatan }}</div>
                        </td>
                        <td>
                            @if(is_array($p->pilihan_pelatihan))
                                @foreach($p->pilihan_pelatihan as $pl)
                                    <div class="badge-capsule badge-capsule-primary d-inline-block mb-1">{{ $pl }}</div>
                                @endforeach
                            @else - @endif
                        </td>
                        <td>
                            <div class="small"><i class="fab fa-whatsapp text-success mr-1"></i> {{ $p->no_hp }}</div>
                        </td>
                        <td class="text-center d-print-none">
                            <button type="button" class="btn btn-sm btn-info rounded-circle shadow-sm btn-detail-peserta" 
                                    data-json="{{ json_encode($p) }}" title="Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <form action="{{ route('diklat.peserta.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger rounded-circle shadow-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $pesertas->links() }}
        </div>
    </div>

    {{-- VIEW 2: TABEL REKAP INSTANSI --}}
    <div id="view-instansi" class="card-box animate__animated animate__fadeIn" style="display: none;">
        <div class="table-responsive">
            <table class="table-modern" id="tableInstansi">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="30%">Nama Instansi</th>
                        <th width="20%">Penanggung Jawab</th>
                        <th width="15%">Total Peserta</th>
                        <th width="20%" class="text-center d-print-none">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse($rekapInstansi as $namaInstansi => $groupPeserta)
                    {{-- Ambil data PJ dari orang pertama di grup --}}
                    @php $pj = $groupPeserta->first(); @endphp 
                    <tr>
                        <td class="text-center font-weight-bold">{{ $no++ }}</td>
                        <td>
                            <div class="font-weight-bold text-dark">{{ $namaInstansi }}</div>
                            <div class="small text-muted"><i class="fas fa-map-marker-alt mr-1"></i> {{ Str::limit($pj->alamat, 50) }}</div>
                        </td>
                        <td>
                            <div class="small text-dark font-weight-bold">{{ $pj->email }}</div>
                            <div class="small text-muted">{{ $pj->no_hp }}</div>
                        </td>
                        <td>
                            <span class="badge-capsule badge-capsule-success px-3">{{ $groupPeserta->count() }} Orang</span>
                        </td>
                        <td class="text-center d-print-none">
                            {{-- TOMBOL DETAIL INSTANSI (MUNCULKAN LIST PESERTA) --}}
                            <button type="button" class="btn btn-sm btn-light border shadow-sm btn-detail-instansi"
                                    data-instansi="{{ $namaInstansi }}"
                                    data-pesertas="{{ json_encode($groupPeserta) }}">
                                <i class="fas fa-list mr-1"></i> Lihat Peserta
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- MODAL 1: LIST PESERTA PER INSTANSI --}}
<div class="modal fade" id="modalListPeserta" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0" style="border-radius: 12px;">
            <div class="modal-header bg-light border-0">
                <h6 class="modal-title font-weight-bold">Daftar Peserta: <span id="titleInstansi" class="text-primary"></span></h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-0" style="max-height: 400px; overflow-y: auto;">
                <div id="containerListPeserta"></div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 2: DETAIL BIODATA PESERTA --}}
<div class="modal fade" id="modalDetailPeserta" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;"> {{-- z-index tinggi biar di atas modal 1 --}}
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0" style="border-radius: 12px;">
            <div class="modal-header bg-dark text-white border-0" style="border-radius: 12px 12px 0 0;">
                <h6 class="modal-title font-weight-bold">Biodata Lengkap</h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div style="width: 60px; height: 60px; background: #ffebee; color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                        <i class="fas fa-user fa-2x"></i>
                    </div>
                    <h5 class="font-weight-bold mb-0" id="d_nama">Nama</h5>
                    <small class="text-muted" id="d_nik">NIK</small>
                </div>

                <table class="table table-sm table-borderless" style="font-size: 13px;">
                    <tr><td class="text-muted" width="35%">Email</td><td class="font-weight-bold" id="d_email"></td></tr>
                    <tr><td class="text-muted">No HP</td><td class="font-weight-bold" id="d_hp"></td></tr>
                    <tr><td class="text-muted">TTL</td><td class="font-weight-bold" id="d_ttl"></td></tr>
                    <tr><td class="text-muted">Jabatan</td><td class="font-weight-bold" id="d_jabatan"></td></tr>
                    <tr><td class="text-muted">Profesi</td><td class="font-weight-bold" id="d_profesi"></td></tr>
                    <tr><td class="text-muted">Pendidikan</td><td class="font-weight-bold" id="d_pendidikan"></td></tr>
                    <tr><td class="text-muted">Status</td><td class="font-weight-bold" id="d_status"></td></tr>
                    <tr><td class="text-muted">Kaos</td><td class="font-weight-bold" id="d_kaos"></td></tr>
                </table>

                <hr>
                <small class="text-muted d-block mb-1">Pelatihan</small>
                <div id="d_pelatihan" class="font-weight-bold text-dark mb-2" style="font-size: 13px;"></div>
                
                <small class="text-muted d-block mb-1">Bukti Bayar</small>
                <div id="d_bukti"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        
        // 1. Init Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // 2. Switcher Logic
        window.switchView = function(view) {
            $('.view-btn').removeClass('active');
            $('#btn-' + view).addClass('active');
            $('#view-peserta, #view-instansi').hide();
            $('#view-' + view).fadeIn();
        };

        // 3. Logic: Klik "Lihat Peserta" di Tabel Instansi
        $('.btn-detail-instansi').click(function() {
            let instansi = $(this).data('instansi');
            let pesertas = $(this).data('pesertas'); // Array Object Peserta
            
            $('#titleInstansi').text(instansi);
            let html = '';

            pesertas.forEach(function(p, index) {
                // Konversi objek p ke JSON string yang aman untuk tombol detail
                // Kita simpan di textarea tersembunyi atau langsung di attribute (hati2 quote)
                // Cara aman: gunakan btoa untuk encode base64 jika data kompleks, atau simpan di global array.
                // Disini kita pakai cara simple attribute data-json
                let jsonStr = JSON.stringify(p).replace(/"/g, '&quot;');

                html += `
                <div class="detail-list-item">
                    <div class="d-flex align-items-center">
                        <div class="detail-avatar">${index+1}</div>
                        <div>
                            <div class="font-weight-bold text-dark" style="font-size:14px;">${p.nama_lengkap}</div>
                            <div class="small text-muted">${p.jabatan}</div>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-info btn-detail-peserta-nested" data-json="${jsonStr}">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>`;
            });

            $('#containerListPeserta').html(html);
            $('#modalListPeserta').modal('show');
        });

        // 4. Logic: Klik "Mata" (Detail Biodata) - Baik dari tabel utama maupun dari modal list
        $(document).on('click', '.btn-detail-peserta, .btn-detail-peserta-nested', function() {
            let data = $(this).data('json');
            
            $('#d_nama').text(data.nama_lengkap);
            $('#d_nik').text(data.nik);
            $('#d_email').text(data.email);
            $('#d_hp').text(data.no_hp);
            $('#d_ttl').text((data.tempat_lahir || '-') + ', ' + (data.tanggal_lahir || '-'));
            $('#d_jabatan').text(data.jabatan);
            $('#d_profesi').text(data.profesi);
            $('#d_pendidikan').text(data.pendidikan_terakhir);
            $('#d_status').text(data.status_pegawai);
            $('#d_kaos').text(data.ukuran_kaos);

            // Pelatihan
            let pel = Array.isArray(data.pilihan_pelatihan) ? data.pilihan_pelatihan.join(', ') : '-';
            $('#d_pelatihan').text(pel);

            // Bukti
            if(data.bukti_pembayaran) {
                let url = "{{ asset('storage') }}/" + data.bukti_pembayaran;
                $('#d_bukti').html(`<a href="${url}" target="_blank" class="btn btn-sm btn-success w-100"><i class="fas fa-download mr-1"></i> Download Bukti</a>`);
            } else {
                $('#d_bukti').html('<span class="text-muted small">Tidak ada bukti</span>');
            }

            $('#modalDetailPeserta').modal('show');
        });

    });

    // 5. Export Excel
    function exportToExcel() {
        let activeId = $('#view-peserta').is(':visible') ? 'tablePeserta' : 'tableInstansi';
        let table = document.getElementById(activeId);
        let clone = table.cloneNode(true);
        
        // Hapus kolom aksi di clone
        clone.querySelectorAll('.d-print-none').forEach(e => e.remove());
        
        let wb = XLSX.utils.table_to_book(clone, {sheet: "Data"});
        XLSX.writeFile(wb, "Rekap_Data_{{ Str::slug($form->judul) }}.xlsx");
    }

    // 6. Export PDF
    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape');
        
        let activeId = $('#view-peserta').is(':visible') ? '#tablePeserta' : '#tableInstansi';
        let title = activeId == '#tablePeserta' ? 'Data Peserta' : 'Rekap Instansi';

        doc.setFontSize(14);
        doc.text(title + ": {{ $form->judul }}", 14, 15);
        
        doc.autoTable({
            html: activeId,
            startY: 25,
            theme: 'grid',
            headStyles: { fillColor: [124, 19, 22] },
            didParseCell: function(data) {
                // Kosongkan kolom aksi di PDF
                if(data.column.index === data.table.columns.length - 1) {
                    data.cell.text = '';
                }
            }
        });
        
        doc.save("Rekap_Data.pdf");
    }
</script>
@endsection