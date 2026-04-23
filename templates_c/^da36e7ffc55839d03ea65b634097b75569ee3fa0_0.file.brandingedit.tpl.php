<?php
/* Smarty version 4.5.3, created on 2026-04-22 05:37:23
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\branding\brandingedit.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69e85e93771723_43270532',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'da36e7ffc55839d03ea65b634097b75569ee3fa0' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\branding\\brandingedit.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69e85e93771723_43270532 (Smarty_Internal_Template $_smarty_tpl) {
echo $_smarty_tpl->tpl_vars['languagecodesjavascript']->value;?>

<?php echo $_smarty_tpl->tpl_vars['languagenamesjavascript']->value;?>

<?php echo $_smarty_tpl->tpl_vars['localizedcodesjavascript']->value;?>

<?php echo $_smarty_tpl->tpl_vars['localizednamesjavascript']->value;?>



var emailSections =
{
	'AA':
	{
		'required': 1,
		'name': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAdministrator');?>
",
		'active': "<?php echo $_smarty_tpl->tpl_vars['smtpadminactive']->value;?>
",
		'gActive': "<?php echo $_smarty_tpl->tpl_vars['gsmtpadminactive']->value;?>
",
		'names': "<?php echo $_smarty_tpl->tpl_vars['smtpadminname']->value;?>
",
		'gNames': "<?php echo $_smarty_tpl->tpl_vars['gsmtpadminname']->value;?>
",
		'emails': "<?php echo $_smarty_tpl->tpl_vars['smtpadminaddress']->value;?>
",
		'gEmails': "<?php echo $_smarty_tpl->tpl_vars['gsmtpadminaddress']->value;?>
"
	},
	'PA':
	{
		'required': 0,
		'name': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProduction');?>
",
		'active': "<?php echo $_smarty_tpl->tpl_vars['smtpprodactive']->value;?>
",
		'gActive': "<?php echo $_smarty_tpl->tpl_vars['gsmtpprodactive']->value;?>
",
		'names': "<?php echo $_smarty_tpl->tpl_vars['smtpprodname']->value;?>
",
		'gNames': "<?php echo $_smarty_tpl->tpl_vars['gsmtpprodname']->value;?>
",
		'emails': "<?php echo $_smarty_tpl->tpl_vars['smtpprodaddress']->value;?>
",
		'gEmails': "<?php echo $_smarty_tpl->tpl_vars['gsmtpprodaddress']->value;?>
"
	},
	'CA':
	{
		'required': 0,
		'name': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
",
		'active': "<?php echo $_smarty_tpl->tpl_vars['smtporderconfactive']->value;?>
",
		'gActive': "<?php echo $_smarty_tpl->tpl_vars['gsmtporderconfactive']->value;?>
",
		'names': "<?php echo $_smarty_tpl->tpl_vars['smtporderconfname']->value;?>
",
		'gNames': "<?php echo $_smarty_tpl->tpl_vars['gsmtporderconfname']->value;?>
",
		'emails': "<?php echo $_smarty_tpl->tpl_vars['smtporderconfaddress']->value;?>
",
		'gEmails': "<?php echo $_smarty_tpl->tpl_vars['gsmtporderconfaddress']->value;?>
"
	},
	'SA':
	{
		'required': 0,
		'name': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSaveOrder');?>
",
		'active': "<?php echo $_smarty_tpl->tpl_vars['smtpsaveorderactive']->value;?>
",
		'gActive': "<?php echo $_smarty_tpl->tpl_vars['gsmtpsaveorderactive']->value;?>
",
		'names': "<?php echo $_smarty_tpl->tpl_vars['smtpsaveordername']->value;?>
",
		'gNames': "<?php echo $_smarty_tpl->tpl_vars['gsmtpsaveordername']->value;?>
",
		'emails': "<?php echo $_smarty_tpl->tpl_vars['smtpsaveorderaddress']->value;?>
",
		'gEmails': "<?php echo $_smarty_tpl->tpl_vars['gsmtpsaveorderaddress']->value;?>
"
	},
	'SH':
	{
		'required': 0,
		'name': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleShipping');?>
",
		'active': "<?php echo $_smarty_tpl->tpl_vars['smtpshippingactive']->value;?>
",
		'gActive': "<?php echo $_smarty_tpl->tpl_vars['gsmtpshippingactive']->value;?>
",
		'names': "<?php echo $_smarty_tpl->tpl_vars['smtpshippingname']->value;?>
",
		'gNames': "<?php echo $_smarty_tpl->tpl_vars['gsmtpshippingname']->value;?>
",
		'emails': "<?php echo $_smarty_tpl->tpl_vars['smtpshippingaddress']->value;?>
",
		'gEmails': "<?php echo $_smarty_tpl->tpl_vars['gsmtpshippingaddress']->value;?>
"
	},
	'NA':
	{
		'required': 0,
		'name': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNewAccount');?>
",
		'active': "<?php echo $_smarty_tpl->tpl_vars['smtpnewaccountactive']->value;?>
",
		'gActive': "<?php echo $_smarty_tpl->tpl_vars['gsmtpnewaccountactive']->value;?>
",
		'names': "<?php echo $_smarty_tpl->tpl_vars['smtpnewaccountname']->value;?>
",
		'gNames': "<?php echo $_smarty_tpl->tpl_vars['gsmtpnewaccountname']->value;?>
",
		'emails': "<?php echo $_smarty_tpl->tpl_vars['smtpnewaccountaddress']->value;?>
",
		'gEmails': "<?php echo $_smarty_tpl->tpl_vars['gsmtpnewaccountaddress']->value;?>
"
	},
	'RP':
	{
		'required': 0,
		'name': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelResetPassword');?>
",
		'active': "<?php echo $_smarty_tpl->tpl_vars['smtpresetpasswordactive']->value;?>
",
		'gActive': "<?php echo $_smarty_tpl->tpl_vars['gsmtpresetpasswordactive']->value;?>
",
		'names': "<?php echo $_smarty_tpl->tpl_vars['smtpresetpasswordname']->value;?>
",
		'gNames': "<?php echo $_smarty_tpl->tpl_vars['gsmtpresetpasswordname']->value;?>
",
		'emails': "<?php echo $_smarty_tpl->tpl_vars['smtpresetpasswordaddress']->value;?>
",
		'gEmails': "<?php echo $_smarty_tpl->tpl_vars['gsmtpresetpasswordaddress']->value;?>
"
	},
	'OU':
	{
		'required': 0,
		'name': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderUploaded');?>
",
		'active': "<?php echo $_smarty_tpl->tpl_vars['smtporderuploadedactive']->value;?>
",
		'gActive': "<?php echo $_smarty_tpl->tpl_vars['gsmtporderuploadedactive']->value;?>
",
		'names': "<?php echo $_smarty_tpl->tpl_vars['smtporderuploadedname']->value;?>
",
		'gNames': "<?php echo $_smarty_tpl->tpl_vars['gsmtporderuploadedname']->value;?>
",
		'emails': "<?php echo $_smarty_tpl->tpl_vars['smtporderuploadedaddress']->value;?>
",
		'gEmails': "<?php echo $_smarty_tpl->tpl_vars['gsmtporderuploadedaddress']->value;?>
"
	}
};

var emailInProcess = '';

var gBrandingCode = "<?php echo $_smarty_tpl->tpl_vars['code']->value;?>
";
var gSmtpAuthPass = "<?php echo $_smarty_tpl->tpl_vars['gsmtpauthpass']->value;?>
";
var initPassword = "<?php echo $_smarty_tpl->tpl_vars['smtpauthpass']->value;?>
";
var gDateFormat = "<?php echo $_smarty_tpl->tpl_vars['dateformat']->value;?>
";
var invalidDateFormatLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInvalidDateFormat');?>
";

var onlinedesignersettings_tx = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleOnlineDesignerSettings');?>
";
var maxmegapixels_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMaxMegaPixels');?>
";

var imagescaling_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelImageScaling');?>
";
var appkey_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAppKey');?>
";
var urls_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUrls');?>
";
var generalsettings_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGeneralSettings');?>
";

var imagescalingbefore_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelImageScalingBefore');?>
";
var imagescalingafter_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelImageScalingAfter');?>
";
var imagescalingbeforeenabled_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnableImageScalingBefore');?>
";
var imagescalingafterenabled_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnableImageScalingAfter');?>
";

var imagescalingBeforeVal = "<?php echo $_smarty_tpl->tpl_vars['imagescalingbefore']->value;?>
";
var imagescalingBeforeEnabledVal = ("<?php echo $_smarty_tpl->tpl_vars['imagescalingbeforeenabled']->value;?>
" == 'checked') ? true : false;
var imagescalingAfterVal = "<?php echo $_smarty_tpl->tpl_vars['imagescalingafter']->value;?>
";
var imagescalingAfterEnabledVal = ("<?php echo $_smarty_tpl->tpl_vars['imagescalingafterenabled']->value;?>
" == 'checked') ? true : false;

var shufflelayoutshowoption_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSuffleLayoutShowOption');?>
";
var shufflelayout_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShuffleLayout');?>
";
var shufflelayoutleftright_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLeftRightPages');?>
";
var shufflelayoutspread_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSpread');?>
";
var shufflelayoutpictures_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSufflePictures');?>
";

var insertDeleteButtons_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInsertDeleteButtons');?>
";
var showInsertDeleteButtons_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowInsertDeleteButtons');?>
";

var totalPagesDropdown_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelTotalPagesDropdown');?>
";
var enableTotalPagesDropdown_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnableTotalPagesDropdown');?>
";

var str_LabelLanguageName    = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLanguageName');?>
";
var str_localizedNameLabel   = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
";

var deleteImg = '<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/delete.png';
var addimg = '<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/add.png';

var awaitingUserIDTrackingResponse = false;

var previousRedactionMode = <?php echo $_smarty_tpl->tpl_vars['redactionMode']->value;?>
;
var initialRedactionMode = <?php echo $_smarty_tpl->tpl_vars['redactionMode']->value;?>
;
var hasMassUnsubscribeTaskRunning = ("<?php echo $_smarty_tpl->tpl_vars['massunsubscribetaskforbrandrunning']->value;?>
" == '1') ? true : false;
var str_massUnsubscribeTaskInProgress = ("<?php echo $_smarty_tpl->tpl_vars['massunsubscribetaskforbrandrunning']->value;?>
" == '1') ? "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUnsubscribeTaskInProgress');?>
" : '';

var orderRedactionMode = <?php echo $_smarty_tpl->tpl_vars['orderredactionmode']->value;?>
;
var desktopThumbnailDeletionEnabled = <?php echo $_smarty_tpl->tpl_vars['desktopthumbnaildeletionenabled']->value;?>


var brandFileArray = new Object();
var brandTextArray = <?php echo $_smarty_tpl->tpl_vars['brandassetstrings']->value;?>
;
var brandTextEnabledArray = <?php echo $_smarty_tpl->tpl_vars['brandassetstringsenabled']->value;?>
;
var brandTextUseDefaultsArray = <?php echo $_smarty_tpl->tpl_vars['brandassetstringsusedefault']->value;?>
;
var defaultBrandTextArray = <?php echo $_smarty_tpl->tpl_vars['defaultbrandstrings']->value;?>
;

// Store tha initial state of the asset when the form is displayed, 0 is using default, any other value would mean that an image has been uploaded.
var lastSavedBrandAssetData = <?php echo $_smarty_tpl->tpl_vars['brandassetsdata']->value;?>
;
// Use the values to track the state of each of the images.
var workingBrandAssetData = <?php echo $_smarty_tpl->tpl_vars['brandassetsdata']->value;?>
;
var entropy = "<?php echo $_smarty_tpl->tpl_vars['entropy']->value;?>
";
var regenerateVisible = ("<?php echo $_smarty_tpl->tpl_vars['regenerateVisible']->value;?>
" == '1') ? false : true;


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
		case <?php echo $_smarty_tpl->tpl_vars['onlineLogoType']->value;?>
:
		{
			popupTitle = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOnlineDesignerLogo');?>
";
			break;
		}
		case <?php echo $_smarty_tpl->tpl_vars['onlineLogoTypeDark']->value;?>
:
		{
			popupTitle = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOnlineDesignerLogoDark');?>
";
			break;
		}
		case <?php echo $_smarty_tpl->tpl_vars['controlLogoType']->value;?>
:
		{
			popupTitle = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCustomerAccountLogo');?>
";
			break;
		}
		case <?php echo $_smarty_tpl->tpl_vars['marketingType']->value;?>
:
		{
			popupTitle = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCustomerAccountSidebar');?>
";
			break;
		}
		case <?php echo $_smarty_tpl->tpl_vars['emailLogoType']->value;?>
:
		{
			popupTitle = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailLogo');?>
";
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
				Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageEmailSent');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
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
			Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorEmailEmpty');?>
", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
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
		Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorEmailEmpty');?>
", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
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
	paramArray['ref'] = "<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
";
	paramArray['ssop'] = Ext.getCmp('oauthprovider').getValue();
	paramArray['sspt'] = Ext.getCmp('oauthrefreshtokenid').getValue();

	emailInProcess = pSectionCode;
	store.singleSort('sectionName', 'ASC');
	Ext.taopix.formPost('', paramArray, 'index.php?fsaction=AjaxAPI.callback', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePleaseWait');?>
", onEmailTestCallback);
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
			defaultText: {langBlank: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSelectLanguage');?>
",  textBlank: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTypeValue');?>
"},
			columnWidth: {langCol: 290, textCol: 595, delCol: 35},
			fieldWidth:  {langField: 290, textField: 565},
			errorMsg:    {blankValue: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTextFieldBlank');?>
"}
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
			ref: "<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
"
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
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleLinkToolTip');?>
",
		items:
		[
			onlineLogoLinkTooltipPanel
		],
		buttons:
		[
			{
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
",
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
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdate');?>
",
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

	
	var id = "<?php echo $_smarty_tpl->tpl_vars['brandingid']->value;?>
";
	var code = "<?php echo $_smarty_tpl->tpl_vars['code']->value;?>
";
	var name = "<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
";
	var UIURL = "<?php echo $_smarty_tpl->tpl_vars['onlineuiurl']->value;?>
";
	var APIURL = "<?php echo $_smarty_tpl->tpl_vars['onlineapiurl']->value;?>
";
	var designerURL = "<?php echo $_smarty_tpl->tpl_vars['onlinedesignerurl']->value;?>
";
	var iv = "<?php echo $_smarty_tpl->tpl_vars['entropy']->value;?>
";
	

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
	
	var gBrandingID = <?php echo $_smarty_tpl->tpl_vars['brandingid']->value;?>
;
	var gBrandingCode = "<?php echo $_smarty_tpl->tpl_vars['code']->value;?>
";
	var gPaymentMethodCount = <?php echo $_smarty_tpl->tpl_vars['paymentmethodcount']->value;?>
;
	var gPaymentMethods = "<?php echo $_smarty_tpl->tpl_vars['defaultpaymentmethods']->value;?>
";
	var gCurrentIntegration = "<?php echo $_smarty_tpl->tpl_vars['currentintegration']->value;?>
";
	var gSmtpAddress = "<?php echo $_smarty_tpl->tpl_vars['gsmtpaddress']->value;?>
";
	var gSmtpAuth = "<?php echo $_smarty_tpl->tpl_vars['gsmtpauth']->value;?>
";
	var gSmtpPort = "<?php echo $_smarty_tpl->tpl_vars['gsmtpport']->value;?>
";
	var gSmtpAuthUser = "<?php echo $_smarty_tpl->tpl_vars['gsmtpauthuser']->value;?>
";
    var gSmtpType = "<?php echo $_smarty_tpl->tpl_vars['gsmtptype']->value;?>
";
	var gSmtpSysFromName = "<?php echo $_smarty_tpl->tpl_vars['gsmtpsysfromname']->value;?>
";
	var gSmtpSysFromAddress = "<?php echo $_smarty_tpl->tpl_vars['gsmtpsysfromaddress']->value;?>
";
	var gSmtpReplyName = "<?php echo $_smarty_tpl->tpl_vars['gsmtpreplyname']->value;?>
";
	var gSmtpReplyAddress = "<?php echo $_smarty_tpl->tpl_vars['gsmtpreplyaddress']->value;?>
";
	var gSmtpAdminName = "<?php echo $_smarty_tpl->tpl_vars['gsmtpadminname']->value;?>
";
	var gSmtpAdminAddress = "<?php echo $_smarty_tpl->tpl_vars['gsmtpadminaddress']->value;?>
";
	var gSmtpProdName = "<?php echo $_smarty_tpl->tpl_vars['gsmtpprodname']->value;?>
";
	var gSmtpProdAddress = "<?php echo $_smarty_tpl->tpl_vars['gsmtpprodaddress']->value;?>
";
	var gSmtpOrderConfName = "<?php echo $_smarty_tpl->tpl_vars['gsmtporderconfname']->value;?>
";
	var gSmtpOrderConfAddress = "<?php echo $_smarty_tpl->tpl_vars['gsmtporderconfaddress']->value;?>
";
	var gSmtpSaveOrderName = "<?php echo $_smarty_tpl->tpl_vars['gsmtpsaveordername']->value;?>
";
	var gSmtpSaveOrderAddress = "<?php echo $_smarty_tpl->tpl_vars['gsmtpsaveorderaddress']->value;?>
";
	var gSmtpAuthPass = "<?php echo $_smarty_tpl->tpl_vars['smtpauthpass']->value;?>
";
	var gDeleteLabel = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDelete');?>
";
	var productionSitesCompanies = <?php echo $_smarty_tpl->tpl_vars['productionSitesCompanies']->value;?>
;
	var productionSitesSelected = "<?php echo $_smarty_tpl->tpl_vars['productionSitesSelected']->value;?>
";
	var gSmtpShippingName = "<?php echo $_smarty_tpl->tpl_vars['gsmtpshippingname']->value;?>
";
	var gSmtpShippingAddress = "<?php echo $_smarty_tpl->tpl_vars['gsmtpshippingaddress']->value;?>
";
	var gSmtpNewAccountName = "<?php echo $_smarty_tpl->tpl_vars['gsmtpnewaccountname']->value;?>
";
	var gSmtpNewAccountAddress = "<?php echo $_smarty_tpl->tpl_vars['gsmtpnewaccountaddress']->value;?>
";
	var gSmtpResetPasswordName = "<?php echo $_smarty_tpl->tpl_vars['gsmtpresetpasswordname']->value;?>
";
	var gSmtpResetPasswordAddress = "<?php echo $_smarty_tpl->tpl_vars['gsmtpresetpasswordaddress']->value;?>
";
	var gSmtpOrderUploadedName = "<?php echo $_smarty_tpl->tpl_vars['gsmtporderuploadedname']->value;?>
";
	var gSmtpOrderUploadedAddress = "<?php echo $_smarty_tpl->tpl_vars['gsmtporderuploadedaddress']->value;?>
";
	var customAccountPagesURL = "<?php echo $_smarty_tpl->tpl_vars['accountpagesurl']->value;?>
";
	

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
			document.getElementById('prev-disp-'+pOption).innerHTML = '<span style="max-height: 130px; max-width: 130px;" id="previewimage-' + pOption + '" name="uploadwarning" class="upload-Warning-text"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPleaseUploadAnImage');?>
</span>'

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
			document.getElementById('prev-disp-'+pOption).innerHTML = '<img style="max-height: 130px; max-width: 130px;" id="previewimage-' + pOption + '" name="previewimage" src="./?fsaction=AdminBranding.getBrandFilePreview&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&typeref=' + pOption + '&code=' + gBrandingCode + '&bid=<?php echo $_smarty_tpl->tpl_vars['brandingid']->value;?>
&tmp=' + tmpFile + '&version=' + gDate + '"><div class="preview-icon"></div>'

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
			<?php if ($_smarty_tpl->tpl_vars['brandingid']->value > 0 && $_smarty_tpl->tpl_vars['code']->value == '') {?>
				parameter['code'] = '';
			<?php } else { ?>
				parameter['code'] = Ext.getCmp('code').getValue();
			<?php }?>

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
				Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorPreviewDomainURLNotSet');?>
", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
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
				Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidOAuthConfiguration');?>
", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
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
					Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoPaymentMethods');?>
", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
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
				<?php if (!$_smarty_tpl->tpl_vars['sslloaded']->value) {?>
					var gSllRequiredIntegrations = "<?php echo $_smarty_tpl->tpl_vars['sllrequiredintegrations']->value;?>
";
					if (gSllRequiredIntegrations.indexOf(paymentIntegration) > -1)
					{
						var message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorPaymentIntegrationRequiresSSL');?>
".replace("^0", paymentIntegration);
						Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: message, buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
						return false;
					}
				<?php }?>
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
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value && ($_smarty_tpl->tpl_vars['owner']->value == '')) {?>
				productionSite  = Ext.getCmp('productionsitelist').getValue();
				parameter['productionsite'] = productionSite;
			<?php }?>

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

			<?php if ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1) {?>
				var useMultiLineWorkflow = (Ext.getCmp('usemultilinebasketworkflow').checked) ? 1 : 0;

				var imagescalingbefore = 0;
				var imagescalingbeforeenabled = 0;

				
				<?php if ($_smarty_tpl->tpl_vars['allowimagescalingbefore']->value) {?>
				
					imagescalingbefore = Ext.getCmp('imagescalingbefore').getValue();
					imagescalingbeforeenabled = (Ext.getCmp('imagescalingbeforeenabled').checked) ? 1 : 0;
				
				<?php }?>
				

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
			<?php }?>

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
				Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=AdminBranding.edit', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
", onCallback);
			}
			else
			{
				Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=AdminBranding.add', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
", onCallback);
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

			<?php if ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1) {?>
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
			<?php }?>

			if ((displayurlValue == "") && (weburlValue != ""))
			{
				Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorWebURLNoDisplayURL');?>
", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			/* check to see if we are editing a brand record. If we are then we need to check if we are editing the default brand */

			<?php if ($_smarty_tpl->tpl_vars['brandingid']->value > 0) {?>
			var brandingGrid = Ext.getCmp('brandingGrid');
			var records = brandingGrid.selModel.getSelections();

			if (records[0].data.code != '')
			{
				if ((weburlValue != "") && (weburlValue != 'http://' && displayurlValue != 'http://'))
				{
					if (displayurlValue == weburlValue)
					{
						Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorWebURLEqualsDisplayURL');?>
", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
						return false;
					}
				}
			}
			<?php } else { ?>
				if ((weburlValue != "") && (weburlValue != 'http://' && displayurlValue != 'http://'))
				{
					if (displayurlValue == weburlValue)
					{
						Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorWebURLEqualsDisplayURL');?>
", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
						return false;
					}
				}
			<?php }?>

			parameter['displayurl'] = displayurlValue;
			parameter['weburl'] = weburlValue;

			<?php if ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1) {?>
				parameter['onlinedesignerurl'] = onlineDesignerURL;
				parameter['onlineuiurl'] = onlineUiURL;
				parameter['onlineapiurl'] = onlineAPIURL;
				parameter['onlinedesignerlogouturl'] = onlineDesignerLogoutURL;
				parameter['onlinedesignercdnurl'] = onlineDesignerCDNURL;
				parameter['onlineabouturl'] = onlineAboutURL;
				parameter['onlinehelpurl'] = onlineHelpURL;
				parameter['onlinetermsandconditionsurl'] = onlineTermsAndConditionsURL;
			<?php }?>

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
				Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoSectionValue');?>
