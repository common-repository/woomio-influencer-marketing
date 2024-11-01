<?php

/**
 * The public-facing functionality of the plugin.
 */
class Woomio_WC_Public {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;


	/**
	 * The version of this plugin.
	 */
	private $version;


	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles() {
		// No front facing CSS required for now
	}


	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	public function enqueue_scripts() {
		// No front facing JS required for now
	}


	/**
	 * Get token and coupon from query string and set cookie
	 */
	public function get_query_vars_from_url() {
		if ( ! is_admin() ) {

			if ( isset( $_GET[ WFW_TOKEN ] ) && isset( $_GET[ WFW_COUPON ] ) ) {
				$token  = sanitize_text_field( $_GET[ WFW_TOKEN ] );
				$coupon = sanitize_text_field( $_GET[ WFW_COUPON ] );

				// Get token TTL or set 30 days by default
				global $wpdb;
				$table     = WFW_TOKEN_TABLE;
				$tokenData = $wpdb->get_results(
					$wpdb->prepare( "SELECT * FROM $table WHERE name = %s LIMIT 1", [ $token ] )
					, ARRAY_A );
				$tokenData = array_shift( $tokenData );

				$ttl = DAY_IN_SECONDS * 30;
				if ( isset( $tokenData['expire_time'] ) ) {
					$ttl = intval( $tokenData['expire_time'] ) * DAY_IN_SECONDS;
				}

				if ( ! empty( $tokenData ) && ! empty( $token ) && ! empty( $coupon ) ) {
					$cookie_ttl = current_time( 'timestamp' ) + $ttl;
					setcookie( WFW_TOKEN, $token, $cookie_ttl, COOKIEPATH, COOKIE_DOMAIN );
					setcookie( WFW_COUPON, $coupon, $cookie_ttl, COOKIEPATH, COOKIE_DOMAIN );
				}

			}

		}
	}


	/**
	 * Get token/coupon and assign to item for record
	 */
	public function add_token_coupon_to_order( $item, $cart_item_key, $values, $order ) {
		if ( ! is_admin() ) {
			$token  = isset( $_COOKIE[ WFW_TOKEN ] ) ? sanitize_text_field( $_COOKIE[ WFW_TOKEN ] ) : false;
			$coupon = isset( $_COOKIE[ WFW_COUPON ] ) ? sanitize_text_field( $_COOKIE[ WFW_COUPON ] ) : false;

			if ( $token && $coupon ) {
				$item->update_meta_data( WFW_TOKEN, $token );
				$item->update_meta_data( WFW_COUPON, $coupon );
			}
		}
	}


	/**
	 * Let's not display token/coupon as meta data in Order complete page
	 */
	public function hide_woomio_order_itemmeta( $arr ) {
		$temp_metas = [];
		foreach ( $arr as $key => $meta ) {
			if ( isset( $meta->key ) && ! in_array( $meta->key, [
					WFW_TOKEN,
					WFW_COUPON,
				] ) ) {
				$temp_metas[ $key ] = $meta;
			}
		}

		return $temp_metas;
	}


	/**
	 * Apply discount code on checkout if Woomio token/coupon is available
	 */
	public function apply_discount_coupon_on_checkout() {
		if ( ! is_admin() ) {

			$token  = isset( $_COOKIE[ WFW_TOKEN ] ) ? sanitize_text_field( $_COOKIE[ WFW_TOKEN ] ) : false;
			$coupon = isset( $_COOKIE[ WFW_COUPON ] ) ? sanitize_text_field( $_COOKIE[ WFW_COUPON ] ) : false;

			global $woocommerce;
			$applied_coupons = $woocommerce->cart->applied_coupons;
			if ( count( $applied_coupons ) > 0 ) {
				if ( in_array( strtolower( $coupon ), $applied_coupons ) ) {
					if ( count( $applied_coupons ) > 1 ) {
						$woocommerce->cart->remove_coupon( $coupon );
						$token = $coupon = false;
					}
				} else {
					$token = $coupon = false;
				}
			}

			if ( $token && $coupon ) {

				global $wpdb;

				// get token detail
				$token_detail = $wpdb->get_results(
					$wpdb->prepare( "SELECT * FROM " . WFW_TOKEN_TABLE . " WHERE name = %s LIMIT 1", [ $token ] )
					, ARRAY_A );
				$token_detail = array_shift( $token_detail );
				$token_id     = $token_detail['id'] ?? null;

				if ( $token_id ) {
					// Get discount code ID associated with token
					$discount_code_detail = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type= %s", $coupon, 'shop_coupon' ) );

					$discount_code_id = $discount_code_detail ?? null;

					if ( $discount_code_id ) {
						// Get discount code
						$coupon = get_post( $discount_code_id );
						if ( $coupon->post_type == 'shop_coupon' ) {
							$c = new WC_Coupon( $coupon->post_title );
							$d = new WC_Discounts();
							if ( $d->is_coupon_valid( $c ) === true ) {
								// only apply coupon if its valid
								$discount_code = $coupon->post_title;

								if ( $discount_code ) {

									// remove any previously applied coupon
									$woocommerce->cart->remove_coupons();

									if ( $woocommerce->cart->cart_contents_total >= 1 ) {
										$woocommerce->cart->add_discount( $discount_code );
									}
								}

							}
						}

					}
				}

			}

		}
	}

}
