<?php

namespace App\Exports;

use App\Exports\Sheets\LegalizationDetailSheet;
use App\Exports\Sheets\LegalizationSummarySheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LegalizationReconciliationExport implements WithMultipleSheets
{
    protected array $filters;
    protected string $periodLabel;
    protected string $levelLabel;
    protected string $exportedBy;

    public function __construct(array $filters, string $periodLabel, string $levelLabel, string $exportedBy)
    {
        $this->filters = $filters;
        $this->periodLabel = $periodLabel;
        $this->levelLabel = $levelLabel;
        $this->exportedBy = $exportedBy;
    }

    public function sheets(): array
    {
        return [
            new LegalizationDetailSheet(
                $this->filters,
                $this->periodLabel,
                $this->levelLabel,
                $this->exportedBy
            ),
            new LegalizationSummarySheet(
                $this->filters,
                $this->periodLabel,
                $this->levelLabel
            ),
        ];
    }
}

