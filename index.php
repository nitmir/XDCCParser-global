<?php

/**
 * XDCC Parser
 * |- Index
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
require_once 'smarty/libs/Smarty.class.php';

//initialize smarty
$s = new Smarty();
$s->caching = false;
$s->template_dir = "./tpl";
$s->compile_dir =  "./templates_c";

//figure out url, if needed.
if(!URL) {
	$uri = explode("/",$_SERVER['REQUEST_URI']);
	array_pop($uri);
	define('_URL', "http://".$_SERVER['SERVER_NAME'].implode("/",$uri)."/");
} else {
	define('_URL', URL);
}

//assign our vars
$s->assign("url", _URL);
$s->assign("skin", $_REQUEST['skin'] ? $_REQUEST['skin'] : SKIN);
$s->assign("display_sc", DISPLAY_SC);
$s->assign("bots", xp_get("bots"));
$s->assign("bookmarks", xp_get("bookmarks"));
$_GET['search'] ? $s->assign("search", htmlentities(stripslashes($_GET['search']))) : null;
$_GET['nick'] ? $s->assign("nick", $_GET['nick']) : null;
if(IRC) {
$s->assign("irc_chan", IRC_CHANNEL);
$s->assign("irc_net", IRC_NETWORK);
}

$s->display("packlist.tpl");

//how old is our cache?
if(time() > xp_get("time")+UPDATE_FREQ)
	file_get_contents(_URL.'refresh.php',0,stream_context_create(array('http' => array('timeout' => 0))));

?>
