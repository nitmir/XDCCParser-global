<?php

/**
 * XDCC Parser
 * |- Search Module
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

if(!$_GET['t'] && !$_GET['n']) die(); // we need something to search aye?

$start = microtime( true );

require_once 'define.php';
require_once 'xp_cache.class.php';
error_reporting(1);
header( "Expires: Mon, 20 Dec 1998 01:00:00 GMT" );
header( "Cache-Control: no-cache, must-revalidate" );
header( "Pragma: no-cache" );
header( "Content-Type: text/plain; charset=iso-8859-1" );

function literalSearch($search) {
	$t = array();
	if(($index = strpos($search,'"'))!== false && ($lastindex = strpos(substr($search,$index+1),'"'))!== false) {
		$t[] = addslashes(substr($search,$index+1,$lastindex));
		$t = array_merge($t,simpleSearch(substr($search,0,$index)));
		return array_merge($t,literalSearch(substr($search,$index+$lastindex+2)));
	} else {
		return simpleSearch($search);
	}
}
function simpleSearch($search) {
	$search = str_replace(array("_", ";", "'", ".")," ",$search);
	return explode(" ",$search);
}

$bots = xp_cache::get("botdb");

$t = literalSearch( str_replace( '-"', '"-', stripslashes($_GET['t']) ) ); // dirty dirty dirty hack - flip -" so I don't have to properly parse it :D
$blacklist = array(); // our blacklist

// dirty dirty hack
if($_GET['n']) {
$bot = $bots[$_GET['n']];
unset($bots);
$bots[$_GET['n']] = $bot;
}

foreach($t as $key => $arg) {
	if(!$arg) { // why are you empty?
		unset($t[$key]);
	} elseif($arg[0] == '-') { // let's blacklist some terms
		$blacklist[] = substr($arg,1);
		unset($t[$key]);
	}
}

$match = preg_match("/.*?[a-f0-9]{7}.*?/i",$_GET['t']) ? 3 : 4; // crc or non crc search, 7 or more is a go

$buffer = array();
echo "[";

foreach($bots as $id => &$bot) {
	$found = array();
	$p = 0;
	foreach( $bot[0] as $key => $pack ) {
		foreach($t as $arg) {
			if(!stristr($bot[$match][$key],$arg)) {
				continue 2;
			}
		}
		foreach($blacklist as $arg) {
			if(stristr($bot[$match][$key],$arg) !== FALSE) {
				continue 2;
			}
		}
		$found[] = $key;
	}

	foreach( $found as $key ) {
		$ago = time()-$bot[2][$key];
		if( $ago < 60 ) {
			$ago = $ago . "s ago";
		} else if( $ago < 3600 ) {
			$ago = floor($ago / 60) . "m ago";
		} else if( $ago < 86400 ) {
			$ago = floor( $ago / 3600 ) . "h " .floor( ( $ago % 3600 ) / 60 ) . "m ago";
		} else if( $ago < 31557600 ) {
			$ago = floor( $ago / 86400 ) . "d " . floor( ( $ago % 86400 ) / 3600 ). "h ago";
		} else {
			$ago = floor( $ago /  31557600) . "y " . floor( ( $ago % 31557600 ) / 86400 ). "d ago";
		}
		echo '['.$id.','.$bot[0][$key].','.$bot[1][$key].',"'.$bot[3][$key].'","'.$ago.'",'.$bot[2][$key].'],';
	}

}

echo (microtime( true ) - $start) . "]";

?>
