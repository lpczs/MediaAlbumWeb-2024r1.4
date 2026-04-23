<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="csrf-token" content="{csrf_token}" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {#str_LabelNewAccount#}</title>

        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}
        
        {include file="includes/customerinclude_large.tpl"}

        <script type="text/javascript" {$nonce}>
            //<![CDATA[

            {include file="newaccount.tpl"}

            //]]>
        </script>
    </head>
    <body id="body">
		<div id="loadingBox" class="section maw_dialog">
			<div class="dialogTop">
					<h2 id="loadingTitle" class="title-bar"></h2>
			</div>
			<div class="content">
					<div class="loadingMessage">
							<img src="{$webroot}/images/loading_shoppingcart_v2.gif" class="loading-icon" alt="{#str_MessageLoading#}" />
					</div>
			</div>
	</div>
	<div id="shimLoading">&nbsp;</div>
    <div id="shim">&nbsp;</div>
	<div id="ordersTermsAndCondtions" class="section">
		<div class="dialogTop">
			<h2 class="title-bar">
				{#str_TitleTermsAndConditions#}
			</h2>
		</div>
		<div class="contentTermsAndConditions">
            <div id="termsandconditionswindow" class="contentFormTermsAndCondition"></div>
        </div>
        <div class="buttonBottomInside">
            <div class="btnRight">
                <div id="closeTermsAndConditionsButton" class="contentBtn">
                    <div class="btn-green-left" ></div>
                    <div class="btn-accept-right"></div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
        <div id="confirmationBox" class="section">
            <h1 class="title-bar">
                {#str_TitleError#}
            </h1>
            <div class="content">
                <div id="confirmationBoxText"></div>
                <p id="buttonsHolderConfirmation">
                    <div class="btnRight">
                         <div id="confirmationCloseButton" class="contentBtn">
                            <div class="btn-green-left" ></div>
                            <div class="btn-green-middle">{#str_LabelClose#}</div>
                            <div class="btn-accept-right"></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </p>
            </div>
        </div>
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
                <div {if $sidebarcontactdetails == ''}class="fullsizepagenewaccount"{/if}>
                    <div id="page" class="section">
                        <noscript>
                            <div class="messageNoScript">
                                {#str_ErrorNoJavaScript#}
                            </div>
                        </noscript>
                        <form method="post" id="mainform" name="mainform" action="#" onsubmit="return verifyAddress();">
                            <div class="content">
                                <div class="message" id="message">{$error}</div>
                                {include file="addressform2.tpl"}
                                <div class="blocAccount">
                                    <h2 class="title-bar">
                                        {#str_LabelCommunicationPreferences#}
                                    </h2>
                                    <div class="currentBloc">
                                        <div class="top_gap">
                                            <input type="checkbox" name="sendmarketinginfo" id="subscribed" value="1" class="widthAuto" {if $sendmarketinginfo == 1} checked="checked"{/if} />
                                            <label class="widthAuto" for="subscribed">
                                                {#str_LabelMarketingSubscribe#}
                                            </label>
                                        </div>
                                    </div>
{if $strictmode == '1'}
                                    <div class="note">
                                        <img src="{$brandroot}/images/asterisk.png" alt="*" class="valign-center"/>
                                        {#str_LabelCompulsoryFields#}
                                    </div>
{/if}
                                </div>

                                {if $showtermsandconditions == 1}
									 <div class="blocAccount">
										<h2 class="title-bar">
											{#str_TitleTermsAndConditions#}
										</h2>
										<div class="currentBloc">
											<div class="top_gap">
											   <input type="checkbox" class="widthAuto" name="ordertermsandconditions" id="ordertermsandconditions">
											<label class="widthAuto" for="ordertermsandconditions">
													{#str_LabelTermsAndConditionsAgreement#} <a id="ordertermsandconditionslink" class="termsAndConditionsLink" href="#">{#str_TitleTermsAndConditions#}</a>
												</label>
											</div>
										</div>
									</div>
                                {/if}

                                <input type="submit" style="display:none;"
                            </div>
                        </form>
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
                <div class="buttonBottom">
                    <div class="btnLeft">
                        <div class="contentBtn" id="backButton">
                            <div class="btn-blue-arrow-left" ></div>
                            <div class="btn-blue-middle">{#str_ButtonBack#}</div>
                            <div class="btn-blue-right"></div>
                        </div>
                    </div>
                    <div class="btnRight">

                        {if $showtermsandconditions == 1}
							<div class="contentBtn" id="confirmButton">
								<div id="btn-confirm-left" class="btn-disabled-left"></div>
								<div id="btn-confirm-middle" class="btn-disabled-middle">{#str_ButtonCreate#}</div>
								<div id="btn-confirm-right" class="btn-disabled-right-tick"></div>
							</div>
                        {else}
                        	<div class="contentBtn" id="confirmButton">
								<div id="btn-confirm-left" class="btn-green-left" ></div>
								<div id="btn-confirm-middle" class="btn-green-middle">{#str_ButtonCreate#}</div>
								<div id="btn-confirm-right" class="btn-accept-right"></div>
                        	</div>
                        {/if}
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <form id="submitform" name="submitform" method="post" action="#" accept-charset="utf-8">
            <input type="hidden" id="ref" name="ref" value="{$ref}" />
			<input type="hidden" id="groupcode" name="groupcode" value="{$groupcode}" />
            <input type="hidden" id="ssotoken" name="ssotoken" value="{$ssotoken}" />
            <input type="hidden" id="fsaction" name="fsaction" value="" />
            <input type="hidden" id="submit_login" name="login" />
            <input type="hidden" id="submit_password" name="password" />
            <input type="hidden" id="submit_format" name="format" />
            <input type="hidden" id="submit_contactfname" name="contactfname" />
            <input type="hidden" id="submit_contactlname" name="contactlname" />
            <input type="hidden" id="submit_companyname" name="companyname" />
            <input type="hidden" id="submit_address1" name="address1" />
            <input type="hidden" id="submit_address2" name="address2" />
            <input type="hidden" id="submit_address3" name="address3" />
            <input type="hidden" id="submit_address4" name="address4" />
            <input type="hidden" id="add41" name="add41" />
            <input type="hidden" id="add42" name="add42" />
            <input type="hidden" id="add43" name="add43" />
            <input type="hidden" id="submit_city" name="city" />
            <input type="hidden" id="submit_county" name="county" />
            <input type="hidden" id="submit_state" name="state" />
            <input type="hidden" id="submit_regioncode" name="regioncode" />
            <input type="hidden" id="submit_region" name="region" />
            <input type="hidden" id="submit_postcode" name="postcode" />
            <input type="hidden" id="submit_countrycode" name="countrycode" />
            <input type="hidden" id="submit_countryname" name="countryname" />
            <input type="hidden" id="submit_telephonenumber" name="telephonenumber" />
            <input type="hidden" id="submit_email" name="email" />
            <input type="hidden" id="submit_registeredtaxnumbertype" name="registeredtaxnumbertype" />
            <input type="hidden" id="submit_registeredtaxnumber" name="registeredtaxnumber" />
            <input type="hidden" id="submit_sendmarketinginfo" name="sendmarketinginfo" />
            <input type="hidden" id="submit_ishighlevel" name="ishighlevel" value="{$ishighlevel}"/>
            <input type="hidden" id="submit_registerfsaction" name="registerfsaction" value="{$registerfsaction}"/>
            <input type="hidden" id="submit_prtz" name="prtz" value="{$prtz}" />
            <input type="hidden" id="submit_mawebhluid" name="mawebhluid" value="{$mawebhluid}"/>
            <input type="hidden" id="submit_mawebhlbr" name="mawebhlbr" value="{$mawebhlbr}"/>
            <input type="hidden" id="submit_fromregisterlink" name="fromregisterlink" value="{$fromregisterlink}"/>
            <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
        </form>
    </body>
</html>