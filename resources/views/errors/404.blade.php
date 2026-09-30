<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #ffb347;
  
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 90vh;
            text-align: center;
            padding: 20px;
            color: #3a3a3a;
        }

        .container {
            max-width: 520px;
            animation: fadeIn 0.8s ease;
        }

        h1 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        p {
            font-size: 15px;
            margin-bottom: 22px;
        }

        img {
            width: 310px;
            margin-bottom: -95px;
            user-select: none;
        }

        .btn-group {
            margin-top: 22px;
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .btn {
            padding: 12px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: 0.25s;
        }

        .home {
            background: #fff;
            color: #ff7b00;
            border: 2px solid white;
        }

        .home:hover {
            background: #ffe8d0;
        }

        .contact {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .contact:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        footer {
            margin-top: 26px;
            font-size: 11px;
            opacity: 0.7;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <img src="{{ asset('icon.png') }}">

        <h1>Oops! Lost in the Internet?</h1>
        <p>Halaman yang kamu cari sepertinya kabur atau sudah tidak tersedia.</p>

        <div class="btn-group">
            <a href="{{ url('/dashboard') }}" class="btn home">Back Home</a>
            <!--<a href="https://akyas-bio.vercel.app" target="_blank" class="btn contact">Contact Us</a>-->
        </div>

        <footer>Illustration by <a href="https://chatgpt.com" target="_blank">Admin IT</footer>
    </div>
</body>

</html>



<!--<!DOCTYPE html>-->
<!--<html lang="id">-->

<!--<head>-->
<!--    <meta charset="UTF-8">-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1.0">-->
<!--    <title>402 — Payment Required</title>-->

<!--    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">-->

<!--    <style>-->
<!--        body {-->
<!--            font-family: 'Poppins', sans-serif;-->
       
<!--            background: linear-gradient(135deg, #ff9966, #ff5e62);-->
<!--            margin: 0;-->
<!--            display: flex;-->
<!--            justify-content: center;-->
<!--            align-items: center;-->
<!--            height: 100vh;-->
<!--            text-align: center;-->
<!--            padding: 20px;-->
<!--            color: #3a3a3a;-->
<!--            overflow: hidden;-->
<!--        }-->

<!--        .container {-->
<!--            background: rgba(255, 255, 255, 0.95);-->
<!--            padding: 40px;-->
<!--            border-radius: 20px;-->
<!--            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);-->
<!--            max-width: 480px;-->
<!--            width: 100%;-->
<!--            animation: slideUp 0.8s ease;-->
<!--            position: relative;-->
<!--        }-->

<!--        h1 {-->
<!--            font-size: 32px;-->
<!--            font-weight: 700;-->
<!--            margin: 20px 0 10px;-->
<!--            color: #d32f2f; -->
<!--        }-->

<!--        p {-->
<!--            font-size: 15px;-->
<!--            color: #555;-->
<!--            line-height: 1.6;-->
<!--            margin-bottom: 30px;-->
<!--        }-->

<!--        .illustration {-->
<!--            width: 180px;-->
<!--            height: auto;-->
<!--            margin-top: -80px; -->
<!--            margin-bottom: 10px;-->
<!--            filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));-->
<!--        }-->

<!--        .btn-group {-->
<!--            display: flex;-->
<!--            justify-content: center;-->
<!--            gap: 12px;-->
<!--            flex-wrap: wrap;-->
<!--        }-->

<!--        .btn {-->
<!--            padding: 12px 24px;-->
<!--            border-radius: 50px;-->
<!--            text-decoration: none;-->
<!--            font-size: 14px;-->
<!--            font-weight: 600;-->
<!--            transition: 0.3s;-->
<!--            display: inline-block;-->
<!--        }-->

<!--        .btn-primary {-->
<!--            background: #ff5e62;-->
<!--            color: white;-->
<!--            box-shadow: 0 4px 15px rgba(255, 94, 98, 0.4);-->
<!--        }-->

<!--        .btn-primary:hover {-->
<!--            background: #e04a4e;-->
<!--            transform: translateY(-2px);-->
<!--        }-->

<!--        .btn-outline {-->
<!--            background: transparent;-->
<!--            color: #555;-->
<!--            border: 2px solid #ddd;-->
<!--        }-->

<!--        .btn-outline:hover {-->
<!--            border-color: #ff5e62;-->
<!--            color: #ff5e62;-->
<!--        }-->

<!--        footer {-->
<!--            margin-top: 30px;-->
<!--            font-size: 12px;-->
<!--            color: #888;-->
<!--            border-top: 1px solid #eee;-->
<!--            padding-top: 15px;-->
<!--        }-->

<!--        footer a {-->
<!--            color: #ff5e62;-->
<!--            text-decoration: none;-->
<!--            font-weight: 600;-->
<!--        }-->

<!--        @keyframes slideUp {-->
<!--            from {-->
<!--                opacity: 0;-->
<!--                transform: translateY(30px);-->
<!--            }-->
<!--            to {-->
<!--                opacity: 1;-->
<!--                transform: translateY(0);-->
<!--            }-->
<!--        }-->
<!--    </style>-->
<!--</head>-->

<!--<body>-->
<!--    <div class="container">-->

<!--        <img src="https://cdn-icons-png.flaticon.com/512/2913/2913133.png" class="illustration" alt="Locked">-->

<!--        <h1>Aktivasi Diperlukan</h1>-->
<!--         <h2 style="padding:0px;">Sindikat -->
        
<!--         </h2>-->
<!--         <h3 style="font-style:italic;"> Sistem informasi Pendidikan, Pelatihan dan Penelitian</h3>-->
<!--        <p>-->
<!--            Maaf, masa aktif lisensi aplikasi Anda telah habis atau belum dibayar. -->
<!--            Fitur ini hanya tersedia untuk pengguna premium.-->
<!--            <br>-->
<!--            <strong>Silakan selesaikan administrasi untuk melanjutkan.</strong>-->
<!--        </p>-->

<!--        <div class="btn-group">-->
<!--            <a href="{{ url('/dashboard') }}" class="btn btn-outline">Kembali</a>-->
<!--            <a href="https://wa.me/6285117801836?text=Halo%20Admin,%20saya%20ingin%20perpanjang%20aplikasi" target="_blank" class="btn btn-primary">-->
<!--                Hubungi Admin-->
<!--            </a>-->
<!--        </div>-->

<!--        <footer>-->
<!--            Butuh Bantuan? Hubungi <a href="https://wa.me/6285117801836?text=Halo%20Admin,%20saya%20ingin%20perpanjang%20aplikasi" target="_blank">IT Support RSUD SLG</a>-->
<!--        </footer>-->
<!--    </div>-->
<!--</body>-->

<!--</html>-->
