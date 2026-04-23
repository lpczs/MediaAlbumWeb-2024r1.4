<div id="contentCancelation" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">

             {#str_LabelPayLaterRef#}

        </div> <!-- pageLabel -->

        <div class="orderInformationBloc outerBox outerBoxPadding outerBoxPadding">

            <div>
                {$str_MessagePayLaterCancellation}
            </div>

        </div> <!-- orderInformationBloc outerBox outerBoxPadding outerBoxPadding -->

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