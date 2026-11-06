
@extends('layouts.app')

@section('title', 'Profil Publik Sekolah')

@section('content')

<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header Profil --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex flex-col md:flex-row items-center gap-6">

            @if($profil && $profil->logo)
                <img
                    src="{{ asset('storage/' . $profil->logo) }}"
                    alt="Logo {{ $sekolah->nama }}"
                    class="w-32 h-32 object-contain rounded-lg border p-2"
                >
            @else
                <div class="w-32 h-32 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                    Logo Sekolah
                </div>
            @endif

            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    {{ $sekolah->nama }}
                </h1>

                <p class="text-gray-500 mt-2">
                    NPSN: {{ $sekolah->npsn }}
                </p>

                <p class="text-gray-500">
                    {{ $sekolah->alamat ?? 'Alamat belum tersedia' }}
                </p>
            </div>

        </div>
    </div>

    {{-- Informasi Sekolah --}}
    <div class="bg-white rounded-xl shadow p-6">

        <h2 class="text-xl font-bold text-gray-800 mb-4">
            Informasi Sekolah
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <div>
                <p class="text-sm text-gray-500">Kepala Sekolah</p>
                <p class="font-semibold">
                    {{ $profil->kepala_sekolah ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="font-semibold">
                    {{ $profil->email ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Website</p>

                @if($profil && $profil->website)
                    <a
                        href="{{ $profil->website }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-blue-600 hover:underline"
                    >
                        {{ $profil->website }}
                    </a>
                @else
                    <p class="font-semibold">-</p>
                @endif
            </div>

            <div>
                <p class="text-sm text-gray-500">Tahun Berdiri</p>
                <p class="font-semibold">
                    {{ $profil->tahun_berdiri ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Akreditasi</p>
                <p class="font-semibold">
                    {{ $profil->akreditasi ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Jumlah Siswa</p>
                <p class="font-semibold">
                    {{ $profil->jumlah_siswa ?? 0 }} siswa
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Jumlah Guru</p>
                <p class="font-semibold">
                    {{ $profil->jumlah_guru ?? 0 }} guru
                </p>
            </div>

        </div>
    </div>

    {{-- Deskripsi Sekolah --}}
    <div class="bg-white rounded-xl shadow p-6">

        <h2 class="text-xl font-bold text-gray-800 mb-4">
            Tentang Sekolah
        </h2>

        <p class="text-gray-700 leading-relaxed whitespace-pre-line">
            {{ $profil->deskripsi ?? 'Deskripsi sekolah belum tersedia.' }}
        </p>

    </div>

    {{-- Tombol Kembali --}}
    <div>
        <a
            href="{{ route('peta.index') }}"
            class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg"
        >
            Kembali ke Peta
        </a>
    </div>

</div>

@endsection