
const Peta = {
    // Pusat Kota Banjarmasin [latitude, longitude]
    pusat: [-3.3194, 114.5908],

    // Membuat peta Leaflet
    buat(idElemen, zoom = 12) {
        const map = L.map(idElemen).setView(Peta.pusat, zoom);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        return map;
    },

    // Mengamankan teks dari database agar tidak dibaca sebagai HTML
    esc(teks) {
        const d = document.createElement('div');
        d.textContent = teks ?? '';
        return d.innerHTML;
    },

    // Mengambil data sekolah dari endpoint GeoJSON
    async ambilSekolah(jenjang = '') {
        const url = `${window.PETA_URL.data}?jenjang=${encodeURIComponent(jenjang)}`;
        const respon = await fetch(url);

        if (!respon.ok) {
            throw new Error('Gagal memuat data sekolah');
        }

        const data = await respon.json();
        return data.features;
    },

    // Mengubah koordinat GeoJSON [longitude, latitude]
    // menjadi koordinat Leaflet [latitude, longitude]
    latlng(fitur) {
        const [lng, lat] = fitur.geometry.coordinates;
        return [lat, lng];
    },

    // Membuat isi popup sekolah
    popup(fitur) {
        const p = fitur.properties;
        const [lat, lng] = Peta.latlng(fitur);

        return `
            <strong>${Peta.esc(p.nama)}</strong><br>
            NPSN: ${Peta.esc(p.npsn)}<br>
            ${Peta.esc(p.jenjang)} - ${Peta.esc(p.status)}<br>
            ${Peta.esc(p.alamat)}, ${Peta.esc(p.kecamatan)}<br>
            Telp: ${Peta.esc(p.telepon) || '-'}<br>
            Kepala sekolah: ${Peta.esc(p.kepala_sekolah) || '-'}<br>
            Akreditasi: ${Peta.esc(p.akreditasi) || '-'}<br>
            Siswa: ${p.jumlah_siswa ?? '-'} | Guru: ${p.jumlah_guru ?? '-'}<br>
            <small>Koordinat: ${lat}, ${lng}</small>
        `;
    },

    // Menghitung jarak dua titik menggunakan rumus Haversine
    jarakKm(a, b) {
        const R = 6371;
        const rad = (derajat) => derajat * Math.PI / 180;

        const dLat = rad(b[0] - a[0]);
        const dLng = rad(b[1] - a[1]);

        const x =
            Math.sin(dLat / 2) ** 2 +
            Math.cos(rad(a[0])) *
            Math.cos(rad(b[0])) *
            Math.sin(dLng / 2) ** 2;

        return 2 * R * Math.asin(Math.sqrt(x));
    }
};
