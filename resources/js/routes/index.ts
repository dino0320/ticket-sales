import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../wayfinder'
/**
* @see routes/web.php:10
* @route '/sign-up'
*/
export const signUp = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: signUp.url(options),
    method: 'get',
})

signUp.definition = {
    methods: ["get","head"],
    url: '/sign-up',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:10
* @route '/sign-up'
*/
signUp.url = (options?: RouteQueryOptions) => {
    return signUp.definition.url + queryParams(options)
}

/**
* @see routes/web.php:10
* @route '/sign-up'
*/
signUp.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: signUp.url(options),
    method: 'get',
})

/**
* @see routes/web.php:10
* @route '/sign-up'
*/
signUp.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: signUp.url(options),
    method: 'head',
})

/**
* @see routes/web.php:16
* @route '/sign-in'
*/
export const signIn = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: signIn.url(options),
    method: 'get',
})

signIn.definition = {
    methods: ["get","head"],
    url: '/sign-in',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:16
* @route '/sign-in'
*/
signIn.url = (options?: RouteQueryOptions) => {
    return signIn.definition.url + queryParams(options)
}

/**
* @see routes/web.php:16
* @route '/sign-in'
*/
signIn.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: signIn.url(options),
    method: 'get',
})

/**
* @see routes/web.php:16
* @route '/sign-in'
*/
signIn.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: signIn.url(options),
    method: 'head',
})

