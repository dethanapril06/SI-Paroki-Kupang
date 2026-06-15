<?php

namespace App\Imports;

use App\Models\Kuasi;
use App\Models\Paroki;
use App\Models\Stasi;
use App\Models\Wilayah;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class WilayahImport implements ToModel, WithStartRow, WithValidation, SkipsEmptyRows, WithBatchInserts
{
    // Kolom: 0=nama, 1=paroki_nama, 2=kuasi_nama, 3=stasi_nama

    public function startRow(): int
    {
        return 2; // Skip baris header (baris 1)
    }

    public function model(array $row): ?Wilayah
    {
        $nama       = trim($row[0] ?? '');
        $parokiNama = trim($row[1] ?? '');
        $kuasiNama  = trim($row[2] ?? '');
        $stasiNama  = trim($row[3] ?? '');

        // Resolve parent: paroki / kuasi / stasi
        $parokiId = null;
        $kuasiId  = null;
        $stasiId  = null;

        if ($parokiNama !== '') {
            $paroki = Paroki::where('nama', $parokiNama)->first();
            if (!$paroki) {
                throw new \Exception("Paroki \"{$parokiNama}\" tidak ditemukan. Pastikan nama paroki sudah benar.");
            }
            $parokiId = $paroki->id;
        } elseif ($kuasiNama !== '') {
            $kuasi = Kuasi::where('nama', $kuasiNama)->first();
            if (!$kuasi) {
                throw new \Exception("Kuasi \"{$kuasiNama}\" tidak ditemukan.");
            }
            $kuasiId = $kuasi->id;
        } elseif ($stasiNama !== '') {
            $stasi = Stasi::where('nama', $stasiNama)->first();
            if (!$stasi) {
                throw new \Exception("Stasi \"{$stasiNama}\" tidak ditemukan.");
            }
            $stasiId = $stasi->id;
        }

        // Cek duplikat
        $exists = Wilayah::where('nama', $nama)
            ->where('paroki_id', $parokiId)
            ->where('kuasi_id', $kuasiId)
            ->where('stasi_id', $stasiId)
            ->exists();

        if ($exists) {
            throw new \Exception("Wilayah \"{$nama}\" sudah ada di sistem.");
        }

        return new Wilayah([
            'nama'      => $nama,
            'paroki_id' => $parokiId,
            'kuasi_id'  => $kuasiId,
            'stasi_id'  => $stasiId,
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => ['required', 'string', 'max:255'], // nama
        ];
    }

    public function customValidationAttributes(): array
    {
        return ['0' => 'nama'];
    }

    public function customValidationMessages(): array
    {
        return [
            '0.required' => 'Kolom "nama" wajib diisi.',
            '0.string'   => 'Kolom "nama" harus berupa teks.',
            '0.max'      => 'Kolom "nama" maksimal 255 karakter.',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }
}
