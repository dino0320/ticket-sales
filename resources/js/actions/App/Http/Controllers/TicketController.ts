import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\TicketController::show
* @see app/Http/Controllers/TicketController.php:33
* @route '/tickets/{ticket}'
*/
export const show = (args: { ticket: number | { id: number } } | [ticket: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/tickets/{ticket}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TicketController::show
* @see app/Http/Controllers/TicketController.php:33
* @route '/tickets/{ticket}'
*/
show.url = (args: { ticket: number | { id: number } } | [ticket: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { ticket: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { ticket: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            ticket: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        ticket: typeof args.ticket === 'object'
        ? args.ticket.id
        : args.ticket,
    }

    return show.definition.url
            .replace('{ticket}', parsedArgs.ticket.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TicketController::show
* @see app/Http/Controllers/TicketController.php:33
* @route '/tickets/{ticket}'
*/
show.get = (args: { ticket: number | { id: number } } | [ticket: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TicketController::show
* @see app/Http/Controllers/TicketController.php:33
* @route '/tickets/{ticket}'
*/
show.head = (args: { ticket: number | { id: number } } | [ticket: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TicketController::showUserTicket
* @see app/Http/Controllers/TicketController.php:50
* @route '/user-tickets/{user_ticket}'
*/
export const showUserTicket = (args: { user_ticket: string | number } | [user_ticket: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showUserTicket.url(args, options),
    method: 'get',
})

showUserTicket.definition = {
    methods: ["get","head"],
    url: '/user-tickets/{user_ticket}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TicketController::showUserTicket
* @see app/Http/Controllers/TicketController.php:50
* @route '/user-tickets/{user_ticket}'
*/
showUserTicket.url = (args: { user_ticket: string | number } | [user_ticket: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return showUserTicket.definition.url
            .replace('{user_ticket}', parsedArgs.user_ticket.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TicketController::showUserTicket
* @see app/Http/Controllers/TicketController.php:50
* @route '/user-tickets/{user_ticket}'
*/
showUserTicket.get = (args: { user_ticket: string | number } | [user_ticket: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showUserTicket.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TicketController::showUserTicket
* @see app/Http/Controllers/TicketController.php:50
* @route '/user-tickets/{user_ticket}'
*/
showUserTicket.head = (args: { user_ticket: string | number } | [user_ticket: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showUserTicket.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TicketController::useTicket
* @see app/Http/Controllers/TicketController.php:209
* @route '/user-tickets/{user_ticket}/use'
*/
export const useTicket = (args: { user_ticket: string | number } | [user_ticket: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: useTicket.url(args, options),
    method: 'get',
})

useTicket.definition = {
    methods: ["get","head"],
    url: '/user-tickets/{user_ticket}/use',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TicketController::useTicket
* @see app/Http/Controllers/TicketController.php:209
* @route '/user-tickets/{user_ticket}/use'
*/
useTicket.url = (args: { user_ticket: string | number } | [user_ticket: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return useTicket.definition.url
            .replace('{user_ticket}', parsedArgs.user_ticket.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TicketController::useTicket
* @see app/Http/Controllers/TicketController.php:209
* @route '/user-tickets/{user_ticket}/use'
*/
useTicket.get = (args: { user_ticket: string | number } | [user_ticket: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: useTicket.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TicketController::useTicket
* @see app/Http/Controllers/TicketController.php:209
* @route '/user-tickets/{user_ticket}/use'
*/
useTicket.head = (args: { user_ticket: string | number } | [user_ticket: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: useTicket.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TicketController::store
* @see app/Http/Controllers/TicketController.php:88
* @route '/tickets'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/tickets',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TicketController::store
* @see app/Http/Controllers/TicketController.php:88
* @route '/tickets'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TicketController::store
* @see app/Http/Controllers/TicketController.php:88
* @route '/tickets'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TicketController::showIssuedTicket
* @see app/Http/Controllers/TicketController.php:72
* @route '/issued-tickets/{ticket}'
*/
export const showIssuedTicket = (args: { ticket: number | { id: number } } | [ticket: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showIssuedTicket.url(args, options),
    method: 'get',
})

showIssuedTicket.definition = {
    methods: ["get","head"],
    url: '/issued-tickets/{ticket}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TicketController::showIssuedTicket
* @see app/Http/Controllers/TicketController.php:72
* @route '/issued-tickets/{ticket}'
*/
showIssuedTicket.url = (args: { ticket: number | { id: number } } | [ticket: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { ticket: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { ticket: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            ticket: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        ticket: typeof args.ticket === 'object'
        ? args.ticket.id
        : args.ticket,
    }

    return showIssuedTicket.definition.url
            .replace('{ticket}', parsedArgs.ticket.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TicketController::showIssuedTicket
* @see app/Http/Controllers/TicketController.php:72
* @route '/issued-tickets/{ticket}'
*/
showIssuedTicket.get = (args: { ticket: number | { id: number } } | [ticket: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showIssuedTicket.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TicketController::showIssuedTicket
* @see app/Http/Controllers/TicketController.php:72
* @route '/issued-tickets/{ticket}'
*/
showIssuedTicket.head = (args: { ticket: number | { id: number } } | [ticket: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showIssuedTicket.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TicketController::update
* @see app/Http/Controllers/TicketController.php:155
* @route '/tickets/{ticket}'
*/
export const update = (args: { ticket: number | { id: number } } | [ticket: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/tickets/{ticket}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\TicketController::update
* @see app/Http/Controllers/TicketController.php:155
* @route '/tickets/{ticket}'
*/
update.url = (args: { ticket: number | { id: number } } | [ticket: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { ticket: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { ticket: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            ticket: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        ticket: typeof args.ticket === 'object'
        ? args.ticket.id
        : args.ticket,
    }

    return update.definition.url
            .replace('{ticket}', parsedArgs.ticket.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TicketController::update
* @see app/Http/Controllers/TicketController.php:155
* @route '/tickets/{ticket}'
*/
update.put = (args: { ticket: number | { id: number } } | [ticket: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

const TicketController = { show, showUserTicket, useTicket, store, showIssuedTicket, update }

export default TicketController