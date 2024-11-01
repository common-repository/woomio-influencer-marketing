<?php

/**
 * Plugin Name:       Woomio (Influencer marketing)
 * Plugin URI:        https://home.woomio.com/
 * Description:       Woomio Plugin allows Shop Owners to add & track purchases made with token/coupon (campaign/affiliate) combinations which we supply to our affiliates.
 * Version:           1.0.4
 * Author:            iClickSee
 * Author URI:        https://iclicksee.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woomio-for-woocommerce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
defined( 'WFW_VERSION' ) or define( 'WFW_VERSION', '1.0.4' );


/**
 * Define Plugin related constant
 */
defined( 'WFW_TOKEN' ) or define( 'WFW_TOKEN', 'wmtoken' );
defined( 'WFW_COUPON' ) or define( 'WFW_COUPON', 'wmcoupon' );


/**
 * Plugin paths
 */
defined( 'WFW_PLUGIN_URL' ) or define( 'WFW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
defined( 'WFW_PLUGIN_PATH' ) or define( 'WFW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );


/**
 * Tables name
 */
global $wpdb;
defined( 'WFW_TOKEN_TABLE' ) or define( 'WFW_TOKEN_TABLE', $wpdb->prefix . 'wfw_token' );
defined( 'WFW_TOKEN_SALES_TABLE' ) or define( 'WFW_TOKEN_SALES_TABLE', $wpdb->prefix . 'wfw_token_sales' );
defined( 'WFW_COUPON_TOKEN_TABLE' ) or define( 'WFW_COUPON_TOKEN_TABLE', $wpdb->prefix . 'wfw_coupon_token' );


/**
 * Plugin activation
 */
function activate_woomio_for_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woomio-for-woocommerce-activator.php';
	Woomio_WC_Activator::activate();
}

/**
 * Plugin deactivation.
 */
function deactivate_woomio_for_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woomio-for-woocommerce-deactivator.php';
	Woomio_WC_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woomio_for_woocommerce' );
register_deactivation_hook( __FILE__, 'deactivate_woomio_for_woocommerce' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woomio-for-woocommerce.php';

/**
 * Log plugin specific messages
 */
if ( ! function_exists( 'smn_wfw_log' ) ) {
	function smn_wfw_log( $message ) {
		$log_file = 'debug.log';
		$path_arr = explode( DIRECTORY_SEPARATOR, dirname( __FILE__ ) );
		$dir_name = end( $path_arr );
		$log_path = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $dir_name . DIRECTORY_SEPARATOR . $log_file;
		$message  = "[" . date( 'Y-m-d h:i:j a' ) . "] " . $message . " \n";
		file_put_contents( $log_path, $message, FILE_APPEND );
		chmod( $log_path, 0755 );
	}
}

/**
 * Begins execution of the plugin.
 */
function run_woomio_for_woocommerce() {

	$plugin = new Woomio_WC();
	$plugin->run();

}

run_woomio_for_woocommerce();
