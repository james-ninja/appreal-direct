<?php

if (!defined('ABSPATH')) {
	exit;
}

abstract class CWG_Instock_Mailer {

	public function __construct() {
		
	}

	public function from_email() {
		$admin_email = get_bloginfo('admin_email');
		return apply_filters('cwginstock_from_email', get_option('woocommerce_email_from_address', $admin_email));
	}

	public function from_name() {
		$from_name = get_bloginfo('name');
		return apply_filters('cwginstock_from_name', get_option('woocommerce_email_from_name', $from_name));
	}

	public function format_data( $message) {
		$replace = html_entity_decode($message);
		return $replace;
	}

	public function get_subject() {
		return apply_filters('cwg' . $this->slug . '_subject', $this->format_data(do_shortcode($this->replace_shortcode($this->get_subject))), $this->subscriber_id);
	}

	public function get_message() {
		return apply_filters('cwg' . $this->slug . '_message', do_shortcode($this->replace_shortcode($this->get_message)), $this->subscriber_id);
	}

	private function replace_shortcode( $content) {
		$obj = new CWG_Instock_API();
		if (!get_post_meta($this->subscriber_id, 'cwginstock_bypass_pid', true)) {
			$pid = get_post_meta($this->subscriber_id, 'cwginstock_pid', true);
		} else {
			$pid = get_post_meta($this->subscriber_id, 'cwginstock_bypass_pid', true);
		}
		$product_name = $obj->display_product_name($this->subscriber_id);
		$only_product_name = $obj->display_only_product_name($this->subscriber_id);
		$product_link = $obj->display_product_link($this->subscriber_id);
		$only_product_sku = $obj->get_product_sku($this->subscriber_id);
		$product_image = $obj->get_product_image($this->subscriber_id);
		$subscriber_name = $obj->get_subscriber_name($this->subscriber_id);
		$subscriber_phone = $obj->get_subscriber_phone($this->subscriber_id);
		$cart_url = $obj->get_cart_link($this->subscriber_id); //esc_url_raw(add_query_arg('add-to-cart', $pid, get_permalink(wc_get_page_id('cart'))));
		$blogname = get_bloginfo('name');
		$find_array = array('{product_name}', '{product_id}', '{product_link}', '{shopname}', '{email_id}', '{subscriber_email}', '{cart_link}', '{only_product_name}', '{only_product_sku}', '{product_image}', '{subscriber_name}', '{subscriber_phone}');
		$replace_array = array(strip_tags($product_name), $pid, $product_link, $blogname, $this->email, $this->email, $cart_url, $only_product_name, $only_product_sku, $product_image, $subscriber_name, $subscriber_phone);
		$formatted_content = str_replace($find_array, $replace_array, $content);
		return apply_filters('cwginstock_replace_shortcode', $formatted_content, $this->subscriber_id);
	}

	public function format_html_message() {
		ob_start();
		if (function_exists('wc_get_template')) {
			//wc_get_template('emails/email-header.php', array('email_heading' => $this->get_subject()));
			do_action('woocommerce_email_header', $this->get_subject(), null);
			echo do_shortcode($this->get_message());
			do_action('woocommerce_email_footer', get_option('woocommerce_email_footer_text'));
			//wc_get_template('emails/email-footer.php');
		} else {
			woocommerce_get_template('emails/email-header.php', array('email_heading' => $this->get_subject()));
			echo do_shortcode($this->get_message());
			woocommerce_get_template('emails/email-footer.php');
		}
		return ob_get_clean();
	}

	public function send() {
		$to = $this->email;
		//$from_name = $this->from_name();
		//$from_email = $this->from_email();
		//$headers[] = "From: $from_name<$from_email>";
		//$headers[] = "Content-Type: text/html; charset=UTF-8";
		// Above commented code is not needed as it is already in woocommerce core function

		$mailer = WC()->mailer();
		$sendmail = $mailer->send($to, $this->get_subject(), $this->format_html_message());

		do_action('cwg_instock_after_' . $this->slug . '_mail', $to, $this->subscriber_id);
		if ('subscribe' == $this->slug) {
			do_action('cwg_instock_mail_send_as_copy', $to, $this->get_subject(), $this->format_html_message());
		}
		if ($sendmail) {
			do_action('cwg_' . $this->slug . '_mail_sent_success', $this->subscriber_id);
			return true;
		} else {
			do_action('cwg_' . $this->slug . '_mail_sent_failure', $this->subscriber_id);
			return false;
		}
	}

}
