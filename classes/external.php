<?php
/**
 * External API for Login Token web service
 * 
 * @package    local_logintoken
 * @copyright  2025 Mochammad Rafi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * External API class for Login Token web service
 */
class local_logintoken_external extends external_api {

    /**
     * Returns description of method parameters for login_with_token
     * 
     * @return external_function_parameters
     */
    public static function login_with_token_parameters() {
        return new external_function_parameters(
            array(
                'redirect_url' => new external_value(PARAM_URL, 'URL to redirect to after successful login', VALUE_DEFAULT, '')
            )
        );
    }

    /**
     * Login user with web service token
     * 
     * @param string $redirect_url Redirect URL after login
     * @return array Login result
     */
    public static function login_with_token($redirect_url = '') {
        global $DB, $SESSION, $CFG, $USER;

        // Validate parameters
        $params = self::validate_parameters(self::login_with_token_parameters(), array(
            'redirect_url' => $redirect_url
        ));

        // Get the current user (authenticated via wstoken)
        $user = $USER;
        
        if (!$user || $user->id <= 1) {
            throw new moodle_exception('usernotfound', 'local_logintoken', '', 'User not authenticated');
        }
        
        // Check if user is active
        if ($user->deleted || $user->suspended) {
            throw new moodle_exception('usernotfound', 'local_logintoken', '', 'User account is inactive');
        }
        
        // Complete user login (user is already logged in via web service)
        // This ensures proper session handling
        complete_user_login($user);
        
        // Set redirect URL
        $final_redirect_url = !empty($params['redirect_url']) ? $params['redirect_url'] : ($SESSION->wantsurl ?? $CFG->wwwroot);
        
        // Clear the wantsurl from session
        unset($SESSION->wantsurl);
        
        return array(
            'success' => true,
            'message' => 'Login successful',
            'user' => array(
                'id' => $user->id,
                'username' => $user->username,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'fullname' => fullname($user)
            ),
            'redirect_url' => $final_redirect_url,
            'session_id' => session_id()
        );
    }

    /**
     * Returns description of method result value for login_with_token
     * 
     * @return external_single_structure
     */
    public static function login_with_token_returns() {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Whether login was successful'),
                'message' => new external_value(PARAM_TEXT, 'Login result message'),
                'user' => new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'User ID'),
                        'username' => new external_value(PARAM_USERNAME, 'Username'),
                        'firstname' => new external_value(PARAM_TEXT, 'First name'),
                        'lastname' => new external_value(PARAM_TEXT, 'Last name'),
                        'email' => new external_value(PARAM_EMAIL, 'Email address'),
                        'fullname' => new external_value(PARAM_TEXT, 'Full name')
                    ),
                    'User information'
                ),
                'redirect_url' => new external_value(PARAM_URL, 'URL to redirect to after login'),
                'session_id' => new external_value(PARAM_RAW, 'Session ID')
            ),
            'Login result'
        );
    }

    /**
     * Returns description of method parameters for validate_token
     * 
     * @return external_function_parameters
     */
    public static function validate_token_parameters() {
        return new external_function_parameters(
            array()
        );
    }

    /**
     * Validate web service token
     * 
     * @return array Validation result
     */
    public static function validate_token() {
        global $DB, $USER;

        // Validate parameters
        $params = self::validate_parameters(self::validate_token_parameters(), array());

        // Check if user is authenticated
        if (!$USER || $USER->id <= 1) {
            return array(
                'valid' => false,
                'message' => 'User not authenticated'
            );
        }
        
        // Check if user is active
        if ($USER->deleted || $USER->suspended) {
            return array(
                'valid' => false,
                'message' => 'User account is inactive'
            );
        }
        
        return array(
            'valid' => true,
            'message' => 'Token is valid',
            'user' => array(
                'id' => $USER->id,
                'username' => $USER->username,
                'firstname' => $USER->firstname,
                'lastname' => $USER->lastname,
                'email' => $USER->email,
                'fullname' => fullname($USER)
            )
        );
    }

    /**
     * Returns description of method result value for validate_token
     * 
     * @return external_single_structure
     */
    public static function validate_token_returns() {
        return new external_single_structure(
            array(
                'valid' => new external_value(PARAM_BOOL, 'Whether token is valid'),
                'message' => new external_value(PARAM_TEXT, 'Validation message'),
                'user' => new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'User ID'),
                        'username' => new external_value(PARAM_USERNAME, 'Username'),
                        'firstname' => new external_value(PARAM_TEXT, 'First name'),
                        'lastname' => new external_value(PARAM_TEXT, 'Last name'),
                        'email' => new external_value(PARAM_EMAIL, 'Email address'),
                        'fullname' => new external_value(PARAM_TEXT, 'Full name')
                    ),
                    'User information',
                    VALUE_OPTIONAL
                ),
            ),
            'Token validation result'
        );
    }
}
