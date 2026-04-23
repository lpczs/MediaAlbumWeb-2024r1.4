<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="csrf-token" content="{csrf_token}" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {#str_LabelForgotPassword#}</title>
        {include file="includes/customerinclude_large.tpl"}

        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}

        {if $returninformation != ''}

        <script type="text/javascript" {$nonce}>
{literal}
            window.onload = function()
            {
                document.getElementById('continue-order').addEventListener('click', function() {
                    window.location.replace('{/literal}{$returninformation}{literal}');
                });
            }
{/literal}
        </script>
        {/if}
    </head>
    <body>
        <div class="outer-page{if $sidebarleft != ''} fullsize-outer-page{/if}">
            <div id="header" class="headertop">
                <div class="headerinside">
                    {include file="$header"}
                </div>
            </div>
            {if $sidebarleft != ''}
                {include file="$sidebarleft"}
            {/if}
            <div id="contentHolder">
                <div id="pageFooterHolder" {if $sidebarcontactdetails == ''}class="fullsizepage"{/if}>
                    <div id="page" class="section">
                        <h1 class="title-bar">
                            {#str_LabelForgottenPassword#}
                        </h1>
                        <div class="content log-in-wrap reset-password">
                            <p class="successmessage">
                                {#str_MessageResetPasswordSuccess#}
                            </p>
                            <p>
                                {#str_MessageResetPasswordSuccessNote#}
                            </p>
                            {if $returninformation != ''}
								<div class="btnLeftContinue">
									<div class="contentBtn" id="continue-order">
										<div class="btn-green-left" ></div>
										<div class="btn-green-middle">{#str_ButtonPasswordResetContinueOrder#}</div>
										<div class="btn-accept-right"></div>
									</div>
								</div>
								<div class="clear"></div>
							{/if}
                        </div>
                    </div>
                    {if $sidebaradditionalinfo != ''}
                        {include file="$sidebaradditionalinfo"}
                    {/if}

                    {if $sidebarcontactdetails != ''}
                        <div class="side-outer-panel">
                            {include file="$sidebarcontactdetails"}
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </body>
</html>