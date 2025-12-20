<?php

namespace App\Services;

use App\Consts\AccountConst;
use App\Models\User;
use App\Models\UserOrganizerApplication;
use Illuminate\Pagination\CursorPaginator;
use InvalidArgumentException;

class OrganizerService
{
    /**
     * Get paginated user organizer applications response
     *
     * @param CursorPaginator $userOrganizerApplications
     * @return array
     */
    public static function getPaginatedUserOrganizerApplicationsResponse(CursorPaginator $userOrganizerApplications): array
    {
        $userOrganizerApplicationsResponse = [
            'data' => self::getUserOrganizerApplicationsResponse($userOrganizerApplications->getCollection()->all()),
            'prev_page_url' => $userOrganizerApplications->previousPageUrl(),
            'next_page_url' => $userOrganizerApplications->nextPageUrl(),
        ];

        return $userOrganizerApplicationsResponse;
    }

    /**
     * Get user organizer applications response
     *
     * @param UserOrganizerApplication[] $userOrganizerApplications
     * @return array
     */
    private static function getUserOrganizerApplicationsResponse(array $userOrganizerApplications): array
    {
        $userOrganizerApplicationsResponse = [];
        foreach ($userOrganizerApplications as $userOrganizerApplication) {
            $userOrganizerApplicationsResponse[] = self::getUserOrganizerApplicationResponse($userOrganizerApplication);
        }

        return $userOrganizerApplicationsResponse;
    }

    /**
     * Get user organizer application response
     *
     * @param UserOrganizerApplication $userOrganizerApplications
     * @return array
     */
    public static function getUserOrganizerApplicationResponse(UserOrganizerApplication $userOrganizerApplication): array
    {
        return [
            'id' => $userOrganizerApplication->id,
            'user_id' => $userOrganizerApplication->user_id,
            'event_description' => $userOrganizerApplication->event_description,
            'is_individual' => $userOrganizerApplication->is_individual,
            'website_url' => $userOrganizerApplication->website_url,
            'applied_at' => $userOrganizerApplication->applied_at,
        ];
    }

    /**
     * Wether an organizer application is applied or not
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
     * Check if the user is an organizer
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
