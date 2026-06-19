<?php

namespace App\Exports\Templates;

class UmatTemplateExport extends BaseTemplateExport
{
    protected function entityName(): string { return 'Umat'; }

    protected function headings(): array
    {
        return [
            'kub_nama', 'wilayah_nama', 'alamat_keluarga',
            'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
            'hubungan_keluarga', 'nama_ayah', 'nama_ibu',
            'status_pernikahan', 'no_telepon', 'golongan_darah',
            'pendidikan', 'pekerjaan', 'penyandang_disabilitas',
            'status_keaktifan', 'keterangan_lain', 'email',
        ];
    }

    protected function exampleRow(): array
    {
        return [
            'KUB Santo Yosef',
            'Wilayah Santo Petrus',
            'Jl. Timor Raya No. 12, RT 01/RW 02, Kelurahan Oebobo',
            'Yohanes Belo',
            'Kupang',
            '1985-06-15',
            'Laki-laki',
            'Suami',
            'Petrus Belo',
            'Maria Seo',
            'Kawin',
            '081234567890',
            'O',
            'S1',
            'PNS',
            'Tidak',
            'aktif',
            '',
            'yohanes.belo@gmail.com',
        ];
    }

    protected function columnNotes(): array
    {
        return [
            'kub_nama'               => 'Nama KUB keluarga umat ini. WAJIB.',
            'wilayah_nama'           => 'Nama wilayah (opsional, untuk disambiguasi).',
            'alamat_keluarga'        => 'Alamat keluarga persis seperti di template Keluarga. WAJIB.',
            'nama'                   => 'Nama lengkap umat. WAJIB.',
            'tempat_lahir'           => 'Kota/kabupaten tempat lahir. WAJIB.',
            'tanggal_lahir'          => 'Format: YYYY-MM-DD (contoh: 1985-06-15). WAJIB.',
            'jenis_kelamin'          => '"Laki-laki" atau "Perempuan". WAJIB.',
            'hubungan_keluarga'      => 'Pilih: Suami | Istri | Anak | Saudara | Ayah | Ibu | Lainnya. WAJIB.',
            'nama_ayah'              => 'Nama ayah (opsional).',
            'nama_ibu'               => 'Nama ibu (opsional).',
            'status_pernikahan'      => 'Pilih: Belum Kawin | Kawin | Cerai Hidup | Cerai Mati. WAJIB.',
            'no_telepon'             => 'Nomor telepon/HP (opsional).',
            'golongan_darah'         => 'Pilih: A | B | AB | O (opsional).',
            'pendidikan'             => 'Pilih: Tidak Sekolah | SD | SMP | SMA | D3 | S1 | S2 | S3 (opsional).',
            'pekerjaan'              => 'Pekerjaan (opsional).',
            'penyandang_disabilitas' => '"Ya" atau "Tidak".',
            'status_keaktifan'       => '"aktif" atau "non-aktif". Default: aktif.',
            'keterangan_lain'        => 'Keterangan tambahan (opsional).',
            'email'                  => 'Alamat email untuk login (opsional). Jika diisi, akun login akan dibuat otomatis dengan password default "password".',
        ];
    }
}
