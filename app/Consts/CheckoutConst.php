<?php

namespace App\Consts;

class CheckoutConst
{
    /**
     * Order status incomplete
     */
    public const ORDER_STATUS_INCOMPLETE = 1;

    /**
     * Order status completed
     */
    public const ORDER_STATUS_COMPLETED = 2;

    /**
     * Total reserved ticket key
     */
    public const TOTAL_RESERVED_TICKET_KEY = 'reserved_ticket';

    /**
     * Reserved ticket key
     */
    public const RESERVED_TICKET_KEY = 'reserved_ticket:%1$d:%2$d';

    /**
     * Reserved ticket expiration
     */
    public const RESERVED_TICKET_EXPIRATION = 86400;
}