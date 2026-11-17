@php
    $teks = [
        'npsn' => 'NPSN (8 digit)',
        'nama' => 'Nama Sekolah',
        'kecamatan' => 'Kecamatan',
        'telepon' => 'Telepon',
    ];

    $pilihan = [
        'jenjang' => ['SD', 'SMP', 'SMA', 'SMK'],
        'status' => ['Negeri', 'Swasta'],
    ];

    $cls = 'w-full border rounded px-3 py-2';
@endphp

<div class="bg-white rounded shadow p-6 grid md:grid-cols-2 gap-6">

    <div class="space-y-4">

        @foreach ($teks as $name => $label)
            <div>
                <label for="{{ $name }}" class="block font-medium">
                    {{ $label }}
                </label>

                <input
                    type="text"
                    id="{{ $name }}"
                    name="{{ $name }}"
                    value="{{ old($name, $sekolah->$name) }}"
                    @readonly($name === 'npsn' && auth()->user()->isSekolah())
                    class="{{ $cls }}"
                >

                @error($name)
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        @foreach ($pilihan as $name => $opsi)
            <div>
                <label for="{{ $name }}" class="block font-medium">
                    {{ ucfirst($name) }}
                </label>

                <select
                    id="{{ $name }}"
                    name="{{ $name }}"
                    class="{{ $cls }}"
                >
                    <option value="">-- Pilih --</option>

                    @foreach ($opsi as $o)
                        <option
                            value="{{ $o }}"
                            @selected(old($name, $sekolah->$name) === $o)
                        >
                            {{ $o }}
                        </option>
                    @endforeach
                </select>

                @error($name)
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        <div>
            <label for="alamat" class="block font-medium">
                Alamat
            </label>

            <textarea
                id="alamat"
                name="alamat"
                rows="3"
                class="{{ $cls }}"
            >{{ old('alamat', $sekolah->alamat) }}</textarea>

            @error('alamat')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            <button
                type="button"
                id="isiAlamatDariPeta"
                style="background-color: #4f46e5; color: white;"
                class="mt-3 px-4 py-2 rounded-lg font-semibold shadow-md hover:opacity-90 transition"
            >
                Isi Alamat dari Peta
            </button>

            <p
                id="statusAlamat"
                class="text-sm text-gray-600 mt-2"
            ></p>
        </div>

    </div>

    <div class="space-y-4">

        <p class="font-medium">
            Klik peta atau geser marker untuk menentukan lokasi sekolah
        </p>

        <div id="map-picker" class="h-80 rounded border"></div>

        <div class="grid grid-cols-2 gap-4">

            <div>
                <label for="latitude" class="block font-medium">
                    Latitude
                </label>

                <input
                    type="number"
                    step="any"
                    id="latitude"
                    name="latitude"
                    value="{{ old('latitude', $sekolah->latitude) }}"
                    class="{{ $cls }}"
                >

                @error('latitude')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="longitude" class="block font-medium">
                    Longitude
                </label>

                <input
                    type="number"
                    step="any"
                    id="longitude"
                    name="longitude"
                    value="{{ old('longitude', $sekolah->longitude) }}"
                    class="{{ $cls }}"
                >

                @error('longitude')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="space-y-2">

            <button
                type="button"
                id="gunakanLokasi"
                style="background-color: #16a34a; color: white;"
                class="px-4 py-2 rounded-lg font-semibold shadow-md hover:opacity-90 transition"
            >
                Gunakan Lokasi Saya
            </button>

            <p id="statusLokasi" class="text-sm text-gray-600"></p>

        </div>

        <div class="flex gap-3 pt-2">

            <button
                type="submit"
                style="background-color: #1d4ed8; color: white;"
                class="px-5 py-2 rounded-lg font-semibold"
            >
                Simpan
            </button>

            <a
                href="{{ auth()->user()->isDinas()
                    ? route('dinas.sekolah.index')
                    : route('dashboard') }}"
                class="px-5 py-2 bg-gray-300 rounded-lg font-semibold"
            >
                Batal
            </a>

        </div>

    </div>

</div>

