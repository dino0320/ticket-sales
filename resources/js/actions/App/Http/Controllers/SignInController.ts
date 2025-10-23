import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\SignInController::authenticate
* @see app/Http/Controllers/SignInController.php:14
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
* @see \App\Http\Controllers\SignInController::authenticate
* @see app/Http/Controllers/SignInController.php:14
* @route '/authenticate'
*/
authenticate.url = (options?: RouteQueryOptions) => {
    return authenticate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SignInController::authenticate
* @see app/Http/Controllers/SignInController.php:14
* @route '/authenticate'
*/
authenticate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: authenticate.url(options),
    method: 'post',
})

const SignInController = { authenticate }

export default SignInController