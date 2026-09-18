<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PksDutyScheduleLocation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pks_duty_schedule_id',
        'pks_duty_location_id',
    ];

    /**
     * Get the schedule that owns this location.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(PksDutySchedule::class, 'pks_duty_schedule_id');
    }

    /**
     * Get the location.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(PksDutyLocation::class, 'pks_duty_location_id');
    }
}
