<?php
/* Smarty version 4.5.3, created on 2026-03-09 03:46:46
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\ordercancellation_small.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69ae42a67f0d25_18886468',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '459d6ea8660ce5d98ec246eb604dfd7fadf155c4' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\ordercancellation_small.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69ae42a67f0d25_18886468 (Smarty_Internal_Template $_smarty_tpl) {
?><div id="contentCancelation" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">

            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleOrderCancellation');?>


        </div> <!-- pageLabel -->

        <div class="orderInformationBloc outerBox outerBoxPadding">

            <div>
                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageOrderCancellation1');?>

            </div>

        </div> <!-- orderInformationBloc outerBox outerBoxPadding -->

    <?php if ($_smarty_tpl->tpl_vars['mainwebsiteurl']->value != '') {?>

        <div class="buttonBottomSection">

            <div data-decorator="fnRedirect" data-url="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['mainwebsiteurl']->value, ENT_QUOTES, 'UTF-8', true);?>
">
                <div class="btnAction btnContinue">
                    <div class="btnContinueContent"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonContinue');?>
</div>
                </div>
            </div>

        </div> <!-- buttonBottomSection -->

        <div class="clear"></div>

    <?php }?>
    </div> <!-- contentVisible -->

</div> <!-- contentScrollCart --><?php }
}
