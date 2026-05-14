<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LetterTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            // Contoh baris data untuk panduan user
            [
                'B/001/UN39.DEP-XYT/VAL-ZJ/2026', // nomor_surat
                '2026-05-14', // tanggal_surat
                'internal', // sasaran_surat
                'B', // klasifikasi_keamanan
                1, // kode_penandatangan (Gunakan ID Angka)
                'AK', // kode_klasifikasi_surat (Contoh)
                'ST', // kode_jenis_surat (Contoh)
                'Tugas Kuliah', // nama_keperluan (Opsional)
                'Undangan Rapat', // perihal
                'Rektorat', // tujuan
                'Budi Santoso', // nama_mahasiswa
                'final', // status
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'nomor_surat',
            'tanggal_surat',
            'sasaran_surat',
            'klasifikasi_keamanan',
            'kode_penandatangan',
            'kode_klasifikasi_surat',
            'kode_jenis_surat',
            'nama_keperluan',
            'perihal',
            'tujuan',
            'nama_mahasiswa',
            'status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Styling baris pertama (header) menjadi bold
            1    => ['font' => ['bold' => true]],
        ];
    }
}
