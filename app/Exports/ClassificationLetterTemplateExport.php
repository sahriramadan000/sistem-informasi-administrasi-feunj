<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ClassificationLetterTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Return collection with example data
     */
    public function collection()
    {
        return collect([
            [
                'UM',
                'Surat Umum',
                'Surat yang bersifat umum dan tidak termasuk kategori khusus',
                'Aktif'
            ],
            [
                'KP',
                'Surat Keputusan',
                'Surat keputusan resmi dari pimpinan',
                'Aktif'
            ],
            [
                'PG',
                'Surat Pengumuman',
                'Surat pengumuman untuk mahasiswa dan dosen',
                'Nonaktif'
            ],
        ]);
    }

    /**
     * Define headings
     */
    public function headings(): array
    {
        return [
            'Kode',
            'Nama',
            'Deskripsi',
            'Status'
        ];
    }

    /**
     * Apply styles to cells
     */
    public function styles(Worksheet $sheet)
    {
        // Style header row (data columns)
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FF4D00'], // Blue-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Add borders to data columns
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:D{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Style example rows with light background
        $sheet->getStyle('A2:D4')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F4F6'], // Gray-100
            ],
        ]);

        return $sheet;
    }

    /**
     * Define column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Kode
            'B' => 30,  // Nama
            'C' => 50,  // Deskripsi
            'D' => 12,  // Status
        ];
    }
}
