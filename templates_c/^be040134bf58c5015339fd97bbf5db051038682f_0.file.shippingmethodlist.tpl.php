<?php
/* Smarty version 4.5.3, created on 2026-03-07 07:52:58
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\shippingmethodlist.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69abd95a4dd803_39130028',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'be040134bf58c5015339fd97bbf5db051038682f' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\shippingmethodlist.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69abd95a4dd803_39130028 (Smarty_Internal_Template $_smarty_tpl) {
?><div id="methodBack" class="contentNavigation">

        <div class="btnDoneTop" id="contentNavigationMethodList" data-decorator="fnSetHashUrl" data-hash-url="shipping">
            <img class="backImage" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDone');?>
</div>
            <div class="clear"></div>
        </div>

    </div>

    <!-- SHIPPING METHOD LIST -->

    <div id="contentRightScrollMethodList" class="contentScrollCart">

        <div class="contentVisible">

            <div class="pageLabel">
                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShippingMethod');?>

            </div>

            <div class="outerBox shippingMethodBox">
                <div class="shippingLabelSelectMethod outerBoxPadding">
                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSelectShippingMethod');?>

                </div>
                <ul id="shippingMethodsList">
                    <?php echo $_smarty_tpl->tpl_vars['shippingmethodslist']->value;?>

                </ul>
            </div>

        </div> <!-- contentVisible -->

    </div> <!-- contentScrollCart -->
<?php }
}
