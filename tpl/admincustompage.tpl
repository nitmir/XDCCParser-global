{include file="adminheader.tpl"}
<div style="padding:20px;">
<form action="admin.php" method="post">
Title: <input type="text" name="page_title" value="{$page_title}" class="search" style="width:150px;"/><br /><br />
URL: <input type="text" name="page_url" value="{$page_url}" class="search" style="width:250px;"/><br /><br />
<input type="hidden" name="do" value="commitpage" />
{if $page_id}<input type="hidden" name="page_id", value="{$page_id}" />{/if}
<input type="submit" value="Submit" class="search"/>
</form>
</div>
{include file="adminfooter.tpl"}
