@extends('layouts.app')

@section('title', 'Ubah Kata Sandi')

@section('content')

<style>
    .password-wrapper {
        max-width: 650px;
        margin: 20px auto;
    }

    .password-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .07);
    }

    .password-header {
        background: #0B6687;
        color: white;
        padding: 27px;
    }

    .password-body {
        padding: 30px;
    }

    .password-label {
        display: block;
        color: #334155;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 9px;
    }

    .password-input {
        width: 100%;
        padding: 13px 15px;
        border: 1px solid #cbd5e1;
        border-radius: 11px;
        outline: none;
        color: #1e293b;
        background: #f8fafc;
        transition: .2s;
    }

    .password-input:focus {
        border-color: #0B6687;
        background: white;
        box-shadow: 0 0 0 3px rgba(11, 102, 135, .12);
    }

    .password-group {
        margin-bottom: 22px;
    }

    .password-submit {
        background: #0B6687;
        color: white;
        padding: 13px 22px;
        border-radius: 11px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: .2s;
    }

    .password-submit:hover {
        background: #075B7D;
        transform: translateY(-2px);
    }

    .password-back {
        display: inline-block;
        color: #0B6687;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
    }

    .password-back:hover {
        color: #075B7D;
    }

    @media (max-width: 640px) {
        .password-body {
            padding: 22px;
        }
    }
</style>

<div class="password-wrapper">

    <!-- JUDUL HALAMAN -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">
            Pengaturan Kata Sandi
        </h1>

        <p class="text-sm text-slate-500 mt-2">
            Perbarui kata sandi akun Anda untuk menjaga keamanan.
        </p>
    </div>


    <!-- CARD PASSWORD -->
    <div class="password-card">

        <!-- HEADER -->
        <div class="password-header">

            <div class="flex items-center gap-4">

                <div class="w-14 h-14 rounded-xl bg-white/15 flex items-center justify-center text-3xl">
                    🔐
                </div>

                <div>
                    <h2 class="text-xl font-bold">
                        Ubah Kata Sandi
                    </h2>

                    <p class="text-blue-100 text-sm mt-1">
                        Keamanan akun SIG Sekolah
                    </p>
                </div>

            </div>

        </div>


        <!-- BODY -->
        <div class="password-body">

            <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-100">

                <p class="text-sm text-[#0B6687]">
                    <strong>Informasi keamanan</strong>
                </p>

                <p class="text-xs text-slate-600 mt-1">
                    Masukkan kata sandi saat ini, kemudian buat kata sandi baru dengan minimal 8 karakter.
                </p>

            </div>


            <form action="{{ route('password.update') }}" method="POST">

                @csrf
                @method('PUT')


                <!-- PASSWORD LAMA -->
                <div class="password-group">

                    <label for="current_password" class="password-label">
                        Kata Sandi Saat Ini
                    </label>

                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        class="password-input"
                        placeholder="Masukkan kata sandi saat ini"
                        autocomplete="current-password"
                        required
                    >

                </div>


                <!-- PASSWORD BARU -->
                <div class="password-group">

                    <label for="password" class="password-label">
                        Kata Sandi Baru
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="password-input"
                        placeholder="Masukkan kata sandi baru"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >

                </div>


                <!-- KONFIRMASI PASSWORD -->
                <div class="password-group">

                    <label for="password_confirmation" class="password-label">
                        Konfirmasi Kata Sandi Baru
                    </label>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="password-input"
                        placeholder="Ulangi kata sandi baru"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >

                </div>


                <!-- TOMBOL -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-7">

                    <a href="{{ route('dashboard') }}" class="password-back">
                        ← Kembali ke Dashboard
                    </a>

                    <button type="submit" class="password-submit w-full sm:w-auto">
                        Simpan Kata Sandi
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection