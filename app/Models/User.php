<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'school_id', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The available roles.
     */
    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_SCHOOL_ADMIN = 'school_admin';
    public const ROLE_OPERATOR = 'operator';
    public const ROLE_TEACHER = 'teacher';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the school that the user belongs to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Check if user is a school admin.
     */
    public function isSchoolAdmin(): bool
    {
        return $this->role === self::ROLE_SCHOOL_ADMIN;
    }

    /**
     * Check if user is a school operator.
     */
    public function isOperator(): bool
    {
        return $this->role === self::ROLE_OPERATOR;
    }

    /**
     * Check if user is a teacher.
     */
    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    /**
     * Check if user has access to all schools (super admin).
     */
    public function hasAccessToAllSchools(): bool
    {
        return $this->isSuperAdmin();
    }

    /**
     * Check if user can manage a specific school.
     */
    public function canManageSchool(?int $schoolId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->school_id === $schoolId;
    }
}
