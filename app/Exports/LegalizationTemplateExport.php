<?php

namespace App\Exports;

use App\Models\EducationLevel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LegalizationTemplateExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths, \Maatwebsite\Excel\Concerns\WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Return dummy data/contoh untuk template
        return collect([
            [
                '2024-03-15',
                'Budi Santoso',
                '2023',
                'Sarjana/S1',
                '5',
            ],
            [
                '2024-03-15',
                'Siti Aminah',
                '2022',
                'Magister/S2',
                '3',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'tanggal_transaksi',
            'nama_alumni',
            'tahun_lulus',
            'nama_jenjang',
            'jumlah_lembar',
        ];
    }

    public function title(): string
    {
        return 'Template Import Legalisir';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4F46E5'] // Indigo 600
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // tanggal_transaksi
            'B' => 35, // nama_alumni
            'C' => 15, // tahun_lulus
            'D' => 30, // nama_jenjang
            'E' => 18, // jumlah_lembar
        ];
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function(\Maatwebsite\Excel\Events\AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Ambil semua nama jenjang dari database
                $educationLevels = \App\Models\EducationLevel::pluck('name')->toArray();
                
                // Gabungkan menjadi string dengan koma. Wajib diapit tanda kutip ganda untuk list statis di Excel.
                $optionsList = '"' . implode(',', $educationLevels) . '"';
                
                // Buat Data Validation dropdown untuk Kolom D (nama_jenjang)
                $validation = $sheet->getCell('D2')->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input Error');
                $validation->setError('Pilih nama jenjang dari dropdown yang tersedia.');
                $validation->setPromptTitle('Pilih Jenjang');
                $validation->setPrompt('Silakan pilih jenjang pendidikan dari daftar dropdown.');
                $validation->setFormula1($optionsList);

                // Terapkan validasi ini ke baris 2 sampai 1000 pada Kolom D
                for ($i = 2; $i <= 1000; $i++) {
                    $sheet->getCell('D'.$i)->setDataValidation(clone $validation);
                }
            },
        ];
    }
}
