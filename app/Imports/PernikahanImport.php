<?php

namespace App\Imports;

use App\Imports\Concerns\ResolvesLookups;
use App\Models\Pernikahan;
use App\Models\Sakramen;
use App\Models\Umat;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class PernikahanImport implements ToModel, WithStartRow, WithValidation, SkipsEmptyRows, WithBatchInserts
{
    use ResolvesLookups;

    /*
     * Kolom:
     *  0  = nama_umat
     *  1  = tanggal_lahir_umat
     *  2  = tanggal_penerimaan
     *  3  = nomor_surat
     *  4  = paroki_nama
     *  5  = klerus_nama
     *  6  = pasangan_nama
     *  7  = pasangan_tanggal_lahir
     *  8  = pasangan_agama
     *  9  = jenis_pernikahan
     * 10  = izin_beda_gereja
     * 11  = dispensasi
     * 12  = tanggal_nikah_katolik
     * 13  = tanggal_catatan_sipil
     */

    public function startRow(): int { return 2; }

    public function model(array $row): ?Sakramen
    {
        $umat     = $this->resolveUmatByValue($row[0] ?? '', $row[1] ?? null);
        $parokiId = $this->resolveParokiByValue($row[4] ?? '');
        $klerusId = $this->resolveKlerusByValue($row[5] ?? '');
        $tanggal  = $this->resolveTanggalByValue($row[2] ?? null, 'tanggal_penerimaan');

        $exists = Sakramen::where('umat_id', $umat->id)->where('jenis_sakramen', 'PERNIKAHAN')->exists();
        if ($exists) {
            throw new \Exception("Umat \"{$umat->nama}\" sudah memiliki data Pernikahan.");
        }

        $nomorSurat = trim($row[3] ?? '') ?: null;
        if ($nomorSurat && Sakramen::where('nomor_surat', $nomorSurat)->exists()) {
            throw new \Exception("Nomor surat \"{$nomorSurat}\" sudah digunakan.");
        }

        // Resolve pasangan (hybrid)
        $pasanganNama  = trim($row[6] ?? '');
        $pasanganId    = null;
        $pasanganAgama = trim($row[8] ?? '') ?: null;

        if ($pasanganNama !== '') {
            $tglPasangan = $this->parseDateHelper($row[7] ?? null);
            $q = Umat::where('nama', $pasanganNama);
            if ($tglPasangan) $q->where('tanggal_lahir', $tglPasangan->toDateString());
            $pasanganUmat = $q->first();
            if ($pasanganUmat) {
                $pasanganId   = $pasanganUmat->id;
                $pasanganNama = null;
            }
        } else {
            $pasanganNama = null;
        }

        $sakramen = Sakramen::create([
            'umat_id'            => $umat->id,
            'jenis_sakramen'     => 'PERNIKAHAN',
            'tanggal_penerimaan' => $tanggal,
            'paroki_id'          => $parokiId,
            'klerus_id'          => $klerusId,
            'nomor_surat'        => $nomorSurat,
        ]);

        Pernikahan::create([
            'sakramen_id'           => $sakramen->id,
            'pasangan_id'           => $pasanganId,
            'pasangan_nama'         => $pasanganNama,
            'pasangan_agama'        => $pasanganAgama,
            'jenis_pernikahan'      => strtoupper(trim($row[9] ?? '')),
            'izin_beda_gereja'      => $this->parseBoolHelper($row[10] ?? 'Tidak'),
            'dispensasi'            => $this->parseBoolHelper($row[11] ?? 'Tidak'),
            'tanggal_nikah_katolik' => $this->parseDateHelper($row[12] ?? null)?->toDateString(),
            'tanggal_catatan_sipil' => $this->parseDateHelper($row[13] ?? null)?->toDateString(),
        ]);

        return null;
    }

    public function rules(): array
    {
        return [
            '0' => ['required', 'string'],
            '2' => ['required'],
            '9' => ['required', Rule::in([
                'KATOLIK_KATOLIK', 'KATOLIK_PROTESTAN', 'KATOLIK_ISLAM',
                'KATOLIK_HINDU', 'KATOLIK_BUDDHA', 'KATOLIK_KONGHUCU', 'KATOLIK_KEPERCAYAAN',
            ])],
        ];
    }

    public function customValidationAttributes(): array
    {
        return ['0' => 'nama_umat', '2' => 'tanggal_penerimaan', '9' => 'jenis_pernikahan'];
    }

    public function customValidationMessages(): array
    {
        return [
            '0.required' => 'Kolom "nama_umat" wajib diisi.',
            '2.required' => 'Kolom "tanggal_penerimaan" wajib diisi.',
            '9.required' => 'Kolom "jenis_pernikahan" wajib diisi.',
            '9.in'       => 'Kolom "jenis_pernikahan" tidak valid. Contoh: KATOLIK_KATOLIK, KATOLIK_PROTESTAN.',
        ];
    }

    public function batchSize(): int { return 50; }
}
