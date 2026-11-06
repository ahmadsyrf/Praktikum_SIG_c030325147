<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIG Sekolah</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #0f172a, #164e63, #0891b2);
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            padding: 35px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        h2 {
            margin-top: 0;
            color: #0f172a;
            text-align: center;
        }

        p {
            color: #64748b;
            text-align: center;
        }

        label {
            display: block;
            margin: 18px 0 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
        }

        button {
            width: 100%;
            margin-top: 25px;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: #0891b2;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #0e7490;
        }

        .error {
            margin-top: 15px;
            color: #dc2626;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <h2>SIG Sekolah</h2>
        <p>Silakan masuk ke akun Anda</p>

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf

            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Masukkan email"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Masukkan password"
                required
            >

            <button type="submit">Masuk</button>
        </form>
    </div>

</body>
</html>