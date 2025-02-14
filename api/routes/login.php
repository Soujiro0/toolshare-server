<?php
// api/routes/login.php
header("Content-Type: application/json");
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    if (isset($data->username) && isset($data->password)) {
        $controller = new AuthController();
        $controller->login($data->username, $data->password);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Username and password required"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}
