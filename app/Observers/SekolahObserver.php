<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Sekolah;

class SekolahObserver
{
    // Mencatat ketika sekolah dibuat
    public function created(Sekolah $sekolah): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'dibuat',
            'model' => Sekolah::class,
            'model_id' => $sekolah->id,
            'data_baru' => $sekolah->getAttributes(),
        ]);
    }

    // Mencatat ketika data sekolah diperbarui
    public function updated(Sekolah $sekolah): void
    {
        $perubahan = $sekolah->getChanges();

        unset($perubahan['updated_at']);

        if (empty($perubahan)) {
            return;
        }

        $dataLama = [];

        foreach ($perubahan as $kolom => $nilaiBaru) {
            $dataLama[$kolom] = $sekolah->getOriginal($kolom);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'diperbarui',
            'model' => Sekolah::class,
            'model_id' => $sekolah->id,
            'data_lama' => $dataLama,
            'data_baru' => $perubahan,
        ]);
    }

    // Mencatat ketika sekolah dihapus
    public function deleted(Sekolah $sekolah): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'dihapus',
            'model' => Sekolah::class,
            'model_id' => $sekolah->id,
            'data_lama' => $sekolah->getAttributes(),
        ]);
    }
}