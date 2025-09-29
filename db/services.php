<?php
/**
 * Web service definitions for Login Token plugin
 * 
 * @package    local_logintoken
 * @copyright  2025 Mochammad Rafi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'local_logintoken_login_with_token' => array(
        'classname'     => 'local_logintoken_external',
        'methodname'    => 'login_with_token',
        'classpath'     => 'local/logintoken/classes/external.php',
        'type'          => 'read',
        'capabilities' => '',
        'ajax'          => true,
        'loginrequired' => false,
    ),
    'local_logintoken_validate_token' => array(
        'classname'     => 'local_logintoken_external',
        'methodname'    => 'validate_token',
        'classpath'     => 'local/logintoken/classes/external.php',
        'type'          => 'read',
        'capabilities' => '',
        'ajax'          => true,
        'loginrequired' => false,
    ),
);

$services = array(
    'Login Token Service' => array(
        'functions' => array(
            'local_logintoken_login_with_token',
            'local_logintoken_validate_token'
        ),
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'logintoken',
        'downloadfiles' => 0,
        'uploadfiles' => 0
    )
);
