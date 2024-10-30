<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://connecticallc.com
 * @since      1.0.0
 *
 * @package    Infosniper
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  exit;
}

$options = get_option('infosniper');

if ($options['delete-database']) {
  global $wpdb;
  $table_name = $wpdb->prefix . "infoSniperAddresses";
  $sql = "DROP TABLE IF EXISTS $table_name";
  $wpdb->query($sql);
  delete_option('infosniper_db_v1_0_1');
}

if ($options['delete-options']) {
  delete_option('infosniper');
  delete_option('infosniper_invalid_key');
  delete_option('infosniper_key_no_queries');
  delete_option('infosniper_queries');
}
