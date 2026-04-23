<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
{include file="includes/admininclude.tpl"}

{literal}
<script language="JavaScript">

function openExportManual()
{
    parent.exportframe.location = "./?fsaction=AdminExportManual.initialize&ref={/literal}{$ref}{literal}";
}

function openExportEvent()
{
    parent.exportframe.location = "./?fsaction=AdminExportEvent.initialize&ref={/literal}{$ref}{literal}";
}

</script>
{/literal}

</head>
<body>
<span class="adminPane" style="position:absolute; width:100%; height:100%">
<a href="javascript:openExportManual();">{#str_ExportTitleManual#}</a>
{if $showexportevents}
<p>
<span class="adminPane" style="position:absolute; width:100%; height:100%">
<a href="javascript:openExportEvent();">{#str_ExportTitleEvent#}</a>
<p>
{/if}
</span>
</body>
</html>