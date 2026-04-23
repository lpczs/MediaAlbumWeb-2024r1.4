{if $showaccountbalance == true}
    <div class="side-panel section">
        <h2 class="title-bar title-bar-panel">
            <div class="textIcon">{#str_LabelAccount#}</div>
            <img src="{$brandroot}/images/icons/account_summary_icon.png" alt="" />
            {* <img src="{$accounticon}" alt="" /> *}
            <div class="clear"></div>
        </h2>
        <div class="contentDotted">
            <div class="titleDetailPanel">
                {#str_LabelAccountBalance#}:
            </div>
            <div class="sidebaraccount_gap priceBold">
                {$accountbalance}
            </div>
            <div class="contentDottedImage"></div>
        </div>
        <div class="content">
            <div class="titleDetailPanel">
                {#str_LabelCreditLimit#}:
            </div>
            <div class="sidebaraccount priceBold">
                {$creditlimit}
            </div>
        </div>
    </div>
{/if}
{if $showgiftcardsbalance == true}
<div class="side-panel section">
    <h2 class="title-bar title-bar-panel">
        <div class="textIcon">{#str_LabelGiftCardBalance#}</div>
        <img src="{$webroot}/images/icons/gift_card_icon.png" alt="" />
        <div class="clear"></div>
    </h2>
    <div class="contentDotted">
        <div class="sidebaraccount_gap priceBold">
            {$giftcardbalance}
        </div>
        <div class="contentDottedImage"></div>
    </div>
    <div class="content">
        <div class="titleDetailPanel">
            {#str_LabelGiftCardRedeemText#}
        </div>
    </div>
    <div class="content">
        <div class="sidebaraccount_gap">
            <input type="text" id="giftcardid" class="inputGiftCard" name="giftcardid" placeholder="{#str_LabelEnterCode#}" />
        </div>
        <div class="align-right">
            <div class="contentBtn" id="setGiftCardButton">
                <div class="btn-green-left" ></div>
                <div class="btn-green-middle">{#str_LabelRedeem#}</div>
                <div class="btn-green-arrow-right"></div>
            </div>
        </div>
    </div>
</div>
{/if}