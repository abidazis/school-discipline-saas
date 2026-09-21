<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PksDutyAssignment extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * The available status options.
     */
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_REPLACED = 'replaced';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'pks_duty_schedule_id',
        'pks_member_id',
        'pks_duty_location_id',
        'status',
        'assigned_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    /**
     * Get the school that owns this assignment.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the schedule for this assignment.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(PksDutySchedule::class, 'pks_duty_schedule_id');
    }

    /**
     * Get the member for this assignment.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(PksMember::class, 'pks_member_id');
    }

    /**
     * Get the location for this assignment.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(PksDutyLocation::class, 'pks_duty_location_id');
    }

    /**
     * Get the status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ASSIGNED => 'Ditugaskan',
            self::STATUS_REPLACED => 'Digantikan',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Scope to get only assigned (active) assignments.
     */
    public function scopeAssigned($query)
    {
        return $query->where('status', self::STATUS_ASSIGNED);
    }

    /**
     * Check if assignment is active (assigned).
     */
    public function isAssigned(): bool
    {
        return $this->status === self::STATUS_ASSIGNED;
    }

    /**
     * Check if assignment is replaced.
     */
    public function isReplaced(): bool
    {
        return $this->status === self::STATUS_REPLACED;
    }

    /**
     * Check if assignment is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if assignment is eligible for attendance.
     */
    public function isEligibleForAttendance(): bool
    {
        return $this->isAssigned();
    }

    /**
     * Get the attendance record for this assignment.
     */
    public function attendance()
    {
        return $this->hasOne(PksDutyAttendance::class, 'pks_duty_assignment_id');
    }

    /**
     * Get the field activities for this assignment.
     */
    public function fieldActivities(): HasMany
    {
        return $this->hasMany(PksFieldActivity::class, 'pks_duty_assignment_id');
    }

    /**
     * Get the count of completed field activities.
     */
    public function getCompletedActivitiesCountAttribute(): int
    {
        return $this->fieldActivities()->where('status', PksFieldActivity::STATUS_COMPLETED)->count();
    }
}
