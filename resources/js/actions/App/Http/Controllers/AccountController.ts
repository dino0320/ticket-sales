import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AccountController::register
* @see app/Http/Controllers/AccountController.php:35
* @route '/register'
*/
export const register = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: register.url(options),
    method: 'post',
})

register.definition = {
    methods: ["post"],
    url: '/register',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AccountController::register
* @see app/Http/Controllers/AccountController.php:35
* @route '/register'
*/
register.url = (options?: RouteQueryOptions) => {
    return register.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::register
* @see app/Http/Controllers/AccountController.php:35
* @route '/register'
*/
register.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: register.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AccountController::authenticate
* @see app/Http/Controllers/AccountController.php:72
* @route '/authenticate'
*/
export const authenticate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: authenticate.url(options),
    method: 'post',
})

authenticate.definition = {
    methods: ["post"],
    url: '/authenticate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AccountController::authenticate
* @see app/Http/Controllers/AccountController.php:72
* @route '/authenticate'
*/
authenticate.url = (options?: RouteQueryOptions) => {
    return authenticate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::authenticate
* @see app/Http/Controllers/AccountController.php:72
* @route '/authenticate'
*/
authenticate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: authenticate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AccountController::show
* @see app/Http/Controllers/AccountController.php:96
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
* @see app/Http/Controllers/AccountController.php:96
* @route '/my-account'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::show
* @see app/Http/Controllers/AccountController.php:96
* @route '/my-account'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountController::show
* @see app/Http/Controllers/AccountController.php:96
* @route '/my-account'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AccountController::resetPassword
* @see app/Http/Controllers/AccountController.php:142
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
* @see app/Http/Controllers/AccountController.php:142
* @route '/reset-password'
*/
resetPassword.url = (options?: RouteQueryOptions) => {
    return resetPassword.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::resetPassword
* @see app/Http/Controllers/AccountController.php:142
* @route '/reset-password'
*/
resetPassword.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resetPassword.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AccountController::showOrderHistory
* @see app/Http/Controllers/AccountController.php:124
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
* @see app/Http/Controllers/AccountController.php:124
* @route '/order-history'
*/
showOrderHistory.url = (options?: RouteQueryOptions) => {
    return showOrderHistory.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::showOrderHistory
* @see app/Http/Controllers/AccountController.php:124
* @route '/order-history'
*/
showOrderHistory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showOrderHistory.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountController::showOrderHistory
* @see app/Http/Controllers/AccountController.php:124
* @route '/order-history'
*/
showOrderHistory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showOrderHistory.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AccountController::applyToBeOrganizer
* @see app/Http/Controllers/AccountController.php:179
* @route '/organizer-application'
*/
export const applyToBeOrganizer = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: applyToBeOrganizer.url(options),
    method: 'post',
})

applyToBeOrganizer.definition = {
    methods: ["post"],
    url: '/organizer-application',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AccountController::applyToBeOrganizer
* @see app/Http/Controllers/AccountController.php:179
* @route '/organizer-application'
*/
applyToBeOrganizer.url = (options?: RouteQueryOptions) => {
    return applyToBeOrganizer.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::applyToBeOrganizer
* @see app/Http/Controllers/AccountController.php:179
* @route '/organizer-application'
*/
applyToBeOrganizer.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: applyToBeOrganizer.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AccountController::showIssuedTickets
* @see app/Http/Controllers/AccountController.php:221
* @route '/issued-tickets'
*/
export const showIssuedTickets = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showIssuedTickets.url(options),
    method: 'get',
})

showIssuedTickets.definition = {
    methods: ["get","head"],
    url: '/issued-tickets',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AccountController::showIssuedTickets
* @see app/Http/Controllers/AccountController.php:221
* @route '/issued-tickets'
*/
showIssuedTickets.url = (options?: RouteQueryOptions) => {
    return showIssuedTickets.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::showIssuedTickets
* @see app/Http/Controllers/AccountController.php:221
* @route '/issued-tickets'
*/
showIssuedTickets.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showIssuedTickets.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountController::showIssuedTickets
* @see app/Http/Controllers/AccountController.php:221
* @route '/issued-tickets'
*/
showIssuedTickets.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showIssuedTickets.url(options),
    method: 'head',
})

const AccountController = { register, authenticate, show, resetPassword, showOrderHistory, applyToBeOrganizer, showIssuedTickets }

export default AccountController