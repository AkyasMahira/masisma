<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - {{ $form->judul }}</title>
    
    {{-- FONTS & ICONS --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        :root {
            --primary-color: #7c1316;
            --primary-hover: #5e0e10;
            --bg-color: #f0f2f5;
            --text-dark: #2c3e50;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .success-card {
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            max-width: 550px;
            width: 90%;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            border-top: 6px solid var(--primary-color);
            position: relative;
            overflow: hidden;
        }

        .icon-wrapper {
            width: 100px;
            height: 100px;
            background-color: #e8f5e9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: bounceIn 0.8s;
        }

        .icon-wrapper i {
            color: #2e7d32;
            font-size: 50px;
        }

        .success-title {
            font-weight: 700;
            font-size: 26px;
            margin-bottom: 10px;
            color: var(--text-dark);
        }

        .success-desc {
            color: #6c757d;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 40px;
        }

        .btn-home {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: block;
            width: 100%;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(124, 19, 22, 0.2);
            border: none;
        }

        .btn-home:hover {
            background-color: var(--primary-hover);
            color: white;
            transform: translateY(-2px);
            text-decoration: none;
        }

        .btn-back-form {
            background-color: transparent;
            color: #6c757d;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: block;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s;
            border: 2px solid #e9ecef;
        }

        .btn-back-form:hover {
            background-color: #f8f9fa;
            color: var(--text-dark);
            text-decoration: none;
            border-color: #dee2e6;
        }

        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { opacity: 1; transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>

    <div class="success-card animate__animated animate__fadeInUp">
        
        <div class="icon-wrapper">
            <i class="fas fa-check"></i>
        </div>

        <h1 class="success-title">Pendaftaran Berhasil!</h1>
        
        <p class="success-desc">
            Terima kasih telah mendaftar pada acara:<br>
            <strong style="color: var(--primary-color);">{{ $form->judul }}</strong>
            <br><br>
            Data Anda (dan rombongan) telah berhasil kami simpan. Silakan tunggu informasi selanjutnya melalui email penanggung jawab.
        </p>

        <div class="row">
            <div class="col-md-12 mb-2">
                {{-- TOMBOL KE LANDING PAGE (BERANDA) --}}
                {{-- Menggunakan URL absolut sesuai request IP server --}}
                <a href="http://103.139.47.173/sindikat/landing" class="btn-home">
                    <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                </a>
            </div>
            <div class="col-md-12">
                {{-- TOMBOL KEMBALI KE FORM (DAFTAR LAGI/EDIT) --}}
                <a href="{{ route('diklat.public.form', $form->public_link) }}" class="btn-back-form">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Form
                </a>
            </div>
        </div>

        <div class="mt-4 text-muted" style="font-size: 12px;">
            &copy; {{ date('Y') }} Sistem Informasi Diklat
        </div>

    </div>

</body>
</html>