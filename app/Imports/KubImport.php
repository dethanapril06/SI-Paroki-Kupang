<?php

namespace App\Imports;

use App\Models\Kub;
use App\Models\Wilayah;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class KubImport implements ToModel, WithStartRow, WithValidation, SkipsEmptyRows, WithBatchInserts
{
    // Kolom: 0=nama, 1=wilayah_nama

    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row): ?Kub
    {
        $nama        = trim($row[0] ?? '');
        $wilayahNama = trim($row[1] ?? '');

        $wilayah = Wilayah::where('nama', $wilayahNama)->first();
        if (!$wilayah) {
            throw new \Exception("Wilayah \"{$wilayahNama}\" tidak ditemukan. Pastikan Wilayah sudah diimport terlebih dahulu.");
        }

        $exists = Kub::where('nama', $nama)->where('wilayah_id', $wilayah->id)->exists();
        if ($exists) {
            throw new \Exception("KUB \"{$nama}\" sudah ada di wilayah \"{$wilayah->nama}\".");
        }

        return new Kub([
            'nama'       => $nama,
            'wilayah_id' => $wilayah->id,
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => ['required', 'string', 'max:255'], // nama
            '1' => ['required', 'string'],             // wilayah_nama
        ];
    }

    public function customValidationAttributes(): array
    {
        return ['0' => 'nama', '1' => 'wilayah_nama'];
    }

    public function customValidationMessages(): array
    {
        return [
            '0.required' => 'Kolom "nama" wajib diisi.',
            '1.required' => 'Kolom "wilayah_nama" wajib diisi.',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }
}
