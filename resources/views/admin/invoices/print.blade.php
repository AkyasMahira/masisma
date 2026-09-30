<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->no_invoice }}</title>
    <style>
        @page { size: A4; margin: 0; }
        body { 
            font-family: 'Helvetica', Arial, sans-serif; 
            font-size: 11px; line-height: 1.4; color: #333; margin: 0; padding: 0; 
        }
        .container { padding: 30px 45px; position: relative; }

        /* STEMPEL STATUS TENGAH (Kecil tapi mencolok) */
        .status-stamp {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            padding: 10px 20px;
            border: 4px solid #dc3545;
            color: #dc3545;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            opacity: 0.3; /* Transparan agar tidak menutupi teks utama */
            z-index: 100;
            border-radius: 10px;
            pointer-events: none;
        }

        /* Kop Surat */
        .header { border-bottom: 3px solid #7c1316; padding-bottom: 10px; margin-bottom: 20px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .logo { width: 80px; }
        .kop-text { padding-left: 15px; }
        .kop-text h1 { margin: 0; color: #7c1316; font-size: 18px; text-transform: uppercase; }
        .kop-text p { margin: 2px 0; font-size: 10px; color: #555; }

        .doc-title { text-align: center; margin-bottom: 20px; }
        .doc-title h2 { margin: 0; font-size: 20px; text-decoration: underline; color: #000; }
        .doc-title p { margin: 5px 0; font-size: 11px; font-weight: bold; }

        .info-section { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-section td { padding: 3px 0; vertical-align: top; }
        .label { color: #666; font-weight: bold; text-transform: uppercase; font-size: 9px; width: 120px; }

        .table-main { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table-main th { 
            background-color: #7c1316; color: #ffffff; padding: 8px; 
            text-align: left; font-size: 10px; text-transform: uppercase; border: 1px solid #7c1316;
        }
        .table-main td { border: 1px solid #dee2e6; padding: 10px 8px; }
        .total-row { background-color: #f8f9fa; font-weight: bold; }

        .terbilang-box { 
            margin-top: 10px; padding: 10px; border: 1px dashed #bbb; 
            background: #fdfdfd; font-style: italic; text-align: center; font-size: 11px;
        }

        .footer-table { width: 100%; margin-top: 30px; border-collapse: collapse; }
        .footer-table td { vertical-align: top; }

        .qr-section { text-align: left; margin-top: 10px; }
        .qr-box img { width: 90px; height: 90px; border: 1px solid #eee; padding: 4px; }
        .qr-text { font-size: 8px; color: #888; margin-top: 3px; }

        .sign-section { text-align: center; width: 220px; position: relative; }
        .signature-space { height: 90px; position: relative; width: 100%; }
        .img-signature { 
            position: absolute; top: 0; left: 50%; transform: translateX(-50%); 
            width: 140px; z-index: 10; 
        }
        .img-stempel {
            position: absolute; top: -10px; left: 20px; width: 100px; 
            opacity: 0.7; z-index: 5;
        }
        .sign-name { font-weight: bold; text-decoration: underline; font-size: 11px; margin: 0; }
        .sign-nip { font-size: 10px; margin: 0; }

        .bank-info { font-size: 10px; margin-top: 10px; line-height: 1.5; }
    </style>
</head>
<body>

<div class="container">
    {{-- LOGIKA: HANYA MUNCUL JIKA BELUM SELESAI/LUNAS --}}
    @if($invoice->status != 'Selesai')
        <div class="status-stamp">
            BELUM LUNAS
        </div>
    @endif

    <div class="header">
        <table class="header-table">
            <tr>
                <td width="12%">
                    <img src="{{ $logoBase64 }}" class="logo">
                </td>
                <td class="kop-text">
                    <h1>RSUD Simpang Lima Gumul</h1>
                    <p>Jalan Galuh Chandra Kirana No. 8, Kec. Ngasem, Kab. Kediri, Jawa Timur</p>
                    <p>Telp: (0354) 2891400 | Email: rsudslg@kedirikab.go.id | rsudslg.kedirikab.go.id</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title">
        <h2>INVOICE</h2>
        <p>Nomor: {{ $invoice->no_invoice }}</p>
    </div>

    <table class="info-section">
        <tr>
            <td><span class="label">Tagihan Kepada</span></td>
            <td>: <strong>{{ $invoice->instansi }}</strong></td>
            <td><span class="label">Tanggal Invoice</span></td>
            <td>: {{ date('d F Y', strtotime($invoice->tgl_invoice)) }}</td>
        </tr>
        <tr>
            <td><span class="label">Penerima Kuasa / CP</span></td>
            <td>: {{ $invoice->penanggung_jawab }}</td>
            <td><span class="label">Jatuh Tempo</span></td>
            <td>: <strong style="color: #7c1316;">{{ date('d F Y', strtotime($invoice->tgl_jatuh_tempo)) }}</strong></td>
        </tr>
        <tr>
            <td><span class="label">Prodi / Jenjang</span></td>
            <td colspan="3">: {{ $invoice->prodi }} ({{ $invoice->jenjang }})</td>
        </tr>
    </table>

    <table class="table-main">
        <thead>
            <tr>
                <th>Deskripsi Layanan / Kegiatan</th>
                <th width="40" style="text-align: center;">Mhs</th>
                <th width="40" style="text-align: center;">Mng</th>
                <th width="100" style="text-align: right;">Tarif Satuan</th>
                <th width="110" style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->deskripsi }}</td>
                <td align="center">{{ $item->jml_mhs }}</td>
                <td align="center">{{ $item->jml_minggu }}</td>
                <td align="right">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                <td align="right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            
            @if($invoice->biaya_konsumsi > 0)
            <tr>
                <td colspan="4" align="right">Biaya Konsumsi / Administrasi Tambahan</td>
                <td align="right">{{ number_format($invoice->biaya_konsumsi, 0, ',', '.') }}</td>
            </tr>
            @endif

            <tr class="total-row">
                <td colspan="4" align="right" style="padding: 12px;"><strong>TOTAL YANG HARUS DIBAYARKAN</strong></td>
                <td align="right" style="padding: 12px; color: #7c1316; font-size: 13px;">
                    <strong>Rp {{ number_format($invoice->jumlah_dibayarkan, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="terbilang-box">
        <strong>Terbilang:</strong><br>
        " {{ $terbilang }} "
    </div>

    <table class="footer-table">
        <tr>
            <td width="60%">
                <div class="bank-info">
                    <strong>Informasi Pembayaran:</strong><br>
                    Bank: <strong>BANK BNI</strong><br>
                    Nomor Rekening: <strong>881669217</strong><br>
                    Atas Nama: <strong>RSUD Simpang Lima Gumul Kediri</strong>
                </div>

                <div class="qr-section">
                    <div class="qr-box">
                        @php $qrLink = route('public.invoice.pay', $invoice->payment_token); @endphp
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($qrLink) }}">
                    </div>
                    <div class="qr-text">ID: {{ $invoice->payment_token }}</div>
                </div>
            </td>
            <td width="40%">
                <div class="sign-section" style="float: right;">
                    <p>Kediri, {{ date('d F Y') }}</p>
                    <p>Bendahara Penerimaan Pembantu,</p>
                    
                    <div class="signature-space">
                        @if($stempelBase64)
                            <!--<img src="{{ $stempelBase64 }}" class="img-stempel">-->
                        @endif

                        @if($invoice->ttd_method == 'digital' && $signatureBase64)
                            <img src="{{ $signatureBase64 }}" class="img-signature">
                        @else
                             <br><br><br>
                        @endif
                    </div>

                    <p class="sign-name">Titi Mukti Handayani, S.K.M</p>
                    <p class="sign-nip">NIP. 19850425 201504 2 002</p>
                </div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>