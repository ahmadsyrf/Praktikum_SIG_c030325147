
@extends('layouts.public')

@section('title', 'Pencarian')

@push('styles')
<link rel="stylesheet"
      href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
<link rel="stylesheet"
      href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">

<style>
    #hasil {
        list-style: none;
        margin: 8px 0 0;
        padding: 0;
    }

    #hasil li {
        padding: 8px 4px;
        border-top: 1px solid #e5e7eb;
        cursor: pointer;
    }

    #hasil li:hover {
        background: #eff6ff;
    }

    .marker-cluster-small,
    .marker-cluster-medium,
    .marker-cluster-large {
        background-color: rgba(37, 99, 235, .18);
    }

    .marker-cluster-small div,
    .marker-cluster-medium div,
    .marker-cluster-large div {
        background-color: #2563eb;
        color: #fff;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="toolbar">
    <input type="search" id="kata" size="32"
           placeholder="Cari nama, NPSN, atau kecamatan">

    <select id="jenjang">
        <option value="">Semua jenjang</option>
        <option value="SD">SD</option>
        <option value="SMP">SMP</option>
        <option value="SMA">SMA</option>
        <option value="SMK">SMK</option>
    </select>

    <button id="hapus" type="button">Hapus</button>
    <span id="jumlah"></span>
</div>

<div id="peta" class="peta"></div>

<div class="panel"
     style="top: 64px; left: 60px; width: 280px; max-height: 55%; overflow: auto;">
    <strong>Hasil pencarian</strong>
    <ul id="hasil"></ul>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
    const map = Peta.buat('peta');
    const lapisan = L.markerClusterGroup({
        chunkedLoading: true,
        chunkInterval: 50,
        chunkDelay: 20,
        showCoverageOnHover: false,
        maxClusterRadius: 50,
        spiderfyOnMaxZoom: true
    }).addTo(map);
    let semua = [];
    const markerSekolah = new Map();
    const hasilElement = document.getElementById('hasil');

    function tampil(daftar) {
        lapisan.clearLayers();

        const fragmen = document.createDocumentFragment();
        const markerTerlihat = [];

        daftar.forEach((f) => {
            const marker = markerSekolah.get(String(f.properties.id));

            const li = document.createElement('li');
            li.dataset.sekolahId = f.properties.id;
            li.textContent = `${f.properties.nama} (${f.properties.kecamatan})`;
            fragmen.appendChild(li);
            if (marker) markerTerlihat.push(marker);
        });

        hasilElement.replaceChildren(fragmen);
        lapisan.addLayers(markerTerlihat);

        document.getElementById('jumlah').textContent =
            `${daftar.length} dari ${semua.length} sekolah`;
    }

    function cari() {
        const kata = document.getElementById('kata')
            .value.trim().toLowerCase();

        const jenjang = document.getElementById('jenjang').value;

        tampil(semua.filter(({ properties: p }) =>
            (!jenjang || p.jenjang === jenjang) &&
            (!kata || [p.nama, p.npsn, p.kecamatan]
                .some((t) => String(t ?? '').toLowerCase().includes(kata)))
        ));
    }

    let tunda;

    document.getElementById('kata').addEventListener('input', () => {
        clearTimeout(tunda);
        tunda = setTimeout(cari, 300);
    });

    document.getElementById('jenjang').addEventListener('change', cari);

    document.getElementById('hapus').addEventListener('click', () => {
        document.getElementById('kata').value = '';
        document.getElementById('jenjang').value = '';
        cari();
    });

    hasilElement.addEventListener('click', (event) => {
        const item = event.target.closest('li[data-sekolah-id]');
        if (!item) return;

        const marker = markerSekolah.get(item.dataset.sekolahId);
        if (marker) {
            lapisan.zoomToShowLayer(marker, () => marker.openPopup());
        }
    });

    Peta.ambilSekolah().then((fitur) => {
        semua = fitur;

        semua.forEach((f) => {
            markerSekolah.set(
                String(f.properties.id),
                L.marker(Peta.latlng(f)).bindPopup(Peta.popup(f))
            );
        });

        tampil(semua);
    }).catch((error) => {
        console.error('Gagal memuat data pencarian sekolah:', error);
        document.getElementById('jumlah').textContent =
            'Gagal memuat data sekolah';
    });
</script>
@endpush
