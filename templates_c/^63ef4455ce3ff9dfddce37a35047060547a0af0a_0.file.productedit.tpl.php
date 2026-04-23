<?php
/* Smarty version 4.5.3, created on 2026-04-20 09:13:34
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\products\productedit.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69e5ee3edce413_97136336',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '63ef4455ce3ff9dfddce37a35047060547a0af0a' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\products\\productedit.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69e5ee3edce413_97136336 (Smarty_Internal_Template $_smarty_tpl) {
?>

function initialize(pParams)
{
    var usedefault_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUseDefault');?>
";
    var imagescalingbefore_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnableImageScalingBefore');?>
";
    var imagescalingbeforeenabled_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnableImageScalingBefore');?>
";
    var maxmegapixels_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMaxMegaPixels');?>
";
    var imagescalingbeforefield_txt ="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelImageScalingBefore');?>
";
    var usedefaultimagescalingbeforeVal = ("<?php echo $_smarty_tpl->tpl_vars['usedefaultimagescalingbefore']->value;?>
" == '1') ? true : false;
    var imagescalingBeforeVal = "<?php echo $_smarty_tpl->tpl_vars['imagescalingbefore']->value;?>
";
    var imagescalingBeforeEnabledVal = ("<?php echo $_smarty_tpl->tpl_vars['imagescalingbeforeenabled']->value;?>
" == '1') ? true : false;

	function forceAlphaNumeric()
	{
		var code = Ext.getCmp('skucode').getValue();
    	code = code.replace(/[^A-Z_0-9a-z-.\-]+/g, "");
    	Ext.getCmp('skucode').setValue(code);
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
	
	function editsaveHandler(btn, ev)
	{
		var selectID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('productgrid'));
		var submitURL = 'index.php?fsaction=AdminProducts.edit&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&id=' + selectID;
		var fp = Ext.getCmp('productForm'), form = fp.getForm();
		
		var paramArray = new Object();
		paramArray['isactive'] = '';
		paramArray['id'] = selectID;
		paramArray['previewupdate'] = gLogoUpdate;
		paramArray['previewremove'] = gLogoRemove;

        <?php if ($_smarty_tpl->tpl_vars['allowimagescalingbefore']->value && $_smarty_tpl->tpl_vars['producttype']->value == 2) {?>

            if (Ext.getCmp('imagescalingbeforeenabled').checked)
            {
                paramArray['usedefaultimagescalingbefore'] = '0';
                paramArray['imagescalingbeforeenabled'] = '1';
                paramArray['imagescalingbefore'] = Ext.getCmp('imagescalingbefore').getValue();
            }
            else if (Ext.getCmp('usedefaultimagescalingbefore').checked)
            {
                paramArray['usedefaultimagescalingbefore'] = '1';
                paramArray['imagescalingbeforeenabled'] = '0';
                paramArray['imagescalingbefore'] = '0.00';
            }
            else
            {
                paramArray['usedefaultimagescalingbefore'] = Ext.getCmp('usedefaultimagescalingbefore').checked ? '1' : '0';
                paramArray['imagescalingbeforeenabled'] = Ext.getCmp('imagescalingbeforeenabled').checked ? '1': '0';
                paramArray['imagescalingbefore'] = Ext.getCmp('imagescalingbefore').getValue();
            }
        <?php } else { ?>
            paramArray['usedefaultimagescalingbefore'] = usedefaultimagescalingbeforeVal ? '1' : '0';
            paramArray['imagescalingbeforeenabled'] = imagescalingBeforeEnabledVal ? '1' : '0';
            paramArray['imagescalingbefore'] = imagescalingBeforeVal;
        <?php }?>

		paramArray['retroprints'] = Ext.getCmp('retroprints').getValue();

		if (Ext.getCmp('isactive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}
		
		if (Ext.getCmp('cancreatenewprojects').checked)
		{
			paramArray['cancreatenewprojects'] = '1';
		}
		else
		{
			paramArray['cancreatenewprojects'] = '0';
		}
		
		if (Ext.getCmp('previewautoflip').checked)
		{
			paramArray['previewautoflip'] = '1';
		}
		else
		{
			paramArray['previewautoflip'] = '0';
		}
		
		if (Ext.getCmp('previewthumbnails').checked)
		{
			paramArray['previewthumbnails'] = '1';
		}
		else
		{
			paramArray['previewthumbnails'] = '0';
		}
		
		if (Ext.getCmp('previewthumbnailsview').checked)
		{
			paramArray['previewthumbnailsview'] = '1';
		}
		else
		{
			paramArray['previewthumbnailsview'] = '0';
		}

		/* Photo print price exception */
		<?php if ($_smarty_tpl->tpl_vars['producttype']->value == 2) {?>
		if (Ext.getCmp('pricetransformationstage').getValue())
		{
			paramArray['pricetransformationstage'] = Ext.getCmp('pricetransformationstage').getValue().inputValue;
		}
		<?php }?>

		paramArray['fontlisttype'] = '';
		paramArray['fontlist'] = '';

		<?php if ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1) {?>
		// Font list options
		paramArray['fontlisttype'] = Ext.getCmp('fontlisttype').getValue().value;
		paramArray['fontlist'] = Ext.getCmp('fontlist').getValue();
		<?php }?>

		Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
