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
        $sekolahs = null;
        $totalSekolah = 0;
        $sekolahNegeri = 0;
        $sekolahSwasta = 0;
        $kecamatanTerpadat = null;

        // Dashboard Admin
        if ($user->isAdmin()) {
            $jumlahPerRole = [
                'Administrator' => User::where('role', 'admin')->count(),
                'Dinas Pendidikan' => User::where('role', 'dinas')->count(),
                'Operator Sekolah' => User::where('role', 'sekolah')->count(),
            ];

            $totalSekolah = Sekolah::count();
            $sekolahNegeri = Sekolah::where('status', 'Negeri')->count();
            $sekolahSwasta = Sekolah::where('status', 'Swasta')->count();
            $kecamatanTerpadat = Sekolah::query()
                ->select('kecamatan')
                ->selectRaw('COUNT(*) as total')
                ->whereNotNull('kecamatan')
                ->groupBy('kecamatan')
                ->orderByDesc('total')
                ->first();

            $sekolahs = Sekolah::query()
                ->when($request->filled('q'), function ($query) use ($request) {
                    $kataKunci = $request->input('q');
                    $query->where(function ($where) use ($kataKunci) {
                        $where->where('nama', 'like', "%{$kataKunci}%")
                            ->orWhere('npsn', 'like', "%{$kataKunci}%")
                            ->orWhere('kecamatan', 'like', "%{$kataKunci}%");
                    });
                })
                ->when(in_array($request->input('status'), ['Negeri', 'Swasta'], true), function ($query) use ($request) {
                    $query->where('status', $request->input('status'));
                })
                ->orderBy('nama')
                ->paginate(8)
                ->withQueryString();
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
            'kelengkapan',
            'sekolahs',
            'totalSekolah',
            'sekolahNegeri',
            'sekolahSwasta',
            'kecamatanTerpadat'
        ));
    }
}
