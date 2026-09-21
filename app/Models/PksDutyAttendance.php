<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PksDutyAttendance extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * The available status options.
     */
    public const STATUS_PRESENT = 'present';
    public const STATUS_LATE = 'late';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_EXCUSED = 'excused';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'pks_duty_assignment_id',
        'status',
        'check_in_at',
        'check_out_at',
        'notes',
        'recorded_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    /**
     * Get the school that owns this attendance.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the assignment for this attendance.
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(PksDutyAssignment::class, 'pks_duty_assignment_id');
    }

    /**
     * Get the user who recorded this attendance.
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Get the status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PRESENT => 'Hadir',
            self::STATUS_LATE => 'Terlambat',
            self::STATUS_ABSENT => 'Tidak Hadir',
            self::STATUS_EXCUSED => 'Izin',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get the badge class for status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PRESENT => 'bg-success',
            self::STATUS_LATE => 'bg-warning text-dark',
            self::STATUS_ABSENT => 'bg-danger',
            self::STATUS_EXCUSED => 'bg-info',
            default => 'bg-secondary',
        };
    }

    /**
     * Check if attendance is present.
     */
    public function isPresent(): bool
    {
        return $this->status === self::STATUS_PRESENT;
    }

    /**
     * Check if attendance is late.
     */
    public function isLate(): bool
    {
        return $this->status === self::STATUS_LATE;
    }

    /**
     * Check if attendance is absent.
     */
    public function isAbsent(): bool
    {
        return $this->status === self::STATUS_ABSENT;
    }

    /**
     * Check if attendance is excused.
     */
    public function isExcused(): bool
    {
        return $this->status === self::STATUS_EXCUSED;
    }
}
