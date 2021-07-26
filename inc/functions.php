<?php

// Add total qty filter
if( !function_exists( 'one_page_checkout_for_edd_ajax_cart_total_qty' ) ){
	function one_page_checkout_for_edd_ajax_cart_total_qty( $return ) {

		$return['total_qty'] = EDD()->cart->get_quantity();
		return $return;
	}
	add_filter( 'edd_ajax_cart_item_quantity_response', 'one_page_checkout_for_edd_ajax_cart_total_qty' );
}

/**
 * Get the URL of the Checkout page
 */
if( !function_exists( 'one_page_checkout_for_edd_get_checkout_uri' ) ){
	function one_page_checkout_for_edd_get_checkout_uri( $uri ) {
		$uri = false;

		// If we are not on a checkout page, determine the URI from the default.
		if ( empty( $uri ) ) {
			$uri = edd_get_option( 'purchase_page', false );
			$uri = isset( $uri ) ? get_permalink( $uri ) : NULL;
		}

		$scheme = defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN ? 'https' : 'admin';

		$ajax_url = admin_url( 'admin-ajax.php', $scheme );

		if ( ( ! preg_match( '/^https/', $uri ) && preg_match( '/^https/', $ajax_url ) && edd_is_ajax_enabled() ) || edd_is_ssl_enforced() ) {
			$uri = preg_replace( '/^http:/', 'https:', $uri );
		}

		if ( edd_get_option( 'no_cache_checkout', false ) ) {
			$uri = edd_add_cache_busting( $uri );
		}

		return apply_filters( 'one_page_checkout_for_edd_get_checkout_uri', $uri );
	}
	add_filter( 'edd_get_checkout_uri', 'one_page_checkout_for_edd_get_checkout_uri', 15 );
}


if ( !function_exists('one_page_checkout_for_edd_checkout_layout') ) {

	function one_page_checkout_for_edd_checkout_layout(){
		$payment_mode = edd_get_chosen_gateway();
		$form_action  = esc_url( edd_get_checkout_uri( 'payment-mode=' . $payment_mode ) );

		ob_start();
			echo '<div id="edd_checkout_wrap">';
			if ( edd_get_cart_contents() || edd_cart_has_fees() ) :

				edd_checkout_cart();

				?>
				<div class="cart_item edd_checkout"><a class="button" href="<?php echo edd_get_checkout_uri(); ?>"><?php _e( 'Checkout', 'easy-digital-downloads' ); ?></a></div>
				<?php
			
			else:
				/**
				 * Fires off when there is nothing in the cart
				 *
				 * @since 1.0
				 */
				do_action( 'edd_cart_empty' );
			endif;
			echo '</div><!--end #edd_checkout_wrap-->';
		return ob_get_clean();
	}

}

if ( ! function_exists('one_page_checkout_for_edd_update_checkout_ajax') ) {
	function one_page_checkout_for_edd_update_checkout_ajax(){
		check_ajax_referer( 'one_page_checkout_for_edd_nonce', 'nonce' );

		echo one_page_checkout_for_edd_checkout_layout();

		die();
	}
}
add_action( 'wp_ajax_one_page_checkout_for_edd_update_checkout', 'one_page_checkout_for_edd_update_checkout_ajax' );
add_action( 'wp_ajax_nopriv_one_page_checkout_for_edd_update_checkout', 'one_page_checkout_for_edd_update_checkout_ajax' );

/**
 * SVG Icons function
 *
 * @return  string
 */
