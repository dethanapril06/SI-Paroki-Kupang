<?php

namespace App\Imports;

use App\Models\Keluarga;
use App\Models\Kub;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class KeluargaImport implements ToModel, WithStartRow, WithValidation, SkipsEmptyRows, WithBatchInserts
{
    // Kolom: 0=kub_nama, 1=wilayah_nama, 2=alamat, 3=status_tempat_tinggal

    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row): ?Keluarga
    {
        $kubNama              = trim($row[0] ?? '');
        $wilayahNama          = trim($row[1] ?? '');
        $alamat               = trim($row[2] ?? '');
        $statusTempat         = trim($row[3] ?? '');

        $kubQuery = Kub::where('nama', $kubNama);
        if ($wilayahNama !== '') {
            $kubQuery->whereHas('wilayah', fn($q) => $q->where('nama', $wilayahNama));
        }
        $kub = $kubQuery->first();

        if (!$kub) {
            $info = $wilayahNama !== ''
                ? "KUB \"{$kubNama}\" di wilayah \"{$wilayahNama}\""
                : "KUB \"{$kubNama}\"";
            throw new \Exception("{$info} tidak ditemukan. Pastikan KUB sudah diimport.");
        }

        $exists = Keluarga::where('kub_id', $kub->id)->where('alamat', $alamat)->exists();
        if ($exists) {
            throw new \Exception("Keluarga dengan alamat \"{$alamat}\" di KUB \"{$kub->nama}\" sudah ada.");
        }

        return new Keluarga([
            'kub_id'               => $kub->id,
            'alamat'               => $alamat,
            'status_tempat_tinggal' => $statusTempat,
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => ['required', 'string'],                                              // kub_nama
            '2' => ['required', 'string'],                                              // alamat
            '3' => ['required', Rule::in(['Rumah Pribadi', 'Kontrak/Kost', 'Dinas'])],  // status_tempat_tinggal
        ];
    }

    public function customValidationAttributes(): array
    {
        return ['0' => 'kub_nama', '2' => 'alamat', '3' => 'status_tempat_tinggal'];
    }

    public function customValidationMessages(): array
    {
        return [
            '0.required' => 'Kolom "kub_nama" wajib diisi.',
            '2.required' => 'Kolom "alamat" wajib diisi.',
            '3.required' => 'Kolom "status_tempat_tinggal" wajib diisi.',
            '3.in'       => 'Kolom "status_tempat_tinggal" harus salah satu dari: Rumah Pribadi, Kontrak/Kost, Dinas.',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }
}
