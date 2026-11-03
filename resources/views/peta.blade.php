
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Peta Sekolah Banjarmasin</title>

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
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        header {
            background: #075985;
            color: white;
            padding: 20px;
        }

        header h2 {
            margin: 0 0 8px;
        }

        .controls {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 15px;
            background: white;
        }

        select,
        input,
        button,
        .admin-link {
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
        }

        button,
        .admin-link {
            background: #075985;
            color: white;
            cursor: pointer;
            text-decoration: none;
        }

        button:hover,
        .admin-link:hover {
            background: #0c4a6e;
        }

        #map {
            width: 100%;
            height: 82vh;
            min-height: 450px;
        }

        /* Marker sekolah */
        .marker-sekolah {
            width: 22px;
            height: 22px;
            border: 3px solid white;
            border-radius: 50%;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.6);
        }

        .leaflet-div-icon {
            background: transparent !important;
            border: none !important;
        }

        /* Legenda */
        .legenda {
            background: white;
            padding: 12px;
            line-height: 25px;
            border-radius: 6px;
            box-shadow: 0 1px 5px #999;
        }

        .bulat {
            display: inline-block;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            margin-right: 7px;
        }
    </style>
</head>

<body>

<header>
    <h2>Peta Persebaran Sekolah Banjarmasin</h2>
    <div id="jumlah">Memuat data sekolah...</div>
</header>

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

    <input type="text"
           id="cariSekolah"
           placeholder="Cari nama sekolah...">

    <button type="button" onclick="cariSekolah()">
        Cari Sekolah
    </button>

    <a href="{{ url('/admin/sekolah') }}" class="admin-link">
        Kelola Data
    </a>

</div>

<div id="map"></div>

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
    // 4. KONTROL PERGANTIAN BASEMAP
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

                const marker = L.marker(
                    [latitude, longitude],
                    {
                        icon: ikonSekolah
                    }
                );

                marker.bindPopup(`
                    <b>${escapeHTML(properti.nama)}</b><br>
                    NPSN: ${escapeHTML(properti.npsn)}<br>
                    Jenjang: ${escapeHTML(properti.jenjang)}<br>
                    Status: ${escapeHTML(properti.status)}<br>
                    Alamat: ${escapeHTML(properti.alamat)}<br>
                    Kecamatan: ${escapeHTML(properti.kecamatan)}
                `);

                daftarMarker.push({
                    nama: properti.nama,
                    marker: marker
                });

                markerCluster.addLayer(marker);
            });

            document.getElementById('jumlah').textContent =
                'Jumlah sekolah: ' + data.features.length;

        } catch (error) {
            console.error('Kesalahan:', error);

            document.getElementById('jumlah').textContent =
                'Gagal memuat data sekolah. Periksa koneksi dan endpoint GeoJSON.';
        }
    }


    // ======================================
    // 9. PENCARIAN SEKOLAH
    // ======================================

    function cariSekolah() {

        const kataKunci = document
            .getElementById('cariSekolah')
            .value.trim()
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
    // 13. LEGENDA WARNA
    // ======================================

    const legenda = L.control({
        position: 'bottomright'
    });

    legenda.onAdd = function() {

        const div = L.DomUtil.create('div', 'legenda');

        div.innerHTML = `
            <b>Legenda Jenjang Sekolah</b><br>
            <span class="bulat" style="background:#16a34a"></span> SD<br>
            <span class="bulat" style="background:#2563eb"></span> SMP<br>
            <span class="bulat" style="background:#dc2626"></span> SMA<br>
            <span class="bulat" style="background:#9333ea"></span> SMK
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
