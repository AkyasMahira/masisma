<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat Penelitian</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
        }
        .certificate {
            width: 297mm;
            height: 210mm;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .certificate-content {
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            color: #333;
            max-width: 700px;
        }
        .certificate-border {
            border: 5px solid #667eea;
            padding: 40px;
        }
        .title {
            font-size: 48pt;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .subtitle {
            font-size: 16pt;
            color: #666;
            margin-bottom: 30px;
        }
        .recipient {
            font-size: 14pt;
            margin: 30px 0;
        }
        .name {
            font-size: 36pt;
            font-weight: bold;
            color: #667eea;
            margin: 20px 0;
            text-decoration: underline;
        }
        .description {
            font-size: 12pt;
            line-height: 1.8;
            margin: 30px 0;
        }
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        .signature {
            width: 200px;
        }
        .signature-line {
            border-top: 2px solid #333;
            margin-top: 60px;
            padding-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="certificate-content">
            <div class="certificate-border">
                <div class="title">SERTIFIKAT</div>
                <div class="subtitle">Pra-Penelitian</div>

                <div class="recipient">Diberikan Kepada:</div>
                <div class="name">{{ $nama_penerima ?? $presentasi->user->name }}</div>

                <div class="description">
                    Telah menyelesaikan kegiatan pra-penelitian dengan judul:<br>
                    <strong style="font-style: italic;">"{{ $presentasi->praPenelitian->judul }}"</strong><br><br>
                    di Rumah Sakit [Nama Rumah Sakit]<br>
                    pada tanggal {{ \Carbon\Carbon::parse($presentasi->praPenelitian->tanggal_mulai)->format('d F Y') }} 
                    s.d. {{ $presentasi->tanggal_presentasi->format('d F Y') }}<br><br>
                    dengan nilai: <strong style="font-size: 16pt; color: #667eea;">{{ $presentasi->nilai }}</strong>
                </div>

                <div class="footer">
                    <div class="signature">
                        <div>Pembimbing (CI)</div>
                        <div class="signature-line">
                            {{ $presentasi->pengajuan->ci_nama }}
                        </div>
                    </div>
                    <div class="signature">
                        <div>Kepala Bagian</div>
                        <div class="signature-line">
                            [Nama Kepala Bagian]
                        </div>
                    </div>
                </div>

                <div style="margin-top: 30px; font-size: 10pt; color: #999;">
                    Diterbitkan pada: {{ now()->format('d F Y') }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>