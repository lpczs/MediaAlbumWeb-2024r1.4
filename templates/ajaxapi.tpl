{if $result eq "ERROR"}
    {$resultParam}
{elseif $result eq "ZONECOUNTRY"}
    {section name=index loop=$othercountries}
        {if $smarty.section.index.first}

        <div class="wizard-dropdown">
            <select id="countrylist" name="countrylist" class="text wizard-dropdown" data-decorator="countryChange">
                    {/if}
                <option value="{$othercountries[index].isocode2}" {if $othercountries[index].isocode2 eq $country}selected="selected"{/if}>
                    {$othercountries[index].name}
                </option>
                    {if $smarty.section.index.last}
            </select>
        </div>

        {/if}
    {/section}
{elseif $result eq "ZONEREGION"}
    {section name=index loop=$regionList}
        {* we are given the label name. e.g. str_LabelState and we have to get the actual string for it *}
        {eval var=$label assign='fieldLabel'}
        {if $smarty.section.index.first}
<label for="regionlist">
    &nbsp;{$smarty.config.$fieldLabel}:&nbsp;
</label>&nbsp;

<div class="wizard-dropdown">
    <select id="regionlist" name="regionlist" class="text" class="text wizard-dropdown">'
                {if $allEnabled eq 1}
        <option value="--" >
            --{#str_LabelAll#}--
        </option>
                {/if}
                {assign var="currentitem" value=''}
            {/if}
            {if $currentitem ne $regionList[index].group}
                {if $currentitem ne ""}
        </optgroup>
                {/if}
        <optgroup label="{$regionList[index].group}">
                {assign var="currentitem" value=$regionList[index].group}
            {/if}
            <option value="{$regionList[index].code}" >
            {$regionList[index].name}
            </option>
            {if $smarty.section.index.last}
                {if $currentitem ne ""}
        </optgroup>
                {/if}
    </select>
</div>
        {/if}
    {/section}
{elseif $result eq "ADDRESSFORM"}
    {strip}
        {section name=addressline loop=$addressForm}
            {eval var=$addressForm[addressline].label assign='fieldLabel'}
            {if $smarty.section.addressline.first}
<div>
            {else}
<div class="top_gap">
            {/if}
    <div class="formLine1">
            {if $addressForm[addressline].name eq "firstname"}
        <label for="maincontactfname">
            {$smarty.config.$fieldLabel}:
        </label>
                {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                {else}
        <div class="gap-label-mandatory"></div>
                {/if}
    </div>
    <div class="formLine2">
            <input type="text" id="maincontactfname" name="maincontactfname" value="" />
            {elseif $addressForm[addressline].name eq "lastname"}
        <label for="maincontactlname">
            {$smarty.config.$fieldLabel}:
        </label>
                {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                {else}
        <div class="gap-label-mandatory"></div>
                {/if}
    </div>
    <div class="formLine2">
        <input type="text" id="maincontactlname" name="maincontactlname" value="" />
            {elseif $addressForm[addressline].name eq "company"}
        <label for="maincompanyname">
            {$smarty.config.$fieldLabel}:
        </label>
                {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                {else}
        <div class="gap-label-mandatory"></div>
                {/if}
    </div>
    <div class="formLine2">
        <input type="text" id="maincompanyname" name="maincompanyname" value="" {$readonly} />
            {elseif $addressForm[addressline].name eq "add1"}
        <label for="mainaddress1">
            {$smarty.config.$fieldLabel}:
        </label>
                {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                {else}
        <div class="gap-label-mandatory"></div>
                {/if}
    </div>
    <div class="formLine2">
        <input type="text" id="mainaddress1" name="mainaddress1" value="" {$readonly} />
            {elseif $addressForm[addressline].name eq "add2"}
        <label for="mainaddress2">
            {$smarty.config.$fieldLabel}:
        </label>
                {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                {else}
        <div class="gap-label-mandatory"></div>
                {/if}
    </div>
    <div class="formLine2">
        <input type="text" id="mainaddress2" name="mainaddress2" value="" {$readonly} />
            {elseif $addressForm[addressline].name eq "add3"}
        <label for="mainaddress3">
            {$smarty.config.$fieldLabel}:
        </label>
                {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                {else}
        <div class="gap-label-mandatory"></div>
                {/if}
    </div>
    <div class="formLine2">
        <input type="text" id="mainaddress3" name="mainaddress3" value="" {$readonly} />
            {elseif $addressForm[addressline].name eq "add4"}
        <label for="mainaddress4">
            {$smarty.config.$fieldLabel}:
        </label>
                {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                {else}
        <div class="gap-label-mandatory"></div>
                {/if}
    </div>
    <div class="formLine2">
        <input type="text" id="mainaddress4" name="mainaddress4" value="" {$readonly} />
    	{elseif $addressForm[addressline].name eq "regtaxnumtype"}
        <label for="regtaxnumtype">
            {$smarty.config.$fieldLabel}:
        </label>
                {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                {else}
        <div class="gap-label-mandatory"></div>
                {/if}
    </div>
    <div class="formLine2">
        <div class="wizard-dropdown">
            <select id="regtaxnumtype" name="regtaxnumtype" class="wizard-dropdown sp-dropdown-size">
                {section name=index loop=$registeredtaxnumbertypes}
                    <option value="{$registeredtaxnumbertypes[index].id}">
                        {$registeredtaxnumbertypes[index].name}
                    </option>
                {/section}
            </select>
        </div>
    	{elseif $addressForm[addressline].name eq "regtaxnum"}
        <label for="regtaxnum">
            {$smarty.config.$fieldLabel}:
        </label>
                {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                {else}
        <div class="gap-label-mandatory"></div>
                {/if}
    </div>
    <div class="formLine2">
        <input type="text" id="regtaxnum" name="regtaxnum" value="" {$readonly} />
            {elseif $addressForm[addressline].name eq "city"}
        <label for="maincity">
            {$smarty.config.$fieldLabel}:
        </label>
                {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                {else}
        <div class="gap-label-mandatory"></div>
                {/if}
    </div>
    <div class="formLine2">
        <input type="text" id="maincity" name="maincity" value="" {$readonly} />
            {elseif $addressForm[addressline].name eq "county"}
                {if $region eq "COUNTY"}
                    {section name=index loop=$regionList}
                        {if $smarty.section.index.first}
        <label for="countylist">
            {$smarty.config.$fieldLabel}:
        </label>
                {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                {else}
        <div class="gap-label-mandatory"></div>
                {/if}
    </div>
    <div class="formLine2">

         <div class="wizard-dropdown ">
            <select id="countylist" name="countylist" class="wizard-dropdown sp-dropdown-size">
                <option value="--" >
                    --{#str_LabelMakeSelection#}--
                </option>
                                {assign var="currentitem" value=''}
                            {/if}
                            {if $currentitem ne $regionList[index].group}
                                {if $currentitem ne ""}
                </optgroup>
                                {/if}
                <optgroup label="{$regionList[index].group}">
                                {assign var="currentitem" value=$regionList[index].group}
                            {/if}
                <option value="{$regionList[index].code}" >
                    {$regionList[index].name}
                </option>
                            {if $smarty.section.index.last}
                                {if $currentitem ne ""}
                </optgroup>
                                {/if}
            </select>
         </div>
                        {/if}
                    {sectionelse}
        <label for="maincounty">
            {$smarty.config.$fieldLabel}:
        </label>
                {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                {else}
        <div class="gap-label-mandatory"></div>
                {/if}
    </div>
    <div class="formLine2">
        <input type="text" id="maincounty" name="maincounty" value="{$county}" {$readonly} />
                    {/section}
                {else}
        <label for="maincounty">
            {$smarty.config.$fieldLabel}:
        </label>
                {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                {else}
        <div class="gap-label-mandatory"></div>
                {/if}
    </div>
    <div class="formLine2">
        <input type="text" id="maincounty" name="maincounty" value="{$county}" {$readonly} />
                {/if}
            {elseif $addressForm[addressline].name eq "state"}
                {if $region eq "STATE"}
                    {section name=index loop=$regionList}
                        {if $smarty.section.index.first}
        <label for="statelist">
            {$smarty.config.$fieldLabel}:
        </label>
                {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                {else}
        <div class="gap-label-mandatory"></div>
                {/if}
    </div>
    <div class="formLine2">
        <div class="wizard-dropdown">
            <select id="statelist" name="statelist" class="wizard-dropdown sp-dropdown-size">
                <option value="--" >
                    --{#str_LabelMakeSelection#}--
                </option>
                                {assign var="currentitem" value=""}
                            {/if}
                            {if $currentitem ne $regionList[index].group}
                                {if $currentitem ne ""}
                </optgroup>
                                {/if}
                <optgroup label="{$regionList[index].group}">
                                    {assign var="currentitem" value=$regionList[index].group}
                                {/if}
                    <option value="{$regionList[index].code}" >
                        {$regionList[index].name}
                    </option>
                                {if $smarty.section.index.last}
                                    {if $currentitem ne ""}
                </optgroup>
                                    {/if}
            </select>
        </div>
                                {/if}
                        {sectionelse}
        <label for="mainstate">
            {$smarty.config.$fieldLabel}:
        </label>
                {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                {else}
        <div class="gap-label-mandatory"></div>
                {/if}
    </div>
    <div class="formLine2">
        <input type="text" id="mainstate" name="mainstate" value="{$state}" {$readonly} />
                        {/section}
                    {else}
        <label for="mainstate">
            {$smarty.config.$fieldLabel}:
        </label>
                    {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                    {else}
        <div class="gap-label-mandatory"></div>
                    {/if}
    </div>
    <div class="formLine2">
        <input type="text" id="mainstate" name="mainstate" value="{$state}" {$readonly} />
                    {/if}
                {elseif $addressForm[addressline].name eq "postcode"}
        <label for="mainpostcode">
            {$smarty.config.$fieldLabel}:
        </label>
                    {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                    {else}
        <div class="gap-label-mandatory"></div>
                    {/if}
    </div>
    <div class="formLine2">
        <input type="text" id="mainpostcode" name="mainpostcode" value="{$postcode}" {$readonly} data-decorator="fnCJKHalfWidthFullWidthToASCII" data-force-uppercase="true" />
                {elseif $addressForm[addressline].name eq "country"}
                    {if $readonly eq ""}
        <label for="countrylist">
            {$smarty.config.$fieldLabel}:
        </label>
                        {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                        {else}
        <div class="gap-label-mandatory"></div>
                        {/if}
    </div>
    <div class="formLine2">
            <div class="wizard-dropdown">
                <select id="countrylist" name="countrylist" class="wizard-dropdown" data-decorator="fnCountryChange">
                                {section name=index loop=$countryList}
                    <option value="{$countryList[index].isocode2}" {if $countryList[index].isocode2 eq $countryCode}selected="selected "{/if}>
                        {$countryList[index].name}
                    </option>
                                {/section}
                </select>
            </div>

                    {else}
        <label for="country">
            {$smarty.config.$fieldLabel}:
        </label>
                        {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                        {else}
        <div class="gap-label-mandatory"></div>
                        {/if}
    </div>
    <div class="formLine2">
        <input type="text" id="country" name="country" value="{$countryName}" {$readonly} />
                    {/if}
                {elseif $addressForm[addressline].name eq "add41"}
        <label for="mainadd41">
            {$smarty.config.$fieldLabel}:
        </label>
                    {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                    {else}
        <div class="gap-label-mandatory"></div>
                    {/if}
    </div>
    <div class="formLine2">
        <input type="text" id="mainadd41" name="mainadd41" value="" {$readonly} />
                {elseif $addressForm[addressline].name eq "add42"}
        <label for="mainadd42">
            {$smarty.config.$fieldLabel}:
        </label>
                    {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                    {else}
        <div class="gap-label-mandatory"></div>
                    {/if}
    </div>
    <div class="formLine2">
        <input type="text" id="mainadd42" name="mainadd42" value="" {$readonly} />
                {elseif $addressForm[addressline].name eq "add43"}
        <label for="mainadd43">
            {$smarty.config.$fieldLabel}:
        </label>
                    {if $addressForm[addressline].compulsory eq "1"}
        <img src="{$brandroot}/images/asterisk.png" alt=""/>
                    {else}
        <div class="gap-label-mandatory"></div>
                    {/if}
    </div>
    <div class="formLine2">
        <input type="text" id="mainadd43" name="mainadd43" value="" {$readonly} />
                {/if}
                {if $addressForm[addressline].compulsory eq "1"}
        <img class="error_form_image" id="{$addressForm[addressline].name}compulsory" src="{$brandroot}/images/asterisk.png" alt=""/>
                {/if}
                {if $addressForm[addressline].name eq "state" && $addressForm[addressline].compulsory eq "0"}
        <img id="{$addressForm[addressline].name}compulsory" src="{$webroot}/images/dummy.gif" alt=""/>
                {/if}
                {if $addressForm[addressline].name eq "county" && $addressForm[addressline].compulsory eq "0"}
        <img id="{$addressForm[addressline].name}compulsory" src="{$webroot}/images/dummy.gif" alt=""/>
                {/if}
                {if $addressForm[addressline].name eq "city" && $addressForm[addressline].compulsory eq "0"}
        <img id="{$addressForm[addressline].name}compulsory" src="{$webroot}/images/dummy.gif" alt=""/>
                {/if}
                {if $addressForm[addressline].name eq "postcode" && $addressForm[addressline].compulsory eq "0"}
        <img id="{$addressForm[addressline].name}compulsory" src="{$webroot}/images/dummy.gif" alt=""/>
                {/if}
        <div class="clear"></div>
    </div>
</div>
            {/section}
<input type="hidden" id="region" name="region" value="{$region}" />

    {/strip}
{else}
    Unforeseen error
{/if}
