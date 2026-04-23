<div id="contentCancelation" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">

            {#str_TitleOrderCancellation#}

        </div> <!-- pageLabel -->

        <div class="orderInformationBloc outerBox outerBoxPadding">

            <div>
                {#str_MessageOrderCancellation1#}
            </div>

        </div> <!-- orderInformationBloc outerBox outerBoxPadding -->

    {if $mainwebsiteurl != ''}

        <div class="buttonBottomSection">

            <div data-decorator="fnRedirect" data-url="{$mainwebsiteurl|escape}">
                <div class="btnAction btnContinue">
                    <div class="btnContinueContent">{#str_ButtonContinue#}</div>
                </div>
            </div>

        </div> <!-- buttonBottomSection -->

        <div class="clear"></div>

    {/if}{* end {if $mainwebsiteurl != ''} *}

    </div> <!-- contentVisible -->

</div> <!-- contentScrollCart -->