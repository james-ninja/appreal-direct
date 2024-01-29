<?php

/**
*  USAepay Addons Filters
*/

class WC_USAePAY_Addons
{
	
	function __construct()
	{
		add_filter( 'usaepay_get_currency_code', array( $this, 'get_currency_code' ), 10, 1 );
		add_action( 'woocommerce_order_actions', array( $this, 'add_order_meta_box_action' ), 10, 1 );
	}

	public function get_currency_code( $currency_code ){

		// Reference https://wiki.usaepay.com/developer/currencycode
		$currencies = array(
				'AFA' => '971', // Afghan Afghani 
				'AWG' => '533', // Aruban Florin
				'AUD' => '036', // Australian Dollars 
				'ARS' => '032', // Argentine Peso 
				'AZN' => '944', // Azerbaijanian Manat
				'BSD' => '044', // Bahamian Dollar
				'BDT' => '050', // Bangladeshi Taka 
				'BBD' => '052', // Barbados Dollar 
				'BYR' => '974', // Belarussian Rouble 
				'BOB' => '068', // Bolivian Boliviano
				'BRL' => '986', // Brazilian Real
				'GBP' => '826', // British Pounds Sterling
				'BGN' => '975', // Bulgarian Lev
				'KHR' => '116', // Cambodia Riel
				'CAD' => '124', // Canadian Dollars
				'KYD' => '136', // Cayman Islands Dollar
				'CLP' => '152', // Chilean Peso
				'CNY' => '156', // Chinese Renminbi Yuan 
				'COP' => '170', // Colombian Peso
				'CRC' => '188', // Costa Rican Colon
				'HRK' => '191', // Croatia Kuna
				'CPY' => '196', // Cypriot Pounds 
				'CZK' => '203', // Czech Koruna
				'DKK' => '208', // Danish Krone
				'DOP' => '214', // Dominican Republic Peso
				'XCD' => '951', // East Caribbean Dollar
				'EGP' => '818', // Egyptian Pound
				'ERN' => '232', // Eritrean Nakfa
				'EEK' => '233', // Estonia Kroon 
				'EUR' => '978', // Euro 
				'GEL' => '981', // Georgian Lari
				'GHC' => '288', // Ghana Cedi
				'GIP' => '292', // Gibraltar Pound
				'GTQ' => '320', // Guatemala Quetzal 
				'HNL' => '340', // Honduras Lempira
				'HKD' => '344', // Hong Kong Dollars 
				'HUF' => '348', // Hungary Forint
				'ISK' => '352', // Icelandic Krona
				'INR' => '356', // Indian Rupee 
				'IDR' => '360', // Indonesia Rupiah 
				'ILS' => '376', // Israel Shekel 
				'JMD' => '388', // Jamaican Dollar
				'JPY' => '392', // Japanese yen 
				'KZT' => '368', // Kazakhstan Tenge 
				'KES' => '404', // Kenyan Shilling 
				'KWD' => '414', // Kuwaiti Dinar 
				'LVL' => '428', // Latvia Lat 
				'LBP' => '422', // Lebanese Pound 
				'LTL' => '440', // Lithuania Litas
				'MOP' => '446', // Macau Pataca
				'MKD' => '807', // Macedonian Denar
				'MGA' => '969', // Malagascy Ariary
				'MYR' => '458', // Malaysian Ringgit
				'MTL' => '470', // Maltese Lira 
				'BAM' => '977', // Marka 
				'MUR' => '480', // Mauritius Rupee
				'MXN' => '484', // Mexican Pesos
				'MZM' => '508', // Mozambique Metical
				'NPR' => '524', // Nepalese Rupee
				'ANG' => '532', // Netherlands Antilles Guilder
				'TWD' => '901', // New Taiwanese Dollars
				'NZD' => '554', // New Zealand Dollars
				'NIO' => '558', // Nicaragua Cordoba
				'NGN' => '566', // Nigeria Naira
				'KPW' => '408', // North Korean Won
				'NOK' => '578', // Norwegian Krone
				'OMR' => '512', // Omani Riyal
				'PKR' => '586', // Pakistani Rupee 
				'PYG' => '600', // Paraguay Guarani
				'PEN' => '604', // Peru New Sol
				'PHP' => '608', // Philippine Pesos
				'QAR' => '634', // Qatari Riyal
				'RON' => '946', // Romanian New Leu
				'RUB' => '643', // Russian Federation Ruble
				'SAR' => '682', // Saudi Riyal
				'CSD' => '891', // Serbian Dinar
				'SCR' => '690', // Seychelles Rupee
				'SGD' => '702', // Singapore Dollars
				'SKK' => '703', // Slovak Koruna
				'SIT' => '705', // Slovenia Tolar
				'ZAR' => '710', // South African Rand
				'KRW' => '410', // South Korean Won
				'LKR' => '144', // Sri Lankan Rupee
				'SRD' => '968', // Surinam Dollar
				'SEK' => '752', // Swedish Krona
				'CHF' => '756', // Swiss Francs
				'TZS' => '834', // Tanzanian Shilling
				'THB' => '764', // Thai Baht
				'TTD' => '780', // Trinidad and Tobago Dollar
				'TRY' => '949', // Turkish New Lira
				'AED' => '784', // UAE Dirham
				'USD' => '840', // US Dollars
				'UGX' => '800', // Ugandian Shilling
				'UAH' => '980', // Ukraine Hryvna
				'UYU' => '858', // Uruguayan Peso
				'UZS' => '860', // Uzbekistani Som
				'VEB' => '862', // Venezuela Bolivar
				'VND' => '704', // Vietnam Dong
				'AMK' => '894', // Zambian Kwacha
				'ZWD' => '716', // Zimbabwe Dollar

			);

		if( array_key_exists( $currency_code , $currencies) ){
			$code = $currencies[$currency_code];
		} else {
			$code = '';
		}

		return $code;
	}

	public function add_order_meta_box_action( $actions ){
		
	    $actions['wc_usaepay_capture_authonly'] = esc_html__( 'USAePay Capture an AuthOnly Transaction', 'woocommerce' );
	    return $actions;
	    
	}

}

new WC_USAePAY_Addons();