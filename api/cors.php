<?php
// api/cors.php

// Allow any origin - adjust this in production for tighter security
header("Access-Control-Allow-Origin: *");

// Allow the following HTTP methods
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");

// Allow the following headers (client must send these headers if needed)
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// If it's an OPTIONS request, stop further execution and return 200 OK
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
