<?php
/* Smarty version 4.5.3, created on 2026-03-06 04:45:24
  from 'C:\TAOPIX\MediaAlbumWeb\templates\customer\main_large.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa5be4b420b6_69747312',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8047013f07585a6a88fd0c6db147f11ccad09ff4' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\customer\\main_large.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/googletagmanager.tpl' => 1,
    'file:includes/customerinclude_large.tpl' => 1,
    'file:customer/main.tpl' => 1,
  ),
),false)) {
function content_69aa5be4b420b6_69747312 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.csrf_token.php','function'=>'smarty_function_csrf_token',),));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" xml:lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" dir="ltr">
    <head>
        <meta name="csrf-token" content="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title><?php echo $_smarty_tpl->tpl_vars['appname']->value;?>
 - <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleMyAccount');?>
</title>
        
        <?php if ($_smarty_tpl->tpl_vars['googletagmanagercccode']->value != '') {?>
            <?php $_smarty_tpl->_subTemplateRender("file:includes/googletagmanager.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('googletagmanagercccode'=>$_smarty_tpl->tpl_vars['googletagmanagercccode']->value), 0, false);
?>
        <?php }?>
        
        <?php $_smarty_tpl->_subTemplateRender("file:includes/customerinclude_large.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

         <?php echo '<script'; ?>
 type="text/javascript" id="mainjavascript" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
>
        //<![CDATA[

            <?php $_smarty_tpl->_subTemplateRender("file:customer/main.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

         //]]>
        <?php echo '</script'; ?>
>

    </head>
    <body id="body">
        <div id="shim">&nbsp;</div>

		<div id="redactConfirmBox" class="section maw_dialog">
			<div class="dialogTop">
				<h2 class="title-bar" id="redactAccountTitle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleConfirmation');?>
</h2>
			</div>

			<div class="content confirmationBoxContent">
				<div id="redactConfirmBoxText" class="message"></div>
				<div id="buttonsHolderRefactor" class="buttonBottomInside">
					<div class="btnLeft">
						<div class="contentBtn" id="closeRedactionConfirmButton">
							<div class="btn-red-cross-left" ></div>
							<div class="btn-red-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonNo');?>
</div>
							<div class="btn-red-right"></div>
						</div>
					</div>
					<div class="btnRight">
						<div class="contentBtn" id="confirmRedactionButton">
							<div class="btn-green-left" ></div>
							<div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonYes');?>
</div>
							<div class="btn-accept-right"></div>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>


		<?php if ($_smarty_tpl->tpl_vars['section']->value == 'yourorders') {?>

            <div id="confirmationBox" class="section">
                <div class="dialogTop">
                    <h2 class="title-bar">
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>

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
                                <div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUnshare');?>
</div>
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
								<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageHowWouldYouLikeToShare');?>

							</h2>
							<div class="shareContent">
								<div id="shareMethodsHolder" class="shareContentLeft">
									<input type="radio" name="shareMethod" id="shareMethodsSocial" checked="checked" value="social" />
									<label for="shareMethodsSocial">
										<img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/share_via_social.png" alt="Social Media" />
									</label><br /><br />
									<?php if ($_smarty_tpl->tpl_vars['sharebyemailmethod']->value > 0) {?>
									<input type="radio" name="shareMethod" id="shareMethodsEmail" value="email"/>
									<label for="shareMethodsEmail">
										<img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/share_via_email.png" alt="Email" />
									</label>
									<?php }?>
								</div>
								<div id="prefiewPasswordHolder" class="shareContentRight">
									<div class="passwordProtectionCheckBoxBloc">
										<input type="checkbox" id="sharepassword" name="sharepassword" />
										<label for="sharepassword">
											<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSharePasswordProtection');?>

										</label>
									</div>
									<div class="passwordProtectionBloc">
										<label for="previewPassword">
											<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSharePassword');?>
:
										</label>
										<img id="previewPasswordcompulsory2" class="imgMessage" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
										<div class="password-input-wrap">
                                            <div class="password-background">
                                                <input id="previewPassword" name="previewPassword" type="password" disabled="disabled" />
                                                <button id="togglepreviewpassword" class="password-visibility password-show"></button>
                                            </div>
                                        </div>
                                        <img id="previewPasswordcompulsory" class="imgMessage error_form_image" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
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
									<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSelectAService');?>

								</h2>
								<div id="a2a_menu_container">
									<div class="clear"></div>
								</div>
								<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['serverprotocol']->value;?>
static.addtoany.com/menu/page.js" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>
							</div>
						</div>
						<div id="shareEmail">
							<div class="lessPaddingTop shareBlocEmail">
								<h2 class="title-bar-inside">
									<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelByEmail');?>

								</h2>
								<form id="popupBox2Form" method="post" action="#" >
									<div>
										<label for="shareByEmailTitle">
											<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMessageTitle');?>

										</label>
										<img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
										<input type="text" id="shareByEmailTitle" name="shareByEmailTitle"/>
										<img id="shareByEmailTitlecompulsory" class="error_form_image" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
										<div class="clear"></div>
									</div>
									<?php if ($_smarty_tpl->tpl_vars['sharebyemailmethod']->value == 1) {?>
										<div class="top_gap">
											<label for="shareByEmailTo">
												<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareWithEmails');?>

											</label>
											<img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
											<textarea id="shareByEmailTo" name="shareByEmailTo" cols="50" rows="2" class="shareByEmailToTextarea"></textarea>
											<div class="clear"></div>
										</div>
										<div class="top_gap">
											<label for="shareByEmailText">
												<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareMessageText');?>

											</label>
											<div class="gap-label-mandatory"></div>
											<textarea id="shareByEmailText" name="shareByEmailText" class="shareByEmailTextTextarea" cols="50" rows="5"></textarea>
											<div class="clear"></div>
										</div>
									<?php } else { ?>
										<div class="top_gap">
											<label for="shareByEmailTo">
												<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareWithEmail');?>

											</label>
											<img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
											<input type="text" id="shareByEmailTo" name="shareByEmailTo" class="shareByEmailToInput"/>
											<div class="clear"></div>
										</div>
										<div class="top_gap">
											<label for="shareByEmailText">
												<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareMessageText');?>

											</label>
											<div class="gap-label-mandatory"></div>
											<textarea id="shareByEmailText" name="shareByEmailText" class="shareByEmailTextInput" cols="50" rows="5"></textarea>
											<div class="clear"></div>
										</div>
									<?php }?>
								</form>
								<div class="note">
									<img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
									<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompulsoryFields');?>

								</div>
							</div>
						</div>
                    </div>
				</div>
				<div class="buttonShare">
					<div class="btnLeft">
						<div class="contentBtn closeConfirmationContainer">
							<div class="btn-red-cross-left" ></div>
							<div class="btn-red-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
</div>
							<div class="btn-red-right"></div>
						</div>
					</div>
					<div class="btnRight">
						<div class="contentBtn" id="shareByEmailBtn">
							<div class="btn-green-left" ></div>
							<div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShare');?>
</div>
							<div class="btn-accept-right"></div>
						</div>
						<div class="clear"></div>
					</div>
					<div class="clear"></div>
				</div>
            </div>

            <div id="browserConfirmBox" class="browserConfirm">
                <div class="dialogTop">
                    <h2 class="title-bar" id="redactAccountTitle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelBrowserCompatibilityIssue');?>
</h2>
                </div>
                <div class="browserConfirmContent"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorBrowserCompatibilityIssue');?>
</div>
                <div class="btnRight">
                    <div class="browserConfirmContentBtn" id="browserConfirmContentBtn">
                        <div class="btn-green-left" ></div>
                        <div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonOk');?>
</div>
                        <div class="btn-accept-right"></div>
                    </div>
                </div>
                <div class="clear"></div>
                </div>
            </div>

        <?php } else { ?>
            <?php if ($_smarty_tpl->tpl_vars['section']->value == 'accountdetails') {?>
                <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['simpleDialog']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
            <?php }?>

            <div id="confirmationBox" class="section">
                <div class="dialogTop">
                    <h2 class="title-bar">
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>

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

        <?php }?>

        <div id="outerPage" class="outer-page<?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?> fullsize-outer-page<?php }?>">
            <div id="header" class="headertop headerScroll">
                <div class="headerinside">
                    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['header']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                </div>
                <div class="clear"></div>
            </div>
            <div class="contentNavigation">
                <div class="contentTextNavigation<?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?> contentNavigationMargin <?php }?>">

                    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'menu') {?>

                        <div class="current-item">
                            <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/home_icon_v2.png" alt="" />
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleMyAccount');?>
 - <?php echo $_smarty_tpl->tpl_vars['userdisplayname']->value;?>

                        </div>

                    <?php } else { ?>

                        <a href="#home" id="homeLink">
                            <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/home_icon_v2.png" alt="" />
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleMyAccount');?>
 - <?php echo $_smarty_tpl->tpl_vars['userdisplayname']->value;?>

                        </a>
                        <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/breadcrumb_icon.png" alt="" class="separator" />
                        <span class="current-item">

                            <?php if ($_smarty_tpl->tpl_vars['section']->value == 'accountdetails') {?>
                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleAccountDetails');?>

                            <?php }?>

                            <?php if ($_smarty_tpl->tpl_vars['section']->value == 'changepassword') {?>
                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleChangePassword');?>

                            <?php }?>

                            <?php if ($_smarty_tpl->tpl_vars['section']->value == 'changepreferences') {?>
                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleChangePreferences');?>

                            <?php }?>

                            <?php if ($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects') {?>
                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleOnlineProjects');?>

                            <?php }?>

                            <?php if ($_smarty_tpl->tpl_vars['section']->value == 'yourorders') {?>
                                <?php echo $_smarty_tpl->tpl_vars['title']->value;?>

                            <?php }?>

                        </span>

                    <?php }?>
                </div>
                <div class="btnLogOut" id="logoutButton">
                    <form id="headerform" method="post" action="#">
                        <input type="hidden" id="header_ref" name="ref" value="<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
" />
                        <input type="hidden" id="header_ssotoken" name="ssotoken" value="<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
" />
                        <input type="hidden" id="header_fsaction" name="fsaction" value="<?php echo $_smarty_tpl->tpl_vars['logoutfsaction']->value;?>
" />
                        <input type="hidden" id="header_basketref" name="basketref" value="<?php echo $_smarty_tpl->tpl_vars['basketref']->value;?>
" />
                        <input type="hidden" id="header_webbrandcode" name="webbrandcode" value="<?php echo $_smarty_tpl->tpl_vars['webbrandcode']->value;?>
" />
                        <input type="hidden" id="header_csrf_token"  name="csrf_token" value="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
                    </form>
                    <span>
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSignOut');?>

                    </span>
                    <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/sign_out_icon_v2.png" alt="" />
                </div>
                <div class="clear"></div>
            </div>
            <div id="contentScroll" class="contentScroll">
                <?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?>
                    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebarleft']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                <?php }?>
                <div id="contentHolder">
                    <?php if ($_smarty_tpl->tpl_vars['hasflaggedonlineprojects']->value) {?>
                        <div id="flaggedForPurge" class="warning-bar"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageMainPurgeWarning');?>
 <a href="#" id="viewOnlineProjects" data-decorator="fnVisitCheckProjects" data-link="<?php echo $_smarty_tpl->tpl_vars['checkprojectslink']->value;?>
" data-internal="<?php echo $_smarty_tpl->tpl_vars['hasonlinedesignerurl']->value;?>
"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitlePurgeWarningLink');?>
</a></div>
                    <?php }?>
                    <!-- ACCOUNT DETAILS -->
                    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'accountdetails') {?>
					<div id="loadingBox" class="section maw_dialog">
                            <div class="dialogTop">
                                <h2 id="loadingTitle" class="title-bar"></h2>
                            </div>
                            <div class="content">
                                <div class="loadingMessage">
                                    <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/loading_shoppingcart_v2.gif" class="loading-icon" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageLoading');?>
" />
                                </div>
                            </div>
                        </div>
                        <div id="shimLoading">&nbsp;</div>
                        <div id="pageFooterHolder" <?php if ($_smarty_tpl->tpl_vars['sidebaraccount']->value == '' && $_smarty_tpl->tpl_vars['sidebarcontactdetails']->value == '') {?>class="fullsizepage"<?php }?>>
                            <div id="page" class="section">
                                <h2 class="title-bar">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleAccountDetails');?>

                                </h2>
                                <form method="post" id="mainform" name="mainform" action="#">
                                    <div class="content contentForm">
                                        <div class="message<?php if ($_smarty_tpl->tpl_vars['isConfirmation']->value == 1) {?> confirmation<?php }?>" id="message"><?php echo $_smarty_tpl->tpl_vars['message']->value;?>
</div>
                                        <div class="top_gap account-section">
                                            <label for="email"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailAddress');?>
:</label>
                                            <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                                            <input type="text" id="email_account" name="email_account" value="<?php echo $_smarty_tpl->tpl_vars['email']->value;?>
" />
                                            <img class="error_form_image" id="emailcompulsory" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                                            <div class="clear"></div>

											<?php if ($_smarty_tpl->tpl_vars['showPendingMessage']->value == 1) {?>
												<div class="informationContainer">
													<p class="informationHeader"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleEmailChangePending');?>
</p>
													<p class="informationMessage"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageEmailChangePending');?>
</p>
												</div>
											<?php }?>
                                        </div>
                                        <div id="ajaxdiv" class="top_gap"></div>
                                        <div class="top_gap">
                                            <label for="telephonenumber"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelTelephoneNumber');?>
:</label>
                                            <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                                            <input type="text" id="telephonenumber_account" name="telephonenumber_account" value="<?php echo $_smarty_tpl->tpl_vars['telephonenumber']->value;?>
" />
                                            <img class="error_form_image" id="telephonenumbercompulsory" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="note">
                                            <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
                                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompulsoryFields');?>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="buttonBottom">
                            <?php if ($_smarty_tpl->tpl_vars['addressupdated']->value != 0) {?>
                            <div class="btnLeft">
                                <div class="contentBtn" id="backButton">
                                    <div class="btn-blue-arrow-left" ></div>
                                    <div class="btn-blue-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonBack');?>
</div>
                                    <div class="btn-blue-right"></div>
                                </div>
                            </div>
                        	<?php }?>
                            <div class="btnRight">
                                <div class="contentBtn" id="updateButton">
                                    <div class="btn-green-left" ></div>
                                    <div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdate');?>
</div>
                                    <div class="btn-accept-right"></div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    <?php }?>
                    <!-- END ACCOUNT DETAILS -->

                    <!-- CHANGE PASSWORD -->
                    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'changepassword') {?>
                        <div id="pageFooterHolder" <?php if ($_smarty_tpl->tpl_vars['sidebaraccount']->value == '' && $_smarty_tpl->tpl_vars['sidebarcontactdetails']->value == '') {?>class="fullsizepage"<?php }?>>
                            <div id="page" class="section">
                                <h2 class="title-bar">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleChangePassword');?>

                                </h2>
                                <form action="#" method="post" id="changePassword">
                                    <div class="contentForm content">
                                        <div class="message<?php if ($_smarty_tpl->tpl_vars['isConfirmation']->value == 1) {?> confirmation<?php }?>" id="message">
                                            <?php echo $_smarty_tpl->tpl_vars['message']->value;?>

                                        </div>
                                        <div>
                                            <label for="oldpassword"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCurrentPassword');?>
:</label>
                                            <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                                            <input type="password" id="oldpassword" name="oldpassword" value="" class="middle" />
                                            <div class="clear"></div>
                                        </div>
                                        <div class="top_gap">
                                            <label for="newpassword"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNewPassword');?>
: </label>
                                            <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                                            <div class="password-input-wrap">
                                                <div class="password-background">
                                                    <input type="password" id="newpassword" name="newpassword" value="" class="middle" />
                                                    <button type="button" id="togglenewpassword" class="password-visibility password-show"></button>
                                                </div>
                                                <div class="progress-wrap">
                                                    <progress id="strengthvalue" value="0" min="0" max="5"></progress>
                                                    <p><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPasswordStrength');?>
: <span id="strengthtext"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStartTyping');?>
</span></p>
                                                </div>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="note">
                                            <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
                                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompulsoryFields');?>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="buttonBottom">
                            <div class="btnLeft">
                                <div class="contentBtn" id="backButton">
                                    <div class="btn-blue-arrow-left" ></div>
                                    <div class="btn-blue-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonBack');?>
</div>
                                    <div class="btn-blue-right"></div>
                                </div>
                            </div>
                            <div class="btnRight">
                                <div class="contentBtn" id="updateButton">
                                    <div class="btn-green-left" ></div>
                                    <div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdate');?>
</div>
                                    <div class="btn-accept-right"></div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    <?php }?>
                    <!-- END CHANGE PASSWORD -->

                    <!-- CHANGE PREFERENCES -->
                    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'changepreferences') {?>
                        <div id="pageFooterHolder" <?php if ($_smarty_tpl->tpl_vars['sidebaraccount']->value == '' && $_smarty_tpl->tpl_vars['sidebarcontactdetails']->value == '') {?>class="fullsizepage"<?php }?>>
                            <div id="page" class="section">
                                <h2 class="title-bar">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleChangePreferences');?>

                                </h2>
                                <form id="changePreferencesForm">
                                    <div class="content contentForm log-in-wrap update-preferences">
                                        <div class="message<?php if ($_smarty_tpl->tpl_vars['isConfirmation']->value == 1) {?> confirmation<?php }?>" id="message">
                                            <?php echo $_smarty_tpl->tpl_vars['message']->value;?>

                                        </div>
                                        <div class="top_gap">
                                            <input type="checkbox" name="sendmarketinginfo" id="subscribed" class="widthAuto" <?php if ($_smarty_tpl->tpl_vars['sendmarketinginfo']->value == 1) {?> checked="checked"<?php }?> />
                                            <label class="widthAuto" for="subscribed">
                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMarketingSubscribe');?>

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
                                    <div class="btn-blue-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonBack');?>
</div>
                                    <div class="btn-blue-right"></div>
                                </div>
                            </div>
                            <div class="btnRight">
                                <div class="contentBtn" id="updateButton">
                                    <div class="btn-green-left" ></div>
                                    <div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdate');?>
</div>
                                    <div class="btn-accept-right"></div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>


                    <?php }?>
                    <!-- END CHANGE PREFERENCES -->

					<?php if ($_smarty_tpl->tpl_vars['section']->value == 'yourorders' || $_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects') {?>

						<div id="loadingBox" class="section maw_dialog">
                            <div class="dialogTop">
                                <h2 id="loadingTitle" class="title-bar"></h2>
                            </div>
                            <div class="content">
                                <div class="loadingMessage">
                                    <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/loading_shoppingcart_v2.gif" class="loading-icon" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageLoading');?>
" />
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
								<input type="hidden" id="tzoffset" value = "<?php echo $_smarty_tpl->tpl_vars['tzoffset']->value;?>
" />

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
					<?php }?>

                    <!-- YOUR ORDERS -->
                    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'yourorders') {?>
                        <div id="pageFooterHolder" <?php if ($_smarty_tpl->tpl_vars['sidebaraccount']->value == '' && $_smarty_tpl->tpl_vars['sidebarcontactdetails']->value == '') {?>class="fullsizepage"<?php }?>>
                            <div id="page" class="section">
                                <div class="title-bar">
                                    <div class="title-current"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleItem');?>
</div>
                                    <div class="title-status"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
</div>
                                    <div class="title-price"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelHeaderPrice');?>
</div>
                                    <div class="clear"></div>
                                </div>
                                <?php if ($_smarty_tpl->tpl_vars['tempordercount']->value == 0) {?>
                                    <div class="content contentNoPaddingSide" id="content">
                                    <?php } else { ?>
                                        <div class="content" id="content">
                                            <?php if ($_smarty_tpl->tpl_vars['sectiontitle']->value != '') {?>
                                                <h2 class="title-bar-current warning-status">
                                                    <?php echo $_smarty_tpl->tpl_vars['sectiontitle']->value;?>

                                                </h2>
                                                <div class="currentBlocRow">
                                                <?php }?>
                                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['temporderlist']->value, 'row', false, NULL, 'orders', array (
  'last' => true,
  'iteration' => true,
  'total' => true,
));
$_smarty_tpl->tpl_vars['row']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['row']->value) {
$_smarty_tpl->tpl_vars['row']->do_else = false;
$_smarty_tpl->tpl_vars['__smarty_foreach_orders']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_orders']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_orders']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_orders']->value['total'];
?>
                                                    <div id="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectref'];?>
" class="contentRow<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_orders']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_orders']->value['last'] : null)) {?> noBorder<?php }?>">
                                                        <?php
$__section_productloop_0_loop = (is_array(@$_loop=sizeof($_smarty_tpl->tpl_vars['row']->value['product'])) ? count($_loop) : max(0, (int) $_loop));
$__section_productloop_0_total = min(ceil(($__section_productloop_0_loop - 0)/ 3), $__section_productloop_0_loop);
$_smarty_tpl->tpl_vars['__smarty_section_productloop'] = new Smarty_Variable(array());
if ($__section_productloop_0_total !== 0) {
for ($__section_productloop_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_productloop']->value['index'] = 0; $__section_productloop_0_iteration <= $__section_productloop_0_total; $__section_productloop_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_productloop']->value['index'] += 3){
?>
                                                            <div class="bloc_content">
                                                                <?php
$__section_indexProduct_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['row']->value['product']) ? count($_loop) : max(0, (int) $_loop));
$__section_indexProduct_1_start = (int)@(isset($_smarty_tpl->tpl_vars['__smarty_section_productloop']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_productloop']->value['index'] : null) < 0 ? max(0, (int)@(isset($_smarty_tpl->tpl_vars['__smarty_section_productloop']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_productloop']->value['index'] : null) + $__section_indexProduct_1_loop) : min((int)@(isset($_smarty_tpl->tpl_vars['__smarty_section_productloop']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_productloop']->value['index'] : null), $__section_indexProduct_1_loop);
$__section_indexProduct_1_total = min(($__section_indexProduct_1_loop - $__section_indexProduct_1_start), 3);
$_smarty_tpl->tpl_vars['__smarty_section_indexProduct'] = new Smarty_Variable(array());
if ($__section_indexProduct_1_total !== 0) {
for ($__section_indexProduct_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] = $__section_indexProduct_1_start; $__section_indexProduct_1_iteration <= $__section_indexProduct_1_total; $__section_indexProduct_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']++){
?>
                                                                    <div class="previewHolder">
                                                                        <div class="previewItem" orderid="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['id'];?>
">
                                                                            <div id="img_<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectref'];?>
" class="product-preview-wrap">
                                                                                <?php $_smarty_tpl->_assignInScope('thumbnailpath', '');?>
                                                                                <?php if ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['previewimage'] !== '') {?>
                                                                                    <?php ob_start();
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['previewimage'], ENT_QUOTES, 'UTF-8', true);
$_prefixVariable1=ob_get_clean();
$_smarty_tpl->_assignInScope('thumbnailpath', ((string)$_smarty_tpl->tpl_vars['onlinedesignerurl']->value).$_prefixVariable1);?>
                                                                                <?php }?>

                                                                                <?php if ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectpreviewthumbnail'] != '') {?>
                                                                                    <img class="product-preview-image" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectpreviewthumbnail'], ENT_QUOTES, 'UTF-8', true);?>
" data-asset="<?php echo $_smarty_tpl->tpl_vars['thumbnailpath']->value;?>
" alt="" />
                                                                                <?php } elseif ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['thumbnailpath'] != '') {?>
                                                                                    <img class="product-preview-image" src="<?php echo $_smarty_tpl->tpl_vars['thumbnailpath']->value;?>
" data-asset="" alt="" />
                                                                                <?php } else { ?>
                                                                                    <img class="product-preview-image" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/no_image-2x.jpg" alt="" />
                                                                                <?php }?>
                                                                            </div>
                                                                            <div class="previewItemText">
                                                                                <div class="textProduct">
                                                                                    <?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectname'];?>

                                                                                </div>
                                                                                <div class="contentDescription">
                                                                                    <div class="description-product">
                                                                                        <?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['productname'];?>

                                                                                    </div>
                                                                                    <div class="ordernumber">
                                                                                        <span class="label-order-number"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderNum');?>
:</span> <?php echo $_smarty_tpl->tpl_vars['row']->value['ordernumber'];?>

                                                                                    </div>
                                                                                    <div class="ordernumber">
                                                                                        <span class="label-order-number"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderDate');?>
:</span> <?php echo $_smarty_tpl->tpl_vars['row']->value['formattedorderdate'];?>

                                                                                    </div>
                                                                                </div>
                                                                                <div class="descriptionStatus">
                                                                                    <?php if ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['status'] == 0 && $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['source'] == 0) {?>
                                                                                        <span class="previewItemDetail textRed">
                                                                                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusWaitingForFiles');?>

                                                                                        </span>
                                                                                    <?php } elseif ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['status'] == 0 && $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['source'] == 1) {?>
                                                                                        <span class="previewItemDetail textRed">
                                                                                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusWaitingForPayment');?>

                                                                                        </span>
                                                                                    <?php } elseif ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['status'] < 60) {?>
                                                                                        <span class="previewItemDetail textRed">
                                                                                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusWaitingForPayment');?>

                                                                                        </span>
                                                                                    <?php }?>
                                                                                </div>
                                                                                <div class="descriptionPrice">

                                                                                    <?php if (sizeof($_smarty_tpl->tpl_vars['row']->value['product']) == 1) {?>

                                                                                        <?php echo $_smarty_tpl->tpl_vars['row']->value['formattedordertotal'];?>


                                                                                    <?php }?>

                                                                                </div>
                                                                                <?php if (($_smarty_tpl->tpl_vars['row']->value['status'] > 0) && ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['parentorderitemid'] == 0)) {?>
                                                                                    <div class="clear"></div>
                                                                                    <div class="btnLinks">
                                                                                        <div id="executePayNowButton" data-ref="<?php echo $_smarty_tpl->tpl_vars['row']->value['sessionid'];?>
" >
                                                                                            <div class="btn-green-left" ></div>
                                                                                            <div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPayNow');?>
</div>
                                                                                            <div class="btn-green-right"></div>
                                                                                        </div>
                                                                                        <?php if ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['previewsonline'] == 1) {?>
                                                                                            <div class="browserPreviewButton" data-baseurl="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['previewurl'], ENT_QUOTES, 'UTF-8', true);?>
" data-ref="<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
" data-productid="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['id'];?>
" data-ssotoken="<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
">
                                                                                                <div class="btn-white-left" ></div>
                                                                                                <div class="btn-white-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonPreview');?>
</div>
                                                                                                <div class="btn-white-right"></div>
                                                                                            </div>
                                                                                        <?php }?>
                                                                                    </div>
                                                                                <?php } else { ?>
                                                                                    <?php if (($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['previewsonline'] == 1) && ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['parentorderitemid'] == 0)) {?>
																						<div class="clear"></div>
                                                                                        <div class="btnLinks">
                                                                                            <div class="browserPreviewButton" data-baseurl="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['previewurl'], ENT_QUOTES, 'UTF-8', true);?>
" data-ref="<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
" data-productid="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['id'];?>
" data-ssotoken="<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
">
                                                                                                <div class="btn-white-left" ></div>
                                                                                                <div class="btn-white-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonPreview');?>
</div>
                                                                                                <div class="btn-white-right"></div>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php }?>
                                                                                <?php }?>
                                                                            </div>
                                                                        </div>
                                                                        <?php if ((sizeof($_smarty_tpl->tpl_vars['row']->value['product']) != 1) && ((sizeof($_smarty_tpl->tpl_vars['row']->value['product']))-1 == (isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null))) {?>
                                                                            <div class="mulitLineSubTotal">
                                                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotalMultiLine');?>
: <?php echo $_smarty_tpl->tpl_vars['row']->value['formattedordertotal'];?>

                                                                            </div>
                                                                        <?php }?>
                                                                    </div>
                                                                <?php
}
}
?>
                                                                <div class="clear"></div>
                                                            </div>
                                                        <?php
}
}
?>
                                                    </div>
                                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                                <?php if ($_smarty_tpl->tpl_vars['sectiontitle']->value != '') {?>
                                                </div>
                                            <?php }?>
                                        <?php }?>

                                        <?php if ($_smarty_tpl->tpl_vars['ordercount']->value > 0) {?>
                                            <?php if ($_smarty_tpl->tpl_vars['sectiontitle2']->value != '') {?>
                                                <h2 class="title-bar-current">
                                                    <?php echo $_smarty_tpl->tpl_vars['sectiontitle2']->value;?>

                                                </h2>
                                                <div class="currentBlocRow noBottom">
                                                <?php }?>
                                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderlist']->value, 'row', false, NULL, 'orders', array (
  'last' => true,
  'iteration' => true,
  'total' => true,
));
$_smarty_tpl->tpl_vars['row']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['row']->value) {
$_smarty_tpl->tpl_vars['row']->do_else = false;
$_smarty_tpl->tpl_vars['__smarty_foreach_orders']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_orders']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_orders']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_orders']->value['total'];
?>
                                                    <div  class="contentRow<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_orders']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_orders']->value['last'] : null)) {?> noBorder<?php }?>">
                                                        <?php
$__section_productloop_2_loop = (is_array(@$_loop=sizeof($_smarty_tpl->tpl_vars['row']->value['product'])) ? count($_loop) : max(0, (int) $_loop));
$__section_productloop_2_total = min(ceil(($__section_productloop_2_loop - 0)/ 3), $__section_productloop_2_loop);
$_smarty_tpl->tpl_vars['__smarty_section_productloop'] = new Smarty_Variable(array());
if ($__section_productloop_2_total !== 0) {
for ($__section_productloop_2_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_productloop']->value['index'] = 0; $__section_productloop_2_iteration <= $__section_productloop_2_total; $__section_productloop_2_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_productloop']->value['index'] += 3){
?>
                                                            <div class="bloc_content">
                                                                <div class="order-details">
                                                                    <div class="order-number"><span class="label-order-number"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderNum');?>
:</span> <?php echo $_smarty_tpl->tpl_vars['row']->value['ordernumber'];?>
</div>
                                                                    <div class="order-delete">
                                                                        <?php if ($_smarty_tpl->tpl_vars['row']->value['orderstatus'] > 0) {?>
                                                                        <div class="deleteOrderButton" data-orderid="<?php echo $_smarty_tpl->tpl_vars['row']->value['orderid'];?>
" data-ordernumber="<?php echo $_smarty_tpl->tpl_vars['row']->value['ordernumber'];?>
" data-ref="<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
" data-ssotoken="<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
">
                                                                            <div class="btn-red-left" ></div>
                                                                            <div class="btn-red-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDelete');?>
</div>
                                                                            <div class="btn-red-right"></div>
                                                                        </div>
                                                                        <?php }?>
                                                                    </div>
                                                                    <div class="clear"></div>
                                                                </div>
                                                                <?php
$__section_indexProduct_3_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['row']->value['product']) ? count($_loop) : max(0, (int) $_loop));
$__section_indexProduct_3_start = (int)@(isset($_smarty_tpl->tpl_vars['__smarty_section_productloop']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_productloop']->value['index'] : null) < 0 ? max(0, (int)@(isset($_smarty_tpl->tpl_vars['__smarty_section_productloop']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_productloop']->value['index'] : null) + $__section_indexProduct_3_loop) : min((int)@(isset($_smarty_tpl->tpl_vars['__smarty_section_productloop']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_productloop']->value['index'] : null), $__section_indexProduct_3_loop);
$__section_indexProduct_3_total = min(($__section_indexProduct_3_loop - $__section_indexProduct_3_start), 3);
$_smarty_tpl->tpl_vars['__smarty_section_indexProduct'] = new Smarty_Variable(array());
if ($__section_indexProduct_3_total !== 0) {
for ($__section_indexProduct_3_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] = $__section_indexProduct_3_start; $__section_indexProduct_3_iteration <= $__section_indexProduct_3_total; $__section_indexProduct_3_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']++){
?>
                                                                    <div class="previewHolder">
                                                                        <div class="previewItem" orderid="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['id'];?>
" id="orderitemid<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['id'];?>
" data-projectname="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectname'];?>
">
                                                                            <div id="img_<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectref'];?>
" class="product-preview-wrap">
                                                                                <?php $_smarty_tpl->_assignInScope('thumbnailpath', '');?>
                                                                                <?php if ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['previewimage'] !== '') {?>
                                                                                    <?php ob_start();
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['previewimage'], ENT_QUOTES, 'UTF-8', true);
$_prefixVariable2=ob_get_clean();
$_smarty_tpl->_assignInScope('thumbnailpath', ((string)$_smarty_tpl->tpl_vars['onlinedesignerurl']->value).$_prefixVariable2);?>
                                                                                <?php }?>

                                                                                <?php if ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectpreviewthumbnail'] != '') {?>
                                                                                    <img class="product-preview-image" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectpreviewthumbnail'], ENT_QUOTES, 'UTF-8', true);?>
" data-asset="<?php echo $_smarty_tpl->tpl_vars['thumbnailpath']->value;?>
" alt="" />
                                                                                <?php } elseif ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['thumbnailpath'] != '') {?>
                                                                                    <img class="product-preview-image" src="<?php echo $_smarty_tpl->tpl_vars['thumbnailpath']->value;?>
" data-asset="" alt="" />
                                                                                <?php } else { ?>
                                                                                    <img class="product-preview-image" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/no_image-2x.jpg" alt="" />
                                                                                <?php }?>
                                                                            </div>
                                                                            <div class="previewItemText">
                                                                                <div class="textProduct">
                                                                                    <?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectname'];?>

                                                                                </div>
                                                                                <div class="contentDescription">
                                                                                    <div class="description-product">
                                                                                        <?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['productname'];?>

                                                                                    </div>
                                                                                    <div class="ordernumber">
                                                                                        <span class="label-order-number"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderNum');?>
:</span> <?php echo $_smarty_tpl->tpl_vars['row']->value['ordernumber'];?>

                                                                                    </div>
                                                                                    <div class="ordernumber">
                                                                                        <span class="label-order-number"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderDate');?>
:</span> <?php echo $_smarty_tpl->tpl_vars['row']->value['formattedorderdate'];?>

                                                                                    </div>
                                                                                </div>
                                                                                <div class="descriptionStatus">
                                                                                    <?php if ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['orderstatus'] == 0) {?>
                                                                                        <?php if ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['status'] == 0 && $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['source'] == 0) {?>
                                                                                            <span class="previewItemDetail textRed">
                                                                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusWaitingForFiles');?>

                                                                                            </span>
                                                                                        <?php } elseif ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['status'] == 0 && $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['source'] == 1) {?>
                                                                                            <span class="previewItemDetail textBlue">
                                                                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusInProduction');?>

                                                                                            </span>
                                                                                        <?php } elseif ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['status'] == 60) {?>
                                                                                             <span class="previewItemDetail textGreen">
                                                                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusShipped');?>

                                                                                            </span>
																						<?php } elseif ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['status'] == 65) {?>
                                                                                             <span class="previewItemDetail textGreen">
                                                                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusReadyToCollectAtStore');?>

                                                                                            </span>
																						<?php } elseif ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['status'] == 66) {?>
                                                                                             <span class="previewItemDetail textGreen">
                                                                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusCompleted');?>

                                                                                            </span>																							
                                                                                        <?php } else { ?>
																							<span class="previewItemDetail textBlue">
                                                                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusInProduction');?>

                                                                                            </span>
                                                                                        <?php }?>
                                                                                    <?php } elseif ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['orderstatus'] == 1) {?>
                                                                                        <span class="previewItemDetail textRed">
                                                                                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusCancelled');?>

                                                                                        </span>
                                                                                    <?php } else { ?>
                                                                                        <span class="previewItemDetail textGreen">
                                                                                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusCompleted');?>

                                                                                        </span>
                                                                                    <?php }?>
                                                                                    <?php if ($_smarty_tpl->tpl_vars['row']->value['showpaymentstatus'] == 1 && sizeof($_smarty_tpl->tpl_vars['row']->value['product']) == 1) {?>
                                                                                        <?php if ($_smarty_tpl->tpl_vars['row']->value['paymentreceived'] == 1) {?>
                                                                                            <p class="paymentstatus textGreen"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusPaymentReceived');?>
</p>
                                                                                        <?php } else { ?>
                                                                                            <p class="paymentstatus textOrange"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusWaitingForPayment');?>
</p>
                                                                                    <?php }?>
                                                                                <?php }?>
                                                                                </div>
                                                                                <div class="descriptionPrice">
                                                                                    <?php if (sizeof($_smarty_tpl->tpl_vars['row']->value['product']) == 1) {?>
                                                                                        <?php echo $_smarty_tpl->tpl_vars['row']->value['formattedordertotal'];?>

                                                                                    <?php }?>
                                                                                </div>
                                                                                <div class="clear"></div>
                                                                                <div class="btnLinks">

																					<?php if (($_smarty_tpl->tpl_vars['row']->value['status'] > 0) && ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['source'] == 1) && ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['parentorderitemid'] == 0) && ($_smarty_tpl->tpl_vars['row']->value['orderstatus'] == 0) && ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['canmodify'] == 1) && ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['isowner'] == 1)) {?>

																						<div class="continueEditingButton yourOrderActionButton" data-productid="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['id'];?>
" data-buttonaction=3 data-projectname="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectname'], ENT_QUOTES, 'UTF-8', true);?>
" data-webbrandapplicationname="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['webbrandapplicationname']->value, ENT_QUOTES, 'UTF-8', true);?>
" data-projectref="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectref'];?>
" data-workflowtype=<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['workflowtype'];?>
 data-indent="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['productindent'];?>
">
																							<div class="btn-white-left" ></div>
																							<div class="btn-white-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonContinueEditing');?>
</div>
																							<div class="btn-white-right"></div>
																						</div>

																					<?php }?>

																					<?php if (($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['parentorderitemid'] == 0)) {?>
																						<?php if (((($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['source'] == 1) && ($_smarty_tpl->tpl_vars['ishighlevel']->value == 0) && ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['isowner'] == 1)) || (($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['source'] == 1) && ($_smarty_tpl->tpl_vars['ishighlevel']->value == 1) && ($_smarty_tpl->tpl_vars['basketref']->value != '') && ($_smarty_tpl->tpl_vars['basketref']->value != 'tpxgnbr') && ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['isowner'] == 1))) && ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['dataavailable'] == 1)) {?>

																							<div class="duplicateButton yourOrderActionButton" data-productid="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['id'];?>
" data-buttonaction=4 data-projectname="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectname'], ENT_QUOTES, 'UTF-8', true);?>
" data-webbrandapplicationname="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['webbrandapplicationname']->value, ENT_QUOTES, 'UTF-8', true);?>
" data-projectref="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectref'];?>
" data-workflowtype=<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['workflowtype'];?>
 data-indent="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['productindent'];?>
">
																								<div class="btn-white-left" ></div>
																								<div class="btn-white-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDuplicateProject');?>
</div>
																								<div class="btn-white-right"></div>
																							</div>

																						<?php }?>
																					<?php }?>

                                                                                	<!-- always allow a re-order as long as files have been received and canmodify is not set -->
                                                                                	<?php if (($_smarty_tpl->tpl_vars['row']->value['status'] > 0) && ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['canreorder'] == $_smarty_tpl->tpl_vars['kCanReorder']->value) && ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['parentorderitemid'] == 0)) {?>

																						<div class="reorderButton yourOrderActionButton" data-productid="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['id'];?>
" data-buttonaction=1 data-projectname="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectname'], ENT_QUOTES, 'UTF-8', true);?>
" data-webbrandapplicationname="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['webbrandapplicationname']->value, ENT_QUOTES, 'UTF-8', true);?>
" data-projectref="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectref'];?>
" data-workflowtype=<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['workflowtype'];?>
 data-indent="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['productindent'];?>
">
																							<div class="btn-green-left" ></div>
																							<div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelReorder');?>
</div>
																							<div class="btn-green-right"></div>
																						</div>

																					<?php }?>
																					<!-- add the actions that can only occur on non cancelled orders -->
                                                                                    <?php if (($_smarty_tpl->tpl_vars['row']->value['orderstatus'] != 1) && ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['parentorderitemid'] == 0)) {?>
                                                                                        <?php if ($_smarty_tpl->tpl_vars['row']->value['status'] != 0) {?>
                                                                                            <?php if ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['isShared'] == true) {?>
                                                                                                <div class="unshareButton yourOrderActionButton" data-productid="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['id'];?>
" data-buttonaction=2 data-projectname="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectname'], ENT_QUOTES, 'UTF-8', true);?>
" data-webbrandapplicationname="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['webbrandapplicationname']->value, ENT_QUOTES, 'UTF-8', true);?>
" data-projectref="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectref'];?>
" data-workflowtype=<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['workflowtype'];?>
 data-indent="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['productindent'];?>
">
                                                                                                    <div class="btn-white-left" ></div>
                                                                                                    <div class="btn-white-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUnshare');?>
</div>
                                                                                                    <div class="btn-white-right"></div>
                                                                                                </div>
                                                                                            <?php }?>

                                                                                            <?php if (($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['dataavailable'] == 1) && ($_smarty_tpl->tpl_vars['row']->value['origorderid'] == 0)) {?>
                                                                                                <div class="shareButton yourOrderActionButton" data-productid="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['id'];?>
" data-buttonaction=0 data-projectname="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectname'], ENT_QUOTES, 'UTF-8', true);?>
" data-webbrandapplicationname="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['webbrandapplicationname']->value, ENT_QUOTES, 'UTF-8', true);?>
" data-projectref="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectref'];?>
" data-workflowtype=<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['workflowtype'];?>
 data-indent="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['productindent'];?>
">
                                                                                                    <div class="btn-white-left" ></div>
                                                                                                    <div class="btn-white-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShare');?>
</div>
                                                                                                    <div class="btn-white-right"></div>
                                                                                                </div>
                                                                                            <?php }?>

                                                                                        <?php } elseif ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['source'] == 1) {?>
                                                                                            <?php if ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['isShared'] == true) {?>
                                                                                                <div class="unshareButton yourOrderActionButton" data-productid="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['id'];?>
" data-buttonaction=2 data-projectname="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectname'], ENT_QUOTES, 'UTF-8', true);?>
" data-webbrandapplicationname="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['webbrandapplicationname']->value, ENT_QUOTES, 'UTF-8', true);?>
" data-projectref="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectref'];?>
" data-workflowtype=<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['workflowtype'];?>
 data-indent="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['productindent'];?>
">
                                                                                                    <div class="btn-white-left" ></div>
                                                                                                    <div class="btn-white-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUnshare');?>
</div>
                                                                                                    <div class="btn-white-right"></div>
                                                                                                </div>
                                                                                            <?php }?>
                                                                                            <?php if (($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['dataavailable'] == 1)) {?>
                                                                                                <div class="shareButton yourOrderActionButton" data-productid="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['id'];?>
" data-buttonaction=0 data-projectname="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectname'], ENT_QUOTES, 'UTF-8', true);?>
" data-webbrandapplicationname="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['webbrandapplicationname']->value, ENT_QUOTES, 'UTF-8', true);?>
" data-projectref="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['projectref'];?>
" data-workflowtype=<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['workflowtype'];?>
 data-indent="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['productindent'];?>
">
                                                                                                    <div class="btn-white-left" ></div>
                                                                                                    <div class="btn-white-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShare');?>
</div>
                                                                                                    <div class="btn-white-right"></div>
                                                                                                </div>
                                                                                            <?php }?>

                                                                                        <?php }?>

                                                                                        <?php if ($_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['previewsonline'] == 1) {?>
                                                                                            <div class="browserPreviewButton" data-baseurl="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['previewurl'], ENT_QUOTES, 'UTF-8', true);?>
" data-ref="<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
" data-productid="<?php echo $_smarty_tpl->tpl_vars['row']->value['product'][(isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null)]['id'];?>
" data-ssotoken="<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
">
                                                                                                <div class="btn-white-left" ></div>
                                                                                                <div class="btn-white-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonPreview');?>
</div>
                                                                                                <div class="btn-white-right"></div>
                                                                                            </div>
                                                                                        <?php }?>
                                                                                    <?php }?>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <?php if ((sizeof($_smarty_tpl->tpl_vars['row']->value['product']) != 1) && ((sizeof($_smarty_tpl->tpl_vars['row']->value['product']))-1 == (isset($_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_indexProduct']->value['index'] : null))) {?>
                                                                            <div class="mulitLineSubTotal">
                                                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotalMultiLine');?>
: <?php echo $_smarty_tpl->tpl_vars['row']->value['formattedordertotal'];?>

                                                                                <?php if ($_smarty_tpl->tpl_vars['row']->value['showpaymentstatus'] == 1) {?>
                                                                                    <?php if ($_smarty_tpl->tpl_vars['row']->value['paymentreceived'] == 1) {?>
                                                                                        <p class="paymentstatus textGreen"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusPaymentReceived');?>
</p>
                                                                                    <?php } else { ?>
                                                                                        <p class="paymentstatus textOrange"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusWaitingForPayment');?>
</p>
                                                                                    <?php }?>
                                                                            <?php }?>
                                                                            </div>
                                                                        <?php }?>
                                                                    </div>
                                                                <?php
}
}
?>
                                                                <div class="clear"></div>
                                                            </div>
                                                        <?php
}
}
?>
                                                    </div>
                                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                                <?php if ($_smarty_tpl->tpl_vars['sectiontitle2']->value != '') {?>
                                                </div>
                                            <?php }?>
                                        <?php } else { ?>
                                            <?php if ($_smarty_tpl->tpl_vars['section']->value == 'yourorders') {?>
                                                <?php if ($_smarty_tpl->tpl_vars['tempordercount']->value == 0) {?>
                                                    <div class="emptyBox">
                                                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNoActiveOrders');?>

                                                    </div>
                                                <?php }?>
                                            <?php }?>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                            <div class="buttonBottom">
                                <div class="btnLeft">
                                    <div class="contentBtn" id="backButton">
                                        <div class="btn-blue-arrow-left" ></div>
                                        <div class="btn-blue-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonBack');?>
</div>
                                        <div class="btn-blue-right"></div>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        <?php }?>
                        <!-- END YOUR ORDERS -->

                        <!-- BEGIN DISPLAY EXISTING ONLINE PROJECTS -->

                        <?php if ($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects') {?>

                        <div id="confirmationBox" class="section maw_dialog">
                            <div id="confirmationBoxTop" class="dialogTop">
                                <h2 class="title-bar">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>

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
                                            <div class="btn-blue-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelClose');?>
</div>
                                            <div class="btn-blue-right"></div>
                                        </div>
                                    </div>
                                    <div class="btnRight">
                                        <div class="contentBtn unshareConfirmContainer">
                                            <div class="btn-green-left" ></div>
                                            <div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUnshare');?>
</div>
                                            <div class="btn-accept-right"></div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>

                        <div id="pageFooterHolder" <?php if ($_smarty_tpl->tpl_vars['sidebaraccount']->value == '' && $_smarty_tpl->tpl_vars['sidebarcontactdetails']->value == '') {?>class="fullsizepage"<?php }?>>
                            <div id="page" class="section">
                                <?php if ($_smarty_tpl->tpl_vars['showpurgeall']->value) {?>
                                    <div id="purgeAllMessage" class="purgeAllMessage">
                                        <p><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageProjectsFlaggedForPurge');?>
 <a href="#" id="purgeAllLink"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleteAllFlaggedProjects');?>
</a></p>
                                    </div>
                                <?php }?>

                                <div class="title-bar">
                                    <div class="title-current"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProjects');?>
</div>
                                    <div class="title-status-right"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
</div>
                                    <div class="clear"></div>
                                </div>

                                <div id="content" class="content contentNoPaddingSide">

                                    <?php if ($_smarty_tpl->tpl_vars['maintenancemode']->value == true) {?>

                                        <div class="emptyBox">
                                           <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorMaintenanceMode');?>

                                        </div>

                                    <?php } else { ?>

                                        <?php if (sizeof($_smarty_tpl->tpl_vars['projects']->value) > 0) {?>
                                            <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['simpleDialog']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>

                                            <div class="projectlist" id="existingOnlineProjectList">

                                            <?php
$__section_index_4_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['projects']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_4_total = $__section_index_4_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_4_total !== 0) {
for ($__section_index_4_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_4_iteration <= $__section_index_4_total; $__section_index_4_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
?>

                                                <div class="contentRow<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_projects']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_projects']->value['last'] : null)) {?> noBorder<?php }?>"
                                                            id="<?php echo $_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['projectref'];?>
"
    														data-projectname="<?php echo $_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"
                                                            data-productident="<?php echo $_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['productident'];?>
"
                                                            data-canedit="<?php echo $_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['canedit'];?>
"
    														data-candelete="<?php echo $_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['candelete'];?>
"
                                                            data-cancompleteorder="<?php echo $_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['cancompleteorder'];?>
"
                                                            data-projectstatus="<?php echo $_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['projectstatus'];?>
"
                                                            data-workflowtype="<?php echo $_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['workflowtype'];?>
">
                                                    <div class="bloc_content">
                                                        <div class="previewHolder projectRowHighLight">
                                                            <div class="previewItem">
                                                                <div id="img_<?php echo $_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['projectref'];?>
" class="product-preview-wrap">
                                                                    <?php $_smarty_tpl->_assignInScope('thumbnailpath', '');?>
                                                                    <?php if ($_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['thumbnailpath'] !== '') {?>
                                                                      <?php ob_start();
echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['thumbnailpath'], ENT_QUOTES, 'UTF-8', true);
$_prefixVariable3=ob_get_clean();
$_smarty_tpl->_assignInScope('thumbnailpath', ((string)$_smarty_tpl->tpl_vars['onlinedesignerurl']->value).$_prefixVariable3);?>
                                                                    <?php }?>

                                                                    <?php if ($_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['projectpreviewthumbnail'] != '') {?>

                                                                        <img class="product-preview-image" src="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['projectpreviewthumbnail'], ENT_QUOTES, 'UTF-8', true);?>
" data-asset="<?php echo $_smarty_tpl->tpl_vars['thumbnailpath']->value;?>
" alt="" />

                                                                    <?php } elseif ($_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['thumbnailpath'] != '') {?>

                                                                        <img class="product-preview-image" src="<?php echo $_smarty_tpl->tpl_vars['thumbnailpath']->value;?>
" data-asset="" alt="" />

                                                                    <?php } else { ?>

                                                                        <img class="product-preview-image" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/no_image-2x.jpg" alt="" />

                                                                    <?php }?>

                                                                </div>
                                                                <div class="previewItemText onlinePreview">
                                                                    <div class="textProduct" id="name_<?php echo $_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['projectref'];?>
">
                                                                        <?php echo $_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>

                                                                    </div>
                                                                    <div class="contentDescription">
                                                                        <div class="description-product">
                                                                            <?php echo $_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['productname'];?>

                                                                        </div>
                                                                        <?php if ($_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['dateofpurge'] != '') {?>
                                                                            <div class="dateofpurge">
                                                                                <span class="label-purge-date"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageProjectDueToBePurged');?>
 <?php echo $_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['dateofpurge'];?>
</span> <a href="#" class="keepProjectLink" data-projectref="<?php echo $_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['projectref'];?>
"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageKeepProject');?>
</a>
                                                                            </div>
                                                                        <?php }?>
                                                                        <div class="ordernumber">
                                                                            <span class="label-order-number"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCreated');?>
</span> <?php echo $_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['datecreated'];?>

                                                                        </div>
                                                                    </div>
                                                                    <div class="online-production-status">

                                                                        <span id="statusDescription<?php echo $_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['projectref'];?>
" class="previewItemDetail textGreen">
                                                                            <?php echo $_smarty_tpl->tpl_vars['projects']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['statusdescription'];?>

                                                                        </span>

                                                                    </div>
                                                                    <div class="clear"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>
                                                </div>
                                            <?php
}
}
?>
                                        </div>

                                        <?php } else { ?> 
                                        <div class="emptyBox">
                                           <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNoOnlineProject');?>

                                        </div>

                                        <?php }?>                                     <?php }?>
                                </div>

                                <?php if (sizeof($_smarty_tpl->tpl_vars['projects']->value) > 0) {?>

                                <div class="onlineproject_btnLinks">

									<?php if ($_smarty_tpl->tpl_vars['browsersupported']->value == false) {?>

								<div id="browserNotSupported">
									<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageBrowserNotSupported');?>

								</div>

									<?php }?>

                                    <div class="online-buttons" id="completeBtn">
                                        <div id="completeBtnLeft" class="btn-disabled-left" ></div>
                                        <div id="completeBtnMiddle" class="btn-disabled-middle btnOnlineMiddle"><span><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCompleteOrder');?>
</span></div>
                                        <div id="completeBtnRight" class="btn-disabled-right"></div>
                                        <div class="clear"></div>
                                    </div>

                                    <div class="online-buttons" id="editBtn">
                                        <div id="editBtnLeft" class="btn-disabled-left" ></div>
                                        <div id="editBtnMiddle"class="btn-disabled-middle btnOnlineMiddle"><span><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonContinueEditing');?>
</span></div>
                                        <div id="editBtnRight"class="btn-disabled-right"></div>
                                        <div class="clear"></div>
                                    </div>

                                    <div class="online-buttons" id="duplicateBtn">
                                        <div id="duplicateBtnLeft" class="btn-disabled-left" ></div>
                                        <div id="duplicateBtnMiddle" class="btn-disabled-middle btnOnlineMiddle"><span><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDuplicateProject');?>
</span></div>
                                        <div id="duplicateBtnRight" class="btn-disabled-right"></div>
                                        <div class="clear"></div>
                                    </div>

                                    <div class="online-buttons" id="renameBtn">
                                        <div id="renameBtnLeft" class="btn-disabled-left" ></div>
                                        <div id="renameBtnMiddle" class="btn-disabled-middle btnOnlineMiddle"><span><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonRenameProject');?>
</span></div>
                                        <div id="renameBtnRight" class="btn-disabled-right"></div>
                                        <div class="clear"></div>
                                    </div>

                                    <div class="online-buttons" id="shareBtn">
                                        <div id="shareBtnLeft" class="btn-disabled-left" ></div>
                                        <div id="shareBtnMiddle" class="btn-disabled-middle btnOnlineMiddle"><span><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonShareProject');?>
</span></div>
                                        <div id="shareBtnRight" class="btn-disabled-right"></div>
                                        <div class="clear"></div>
                                    </div>

                                    <div class="online-buttons" id="deleteBtn">
                                        <div id="deleteBtnLeft" class="btn-disabled-left" ></div>
                                        <div id="deleteBtnMiddle" class="btn-disabled-middle btnOnlineMiddle"><span><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDeleteProject');?>
</span></div>
                                        <div id="deleteBtnRight" class="btn-disabled-right"></div>
                                        <div class="clear"></div>
                                    </div>

                                    <div class="clear"></div>

                                </div>

                                <?php }?> 
                            </div>
                        </div>

                        <?php }?>

                        <!-- END DISPLAY EXISTING ONLINE PROJECTS -->

                        <!-- MENU -->
                        <?php if ($_smarty_tpl->tpl_vars['section']->value == 'menu') {?>

                            <div id="pageFooterHolder" <?php if ($_smarty_tpl->tpl_vars['sidebaraccount']->value == '' && $_smarty_tpl->tpl_vars['sidebarcontactdetails']->value == '') {?> class="fullsizepage"<?php }?>>
                                <div id="page" class="section">
                                    <h2 class="title-bar">
                                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleMyAccount');?>

                                    </h2>
                                    <div class="content contentMenu" id="content">
                                        <div class="message<?php if ($_smarty_tpl->tpl_vars['isConfirmation']->value == 1) {?> confirmation<?php }?>" id="message">
                                            <?php echo $_smarty_tpl->tpl_vars['message']->value;?>

                                        </div>
                                        <div>
                                            <div class="menuItem menuItemCurrentOrder">
                                                <a href="#" class="menuActionButton" data-action="Customer.yourOrders">
                                                    <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/dashboard_icons/account_current_orders.png" alt="" /><br />
                                                    <span>
                                                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleYourOrders');?>

                                                    </span>
                                                </a>
                                            </div>

                                            <?php if ($_smarty_tpl->tpl_vars['hasonlinedesignerurl']->value == 1) {?>

                                                <div class="menuItem menuItemOnlineProject">
                                                    <a href="#" class="menuActionButton" data-action="Customer.displayOnlineProjectList">
                                                        <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/dashboard_icons/account_online_projects.png" alt=""><br>
                                                        <span>
                                                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleOnlineProjects');?>

                                                        </span>
                                                    </a>
                                                </div>

                                            <?php }?>

                                            <?php if ($_smarty_tpl->tpl_vars['canmodifyaccountdetails']->value == 1) {?>

                                                <div class="menuItem menuItemAccountDetails">
                                                    <a href="#" class="menuActionButton" data-action="Customer.accountDetails">
                                                        <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/dashboard_icons/account_details.png" alt="" /><br />
                                                        <span>
                                                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleAccountDetails');?>

                                                        </span>
                                                    </a>
                                                </div>

                                            <?php }?>

                                            <?php if ($_smarty_tpl->tpl_vars['canmodifypassword']->value == 1) {?>

                                                <div class="menuItem menuItemChangePassword">
                                                    <a href="#" class="menuActionButton" data-action="Customer.changePassword">
                                                        <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/dashboard_icons/account_password.png" alt="" /><br />
                                                        <span>
                                                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleChangePassword');?>

                                                        </span>
                                                    </a>
                                                </div>

                                            <?php }?>
                                                <div class="menuItem menuItemChangePreferences">
                                                    <a href="#" class="menuActionButton" data-action="Customer.changePreferences">
                                                        <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/dashboard_icons/account_preferences.png" alt="" /><br />
                                                        <span>
                                                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MenuTitleChangePreferences');?>

                                                        </span>
                                                    </a>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php }?>
                            <!-- END MENU -->
                            <?php if ($_smarty_tpl->tpl_vars['sidebaradditionalinfo']->value != '') {?>
                                <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebaradditionalinfo']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                            <?php }?>
                        </div>
                        <div class="clear"></div>
                        <?php if ($_smarty_tpl->tpl_vars['sidebaraccount']->value != '' || $_smarty_tpl->tpl_vars['sidebarcontactdetails']->value != '') {?>
                            <div id="side-outer-panel" class="side-outer-panel side-outer-panel-scroll" >
                                <?php if ($_smarty_tpl->tpl_vars['sidebaraccount']->value != '') {?>
                                    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebaraccount']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                                <?php }?>
                                <?php if ($_smarty_tpl->tpl_vars['sidebarcontactdetails']->value != '') {?>
                                    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebarcontactdetails']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                                <?php }?>
								<?php if ($_smarty_tpl->tpl_vars['displayredaction']->value == 1) {?>
                                    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebarredaction_default']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
								<?php }?>
                            </div>
                        <?php }?>
                        <div class="clear"></div>
                        </div>

                    <form id="showPreviewForm" method="post" target="_blank">
                        <input type="hidden" name="csrf_token" value="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
                    </form>

                    <form id="submitform" name="submitform" method="post" action="#" accept-charset="utf-8">
                        <input type="hidden" id="ref" name="ref" value="<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
" />
                        <input type="hidden" id="fsaction" name="fsaction" value="" />
                        <input type="hidden" id="ssotoken" name="ssotoken" value="<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
" />
                        <?php if ($_smarty_tpl->tpl_vars['section']->value == 'accountdetails') {?>
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
                            <?php if ($_smarty_tpl->tpl_vars['customerupdateauthrequired']->value) {?>
                                <input type="hidden" id="confirmpassword" name="confirmpassword"/>
                                <input type="hidden" id="confirmformat" name="confirmformat"/>
                            <?php }?>
                        <?php } elseif ($_smarty_tpl->tpl_vars['section']->value == 'changepassword') {?>
                            <input type="hidden" id="data1" name="data1" />
                            <input type="hidden" id="data2" name="data2" />
                            <input type="hidden" id="format" name="format" />
                        <?php } elseif ($_smarty_tpl->tpl_vars['section']->value == 'changepreferences') {?>
                            <input type="hidden" id="sendmarketinginfo" name="sendmarketinginfo" />
                        <?php }?>
                        <input type="hidden" id="orderitemid" name="orderitemid" value="" />
                        <input type="hidden" id="action" name="action" value="" />
                        <input type="hidden" id="giftcardcode" name="giftcardcode" value="" />
                        <input type="hidden" id="giftcardaction" name="giftcardaction" value="" />
                        <input type="hidden" id="showgiftcardmessage" name="showgiftcardmessage" value="0"/>
						<input type="hidden" id="tzoffset" name="tzoffset" value="" />
                        <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
                    </form>

                <div id="dialogOuter" class="dialogOuter"></div>
                </body>
            </html><?php }
}
