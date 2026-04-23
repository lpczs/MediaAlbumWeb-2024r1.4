function initialize(pParams)
{
	|:$localizedcodesjavascript:|
	|:$localizednamesjavascript:|
	|:$languagecodesjavascript:|
	|:$languagenamesjavascript:|
	|:$sitegrouplocalizedcodesjavascript:|
	|:$sitegrouplocalizednamesjavascript:|

	var str_LabelLanguageName    = "|:#str_LabelLanguageName#:|";
	var str_localizedNameLabel   = "|:#str_LabelName#:|";
	var deleteImg = '|:$webroot:|/utils/ext/images/silk/delete.png';
	var addimg = '|:$webroot:|/utils/ext/images/silk/add.png';
	var d = new Date();
	var gDate = d.getTime();
	var langListStore = [];
	var dataList = [  ];

	function createUploadDialog()
	{
		var uploadFormPanelObj = new Ext.FormPanel({
			id: 'uploadform',
			frame:true,
			autoWidth: true,
			autoHeight:true,
			layout: 'column',
			bodyBorder: false,
			border: false,
			url: './?fsaction=AdminShippingMethods.uploadLogo&ref=|:$ref:|',
			method: 'POST',
			fileUpload: true,
			items: [
				{
					xtype: 'hidden',
					id: 'clear',
					value: 0
				},
				{
					xtype: 'box',
			   	 	autoEl: {
						tag: 'div',
						html: "|:#str_LabelSelectLogo#:|"
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
			buttons:[
				{
					text: "|:#str_ButtonCancel#:|",
					id: 'cancelUpload',
					handler: closeUpload
				},
				{
					text: "|:#str_ButtonUpdate#:|",
					handler: uploadLogo
				}
			]
		});

		/* create modal window for logo upload */
		gUploadObj = new Ext.Window({
			id: 'uploaddialog',
			title: "|:#str_TitleSelectLogo#:|",
			closable:false,
			plain:true,
			modal:true,
			draggable:true,
			resizable:false,
			bodyBorder: false,
			layout: 'fit',
			autoHeight:true,
			width: 490,
			items: [uploadFormPanelObj]
		});

		gUploadObj.show();
	}

	function clearUpload(btn, ev)
	{
		Ext.getDom('preview').value = '';
	}

	function closeUpload(btn, ev)
	{
		gUploadObj.close();
	}

	function uploadLogo(btn, ev)
	{
		var fileName = Ext.getDom('preview').value.toLowerCase();
		if (!validateFileExtension(fileName))
		{
			if (fileName == '')
			{
				Ext.MessageBox.show({ title: "|:#str_TitleWarning#:|", msg: "|:#str_MessageLogoSelectImage#:|", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				return;
			}
			else
			{
				Ext.MessageBox.show({ title: "|:#str_TitleWarning#:|", msg: "|:#str_MessageLogoFileTypes#:|", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				return;
			}
		}
		onUploadLogo();
	}

	function onUploadLogo()
	{
		var theForm = Ext.getCmp('uploadform').getForm();
		theForm.submit({
			params: {code: Ext.getCmp('code').value, csrf_token: Ext.taopix.getCSRFToken()},
			scope: this,
			success: function(form,action)
			{
				gUploadObj.close();
				var d = new Date();
				Ext.getDom('previewimage').src = './?fsaction=AdminShippingMethods.getPreviewImage&no=1&id=' + Ext.getCmp('code').value + '&ref=|:$ref:|&tmp=1&version=' + d.getTime();
				if (action.result.msg != '')
				{
					Ext.MessageBox.show({ title: "|:#str_TitleWarning#:|", msg: action.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				}
				gLogoUpdate = 1;
				gLogoRemove = 0;
				Ext.getCmp('resetLogoButton').enable();
			},
			failure: function(form, action)
			{
				Ext.MessageBox.show({ title: "|:#str_TitleError#:|", msg: "|:#str_ErrorLogoUpload#:|", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
			},
			waitMsg: "|:#str_AlertUploading#:|"
		});
	}

	function validateFileExtension(fileName)
	{
		var exp = /^.*\.(jpg|jpeg|png|gif)$/;
		return exp.test(fileName);
	}

	/* save functions */
	function addsaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminShippingMethods.add&ref=|:$ref:|';
		var fp = Ext.getCmp('mainform'), form = fp.getForm();
		var submit = true;
		var paramArray = new Object();
		paramArray['ordervalueincludesdiscount'] = '';
		paramArray['requiresdelivery'] = '';
		paramArray['isdefault'] = '';
		paramArray['logoupdate'] = gLogoUpdate;
		paramArray['logoremove'] = gLogoRemove;

		if (Ext.getCmp('ordervalueincludesdiscount').checked)
		{
			paramArray['ordervalueincludesdiscount'] = '1';
		}
		else
		{
			paramArray['ordervalueincludesdiscount'] = '0';
		}

		if (Ext.getCmp('usedefaultbillingaddress').checked)
		{
			paramArray['usedefaultbillingaddress'] = '1';
		}
		else
		{
			paramArray['usedefaultbillingaddress'] = '0';
		}

		if (Ext.getCmp('usedefaultshippingaddress').checked)
		{
			paramArray['usedefaultshippingaddress'] = '1';
		}
		else
		{
			paramArray['usedefaultshippingaddress'] = '0';
		}

		if (Ext.getCmp('requiresdelivery').checked)
		{
			paramArray['requiresdelivery'] = '1';
		}
		else
		{
			paramArray['requiresdelivery'] = '0';
		}

		if (Ext.getCmp('isdefault').checked)
		{
			paramArray['isdefault'] = '1';
		}
		else
		{
			paramArray['isdefault'] = '0';
		}

		|:if $optioncfs:|
			if (Ext.getCmp('collectfromstore').checked)
			{
				paramArray['collectfromstore'] = '1';
			}
			else
			{
				paramArray['collectfromstore'] = '0';
			}

			if (Ext.getCmp('showstorelistonopen').checked)
			{
				paramArray['showstorelistonopen'] = '1';
			}
			else
			{
				paramArray['showstorelistonopen'] = '0';
			}

			if (Ext.getCmp('allowgroupingbycountry').checked)
			{
				paramArray['allowgroupingbycountry'] = '1';
			}
			else
			{
				paramArray['allowgroupingbycountry'] = '0';
			}

			if (Ext.getCmp('allowgroupingbyregion').checked)
			{
				paramArray['allowgroupingbyregion'] = '1';
			}
			else
			{
				paramArray['allowgroupingbyregion'] = '0';
			}

			if (Ext.getCmp('allowgroupingbystoregroup').checked)
			{
				paramArray['allowgroupingbystoregroup'] = '1';

				if (!Ext.getCmp('storegrouplabelpanel').isValid())
				{
					Ext.MessageBox.show({ title: "|:#str_TitleError#:|", msg: "|:#str_ErrorNoStoreGroupLabel#:|", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
					submit = false;
					return false;
				}
			}
			else
			{
				paramArray['allowgroupingbystoregroup'] = '0';
			}
		|:/if:|

		if (!Ext.getCmp('langPanel').isValid())
		{
			Ext.MessageBox.show({ title: "|:#str_TitleError#:|", msg: "|:#str_ExtJsErrorNoName#:|", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
			submit = false;
		}

		var orderMinValue = "0.00";
    	var orderMaxValue = "0.00";
    	var orderMinValueNumber = 0.00;
    	var orderMaxValueNumber = 0.00;
    	var orderValueRange = Ext.getCmp('ordervaluerange').getValue();
		if(orderValueRange != '')
		{
	    	var orderMinValue = Ext.getCmp('orderminvalue').getValue();
			var orderMaxValue = Ext.getCmp('ordermaxvalue').getValue();

			 if (orderMinValue != "")
			 {
			     orderMinValueNumber = parseFloat(orderMinValue);
			 }

			 if (orderMaxValue != "")
			 {
			     orderMaxValueNumber = parseFloat(orderMaxValue);
			 }

			 if (orderMaxValueNumber <= 0.00)
			 {
				 Ext.getCmp('ordermaxvalue').markInvalid("|:#str_ErrorOrderValueRangeError1#:|");
				 submit = false;
			 }

			 if (orderMaxValueNumber <= orderMinValueNumber)
			 {
			     Ext.getCmp('ordermaxvalue').markInvalid("|:#str_ErrorOrderValueRangeError2#:|");
			     submit = false;
			 }
		}
		if(submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "|:#str_MessageSaving#:|", saveCallback);
		}
	}

	/* save functions */
	function editsaveHandler(btn, ev)
	{
		var selectID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
		var submitURL = 'index.php?fsaction=AdminShippingMethods.edit&ref=|:$ref:|&id=' + selectID;
		var fp = Ext.getCmp('mainform'), form = fp.getForm();
		var submit = true;
		var paramArray = new Object();
		paramArray['ordervalueincludesdiscount'] = '';
		paramArray['requiresdelivery'] = '';
		paramArray['isdefault'] = '';
		paramArray['logoupdate'] = gLogoUpdate;
		paramArray['logoremove'] = gLogoRemove;

		if (Ext.getCmp('ordervalueincludesdiscount').checked)
		{
			paramArray['ordervalueincludesdiscount'] = '1';
		}
		else
		{
			paramArray['ordervalueincludesdiscount'] = '0';
		}

		if (Ext.getCmp('usedefaultbillingaddress').checked)
		{
			paramArray['usedefaultbillingaddress'] = '1';
		}
		else
		{
			paramArray['usedefaultbillingaddress'] = '0';
		}

		if (Ext.getCmp('usedefaultshippingaddress').checked)
		{
			paramArray['usedefaultshippingaddress'] = '1';
		}
		else
		{
			paramArray['usedefaultshippingaddress'] = '0';
		}

		if (Ext.getCmp('canmodifycontactdetails').checked)
		{
			paramArray['canmodifycontactdetails'] = '1';
		}
		else
		{
			paramArray['canmodifycontactdetails'] = '0';
		}

		if (Ext.getCmp('requiresdelivery').checked)
		{
			paramArray['requiresdelivery'] = '1';
		}
		else
		{
			paramArray['requiresdelivery'] = '0';
		}

		if (Ext.getCmp('isdefault').checked)
		{
			paramArray['isdefault'] = '1';
		}
		else
		{
			paramArray['isdefault'] = '0';
		}

		|:if $optioncfs:|
			if (Ext.getCmp('collectfromstore').checked)
			{
				paramArray['collectfromstore'] = '1';
			}
			else
			{
				paramArray['collectfromstore'] = '0';
			}

			if (Ext.getCmp('showstorelistonopen').checked)
			{
				paramArray['showstorelistonopen'] = '1';
			}
			else
			{
				paramArray['showstorelistonopen'] = '0';
			}

			if (Ext.getCmp('allowgroupingbycountry').checked)
			{
				paramArray['allowgroupingbycountry'] = '1';
			}
			else
			{
				paramArray['allowgroupingbycountry'] = '0';
			}

			if (Ext.getCmp('allowgroupingbyregion').checked)
			{
				paramArray['allowgroupingbyregion'] = '1';
			}
			else
			{
				paramArray['allowgroupingbyregion'] = '0';
			}

			if (Ext.getCmp('allowgroupingbystoregroup').checked)
			{
				paramArray['allowgroupingbystoregroup'] = '1';

				if (!Ext.getCmp('storegrouplabelpanel').isValid())
				{
					Ext.MessageBox.show({ title: "|:#str_TitleError#:|", msg: "|:#str_ErrorNoStoreGroupLabel#:|", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
					submit = false;
					return false;
				}
			}
			else
			{
				paramArray['allowgroupingbystoregroup'] = '0';
			}
		|:/if:|

		if (!Ext.getCmp('langPanel').isValid())
		{
			Ext.MessageBox.show({ title: "|:#str_TitleError#:|", msg: "|:#str_ExtJsErrorNoName#:|", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
			submit = false;
		}

		var orderMinValue = "0.00";
   	 	var orderMaxValue = "0.00";
   	 	var orderMinValueNumber = 0.00;
    	var orderMaxValueNumber = 0.00;
    	var orderValueRange = Ext.getCmp('ordervaluerange').getValue();
		if(orderValueRange != '')
		{
	    	var orderMinValue = Ext.getCmp('orderminvalue').getValue();
			var orderMaxValue = Ext.getCmp('ordermaxvalue').getValue();

			 if (orderMinValue != "")
			 {
			     orderMinValueNumber = parseFloat(orderMinValue);
			 }

			 if (orderMaxValue != "")
			 {
			     orderMaxValueNumber = parseFloat(orderMaxValue);
			 }

			 if (orderMaxValueNumber <= 0.00)
			 {
				 Ext.getCmp('ordermaxvalue').markInvalid("|:#str_ErrorOrderValueRangeError1#:|");
				 submit = false;
		 	}

		 	if (orderMaxValueNumber <= orderMinValueNumber)
			{
		   		Ext.getCmp('ordermaxvalue').markInvalid("|:#str_ErrorOrderValueRangeError2#:|");
		     	submit = false;
		 	}
		}

		if(submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "|:#str_MessageSaving#:|", saveCallback);
		}
	}

	function saveCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var gridObj = gMainWindowObj.findById('maingrid');
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

	function refreshAddressSettings()
	{
		var useDefaultShippingAddressControl = Ext.getCmp('usedefaultshippingaddress');
		var canModifyContactDetailsControl = Ext.getCmp('canmodifycontactdetails');

		if (useDefaultShippingAddressControl.checked)
		{
			canModifyContactDetailsControl.enable();
		}
		else
		{
			canModifyContactDetailsControl.setValue(false);
			canModifyContactDetailsControl.disable();
		}
	}

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

    var langPanel = new Ext.taopix.LangPanel({
		id: 'langPanel',
		name: 'name',
		height:153,
		width: 550,
		post: true,
		style: 'border:1px solid #b4b8c8',
		data: {langList: langListStore, dataList: dataList},
		settings:
		{
			headers:     {langLabel: str_LabelLanguageName,  textLabel: str_localizedNameLabel, deletePic: deleteImg, addPic: addimg},
			defaultText: {langBlank: "|:#str_LabelSelectLanguage#:|",  textBlank: "|:#str_ExtJsTypeValue#:|", defaultValue: "|:$defaultlanguagecode:|"},
			columnWidth: {langCol: 200, textCol: 287, delCol: 35},
			fieldWidth:  {langField: 185, textField: 272},
			errorMsg:    {blankValue: "|:#str_ExtJsTextFieldBlank#:|"}
		}
	});

	var storeGroupLabelList = [];
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
    		storeGroupLabelList.push([gAllLanguageCodesArray[i],gAllLanguageNamesArray[i]]);
    	}
   };

   var storeGrouplangPanel = new Ext.taopix.LangPanel({
		id: 'storegrouplabelpanel',
		name:'storegrouplabel',
		height: 155,
		width: 490,
		post: true,
		style: 'border:1px solid #b4b8c8',
		data: {langList: storeGroupLabelList, dataList: dataList2},
		settings:
		{
			headers:     {langLabel: str_LabelLanguageName,  textLabel: str_localizedNameLabel, deletePic: deleteImg, addPic: addimg},
			defaultText: {langBlank: "|:#str_LabelSelectLanguage#:|",  textBlank: "|:#str_ExtJsTypeValue#:|", defaultValue: "|:$defaultlanguagecode:|"},
			columnWidth: {langCol: 200, textCol: 230, delCol: 35},
			fieldWidth:  {langField: 185, textField: 211},
			errorMsg:    {blankValue: "|:#str_ExtJsTextFieldBlank#:|"}
		}
	});

	var LangContainer = {
		xtype: 'panel',
	    width: 550,
	    fieldLabel: "|:#str_LabelName#:|",
	    items: [ langPanel ]
	};

	var storeGroupContainer = {
    	xtype: 'panel',
        width: 500,
		fieldLabel: "|:#str_LabelStoreGroupLabel#:|",
        items: [ storeGrouplangPanel ]
    };

	var requiresDelivery = new Ext.form.Checkbox({
		id: 'requiresdelivery',
		name: 'requiresdelivery',
		boxLabel: "|:#str_LabelRequiresDelivery#:|",
		|:if $requiresdeliverychecked == 1:|
			checked: true,
		|:else:|
			checked: false,
		|:/if:|
		|:if $collectfromstore == 1:|
			disabled: true,
		|:else:|
			disabled: false,
		|:/if:|
		post: true

	});

	var useDefaultBillingAddress = new Ext.form.Checkbox({
		id: 'usedefaultbillingaddress',
		name: 'usedefaultbillingaddress',
		boxLabel: "|:#str_LabelUseDefaultBillingAddress#:|",
		|:if $usedefaultbillingaddress == 1:|
			checked: true,
		|:else:|
			checked: false,
		|:/if:|
		post: true

	});

	var useDefaultShippingAddress = new Ext.form.Checkbox({
		id: 'usedefaultshippingaddress',
		name: 'usedefaultshippingaddress',
		boxLabel: "|:#str_LabelUseDefaultShippingAddress#:|",
		|:if $usedefaultshippingaddress == 1:|
			checked: true,
		|:else:|
			checked: false,
		|:/if:|
		post: true,
		listeners: { check: refreshAddressSettings }

	});

	var canModifyContactDetails = new Ext.form.Checkbox({
		id: 'canmodifycontactdetails',
		name: 'canmodifycontactdetails',
		boxLabel: "|:#str_LabelUseCanModifyContactDetails#:|",
		|:if $canmodifycontactdetails == 1:|
			checked: true,
		|:else:|
			checked: false,
		|:/if:|
		post: true,
		listeners: { check: refreshAddressSettings }

	});

	var collectfromStoreCheckBox =  new Ext.form.Checkbox({
		id: 'collectfromstore',
		name: 'collectfromstore',
		boxLabel: "|:#str_LabelCollectFromStore#:|",
		|:if $collectfromstore == 1:|
			checked: true,
		|:else:|
			checked: false,
		|:/if:|
		listeners: {
			'check': {
				fn: function(cb, checked){
					if (checked)
					{
						Ext.getCmp('requiresdelivery').setValue(false);
						Ext.getCmp('requiresdelivery').disable();
						Ext.getCmp('collectfromstoretab').enable();
						Ext.getCmp('storegrouping').enable();
						Ext.getCmp('usedefaultbillingaddress').disable();
						Ext.getCmp('usedefaultshippingaddress').disable();
						Ext.getCmp('usedefaultbillingaddress').setValue(false);
						Ext.getCmp('usedefaultshippingaddress').setValue(false);
					}
					else
					{
						Ext.getCmp('requiresdelivery').setValue(true);
						Ext.getCmp('requiresdelivery').enable();
						Ext.getCmp('collectfromstoretab').disable();
						Ext.getCmp('storegrouping').disable();
						Ext.getCmp('usedefaultbillingaddress').enable();
						Ext.getCmp('usedefaultshippingaddress').enable();
						Ext.getCmp('usedefaultbillingaddress').setValue(true);
						Ext.getCmp('usedefaultshippingaddress').setValue(true);
					}
				}
			}
		},
		post: true
	});

	var isDefault = new Ext.form.Checkbox({
		id: 'isdefault',
		name: 'isdefault',
		boxLabel: "|:#str_LabelDefault#:|",
		|:if $defaultchecked == 1:|
			checked: true,
		|:else:|
			checked: false,
		|:/if:|
		post: true
	});

	var showStoreListOnOpen = new Ext.form.Checkbox({
		id: 'showstorelistonopen',
		name: 'showstorelistonopen',
		boxLabel: "|:#str_LabelShowStoreListOnOpen#:|",
		hideLabel:true,
		|:if $showstorelistonopen == 1:|
			checked: true,
		|:else:|
			checked: false,
		|:/if:|
		post: true
	});

	var allowGroupingByCountry = new Ext.form.Checkbox({
		id: 'allowgroupingbycountry',
		name: 'allowgroupingbycountry',
		boxLabel: "|:#str_LabelAllowCountryGrouping#:|",
		hideLabel:true,
		|:if $allowgroupingbycountry == 1:|
			checked: true,
		|:else:|
			checked: false,
		|:/if:|
		listeners: {
			'check': {
				fn: function(cb, checked){
					if (checked)
					{
						Ext.getCmp('allowgroupingbyregion').enable();
						Ext.getCmp('allowgroupingbyregion').setValue(true);
					}
					else
					{
						Ext.getCmp('allowgroupingbyregion').disable();
						Ext.getCmp('allowgroupingbyregion').setValue(false);
					}
				}
			}
		},
		post: true
	});

	var allowGroupingByRegion = new Ext.form.Checkbox({
		id: 'allowgroupingbyregion',
		name: 'allowgroupingbyregion',
		boxLabel: "|:#str_LabelAllowRegionGrouping#:|",
		hideLabel:true,
		|:if $allowgroupingbyregion == 1:|
			checked: true,
		|:else:|
			checked: false,
		|:/if:|
		post: true
	});

	var allowGroupingByStoreGroup = new Ext.form.Checkbox({
		id: 'allowgroupingbystoregroup',
		name: 'allowgroupingbystoregroup',
		boxLabel: "|:#str_LabelAllowStoreGroupGrouping#:|",
		hideLabel:true,
		|:if $allowgroupingbystoregroup == 1:|
			checked: true,
		|:else:|
			checked: false,
		|:/if:|
		listeners: {
			'check': {
				fn: function(cb, checked){
					if (checked)
					{
						Ext.getCmp('storegrouplabelpanel').enable();
					}
					else
					{
						Ext.getCmp('storegrouplabelpanel').disable();
					}
				}
			}
		},
		post: true
	});

	var orderValueRange = new Ext.form.ComboBox({
		id: 'ordervaluerange',
		name: 'ordervaluerange',
		mode: 'local',
		editable: false,
		forceSelection: true,
		selectOnFocus: true,
		listeners:{
			select:{
				fn: rangeConfig
			}
		},
		triggerAction: 'all',
		fieldLabel: "|:#str_LabelOrderValueRange#:|",
		store: new Ext.data.ArrayStore({
			id: 0,
			fields: ['id', 'name'],
			data: [
					['', "|:#str_LabelNone#:|"],
					['WITHOUTTAX', "|:#str_OrderValueRangeWITHOUTTAX#:|"],
					['WITHTAX', "|:#str_OrderValueRangeWITHTAX#:|"]

				]
		}),
		valueField: 'id',
		displayField: 'name',
		value: '|:$ordervaluerange:|',
		useID: true,
		post: true
	});

	var orderValueIncludeDiscount = new Ext.form.Checkbox({
		id: 'ordervalueincludesdiscount',
		name: 'ordervalueincludesdiscount',
		boxLabel: "|:#str_LabelIncludeDiscount#:|",
		hideLabel: true,
		|:if $ordervalueincludesdiscountchecked == 1:|
			checked: true,
		|:else:|
			checked: false,
		|:/if:|
		post: true
	});

	var dialogFormPanelObj = new Ext.FormPanel({
		id: 'mainform',
		header: false,
		frame:true,
		autoWidth: true,
		autoHeight:true,
		layout: 'form',
		defaultType: 'textfield',
		bodyBorder: false,
		border: false,
		defaults: {labelWidth:70},
		items: [
			{
				xtype: 'panel',
		    	id: 'topPanel',
				layout: 'form',
				style:'background: #c9d8ed; padding: 3px 0; border:1px solid #8ca9cf',
				plain:true,
				bodyBorder: false,
				border: false,
				defaults:{xtype: 'textfield', width: 280, labelWidth: 75},
				bodyStyle:'padding:5px 5px 0; border-top: 0px',
				items: [
					{
						fieldLabel: "|:#str_LabelCode#:|",
			   			id: 'code',
						name: 'code',
						allowBlank: false,
						maxLength: 20,
						|:if $isEdit == 1:|
							value: '|:$shippingmethodcode:|',
							style: {textTransform: "uppercase", background: "#c9d8ed"},
							readOnly: true,
						|:else:|
				 			style: {textTransform: "uppercase"},
						|:/if:|
				 		listeners:{	blur:{ fn: forceAlphaNumeric } },
				 		post: true
				 	},
				 	{ xtype: 'hidden', id: 'assetid', name: 'assetid', value: '|:$assetID:|{literal}',  post: true}
				]
			},

			{
				xtype: 'tabpanel',
				id: 'maintabpanel',
				deferredRender: false,
				activeTab: 0,
				width: 690,
				height: 350,
				plain:true,
				bodyBorder: false,
				border: false,
				style: 'margin-top: 7px',
				bodyStyle:'border-right: 1px solid #96bde7; border-left: 1px solid #96bde7;border-bottom: 1px solid #96bde7;',
				defaults: {frame: false, defaultType: 'textfield', autoScroll: true, hideMode:'offsets', layout: 'form', style:'padding: 10px 10px 0 10px; background-color: #eaf0f8;'},
				items: [
					{
						title: "|:#str_LabelSettings#:|",
						id: 'settingsTab',
						items: [
							{
								xtype: 'panel', width: 630, layout: 'form',
								items:
								[
									LangContainer,
									useDefaultBillingAddress,
									useDefaultShippingAddress,
									canModifyContactDetails,
									requiresDelivery,
	    							isDefault
	    							|:if $optioncfs:|
										,collectfromStoreCheckBox
									|:/if:|
								]
							}
						]
					},
					{
						title: "|:#str_LabelOptions#:|",
						id: 'optionsTab',
						listeners: { 'beforeshow': function(){ Ext.getCmp('maintabpanel').doLayout(); }},
						defaults: { xtype: 'textfield', width: 200, labelWidth: 150},
						items:
						[
							{
								xtype: 'panel', width: 580, layout: 'form', defaults: { xtype: 'textfield', width: 200, labelWidth: 120},
								items:
								[
									orderValueRange,
	    							{
										fieldLabel: "|:#str_LabelMinimumValue#:|",
										id: 'orderminvalue',
										name: 'orderminvalue',
										value:'|:$orderminvalue:|',
										width: 100,
										validator: function(v){ return validate(v,true,false);  },
										post: true
									},
									{
										fieldLabel: "|:#str_LabelMaximumValue#:|",
										id: 'ordermaxvalue',
										name: 'ordermaxvalue',
										value:'|:$ordermaxvalue:|',
										width: 100,
										validator: function(v){ return validate(v,true,false);  },
										post: true
									},
									orderValueIncludeDiscount
								]
							}
						]
					}

					|:if $optioncfs:|
					,
					{
						title: "|:#str_LabelCollectFromStore#:|",
						id: 'collectfromstoretab',
						listeners: { 'beforeshow': function(){ Ext.getCmp('maintabpanel').doLayout(); }},
						labelWidth:110,
						fieldWidth:  {textField: 10},
						items:
						[
							{
								xtype: 'panel',
								layout: 'form',
								width: 602,
								items:
								[
									showStoreListOnOpen,
									{
								        xtype: 'buttongroup',
								        frame: false,
								        columns: 5,
								        items: [
											{
												text: "|:#str_ButtonUpdateLogo#:|",
												handler: function() {

													createUploadDialog();
													Ext.getDom('preview').value = '';
												}
											},
											{
												  xtype: 'spacer',
												  width: 5
											},
									        {
												text: "|:#str_ButtonRemoveLogo#:|",
												handler: onRemoveLogo
									        },
											{
												  xtype: 'spacer',
												  width: 5
											},
									        {
												text: "|:#str_ButtonResetLogo#:|",
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
							        	autoEl: {
											tag: 'div',
							        		html: '<img style="border: 1px solid; max-height: 150px; max-width: 600px;" id="previewimage" name="previewimage" src="./?fsaction=AdminShippingMethods.getPreviewImage&id=|:$assetID:|&ref=|:$ref:|&no=1&tmp=0&version=' + gDate + '">'
							        	}
									}

								]
							}
						]
					}
					,
					{
						title: "|:#str_LabelStoreGrouping#:|",
						id: 'storegrouping',
						listeners: { 'beforeshow': function(){ Ext.getCmp('maintabpanel').doLayout(); }},
						labelWidth:140,
						items:
						[
							{
								xtype: 'panel', layout: 'form', width: 645,
								items:
								[
								 	allowGroupingByCountry,
								 	allowGroupingByRegion,
								 	allowGroupingByStoreGroup,
									storeGroupContainer
								]
							}
						]
					}
					|:/if:|
				]
			}
		]
	});

	gDialogObj = new Ext.Window({
		id: 'dialog',
		closable:false,
		plain:true,
		title: "|:$title:|",
		modal:true,
		draggable:true,
		resizable:false,
		bodyBorder: false,
		layout: 'fit',
		height: 475,
		width: 720,
		items: dialogFormPanelObj,
		listeners: {
			'close': {
				fn: function(){
					shippingMethodsEditWindowExists = false;
				}
			}
		},
		buttons:
		[
			{
				text: "|:#str_ButtonCancel#:|",
				handler: function(){ gDialogObj.close();}
			},
			{
				id: 'addEditButton',
				|:if $isEdit == 0:|
					handler: addsaveHandler,
					text: "|:#str_ButtonAdd#:|"
				|:else:|
					handler: editsaveHandler,
					text: "|:#str_ButtonUpdate#:|"
				|:/if:|
			}
		]
	});

	if ('|:$isEdit:|' == 0)
	{
		|:if $optioncfs:|
			Ext.getCmp('collectfromstoretab').disable();
			Ext.getCmp('storegrouping').disable();
			Ext.getCmp('storegrouplabelpanel').disable();
		|:/if:|
		rangeConfig('', '', 0);
	}
	else if('|:$isEdit:|' == 1 && '|:$ordervaluerange:|' == '')
	{
		rangeConfig('', '', 0);
	}

	|:if $optioncfs:|
		if (Ext.getCmp('allowgroupingbycountry').checked)
		{
			Ext.getCmp('allowgroupingbyregion').enable();
		}
		else
		{
			Ext.getCmp('allowgroupingbyregion').disable();
		}
	|:/if:|

	|:if $isEdit == 1:|
		|:if $optioncfs:|
			|:if $collectfromstore == 1:|
				Ext.getCmp('storegrouping').enable();
				Ext.getCmp('collectfromstoretab').enable();
			|:else:|
				Ext.getCmp('collectfromstoretab').disable();
				Ext.getCmp('storegrouping').disable();
			|:/if:|
			if (Ext.getCmp('allowgroupingbystoregroup').checked)
			{
				Ext.getCmp('storegrouplabelpanel').enable();
			}
			else
			{
				Ext.getCmp('storegrouplabelpanel').disable();
			}
		|:/if:|
	|:/if:|

	refreshAddressSettings();
	gDialogObj.show();
}

function rangeConfig(comboBox, record, index)
{
	if (index == 0)
	{
		 Ext.getCmp('orderminvalue').disable();
		 Ext.getCmp('ordermaxvalue').disable();
		 Ext.getCmp('ordervalueincludesdiscount').disable();
		 Ext.getCmp('orderminvalue').setValue(0);
		 Ext.getCmp('ordermaxvalue').setValue(0);
		 Ext.getCmp('ordervalueincludesdiscount').setValue(0);
	}
	else
	{
		Ext.getCmp('orderminvalue').enable();
		Ext.getCmp('ordermaxvalue').enable();
		Ext.getCmp('ordervalueincludesdiscount').enable();
	}
}

function forceAlphaNumeric()
{
	var code = Ext.getCmp('code').getValue();

    code = code.toUpperCase();
    code = code.replace(/[^A-Z_0-9\-]+/g, "");

    Ext.getCmp('code').setValue(code);
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
	Ext.getDom('previewimage').src = './?fsaction=AdminShippingMethods.getPreviewImage&no=1&id=|:$assetID:|&ref=|:$ref:|&tmp=0&version=' + d.getTime();
	Ext.getCmp('resetLogoButton').disable();
}
