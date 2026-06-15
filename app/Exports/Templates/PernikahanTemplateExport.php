<?php

namespace App\Exports\Templates;

class PernikahanTemplateExport extends BaseTemplateExport
{
    protected function entityName(): string { return 'Sakramen Pernikahan'; }

    protected function headings(): array
    {
        return [
            'nama_umat', 'tanggal_lahir_umat', 'tanggal_penerimaan', 'nomor_surat',
            'paroki_nama', 'klerus_nama',
            'pasangan_nama', 'pasangan_tanggal_lahir', 'pasangan_agama',
            'jenis_pernikahan', 'izin_beda_gereja', 'dispensasi',
            'tanggal_nikah_katolik', 'tanggal_catatan_sipil',
        ];
    }

    protected function exampleRow(): array
    {
        return [
            'Yohanes Belo', '1985-06-15', '2010-09-17', 'NIK/2010/007',
            'Paroki Kristus Raja', 'Rm. Antonius Seo',
            'Maria Seo', '1988-03-22', '',
            'KATOLIK_KATOLIK', 'Tidak', 'Tidak',
            '2010-09-17', '2010-09-20',
        ];
    }

    protected function columnNotes(): array
    {
        return [
            'nama_umat'              => 'Nama lengkap umat (pencatat pertama). WAJIB.',
            'tanggal_lahir_umat'     => 'Tanggal lahir umat untuk identifikasi. Format: YYYY-MM-DD.',
            'tanggal_penerimaan'     => 'Tanggal sakramen pernikahan. WAJIB. Format: YYYY-MM-DD.',
            'nomor_surat'            => 'Nomor surat nikah gereja (opsional, harus unik).',
            'paroki_nama'            => 'Nama paroki tempat menikah (opsional).',
            'klerus_nama'            => 'Nama klerus yang memimpin pernikahan (opsional).',
            'pasangan_nama'          => 'Nama lengkap pasangan. Jika terdaftar di sistem, akan otomatis dihubungkan.',
            'pasangan_tanggal_lahir' => 'Tanggal lahir pasangan untuk identifikasi di sistem. Format: YYYY-MM-DD.',
            'pasangan_agama'         => 'Agama pasangan (isi jika beda agama, misal: "Islam", "Protestan").',
            'jenis_pernikahan'       => 'Pilih: KATOLIK_KATOLIK | KATOLIK_PROTESTAN | KATOLIK_ISLAM | KATOLIK_HINDU | KATOLIK_BUDDHA | KATOLIK_KONGHUCU | KATOLIK_KEPERCAYAAN. WAJIB.',
            'izin_beda_gereja'       => '"Ya" atau "Tidak".',
            'dispensasi'             => '"Ya" atau "Tidak".',
            'tanggal_nikah_katolik'  => 'Tanggal nikah gereja (opsional). Format: YYYY-MM-DD.',
            'tanggal_catatan_sipil'  => 'Tanggal catatan sipil (opsional). Format: YYYY-MM-DD.',
        ];
    }
}