".replace("^0", emailSections['AA'].name), buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			if ((emailSections['PA'].required == 1 && parameter['smtpprodname'] == '') ||
			(parameter['smtpprodname'].split(';').length != parameter['smtpprodaddress'].split(';').length) ||
			(parameter['smtpprodname'].split(';')[0] == '' && parameter['smtpprodaddress'].split(';')[0] != '') ||
			(parameter['smtpprodname'].split(';')[0] != '' && parameter['smtpprodaddress'].split(';')[0] == ''))
			{
				Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoSectionValue');?>
".replace("^0", emailSections['PA'].name), buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			if ((emailSections['CA'].required == 1 && parameter['smtporderconfname'] == '') ||
			(parameter['smtporderconfname'].split(';').length != parameter['smtporderconfaddress'].split(';').length) ||
			(parameter['smtporderconfname'].split(';')[0] == '' && parameter['smtporderconfaddress'].split(';')[0] != '') ||
			(parameter['smtporderconfname'].split(';')[0] != '' && parameter['smtporderconfaddress'].split(';')[0] == ''))
			{
				Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoSectionValue');?>
".replace("^0", emailSections['CA'].name), buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			if ((emailSections['SA'].required == 1 && parameter['smtpsaveordername'] == '') ||
			(parameter['smtpsaveordername'].split(';').length != parameter['smtpsaveorderaddress'].split(';').length) ||
			(parameter['smtpsaveordername'].split(';')[0] == '' && parameter['smtpsaveorderaddress'].split(';')[0] != '') ||
			(parameter['smtpsaveordername'].split(';')[0] != '' && parameter['smtpsaveorderaddress'].split(';')[0] == ''))
			{
				Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoSectionValue');?>
".replace("^0", emailSections['SA'].name), buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			if ((emailSections['SH'].required == 1 && parameter['smtpshippingname'] == '') ||
			(parameter['smtpshippingname'].split(';').length != parameter['smtpshippingaddress'].split(';').length) ||
			(parameter['smtpshippingname'].split(';')[0] == '' && parameter['smtpshippingaddress'].split(';')[0] != '') ||
			(parameter['smtpshippingname'].split(';')[0] != '' && parameter['smtpshippingaddress'].split(';')[0] == ''))
			{
				Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoSectionValue');?>
".replace("^0", emailSections['SH'].name), buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			if ((emailSections['NA'].required == 1 && parameter['smtpnewaccountname'] == '') ||
			(parameter['smtpnewaccountname'].split(';').length != parameter['smtpnewaccountaddress'].split(';').length) ||
			(parameter['smtpnewaccountname'].split(';')[0] == '' && parameter['smtpnewaccountaddress'].split(';')[0] != '') ||
			(parameter['smtpnewaccountname'].split(';')[0] != '' && parameter['smtpnewaccountaddress'].split(';')[0] == ''))
			{
				Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoSectionValue');?>
".replace("^0", emailSections['NA'].name), buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			if ((emailSections['RP'].required == 1 && parameter['smtpresetpasswordname'] == '') ||
			(parameter['smtpresetpasswordname'].split(';').length != parameter['smtpresetpasswordaddress'].split(';').length) ||
			(parameter['smtpresetpasswordname'].split(';')[0] == '' && parameter['smtpresetpasswordaddress'].split(';')[0] != '') ||
			(parameter['smtpresetpasswordname'].split(';')[0] != '' && parameter['smtpresetpasswordaddress'].split(';')[0] == ''))
			{
				Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoSectionValue');?>
".replace("^0", emailSections['RP'].name), buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			if ((emailSections['OU'].required == 1 && parameter['smtporderuploadedname'] == '') ||
			(parameter['smtporderuploadedname'].split(';').length != parameter['smtporderuploadedaddress'].split(';').length) ||
			(parameter['smtporderuploadedname'].split(';')[0] == '' && parameter['smtporderuploadedaddress'].split(';')[0] != '') ||
			(parameter['smtporderuploadedname'].split(';')[0] != '' && parameter['smtporderuploadedaddress'].split(';')[0] == ''))
			{
				Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoSectionValue');?>
".replace("^0", emailSections['OU'].name), buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				return false;
			}

			if ((Ext.getCmp('productionsitelist')) && (gBrandingID > 0))
			{
				var productionSite = Ext.getCmp('productionsitelist').getValue();

				var originalCompanyCode = productionSitesCompanies[productionSitesSelected];
				var newCompanyCode = productionSitesCompanies[productionSite];

				if (originalCompanyCode != newCompanyCode)
				{
					Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageProductionSiteCompanyChanged');?>
", onProductionCompanyChangeConfirm);
					return;
				}
			}

			// If using a custom signature has been checked, make sure the signature is not empty.
			if ((Ext.getCmp('emailSignatureenablecheck').checked) && (Ext.getCmp('mainform').findById('emailSignaturelangpanel').getStore().getCount() === 0))
			{
				// Show an errro message.
				Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoEmailSignature');?>
", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });

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
			var emailPic = "<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/taopix/progress-icon.gif";
			button = '<div style="overflow: hidden;"><div style="float: right; margin-right: 3px" onClick="return false;" OnMouseOver="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\') + \' x-btn-over\';" OnMouseOut="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\');"><table cellspacing="0" class="x-btn  x-btn-icon"><tbody class="x-btn-small x-btn-icon-small-left"><tr><td class="x-btn-tl"><i>&nbsp;</i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-ml"><i>&nbsp;</i></td><td class="x-btn-mc"><em unselectable="on" class=""><button type="button" class="x-btn-text " style="background-image: url(&quot;'+emailPic+'&quot;);">&nbsp;</button></em></td><td class="x-btn-mr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-bl"><i>&nbsp;</i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i>&nbsp;</i></td></tr></tbody></table></div></div>';
		}
		else
		{
			if (pRecord.data.isEmptyRow != 1)
			{
				var delPic = "<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/delete.png";
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
			smtpAddressValue = "<?php echo $_smarty_tpl->tpl_vars['gsmtpaddress']->value;?>
";
			Ext.getCmp('smtpaddress').disable();

			smtpAuthValue = parseInt("<?php echo $_smarty_tpl->tpl_vars['gsmtpauth']->value;?>
", 10);

			Ext.getCmp('smtpauth').disable();

			smtpPortValue = "<?php echo $_smarty_tpl->tpl_vars['gsmtpport']->value;?>
";
			Ext.getCmp('smtpport').disable();

			smtpAuthUserValue = "<?php echo $_smarty_tpl->tpl_vars['gsmtpauthuser']->value;?>
";
			smtpAuthPaswValue = "<?php echo $_smarty_tpl->tpl_vars['gsmtpauthpass']->value;?>
";
            smtpType = "<?php echo $_smarty_tpl->tpl_vars['gsmtptype']->value;?>
";
            Ext.getCmp('smtptype').disable();

			fromNameValue = "<?php echo $_smarty_tpl->tpl_vars['gsmtpsysfromname']->value;?>
";
			Ext.getCmp('smtpsysfromname').disable();

			fromAddressValue = "<?php echo $_smarty_tpl->tpl_vars['gsmtpsysfromaddress']->value;?>
";
			Ext.getCmp('smtpsysfromaddress').disable();

			replyToNameValue = "<?php echo $_smarty_tpl->tpl_vars['gsmtpreplyname']->value;?>
";
			Ext.getCmp('smtpreplyname').disable();

			replyToAddressValue = "<?php echo $_smarty_tpl->tpl_vars['gsmtpreplyaddress']->value;?>
";
			Ext.getCmp('smtpreplyaddress').disable();

			oauthProvider = parseInt("<?php echo $_smarty_tpl->tpl_vars['goauthprovider']->value;?>
", 10);
			oauthToken = parseInt("<?php echo $_smarty_tpl->tpl_vars['goauthtokenid']->value;?>
", 10);

			Ext.getCmp('oauthprovider').disable();
			Ext.getCmp('oauthrefreshtoken').disable();
			Ext.getCmp('oauthauthenticate').disable();
		}
		else
		{
			smtpAddressValue = "<?php echo $_smarty_tpl->tpl_vars['smtpaddress']->value;?>
";
			Ext.getCmp('smtpaddress').enable();

			smtpAuthValue = parseInt("<?php echo $_smarty_tpl->tpl_vars['smtpauth']->value;?>
", 10);
			Ext.getCmp('smtpauth').enable();

			smtpPortValue = "<?php echo $_smarty_tpl->tpl_vars['smtpport']->value;?>
";
			Ext.getCmp('smtpport').enable();

			smtpAuthUserValue = "<?php echo $_smarty_tpl->tpl_vars['smtpauthuser']->value;?>
";
			smtpAuthPaswValue = "<?php echo $_smarty_tpl->tpl_vars['smtpauthpass']->value;?>
";
            smtpType = "<?php echo $_smarty_tpl->tpl_vars['smtptype']->value;?>
";
            Ext.getCmp('smtptype').enable();

			fromNameValue = "<?php echo $_smarty_tpl->tpl_vars['smtpsysfromname']->value;?>
";
			Ext.getCmp('smtpsysfromname').enable();

			fromAddressValue = "<?php echo $_smarty_tpl->tpl_vars['smtpsysfromaddress']->value;?>
";
			Ext.getCmp('smtpsysfromaddress').enable();

			replyToNameValue = "<?php echo $_smarty_tpl->tpl_vars['smtpreplyname']->value;?>
";
			Ext.getCmp('smtpreplyname').enable();

			replyToAddressValue = "<?php echo $_smarty_tpl->tpl_vars['smtpreplyaddress']->value;?>
";
			Ext.getCmp('smtpreplyaddress').enable();

			oauthProvider = parseInt("<?php echo $_smarty_tpl->tpl_vars['oauthprovider']->value;?>
", 10);
			oauthToken = parseInt("<?php echo $_smarty_tpl->tpl_vars['oauthtokenid']->value;?>
", 10);

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

		
		<?php if ($_smarty_tpl->tpl_vars['currentintegration']->value == 'NONE') {?>
			Ext.getCmp('integrationNone').setValue(true);
		<?php } elseif ($_smarty_tpl->tpl_vars['currentintegration']->value == 'DEFAULT') {?>
			if (Ext.getCmp('integrationDefault')) Ext.getCmp('integrationDefault').setValue(true);
		<?php } else { ?>
			Ext.getCmp('integrationCustom').setValue(true);
		<?php }?>
		//if (Ext.getCmp('integrationlist').getStore().getAt(0)) Ext.getCmp('integrationlist').setValue(Ext.getCmp('integrationlist').getStore().getAt(0).data.id);

		setPaymentIntegration();

		<?php if ($_smarty_tpl->tpl_vars['displayurl']->value == '') {?>
			Ext.getCmp('displayurl').setValue('http://');
		<?php }?>

		<?php if ($_smarty_tpl->tpl_vars['previewdomainurl']->value === '') {?>
			Ext.getCmp('previewdomainurl').setValue('http://');
		<?php }?>

		<?php if ($_smarty_tpl->tpl_vars['weburl']->value == '') {?>
			Ext.getCmp('weburl').setValue('http://');
		<?php }?>

		<?php if ($_smarty_tpl->tpl_vars['mainwebsiteurl']->value == '') {?>
			Ext.getCmp('mainwebsiteurl').setValue('http://');
		<?php }?>

		<?php if ($_smarty_tpl->tpl_vars['macdownloadurl']->value == '') {?>
			Ext.getCmp('macdownloadurl').setValue('http://');
		<?php }?>

		<?php if ($_smarty_tpl->tpl_vars['win32downloadurl']->value == '') {?>
			Ext.getCmp('win32downloadurl').setValue('http://');
		<?php }?>

		<?php if (($_smarty_tpl->tpl_vars['onlinedesignerurl']->value == '') && ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1)) {?>
			Ext.getCmp('onlinedesignerurl').setValue('http://');
		<?php }?>

		<?php if (($_smarty_tpl->tpl_vars['onlineuiurl']->value == '') && ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1)) {?>
			Ext.getCmp('onlineuiurl').setValue('http://');
		<?php }?>

		<?php if (($_smarty_tpl->tpl_vars['onlineapiurl']->value == '') && ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1)) {?>
			Ext.getCmp('onlineapiurl').setValue('http://');
		<?php }?>

		<?php if (($_smarty_tpl->tpl_vars['onlinedesignerlogouturl']->value == '') && ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1)) {?>
			Ext.getCmp('onlinedesignerlogouturl').setValue('http://');
		<?php }?>

		<?php if (($_smarty_tpl->tpl_vars['onlinedesignerlogolinkurl']->value == '') && ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1)) {?>
			Ext.getCmp('onlinedesignerlogolinkurl').setValue('http://');
		<?php }?>

		<?php if (($_smarty_tpl->tpl_vars['onlinedesignercdnurl']->value == '') && ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1)) {?>
			Ext.getCmp('onlinedesignercdnurl').setValue('http://');
		<?php }?>

		<?php if (($_smarty_tpl->tpl_vars['onlineabouturl']->value == '') && ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1)) {?>
			Ext.getCmp('onlineabouturl').setValue('http://');
		<?php }?>

		<?php if (($_smarty_tpl->tpl_vars['onlinehelpurl']->value == '') && ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1)) {?>
			Ext.getCmp('onlinehelpurl').setValue('http://');
		<?php }?>

		<?php if (($_smarty_tpl->tpl_vars['onlinetermsandconditionsurl']->value == '') && ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1)) {?>
			Ext.getCmp('onlinetermsandconditionsurl').setValue('http://');
		<?php }?>

		

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
				fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCode');?>
