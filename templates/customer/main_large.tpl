<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="csrf-token" content="{csrf_token}" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {#str_TitleMyAccount#}</title>
        
        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}
        
        {include file="includes/customerinclude_large.tpl"}

         <script type="text/javascript" id="mainjavascript" {$nonce}>
        //<![CDATA[

            {include file="customer/main.tpl"}

         //]]>
        </script>

    </head>
    <body id="body">
        <div id="shim">&nbsp;</div>

		<div id="redactConfirmBox" class="section maw_dialog">
			<div class="dialogTop">
				<h2 class="title-bar" id="redactAccountTitle">{#str_TitleConfirmation#}</h2>
			</div>

			<div class="content confirmationBoxContent">
				<div id="redactConfirmBoxText" class="message"></div>
				<div id="buttonsHolderRefactor" class="buttonBottomInside">
					<div class="btnLeft">
						<div class="contentBtn" id="closeRedactionConfirmButton">
							<div class="btn-red-cross-left" ></div>
							<div class="btn-red-middle">{#str_ButtonNo#}</div>
							<div class="btn-red-right"></div>
						</div>
					</div>
					<div class="btnRight">
						<div class="contentBtn" id="confirmRedactionButton">
							<div class="btn-green-left" ></div>
							<div class="btn-green-middle">{#str_ButtonYes#}</div>
							<div class="btn-accept-right"></div>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>


		{if $section=='yourorders'}

            <div id="confirmationBox" class="section">
                <div class="dialogTop">
                    <h2 class="title-bar">
                        {#str_LabelConfirmation#}
                    </h2>
                </div>
                <div class="content confirmationBoxContent">
                    <div id="confirmationBoxText" class="message"></div>
                    <div id="buttonsHolderConfirmation" class="buttonBottomInside">
                        <div class="btnRight">
                            <div class="contentBtn closeConfirmationContainer">
                                <div class="btn-green-left" ></div>
                                <div class="btn-accept-right"></div>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div id="buttonsHolderQuestion" class="buttonBottomInside">
                        <div class="btnLeft">
                            <div class="contentBtn closeConfirmationContainer">
                                <div class="btn-blue-left" ></div>
                                <div class="btn-blue-right"></div>
                            </div>
                        </div>
                        <div class="btnRight">
                            <div class="contentBtn unshareConfirmContainer">
                                <div class="btn-green-left" ></div>
                                <div class="btn-green-middle">{#str_LabelUnshare#}</div>
                                <div class="btn-accept-right"></div>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
            <div id="dialogBox" class="section">
                <div class="dialogTop">
                    <h2 class="title-bar" id="shareProjectTitle"></h2>
                </div>
				<div class="dialogContentContainer">
					<div class="content">
						<div class="lessPaddingTop" id="shareMethodsTitle">
							<h2 class="title-bar-inside">
								{#str_MessageHowWouldYouLikeToShare#}
							</h2>
							<div class="shareContent">
								<div id="shareMethodsHolder" class="shareContentLeft">
									<input type="radio" name="shareMethod" id="shareMethodsSocial" checked="checked" value="social" />
									<label for="shareMethodsSocial">
										<img src="{$brandroot}/images/icons/share_via_social.png" alt="Social Media" />
									</label><br /><br />
									{if $sharebyemailmethod > 0}
									<input type="radio" name="shareMethod" id="shareMethodsEmail" value="email"/>
									<label for="shareMethodsEmail">
										<img src="{$brandroot}/images/icons/share_via_email.png" alt="Email" />
									</label>
									{/if}
								</div>
								<div id="prefiewPasswordHolder" class="shareContentRight">
									<div class="passwordProtectionCheckBoxBloc">
										<input type="checkbox" id="sharepassword" name="sharepassword" />
										<label for="sharepassword">
											{#str_LabelSharePasswordProtection#}
										</label>
									</div>
									<div class="passwordProtectionBloc">
										<label for="previewPassword">
											{#str_LabelSharePassword#}:
										</label>
										<img id="previewPasswordcompulsory2" class="imgMessage" src="{$brandroot}/images/asterisk.png" alt="*"/>
										<div class="password-input-wrap">
                                            <div class="password-background">
                                                <input id="previewPassword" name="previewPassword" type="password" disabled="disabled" />
                                                <button id="togglepreviewpassword" class="password-visibility password-show"></button>
                                            </div>
                                        </div>
                                        <img id="previewPasswordcompulsory" class="imgMessage error_form_image" src="{$brandroot}/images/asterisk.png" alt="*"/>
										<div class="clear"></div>
									</div>
									<div class="clear"></div>
								</div>
								<div class="clear"></div>
							</div>
						</div>
						<div id="shareMethods">
							<div class="lessPaddingTop">
								<h2 class="title-bar-inside">
									{#str_MessageSelectAService#}
								</h2>
								<div id="a2a_menu_container">
									<div class="clear"></div>
								</div>
								<script type="text/javascript" src="{$serverprotocol}static.addtoany.com/menu/page.js" {$nonce}></script>
							</div>
						</div>
						<div id="shareEmail">
							<div class="lessPaddingTop shareBlocEmail">
								<h2 class="title-bar-inside">
									{#str_LabelByEmail#}
								</h2>
								<form id="popupBox2Form" method="post" action="#" >
									<div>
										<label for="shareByEmailTitle">
											{#str_LabelMessageTitle#}
										</label>
										<img src="{$brandroot}/images/asterisk.png" alt="*"/>
										<input type="text" id="shareByEmailTitle" name="shareByEmailTitle"/>
										<img id="shareByEmailTitlecompulsory" class="error_form_image" src="{$brandroot}/images/asterisk.png" alt="*"/>
										<div class="clear"></div>
									</div>
									{if $sharebyemailmethod == 1}
										<div class="top_gap">
											<label for="shareByEmailTo">
												{#str_LabelShareWithEmails#}
											</label>
											<img src="{$brandroot}/images/asterisk.png" alt="*"/>
											<textarea id="shareByEmailTo" name="shareByEmailTo" cols="50" rows="2" class="shareByEmailToTextarea"></textarea>
											<div class="clear"></div>
										</div>
										<div class="top_gap">
											<label for="shareByEmailText">
												{#str_LabelShareMessageText#}
											</label>
											<div class="gap-label-mandatory"></div>
											<textarea id="shareByEmailText" name="shareByEmailText" class="shareByEmailTextTextarea" cols="50" rows="5"></textarea>
											<div class="clear"></div>
										</div>
									{else}
										<div class="top_gap">
											<label for="shareByEmailTo">
												{#str_LabelShareWithEmail#}
											</label>
											<img src="{$brandroot}/images/asterisk.png" alt="*"/>
											<input type="text" id="shareByEmailTo" name="shareByEmailTo" class="shareByEmailToInput"/>
											<div class="clear"></div>
										</div>
										<div class="top_gap">
											<label for="shareByEmailText">
												{#str_LabelShareMessageText#}
											</label>
											<div class="gap-label-mandatory"></div>
											<textarea id="shareByEmailText" name="shareByEmailText" class="shareByEmailTextInput" cols="50" rows="5"></textarea>
											<div class="clear"></div>
										</div>
									{/if}
								</form>
								<div class="note">
									<img src="{$brandroot}/images/asterisk.png" alt="*" />
									{#str_LabelCompulsoryFields#}
								</div>
							</div>
						</div>
                    </div>
				</div>
				<div class="buttonShare">
					<div class="btnLeft">
						<div class="contentBtn closeConfirmationContainer">
							<div class="btn-red-cross-left" ></div>
							<div class="btn-red-middle">{#str_ButtonCancel#}</div>
							<div class="btn-red-right"></div>
						</div>
					</div>
					<div class="btnRight">
						<div class="contentBtn" id="shareByEmailBtn">
							<div class="btn-green-left" ></div>
							<div class="btn-green-middle">{#str_LabelShare#}</div>
							<div class="btn-accept-right"></div>
						</div>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
				</div>
            </div>

            <div id="browserConfirmBox" class="browserConfirm">
                <div class="dialogTop">
                    <h2 class="title-bar" id="redactAccountTitle">{#str_LabelBrowserCompatibilityIssue#}</h2>
                </div>
                <div class="browserConfirmContent">{#str_ErrorBrowserCompatibilityIssue#}</div>
                <div class="btnRight">
                    <div class="browserConfirmContentBtn" id="browserConfirmContentBtn">
                        <div class="btn-green-left" ></div>
                        <div class="btn-green-middle">{#str_ButtonOk#}</div>
                        <div class="btn-accept-right"></div>
                    </div>
                </div>
                <div class="clear"></div>
                </div>
            </div>

        {else}
            {if $section=='accountdetails'}
                {include file="$simpleDialog"}
            {/if}

            <div id="confirmationBox" class="section">
                <div class="dialogTop">
                    <h2 class="title-bar">
                        {#str_TitleError#}
                    </h2>
                </div>
                <div class="content confirmationBoxContent">
                    <div id="confirmationBoxText"></div>
                    <div id="buttonsHolderConfirmation" class="buttonBottomInside">
                        <div class="btnRight">
                            <div class="contentBtn" id="confirmationBoxAcceptButton">
                                <div class="btn-green-left" ></div>
                                <div class="btn-accept-right"></div>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>

        {/if}

        <div id="outerPage" class="outer-page{if $sidebarleft != ''} fullsize-outer-page{/if}">
            <div id="header" class="headertop headerScroll">
                <div class="headerinside">
                    {include file="$header"}
                </div>
                <div class="clear"></div>
            </div>
            <div class="contentNavigation">
                <div class="contentTextNavigation{if $sidebarleft != ''} contentNavigationMargin {/if}">

                    {if $section == 'menu'}

                        <div class="current-item">
                            <img src="{$brandroot}/images/icons/home_icon_v2.png" alt="" />
                            {#str_TitleMyAccount#} - {$userdisplayname}
                        </div>

                    {else}

                        <a href="#home" id="homeLink">
                            <img src="{$brandroot}/images/icons/home_icon_v2.png" alt="" />
                            {#str_TitleMyAccount#} - {$userdisplayname}
                        </a>
                        <img src="{$brandroot}/images/icons/breadcrumb_icon.png" alt="" class="separator" />
                        <span class="current-item">

                            {if $section=='accountdetails'}
                                {#str_MenuTitleAccountDetails#}
                            {/if}

                            {if $section=='changepassword'}
                                {#str_MenuTitleChangePassword#}
                            {/if}

                            {if $section=='changepreferences'}
                                {#str_MenuTitleChangePreferences#}
                            {/if}

                            {if $section=='existingonlineprojects'}
                                {#str_MenuTitleOnlineProjects#}
                            {/if}

                            {if $section == 'yourorders'}
                                {$title}
                            {/if}

                        </span>

                    {/if}
                </div>
                <div class="btnLogOut" id="logoutButton">
                    <form id="headerform" method="post" action="#">
                        <input type="hidden" id="header_ref" name="ref" value="{$session}" />
                        <input type="hidden" id="header_ssotoken" name="ssotoken" value="{$ssotoken}" />
                        <input type="hidden" id="header_fsaction" name="fsaction" value="{$logoutfsaction}" />
                        <input type="hidden" id="header_basketref" name="basketref" value="{$basketref}" />
                        <input type="hidden" id="header_webbrandcode" name="webbrandcode" value="{$webbrandcode}" />
                        <input type="hidden" id="header_csrf_token"  name="csrf_token" value="{csrf_token}" />
                    </form>
                    <span>
                        {#str_LabelSignOut#}
                    </span>
                    <img src="{$brandroot}/images/icons/sign_out_icon_v2.png" alt="" />
                </div>
                <div class="clear"></div>
            </div>
            <div id="contentScroll" class="contentScroll">
                {if $sidebarleft != ''}
                    {include file="$sidebarleft"}
                {/if}
                <div id="contentHolder">
                    {if $hasflaggedonlineprojects}
                        <div id="flaggedForPurge" class="warning-bar">{#str_MessageMainPurgeWarning#} <a href="#" id="viewOnlineProjects" data-decorator="fnVisitCheckProjects" data-link="{$checkprojectslink}" data-internal="{$hasonlinedesignerurl}">{#str_TitlePurgeWarningLink#}</a></div>
                    {/if}
                    <!-- ACCOUNT DETAILS -->
                    {if $section=='accountdetails'}
					<div id="loadingBox" class="section maw_dialog">
                            <div class="dialogTop">
                                <h2 id="loadingTitle" class="title-bar"></h2>
                            </div>
                            <div class="content">
                                <div class="loadingMessage">
                                    <img src="{$brandroot}/images/loading_shoppingcart_v2.gif" class="loading-icon" alt="{#str_MessageLoading#}" />
                                </div>
                            </div>
                        </div>
                        <div id="shimLoading">&nbsp;</div>
                        <div id="pageFooterHolder" {if $sidebaraccount == '' && $sidebarcontactdetails == ''}class="fullsizepage"{/if}>
                            <div id="page" class="section">
                                <h2 class="title-bar">
                                    {#str_MenuTitleAccountDetails#}
                                </h2>
                                <form method="post" id="mainform" name="mainform" action="#">
                                    <div class="content contentForm">
                                        <div class="message{if $isConfirmation==1} confirmation{/if}" id="message">{$message}</div>
                                        <div class="top_gap account-section">
                                            <label for="email">{#str_LabelEmailAddress#}:</label>
                                            <img src="{$brandroot}/images/asterisk.png" alt="*"/>
                                            <input type="text" id="email_account" name="email_account" value="{$email}" />
                                            <img class="error_form_image" id="emailcompulsory" src="{$brandroot}/images/asterisk.png" alt="*"/>
                                            <div class="clear"></div>

											{if $showPendingMessage==1}
												<div class="informationContainer">
													<p class="informationHeader">{#str_TitleEmailChangePending#}</p>
													<p class="informationMessage">{#str_MessageEmailChangePending#}</p>
												</div>
											{/if}
                                        </div>
                                        <div id="ajaxdiv" class="top_gap"></div>
                                        <div class="top_gap">
                                            <label for="telephonenumber">{#str_LabelTelephoneNumber#}:</label>
                                            <img src="{$brandroot}/images/asterisk.png" alt="*"/>
                                            <input type="text" id="telephonenumber_account" name="telephonenumber_account" value="{$telephonenumber}" />
                                            <img class="error_form_image" id="telephonenumbercompulsory" src="{$brandroot}/images/asterisk.png" alt="*"/>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="note">
                                            <img src="{$brandroot}/images/asterisk.png" alt="*" />
                                            {#str_LabelCompulsoryFields#}
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="buttonBottom">
                            {if $addressupdated != 0}
                            <div class="btnLeft">
                                <div class="contentBtn" id="backButton">
                                    <div class="btn-blue-arrow-left" ></div>
                                    <div class="btn-blue-middle">{#str_ButtonBack#}</div>
                                    <div class="btn-blue-right"></div>
                                </div>
                            </div>
                        	{/if}
                            <div class="btnRight">
                                <div class="contentBtn" id="updateButton">
                                    <div class="btn-green-left" ></div>
                                    <div class="btn-green-middle">{#str_ButtonUpdate#}</div>
                                    <div class="btn-accept-right"></div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    {/if}
                    <!-- END ACCOUNT DETAILS -->

                    <!-- CHANGE PASSWORD -->
                    {if $section=='changepassword'}
                        <div id="pageFooterHolder" {if $sidebaraccount == '' && $sidebarcontactdetails == ''}class="fullsizepage"{/if}>
                            <div id="page" class="section">
                                <h2 class="title-bar">
                                    {#str_MenuTitleChangePassword#}
                                </h2>
                                <form action="#" method="post" id="changePassword">
                                    <div class="contentForm content">
                                        <div class="message{if $isConfirmation==1} confirmation{/if}" id="message">
                                            {$message}
                                        </div>
                                        <div>
                                            <label for="oldpassword">{#str_LabelCurrentPassword#}:</label>
                                            <img src="{$brandroot}/images/asterisk.png" alt="*"/>
                                            <input type="password" id="oldpassword" name="oldpassword" value="" class="middle" />
                                            <div class="clear"></div>
                                        </div>
                                        <div class="top_gap">
                                            <label for="newpassword">{#str_LabelNewPassword#}: </label>
                                            <img src="{$brandroot}/images/asterisk.png" alt="*"/>
                                            <div class="password-input-wrap">
                                                <div class="password-background">
                                                    <input type="password" id="newpassword" name="newpassword" value="" class="middle" />
                                                    <button type="button" id="togglenewpassword" class="password-visibility password-show"></button>
                                                </div>
                                                <div class="progress-wrap">
                                                    <progress id="strengthvalue" value="0" min="0" max="5"></progress>
                                                    <p>{#str_LabelPasswordStrength#}: <span id="strengthtext">{#str_LabelStartTyping#}</span></p>
                                                </div>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="note">
                                            <img src="{$brandroot}/images/asterisk.png" alt="*" />
                                            {#str_LabelCompulsoryFields#}
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="buttonBottom">
                            <div class="btnLeft">
                                <div class="contentBtn" id="backButton">
                                    <div class="btn-blue-arrow-left" ></div>
                                    <div class="btn-blue-middle">{#str_ButtonBack#}</div>
                                    <div class="btn-blue-right"></div>
                                </div>
                            </div>
                            <div class="btnRight">
                                <div class="contentBtn" id="updateButton">
                                    <div class="btn-green-left" ></div>
                                    <div class="btn-green-middle">{#str_ButtonUpdate#}</div>
                                    <div class="btn-accept-right"></div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    {/if}
                    <!-- END CHANGE PASSWORD -->

                    <!-- CHANGE PREFERENCES -->
                    {if $section=='changepreferences'}
                        <div id="pageFooterHolder" {if $sidebaraccount == '' && $sidebarcontactdetails == ''}class="fullsizepage"{/if}>
                            <div id="page" class="section">
                                <h2 class="title-bar">
                                    {#str_MenuTitleChangePreferences#}
                                </h2>
                                <form id="changePreferencesForm">
                                    <div class="content contentForm log-in-wrap update-preferences">
                                        <div class="message{if $isConfirmation==1} confirmation{/if}" id="message">
                                            {$message}
                                        </div>
                                        <div class="top_gap">
                                            <input type="checkbox" name="sendmarketinginfo" id="subscribed" class="widthAuto" {if $sendmarketinginfo == 1} checked="checked"{/if} />
                                            <label class="widthAuto" for="subscribed">
                                                {#str_LabelMarketingSubscribe#}
                                            </label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="buttonBottom">
                            <div class="btnLeft">
                                <div class="contentBtn" id="backButton">
                                    <div class="btn-blue-arrow-left" ></div>
                                    <div class="btn-blue-middle">{#str_ButtonBack#}</div>
                                    <div class="btn-blue-right"></div>
                                </div>
                            </div>
                            <div class="btnRight">
                                <div class="contentBtn" id="updateButton">
                                    <div class="btn-green-left" ></div>
                                    <div class="btn-green-middle">{#str_ButtonUpdate#}</div>
                                    <div class="btn-accept-right"></div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>


                    {/if}
                    <!-- END CHANGE PREFERENCES -->

					{if $section=='yourorders' || $section=='existingonlineprojects'}

						<div id="loadingBox" class="section maw_dialog">
                            <div class="dialogTop">
                                <h2 id="loadingTitle" class="title-bar"></h2>
                            </div>
                            <div class="content">
                                <div class="loadingMessage">
                                    <img src="{$brandroot}/images/loading_shoppingcart_v2.gif" class="loading-icon" alt="{#str_MessageLoading#}" />
                                </div>
                            </div>
                        </div>
                        <div id="shimLoading">&nbsp;</div>

						<div id="dialogBoxOnlineAction" class="section maw_dialog">
							<div class="dialogTop">
								<h2 class="title-bar" id="renameProjectTitle"></h2>
							</div>
							<div class="content">
								<input type="hidden" id="projectrefhidden" value = "" />
								<input type="hidden" id="projectnamehidden" value = "" />
								<input type="hidden" id="projectworkflowtype" value = "" />
								<input type="hidden" id="productindent" value = "" />
								<input type="hidden" id="projectstatus" value = "" />
								<input type="hidden" id="tzoffset" value = "{$tzoffset}" />

								<div class="projectname_container" id="projectname_container"></div>

								<div class="buttonShare">
									<div class="btnLeft">
										<div class="contentBtn" id="projectcancelbutton">

										</div>
									</div>
									<div class="btnRight">
										<div class="contentBtn" id="projectacceptbutton">

										</div>
										<div class="clear"></div>
									</div>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					{/if}

                    <!-- YOUR ORDERS -->
                    {if $section=='yourorders'}
                        <div id="pageFooterHolder" {if $sidebaraccount == '' && $sidebarcontactdetails == ''}class="fullsizepage"{/if}>
                            <div id="page" class="section">
                                <div class="title-bar">
                                    <div class="title-current">{#str_TitleItem#}</div>
                                    <div class="title-status">{#str_LabelStatus#}</div>
                                    <div class="title-price">{#str_LabelHeaderPrice#}</div>
                                    <div class="clear"></div>
                                </div>
                                {if $tempordercount == 0}
                                    <div class="content contentNoPaddingSide" id="content">
                                    {else}
                                        <div class="content" id="content">
                                            {if $sectiontitle != ''}
                                                <h2 class="title-bar-current warning-status">
                                                    {$sectiontitle}
                                                </h2>
                                                <div class="currentBlocRow">
                                                {/if}
                                                {foreach from=$temporderlist item=row name=orders}
                                                    <div id="{$row.product[indexProduct].projectref}" class="contentRow{if $smarty.foreach.orders.last} noBorder{/if}">
                                                        {section name=productloop loop=$row.product|@sizeof step=3}
                                                            <div class="bloc_content">
                                                                {section name=indexProduct start=$smarty.section.productloop.index loop=$row.product step=1 max=3}
                                                                    <div class="previewHolder">
                                                                        <div class="previewItem" orderid="{$row.product[indexProduct].id}">
                                                                            <div id="img_{$row.product[indexProduct].projectref}" class="product-preview-wrap">
                                                                                {assign var='thumbnailpath' value=''}
                                                                                {if $row.product[indexProduct].previewimage !== ''}
                                                                                    {assign var='thumbnailpath' value="{$onlinedesignerurl}{$row.product[indexProduct].previewimage|escape}"}
                                                                                {/if}

                                                                                {if $row.product[indexProduct].projectpreviewthumbnail != ''}
                                                                                    <img class="product-preview-image" src="{$row.product[indexProduct].projectpreviewthumbnail|escape}" data-asset="{$thumbnailpath}" alt="" />
                                                                                {else if $row.product[indexProduct].thumbnailpath != ''}
                                                                                    <img class="product-preview-image" src="{$thumbnailpath}" data-asset="" alt="" />
                                                                                {else}
                                                                                    <img class="product-preview-image" src="{$brandroot}/images/no_image-2x.jpg" alt="" />
                                                                                {/if}
                                                                            </div>
                                                                            <div class="previewItemText">
                                                                                <div class="textProduct">
                                                                                    {$row.product[indexProduct].projectname}
                                                                                </div>
                                                                                <div class="contentDescription">
                                                                                    <div class="description-product">
                                                                                        {$row.product[indexProduct].productname}
                                                                                    </div>
                                                                                    <div class="ordernumber">
                                                                                        <span class="label-order-number">{#str_LabelOrderNum#}:</span> {$row.ordernumber}
                                                                                    </div>
                                                                                    <div class="ordernumber">
                                                                                        <span class="label-order-number">{#str_LabelOrderDate#}:</span> {$row.formattedorderdate}
                                                                                    </div>
                                                                                </div>
                                                                                <div class="descriptionStatus">
                                                                                    {if $row.product[indexProduct].status == 0 && $row.product[indexProduct].source == 0}
                                                                                        <span class="previewItemDetail textRed">
                                                                                            {#str_LabelStatusWaitingForFiles#}
                                                                                        </span>
                                                                                    {elseif $row.product[indexProduct].status == 0 && $row.product[indexProduct].source == 1}
                                                                                        <span class="previewItemDetail textRed">
                                                                                            {#str_LabelStatusWaitingForPayment#}
                                                                                        </span>
                                                                                    {elseif $row.product[indexProduct].status < 60}
                                                                                        <span class="previewItemDetail textRed">
                                                                                            {#str_LabelStatusWaitingForPayment#}
                                                                                        </span>
                                                                                    {/if}
                                                                                </div>
                                                                                <div class="descriptionPrice">

                                                                                    {if $row.product|@sizeof == 1}

                                                                                        {$row.formattedordertotal}

                                                                                    {/if}

                                                                                </div>
                                                                                {if ($row.status > 0) && ($row.product[indexProduct].parentorderitemid == 0)}
                                                                                    <div class="clear"></div>
                                                                                    <div class="btnLinks">
                                                                                        <div id="executePayNowButton" data-ref="{$row.sessionid}" >
                                                                                            <div class="btn-green-left" ></div>
                                                                                            <div class="btn-green-middle">{#str_LabelPayNow#}</div>
                                                                                            <div class="btn-green-right"></div>
                                                                                        </div>
                                                                                        {if $row.product[indexProduct].previewsonline==1}
                                                                                            <div class="browserPreviewButton" data-baseurl="{$row.product[indexProduct].previewurl|escape}" data-ref="{$session}" data-productid="{$row.product[indexProduct].id}" data-ssotoken="{$ssotoken}">
                                                                                                <div class="btn-white-left" ></div>
                                                                                                <div class="btn-white-middle">{#str_ButtonPreview#}</div>
                                                                                                <div class="btn-white-right"></div>
                                                                                            </div>
                                                                                        {/if}
                                                                                    </div>
                                                                                {else}
                                                                                    {if ($row.product[indexProduct].previewsonline==1) && ($row.product[indexProduct].parentorderitemid == 0)}
																						<div class="clear"></div>
                                                                                        <div class="btnLinks">
                                                                                            <div class="browserPreviewButton" data-baseurl="{$row.product[indexProduct].previewurl|escape}" data-ref="{$session}" data-productid="{$row.product[indexProduct].id}" data-ssotoken="{$ssotoken}">
                                                                                                <div class="btn-white-left" ></div>
                                                                                                <div class="btn-white-middle">{#str_ButtonPreview#}</div>
                                                                                                <div class="btn-white-right"></div>
                                                                                            </div>
                                                                                        </div>
                                                                                    {/if}
                                                                                {/if}
                                                                            </div>
                                                                        </div>
                                                                        {if ($row.product|@sizeof != 1) && (($row.product|@sizeof) -1 == $smarty.section.indexProduct.index)}
                                                                            <div class="mulitLineSubTotal">
                                                                                {#str_LabelSubTotalMultiLine#}: {$row.formattedordertotal}
                                                                            </div>
                                                                        {/if}
                                                                    </div>
                                                                {/section}
                                                                <div class="clear"></div>
                                                            </div>
                                                        {/section}
                                                    </div>
                                                {/foreach}
                                                {if $sectiontitle != ''}
                                                </div>
                                            {/if}
                                        {/if}

                                        {if $ordercount > 0}
                                            {if $sectiontitle2 != ''}
                                                <h2 class="title-bar-current">
                                                    {$sectiontitle2}
                                                </h2>
                                                <div class="currentBlocRow noBottom">
                                                {/if}
                                                {foreach from=$orderlist item=row name=orders}
                                                    <div  class="contentRow{if $smarty.foreach.orders.last} noBorder{/if}">
                                                        {section name=productloop loop=$row.product|@sizeof step=3}
                                                            <div class="bloc_content">
                                                                <div class="order-details">
                                                                    <div class="order-number"><span class="label-order-number">{#str_LabelOrderNum#}:</span> {$row.ordernumber}</div>
                                                                    <div class="order-delete">
                                                                        {if $row.orderstatus > 0}
                                                                        <div class="deleteOrderButton" data-orderid="{$row.orderid}" data-ordernumber="{$row.ordernumber}" data-ref="{$session}" data-ssotoken="{$ssotoken}">
                                                                            <div class="btn-red-left" ></div>
                                                                            <div class="btn-red-middle">{#str_ButtonDelete#}</div>
                                                                            <div class="btn-red-right"></div>
                                                                        </div>
                                                                        {/if}
                                                                    </div>
                                                                    <div class="clear"></div>
                                                                </div>
                                                                {section name=indexProduct start=$smarty.section.productloop.index loop=$row.product step=1 max=3}
                                                                    <div class="previewHolder">
                                                                        <div class="previewItem" orderid="{$row.product[indexProduct].id}" id="orderitemid{$row.product[indexProduct].id}" data-projectname="{$row.product[indexProduct].projectname}">
                                                                            <div id="img_{$row.product[indexProduct].projectref}" class="product-preview-wrap">
                                                                                {assign var='thumbnailpath' value=''}
                                                                                {if $row.product[indexProduct].previewimage !== ''}
                                                                                    {assign var='thumbnailpath' value="{$onlinedesignerurl}{$row.product[indexProduct].previewimage|escape}"}
                                                                                {/if}

                                                                                {if $row.product[indexProduct].projectpreviewthumbnail != ''}
                                                                                    <img class="product-preview-image" src="{$row.product[indexProduct].projectpreviewthumbnail|escape}" data-asset="{$thumbnailpath}" alt="" />
                                                                                {else if $row.product[indexProduct].thumbnailpath != ''}
                                                                                    <img class="product-preview-image" src="{$thumbnailpath}" data-asset="" alt="" />
                                                                                {else}
                                                                                    <img class="product-preview-image" src="{$brandroot}/images/no_image-2x.jpg" alt="" />
                                                                                {/if}
                                                                            </div>
                                                                            <div class="previewItemText">
                                                                                <div class="textProduct">
                                                                                    {$row.product[indexProduct].projectname}
                                                                                </div>
                                                                                <div class="contentDescription">
                                                                                    <div class="description-product">
                                                                                        {$row.product[indexProduct].productname}
                                                                                    </div>
                                                                                    <div class="ordernumber">
                                                                                        <span class="label-order-number">{#str_LabelOrderNum#}:</span> {$row.ordernumber}
                                                                                    </div>
                                                                                    <div class="ordernumber">
                                                                                        <span class="label-order-number">{#str_LabelOrderDate#}:</span> {$row.formattedorderdate}
                                                                                    </div>
                                                                                </div>
                                                                                <div class="descriptionStatus">
                                                                                    {if $row.product[indexProduct].orderstatus==0}
                                                                                        {if $row.product[indexProduct].status == 0 && $row.product[indexProduct].source == 0}
                                                                                            <span class="previewItemDetail textRed">
                                                                                                {#str_LabelStatusWaitingForFiles#}
                                                                                            </span>
                                                                                        {elseif $row.product[indexProduct].status == 0 && $row.product[indexProduct].source == 1}
                                                                                            <span class="previewItemDetail textBlue">
                                                                                                {#str_LabelStatusInProduction#}
                                                                                            </span>
                                                                                        {elseif $row.product[indexProduct].status == 60}
                                                                                             <span class="previewItemDetail textGreen">
                                                                                                {#str_LabelStatusShipped#}
                                                                                            </span>
																						{elseif $row.product[indexProduct].status == 65}
                                                                                             <span class="previewItemDetail textGreen">
                                                                                                {#str_LabelStatusReadyToCollectAtStore#}
                                                                                            </span>
																						{elseif $row.product[indexProduct].status == 66}
                                                                                             <span class="previewItemDetail textGreen">
                                                                                                {#str_LabelStatusCompleted#}
                                                                                            </span>																							
                                                                                        {else}
																							<span class="previewItemDetail textBlue">
                                                                                                {#str_LabelStatusInProduction#}
                                                                                            </span>
                                                                                        {/if}
                                                                                    {elseif $row.product[indexProduct].orderstatus == 1}
                                                                                        <span class="previewItemDetail textRed">
                                                                                            {#str_LabelStatusCancelled#}
                                                                                        </span>
                                                                                    {else}
                                                                                        <span class="previewItemDetail textGreen">
                                                                                            {#str_LabelStatusCompleted#}
                                                                                        </span>
                                                                                    {/if}
                                                                                    {if  $row.showpaymentstatus == 1 && $row.product|@sizeof == 1}
                                                                                        {if $row.paymentreceived == 1}
                                                                                            <p class="paymentstatus textGreen">{#str_LabelStatusPaymentReceived#}</p>
                                                                                        {else}
                                                                                            <p class="paymentstatus textOrange">{#str_LabelStatusWaitingForPayment#}</p>
                                                                                    {/if}
                                                                                {/if}
                                                                                </div>
                                                                                <div class="descriptionPrice">
                                                                                    {if $row.product|@sizeof == 1}
                                                                                        {$row.formattedordertotal}
                                                                                    {/if}
                                                                                </div>
                                                                                <div class="clear"></div>
                                                                                <div class="btnLinks">

																					{if ($row.status > 0) && ($row.product[indexProduct].source == 1) && ($row.product[indexProduct].parentorderitemid == 0) && ($row.orderstatus == 0) && ($row.product[indexProduct].canmodify == 1) && ($row.product[indexProduct].isowner == 1)}

																						<div class="continueEditingButton yourOrderActionButton" data-productid="{$row.product[indexProduct].id}" data-buttonaction=3 data-projectname="{$row.product[indexProduct].projectname|escape}" data-webbrandapplicationname="{$webbrandapplicationname|escape}" data-projectref="{$row.product[indexProduct].projectref}" data-workflowtype={$row.product[indexProduct].workflowtype} data-indent="{$row.product[indexProduct].productindent}">
																							<div class="btn-white-left" ></div>
																							<div class="btn-white-middle">{#str_ButtonContinueEditing#}</div>
																							<div class="btn-white-right"></div>
																						</div>

																					{/if}

																					{if ($row.product[indexProduct].parentorderitemid == 0)}
																						{if ((($row.product[indexProduct].source == 1) && ($ishighlevel == 0) && ($row.product[indexProduct].isowner == 1)) || (($row.product[indexProduct].source == 1) && ($ishighlevel == 1) && ($basketref != '') && ($basketref != 'tpxgnbr') && ($row.product[indexProduct].isowner == 1))) && ($row.product[indexProduct].dataavailable == 1)}

																							<div class="duplicateButton yourOrderActionButton" data-productid="{$row.product[indexProduct].id}" data-buttonaction=4 data-projectname="{$row.product[indexProduct].projectname|escape}" data-webbrandapplicationname="{$webbrandapplicationname|escape}" data-projectref="{$row.product[indexProduct].projectref}" data-workflowtype={$row.product[indexProduct].workflowtype} data-indent="{$row.product[indexProduct].productindent}">
																								<div class="btn-white-left" ></div>
																								<div class="btn-white-middle">{#str_ButtonDuplicateProject#}</div>
																								<div class="btn-white-right"></div>
																							</div>

																						{/if}
																					{/if}

                                                                                	<!-- always allow a re-order as long as files have been received and canmodify is not set -->
                                                                                	{if ($row.status > 0) && ($row.product[indexProduct].canreorder == $kCanReorder) && ($row.product[indexProduct].parentorderitemid == 0)}

																						<div class="reorderButton yourOrderActionButton" data-productid="{$row.product[indexProduct].id}" data-buttonaction=1 data-projectname="{$row.product[indexProduct].projectname|escape}" data-webbrandapplicationname="{$webbrandapplicationname|escape}" data-projectref="{$row.product[indexProduct].projectref}" data-workflowtype={$row.product[indexProduct].workflowtype} data-indent="{$row.product[indexProduct].productindent}">
																							<div class="btn-green-left" ></div>
																							<div class="btn-green-middle">{#str_LabelReorder#}</div>
																							<div class="btn-green-right"></div>
																						</div>

																					{/if}
																					<!-- add the actions that can only occur on non cancelled orders -->
                                                                                    {if ($row.orderstatus !=1) && ($row.product[indexProduct].parentorderitemid == 0)}
                                                                                        {if $row.status != 0}
                                                                                            {if $row.product[indexProduct].isShared == true}
                                                                                                <div class="unshareButton yourOrderActionButton" data-productid="{$row.product[indexProduct].id}" data-buttonaction=2 data-projectname="{$row.product[indexProduct].projectname|escape}" data-webbrandapplicationname="{$webbrandapplicationname|escape}" data-projectref="{$row.product[indexProduct].projectref}" data-workflowtype={$row.product[indexProduct].workflowtype} data-indent="{$row.product[indexProduct].productindent}">
                                                                                                    <div class="btn-white-left" ></div>
                                                                                                    <div class="btn-white-middle">{#str_LabelUnshare#}</div>
                                                                                                    <div class="btn-white-right"></div>
                                                                                                </div>
                                                                                            {/if}

                                                                                            {if ($row.product[indexProduct].dataavailable == 1) && ($row.origorderid == 0)}
                                                                                                <div class="shareButton yourOrderActionButton" data-productid="{$row.product[indexProduct].id}" data-buttonaction=0 data-projectname="{$row.product[indexProduct].projectname|escape}" data-webbrandapplicationname="{$webbrandapplicationname|escape}" data-projectref="{$row.product[indexProduct].projectref}" data-workflowtype={$row.product[indexProduct].workflowtype} data-indent="{$row.product[indexProduct].productindent}">
                                                                                                    <div class="btn-white-left" ></div>
                                                                                                    <div class="btn-white-middle">{#str_LabelShare#}</div>
                                                                                                    <div class="btn-white-right"></div>
                                                                                                </div>
                                                                                            {/if}

                                                                                        {elseif $row.product[indexProduct].source == 1}
                                                                                            {if $row.product[indexProduct].isShared == true}
                                                                                                <div class="unshareButton yourOrderActionButton" data-productid="{$row.product[indexProduct].id}" data-buttonaction=2 data-projectname="{$row.product[indexProduct].projectname|escape}" data-webbrandapplicationname="{$webbrandapplicationname|escape}" data-projectref="{$row.product[indexProduct].projectref}" data-workflowtype={$row.product[indexProduct].workflowtype} data-indent="{$row.product[indexProduct].productindent}">
                                                                                                    <div class="btn-white-left" ></div>
                                                                                                    <div class="btn-white-middle">{#str_LabelUnshare#}</div>
                                                                                                    <div class="btn-white-right"></div>
                                                                                                </div>
                                                                                            {/if}
                                                                                            {if ($row.product[indexProduct].dataavailable == 1)}
                                                                                                <div class="shareButton yourOrderActionButton" data-productid="{$row.product[indexProduct].id}" data-buttonaction=0 data-projectname="{$row.product[indexProduct].projectname|escape}" data-webbrandapplicationname="{$webbrandapplicationname|escape}" data-projectref="{$row.product[indexProduct].projectref}" data-workflowtype={$row.product[indexProduct].workflowtype} data-indent="{$row.product[indexProduct].productindent}">
                                                                                                    <div class="btn-white-left" ></div>
                                                                                                    <div class="btn-white-middle">{#str_LabelShare#}</div>
                                                                                                    <div class="btn-white-right"></div>
                                                                                                </div>
                                                                                            {/if}

                                                                                        {/if}

                                                                                        {if $row.product[indexProduct].previewsonline==1}
                                                                                            <div class="browserPreviewButton" data-baseurl="{$row.product[indexProduct].previewurl|escape}" data-ref="{$session}" data-productid="{$row.product[indexProduct].id}" data-ssotoken="{$ssotoken}">
                                                                                                <div class="btn-white-left" ></div>
                                                                                                <div class="btn-white-middle">{#str_ButtonPreview#}</div>
                                                                                                <div class="btn-white-right"></div>
                                                                                            </div>
                                                                                        {/if}
                                                                                    {/if}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        {if ($row.product|@sizeof != 1) && (($row.product|@sizeof) -1 == $smarty.section.indexProduct.index)}
                                                                            <div class="mulitLineSubTotal">
                                                                                {#str_LabelSubTotalMultiLine#}: {$row.formattedordertotal}
                                                                                {if  $row.showpaymentstatus == 1}
                                                                                    {if $row.paymentreceived == 1}
                                                                                        <p class="paymentstatus textGreen">{#str_LabelStatusPaymentReceived#}</p>
                                                                                    {else}
                                                                                        <p class="paymentstatus textOrange">{#str_LabelStatusWaitingForPayment#}</p>
                                                                                    {/if}
                                                                            {/if}
                                                                            </div>
                                                                        {/if}
                                                                    </div>
                                                                {/section}
                                                                <div class="clear"></div>
                                                            </div>
                                                        {/section}
                                                    </div>
                                                {/foreach}
                                                {if $sectiontitle2 != ''}
                                                </div>
                                            {/if}
                                        {else}
                                            {if $section=='yourorders'}
                                                {if $tempordercount == 0}
                                                    <div class="emptyBox">
                                                        {#str_LabelNoActiveOrders#}
                                                    </div>
                                                {/if}
                                            {/if}
                                        {/if}
                                    </div>
                                </div>
                            </div>
                            <div class="buttonBottom">
                                <div class="btnLeft">
                                    <div class="contentBtn" id="backButton">
                                        <div class="btn-blue-arrow-left" ></div>
                                        <div class="btn-blue-middle">{#str_ButtonBack#}</div>
                                        <div class="btn-blue-right"></div>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        {/if}
                        <!-- END YOUR ORDERS -->

                        <!-- BEGIN DISPLAY EXISTING ONLINE PROJECTS -->

                        {if $section == 'existingonlineprojects'}

                        <div id="confirmationBox" class="section maw_dialog">
                            <div id="confirmationBoxTop" class="dialogTop">
                                <h2 class="title-bar">
                                    {#str_TitleError#}
                                </h2>
                            </div>
                            <div class="content confirmationBoxContent">
                                <div id="confirmationBoxText" class="message"></div>
                                <div id="buttonsHolderConfirmation" class="buttonBottomInside">
                                    <div class="btnRight">
                                        <div class="contentBtn closeConfirmationContainer">
                                            <div class="btn-green-left" ></div>
                                            <div class="btn-accept-right"></div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div id="buttonsHolderQuestion" class="buttonBottomInside">
                                    <div class="btnLeft">
                                        <div class="contentBtn closeConfirmationContainer">
                                            <div class="btn-blue-left" ></div>
                                            <div class="btn-blue-middle">{#str_LabelClose#}</div>
                                            <div class="btn-blue-right"></div>
                                        </div>
                                    </div>
                                    <div class="btnRight">
                                        <div class="contentBtn unshareConfirmContainer">
                                            <div class="btn-green-left" ></div>
                                            <div class="btn-green-middle">{#str_LabelUnshare#}</div>
                                            <div class="btn-accept-right"></div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>

                        <div id="pageFooterHolder" {if $sidebaraccount == '' && $sidebarcontactdetails == ''}class="fullsizepage"{/if}>
                            <div id="page" class="section">
                                {if $showpurgeall}
                                    <div id="purgeAllMessage" class="purgeAllMessage">
                                        <p>{#str_MessageProjectsFlaggedForPurge#} <a href="#" id="purgeAllLink">{#str_MessageDeleteAllFlaggedProjects#}</a></p>
                                    </div>
                                {/if}

                                <div class="title-bar">
                                    <div class="title-current">{#str_LabelProjects#}</div>
                                    <div class="title-status-right">{#str_LabelStatus#}</div>
                                    <div class="clear"></div>
                                </div>

                                <div id="content" class="content contentNoPaddingSide">

                                    {if $maintenancemode eq true}

                                        <div class="emptyBox">
                                           {#str_ErrorMaintenanceMode#}
                                        </div>

                                    {else}

                                        {if $projects|@sizeof > 0}
                                            {include file="$simpleDialog"}

                                            <div class="projectlist" id="existingOnlineProjectList">

                                            {section name=index loop=$projects}

                                                <div class="contentRow{if $smarty.section.projects.last} noBorder{/if}"
                                                            id="{$projects[index].projectref}"
    														data-projectname="{$projects[index].name}"
                                                            data-productident="{$projects[index].productident}"
                                                            data-canedit="{$projects[index].canedit}"
    														data-candelete="{$projects[index].candelete}"
                                                            data-cancompleteorder="{$projects[index].cancompleteorder}"
                                                            data-projectstatus="{$projects[index].projectstatus}"
                                                            data-workflowtype="{$projects[index].workflowtype}">
                                                    <div class="bloc_content">
                                                        <div class="previewHolder projectRowHighLight">
                                                            <div class="previewItem">
                                                                <div id="img_{$projects[index].projectref}" class="product-preview-wrap">
                                                                    {assign var='thumbnailpath' value=''}
                                                                    {if $projects[index].thumbnailpath !== ''}
                                                                      {assign var='thumbnailpath' value="{$onlinedesignerurl}{$projects[index].thumbnailpath|escape}"}
                                                                    {/if}

                                                                    {if $projects[index].projectpreviewthumbnail != ''}

                                                                        <img class="product-preview-image" src="{$projects[index].projectpreviewthumbnail|escape}" data-asset="{$thumbnailpath}" alt="" />

                                                                    {else if $projects[index].thumbnailpath != ''}

                                                                        <img class="product-preview-image" src="{$thumbnailpath}" data-asset="" alt="" />

                                                                    {else}

                                                                        <img class="product-preview-image" src="{$brandroot}/images/no_image-2x.jpg" alt="" />

                                                                    {/if}

                                                                </div>
                                                                <div class="previewItemText onlinePreview">
                                                                    <div class="textProduct" id="name_{$projects[index].projectref}">
                                                                        {$projects[index].name}
                                                                    </div>
                                                                    <div class="contentDescription">
                                                                        <div class="description-product">
                                                                            {$projects[index].productname}
                                                                        </div>
                                                                        {if $projects[index].dateofpurge != ''}
                                                                            <div class="dateofpurge">
                                                                                <span class="label-purge-date">{#str_MessageProjectDueToBePurged#} {$projects[index].dateofpurge}</span> <a href="#" class="keepProjectLink" data-projectref="{$projects[index].projectref}">{#str_MessageKeepProject#}</a>
                                                                            </div>
                                                                        {/if}
                                                                        <div class="ordernumber">
                                                                            <span class="label-order-number">{#str_LabelCreated#}</span> {$projects[index].datecreated}
                                                                        </div>
                                                                    </div>
                                                                    <div class="online-production-status">

                                                                        <span id="statusDescription{$projects[index].projectref}" class="previewItemDetail textGreen">
                                                                            {$projects[index].statusdescription}
                                                                        </span>

                                                                    </div>
                                                                    <div class="clear"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>
                                                </div>
                                            {/section}
                                        </div>

                                        {else} {* else {if $projects|@sizeof > 0} *}

                                        <div class="emptyBox">
                                           {#str_LabelNoOnlineProject#}
                                        </div>

                                        {/if} {* end {if $projects|@sizeof > 0} *}
                                    {/if}
                                </div>

                                {if $projects|@sizeof > 0}

                                <div class="onlineproject_btnLinks">

									{if $browsersupported == false}

								<div id="browserNotSupported">
									{#str_MessageBrowserNotSupported#}
								</div>

									{/if}

                                    <div class="online-buttons" id="completeBtn">
                                        <div id="completeBtnLeft" class="btn-disabled-left" ></div>
                                        <div id="completeBtnMiddle" class="btn-disabled-middle btnOnlineMiddle"><span>{#str_ButtonCompleteOrder#}</span></div>
                                        <div id="completeBtnRight" class="btn-disabled-right"></div>
                                        <div class="clear"></div>
                                    </div>

                                    <div class="online-buttons" id="editBtn">
                                        <div id="editBtnLeft" class="btn-disabled-left" ></div>
                                        <div id="editBtnMiddle"class="btn-disabled-middle btnOnlineMiddle"><span>{#str_ButtonContinueEditing#}</span></div>
                                        <div id="editBtnRight"class="btn-disabled-right"></div>
                                        <div class="clear"></div>
                                    </div>

                                    <div class="online-buttons" id="duplicateBtn">
                                        <div id="duplicateBtnLeft" class="btn-disabled-left" ></div>
                                        <div id="duplicateBtnMiddle" class="btn-disabled-middle btnOnlineMiddle"><span>{#str_ButtonDuplicateProject#}</span></div>
                                        <div id="duplicateBtnRight" class="btn-disabled-right"></div>
                                        <div class="clear"></div>
                                    </div>

                                    <div class="online-buttons" id="renameBtn">
                                        <div id="renameBtnLeft" class="btn-disabled-left" ></div>
                                        <div id="renameBtnMiddle" class="btn-disabled-middle btnOnlineMiddle"><span>{#str_ButtonRenameProject#}</span></div>
                                        <div id="renameBtnRight" class="btn-disabled-right"></div>
                                        <div class="clear"></div>
                                    </div>

                                    <div class="online-buttons" id="shareBtn">
                                        <div id="shareBtnLeft" class="btn-disabled-left" ></div>
                                        <div id="shareBtnMiddle" class="btn-disabled-middle btnOnlineMiddle"><span>{#str_ButtonShareProject#}</span></div>
                                        <div id="shareBtnRight" class="btn-disabled-right"></div>
                                        <div class="clear"></div>
                                    </div>

                                    <div class="online-buttons" id="deleteBtn">
                                        <div id="deleteBtnLeft" class="btn-disabled-left" ></div>
                                        <div id="deleteBtnMiddle" class="btn-disabled-middle btnOnlineMiddle"><span>{#str_ButtonDeleteProject#}</span></div>
                                        <div id="deleteBtnRight" class="btn-disabled-right"></div>
                                        <div class="clear"></div>
                                    </div>

                                    <div class="clear"></div>

                                </div>

                                {/if} {* end {if $projects|@sizeof > 0} *}

                            </div>
                        </div>

                        {/if}

                        <!-- END DISPLAY EXISTING ONLINE PROJECTS -->

                        <!-- MENU -->
                        {if $section=='menu'}

                            <div id="pageFooterHolder" {if $sidebaraccount == '' && $sidebarcontactdetails == ''} class="fullsizepage"{/if}>
                                <div id="page" class="section">
                                    <h2 class="title-bar">
                                        {#str_TitleMyAccount#}
                                    </h2>
                                    <div class="content contentMenu" id="content">
                                        <div class="message{if $isConfirmation==1} confirmation{/if}" id="message">
                                            {$message}
                                        </div>
                                        <div>
                                            <div class="menuItem menuItemCurrentOrder">
                                                <a href="#" class="menuActionButton" data-action="Customer.yourOrders">
                                                    <img src="{$brandroot}/images/dashboard_icons/account_current_orders.png" alt="" /><br />
                                                    <span>
                                                        {#str_MenuTitleYourOrders#}
                                                    </span>
                                                </a>
                                            </div>

                                            {if $hasonlinedesignerurl == 1}

                                                <div class="menuItem menuItemOnlineProject">
                                                    <a href="#" class="menuActionButton" data-action="Customer.displayOnlineProjectList">
                                                        <img src="{$brandroot}/images/dashboard_icons/account_online_projects.png" alt=""><br>
                                                        <span>
                                                            {#str_MenuTitleOnlineProjects#}
                                                        </span>
                                                    </a>
                                                </div>

                                            {/if}

                                            {if $canmodifyaccountdetails==1}

                                                <div class="menuItem menuItemAccountDetails">
                                                    <a href="#" class="menuActionButton" data-action="Customer.accountDetails">
                                                        <img src="{$brandroot}/images/dashboard_icons/account_details.png" alt="" /><br />
                                                        <span>
                                                            {#str_MenuTitleAccountDetails#}
                                                        </span>
                                                    </a>
                                                </div>

                                            {/if}

                                            {if $canmodifypassword==1}

                                                <div class="menuItem menuItemChangePassword">
                                                    <a href="#" class="menuActionButton" data-action="Customer.changePassword">
                                                        <img src="{$brandroot}/images/dashboard_icons/account_password.png" alt="" /><br />
                                                        <span>
                                                            {#str_MenuTitleChangePassword#}
                                                        </span>
                                                    </a>
                                                </div>

                                            {/if}
                                                <div class="menuItem menuItemChangePreferences">
                                                    <a href="#" class="menuActionButton" data-action="Customer.changePreferences">
                                                        <img src="{$brandroot}/images/dashboard_icons/account_preferences.png" alt="" /><br />
                                                        <span>
                                                            {#str_MenuTitleChangePreferences#}
                                                        </span>
                                                    </a>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            {/if}
                            <!-- END MENU -->
                            {if $sidebaradditionalinfo != ''}
                                {include file="$sidebaradditionalinfo"}
                            {/if}
                        </div>
                        <div class="clear"></div>
                        {if $sidebaraccount != '' or $sidebarcontactdetails != ''}
                            <div id="side-outer-panel" class="side-outer-panel side-outer-panel-scroll" >
                                {if $sidebaraccount != ''}
                                    {include file="$sidebaraccount"}
                                {/if}
                                {if $sidebarcontactdetails != ''}
                                    {include file="$sidebarcontactdetails"}
                                {/if}
								{if $displayredaction == 1}
                                    {include file="$sidebarredaction_default"}
								{/if}
                            </div>
                        {/if}
                        <div class="clear"></div>
                        </div>

                    <form id="showPreviewForm" method="post" target="_blank">
                        <input type="hidden" name="csrf_token" value="{csrf_token}" />
                    </form>

                    <form id="submitform" name="submitform" method="post" action="#" accept-charset="utf-8">
                        <input type="hidden" id="ref" name="ref" value="{$session}" />
                        <input type="hidden" id="fsaction" name="fsaction" value="" />
                        <input type="hidden" id="ssotoken" name="ssotoken" value="{$ssotoken}" />
                        {if $section=='accountdetails'}
                            <input type="hidden" id="contactfname" name="contactfname" />
                            <input type="hidden" id="contactlname" name="contactlname" />
                            <input type="hidden" id="companyname" name="companyname" />
                            <input type="hidden" id="address1" name="address1" />
                            <input type="hidden" id="address2" name="address2" />
                            <input type="hidden" id="address3" name="address3" />
                            <input type="hidden" id="address4" name="address4" />
                            <input type="hidden" id="add41" name="add41" />
                            <input type="hidden" id="add42" name="add42" />
                            <input type="hidden" id="add43" name="add43" />
                            <input type="hidden" id="city" name="city" />
                            <input type="hidden" id="county" name="county" />
                            <input type="hidden" id="state" name="state" />
                            <input type="hidden" id="regioncode" name="regioncode" />
                            <input type="hidden" id="region" name="region" />
                            <input type="hidden" id="postcode" name="postcode" />
                            <input type="hidden" id="countrycode" name="countrycode" />
                            <input type="hidden" id="countryname" name="countryname" />
                            <input type="hidden" id="telephonenumber" name="telephonenumber" />
                            <input type="hidden" id="email" name="email" />
                            <input type="hidden" id="originalemail" name="originalemail" />
                            <input type="hidden" id="registeredtaxnumbertype" name="registeredtaxnumbertype" />
                            <input type="hidden" id="registeredtaxnumber" name="registeredtaxnumber" />
                            {if $customerupdateauthrequired}
                                <input type="hidden" id="confirmpassword" name="confirmpassword"/>
                                <input type="hidden" id="confirmformat" name="confirmformat"/>
                            {/if}
                        {elseif $section=='changepassword'}
                            <input type="hidden" id="data1" name="data1" />
                            <input type="hidden" id="data2" name="data2" />
                            <input type="hidden" id="format" name="format" />
                        {elseif $section=='changepreferences'}
                            <input type="hidden" id="sendmarketinginfo" name="sendmarketinginfo" />
                        {/if}
                        <input type="hidden" id="orderitemid" name="orderitemid" value="" />
                        <input type="hidden" id="action" name="action" value="" />
                        <input type="hidden" id="giftcardcode" name="giftcardcode" value="" />
                        <input type="hidden" id="giftcardaction" name="giftcardaction" value="" />
                        <input type="hidden" id="showgiftcardmessage" name="showgiftcardmessage" value="0"/>
						<input type="hidden" id="tzoffset" name="tzoffset" value="" />
                        <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
                    </form>

                <div id="dialogOuter" class="dialogOuter"></div>
                </body>
            </html>