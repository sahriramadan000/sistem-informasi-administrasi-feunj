<?php

namespace App\Exports\Sheets;

use App\Models\EducationLevel;
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

class LegalizationSummarySheet implements FromCollection, WithTitle, WithColumnWidths, WithEvents
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
        return 'Rekap per Jenjang';
    }

    public function collection(): Collection
    {
        $query = Legalization::query();

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

        $grouped = $query->selectRaw('education_level_id, COUNT(*) as tx_count, SUM(page_count) as total_pages, SUM(total_price) as total_amount')
            ->groupBy('education_level_id')
            ->get()
            ->keyBy('education_level_id');

        $levelsQuery = EducationLevel::withTrashed()->orderBy('name');
        if (!empty($this->filters['education_level_id'])) {
            $levelsQuery->where('id', $this->filters['education_level_id']);
        }
        $levels = $levelsQuery->get()->filter(function ($lvl) use ($grouped) {
            // Tampilkan jenjang yang aktif atau yang memiliki transaksi pada periode ini
            return !$lvl->trashed() || $grouped->has($lvl->id);
        })->values();

        $this->rowCount = $levels->count();

        $rows = collect([
            ['REKAPITULASI PENDAPATAN LEGALISIR PER JENJANG PENDIDIKAN'],
            ['FAKULTAS EKONOMI DAN BISNIS - UNIVERSITAS NEGERI JAKARTA'],
            ["Periode: {$this->periodLabel}   |   Jenjang: {$this->levelLabel}"],
            [''],
            // Baris 5: Header Tabel Rekap
            [
                'No',
                'Jenjang Pendidikan',
                'Tarif Satuan / Lembar',
                'Jumlah Transaksi',
                'Total Lembar',
                'Subtotal Nominal (Rp)',
                'Persentase (%)',
            ],
        ]);

        $firstDataRow = 6;
        $totalRow = $firstDataRow + max($this->rowCount, 1);

        foreach ($levels as $index => $level) {
            $stat = $grouped->get($level->id);
            $currentRow = $firstDataRow + $index;

            $rows->push([
                $index + 1,
                $level->name . ($level->trashed() ? ' (Arsip)' : ''),
                (int) $level->price_per_page,
                $stat ? (int) $stat->tx_count : 0,
                $stat ? (int) $stat->total_pages : 0,
                $stat ? (int) $stat->total_amount : 0,
                "=IF(\$F\${$totalRow}>0, F{$currentRow}/\$F\${$totalRow}, 0)",
            ]);
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 28,
            'C' => 22,
            'D' => 18,
            'E' => 16,
            'F' => 24,
            'G' => 16,
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
                $sheet->mergeCells('A1:G1');
                $sheet->mergeCells('A2:G2');
                $sheet->mergeCells('A3:G3');

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
                $sheet->getStyle("A{$headerRow}:G{$headerRow}")->applyFromArray([
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
                            $sheet->getStyle("A{$r}:G{$r}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFF8FAFC');
                        }
                    }

                    $sheet->getStyle("A{$firstDataRow}:G{$lastDataRow}")->applyFromArray([
                        'font' => ['size' => 10, 'color' => ['argb' => 'FF1E293B']],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']],
                        ],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $sheet->getStyle("A{$firstDataRow}:A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B{$firstDataRow}:B{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("C{$firstDataRow}:F{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("G{$firstDataRow}:G{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("C{$firstDataRow}:C{$lastDataRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
                    $sheet->getStyle("D{$firstDataRow}:E{$lastDataRow}")->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("F{$firstDataRow}:F{$lastDataRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
                    $sheet->getStyle("F{$firstDataRow}:F{$lastDataRow}")->getFont()->setBold(true);
                    $sheet->getStyle("G{$firstDataRow}:G{$lastDataRow}")->getNumberFormat()->setFormatCode('0.0%');
                }

                // 4. Grand Total Row
                $sheet->mergeCells("A{$totalRow}:C{$totalRow}");
                $sheet->setCellValue("A{$totalRow}", 'TOTAL KESELURUHAN');
                $sheet->setCellValue("D{$totalRow}", $this->rowCount > 0 ? "=SUM(D{$firstDataRow}:D{$lastDataRow})" : 0);
                $sheet->setCellValue("E{$totalRow}", $this->rowCount > 0 ? "=SUM(E{$firstDataRow}:E{$lastDataRow})" : 0);
                $sheet->setCellValue("F{$totalRow}", $this->rowCount > 0 ? "=SUM(F{$firstDataRow}:F{$lastDataRow})" : 0);
                $sheet->setCellValue("G{$totalRow}", $this->rowCount > 0 ? "=SUM(G{$firstDataRow}:G{$lastDataRow})" : 0);

                $sheet->getRowDimension($totalRow)->setRowHeight(26);
                $sheet->getStyle("A{$totalRow}:G{$totalRow}")->applyFromArray([
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
                $sheet->getStyle("D{$totalRow}:E{$totalRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberFormat' => ['formatCode' => '#,##0'],
                ]);
                $sheet->getStyle("F{$totalRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberFormat' => ['formatCode' => '"Rp "#,##0'],
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FF047857']],
                ]);
                $sheet->getStyle("G{$totalRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'numberFormat' => ['formatCode' => '0.0%'],
                ]);

                $sheet->freezePane("A{$firstDataRow}");
            },
        ];
    }
}

