<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="viewport" content = "width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$projectname} ({$productname})</title>

        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}

		<!-- load page flip preview -->
		{if ($displaytype == 1) && ($previewlicensekey != '')}
            <link type="text/css" rel="stylesheet" href="{$webroot}{asset file='/css/pageflip.css'}" media="screen" />
            <script type="text/javascript" src="{$webroot}/utils/jquery.js" {$nonce}></script>
            <script type="text/javascript" src="{$webroot}/utils/pageturning/pageflip5/js/pageflip5-min.js" {$nonce}></script>
		{/if}
			
        {include file="includes/customerinclude_small.tpl"}

        <script type="text/javascript" {$nonce}>
            //<![CDATA[

            var gSiteContainer = 0;


            {include file="share/preview.tpl"}

{literal}

            window.addEventListener('DOMContentLoaded', function(e) {

                document.body.onresize = function(e) {
                  resizeApp();
                };

                document.body.addEventListener('click', decoratorListener);
            });

            function resizeApp()
            {
                resetPreview();

                initSharePreview(false);

                //move to the current image
                showCurrent(gCurrentPosition);

                if (gDialogStatus == 'open')
                {
                    setDialogPosition();
                }
            }

            function initSharePreview(pInit)
            {
                showLoadingDialog();

                var contentNavigationPreview = document.getElementById('contentNavigationPreview');
                var styleContentNavigationPreview = contentNavigationPreview.currentStyle || window.getComputedStyle(contentNavigationPreview);
                var width = parseIntStyle(styleContentNavigationPreview.width);

                var contentScrollCart = document.getElementById('contentScrollCart');
                var styleContentScrollCart = contentScrollCart.currentStyle || window.getComputedStyle(contentScrollCart);
                var contentScrollCartWidth = parseIntStyle(styleContentScrollCart.paddingLeft) + parseIntStyle(styleContentScrollCart.paddingRight);

                gSiteContainer = width - contentScrollCartWidth;

                initPreview(pInit);
            }

            // wrapper method for productReorder
            function fnProductReorder(pElement)
            {
                if (!pElement)
                {
                    return false;
                }

                return productReorder(pElement.getAttribute('data-item-id'));
            }

            function productReorder(prderItemID)
            {
                var form = document.submitform;
                var fsactionField = form.fsaction;
                var orderItemIdField = form.orderitemid;
                var actionField = form.action;
                if (fsactionField)
                {
                    fsactionField.value = 'Share.reorder';
                }
                if (orderItemIdField)
                {
                    orderItemIdField.value = prderItemID;
                }
                if (actionField)
                {
                    actionField.value = 'CUSTOMER REORDER';
                }
                form.submit();
            }

{/literal}
            //]]>
        </script>
    </head>
    <body>

        <!-- DIALOGS -->

        <div id="shim" class="shim">&nbsp;</div>
        <div id="shimSpinner" class="shimSpinner">&nbsp;</div>

        <div id="dialogOuter" class="dialogOuter"></div>

        <div id="dialogLoading" class="dialogLoading">
          <img class="loadingImage" src="{$webroot}/images/mobile_loading.png" alt=""/>
        </div>

        <!-- END DIALOGS -->

        <!-- HIDDEN DIV TO ACCESS STYLE -->

        <div id="contentScrollCart" class="contentScrollCart hide"></div>

        <!-- END HIDDEN DIV TO ACCESS STYLE -->

        <div id="outerPage" class="outerPage">

             <div id="headerSmall" class="header" style="display:none;">
                <div class="headerinside">
                    {include file="$header"}
                </div>
            </div>

            <div id="contentBlocSite" class="contentBlocSite">

                <div id="loadingGif" class="loadingGif"></div>

    {if !$ordercancelled}

        {if $canOrder == 1}

                <div id="contentNavigationPreview" class="contentNavigationPreview">

                    <div class="buttonTopSection">

                        <div class="btnRightSectionPreview">

                            <div class="btnPreviewRight" data-decorator="fnProductReorder" data-item-id="{$orderitemid}">
                                <div class="btnUpdate">{#str_LabelOrder#}</div>
                            </div>

                        </div>
                        <div class="clear"></div>

                    </div> <!-- buttonTopSection -->

                </div> <!-- contentNavigation -->

        {else} {* end {if $canOrder == 1}*}

                <div id="contentNavigationPreview"></div>

        {/if}  {* end {if $canOrder == 1} *}

    {else} {* else {if !$ordercancelled}*}

                <div id="contentNavigationPreview"></div>

    {/if} {* end {if !$ordercancelled} *}

                <div id="contentRightScrollPreview" class="contentScrollCart">


    {if $displaytype == 4}

                    <iframe id="onlinePreviewFrame" style="border:0;" scrolling="no" seamless="seamless" src="{$externalpreviewurl}"></iframe>

    {else} {* else {if $displaytype == 4} *}

                    {include file="share/preview2_small.tpl"}

    {/if} {* end {if $displaytype == 4} *}

                </div> <!-- contentScrollCart -->

            </div> <!-- contentBlocSite -->

        </div> <!-- outerPage -->

        <script type="text/javascript" {$nonce}>
            //<![CDATA[
{literal}
                initSharePreview(true);

{/literal}
            //]]>
        </script>

        <form id="submitform" name="submitform" method="post" action="#" accept-charset="utf-8">
            <input type="hidden" id="ref" name="ref" value="{$session}" />
            <input type="hidden" id="fsaction" name="fsaction" value="" />
            <input type="hidden" id="orderitemid" name="orderitemid" value="" />
            <input type="hidden" id="action" name="action" value="" />
            <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
        </form>

    </body>
</html>