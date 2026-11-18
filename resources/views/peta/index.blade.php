@extends('layouts.public')

@section('title', 'Peta Sekolah Banjarmasin')

@push('styles')
<link rel="stylesheet"
      href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">

<link rel="stylesheet"
      href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">

<style>
    html, body { height: 100%; overflow: hidden; }
    body { min-height: 100%; }
    body > main { width: 100%; height: 100vh; min-height: 100vh; overflow: hidden; }
    .dashboard-peta { position: fixed; inset: 0; z-index: 1; }
    .map-card, #map { position: absolute; inset: 0; width: 100%; height: 100%; }
    #map { z-index: 1; background: #dbeafe; }
    .leaflet-control-attribution { font-size: 10px !important; }

    .floating-panel {
        position: absolute;
        z-index: 600;
        top: 105px;
        left: max(24px, calc((100vw - 1440px) / 2));
        width: 356px;
        max-height: calc(100vh - 130px);
        overflow: auto;
        padding: 18px;
        border: 1px solid rgba(226, 232, 240, .9);
        border-radius: 18px;
        background: rgba(255, 255, 255, .94);
        box-shadow: 0 20px 55px rgba(15, 23, 42, .16);
        backdrop-filter: blur(18px);
        transition: width .25s ease, transform .25s ease;
    }

    .panel-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .eyebrow { margin: 0 0 6px; color: #2563eb; font-size: 10px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
    .panel-heading h1 { margin: 0; color: #0f172a; font-size: 19px; line-height: 1.25; letter-spacing: -.04em; }
    .panel-subtitle { margin: 7px 0 0; color: #64748b; font-size: 12px; line-height: 1.5; }
    .collapse-button { display: grid; flex: 0 0 34px; width: 34px; height: 34px; place-items: center; border: 1px solid #e2e8f0; border-radius: 10px; background: #fff; color: #475569; cursor: pointer; transition: .2s ease; }
    .collapse-button:hover { border-color: #bfdbfe; background: #eff6ff; color: #2563eb; }
    .panel-body { overflow: hidden; opacity: 1; transition: opacity .2s ease; }
    .floating-panel.is-collapsed { width: 280px; }
    .floating-panel.is-collapsed .panel-body { display: none; }
    .floating-panel.is-collapsed .collapse-button svg { transform: rotate(180deg); }

    .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin: 18px 0 16px; }
    .stat-card { min-width: 0; padding: 11px 10px; border: 1px solid #e8edf5; border-radius: 12px; background: #fff; }
    .stat-label { display: flex; align-items: center; gap: 5px; margin-bottom: 7px; color: #64748b; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
    .stat-value { overflow: hidden; color: #0f172a; font-size: 13px; font-weight: 800; text-overflow: ellipsis; white-space: nowrap; }
    .stat-value.total { color: #2563eb; font-size: 12px; }
    .status-dot { display: inline-block; flex: 0 0 7px; width: 7px; height: 7px; border-radius: 50%; background: #22c55e; box-shadow: 0 0 0 3px #dcfce7; }
    .panel-section-label { margin: 0 0 9px; color: #334155; font-size: 11px; font-weight: 750; }
    .search-wrap { position: relative; margin-bottom: 9px; }
    .search-wrap svg { position: absolute; top: 12px; left: 12px; color: #94a3b8; pointer-events: none; }
    .map-search, .map-select { width: 100%; height: 40px; padding: 0 12px; border: 1px solid #e2e8f0; border-radius: 10px; outline: none; background: #fff; color: #0f172a; font: inherit; font-size: 12px; transition: border-color .2s ease, box-shadow .2s ease; }
    .map-search { padding-left: 37px; }
    .map-search:focus, .map-select:focus { border-color: #60a5fa; box-shadow: 0 0 0 3px rgba(37, 99, 235, .11); }
    .filter-row { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .map-select { height: 38px; padding: 0 9px; color: #475569; }
    .panel-divider { height: 1px; margin: 16px 0; background: #e8edf5; }
    .quick-actions { display: grid; gap: 8px; }
    .quick-action { display: flex; align-items: center; gap: 10px; min-height: 41px; padding: 8px 10px; border: 1px solid #e2e8f0; border-radius: 10px; background: #fff; color: #334155; cursor: pointer; text-align: left; font: inherit; font-size: 11px; font-weight: 700; transition: .2s ease; }
    .quick-action:hover { transform: translateY(-1px); border-color: #bfdbfe; background: #f8fbff; color: #1d4ed8; }
    .quick-action svg { flex: 0 0 17px; color: #2563eb; }
    .quick-action.primary { border-color: #2563eb; background: #2563eb; color: #fff; }
    .quick-action.primary svg { color: #fff; }
    .quick-action.primary:hover { background: #1d4ed8; color: #fff; }
    .map-source { display: flex; justify-content: space-between; align-items: center; margin-top: 14px; color: #94a3b8; font-size: 10px; }

    .layer-switcher { display: grid; gap: 6px; min-width: 132px; margin-top: 82px !important; padding: 10px; border: 1px solid rgba(226, 232, 240, .95); border-radius: 14px; background: rgba(255, 255, 255, .94); box-shadow: 0 12px 30px rgba(15, 23, 42, .13); backdrop-filter: blur(14px); }
    .layer-switcher-title { padding: 1px 5px 5px; color: #64748b; font-size: 9px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
    .layer-switcher button { display: flex; align-items: center; gap: 8px; min-height: 31px; padding: 5px 7px; border: 0; border-radius: 8px; background: transparent; color: #475569; cursor: pointer; text-align: left; font: inherit; font-size: 10px; font-weight: 650; transition: .18s ease; }
    .layer-switcher button:hover { background: #f1f5f9; color: #0f172a; }
    .layer-switcher button.active { background: #eff6ff; color: #2563eb; }
    .layer-icon { display: grid; width: 18px; height: 18px; place-items: center; border-radius: 5px; background: #f1f5f9; font-size: 11px; }
    .layer-switcher button.active .layer-icon { background: #dbeafe; }

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
        .floating-panel { top: 83px; left: 12px; width: min(356px, calc(100vw - 24px)); max-height: min(55vh, 500px); padding: 14px; border-radius: 16px; }
        .floating-panel.is-collapsed { width: min(280px, calc(100vw - 24px)); }
        .stats { margin: 14px 0; }
        .stat-card { padding: 9px 7px; }
        .stat-label { font-size: 8px; }
        .stat-value { font-size: 11px; }
        .stat-value.total { font-size: 14px; }
        .layer-switcher { margin-top: 67px !important; min-width: 116px; padding: 7px; }
        .layer-switcher button { min-height: 28px; }
    }

    @media (max-width: 420px) {
        .floating-panel { top: 78px; }
        .layer-switcher { margin-top: 59px !important; }
        .map-source { font-size: 9px; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-peta">
    <div class="map-card">
        <div id="map" aria-label="Peta interaktif persebaran sekolah Banjarmasin"></div>
    </div>

    <aside class="floating-panel" id="filterPanel" aria-label="Filter dan statistik sekolah">
        <div class="panel-heading">
            <div>
                <p class="eyebrow">Peta publik · Banjarmasin</p>
                <h1>Jelajahi sekolah</h1>
                <p class="panel-subtitle">Temukan sebaran sekolah dan informasi wilayah secara langsung.</p>
            </div>
            <button type="button" class="collapse-button" id="toggleFilterPanel" aria-label="Ciutkan panel" aria-expanded="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>

        <div class="panel-body" id="filterPanelBody">
            <section class="stats" aria-label="Ringkasan data peta">
        <div class="stat-card">
                    <div class="stat-label"><span class="status-dot"></span> Total sekolah</div>
                    <div class="stat-value total" id="jumlah" aria-live="polite">Memuat...</div>
        </div>
        <div class="stat-card">
                    <div class="stat-label">Wilayah</div>
                    <div class="stat-value">Banjarmasin</div>
        </div>
        <div class="stat-card">
                    <div class="stat-label">Sumber</div>
                    <div class="stat-value">OpenStreetMap</div>
        </div>
            </section>

            <section aria-label="Pencarian dan filter">
                <p class="panel-section-label">Cari dan filter sekolah</p>
                <div class="search-wrap">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
                        <path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <input type="search" id="cariSekolah" class="map-search" placeholder="Cari nama sekolah..." autocomplete="off" aria-label="Cari nama sekolah">
                </div>
                <div class="filter-row">
                    <select id="filterJenjang" class="map-select" aria-label="Filter jenjang">
                        <option value="">Semua Jenjang</option>
                        <option value="SD">SD</option>
                        <option value="SMP">SMP</option>
                        <option value="SMA">SMA</option>
                        <option value="SMK">SMK</option>
                    </select>
                    <select id="filterStatus" class="map-select" aria-label="Filter status">
                        <option value="">Semua Status</option>
                        <option value="Negeri">Negeri</option>
                        <option value="Swasta">Swasta</option>
                    </select>
                </div>
                <button type="button" id="tombolCari" hidden>Cari Sekolah</button>
            </section>

            <div class="panel-divider"></div>

            <section aria-label="Aksi cepat">
                <p class="panel-section-label">Aksi cepat</p>
                <div class="quick-actions">
                    <button type="button" id="cekLokasiSaya" class="quick-action">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/>
                            <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M12 2v3m0 14v3M2 12h3m14 0h3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                        Cek Lokasi Saya
                    </button>
                    <button type="button" id="analisisRadius" class="quick-action">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.6" stroke-dasharray="3 2"/>
                            <circle cx="12" cy="12" r="2.5" fill="currentColor"/>
                        </svg>
                        Analisis Radius 1 KM
                    </button>
                    <button type="button" id="downloadGeoJSON" class="quick-action primary">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3v12m0 0 4-4m-4 4-4-4M4 17v3h16v-3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Download GeoJSON
                    </button>
                </div>
            </section>

            <div class="map-source">
                <span id="mapStatus" role="status" aria-live="polite"><span class="status-dot"></span> Data peta aktif</span>
                <span>Leaflet · OpenStreetMap</span>
            </div>
        </div>
    </aside>
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
            updateWhenIdle: true,
            updateWhenZooming: false,
            keepBuffer: 1,
            attribution: '&copy; OpenStreetMap contributors'
        }
    );

    const esriSatellite = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        {
            maxZoom: 19,
            updateWhenIdle: true,
            updateWhenZooming: false,
            keepBuffer: 1,
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

            if (map.hasLayer(markerLayer)) {
                markerLayer.addLayer(buatMarker(feature));
            }

            if (map.hasLayer(markerCluster)) {
                markerCluster.addLayer(buatMarker(feature));
            }

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
    let permintaanAktif = null;
    let timerPeta = null;

    // 20. MEMUAT DATA SESUAI BOUNDING BOX PETA
    async function muatDataSekolah() {

        if (permintaanAktif) {
            permintaanAktif.abort();
        }

        const controller = new AbortController();
        permintaanAktif = controller;

        try {

            const bounds = map.getBounds();

            const parameter = new URLSearchParams({

                west: bounds.getWest(),
                south: bounds.getSouth(),
                east: bounds.getEast(),
                north: bounds.getNorth()

            });

            const url = @json(route('peta.data')) + '?' + parameter.toString();

            const response = await fetch(url, {
                signal: controller.signal
            });

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

            if (error.name === 'AbortError') {
                return;
            }

            console.error('Kesalahan memuat sekolah:', error);

            document.getElementById('jumlah').textContent =
                'Gagal memuat data';

        } finally {

            if (permintaanAktif === controller) {
                permintaanAktif = null;
            }

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

    let timerPencarian = null;

    document.getElementById('cariSekolah')
        .addEventListener('input', function() {
            clearTimeout(timerPencarian);
            timerPencarian = setTimeout(tampilkanSekolah, 250);
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
    osm.addTo(map);
    markerCluster.addTo(map);
    kecamatanLayer.addTo(map);

    let basemapAktif = osm;
    const tombolLapisan = {};

    function perbaruiStatusLapisan() {
        Object.keys(tombolLapisan).forEach(function(nama) {
            const tombol = tombolLapisan[nama];
            const aktif = nama === 'standar'
                ? basemapAktif === osm
                : nama === 'satelit'
                    ? basemapAktif === esriSatellite
                    : map.hasLayer({
                        marker: markerLayer,
                        klaster: markerCluster,
                        heatmap: heatLayer,
                        wilayah: kecamatanLayer
                    }[nama]);

            tombol.classList.toggle('active', aktif);
            tombol.setAttribute('aria-pressed', aktif ? 'true' : 'false');
        });
    }

    const kontrolLapisan = L.control({ position: 'topright' });

    kontrolLapisan.onAdd = function() {
        const container = L.DomUtil.create('div', 'layer-switcher');
        const pilihan = [
            ['standar', '▧', 'Peta Standar'],
            ['satelit', '◈', 'Peta Satelit'],
            ['marker', '●', 'Marker'],
            ['klaster', '◉', 'Klaster'],
            ['heatmap', '◌', 'Heatmap'],
            ['wilayah', '⬡', 'Batas Kecamatan']
        ];

        container.innerHTML = '<div class="layer-switcher-title">Lapisan Peta</div>';

        pilihan.forEach(function(item) {
            const nama = item[0];
            const tombol = L.DomUtil.create('button', '', container);
            tombol.type = 'button';
            tombol.innerHTML = '<span class="layer-icon" aria-hidden="true">' +
                item[1] + '</span><span>' + item[2] + '</span>';
            tombol.setAttribute('aria-label', item[2]);
            tombolLapisan[nama] = tombol;

            L.DomEvent.on(tombol, 'click', function(event) {
                L.DomEvent.stop(event);

                if (nama === 'standar' || nama === 'satelit') {
                    if (basemapAktif === (nama === 'standar' ? osm : esriSatellite)) return;
                    map.removeLayer(basemapAktif);
                    basemapAktif = nama === 'standar' ? osm : esriSatellite;
                    basemapAktif.addTo(map);
                    basemapAktif.bringToBack();
                } else {
                    const lapisan = {
                        marker: markerLayer,
                        klaster: markerCluster,
                        heatmap: heatLayer,
                        wilayah: kecamatanLayer
                    }[nama];

                    if (map.hasLayer(lapisan)) {
                        map.removeLayer(lapisan);
                    } else {
                        if (lapisan === markerLayer || lapisan === markerCluster) {
                            hasilTerakhir.forEach(function(feature) {
                                lapisan.addLayer(buatMarker(feature));
                            });
                        }

                        lapisan.addTo(map);
                    }
                }

                perbaruiStatusLapisan();
            });
        });

        L.DomEvent.disableClickPropagation(container);
        L.DomEvent.disableScrollPropagation(container);
        return container;
    };

    kontrolLapisan.addTo(map);
    perbaruiStatusLapisan();

    // Panel peta bisa diciutkan untuk memperluas area eksplorasi.
    document.getElementById('toggleFilterPanel')
        .addEventListener('click', function() {
            const panel = document.getElementById('filterPanel');
            const isCollapsed = panel.classList.toggle('is-collapsed');
            this.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
            this.setAttribute('aria-label', isCollapsed ? 'Buka panel' : 'Ciutkan panel');
            setTimeout(function() {
                map.invalidateSize();
            }, 260);
        });

    // Cari lokasi perangkat dan gambar radius analisis satu kilometer.
    let penandaLokasi = null;
    let lingkaranRadius = null;

    document.getElementById('cekLokasiSaya')
        .addEventListener('click', function() {
            const status = document.getElementById('mapStatus');
            status.textContent = 'Meminta akses lokasi...';

            if (!navigator.geolocation) {
                status.textContent = 'Browser tidak mendukung fitur lokasi.';
                return;
            }

            navigator.geolocation.getCurrentPosition(function(posisi) {
                const lokasi = [posisi.coords.latitude, posisi.coords.longitude];
                map.setView(lokasi, 15);

                if (penandaLokasi) map.removeLayer(penandaLokasi);
                penandaLokasi = L.circleMarker(lokasi, {
                    radius: 8,
                    color: '#fff',
                    weight: 3,
                    fillColor: '#2563eb',
                    fillOpacity: 1
                }).addTo(map).bindTooltip('Lokasi Anda');

                status.textContent = 'Lokasi Anda ditemukan.';
            }, function(error) {
                const pesan = error.code === 1
                    ? 'Izin lokasi ditolak.'
                    : error.code === 2
                        ? 'Lokasi tidak tersedia.'
                        : 'Waktu pencarian lokasi habis.';
                status.textContent = pesan;
            }, {
                enableHighAccuracy: true,
                timeout: 12000,
                maximumAge: 0
            });
        });

    document.getElementById('analisisRadius')
        .addEventListener('click', function() {
            const status = document.getElementById('mapStatus');

            if (lingkaranRadius) {
                map.removeLayer(lingkaranRadius);
                lingkaranRadius = null;
                status.innerHTML = '<span class="status-dot"></span> Data peta aktif';
                return;
            }

            const pusat = map.getCenter();
            lingkaranRadius = L.circle(pusat, {
                radius: 1000,
                color: '#2563eb',
                weight: 2,
                fillColor: '#60a5fa',
                fillOpacity: .12
            }).addTo(map);

            function jarakMeter(lat1, lng1, lat2, lng2) {
                const rad = function(nilai) { return nilai * Math.PI / 180; };
                const deltaLat = rad(lat2 - lat1);
                const deltaLng = rad(lng2 - lng1);
                const a = Math.sin(deltaLat / 2) ** 2 +
                    Math.cos(rad(lat1)) * Math.cos(rad(lat2)) *
                    Math.sin(deltaLng / 2) ** 2;
                return 6371000 * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            }

            const dalamRadius = semuaSekolah.filter(function(feature) {
                const koordinat = feature.geometry.coordinates;
                return jarakMeter(pusat.lat, pusat.lng, koordinat[1], koordinat[0]) <= 1000;
            }).length;

            status.textContent = dalamRadius + ' sekolah dalam radius 1 km dari tengah peta.';
        });

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