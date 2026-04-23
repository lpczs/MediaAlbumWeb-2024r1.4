<div id="dialogTop" class="dialogTop">
    <div class="dialogTitle">
        {#str_LabelStoreDetails#}
    </div>
</div>
<div>
    <div id="dialogContent" class="dialogContent">
        <div>
            <div class="labelTitle">
                {$storename}
            </diV>
            {$storedetails}
        </div>

        {if $telephonenumber != ''}

            <div class="topGap">
                <div class="labelTitle">
                    {#str_LabelTelephoneNumber#}
                </div>
                {$telephonenumber}
            </div>

        {/if}

        {if $emailaddress != ''}

            <div class="topGap">
                <div class="labelTitle">
                    {#str_LabelEmailAddress#}
                </div>
                <a href="mailto:{$emailaddress}">
                    {$emailaddress}
                </a>
            </div>

        {/if}

        {if $storeurl != ''}

            <div class="topGap">
                <div class="labelTitle">
                    {#str_LabelWebSite#}
                </div>
                <a href="{$storeurl}" target="blank">
                    {$storeurl}
                </a>
            </div>

        {/if}

        {if $information != ''}

            <div class="topGap">
                <div class="labelTitle">
                    {#str_LabelAdditionalInformation#}
                </div>
                {$information}
            </div>

        {/if}

        <div class="topGap">
            <div class="labelTitle">
                {#str_LabelOpeningTimes#}
            </div>

            {if $storeopeningtimes != ''}

                {$storeopeningtimes}
            {else}

                {#str_NoInformation#}

            {/if}

        </div>
    </div>
    <div id="dialogBtn" class="btnRightSection btnInside" data-decorator="closeDialog">
        <div class="btnAction btnAccept">
            <div class="btnConfirmTickLeftImage"></div>
        </div>
        <div class="clear"></div>
    </div>
</div>