<?php

namespace App\Exports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Base template export: Sheet 1 = data template, Sheet 2 = petunjuk pengisian.
 */
abstract class BaseTemplateExport implements WithMultipleSheets
{
    abstract protected function headings(): array;
    abstract protected function exampleRow(): array;
    abstract protected function entityName(): string;
    abstract protected function columnNotes(): array;

    public function sheets(): array
    {
        return [
            new DataSheet($this->headings(), $this->exampleRow()),
            new PetunjukSheet($this->entityName(), $this->headings(), $this->columnNotes()),
        ];
    }
}

// ---------------------------------------------------------------------------
// Sheet 1: Data Template
// ---------------------------------------------------------------------------
class DataSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function __construct(
        private array $headings,
        private array $exampleRow,
    ) {}

    public function array(): array
    {
        return [$this->exampleRow];
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function styles(Worksheet $sheet): array
    {
        // Header row style
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->headings));

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Example row style (yellow highlight)
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FEF08A']],
            'font' => ['italic' => true, 'color' => ['rgb' => '78716C']],
        ]);

        // Note on row 2
        $sheet->getComment("A2")->getText()->createTextRun(
            "Baris ini adalah CONTOH. Hapus dan ganti dengan data asli Anda."
        );

        return [];
    }

    public function columnWidths(): array
    {
        $widths = [];
        foreach (range(1, count($this->headings)) as $i) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $widths[$col] = 22;
        }
        return $widths;
    }
}

// ---------------------------------------------------------------------------
// Sheet 2: Petunjuk Pengisian
// ---------------------------------------------------------------------------
class PetunjukSheet implements FromArray, WithStyles, WithColumnWidths
{
    public function __construct(
        private string $entityName,
        private array  $headings,
        private array  $columnNotes,
    ) {}

    public function array(): array
    {
        $rows = [
            ["PETUNJUK PENGISIAN — {$this->entityName}"],
            [],
            ["Kolom", "Keterangan", "Wajib?"],
        ];

        foreach ($this->headings as $heading) {
            $rows[] = [
                $heading,
                $this->columnNotes[$heading] ?? '-',
                in_array($heading, $this->requiredColumns()) ? 'Ya' : 'Tidak',
            ];
        }

        $rows[] = [];
        $rows[] = ["⚠️  CATATAN PENTING:"];
        $rows[] = ["• Hapus baris contoh (baris 2 di Sheet Data) sebelum mengimport."];
        $rows[] = ["• Format tanggal: YYYY-MM-DD (contoh: 2000-01-25) atau DD/MM/YYYY (contoh: 25/01/2000)."];
        $rows[] = ["• Pastikan data parent (Wilayah/KUB/Keluarga/Umat) sudah diimport terlebih dahulu."];
        $rows[] = ["• Jika ada data duplikat, import akan dihentikan dan ditampilkan pesan error."];

        return $rows;
    }

    protected function requiredColumns(): array
    {
        return []; // Override di subclass jika perlu
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E40AF']],
        ]);
        $sheet->getStyle('A3:C3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2563EB']],
        ]);
        return [];
    }

    public function columnWidths(): array
    {
        return ['A' => 30, 'B' => 55, 'C' => 10];
    }
}
