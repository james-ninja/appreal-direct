<?php 
global $wpdb, $woocommerce;
	$headers[] = 'From: Apparel Direct Distributor <website@appareldirectdistributor.com>';
	//$headers = 'MIME-Version: 1.0' . "\r\n";
	//$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
	//$headers .= 'From: Apparel Direct Distributor <website@appareldirectdistributor.com>';

    // Define the WooCommerce product variation price table and product table.
    $product_variation_price_table = $wpdb->prefix . 'postmeta';
    $product_table = $wpdb->prefix . 'posts';
	
	$query = $wpdb->prepare(
    "SELECT p.post_title AS product_name, v.post_id AS variation_id, v.meta_value AS variation_price
    FROM $product_variation_price_table AS v
    INNER JOIN $product_table AS p ON v.post_id = p.ID
    WHERE v.meta_key = '_regular_price'
    AND (v.meta_value = %s OR v.meta_value = %s)",
    0,
    '0.00','0.0'
);
	
    $results = $wpdb->get_results($query);
	$totalProducts = count($results);

    if ($results) {
				ob_start();
				do_action( 'woocommerce_email_header', $email_heading );
				
				echo '<table width="100%" border="1" cellpadding="10" style="border-collapse: collapse;"><tbody>';
				echo '<tr><th align="left">Date & time:</th><td align="left">'.date("m-d-Y H:i:s").'</td></tr>';
				echo '<tr><th align="left">Products:</th><td align="left"><ul>';
				$newline = '
';
				echo 'Hi Admin,<br/><br/>';
				echo '<b>Total product :'.$totalProducts.'</b><br/><br/>';
				echo 'Below is list of product variation price with 0.<br/><br/>';
				foreach ($results as $result) {
					$product_name = $result->product_name;
					$variation_id = $result->variation_id;
					$variation_price = $result->variation_price;
					$product_url = get_permalink($variation_id);
					echo '<li>The price for variation ID '.$variation_id.' of product <a href="'.$product_url.'">'.$product_name.' is ('.$variation_price.')</a></li><br/>';
					//echo 'The price for variation ID '.$variation_id.' of product '.$product_name.' is ('.$variation_price.')'.$newline.''.$newline;
				}
				echo '</ul></td></tr>';
				echo '</tbody></table>';
			
				do_action( 'woocommerce_email_footer', $email );
				$message = ob_get_clean();
				
				add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );


				$to = "james@ninjatechnolabs.com";
				$subject = "Apparel Direct - Product Variation Price 0 (Total Count ".$totalProducts.")";
				
				$retval = wp_mail($to, $subject, $message,$headers);
				remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
		
			//wp_mail($to, $subject, implode("<br>", $message),$headers);  
		    
    }
?>