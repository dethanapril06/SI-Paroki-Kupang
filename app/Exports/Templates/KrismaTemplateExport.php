<?php

namespace App\Exports\Templates;

class KrismaTemplateExport extends BaseTemplateExport
{
    protected function entityName(): string { return 'Sakramen Krisma'; }

    protected function headings(): array
    {
        return ['nama_umat', 'tanggal_lahir_umat', 'tanggal_penerimaan', 'nomor_surat', 'paroki_nama', 'uskup_nama', 'nama_krisma'];
    }

    protected function exampleRow(): array
    {
        return ['Yohanes Belo', '1985-06-15', '2002-11-03', 'KRS/2002/012', 'Paroki Kristus Raja', 'Mgr. Petrus Turang', 'Yohanes Maria'];
    }

    protected function columnNotes(): array
    {
        return [
            'nama_umat'          => 'Nama lengkap umat (harus sudah ada di sistem). WAJIB.',
            'tanggal_lahir_umat' => 'Tanggal lahir umat untuk identifikasi. Format: YYYY-MM-DD.',
            'tanggal_penerimaan' => 'Tanggal penerimaan Krisma. WAJIB. Format: YYYY-MM-DD.',
            'nomor_surat'        => 'Nomor surat krisma (opsional, harus unik).',
            'paroki_nama'        => 'Nama paroki tempat penerimaan (opsional).',
            'uskup_nama'         => 'Nama uskup yang menerimakan (opsional, harus terdaftar di data Klerus).',
            'nama_krisma'        => 'Nama krisma yang dipilih (opsional).',
        ];
    }
}
