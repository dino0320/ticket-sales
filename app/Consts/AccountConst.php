<?php

namespace App\Consts;

class AccountConst
{
    /**
     * Organizer status unapproved
     */
    public const ORGANIZER_STATUS_UNAPPROVED = 1;

    /**
     * Organizer status pending
     */
    public const ORGANIZER_STATUS_PENDING = 2;

    /**
     * Organizer status approved
     */
    public const ORGANIZER_STATUS_APPROVED = 3;

    /**
     * Max name length
     */
    public const NAME_LENGTH_MAX = 50;

    /**
     * Max email length
     */
    public const EMAIL_LENGTH_MAX = 254;

    /**
     * Min password length
     */
    public const PASSWORD_LENGTH_MIN = 8;

    /**
     * Max password length
     */
    public const PASSWORD_LENGTH_MAX = 100;

    /**
     * Max URL length
     */
    public const URL_LENGTH_MAX = 2048;
}