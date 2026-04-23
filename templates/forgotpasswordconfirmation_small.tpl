<div id="contentScrollForgotPassworConfirmation" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">
            {#str_LabelForgottenPassword#}
        </div>

        <div class="outerBox outerBoxPadding">

            <p>
                {#str_ConfirmationResetPassword#}
            </p>
			 {if $resetpasswordauthcode != 0}
			 <div class="highlightbox">
				<p>
					{#str_ResetAuthCodeMessage#}
				</p>
				<p class="highlighttext">
					{$resetpasswordauthcode}
				</p>
			</div>
			{/if}
            <p class="note">
                {#str_ConfirmationResetMessage#}
            </p>

        </div> <!-- outerBox outerBoxPadding -->

    </div> <!-- contentVisible -->

</div> <!-- contentScrollCart -->