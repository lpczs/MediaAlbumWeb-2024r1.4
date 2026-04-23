<div id="contentNavigationAddress" class="contentNavigation">
    <div class="buttonTopSection">

    {if $useraddressupdated == 1}

        <div class="btnLeftSection" data-decorator="cancelDataEntry">
            <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone">{#str_ButtonCancel#}</div>
            <div class="clear"></div>
        </div>

    {/if} {* end {if $useraddressupdated == 1} *}

    </div>
</div>

<div id="contentRightScrollAddress" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">
            {$title}
        </div>
        <div class="outerBox outerBoxPadding outerBoxMarginBottom">
            <div id="contentAddressForm">
                {include file="addressform.tpl"}
            </div>
        </div>
        <div class="btnUpdateLarge" data-decorator="verifyAddress">
            <div id="updateButtonLarge" class="btnUpdateContent">{#str_ButtonUpdate#}</div>
        </div>
        <form id="submitformaddress" name="submitformaddress" method="post" accept-charset="utf-8" action="#">
            <input type="hidden" id="ref" name="ref" value="{$ref}" />
            <input type="hidden" id="fsaction" name="fsaction" value="" />
            <input type="hidden" id="ssotoken" name="ssotoken" value="{$ssotoken}" />
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
            <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
        </form>

    </div> <!-- contentVisible -->

</div> <!-- contentScrollCart -->