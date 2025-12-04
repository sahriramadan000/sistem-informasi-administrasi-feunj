<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LetterPurpose;

class LetterPurposeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $letterPurposes = [
            [
                'name' => 'Legalisir Ijazah',
                'description' => 'Keperluan untuk legalisir ijazah mahasiswa',
                'is_active' => true,
            ],
            [
                'name' => 'Legalisir Transkrip Nilai',
                'description' => 'Keperluan untuk legalisir transkrip nilai mahasiswa',
                'is_active' => true,
            ],
            [
                'name' => 'Surat Keterangan Aktif Kuliah',
                'description' => 'Keperluan untuk surat keterangan aktif kuliah',
                'is_active' => true,
            ],
            [
                'name' => 'Surat Keterangan Lulus',
                'description' => 'Keperluan untuk surat keterangan lulus',
                'is_active' => true,
            ],
            [
                'name' => 'Permohonan Izin Penelitian',
                'description' => 'Keperluan untuk permohonan izin penelitian mahasiswa',
                'is_active' => true,
            ],
            [
                'name' => 'Permohonan Izin PKL/Magang',
                'description' => 'Keperluan untuk permohonan izin PKL atau magang',
                'is_active' => true,
            ],
            [
                'name' => 'Surat Rekomendasi Beasiswa',
                'description' => 'Keperluan untuk surat rekomendasi beasiswa',
                'is_active' => true,
            ],
            [
                'name' => 'Surat Pengantar KRS',
                'description' => 'Keperluan untuk surat pengantar KRS',
                'is_active' => true,
            ],
        ];

        foreach ($letterPurposes as $purpose) {
            LetterPurpose::create($purpose);
        }
    }
}
