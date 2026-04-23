<?php
/* Smarty version 4.5.3, created on 2026-04-09 05:48:26
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\componentcategories\componentcategoriesedit.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69d73daa3e3425_22995169',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2afe9652f3e777bf313b78788e668733f9c2f592' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\componentcategories\\componentcategoriesedit.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69d73daa3e3425_22995169 (Smarty_Internal_Template $_smarty_tpl) {
?>

function initialize(pParams)
{	
	<?php echo $_smarty_tpl->tpl_vars['localizedcodesjavascript']->value;?>

	<?php echo $_smarty_tpl->tpl_vars['localizednamesjavascript']->value;?>

	<?php echo $_smarty_tpl->tpl_vars['languagecodesjavascript']->value;?>

	<?php echo $_smarty_tpl->tpl_vars['languagenamesjavascript']->value;?>

	<?php echo $_smarty_tpl->tpl_vars['sitegrouplocalizedcodesjavascript']->value;?>

	<?php echo $_smarty_tpl->tpl_vars['sitegrouplocalizednamesjavascript']->value;?>

    
    var str_LabelLanguageName    = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLanguageName');?>
";
	var str_localizedNameLabel   = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
";
	var deleteImg = '<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/delete.png';
	var addimg = '<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/add.png';

	const TPX_COMPONENT_DISPLAY_STAGE_NONE = <?php echo $_smarty_tpl->tpl_vars['TPX_COMPONENT_DISPLAY_STAGE_NONE']->value;?>
;
	const TPX_COMPONENT_DISPLAY_STAGE_START = <?php echo $_smarty_tpl->tpl_vars['TPX_COMPONENT_DISPLAY_STAGE_START']->value;?>
;
	const TPX_COMPONENT_DISPLAY_STAGE_ORDER = <?php echo $_smarty_tpl->tpl_vars['TPX_COMPONENT_DISPLAY_STAGE_ORDER']->value;?>
;
	const TPX_COMPONENT_DISPLAY_STAGE_ALL = <?php echo $_smarty_tpl->tpl_vars['TPX_COMPONENT_DISPLAY_STAGE_ALL']->value;?>
;
	
	/* save functions */
	function addsaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminComponentCategories.add&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
