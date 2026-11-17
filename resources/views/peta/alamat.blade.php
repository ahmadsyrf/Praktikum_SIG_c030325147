
@extends('layouts.public')

@section('title', 'Cek Lokasi')

@push('styles')
<style>
    .cek-lokasi {
        max-width: 1400px;
        margin: 20px auto;
        padding: 0 20px;
    }

    .toolbar {
        background: white;
        padding: 16px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .toolbar-info {
        color: #475569;
        font-size: 14px;
    }

    .lokasi-button {
        background: #075985;
        color: white;
        border: none;
        padding: 11px 17px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
    }

    .lokasi-button:hover {
        background: #0c4a6e;
    }

    .lokasi-button:disabled {
        opacity: 0.6;
        cursor: wait;
    }

    .peta {
        width: 100%;
        height: 72vh;
        min-height: 450px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .panel {
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 3px 15px rgba(15, 23, 42, 0.15);
        font-size: 13px;
        color: #334155;
    }

    .panel strong {
        color: #075985;
    }

    .panel ul {
        list-style: none;
        margin: 4px 0 0;
        padding: 0;
    }

    .panel li {
        padding: 8px 0;
        border-top: 1px solid #e5e7eb;
        cursor: pointer;
    }

    .panel li:hover {
        background: #f1f5f9;
    }

    .hasil-jumlah {
        display: inline-block;
        margin: 7px 0;
        padding: 7px 12px;
        background: #dbeafe;
        color: #1e40af;
        border-radius: 7px;
        font-weight: 700;
    }

    @media (max-width: 768px) {
        .cek-lokasi {
            padding: 0 12px;
        }

        .toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .lokasi-button {
            width: 100%;
        }

        .peta {
            height: 65vh;
            min-height: 400px;
        }

        .panel {
            position: static !important;
            width: auto !important;
            max-width: none !important;
            max-height: 350px !important;
            margin-top: 12px;
        }
    }
</style>
@endpush

@section('content')

<div class="cek-lokasi">

    <div class="toolbar">

        <div class="toolbar-info">
            Klik di mana saja pada peta untuk melihat alamat,
            sekolah terdekat, dan cakupan sekolah dalam radius 1 km.
        </div>

        <button
            type="button"
            id="tombolLokasi"
            class="lokasi-button">

            Lokasi Saya

        </button>

    </div>

    <div id="peta" class="peta"></div>

    <div
        id="hasil"
        class="panel"
        style="position: absolute; top: 150px; right: 30px;
               width: 320px; max-width: calc(100% - 40px);
               max-height: 65%; overflow: auto;
               display: none; z-index: 500;">

    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // 1. MEMBUAT PETA
    const map = Peta.buat('peta');

    const lapisan = L.layerGroup().addTo(map);

    const panel = document.getElementById('hasil');

    const tombolLokasi = document.getElementById('tombolLokasi');

    let semua = [];
    let terakhir = 0;
    let markerLokasi = null;

    // 2. MENGAMBIL DATA SEKOLAH
    Peta.ambilSekolah()
        .then((fitur) => {
            semua = fitur;
        })
        .catch((err) => {
            console.error(err);
            alert('Gagal mengambil data sekolah.');
        });

    // 3. MENCARI SEKOLAH TERDEKAT
    function terdekat(titik, n = 3) {

        return semua
            .map((f) => ({
                fitur: f,
                jarak: Peta.jarakKm(titik, Peta.latlng(f))
            }))
            .sort((a, b) => a.jarak - b.jarak)
            .slice(0, n);

    }

    // 4. MENGAMBIL SEKOLAH DALAM RADIUS 1 KM
    function sekolahDalamRadius(titik) {

        return semua
            .map((f) => ({
                fitur: f,
                jarak: Peta.jarakKm(titik, Peta.latlng(f))
            }))
            .filter((d) => d.jarak <= 1)
            .sort((a, b) => a.jarak - b.jarak);

    }

    // 5. MENGAMBIL ALAMAT BERDASARKAN KOORDINAT
    async function ambilAlamat(lat, lng) {

        const url =
            'https://nominatim.openstreetmap.org/reverse'
            + '?format=jsonv2&addressdetails=1&accept-language=id'
            + `&lat=${lat}&lon=${lng}`;

        const respon = await fetch(url);

        if (!respon.ok) {
            throw new Error('HTTP ' + respon.status);
        }

        return respon.json();

    }

    // 6. MENAMPILKAN HASIL LOKASI
    async function prosesLokasi(lat, lng) {

        const titik = [lat, lng];

        lapisan.clearLayers();

        // Marker lokasi yang dipilih
        L.circleMarker(titik, {
            radius: 9,
            color: '#ffffff',
            weight: 3,
            fillColor: '#ef4444',
            fillOpacity: 1
        }).addTo(lapisan);

        // Lingkaran cakupan dengan radius 1 kilometer
        L.circle(titik, {
            radius: 1000,
            color: '#2563eb',
            weight: 2,
            fillColor: '#3b82f6',
            fillOpacity: 0.12
        }).addTo(lapisan);

        // Sekolah terdekat
        const dekat = terdekat(titik);

        // Sekolah yang masuk dalam radius 1 km
        const dalamRadius = sekolahDalamRadius(titik);

        // Garis menuju 3 sekolah terdekat
        dekat.forEach((d) => {

            L.polyline(
                [titik, Peta.latlng(d.fitur)],
                {
                    color: '#2563eb',
                    weight: 2,
                    dashArray: '6 6'
                }
            ).addTo(lapisan);

        });

        // Tampilkan panel
        panel.style.display = 'block';
        panel.textContent = 'Mencari alamat...';

        let bagianAlamat = '<em>Alamat tidak dapat dimuat.</em>';

        try {

            const data = await ambilAlamat(lat, lng);

            const a = data.address || {};

            const baris = [
                ['Jalan', a.road],
                [
                    'Kelurahan/Desa',
                    a.suburb || a.neighbourhood || a.village
                ],
                [
                    'Kota/Kabupaten',
                    a.city || a.town || a.municipality || a.county
                ],
                ['Provinsi', a.state],
                ['Kode pos', a.postcode]
            ].map(([k, v]) => `
                <tr>
                    <td style="padding: 2px 8px 2px 0; color: #6b7280;">
                        ${k}
                    </td>
                    <td>${Peta.esc(v) || '-'}</td>
                </tr>
            `).join('');

            bagianAlamat = `
                <p style="margin: 6px 0;">
                    ${Peta.esc(data.display_name || '-')}
                </p>

                <table style="font-size: 13px;">
                    ${baris}
                </table>
            `;

        } catch (err) {

            console.error(err);

        }

        // DAFTAR SEKOLAH TERDEKAT
        const daftarTerdekat = dekat.map((d) => `
            <li
                data-npsn="${Peta.esc(d.fitur.properties.npsn)}">

                <strong>
                    ${Peta.esc(d.fitur.properties.nama)}
                </strong>

                <br>

                <small>
                    ${d.jarak.toFixed(2)} km (garis lurus)
                </small>

            </li>
        `).join('');

        // DAFTAR SEKOLAH DALAM RADIUS 1 KM
        const daftarRadius = dalamRadius.map((d) => `
            <li
                data-npsn="${Peta.esc(d.fitur.properties.npsn)}">

                <strong>
                    ${Peta.esc(d.fitur.properties.nama)}
                </strong>

                <br>

                <small>
                    Jarak: ${d.jarak.toFixed(2)} km
                </small>

            </li>
        `).join('');

        // MENAMPILKAN HASIL
        panel.innerHTML = `
            <strong>Koordinat</strong>

            <div style="font-family: monospace;">
                ${lat.toFixed(6)}, ${lng.toFixed(6)}
            </div>

            <strong style="display: block; margin-top: 8px;">
                Alamat
            </strong>

            ${bagianAlamat}

            <strong style="display: block; margin-top: 12px;">
                Analisis Cakupan Sekolah
            </strong>

            <div class="hasil-jumlah">
                ${dalamRadius.length} sekolah dalam radius 1 km
            </div>

            <small style="display: block; margin-bottom: 8px;">
                Perhitungan berdasarkan jarak garis lurus dari titik pilihan.
            </small>

            <ul>
                ${daftarRadius || '<li>Tidak ada sekolah dalam radius 1 km.</li>'}
            </ul>

            <strong style="display: block; margin-top: 15px;">
                3 Sekolah Terdekat
            </strong>

            <ul>
                ${daftarTerdekat || '<li>Tidak ada data sekolah.</li>'}
            </ul>
        `;

    }

    // 7. KLIK PETA UNTUK MEMILIH LOKASI
    map.on('click', async (e) => {

        const sekarang = Date.now();

        if (sekarang - terakhir < 1200) return;

        terakhir = sekarang;

        prosesLokasi(e.latlng.lat, e.latlng.lng);

    });

    // 8. TOMBOL LOKASI SAYA
    tombolLokasi.addEventListener('click', function () {

        if (!navigator.geolocation) {

            alert('Browser tidak mendukung fitur lokasi.');

            return;

        }

        if (semua.length === 0) {

            alert('Data sekolah masih dimuat. Coba lagi sebentar.');

            return;

        }

        tombolLokasi.disabled = true;
        tombolLokasi.textContent = 'Mencari Lokasi...';

        navigator.geolocation.getCurrentPosition(

            function (posisi) {

                const lat = posisi.coords.latitude;
                const lng = posisi.coords.longitude;

                const titik = [lat, lng];

                // Hapus marker lokasi sebelumnya
                if (markerLokasi) {
                    map.removeLayer(markerLokasi);
                }

                // Marker lokasi pengguna
                markerLokasi = L.marker(titik)
                    .addTo(map)
                    .bindPopup('<strong>Lokasi Saya</strong>')
                    .openPopup();

                // Pindahkan peta ke lokasi pengguna
                map.flyTo(titik, 15);

                // Tampilkan hasil analisis lokasi
                prosesLokasi(lat, lng);

                tombolLokasi.disabled = false;
                tombolLokasi.textContent = 'Lokasi Saya';

            },

            function (error) {

                let pesan = 'Gagal mendapatkan lokasi.';

                if (error.code === 1) {
                    pesan = 'Izin lokasi ditolak. Aktifkan izin lokasi pada browser.';
                } else if (error.code === 2) {
                    pesan = 'Informasi lokasi tidak tersedia.';
                } else if (error.code === 3) {
                    pesan = 'Waktu pencarian lokasi habis. Coba lagi.';
                }

                alert(pesan);

                tombolLokasi.disabled = false;
                tombolLokasi.textContent = 'Lokasi Saya';

            },

            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            }

        );

    });

    // 9. KLIK SEKOLAH PADA DAFTAR UNTUK MEMBUKA POPUP
    panel.addEventListener('click', (e) => {

        const li = e.target.closest('[data-npsn]');

        if (!li) return;

        const f = semua.find(
            (x) => String(x.properties.npsn) === li.dataset.npsn
        );

        if (f) {

            L.popup()
                .setLatLng(Peta.latlng(f))
                .setContent(Peta.popup(f))
                .openOn(map);

            map.flyTo(Peta.latlng(f), 17);

        }

    });

    // 10. MENYESUAIKAN UKURAN PETA
    setTimeout(function () {
        map.invalidateSize();
    }, 500);

});
</script>
@endpush
