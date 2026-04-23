<div class="dialogContentContainer">
	<div id="storeLogo" class="storeLogo">
		<input type="hidden" id="storeLogoImgHeight" value="{$logoheight}" />
	{if $logoheight != 0}
		<img id="storeLogoImg" class="storeLogoImg" src="{$logorow}" alt="" />
	{/if}
	</div>
	<div class="contentFormStoreLocator">
		<form method="post" id="mainformstore" name="mainformstore" action="#">
			<div id="storeDetails">
				<div class="message" id="message">
					{$error}
				</div>
				<h2 class="title-bar-inside">
					{#str_LabelSelectStore#}
				</h2>
	{if $showcountrylist + $showregionlist > 0}
		{if $showcountrylist == 1}
				<div class="inputGap">
			{section name=index loop=$countrylist}
				{if $smarty.section.index.first}
					<label for="countries">{#str_LabelCountry#}:</label>
					<select id="countries" name="countries" class="middle">
						<option value="{$showAllCode}" selected="selected">
							 {#str_CountryOptional#}
						</option>
				{/if}
						<option value="{$countrylist[index].code}">
							{$countrylist[index].name}
						</option>
				{if $smarty.section.index.last}
					</select>
				{/if}
			{/section}
					<div class="clear"></div>
				</div>
		{/if}
		{if $showregionlist == 1}
				<div class="inputGap">
					<label class="text" for="regions">{#str_LabelRegion#}:</label>
					<select id="regions" name="regions" disabled="true" class="middle">
						<option value="{$showAllCode}">{#str_RegionOptional#}</option>
					</select>
					<div class="clear"></div>
				</div>
		{/if}
	{/if}
	{if $showstoregroups == 1}
				<div class="inputGap">
					<label for="storegroups">{$storegrouplabel}</label>
					<select id="storegroups" name="storegroups" disabled="disabled" class="middle"></select>
					<div class="clear"></div>
				</div>
	{/if}
				<div class="inputGap">
					<div class="contentSearchText">
						<label for="searchText">{$labelAddressSearch}:</label>
						<input type="text" id="searchText" name="searchText" value="" class="storeInputLong" data-decorator="fnSearchForStore" data-trigger="keypress" />
					</div>
					<div class="btnRight">
						<div class="contentBtn" id="searchButton" data-decorator="fnSearchForStore" data-trigger="click">
							<div class="btn-white-left" ></div>
							<div class="btn-white-middle"><img src="{$webroot}/images/icons/search_icon.png" alt="" title="{#str_ButtonSearch#}"/></div>
							<div class="btn-white-right"></div>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<div id="storeList">
				<h2 class="title-bar-inside">
					{#str_LabelListOfStores#}
				</h2>
				<div id="storeListAjaxDiv">{$storelist}</div>
			</div>
		</form>
	</div>
</div>
<div class="buttonBottomInside">
    <div class="btnLeft">
        <div class="contentBtn" id="doneSelectStoreButton" data-decorator="fnDoneSelectStoreButton">
            <div class="btn-red-cross-left" ></div>
            <div class="btn-red-middle">{#str_ButtonCancel#}</div>
            <div class="btn-red-right"></div>
        </div>
    </div>
    <div class="btnRight">
        <div class="contentBtn" id="validateSelectStoreButton" data-decorator="acceptDataEntryStoreLocator" data-trigger="click">
            <div class="btn-green-left" ></div>
            <div class="btn-green-middle">{#str_ButtonSelectStore#}</div>
            <div class="btn-accept-right"></div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<form id="submitformStoreLocator" name="submitformStoreLocator" method="post" accept-charset="utf-8" action="#">
    <input type="hidden" id="ref" name="ref" value="{$ref}" />
    <input type="hidden" id="fsaction" name="fsaction" value="" />
    <input type="hidden" id="ssotoken" name="ssotoken" value="{$ssotoken}" />
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
<script type="text/javascript" {$nonce}>
        {include file="order/storelocatorJSON_large.tpl"}
</script>