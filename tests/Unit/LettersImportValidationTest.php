<?php

namespace Tests\Unit;

use App\Imports\LettersImport;
use App\Models\Signatory;
use App\Models\LetterType;
use App\Models\ClassificationLetter;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Unit tests for LettersImport validation logic
 * Tests the core validation and error collection mechanism
 */
class LettersImportValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedMasterData();
    }

    /**
     * Seed master data for testing
     */
    private function seedMasterData(): void
    {
        Signatory::create([
            'code' => 'UN39.5.FEB',
            'name' => 'Test Signatory 1',
            'position' => 'Position 1',
            'is_active' => true
        ]);

        LetterType::create(['code' => 'ST', 'name' => 'Surat Tugas']);
        
        ClassificationLetter::create(['code' => 'BU', 'name' => 'Test Classification']);
    }

    /**
     * Test 1: Valid minimal data (only required fields)
     * Should NOT have errors and data should be buffered
     */
    public function test_valid_minimal_data_passes_validation()
    {
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
            'sasaran_surat' => '', // empty - use default
            'klasifikasi_keamanan' => '', // empty - use default
            'nama_keperluan' => '', // empty - nullable
            'nama_mahasiswa' => '', // empty - nullable
        ];

        $importer->model($row);

        // Should not have errors
        $this->assertFalse($importer->hasErrors(), 'Should not have validation errors for valid data');

        // Data should be buffered for processing
        $buffered = LettersImport::getBufferedLetters();
        $this->assertNotEmpty($buffered, 'Letter data should be buffered');
    }

    /**
     * Test 2: Missing required field - nomor_surat
     */
    public function test_missing_nomor_surat_produces_error()
    {
        LettersImport::resetAll();
        $importer = new LettersImport();

        $row = [
            'nomor_surat' => '', // EMPTY - REQUIRED
            'tanggal_surat' => '2026-05-14',
            'kode_penandatangan' => 1,
            'kode_klasifikasi_surat' => 'BU',
            'kode_jenis_surat' => 'ST',
            'perihal' => 'Test',
            'tujuan' => 'Test',
            'status' => 'draft',
        ];

        $importer->model($row);

        $this->assertTrue($importer->hasErrors(), 'Should have error for missing nomor_surat');
        $errors = $importer->getErrors();
        $this->assertEquals('nomor_surat', $errors[0]['field']);
        $this->assertStringContainsString('wajib diisi', $errors[0]['message']);
    }

    /**
     * Test 3: Missing required field - tanggal_surat
     */
    public function test_missing_tanggal_surat_produces_error()
    {
        LettersImport::resetAll();
        $importer = new LettersImport();

        $row = [
            'nomor_surat' => 'B/001/2026',
            'tanggal_surat' => '', // EMPTY - REQUIRED
            'kode_penandatangan' => 1,
            'kode_klasifikasi_surat' => 'BU',
            'kode_jenis_surat' => 'ST',
            'perihal' => 'Test',
            'tujuan' => 'Test',
            'status' => 'draft',
        ];

        $importer->model($row);

        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertEquals('tanggal_surat', $errors[0]['field']);
    }

    /**
     * Test 4: Invalid date format
     */
    public function test_invalid_date_format_produces_error()
    {
        LettersImport::resetAll();
        $importer = new LettersImport();

        $row = [
            'nomor_surat' => 'B/001/2026',
            'tanggal_surat' => 'invalid-date',
            'kode_penandatangan' => 1,
            'kode_klasifikasi_surat' => 'BU',
            'kode_jenis_surat' => 'ST',
            'perihal' => 'Test',
            'tujuan' => 'Test',
            'status' => 'draft',
        ];

        $importer->model($row);

        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertEquals('tanggal_surat', $errors[0]['field']);
        $this->assertStringContainsString('tidak valid', $errors[0]['message']);
    }

    /**
     * Test 5: Invalid signatory ID
     */
    public function test_invalid_signatory_id_produces_error()
    {
        LettersImport::resetAll();
        $importer = new LettersImport();

        $row = [
            'nomor_surat' => 'B/001/2026',
            'tanggal_surat' => '2026-05-14',
            'kode_penandatangan' => 999, // Non-existent
            'kode_klasifikasi_surat' => 'BU',
            'kode_jenis_surat' => 'ST',
            'perihal' => 'Test',
            'tujuan' => 'Test',
            'status' => 'draft',
        ];

        $importer->model($row);

        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertEquals('kode_penandatangan', $errors[0]['field']);
        $this->assertStringContainsString('tidak ditemukan', $errors[0]['message']);
    }

    /**
     * Test 6: Invalid classification code
     */
    public function test_invalid_classification_code_produces_error()
    {
        LettersImport::resetAll();
        $importer = new LettersImport();

        $row = [
            'nomor_surat' => 'B/001/2026',
            'tanggal_surat' => '2026-05-14',
            'kode_penandatangan' => 1,
            'kode_klasifikasi_surat' => 'INVALID_CODE', // Non-existent
            'kode_jenis_surat' => 'ST',
            'perihal' => 'Test',
            'tujuan' => 'Test',
            'status' => 'draft',
        ];

        $importer->model($row);

        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertEquals('kode_klasifikasi_surat', $errors[0]['field']);
    }

    /**
     * Test 7: Invalid letter type code
     */
    public function test_invalid_letter_type_code_produces_error()
    {
        LettersImport::resetAll();
        $importer = new LettersImport();

        $row = [
            'nomor_surat' => 'B/001/2026',
            'tanggal_surat' => '2026-05-14',
            'kode_penandatangan' => 1,
            'kode_klasifikasi_surat' => 'BU',
            'kode_jenis_surat' => 'INVALID', // Non-existent
            'perihal' => 'Test',
            'tujuan' => 'Test',
            'status' => 'draft',
        ];

        $importer->model($row);

        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertEquals('kode_jenis_surat', $errors[0]['field']);
    }

    /**
     * Test 8: Invalid sasaran_surat (optional enum field)
     */
    public function test_invalid_sasaran_surat_produces_error()
    {
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
        ];

        $importer->model($row);

        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertEquals('sasaran_surat', $errors[0]['field']);
    }

    /**
     * Test 9: Invalid klasifikasi_keamanan (optional enum field)
     */
    public function test_invalid_klasifikasi_keamanan_produces_error()
    {
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
            'klasifikasi_keamanan' => 'INVALID', // Invalid enum value
        ];

        $importer->model($row);

        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertEquals('klasifikasi_keamanan', $errors[0]['field']);
    }

    /**
     * Test 10: Optional fields are correctly set to NULL when empty
     */
    public function test_optional_fields_receive_null_when_empty()
    {
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
            'sasaran_surat' => '', // Should be NULL
            'klasifikasi_keamanan' => '', // Should be NULL
        ];

        $importer->model($row);

        $this->assertFalse($importer->hasErrors(), 'Should not have errors for null optional fields');

        $buffered = LettersImport::getBufferedLetters();
        $letterData = array_values($buffered)[0]['letters'][0];

        // Verify fields are NULL
        $this->assertNull($letterData['letter_target']);
        $this->assertNull($letterData['security_classification']);
    }

    /**
     * Test 11: Error collection happens per row
     * First row is valid, second row has error
     */
    public function test_error_collection_across_multiple_rows()
    {
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
        ];

        // Row 2: Invalid - missing nomor_surat
        $row2 = [
            'nomor_surat' => '', // ERROR
            'tanggal_surat' => '2026-05-15',
            'kode_penandatangan' => 1,
            'kode_klasifikasi_surat' => 'BU',
            'kode_jenis_surat' => 'ST',
            'perihal' => 'Invalid Row',
            'tujuan' => 'Invalid Recipient',
            'status' => 'draft',
        ];

        $importer->model($row1);
        $importer->model($row2);

        // Should have 1 error
        $this->assertTrue($importer->hasErrors());
        $errors = $importer->getErrors();
        $this->assertCount(1, $errors, 'Should have exactly 1 error');
        
        // First row should be buffered (valid)
        $buffered = LettersImport::getBufferedLetters();
        $this->assertNotEmpty($buffered, 'Valid rows should be buffered');
    }

    /**
     * Test 12: Optional fields can be filled with valid values
     */
    public function test_optional_fields_save_filled_values()
    {
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
            'sasaran_surat' => 'external', // Fill with value
            'klasifikasi_keamanan' => 'R', // Fill with value
            'nama_mahasiswa' => 'Budi Santoso',
        ];

        $importer->model($row);

        $this->assertFalse($importer->hasErrors());

        $buffered = LettersImport::getBufferedLetters();
        $letterData = array_values($buffered)[0]['letters'][0];

        // Verify values are saved
        $this->assertEquals('external', $letterData['letter_target']);
        $this->assertEquals('R', $letterData['security_classification']);
        $this->assertEquals('Budi Santoso', $letterData['student_name']);
    }
}