if ( ! function_exists('one_page_checkout_for_edd_get_svg_icon') ) {
	function one_page_checkout_for_edd_get_svg_icon( $icon = null ){

		if ( ! $icon ) {
			return;
		}

		switch ( $icon ) {
				case 'shopping_cart':
					$output ='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="black" width="18px" height="18px"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>';
					break;

				case 'shopping_basket':
					$output ='<svg xmlns="http://www.w3.org/2000/svg" fill="#444" width="24px" height="24px"><path d="M0 0h24v24H0z" fill="none"/><path d="M17.21 9l-4.38-6.56c-.19-.28-.51-.42-.83-.42-.32 0-.64.14-.83.43L6.79 9H2c-.55 0-1 .45-1 1 0 .09.01.18.04.27l2.54 9.27c.23.84 1 1.46 1.92 1.46h13c.92 0 1.69-.62 1.93-1.46l2.54-9.27L23 10c0-.55-.45-1-1-1h-4.79zM9 9l3-4.4L15 9H9zm3 8c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>';
					break;

				case 'spinner':
					$output ='<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="456.817px" height="456.817px" viewBox="0 0 456.817 456.817" style="enable-background:new 0 0 456.817 456.817;" xml:space="preserve"><g><g><path d="M109.641,324.332c-11.423,0-21.13,3.997-29.125,11.991c-7.992,8.001-11.991,17.706-11.991,29.129c0,11.424,3.996,21.129,11.991,29.13c7.998,7.994,17.705,11.991,29.125,11.991c11.231,0,20.889-3.997,28.98-11.991c8.088-7.991,12.132-17.706,12.132-29.13c0-11.423-4.043-21.121-12.132-29.129C130.529,328.336,120.872,324.332,109.641,324.332z"/><path d="M100.505,237.542c0-12.562-4.471-23.313-13.418-32.267c-8.946-8.946-19.702-13.418-32.264-13.418c-12.563,0-23.317,4.473-32.264,13.418c-8.945,8.947-13.417,19.701-13.417,32.267c0,12.56,4.471,23.309,13.417,32.258c8.947,8.949,19.701,13.422,32.264,13.422c12.562,0,23.318-4.473,32.264-13.422C96.034,260.857,100.505,250.102,100.505,237.542z"/><path d="M365.454,132.48c6.276,0,11.662-2.24,16.129-6.711c4.473-4.475,6.714-9.854,6.714-16.134c0-6.283-2.241-11.658-6.714-16.13c-4.47-4.475-9.853-6.711-16.129-6.711c-6.283,0-11.663,2.24-16.136,6.711c-4.47,4.473-6.707,9.847-6.707,16.13s2.237,11.659,6.707,16.134C353.791,130.244,359.171,132.48,365.454,132.48z"/><path d="M109.644,59.388c-13.897,0-25.745,4.902-35.548,14.703c-9.804,9.801-14.703,21.65-14.703,35.544c0,13.899,4.899,25.743,14.703,35.548c9.806,9.804,21.654,14.705,35.548,14.705s25.743-4.904,35.544-14.705c9.801-9.805,14.703-21.652,14.703-35.548c0-13.894-4.902-25.743-14.703-35.544C135.387,64.29,123.538,59.388,109.644,59.388z"/><path d="M439.684,218.125c-5.328-5.33-11.799-7.992-19.41-7.992c-7.618,0-14.089,2.662-19.417,7.992c-5.325,5.33-7.987,11.803-7.987,19.421c0,7.61,2.662,14.092,7.987,19.41c5.331,5.332,11.799,7.994,19.417,7.994c7.611,0,14.086-2.662,19.41-7.994c5.332-5.324,7.991-11.8,7.991-19.41C447.675,229.932,445.02,223.458,439.684,218.125z"/><path d="M365.454,333.473c-8.761,0-16.279,3.138-22.562,9.421c-6.276,6.276-9.418,13.798-9.418,22.559c0,8.754,3.142,16.276,9.418,22.56c6.283,6.282,13.802,9.417,22.562,9.417c8.754,0,16.272-3.141,22.555-9.417c6.283-6.283,9.422-13.802,9.422-22.56c0-8.761-3.139-16.275-9.422-22.559C381.727,336.61,374.208,333.473,365.454,333.473z"/><path d="M237.547,383.717c-10.088,0-18.702,3.576-25.844,10.715c-7.135,7.139-10.705,15.748-10.705,25.837s3.566,18.699,10.705,25.837c7.142,7.139,15.752,10.712,25.844,10.712c10.089,0,18.699-3.573,25.838-10.712c7.139-7.138,10.708-15.748,10.708-25.837s-3.569-18.698-10.708-25.837S247.636,383.717,237.547,383.717z"/><path d="M237.547,0c-15.225,0-28.174,5.327-38.834,15.986c-10.657,10.66-15.986,23.606-15.986,38.832c0,15.227,5.327,28.167,15.986,38.828c10.66,10.657,23.606,15.987,38.834,15.987c15.232,0,28.172-5.327,38.828-15.987c10.656-10.656,15.985-23.601,15.985-38.828c0-15.225-5.329-28.168-15.985-38.832C265.719,5.33,252.779,0,237.547,0z"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>';
					break;

				case 'close':
					$output ='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="black" width="30px" height="30px"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>';
					break;

				default:
				$output = '';
				break;
		}

		return $output;
	}
}

