<?php
/**
 * Plugin Name: One Page Checkout for EDD
 * Plugin URI: https://addonmaster.com/one-page-checkout-for-edd/
 * Description: Simply reduce the process of purchasing using One Page Checkout for Easy Digital Downloads. 
 * Author: AddonMaster
 * Author URI: https://addonmaster.com
 * Version: 1.0.0
 * Text Domain: onepage-checkout-for-edd
 * Domain Path: /lang
 * EDD tested up to: 3.1.0.3
 *
 */

/**
 * Including Plugin file for security
 * Include_once
 *
 * @since 1.0.0
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Defines
define( 'ONEPAGE_CHECKOUT_EDD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ONEPAGE_CHECKOUT_EDD_VERSION', '1.0.0' );

/**
 *	Plugin Main Class
 */
if ( ! class_exists( 'OnePageCheckoutForEDD' ) ) {
	class OnePageCheckoutForEDD{

		/**
		 * Constructor
		 */
		public function __construct() {
			// Loaded textdomain
			add_action('plugins_loaded', array( $this, 'plugin_loaded_action' ), 10, 2);

			// Enqueue frontend scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'edd_ajax_override' ), 15 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Admin Scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

			// Added plugin action link
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );

			// Functions
			add_action( 'init', array( $this, 'includes' ) );

			// Layout
			add_action( 'wp_footer', array( $this, 'layout' ) );

			// License Inactive Notice
			add_action( 'one-page-checkout-pro-for-edd_license_inactive_notice', array( $this, 'inactive_notice' ) );

			// Register Options Page Submenu
			add_action( 'admin_menu', array( $this, 'add_options_page' ), 110 );
		}

		/**
		 * Includes required files
		 */
		public function includes(){
			// General Functions
			require_once( dirname( __FILE__ ) . '/inc/functions.php' );

		}

		/**
		 * Adds plugin action links.
		 */
		function action_links( $links ) {
			$plugin_links = array(
				'<a href="' . esc_url( admin_url( 'edit.php?post_type=download&page=one-page-checkout-for-edd' ) ) . '">' . esc_html__( 'Settings', 'onepage-checkout-for-edd' ) . '</a>',
				'<a href="' . esc_url( admin_url( 'options-general.php?page=one-page-checkout-pro-for-edd-license' ) ) . '">' . esc_html__( 'License Activation', 'onepage-checkout-for-edd' ) . '</a>',
				'<a href="?update=force-check">' . esc_html__( 'Check for Updates', 'onepage-checkout-for-edd' ) . '</a>',
				'<a href="' . esc_url( 'https://addonmaster.com/docs/onepage-checkout-for-edd/available-options/' ) . '" target="_blank">' . esc_html__( 'Documentation', 'onepage-checkout-for-edd' ) . '</a>',
			);
			return array_merge( $plugin_links, $links );
		}

		/**
		 * Plugin Loaded Action
		 */
		function plugin_loaded_action() {

			// Loading Text Domain for Internationalization
			load_plugin_textdomain( 'onepage-checkout-for-edd', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );

		}

		/**
		 * Enqueue Frontend Scripts
		 */
		function enqueue_scripts() {

			// Styles
		    wp_enqueue_style( 'onepage-checkout-for-edd', ONEPAGE_CHECKOUT_EDD_PLUGIN_URL . 'assets/css/frontend.css', null, ONEPAGE_CHECKOUT_EDD_VERSION );

		    // Scripts
		    wp_enqueue_script( 'onepage-checkout-for-edd', ONEPAGE_CHECKOUT_EDD_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), ONEPAGE_CHECKOUT_EDD_VERSION );
			
			// Localizations
			wp_localize_script( 'onepage-checkout-for-edd', 'opcfedd_ajax_vars',
            	array(
            	    'nonce'   => wp_create_nonce( 'eddnstant_nonce' ),
            	    'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
            	)
            );
		}

		/**
		 * Enqueue Admin Scripts
		 */
		function admin_scripts() {
			// Add the color picker css file       
			wp_enqueue_style( 'wp-color-picker' ); 
			wp_enqueue_style( 'onepage-checkout-for-edd-admin', ONEPAGE_CHECKOUT_EDD_PLUGIN_URL . 'assets/css/admin.css', null, ONEPAGE_CHECKOUT_EDD_VERSION );
		}

		/**
		 * The Layout for display
		 */
		function layout(){
			if ( !class_exists( 'Easy_Digital_Downloads' ) ) {
		        return;
		    }

		    // Layout File
			include_once dirname( __FILE__ ) . '/inc/layout.php';
		}

		/**
		 * EDD Ajax JS override
		 */
		function edd_ajax_override() {

			// Use minified libraries if SCRIPT_DEBUG is turned off
			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			$has_purchase_links = false;
			if ( ( ! empty( $post->post_content ) && ( has_shortcode( $post->post_content, 'purchase_link' ) || has_shortcode( $post->post_content, 'downloads' ) ) ) || is_post_type_archive( 'download' ) ) {
				$has_purchase_links = true;
			}

			$in_footer = edd_scripts_in_footer();

			// Load AJAX scripts, if enabled
			if ( ! edd_is_ajax_disabled() ) {

				wp_enqueue_script( 'edd-ajax' );

				wp_localize_script( 'edd-ajax', 'edd_scripts', apply_filters( 'edd_ajax_script_vars', array(
					'ajaxurl'                 => esc_url(edd_get_ajax_url()),
					'position_in_cart'        => isset( $position ) ? $position : -1,
					'has_purchase_links'      => $has_purchase_links,
					'already_in_cart_message' => __('You have already added this item to your cart','onepage-checkout-for-edd' ), // Item already in the cart message
					'empty_cart_message'      => __('Your cart is empty','onepage-checkout-for-edd' ), // Item already in the cart message
					'loading'                 => __('Loading','onepage-checkout-for-edd' ) , // General loading message
					'select_option'           => __('Please select an option','onepage-checkout-for-edd' ) , // Variable pricing error with multi-purchase option enabled
					'is_checkout'             => edd_is_checkout() ? '1' : '0',
					'default_gateway'         => edd_get_default_gateway(),
					'redirect_to_checkout'    => ( edd_straight_to_checkout() || edd_is_checkout() ) ? '1' : '0',
					'checkout_page'           => esc_url(edd_get_checkout_uri()),
					'permalinks'              => get_option( 'permalink_structure' ) ? '1' : '0',
					'quantities_enabled'      => edd_item_quantities_enabled(),
					'taxes_enabled'           => edd_use_taxes() ? '1' : '0', // Adding here for widget, but leaving in checkout vars for backcompat
				) ) );
			}

		}

		// Inactive Notice
		public function inactive_notice( $args ) { 
			?>
			<div class="notice notice-error is-dismissible">
				<div class="am--message-inner" style="padding: 5px;">
					<div class="am--message-content">
						<h3 style=" font-weight: normal; margin-bottom: 0px">You're using <strong>One Page Checkout for EDD</strong> but no license key has been entered. Please add your <a class="am-tab-nav" href="<?php echo admin_url('options-general.php?page=one-page-checkout-pro-for-edd-license'); ?>">license key</a> if you want to continue using the plugin.</h3>
						<p><?php printf( esc_html( 'If you don\'t have license key, you can get one from %s. You can get your purchased key from %s page.', 'ald' ), "<a href='https://addonmaster.com/one-page-checkout-for-edd/' target='_blank'>Here</a>", "<a href='https://portal.addonmaster.com/my-account/' target='_blank'>My Account</a>") ?></p>
					</div>
				</div>
			</div>
			<?php 
		}

		// Add Options Page Submenu
		public function add_options_page() {
			add_submenu_page( 
				'edit.php?post_type=download', 
				__( 'One Page Checkout for EDD', 'onepage-checkout-for-edd' ),
				__( 'One Page Checkout', 'onepage-checkout-for-edd' ),
				'manage_options', 
				'one-page-checkout-for-edd', 
				array( $this, 'options_page' )
			);
		}

		// Options Page
		public function options_page() {
			// Include option-panel.php
			include_once dirname( __FILE__ ) . '/inc/option-panel.php';
		}

	} // End Class
} // Endif

