<?php
/* Smarty version 4.5.3, created on 2026-03-06 04:45:51
  from 'C:\TAOPIX\MediaAlbumWeb\templates\customer\sidebaraccount_default.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa5bff8d4094_70788148',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2ac4d18c96ee0696f25a7a4caeec2cd476d1b4f2' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\customer\\sidebaraccount_default.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aa5bff8d4094_70788148 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['showaccountbalance']->value == true) {?>
    <div class="side-panel section">
        <h2 class="title-bar title-bar-panel">
            <div class="textIcon"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAccount');?>
</div>
            <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/account_summary_icon.png" alt="" />
                        <div class="clear"></div>
        </h2>
        <div class="contentDotted">
            <div class="titleDetailPanel">
                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAccountBalance');?>
:
            </div>
            <div class="sidebaraccount_gap priceBold">
                <?php echo $_smarty_tpl->tpl_vars['accountbalance']->value;?>

            </div>
            <div class="contentDottedImage"></div>
        </div>
        <div class="content">
            <div class="titleDetailPanel">
                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCreditLimit');?>
:
            </div>
            <div class="sidebaraccount priceBold">
                <?php echo $_smarty_tpl->tpl_vars['creditlimit']->value;?>

            </div>
        </div>
    </div>
<?php }
if ($_smarty_tpl->tpl_vars['showgiftcardsbalance']->value == true) {?>
<div class="side-panel section">
    <h2 class="title-bar title-bar-panel">
        <div class="textIcon"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGiftCardBalance');?>
</div>
        <img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/gift_card_icon.png" alt="" />
        <div class="clear"></div>
    </h2>
    <div class="contentDotted">
        <div class="sidebaraccount_gap priceBold">
            <?php echo $_smarty_tpl->tpl_vars['giftcardbalance']->value;?>

        </div>
        <div class="contentDottedImage"></div>
    </div>
    <div class="content">
        <div class="titleDetailPanel">
            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGiftCardRedeemText');?>

        </div>
    </div>
    <div class="content">
        <div class="sidebaraccount_gap">
            <input type="text" id="giftcardid" class="inputGiftCard" name="giftcardid" placeholder="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnterCode');?>
" />
        </div>
        <div class="align-right">
            <div class="contentBtn" id="setGiftCardButton">
                <div class="btn-green-left" ></div>
                <div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedeem');?>
</div>
                <div class="btn-green-arrow-right"></div>
            </div>
        </div>
    </div>
</div>
<?php }
}
}
