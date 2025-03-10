<?php
header("Content-Type: application/json");

$basePath = '/toolshare-server'; // Change this if your project is in a different subdirectory
$requestUri = str_replace($basePath, '', strtok($_SERVER['REQUEST_URI'], '?')); 

// Define route mappings
$routes = [
    '/api/login' => 'routes/login.php',
    '/api/items' => 'routes/items.php',
    '/api/categories' => 'routes/categories.php',
    '/api/user' => 'routes/user.php'
];

// Route handling
if (array_key_exists($requestUri, $routes)) {
    require $routes[$requestUri];
} else {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint not found"]);
}

