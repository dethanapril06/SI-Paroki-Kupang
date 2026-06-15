<?php

namespace App\Exports\Templates;

class KubTemplateExport extends BaseTemplateExport
{
    protected function entityName(): string { return 'KUB (Komunitas Umat Basis)'; }

    protected function headings(): array
    {
        return ['nama', 'wilayah_nama'];
    }

    protected function exampleRow(): array
    {
        return ['KUB Santo Yosef', 'Wilayah Santo Petrus'];
    }

    protected function columnNotes(): array
    {
        return [
            'nama'         => 'Nama KUB. WAJIB.',
            'wilayah_nama' => 'Nama wilayah induk (harus sudah ada di sistem). WAJIB.',
        ];
    }
}
