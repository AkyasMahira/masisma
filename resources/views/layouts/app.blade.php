<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Icon Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --maroon: #7c1316;
            --maroon-light: #9d2a2e;
            --bg-light: #f8f9fa;
            --text-dark: #222;
            --text-muted: #6c757d;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: var(--maroon);
            color: #fff;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar-header {
            text-align: center;
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h4 {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .nav-link {
            color: #fff;
            padding: 0.8rem 1.25rem;
            display: flex;
            align-items: center;
            border-radius: 6px;
            margin: 0.2rem 0.5rem;
            transition: all 0.3s ease;
        }

        .nav-link i {
            margin-right: 8px;
            font-size: 1.1rem;
        }

        .nav-link.active,
        .nav-link:hover {
            background: var(--maroon-light);
            transform: translateX(4px);
        }

        /* Content */
        .content {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        /* Footer */
        footer {
            border-top: 1px solid #dee2e6;
            margin-top: 50px;
            padding: 1rem 0;
            color: var(--text-muted);
        }

        footer a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: var(--maroon);
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: rgba(124, 19, 22, 0.6);
            border-radius: 8px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: rgba(124, 19, 22, 0.9);
        }
    </style>
</head>

<body>

    {{-- Sidebar --}}
    @include('partials.sidebar')

    <div class="content">
        {{-- Isi Halaman --}}
        @yield('content')

        {{-- Footer --}}
        @include('partials.footer')
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
