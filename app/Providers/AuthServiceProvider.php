<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\Member;
use App\Models\AppointmentSession;
use App\Models\ConcernBox;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Member::class => \App\Policies\AppointmentPolicy::class,
        AppointmentSession::class => \App\Policies\AppointmentSessionPolicy::class,
        User::class => \App\Policies\UserPolicy::class,
        ConcernBox::class => \App\Policies\ConcernBoxPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // ============================================
        // USER MANAGEMENT GATES
        // ============================================

        // Admin can manage all users
        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin();
        });

        // Admin and Assistant can view patients
        Gate::define('view-patients', function (User $user) {
            return $user->hasAnyRole(['admin', 'assistant', 'dentist']);
        });

        // Only Admin can create/edit/delete dentists and assistants
        Gate::define('manage-staff', function (User $user) {
            return $user->isAdmin();
        });

        // Admin and Assistant can update patient status
        Gate::define('update-patient-status', function (User $user) {
            return $user->hasAnyRole(['admin', 'assistant']);
        });

        // Only Admin can delete patients
        Gate::define('delete-patient', function (User $user) {
            return $user->isAdmin();
        });

        // ============================================
        // APPOINTMENT GATES
        // ============================================

        // Patients can book appointments
        Gate::define('book-appointment', function (User $user) {
            return $user->isPatient();
        });

        // Assistant and Admin can approve appointments
        Gate::define('approve-appointment', function (User $user) {
            return $user->hasAnyRole(['admin', 'assistant']);
        });

        // Assistant and Admin can disapprove appointments
        Gate::define('disapprove-appointment', function (User $user) {
            return $user->hasAnyRole(['admin', 'assistant']);
        });

        // Assistant, Dentist, and Admin can create sessions
        Gate::define('create-session', function (User $user) {
            return $user->hasAnyRole(['admin', 'assistant', 'dentist']);
        });

        // Assistant, Dentist, and Admin can cancel sessions
        Gate::define('cancel-session', function (User $user) {
            return $user->hasAnyRole(['admin', 'assistant', 'dentist']);
        });

        // ============================================
        // SERVICES GATES
        // ============================================

        // Everyone can view services
        Gate::define('view-services', function (User $user) {
            return true; // All authenticated users can view services
        });

        // Assistant and Admin can create services
        Gate::define('create-services', function (User $user) {
            return $user->hasAnyRole(['admin', 'assistant']);
        });

        // Assistant and Admin can update services
        Gate::define('update-services', function (User $user) {
            return $user->hasAnyRole(['admin', 'assistant']);
        });

        // Only Admin can delete services
        Gate::define('delete-services', function (User $user) {
            return $user->isAdmin();
        });

        // Assistant and Admin can manage services (general permission)
        Gate::define('manage-services', function (User $user) {
            return $user->hasAnyRole(['admin', 'assistant']);
        });

        // ============================================
        // CONCERN BOX GATES
        // ============================================

        // Patients can create concerns
        Gate::define('create-concern', function (User $user) {
            return $user->isPatient();
        });

        // Admin can reply to concerns
        Gate::define('reply-concern', function (User $user) {
            return $user->isAdmin();
        });

        // ============================================
        // AUDIT LOGS GATES
        // ============================================

        // Only Admin can view audit logs
        Gate::define('view-audit-logs', function (User $user) {
            return $user->isAdmin();
        });

        // ============================================
        // PROFILE GATES
        // ============================================

        // Users can view their own profile
        Gate::define('view-profile', function (User $user, User $profileUser) {
            return $user->id === $profileUser->id || $user->isAdmin();
        });

        // Users can update their own profile
        Gate::define('update-profile', function (User $user, User $profileUser) {
            return $user->id === $profileUser->id || $user->isAdmin();
        });
    }
}
