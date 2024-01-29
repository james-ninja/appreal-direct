<?php
/**
 * Wishlist manage template - Modern layout
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $page_title            string Page title
 * @var $template_part         string Template part currently being loaded (manage)
 * @var $user_wishlists        YITH_WCWL_Wishlist[] Array of user wishlists
 * @var $show_number_of_items  bool Whether to show number of items or not
 * @var $show_date_of_creation bool Whether to show date of creation or not
 * @var $show_download_as_pdf  bool Whether to show download button or not
 * @var $show_rename_wishlist  bool Whether to show rename button or not
 * @var $show_delete_wishlist  bool Whether to show delete button or not
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>
<?php 
	$user_wishlists = YITH_WCWL()->get_current_user_wishlists(); 
	$show_number_of_items = get_option( 'yith_wcwl_manage_num_of_items_show' );
	//$show_date_of_creation = get_option( 'yith_wcwl_manage_creation_date_show' );
	$show_delete_wishlist = get_option( 'yith_wcwl_manage_delete_wishlist_show' );

	$user_wishlists = array_slice($user_wishlists, 0, 3, true);

?>
<ul class="shop_table cart wishlist_table wishlist_manage_table modern_grid responsive" cellspacing="0">

	<?php
	if ( ! empty( $user_wishlists ) ) :
		foreach ( $user_wishlists as $wishlist ) :
			?>
			<li data-wishlist-id="<?php echo esc_attr( $wishlist->get_id() ); ?>">
				<div class="item-wrapper">
					<div class="item-details">
						<div class="wishlist-name wishlist-title <?php echo $show_rename_wishlist ? 'wishlist-title-with-form' : ''; ?>">
							<h3>
								<a class="wishlist-anchor" href="<?php echo esc_url( $wishlist->get_url() ); ?>"><?php echo esc_html( $wishlist->get_formatted_name() ); ?></a>
							</h3>

							<?php if ( $show_rename_wishlist ) : ?>
								<a class="show-title-form">
									<?php echo apply_filters( 'yith_wcwl_edit_title_icon', '<i class="fa fa-pencil"></i>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</a>
							<?php endif; ?>
						</div>

						<?php if ( $show_rename_wishlist ) : ?>
							<div class="hidden-title-form">
								<input type="text" value="<?php echo esc_attr( $wishlist->get_formatted_name() ); ?>" name="wishlist_options[<?php echo esc_attr( $wishlist->get_id() ); ?>][wishlist_name]"/>
								<div class="edit-title-buttons">
									<a href="#" class="hide-title-form">
										<?php echo apply_filters( 'yith_wcwl_cancel_wishlist_title_icon', '<i class="fa fa-remove"></i>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</a>
									<a href="#" class="save-title-form">
										<?php echo apply_filters( 'yith_wcwl_save_wishlist_title_icon', '<i class="fa fa-check"></i>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</a>
								</div>
							</div>
						<?php endif; ?>

						<table class="item-details-table">
							<?php if ( $show_number_of_items ) : ?>
								<tr class="wishlist-item-count">
									<td class="label"><?php esc_html_e( 'Items:', 'yith-woocommerce-wishlist' ); ?></td>
									<td class="value">
										<?php
											echo esc_html( sprintf( __( '%d items', 'yith-woocommerce-wishlist' ), $wishlist->count_items() ) );
										?>
									</td>
								</tr>
							<?php endif; ?>
							<?php /* ?> 
							<tr class="wishlist-privacy">
								<td class="label"><?php esc_html_e( 'Visibility:', 'yith-woocommerce-wishlist' ); ?></td>
								<td class="value">
									<select name="wishlist_options[<?php echo esc_attr( $wishlist->get_id() ); ?>][wishlist_privacy]" class="wishlist-visibility selectBox">
										<option value="0" class="public-visibility" <?php selected( $wishlist->get_privacy(), 0 ); ?> ><?php echo yith_wcwl_get_privacy_label( 0 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></option>
										<option value="1" class="shared-visibility" <?php selected( $wishlist->get_privacy(), 1 ); ?> ><?php echo yith_wcwl_get_privacy_label( 1 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></option>
										<option value="2" class="private-visibility" <?php selected( $wishlist->get_privacy(), 2 ); ?> ><?php echo yith_wcwl_get_privacy_label( 2 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></option>
									</select>
								</td>
							</tr>
							<?php */ ?> 
							<?php if ( $show_date_of_creation ): ?>
								<tr class="wishlist-dateadded">
									<td class="label"><?php esc_html_e( 'Created on:', 'yith-woocommerce-wishlist' ); ?></td>
									<td class="value"><?php echo esc_html( $wishlist->get_date_added_formatted() ); ?></td>
								</tr>
							<?php endif; ?>

							<?php if ( $show_delete_wishlist || $show_download_as_pdf ): ?>
								<tr>
									<td class="value" colspan="2">
										<?php if ( $show_download_as_pdf ): ?>
											<a class="wishlist-download" href="<?php echo esc_url( $wishlist->get_download_url() ); ?>">
												<i class="fa fa-download"></i>
											</a>
										<?php endif; ?>

										<?php if ( $show_delete_wishlist ): ?>
											<a class="wishlist-delete" onclick="return confirm('<?php esc_html_e( 'Are you sure you want to delete this wishlist?', 'yith-woocommerce-wishlist' ); ?>');" href="<?php echo esc_url( $wishlist->get_delete_url() ); ?>"><i class="fa fa-trash"></i></a>
										<?php endif; ?>
									</td>
								</tr>
							<?php endif; ?>
						</table>
						<?php
						//custom start
						// share options.
						$enable_share = get_option( 'yith_wcwl_enable_share' ) == 'yes' && ! $wishlist->has_privacy( 'private' );
						$share_facebook_enabled = get_option( 'yith_wcwl_share_fb' ) == 'yes';
						$share_twitter_enabled = get_option( 'yith_wcwl_share_twitter' ) == 'yes';
						$share_pinterest_enabled = get_option( 'yith_wcwl_share_pinterest' ) == 'yes';
						$share_email_enabled = get_option( 'yith_wcwl_share_email' ) == 'yes';
						$share_whatsapp_enabled = get_option( 'yith_wcwl_share_whatsapp' ) == 'yes';
						$share_url_enabled = get_option( 'yith_wcwl_share_url' ) == 'yes';

						$share_title = apply_filters( 'yith_wcwl_socials_share_title', __( 'Share on:', 'yith-woocommerce-wishlist' ) );
						$share_link_url = apply_filters( 'yith_wcwl_shortcode_share_link_url', $wishlist->get_url(), $wishlist );
						$share_link_title = apply_filters( 'plugin_text', urlencode( get_option( 'yith_wcwl_socials_title' ) ) );
						$share_summary = urlencode( str_replace( '%wishlist_url%', $share_link_url, get_option( 'yith_wcwl_socials_text' ) ) );

						$share_atts = array(
							'share_facebook_enabled' => $share_facebook_enabled,
							'share_twitter_enabled' => $share_twitter_enabled,
							'share_pinterest_enabled' => $share_pinterest_enabled,
							'share_email_enabled' => $share_email_enabled,
							'share_whatsapp_enabled' => $share_whatsapp_enabled,
							'share_url_enabled' => $share_url_enabled,
							'share_title' => $share_title,
							'share_link_url' => $share_link_url,
							'share_link_title' => $share_link_title,
						);


						if ( $share_facebook_enabled ) {
							$share_facebook_icon = get_option( 'yith_wcwl_fb_button_icon', 'fa-facebook' );
							$share_facebook_custom_icon = get_option( 'yith_wcwl_fb_button_custom_icon' );
	
							if ( ! in_array( $share_facebook_icon, array( 'none', 'custom' ) ) ) {
								$share_atts['share_facebook_icon'] = "<i class='fa {$share_facebook_icon}'></i>";
							} elseif ( 'custom' == $share_facebook_icon && $share_facebook_custom_icon ) {
								$alt_text = __( 'Share on Facebook', 'yith-woocommerce-wishlist' );
								$share_atts['share_facebook_icon'] = "<img src='{$share_facebook_custom_icon}' alt='{$alt_text}'/>";
							} else {
								$share_atts['share_facebook_icon'] = '';
							}
						}
	
						if ( $share_twitter_enabled ) {
							$share_twitter_summary = urlencode( str_replace( '%wishlist_url%', '', get_option( 'yith_wcwl_socials_text' ) ) );
							$share_twitter_icon = get_option( 'yith_wcwl_tw_button_icon', 'fa-twitter' );
							$share_twitter_custom_icon = get_option( 'yith_wcwl_tw_button_custom_icon' );
	
							$share_atts['share_twitter_summary'] = $share_twitter_summary;
	
							if ( ! in_array( $share_twitter_icon, array( 'none', 'custom' ) ) ) {
								$share_atts['share_twitter_icon'] = "<i class='fa {$share_twitter_icon}'></i>";
							} elseif ( 'custom' == $share_twitter_icon && $share_twitter_custom_icon ) {
								$alt_text = __( 'Tweet on Twitter', 'yith-woocommerce-wishlist' );
								$share_atts['share_twitter_icon'] = "<img src='{$share_twitter_custom_icon}' alt='{$alt_text}'/>";
							} else {
								$share_atts['share_twitter_icon'] = '';
							}
						}
	
						if ( $share_pinterest_enabled ) {
							$share_image_url = urlencode( get_option( 'yith_wcwl_socials_image_url' ) );
							$share_pinterest_icon = get_option( 'yith_wcwl_pr_button_icon', 'fa-pinterest' );
							$share_pinterest_custom_icon = get_option( 'yith_wcwl_pr_button_custom_icon' );
	
							$share_atts['share_summary'] = $share_summary;
							$share_atts['share_image_url'] = $share_image_url;
	
							if ( ! in_array( $share_pinterest_icon, array( 'none', 'custom' ) ) ) {
								$share_atts['share_pinterest_icon'] = "<i class='fa {$share_pinterest_icon}'></i>";
							} elseif ( 'custom' == $share_pinterest_icon && $share_pinterest_custom_icon ) {
								$alt_text = __( 'Pin on Pinterest', 'yith-woocommerce-wishlist' );
								$share_atts['share_pinterest_icon'] = "<img src='{$share_pinterest_custom_icon}' alt='{$alt_text}'/>";
							} else {
								$share_atts['share_pinterest_icon'] = '';
							}
						}
	
						if ( $share_email_enabled ) {
							$share_email_icon = get_option( 'yith_wcwl_em_button_icon', 'fa-email' );
							$share_email_custom_icon = get_option( 'yith_wcwl_em_button_custom_icon' );
	
							if ( ! in_array( $share_email_icon, array( 'none', 'custom' ) ) ) {
								$share_atts['share_email_icon'] = "<i class='fa {$share_email_icon}'></i>";
							} elseif ( 'custom' == $share_email_icon && $share_email_custom_icon ) {
								$alt_text = __( 'Share via email', 'yith-woocommerce-wishlist' );
								$share_atts['share_email_icon'] = "<img src='{$share_email_custom_icon}' alt='{$alt_text}'/>";
							} else {
								$share_atts['share_email_icon'] = '';
							}
						}
	
						if ( $share_whatsapp_enabled ) {
							$share_whatsapp_icon = get_option( 'yith_wcwl_wa_button_icon', 'fa-whatsapp' );
							$share_whatsapp_custom_icon = get_option( 'yith_wcwl_wa_button_custom_icon' );
							$share_whatsapp_url = '';
	
							if ( wp_is_mobile() ) {
								$share_whatsapp_url = 'whatsapp://send?text=' . __( 'My wishlist on ', 'yith-woocommerce-wishlist' ) . ' – ' . urlencode( $share_link_url );
							} else {
								$share_whatsapp_url = 'https://web.whatsapp.com/send?text=' . __( 'My wishlist on ', 'yith-woocommerce-wishlist' ) . ' – ' . urlencode( $share_link_url );
							}
	
							$share_atts['share_whatsapp_url'] = $share_whatsapp_url;
	
							if ( ! in_array( $share_whatsapp_icon, array( 'none', 'custom' ) ) ) {
								$share_atts['share_whatsapp_icon'] = "<i class='fa {$share_whatsapp_icon}'></i>";
							} elseif ( 'custom' == $share_whatsapp_icon && $share_whatsapp_custom_icon ) {
								$alt_text = __( 'Share on WhatsApp', 'yith-woocommerce-wishlist' );
								$share_atts['share_whatsapp_icon'] = "<img src='{$share_whatsapp_custom_icon}' alt='{$alt_text}'/>";
							} else {
								$share_atts['share_whatsapp_icon'] = '';
							}
						}
						yith_wcwl_get_template( 'share.php', array_merge( $share_atts, array( 'wishlist' => $wishlist ) ) );

						//custom end
						?>
					</div>
					<div class="product-thumbnail">
						<?php
						if ( $wishlist->has_items() ) :
							$items = $wishlist->get_items( 1 );

							foreach ( $items as $item ) {
								echo $item->get_product()->get_image(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
						else :
							?>
							<div class="placeholder-item empty-box">
								<p><?php echo wp_kses_post( apply_filters('yith_wcwl_modern_wishlist_empty_message', __( 'This wishlist is empty.<br/>Add some item soon!', 'yith-woocommerce-wishlist' ) ) ); ?></p>
							</div>
							<?php
						endif;
						?>
					</div>
				</div>
			</li>
		<?php
		endforeach;
	else:
		?>
		<li class="wishlist-empty">
			<?php echo wp_kses_post( YITH_WCWL_Frontend_Premium()->get_no_wishlist_message() ); ?>
		</li>
	<?php
	endif;
	?>

</ul>