<?php
/**
  * Ajax Server Class
  * @version since 1.0.0
  * @author ORAGON
  */ 
class WCUSAEPAY_Ajax_Server 
{
    public static function ajax_local()
    {
        wp_register_script( 'wcusaepay_ajax_script', plugins_url( 'assets/js/script.js' , __FILE__ ) );	
        wp_localize_script( 'wcusaepay_ajax_script', 'wcusaepay_ajax_service', array(
		    'ajax_url' => admin_url( 'admin-ajax.php' ),
            'site_url' => get_option('siteurl')
		) );

		wp_enqueue_script( 'wcusaepay_ajax_script' );
        //wp_enqueue_script( 'jquery');
	}

	public static function ajax_call()
    {

        $method = sanitize_text_field( $_REQUEST[ 'method' ] );
        $func = sanitize_text_field( $_REQUEST[ 'func' ] );
        $data	= $_REQUEST[ 'data' ] ;

        if( method_exists( $method , $func ) ){
            $return = call_user_func_array( "$method::$func" , array( $data ) );

            $response_code 		= ( $return[ 'code' ] == true ? true : false );
            $response_message 	= esc_html__( $return[ 'message' ] , 'woocommerce' );
            $response_result 	= ( ! empty( $return[ 'data' ] ) ? $return[ 'data' ] : '' );

        } else {
            $response_code 		=  false;
            $response_message 	= esc_html__( 'Invalid method Name!', 'woocommerce' );
            $response_result 	= '';
        }

        header( "Content-Type: application/json" );
        echo json_encode( array(
            'success' => $response_code,
            'response_message' => $response_message,
            'data' => $response_result,
            'time' => time()
        ) );
    exit;
	}


}