<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Dokumen Resmi - RSUD SLG</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #7c1316;
            --primary-light: #a3191d;
            --primary-bg: #fdf2f2;
            --success-color: #198754;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary-color) 0%, #2c0405 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        /* Card Styling */
        .validation-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
            position: relative;
            border: none;
            animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        /* Header Decoration */
        .card-header-deco {
            height: 8px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            width: 100%;
        }

        /* Animated Icon Wrapper */
        .icon-wrapper {
            width: 100px;
            height: 100px;
            background: #d1e7dd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            position: relative;
            animation: pulse 2s infinite;
        }

        .icon-wrapper i {
            font-size: 3.5rem;
            color: var(--success-color);
        }

        /* Text Styling */
        .status-title {
            color: var(--primary-color);
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .status-subtitle {
            font-size: 0.85rem;
            color: #6c757d;
        }

        /* Info Box Styling */
        .info-box {
            background-color: #f8f9fa;
            border-radius: 16px;
            padding: 20px;
            margin: 25px 0;
            border: 1px dashed #dee2e6;
            text-align: left;
        }

        .info-item {
            display: flex;
            margin-bottom: 12px;
            align-items: flex-start;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-icon {
            color: var(--primary-color);
            margin-right: 12px;
            font-size: 1.1rem;
            margin-top: 2px;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #888;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .info-value {
            display: block;
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        /* Button Styling */
        .btn-home {
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(124, 19, 22, 0.3);
            transition: all 0.3s ease;
            width: 100%;
            display: inline-block;
            text-decoration: none;
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(124, 19, 22, 0.4);
            color: white;
        }

        /* Animations */
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.4); }
            70% { box-shadow: 0 0 0 20px rgba(25, 135, 84, 0); }
            100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Logo styling */
        .rs-logo {
            height: 50px;
            width: auto;
            margin-bottom: 20px;
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <div class="validation-card p-4 p-md-5">
        <div class="card-header-deco position-absolute top-0 start-0"></div>
        
        <div class="text-center">
            <img src="https://rsudslg.kedirikab.go.id/asset_compro/img/logo/Logo.png" alt="Logo RSUD" class="rs-logo">

            <div class="icon-wrapper">
                <i class="bi bi-patch-check-fill"></i>
            </div>

            <h2 class="status-title">DOKUMEN VALID</h2>
            <p class="status-subtitle px-3">
                Sertifikat ini diterbitkan secara sah oleh sistem elektronik <br>
                <strong>RSUD Simpang Lima Gumul Kediri</strong>.
            </p>
        </div>

        <div class="info-box">
            
            @if($tipe === 'mahasiswa')
                <!-- DATA UNTUK MAHASISWA MAGANG -->
                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-person-badge-fill"></i></div>
                    <div class="info-content">
                        <span class="info-label">Nama Mahasiswa</span>
                        <span class="info-value">{{ $data->nm_mahasiswa ?? '-' }}</span>
                    </div>
                </div>

                <hr style="margin: 10px 0; border-color: #e9ecef;">

                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-building-fill"></i></div>
                    <div class="info-content">
                        <span class="info-label">Instansi / Universitas</span>
                        <span class="info-value">
                            {{ $data->mou->nama_instansi ?? $data->mou->nama_universitas ?? $data->univ_asal ?? '-' }}
                        </span>
                    </div>
                </div>

                <hr style="margin: 10px 0; border-color: #e9ecef;">

                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-calendar-range-fill"></i></div>
                    <div class="info-content">
                        <span class="info-label">Periode Magang</span>
                        <span class="info-value">
                            {{ \Carbon\Carbon::parse($data->tanggal_mulai)->isoFormat('D MMMM Y') }} 
                            <span class="text-muted mx-1" style="font-weight: 400; font-size: 0.8rem;">s/d</span> 
                            {{ \Carbon\Carbon::parse($data->tanggal_berakhir)->isoFormat('D MMMM Y') }}
                        </span>
                    </div>
                </div>
                
                <hr style="margin: 10px 0; border-color: #e9ecef;">

                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-mortarboard-fill"></i></div>
                    <div class="info-content">
                        <span class="info-label">Program Studi</span>
                        <span class="info-value">{{ $data->prodi ?? '-' }}</span>
                    </div>
                </div>

            @elseif($tipe === 'pegawai')
                <!-- DATA UNTUK PEGAWAI / PESERTA KEGIATAN -->
                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-person-badge-fill"></i></div>
                    <div class="info-content">
                        <span class="info-label">Nama Peserta / Pegawai</span>
                        <span class="info-value">{{ $data->nama_peserta ?? $data->nama_lengkap_gelar ?? '-' }}</span>
                    </div>
                </div>

                <hr style="margin: 10px 0; border-color: #e9ecef;">

                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-building-fill"></i></div>
                    <div class="info-content">
                        <span class="info-label">Instansi</span>
                        <span class="info-value">
                            {{ $data->instansi->nama_instansi ?? $data->asal_instansi ?? 'RSUD Simpang Lima Gumul' }}
                        </span>
                    </div>
                </div>

                <hr style="margin: 10px 0; border-color: #e9ecef;">

                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
                    <div class="info-content">
                        <span class="info-label">Nama Kegiatan</span>
                        <span class="info-value">{{ $data->kegiatan->nama_kegiatan ?? $data->kegiatan->judul ?? '-' }}</span>
                    </div>
                </div>
                
                <hr style="margin: 10px 0; border-color: #e9ecef;">

                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-calendar-event-fill"></i></div>
                    <div class="info-content">
                        <span class="info-label">Tanggal Pelaksanaan</span>
                        <span class="info-value">
                            @if(isset($data->kegiatan->tanggal_mulai) && isset($data->kegiatan->tanggal_selesai))
                                {{ \Carbon\Carbon::parse($data->kegiatan->tanggal_mulai)->isoFormat('D MMMM Y') }}
                                @if($data->kegiatan->tanggal_mulai != $data->kegiatan->tanggal_selesai)
                                    <span class="text-muted mx-1" style="font-weight: 400; font-size: 0.8rem;">s/d</span> 
                                    {{ \Carbon\Carbon::parse($data->kegiatan->tanggal_selesai)->isoFormat('D MMMM Y') }}
                                @endif
                            @else
                                {{ \Carbon\Carbon::parse($data->created_at)->isoFormat('D MMMM Y') }}
                            @endif
                        </span>
                    </div>
                </div>
            @endif

        </div>

        <div class="text-center mt-4">
            <a href="https://rsudslg.kedirikab.go.id/" class="btn btn-home">
                <i class="bi bi-globe2 me-2"></i> Kunjungi Website Utama
            </a>
            <div class="mt-3">
                <small class="text-muted" style="font-size: 0.7rem;">&copy; {{ date('Y') }} RSUD SLG Kediri. All Rights Reserved.</small>
            </div>
        </div>
    </div>

</body>
</html>