/**
 * SVG Icon display
 *
 * @return  void
 */
if ( ! function_exists('one_page_checkout_for_edd_svg_icon') ) {
	function one_page_checkout_for_edd_svg_icon( $icon = null ){
		echo one_page_checkout_for_edd_get_svg_icon( $icon );
	}
}


/**
 * Cart Count function
 */
if ( ! function_exists( 'one_page_checkout_for_edd_cart_count' ) ) {
	function one_page_checkout_for_edd_cart_count() {
		if ( ! function_exists( 'edd_get_cart_quantity' ) ) {
			return;
		}

		?>
		<span class="eddnstant_cart_total">
			<?php echo ( edd_get_cart_quantity() > 0 ) ? edd_get_cart_quantity() : 0; ?>
		</span> <?php
	}
}

/**
 *	Custom CSS function
 */
if( !function_exists( 'one_page_checkout_for_edd_custom_css' ) ){
	function one_page_checkout_for_edd_custom_css(){

		// Return if EDD not activated
	    if ( !class_exists( 'Easy_Digital_Downloads' ) ) {
	        return;
	    }

		global $opcfedd_opt;

		$output = '';

		if( $opcfedd_opt['panel-width'] ) :
			$output .= '
			@media (min-width: 768px) {
				.eddnstant-checkout-wrap {
					width: ' . $opcfedd_opt['panel-width'] . '%;
					height: ' . $opcfedd_opt['panel-height'] . '%;
				}
			}
			';
		endif;

		if( $opcfedd_opt['panel-bg'] ) :
			$output .= '
			.eddnstant-checkout-wrap {
				background: ' . $opcfedd_opt['panel-bg'] . ';
			}
			';
		endif;

		if( $opcfedd_opt['sticky-cart-bg'] ) :
			$output .= '
			.eddnstant-sticky-cart {
				background: ' . $opcfedd_opt['sticky-cart-bg'] . ';
			}
			';
		endif;

		if( $opcfedd_opt['sticky-cart-color'] ) :
			$output .= '
			.eddnstant-sticky-cart .eddnstant_cart_total {
				color: ' . $opcfedd_opt['sticky-cart-color'] . ';
			}
			';
		endif;

		if( $opcfedd_opt['sticky-cart-count-bg'] ) :
			$output .= '
			.eddnstant-sticky-cart .eddnstant_cart_total {
				background: ' . $opcfedd_opt['sticky-cart-count-bg'] . ';
			}
			';
		endif;

		if( $opcfedd_opt['sticky-cart-icon-color'] ) :
			$output .= '
			.eddnstant-sticky-cart svg {
				fill: ' . $opcfedd_opt['sticky-cart-icon-color'] . ';
			}
			';
		endif;

		if( $opcfedd_opt['panel-zindex'] ) :
			$output .= '
			.eddnstant-checkout-wrap {
				z-index: ' . $opcfedd_opt['panel-zindex'] . ';
			}
			';
		endif;

		$output .= isset($opcfedd_opt['custom-css']) ? $opcfedd_opt['custom-css'] : ""; //Custom css

		wp_add_inline_style( 'eddnstant', $output );
	}
}
add_action( 'wp_enqueue_scripts', 'one_page_checkout_for_edd_custom_css', 200 );