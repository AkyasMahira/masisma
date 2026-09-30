<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Observasi - {{ $peserta->nama_lengkap_gelar }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        body {
            background-color: #cbd5e1;
            font-family: "Times New Roman", Times, serif;
            color: #000;
            padding-top: 80px;
            padding-bottom: 40px;
        }

        /* Action Bar UI */
        .action-bar {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            font-family: Arial, sans-serif;
        }
        .action-bar .title { font-weight: bold; color: #1e293b; margin: 0; }
        .btn-maroon { background-color: #7c1316; color: #fff; font-weight: bold; }
        .btn-maroon:hover { background-color: #5c0d10; color: #fff; }

        /* Ukuran Kertas */
        .a4-paper {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            margin: 0 auto;
            padding: 15mm 20mm;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            box-sizing: border-box;
        }

        /* KOP SURAT RESMI */
        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 5px;
            padding-bottom: 5px;
        }
        .kop-logo {
            width: 80px;
            margin-right: 20px;
        }
        .kop-text {
            text-align: center;
            flex: 1;
        }
        .kop-text h3 {
            margin: 0;
            font-size: 14pt;
            font-weight: normal;
        }
        .kop-text h1 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .kop-text p {
            margin: 3px 0 0;
            font-size: 10pt;
        }
        .garis-kop {
            border: none;
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 2px;
            margin-bottom: 25px;
        }

        /* HEADER DOKUMEN */
        .doc-header {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            line-height: 1.5;
            margin-bottom: 25px;
            text-transform: uppercase;
        }

        /* TABEL INFO */
        .info-table { width: 100%; margin-bottom: 15px; font-size: 11pt; }
        .info-table td { padding: 3px 0; vertical-align: top; }
        .info-table td.label { width: 140px; }
        .info-table td.colon { width: 15px; text-align: center; }

        /* TABEL OBSERVASI */
        .obs-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
            margin-bottom: 15px;
        }
        .obs-table th, .obs-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
        }
        .obs-table th { text-align: center; font-weight: bold; }
        
        .col-no { width: 5%; text-align: center; }
        .col-aspek { width: 45%; }
        .col-penilaian { text-align: center; font-size: 9pt; }
        .col-catatan { width: 15%; }

        .check-mark {
            font-family: Arial, sans-serif;
            font-weight: bold;
            font-size: 14pt;
            text-align: center;
        }

        /* FOOTER & QR CODE */
        .footer-row td { font-weight: bold; padding: 10px; }
        .box-check {
            display: inline-block;
            width: 14px; height: 14px;
            border: 1px solid #000;
            margin-right: 6px;
            text-align: center;
            line-height: 14px;
            font-size: 12px;
            font-family: Arial, sans-serif;
        }
        
        .qr-box {
            text-align: center;
            margin: 10px 0;
            min-height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qr-box img {
            height: 70px !important;
            width: 70px !important;
            object-fit: contain;
        }

        @media print {
            .action-bar, .no-print { display: none !important; }
            body { background-color: #fff; padding: 0; margin: 0; }
            .a4-paper { box-shadow: none; margin: 0; padding: 0; width: 100%; min-height: auto; }
            .obs-table th, .obs-table td {
                border: 1pt solid #000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <div class="action-bar no-print">
        <h5 class="title"><i class="bi bi-file-earmark-text"></i> Mode Cetak Dokumen</h5>
        <div>
            <button onclick="window.print()" class="btn btn-dark me-2">
                <i class="bi bi-printer-fill me-1"></i> Print / PDF
            </button>
            <button onclick="downloadPDF()" class="btn btn-maroon" id="btn-download">
                <i class="bi bi-download me-1"></i> Download File
            </button>
        </div>
    </div>

    <!-- KONVERSI LOGO KE BASE64 VIA PHP (BYPASS CORS) -->
    @php
        $logoUrl = 'https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png';
        $logoBase64 = '';
        try {
            // Ambil gambar langsung dari server saat halaman di-load
            $logoData = file_get_contents($logoUrl);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        } catch (\Exception $e) {
            // Jika gagal ambil (misal server target down), fallback ke link biasa
            $logoBase64 = $logoUrl;
        }

        // GENERATE DATA UNTUK QR CODE
        $namaFasilitator = $fasilitator->nama_fasilitator ?? 'Instruktur';
        $waktuTtd = $ttd ? \Carbon\Carbon::parse($ttd->waktu_ttd)->format('Y-m-d H:i:s') : date('Y-m-d');
        // Teks yang akan masuk ke dalam QR Code
        $qrDataText = urlencode("Ditandatangani oleh: " . $namaFasilitator . " pada " . $waktuTtd);
        
        // URL API QR Code Server
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . $qrDataText;
    @endphp

    <div class="a4-paper" id="dokumen-cetak">
        
        <!-- KOP SURAT -->
        <div class="kop-surat">
            <!-- Menampilkan Logo Base64 -->
            <img src="{{ $logoBase64 }}" alt="Logo RS" class="kop-logo">
            <div class="kop-text">
                <h3>PEMERINTAH KABUPATEN KEDIRI</h3>
                <h1>RUMAH SAKIT UMUM DAERAH SIMPANG LIMA GUMUL</h1>
                <p>Jl. Galuh Candra Kirana, Tugurejo, Kec. Ngasem, Kabupaten Kediri, Jawa Timur 64128</p>
            </div>
        </div>
        <hr class="garis-kop">
        
        <!-- JUDUL SESUAI REQUEST -->
        <div class="doc-header">
            LAMPIRAN<br>
            LEMBAR OBSERVASI MELAKUKAN ASUHAN KEPERAWATAN PADA PASIEN<br>
            {{ $kegiatan->nama_kegiatan }}<br>
            DI LINGKUNGAN RSUD SIMPANG LIMA GUMUL
        </div>

        <table class="info-table">
            <tr>
                <td class="label">Nama Peserta</td>
                <td class="colon">:</td>
                <td>{{ $peserta->nama_lengkap_gelar }}</td>
            </tr>
            <tr>
                <td class="label">Asal Instansi</td>
                <td class="colon">:</td>
                <td>{{ $peserta->instansi->nama_instansi ?? 'Internal RS' }}</td>
            </tr>
        </table>

        <table class="obs-table">
            <thead>
                <tr>
                    <th rowspan="2" class="col-no">No</th>
                    <th rowspan="2" class="col-aspek">Aspek Observasi</th>
                    
                    @if($setting->jenis_penilaian == 'centang')
                        <th colspan="2">Penilaian</th>
                    @else
                        <th colspan="3">Penilaian</th>
                    @endif
                    
                    <th rowspan="2" class="col-catatan">Catatan</th>
                </tr>
                <tr>
                    @if($setting->jenis_penilaian == 'centang')
                        <th class="col-penilaian" style="width: 17%">Tidak Kompeten</th>
                        <th class="col-penilaian" style="width: 17%">Kompeten</th>
                    @else
                        <th class="col-penilaian" style="width: 12%">Tidak Dilakukan<br>(0)</th>
                        <th class="col-penilaian" style="width: 12%">Kurang Tepat<br>(1)</th>
                        <th class="col-penilaian" style="width: 12%">Tepat Sempurna<br>(2)</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($items as $i => $item)
                    @php $nilai = $nilaiTersimpan[$item->id] ?? null; @endphp
                    <tr>
                        <td class="col-no">{{ $i + 1 }}</td>
                        <td>{{ $item->aspek_tindakan }}</td>
                        
                        @if($setting->jenis_penilaian == 'centang')
                            <td class="check-mark">{{ $nilai === 0.00 ? '✓' : '' }}</td>
                            <td class="check-mark">{{ $nilai === 1.00 ? '✓' : '' }}</td>
                        @else
                            <td class="check-mark">{{ $nilai === 0.00 ? '✓' : '' }}</td>
                            <td class="check-mark">{{ $nilai === 1.00 ? '✓' : '' }}</td>
                            <td class="check-mark">{{ $nilai === 2.00 ? '✓' : '' }}</td>
                        @endif
                        
                        <td></td>
                    </tr>
                @endforeach
                
                <tr class="footer-row">
                    <td colspan="2">
                        TOTAL (&ge; {{ $setting->batas_lulus_persen ?? 80 }}% Kompeten)
                    </td>
                    <td colspan="{{ $setting->jenis_penilaian == 'centang' ? '3' : '4' }}" style="text-align: center; font-style: italic;">
                        Total = {{ $persenSkill }}%
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="padding: 10px; vertical-align: top;">
                        <div style="font-weight: bold; margin-bottom: 5px;">Hasil Penilaian :</div>
                        <div style="margin-bottom: 2px;">
                            <span class="box-check">{{ $statusLulus == 'Tidak Kompeten' ? '✓' : '' }}</span> Tidak Kompeten
                        </div>
                        <div>
                            <span class="box-check">{{ $statusLulus == 'Kompeten' ? '✓' : '' }}</span> Kompeten
                        </div>
                    </td>
                    <td colspan="{{ $setting->jenis_penilaian == 'centang' ? '2' : '2' }}" style="padding: 10px; vertical-align: top;">
                        <div style="font-weight: bold; margin-bottom: 15px;">Tanggal Ujian:</div>
                        <div style="font-weight: bold;">Tanggal Penilaian:</div>
                        <div>{{ $ttd ? \Carbon\Carbon::parse($ttd->waktu_ttd)->translatedFormat('d F Y') : '-' }}</div>
                    </td>
                    <td colspan="{{ $setting->jenis_penilaian == 'centang' ? '1' : '2' }}" style="padding: 10px; vertical-align: top; text-align: center;">
                        <div style="font-weight: bold;">Paraf Instruktur/Nama:</div>
                        
                        <!-- TAMPILKAN QR CODE DARI API -->
                        <div class="qr-box">
                            <img src="{{ $qrCodeUrl }}" alt="QR Code TTD" crossorigin="anonymous">
                        </div>
                        
                        <div style="text-decoration: underline; font-weight: bold;">
                            {{ $namaFasilitator }}
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        function downloadPDF() {
            const btn = document.getElementById('btn-download');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
            btn.disabled = true;

            const element = document.getElementById('dokumen-cetak');
            const namaFile = "Lembar_Observasi_{{ str_replace(' ', '_', $peserta->nama_lengkap_gelar) }}.pdf";

            const opt = {
                margin:       10, 
                filename:     namaFile,
                image:        { type: 'jpeg', quality: 1 },
                html2canvas:  { scale: 2, useCORS: true, allowTaint: true, windowWidth: 1024 },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).save().then(function() {
                btn.innerHTML = '<i class="bi bi-check-circle"></i> Berhasil Diunduh';
                setTimeout(() => {
                    btn.innerHTML = '<i class="bi bi-download me-1"></i> Download File';
                    btn.disabled = false;
                }, 3000);
            });
        }
    </script>
</body>
</html>