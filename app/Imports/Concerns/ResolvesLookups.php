<?php

namespace App\Imports\Concerns;

use App\Models\Klerus;
use App\Models\Paroki;
use App\Models\Umat;
use Carbon\Carbon;

/**
 * Shared helpers for all Sakramen import classes (column-index based).
 */
trait ResolvesLookups
{
    /**
     * Lookup umat by nama + tanggal_lahir.
     * $namaVal = nilai string nama, $tglVal = nilai string/numeric tanggal lahir
     */
    protected function resolveUmatByValue(string $namaUmat, mixed $tglLahirVal): Umat
    {
        $namaUmat = trim($namaUmat);
        $tglLahir = $this->parseDateHelper($tglLahirVal);

        $query = Umat::where('nama', $namaUmat);
        if ($tglLahir) {
            $query->where('tanggal_lahir', $tglLahir->toDateString());
        }

        $umat = $query->first();
        if (!$umat) {
            $tglStr = $tglLahir ? $tglLahir->toDateString() : ($tglLahirVal ?? '-');
            throw new \Exception("Umat \"{$namaUmat}\" (lahir: {$tglStr}) tidak ditemukan. Pastikan data Umat sudah diimport.");
        }

        return $umat;
    }

    /**
     * Lookup paroki by nama string (nullable).
     */
    protected function resolveParokiByValue(string $parokiNama): ?int
    {
        $parokiNama = trim($parokiNama);
        if ($parokiNama === '') return null;

        $paroki = Paroki::where('nama', $parokiNama)->first();
        if (!$paroki) {
            throw new \Exception("Paroki \"{$parokiNama}\" tidak ditemukan.");
        }
        return $paroki->id;
    }

    /**
     * Lookup klerus by nama string (nullable).
     */
    protected function resolveKlerusByValue(string $klerusNama): ?int
    {
        $klerusNama = trim($klerusNama);
        if ($klerusNama === '') return null;

        $klerus = Klerus::where('nama', $klerusNama)->first();
        if (!$klerus) {
            throw new \Exception("Klerus \"{$klerusNama}\" tidak ditemukan.");
        }
        return $klerus->id;
    }

    /**
     * Parse tanggal_penerimaan dari nilai raw.
     */
    protected function resolveTanggalByValue(mixed $value, string $label = 'tanggal_penerimaan'): string
    {
        $tgl = $this->parseDateHelper($value);
        if (!$tgl) {
            throw new \Exception("Format {$label} \"{$value}\" tidak valid. Gunakan YYYY-MM-DD atau DD/MM/YYYY.");
        }
        return $tgl->toDateString();
    }

    /**
     * Resolve wali baptis (hybrid: FK lookup via nama+tanggal lahir, fallback nama manual).
     * Returns [umat_id|null, nama_manual|null]
     */
    protected function resolveWaliBaptisByValue(string $namaWali, mixed $tglLahirWali): array
    {
        $namaWali = trim($namaWali);
        if ($namaWali === '') return [null, null];

        $tglLahir = $this->parseDateHelper($tglLahirWali);
        $query = Umat::where('nama', $namaWali);
        if ($tglLahir) {
            $query->where('tanggal_lahir', $tglLahir->toDateString());
        }
        $umat = $query->first();

        return $umat ? [$umat->id, null] : [null, $namaWali];
    }

    /**
     * Parse date from Excel serial number or string.
     */
    protected function parseDateHelper(mixed $value): ?Carbon
    {
        if (empty($value)) return null;

        if (is_numeric($value)) {
            try {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
            } catch (\Exception) { return null; }
        }

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d'] as $fmt) {
            try { return Carbon::createFromFormat($fmt, trim($value)); } catch (\Exception) { continue; }
        }

        return null;
    }

    /**
     * Parse Ya/Tidak or 1/0 to boolean.
     */
    protected function parseBoolHelper(mixed $value): bool
    {
        if (is_bool($value)) return $value;
        return in_array(strtolower(trim((string) $value)), ['ya', 'yes', '1', 'true']);
    }
}
