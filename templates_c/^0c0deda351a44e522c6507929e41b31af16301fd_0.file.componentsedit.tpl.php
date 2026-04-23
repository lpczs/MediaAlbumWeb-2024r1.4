<?php
/* Smarty version 4.5.3, created on 2026-04-09 05:49:23
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\components\componentsedit.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69d73de3a5d8a3_21476776',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0c0deda351a44e522c6507929e41b31af16301fd' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\components\\componentsedit.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69d73de3a5d8a3_21476776 (Smarty_Internal_Template $_smarty_tpl) {
?>
function initialize(pParams)
{			
	<?php echo $_smarty_tpl->tpl_vars['localizedcodesjavascript']->value;?>

	<?php echo $_smarty_tpl->tpl_vars['localizednamesjavascript']->value;?>

	<?php echo $_smarty_tpl->tpl_vars['languagecodesjavascript']->value;?>

	<?php echo $_smarty_tpl->tpl_vars['languagenamesjavascript']->value;?>

	<?php echo $_smarty_tpl->tpl_vars['sitegrouplocalizedcodesjavascript']->value;?>

	<?php echo $_smarty_tpl->tpl_vars['sitegrouplocalizednamesjavascript']->value;?>

	<?php echo $_smarty_tpl->tpl_vars['moreinfolinktextcodes']->value;?>

	<?php echo $_smarty_tpl->tpl_vars['moreinfolinktextnames']->value;?>

	
	var gLogoUpdate = 0;
	var gLogoRemove = 0;

	function validateFileExtension(fileName)
	{
		var exp = /^.*\.(jpg|jpeg|png|gif)$/;
		return exp.test(fileName);
	}

	/**
	 * Checks that the More Info Link text has been entered when a More Info Link URL has been set.
	 * 
	 * @param ExtJs.component pMoreInfoLinkURLCmp The More Info Link URL component object.
	 * @param ExtJs.component pMoreInfoLinkTextCmp The More Info Link Text LangPanel component.
	 * @return boolean True if the text is set for a URL.
	 */
	function validateMoreInfoLinkText(pMoreInfoLinkURLCmp, pMoreInfoLinkTextCmp)
	{
		var valid = true;

		var moreInfoLinkURL = pMoreInfoLinkURLCmp.getValue();
		if ((moreInfoLinkURL !== '') && (moreInfoLinkURL !== 'http://') && (moreInfoLinkURL !== 'https://') && (pMoreInfoLinkTextCmp.convertTableToString() === '')) {
			Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsErrorNoMoreInfoLinkText');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
			valid = false;
		}

		return valid;
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
		Ext.getDom('previewimage').src = "<?php echo $_smarty_tpl->tpl_vars['componentpreview']->value;?>
";
		Ext.getCmp('resetLogoButton').disable();
	}
			
	
	function forceAlphaNumeric(pObj)
	{
		var code = Ext.getCmp(pObj.id).getValue();
    	
		if (pObj.id == 'skucode')
		{
			code = code.replace(/[^A-Z_0-9a-z-.\-]+/g, "");
		}
		else
		{
			code = code.toUpperCase();
			code = code.replace(/[^A-Z_0-9\-]+/g, "");
		}

    	
    	Ext.getCmp(pObj.id).setValue(code);
	}

	function validate(value, allowDecimal, allowNegative)
	{
		if (! isNumeric(value, allowDecimal, allowNegative))
		{
			valid = false;
		}
		else
		{
			valid = true;
		}
		return valid;
	}

  	function validateUrl(obj)
	{
		if ((obj.getValue() !== 'http://') && (obj.getValue() !== 'https://') && (obj.getValue() !== ''))
		{
			var domainValid = (Ext.form.VTypes.url(obj.getValue()));
			var regexp = /^((([hH][tT][tT][pP][sS]?|[fF][tT][pP])\:\/\/)?([\w\.\-]+(\:[\w\.\&%\$\-]+)*@)?((([^\s\(\)\<\>\\\"\.\[\]\,@;:]+)(\.[^\s\(\)\<\>\\\"\.\[\]\,@;:]+)*(\.[a-zA-Z]{2,4}))|((([01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}([01]?\d{1,2}|2[0-4]\d|25[0-5])))(\b\:(6553[0-5]|655[0-2]\d|65[0-4]\d{2}|6[0-4]\d{3}|[1-5]\d{4}|[1-9]\d{0,3}|0)\b)?((\/[^\/][\w\.\,\?\'\\\/\+&%\$#\=~_\-@]*)*[^\.\,\?\"\'\(\)\[\]!;<>{}\s\x7F-\xFF])?)$/;
			var ipValid = regexp.test(obj.getValue());
			return (domainValid || ipValid);
		}

		obj.clearInvalid();
		return true;
	};

	function getLocalisedData(pLocalizedNamesArray, pLocalizedCodesArray)
	{
		var langListStore = [];
		var dataList = [];

		for (var i = 0; i < gAllLanguageCodesArray.length; i++)
		{
			var languageName = "";
			var languageCode = "";
			var languageNameIndex = ArrayIndexOf(gAllLanguageCodesArray, pLocalizedCodesArray[i]);

			if (languageNameIndex > -1)
			{
				languageName = gAllLanguageNamesArray[languageNameIndex];
				languageCode = gAllLanguageCodesArray[languageNameIndex];
			}
			
			if ((languageName) && (languageName!=undefined)) dataList.push([languageCode, languageName, pLocalizedNamesArray[i]]);

			if (ArrayIndexOf(pLocalizedCodesArray, gAllLanguageCodesArray[i]) === -1)
			{
				langListStore.push([gAllLanguageCodesArray[i], gAllLanguageNamesArray[i]]);
			}
		}

		return {'langList': langListStore, 'dataList': dataList};
	};
	
	function componentAddSaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminComponents.add&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&id=0';
		var fp = Ext.getCmp('componentsForm'), form = fp.getForm();
		var submit = true;
	
		var paramArray = new Object();
		paramArray['isactive'] = '';
		paramArray['previewupdate'] = gLogoUpdate;
		paramArray['previewremove'] = gLogoRemove;
		
		var metadataGroupId = Ext.taopix.gridSelection2IDList(Ext.getCmp('keywordsGroupsGrid'));
		if (metadataGroupId == '')
		{
			metadataGroupId = 0;
		}
		paramArray['metadatagroupheader'] = metadataGroupId;
	
		if (Ext.getCmp('iscomponentActive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}
	
		if (Ext.getCmp('isListDefault').checked)
		{
			paramArray['default'] = '1';
		}
		else
		{
			paramArray['default'] = '0';
		}
		
		if (Ext.getCmp('orderfooterusesprodqty').checked)
		{
			paramArray['orderfooterusesprodqty'] = '1';
		}
		else
		{
			paramArray['orderfooterusesprodqty'] = '0';
		}
		
		if (Ext.getCmp('storewhennotselected').checked)
		{
			paramArray['storewhennotselected'] = '1';
		}
		else
		{
			paramArray['storewhennotselected'] = '0';
		}

		if (!Ext.getCmp('componentname').isValid())
		{
			Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsErrorNoName');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
			submit = false;
		}

		if ((submit) && (Ext.getCmp('componentmoreinfolinkurl'))) {
			submit = validateMoreInfoLinkText(Ext.getCmp('componentmoreinfolinkurl'), Ext.getCmp('componentmoreinfolinktext'));
		}

		var gridObj = Ext.getCmp('maingrid');
		var selRecords = gridObj.selModel.getSelections();
		var requiresPageCount = selRecords[0].data.requirespagecount;
		
		if (requiresPageCount == 1)
		{
			var minPageCount = 0;
		    if (Ext.getCmp('minpagecount').getValue() > 0)
		    {
		    	minPageCount = Ext.getCmp('minpagecount').getValue();
		    }
		    
		    if (minPageCount < 1)
		    {
		    	Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorMinPageCountError');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
		    	submit = false;
		    }
			    
		    var maxPageCount = 0;
		    if (Ext.getCmp('maxpagecount').getValue() > 0)
		    {
		    	maxPageCount = Ext.getCmp('maxpagecount').getValue();
		    }
		    
		    if (minPageCount > maxPageCount)
		    {
		    	Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorMaxPageCountError');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
		    	submit = false;
		    }
		}		
		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
", componentSaveCallback);
		}
	}

	function componentEditSaveHandler(btn, ev)
	{
		var id = Ext.taopix.gridSelection2IDList(Ext.getCmp('componentgrid'));
	
		var submitURL = 'index.php?fsaction=AdminComponents.edit&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&id='+id;
		var fp = Ext.getCmp('componentsForm'), form = fp.getForm();
		var submit = true;
	
		var paramArray = new Object();
		paramArray['isactive'] = '';
		paramArray['previewupdate'] = gLogoUpdate;
		paramArray['previewremove'] = gLogoRemove;
		
		
		var metadataGroupId = Ext.taopix.gridSelection2IDList(Ext.getCmp('keywordsGroupsGrid'));
		if (metadataGroupId == '')
		{
			metadataGroupId = 0;
		}
		paramArray['metadatagroupheader'] = metadataGroupId;
		
		if (Ext.getCmp('iscomponentActive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}
	
		if (Ext.getCmp('isListDefault').checked)
		{
			paramArray['default'] = '1';
		}
		else
		{
			paramArray['default'] = '0';
		}
		
		if (Ext.getCmp('orderfooterusesprodqty').checked)
		{
			paramArray['orderfooterusesprodqty'] = '1';
		}
		else
		{
			paramArray['orderfooterusesprodqty'] = '0';
		}
		
		if (Ext.getCmp('storewhennotselected').checked)
		{
			paramArray['storewhennotselected'] = '1';
		}
		else
		{
			paramArray['storewhennotselected'] = '0';
		}
	
		if (!Ext.getCmp('componentname').isValid())
		{
			Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsErrorNoName');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
			submit = false;
		}

		if ((submit) && (Ext.getCmp('componentmoreinfolinkurl'))) {
			submit = validateMoreInfoLinkText(Ext.getCmp('componentmoreinfolinkurl'), Ext.getCmp('componentmoreinfolinktext'));
		}
		
		var gridObj = Ext.getCmp('maingrid');
		var selRecords = gridObj.selModel.getSelections();
		var requiresPageCount = selRecords[0].data.requirespagecount;
		
		if (requiresPageCount == 1)
		{
			var minPageCount = 0;
		    if (Ext.getCmp('minpagecount').getValue() > 0)
		    {
		    	minPageCount = Ext.getCmp('minpagecount').getValue();
		    }
		    if (minPageCount < 1)
		    {
		    	Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorMinPageCountError');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
		    	submit = false;
		    }
			    
		    var maxPageCount = 0;
		    if (Ext.getCmp('maxpagecount').getValue() > 0)
		    {
		    	maxPageCount = Ext.getCmp('maxpagecount').getValue();
		    }
		    
		    if (minPageCount > maxPageCount)
		    {
		    	Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorMaxPageCountError');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
		    	submit = false;
		    }
		}
	
		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
", componentSaveCallback);
		}
	}

	function componentSaveCallback(pUpdated, pActionForm, pActionData)
	{		
		if (pUpdated)
		{
			var gridObj = Ext.getCmp('componentgrid');
			var dataStore = gridObj.store;	
		
			gridObj.store.reload();
			gComponentsAddDialogObj.close();
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
	
	function onUploadLogo()
	{
		var theForm = Ext.getCmp('uploadcomponentform').getForm();
		theForm.submit({
			scope: this,
			clientValidation: false,
			success: function(form,action)
			{
				Ext.getCmp('uploaddialog').close();
				var d = new Date();
				Ext.getDom('previewimage').src = './?fsaction=AdminComponents.getPreviewImage&no=1&id=' + Ext.getCmp('componentcode').value + '&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
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
", msg: 'Failed', buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
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
	
	function createUploadDialog()
	{
		var uploadFormPanelObj = new Ext.FormPanel({
			id: 'uploadcomponentform',
			frame:true,
			autoWidth: true,
			autoHeight:true,
			layout: 'column',
			bodyBorder: false,
			border: false,
			url: './?fsaction=AdminComponents.uploadPreviewImage&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
',
			method: 'POST',
			baseParams: {csrf_token: Ext.taopix.getCSRFToken()},
			fileUpload: true,
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
	
	var d = new Date();
	var gDate = d.getTime();
	
	var gridObj = Ext.getCmp('maingrid');
	var selRecords = gridObj.selModel.getSelections();
	var category = selRecords[0].data.code;
	var requiresPageCount = selRecords[0].data.requirespagecount;
	var isList = selRecords[0].data.islist;

	var str_LabelLanguageName    = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLanguageName');?>
";
	var str_localizedNameLabel   = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
";
		
	var deleteImg = '<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/delete.png';
	var addimg = '<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/add.png';
	
	var localisedData = getLocalisedData(gLocalizedNamesArray, gLocalizedCodesArray);
	var langListStore = localisedData.langList;
	var dataList = localisedData.dataList;
	
    var localisedData2 = getLocalisedData(gSiteGroupLocalizedNamesArray, gSiteGroupLocalizedCodesArray);
    var promptList = localisedData2.langList;
	var dataList2 = localisedData2.dataList;

	var componentNamePanel = new Ext.taopix.LangPanel({
		id: 'componentname',
		name:'componentname',
		height:150,
        width: 480,
		post:true,
		data: {langList: langListStore, dataList: dataList},
		settings: 
		{ 
			headers:     {langLabel: str_LabelLanguageName,  textLabel: str_localizedNameLabel, deletePic: deleteImg, addPic: addimg},
			defaultText: {langBlank: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSelectLanguage');?>
",  textBlank: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTypeValue');?>
", defaultValue: "<?php echo $_smarty_tpl->tpl_vars['defaultlanguagecode']->value;?>
"},
			columnWidth: {langCol: 190, textCol: 227, delCol: 35},
			fieldWidth:  {langField: 185, textField: 205},
			errorMsg:    {blankValue: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTextFieldBlank');?>
"}
		}
	});
  
  	var componentNameContainer = 
  	{
		xtype: 'panel',
        width: 480,
      	bodyBorder: false,
        border:false,
        fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
",
        items: componentNamePanel
    };
  
  	var infoPanel = new Ext.taopix.LangPanel({
		id: 'componentinfo', 
		name:'componentinfo',
		height:150,
        width: 480,
		post:true,
		data: {langList: promptList, dataList: dataList2},
		settings: 
		{ 
			headers:     {langLabel: str_LabelLanguageName,  textLabel: str_localizedNameLabel, deletePic: deleteImg, addPic: addimg},
			defaultText: {langBlank: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSelectLanguage');?>
",  textBlank: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTypeValue');?>
", defaultValue: "<?php echo $_smarty_tpl->tpl_vars['defaultlanguagecode']->value;?>
"},
			columnWidth: {langCol: 190, textCol: 227, delCol: 35},
			fieldWidth:  {langField: 185, textField: 205},
			errorMsg:    {blankValue: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTextFieldBlank');?>
"}
		}
	});
	
	var infoContainer = 
	{
    	xtype: 'panel',
        width: 480,
      	bodyBorder: false,
        border:false,
        fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInfo');?>
",
        items: infoPanel
	};

	<?php if ($_smarty_tpl->tpl_vars['canshowmoreinfolink']->value) {?>
  	var moreInfoLinkURL = new Ext.form.TextField({
		id: 'componentmoreinfolinkurl',
		name: 'componentmoreinfolinkurl',
		width: 480,
		validateOnBlur: true,
        validator: function(v){ return validateUrl(this); },
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMoreInfoLinkURL');?>
",
		<?php if ($_smarty_tpl->tpl_vars['moreinfolinkurl']->value === '') {?>
			value: "http://",
  		<?php } else { ?>
			value: "<?php echo $_smarty_tpl->tpl_vars['moreinfolinkurl']->value;?>
",
		<?php }?>
		post: true
	});

	var moreinfoLinkTextLocalisedData = getLocalisedData(gMoreInfoLinkTextNames, gMoreInfoLinkTextCodes);
  	var moreInfoLinkTextPanel = new Ext.taopix.LangPanel({
		id: 'componentmoreinfolinktext', 
		name:'componentmoreinfolinktext',
		height: 150,
		width: 480,
		post: true,
		data: {langList: moreinfoLinkTextLocalisedData.langList, dataList: moreinfoLinkTextLocalisedData.dataList},
		settings: 
		{ 
			headers: {langLabel: str_LabelLanguageName,  textLabel: str_localizedNameLabel, deletePic: deleteImg, addPic: addimg},
			defaultText: {langBlank: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSelectLanguage');?>
",  textBlank: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTypeValue');?>
", defaultValue: "<?php echo $_smarty_tpl->tpl_vars['defaultlanguagecode']->value;?>
"},
			columnWidth: {langCol: 190, textCol: 227, delCol: 35},
			fieldWidth: {langField: 185, textField: 205},
			errorMsg: {blankValue: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTextFieldBlank');?>
"}
		}
	});

	var moreInfoLinkTextContainer = 
	{
		xtype: 'panel',
		width: 480,
		bodyBorder: false,
		border: false,
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMoreInfoLinkText');?>
",
		items: moreInfoLinkTextPanel
	};
	<?php }?>
	
	Ext.layout.FormLayout.prototype.trackLabels = true;
			
	var companyCombo = new Ext.taopix.CompanyCombo({
		id: 'company',
		name: 'company',
		width:300,
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
",
		hideLabel:false,
		allowBlank:false,
		disabled: '<?php echo $_smarty_tpl->tpl_vars['controldisabled']->value;?>
',
		<?php if ($_smarty_tpl->tpl_vars['companycode']->value == '') {?>
			defvalue: 'GLOBAL',
		<?php } else { ?>
			defvalue: '<?php echo $_smarty_tpl->tpl_vars['companycode']->value;?>
',
		<?php }?>
		options: {
			ref: '<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', 
			storeId: 'companyStore', 
			includeGlobal: '<?php echo $_smarty_tpl->tpl_vars['includeglobal']->value;?>
', 
			includeShowAll: '0', 
			onchange: function(){var companyCode = companyCombo.getValue(); if (companyCode == 'GLOBAL') companyCode = '';}
		}
	});
	
	var skuCode = new Ext.form.TextField({
		id: 'skucode',
		name: 'skucode',
		maxLength: 50,
		width: 200,
		validationEvent:false,
		validateOnBlur: false,
		value: "<?php echo $_smarty_tpl->tpl_vars['sku']->value;?>
",
		listeners: {'blur': {fn: function(obj){forceAlphaNumeric(obj)}}},
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSKUCode');?>
",
		post: true
	});
	
	var minPageCount = new Ext.form.NumberField({
		id: 'minpagecount',
		name: 'minpagecount',
		maxLength: 10,
		width: 50,
		validationEvent:false,
		validateOnBlur: false,
		value: "<?php echo $_smarty_tpl->tpl_vars['minPageCount']->value;?>
",
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMinPageCount');?>
",
		post: true,
		validator: function(v){ return validate(v,false,false);  }
	});
	
	var maxPageCount = new Ext.form.NumberField({
		id: 'maxpagecount',
		name: 'maxpagecount',
		maxLength: 10,
		width: 50,
		validationEvent:false,
		validateOnBlur: false,
		value: "<?php echo $_smarty_tpl->tpl_vars['maxPageCount']->value;?>
",
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMaxPageCount');?>
",
		post: true,
		validator: function(v){ return validate(v,false,false);  }
	});
	
	var isListDefault = new Ext.form.Checkbox({
		id: 'isListDefault',
		name: 'isListDefault',
		boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDefault');?>
",
		post: true,
		<?php if ($_smarty_tpl->tpl_vars['checkedByDefault']->value == 1) {?>
		checked: true
		<?php } else { ?>
    	checked: false
    	<?php }?>
	});
	
	var orderFooterUsesProductQuantity = new Ext.form.Checkbox({
		id: 'orderfooterusesprodqty',
		name: 'orderfooterusesprodqty',
		boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderFooterUsesProdQuantity');?>
",
		post: true,
		<?php if ($_smarty_tpl->tpl_vars['orderfooterusesprodqty']->value == 1) {?>
		checked: true
		<?php } else { ?>
    	checked: false
    	<?php }?>
	});
	
	var saveCheckBoxComponentComponentNotSelected = new Ext.form.Checkbox({
		id: 'storewhennotselected',
		name: 'storewhennotselected',
		boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStoreComponentWhenNotSelected');?>
",
		post: true,
		<?php if ($_smarty_tpl->tpl_vars['storewhennotselected']->value == 1) {?>
		checked: true
		<?php } else { ?>
    	checked: false
    	<?php }?>
	});
		
	var unitCost = new Ext.form.TextField({
		id: 'unitcost',
		name: 'unitcost',
		allowBlank:false,
		maxLength: 10,
		width: 70,
		validator: function(v){ return validate(v,true,false);  },
		value: "<?php echo $_smarty_tpl->tpl_vars['unitCost']->value;?>
",
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCost');?>
",
		post: true,
		validator: function(v){ return validate(v,true,false);  }
	});
	
	var weight = new Ext.form.TextField({
		id: 'weight',
		name: 'weight',
		allowBlank:false,
		maxLength: 10,
		width: 70,
		validator: function(v){ return validate(v,true,false);  },
		value: "<?php echo $_smarty_tpl->tpl_vars['weight']->value;?>
",
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShippingWeight');?>
",
		post: true,
		validator: function(v){ return validate(v,true,false);  }
	});
	
	var taxLevelCombo = new Ext.form.ComboBox({
		id: 'orderfootertaxlevel',
		name: 'orderfootertaxlevel',
		mode: 'local',
		width:250,
		editable: false,
		forceSelection: true,
		selectOnFocus: true,
		allowBlank: false,
		triggerAction: 'all',
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderFooterTaxLevel');?>
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
	
	var keywordGroupsCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({ singleSelect: true });
	
	var keywordGroupsDataStoreObj = new Ext.data.Store({
		remoteSort: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminMetadataKeywordsGroups.getGridData&ref=' + sessionId + '&section=COMPONENT&size=440&defaultscol=1' }),
		method:'POST',
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([ 
				{name: 'groupId', mapping: 0},
				{name: 'keywordCode', mapping: 3},
				{name: 'keywordDefaults', mapping: 4}
			])
		),
		sortInfo:{field: 'code', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	}); 
	keywordGroupsDataStoreObj.load();
	
	
	var keywordGroupsColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {	sortable: true, resizable: true },
		columns: [keywordGroupsCheckBoxSelectionModelObj,
	    	{ header: "groupId", dataIndex: 'groupId', menuDisabled:true, hidden:true },
	    	{ id: 'keywordsCol', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleMetaDataKeyWordGroups');?>
", dataIndex: 'keywordCode', width:440, menuDisabled:  true},
	    	{ id: 'keywordDefaultsCol', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDefaultValues');?>
", dataIndex: 'keywordDefaults', width:150, menuDisabled:  true}
        ]
	});
	              
	var dialogFormPanelObj = new Ext.taopix.FormPanel({
		id: 'componentsForm',
		header: false,
		frame:true,
		autoWidth: true,
		autoHeight:true,
		layout: 'form',
		defaultType: 'textfield',
		bodyBorder: false,
		border: false,
		defaults: {labelWidth:110},
		items: 
		[
			{ 
				xtype: 'panel', id: 'topPanel', layout: 'column',style:'background:#c9d8ed; padding: 3px 0; border:1px solid #8ca9cf', columns: 2, plain:true, 
				bodyBorder: false, border: false, 	defaults:{labelWidth: 70}, bodyStyle:'padding:5px 5px 0; border-top: 0px',
				items: 
				[
					new Ext.Container({ layout: 'form', defaults:{xtype: 'textfield', width: 195}, width:290,
					items: 
						{
							id: 'localcode',
							name: 'localcode',
							allowBlank:false,
							maxLength: 50,
			           		<?php if ($_smarty_tpl->tpl_vars['id']->value > 0) {?>
			           			value: "<?php echo $_smarty_tpl->tpl_vars['localcode']->value;?>
",     	
			           			style: {textTransform: "uppercase", background: "#dee9f6"},
			                 	readOnly: true,
			              	<?php } else { ?>    
								style: {textTransform: "uppercase"},
					      	<?php }?>
							validateOnBlur: false,
							listeners: {'blur': {fn: function(obj){forceAlphaNumeric(obj)}}},
							fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCode');?>
",
							validator: function(pValue){
								return (pValue !== 'TAOPIX_RETRO_PRINTS'); 
							},
							post: true
						}
					}),
					<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
						<?php if (!$_smarty_tpl->tpl_vars['companyLogin']->value) {?>
						new Ext.Container({ layout: 'form', defaults:{xtype: 'textfield', width: 225}, width:350, items: companyCombo }),
						<?php }?>
					<?php }?>
					{ xtype: 'hidden', id: 'categorycode', name: 'categorycode', value: category,  post: true}
				]
			},
			
			{ /* tabpanel */
				xtype: 'tabpanel',
				id: 'maintabpanel',
				deferredRender: false,
				activeTab: 0,
				<?php if ($_smarty_tpl->tpl_vars['canshowmoreinfolink']->value) {?>
				height: 600,
				<?php } else { ?>
				height: 390,
				<?php }?>
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
						title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDescription');?>
",
						id: 'descriptionTab',
						labelWidth: 100,
						items: 
						[
							skuCode,
						 	componentNameContainer,
						 	infoContainer
              <?php if ($_smarty_tpl->tpl_vars['canshowmoreinfolink']->value) {?>,
							{
								xtype: 'box',
								style: {height: '25px'}
							},
							moreInfoLinkURL,
							moreInfoLinkTextContainer<?php }?>
						]
					},
					{
						title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSettings');?>
",
						id: 'settingsTab',
						items: 
						[
						 	unitCost,
						 	minPageCount,
						 	maxPageCount,
						 	weight,
						 	taxLevelCombo,
						 	isListDefault,
						 	orderFooterUsesProductQuantity,
						 	saveCheckBoxComponentComponentNotSelected,
						 	{ xtype: 'hidden', id: 'componentcode', name: 'componentcode', value: '<?php echo $_smarty_tpl->tpl_vars['componentcode']->value;?>
', post: true},
						 	{ xtype: 'hidden', id: 'isList', name: 'isList', value: isList, post: true}
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
							        html: '<img style="border: 1px solid;" id="previewimage" name="previewimage" src="<?php echo $_smarty_tpl->tpl_vars['componentpreview']->value;?>
">'
							    }
							}
						]
					},
					
					{
						title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleMetaData');?>
",
						id: 'matadataTab',
						layout: 'fit',
						listeners: { 'beforeshow': function(){ Ext.getCmp('maintabpanel').doLayout(); }},
						items: 
						[
							new Ext.grid.GridPanel({
						   		id: 'keywordsGroupsGrid',
						   		store: keywordGroupsDataStoreObj,
						    	selModel: keywordGroupsCheckBoxSelectionModelObj,
						    	cm: keywordGroupsColumnModelObj,
						    	stripeRows: true,
						    	stateful: true,
						    	enableColLock: false,
								draggable: false,
								enableColumnHide: false,
								enableColumnMove: false,
								trackMouseOver: false,
								columnLines:true,
						    	stateId: 'keywordsGroupsGrid',
						    	ctCls: 'grid',
						    	bodyStyle: 'border: 1px solid #b4b8c8'
						  })
						]
					}
					
					
				]
			}
		]
	});
	
	var gComponentsAddDialogObj = new Ext.Window({
		id: 'componentAddDialog',
	  	closable:false,
	  	plain:true,
	  	modal:true,
	  	draggable:true,
	  	resizable:false,
	  	layout: 'fit',
	  	autoHeight: true,
	  	width: 680,
	  	items: dialogFormPanelObj,
	  	listeners: {
			'close': {   
				fn: function(){
					componentAddEditWindowExists = false;
				}
			}
		},
	  	cls: 'left-right-buttons', 	
	  	title: "<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
".replace("^0", category),
	  	buttons: 
		[
			new Ext.form.Checkbox({
				id: 'iscomponentActive',
				name: 'iscomponentActive',
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
				handler: function(){ Ext.getCmp('componentAddDialog').close();},
				cls: 'x-btn-right' 		
			},
			{
				id: 'componentAddEditButton',
				cls: 'x-btn-right', 	
				<?php if ($_smarty_tpl->tpl_vars['id']->value == 0) {?>
					handler: componentAddSaveHandler,
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
"
				<?php } else { ?>
					handler: componentEditSaveHandler,
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdate');?>
"
				<?php }?>
			}
		]
	});
	
	
	gComponentsAddDialogObj.show();
	
	Ext.getCmp('keywordsGroupsGrid').store.on({
                    'load': function(){
                    setTimeout(function(){
                    var recs = Ext.getCmp('keywordsGroupsGrid').store.findExact('groupId', '<?php echo $_smarty_tpl->tpl_vars['keywordsGroupId']->value;?>
'); 
					keywordGroupsCheckBoxSelectionModelObj.selectRow(recs);
                    }, 1);
					

                    }
                });
	
	if (requiresPageCount != 1)
	{
		minPageCount.hide();
		maxPageCount.hide();
	}
	
	if (isList == 1)
	{
		isListDefault.hide();
		saveCheckBoxComponentComponentNotSelected.hide();
	}
}
<?php }
}
