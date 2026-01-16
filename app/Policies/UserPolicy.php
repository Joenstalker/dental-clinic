<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        // Admin, Assistant, and Dentist can view users
        return $user->hasAnyRole(['admin', 'assistant', 'dentist']);
    }

    /**
     * Determine whether the user can view the user.
     */
    public function view(User $user, User $model): bool
    {
        // Admin can view any user
        if ($user->isAdmin()) {
            return true;
        }

        // Assistant can view patients
        if ($user->isAssistant() && $model->isPatient()) {
            return true;
        }

        // Dentist can view patients
        if ($user->isDentist() && $model->isPatient()) {
            return true;
        }

        // Users can view their own profile
        if ($user->id === $model->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        // Only admin can create staff (dentists, assistants)
        // Patients can register themselves (handled in SignupController)
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the user.
     */
    public function update(User $user, User $model): bool
    {
        // Admin can update any user
        if ($user->isAdmin()) {
            return true;
        }

        // Users can update their own profile
        if ($user->id === $model->id) {
            return true;
        }

        // Assistant can update patient status
        if ($user->isAssistant() && $model->isPatient()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the user.
     */
    public function delete(User $user, User $model): bool
    {
        // Admin can delete any user
        if ($user->isAdmin()) {
            return true;
        }

        // Assistant can delete patients
        if ($user->isAssistant() && $model->isPatient()) {
            return true;
        }

        // Users can delete their own account
        if ($user->id === $model->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update patient status.
     */
    public function updateStatus(User $user, User $model): bool
    {
        // Only admin and assistant can update patient status
        if (!$model->isPatient()) {
            return false;
        }

        return $user->hasAnyRole(['admin', 'assistant']);
    }

    /**
     * Determine whether the user can manage staff (dentists/assistants).
     */
    public function manageStaff(User $user): bool
    {
        return $user->isAdmin();
    }
}
