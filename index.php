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
 * @version 2.0
 * @author Alex 'xshadowfire' Yu <ayu@xshadowfire.net>
 * @author DrX
 * @copyright 2008-2009 Alex Yu and DrX
 */

require_once 'fauxdb.class.php';
require_once 'smarty/libs/Smarty.class.php';

//initialize smarty
$s = new Smarty();
$s->caching = false;
$s->template_dir = "./tpl";
$s->compile_dir =  "./templates_c";

//assign our vars
$db = new fauxdb("indexdb");
$s->assign("url", URL);
$s->assign("skin", $_REQUEST['skin'] ? $_REQUEST['skin'] : SKIN);
$s->assign("bots", $db->data['botlist']);
$s->assign("bookmarks", $db->data['bookmarks']);
$s->assign("pages", $db->data['pages']);
$_GET['search'] ? $s->assign("search", stripslashes($_GET['search'])): null;
$_GET['bot'] ? $s->assign("botid", $_GET['bot']) : null;
$_GET['page'] ? $s->assign("page", $_GET['page']) : null;
if(IRC) {
$s->assign("irc_chan", IRC_CHANNEL);
$s->assign("irc_net", IRC_NETWORK);
}

$s->display("packlist.tpl");

//how old is our cache?
if(time() > filemtime("./cache/last_refresh")+UPDATE_FREQ)
	@file_get_contents(URL.'refresh.php',0,stream_context_create(array('http' => array('timeout' => 0))));

?>
