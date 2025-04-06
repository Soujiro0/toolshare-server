<?php
header("Content-Type: application/json");

// Adjust base path if your API is not in the root directory
$basePath = '/toolshare-server'; // Update based on actual directory if needed

// Normalize URI (remove base path and query string)
$requestUri = str_replace($basePath, '', strtok($_SERVER['REQUEST_URI'], '?'));

// Define route mappings
$routes = [
    '/api/login'      => 'routes/login.php',
    '/api/items'      => 'routes/items.php',
    '/api/categories' => 'routes/categories.php',
    '/api/user'       => 'routes/user.php'
];

// Handle route
if (isset($routes[$requestUri])) {
    require __DIR__ . '/' . $routes[$requestUri];
} else {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint not found", "requested" => $requestUri]);
}
