<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Root Domain
    |--------------------------------------------------------------------------
    |
    | The platform's own domain. Requests to this host serve the Gallery Row
    | site itself; requests to "{slug}.{root_domain}" serve that store. Any
    | other host is looked up as a store's custom domain.
    |
    */

    'root_domain' => env('TENANCY_ROOT_DOMAIN', 'gallery-row.test'),

    /*
    |--------------------------------------------------------------------------
    | Reserved Subdomains
    |--------------------------------------------------------------------------
    |
    | Subdomains that never resolve to a store, even if a store has the slug.
    |
    */

    'reserved_subdomains' => ['www', 'admin', 'app', 'api', 'mail', 'static'],

];
