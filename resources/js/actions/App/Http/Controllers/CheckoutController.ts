import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CheckoutController::show
* @see app/Http/Controllers/CheckoutController.php:21
* @route '/review'
*/
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/review',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CheckoutController::show
* @see app/Http/Controllers/CheckoutController.php:21
* @route '/review'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CheckoutController::show
* @see app/Http/Controllers/CheckoutController.php:21
* @route '/review'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutController::show
* @see app/Http/Controllers/CheckoutController.php:21
* @route '/review'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CheckoutController::checkout
* @see app/Http/Controllers/CheckoutController.php:46
* @route '/checkout'
*/
export const checkout = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: checkout.url(options),
    method: 'get',
})

checkout.definition = {
    methods: ["get","head"],
    url: '/checkout',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CheckoutController::checkout
* @see app/Http/Controllers/CheckoutController.php:46
* @route '/checkout'
*/
checkout.url = (options?: RouteQueryOptions) => {
    return checkout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CheckoutController::checkout
* @see app/Http/Controllers/CheckoutController.php:46
* @route '/checkout'
*/
checkout.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: checkout.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutController::checkout
* @see app/Http/Controllers/CheckoutController.php:46
* @route '/checkout'
*/
checkout.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: checkout.url(options),
    method: 'head',
})

const CheckoutController = { show, checkout }

export default CheckoutController