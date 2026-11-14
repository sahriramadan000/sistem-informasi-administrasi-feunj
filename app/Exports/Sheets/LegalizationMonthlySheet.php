<?php

namespace App\Exports\Sheets;

use App\Models\Legalization;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LegalizationMonthlySheet implements FromCollection, WithTitle, WithColumnWidths, WithEvents
{
    protected array $filters;
    protected string $periodLabel;
    protected string $levelLabel;
    protected int $rowCount = 0;

    public function __construct(array $filters, string $periodLabel, string $levelLabel)
    {
        $this->filters = $filters;
        $this->periodLabel = $periodLabel;
        $this->levelLabel = $levelLabel;
    }

    public function title(): string
    {
        return 'Rekap Bulanan';
    }

    public function collection(): Collection
    {
        $query = Legalization::query();

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where('alumni_name', 'like', "%{$search}%");
        }

        $rangeStart = null;
        $rangeEnd = null;

        if (!empty($this->filters['date_range'])) {
            $separator = strpos($this->filters['date_range'], ' to ') !== false ? ' to ' : ' - ';
            $dates = explode($separator, $this->filters['date_range']);
            if (count($dates) === 2) {
                $startStr = trim($dates[0]);
                $endStr = trim($dates[1]);
                $query->whereBetween('date', [$startStr, $endStr]);
                $rangeStart = Carbon::parse($startStr)->startOfMonth();
                $rangeEnd = Carbon::parse($endStr)->startOfMonth();
            } elseif (count($dates) === 1) {
                $singleStr = trim($dates[0]);
                $query->whereDate('date', $singleStr);
                $rangeStart = Carbon::parse($singleStr)->startOfMonth();
                $rangeEnd = $rangeStart->copy();
            }
        }

        if (!empty($this->filters['education_level_id'])) {
            $query->where('education_level_id', $this->filters['education_level_id']);
        }

        $grouped = $query->selectRaw('YEAR(date) as yr, MONTH(date) as mo, COUNT(*) as tx_count, SUM(page_count) as total_pages, SUM(total_price) as total_amount')
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->orderByRaw('YEAR(date) ASC, MONTH(date) ASC')
            ->get()
            ->keyBy(function ($row) {
                return sprintf('%04d-%02d', $row->yr, $row->mo);
            });

        $months = [];
        if ($rangeStart && $rangeEnd) {
            if ($rangeStart->gt($rangeEnd)) {
                [$rangeStart, $rangeEnd] = [$rangeEnd, $rangeStart];
            }
            foreach (CarbonPeriod::create($rangeStart, '1 month', $rangeEnd) as $m) {
                $months[] = $m->format('Y-m');
            }
        } elseif ($grouped->isNotEmpty()) {
            $keys = $grouped->keys()->sort()->values();
            $first = Carbon::createFromFormat('Y-m', $keys->first())->startOfMonth();
            $last = Carbon::createFromFormat('Y-m', $keys->last())->startOfMonth();
            foreach (CarbonPeriod::create($first, '1 month', $last) as $m) {
                $months[] = $m->format('Y-m');
            }
        } else {
            $months[] = Carbon::now()->format('Y-m');
        }

        $this->rowCount = count($months);

        $rows = collect([
            ['REKAPITULASI PENDAPATAN LEGALISIR PER BULAN'],
            ['FAKULTAS EKONOMI DAN BISNIS - UNIVERSITAS NEGERI JAKARTA'],
            ["Periode: {$this->periodLabel}   |   Jenjang: {$this->levelLabel}"],
            [''],
            [
                'No',
                'Bulan & Tahun',
                'Jumlah Transaksi',
                'Total Lembar',
                'Total Pendapatan (Rp)',
                'Persentase (%)',
            ],
        ]);

        $firstDataRow = 6;
        $totalRow = $firstDataRow + max($this->rowCount, 1);

        foreach ($months as $index => $ym) {
            $stat = $grouped->get($ym);
            $currentRow = $firstDataRow + $index;
            $monthLabel = Carbon::createFromFormat('Y-m', $ym)->locale('id')->translatedFormat('F Y');

            $rows->push([
                $index + 1,
                $monthLabel,
                $stat ? (int) $stat->tx_count : 0,
                $stat ? (int) $stat->total_pages : 0,
                $stat ? (int) $stat->total_amount : 0,
                "=IF(\$E\${$totalRow}>0, E{$currentRow}/\$E\${$totalRow}, 0)",
            ]);
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 26,
            'C' => 20,
            'D' => 18,
            'E' => 26,
            'F' => 16,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $headerRow = 5;
                $firstDataRow = 6;
                $lastDataRow = $this->rowCount > 0 ? ($firstDataRow + $this->rowCount - 1) : $firstDataRow;
                $totalRow = $lastDataRow + 1;

                // 1. Styling Kop
                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');
                $sheet->mergeCells('A3:F3');

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF0F172A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10.5, 'color' => ['argb' => 'FFEA580C']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 9.5, 'color' => ['argb' => 'FF64748B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(22);
                $sheet->getRowDimension(2)->setRowHeight(18);
                $sheet->getRowDimension(3)->setRowHeight(18);
                $sheet->getRowDimension(4)->setRowHeight(10);

                // 2. Header Tabel
                $sheet->getRowDimension($headerRow)->setRowHeight(28);
                $sheet->getStyle("A{$headerRow}:F{$headerRow}")->applyFromArray([
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

                // 3. Baris Data
                if ($this->rowCount > 0) {
                    for ($r = $firstDataRow; $r <= $lastDataRow; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(22);
                        if (($r - $firstDataRow) % 2 === 1) {
                            $sheet->getStyle("A{$r}:F{$r}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFF8FAFC');
                        }
                    }

                    $sheet->getStyle("A{$firstDataRow}:F{$lastDataRow}")->applyFromArray([
                        'font' => ['size' => 10, 'color' => ['argb' => 'FF1E293B']],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']],
                        ],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $sheet->getStyle("A{$firstDataRow}:A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B{$firstDataRow}:B{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("C{$firstDataRow}:E{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("F{$firstDataRow}:F{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("C{$firstDataRow}:D{$lastDataRow}")->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("E{$firstDataRow}:E{$lastDataRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
                    $sheet->getStyle("E{$firstDataRow}:E{$lastDataRow}")->getFont()->setBold(true);
                    $sheet->getStyle("F{$firstDataRow}:F{$lastDataRow}")->getNumberFormat()->setFormatCode('0.0%');
                }

                // 4. Grand Total Row
                $sheet->mergeCells("A{$totalRow}:B{$totalRow}");
                $sheet->setCellValue("A{$totalRow}", 'TOTAL KESELURUHAN');
                $sheet->setCellValue("C{$totalRow}", $this->rowCount > 0 ? "=SUM(C{$firstDataRow}:C{$lastDataRow})" : 0);
                $sheet->setCellValue("D{$totalRow}", $this->rowCount > 0 ? "=SUM(D{$firstDataRow}:D{$lastDataRow})" : 0);
                $sheet->setCellValue("E{$totalRow}", $this->rowCount > 0 ? "=SUM(E{$firstDataRow}:E{$lastDataRow})" : 0);
                $sheet->setCellValue("F{$totalRow}", $this->rowCount > 0 ? "=SUM(F{$firstDataRow}:F{$lastDataRow})" : 0);

                $sheet->getRowDimension($totalRow)->setRowHeight(26);
                $sheet->getStyle("A{$totalRow}:F{$totalRow}")->applyFromArray([
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
                $sheet->getStyle("C{$totalRow}:D{$totalRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberFormat' => ['formatCode' => '#,##0'],
                ]);
                $sheet->getStyle("E{$totalRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberFormat' => ['formatCode' => '"Rp "#,##0'],
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF047857']],
                ]);
                $sheet->getStyle("F{$totalRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'numberFormat' => ['formatCode' => '0.0%'],
                ]);

                $sheet->freezePane("A{$firstDataRow}");
            },
        ];
    }
}

