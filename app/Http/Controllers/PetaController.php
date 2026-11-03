<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;

class PetaController extends Controller
{
    // Menampilkan halaman peta publik
    public function index()
    {
        return view('peta');
    }

    // Mengambil data sekolah dalam format GeoJSON
    public function geojson(Request $request)
    {
        $features = Sekolah::query()

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
                    ],
                ];
            });

        // Mengirim hasil dalam bentuk JSON
        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}
