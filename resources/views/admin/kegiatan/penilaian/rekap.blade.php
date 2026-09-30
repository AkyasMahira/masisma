@extends('layouts.app')
@section('title', 'Rekap Penilaian & Kelulusan')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <style>
        :root {
            --custom-maroon: #7c1316;
            --custom-maroon-dark: #5c0d10;
            --custom-maroon-light: #fef1f2;
            --card-radius: 20px;
            --border-color: #e2e8f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --bg-body: #f8fafc;
        }

        body { background: var(--bg-body); }

        .page-header { background: #fff; border-radius: var(--card-radius); padding: 24px 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 24px; border-left: 5px solid var(--custom-maroon); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }

        /* Stat Cards */
        .stat-card { border-radius: var(--card-radius); padding: 20px; background: #fff; border: 1px solid var(--border-color); box-shadow: 0 4px 15px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 16px; transition: transform 0.3s; height: 100%; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.05); }
        .stat-icon { width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
        .stat-value { font-size: 1.8rem; font-weight: 800; color: var(--text-dark); line-height: 1; margin-bottom: 4px; }
        .stat-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); }

        .icon-maroon { background: var(--custom-maroon-light); color: var(--custom-maroon); }
        .icon-success { background: #ecfdf5; color: #059669; }
        .icon-danger { background: #fef2f2; color: #dc2626; }
        .icon-warning { background: #fffbeb; color: #d97706; }

        /* Filter Card */
        .custom-card { background: #fff; border-radius: var(--card-radius); box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid var(--border-color); overflow: hidden; margin-bottom: 1.5rem; padding: 24px; }
        
        .filter-label { font-size: 0.75rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; display: block; }
        .form-control, .form-select { border-radius: 10px; padding: 10px 14px; border: 1px solid var(--border-color); font-size: 0.95rem; font-weight: 500; color: var(--text-dark); background: var(--bg-body); transition: 0.3s; height: 44px; }
        .form-control:focus, .form-select:focus { border-color: var(--custom-maroon); box-shadow: 0 0 0 4px var(--custom-maroon-light); background: #fff; }

        /* Buttons */
        .btn-theme { background-color: var(--custom-maroon); color: white; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 700; transition: 0.2s; font-size: 0.9rem; text-decoration: none !important; display: inline-flex; align-items: center; justify-content: center; gap: 8px;}
        .btn-theme:hover { background-color: var(--custom-maroon-dark); color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(124, 19, 22, 0.2);}
        .btn-outline-theme { background-color: #fff; color: var(--text-dark); border: 1px solid var(--border-color); padding: 10px 24px; border-radius: 10px; font-weight: 700; transition: 0.2s; font-size: 0.9rem; text-decoration: none !important; display: inline-flex; align-items: center; justify-content: center; gap: 8px;}
        .btn-outline-theme:hover { border-color: var(--custom-maroon); color: var(--custom-maroon); background: var(--bg-body); }
        .btn-excel { background: #10b981; color: #fff; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s; text-decoration: none !important;}
        .btn-excel:hover { background: #059669; color: #fff; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16,185,129,0.2);}
        .btn-excel:disabled { background: #94a3b8; cursor: not-allowed; transform: none; box-shadow: none; }

        /* Table */
        .table-custom { margin-bottom: 0; }
        .table-custom thead th { background-color: var(--bg-body); color: var(--text-muted); font-weight: 800; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 16px; border-bottom: 1px solid var(--border-color); border-top: none; white-space: nowrap; }
        .table-custom tbody td { padding: 16px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; font-weight: 600; color: var(--text-dark); }
        .table-custom tbody tr { transition: 0.2s; }
        .table-custom tbody tr:hover { background-color: var(--custom-maroon-light); }
        .th-materi { min-width: 140px; white-space: normal !important; }

        .badge-status { padding: 6px 14px; border-radius: 50px; font-weight: 700; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; text-transform: uppercase; letter-spacing: 0.5px;}
        .badge-lulus { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .badge-tidak-lulus { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-belum { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }

        .btn-pdf-mini { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 8px; background: #fef1f2; color: var(--custom-maroon); border: 1px solid #fecaca; text-decoration: none !important; margin-left: 8px; font-size: 0.9rem; transition: 0.2s; }
        .btn-pdf-mini:hover { background: var(--custom-maroon); color: #fff; border-color: var(--custom-maroon); }
        
        .table-custom-wrapper { margin: -24px; margin-top: 0; border-top: 1px solid var(--border-color); }
    </style>

    @php
        // 1. FILTER MATERI: HANYA TAMPILKAN YANG BUTUH PENILAIAN (SKILL)
        $materiDinilai = collect($materiList)->where('butuh_penilaian', 1);
        $batas = (float) $batasLulus;

        // 2. KALKULASI RATA-RATA TEORI(POST-TEST) + SKILL / 2
        $rekap = collect($rekap)->map(function($row) use ($materiDinilai, $batas) {
            $totalPersenSkill = 0;
            $jumlahMateri = $materiDinilai->count();
            $semuaSelesai = true; 
            
            // Hitung Rata-rata Skill
            if ($jumlahMateri > 0) {
                foreach($materiDinilai as $m) {
                    $pm = $row['per_materi'][$m->id] ?? null;
                    if($pm && isset($pm['persen'])) {
                        $totalPersenSkill += $pm['persen'];
                    }
                    if (!$pm || empty($pm['lengkap'])) {
                        $semuaSelesai = false;
                    }
                }
                $row['rata_rata_skill'] = round($totalPersenSkill / $jumlahMateri, 2);
            } else {
                $row['rata_rata_skill'] = 0;
            }

            // Ambil Nilai Teori (Post-Test)
            $nilaiTeori = null;
            if (isset($row['nilai_posttest']) && is_numeric($row['nilai_posttest'])) {
                $nilaiTeori = (float)$row['nilai_posttest'];
            } else {
                $semuaSelesai = false; // Jika belum ada nilai Teori/Post-Test, status jadi Belum Selesai
            }

            // Hitung Nilai Akhir = (Teori + Skill) / 2
            if ($semuaSelesai) {
                $row['nilai_akhir'] = round(($nilaiTeori + $row['rata_rata_skill']) / 2, 2);
                
                // LULUS JIKA Nilai Akhir >= Batas Lulus (85)
                if ($row['nilai_akhir'] >= $batas) {
                    $row['status_kelulusan'] = 'Lulus';
                } else {
                    $row['status_kelulusan'] = 'Tidak Lulus';
                }
            } else {
                $row['nilai_akhir'] = 0;
                $row['status_kelulusan'] = 'Belum Selesai Dinilai';
            }

            return $row;
        });

        // 3. FITUR SORTING (Mengurutkan berdasarkan Nilai Akhir)
        $sort = request('sort', 'default');
        if ($sort == 'rata_desc') {
            $rekap = $rekap->sortByDesc('nilai_akhir')->values();
        } elseif ($sort == 'rata_asc') {
            $rekap = $rekap->sortBy('nilai_akhir')->values();
        } elseif ($sort == 'nama_asc') {
            $rekap = $rekap->sortBy('nama', SORT_NATURAL | SORT_FLAG_CASE)->values();
        } elseif ($sort == 'nama_desc') {
            $rekap = $rekap->sortByDesc('nama', SORT_NATURAL | SORT_FLAG_CASE)->values();
        }

        // 4. HITUNG ULANG STATISTIK UNTUK KARTU INDIKATOR ATAS
        $totalPeserta = $rekap->count();
        $totalLulus = $rekap->where('status_kelulusan', 'Lulus')->count();
        $totalTidakLulus = $rekap->where('status_kelulusan', 'Tidak Lulus')->count();
        $totalBelum = $rekap->where('status_kelulusan', 'Belum Selesai Dinilai')->count();
        $jumlahKolom = 7 + $materiDinilai->count();

        // 5. REKONSTRUKSI DATA EXCEL (Agar Kolom Tabel dan Excel Presisi Sama)
        $newExportHeader = ['No', 'Nama Peserta', 'Instansi', 'Nilai Teori (Post-Test)'];
        foreach($materiDinilai as $m) {
            $newExportHeader[] = $m->nama_materi;
        }
        $newExportHeader[] = 'Rata-rata Skill';
        $newExportHeader[] = 'Nilai Akhir (Teori+Skill)/2';
        $newExportHeader[] = 'Status Kelulusan';

        $newExportRows = [];
        foreach($rekap as $i => $row) {
            $rowData = [
                $i + 1,
                $row['nama'],
                $row['instansi'],
                $row['nilai_posttest'] ?? '-'
            ];
            foreach($materiDinilai as $m) {
                $pm = $row['per_materi'][$m->id] ?? null;
                $rowData[] = ($pm && isset($pm['persen'])) ? $pm['persen'].'%' : '-';
            }
            $rowData[] = $row['rata_rata_skill'] . '%';
            $rowData[] = $row['nilai_akhir'] > 0 ? $row['nilai_akhir'] . '%' : '-';
            $rowData[] = $row['status_kelulusan'];
            $newExportRows[] = $rowData;
        }
    @endphp

    <div class="row">
        <div class="col-12">

            <div class="page-header">
                <div>
                    <h3 class="fw-bold mb-2"><i class="bi bi-clipboard2-check-fill me-2" style="color: var(--custom-maroon);"></i> Rekap Penilaian &amp; Kelulusan</h3>
                    <p class="mb-0 fw-semibold text-muted d-flex align-items-center flex-wrap gap-2">
                        <i class="bi bi-calendar-event"></i> {{ $kegiatan->nama_kegiatan }}
                        <span class="badge bg-light text-dark border px-2 py-1 ms-1">Batas Lulus Nilai Akhir: {{ $batasLulus }}%</span>
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap mt-3 mt-md-0">
                    <button type="button" id="btnExportExcel" onclick="exportRekapExcel()" class="btn-excel" {{ $totalPeserta ? '' : 'disabled' }}>
                        <i class="bi bi-file-earmark-excel-fill"></i> Download Excel
                    </button>
                    <a href="{{ route('admin.kegiatan.peserta.index', $kegiatan->id) }}" class="btn-outline-theme">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            @if($materiDinilai->isEmpty())
                <div class="alert border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center p-3" style="background: #fffbeb; color: #d97706;">
                    <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                    <div>
                        <strong class="d-block mb-1">Perhatian!</strong>
                        <span class="small">Belum ada materi yang membutuhkan penilaian (checklist), sehingga tabel ini kosong. 
                        <a href="{{ route('admin.kegiatan.penilaian.setting', $kegiatan->id) }}" class="fw-bold" style="color: #b45309; text-decoration: underline;">Atur di Setting Penilaian</a>.</span>
                    </div>
                </div>
            @endif

            {{-- STAT CARDS --}}
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6 col-6">
                    <div class="stat-card">
                        <div class="stat-icon icon-maroon"><i class="bi bi-people-fill"></i></div>
                        <div>
                            <div class="stat-value">{{ $totalPeserta }}</div>
                            <div class="stat-label">Total Peserta</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-6">
                    <div class="stat-card">
                        <div class="stat-icon icon-success"><i class="bi bi-patch-check-fill"></i></div>
                        <div>
                            <div class="stat-value">{{ $totalLulus }}</div>
                            <div class="stat-label">Lulus</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-6">
                    <div class="stat-card">
                        <div class="stat-icon icon-danger"><i class="bi bi-x-circle-fill"></i></div>
                        <div>
                            <div class="stat-value">{{ $totalTidakLulus }}</div>
                            <div class="stat-label">Tidak Lulus</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-6">
                    <div class="stat-card">
                        <div class="stat-icon icon-warning"><i class="bi bi-hourglass-split"></i></div>
                        <div>
                            <div class="stat-value">{{ $totalBelum }}</div>
                            <div class="stat-label">Belum Selesai</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FILTER & TABLE CARD --}}
            <div class="custom-card">
                
                <form method="GET" class="row g-3 mb-4 align-items-end">
                    <div class="col-xl-3 col-md-6">
                        <label class="filter-label">Cari Peserta</label>
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="left: 14px;"></i>
                            <input type="text" name="search" class="form-control ps-5" placeholder="Ketik nama..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <label class="filter-label">Filter Instansi</label>
                        <select name="instansi_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Instansi</option>
                            @foreach($instansi as $ins)
                                <option value="{{ $ins->id }}" {{ request('instansi_id') == $ins->id ? 'selected' : '' }}>{{ $ins->nama_instansi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-6">
                        <label class="filter-label">Status Kelulusan</label>
                        <select name="status_kelulusan" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="Lulus" {{ request('status_kelulusan') == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                            <option value="Tidak Lulus" {{ request('status_kelulusan') == 'Tidak Lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                            <option value="Belum Selesai Dinilai" {{ request('status_kelulusan') == 'Belum Selesai Dinilai' ? 'selected' : '' }}>Belum Selesai</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-6">
                        <label class="filter-label">Urutkan Berdasarkan</label>
                        <select name="sort" class="form-select" onchange="this.form.submit()">
                            <option value="default">Default</option>
                            <option value="rata_desc" {{ request('sort') == 'rata_desc' ? 'selected' : '' }}>Nilai Akhir Tertinggi</option>
                            <option value="rata_asc" {{ request('sort') == 'rata_asc' ? 'selected' : '' }}>Nilai Akhir Terendah</option>
                            <option value="nama_asc" {{ request('sort') == 'nama_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
                            <option value="nama_desc" {{ request('sort') == 'nama_desc' ? 'selected' : '' }}>Nama (Z - A)</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-12 d-flex gap-2">
                        <button type="submit" class="btn-theme w-100 p-0" style="height: 44px;" title="Terapkan Filter"><i class="bi bi-search"></i> Cari</button>
                        @if(request('search') || request('instansi_id') || request('status_kelulusan') || request('sort'))
                            <a href="{{ route('admin.kegiatan.penilaian.rekap', $kegiatan->id) }}" class="btn-outline-theme w-100 p-0 border-danger text-danger" style="height: 44px; background: #fef2f2;" title="Reset Filter"><i class="bi bi-x-lg"></i> Reset</a>
                        @endif
                    </div>
                </form>

                <div class="table-custom-wrapper table-responsive mt-4">
                    <table class="table table-custom mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Nama Peserta</th>
                                <th>Instansi</th>
                                <th class="text-center">Teori<br><span class="fw-medium text-lowercase opacity-75">(Post-Test)</span></th>
                                @foreach($materiDinilai as $m)
                                    <th class="text-center th-materi">{{ $m->nama_materi }}<br><span class="fw-medium text-lowercase opacity-75">(% skill)</span></th>
                                @endforeach
                                <th class="text-center">Rata-rata<br><span class="fw-medium text-lowercase opacity-75">(Skill)</span></th>
                                <th class="text-center">Nilai Akhir<br><span class="fw-medium text-lowercase opacity-75">(Teori+Skill) / 2</span></th>
                                <th class="text-center">Status Kelulusan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rekap as $i => $row)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $row['nama'] }}</div>
                                </td>
                                <td>
                                    <div class="text-muted small fw-semibold"><i class="bi bi-building me-1"></i> {{ $row['instansi'] }}</div>
                                </td>
                                
                                {{-- Nilai Teori (Post-Test) --}}
                                <td class="text-center">
                                    @if(isset($row['nilai_posttest']) && is_numeric($row['nilai_posttest']))
                                        <span class="badge bg-light text-dark border px-3 py-2 fs-6">{{ $row['nilai_posttest'] + 0 }}</span>
                                    @else
                                        <span class="text-muted opacity-50">-</span>
                                    @endif
                                </td>

                                {{-- Nilai Per Materi Skill --}}
                                @foreach($materiDinilai as $m)
                                    @php $pm = $row['per_materi'][$m->id] ?? null; @endphp
                                    <td class="text-center">
                                        @if($pm && $pm['lengkap'])
                                            <span class="fw-bold text-dark">{{ $pm['persen'] }}%</span>
                                            <a href="{{ route('admin.kegiatan.penilaian.cetak_pdf', [$kegiatan->id, $row['id'], $m->id]) }}" target="_blank" class="btn-pdf-mini" title="Cetak Lembar Observasi: {{ $m->nama_materi }}">
                                                <i class="bi bi-file-earmark-pdf-fill"></i>
                                            </a>
                                        @elseif($pm && $pm['terisi'] > 0)
                                            <span class="text-muted fw-bold">{{ $pm['persen'] }}%</span>
                                            <i class="bi bi-hourglass-split text-warning ms-1" title="Belum selesai dinilai"></i>
                                        @else
                                            <span class="text-muted opacity-50">-</span>
                                        @endif
                                    </td>
                                @endforeach

                                {{-- Rata-Rata Skill --}}
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-3 py-2 fs-6">{{ $row['rata_rata_skill'] }}%</span>
                                </td>

                                {{-- Nilai Akhir Final --}}
                                <td class="text-center">
                                    @if($row['nilai_akhir'] > 0)
                                        <span class="badge {{ ((float)$row['nilai_akhir'] >= $batas) ? 'bg-success' : 'bg-danger' }} px-3 py-2 fs-6">{{ $row['nilai_akhir'] }}%</span>
                                    @else
                                        <span class="text-muted opacity-50">-</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if($row['status_kelulusan'] == 'Lulus')
                                        <span class="badge-status badge-lulus"><i class="bi bi-check-circle-fill"></i> Lulus</span>
                                    @elseif($row['status_kelulusan'] == 'Tidak Lulus')
                                        <span class="badge-status badge-tidak-lulus"><i class="bi bi-x-circle-fill"></i> Tidak Lulus</span>
                                    @else
                                        <span class="badge-status badge-belum"><i class="bi bi-hourglass-split"></i> Belum Selesai</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $jumlahKolom }}" class="text-center py-5">
                                    <div class="text-muted mb-3"><i class="bi bi-file-earmark-x fs-1 d-block opacity-25"></i></div>
                                    <h6 class="fw-bold text-dark">Tidak ada data rekap</h6>
                                    <p class="text-muted mb-0 small">Belum ada peserta atau tidak ada yang cocok dengan filter.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>

    <script>
        const exportHeader = @json($newExportHeader);
        const exportRows = @json($newExportRows);

        function exportRekapExcel() {
            if (!exportRows.length) return;

            const ws = XLSX.utils.aoa_to_sheet([exportHeader, ...exportRows]);
            ws['!cols'] = exportHeader.map((h, i) => ({ wch: i === 1 ? 34 : (i === 2 ? 28 : 18) }));

            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Rekap Penilaian');
            XLSX.writeFile(wb, 'Rekap_Penilaian_{{ Str::slug($kegiatan->nama_kegiatan) }}.xlsx');
        }
    </script>
@endsection