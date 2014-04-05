<?php

/**
 * XDCC Parser
 * |- Variable Definitions
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

// this block to be moved to db
define('SKIN', 5); //default skin. comes with 6 skins, set to a number 1-6.
// 1 - dark 3 - dark-expanded 2 - light pink 4 - light pink-expanded 5 - rain 6 - rain-expanded
define('XCACHE_PREFIX', "xp2_");
define('UPDATE_FREQ', 3600); //update frequency in seconds (3600 = 1 hour)
define('IRC', 0); // if you set this to 1 make sure you set the channel and network too
define('IRC_CHANNEL', 'XDCCParser'); //don't include the #
define('IRC_NETWORK', 'irc.rizon.net'); //the network

define('URL', 'http://xdccparser.is-fabulo.us/global/');
define('XCACHE_ENABLED', function_exists("xcache_isset") ? 1 : 0); //set statiic with installer
define('CURL_ENABLED', function_exists("curl_init") ? 1 : 0); //set static with installer
define('LOG_LEVEL', 1);
ob_start('ob_gzhandler');

?>
