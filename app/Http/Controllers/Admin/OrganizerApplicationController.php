<?php

namespace App\Http\Controllers\Admin;

use App\Consts\AccountConst;
use App\Http\Controllers\Controller;
use App\Models\UserOrganizerApplication;
use App\Repositories\UserOrganizerApplicationRepository;
use App\Repositories\UserRepository;
use App\Services\OrganizerService;
use App\Services\PaginationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;

class OrganizerApplicationController extends Controller
{
    /**
     * Show organizer applications
     *
     * @return Response
     */
    public function index(): Response
    {
        $userOrganizerApplicationRepository = new UserOrganizerApplicationRepository();

        $paginator = $userOrganizerApplicationRepository->selectByStatus(AccountConst::ORGANIZER_STATUS_PENDING);

        return Inertia::render('Admin/OrganizerApplication', [
            'userOrganizerApplications' => PaginationService::getPaginatedDataResponse($paginator, OrganizerService::getUserOrganizerApplicationsResponse($paginator->getCollection()->all())),
        ]);
    }

    /**
     * Show an organizer application
     *
     * @param UserOrganizerApplication $userOrganizerApplication
     * @return Response
     */
    public function show(UserOrganizerApplication $userOrganizerApplication): Response
    {
        return Inertia::render('Admin/OrganizerApplicationDetail', [
            'userOrganizerApplication' => OrganizerService::getUserOrganizerApplicationResponse($userOrganizerApplication),
        ]);
    }

    /**
     * Update an organizer application status
     *
     * @param UserOrganizerApplication $userOrganizerApplication
     * @return RedirectResponse
     */
    public function updateStatus(Request $request, UserOrganizerApplication $userOrganizerApplication): RedirectResponse
    {
        $request->validate([
            'is_approved' => ['required', 'boolean'],
        ]);

        $userRepository = new UserRepository();
        $userOrganizerApplicationRepository = new UserOrganizerApplicationRepository();

        if ($userOrganizerApplication->status !== AccountConst::ORGANIZER_STATUS_PENDING) {
            throw new InvalidArgumentException("Invalid organizer application status. status: {$userOrganizerApplication->status}");
        }

        $user = $userRepository->selectById($userOrganizerApplication->user_id) ?? throw new InvalidArgumentException("There is not this user. user_id: {$userOrganizerApplication->user_id}");

        if ($user->is_organizer) {
            throw new InvalidArgumentException("This user is already an organizer. user_id: {$user->id}");
        }

        if ($request->is_approved) {
            $user->is_organizer = true;
            $userOrganizerApplication->status = AccountConst::ORGANIZER_STATUS_APPROVED;

            $userRepository->save($user);
            $userOrganizerApplicationRepository->save($userOrganizerApplication);

            return redirect()->intended('/admin/organizer-applications');
        }

        $userOrganizerApplication->status = AccountConst::ORGANIZER_STATUS_UNAPPROVED;

        $userOrganizerApplicationRepository->save($userOrganizerApplication);

        return redirect()->intended('/admin/organizer-applications');
    }
}
