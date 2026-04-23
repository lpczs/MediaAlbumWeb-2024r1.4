<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
{include file="includes/admininclude.tpl"}

{literal}
<script type="text/javascript">

function newReport()
{	
	parent.exportframe.location = "./?fsaction=AdminExportManual.initialize&ref={/literal}{$ref}{literal}";
	
	return false;
}

</script>
{/literal}

</head>
<body>
<span class="text2">
{if $datetype eq 'OR'}
 {#str_LabelOrdersReceived#}
{elseif $datetype eq 'OP'}
 {#str_LabelPrintedDate#}
{else}
 {#str_LabelShippedDate#}
{/if}</span>
<p>
<span class="text1">
{#str_LabelStartDate#}: {$startdate}<br>
{#str_LabelEndDate#}: {$enddate}<br>
{if $filtertype eq 'BRAND'}
{#str_LabelBrand#}:
{if $filtervalue eq ''}
<i>{#str_LabelNone#}</i>
{else}
{$filtervalue}
{/if}
{elseif $filtertype eq 'GROUPCODE'}
{#str_LabelLicenseKey#}: {$filtervalue}
{/if}
<br>
</span>
<span class="text3" style="color:#FF0000">{$error}&nbsp;</span>
<table class="adminList" cellpadding="5" cellspacing="0">
<tbody class="text1">
<tr><td colspan="9"></td></tr>
<tr>
 <td width="5"></td>
 <td class="tableHeadBorderLeft">{#str_LabelOrderNumber#}</td>
 <td class="tableHeadBorderMiddle">{#str_LabelOrderDate#}</td>
 <td class="tableHeadBorderMiddle">{#str_LabelPrintedDate#}</td>
 <td class="tableHeadBorderMiddle">{#str_LabelShippedDate#}</td>
 <td class="tableHeadBorderMiddle">{#str_LabelProduct#}</td>
 <td class="tableHeadBorderMiddle">{#str_LabelQuantity#}</td>
 <td class="tableHeadBorderMiddle">{#str_SectionTitleBranding#}</td>
 <td class="tableHeadBorderMiddle">{#str_LabelOrderTotal#}</td>
 <td class="tableHeadBorderMiddle">{#str_LabelBillingAddress#}</td>
 <td class="tableHeadBorderMiddle">{#str_LabelShippingAddress#}</td>
 <td width="5"></td>
</tr>
</tbody>
<tbody class="text">
{$rows}
<tr><td colspan="9"></td></tr>
<tr>
 <td width="5"></td>
 <td colspan="7">
   <input type="button" id="add" class="button1" style="position:relative; left:-5" value="{#str_ButtonNewExport#}" onClick="return newReport();"/>
 </td>
 <td width="5"></td>
</tr>
<tr><td colspan="9"></td></tr>
</tbody>
</table>
</body>
</html>


