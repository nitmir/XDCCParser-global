<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
	<title>Packlist</title>
	<link rel="stylesheet" href="{$url}style{$skin}.css" type="text/css" id="skin" />
	<!--[if lte IE 6]>
	<link rel="stylesheet" href="{$url}style-ie.css" type="text/css" />
	<![endif]-->
	<script type="text/javascript" src="{$url}packlist.js"></script>
</head>
<body onload="p.init('{$url}');{if $nick}p.nickPacks('{$nick}');{/if}">
<div class="botlist">
<div id="botlist" style="padding:10px;">
{if $display_sc}<h3>Skins</h3>
<a href="javascript:p.setSkin(1);">dark</a><br />
<a href="javascript:p.setSkin(3);">dark-expanded</a><br />
<a href="javascript:p.setSkin(2);">light pink</a><br />
<a href="javascript:p.setSkin(4);">light pink-expanded</a><br />
<a href="javascript:p.setSkin(5);">rain</a><br />
<a href="javascript:p.setSkin(6);">rain-expanded</a><br /><br />{/if}
<h3>Bots</h3>
{foreach from=$bots item=bot}
<a href="javascript:p.nickPacks('{$bot.nick|replace:'\\':'\\\\'}');">{$bot.nick}</a><br />
{/foreach}
{if $bookmarks}
<br />
<h3>Bookmarks</h3>
{foreach from=$bookmarks item=bookmark}
<a href="javascript:document.getElementById('search').value='{$bookmark.1|escape:"quotes"|replace:'"':'&quot;'}';p.search();">{$bookmark.0}</a><br />
{/foreach}{/if}
</div>
</div>
<div class="mainWrapper">
<div class="smallWrapper">
	<div class="header" id="header">
		<h1 class="name">Packlist</h1>
		{if $irc_chan}<h2 class="irc"><a href="irc://{$irc_net}/{$irc_chan}">#{$irc_chan}@{$irc_net}</a></h2>{/if}
	</div>
	<div class="content">
		<div class="contentPad">
			<div id="searchdiv">
				<form action="#" onsubmit="p.search();return false;">Search:&nbsp;&nbsp;<input type="text" name="search" id="search" class="search" style="width:220px;" {if $search}value="{$search|escape:'html'}" {/if}/>&nbsp;&nbsp;<input type="submit" class="search" value="search" style="width:40px;" />&nbsp;&nbsp;<span class="default">(<a href="#" onclick="p.getLastURI();">permalink</a>)</span></form>
			</div>
		</div>
	<h2 id="listname">&nbsp;</h2>
	</div>
	<div id="listtable">
		<h1>Javascript is required for this site.</h1>
	</div>
	<div class="content" align="center"><a href="javascript:p.goTop();">&#8593;&#8593;</a></div>
	<div class="footer"><span class="default">Powered by </span><a href="http://xdccparser.is-fabulo.us/"><span class="default">XDCC Parser v1.2beta</span></a></div>
	<div id="status"><p><span class="loading">Searching...</span></p></div>
</div>
</div>
</body>
</html>