', saveCallback);
	}
	
	function saveCallback(pUpdated, pActionForm, pActionData)
	{	
		if (pUpdated)
		{
			var gridObj = gMainWindowObj.findById('productgrid');
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
	
	var gLogoUpdate = 0;
	var gLogoRemove = 0;
	
	function validateFileExtension(fileName)
	{
		var exp = /^.*\.(jpg|jpeg|png|gif)$/;
		return exp.test(fileName);
	}
	
	function onRemoveLogo()
	{
		gLogoRemove = 1; 
		gLogoUpdate = 0; 
		Ext.getDom('previewimage').src = 'images/admin/nopreview.gif';
		Ext.getCmp('resetLogoButton').enable();
	}

	function onResetLogo()
	{
		gLogoRemove = 0; 
		gLogoUpdate = 0; 
		var d = new Date();
		Ext.getDom('previewimage').src = '<?php echo $_smarty_tpl->tpl_vars['productpreview']->value;?>
';		
		Ext.getCmp('resetLogoButton').disable();
	}
	
	function onUploadLogo()
	{
		var theForm = Ext.getCmp('uploadform').getForm();
		theForm.submit({
			params: {code: Ext.getCmp('code').value},
			scope: this,
			success: function(form,action)
			{
				Ext.getCmp('uploaddialog').close();
				var d = new Date();
				Ext.getDom('previewimage').src = './?fsaction=AdminProducts.getPreviewImage&no=1&id=' + Ext.getCmp('code').value + '&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&tmp=1&version=' + d.getTime();
				if (action.result.msg != '')
				{
					Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", msg: action.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				}
				gLogoUpdate = 1;
				gLogoRemove = 0;
				Ext.getCmp('resetLogoButton').enable();
			},
			failure: function(form, action) 
			{
				Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorCompulsoryFields');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
			},
			waitMsg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_AlertUploading');?>
"
		});
	}
	
	function uploadLogo(btn, ev)
	{
		var fileName = Ext.getDom('preview').value.toLowerCase();
		if (!validateFileExtension(fileName)) 
		{
			if (fileName == '')
			{
				Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageLogoSelectImage');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				return;
			}
			else
			{
				Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageLogoFileTypes');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				return;
			}
		}
		onUploadLogo();
	}
	

 function createUploadDialog()
 {
	 var uploadFormPanelObj = new Ext.FormPanel({
			id: 'uploadform',
			frame: true,
			autoWidth: true,
			autoHeight: true,
			layout: 'column',
			bodyBorder: false,
			border: false,
			url: './?fsaction=AdminProducts.uploadPreviewImage&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
',
			method: 'POST',
			fileUpload: true,
			baseParams: {csrf_token: Ext.taopix.getCSRFToken()},
			items: 
			[	    
				{
					xtype: 'hidden',
					id: 'clear',
					value: 0
				},
				{
					xtype: 'box',
			    	autoEl: {
						tag: 'div',
						html: "<?php echo $_smarty_tpl->tpl_vars['previewImageText']->value;?>
"
					},
					style:'padding-bottom:5px'
				},
				{
					  xtype: 'spacer',
					  height: 5
				},
				{
					xtype: 'textfield',
					hideLabel: true,
					name: 'preview',
					id: 'preview',
					inputType: 'file'
				}
			],
			buttons:
			[
				{	
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
",
					id: 'cancelUpload',
					handler: function(){ Ext.getCmp('uploaddialog').close(); }
				},
				{
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdate');?>
",
					handler: uploadLogo
				}
			]
		});
	 
	 var gUploadObj = new Ext.Window({
		id: 'uploaddialog',
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleSelectPreviewImage');?>
",
		closable:false,
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		bodyBorder: false,
		layout: 'fit',
		autoHeight:true,
		width: 490,
		items: uploadFormPanelObj
	});
	
	gUploadObj.show();
 }		

	var retroPrints =
	{
		xtype: 'fieldset',
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleShowAsPrints');?>
",
		autoHeight: true,
		defaultType: 'textfield',
		labelWidth: 293,
		items:
		[
			new Ext.Container(
			{
				html: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageShowAsPrints');?>
",
				style: { padding: '10px 0px 5px 0px' }
			}),
			{
				xtype : 'container',
				layout : 'form',
				width: 500,
				items : [
					new Ext.form.ComboBox({
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowAsPrints');?>
",
						id: 'retroprints',
						name: 'retroprints',
						mode: 'local',
						editable: false,
						forceSelection: true,
						width: 200,
						useID: true,
						post: true,
						valueField: 'id',
						displayField: 'name',
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data: [
								[0, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOff');?>
"],
								[1, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOn');?>
"]
							]
						}),
						triggerAction: 'all',
						value: "<?php echo $_smarty_tpl->tpl_vars['retroprints']->value;?>
"
					})
				]
			},
			{
				xtype: 'numberfield',
				id: 'minimumprintsperproject',
				name: 'minimumprintsperproject',
				fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPriceMinimumNumberOfPrintsPerProject');?>
",
				width: 100,
				minValue: 1,
				value: '<?php echo $_smarty_tpl->tpl_vars['minimumprintsperproject']->value;?>
',
				validateOnBlur: true,
				post: true,
				allowBlank: false,
				allowNegative: false,
				allowDecimals: false,
				readOnly:false
			}
		]
	};

	<?php if ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1) {?>
	var fontList =
	{
		xtype: 'fieldset',
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleFontSection');?>
",
		autoHeight: true,
		defaultType: 'textfield',
		labelWidth: 293,
		items:
		[
			{
				xtype: 'radiogroup',
				id: 'fontlisttype',
				name: 'fontlisttype',
				fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelFontListSelection');?>
",
				title: '',
				layout: 'form',
				columns: 1,
				items: [
					new Ext.form.Radio(
					{
						name: 'fontlistselection',
						value: -1,
						id: 'usedefaultfonts',
						checked: <?php if (-1 === $_smarty_tpl->tpl_vars['selectedfontlist']->value) {?>1<?php } else { ?>0<?php }?>,
						boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUseDefault');?>
"
					}),
					new Ext.form.Radio(
					{
						name: 'fontlistselection',
						value: 1,
						id: 'useallfonts',
						checked: <?php if (null === $_smarty_tpl->tpl_vars['selectedfontlist']->value) {?>1<?php } else { ?>0<?php }?>,
						boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAll');?>
"
					}),
					new Ext.form.Radio(
					{
						name: 'fontlistselection',
						value: 0,
						id: 'useselectedfonts',
						checked: <?php if (!is_null($_smarty_tpl->tpl_vars['selectedfontlist']->value) && -1 !== $_smarty_tpl->tpl_vars['selectedfontlist']->value) {?>1<?php } else { ?>0<?php }?>,
						boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleFontList');?>
"
					})
				],
				listeners: {
					change: function() {
						var list = Ext.getCmp('fontlist');
						if (-1 === this.getValue().value || 1 === this.getValue().value) {
							list.setDisabled(true);
						} else {
							list.setDisabled(false).setValue(<?php if (!is_null($_smarty_tpl->tpl_vars['selectedfontlist']->value) && -1 !== $_smarty_tpl->tpl_vars['selectedfontlist']->value) {
echo $_smarty_tpl->tpl_vars['selectedfontlist']->value;
} else { ?>null<?php }?>);
						}
					}
				}
			},
			{
				xtype : 'container',
				layout : 'form',
				width: 500,
				items : new Ext.form.ComboBox({
					id: 'fontlist',
					name: 'fontlist',
					mode: 'local',
					disabled: <?php if (is_null($_smarty_tpl->tpl_vars['selectedfontlist']->value) || -1 === $_smarty_tpl->tpl_vars['selectedfontlist']->value) {?>true<?php } else { ?>false<?php }?>,
					value: <?php if (is_null($_smarty_tpl->tpl_vars['selectedfontlist']->value) || -1 === $_smarty_tpl->tpl_vars['selectedfontlist']->value) {?>null<?php } else {
echo $_smarty_tpl->tpl_vars['selectedfontlist']->value;
}?>,
					editable: false,
					forceSelection: true,
					width: 200,
					valueField: 'fontlist_id',
					displayField: 'fontlist_name',
					useID: true,
					hideLabel: false,
					allowBlank: true,
					store: new Ext.data.ArrayStore({
						id: 'fontlistvalues',
						fields: ['fontlist_id', 'fontlist_name'],
						data: <?php echo $_smarty_tpl->tpl_vars['fontlists']->value;?>

					}),
					triggerAction: 'all',
					validationEvent: false
				})
			}
		]
	};
	<?php }?>

	<?php if ($_smarty_tpl->tpl_vars['producttype']->value == 2) {?>
	var imageScalingPanel = {
		xtype:'fieldset',
		columnWidth: 0.5,
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelImageScaling');?>
",
		collapsible: false,
		autoHeight: true,
		defaultType: 'textfield',
		items: [
			new Ext.form.Checkbox({
				name: 'usedefaultimagescalingbefore',
				id: 'usedefaultimagescalingbefore',
				checked: usedefaultimagescalingbeforeVal,
				boxLabel: usedefault_txt,
				fieldLabel: imagescalingbeforefield_txt,
				disabled: imagescalingBeforeEnabledVal,
				listeners:{
					check: function() {
						Ext.getCmp('imagescalingbeforeenabled').setDisabled(Ext.getCmp('usedefaultimagescalingbefore').checked);
						if(Ext.getCmp('usedefaultimagescalingbefore').checked) {
							Ext.getCmp('imagescalingbeforeenabled').setValue(false);
						}
					}
				}
			}),
			new Ext.form.Checkbox({
				name: 'imagescalingbeforeenabled',
				id: 'imagescalingbeforeenabled',
				checked: imagescalingBeforeEnabledVal,
				boxLabel: imagescalingbefore_txt,
				disabled: usedefaultimagescalingbeforeVal,
				listeners: {
					check: function(){
						Ext.getCmp('imagescalingbefore').setDisabled(!Ext.getCmp('imagescalingbeforeenabled').checked || Ext.getCmp('usedefaultimagescalingbefore').checked);
						Ext.getCmp('usedefaultimagescalingbefore').setDisabled(Ext.getCmp('imagescalingbeforeenabled').checked);
					}
				}
			}),
			{
				id: 'imagescalingbefore',
				name: 'imagescalingbefore',
				xtype: 'numberfield',
				value: imagescalingBeforeVal,
				disabled: !imagescalingBeforeEnabledVal,
				post: true,
				validateOnBlur: true,
				maxValue: 999.99,
				minValue: 2.00,
				decimalPrecision: 2,
				forcePrecision: true,
				width: 50,
				fieldLabel: maxmegapixels_txt,
				validator: function(v)
				{
					v = String(v).replace(this.decimalSeparator, ".");
					return ((v <= 999.99) && (v >= 2));
				},
				setValue: function(v)
				{
					if (v < 2)
					{
						v = 2;
					}
					else if (v > 999.99)
					{
						v = 999.99;
					}

					var dp = this.decimalPrecision;

					if (dp < 0 || !this.allowDecimals)
					{
						dp = 0;
					}

					v = this.fixPrecision(v);
					v = Ext.isNumber(v) ? v : parseFloat(String(v).replace(this.decimalSeparator, "."));
					v = isNaN(v) ? '' : String(v.toFixed(dp)).replace(".", this.decimalSeparator);

					return Ext.form.NumberField.superclass.setValue.call(this, v);
				}
			}
		]
	};
	<?php }?>

	var d = new Date();
	var gDate = d.getTime();
	
	var customJobTicketContainer = {
            xtype: 'panel',
            layout: 'form',
            style:'border:1px solid #B5B8C8',
            bodyStyle:'padding:5px 5px 5px 5px',
            labelWidth: 100,
            width: 520,
            fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelJobTicketFields');?>
',
            items: [
                    	{ xtype: 'textfield', id: 'jobticket1name', name: 'jobticket1name', width: 200, maxLength: 100, value: '<?php echo $_smarty_tpl->tpl_vars['jobticket1name']->value;?>
', fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
', post: true},
                    	{ xtype: 'textfield', id: 'jobticket1value', name: 'jobticket1value', width: 400, maxLength: 200, value: '<?php echo $_smarty_tpl->tpl_vars['jobticket1value']->value;?>
', fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInformation');?>
', post: true},
                    	{ xtype: 'textfield', id: 'jobticket2name', name: 'jobticket2name', width: 200, maxLength: 100, value: '<?php echo $_smarty_tpl->tpl_vars['jobticket2name']->value;?>
', fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
', post: true},
                    	{ xtype: 'textfield', id: 'jobticket2value', name: 'jobticket2value', width: 400, maxLength: 200, value: '<?php echo $_smarty_tpl->tpl_vars['jobticket2value']->value;?>
', fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInformation');?>
', post: true},
                    	{ xtype: 'textfield', id: 'jobticket3name', name: 'jobticket3name', width: 200, maxLength: 100, value: '<?php echo $_smarty_tpl->tpl_vars['jobticket3name']->value;?>
', fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
', post: true},
                    	{ xtype: 'textfield', id: 'jobticket3value', name: 'jobticket3value', width: 400, maxLength: 200, value: '<?php echo $_smarty_tpl->tpl_vars['jobticket3value']->value;?>
', fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInformation');?>
', post: true},
                    	{ xtype: 'textfield', id: 'jobticket4name', name: 'jobticket4name', width: 200, maxLength: 100, maxLength: 100, value: '<?php echo $_smarty_tpl->tpl_vars['jobticket4name']->value;?>
', fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
', post: true},
                    	{ xtype: 'textfield', id: 'jobticket4value', name: 'jobticket4value', width: 400,maxLength: 200, value: '<?php echo $_smarty_tpl->tpl_vars['jobticket4value']->value;?>
', fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInformation');?>
', post: true},
                    	{ xtype: 'textfield', id: 'jobticket5name', name: 'jobticket5name', width: 200, maxLength: 100, value: '<?php echo $_smarty_tpl->tpl_vars['jobticket5name']->value;?>
', fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
', post: true},
                    	{ xtype: 'textfield', id: 'jobticket5value', name: 'jobticket5value', width: 400,maxLength: 200, value: '<?php echo $_smarty_tpl->tpl_vars['jobticket5value']->value;?>
', fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInformation');?>
', post: true}
                 ]
        };
	
	var taxLevelCombo = new Ext.form.ComboBox({
		id: 'taxlevel',
		name: 'taxlevel',
		mode: 'local',
		width:250,
		editable: false,
		forceSelection: true,
		selectOnFocus: true,
		allowBlank: false,
		triggerAction: 'all',
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelTaxLevel');?>
",
		store: new Ext.data.ArrayStore({
			id: 'taxlevelstore',
			fields: ['id', 'name'],
			data: [
				
				<?php
$__section_index_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['taxlevellist']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_0_total = $__section_index_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_0_total !== 0) {
for ($__section_index_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_0_iteration <= $__section_index_0_total; $__section_index_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_0_iteration === $__section_index_0_total);
?>
				<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
					["<?php echo $_smarty_tpl->tpl_vars['taxlevellist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['taxlevellist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"]
				<?php } else { ?>
					["<?php echo $_smarty_tpl->tpl_vars['taxlevellist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['taxlevellist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"],
				<?php }?>
				<?php
}
}
?>
				
			]
		}),
		valueField: 'id',
		displayField: 'name',
		value: '<?php echo $_smarty_tpl->tpl_vars['taxlevel']->value;?>
',
		useID: true,
		post: true
	});
	
	var dialogFormPanelObj = new Ext.FormPanel({
		id: 'productForm',
        labelAlign: 'left',
        labelWidth:150,
        autoHeight: true,
        frame:true,
        bodyStyle:'padding:3px 5px 0;',
        items: [{ 
			xtype: 'panel', id: 'topPanel', layout: 'column',style:'background:#c9d8ed; padding: 3px 0; border:1px solid #8ca9cf', columns: 2, plain:true, 
			bodyBorder: false, border: false, 	defaults:{labelWidth: 70}, bodyStyle:'padding:5px 5px 0; border-top: 0px',
			items: 
			[
				new Ext.Container({ layout: 'form', defaults:{xtype: 'textfield', width: 195}, width:290,
				items: 
					{
					 xtype: 'textfield', id: 'code', name: 'code', width: 200, disabled:true, maxLength: 50, value: '<?php echo $_smarty_tpl->tpl_vars['code']->value;?>
', allowBlank:false, fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCode');?>
', readOnly:true, post: true
					}
				})
				
				<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
					,
					new Ext.Container({ layout: 'form', defaults:{xtype: 'textfield', width: 225}, width:350, items: { xtype: 'textfield', id: 'company', disabled:true, fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", readOnly:true, name: 'company', value: '<?php echo $_smarty_tpl->tpl_vars['companycode']->value;?>
',  post: true} })
				<?php }?>
			]
		},
		
		{ /* tabpanel */
			xtype: 'tabpanel',
			id: 'maintabpanel',
			deferredRender: false,
			activeTab: 0,
			height: 510,
			shadow: true,
			plain:true,
			bodyBorder: false,
			layoutOnTabChange: true,
			border: true,
			style: 'margin-top: 7px',
			bodyStyle:'border-right: 1px solid #96bde7;',		
			defaults:{frame: false, autoScroll: true, hideMode:'offsets', layout: 'form', labelWidth: 140,  style:'padding:10px; background-color: #eaf0f8;'},
			items: 
			[
				{
					title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSettings');?>
",
					id: 'settingsTab',
					items: 
					[
						{ xtype: 'textfield', id: 'skucode', name: 'skucode', width: 250, maxLength: 50, value: '<?php echo $_smarty_tpl->tpl_vars['skucode']->value;?>
', fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSKUCode');?>
', listeners:{	blur:{	fn: forceAlphaNumeric }	}, post: true},
						{ xtype: 'textfield', id: 'name', name: 'name', width: 250, disabled:true, value: '<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
', allowBlank:false, fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
', readOnly:true, post: true},
						{ xtype: 'textfield', id: 'category', name: 'category', disabled:true, width: 250, value: '<?php echo $_smarty_tpl->tpl_vars['category']->value;?>
', allowBlank:false, fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCategory');?>
', readOnly:true, post: true},
						taxLevelCombo,
						{ xtype: 'textfield', id: 'cost', name: 'cost', width: 100, value: '<?php echo $_smarty_tpl->tpl_vars['cost']->value;?>
', allowBlank:false, validator: function(v){ return validate(v,true,false);  }, fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCost');?>
', readOnly:false, post: true},
						{ xtype: 'textfield', id: 'weight', name: 'weight', width: 100, value: '<?php echo $_smarty_tpl->tpl_vars['weight']->value;?>
', allowBlank:false, validator: function(v){ return validate(v,true,false); }, fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShippingWeight');?>
', readOnly:false, post: true},
						customJobTicketContainer,
						{ 
							xtype: 'checkbox', 
							id: 'cancreatenewprojects',
							name: 'cancreatenewprojects',
							boxLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCanCreateNewProjects');?>
',
							<?php if ($_smarty_tpl->tpl_vars['createnewprojectschecked']->value == 1) {?>
								checked: true
							<?php } else { ?>
								checked: false
							<?php }?>
						}
					]
				},
				{
					title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonPreview');?>
",
					id: 'previewTab',
					layout: 'form',
					listeners: { 'beforeshow': function(){ Ext.getCmp('maintabpanel').doLayout(); }},
					items: 
					[
						{
        					xtype:'fieldset',
        					columnWidth: 0.5,
					        title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProductPreviewIcon');?>
",
					        collapsible: false,
					        autoHeight:true,
					        defaultType: 'textfield',
					        items :[
					        	{
								    xtype: 'buttongroup',
								    frame: false,
								    columns: 5,
								    items: 
									[
										{
											text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdatePreviewImage');?>
",
											handler: function() 
											{
												createUploadDialog();												
												Ext.getDom('preview').value = '';
											}
										},
										{
											xtype: 'spacer',
											width: 5
										},
										{
											text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonRemovePreviewImage');?>
",
											handler: onRemoveLogo
										},
										{
											xtype: 'spacer',
											width: 5
										},
										{
											text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonResetPreviewImage');?>
",
											id: 'resetLogoButton',
											disabled: true,
											handler: onResetLogo
										}
									]
								},
								{
									xtype: 'spacer',
									height: 5
								},
								{
									xtype: 'box',
								   	autoEl: 
								   	{
										tag: 'div',  
								        html: '<img style="border: 1px solid;" id="previewimage" name="previewimage" src="<?php echo $_smarty_tpl->tpl_vars['productpreview']->value;?>
">'
								    }
								}
					        ]
					    },
					    
					    {
        					xtype:'fieldset',
        					columnWidth: 0.5,
					        title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPageTurningPreview');?>
",
					        collapsible: false,
					        autoHeight:true,
					        defaultType: 'textfield',
					        items :[
					        	new Ext.form.ComboBox({id: 'previewtype', name: 'previewtype', hiddenName:'previewType_hn', width:270, allowBlank: false,
									forceSelection: true, editable: false, mode: 'local', 	hiddenId:'previewType_hi', validationEvent:false,
									store: new Ext.data.ArrayStore({ id: 0, fields: ['previewType_id', 'previewType_name'], data: [[1, '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelHorizontalPageTurning');?>
'], [2, '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelVerticalPageTurning');?>
'], [3, '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSlideshow');?>
']] }), 
									valueField: 'previewType_id', displayField: 'previewType_name', useID: true, post: true, fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPreviewType');?>
", triggerAction: 'all',
									value: '<?php echo $_smarty_tpl->tpl_vars['previewtype']->value;?>
'
								}),
					        	new Ext.form.ComboBox({id: 'previewcovertype', name: 'previewcovertype', hiddenName:'coverType_hn', width:270, allowBlank: false,
									forceSelection: true, editable: false, mode: 'local', 	hiddenId:'coverType_hi', validationEvent:false,
									store: new Ext.data.ArrayStore({ id: 0, fields: ['coverType_id', 'coverType_name'], data: [[0, '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCoverHard');?>
'], [1, '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCoverSoft');?>
']] }), 
									valueField: 'coverType_id', displayField: 'coverType_name', useID: true, post: true, fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCoverType');?>
", triggerAction: 'all',
									value: '<?php echo $_smarty_tpl->tpl_vars['previewcovertype']->value;?>
'
								}),
						    	{ 
									xtype: 'checkbox', 
									id: 'previewautoflip',
									name: 'previewautoflip',
									boxLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAutoFlip');?>
',
									hideLabel: true,
									<?php if ($_smarty_tpl->tpl_vars['previewautoflip']->value == 1) {?>
										checked: true
									<?php } else { ?>
										checked: false
									<?php }?>
								}, 
								{ 
									xtype: 'checkbox', 
									id: 'previewthumbnails',
									name: 'previewthumbnails',
									hideLabel: true,
									boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelThumbnailsVisible');?>
",
									<?php if ($_smarty_tpl->tpl_vars['previewthumbnails']->value == 1) {?>
										checked: true,
									<?php } else { ?>
										checked: false,
									<?php }?>
									listeners: { check: refreshThumbnailsDisplay }
								},
								{
									xtype: 'checkbox',
									id: 'previewthumbnailsview',
									name: 'previewthumbnailsview',
									hideLabel: true,
									boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelThumbnailsDisplayedAtTheBottom');?>
",
									<?php if ($_smarty_tpl->tpl_vars['previewthumbnailsview']->value == 1) {?>
										checked: true
									<?php } else { ?>
										checked: false
									<?php }?>
								}
					        ]
					    }	
					]
				}
				<?php if ($_smarty_tpl->tpl_vars['producttype']->value == 2) {?>
				,{
					title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPhotoPrintsOptions');?>
",
					layout: 'form',
					id: 'photoPrintTab',
					labelWidth: 240,
					items:
					[
						<?php if ($_smarty_tpl->tpl_vars['allowimagescalingbefore']->value) {?>
                        imageScalingPanel,
                        <?php }?>
						{
							xtype:'fieldset',
							columnWidth: 0.5,
							title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPricingOptions');?>
",
							collapsible: false,
							autoHeight:true,
							defaultType: 'textfield',
							items :[
								new Ext.form.ComboBox({
									id: 'productoptions',
									name: 'productoptions',
									width: 250,
									fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPricingModel');?>
",
									mode: 'local',
									editable: false,
									forceSelection: true,
									store: new Ext.data.ArrayStore({
										id: 'sppos',
										fields: ['id', 'name'],
										data: [
											[1, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProductOptionPricingPerPicture');?>
"],
											[2, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProductOptionPricingPerComponentSubComponent');?>
"]
										]
									}),
									selectOnFocus: true,
									triggerAction: 'all',
									valueField: 'id',
									displayField: 'name',
									useID: true,
									allowBlank: false,
									value: <?php echo $_smarty_tpl->tpl_vars['productoptions']->value;?>
,
									post: true
								}),
								new Ext.form.RadioGroup({
									id: 'pricetransformationstage',
									name: 'pricetransformationstage',
									width: 250,
									fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCalculationMethod');?>
",
									columns: 2,
									layout:'row',
									items: [
										{boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPriceTransformationStagePre');?>
", name: 'pricetransformationstage', inputValue: 1},
										{boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPriceTransformationStagePost');?>
", name: 'pricetransformationstage', inputValue: 2},
									],
									value: <?php echo $_smarty_tpl->tpl_vars['pricetransformationstage']->value;?>
,
								})
							]
						}
						/* Option only displays if online is active*/
						<?php if ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1) {?>
						,
						{
							xtype:'fieldset',
							columnWidth: 0.5,
							title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOnlineOptions');?>
",
							collapsible: false,
							autoHeight:true,
							defaultType: 'textfield',
							items :[
							{
								xtype: 'numberfield',
								id: 'minimumprintsperproject',
								name: 'minimumprintsperproject',
								fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPriceMinimumNumberOfPrintsPerProject');?>
",
								width: 100,
								minValue: 1,
								value: '<?php echo $_smarty_tpl->tpl_vars['minimumprintsperproject']->value;?>
',
								validateOnBlur: true,
								post: true,
								allowBlank: false,
								allowNegative: false,
								allowDecimals: false,
								readOnly:false
							}]
						}
						<?php }?>
					]
				}
				<?php }?>
                <?php if ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1) {?>
                ,
                {
                    title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleOnlineDesignerSettings');?>
",
                    layout: 'form',
                    id: 'onlineDesignerSettingsTab',
                    labelWidth: 280,
                    items: [
						fontList
						<?php if (($_smarty_tpl->tpl_vars['allowretroprints']->value) && ($_smarty_tpl->tpl_vars['producttype']->value == 0) && ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1)) {?>
						, retroPrints
						<?php }?>
                    ]
                }
                <?php }?>
			]
		}]
	});
		
	 gDialogObj = new Ext.Window({
			id: 'dialog',
		  	closable:false,
		  	title: "<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
",
		  	plain:true,
		  	modal:true,
		  	draggable:true,
		 	resizable:false,
		  	layout: 'fit',
		  	height: 'auto',
		  	width: 750,
		  	cls: 'left-right-buttons',
		  	items: dialogFormPanelObj,
		  	listeners: {
				'close': {   
					fn: function(){
		                productEditWindowExists = false;
					}
				}
			},
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
							<?php if ($_smarty_tpl->tpl_vars['activechecked']->value == 1) {?>
						checked: true
					<?php } else { ?>
						checked: false
					<?php }?>
				}),
				{ text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
", handler: function(){ gDialogObj.close();} },
				{ text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
", id: 'addEditButton',
				handler: editsaveHandler,
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdate');?>
"
				}
			]
		});

		refreshThumbnailsDisplay();
		var mainPanel = Ext.getCmp('dialog');
		mainPanel.show();
}
		
function ArrayIndexOf(source, v) 
{
    for (var i = 0, l = source.length; i < l; i++) 
    {
        if (source[i] == v) 
        {
            return i;
        }
    }
    return -1;
}

function refreshThumbnailsDisplay()
{
	var previewThumbnailsVisible = Ext.getCmp('previewthumbnails');
	var previewthumbnailsatbottom = Ext.getCmp('previewthumbnailsview');

	if (previewThumbnailsVisible.checked)
	{
		previewthumbnailsatbottom.enable();
	}
	else
	{
		previewthumbnailsatbottom.setValue(false);
		previewthumbnailsatbottom.disable();
	}
}


<?php }
}
