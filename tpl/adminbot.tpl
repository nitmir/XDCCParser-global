{include file="adminheader.tpl"}
<div style="padding:20px;">
<form action="admin.php" method="post">
Nick: <input type="text" name="bot_nick" value="{$bot_nick}" class="search" style="width:150px;"/><br /><br />
Access URI: <input type="text" name="bot_uri" value="{$bot_uri}" class="search" style="width:250px;"/><br /><br />
{if $bot_nick}<input type="hidden" name="bot_id" value="{$bot_id}" />{/if}
<input type="hidden" name="do" value="commitbot" />
<input type="submit" value="Submit" class="search"/>
</form>
</div>
{include file="adminfooter.tpl"}
