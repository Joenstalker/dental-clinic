<?php

namespace App\Policies;

use App\Models\ConcernBox;
use App\Models\User;

class ConcernBoxPolicy
{
    /**
     * Determine whether the user can view any concerns.
     */
    public function viewAny(User $user): bool
    {
        // Admin can view all concerns
        // Patients can view their own concerns (handled in controller)
        return $user->hasAnyRole(['admin', 'patient']);
    }

    /**
     * Determine whether the user can view the concern.
     */
    public function view(User $user, ConcernBox $concernBox): bool
    {
        // Admin can view any concern
        if ($user->isAdmin()) {
            return true;
        }

        // Patient can only view their own concerns
        if ($user->isPatient()) {
            return $concernBox->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create concerns.
     */
    public function create(User $user): bool
    {
        // Only patients can create concerns
        return $user->isPatient();
    }

    /**
     * Determine whether the user can update the concern.
     */
    public function update(User $user, ConcernBox $concernBox): bool
    {
        // Admin can update any concern (to reply)
        if ($user->isAdmin()) {
            return true;
        }

        // Patient can update their own open concerns
        if ($user->isPatient() && $concernBox->user_id === $user->id) {
            return $concernBox->status === 'open';
        }

        return false;
    }

    /**
     * Determine whether the user can delete the concern.
     */
    public function delete(User $user, ConcernBox $concernBox): bool
    {
        // Admin can delete any concern
        if ($user->isAdmin()) {
            return true;
        }

        // Patient can delete their own concerns
        if ($user->isPatient()) {
            return $concernBox->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can reply to the concern.
     */
    public function reply(User $user, ConcernBox $concernBox): bool
    {
        // Only admin can reply to concerns
        return $user->isAdmin();
    }
}
