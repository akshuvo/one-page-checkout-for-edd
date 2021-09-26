<?php
/**
 * Plugin Name: One Page Checkout for EDD
 * Plugin URI: https://plugins.addonmaster.com/one-page-checkout-for-edd/
 * Bitbucket Plugin URI: https://github.com/akshuvo/one-page-checkout-for-edd
 * Description: Simply reduce the process of purchasing using One Page Checkout for Easy Digital Downloads. 
 * Author: AddonMaster
 * Author URI: https://addonmaster.com
 * Version: 1.0.0
 * Text Domain: one_page_checkout_for_edd
 * Domain Path: /lang
 * EDD tested up to: 2.10.6
 *
 */

/**
* Including Plugin file for security
* Include_once
*
* @since 1.0.0
*/
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

define( 'ONE_PAGE_CHECKOUT_FOR_EDD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );


/**
 *	Plugin Main Class
 */
if ( ! class_exists( 'OnePageCheckoutForEDD' ) ) :
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

			// Added plugin action link
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );

			add_action( 'init', array( $this, 'includes' ) );

		}

		/**
		 * Includes required files
		 */
		public function includes(){
			// General Functions
			require_once( dirname( __FILE__ ) . '/inc/functions.php' );

			// Layout File
			require_once( dirname( __FILE__ ) . '/inc/layout.php' );
		}

		/**
		 * Adds plugin action links.
		 */
		function action_links( $links ) {
			$plugin_links = array(
				'<a href="' . admin_url( 'edit.php?post_type=download&page=_opcfedd' ) . '">' . esc_html__( 'Settings', 'eddnstant' ) . '</a>',
			);
			return array_merge( $plugin_links, $links );
		}

		/**
		 * Plugin Loaded Action
		 */
		function plugin_loaded_action() {

			// Loading Text Domain for Internationalization
			load_plugin_textdomain( 'one_page_checkout_for_edd', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );

			// Options Framework Including
			if ( !class_exists( 'ReduxFramework' ) && file_exists( dirname( __FILE__ ) . '/inc/redux-framework/framework.php' ) ) {
			    require_once( dirname( __FILE__ ) . '/inc/redux-framework/framework.php' );
			}

			// Options File Including
			if ( class_exists( 'ReduxFramework' ) ) {
			    require_once dirname( __FILE__ ) . '/inc/options-init.php';
			}

		}

		/**
		 * Enqueue Frontend Scripts
		 */
		function enqueue_scripts() {
			//$ver = current_time( 'timestamp' );
			$ver = '1.0.0';

		    wp_enqueue_style( 'one_page_checkout_for_edd', ONE_PAGE_CHECKOUT_FOR_EDD_PLUGIN_URL . 'assets/css/one_page_checkout_for_edd.css', null, $ver );

		    wp_enqueue_script( 'one_page_checkout_for_edd', ONE_PAGE_CHECKOUT_FOR_EDD_PLUGIN_URL . 'assets/js/one_page_checkout_for_edd.js', array('jquery'), $ver );
			wp_localize_script( 'one_page_checkout_for_edd', 'one_page_checkout_for_edd_ajax_vars',
            	array(
            	    'nonce' => wp_create_nonce( 'one_page_checkout_for_edd_nonce' ),
            	    'ajaxurl' => admin_url( 'admin-ajax.php' ),
            	)
            );

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
				//wp_deregister_script( 'edd-ajax' );
				//wp_register_script( 'edd-ajax', ONE_PAGE_CHECKOUT_FOR_EDD_PLUGIN_URL . 'assets/js/edd-ajax' . $suffix . '.js', array( 'jquery' ), EDD_VERSION, $in_footer );
				wp_enqueue_script( 'edd-ajax' );

				wp_localize_script( 'edd-ajax', 'edd_scripts', apply_filters( 'edd_ajax_script_vars', array(
					'ajaxurl'                 => edd_get_ajax_url(),
					'position_in_cart'        => isset( $position ) ? $position : -1,
					'has_purchase_links'      => $has_purchase_links,
					'already_in_cart_message' => __('You have already added this item to your cart','easy-digital-downloads' ), // Item already in the cart message
					'empty_cart_message'      => __('Your cart is empty','easy-digital-downloads' ), // Item already in the cart message
					'loading'                 => __('Loading','easy-digital-downloads' ) , // General loading message
					'select_option'           => __('Please select an option','easy-digital-downloads' ) , // Variable pricing error with multi-purchase option enabled
					'is_checkout'             => edd_is_checkout() ? '1' : '0',
					'default_gateway'         => edd_get_default_gateway(),
					'redirect_to_checkout'    => ( edd_straight_to_checkout() || edd_is_checkout() ) ? '1' : '0',
					'checkout_page'           => edd_get_checkout_uri(),
					'permalinks'              => get_option( 'permalink_structure' ) ? '1' : '0',
					'quantities_enabled'      => edd_item_quantities_enabled(),
					'taxes_enabled'           => edd_use_taxes() ? '1' : '0', // Adding here for widget, but leaving in checkout vars for backcompat
				) ) );
			}

		}
	}

endif;

/**
 * EDD Plugin inactive Notice
 */
function one_page_checkout_for_edd_inactive_notice_warn() {
	?>
	<div class="notice notice-warning is-dismissible">
	    <p>
	    	<strong><?php echo esc_html__( 'One Page Checkout For EDD requires Easy Digital Downloads to be activated ', 'one_page_checkout_for_edd' ); ?> <a href="<?php echo esc_url( admin_url('/plugin-install.php?s=slug:easy-digital-downloads&tab=search&type=term') ); ?>"><?php echo esc_html__('Install Now','one_page_checkout_for_edd'); ?></a></strong>
	    </p>
	</div>
	<?php
}

/**
* Plugin Initialize if Easy Digital Downloads Plugin Exists
*/
if ( class_exists( 'Easy_Digital_Downloads' ) ) {
	new OnePageCheckoutForEDD();
} else {
	add_action( 'admin_notices', 'one_page_checkout_for_edd_inactive_notice_warn' );
}