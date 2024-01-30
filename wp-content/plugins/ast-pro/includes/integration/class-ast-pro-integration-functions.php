<?php

/*
* AST: get specific string between two string
*/
if ( !function_exists( 'ast_get_string_between' ) ) {
	function ast_get_string_between( $input, $start, $end ) {
		$substr = substr( $input, strlen( $start ) + strpos( $input, $start ), ( strlen( $input ) - strpos( $input, $end ) ) * ( -1 ) );        
		return $substr;
	}
}

/*
* AST: get specific string between two string
*/
if ( !function_exists( 'ast_get_string_between_html' ) ) {
	function ast_get_string_between_html( $string, $start, $end ) {		
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ( 0 == $ini ) {
			return '';
		}	
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}
}

/*
* AST: get specific string after string
*/
if ( !function_exists( 'ast_get_string_after' ) ) {
	function ast_get_string_after( $input, $after ) {
		$substr = substr($input, strpos($input, $after) + strlen($after));
		return $substr;
	}
}

/*
* AST: get specific string before string
*/
if ( !function_exists( 'ast_get_string_before' ) ) {
	function ast_get_string_before( $input, $before ) {
		//$substr = strtok( $input, $before );
		$substr = strstr( $input, $before, true );
		return $substr;
	}
}
