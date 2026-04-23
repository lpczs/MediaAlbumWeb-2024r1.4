<table id="{$emailaddresstablename}" name="{$emailaddresstablename}" class="adminTableEntryBorder" border="0" cellpadding="5" cellspacing="0" width="100%">
<tbody class="text1">
<tr><td colspan="4"></td></tr>
<tr>
	<td width="1"></td>
	<td class="tableHeadBorderLeft" width="30%">{#str_LabelName#}</td>
	<td class="tableHeadBorderMiddle" width="70%">{#str_LabelAddress#}</td>
	<td></td>
</tr>

{section name=index loop=$emaildetailsarray}
<tr>
	<td></td>
    <td class="tableColBorderLeft"><input type="text" id="{$emailaddresstablename}_name{$smarty.section.index.iteration}" value="{$emaildetailsarray[index].name}" style="width:100%;"></td>
    <td class="tableColBorderMiddle"><input type="text" id="{$emailaddresstablename}_address{$smarty.section.index.iteration}" value="{$emaildetailsarray[index].address}" style="width:100%;"></td>
{if $smarty.section.index.total > 1 }
	<td class="text"><a href="" id="{$emailaddresstablename}_delete{$smarty.section.index.iteration}" onclick="return deleteAddressFromList('{$emailaddresstablename}', {$smarty.section.index.iteration});">{#str_ButtonDelete#}</a></td>
{else}
	<td>&nbsp;</td>
{/if}
</tr>
{/section}

<tr>
	<td></td>
	<td>
		<input id="emailadd{$emailaddresstablename}" type="button" class="button1" value="{#str_ButtonAdd#}" onClick="return addEmailAddress('{$emailaddresstablename}','','');" />
		<input id="emailtest{$emailaddresstablename}" class="button1" value="Test" onclick="return emailTest('{$emailaddresstablename}');" type="submit">
	</td>
	<td>
		<div id="pleaseWait{$emailaddresstablename}" style="color:red; font-weight:bold;" ></div>
	</td>
	<td></td>
</tr>
</table>