(function ( $ ) {
	"use strict";

jQuery(function($) {

  $( document ).on( 'click', '.notice-dismiss', function () {
      parent = $(this).parent().attr('data-usaepay');
      if( parent == 'usaepay_notice_dismiss' ){
         var type = $( this ).closest( '.usaepay-notice-dismiss' ).data( 'usaepay' );
          $.ajax( ajaxurl,
            {
              type: 'POST',
              data: {
                action: 'usaepay_dismissed_notice_handler',
                type: type,
              }
            } );

      }
     
    } );

  });


}( jQuery ) );