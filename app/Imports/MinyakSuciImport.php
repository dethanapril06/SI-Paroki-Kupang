<?php

namespace App\Imports;

use App\Imports\Concerns\ResolvesLookups;
use App\Models\MinyakSuci;
use App\Models\Sakramen;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class MinyakSuciImport implements ToModel, WithStartRow, WithValidation, SkipsEmptyRows, WithBatchInserts
{
    use ResolvesLookups;

    // Kolom: 0=nama_umat, 1=tanggal_lahir_umat, 2=tanggal_penerimaan, 3=nomor_surat, 4=paroki_nama, 5=klerus_nama, 6=tempat_terima, 7=nama_pemberi, 8=keterangan_sebab

    public function startRow(): int { return 2; }

    public function model(array $row): ?Sakramen
    {
        $umat     = $this->resolveUmatByValue($row[0] ?? '', $row[1] ?? null);
        $parokiId = $this->resolveParokiByValue($row[4] ?? '');
        $klerusId = $this->resolveKlerusByValue($row[5] ?? '');
        $tanggal  = $this->resolveTanggalByValue($row[2] ?? null, 'tanggal_penerimaan');
        $tempat   = trim($row[6] ?? '');

        $exists = Sakramen::where('umat_id', $umat->id)
            ->where('jenis_sakramen', 'MINYAK_SUCI')
            ->where('tanggal_penerimaan', $tanggal)
            ->exists();
        if ($exists) {
            throw new \Exception("Umat \"{$umat->nama}\" sudah memiliki data Minyak Suci pada tanggal {$tanggal}.");
        }

        $nomorSurat = trim($row[3] ?? '') ?: null;
        if ($nomorSurat && Sakramen::where('nomor_surat', $nomorSurat)->exists()) {
            throw new \Exception("Nomor surat \"{$nomorSurat}\" sudah digunakan.");
        }

        $sakramen = Sakramen::create([
            'umat_id'            => $umat->id,
            'jenis_sakramen'     => 'MINYAK_SUCI',
            'tanggal_penerimaan' => $tanggal,
            'paroki_id'          => $parokiId,
            'klerus_id'          => $klerusId,
            'nomor_surat'        => $nomorSurat,
        ]);

        MinyakSuci::create([
            'sakramen_id'      => $sakramen->id,
            'tempat_terima'    => $tempat,
            'nama_pemberi'     => trim($row[7] ?? '') ?: null,
            'keterangan_sebab' => trim($row[8] ?? '') ?: null,
        ]);

        return null;
    }

    public function rules(): array
    {
        return [
            '0' => ['required', 'string'],
            '2' => ['required'],
            '6' => ['required', 'string'],
        ];
    }

    public function customValidationAttributes(): array
    {
        return ['0' => 'nama_umat', '2' => 'tanggal_penerimaan', '6' => 'tempat_terima'];
    }

    public function customValidationMessages(): array
    {
        return [
            '0.required' => 'Kolom "nama_umat" wajib diisi.',
            '2.required' => 'Kolom "tanggal_penerimaan" wajib diisi.',
            '6.required' => 'Kolom "tempat_terima" wajib diisi.',
        ];
    }

    public function batchSize(): int { return 50; }
}
