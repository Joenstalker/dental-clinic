<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class AppointmentPolicy
{
    /**
     * Determine whether the user can view any appointments.
     */
    public function viewAny(User $user): bool
    {
        // Admin, Assistant, and Dentist can view all appointments
        // Patients can only view their own (handled in controller)
        return $user->hasAnyRole(['admin', 'assistant', 'dentist', 'patient']);
    }

    /**
     * Determine whether the user can view the appointment.
     */
    public function view(User $user, Member $member): bool
    {
        // Admin and Assistant can view any appointment
        if ($user->hasAnyRole(['admin', 'assistant'])) {
            return true;
        }

        // Dentist can view appointments in their sessions
        if ($user->isDentist()) {
            return $member->appointmentSession->user_id === $user->id;
        }

        // Patient can only view their own appointments
        if ($user->isPatient()) {
            return $member->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create appointments.
     */
    public function create(User $user): bool
    {
        // Only patients can book appointments
        return $user->isPatient();
    }

    /**
     * Determine whether the user can update the appointment.
     */
    public function update(User $user, Member $member): bool
    {
        // Admin and Assistant can approve/disapprove appointments
        return $user->hasAnyRole(['admin', 'assistant']);
    }

    /**
     * Determine whether the user can delete the appointment.
     */
    public function delete(User $user, Member $member): bool
    {
        // Admin can delete any appointment
        if ($user->isAdmin()) {
            return true;
        }

        // Patient can delete their own pending appointments
        if ($user->isPatient() && $member->user_id === $user->id) {
            return $member->status === 'pending';
        }

        return false;
    }

    /**
     * Determine whether the user can approve the appointment.
     */
    public function approve(User $user, Member $member): bool
    {
        return $user->hasAnyRole(['admin', 'assistant']);
    }

    /**
     * Determine whether the user can disapprove the appointment.
     */
    public function disapprove(User $user, Member $member): bool
    {
        return $user->hasAnyRole(['admin', 'assistant']);
    }
}
