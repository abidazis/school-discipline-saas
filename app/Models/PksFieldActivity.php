<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PksFieldActivity extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * The available activity types.
     */
    public const TYPE_MONITORING = 'monitoring';
    public const TYPE_PATROL = 'patrol';
    public const TYPE_STUDENT_CONTROL = 'student_control';
    public const TYPE_GATE_MONITORING = 'gate_monitoring';
    public const TYPE_PARKING_MONITORING = 'parking_monitoring';
    public const TYPE_AREA_MONITORING = 'area_monitoring';
    public const TYPE_INCIDENT_HANDLING = 'incident_handling';
    public const TYPE_OTHER = 'other';

    /**
     * The available status options.
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Activity type labels in Indonesian.
     */
    public const TYPE_LABELS = [
        self::TYPE_MONITORING => 'Monitoring',
        self::TYPE_PATROL => 'Patroli',
        self::TYPE_STUDENT_CONTROL => 'Pengawasan Siswa',
        self::TYPE_GATE_MONITORING => 'Monitoring Gerbang',
        self::TYPE_PARKING_MONITORING => 'Monitoring Parkiran',
        self::TYPE_AREA_MONITORING => 'Monitoring Area',
        self::TYPE_INCIDENT_HANDLING => 'Penanganan Kejadian',
        self::TYPE_OTHER => 'Lainnya',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'pks_duty_assignment_id',
        'activity_date',
        'started_at',
        'ended_at',
        'activity_type',
        'description',
        'finding',
        'action_taken',
        'status',
        'recorded_by',
        'violation_id',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'activity_date' => 'date',
    ];

    /**
     * Get the school that owns this activity.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the assignment for this activity.
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(PksDutyAssignment::class, 'pks_duty_assignment_id');
    }

    /**
     * Get the user who recorded this activity.
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Get the linked violation.
     */
    public function violation(): BelongsTo
    {
        return $this->belongsTo(Violation::class);
    }

    /**
     * Get the activity type label.
     */
    public function getActivityTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->activity_type] ?? ucfirst($this->activity_type);
    }

    /**
     * Get the status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'bg-secondary',
            self::STATUS_COMPLETED => 'bg-success',
            self::STATUS_CANCELLED => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Check if activity is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if activity is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if activity is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Scope to get only completed activities.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope to get only draft activities.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope to get only active (non-cancelled) activities.
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', self::STATUS_CANCELLED);
    }

    /**
     * Scope to filter by activity type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('activity_type', $type);
    }
}
