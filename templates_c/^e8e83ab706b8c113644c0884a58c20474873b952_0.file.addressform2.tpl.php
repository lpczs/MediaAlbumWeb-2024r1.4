<?php
/* Smarty version 4.5.3, created on 2026-03-23 02:01:36
  from 'C:\TAOPIX\MediaAlbumWeb\templates\addressform2.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69c09f007312d9_39117707',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e8e83ab706b8c113644c0884a58c20474873b952' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\addressform2.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69c09f007312d9_39117707 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['useraddressupdated']->value != 2) {?>
<div id="logintable" class="blocAccount outerBox">
	<h2 class="title-bar outerBoxPadding" id="logintitle">
        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLoginInformation');?>

    </h2>
    <div class="currentBloc outerBoxPadding" id="useremail">
        <div>
<?php if ($_smarty_tpl->tpl_vars['showusernameinput']->value == 1) {?>
            <div class="formLine1">
                <label for="login"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUserName');?>
:</label>
    <?php if ($_smarty_tpl->tpl_vars['strictmode']->value == '1') {?>
                <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
    <?php }?>
             </div>
            <div class="formLine2">
                <input type="text" id="login" name="login" value="<?php echo $_smarty_tpl->tpl_vars['login']->value;?>
" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"/>
    <?php if ($_smarty_tpl->tpl_vars['strictmode']->value == '1') {?>
                <img id="logincompulsory" class="error_form_image" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
    <?php }?>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="top_gap">
<?php }?>
            <div class="formLine1">
                <label for="email"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailAddress');?>
:</label>
<?php if ($_smarty_tpl->tpl_vars['strictmode']->value == '1') {?>
                <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
<?php }?>
            </div>
            <div class="formLine2">
                <input type="email" id="email" name="email" value="<?php echo $_smarty_tpl->tpl_vars['email']->value;?>
" autocorrect="off" autocapitalize="off" spellcheck="false" />
<?php if ($_smarty_tpl->tpl_vars['strictmode']->value == '1') {?>
                <img id="emailcompulsory" class="error_form_image" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
<?php }?>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
        <div id="loginpassword">
            <div class="top_gap">
                <div class="formLine1">
                    <label for="password"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPassword');?>
:</label>
<?php if ($_smarty_tpl->tpl_vars['strictmode']->value == '1') {?>
                    <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
<?php }?>
            </div>
            <div class="formLine2">
				<div class="password-input-wrap">
                    <div class="password-background">
                        <input type="password" id="password" name="password" value="" data-decorator="fnHandlePasswordStrength" />
                        <button type="button" id="togglepassword" class="password-visibility password-show"></button>
                    </div>
					<img class="error_form_image" id="newpasswordcompulsory" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
					<div class="progress-wrap">
						<progress id="strengthvalue" value="0" min="0" max="5"></progress>
						<p><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPasswordStrength');?>
: <span id="strengthtext"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStartTyping');?>
</span></p>
					</div>
				</div>
<?php if ($_smarty_tpl->tpl_vars['strictmode']->value == '1') {?>
                    <img id="passwordcompulsory" class="error_form_image" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
<?php }?>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>
<?php }?>
<div class="blocAccount outerBox outerBoxMarginTop" id="addressBlocFirst">
	<?php if ($_smarty_tpl->tpl_vars['useraddressupdated']->value != 2) {?>
    <h2 class="title-bar outerBoxPadding" id="blocTitle">
        <?php echo $_smarty_tpl->tpl_vars['addresstitle']->value;?>

    </h2>
	<?php }?>
    <div class="currentBloc outerBoxPadding" id="blocContent">
        <div id="ajaxdiv" name="ajaxdiv"></div>
        <div id="contacttable">
            <div class="top_gap">
                <div class="formLine1">
                    <label for="telephonenumber"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelTelephoneNumber');?>
:</label>
 <?php if ($_smarty_tpl->tpl_vars['strictmode']->value == '1') {?>
                    <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
<?php }?>
                </div>
                <div class="formLine2">
                    <input type="tel" id="telephonenumber" name="telephonenumber" value="<?php echo $_smarty_tpl->tpl_vars['telephonenumber']->value;?>
" data-decorator="fnCJKHalfWidthFullWidthToASCII" data-force-uppercase="false" autocorrect="off" autocapitalize="off" spellcheck="false" />
<?php if ($_smarty_tpl->tpl_vars['strictmode']->value == '1') {?>
                    <img id="telephonenumbercompulsory" class="error_form_image" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
<?php }?>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div><?php }
}
