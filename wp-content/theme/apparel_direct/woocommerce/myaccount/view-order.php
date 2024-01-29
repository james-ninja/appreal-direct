<?php

/**
 * View Order
 *
 * Shows the details of a particular order on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/view-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */
//ob_start();
defined('ABSPATH') || exit;

$notes = $order->get_customer_order_notes();
?>
<div class="order-details-head">
	<h2 class="woocommerce-order-details__title"><?php esc_html_e('Order summary', 'woocommerce'); ?></h2>
	<div class="order_help_print">
		<?php
		$current_user = wp_get_current_user();
		echo '<a href="javascript:void(0)" data-emailid="' . $current_user->user_email . '" data-user-name="' . $current_user->user_firstname . '" data-orderid="' . $order->id . '" class="woocommerce-button button order-help-btn">Help</a>';
		?>
		<?php echo do_shortcode('[wcpdf_download_invoice link_text="Print order"]'); ?>
		<a href="javascript:void(0)" class="ad_export_order" onclick="ad_export_order()">Export order</a>
	</div>
</div>
<p>
	<?php
	printf(
		/* translators: 1: order number 2: order date 3: order status */
		esc_html__('Your order #%1$s was placed on %2$s and is currently %3$s.', 'woocommerce'),
		'<mark class="order-number">' . $order->get_order_number() . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		'<mark class="order-date">' . wc_format_datetime($order->get_date_created()) . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		'<mark class="order-status">' . wc_get_order_status_name($order->get_status()) . '</mark>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
	?>
</p>

<?php

//function export_ad_order($order) {
	$ad_order_export = $_GET['export'];
	if ($ad_order_export) {
		while (ob_get_level()) {
			ob_end_clean();
		}
		ob_start();
		$ast = AST_Pro_Actions::get_instance();
		$tracking_items = $ast->get_tracking_items($order->get_id());

		$order_shipping_address = ad_formatted_shipping_address($order);
		$order_billing_address = ad_formatted_billing_address($order);

		foreach ($order->get_items() as $item_id => $item) {
			$tracking_number = '';
			$product = $item->get_product();
			$item_meta_array = array();
			if ($product) {
				$product_id = wp_get_post_parent_id($product->get_id());
				$product_title = get_the_title($product_id);
				$product_style = get_post_meta($product_id, 'product_style', true);
				$upc_field = get_post_meta($product->get_id(), 'upc_field', true);
				$variation_attributes = $product->get_variation_attributes();
				$variation_color = woo2_helper_attribute_name('pa_color', $variation_attributes['attribute_pa_color']);
				$variation_size = woo2_helper_attribute_name('pa_size', $variation_attributes['attribute_pa_size']);

				foreach ($tracking_items as $tracking_item) {

					if (isset($tracking_item['products_list']) && '' != $tracking_item['products_list']) {
						if (in_array($product->get_id(), array_column($tracking_item['products_list'], 'product'))) {
							$tracking_number = $tracking_item['tracking_number'];
						}
					}
				}
			} else {
				$product_title = $item->get_name();
				$upc_field = '';
				$product_style = '';

				foreach ($item->get_formatted_meta_data('_', true) as $meta_id => $meta) {
					$value = wp_kses_post(make_clickable(trim(strip_tags($meta->display_value))));
					$item_meta_array[] = $value;
				}
				$variation_size = $item_meta_array[0];
				$variation_color = $item_meta_array[1];
				$tracking_number = '';
			}


			$items_data[] = array(
				'Product' => $product_title,
				'UPC' => $upc_field,
				'Style' => $product_style,
				'Size' => $variation_size,
				'Color' => $variation_color,
				'Quantity'    => $item->get_quantity(),
				'Price'       => $item->get_subtotal(),
				'Tracking Number' => $tracking_number
			);
		}

		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()
			->setCreator("user")
			->setLastModifiedBy("user")
			->setTitle("Order Details")
			->setSubject("Order Details")
			->setDescription("Order Details")
			->setKeywords("Order Details")
			->setCategory("Order Details");

		// Set the active Excel worksheet to sheet 0
		$objPHPExcel->setActiveSheetIndex(0);

		// Sheet cells
		$cell_definition = array(
			'A' => 'Product',
			'B' => 'UPC',
			'C' => 'Style',
			'D' => 'Size',
			'E' => 'Color',
			'F' => 'Quantity',
			'G' => 'Price',
			'H' => 'Tracking Number'
		);

		$ad_company_name = 'Apparel Direct Distributor';
		$ad_company_address_email = 'customerservice@appareldirectdistributor.com';
		$ad_company_address = '390 Cassell Street Winston-Salem, NC 27107';
		$ad_company_address_phone = '336-265-2255';

		$styleArray = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => 'FFFFFF')
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => '000000')
			)
		);

		// Billing and shipping address title
		$objPHPExcel->getActiveSheet()->setCellValue("A8", 'Billing Address');
		$objPHPExcel->getActiveSheet()->setCellValue("E8", 'Shipping Address');
		$objPHPExcel->getActiveSheet()->mergeCells('A8:D8');
		$objPHPExcel->getActiveSheet()->mergeCells('E8:H8');
		$objPHPExcel->getActiveSheet()->getStyle('A8:H8')->getFont()->setBold(true);

		// Heading Style
		$objPHPExcel->getActiveSheet()->getStyle('A8:H8')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle("A14:H14")->applyFromArray($styleArray);

		// Billing and shipping address 
		$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(-1);
		$objPHPExcel->getActiveSheet()->setCellValue("A9", $order_shipping_address);
		$objPHPExcel->getActiveSheet()->setCellValue("E9", $order_billing_address);
		$objPHPExcel->getActiveSheet()->mergeCells('A9:D11');
		$objPHPExcel->getActiveSheet()->mergeCells('E9:H11');
		$objPHPExcel->getActiveSheet()->getStyle('A9:H9')->getAlignment()->setWrapText(true);

		// Logo
		$objPHPExcel->getActiveSheet()->mergeCells('A1:C5');

		//Company Name
		$objPHPExcel->getActiveSheet()->setCellValue("D1", $ad_company_name);
		$objPHPExcel->getActiveSheet()->mergeCells('D1:H1');
		$objPHPExcel->getActiveSheet()->getStyle('D1:H1')->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle("D1:H1")->getFont()->setBold(true);

		//Company Email
		$objPHPExcel->getActiveSheet()->setCellValue("D2", $ad_company_address_email);
		$objPHPExcel->getActiveSheet()->mergeCells('D2:H2');
		$objPHPExcel->getActiveSheet()->getStyle('D2:H2')->getAlignment()->setWrapText(true);

		//Company Address
		$objPHPExcel->getActiveSheet()->setCellValue("D3", $ad_company_address);
		$objPHPExcel->getActiveSheet()->mergeCells('D3:H3');
		$objPHPExcel->getActiveSheet()->getStyle('D3:H3')->getAlignment()->setWrapText(true);

		//Company Phone
		$objPHPExcel->getActiveSheet()->setCellValue("D4", $ad_company_address_phone);
		$objPHPExcel->getActiveSheet()->mergeCells('D4:H4');
		$objPHPExcel->getActiveSheet()->getStyle('D3:H4')->getAlignment()->setWrapText(true);

		//$objPHPExcel->getActiveSheet()->getStyle('A15:A100')->getAlignment()->setWrapText(true);
		//$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		//Order Number
		$objPHPExcel->getActiveSheet()->setCellValue("D5", 'Order Number');
		$objPHPExcel->getActiveSheet()->setCellValue("F5", $order->get_order_number());
		$objPHPExcel->getActiveSheet()->getStyle('D5')->getFont()->setBold(true);
		
		//$objPHPExcel->getActiveSheet()->mergeCells('D1:E5');

		// Build headers
		foreach ($cell_definition as $column => $value)
			$objPHPExcel->getActiveSheet()->setCellValue("{$column}14", $value);


		// Build cells
		$rowCount = 0;
		while ($rowCount < count($items_data)) {
			$cell = $rowCount + 15;
			foreach ($cell_definition as $column => $value)
				$objPHPExcel->getActiveSheet()->setCellValue($column . $cell, $items_data[$rowCount][$value]);

			$rowCount++;
		}

		// Order Total
		$after_item_num = count($items_data) + 16;
		$column_total = 'F';
		$column_total_value = 'G';

		$rowCount_total = 1;
		if ($subtotal = (float)$order->get_subtotal()) {
			$total_rows[] = array(
				'title' => 'Subtotal',
				'value' => $subtotal
			);
		}

		if ($cart_discount = (float)get_post_meta($order->get_id(), '_cart_discount', true)) {
			$total_rows[] = array(
				'title' => 'Discount',
				'value' => $order->cart_discount
			);
		}
		if ($order_shipping = (float)get_post_meta($order->get_id(), '_order_shipping', true)) {

			$total_rows[] = array(
				'title' => 'Shipping',
				'value' => $order_shipping . ' via ' . $order->get_shipping_method()
			);
		}
		if ($order_tax = (float)get_post_meta($order->get_id(), '_order_tax', true)) {
			$total_rows[] = array(
				'title' => 'tax',
				'value' => $order_tax
			);
		}
		if ($payment_method = $order->get_payment_method_title()) {
			$total_rows[] = array(
				'title' => 'Payment Method',
				'value' => $payment_method
			);
		}

		if ($gettotals = (float)$order->get_total()) {
			$total_rows[] = array(
				'title' => 'Total',
				'value' => $gettotals
			);
		}

		foreach ($total_rows as $key => $total) {

			$objPHPExcel->getActiveSheet()->setCellValue($column_total . ($after_item_num + $rowCount_total), $total['title']);
			$objPHPExcel->getActiveSheet()->setCellValue($column_total_value . ($after_item_num + $rowCount_total), $total['value']);

			$rowCount_total++;
		}

		//$objPHPExcel->getActiveSheet()->getStyle($column_total_value . ($after_item_num +1).':'.$column_total_value . ($after_item_num + $rowCount_total))->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
		$objPHPExcel->getActiveSheet()->getStyle($column_total . ($after_item_num + 1) . ':' . $column_total . ($after_item_num + $rowCount_total))->getFont()->setBold(true);

		$IMG = get_stylesheet_directory_uri() . '/assets/images/logo-new.png';

		$objDrawing = new PHPExcel_Worksheet_Drawing();    //create object for Worksheet drawing

		if (wc_is_valid_url($IMG)) {

			$url  = $IMG;
			$path = get_temp_dir() . '/' . md5($url); //Path to signature .jpg file

			if (!file_exists($path)) {

				$ch = curl_init($url);
				$fp = fopen($path, 'wb');
				curl_setopt($ch, CURLOPT_FILE, $fp);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_exec($ch);
				curl_close($ch);
				fclose($fp);
			}
		} else {
			$path = $IMG;
		}
		if (file_exists($path)) {

			$objDrawing->setName('Logo');
			$objDrawing->setDescription('Logo');
			$objDrawing->setPath($path);
			$objDrawing->setOffsetX(40);
			$objDrawing->setOffsetY(10);
			$objDrawing->setHeight(72);
			$objDrawing->setWidth(110);
			//$objDrawing -> setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
			$objDrawing->setCoordinates('A1');
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
		}



		$ordenumber = $order->get_order_number();
		// Excel file name for download 
		$fileName = "export_order_" . $ordenumber . ".xlsx";

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $fileName . '"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		ob_end_flush();
		exit;
	}


