<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
{include file="includes/admininclude.tpl"}

<script language="JavaScript">
{literal}
function openTaxRates()
{
    parent.taxframe.location = "./?fsaction=AdminTaxRates.initialize&ref={/literal}{$ref}{literal}";
}

function openTaxZones()
{
    parent.taxframe.location = "./?fsaction=AdminTaxZones.initialize&ref={/literal}{$ref}{literal}";
}
{/literal}
</script>

</head>
<body>
<span class="adminPane" style="position:absolute; width:100%; height:100%">
{if !$TPX_LOGIN_COMPANY_ADMIN}
<a href="javascript:openTaxRates();">{#str_TaxTitleTaxRates#}</a>
<p>
{/if}
<a href="javascript:openTaxZones();">{#str_TaxTitleTaxZones#}</a>
</span>
</body>
</html>