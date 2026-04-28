<?php

namespace App\Controllers;

class HomeController
{
    public function index(array $config, array $equipment): array
    {
        return [
            'title'      => 'Mini Equipment Reservation App',
            'app_name'   => $config['app']['name'],
            'organizer'  => $config['app']['organizer'],
            'app_env'    => $config['app']['env'],
            'app_debug'  => $config['app']['debug'] ? 'true' : 'false',
            'equipment'  => $equipment,
        ];
    }
}
