<?php

namespace App\Controllers;

use App\Support\Response;

class EquipmentController
{
    public function index(array $equipment): void
    {
        Response::json(200, [
            'message' => 'Equipment list',
            'data'    => $equipment,
        ]);
    }

    public function head(): void
    {
        http_response_code(200);
        header('Content-Type: application/json; charset=UTF-8');
        exit;
    }
}
