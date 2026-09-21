<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PksMember extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * The available status options.
     */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_GRADUATED = 'graduated';
    public const STATUS_RESIGNED = 'resigned';

    /**
     * The available positions.
     */
    public const POSITION_MEMBER = 'Anggota';
    public const POSITION_KORLAP = 'Korlap';
    public const POSITION_WAKIL_KORLAP = 'Wakil Korlap';
    public const POSITION_KETUA = 'Ketua';
    public const POSITION_WAKIL_KETUA = 'Wakil Ketua';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'student_id',
        'position',
        'status',
        'joined_at',
        'ended_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'joined_at' => 'date',
        'ended_at' => 'date',
    ];

    /**
     * Get the student that owns this PKS member.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_INACTIVE => 'Tidak Aktif',
            self::STATUS_GRADUATED => 'Lulus',
            self::STATUS_RESIGNED => 'Mengundurkan Diri',
            default => ucfirst($this->status),
        };
    }

    /**
     * Scope to get only active members.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Check if member is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if student already has an active membership at this school.
     */
    public static function hasActiveMembership(int $studentId, int $schoolId): bool
    {
        return static::where('student_id', $studentId)
            ->where('school_id', $schoolId)
            ->where('status', self::STATUS_ACTIVE)
            ->exists();
    }

    /**
     * Get the duty assignments for this member.
     */
    public function dutyAssignments(): HasMany
    {
        return $this->hasMany(PksDutyAssignment::class, 'pks_member_id');
    }
}
