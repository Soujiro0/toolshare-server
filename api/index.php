<?php
header("Content-Type: application/json");

$requestUri = strtok($_SERVER['REQUEST_URI'], '?'); // Remove query strings

// Define route mappings
$routes = [
    '/api/login' => 'routes/login.php',
    '/api/items' => 'routes/items.php',
    '/api/categories' => 'routes/categories.php'
];

// Route handling
if (array_key_exists($requestUri, $routes)) {
    require $routes[$requestUri];
} else {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint not found"]);
}

