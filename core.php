<?php

/**
 * XDCC Parser
 * |- Core Functions
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

define('SKIN', 5); //default skin. comes with 6 skins, set to a number 1-6.
// 1 - dark 3 - dark-expanded 2 - light pink 4 - light pink-expanded 5 - rain 6 - rain-expanded
define('DISPLAY_SC', 1); //whether or not to show the skin changer. 0 = off, 1 = on.
define('XCACHE_PREFIX', "xp2_");
define('UPDATE_FREQ', 3600); //update frequency in seconds (3600 = 1 hour)
define('IRC', 0); // if you set this to 1 make sure you set the channel and network too
define('IRC_CHANNEL', 'XDCCParser'); //don't include the #
define('IRC_NETWORK', 'irc.rizon.net'); //the network
define('URL', ''); // optional: your url goes here. example: http://youpacklist.com/
// trailing slash required. if left blank, the script will figure out the url.

/* ############################################# */
/* #             DO NOT EDIT BELOW             # */
/* ############################################# */

define('XCACHE_ENABLED', function_exists("xcache_get"));
ob_start('ob_gzhandler');
error_reporting(1);

function xp_get($var) {
	if(XCACHE_ENABLED && xcache_isset(XCACHE_PREFIX.$var))
		return xcache_get(XCACHE_PREFIX.$var);
	$data = unserialize(file_get_contents("./cache/".XCACHE_PREFIX.$var));
	xp_set($var,$data,1);
	return $data;
}

function xp_set($var,$data,$xonly=0) {
	if(XCACHE_ENABLED)
		xcache_set(XCACHE_PREFIX.$var,$data);
	if(!$xonly)
		file_put_contents("./cache/".XCACHE_PREFIX.$var,serialize($data));
}

function xp_unset($var) {
	if(XCACHE_ENABLED)
		xcache_unset(XCACHE_PREFIX.$var);
	unlink("./cache/".XCACHE_PREFIX.$var);
}

?>
