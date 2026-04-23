{$languagecodesjavascript}
{$languagenamesjavascript}
{$localizedcodesjavascript}
{$localizednamesjavascript}

{literal}
var emailSections =
{
	'AA':
	{
		'required': 1,
		'name': "{/literal}{#str_LabelAdministrator#}{literal}",
		'active': "{/literal}{$smtpadminactive}{literal}",
		'gActive': "{/literal}{$gsmtpadminactive}{literal}",
		'names': "{/literal}{$smtpadminname}{literal}",
		'gNames': "{/literal}{$gsmtpadminname}{literal}",
		'emails': "{/literal}{$smtpadminaddress}{literal}",
		'gEmails': "{/literal}{$gsmtpadminaddress}{literal}"
	},
	'PA':
	{
		'required': 0,
		'name': "{/literal}{#str_LabelProduction#}{literal}",
		'active': "{/literal}{$smtpprodactive}{literal}",
		'gActive': "{/literal}{$gsmtpprodactive}{literal}",
		'names': "{/literal}{$smtpprodname}{literal}",
		'gNames': "{/literal}{$gsmtpprodname}{literal}",
		'emails': "{/literal}{$smtpprodaddress}{literal}",
		'gEmails': "{/literal}{$gsmtpprodaddress}{literal}"
	},
	'CA':
	{
		'required': 0,
		'name': "{/literal}{#str_LabelConfirmation#}{literal}",
		'active': "{/literal}{$smtporderconfactive}{literal}",
		'gActive': "{/literal}{$gsmtporderconfactive}{literal}",
		'names': "{/literal}{$smtporderconfname}{literal}",
		'gNames': "{/literal}{$gsmtporderconfname}{literal}",
		'emails': "{/literal}{$smtporderconfaddress}{literal}",
		'gEmails': "{/literal}{$gsmtporderconfaddress}{literal}"
	},
	'SA':
	{
		'required': 0,
		'name': "{/literal}{#str_LabelSaveOrder#}{literal}",
		'active': "{/literal}{$smtpsaveorderactive}{literal}",
		'gActive': "{/literal}{$gsmtpsaveorderactive}{literal}",
		'names': "{/literal}{$smtpsaveordername}{literal}",
		'gNames': "{/literal}{$gsmtpsaveordername}{literal}",
		'emails': "{/literal}{$smtpsaveorderaddress}{literal}",
		'gEmails': "{/literal}{$gsmtpsaveorderaddress}{literal}"
	},
	'SH':
	{
		'required': 0,
		'name': "{/literal}{#str_SectionTitleShipping#}{literal}",
		'active': "{/literal}{$smtpshippingactive}{literal}",
		'gActive': "{/literal}{$gsmtpshippingactive}{literal}",
		'names': "{/literal}{$smtpshippingname}{literal}",
		'gNames': "{/literal}{$gsmtpshippingname}{literal}",
		'emails': "{/literal}{$smtpshippingaddress}{literal}",
		'gEmails': "{/literal}{$gsmtpshippingaddress}{literal}"
	},
	'NA':
	{
		'required': 0,
		'name': "{/literal}{#str_LabelNewAccount#}{literal}",
		'active': "{/literal}{$smtpnewaccountactive}{literal}",
		'gActive': "{/literal}{$gsmtpnewaccountactive}{literal}",
		'names': "{/literal}{$smtpnewaccountname}{literal}",
		'gNames': "{/literal}{$gsmtpnewaccountname}{literal}",
		'emails': "{/literal}{$smtpnewaccountaddress}{literal}",
		'gEmails': "{/literal}{$gsmtpnewaccountaddress}{literal}"
	},
	'RP':
	{
		'required': 0,
		'name': "{/literal}{#str_LabelResetPassword#}{literal}",
		'active': "{/literal}{$smtpresetpasswordactive}{literal}",
		'gActive': "{/literal}{$gsmtpresetpasswordactive}{literal}",
		'names': "{/literal}{$smtpresetpasswordname}{literal}",
		'gNames': "{/literal}{$gsmtpresetpasswordname}{literal}",
		'emails': "{/literal}{$smtpresetpasswordaddress}{literal}",
		'gEmails': "{/literal}{$gsmtpresetpasswordaddress}{literal}"
	},
	'OU':
	{
		'required': 0,
		'name': "{/literal}{#str_LabelOrderUploaded#}{literal}",
		'active': "{/literal}{$smtporderuploadedactive}{literal}",
		'gActive': "{/literal}{$gsmtporderuploadedactive}{literal}",
		'names': "{/literal}{$smtporderuploadedname}{literal}",
		'gNames': "{/literal}{$gsmtporderuploadedname}{literal}",
		'emails': "{/literal}{$smtporderuploadedaddress}{literal}",
		'gEmails': "{/literal}{$gsmtporderuploadedaddress}{literal}"
	}
};

var emailInProcess = '';
{/literal}
var gBrandingCode = "{$code}";
var gSmtpAuthPass = "{$gsmtpauthpass}";
var initPassword = "{$smtpauthpass}";
var gDateFormat = "{$dateformat}";
var invalidDateFormatLabel_txt = "{#str_LabelInvalidDateFormat#}";

var onlinedesignersettings_tx = "{#str_TitleOnlineDesignerSettings#}";
var maxmegapixels_txt = "{#str_LabelMaxMegaPixels#}";

var imagescaling_txt = "{#str_LabelImageScaling#}";
var appkey_txt = "{#str_LabelAppKey#}";
var urls_txt = "{#str_LabelUrls#}";
var generalsettings_txt = "{#str_LabelGeneralSettings#}";

var imagescalingbefore_txt = "{#str_LabelImageScalingBefore#}";
var imagescalingafter_txt = "{#str_LabelImageScalingAfter#}";
var imagescalingbeforeenabled_txt = "{#str_LabelEnableImageScalingBefore#}";
var imagescalingafterenabled_txt = "{#str_LabelEnableImageScalingAfter#}";

var imagescalingBeforeVal = "{$imagescalingbefore}";
var imagescalingBeforeEnabledVal = ("{$imagescalingbeforeenabled}" == 'checked') ? true : false;
var imagescalingAfterVal = "{$imagescalingafter}";
var imagescalingAfterEnabledVal = ("{$imagescalingafterenabled}" == 'checked') ? true : false;

var shufflelayoutshowoption_txt = "{#str_LabelSuffleLayoutShowOption#}";
var shufflelayout_txt = "{#str_LabelShuffleLayout#}";
var shufflelayoutleftright_txt = "{#str_LabelLeftRightPages#}";
var shufflelayoutspread_txt = "{#str_LabelSpread#}";
var shufflelayoutpictures_txt = "{#str_LabelSufflePictures#}";

var insertDeleteButtons_txt = "{#str_LabelInsertDeleteButtons#}";
var showInsertDeleteButtons_txt = "{#str_LabelShowInsertDeleteButtons#}";

var totalPagesDropdown_txt = "{#str_LabelTotalPagesDropdown#}";
var enableTotalPagesDropdown_txt = "{#str_LabelEnableTotalPagesDropdown#}";

var str_LabelLanguageName    = "{#str_LabelLanguageName#}";
var str_localizedNameLabel   = "{#str_LabelName#}";

var deleteImg = '{$webroot}/utils/ext/images/silk/delete.png';
var addimg = '{$webroot}/utils/ext/images/silk/add.png';

var awaitingUserIDTrackingResponse = false;

var previousRedactionMode = {$redactionMode};
var initialRedactionMode = {$redactionMode};
var hasMassUnsubscribeTaskRunning = ("{$massunsubscribetaskforbrandrunning}" == '1') ? true : false;
var str_massUnsubscribeTaskInProgress = ("{$massunsubscribetaskforbrandrunning}" == '1') ? "{#str_LabelUnsubscribeTaskInProgress#}" : '';

var orderRedactionMode = {$orderredactionmode};
var desktopThumbnailDeletionEnabled = {$desktopthumbnaildeletionenabled}

var brandFileArray = new Object();
var brandTextArray = {$brandassetstrings};
var brandTextEnabledArray = {$brandassetstringsenabled};
var brandTextUseDefaultsArray = {$brandassetstringsusedefault};
var defaultBrandTextArray = {$defaultbrandstrings};

// Store tha initial state of the asset when the form is displayed, 0 is using default, any other value would mean that an image has been uploaded.
var lastSavedBrandAssetData = {$brandassetsdata};
// Use the values to track the state of each of the images.
var workingBrandAssetData = {$brandassetsdata};
var entropy = "{$entropy}";
var regenerateVisible = ("{$regenerateVisible}" == '1') ? false : true;

{literal}
var getLocalisedData = function(gLocalizedNamesArray, gLocalizedCodesArray)
{
	var langListStore = [], dataList = [];

	for (var i = 0; i < gAllLanguageCodesArray.length; i++)
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

		if (ArrayIndexOf(gLocalizedCodesArray, gAllLanguageCodesArray[i]) == -1)
		{
			langListStore.push([gAllLanguageCodesArray[i],gAllLanguageNamesArray[i]]);
		}
	}
	return {'langList': langListStore, 'dataList': dataList};
};

// Show a large version of a branding logo picture.
var showPreview = function(pOption)
{
	var popupTitle = '';

	// Set the title compare to the element clicked.
	switch(pOption)
	{
		case {/literal}{$onlineLogoType}{literal}:
		{
			popupTitle = "{/literal}{#str_LabelOnlineDesignerLogo#}{literal}";
			break;
		}
		case {/literal}{$onlineLogoTypeDark}{literal}:
		{
			popupTitle = "{/literal}{#str_LabelOnlineDesignerLogoDark#}{literal}";
			break;
		}
		case {/literal}{$controlLogoType}{literal}:
		{
			popupTitle = "{/literal}{#str_LabelCustomerAccountLogo#}{literal}";
			break;
		}
		case {/literal}{$marketingType}{literal}:
		{
			popupTitle = "{/literal}{#str_LabelCustomerAccountSidebar#}{literal}";
			break;
		}
		case {/literal}{$emailLogoType}{literal}:
		{
			popupTitle = "{/literal}{#str_LabelEmailLogo#}{literal}";
			break;
		}
	}

	var d = new Date();
	var date = d.getTime();
	var srcFile = Ext.getDom('previewimage-' + pOption).src;

	// Create the popup and its content.
	previewWindow = new Ext.Window({
		id: 'image-preview',
		closable:true,
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		title: popupTitle,
		width: 545,
		height: 565,
		html:'<div style="text-align: center; vertical-align: middle; display: table-cell; width: 500px; height: 500px; padding: 15px;"><img style="max-height: 500px; max-width: 500px;" id="fullpreviewimage-' + pOption + '" name="fullpreviewimage" src="' + srcFile + '"></div>'
	});
	previewWindow.show();
}

function getShuffleSettings(pShuffleLayout)
{
	return {
		pages: ((pShuffleLayout & 1) > 0) ? true : false,
		spread: ((pShuffleLayout & 2) > 0) ? true : false,
		pictures: ((pShuffleLayout & 4) > 0) ? true : false
	}
}

var onAddRecord = function(sectionCode)
{
	var store = Ext.getCmp('emailsGrid').getStore();
	var sectionRecords = store.query('sectionCode', sectionCode, false, true).items;
	var sectionRecordsCount = sectionRecords.length;

	/* if section is empty */
	if ((sectionRecordsCount == 1) && (sectionRecords[0].data.isEmptyRow == 1))
	{
		var posInStore = store.find('id', sectionRecords[0].data.id);
		Ext.getCmp('emailsGrid').startEditing(posInStore, 1);
	}
	else
	{
		/* if not empty then add a new empty record */
		var lastId = 0;
		if (store.getCount() > 0)
		{
  			lastId = store.getAt(0).get('id');
  			store.each(function(rec) { lastId = Math.max(lastId, rec.get('id')); });
		}
		lastId++;

		var defaultData =
		{
			id: lastId,
			sectionCode: sectionCode,
			sectionName: emailSections[sectionCode].name,
			valueName: '',
			valueEmail: '',
			controlsRow: '',
			isEmptyRow: 1
		};
		var r = new store.recordType(defaultData);
		r.commit();
		store.add(r);
		store.commitChanges();
		store.singleSort('sectionCode', 'ASC');
		var posInStore = store.find('id', lastId);
		Ext.getCmp('emailsGrid').startEditing(posInStore, 1);
	}
};

var onDeleteRecord = function(recordId)
{
	var store = Ext.getCmp('emailsGrid').getStore();
	var storeRec = store.query('id', recordId, false, true).items[0];
	var sectionCode = storeRec.data.sectionCode;
	var sectionRecords = store.query('sectionCode', sectionCode, false, true).items;
	var sectionRecordsCount = sectionRecords.length;

	if (sectionRecordsCount <= 1)
	{
		storeRec.data.valueName = '';
		storeRec.data.valueEmail = '';
		storeRec.data.isEmptyRow = 1;
	}
	else
	{
		var pos = store.findExact('id', storeRec.data.id, 0);
		store.removeAt(pos);
	}
	store.commitChanges();
	store.singleSort('sectionCode', 'ASC');
};


var activeCheckboxClicked = function()
{
	Ext.getCmp('emailsGrid').getStore().singleSort('sectionCode', 'ASC');
	return true;
};


var onEmailTestRecord = function(pSectionCode)
{
	var onEmailTestCallback = function(pUpdated, pTheForm, pActionData)
	{
		if ((pUpdated) && (pActionData))
		{
			if (pActionData.result.msg)
			{
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.ERROR	});
			}
			else
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_LabelConfirmation#}{literal}", msg: "{/literal}{#str_MessageEmailSent#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
		}
		emailInProcess = '';
		Ext.getCmp('emailsGrid').getStore().singleSort('sectionName', 'ASC');
	};


	var store = Ext.getCmp('emailsGrid').getStore();
	var records = store.query('sectionCode', pSectionCode, false, true);
	records = records.filterBy(function(o, k){ if (o.data.isEmptyRow == 0) return true; });

	var smtpAddress = Ext.getCmp('smtpaddress').getValue();
    var smtpAuth = Ext.getCmp('smtpauth').getValue();

  	var smtpPort = Ext.getCmp('smtpport').getValue();
    var smtpUsername = Ext.getCmp('smtpauthuser').getValue();
    var smtpPassword = Ext.getCmp('smtpauthpass').getValue();
    var smtpType = Ext.getCmp('smtptype').getValue();
    var smtpFromName = Ext.getCmp('smtpsysfromname').getValue();
    var smtpFromAddress = Ext.getCmp('smtpsysfromaddress').getValue();
    var smtpReplyName = Ext.getCmp('smtpreplyname').getValue();
    var smtpReplyAddress = Ext.getCmp('smtpreplyaddress').getValue();
	if (smtpPassword != initPassword)
	{
		smtpPassword = EncodeEmailPassword(smtpPassword);
	}
    var sectionName = [];
    var sectionAddress = [];

	records.each(function(item, index, length){
		if ((item.data.valueName == '') || (item.data.valueEmail == ''))
		{
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorEmailEmpty#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
			return false;
		}
		sectionName.push(item.data.valueName);
		sectionAddress.push(item.data.valueEmail);
		return true;
	});
	sectionName = sectionName.join(';');
	sectionAddress = sectionAddress.join(';');

	if ((sectionName == '') || (sectionAddress == ''))
	{
		Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorEmailEmpty#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
		return false;
	}

	var paramArray = {};
	paramArray['section'] = pSectionCode;
	paramArray['ssa'] = smtpAddress;
	paramArray['ssp'] = smtpPort;
	paramArray['saut'] = smtpAuth;
	paramArray['sau'] = smtpUsername;
	paramArray['sap'] = smtpPassword;
    paramArray['sst'] = smtpType;
	paramArray['sfn'] = smtpFromName;
	paramArray['sfa'] = smtpFromAddress;
	paramArray['srn'] = smtpReplyName;
	paramArray['sra'] = smtpReplyAddress;
	paramArray['sn'] = sectionName;
	paramArray['sa'] = sectionAddress;
	paramArray['bc'] = gBrandingCode;
	paramArray['applicationname'] = Ext.getCmp('applicationname').getValue();
	paramArray['displayurl'] = Ext.getCmp('displayurl').getValue();
	paramArray['cmd'] = 'EMAILTEST';
	paramArray['ref'] = "{/literal}{$ref}{literal}";
	paramArray['ssop'] = Ext.getCmp('oauthprovider').getValue();
	paramArray['sspt'] = Ext.getCmp('oauthrefreshtokenid').getValue();

	emailInProcess = pSectionCode;
	store.singleSort('sectionName', 'ASC');
	Ext.taopix.formPost('', paramArray, 'index.php?fsaction=AjaxAPI.callback', "{/literal}{#str_MessagePleaseWait#}{literal}", onEmailTestCallback);
};

