<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AST_Tpi {
	
	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	 */
	private static $instance;
	
	/**
	 * Initialize the main plugin function
	*/
	public function __construct() {
		$this->init();	
	}
	
	/**
	 * Get the class instance
	 *
	 * @return AST_Tpi
	*/
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	
	/*
	* init from parent mail class
	*/
	public function init() {
		
		add_filter( 'woocommerce_admin_order_actions', array( $this, 'include_tpi_styles'), 100, 2 );
		add_action( 'ast_tracking_form_between_form', array( $this, 'ast_tracking_form_products' ), 10, 2 );
		add_action( 'ast_vendor_tracking_form_between_form', array( $this, 'ast_vendor_tracking_form_between_form_fun' ), 10, 2 );		

		add_filter( 'tracking_info_args', array( $this, 'tracking_info_args_callback' ), 10, 3 );
		add_filter( 'tracking_info_args', array( $this, 'tracking_info_args_inline_callback' ), 10, 3 );
		add_action(	'ast_after_tracking_number', array( $this, 'ast_after_tracking_number_fun' ), 10, 2 );	
		
		//add_action(	'ast_fluid_left_cl_end', array( $this, 'ast_fluid_left_cl_end' ), 10, 2 );		
		
		add_action( 'woocommerce_before_order_itemmeta', array( $this, 'before_order_itemmeta' ), 10, 3 );				
		//add_action( 'woocommerce_order_item_meta_end', array( $this, 'action_woocommerce_order_item_meta_end' ), 10, 3 );

		add_action( 'ast_api_create_item_arg', array( $this, 'ast_api_create_item_arg_callback' ), 10, 2 );	
		add_action( 'trackship_tracking_header_before', array( $this, 'trackship_tracking_header_before_callback' ), 10, 4 );		
	}

	public function include_tpi_styles( $actions, $order ) {
		wp_enqueue_style( 'tpi_styles', ast_pro()->plugin_dir_url() . 'assets/css/tpi.css', array(), ast_pro()->version );
		wp_enqueue_script( 'tpi_scripts', ast_pro()->plugin_dir_url() . 'assets/js/tpi.js' , array( 'jquery', 'wp-util' ), ast_pro()->version, true );
		return $actions;
	}	
	
	public function ast_vendor_tracking_form_between_form_fun( $order_id, $location ) {
		wp_enqueue_style( 'tpi_styles', ast_pro()->plugin_dir_url() . 'assets/css/tpi.css', array(), ast_pro()->version );
		wp_enqueue_script( 'tpi_scripts', ast_pro()->plugin_dir_url() . 'assets/js/tpi.js' , array( 'jquery', 'wp-util' ), ast_pro()->version, true );
		
		$ast = AST_Pro_Actions::get_instance();
		
		$order = new WC_Order( $order_id );
		$items = $order->get_items();
		
		$total_items = count($items);				
		
		$product_list = array();
		$tracking_items = $ast->get_tracking_items( $order_id );		
		
		//echo '<pre>';print_r($tracking_items);echo '</pre>';
		
		foreach ( $tracking_items as $tracking_item ) {			
			if ( isset( $tracking_item[ 'products_list' ] ) ) {
				$product_list[] = $tracking_item[ 'products_list' ];
			}
		}
		
		$all_list = array();		
		foreach ( $product_list as $list ) {
			if ( !empty($list ) ) {
				foreach ( (array) $list as $in_list ) {
					if ( isset( $in_list->item_id) ) {
						if ( isset( $all_list[ $in_list->item_id ] ) ) {
							$all_list[ $in_list->item_id ] = (int) $all_list[ $in_list->item_id ] + (int) $in_list->qty;							
						} else {
							$all_list[ $in_list->item_id ] = $in_list->qty;	
						}
					} else {
						if ( isset( $all_list[ $in_list->product ] ) ) {
							$all_list[ $in_list->product ] = (int) $all_list[ $in_list->product ] + (int) $in_list->qty;							
						} else {
							$all_list[ $in_list->product ] = $in_list->qty;	
						}	
					}					
				}				
			}
		}
		$inline_class = ( 'inline' == $location ) ? 'ast_tracking_item_div' : '';
		?>
		<div class="<?php esc_html_e( $inline_class ); ?>">
			<?php
			$enable_tpi_by_default = get_option( 'enable_tpi_by_default', 0 );
			$checked = ( 1 == $enable_tpi_by_default ) ? 'checked' : '' ;
			$display_sku_for_tpi = get_option( 'display_sku_for_tpi', 0 );
			$display_image_for_tpi = get_option( 'display_image_for_tpi', 0 );
			?>
			<h2 class="product-table-header">
				<input type="checkbox" name="enable_tracking_per_item" class="enable_tracking_per_item" value="1" <?php esc_html_e( $checked ); ?>><?php esc_html_e( 'Tracking Per Item', 'ast-pro'); ?>
			</h2>
			
			<table class="wp-list-table widefat fixed posts ast-product-table" style="<?php esc_html_e( ( 1 != $enable_tpi_by_default ) ? 'display:none;' : '' ); ?>">			
				<?php 
				$items = $order->get_items();
				global $wpdb;
				$vendor_table = $wpdb->prefix . 'wcpv_commissions';
				
				$query = "SELECT order_item_id FROM {$vendor_table} WHERE order_id = " . $order_id . "";
				$order_items = $wpdb->get_results( $query );
				
				?>
				<tbody>
					<?php 
					$n = 0;					
					
					foreach ( $order_items as $order_item ) {
						$item_id = $order_item->order_item_id;
						$item = new WC_Order_Item_Product($item_id);
						$product = $item->get_product();
						$checked = 0;
						$qty = $item->get_quantity();
						
						$variation_id = $item->get_variation_id();
						$product_id = $item->get_product_id();					
						
						if ( 0 != $variation_id ) {
							$product_id = $variation_id;
						}
											
						if ( array_key_exists( $product_id, $all_list ) ) {	
							if ( isset( $all_list[ $product_id ] ) ) {									  
								$qty = (int) $item->get_quantity() - (int) $all_list[ $product_id ];
								if ( $all_list[ $product_id ] == $item->get_quantity() ) {
									$checked = 1;										
								}
							}
						}
						//echo '<pre>';print_r($order_items);echo '</pre>';exit;
						if ( array_key_exists( $item_id, $all_list ) ) {	
							if ( isset( $all_list[ $item_id ] ) ) {									  
								$qty = (int) $item->get_quantity() - (int) $all_list[ $item_id ];
								if ( $all_list[ $item_id ] == $item->get_quantity() ) {
									$checked = 1;										
								}
							}
						}
						
						$item_sku = '';
						$image = '';
						if ( $item->get_product_id() ) {
							$product = wc_get_product( $item->get_product_id() );
							$item_sku = ( $product ) ? $product->get_sku() : '';
							$image = ( $product ) ? $product->get_image( array( 50, 50 )  ) : '';	
						}
						$qty = ( $qty < 0 ) ? 0 : $qty ;
						$disable_row = ( 0 == $qty ) ? 'disable_row' : '';
						?>
						<tr class="ASTProduct_row <?php esc_html_e( $disable_row ); ?>">
							<?php 
							if ( 'inline' == $location ) {
								if ( 1 == $display_image_for_tpi ) {
									?>
								<td style="width: 50px;"><?php echo wp_kses_post( $image ); ?></td>
							<?php } } ?>							
							<td>
								<?php 
									esc_html_e( $item->get_name() );
								if ( 1 == $display_sku_for_tpi ) { 
									?>
									<br/><span class="ASTProduct_sku"><?php esc_html_e( 'SKU:', 'ast-pro' ); ?> <?php esc_html_e( $item_sku ); ?></span>
								<?php } ?>
								<input type="hidden" value="<?php esc_html_e( $item->get_name() ); ?>" name="ASTProduct[<?php esc_html_e( $n ); ?>][title]">
								<input type="hidden" class="product_id" value="<?php esc_html_e( $product_id ); ?>" name="ASTProduct[<?php esc_html_e( $n ); ?>][product]">
								<input type="hidden" class="item_id" value="<?php esc_html_e( $item_id ); ?>" name="ASTProduct[<?php esc_html_e( $n ); ?>][item_id]">
							</td>
							<td style="">
								<div class="value-button" id="decrease" value="Decrease Value">-</div>
								<input type="number" class="ast_product_number" name="ASTProduct[<?php esc_html_e( $n ); ?>][qty]" min="0" max="<?php esc_html_e( $qty ); ?>" oninput="(validity.valid)||(value='');"  value="<?php esc_html_e( $qty ); ?>" />
								<div class="value-button" id="increase" value="Increase Value">+</div>
								<?php if ( 'inline' == $location ) { ?>
									<span>
									<?php 
									esc_html_e( ' out of ', 'ast-pro'); 
									esc_html_e( $item->get_quantity() ); 
									?>
									</span>
								<?php } ?>		
							</td>
						</tr>	
					<?php $n++; } ?>						
				</tbody>			
			</table>			
		</div>
		<div class="qty_validation"><?php esc_html_e( 'Please choose at least one item quantity', 'ast-pro' ); ?></div>
		<style>
		
		<?php if ( 'single_order' == $location ) { ?>
		.ast_tracking_item_div {
			margin: 0 0 10px;
		}
		table.widefat.ast-product-table {
			border-left: 1px solid #e0e0e0;			
			border-right: 1px solid #e0e0e0;
		}	
		.ast-product-table tr.ASTProduct_row td:first-child{
			 width: auto;
		}
		<?php } ?>
		</style>
	<?php
	}	
	/*
	* functions for add products table in tracking form
	*/
	public function ast_tracking_form_products( $order_id, $location ) {		
		
		wp_enqueue_style( 'tpi_styles', ast_pro()->plugin_dir_url() . 'assets/css/tpi.css', array(), ast_pro()->version );
		wp_enqueue_script( 'tpi_scripts', ast_pro()->plugin_dir_url() . 'assets/js/tpi.js' , array( 'jquery', 'wp-util' ), ast_pro()->version, true );
		
		$ast = AST_Pro_Actions::get_instance();
		
		$order = new WC_Order( $order_id );
		$items = $order->get_items();
		
		$total_items = count($items);				
		
		$product_list = array();
		$tracking_items = $ast->get_tracking_items( $order_id );		
		
		foreach ( $tracking_items as $tracking_item ) {			
			if ( isset( $tracking_item[ 'products_list' ] ) ) {
				$product_list[] = $tracking_item[ 'products_list' ];
			}
		}
		
		$all_list = array();		
		foreach ( $product_list as $list ) {
			if ( !empty($list ) ) {
				foreach ( (array) $list as $in_list ) {
					if ( isset( $in_list->item_id) ) {
						if ( isset( $all_list[ $in_list->item_id ] ) ) {
							$all_list[ $in_list->item_id ] = (int) $all_list[ $in_list->item_id ] + (int) $in_list->qty;							
						} else {
							$all_list[ $in_list->item_id ] = $in_list->qty;	
						}
					} else {
						if ( isset( $all_list[ $in_list->product ] ) ) {
							$all_list[ $in_list->product ] = (int) $all_list[ $in_list->product ] + (int) $in_list->qty;							
						} else {
							$all_list[ $in_list->product ] = $in_list->qty;	
						}	
					}					
				}				
			}
		}
			
		$inline_class = ( 'inline' == $location ) ? 'ast_tracking_item_div' : '';
		?>
		<div class="<?php esc_html_e( $inline_class ); ?>">
			<?php
			$enable_tpi_by_default = get_option( 'enable_tpi_by_default', 0 );
			$checked = ( 1 == $enable_tpi_by_default ) ? 'checked' : '' ;
			$display_sku_for_tpi = get_option( 'display_sku_for_tpi', 0 );
			$display_image_for_tpi = get_option( 'display_image_for_tpi', 0 );
			?>
			<h2 class="product-table-header">
				<input type="checkbox" name="enable_tracking_per_item" class="enable_tracking_per_item" value="1" <?php esc_html_e( $checked ); ?>><?php esc_html_e( 'Tracking Per Item', 'ast-pro'); ?>
			</h2>
			
			<table class="wp-list-table widefat fixed posts ast-product-table" style="<?php esc_html_e( ( 1 != $enable_tpi_by_default ) ? 'display:none;' : '' ); ?>">			
				<?php $items = $order->get_items(); ?>
				<tbody>
					<?php 
					$n = 0;
					$total_product = count( $items );
					
					foreach ( $items as $item_id => $item ) {												
						$product = $item->get_product();
						$checked = 0;
						$qty = $item->get_quantity();
						
						$variation_id = $item->get_variation_id();
						$product_id = $item->get_product_id();					
						
						if ( 0 != $variation_id ) {
							$product_id = $variation_id;
						}
											
						if ( array_key_exists( $product_id, $all_list ) ) {	
							if ( isset( $all_list[ $product_id ] ) ) {									  
								$qty = (int) $item->get_quantity() - (int) $all_list[ $product_id ];
								if ( $all_list[ $product_id ] == $item->get_quantity() ) {
									$checked = 1;										
								}
							}
						}
						
						if ( array_key_exists( $item_id, $all_list ) ) {	
							if ( isset( $all_list[ $item_id ] ) ) {									  
								$qty = (int) $item->get_quantity() - (int) $all_list[ $item_id ];
								if ( $all_list[ $item_id ] == $item->get_quantity() ) {
									$checked = 1;										
								}
							}
						}
						
						$item_sku = '';
						$image = '';
						if ( $item->get_product_id() ) {
							$product = wc_get_product( $item->get_product_id() );
							$item_sku = ( $product ) ? $product->get_sku() : '';
							$image = ( $product ) ? $product->get_image( array( 50, 50 )  ) : '';	
						}
						$qty = ( $qty < 0 ) ? 0 : $qty ;
						$disable_row = ( 0 == $qty ) ? 'disable_row' : '';
						?>
						<tr class="ASTProduct_row <?php esc_html_e( $disable_row ); ?>">
							<?php 
							if ( 'inline' == $location ) {
								if ( 1 == $display_image_for_tpi ) {
									?>
								<td style="width: 15px;"><?php echo wp_kses_post( $image ); ?></td>
							<?php } } ?>							
							<td>
								<?php 
									esc_html_e( $item->get_name() );
								if ( 1 == $display_sku_for_tpi ) { 
									?>
									<br/><span class="ASTProduct_sku"><?php esc_html_e( 'SKU:', 'ast-pro' ); ?> <?php esc_html_e( $item_sku ); ?></span>
								<?php } ?>
								<input type="hidden" value="<?php esc_html_e( $item->get_name() ); ?>" name="ASTProduct[<?php esc_html_e( $n ); ?>][title]">
								<input type="hidden" class="product_id" value="<?php esc_html_e( $product_id ); ?>" name="ASTProduct[<?php esc_html_e( $n ); ?>][product]">
								<input type="hidden" class="item_id" value="<?php esc_html_e( $item_id ); ?>" name="ASTProduct[<?php esc_html_e( $n ); ?>][item_id]">
							</td>
							<td style="">
								<div class="value-button" id="decrease" value="Decrease Value">-</div>
								<input type="number" class="ast_product_number" name="ASTProduct[<?php esc_html_e( $n ); ?>][qty]" min="0" max="<?php esc_html_e( $qty ); ?>" oninput="(validity.valid)||(value='');"  value="<?php esc_html_e( $qty ); ?>" />
								<div class="value-button" id="increase" value="Increase Value">+</div>
								<?php if ( 'inline' == $location ) { ?>
									<span>
									<?php 
									esc_html_e( ' out of ', 'ast-pro'); 
									esc_html_e( $item->get_quantity() ); 
									?>
									</span>
								<?php } ?>		
							</td>
						</tr>	
					<?php $n++; } ?>						
				</tbody>			
			</table>			
		</div>
		<div class="qty_validation"><?php esc_html_e( 'Please choose at least one item quantity', 'ast-pro' ); ?></div>
		<style>
		
		<?php if ( 'single_order' == $location ) { ?>
		.ast_tracking_item_div {
			margin: 0 0 10px;
		}
		table.widefat.ast-product-table {
			border-left: 1px solid #e0e0e0;			
			border-right: 1px solid #e0e0e0;
		}	
		.ast-product-table tr.ASTProduct_row td:first-child{
			 width: auto;
		}
		<?php } ?>
		</style>
	<?php	
	}

	/**
	 * Function for return tracking per item args when save tracking information from single order page
	 */
	public function tracking_info_args_callback( $args, $postdata, $order_id ) {
		
		//echo '<pre>';print_r($postdata);echo '</pre>';exit;		
		
		$enable_tracking_per_item = isset( $postdata[ 'enable_tracking_per_item' ] ) ? wc_clean( $postdata[ 'enable_tracking_per_item' ] ) : '' ;
		
		if ( 1 == $enable_tracking_per_item ) {
			if ( isset( $postdata['productlist'] ) ) {				
				$product_data = json_decode( stripslashes( $postdata[ 'productlist' ] ) );					
				$product_args = array(
					'products_list' => wc_clean( $product_data ),				
				);							
				$args = array_merge( $args, $product_args );				
			}
		}
		return $args;
	}
	
	/**
	 * Function for return tracking per item args when save tracking information from orders page 
	 */
	public function tracking_info_args_inline_callback( $args, $postdata, $order_id ) {
		
		$enable_tracking_per_item = isset( $postdata[ 'enable_tracking_per_item' ] ) ? wc_clean( $postdata[ 'enable_tracking_per_item' ] ) : '';
		
		if ( 1 == $enable_tracking_per_item ) {
			if ( isset( $postdata['ASTProduct'] ) ) {	
			
				$products_list = array();
				
				foreach ( $postdata['ASTProduct'] as $product ) {				
					if ( $product['qty'] > 0 ) {
						$product_data =  (object) array (
							'product' => $product['product'],
							'item_id' => $product['item_id'],
							'qty' => $product['qty'],
						);
						array_push( $products_list, $product_data );								
					}
				}																			
				
				$product_args = array(
					'products_list' => $products_list,				
				);
				
				$args = array_merge( $args, $product_args );
			}
		}
		return $args;
	}
	
	/**	 
	 * Function for check if order is Tracking Per Item
	 */
	public function check_if_tpi_order( $tracking_items, $order ) {
		
		$show_products = array();
		$product_list = array();
		$show = false;
		$items = $order->get_items();		
		
		foreach ( $items as $item ) {	
			
			$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();			
			
			$products[] = (object) array (
				'product' => $product_id,
				'qty' => $item->get_quantity(),
			);					
		}		
		
		foreach ( $tracking_items as $t_item ) {			
			if ( isset( $t_item[ 'products_list' ] ) && !empty( $t_item[ 'products_list' ] ) ) {
				$product_list[ $t_item[ 'tracking_id' ] ] = $t_item[ 'products_list' ];
				
				$array_check = ( $product_list[ $t_item[ 'tracking_id' ] ] == $products );
				
				if ( empty( $t_item[ 'products_list' ] ) || 1 == $array_check ) {
					$show_products[$t_item['tracking_id']] = 0;
				} else {
					$show_products[$t_item['tracking_id']] = 1;
				} 
			}
		}			
		
		foreach ( $show_products as $key => $value ) {
			if ( 1 == $value ) {
				$show = true;
				break;
			}
		}
		return $show;
	}

	/**	 
	 * Function for adding tracking details for product after tracking number
	 */
	public function ast_after_tracking_number_fun( $order_id, $tracking_id ) {	
		
		$ast = AST_Pro_Actions::get_instance();		
		$tracking_items = $ast->get_tracking_items( $order_id );
		$order = wc_get_order( $order_id );		
		
		$show = $this->check_if_tpi_order( $tracking_items, $order );
		
		if ( $show ) {
			foreach ( $tracking_items as $tracking_item ) {			
				if ( $tracking_item[ 'tracking_id' ] == $tracking_id ) {
					if ( isset( $tracking_item[ 'products_list' ] ) && '' != $tracking_item[ 'products_list' ] ) {
						foreach ( $tracking_item[ 'products_list' ] as $products ) {						
							$product = wc_get_product( $products->product );
							if ( $product ) {
								$product_name = $product->get_name();
								echo '<span class="tracking_product_list">' . esc_html( $product_name ) . ' x ' . esc_html( $products->qty ) . '</span></br>';
							}
						}
					}	
				}
			}	
		}	
	}	
	
	/*
	* Display TPI Product details in fluid template
	*/
	public function ast_fluid_left_cl_end( $tracking_item, $order_id ) {
		
		$ast = AST_Pro_Actions::get_instance();	
		$tracking_items = $ast->get_tracking_items( $order_id );		
		$order = wc_get_order( $order_id );
		
		if ( !$order ) {
			return;
		}
		
		$show = $this->check_if_tpi_order( $tracking_items, $order );
		
		if ( $show ) {
			echo '<ul style="padding-left: 0px;margin: 0;margin-top: 5px;text-align: left;">';
			if ( isset( $tracking_item[ 'products_list' ] ) ) {								
				foreach ( $tracking_item[ 'products_list' ] as $products ) {
					$product = wc_get_product( $products->product );			
					if ( $product ) {
						$product_name = $product->get_name();
						echo '<li style="list-style: none;">' . esc_html( $product_name ) . ' x ' . esc_html( $products->qty ) . '</li>';
					}
				}
			}
			echo '</ul>';
		}
	}	

	/**	 
	 * Function for show tracking info before order meta
	 */
	public function before_order_itemmeta( $item_id, $item, $_product ) {			
		
		if ( !$_product ) {
			return;
		}	
		
		$order_id = $item->get_order_id();
		$order = wc_get_order( $order_id );
		$item_quantity  = $item->get_quantity();
		
		$ast = AST_Pro_Actions::get_instance();				
		$tracking_items = $ast->get_tracking_items( $order_id );				
		
		$show = $this->check_if_tpi_order( $tracking_items, $order );
		
		if ( !$show ) {
			return;
		}	
		
		$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();					
		
		echo '<div id="tracking-items">';
		foreach ( $tracking_items as $tracking_item ) {
			$formatted = $ast->get_formatted_tracking_item( $order_id, $tracking_item );
			if ( isset( $tracking_item[ 'products_list' ] ) && is_array( $tracking_item[ 'products_list' ] ) ) {					
				if ( in_array( $product_id, array_column( $tracking_item[ 'products_list' ], 'product' ) ) ) {
					foreach ( $tracking_item[ 'products_list' ] as $products ) {
						
						if ( isset( $products->item_id ) && $products->item_id == $item_id ) {						
							?>
							<div class="wc-order-item-sku">
								<strong><?php esc_html_e( 'Shipped with:', 'ast-pro' ); ?></strong>
								<strong><?php echo esc_html( $formatted['formatted_tracking_provider'] ); ?></strong>
								<?php if ( strlen( $formatted['ast_tracking_link'] ) > 0 ) { ?>
								- 
								<?php 
								echo sprintf( '<a href="%s" target="_blank" title="' . esc_attr( __( 'Track Shipment', 'ast-pro' ) ) . '">' . esc_html( $tracking_item['tracking_number'] ) . '</a>', esc_url( $formatted['ast_tracking_link'] ) ); 
								} else {
									?>
									<span> - <?php esc_html_e( $tracking_item['tracking_number'] ); ?></span>
								<?php 
								} 
								echo '<span class="tracking_product_list"> x ' . esc_html( $products->qty ) . '</span>';																	
								?>
							</div>
							<?php
						} elseif ( !isset( $products->item_id ) && $products->product == $product_id ) {
							echo 'product_id';	
							?>
							<div class="wc-order-item-sku">
								<strong><?php esc_html_e( 'Shipped with:', 'ast-pro' ); ?></strong>
								<strong><?php echo esc_html( $formatted['formatted_tracking_provider'] ); ?></strong>
								<?php if ( strlen( $formatted['ast_tracking_link'] ) > 0 ) { ?>
								- 
								<?php 
								echo sprintf( '<a href="%s" target="_blank" title="' . esc_attr( __( 'Track Shipment', 'ast-pro' ) ) . '">' . esc_html( $tracking_item['tracking_number'] ) . '</a>', esc_url( $formatted['ast_tracking_link'] ) ); 
								} else {
									?>
									<span> - <?php esc_html_e( $tracking_item['tracking_number'] ); ?></span>
								<?php 
								} 
								echo '<span class="tracking_product_list"> x ' . esc_html( $products->qty ) . '</span>';																	
								?>
							</div>												
							<?php
						}
					}	
				} 
			}	
		}
		echo '</div>';		
	}

	/**	 
	 * Function for show tracking info after order meta
	 */
	public function action_woocommerce_order_item_meta_end( $item_id, $item, $order ) {
		
		$tpi_display_tracking_order_line_items = get_option( 'tpi_display_tracking_order_line_items', 0 );			
		
		$order_id = $order->get_id();
		$order = wc_get_order( $order_id );
		if ( !$order ) {
			return;
		}	
		
		$ast = AST_Pro_Actions::get_instance();				
		$tracking_items = $ast->get_tracking_items( $order_id );
		
		$show = $this->check_if_tpi_order( $tracking_items, $order );
		if ( !$show ) {
			return;
		}	
		?>
		
		<style>
		.before-meta-tracking-content {
			padding: 5px 0 0;
			position: relative;
		}
		</style>	
		<?php
		$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();	
		echo '<div id="tracking-items">';
		
		foreach ( $tracking_items as $tracking_item ) {
			$formatted = $ast->get_formatted_tracking_item( $order_id, $tracking_item ); 				
			if ( isset( $tracking_item[ 'products_list' ] ) && '' != $tracking_item[ 'products_list' ] ) {
				if ( in_array( $product_id, array_column( $tracking_item[ 'products_list' ], 'product' ) ) ) {
					?>
						<div class="before-meta-tracking-content">
							<div class="tracking-content-div">
								<strong><?php echo esc_html( $formatted['formatted_tracking_provider'] ); ?></strong>
								<?php
								if ( strlen( $formatted['ast_tracking_link'] ) > 0 ) { 
									?>
									- 
									<?php 
									echo sprintf( '<a href="%s" target="_blank" title="' . esc_attr( __( 'Track Shipment', 'ast-pro' ) ) . '">' . esc_html( $tracking_item['tracking_number'] ) . '</a>', esc_url( $formatted['ast_tracking_link'] ) ); 
								} else { 
									?>
									<span> - <?php esc_html_e( $tracking_item['tracking_number'] ); ?></span>
								<?php } ?>
							</div>
							<?php 
							if ( strlen( $formatted['ast_tracking_link'] ) > 0 ) { 
								echo sprintf( '<a href="%s" target="_blank" class="button ast-track-button">' . esc_html( 'Track', 'ast-pro' ) . '</a>', esc_url( $formatted['ast_tracking_link'] ) ); 
							}	
							?>
						<style>
						a.button.ast-track-button {
							margin: 5px 0;
						}
						</style>	
						</div>						
						<?php
				} 
			}	
		}
		echo '</div>';	
	}

	/*
	* Return args with add tracking per item addon arguments
	*/	
	public function ast_api_create_item_arg_callback( $args, $request ) {				
		
		$sku_string = $request['sku'];
		$qty_string = $request['qty'];
				
		$sku_array = explode( ',', $sku_string );
		$qty_array = explode( ',', $qty_string );
		
		$order_id = (int) $request['order_id'];		
		
		if ( isset($request['sku'] ) && isset( $request['qty'] ) ) {						
						
			$products_list = array();
				
			foreach ( $sku_array as $key => $sku ) {
				if ($qty_array[$key] > 0 ) {
					
					$product_id = ast_get_product_id_by_sku( $sku );
					
					$product_data =  (object) array (
						'product' => $product_id,
						'qty' => $qty_array[$key],
					);	
					array_push( $products_list, $product_data );								
				}
			}																			
			
			$product_args = array(
				'products_list' => $products_list,				
			);
			
			$args = array_merge( $args, $product_args );
			
			$autocomplete_order_tpi = get_option( 'autocomplete_order_tpi', 0 );
			if ( 1 == $autocomplete_order_tpi ) {
				$args = $this->autocomplete_order_after_adding_all_products( $order_id, $args, $products_list );		
			}
		}				
		
		return $args;
	}	
	
	public function autocomplete_order_after_adding_all_products( $order_id, $args, $products_list ) {
		
		$order = wc_get_order( $order_id );
		$items = $order->get_items();
		$items_count = count($items);
		
		$added_products = $this->get_all_added_product_list_with_qty( $order_id );
		
		$new_products = array();
		
		foreach ( $products_list as $in_list ) {						
			if ( isset( $new_products[ $in_list->product ] ) ) {
				$new_products[$in_list->product] = (int) $new_products[ $in_list->product ] + (int) $in_list->qty;							
			} else {
				$new_products[ $in_list->product ] = $in_list->qty;	
			}
		}
		
		$total_products_data = array();
		
		foreach ( array_keys( $new_products + $added_products ) as $products ) {
			$total_products_data[ $products ] = ( isset( $new_products[ $products ] ) ? $new_products[ $products ] : 0 ) + ( isset( $added_products[ $products ] ) ? $added_products[ $products ] : 0 );
		}			
		
		$orders_products_data = array();
		
		foreach ( $items as $item ) {																
			
			$checked = 0;
			$qty = $item->get_quantity();
			
			if ( 1 == $items_count && 1 == $qty ) {
				return $args;
			}	
			
			$variation_id = $item->get_variation_id();
			$product_id = $item->get_product_id();					
			
			if ( 0 != $variation_id ) {
				$product_id = $variation_id;
			}
			
			$orders_products_data[$product_id] = $qty;
		}				
		
		$change_status = 0;
		$autocomplete_order = true;				
		
		foreach ( $orders_products_data as $product_id => $qty ) {		
			if ( isset( $total_products_data[ $product_id ] ) ) {
				if ( $qty > $total_products_data[ $product_id ] ) {
					$autocomplete_order = false;
					$change_status = 1;
				} else {
					$change_status = 1;
				}
			} else {
				$autocomplete_order = false;
			}
		}
		
		if ( $autocomplete_order && 1 == $change_status ) {
			$args['status_shipped'] = 1;
		}
		
		return $args;
	}

	public function get_all_added_product_list_with_qty( $order_id ) {
		
		$ast = AST_Pro_Actions::get_instance();
		$tracking_items = $ast->get_tracking_items( $order_id, true );
		
		$product_list = array();			
		
		foreach ( $tracking_items as $tracking_item ) {
			if ( isset( $tracking_item[ 'products_list' ] ) ) {
				$product_list[] = $tracking_item[ 'products_list' ];				
			}
		}
		
		$all_list = array();
		foreach ( $product_list as $list ) {
			foreach ( $list as $in_list ) {
				if ( isset( $all_list[ $in_list->product ] ) ) {
					$all_list[ $in_list->product ] = (int) $all_list[ $in_list->product ] + (int) $in_list->qty;							
				} else {
					$all_list[ $in_list->product ] = $in_list->qty;	
				}
			}				
		}
		return $all_list;
	}	
	
	/*
	* Display TPI Product details in TrackShip Tracking Page
	*/
	public function trackship_tracking_header_before_callback( $order_id, $tracker, $tracking_provider, $tracking_number ) {
		
		$ast = AST_Pro_Actions::get_instance();				
		$tracking_items = $ast->get_tracking_items( $order_id ); 
		$order = wc_get_order( $order_id );
		
		$show = $this->check_if_tpi_order( $tracking_items, $order );
		
		if ( !$show ) {
			return;
		}
		
		foreach ( $tracking_items as $tracking_item ) {
			if ( $tracking_item['tracking_number'] == $tracking_number ) {
				
				if ( !isset( $tracking_item['products_list'] ) ) {
					return; 
				}
				
				if ( empty( $tracking_item['products_list'] ) ) {
					return; 
				}
			}
		}	
		?>
		<h4 class="h4-heading tpi_products_heading"><?php esc_html_e( 'Products', 'woocommerce' ); ?></h4>			
		<ul class="tpi_product_tracking_ul">
			<?php
			foreach ( $tracking_items as $tracking_item ) {
				if ( $tracking_item[ 'tracking_number' ] == $tracking_number ) {
					if ( isset( $tracking_item[ 'products_list' ] ) ) {
						foreach ( (array) $tracking_item[ 'products_list' ] as $products ) {
							if ( $products->product ) {
								$product = wc_get_product( $products->product );
								if ( $product ) {
									$product_name = $product->get_name();
									echo '<li><a target="_blank" href=' . esc_url( get_permalink( $products->product ) ) . '>' . esc_html( $product_name ) . '</a> x ' . esc_html( $products->qty ) . '</li>';
								}
							}
						}
					}
				}
			}
			?>
		</ul>
		<style>
		ul.tpi_product_tracking_ul {
			list-style: none;
		}
		ul.tpi_product_tracking_ul li{
			font-size: 14px;
			margin: 0;
		}
		.tpi_products_heading{
			margin-top: -10px;
		}
		</style>
		<?php
	}	
}
