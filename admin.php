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
 * @version 2.0
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

require_once 'smarty/libs/Smarty.class.php';
require_once 'fauxdb.class.php';

//initialize smarty
$s = new Smarty();
$s->caching = false;
$s->template_dir = "./tpl";
$s->compile_dir =  "./templates_c";

//initialize database
$masterdb = new fauxdb("masterdb");
$indexdb = new fauxdb("indexdb");
$pagedb = new fauxdb("pagedb");

$s->assign("skin", $_REQUEST['skin'] ? $_REQUEST['skin'] : SKIN);
if(IRC) {
$s->assign("irc_chan", IRC_CHANNEL);
$s->assign("irc_net", IRC_NETWORK);
}

switch( $_REQUEST['do'] ) {
	case 'editbot':
		if( $bot = $masterdb->select_by_id( 'botconfig', $_REQUEST['bot'] ) ) {
			$s->assign( "bot_id", $_REQUEST['bot'] );
			$s->assign( "bot_nick", htmlentities( $bot['nick'] ) );
			$s->assign( "bot_uri", htmlentities( $bot['uri'] ) );
		}
		$s->display("adminbot.tpl");
		exit();
	case 'editbookmark':
		if( $bookmark = $indexdb->select_by_id( 'bookmarks', $_REQUEST['bm_id'] ) ) {
			$s->assign( "bm_id", $_REQUEST['bm_id'] );
			$s->assign( "bm", htmlentities( $bookmark[0] ) );
			$s->assign( "bmv", htmlentities( $bookmark[1] ) );
		}
		$s->display("adminbookmark.tpl");
		exit();
	case 'editpage':
		if( $page = $pagedb->select_by_id( 'pages', $_REQUEST['page_id'] ) ) {
			$s->assign( "page_id", $_REQUEST['page_id'] );
			$s->assign( "page_title", htmlentities( $page['title'] ) );
			$s->assign( "page_url", htmlentities( $page['file'] ) );
		}
		$s->display("admincustompage.tpl");
		exit();		
	case 'editgroup':
		$s->assign( "group", $masterdb->data['config']['group'] );
		$s->display("admingroup.tpl");
		exit();
	case 'deletebot':
		$masterdb->delete( 'botconfig', $_REQUEST['bot'] );
		$masterdb->commit();
		$indexdb->delete( 'botlist', $_REQUEST['bot'] );
		$indexdb->commit();
		$botdb = xp_cache::get("botdb");
		unset($botdb[$_REQUEST['bot']]);
		xp_cache::set("botdb",$botdb);
		$refresh = 1;
		break;
	case 'commitbot':
		$masterdb->update( 'botconfig', $_REQUEST['bot_id'], array( 'nick' => $_REQUEST['bot_nick'], 'uri' => $_REQUEST['bot_uri'] ) );
		$masterdb->commit();
		$indexdb->update( 'botlist', $_REQUEST['bot_id'], array( 'nick' => $_REQUEST['bot_nick'] ) );
		$indexdb->commit();
		$refresh = 1;
		break;
	case 'deletebookmark':
		$indexdb->delete( 'bookmarks', $_REQUEST['bm_id'] );
		$indexdb->commit();
		break;
	case 'commitbookmark':
		if($_REQUEST['bmname'] && $_REQUEST['bmval']) {
			$indexdb->update( 'bookmarks', $_REQUEST['bm_id'], array( $_REQUEST['bmname'], $_REQUEST['bmval'] ) );
			$indexdb->commit();
		}
		break;
	case 'deletepage':
		$indexdb->delete( 'pages', $_REQUEST['page_id'] );
		$indexdb->commit();
		$pagedb->delete( 'pages', $_REQUEST['page_id'] );
		$pagedb->commit();
		break;
	case 'commitpage':
		if($_REQUEST['page_title'] && $_REQUEST['page_url']) {
			$indexdb->update( 'pages', $_REQUEST['page_id'], array( 'title' => $_REQUEST['page_title'] ) );
			$indexdb->commit();
			$pagedb->update( 'pages', $_REQUEST['page_id'], array( 'title' => $_REQUEST['page_title'], 'file' => $_REQUEST['page_url'] ) );
			$pagedb->commit();
		}
		break;
	case 'commitgroup':
		$masterdb->update( 'config', 'group', $_REQUEST['groupname'] );
		$masterdb->commit();
		$refresh = 1;
		break;
	case 'refresh':
		$refresh = 1;
		break;
}

if($refresh) {
	@file_get_contents(URL.'refresh.php',0,stream_context_create(array('http' => array('timeout' => 0))));
}
$s->assign("bots",$masterdb->data['botconfig']);
$s->assign("bookmarks",$indexdb->data['bookmarks']);
$s->assign("pages",$pagedb->data['pages']);
$s->assign("config",$masterdb->data['config']);
$s->display("adminindex.tpl");

?>
