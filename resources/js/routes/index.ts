import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../wayfinder'
/**
* @see routes/web.php:14
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
* @see routes/web.php:14
* @route '/sign-up'
*/
signUp.url = (options?: RouteQueryOptions) => {
    return signUp.definition.url + queryParams(options)
}

/**
* @see routes/web.php:14
* @route '/sign-up'
*/
signUp.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: signUp.url(options),
    method: 'get',
})

/**
* @see routes/web.php:14
* @route '/sign-up'
*/
signUp.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: signUp.url(options),
    method: 'head',
})

/**
* @see routes/web.php:20
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
* @see routes/web.php:20
* @route '/sign-in'
*/
signIn.url = (options?: RouteQueryOptions) => {
    return signIn.definition.url + queryParams(options)
}

/**
* @see routes/web.php:20
* @route '/sign-in'
*/
signIn.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: signIn.url(options),
    method: 'get',
})

/**
* @see routes/web.php:20
* @route '/sign-in'
*/
signIn.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: signIn.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CheckoutController::review
* @see app/Http/Controllers/CheckoutController.php:33
* @route '/review'
*/
export const review = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: review.url(options),
    method: 'get',
})

review.definition = {
    methods: ["get","head"],
    url: '/review',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CheckoutController::review
* @see app/Http/Controllers/CheckoutController.php:33
* @route '/review'
*/
review.url = (options?: RouteQueryOptions) => {
    return review.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CheckoutController::review
* @see app/Http/Controllers/CheckoutController.php:33
* @route '/review'
*/
review.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: review.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutController::review
* @see app/Http/Controllers/CheckoutController.php:33
* @route '/review'
*/
review.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: review.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CheckoutController::checkout
* @see app/Http/Controllers/CheckoutController.php:55
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
* @see app/Http/Controllers/CheckoutController.php:55
* @route '/checkout'
*/
checkout.url = (options?: RouteQueryOptions) => {
    return checkout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CheckoutController::checkout
* @see app/Http/Controllers/CheckoutController.php:55
* @route '/checkout'
*/
checkout.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: checkout.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutController::checkout
* @see app/Http/Controllers/CheckoutController.php:55
* @route '/checkout'
*/
checkout.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: checkout.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CheckoutController::checkoutSuccess
* @see app/Http/Controllers/CheckoutController.php:101
* @route '/checkout/success'
*/
export const checkoutSuccess = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: checkoutSuccess.url(options),
    method: 'get',
})

checkoutSuccess.definition = {
    methods: ["get","head"],
    url: '/checkout/success',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CheckoutController::checkoutSuccess
* @see app/Http/Controllers/CheckoutController.php:101
* @route '/checkout/success'
*/
checkoutSuccess.url = (options?: RouteQueryOptions) => {
    return checkoutSuccess.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CheckoutController::checkoutSuccess
* @see app/Http/Controllers/CheckoutController.php:101
* @route '/checkout/success'
*/
checkoutSuccess.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: checkoutSuccess.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CheckoutController::checkoutSuccess
* @see app/Http/Controllers/CheckoutController.php:101
* @route '/checkout/success'
*/
checkoutSuccess.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: checkoutSuccess.url(options),
    method: 'head',
})

/**
* @see routes/web.php:52
* @route '/reset-password'
*/
export const resetPassword = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resetPassword.url(options),
    method: 'get',
})

resetPassword.definition = {
    methods: ["get","head"],
    url: '/reset-password',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:52
* @route '/reset-password'
*/
resetPassword.url = (options?: RouteQueryOptions) => {
    return resetPassword.definition.url + queryParams(options)
}

/**
* @see routes/web.php:52
* @route '/reset-password'
*/
resetPassword.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resetPassword.url(options),
    method: 'get',
})

/**
* @see routes/web.php:52
* @route '/reset-password'
*/
resetPassword.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: resetPassword.url(options),
    method: 'head',
})

/**
* @see routes/web.php:60
* @route '/organizer-application'
*/
export const organizerApplication = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: organizerApplication.url(options),
    method: 'get',
})

organizerApplication.definition = {
    methods: ["get","head"],
    url: '/organizer-application',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:60
* @route '/organizer-application'
*/
organizerApplication.url = (options?: RouteQueryOptions) => {
    return organizerApplication.definition.url + queryParams(options)
}

/**
* @see routes/web.php:60
* @route '/organizer-application'
*/
organizerApplication.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: organizerApplication.url(options),
    method: 'get',
})

/**
* @see routes/web.php:60
* @route '/organizer-application'
*/
organizerApplication.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: organizerApplication.url(options),
    method: 'head',
})

/**
* @see routes/web.php:68
* @route '/issue-ticket'
*/
export const issueTicket = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: issueTicket.url(options),
    method: 'get',
})

issueTicket.definition = {
    methods: ["get","head"],
    url: '/issue-ticket',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:68
* @route '/issue-ticket'
*/
issueTicket.url = (options?: RouteQueryOptions) => {
    return issueTicket.definition.url + queryParams(options)
}

/**
* @see routes/web.php:68
* @route '/issue-ticket'
*/
issueTicket.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: issueTicket.url(options),
    method: 'get',
})

/**
* @see routes/web.php:68
* @route '/issue-ticket'
*/
issueTicket.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: issueTicket.url(options),
    method: 'head',
})

