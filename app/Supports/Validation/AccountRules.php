<?php

namespace App\Supports\Validation;

use App\Consts\AccountConst;
use Illuminate\Validation\Rules\Password;

class AccountRules
{
    /**
     * Validate password
     *
     * @return Password
     */
    public static function password(): Password
    {
        return Password::defaults()->min(AccountConst::PASSWORD_LENGTH_MIN)->max(AccountConst::PASSWORD_LENGTH_MAX)->mixedCase()->numbers()->symbols();
    }
}
