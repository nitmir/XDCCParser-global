<?php

/**
 * XDCC Parser
 * |- Faux Database Library - the poor man's SQLite
 *
 * This software is free software and you are permitted to
 * modify and redistribute it under the terms of the GNU General
 * Public License version 3 as published by the Free Sofware
 * Foundation.
 *
 * @link http://xdccparser.is-fabulo.us/
 * @version 2.0
 * @author Alex 'xshadowfire' Yu <ayu@xshadowfire.net>
 * @author DrX
 * @copyright 2008-2009 Alex Yu and DrX
 */

class fauxdb {
	
	var $name = "";
	var $data = null;

	function __construct( $dbname = '' ) {
		require_once 'define.php'; // make sure our variables are set
		require_once 'xp_cache.class.php'; // aaaand cache functions
		$this->name = $dbname;
		$this->data = xp_cache::get( $dbname ) or array(); // fetch or create new if not exist
	}

	function commit() {
		xp_cache::set( $this->name, $this->data );
		return TRUE;
	}

	function create_if_not_exist( $table = '' ) {
		if( is_array( $this->data[$table] ) )
			return FALSE;
		$this->data[$table] = array();
		return TRUE;
	}

	function select( $table = '', $field = '', $value = '' ) {
		if( !( is_array( $this->data[$table] ) && $field && $value ) )
			return FALSE;
		foreach( $this->data[$table] as $row ) {
			if( $row[$field] == $value )
				return $row;
		}
	}
	function select_by_id( $table = '', $id = 0 ) {
		if( !( $table && is_array( $this->data[$table] ) ) )
			return FALSE;
		return $this->data[$table][$id];
	}

	function update( $table = '', $id = 0, $values = array() ) {
		if( !$table  || empty( $values ) )
			return FALSE;
		if( !$id ) {
			if( empty( $this->data[$table] ) ) {
				$this->create_if_not_exist( $table );
				$id = 1;
			} else {
				$id = array_pop( array_keys( $this->data[$table] ) ) + 1;
			}
		}
		if( get_magic_quotes_gpc() ) {
			foreach( $values as &$value )
				$value = stripslashes( $value );
		}
		$this->data[$table][$id] = $values;
		return TRUE;
	}

	function delete( $table = '', $id = 0 ) {
		if( !( $table && is_array( $this->data[$table] ) && $id ) )
			return FALSE;
		unset( $this->data[$table][$id] );
		return TRUE;
	}

}

?>
