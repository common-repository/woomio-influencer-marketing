<?php

/**
 * The file that defines the core plugin class
 */
class Woomio_WC {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct() {
		if ( defined( 'WFW_VERSION' ) ) {
			$this->version = WFW_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'woomio-influencer-marketing';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woomio-for-woocommerce-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woomio-for-woocommerce-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woomio-for-woocommerce-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woomio-for-woocommerce-public.php';
		$this->loader = new Woomio_WC_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 */
	private function set_locale() {

		$plugin_i18n = new Woomio_WC_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Woomio_WC_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wfw_admin_menu' );
		$this->loader->add_action( 'woocommerce_order_status_completed', $plugin_admin, 'payment_completed' );
		$this->loader->add_filter( 'woocommerce_order_item_display_meta_key', $plugin_admin, 'order_item_display_token_coupon_meta_key', 10, 3 );
		$this->loader->add_filter( 'woocommerce_hidden_order_itemmeta', $plugin_admin, 'hide_woomio_order_itemmeta' );
		$this->loader->add_action( "wp_ajax_wfw_admin_ajax", $plugin_admin, 'handle_ajax_requests_admin' );
		$this->loader->add_filter( 'plugins_loaded', $plugin_admin, 'wfw_update_db_check' );
		$this->loader->add_filter( 'admin_notices', $plugin_admin, 'wfw_admin_notices' );
		$this->loader->add_action( "after_delete_post", $plugin_admin, 'delete_coupon_association', 10, 2 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 */
	private function define_public_hooks() {

		$plugin_public = new Woomio_WC_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'get_query_vars_from_url' );
		$this->loader->add_action( 'woocommerce_checkout_create_order_line_item', $plugin_public, 'add_token_coupon_to_order', 10, 4 );
		$this->loader->add_filter( 'woocommerce_order_item_get_formatted_meta_data', $plugin_public, 'hide_woomio_order_itemmeta' );
		$this->loader->add_filter( 'woocommerce_before_cart', $plugin_public, 'apply_discount_coupon_on_checkout' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
