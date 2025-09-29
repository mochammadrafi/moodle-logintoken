<?php
/**
 * Login Token API endpoint
 * 
 * This endpoint handles authentication using Moodle web service tokens (wstoken)
 * Usage: /auth/login.php?wstoken=xxxx
 * 
 * @package    local_logintoken
 * @copyright  2025 Mochammad Rafi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->libdir . '/authlib.php');

// Set content type to JSON
header('Content-Type: application/json');

// Get the wstoken and wantsurl from the request
$wstoken = optional_param('wstoken', '', PARAM_RAW);
$wantsurl = optional_param('wantsurl', '', PARAM_URL);

// Validate token parameter
if (empty($wstoken)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Missing wstoken parameter',
        'message' => 'Please provide a valid wstoken parameter'
    ]);
    exit;
}

try {
    // Validate the web service token
    $token = $DB->get_record('external_tokens', ['token' => $wstoken]);
    
    if (!$token) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid token',
            'message' => 'The provided wstoken is invalid or expired'
        ]);
        exit;
    }
    
    // Check if token is valid and not expired
    if ($token->validuntil && $token->validuntil < time()) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'Token expired',
            'message' => 'The provided wstoken has expired'
        ]);
        exit;
    }
    
    // Get the user associated with this token
    $user = $DB->get_record('user', ['id' => $token->userid, 'deleted' => 0, 'suspended' => 0]);
    
    if (!$user) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'User not found',
            'message' => 'User associated with this token not found or inactive'
        ]);
        exit;
    }
    
    // Complete user login
    complete_user_login($user);
    
    // Update token last access time
    $token->lastaccess = time();
    $DB->update_record('external_tokens', $token);
    
    // Get the redirect URL - prioritize wantsurl parameter, then session, then default
    if (!empty($wantsurl)) {
        $redirect_url = $wantsurl;
    } elseif (!empty($SESSION->wantsurl)) {
        $redirect_url = $SESSION->wantsurl;
    } else {
        $redirect_url = $CFG->wwwroot;
    }
    
    // Clear the wantsurl from session
    unset($SESSION->wantsurl);
    
    $auto_redirect = optional_param('redirect', false, PARAM_BOOL);
    
    // If auto_redirect is false, return JSON response
    if (!$auto_redirect) {
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'fullname' => fullname($user)
            ],
            'redirect_url' => $redirect_url,
            'session_id' => session_id()
        ]);
    } else {
        // Auto redirect to the specified URL
        header('Location: ' . $redirect_url);
        exit;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'message' => 'An error occurred during authentication: ' . $e->getMessage()
    ]);
}
