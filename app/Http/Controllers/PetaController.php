<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Support\Geometri;
use Illuminate\Http\Request;

class PetaController extends Controller
{
    // Praktikum 20: Menampilkan halaman peta publik
    public function index()
    {
        return view('peta.index');
    }

    // Praktikum 21: Menampilkan halaman pencarian
    public function cari()
    {
        return view('peta.cari');
    }

    // Praktikum 22: Menampilkan halaman klaster
    public function klaster()
    {
        return view('peta.klaster');
    }

    // Praktikum 23: Menampilkan halaman peta panas
    public function panas()
    {
        return view('peta.panas');
    }

    // Praktikum 24: Menampilkan halaman wilayah
    public function wilayah()
    {
        return view('peta.wilayah');
    }
    // Praktikum 25: Halaman Rute
    public function rute()
    {
        return view('peta.rute');
    }
    // Praktikum 26: Menu Cek Lokasi
    public function alamat()
    {
        return view('peta.alamat');
    }

    // Mengambil data sekolah dalam format GeoJSON
    public function geojson(Request $request)
    {
        $features = Sekolah::with('profil')

            // Filter berdasarkan jenjang sekolah
            ->when($request->jenjang, function ($query, $jenjang) {
                $query->where('jenjang', $jenjang);
            })

            // Filter berdasarkan status sekolah
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })

            // Hanya mengambil sekolah yang memiliki koordinat
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')

            ->get()

            // Mengubah data sekolah menjadi GeoJSON
            ->map(function ($sekolah) {
                return [
                    'type' => 'Feature',

                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            (float) $sekolah->longitude,
                            (float) $sekolah->latitude,
                        ],
                    ],

                    'properties' => [
                        'id' => $sekolah->id,
                        'npsn' => $sekolah->npsn,
                        'nama' => $sekolah->nama,
                        'jenjang' => $sekolah->jenjang,
                        'status' => $sekolah->status,
                        'alamat' => $sekolah->alamat,
                        'kecamatan' => $sekolah->kecamatan,
                        'telepon' => $sekolah->telepon,

                        // Data profil sekolah
                        'kepala_sekolah' => $sekolah->profil?->kepala_sekolah,
                        'akreditasi' => $sekolah->profil?->akreditasi,
                        'jumlah_siswa' => $sekolah->profil?->jumlah_siswa,
                        'jumlah_guru' => $sekolah->profil?->jumlah_guru,

                        // URL halaman profil publik
                        'profil_url' => route(
                            'sekolah.profil.public',
                            $sekolah->id
                        ),
                    ],
                ];
            });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    // Praktikum 24: Mengambil data wilayah dalam format GeoJSON
    public function geojsonWilayah(Request $request)
    {
        $features = Sekolah::with('profil')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')

            // Filter berdasarkan jenjang
            ->when($request->jenjang, function ($query, $jenjang) {
                $query->where('jenjang', $jenjang);
            })

            ->get()
            ->groupBy('kecamatan')

            ->map(function ($kelompok, $kecamatan) {

                // Mengambil koordinat sekolah [longitude, latitude]
                $titik = $kelompok
                    ->map(function ($sekolah) {
                        return [
                            (float) $sekolah->longitude,
                            (float) $sekolah->latitude,
                        ];
                    })
                    ->all();

                // Menghitung convex hull
                $hull = Geometri::convexHull($titik);

                // Poligon membutuhkan minimal 3 titik
                if (count($hull) < 3) {
                    return null;
                }

                // Menutup cincin poligon GeoJSON
                $hull[] = $hull[0];

                return [
                    'type' => 'Feature',

                    'geometry' => [
                        'type' => 'Polygon',
                        'coordinates' => [$hull],
                    ],

                    'properties' => [
                        'kecamatan' => $kecamatan,
                        'jumlah_sekolah' => $kelompok->count(),

                        'jumlah_siswa' => (int) $kelompok->sum(
                            function ($sekolah) {
                                return $sekolah->profil?->jumlah_siswa ?? 0;
                            }
                        ),
                    ],
                ];
            })

            ->filter()
            ->values();

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}
