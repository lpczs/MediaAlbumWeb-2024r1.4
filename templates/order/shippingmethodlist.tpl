<div id="methodBack" class="contentNavigation">

        <div class="btnDoneTop" id="contentNavigationMethodList" data-decorator="fnSetHashUrl" data-hash-url="shipping">
            <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone">{#str_ButtonDone#}</div>
            <div class="clear"></div>
        </div>

    </div>

    <!-- SHIPPING METHOD LIST -->

    <div id="contentRightScrollMethodList" class="contentScrollCart">

        <div class="contentVisible">

            <div class="pageLabel">
                {#str_LabelShippingMethod#}
            </div>

            <div class="outerBox shippingMethodBox">
                <div class="shippingLabelSelectMethod outerBoxPadding">
                    {#str_LabelSelectShippingMethod#}
                </div>
                <ul id="shippingMethodsList">
                    {$shippingmethodslist}
                </ul>
            </div>

        </div> <!-- contentVisible -->

    </div> <!-- contentScrollCart -->
