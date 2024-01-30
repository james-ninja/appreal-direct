<?php
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('CWG_Instock_Premium_Extensions')) {

	class CWG_Instock_Premium_Extensions {

		public function __construct() {
			add_action('admin_menu', array($this, 'add_settings_menu'), 999);
		}

		public function add_settings_menu() {
			add_submenu_page('edit.php?post_type=cwginstocknotifier', __('Add-ons', 'back-in-stock-notifier-for-woocommerce'), __('Add-ons', 'back-in-stock-notifier-for-woocommerce'), 'manage_woocommerce', 'cwg-instock-extensions', array($this, 'manage_settings'));
		}

		public function manage_settings() {
			$this->display_as_html();
		}

		public function display_as_html() {
			$array_of_extensions = apply_filters('cwginstock_addon_list_with_link', array(
				'Bundle Add-ons - Back In Stock Notifier for WooCommerce' => 'https://codewoogeek.online/shop/back-in-stock-notifier-bundle-add-ons/',
				'WPML - Back In Stock Notifier for WooCommerce' => 'https://codewoogeek.online/shop/back-in-stock-notifier/wpml/',
				'Unsubscribe - Back In Stock Notifier for WooCommerce' => 'https://codewoogeek.online/shop/back-in-stock-notifier/unsubscribe/',
				'Double Opt-In - Back In Stock Notifier for WooCommerce' => 'https://codewoogeek.online/shop/back-in-stock-notifier/doubleoptin/',
				'Ban Email Domains and Email Addresses - Back In Stock Notifier for WooCommerce' => 'https://codewoogeek.online/shop/back-in-stock-notifier/ban-emails/',
				'Export CSV - Back In Stock Notifier for WooCommerce' => 'https://codewoogeek.online/shop/back-in-stock-notifier/export-csv/',
				'Custom CSS - Back In Stock Notifier for WooCommerce' => 'https://codewoogeek.online/shop/back-in-stock-notifier/custom-css/',
				'Mailchimp - Back In Stock Notifier for WooCommerce' => 'https://codewoogeek.online/shop/back-in-stock-notifier/mailchimp/',
				'Track Sales - Back In Stock Notifier for WooCommerce' => 'https://codewoogeek.online/shop/back-in-stock-notifier/track-sales/',
				'Import CSV - Back In Stock Notifier for WooCommerce' => 'https://codewoogeek.online/shop/back-in-stock-notifier/import-csv/',
				'Edit Subscribers - Back In Stock Notifier for WooCommerce' => 'https://codewoogeek.online/shop/back-in-stock-notifier/edit-subscribers/',
				'PolyLang - Back In Stock Notifier for WooCommerce' => 'https://codewoogeek.online/shop/back-in-stock-notifier/polylang/',
				'More Addons coming soon' => '',
			));
			?>
			<div class="wrap cwg-addon-wrap">
				<h1>
					Add-ons for Back In Stock Notifier
				</h1>
				<p>
					We created few add-ons below which boosts the core products with extended functionality.
				</p>
				<h3>Advantage of buying this Add-ons</h3>
				<ol>
					<li>
						You can use this add-ons for Unlimited Sites, hence single purchase is enough.
					</li>
					<li>
						Premium Support
					</li>
					<li>
						No Monthly/Yearly Subscription - One time fee and life time updates.
					</li>
					<li>
						Handy Price for each Add-on - Just $5.00 (limited period) only
					</li>
				</ol>

				<h3>Add-on Price Change from 15th January 2022</h3>
				<p> We're planned to increase our add-on price from $5.00 to $10.00 on coming 15th January 2022. People whoever purchased already need not to pay extra amount, as this applicable only to the new buyers.</p>
				<?php
				$i = 1;
				foreach ($array_of_extensions as $name => $url) {

					$final_url = '' != $url ? $url : 'http://codewoogeek.online/product-category/back-in-stock-notifier/';
					?>

					<div class="cwg-section">
						<a href="<?php echo esc_url_raw($final_url); ?>" target="__blank">
							<span style="width: 200px;height: 200px;position: absolute;">
								<span class="cwg-addon-title">
									<?php esc_html_e($name); ?>
								</span>
								<?php if ('' != $url) { ?>
									<?php
									if (1 == $i) {
										$price_tag = '$40.00';
									} else {
										$price_tag = '$10.00';
									}
									?>
									<span class="pricetag"><?php echo do_shortcode($price_tag); ?></span>
									<span class="cwg-addon-bottom" style="font-weight:bold;">

										<?php
										if (1 == $i) {
											$text = 'Unlimited Sites for $40.00';
										} else {
											$text = 'Unlimited Sites for $10.00';
										}
										echo do_shortcode(get_submit_button($text));
										?>
									</span>
								<?php } ?>
							</span>
						</a>
					</div>
					<?php
					$i++;
				}
				?>
				<div class="clear"></div>
			</div>
			<?php
		}

	}

	new CWG_Instock_Premium_Extensions();
}
