<?php

namespace App\Services;

use App\Consts\AccountConst;
use App\Models\User;
use App\Models\UserOrganizerApplication;
use InvalidArgumentException;

class OrganizerService
{
    /**
     * Whether organizer application is applied or not
     *
     * @param UserOrganizerApplication|null $userOrganizerApplication
     * @return boolean
     */
    public static function isOrganizerApplicationApplied(?UserOrganizerApplication $userOrganizerApplication): bool
    {
        if ($userOrganizerApplication === null) {
            return false;
        }

        if ($userOrganizerApplication->status === AccountConst::ORGANIZER_STATUS_UNAPPROVED) {
            return false;
        }

        return true;
    }

    /**
     * Check if user is organizer
     *
     * @param User $user
     * @return void
     */
    public static function checkIfUserIsOrganizer(User $user): void
    {
        if ($user->is_organizer) {
            return;
        }

        throw new InvalidArgumentException("The User ID is not an organizer. user_id: {$user->id}");
    }
}
