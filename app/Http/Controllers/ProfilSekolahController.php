<?php

namespace App\Http\Controllers;

use App\Models\ProfilSekolah;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfilSekolahController extends Controller
{
    // Menampilkan halaman profil sekolah untuk pengelola
    public function edit(Sekolah $sekolah)
    {
        Gate::authorize('update', $sekolah);

        $profil = $sekolah->profil ?? new ProfilSekolah();

        return view('sekolah.profil', compact('sekolah', 'profil'));
    }

    // Menampilkan profil sekolah untuk publik
    public function publicShow(Sekolah $sekolah)
    {
        $profil = $sekolah->profil;

        return view(
            'sekolah.profil-public',
            compact('sekolah', 'profil')
        );
    }

    // Menyimpan perubahan profil sekolah
    public function update(Request $request, Sekolah $sekolah)
    {
        Gate::authorize('update', $sekolah);

        $data = $request->validate([
            'kepala_sekolah' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],

            'tahun_berdiri' => [
                'nullable',
                'integer',
                'between:1800,' . date('Y'),
            ],

            'akreditasi' => [
                'nullable',
                Rule::in(['A', 'B', 'C', 'Belum Terakreditasi']),
            ],

            'jumlah_siswa' => ['nullable', 'integer', 'min:0'],
            'jumlah_guru' => ['nullable', 'integer', 'min:0'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],

            // Validasi logo
            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        // Mengambil profil sekolah yang sudah ada
        $profil = $sekolah->profil;

        // Jika logo baru diunggah
        if ($request->hasFile('logo')) {

            // Hapus logo lama jika tersedia
            if ($profil && $profil->logo) {
                Storage::disk('public')->delete($profil->logo);
            }

            // Simpan logo baru
            $data['logo'] = $request->file('logo')
                ->store('logo-sekolah', 'public');
        }

        // Simpan atau perbarui profil
        $sekolah->profil()->updateOrCreate([], $data);

        return back()->with(
            'success',
            'Profil sekolah berhasil disimpan.'
        );
    }
}
