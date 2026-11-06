
@extends('layouts.app')

@section('title', 'Tambah Sekolah')

@section('content')

<div class="bg-white rounded-xl shadow-md p-6">

    <h1 class="text-2xl font-bold mb-4 text-gray-800">
        Tambah Sekolah
    </h1>

    <p class="text-gray-600 mb-6">
        Silakan isi formulir berikut untuk menambahkan data sekolah baru.
    </p>

    <form method="POST" action="{{ route('dinas.sekolah.store') }}">
        @csrf

        @include('admin.sekolah._form', [
            'sekolah' => new \App\Models\Sekolah
        ])
    </form>

</div>

@endsection