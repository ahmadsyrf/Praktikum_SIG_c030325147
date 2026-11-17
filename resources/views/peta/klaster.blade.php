@extends('layouts.public')

@section('title', 'Klaster')

@push('styles')
<link rel="stylesheet"
      href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css">

<link rel="stylesheet"
      href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css">

<style>
    .toolbar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 15px;
    }

    .toolbar label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
    }

    .toolbar select {
        padding: 9px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: white;
    }

    #jumlah {
        font-weight: 600;
        color: #2563eb;
    }

    .peta {
        width: 100%;
        height: 550px;
        border-radius: 12px;
        overflow: hidden;
    }
</style>
@endpush

@section('content')

<div class="toolbar">

    <label>
        Jenjang
        <select id="filter-jenjang">
            <option value="">Semua jenjang</option>
            <option value="SD">SD</option>
            <option value="SMP">SMP</option>
            <option value="SMA">SMA</option>
            <option value="SMK">SMK</option>
        </select>
    </label>

    <label>
        Status
        <select id="filter-status">
            <option value="">Semua status</option>
            <option value="Negeri">Negeri</option>
            <option value="Swasta">Swasta</option>
        </select>
    </label>

    <span id="jumlah">Memuat data...</span>

    <span style="color: #6b7280;">
        Klik angka pada klaster untuk memperbesar peta.
    </span>

</div>

<div id="peta" class="peta"></div>

@endsection

@push('scripts')

<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script>
    const map = Peta.buat('peta');

    const klaster = L.markerClusterGroup({
        showCoverageOnHover: false,
        maxClusterRadius: 60,
        spiderfyOnMaxZoom: true,
        disableClusteringAtZoom: 17,
        chunkedLoading: true,
    });

    map.addLayer(klaster);

    async function muat(jenjang = '', status = '') {
        try {
            const parameter = new URLSearchParams();

            if (jenjang) {
                parameter.append('jenjang', jenjang);
            }

            if (status) {
                parameter.append('status', status);
            }

            const response = await fetch(
                `{{ route('peta.data') }}?${parameter.toString()}`
            );

            if (!response.ok) {
                throw new Error('Gagal mengambil data sekolah');
            }

            const data = await response.json();

            const fitur = data.features || [];

            klaster.clearLayers();

            klaster.addLayers(
                fitur.map((f) =>
                    L.marker(Peta.latlng(f))
                        .bindPopup(Peta.popup(f))
                )
            );

            document.getElementById('jumlah').textContent =
                fitur.length + ' sekolah';

            if (fitur.length > 0) {
                map.fitBounds(
                    L.latLngBounds(
                        fitur.map((f) => Peta.latlng(f))
                    ).pad(0.1)
                );
            }

        } catch (error) {
            console.error('Gagal memuat klaster:', error);

            document.getElementById('jumlah').textContent =
                'Gagal memuat data sekolah';
        }
    }

    function terapkanFilter() {
        const jenjang = document.getElementById('filter-jenjang').value;
        const status = document.getElementById('filter-status').value;

        muat(jenjang, status);
    }

    document.getElementById('filter-jenjang')
        .addEventListener('change', terapkanFilter);

    document.getElementById('filter-status')
        .addEventListener('change', terapkanFilter);

    muat();
</script>

@endpush