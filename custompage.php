<?
/**
 * XDCC Parser
 * |- Custom Page Module
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

$pages = new fauxdb('pagedb');
$out = "";

if( ( $page = $pages->select_by_id( 'pages', intval( $_REQUEST['p'] ) ) )&& file_exists( "custompages/" . $page['file'] ) ) {
	$out .= "<script>" . $page['title'] . "</script>";
	$out .= file_get_contents( "custompages/" . $page['file'] );
} else {
	$out .= "<div class=\"content\">Error: 404 Not Found.</div>";
}

print($out);

?>
