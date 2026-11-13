<?php

namespace App\Exports\Sheets;

use App\Models\Legalization;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LegalizationDetailSheet implements FromCollection, WithTitle, WithColumnWidths, WithEvents
{
    protected array $filters;
    protected string $periodLabel;
    protected string $levelLabel;
    protected string $exportedBy;
    protected int $rowCount = 0;

    public function __construct(array $filters, string $periodLabel, string $levelLabel, string $exportedBy)
    {
        $this->filters = $filters;
        $this->periodLabel = $periodLabel;
        $this->levelLabel = $levelLabel;
        $this->exportedBy = $exportedBy;
    }

    public function title(): string
    {
        return 'Rincian Transaksi';
    }

    public function collection(): Collection
    {
        $query = Legalization::with(['educationLevel', 'creator'])
            ->orderBy('date', 'asc')
            ->orderBy('running_number', 'asc');

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where('alumni_name', 'like', "%{$search}%");
        }

        if (!empty($this->filters['date_range'])) {
            $separator = strpos($this->filters['date_range'], ' to ') !== false ? ' to ' : ' - ';
            $dates = explode($separator, $this->filters['date_range']);
            if (count($dates) === 2) {
                $query->whereBetween('date', [trim($dates[0]), trim($dates[1])]);
            } elseif (count($dates) === 1) {
                $query->whereDate('date', trim($dates[0]));
            }
        }

        if (!empty($this->filters['education_level_id'])) {
            $query->where('education_level_id', $this->filters['education_level_id']);
        }

        $records = $query->get();
        $this->rowCount = $records->count();

        // Baris 1-6: Kop, Metadata, dan Summary Box (diatur penuh via AfterSheet)
        $rows = collect([
            ['LAPORAN REKONSILIASI TRANSAKSI LEGALISIR IJAZAH & TRANSKRIP'],
            ['FAKULTAS EKONOMI DAN BISNIS - UNIVERSITAS NEGERI JAKARTA'],
            ["Periode: {$this->periodLabel}   |   Jenjang: {$this->levelLabel}   |   Dicetak: " . now()->format('d/m/Y H:i') . " oleh {$this->exportedBy}"],
            [''],
            ['Total Transaksi', '', '', 'Total Lembar', '', '', 'Total Nominal Rekon', '', '', '', ''],
            [''],
            // Baris 7: Header Tabel Utama
            [
                'No',
                'No. Urut',
                'Tanggal',
                'Nama Alumni',
                'Tahun Lulus',
                'Jenjang Pendidikan',
                'Tarif / Lembar',
                'Jumlah Lembar',
                'Total Nominal',
                'Petugas Input',
                'Catatan / Cek Rekon',
            ],
        ]);

        // Baris 8 dst: Data Transaksi
        foreach ($records as $index => $item) {
            $paddedNumber = '#' . str_pad((string) $item->running_number, 3, '0', STR_PAD_LEFT);
            $pricePerPage = $item->educationLevel ? (int) $item->educationLevel->price_per_page : (
                $item->page_count > 0 ? (int) round($item->total_price / $item->page_count) : 0
            );

            $rows->push([
                $index + 1,
                $paddedNumber,
                $item->date ? $item->date->format('d/m/Y') : '-',
                $item->alumni_name,
                $item->graduation_year,
                $item->educationLevel->name ?? '-',
                $pricePerPage,
                (int) $item->page_count,
                (int) $item->total_price,
                $item->creator->name ?? 'Sistem',
                '',
            ]);
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 13,
            'C' => 14,
            'D' => 30,
            'E' => 13,
            'F' => 22,
            'G' => 17,
            'H' => 15,
            'I' => 20,
            'J' => 20,
            'K' => 24,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $headerRow = 7;
                $firstDataRow = 8;
                $lastDataRow = $this->rowCount > 0 ? ($firstDataRow + $this->rowCount - 1) : $firstDataRow;
                $totalRow = $lastDataRow + 1;

                // 1. Styling Kop Laporan (Baris 1-3)
                $sheet->mergeCells('A1:K1');
                $sheet->mergeCells('A2:K2');
                $sheet->mergeCells('A3:K3');

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF0F172A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFEA580C']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 9.5, 'color' => ['argb' => 'FF64748B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(24);
                $sheet->getRowDimension(2)->setRowHeight(18);
                $sheet->getRowDimension(3)->setRowHeight(18);
                $sheet->getRowDimension(4)->setRowHeight(8);

                // 2. Styling KPI Summary Box (Baris 5)
                $sheet->mergeCells('A5:B5');
                $sheet->mergeCells('D5:E5');
                $sheet->mergeCells('G5:H5');
                $sheet->mergeCells('I5:K5');

                $sheet->setCellValue('A5', 'Total Transaksi:');
                $sheet->setCellValue('C5', $this->rowCount > 0 ? "=COUNTA(B{$firstDataRow}:B{$lastDataRow})" : 0);

                $sheet->setCellValue('D5', 'Total Lembar:');
                $sheet->setCellValue('F5', $this->rowCount > 0 ? "=SUM(H{$firstDataRow}:H{$lastDataRow})" : 0);

                $sheet->setCellValue('G5', 'Total Nominal Rekon:');
                $sheet->setCellValue('I5', $this->rowCount > 0 ? "=SUM(I{$firstDataRow}:I{$lastDataRow})" : 0);

                $sheet->getRowDimension(5)->setRowHeight(26);
                $sheet->getRowDimension(6)->setRowHeight(10);

                $sheet->getStyle('A5:K5')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8FAFC']],
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']],
                        'inside' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']],
                    ],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF475569');
                $sheet->getStyle('D5')->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF475569');
                $sheet->getStyle('G5')->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF475569');

                $sheet->getStyle('C5')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF0F172A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'numberFormat' => ['formatCode' => '#,##0'],
                ]);
                $sheet->getStyle('F5')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF0F172A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'numberFormat' => ['formatCode' => '#,##0" lbr"'],
                ]);
                $sheet->getStyle('I5')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11.5, 'color' => ['argb' => 'FF047857']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFECFDF5']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberFormat' => ['formatCode' => '"Rp "#,##0'],
                ]);

                // 3. Header Tabel Utama (Baris 7)
                $sheet->getRowDimension($headerRow)->setRowHeight(28);
                $sheet->getStyle("A{$headerRow}:K{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E293B']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF334155']],
                    ],
                ]);

                // 4. Baris Data
                if ($this->rowCount > 0) {
                    for ($r = $firstDataRow; $r <= $lastDataRow; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(21);
                        if (($r - $firstDataRow) % 2 === 1) {
                            $sheet->getStyle("A{$r}:K{$r}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFF8FAFC');
                        }
                    }

                    $sheet->getStyle("A{$firstDataRow}:K{$lastDataRow}")->applyFromArray([
                        'font' => ['size' => 10, 'color' => ['argb' => 'FF1E293B']],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']],
                        ],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    // Alignment per kolom
                    $sheet->getStyle("A{$firstDataRow}:C{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E{$firstDataRow}:E{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("D{$firstDataRow}:D{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("F{$firstDataRow}:F{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("G{$firstDataRow}:I{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("J{$firstDataRow}:K{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    // Number Formatting
                    $sheet->getStyle("G{$firstDataRow}:G{$lastDataRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
                    $sheet->getStyle("H{$firstDataRow}:H{$lastDataRow}")->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("I{$firstDataRow}:I{$lastDataRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
                    $sheet->getStyle("I{$firstDataRow}:I{$lastDataRow}")->getFont()->setBold(true);
                } else {
                    $sheet->mergeCells("A{$firstDataRow}:K{$firstDataRow}");
                    $sheet->setCellValue("A{$firstDataRow}", 'Tidak ada data transaksi legalisir pada periode atau filter yang dipilih.');
                    $sheet->getRowDimension($firstDataRow)->setRowHeight(30);
                    $sheet->getStyle("A{$firstDataRow}:K{$firstDataRow}")->applyFromArray([
                        'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF64748B']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']]],
                    ]);
                }

                // 5. Grand Total Footer Row
                $sheet->mergeCells("A{$totalRow}:G{$totalRow}");
                $sheet->setCellValue("A{$totalRow}", 'GRAND TOTAL REKONSILIASI');
                $sheet->setCellValue("H{$totalRow}", $this->rowCount > 0 ? "=SUM(H{$firstDataRow}:H{$lastDataRow})" : 0);
                $sheet->setCellValue("I{$totalRow}", $this->rowCount > 0 ? "=SUM(I{$firstDataRow}:I{$lastDataRow})" : 0);

                $sheet->getRowDimension($totalRow)->setRowHeight(26);
                $sheet->getStyle("A{$totalRow}:K{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10.5, 'color' => ['argb' => 'FF0F172A']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF1F5F9']],
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF334155']],
                        'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => 'FF334155']],
                        'left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']],
                        'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']],
                        'inside' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']],
                    ],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("H{$totalRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberFormat' => ['formatCode' => '#,##0'],
                ]);
                $sheet->getStyle("I{$totalRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberFormat' => ['formatCode' => '"Rp "#,##0'],
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF047857']],
                ]);

                // 6. Blok Tanda Tangan untuk Kebutuhan Cetak Rekon Fisik
                $signStartRow = $totalRow + 3;
                $sheet->mergeCells("B{$signStartRow}:D{$signStartRow}");
                $sheet->setCellValue("B{$signStartRow}", 'Verifikator / Tim Rekon Keuangan,');
                $sheet->mergeCells("B" . ($signStartRow + 1) . ":D" . ($signStartRow + 1));
                $sheet->setCellValue("B" . ($signStartRow + 1), 'Fakultas Ekonomi dan Bisnis UNJ');

                $sheet->mergeCells("I{$signStartRow}:K{$signStartRow}");
                $sheet->setCellValue("I{$signStartRow}", 'Jakarta, ' . now()->translatedFormat('d F Y'));
                $sheet->mergeCells("I" . ($signStartRow + 1) . ":K" . ($signStartRow + 1));
                $sheet->setCellValue("I" . ($signStartRow + 1), 'Petugas Layanan Legalisir,');

                $signNameRow = $signStartRow + 5;
                $sheet->mergeCells("B{$signNameRow}:D{$signNameRow}");
                $sheet->setCellValue("B{$signNameRow}", '( ___________________________ )');

                $sheet->mergeCells("I{$signNameRow}:K{$signNameRow}");
                $sheet->setCellValue("I{$signNameRow}", $this->exportedBy);

                $sheet->getStyle("B{$signStartRow}:K{$signNameRow}")->getFont()->setSize(10);
                $sheet->getStyle("B{$signNameRow}:K{$signNameRow}")->getFont()->setBold(true);

                // 7. Freeze Pane & AutoFilter
                $sheet->freezePane("A{$firstDataRow}");
                if ($this->rowCount > 0) {
                    $sheet->setAutoFilter("A{$headerRow}:K{$lastDataRow}");
                }

                // Page Setup agar rapi jika dicetak landscape
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
            },
        ];
    }
}

