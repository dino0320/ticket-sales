import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\UserCartController::store
* @see app/Http/Controllers/UserCartController.php:23
* @route '/cart'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/cart',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserCartController::store
* @see app/Http/Controllers/UserCartController.php:23
* @route '/cart'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserCartController::store
* @see app/Http/Controllers/UserCartController.php:23
* @route '/cart'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\UserCartController::show
* @see app/Http/Controllers/UserCartController.php:56
* @route '/cart'
*/
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/cart',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\UserCartController::show
* @see app/Http/Controllers/UserCartController.php:56
* @route '/cart'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserCartController::show
* @see app/Http/Controllers/UserCartController.php:56
* @route '/cart'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\UserCartController::show
* @see app/Http/Controllers/UserCartController.php:56
* @route '/cart'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

const UserCartController = { store, show }

export default UserCartController