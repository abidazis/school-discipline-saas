<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Violation extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * The available status options.
     */
    public const STATUS_RECORDED = 'recorded';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'student_id',
        'violation_type_id',
        'academic_year_id',
        'school_class_id',
        'officer_id',
        'occurred_at',
        'location',
        'description',
        'points',
        'status',
        'cancelled_reason',
        'cancelled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'occurred_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the student that owns the violation.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the violation type.
     */
    public function violationType(): BelongsTo
    {
        return $this->belongsTo(ViolationType::class);
    }

    /**
     * Get the academic year.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the school class.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Get the officer who recorded the violation.
     */
    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    /**
     * Get the evidence files.
     */
    public function evidences(): HasMany
    {
        return $this->hasMany(ViolationEvidence::class);
    }

    /**
     * Get status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_RECORDED => 'Tercatat',
            self::STATUS_VERIFIED => 'Terverifikasi',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Scope to get only active (non-cancelled) violations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', self::STATUS_CANCELLED);
    }

    /**
     * Scope to get recorded violations.
     */
    public function scopeRecorded($query)
    {
        return $query->where('status', self::STATUS_RECORDED);
    }

    /**
     * Scope to get verified violations.
     */
    public function scopeVerified($query)
    {
        return $query->where('status', self::STATUS_VERIFIED);
    }

    /**
     * Scope to get cancelled violations.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Check if violation is recorded.
     */
    public function isRecorded(): bool
    {
        return $this->status === self::STATUS_RECORDED;
    }

    /**
     * Check if violation is verified.
     */
    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    /**
     * Check if violation is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if violation can be edited.
     */
    public function canEdit(): bool
    {
        return $this->status === self::STATUS_RECORDED;
    }

    /**
     * Check if violation can be verified.
     */
    public function canVerify(): bool
    {
        return $this->status === self::STATUS_RECORDED;
    }

    /**
     * Check if violation can be cancelled.
     */
    public function canCancel(): bool
    {
        return $this->status !== self::STATUS_CANCELLED;
    }

    /**
     * Verify the violation.
     */
    public function verify(): bool
    {
        if (!$this->canVerify()) {
            return false;
        }

        $this->update(['status' => self::STATUS_VERIFIED]);
        return true;
    }

    /**
     * Cancel the violation.
     */
    public function cancel(string $reason = null): bool
    {
        if (!$this->canCancel()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_reason' => $reason,
            'cancelled_at' => now(),
        ]);

        return true;
    }
}
