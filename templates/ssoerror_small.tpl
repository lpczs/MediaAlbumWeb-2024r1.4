<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
        <title>{$appname}</title>

        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}

        {include file="$customerinclude"}
    </head>
    <body>
        <div class="outer-page fullsize-outer-page">

            <div id="headerSmall" class="header">
                <div class="headerinside">
                    {include file="$header"}
                </div>
            </div>

            <div class="contentScrollCartNoNavigation">
                <h1>{$title}</h1>
                <p>{$message}</p>   
            </div>
        </div>
    </body>

</html>