(function ( $ ) {
	"use strict";

jQuery(document).ready(function($) {
    $("body").delegate('.woccommerce-usaepay-dashicons-dismiss', 'click', function(e) {

        var $this = $(this);
        var $card_index = $this.data('index-id');
        var $card_token = $this.data('card-token');
		
		//var $card_index = $this.attr('data-index-id');
        //var $card_token = $this.attr('data-card-token');

        var values = {
            'card_index': $card_index,
            'card_token': $card_token
        };
		
		console.log(values);

        var data = {
            'action': 'wcusaepay-ajax-request',
            'func': 'remove_card_token',
            'method': 'WCUSAEPAY_Ajax_Call',
            'data': values,

        };

        $this.hide().after('<div class="woocommerce-usaepay-load-spinner" style="display: inline !important;"><img src="' + wcusaepay_ajax_service.site_url + '/wp-includes/images/spinner.gif" /></div>');

        $.post(wcusaepay_ajax_service.ajax_url, data, function(response) {

            console.log(response);

            $(".woocommerce-usaepay-load-spinner").hide();


            if (response.success === true) {
                $this.parent().remove();
            }

        });

        e.preventDefault();
    });

});

}( jQuery ) );