<?php

namespace App\Controllers;

use App\Support\Response;

class ReservationController
{
    public function store(array $equipment, array $config): void
    {
        // --- Bước 1: Kiểm tra Content-Type ---
        $headers     = function_exists('getallheaders') ? getallheaders() : [];
        $contentType = $headers['Content-Type']
            ?? $headers['content-type']
            ?? ($_SERVER['CONTENT_TYPE'] ?? '');

        if (!str_contains(strtolower($contentType), 'application/json')) {
            Response::json(415, [
                'error'   => 'Unsupported Media Type',
                'message' => 'Content-Type must be application/json',
            ]);
        }

        // --- Bước 2: Đọc và parse JSON body ---
        $raw     = file_get_contents('php://input');
        $payload = json_decode($raw, true);

        if (!is_array($payload)) {
            Response::json(400, [
                'error'   => 'Bad Request',
                'message' => 'Invalid JSON body',
            ]);
        }

        // --- Bước 3: Lấy dữ liệu từ payload ---
        $equipmentId  = $payload['equipment_id']  ?? null;
        $borrowerName = trim($payload['borrower_name'] ?? '');
        $email        = trim($payload['email']        ?? '');
        $quantity     = (int) ($payload['quantity']   ?? 0);
        $borrowDate   = trim($payload['borrow_date']  ?? '');

        // --- Bước 4: Kiểm tra field bắt buộc ---
        if (!$equipmentId || $borrowerName === '' || $email === ''
            || $quantity <= 0 || $borrowDate === '') {
            Response::json(422, [
                'error'   => 'Unprocessable Content',
                'message' => 'equipment_id, borrower_name, email, quantity, borrow_date are required and must be valid',
            ]);
        }

        // --- Bước 5: Kiểm tra số lượng tối đa cho phép ---
        $maxAllowed = $config['app']['max_reservations_per_request'];
        if ($quantity > $maxAllowed) {
            Response::json(422, [
                'error'   => 'Unprocessable Content',
                'message' => "Quantity exceeds allowed limit of {$maxAllowed} per request",
            ]);
        }

        // --- Bước 6: Tìm thiết bị trong dữ liệu ---
        $selectedItem = null;
        foreach ($equipment as $item) {
            if ($item['id'] === (int) $equipmentId) {
                $selectedItem = $item;
                break;
            }
        }

        if (!$selectedItem) {
            Response::json(422, [
                'error'   => 'Unprocessable Content',
                'message' => 'Selected equipment does not exist',
            ]);
        }

        // --- Bước 7: Kiểm tra còn đủ số lượng không ---
        if ($selectedItem['available_units'] < $quantity) {
            Response::json(422, [
                'error'   => 'Unprocessable Content',
                'message' => 'Not enough units available for this equipment',
            ]);
        }

        // --- Bước 8: Tạo reservation thành công ---
        $reservationId = time();

        Response::json(201, [
            'message' => 'Reservation created successfully',
            'data'    => [
                'reservation_id' => $reservationId,
                'borrower_name'  => $borrowerName,
                'email'          => $email,
                'equipment_id'   => (int) $equipmentId,
                'equipment_name' => $selectedItem['name'],
                'quantity'       => $quantity,
                'borrow_date'    => $borrowDate,
            ],
        ], [
            'Location' => '/reservations/' . $reservationId,
        ]);
    }

    public function options(): void
    {
        http_response_code(204);
        header('Allow: POST, OPTIONS');
        exit;
    }
}