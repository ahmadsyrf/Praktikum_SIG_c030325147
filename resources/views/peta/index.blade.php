
@extends('layouts.public')

@section('title', 'Peta Persebaran Sekolah')

@push('styles')
<link rel="stylesheet"
      href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">

<link rel="stylesheet"
      href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">

<style>
    .dashboard-peta {
        max-width: 1400px;
        margin: 25px auto;
        padding: 0 20px;
    }

    .stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 13px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 3px 12px rgba(15, 23, 42, 0.05);
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

    .filter-panel {
        background: white;
        padding: 20px;
        border-radius: 13px;
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
    }

    .filter-title {
        font-size: 17px;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .controls {
        display: grid;
        grid-template-columns: 1fr 1fr 2fr auto;
        gap: 12px;
    }

    .controls select,
    .controls input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: white;
    }

    .controls button {
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        background: #075985;
        color: white;
        font-weight: 600;
        cursor: pointer;
    }

    .controls button:hover {
        background: #0c4a6e;
    }

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

    #map {
        width: 100%;
        height: 72vh;
        min-height: 450px;
        border-radius: 10px;
        z-index: 1;
    }

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

    .popup-sekolah {
        min-width: 220px;
        font-family: Arial, sans-serif;
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

    @media (max-width: 768px) {
        .dashboard-peta {
            padding: 0 12px;
        }

        .controls {
            grid-template-columns: 1fr;
        }

        #map {
            height: 65vh;
            min-height: 400px;
        }
    }
</style>
@endpush


@section('content')

<div class="dashboard-peta">

    {{-- STATISTIK --}}
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


    {{-- FILTER DAN PENCARIAN --}}
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

            <button type="button" id="tombolCari">
                Cari Sekolah
            </button>

        </div>

    </section>


    {{-- PETA --}}
    <section class="map-card">

        <div class="map-heading">
            <h2>Peta Persebaran Sekolah</h2>
            <span>Klik marker untuk melihat informasi sekolah</span>
        </div>

        <div id="map"></div>

    </section>

</div>

@endsection


@push('scripts')

<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // 1. MEMBUAT PETA
    const map = Peta.buat('map');

    // 2. BASEMAP ESRI
    const esriLight = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}',
        {
            maxZoom: 16,
            attribution: 'Tiles &copy; Esri'
        }
    );

    L.control.layers(
        null,
        {
            'Esri Light Gray': esriLight
        },
        {
            collapsed: false
        }
    ).addTo(map);


    // 3. MARKER CLUSTER
    const markerCluster = L.markerClusterGroup({
        showCoverageOnHover: false,
        spiderfyOnMaxZoom: true,
        zoomToBoundsOnClick: true,
        maxClusterRadius: 50
    });

    map.addLayer(markerCluster);

    let semuaSekolah = [];
    let daftarMarker = [];


    // 4. WARNA MARKER JENJANG
    function warnaJenjang(jenjang) {

        const warna = {
            'SD': '#16a34a',
            'SMP': '#2563eb',
            'SMA': '#dc2626',
            'SMK': '#9333ea'
        };

        return warna[jenjang] || '#64748b';
    }


    // 5. MENGAMANKAN TEKS POPUP
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


    // 6. MEMBUAT MARKER
    function buatMarker(feature) {

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


        // POPUP INFORMASI SEKOLAH
        const popup = `
            <div class="popup-sekolah">

                <h3>${escapeHTML(properti.nama)}</h3>

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

                ${
                    properti.profil_url
                    ? `<a href="${escapeHTML(properti.profil_url)}"
                         class="popup-profil">
                         Lihat Profil Sekolah
                       </a>`
                    : ''
                }

            </div>
        `;

        marker.bindPopup(popup);

        return marker;
    }


    // 7. MENAMPILKAN DATA SEKOLAH
    function tampilkanSekolah() {

        const jenjang = document.getElementById('filterJenjang').value;
        const status = document.getElementById('filterStatus').value;

        const kataKunci = document.getElementById('cariSekolah')
            .value.trim().toLowerCase();


        const hasil = semuaSekolah.filter(function(feature) {

            const p = feature.properties;

            const cocokJenjang = !jenjang || p.jenjang === jenjang;

            const cocokStatus = !status || p.status === status;

            const cocokNama = !kataKunci ||
                String(p.nama ?? '').toLowerCase().includes(kataKunci);

            return cocokJenjang && cocokStatus && cocokNama;

        });


        markerCluster.clearLayers();
        daftarMarker = [];


        hasil.forEach(function(feature) {

            const marker = buatMarker(feature);

            daftarMarker.push({
                nama: String(feature.properties.nama ?? ''),
                marker: marker
            });

            markerCluster.addLayer(marker);

        });


        // MEMPERBARUI JUMLAH SEKOLAH
        document.getElementById('jumlah').textContent =
            hasil.length + ' Sekolah';


        if (hasil.length > 0) {

            const bounds = L.latLngBounds(
                hasil.map(function(feature) {
                    const c = feature.geometry.coordinates;
                    return [c[1], c[0]];
                })
            );

            map.fitBounds(bounds.pad(0.1));

        }

    }


    // 8. MENGAMBIL DATA GEOJSON
    async function muatDataSekolah() {

        try {

            const response = await fetch(@json(route('peta.data')));

            if (!response.ok) {
                throw new Error('Gagal mengambil data sekolah');
            }

            const data = await response.json();

            if (!data.features || !Array.isArray(data.features)) {
                throw new Error('Format GeoJSON tidak sesuai');
            }

            semuaSekolah = data.features;

            tampilkanSekolah();

        } catch (error) {

            console.error('Kesalahan:', error);

            document.getElementById('jumlah').textContent =
                'Gagal memuat data';

        }

    }


    // 9. PENCARIAN SEKOLAH
    document.getElementById('tombolCari')
        .addEventListener('click', tampilkanSekolah);

    document.getElementById('cariSekolah')
        .addEventListener('keydown', function(event) {

            if (event.key === 'Enter') {
                tampilkanSekolah();
            }

        });


    // 10. FILTER JENJANG
    document.getElementById('filterJenjang')
        .addEventListener('change', tampilkanSekolah);


    // 11. FILTER STATUS
    document.getElementById('filterStatus')
        .addEventListener('change', tampilkanSekolah);


    // 12. LEGENDA
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


    // 13. MEMUAT DATA AWAL
    muatDataSekolah();

    setTimeout(function() {
        map.invalidateSize();
    }, 500);

});
</script>

@endpush
