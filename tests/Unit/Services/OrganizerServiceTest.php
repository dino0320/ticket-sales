<?php

namespace Tests\Unit\Services;

use App\Consts\AccountConst;
use App\Models\User;
use App\Models\UserOrganizerApplication;
use App\Services\OrganizerService;
use RuntimeException;
use Tests\TestCase;

class OrganizerServiceTest extends TestCase
{
    /**
     * Test isOrganizerApplicationApplied()
     */
    public function test_is_organizer_application_applied(): void
    {
        $userOrganizerApplication1 = new UserOrganizerApplication(['status' => AccountConst::ORGANIZER_STATUS_UNAPPROVED,]);
        $userOrganizerApplication2 = new UserOrganizerApplication(['status' => AccountConst::ORGANIZER_STATUS_PENDING,]);
        $userOrganizerApplication3 = new UserOrganizerApplication(['status' => AccountConst::ORGANIZER_STATUS_APPROVED,]);
        $this->assertFalse(OrganizerService::isOrganizerApplicationApplied(null));
        $this->assertFalse(OrganizerService::isOrganizerApplicationApplied($userOrganizerApplication1));
        $this->assertTrue(OrganizerService::isOrganizerApplicationApplied($userOrganizerApplication2));
        $this->assertTrue(OrganizerService::isOrganizerApplicationApplied($userOrganizerApplication3));
    }

    /**
     * Test normal checkIfUserIsOrganizer()
     */
    public function test_check_if_user_is_organizer_normal(): void
    {
        $this->expectNotToPerformAssertions();
        OrganizerService::checkIfUserIsOrganizer(User::factory()->make(['is_organizer' => true]));
    }

    /**
     * Test abnormal checkIfUserIsOrganizer()
     */
    public function test_check_if_user_is_organizer_abnormal(): void
    {
        $this->expectException(RuntimeException::class);
        OrganizerService::checkIfUserIsOrganizer(User::factory()->make(['is_organizer' => false]));
    }
}
