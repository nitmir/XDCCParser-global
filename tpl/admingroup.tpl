{include file="adminheader.tpl"}
<div style="padding:20px;">
<form action="admin.php" method="post">
Group Name (leave blank for none): <input type="text" name="groupname" value="{$group}" class="search" style="width:150px;"/><br /><br />
<input type="hidden" name="do" value="commitgroup" />
<input type="submit" value="Submit" class="search"/>
</form>
</div>
{include file="adminfooter.tpl"}
