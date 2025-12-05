<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'letter_type_id',
        'letter_purpose_id',
        'letter_date',
        'subject',
        'recipient',
        'student_name',
        'status',
        'is_active',
        'created_by',
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
     * Scope untuk filter berdasarkan tahun
     */
    public function scopeYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope untuk filter berdasarkan penandatangan
     */
    public function scopeSignatory($query, $signatoryId)
    {
        return $query->where('signatory_id', $signatoryId);
    }

    /**
     * Scope untuk filter berdasarkan klasifikasi
     */
    public function scopeClassification($query, $classificationId)
    {
        return $query->where('classification_id', $classificationId);
    }

    /**
     * Scope untuk filter berdasarkan jenis surat
     */
    public function scopeLetterType($query, $letterTypeId)
    {
        return $query->where('letter_type_id', $letterTypeId);
    }

    /**
     * Scope untuk pencarian berdasarkan nomor surat, perihal, atau nama mahasiswa
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('letter_number', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%")
                ->orWhere('student_name', 'like', "%{$search}%")
                ->orWhere('recipient', 'like', "%{$search}%");
        });
    }

    /**
     * Scope untuk filter hanya surat aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
