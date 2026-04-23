<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <script type="text/javascript" src="{$webroot}{asset file='/utils/functions.js'}" {$nonce}></script>
        <script type="text/javascript" {$nonce}>
{literal}
            /* create a boolean variable to check for a valid Microsoft ActiveX instance */
            var xmlhttp = false;
            /* check if we are using Internet Explorer */
            try
            {
                /* if the Javascript version is greater then 5 */
                xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch (e)
            {
                /* if not, then use the older ActiveX object */
                try
                {
                    /* if we are using Internet Explorer */
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch (e)
                {
                    /* else we must be using a non-Internet Explorer browser */
                    xmlhttp = false;
                }
            }

            /* if we are not using IE, create a JavaScript instance of the object */
            if (!xmlhttp && typeof XMLHttpRequest != 'undefined')
            {
                xmlhttp = new XMLHttpRequest();
            }

			xmlhttp.open("POST", "{/literal}{$url}{literal}", false);
			xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xmlhttp.send("csrf_token={/literal}{csrf_token}{literal}");

            var urlResult = parseJson(xmlhttp.responseText);

{/literal}
        {if $edittype == 0}
{literal}
            if (urlResult.result != 0)
            {
                document.location.replace('?error=' + urlResult.resultmessage);
            }
            else
            {
                document.location.replace(urlResult.designurl);   
            }
{/literal}

        {else}

{literal}

            if (urlResult.error != '')
            {
                document.location.replace('?error=' + urlResult.error);
            }
            else
            {
                document.location.replace(urlResult.brandurl);   
            }

{/literal}

        {/if}
        </script>
    </head>
</html>