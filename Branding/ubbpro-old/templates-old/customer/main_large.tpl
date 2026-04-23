<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {#str_TitleMyAccount#}</title>
        {include file="includes/customerinclude_large.tpl"}

         <script type="text/javascript" id="mainjavascript">
        //<![CDATA[

            {include file="customer/main.tpl"}

         //]]>
        </script>
			
    </head>
    <body id="body" onresize="resizePopup();">
        <div id="shim">&nbsp;</div>
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
                            <div class="contentBtn" onclick="closeConfirmationBox();">
                                <div class="btn-green-left" ></div>
                                <div class="btn-accept-right"></div>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div id="buttonsHolderQuestion" class="buttonBottomInside">
                        <div class="btnLeft">
                            <div class="contentBtn" onclick="closeConfirmationBox();">
                                <div class="btn-blue-left" ></div>
                                <div class="btn-blue-right"></div>
                            </div>
                        </div>
                        <div class="btnRight">
                            <div class="contentBtn" onclick="unshareConfirm();">
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
									<input type="radio" name="shareMethod" id="shareMethodsSocial" checked="checked" onclick="changeShareMethod();" value="social" />
									<label for="shareMethodsSocial">
										<img src="{$webroot}/images/icons/share_via_social.png" alt="Social Media" />
									</label><br /><br />
									{if $sharebyemailmethod > 0}
									<input type="radio" name="shareMethod" id="shareMethodsEmail" onclick="changeShareMethod();" value="email"/>
									<label for="shareMethodsEmail">
										<img src="{$webroot}/images/icons/share_via_email.png" alt="Email" />
									</label>
									{/if}
								</div>
								<div id="prefiewPasswordHolder" class="shareContentRight">
									<div class="passwordProtectionCheckBoxBloc">
										<input type="checkbox" id="sharepassword" name="sharepassword" onclick="passwordDisplay();"/>
										<label for="sharepassword">
											{#str_LabelSharePasswordProtection#}
										</label>
									</div>
									<div class="passwordProtectionBloc">
										<label for="previewPassword">
											{#str_LabelSharePassword#}:
										</label>
										<img id="previewPasswordcompulsory2" class="imgMessage" src="{$webroot}/images/asterisk.png" alt="*"/>
										<input id="previewPassword" name="previewPassword" type="password" disabled="disabled" />
										<img id="previewPasswordcompulsory" class="imgMessage error_form_image" src="{$webroot}/images/asterisk.png" alt="*"/>
										<div class="clear"></div>
									</div>
									<div class="passwordProtectionBloc">
										<label for="previewPassword2">
											{#str_LabelShareConfirmPassword#}:
										</label>
										<img id="previewPassword2compulsory2" class="imgMessage" src="{$webroot}/images/asterisk.png" alt="*"/>
										<input id="previewPassword2" name="previewPassword2" type="password" disabled="disabled" />
										<img id="previewPassword2compulsory" class="imgMessage error_form_image" src="{$webroot}/images/asterisk.png" alt="*"/>
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
								<script type="text/javascript" src="{$serverprotocol}static.addtoany.com/menu/page.js"></script>
							</div>
						</div>
						<div id="shareEmail">
							<div class="lessPaddingTop shareBlocEmail">
								<h2 class="title-bar-inside">
									{#str_LabelByEmail#}
								</h2>
								<form id="popupBox2Form" method="post" action="#" onsubmit="shareByEmail(); return false;">
									<div>
										<label for="shareByEmailTitle">
											{#str_LabelMessageTitle#}
										</label>
										<img src="{$webroot}/images/asterisk.png" alt="*"/>
										<input type="text" id="shareByEmailTitle" name="shareByEmailTitle"/>
										<img id="shareByEmailTitlecompulsory" class="error_form_image" src="{$webroot}/images/asterisk.png" alt="*"/>
										<div class="clear"></div>
									</div>
									{if $sharebyemailmethod == 1}
										<div class="top_gap">
											<label for="shareByEmailTo">
												{#str_LabelShareWithEmails#}
											</label>
											<img src="{$webroot}/images/asterisk.png" alt="*"/>
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
											<img src="{$webroot}/images/asterisk.png" alt="*"/>
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
									<img src="{$webroot}/images/asterisk.png" alt="*" />
									{#str_LabelCompulsoryFields#}
								</div>
							</div>
						</div>
                    </div>
				</div>
				<div class="buttonShare">
					<div class="btnLeft">
						<div class="contentBtn" onclick="closeConfirmationBox();">
							<div class="btn-red-cross-left" ></div>
							<div class="btn-red-middle">{#str_ButtonCancel#}</div>
							<div class="btn-red-right"></div>
						</div>
					</div>
					<div class="btnRight">
						<div class="contentBtn" id="shareByEmailBtn" onclick="shareByEmail();">
							<div class="btn-green-left" ></div>
							<div class="btn-green-middle">{#str_LabelShare#}</div>
							<div class="btn-accept-right"></div>
						</div>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
				</div>
            </div>
        {else}
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
                            <div class="contentBtn" onclick="closeConfirmationBox();">
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
					   {if $projects|@sizeof > 0}

					   <div style="display:none;">
					     {section name=index loop=$projects}
						  <div>{$projects[index].productname}</div>
						 {/section}
					   </div>
					   {/if}
                        <div class="current-item">
                            <img src="{$webroot}/images/icons/home_icon.png" alt="" />
                            {#str_TitleMyAccount#} - {$userlogin}
                        </div>
                    {else}
                        <a href="#home" id="homeLink">
                            <img src="{$webroot}/images/icons/home_icon.png" alt="" />
                            {#str_TitleMyAccount#} - {$userlogin}
                        </a>
                        <img src="{$webroot}/images/icons/breadcrumb_icon.png" alt="" class="separator" />
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
                <div class="btnLogOut" onclick="document.getElementById('headerform').submit();">
                    <form id="headerform" method="post" action="#">
                        <input type="hidden" id="header_ref" name="ref" value="{$session}" />
                        <input type="hidden" id="header_fsaction" name="fsaction" value="Customer.logout" />
                    </form>
                    <span>
                        {#str_LabelSignOut#}
                    </span>
                    <img src="{$webroot}/images/icons/sign_out_icon.png" alt="" />
                </div>
                <div class="clear"></div>
            </div>
            <div id="contentScroll" class="contentScroll">
                {if $sidebarleft != ''}
                    {include file="$sidebarleft"}
                {/if}
                <div id="contentHolder">
                    <!-- ACCOUNT DETAILS -->
                    {if $section=='accountdetails'}
                        <div id="pageFooterHolder" {if $sidebaraccount == '' && $sidebarcontactdetails == ''}class="fullsizepage"{/if}>
                            <div id="page" class="section">
                                <h2 class="title-bar">
                                    {#str_MenuTitleAccountDetails#}
                                </h2>
                                <form method="post" id="mainform" name="mainform" action="#" onsubmit="return verifyAddress();">
                                    <div class="content contentForm">
                                        <div class="message{if $isConfirmation==1} confirmation{/if}" id="message">{$message}</div>
                                        <div id="ajaxdiv"></div>
                                        <div class="top_gap">
                                            <label for="telephonenumber">{#str_LabelTelephoneNumber#}:</label>
                                            <img src="{$webroot}/images/asterisk.png" alt="*"/>
                                            <input type="text" id="telephonenumber_account" name="telephonenumber_account" value="{$telephonenumber}" onblur="CJKHalfWidthFullWidthToASCII(this, false)" class="middle" />
                                            <img class="error_form_image" id="telephonenumbercompulsory" src="{$webroot}/images/asterisk.png" alt="*"/>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="top_gap">
                                            <label for="email">{#str_LabelEmailAddress#}:</label>
                                            <img src="{$webroot}/images/asterisk.png" alt="*"/>
                                            <input type="text" id="email_account" name="email_account" value="{$email}" class="middle" />
                                            <img class="error_form_image" id="emailcompulsory" src="{$webroot}/images/asterisk.png" alt="*"/>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="top_gap">
                                            <label for="email2">{#str_LabelRetypeEmailAddress#}:</label>
                                            <img src="{$webroot}/images/asterisk.png" alt="*"/>
                                            <input type="text" id="email2" name="email2" value="{$email}" class="middle" />
                                            <img class="error_form_image" id="email2compulsory" src="{$webroot}/images/asterisk.png" alt="*"/>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="note">
                                            <img src="{$webroot}/images/asterisk.png" alt="*" />
                                            {#str_LabelCompulsoryFields#}
                                        </div>
                                        <input type="submit" style="display:none;"/>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="buttonBottom">
                            {if $addressupdated != 0}
                            <div class="btnLeft">
                                <div class="contentBtn" id="backButton">
                                    
                                    <div class="btn-green-middle">{#str_ButtonBack#}</div>
                                    
                                </div>
                            </div>
                        	{/if}
                            <div class="btnRight">
                                <div class="contentBtn" id="updateButton">
                                  
                                    <div class="btn-green-middle">{#str_ButtonUpdate#}</div>
                                    
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
                                <form action="#" method="post" id="changePassword" onsubmit="return checkFormChangePassword();">
                                    <div class="contentForm content">
                                        <div class="message{if $isConfirmation==1} confirmation{/if}" id="message">
                                            {$message}
                                        </div>
                                        <div>
                                            <label for="oldpassword">{#str_LabelCurrentPassword#}:</label>
                                            <img src="{$webroot}/images/asterisk.png" alt="*"/>
                                            <input type="password" id="oldpassword" name="oldpassword" value="" class="middle" />
                                            <div class="clear"></div>
                                        </div>
                                        <div class="top_gap">
                                            <label for="newpassword">{#str_LabelNewPassword#}: </label>
                                            <img src="{$webroot}/images/asterisk.png" alt="*"/>
                                            <input type="password" id="newpassword" name="newpassword" value="" class="middle" />
                                            <div class="clear"></div>
                                        </div>
                                        <div class="top_gap">
                                            <label for="newpassword2">{#str_LabelRetypePassword#}:</label>
                                            <img src="{$webroot}/images/asterisk.png" alt="*"/>
                                            <input type="password" id="newpassword2" name="newpassword2" value="" class="middle" />
                                            <div class="clear"></div>
                                        </div>
                                        <div class="note">
                                            <img src="{$webroot}/images/asterisk.png" alt="*" />
                                            {#str_LabelCompulsoryFields#}
                                        </div>
                                        <input type="submit" style="display:none;"/>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="buttonBottom">
                            <div class="btnLeft">
                                <div class="contentBtn" id="backButton">
                                    
                                    <div class="btn-green-middle">{#str_ButtonBack#}</div>
                                   
                                </div>
                            </div>
                            <div class="btnRight">
                                <div class="contentBtn" id="updateButton">
                                    
                                    <div class="btn-green-middle">{#str_ButtonUpdate#}</div>
                                    
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
                                <form action="#" method="post" onsubmit="return checkFormChangePreferences();">
                                    <div class="content contentForm">
                                        <div class="message{if $isConfirmation==1} confirmation{/if}" id="message">
                                            {$message}
                                        </div>
                                        <div class="top_gap">
                                            <input type="radio" name="sendmarketinginfo" id="subscribeYes" value="1" class="widthAuto" {if $sendmarketinginfo == 1} checked="checked"{/if} />
                                            <label class="widthAuto" for="subscribeYes">
                                                {#str_LabelMarketingSubscribed#}
                                            </label>
                                        </div>
                                        <div class="accountSeparator"></div>
                                        <div class="top_gap">
                                            <input type="radio" name="sendmarketinginfo" id="subscribeNo" value="0" class="widthAuto" {if $sendmarketinginfo != 1} checked="checked"{/if} />
                                            <label class="widthAuto" for="subscribeNo">
                                                {#str_LabelMarketingUnsubscribe#}
                                            </label>
                                        </div>
                                        <input type="submit" style="display:none;"/>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="buttonBottom">
                            <div class="btnLeft">
                                <div class="contentBtn" id="backButton">
                                    
                                    <div class="btn-green-middle">{#str_ButtonBack#}</div>
                                    
                                </div>
                            </div>
                            <div class="btnRight">
                                <div class="contentBtn" id="updateButton">
                                   
                                    <div class="btn-green-middle">{#str_ButtonUpdate#}</div>
                                    
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>


                    {/if}
                    <!-- END CHANGE PREFERENCES -->

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
                                                    <div  class="contentRow{if $smarty.foreach.orders.last} noBorder{/if}">
                                                        {section name=productloop loop=$row.product|@sizeof step=3}
                                                            <div class="bloc_content">
                                                                {section name=indexProduct start=$smarty.section.productloop.index loop=$row.product step=1 max=3}
                                                                    <div class="previewHolder">
                                                                        <div class="previewItem" orderid="{$row.product[indexProduct].id}">
                                                                            <div class="previewItemImg">
                                                                                {if $row.product[indexProduct].previewimage != ''}
                                                                                    <img src="{$row.product[indexProduct].previewimage|escape}" alt="" />
                                                                                {else}
                                                                                    <img src="{$webroot}/images/no_image.png" alt="" />
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
                                                                                {if $row.status > 0}
                                                                                    <div class="clear"></div>
                                                                                    <div class="btnLinks">
                                                                                        <div onclick="window.location.replace('{$row.sessionurl|escape}');">
                                                                                            <div class="btn-green-left" ></div>
                                                                                            <div class="btn-green-middle">{#str_LabelPayNow#}</div>
                                                                                            <div class="btn-green-right"></div>
                                                                                        </div>
                                                                                        {if $row.product[indexProduct].previewsonline==1}
                                                                                            <div onclick="window.open('{$webbranddisplayurl}{$row.product[indexProduct].previewurl|escape}&amp;ref={$session}&amp;id={$row.product[indexProduct].id}');">
                                                                                                <div class="btn-white-left" ></div>
                                                                                                <div class="btn-white-middle">{#str_ButtonPreview#}</div>
                                                                                                <div class="btn-white-right"></div>
                                                                                            </div>
                                                                                        {/if}
                                                                                    </div>
                                                                                {else}
                                                                                    {if $row.product[indexProduct].previewsonline==1}
																						<div class="clear"></div>
                                                                                        <div class="btnLinks">
                                                                                            <div onclick="window.open('{$webbranddisplayurl}{$row.product[indexProduct].previewurl|escape}&amp;ref={$session}&amp;id={$row.product[indexProduct].id}');">
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
                                                                {section name=indexProduct start=$smarty.section.productloop.index loop=$row.product step=1 max=3}
                                                                    <div class="previewHolder">
                                                                        <div class="previewItem" orderid="{$row.product[indexProduct].id}">
                                                                            <div class="previewItemImg">
                                                                                {if $row.product[indexProduct].previewimage != ''}
                                                                                    <img src="{$row.product[indexProduct].previewimage|escape}" alt="" />
                                                                                {else}
                                                                                    <img src="{$webroot}/images/no_image.png" alt="" />
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
                                                                                        {elseif $row.product[indexProduct].status < 60}
                                                                                            <span class="previewItemDetail textBlue">
                                                                                                {#str_LabelStatusInProduction#}
                                                                                            </span>
                                                                                        {else}
                                                                                            <span class="previewItemDetail textGreen">
                                                                                                {#str_LabelStatusShipped#}
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
                                                                                </div>
                                                                                <div class="descriptionPrice">
                                                                                    {if $row.product|@sizeof == 1}
                                                                                        {$row.formattedordertotal}
                                                                                    {/if}
                                                                                </div>
                                                                                <div class="clear"></div>
                                                                                <div class="btnLinks">
                                                                                    {if $row.orderstatus !=1 }
                                                                                        {if $row.status != 0}
                                                                                            {if $row.canreorder == 1}
																								<div onclick="return executeButtonAction(this, '{$row.product[indexProduct].id}', 1, &quot;{$row.product[indexProduct].projectname|escape}&quot;, &quot;{$row.product[indexProduct].productname|escape}&quot;);">
																									<div class="btn-green-middle">{#str_LabelReorder#}</div>
																								</div>
                                                                                            {/if}
                                                                                            {if $row.product[indexProduct].isShared == true}
                                                                                                <div onclick="return executeButtonAction(this, '{$row.product[indexProduct].id}', 2, &quot;{$row.product[indexProduct].projectname|escape}&quot;, &quot;{$row.product[indexProduct].productname|escape}&quot;);">
                                                                                                    
                                                                                                    <div class="btn-green-middle">{#str_LabelUnshare#}</div>
                                                                                                    
                                                                                                </div>
                                                                                            {else}
                                                                                                <div onclick="return executeButtonAction(this, '{$row.product[indexProduct].id}', 2, &quot;{$row.product[indexProduct].projectname|escape}&quot;, &quot;{$row.product[indexProduct].productname|escape}&quot;);" style="display: none">
                                                                                                    
                                                                                                    <div class="btn-green-middle">{#str_LabelUnshare#}</div>
                                                                                                    
                                                                                                </div>
                                                                                            {/if}
                                                                                            {if $row.origorderid == 0}
                                                                                                <div onclick="return executeButtonAction(this, '{$row.product[indexProduct].id}', 0, &quot;{$row.product[indexProduct].projectname|escape}&quot;, &quot;{$row.product[indexProduct].productname|escape}&quot;);">
                                                                                                    
                                                                                                    <div class="btn-green-middle">{#str_LabelShare#}</div>
                                                                                                   
                                                                                                </div>
                                                                                            {/if}

                                                                                        {elseif $row.product[indexProduct].source == 1}
                                                                                            {if $row.product[indexProduct].isShared == true}
                                                                                                <div onclick="return executeButtonAction(this, '{$row.product[indexProduct].id}', 2, &quot;{$row.product[indexProduct].projectname|escape}&quot;, &quot;{$row.product[indexProduct].productname|escape}&quot;);">
                                                                                                    
                                                                                                    <div class="btn-green-middle">{#str_LabelUnshare#}</div>
                                                                                                    
                                                                                                </div>
                                                                                            {else}
                                                                                                <div onclick="return executeButtonAction(this, '{$row.product[indexProduct].id}', 2, &quot;{$row.product[indexProduct].projectname|escape}&quot;, &quot;{$row.product[indexProduct].productname|escape}&quot;);" style="display: none">
                                                                                                    
                                                                                                    <div class="btn-green-middle">{#str_LabelUnshare#}</div>
                                                                                                    
                                                                                                </div>
                                                                                            {/if}

                                                                                            <div onclick="return executeButtonAction(this, '{$row.product[indexProduct].id}', 0, &quot;{$row.product[indexProduct].projectname|escape}&quot;, &quot;{$row.product[indexProduct].productname|escape}&quot;);">
                                                                                                
                                                                                                <div class="btn-green-middle">{#str_LabelShare#}</div>
                                                                                                
                                                                                            </div>

                                                                                        {/if}

                                                                                        {if $row.product[indexProduct].previewsonline==1}
                                                                                            <div onclick="window.open('{$row.product[indexProduct].previewurl|escape}&amp;ref={$session}&amp;id={$row.product[indexProduct].id}');">
                                                                                                
                                                                                                <div class="btn-green-middle">{#str_ButtonPreview#}</div>
                                                                                                
                                                                                            </div>
                                                                                        {/if}
                                                                                    {/if}
                                                                                </div>
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
                                        
                                        <div class="btn-green-middle">{#str_ButtonBack#}</div>
                                        
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        {/if}
                        <!-- END YOUR ORDERS -->

                        <!-- BEGIN DISPLAY EXISTING ONLINE PROJECTS -->

                        {if $section=='existingonlineprojects'}

                        <div id="loadingBox" class="section maw_dialog">
                            <div class="dialogTop">
                                <h2 id="loadingTitle" class="title-bar"></h2>
                            </div>
                            <div class="content">
                                <div class="loadingMessage">
                                    <img src="{$webroot}/images/loading_shoppingcart.gif" class="loading-icon" alt="{#str_MessageLoading#}" />
                                </div>
                            </div>
                        </div>
                        <div id="shimLoading">&nbsp;</div>

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
                                        <div class="contentBtn" onclick="closeConfirmationBox();">
                                            <div class="btn-green-left" ></div>
                                            <div class="btn-accept-right"></div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div id="buttonsHolderQuestion" class="buttonBottomInside">
                                    <div class="btnLeft">
                                        <div class="contentBtn" onclick="closeConfirmationBox();">
                                            <div class="btn-blue-left" ></div>
                                            <div class="btn-blue-middle">{#str_LabelClose#}</div>
                                            <div class="btn-blue-right"></div>
                                        </div>
                                    </div>
                                    <div class="btnRight">
                                        <div class="contentBtn" onclick="unshareConfirm();">
                                            <div class="btn-green-left" ></div>
                                            <div class="btn-green-middle">{#str_LabelUnshare#}</div>
                                            <div class="btn-accept-right"></div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>

                        <div id="dialogBox" class="section maw_dialog">
                        <div class="dialogTop">
                            <h2 class="title-bar" id="renameProjectTitle"></h2>
                        </div>
                        <div class="content">
                            <input type="hidden" id="projectrefhidden" value = "" />
                            <input type="hidden" id="projectnamehidden" value = "" />
                            <input type="hidden" id="projectworkflowtype" value = "" />
                            <input type="hidden" id="productindent" value = "" />

                            <div class="projectname_container" id="projectname_container"></div>

                            <div class="buttonShare">
                                <div class="btnLeft">
                                    <div class="contentBtn" id="projectcancelbutton" onclick="closeDialogBox();">

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
                        <div id="pageFooterHolder" {if $sidebaraccount == '' && $sidebarcontactdetails == ''}class="fullsizepage"{/if}>
                            <div id="page" class="section">
                                <div class="title-bar">
                                    <div class="title-current">{#str_LabelProjects#}</div>
                                    <div class="title-status-right">{#str_LabelStatus#}</div>
                                    <div class="clear"></div>
                                </div>

                                <div id="content" class="content contentNoPaddingSide">

                                    {if $projects|@sizeof > 0}

                                    <div class="projectlist" id="existingOnlineProjectList">

                                        {section name=index loop=$projects}

                                            <div class="contentRow{if $smarty.section.projects.last} noBorder{/if}" onclick="selectProject(this);"
                                                        id="{$projects[index].projectref}"
														data-projectname="{$projects[index].name}"
                                                        data-productident="{$projects[index].productident}"
                                                        data-canedit="{$projects[index].canedit}"
														data-candelete="{$projects[index].candelete}"
                                                        data-cancompleteorder="{$projects[index].cancompleteorder}"
                                                        data-workflowtype="{$projects[index].workflowtype}">
                                                <div class="bloc_content">
                                                    <div class="previewHolder projectRowHighLight">
                                                        <div class="previewItem">
                                                            <div id="img_{$projects[index].projectref}" class="previewItemImg">

                                                                {if $projects[index].thumbnailpath != ''}

                                                                    <img src="{$onlinedesignerurl}{$projects[index].thumbnailpath|escape}" alt="" />

                                                                {else}

                                                                    <img src="{$webroot}/images/no_image.png" alt="" />

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

                                </div>

                                {if $projects|@sizeof > 0}

                                <div class="onlineproject_btnLinks">

                                    <div class="online-buttons" id="completeBtn">
                                       
                                        <div id="completeBtnMiddle" class="btn-disabled-middle btnOnlineMiddle">{#str_ButtonCompleteOrder#}</div>
                                       
                                        <div class="clear"></div>
                                    </div>

                                    <div class="online-buttons" id="editBtn">
                                        <div id="editBtnMiddle"class="btn-disabled-middle btnOnlineMiddle">{#str_ButtonContinueEditing#}</div>
                                        <div class="clear"></div>
                                    </div>

                                    <div class="online-buttons" id="duplicateBtn">
                                        <div id="duplicateBtnMiddle" class="btn-disabled-middle btnOnlineMiddle">{#str_ButtonDuplicateProject#}</div>
                                        <div class="clear"></div>
                                    </div>

                                    <div class="online-buttons" id="renameBtn">
                                        <div id="renameBtnMiddle" class="btn-disabled-middle btnOnlineMiddle">{#str_ButtonRenameProject#}</div>
                                        <div class="clear"></div>
                                    </div>

                                    <div class="online-buttons" id="deleteBtn">
                                        <div id="deleteBtnMiddle" class="btn-disabled-middle btnOnlineMiddle">{#str_ButtonDeleteProject#}</div>
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
                                            <div class="menuItem menuItemCurrentOrder itemTop">
                                                <a href="#" onclick="return menuAction('Customer.yourOrders');">
                                                    <img src="{$brandroot}/images/dashboard_icons/account_current_orders.png" alt="" />
                                                    <span>
                                                        {#str_MenuTitleYourOrders#}
                                                    </span>
                                                </a>
                                            </div>


                                            {if $canmodifyaccountdetails==1}

                                                <div class="menuItem menuItemAccountDetails itemRight itemTop">
                                                    <a href="#" onclick="return menuAction('Customer.accountDetails');">
                                                        <img src="{$brandroot}/images/dashboard_icons/account_details.png" alt="" />
                                                        <span>
                                                            {#str_MenuTitleAccountDetails#}
                                                        </span>
                                                    </a>
                                                </div>

                                            {/if}

                                            {if $hasonlinedesignerurl == 1}

                                                <div class="menuItem menuItemOnlineProject">
                                                    <a href="#" onclick="return menuAction('Customer.displayOnlineProjectList');">
                                                        <img src="{$brandroot}/images/dashboard_icons/account_online_projects.png" alt="">
                                                        <span>
                                                            {#str_MenuTitleOnlineProjects#}
                                                        </span>
                                                    </a>
                                                </div>

                                            {/if}											
                                          <div class="menuItem menuItemChangePreferences itemRight">
                                                    <a href="#" onclick="return menuAction('Customer.changePreferences');">
                                                        <img src="{$brandroot}/images/dashboard_icons/account_preferences.png" alt="" />
                                                        <span>
                                                            {#str_MenuTitleChangePreferences#}
                                                        </span>
                                                    </a>
                                                </div>
                                            {if $canmodifypassword==1}

                                                <div class="menuItem menuItemChangePassword">
                                                    <a href="#" onclick="return menuAction('Customer.changePassword');">
                                                        <img src="{$brandroot}/images/dashboard_icons/account_password.png" alt="" />
                                                        <span>
                                                            {#str_MenuTitleChangePassword#}
                                                        </span>
                                                    </a>
                                                </div>

                                            {/if}
                                                <div class="menuItem menuItemOldOrders itemRight">
                                                    <a href="https://hk.ubabybaby.com/shopping/index.php?main_page=account" target="_blank">
                                                        <img src="{$brandroot}/images/dashboard_icons/account_old_orders.png" alt="" />
                                                        <span>
                                                            {#str_MenuTitleOldOrders#}
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
                            </div>
                        {/if}
                        <div class="clear"></div>
                        </div>

                    <form id="submitform" name="submitform" method="post" action="#" accept-charset="utf-8">
                        <input type="hidden" id="ref" name="ref" value="{$session}" />
                        <input type="hidden" id="fsaction" name="fsaction" value="" />
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
                            <input type="hidden" id="registeredtaxnumbertype" name="registeredtaxnumbertype" />
                            <input type="hidden" id="registeredtaxnumber" name="registeredtaxnumber" />
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
                    </form>

                </body>
            </html>