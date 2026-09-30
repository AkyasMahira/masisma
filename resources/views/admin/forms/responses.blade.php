@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root {
        --primary: #7c1316;
        --primary-light: #fff5f5;
        --dark: #0f172a;
        --slate: #64748b;
        --bg: #f8fafc;
        --radius-lg: 30px;
        --radius-md: 20px;
    }

    body { background-color: var(--bg);  }
    .main-wrapper {  margin: 0 auto; padding: 20px; }

    /* --- HEADER RESPONSIVE --- */
    .dashboard-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px; flex-wrap: wrap; gap: 20px; }
    .header-text h1 { font-size: clamp(24px, 4vw, 34px); font-weight: 900; letter-spacing: -1.5px; margin: 0; }
    
    .btn-group { display: flex; gap: 10px; flex-wrap: wrap; }
    .btn-lux {
        padding: 12px 24px; border-radius: 14px; border: none; font-weight: 800; font-size: 13px;
        cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 8px; text-decoration: none;
    }
    .btn-lux-excel { background: #065f46; color: white; }
    .btn-lux-pdf { background: var(--primary); color: white; }

    /* --- STATS GRID --- */
   /* --- STATS GRID --- */
    .stats-row { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
        gap: 20px; 
        margin-bottom: 40px; 
    }

    .stat-box {
        background: white;
        border-radius: var(--radius-md);
        padding: 30px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
    }

    /* Icon Background */
    .stat-box .bg-icon {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 80px;
        color: var(--primary);
        opacity: 0.05; /* Bikin sangat tipis biar elegan */
        z-index: 0;
        transition: 0.3s ease;
    }

    .stat-box:hover .bg-icon {
        transform: scale(1.1) rotate(-5deg);
        opacity: 0.1;
    }

    /* Content Styling */
    .stat-box .content {
        position: relative;
        z-index: 1;
    }

    .stat-box .label {
        font-size: 11px;
        font-weight: 800;
        color: var(--slate);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 5px;
        display: block;
    }

    .stat-box .value {
        font-size: 42px;
        font-weight: 900;
        color: var(--dark);
        margin: 10px 0;
        line-height: 1;
    }

    /* Status Badges */
    .stat-status {
        font-weight: 800;
        font-size: 10px;
        letter-spacing: 0.5px;
        padding: 4px 10px;
        border-radius: 8px;
        display: inline-block;
    }

    .status-verified {
        background: #ecfdf5;
        color: #059669;
    }

    .status-update {
        background: var(--primary-light);
        color: var(--primary);
    }
    /* --- CHART GRID --- */
    .charts-main-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; margin-bottom: 40px; }
    .charts-sub-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
    
    .chart-tile {
        background: white; border: 1px solid #f1f5f9; border-radius: 22px;
        padding: 20px; min-height: 450px; display: flex; flex-direction: column;
    }
    .canvas-box { flex: 1; position: relative; width: 100%; height: 300px; }

    /* --- OPTION GROUP (PEMATERI DLL) --- */
    .option-header-card {
        background: var(--primary); color: white; padding: 20px 30px;
        border-radius: var(--radius-md) var(--radius-md) 0 0; display: flex; align-items: center; gap: 12px;
    }
    .option-body-card {
        background: white; border: 1px solid #f1f5f9; border-top: none;
        padding: 25px; border-radius: 0 0 var(--radius-md) var(--radius-md); margin-bottom: 40px;
    }

    /* --- TABLE RESPONSIVE --- */
    .table-container { background: white; border-radius: var(--radius-md); overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.03); }
    .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    table { width: 100%; border-collapse: collapse; min-width: 800px; }
    th { padding: 20px; background: #fafafa; font-size: 11px; font-weight: 900; color: #94a3b8; text-transform: uppercase; text-align: left; }
    td { padding: 20px; border-bottom: 1px solid #f8fafc; font-weight: 600; font-size: 14px; }

    /* --- MODAL --- */
    #detailModal {
        display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.8);
        z-index: 9999; backdrop-filter: blur(10px); align-items: center; justify-content: center; padding: 20px;
    }
    .modal-lux-card { background: white; width: 100%; max-width: 850px; border-radius: 30px; overflow: hidden; max-height: 90vh; display: flex; flex-direction: column; }
    .modal-lux-body { padding: 30px; overflow-y: auto; flex: 1; }
    .info-group { background: #f8fafc; padding: 20px; border-radius: 20px; margin-bottom: 15px; border: 1px solid #f1f5f9; }
    .nested-box { margin-top: 15px; padding-left: 20px; border-left: 4px solid #fee2e2; }

    @media (max-width: 768px) {
        .main-wrapper { padding: 10px; }
        .dashboard-header { flex-direction: column; align-items: flex-start; }
        .btn-group { width: 100%; }
        .btn-lux { flex: 1; justify-content: center; }
        .option-header-card h3 { font-size: 14px; }
    }
</style>

<div class="main-wrapper animate-sultan">
    
    <div class="dashboard-header no-print">
        <div class="header-text">
            <h1><span style="color: var(--primary);">Rekapitulasi</span></h1>
            <p style="font-weight: 700; color: var(--slate);">{{ $form->title }}</p>
        </div>
        <div class="btn-group">
            <button onclick="exportToExcel()" class="btn-lux btn-lux-excel"><i class="fas fa-file-excel"></i> EXCEL REPORT</button>
            <a href="{{ route('admin.forms.export_pdf', $form->id) }}" class="btn-lux btn-lux-pdf"><i class="fas fa-file-pdf"></i> SUMMARY PDF</a>
        </div>
    </div>

<div class="stats-row">
    <div class="stat-box">
        <i class="fas fa-users bg-icon"></i>
    
            <span class="label">Total Partisipan</span>
            <div class="value">{{ $form->responses->count() }}</div>
            <span class="stat-status status-verified">
                <i class="fas fa-check-circle mr-1"></i> DATA TERVERIFIKASI
            </span>
       
    </div>

    <div class="stat-box">
        <i class="fas fa-chart-line bg-icon"></i>
       
            <span class="label">Respon Hari Ini</span>
            <div class="value">{{ $form->responses->where('created_at', '>=', now()->startOfDay())->count() }}</div>
            <span class="stat-status status-update">
                <i class="fas fa-sync-alt fa-spin-hover mr-1"></i> UPDATE TERBARU
            </span>
        
    </div>
</div>

    <div class="charts-main-grid no-print">
        @foreach($form->fields as $fIndex => $field)
            @if(in_array($field['type'], ['radio', 'select']))
                @php
                    $labels = []; $values = [];
                    foreach($field['options'] as $opt) {
                        $labels[] = $opt['text'];
                        $c = 0;
                        foreach($form->responses as $r) {
                            $ans = $r->answers['ans_'.$fIndex] ?? null;
                            if(is_array($ans)) { if(in_array($opt['text'], $ans)) $c++; }
                            else { if($ans == $opt['text']) $c++; }
                        }
                        $values[] = $c;
                    }
                @endphp
                <div class="chart-tile">
                    <h4 style="text-align: center; font-size: 30px; color: var(--slate); margin-bottom: 20px;">{{ $field['label'] }}</h4>
                    <div class="canvas-box">
                        <canvas id="main-chart-{{ $fIndex }}"></canvas>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        new Chart(document.getElementById('main-chart-{{ $fIndex }}'), {
                            type: 'doughnut',
                            data: {
                                labels: {!! json_encode($labels) !!},
                                datasets: [{
                                    data: {!! json_encode($values) !!},
                                    backgroundColor: ['#7c1316', '#1e293b', '#475569', '#94a3b8', '#cbd5e1', '#3b82f6', '#10b981'],
                                    borderWidth: 0, hoverOffset: 20
                                }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { weight: 'bold', size: 11 } } } } }
                        });
                    });
                </script>
            @endif
        @endforeach
    </div>

    @foreach($form->fields as $fIndex => $field)
        @if(in_array($field['type'], ['radio', 'select']))
            @foreach($field['options'] as $oIndex => $opt)
                @if(!empty($opt['children']))
                    <div class="option-group-wrapper no-print">
                        <div class="option-header-card">
                            <i class="fas fa-star"></i>
                            <h3 style="margin:0; text-transform: uppercase; letter-spacing: 1px;">{{ $opt['text'] }}</h3>
                        </div>
                        <div class="option-body-card">
                            <div class="charts-sub-grid">
                                @foreach($opt['children'] as $cIndex => $child)
                                    @if($child['type'] === 'choice')
                                        @php
                                            $childLabels = $child['choices'] ?? [];
                                            $childValues = [];
                                            $subKey = "ans_{$fIndex}_sub_" . \Illuminate\Support\Str::slug($opt['text'] ?? '');
                                            foreach($childLabels as $cl) {
                                                $count = 0;
                                                foreach($form->responses as $r) {
                                                    if(isset($r->answers[$subKey][$child['label']]) && $r->answers[$subKey][$child['label']] == $cl) { $count++; }
                                                }
                                                $childValues[] = $count;
                                            }
                                        @endphp
                                        <div class="chart-tile" style="min-height: 380px;">
                                            <h4 style="font-size: 13px; font-weight: 800; color: var(--slate); text-align: center;">{{ $child['label'] }}</h4>
                                            <div class="canvas-box">
                                                <canvas id="sub-chart-{{ $fIndex }}-{{ $oIndex }}-{{ $cIndex }}"></canvas>
                                            </div>
                                        </div>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                new Chart(document.getElementById('sub-chart-{{ $fIndex }}-{{ $oIndex }}-{{ $cIndex }}'), {
                                                    type: 'line',
                                                    data: {
                                                        labels: {!! json_encode($childLabels) !!},
                                                        datasets: [{
                                                            label: 'Skor',
                                                            data: {!! json_encode($childValues) !!},
                                                            borderColor: '#7c1316', backgroundColor: 'rgba(124, 19, 22, 0.1)',
                                                            fill: true, tension: 0.4, borderWidth: 3, pointRadius: 4
                                                        }]
                                                    },
                                                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
                                                });
                                            });
                                        </script>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    @endforeach

    <div class="table-container">
        <div style="padding: 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <h3 style="margin:0; font-weight: 900;">Database Responden</h3>
            <input type="text" id="tableSearch" placeholder="Cari data..." style="padding: 10px 20px; border-radius: 12px; border: 2px solid #f1f5f9; width: 100%; max-width: 300px;">
        </div>
        <div class="table-responsive">
            <table id="responseTable">
                <thead>
                    <tr>
                        <th width="60">NO</th>
                        @foreach(array_slice($form->fields, 0, 3) as $f) <th>{{ $f['label'] }}</th> @endforeach
                        <th>WAKTU</th>
                        <th style="text-align: center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($form->responses as $idx => $res)
                        <tr class="res-row">
                            <td>#{{ $idx + 1 }}</td>
                            @foreach(array_slice($form->fields, 0, 3) as $i => $f)
                                <td>
                                    @php 
                                        $val = $res->answers['ans_'.$i] ?? '-';
                                        $txt = is_array($val) ? implode(', ', $val) : (string)$val;
                                    @endphp
                                    {{ \Illuminate\Support\Str::limit($txt, 40) }}
                                </td>
                            @endforeach
                            <td style="font-size: 12px; color: var(--slate);">{{ $res->created_at->format('d/m/Y H:i') }}</td>
                           <td style="text-align: center; display: flex; gap: 5px; justify-content: center;">
    <button onclick="showDetail({{ $idx }})" class="btn-lux" style="background: #f1f5f9; color: var(--dark); padding: 8px 16px;">
        DETAIL
    </button>
    
    <form action="{{ route('admin.forms.responses.destroy', $res->id) }}" method="POST" id="delete-form-{{ $res->id }}" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="button" onclick="confirmDelete({{ $res->id }})" class="btn-lux" style="background: #fee2e2; color: #b91c1c; padding: 8px 16px;">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="detailModal">
    <div class="modal-lux-card">
        <div class="modal-lux-head">
            <!--<h3 style="margin:0; font-weight: 900;"><i class="fas fa-user-check mr-2"></i> Detail Lengkap</h3>-->
            <button onclick="closeModal()" style="background:none; border:none; color:white; font-size:30px; cursor:pointer;">&times;</button>
        </div>
        <div class="modal-lux-body" id="modalBody"></div>
        <div style="padding: 20px; background: #fafafa; text-align: right; border-top: 1px solid #eee;">
            <button onclick="closeModal()" style="background: var(--dark); color: white; border: none; padding: 12px 30px; border-radius: 12px; cursor: pointer; font-weight: 700;">Tutup</button>
        </div>
    </div>
