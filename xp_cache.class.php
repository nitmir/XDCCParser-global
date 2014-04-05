<?php

/**
 * XDCC Parser
 * |- Cache Functions
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

class xp_cache {

	static $loghandle;

	static function get( $var ) {
		if( XCACHE_ENABLED && xcache_isset( XCACHE_PREFIX . $var ) )
			return xcache_get( XCACHE_PREFIX . $var );
		if( file_exists( "./cache/" . $var ) ) {
			$data = unserialize( file_get_contents( "./cache/" . $var ) );
			self::set( $var, $data, 1 );
			return $data;
		} else {
			return FALSE;
		}
	}

	static function set( $var, $data, $xonly = 0 ) {
		if( XCACHE_ENABLED )
			xcache_set( XCACHE_PREFIX . $var, $data );
		if( !$xonly )
			file_put_contents( "./cache/" . $var, serialize( $data ) );
	}

	static function log( $msg = '', $level = 1 ) {
		$msg .= "\n";
		if( $level <= LOG_LEVEL ) {
			if( !self::$loghandle )
				self::$loghandle = fopen("./cache/log","a");
			fwrite(self::$loghandle,time().": ".$msg);
		}
		print( nl2br( $msg ) );
	}

}

?>
