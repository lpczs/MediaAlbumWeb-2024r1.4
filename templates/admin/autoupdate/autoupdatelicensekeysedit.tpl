{$languagecodesjavascript}
{$languagenamesjavascript}
{$localizedcodesjavascript}
{$localizednamesjavascript}

Ext.override(Ext.form.Checkbox, {
	setBoxLabel: function(boxLabel){
		this.boxLabel = boxLabel;
		if(this.rendered){
			this.wrap.child('.x-form-cb-label').update(boxLabel);
		}
	}
});

var deleteImg = '{$webroot}/utils/ext/images/silk/delete.png';
var addimg = '{$webroot}/utils/ext/images/silk/add.png';

var str_LabelLanguageName    = "{#str_LabelLanguageName#}";
var str_localizedNameLabel   = "{#str_LabelName#}";

{literal}
var getLocalisedData = function(gLocalizedNamesArray, gLocalizedCodesArray)
{
	var langListStore = [], dataList = [];

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

		if (ArrayIndexOf(gLocalizedCodesArray, gAllLanguageCodesArray[i]) == -1)
		{
			langListStore.push([gAllLanguageCodesArray[i],gAllLanguageNamesArray[i]]);
		}
	}
	return {'langList': langListStore, 'dataList': dataList};
};

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

function refreshTaxControls()
{
	if (Ext.getCmp('taxcodedefault').checked)
	{
		Ext.getCmp('taxratelist').reset();
		Ext.getCmp('taxratelist').disable();
	}
	else
	{
		Ext.getCmp('taxratelist').enable();
	}
}

function refreshShippingTaxControls()
{
	if (Ext.getCmp('shippingtaxcodedefault').checked)
	{
		Ext.getCmp('shippingtaxratelist').reset();
		Ext.getCmp('shippingtaxratelist').disable();
	}
	else
	{
		Ext.getCmp('shippingtaxratelist').enable();
	}
}

function onlineLogoLinkToolTip()
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
			columnWidth: {langCol: 290, textCol: 600, delCol: 35},
			fieldWidth:  {langField: 290, textField: 565},
			errorMsg:    {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"}
		}
	});

	var onlineLogoLinkTooltipPanelContainer =
	{
    	xtype: 'panel',
        width: 950,
      	bodyBorder: false,
        border: false,
        hideLabel: true,
        items: onlineLogoLinkTooltipPanelPanel
	};

	var onlineLogoLinkTooltipPanel = new Ext.FormPanel(
	{
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

	var onlineLogoLinkTooltipDialog = new Ext.Window(
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
					onlineLogoLinkTooltipDialog.close();
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

					onlineLogoLinkTooltipDialog.close();
				},
				text: "{/literal}{#str_ButtonUpdate#}{literal}",
				cls: 'x-btn-right'
			}
		],
		width: 980
	});

	onlineLogoLinkTooltipDialog.show();
};

