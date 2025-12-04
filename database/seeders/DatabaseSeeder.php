<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ClassificationLetter;
use App\Models\LetterType;
use App\Models\Signatory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder untuk data awal sistem
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // // Buat user admin default
        // User::create([
        //     'name' => 'Administrator',
        //     'email' => 'admin@feb.unj.ac.id',
        //     'username' => 'admin',
        //     'password' => Hash::make('password123'), // Ganti di production
        //     'role' => 'admin',
        //     'is_active' => true,
        // ]);

        // // Buat user operator
        // User::create([
        //     'name' => 'Operator Surat',
        //     'email' => 'operator@feb.unj.ac.id',
        //     'username' => 'operator',
        //     'password' => Hash::make('password123'), // Ganti di production
        //     'role' => 'operator',
        //     'is_active' => true,
        // ]);

        // // Buat user viewer
        // User::create([
        //     'name' => 'Viewer',
        //     'email' => 'viewer@feb.unj.ac.id',
        //     'username' => 'viewer',
        //     'password' => Hash::make('password123'), // Ganti di production
        //     'role' => 'viewer',
        //     'is_active' => true,
        // ]);

        // // Buat data klasifikasi surat
        // $classifications = [
        //     ['code' => 'VAL-ZJ', 'name' => 'Validasi Berkas Ijazah', 'description' => 'Surat validasi berkas ijazah alumni'],
        //     ['code' => 'SKL-MS', 'name' => 'Surat Keterangan Lulus', 'description' => 'Surat keterangan telah lulus'],
        //     ['code' => 'KPT-MK', 'name' => 'Keterangan Pindah Kuliah', 'description' => 'Surat keterangan pindah kuliah'],
        //     ['code' => 'PKL-MB', 'name' => 'Perizinan Kegiatan Mahasiswa', 'description' => 'Surat izin kegiatan mahasiswa'],
        //     ['code' => 'RGS-PR', 'name' => 'Registrasi Ulang', 'description' => 'Surat registrasi ulang mahasiswa'],
        // ];

        // foreach ($classifications as $classification) {
        //     ClassificationLetter::create([
        //         'code' => $classification['code'],
        //         'name' => $classification['name'],
        //         'description' => $classification['description'],
        //         'is_active' => true,
        //     ]);
        // }

        // // Buat data jenis surat
        // $letterTypes = [
        //     ['code' => 'ST', 'name' => 'Surat Tugas', 'description' => 'Surat tugas untuk kegiatan tertentu', 'requires_subject' => false],
        //     ['code' => 'SK', 'name' => 'Surat Keterangan', 'description' => 'Surat keterangan berbagai keperluan', 'requires_subject' => true],
        //     ['code' => 'SP', 'name' => 'Surat Pengantar', 'description' => 'Surat pengantar untuk instansi lain', 'requires_subject' => true],
        //     ['code' => 'SR', 'name' => 'Surat Rekomendasi', 'description' => 'Surat rekomendasi', 'requires_subject' => true],
        //     ['code' => 'SU', 'name' => 'Surat Undangan', 'description' => 'Surat undangan resmi', 'requires_subject' => false],
        // ];

        // foreach ($letterTypes as $letterType) {
        //     LetterType::create([
        //         'code' => $letterType['code'],
        //         'name' => $letterType['name'],
        //         'description' => $letterType['description'],
        //         'requires_subject' => $letterType['requires_subject'],
        //         'is_active' => true,
        //     ]);
        // }

        // // Buat data penandatangan
        // $signatories = [
        //     [
        //         'code' => 'DEP-XTY',
        //         'name' => 'Dr. Ahmad Wijaya, S.E., M.M.',
        //         'position' => 'Dekan FEB UNJ',
        //         'nip' => '197501012000031001',
        //     ],
        //     [
        //         'code' => 'WD1-ABC',
        //         'name' => 'Dr. Siti Nurhaliza, S.E., M.Si.',
        //         'position' => 'Wakil Dekan Bidang Akademik',
        //         'nip' => '198002012005012001',
        //     ],
        //     [
        //         'code' => 'WD2-DEF',
        //         'name' => 'Dr. Budi Santoso, S.E., M.Ak.',
        //         'position' => 'Wakil Dekan Bidang Administrasi Umum dan Keuangan',
        //         'nip' => '198203012006011001',
        //     ],
        //     [
        //         'code' => 'WD3-GHI',
        //         'name' => 'Dr. Diana Putri, S.E., M.Com.',
        //         'position' => 'Wakil Dekan Bidang Kemahasiswaan, Alumni dan Kerjasama',
        //         'nip' => '198404012007012001',
        //     ],
        // ];

        // foreach ($signatories as $signatory) {
        //     Signatory::create([
        //         'code' => $signatory['code'],
        //         'name' => $signatory['name'],
        //         'position' => $signatory['position'],
        //         'nip' => $signatory['nip'],
        //         'is_active' => true,
        //     ]);
        // }

        // // Seed letter purposes
        // $this->call(LetterPurposeSeeder::class);

        $this->command->info('Database seeding completed successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin / password123');
        $this->command->info('Operator: operator / password123');
        $this->command->info('Viewer: viewer / password123');
        $this->command->warn('IMPORTANT: Change default passwords in production!');
    }
}
