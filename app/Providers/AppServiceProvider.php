<?php

namespace App\Providers;

use App\Models\Violation;
use App\Models\ViolationEvidence;
use App\Models\ViolationType;
use App\Policies\PksMemberPolicy;
use App\Policies\ViolationEvidencePolicy;
use App\Policies\ViolationPolicy;
use App\Policies\ViolationTypePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies explicitly for clarity
        Gate::policy(ViolationType::class, ViolationTypePolicy::class);
        Gate::policy(Violation::class, ViolationPolicy::class);
        Gate::policy(ViolationEvidence::class, ViolationEvidencePolicy::class);
        Gate::policy(\App\Models\PksMember::class, PksMemberPolicy::class);
    }
}
