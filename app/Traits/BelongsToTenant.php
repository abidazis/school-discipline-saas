<?php

namespace App\Traits;

use App\Models\School;
use App\Services\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

trait BelongsToTenant
{
    /**
     * Boot the trait.
     */
    public static function bootBelongsToTenant(): void
    {
        // Automatically add tenant scope to all queries
        static::addGlobalScope('tenant', function (Builder $builder) {
            $user = auth()->user();

            // Only apply scope if user exists and tenant context is set
            if ($user && !$user->isSuperAdmin()) {
                $model = $builder->getModel();
                $table = $model->getTable();

                // Check if model has school_id column directly
                if (Schema::hasColumn($table, 'school_id')) {
                    $builder->where('school_id', $user->school_id);
                }
                // For models without school_id but with a violation relationship (like ViolationEvidence)
                elseif (method_exists($model, 'violation')) {
                    $builder->whereHas('violation', function ($q) use ($user) {
                        $q->where('school_id', $user->school_id);
                    });
                }
            }
        });

        // Automatically set school_id on creating
        static::creating(function ($model) {
            $user = auth()->user();
            $table = $model->getTable();

            if ($user && !$user->isSuperAdmin() && !$model->school_id && Schema::hasColumn($table, 'school_id')) {
                $model->school_id = $user->school_id;
            }
        });
    }

    /**
     * Get the tenant that the model belongs to.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Scope query to only include records from current tenant.
     */
    public function scopeForCurrentTenant(Builder $query): Builder
    {
        $user = auth()->user();
        $table = $this->getTable();

        if ($user && !$user->isSuperAdmin()) {
            if (Schema::hasColumn($table, 'school_id')) {
                return $query->where('school_id', $user->school_id);
            }
        }

        return $query;
    }

    /**
     * Scope query to include all records (bypass tenant scope).
     */
    public function scopeWithoutTenantScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('tenant');
    }
}
