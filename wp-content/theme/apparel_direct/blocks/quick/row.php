<?php

$all = woo2_quick_v2_all();

?>
<div class="cloner no-display">
    <div class="row rowp">
        <input type="hidden" value="" data-name="product_id" tabindex="-1" />
        <input type="hidden" value="" data-name="variation_id" tabindex="-1" />
        <input type="hidden" value="" data-name="variation_price" tabindex="-1" />

        <div class="cell product_variation">
            <input type="text" data-name="product_style" class="dropdown-input input" />
            <?php /* <button class="dropdown-btn" type="button" data-name="btn" tabindex="-1"><span class="caret">></span></button> */ ?>
            <div class="product_not_available"></div>
        </div>

        <div class="cell product">
            <input type="text" data-name="product" class="dropdown-input input" />
            <?php /* <button class="dropdown-btn" type="button" data-name="btn" tabindex="-1"><span class="caret">></span></button> */ ?>

        </div>

        <?php foreach ($all['attributes'] as $attrib) : ?>
            <div class="cell attrib attrib-<?php echo $attrib ?>" data-attrib="<?php echo $attrib ?>">
            </div>
        <?php endforeach ?>

        <div class="cell upc">
            <input type="text" data-name="upc" class="dropdown-input input upc_cal" />
        </div>

        <div class="cell instock">
            <span class="instock_qty"></span>
        </div>
        
        <div class="cell qty">
            <input type="number" class="input qty_cal" data-name="qty" max="" min="0" />
        </div>

        <div class="cell action d-none">
            <a title="Add each variation" class="each" href="#"><span>+</span> each sku</a>
        </div>

        <div class="cell rxtray">
            <?php echo get_woocommerce_currency_symbol(); ?><span class="rxtray_price"></span>
            <?php /* ?>
            <input type="text" class="input" data-name="rxtray"/>
        <?php */ ?>
        </div>

        <div class="cell remove">
            <a title="Remove this item" class="remove" href="#">×</a>
        </div>

    </div>
</div>