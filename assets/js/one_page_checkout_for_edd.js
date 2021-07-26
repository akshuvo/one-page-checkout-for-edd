(function($) {
	"use strict";
	$(document).ready(function(){

		// Checkout Ajax Refresh
		$(document.body).on('edd_cart_item_added', function( e, response ){

			// Quantity Update
			$(document.body).trigger('eddnstant_quantity_updated', [ response.cart_quantity ]);

            $.ajax({
                type: 'POST',
                url: one_page_checkout_for_edd_ajax_vars.ajaxurl,
                data: {
                    'action': 'one_page_checkout_for_edd_update_checkout',
                    'nonce': one_page_checkout_for_edd_ajax_vars.nonce,
                    'response': response,
                },
                beforeSend: function(data){
    				$('.eddnstant-checkout-inner').addClass( 'eddnstant-loading' );
    			},
    			complete: function(data){
    				$('.eddnstant-checkout-inner').removeClass( 'eddnstant-loading' );
    			},
                success: function(data){
                	if( data ){
                		$('.eddnstant-checkout-inner').html( data );
                		jQuery('select#edd-gateway, input.edd-gateway').trigger('change');

                		// init on document.ready
                		if ( (typeof(EDD_Checkout) !== "undefined") ) {
							window.jQuery(document).ready(EDD_Checkout.init);
						}
                	}
                },
    			error: function(data){
    				console.log(data);
    			},
            });
		});

		// Cart Quantity Update
		$(document.body).on('edd_quantity_updated', function( e, response ){

			if ( response && response.total_qty ) {
                $(document.body).trigger('eddnstant_quantity_updated', [ response.total_qty ]);
            }

		});

        // Total Quantity Update/Show
        $(document.body).on('eddnstant_quantity_updated', function( e, response ){

            var cart_quantity = ( response ) ? response : 0;
            $('.eddnstant_cart_total').text( cart_quantity );
            jQuery(document).trigger('cart_shake');


        });

		// Ajax remove item from cart
		$(document).on('click', '.edd_cart_remove_item_btn', function( e ){

			e.preventDefault();

			var $this = $(this);

			var requestUrl = $this.attr('href');

            $.ajax({
                type: 'GET',
                url: requestUrl,
                beforeSend: function(data){
    				$('.eddnstant-checkout-inner').addClass( 'eddnstant-loading' );
    			},
    			complete: function(data){
    				$('.eddnstant-checkout-inner').removeClass( 'eddnstant-loading' );
    			},
                success: function(data){
                	if( data ){
                		$('.eddnstant-checkout-inner').html( $('.eddnstant-checkout-inner', data).html() );

                		var cart_quantity = parseInt( $('.eddnstant_cart_total', data).html() );
						$(document.body).trigger('eddnstant_quantity_updated', [ cart_quantity ]);

                		jQuery('select#edd-gateway, input.edd-gateway').trigger('change');

                		// init on document.ready
                		if ( (typeof(EDD_Checkout) !== "undefined") ) {
							window.jQuery(document).ready(EDD_Checkout.init);
						}

                	}
                },
    			error: function(data){
    				console.log(data);
    			},
            });
		});

		// Cart Shake
		$(document).on('cart_shake', function(){

			$('#eddnstant-sticky-cart').removeClass('eddnstant-cart-shake');

			setTimeout(function(){
				$('#eddnstant-sticky-cart').addClass('eddnstant-cart-shake');
			}, 50);

		});

		// Panel Toggle
		$(document).on('click', '#eddnstant-toggler, #eddnstant-sticky-cart, .eddnstant-close', function( e ){
			e.preventDefault();
			$(document).find('.eddnstant-checkout-wrap').toggleClass('active');
		});
	});
})(jQuery);