",
				validateOnBlur:true,
				allowBlank: false,
				maskRe: /^\w+$/,
				maxLength: 50,
				<?php if ($_smarty_tpl->tpl_vars['brandingid']->value == 0) {?>
					readOnly: false,
					style: {textTransform: "uppercase"}
				<?php } else { ?>
					value: "<?php if ($_smarty_tpl->tpl_vars['code']->value == '') {
echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDefault');
} else {
echo $_smarty_tpl->tpl_vars['code']->value;
}?>",
					readOnly: true,
					style: 'background:#c9d8ed; textTransform: uppercase'
				<?php }?>
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
				Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_UserIdTrackingWarning');?>
", onActivateUserIDTracking);
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
	<?php if ($_smarty_tpl->tpl_vars['redactionMode']->value == 0) {?>
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPersonalDataDeletionMode');?>
 <b><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisabled');?>
</b>",
	<?php } else { ?>
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPersonalDataDeletionMode');?>
 <b><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnabled');?>
</b>",
	<?php }?>
		items: [
			new Ext.Button({
				id: 'redactionmodebutton',
				name: 'redactionmodebutton',
				<?php if ($_smarty_tpl->tpl_vars['redactionMode']->value == 0) {?>
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnable');?>
",
				<?php } else { ?>
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisable');?>
",
				<?php }?>
				minWidth: 100,
				listeners: { click: changeRedactionActiveState }
			}),
			new Ext.form.Checkbox(
			{
				boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DataDeletionAdmin');?>
",
				name: 'redactbyadmin',
				id: 'redactbyadmin',
				checked: <?php echo $_smarty_tpl->tpl_vars['redactByAdmin']->value;?>
,
				hideLabel: true,
				disabled: true,
				style: { padding: '0px 0px 2px 0px' }
			})
		]
	});


	var redactionUserConfigPanel = new Ext.Panel({
		id: 'redactionconfig',
		name: 'redactionconfig',
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUserSettings');?>
",
		items:
		[
			{
				xtype: 'radiogroup',
				columns: 1,
				autoWidth: true,
				id: 'redactionModeSelect',
				value: <?php echo $_smarty_tpl->tpl_vars['redactionMode']->value;?>
,
				listeners: { change: changeRedactionSelection },
				items:
				[
					{
						boxLabel: "Disabled",
						hidden: true,
						name: 'redactoption',
						inputValue: <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['disabled'];?>

					},
					{
						boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DataDeletionAdminOnly');?>
",
						name: 'redactoption',
						inputValue: <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['administrator'];?>

					},
					{
						boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DataDeletionRequest');?>
",
						name: 'redactoption',
						inputValue: <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['request'];?>

					},
					{
						boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DataDeletionUser');?>
",
						name: 'redactoption',
						inputValue: <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['allow'];?>

					}
				]
			},
			{
				xtype: 'radiogroup',
				columns: 1,
				autoWidth: true,
				id: 'redactionModeSelectUser',
				value: <?php echo $_smarty_tpl->tpl_vars['redactionMode']->value;?>
,
				style: { padding: '0px 0px 0px 30px' },
				listeners: { change: changeRedactionSelectionUser },
				items:
				[
					{
						boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DataDeletionUserImmediate');?>
",
						name: 'redactoptionuser',
						inputValue: <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['immediate'];?>

					},
					{
						boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DataDeletionUserDelayed');?>
",
						name: 'redactoptionuser',
						inputValue: <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['allow'];?>

					}
				]
			},
			{
				xtype: 'numberfield',
				id: 'redactionnotificationdays',
				name: 'redactionnotificationdays',
				value: <?php echo $_smarty_tpl->tpl_vars['redactionnotificationdays']->value;?>
,
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
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAutomaticDeletionSettings');?>
",
		items: [
			new Ext.form.Checkbox(
			{
				boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAutomaticDeletionEnable');?>
",
				name: 'automaticredaction',
				id: 'automaticredaction',
				checked: <?php echo $_smarty_tpl->tpl_vars['automaticredactionenabled']->value;?>
,
				hideLabel: true,
				listeners: { check: refreshRedactionSettings }
			}),
			new Ext.Container(
			{
				id: 'automaticredactionmessage',
				name: 'automaticredactionmessage',
				html: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageAutoDataDeletion');?>
",
				style: { padding: '10px 0px' }
			}),
			{
				xtype: 'numberfield',
				id: 'redactiondays',
				name: 'redactiondays',
				fieldLabel: '',
				value: <?php echo $_smarty_tpl->tpl_vars['automaticredactiondays']->value;?>
,
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

		if (selectedRedactionMode == <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['allow'];?>
)
		{
			// enable the user options
			currentRedactionOptionUser.enable();

			if (initialRedactionMode <= <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['allow'];?>
)
			{
				currentRedactionOptionUser.setValue(<?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['allow'];?>
);
			}
			else
			{
				currentRedactionOptionUser.setValue(<?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['immediate'];?>
);
			}
		}
		else
		{
			// disable the user options
			currentRedactionOptionUser.disable();
			currentRedactionOptionUser.setValue(<?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['disabled'];?>
);

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
		if (selectedRedactionMode >= <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['allow'];?>
)
		{
			var currentRedactionOptionUser = Ext.getCmp('redactionModeSelectUser');
			var currentRedactNotification = Ext.getCmp('redactionnotificationdays');

			var selectedRedactionOptionUser = currentRedactionOptionUser.getValue();

			selectedRedactionMode = selectedRedactionOptionUser.inputValue;

			if (selectedRedactionMode == <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['allow'];?>
)
			{
				// enable the notification box
				currentRedactNotification.enable();
			}
			else if (selectedRedactionMode == <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['immediate'];?>
)
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
			Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageOrderDataDeletionConfirm');?>
", function(btn)
			{
				if (btn == "yes")
				{
					orderRedactionMode = 1;

					Ext.getCmp('orderredactiondays').enable();
					Ext.getCmp('orderredactionmodebutton').setText("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisable');?>
");
					Ext.getCmp('orderredactionmodeactivate').label.update("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderDataDeletionMode');?>
 <b><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnabled');?>
</b>:");
				}
			});
		}
		else
		{
			orderRedactionMode = 0;

			Ext.getCmp('orderredactiondays').disable();
			Ext.getCmp('orderredactionmodebutton').setText("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnable');?>
");
			Ext.getCmp('orderredactionmodeactivate').label.update("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderDataDeletionMode');?>
 <b><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisabled');?>
</b>:");
		}
	}

	function changeDesktopProjectThumbnailDeletionModeState()
	{
		if (desktopThumbnailDeletionEnabled == 0)
		{
			Ext.MessageBox.minWidth = 350;
			Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDesktopProjectThumbnailDeletionConfirm');?>
", function(btn)
			{
				if (btn == "yes")
				{
					desktopThumbnailDeletionEnabled = 1;

					Ext.getCmp('ordereddesktopprojectthumbnaildeletiondays').enable();
					Ext.getCmp('desktopprojectthumbnaildeletionactivatebutton').setText("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisable');?>
");
					Ext.getCmp('desktopprojectthumbnaildeletionactivate').label.update("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDesktopProjectThumbnailDeletionMode');?>
 <b><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnabled');?>
</b>:");
				}
			});
		}
		else
		{
			desktopThumbnailDeletionEnabled = 0;

			Ext.getCmp('ordereddesktopprojectthumbnaildeletiondays').disable();
			Ext.getCmp('desktopprojectthumbnaildeletionactivatebutton').setText("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnable');?>
");
			Ext.getCmp('desktopprojectthumbnaildeletionactivate').label.update("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDesktopProjectThumbnailDeletionMode');?>
 <b><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisabled');?>
</b>:");
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
					if (selectedRedactionMode == <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['disabled'];?>
)
					{
						selectedRedactionMode = <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['administrator'];?>
;
					}

					// update the button and label text
					currentActivateButton.setText("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisable');?>
");
					currentActive.label.update("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPersonalDataDeletionMode');?>
 <b><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnabled');?>
</b>:");

					// set the administrator redaction check box
					currentRedactByAdmin.setValue(1);

					// enable the primary redaction options for the user
					currentRedactionOption.enable();

					if (selectedRedactionMode >= <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['allow'];?>
)
					{
						// if the user option is the user can trigger the redaction, enable the sub options
						// set the primary option to 'user can'
						currentRedactionOption.setValue(<?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['allow'];?>
);

						currentRedactionOptionUser.enable();
						currentRedactionOptionUser.setValue(selectedRedactionMode);

						if (selectedRedactionMode == <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['allow'];?>
)
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
			Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageAutoDataDeletionConfirm');?>
", onActivateRedactionConfirmed);
		}
		else
		{
			// currently enabled, disable the setting
			// update the button and label text
			currentActivateButton.setText("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnable');?>
");
			currentActive.label.update("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPersonalDataDeletionMode');?>
 <b><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisabled');?>
</b>:");

			// get the current redaction mode setting
			var selectedRedactionOption = currentRedactionOption.getValue();
			var selectedRedactionOptionUser = currentRedactionOptionUser.getValue();

			if (selectedRedactionOption.inputValue == <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['allow'];?>
)
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

			if (currentRedactionModeValue >= <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['allow'];?>
)
			{
				currentRedactionOption.setValue(<?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['allow'];?>
);
				currentRedactionOption.enable();

				currentRedactionOptionUser.setValue(currentRedactionModeValue);
				currentRedactionOptionUser.enable();

				if (currentRedactionModeValue == <?php echo $_smarty_tpl->tpl_vars['redactionModeOptions']->value['allow'];?>
)
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
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDataManagement');?>
",
		id:'dataDeletionTab',
		hideMode:'offsets',
		items:
		[
			{
		        xtype:'fieldset',
		        title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPersonalDataDeletion');?>
",
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
						html: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePersonalDataDeletion');?>
",
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
		        title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderDataDeletion');?>
",
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
						html: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageOrderDataDeletion');?>
",
						style: { padding: '10px 0px 5px 0px' }
					}),
					new Ext.Panel(
					{
						layout: 'form',
						id: 'orderredactionmodeactivate',
						name: 'orderredactionmodeactivate',
						<?php if ($_smarty_tpl->tpl_vars['orderredactionmode']->value == 0) {?>
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderDataDeletionMode');?>
 <b><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisabled');?>
</b>",
						<?php } else { ?>
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderDataDeletionMode');?>
 <b><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnabled');?>
</b>",
						<?php }?>
						items:
						[
							new Ext.Button(
							{
								id: 'orderredactionmodebutton',
								name: 'orderredactionmodebutton',
								<?php if ($_smarty_tpl->tpl_vars['orderredactionmode']->value == 0) {?>
								text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnable');?>
",
								<?php } else { ?>
								text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisable');?>
",
								<?php }?>
								minWidth: 100,
								listeners: { click: changeOrderRedactionActiveState }
							})
						]
					}),
					{
						xtype: 'numberfield',
						id: 'orderredactiondays',
						name: 'orderredactiondays',
						fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderDataDays');?>
',
						value: <?php echo $_smarty_tpl->tpl_vars['orderredactiondays']->value;?>
,
						post: true,
						validateOnBlur: true,
						minValue: 7,
						width: 100,
						allowBlank: false,
						allowNegative: false,
						allowDecimal: false,
						<?php if ($_smarty_tpl->tpl_vars['orderredactionmode']->value == 0) {?>
						disabled: true
						<?php }?>
					}
				]
			},
			{
		        xtype:'fieldset',
		        title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDesktopProjectThumbnailDeletion');?>
",
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
						<?php if ($_smarty_tpl->tpl_vars['desktopthumbnaildeletionenabled']->value == 0) {?>
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDesktopProjectThumbnailDeletionMode');?>
 <b><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisabled');?>
</b>",
						<?php } else { ?>
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDesktopProjectThumbnailDeletionMode');?>
 <b><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnabled');?>
</b>",
						<?php }?>
						items:
						[
							new Ext.Button(
							{
								id: 'desktopprojectthumbnaildeletionactivatebutton',
								name: 'desktopprojectthumbnaildeletionactivatebutton',
								<?php if ($_smarty_tpl->tpl_vars['desktopthumbnaildeletionenabled']->value == 0) {?>
								text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnable');?>
",
								<?php } else { ?>
								text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisable');?>
",
								<?php }?>
								minWidth: 100,
								listeners: { click: changeDesktopProjectThumbnailDeletionModeState }
							})
						]
					}),
					{
						xtype: 'displayfield',
						id: 'desktopprojectthumbnaildaystodeletiondisplayfield',
						name: 'desktopprojectthumbnaildaystodeletiondisplayfield',
						value: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderDataDays');?>
'
					},
					{
						xtype: 'numberfield',
						id: 'ordereddesktopprojectthumbnaildeletiondays',
						name: 'ordereddesktopprojectthumbnaildeletiondays',
						fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderedProjectThumbnails');?>
',
						value: <?php echo $_smarty_tpl->tpl_vars['desktopthumbnaildeletionordereddays']->value;?>
,
						post: true,
						validateOnBlur: true,
						minValue: 7,
						width: 100,
						allowBlank: false,
						allowNegative: false,
						allowDecimal: false,
						<?php if ($_smarty_tpl->tpl_vars['desktopthumbnaildeletionenabled']->value == 0) {?>
						disabled: true
						<?php }?>
					}
				]
			}
		<?php if ($_smarty_tpl->tpl_vars['optionDESOL']->value && !$_smarty_tpl->tpl_vars['optionHOLDES']->value) {?>
			,{
				xtype:'fieldset',
		        title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleManagementPolicies');?>
",
		        collapsible: false,
		        autoHeight: true,
		        defaultType: 'textfield',
		        labelWidth: 293,
				items :
		        [
					{
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleManagementPolicy');?>
",
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
								[0, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNone');?>
"]
								
									<?php
$__section_index_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['onlinedataretentionpolicyoptions']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_0_total = $__section_index_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_0_total !== 0) {
for ($__section_index_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_0_iteration <= $__section_index_0_total; $__section_index_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_0_iteration === $__section_index_0_total);
?>
										,[<?php echo $_smarty_tpl->tpl_vars['onlinedataretentionpolicyoptions']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
, "<?php echo $_smarty_tpl->tpl_vars['onlinedataretentionpolicyoptions']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"]
									<?php
}
}
?>
								
							]
						}),
						valueField: 'id',
						displayField: 'name',
						useID: true,
						value: <?php echo $_smarty_tpl->tpl_vars['onlinedataretentionpolicy']->value;?>
,
						post: true
					}
				]
			}
		<?php }?>
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
				fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOnlineDesignerURL');?>
",
				width: 505,
				<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
				value: "<?php echo $_smarty_tpl->tpl_vars['onlinedesignerurl']->value;?>
",
				<?php }?>
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
				fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAPIURL');?>
", //change
				<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
				value: "<?php echo $_smarty_tpl->tpl_vars['onlineapiurl']->value;?>
", //change
				<?php }?>
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
				fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUiURL');?>
", // change
				<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
				value: "<?php echo $_smarty_tpl->tpl_vars['onlineuiurl']->value;?>
", // change
				<?php }?>
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
				fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOnlineDesignerLogoutURL');?>
",
				width: 505,
				<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
				value: "<?php echo $_smarty_tpl->tpl_vars['onlinedesignerlogouturl']->value;?>
",
				<?php }?>
				validateOnBlur: true,
				post: true,
				validator: function(v){ return validateUrl(this); }
			},
			{
				xtype: 'textfield',
				id: 'onlinedesignercdnurl',
				name: 'onlinedesignercdnurl',
				fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOnlineDesignerCDNURL');?>
",
				width: 505,
				<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
				value: "<?php echo $_smarty_tpl->tpl_vars['onlinedesignercdnurl']->value;?>
",
				<?php }?>
				validateOnBlur: true,
				post: true,
				validator: function(v){ return validateUrl(this); }
			},
			{
				xtype: 'textfield',
				id: 'onlineabouturl',
				name: 'onlineabouturl',
				fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOnlineDesignerAboutUrl');?>
",
				width: 505,
				value: "<?php echo $_smarty_tpl->tpl_vars['onlineabouturl']->value;?>
",
				validateOnBlur: true,
				post: true,
				validator: function(v){ return validateUrl(this); }
			},
			{
				xtype: 'textfield',
				id: 'onlinehelpurl',
				name: 'onlinehelpurl',
				fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOnlineDesignerHelpUrl');?>
",
				width: 505,
				value: "<?php echo $_smarty_tpl->tpl_vars['onlinehelpurl']->value;?>
",
				validateOnBlur: true,
				post: true,
				validator: function(v){ return validateUrl(this); }
			},
			{
				xtype: 'textfield',
				id: 'onlinetermsandconditionsurl',
				name: 'onlinetermsandconditionsurl',
				fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOnlineDesignerTermsAndConditionsUrl');?>
",
				width: 505,
				value: "<?php echo $_smarty_tpl->tpl_vars['onlinetermsandconditionsurl']->value;?>
",
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
						text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDownloadAppKey');?>
",
						minWidth: 100,
						handler: function() {
							downloadKey(false);
						}
					},
					{
						xtype: 'button',
						text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGenerateAppKey');?>
",
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
						boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMultiLineBasketWorkflowEnabled');?>
",
						name: 'usemultilinebasketworkflow',
						id: 'usemultilinebasketworkflow',
						hideLabel: false,
						checked: <?php echo $_smarty_tpl->tpl_vars['usemultilinebasketworkflow']->value;?>

					})
				]
			},
		]
	};

	var generalSettingsPanel =
	{
		xtype:'fieldset',
        title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPhotoPrintsOptions');?>
",
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
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOnlineDesignerLogoLinkUrl');?>
",
						width: 505,
						value: "<?php echo $_smarty_tpl->tpl_vars['onlinedesignerlogolinkurl']->value;?>
",
						validateOnBlur: true,
						post: true,
						validator: function(v){ return validateUrl(this); }
					},
					new Ext.Button(
					{
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOnlineDesignerLogoLinkTitle');?>
",
						text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSetLinkToolTip');?>
",
						minWidth: 100,
						listeners: { click: openOnlineDesignerToolTip },
					}),
					{
						xtype: 'hidden',
						id: 'onlinedesignerlogolinktooltip',
						name: 'onlinedesignerlogolinktooltip',
						maxLength: 1024,
						value: "<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['onlinedesignerlogolinktooltip']->value, ENT_QUOTES, 'UTF-8', true);?>
"
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
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSavePromptDelay');?>
",
						<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
							value: "<?php echo $_smarty_tpl->tpl_vars['nagdelay']->value;?>
",
						<?php } else { ?>
							value: 10,
						<?php }?>
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
			
			<?php if ($_smarty_tpl->tpl_vars['allowimagescalingbefore']->value) {?>
			
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
			
			<?php }?>
			
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
							fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPerfectlyClear');?>
