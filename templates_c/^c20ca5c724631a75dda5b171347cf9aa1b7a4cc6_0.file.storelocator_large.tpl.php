<?php
/* Smarty version 4.5.3, created on 2026-03-14 01:34:59
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\storelocator_large.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b4bb43517787_99769384',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c20ca5c724631a75dda5b171347cf9aa1b7a4cc6' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\storelocator_large.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:order/storelocatorJSON_large.tpl' => 1,
  ),
),false)) {
function content_69b4bb43517787_99769384 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="dialogContentContainer">
	<div id="storeLogo" class="storeLogo">
		<input type="hidden" id="storeLogoImgHeight" value="<?php echo $_smarty_tpl->tpl_vars['logoheight']->value;?>
" />
	<?php if ($_smarty_tpl->tpl_vars['logoheight']->value != 0) {?>
		<img id="storeLogoImg" class="storeLogoImg" src="<?php echo $_smarty_tpl->tpl_vars['logorow']->value;?>
" alt="" />
	<?php }?>
	</div>
	<div class="contentFormStoreLocator">
		<form method="post" id="mainformstore" name="mainformstore" action="#">
			<div id="storeDetails">
				<div class="message" id="message">
					<?php echo $_smarty_tpl->tpl_vars['error']->value;?>

				</div>
				<h2 class="title-bar-inside">
					<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSelectStore');?>

				</h2>
	<?php if ($_smarty_tpl->tpl_vars['showcountrylist']->value+$_smarty_tpl->tpl_vars['showregionlist']->value > 0) {?>
		<?php if ($_smarty_tpl->tpl_vars['showcountrylist']->value == 1) {?>
				<div class="inputGap">
			<?php
$__section_index_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['countrylist']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_0_total = $__section_index_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_0_total !== 0) {
for ($__section_index_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_0_iteration <= $__section_index_0_total; $__section_index_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['first'] = ($__section_index_0_iteration === 1);
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_0_iteration === $__section_index_0_total);
?>
				<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['first'] : null)) {?>
					<label for="countries"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCountry');?>
:</label>
					<select id="countries" name="countries" class="middle">
						<option value="<?php echo $_smarty_tpl->tpl_vars['showAllCode']->value;?>
" selected="selected">
							 <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_CountryOptional');?>

						</option>
				<?php }?>
						<option value="<?php echo $_smarty_tpl->tpl_vars['countrylist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['code'];?>
">
							<?php echo $_smarty_tpl->tpl_vars['countrylist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>

						</option>
				<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
					</select>
				<?php }?>
			<?php
}
}
?>
					<div class="clear"></div>
				</div>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['showregionlist']->value == 1) {?>
				<div class="inputGap">
					<label class="text" for="regions"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRegion');?>
:</label>
					<select id="regions" name="regions" disabled="true" class="middle">
						<option value="<?php echo $_smarty_tpl->tpl_vars['showAllCode']->value;?>
"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_RegionOptional');?>
</option>
					</select>
					<div class="clear"></div>
				</div>
		<?php }?>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['showstoregroups']->value == 1) {?>
				<div class="inputGap">
					<label for="storegroups"><?php echo $_smarty_tpl->tpl_vars['storegrouplabel']->value;?>
</label>
					<select id="storegroups" name="storegroups" disabled="disabled" class="middle"></select>
					<div class="clear"></div>
				</div>
	<?php }?>
				<div class="inputGap">
					<div class="contentSearchText">
						<label for="searchText"><?php echo $_smarty_tpl->tpl_vars['labelAddressSearch']->value;?>
:</label>
						<input type="text" id="searchText" name="searchText" value="" class="storeInputLong" data-decorator="fnSearchForStore" data-trigger="keypress" />
					</div>
					<div class="btnRight">
						<div class="contentBtn" id="searchButton" data-decorator="fnSearchForStore" data-trigger="click">
							<div class="btn-white-left" ></div>
							<div class="btn-white-middle"><img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/search_icon.png" alt="" title="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonSearch');?>
"/></div>
							<div class="btn-white-right"></div>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<div id="storeList">
				<h2 class="title-bar-inside">
					<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelListOfStores');?>

				</h2>
				<div id="storeListAjaxDiv"><?php echo $_smarty_tpl->tpl_vars['storelist']->value;?>
</div>
			</div>
		</form>
	</div>
</div>
<div class="buttonBottomInside">
    <div class="btnLeft">
        <div class="contentBtn" id="doneSelectStoreButton" data-decorator="fnDoneSelectStoreButton">
            <div class="btn-red-cross-left" ></div>
            <div class="btn-red-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
</div>
            <div class="btn-red-right"></div>
        </div>
    </div>
    <div class="btnRight">
        <div class="contentBtn" id="validateSelectStoreButton" data-decorator="acceptDataEntryStoreLocator" data-trigger="click">
            <div class="btn-green-left" ></div>
            <div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonSelectStore');?>
</div>
            <div class="btn-accept-right"></div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<form id="submitformStoreLocator" name="submitformStoreLocator" method="post" accept-charset="utf-8" action="#">
    <input type="hidden" id="ref" name="ref" value="<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
" />
    <input type="hidden" id="fsaction" name="fsaction" value="" />
    <input type="hidden" id="ssotoken" name="ssotoken" value="<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
" />
    <input type="hidden" name="previousstage" value="<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
"/>
    <input type="hidden" name="stage" value="<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
"/>
    <input type="hidden" name="filter" value=""/>
    <input type="hidden" name="privatefilter" value=""/>
    <input type="hidden" name="storegroup" value=""/>
    <input type="hidden" name="region" value=""/>
    <input type="hidden" name="country" value=""/>
    <input type="hidden" name="storecode" value=""/>
    <input type="hidden" name="payinstoreallowed" value=""/>
    <input type="hidden" name="shippingratecode" value="<?php echo $_smarty_tpl->tpl_vars['shippingratecode']->value;?>
"/>
    <input type="hidden" name="sameshippingandbillingaddress" value="<?php echo $_smarty_tpl->tpl_vars['sameshippingandbillingaddress']->value;?>
"/>
</form>
<?php echo '<script'; ?>
 type="text/javascript" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
>
        <?php $_smarty_tpl->_subTemplateRender("file:order/storelocatorJSON_large.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '</script'; ?>
><?php }
}
