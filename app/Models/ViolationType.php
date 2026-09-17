<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ViolationType extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * The available severity levels.
     */
    public const SEVERITY_LOW = 'low';
    public const SEVERITY_MEDIUM = 'medium';
    public const SEVERITY_HIGH = 'high';
    public const SEVERITY_CRITICAL = 'critical';

    /**
     * The available categories.
     */
    public const CATEGORY_ATTENDANCE = 'Attendance';
    public const CATEGORY_UNIFORM = 'Uniform';
    public const CATEGORY_BEHAVIOR = 'Behavior';
    public const CATEGORY_SAFETY = 'Safety';
    public const CATEGORY_ACADEMIC = 'Academic';
    public const CATEGORY_TECHNOLOGY = 'Technology';
    public const CATEGORY_LEAVING_SCHOOL = 'Leaving School';
    public const CATEGORY_OTHER = 'Other';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'code',
        'name',
        'category',
        'severity',
        'points',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'points' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the school that owns this violation type.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the violations for this type.
     */
    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }

    /**
     * Get severity display name.
     */
    public function getSeverityDisplayAttribute(): string
    {
        return match ($this->severity) {
            self::SEVERITY_LOW => 'Ringan',
            self::SEVERITY_MEDIUM => 'Sedang',
            self::SEVERITY_HIGH => 'Berat',
            self::SEVERITY_CRITICAL => 'Sangat Berat',
            default => ucfirst($this->severity),
        };
    }

    /**
     * Scope to get only active violation types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if this violation type is currently active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if this violation type is used in any violation.
     */
    public function isUsed(): bool
    {
        return $this->violations()->exists();
    }
}
