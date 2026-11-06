
@extends('layouts.app')

@section('title', 'Data Sekolah')

@section('content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Data Sekolah</h1>

    <a href="{{ route('dinas.sekolah.create') }}"
       class="px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-800">
        + Tambah Sekolah
    </a>
</div>

<form method="GET" class="mb-4 flex gap-2">
    <input
        type="text"
        name="q"
        value="{{ request('q') }}"
        placeholder="Cari nama, NPSN, atau kecamatan"
        class="border rounded px-3 py-2 w-80"
    >

    <button class="px-4 py-2 bg-gray-700 text-white rounded">
        Cari
    </button>
</form>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-left">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-3">No</th>
                <th class="p-3">NPSN</th>
                <th class="p-3">Nama</th>
                <th class="p-3">Jenjang</th>
                <th class="p-3">Status</th>
                <th class="p-3">Kecamatan</th>
                <th class="p-3">Koordinat</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($sekolahs as $s)
                <tr class="border-b">
                    <td class="p-3">
                        {{ $sekolahs->firstItem() + $loop->index }}
                    </td>

                    <td class="p-3">{{ $s->npsn }}</td>
                    <td class="p-3">{{ $s->nama }}</td>
                    <td class="p-3">{{ $s->jenjang }}</td>
                    <td class="p-3">{{ $s->status }}</td>
                    <td class="p-3">{{ $s->kecamatan }}</td>

                    <td class="p-3 text-sm">
                        {{ $s->latitude }}, {{ $s->longitude }}
                    </td>

                    <td class="p-3 whitespace-nowrap">
                        <a
                            href="{{ route('sekolah.edit', $s) }}"
                            class="text-blue-700 hover:underline">
                            Ubah
                        </a>

                        <form
                            method="POST"
                            action="{{ route('dinas.sekolah.destroy', $s) }}"
                            onsubmit="return confirm('Hapus data ini?')"
                            class="inline">

                            @csrf
                            @method('DELETE')

                            <button class="text-red-600 hover:underline ml-2">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="p-4 text-center">
                        Belum ada data.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $sekolahs->links() }}
</div>

@endsection
