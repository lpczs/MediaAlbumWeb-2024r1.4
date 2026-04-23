<?php
/* Smarty version 4.5.3, created on 2026-03-16 21:32:39
  from 'C:\TAOPIX\MediaAlbumWeb\templates\ajaxapi.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b876f72eb156_14992353',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5921f155c1e70c239d937cb24ecdde6d72483e82' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\ajaxapi.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b876f72eb156_14992353 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['result']->value == "ERROR") {?>
    <?php echo $_smarty_tpl->tpl_vars['resultParam']->value;?>

<?php } elseif ($_smarty_tpl->tpl_vars['result']->value == "ZONECOUNTRY") {?>
    <?php
$__section_index_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['othercountries']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_0_total = $__section_index_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_0_total !== 0) {
for ($__section_index_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_0_iteration <= $__section_index_0_total; $__section_index_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['first'] = ($__section_index_0_iteration === 1);
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_0_iteration === $__section_index_0_total);
?>
        <?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['first'] : null)) {?>

        <div class="wizard-dropdown">
            <select id="countrylist" name="countrylist" class="text wizard-dropdown" data-decorator="countryChange">
                    <?php }?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['othercountries']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['isocode2'];?>
" <?php if ($_smarty_tpl->tpl_vars['othercountries']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['isocode2'] == $_smarty_tpl->tpl_vars['country']->value) {?>selected="selected"<?php }?>>
                    <?php echo $_smarty_tpl->tpl_vars['othercountries']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>

                </option>
                    <?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
            </select>
        </div>

        <?php }?>
    <?php
}
}
} elseif ($_smarty_tpl->tpl_vars['result']->value == "ZONEREGION") {?>
    <?php
$__section_index_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['regionList']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_1_total = $__section_index_1_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_1_total !== 0) {
for ($__section_index_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_1_iteration <= $__section_index_1_total; $__section_index_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['first'] = ($__section_index_1_iteration === 1);
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_1_iteration === $__section_index_1_total);
?>
                <?php $_template = new Smarty_Internal_Template('eval:'.$_smarty_tpl->tpl_vars['label']->value, $_smarty_tpl->smarty, $_smarty_tpl);$_smarty_tpl->assign('fieldLabel',$_template->fetch()); ?>
        <?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['first'] : null)) {?>
<label for="regionlist">
    &nbsp;<?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:&nbsp;
</label>&nbsp;

<div class="wizard-dropdown">
    <select id="regionlist" name="regionlist" class="text" class="text wizard-dropdown">'
                <?php if ($_smarty_tpl->tpl_vars['allEnabled']->value == 1) {?>
        <option value="--" >
            --<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAll');?>
--
        </option>
                <?php }?>
                <?php $_smarty_tpl->_assignInScope('currentitem', '');?>
            <?php }?>
            <?php if ($_smarty_tpl->tpl_vars['currentitem']->value != $_smarty_tpl->tpl_vars['regionList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['group']) {?>
                <?php if ($_smarty_tpl->tpl_vars['currentitem']->value != '') {?>
        </optgroup>
                <?php }?>
        <optgroup label="<?php echo $_smarty_tpl->tpl_vars['regionList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['group'];?>
">
                <?php $_smarty_tpl->_assignInScope('currentitem', $_smarty_tpl->tpl_vars['regionList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['group']);?>
            <?php }?>
            <option value="<?php echo $_smarty_tpl->tpl_vars['regionList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['code'];?>
" >
            <?php echo $_smarty_tpl->tpl_vars['regionList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>

            </option>
            <?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
                <?php if ($_smarty_tpl->tpl_vars['currentitem']->value != '') {?>
        </optgroup>
                <?php }?>
    </select>
</div>
        <?php }?>
    <?php
}
}
} elseif ($_smarty_tpl->tpl_vars['result']->value == "ADDRESSFORM") {?>
    <?php
$__section_addressline_2_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['addressForm']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_addressline_2_total = $__section_addressline_2_loop;
$_smarty_tpl->tpl_vars['__smarty_section_addressline'] = new Smarty_Variable(array());
if ($__section_addressline_2_total !== 0) {
for ($__section_addressline_2_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] = 0; $__section_addressline_2_iteration <= $__section_addressline_2_total; $__section_addressline_2_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['first'] = ($__section_addressline_2_iteration === 1);
$_template = new Smarty_Internal_Template('eval:'.$_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['label'], $_smarty_tpl->smarty, $_smarty_tpl);$_smarty_tpl->assign('fieldLabel',$_template->fetch());
if ((isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['first'] : null)) {?><div><?php } else { ?><div class="top_gap"><?php }?><div class="formLine1"><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "firstname") {?><label for="maincontactfname"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="maincontactfname" name="maincontactfname" value="" /><?php } elseif ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "lastname") {?><label for="maincontactlname"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="maincontactlname" name="maincontactlname" value="" /><?php } elseif ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "company") {?><label for="maincompanyname"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="maincompanyname" name="maincompanyname" value="" <?php echo $_smarty_tpl->tpl_vars['readonly']->value;?>
 /><?php } elseif ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "add1") {?><label for="mainaddress1"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="mainaddress1" name="mainaddress1" value="" <?php echo $_smarty_tpl->tpl_vars['readonly']->value;?>
 /><?php } elseif ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "add2") {?><label for="mainaddress2"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="mainaddress2" name="mainaddress2" value="" <?php echo $_smarty_tpl->tpl_vars['readonly']->value;?>
 /><?php } elseif ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "add3") {?><label for="mainaddress3"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="mainaddress3" name="mainaddress3" value="" <?php echo $_smarty_tpl->tpl_vars['readonly']->value;?>
 /><?php } elseif ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "add4") {?><label for="mainaddress4"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="mainaddress4" name="mainaddress4" value="" <?php echo $_smarty_tpl->tpl_vars['readonly']->value;?>
 /><?php } elseif ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "regtaxnumtype") {?><label for="regtaxnumtype"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><div class="wizard-dropdown"><select id="regtaxnumtype" name="regtaxnumtype" class="wizard-dropdown sp-dropdown-size"><?php
$__section_index_3_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['registeredtaxnumbertypes']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_3_total = $__section_index_3_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_3_total !== 0) {
for ($__section_index_3_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_3_iteration <= $__section_index_3_total; $__section_index_3_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['first'] = ($__section_index_3_iteration === 1);
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_3_iteration === $__section_index_3_total);
?><option value="<?php echo $_smarty_tpl->tpl_vars['registeredtaxnumbertypes']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['registeredtaxnumbertypes']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
</option><?php
}
}
?></select></div><?php } elseif ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "regtaxnum") {?><label for="regtaxnum"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="regtaxnum" name="regtaxnum" value="" <?php echo $_smarty_tpl->tpl_vars['readonly']->value;?>
 /><?php } elseif ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "city") {?><label for="maincity"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="maincity" name="maincity" value="" <?php echo $_smarty_tpl->tpl_vars['readonly']->value;?>
 /><?php } elseif ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "county") {
if ($_smarty_tpl->tpl_vars['region']->value == "COUNTY") {
$__section_index_4_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['regionList']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_4_total = $__section_index_4_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_4_total !== 0) {
for ($__section_index_4_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_4_iteration <= $__section_index_4_total; $__section_index_4_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['first'] = ($__section_index_4_iteration === 1);
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_4_iteration === $__section_index_4_total);
if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['first'] : null)) {?><label for="countylist"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><div class="wizard-dropdown "><select id="countylist" name="countylist" class="wizard-dropdown sp-dropdown-size"><option value="--" >--<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeSelection');?>
--</option><?php $_smarty_tpl->_assignInScope('currentitem', '');
}
if ($_smarty_tpl->tpl_vars['currentitem']->value != $_smarty_tpl->tpl_vars['regionList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['group']) {
if ($_smarty_tpl->tpl_vars['currentitem']->value != '') {?></optgroup><?php }?><optgroup label="<?php echo $_smarty_tpl->tpl_vars['regionList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['group'];?>
"><?php $_smarty_tpl->_assignInScope('currentitem', $_smarty_tpl->tpl_vars['regionList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['group']);
}?><option value="<?php echo $_smarty_tpl->tpl_vars['regionList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['code'];?>
" ><?php echo $_smarty_tpl->tpl_vars['regionList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
</option><?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {
if ($_smarty_tpl->tpl_vars['currentitem']->value != '') {?></optgroup><?php }?></select></div><?php }
}} else {
 ?><label for="maincounty"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="maincounty" name="maincounty" value="<?php echo $_smarty_tpl->tpl_vars['county']->value;?>
" <?php echo $_smarty_tpl->tpl_vars['readonly']->value;?>
 /><?php
}
} else { ?><label for="maincounty"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="maincounty" name="maincounty" value="<?php echo $_smarty_tpl->tpl_vars['county']->value;?>
" <?php echo $_smarty_tpl->tpl_vars['readonly']->value;?>
 /><?php }
} elseif ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "state") {
if ($_smarty_tpl->tpl_vars['region']->value == "STATE") {
$__section_index_5_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['regionList']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_5_total = $__section_index_5_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_5_total !== 0) {
for ($__section_index_5_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_5_iteration <= $__section_index_5_total; $__section_index_5_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['first'] = ($__section_index_5_iteration === 1);
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_5_iteration === $__section_index_5_total);
if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['first'] : null)) {?><label for="statelist"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><div class="wizard-dropdown"><select id="statelist" name="statelist" class="wizard-dropdown sp-dropdown-size"><option value="--" >--<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeSelection');?>
--</option><?php $_smarty_tpl->_assignInScope('currentitem', '');
}
if ($_smarty_tpl->tpl_vars['currentitem']->value != $_smarty_tpl->tpl_vars['regionList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['group']) {
if ($_smarty_tpl->tpl_vars['currentitem']->value != '') {?></optgroup><?php }?><optgroup label="<?php echo $_smarty_tpl->tpl_vars['regionList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['group'];?>
"><?php $_smarty_tpl->_assignInScope('currentitem', $_smarty_tpl->tpl_vars['regionList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['group']);
}?><option value="<?php echo $_smarty_tpl->tpl_vars['regionList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['code'];?>
" ><?php echo $_smarty_tpl->tpl_vars['regionList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
</option><?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {
if ($_smarty_tpl->tpl_vars['currentitem']->value != '') {?></optgroup><?php }?></select></div><?php }
}} else {
 ?><label for="mainstate"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="mainstate" name="mainstate" value="<?php echo $_smarty_tpl->tpl_vars['state']->value;?>
" <?php echo $_smarty_tpl->tpl_vars['readonly']->value;?>
 /><?php
}
} else { ?><label for="mainstate"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="mainstate" name="mainstate" value="<?php echo $_smarty_tpl->tpl_vars['state']->value;?>
" <?php echo $_smarty_tpl->tpl_vars['readonly']->value;?>
 /><?php }
} elseif ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "postcode") {?><label for="mainpostcode"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="mainpostcode" name="mainpostcode" value="<?php echo $_smarty_tpl->tpl_vars['postcode']->value;?>
" <?php echo $_smarty_tpl->tpl_vars['readonly']->value;?>
 data-decorator="fnCJKHalfWidthFullWidthToASCII" data-force-uppercase="true" /><?php } elseif ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "country") {
if ($_smarty_tpl->tpl_vars['readonly']->value == '') {?><label for="countrylist"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><div class="wizard-dropdown"><select id="countrylist" name="countrylist" class="wizard-dropdown" data-decorator="fnCountryChange"><?php
$__section_index_6_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['countryList']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_6_total = $__section_index_6_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_6_total !== 0) {
for ($__section_index_6_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_6_iteration <= $__section_index_6_total; $__section_index_6_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['first'] = ($__section_index_6_iteration === 1);
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_6_iteration === $__section_index_6_total);
?><option value="<?php echo $_smarty_tpl->tpl_vars['countryList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['isocode2'];?>
" <?php if ($_smarty_tpl->tpl_vars['countryList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['isocode2'] == $_smarty_tpl->tpl_vars['countryCode']->value) {?>selected="selected "<?php }?>><?php echo $_smarty_tpl->tpl_vars['countryList']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
</option><?php
}
}
?></select></div><?php } else { ?><label for="country"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="country" name="country" value="<?php echo $_smarty_tpl->tpl_vars['countryName']->value;?>
" <?php echo $_smarty_tpl->tpl_vars['readonly']->value;?>
 /><?php }
} elseif ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "add41") {?><label for="mainadd41"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="mainadd41" name="mainadd41" value="" <?php echo $_smarty_tpl->tpl_vars['readonly']->value;?>
 /><?php } elseif ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "add42") {?><label for="mainadd42"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="mainadd42" name="mainadd42" value="" <?php echo $_smarty_tpl->tpl_vars['readonly']->value;?>
 /><?php } elseif ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "add43") {?><label for="mainadd43"><?php echo $_smarty_tpl->smarty->ext->configload->_getConfigVariable($_smarty_tpl, $_smarty_tpl->tpl_vars['fieldLabel']->value);?>
:</label><?php if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php } else { ?><div class="gap-label-mandatory"></div><?php }?></div><div class="formLine2"><input type="text" id="mainadd43" name="mainadd43" value="" <?php echo $_smarty_tpl->tpl_vars['readonly']->value;?>
 /><?php }
if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "1") {?><img class="error_form_image" id="<?php echo $_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'];?>
compulsory" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt=""/><?php }
if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "state" && $_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "0") {?><img id="<?php echo $_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'];?>
compulsory" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/dummy.gif" alt=""/><?php }
if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "county" && $_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "0") {?><img id="<?php echo $_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'];?>
compulsory" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/dummy.gif" alt=""/><?php }
if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "city" && $_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "0") {?><img id="<?php echo $_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'];?>
compulsory" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/dummy.gif" alt=""/><?php }
if ($_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'] == "postcode" && $_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['compulsory'] == "0") {?><img id="<?php echo $_smarty_tpl->tpl_vars['addressForm']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_addressline']->value['index'] : null)]['name'];?>
compulsory" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/dummy.gif" alt=""/><?php }?><div class="clear"></div></div></div><?php
}
}
?><input type="hidden" id="region" name="region" value="<?php echo $_smarty_tpl->tpl_vars['region']->value;?>
" />
<?php } else { ?>
    Unforeseen error
<?php }
}
}
