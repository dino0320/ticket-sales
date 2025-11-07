import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\UserCartController::store
* @see app/Http/Controllers/UserCartController.php:24
* @route '/cart'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/cart',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserCartController::store
* @see app/Http/Controllers/UserCartController.php:24
* @route '/cart'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserCartController::store
* @see app/Http/Controllers/UserCartController.php:24
* @route '/cart'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserCartController::show
* @see app/Http/Controllers/UserCartController.php:57
* @route '/cart'
*/
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/cart',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserCartController::show
* @see app/Http/Controllers/UserCartController.php:57
* @route '/cart'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserCartController::show
* @see app/Http/Controllers/UserCartController.php:57
* @route '/cart'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserCartController::show
* @see app/Http/Controllers/UserCartController.php:57
* @route '/cart'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\UserCartController::updateNumberOfTickets
* @see app/Http/Controllers/UserCartController.php:78
* @route '/cart/{ticket}/number-of-tickets'
*/
export const updateNumberOfTickets = (args: { ticket: number | { id: number } } | [ticket: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateNumberOfTickets.url(args, options),
    method: 'post',
})

updateNumberOfTickets.definition = {
    methods: ["post"],
    url: '/cart/{ticket}/number-of-tickets',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserCartController::updateNumberOfTickets
* @see app/Http/Controllers/UserCartController.php:78
* @route '/cart/{ticket}/number-of-tickets'
*/
updateNumberOfTickets.url = (args: { ticket: number | { id: number } } | [ticket: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return updateNumberOfTickets.definition.url
            .replace('{ticket}', parsedArgs.ticket.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserCartController::updateNumberOfTickets
* @see app/Http/Controllers/UserCartController.php:78
* @route '/cart/{ticket}/number-of-tickets'
*/
updateNumberOfTickets.post = (args: { ticket: number | { id: number } } | [ticket: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateNumberOfTickets.url(args, options),
    method: 'post',
})

const UserCartController = { store, show, updateNumberOfTickets }

export default UserCartController