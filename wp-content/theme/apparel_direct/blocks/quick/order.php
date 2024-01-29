<?php

$all = woo2_quick_v2_all();
$all_var = woo2_quick_v2_all_variation();

$editclass = "";
$total = 0;
$total_qty = 0;

if (isset($_SESSION['editOrder']['pr']) && count($_SESSION['editOrder']['pr']) > 0) {

	$editdata = $_SESSION['editOrder']['pr'];
	$total = count($editdata);
	$editclass = "editorder";
}

function get_product_variation($product_id, $type = "")
{

	$product = new WC_Product_Variable($product_id);
	$variations = $product->get_available_variations();

	$data = [];

	if ($type == 'color') {

		foreach ($variations as $variation) {
			$data[] = $variation['attributes']['attribute_pa_color'];
		}
	} else if ($type == 'size') {

		foreach ($variations as $variation) {
			$data[] = $variation['attributes']['attribute_pa_size'];
		}
	} else {

		foreach ($variations as $variation) {
			$data[] = $variation['attributes']['attribute_pa_pack-of'];
		}
	}

	$data = array_unique($data);

	return $data;
}

?>



<div class="block-quick-order woocommerce">

	<div class="table master" data-index="<?php echo $total; ?>">

		<?php

		if (isset($_SESSION['editOrder']['pr']) && count($_SESSION['editOrder']['pr']) > 0) {

			$editdata = $_SESSION['editOrder']['pr'];

			$exist_packof = false;

			foreach ($editdata as $data) {

				$packof = get_product_variation($data['product_id'], 'packof');

				if ($packof[0] != "") {

					$exist_packof = true;
				}
			}

		?>

			<div class="thead row">

				<div class="cell product_style">Style</div>
				<div class="cell product">Product</div>

				<?php

				foreach ($all['attributes'] as $attrib) :

					if ($attrib == "pa_pack-of" && $exist_packof == true) { ?>

						<div class="cell attrib attrib-<?php echo $attrib ?>" data-attrib="<?php echo $attrib ?>"><?php echo wc_attribute_label($attrib) ?></div>

					<?php } else if ($attrib != "pa_pack-of") { ?>

						<div class="cell attrib attrib-<?php echo $attrib ?>" data-attrib="<?php echo $attrib ?>"><?php echo wc_attribute_label($attrib) ?></div>

				<?php }

				endforeach

				?>
				<div class="cell upc">UPC</div>
				<div class="cell instock">In Stock</div>
				<div class="cell qty">Qty</div>
				<div class="cell action d-none"></div>
				<div class="cell rxtray">Subtotal</div>
				<div class="cell remove"></div>

			</div>

			<?php

			$count = 0;

			foreach ($editdata as $data) {

				if ($data['product_id']) {

					$total_qty = $total_qty + $data['qty'];

			?>

					<div class="row rowp product-row available <?php echo $editclass; ?>" data-index="<?php echo $count; ?>" style="">

						<input type="hidden" value="<?php echo $data['product_id']; ?>" data-name="product_id" tabindex="-1" name="pr[<?php echo $count ?>][product_id]">

						<input type="hidden" value="<?php echo $data['variation_id']; ?>" data-name="variation_id" tabindex="-1" name="pr[<?php echo $count ?>][variation_id]">

						<input type="hidden" value="<?php echo $data['variation_price']; ?>" data-name="variation_price" tabindex="-1" name="pr[<?php echo $count ?>][variation_price]">

						<div class="cell product_style">

							<div class="awesomplete">

								<input type="text" data-name="product_style" class="dropdown-input input" name="pr[<?php echo $count ?>][product_style]" value="<?php echo $data['product_style']; ?>" required="required" id="pr_0" autocomplete="off" aria-autocomplete="list">

								<span class="visually-hidden" role="status" aria-live="assertive" aria-relevant="additions"><?php echo $data['product_style']; ?></span>

							</div>
							<div class="product_not_available"></div>
						</div>

						<div class="cell product">

							<div class="awesomplete">

								<input type="text" data-name="product" class="dropdown-input input" name="pr[<?php echo $count ?>][product]" value="<?php echo $data['product']; ?>" required="required" id="pr_0" autocomplete="off" aria-autocomplete="list">

								<span class="visually-hidden" role="status" aria-live="assertive" aria-relevant="additions"><?php echo $data['product']; ?></span>

							</div>
						</div>

						<div class="cell attrib attrib-pa_color" data-attrib="pa_color" style="display: table-cell;">

							<select class="attribute" name="pr[<?php echo $count ?>][attribute_pa_color]" required="required" style="width: 100%;">

								<option value="">Please Select...</option>

								<?php

								$colors = get_product_variation($data['product_id'], 'color');

								foreach ($colors as $color) {

									echo '<option value="' . $color . '" ' . ($color == $data['attribute_pa_color'] ? "Selected" : "") . '>' . $color . '</option>';
								}

								?>

							</select>

						</div>

						<div class="cell attrib attrib-pa_size" data-attrib="pa_size" style="display: table-cell;">

							<?php

							$sizes = get_product_variation($data['product_id'], 'size');

							if ($sizes[0] != "") {

							?>

								<select class="attribute" name="pr[<?php echo $count ?>][attribute_pa_size]" required="required" style="width: 100%;">

									<?php

									foreach ($sizes as $size) {

										echo '<option value="' . $size . '" ' . ($size == $data['attribute_pa_size'] ? "Selected" : "") . ' >' . $size . '</option>';
									}

									?>

								</select>

							<?php } ?>

						</div>



						<?php if ($exist_packof == true) { ?>

							<div class="cell attrib attrib-pa_pack-of" data-attrib="pa_pack-of" style="display: table-cell;">

								<?php

								$packof = get_product_variation($data['product_id'], 'packof');

								if ($packof[0] != "") {

								?>
									<select class="attribute" name="pr[<?php echo $count ?>][attribute_pa_pack-of]" required="required" style="width: 100%;">

										<?php

										foreach ($packof as $pack) {

											echo '<option value="' . $pack . '" ' . ($pack == $data['attribute_pa_pack-of'] ? "Selected" : "") . ' >' . $pack . '</option>';
										}

										?>

									</select>

								<?php } ?>

							</div>

						<?php } ?>

						<div class="cell upc">

							<div class="awesomplete">

								<input type="text" data-name="upc" class="dropdown-input input" name="pr[<?php echo $count ?>][upc]" value="<?php echo $data['upc']; ?>" required="required" id="pr_0" autocomplete="off" aria-autocomplete="list">
								<span class="visually-hidden" role="status" aria-live="assertive" aria-relevant="additions"><?php echo $data['upc']; ?></span>
							</div>

						</div>

						<div class="cell instock">
            				<span class="instock_qty"></span>
						</div>
						<div class="cell qty">

							<input type="number" class="qty_cal input" data-name="qty" min="0" max="" name="pr[<?php echo $count ?>][qty]" value="<?php echo $data['qty']; ?>">

						</div>

						<div class="cell action">

							<a title="Add each variation" class="each" href="#">
								<span>+</span> each sku
							</a>

						</div>

						<div class="cell rxtray">
							<?php echo get_woocommerce_currency_symbol(); ?><span class="rxtray_price"></span>
							<?php /* ?>
							<input type="text" class="input" data-name="rxtray" name="pr[<?php echo $count ?>][rxtray]" value="<?php echo $data['rxtray']; ?>">
							<?php */ ?>
						</div>

						<div class="cell remove">
							<a title="Remove this item" class="remove" href="#">×</a>
						</div>

					</div>

			<?php

					$count++;
				}
			}
		} else {

			?>

			<div class="thead row">

				<div class="cell product_style">Style</div>
				<div class="cell product">Product</div>

				<?php foreach ($all['attributes'] as $attrib) : ?>
					<div class="cell attrib attrib-<?php echo $attrib ?>" data-attrib="<?php echo $attrib ?>"><?php echo wc_attribute_label($attrib) ?></div>
				<?php endforeach ?>

				<div class="cell upc">UPC</div>
				<div class="cell instock">In Stock</div>
				<div class="cell qty">Qty</div>
				<div class="cell action d-none"></div>
				<div class="cell rxtray">Subtotal</div>
				<div class="cell remove"></div>

			</div>

		<?php

		}

		?>

	</div>

	<div class="table quick_footer_section">

		<div class="tfoot row">

			<div class="left cell">
				<button type="button" class="add-item button button-add-to-cart">Add Item</button>
				<button type="button" class="add-more-item button button-add-to-cart">Add Multiple Items</button>
				<a class="btn" href="<?php echo wc_get_page_permalink('shop'); ?>">Continue Shopping</a>
			</div>

			<div class="right cell">
				<span class="total"><strong>Items:</strong> <b><?php echo $total_qty; ?></b></span>
				<span class="total_price"><strong>Total:</strong><strong> <?php echo get_woocommerce_currency_symbol(); ?> </strong><b></b></span>

				<?php /* ?>
				<button class="checkout-b button-add-to-cart button" type="submit">Review Order</button>
				<?php */ ?>
				<button disabled class="btn primary-btn add_to_cart_quick" type="submit">Add to Cart</button>

			</div>

		</div>

	</div>

</div>

<ul id="all_products" class="no-display">
	<?php foreach ($all['products'] as $key => $value) : ?>
		<li data-id="<?php echo $value['id'] ?>"><?php echo $value['text'] ?></li>
	<?php endforeach ?>
</ul>

<ul id="all_products_style" class="no-display">
	<?php foreach ($all['products'] as $key => $value) : ?>
		<li data-id="<?php echo $value['id'] ?>"><?php echo $value['product_style'] ?></li>
	<?php endforeach ?>

</ul>

<ul id="all_products_variation_upc" class="no-display">
	<?php foreach ($all_var['products'] as $key => $value) : ?>
		<li data-id="<?php echo $value['id'] ?>" data-parentid="<?php echo $value['parent_id'] ?>"><?php echo $value['text'] ?></li>
	<?php endforeach ?>
</ul>