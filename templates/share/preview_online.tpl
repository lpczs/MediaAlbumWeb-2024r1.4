<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="csrf-token" content="{csrf_token}" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />

        <!-- NOTE VIEWPORT SETTINGS - important to prevent screen size calculations, minimal-ui to support iOS8 -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
        <title>{$projectname}</title>

        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}
        
        <link rel="stylesheet" href="{$brandroot}{asset file='/css/styles.css'}" type="text/css">
    </head>
    <body class="share-preview {if $sharehidebranding != 0}no-branding{/if}">
        <div id="header" class="header">
            {include file="$header"}
        </div>

        {if !$error}
            <iframe id="tframe" class="preview-iframe" scrolling="no" seamless="seamless" src="{$designurl}" >
            </iframe>
        {else}
            <div class="share-error-message">
                <p>{$error}</p>
            </div>
        {/if}

        <script type="text/javascript" {$nonce}>
            var shareHideBranding = {$sharehidebranding};
            init();

            function init()
            {
                registerListeners();
            }

            function registerListeners()
            {
                // We only need these listeners if we are on iOS device
                if (navigator.userAgent.match(/iphone|ipad/i))
                {

                    // do not reguster listeners if Chrome Browser or Safari Browser with version >= 13
                    if(!navigator.userAgent.match(/CriOS\/\d*/) && (navigator.userAgent.match(/Safari\/\d*/) && parseInt(navigator.userAgent.match(/Version\/\d*/)[0].slice(8)) >= 13)){
                        return;
                    }

                    window.addEventListener('orientationchange',resizeIframe);
                    window.addEventListener('DOMContentLoaded',resizeIframeOnDOMLoaded);
                }
            }

            function resizeIframeOnDOMLoaded(){
                resizeIframe(false);
            }

            // Need to resizeIframe as iOS devices make height adjustments as part of their page scroll implementation
            function resizeIframe(reload)
            {
                // Workaround for DOM race condition issues when calculating how to present the page
                if(reload) {
                    document.body.innerHTML = '';
                    window.location.reload();
                }
            }
        </script>

    </body>
</html>