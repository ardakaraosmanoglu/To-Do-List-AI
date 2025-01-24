<?php
require_once 'config.php';

function get_optimized_title($original_title) {
    $api_key = 'AIzaSyDFsE4-xSSLKpu09sSr1BrJ0VNhnt008_M';
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key=" . $api_key;

    $prompt = "Task Title: " . $original_title . "\n";
    $prompt .= "Bu task Titleini iyileştir ve yazıldığı dilde tekrar yaz. Optimize ve Minimal olsun. Output olarak sadece başlığı ver başka tek bir kelime yazma.";

    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($data)
        ]
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        throw new Exception('API request failed');
    }

    $result = json_decode($response, true);
    
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        return trim($result['candidates'][0]['content']['parts'][0]['text']);
    }
    
    return $original_title; // Fallback to original title if API fails
}

function get_optimized_description($title, $original_description) {
    $api_key = 'AIzaSyDFsE4-xSSLKpu09sSr1BrJ0VNhnt008_M';
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key=" . $api_key;

    $prompt = "Task Title: " . $title . "\n";
    $prompt .= "Task Description: " . $original_description . "\n";
    $prompt .= "Bu task Descriptionını task titleını da göz önünde bulundurarak iyileştir ve yazıldığı dilde tekrar yaz. Optimize ve Minimal olsun. Output olarak sadece açıklamayı ver başka tek bir kelime yazma.";

    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($data)
        ]
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        throw new Exception('API request failed');
    }

    $result = json_decode($response, true);
    
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        return trim($result['candidates'][0]['content']['parts'][0]['text']);
    }
    
    return $original_description;
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (isset($data['title']) && !isset($data['description'])) {
            // Title optimization
            $optimized_title = get_optimized_title($data['title']);
            echo json_encode([
                'success' => true,
                'title' => $optimized_title
            ]);
        } elseif (isset($data['title']) && isset($data['description'])) {
            // Description optimization
            $optimized_description = get_optimized_description($data['title'], $data['description']);
            echo json_encode([
                'success' => true,
                'description' => $optimized_description
            ]);
        } else {
            throw new Exception('Invalid request parameters');
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} 