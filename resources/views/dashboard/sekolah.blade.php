<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sekolah - SIG Sekolah</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 30px;
            color: #1e293b;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 5px 20px rgba(0,0,0,.08);
        }

        h1 {
            color: #15803d;
        }

        .info {
            background: #f0fdf4;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        button {
            background: #dc2626;
            color: white;
            border: none;
            padding: 11px 20px;
            border-radius: 7px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Dashboard Operator Sekolah</h1>

    <p>Selamat datang, {{ auth()->user()->name }}!</p>

    <div class="info">
        <h3>Informasi Akun</h3>
        <p>Email: {{ auth()->user()->email }}</p>
        <p>Role: {{ auth()->user()->roleLabel() }}</p>
        <p>Sekolah: {{ auth()->user()->sekolah?->nama ?? 'Belum terhubung' }}</p>
    </div>

    <p>Anda masuk sebagai operator sekolah.</p>

    <a href="{{ route('peta.index') }}">Lihat Peta Sekolah</a>

    <br><br>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>
</div>

</body>
</html>