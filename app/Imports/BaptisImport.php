<?php

namespace App\Imports;

use App\Imports\Concerns\ResolvesLookups;
use App\Models\Baptis;
use App\Models\Sakramen;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class BaptisImport implements ToModel, WithStartRow, WithValidation, SkipsEmptyRows, WithBatchInserts
{
    use ResolvesLookups;

    /*
     * Kolom:
     *  0  = nama_umat
     *  1  = tanggal_lahir_umat
     *  2  = tanggal_penerimaan
     *  3  = nomor_surat
     *  4  = paroki_nama
     *  5  = sumber_baptis       (KATOLIK / PROTESTAN)
     *  6  = klerus_nama
     *  7  = tgl_baptis
     *  8  = nama_baptis
     *  9  = nama_pemberi_protestan
     * 10  = nama_gereja_protestan
     * 11  = tgl_diterima_katolik
     * 12  = bapak_baptis_nama
     * 13  = bapak_baptis_tanggal_lahir
     * 14  = ibu_baptis_nama
     * 15  = ibu_baptis_tanggal_lahir
     */

    public function startRow(): int { return 2; }

    public function model(array $row): ?Sakramen
    {
        $umat     = $this->resolveUmatByValue($row[0] ?? '', $row[1] ?? null);
        $parokiId = $this->resolveParokiByValue($row[4] ?? '');
        $tanggal  = $this->resolveTanggalByValue($row[2] ?? null, 'tanggal_penerimaan');
        $tglBaptis = $this->parseDateHelper($row[7] ?? null);
        if (!$tglBaptis) {
            throw new \Exception("Format tgl_baptis \"{$row[7]}\" tidak valid.");
        }

        $sumber = strtoupper(trim($row[5] ?? 'KATOLIK'));
        $klerusId = ($sumber === 'KATOLIK') ? $this->resolveKlerusByValue($row[6] ?? '') : null;

        $exists = Sakramen::where('umat_id', $umat->id)->where('jenis_sakramen', 'BAPTIS')->exists();
        if ($exists) {
            throw new \Exception("Umat \"{$umat->nama}\" sudah memiliki data Baptis.");
        }

        $nomorSurat = trim($row[3] ?? '') ?: null;
        if ($nomorSurat && Sakramen::where('nomor_surat', $nomorSurat)->exists()) {
            throw new \Exception("Nomor surat \"{$nomorSurat}\" sudah digunakan.");
        }

        [$bapakId, $bapakNama] = $this->resolveWaliBaptisByValue($row[12] ?? '', $row[13] ?? null);
        [$ibuId, $ibuNama]     = $this->resolveWaliBaptisByValue($row[14] ?? '', $row[15] ?? null);

        $sakramen = Sakramen::create([
            'umat_id'            => $umat->id,
            'jenis_sakramen'     => 'BAPTIS',
            'tanggal_penerimaan' => $tanggal,
            'paroki_id'          => $parokiId,
            'klerus_id'          => $klerusId,
            'nomor_surat'        => $nomorSurat,
        ]);

        Baptis::create([
            'sakramen_id'            => $sakramen->id,
            'sumber_baptis'          => $sumber,
            'klerus_id'              => $klerusId,
            'nama_pemberi_protestan' => ($sumber === 'PROTESTAN') ? (trim($row[9] ?? '') ?: null) : null,
            'nama_gereja_protestan'  => ($sumber === 'PROTESTAN') ? (trim($row[10] ?? '') ?: null) : null,
            'tgl_baptis'             => $tglBaptis->toDateString(),
            'tgl_diterima_katolik'   => ($sumber === 'PROTESTAN') ? ($this->parseDateHelper($row[11] ?? null)?->toDateString()) : null,
            'nama_baptis'            => trim($row[8] ?? '') ?: null,
            'bapak_baptis_id'        => $bapakId,
            'bapak_baptis_nama'      => $bapakNama,
            'ibu_baptis_id'          => $ibuId,
            'ibu_baptis_nama'        => $ibuNama,
        ]);

        return null;
    }

    public function rules(): array
    {
        return [
            '0' => ['required', 'string'],
            '2' => ['required'],
            '5' => ['required', Rule::in(['KATOLIK', 'Katolik', 'PROTESTAN', 'Protestan'])],
            '7' => ['required'],
        ];
    }

    public function customValidationAttributes(): array
    {
        return ['0' => 'nama_umat', '2' => 'tanggal_penerimaan', '5' => 'sumber_baptis', '7' => 'tgl_baptis'];
    }

    public function customValidationMessages(): array
    {
        return [
            '0.required' => 'Kolom "nama_umat" wajib diisi.',
            '2.required' => 'Kolom "tanggal_penerimaan" wajib diisi.',
            '5.required' => 'Kolom "sumber_baptis" wajib diisi (KATOLIK / PROTESTAN).',
            '5.in'       => 'Kolom "sumber_baptis" harus KATOLIK atau PROTESTAN.',
            '7.required' => 'Kolom "tgl_baptis" wajib diisi.',
        ];
    }

    public function batchSize(): int { return 50; }
}
