<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat Orientasi RSUD SLG</title>
    <style>
        /* 1. SETUP PAGE & RESET */
        @page { margin: 0; size: A4 landscape; }
        body { margin: 0; padding: 0; font-family: 'Times New Roman', serif; background: #fff; }

        /* 2. MAIN FRAME (SAFETY ZONE) 
           Memberi jarak 20px dari tepi kertas agar tidak terpotong printer/viewer */
        .page-frame {
            position: absolute;
            top: 20px; left: 20px; right: 20px; bottom: 20px;
            /* Border Utama Maroon */
            border: 15px solid #7c1316; 
            background: #fff;
            z-index: 10;
            overflow: hidden;
        }

        /* 3. INNER GOLD BORDER */
        .inner-frame {
            position: absolute;
            top: 8px; left: 8px; right: 8px; bottom: 8px;
            border: 2px solid #d4af37; /* Emas */
            z-index: 11;
        }

        /* 4. ORNAMEN SUDUT (OPSIONAL - DIHILANGKAN AGAR CLEAN/TIDAK POTONG) */
        /* Kita pakai style klasik bersih saja */

        /* 5. WATERMARK TENGAH */
        .watermark {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 550px; opacity: 0.04; z-index: 0;
            filter: grayscale(100%);
        }

        /* 6. CONTENT WRAPPER */
        .content {
            position: relative;
            z-index: 20;
            padding: 20px 50px; /* Padding dalam yang nyaman */
            text-align: center;
        }

        /* 7. KOP SURAT */
        .header-table { width: 100%; border-bottom: 3px double #7c1316; padding-bottom: 10px; margin-bottom: 10px; }
        .logo-rs { height: 95px; width: auto; } /* Ukuran disesuaikan */
        .logo-app { height: 120px; width: auto; }

        .header-text h3 { margin: 0; font-family: Arial, sans-serif; font-size: 15px; letter-spacing: 2px; color: #555; text-transform: uppercase; }
        .header-text h1 { margin: 2px 0; font-family: 'Times New Roman', serif; font-size: 30px; font-weight: 900; color: #7c1316; text-transform: uppercase; letter-spacing: 1px; }
        .header-text p { margin: 0; font-family: Arial, sans-serif; font-size: 11px; color: #333; }

        /* 8. JUDUL */
        .cert-title {
            font-size: 42px; font-weight: bold; color: #7c1316;
            text-transform: uppercase; letter-spacing: 2px;
            margin-top: 10px; margin-bottom: 0;
            text-shadow: 1px 1px 0px #d4af37;
        }
        .cert-no { font-family: Arial, sans-serif; font-size: 13px; font-weight: bold; color: #555; margin-top: 2px; margin-bottom: 20px; }

        /* 9. PENERIMA */
        .recipient-label { font-family: Arial, sans-serif; font-size: 13px; color: #666; font-style: italic; margin-bottom: 5px; }
        .name {
            font-size: 36px; font-weight: bold; color: #000;
            text-transform: uppercase; border-bottom: 2px solid #d4af37;
            display: inline-block; padding-bottom: 2px; margin-bottom: 5px;
        }
        .instansi { font-family: Arial, sans-serif; font-size: 16px; font-weight: bold; color: #7c1316; text-transform: uppercase; margin-bottom: 15px; }

        /* 10. TEXT BODY */
        .desc {
            font-size: 13px; line-height: 1.4; color: #333;
            max-width: 850px; margin: 0 auto 15px auto;
        }

        /* 11. TABEL NILAI */
        .score-table {
            width: 70%; margin: 0 auto 15px auto;
            border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;
        }
        .score-table th { background: #7c1316; color: #fff; padding: 8px; border: 1px solid #5a0e10; text-transform: uppercase; letter-spacing: 1px; }
        .score-table td { border: 1px solid #ccc; padding: 8px; text-align: center; background: rgba(255,255,255,0.8); }
        .predikat-lulus { color: #7c1316; font-weight: bold; }

        /* 12. FOOTER & TTD */
        .footer-table { width: 100%; margin-top: 0; }
        .footer-col { vertical-align: top; text-align: center; width: 33.33%; }
        
        .date-line { font-family: Arial, sans-serif; font-size: 12px; margin-bottom: 2px; }
        .jabatan { font-family: Arial, sans-serif; font-size: 11px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        
        .qr-box { 
            width: 75px; height: 75px; margin: 2px auto; 
            border: 1px solid #ddd; padding: 2px; background: #fff; 
        }
        .qr-img { width: 100%; height: 100%; }

        .ttd-name { font-size: 13px; font-weight: bold; text-decoration: underline; color: #7c1316; margin-top: 3px; }
        .ttd-nip { font-family: Arial, sans-serif; font-size: 11px; color: #333; }

        /* Stempel Emas Kiri Bawah */
        .seal {
            width: 90px; height: 90px; border: 3px double #d4af37; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #d4af37; font-weight: bold; font-size: 9px; text-transform: uppercase;
            letter-spacing: 1px; transform: rotate(-15deg); margin: 0 auto; opacity: 0.7;
        }
    </style>
</head>
<body>

    <div class="page-frame">
        
        <div class="inner-frame"></div>

        <img src="{{ public_path('logors.png') }}" class="watermark">

        <div class="content">
            
            <table class="header-table">
                <tr>
                    <td width="15%" align="left"><img src="{{ public_path('logors.png') }}" class="logo-rs"></td>
                    <td width="70%" align="center">
                        <div class="header-text">
                            <h3>Pemerintah Kabupaten Kediri</h3>
                            <h1>RSUD Simpang Lima Gumul</h1>
                            <p>Jalan Galuh Candrakirana, Tugurejo, Kec. Ngasem, Kab. Kediri, Jawa Timur 64182</p>
                            <p>Phone: (0354) 2891400 | Email: rsudslg@kedirikab.go.id</p>
                        </div>
                    </td>
                    <td width="15%" align="right"><img src="{{ public_path('icon.png') }}" class="logo-app"></td>
                </tr>
            </table>

            <div class="cert-title">Sertifikat Kelulusan</div>
            <div class="cert-no">NO: {{ rand(100,999) }}/DIKLAT/RSUD-SLG/{{ date('Y') }}</div>

            <div class="recipient-label">Diberikan Kepada:</div>
            <div class="name">{{ $user->name }}</div>
            
            @php
                $mhs = \App\Models\Mahasiswa::where('user_id', $user->id)->first();
                $instansi = $mhs && $mhs->mou ? ($mhs->mou->nama_instansi ?? $mhs->mou->nama_universitas) : 'PESERTA UMUM';
            @endphp
            <div class="instansi">{{ $instansi }}</div>

            <div class="desc">
                Telah berpartisipasi aktif dan menyelesaikan<br>
                <strong>PROGRAM ORIENTASI UMUM & KESELAMATAN PASIEN</strong><br>
                Materi: Profil RS, PMKP, PPI, K3RS, dan Bantuan Hidup Dasar (BHD).
            </div>

            <table class="score-table">
                <thead>
                    <tr>
                        <th width="40%">MATERI EVALUASI</th>
                        <th width="30%">NILAI AKHIR</th>
                        <th width="30%">PREDIKAT</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Pre Test (Evaluasi Awal)</td>
                        <td>{{ $pre_score }}</td>
                        <td>Selesai</td>
                    </tr>
                    <tr>
                        <td><strong>Post Test (Evaluasi Akhir)</strong></td>
                        <td style="font-weight:bold; color:#7c1316;">{{ $post_score }}</td>
                        <td class="predikat-lulus">LULUS KOMPETEN</td>
                    </tr>
                </tbody>
            </table>

            <table class="footer-table">
                <tr>
                
                    <td class="footer-col"></td>
                    <td class="footer-col">
                        <div class="date-line">Ditetapkan di: Kediri</div>
                        <div class="date-line">Pada Tanggal: <b>{{ $date }}</b></div>
                        <div class="jabatan" style="margin-top: 5px;">KOORDINATOR DIKLAT</div>

                        @php
                            $qrData = "RSUD SLG - VALID\nNama: ".$user->name."\nStatus: LULUS\nTgl: ".$date."\nNIP: 198410281009011005";
                            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);
                        @endphp
                        
                        <div class="qr-box">
                            <img src="{{ $qrUrl }}" class="qr-img">
                        </div>

                        <div class="ttd-name">Hardityo Fajar Siwi, S.Kep.Ns., M.Kep.</div>
                        <div class="ttd-nip">NIP. 19841028 100901 1 005</div>
                    </td>
                </tr>
            </table>

        </div>
    </div>

</body>