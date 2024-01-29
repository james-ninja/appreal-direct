<?php
class WCUSAEPAY_Ajax_Call {

    public static function remove_card_token($params)
    {
        
        if ( is_user_logged_in() ) {
        
            $card_index = base64_decode($params['card_index']);
            $card_token = base64_decode($params['card_token']);

            $customer_id = get_current_user_id();
            

            if( $customer_id ){
				
                $tokens = get_option( 'woocommerce_usaepay_user_'.$customer_id );
                unset($tokens[$card_index]);
                update_option( 'woocommerce_usaepay_user_'.$customer_id, $tokens );

			}
            

            $response[ 'message' ]    = '';
            $response[ 'code' ] 	   = true;
            $response[ 'data' ] 	   = '';

        } else {
            $response[ 'message' ]    = '';
            $response[ 'code' ] 	   = false;
            $response[ 'data' ] 	   = '';

        }
        


         
         return $response;
    }
}