",
							boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAutomaticallyApplyToAllImages');?>
",
							checked: "<?php echo $_smarty_tpl->tpl_vars['automaticallyapplyperfectlyclear']->value;?>
",
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
							boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAllowUsersToToggle');?>
",
							checked: "<?php echo $_smarty_tpl->tpl_vars['allowuserstotoggleperfectlyclear']->value;?>
"
						}
					]
			})
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
						value: 1,
						id: 'useallfonts',
						checked: <?php if (is_null($_smarty_tpl->tpl_vars['selectedfontlist']->value) || -1 === $_smarty_tpl->tpl_vars['selectedfontlist']->value) {?>1<?php } else { ?>0<?php }?>,
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
						if (1 === this.getValue().value) {
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

	function unsubscribeAllCustomersConfirmation()
	{
		Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageUnsubscribeAllCustomersConfirmation');?>
", unsubscribeAllCustomers);
    };

	function unsubscribeAllCustomers(btn)
	{
		if (btn == "yes")
		{
			Ext.getCmp('unsubscribeall').disable();
			Ext.getCmp('unsubscribeallwarning').disable();
			Ext.getCmp('unsubscribeall').setTooltip("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUnsubscribeTaskInProgress');?>
");

			var paramArray = {
			<?php if ($_smarty_tpl->tpl_vars['brandingid']->value > 0 && $_smarty_tpl->tpl_vars['code']->value == '') {?>
				'code': ''
			<?php } else { ?>
				'code': Ext.getCmp('code').getValue()
			<?php }?>
			};

			Ext.taopix.formPost(Ext.getCmp('mainform'), paramArray, 'index.php?fsaction=AdminBranding.unsubscribeAllUsers', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageUpdating');?>
", onUnsubscribeAllCallback);
		}
	};

	var accountPagesURLPanel =
	{
		xtype: 'fieldset',
		columnWidth: 0.5,
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAccountPagesURL');?>
",
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
						boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisplayURL');?>
",
						name: 'defaultaccountpagesurl',
						id:'usedefaultaccountpagesurl',
						inputValue: 1,
						checked: <?php echo $_smarty_tpl->tpl_vars['usedefaultaccountpagesurl']->value;?>
,
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
										boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCustomURL');?>
",
										hideLabel: false,
										id:'usecustomaccountpagesurl',
										name: 'defaultaccountpagesurl',
										inputValue: 0,
										checked: ! (<?php echo $_smarty_tpl->tpl_vars['usedefaultaccountpagesurl']->value;?>
)
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
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDesktopDesignerSettings');?>
",
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
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCustomise');?>
",
		id: 'customiseTab',
		hideMode: 'offsets',
		items:
		[
			new Ext.form.FieldSet({
		        xtype: 'fieldset',
		        title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCustomiseOptions');?>
",
		        collapsible: false,
		        autoHeight: true,
				labelWidth: 320,
				style: 'position: relative;',
				defaults: {xtype: 'textfield'},
				layout: 'form',
		        items :
		        [
					<?php if ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1) {?>
					brandUpdateOption(<?php echo $_smarty_tpl->tpl_vars['onlineLogoType']->value;?>
, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOnlineDesignerLogo');?>
"),
					brandUpdateOption(<?php echo $_smarty_tpl->tpl_vars['onlineLogoTypeDark']->value;?>
, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOnlineDesignerLogoDark');?>
"),
					<?php }?>
					brandUpdateOption(<?php echo $_smarty_tpl->tpl_vars['controlLogoType']->value;?>
, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCustomerAccountLogo');?>
"),
					brandUpdateOption(<?php echo $_smarty_tpl->tpl_vars['marketingType']->value;?>
, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCustomerAccountSidebar');?>
"),
					brandUpdateOption(<?php echo $_smarty_tpl->tpl_vars['emailLogoType']->value;?>
, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailLogo');?>
")
				]
			}),
			new Ext.form.FieldSet({
		        xtype: 'fieldset',
		        title: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCustomiseTextOptions');?>
',
		        collapsible: false,
		        autoHeight: true,
				labelWidth: 320,
				style: 'position: relative;',
				defaults: {xtype: 'textfield'},
				layout: 'form',
		        items :
		        [
					brandTextOption('emailSignature', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailSignatureText');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailSignatureDescription');?>
")
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
					Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", msg: action.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				}

				brandFileArray[pType] = {'action': 'update', 'path': tmpFile};

				// Update the workingBrandAssetData, a non 0 value prevents the upload request message being displayed.
				workingBrandAssetData[pType] = 1;

				// Update the brand asset preview.
				updatePreviewDisplay(pType);
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
	};

	function uploadLogo(pOption)
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
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageBrandFileTypes');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
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
			   url: './?fsaction=AdminBranding.uploadBrandFile&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
',
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
					   text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpload');?>
",
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
		var recArray = <?php echo $_smarty_tpl->tpl_vars['recommended']->value;?>
;
		var maxArray = <?php echo $_smarty_tpl->tpl_vars['maximums']->value;?>
;
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
									text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdateNewBrandImage');?>
",
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
									text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonResetToSavedBrandImage');?>
",
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
									text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonRemoveBrandImage');?>
",
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
						enableCustomTextCheck(pOption, 101, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnableCustomEmailSignature');?>
"),
						useDefaultTextCheck(pOption, 101, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailSignatureUseDefault');?>
"),
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
			<?php if ($_smarty_tpl->tpl_vars['brandingid']->value == 0 || ($_smarty_tpl->tpl_vars['brandingid']->value > 0 && $_smarty_tpl->tpl_vars['code']->value != '')) {?>
				checked: (useDefault == 1)
			<?php } else { ?>
				checked: false,
				hidden: true
			<?php }?>
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

		<?php if ((($_smarty_tpl->tpl_vars['brandingid']->value == 0) || ($_smarty_tpl->tpl_vars['brandingid']->value > 0 && $_smarty_tpl->tpl_vars['code']->value != ''))) {?>
			// Existing, not default, brand, disable grid if custom signature is disabled or use default is checked.
			gridDiabled = (customTextEnabled) ? useDefault : true;
		<?php } else { ?>
			// Default Brand, disable grid if custom signature is disabled.
			gridDiabled = (! customTextEnabled);
		<?php }?>

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
				headers: {langLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLanguageName');?>
",  textLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
", deletePic: deleteImg, addPic: addimg},
				defaultText: {langBlank: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSelectLanguage');?>
",  textBlank: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTypeValue');?>
", defaultValue: "<?php echo $_smarty_tpl->tpl_vars['defaultlanguagecode']->value;?>
"},
				columnWidth: {langCol: 200, textCol: 307, delCol: 35},
				fieldWidth: {langField: 190, textField: 286},
				errorMsg: {blankValue: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTextFieldBlank');?>
"}
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
				title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSettings');?>
",
				defaults:{xtype: 'textfield'},
				items:
				[
					<?php if ($_smarty_tpl->tpl_vars['brandingid']->value == 0 || ($_smarty_tpl->tpl_vars['brandingid']->value > 0 && $_smarty_tpl->tpl_vars['code']->value != '')) {?>
					{
						xtype: 'textfield',
						id: 'name',
						name: 'name',
                        width: 605,
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelFolderName');?>
",
						listeners:{
							blur:{
								fn: validateFolder
							}
						},
						post: true,
						allowBlank: false,
						maxLength: 50
						<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
						,
						readOnly: true,
						value: "<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
"
						<?php }?>
					},
					<?php }?>
					{
						xtype: 'textfield',
						id: 'applicationname',
						name: 'applicationname',
                        width: 605,
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelApplicationName');?>
",
						<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
						value: "<?php echo $_smarty_tpl->tpl_vars['applicationname']->value;?>
",
						<?php }?>
						validateOnBlur: true,
						post: true,
						allowBlank: false
					},
					{
						xtype: 'textfield',
						id: 'displayurl',
						name: 'displayurl',
                        width: 605,
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisplayURL');?>
",
						<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
						value: "<?php echo $_smarty_tpl->tpl_vars['displayurl']->value;?>
",
						<?php }?>
						validateOnBlur: true,
						validator: function(v){ return validateUrl(this);  }
					},
					{
						xtype: 'textfield',
						id: 'weburl',
						name: 'weburl',
                        width: 605,
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelWebURL');?>
",
						<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
						value: "<?php echo $_smarty_tpl->tpl_vars['weburl']->value;?>
",
						<?php }?>
						validateOnBlur: true,
						validator: function(v){ return validateUrl(this); }
					},
					{
						xtype: 'textfield',
						id: 'mainwebsiteurl',
						name: 'mainwebsiteurl',
                        width: 605,
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMainWebsiteURL');?>
",
						<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
						value: "<?php echo $_smarty_tpl->tpl_vars['mainwebsiteurl']->value;?>
",
						<?php }?>
						validateOnBlur: true,
						post: true,
						validator: function(v){ return validateUrl(this); }
					},
					{
						xtype: 'textfield',
						id: 'macdownloadurl',
						name: 'macdownloadurl',
                        width: 605,
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMacDownloadLink');?>
",
						<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
						value: "<?php echo $_smarty_tpl->tpl_vars['macdownloadurl']->value;?>
",
						<?php }?>
						validateOnBlur: true,
						post: true,
						validator: function(v){ return validateUrl(this); }
					},
					{
						xtype: 'textfield',
						id: 'win32downloadurl',
						name: 'win32downloadurl',
                        width: 605,
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelWin32DownloadLink');?>
",
						<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
						value: "<?php echo $_smarty_tpl->tpl_vars['win32downloadurl']->value;?>
",
						<?php }?>
						validateOnBlur: true,
						post: true,
						validator: function(v){ return validateUrl(this); }
					},
					{
						xtype: 'textfield',
						id: 'supporttelephonenumber',
						name: 'supporttelephonenumber',
                        width: 605,
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSupportTelephone');?>
",
						<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
						value: "<?php echo $_smarty_tpl->tpl_vars['supporttelephonenumber']->value;?>
",
						<?php }?>
						validateOnBlur: true,
						listeners: {'blur': {fn: function(obj){CJKHalfWidthFullWidthToASCII(obj.getEl().dom, false)}}},
						post: true

					},
					{
						xtype: 'textfield',
						id: 'supportemailaddress',
						name: 'supportemailaddress',
                        width: 605,
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSupportEmail');?>
",
						<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
						value: "<?php echo $_smarty_tpl->tpl_vars['supportemailaddress']->value;?>
",
						<?php }?>
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
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDefaultCommunicationPreference');?>
",
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data:
							[
                                [1, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubscribed');?>
"],
                                [0, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUnsubscribed');?>
"]
							]
						}),
						valueField: 'id',
						displayField: 'name',
                        width: 130,
						useID: true,
						value: "<?php echo $_smarty_tpl->tpl_vars['defaultcommunicationpreference']->value;?>
",
						post: true
					}),
					<?php if ($_smarty_tpl->tpl_vars['brandingid']->value > 0) {?>
					new Ext.Button({
						id: 'unsubscribeall',
						name: 'unsubscribeall',
						text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUnsubscribeAllCustomers');?>
',
						minWidth: 100,
						disabled: hasMassUnsubscribeTaskRunning,
						fieldLabel: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUnsubscribeAll');?>
',
						listeners: { click: unsubscribeAllCustomersConfirmation },
						tooltip: str_massUnsubscribeTaskInProgress
					}),
					new Ext.Container(
					{
						id: 'unsubscribeallwarning',
						name: 'unsubscribeallwarning',
						disabled: hasMassUnsubscribeTaskRunning,
						html: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMassUnsubscribeWarning');?>
",
						style: { margin: '5px 0px 5px 233px' },
						width: 605
					}),
					<?php }?>
					<?php if (($_smarty_tpl->tpl_vars['optionms']->value) && ($_smarty_tpl->tpl_vars['owner']->value == '')) {?>
					new Ext.form.ComboBox({
						id: 'productionsitelist',
						name: 'productionsitelist',
						mode: 'local',
						editable: false,
						forceSelection: true,
						selectOnFocus: true,
						triggerAction: 'all',
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProductionSite');?>
",
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data:
							[
								
								<?php
$__section_index_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['productionsites']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_1_total = $__section_index_1_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_1_total !== 0) {
for ($__section_index_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_1_iteration <= $__section_index_1_total; $__section_index_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_1_iteration === $__section_index_1_total);
?>
									<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
										["<?php echo $_smarty_tpl->tpl_vars['productionsites']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['productionsites']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"]
									<?php } else { ?>
										["<?php echo $_smarty_tpl->tpl_vars['productionsites']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['productionsites']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
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
						useID: true,
						value: "<?php echo $_smarty_tpl->tpl_vars['productionSitesSelected']->value;?>
",
						post: true,
						listeners:
						{
							'select': function(combo, record, index)
							{
								Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageProductionSiteChanged');?>
", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.WARNING });
	    					}
						}
					}),
					<?php }?>
					new Ext.form.ComboBox({
						id: 'registerusingemail',
						name: 'registerusingemail',
						mode: 'local',
						editable: false,
						forceSelection: true,
						selectOnFocus: true,
						triggerAction: 'all',
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRegisterUsingEmail');?>
",
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data:
							[
								
								<?php
$__section_index_2_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['registerwithemailoptions']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_2_total = $__section_index_2_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_2_total !== 0) {
for ($__section_index_2_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_2_iteration <= $__section_index_2_total; $__section_index_2_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_2_iteration === $__section_index_2_total);
?>
									<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
										["<?php echo $_smarty_tpl->tpl_vars['registerwithemailoptions']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['registerwithemailoptions']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"]
									<?php } else { ?>
										["<?php echo $_smarty_tpl->tpl_vars['registerwithemailoptions']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['registerwithemailoptions']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
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
						useID: true,
						value: "<?php echo $_smarty_tpl->tpl_vars['registerusingselected']->value;?>
",
						post: true
					})
                ]
			},
			{
				title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PaymentSettings');?>
",
				defaults: {xtype: 'textfield'},
				items:
				[
					{
						xtype: 'checkboxgroup',
						columns: 1,
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPaymentMethods');?>
",
						autoWidth:true,
						style: 'margin-bottom: 10px',
						items:
						[
							<?php if ($_smarty_tpl->tpl_vars['brandingid']->value == 0 || ($_smarty_tpl->tpl_vars['brandingid']->value > 0 && $_smarty_tpl->tpl_vars['code']->value != '')) {?>
							{
								boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUseDefaultPaymentMethods');?>
",
								name: "usedefaultpaymentmethods",
								id:"usedefaultpaymentmethods",
								checked: <?php echo $_smarty_tpl->tpl_vars['usedefaultpaymentmethodschecked']->value;?>
,
								listeners: {'check': setPaymentMethods }
							},
							<?php }?>

							<?php
$__section_index_3_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['paymentmethodslist']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_3_total = $__section_index_3_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_3_total !== 0) {
for ($__section_index_3_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_3_iteration <= $__section_index_3_total; $__section_index_3_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_3_iteration === $__section_index_3_total);
?>
								<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
									{boxLabel: "<?php echo $_smarty_tpl->tpl_vars['paymentmethodslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['text'];?>
", name: "<?php echo $_smarty_tpl->tpl_vars['paymentmethodslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", id:"<?php echo $_smarty_tpl->tpl_vars['paymentmethodslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", inputValue: "<?php echo $_smarty_tpl->tpl_vars['paymentmethodslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['value'];?>
", checked: <?php echo $_smarty_tpl->tpl_vars['paymentmethodslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['selected'];?>
 }
								<?php } else { ?>
									{boxLabel: "<?php echo $_smarty_tpl->tpl_vars['paymentmethodslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['text'];?>
", name: "<?php echo $_smarty_tpl->tpl_vars['paymentmethodslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", id:"<?php echo $_smarty_tpl->tpl_vars['paymentmethodslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", inputValue: "<?php echo $_smarty_tpl->tpl_vars['paymentmethodslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['value'];?>
", checked: <?php echo $_smarty_tpl->tpl_vars['paymentmethodslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['selected'];?>
 },
								<?php }?>
							<?php
}
}
?>
						]
					},
					{
						xtype: 'radiogroup',
						columns: 1,
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPaymentIntegrations');?>
",
						autoWidth:true,
						items:
						[
    						{
    							boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNone');?>
",
    							name: 'integration',
    							id:'integrationNone',
    							inputValue: 'N',
    							listeners: {'check': setPaymentIntegration}
    						},
							<?php if ($_smarty_tpl->tpl_vars['brandingid']->value == 0 || ($_smarty_tpl->tpl_vars['brandingid']->value > 0 && $_smarty_tpl->tpl_vars['code']->value != '')) {?>
							{
								boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDefault');?>
 (<?php echo $_smarty_tpl->tpl_vars['defaultintegration']->value;?>
)",
								name: 'integration',
								id:'integrationDefault',
								inputValue: 'D',
								listeners: {'check': setPaymentIntegration}
							},
							<?php }?>
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
														
														<?php
$__section_index_4_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['integrationlist']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_4_total = $__section_index_4_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_4_total !== 0) {
for ($__section_index_4_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_4_iteration <= $__section_index_4_total; $__section_index_4_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_4_iteration === $__section_index_4_total);
?>
														<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
															["<?php echo $_smarty_tpl->tpl_vars['integrationlist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['integrationlist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"]
														<?php } else { ?>
															["<?php echo $_smarty_tpl->tpl_vars['integrationlist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['integrationlist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
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
												useID: true,
												<?php if ($_smarty_tpl->tpl_vars['brandingid']->value == 0) {?>
													value: "<?php echo $_smarty_tpl->tpl_vars['defaultintegration']->value;?>
",
												<?php } else { ?>
													value: "<?php echo $_smarty_tpl->tpl_vars['currentintegration']->value;?>
",
												<?php }?>
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
                        fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelTitleVouchersGiftCards');?>
",
						autoWidth:true,
                        xtype: 'checkbox',
                        id: 'allowvouchers',
                        name: 'allowvouchers',
                        columns: 1,
                        boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAllowVouchers');?>
",
                        <?php if ($_smarty_tpl->tpl_vars['allowvouchers']->value == 1) {?>
                            checked: true
                        <?php } else { ?>
                            checked: false
                        <?php }?>
                    },
                    {
                        xtype: 'checkbox',
                        id: 'allowgiftcards',
                        name: 'allowgiftcards',
                        columns: 1,
                        boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAllowGiftCards');?>
",
                        <?php if ($_smarty_tpl->tpl_vars['allowgiftcards']->value == 1) {?>
                            checked: true
                        <?php } else { ?>
                            checked: false
                        <?php }?>
                    }
				]
			},
			{
				title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelBrandEmailSettings');?>
",
				defaults: {xtype: 'textfield', width: 300, labelWidth: 100},
				items:
				[
					<?php if ($_smarty_tpl->tpl_vars['brandingid']->value == 0 || ($_smarty_tpl->tpl_vars['brandingid']->value > 0 && $_smarty_tpl->tpl_vars['code']->value != '')) {?>
					{
						xtype: 'checkbox',
						name: 'usedefaultemailsettings',
						id: 'usedefaultemailsettings',
						hideLabel: true,
						boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUseDefaultEmailSettings');?>
",
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
						<?php if ($_smarty_tpl->tpl_vars['usedefaultemailsettings']->value == 1) {?>, checked: true <?php }?>
					},
					<?php }?>
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
												fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSMTPAddress');?>
",
												<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
												value: "<?php echo $_smarty_tpl->tpl_vars['smtpaddress']->value;?>
",
												<?php }?>
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
												fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSMTPPort');?>
",
												<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
												value: "<?php echo $_smarty_tpl->tpl_vars['smtpport']->value;?>
",
												<?php }?>
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
                                fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSMTPType');?>
",
                                width: 280,
                                store: new Ext.data.ArrayStore({
                                    id: 0,
                                    fields: ['id', 'name'],
                                    data: [
                                        ["", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNoneSMTPType');?>
"],
                                        ["ssl", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSSLSMTPType');?>
"],
                                        ["tls", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelTLSSMTPType');?>
"]
                                    ]
                                }),
                                valueField: 'id',
                                displayField: 'name',
                                useID: true,
                                value: "<?php echo $_smarty_tpl->tpl_vars['smtptype']->value;?>
",
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
								fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSMTPAuthentication');?>
",
								listeners: { 'select': authenticationContainerDisplay },
								store: new Ext.data.ArrayStore({
									id: 0,
									fields: ['id', 'name'],
									data: [
										[0, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNone');?>
"],
										[1, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUserNamePassword');?>
"],
										[2, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOAuth');?>
"]
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
												fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUserName');?>
",
												<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
												value: "<?php echo $_smarty_tpl->tpl_vars['smtpauthuser']->value;?>
",
												<?php }?>
												validateOnBlur: true,
												post: true,
												validator: function(v){ return validateSmtpAuth(this); }
											},
											{
												xtype: 'textfield',
												id: 'smtpauthpass',
												name: 'smtpauthpass',
												inputType: 'password',
												fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPassword');?>
",
											<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
												value: "<?php echo $_smarty_tpl->tpl_vars['smtpauthpass']->value;?>
",
											<?php }?>
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
												html: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TipConfigProviders');?>
",
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
												fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOAuthProvider');?>
",
												store: new Ext.data.ArrayStore({
													id: 0,
													fields: ['id', 'name'],
													data: <?php echo $_smarty_tpl->tpl_vars['oauthproviders']->value;?>

												}),
												valueField: 'id',
												displayField: 'name',
												useID: true,
												<?php if ($_smarty_tpl->tpl_vars['brandingid']->value == 0) {?>
												value: "0",
												<?php } else { ?>
												value: "<?php echo $_smarty_tpl->tpl_vars['oauthprovider']->value;?>
",
												<?php }?>
												post: true,
												width: 250
											}),
											{
												xtype: 'textfield',
												id: 'oauthrefreshtokenid',
												name: 'oauthrefreshtokenid',
												inputType: 'password',
												value: "<?php echo $_smarty_tpl->tpl_vars['oauthtokenid']->value;?>
",
												post: true,
												hidden: true,
												width: 170
											},
											{
												xtype: 'textfield',
												id: 'oauthrefreshtoken',
												name: 'oauthrefreshtoken',
												inputType: 'password',
												fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOAuthToken');?>
",
												value: "<?php echo $_smarty_tpl->tpl_vars['oauthtoken']->value;?>
",
												post: false,
												disabled: true,
												width: 175
											},
											{
												xtype: 'button',
												id: 'oauthauthenticate',
												name: 'oauthauthenticate',
												text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAuthenticate');?>
",
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
            					title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelFrom');?>
:",
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
										fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
",
										<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
										value: "<?php echo $_smarty_tpl->tpl_vars['smtpsysfromname']->value;?>
",
										<?php }?>
										validateOnBlur: true,
										post: true,
										allowBlank: false
									},
									{
										xtype: 'textfield',
										id: 'smtpsysfromaddress',
										name: 'smtpsysfromaddress',
										fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailAddress');?>
",
										<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
										value: "<?php echo $_smarty_tpl->tpl_vars['smtpsysfromaddress']->value;?>
",
										<?php }?>
										validateOnBlur: true,
										post: true,
										allowBlank: false,
										vtype: 'email'
									}
         						]
        					},

        					{
            					xtype: 'fieldset',
            					title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelReplyTo');?>
:",
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
										fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
",
										<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
										value: "<?php echo $_smarty_tpl->tpl_vars['smtpreplyname']->value;?>
",
										<?php }?>
										validateOnBlur: true,
										post: true,
										allowBlank: false
									},
									{
										xtype: 'textfield',
										id: 'smtpreplyaddress',
										name: 'smtpreplyaddress',
										fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailAddress');?>
",
										<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
										value: "<?php echo $_smarty_tpl->tpl_vars['smtpreplyaddress']->value;?>
",
										<?php }?>
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
				title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelBrandEmails');?>
",
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
            					header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSection');?>
",
            					dataIndex: 'sectionName',
            					hidden: true
            				},
           					{
            					id: 'valueName',
            					header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
",
            					width: 100,
            					sortable: false,
            					dataIndex: 'valueName',
            					editor: new Ext.form.TextField({ validateOnBlur: true }),
            					menuDisabled: true,
            					renderer: columnRenderer
            				},
            				{
            					header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailAddress');?>
",
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
            				groupTextTpl: '{[values.rs[0].data.sectionName]} <div class="gridHeaderCheckboxHolder"><span class="gridHeaderCheckbox"><input type="checkbox" class="cbx" id="cbx_{[values.rs[0].data.sectionCode]}" onclick="activeCheckboxClicked();">&nbsp;<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
</span><div class="gridHeaderButtons"><div class="gridHeaderButton" onClick="onEmailTestRecord(\'{[values.rs[0].data.sectionCode]}\'); return false;" OnMouseOver="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\') + \' x-btn-over\';" OnMouseOut="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\');"><table cellspacing="0" class="x-btn  x-btn-icon"><tbody class="x-btn-small x-btn-icon-small-left"><tr><td class="x-btn-tl"><i>&nbsp;</i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-ml"><i>&nbsp;</i></td><td class="x-btn-mc"><em unselectable="on" class=""><button type="button" class="x-btn-text " style="background-image: url(&quot;<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/email_go.png&quot;);">&nbsp;</button></em></td><td class="x-btn-mr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-bl"><i>&nbsp;</i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i>&nbsp;</i></td></tr></tbody></table></div><div class="gridHeaderButton" onClick="onAddRecord(\'{[values.rs[0].data.sectionCode]}\'); return false;" OnMouseOver="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\') + \' x-btn-over\';" OnMouseOut="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\');"><table cellspacing="0" class="x-btn  x-btn-icon"><tbody class="x-btn-small x-btn-icon-small-left"><tr><td class="x-btn-tl"><i>&nbsp;</i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-ml"><i>&nbsp;</i></td><td class="x-btn-mc"><em unselectable="on" class=""><button type="button" class="x-btn-text " style="background-image: url(&quot;<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/add.png&quot;);">&nbsp;</button></em></td><td class="x-btn-mr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-bl"><i>&nbsp;</i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i>&nbsp;</i></td></tr></tbody></table></div></div></div>'

        					,listeners: {
        						'beforerefresh': function()
        						{
        							var isDefaultBrand = false;

        							<?php if ($_smarty_tpl->tpl_vars['brandingid']->value > 0) {?>
	        							var brandingGrid = Ext.getCmp('brandingGrid');
			        					var records = brandingGrid.selModel.getSelections();

			        					if (records[0].data.code == '')
			        					{
			        						isDefaultBrand = true;
			        					}
		        					<?php }?>

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
				title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleAdminSharing');?>
",
				id: 'pageTurningTab',
				layout: 'form',
				listeners: { 'beforeshow': function(){ Ext.getCmp('maintabpanel').doLayout(); }},
				items:[
                    {
                        xtype:'fieldset',
                        columnWidth: 0.5,
                        title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitlePageTurningPreview');?>
",
                        collapsible: false,
                        autoHeight:true,
                        defaultType: 'textfield',
                        items :[
                                    {
                                        xtype: 'textfield',
                                        id: 'previewlicensekey',
                                        name: 'previewlicensekey',
                                        fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPageTurningPreviewLicense');?>
",
                                        <?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
                                            value: "<?php echo $_smarty_tpl->tpl_vars['previewlicensekey']->value;?>
",
                                        <?php }?>
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
                                                        boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitlePageTurningPreviewLimit');?>
",
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
                                        fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNumberOfDays');?>
",
                                        <?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
                                            value: "<?php echo $_smarty_tpl->tpl_vars['previewexpiredays']->value;?>
",
                                        <?php } else { ?>
                                            value: 30,
                                        <?php }?>
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
                        title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleShareOrderedProjects');?>
",
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
                                fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareByEmailMethod');?>
",
                                store: new Ext.data.ArrayStore({
                                    id: 0,
                                    fields: ['id', 'name'],
                                    data: [
                                        [0, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareByEmailDisabled');?>
"],
                                        [1, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareByEmailTaopixControlCentre');?>
"],
                                        [2, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareByEmailMailToLink');?>
"]
                                    ]
                                }),
                                valueField: 'id',
                                displayField: 'name',
                                useID: true,
                                value: "<?php echo $_smarty_tpl->tpl_vars['sharebyemailmethod']->value;?>
",
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
                                        boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderFromPreview');?>
",
                                        name: 'orderfrompreview',
                                        id: 'orderfrompreview',
                                        hideLabel: true,
                                        <?php if ($_smarty_tpl->tpl_vars['orderfrompreview']->value == 1) {?>
                                        checked: true,
                                        <?php } else { ?>
                                        checked: false,
                                        <?php }?>
                                        post: true
                                    })
                                ]
                            })
                        ]
                    },
					{
						xtype:'fieldset',
						columnWidth: 0.5,
						title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleShareUnorderedProjects');?>
",
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
								fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleBranding');?>
",
								store: new Ext.data.ArrayStore({
									id: 0,
									fields: ['id', 'name'],
									data: [
										[0, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowBranding');?>
"],
										[1, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelHideBranding');?>
"],
									]
								}),
								valueField: 'id',
								displayField: 'name',
								useID: true,
								value: "<?php echo $_smarty_tpl->tpl_vars['sharehidebranding']->value;?>
",
								post: true,
								listeners: {
									select: togglePreviewDomainURL
								}
							}),
							{
								xtype: 'textfield',
								id: 'previewdomainurl',
								name: 'previewdomainurl',
								fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPreviewDomainURL');?>
",
								value: "<?php echo $_smarty_tpl->tpl_vars['previewdomainurl']->value;?>
",
								validateOnBlur: true,
								post: true,
								width: 424,
								validator: function(v){ return validateUrl(this); }
							}
						]
					}
                ]
			},
			<?php if ($_smarty_tpl->tpl_vars['hasonlinedesigner']->value == 1) {?>
				{
					title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleOnlineDesignerSettings');?>
",
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
			<?php }?>
			desktopDesignerSettingsTab,
			{
				title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleGoogleAnalytics');?>
",
				id: 'googleanalyticstab',
				layout: 'form',
				listeners: { 'beforeshow': function(){ Ext.getCmp('maintabpanel').doLayout(); }},
				items:
				[
					{
                        xtype:'fieldset',
                        columnWidth: 0.5,
                        title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleShoppingCartAndAccount');?>
",
                        collapsible: false,
                        autoHeight:true,
                        defaultType: 'textfield',
						items:[
							{
								xtype: 'textfield',
								id: 'googlecode',
								name: 'googlecode',
								fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGoogleAnalyticsID');?>
",
								width: 250,
								<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
								value: "<?php echo $_smarty_tpl->tpl_vars['googleanalyticscode']->value;?>
",
								<?php }?>
								validateOnBlur: true,
								post: true,
								validator: function(v){ return validateAnalytics(this); },
								listeners: { valid: enableUserIDTrackingCheckBox }
							},
							new Ext.form.Checkbox(
							{
								boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_UserIdTrackingLabel');?>
",
								name: 'useridtracking',
								id: 'useridtracking',
								disabled: true,
								checked: <?php echo $_smarty_tpl->tpl_vars['useridtrackingchecked']->value;?>
,
								hideLabel: true,
								listeners: { check: activateUserIDTracking }
							})
						]
					},
					{
                        xtype:'fieldset',
                        columnWidth: 0.5,
                        title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGoogleTagManager');?>
",
                        collapsible: false,
                        autoHeight:true,
                        defaultType: 'textfield',
						items:[
							{
								xtype: 'textfield',
								id: 'googletagmanageronlinecode',
								name: 'googletagmanageronlinecode',
								fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOnlineGoogleTagManagerID');?>
",
								width: 250,
								<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
								value: "<?php echo $_smarty_tpl->tpl_vars['googletagmanageronlinecode']->value;?>
",
								<?php }?>
								validateOnBlur: true,
								post: true,
								validator: function(v){ return validateTagManager(this); }
							},
							{
								xtype: 'textfield',
								id: 'googletagmanagercccode',
								name: 'googletagmanagercccode',
								fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCCGoogleTagManagerID');?>
",
								width: 250,
								<?php if ($_smarty_tpl->tpl_vars['brandingid']->value != 0) {?>
								value: "<?php echo $_smarty_tpl->tpl_vars['googletagmanagercccode']->value;?>
",
								<?php }?>
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
		baseParams:	{ ref: "<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
" }
	});

	gDialogObj = new Ext.Window({
		id: 'dialog',
		closable:false,
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		title: "<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
",
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
				boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
",
				post: true,
				cls: 'x-btn-left',
				ctCls: 'width_100'
			}),
			{
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
",
				handler: function(btn, ev){ gDialogObj.close(); },
				cls: 'x-btn-right'
			},
			{
				id: 'updateButton',
				<?php if ($_smarty_tpl->tpl_vars['brandingid']->value < 1) {?>
					handler: editSaveHandler,
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
",
				<?php } else { ?>
					handler: editSaveHandler,
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdate');?>
",
				<?php }?>
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
		var authUrl = '/?fsaction=AdminBranding.getAuthentication&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&provider=' + Ext.getCmp('oauthprovider').getValue();
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

	Ext.getCmp('isactive').setValue("<?php echo $_smarty_tpl->tpl_vars['activechecked']->value;?>
" == 'checked' ? true : false);

	Ext.getCmp('previewExpire').setValue(<?php echo $_smarty_tpl->tpl_vars['previewexpire']->value;?>
);

	showPreviewExpire();

	// Set up the custom brand images.
	initializeBrandAssets();
}

<?php }
}
