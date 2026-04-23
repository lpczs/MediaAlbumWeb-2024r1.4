<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
{include file="includes/admininclude.tpl"}

{literal}
<script language="JavaScript">

function openShippingMethods()
{
    parent.shippingframe.location = "./?fsaction=AdminShippingMethods.initialize&ref={/literal}{$ref}{literal}";
}

function openShippingZones()
{
    parent.shippingframe.location = "./?fsaction=AdminShippingZones.initialize&ref={/literal}{$ref}{literal}";
}

function openShippingRates()
{
    parent.shippingframe.location = "./?fsaction=AdminShippingRates.initialize&ref={/literal}{$ref}{literal}";
}

</script>
{/literal}

</head>
<body>
<span class="adminPane" style="position:absolute; width:100%; height:100%">
{if !$TPX_LOGIN_COMPANY_ADMIN}
<a href="javascript:openShippingMethods();">{#str_ShippingTitleShippingMethods#}</a>
<p>
{/if}
<a href="javascript:openShippingZones();">{#str_ShippingTitleShippingZones#}</a>
<p>
<a href="javascript:openShippingRates();">{#str_ShippingTitleShippingRates#}</a>
</span>
</body>
</html>