@push('scripts')
<script>
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    const tombolLokasi = document.getElementById('gunakanLokasi');
    const statusLokasi = document.getElementById('statusLokasi');

    const alamatInput = document.getElementById('alamat');
    const tombolAlamat = document.getElementById('isiAlamatDariPeta');
    const statusAlamat = document.getElementById('statusAlamat');

    const sudahAda =
        latInput.value !== '' && lngInput.value !== '';

    const pusat = sudahAda
        ? [parseFloat(latInput.value), parseFloat(lngInput.value)]
        : [-3.3194, 114.5908];

    const map = L.map('map-picker').setView(
        pusat,
        sudahAda ? 16 : 13
    );

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    const marker = L.marker(pusat, {
        draggable: true
    }).addTo(map);

    function isiKoordinat(lat, lng) {
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
    }

    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        isiKoordinat(e.latlng.lat, e.latlng.lng);
    });

    marker.on('dragend', function () {
        const posisi = marker.getLatLng();
        isiKoordinat(posisi.lat, posisi.lng);
    });

    [latInput, lngInput].forEach(function (el) {
        el.addEventListener('change', function () {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);

            if (
                latInput.value !== '' &&
                lngInput.value !== '' &&
                Number.isFinite(lat) &&
                Number.isFinite(lng) &&
                lat >= -90 && lat <= 90 &&
                lng >= -180 && lng <= 180
            ) {
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], 16);
            }
        });
    });

    // Mengambil lokasi perangkat
    tombolLokasi.addEventListener('click', function () {

        if (!navigator.geolocation) {
            statusLokasi.textContent =
                'Browser tidak mendukung fitur lokasi.';
            return;
        }

        tombolLokasi.disabled = true;
        statusLokasi.textContent =
            'Sedang mengambil lokasi...';

        navigator.geolocation.getCurrentPosition(

            function (posisi) {
                const lat = posisi.coords.latitude;
                const lng = posisi.coords.longitude;

                isiKoordinat(lat, lng);
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], 16);

                statusLokasi.textContent =
                    'Lokasi berhasil diambil.';

                tombolLokasi.disabled = false;
            },

            function (error) {
                let pesan = 'Gagal mengambil lokasi.';

                if (error.code === 1) {
                    pesan = 'Izin lokasi ditolak. Izinkan akses lokasi pada browser.';
                } else if (error.code === 2) {
                    pesan = 'Lokasi tidak tersedia. Periksa GPS atau koneksi perangkat.';
                } else if (error.code === 3) {
                    pesan = 'Waktu pengambilan lokasi habis. Silakan coba lagi.';
                }

                statusLokasi.textContent = pesan;
                tombolLokasi.disabled = false;
            },

            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            }
        );
    });

    // Reverse Geocoding: koordinat menjadi alamat
    tombolAlamat.addEventListener('click', async function () {

        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);

        if (
            latInput.value === '' ||
            lngInput.value === '' ||
            !Number.isFinite(lat) ||
            !Number.isFinite(lng) ||
            lat < -90 || lat > 90 ||
            lng < -180 || lng > 180
        ) {
            statusAlamat.textContent =
                'Tentukan lokasi sekolah pada peta terlebih dahulu.';
            return;
        }

        tombolAlamat.disabled = true;
        statusAlamat.textContent =
            'Sedang mencari alamat...';

        try {
            const parameter = new URLSearchParams({
                format: 'jsonv2',
                lat: lat,
                lon: lng,
                'accept-language': 'id'
            });

            const response = await fetch(
                'https://nominatim.openstreetmap.org/reverse?' + parameter
            );

            if (!response.ok) {
                throw new Error('Gagal menghubungi layanan pencarian alamat.');
            }

            const data = await response.json();

            if (!data.display_name) {
                throw new Error('Alamat tidak ditemukan.');
            }

            alamatInput.value = data.display_name;

            statusAlamat.textContent =
                'Alamat berhasil diisi dari lokasi peta.';

        } catch (error) {
            statusAlamat.textContent =
                error.message || 'Terjadi kesalahan saat mencari alamat.';
        } finally {
            tombolAlamat.disabled = false;
        }
    });
</script>
@endpush
