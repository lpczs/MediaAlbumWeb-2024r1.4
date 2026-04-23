<div id="contentScrollForgotPassword" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">

			{#str_LabelForgottenPassword#}

		</div> <!-- pageLabel -->

		<div id="contentHolder">
			<div id="changePasswordForm" class="outerBox outerBoxPadding">

			<div class="top_gap">

				<div class="formLine1">
					<label for="newpassword">{#str_LabelNewPassword#}: </label>
					<img src="{$brandroot}/images/asterisk.png" alt="*"/>
				</div>

				<div class="formLine2">
                    <div class="password-input-wrap">
                        <input type="password" id="newpassword" name="newpassword" value="" class="middle" data-decorator="fnHandlePasswordStrength" />
						<button type="button" id="togglenewpassword" class="password-visibility password-show"></button>
                        <img class="error_form_image" id="newpasswordcompulsory" src="{$brandroot}/images/asterisk.png" alt="*" />
                        <div class="progress-wrap">
                            <progress id="strengthvalue" value="0" min="0" max="5"></progress>
                            <p>{#str_LabelPasswordStrength#}: <span id="strengthtext">{#str_LabelStartTyping#}</span></p>
                        </div>
                    </div>
					<div class="clear"></div>
				</div>

			</div>

		</div> <!-- outerBox outerBoxPadding -->

		<div class="paddingBtnBottomPage">
			<div class="btnAction btnContinue" data-decorator="fnValidateDataEntry">
				<div class="btnContinueContent">{#str_ButtonSaveNewPassword#}</div>
			</div>

		</div>
		</div> <!-- contentHolder -->

    </div> <!-- contentVisible -->

</div> <!-- contentScrollCart -->