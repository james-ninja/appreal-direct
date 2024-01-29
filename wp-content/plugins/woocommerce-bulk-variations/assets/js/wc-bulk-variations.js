( function( $, window, document, undefined ) {
	"use strict";

	$( function() {

	    $( document.body ).on( 'input', '.wcbvp_quantity', onInputChange );

	    wcbvp_calculate_product_totals();

	    function onInputChange( e ) {

	        var self = e.currentTarget;
	        var $this = $( self );

	        var max = $this.attr( 'max' );
	        if ( max ) {
	            max = max.toString();
	        }

	        if ( max && ! isNaN( parseFloat( max ) ) && isFinite( max ) ) {

	            max = parseInt( max );
	            if ( $this.val() > max ) {
	                e.preventDefault();
	                $this.val(max);
	            }
	        }

	        var individual    = $this.data( 'individual' );
	        var table_id      = $this.data( 'table_id' );
	        var current_value = $this.val();

	        if ( individual && current_value > 0 ) {
	            $( '.wcbvp_quantity[data-table_id="' + table_id + '"]' ).not( self ).val( 0 );
	            if ( current_value > 1 ) {
	                $this.val( 1 );
	            }
	        }

	        wcbvp_calculate_product_totals();

	    }

	    function wcbvp_calculate_product_totals() {

	        var total_quantity = [];
	        var total_price    = [];
	        var product_list   = [];

	        $( ".wcbvp_quantity" ).each(function( i ) {

	            var table_id      = $( this ).data( 'table_id' );
	            var quantity      = parseInt( $( this ).val() );
	            var price         = parseFloat( $( this ).data( 'price' ) );
	            var product_id    = parseInt( $( this ).data( 'product_id' ) );

	            if ( quantity >= 0 ) {

	                var product_price = quantity * price;
	                var old_qty   = 0;
	                var old_price = 0;

	                if ( total_quantity[ table_id ] !== undefined ) {
	                    old_qty += total_quantity[ table_id ];
	                }

	                if ( total_price[ table_id ] !== undefined ) {
	                    old_price += total_price[ table_id ];
	                }

	                total_quantity[ table_id ]   = old_qty + quantity;
	                total_price[ table_id ]      = old_price + product_price;

	                for ( i = 0; i < quantity; i++ ) {
	                    if ( product_list[table_id] == undefined ) {
	                        product_list[table_id] = [];
	                    }
	                    product_list[table_id].push( product_id );
	                }
	            }
	        } );

	        function b2_format_price( nStr, d_separator, t_separator ) {

	            nStr += '';
	            var x = nStr.split('.');
	            var x1 = x[0];
	            var x2 = x.length > 1 ? d_separator + x[1] : '';

	            if ( x1.length > 3 && t_separator ) {
	                var decimalPlaces =  Math.floor( x1.length / 3 );
	                for( var i = 0; i < decimalPlaces; i ++ ) {
	                    var position = x1.length - ( i * t_separator.length ) - ( ( i + 1 ) * 3 );
	                    if ( position <= 0 ) {
	                        break;
	                    }
	                    x1 = [ x1.slice(0, position), x1.slice(position) ].join( t_separator );
	                }
	            }

	            return x1 + x2;
	        }

	        $( ".wcbvp_total_price" ).each(function( i ) {

	            var decimals    = b2_currency_options.decimals;
	            var d_separator = b2_currency_options.d_separator;
	            var t_separator = b2_currency_options.t_separator;

	            if ( decimals == undefined ) {
	                decimals = 2;
	            }
	            if ( d_separator == undefined ) {
	                d_separator = '.';
	            }
	            if ( t_separator == undefined ) {
	                t_separator = '';
	            }

	            var table_id = $( this ).data( 'table_id' );
	            if ( total_price[ table_id ] !== undefined ) {

	                var t_total_price = total_price[ table_id ].toFixed( decimals );

	                t_total_price = b2_format_price( t_total_price, d_separator, t_separator );

	                $( this ).text( t_total_price );
	            }
	        } );

	        $( ".wcbvp_total_quantity" ).each(function( i ) {

	            var table_id = $( this ).data( 'table_id' );
	            if ( total_quantity[ table_id ] !== undefined ) {
	                $( this ).text( total_quantity[ table_id ] );

	                if ( total_quantity[ table_id ] > 0 && product_list[ table_id ] !== undefined ) {

	                    $( '#wcbvp_wrapper_' + table_id + ' .single_add_to_cart_button' ).removeClass( 'disabled' );
	                    $( '#wcbvp_wrapper_' + table_id + ' .single_add_to_cart_button' ).removeClass( 'wc-variation-selection-needed' );
	                    $( '#wcbvp_wrapper_' + table_id + ' .single_add_to_cart_button' ).prop( "disabled", false );

	                    $( '#wcbvp_wrapper_' + table_id + ' [name="multiple-add-to-cart"]' ).val( product_list[ table_id ].toString() );
	                }
	                else {

	                    $( '#wcbvp_wrapper_' + table_id + '.single_add_to_cart_button' ).addClass( 'disabled' );
	                    $( '#wcbvp_wrapper_' + table_id + '.single_add_to_cart_button' ).addClass( 'wc-variation-selection-needed' );
	                    $( '#wcbvp_wrapper_' + table_id + '.single_add_to_cart_button' ).prop( "disabled", true );
	                }
	            }
	        } );

	    }

	    function onOpenPhotoswipe( event ) {
	        if ( typeof PhotoSwipe === 'undefined' || typeof PhotoSwipeUI_Default === 'undefined' ) {
	            return true;
	        }

	        event.preventDefault();

	        var pswpElement = $( '.pswp' )[0],
	            $target = $( event.target ),
	            $galleryImage = $target.closest( '.woocommerce-product-gallery__image' ),
	            items = [];

	        pswpElement.classList.add( 'wbv-gallery' );

	        if ( $galleryImage.length > 0 ) {
	            $galleryImage.each( function( i, el ) {
	                var img = $( el ).find( 'img' ),
	                    large_image_src = img.attr( 'data-large_image' ),
	                    large_image_w = img.attr( 'data-large_image_width' ),
	                    large_image_h = img.attr( 'data-large_image_height' ),
	                    item = {
	                        src: large_image_src,
	                        w: large_image_w,
	                        h: large_image_h,
	                        title: ( img.attr( 'data-caption' ) && img.attr( 'data-caption' ).length ) ? img.attr( 'data-caption' ) : img.attr( 'title' )
	                    };
	                items.push( item );
	            } );
	        }

	        var options = {
	            index: 0,
	            shareEl: false,
	            closeOnScroll: false,
	            history: false,
	            hideAnimationDuration: 0,
	            showAnimationDuration: 0
	        };

	        // Initializes and opens PhotoSwipe
	        var photoswipe = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options );
	        photoswipe.init();
	        photoswipe.listen( 'close', function() {
	            window.dontCloseQVP = true;
	        });
	    }

	    $( document.body ).on( 'click', '.wc-bulk-variations-table .woocommerce-product-gallery__image a', onOpenPhotoswipe );

	} );

} )( jQuery, window, document );
