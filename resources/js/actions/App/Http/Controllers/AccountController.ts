import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AccountController::show
* @see app/Http/Controllers/AccountController.php:23
* @route '/my-account'
*/
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/my-account',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AccountController::show
* @see app/Http/Controllers/AccountController.php:23
* @route '/my-account'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::show
* @see app/Http/Controllers/AccountController.php:23
* @route '/my-account'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountController::show
* @see app/Http/Controllers/AccountController.php:23
* @route '/my-account'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AccountController::showOrderHistory
* @see app/Http/Controllers/AccountController.php:43
* @route '/order-history'
*/
export const showOrderHistory = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showOrderHistory.url(options),
    method: 'get',
})

showOrderHistory.definition = {
    methods: ["get","head"],
    url: '/order-history',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AccountController::showOrderHistory
* @see app/Http/Controllers/AccountController.php:43
* @route '/order-history'
*/
showOrderHistory.url = (options?: RouteQueryOptions) => {
    return showOrderHistory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::showOrderHistory
* @see app/Http/Controllers/AccountController.php:43
* @route '/order-history'
*/
showOrderHistory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showOrderHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountController::showOrderHistory
* @see app/Http/Controllers/AccountController.php:43
* @route '/order-history'
*/
showOrderHistory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showOrderHistory.url(options),
    method: 'head',
})

const AccountController = { show, showOrderHistory }

export default AccountController