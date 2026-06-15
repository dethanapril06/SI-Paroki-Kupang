<?php

namespace App\Imports;

use App\Imports\Concerns\ResolvesLookups;
use App\Models\KomuniPertama;
use App\Models\Sakramen;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class KomuniPertamaImport implements ToModel, WithStartRow, WithValidation, SkipsEmptyRows, WithBatchInserts
{
    use ResolvesLookups;

    // Kolom: 0=nama_umat, 1=tanggal_lahir_umat, 2=tanggal_penerimaan, 3=nomor_surat, 4=paroki_nama, 5=klerus_nama

    public function startRow(): int { return 2; }

    public function model(array $row): ?Sakramen
    {
        $umat     = $this->resolveUmatByValue($row[0] ?? '', $row[1] ?? null);
        $parokiId = $this->resolveParokiByValue($row[4] ?? '');
        $klerusId = $this->resolveKlerusByValue($row[5] ?? '');
        $tanggal  = $this->resolveTanggalByValue($row[2] ?? null, 'tanggal_penerimaan');

        $exists = Sakramen::where('umat_id', $umat->id)->where('jenis_sakramen', 'KOMUNI_PERTAMA')->exists();
        if ($exists) {
            throw new \Exception("Umat \"{$umat->nama}\" sudah memiliki data Komuni Pertama.");
        }

        $nomorSurat = trim($row[3] ?? '') ?: null;
        if ($nomorSurat && Sakramen::where('nomor_surat', $nomorSurat)->exists()) {
            throw new \Exception("Nomor surat \"{$nomorSurat}\" sudah digunakan.");
        }

        $sakramen = Sakramen::create([
            'umat_id'            => $umat->id,
            'jenis_sakramen'     => 'KOMUNI_PERTAMA',
            'tanggal_penerimaan' => $tanggal,
            'paroki_id'          => $parokiId,
            'klerus_id'          => $klerusId,
            'nomor_surat'        => $nomorSurat,
        ]);

        KomuniPertama::create(['sakramen_id' => $sakramen->id]);

        return null;
    }

    public function rules(): array
    {
        return [
            '0' => ['required', 'string'],
            '2' => ['required'],
        ];
    }

    public function customValidationAttributes(): array
    {
        return ['0' => 'nama_umat', '2' => 'tanggal_penerimaan'];
    }

    public function customValidationMessages(): array
    {
        return [
            '0.required' => 'Kolom "nama_umat" wajib diisi.',
            '2.required' => 'Kolom "tanggal_penerimaan" wajib diisi.',
        ];
    }

    public function batchSize(): int { return 50; }
}
