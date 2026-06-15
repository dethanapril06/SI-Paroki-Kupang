<?php

namespace App\Exports\Templates;

class BaptisTemplateExport extends BaseTemplateExport
{
    protected function entityName(): string { return 'Sakramen Baptis'; }

    protected function headings(): array
    {
        return [
            'nama_umat', 'tanggal_lahir_umat', 'tanggal_penerimaan', 'nomor_surat',
            'paroki_nama', 'sumber_baptis', 'klerus_nama',
            'tgl_baptis', 'nama_baptis',
            'nama_pemberi_protestan', 'nama_gereja_protestan', 'tgl_diterima_katolik',
            'bapak_baptis_nama', 'bapak_baptis_tanggal_lahir',
            'ibu_baptis_nama', 'ibu_baptis_tanggal_lahir',
        ];
    }

    protected function exampleRow(): array
    {
        return [
            'Yohanes Belo', '1985-06-15', '1985-07-20', 'BAP/2025/001',
            'Paroki Kristus Raja', 'KATOLIK', 'Rm. Antonius Seo',
            '1985-07-20', 'Yohanes',
            '', '', '',
            'Petrus Belo', '1960-01-10',
            'Maria Leba', '1962-03-05',
        ];
    }

    protected function columnNotes(): array
    {
        return [
            'nama_umat'                => 'Nama lengkap umat (harus sudah ada di sistem). WAJIB.',
            'tanggal_lahir_umat'       => 'Tanggal lahir umat (untuk mencari jika ada nama sama). Format: YYYY-MM-DD.',
            'tanggal_penerimaan'       => 'Tanggal resmi penerimaan sakramen. WAJIB. Format: YYYY-MM-DD.',
            'nomor_surat'              => 'Nomor surat baptis (opsional, harus unik).',
            'paroki_nama'              => 'Nama paroki tempat baptis (opsional).',
            'sumber_baptis'            => '"KATOLIK" atau "PROTESTAN". WAJIB.',
            'klerus_nama'              => 'Nama klerus pembaptis (opsional, jika KATOLIK).',
            'tgl_baptis'               => 'Tanggal baptis asli. WAJIB. Format: YYYY-MM-DD.',
            'nama_baptis'              => 'Nama baptis (opsional).',
            'nama_pemberi_protestan'   => 'Nama pemberi baptis (isi jika PROTESTAN).',
            'nama_gereja_protestan'    => 'Nama gereja (isi jika PROTESTAN).',
            'tgl_diterima_katolik'     => 'Tanggal diterima Gereja Katolik (isi jika PROTESTAN). Format: YYYY-MM-DD.',
            'bapak_baptis_nama'        => 'Nama bapak baptis/wali pria (opsional). Jika terdaftar di sistem, akan otomatis dihubungkan.',
            'bapak_baptis_tanggal_lahir' => 'Tanggal lahir bapak baptis untuk identifikasi di sistem. Format: YYYY-MM-DD.',
            'ibu_baptis_nama'          => 'Nama ibu baptis/wali wanita (opsional). Jika terdaftar di sistem, akan otomatis dihubungkan.',
            'ibu_baptis_tanggal_lahir' => 'Tanggal lahir ibu baptis untuk identifikasi di sistem. Format: YYYY-MM-DD.',
        ];
    }
}
