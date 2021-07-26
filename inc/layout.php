<?php
/**
 * The Layout
 */
if( !function_exists( 'one_page_checkout_for_edd_layout_function' ) ){
    function one_page_checkout_for_edd_layout_function(){

        if ( is_admin() || !class_exists( 'Easy_Digital_Downloads' ) ) {
            return;
        }

    	global $opcfedd_opt;
        ?>
        <div class="eddnstant-checkout-wrap">

            <div class="eddnstant-checkout-outer">

                <?php if( $opcfedd_opt['show-close-btn'] == 1 ) : ?>
                    <div class="eddnstant-close-wrap">
                        <a href="#" class="eddnstant-close" title="<?php esc_attr_e( 'Close', 'eddnstant' ); ?>"><?php one_page_checkout_for_edd_svg_icon('close'); ?></a>
                    </div>
                <?php endif; ?>
                
                <div class="eddnstant-checkout-inner">
                    <?php echo one_page_checkout_for_edd_checkout_layout(); ?>
                </div>
                <span class="eddnstant-loader"><?php one_page_checkout_for_edd_svg_icon('spinner'); ?></span>
            </div>
        </div>

        <div class="opcfedd-backdrop-shadow"></div>

        <?php if( $opcfedd_opt['cart-position'] == 1 ) : ?>
            <div id="eddnstant-sticky-cart" class="eddnstant-sticky-cart eddnstant-cart-shake eddnstant-sticky-cart-right">
                <span class="eddnstant-icon-cart"><?php one_page_checkout_for_edd_svg_icon('shopping_basket'); ?></span>
            <?php echo one_page_checkout_for_edd_cart_count(); ?>
             
            </div>
        <?php else: ?>
            <div id="eddnstant-sticky-cart" class="eddnstant-sticky-cart eddnstant-cart-shake eddnstant-sticky-cart-left">
                <span class="eddnstant-icon-cart"><?php one_page_checkout_for_edd_svg_icon('shopping_basket'); ?></span>
            <?php echo one_page_checkout_for_edd_cart_count(); ?>
            </div>
        <?php endif; ?>



        <?php 
    }
    add_action('wp_footer', 'one_page_checkout_for_edd_layout_function');
}
