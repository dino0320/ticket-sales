<?php

namespace App\Consts;

class TicketConst
{
    /**
     * Max event title length
     */
    public const EVENT_TITLE_LENGTH_MAX = 100;

    /**
     * Max event description length
     */
    public const EVENT_DESCRIPTION_LENGTH_MAX = 1000;
    
    /**
     * Min price (cents)
     */
    public const PRICE_MIN = 100;

    /**
     * Max price (cents)
     */
    public const PRICE_MAX = 100000;

    /**
     * The min number of tickets
     */
    public const NUMBER_OF_TICKETS_MIN = 1;

    /**
     * The max number of tickets
     */
    public const NUMBER_OF_TICKETS_MAX = 10000;
}