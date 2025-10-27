<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #7c1316;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            background: #fff;
            color: #333;
            border: none;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }
        .btn-maroon {
            background-color: #7c1316;
            color: #fff;
        }
        .btn-maroon:hover {
            background-color: #9d2a2e;
        }
    </style>
</head>
<body>
    <div class="card p-4" style="width: 360px;">
        <h4 class="text-center mb-3 fw-bold">Welcome Back 👋</h4>
        <p class="text-center text-muted mb-4">Login to continue</p>

        @if ($errors->any())
            <div class="alert alert-danger py-2">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-maroon w-100 mt-3">Login</button>
        </form>

        <footer class="text-center mt-4">
            <small class="text-muted">
                &copy; {{ date('Y') }} 
                <a href="https://akyas-bio.vercel.app" target="_blank" class="text-decoration-none text-muted">
                    MyApp. All rights reserved.
                </a>
            </small>
        </footer>
    </div>
</body>
</html>
