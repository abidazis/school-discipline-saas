<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PksDutySchedule extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * The available status options.
     */
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'pks_shift_id',
        'schedule_date',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'schedule_date' => 'date',
    ];

    /**
     * Get the school that owns this schedule.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the shift for this schedule.
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(PksShift::class, 'pks_shift_id');
    }

    /**
     * Get the locations for this schedule.
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(
            PksDutyLocation::class,
            'pks_duty_schedule_locations',
            'pks_duty_schedule_id',
            'pks_duty_location_id'
        )->withTimestamps();
    }

    /**
     * Get the schedule locations.
     */
    public function scheduleLocations(): HasMany
    {
        return $this->hasMany(PksDutyScheduleLocation::class);
    }

    /**
     * Get the status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SCHEDULED => 'Terjadwal',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get the time range from the shift.
     */
    public function getTimeRangeAttribute(): ?string
    {
        return $this->shift?->time_range;
    }

    /**
     * Scope to get only scheduled.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    /**
     * Check if schedule is scheduled.
     */
    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    /**
     * Check if schedule is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if schedule is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Get the assignments for this schedule.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(PksDutyAssignment::class, 'pks_duty_schedule_id');
    }

    /**
     * Get active assignments for this schedule.
     */
    public function activeAssignments(): HasMany
    {
        return $this->assignments()->where('status', PksDutyAssignment::STATUS_ASSIGNED);
    }
}
