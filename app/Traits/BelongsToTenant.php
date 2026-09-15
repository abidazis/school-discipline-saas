<?php

namespace App\Traits;

use App\Models\School;
use App\Services\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

trait BelongsToTenant
{
    /**
     * Boot the trait.
     */
    public static function bootBelongsToTenant(): void
    {
        // Automatically add tenant scope to all queries
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantContext = App::make(TenantContext::class);
            $user = auth()->user();

            // Only apply scope if user exists and tenant context is set
            if ($user && !$user->isSuperAdmin()) {
                $builder->where('school_id', $user->school_id);
            }
        });

        // Automatically set school_id on creating
        static::creating(function ($model) {
            $user = auth()->user();

            if ($user && !$user->isSuperAdmin() && !$model->school_id) {
                $model->school_id = $user->school_id;
            }
        });
    }

    /**
     * Get the tenant that the model belongs to.
     */
    public function school(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Scope query to only include records from current tenant.
     */
    public function scopeForCurrentTenant(Builder $query): Builder
    {
        $user = auth()->user();

        if ($user && !$user->isSuperAdmin()) {
            return $query->where('school_id', $user->school_id);
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
