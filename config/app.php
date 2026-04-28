<?php

use App\Support\Env;

return [
    'app' => [
        'name'                        => Env::get('APP_NAME', 'Equipment Reservation'),
        'env'                         => Env::get('APP_ENV', 'prod'),
        'debug'                       => Env::bool('APP_DEBUG', false),
        'url'                         => Env::get('APP_URL', 'http://localhost:8000'),
        'organizer'                   => Env::get('ORGANIZER_NAME', 'Lab Center'),
        'max_reservations_per_request'=> Env::int('MAX_RESERVATIONS_PER_REQUEST', 1),
    ],
];
