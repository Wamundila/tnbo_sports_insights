<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TNBO Insights Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            background:
                radial-gradient(circle at top left, rgba(194, 91, 45, 0.16), transparent 30%),
                linear-gradient(135deg, #152633 0%, #29455c 100%);
            font-family: "Source Sans 3", sans-serif;
            color: #13212b;
        }

        .login-card {
            width: min(440px, calc(100vw - 2rem));
            background: rgba(255, 252, 246, 0.96);
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.18);
        }

        .login-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 3rem;
            height: 3rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, #f0c27b 0%, #c25b2d 100%);
            font-family: "Space Grotesk", sans-serif;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        h1 {
            font-family: "Space Grotesk", sans-serif;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-badge">I</div>
        <h1 class="h2 mb-2">TNBO Insights Admin</h1>
        <p class="text-secondary mb-4">Sign in with your admin account to manage sponsorship inventory and reporting.</p>

        @if ($errors->any())
            <div class="alert alert-danger rounded-4">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}" class="d-grid gap-3">
            @csrf
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg rounded-4" required autofocus>
            </div>
            <div>
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control form-control-lg rounded-4" required>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                <label class="form-check-label" for="remember">Keep me signed in</label>
            </div>
            <button type="submit" class="btn btn-primary btn-lg rounded-4">Sign in</button>
        </form>
    </div>
</body>
</html>
