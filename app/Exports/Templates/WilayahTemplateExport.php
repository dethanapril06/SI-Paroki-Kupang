<?php

namespace App\Exports\Templates;

class WilayahTemplateExport extends BaseTemplateExport
{
    protected function entityName(): string { return 'Wilayah'; }

    protected function headings(): array
    {
        return ['nama', 'paroki_nama', 'kuasi_nama', 'stasi_nama'];
    }

    protected function exampleRow(): array
    {
        return ['Wilayah Santo Petrus', 'Paroki Kristus Raja', '', ''];
    }

    protected function columnNotes(): array
    {
        return [
            'nama'        => 'Nama wilayah. WAJIB.',
            'paroki_nama' => 'Nama paroki induk (sesuai data di sistem). Isi salah satu dari paroki/kuasi/stasi.',
            'kuasi_nama'  => 'Nama kuasi paroki induk. Isi jika wilayah berada di bawah kuasi.',
            'stasi_nama'  => 'Nama stasi induk. Isi jika wilayah berada di bawah stasi.',
        ];
    }
}