function initialize(pParams)
{
	{/literal}

	var TPX_REGISTEREDTAXNUMBERTYPE_NA = {$TPX_REGISTEREDTAXNUMBERTYPE_NA};
	var TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL = {$TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL};
	var TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE = {$TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE};

	var billingAddressTitle_tx ="{#str_LabelBillingAddress#}";
	var shippingAddressTitle_tx ="{#str_LabelShippingAddress#}";
	var shippingBCCTitle_tx ="{#str_LabelShippingBCCBillingAddress#}";
	var billingBCCTitle_tx ="{#str_LabelBillingBCCShippingAddress#}";
	var licenseKeyAddressLabel_tx = "{#str_LicenseKeyAddress#}";
	var loginTitle_tx ="{#str_LabelAuthenticate1#}";
	var loginValue = "{$login}";
	var passwordTitle_tx ="{#str_LabelAuthenticate2#}";
	var passwordValue = "{$password}";
	var showPricesWithTaxTitle_tx ="{#str_ShowPricesWithTax#}";
	var showTaxBreakdownTitle_tx ="{#str_ShowTaxBreakdown#}";
	var showZeroTaxTitle_tx ="{#str_ShowZeroTax#}";
	var showAlwaysTaxTotalTitle_tx ="{#str_ShowAlwaysTaxTotal#}";
	var paymentMethodsTitle_tx ="{#str_LabelPaymentMethods#}";
	var useDefaultPaymentMethodsTitle_tx ="{#str_LabelUseDefaultPaymentMethods#}";
	var canCreateUserAccountsTitle_tx ="{#str_LabelCanCreateUserAccounts#}";
	var useAddressForShippingTitle_tx ="{#str_LabelUseAddressForShipping#}";
	var modifyShippingAddressTitle_tx ="{#str_LabelModifyShippingAddress#}";
	var modifyShippingContactDetailsTitle_tx ="{#str_LabelModifyShippingContactDetails#}";
	var useAddressForBillingTitle_tx ="{#str_LabelUseAddressForBilling#}";
	var modifyBillingAddressTitle_tx ="{#str_LabelModifyBillingAddress#}";
	var useEmailDestinationTitle_tx ="{#str_LabelUserEmailDestination#}";
	var currencyTitle_tx ="{#str_LabelCurrency#}";
	var labelDefault_tx ="{#str_LabelDefault#}";
	var defaultCurrency_tx ="{$defaultcurrency}";
	var buttonUpdate_tx ="{#str_ButtonUpdate#}";
	var otherLabel_txt = "{#str_Other#}";
	var codeTitle_tx ="{#str_LabelCode#}";
	var brandingTitle_tx ="{#str_SectionTitleBranding#}";
    var telephonenumber_tx ="{#str_LabelTelephoneNumber#}";
    var email_tx ="{#str_LabelEmailAddress#}";
    var designerSplashScreen_tx = "{#str_LabelDesignerSplashScreen#}";
	var desktopdesignersettings_tx = "{#str_LabelDesktopDesignerSettings#}";
    var banner_tx = "{#str_LabelBanner#}";
	var accountpagesurl_tx = "{#str_LabelAccountPagesURL#}";
	var legacydesktopdesigner_tx = "{#str_LabelLegacyDesktopDesigner#}";
    var gDateFormat = "{$dateformat}";
    var onlinedesignersettings_tx = "{#str_TitleOnlineDesignerSettings#}";
	var useCustomURL_tx = "{#str_LabelCustomURL#}";
	var displayURL_tx = "{#str_LabelDisplayURL#}";
	var brandCustomURL_tx = "{#str_LabelBrandCustomURL#}";
	var promoPanel_tx = "{#str_LabelPromoPanel#}";
	var image_tx = "{#str_LabelImage#}"
	var url_tx = "{#str_LabelURL#}"
	var mode_tx = "{#str_LabelMode#}"
	var useLicenseKey_tx = "{#str_LabelUseLicenseKeyFileConfiguration#}"

    var imagescaling_txt = "{#str_LabelImageScaling#}";

    var imagescalingbefore_txt = "{#str_LabelImageScalingBefore#}";
    var imagescalingafter_txt = "{#str_LabelImageScalingAfter#}";

    var imagescalingbeforeenabled_txt = "{#str_LabelEnableImageScalingBefore#}";
    var imagescalingafterenabled_txt = "{#str_LabelEnableImageScalingAfter#}";

    var maxmegapixels_txt = "{#str_LabelMaxMegaPixels#}";

	var usedefault_txt = "{#str_LabelUseDefault#}";

	var shufflelayoutshowoption_txt = "{#str_LabelSuffleLayoutShowOption#}";
    var shufflelayout_txt = "{#str_LabelShuffleLayout#}";
    var shufflelayoutleftright_txt = "{#str_LabelLeftRightPages#}";
    var shufflelayoutspread_txt = "{#str_LabelSpread#}";
    var shufflelayoutpictures_txt = "{#str_LabelSufflePictures#}";

	var logolinkurl_txt = "{#str_LabelOnlineDesignerLogoLinkUrl#}";

	var session_id = "{$ref}";
	var taxCode = "{$taxcode}";
	var shippingTaxCode = "{$shippingtaxcode}";
	var startDate_tx = "{#str_LabelStartDate#}";
	var endDate_tx = "{#str_LabelEndDate#}";
	var startDateLaterEndDateLabel_txt = "{#str_LabelStartDateLaterEndDate#}";
	var invalidDateFormatLabel_txt = "{#str_LabelInvalidDateFormat#}";

	var imagescalingBeforeVal = "{$imagescalingbefore}";
	var imagescalingBeforeEnabledVal = ("{$imagescalingbeforeenabled}" == 'checked') ? true : false;

	var usedefaultimagescalingbeforechecked = ("{$usedefaultimagescalingbefore}" == 'checked') ? true : false;

	var imagescalingAfterVal = "{$imagescalingafter}";
	var imagescalingAfterEnabledVal = ("{$imagescalingafterenabled}" == 'checked') ? true : false;

	var usedefaultimagescalingafterchecked = ("{$usedefaultimagescalingafter}" == 'checked') ? true : false;

	var brandOnlineEditorSettings = {$onlineeditorsettings};

	var onlineDesignerLogoLinkUrlVal = "{$onlinedesignerlogolinkurl}";
	var brandOnlineDesignerLogoLinkURLs = {$onlinedesignerlogolinkurlbrands};
	var useDefaultLogoLinkUrlChecked = ("{$usedefaultlogolinkurlchecked}" == 'checked') ? true : false;

	var useDefaultAutomaticallyApplyPerfectlyClear = ({$usedefaultautomaticallyapplyperfectlyclear} == 1) ? true : false;
	var automaticallyApplyPerfectlyClear = ({$automaticallyapplyperfectlyclear} == 1) ? true : false;
	var allowUsersToTogglePerfectlyClear = ({$allowuserstotoggleperfectlyclear} == 1) ? true : false;
	{literal}
	var allUsersToToggleEnabled = true;

	if ((!useDefaultAutomaticallyApplyPerfectlyClear) && (automaticallyApplyPerfectlyClear))
	{
		allUsersToToggleEnabled = false;
	}

	{/literal}
	loginVal = "{$login}";
	passwordVal = "{$password}";
	webbrandcode = "{$webbrandcode}";
	usedefaultcurrency = "{$usedefaultcurrency}";
	currencyCode = "{$currencySelected}";
	defaultCurrency = "{$defaultcurrency}";
	licenceCode = "{$groupcode}";

    var telephonenumberVal = "{$telephonenumber}";
    var emailVal = "{$email}";

	useremaildestinationVal = "{$useremaildestination}";
	var licenseKeyAddressLabel_txt = "{#str_LicenseKeyAddress#}";
	var licenseKeySettingsLabel_txt = "{#str_LicenseKeySettings#}";
	var paymentSettingsLabel_txt = "{#str_PaymentSettings#}";
	var settingsLabel_txt = "{#str_LabelSettings#}";
	var authenticate1RequiredLabel_txt = "{#str_LabelAuthenticate1Required#}";
	licenceKeyID = "{$id}";
	var cancreateaccountsVal = ("{$cancreateaccountschecked}" == 'checked') ? true : false;
	var useaddressforshippingVal = ("{$useaddressforshippingchecked}" == 'checked') ? true : false;
	var canmodifyshippingaddressVal = ("{$canmodifyshippingaddresschecked}" == 'checked') ? true : false;
	var canmodifyshippingcontactdetailsVal = ("{$canmodifyshippingcontactdetailschecked}" == 'checked') ? true : false;
	var useaddressforbillingVal = ("{$useaddressforbillingchecked}" == 'checked') ? true : false;
	var canmodifybillingaddressVal = ("{$canmodifybillingaddresschecked}" == 'checked') ? true : false;
	var activeVal = ("{$activechecked}" == 'checked') ? true : false;
	var showpriceswithtaxchecked = ("{$showpriceswithtaxchecked}" == 'checked') ? true : false;
	var showtaxbreakdownchecked = ("{$showtaxbreakdownchecked}" == 'checked') ? true : false;
	var showzerotaxchecked = ("{$showzerotaxchecked}" == 'checked') ? true : false;
	var showalwaystaxtotalchecked = ("{$showalwaystaxtotalchecked}" == 'checked') ? true : false;
	var usedefaultpaymentmethodschecked = ("{$usedefaultpaymentmethodschecked}" == 'checked') ? true : false;
	gPaymentMethodCount = {$paymentmethodcount};
	var brandList = {$webbrandinglist};
	var currencyArray = {$currencylist};
	var paymentMethods = "{$paymentmethodshtml}";
	var emailDestArray = [['0',billingAddressTitle_tx],['1',shippingAddressTitle_tx],['2',shippingBCCTitle_tx],['3',billingBCCTitle_tx]];
	gPaymentMethodCount = {$paymentmethodcount};
	{$brandingpaymentmethodlist}
	noPaymentMethods_txt = "{#str_ErrorNoPaymentMethods#}";

	var vouchersAndGiftCards_txt = "{#str_SectionTitleVouchersGiftCards#}";
	var useDefaultSettings_txt = "{#str_LabelUseDefault#}";
	var allowVouchers_txt = "{#str_LabelAllowVouchers#}";
	var allowGiftCards_txt = "{#str_LabelAllowGiftCards#}";
	var useDefaultVoucherGiftcardSettingsChecked = ({$usedefaultvouchersettings} == 1) ? true : false;
	var allowVouchersChecked = ({$allowvouchers} == 1) ? true : false;
	var allowGiftcardsChecked = ({$allowgiftcards} == 1) ? true : false;
	var brandVoucherSettings = {$brandvouchersettings};
	var brandPerfectlyClearSettings = {$brandperfectlyclearsettings};
	var allowVouchersCheckedOriginal = ({$allowvouchers} == 1) ? true : false;
	var allowGiftcardsCheckedOriginal = ({$allowgiftcards} == 1) ? true : false;

	{* Desktop designer settings *}
	var useDefaultAccountPagesURL = ({$usedefaultaccountpagesurl} == 1);
	var accountPagesURL = "{$accountpagesurl}";
	var startDate_val = "{$splashstartdate}";
	var endDate_val = "{$splashenddate}";
	var bannerStartDate_val = "{$bannerstartdate}";
	var bannerEndDate_val = "{$bannerenddate}";
	var brandAccountPagesDefaultTypes = {$brandaccountpagesurldefaulttypes};
	var accountPagesDefaultLabel = labelDefault_tx;
	var originalPromoPanelMode = {$promopaneloverridemode};
	var originalPromoPanelStartDate = "{$promopaneloverridestartdate}";
	var originalPromoPanelEndDate = "{$promopaneloverrideenddate}";
	var originalPromoPanelURL = "{$promopaneloverrideurl}";
	var originalPromoPanelHeight = {$promopaneloverrideheight};
	var originalPromoPanelDevicePixelRatio = {$promopaneloverridepixelratio};
	var originalPromoPanelHeight = {$promopaneloverrideheight};
	var originalPromoPanelRequireHiDPI = {$promopaneloverriderequirehidpi};
	var promoPanelRequireHiDPI = originalPromoPanelRequireHiDPI;

	// if our brand is an empty string set it to the default brand string
	var brand = webbrandcode || '__DEFAULT__';

	if (brandAccountPagesDefaultTypes[brand] == 1)
	{
		accountPagesDefaultLabel += ' (' + displayURL_tx + ')';
	}
	else
	{
		accountPagesDefaultLabel += ' (' + brandCustomURL_tx + ')';
	}

	var brandImageScaling = {$imagescalingjs};
	var brandLayouts = {$layoutsjs};
	{literal}
	var gLogoUpdate = 0;
	var gLogoRemove = 0;

	var gBannerUpdate = 0;
	var gBannerRemove = 0;

	var gPromoPanelUpdate = 0;
	var gPromoPanelRemove = 0;
	var gPromoPanelDirty = 0;

	function validateFileExtension(fileName)
	{
		var exp = /^.*\.(jpg|jpeg)$/;
		return exp.test(fileName);
	}

	function validateBannerFileExtension(fileName)
	{
		var exp = /^.*\.(png)$/;
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
		Ext.getDom('previewimage').src = './?fsaction=AdminAutoUpdate.getDesignerSplashScreenImage&no=1&id={/literal}{$splashScreenAssetID}{literal}&ref={/literal}{$ref}{literal}&tmp=0&version=' + d.getTime();
		Ext.getCmp('resetLogoButton').disable();
	}

	function onRemoveBanner()
	{
		gBannerRemove = 1;
		gBannerUpdate = 0;
		Ext.getDom('bannerpreviewimage').src = 'images/admin/nopreview.gif';
		Ext.getCmp('resetBannerButton').enable();
	}

	function onResetBanner()
	{
		gBannerRemove = 0;
		gBannerUpdate = 0;
		var d = new Date();
		Ext.getDom('bannerpreviewimage').src = './?fsaction=AdminAutoUpdate.getBannerImage&no=1&id={/literal}{$bannerAssetID}{literal}&ref={/literal}{$ref}{literal}&tmp=0&version=' + d.getTime();
		Ext.getCmp('resetBannerButton').disable();
	}

	function onUploadLogo()
	{
		var theForm = Ext.getCmp('uploadform').getForm();
		theForm.submit({
			params: {code: Ext.getCmp('groupcode').value},
			scope: this,
			success: function(form,action)
			{
				Ext.getCmp('uploaddialog').close();
				var d = new Date();
				Ext.getDom('previewimage').src = './?fsaction=AdminAutoUpdate.getDesignerSplashScreenImage&no=1&id={/literal}{$splashScreenAssetID}{literal}&ref={/literal}{$ref}{literal}&tmp=1&version=' + d.getTime();
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
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorCompulsoryFields#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
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
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleWarning#}{literal}", msg: "{/literal}{#str_MessageSplashScreenFileTypes#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				return;
			}
		}
		onUploadLogo();
	}

	function onUploadBanner()
	{
		var theForm = Ext.getCmp('banneruploadform').getForm();
		theForm.submit({
			params: {code: Ext.getCmp('groupcode').value},
			scope: this,
			success: function(form,action)
			{
				Ext.getCmp('banneruploaddialog').close();
				var d = new Date();
				Ext.getDom('bannerpreviewimage').src = './?fsaction=AdminAutoUpdate.getBannerImage&no=1&id={/literal}{$bannerAssetID}{literal}&ref={/literal}{$ref}{literal}&tmp=1&version=' + d.getTime();
				if (action.result.msg != '')
				{
					Ext.MessageBox.show({ title: "{/literal}{#str_TitleWarning#}{literal}", msg: action.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				}
				gBannerUpdate = 1;
				gBannerRemove = 0;
				Ext.getCmp('resetBannerButton').enable();
			},
			failure: function(form, action)
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorCompulsoryFields#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
			},
			waitMsg: {/literal}"{#str_AlertUploading#}"{literal}
		});
	}

	function uploadBanner(btn, ev)
	{
		var fileName = Ext.getDom('bannerpreview').value.toLowerCase();
		if (!validateBannerFileExtension(fileName))
		{
			if (fileName == '')
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleWarning#}{literal}", msg: "{/literal}{#str_MessageLogoSelectImage#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				return;
			}
			else
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleWarning#}{literal}", msg: "{/literal}{#str_MessageBannerFileTypes#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				return;
			}
		}
		onUploadBanner();
	}

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
				url: './?fsaction=AdminAutoUpdate.uploadDesignerSplashScreenImage&ref={/literal}{$ref}{literal}',
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
							tag: 'div'
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
			title: "{/literal}{#str_TitleSelectSplashScreenImage#}{literal}",
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

	 function createBannerUploadDialog()
	 {
		 var bannerUploadFormPanelObj = new Ext.FormPanel({
				id: 'banneruploadform',
				frame:true,
				autoWidth: true,
				autoHeight:true,
				layout: 'column',
				bodyBorder: false,
				border: false,
				url: './?fsaction=AdminAutoUpdate.uploadBannerImage&ref={/literal}{$ref}{literal}',
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
							tag: 'div'
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
						name: 'bannerpreview',
						id: 'bannerpreview',
						inputType: 'file'
					}
				],
				buttons:
				[
					{
						text: "{/literal}{#str_ButtonCancel#}{literal}",
						id: 'cancelBannerUpload',
						handler: function(){ Ext.getCmp('banneruploaddialog').close(); }
					},
					{
						text: "{/literal}{#str_ButtonUpdate#}{literal}",
						handler: uploadBanner
					}
				]
			});

		 var gBannerUploadObj = new Ext.Window({
			id: 'banneruploaddialog',
			title: "{/literal}{#str_TitleSelectBannerImage#}{literal}",
			closable:false,
			plain:true,
			modal:true,
			draggable:true,
			resizable:false,
			bodyBorder: false,
			layout: 'fit',
			autoHeight:true,
			width: 490,
			items: bannerUploadFormPanelObj
		});

		gBannerUploadObj.show();
	}

	function onResetPromoPanel()
	{
		var hiDpiCheckbox = Ext.getCmp('promopanelhidpitoggle');
		gPromoPanelRemove = 0;
		gPromoPanelUpdate = 0;
		var d = new Date();
		Ext.getDom('promopanelpreviewimage').src = './?fsaction=AdminAutoUpdate.getPromoPanelImage&no=1&groupcode=' + licenceCode + '&ref={/literal}{$ref}{literal}&tmp=0&version=' + d.getTime();
		Ext.getCmp('resetpromopanelbutton').disable();
		hiDpiCheckbox.setValue(originalPromoPanelDevicePixelRatio > 1);
		promoPanelRequireHiDPI = originalPromoPanelRequireHiDPI;

		if (originalPromoPanelRequireHiDPI == 1)
		{
			hiDpiCheckbox.disable();
		}
		else
		{
			hiDpiCheckbox.enable();
		}
	}

	function onUploadPromoPanel()
	{
		var theForm = Ext.getCmp('promopaneluploadform').getForm();
		theForm.submit({
			params: {code: Ext.getCmp('groupcode').value},
			scope: this,
			success: function(form,action)
			{
				var hiDpiCheckbox = Ext.getCmp('promopanelhidpitoggle');
				Ext.getCmp('promopaneluploaddialog').close();
				var d = new Date();
				Ext.getDom('promopanelpreviewimage').src = './?fsaction=AdminAutoUpdate.getPromoPanelImage&no=1&groupcode=' + licenceCode + '&ref={/literal}{$ref}{literal}&tmp=1&version=' + d.getTime();

				if (action.result.hidpi == 1)
				{
					promoPanelRequireHiDPI = 1;
					// an image with hidpi only dimensions has been uploaded so check and disable the hidpi checkbox
					hiDpiCheckbox.setValue(true);
					hiDpiCheckbox.disable();
				}
				else
				{
					promoPanelRequireHiDPI = 0;
					hiDpiCheckbox.enable();
				}

				gPromoPanelRemove = 0;
				gPromoPanelUpdate = 1;
				
				Ext.getCmp('resetpromopanelbutton').enable();
			},
			failure: function(form, action)
			{
				if (action.result.msg != '')
				{
					Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: action.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });

				}
				else
				{
					Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorCompulsoryFields#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				}
			},
			waitMsg: "{/literal}{#str_AlertUploading#}{literal}"
		});
	}

	function uploadPromoPanel(btn, ev)
	{
		var fileName = Ext.getDom('promopanelpreview').value.toLowerCase();
		if (!validateFileExtension(fileName))
		{
			if (fileName == '')
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleWarning#}{literal}", msg: "{/literal}{#str_MessageLogoSelectImage#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				return;
			}
			else
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleWarning#}{literal}", msg: "{/literal}{#str_MessageSplashScreenFileTypes#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				return;
			}
		}
		onUploadPromoPanel();
	}

	function createPromoPanelUploadDialog()
	 {
		 var promoPanelUploadFormPanelObj = new Ext.FormPanel({
				id: 'promopaneluploadform',
				frame:true,
				autoWidth: true,
				autoHeight:true,
				layout: 'column',
				bodyBorder: false,
				border: false,
				url: './?fsaction=AdminAutoUpdate.uploadPromoPanelImage&ref={/literal}{$ref}{literal}',
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
							tag: 'div'
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
						name: 'promopanelpreview',
						id: 'promopanelpreview',
						inputType: 'file'
					}
				],
				buttons:
				[
					{
						text: "{/literal}{#str_ButtonCancel#}{literal}",
						id: 'cancelpromopanelupload',
						handler: function(){ Ext.getCmp('promopaneluploaddialog').close(); }
					},
					{
						text: "{/literal}{#str_ButtonUpdate#}{literal}",
						handler: uploadPromoPanel
					}
				]
			});

		 var gPromoPanelUploadObj = new Ext.Window({
			id: 'promopaneluploaddialog',
			title: "{/literal}{#str_TitleSelectPromoPanelImage#}{literal}",
			closable:false,
			plain:true,
			modal:true,
			draggable:true,
			resizable:false,
			bodyBorder: false,
			layout: 'fit',
			autoHeight:true,
			width: 490,
			items: promoPanelUploadFormPanelObj
		});

		gPromoPanelUploadObj.show();
	}

	var d = new Date();
	var gDate = d.getTime();

	function addsaveHandler()
	{
		function onSavedCallback(pUpdated, pTheForm, pActionData)
		{
			grid.store.reload();
		}

		function saveEditForm()
		{
			var usedefaultcurrency = 0;
			var currencycode = '';
			var usedefaultpaymentmethods = 0;
			var selectedPaymentMethods = "";
			var paymentmethods = '';
			var cancreateaccounts = 0;
			var showpriceswithtax = 0;
			var showtaxbreakdown = 0;
			var showzerotax = 0;
			var showalwaystaxtotal = 0;
			var useaddressforshipping = 0;
			var canmodifyshippingcontactdetails = 0;
			var canmodifyshippingaddress = 0;
			var canmodifybillingaddress = 0;
			var useaddressforbilling = 0;
			var isactive = 0;
			var registeredTaxNumberType = 0;
			var registeredTaxNumber = '';
			var usedefaultautomaticallyapplyperfectlyclear = 0;
			var automaticallyapplyperfectlyclear = 0;
			var allowuserstotoggleperfectlyclear = 0;
			var webbrandcode = Ext.getCmp('webbrandinglist').getValue();
			var useremaildestination = Ext.getCmp('useremaildestination').getValue();
	        var orderfrompreview = Ext.getCmp('orderfrompreview').getValue();
			var countryname = Ext.getCmp('countrylist').getRawValue();

			var login = Ext.getCmp('login').getValue();
	    	var password = Ext.getCmp('password').getValue();

			if (login.length > 0)
			{
				if (password != '')
				{
					if (password != "**UNCHANGED**")
					{
						password = hex_md5(password); /* ???? hex_md5  */
					}
			    }
			    else
			    {
			    	password = '';
			    }
			}

	    	if (Ext.getCmp('currCustom').checked) { usedefaultcurrency = 0;	currencycode = Ext.getCmp('currencylist').getValue();  }
	        else if (Ext.getCmp('currDefault').checked)  {	usedefaultcurrency = 1; currencycode = ""; }

	    	if (Ext.getCmp('usedefaultpaymentmethods').checked) { usedefaultpaymentmethods = 1; }
	        else {	usedefaultpaymentmethods = 0;  }

	    	if (! Ext.getCmp('usedefaultpaymentmethods').checked) {
	    		for (var i = 0; i < gPaymentMethodCount; i++){
	            	var theCheckBox = Ext.getCmp("paymentmethod" + i);
					if (theCheckBox.checked) selectedPaymentMethods = selectedPaymentMethods + theCheckBox.name + ",";
	            }
	            selectedPaymentMethods = trim(selectedPaymentMethods, ",");
	        } paymentmethods = selectedPaymentMethods;

	    	if (Ext.getCmp('cancreateaccounts').checked) cancreateaccounts = 1; else cancreateaccounts = 0;
	        if (Ext.getCmp('showpriceswithtax').checked) showpriceswithtax = 1; else showpriceswithtax = 0;
	        if (Ext.getCmp('showtaxbreakdown').checked) showtaxbreakdown = 1;   else showtaxbreakdown = 0;
	        if (Ext.getCmp('showzerotax').checked) 	showzerotax = 1; else  	showzerotax = 0;
	        if (Ext.getCmp('showalwaystaxtotal').checked)	showalwaystaxtotal = 1; else 	showalwaystaxtotal = 0;
	        if (Ext.getCmp('useaddressforshipping').checked) useaddressforshipping = 1; else useaddressforshipping = 0;
	        if (Ext.getCmp('canmodifyshippingcontactdetails').checked)	canmodifyshippingcontactdetails = 1; else canmodifyshippingcontactdetails = 0;
	        if (Ext.getCmp('canmodifyshippingaddress').checked)	canmodifyshippingaddress = 1;  else	canmodifyshippingaddress = 0;
	        if (Ext.getCmp('canmodifybillingaddress').checked)  canmodifybillingaddress = 1; else canmodifybillingaddress = 0;
	        if (Ext.getCmp('useaddressforbilling').checked) useaddressforbilling = 1; else 	useaddressforbilling = 0;
	        if (Ext.getCmp('isactive').checked) isactive = 1; else isactive = 0;

			if (Ext.getCmp('usedefaultautomaticallyapplyperfectlyclear').checked) usedefaultautomaticallyapplyperfectlyclear = 1; else usedefaultautomaticallyapplyperfectlyclear = 0;
			if (Ext.getCmp('automaticallyapplyperfectlyclear').checked) automaticallyapplyperfectlyclear = 1; else automaticallyapplyperfectlyclear = 0;
			if (Ext.getCmp('toggleperfectlyclear').checked) allowuserstotoggleperfectlyclear = 1; else allowuserstotoggleperfectlyclear = 0;

			var guestWorkflowMode = Ext.getCmp('guestworkflowcombo').getValue();

	        var paramArray = Ext.getCmp('addressForm').getAddressValues();

	        paramArray['ref'] = session_id;
	        paramArray['login'] = login;
	        paramArray['password'] = password;
	        paramArray['telephonenumber'] = Ext.getCmp('telephonenumber').getValue();
	        paramArray['email'] = Ext.getCmp('email').getValue();
	        paramArray['webbrandcode'] = webbrandcode;
	        paramArray['showpriceswithtax'] = showpriceswithtax;
	        paramArray['showtaxbreakdown'] = showtaxbreakdown;
	        paramArray['showzerotax'] = showzerotax;
	        paramArray['showalwaystaxtotal'] = showalwaystaxtotal;
	        paramArray['useaddressforshipping'] = useaddressforshipping;
	        paramArray['canmodifyshippingaddress'] = canmodifyshippingaddress;
	        paramArray['canmodifybillingaddress'] = canmodifybillingaddress;
	        paramArray['canmodifyshippingcontactdetails'] = canmodifyshippingcontactdetails;
	        paramArray['useaddressforbilling'] = useaddressforbilling;
	        paramArray['useremaildestination'] = useremaildestination;
	        paramArray['orderfrompreview'] = orderfrompreview;
	        paramArray['cancreateaccounts'] = cancreateaccounts;
	        paramArray['usedefaultcurrency'] = usedefaultcurrency;
	        paramArray['currencycode'] = currencycode;
	        paramArray['usedefaultpaymentmethods'] = usedefaultpaymentmethods;
	        paramArray['paymentmethods'] = paymentmethods;
	        paramArray['isactive'] = isactive;
	        paramArray['guestworkflowmode'] = guestWorkflowMode;

	        paramArray['usedefaultautomaticallyapplyperfectlyclear'] = usedefaultautomaticallyapplyperfectlyclear;
	        paramArray['automaticallyapplyperfectlyclear'] = automaticallyapplyperfectlyclear;
	        paramArray['toggleperfectlyclear'] = allowuserstotoggleperfectlyclear;

	        if (Ext.getCmp('taxcodecustom').checked)
			{
				if (Ext.getCmp('taxratelist').getValue() == '')
				{
					Ext.getCmp('taxratelist').markInvalid();
					return false;
				}
				else
				{
					taxCode = Ext.getCmp('taxratelist').getValue();
				}
			}
	        else if (Ext.getCmp('taxcodedefault').checked)
	        {
	        	taxCode = "";
	        }

	        if (Ext.getCmp('shippingtaxcodecustom').checked)
			{
				if (Ext.getCmp('shippingtaxratelist').getValue() == '')
				{
					Ext.getCmp('shippingtaxratelist').markInvalid();
					return false;
				}
				else
				{
					shippingTaxCode = Ext.getCmp('shippingtaxratelist').getValue();
				}
			}
	        else if (Ext.getCmp('shippingtaxcodedefault').checked)
	        {
	        	shippingTaxCode = "";
	        }

			paramArray['previewupdate'] = gLogoUpdate;
			paramArray['previewremove'] = gLogoRemove;

			paramArray['bannerupdate'] = gBannerUpdate;
			paramArray['bannerremove'] = gBannerRemove;

			paramArray['assetid'] = Ext.getCmp('assetid').getValue();
			paramArray['bannerassetid'] = Ext.getCmp('bannerassetid').getValue();

			var startdate = Ext.getCmp('startdt');
			var enddate = Ext.getCmp('enddt');
			paramArray['splashscreenstartdatevalue'] = formatPHPDate(startdate.getRawValue(), gDateFormat, "yyyy-MM-dd") + " 00:00:00";
		    paramArray['splashscreenenddatevalue'] = formatPHPDate(enddate.getRawValue(), gDateFormat, "yyyy-MM-dd") + " 23:59:59";

			var bannerstartdate = Ext.getCmp('bannerstartdt');
			var bannerenddate = Ext.getCmp('bannerenddt');

			paramArray['bannerstartdatevalue'] = formatPHPDate(bannerstartdate.getRawValue(), gDateFormat, "yyyy-MM-dd") + " 00:00:00";
		    paramArray['bannerenddatevalue'] = formatPHPDate(bannerenddate.getRawValue(), gDateFormat, "yyyy-MM-dd") + " 23:59:59";

			paramArray['taxcode'] = taxCode;
			paramArray['shippingtaxcode'] = shippingTaxCode;

			var elRegisteredTaxNumberType = Ext.getCmp('regtaxnumtype');
			var elRegisteredTaxNumber = Ext.getCmp('regtaxnum');

			if (elRegisteredTaxNumberType && elRegisteredTaxNumber)
			{
				var registeredTaxNumberType = elRegisteredTaxNumberType.getValue();
				var registeredTaxNumber = elRegisteredTaxNumber.getValue().replace(/[A-Z\-\.]+/g, "");

				if (registeredTaxNumberType == TPX_REGISTEREDTAXNUMBERTYPE_NA)
				{
					Ext.getCmp('regtaxnumtype').markInvalid();
					return false;
				}

				if (registeredTaxNumberType == TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL)
				{
					var CPF = registeredTaxNumber;

					if (CPF.length != 11 || CPF == "00000000000" || CPF == "11111111111" || CPF == "22222222222" || CPF == "33333333333" || CPF == "44444444444" || CPF == "55555555555" || CPF == "66666666666" || CPF == "77777777777" || CPF == "88888888888" || CPF == "99999999999")
					{
						Ext.getCmp('regtaxnum').markInvalid();
						return false;
					}

					add = 0;

					for (i = 0; i < 9; i++)
					{
						add += parseInt(CPF.charAt(i)) * (10 - i);
					}

					rev = 11 - (add % 11);

					if (rev == 10 || rev == 11)
					{
						rev = 0;
					}

					if (rev != parseInt(CPF.charAt(9)))
					{
						Ext.getCmp('regtaxnum').markInvalid();
						return false;
					}

					add = 0;

					for (i = 0; i < 10; i++)
					{
						add += parseInt(CPF.charAt(i)) * (11 - i);
					}

					rev = 11 - (add % 11);

					if (rev == 10 || rev == 11)
					{
						rev = 0;
					}

					if (rev != parseInt(CPF.charAt(10)))
					{
						Ext.getCmp('regtaxnum').markInvalid();
						return false;
					}
				}
				else
				{
					var CNPJ = registeredTaxNumber;
					var i = 0;
					var l = 0;
					var strNum = "";
					var strMul = "6543298765432";
					var character = "";
					var iValido = 1;
					var iSoma = 0;
					var strNum_base = "";
					var iLenNum_base = 0;
					var iLenMul = 0;
					var iSoma = 0;
					var strNum_base = 0;
					var iLenNum_base = 0;
					var taxNumberInvalid = false;


					if (CNPJ == "")
					{
						 taxNumberInvalid = true;
						 Ext.getCmp('regtaxnum').markInvalid();
						 return false;
					}

					l = CNPJ.length;

					for (i = 0; i < l; i++)
					{
						character = CNPJ.substring(i, i + 1);

						if ((character >= '0') && (character <= '9'))
						{
						   strNum = strNum + character;
						}
					};

					if (strNum.length != 14)
					{
						taxNumberInvalid = true;
						Ext.getCmp('regtaxnum').markInvalid();
						return false;
					}

					strNum_base = strNum.substring(0, 12);
					iLenNum_base = strNum_base.length - 1;
					iLenMul = strMul.length - 1;

					for (i = 0;i < 12; i++)
					{
						iSoma = iSoma + parseInt(strNum_base.substring((iLenNum_base-i),(iLenNum_base-i) + 1), 10) * parseInt(strMul.substring((iLenMul - i),(iLenMul - i) + 1), 10);
					}

					iSoma = 11 - (iSoma - Math.floor(iSoma/11) * 11);

					if (iSoma == 11 || iSoma == 10)
					{
						iSoma = 0;
					}

					strNum_base = strNum_base + iSoma;
					iSoma = 0;
					iLenNum_base = strNum_base.length - 1;

					for (i = 0; i < 13; i++)
					{
						iSoma = iSoma + parseInt(strNum_base.substring((iLenNum_base-i),(iLenNum_base-i) + 1), 10) * parseInt(strMul.substring((iLenMul-i),(iLenMul-i) + 1), 10);
					}

					iSoma = 11 - (iSoma - Math.floor(iSoma/11) * 11);

					if (iSoma == 11 || iSoma == 10)
					{
						iSoma = 0;
					}

					strNum_base = strNum_base + iSoma;

					if (strNum != strNum_base)
					{
						taxNumberInvalid = true;
						Ext.getCmp('regtaxnum').markInvalid();
						return false;
					}

					if (!taxNumberInvalid)
					{
						registeredTaxNumber = strNum;
					}
				}
			}

	        paramArray['validregisteredtaxnumbertype'] = registeredTaxNumberType;
	        paramArray['validregisteredtaxnumber'] = registeredTaxNumber;

			var usedefaultimagescalingbefore = 0;
			var imagescalingbefore = 0;
			var imagescalingbeforeenabled = 0;

		{/literal}
		{if $allowimagescalingbefore}
		{literal}
	        if (Ext.getCmp('usedefaultimagescalingbefore').checked)
	        {
	        	usedefaultimagescalingbefore = 1;
	        	imagescalingbefore = imagescalingBeforeVal;
	        	imagescalingbeforeenabled = (imagescalingBeforeEnabledVal) ? 1 : 0;
	        }
	        else
	        {
	        	imagescalingbefore = Ext.getCmp('imagescalingbefore').getValue();
	        	imagescalingbeforeenabled = (Ext.getCmp('imagescalingbeforeenabled').checked) ? 1 : 0;
	        }
		{/literal}
		{/if}
		{literal}

			paramArray['usedefaultimagescalingbefore'] = usedefaultimagescalingbefore;
	        paramArray['imagescalingbefore'] = imagescalingbefore;
	        paramArray['imagescalingbeforeenabled'] = imagescalingbeforeenabled;

			var imagescalingafter = 0;
			var usedefaultimagescalingafter = 0;
			var imagescalingafterenabled = 0;

	        if (Ext.getCmp('usedefaultimagescalingafter').checked)
	        {
	        	usedefaultimagescalingafter = 1;
	        	imagescalingafter = imagescalingAfterVal;
	        	imagescalingafterenabled = (imagescalingAfterEnabledVal) ? 1 : 0;
	        }
	        else
	        {
	        	imagescalingafter = Ext.getCmp('imagescalingafter').getValue();
	        	imagescalingafterenabled = (Ext.getCmp('imagescalingafterenabled').checked) ? 1 : 0;
	        }

	        paramArray['usedefaultimagescalingafter'] = usedefaultimagescalingafter;
	        paramArray['imagescalingafterenabled'] = imagescalingafterenabled;
	        paramArray['imagescalingafter'] = imagescalingafter;

			// Online editor


			if (Ext.getCmp('usedefaultlogolinkurl').checked)
			{
				paramArray['usedefaultlogolinkurl'] = 1;
				onlineDesignerLogoLinkURL = onlineDesignerLogoLinkUrlVal;
			}
			else
			{
				paramArray['usedefaultlogolinkurl'] = 0;
				onlineDesignerLogoLinkURL = (Ext.getCmp('onlinedesignerlogolinkurl').getValue() == 'http://') ? '' : Ext.getCmp('onlinedesignerlogolinkurl').getValue();
			}
			paramArray['onlinedesignerlogolinkurl'] = onlineDesignerLogoLinkURL;
			paramArray['onlinedesignerlogolinktooltip'] = Ext.getCmp('onlinedesignerlogolinktooltip').getValue();

			paramArray['usedefaultvouchersettings'] = (Ext.getCmp('useDefaultVoucherGiftcardSettings').checked) ? 1 : 0;
			paramArray['allowvouchers'] = (Ext.getCmp('allowVouchers').checked) ? 1 : 0;
			paramArray['allowgiftcards'] = (Ext.getCmp('allowGiftcards').checked) ? 1 : 0;

			// Font list options
			paramArray['fontlisttype'] = Ext.getCmp('fontlisttype').getValue().value;
			paramArray['fontlist'] = Ext.getCmp('fontlist').getValue();

			// Desktop Designer Settings
			var customAccountPagesURLField = Ext.getCmp('customaccountpagesurl');
			var useDefaultAccountPagesURLValue = Ext.getCmp('accountpagesurltype').getValue().inputValue;
			paramArray['usedefaultaccountpagesurl'] = useDefaultAccountPagesURLValue;

			if (useDefaultAccountPagesURLValue == 0)
			{
				if (customAccountPagesURLField.isValid() == true)
				{
					paramArray['accountpagesurl'] = customAccountPagesURLField.getValue();
				}
				else
				{
					return false;
				}
			}
			else
			{
				paramArray['accountpagesurl'] = accountPagesURL;
			}

			var promoPanelModeValue = Ext.getCmp('promopanelmode').getValue();

			if (promoPanelModeValue != 0)
			{
				var promoPanelStartDate = formatPHPDate(Ext.getCmp('promopanelstartdate').getRawValue(), gDateFormat, "yyyy-MM-dd") + " 00:00:00";
				var promoPanelEndDate = formatPHPDate(Ext.getCmp('promopanelenddate').getRawValue(), gDateFormat, "yyyy-MM-dd") + " 23:59:59";
				var promoPanelURL = Ext.getCmp('promopanelurl').getValue();
				var promoPanelHeight = Ext.getCmp('promopanelheight').getValue();
				var promoPanelDevicePixelRatio = (Ext.getCmp('promopanelhidpitoggle').getValue()) ? 2 : 1;	
			}

			if ((promoPanelModeValue != originalPromoPanelMode) || (gPromoPanelDirty == 1))
			{
				paramArray['promopaneldirty'] = 1;
			}
			else
			{
				paramArray['promopaneldirty'] = 0;
			}

			if ((originalPromoPanelMode != promoPanelModeValue) && ((promoPanelModeValue == 0) || (originalPromoPanelMode == 2)))
			{
				// always delete the promo panel if we have switched away from an image override or switched to off
				paramArray['promopanelremove'] = 1;
			}
			else
			{
				paramArray['promopanelremove'] = gPromoPanelRemove;
			}

			if (promoPanelModeValue == 2)
			{
				paramArray['promopanelupdate'] = gPromoPanelUpdate;
			}
			else
			{
				// preventing saving of any uploaded image if we have switched away from image
				paramArray['promopanelupdate'] = 0;
			}

			paramArray['promopanelmode'] = promoPanelModeValue;

			switch (promoPanelModeValue)
			{
				case 0:
					// default
					paramArray['promopanelstartdate'] = originalPromoPanelStartDate;
					paramArray['promopanelenddate'] = originalPromoPanelEndDate;
					paramArray['promopanelurl'] = originalPromoPanelURL;
					paramArray['promopanelheight'] = originalPromoPanelHeight;
					paramArray['promopaneldevicepixelratio'] = originalPromoPanelDevicePixelRatio;
					paramArray['promopanelhidpicantoggle'] = originalPromoPanelRequireHiDPI;
					break;
				case 1:
					// url
					paramArray['promopanelstartdate'] = promoPanelStartDate;
					paramArray['promopanelenddate'] = promoPanelEndDate;
					paramArray['promopanelurl'] = promoPanelURL;
					paramArray['promopanelheight'] = promoPanelHeight;
					paramArray['promopaneldevicepixelratio'] = originalPromoPanelDevicePixelRatio;
					paramArray['promopanelhidpicantoggle'] = originalPromoPanelRequireHiDPI;
					break;
				case 2:
					// image
					paramArray['promopanelstartdate'] = promoPanelStartDate;
					paramArray['promopanelenddate'] = promoPanelEndDate;
					paramArray['promopanelurl'] = originalPromoPanelURL;
					paramArray['promopanelheight'] = originalPromoPanelHeight;
					paramArray['promopaneldevicepixelratio'] = promoPanelDevicePixelRatio;
					paramArray['promopanelhidpicantoggle'] = promoPanelRequireHiDPI;
					break;
			}

	        Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminAutoUpdate.editLicenseKey&id='+licenceKeyID, 'Saving...', onSavedCallback);

			var mainPanel = Ext.getCmp('licenceUpdateEditForm');
			mainPanel.removeAll(true);
			gDialogObj.close();
		}

		if (Ext.getCmp('startdt').isValid() && Ext.getCmp('enddt').isValid() && Ext.getCmp('bannerstartdt').isValid() && Ext.getCmp('bannerenddt').isValid()
			&& Ext.getCmp('promopanelstartdate').isValid() && Ext.getCmp('promopanelenddate').isValid())
        {
            if (Ext.getCmp('licenceUpdateEditForm').getForm().isValid())
            {
                if (! Ext.getCmp('usedefaultpaymentmethods').checked)
                {
                    var somechecked = false;
                    for (var i = 0; i < gPaymentMethodCount; i++)
                    {
                        var theCheckBox = Ext.getCmp("paymentmethod" + i);
                        if (theCheckBox.checked) somechecked = true;
                    }
                    if(!somechecked)
                    {
                        Ext.MessageBox.show({ title: errorLabel_txt, msg: noPaymentMethods_txt, buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
                        return false;
                    }
                }

                if ((Ext.getCmp('currCustom').checked) && (Ext.getCmp('currencylist').getValue() == ''))
                {
                    Ext.getCmp('editLicenseKeyTabPanel').activate('paymentsTab');
                    Ext.getCmp('currencylist').markInvalid();
                    return false;
                }

				// check that the user has uploaded an image if they have switched to the image mode
				var newPromoPanelMode = Ext.getCmp('promopanelmode').getValue()

				if ((newPromoPanelMode != originalPromoPanelMode) && (newPromoPanelMode == 2))
				{
					if (gPromoPanelUpdate == 0)
					{
						Ext.MessageBox.show({ title: "{/literal}{#str_TitleWarning#}{literal}", msg: "{/literal}{#str_MessageLogoSelectImage#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
						return false;
					}
				}

                saveEditForm();
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
	}

	var init = function()
	{
		var webbrandinglistSel = Ext.getCmp('webbrandinglist');	if(webbrandinglistSel) webbrandinglistSel.setValue(webbrandcode);

		if(loginVal=='') passwordVal = '';	var passwordInput = Ext.getCmp('password');	if(passwordInput) passwordInput.setValue(passwordVal);

		var useremaildestinationSel = Ext.getCmp('useremaildestination'); if(useremaildestinationSel) useremaildestinationSel.setValue(useremaildestinationVal);

		var isactiveChb = Ext.getCmp('isactive'); if(isactiveChb) isactiveChb.setValue(activeVal);

		if (taxCode == '')
		{
			Ext.getCmp('taxcodedefault').setValue(true);
		}
		else
		{

			Ext.getCmp('taxcodecustom').setValue(true);
			Ext.getCmp('taxratelist').setValue(taxCode);
		}

		if (shippingTaxCode == '')
		{
			Ext.getCmp('shippingtaxcodedefault').setValue(true);
		}
		else
		{

			Ext.getCmp('shippingtaxcodecustom').setValue(true);
			Ext.getCmp('shippingtaxratelist').setValue(shippingTaxCode);
		}

		refreshTaxControls();

		var startdt = Ext.getCmp('startdt');
		var enddt = Ext.getCmp('enddt');

		if (startdt)
		{
			Ext.getCmp('startdt').setValue(startDate_val);
		}

		if (enddt)
		{
			Ext.getCmp('enddt').setValue(endDate_val);
		}

		var bannerstartdt = Ext.getCmp('bannerstartdt');
		var bannerenddt = Ext.getCmp('bannerenddt');

		if (bannerstartdt)
		{
			Ext.getCmp('bannerstartdt').setValue(bannerStartDate_val);
		}

		if (bannerenddt)
		{
			Ext.getCmp('bannerenddt').setValue(bannerEndDate_val);
		}

		var currCustomRb = Ext.getCmp('currCustom');
		var currDefaultRb = Ext.getCmp('currDefault');
		var currencylistSel = Ext.getCmp('currencylist');

		if (usedefaultcurrency=='1')
		{
			currDefaultRb.setValue(true);
		}
		else
		{
			currCustomRb.setValue(true);
			currencylistSel.setValue(currencyCode);
		}

		refreshLicenceSettings();
		refreshPaymentSettings();
		refreshPaymentMethods();
		refreshCurrency();
		refreshDesktopAccountPagesURlSettings();
	{/literal}
	{if $allowimagescalingbefore}
	{literal}
		refreshImageScalingBefore();
	{/literal}
	{/if}
	{if $onlinedesignerlogolinkurl == ''}
		Ext.getCmp('onlinedesignerlogolinkurl').setValue('http://');
	{/if}
	{literal}

		refreshImageScalingAfter();
		refreshOnlineDesignerLogoLinkURL();
		refreshVoucherSettings(true);
		refreshPerfectlyClearSettings();
		refreshPromoPanelSettings(Ext.getCmp('promopanelmode'), 0, originalPromoPanelMode);
	};


	function dateValidation()
	{
		var start = Ext.get('startdt');
    	var end = Ext.get('enddt');

		try
		{
    		var startVal = formatPHPDate(start.getValue(), gDateFormat, "yyyy-MM-dd").split('-');
        	var endVal = formatPHPDate(end.getValue(), gDateFormat, "yyyy-MM-dd").split('-');
        	var startDate = new Date(startVal[0],startVal[1],startVal[2]);
        	var endDate = new Date(endVal[0],endVal[1],endVal[2]);

        	if(startDate>endDate)
        	{
        		return startDateLaterEndDateLabel_txt;
        	}
        	else
        	{
        		Ext.getCmp('startdt').clearInvalid();
        		Ext.getCmp('enddt').clearInvalid();
        		return true;
        	}
		}
		catch(err)
		{
			return invalidDateFormatLabel_txt.replace('^0',gDateFormat);
		}

    	start.clearInvalid();
        end.clearInvalid();
        return true;
	};

	function bannerDateValidation()
	{
		var start = Ext.get('bannerstartdt');
    	var end = Ext.get('bannerenddt');

		try
		{
    		var startVal = formatPHPDate(start.getValue(), gDateFormat, "yyyy-MM-dd").split('-');
        	var endVal = formatPHPDate(end.getValue(), gDateFormat, "yyyy-MM-dd").split('-');
        	var startDate = new Date(startVal[0],startVal[1],startVal[2]);
        	var endDate = new Date(endVal[0],endVal[1],endVal[2]);
        	if(startDate>endDate)
        	{
        		return startDateLaterEndDateLabel_txt;
        	}
        	else
        	{
        		Ext.getCmp('bannerstartdt').clearInvalid();
        		Ext.getCmp('bannerenddt').clearInvalid();
        		return true;
        	}
		}
		catch(err)
		{
			return invalidDateFormatLabel_txt.replace('^0',gDateFormat);
		}

    	start.clearInvalid();
        end.clearInvalid();
        return true;
	};

	function promoPanelDataValidation()
	{
		var start = Ext.get('promopanelstartdate');
    	var end = Ext.get('promopanelenddate');

		try
		{
    		var startVal = formatPHPDate(start.getValue(), gDateFormat, "yyyy-MM-dd").split('-');
        	var endVal = formatPHPDate(end.getValue(), gDateFormat, "yyyy-MM-dd").split('-');
        	var startDate = new Date(startVal[0],startVal[1],startVal[2]);
        	var endDate = new Date(endVal[0],endVal[1],endVal[2]);

        	if(startDate>endDate)
        	{
        		return startDateLaterEndDateLabel_txt;
        	}
        	else
        	{
        		return true;
        	}
		}
		catch(err)
		{
			return invalidDateFormatLabel_txt.replace('^0',gDateFormat);
		}

        return true;
	}

	function refreshPaymentSettings(){
		var showpriceswithtaxCbx = Ext.getCmp('showpriceswithtax');
		var showtaxbreakdownCbx = Ext.getCmp('showtaxbreakdown');
		var showzerotaxCbx = Ext.getCmp('showzerotax');
		var showalwaystaxtotalCbx = Ext.getCmp('showalwaystaxtotal');

		if((showpriceswithtaxCbx)&&(showtaxbreakdownCbx)&&(showzerotaxCbx)&&(showalwaystaxtotalCbx)){
			if (showpriceswithtaxCbx.checked) {	showtaxbreakdownCbx.enable(); }
			else {  showtaxbreakdownCbx.setValue(true);	showtaxbreakdownCbx.disable(); }

			if (showtaxbreakdownCbx.checked) {
				showalwaystaxtotalCbx.enable();	showzerotaxCbx.enable();
			} else {
				showalwaystaxtotalCbx.setValue(false);
				showalwaystaxtotalCbx.disable();
				showzerotaxCbx.setValue(false);
				showzerotaxCbx.disable();
			}
		}
	};

	function refreshLicenceSettings(){
		var cancreateaccountsCbx = Ext.getCmp('cancreateaccounts');
		var useaddressforshippingCbx = Ext.getCmp('useaddressforshipping');
		var canmodifyshippingaddressCbx = Ext.getCmp('canmodifyshippingaddress');
		var canmodifyshippingcontactdetailsCbx = Ext.getCmp('canmodifyshippingcontactdetails');
		var useaddressforbillingCbx = Ext.getCmp('useaddressforbilling');
		var canmodifybillingaddressCbx = Ext.getCmp('canmodifybillingaddress');
		var guestworkmodeCombo = Ext.getCmp('guestworkflowcombo');

		if((cancreateaccountsCbx)&&(useaddressforshippingCbx)&&(canmodifyshippingaddressCbx)&&(canmodifyshippingcontactdetailsCbx)&&(useaddressforbillingCbx)&&(canmodifybillingaddressCbx))
		{
			if (cancreateaccountsCbx.checked)
			{
				 guestworkmodeCombo.enable();
			}
			else
			{
				guestworkmodeCombo.disable();
				guestworkmodeCombo.setValue(0);
			}

			if (useaddressforshippingCbx.checked)
			{
				canmodifyshippingaddressCbx.setValue(false);
				canmodifyshippingaddressCbx.disable();
				canmodifyshippingcontactdetailsCbx.enable();
			}
			else
			{
				canmodifyshippingaddressCbx.enable();

				if (canmodifyshippingaddressCbx.checked)
				{
					canmodifyshippingcontactdetailsCbx.setValue(true);
					canmodifyshippingcontactdetailsCbx.disable();
				}
				else
				{
					canmodifyshippingcontactdetailsCbx.enable();
				}
			}

			useaddressforbillingCbx.enable();

			if (useaddressforbillingCbx.checked)
			{
				canmodifybillingaddressCbx.setValue(false);
				canmodifybillingaddressCbx.disable();
			}
			else
			{
				canmodifybillingaddressCbx.enable();
			}
		}
    };

	function refreshPaymentMethods(){
		var usedefaultpaymentmethodsCbx = Ext.getCmp('usedefaultpaymentmethods');
		var paymentMethodsLocal = '';

		if (usedefaultpaymentmethodsCbx.checked)
		{
			var webbrandinglistSel = Ext.getCmp('webbrandinglist');
			var brand = webbrandinglistSel.getValue();
			var paymentMethods = (brandingPaymentMethodList[brand]) ? brandingPaymentMethodList[brand] : '' ;
			var paymentMethodsArray = paymentMethods.split(',');
			paymentMethodsLocal = paymentMethodsArray;
		}

		for (var i = 0, theCheckBox; i < gPaymentMethodCount; i++) {
   			theCheckBox = Ext.getCmp("paymentmethod" + i);
			if (theCheckBox)
			{
				if (usedefaultpaymentmethodsCbx.checked)
				{
					for (var j = 0; j < paymentMethodsLocal.length; j++)
					{
						if (theCheckBox.name == paymentMethodsLocal[j])
						{
							theCheckBox.setValue(true);
							break;
						}
						else
						{
							theCheckBox.setValue(false);
						}
					}
				}

				if (usedefaultpaymentmethodsCbx.checked) theCheckBox.disable(); else theCheckBox.enable();
			}
		}
    };

	function refreshVoucherSettings(pRefreshAll)
	{
		var useDefaultVoucherSettings = Ext.getCmp('useDefaultVoucherGiftcardSettings').checked;
		var currentAllowVouchersCheckbox = Ext.getCmp('allowVouchers');
		var currentAllowGiftcardsCheckbox = Ext.getCmp('allowGiftcards');

		if (useDefaultVoucherSettings)
		{
			// disable the buttons to lock them as the default
			currentAllowVouchersCheckbox.disable();
			currentAllowGiftcardsCheckbox.disable();
			//grab currently selected brand
			var webbrandinglistSel = Ext.getCmp('webbrandinglist');
			var brand = webbrandinglistSel.getValue();

			if (brand == '')
			{
				brand = '__DEFAULT__';
			}
			var voucherSettings = brandVoucherSettings[brand];
			currentAllowVouchersCheckbox.setValue(voucherSettings.allowvouchers);
			currentAllowGiftcardsCheckbox.setValue(voucherSettings.allowgiftcards);
		}
		else
		{
			if(pRefreshAll)
			{
				currentAllowVouchersCheckbox.setValue(allowVouchersCheckedOriginal);
				currentAllowGiftcardsCheckbox.setValue(allowGiftcardsCheckedOriginal);
			}
			currentAllowVouchersCheckbox.enable();
			currentAllowGiftcardsCheckbox.enable();
		}
	};

	function refreshCurrency()
	{
		var currDefaultRbn = Ext.getCmp('currDefault');
		var currCustomRbn = Ext.getCmp('currCustom');
		var currencylistSel = Ext.getCmp('currencylist');
		if (currDefaultRbn.checked)
		{
			currencylistSel.reset();
			currencylistSel.disable();
		}
		else
		{
			currencylistSel.enable();
		}
	};

	function refreshImageScalingAfter()
	{
		if (Ext.getCmp('usedefaultimagescalingafter').checked)
		{
			var webbrandinglistSel = Ext.getCmp('webbrandinglist');
			var brand = webbrandinglistSel.getValue();

			if (brand == '')
			{
				brand = '__DEFAULT__';
			}

			// default values incase the brand is not found
			var imageScalingSettings =
			{
				"enabled": false,
				"value": "0.00"
			};

			if (brandImageScaling[brand])
			{
				imageScalingSettings = brandImageScaling[brand].after
			}

			Ext.getCmp('imagescalingafterenabled').setValue(imageScalingSettings.enabled);
			Ext.getCmp('imagescalingafter').setValue(imageScalingSettings.value);
		}
	}

	{/literal}
	{if $allowimagescalingbefore}
	{literal}
	function refreshImageScalingBefore()
	{
		if (Ext.getCmp('usedefaultimagescalingbefore').checked)
		{
			var webbrandinglistSel = Ext.getCmp('webbrandinglist');
			var brand = webbrandinglistSel.getValue();

			if (brand == '')
			{
				brand = '__DEFAULT__';
			}

			// default values incase the brand is not found
			var imageScalingSettings =
			{
				"enabled": false,
				"value": "0.00"
			};

			if (brandImageScaling[brand])
			{
				imageScalingSettings = brandImageScaling[brand].before;
			}

			Ext.getCmp('imagescalingbeforeenabled').setValue(imageScalingSettings.enabled);
			Ext.getCmp('imagescalingbefore').setValue(imageScalingSettings.value);
		}
	}
	{/literal}
	{/if}
	{literal}

	function refreshPerfectlyClearSettings()
	{
		if (Ext.getCmp('usedefaultautomaticallyapplyperfectlyclear').checked)
		{
			var webbrandinglistSel = Ext.getCmp('webbrandinglist');
			var brand = webbrandinglistSel.getValue();

			if (brand == '')
			{
				brand = '__DEFAULT__';
			}

			if (brandPerfectlyClearSettings[brand])
			{
				autotomaticallyApplyPerfectlyClear = brandPerfectlyClearSettings[brand].automaticallyapplyperfectlyclear;
				allowUsersToToggle = brandPerfectlyClearSettings[brand].allowuserstotoggleperfectlyclear;

				Ext.getCmp('automaticallyapplyperfectlyclear').setValue(autotomaticallyApplyPerfectlyClear);
				Ext.getCmp('toggleperfectlyclear').setValue(allowUsersToToggle);

				 Ext.getCmp('automaticallyapplyperfectlyclear').disable();
				 Ext.getCmp('toggleperfectlyclear').disable();
			}
		}
	}

	function refreshOnlineDesignerLogoLinkURL()
	{
		if (Ext.getCmp('usedefaultlogolinkurl').checked)
		{
			var brandLogoLinkURL = '';
			var brandLogoTooltip = '';

			var webbrandinglistSel = Ext.getCmp('webbrandinglist');
			var brand = webbrandinglistSel.getValue();

			if (brand == '')
			{
				brand = '__DEFAULT__';
			}

			if (brandImageScaling[brand])
			{
				brandLogoLinkURL = brandOnlineDesignerLogoLinkURLs[brand].before.url;
				brandLogoTooltip = brandOnlineDesignerLogoLinkURLs[brand].before.tooltip.string;
				gLocalizedCodesArray = eval(brandOnlineDesignerLogoLinkURLs[brand].before.tooltip.codes);
				gLocalizedNamesArray = eval(brandOnlineDesignerLogoLinkURLs[brand].before.tooltip.names);
			}

			Ext.getCmp('onlinedesignerlogolinkurl').setValue(brandLogoLinkURL);
			Ext.getCmp('onlinedesignerlogolinktooltip').setValue(brandLogoTooltip);
		}
	}

	var refreshDesktopAccountPagesURlSettings = function()
	{
		var defaultChecked = Ext.getCmp('usedefaultaccountpagesurl').checked;
		var accountPagesURLEditField = Ext.getCmp('customaccountpagesurl');

		if (defaultChecked)
		{
			accountPagesURLEditField.reset();

			// disable the url edit field
			accountPagesURLEditField.disable();
		}
		else
		{
			accountPagesURLEditField.reset();

			// enable and clear the url edit field
			if (accountPagesURL == '')
			{
				accountPagesURLEditField.setValue("https://");
			}

			accountPagesURLEditField.enable();
		}
	}

	refreshDefaultAccountPagesURLLabel = function()
	{
		var accountPagesDefaultLabel = labelDefault_tx;
		var brand = Ext.getCmp('webbrandinglist').getValue();

		// if our brand is an empty string set it to the default brand string
		brand = brand || '__DEFAULT__';

		if (brandAccountPagesDefaultTypes[brand] == 1)
		{
			accountPagesDefaultLabel += ' (' + displayURL_tx + ')';
		}
		else
		{
			accountPagesDefaultLabel += ' (' + brandCustomURL_tx + ')';
		}

		Ext.getCmp('usedefaultaccountpagesurl').setBoxLabel(accountPagesDefaultLabel);
	}

	function loginValidation()
	{

		if  (((Ext.getCmp('login').getValue().length==0) && (Ext.getCmp('password').getValue().length==0)) || ((Ext.getCmp('login').getValue().length>0)))
		{
			Ext.getCmp('login').clearInvalid(); Ext.getCmp('password').clearInvalid();
			return true;
		}
		else
		{
			return authenticate1RequiredLabel_txt;
		}
	};

    function requiredValidation(v, text){

		if  ( v != '')
		{
			return true;
		}
		else
		{
			return text;
		}
	};

	var refreshPromoPanelSettings = function(promoPanelComboBoxComponent, newValue, index)
	{
		var startDateComponent = Ext.getCmp('promopanelstartdate');
		var endDateComponent = Ext.getCmp('promopanelenddate');
		var urlComponent = Ext.getCmp('promopanelurl');
		var previewImageComponent = Ext.getCmp('promopanelimagecontainer');
		var heightComponent = Ext.getCmp('promopanelheight');

		switch (index)
		{
			case 0:
				urlComponent.disable();
				urlComponent.hide();
				heightComponent.disable();
				heightComponent.hide();
				startDateComponent.disable();
				startDateComponent.hide();
				endDateComponent.disable();
				endDateComponent.hide();
				previewImageComponent.hide();
				previewImageComponent.disable();
				break;
			case 1:
				if (urlComponent.value == "")
				{
					urlComponent.setValue("https://");
				}

				urlComponent.enable();
				urlComponent.show();
				heightComponent.enable();
				heightComponent.show();
				startDateComponent.enable();
				startDateComponent.show();
				endDateComponent.enable()
				endDateComponent.show();
				previewImageComponent.hide();
				break;
			case 2:
				urlComponent.disable();
				urlComponent.hide();
				heightComponent.disable();
				heightComponent.hide();
				startDateComponent.enable();
				startDateComponent.show();
				endDateComponent.enable()
				endDateComponent.show();
				previewImageComponent.show();
				previewImageComponent.enable();
				break;
		}
	}

	var guestWorkflowDataStore = new Ext.data.ArrayStore({
		id: 'guestworkflowstore',
		fields: ['id', 'name'],
		data: [
			{/literal}
			{section name=index loop=$guestworkflowdata}
			{if $smarty.section.index.last}
				["{$guestworkflowdata[index].id}", "{$guestworkflowdata[index].name}"]
			{else}
				["{$guestworkflowdata[index].id}", "{$guestworkflowdata[index].name}"],
			{/if}
			{/section}
			{literal}
		]
	});

    var guestWorkflowCombo = new Ext.form.ComboBox({
		id: 'guestworkflowcombo',
		name: 'guestworkflowcombo',
		width:300,
		labelWidth: 150,
		fieldLabel: "{/literal}{#str_MessageGuestWorkflowMode#}{literal}",
		mode: 'local',
		editable: false,
		hideLabel: false,
		forceSelection: true,
		selectOnFocus: true,
		triggerAction: 'all',
		store: guestWorkflowDataStore,
		valueField: 'id',
		displayField: 'name',
		useID: true,
		value: "{/literal}{$guestworkflowmode}{literal}",
		allowBlank: false,
		post: true
	});

	var licenceAddressTab = {
		title: licenseKeyAddressLabel_tx, hideMode:'offsets', defaults:{xtype: 'textfield', width: 230, labelWidth: 200},
		listeners: { 'beforeshow': function(){ Ext.getCmp('editLicenseKeyTabPanel').doLayout(); }},
		items: [
		    new Ext.taopix.AddressPanel({
			id: 'addressForm',
			options: {
				ref: {/literal}{$ref}{literal},
				editMode: 0,
				strict: 1,
				fieldWidth: 230
			},
	        data: {
			{/literal}
				countryCode:"{$country}",
                contactFirstName : "{$contactfname}",
                contactLastName: "{$contactlname}",
				companyName:"{$companyname}",
				address1:"{$address1}",
				address2:"{$address2}",
				address3:"{$address3}",
				address4:"{$address4}",
				add41:"{$add41}",
				add42:"{$add42}",
				add43:"{$add43}",
				city:"{$city}",
				countyName: "{$county}",
	            countyCode: "{$regioncode}",
	            stateName: "{$state}",
	            stateCode: "{$regioncode}",
	            postCode: "{$postcode}",
	            regtaxnumtype:"{$registeredtaxnumbertype}",
				regtaxnum:"{$registeredtaxnumber}"
			{literal}
			}
			}),
            {
                id: 'telephonenumber',
                name: 'telephonenumber',
                fieldLabel: telephonenumber_tx,
                value: telephonenumberVal,
                post: true,
                validateOnBlur:true,
                labelStyle: 'width: 202px;',
                listeners: {'blur': {fn: function(obj){CJKHalfWidthFullWidthToASCII(obj.getEl().dom, false)}}},
                validator: function(v){ return requiredValidation(v, "{/literal}{#str_MessageCompulsoryPhoneMandatory#}{literal}");}
            },
            {
                id: 'email',
                name: 'email',
                fieldLabel: email_tx,
                value: emailVal,
                post: true,
                validateOnBlur:true,
                labelStyle: 'width: 202px;',
                validator: function(v){
                    if( validateEmailAddress(v)){
                        return true;
                    } else {
                        return "{/literal}{#str_MessageCompulsoryEmaiInvalid#}{literal}";
                    }
                }
            }
        ]
	};

	var licenceSettings = { title: licenseKeySettingsLabel_txt, id:'settingsTab', hideMode:'offsets', items: [
		new Ext.Panel({ layout: 'form',  autoWidth:true, defaults:{xtype: 'textfield', width: 270, labelWidth: 150},
		items: [
			new Ext.Panel({ layout: 'column', columns: 2,  autoWidth:true, defaults:{labelWidth: 130}, style:'padding-top:7px; padding-bottom:10px;border-bottom:1px solid #ccc; margin-bottom:7px;',
				items: [
					new Ext.Panel(
                    {
                    layout: 'form',
                    defaults:{xtype: 'textfield', width: 140},
                    width:333,
					items: [
                        { id: 'login',
                        name: 'login',
                        inputType: 'password',
                        fieldLabel: loginTitle_tx,
                        value: loginVal,
                        post: true,
                        validateOnBlur:true,
                        labelStyle: 'text-align:right;',
                        validator: function(v){ return loginValidation();}
                        }
                    ]}),
					new Ext.Panel({
                        layout: 'form',
                        defaults:{xtype: 'textfield', width: 140},
                        style:'padding-left:15',
                        width:333,
                        items: [
                            { id: 'password',
                            name: 'password',
                            inputType: 'password',
                            fieldLabel: passwordTitle_tx,
                            value: passwordVal,
                            post: true,
                            validateOnBlur:true,
                            labelStyle: 'text-align:right;',
                            validator: function(v){ return loginValidation(); }
                            }
                        ]
                   })
				]}),

				new Ext.form.CheckboxGroup({columns: 1,  autoWidth:true, layout:'column', style:'margin-top:10px;margin-bottom:10px',hideLabel:true,
					items: [
						new Ext.form.Checkbox({boxLabel: canCreateUserAccountsTitle_tx, name: 'cancreateaccounts', id: 'cancreateaccounts',hideLabel:true, checked: cancreateaccountsVal, listeners: { check: refreshLicenceSettings }}),
						new Ext.form.Checkbox({boxLabel: useAddressForShippingTitle_tx, name: 'useaddressforshipping', id: 'useaddressforshipping',hideLabel:true, checked: useaddressforshippingVal, listeners: { check: refreshLicenceSettings }}),
						new Ext.form.Checkbox({boxLabel: modifyShippingAddressTitle_tx, name: 'canmodifyshippingaddress', id: 'canmodifyshippingaddress',hideLabel:true, style:'margin-left:17px', checked: canmodifyshippingaddressVal, listeners: { check: refreshLicenceSettings }}),
						new Ext.form.Checkbox({boxLabel: modifyShippingContactDetailsTitle_tx, name: 'canmodifyshippingcontactdetails', id: 'canmodifyshippingcontactdetails',hideLabel:true, style:'margin-left:17px', checked: canmodifyshippingcontactdetailsVal, listeners: { check: refreshLicenceSettings }}),
						new Ext.form.Checkbox({boxLabel: useAddressForBillingTitle_tx, name: 'useaddressforbilling', id: 'useaddressforbilling',hideLabel:true, checked: useaddressforbillingVal, listeners: { check: refreshLicenceSettings }}),
						new Ext.form.Checkbox({boxLabel: modifyBillingAddressTitle_tx, name: 'canmodifybillingaddress', id: 'canmodifybillingaddress',hideLabel:true, style:'margin-left:17px', checked: canmodifybillingaddressVal, listeners: { check: refreshLicenceSettings }})
					]
				}),

				new Ext.Panel({
                    layout: 'form',
                    defaults:{xtype: 'combo', width: 300, labelWidth: 150},
                    autoWidth:true,
                    labelWidth: 150,
                    items: [
                        {
                            xtype: 'combo',
                            id: 'useremaildestination',
                            name: 'useremaildestination',
                            hiddenName:'useremaildestination_hn',
                            validationEvent: false,
                            displayField: 'email_name',
                            mode: 'local',
                            editable: false,
                            forceSelection: true,
                            hiddenId:'useremaildestination_hi',
                            valueField: 'email_id',
                            useID: true,
                            post: true,
                            fieldLabel:useEmailDestinationTitle_tx,
                            store: new Ext.data.ArrayStore({
                                id: 0,
                                fields: ['email_id', 'email_name'],	data: emailDestArray
                            }),
                            allowBlank: false,
                            triggerAction: 'all',
                            labelWidth:150
                        },
						guestWorkflowCombo,
                            new Ext.form.ComboBox({
                                id: 'orderfrompreview',
                                name: 'orderfrompreview',
                                width:300,
                                labelWidth: 150,
                                fieldLabel: "{/literal}{#str_LabelOrderFromPreview#}{literal}",
                                mode: 'local',
                                editable: false,
                                hideLabel: false,
                                forceSelection: true,
                                selectOnFocus: true,
                                triggerAction: 'all',
                                store: new Ext.data.ArrayStore({
                                           id: 0,
                                           fields: ['id', 'name'],
                                           data: [
                                               [2, "{/literal}{#str_LabelDefault#}{literal}"],
                                               [1, "{/literal}{#str_LabelYes#}{literal}"],
                                               [0, "{/literal}{#str_LabelNo#}{literal}"]
                                           ]
                                       }),
                                valueField: 'id',
                                displayField: 'name',
                                useID: true,
                                value: "{/literal}{$orderfrompreview}{literal}",
                                allowBlank: false,
                                post: true
                            })
				]})
			]})
		]
	};

	var paymentOptions = [];
	var paymentMethod = paymentMethods.split(';;');
	for(var i=0, elem; i < paymentMethod.length; i++)
	{
		elem = eval(paymentMethod[i]);
		paymentOptions.push(elem);
	}

	var paymentSettings =
	{
		title: paymentSettingsLabel_txt,
		defaults:{xtype: 'textfield', labelWidth: 200},
		id:'paymentsTab',
		hideMode:'offsets',
		listeners: { 'beforeshow': function(){ Ext.getCmp('editLicenseKeyTabPanel').doLayout(); }},
		items: [
			new Ext.Panel(
			{
				layout: 'form',
				width: 845,
				items:
				[
					{
						xtype: 'radiogroup',
						columns: 1,
						fieldLabel: currencyTitle_tx,
						autoWidth:true,
						style:'margin-top:5px; ',
						items:
						[
							{
								boxLabel: labelDefault_tx+' ('+defaultCurrency+')',
								name: 'currency',
								inputValue: 'D',
								id: 'currDefault',
								listeners: { check: refreshCurrency }
							},
							{
								xtype : 'container',
								border : false,
								layout : 'column',
								autoHeight:true,
								width:400,
								items :
								[
									{
										xtype : 'container',
										layout : 'form',
										style:'margin-right:10px',
										width:70,
										items: new Ext.form.Radio({ hideLabel:true, boxLabel: otherLabel_txt, name: 'currency', inputValue: 'C', id: 'currCustom', listeners: { check: refreshCurrency } })
									},
									{
										xtype : 'container',
										layout : 'form',
										width:400,
										items : new Ext.form.ComboBox({ id: 'currencylist', name: 'currencylist', hiddenName:'currencylist_hn',	hiddenId:'currencylist_hi',	mode: 'local', editable: false,
				   							forceSelection: true,width:300, valueField: 'currency_id', displayField: 'currency_name', useID: true, post: true, hideLabel:true,	allowBlank: false,
											store: new Ext.data.ArrayStore({ id: 0, fields: ['currency_id', 'currency_name'],	data: currencyArray }), triggerAction: 'all', validationEvent:false	})
										}
								]
							}
						]
					},
					{
									xtype: 'radiogroup',
									columns: 1,
									fieldLabel: '{/literal}{#str_LabelTaxCode#}{literal}',
									autoWidth:true,
									style:'margin-top:5px; ',
									items:
									[
										{
											boxLabel: '{/literal}{#str_LabelDefault#}{literal}',
											name: 'taxcode',
											inputValue: 'D',
											id: 'taxcodedefault',
											listeners: {check: refreshTaxControls}
										},
										{
											xtype : 'container',
											border : false,
											layout : 'column',
											autoHeight:true,
											width:300,
											items :
											[
												{
													xtype : 'container',
													layout : 'form',
													style:'margin-right:10px',
													width:70,
													items: new Ext.form.Radio({ hideLabel:true, boxLabel: '{/literal}{#str_Other#}{literal}', name: 'taxcode', inputValue: 'C', id: 'taxcodecustom', listeners: {check: refreshTaxControls}})
												},
												{
													xtype : 'container',
													layout : 'form',
													width:400,
													items : new Ext.form.ComboBox({ id: 'taxratelist', name: 'taxratelist', hiddenName:'taxratelist_hn',	hiddenId:'taxratelist_hn',	mode: 'local', editable: false,
														forceSelection: true, width:300, valueField: 'taxrate_id', displayField: 'taxrate_name', useID: true, post: true, hideLabel:true,	allowBlank: false,
														store: new Ext.data.ArrayStore({ id: 0,
															fields: ['taxrate_id', 'taxrate_name'],
															data: [
																	{/literal}
																	{section name=index loop=$taxcodelist}
																	{if $smarty.section.index.last}
																		["{$taxcodelist[index].code}", "{$taxcodelist[index].name}"]
																	{else}
																		["{$taxcodelist[index].code}", "{$taxcodelist[index].name}"],
																	{/if}
																	{/section}
																	{literal}
																]
														}),
														triggerAction: 'all',
														validationEvent:false})
													}
											]
										}
									]
								},
								{
									xtype: 'radiogroup',
									columns: 1,
									fieldLabel: '{/literal}{#str_LabelShippingTaxCode#}{literal}',
									autoWidth:true,
									style:'margin-top:5px; ',
									items:
									[
										{
											boxLabel: '{/literal}{#str_LabelDefault#}{literal}',
											name: 'shippingtaxcode',
											inputValue: 'D',
											id: 'shippingtaxcodedefault',
											listeners: {check: refreshShippingTaxControls}
										},
										{
											xtype : 'container',
											border : false,
											layout : 'column',
											autoHeight:true,
											width:300,
											items :
											[
												{
													xtype : 'container',
													layout : 'form',
													style:'margin-right:10px',
													width:70,
													items: new Ext.form.Radio({ hideLabel:true, boxLabel: '{/literal}{#str_Other#}{literal}', name: 'shippingtaxcode', inputValue: 'C', id: 'shippingtaxcodecustom', listeners: {check: refreshShippingTaxControls}})
												},
												{
													xtype : 'container',
													layout : 'form',
													width:400,
													items : new Ext.form.ComboBox({ id: 'shippingtaxratelist', name: 'shippingtaxratelist', hiddenName:'shippingtaxratelist_hn',	hiddenId:'shippingtaxratelist_hn',	mode: 'local', editable: false,
														forceSelection: true, width:300, valueField: 'shippingtaxrate_id', displayField: 'shippingtaxrate_name', useID: true, post: true, hideLabel:true,	allowBlank: false,
														store: new Ext.data.ArrayStore({ id: 0,
															fields: ['shippingtaxrate_id', 'shippingtaxrate_name'],
															data: [
																	{/literal}
																	{section name=index loop=$taxcodelist}
																	{if $smarty.section.index.last}
																		["{$taxcodelist[index].code}", "{$taxcodelist[index].name}"]
																	{else}
																		["{$taxcodelist[index].code}", "{$taxcodelist[index].name}"],
																	{/if}
																	{/section}
																	{literal}
																]
														}),
														triggerAction: 'all',
														validationEvent:false})
													}
											]
										}
									]
								},

					new Ext.form.CheckboxGroup({columns: 2, fieldLabel: settingsLabel_txt, width:500, layout:'column', id:'paymentCustomSettings', style:'margin-top:10px; margin-bottom:10px',
						items:
						[
							new Ext.form.Checkbox({boxLabel: showPricesWithTaxTitle_tx, name: 'showpriceswithtax', id: 'showpriceswithtax', checked:showpriceswithtaxchecked, listeners: { check: refreshPaymentSettings }}),
							new Ext.form.Checkbox({boxLabel: showTaxBreakdownTitle_tx, name: 'showtaxbreakdown', id: 'showtaxbreakdown',checked:showtaxbreakdownchecked, listeners: { check: refreshPaymentSettings }}),
							new Ext.form.Checkbox({boxLabel: showZeroTaxTitle_tx, name: 'showzerotax', id: 'showzerotax', checked:showzerotaxchecked, listeners: { check: refreshPaymentSettings }}),
							new Ext.form.Checkbox({boxLabel: showAlwaysTaxTotalTitle_tx, name: 'showalwaystaxtotal', id: 'showalwaystaxtotal', checked:showalwaystaxtotalchecked, listeners: { check: refreshPaymentSettings }})
						]
					}),
					new Ext.form.Checkbox({boxLabel: useDefaultPaymentMethodsTitle_tx, name: 'usedefaultpaymentmethods', id: 'usedefaultpaymentmethods', checked:usedefaultpaymentmethodschecked, fieldLabel:paymentMethodsTitle_tx, listeners: { check: refreshPaymentMethods } }),
					new Ext.form.CheckboxGroup({id:'paymentMethodsHolder', columns: 2, width:500, layout:'column',	items: paymentOptions, style:'margin-bottom:10px'}),
					new Ext.form.Hidden({id:'paswHd', name:'paswHd', value:''}),
					new Ext.form.Checkbox({boxLabel: useDefaultSettings_txt, name: 'useDefaultVoucherGiftcardSettings', id: 'useDefaultVoucherGiftcardSettings', checked:useDefaultVoucherGiftcardSettingsChecked, fieldLabel:vouchersAndGiftCards_txt, listeners: { check: function(){refreshVoucherSettings(true);} } }),
					new Ext.form.CheckboxGroup({columns: 2, width:500, layout:'column', id:'vouchersGiftcardSettings', style:'margin-bottom:10px',
						items:
						[
							new Ext.form.Checkbox({boxLabel: allowVouchers_txt, name: 'allowVouchers', id: 'allowVouchers',checked:allowVouchersChecked}),
							new Ext.form.Checkbox({boxLabel: allowGiftCards_txt, name: 'allowGiftcards', id: 'allowGiftcards', checked:allowGiftcardsChecked})
						]
					})
				]
			})
		]
	};

	var legacyDesignerSplashScreenPanel =
	{
		xtype: 'fieldset',
		columnWidth: 0.5,
		title: designerSplashScreen_tx,
		collapsible: false,
		autoHeight: true,
		defaultType: 'textfield',
		items:[
			new Ext.form.DateField({ fieldLabel: startDate_tx, name: 'startdate', id: 'startdt', width: 150, validator: function(v){ return dateValidation();  }, validateOnBlur:true, endDateField: 'enddt', format: gDateFormat, value: "{/literal}{$splashstartdate}{literal}"}),
			new Ext.form.DateField({ fieldLabel: endDate_tx,   name: 'enddate',   id: 'enddt', width: 150, validator: function(v){ return dateValidation();  }, validateOnBlur:true, startDateField: 'startdt', format: gDateFormat, value: "{/literal}{$splashenddate}{literal}"}),
			{
				xtype:'fieldset',
				columnWidth: 0.5,
				title: "{/literal}{#str_LabelProductPreviewIcon#}{literal}",
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
								text: "{/literal}{#str_ButtonUpdateSplashScreenImage#}{literal}",
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
								text: "{/literal}{#str_ButtonRemoveSplashScreenImage#}{literal}",
								handler: onRemoveLogo
							},
							{
								xtype: 'spacer',
								width: 5
							},
							{
								text: "{/literal}{#str_ButtonResetSplashScreenImage#}{literal}",
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
							html: '<img style="border: 1px solid;" id="previewimage" name="previewimage" src="./?fsaction=AdminAutoUpdate.getDesignerSplashScreenImage&id={/literal}{$splashScreenAssetID}{literal}&ref={/literal}{$ref}{literal}&no=1&tmp=0&version=' + gDate + '">'
						}
					}
				]
			},
			{ xtype: 'hidden', id: 'assetid', name: 'assetid', value: '{/literal}{$splashScreenAssetID}{literal}',  post: true}
		]
	};

	var legacyDesignerBannerPanel = 
	{
		xtype: 'fieldset',
		columnWidth: 0.5,
		title: banner_tx,
		collapsible: false,
		autoHeight: true,
		defaultType: 'textfield',
		items:[
				new Ext.form.DateField({ fieldLabel: startDate_tx, name: 'bannerstartdate', id: 'bannerstartdt', width: 150, validator: function(v){ return bannerDateValidation();  }, validateOnBlur:true, endDateField: 'bannerenddt', format: gDateFormat, value: "{/literal}{$bannerstartdate}{literal}"}),
				new Ext.form.DateField({ fieldLabel: endDate_tx,   name: 'bannerenddate',   id: 'bannerenddt', width: 150, validator: function(v){ return bannerDateValidation();  }, validateOnBlur:true, startDateField: 'bannerstartdt', format: gDateFormat, value: "{/literal}{$bannerenddate}{literal}"}),
				{
					xtype:'fieldset',
					columnWidth: 0.5,
					title: "{/literal}{#str_LabelProductPreviewIcon#}{literal}",
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
									text: "{/literal}{#str_ButtonUpdateBannerImage#}{literal}",
									handler: function()
									{
										createBannerUploadDialog();
										Ext.getDom('bannerpreview').value = '';
									}
								},
								{
									xtype: 'spacer',
									width: 5
								},
								{
									text: "{/literal}{#str_ButtonRemoveBannerImage#}{literal}",
									handler: onRemoveBanner
								},
								{
									xtype: 'spacer',
									width: 5
								},
								{
									text: "{/literal}{#str_ButtonResetBannerImage#}{literal}",
									id: 'resetBannerButton',
									disabled: true,
									handler: onResetBanner
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
								html: '<img style="border: 1px solid;" id="bannerpreviewimage" name="bannerpreviewimage" src="./?fsaction=AdminAutoUpdate.getBannerImage&id={/literal}{$bannerAssetID}{literal}&ref={/literal}{$ref}{literal}&no=1&tmp=0&version=' + gDate + '">'
							}
						}
					]
				},
				{ xtype: 'hidden', id: 'bannerassetid', name: 'bannerassetid', value: '{/literal}{$bannerAssetID}{literal}',  post: true}
		]
	}

	var accountPagesURLPanel = 
	{
		xtype: 'fieldset',
		columnWidth: 0.5,
		title: accountpagesurl_tx,
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
						boxLabel: accountPagesDefaultLabel,
						name: 'defaultaccountpagesurl',
						id:'usedefaultaccountpagesurl',
						inputValue: 1,
						checked: useDefaultAccountPagesURL,
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
										boxLabel: useCustomURL_tx,
										hideLabel: false,
										id:'usecustomaccountpagesurl',
										name: 'defaultaccountpagesurl',
										inputValue: 0,
										checked: (! useDefaultAccountPagesURL)
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
										value: accountPagesURL,
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

	var promoPanelOverridePanel = 
	{
		xtype: 'fieldset',
		columnWidth: 0.5,
		title: promoPanel_tx,
		collapsible: false,
		autoHeight: true,
		columns: 1,
		defaultType: 'textfield',
		items:[
			new Ext.Container(
			{
				html: "{/literal}{#str_LabelPromoPanelExplanation#}{literal}",
				style: 'color: gray; position: relative;',
			}),
			{
				xtype: 'spacer',
				height: 5
			},
			new Ext.form.ComboBox({
				id: 'promopanelmode',
				name: 'promopanelmode',
				mode: 'local',
				value: originalPromoPanelMode,
				editable: false,
				forceSelection: true,
				fieldLabel: mode_tx,
				width: 400,
				valueField: 'promopanelmode',
				displayField: 'promopanelmode_name',
				useID: true,
				hideLabel: false,
				allowBlank: true,
				store: new Ext.data.ArrayStore({
					id: 'promopanelvalues',
					fields: ['promopanelmode', 'promopanelmode_name'],
					data: [[0, useLicenseKey_tx], [1, url_tx], [2, image_tx]]
				}),
				triggerAction: 'all',
				validationEvent: false,
				listeners: {
					'select': refreshPromoPanelSettings
				}
			}),
			new Ext.form.DateField({ 
				fieldLabel: startDate_tx,
				name: 'promostartdate',
				id: 'promopanelstartdate',
				width: 150,
				validator: function(v){ return promoPanelDataValidation();  },
				validateOnBlur:true,
				endDateField: 'promopanelenddate',
				format: gDateFormat,
				value: originalPromoPanelStartDate,
				listeners: {change: function() {gPromoPanelDirty = 1;}}}),
			new Ext.form.DateField({
				fieldLabel: endDate_tx,
				name: 'promoenddate',
				id: 'promopanelenddate',
				width: 150,
				validator: function(v){ return promoPanelDataValidation();  },
				validateOnBlur:true,
				startDateField: 'promopanelstartdate',
				format: gDateFormat,
				value: originalPromoPanelEndDate,
				listeners: {change: function() {gPromoPanelDirty = 1;}}
			}),
			{
				xtype: 'textfield',
				id: 'promopanelurl',
				name: 'promopanelurl',
				width: 505,
				fieldLabel: url_tx,
				value: originalPromoPanelURL,
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
				},
				listeners: {change: function() {gPromoPanelDirty = 1;}}
			},
			{
				xtype:"numberfield",
				allowNegative: false,
				allowDecimals: false,
				allowBlank: false,
				maxValue: 300,
				minValue: 1,
				id: 'promopanelheight',
				name: 'promopanelheight',
				fieldLabel: "{/literal}{#str_LabelHeight#}{literal}",
				post: true,
				width: 190,
				validateOnBlur: true,
				value: originalPromoPanelHeight,
				listeners: {valid: function() {gPromoPanelDirty = 1;}}
			},
			{
				xtype:'fieldset',
				columnWidth: 0.5,
				title: "{/literal}{#str_LabelProductPreviewIcon#}{literal}",
				collapsible: false,
				autoHeight:true,
				defaultType: 'textfield',
				name: 'promopanelimagecontainer',
				id: 'promopanelimagecontainer',
				items :[
					{
						xtype: 'buttongroup',
						frame: false,
						columns: 5,
						items:
						[
							{
								text: "{/literal}{#str_LabelUpdatePromoPanelImage#}{literal}",
								id: 'updatepromopanelimage',
								handler: function()
								{
									createPromoPanelUploadDialog();
									Ext.getDom('promopanelpreview').value = '';
								}
							},
							{
								xtype: 'spacer',
								width: 5
							},
							{
								xtype: 'spacer',
								width: 5
							},
							{
								text: "{/literal}{#str_LabelResetPromoPanelImage#}{literal}",
								id: 'resetpromopanelbutton',
								disabled: true,
								handler: onResetPromoPanel
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
							html: '<img style="border: 1px solid;" id="promopanelpreviewimage" name="promopanelpreviewimage" src="./?fsaction=AdminAutoUpdate.getPromoPanelImage&no=1&groupcode=' + licenceCode + '&ref={/literal}{$ref}{literal}&tmp=0&version=' + gDate + '">',
							name: 'promopanelpreviewimage'
						}
					},
					{
						xtype: 'spacer',
						height: 5
					},
					new Ext.form.Checkbox({
						boxLabel: "{/literal}{#str_LabelHiDpiToggle#}{literal}",
						name: 'promopanelhidpitoggle',
						id: 'promopanelhidpitoggle',
						hideLabel:true,
						checked: (originalPromoPanelDevicePixelRatio > 1),
						disabled: (originalPromoPanelRequireHiDPI),
						listeners: { check: function() {gPromoPanelDirty = 1;}}
					})
				]
			}
		]
	}

	var desktopDesignerTab =
	{
		title: desktopdesignersettings_tx,
		defaults:{xtype: 'textfield', labelWidth: 130},
		id:'desktopDesignerTab',
		hideMode:'offsets',
		items: [
			new Ext.Panel(
			{
				layout: 'form',
				width: 928,
				items:
				[
					accountPagesURLPanel,
					promoPanelOverridePanel
				]
			})
		]
	};

	var legacyDesktopDesignerTab =
	{
		title: legacydesktopdesigner_tx,
		defaults: {xtype: 'textfield', labelWidth: 130},
		id: 'legacyDesktopDesignerTab',
		hidemode: 'offsets',
		items: [
			new Ext.Panel(
				{
					layout: 'form',
					width: 928,
					items:
					[
						legacyDesignerSplashScreenPanel,
						legacyDesignerBannerPanel
					]
				}
			)
		]
	}

	var checkImageScalingDefault = function(pTextBoxName, pEnabledName, pDefaultName, pOldValue, pOldEnabled, pRefreshFunc)
	{
		var valueTextBox = Ext.getCmp(pTextBoxName);
		var enabledCheckBox = Ext.getCmp(pEnabledName);

		if (Ext.getCmp(pDefaultName).checked)
		{
			valueTextBox.disable();
			enabledCheckBox.disable();
			pRefreshFunc();
		}
		else
		{
			enabledCheckBox.enable().setValue(pOldEnabled);
			valueTextBox.setValue(pOldValue).focus().setDisabled(!pOldEnabled);
		}
	}

	var checkImageScalingEnabled = function(pDefaultName, pTextBoxName, pEnabledName)
	{
		if (!Ext.getCmp(pDefaultName).checked)
		{
			Ext.getCmp(pTextBoxName).setDisabled(!Ext.getCmp(pEnabledName).checked);
		}
	}

	var checkLogoLinkURLDefault = function(pTextBoxName, pTooltipTextBoxName, pTooltipButton, pDefaultName, pOldValue, pRefreshFunc)
	{
		var valueTextBox = Ext.getCmp(pTextBoxName);
		var toolTipButton = Ext.getCmp(pTooltipButton);

		if (Ext.getCmp(pDefaultName).checked)
		{
			valueTextBox.disable();
			toolTipButton.disable();
			pRefreshFunc();
		}
		else
		{
			if (pOldValue != '')
			{
				valueTextBox.setValue(pOldValue).focus().setDisabled();
				toolTipButton.setDisabled();
			}
			else if (valueTextBox.getValue() != '')
			{
				valueTextBox.focus().setDisabled();
				toolTipButton.setDisabled();
			}
			else
			{
				valueTextBox.setValue('http://').focus().setDisabled();
				toolTipButton.setDisabled();
			}
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

	var optionsPanel =
	{
        xtype:'fieldset',
        title: "{/literal}{#str_LabelPhotoPrintsOptions#}{literal}",
        collapsible: false,
        autoHeight: true,
        defaultType: 'textfield',
		labelWidth: 223,
        items :
        [
			new Ext.Panel(
				{
					style:'padding-top:7px; padding-bottom:10px;border-bottom:1px solid #ccc; margin-bottom:7px;',
					layout: 'form',
					width: 835,
					items:
					[
						new Ext.form.Checkbox(
						{
							name: 'usedefaultlogolinkurl',
							id: 'usedefaultlogolinkurl',
							checked: useDefaultLogoLinkUrlChecked,
							boxLabel: usedefault_txt,
							fieldLabel: logolinkurl_txt,
							listeners:
							{
								check: function()
								{
									checkLogoLinkURLDefault('onlinedesignerlogolinkurl', 'onlinedesignerlogolinktooltip', 'onlinedesignerlogolinktooltipbtn', 'usedefaultlogolinkurl', onlineDesignerLogoLinkUrlVal, refreshOnlineDesignerLogoLinkURL);
								}
							}
						}),
						{
							xtype: 'textfield',
							id: 'onlinedesignerlogolinkurl',
							name: 'onlinedesignerlogolinkurl',
							width: 505,
							value: "{/literal}{$onlinedesignerlogolinkurl}{literal}",
							validateOnBlur: true,
							post: true,
							disabled: useDefaultLogoLinkUrlChecked,
							validator: function(v){ return validateUrl(this); }
						},
						new Ext.Button(
						{
							id: 'onlinedesignerlogolinktooltipbtn',
							name: 'onlinedesignerlogolinktooltipbtn',
							fieldLabel: "{/literal}{#str_LabelOnlineDesignerLogoLinkTitle#}{literal}",
							text: "{/literal}{#str_LabelSetLinkToolTip#}{literal}",
							minWidth: 100,
							listeners: { click: onlineLogoLinkToolTip },
							disabled: useDefaultLogoLinkUrlChecked
						}),
						{
							xtype: 'hidden',
							id: 'onlinedesignerlogolinktooltip',
							name: 'onlinedesignerlogolinktooltip',
							maxLength: 1024,
							value: '{/literal}{$onlinedesignerlogolinktooltip|escape}{literal}'
						}
					]
				}
			),
		{/literal}
		{if $allowimagescalingbefore}
		{literal}
        	new Ext.Panel(
			{
				style:'padding-top:7px; padding-bottom:10px;border-bottom:1px solid #ccc; margin-bottom:7px;',
				layout: 'form',
				width: 835,
				items:
				[
					new Ext.form.Checkbox(
					{
						name: 'usedefaultimagescalingbefore',
						id: 'usedefaultimagescalingbefore',
						checked: usedefaultimagescalingbeforechecked,
						boxLabel: usedefault_txt,
						fieldLabel: imagescalingbefore_txt,
						listeners:
						{
							check: function()
							{
								checkImageScalingDefault('imagescalingbefore', 'imagescalingbeforeenabled', 'usedefaultimagescalingbefore', imagescalingBeforeVal, imagescalingBeforeEnabledVal, refreshImageScalingBefore);
							}
						}
					}),
					new Ext.form.Checkbox(
					{
						name: 'imagescalingbeforeenabled',
						id: 'imagescalingbeforeenabled',
						checked: imagescalingBeforeEnabledVal,
						boxLabel: imagescalingbeforeenabled_txt,
						disabled: usedefaultimagescalingbeforechecked,
						listeners:
						{
							check: function()
							{
								checkImageScalingEnabled('usedefaultimagescalingbefore', 'imagescalingbefore', 'imagescalingbeforeenabled');
							}
						}
					}),
					{
						id: 'imagescalingbefore',
                        name: 'imagescalingbefore',
                        xtype: 'numberfield',
                        value: (usedefaultimagescalingbeforechecked ? '' : imagescalingBeforeVal),
                        disabled: (usedefaultimagescalingbeforechecked ? true : !imagescalingBeforeEnabledVal),
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
				width: 835,
				items:
				[
					new Ext.form.Checkbox(
					{
						name: 'usedefaultimagescalingafter',
						id: 'usedefaultimagescalingafter',
						checked:  usedefaultimagescalingafterchecked,
						boxLabel: usedefault_txt,
						fieldLabel: imagescalingafter_txt,
						listeners:
						{
							check: function()
							{
								checkImageScalingDefault('imagescalingafter', 'imagescalingafterenabled', 'usedefaultimagescalingafter', imagescalingAfterVal, imagescalingAfterEnabledVal, refreshImageScalingAfter);
							}
						}
					}),
					new Ext.form.Checkbox(
					{
						name: 'imagescalingafterenabled',
						id: 'imagescalingafterenabled',
						checked: imagescalingAfterEnabledVal,
						boxLabel: imagescalingafterenabled_txt,
						disabled: usedefaultimagescalingafterchecked,
						listeners:
						{
							check: function()
							{
								checkImageScalingEnabled('usedefaultimagescalingafter', 'imagescalingafter', 'imagescalingafterenabled');
							}
						}
					}),
					{
						id: 'imagescalingafter',
						name: 'imagescalingafter',
						xtype: 'numberfield',
						value: (usedefaultimagescalingafterchecked ? '' : imagescalingAfterVal),
						disabled: (usedefaultimagescalingafterchecked ? true : !imagescalingAfterEnabledVal),
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
				width: 835,
				items:
				[
					new Ext.form.Checkbox(
					{
						name: 'usedefaultautomaticallyapplyperfectlyclear',
						id: 'usedefaultautomaticallyapplyperfectlyclear',
						checked:  useDefaultAutomaticallyApplyPerfectlyClear,
						boxLabel: usedefault_txt,
						fieldLabel: "{/literal}{#str_LabelPerfectlyClear#}{literal}",
						listeners:
						{
							check: function()
							{
								var automaticallyapplyperfectlyclear = Ext.getCmp('automaticallyapplyperfectlyclear');
								var toggleperfectlyclear = Ext.getCmp('toggleperfectlyclear');

								if (Ext.getCmp('usedefaultautomaticallyapplyperfectlyclear').checked)
								{
									automaticallyapplyperfectlyclear.disable();
									toggleperfectlyclear.disable();
									refreshPerfectlyClearSettings();
								}
								else
								{
									automaticallyapplyperfectlyclear.enable();
									if (automaticallyapplyperfectlyclear.checked)
									{
										Ext.getCmp('toggleperfectlyclear').enable();
									}
								}
							}
						}
					}),
					new Ext.form.Checkbox(
					{
						name: 'automaticallyapplyperfectlyclear',
						id: 'automaticallyapplyperfectlyclear',
						boxLabel: "{/literal}{#str_LabelAutomaticallyApplyToAllImages#}{literal}",
						checked: (useDefaultAutomaticallyApplyPerfectlyClear ? false : automaticallyApplyPerfectlyClear),
						disabled: (useDefaultAutomaticallyApplyPerfectlyClear ? true : false),
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
						disabled: allUsersToToggleEnabled,
						width: 400,
						boxLabel: "{/literal}{#str_LabelAllowUsersToToggle#}{literal}",
						checked: {/literal}{$allowuserstotoggleperfectlyclear}{literal}
					}
				]
			})
		]
	};

	var checkSmartGuideDefault = function(pDefaultID, pEnableID, pDisabledID, pObjectGuideColourID, pPageGuideColourID, pOldEnableValue, pOldObjectGuideColour, pOldPageGuideColour, pRefreshFunc)
	{
		var defaultCheckbox = Ext.getCmp(pDefaultID);
		var enabledRadio = Ext.getCmp(pEnableID);
		var disabledRadio = Ext.getCmp(pDisabledID);
		var objectGuideColourInput = Ext.getCmp(pObjectGuideColourID);
		var pageGuideColourInput = Ext.getCmp(pPageGuideColourID);

		if (defaultCheckbox.checked)
		{
			enabledRadio.disable();
			disabledRadio.disable();

			objectGuideColourInput.setValue(pOldObjectGuideColour);
			pageGuideColourInput.setValue(pOldPageGuideColour);

			objectGuideColourInput.disable();
			pageGuideColourInput.disable();
		}
		else
		{
			if (pOldEnableValue)
			{
				enabledRadio.setValue(true);
			}
			else
			{
				disabledRadio.setValue(true);
			}

			enabledRadio.enable();
			disabledRadio.enable();
			objectGuideColourInput.setValue(pOldObjectGuideColour).enable();
			pageGuideColourInput.setValue(pOldPageGuideColour).enable();
		}

		pRefreshFunc();
	};

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
						value: -1,
						id: 'usedefaultfonts',
						checked: {/literal}{if -1 === $selectedfontlist}1{else}0{/if}{literal},
						boxLabel: "{/literal}{#str_LabelUseDefault#}{literal}"
					}),
					new Ext.form.Radio(
					{
						name: 'fontlistselection',
						value: 1,
						id: 'useallfonts',
						checked: {/literal}{if null === $selectedfontlist}1{else}0{/if}{literal},
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
						if (-1 === this.getValue().value || 1 === this.getValue().value) {
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

	var onlineDesignerSettingsTab =
	{
		title: onlinedesignersettings_tx,
		defaults:{xtype: 'textfield', labelWidth: 230},
		id:'onlineDesignerSettingsTab',
		hideMode:'offsets',
		items:
		[
			new Ext.Panel(
			{
				layout: 'form',
				width: 835,
				items:
				[
					fontList,
					optionsPanel
				]
			})
		]
	};

	var topPanel = new Ext.Panel({
		id: 'topPanel',
		layout: 'column',
		style:'background: #c9d8ed; padding: 3px 0; border:1px solid #8ca9cf',
		columns: 2,
		plain:true,
		bodyBorder: false,
		border: false,
		defaults: {labelWidth: 80},
		bodyStyle:'padding:5px 5px 0; border-top: 0px',
		items: [
			new Ext.Container({
				layout: 'form',
				defaults: {xtype: 'textfield', width: 195},
				width:300,
				items:
				[
					{ id: 'groupcode', name: 'groupcode',	fieldLabel: codeTitle_tx, readOnly: true, value: licenceCode, post: true, style:'background:#c9d8ed; ' }
				]
			}),
			new Ext.Container({
				layout: 'form',
				defaults: {xtype: 'textfield', width: 400},
				style:'padding-left:15px',
				width:550,
				items:
				[
					new Ext.form.ComboBox(
					{
						id: 'webbrandinglist',
						name: 'webbrandinglist',
						hiddenName:'webbrandinglist_hn',
						width:180,
						allowBlank: false,
						mode: 'local',
						editable: false,
						forceSelection: true,
						hiddenId: 'webbrandinglist_hi',
						store: new Ext.data.ArrayStore(
						{
							id: 0,
							fields: ['brand_id', 'brand_name'],
							data: brandList
						}),
						validationEvent:false,
						post: true,
						valueField: 'brand_id',
						displayField: 'brand_name',
						useID: true,
						fieldLabel:brandingTitle_tx,
						triggerAction: 'all',
						labelStyle: 'width:130px',
						listeners:
						{
							select: function()
							{
								refreshPaymentMethods();
							{/literal}
							{if $allowimagescalingbefore}
							{literal}
								refreshImageScalingBefore();
							{/literal}
							{/if}
							{literal}
								refreshImageScalingAfter();
								refreshOnlineDesignerLogoLinkURL();
								refreshVoucherSettings(false);
								refreshPerfectlyClearSettings();
								refreshDefaultAccountPagesURLLabel();
							}
						}
					})
				]
			})
		]
	});


	var tabPanel = {
		xtype: 'tabpanel',
		id: 'editLicenseKeyTabPanel',
		deferredRender: false,
		enableTabScroll:true,
		activeTab: 0,
		width: 950,
		height: 470,
		shadow: true,
		plain:true,
		bodyBorder: false,
		border: false,
		style:'margin-top:6px; ',
		bodyStyle:'border-right: 1px solid #96bde7; border-left: 1px solid #96bde7; ',
		defaults:{frame: false, autoScroll: true, hideMode:'offsets', layout: 'form', labelWidth: 120, bodyStyle:'padding:5px 10px 0 10px; border-top: 0px; background-color: #eaf0f8;'},
		items:
		[
			licenceSettings,
			licenceAddressTab,
			paymentSettings,
			desktopDesignerTab,
			legacyDesktopDesignerTab,
			onlineDesignerSettingsTab
		]
	};

	var dialogFormPanelObj = new Ext.taopix.FormPanel({
		id: 'licenceUpdateEditForm',
		header: false,
		frame:true,
		width: 960,
		layout: 'form',
		defaultType: 'textfield',
		bodyStyle:'border-bottom: 1px solid #96bde7;',
		autoHeight: true,
		items: [ topPanel, tabPanel ],
		baseParams:	{ ref: '{/literal}{$ref}{literal}' }
	});

	gDialogObj = new Ext.Window({
		id: 'editdialog',
		closable:false,
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		width: 980,
		title: "{/literal}{#str_TitleEditLicenseKey#}{literal}",
		items: dialogFormPanelObj,
		listeners: {
			'close': {
				fn: function(){
					licenseKeyEditWindowExists = false;
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
				text: "{/literal}{#str_ButtonUpdate#}{literal}",
				id: 'updateButton',
				handler: addsaveHandler,
				cls: 'x-btn-right'
			}
		]
	});

	gDialogObj.show();

	init();
}

{/literal}