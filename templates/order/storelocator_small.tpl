<div id="contentNavigationStore" class="contentNavigation">
    <div class="buttonTopSection">

        <div class="btnLeftSection" id="doneSelectStoreButton" data-decorator="acceptDataEntryStoreLocator">
            <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone">{#str_ButtonDone#}</div>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>

    </div>
</div>
<div id="contentStoreList" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">
            {#str_LabelSelectStore#}
        </div>

        <div id="storeLogo" class="storeLogo">
            {if $logoheight != 0}
                <img id="storeLogoImg" class="storeLogoImg" src="{$logorow}" alt="" />
            {/if}
        </div>

        <form method="post" id="mainformstore" name="mainformstore" action="#">
            <div class="outerBox outerBoxMarginTop">

                <div id="storeDetails" class="outerBoxPadding">

                    <div class="message" id="message">
                        {$error}
                    </div>

                    <div class="inputGap">
                        <div class="formLine1">
                            <label for="searchText" class="searchStore">{$labelAddressSearch}:</label>
                        </div>
                        <div class="formLine2">
                            <input type="text" id="searchText" name="searchText" value="" class="storeInputLong" placeholder="Location" data-decorator="fnSearchForStore" data-trigger="keypress" />
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>

                <div id="storeOptions">
                    {if $showcountrylist + $showregionlist > 0}
                        {if $showcountrylist == 1}
                            <div class="inputGap">
                                <div class="formLine1">
                                    {*<label for="countries">{#str_LabelCountry#}:</label>*}
                                </div>
                                <div class="formLine2">
                                    <div class="wizard-dropdown">
                                        <select id="countries" name="countries" class="middle wizard-dropdown">
                                            <option value="{$showAllCode}" selected="selected">
                                                {#str_CountryOptional#}
                                            </option>
                                        {section name=index loop=$countrylist}

                                        <option value="{$countrylist[index].code}">
                                            {$countrylist[index].name}
                                        </option>
                                        {/section}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        {/if}
                    {/if}

                    {if $showregionlist == 1}
                        <div class="inputGap">
                            <div class="formLine1">
                                {*<label class="text" for="regions">{#str_LabelRegion#}:</label>*}
                            </div>
                            <div class="formLine2">
                                <div class="wizard-dropdown click_disabled">
                                    <select id="regions" name="regions" disabled="disabled" class="middle wizard-dropdown click_disabled">
                                        <option value="{$showAllCode}">{#str_RegionOptional#}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    {/if}

                    {if $showstoregroups == 1}
                        <div class="inputGap">
                            <div class="formLine1">
                                {*<label for="storegroups">{$storegrouplabel}</label>*}
                            </div>
                            <div class="formLine2">
                                <div class="wizard-dropdown click_disabled">
                                    <select id="storegroups" name="storegroups" disabled="disabled" class="middle wizard-dropdown click_disabled">
                                        <option value="{$showAllCode}">{#str_SiteGroupOptional#}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    {/if}
                </div>
            </div><!-- outerBox outerBoxPadding outerBoxMarginTop -->

            <div class="btnSearchStore btnBlue">
                <div id="searchButton" class="btnContinueContent" data-decorator="fnSearchForStore" data-trigger="click">{#str_ButtonSearch#}</div>
            </div>

            <div id="storeList">
                <div id="storeListAjaxDiv">{$storelist}</div>
            </div>
        </form>
    </div> <!-- contentVisible -->

</div> <!-- contentScrollCart -->

<form id="submitformStoreLocator" name="submitformStoreLocator" method="post" accept-charset="utf-8" action="#">
    <input type="hidden" id="ref" name="ref" value="{$ref}" />
    <input type="hidden" id="fsaction" name="fsaction" value="" />
    <input type="hidden" name="previousstage" value="{$previousstage}"/>
    <input type="hidden" name="stage" value="{$stage}"/>
    <input type="hidden" name="filter" value=""/>
    <input type="hidden" name="privatefilter" value=""/>
    <input type="hidden" name="storegroup" value=""/>
    <input type="hidden" name="region" value=""/>
    <input type="hidden" name="country" value=""/>
    <input type="hidden" name="storecode" value=""/>
    <input type="hidden" name="payinstoreallowed" value=""/>
    <input type="hidden" name="shippingratecode" value="{$shippingratecode}"/>
    <input type="hidden" name="sameshippingandbillingaddress" value="{$sameshippingandbillingaddress}"/>
</form>