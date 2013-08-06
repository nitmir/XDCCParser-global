{include file="adminheader.tpl"}
<div style="padding:20px;">
<form action="admin.php" method="post">
Nick: {if $edit}{$edit}<input type="hidden" name="botname" value="{$edit}" />{else}<input type="text" name="botname" class="search" style="width:150px;"/>{/if}<br /><br />
Access URI: <input type="text" name="boturi" value="{$boturi}" class="search" style="width:250px;"/><br /><br />
<input type="hidden" name="do" value="commitbot" />
<input type="submit" value="Submit" class="search"/>
</form>
</div>
{include file="adminfooter.tpl"}
