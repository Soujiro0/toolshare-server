<?php
// api/auth/middleware.php

require_once 'auth.php';

/**
 * @param string|array|null $requiredRoles A string for a single role (e.g. 'admin'),
 *                                         or an array of roles (e.g. ['admin', 'super_admin']),
 *                                         or null for any authenticated user.
 */
function requireAuth($requiredRoles = null)
{
    // Retrieve the token from the Authorization header
    $token = Auth::getBearerToken();
    if (!$token) {
        http_response_code(401);
        echo json_encode(["message" => "Access Denied. No token provided."]);
        exit;
    }

    // Validate the token and decode payload
    $decoded = Auth::validateToken($token);
    if (!$decoded) {
        http_response_code(401);
        echo json_encode(["message" => "Access Denied. Invalid token."]);
        exit;
    }

    // If roles are specified, check that the user's role is allowed
    if ($requiredRoles) {
        // Handle multiple roles
        if (is_array($requiredRoles)) {
            if (!in_array($decoded->role, $requiredRoles)) {
                http_response_code(403);
                echo json_encode(["message" => "Forbidden. Insufficient permissions."]);
                exit;
            }
        }
        // Handle a single role
        else {
            if (!isset($decoded->role) || $decoded->role !== $requiredRoles) {
                http_response_code(403);
                echo json_encode(["message" => "Forbidden. Insufficient permissions."]);
                exit;
            }
        }
    }

    // Optionally store user info for later use
    $_REQUEST['user'] = $decoded;

    // Return the decoded token for further processing if needed
    return $decoded;
}
