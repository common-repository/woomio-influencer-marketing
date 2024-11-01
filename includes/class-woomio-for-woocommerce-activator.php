<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 */
class Woomio_WC_Activator {

	/**
	 * Create required tables for this plugin.
	 */
	public static function activate() {

		global $wpdb;
		$installed_ver = get_option( "wfw_db_version" );
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$charset_collate = $wpdb->get_charset_collate();


		if ( $installed_ver != WFW_VERSION ) {

			$sql = "CREATE TABLE " . WFW_TOKEN_TABLE . " (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  user_id int(11) DEFAULT NULL,
			  campaign_name varchar(255) DEFAULT NULL,
			  campaign_url text DEFAULT NULL,
			  name varchar(255) DEFAULT NULL,
			  expire_time int(11) DEFAULT NULL,
			  created datetime NOT NULL DEFAULT current_timestamp(),
			  updated datetime NOT NULL DEFAULT current_timestamp(),
			  PRIMARY KEY (id)
			) $charset_collate;";
			dbDelta( $sql );


			$sql = "CREATE TABLE " . WFW_TOKEN_SALES_TABLE . " (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  token varchar(255) DEFAULT NULL,
			  token_id int(11) DEFAULT NULL,
			  coupon varchar(255) DEFAULT NULL,
			  coupon_id int(11) DEFAULT NULL,
			  currency varchar(100) DEFAULT NULL,
			  amount decimal(10,2) DEFAULT NULL,
			  order_id int(11) DEFAULT NULL,
			  created datetime NOT NULL DEFAULT current_timestamp(),
			  updated datetime NOT NULL DEFAULT current_timestamp(),
    		  PRIMARY KEY (id),
              KEY token_idx (token),
              KEY coupon_idx (coupon),
              KEY token_id_idx (token_id),
              KEY coupon_id_idx (coupon_id)
			) $charset_collate;";
			dbDelta( $sql );


			$sql = "CREATE TABLE " . WFW_COUPON_TOKEN_TABLE . " (
			  id int(11) NOT NULL AUTO_INCREMENT,
			  coupon_id int(11) DEFAULT NULL,
			  token_id int(11) DEFAULT NULL,
    		  PRIMARY KEY (id),
              KEY token_id_idx (token_id),
              KEY coupon_id_idx (coupon_id)
			) $charset_collate;";
			dbDelta( $sql );


			update_option( 'wfw_db_version', WFW_VERSION );

		}


	}

}
