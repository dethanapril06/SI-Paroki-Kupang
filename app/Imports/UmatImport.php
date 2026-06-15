<?php

namespace App\Imports;

use App\Models\Keluarga;
use App\Models\Kub;
use App\Models\Umat;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class UmatImport implements ToModel, WithStartRow, WithValidation, SkipsEmptyRows, WithBatchInserts
{
    /*
     * Kolom (index sesuai template):
     *  0  = kub_nama
     *  1  = wilayah_nama
     *  2  = alamat_keluarga
     *  3  = nama
     *  4  = tempat_lahir
     *  5  = tanggal_lahir
     *  6  = jenis_kelamin
     *  7  = hubungan_keluarga
     *  8  = nama_ayah
     *  9  = nama_ibu
     * 10  = status_pernikahan
     * 11  = no_telepon
     * 12  = golongan_darah
     * 13  = pendidikan
     * 14  = pekerjaan
     * 15  = penyandang_disabilitas
     * 16  = status_keaktifan
     * 17  = keterangan_lain
     */

    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row): ?Umat
    {
        $kubNama       = trim($row[0] ?? '');
        $wilayahNama   = trim($row[1] ?? '');
        $alamatKel     = trim($row[2] ?? '');

        // Lookup keluarga
        $kubQuery = Kub::where('nama', $kubNama);
        if ($wilayahNama !== '') {
            $kubQuery->whereHas('wilayah', fn($q) => $q->where('nama', $wilayahNama));
        }
        $kub = $kubQuery->first();
        if (!$kub) {
            throw new \Exception("KUB \"{$kubNama}\" tidak ditemukan.");
        }

        $keluarga = Keluarga::where('kub_id', $kub->id)->where('alamat', $alamatKel)->first();
        if (!$keluarga) {
            throw new \Exception("Keluarga dengan alamat \"{$alamatKel}\" di KUB \"{$kub->nama}\" tidak ditemukan. Pastikan Keluarga sudah diimport.");
        }

        $tanggalLahir = $this->parseDate($row[5] ?? null);
        if (!$tanggalLahir) {
            throw new \Exception("Format tanggal_lahir \"{$row[5]}\" tidak valid. Gunakan YYYY-MM-DD atau DD/MM/YYYY.");
        }

        $nama = trim($row[3] ?? '');
        $exists = Umat::where('keluarga_id', $keluarga->id)
            ->where('nama', $nama)
            ->where('tanggal_lahir', $tanggalLahir->toDateString())
            ->exists();
        if ($exists) {
            throw new \Exception("Umat \"{$nama}\" (lahir: {$tanggalLahir->toDateString()}) sudah ada dalam keluarga tersebut.");
        }

        return new Umat([
            'keluarga_id'            => $keluarga->id,
            'nama'                   => $nama,
            'tempat_lahir'           => trim($row[4] ?? ''),
            'tanggal_lahir'          => $tanggalLahir->toDateString(),
            'jenis_kelamin'          => trim($row[6] ?? ''),
            'hubungan_keluarga'      => trim($row[7] ?? ''),
            'nama_ayah'              => $row[8] !== '' ? trim($row[8]) : null,
            'nama_ibu'               => $row[9] !== '' ? trim($row[9]) : null,
            'status_pernikahan'      => trim($row[10] ?? ''),
            'no_telepon'             => $row[11] !== '' ? trim($row[11]) : null,
            'golongan_darah'         => $row[12] !== '' ? trim($row[12]) : null,
            'pendidikan'             => $row[13] !== '' ? trim($row[13]) : null,
            'pekerjaan'              => $row[14] !== '' ? trim($row[14]) : null,
            'penyandang_disabilitas' => $this->parseBool($row[15] ?? 'Tidak'),
            'status_almarhum'        => false,
            'status_keaktifan'       => ($row[16] ?? 'aktif') !== '' ? trim($row[16]) : 'aktif',
            'keterangan_lain'        => $row[17] !== '' ? trim($row[17]) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            '0'  => ['required', 'string'],
            '2'  => ['required', 'string'],
            '3'  => ['required', 'string', 'max:255'],
            '4'  => ['required', 'string'],
            '5'  => ['required'],
            '6'  => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            '7'  => ['required', Rule::in(['Suami', 'Istri', 'Anak', 'Saudara', 'Ayah', 'Ibu', 'Lainnya'])],
            '10' => ['required', Rule::in(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'])],
            '12' => ['nullable', Rule::in(['A', 'B', 'AB', 'O', ''])],
            '13' => ['nullable', Rule::in(['Tidak Sekolah', 'SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3', ''])],
            '16' => ['nullable', Rule::in(['aktif', 'non-aktif', ''])],
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            '0'  => 'kub_nama',
            '2'  => 'alamat_keluarga',
            '3'  => 'nama',
            '4'  => 'tempat_lahir',
            '5'  => 'tanggal_lahir',
            '6'  => 'jenis_kelamin',
            '7'  => 'hubungan_keluarga',
            '10' => 'status_pernikahan',
            '12' => 'golongan_darah',
            '13' => 'pendidikan',
            '16' => 'status_keaktifan',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '0.required'  => 'Kolom "kub_nama" wajib diisi.',
            '2.required'  => 'Kolom "alamat_keluarga" wajib diisi.',
            '3.required'  => 'Kolom "nama" wajib diisi.',
            '4.required'  => 'Kolom "tempat_lahir" wajib diisi.',
            '5.required'  => 'Kolom "tanggal_lahir" wajib diisi.',
            '6.required'  => 'Kolom "jenis_kelamin" wajib diisi.',
            '6.in'        => 'Kolom "jenis_kelamin" harus "Laki-laki" atau "Perempuan".',
            '7.required'  => 'Kolom "hubungan_keluarga" wajib diisi.',
            '7.in'        => 'Kolom "hubungan_keluarga" tidak valid.',
            '10.required' => 'Kolom "status_pernikahan" wajib diisi.',
            '10.in'       => 'Kolom "status_pernikahan" tidak valid.',
            '12.in'       => 'Kolom "golongan_darah" harus A, B, AB, atau O.',
            '13.in'       => 'Kolom "pendidikan" tidak valid.',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    private function parseDate(mixed $value): ?Carbon
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

    private function parseBool(mixed $value): bool
    {
        if (is_bool($value)) return $value;
        return in_array(strtolower(trim((string) $value)), ['ya', 'yes', '1', 'true']);
    }
}
