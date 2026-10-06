<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
    :root {
        --primary-color: #004aad;
        --secondary-color: #f9c300;
        --hover-color: #003389;
        --text-color: #333;
        --light-gray: #f5f5f5;
        --border-color: #ddd;
        --error-color: #e74c3c;
        --success-color: #2ecc71;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #233453;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        color: var(--text-color);
    }

    .login-wrapper {
        background-color: white;
        border-radius: 15px;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        width: 100%;
        max-width: 420px;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .login-wrapper:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .login-wrapper h1 {
        font-size: 1.8rem;
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        font-weight: 700;
    }

    .logo-container {
        margin: 0 auto 1.5rem;
        text-align: center;
    }

    .logo {
        height: 100px;
        width: auto;
        max-width: 100%;
        object-fit: contain;
    }

    .form-group {
        margin-bottom: 1.5rem;
        text-align: left;
        position: relative;
    }

    label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--primary-color);
        font-size: 0.95rem;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        background-color: var(--light-gray);
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0, 74, 173, 0.2);
        outline: none;
        background-color: white;
    }

    .btn-login {
        background-color: var(--primary-color);
        color: white;
        padding: 0.8rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 1rem;
        width: 100%;
        transition: background-color 0.3s ease, transform 0.2s ease;
        margin-top: 0.5rem;
    }

    .btn-login:hover {
        background-color: var(--hover-color);
        transform: translateY(-2px);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .footer {
        margin-top: 1.5rem;
        font-size: 0.8rem;
        color: #666;
        line-height: 1.5;
    }

    .alert {
        padding: 0.8rem 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .alert-success {
        background-color: rgba(46, 204, 113, 0.1);
        color: var(--success-color);
        border: 1px solid var(--success-color);
    }

    .alert-danger {
        background-color: rgba(231, 76, 60, 0.1);
        color: var(--error-color);
        border: 1px solid var(--error-color);
    }

    .error-message {
        color: var(--error-color);
        font-size: 0.8rem;
        margin-top: 0.3rem;
        display: block;
    }

    @media (max-width: 480px) {
        .login-wrapper {
            padding: 1.5rem;
            margin: 0 1rem;
        }

        .login-wrapper h1 {
            font-size: 1.5rem;
        }

        .logo {
            height: 80px;
        }
    }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="logo-container">
            <img src="{{ asset('timetable/datesheetLogo.png') }}" alt="Exam Centre Logo" class="logo">
        </div>

        <h1>Login to Frobel Education</h1>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ url('custom-login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus>
                @error('username')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <div class="footer">
            © {{ date('Y') }} Frobel Education. All rights reserved.
        </div>
    </div>
</body>

</html>
