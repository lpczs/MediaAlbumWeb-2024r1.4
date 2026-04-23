{literal}
function initialize(pParams)
{			
	{/literal}{$localizedcodesjavascript}
	{$localizednamesjavascript}
	{$languagecodesjavascript}
	{$languagenamesjavascript}
	{$sitegrouplocalizedcodesjavascript}
	{$sitegrouplocalizednamesjavascript}
	{$moreinfolinktextcodes}
	{$moreinfolinktextnames}{literal}
	
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
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ExtJsErrorNoMoreInfoLinkText#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
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
		Ext.getDom('previewimage').src = "{/literal}{$componentpreview}{literal}";
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
		var submitURL = 'index.php?fsaction=AdminComponents.add&ref={/literal}{$ref}{literal}&id=0';
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
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ExtJsErrorNoName#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
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
		    	Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorMinPageCountError#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
		    	submit = false;
		    }
			    
		    var maxPageCount = 0;
		    if (Ext.getCmp('maxpagecount').getValue() > 0)
		    {
		    	maxPageCount = Ext.getCmp('maxpagecount').getValue();
		    }
		    
		    if (minPageCount > maxPageCount)
		    {
		    	Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorMaxPageCountError#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
		    	submit = false;
		    }
		}		
		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", componentSaveCallback);
		}
	}

	function componentEditSaveHandler(btn, ev)
	{
		var id = Ext.taopix.gridSelection2IDList(Ext.getCmp('componentgrid'));
	
		var submitURL = 'index.php?fsaction=AdminComponents.edit&ref={/literal}{$ref}{literal}&id='+id;
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
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ExtJsErrorNoName#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
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
		    	Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorMinPageCountError#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
		    	submit = false;
		    }
			    
		    var maxPageCount = 0;
		    if (Ext.getCmp('maxpagecount').getValue() > 0)
		    {
		    	maxPageCount = Ext.getCmp('maxpagecount').getValue();
		    }
		    
		    if (minPageCount > maxPageCount)
		    {
		    	Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorMaxPageCountError#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
		    	submit = false;
		    }
		}
	
		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", componentSaveCallback);
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
				Ext.getDom('previewimage').src = './?fsaction=AdminComponents.getPreviewImage&no=1&id=' + Ext.getCmp('componentcode').value + '&ref={/literal}{$ref}{literal}&tmp=1&version=' + d.getTime();
				if (action.result.msg != '')
				{
					Ext.MessageBox.show({ title: "{/literal}{#str_TitleWarning#}{literal}", msg: action.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				}
				gLogoUpdate = 1;
				gLogoRemove = 0;
				Ext.getCmp('resetLogoButton').enable();
			},
			failure: function(form, action) 
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: 'Failed', buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
			},
			waitMsg: {/literal}"{#str_AlertUploading#}"{literal}
		});
	}
	
	function uploadLogo(btn, ev)
	{
		var fileName = Ext.getDom('preview').value.toLowerCase();
		if (!validateFileExtension(fileName)) 
		{
			if (fileName == '')
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleWarning#}{literal}", msg: "{/literal}{#str_MessageLogoSelectImage#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				return;
			}
			else
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleWarning#}{literal}", msg: "{/literal}{#str_MessageLogoFileTypes#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
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
			url: './?fsaction=AdminComponents.uploadPreviewImage&ref={/literal}{$ref}{literal}',
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
						html: "{/literal}{$previewImageText}{literal}"
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
					text: "{/literal}{#str_ButtonCancel#}{literal}",
					id: 'cancelUpload',
					handler: function(){ Ext.getCmp('uploaddialog').close(); }
				},
				{
					text: "{/literal}{#str_ButtonUpdate#}{literal}",
					handler: uploadLogo
				}
			]
		});	
		
		var gUploadObj = new Ext.Window({
			id: 'uploaddialog',
			title: "{/literal}{#str_TitleSelectPreviewImage#}{literal}",
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

	var str_LabelLanguageName    = "{/literal}{#str_LabelLanguageName#}{literal}";
	var str_localizedNameLabel   = "{/literal}{#str_LabelName#}{literal}";
		
	var deleteImg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png';
	var addimg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/add.png';
	
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
			defaultText: {langBlank: "{/literal}{#str_LabelSelectLanguage#}{literal}",  textBlank: "{/literal}{#str_ExtJsTypeValue#}{literal}", defaultValue: "{/literal}{$defaultlanguagecode}{literal}"},
			columnWidth: {langCol: 190, textCol: 227, delCol: 35},
			fieldWidth:  {langField: 185, textField: 205},
			errorMsg:    {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"}
		}
	});
  
  	var componentNameContainer = 
  	{
		xtype: 'panel',
        width: 480,
      	bodyBorder: false,
        border:false,
        fieldLabel: "{/literal}{#str_LabelName#}{literal}",
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
			defaultText: {langBlank: "{/literal}{#str_LabelSelectLanguage#}{literal}",  textBlank: "{/literal}{#str_ExtJsTypeValue#}{literal}", defaultValue: "{/literal}{$defaultlanguagecode}{literal}"},
			columnWidth: {langCol: 190, textCol: 227, delCol: 35},
			fieldWidth:  {langField: 185, textField: 205},
			errorMsg:    {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"}
		}
	});
	
	var infoContainer = 
	{
    	xtype: 'panel',
        width: 480,
      	bodyBorder: false,
        border:false,
        fieldLabel: "{/literal}{#str_LabelInfo#}{literal}",
        items: infoPanel
	};

	{/literal}{if $canshowmoreinfolink}{literal}
  	var moreInfoLinkURL = new Ext.form.TextField({
		id: 'componentmoreinfolinkurl',
		name: 'componentmoreinfolinkurl',
		width: 480,
		validateOnBlur: true,
        validator: function(v){ return validateUrl(this); },
		fieldLabel: "{/literal}{#str_LabelMoreInfoLinkURL#}{literal}",
		{/literal}{if $moreinfolinkurl === ''}{literal}
			value: "http://",
  		{/literal}{else}{literal}
			value: "{/literal}{$moreinfolinkurl}{literal}",
		{/literal}{/if}{literal}
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
			defaultText: {langBlank: "{/literal}{#str_LabelSelectLanguage#}{literal}",  textBlank: "{/literal}{#str_ExtJsTypeValue#}{literal}", defaultValue: "{/literal}{$defaultlanguagecode}{literal}"},
			columnWidth: {langCol: 190, textCol: 227, delCol: 35},
			fieldWidth: {langField: 185, textField: 205},
			errorMsg: {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"}
		}
	});

	var moreInfoLinkTextContainer = 
	{
		xtype: 'panel',
		width: 480,
		bodyBorder: false,
		border: false,
		fieldLabel: "{/literal}{#str_LabelMoreInfoLinkText#}{literal}",
		items: moreInfoLinkTextPanel
	};
	{/literal}{/if}{literal}
	
	Ext.layout.FormLayout.prototype.trackLabels = true;
			
	var companyCombo = new Ext.taopix.CompanyCombo({
		id: 'company',
		name: 'company',
		width:300,
		fieldLabel: "{/literal}{#str_LabelCompany#}{literal}",
		hideLabel:false,
		allowBlank:false,
		disabled: '{/literal}{$controldisabled}{literal}',
		{/literal}{if $companycode == ""}{literal}
			defvalue: 'GLOBAL',
		{/literal}{else}{literal}
			defvalue: '{/literal}{$companycode}{literal}',
		{/literal}{/if}{literal}
		options: {
			ref: '{/literal}{$ref}{literal}', 
			storeId: 'companyStore', 
			includeGlobal: '{/literal}{$includeglobal}{literal}', 
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
		value: "{/literal}{$sku}{literal}",
		listeners: {'blur': {fn: function(obj){forceAlphaNumeric(obj)}}},
		fieldLabel: "{/literal}{#str_LabelSKUCode#}{literal}",
		post: true
	});
	
	var minPageCount = new Ext.form.NumberField({
		id: 'minpagecount',
		name: 'minpagecount',
		maxLength: 10,
		width: 50,
		validationEvent:false,
		validateOnBlur: false,
		value: "{/literal}{$minPageCount}{literal}",
		fieldLabel: "{/literal}{#str_LabelMinPageCount#}{literal}",
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
		value: "{/literal}{$maxPageCount}{literal}",
		fieldLabel: "{/literal}{#str_LabelMaxPageCount#}{literal}",
		post: true,
		validator: function(v){ return validate(v,false,false);  }
	});
	
	var isListDefault = new Ext.form.Checkbox({
		id: 'isListDefault',
		name: 'isListDefault',
		boxLabel: "{/literal}{#str_LabelDefault#}{literal}",
		post: true,
		{/literal}{if $checkedByDefault == 1}{literal}
		checked: true
		{/literal}{else}{literal}
    	checked: false
    	{/literal}{/if}{literal}
	});
	
	var orderFooterUsesProductQuantity = new Ext.form.Checkbox({
		id: 'orderfooterusesprodqty',
		name: 'orderfooterusesprodqty',
		boxLabel: "{/literal}{#str_LabelOrderFooterUsesProdQuantity#}{literal}",
		post: true,
		{/literal}{if $orderfooterusesprodqty == 1}{literal}
		checked: true
		{/literal}{else}{literal}
    	checked: false
    	{/literal}{/if}{literal}
	});
	
	var saveCheckBoxComponentComponentNotSelected = new Ext.form.Checkbox({
		id: 'storewhennotselected',
		name: 'storewhennotselected',
		boxLabel: "{/literal}{#str_LabelStoreComponentWhenNotSelected#}{literal}",
		post: true,
		{/literal}{if $storewhennotselected == 1}{literal}
		checked: true
		{/literal}{else}{literal}
    	checked: false
    	{/literal}{/if}{literal}
	});
		
	var unitCost = new Ext.form.TextField({
		id: 'unitcost',
		name: 'unitcost',
		allowBlank:false,
		maxLength: 10,
		width: 70,
		validator: function(v){ return validate(v,true,false);  },
		value: "{/literal}{$unitCost}{literal}",
		fieldLabel: "{/literal}{#str_LabelCost#}{literal}",
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
		value: "{/literal}{$weight}{literal}",
		fieldLabel: "{/literal}{#str_LabelShippingWeight#}{literal}",
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
		fieldLabel: "{/literal}{#str_LabelOrderFooterTaxLevel#}{literal}",
		store: new Ext.data.ArrayStore({
			id: 'taxlevelstore',
			fields: ['id', 'name'],
			data: [
				{/literal}
				{section name=index loop=$taxlevellist}
				{if $smarty.section.index.last}
					["{$taxlevellist[index].id}", "{$taxlevellist[index].name}"]
				{else}
					["{$taxlevellist[index].id}", "{$taxlevellist[index].name}"],
				{/if}
				{/section}
				{literal}
			]
		}),
		valueField: 'id',
		displayField: 'name',
		value: '{/literal}{$taxlevel}{literal}',
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
	    	{ id: 'keywordsCol', header: "{/literal}{#str_SectionTitleMetaDataKeyWordGroups#}{literal}", dataIndex: 'keywordCode', width:440, menuDisabled:  true},
	    	{ id: 'keywordDefaultsCol', header: "{/literal}{#str_LabelDefaultValues#}{literal}", dataIndex: 'keywordDefaults', width:150, menuDisabled:  true}
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
			           		{/literal}{if $id > 0}{literal}
			           			value: "{/literal}{$localcode}{literal}",     	
			           			style: {textTransform: "uppercase", background: "#dee9f6"},
			                 	readOnly: true,
			              	{/literal}{else}{literal}    
								style: {textTransform: "uppercase"},
					      	{/literal}{/if}{literal}
							validateOnBlur: false,
							listeners: {'blur': {fn: function(obj){forceAlphaNumeric(obj)}}},
							fieldLabel: "{/literal}{#str_LabelCode#}{literal}",
							validator: function(pValue){
								return (pValue !== 'TAOPIX_RETRO_PRINTS'); 
							},
							post: true
						}
					}),
					{/literal}{if $optionms}{literal}
						{/literal}{if !$companyLogin}{literal}
						new Ext.Container({ layout: 'form', defaults:{xtype: 'textfield', width: 225}, width:350, items: companyCombo }),
						{/literal}{/if}{literal}
					{/literal}{/if}{literal}
					{ xtype: 'hidden', id: 'categorycode', name: 'categorycode', value: category,  post: true}
				]
			},
			
			{ /* tabpanel */
				xtype: 'tabpanel',
				id: 'maintabpanel',
				deferredRender: false,
				activeTab: 0,
				{/literal}{if $canshowmoreinfolink}{literal}
				height: 600,
				{/literal}{else}{literal}
				height: 390,
				{/literal}{/if}{literal}
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
						title: "{/literal}{#str_LabelDescription#}{literal}",
						id: 'descriptionTab',
						labelWidth: 100,
						items: 
						[
							skuCode,
						 	componentNameContainer,
						 	infoContainer
              {/literal}{if $canshowmoreinfolink}{literal},
							{
								xtype: 'box',
								style: {height: '25px'}
							},
							moreInfoLinkURL,
							moreInfoLinkTextContainer{/literal}{/if}{literal}
						]
					},
					{
						title: "{/literal}{#str_LabelSettings#}{literal}",
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
						 	{ xtype: 'hidden', id: 'componentcode', name: 'componentcode', value: '{/literal}{$componentcode}{literal}', post: true},
						 	{ xtype: 'hidden', id: 'isList', name: 'isList', value: isList, post: true}
						]
					},
					{
						title: "{/literal}{#str_ButtonPreview#}{literal}",
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
										text: "{/literal}{#str_ButtonUpdatePreviewImage#}{literal}",
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
										text: "{/literal}{#str_ButtonRemovePreviewImage#}{literal}",
										handler: onRemoveLogo
									},
									{
										xtype: 'spacer',
										width: 5
									},
									{
										text: "{/literal}{#str_ButtonResetPreviewImage#}{literal}",
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
							        html: '<img style="border: 1px solid;" id="previewimage" name="previewimage" src="{/literal}{$componentpreview}{literal}">'
							    }
							}
						]
					},
					
					{
						title: "{/literal}{#str_SectionTitleMetaData#}{literal}",
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
	  	title: "{/literal}{$title}{literal}".replace("^0", category),
	  	buttons: 
		[
			new Ext.form.Checkbox({
				id: 'iscomponentActive',
				name: 'iscomponentActive',
				boxLabel: "{/literal}{#str_LabelActive#}{literal}",
				post: true,
				cls: 'x-btn-left', 
      			ctCls: 'width_100',
      			{/literal}{if $isActive == 1}{literal}
					checked: true
				{/literal}{else}{literal}
					checked: false
				{/literal}{/if}{literal}
			}),
			{
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(){ Ext.getCmp('componentAddDialog').close();},
				cls: 'x-btn-right' 		
			},
			{
				id: 'componentAddEditButton',
				cls: 'x-btn-right', 	
				{/literal}{if $id == 0}{literal}
					handler: componentAddSaveHandler,
					text: "{/literal}{#str_ButtonAdd#}{literal}"
				{/literal}{else}{literal}
					handler: componentEditSaveHandler,
					text: "{/literal}{#str_ButtonUpdate#}{literal}"
				{/literal}{/if}{literal}
			}
		]
	});
	
	
	gComponentsAddDialogObj.show();
	
	Ext.getCmp('keywordsGroupsGrid').store.on({
                    'load': function(){
                    setTimeout(function(){
                    var recs = Ext.getCmp('keywordsGroupsGrid').store.findExact('groupId', '{/literal}{$keywordsGroupId}{literal}'); 
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
{/literal}