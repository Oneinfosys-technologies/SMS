<?php

namespace App\Policies\Communication;

use App\Models\Communication\Communication;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommunicationPolicy
{
    use HandlesAuthorization;

    private function validatePeriod(User $user, Communication $communication)
    {
        return $communication->period_id == $user->current_period_id;
    }

    /**
     * Determine whether the user can request for pre-requisites.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function preRequisite(User $user)
    {
        return $user->canAny(['email:send']);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAnyEmail(User $user)
    {
        return $user->can('email:read');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewEmail(User $user, Communication $communication)
    {
        if (! $this->validatePeriod($user, $communication)) {
            return false;
        }

        return $user->can('email:read');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function sendEmail(User $user)
    {
        return $user->can('email:send');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function deleteEmail(User $user, Communication $communication)
    {
        if (! $this->validatePeriod($user, $communication)) {
            return false;
        }

        return $user->can('email:read');
    }
}
