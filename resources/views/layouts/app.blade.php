
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'SIG Sekolah') - SIG Sekolah</title>

    <!-- Leaflet CSS -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    >

    <!-- Tailwind CSS dan JavaScript Laravel -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-800 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-blue-800 text-white shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-4 flex flex-wrap items-center gap-6">

            <span class="font-bold text-xl">
                SIG Sekolah
            </span>

            <a href="{{ url('/') }}" class="hover:text-blue-200">
                Peta Publik
            </a>

            <a href="{{ route('admin.sekolah.index') }}"
               class="hover:text-blue-200">
                Data Sekolah
            </a>

        </div>
    </nav>

    <!-- Konten Halaman -->
    <main class="max-w-6xl mx-auto px-4 py-6">

        @if (session('success'))
            <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800 border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 border border-red-300">
                <strong>Terjadi kesalahan:</strong>
                <ul class="list-disc ml-5 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')

    </main>

    <!-- Leaflet JavaScript -->
    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin="">
    </script>

    <!-- Script dari halaman masing-masing -->
    @stack('scripts')

</body>
</html>
