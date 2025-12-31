import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\TicketController::use
* @see app/Http/Controllers/TicketController.php:201
* @route '/user-tickets/{user_ticket}/use'
*/
export const use = (args: { user_ticket: string | number } | [user_ticket: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: use.url(args, options),
    method: 'get',
})

use.definition = {
    methods: ["get","head"],
    url: '/user-tickets/{user_ticket}/use',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TicketController::use
* @see app/Http/Controllers/TicketController.php:201
* @route '/user-tickets/{user_ticket}/use'
*/
use.url = (args: { user_ticket: string | number } | [user_ticket: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user_ticket: args }
    }

    if (Array.isArray(args)) {
        args = {
            user_ticket: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user_ticket: args.user_ticket,
    }

    return use.definition.url
            .replace('{user_ticket}', parsedArgs.user_ticket.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TicketController::use
* @see app/Http/Controllers/TicketController.php:201
* @route '/user-tickets/{user_ticket}/use'
*/
use.get = (args: { user_ticket: string | number } | [user_ticket: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: use.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TicketController::use
* @see app/Http/Controllers/TicketController.php:201
* @route '/user-tickets/{user_ticket}/use'
*/
use.head = (args: { user_ticket: string | number } | [user_ticket: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: use.url(args, options),
    method: 'head',
})

const userTickets = {
    use: Object.assign(use, use),
}

export default userTickets