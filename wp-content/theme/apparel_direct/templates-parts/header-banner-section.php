<?php 
 $header_banner_image = get_field('header_banner_image');
if(is_cart() || is_checkout() ||  $header_banner_image){
    $header_banner_image = get_field('header_banner_image');
    $header_custom_title = get_field('header_custom_title');
    if(!$header_custom_title){
        $header_custom_title =  get_the_title();

        if ( is_wc_endpoint_url( 'edit-account' )) {
            $header_custom_title = "Account Details";
        }
        if ( is_wc_endpoint_url( 'orders' )) {
            $header_custom_title = "Orders";
        }
        if ( is_wc_endpoint_url( 'view-order' )) {
            $header_custom_title = "Orders";
        }
        if ( is_wc_endpoint_url( 'edit-address' )) {
            $header_custom_title = "Address Book";
        }
        if ( is_wc_endpoint_url( 'edit-address' )) {
            $header_custom_title = "Address Book";
        }
        if ( is_wc_endpoint_url( 'payment-methods' )) {
            $header_custom_title = "Payment Methods";
        }
        if ( is_wc_endpoint_url( 'add-payment-method' )) {
            $header_custom_title = "Add payment method";
        }
        if ( $wp->request == 'my-account/my-lists') {
            $header_custom_title = "My Lists";
        }
    }
    
    ?>
    <div class="inner-banner" style="background-image: url(<?php echo $header_banner_image['url'] ?>);">
			<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 text-center">
                <h2 class="text-center text-uppercase"><?php echo $header_custom_title; ?></h2>
                <?php if(is_cart() || is_checkout()){ ?>
                <a href="<?php echo wc_get_page_permalink( 'shop' ); ?>">Back to Shopping</a>
                 <?php } ?>   
				</div>
			</div>
		</div>
	</div>
<?php } ?>
<?php

if(is_checkout()){ 
    $offer_content = get_field('offer_content', 'option',false, false);
    
    ?>

<?php if($offer_content){ ?>
    <div class="card offer-section">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-body"><?php echo $offer_content; ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php }?>

<?php }
?>