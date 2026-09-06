<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Apollo.io API Key
    |--------------------------------------------------------------------------
    |
    | Find it under Settings > Integrations > API in your Apollo.io account.
    |
    */
    'api_key' => env('APOLLO_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Pagination Per Page
    |--------------------------------------------------------------------------
    |
    | Used as the default "per_page" for search endpoints when none is given.
    |
    */
    'default_per_page' => env('APOLLO_DEFAULT_PER_PAGE', 25),

];
