<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Sekolah;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $jumlahPerRole = [];
        $jumlahSekolah = 0;
        $sekolah = null;
        $kelengkapan = 0;

        // Dashboard Admin
        if ($user->isAdmin()) {
            $jumlahPerRole = [
                'Administrator' => User::where('role', 'admin')->count(),
                'Dinas Pendidikan' => User::where('role', 'dinas')->count(),
                'Operator Sekolah' => User::where('role', 'sekolah')->count(),
            ];
        }

        // Dashboard Dinas Pendidikan
        elseif ($user->isDinas()) {
            $jumlahSekolah = Sekolah::count();
        }

        // Dashboard Operator Sekolah
        elseif ($user->isSekolah()) {
            $sekolah = $user->sekolah;

            if ($sekolah) {
                $profil = $sekolah->profil;

                $kolom = [
                    'kepala_sekolah',
                    'email',
                    'website',
                    'tahun_berdiri',
                    'akreditasi',
                    'jumlah_siswa',
                    'jumlah_guru',
                    'deskripsi',
                ];

                $terisi = 0;

                if ($profil) {
                    foreach ($kolom as $field) {
                        if (!empty($profil->$field)) {
                            $terisi++;
                        }
                    }
                }

                $kelengkapan = round(
                    ($terisi / count($kolom)) * 100
                );
            }
        }

        return view('dashboard', compact(
            'user',
            'jumlahPerRole',
            'jumlahSekolah',
            'sekolah',
            'kelengkapan'
        ));
    }
}
