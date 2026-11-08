<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\LetterTarget;

/**
 * Model Letter untuk data surat dan nomor surat
 */
class Letter extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'letter_number',
        'running_number',
        'year',
        'signatory_id',
        'classification_id',
        'security_classification',
        'letter_target',
        'letter_type_id',
        'letter_purpose_id',
        'letter_date',
        'subject',
        'recipient',
        'student_name',
        'status',
        'is_active',
        'created_by',
        'drive_url',
        'archived_at',
        'archived_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'letter_date' => 'date',
        'running_number' => 'integer',
        'year' => 'integer',
        'archived_at' => 'datetime',
    ];

    /**
     * Relasi ke penandatangan surat
     */
    public function signatory()
    {
        return $this->belongsTo(Signatory::class, 'signatory_id');
    }

    /**
     * Relasi ke klasifikasi surat
     */
    public function classification()
    {
        return $this->belongsTo(ClassificationLetter::class, 'classification_id');
    }

    /**
     * Relasi ke jenis surat
     */
    public function letterType()
    {
        return $this->belongsTo(LetterType::class, 'letter_type_id');
    }

    /**
     * Relasi ke keperluan surat
     */
    public function letterPurpose()
    {
        return $this->belongsTo(LetterPurpose::class, 'letter_purpose_id');
    }

    /**
     * Relasi ke user yang membuat surat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke user yang mengarsipkan surat
     */
    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    /**
     * Check apakah surat sudah diarsipkan di Google Drive
     */
    public function getIsArchivedAttribute(): bool
    {
        return !empty($this->drive_url);
    }

    /**
     * Ekstrak Google Drive File ID dari berbagai format link
     */
    public function getDriveFileIdAttribute(): ?string
    {
        if (empty($this->drive_url)) {
            return null;
        }

        // Format: /file/d/{FILE_ID}/view atau /file/d/{FILE_ID}
        if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $this->drive_url, $matches)) {
            return $matches[1];
        }

        // Format: id={FILE_ID}
        if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $this->drive_url, $matches)) {
            return $matches[1];
        }

        // Format: /folders/{FILE_ID}
        if (preg_match('/\/folders\/([a-zA-Z0-9_-]+)/', $this->drive_url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * URL pratinjau embed (Iframe) Google Drive
     */
    public function getDrivePreviewUrlAttribute(): ?string
    {
        $fileId = $this->drive_file_id;
        if ($fileId) {
            return "https://drive.google.com/file/d/{$fileId}/preview";
        }
        return $this->drive_url;
    }

    /**
     * Relasi ke user letter views (many to many through pivot)
     */
    public function viewedByUsers()
    {
        return $this->hasMany(UserLetterView::class);
    }

    /**
     * Check apakah surat sudah dilihat oleh user tertentu
     */
    public function isViewedBy($userId)
    {
        return $this->viewedByUsers()->where('user_id', $userId)->exists();
    }

    /**
     * Check apakah surat baru untuk user tertentu (dibuat dalam 24 jam dan belum dilihat)
     */
    public function isNewFor($userId)
    {
        // Surat dianggap baru jika dibuat dalam 24 jam terakhir
        $isRecent = $this->created_at >= now()->subMinutes(2);

        // Dan belum dilihat oleh user tersebut
        $notViewed = !$this->isViewedBy($userId);

        // Tapi bukan dibuat oleh user tersebut
        $notCreatedByUser = $this->created_by == $userId;

        return $isRecent && $notViewed && $notCreatedByUser;
    }

    /**
     * Boot model - auto-generate letter_number saat creating
     * 
     * Jika running_number sudah ada, generate letter_number dengan format:
     * [SEC]/[RUNNING]/[TARGET_CODE][SIGNATORY]/[CLASS]/[YEAR]
     * Contoh: B/001/UN39.DEP-XYT/VAL-ZJ/2026
     */
    protected static function booted()
    {
        static::creating(function (self $model) {
            // Jika letter_number belum ada tapi running_number sudah ada, generate letter_number
            if (!$model->letter_number && $model->running_number) {
                // Ambil relasi yang diperlukan
                $signatory = $model->signatory ?? Signatory::find($model->signatory_id);
                $classification = $model->classification ?? ClassificationLetter::find($model->classification_id);
                
                if (!$signatory || !$classification) {
                    throw new \Exception('Signatory atau Classification tidak ditemukan');
                }

                $letterTarget = LetterTarget::from($model->letter_target);
                $targetCode = $letterTarget->code();
                
                // Mencegah double UN39 jika kode penandatangan sudah memiliki awalan UN39
                if (str_contains($signatory->code, 'UN39')) {
                    $targetCode = '';
                }

                $runningNumberFormatted = str_pad($model->running_number, 3, '0', STR_PAD_LEFT);

                $model->letter_number = sprintf(
                    '%s/%s/%s%s/%s/%d',
                    $model->security_classification,
                    $runningNumberFormatted,
                    $targetCode,
                    $signatory->code,
                    $classification->code,
                    $model->year
                );
            }
        });
    }

    /**
     * Scope untuk filter berdasarkan tahun
     */
    public function scopeYear($query, $year)
    {
        return $query->where($this->getTable() . '.year', $year);
    }

    /**
     * Scope untuk filter berdasarkan penandatangan
     */
    public function scopeSignatory($query, $signatoryId)
    {
        return $query->where($this->getTable() . '.signatory_id', $signatoryId);
    }

    /**
     * Scope untuk filter berdasarkan klasifikasi
     */
    public function scopeClassification($query, $classificationId)
    {
        return $query->where($this->getTable() . '.classification_id', $classificationId);
    }

    /**
     * Scope untuk filter berdasarkan jenis surat
     */
    public function scopeLetterType($query, $letterTypeId)
    {
        return $query->where($this->getTable() . '.letter_type_id', $letterTypeId);
    }

    /**
     * Scope untuk pencarian berdasarkan nomor surat, perihal, atau nama mahasiswa
     */
    public function scopeSearch($query, $search)
    {
        $table = $this->getTable();
        return $query->where(function ($q) use ($search, $table) {
            $q->where("{$table}.letter_number", 'like', "%{$search}%")
                ->orWhere("{$table}.subject", 'like', "%{$search}%")
                ->orWhere("{$table}.student_name", 'like', "%{$search}%")
                ->orWhere("{$table}.recipient", 'like', "%{$search}%");
        });
    }

    /**
     * Scope untuk filter hanya surat aktif
     */
    public function scopeActive($query)
    {
        return $query->where($this->getTable() . '.is_active', true);
    }

    /**
     * Scope untuk filter surat yang sudah terarsip
     */
    public function scopeArchived($query)
    {
        $table = $this->getTable();
        return $query->whereNotNull("{$table}.drive_url")->where("{$table}.drive_url", '!=', '');
    }

    /**
     * Scope untuk filter surat yang belum terarsip (hanya untuk jenis surat yang memerlukan arsip)
     */
    public function scopeUnarchived($query)
    {
        $table = $this->getTable();
        return $query->archivable()->where(function ($q) use ($table) {
            $q->whereNull("{$table}.drive_url")->orWhere("{$table}.drive_url", '');
        });
    }

    /**
     * Scope untuk filter klasifikasi keamanan
     */
    public function scopeSecurityClassification($query, $securityClassification)
    {
        return $query->where($this->getTable() . '.security_classification', $securityClassification);
    }

    /**
     * Scope untuk filter sasaran surat
     */
    public function scopeLetterTarget($query, $letterTarget)
    {
        return $query->where($this->getTable() . '.letter_target', $letterTarget);
    }

    /**
     * Scope untuk filter hanya surat yang jenis suratnya mewajibkan/memerlukan arsip Google Drive
     */
    public function scopeArchivable($query)
    {
        return $query->whereHas('letterType', function ($q) {
            $q->where('requires_drive_archive', true);
        });
    }

    /**
     * Cek apakah surat ini memerlukan arsip Google Drive berdasarkan jenis suratnya
     */
    public function getRequiresDriveArchiveAttribute(): bool
    {
        return (bool) ($this->letterType?->requires_drive_archive ?? false);
    }
}
