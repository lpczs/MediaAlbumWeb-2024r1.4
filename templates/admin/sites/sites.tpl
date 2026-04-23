<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
{include file="includes/admininclude.tpl"}

{literal}
<script language="JavaScript">

{/literal}{if $optionMC}{literal}
function openCompanyAdmin()
{
    parent.sitesframe.location = "./?fsaction=AdminSitesCompanies.initialize&ref={/literal}{$ref}{literal}";
}
{/literal}{/if}{literal}

function openSitesAdmin()
{
    parent.sitesframe.location = "./?fsaction=AdminSitesSitesAdmin.initialize&ref={/literal}{$ref}{literal}";
}

{/literal}{if $optionCFS}{literal}
function openSiteGroups()
{
    parent.sitesframe.location = "./?fsaction=AdminSitesSiteGroups.initialize&ref={/literal}{$ref}{literal}";
}

{/literal}{/if}{literal}
{/literal}{if $optionMS}{literal}
function openRouting()
{
    parent.sitesframe.location = "./?fsaction=AdminSitesOrderRouting.initialize&ref={/literal}{$ref}{literal}";
}
{/literal}{/if}{literal}
</script>
{/literal}
</head>
<body>
<span class="adminPane" style="position:absolute; width:100%; height:100%">
{if $optionMC}
<a href="javascript:openCompanyAdmin();">{#str_SiteCompanies#}</a>
<p>
{/if}
<a href="javascript:openSitesAdmin();">{#str_SiteAdmin#}</a>
{if $optionCFS}
<p>
<a href="javascript:openSiteGroups();">{#str_StoreGroups#}</a>
</p>
{/if}
{if $optionMS}
<p>
<a href="javascript:openRouting();">{#str_SiteOrderRouting#}</a>
</p>
{/if}
</span>
</body>
</html>