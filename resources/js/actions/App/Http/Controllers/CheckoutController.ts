import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CheckoutController::show
* @see app/Http/Controllers/CheckoutController.php:28
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
* @see app/Http/Controllers/CheckoutController.php:28
* @route '/review'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CheckoutController::show
* @see app/Http/Controllers/CheckoutController.php:28
* @route '/review'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutController::show
* @see app/Http/Controllers/CheckoutController.php:28
* @route '/review'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CheckoutController::checkout
* @see app/Http/Controllers/CheckoutController.php:50
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
* @see app/Http/Controllers/CheckoutController.php:50
* @route '/checkout'
*/
checkout.url = (options?: RouteQueryOptions) => {
    return checkout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CheckoutController::checkout
* @see app/Http/Controllers/CheckoutController.php:50
* @route '/checkout'
*/
checkout.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: checkout.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutController::checkout
* @see app/Http/Controllers/CheckoutController.php:50
* @route '/checkout'
*/
checkout.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: checkout.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CheckoutController::showCheckoutSuccess
* @see app/Http/Controllers/CheckoutController.php:89
* @route '/checkout/success'
*/
export const showCheckoutSuccess = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showCheckoutSuccess.url(options),
    method: 'get',
})

showCheckoutSuccess.definition = {
    methods: ["get","head"],
    url: '/checkout/success',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CheckoutController::showCheckoutSuccess
* @see app/Http/Controllers/CheckoutController.php:89
* @route '/checkout/success'
*/
showCheckoutSuccess.url = (options?: RouteQueryOptions) => {
    return showCheckoutSuccess.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CheckoutController::showCheckoutSuccess
* @see app/Http/Controllers/CheckoutController.php:89
* @route '/checkout/success'
*/
showCheckoutSuccess.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showCheckoutSuccess.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutController::showCheckoutSuccess
* @see app/Http/Controllers/CheckoutController.php:89
* @route '/checkout/success'
*/
showCheckoutSuccess.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showCheckoutSuccess.url(options),
    method: 'head',
})

const CheckoutController = { show, checkout, showCheckoutSuccess }

export default CheckoutController