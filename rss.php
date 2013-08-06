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

//figure out url, if needed.
if(!URL) {
	$uri = explode("/",$_SERVER['REQUEST_URI']);
	array_pop($uri);
	define('_URL', "http://".$_SERVER['SERVER_NAME'].implode("/",$uri)."/");
} else {
	define('_URL', URL);
}


//how old is our cache?
if(time() > xp_get("time")+UPDATE_FREQ)
	file_get_contents(_URL.'refresh.php',0,stream_context_create(array('http' => array('timeout' => 0))));

$bots=xp_get("bots");
if(isset($_GET['nick'])){
header('Content-type: application/rss+xml');
for($i=0;isset($bots[$i]);$i++){
if($bots[$i]['nick']==$_GET['nick']){
echo '<?xml version="1.0" encoding="utf-8" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
		<channel>
		<title>Xdcc Mooo !</title>
		<atom:link href="'.URL.'rss.php?nick='.rawurlencode($bots[$i]['nick']).'" rel="self" type="application/rss+xml"/>
		<link>'.URL.'</link>
		<description></description>
		<language>en</language>';
	for($j=0;isset($bots[$i]['packs'][1][$j]);$j++){
echo '		<item>
		<title>'.$bots[$i]['packs'][5][$j].'</title>'."\n";
		echo '<link>'.URL.'?nick='.rawurlencode($bots[$i]['nick']).'</link>'."\n";
		echo '<guid>urn:uuid:'.md5(
	$bots[$i]['packs'][1][$j].
	$bots[$i]['packs'][2][$j].
	$bots[$i]['packs'][3][$j].
	$bots[$i]['packs'][4][$j].
	$bots[$i]['packs'][5][$j].
	$bots[$i]['nick']
		).'</guid>
		<description>/msg '.$bots[$i]['nick'].' XDCC SEND '.$bots[$i]['packs'][1][$j].'</description>
		</item>
';
}
	}
}
?>
		</channel>
		</rss>
<?php
}else{
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Mooo XDCC RSS</title>
</head><body><ul>';
for($i=0;isset($bots[$i]);$i++){
echo '<li><a href="?nick='.$bots[$i]['nick'].'">'.$bots[$i]['nick'].'</a></li>';
}
echo '</ul></body></html>';
}

?>
