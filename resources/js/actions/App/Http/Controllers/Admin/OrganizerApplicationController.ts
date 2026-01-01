import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\OrganizerApplicationController::index
* @see app/Http/Controllers/Admin/OrganizerApplicationController.php:24
* @route '/admin/organizer-applications'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/organizer-applications',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\OrganizerApplicationController::index
* @see app/Http/Controllers/Admin/OrganizerApplicationController.php:24
* @route '/admin/organizer-applications'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\OrganizerApplicationController::index
* @see app/Http/Controllers/Admin/OrganizerApplicationController.php:24
* @route '/admin/organizer-applications'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\OrganizerApplicationController::index
* @see app/Http/Controllers/Admin/OrganizerApplicationController.php:24
* @route '/admin/organizer-applications'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\OrganizerApplicationController::show
* @see app/Http/Controllers/Admin/OrganizerApplicationController.php:41
* @route '/admin/organizer-applications/{user_organizer_application}'
*/
export const show = (args: { user_organizer_application: string | number } | [user_organizer_application: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/admin/organizer-applications/{user_organizer_application}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\OrganizerApplicationController::show
* @see app/Http/Controllers/Admin/OrganizerApplicationController.php:41
* @route '/admin/organizer-applications/{user_organizer_application}'
*/
show.url = (args: { user_organizer_application: string | number } | [user_organizer_application: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user_organizer_application: args }
    }

    if (Array.isArray(args)) {
        args = {
            user_organizer_application: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user_organizer_application: args.user_organizer_application,
    }

    return show.definition.url
            .replace('{user_organizer_application}', parsedArgs.user_organizer_application.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\OrganizerApplicationController::show
* @see app/Http/Controllers/Admin/OrganizerApplicationController.php:41
* @route '/admin/organizer-applications/{user_organizer_application}'
*/
show.get = (args: { user_organizer_application: string | number } | [user_organizer_application: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\OrganizerApplicationController::show
* @see app/Http/Controllers/Admin/OrganizerApplicationController.php:41
* @route '/admin/organizer-applications/{user_organizer_application}'
*/
show.head = (args: { user_organizer_application: string | number } | [user_organizer_application: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\OrganizerApplicationController::updateStatus
* @see app/Http/Controllers/Admin/OrganizerApplicationController.php:54
* @route '/admin/organizer-applications/{user_organizer_application}'
*/
export const updateStatus = (args: { user_organizer_application: string | number } | [user_organizer_application: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateStatus.url(args, options),
    method: 'put',
})

updateStatus.definition = {
    methods: ["put"],
    url: '/admin/organizer-applications/{user_organizer_application}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\OrganizerApplicationController::updateStatus
* @see app/Http/Controllers/Admin/OrganizerApplicationController.php:54
* @route '/admin/organizer-applications/{user_organizer_application}'
*/
updateStatus.url = (args: { user_organizer_application: string | number } | [user_organizer_application: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user_organizer_application: args }
    }

    if (Array.isArray(args)) {
        args = {
            user_organizer_application: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        user_organizer_application: args.user_organizer_application,
    }

    return updateStatus.definition.url
            .replace('{user_organizer_application}', parsedArgs.user_organizer_application.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\OrganizerApplicationController::updateStatus
* @see app/Http/Controllers/Admin/OrganizerApplicationController.php:54
* @route '/admin/organizer-applications/{user_organizer_application}'
*/
updateStatus.put = (args: { user_organizer_application: string | number } | [user_organizer_application: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateStatus.url(args, options),
    method: 'put',
})

const OrganizerApplicationController = { index, show, updateStatus }

export default OrganizerApplicationController