<?php
/**
 * Session Check API endpoint
 * 
 * This endpoint checks if a user is already logged in on their PC
 * Usage: /local/logintoken/auth/check_session.php
 * 
 * @package    local_logintoken
 * @copyright  2025 Mochammad Rafi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');

header('Content-Type: application/json');

try {
    if (!$USER || $USER->id <= 1) {
        echo json_encode([
            'logged_in' => false,
            'message' => 'No active session found',
            'user' => null,
            'session_info' => [
                'session_id' => session_id(),
                'session_start' => $_SESSION['SESSION_START'] ?? null,
                'last_activity' => $_SESSION['LAST_ACTIVITY'] ?? null
            ]
        ]);
        exit;
    }
    
    if ($USER->deleted || $USER->suspended) {
        echo json_encode([
            'logged_in' => false,
            'message' => 'User account is inactive',
            'user' => null,
            'session_info' => [
                'session_id' => session_id(),
                'session_start' => $_SESSION['SESSION_START'] ?? null,
                'last_activity' => $_SESSION['LAST_ACTIVITY'] ?? null
            ]
        ]);
        exit;
    }

    $session_info = [
        'session_id' => session_id(),
        'session_start' => $_SESSION['SESSION_START'] ?? null,
        'last_activity' => $_SESSION['LAST_ACTIVITY'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
    ];

    echo json_encode([
        'logged_in' => true,
        'message' => 'User is logged in',
        'user' => [
            'id' => $USER->id,
            'username' => $USER->username,
            'firstname' => $USER->firstname,
            'lastname' => $USER->lastname,
            'email' => $USER->email,
            'fullname' => fullname($USER)
        ],
        'session_info' => $session_info
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'logged_in' => false,
        'error' => 'Internal server error',
        'message' => 'An error occurred during session check: ' . $e->getMessage()
    ]);
}
