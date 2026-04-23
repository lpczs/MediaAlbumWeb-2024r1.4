<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {$title}</title>
        {include file="includes/customerinclude_large.tpl"}
        <script type="text/javascript">
        //<![CDATA[
            {include file="order/updateaddress.tpl"}
        //]]>
        </script>
    </head>
        <!--[if IE 6]><body onload="initializeAddress(false, '');" style="position: relative" class="ie6"><![endif]-->
        <!--[if gt IE 6]><!-->
    <body onload="initializeAddress(false, '');" style="position: relative">
        <!--<![endif]-->
        <div id="outerPage" class="outer-page{if $sidebarleft != ''} fullsize-outer-page{/if}">
            <div id="header" class="headertop">
                <div class="headerinside">
                    {include file="header_large.tpl"}
                </div>
            </div>
{if $sidebarleft != ''}
            {include file="$sidebarleft"}
{/if}
            <div id="contentHolder" class="longContentHeader">
                <div id="pageFooterHolder" {if $sidebaraccount == '' and $sidebarcontactdetails == '' and $sidebarsearch == ''}class="fullsizepage"{/if}>
                    <div id="page" class="section">
                        <h1 class="title-bar">
                            {$title}
                        </h1>
                        <div class="content">
							{include file="addressform.tpl"}
                        </div>
                    </div>
                </div>
                <div class="buttonBottom">
{if $useraddressupdated == 1}
                    <div class="btnLeft">
                        <div class="contentBtn" id="cancel" onclick="cancelDataEntry();">
                            <div class="btn-red-cross-left" ></div>
                            <div class="btn-red-middle">{#str_ButtonCancel#}</div>
                            <div class="btn-red-right"></div>
                        </div>
                    </div>
{/if}
                    <div class="btnRight">
                        <div class="contentBtn" id="ok" onclick="verifyAddress();">
                            <div class="btn-green-middle">{#str_ButtonChange#}</div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
{if $sidebaraccount != '' or $sidebarcontactdetails != ''}
                <div class="side-outer-panel">
    {if $sidebaraccount != ''}
                    {include file="$sidebaraccount"}
    {/if}
	{if $sidebarcontactdetails != ''}
                    {include file="$sidebarcontactdetails"}
    {/if}
                </div>
{/if}

{if $sidebaradditionalinfo != ''}
                {include file="$sidebaradditionalinfo"}
{/if}
            </div>
        </div>
        <form id="submitformaddress" name="submitformaddress" method="post" accept-charset="utf-8" action="#">
            <input type="hidden" id="ref" name="ref" value="{$ref}" />
            <input type="hidden" id="fsaction" name="fsaction" value="" />
            <input type="hidden" id="contactfname" name="contactfname" />
            <input type="hidden" id="contactlname" name="contactlname" />
            <input type="hidden" id="companyname" name="companyname" />
            <input type="hidden" id="address1" name="address1" />
            <input type="hidden" id="address2" name="address2" />
            <input type="hidden" id="address3" name="address3" />
            <input type="hidden" id="address4" name="address4" />
            <input type="hidden" id="add41" name="add41" />
            <input type="hidden" id="add42" name="add42" />
            <input type="hidden" id="add43" name="add43" />
            <input type="hidden" id="city" name="city" />
            <input type="hidden" id="county" name="county" />
            <input type="hidden" id="state" name="state" />
            <input type="hidden" id="regioncode" name="regioncode" />
            <input type="hidden" id="region" name="region" />
            <input type="hidden" id="postcode" name="postcode" />
            <input type="hidden" id="countrycode" name="countrycode" />
            <input type="hidden" id="countryname" name="countryname" />
            <input type="hidden" id="submit_telephonenumber" name="telephonenumber" />
            <input type="hidden" id="submit_email" name="email" />
            <input type="hidden" id="submit_registeredtaxnumbertype" name="registeredtaxnumbertype" />
            <input type="hidden" id="submit_registeredtaxnumber" name="registeredtaxnumber" />
            <input type="hidden" name="previousstage" value="{$previousstage}"/>
            <input type="hidden" name="stage" value="{$stage}"/>
        </form>
    </body>
</html>