<?php

namespace App\Policies;

use App\Models\AppointmentSession;
use App\Models\User;

class AppointmentSessionPolicy
{
    /**
     * Determine whether the user can view any sessions.
     */
    public function viewAny(User $user): bool
    {
        // Admin, Assistant, Dentist, and Patients can view sessions
        return $user->hasAnyRole(['admin', 'assistant', 'dentist', 'patient']);
    }

    /**
     * Determine whether the user can view the session.
     */
    public function view(User $user, AppointmentSession $appointmentSession): bool
    {
        // Admin and Assistant can view any session
        if ($user->hasAnyRole(['admin', 'assistant'])) {
            return true;
        }

        // Dentist can view their own sessions
        if ($user->isDentist()) {
            return $appointmentSession->user_id === $user->id;
        }

        // Patients can view any session (to book appointments)
        if ($user->isPatient()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create sessions.
     */
    public function create(User $user): bool
    {
        // Admin, Assistant, and Dentist can create sessions
        return $user->hasAnyRole(['admin', 'assistant', 'dentist']);
    }

    /**
     * Determine whether the user can update the session.
     */
    public function update(User $user, AppointmentSession $appointmentSession): bool
    {
        // Admin can update any session
        if ($user->isAdmin()) {
            return true;
        }

        // Assistant can update any session
        if ($user->isAssistant()) {
            return true;
        }

        // Dentist can only update their own sessions
        if ($user->isDentist()) {
            return $appointmentSession->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the session.
     */
    public function delete(User $user, AppointmentSession $appointmentSession): bool
    {
        // Admin can delete any session
        if ($user->isAdmin()) {
            return true;
        }

        // Assistant can delete any session
        if ($user->isAssistant()) {
            return true;
        }

        // Dentist can only delete their own sessions
        if ($user->isDentist()) {
            return $appointmentSession->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can cancel the session.
     */
    public function cancel(User $user, AppointmentSession $appointmentSession): bool
    {
        return $this->delete($user, $appointmentSession);
    }
}