/**
 * EDD Plugin inactive Notice
 */
if ( !function_exists('one_page_checkout_for_edd_inactive_notice_warn') ) {
	function one_page_checkout_for_edd_inactive_notice_warn() {
		?>
		<div class="notice notice-warning is-dismissible">
		    <p>
		    	<strong><?php esc_html_e( 'One Page Checkout For EDD requires Easy Digital Downloads to be activated ', 'onepage-checkout-for-edd' ); ?> <a href="<?php echo esc_url( admin_url('/plugin-install.php?s=slug:easy-digital-downloads&tab=search&type=term') ); ?>"><?php esc_html_e('Install Now', 'onepage-checkout-for-edd'); ?></a></strong>
		    </p>
		</div>
		<?php
	}
}

/**
 * Plugin Initialize if Easy Digital Downloads Plugin Exists
 */
if ( class_exists( 'Easy_Digital_Downloads' ) ) {
	new OnePageCheckoutForEDD();
} else {
	add_action( 'admin_notices', 'one_page_checkout_for_edd_inactive_notice_warn' );
}

/**
 * License Management
 */
add_action( 'init', 'one_page_checkout_for_edd_license_updates' );
function one_page_checkout_for_edd_license_updates( ){

    // Load Class
    include_once( dirname( __FILE__ ) . '/updates/LmfwpptAutoUpdatePlugin.php' );

    // Plugin Args
    $plugin = plugin_basename( __FILE__ );
    $plugin_slug = (dirname(plugin_basename(__FILE__)));
    $current_version = '1.0.0';
    $remote_url = 'https://portal.addonmaster.com/';

    // Required args
    $args = array(
        'plugin' => $plugin,
        'plugin_slug' => $plugin_slug,
        'current_version' => $current_version,
        'remote_url' => $remote_url,
        'menu_type' => 'sub_menu',
        'parent_slug' => 'options-general.php',
        'page_title' => 'One Page Checkout for EDD License Activation',
        'menu_title' => 'One Page Checkout for EDD License',
    );

    $LmfwpptAutoUpdatePlugin = new LmfwpptAutoUpdatePlugin( $args );
}
   