<?php
// api/index.php
header("Content-Type: application/json");

$request = $_SERVER['REQUEST_URI'];
$method  = $_SERVER['REQUEST_METHOD'];

// Basic routing logic (for a production system, consider using a routing library)
if (strpos($request, '/api/login') !== false) {
    require 'routes/login.php';
} elseif (strpos($request, '/api/borrow-requests') !== false) {
    require 'routes/borrow.php';
} elseif (strpos($request, '/api/items') !== false) {
    require 'routes/items.php';
} elseif (strpos($request, '/api/return') !== false) {
    require 'routes/return.php';
} else {
    http_response_code(404);
    echo json_encode(["message" => "Endpoint not found"]);
}
