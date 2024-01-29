=== WooCommerce Bulk Variations ===
Contributors: barn2media
Tags: woocommerce, table, product, list, grid, variations
Requires at least: 4.7
Tested up to: 5.8.1
Requires PHP: 7.1
Stable tag: 1.2.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

== Description ==

Displays product variations in a variations order form or price matrix.

== Installation ==

1. Download the plugin from the order confirmation email or your Account. This will save a zip file to your computer.
2. Go to Plugins -> Add New -> Upload and select the plugin ZIP file.
3. Once installed, click to Activate the plugin
4. Enter your license key under WooCommerce -> Settings -> Products -> Bulk variations
5. Choose your global settings on the settings page and click Save Changes.
6. To create a bulk variations grid, either enable it globally on the settings page, enable it for individual products, or create it using a shortcode.

== Frequently Asked Questions ==

Please refer to [our support page](https://barn2.com/support-center/).

== Changelog ==

= 1.2.1 =
Release date 16 September 2021

* Fix: Resolved fatal error when WooCommerce is not active

<!--more-->

= 1.2 =
Release date 14 September 2021

* Fix: Resolved incorrect stock availability markup output
* Fix: Resolved missing columns/rows when attribute terms are equivalent in a loose comparison (e.g. "0" and "00")
* Dev: Code refactoring

= 1.1.9 =
Release date 20 May 2021

* Fix: Resolved issue with Quick View Pro lightboxes being closed unintentionally.
* Fix: Resolved issue with 'Add to Cart' button never enabling when all variation quantities default to zero.
* Fix: Resolved issue with variation grid not appearing when every variation is out of stock.

= 1.1.8 =
Release date 5 May 2021

* Fix: Resolved warning regarding photoswipe template when lightboxes are enabled and the theme does not already support photoswipe.
* Fix: Resolved issue with image lightbox while using Bulk Variations inside Quick View Pro.

= 1.1.7 =
Release date 29 March 2021

* Fix: Added translation functions to missing stock level strings.

= 1.1.6 =
Release date 22 March 2021

* Fix: Resolved issue with empty thousands separator crashing browser when total price is at or above 1,000.
* Change: Allow grid to display when price of product is zero.
* New: Added additional development hook to allow quantity inputs to utilize standard WooCommerce woocommerce_quantity_input_args filter.
* New: Added Bulk Variations to the WooCommerce Admin menu.

= 1.1.5 =
Release date 2 March 2021

* New: Added filter to table cell output for easier development customization of WBV grid.
* Fix: Resolved issue with zero value attributes being hidden from variations table grid.

= 1.1.4 =
Release date 10 February 2021

* Fix: Resolved issue with quantity selection on product with negative stock levels and backorders are allowed.
* Fix: Resolved conflict with default quantities in the WooCommerce Default Quantity plugin.

= 1.1.3 =
Release date 9 February 2021

* Fix: Resolved issue with taxes not being included in total amount calculation.

= 1.1.2 =
Release date 9 February 2021

* Fix: Resolved issue with displaying attribute labels that are negative numbers.

= 1.1.1 =
Release date 3 February 2021

* Fix: Resolved fatal error on versions of PHP older than 7.2.

= 1.1 =
Release date 1 February 2021

* New: Added lightbox support to grid thumbnail images (enabled by default, disable in settings).
* New: Added stock indicators to variations grid (disabled by default, enable in settings).

= 1.0.8 =
Release date 09 November 2020

* Fix: Added compatibility for OceanWP and other themes that are not using the woocommerce_template_single_add_to_cart hook.

= 1.0.7 =
Release date 15 September 2020

* Fix: Bug with non-numeric values.

= 1.0.6 =
Release date 11 September 2020

* New: Add support for alternative decimal separators in the 'Total' field.

= 1.0.5 =
Release date 9 September 2020

* Fix: Bug for quantity inputs not being displayed for vertical layouts with one attribute.

= 1.0.4 =
Release date 23 July 2020

* Tweak: Added missing strings.

= 1.0.3 =
Release date 9 July 2020

* Fix: Bug with the global option affecting other product types.

= 1.0.2 =
Release date 3 June 2020

* Fully tested with WooCommerce 4.2.

= 1.0.1 =
Release date 19 May 2020

* Ensure that product-level settings can be saved without previously having saved the global settings.

= 1.0 =
Release date 7 May 2020

* Initial release.