';
		var fp = Ext.getCmp('componentCategoryForm'), form = fp.getForm();
		var submit = true;
	
		var paramArray = new Object();
		paramArray['isactive'] = '';
	
		if (Ext.getCmp('isactive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}
	
		paramArray['requirespagecount'] = '';
		
		if (Ext.getCmp('requirespagecount').checked)
		{
			paramArray['requirespagecount'] = '1';
		}
		else
		{
			paramArray['requirespagecount'] = '0';
		}

		paramArray['displaystage'] = TPX_COMPONENT_DISPLAY_STAGE_NONE;
		
		if (Ext.getCmp('onlinedisplaystageorder').checked && Ext.getCmp('onlinedisplaystageprojectstart').checked)
		{
			paramArray['displaystage'] = TPX_COMPONENT_DISPLAY_STAGE_ALL;
		}
		else
		{
			if (Ext.getCmp('onlinedisplaystageorder').checked)
			{
				paramArray['displaystage'] = TPX_COMPONENT_DISPLAY_STAGE_ORDER;
			}
			else if (Ext.getCmp('onlinedisplaystageprojectstart').checked)
			{
				paramArray['displaystage'] = TPX_COMPONENT_DISPLAY_STAGE_START;
			}
		}
				
		if (!Ext.getCmp('langPanel').isValid())
		{
			Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsErrorNoName');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING }); 
			submit = false;
		}	
	
		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
", saveCallback);
		}
	}

	/* save functions */
	function editsaveHandler(btn, ev)
	{
		var selectID = Ext.taopix.gridSelection2IDList(gMainComponentWindowObj.findById('maingrid'));
		
		var submitURL = 'index.php?fsaction=AdminComponentCategories.edit&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&id=' + selectID;
		var fp = Ext.getCmp('componentCategoryForm'), form = fp.getForm();
		var submit = true;
	
		var paramArray = new Object();
		paramArray['isactive'] = '';
	
		if (Ext.getCmp('isactive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}
	
		paramArray['requirespagecount'] = '';
		
		if (Ext.getCmp('requirespagecount').checked)
		{
			paramArray['requirespagecount'] = '1';
		}
		else
		{
			paramArray['requirespagecount'] = '0';
		}

		paramArray['displaystage'] = TPX_COMPONENT_DISPLAY_STAGE_NONE;
		
		if (Ext.getCmp('onlinedisplaystageorder').checked && Ext.getCmp('onlinedisplaystageprojectstart').checked)
		{
			paramArray['displaystage'] = TPX_COMPONENT_DISPLAY_STAGE_ALL;
		}
		else
		{
			if (Ext.getCmp('onlinedisplaystageorder').checked)
			{
				paramArray['displaystage'] = TPX_COMPONENT_DISPLAY_STAGE_ORDER;
			}
			else if (Ext.getCmp('onlinedisplaystageprojectstart').checked)
			{
				paramArray['displaystage'] = TPX_COMPONENT_DISPLAY_STAGE_START;
			}
		}
						
		if (!Ext.getCmp('langPanel').isValid())
		{
			Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsErrorNoName');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING }); 
			submit = false;
		}	
					
		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
", saveCallback);
		}	
	}

	function saveCallback(pUpdated, pActionForm, pActionData)
	{	
		if (pUpdated)
		{		
			var gridObj = gMainComponentWindowObj.findById('maingrid');
			var dataStore = gridObj.store;	
		
			gridObj.store.reload();
			gDialogObj.close();
		}
		else
		{
			icon = Ext.MessageBox.WARNING;
				
			Ext.MessageBox.show({
				title: pActionData.result.title,
				msg: pActionData.result.msg,
				buttons: Ext.MessageBox.OK,
				icon: icon
			});
		}
	}
	
	function validate(value, allowDecimal, allowNegative)
	{
		if (! isNumeric(value, true, false))
		{
			valid = false;
		}
		else
		{
			valid = true;
		}
	
		return valid;
	}

	function forceAlphaNumeric()	
	{
		var code = Ext.getCmp('code').getValue();
    
   		code = code.toUpperCase();
    	code = code.replace(/[^A-Z_0-9\-]+/g, "");
    
   	 	Ext.getCmp('code').setValue(code);
	}
	
    var langListStore = [];
	var dataList = [  ];
	for (var i =0; i < gAllLanguageCodesArray.length; i++)
    {
    	var languageName = "";
    	var languageCode = "";
        var languageNameIndex = ArrayIndexOf(gAllLanguageCodesArray, gLocalizedCodesArray[i]);
        if (languageNameIndex > -1)
        {
            languageName = gAllLanguageNamesArray[languageNameIndex];
            languageCode = gAllLanguageCodesArray[languageNameIndex];
        }
     	if ((languageName) && (languageName!=undefined)) dataList.push([languageCode,languageName,gLocalizedNamesArray[i]]);
        
        var languageCodeIndex = ArrayIndexOf(gLocalizedCodesArray, gAllLanguageCodesArray[i]);
        if (languageCodeIndex == -1)
        {
        	langListStore.push([gAllLanguageCodesArray[i],gAllLanguageNamesArray[i]]);
        }
    };
    
    var promptList = [];
	var dataList2 = [ ];
	for (var i =0; i < gAllLanguageCodesArray.length; i++)
	{
		var groupLabelLanguageName = "";
		var groupLabelLanguageCode = "";
		var groupLabeLlanguageNameIndex = ArrayIndexOf(gAllLanguageCodesArray, gSiteGroupLocalizedCodesArray[i]);
		if (groupLabeLlanguageNameIndex > -1)
		{
			groupLabelLanguageName = gAllLanguageNamesArray[groupLabeLlanguageNameIndex];
			groupLabelLanguageCode = gAllLanguageCodesArray[groupLabeLlanguageNameIndex];
		}
    	if ((groupLabelLanguageName) && (groupLabelLanguageName!=undefined)) dataList2.push([groupLabelLanguageCode,groupLabelLanguageName,gSiteGroupLocalizedNamesArray[i]]);
       
    	var groupLabellanguageCodeIndex = ArrayIndexOf(gSiteGroupLocalizedCodesArray, gAllLanguageCodesArray[i]);
    	if (groupLabellanguageCodeIndex == -1)
    	{
    		promptList.push([gAllLanguageCodesArray[i],gAllLanguageNamesArray[i]]);
    	}
   	};
      
   	var promptlangPanel = new Ext.taopix.LangPanel({
		id: 'promptLang', 
		name:'prompt',
		height:150,
        width: 450,
		post:true,
		data: {langList: promptList, dataList: dataList2},
		settings: 
		{ 
			headers:     {langLabel: str_LabelLanguageName,  textLabel: str_localizedNameLabel, deletePic: deleteImg, addPic: addimg},
			defaultText: {langBlank: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSelectLanguage');?>
",  textBlank: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTypeValue');?>
", defaultValue: "<?php echo $_smarty_tpl->tpl_vars['defaultlanguagecode']->value;?>
"},
			columnWidth: {langCol: 185, textCol: 202, delCol: 35},
			fieldWidth:  {langField: 175, textField: 186},
			errorMsg:    {blankValue: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTextFieldBlank');?>
"}
		}
	});
   
   	var promptContainer = {
		xtype: 'panel',
    	width: 450,
       	bodyBorder: false,
        border:false,
        fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPrompt');?>
",
        items: promptlangPanel
    };
   
    var pricingModelStore = new Ext.data.ArrayStore({
		id: 'pricingModelStore',
		fields: ['id', 'name'],
		data: [
			
			<?php
$__section_index_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['pricingModelData']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_0_total = $__section_index_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_0_total !== 0) {
for ($__section_index_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_0_iteration <= $__section_index_0_total; $__section_index_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_0_iteration === $__section_index_0_total);
?>
			<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
				["<?php echo $_smarty_tpl->tpl_vars['pricingModelData']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['pricingModelData']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"]
			<?php } else { ?>
				["<?php echo $_smarty_tpl->tpl_vars['pricingModelData']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['pricingModelData']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"],
			<?php }?>
			<?php
}
}
?>
			
		]
	});
    
    var pricingModelCombo = new Ext.form.ComboBox({
		id: 'pricingModelCombo',
		name: 'pricingModelCombo',
		width:330,
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPricingModel');?>
",
		mode: 'local',
		editable: false,
		hideLabel: false,
		forceSelection: true,
		<?php if ($_smarty_tpl->tpl_vars['hascomponents']->value == 1 || $_smarty_tpl->tpl_vars['isprivate']->value == 1) {?>
			disabled: true,
		<?php } else { ?>
	    	disabled: false,
	    <?php }?>
		selectOnFocus: true,
		triggerAction: 'all',
		store: pricingModelStore,
		valueField: 'id',
		displayField: 'name',
		useID: true,
		value: "<?php echo $_smarty_tpl->tpl_vars['categorypricingmodel']->value;?>
",
		allowBlank: false,
		post: true
	});
    
    var displayTypeStore = new Ext.data.ArrayStore({
		id: 'displayTypeStore',
		fields: ['id', 'name'],
		data: [
			
			<?php
$__section_index_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['displayTypeData']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_1_total = $__section_index_1_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_1_total !== 0) {
for ($__section_index_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_1_iteration <= $__section_index_1_total; $__section_index_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_1_iteration === $__section_index_1_total);
?>
			<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
				["<?php echo $_smarty_tpl->tpl_vars['displayTypeData']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['displayTypeData']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"]
			<?php } else { ?>
				["<?php echo $_smarty_tpl->tpl_vars['displayTypeData']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['displayTypeData']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"],
			<?php }?>
			<?php
}
}
?>
			
		]
	});

	var onlineDisplayStore = new Ext.data.ArrayStore({
		id: 'onlineDisplayStore',
		fields: ['id', 'name'],
		data: [
			
			<?php
$__section_index_2_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['onlineDisplayData']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_2_total = $__section_index_2_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_2_total !== 0) {
for ($__section_index_2_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_2_iteration <= $__section_index_2_total; $__section_index_2_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_2_iteration === $__section_index_2_total);
?>
			<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
				["<?php echo $_smarty_tpl->tpl_vars['onlineDisplayData']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['onlineDisplayData']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"]
			<?php } else { ?>
				["<?php echo $_smarty_tpl->tpl_vars['onlineDisplayData']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['onlineDisplayData']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"],
			<?php }?>
			<?php
}
}
?>
			
		]
	});
    
    var displayTypeCombo = new Ext.form.ComboBox({
		id: 'displayTypeCombo',
		name: 'displayTypeCombo',
		width:110,
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisplayType');?>
",
		mode: 'local',
		editable: false,
		<?php if ($_smarty_tpl->tpl_vars['hascomponents']->value == 1 || $_smarty_tpl->tpl_vars['isprivate']->value == 1) {?>
			disabled: true,
		<?php } else { ?>
	    	disabled: false,
	    <?php }?>
		hideLabel: false,
		forceSelection: true,
		selectOnFocus: true,
		triggerAction: 'all',
		store: displayTypeStore,
		valueField: 'id',
		displayField: 'name',
		useID: true,
		value: "<?php echo $_smarty_tpl->tpl_vars['categorydisplaytype']->value;?>
",
		allowBlank: false,
		post: true
	});

	var onlineComponentDisplayStageProjectStart = new Ext.form.Checkbox({
		id: 'onlinedisplaystageprojectstart',
		name: 'onlinedisplaystageprojectstart',
		boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ComponentsDisplayStartingProject');?>
",
		<?php if ((($_smarty_tpl->tpl_vars['onlinedisplaystage']->value&$_smarty_tpl->tpl_vars['TPX_COMPONENT_DISPLAY_STAGE_ORDER']->value) === $_smarty_tpl->tpl_vars['TPX_COMPONENT_DISPLAY_STAGE_ORDER']->value)) {?>
		disabled: false,
		<?php } else { ?>
		disabled: true,
		<?php }?>
		style: 'margin-left: 15px; margin-top: 10px;',
		<?php if ((($_smarty_tpl->tpl_vars['onlinedisplaystage']->value&$_smarty_tpl->tpl_vars['TPX_COMPONENT_DISPLAY_STAGE_START']->value) === $_smarty_tpl->tpl_vars['TPX_COMPONENT_DISPLAY_STAGE_START']->value)) {?>
		checked: true
		<?php } else { ?>
		checked: false
		<?php }?>
	});

	var onlineComponentDisplayStageOrder = new Ext.form.Checkbox({
		id: 'onlinedisplaystageorder',
		name: 'onlinedisplaystageprojectorder',
		boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ComponentsDisplayAddingToBasket');?>
",
	    disabled: false,
		listeners:
		{
			check: function(pCheckBox, pChecked)
			{
				var onlineDisplayStageProjectStartCmp = Ext.getCmp('onlinedisplaystageprojectstart');
				onlineDisplayStageProjectStartCmp.setDisabled(! pChecked);
				onlineDisplayStageProjectStartCmp.setValue(false);
			}
		},	
		<?php if ($_smarty_tpl->tpl_vars['onlinedisplaystage']->value == $_smarty_tpl->tpl_vars['TPX_COMPONENT_DISPLAY_STAGE_ORDER']->value || $_smarty_tpl->tpl_vars['onlinedisplaystage']->value == $_smarty_tpl->tpl_vars['TPX_COMPONENT_DISPLAY_STAGE_ALL']->value) {?>
		checked: true
		<?php } else { ?>
    	checked: false
    	<?php }?>
	});
    
    var requiresPageCount = new Ext.form.Checkbox({
		id: 'requirespagecount',
		name: 'requirespagecount',
		boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRequiresPageCount');?>
",
		<?php if ($_smarty_tpl->tpl_vars['isprivate']->value == 1) {?>
			disabled: true,
		<?php } else { ?>
	    	disabled: false,
	    <?php }?>
		<?php if ($_smarty_tpl->tpl_vars['requirespagecount']->value == 1) {?>
		checked: true
		<?php } else { ?>
    	checked: false
    	<?php }?>
	});
    
	var langPanel = new Ext.taopix.LangPanel({
		id: 'langPanel', 
		name:'name',
		height:150,
        width: 450,
		post:true,
		data: {langList: langListStore, dataList: dataList},
		settings: 
		{ 
			headers:     {langLabel: str_LabelLanguageName,  textLabel: str_localizedNameLabel, deletePic: deleteImg, addPic: addimg},
			defaultText: {langBlank: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSelectLanguage');?>
",  textBlank: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTypeValue');?>
", defaultValue: "<?php echo $_smarty_tpl->tpl_vars['defaultlanguagecode']->value;?>
"},
			columnWidth: {langCol: 185, textCol: 202, delCol: 35},
			fieldWidth:  {langField: 175, textField: 186},
			errorMsg:    {blankValue: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTextFieldBlank');?>
"}
		}
	});
	
	var LangContainer = {
        xtype: 'panel',
        width: 450,
        bodyBorder: false,
        border:false,
        fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
",
        items: langPanel
    };
	
	var companyCombo = new Ext.taopix.CompanyCombo({
		id: 'company',
		name: 'company',
		width:300,
		<?php if ($_smarty_tpl->tpl_vars['hascomponents']->value == 1 || $_smarty_tpl->tpl_vars['isprivate']->value == 1) {?>
			disabled: true,
		<?php } else { ?>
	    	disabled: false,
	    <?php }?>
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
",
		hideLabel:false,
		allowBlank:false,
		<?php if ($_smarty_tpl->tpl_vars['isEdit']->value == 1 || $_smarty_tpl->tpl_vars['companyLogin']->value) {?>
			<?php if ($_smarty_tpl->tpl_vars['companycode']->value == '') {?>
				defvalue: 'GLOBAL',
			<?php } else { ?>
				defvalue: '<?php echo $_smarty_tpl->tpl_vars['companycode']->value;?>
',
			<?php }?>
		<?php } else { ?>	
			defvalue: 'GLOBAL',
		<?php }?>
		options: {
			ref: '<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', 
			storeId: 'companyStore', 
			<?php if ($_smarty_tpl->tpl_vars['companyLogin']->value) {?>
			includeGlobal: '0', 
			<?php } else { ?>
			includeGlobal: '1', 
			<?php }?>
			includeShowAll: '0',
			onchange: function(){var companyCode = companyCombo.getValue(); if (companyCode == 'GLOBAL') companyCode = '';}
		}
	});
	
	var dialogFormPanelObj = new Ext.FormPanel({
		id: 'componentCategoryForm',
        labelAlign: 'left',
        labelWidth:120,
        autoHeight: true,
        frame:true,
        layout:'form',
        cls: 'left-right-buttons',
        bodyStyle:'padding-left:5px;',
        items: [
        	{ 
        		xtype: 'textfield', 
              	id: 'code', 
              	name: 'code', 
             	allowBlank: false,
              	maxLength: 50,
				width:300,
				<?php if ($_smarty_tpl->tpl_vars['isEdit']->value == 1) {?>
                	value: '<?php echo $_smarty_tpl->tpl_vars['categorycode']->value;?>
', 
                	style: {textTransform: "uppercase", background: "#dee9f6"},
                	readOnly: true,
              	<?php } else { ?>    
					style: {textTransform: "uppercase"},
		      	<?php }?>
              	fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCode');?>
", 
              	listeners:{
  					blur:{ fn: forceAlphaNumeric }
  				},
              	post: true,
				validator: function(pValue){
					return (pValue !== 'TAOPIX_RETRO_PRINTS'); 
				}
             },
             <?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
	             <?php if ($_smarty_tpl->tpl_vars['showcompany']->value) {?>
	            	 companyCombo,
	             <?php }?>
             <?php }?>
             LangContainer,
             promptContainer,
             { 
         		xtype: 'textfield', 
               	id: 'numberofdeciamalplaces', 
               	name: 'numberofdeciamalplaces', 
              	allowBlank: false,
               	maxLength: 2,
 				width:50,
                value: '<?php echo $_smarty_tpl->tpl_vars['decimalplaces']->value;?>
', 
                fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDecimalPlaces');?>
", 
               	post: true
              },
         <?php ob_start();
echo !in_array($_smarty_tpl->tpl_vars['categorycode']->value,array('TAOPIXAI','CALENDARCUSTOMISATION','SINGLEPRINT','SINGLEPRINTOPTION'));
$_prefixVariable1 = ob_get_clean();
if ($_prefixVariable1) {?>
			  {
				
				fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelComponentUpsell');?>
",
				autoWidth: true,
				items:
				[
          			onlineComponentDisplayStageOrder,
					onlineComponentDisplayStageProjectStart
				],
				style: 'padding-bottom: 5px;',
			 },
       <?php }?>
             pricingModelCombo,
             displayTypeCombo,
             requiresPageCount,
             { xtype: 'hidden', id: 'originaldisplaytype', name: 'originaldisplaytype', value: "<?php echo $_smarty_tpl->tpl_vars['categorydisplaytype']->value;?>
",  post: true}	
        ]
    });
	    
    /* create modal window for add and edit */
    var gDialogObj = new Ext.Window({
		id: 'dialog',
	  	closable:false,
	  	plain:true,
	  	modal:true,
	  	draggable:true,
	  	resizable:false,
	  	layout: 'fit',
	  	autoHeight:false,
	  	autoHeight: true,
	  	width: 610,
	  	items: dialogFormPanelObj,
	  	listeners: {
			'close': {   
				fn: function(){
    	componentCategoriesEditWindowExists = false;
				}
			}
		},
	  	cls: 'left-right-buttons', 	
	  	title: "<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
",
	  	buttons: 
		[
			
			new Ext.form.Checkbox({
				id: 'isactive',
				name: 'isactive',
				boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
",
				post: true,
				cls: 'x-btn-left', 
      			ctCls: 'width_100',
      			<?php if ($_smarty_tpl->tpl_vars['isActive']->value == 1) {?>
					checked: true
				<?php } else { ?>
					checked: false
				<?php }?>
			}),
			{
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
",
				handler: function(){ Ext.getCmp('dialog').close();},
				cls: 'x-btn-right' 		
			},
			{
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
",
				id: 'addEditButton',
				cls: 'x-btn-right', 	
				<?php if ($_smarty_tpl->tpl_vars['isEdit']->value == 0) {?>
					handler: addsaveHandler,
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
"
				<?php } else { ?>
					handler: editsaveHandler,
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdate');?>
"
				<?php }?>
			}
		]
	});
    
	<?php if ($_smarty_tpl->tpl_vars['isEdit']->value == 0) {?>
		pricingModelCombo.setValue(pricingModelStore.getAt(0).get('id'));  
    <?php }?>
	gDialogObj.show();	
}

<?php }
}
