(function($) {

    /**
     * Events & links
     */
    $( document ).on( 'submit', '.variation-form', addVariation );
    $( document ).on( 'input', '[name="quicker-qty"]', massQty );

    /**
     * DOM ready
     */
    $( function() {

    });


    /**
     * Add variation to cart via ajax
     *
     * @event form.variation-form.submit
     */
    function addVariation( event )
    {
        event.preventDefault();
        event.stopPropagation();

        var form = $( event.target );
        var product_id = form.find('[name="product_id"]').val();

        var variations = {};

        form.find('.variation-row').each( function() {
            var tr = $(this);
            var variation_id = tr.data('id');

            var qty = parseInt( tr.find('[name=quantity]').val() );

            if( !qty ) {
                return;
            }

            var variation = {};
            tr.find( '[data-type=attribute]' ).each( function() {
                var name = $(this).attr('name');
                var value = $(this).val();

                variation[name] = value;
            });

            var customer = tr.find('[name=customer]').val();

            variations[variation_id] = {
                quantity : qty,
                variation_id : variation_id,
                customer : customer,
                variation : variation
            };
        });

        if( !Object.keys( variations ).length ) {
            console.log( 'empty' );
            return false;
        }

        form.addClass('ajax');

        var a = $.ajax({
            url: woo2.ajaxurl,
            method: "POST",
            data: {
                action : "woocommerce_add_variation_to_cart",
                product_id : product_id,
                variations : variations,
                type: "POST"
            }
        });

        a.done(function( data ) {
            if( data.error ) {
            }
            else {

                if( form.hasClass('quickorder') ) {
                    location.reload();
                    return false;
                }
                else {
                    window.location.href = woo2.carturl;
                    return false;
                }
            }

            form.removeClass('ajax');
        });

        return false;
    }


    function massQty( event )
    {
        var el = $( event.target );
        var qty = el.val();
        $('.variation-form [name="quantity"]').each( function() {
            $(this).val(qty);
        });

    }

})(jQuery);




