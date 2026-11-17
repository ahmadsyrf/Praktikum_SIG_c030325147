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
        margin: 0 0 5px;
        font-size: 18px;
    }

    .btn-download {
        display: inline-block;
        padding: 10px 16px;
        background: #15803d;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .btn-download:hover {
        background: #166534;
    }

    .btn-download:disabled {
        background: #94a3b8;
        cursor: not-allowed;
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

    .kotak-legenda {
        display: inline-block;
        width: 13px;
        height: 13px;
        margin-right: 7px;
        border: 1px solid #64748b;
        vertical-align: middle;
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

        .map-heading {
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')

<div class="dashboard-peta">

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

    <section class="map-card">

        <div class="map-heading">

            <div>
                <h2>Peta Persebaran Sekolah</h2>
                <span>Klik marker atau wilayah kecamatan untuk melihat informasi</span>
            </div>

            <button type="button"
                    id="downloadGeoJSON"
                    class="btn-download">
                Download GeoJSON
            </button>

        </div>

        <div id="map"></div>

    </section>

</div>

@endsection

@push('scripts')

<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // 1. MEMBUAT PETA
    const map = Peta.buat('map');

    // 2. BASEMAP
    const osm = L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }
    );

    const esriSatellite = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        {
            maxZoom: 19,
            attribution: 'Imagery &copy; Esri, Maxar, Earthstar Geographics'
        }
    );

    // 3. LAPISAN MARKER
    const markerLayer = L.layerGroup();

    // 4. LAPISAN KLASTER
    const markerCluster = L.markerClusterGroup({
        showCoverageOnHover: false,
        spiderfyOnMaxZoom: true,
        zoomToBoundsOnClick: true,
        maxClusterRadius: 50
    });

    // 5. LAPISAN HEATMAP
    const heatLayer = L.heatLayer([], {
        radius: 30,
        blur: 20,
        maxZoom: 17,
        max: 1,
        minOpacity: 0.35
    });

    // 6. DATA BOUNDARY
    let geojsonKecamatan = null;
    let hasilTerakhir = [];
    let jumlahKecamatan = {};
    let semuaSekolah = [];

    // 7. NORMALISASI NAMA KECAMATAN
    function normalisasi(nama) {

        return String(nama ?? '')
            .toLowerCase()
            .replace(/^kecamatan\s+/i, '')
            .replace(/[^a-z0-9]/g, '')
            .trim();

    }

    // 8. NAMA KECAMATAN
    function ambilNamaKecamatan(feature) {

        const p = feature.properties || {};

        return (
            p.WADMKC ||
            p.wadmkc ||
            p.NAMOBJ ||
            p.namobj ||
            p.KECAMATAN ||
            p.kecamatan ||
            'Tidak diketahui'
        );

    }

    // 9. WARNA POLIGON
    function warnaJumlah(jumlah) {

        const nilai = Object.values(jumlahKecamatan);
        const maksimum = Math.max(0, ...nilai);

        if (jumlah === 0) {
            return '#cbd5e1';
        }

        const rendah = Math.ceil(maksimum / 3);
        const sedang = Math.ceil((maksimum * 2) / 3);

        if (jumlah <= rendah) {
            return '#86efac';
        }

        if (jumlah <= sedang) {
            return '#facc15';
        }

        return '#f87171';

    }

    // 10. STYLE POLIGON
    function styleKecamatan(feature) {

        const nama = ambilNamaKecamatan(feature);
        const jumlah = jumlahKecamatan[normalisasi(nama)] || 0;

        return {
            fillColor: warnaJumlah(jumlah),
            weight: 2,
            opacity: 1,
            color: '#334155',
            fillOpacity: 0.45
        };

    }

    // 11. ESCAPE HTML
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

    // 12. HITUNG SEKOLAH PER KECAMATAN
    function hitungSekolahKecamatan(dataSekolah) {

        jumlahKecamatan = {};

        dataSekolah.forEach(function(feature) {

            const nama = normalisasi(feature.properties.kecamatan);

            if (!nama) return;

            jumlahKecamatan[nama] =
                (jumlahKecamatan[nama] || 0) + 1;

        });

    }

    // 13. LEGENDA KECAMATAN
    let legendaKecamatanControl = null;

    function perbaruiLegendaKecamatan() {

        if (legendaKecamatanControl) {
            legendaKecamatanControl.remove();
        }

        legendaKecamatanControl = L.control({
            position: 'bottomleft'
        });

        legendaKecamatanControl.onAdd = function() {

            const div = L.DomUtil.create('div', 'legenda');

            const maksimum = Math.max(
                0,
                ...Object.values(jumlahKecamatan)
            );

            const rendah = Math.ceil(maksimum / 3);
            const sedang = Math.ceil((maksimum * 2) / 3);

            div.innerHTML = `
                <strong>Jumlah Sekolah per Kecamatan</strong><br>

                <span class="kotak-legenda"
                      style="background:#cbd5e1"></span>
                0 sekolah<br>

                <span class="kotak-legenda"
                      style="background:#86efac"></span>
                1 - ${rendah} sekolah<br>

                <span class="kotak-legenda"
                      style="background:#facc15"></span>
                ${rendah + 1} - ${sedang} sekolah<br>

                <span class="kotak-legenda"
                      style="background:#f87171"></span>
                Lebih dari ${sedang} sekolah
            `;

            return div;

        };

        legendaKecamatanControl.addTo(map);

    }

    // 14. PERBARUI BATAS KECAMATAN
    const kecamatanLayer = L.geoJSON(null, {
        style: styleKecamatan,

        onEachFeature: function(feature, layer) {

            const nama = ambilNamaKecamatan(feature);
            const jumlah = jumlahKecamatan[normalisasi(nama)] || 0;

            layer.bindPopup(`
                <div style="min-width:180px;">
                    <strong style="color:#075985;">
                        Kecamatan ${escapeHTML(nama)}
                    </strong>
                    <hr>
                    Jumlah sekolah:
                    <strong>${jumlah} sekolah</strong>
                </div>
            `);

            layer.bindTooltip(
                nama + ' (' + jumlah + ' sekolah)'
            );

        }
    });

    function perbaruiKecamatan() {

        if (!geojsonKecamatan) return;

        hitungSekolahKecamatan(hasilTerakhir);

        kecamatanLayer.clearLayers();
        kecamatanLayer.addData(geojsonKecamatan);

        perbaruiLegendaKecamatan();

    }

    // 15. MEMUAT GEOJSON BATAS KECAMATAN
    async function muatBatasKecamatan() {

        try {

            const urlGeoJSON = @json(asset('geojson/batas-kecamatan-banjarmasin.geojson'));

            const response = await fetch(urlGeoJSON);

            if (!response.ok) {
                throw new Error('Gagal mengambil GeoJSON kecamatan.');
            }

            const data = await response.json();

            if (!data.features || !Array.isArray(data.features)) {
                throw new Error('Format GeoJSON kecamatan tidak valid.');
            }

            geojsonKecamatan = data;

            perbaruiKecamatan();

            console.log('Batas kecamatan berhasil dimuat.');

        } catch (error) {

            console.error('Gagal memuat batas kecamatan:', error);

        }

    }

    // 16. WARNA MARKER
    function warnaJenjang(jenjang) {

        const warna = {
            'SD': '#16a34a',
            'SMP': '#2563eb',
            'SMA': '#dc2626',
            'SMK': '#9333ea'
        };

        return warna[jenjang] || '#64748b';

    }

    // 17. MEMBUAT MARKER
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
            { icon: ikonSekolah }
        );

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

    // 18. TAMPILKAN SEKOLAH BERDASARKAN FILTER
    function tampilkanSekolah() {

        const jenjang =
            document.getElementById('filterJenjang').value;

        const status =
            document.getElementById('filterStatus').value;

        const kataKunci =
            document.getElementById('cariSekolah')
                .value.trim().toLowerCase();

        const hasil = semuaSekolah.filter(function(feature) {

            const p = feature.properties;

            const cocokJenjang =
                !jenjang || p.jenjang === jenjang;

            const cocokStatus =
                !status || p.status === status;

            const cocokNama =
                !kataKunci ||
                String(p.nama ?? '').toLowerCase().includes(kataKunci);

            return cocokJenjang && cocokStatus && cocokNama;

        });

        hasilTerakhir = hasil;

        markerLayer.clearLayers();
        markerCluster.clearLayers();

        const dataHeatmap = [];

        hasil.forEach(function(feature) {

            const koordinat = feature.geometry.coordinates;

            const longitude = koordinat[0];
            const latitude = koordinat[1];

            const marker = buatMarker(feature);

            markerLayer.addLayer(marker);
            markerCluster.addLayer(buatMarker(feature));

            dataHeatmap.push([
                latitude,
                longitude,
                0.7
            ]);

        });

        heatLayer.setLatLngs(dataHeatmap);

        document.getElementById('jumlah').textContent =
            hasil.length + ' Sekolah';

        perbaruiKecamatan();

    }

    // 19. STATUS PEMUATAN DATA
    let sedangMemuat = false;
    let permintaanTerakhir = 0;
    let timerPeta = null;

    // 20. MEMUAT DATA SESUAI BOUNDING BOX PETA
    async function muatDataSekolah() {

        if (sedangMemuat) {
            return;
        }

        sedangMemuat = true;

        const idPermintaan = ++permintaanTerakhir;

        try {

            const bounds = map.getBounds();

            const parameter = new URLSearchParams({

                west: bounds.getWest(),
                south: bounds.getSouth(),
                east: bounds.getEast(),
                north: bounds.getNorth()

            });

            const url = @json(route('peta.data')) + '?' + parameter.toString();

            const response = await fetch(url);

            if (!response.ok) {
                throw new Error('Gagal mengambil data sekolah.');
            }

            const data = await response.json();

            if (
                data.type !== 'FeatureCollection' ||
                !Array.isArray(data.features)
            ) {
                throw new Error('Format GeoJSON sekolah tidak valid.');
            }

            semuaSekolah = data.features;

            tampilkanSekolah();

            console.log(
                'Data sekolah dalam area peta:',
                semuaSekolah.length
            );

        } catch (error) {

            console.error('Kesalahan memuat sekolah:', error);

            document.getElementById('jumlah').textContent =
                'Gagal memuat data';

        } finally {

            sedangMemuat = false;

        }

    }

    // 21. MUAT ULANG KETIKA PETA DIGESER ATAU ZOOM
    map.on('moveend', function() {

        clearTimeout(timerPeta);

        timerPeta = setTimeout(function() {
            muatDataSekolah();
        }, 300);

    });

    // 22. PENCARIAN
    document.getElementById('tombolCari')
        .addEventListener('click', tampilkanSekolah);

    document.getElementById('cariSekolah')
        .addEventListener('keydown', function(event) {

            if (event.key === 'Enter') {
                tampilkanSekolah();
            }

        });

    // 23. FILTER JENJANG
    document.getElementById('filterJenjang')
        .addEventListener('change', tampilkanSekolah);

    // 24. FILTER STATUS
    document.getElementById('filterStatus')
        .addEventListener('change', tampilkanSekolah);

    // 25. LEGENDA JENJANG
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

    // 26. DOWNLOAD SELURUH DATA GEOJSON
    document.getElementById('downloadGeoJSON')
        .addEventListener('click', async function() {

            const tombol = this;
            const teksAwal = tombol.textContent;

            try {

                tombol.disabled = true;
                tombol.textContent = 'Menyiapkan GeoJSON...';

                // Tanpa parameter Bounding Box = seluruh data
                const response = await fetch(@json(route('peta.data')));

                if (!response.ok) {
                    throw new Error('Gagal mengambil data GeoJSON.');
                }

                const data = await response.json();

                if (
                    data.type !== 'FeatureCollection' ||
                    !Array.isArray(data.features)
                ) {
                    throw new Error('Format GeoJSON tidak valid.');
                }

                const blob = new Blob(
                    [JSON.stringify(data, null, 2)],
                    { type: 'application/geo+json' }
                );

                const url = URL.createObjectURL(blob);

                const link = document.createElement('a');

                link.href = url;
                link.download = 'data-sekolah-banjarmasin.geojson';

                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                URL.revokeObjectURL(url);

                tombol.textContent = 'Download Berhasil';

            } catch (error) {

                console.error('Download GeoJSON:', error);

                alert('Gagal mengunduh GeoJSON.');

            } finally {

                tombol.disabled = false;

                setTimeout(function() {
                    tombol.textContent = teksAwal;
                }, 2000);

            }

        });

    // 27. KONTROL LAPISAN
    const baseMaps = {
        'Peta Standar (OpenStreetMap)': osm,
        'Peta Satelit (Esri Satellite)': esriSatellite
    };

    const overlayMaps = {
        'Marker Sekolah': markerLayer,
        'Klaster Sekolah': markerCluster,
        'Peta Panas (Heatmap)': heatLayer,
        'Batas Kecamatan': kecamatanLayer
    };

    osm.addTo(map);
    markerCluster.addTo(map);
    kecamatanLayer.addTo(map);

    L.control.layers(
        baseMaps,
        overlayMaps,
        {
            collapsed: false,
            position: 'topright'
        }
    ).addTo(map);

    // 28. MEMULAI PEMUATAN DATA
    muatBatasKecamatan();

    setTimeout(function() {

        map.invalidateSize();

        // Ambil sekolah sesuai area peta pertama kali
        muatDataSekolah();

    }, 500);

});
</script>

@endpush