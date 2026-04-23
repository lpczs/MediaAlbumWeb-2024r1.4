<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
{include file="includes/admininclude.tpl"}

{literal}
<script language="JavaScript">

function openAutoUpdateApplication()
{
    parent.autoupdateframe.location = "./?fsaction=AdminAutoUpdate.initializeApplication&ref={/literal}{$ref}{literal}";
}

function openAutoUpdateLicenseKeys()
{
    parent.autoupdateframe.location = "./?fsaction=AdminAutoUpdate.initializeLicenseKeys&ref={/literal}{$ref}{literal}";
}

function openAutoUpdateProducts()
{
    parent.autoupdateframe.location = "./?fsaction=AdminAutoUpdate.initializeProducts&ref={/literal}{$ref}{literal}";
}

function openAutoUpdateBackgrounds()
{
    parent.autoupdateframe.location = "./?fsaction=AdminAutoUpdate.initializeBackgrounds&ref={/literal}{$ref}{literal}";
}

function openAutoUpdateMasks()
{
    parent.autoupdateframe.location = "./?fsaction=AdminAutoUpdate.initializeMasks&ref={/literal}{$ref}{literal}";
}

function openAutoScrapbook()
{
    parent.autoupdateframe.location = "./?fsaction=AdminAutoUpdate.initializeScrapbookPictures&ref={/literal}{$ref}{literal}";
}
function openAutoFrames()
{
    parent.autoupdateframe.location = "./?fsaction=AdminAutoUpdate.initializeFrames&ref={/literal}{$ref}{literal}";
}
</script>
{/literal}

</head>
<body>
<span class="adminPane" style="position:absolute; width:100%; height:100%">
<a href="javascript:openAutoUpdateApplication();">{#str_AutoUpdateTitleApplication#}</a>
<p>
<a href="javascript:openAutoUpdateLicenseKeys();">{#str_AutoUpdateTitleLicenseKeys#}</a>
<p>
<a href="javascript:openAutoUpdateProducts();">{#str_AutoUpdateTitleProducts#}</a>
<p>
<a href="javascript:openAutoUpdateBackgrounds();">{#str_AutoUpdateTitleBackgrounds#}</a>
<p>
<a href="javascript:openAutoUpdateMasks();">{#str_AutoUpdateTitleMasks#}</a>
<p>
<a href="javascript:openAutoScrapbook();">{#str_AutoUpdateTitleScrapbook#}</a>
<p>
<a href="javascript:openAutoFrames();">{#str_AutoUpdateTitleFrames#}</a>
</span>
</body>
</html>