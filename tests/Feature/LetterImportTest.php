<?php

namespace Tests\Feature;

use App\Imports\LettersImport;
use App\Models\Letter;
use App\Models\Signatory;
use App\Models\LetterType;
use App\Models\ClassificationLetter;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class LetterImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    /**
     * Setup test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /**
     * Test 1: Import dengan hanya required fields - MINIMAL DATA
     * 
     * Required fields:
     * - nomor_surat
     * - tanggal_surat
     * - kode_penandatangan
     * - kode_klasifikasi_surat
     * - kode_jenis_surat
     * - perihal
     * - tujuan
     * - status
     * 
     * Optional fields boleh kosong:
     * - sasaran_surat (default: internal)
     * - klasifikasi_keamanan (default: B)
     * - nama_keperluan
     * - nama_mahasiswa
     */
    public function test_import_with_only_required_fields_succeeds()
    {
        // Create minimal test data
        $this->createTestData();

        // Create a fake Excel file with minimal data
        $file = UploadedFile::fake()->createWithContent(
            'test_minimal.xlsx',
            $this->createMinimalTestExcelContent()
        );

        // Call import endpoint
        $response = $this->post(route('letters.import'), [
            'file_excel' => $file
        ]);

        // Assert redirect to letters.index with success message
        $response->assertRedirect(route('letters.index'));
        $response->assertSessionHas('success');
        $response->assertSessionMissing('import_errors');

        // Assert letter was created in database
        $this->assertDatabaseHas('letters', [
            'letter_number' => 'B/001/2026',
            'subject' => 'Test Subject',
            'recipient' => 'Test Recipient',
            'status' => 'draft',
        ]);
    }

    /**
     * Test 2: Import gagal - missing required field (nomor_surat)
     */
    public function test_import_fails_when_nomor_surat_is_missing()
    {
        $this->createTestData();

        // Reset importer
        LettersImport::resetAll();
        $importer = new LettersImport();

        // Simulate a row with missing nomor_surat
        $row = [
            'nomor_surat' => '', // Empty - required field
            'tanggal_surat' => '2026-05-14',
            'kode_penandatangan' => 1,
            'kode_klasifikasi_surat' => 'BU',
            'kode_jenis_surat' => 'ST',
            'perihal' => 'Test',
            'tujuan' => 'Test',
            'status' => 'draft',
            'sasaran_surat' => null,
            'klasifikasi_keamanan' => null,
            'nama_keperluan' => null,
            'nama_mahasiswa' => null,
        ];

        // Process row
        $importer->model($row);

        // Assert error was collected
        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertEquals(1, count($errors));
        $this->assertEquals('nomor_surat', $errors[0]['field']);
        $this->assertStringContainsString('wajib diisi', $errors[0]['message']);
    }

    /**
     * Test 3: Import gagal - missing required field (tanggal_surat)
     */
    public function test_import_fails_when_tanggal_surat_is_missing()
    {
        $this->createTestData();

        LettersImport::resetAll();
        $importer = new LettersImport();

        $row = [
            'nomor_surat' => 'B/001/2026',
            'tanggal_surat' => '', // Empty - required field
            'kode_penandatangan' => 1,
            'kode_klasifikasi_surat' => 'BU',
            'kode_jenis_surat' => 'ST',
            'perihal' => 'Test',
            'tujuan' => 'Test',
            'status' => 'draft',
            'sasaran_surat' => null,
            'klasifikasi_keamanan' => null,
            'nama_keperluan' => null,
            'nama_mahasiswa' => null,
        ];

        $importer->model($row);

        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertEquals('tanggal_surat', $errors[0]['field']);
    }

    /**
     * Test 4: Import gagal - invalid date format
     */
    public function test_import_fails_when_tanggal_surat_has_invalid_format()
    {
        $this->createTestData();

        LettersImport::resetAll();
        $importer = new LettersImport();

        $row = [
            'nomor_surat' => 'B/001/2026',
            'tanggal_surat' => 'invalid-date-format', // Invalid format
            'kode_penandatangan' => 1,
            'kode_klasifikasi_surat' => 'BU',
            'kode_jenis_surat' => 'ST',
            'perihal' => 'Test',
            'tujuan' => 'Test',
            'status' => 'draft',
            'sasaran_surat' => null,
            'klasifikasi_keamanan' => null,
            'nama_keperluan' => null,
            'nama_mahasiswa' => null,
        ];

        $importer->model($row);

        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertEquals('tanggal_surat', $errors[0]['field']);
        $this->assertStringContainsString('tidak valid', $errors[0]['message']);
    }

    /**
     * Test 5: Import gagal - invalid signatory ID
     */
    public function test_import_fails_when_signatory_id_not_found()
    {
        $this->createTestData();

        LettersImport::resetAll();
        $importer = new LettersImport();

        $row = [
            'nomor_surat' => 'B/001/2026',
            'tanggal_surat' => '2026-05-14',
            'kode_penandatangan' => 999, // Non-existent ID
            'kode_klasifikasi_surat' => 'BU',
            'kode_jenis_surat' => 'ST',
            'perihal' => 'Test',
            'tujuan' => 'Test',
            'status' => 'draft',
            'sasaran_surat' => null,
            'klasifikasi_keamanan' => null,
            'nama_keperluan' => null,
            'nama_mahasiswa' => null,
        ];

        $importer->model($row);

        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertEquals('kode_penandatangan', $errors[0]['field']);
        $this->assertStringContainsString('tidak ditemukan', $errors[0]['message']);
    }

    /**
     * Test 6: Import gagal - invalid classification code
     */
    public function test_import_fails_when_classification_code_not_found()
    {
        $this->createTestData();

        LettersImport::resetAll();
        $importer = new LettersImport();

        $row = [
            'nomor_surat' => 'B/001/2026',
            'tanggal_surat' => '2026-05-14',
            'kode_penandatangan' => 1,
            'kode_klasifikasi_surat' => 'INVALID_CODE', // Non-existent code
            'kode_jenis_surat' => 'ST',
            'perihal' => 'Test',
            'tujuan' => 'Test',
            'status' => 'draft',
            'sasaran_surat' => null,
            'klasifikasi_keamanan' => null,
            'nama_keperluan' => null,
            'nama_mahasiswa' => null,
        ];

        $importer->model($row);

        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertEquals('kode_klasifikasi_surat', $errors[0]['field']);
        $this->assertStringContainsString('tidak ditemukan', $errors[0]['message']);
    }

    /**
     * Test 7: Import gagal - invalid letter type code
     */
    public function test_import_fails_when_letter_type_code_not_found()
    {
        $this->createTestData();

        LettersImport::resetAll();
        $importer = new LettersImport();

        $row = [
            'nomor_surat' => 'B/001/2026',
            'tanggal_surat' => '2026-05-14',
            'kode_penandatangan' => 1,
            'kode_klasifikasi_surat' => 'BU',
            'kode_jenis_surat' => 'INVALID_TYPE', // Non-existent type
            'perihal' => 'Test',
            'tujuan' => 'Test',
            'status' => 'draft',
            'sasaran_surat' => null,
            'klasifikasi_keamanan' => null,
            'nama_keperluan' => null,
            'nama_mahasiswa' => null,
        ];

        $importer->model($row);

        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertEquals('kode_jenis_surat', $errors[0]['field']);
        $this->assertStringContainsString('tidak ditemukan', $errors[0]['message']);
    }

    /**
     * Test 8: Import gagal - invalid optional field (sasaran_surat)
     */
    public function test_import_fails_when_sasaran_surat_has_invalid_value()
    {
        $this->createTestData();

        LettersImport::resetAll();
        $importer = new LettersImport();

        $row = [
            'nomor_surat' => 'B/001/2026',
            'tanggal_surat' => '2026-05-14',
            'kode_penandatangan' => 1,
            'kode_klasifikasi_surat' => 'BU',
            'kode_jenis_surat' => 'ST',
            'perihal' => 'Test',
            'tujuan' => 'Test',
            'status' => 'draft',
            'sasaran_surat' => 'invalid_target', // Invalid enum value
            'klasifikasi_keamanan' => null,
            'nama_keperluan' => null,
            'nama_mahasiswa' => null,
        ];

        $importer->model($row);

        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertEquals('sasaran_surat', $errors[0]['field']);
        $this->assertStringContainsString('tidak valid', $errors[0]['message']);
    }

    /**
     * Test 9: Import gagal - invalid optional field (klasifikasi_keamanan)
     */
    public function test_import_fails_when_klasifikasi_keamanan_has_invalid_value()
    {
        $this->createTestData();

        LettersImport::resetAll();
        $importer = new LettersImport();

        $row = [
            'nomor_surat' => 'B/001/2026',
            'tanggal_surat' => '2026-05-14',
            'kode_penandatangan' => 1,
            'kode_klasifikasi_surat' => 'BU',
            'kode_jenis_surat' => 'ST',
            'perihal' => 'Test',
            'tujuan' => 'Test',
            'status' => 'draft',
            'sasaran_surat' => null,
            'klasifikasi_keamanan' => 'INVALID_SECURITY', // Invalid enum value
            'nama_keperluan' => null,
            'nama_mahasiswa' => null,
        ];

        $importer->model($row);

        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertEquals('klasifikasi_keamanan', $errors[0]['field']);
    }

    /**
     * Test 10: Import gagal - invalid status
     */
    public function test_import_fails_when_status_is_invalid()
    {
        $this->createTestData();

        LettersImport::resetAll();
        $importer = new LettersImport();

        $row = [
            'nomor_surat' => 'B/001/2026',
            'tanggal_surat' => '2026-05-14',
            'kode_penandatangan' => 1,
            'kode_klasifikasi_surat' => 'BU',
            'kode_jenis_surat' => 'ST',
            'perihal' => 'Test',
            'tujuan' => 'Test',
            'status' => 'invalid_status', // Invalid status
            'sasaran_surat' => null,
            'klasifikasi_keamanan' => null,
            'nama_keperluan' => null,
            'nama_mahasiswa' => null,
        ];

        $importer->model($row);

        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertEquals('status', $errors[0]['field']);
    }

    /**
     * Test 11: Optional fields are correctly set to defaults when empty
     */
    public function test_optional_fields_get_default_values()
    {
        $this->createTestData();

        LettersImport::resetAll();
        $importer = new LettersImport();

        $row = [
            'nomor_surat' => 'B/001/2026',
            'tanggal_surat' => '2026-05-14',
            'kode_penandatangan' => 1,
            'kode_klasifikasi_surat' => 'BU',
            'kode_jenis_surat' => 'ST',
            'perihal' => 'Test Subject',
            'tujuan' => 'Test Recipient',
            'status' => 'draft',
            'sasaran_surat' => '', // Empty - should use default
            'klasifikasi_keamanan' => '', // Empty - should use default
            'nama_keperluan' => '', // Empty - should remain null
            'nama_mahasiswa' => '', // Empty - should remain null
        ];

        $result = $importer->model($row);

        // Should not have errors
        $this->assertFalse($importer->hasErrors());

        // Check buffered data has correct defaults
        $buffered = LettersImport::getBufferedLetters();
        $this->assertNotEmpty($buffered);

        $letterData = array_values($buffered)[0]['letters'][0];
        $this->assertEquals('internal', $letterData['letter_target']);
        $this->assertEquals('B', $letterData['security_classification']);
        $this->assertNull($letterData['student_name']);
    }

    /**
     * Test 12: Multiple rows - first error stops processing
     * (According to design: "Tampilkan error pertama per baris")
     */
    public function test_import_stops_at_first_error_per_row()
    {
        $this->createTestData();

        LettersImport::resetAll();
        $importer = new LettersImport();

        // Row 1: Valid
        $row1 = [
            'nomor_surat' => 'B/001/2026',
            'tanggal_surat' => '2026-05-14',
            'kode_penandatangan' => 1,
            'kode_klasifikasi_surat' => 'BU',
            'kode_jenis_surat' => 'ST',
            'perihal' => 'Valid Row',
            'tujuan' => 'Valid Recipient',
            'status' => 'draft',
            'sasaran_surat' => null,
            'klasifikasi_keamanan' => null,
            'nama_keperluan' => null,
            'nama_mahasiswa' => null,
        ];

        // Row 2: Invalid (missing nomor_surat)
        $row2 = [
            'nomor_surat' => '', // Error
            'tanggal_surat' => '2026-05-15',
            'kode_penandatangan' => 1,
            'kode_klasifikasi_surat' => 'BU',
            'kode_jenis_surat' => 'ST',
            'perihal' => 'Invalid Row',
            'tujuan' => 'Invalid Recipient',
            'status' => 'draft',
            'sasaran_surat' => null,
            'klasifikasi_keamanan' => null,
            'nama_keperluan' => null,
            'nama_mahasiswa' => null,
        ];

        $importer->model($row1);
        $importer->model($row2);

        // Should have 1 error (from row 2)
        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertEquals(1, count($errors));
        $this->assertEquals(3, $errors[0]['row']); // Row 3 (header is row 1)
    }

    /**
     * Create test data in database
     */
    private function createTestData()
    {
        // Create signatory if doesn't exist
        if (!Signatory::exists()) {
            Signatory::create([
                'code' => 'UN39.5.FEB',
                'name' => 'Test Signatory',
                'position' => 'Test Position',
                'nip' => '123456789',
                'is_active' => true
            ]);
        }

        // Create letter type if doesn't exist
        if (!LetterType::exists()) {
            LetterType::create(['code' => 'ST', 'name' => 'Surat Tugas']);
        }

        // Create classification if doesn't exist
        if (!ClassificationLetter::exists()) {
            ClassificationLetter::create(['code' => 'BU', 'name' => 'Test Classification']);
        }
    }

    /**
     * Create minimal test Excel content
     * Only required fields filled
     */
    private function createMinimalTestExcelContent()
    {
        // This is a simple representation
        // In real test, you'd use a proper Excel library
        // For now, we'll test directly with the importer
        return '';
    }
}
