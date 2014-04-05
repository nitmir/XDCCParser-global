{include file="adminheader.tpl"}
		<h2>Bots</h2>
		<ul class="forwards">
{if $bots}{foreach from=$bots key=bot_id item=bot}
			<li>{$bot.nick} ( <a href="?do=editbot&bot={$bot_id}">edit</a> | <a href="?do=deletebot&bot={$bot_id}" onclick="return confirm('Are you sure you want to delete {$bot.nick}?');">delete</a> )</li>
{/foreach}
{else}
			<li>None</li>
{/if}
			<li><a href="?do=editbot">add new bot</a></li>
		</ul>
		<h2>Bookmarks</h2>
		<ul class="forwards">
{if $bookmarks}{foreach from=$bookmarks key=bm_id item=bookmark}
			<li>{$bookmark.0} ( <a href="?do=editbookmark&bm_id={$bm_id}">edit</a> | <a href="?do=deletebookmark&bm_id={$bm_id}" onclick="return confirm('Are you sure you want to delete the bookmark \'{$bookmark.0}\' ?');">delete</a> )</li>
{/foreach}
{else}
			<li>None</li>
{/if}
			<li><a href="?do=editbookmark">add new bookmark</a></li>
		</ul>
		<h2>Custom Pages</h2>
		<ul class="forwards">
{if $pages}{foreach from=$pages key=page_id item=page}
			<li>{$page.title} ( <a href="?do=editpage&page_id={$page_id}">edit</a> | <a href="?do=deletepage&page_id={$page_id}" onclick="return confirm('Are you sure you want to delete the page \'{$page.title} ?');">delete</a> )</li>{/foreach}
{else}
			<li>None</li>
{/if}
			<li><a href="?do=editpage">add new custom page</a></li>
		</ul>
		<h2>Admin</h2>
		<ul class="forwards">
			<li>Group Filter - {if $config.group}{$config.group}{else}Not Set{/if} ( <a href="?do=editgroup">edit</a> | <a href="?do=commitgroup">remove</a> )</li>
			<li><a href="?do=refresh">refresh bots</a></li>
		</ul>
{include file="adminfooter.tpl"}
