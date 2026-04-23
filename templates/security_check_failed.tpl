<!DOCTYPE html>
<html lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <title>{$appname} - {#str_LabelWelcome#}</title>

        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}
        
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />

        {include file="includes/customerinclude_large.tpl"}
    </head>

    <body>
        <div class="outer-page fullsize-outer-page responsive-page">
            <div id="header" class="headertop">
                <div class="headerinside">
                    {include file="$header"}
                </div>
            </div>

            <div class="page-content">
                <h2 class="title-bar">
                    {#str_Error#}
                </h2>

                <div class="content error">
                    {#str_ErrorSecurityChecksFailed#}
                </div>
            </div>
        </div>
    </body>
</html>
