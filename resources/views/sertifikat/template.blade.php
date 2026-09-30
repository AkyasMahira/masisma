<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sertifikat Magang - {{ $mahasiswa->nm_mahasiswa }}</title>
    <style>
        /* PENGATURAN KERTAS A4 LANDSCAPE */
        @page { margin: 0; size: A4 landscape; }
        body { 
            font-family: 'Times New Roman', Times, serif; 
            margin: 0; 
            padding: 0; 
            background-color: #ffffff; 
            color: #222; 
        }
/* KOMPETENSI */
        .kompetensi-teks { font-size: 13px; text-align: center; margin: 0 auto 10px; max-width: 850px; line-height: 1.4;}
        .kompetensi-teks b { color: #7c1316; }
        /* BINGKAI SERTIFIKAT (ELEGAN & SIMPEL) */
        .border-outer { position: absolute; top: 25px; bottom: 25px; left: 25px; right: 25px; border: 3px solid #7c1316; z-index: 1; }
        .border-inner { position: absolute; top: 32px; bottom: 32px; left: 32px; right: 32px; border: 1px solid #d4af37; z-index: 1; }
        .border-inner-2 { position: absolute; top: 35px; bottom: 35px; left: 35px; right: 35px; border: 1px solid #d4af37; z-index: 1; }

        /* WATERMARK LOGO DI TENGAH */
        .watermark { position: absolute; top: 20%; left: 35%; width: 30%; opacity: 0.05; z-index: 0; }

/* WADAH KONTEN (Padding atas tetap aman 45px) */
        .content-wrapper { position: relative; z-index: 10; padding: 45px 60px 15px; text-align: center; }

        /* KOP SURAT */
        .kop-surat { width: 100%; margin-bottom: 8px; border-bottom: 3px solid #222; padding-bottom: 8px; }
        .kop-surat td { vertical-align: middle; }
        .logo-kiri { width: 70px; height: auto; }
        .logo-kanan { width: 100px; height: auto; } 
        
        .kop-text { text-align: center; }
        .kop-gov { font-size: 13px; font-weight: bold; letter-spacing: 1px; color: #000; }
        .kop-rs { font-size: 18px; font-weight: bold; color: #7c1316; letter-spacing: 1px; margin: 2px 0; }
        .kop-alamat { font-size: 11px; font-family: 'Arial', sans-serif; color: #444; }

        /* JUDUL SERTIFIKAT */
        .cert-title { font-size: 20px; font-weight: bold; color: #7c1316; margin-top: 10px; letter-spacing: 2px; text-transform: uppercase; }
        .cert-nomor { font-size: 14px; font-weight: bold; font-family: 'Arial', sans-serif; margin-bottom: 12px; }

        /* NAMA MAHASISWA */
        .text-pengantar { font-size: 15px; font-style: italic; color: #555; margin-bottom: 2px; }
        .nama-mahasiswa { font-size: 28px; font-weight: bold; color: #000; text-transform: uppercase; letter-spacing: 1px; margin: 0; }
        .garis-nama { border-bottom: 1px dotted #7c1316; width: 60%; margin: 5px auto; }
        .detail-institusi { font-size: 15px; font-weight: bold; color: #7c1316; text-transform: uppercase; margin-bottom: 8px; }

        /* DESKRIPSI */
        .teks-deskripsi { font-size: 14px; line-height: 1.4; text-align: center; margin: 0 auto 8px; max-width: 850px; }

        /* KOMPETENSI */
        .kompetensi-teks { font-size: 14px; text-align: center; margin: 0 auto 8px; max-width: 850px; line-height: 1.4;}
        .kompetensi-teks b { color: #7c1316; }

        /* TABEL NILAI */
        .table-nilai { width: 75%; margin: 0 auto 10px; border-collapse: collapse; font-family: 'Arial', sans-serif; font-size: 12px; }
        .table-nilai th, .table-nilai td { border: 1px solid #999; padding: 5px 8px; }
        .table-nilai th { background-color: #f4f4f4; color: #7c1316; text-transform: uppercase; font-size: 11px; }
        .table-nilai td.t-left { text-align: left; }
        .table-nilai td.t-center { text-align: center; font-weight: bold; }
        .row-total td { background-color: #fdf2f2; color: #7c1316; font-size: 13px; font-weight: bold; }

        /* PREDIKAT */
        .predikat-teks { font-size: 14px; margin-top: 5px; }
        .predikat-nilai { font-size: 20px; font-weight: bold; color: #7c1316; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; display: block;}

        /* TANDA TANGAN */
        .footer-ttd { width: 100%; margin-top: 5px; font-family: 'Arial', sans-serif; }
        .ttd-box { text-align: center; font-size: 13px; line-height: 1.3; }
        .qr-box { margin: 2px 0; height: 65px; }
        .qr-img { height: 100%; width: auto; }
        .nama-direktur { font-weight: bold; text-decoration: underline; font-size: 14px; margin-bottom: 2px; }
    </style>
</head>

<body>
@php
    // Variabel kalkulasi nilai dihapus karena sudah di-passing dari Controller.
    // Menyisakan generate nomor surat & path icon.
    $tahun = date('Y');
    $id_pad = str_pad($mahasiswa->id ?? rand(100,999), 3, '0', STR_PAD_LEFT);
    $nomorSurat = "445 / " . $id_pad . " / 418.48 / " . $tahun;
    $iconUrl = asset('icon.png');
@endphp

    <div class="border-outer"></div>
    <div class="border-inner"></div>
    <div class="border-inner-2"></div>

    <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" class="watermark">

    <div class="content-wrapper">
        
        <table class="kop-surat" cellspacing="0" cellpadding="0">
            <tr>
                <td width="15%" align="left">
                    <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" class="logo-kiri">
                </td>
                <td width="70%" class="kop-text">
                    <div class="kop-gov">PEMERINTAH KABUPATEN KEDIRI</div>
                    <div class="kop-rs">RUMAH SAKIT UMUM DAERAH SIMPANG LIMA GUMUL</div>
                    <div class="kop-alamat">
                        Jl. Galuh Candra Kirana, Tugurejo, Kec. Ngasem, Kabupaten Kediri, Jawa Timur 64182<br>
                        Telp. (0354) 2891400 | Email: rsud.slg@kedirikab.go.id
                    </div>
                </td>
                <td width="15%" align="right">
                    <img src="{{ $iconUrl }}" class="logo-kanan" alt="Icon Tambahan">
                </td>
            </tr>
        </table>

        <div class="cert-title">SERTIFIKAT MAGANG / PKL</div>
        <div class="cert-nomor">Nomor : {{ $nomorSurat }}</div>

        <div class="text-pengantar">Diberikan dengan bangga kepada:</div>
        <h1 class="nama-mahasiswa">{{ $mahasiswa->nm_mahasiswa }}</h1>
        <div class="garis-nama"></div>
        <div class="detail-institusi">
            Mahasiswa {{ $mahasiswa->univ_asal ?? 'Institusi' }} 
            @if ($mahasiswa->prodi)
                - Program Studi {{ $mahasiswa->prodi }}
            @endif
        </div>

       <div class="teks-deskripsi">
            Telah berpartisipasi dan menyelesaikan program magang/praktik kerja lapangan di <b>RSUD Simpang Lima Gumul Kediri</b> dengan dedikasi dan disiplin yang baik, terhitung sejak tanggal <b>{{ optional($mahasiswa->tanggal_mulai)->isoFormat('D MMMM YYYY') ?? '-' }}</b> sampai dengan <b>{{ optional($mahasiswa->tanggal_berakhir)->isoFormat('D MMMM YYYY') ?? '-' }}</b>.
        </div>

       {{-- AREA KOMPETENSI DIMULAI DI SINI --}}
        @if(is_array($mahasiswa->kompetensi_json) && count($mahasiswa->kompetensi_json) > 0)
            <div class="kompetensi-teks">
                <b>Kompetensi : </b> {{ implode(', ', $mahasiswa->kompetensi_json) }}.
            </div>
        @endif
        {{-- AREA KOMPETENSI SELESAI --}}

        <table class="table-nilai">
            <thead>
                <tr>
                    <th width="45%">Komponen Evaluasi</th>
                    <th width="15%">Nilai Asli</th>
                    <th width="15%">Bobot</th>
                    <th width="25%">Nilai Akhir (Tertimbang)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="t-left">Kehadiran & Partisipasi Aktif</td>
                    <td class="t-center">{{ round($nilaiAbsensi, 1) }}</td>
                    <td class="t-center">60%</td>
                    <td class="t-center">{{ round($bobotAbsensi, 1) }}</td>
                </tr>
                <tr>
                    <td class="t-left">Penilaian Praktik Klinis</td>
                    <td class="t-center">{{ $nilaiKaruAngka }}</td> 
                    <td class="t-center">40%</td>
                    <td class="t-center">{{ round($bobotKaru, 1) }}</td>
                </tr>
                <tr class="row-total">
                    <td colspan="3" align="right" style="padding-right: 15px;">TOTAL NILAI KESELURUHAN</td>
                    <td class="t-center">{{ $totalNilai }}</td>
                </tr>
            </tbody>
        </table>

        <div class="predikat-teks">
            Berdasarkan hasil evaluasi, yang bersangkutan dinyatakan <b>LULUS</b> dengan predikat: <br>
            <span class="predikat-nilai">{{ $predikatAkhir }}</span>
        </div>

        {{-- AREA KOMPETENSI SELESAI --}}
        <table class="footer-ttd" cellspacing="0" cellpadding="0">
            <tr>
                <td width="65%"></td>
                <td width="35%" class="ttd-box">
                    <div>Kediri, {{ $tanggal_terbit }}</div>
                    <div>Direktur RSUD Simpang Lima Gumul,</div>
                    
                    <div class="qr-box">
                        @if(!empty($qr_base64))
                            <img src="{{ $qr_base64 }}" class="qr-img">
                        @else
                            <div style="width:75px; height:75px; border:1px dashed #ccc; display:inline-flex; align-items:center; justify-content:center;">
                                <small style="font-size:9px;">QR Error</small>
                            </div>
                        @endif
                    </div>
                    
                    <div class="nama-direktur">dr. Tony Widyanto, Sp.OG (K)</div>
                    <div>Pembina Tk. I (IV/B)</div>
                    <div>NIP. 19750714 200212 1 006</div>
                </td>
            </tr>
        </table>

    </div>
</body>
</html>