var openOnlineDesignerToolTip = function()
{
	var localisedData = getLocalisedData(gLocalizedNamesArray, gLocalizedCodesArray);

	var onlineLogoLinkTooltipPanelPanel = new Ext.taopix.LangPanel(
	{
		id: 'logolinktooltip',
		name: 'logolinktooltip',
		height: 350,
		width: 950,
		data: localisedData,
		settings:
		{
			headers:     {langLabel: str_LabelLanguageName,  textLabel: str_localizedNameLabel, deletePic: deleteImg, addPic: addimg},
			defaultText: {langBlank: "{/literal}{#str_LabelSelectLanguage#}{literal}",  textBlank: "{/literal}{#str_ExtJsTypeValue#}{literal}"},
			columnWidth: {langCol: 290, textCol: 595, delCol: 35},
			fieldWidth:  {langField: 290, textField: 565},
			errorMsg:    {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"}
		}
	});

	var onlineLogoLinkTooltipPanelContainer =
	{
    	xtype: 'panel',
        width: 950,
      	bodyBorder: false,
        border:false,
        hideLabel: true,
        items: onlineLogoLinkTooltipPanelPanel
	};

	var onlineLogoLinkTooltipPanel = new Ext.FormPanel(
	{
		id: 'addModelForm',
        labelAlign: 'left',
        labelWidth: 120,
        autoHeight: true,
        frame: true,
        layout: 'form',
        bodyStyle: 'padding-left: 5px;',
		plain: true,
        items:
        [
			onlineLogoLinkTooltipPanelContainer
		],
		baseParams:
		{
			ref: "{/literal}{$ref}{literal}"
		}
    });

	var onlineDesignerLogoLinkTooltipDialog = new Ext.Window(
	{
		id: 'onlinelogolinktooltipdialog',
		closable: false,
		plain: true,
		modal: true,
		draggable: true,
		resizable: false,
		title: "{/literal}{#str_TitleLinkToolTip#}{literal}",
		items:
		[
			onlineLogoLinkTooltipPanel
		],
		buttons:
		[
			{
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(btn, ev)
				{
					Ext.getCmp('onlinelogolinktooltipdialog').close();
				},
				cls: 'x-btn-right'
			},
			{
				id: 'onlinedesignerlogolinktooltipupdatebutton',
				handler: function()
				{
					// copy lang panel data to hidden field
					Ext.getCmp('onlinedesignerlogolinktooltip').setValue(Ext.getCmp('logolinktooltip').convertTableToString());
					updateLangForBrand();

					Ext.getCmp('onlinelogolinktooltipdialog').close();
				},
				text: "{/literal}{#str_ButtonUpdate#}{literal}",
				cls: 'x-btn-right'
			}
		],
		width: 980
	});
	onlineDesignerLogoLinkTooltipDialog.show();
};

function downloadKey(regenerate)
{
	var parameter = [];

	{/literal}
	var id = "{$brandingid}";
	var code = "{$code}";
	var name = "{$name}";
	var UIURL = "{$onlineuiurl}";
	var APIURL = "{$onlineapiurl}";
	var designerURL = "{$onlinedesignerurl}";
	var iv = "{$entropy}";
	{literal}

	if (regenerate == true) {
		var generate = 1;
	} else {
		var generate = 0;
	}

	var URL = "";
	if ("" != UIURL) {
		URL = UIURL;
	} else {
		URL = designerURL;
	}

	if ("" != iv) {
		ivParam = '&IV='+escape(iv);
	} else {
		ivParam = '';
	}

	location.replace('index.php?fsaction=AdminBranding.downloadKey&id='+escape(id)+'&code='+escape(code)+'&name='+escape(name)+'&URL='+escape(URL)+'&generate='+escape(generate)+'&APIURL='+escape(APIURL)+ivParam);
}

function generateKey()
{
	downloadKey(true);
}

var updateLangForBrand = function()
{
	var data = Ext.getCmp('logolinktooltip').getStore().data.items;
	var dataLength = Ext.getCmp('logolinktooltip').getStore().data.items.length;
	var langCodes = [];
	var langNames = [];

	for (var i = 0; i < dataLength; i++)
	{
		langCodes.push(data[i].data.langCode);
		langNames.push(data[i].data.textValue);
	}

	gLocalizedNamesArray = langNames;
	gLocalizedCodesArray = langCodes;
};

function initialize(pParams)
{
	{/literal}
	var gBrandingID = {$brandingid};
	var gBrandingCode = "{$code}";
	var gPaymentMethodCount = {$paymentmethodcount};
	var gPaymentMethods = "{$defaultpaymentmethods}";
	var gCurrentIntegration = "{$currentintegration}";
	var gSmtpAddress = "{$gsmtpaddress}";
	var gSmtpAuth = "{$gsmtpauth}";
	var gSmtpPort = "{$gsmtpport}";
	var gSmtpAuthUser = "{$gsmtpauthuser}";
    var gSmtpType = "{$gsmtptype}";
	var gSmtpSysFromName = "{$gsmtpsysfromname}";
	var gSmtpSysFromAddress = "{$gsmtpsysfromaddress}";
	var gSmtpReplyName = "{$gsmtpreplyname}";
	var gSmtpReplyAddress = "{$gsmtpreplyaddress}";
	var gSmtpAdminName = "{$gsmtpadminname}";
	var gSmtpAdminAddress = "{$gsmtpadminaddress}";
	var gSmtpProdName = "{$gsmtpprodname}";
	var gSmtpProdAddress = "{$gsmtpprodaddress}";
	var gSmtpOrderConfName = "{$gsmtporderconfname}";
	var gSmtpOrderConfAddress = "{$gsmtporderconfaddress}";
	var gSmtpSaveOrderName = "{$gsmtpsaveordername}";
	var gSmtpSaveOrderAddress = "{$gsmtpsaveorderaddress}";
	var gSmtpAuthPass = "{$smtpauthpass}";
	var gDeleteLabel = "{#str_ButtonDelete#}";
	var productionSitesCompanies = {$productionSitesCompanies};
	var productionSitesSelected = "{$productionSitesSelected}";
	var gSmtpShippingName = "{$gsmtpshippingname}";
	var gSmtpShippingAddress = "{$gsmtpshippingaddress}";
	var gSmtpNewAccountName = "{$gsmtpnewaccountname}";
	var gSmtpNewAccountAddress = "{$gsmtpnewaccountaddress}";
	var gSmtpResetPasswordName = "{$gsmtpresetpasswordname}";
	var gSmtpResetPasswordAddress = "{$gsmtpresetpasswordaddress}";
	var gSmtpOrderUploadedName = "{$gsmtporderuploadedname}";
	var gSmtpOrderUploadedAddress = "{$gsmtporderuploadedaddress}";
	var customAccountPagesURL = "{$accountpagesurl}";
	{literal}

	onUnsubscribeAllCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
		}
	};

	// Update the Preview image / message based on the current status of the brand asset.
	function updatePreviewDisplay(pOption)
	{
		var d = new Date();
		var gDate = d.getTime();
		var tmpFile = '';

		// Update the current display to show a 'Please upload an image' message.
		if (workingBrandAssetData[pOption] == 0)
		{
			// Update the preview box content and change the cursor.
			document.getElementById('prev-disp-'+pOption).style.cursor = 'default';
			document.getElementById('prev-disp-'+pOption).innerHTML = '<span style="max-height: 130px; max-width: 130px;" id="previewimage-' + pOption + '" name="uploadwarning" class="upload-Warning-text">{/literal}{#str_LabelPleaseUploadAnImage#}{literal}</span>'

			// Hide the buttons for resetting and removing the brand asset.
			if (lastSavedBrandAssetData[pOption] == 0)
			{
				// Only hide the reset to last saved if the last saved value was to use the default.
				Ext.getCmp('reset_' + pOption).hide();
			}
			Ext.getCmp('remove_' + pOption).hide();
		}
		else
		{
			// A brand image is in use, update the preview box to display the image.

			// Check to see if the brand preview is using a temp file ie. not saved yet.
			if (typeof brandFileArray[pOption] != "undefined")
			{
				if (typeof brandFileArray[pOption].path != "undefined")
				{
					tmpFile = brandFileArray[pOption].path;
				}
			}

			// Update the preview box content and change the cursor.
			document.getElementById('prev-disp-'+pOption).style.cursor = 'pointer';
			document.getElementById('prev-disp-'+pOption).innerHTML = '<img style="max-height: 130px; max-width: 130px;" id="previewimage-' + pOption + '" name="previewimage" src="./?fsaction=AdminBranding.getBrandFilePreview&ref={/literal}{$ref}{literal}&typeref=' + pOption + '&code=' + gBrandingCode + '&bid={/literal}{$brandingid}{literal}&tmp=' + tmpFile + '&version=' + gDate + '"><div class="preview-icon"></div>'

			// Display the options to remove the brand image, and reset to last saved.
			Ext.getCmp('reset_' + pOption).show();
			Ext.getCmp('remove_' + pOption).show();
		}

		if ((workingBrandAssetData[pOption] != lastSavedBrandAssetData[pOption]) && (0 != lastSavedBrandAssetData[pOption]))
		{
			// Enable the reset to last saved button, if a last saved image exists.
			Ext.getCmp('reset_' + pOption).enable();
		}
	}

	function editSaveHandler()
	{
		var parameter = [];

		function submitForm()
		{
			{/literal}{if $brandingid > 0 and $code == ''}{literal}
				parameter['code'] = '';
			{/literal}{else}{literal}
				parameter['code'] = Ext.getCmp('code').getValue();
			{/literal}{/if}{literal}

            if (Ext.getCmp('orderfrompreview') && (Ext.getCmp('orderfrompreview').checked))
			{
				parameter['orderfrompreview'] = '1';
			}
			else
			{
				parameter['orderfrompreview'] = '0';
			}

			parameter['sharehidebranding'] = Ext.getCmp('sharehidebranding').getValue();
			var previewDomainURL = Ext.getCmp('previewdomainurl').getValue();
			parameter['previewdomainurl'] = (previewDomainURL === 'http://' || previewDomainURL === 'https://') ? '' : previewDomainURL;

			// Do not permit submission if sharehidebranding is ON and a previewdomainurl has not been set
			if ((parseInt(parameter['sharehidebranding']) === 1) && (parameter['previewdomainurl'] === ''))
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorPreviewDomainURLNotSet#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			parameter['smtpadminactive'] = emailSections['AA'].active;
			parameter['smtpproductionactive'] = emailSections['PA'].active;
			parameter['smtporderconfirmationactive'] = emailSections['CA'].active;
			parameter['smtpsaveorderactive'] = emailSections['SA'].active;
			parameter['smtpshippingactive'] = emailSections['SH'].active;
			parameter['smtpnewaccountactive'] = emailSections['NA'].active;
			parameter['smtpresetpasswordactive'] = emailSections['RP'].active;
			parameter['smtporderuploadedactive'] = emailSections['OU'].active;

			/* check to see if the password has already been encrypted */
			var encrypt = Ext.getCmp('smtpauthpass').getValue();
			if (encrypt != gSmtpAuthPass)
			{
				var encPassword = EncodeEmailPassword(encrypt);
				parameter['_smtpauthpass'] = encPassword;
			}
			else
			{
				parameter['_smtpauthpass'] = Ext.getCmp('smtpauthpass').getValue();
			}

			if (Ext.getCmp('usedefaultpaymentmethods') && (Ext.getCmp('usedefaultpaymentmethods').checked))
			{
				parameter['usedefaultpaymentmethods'] = '1';
			}
			else
			{
				parameter['usedefaultpaymentmethods'] = '0';
			}

			if (Ext.getCmp('usedefaultemailsettings') && (Ext.getCmp('usedefaultemailsettings').checked))
			{
				parameter['usedefaultemailsettings'] = '1';
			}
			else
			{
				parameter['usedefaultemailsettings'] = '0';
			}

			parameter['smtpauth'] = Ext.getCmp('smtpauth').getValue();

			if (2 == parameter['smtpauth'] && (0 == Ext.getCmp('oauthprovider').getValue() || 0 == Ext.getCmp('oauthrefreshtokenid').getValue())) {
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorInvalidOAuthConfiguration#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			var selectedPaymentMethods = "";
			if (! (Ext.getCmp('usedefaultpaymentmethods') && Ext.getCmp('usedefaultpaymentmethods').checked))
			{
				for (var i = 0; i < gPaymentMethodCount; i++)
				{
					var theCheckBox = Ext.getCmp("paymentmethod" + i);
					if (theCheckBox.checked)
					{
						selectedPaymentMethods = selectedPaymentMethods + theCheckBox.getEl().dom.value + ",";
					}
				}
				selectedPaymentMethods = trim(selectedPaymentMethods, ",");
				if (selectedPaymentMethods == '')
				{
					Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoPaymentMethods#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
					return false;
				}
			}

			parameter['paymentmethods'] = selectedPaymentMethods;

            if (Ext.getCmp('allowvouchers').checked)
			{
				parameter['allowvouchers'] = '1';
			}
			else
			{
				parameter['allowvouchers'] = '0';
			}

            if (Ext.getCmp('allowgiftcards').checked)
			{
				parameter['allowgiftcards'] = '1';
			}
			else
			{
				parameter['allowgiftcards'] = '0';
			}

			if (Ext.getCmp('isactive').checked)
			{
				parameter['isactive'] = '1';
			}
			else
			{
				parameter['isactive'] = '0';
			}

			if (Ext.getCmp('previewExpire').checked)
			{
				parameter['previewExpire'] = '1';
			}
			else
			{
				parameter['previewExpire'] = '0';
			}

			/* get integration code as per radio button selection */
			if (Ext.getCmp("integrationCustom").checked)
			{
				/* get integration code as per drop down list */
				paymentIntegration = Ext.getCmp('integrationlist').getValue();
				{/literal}{if !$sslloaded}{literal}
					var gSllRequiredIntegrations = "{/literal}{$sllrequiredintegrations}{literal}";
					if (gSllRequiredIntegrations.indexOf(paymentIntegration) > -1)
					{
						var message = "{/literal}{#str_ErrorPaymentIntegrationRequiresSSL#}{literal}".replace("^0", paymentIntegration);
						Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: message, buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
						return false;
					}
				{/literal}{/if}{literal}
			}
			else if (Ext.getCmp("integrationNone").checked)
			{
				/* no integration  */
				paymentIntegration = "NONE";
			}
			else if (Ext.getCmp("integrationDefault").checked)
			{
				/* default integration  */
				paymentIntegration = "DEFAULT";
			}

			parameter['paymentintegration'] = paymentIntegration;
			{/literal}{if $optionms && ($owner == '')}{literal}
				productionSite  = Ext.getCmp('productionsitelist').getValue();
				parameter['productionsite'] = productionSite;
			{/literal}{/if}{literal}

			// save the updated personal data deletion settings
			var currentRedactionOption = Ext.getCmp('redactionModeSelect');
			var selectedRedactionOption = currentRedactionOption.getValue();
			var redactionMode = 0;

			if (selectedRedactionOption != null)
			{
				redactionMode = selectedRedactionOption.inputValue;
			}

			if (redactionMode == 3)
			{
				var currentRedactionOptionUser = Ext.getCmp('redactionModeSelectUser');
				var selectedRedactionOptionUser = currentRedactionOptionUser.getValue();
				redactionMode = selectedRedactionOptionUser.inputValue;
			}

			var automaticRedactionEnabled = (Ext.getCmp('automaticredaction').checked) ? 1 : 0;
			var automaticRedactionDays = Ext.getCmp('redactiondays').getValue();
			var redactionNotification = Ext.getCmp('redactionnotificationdays').getValue();

			{/literal}{if $hasonlinedesigner == 1}{literal}
				var useMultiLineWorkflow = (Ext.getCmp('usemultilinebasketworkflow').checked) ? 1 : 0;

				var imagescalingbefore = 0;
				var imagescalingbeforeenabled = 0;

				{/literal}
				{if $allowimagescalingbefore}
				{literal}
					imagescalingbefore = Ext.getCmp('imagescalingbefore').getValue();
					imagescalingbeforeenabled = (Ext.getCmp('imagescalingbeforeenabled').checked) ? 1 : 0;
				{/literal}
				{/if}
				{literal}

				parameter['imagescalingbefore'] = imagescalingbefore;
				parameter['imagescalingbeforeenabled'] = imagescalingbeforeenabled;

				var imagescalingafter = 0;
				var imagescalingafterenabled = 0;

				imagescalingafter = Ext.getCmp('imagescalingafter').getValue();
				imagescalingafterenabled = (Ext.getCmp('imagescalingafterenabled').checked) ? 1 : 0;

				parameter['imagescalingafterenabled'] = imagescalingafterenabled;
				parameter['imagescalingafter'] = imagescalingafter;

				parameter['usemultilinebasketworkflow'] = useMultiLineWorkflow;

				parameter['onlinedesignerlogolinkurl'] = (Ext.getCmp('onlinedesignerlogolinkurl').getValue() == 'http://') ? '' : Ext.getCmp('onlinedesignerlogolinkurl').getValue();
				parameter['onlinedesignerlogolinktooltip'] = Ext.getCmp('onlinedesignerlogolinktooltip').getValue();

                var toggleperfectlyclear = 0;
                var automaticallyapplyperfectlyclear = (Ext.getCmp('automaticallyapplyperfectlyclear').checked) ? 1 : 0;

                // only uppdate the values of the shuffle layout if it is enabled
                if (automaticallyapplyperfectlyclear == 1)
                {
                    toggleperfectlyclear = (Ext.getCmp('toggleperfectlyclear').checked) ? 1 : 0;
                }

                parameter['automaticallyapplyperfectlyclear'] = automaticallyapplyperfectlyclear;
                parameter['toggleperfectlyclear'] = toggleperfectlyclear;

                // Fontlist params
                parameter['fontlisttype'] = Ext.getCmp('fontlisttype').getValue().value;
                parameter['fontlist'] = Ext.getCmp('fontlist').getValue();
			{/literal}{/if}{literal}

			parameter['redactionmode'] = redactionMode;
			parameter['automaticredactionenabled'] = automaticRedactionEnabled;
			parameter['automaticredactiondays'] = automaticRedactionDays;
			parameter['redactionnotificationdays'] = redactionNotification;
			parameter['orderredactionmode'] = orderRedactionMode;
			parameter['desktopthumbnaildeletionenabled'] = desktopThumbnailDeletionEnabled;

			parameter['onlineappkeyentropyvalue'] = entropy;


			parameter['googleuseridtracking'] = (Ext.getCmp('useridtracking').getValue() ? 1 : 0);

			// Populate the branded text array.
			parameter['brandfiles'] = JSON.stringify(brandFileArray);

			// Populate the default text check box values.
			parameter['emailSignatureenablecheck'] = (Ext.getCmp('emailSignatureenablecheck').checked ? 1 : 0);
			parameter['emailSignatureusedefaultcheck'] = (Ext.getCmp('emailSignatureusedefaultcheck').checked ? 1 : 0);

			// Get the desktop account pages url option fields
			var customAccountPagesURLField = Ext.getCmp('customaccountpagesurl');
			var useDefaultAccountPagesURLValue = Ext.getCmp('accountpagesurltype').getValue().inputValue;

			parameter['usedefaultaccountpagesurl'] = useDefaultAccountPagesURLValue;

			if (useDefaultAccountPagesURLValue == 0)
			{
				if (customAccountPagesURLField.isValid() == true)
				{
					parameter['accountpagesurl'] = customAccountPagesURLField.getValue();
				}
				else
				{
					return false;
				}
			}
			else
			{
				// Send the previous custom account page url back up
				parameter['accountpagesurl'] = customAccountPagesURL;
			}

			var fp = Ext.getCmp('mainform'), form = fp.getForm();

			if (gBrandingID > 0)
			{
				parameter['id'] = gBrandingID;
				Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=AdminBranding.edit', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
			}
			else
			{
				Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=AdminBranding.add', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
			}
		}

		function onProductionCompanyChangeConfirm(btn)
		{
			if (btn == "yes")
			{
				submitForm();
			}
		}

		if (Ext.getCmp('mainform').getForm().isValid())
		{
			var displayurlValue = (Ext.getCmp('displayurl').getValue() == 'http://') ? '' : Ext.getCmp('displayurl').getValue();
            displayurlValue = (Ext.getCmp('displayurl').getValue() == 'https://') ? '' : Ext.getCmp('displayurl').getValue();
			var weburlValue = (Ext.getCmp('weburl').getValue() == 'http://') ? '' : Ext.getCmp('weburl').getValue();
            weburlValue = (Ext.getCmp('weburl').getValue() == 'https://') ? '' : Ext.getCmp('weburl').getValue();

			{/literal}{if $hasonlinedesigner == 1}{literal}
				var onlineDesignerURL = (Ext.getCmp('onlinedesignerurl').getValue() == 'http://') ? '' : Ext.getCmp('onlinedesignerurl').getValue();
				onlineDesignerURL = (Ext.getCmp('onlinedesignerurl').getValue() == 'https://') ? '' : Ext.getCmp('onlinedesignerurl').getValue();
				var onlineUiURL = (Ext.getCmp('onlineuiurl').getValue() == 'http://') ? '' : Ext.getCmp('onlineuiurl').getValue();
				onlineUiURL = (Ext.getCmp('onlineuiurl').getValue() == 'https://') ? '' : Ext.getCmp('onlineuiurl').getValue();
				var onlineAPIURL = (Ext.getCmp('onlineapiurl').getValue() == 'http://') ? '' : Ext.getCmp('onlineapiurl').getValue();
				onlineAPIURL = (Ext.getCmp('onlineapiurl').getValue() == 'https://') ? '' : Ext.getCmp('onlineapiurl').getValue();
				var onlineDesignerLogoutURL = (Ext.getCmp('onlinedesignerlogouturl').getValue() == 'http://') ? '' : Ext.getCmp('onlinedesignerlogouturl').getValue();
				onlineDesignerLogoutURL = (Ext.getCmp('onlinedesignerlogouturl').getValue() == 'https://') ? '' : Ext.getCmp('onlinedesignerlogouturl').getValue();
				var onlineDesignerCDNURL = (Ext.getCmp('onlinedesignercdnurl').getValue() == 'http://') ? '' : Ext.getCmp('onlinedesignercdnurl').getValue();
				onlineDesignerCDNURL = (Ext.getCmp('onlinedesignercdnurl').getValue() == 'https://') ? '' : Ext.getCmp('onlinedesignercdnurl').getValue();
				var onlineAboutURL = (Ext.getCmp('onlineabouturl').getValue() == 'https://' || Ext.getCmp('onlineabouturl').getValue() == 'http://') ? '' : Ext.getCmp('onlineabouturl').getValue();
				var onlineHelpURL = (Ext.getCmp('onlinehelpurl').getValue() == 'https://' || Ext.getCmp('onlinehelpurl').getValue() == 'http://') ? '' : Ext.getCmp('onlinehelpurl').getValue();
				var onlineTermsAndConditionsURL = (Ext.getCmp('onlinetermsandconditionsurl').getValue() == 'https://' || Ext.getCmp('onlinetermsandconditionsurl').getValue() == 'http://') ? '' : Ext.getCmp('onlinetermsandconditionsurl').getValue();
			{/literal}{/if}{literal}

			if ((displayurlValue == "") && (weburlValue != ""))
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorWebURLNoDisplayURL#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			/* check to see if we are editing a brand record. If we are then we need to check if we are editing the default brand */

			{/literal}{if $brandingid > 0}{literal}
			var brandingGrid = Ext.getCmp('brandingGrid');
			var records = brandingGrid.selModel.getSelections();

			if (records[0].data.code != '')
			{
				if ((weburlValue != "") && (weburlValue != 'http://' && displayurlValue != 'http://'))
				{
					if (displayurlValue == weburlValue)
					{
						Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorWebURLEqualsDisplayURL#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
						return false;
					}
				}
			}
			{/literal}{else}{literal}
				if ((weburlValue != "") && (weburlValue != 'http://' && displayurlValue != 'http://'))
				{
					if (displayurlValue == weburlValue)
					{
						Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorWebURLEqualsDisplayURL#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
						return false;
					}
				}
			{/literal}{/if}{literal}

			parameter['displayurl'] = displayurlValue;
			parameter['weburl'] = weburlValue;

			{/literal}{if $hasonlinedesigner == 1}{literal}
				parameter['onlinedesignerurl'] = onlineDesignerURL;
				parameter['onlineuiurl'] = onlineUiURL;
				parameter['onlineapiurl'] = onlineAPIURL;
				parameter['onlinedesignerlogouturl'] = onlineDesignerLogoutURL;
				parameter['onlinedesignercdnurl'] = onlineDesignerCDNURL;
				parameter['onlineabouturl'] = onlineAboutURL;
				parameter['onlinehelpurl'] = onlineHelpURL;
				parameter['onlinetermsandconditionsurl'] = onlineTermsAndConditionsURL;
			{/literal}{/if}{literal}

			function getGridValues(sectionCode, valueType)
			{
				var store = Ext.getCmp('emailsGrid').getStore();
				var sectionRecords = store.query('sectionCode', sectionCode, false, true).items;
				var valueArray = [];
				for (var i = 0; i < sectionRecords.length; i++)
				{
					if (sectionRecords[i].data[valueType] != '')
					{
						valueArray.push(sectionRecords[i].data[valueType]);
					}
				}
				return valueArray.join(';');
			}

			parameter['smtpadminname'] = getGridValues('AA', 'valueName');
			parameter['smtpadminaddress'] = getGridValues('AA', 'valueEmail');
			parameter['smtpprodname'] = getGridValues('PA', 'valueName');
			parameter['smtpprodaddress'] = getGridValues('PA', 'valueEmail');
			parameter['smtporderconfname'] = getGridValues('CA', 'valueName');
			parameter['smtporderconfaddress'] = getGridValues('CA', 'valueEmail');
			parameter['smtpsaveordername'] = getGridValues('SA', 'valueName');
			parameter['smtpsaveorderaddress'] = getGridValues('SA', 'valueEmail');
			parameter['smtpshippingname'] = getGridValues('SH', 'valueName');
			parameter['smtpshippingaddress'] = getGridValues('SH', 'valueEmail');
			parameter['smtpnewaccountname'] = getGridValues('NA', 'valueName');
			parameter['smtpnewaccountaddress'] = getGridValues('NA', 'valueEmail');
			parameter['smtpresetpasswordname'] = getGridValues('RP', 'valueName');
			parameter['smtpresetpasswordaddress'] = getGridValues('RP', 'valueEmail');
			parameter['smtporderuploadedname'] = getGridValues('OU', 'valueName');
			parameter['smtporderuploadedaddress'] = getGridValues('OU', 'valueEmail');

			/* check for branding grid to be valid*/
			if ((emailSections['AA'].required == 1 && parameter['smtpadminname'] == '') ||
			(parameter['smtpadminname'].split(';').length != parameter['smtpadminaddress'].split(';').length) ||
			(parameter['smtpadminname'].split(';')[0] == '' && parameter['smtpadminaddress'].split(';')[0] != '') ||
			(parameter['smtpadminname'].split(';')[0] != '' && parameter['smtpadminaddress'].split(';')[0] == ''))
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoSectionValue#}{literal}".replace("^0", emailSections['AA'].name), buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			if ((emailSections['PA'].required == 1 && parameter['smtpprodname'] == '') ||
			(parameter['smtpprodname'].split(';').length != parameter['smtpprodaddress'].split(';').length) ||
			(parameter['smtpprodname'].split(';')[0] == '' && parameter['smtpprodaddress'].split(';')[0] != '') ||
			(parameter['smtpprodname'].split(';')[0] != '' && parameter['smtpprodaddress'].split(';')[0] == ''))
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoSectionValue#}{literal}".replace("^0", emailSections['PA'].name), buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			if ((emailSections['CA'].required == 1 && parameter['smtporderconfname'] == '') ||
			(parameter['smtporderconfname'].split(';').length != parameter['smtporderconfaddress'].split(';').length) ||
			(parameter['smtporderconfname'].split(';')[0] == '' && parameter['smtporderconfaddress'].split(';')[0] != '') ||
			(parameter['smtporderconfname'].split(';')[0] != '' && parameter['smtporderconfaddress'].split(';')[0] == ''))
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoSectionValue#}{literal}".replace("^0", emailSections['CA'].name), buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			if ((emailSections['SA'].required == 1 && parameter['smtpsaveordername'] == '') ||
			(parameter['smtpsaveordername'].split(';').length != parameter['smtpsaveorderaddress'].split(';').length) ||
			(parameter['smtpsaveordername'].split(';')[0] == '' && parameter['smtpsaveorderaddress'].split(';')[0] != '') ||
			(parameter['smtpsaveordername'].split(';')[0] != '' && parameter['smtpsaveorderaddress'].split(';')[0] == ''))
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoSectionValue#}{literal}".replace("^0", emailSections['SA'].name), buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			if ((emailSections['SH'].required == 1 && parameter['smtpshippingname'] == '') ||
			(parameter['smtpshippingname'].split(';').length != parameter['smtpshippingaddress'].split(';').length) ||
			(parameter['smtpshippingname'].split(';')[0] == '' && parameter['smtpshippingaddress'].split(';')[0] != '') ||
			(parameter['smtpshippingname'].split(';')[0] != '' && parameter['smtpshippingaddress'].split(';')[0] == ''))
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoSectionValue#}{literal}".replace("^0", emailSections['SH'].name), buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			if ((emailSections['NA'].required == 1 && parameter['smtpnewaccountname'] == '') ||
			(parameter['smtpnewaccountname'].split(';').length != parameter['smtpnewaccountaddress'].split(';').length) ||
			(parameter['smtpnewaccountname'].split(';')[0] == '' && parameter['smtpnewaccountaddress'].split(';')[0] != '') ||
			(parameter['smtpnewaccountname'].split(';')[0] != '' && parameter['smtpnewaccountaddress'].split(';')[0] == ''))
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoSectionValue#}{literal}".replace("^0", emailSections['NA'].name), buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			if ((emailSections['RP'].required == 1 && parameter['smtpresetpasswordname'] == '') ||
			(parameter['smtpresetpasswordname'].split(';').length != parameter['smtpresetpasswordaddress'].split(';').length) ||
			(parameter['smtpresetpasswordname'].split(';')[0] == '' && parameter['smtpresetpasswordaddress'].split(';')[0] != '') ||
			(parameter['smtpresetpasswordname'].split(';')[0] != '' && parameter['smtpresetpasswordaddress'].split(';')[0] == ''))
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoSectionValue#}{literal}".replace("^0", emailSections['RP'].name), buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			if ((emailSections['OU'].required == 1 && parameter['smtporderuploadedname'] == '') ||
			(parameter['smtporderuploadedname'].split(';').length != parameter['smtporderuploadedaddress'].split(';').length) ||
			(parameter['smtporderuploadedname'].split(';')[0] == '' && parameter['smtporderuploadedaddress'].split(';')[0] != '') ||
			(parameter['smtporderuploadedname'].split(';')[0] != '' && parameter['smtporderuploadedaddress'].split(';')[0] == ''))
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoSectionValue#}{literal}".replace("^0", emailSections['OU'].name), buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			if ((Ext.getCmp('productionsitelist')) && (gBrandingID > 0))
			{
				var productionSite = Ext.getCmp('productionsitelist').getValue();

				var originalCompanyCode = productionSitesCompanies[productionSitesSelected];
				var newCompanyCode = productionSitesCompanies[productionSite];

				if (originalCompanyCode != newCompanyCode)
				{
					Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_MessageProductionSiteCompanyChanged#}{literal}", onProductionCompanyChangeConfirm);
					return;
				}
			}

			// If using a custom signature has been checked, make sure the signature is not empty.
			if ((Ext.getCmp('emailSignatureenablecheck').checked) && (Ext.getCmp('mainform').findById('emailSignaturelangpanel').getStore().getCount() === 0))
			{
				// Show an errro message.
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoEmailSignature#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });

				// Open the correct panel if not open already.
				Ext.getCmp('maintabpanel').activate('customiseTab');
				return false;
			}

			submitForm();
		}
	}

	var validateUrl = function(obj)
	{
		if ((obj.getValue() != 'http://') && (obj.getValue() != 'https://')&& (obj.getValue() != ''))
		{
			var domainValid = (Ext.form.VTypes.url(obj.getValue()));
			var regexp = /^((([hH][tT][tT][pP][sS]?|[fF][tT][pP])\:\/\/)?([\w\.\-]+(\:[\w\.\&%\$\-]+)*@)?((([^\s\(\)\<\>\\\"\.\[\]\,@;:]+)(\.[^\s\(\)\<\>\\\"\.\[\]\,@;:]+)*(\.[a-zA-Z]{2,4}))|((([01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}([01]?\d{1,2}|2[0-4]\d|25[0-5])))(\b\:(6553[0-5]|655[0-2]\d|65[0-4]\d{2}|6[0-4]\d{3}|[1-5]\d{4}|[1-9]\d{0,3}|0)\b)?((\/[^\/][\w\.\,\?\'\\\/\+&%\$#\=~_\-@]*)*[^\.\,\?\"\'\(\)\[\]!;<>{}\s\x7F-\xFF])?)$/;
			var ipValid = regexp.test(obj.getValue());
			if (domainValid || ipValid) return true; else return false;
		}
		obj.clearInvalid();
		return true;
	};

	function validateFolder(obj)
	{
		var folder = obj.getValue();

    	folder = folder.replace(/[^a-zA-Z_0-9\-]+/g, "");

   	 	obj.setValue(folder);
	}

	var validateSmtpAuth = function(obj)
	{
		if (1 == Ext.getCmp('smtpauth').getValue())
		{
			return (obj.getValue() != '');
		}
		obj.clearInvalid();
		return true;
	};

	var validateAnalytics = function isAnalytics(obj)
	{
    	var googleAnalyticsCode = obj.getValue();
		var testGA4 = false;
		var testUA = false;

    	if (googleAnalyticsCode == '')
    	{
    		return true;
    	}
    	else
    	{
			testGA4 = (/^g-(\d|[a-zA-Z]){4,12}$/i).test(googleAnalyticsCode.toString());
			testUA = (/^ua-\d{4,9}-\d{1,4}$/i).test(googleAnalyticsCode.toString());

			return ((testGA4||testUA));
    	}
	}

	var validateTagManager = function isAnalytics(obj)
	{
    	var gtmCode = obj.getValue();

    	if (gtmCode == '')
    	{
    		return true;
    	}
    	else
    	{
    		return (/^gtm-[a-zA-Z0-9]+$/i).test(gtmCode.toString());
    	}
	}


	var deleteColRenderer = function(pRecord)
	{
		var button = '';
		if ((emailInProcess != '') && (emailInProcess == pRecord.data.sectionCode))
		{
			var emailPic = "{/literal}{$webroot}{literal}/utils/ext/images/taopix/progress-icon.gif";
			button = '<div style="overflow: hidden;"><div style="float: right; margin-right: 3px" onClick="return false;" OnMouseOver="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\') + \' x-btn-over\';" OnMouseOut="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\');"><table cellspacing="0" class="x-btn  x-btn-icon"><tbody class="x-btn-small x-btn-icon-small-left"><tr><td class="x-btn-tl"><i>&nbsp;</i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-ml"><i>&nbsp;</i></td><td class="x-btn-mc"><em unselectable="on" class=""><button type="button" class="x-btn-text " style="background-image: url(&quot;'+emailPic+'&quot;);">&nbsp;</button></em></td><td class="x-btn-mr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-bl"><i>&nbsp;</i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i>&nbsp;</i></td></tr></tbody></table></div></div>';
		}
		else
		{
			if (pRecord.data.isEmptyRow != 1)
			{
				var delPic = "{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png";
				button = '<div style="overflow: hidden;"><div style="float: right; margin-right: 3px" onClick="onDeleteRecord(\''+pRecord.data.id+'\'); return false;" OnMouseOver="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\') + \' x-btn-over\';" OnMouseOut="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\');"><table cellspacing="0" class="x-btn  x-btn-icon"><tbody class="x-btn-small x-btn-icon-small-left"><tr><td class="x-btn-tl"><i>&nbsp;</i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-ml"><i>&nbsp;</i></td><td class="x-btn-mc"><em unselectable="on" class=""><button type="button" class="x-btn-text " style="background-image: url(&quot;'+delPic+'&quot;);">&nbsp;</button></em></td><td class="x-btn-mr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-bl"><i>&nbsp;</i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i>&nbsp;</i></td></tr></tbody></table></div></div>';
			}
		}
		return button;
	};

	var columnRenderer = function(value, p, record, rowIndex, colIndex, store)
    {
		var className = '';
		if (record.data.isEmptyRow != 1)
		{
			if (value != '')
			{
				var sectionCode = record.data.sectionCode;

				if ((document.getElementById('cbx_' + sectionCode)) && (document.getElementById('cbx_' + sectionCode).checked == false))
				{
					className = 'class = "inactive"';
				}
				value = '<span '+ className +'>'+value+'</span>';
			}
		}
		return value;
    };


	var setPaymentMethods = function()
	{
   		for (var i = 0, theCheckBox; i < gPaymentMethodCount; i++)
   		{
       		theCheckBox = Ext.getCmp("paymentmethod" + i);
     	  	if ((Ext.getCmp('usedefaultpaymentmethods'))&&(Ext.getCmp('usedefaultpaymentmethods').checked))
       		{
       			theCheckBox.setValue(gPaymentMethods.indexOf(theCheckBox.getRawValue()) > -1);
       			theCheckBox.disable();
       		}
       		else
       		{
       			theCheckBox.enable();
       		}
   		}
	};

	var setPaymentIntegration = function()
	{
		if (Ext.getCmp('integrationCustom').checked)
		{
			Ext.getCmp('integrationlist').enable();
		}
		else
		{
			Ext.getCmp('integrationlist').disable();
		}
	};

	var setEmailSettings = function()
	{
		var smtpAuthValue = 0;
		var smtpAddressValue = '';
		var smtpPortValue = 0;
		var smtpAuthUserValue = '';
		var smtpAuthPaswValue = '';
        var smtpType = '';
		var fromNameValue = '';
		var fromAddressValue = '';
		var replyToNameValue = '';
		var replyToAddressValue = '';
		var oauthProvider = 0;
		var oauthToken = 0;

		if ((Ext.getCmp('usedefaultemailsettings')) && (Ext.getCmp('usedefaultemailsettings').checked))
		{
			smtpAddressValue = "{/literal}{$gsmtpaddress}{literal}";
			Ext.getCmp('smtpaddress').disable();

			smtpAuthValue = parseInt("{/literal}{$gsmtpauth}{literal}", 10);

			Ext.getCmp('smtpauth').disable();

			smtpPortValue = "{/literal}{$gsmtpport}{literal}";
			Ext.getCmp('smtpport').disable();

			smtpAuthUserValue = "{/literal}{$gsmtpauthuser}{literal}";
			smtpAuthPaswValue = "{/literal}{$gsmtpauthpass}{literal}";
            smtpType = "{/literal}{$gsmtptype}{literal}";
            Ext.getCmp('smtptype').disable();

			fromNameValue = "{/literal}{$gsmtpsysfromname}{literal}";
			Ext.getCmp('smtpsysfromname').disable();

			fromAddressValue = "{/literal}{$gsmtpsysfromaddress}{literal}";
			Ext.getCmp('smtpsysfromaddress').disable();

			replyToNameValue = "{/literal}{$gsmtpreplyname}{literal}";
			Ext.getCmp('smtpreplyname').disable();

			replyToAddressValue = "{/literal}{$gsmtpreplyaddress}{literal}";
			Ext.getCmp('smtpreplyaddress').disable();

			oauthProvider = parseInt("{/literal}{$goauthprovider}{literal}", 10);
			oauthToken = parseInt("{/literal}{$goauthtokenid}{literal}", 10);

			Ext.getCmp('oauthprovider').disable();
			Ext.getCmp('oauthrefreshtoken').disable();
			Ext.getCmp('oauthauthenticate').disable();
		}
		else
		{
			smtpAddressValue = "{/literal}{$smtpaddress}{literal}";
			Ext.getCmp('smtpaddress').enable();

			smtpAuthValue = parseInt("{/literal}{$smtpauth}{literal}", 10);
			Ext.getCmp('smtpauth').enable();

			smtpPortValue = "{/literal}{$smtpport}{literal}";
			Ext.getCmp('smtpport').enable();

			smtpAuthUserValue = "{/literal}{$smtpauthuser}{literal}";
			smtpAuthPaswValue = "{/literal}{$smtpauthpass}{literal}";
            smtpType = "{/literal}{$smtptype}{literal}";
            Ext.getCmp('smtptype').enable();

			fromNameValue = "{/literal}{$smtpsysfromname}{literal}";
			Ext.getCmp('smtpsysfromname').enable();

			fromAddressValue = "{/literal}{$smtpsysfromaddress}{literal}";
			Ext.getCmp('smtpsysfromaddress').enable();

			replyToNameValue = "{/literal}{$smtpreplyname}{literal}";
			Ext.getCmp('smtpreplyname').enable();

			replyToAddressValue = "{/literal}{$smtpreplyaddress}{literal}";
			Ext.getCmp('smtpreplyaddress').enable();

			oauthProvider = parseInt("{/literal}{$oauthprovider}{literal}", 10);
			oauthToken = parseInt("{/literal}{$oauthtokenid}{literal}", 10);

			Ext.getCmp('oauthprovider').enable();
			Ext.getCmp('oauthrefreshtoken').enable();
			Ext.getCmp('oauthauthenticate').enable();
		}
		oauthTokenString = 2 === smtpAuthValue ? (Math.random() + 1).toString(36) : '';

		Ext.getCmp('smtpaddress').setValue(smtpAddressValue);
		Ext.getCmp('smtpauth').setValue(smtpAuthValue);
		Ext.getCmp('smtpport').setValue(smtpPortValue);
		Ext.getCmp('smtpauthuser').setValue(smtpAuthUserValue);
		Ext.getCmp('smtpauthpass').setValue(smtpAuthPaswValue);
        Ext.getCmp('smtptype').setValue(smtpType);
		Ext.getCmp('smtpsysfromname').setValue(fromNameValue);
		Ext.getCmp('smtpsysfromaddress').setValue(fromAddressValue);
		Ext.getCmp('smtpreplyname').setValue(replyToNameValue);
		Ext.getCmp('smtpreplyaddress').setValue(replyToAddressValue);
		Ext.getCmp('oauthprovider').setValue(oauthProvider);
		Ext.getCmp('oauthrefreshtokenid').setValue(oauthToken);
		Ext.getCmp('oauthrefreshtoken').setValue(oauthTokenString);

		authenticationContainerDisplay(null, null, smtpAuthValue);
		setEmailsGrid();
	};


	var setEmailsGridActive = function()
	{
		var activeCheckbox;
		var checkboxChecked = false;

		for (sectionCode in emailSections)
		{
			section = emailSections[sectionCode];
			activeCheckbox = document.getElementById('cbx_' + sectionCode);

			if ((Ext.getCmp('usedefaultemailsettings')) && (Ext.getCmp('usedefaultemailsettings').checked))
			{
				checkboxChecked = (section.gActive == 1);
			}
			else
			{
				checkboxChecked = (section.active == 1);
			}

			if (activeCheckbox)
			{
				activeCheckbox.checked = checkboxChecked;
			}
		}
	};

	var clearEmailValues = function()
	{
		for (sectionCode in emailSections)
		{
			emailSections[sectionCode].names = '';
			emailSections[sectionCode].emails = '';
		}
	};

	var setEmailsGrid = function()
	{
		var emailsStoreData = [];

		var index = 0;
		for (sectionCode in emailSections)
		{
			section = emailSections[sectionCode];

			if ((Ext.getCmp('usedefaultemailsettings')) && (Ext.getCmp('usedefaultemailsettings').checked))
			{
				names = section.gNames.split(';');
				values = section.gEmails.split(';');
			}
			else
			{
				names = section.names.split(';');
				values = section.emails.split(';');
			}

			for (var i = 0; i < names.length; i++)
			{
				if ((names[i] == '') && (values[i] == ''))
				{
					emailsStoreData.push([index, sectionCode, section.name, '', '', '', 1]);
				}
				else
				{
					emailsStoreData.push([index, sectionCode, section.name, names[i], values[i], '', 0]);
				}
				index++;
			}
		}

		Ext.getCmp('emailsGrid').getStore().loadData(emailsStoreData);

		if ((Ext.getCmp('usedefaultemailsettings')) && (Ext.getCmp('usedefaultemailsettings').checked))
		{
			Ext.getCmp('emailsGrid').disable();
		}
		else
		{
			Ext.getCmp('emailsGrid').enable();
		}
	};


	var initializeControls = function()
	{
		setPaymentMethods();

		{/literal}
		{if $currentintegration == 'NONE'}
			Ext.getCmp('integrationNone').setValue(true);
		{elseif $currentintegration == 'DEFAULT'}
			if (Ext.getCmp('integrationDefault')) Ext.getCmp('integrationDefault').setValue(true);
		{else}
			Ext.getCmp('integrationCustom').setValue(true);
		{/if}
		//if (Ext.getCmp('integrationlist').getStore().getAt(0)) Ext.getCmp('integrationlist').setValue(Ext.getCmp('integrationlist').getStore().getAt(0).data.id);

		setPaymentIntegration();

		{if $displayurl==''}
			Ext.getCmp('displayurl').setValue('http://');
		{/if}

		{if $previewdomainurl === ''}
			Ext.getCmp('previewdomainurl').setValue('http://');
		{/if}

		{if $weburl==''}
			Ext.getCmp('weburl').setValue('http://');
		{/if}

		{if $mainwebsiteurl==''}
			Ext.getCmp('mainwebsiteurl').setValue('http://');
		{/if}

		{if $macdownloadurl==''}
			Ext.getCmp('macdownloadurl').setValue('http://');
		{/if}

		{if $win32downloadurl==''}
			Ext.getCmp('win32downloadurl').setValue('http://');
		{/if}

		{if ($onlinedesignerurl == '') && ($hasonlinedesigner == 1)}
			Ext.getCmp('onlinedesignerurl').setValue('http://');
		{/if}

		{if ($onlineuiurl == '') && ($hasonlinedesigner == 1)}
			Ext.getCmp('onlineuiurl').setValue('http://');
		{/if}

		{if ($onlineapiurl == '') && ($hasonlinedesigner == 1)}
			Ext.getCmp('onlineapiurl').setValue('http://');
		{/if}

		{if ($onlinedesignerlogouturl == '') && ($hasonlinedesigner == 1)}
			Ext.getCmp('onlinedesignerlogouturl').setValue('http://');
		{/if}

		{if ($onlinedesignerlogolinkurl == '') && ($hasonlinedesigner == 1)}
			Ext.getCmp('onlinedesignerlogolinkurl').setValue('http://');
		{/if}

		{if ($onlinedesignercdnurl == '') && ($hasonlinedesigner == 1)}
			Ext.getCmp('onlinedesignercdnurl').setValue('http://');
		{/if}

		{if ($onlineabouturl == '') && ($hasonlinedesigner == 1)}
			Ext.getCmp('onlineabouturl').setValue('http://');
		{/if}

		{if ($onlinehelpurl == '') && ($hasonlinedesigner == 1)}
			Ext.getCmp('onlinehelpurl').setValue('http://');
		{/if}

		{if ($onlinetermsandconditionsurl == '') && ($hasonlinedesigner == 1)}
			Ext.getCmp('onlinetermsandconditionsurl').setValue('http://');
		{/if}

		{literal}

		setEmailSettings();

		refreshRedactionSettings();
		refreshDesktopAccountPagesURlSettings();

		togglePreviewDomainURL();
	};

	function initializeBrandAssets()
	{
		// Get the list of asset refs.
		var assetRefs = Object.keys(lastSavedBrandAssetData);

		// Loop around each asset, set up brand asset.
		assetRefs.forEach(updatePreviewDisplay);
	};

	var showPreviewExpire = function()
	{
		if (Ext.getCmp('previewExpire').checked)
		{
			Ext.getCmp('previewExpireDays').enable();
		}
		else
		{
			Ext.getCmp('previewExpireDays').disable();
			Ext.getCmp('previewExpireDays').setValue(1);
		}
	};

	// Toggle disable/enable for the "Preview Domain URL" field
	function togglePreviewDomainURL()
	{
		if (parseInt(Ext.getCmp('sharehidebranding').getValue()) === 0)
		{
			Ext.getCmp('previewdomainurl').disable();
		}
		else
		{
			Ext.getCmp('previewdomainurl').enable();
		}
	}


	var topPanel = new Ext.Panel({
		id: 'topPanel',
		layout: 'form',
		style: 'background: #c9d8ed; padding: 3px 5px; border:1px solid #8ca9cf',
		plain:true,
		bodyBorder: false,
		border: false,
		defaults: {xtype: 'textfield', width: 505, labelWidth: 150},
		labelWidth: 150,
		bodyStyle:'padding:5px 5px 0; border-top: 0px',
		items:
		[
			{
				xtype: 'textfield',
				id: 'code',
				name: 'code',
				fieldLabel: "{/literal}{#str_LabelCode#}{literal}",
				validateOnBlur:true,
				allowBlank: false,
				maskRe: /^\w+$/,
				maxLength: 50,
				{/literal}{if $brandingid == 0}{literal}
					readOnly: false,
					style: {textTransform: "uppercase"}
				{/literal}{else}{literal}
					value: "{/literal}{if $code == ''}{#str_LabelDefault#}{else}{$code}{/if}{literal}",
					readOnly: true,
					style: 'background:#c9d8ed; textTransform: uppercase'
				{/literal}{/if}{literal}
			}
		]
	});

	function activateUserIDTracking()
	{
		if (! awaitingUserIDTrackingResponse)
		{
			awaitingUserIDTrackingResponse = true;
			var currentIDTrackingActive = Ext.getCmp('useridtracking');

			if (currentIDTrackingActive.checked)
			{
				currentIDTrackingActive.setValue(0);

				var onActivateUserIDTracking = function(btn)
				{
					if (btn == "yes")
					{
						currentIDTrackingActive.setValue(1);
					}
					awaitingUserIDTrackingResponse = false;
				};

				Ext.MessageBox.minWidth = 350;
				Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_UserIdTrackingWarning#}{literal}", onActivateUserIDTracking);
			}
			else
			{
				awaitingUserIDTrackingResponse = false;
			}
		}
	}

	function enableUserIDTrackingCheckBox()
	{
		var userIDTrackingCheckbox = Ext.getCmp('useridtracking');
		var googleAnalyticsCodeField = Ext.getCmp('googlecode');

		if (googleAnalyticsCodeField.getValue() != "")
		{
			userIDTrackingCheckbox.enable();
		}
		else
		{
			userIDTrackingCheckbox.disable();
			userIDTrackingCheckbox.setValue(0);
		}
	}

	var redactionModeActionButton = new Ext.Panel({
		layout: 'form',
		id: 'redactionmodeactivate',
		name: 'redactionmodeactivate',
	{/literal}{if $redactionMode == 0}{literal}
		fieldLabel: "{/literal}{#str_LabelPersonalDataDeletionMode#} <b>{#str_LabelDisabled#}</b>{literal}",
	{/literal}{else}{literal}
		fieldLabel: "{/literal}{#str_LabelPersonalDataDeletionMode#} <b>{#str_LabelEnabled#}</b>{literal}",
	{/literal}{/if}{literal}
		items: [
			new Ext.Button({
				id: 'redactionmodebutton',
				name: 'redactionmodebutton',
				{/literal}{if $redactionMode == 0}{literal}
					text: "{/literal}{#str_LabelEnable#}{literal}",
				{/literal}{else}{literal}
					text: "{/literal}{#str_LabelDisable#}{literal}",
				{/literal}{/if}{literal}
				minWidth: 100,
				listeners: { click: changeRedactionActiveState }
			}),
			new Ext.form.Checkbox(
			{
				boxLabel: "{/literal}{#str_DataDeletionAdmin#}{literal}",
				name: 'redactbyadmin',
				id: 'redactbyadmin',
				checked: {/literal}{$redactByAdmin}{literal},
				hideLabel: true,
				disabled: true,
				style: { padding: '0px 0px 2px 0px' }
			})
		]
	});


	var redactionUserConfigPanel = new Ext.Panel({
		id: 'redactionconfig',
		name: 'redactionconfig',
		fieldLabel: "{/literal}{#str_LabelUserSettings#}{literal}",
		items:
		[
			{
				xtype: 'radiogroup',
				columns: 1,
				autoWidth: true,
				id: 'redactionModeSelect',
				value: {/literal}{$redactionMode}{literal},
				listeners: { change: changeRedactionSelection },
				items:
				[
					{
						boxLabel: "Disabled",
						hidden: true,
						name: 'redactoption',
						inputValue: {/literal}{$redactionModeOptions.disabled}{literal}
					},
					{
						boxLabel: "{/literal}{#str_DataDeletionAdminOnly#}{literal}",
						name: 'redactoption',
						inputValue: {/literal}{$redactionModeOptions.administrator}{literal}
					},
					{
						boxLabel: "{/literal}{#str_DataDeletionRequest#}{literal}",
						name: 'redactoption',
						inputValue: {/literal}{$redactionModeOptions.request}{literal}
					},
					{
						boxLabel: "{/literal}{#str_DataDeletionUser#}{literal}",
						name: 'redactoption',
						inputValue: {/literal}{$redactionModeOptions.allow}{literal}
					}
				]
			},
			{
				xtype: 'radiogroup',
				columns: 1,
				autoWidth: true,
				id: 'redactionModeSelectUser',
				value: {/literal}{$redactionMode}{literal},
				style: { padding: '0px 0px 0px 30px' },
				listeners: { change: changeRedactionSelectionUser },
				items:
				[
					{
						boxLabel: "{/literal}{#str_DataDeletionUserImmediate#}{literal}",
						name: 'redactoptionuser',
						inputValue: {/literal}{$redactionModeOptions.immediate}{literal}
					},
					{
						boxLabel: "{/literal}{#str_DataDeletionUserDelayed#}{literal}",
						name: 'redactoptionuser',
						inputValue: {/literal}{$redactionModeOptions.allow}{literal}
					}
				]
			},
			{
				xtype: 'numberfield',
				id: 'redactionnotificationdays',
				name: 'redactionnotificationdays',
				value: {/literal}{$redactionnotificationdays}{literal},
				post: true,
				validateOnBlur: true,
				minValue: 7,
				width: 100,
				allowBlank: false,
				allowNegative: false,
				allowDecimal: false,
				listeners: { disable: disableNotificationDays },
				style:
				{
					margin: '0 0 0 50px'
				}
			}
		]
	});


	var redactionAutoConfigPanel = new Ext.Panel({
		layout: 'form',
		id: 'redactionauto',
		name: 'redactionauto',
		hidden: true,
		fieldLabel: "{/literal}{#str_LabelAutomaticDeletionSettings#}{literal}",
		items: [
			new Ext.form.Checkbox(
			{
				boxLabel: "{/literal}{#str_LabelAutomaticDeletionEnable#}{literal}",
				name: 'automaticredaction',
				id: 'automaticredaction',
				checked: {/literal}{$automaticredactionenabled}{literal},
				hideLabel: true,
				listeners: { check: refreshRedactionSettings }
			}),
			new Ext.Container(
			{
				id: 'automaticredactionmessage',
				name: 'automaticredactionmessage',
				html: "{/literal}{#str_MessageAutoDataDeletion#}{literal}",
				style: { padding: '10px 0px' }
			}),
			{
				xtype: 'numberfield',
				id: 'redactiondays',
				name: 'redactiondays',
				fieldLabel: '',
				value: {/literal}{$automaticredactiondays}{literal},
				post: true,
				validateOnBlur: true,
				minValue: 180,
				width: 100,
				allowBlank: false,
				allowNegative: false,
				allowDecimal: false,
				hideLabel: true
			}
		]
	});


	function changeRedactionSelection()
	{
		// enable or disable the user based redaction option
		var currentRedactionOption = Ext.getCmp('redactionModeSelect');
		var currentRedactionOptionUser = Ext.getCmp('redactionModeSelectUser');
		var currentRedactNotification = Ext.getCmp('redactionnotificationdays');

		var selectedRedactionOption = currentRedactionOption.getValue();
		var selectedRedactionOptionUser = currentRedactionOptionUser.getValue();

		var selectedRedactionMode = selectedRedactionOption.inputValue;

		if (selectedRedactionMode == {/literal}{$redactionModeOptions.allow}{literal})
		{
			// enable the user options
			currentRedactionOptionUser.enable();

			if (initialRedactionMode <= {/literal}{$redactionModeOptions.allow}{literal})
			{
				currentRedactionOptionUser.setValue({/literal}{$redactionModeOptions.allow}{literal});
			}
			else
			{
				currentRedactionOptionUser.setValue({/literal}{$redactionModeOptions.immediate}{literal});
			}
		}
		else
		{
			// disable the user options
			currentRedactionOptionUser.disable();
			currentRedactionOptionUser.setValue({/literal}{$redactionModeOptions.disabled}{literal});

			// disable the notification days edit box
			currentRedactNotification.disable();
		}
	}

	function changeRedactionSelectionUser()
	{
        var currentRedactionOption = Ext.getCmp('redactionModeSelect');
		var selectedRedactionOption = currentRedactionOption.getValue();
		var selectedRedactionMode = selectedRedactionOption.inputValue;

		// user level options have changed, if the selected level has the options available, enable or disable the notification edit box
		if (selectedRedactionMode >= {/literal}{$redactionModeOptions.allow}{literal})
		{
			var currentRedactionOptionUser = Ext.getCmp('redactionModeSelectUser');
			var currentRedactNotification = Ext.getCmp('redactionnotificationdays');

			var selectedRedactionOptionUser = currentRedactionOptionUser.getValue();

			selectedRedactionMode = selectedRedactionOptionUser.inputValue;

			if (selectedRedactionMode == {/literal}{$redactionModeOptions.allow}{literal})
			{
				// enable the notification box
				currentRedactNotification.enable();
			}
			else if (selectedRedactionMode == {/literal}{$redactionModeOptions.immediate}{literal})
			{
				// disable the notification box
				currentRedactNotification.disable();
			}
		}
	}

	function changeOrderRedactionActiveState()
	{
		if (orderRedactionMode == 0)
		{
			Ext.MessageBox.minWidth = 350;
			Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_MessageOrderDataDeletionConfirm#}{literal}", function(btn)
			{
				if (btn == "yes")
				{
					orderRedactionMode = 1;

					Ext.getCmp('orderredactiondays').enable();
					Ext.getCmp('orderredactionmodebutton').setText("{/literal}{#str_LabelDisable#}{literal}");
					Ext.getCmp('orderredactionmodeactivate').label.update("{/literal}{#str_LabelOrderDataDeletionMode#} <b>{#str_LabelEnabled#}</b>:{literal}");
				}
			});
		}
		else
		{
			orderRedactionMode = 0;

			Ext.getCmp('orderredactiondays').disable();
			Ext.getCmp('orderredactionmodebutton').setText("{/literal}{#str_LabelEnable#}{literal}");
			Ext.getCmp('orderredactionmodeactivate').label.update("{/literal}{#str_LabelOrderDataDeletionMode#} <b>{#str_LabelDisabled#}</b>:{literal}");
		}
	}

	function changeDesktopProjectThumbnailDeletionModeState()
	{
		if (desktopThumbnailDeletionEnabled == 0)
		{
			Ext.MessageBox.minWidth = 350;
			Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_MessageDesktopProjectThumbnailDeletionConfirm#}{literal}", function(btn)
			{
				if (btn == "yes")
				{
					desktopThumbnailDeletionEnabled = 1;

					Ext.getCmp('ordereddesktopprojectthumbnaildeletiondays').enable();
					Ext.getCmp('desktopprojectthumbnaildeletionactivatebutton').setText("{/literal}{#str_LabelDisable#}{literal}");
					Ext.getCmp('desktopprojectthumbnaildeletionactivate').label.update("{/literal}{#str_LabelDesktopProjectThumbnailDeletionMode#} <b>{#str_LabelEnabled#}</b>:{literal}");
				}
			});
		}
		else
		{
			desktopThumbnailDeletionEnabled = 0;

			Ext.getCmp('ordereddesktopprojectthumbnaildeletiondays').disable();
			Ext.getCmp('desktopprojectthumbnaildeletionactivatebutton').setText("{/literal}{#str_LabelEnable#}{literal}");
			Ext.getCmp('desktopprojectthumbnaildeletionactivate').label.update("{/literal}{#str_LabelDesktopProjectThumbnailDeletionMode#} <b>{#str_LabelDisabled#}</b>:{literal}");
		}
	}

	function changeRedactionActiveState()
	{
		// change the status of the personal data deletion
		var currentActive = Ext.getCmp('redactionmodeactivate');
		var currentActivateButton = Ext.getCmp('redactionmodebutton');
		var currentRedactByAdmin = Ext.getCmp('redactbyadmin');
		var currentRedactionOption = Ext.getCmp('redactionModeSelect');
		var currentRedactionOptionUser = Ext.getCmp('redactionModeSelectUser');
		var currentRedactNotification = Ext.getCmp('redactionnotificationdays');

		if (!currentRedactByAdmin.checked)
		{
			// currently disabled, enable the setting
			var onActivateRedactionConfirmed = function(btn)
			{
				if (btn == "yes")
				{
					// get the previously selected mode
					selectedRedactionMode = previousRedactionMode;
					if (selectedRedactionMode == {/literal}{$redactionModeOptions.disabled}{literal})
					{
						selectedRedactionMode = {/literal}{$redactionModeOptions.administrator}{literal};
					}

					// update the button and label text
					currentActivateButton.setText("{/literal}{#str_LabelDisable#}{literal}");
					currentActive.label.update("{/literal}{#str_LabelPersonalDataDeletionMode#} <b>{#str_LabelEnabled#}</b>:{literal}");

					// set the administrator redaction check box
					currentRedactByAdmin.setValue(1);

					// enable the primary redaction options for the user
					currentRedactionOption.enable();

					if (selectedRedactionMode >= {/literal}{$redactionModeOptions.allow}{literal})
					{
						// if the user option is the user can trigger the redaction, enable the sub options
						// set the primary option to 'user can'
						currentRedactionOption.setValue({/literal}{$redactionModeOptions.allow}{literal});

						currentRedactionOptionUser.enable();
						currentRedactionOptionUser.setValue(selectedRedactionMode);

						if (selectedRedactionMode == {/literal}{$redactionModeOptions.allow}{literal})
						{
							currentRedactNotification.enable();
						}
						else
						{
							currentRedactNotification.disable();
						}
					}
					else
					{
						// if the user option is either of the first 2, set the value and leave the sub options disabled
						currentRedactionOption.setValue(selectedRedactionMode);
					}
				}
			};

			Ext.MessageBox.minWidth = 350;
			Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_MessageAutoDataDeletionConfirm#}{literal}", onActivateRedactionConfirmed);
		}
		else
		{
			// currently enabled, disable the setting
			// update the button and label text
			currentActivateButton.setText("{/literal}{#str_LabelEnable#}{literal}");
			currentActive.label.update("{/literal}{#str_LabelPersonalDataDeletionMode#} <b>{#str_LabelDisabled#}</b>:{literal}");

			// get the current redaction mode setting
			var selectedRedactionOption = currentRedactionOption.getValue();
			var selectedRedactionOptionUser = currentRedactionOptionUser.getValue();

			if (selectedRedactionOption.inputValue == {/literal}{$redactionModeOptions.allow}{literal})
			{
				selectedRedactionMode = selectedRedactionOptionUser.inputValue;
			}
			else
			{
				selectedRedactionMode = selectedRedactionOption.inputValue;
			}

			// store the setting so it can be restored if the redaction is enabled again, but only while the window is open
			previousRedactionMode = selectedRedactionMode;

			// disable all of the options
			currentRedactByAdmin.setValue(0);
			currentRedactionOption.disable();
			currentRedactionOptionUser.disable();
			currentRedactNotification.disable();

			currentRedactionOption.setValue(0);
			currentRedactionOptionUser.setValue(0);
		}
	}


	function refreshRedactionSettings()
	{
		// set the forms initial display
		var currentActive = Ext.getCmp('redactionmodeactivate');
		var currentActivateButton = Ext.getCmp('redactionmodebutton');
		var currentRedactByAdmin = Ext.getCmp('redactbyadmin');
		var currentRedactionOption = Ext.getCmp('redactionModeSelect');
		var currentRedactionOptionUser = Ext.getCmp('redactionModeSelectUser');
		var currentRedactNotification = Ext.getCmp('redactionnotificationdays');

		var currentAutoRedaction = Ext.getCmp('automaticredaction');
		var currentAutoRedactionDays = Ext.getCmp('redactiondays');
		var currentAutoRedactionMessage = Ext.getCmp('automaticredactionmessage');

		var currentRedactionModeValue = initialRedactionMode;

		// set up the form
		if (currentRedactByAdmin.checked == true)
		{
			currentRedactByAdmin.setValue(1);

			if (currentRedactionModeValue >= {/literal}{$redactionModeOptions.allow}{literal})
			{
				currentRedactionOption.setValue({/literal}{$redactionModeOptions.allow}{literal});
				currentRedactionOption.enable();

				currentRedactionOptionUser.setValue(currentRedactionModeValue);
				currentRedactionOptionUser.enable();

				if (currentRedactionModeValue == {/literal}{$redactionModeOptions.allow}{literal})
				{
					currentRedactNotification.enable();
				}
				else
				{
					currentRedactNotification.disable();
				}
			}
			else
			{
				currentRedactNotification.disable();

				currentRedactionOptionUser.setValue(0);
				currentRedactionOptionUser.disable();

				currentRedactionOption.setValue(currentRedactionModeValue);
				currentRedactionOption.enable();
			}
		}
		else
		{
			currentRedactByAdmin.setValue(0);
			currentRedactNotification.disable();
			currentRedactionOption.disable();
			currentRedactionOptionUser.disable();

			currentAutoRedaction.disable();
			currentAutoRedactionDays.disable();
			currentAutoRedactionMessage.enable();
		}
	}

	var refreshDesktopAccountPagesURlSettings = function()
	{
		var defaultChecked = Ext.getCmp('usedefaultaccountpagesurl').checked;
		var accountPagesURLEditField = Ext.getCmp('customaccountpagesurl');

		if (defaultChecked)
		{
			// disable the url edit field
			accountPagesURLEditField.reset();
			accountPagesURLEditField.disable();
		}
		else
		{
			// enable and clear the url edit field
			accountPagesURLEditField.reset();
			if (customAccountPagesURL != '')
			{
				accountPagesURLEditField.setValue(customAccountPagesURL);
			}
			else
			{
				accountPagesURLEditField.setValue('https://');
			}

			accountPagesURLEditField.enable();
		}
	}


	function disableNotificationDays()
	{
		// force the value of the notification days to 7 if the field is diabled
		var notificationField = Ext.getCmp('redactionnotificationdays');

		if (notificationField.value < 7)
		{
			notificationField.setValue(7);
		}
	}


	var dataDeletionTab =
	{
		title: "{/literal}{#str_LabelDataManagement#}{literal}",
		id:'dataDeletionTab',
		hideMode:'offsets',
		items:
		[
			{
		        xtype:'fieldset',
		        title: "{/literal}{#str_LabelPersonalDataDeletion#}{literal}",
		        collapsible: false,
		        autoHeight: true,
				labelWidth: 320,
				style: 'position: relative;',
				defaults: {xtype: 'textfield'},
				layout: 'form',
		        items :
		        [
		        	new Ext.Container(
					{
						html: "{/literal}{#str_MessagePersonalDataDeletion#}{literal}",
						style: { padding: '10px 0px 5px 0px' }
					}),
					redactionModeActionButton,
					{
						xtype: 'box',
						autoEl: {tag: 'hr'}
					},
					redactionUserConfigPanel,
					{
						xtype: 'box',
						autoEl: {tag: 'br'}
					},
					redactionAutoConfigPanel
				]
			},
			{
		        xtype:'fieldset',
		        title: "{/literal}{#str_LabelOrderDataDeletion#}{literal}",
		        collapsible: false,
		        autoHeight: true,
				labelWidth: 320,
				style: 'position: relative;',
				defaults: {xtype: 'textfield'},
				layout: 'form',
		        items :
		        [
		        	new Ext.Container(
					{
						html: "{/literal}{#str_MessageOrderDataDeletion#}{literal}",
						style: { padding: '10px 0px 5px 0px' }
					}),
					new Ext.Panel(
					{
						layout: 'form',
						id: 'orderredactionmodeactivate',
						name: 'orderredactionmodeactivate',
						{/literal}{if $orderredactionmode == 0}{literal}
						fieldLabel: "{/literal}{#str_LabelOrderDataDeletionMode#} <b>{#str_LabelDisabled#}</b>{literal}",
						{/literal}{else}{literal}
						fieldLabel: "{/literal}{#str_LabelOrderDataDeletionMode#} <b>{#str_LabelEnabled#}</b>{literal}",
						{/literal}{/if}{literal}
						items:
						[
							new Ext.Button(
							{
								id: 'orderredactionmodebutton',
								name: 'orderredactionmodebutton',
								{/literal}{if $orderredactionmode == 0}{literal}
								text: "{/literal}{#str_LabelEnable#}{literal}",
								{/literal}{else}{literal}
								text: "{/literal}{#str_LabelDisable#}{literal}",
								{/literal}{/if}{literal}
								minWidth: 100,
								listeners: { click: changeOrderRedactionActiveState }
							})
						]
					}),
					{
						xtype: 'numberfield',
						id: 'orderredactiondays',
						name: 'orderredactiondays',
						fieldLabel: '{/literal}{#str_LabelOrderDataDays#}{literal}',
						value: {/literal}{$orderredactiondays}{literal},
						post: true,
						validateOnBlur: true,
						minValue: 7,
						width: 100,
						allowBlank: false,
						allowNegative: false,
						allowDecimal: false,
						{/literal}{if $orderredactionmode == 0}{literal}
						disabled: true
						{/literal}{/if}{literal}
					}
				]
			},
			{
		        xtype:'fieldset',
		        title: "{/literal}{#str_LabelDesktopProjectThumbnailDeletion#}{literal}",
		        collapsible: false,
		        autoHeight: true,
				labelWidth: 320,
				style: 'position: relative;',
				defaults: {xtype: 'textfield'},
				layout: 'form',
		        items :
		        [
					new Ext.Panel(
					{
						layout: 'form',
						id: 'desktopprojectthumbnaildeletionactivate',
						name: 'desktopprojectthumbnaildeletionactivate',
						{/literal}{if $desktopthumbnaildeletionenabled == 0}{literal}
						fieldLabel: "{/literal}{#str_LabelDesktopProjectThumbnailDeletionMode#} <b>{#str_LabelDisabled#}</b>{literal}",
						{/literal}{else}{literal}
						fieldLabel: "{/literal}{#str_LabelDesktopProjectThumbnailDeletionMode#} <b>{#str_LabelEnabled#}</b>{literal}",
						{/literal}{/if}{literal}
						items:
						[
							new Ext.Button(
							{
								id: 'desktopprojectthumbnaildeletionactivatebutton',
								name: 'desktopprojectthumbnaildeletionactivatebutton',
								{/literal}{if $desktopthumbnaildeletionenabled == 0}{literal}
								text: "{/literal}{#str_LabelEnable#}{literal}",
								{/literal}{else}{literal}
								text: "{/literal}{#str_LabelDisable#}{literal}",
								{/literal}{/if}{literal}
								minWidth: 100,
								listeners: { click: changeDesktopProjectThumbnailDeletionModeState }
							})
						]
					}),
					{
						xtype: 'displayfield',
						id: 'desktopprojectthumbnaildaystodeletiondisplayfield',
						name: 'desktopprojectthumbnaildaystodeletiondisplayfield',
						value: '{/literal}{#str_LabelOrderDataDays#}{literal}'
					},
					{
						xtype: 'numberfield',
						id: 'ordereddesktopprojectthumbnaildeletiondays',
						name: 'ordereddesktopprojectthumbnaildeletiondays',
						fieldLabel: '{/literal}{#str_LabelOrderedProjectThumbnails#}{literal}',
						value: {/literal}{$desktopthumbnaildeletionordereddays}{literal},
						post: true,
						validateOnBlur: true,
						minValue: 7,
						width: 100,
						allowBlank: false,
						allowNegative: false,
						allowDecimal: false,
						{/literal}{if $desktopthumbnaildeletionenabled == 0}{literal}
						disabled: true
						{/literal}{/if}{literal}
					}
				]
			}
		{/literal}{if $optionDESOL && !$optionHOLDES}{literal}
			,{
				xtype:'fieldset',
		        title: "{/literal}{#str_SectionTitleManagementPolicies#}{literal}",
		        collapsible: false,
		        autoHeight: true,
		        defaultType: 'textfield',
		        labelWidth: 293,
				items :
		        [
					{
						fieldLabel: "{/literal}{#str_SectionTitleManagementPolicy#}{literal}",
						xtype: 'combo',
						id: 'onlinedataretentionpolicy',
						name: 'onlinedataretentionpolicy',
						mode: 'local',
						editable: false,
						forceSelection: true,
						selectOnFocus: true,
						triggerAction: 'all',
						width: 200,
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data: [
								[0, "{/literal}{#str_LabelNone#}{literal}"]
								{/literal}
									{section name=index loop=$onlinedataretentionpolicyoptions}
										,[{$onlinedataretentionpolicyoptions[index].id}, "{$onlinedataretentionpolicyoptions[index].name}"]
									{/section}
								{literal}
							]
						}),
						valueField: 'id',
						displayField: 'name',
						useID: true,
						value: {/literal}{$onlinedataretentionpolicy}{literal},
						post: true
					}
				]
			}
		{/literal}{/if}{literal}
		]
	};

	var urlsPanel =
	{
		xtype:'fieldset',
        title: urls_txt,
        collapsible: false,
        autoHeight: true,
        defaultType: 'textfield',
        labelWidth: 293,
		items :
        [
			{
				xtype: 'textfield',
				id: 'onlinedesignerurl',
				name: 'onlinedesignerurl',
				fieldLabel: "{/literal}{#str_LabelOnlineDesignerURL#}{literal}",
				width: 505,
				{/literal}{if $brandingid != 0}{literal}
				value: "{/literal}{$onlinedesignerurl}{literal}",
				{/literal}{/if}{literal}
				validateOnBlur: true,
				post: true,
				validator: function(v){ return validateUrl(this); },
				listeners: {
					blur: function() {
						var value = this.getValue();
						if ('http://' !== value && '/' === value.split('')[value.length - 1]) {
							this.setValue(value.slice(0, value.length - 1))
						}
					}
				}
			},
			{
				xtype: 'textfield',
				id: 'onlineapiurl',
				name: 'onlineapiurl',
				width: 505,
				fieldLabel: "{/literal}{#str_LabelAPIURL#}{literal}", //change
				{/literal}{if $brandingid != 0}{literal}
				value: "{/literal}{$onlineapiurl}{literal}", //change
				{/literal}{/if}{literal}
				validateOnBlur: true,
				post: true,
				validator: function(v){ return validateUrl(this);  },
				listeners: {
					blur: function() {
						var value = this.getValue();
						if ('http://' !== value && '/' === value.split('')[value.length - 1]) {
							this.setValue(value.slice(0, value.length - 1))
						}

						var ui = Ext.getCmp('onlineuiurl');
						if ('http://' !== ui.getValue()) {
							return;
						}
						var name = Ext.getCmp('name').getValue().toLowerCase();
						var brandedUIURL = this.getValue() + '/ui/' + name;

						if (brandedUIURL) {
							ui.setValue(brandedUIURL);
						}
					}
				}
			},
			{
				xtype: 'textfield',
				id: 'onlineuiurl',
				name: 'onlineuiurl',
				width: 505,
				fieldLabel: "{/literal}{#str_LabelUiURL#}{literal}", // change
				{/literal}{if $brandingid != 0}{literal}
				value: "{/literal}{$onlineuiurl}{literal}", // change
				{/literal}{/if}{literal}
				validateOnBlur: true,
				post: true,
				validator: function(v){ return validateUrl(this);  },
				listeners: {
					blur: function() {
						var value = this.getValue();
						if ('http://' !== value && '/' === value.split('')[value.length - 1]) {
							this.setValue(value.slice(0, value.length - 1))
						}
					}
				}
			},
			{
				xtype: 'textfield',
				id: 'onlinedesignerlogouturl',
				name: 'onlinedesignerlogouturl',
				fieldLabel: "{/literal}{#str_LabelOnlineDesignerLogoutURL#}{literal}",
				width: 505,
				{/literal}{if $brandingid != 0}{literal}
				value: "{/literal}{$onlinedesignerlogouturl}{literal}",
				{/literal}{/if}{literal}
				validateOnBlur: true,
				post: true,
				validator: function(v){ return validateUrl(this); }
			},
			{
				xtype: 'textfield',
				id: 'onlinedesignercdnurl',
				name: 'onlinedesignercdnurl',
				fieldLabel: "{/literal}{#str_LabelOnlineDesignerCDNURL#}{literal}",
				width: 505,
				{/literal}{if $brandingid != 0}{literal}
				value: "{/literal}{$onlinedesignercdnurl}{literal}",
				{/literal}{/if}{literal}
				validateOnBlur: true,
				post: true,
				validator: function(v){ return validateUrl(this); }
			},
			{
				xtype: 'textfield',
				id: 'onlineabouturl',
				name: 'onlineabouturl',
				fieldLabel: "{/literal}{#str_LabelOnlineDesignerAboutUrl#}{literal}",
				width: 505,
				value: "{/literal}{$onlineabouturl}{literal}",
				validateOnBlur: true,
				post: true,
				validator: function(v){ return validateUrl(this); }
			},
			{
				xtype: 'textfield',
				id: 'onlinehelpurl',
				name: 'onlinehelpurl',
				fieldLabel: "{/literal}{#str_LabelOnlineDesignerHelpUrl#}{literal}",
				width: 505,
				value: "{/literal}{$onlinehelpurl}{literal}",
				validateOnBlur: true,
				post: true,
				validator: function(v){ return validateUrl(this); }
			},
			{
				xtype: 'textfield',
				id: 'onlinetermsandconditionsurl',
				name: 'onlinetermsandconditionsurl',
				fieldLabel: "{/literal}{#str_LabelOnlineDesignerTermsAndConditionsUrl#}{literal}",
				width: 505,
				value: "{/literal}{$onlinetermsandconditionsurl}{literal}",
				validateOnBlur: true,
				post: true,
				validator: function(v){ return validateUrl(this); }
			}
		]
	};

	var keyPanel =
	{
		xtype:'fieldset',
        title: appkey_txt,
        collapsible: false,
        autoHeight: true,
        defaultType: 'textfield',
        labelWidth: 293,
		items :
        [
			{
				layout: 'hbox',
				xtype: 'container',
				items: [
					{
						xtype: 'button',
						text: "{/literal}{#str_LabelDownloadAppKey#}{literal}",
						minWidth: 100,
						handler: function() {
							downloadKey(false);
						}
					},
					{
						xtype: 'button',
						text: "{/literal}{#str_LabelGenerateAppKey#}{literal}",
						minWidth: 100,
						handler: generateKey,
						disabled: regenerateVisible
					},
				]
			},
		]
	};

	var multiLinePanel =
	{
		xtype:'fieldset',
        title: generalsettings_txt,
        collapsible: false,
        autoHeight: true,
        defaultType: 'textfield',
        labelWidth: 293,
		items :
        [
			{
				layout: 'hbox',
				xtype: 'container',
				items: [
					new Ext.form.Checkbox(
					{
						boxLabel: "{/literal}{#str_LabelMultiLineBasketWorkflowEnabled#}{literal}",
						name: 'usemultilinebasketworkflow',
						id: 'usemultilinebasketworkflow',
						hideLabel: false,
						checked: {/literal}{$usemultilinebasketworkflow}{literal}
					})
				]
			},
		]
	};

	var generalSettingsPanel =
	{
		xtype:'fieldset',
        title: "{/literal}{#str_LabelPhotoPrintsOptions#}{literal}",
        collapsible: false,
        autoHeight: true,
        defaultType: 'textfield',
        labelWidth: 293,
		items :
        [
			new Ext.Panel(
			{
				style:'padding-top:7px; padding-bottom:10px;border-bottom:1px solid #ccc; margin-bottom:7px;',
				layout: 'form',
				items: [
					{
						xtype: 'textfield',
						id: 'onlinedesignerlogolinkurl',
						name: 'onlinedesignerlogolinkurl',
						fieldLabel: "{/literal}{#str_LabelOnlineDesignerLogoLinkUrl#}{literal}",
						width: 505,
						value: "{/literal}{$onlinedesignerlogolinkurl}{literal}",
						validateOnBlur: true,
						post: true,
						validator: function(v){ return validateUrl(this); }
					},
					new Ext.Button(
					{
						fieldLabel: "{/literal}{#str_LabelOnlineDesignerLogoLinkTitle#}{literal}",
						text: "{/literal}{#str_LabelSetLinkToolTip#}{literal}",
						minWidth: 100,
						listeners: { click: openOnlineDesignerToolTip },
					}),
					{
						xtype: 'hidden',
						id: 'onlinedesignerlogolinktooltip',
						name: 'onlinedesignerlogolinktooltip',
						maxLength: 1024,
						value: "{/literal}{$onlinedesignerlogolinktooltip|escape}{literal}"
					},
				]
			}),
			new Ext.Panel(
			{
				style:'padding-top:7px; padding-bottom:10px;border-bottom:1px solid #ccc; margin-bottom:7px;',
				layout: 'form',
				items:
				[
					{
						xtype: 'numberfield',
						id: 'nagdelay',
						name: 'nagdelay',
						fieldLabel: "{/literal}{#str_LabelSavePromptDelay#}{literal}",
						{/literal}{if $brandingid != 0}{literal}
							value: "{/literal}{$nagdelay}{literal}",
						{/literal}{else}{literal}
							value: 10,
						{/literal}{/if}{literal}
						minValue: 1,
						maxValue: 9999,
						validateOnBlur: true,
						post: true,
						width: 50,
						allowBlank: false,
						allowNegative: false,
						allowDecimal: false
					}
				]
			}),
			{/literal}
			{if $allowimagescalingbefore}
			{literal}
			new Ext.Panel(
			{
				style:'padding-top:7px; padding-bottom:10px;border-bottom:1px solid #ccc; margin-bottom:7px;',
				layout: 'form',
				items:
				[
					new Ext.form.Checkbox(
					{
						name: 'imagescalingbeforeenabled',
						id: 'imagescalingbeforeenabled',
						checked: imagescalingBeforeEnabledVal,
						boxLabel: imagescalingbeforeenabled_txt,
						fieldLabel: imagescalingbefore_txt,
						listeners:
						{
							check: function()
							{
								Ext.getCmp('imagescalingbefore').setDisabled(!Ext.getCmp('imagescalingbeforeenabled').checked);
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
			}),
			{/literal}
			{/if}
			{literal}
			new Ext.Panel(
			{
				style:'padding-top:7px; padding-bottom:10px;border-bottom:1px solid #ccc; margin-bottom:7px;',
				layout: 'form',
				items:
				[
					new Ext.form.Checkbox(
					{
						name: 'imagescalingafterenabled',
						id: 'imagescalingafterenabled',
						checked: imagescalingAfterEnabledVal,
						boxLabel: imagescalingafterenabled_txt,
						fieldLabel: imagescalingafter_txt,
						listeners:
						{
							check: function()
							{
								Ext.getCmp('imagescalingafter').setDisabled(!Ext.getCmp('imagescalingafterenabled').checked);
							}
						}
					}),
					{
						id: 'imagescalingafter',
						name: 'imagescalingafter',
						xtype: 'numberfield',
						value: imagescalingAfterVal,
						disabled: !imagescalingAfterEnabledVal,
						post: true,
						validateOnBlur: true,
						labelStyle: 'text-align:left;',
						maxValue: 999.99,
						minValue: 30.00,
						decimalPrecision: 2,
						forcePrecision: true,
						width: 50,
						fieldLabel: maxmegapixels_txt,
						validator: function(v)
						{
							v = String(v).replace(this.decimalSeparator, ".");
							return ((v <= 999.99) && (v >= 30));
						},
						setValue: function(v)
						{
							if (v < 30)
							{
								v = 30;
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
			}),
			new Ext.Panel(
			{
				style:'padding-top:7px; margin-bottom:7px;',
				layout: 'form',
				items:
				[
					new Ext.form.Checkbox(
						{
							name: 'automaticallyapplyperfectlyclear',
							id: 'automaticallyapplyperfectlyclear',
							fieldLabel: "{/literal}{#str_LabelPerfectlyClear#}{literal}",
							boxLabel: "{/literal}{#str_LabelAutomaticallyApplyToAllImages#}{literal}",
							checked: "{/literal}{$automaticallyapplyperfectlyclear}{literal}",
							listeners:
							{
								check: function(pCheckBox, pChecked)
								{
									var togglePerfectlyClearOnOff = Ext.getCmp('toggleperfectlyclear');
									togglePerfectlyClearOnOff.setDisabled(! Ext.getCmp('automaticallyapplyperfectlyclear').checked);
									togglePerfectlyClearOnOff.clearInvalid(true);

									if (! pChecked)
									{
										Ext.getCmp('toggleperfectlyclear').setValue(false);
									}

								}
							}
						}),
						{
							xtype: 'checkbox',
							id: 'toggleperfectlyclear',
							itemCls: 'x-check-group-alt',
							columns: 1,
							style: 'margin-left: 15px',
							disabled: true,
							width: 400,
							boxLabel: "{/literal}{#str_LabelAllowUsersToToggle#}{literal}",
							checked: "{/literal}{$allowuserstotoggleperfectlyclear}{literal}"
						}
					]
			})
		]
	};

	{/literal}{if $hasonlinedesigner == 1}{literal}
	var fontList =
	{
		xtype: 'fieldset',
		title: "{/literal}{#str_TitleFontSection#}{literal}",
		autoHeight: true,
		defaultType: 'textfield',
		labelWidth: 293,
		items:
		[
			{
				xtype: 'radiogroup',
				id: 'fontlisttype',
				name: 'fontlisttype',
				fieldLabel: "{/literal}{#str_LabelFontListSelection#}{literal}",
				title: '',
				layout: 'form',
				columns: 1,
				items: [
					new Ext.form.Radio(
					{
						name: 'fontlistselection',
						value: 1,
						id: 'useallfonts',
						checked: {/literal}{if is_null($selectedfontlist) || -1 === $selectedfontlist}1{else}0{/if}{literal},
						boxLabel: "{/literal}{#str_LabelAll#}{literal}"
					}),
					new Ext.form.Radio(
					{
						name: 'fontlistselection',
						value: 0,
						id: 'useselectedfonts',
						checked: {/literal}{if !is_null($selectedfontlist) && -1 !== $selectedfontlist}1{else}0{/if}{literal},
						boxLabel: "{/literal}{#str_TitleFontList#}{literal}"
					})
				],
				listeners: {
					change: function() {
						var list = Ext.getCmp('fontlist');
						if (1 === this.getValue().value) {
							list.setDisabled(true);
						} else {
							list.setDisabled(false).setValue({/literal}{if !is_null($selectedfontlist) && -1 !== $selectedfontlist}{$selectedfontlist}{else}null{/if}{literal});
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
					disabled: {/literal}{if is_null($selectedfontlist) || -1 === $selectedfontlist}true{else}false{/if}{literal},
					value: {/literal}{if is_null($selectedfontlist) || -1 === $selectedfontlist}null{else}{$selectedfontlist}{/if}{literal},
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
						data: {/literal}{$fontlists}{literal}
					}),
					triggerAction: 'all',
					validationEvent: false
				})
			}
		]
	};
    {/literal}{/if}{literal}

	function unsubscribeAllCustomersConfirmation()
	{
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_MessageUnsubscribeAllCustomersConfirmation#}{literal}", unsubscribeAllCustomers);
    };

	function unsubscribeAllCustomers(btn)
	{
		if (btn == "yes")
		{
			Ext.getCmp('unsubscribeall').disable();
			Ext.getCmp('unsubscribeallwarning').disable();
			Ext.getCmp('unsubscribeall').setTooltip("{/literal}{#str_LabelUnsubscribeTaskInProgress#}{literal}");

			var paramArray = {
			{/literal}{if $brandingid > 0 and $code == ''}{literal}
				'code': ''
			{/literal}{else}{literal}
				'code': Ext.getCmp('code').getValue()
			{/literal}{/if}{literal}
			};

			Ext.taopix.formPost(Ext.getCmp('mainform'), paramArray, 'index.php?fsaction=AdminBranding.unsubscribeAllUsers', "{/literal}{#str_MessageUpdating#}{literal}", onUnsubscribeAllCallback);
		}
	};

	var accountPagesURLPanel =
	{
		xtype: 'fieldset',
		columnWidth: 0.5,
		title: "{/literal}{#str_LabelAccountPagesURL#}{literal}",
		collapsible: false,
		autoHeight: true,
		columns: 1,
		defaultType: 'textfield',
		items:[
			{
				xtype: 'radiogroup',
				columns: 1,
				id: 'accountpagesurltype',
				name: 'accountpagesurltype',
				hideLabel: true,
				autoWidth:true,
				items: [
					{
						boxLabel: "{/literal}{#str_LabelDisplayURL#}{literal}",
						name: 'defaultaccountpagesurl',
						id:'usedefaultaccountpagesurl',
						inputValue: 1,
						checked: {/literal}{$usedefaultaccountpagesurl}{literal},
						listeners: {
							'check': refreshDesktopAccountPagesURlSettings
						}
					},
					{
						xtype : 'container',
						border : false,
						layout : 'column',
						autoHeight:true,
						items : [
							{
								xtype : 'container',
								layout : 'form',
								width: 160,
								items : [
									{
										xtype: 'radio',
										boxLabel: "{/literal}{#str_LabelCustomURL#}{literal}",
										hideLabel: false,
										id:'usecustomaccountpagesurl',
										name: 'defaultaccountpagesurl',
										inputValue: 0,
										checked: ! ({/literal}{$usedefaultaccountpagesurl}{literal})
									}
								]
							},
							{
								xtype : 'container',
								layout : 'form',
								width: 505,
								items : [
									{
										xtype: 'textfield',
										id: 'customaccountpagesurl',
										name: 'customaccountpagesurl',
										width: 505,
										value: customAccountPagesURL,
										validateOnBlur: true,
										post: true,
										validator: function(v)
										{
											// check that our URL isn't blank or just the scheme
											if ((v != 'http://') && (v != 'https://') && (v != ''))
											{
												return validateUrl(this);
											}
											else
											{
												return false;
											}
										}
									}
								]
							}
						]
					}
				]
			}
		]
	}

	var desktopDesignerSettingsTab =
	{
		title: "{/literal}{#str_LabelDesktopDesignerSettings#}{literal}",
		id: 'desktopDesignerSettingsTab',
		items:
		[
			new Ext.Panel(
				{
					layout: 'form',
					width: 928,
					items:
					[
						accountPagesURLPanel,
					]
				})
		]
	}

	var customiseTab =
	{
		title: "{/literal}{#str_LabelCustomise#}{literal}",
		id: 'customiseTab',
		hideMode: 'offsets',
		items:
		[
			new Ext.form.FieldSet({
		        xtype: 'fieldset',
		        title: "{/literal}{#str_LabelCustomiseOptions#}{literal}",
		        collapsible: false,
		        autoHeight: true,
				labelWidth: 320,
				style: 'position: relative;',
				defaults: {xtype: 'textfield'},
				layout: 'form',
		        items :
		        [
					{/literal}{if $hasonlinedesigner == 1}{literal}
					brandUpdateOption({/literal}{$onlineLogoType}{literal}, "{/literal}{#str_LabelOnlineDesignerLogo#}{literal}"),
					brandUpdateOption({/literal}{$onlineLogoTypeDark}{literal}, "{/literal}{#str_LabelOnlineDesignerLogoDark#}{literal}"),
					{/literal}{/if}{literal}
					brandUpdateOption({/literal}{$controlLogoType}{literal}, "{/literal}{#str_LabelCustomerAccountLogo#}{literal}"),
					brandUpdateOption({/literal}{$marketingType}{literal}, "{/literal}{#str_LabelCustomerAccountSidebar#}{literal}"),
					brandUpdateOption({/literal}{$emailLogoType}{literal}, "{/literal}{#str_LabelEmailLogo#}{literal}")
				]
			}),
			new Ext.form.FieldSet({
		        xtype: 'fieldset',
		        title: '{/literal}{#str_LabelCustomiseTextOptions#}{literal}',
		        collapsible: false,
		        autoHeight: true,
				labelWidth: 320,
				style: 'position: relative;',
				defaults: {xtype: 'textfield'},
				layout: 'form',
		        items :
		        [
					brandTextOption('emailSignature', "{/literal}{#str_LabelEmailSignatureText#}{literal}", "{/literal}{#str_LabelEmailSignatureDescription#}{literal}")
				]
			})
		]
	};

	function onUploadLogo(pType)
	{
		var theForm = Ext.getCmp('uploadform').getForm();
		theForm.submit({
			scope: this,
			success: function(form, action)
			{
				Ext.getCmp('uploaddialog').close();

				var tmpFile = action.result.tempfilepath;
				var d = new Date();
				var comp = 'previewimage-' + pType;
				if (action.result.msg != '')
				{
					Ext.MessageBox.show({ title: "{/literal}{#str_TitleWarning#}{literal}", msg: action.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				}

				brandFileArray[pType] = {'action': 'update', 'path': tmpFile};

				// Update the workingBrandAssetData, a non 0 value prevents the upload request message being displayed.
				workingBrandAssetData[pType] = 1;

				// Update the brand asset preview.
				updatePreviewDisplay(pType);
			},
			failure: function(form, action)
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorCompulsoryFields#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
			},
			waitMsg: {/literal}"{#str_AlertUploading#}"{literal}
		});
	};

	function uploadLogo(pOption)
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
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleWarning#}{literal}", msg: "{/literal}{#str_MessageBrandFileTypes#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				return;
			}
		}
		onUploadLogo(pOption);
	};

	function createUploadDialog(pOption)
	{
		var uploadFormPanelObj = new Ext.FormPanel(
		{
			   id: 'uploadform',
			   frame: true,
			   autoWidth: true,
			   autoHeight: true,
			   layout: 'column',
			   bodyBorder: false,
			   border: false,
			   url: './?fsaction=AdminBranding.uploadBrandFile&ref={/literal}{$ref}{literal}',
			   method: 'POST',
			   fileUpload: true,
			   baseParams: {csrf_token: Ext.taopix.getCSRFToken(), typeref: pOption},
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
					   text: "{/literal}{#str_ButtonUpload#}{literal}",
					   handler:
					   function()
					   {
							uploadLogo(pOption);
						}
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
	};

	function onRemoveLogo(pOption)
	{
		brandFileArray[pOption] = {'action': 'remove'};

		// Update the working state of the images, setting the display to the please upload message.
		workingBrandAssetData[pOption] = 0;

		// Enable the reset to last saved button.
		Ext.getCmp('reset_' + pOption).enable();

		updatePreviewDisplay(pOption);
	};

	function onResetLogo(pOption)
	{
		delete brandFileArray[pOption];

		// Restore the working state of the image to the last saved state.
		workingBrandAssetData[pOption] = lastSavedBrandAssetData[pOption];

		// Disable the reset to last saved button.
		Ext.getCmp('reset_' + pOption).disable();

		updatePreviewDisplay(pOption);
	};

	function validateFileExtension(fileName)
	{
		var exp = /^.*\.(png|jpg|jpeg)$/;
		return exp.test(fileName);
	}

	function brandUpdateOption(pOption, pLabel)
	{
		var recArray = {/literal}{$recommended}{literal};
		var maxArray = {/literal}{$maximums}{literal};
		var imageUploaded = workingBrandAssetData[pOption];

		var d = new Date();
		var gDate = d.getTime();
		var brandOption = new Ext.Panel({
			xtype: 'panel',
			height: 170,
			style: 'position: relative; padding: 10px',
			layout: 'hbox',
			items:
			[
				{
					xtype: 'panel',
					width: 250,
					layout: 'form',
					items:
					[
						{
							autoEl:
							{
								tag: 'div',
								html: pLabel,
								style: { 'margin-bottom': '8px' }
							}
						},
						{
							autoEl:
							{
								tag: 'div',
								html: recArray[pOption],
								style: { 'color': '#A1A1A1', 'margin-bottom': '2px' }
							}
						},
						{
							autoEl:
							{
								tag: 'div',
								html: maxArray[pOption],
								style: { 'color': '#A1A1A1' }
							}
						}
					]
				},
				{
					xtype: 'panel',
					width: 150,
					height: 150,
					layout: 'form',
					id: 'previewbox-' + pOption,
					items:
					[
						{
							xtype: 'box',
							id: 'prev-disp-' + pOption,
							autoEl:
							{
								tag: 'div',
								style: { 'border': '1px solid', 'width': '140px', 'height': '140px', 'text-align': 'center', 'vertical-align': 'middle', 'display': 'table-cell' },
								html: ''
							},
							listeners: {
								render: function(pComponent)
								{
									pComponent.getEl().on('click', function(e)
									{
										if (workingBrandAssetData[pOption] != 0)
										{
											showPreview(pOption);
										}
									});
								}
							}
						}
					]
				},
				{
					xtype: 'spacer',
					width: 10
				},
				{
					xtype: 'panel',
					height: 150,
					layout: 'vbox',
					items:
					[
						{
							xtype: 'buttongroup',
							frame: false,
							columns: 1,
							items:
							[
								{
									cls: 'brandingCustomiseButton',
									text: "{/literal}{#str_ButtonUpdateNewBrandImage#}{literal}",
									handler: function()
									{
										createUploadDialog(pOption);
										Ext.getDom('preview').value = '';
									}
								},
								{
									xtype: 'spacer',
									height: 8
								},
								{
									text: "{/literal}{#str_ButtonResetToSavedBrandImage#}{literal}",
									id: 'reset_' + pOption,
									disabled: true,
									cls: 'brandingCustomiseButton',
									handler: function()
									{
										onResetLogo(pOption);
									}
								},
								{
									xtype: 'spacer',
									height: 8
								},
								{
									text: "{/literal}{#str_ButtonRemoveBrandImage#}{literal}",
									id: 'remove_' + pOption,
									cls: 'brandingCustomiseButton',
									handler: function()
									{
										onRemoveLogo(pOption);
									}
								}
							]
						}
					]
				}
			]
		});

		return brandOption;
	};


	function brandTextOption(pOption, pLabel, pDescription)
	{
		var d = new Date();
		var gDate = d.getTime();
		var brandOption = new Ext.Panel({
			xtype: 'panel',
			height: 340,
			style: 'position: relative; padding: 10px',
			layout: 'hbox',
			items:
			[
				{
					xtype: 'panel',
					width: 250,
					layout: 'form',
					items:
					[
						{
							xtype: 'panel',
							autoEl:
							{
								tag: 'div',
								html: pLabel,
								style: { 'margin-bottom': '8px' }
							}
						},
						{
							xtype: 'panel',
							width: 190,
							autoEl:
							{
								tag: 'div',
								html: pDescription,
								style: { 'color': '#A1A1A1', 'margin-bottom': '2px' }
							}
						}
					]
				},
				{
					xtype: 'panel',
					width: 590,
					layout: 'form',
					items:
					[
						enableCustomTextCheck(pOption, 101, "{/literal}{#str_LabelEnableCustomEmailSignature#}{literal}"),
						useDefaultTextCheck(pOption, 101, "{/literal}{#str_LabelEmailSignatureUseDefault#}{literal}"),
						generateCustomTextPanel(pOption, 101)
					]
				}
			]
		});

		return brandOption;
	};

	function getLocalisedStringData(pLanguageString)
	{
		var langStringVals = pLanguageString.split('<p>');

		var langListStore = [];
		var dataList = [];
		var translatedCodeArray = [];

		// Populate the list of translated strings.
		for (var i = 0; i < langStringVals.length; i++)
		{
			var langText = '';
			var langCode = '';
			var languageName = '';
			var languageNameIndex = -1;
			var langComponents = langStringVals[i].split(' ');

			if (langComponents.length > 0)
			{
				langCode = langComponents[0];
				languageNameIndex = ArrayIndexOf(gAllLanguageCodesArray, langComponents.shift());
				if (languageNameIndex > -1)
				{
					languageName = gAllLanguageNamesArray[languageNameIndex];
					langText = langComponents.join(' ');

					dataList.push([langCode, languageName, langText]);
					translatedCodeArray.push(langCode);
				}
			}
		}

		// Populate a list of languages which have no translations.
		for (var i = 0; i < gAllLanguageCodesArray.length; i++)
		{
			if (ArrayIndexOf(translatedCodeArray, gAllLanguageCodesArray[i]) == -1)
			{
				langListStore.push([gAllLanguageCodesArray[i],gAllLanguageNamesArray[i]]);
			}
		}

		return {'langList': langListStore, 'dataList': dataList};
	};


	function enableCustomTextCheck(pOption, pTypeCode, pLabelText)
	{
		var useDefault = brandTextUseDefaultsArray[pTypeCode];
		var customTextEnabled = brandTextEnabledArray[pTypeCode];

		var langDefaultCheck = new Ext.form.Checkbox(
		{
			xtype: 'checkbox',
			name: pOption + 'enablecheck',
			id: pOption + 'enablecheck',
			hideLabel: true,
			boxLabel: pLabelText,
			ctCls: 'multilinelangcheckbox',
			listeners:
			{
				'check': function(pCheckBox, checked)
				{
					brandTextEnabledArray[pTypeCode] = (checked) ? 1 : 0;

					setCustomTextElementsActive(pOption, pTypeCode);
				}
			},
			checked: (brandTextEnabledArray[pTypeCode] == 1)
		});

		return langDefaultCheck;
	}

	function useDefaultTextCheck(pOption, pTypeCode, pLabelText)
	{
		var useDefault = brandTextUseDefaultsArray[pTypeCode];
		var customTextEnabled = brandTextEnabledArray[pTypeCode];

		var langDefaultCheck = new Ext.form.Checkbox(
		{
			xtype: 'checkbox',
			name: pOption + 'usedefaultcheck',
			id: pOption + 'usedefaultcheck',
			hideLabel: true,
			boxLabel: pLabelText,
			ctCls: 'multilinelangcheckbox',
			disabled: (! customTextEnabled),
			listeners:
			{
				'check': function(pCheckBox, checked)
				{
					brandTextUseDefaultsArray[pTypeCode] = (checked) ? 1 : 0;

					// Update the display displayed in the language grid.
					var customGridID = pOption + 'langpanel';
					var langGridComponent = Ext.getCmp(customGridID);

					var dataToDisplay = (checked) ? defaultBrandTextArray[pTypeCode] : brandTextArray[pTypeCode];

					langGridComponent.loadData(getLocalisedStringData(dataToDisplay));

					// Enable / Disable the form elements.
					setCustomTextElementsActive(pOption, pTypeCode);
				}
			},
			{/literal}{if $brandingid == 0 || ($brandingid > 0 && $code != '')}{literal}
				checked: (useDefault == 1)
			{/literal}{else}{literal}
				checked: false,
				hidden: true
			{/literal}{/if}{literal}
		});

		return langDefaultCheck;
	}

	function setCustomTextElementsActive(pOption, pTypeCode)
	{
		// Set the use default brand custom text check box and language grid to be active based on the combination of check boxes.
		var customTextEnabled = (brandTextEnabledArray[pTypeCode] == 1);
		var useDefault = (brandTextUseDefaultsArray[pTypeCode] == 1);

		var useDefaultCheckID = pOption + 'usedefaultcheck';
		var langGridContainerID = pOption + 'container';

		var useDefaultCheckComponent = Ext.getCmp(useDefaultCheckID);
		var langGridComponent = Ext.getCmp(langGridContainerID);

		if (customTextEnabled)
		{
			useDefaultCheckComponent.enable();
			if (useDefault)
			{
				langGridComponent.disable();
			}
			else
			{
				langGridComponent.enable();
			}
		}
		else
		{
			useDefaultCheckComponent.disable();
			langGridComponent.disable();
		}
	}

	function isLangPanelDisabled(pTypeCode)
	{
		var gridDiabled = false;

		var useDefault = (brandTextUseDefaultsArray[pTypeCode] == 1);
		var customTextEnabled = (brandTextEnabledArray[pTypeCode] == 1);

		// Default Brand - Grid disabled if custom sig not checked, no use default.
		// New Brand - Grid will be disabled, custom sig not checked, use default not checked.
		// Other Brand - Grid disabled if custom sig not checked or use default checked.
		//			   - Enabled only if custom sig checked and use default not checked.

		{/literal}{if (($brandingid == 0) || ($brandingid > 0 && $code != ''))}{literal}
			// Existing, not default, brand, disable grid if custom signature is disabled or use default is checked.
			gridDiabled = (customTextEnabled) ? useDefault : true;
		{/literal}{else}{literal}
			// Default Brand, disable grid if custom signature is disabled.
			gridDiabled = (! customTextEnabled);
		{/literal}{/if}{literal}

		return gridDiabled;
	}

	function generateCustomTextPanel(pOption, pTypeCode)
	{
		var localisedData = getLocalisedStringData(brandTextArray[pTypeCode]);

		var disableLangPanel = isLangPanelDisabled(pTypeCode);

		var langInputPanel = new Ext.taopix.MultiLineLangPanel(
		{
			id: pOption + 'langpanel',
			name: pOption + 'langpanel',
			height: 260,
			width: 570,
			post: true,
			style: 'border:1px solid #b4b8c8',
			data: localisedData,
			settings:
			{
				headers: {langLabel: "{/literal}{#str_LabelLanguageName#}{literal}",  textLabel: "{/literal}{#str_LabelName#}{literal}", deletePic: deleteImg, addPic: addimg},
				defaultText: {langBlank: "{/literal}{#str_LabelSelectLanguage#}{literal}",  textBlank: "{/literal}{#str_ExtJsTypeValue#}{literal}", defaultValue: "{/literal}{$defaultlanguagecode}{literal}"},
				columnWidth: {langCol: 200, textCol: 307, delCol: 35},
				fieldWidth: {langField: 190, textField: 286},
				errorMsg: {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"}
			}
		});

		var langPanel = new Ext.Panel(
		{
			id: pOption + 'container',
			name: pOption + 'container',
			height: 260,
			width: 570,
			disabled: disableLangPanel,
			cls: 'multilinetoolbar',
			items:
			[
				langInputPanel
			]
		});

		return langPanel;
	}

	var tabPanel = {
		xtype: 'tabpanel',
		id: 'maintabpanel',
		deferredRender: false,
		enableTabScroll:true,
		activeTab: 0,
		width: 950,
		height: 450,
		shadow: true,
		plain:true,
		bodyBorder: false,
		border: false,
		style:'margin-top:6px; ',
		bodyStyle:'border-right: 1px solid #96bde7; border-left: 1px solid #96bde7; ',
		defaults:{frame: false, autoScroll: true, hideMode:'offsets', layout: 'form', labelWidth: 230, bodyStyle:'padding:5px 10px 0 10px; border-top: 0px; background-color: #eaf0f8;'},
		items:
		[
			{
				title: "{/literal}{#str_LabelSettings#}{literal}",
				defaults:{xtype: 'textfield'},
				items:
				[
					{/literal}{if $brandingid == 0 or ($brandingid > 0 and $code != '')}{literal}
					{
						xtype: 'textfield',
						id: 'name',
						name: 'name',
                        width: 605,
						fieldLabel: "{/literal}{#str_LabelFolderName#}{literal}",
						listeners:{
							blur:{
								fn: validateFolder
							}
						},
						post: true,
						allowBlank: false,
						maxLength: 50
						{/literal}{if $brandingid != 0}{literal}
						,
						readOnly: true,
						value: "{/literal}{$name}{literal}"
						{/literal}{/if}{literal}
					},
					{/literal}{/if}{literal}
					{
						xtype: 'textfield',
						id: 'applicationname',
						name: 'applicationname',
                        width: 605,
						fieldLabel: "{/literal}{#str_LabelApplicationName#}{literal}",
						{/literal}{if $brandingid != 0}{literal}
						value: "{/literal}{$applicationname}{literal}",
						{/literal}{/if}{literal}
						validateOnBlur: true,
						post: true,
						allowBlank: false
					},
					{
						xtype: 'textfield',
						id: 'displayurl',
						name: 'displayurl',
                        width: 605,
						fieldLabel: "{/literal}{#str_LabelDisplayURL#}{literal}",
						{/literal}{if $brandingid != 0}{literal}
						value: "{/literal}{$displayurl}{literal}",
						{/literal}{/if}{literal}
						validateOnBlur: true,
						validator: function(v){ return validateUrl(this);  }
					},
					{
						xtype: 'textfield',
						id: 'weburl',
						name: 'weburl',
                        width: 605,
						fieldLabel: "{/literal}{#str_LabelWebURL#}{literal}",
						{/literal}{if $brandingid != 0}{literal}
						value: "{/literal}{$weburl}{literal}",
						{/literal}{/if}{literal}
						validateOnBlur: true,
						validator: function(v){ return validateUrl(this); }
					},
					{
						xtype: 'textfield',
						id: 'mainwebsiteurl',
						name: 'mainwebsiteurl',
                        width: 605,
						fieldLabel: "{/literal}{#str_LabelMainWebsiteURL#}{literal}",
						{/literal}{if $brandingid != 0}{literal}
						value: "{/literal}{$mainwebsiteurl}{literal}",
						{/literal}{/if}{literal}
						validateOnBlur: true,
						post: true,
						validator: function(v){ return validateUrl(this); }
					},
					{
						xtype: 'textfield',
						id: 'macdownloadurl',
						name: 'macdownloadurl',
                        width: 605,
						fieldLabel: "{/literal}{#str_LabelMacDownloadLink#}{literal}",
						{/literal}{if $brandingid != 0}{literal}
						value: "{/literal}{$macdownloadurl}{literal}",
						{/literal}{/if}{literal}
						validateOnBlur: true,
						post: true,
						validator: function(v){ return validateUrl(this); }
					},
					{
						xtype: 'textfield',
						id: 'win32downloadurl',
						name: 'win32downloadurl',
                        width: 605,
						fieldLabel: "{/literal}{#str_LabelWin32DownloadLink#}{literal}",
						{/literal}{if $brandingid != 0}{literal}
						value: "{/literal}{$win32downloadurl}{literal}",
						{/literal}{/if}{literal}
						validateOnBlur: true,
						post: true,
						validator: function(v){ return validateUrl(this); }
					},
					{
						xtype: 'textfield',
						id: 'supporttelephonenumber',
						name: 'supporttelephonenumber',
                        width: 605,
						fieldLabel: "{/literal}{#str_LabelSupportTelephone#}{literal}",
						{/literal}{if $brandingid != 0}{literal}
						value: "{/literal}{$supporttelephonenumber}{literal}",
						{/literal}{/if}{literal}
						validateOnBlur: true,
						listeners: {'blur': {fn: function(obj){CJKHalfWidthFullWidthToASCII(obj.getEl().dom, false)}}},
						post: true

					},
					{
						xtype: 'textfield',
						id: 'supportemailaddress',
						name: 'supportemailaddress',
                        width: 605,
						fieldLabel: "{/literal}{#str_LabelSupportEmail#}{literal}",
						{/literal}{if $brandingid != 0}{literal}
						value: "{/literal}{$supportemailaddress}{literal}",
						{/literal}{/if}{literal}
						validateOnBlur: true,
						vtype: 'email',
						post: true,
						validator: function(v){ return validateUrl(this); }
					},
                    new Ext.form.ComboBox({
						id: 'defaultcommunicationpreference',
						name: 'defaultcommunicationpreference',
						mode: 'local',
						editable: false,
						forceSelection: true,
						selectOnFocus: true,
						triggerAction: 'all',
						fieldLabel: "{/literal}{#str_LabelDefaultCommunicationPreference#}{literal}",
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data:
							[
                                [1, "{/literal}{#str_LabelSubscribed#}{literal}"],
                                [0, "{/literal}{#str_LabelUnsubscribed#}{literal}"]
							]
						}),
						valueField: 'id',
						displayField: 'name',
                        width: 130,
						useID: true,
						value: "{/literal}{$defaultcommunicationpreference}{literal}",
						post: true
					}),
					{/literal}{if $brandingid > 0}{literal}
					new Ext.Button({
						id: 'unsubscribeall',
						name: 'unsubscribeall',
						text: '{/literal}{#str_LabelUnsubscribeAllCustomers#}{literal}',
						minWidth: 100,
						disabled: hasMassUnsubscribeTaskRunning,
						fieldLabel: '{/literal}{#str_LabelUnsubscribeAll#}{literal}',
						listeners: { click: unsubscribeAllCustomersConfirmation },
						tooltip: str_massUnsubscribeTaskInProgress
					}),
					new Ext.Container(
					{
						id: 'unsubscribeallwarning',
						name: 'unsubscribeallwarning',
						disabled: hasMassUnsubscribeTaskRunning,
						html: "{/literal}{#str_LabelMassUnsubscribeWarning#}{literal}",
						style: { margin: '5px 0px 5px 233px' },
						width: 605
					}),
					{/literal}{/if}{literal}
					{/literal}{if ($optionms) && ($owner == '')}{literal}
					new Ext.form.ComboBox({
						id: 'productionsitelist',
						name: 'productionsitelist',
						mode: 'local',
						editable: false,
						forceSelection: true,
						selectOnFocus: true,
						triggerAction: 'all',
						fieldLabel: "{/literal}{#str_LabelProductionSite#}{literal}",
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data:
							[
								{/literal}
								{section name=index loop=$productionsites}
									{if $smarty.section.index.last}
										["{$productionsites[index].id}", "{$productionsites[index].name}"]
									{else}
										["{$productionsites[index].id}", "{$productionsites[index].name}"],
									{/if}
								{/section}
								{literal}
							]
						}),
						valueField: 'id',
						displayField: 'name',
						useID: true,
						value: "{/literal}{$productionSitesSelected}{literal}",
						post: true,
						listeners:
						{
							'select': function(combo, record, index)
							{
								Ext.MessageBox.show({ title: "{/literal}{#str_TitleWarning#}{literal}", msg: "{/literal}{#str_MessageProductionSiteChanged#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.WARNING });
	    					}
						}
					}),
					{/literal}{/if}{literal}
					new Ext.form.ComboBox({
						id: 'registerusingemail',
						name: 'registerusingemail',
						mode: 'local',
						editable: false,
						forceSelection: true,
						selectOnFocus: true,
						triggerAction: 'all',
						fieldLabel: "{/literal}{#str_LabelRegisterUsingEmail#}{literal}",
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data:
							[
								{/literal}
								{section name=index loop=$registerwithemailoptions}
									{if $smarty.section.index.last}
										["{$registerwithemailoptions[index].id}", "{$registerwithemailoptions[index].name}"]
									{else}
										["{$registerwithemailoptions[index].id}", "{$registerwithemailoptions[index].name}"],
									{/if}
								{/section}
								{literal}
							]
						}),
						valueField: 'id',
						displayField: 'name',
						useID: true,
						value: "{/literal}{$registerusingselected}{literal}",
						post: true
					})
                ]
			},
			{
				title: "{/literal}{#str_PaymentSettings#}{literal}",
				defaults: {xtype: 'textfield'},
				items:
				[
					{
						xtype: 'checkboxgroup',
						columns: 1,
						fieldLabel: "{/literal}{#str_LabelPaymentMethods#}{literal}",
						autoWidth:true,
						style: 'margin-bottom: 10px',
						items:
						[
							{/literal}{if $brandingid == 0 or ($brandingid > 0 and $code != '')}{literal}
							{
								boxLabel: "{/literal}{#str_LabelUseDefaultPaymentMethods#}{literal}",
								name: "usedefaultpaymentmethods",
								id:"usedefaultpaymentmethods",
								checked: {/literal}{$usedefaultpaymentmethodschecked}{literal},
								listeners: {'check': setPaymentMethods }
							},
							{/literal}{/if}{literal}

							{/literal}{section name=index loop=$paymentmethodslist}{literal}
								{/literal}{if $smarty.section.index.last}{literal}
									{boxLabel: "{/literal}{$paymentmethodslist[index].text}{literal}", name: "{/literal}{$paymentmethodslist[index].id}{literal}", id:"{/literal}{$paymentmethodslist[index].id}{literal}", inputValue: "{/literal}{$paymentmethodslist[index].value}{literal}", checked: {/literal}{$paymentmethodslist[index].selected}{literal} }
								{/literal}{else}{literal}
									{boxLabel: "{/literal}{$paymentmethodslist[index].text}{literal}", name: "{/literal}{$paymentmethodslist[index].id}{literal}", id:"{/literal}{$paymentmethodslist[index].id}{literal}", inputValue: "{/literal}{$paymentmethodslist[index].value}{literal}", checked: {/literal}{$paymentmethodslist[index].selected}{literal} },
								{/literal}{/if}{literal}
							{/literal}{/section}{literal}
						]
					},
					{
						xtype: 'radiogroup',
						columns: 1,
						fieldLabel: "{/literal}{#str_LabelPaymentIntegrations#}{literal}",
						autoWidth:true,
						items:
						[
    						{
    							boxLabel: "{/literal}{#str_LabelNone#}{literal}",
    							name: 'integration',
    							id:'integrationNone',
    							inputValue: 'N',
    							listeners: {'check': setPaymentIntegration}
    						},
							{/literal}{if $brandingid == 0 or ($brandingid > 0 and $code != '')}{literal}
							{
								boxLabel: "{/literal}{#str_LabelDefault#} ({$defaultintegration}){literal}",
								name: 'integration',
								id:'integrationDefault',
								inputValue: 'D',
								listeners: {'check': setPaymentIntegration}
							},
							{/literal}{/if}{literal}
							{
								xtype : 'container',
								border : false,
								layout : 'column',
								autoHeight:true,
								items :
								[
									{
										xtype : 'container',
										layout : 'form',
										width: 20,
										items :
										[
											{
												xtype: 'radio',
												boxLabel: " ",
												hideLabel:true,
												name: 'integration',
												id:'integrationCustom',
												inputValue: 'C',
												listeners: {'check': setPaymentIntegration}
											}
										]
									},
									{
										xtype : 'container',
										layout : 'form',
										width:340,
										items :
										[
											new Ext.form.ComboBox({
												id: 'integrationlist',
												name: 'integrationlist',
												mode: 'local',
												editable: false,
												forceSelection: true,
												selectOnFocus: true,
												triggerAction: 'all',
												hideLabel: true,
												store: new Ext.data.ArrayStore({
													id: 0,
													fields: ['id', 'name'],
													data: [
														{/literal}
														{section name=index loop=$integrationlist}
														{if $smarty.section.index.last}
															["{$integrationlist[index].id}", "{$integrationlist[index].name}"]
														{else}
															["{$integrationlist[index].id}", "{$integrationlist[index].name}"],
														{/if}
														{/section}
														{literal}
													]
												}),
												valueField: 'id',
												displayField: 'name',
												useID: true,
												{/literal}{if $brandingid == 0}{literal}
													value: "{/literal}{$defaultintegration}{literal}",
												{/literal}{else}{literal}
													value: "{/literal}{$currentintegration}{literal}",
												{/literal}{/if}{literal}
												post: true,
												width: 250
											})
										]
									}
								]
							}
						]
					},
                    {
                        fieldLabel: "{/literal}{#str_LabelTitleVouchersGiftCards#}{literal}",
						autoWidth:true,
                        xtype: 'checkbox',
                        id: 'allowvouchers',
                        name: 'allowvouchers',
                        columns: 1,
                        boxLabel: "{/literal}{#str_LabelAllowVouchers#}{literal}",
                        {/literal}{if $allowvouchers == 1}{literal}
                            checked: true
                        {/literal}{else}{literal}
                            checked: false
                        {/literal}{/if}{literal}
                    },
                    {
                        xtype: 'checkbox',
                        id: 'allowgiftcards',
                        name: 'allowgiftcards',
                        columns: 1,
                        boxLabel: "{/literal}{#str_LabelAllowGiftCards#}{literal}",
                        {/literal}{if $allowgiftcards == 1}{literal}
                            checked: true
                        {/literal}{else}{literal}
                            checked: false
                        {/literal}{/if}{literal}
                    }
				]
			},
			{
				title: "{/literal}{#str_LabelBrandEmailSettings#}{literal}",
				defaults: {xtype: 'textfield', width: 300, labelWidth: 100},
				items:
				[
					{/literal}{if $brandingid == 0 or ($brandingid > 0 and $code != '')}{literal}
					{
						xtype: 'checkbox',
						name: 'usedefaultemailsettings',
						id: 'usedefaultemailsettings',
						hideLabel: true,
						boxLabel: "{/literal}{#str_LabelUseDefaultEmailSettings#}{literal}",
						listeners:
						{
							'check': function(checkbox, checked)
							{
								if (checked == true)
								{
									/* if user checked use default then if he unchecks it we want to show empty grid */
									clearEmailValues();
								}
								setEmailSettings();
							}
						}
						{/literal}{if $usedefaultemailsettings == 1}, checked: true {/if}{literal}
					},
					{/literal}{/if}{literal}
					{
						xtype: 'panel', layout: 'form', style: 'padding-left: 20px; padding-top: 10px', autoWidth: true,
						defaults: {xtype: 'textfield', width: 300},
						items: [
							{
								xtype: 'container', layout: 'column', width: 750, style: 'margin-bottom: 10px',
								items:
								[
									{
										width: 424,
										xtype: 'panel',
										layout: 'form',
										defaults: {xtype: 'textfield', width: 280},
										items: [
											{
												xtype: 'textfield',
												id: 'smtpaddress',
												name: 'smtpaddress',
												fieldLabel: "{/literal}{#str_LabelSMTPAddress#}{literal}",
												{/literal}{if $brandingid != 0}{literal}
												value: "{/literal}{$smtpaddress}{literal}",
												{/literal}{/if}{literal}
												validateOnBlur: true,
												post: true,
												allowBlank: false
											}
										]
									},
									{
										width: 315,
										xtype: 'panel',
										layout: 'form',
										defaults: {xtype: 'numberfield', width: 80},
										items: [
											{
												xtype: 'numberfield',
												id: 'smtpport',
												name: 'smtpport',
												fieldLabel: "{/literal}{#str_LabelSMTPPort#}{literal}",
												{/literal}{if $brandingid != 0}{literal}
												value: "{/literal}{$smtpport}{literal}",
												{/literal}{/if}{literal}
												validateOnBlur: true,
												post: true,
												allowBlank: false,
												allowNegative: false,
												allowDecimal: false
											}
										]
									}
								]
							},
                            {
                                xtype: 'combo',
                                id: 'smtptype',
                                name: 'smtptype',
                                mode: 'local',
                                editable: false,
                                forceSelection: true,
                                selectOnFocus: true,
                                triggerAction: 'all',
                                fieldLabel: "{/literal}{#str_LabelSMTPType#}{literal}",
                                width: 280,
                                store: new Ext.data.ArrayStore({
                                    id: 0,
                                    fields: ['id', 'name'],
                                    data: [
                                        ["", "{/literal}{#str_LabelNoneSMTPType#}{literal}"],
                                        ["ssl", "{/literal}{#str_LabelSSLSMTPType#}{literal}"],
                                        ["tls", "{/literal}{#str_LabelTLSSMTPType#}{literal}"]
                                    ]
                                }),
                                valueField: 'id',
                                displayField: 'name',
                                useID: true,
                                value: "{/literal}{$smtptype}{literal}",
                                post: true
                            },
							{
								xtype: 'combo',
								name: 'smtpauth',
								id: 'smtpauth',
								mode: 'local',
								editable: false,
								forceSelection: true,
								triggerAction: 'all',
								hideLabel: false,
								fieldLabel: "{/literal}{#str_LabelSMTPAuthentication#}{literal}",
								listeners: { 'select': authenticationContainerDisplay },
								store: new Ext.data.ArrayStore({
									id: 0,
									fields: ['id', 'name'],
									data: [
										[0, "{/literal}{#str_LabelNone#}{literal}"],
										[1, "{/literal}{#str_LabelUserNamePassword#}{literal}"],
										[2, "{/literal}{#str_LabelOAuth#}{literal}"]
									]
								}),
								valueField: 'id',
								displayField: 'name',
								useID: true,
								post: true
							},
							{
								xtype: 'container', layout: 'column', width: 650, style: 'margin-bottom: 10px; margin-left: 110px',
								id: 'userNamePassword',
								name: 'usernamePassword',
								items: [
									{
										width: 315,
										xtype: 'panel',
										layout: 'form',
										defaults: {xtype: 'textfield', width: 170, labelWidth: 90},
										items: [
											{
												xtype: 'textfield',
												id: 'smtpauthuser',
												name: 'smtpauthuser',
												fieldLabel: "{/literal}{#str_LabelUserName#}{literal}",
												{/literal}{if $brandingid != 0}{literal}
												value: "{/literal}{$smtpauthuser}{literal}",
												{/literal}{/if}{literal}
												validateOnBlur: true,
												post: true,
												validator: function(v){ return validateSmtpAuth(this); }
											},
											{
												xtype: 'textfield',
												id: 'smtpauthpass',
												name: 'smtpauthpass',
												inputType: 'password',
												fieldLabel: "{/literal}{#str_LabelPassword#}{literal}",
											{/literal}{if $brandingid != 0}{literal}
												value: "{/literal}{$smtpauthpass}{literal}",
											{/literal}{/if}{literal}
												validateOnBlur: true,
												post: false,
												validator: function(v){ return validateSmtpAuth(this); }
											}
										]
									}
								]
							},
							{
								xtype: 'container', layout: 'column', width: "100%", style: 'margin-bottom: 10px; margin-left: 110px',
								id: 'oauthCredentials',
								name: 'oauthCredentials',
								items: [
									{
										width: '100%',
										xtype: 'panel',
										layout: 'form',
										defaults: {xtype: 'textfield', width: '100%', labelWidth: 90},
										items: [
											{
												xtype: "",
												html: "{/literal}{#str_TipConfigProviders#}{literal}",
												style: "padding: 10px 0px; text-align: left; overflow-x: none;",
												width: '100%'
											},
											new Ext.form.ComboBox({
												id: 'oauthprovider',
												name: 'oauthprovider',
												mode: 'local',
												editable: false,
												forceSelection: true,
												selectOnFocus: true,
												triggerAction: 'all',
												fieldLabel: "{/literal}{#str_LabelOAuthProvider#}{literal}",
												store: new Ext.data.ArrayStore({
													id: 0,
													fields: ['id', 'name'],
													data: {/literal}{$oauthproviders}{literal}
												}),
												valueField: 'id',
												displayField: 'name',
												useID: true,
												{/literal}{if $brandingid == 0}{literal}
												value: "0",
												{/literal}{else}{literal}
												value: "{/literal}{$oauthprovider}{literal}",
												{/literal}{/if}{literal}
												post: true,
												width: 250
											}),
											{
												xtype: 'textfield',
												id: 'oauthrefreshtokenid',
												name: 'oauthrefreshtokenid',
												inputType: 'password',
												value: "{/literal}{$oauthtokenid}{literal}",
												post: true,
												hidden: true,
												width: 170
											},
											{
												xtype: 'textfield',
												id: 'oauthrefreshtoken',
												name: 'oauthrefreshtoken',
												inputType: 'password',
												fieldLabel: "{/literal}{#str_LabelOAuthToken#}{literal}",
												value: "{/literal}{$oauthtoken}{literal}",
												post: false,
												disabled: true,
												width: 175
											},
											{
												xtype: 'button',
												id: 'oauthauthenticate',
												name: 'oauthauthenticate',
												text: "{/literal}{#str_ButtonAuthenticate#}{literal}",
												post: false,
												handler: getAuthentication,
												width: 280
											}
										]
									}
								]
							},
							{
            					xtype: 'fieldset',
            					title: "{/literal}{#str_LabelFrom#}:{literal}",
            					autoHeight:true,
            					autoWidth: true,
            					border: false,
            					padding: '0 0 0 20px',
            					defaults: {width: 485},
            					defaultType: 'textfield', style: 'padding: 3px 0',
            					items :
            					[
         							{
										xtype: 'textfield',
										id: 'smtpsysfromname',
										name: 'smtpsysfromname',
										fieldLabel: "{/literal}{#str_LabelName#}{literal}",
										{/literal}{if $brandingid != 0}{literal}
										value: "{/literal}{$smtpsysfromname}{literal}",
										{/literal}{/if}{literal}
										validateOnBlur: true,
										post: true,
										allowBlank: false
									},
									{
										xtype: 'textfield',
										id: 'smtpsysfromaddress',
										name: 'smtpsysfromaddress',
										fieldLabel: "{/literal}{#str_LabelEmailAddress#}{literal}",
										{/literal}{if $brandingid != 0}{literal}
										value: "{/literal}{$smtpsysfromaddress}{literal}",
										{/literal}{/if}{literal}
										validateOnBlur: true,
										post: true,
										allowBlank: false,
										vtype: 'email'
									}
         						]
        					},

        					{
            					xtype: 'fieldset',
            					title: "{/literal}{#str_LabelReplyTo#}:{literal}",
            					autoHeight:true, style: 'padding: 3px 0',
            					autoWidth: true,
            					border: false,
            					padding: '0 0 0 20px',
            					defaults: {width: 485},
            					defaultType: 'textfield',
            					items :
            					[
         							{
										xtype: 'textfield',
										id: 'smtpreplyname',
										name: 'smtpreplyname',
										fieldLabel: "{/literal}{#str_LabelName#}{literal}",
										{/literal}{if $brandingid != 0}{literal}
										value: "{/literal}{$smtpreplyname}{literal}",
										{/literal}{/if}{literal}
										validateOnBlur: true,
										post: true,
										allowBlank: false
									},
									{
										xtype: 'textfield',
										id: 'smtpreplyaddress',
										name: 'smtpreplyaddress',
										fieldLabel: "{/literal}{#str_LabelEmailAddress#}{literal}",
										{/literal}{if $brandingid != 0}{literal}
										value: "{/literal}{$smtpreplyaddress}{literal}",
										{/literal}{/if}{literal}
										validateOnBlur: true,
										post: true,
										vtype: 'email',
										allowBlank: false
									}
         						]
        					}
						]
					}
				]
			},
			{
				title: "{/literal}{#str_LabelBrandEmails#}{literal}",
				listeners:
				{
					'show': function()
					{
						setEmailsGridActive();
						Ext.getCmp('emailsGrid').getStore().singleSort('sectionCode', 'ASC');
					},
					'hide': function()
					{
						/* on hide save the status of the checkboxes*/
						if (Ext.getCmp('usedefaultemailsettings') && (!Ext.getCmp('usedefaultemailsettings').checked))
						{
							var activeCheckbox;
							for (sectionCode in emailSections)
							{
								activeCheckbox = document.getElementById('cbx_' + sectionCode);

								if (activeCheckbox)
								{
									emailSections[sectionCode].active = (activeCheckbox.checked) ? 1 : 0;
								}
							}
						}
					}
				},
				items:
				[
					new Ext.grid.EditorGridPanel({
   					 	id: 'emailsGrid',
   					 	ctCls: 'grid',
   					 	style: 'border: 1px solid #b4b8c8',
        				store: new Ext.data.GroupingStore({
    						reader: new Ext.data.ArrayReader({}, [
       							{name: 'id'},
       							{name: 'sectionCode'},
       							{name: 'sectionName'},
       							{name: 'valueName'},
       							{name: 'valueEmail'},
       							{name: 'controlsRow'},
       							{name: 'isEmptyRow'}
    						]),
        					sortInfo:{field: 'sectionCode', direction: "ASC"},
        					groupField:'sectionName'
    					}),
        				columns:
        				[
            				{
            					id:'sectionName',
            					header: "{/literal}{#str_LabelSection#}{literal}",
            					dataIndex: 'sectionName',
            					hidden: true
            				},
           					{
            					id: 'valueName',
            					header: "{/literal}{#str_LabelName#}{literal}",
            					width: 100,
            					sortable: false,
            					dataIndex: 'valueName',
            					editor: new Ext.form.TextField({ validateOnBlur: true }),
            					menuDisabled: true,
            					renderer: columnRenderer
            				},
            				{
            					header: "{/literal}{#str_LabelEmailAddress#}{literal}",
            					width: 100,
            					sortable: false,
            					dataIndex: 'valueEmail',
            					editor: new Ext.form.TextField({ vtype: 'email', validateOnBlur: true }),
            					menuDisabled: true,
            					renderer: columnRenderer
            				},
            				{
            					header: "",
            					width: 13,
            					sortable: false,
            					dataIndex: 'controlsRow',
            					menuDisabled: true,
            					renderer: function(value, p, record, rowIndex, colIndex, store){ return deleteColRenderer(record); }
            				}
        				],
        				view: new Ext.grid.GroupingView({
            				forceFit:true,
            				groupTextTpl: '{[values.rs[0].data.sectionName]} <div class="gridHeaderCheckboxHolder"><span class="gridHeaderCheckbox"><input type="checkbox" class="cbx" id="cbx_{[values.rs[0].data.sectionCode]}" onclick="activeCheckboxClicked();">&nbsp;{/literal}{#str_LabelActive#}{literal}</span><div class="gridHeaderButtons"><div class="gridHeaderButton" onClick="onEmailTestRecord(\'{[values.rs[0].data.sectionCode]}\'); return false;" OnMouseOver="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\') + \' x-btn-over\';" OnMouseOut="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\');"><table cellspacing="0" class="x-btn  x-btn-icon"><tbody class="x-btn-small x-btn-icon-small-left"><tr><td class="x-btn-tl"><i>&nbsp;</i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-ml"><i>&nbsp;</i></td><td class="x-btn-mc"><em unselectable="on" class=""><button type="button" class="x-btn-text " style="background-image: url(&quot;{/literal}{$webroot}{literal}/utils/ext/images/silk/email_go.png&quot;);">&nbsp;</button></em></td><td class="x-btn-mr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-bl"><i>&nbsp;</i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i>&nbsp;</i></td></tr></tbody></table></div><div class="gridHeaderButton" onClick="onAddRecord(\'{[values.rs[0].data.sectionCode]}\'); return false;" OnMouseOver="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\') + \' x-btn-over\';" OnMouseOut="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\');"><table cellspacing="0" class="x-btn  x-btn-icon"><tbody class="x-btn-small x-btn-icon-small-left"><tr><td class="x-btn-tl"><i>&nbsp;</i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-ml"><i>&nbsp;</i></td><td class="x-btn-mc"><em unselectable="on" class=""><button type="button" class="x-btn-text " style="background-image: url(&quot;{/literal}{$webroot}{literal}/utils/ext/images/silk/add.png&quot;);">&nbsp;</button></em></td><td class="x-btn-mr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-bl"><i>&nbsp;</i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i>&nbsp;</i></td></tr></tbody></table></div></div></div>'

        					,listeners: {
        						'beforerefresh': function()
        						{
        							var isDefaultBrand = false;

        							{/literal}{if $brandingid > 0}{literal}
	        							var brandingGrid = Ext.getCmp('brandingGrid');
			        					var records = brandingGrid.selModel.getSelections();

			        					if (records[0].data.code == '')
			        					{
			        						isDefaultBrand = true;
			        					}
		        					{/literal}{/if}{literal}

		        					if ((Ext.getCmp('usedefaultemailsettings') && !Ext.getCmp('usedefaultemailsettings').checked) || (isDefaultBrand == true))
									{
        								var activeCheckbox;
										for (sectionCode in emailSections)
										{
											activeCheckbox = document.getElementById('cbx_' + sectionCode);
											if (activeCheckbox)
											{
												emailSections[sectionCode].active = (activeCheckbox.checked) ? 1 : 0;
											}
										}
									}
        						},
        						'refresh': function()
        						{
        							setEmailsGridActive();
        						}
        					}
        				}),
						clicksToEdit: 1,
						autoExpandColumn: 'valueName',
        				width: 930,
        				height: 415,
        				iconCls: 'icon-grid',
        				stripeRows: true,
    					stateful: true,
    					enableColLock:false,
						draggable:false,
						enableColumnHide:false,
						enableColumnMove:false,
						trackMouseOver:false,
						listeners:
						{
							'afteredit': function(e)
        					{
        						/* if both fields are empty then delete the record
        						   if there is only one record in this section then just stop editing */
        						var store = Ext.getCmp('emailsGrid').getStore();
								e.record.data.valueName = e.record.data.valueName.replace('"', '');

								if ((e.record.data.valueName == '') && (e.record.data.valueEmail == ''))
        						{
        							var sectionRecords = store.query('sectionCode', e.record.data.sectionCode, false, true).items;
									var sectionRecordsCount = sectionRecords.length;

									if (sectionRecordsCount <= 1)
									{
										/* don't delete just stop editing */
										e.record.data.isEmptyRow = 1;
										store.commitChanges();
										store.singleSort('sectionCode', 'ASC');
									}
									else
									{
										/* delete the record */
										store.removeAt(store.findExact('id', e.record.data.id));
										store.commitChanges();
										store.singleSort('sectionCode', 'ASC');
									}
        						}
        						else
        						{
        							/* need to add record to the store */
        							e.record.data.isEmptyRow = 0;
        							store.commitChanges();
									store.singleSort('sectionCode', 'ASC');
	    						}
        					}
						}
    				})
				]
			},
			{
				title: "{/literal}{#str_TitleAdminSharing#}{literal}",
				id: 'pageTurningTab',
				layout: 'form',
				listeners: { 'beforeshow': function(){ Ext.getCmp('maintabpanel').doLayout(); }},
				items:[
                    {
                        xtype:'fieldset',
                        columnWidth: 0.5,
                        title: "{/literal}{#str_TitlePageTurningPreview#}{literal}",
                        collapsible: false,
                        autoHeight:true,
                        defaultType: 'textfield',
                        items :[
                                    {
                                        xtype: 'textfield',
                                        id: 'previewlicensekey',
                                        name: 'previewlicensekey',
                                        fieldLabel: "{/literal}{#str_LabelPageTurningPreviewLicense#}{literal}",
                                        {/literal}{if $brandingid != 0}{literal}
                                            value: "{/literal}{$previewlicensekey}{literal}",
                                        {/literal}{/if}{literal}
                                        validateOnBlur: true,
                                        post: true,
                                        width: 424
                                    },
                                    {
                                        xtype : 'container',
                                        border : false,
                                        layout : 'column',
                                        autoHeight:true,
                                        items :[
                                            {
                                                xtype : 'container',
                                                layout : 'form',
                                                autoWidth: true,
                                                items :
                                                [
                                                    {
                                                        xtype: 'checkbox',
                                                        id: 'previewExpire',
                                                        name: 'previewExpire',
                                                        hideLabel: true,
                                                        boxLabel: "{/literal}{#str_TitlePageTurningPreviewLimit#}{literal}",
                                                        post: true,
                                                        listeners: {
                                                            'check': function(){ showPreviewExpire(); }
                                                        }
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'numberfield',
                                        id: 'previewExpireDays',
                                        name: 'previewExpireDays',
                                        fieldLabel: "{/literal}{#str_LabelNumberOfDays#}{literal}",
                                        {/literal}{if $brandingid != 0}{literal}
                                            value: "{/literal}{$previewexpiredays}{literal}",
                                        {/literal}{else}{literal}
                                            value: 30,
                                        {/literal}{/if}{literal}
                                        minValue: 1,
                                        maxValue: 9999,
                                        validateOnBlur: true,
                                        post: true,
                                        width: 50,
                                        allowBlank: false,
                                        allowNegative: false,
                                        allowDecimal: false
                                    }
                                ]
                    },
                    {
                        xtype:'fieldset',
                        columnWidth: 0.5,
                        title: "{/literal}{#str_TitleShareOrderedProjects#}{literal}",
                        collapsible: false,
                        autoHeight:true,
                        defaultType: 'textfield',
                        items :[
                            new Ext.form.ComboBox({
                                id: 'sharebyemailmethod',
                                name: 'sharebyemailmethod',
                                mode: 'local',
                                editable: false,
                                forceSelection: true,
                                selectOnFocus: true,
                                triggerAction: 'all',
                                fieldLabel: "{/literal}{#str_LabelShareByEmailMethod#}{literal}",
                                store: new Ext.data.ArrayStore({
                                    id: 0,
                                    fields: ['id', 'name'],
                                    data: [
                                        [0, "{/literal}{#str_LabelShareByEmailDisabled#}{literal}"],
                                        [1, "{/literal}{#str_LabelShareByEmailTaopixControlCentre#}{literal}"],
                                        [2, "{/literal}{#str_LabelShareByEmailMailToLink#}{literal}"]
                                    ]
                                }),
                                valueField: 'id',
                                displayField: 'name',
                                useID: true,
                                value: "{/literal}{$sharebyemailmethod}{literal}",
                                post: true
                            }),
                            new Ext.form.CheckboxGroup({
                                columns: 1,
                                width: 700,
                                layout:'column',
                                style:'margin-bottom:10px',
                                hideLabel:true,
                                items: [
                                    new Ext.form.Checkbox({
                                        boxLabel: "{/literal}{#str_LabelOrderFromPreview#}{literal}",
                                        name: 'orderfrompreview',
                                        id: 'orderfrompreview',
                                        hideLabel: true,
                                        {/literal}{if $orderfrompreview == 1}{literal}
                                        checked: true,
                                        {/literal}{else}{literal}
                                        checked: false,
                                        {/literal}{/if}{literal}
                                        post: true
                                    })
                                ]
                            })
                        ]
                    },
					{
						xtype:'fieldset',
						columnWidth: 0.5,
						title: "{/literal}{#str_TitleShareUnorderedProjects#}{literal}",
						collapsible: false,
						autoHeight:true,
						defaultType: 'textfield',
						items :[
							new Ext.form.ComboBox({
								id: 'sharehidebranding',
								name: 'sharehidebranding',
								mode: 'local',
								editable: false,
								forceSelection: true,
								selectOnFocus: true,
								triggerAction: 'all',
								fieldLabel: "{/literal}{#str_SectionTitleBranding#}{literal}",
								store: new Ext.data.ArrayStore({
									id: 0,
									fields: ['id', 'name'],
									data: [
										[0, "{/literal}{#str_LabelShowBranding#}{literal}"],
										[1, "{/literal}{#str_LabelHideBranding#}{literal}"],
									]
								}),
								valueField: 'id',
								displayField: 'name',
								useID: true,
								value: "{/literal}{$sharehidebranding}{literal}",
								post: true,
								listeners: {
									select: togglePreviewDomainURL
								}
							}),
							{
								xtype: 'textfield',
								id: 'previewdomainurl',
								name: 'previewdomainurl',
								fieldLabel: "{/literal}{#str_LabelPreviewDomainURL#}{literal}",
								value: "{/literal}{$previewdomainurl}{literal}",
								validateOnBlur: true,
								post: true,
								width: 424,
								validator: function(v){ return validateUrl(this); }
							}
						]
					}
                ]
			},
			{/literal}{if $hasonlinedesigner == 1}{literal}
				{
					title: "{/literal}{#str_TitleOnlineDesignerSettings#}{literal}",
					id: 'onlinedesignertab',
					layout: 'form',
					labelWidth: 305,
					listeners: { 'beforeshow': function(){ Ext.getCmp('maintabpanel').doLayout(); }},
					items:
					[
						urlsPanel,
						multiLinePanel,
						keyPanel,
						fontList,
						generalSettingsPanel
					]
				},
			{/literal}{/if}{literal}
			desktopDesignerSettingsTab,
			{
				title: "{/literal}{#str_TitleGoogleAnalytics#}{literal}",
				id: 'googleanalyticstab',
				layout: 'form',
				listeners: { 'beforeshow': function(){ Ext.getCmp('maintabpanel').doLayout(); }},
				items:
				[
					{
                        xtype:'fieldset',
                        columnWidth: 0.5,
                        title: "{/literal}{#str_TitleShoppingCartAndAccount#}{literal}",
                        collapsible: false,
                        autoHeight:true,
                        defaultType: 'textfield',
						items:[
							{
								xtype: 'textfield',
								id: 'googlecode',
								name: 'googlecode',
								fieldLabel: "{/literal}{#str_LabelGoogleAnalyticsID#}{literal}",
								width: 250,
								{/literal}{if $brandingid != 0}{literal}
								value: "{/literal}{$googleanalyticscode}{literal}",
								{/literal}{/if}{literal}
								validateOnBlur: true,
								post: true,
								validator: function(v){ return validateAnalytics(this); },
								listeners: { valid: enableUserIDTrackingCheckBox }
							},
							new Ext.form.Checkbox(
							{
								boxLabel: "{/literal}{#str_UserIdTrackingLabel#}{literal}",
								name: 'useridtracking',
								id: 'useridtracking',
								disabled: true,
								checked: {/literal}{$useridtrackingchecked}{literal},
								hideLabel: true,
								listeners: { check: activateUserIDTracking }
							})
						]
					},
					{
                        xtype:'fieldset',
                        columnWidth: 0.5,
                        title: "{/literal}{#str_LabelGoogleTagManager#}{literal}",
                        collapsible: false,
                        autoHeight:true,
                        defaultType: 'textfield',
						items:[
							{
								xtype: 'textfield',
								id: 'googletagmanageronlinecode',
								name: 'googletagmanageronlinecode',
								fieldLabel: "{/literal}{#str_LabelOnlineGoogleTagManagerID#}{literal}",
								width: 250,
								{/literal}{if $brandingid != 0}{literal}
								value: "{/literal}{$googletagmanageronlinecode}{literal}",
								{/literal}{/if}{literal}
								validateOnBlur: true,
								post: true,
								validator: function(v){ return validateTagManager(this); }
							},
							{
								xtype: 'textfield',
								id: 'googletagmanagercccode',
								name: 'googletagmanagercccode',
								fieldLabel: "{/literal}{#str_LabelCCGoogleTagManagerID#}{literal}",
								width: 250,
								{/literal}{if $brandingid != 0}{literal}
								value: "{/literal}{$googletagmanagercccode}{literal}",
								{/literal}{/if}{literal}
								validateOnBlur: true,
								post: true,
								validator: function(v){ return validateTagManager(this); }
							}
						]
					}
				]
			},
			dataDeletionTab,
			customiseTab
		]
	};

	var dialogFormPanelObj = new Ext.taopix.FormPanel({
		id: 'mainform',
		header: false,
		frame:true,
		width: 935,
		layout: 'form',
		defaultType: 'textfield',
		bodyStyle:'border-bottom: 1px solid #96bde7;',
		autoHeight: true,
		items: [ topPanel, tabPanel ],
		baseParams:	{ ref: "{/literal}{$ref}{literal}" }
	});

	gDialogObj = new Ext.Window({
		id: 'dialog',
		closable:false,
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		title: "{/literal}{$title}{literal}",
		layout: 'fit',
		width: 980,
		items: dialogFormPanelObj,
		listeners: {
			'close': {
				fn: function(){
					brandingEditWindowExists = false;
				}
			}
		},
		cls: 'left-right-buttons',
		buttons:
		[
			new Ext.form.Checkbox({
				id: 'isactive',
				name: 'isactive',
				boxLabel: "{/literal}{#str_LabelActive#}{literal}",
				post: true,
				cls: 'x-btn-left',
				ctCls: 'width_100'
			}),
			{
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(btn, ev){ gDialogObj.close(); },
				cls: 'x-btn-right'
			},
			{
				id: 'updateButton',
				{/literal}{if $brandingid < 1}{literal}
					handler: editSaveHandler,
					text: "{/literal}{#str_ButtonAdd#}{literal}",
				{/literal}{else}{literal}
					handler: editSaveHandler,
					text: "{/literal}{#str_ButtonUpdate#}{literal}",
				{/literal}{/if}{literal}
				cls: 'x-btn-right'
			}
		]
	});
	gDialogObj.show();

	initializeControls();


	/* override default even handler to prevent grouping from collapsing when checkbox is clicked */
	(function()
	{
		/* code form groupingview.js overriden */
		var originalFunction = function(name, e)
		{
			Ext.grid.GroupingView.superclass.processEvent.call(this, name, e);
	        var hd = e.getTarget('.x-grid-group-hd', this.mainBody);
	        if(hd)
	        {
	        	Ext.getCmp('emailsGrid').stopEditing(false);
	            var field = this.getGroupField(),
	                prefix = this.getPrefix(field),
	                groupValue = hd.id.substring(prefix.length),
	                emptyRe = new RegExp('gp-' + Ext.escapeRe(field) + '--hd');

	            groupValue = groupValue.substr(0, groupValue.length - 3);

	            if(groupValue || emptyRe.test(hd.id)){
	                this.grid.fireEvent('group' + name, this.grid, field, groupValue, e);
	            }
	            if(name == 'mousedown' && e.button == 0){
	                this.toggleGroup(hd.parentNode);
	            }
	    	}
		};

		Ext.override(Ext.grid.GroupingView, {
    		processEvent : function(name, e)
    		{
    			if ((e.getTarget().tagName == 'INPUT') || (e.getTarget().tagName == 'BUTTON'))
    			{
    				e.stopPropagation();
    			}
    			else
    			{
    				originalFunction.apply(this, arguments);
    			}
    		}
		});
	})();

	/* Override the events to make grid fire editcompete even if value wasnt modified */
	Ext.grid.EditorGridPanel.prototype.onEditComplete = function(ed, value, startValue)
	{
    	this.editing = false;
        this.lastActiveEditor = this.activeEditor;
        this.activeEditor = null;

        var r = ed.record,
        field = this.colModel.getDataIndex(ed.col);
        value = this.postEditValue(value, startValue, r, field);
        if(this.forceValidation === true || String(value) !== String(startValue)){
            var e = {
                grid: this,
                record: r,
                field: field,
                originalValue: startValue,
                value: value,
                row: ed.row,
                column: ed.col,
                cancel:false
            };
            if(this.fireEvent("validateedit", e) !== false && !e.cancel){
                r.set(field, e.value);
                delete e.cancel;
                this.fireEvent("afteredit", e);
            }
        }
        this.view.focusCell(ed.row, ed.col);
    };

	function getAuthentication(evt)
	{
		var authUrl = '/?fsaction=AdminBranding.getAuthentication&ref={/literal}{$ref}{literal}&provider=' + Ext.getCmp('oauthprovider').getValue();
		window.open(authUrl, '_blank', 'popup,width=350,height=500')
	}

	function authenticationContainerDisplay(box, record, authMethod)
	{
		var userNameAuthentication = Ext.getCmp('userNamePassword');
		var oauthAuthentication = Ext.getCmp('oauthCredentials');

		setAuthFields(authMethod);
		if (0 === authMethod || false === authMethod) {
			userNameAuthentication.hide()
			oauthAuthentication.hide();
		} else if (1 === authMethod || true === authMethod) {
			userNameAuthentication.show();
			oauthAuthentication.hide();
		} else {
			userNameAuthentication.hide();
			oauthAuthentication.show();
		}

		Ext.getCmp('smtpauthuser').clearInvalid();
		Ext.getCmp('smtpauthpass').clearInvalid();
	}

	function setAuthFields(authMethod)
	{
		Ext.getCmp('smtpauthuser').disable();
		Ext.getCmp('smtpauthpass').disable();
		Ext.getCmp('oauthprovider').disable();
		Ext.getCmp('oauthrefreshtoken').disable();
		Ext.getCmp('oauthauthenticate').disable();
		Ext.getCmp('smtpsysfromname').enable();
		Ext.getCmp('smtpsysfromaddress').enable();
		if (1 === authMethod || true === authMethod) {
			Ext.getCmp('smtpauthuser').enable();
			Ext.getCmp('smtpauthpass').enable();
		} else  if (2 === authMethod) {
			Ext.getCmp('oauthprovider').enable();
			Ext.getCmp('oauthrefreshtoken').disable();
			Ext.getCmp('oauthauthenticate').enable();
			Ext.getCmp('smtpsysfromname').disable();
			Ext.getCmp('smtpsysfromaddress').disable();
		}
	}

    dialogFormPanelObj.getForm().clearInvalid();

	Ext.getCmp('isactive').setValue("{/literal}{$activechecked}{literal}" == 'checked' ? true : false);

	Ext.getCmp('previewExpire').setValue({/literal}{$previewexpire}{literal});

	showPreviewExpire();

	// Set up the custom brand images.
	initializeBrandAssets();
}
{/literal}
