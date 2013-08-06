<?php

/**
 * XDCC Parser
 * |- Admin Module
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

//set your user and password here
define('ADMIN_USER', "changeme");
define('ADMIN_PASS', "yougonnagethackedifyoudont");

// DO NOT EDIT BELOW!!
if (!($_SERVER['PHP_AUTH_USER'] == ADMIN_USER &&$_SERVER['PHP_AUTH_PW'] == ADMIN_PASS)) {
	header('WWW-Authenticate: Basic realm="XDCC Parser Admin"');
	header('HTTP/1.0 401 Unauthorized');
	die("<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n<html><head>\n<title>403 Forbidden</title>\n</head><body>\n<h1>Forbidden</h1>\n<p>You don't have permission to access ".$_SERVER['REQUEST_URI']." on this server.</p>\n</body></html>\n");
}

require_once 'core.php';
require_once 'smarty/libs/Smarty.class.php';

//initialize smarty
$s = new Smarty();
$s->caching = false;
$s->template_dir = "./tpl";
$s->compile_dir =  "./templates_c";
$botconfig = xp_get("botconfig");
$config = xp_get("config");
$bookmarks = xp_get("bookmarks");

$s->assign("skin", $_REQUEST['skin'] ? $_REQUEST['skin'] : SKIN);
if(IRC) {
$s->assign("irc_chan", IRC_CHANNEL);
$s->assign("irc_net", IRC_NETWORK);
}

switch($_REQUEST['do']) {
	case 'editbot':
		if($botconfig[$_REQUEST['bot']]) {
			$s->assign("edit", $_REQUEST['bot']);
			$s->assign("boturi", $botconfig[$_REQUEST['bot']]);
		}
		$s->display("adminbot.tpl");
		exit();
	case 'editbookmark':
		if($bookmarks[$_REQUEST['bm_id']]) {
			$s->assign("bm", htmlentities($bookmarks[$_REQUEST['bm_id']][0]));
			$s->assign("bmv", htmlentities($bookmarks[$_REQUEST['bm_id']][1]));
			$s->assign("bm_id", $_REQUEST['bm_id']);
		}
		$s->display("adminbookmark.tpl");
		exit();
	case 'editgroup':
		$s->assign("group",$config['group']);
		$s->display("admingroup.tpl");
		exit();
	case 'deletebot':
		if($botconfig[$_REQUEST['bot']]) {
			unset($botconfig[$_REQUEST['bot']]);
			xp_set("botconfig",$botconfig);
			$refresh = 1;
		}
		break;
	case 'commitbot':
		if($_REQUEST['botname'] && $_REQUEST['boturi']) {
			$botconfig[$_REQUEST['botname']] = $_REQUEST['boturi'];
			xp_set("botconfig",$botconfig);
			$refresh = 1;
		}
		break;
	case 'deletebookmark':
		if($bookmarks[$_REQUEST['bm_id']]) {
			unset($bookmarks[$_REQUEST['bm_id']]);
			xp_set("bookmarks",$bookmarks);
		}
		break;
	case 'commitbookmark':
		if($_REQUEST['bmname'] && $_REQUEST['bmval']) {
			if(!$_REQUEST['bm_id']) {
				if(empty($bookmarks))
					$_REQUEST['bm_id'] = 1;
				else
					$_REQUEST['bm_id'] = array_pop(array_keys($bookmarks)) + 1;
			}
			$bookmarks[$_REQUEST['bm_id']] = array( stripslashes($_REQUEST['bmname']), stripslashes($_REQUEST['bmval']) );
			xp_set("bookmarks",$bookmarks);
		}
		break;
	case 'commitgroup':
		$config['group'] = stripslashes($_REQUEST['groupname']);
		xp_set("config",$config);
		$refresh = 1;
		break;
	case 'refresh':
		$refresh = 1;
		break;
}

if($refresh) require_once "refresh.php";
$s->assign("bots",$botconfig);
$s->assign("config",$config);
$s->assign("bookmarks",$bookmarks);
$s->display("adminindex.tpl");

?>
