<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    /**
     * Show user account
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        return Inertia::render('Account', [
        ]);
    }
}
