<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PksDutyLocation extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * The available status options.
     */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'name',
        'code',
        'status',
        'description',
    ];

    /**
     * Get the school that owns this location.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the schedule locations for this location.
     */
    public function scheduleLocations(): HasMany
    {
        return $this->hasMany(PksDutyScheduleLocation::class, 'pks_duty_location_id');
    }

    /**
     * Get the duty schedules through this location.
     */
    public function dutySchedules(): HasMany
    {
        return $this->hasMany(PksDutySchedule::class, 'pks_shift_id');
    }

    /**
     * Get the status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_INACTIVE => 'Tidak Aktif',
            default => ucfirst($this->status),
        };
    }

    /**
     * Scope to get only active locations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Check if location is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
