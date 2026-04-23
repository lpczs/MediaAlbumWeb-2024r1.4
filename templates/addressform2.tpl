{if $useraddressupdated != 2}
<div id="logintable" class="blocAccount outerBox">
	<h2 class="title-bar outerBoxPadding" id="logintitle">
        {#str_LabelLoginInformation#}
    </h2>
    <div class="currentBloc outerBoxPadding" id="useremail">
        <div>
{if $showusernameinput == 1}
            <div class="formLine1">
                <label for="login">{#str_LabelUserName#}:</label>
    {if $strictmode == '1'}
                <img src="{$brandroot}/images/asterisk.png" alt="*" />
    {/if}
             </div>
            <div class="formLine2">
                <input type="text" id="login" name="login" value="{$login}" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"/>
    {if $strictmode == '1'}
                <img id="logincompulsory" class="error_form_image" src="{$brandroot}/images/asterisk.png" alt="*" />
    {/if}
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="top_gap">
{/if}
            <div class="formLine1">
                <label for="email">{#str_LabelEmailAddress#}:</label>
{if $strictmode == '1'}
                <img src="{$brandroot}/images/asterisk.png" alt="*" />
{/if}
            </div>
            <div class="formLine2">
                <input type="email" id="email" name="email" value="{$email}" autocorrect="off" autocapitalize="off" spellcheck="false" />
{if $strictmode == '1'}
                <img id="emailcompulsory" class="error_form_image" src="{$brandroot}/images/asterisk.png" alt="*" />
{/if}
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
        <div id="loginpassword">
            <div class="top_gap">
                <div class="formLine1">
                    <label for="password">{#str_LabelPassword#}:</label>
{if $strictmode == '1'}
                    <img src="{$brandroot}/images/asterisk.png" alt="*" />
{/if}
            </div>
            <div class="formLine2">
				<div class="password-input-wrap">
                    <div class="password-background">
                        <input type="password" id="password" name="password" value="" data-decorator="fnHandlePasswordStrength" />
                        <button type="button" id="togglepassword" class="password-visibility password-show"></button>
                    </div>
					<img class="error_form_image" id="newpasswordcompulsory" src="{$brandroot}/images/asterisk.png" alt="*" />
					<div class="progress-wrap">
						<progress id="strengthvalue" value="0" min="0" max="5"></progress>
						<p>{#str_LabelPasswordStrength#}: <span id="strengthtext">{#str_LabelStartTyping#}</span></p>
					</div>
				</div>
{if $strictmode == '1'}
                    <img id="passwordcompulsory" class="error_form_image" src="{$brandroot}/images/asterisk.png" alt="*" />
{/if}
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>
{/if}
<div class="blocAccount outerBox outerBoxMarginTop" id="addressBlocFirst">
	{if $useraddressupdated != 2}
    <h2 class="title-bar outerBoxPadding" id="blocTitle">
        {$addresstitle}
    </h2>
	{/if}
    <div class="currentBloc outerBoxPadding" id="blocContent">
        <div id="ajaxdiv" name="ajaxdiv"></div>
        <div id="contacttable">
            <div class="top_gap">
                <div class="formLine1">
                    <label for="telephonenumber">{#str_LabelTelephoneNumber#}:</label>
 {if $strictmode == '1'}
                    <img src="{$brandroot}/images/asterisk.png" alt="*" />
{/if}
                </div>
                <div class="formLine2">
                    <input type="tel" id="telephonenumber" name="telephonenumber" value="{$telephonenumber}" data-decorator="fnCJKHalfWidthFullWidthToASCII" data-force-uppercase="false" autocorrect="off" autocapitalize="off" spellcheck="false" />
{if $strictmode == '1'}
                    <img id="telephonenumbercompulsory" class="error_form_image" src="{$brandroot}/images/asterisk.png" alt="*" />
{/if}
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>