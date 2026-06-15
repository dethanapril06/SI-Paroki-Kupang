<?php

namespace App\Exports\Templates;

class MinyakSuciTemplateExport extends BaseTemplateExport
{
    protected function entityName(): string { return 'Sakramen Minyak Suci'; }

    protected function headings(): array
    {
        return [
            'nama_umat', 'tanggal_lahir_umat', 'tanggal_penerimaan', 'nomor_surat',
            'paroki_nama', 'klerus_nama', 'tempat_terima', 'nama_pemberi', 'keterangan_sebab',
        ];
    }

    protected function exampleRow(): array
    {
        return [
            'Petrus Belo', '1960-01-10', '2024-03-15', '',
            'Paroki Kristus Raja', 'Rm. Antonius Seo',
            'RS Siloam Kupang', '', 'Sakit berat',
        ];
    }

    protected function columnNotes(): array
    {
        return [
            'nama_umat'          => 'Nama lengkap umat penerima (harus sudah ada di sistem). WAJIB.',
            'tanggal_lahir_umat' => 'Tanggal lahir umat untuk identifikasi. Format: YYYY-MM-DD.',
            'tanggal_penerimaan' => 'Tanggal penerimaan minyak suci. WAJIB. Format: YYYY-MM-DD.',
            'nomor_surat'        => 'Nomor surat (opsional, harus unik jika diisi).',
            'paroki_nama'        => 'Nama paroki (opsional).',
            'klerus_nama'        => 'Nama klerus pemberi (opsional, harus terdaftar di data Klerus).',
            'tempat_terima'      => 'Tempat penerimaan minyak suci (misal: RS Siloam, Rumah). WAJIB.',
            'nama_pemberi'       => 'Nama pemberi minyak suci secara manual (opsional, jika bukan klerus terdaftar).',
            'keterangan_sebab'   => 'Sebab penerimaan minyak suci (opsional).',
        ];
    }
}
