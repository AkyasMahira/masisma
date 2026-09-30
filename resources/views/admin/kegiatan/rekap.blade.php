@extends('layouts.app')
@section('title', 'Dashboard Rekapitulasi Kehadiran')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <style>
        :root { 
            --custom-maroon: #7c1316; 
            --custom-maroon-light: #fef1f2; 
            --card-radius: 16px; 
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        .page-header { background: #fff; border-radius: var(--card-radius); padding: 25px 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 25px; border-left: 5px solid var(--custom-maroon); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;}
        
        .stat-card { border-radius: var(--card-radius); padding: 25px; color: white; display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.08); transition: transform 0.3s;}
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { position: absolute; right: -10px; bottom: -15px; font-size: 6rem; opacity: 0.15; line-height: 1;}
        .stat-value { font-size: 2.5rem; font-weight: 800; line-height: 1; margin: 10px 0 5px 0; z-index: 1; position: relative;}
        .stat-label { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; z-index: 1; position: relative;}
        
        .bg-maroon-gradient { background: linear-gradient(135deg, #7c1316 0%, #9f181c 100%); }
        .bg-success-gradient { background: linear-gradient(135deg, #059669 0%, #10b981 100%); }
        .bg-warning-gradient { background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%); }
        .bg-danger-gradient { background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); }

        .table-card { background: white; border-radius: var(--card-radius); padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid var(--border-color);}
        .table-custom thead th { background-color: #f8fafc; color: var(--text-muted); font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 15px; border-bottom: 2px solid var(--border-color); border-top: none; white-space: nowrap;}
        .table-custom tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; font-weight: 600; color: var(--text-dark);}
        
        .badge-status { padding: 6px 12px; border-radius: 50px; font-weight: 700; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 5px;}
        .bg-hadir-penuh { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;}
        .bg-hadir-separuh { background: #fef9c3; color: #b45309; border: 1px solid #fde047;}
        .bg-izin { background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa;}
        .bg-belum { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;}

        .btn-lihat-foto { background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; font-size: 0.75rem; padding: 5px 12px; border-radius: 6px; font-weight: 700; transition: 0.2s;}
        .btn-lihat-foto:hover { background: #3b82f6; color: white; }

        .chart-container { background: #f8fafc; border-radius: 16px; padding: 20px; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center;}
    </style>

    @php
        $totalTarget = count($pesertaTerdaftar);
        $totalHadir = 0; $totalIzin = 0; $totalBelum = 0;
        
        foreach($pesertaTerdaftar as $p) {
            if($p->absensiAktual) {
                if($p->absensiAktual->status_kehadiran == 'hadir') { $totalHadir++; } 
                else { $totalIzin++; }
            } else { $totalBelum++; }
        }
        $persentase = $totalTarget > 0 ? round(($totalHadir / $totalTarget) * 100) : 0;
    @endphp

    <div class="row">
        <div class="col-12">
            
            {{-- HEADER --}}
            <div class="page-header">
                <div>
                    <h3 class="fw-bold mb-1">Rekapitulasi {{ $kegiatan->nama_kegiatan }}</h3>
                    <p class="mb-0 opacity-75 small fw-bold text-muted"><i class="bi bi-calendar-event me-1"></i> {{ $kegiatan->nama_kegiatan }} (Sistem: {{ strtoupper(str_replace('_', ' ', $kegiatan->tipe_absen)) }})</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline-secondary fw-bold rounded-3"><i class="bi bi-arrow-left"></i> Kembali</a>
                    <button onclick="exportPdfRincian()" class="btn btn-danger fw-bold rounded-3 shadow-sm"><i class="bi bi-file-earmark-pdf"></i> PDF Rincian</button>
                    <button onclick="exportPdfRuangan()" class="btn btn-danger fw-bold rounded-3 shadow-sm"><i class="bi bi-file-earmark-pdf"></i> PDF Ruangan</button>
                    <button onclick="exportRekapExcel()" class="btn btn-success fw-bold rounded-3 shadow-sm"><i class="bi bi-file-earmark-excel"></i> Export Excel</button>
                </div>
            </div>

            {{-- 2 KARTU STATISTIK FILTER (Ruangan & Instansi) --}}
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="table-card">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-door-open-fill me-2"></i>
                            Statistik Ruangan
                        </h5>
                        <select id="filterRuangan">
                            @foreach($rekapRuangan as $r)
                                <option value="{{ $r['nama'] }}">{{ $r['nama'] }}</option>
                            @endforeach
                        </select>
                        <div class="row text-center mt-4">
                            <div class="col-3">
                                <h4 id="ruanganTarget">0</h4>
                                <small>Target</small>
                            </div>
                            <div class="col-3">
                                <h4 id="ruanganHadir" class="text-success">0%</h4>
                                <small>Hadir</small>
                            </div>
                            <div class="col-3">
                                <h4 id="ruanganIzin" class="text-warning">0%</h4>
                                <small>Izin</small>
                            </div>
                            <div class="col-3">
                                <h4 id="ruanganBelum" class="text-danger">0%</h4>
                                <small>Belum</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="table-card">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-building me-2"></i>
                            Statistik Instansi
                        </h5>
                        <select id="filterInstansi">
                            @foreach($rekapInstansi as $r)
                                <option value="{{ $r['nama'] }}">{{ $r['nama'] }}</option>
                            @endforeach
                        </select>
                        <div class="row text-center mt-4">
                            <div class="col-3">
                                <h4 id="instansiTarget">0</h4>
                                <small>Target</small>
                            </div>
                            <div class="col-3">
                                <h4 id="instansiHadir" class="text-success">0%</h4>
                                <small>Hadir</small>
                            </div>
                            <div class="col-3">
                                <h4 id="instansiIzin" class="text-warning">0%</h4>
                                <small>Izin</small>
                            </div>
                            <div class="col-3">
                                <h4 id="instansiBelum" class="text-danger">0%</h4>
                                <small>Belum</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-card">
                
                {{-- Navigasi Pills --}}
                <ul class="nav nav-pills-custom text-dark pt-2 mb-3" id="rekapTabs" role="tablist" style="display:flex; gap:10px;">
                    <li class="nav-item" role="presentation"><button class="nav-link active btn btn-light" data-bs-toggle="tab" data-bs-target="#tab-rincian" type="button"><i class="bi bi-list-check me-1"></i> Peserta</button></li>
                    <li class="nav-item" role="presentation"><button class="nav-link btn btn-light" data-bs-toggle="tab" data-bs-target="#tab-grafik" type="button"><i class="bi bi-bar-chart-fill me-1"></i> Grafik Analitik</button></li>
                    <li class="nav-item" role="presentation"><button class="nav-link btn btn-light" data-bs-toggle="tab" data-bs-target="#tab-instansi" type="button"><i class="bi bi-building me-1"></i> Rekap Instansi</button></li>
                    <li class="nav-item" role="presentation"><button class="nav-link btn btn-light" data-bs-toggle="tab" data-bs-target="#tab-ruangan" type="button"><i class="bi bi-door-open-fill me-1"></i> Rekap Ruangan</button></li>
                </ul>

                <div class="tab-content" id="rekapTabsContent">
                    
                    {{-- TAB 1: RINCIAN PESERTA --}}
                    <div class="tab-pane fade show active" id="tab-rincian" role="tabpanel">
                        <div class="mb-3 d-flex justify-content-end">
                            <div class="input-group" style="max-width: 300px;">
                                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchRincian" class="form-control bg-light" placeholder="Cari nama peserta...">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-custom" id="tableRincian">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th>Nama Lengkap</th>
                                        <th>Instansi / Unit</th>
                                        <th>Waktu Masuk</th>
                                        <th>Waktu Pulang</th>
                                        <th class="text-center">Status Kehadiran</th>
                                        <th class="text-center">Bukti Absen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pesertaTerdaftar as $index => $p)
   @php
    $absen = $p->absensiAktual;
    $statusBadge = '<span class="badge-status bg-belum"><i class="bi bi-dash-circle"></i> Belum Absen</span>';
    $waktuMasuk = '<span class="text-muted">-</span>';
    $waktuPulang = '<span class="text-muted">-</span>';
    $btnFoto = '<button disabled class="btn btn-sm btn-light text-muted border" style="font-size:0.75rem;">Kosong</button>';
    $btnReset = ''; 

    if($absen) {
        if($absen->status_kehadiran == 'tidak_hadir') {
            $statusBadge = '<span class="badge-status bg-izin"><i class="bi bi-file-earmark-medical"></i> Izin / Sakit</span>';
        } 
        elseif($absen->status_kehadiran == 'hadir') {
            
            // 1. CEK WAKTU DATANG (created_at)
            // Jika kosong, cegah sistem menampilkan jam saat ini
            if (!empty($absen->created_at)) {
                $waktuMasuk = \Carbon\Carbon::parse($absen->created_at)->format('H:i:s') . ' WIB';
            } else {
                $waktuMasuk = '<span class="text-danger small fst-italic">Tidak terekam</span>';
            }
            
            // 2. CEK WAKTU PULANG (updated_at)
            if($kegiatan->tipe_absen == 'masuk_keluar') {
                // AMAN: Pastikan updated_at dan created_at sama-sama ada isinya sebelum di-format
                if(!empty($absen->updated_at) && !empty($absen->created_at) && $absen->updated_at->format('Y-m-d H:i:s') !== $absen->created_at->format('Y-m-d H:i:s')) {
                    $waktuPulang = \Carbon\Carbon::parse($absen->updated_at)->format('H:i:s') . ' WIB';
                    $statusBadge = '<span class="badge-status bg-hadir-penuh"><i class="bi bi-check-all"></i> Hadir (Selesai)</span>';
                } else {
                    $waktuPulang = '-';
                    $statusBadge = '<span class="badge-status bg-hadir-separuh"><i class="bi bi-box-arrow-in-right"></i> Hadir (Belum Pulang)</span>';
                }
            } 
            else {
                $statusBadge = '<span class="badge-status bg-hadir-penuh"><i class="bi bi-check-circle-fill"></i> Hadir Penuh</span>';
            }
        }

        if($absen->foto_bukti) {
            $urlFoto = asset($absen->foto_bukti);
            // Menggunakan strip_tags agar tulisan HTML "Tidak terekam" tidak merusak tombol pop-up bukti foto
            $waktuRecord = strip_tags($waktuMasuk);
            $btnFoto = "<button onclick=\"lihatFoto('$urlFoto', '$waktuRecord', '{$p->nama_lengkap_gelar}')\" class='btn-lihat-foto'><i class='bi bi-image me-1'></i> Lihat Bukti</button>";
        } else if ($absen->status_kehadiran == 'tidak_hadir') {
            $btnFoto = '<span class="small text-muted"><i class="bi bi-info-circle"></i> Tanpa Foto (Izin)</span>';
        }

        $routeReset = route('admin.kegiatan.peserta.reset_absen', ['kegiatan_id' => $kegiatan->id, 'id' => $p->id]);
        $csrf = csrf_field();
        $method = method_field('DELETE');

        $btnReset = "
        <form action='{$routeReset}' method='POST' class='d-inline' onsubmit='return confirm(\"Yakin ingin mereset absensi {$p->nama_lengkap_gelar}? Data kehadiran dan foto akan dihapus permanen.\")'>
            {$csrf}
            {$method}
            <button type='submit' class='btn btn-sm btn-outline-danger bg-white' style='font-size: 0.75rem; padding: 5px 12px; border-radius: 6px; font-weight: 700;' title='Reset Absen'>
                <i class='bi bi-arrow-counterclockwise'></i> Reset
            </button>
        </form>";
    }
@endphp
                                        <tr class="row-rincian">
                                            <td class="text-center text-muted row-no">{{ $index + 1 }}</td>
                                            <td class="fw-bold search-target">{{ $p->nama_lengkap_gelar }}</td>
                                            <td class="search-target">
                                                <div style="font-size: 0.85rem;">{{ $p->instansi->nama_instansi ?? 'Internal RS' }}</div>
                                                <div class="small text-muted">{{ $p->ruangan->nama_ruangan ?? '-' }}</div>
                                            </td>
                                            <td><span class="badge bg-light text-dark border">{{ $waktuMasuk }}</span></td>
                                            <td><span class="badge bg-light text-dark border">{{ $waktuPulang }}</span></td>
                                            <td class="text-center">{!! $statusBadge !!}</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center align-items-center gap-2">
                                                    {!! $btnFoto !!}
                                                    {!! $btnReset !!}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada peserta terdaftar.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 2: GRAFIK ANALITIK --}}
                    <div class="tab-pane fade" id="tab-grafik" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mx-auto">
                                <div class="chart-container">
                                    <canvas id="kehadiranChart" width="300" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 3: REKAP INSTANSI --}}
                    <div class="tab-pane fade" id="tab-instansi" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover table-custom" id="tableInstansi">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th>Nama Instansi</th>
                                        <th>Target</th>
                                        <th>Hadir</th>
                                        <th>% Hadir</th>
                                        <th>Izin</th>
                                        <th>% Izin</th>
                                        <th>Belum</th>
                                        <th>% Belum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rekapInstansi as $index => $r)
                                        <tr>
                                            <td class="text-center text-muted">{{ $index + 1 }}</td>
                                            <td class="fw-bold">{{ $r['nama'] }}</td>
                                            <td>{{ $r['target'] }}</td>
                                            <td class="text-success">{{ $r['hadir'] }}</td>
                                            <td class="fw-bold text-success">{{ $r['persen_hadir'] }}%</td>
                                            <td class="text-warning">{{ $r['izin'] }}</td>
                                            <td class="fw-bold text-warning">{{ $r['persen_izin'] }}%</td>
                                            <td class="text-danger">{{ $r['belum'] }}</td>
                                            <td class="fw-bold text-danger">{{ $r['persen_belum'] }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 4: REKAP RUANGAN --}}
                    <div class="tab-pane fade" id="tab-ruangan" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover table-custom" id="tableRuangan">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th>Nama Ruangan / Unit</th>
                                        <th class="text-center">Target</th>
                                        <th class="text-center">Hadir</th>
                                        <th class="text-center">Izin</th>
                                        <th class="text-center">Belum</th>
                                        <th class="text-center">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rekapRuangan as $index => $r)
                                        @php
                                            $persentase = $r['target'] > 0 ? ($r['hadir'] / $r['target']) * 100 : 0;
                                        @endphp
                                        <tr>
                                            <td class="text-center text-muted">{{ $index + 1 }}</td>
                                            <td>{{ $r['nama'] }}</td>
                                            <td class="text-center">{{ $r['target'] }}</td>
                                            <td class="text-center text-success">{{ $r['hadir'] }}</td>
                                            <td class="text-center text-warning">{{ $r['izin'] }}</td>
                                            <td class="text-center text-danger">{{ $r['belum'] }}</td>
                                            <td class="text-center fw-bold text-primary">
                                                {{ number_format($persentase, 1, ',', '.') }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL BUKTI FOTO --}}
    <div class="modal fade" id="modalFoto" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border:none;">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold" id="namaPesertaModal">Bukti Absen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <img id="imgBukti" src="" class="img-fluid rounded-4 shadow-sm mb-3" alt="Foto Kehadiran" style="max-height: 400px; width:100%; object-fit: cover;">
                    <div class="alert alert-success py-2 mb-0 border-0 fw-bold small" id="txtWaktuModal"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const dataRuangan = @json($rekapRuangan);
        const dataInstansi = @json($rekapInstansi);

        document.addEventListener('DOMContentLoaded', function(){
            const ruanganSelect = document.getElementById('filterRuangan');
            const instansiSelect = document.getElementById('filterInstansi');

            new Choices(ruanganSelect,{ searchEnabled:true, itemSelectText:'' });
            new Choices(instansiSelect,{ searchEnabled:true, itemSelectText:'' });

            if(dataRuangan.length){ updateRuangan(dataRuangan[0]); }
            if(dataInstansi.length){ updateInstansi(dataInstansi[0]); }

            ruanganSelect.addEventListener('change', function(){
                const item = dataRuangan.find(x => x.nama === this.value);
                if(item){ updateRuangan(item); }
            });

            instansiSelect.addEventListener('change', function(){
                const item = dataInstansi.find(x => x.nama === this.value);
                if(item){ updateInstansi(item); }
            });
        });

        function updateRuangan(item){
            document.getElementById('ruanganTarget').innerHTML = item.target;
            document.getElementById('ruanganHadir').innerHTML = item.persen_hadir + '%';
            document.getElementById('ruanganIzin').innerHTML = item.persen_izin + '%';
            document.getElementById('ruanganBelum').innerHTML = item.persen_belum + '%';
        }

        function updateInstansi(item){
            document.getElementById('instansiTarget').innerHTML = item.target;
            document.getElementById('instansiHadir').innerHTML = item.persen_hadir + '%';
            document.getElementById('instansiIzin').innerHTML = item.persen_izin + '%';
            document.getElementById('instansiBelum').innerHTML = item.persen_belum + '%';
        }

        // Live Search
        document.getElementById('searchRincian').addEventListener('input', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('.row-rincian');
            let visibleCount = 0;
            rows.forEach(row => {
                let text = row.querySelector('.search-target').textContent.toLowerCase();
                if(text.includes(filter)) {
                    row.style.display = '';
                    visibleCount++;
                    row.querySelector('.row-no').textContent = visibleCount;
                } else {
                    row.style.display = 'none';
                }
            });
        });

        function lihatFoto(url, waktu, nama) {
            document.getElementById('imgBukti').src = url;
            document.getElementById('namaPesertaModal').innerText = 'Foto: ' + nama;
            document.getElementById('txtWaktuModal').innerHTML = '<i class="bi bi-clock-history me-1"></i> Waktu Record Masuk: ' + waktu;
            new bootstrap.Modal(document.getElementById('modalFoto')).show();
        }

        // Chart.js
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('kehadiranChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Izin/Sakit', 'Belum Absen'],
                    datasets: [{
                        data: [{{ $totalHadir }}, {{ $totalIzin }}, {{ $totalBelum }}],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 20, font: {family: 'Plus Jakarta Sans', weight: 'bold'} } }
                    },
                    cutout: '65%'
                }
            });
        });

        // Export Excel
        function exportRekapExcel() {
            let wb = XLSX.utils.book_new();

            function sortSheetAtoZ(sheet, nameColIndex, noColIndex) {
                let data = XLSX.utils.sheet_to_json(sheet, { header: 1 });
                if (data.length <= 1) return sheet; 

                let header = data[0];
                let rows = data.slice(1);
                
                rows.sort((a, b) => {
                    let nameA = (a[nameColIndex] || "").toString().trim().toLowerCase();
                    let nameB = (b[nameColIndex] || "").toString().trim().toLowerCase();
                    return nameA.localeCompare(nameB);
                });

                rows.forEach((row, idx) => {
                    if(row[noColIndex] !== undefined) { row[noColIndex] = idx + 1; }
                });

                return XLSX.utils.aoa_to_sheet([header, ...rows]);
            }

            let tableRincianClone = document.getElementById('tableRincian').cloneNode(true);
            for (let i = 0; i < tableRincianClone.rows.length; i++) {
                if (tableRincianClone.rows[i].cells.length > 0) {
                    tableRincianClone.rows[i].deleteCell(-1);
                }
            }
            let wsRincian = XLSX.utils.table_to_sheet(tableRincianClone);
            wsRincian = sortSheetAtoZ(wsRincian, 1, 0); 
            XLSX.utils.book_append_sheet(wb, wsRincian, "Rincian_Peserta");

            let dataGrafik = [
                ["KATEGORI KEHADIRAN", "JUMLAH PESERTA"],
                ["Hadir", {{ $totalHadir }}],
                ["Izin / Sakit", {{ $totalIzin }}],
                ["Belum Absen", {{ $totalBelum }}],
                [""],
                ["TOTAL TARGET PESERTA", {{ $totalTarget }}]
            ];
            let wsGrafik = XLSX.utils.aoa_to_sheet(dataGrafik);
            XLSX.utils.book_append_sheet(wb, wsGrafik, "Data_Grafik");

            let tableInstansi = document.getElementById('tableInstansi');
            let wsInstansi = XLSX.utils.table_to_sheet(tableInstansi);
            wsInstansi = sortSheetAtoZ(wsInstansi, 1, 0);
            XLSX.utils.book_append_sheet(wb, wsInstansi, "Rekap_Instansi");

            let tableRuangan = document.getElementById('tableRuangan');
            let wsRuangan = XLSX.utils.table_to_sheet(tableRuangan);
            wsRuangan = sortSheetAtoZ(wsRuangan, 1, 0);
            XLSX.utils.book_append_sheet(wb, wsRuangan, "Rekap_Ruangan");

            let fileName = "Rekap_Kehadiran_{{ Str::slug($kegiatan->nama_kegiatan) }}.xlsx";
            XLSX.writeFile(wb, fileName);
        }

        // JS PDF
        const { jsPDF } = window.jspdf;

        function extractTableData(tableId, ignoreLastCol = false) {
            const table = document.getElementById(tableId);
            const rows = Array.from(table.querySelectorAll('tbody tr')).filter(tr => tr.cells.length > 1);
            const data = [];
            rows.forEach(tr => {
                const rowData = [];
                const len = ignoreLastCol ? tr.cells.length - 1 : tr.cells.length;
                for (let i = 0; i < len; i++) {
                    rowData.push(tr.cells[i].innerText.trim().replace(/\n+/g, ' ')); 
                }
                data.push(rowData);
            });
            return data;
        }

        function addSignature(doc, finalY) {
            const pageWidth = doc.internal.pageSize.width;
            const rightMargin = pageWidth - 20;

            if (finalY > doc.internal.pageSize.height - 80) {
                doc.addPage();
                finalY = 20;
            }

            const dateObj = new Date();
            const months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            const tanggalSekarang = `${dateObj.getDate()} ${months[dateObj.getMonth()]} ${dateObj.getFullYear()}`;
            const waktuValid = `${dateObj.getDate()}/${dateObj.getMonth()+1}/${dateObj.getFullYear()} ${dateObj.getHours()}:${dateObj.getMinutes()}`;

            const qr = new QRious({
                value: `Ttd valid pada tanggal ${waktuValid} Oleh Direktur RSUD Simpang Lima Gumul`,
                size: 150
            });
            const qrImage = qr.toDataURL('image/png');

            let currentY = finalY + 15;
            
            doc.setFont("helvetica", "normal");
            doc.setFontSize(10);
            
            doc.text(`Kediri, ${tanggalSekarang}`, rightMargin, currentY, { align: 'right' });
            currentY += 5;
            doc.text("Dokumen disetujui,", rightMargin, currentY, { align: 'right' });
            currentY += 5;
            doc.text("Direktur RSUD Simpang Lima Gumul", rightMargin, currentY, { align: 'right' });

            currentY += 2;
            doc.addImage(qrImage, 'PNG', rightMargin - 25, currentY, 25, 25); 
            currentY += 32;

            doc.setFont("helvetica", "bold");
            doc.text("dr. Tony Widyanto, Sp.OG", rightMargin, currentY, { align: 'right' });
            doc.setFont("helvetica", "normal");
            currentY += 5;
            doc.text("NIP. 19750714 200212 1 006", rightMargin, currentY, { align: 'right' });
        }

        function exportPdfRincian() {
            const doc = new jsPDF('p', 'mm', 'a4');
            const judul = "Rekapitulasi Kehadiran Peserta - {{ $kegiatan->nama_kegiatan }}";

            doc.setFontSize(12);
            doc.setFont("helvetica", "bold");
            
            const splitJudul = doc.splitTextToSize(judul, 180); 
            doc.text(splitJudul, 14, 15);
            const tableStartY = 15 + (splitJudul.length * 6) + 2; 

            const bodyData = extractTableData('tableRincian', true);

            doc.autoTable({
                startY: tableStartY,
                head: [['No', 'Nama Lengkap', 'Instansi / Unit', 'Waktu Masuk', 'Waktu Pulang', 'Status']],
                body: bodyData,
                theme: 'grid',
                styles: { fontSize: 8, cellPadding: 3 },
                headStyles: { fillColor: [124, 19, 22] },
                columnStyles: {
                    0: { halign: 'center', cellWidth: 10 },
                    3: { halign: 'center' },
                    4: { halign: 'center' },
                    5: { halign: 'center' }
                }
            });

            addSignature(doc, doc.lastAutoTable.finalY);
            doc.save("Rincian_Peserta_{{ Str::slug($kegiatan->nama_kegiatan) }}.pdf");
        }

        function exportPdfRuangan() {
            const doc = new jsPDF('p', 'mm', 'a4');
            const judul = "Rekapitulasi Kehadiran Per Ruangan - {{ $kegiatan->nama_kegiatan }}";

            doc.setFontSize(12);
            doc.setFont("helvetica", "bold");
            
            const splitJudul = doc.splitTextToSize(judul, 180); 
            doc.text(splitJudul, 14, 15);
            const tableStartY = 15 + (splitJudul.length * 6) + 2;

            const bodyData = extractTableData('tableRuangan', false);

            doc.autoTable({
                startY: tableStartY,
                head: [['No', 'Nama Ruangan / Unit', 'Target', 'Hadir', 'Izin', 'Belum', 'Persentase']],
                body: bodyData,
                theme: 'grid',
                styles: { fontSize: 8, cellPadding: 3 },
                headStyles: { fillColor: [124, 19, 22] },
                columnStyles: {
                    0: { halign: 'center', cellWidth: 10 },
                    2: { halign: 'center' },
                    3: { halign: 'center' },
                    4: { halign: 'center' },
                    5: { halign: 'center' },
                    6: { halign: 'center', fontStyle: 'bold' }
                }
            });

            addSignature(doc, doc.lastAutoTable.finalY);
            doc.save("Rekap_Ruangan_{{ Str::slug($kegiatan->nama_kegiatan) }}.pdf");
        }
    </script>
@endsection