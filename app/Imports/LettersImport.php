<?php

namespace App\Imports;

use App\Models\Letter;
use App\Models\Signatory;
use App\Models\ClassificationLetter;
use App\Models\LetterType;
use App\Models\LetterPurpose;
use App\Models\LetterSequence;
use App\Enums\LetterTarget;
use App\Enums\SecurityClassification;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;
use Exception;
use Illuminate\Validation\Rule;

class LettersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip empty rows
        if (!array_filter($row)) {
            return null;
        }

        return DB::transaction(function () use ($row) {
            $date = null;
            if (!empty($row['tanggal_surat'])) {
                try {
                    // Coba parsing tanggal. Di Excel biasanya menjadi integer serial number atau string Y-m-d.
                    if (is_numeric($row['tanggal_surat'])) {
                        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_surat']);
                    } else {
                        $date = Carbon::parse($row['tanggal_surat']);
                    }
                } catch (Exception $e) {
                    $date = Carbon::now();
                }
            } else {
                $date = Carbon::now();
            }

            $year = $date->format('Y');

            // Lookup Relasi berdasarkan ID untuk penandatangan, Kode untuk yang lain
            $signatory = Signatory::find($row['kode_penandatangan']);
            $classification = ClassificationLetter::where('code', $row['kode_klasifikasi_surat'])->first();
            $letterType = LetterType::where('code', $row['kode_jenis_surat'])->first();
            
            $purposeId = null;
            if (!empty($row['nama_keperluan'])) {
                $purpose = LetterPurpose::where('name', 'like', "%" . trim($row['nama_keperluan']) . "%")->first();
                if ($purpose) {
                    $purposeId = $purpose->id;
                }
            }

            // Validasi data penting
            if (!$signatory || !$classification || !$letterType) {
                // Skip baris ini jika relasi master tidak valid, atau bisa menggunakan Exception agar proses import batal.
                // Disini kita throw exception untuk memastikan integritas data.
                throw new Exception("Master data tidak valid pada baris dengan tanggal: " . $row['tanggal_surat'] . ". Pastikan kode penandatangan, klasifikasi, dan jenis surat benar.");
            }

            // Penentuan Nomor Surat & Running Number
            $letterNumber = $row['nomor_surat'] ?? null;
            
            // Generate running_number yang aman dan unik per letter_type_id dan year
            // sesuai dengan constraint di database.
            $maxRunningNumber = Letter::where('letter_type_id', $letterType->id)
                                      ->where('year', $year)
                                      ->max('running_number');
                                      
            $runningNumber = ($maxRunningNumber ?? 0) + 1;

            $target = strtolower(trim($row['sasaran_surat'] ?? 'internal'));
            $security = strtoupper(trim($row['klasifikasi_keamanan'] ?? 'B'));
            $status = strtolower(trim($row['status'] ?? 'final'));

            // Memastikan enum valid
            if (!in_array($target, ['internal', 'external'])) $target = 'internal';
            if (!in_array($security, ['B', 'T', 'R', 'SR'])) $security = 'B';
            if (!in_array($status, ['draft', 'final'])) $status = 'final';

            return new Letter([
                'letter_number' => $letterNumber,
                'running_number' => $runningNumber,
                'year' => $year,
                'signatory_id' => $signatory->id,
                'classification_id' => $classification->id,
                'security_classification' => $security,
                'letter_target' => $target,
                'letter_type_id' => $letterType->id,
                'letter_purpose_id' => $purposeId,
                'letter_date' => $date,
                'subject' => $row['perihal'] ?? null,
                'recipient' => $row['tujuan'] ?? null,
                'student_name' => $row['nama_mahasiswa'] ?? null,
                'status' => $status,
                'is_active' => true,
                'created_by' => auth()->id() ?? 1, // fallback jika dijalankan via console
            ]);
        });
    }

    public function rules(): array
    {
        return [
            'tanggal_surat' => 'required',
            'kode_penandatangan' => 'required',
            'kode_klasifikasi_surat' => 'required',
            'kode_jenis_surat' => 'required',
        ];
    }
}
