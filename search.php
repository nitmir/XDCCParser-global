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
 * @version 1.2.0
 * @author Alex 'xshadowfire' Yu <ayu@xshadowfire.net>
 * @author DrX
 * @copyright 2008-2009 Alex Yu and DrX
 */

require_once 'core.php';
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

$x = 0;
$bots = xp_get("bots");

$t = literalSearch( str_replace( '-"', '"-', stripslashes($_GET['t']) ) ); // dirty dirty dirty hack - flip -" so I don't have to properly parse it :D
$b = array(); // our blacklist

if($_GET['nick']) foreach($bots as $key => &$bot) if($bot['nick'] != $_GET['nick']) unset($bots[$key]); // dirty hack - get rid of all bots that aren't the right one!

foreach($t as $key => $arg) {
	if(!$arg) { // why are you empty?
		unset($t[$key]);
	} elseif($arg[0] == '-') { // let's blacklist some terms
		$b[] = substr($arg,1);
		unset($t[$key]);
	}
}

$match = preg_match("/.*?[a-f0-9]{7}.*?/i",$_GET['t']) ? 5 : 4; // crc or non crc search, 7 or more is a go

foreach($bots as &$bot) {
	$xpacks = array();
	$key = count($bot['packs']['1']);
	for($i=0;$i<$key;$i++) {
		foreach($t as $arg) {
			if(!stristr($bot['packs'][$match][$i],$arg)) {
				continue 2;
			}
		}
		foreach($b as $arg) {
			if(stristr($bot['packs'][$match][$i],$arg) !== FALSE) {
				continue 2;
			}
		}
		$xpacks[$bot['packs'][1][$i]]['number'] = $bot['packs'][1][$i];
		$xpacks[$bot['packs'][1][$i]]['name'] = $bot['packs'][5][$i];
		$xpacks[$bot['packs'][1][$i]]['size'] = $bot['packs'][2][$i];
	}

	foreach($xpacks as $pack)
		print("p.k[".$x++."] = {b:\"".$bot['nick']."\", n:".$pack['number'].", s:".$pack['size'].", f:\"".$pack['name']."\"};\n");

}
?>