</div>

<script id="full-data-registry" type="application/json">{!! json_encode($form->responses) !!}</script>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data respon ini akan dihapus permanen dari sistem!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#7c1316',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    })
}
    const fieldSettings = @json($form->fields);
    const registryData = JSON.parse(document.getElementById('full-data-registry').textContent);

    function showDetail(idx) {
    const res = registryData[idx];
    let html = '';

    fieldSettings.forEach((field, i) => {
        const val = res.answers['ans_' + i] || '-';
        let displayVal = '';

        // 1. Logika Deteksi Tipe File
        if (field.type === 'file' && val !== '-') {
            const fileUrl = `/storage/${val}`;
            // Cek apakah file adalah gambar berdasarkan ekstensi
            const isImage = val.match(/\.(jpeg|jpg|gif|png|webp)$/i);
            
            if (isImage) {
                displayVal = `
                    <div style="margin-top:10px;">
                        <img src="${fileUrl}" style="max-width: 100%; max-height: 250px; border-radius: 12px; border: 1px solid #e2e8f0; display: block; margin-bottom: 8px; object-fit: contain;">
                        <a href="${fileUrl}" target="_blank" class="btn-lux" style="background:var(--primary); color:white; padding:6px 12px; font-size:11px; text-decoration:none; display:inline-block; border-radius:8px;">
                            <i class="fas fa-expand"></i> Lihat Ukuran Penuh
                        </a>
                    </div>`;
            } else {
                displayVal = `
                    <a href="${fileUrl}" target="_blank" style="color: var(--primary); font-weight: 800; text-decoration: none; display: flex; align-items: center; gap: 8px; background: #fff5f5; padding: 10px; border-radius: 10px; border: 1px solid #fee2e2;">
                        <i class="fas fa-file-download" style="font-size: 20px;"></i> 
                        <span>Download Dokumen</span>
                    </a>`;
            }
        } else {
            // Jika bukan file, tampilkan teks biasa atau array (untuk checkbox/multiple select)
            displayVal = Array.isArray(val) ? val.join(', ') : val;
        }

        // 2. Render Pertanyaan Utama
        html += `
            <div class="info-group" style="margin-bottom: 20px; padding: 20px; background: #f8fafc; border-radius: 15px; border: 1px solid #edf2f7;">
                <label style="font-size: 11px; font-weight: 900; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px;">${field.label}</label>
                <div style="font-weight: 700; font-size: 16px; margin-top: 8px; color: #1a202c;">${displayVal}</div>
        `;

        // 3. Logika Sub-Jawaban Bertingkat (Anak Pertanyaan)
        if (field.options && Array.isArray(field.options)) {
            field.options.forEach(opt => {
                const optText = opt.text || "";
                // Buat slug manual yang konsisten dengan PHP Str::slug
                const slug = optText.toLowerCase()
                                    .trim()
                                    .replace(/[^\w\s-]/g, '')
                                    .replace(/[\s_-]+/g, '-')
                                    .replace(/^-+|-+$/g, '');
                
                const subKey = `ans_${i}_sub_${slug}`;
                
                if (res.answers[subKey]) {
                    html += `<div style="margin-top:15px; font-size:11px; font-weight:900; color:#94a3b8; border-top:1px dashed #cbd5e0; padding-top:12px; margin-bottom: 10px;">ANALISIS LANJUTAN (${optText.toUpperCase()}):</div>`;
                    
                    for (let [label, subVal] of Object.entries(res.answers[subKey])) {
                        html += `
                            <div class="nested-box" style="margin-left: 15px; border-left: 3px solid #fee2e2; padding-left: 15px; margin-bottom: 12px;">
                                <div style="color:var(--primary); font-weight:900; font-size:10px; text-transform:uppercase;">${label}</div>
                                <div style="font-weight:700; color:#4a5568; font-size:14px; margin-top:2px;">${subVal || '-'}</div>
                            </div>
                        `;
                    }
                }
            });
        }
        
        html += `</div>`; // Tutup info-group
    });

    document.getElementById('modalBody').innerHTML = html;
    document.getElementById('detailModal').style.display = 'flex';
}
    function closeModal() { document.getElementById('detailModal').style.display = 'none'; }
    window.onclick = (e) => { if(e.target == document.getElementById('detailModal')) closeModal(); }

    document.getElementById('tableSearch').addEventListener('keyup', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.res-row').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    function exportToExcel() {
        const table = document.getElementById("responseTable");
        const wb = XLSX.utils.table_to_book(table);
        XLSX.writeFile(wb, "Rekap_Lengkap_{{ \Illuminate\Support\Str::slug($form->title) }}.xlsx");
    }
</script>
@endsection