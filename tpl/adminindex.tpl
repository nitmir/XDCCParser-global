{include file="adminheader.tpl"}
		<h2>Bots</h2>
		<ul class="forwards">
{if $bots}{foreach from=$bots key=nick item=bot}
			<li>{$nick} ( <a href="?do=editbot&bot={$nick}">edit</a> | <a href="?do=deletebot&bot={$nick}" onclick="return confirm('Are you sure you want to delete {$nick}?');">delete</a> )</li>
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
		<h2>Admin</h2>
		<ul class="forwards">
			<li>Group Filter - {if $config.group}{$config.group}{else}Not Set{/if} ( <a href="?do=editgroup">edit</a> | <a href="?do=commitgroup">remove</a> )</li>
			<li><a href="?do=refresh">refresh bots</a></li>
		</ul>
{include file="adminfooter.tpl"}
