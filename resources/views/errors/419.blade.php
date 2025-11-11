<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 — Page Expired</title>

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

        <!-- Illustration CDN / Asset -->
        <img src="{{ asset('icon.png') }}" alt="Expired">

        <h1>Session Expired!</h1>
        <p>Sesi kamu sudah berakhir. Refresh halaman atau login ulang untuk melanjutkan.</p>

        <div class="btn-group">
            <a href="{{ url('/login') }}" class="btn home">Login Again</a>
            <a href="https://akyas-bio.vercel.app" target="_blank" class="btn contact">Contact Support</a>
        </div>

        <footer>Illustration by <a href="https://chatgpt.com" target="_blank">Sora Ai</a></footer>
    </div>
</body>

</html>
