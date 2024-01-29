<?php

class CWG_Instock_Subscription extends CWG_Instock_Mailer {

	public function __construct( $subscriber_id) {
		parent::__construct();
		$this->slug = 'subscribe';
		$this->subscriber_id = $subscriber_id;
		$this->email = get_post_meta($subscriber_id, 'cwginstock_subscriber_email', true);
		do_action('cwg_instock_before_' . $this->slug . '_mail', $this->email, $this->subscriber_id);
		$option = get_option('cwginstocksettings');
		$this->get_subject = apply_filters('cwgsubscribe_raw_subject', $option['success_sub_subject'], $subscriber_id);
		$this->get_message = apply_filters('cwgsubscribe_raw_message', nl2br($option['success_sub_message']), $subscriber_id);
	}

}
