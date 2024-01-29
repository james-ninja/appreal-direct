<?php

namespace Barn2\Plugin\WC_Bulk_Variations;

/**
 * Table_Factory class.
 *
 * @package   Barn2\woocommerce-bulk-variations
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table_Factory {

	private static $tables     = [];
	private static $current_id = 1;

	/**
	 * Create a new table based on the supplied args.
	 *
	 * @param array $args The args to use for the table.
	 * @return Table The product table object.
	 */
	public static function create( $args ) {

		// Merge in the default args, so our table ID reflects the full list of args, including settings page.
		$id = self::generate_id( $args );

		$table               = new Table( $id, $args );
		self::$tables[ $id ] = $table;

		return $table;
	}

	/**
	 * Fetch an existing table by ID.
	 *
	 * @param string $id The product table ID.
	 * @return Table The product varriations table object.
	 */
	public static function fetch( $id ) {

		if ( empty( $id ) ) {
			return false;
		}

		$table = false;

		if ( isset( self::$tables[ $id ] ) ) {
			$table = self::$tables[ $id ];
		}

		return $table;
	}

	private static function generate_id( $args ) {
		$id = 'wbv_' . substr( md5( serialize( $args ) ), 0, 16 ) . '_' . self::$current_id; //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
		self::$current_id ++;

		return $id;
	}
}
