import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\SignInController::authenticate
* @see app/Http/Controllers/Admin/SignInController.php:15
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
* @see \App\Http\Controllers\Admin\SignInController::authenticate
* @see app/Http/Controllers/Admin/SignInController.php:15
* @route '/admin/authenticate'
*/
authenticate.url = (options?: RouteQueryOptions) => {
    return authenticate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\SignInController::authenticate
* @see app/Http/Controllers/Admin/SignInController.php:15
* @route '/admin/authenticate'
*/
authenticate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: authenticate.url(options),
    method: 'post',
})

const SignInController = { authenticate }

export default SignInController