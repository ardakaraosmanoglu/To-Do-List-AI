<?php
require_once 'config.php';
require_once 'auth.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) {
        throw new Exception('Invalid request data');
    }

    if (empty($data['username']) || empty($data['password']) || empty($data['email'])) {
        throw new Exception('All fields are required');
    }

    if (register_user($data['username'], $data['password'], $data['email'])) {
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful'
        ]);
    } else {
        throw new Exception('Registration failed');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 