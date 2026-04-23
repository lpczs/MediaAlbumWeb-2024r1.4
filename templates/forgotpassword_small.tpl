<div id="contentNavigationForgotPassword" class="contentNavigation">

    <div class="btnDoneTop" data-decorator="fnSetHashUrl" data-hash-url=''>
        <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
        <div class="btnDone">{#str_ButtonCancel#}</div>
        <div class="clear"></div>
    </div>

</div> <!-- contentNavigationShare -->

<div id="contentScrollForgotPassword" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">
            {#str_LabelForgotPassword#}
        </div>

        <div class="outerBox outerBoxPadding">

            <div class="informationText">
                {#str_TextResetPassword#}
            </div>

            <div>

                <div class="formLine1">
                    <label for="loginForgotPassword">{#str_LabelEmailorUsername#}:</label>
                    <img class="valign-center" src="{$brandroot}/images/asterisk.png" alt="*"/>
                </div>

                <div class="formLine2">
                    <input type="text" id="loginForgotPassword" name="loginForgotPassword" value="" class="middle" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"/>
                </div>

                <div class="clear"></div>
            </div>

			<input type="hidden" id="passwordresetrequesttoken" name="passwordresetrequesttoken" value="{$passwordresetrequesttoken}" />
        </div> <!-- outerBox outerBoxPadding -->

        <div class="paddingBtnBottomPage">

            <div class="btnAction btnContinue" data-decorator="validateDataEntryForgotPassword">
                <div class="btnContinueContent">{#str_ButtonSendResetLink#}</div>
            </div>

        </div>

    </div> <!-- contentVisible -->

</div> <!-- contentScrollCart -->