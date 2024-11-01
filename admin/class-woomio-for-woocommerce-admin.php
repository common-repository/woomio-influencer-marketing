<?php

/**
 * The admin-specific functionality of the plugin.
 */
class Woomio_WC_Admin {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;


	/**
	 * The version of this plugin.
	 */
	private $version;


	/**
	 * Add all admin page slugs here ...
	 */
	private $plugin_pages_slug = [
		'woomio-for-woocommerce',
		'woomio-for-woocommerce-token',
		'woomio-for-woocommerce-add-token',
	];


	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}


	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {

		// Add css to this plugin page only
		$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : "";
		if ( in_array( $page, $this->plugin_pages_slug ) ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woomio-for-woocommerce-admin.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'wfw-bootstrap-css', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'wfw-datatable-css', plugin_dir_url( __FILE__ ) . 'css/datatable-bs4.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'wfw-select2-css', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
		}

	}


	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
		// Add JS to this plugin page only
		$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : "";
		if ( in_array( $page, $this->plugin_pages_slug ) ) {
			wp_enqueue_script( 'wfw-bootstrap-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap.bundle.min.js', array(), $this->version, true );
			wp_enqueue_script( 'wfw-datatable-js', plugin_dir_url( __FILE__ ) . 'js/datatable.bs4.min.js', array(), $this->version, true );
			wp_enqueue_script( 'wfw-jqvalidate-js', plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js', array(), $this->version, true );
			wp_enqueue_script( 'wfw-select2-js', plugin_dir_url( __FILE__ ) . 'js/select2.full.min.js', array(), $this->version, true );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woomio-for-woocommerce-admin.js', array( 'jquery' ), $this->version, true );
		}

	}


	/**
	 * Add our custom menu for admin section
	 */
	public function wfw_admin_menu() {
		add_menu_page( __( "Woomio", "woomio-for-woocommerce" ),
			__( "Woomio", "woomio-for-woocommerce" ),
			'manage_options',
			'woomio-for-woocommerce',
			[ $this, 'wfw_admin_page' ],
			'dashicons-admin-site',
			250 );

		add_submenu_page(
			'woomio-for-woocommerce',
			__( 'Dashboard - Woomio for WooCommerce', "woomio-for-woocommerce" ),
			__( 'Dashboard', "woomio-for-woocommerce" ),
			'manage_options',
			'woomio-for-woocommerce',
			[ $this, 'wfw_admin_page' ]
		);

		add_submenu_page(
			'woomio-for-woocommerce',
			__( 'Tokens', "woomio-for-woocommerce" ),
			__( 'Tokens', "woomio-for-woocommerce" ),
			'manage_options',
			'woomio-for-woocommerce-token',
			[ $this, 'wfw_admin_token_page' ]
		);

		add_submenu_page(
			'woomio-for-woocommerce',
			__( 'Add Token', "woomio-for-woocommerce" ),
			__( 'Add Token', "woomio-for-woocommerce" ),
			'manage_options',
			'woomio-for-woocommerce-add-token',
			[ $this, 'wfw_admin_add_token_page' ]
		);

	}


	/**
	 * Return admin dashboard view
	 */
	public function wfw_admin_page() {
		global $wpdb;
		$sales = $wpdb->get_results( "SELECT * FROM " . WFW_TOKEN_SALES_TABLE . " as tblTokenSales LEFT JOIN " . WFW_TOKEN_TABLE . " as tblToken ON tblTokenSales.token_id = tblToken.id" );

		$salesDetail = [];
		foreach ( $sales as $sale ) {
			$campaign_name = $sale->campaign_name ?? '';
			$campaign_url  = $sale->campaign_url ?? '';

			$salesDetail[ $sale->token . '||' . $campaign_name . '||' . $campaign_url ][ $sale->coupon ][ $sale->currency ][] = $sale->amount;
		}

		require_once 'partials/template-dashboard.php';

	}


	/**
	 * Responsible for admin token index page
	 */
	public function wfw_admin_token_page() {
		global $wpdb;
		$tokens = $wpdb->get_results( "
		SELECT  tblToken.*,
	        (
		        SELECT  COUNT(*)
		        FROM " . WFW_COUPON_TOKEN_TABLE . " as tblCouponToken
		        WHERE   tblCouponToken.token_id = tblToken.id
	        ) AS couponCount
		FROM " . WFW_TOKEN_TABLE . " as tblToken" );

		require_once 'partials/template-token-list.php';
	}


	/**
	 * Responsible for admin Token add and edit page
	 */
	public function wfw_admin_add_token_page() {
		global $wpdb;

		$token = [
			'name'        => '',
			'expire_time' => '',
		];

		$associated_coupon_ids       = [];
		$other_associated_coupon_ids = [];

		if ( isset( $_REQUEST['token_id'] ) && isset( $_REQUEST['action'] ) ) {

			$token_id = isset( $_REQUEST['token_id'] ) ? intval( $_REQUEST['token_id'] ) : "";

			$token = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM " . WFW_TOKEN_TABLE . " WHERE id = %d LIMIT 1", [ $token_id ] )
				, ARRAY_A );
			$token = array_shift( $token );

			// get associated coupons id
			$coupon_ids = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM " . WFW_COUPON_TOKEN_TABLE . " WHERE token_id = %d", [ $token_id ] ) );


			foreach ( $coupon_ids as $c ) {
				$associated_coupon_ids[] = $c->coupon_id;
			}

			// get coupon associated with other tokens
			$una_coupon_ids = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . WFW_COUPON_TOKEN_TABLE . " WHERE token_id != %d", [ $token_id ] ) );

			foreach ( $una_coupon_ids as $c ) {
				$other_associated_coupon_ids[] = $c->coupon_id;
			}

		} else {
			// get coupon associated with other tokens
			$una_coupon_ids = $wpdb->get_results( "SELECT * FROM " . WFW_COUPON_TOKEN_TABLE );

			foreach ( $una_coupon_ids as $c ) {
				$other_associated_coupon_ids[] = $c->coupon_id;
			}
		}

		$args = array(
			'posts_per_page' => - 1,
			'orderby'        => 'title',
			'order'          => 'asc',
			'post_type'      => 'shop_coupon',
			'post_status'    => 'publish',
		);

		$coupons = get_posts( $args );

		$coupons_codes = [];
		foreach ( $coupons as $coupon ) {
			$c             = new WC_Coupon( $coupon->post_title );
			$code_amount   = $c->get_amount();
			$description   = $c->get_description();
			$discount_type = $c->get_discount_type();
			$date_expires  = ! empty( $c->get_date_expires() ) ? $c->get_date_expires()->date( "Y-m-d" ) : __( 'N/A', 'woomio-for-woocommerce' );
			$d             = new WC_Discounts();
			if ( $d->is_coupon_valid( $c ) === true ) {
				// only show coupons that are not associated with given token
				if ( ! in_array( $coupon->ID, $other_associated_coupon_ids ) ) {
					$coupons_codes[ $coupon->ID ] = [
						'code'         => $coupon->post_title,
						'code_type'    => ucwords( str_replace( '_', ' ', $discount_type ) ) . " ($code_amount)",
						'description'  => $description,
						'date_expires' => $date_expires,
					];
				}
			}
		}

		require_once 'partials/template-token-add.php';

	}


	/**
	 * Post data to Woomio
	 * Data is posted to Woomio only when Woocommerce Order status is set to COMPLETE.
	 */
	public function payment_completed( $order_id ) {
		global $wpdb;

		// Check if Woocommerce is active
		if ( function_exists( 'wc_get_order' ) ) {
			$token    = null;
			$coupon   = null;
			$order    = wc_get_order( $order_id );
			$items    = $order->get_items();
			$subTotal = $order->get_subtotal();
			$discount = $order->get_total_discount();
			$currency = $order->get_currency();
			$amount   = $subTotal - $discount;

			foreach ( $items as $item ) {
				$metas = $item->get_meta_data();
				foreach ( $metas as $meta ) {
					$data  = $meta->get_data();
					$key   = $data['key'];
					$value = $data['value'];
					if ( $key == WFW_TOKEN ) {
						$token = $value;
					}
					if ( $key == WFW_COUPON ) {
						$coupon = $value;
					}
				}
			}

			// If we have token and coupon add to Woomio
			$used_coupon_codes_arr = $order->get_coupon_codes();

			if ( ! is_null( $token ) && ! is_null( $coupon ) && in_array( strtolower($coupon), $used_coupon_codes_arr ) ) {
				$response = $this->addToWoomio( $amount, $coupon, $token );
				$this->add_to_sales( $amount, $coupon, $currency, $order_id, $token );
				smn_wfw_log( "Referral URL Used ---- Woomio response: " . "(" . $response['response']['code'] . ") " . $response['response']['message'] . " --- AMOUNT: $amount, COUPON: $coupon, TOKEN: $token" );

			} // If no token and coupon is found, lets check if coupon was used in checkout
			else {

				foreach ( $used_coupon_codes_arr as $coupon_code ) {
					$coupon_code = strtolower( $coupon_code );
					$coupon_id   = $wpdb->get_var(
						$wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s LIMIT 1", $coupon_code, 'shop_coupon' )
					);
					if ( $coupon_id ) {
						$coupon_token = $wpdb->get_results(
							$wpdb->prepare( "SELECT * FROM " . WFW_COUPON_TOKEN_TABLE . " WHERE coupon_id = %d LIMIT 1", [ $coupon_id ] )
							, ARRAY_A );
						if ( ! empty( $coupon_token ) ) {
							$coupon_token = array_shift( $coupon_token );
							$token_id     = $coupon_token['token_id'] ?? null;
							if ( $token_id ) {
								$token      = $wpdb->get_results(
									$wpdb->prepare( "SELECT * FROM " . WFW_TOKEN_TABLE . " WHERE id = %d LIMIT 1", [ $token_id ] )
									, ARRAY_A );
								$token      = array_shift( $token );
								$token_name = $token['name'] ?? null;
								if ( $token_name ) {
									$response = $this->addToWoomio( $amount, $coupon_code, $token_name );
									$this->add_to_sales( $amount, $coupon_code, $currency, $order_id, $token_name );
									smn_wfw_log( "Discount code used --- Woomio response: " . "(" . $response['response']['code'] . ") " . $response['response']['message'] . " --- AMOUNT: $amount, COUPON: $coupon_code, TOKEN: $token_name" );
									break;
								}
							}
						}
					}
				}

			}

		}
	}


	/**
	 * Post sales record to Woomio
	 */
	private function addToWoomio( $amount, $coupon, $token ) {
		$url = 'https://api.woomio.com/api/endpoints/RegisterTransaction?amount=' . $amount . '&couponCode=' . $coupon . '&token=' . $token . '&url=' . home_url();
		return wp_remote_get($url);
	}


	/**
	 * Add sales to sales table
	 */
	private function add_to_sales( $amount, $coupon, $currency, $order_id, $token ) {
		// add data to sales table
		global $wpdb;
		$isAlreadyAdded = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM " . WFW_TOKEN_SALES_TABLE . " WHERE token = %s AND coupon = %s AND order_id = %d LIMIT 1", [
				$token,
				$coupon,
				$order_id
			] )
			, ARRAY_A );

		if ( count( $isAlreadyAdded ) == 0 ) {

			$token_detail = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM " . WFW_TOKEN_TABLE . " WHERE name = %s LIMIT 1", [ $token ] )
				, ARRAY_A );
			$token_detail = array_shift( $token_detail );

			$args = array(
				'name'      => $coupon,
				'post_type' => 'shop_coupon',
			);

			$coupons       = get_posts( $args );
			$coupon_detail = array_shift( $coupons );

			$wpdb->insert( WFW_TOKEN_SALES_TABLE,
				[
					'token'     => strtolower( $token ),
					'token_id'  => $token_detail['id'] ?? null,
					'coupon'    => strtolower( $coupon ),
					'coupon_id' => $coupon_detail->ID ?? null,
					'currency'  => $currency,
					'amount'    => $amount,
					'order_id'  => $order_id,
				]
			);
		}
	}


	/**
	 * Handles Token/Coupon display at Order page
	 */
	public function order_item_display_token_coupon_meta_key( $display_key, $meta, $item ) {
		if ( is_admin() ) {
			if ( $meta->key === WFW_TOKEN ) {
				$display_key = __( "Woomio Token", "woomio-for-woocommerce" );
			}

			if ( $meta->key === WFW_COUPON ) {
				$display_key = __( "Woomio Coupon", "woomio-for-woocommerce" );
			}

			return $display_key;
		}
	}


	/**
	 * Let's not display token/coupon as meta data in Order page
	 */
	public function hide_woomio_order_itemmeta( $arr ) {
		$arr[] = WFW_TOKEN;
		$arr[] = WFW_COUPON;

		return $arr;
	}


	/**
	 * Handles Token Add,Update and Delete actions from admin
	 */
	public function handle_ajax_requests_admin() {
		global $wpdb;
		$param = isset( $_REQUEST['param'] ) ? sanitize_text_field( $_REQUEST['param'] ) : "";
		if ( ! empty( $param ) ) {
			if ( $param == 'cu_token' ) {
				$campaign_name = isset( $_REQUEST['campaign_name'] ) ? sanitize_text_field( $_REQUEST['campaign_name'] ) : "";
				$campaign_url  = isset( $_REQUEST['campaign_url'] ) ? sanitize_text_field( $_REQUEST['campaign_url'] ) : "";
				$name          = isset( $_REQUEST['name'] ) ? sanitize_text_field( $_REQUEST['name'] ) : "";
				$expire_time   = isset( $_REQUEST['expire_time'] ) ? intval( sanitize_text_field( $_REQUEST['expire_time'] ) ) : "";
				$coupons       = isset( $_REQUEST['coupons'] ) ? $_REQUEST['coupons'] : [];
				$id            = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : "";

				// Validate inputs
				$msg = '';
				if ( empty( $campaign_name ) || empty( $campaign_url ) || empty( $name ) || empty( $expire_time ) || empty( $coupons ) ) {
					$msg = __( 'Campaing name, Campaign URL, Token, Coupon and Expire time are required.', "woomio-for-woocommerce" );
					wp_send_json( [ 'status' => 0, 'message' => $msg ], 200 );
				}

				if ( ! filter_var( $campaign_url, FILTER_VALIDATE_URL ) ) {
					$msg .= __( 'Please enter a valid campaign URL.', "woomio-for-woocommerce" );
					wp_send_json( [ 'status' => 0, 'message' => $msg ], 200 );
				}

				if ( ! is_int( $expire_time ) ) {
					$msg .= __( 'Expired time should be an integer.', "woomio-for-woocommerce" );
					wp_send_json( [ 'status' => 0, 'message' => $msg ], 200 );
				}

				if ( strlen( $campaign_name ) > 250 || strlen( $name ) > 250 ) {
					$msg .= __( 'Campaign name and Token cannot be more than 250 characters long.', "woomio-for-woocommerce" );
					wp_send_json( [ 'status' => 0, 'message' => $msg ], 200 );
				}

				// Check if token is already there
				if ( empty( $id ) ) {
					$token = $wpdb->get_results(
						$wpdb->prepare( "SELECT * FROM " . WFW_TOKEN_TABLE . " WHERE name = %s LIMIT 1", [ $name ] )
						, ARRAY_A );
					$token = array_shift( $token );
					if ( $token ) {
						$msg .= __( 'Token already added.', "woomio-for-woocommerce" );
						wp_send_json( [ 'status' => 0, 'message' => $msg ], 200 );
					}
				}

				$fields = [
					'user_id'       => get_current_user_id(),
					'name'          => $name,
					'campaign_name' => $campaign_name,
					'campaign_url'  => $campaign_url,
					'expire_time'   => $expire_time,
				];

				if ( ! empty( $id ) ) {
					// Update
					$message           = [
						'status'  => 1,
						'message' => __( 'Token updated successfully.', "woomio-for-woocommerce" ),
						'action'  => 'edit',
					];
					$fields['updated'] = current_time( 'mysql' );

					// Delete old coupon/token association
					$wpdb->query( "DELETE FROM " . WFW_COUPON_TOKEN_TABLE . " WHERE token_id = $id" );

					$wpdb->update( WFW_TOKEN_TABLE,
						$fields,
						[ 'id' => $id ] );
				} else {
					// Create
					$message = [
						'status'  => 1,
						'message' => __( 'Token added successfully.', "woomio-for-woocommerce" ),
						'action'  => 'create'
					];
					$wpdb->insert( WFW_TOKEN_TABLE,
						$fields
					);
				}

				if ( empty( $id ) ) {
					$token_id = $wpdb->insert_id;
					foreach ( $coupons as $coupon ) {
						$wpdb->insert( WFW_COUPON_TOKEN_TABLE,
							[ 'token_id' => $token_id, 'coupon_id' => $coupon ]
						);
					}
				} else {
					foreach ( $coupons as $coupon ) {
						$wpdb->insert( WFW_COUPON_TOKEN_TABLE,
							[ 'token_id' => $id, 'coupon_id' => $coupon ]
						);
					}
				}

				if ( $wpdb->result ) {
					wp_send_json( $message, 200 );
				} else {
					wp_send_json( [
						'status'  => 0,
						'message' => __( 'Failed to create token.', "woomio-for-woocommerce" )
					], 200 );
				}

			} // Handle delete token request
			else if ( $param == "delete_token" ) {
				$token_id = isset( $_REQUEST['token_id'] ) ? intval( $_REQUEST['token_id'] ) : "";
				if ( $token_id > 0 ) {
					$wpdb->query( "DELETE FROM " . WFW_COUPON_TOKEN_TABLE . " WHERE token_id = $token_id" );
					$wpdb->delete( WFW_TOKEN_TABLE, [ 'id' => $token_id ] );
					wp_send_json( [
						'status'  => 1,
						'message' => __( 'Token deleted successfully.', "woomio-for-woocommerce" )
					], 200 );
				} else {
					wp_send_json( [
						'status'  => 0,
						'message' => __( 'Failed to delete token.', "woomio-for-woocommerce" )
					], 500 );
				}
			}
		}
	}


	/**
	 * Add admin notice if Woocommerce is not active
	 */
	public function wfw_admin_notices() {
		$pluginList = get_option( 'active_plugins' );
		$woo_plugin = 'woocommerce/woocommerce.php';

		if ( ! in_array( $woo_plugin, $pluginList ) ) {
			echo '<div class="notice notice-warning is-dismissible">
		             <p><strong>' . ucwords( str_replace( '-', ' ', $this->plugin_name ) ) . '</strong> only works with store created with <a target="_blank" href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a>.</p>
		         </div>';
		}
	}


	/**
	 * Delete coupon association if coupon is deleted at Woocommerce
	 */
	public function delete_coupon_association( $postId, $post ) {
		global $wpdb;
		if ( $post->post_type == 'shop_coupon' ) {
			$wpdb->query( "DELETE FROM " . WFW_COUPON_TOKEN_TABLE . " WHERE coupon_id = $postId" );
		}
	}


	/**
	 * Plugin database update checks
	 */
	public function wfw_update_db_check() {
		if ( get_site_option( 'wfw_db_version' ) != WFW_VERSION ) {
			require_once WFW_PLUGIN_PATH . '/includes/class-woomio-for-woocommerce-activator.php';
			Woomio_WC_Activator::activate();
		}
	}

}
