<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SekolahController extends Controller
{
    // Menampilkan daftar sekolah
    public function index(Request $request)
    {
        $sekolahs = Sekolah::query()
            ->when($request->q, function ($query, $q) {
                $query->where(function ($w) use ($q) {
                    $w->whereLike('nama', "%{$q}%")
                    ->orWhereLike('npsn', "%{$q}%")
                    ->orWhereLike('kecamatan', "%{$q}%");
                });
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();


        return view('admin.sekolah.index', compact('sekolahs'));
    }

    // Form tambah sekolah
    public function create()
    {
        return view('admin.sekolah.create');
    }

    // Menyimpan sekolah baru
    public function store(Request $request)
    {
        Sekolah::create($request->validate($this->rules()));

        return redirect()->route('dinas.sekolah.index')
            ->with('success', 'Data sekolah berhasil ditambahkan.');
    }

    // Form edit sekolah
    public function edit(Sekolah $sekolah)
    {
        Gate::authorize('update', $sekolah);

        return view('admin.sekolah.edit', compact('sekolah'));
    }

    // Memperbarui data sekolah
    public function update(Request $request, Sekolah $sekolah)
    {
        Gate::authorize('update', $sekolah);

        $isOperatorSekolah = $request->user()->isSekolah();

        // Aturan validasi
        $rules = $this->rules($sekolah->id);

        // Operator sekolah tidak perlu mengirim NPSN
        if ($isOperatorSekolah) {
            unset($rules['npsn']);
        }

        $data = $request->validate($rules);

        // Pertahankan NPSN lama untuk operator sekolah
        if ($isOperatorSekolah) {
            $data['npsn'] = $sekolah->npsn;
        }

        // Validasi jarak perubahan koordinat
        $latitudeBerubah = (float) $data['latitude'] !== (float) $sekolah->latitude;
        $longitudeBerubah = (float) $data['longitude'] !== (float) $sekolah->longitude;

        if ($latitudeBerubah || $longitudeBerubah) {
            $jarak = $this->hitungJarak(
                (float) $sekolah->latitude,
                (float) $sekolah->longitude,
                (float) $data['latitude'],
                (float) $data['longitude']
            );

            if ($jarak > 1000) {
                throw ValidationException::withMessages([
                    'latitude' => 'Perubahan koordinat tidak boleh lebih dari 1.000 meter dari lokasi sebelumnya.',
                ]);
            }
        }

        $sekolah->update($data);

        return $this->kembali($request)
            ->with('success', 'Data sekolah berhasil diperbarui.');
    }

    // Menghapus sekolah
    public function destroy(Sekolah $sekolah)
    {
        Gate::authorize('delete', $sekolah);

        $sekolah->delete();

        return redirect()->route('dinas.sekolah.index')
            ->with('success', 'Data sekolah berhasil dihapus.');
    }

    // Menghitung jarak menggunakan rumus Haversine
    private function hitungJarak(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $radiusBumi = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1))
            * cos(deg2rad($lat2))
            * sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $radiusBumi * $c;
    }

    // Mengarahkan kembali pengguna
    private function kembali(Request $request)
    {
        return $request->user()->isDinas()
            ? redirect()->route('dinas.sekolah.index')
            : redirect()->route('dashboard');
    }

    // Aturan validasi
    private function rules(?int $id = null): array
    {
        return [
            'npsn' => [
                'required',
                'digits:8',
                Rule::unique('sekolahs', 'npsn')->ignore($id),
            ],
            'nama' => ['required', 'string', 'max:255'],
            'jenjang' => ['required', Rule::in(['SD', 'SMP', 'SMA', 'SMK'])],
            'status' => ['required', Rule::in(['Negeri', 'Swasta'])],
            'alamat' => ['required', 'string'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }
}
