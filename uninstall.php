<?php

/**
 * Fired when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}


/**
 * Delete options we have saved
 */
delete_option( 'wfw_db_version' );


/**
 * Drop tables because users is deleting the plugin
 */
global $wpdb;

$tables = [
	$wpdb->prefix . 'wfw_token',
	$wpdb->prefix . 'wfw_token_sales',
	$wpdb->prefix . 'wfw_coupon_token',
];

foreach ( $tables as $table ) {
	$wpdb->query( "DROP TABLE IF EXISTS $table" );
}
