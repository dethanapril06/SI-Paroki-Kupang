<?php

namespace App\Exports\Templates;

class KomuniPertamaTemplateExport extends BaseTemplateExport
{
    protected function entityName(): string { return 'Sakramen Komuni Pertama'; }

    protected function headings(): array
    {
        return ['nama_umat', 'tanggal_lahir_umat', 'tanggal_penerimaan', 'nomor_surat', 'paroki_nama', 'klerus_nama'];
    }

    protected function exampleRow(): array
    {
        return ['Yohanes Belo', '1985-06-15', '1998-05-10', 'KOM/1998/045', 'Paroki Kristus Raja', 'Rm. Antonius Seo'];
    }

    protected function columnNotes(): array
    {
        return [
            'nama_umat'          => 'Nama lengkap umat (harus sudah ada di sistem). WAJIB.',
            'tanggal_lahir_umat' => 'Tanggal lahir umat untuk identifikasi jika ada nama sama. Format: YYYY-MM-DD.',
            'tanggal_penerimaan' => 'Tanggal penerimaan Komuni Pertama. WAJIB. Format: YYYY-MM-DD.',
            'nomor_surat'        => 'Nomor surat komuni pertama (opsional, harus unik).',
            'paroki_nama'        => 'Nama paroki tempat penerimaan (opsional).',
            'klerus_nama'        => 'Nama klerus yang memimpin (opsional).',
        ];
    }
}
