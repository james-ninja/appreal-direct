<?php
$so_ns_text = get_field('so_ns_text', 'option');
$so_ns_form_shortcode = get_field('so_ns_form_shortcode', 'option');
?>
<div class="newsletter" id="newsletter">
    <div class="container">
        <div class="row">
            <div class="col-xl-5">
                <p><?php echo $so_ns_text; ?></p>
            </div>
            <div class="col-xl-7">
                    <?php echo do_shortcode($so_ns_form_shortcode); ?>
            </div>
        </div>
    </div>
</div>