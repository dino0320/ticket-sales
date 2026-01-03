import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AccountController::authenticate
* @see app/Http/Controllers/Admin/AccountController.php:16
* @route '/admin/authenticate'
*/
export const authenticate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: authenticate.url(options),
    method: 'post',
})

authenticate.definition = {
    methods: ["post"],
    url: '/admin/authenticate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AccountController::authenticate
* @see app/Http/Controllers/Admin/AccountController.php:16
* @route '/admin/authenticate'
*/
authenticate.url = (options?: RouteQueryOptions) => {
    return authenticate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AccountController::authenticate
* @see app/Http/Controllers/Admin/AccountController.php:16
* @route '/admin/authenticate'
*/
authenticate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: authenticate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\AccountController::signOut
* @see app/Http/Controllers/Admin/AccountController.php:37
* @route '/admin/sign-out'
*/
export const signOut = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: signOut.url(options),
    method: 'post',
})

signOut.definition = {
    methods: ["post"],
    url: '/admin/sign-out',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AccountController::signOut
* @see app/Http/Controllers/Admin/AccountController.php:37
* @route '/admin/sign-out'
*/
signOut.url = (options?: RouteQueryOptions) => {
    return signOut.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AccountController::signOut
* @see app/Http/Controllers/Admin/AccountController.php:37
* @route '/admin/sign-out'
*/
signOut.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: signOut.url(options),
    method: 'post',
})

const AccountController = { authenticate, signOut }

export default AccountController