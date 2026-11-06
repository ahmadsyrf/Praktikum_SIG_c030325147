
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SIG Sekolah - Peta Persebaran Sekolah</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <!-- Marker Cluster CSS -->
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">

    <link rel="stylesheet"
          href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        /* HEADER */
        header {
            background: linear-gradient(135deg, #075985, #0e7490);
            color: white;
            padding: 25px 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
        }

        .header-content {
            max-width: 1400px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .brand-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.18);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 27px;
        }

        header h1 {
            margin: 0;
            font-size: 25px;
        }

        header p {
            margin: 6px 0 0;
            color: #dbeafe;
            font-size: 14px;
        }

        .header-link {
            display: inline-block;
            padding: 11px 18px;
            background: white;
            color: #075985;
            border-radius: 9px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .header-link:hover {
            background: #e0f2fe;
            transform: translateY(-2px);
        }

        /* KONTEN */
        .container {
            max-width: 1400px;
            margin: 25px auto;
            padding: 0 20px;
        }

        /* STATISTIK */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 13px;
            box-shadow: 0 3px 12px rgba(15, 23, 42, 0.05);
            border: 1px solid #e2e8f0;
        }

        .stat-label {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 25px;
            font-weight: 700;
            color: #075985;
        }

        /* PANEL FILTER */
        .filter-panel {
            background: white;
            padding: 20px;
            border-radius: 13px;
            box-shadow: 0 3px 12px rgba(15, 23, 42, 0.05);
            border: 1px solid #e2e8f0;
            margin-bottom: 20px;
        }

        .filter-title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #0f172a;
        }

        .controls {
            display: grid;
            grid-template-columns: 1fr 1fr 2fr auto;
            gap: 12px;
        }

        select,
        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            outline: none;
        }

        select:focus,
        input:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        }

        button {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            background: #075985;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        button:hover {
            background: #0c4a6e;
        }

        /* PETA */
        .map-card {
            background: white;
            padding: 12px;
            border-radius: 14px;
            box-shadow: 0 3px 15px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
        }

        .map-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 8px 8px 15px;
            flex-wrap: wrap;
        }

        .map-heading h2 {
            margin: 0;
            font-size: 18px;
        }

        .map-heading span {
            font-size: 12px;
            color: #64748b;
        }

        #map {
            width: 100%;
            height: 72vh;
            min-height: 450px;
            border-radius: 10px;
            z-index: 1;
        }

        /* MARKER SEKOLAH */
        .marker-sekolah {
            width: 22px;
            height: 22px;
            border: 3px solid white;
            border-radius: 50%;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.5);
        }

        .leaflet-div-icon {
            background: transparent !important;
            border: none !important;
        }

        /* LEGENDA */
        .legenda {
            background: white;
            padding: 13px 16px;
            line-height: 25px;
            border-radius: 9px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
            font-size: 13px;
        }

        .bulat {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 7px;
        }

        /* POPUP */
        .popup-sekolah {
            min-width: 220px;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        .popup-sekolah h3 {
            color: #075985;
            font-size: 16px;
            margin: 0 0 10px;
            padding-bottom: 9px;
            border-bottom: 1px solid #e2e8f0;
        }

        .popup-info {
            font-size: 12px;
            line-height: 1.8;
            color: #475569;
        }

        .popup-info strong {
            color: #1e293b;
        }

        .popup-profil {
            display: block;
            margin-top: 12px;
            padding: 10px;
            text-align: center;
            background: #075985;
            color: white !important;
            border-radius: 7px;
            text-decoration: none !important;
            font-weight: 600;
            font-size: 13px;
        }

        .popup-profil:hover {
            background: #0c4a6e;
        }

        /* FOOTER */
        footer {
            text-align: center;
            padding: 25px 15px;
            color: #64748b;
            font-size: 13px;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            header {
                padding: 20px;
            }

            header h1 {
                font-size: 19px;
            }

            .container {
                margin: 15px auto;
                padding: 0 12px;
            }

            .controls {
                grid-template-columns: 1fr;
            }

            #map {
                height: 65vh;
                min-height: 400px;
            }

            .header-content {
                align-items: flex-start;
            }

            .header-link {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>

<!-- HEADER -->
<header>
    <div class="header-content">

        <div class="brand">
            <div class="brand-icon">SIG</div>

            <div>
                <h1>Sistem Informasi Geografis Sekolah</h1>
                <p>Peta Persebaran Sekolah Kota Banjarmasin</p>
            </div>
        </div>

        <div>
            @auth
                <a href="{{ route('dashboard') }}" class="header-link">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="header-link">
                    Masuk Pengelola
                </a>
            @endauth
        </div>

    </div>
</header>


<main class="container">

    <!-- STATISTIK -->
    <section class="stats">

        <div class="stat-card">
            <div class="stat-label">Total Sekolah Ditampilkan</div>
            <div class="stat-value" id="jumlah">Memuat...</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Wilayah Pemetaan</div>
            <div class="stat-value">Banjarmasin</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Sumber Peta</div>
            <div class="stat-value">OpenStreetMap</div>
        </div>

    </section>


    <!-- FILTER DAN PENCARIAN -->
    <section class="filter-panel">

        <div class="filter-title">
            Pencarian dan Filter Sekolah
        </div>

        <div class="controls">

            <select id="filterJenjang">
                <option value="">Semua Jenjang</option>
                <option value="SD">SD</option>
                <option value="SMP">SMP</option>
                <option value="SMA">SMA</option>
                <option value="SMK">SMK</option>
            </select>

            <select id="filterStatus">
                <option value="">Semua Status</option>
                <option value="Negeri">Negeri</option>
                <option value="Swasta">Swasta</option>
            </select>

            <input
                type="text"
                id="cariSekolah"
                placeholder="Masukkan nama sekolah..."
            >

            <button type="button" onclick="cariSekolah()">
                Cari Sekolah
            </button>

        </div>

    </section>


    <!-- PETA -->
    <section class="map-card">

        <div class="map-heading">
            <h2>Peta Persebaran Sekolah</h2>
            <span>Klik marker untuk melihat informasi sekolah</span>
        </div>

        <div id="map"></div>

    </section>

</main>


<footer>
    Sistem Informasi Geografis Sekolah &copy; {{ date('Y') }}
    <br>
    Peta Persebaran Sekolah Kota Banjarmasin
</footer>


<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Marker Cluster JS -->
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>


<script>

    // ======================================
    // 1. MEMBUAT PETA
    // ======================================

    const map = L.map('map').setView([-3.3194, 114.5908], 13);


    // ======================================
    // 2. BASEMAP OPENSTREETMAP
    // ======================================

    const osm = L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }
    );


    // ======================================
    // 3. BASEMAP ESRI LIGHT GRAY
    // ======================================

    const esriLight = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}',
        {
            maxZoom: 16,
            attribution: 'Tiles &copy; Esri'
        }
    );


    // ======================================
    // 4. KONTROL BASEMAP
    // ======================================

    osm.addTo(map);

    L.control.layers(
        {
            'OpenStreetMap': osm,
            'Esri Light Gray': esriLight
        },
        null,
        {
            collapsed: false
        }
    ).addTo(map);


    // ======================================
    // 5. MARKER CLUSTER
    // ======================================

    const markerCluster = L.markerClusterGroup({
        showCoverageOnHover: false,
        spiderfyOnMaxZoom: true,
        zoomToBoundsOnClick: true,
        maxClusterRadius: 50
    });

    map.addLayer(markerCluster);

    let daftarMarker = [];


    // ======================================
    // 6. WARNA MARKER JENJANG
    // ======================================

    function warnaJenjang(jenjang) {

        const warna = {
            'SD': '#16a34a',
            'SMP': '#2563eb',
            'SMA': '#dc2626',
            'SMK': '#9333ea'
        };

        return warna[jenjang] || '#64748b';
    }


    // ======================================
    // 7. MENGAMANKAN TEKS POPUP
    // ======================================

    function escapeHTML(teks) {

        return String(teks ?? '').replace(/[&<>"']/g, function(karakter) {

            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[karakter];

        });
    }


    // ======================================
    // 8. MENAMPILKAN DATA SEKOLAH
    // ======================================

    async function tampilkanSekolah() {

        const jenjang = document.getElementById('filterJenjang').value;
        const status = document.getElementById('filterStatus').value;

        const params = new URLSearchParams();

        if (jenjang) {
            params.append('jenjang', jenjang);
        }

        if (status) {
            params.append('status', status);
        }

        try {

            const response = await fetch(
                "{{ url('/data/sekolah') }}?" + params.toString()
            );

            if (!response.ok) {
                throw new Error('Gagal mengambil data sekolah');
            }

            const data = await response.json();

            if (!data.features || !Array.isArray(data.features)) {
                throw new Error('Format GeoJSON tidak sesuai');
            }

            markerCluster.clearLayers();
            daftarMarker = [];

            data.features.forEach(function(feature) {

                const properti = feature.properties;
                const koordinat = feature.geometry.coordinates;

                const longitude = koordinat[0];
                const latitude = koordinat[1];

                const warna = warnaJenjang(properti.jenjang);

                const ikonSekolah = L.divIcon({

                    className: 'leaflet-div-icon',

                    html:
                        '<div class="marker-sekolah" style="background-color:' +
                        warna + ';"></div>',

                    iconSize: [22, 22],
                    iconAnchor: [11, 11],
                    popupAnchor: [0, -11]

                });


                // Membuat marker
                const marker = L.marker(
                    [latitude, longitude],
                    {
                        icon: ikonSekolah
                    }
                );


                // ======================================
                // POPUP INFORMASI DAN LINK PROFIL
                // ======================================

                const popup = `
                    <div class="popup-sekolah">

                        <h3>
                            ${escapeHTML(properti.nama)}
                        </h3>

                        <div class="popup-info">

                            <strong>NPSN:</strong>
                            ${escapeHTML(properti.npsn)}<br>

                            <strong>Jenjang:</strong>
                            ${escapeHTML(properti.jenjang)}<br>

                            <strong>Status:</strong>
                            ${escapeHTML(properti.status)}<br>

                            <strong>Alamat:</strong>
                            ${escapeHTML(properti.alamat)}<br>

                            <strong>Kecamatan:</strong>
                            ${escapeHTML(properti.kecamatan)}<br>

                            <strong>Telepon:</strong>
                            ${escapeHTML(properti.telepon) || '-'}<br>

                            <strong>Kepala Sekolah:</strong>
                            ${escapeHTML(properti.kepala_sekolah) || '-'}<br>

                            <strong>Akreditasi:</strong>
                            ${escapeHTML(properti.akreditasi) || '-'}<br>

                            <strong>Jumlah Siswa:</strong>
                            ${escapeHTML(properti.jumlah_siswa ?? '-')}<br>

                            <strong>Jumlah Guru:</strong>
                            ${escapeHTML(properti.jumlah_guru ?? '-')}

                        </div>

                        <a
                            href="${escapeHTML(properti.profil_url)}"
                            class="popup-profil"
                        >
                            Lihat Profil Sekolah
                        </a>

                    </div>
                `;


                marker.bindPopup(popup);

                daftarMarker.push({
                    nama: String(properti.nama ?? ''),
                    marker: marker
                });

                markerCluster.addLayer(marker);

            });


            // Memperbarui jumlah sekolah
            document.getElementById('jumlah').textContent =
                data.features.length + ' Sekolah';


        } catch (error) {

            console.error('Kesalahan:', error);

            document.getElementById('jumlah').textContent =
                'Gagal memuat data';

            alert(
                'Gagal memuat data sekolah. Periksa koneksi dan endpoint GeoJSON.'
            );

        }

    }


    // ======================================
    // 9. PENCARIAN SEKOLAH
    // ======================================

    function cariSekolah() {

        const kataKunci = document
            .getElementById('cariSekolah')
            .value
            .trim()
            .toLowerCase();

        if (!kataKunci) {
            alert('Masukkan nama sekolah.');
            return;
        }

        const hasil = daftarMarker.find(function(item) {

            return item.nama.toLowerCase().includes(kataKunci);

        });


        if (hasil) {

            markerCluster.zoomToShowLayer(
                hasil.marker,
                function() {

                    map.setView(hasil.marker.getLatLng(), 17);

                    hasil.marker.openPopup();

                }
            );

        } else {

            alert('Sekolah tidak ditemukan.');

        }

    }


    // ======================================
    // 10. PENCARIAN DENGAN ENTER
    // ======================================

    document.getElementById('cariSekolah')
        .addEventListener('keydown', function(event) {

            if (event.key === 'Enter') {
                cariSekolah();
            }

        });


    // ======================================
    // 11. FILTER JENJANG
    // ======================================

    document.getElementById('filterJenjang')
        .addEventListener('change', tampilkanSekolah);


    // ======================================
    // 12. FILTER STATUS
    // ======================================

    document.getElementById('filterStatus')
        .addEventListener('change', tampilkanSekolah);


    // ======================================
    // 13. LEGENDA
    // ======================================

    const legenda = L.control({
        position: 'bottomright'
    });

    legenda.onAdd = function() {

        const div = L.DomUtil.create('div', 'legenda');

        div.innerHTML = `
            <b>Legenda Jenjang Sekolah</b><br>

            <span class="bulat" style="background:#16a34a"></span>
            SD<br>

            <span class="bulat" style="background:#2563eb"></span>
            SMP<br>

            <span class="bulat" style="background:#dc2626"></span>
            SMA<br>

            <span class="bulat" style="background:#9333ea"></span>
            SMK
        `;

        return div;

    };

    legenda.addTo(map);


    // ======================================
    // 14. MEMUAT DATA AWAL
    // ======================================

    tampilkanSekolah();

    setTimeout(function() {

        map.invalidateSize();

    }, 500);

</script>

</body>
</html>