//export_ad_order($order);
?>

<?php if ($notes) : ?>
	<h2><?php esc_html_e('Order updates', 'woocommerce'); ?></h2>
	<ol class="woocommerce-OrderUpdates commentlist notes">
		<?php foreach ($notes as $note) : ?>
			<li class="woocommerce-OrderUpdate comment note">
				<div class="woocommerce-OrderUpdate-inner comment_container">
					<div class="woocommerce-OrderUpdate-text comment-text">
						<p class="woocommerce-OrderUpdate-meta meta"><?php echo date_i18n(esc_html__('l jS \o\f F Y, h:ia', 'woocommerce'), strtotime($note->comment_date)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
																		?></p>
						<div class="woocommerce-OrderUpdate-description description">
							<?php echo wpautop(wptexturize($note->comment_content)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
							?>
						</div>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
				</div>
			</li>
		<?php endforeach; ?>
	</ol>
<?php endif; ?>
<?php
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
if ($show_customer_details) {
	wc_get_template('order/order-details-customer.php', array('order' => $order));
}
?>
<?php do_action('woocommerce_view_order', $order_id); ?>
<div id="modal_order_form" style="display: none;" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/cancel.png" alt="cancel"></button>
			<div class="modal-body text-center">
				<?php echo do_shortcode('[contact-form-7 id="1123" title="Order Support Form"]'); ?>
			</div>
		</div>
	</div>
</div>
<script>
	function ad_export_order() {
		var url = window.location.href;
		if (url.indexOf('?') > -1) {
			url += '&export=1'
		} else {
			url += '?export=1'
		}
		window.location.href = url;
	}
</script>