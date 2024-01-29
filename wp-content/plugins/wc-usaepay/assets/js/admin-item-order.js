(function ( $ ) {
	"use strict";
	jQuery( document ).ready( function( $ ) {

		// remove Items Subtotal in latest version
		var $st 			= $(".usaepaytransapi_subtotal").closest('tr').prev().remove();

		$( "body" ).on( 'click', '.usaepaytransapi_capture_subtotal_btn', function( e ){	
			e.preventDefault();

			var c = window.prompt("Please input the new Subtotal");

			if (c != null) {

				//$('.wc-order-totals').block({ message: null, overlayCSS: { background: '#fff no-repeat center', opacity: 0.6 } });
				$( '#woocommerce-order-items' ).block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});

				var country          = '';
				var state            = '';
				var postcode         = '';
				var city             = '';

				if ( 'shipping' === woocommerce_admin_meta_boxes.tax_based_on ) {
					country  = $( '#_shipping_country' ).val();
					state    = $( '#_shipping_state' ).val();
					postcode = $( '#_shipping_postcode' ).val();
					city     = $( '#_shipping_city' ).val();
				}

				if ( 'billing' === woocommerce_admin_meta_boxes.tax_based_on || ! country ) {
					country  = $( '#_billing_country' ).val();
					state    = $( '#_billing_state' ).val();
					postcode = $( '#_billing_postcode' ).val();
					city     = $( '#_billing_city' ).val();
				}

				var newdata = {
					action:        'woocommerce_add_new_subtotal',
					orderId:       $(this).attr('data-order-id'),
					itemId:        $(this).attr('data-item-id'),
					osubtotal:     $(this).attr('data-osubtotal'),
					security:      order_line_totals.new_subtotal_nonce
				};

				$.ajax( {
					url:     order_line_totals.ajax_url,
					data:    newdata,
					type:    'POST',
					success: function( response ) {},
					complete: function() {

						var data =  {
							country:  country,
							state:    state,
							postcode: postcode,
							city:     city,
							action:   'woocommerce_calc_line_taxes',
							order_id: woocommerce_admin_meta_boxes.post_id,
							items:    $( 'table.woocommerce_order_items :input[name], .wc-order-totals-items :input[name]' ).serialize(),
							security: woocommerce_admin_meta_boxes.calc_totals_nonce
						};

						$( document.body ).trigger( 'order-totals-recalculate-before', data );

						$.ajax({
							url:  woocommerce_admin_meta_boxes.ajax_url,
							data: data,
							type: 'POST',
							success: function( response ) {
								$( '#woocommerce-order-items' ).find( '.inside' ).empty();
								$( '#woocommerce-order-items' ).find( '.inside' ).append( response );
								//wc_meta_boxes_order_items.reloaded_items();
								//wc_meta_boxes_order_items.unblock();

								$( document.body ).trigger( 'order-totals-recalculate-success', response );
							},
							complete: function( response ) {

								$( document.body ).trigger( 'order-totals-recalculate-complete', response );





								// subtotal lines
				
								var $currency 	= $('#order_line_items tr.item').find('.woocommerce-Price-currencySymbol');
								var $s 			= $(".usaepaytransapi_subtotal");

								$s.html("");
								$s.append( '<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">'+ $currency.html() +'</span>'+ c +'</span>' );

								var $line_subtotal 	= $('#order_line_items tr.item').find('.line_subtotal.wc_input_price');
								var $sublength 		= $line_subtotal.length;

								$line_subtotal.each(function( idx ){
					
									var $row = $(this);

									$($row).attr('value', 0);	

									 
								});

								// total lines

								var $line_total 	= $('#order_line_items tr.item').find('.line_total');
								var $length 		= $line_total.length;

								$line_total.each(function( idx ){
					
									var $row = $(this);

									var $last_i = ($length - 1);

									if( $length == 0  ){
										$($row).attr('value', c);
									} else {
										if(  idx == $last_i ){
											$($row).attr('value', c);
									    } else {
									    	$($row).attr('value', 0);
									    }

									}
									
								});

								

								var data =  {
									country:  country,
									state:    state,
									postcode: postcode,
									city:     city,
									action:   'woocommerce_calc_line_taxes',
									order_id: woocommerce_admin_meta_boxes.post_id,
									items:    $( 'table.woocommerce_order_items :input[name], .wc-order-totals-items :input[name]' ).serialize(),
									security: woocommerce_admin_meta_boxes.calc_totals_nonce
								};

								$( document.body ).trigger( 'order-totals-recalculate-before', data );

								$.ajax({
									url:  woocommerce_admin_meta_boxes.ajax_url,
									data: data,
									type: 'POST',
									success: function( response ) {
										$( '#woocommerce-order-items' ).find( '.inside' ).empty();
										$( '#woocommerce-order-items' ).find( '.inside' ).append( response );
										//wc_meta_boxes_order_items.reloaded_items();
										//wc_meta_boxes_order_items.unblock();

										$( document.body ).trigger( 'order-totals-recalculate-success', response );
									},
									complete: function( response ) {

										$( document.body ).trigger( 'order-totals-recalculate-complete', response );

										window.wcTracks.recordEvent( 'order_edit_recalc_totals', {
											order_id: data.post_id,
											OK_cancel: 'OK',
											status: $( '#order_status' ).val()
										} );



										//$('.wc-order-totals').block({ message: null, overlayCSS: { background: '#fff no-repeat center', opacity: 0.6 } });
										// remove coupon

										var $coupon_code = $(".usaepaytransapi_coupon_code").val();

										var data1 = {
											action : 'woocommerce_remove_order_coupon',
											dataType : 'json',
											country:  country,
											state:    state,
											postcode: postcode,
											city:     city,
											order_id : woocommerce_admin_meta_boxes.post_id,
											security : woocommerce_admin_meta_boxes.order_item_nonce,
											coupon : $coupon_code
										};

										if( $coupon_code != "" ){

											console.log( "remove coupon" );
											console.log( $(".usaepaytransapi_coupon_code").val() );


											$.post( woocommerce_admin_meta_boxes.ajax_url, data1, function( response ) {
												if ( response.success ) {
													$( '#woocommerce-order-items' ).find( '.inside' ).empty();
													$( '#woocommerce-order-items' ).find( '.inside' ).append( response.data.html );
														//wc_meta_boxes_order_items.reloaded_items();
														//wc_meta_boxes_order_items.unblock();

														//$('.wc-order-totals').block({ message: null, overlayCSS: { background: '#fff no-repeat center', opacity: 0.6 } });

														// addnew coupon

														if( $coupon_code != "" ){

															console.log( "add coupon" );
															console.log( $(".usaepaytransapi_coupon_code").val() );

															var user_id    = $( '#customer_user' ).val();
															var user_email = $( '#_billing_email' ).val();

															var data =  {
																action     : 'woocommerce_add_coupon_discount',
																dataType   : 'json',
																order_id   : woocommerce_admin_meta_boxes.post_id,
																security   : woocommerce_admin_meta_boxes.order_item_nonce,
																country:  country,
																state:    state,
																postcode: postcode,
																city:     city,
																coupon     : $coupon_code,
																user_id    : user_id,
																user_email : user_email
															};

															console.log(data);

															$.ajax( {
																url:     woocommerce_admin_meta_boxes.ajax_url,
																data:    data,
																type:    'POST',
																success: function( response ) {
																	if ( response.success ) {
																		$( '#woocommerce-order-items' ).find( '.inside' ).empty();
																			$( '#woocommerce-order-items' ).find( '.inside' ).append( response.data.html );
																			//wc_meta_boxes_order_items.reloaded_items();
																			//wc_meta_boxes_order_items.unblock();
																	} else {
																		window.alert( response.data.error );
																	}
																	//wc_meta_boxes_order_items.unblock();
																},
																complete: function() {
																	$('#woocommerce-order-items').unblock();
																	window.wcTracks.recordEvent( 'order_edit_added_coupon', {
																		order_id: data.order_id,
																		status: $( '#order_status' ).val()
																	} );
																}
															} );

														} else {
															$('#woocommerce-order-items').unblock();
														}


												} else {
													window.alert( response.data.error );
												}
												
											});
										} else {
											$('#woocommerce-order-items').unblock();
										}


									}
								});




								
							}
						});	
						
					}
				} );

				//return false;



				

				// refresh coupon ==================================

				

				

				/*
				
				
				*/

				

			} 

			//$('.wc-order-totals').unblock();

			//return false;
		} );
		
	});
	

}( jQuery ) );