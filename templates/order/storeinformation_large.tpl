<div id="" class="dialogTop">
    <h2 class="title-bar">{$storename}</h2>
</div>
<div class="content">
	<div id="storeInfoContent">
        <div class="headerStoreInfo">
            <h2 id="storeDetailLabel" class="noTopMargin">
                {#str_LabelStoreDetails#}
            </h2>
            <h2 id="storeOpeningTime" class="noTopMargin">
                {#str_LabelOpeningTimes#}
            </h2>
            <div class="clear"></div>
        </div>
		<div id="storeInfoDetails">
            <div class="storeContent">
                <div>
                    <b>{$storename}</b>
                </div>
                <div class="storeDetail">
                    {$storedetails}
                </div>

{if $telephonenumber != ''}
                <div class="top_gap">
                    <b>{#str_LabelTelephoneNumber#}</b><br />
                    {$telephonenumber}
                </div>
{/if}
{if $emailaddress != ''}
				<div class="top_gap">
                    <b>{#str_LabelEmailAddress#}</b><br />
                    <a href="mailto:{$emailaddress}">
                        {$emailaddress}
                    </a>
                </div>
{/if}
{if $storeurl != ''}
				<div class="top_gap">
                    <b>{#str_LabelWebSite#}</b><br />
                    <a href="{$storeurl}" target="blank">
                        {$storeurl}
                    </a>
                </div>
{/if}
{if $information != ''}
				<div class="top_gap">
                    <b>{#str_LabelAdditionalInformation#}</b><br />
                    {$information}
                </div>
{/if}
            </div>
		</div>
		<div id="storeInfoOpeningTimes">
            <div class="storeContent">
                {if $storeopeningtimes != ''}
                    {$storeopeningtimes}
                {else}
                    {#str_NoInformation#}
                {/if}
            </div>
		</div>
	</div>
	<div class="buttonBottomInside btnRight">
        <div class="contentBtn" id="storeInfoBackButton" data-decorator="fnCloseStoreInfo">
            <div class="btn-green-left" ></div>
            <div class="btn-green-middle">{#str_ButtonClose#}</div>
            <div class="btn-green-right"></div>
        </div>
    </div>
    <div class="clear"></div>
</div>