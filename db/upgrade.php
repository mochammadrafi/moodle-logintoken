<?php
/**
 * Upgrade script for Login Token plugin
 * 
 * @package    local_logintoken
 * @copyright  2025 Mochammad Rafi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade function to add login token functions to mobile app service
 * 
 * @param int $oldversion The old version of the plugin
 * @return bool
 */
function xmldb_local_logintoken_upgrade($oldversion) {
    global $DB;
    
    $dbman = $DB->get_manager();
    
    if ($oldversion < 2025120102) {
        // Add login token functions to Moodle Mobile App service
        $mobile_service = $DB->get_record('external_services', ['shortname' => 'moodle_mobile_app']);
        
        if ($mobile_service) {
            // Check if functions are already in mobile service
            $existing_login = $DB->get_record('external_services_functions', [
                'externalserviceid' => $mobile_service->id,
                'functionname' => 'local_logintoken_login_with_token'
            ]);
            
            $existing_validate = $DB->get_record('external_services_functions', [
                'externalserviceid' => $mobile_service->id,
                'functionname' => 'local_logintoken_validate_token'
            ]);
            
            if (!$existing_login) {
                $record = new stdClass();
                $record->externalserviceid = $mobile_service->id;
                $record->functionname = 'local_logintoken_login_with_token';
                $DB->insert_record('external_services_functions', $record);
            }
            
            if (!$existing_validate) {
                $record = new stdClass();
                $record->externalserviceid = $mobile_service->id;
                $record->functionname = 'local_logintoken_validate_token';
                $DB->insert_record('external_services_functions', $record);
            }
        }
        
        upgrade_plugin_savepoint(true, 2025120102, 'local', 'logintoken');
    }
    
    return true;
}
