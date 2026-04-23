<div id="contentNavigationNewAccount" class="contentNavigation">

    <div class="btnDoneTop" data-decorator="fnSetHashUrl" data-hash-url="">
        <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
        <div class="btnDone">{#str_ButtonCancel#}</div>
        <div class="clear"></div>
    </div>

</div> <!-- contentNavigationShare -->

<div id="contentScrollNewAccount" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">
            {#str_LabelNewAccount#}
        </div>

        <div class="newAccountBloc">
			 <form method="post" id="mainform" name="mainform" action="#">
            	{include file="addressform2.tpl"}
			</form>
        </div>

         <div class="outerBox outerBoxMarginTop">
            <h2 class="title-bar outerBoxPadding">
                {#str_LabelCommunicationPreferences#}
            </h2>

            <ul id="marketingList" class="marketingInfo">
                <li class="outerBoxPadding optionListNoBorder">
                    <input type="checkbox" name="sendmarketinginfo" id="subscribed" value="1" {if $sendmarketinginfo == 1} checked="checked"{/if} />
                    <div>
                        <label class="listLabel" for="subscribed">
                            <span>{#str_LabelMarketingSubscribe#}</span>
                    </div>
                    <div class="clear"></div>
                </li>
            </ul>

        </div>

    {if $showtermsandconditions == 1}

        <div class="outerBox outerBoxMarginTop">

            <h2 class="title-bar outerBoxPadding">
                {#str_TitleTermsAndConditions#}
            </h2>

            <div class="termsAndConditionsText">
                <input class="inputTermsAndConditions" type="checkbox" name="ordertermsandconditions" id="ordertermsandconditions" data-decorator="acceptTermsAndConditions">
                <label class="labelTermsAndConditions" for="ordertermsandconditions" id="labelTermsAndConditions">
                    {#str_LabelTermsAndConditionsAgreement#} <a id="ordertermsandconditionslink" href="#" data-decorator="fnOrderTermsAndConditions" class="termsAndConditionsLink">{#str_TitleTermsAndConditions#}</a>
                </label>
                <div class="clear"></div>
            </div>

        </div>

        <div class="paddingBtnBottomPage">
            <div class="btnAction btnContinue disabled" id="confirmButton">
                <div class="btnContinueContent">{#str_ButtonCreate#}</div>
            </div>
        </div>

    {else}


        <div class="paddingBtnBottomPage">
            <div class="btnAction btnContinue" id="confirmButton">
                <div class="btnContinueContent">{#str_ButtonCreate#}</div>
            </div>
        </div>
    {/if} {* end {if $showtermsandconditions == 1}*}

    </div> <!-- contentVisible -->

</div> <!-- contentScrollCart -->

<form id="submitformNewAccount" name="submitformNewAccount" method="post" action="#" accept-charset="utf-8">
    <input type="hidden" id="ref" name="ref" value="{$ref}" />
    <input type="hidden" id="fsaction" name="fsaction" value="" />
    <input type="hidden" id="ssotoken" name="ssotoken" value="{$ssotoken}" />
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
    <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
</form>