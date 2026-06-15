<?php

namespace App\Exports\Templates;

class KeluargaTemplateExport extends BaseTemplateExport
{
    protected function entityName(): string { return 'Keluarga'; }

    protected function headings(): array
    {
        return ['kub_nama', 'wilayah_nama', 'alamat', 'status_tempat_tinggal'];
    }

    protected function exampleRow(): array
    {
        return ['KUB Santo Yosef', 'Wilayah Santo Petrus', 'Jl. Timor Raya No. 12, RT 01/RW 02, Kelurahan Oebobo', 'Rumah Pribadi'];
    }

    protected function columnNotes(): array
    {
        return [
            'kub_nama'              => 'Nama KUB tempat keluarga ini terdaftar. WAJIB.',
            'wilayah_nama'          => 'Nama wilayah (opsional, untuk membantu jika ada KUB dengan nama sama di wilayah berbeda).',
            'alamat'                => 'Alamat lengkap keluarga. WAJIB.',
            'status_tempat_tinggal' => 'Pilih: "Rumah Pribadi" | "Kontrak/Kost" | "Dinas". WAJIB.',
        ];
    }
}
