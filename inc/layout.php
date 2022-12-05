<?php
/**
 * The Layout
 */

//Prevent direct access
if ( !defined('ABSPATH') ) {
    die();
}

// Options
$opcfedd_opt = opcfedd_get_option();
?>
<div class="eddnstant-checkout-wrap">
	<?php if( isset( $opcfedd_opt['show-close-btn'] ) && $opcfedd_opt['show-close-btn'] != "0" ) : ?>
        <div class="eddnstant-close-wrap">
		    <a class="eddnstant-close" title="<?php esc_attr_e( 'Close', 'onepage-checkout-for-edd' ); ?>"><?php onepage_checkout_for_edd_svg_icon('close'); ?></a>
        </div>
	<?php endif; ?>
    <div class="eddnstant-checkout-outer">
        <div class="eddnstant-checkout-inner">
            <?php echo do_shortcode('[download_checkout]'); ?>
        </div>
        <span class="eddnstant-loader"><?php onepage_checkout_for_edd_svg_icon('spinner'); ?></span>
    </div>
</div>
<div class="eddnstant-modal-drop-shadow"></div>
<?php if( isset( $opcfedd_opt['cart-position'] ) && $opcfedd_opt['cart-position'] == 1 ) : ?>
    <div id="eddnstant-sticky-cart" class="eddnstant-sticky-cart eddnstant-cart-shake eddnstant-sticky-cart-right">
        <span class="eddnstant-icon-cart"><?php onepage_checkout_for_edd_svg_icon('shopping_basket'); ?></span>
        <?php echo onepage_checkout_for_edd_cart_count(); ?>
    </div>
<?php else: ?>
    <div id="eddnstant-sticky-cart" class="eddnstant-sticky-cart eddnstant-cart-shake eddnstant-sticky-cart-left">
        <span class="eddnstant-icon-cart"><?php onepage_checkout_for_edd_svg_icon('shopping_basket'); ?></span>
        <?php echo onepage_checkout_for_edd_cart_count(); ?>
    </div>
<?php endif; ?>