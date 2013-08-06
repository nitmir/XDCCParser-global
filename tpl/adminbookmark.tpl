{include file="adminheader.tpl"}
<div style="padding:20px;">
<form action="admin.php" method="post">
Name: <input type="text" name="bmname" value="{$bm}" class="search" style="width:150px;"/><br /><br />
Search Value: <input type="text" name="bmval" value="{$bmv}" class="search" style="width:250px;"/><br /><br />
<input type="hidden" name="do" value="commitbookmark" />
{if $bm_id}<input type="hidden" name="bm_id", value="{$bm_id}" />{/if}
<input type="submit" value="Submit" class="search"/>
</form>
</div>
{include file="adminfooter.tpl"}
