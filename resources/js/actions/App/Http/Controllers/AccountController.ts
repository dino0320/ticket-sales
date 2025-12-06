import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AccountController::show
* @see app/Http/Controllers/AccountController.php:27
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
* @see app/Http/Controllers/AccountController.php:27
* @route '/my-account'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::show
* @see app/Http/Controllers/AccountController.php:27
* @route '/my-account'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountController::show
* @see app/Http/Controllers/AccountController.php:27
* @route '/my-account'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AccountController::resetPassword
* @see app/Http/Controllers/AccountController.php:65
* @route '/reset-password'
*/
export const resetPassword = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resetPassword.url(options),
    method: 'post',
})

resetPassword.definition = {
    methods: ["post"],
    url: '/reset-password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AccountController::resetPassword
* @see app/Http/Controllers/AccountController.php:65
* @route '/reset-password'
*/
resetPassword.url = (options?: RouteQueryOptions) => {
    return resetPassword.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::resetPassword
* @see app/Http/Controllers/AccountController.php:65
* @route '/reset-password'
*/
resetPassword.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resetPassword.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AccountController::showOrderHistory
* @see app/Http/Controllers/AccountController.php:47
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
* @see app/Http/Controllers/AccountController.php:47
* @route '/order-history'
*/
showOrderHistory.url = (options?: RouteQueryOptions) => {
    return showOrderHistory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::showOrderHistory
* @see app/Http/Controllers/AccountController.php:47
* @route '/order-history'
*/
showOrderHistory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showOrderHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountController::showOrderHistory
* @see app/Http/Controllers/AccountController.php:47
* @route '/order-history'
*/
showOrderHistory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showOrderHistory.url(options),
    method: 'head',
})

const AccountController = { show, resetPassword, showOrderHistory }

export default AccountController