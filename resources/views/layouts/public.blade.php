<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Halaman')</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="/favicon.png">

    {{-- Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap (Opsional, dipakai kalau perlu komponen siap pakai) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Base Style untuk Public Page --}}
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-family: "Inter", sans-serif;
            background: #f5f6fa;
        }

        .main-container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 18px;
        }

        /* Smooth Animation */
        * {
            transition: 0.25s ease;
        }

        @media(max-width: 480px) {
            body {
                padding: 8px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="main-container">
        @yield('content')
    </div>

    {{-- Script --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
