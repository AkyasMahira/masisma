<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Penilaian - {{ $peserta->nama_lengkap_gelar }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sindikat-maroon: #7c1316;
            --sindikat-maroon-dark: #5c0d10;
            --sindikat-surface: #ffffff;
            --sindikat-text: #1e293b;
            --sindikat-text-muted: #64748b;
            --sindikat-border: #e2e8f0;
            --sindikat-input-bg: #f8fafc;
        }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f1f5f9;
            color: var(--sindikat-text); 
            padding-bottom: 140px; 
        }
        
        .btn-back { color: var(--sindikat-text-muted); font-weight: 700; text-decoration: none; transition: 0.2s; display: inline-flex; align-items: center; background: #fff; padding: 10px 20px; border-radius: 50px; border: 1px solid var(--sindikat-border); box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .btn-back:hover { color: var(--sindikat-maroon); border-color: var(--sindikat-maroon); transform: translateX(-4px); }

        .form-card { background: var(--sindikat-surface); border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 0; border: 1px solid var(--sindikat-border); margin-bottom: 24px; overflow: hidden; }
        .info-box { background: linear-gradient(to right, #fff, var(--sindikat-input-bg)); border: 1px solid var(--sindikat-border); border-radius: 20px; padding: 24px; border-left: 6px solid var(--sindikat-maroon); }
        
        /* Table Styling */
        .table-custom { margin-bottom: 0; }
        .table-custom th { background-color: #f8fafc; color: var(--sindikat-text-muted); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px 20px; border-bottom: 1px solid var(--sindikat-border); }
        .table-custom td { padding: 20px; vertical-align: middle; border-bottom: 1px solid var(--sindikat-border); font-size: 0.95rem; font-weight: 600; color: var(--sindikat-text); }
        .tr-category td { background-color: #fef1f2; color: var(--sindikat-maroon); font-weight: 800; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; border-bottom: none; padding: 12px 20px; }
        
        /* Inputs & Buttons */
        .form-control, .form-select { border-radius: 12px; padding: 14px 16px; background: var(--sindikat-input-bg); border: 1px solid var(--sindikat-border); font-size: 0.95rem; font-weight: 600; transition: all 0.3s; color: var(--sindikat-text); }
        .form-control:focus, .form-select:focus { background: #ffffff; border-color: var(--sindikat-maroon); box-shadow: 0 0 0 4px rgba(124, 19, 22, 0.1); }
        
        /* Custom Radio Buttons */
        .radio-wrapper { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; width: 100%; }
        .btn-check-custom + label { width: 100%; padding: 10px; border-radius: 10px; border: 2px solid var(--sindikat-border); font-weight: 700; text-align: center; cursor: pointer; transition: all 0.2s; background: #fff; color: var(--sindikat-text-muted); }
        
        .btn-check-custom[value="0"]:checked + label { background-color: #fef2f2; color: #dc2626; border-color: #dc2626; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15); }
        .btn-check-custom[value="1"]:checked + label { background-color: #ecfdf5; color: #059669; border-color: #059669; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.15); }

        /* Sticky Bottom Bar */
        .sticky-bottom-bar { position: fixed; bottom: 0; left: 0; right: 0; z-index: 1000; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-top: 1px solid var(--sindikat-border); padding: 16px 0; box-shadow: 0 -10px 40px rgba(0,0,0,0.06); }
        .btn-submit { background-color: var(--sindikat-maroon); color: white; border-radius: 50px; padding: 14px 40px; font-weight: 800; font-size: 1.05rem; border: none; transition: 0.3s; width: 100%; display: flex; justify-content: center; align-items: center; gap: 10px; }
        .btn-submit:hover { background-color: var(--sindikat-maroon-dark); color: white; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(124, 19, 22, 0.25); }
        
        /* Styling tambahan untuk Total Skor */
        .score-display { background: #fef1f2; padding: 12px 20px; border-radius: 15px; border: 1px solid #fecdd3; }

        @media (min-width: 768px) {
            .btn-submit { width: auto; }
            .radio-wrapper { display: flex; }
            .btn-check-custom + label { padding: 10px 24px; }
        }
        @media (max-width: 767px) {
            .table-custom th.text-center, .table-custom td.text-center { display: none; } 
            .aspek-col { display: block; padding-bottom: 8px !important; border-bottom: none !important; }
            .nilai-col { display: block; padding-top: 0 !important; }
            .btn-submit { padding: 12px 20px; font-size: 0.95rem; width: auto; }
            .score-display { padding: 10px 15px; }
            .score-display .fs-2 { font-size: 1.5rem !important; }
        }
    </style>
</head>
<body>
<div class="container pt-4">
    
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <a href="{{ route('fasilitator.penilaian.materi', [$fasilitator->token_fasilitator, $materi->id]) }}" class="btn-back mb-4">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar Peserta
            </a>

            <!-- Info Peserta -->
            <div class="info-box mb-4 shadow-sm">
                <div class="small fw-bold text-muted text-uppercase letter-spacing-1 mb-1">Menilai Peserta</div>
                <h3 class="fw-bold mb-2" style="color: var(--sindikat-maroon);">{{ $peserta->nama_lengkap_gelar }}</h3>
                <p class="text-muted mb-0 fw-medium d-flex align-items-center">
                    <i class="bi bi-journal-check me-2"></i> Materi: {{ $materi->nama_materi }}
                </p>
            </div>

            <form action="{{ route('fasilitator.penilaian.submit', [$fasilitator->token_fasilitator, $materi->id, $peserta->id]) }}" method="POST">
                @csrf
                
                <!-- Card Tabel Observasi -->
                <div class="form-card">
                    <div class="p-4 border-bottom bg-white d-flex flex-wrap gap-2 justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0"><i class="bi bi-card-checklist me-2 text-muted"></i> Form Observasi</h5>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">Mode: {{ ucfirst($setting->jenis_penilaian) }}</span>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-custom table-hover">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center d-none d-md-table-cell">No</th>
                                    <th>Aspek Penilaian</th>
                                    <th width="35%" class="text-center d-none d-md-table-cell">Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $currentKat = null; @endphp
                                @foreach($items as $item)
                                    @if($item->kategori && $item->kategori != $currentKat)
                                        <tr class="tr-category">
                                            <td colspan="3"><i class="bi bi-tag-fill me-2 opacity-50"></i> {{ $item->kategori }}</td>
                                        </tr>
                                        @php $currentKat = $item->kategori; @endphp
                                    @endif
                                    
                                    @php $nilaiSaatIni = $nilaiTersimpan[$item->id] ?? null; @endphp

                                    <tr>
                                        {{-- MENGGUNAKAN $item->urutan AGAR SESUAI DENGAN SETTING ADMIN --}}
                                        <td class="text-center fw-bold text-muted d-none d-md-table-cell">{{ $item->urutan }}</td>
                                        <td class="aspek-col">{{ $item->aspek_tindakan }}</td>
                                        <td class="nilai-col">
                                            @if($setting->jenis_penilaian == 'centang')
                                                <div class="radio-wrapper">
                                                    <div>
                                                        <input type="radio" class="btn-check btn-check-custom input-nilai" name="nilai[{{ $item->id }}]" id="n0_{{ $item->id }}" value="0" {{ $nilaiSaatIni === 0.00 ? 'checked' : '' }} required>
                                                        <label for="n0_{{ $item->id }}">Tidak</label>
                                                    </div>
                                                    <div>
                                                        <!-- Nilai otomatis full 1 jika data null -->
                                                        <input type="radio" class="btn-check btn-check-custom input-nilai" name="nilai[{{ $item->id }}]" id="n1_{{ $item->id }}" value="1" {{ ($nilaiSaatIni === 1.00 || $nilaiSaatIni === null) ? 'checked' : '' }} required>
                                                        <label for="n1_{{ $item->id }}">Ya</label>
                                                    </div>
                                                </div>
                                            @else
                                                <select name="nilai[{{ $item->id }}]" class="form-select shadow-sm input-nilai" required>
                                                    <option value="" disabled>-- Pilih Skor --</option>
                                                    <option value="0" {{ $nilaiSaatIni === 0.00 ? 'selected' : '' }}>0 - Tidak Dilakukan</option>
                                                    <option value="1" {{ $nilaiSaatIni === 1.00 ? 'selected' : '' }}>1 - Kurang Tepat</option>
                                                    <!-- Nilai otomatis full 2 jika data null -->
                                                    <option value="2" {{ ($nilaiSaatIni === 2.00 || $nilaiSaatIni === null) ? 'selected' : '' }}>2 - Tepat & Sempurna</option>
                                                </select>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tanda Tangan -->
                <div class="form-card p-4 mb-4">
                    <label class="fw-bold mb-2 d-flex align-items-center fs-6"><i class="bi bi-pen-fill me-2" style="color: var(--sindikat-maroon);"></i> Nama Terang Penilai</label>
                    <p class="text-muted small mb-3">Nama ini akan tercetak sebagai penanggung jawab penilaian.</p>
                    <input type="text" name="tanda_tangan" class="form-control form-control-lg" value="{{ $fasilitator->nama_fasilitator }}" required placeholder="Ketik Nama Terang Fasilitator">
                </div>

                <!-- Spacer supaya form tidak tertutup bottom bar -->
                <div style="height: 40px;"></div>

                <!-- Sticky Bottom Bar dengan Total Skor -->
                <div class="sticky-bottom-bar">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-9 d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <!-- Realtime Score Display -->
                                <div class="score-display flex-grow-1">
                                    <span class="text-muted fw-bold d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">NILAI AKHIR (SKALA 100)</span>
                                    <div class="d-flex align-items-baseline flex-wrap gap-2">
                                        <!-- Nilai Skala 100 -->
                                        <span id="converted-score" class="fw-bolder fs-2" style="color: var(--sindikat-maroon); line-height: 1;">0</span>
                                        
                                        <!-- Skor Mentah X/Y -->
                                        <span class="text-muted fs-6 fw-normal ms-sm-2">
                                            (Skor Mentah: <span id="total-score" class="fw-bold">0</span> / <span id="max-score">0</span>)
                                        </span>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn-submit">
                                    <i class="bi bi-check2-circle fs-5 d-none d-md-block"></i> Simpan Penilaian
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Script untuk Kalkulasi Realtime -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.input-nilai');
    const totalScoreEl = document.getElementById('total-score');
    const maxScoreEl = document.getElementById('max-score');
    const convertedScoreEl = document.getElementById('converted-score');

    // Menentukan nilai maksimum per item berdasarkan jenis penilaian
    // Mode centang max = 1, Mode dropdown max = 2
    const jenisPenilaian = "{{ $setting->jenis_penilaian }}";
    const maxPerItem = jenisPenilaian === 'centang' ? 1 : 2;
    
    // Menghitung total skor maksimal keseluruhan
    const jumlahItem = {{ $items->count() }};
    const maxScore = jumlahItem * maxPerItem;
    maxScoreEl.innerText = maxScore;

    function hitungTotal() {
        let total = 0;
        
        if(jenisPenilaian === 'centang') {
            const radios = document.querySelectorAll('.input-nilai:checked');
            radios.forEach(radio => {
                total += parseFloat(radio.value) || 0;
            });
        } else {
            const selects = document.querySelectorAll('.input-nilai');
            selects.forEach(select => {
                total += parseFloat(select.value) || 0;
            });
        }
        
        // 1. Tampilkan Total Skor Mentah (misal: 26 dari 26)
        totalScoreEl.innerText = total;

        // 2. Hitung Konversi ke skala 1-100
        let konversi = 0;
        if (maxScore > 0) {
            konversi = (total / maxScore) * 100;
        }

        // Tampilkan hasil konversi dengan pembulatan (misal: 100)
        convertedScoreEl.innerText = Math.round(konversi);
    }

    // Pasang Event Listener ke semua input (radio/select)
    inputs.forEach(input => {
        input.addEventListener('change', hitungTotal);
    });

    // Jalankan hitungan pertama kali saat halaman dimuat
    hitungTotal();
});
</script>
</body>
</html>