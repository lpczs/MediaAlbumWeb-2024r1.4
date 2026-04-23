{$paymentmethoddefaults}
{$addressdefaultsjs}
var gPaymentMethodCount = {$paymentmethodcount};
var gOrigAccountBalance = {$accountbalance};
var userPaymentMethods = "{$userPaymentMethodsList}";
var taxCode = "{$taxcode}";
var shippingTaxCode = "{$shippingtaxcode}";
var emailDestArray = [['0',"{#str_LabelBillingAddress#}"],['1',"{#str_LabelShippingAddress#}"],['2',"{#str_LabelShippingBCCBillingAddress#}"],['3',"{#str_LabelBillingBCCShippingAddress#}"]];
var TPX_REGISTEREDTAXNUMBERTYPE_NA = {$TPX_REGISTEREDTAXNUMBERTYPE_NA};
var TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL = {$TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL};
var TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE = {$TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE};
var gGiftVoucherOptionCount = 2; // number of options to loop over
// these are json encoded arrays containing the default and user assigned giftcard/voucher options
var userGiftVoucherMethods = {$userGiftVoucherMethods};
var defaultAllGiftVoucherMethods = {$defaultAllGiftVoucherMethods};
var defaultGiftVoucherMethods = [];
var currencyTitle_tx = "{#str_LabelCurrency#}";
var labelDefault_tx ="{#str_LabelDefault#}";
var otherLabel_txt = "{#str_Other#}";;
var usedefaultcurrency = "{$usedefaultcurrency}";
var currencyCode = "{$currencySelected}";
var currencyArray = {$currencylist};
var licenseKeyCurrencySettings = {$licensekeycurrencysettings};

{literal}

var loginValidation = function(v)
{
	if ((Ext.getCmp('login2').getValue() != Ext.getCmp('login_customer').getValue()) )
	{
		if (Ext.getCmp('login2').getValue() != '')
		{
			return "{/literal}{#str_MessageCompulsoryUserNameMismatch#}{literal}";
		}
		return true;
	}
	else
	{
		return true;
	}
};

function passwordValidation(pValue, pCall)
{
	if (Ext.getCmp(pCall).getValue() != pValue)
	{
		if (Ext.getCmp('password2').getValue() != '')
		{
			return "{/literal}{#str_ErrorReEnterPassword#}{literal}";
		}
		return true;
	}
	else
	{
        Ext.getCmp(pCall).clearInvalid();
		return true;
	}
};


var passwordBlurValidation = function(pValue)
{
	return passwordValidation(pValue, 'password2');
};

var passwordBlurValidation2 = function(pValue)
{
	return passwordValidation(pValue, 'password_customer');
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


Ext.apply(Ext.form.VTypes, {
	customerEmail : function(val, field) {
		return validateEmailAddress(val);
	},
	customerEmailText : "{/literal}{#str_ExtJsVTypesEmail#}{literal}"
});

var emailValidation = function(v)
{
	if ((Ext.getCmp('email2').getValue() != Ext.getCmp('email').getValue()) )
	{
		if (Ext.getCmp('email2').getValue() != '')
		{
			return "{/literal}{#str_MessageCompulsoryEmailMismatch#}{literal}";
		}
		return true;
	}
	else
	{
		return true;
	}
};


var editSaveHandler = function()
{
	if(Ext.getCmp('mainform').getForm().isValid())
	{
		var selectedPaymentMethods = [];
		if (!Ext.getCmp('usedefaultpaymentmethods').checked)
		{
        	var paymentMethodsChecked = false;
			for (var i = 0; i < gPaymentMethodCount; i++)
        	{
            	var theCheckBox = Ext.getCmp("paymentmethod" + i);
            	if (theCheckBox.checked)
           	 	{
                	selectedPaymentMethods.push(theCheckBox.name);
            	}
        	}
    	}
    	selectedPaymentMethods = selectedPaymentMethods.join(',');
    	if ((!selectedPaymentMethods) && (!Ext.getCmp('usedefaultpaymentmethods').checked) )
		{
	    	Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoPaymentMethods#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
	    	return false;
		}

		var parameter = Ext.getCmp('addressForm').getAddressValues();
		parameter['paymentmethods'] = selectedPaymentMethods;

    	var loginVal = Ext.getCmp('login_customer').getValue();
    	var passwordVal = Ext.getCmp('password_customer').getValue();
    	var passwordFormat = ((document.location.protocol != 'https:') ? 1 : 0);
    	parameter['format'] = passwordFormat;

    	if (loginVal.length > 0)
    	{
        	if ((passwordVal != "**UNCHANGED**") && (passwordFormat == 1))
        	{
           	 	Ext.getCmp('password_customer').setValue(hex_md5(passwordVal));
				Ext.getCmp('password2').setValue(hex_md5(passwordVal));
        	}
    	}

		parameter['protectedfromredaction'] = (Ext.getCmp('protectedfromredaction').checked) ? 1 : 0;

    	parameter['accountbalancedifference'] = Ext.getCmp('accountbalance').getValue() - gOrigAccountBalance;
    	parameter['origaccountbalance'] = gOrigAccountBalance;

    	if (Ext.getCmp('isactive').checked)
		{
			parameter['active'] = '1';
		}
		else
		{
			parameter['active'] = '0';
		}

		if (Ext.getCmp('defaultaddresscontrol').checked)
		{
			parameter['defaultaddresscontrol'] = '1';
		}
		else
		{
			parameter['defaultaddresscontrol'] = '0';
		}
		if (Ext.getCmp('usedefaultpaymentmethods').checked)
		{
			parameter['usedefaultpaymentmethods'] = '1';
		}
		else
		{
			parameter['usedefaultpaymentmethods'] = '0';
		}
		if (Ext.getCmp('uselicensekeyforshippingaddress').checked)
		{
			parameter['uselicensekeyforshippingaddress'] = '1';
		}
		else
		{
			parameter['uselicensekeyforshippingaddress'] = '0';
		}
		if (Ext.getCmp('canmodifyshippingaddress').checked)
		{
			parameter['canmodifyshippingaddress'] = '1';
		}
		else
		{
			parameter['canmodifyshippingaddress'] = '0';
		}
		if (Ext.getCmp('canmodifyshippingcontactdetails').checked)
		{
			parameter['canmodifyshippingcontactdetails'] = '1';
		}
		else
		{
			parameter['canmodifyshippingcontactdetails'] = '0';
		}
		if (Ext.getCmp('uselicensekeyforbillingaddress').checked)
		{
			parameter['uselicensekeyforbillingaddress'] = '1';
		}
		else
		{
			parameter['uselicensekeyforbillingaddress'] = '0';
		}
		if (Ext.getCmp('canmodifybillingaddress').checked)
		{
			parameter['canmodifybillingaddress'] = '1';
		}
		else
		{
			parameter['canmodifybillingaddress'] = '0';
		}
		if (Ext.getCmp('canmodifypassword').checked)
		{
			parameter['canmodifypassword'] = '1';
		}
		else
		{
			parameter['canmodifypassword'] = '0';
		}
		if (Ext.getCmp('sendmarketinginfo').checked)
		{
			parameter['sendmarketinginfo'] = '1';
		}
		else
		{
			parameter['sendmarketinginfo'] = '0';
		}

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

		parameter['taxcode'] = taxCode;
		parameter['shippingtaxcode'] = shippingTaxCode;

		var registeredTaxNumberType = 0;
		var registeredTaxNumber = '';

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

		parameter['validregisteredtaxnumbertype'] = registeredTaxNumberType;
        parameter['validregisteredtaxnumber'] = registeredTaxNumber;

		// user gift card options
		parameter['usedefaultgiftvouchersettings'] = Ext.getCmp('usedefaultgiftvouchersettings').checked ? 1 : 0;
		parameter['allowvouchers'] = Ext.getCmp('giftvoucheroption0').checked ? 1 : 0;
		parameter['allowgiftcards'] = Ext.getCmp('giftvoucheroption1').checked ? 1 : 0;

		if (Ext.getCmp('currCustom').checked) 
		{ 
			parameter['usedefaultcurrency'] = 0;	
			parameter['currencycode'] = Ext.getCmp('currencylist').getValue();  
		}
		else if (Ext.getCmp('currDefault').checked)
		{	
			parameter['usedefaultcurrency'] = 1; 
			parameter['currencycode'] = ""; 
		}

    	var fp = Ext.getCmp('mainform'), form = fp.getForm();
 	   	{/literal}{if $customerid < 1}{literal}
			parameter['id'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('customersGrid'));
			Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=AdminCustomers.add', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
    	{/literal}{else}{literal}
			parameter['id'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('customersGrid'));
			Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=AdminCustomers.edit', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
    	{/literal}{/if}{literal}
    }
    else
    {
		return false;
	}
};

var setPaymentOptions = function()
{
	var paymentMethodsChecked = paymentMethodDefaults[Ext.getCmp('licenseKeyList').getValue()];

	for (var i = 0; i < gPaymentMethodCount; i++)
	{
		var theCheckBox = Ext.getCmp("paymentmethod" + i);
		if (Ext.getCmp('usedefaultpaymentmethods').checked)
		{
			theCheckBox.disable();
			theCheckBox.setValue(paymentMethodsChecked.indexOf(theCheckBox.name) > -1);
		}
		else
		{
			theCheckBox.enable();
			theCheckBox.setValue(userPaymentMethods.indexOf(theCheckBox.name) > -1);
		}
	}
};

var setGiftAndVoucherOptions = function(pResetSettings)
{
	var useDefaults = Ext.getCmp('usedefaultgiftvouchersettings').checked;
	
	for (var i = 0; i < gGiftVoucherOptionCount; i++)
	{
		var currentCheckbox = Ext.getCmp('giftvoucheroption' + i);
		if(useDefaults === true)
		{
			currentCheckbox.disable();
			currentCheckbox.setValue(defaultGiftVoucherMethods.indexOf(currentCheckbox.name) > -1);
		}
		else
		{
			currentCheckbox.enable();
			if(pResetSettings)
			{
				currentCheckbox.setValue(userGiftVoucherMethods.indexOf(currentCheckbox.name) > -1);
			}
		}
	}
};

var setNumber = function(v)
{
    v = typeof v == 'number' ? v : String(v).replace(this.decimalSeparator, ".");
    v = isNaN(v) ? '' : this.fixPrecision(v);
    v = isNaN(v) ? '' : String(v).replace(".", this.decimalSeparator);
    /*  ensure that the values being set on the field is forced to the required number of decimal places. */
    return Ext.form.NumberField.superclass.setValue.call(this, v);
};
var setFixPrecision = function(value)
{
	var nan = isNaN(value);
	if(!this.allowDecimals || this.decimalPrecision == -1 || nan || !value)
	{
	   return nan ? '' : value;
	}
	return parseFloat(value).toFixed(this.decimalPrecision);
};

function refreshLicenceSettings(){
	var defaultaddresscontrol = Ext.getCmp('defaultaddresscontrol');
	var uselicensekeyforshippingaddress = Ext.getCmp('uselicensekeyforshippingaddress');
	var canmodifyshippingaddress = Ext.getCmp('canmodifyshippingaddress');
	var canmodifyshippingcontactdetails = Ext.getCmp('canmodifyshippingcontactdetails');
	var uselicensekeyforbillingaddress = Ext.getCmp('uselicensekeyforbillingaddress');
	var canmodifybillingaddress = Ext.getCmp('canmodifybillingaddress');
	var useremaildestination = Ext.getCmp('useremaildestination');
	
	if (defaultaddresscontrol.checked)
    {
    	var groupCode = Ext.getCmp('licenseKeyList').getValue();
		uselicensekeyforshippingaddress.setValue(addressDefaults[groupCode]['useaddressforshipping']);
		uselicensekeyforbillingaddress.setValue(addressDefaults[groupCode]['useaddressforbilling']);

		canmodifyshippingaddress.setValue(addressDefaults[groupCode]['canmodifyshippingaddress']);
		canmodifyshippingcontactdetails.setValue(addressDefaults[groupCode]['canmodifyshippingcontactdetails']);
		canmodifybillingaddress.setValue(addressDefaults[groupCode]['canmodifybillingaddress']);
		useremaildestination.setValue(addressDefaults[groupCode]['useremaildestination']);

		uselicensekeyforshippingaddress.disable();
		uselicensekeyforbillingaddress.disable();
		canmodifyshippingaddress.disable();
		canmodifyshippingcontactdetails.disable();
		canmodifybillingaddress.disable();
		useremaildestination.disable();
    }
    else
    {
		uselicensekeyforshippingaddress.enable();
		uselicensekeyforbillingaddress.enable();
		useremaildestination.enable();

		if (uselicensekeyforshippingaddress.checked)
		{
			canmodifyshippingaddress.setValue(false);
			canmodifyshippingaddress.disable();
			canmodifyshippingcontactdetails.enable();
		}
		else
		{
			canmodifyshippingaddress.enable();

			if (canmodifyshippingaddress.checked)
			{
				canmodifyshippingcontactdetails.setValue(true);
				canmodifyshippingcontactdetails.disable();
			}
			else
			{
				canmodifyshippingcontactdetails.enable();
			}
		}

		if (uselicensekeyforbillingaddress.checked)
		{
			canmodifybillingaddress.setValue(false);
			canmodifybillingaddress.disable();
		}
		else
		{
			canmodifybillingaddress.enable();
		}
	}
	
	var groupCode = Ext.getCmp('licenseKeyList').getValue();
	defaultGiftVoucherMethods = [];
	var giftVoucherDefaultSettings = defaultAllGiftVoucherMethods[groupCode];
	if(giftVoucherDefaultSettings !== undefined)
	{
		if(giftVoucherDefaultSettings.allowvouchers === 1)
		{
			defaultGiftVoucherMethods[defaultGiftVoucherMethods.length] = 'allowvouchers';
		}
		if(giftVoucherDefaultSettings.allowgiftcards === 1)
		{
			defaultGiftVoucherMethods[defaultGiftVoucherMethods.length] = 'allowgiftcards';
		}
	}
	
	// due to the version of ExtJS used there is no method to update the box label.
	// the box label itself contains a span with an id so we can target it and update the text.
	document.getElementById('dcurrlabel').innerHTML = licenseKeyCurrencySettings[groupCode]['currencylocale'];
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

var init = function(){

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

		var currCustomRb = Ext.getCmp('currCustom');
		var currDefaultRb = Ext.getCmp('currDefault');
		var currencylistSel = Ext.getCmp('currencylist');

		if (usedefaultcurrency == '1')
		{
			currDefaultRb.setValue(true);
		}
		else
		{
			currCustomRb.setValue(true);
			currencylistSel.setValue(currencyCode);
		}

		refreshTaxControls();
		refreshCurrency();
	};


function initialize(pParams)
{
	var topPanel = new Ext.Panel({
		id: 'topPanel',
		layout: 'form',
		style:'background: #c9d8ed; padding: 3px 0; border:1px solid #8ca9cf',
		plain:true,
		bodyBorder: false,
		border: false,
		defaults: {xtype: 'combo', labelWidth: 120, width: 500},
		labelWidth: 125,
		bodyStyle:'padding:5px 5px 0; border-top: 0px',
		items: [
			new Ext.form.ComboBox({
				id: 'licenseKeyList',
				name: 'licenseKeyList',
				mode: 'local',
				editable: false,
				forceSelection: true,
				selectOnFocus: true,
				triggerAction: 'all',
				fieldLabel: "{/literal}{#str_LabelLicenseKey#}{literal}",
				store: new Ext.data.ArrayStore({
					id: 0,
					fields: ['id', 'name'],
					data: [
						{/literal}
						{section name=index loop=$grouplist}
							{if $smarty.section.index.last}
								["{$grouplist[index].id}", "{$grouplist[index].name}"]
							{else}
								["{$grouplist[index].id}", "{$grouplist[index].name}"],
							{/if}
						{/section}
						{literal}
					]
				}),
				listeners: { 'select': function(){setPaymentOptions(); refreshLicenceSettings(); setGiftAndVoucherOptions(false);} },
				valueField: 'id',
				displayField: 'name',
				useID: true,
				value: "{/literal}{$groupcode}{literal}",
				post: true
			})
		]
	});

	var paymentOptions = eval('(' + '{/literal}{$paymentmethodshtml}{literal}' + ')');
	var voucherOptions = ([
		{ style:"margin-left: 17px", name: "allowvouchers", id: "giftvoucheroption0", checked: false, hideLabel: true, boxLabel: "{/literal}{#str_LabelAllowVouchers#}{literal}"},
		{ style:"margin-left: 17px", name: "allowgiftcards", id: "giftvoucheroption1", checked: false, hideLabel: true, boxLabel: "{/literal}{#str_LabelAllowGiftCards#}{literal}"}
	]);
	var sTextErrorLogin = "{/literal}{#str_MessageCompulsoryUserNameLength#}{literal}";
    sTextErrorLogin = sTextErrorLogin.replace("^0", '5');

    var sTextErrorPassword = '{/literal}{#str_MessageCompulsoryPasswordLength#}{literal}';
    sTextErrorPassword = sTextErrorPassword.replace("^0", '5');

	var tabPanel = {
		xtype: 'tabpanel',
		id: 'maintabpanel',
		deferredRender: false,
		activeTab: 0,
		width: 923,
		height: 515,
		shadow: true,
		plain:true,
		bodyBorder: false,
		border: false,
		style:'margin-top:6px; ',
		bodyStyle:'border-right: 1px solid #96bde7; border-left: 1px solid #96bde7;',
		defaults:{frame: false, autoScroll: true, hideMode:'offsets', layout: 'form', labelWidth: 210, bodyStyle:'padding:5px 10px 0 10px; border-top: 0px; background-color: #eaf0f8;'},
		items: [
			{
				title: "{/literal}{#str_AccountSettings#}{literal}",
				id: 'accountsettingstab',
				defaults:{xtype: 'textfield'},
				items: [
					{
                    xtype: 'container',
                    layout: 'column',
                    items:
					[
						{
							xtype : 'container',
							layout : 'form',
							style: 'margin-right: 10px',
							items:
							[
								{
									xtype: 'fieldset',
									title: "{/literal}{#str_LabelLoginInformation#}{literal}",
									width: 445,
									height: 310,
									items:
									[
										{
											xtype: 'textfield',
											width: 190,
											id: 'login_customer',
											minLength:5,
											minLengthText: sTextErrorLogin,
											name: 'login_customer',
											fieldLabel: "{/literal}{#str_LabelLoginID#}{literal}"
											{/literal}{if $customerid > 0}{literal}, value: "{/literal}{$login}{literal}" {/literal}{/if}{literal},
											post: true,
											validator: function(v){ return loginValidation(v);  },
											validateOnBlur:true, allowBlank: false
										},
										{
											xtype: 'textfield',
											width: 190,
											id: 'login2',
											minLength:5,
											minLengthText: sTextErrorLogin,
											validator: function(v){ return loginValidation(v);  },
											validateOnBlur:true,
											name: 'login2',
											fieldLabel: "{/literal}{#str_LabelRetypeLoginID#}{literal}"
											{/literal}{if $customerid > 0}{literal}, value: "{/literal}{$login}{literal}" {/literal}{/if}{literal},
											post: true,
											allowBlank: false
										},
										{
											xtype: 'textfield',
											width: 190,
											id:'password_customer',
											minLength:5,
											minLengthText: sTextErrorPassword,
											name:'password_customer',
											fieldLabel: "{/literal}{#str_LabelPassword#}{literal}",
											{/literal}{if $customerid > 0}{literal}
												value: "{/literal}{$password}{literal}",
											{/literal}{/if}{literal}
											post: true,
											inputType:'password',
											validator: function(v){
												return passwordBlurValidation(v);
											},
											validateOnBlur:true,
											allowBlank: false
										},
										{
											xtype: 'textfield',
											width: 190,
											id: 'password2',
											minLength:5,
											minLengthText: sTextErrorPassword,
											name: 'password2',
											fieldLabel: "{/literal}{#str_LabelRetypePassword#}{literal}",
											{/literal}{if $customerid > 0}{literal}
												value: "{/literal}{$password}{literal}",
											{/literal}{/if}{literal}
											post: true,
											inputType: 'password',
											validator: function(v){
												return passwordBlurValidation2(v);
											},
											validateOnBlur:true,
											allowBlank: false
										},
										new Ext.form.Checkbox(
										{
											boxLabel: "{/literal}{#str_LabelModifyPassword#}{literal}",
											name: 'canmodifypassword',
											id: 'canmodifypassword',
											checked: ("{/literal}{$canmodifypasswordchecked}{literal}" == 'checked') ? true: false
										}),
										{
											xtype: 'textfield',
											width: 190,
											id: 'lastlogindate',
											name: 'lastlogindate',
											post: false,
											readOnly: true,
											fieldLabel: "{/literal}{#str_LabelLastLoginDate#}{literal}",
											value: "{/literal}{$lastlogindate}{literal}",
											style: { background: "#dee9f6"}
										},
										{
											xtype: 'textfield',
											width: 190,
											id: 'lastloginip',
											name: 'lastloginip',
											post: false,
											readOnly: true,
											fieldLabel: "{/literal}{#str_LabelLastLoginIP#}{literal}",
											value: "{/literal}{$lastloginip}{literal}",
											style: { background: "#dee9f6"}

										},
										new Ext.form.Checkbox(
										{
											boxLabel: "{/literal}{#str_LabelProtectFromRedaction#}{literal}",
											name: 'protectedfromredaction',
											id: 'protectedfromredaction',
											checked: {/literal}{$protectedfromredaction}{literal}
										})
								]},
								{ 
									xtype: 'fieldset',
									title: "{/literal}{#str_CreditSettings#}{literal}",
									width: 445,
									height: 145,
									defaults:{xtype: 'textfield', width:210},
									items: [
									{
										id: 'accountcode',
										name: 'accountcode',
										fieldLabel: "{/literal}{#str_LabelAccountCode#}{literal}",
										value: "{/literal}{$accountcode}{literal}",
										post: true,
										width: 190,
										style: {textTransform: "uppercase"}
									},
									{
										xtype:"numberfield",
										allowNegative: false,
										allowDecimals: true,
										decimalPrecision: 2,
										id: 'creditlimit',
										name: 'creditlimit',
										fieldLabel: "{/literal}{#str_LabelCreditLimit#}{literal}",
										value: "{/literal}{$creditlimit}{literal}",
										post: true,
										width: 190,
										validateOnBlur: true,
										setValue: setNumber,
										fixPrecision:setFixPrecision
									},
									{
										xtype:"numberfield",
										allowNegative: false,
										allowDecimals: true,
										decimalPrecision: 2,
										id: 'accountbalance',
										name: 'accountbalance',
										fieldLabel: "{/literal}{#str_LabelAccountBalance#}{literal}",
										value: "{/literal}{$accountbalance}{literal}",
										post: true,
										width: 190,
										validateOnBlur: true,
										setValue: setNumber,
										fixPrecision:setFixPrecision
									},
									{
										xtype:"numberfield",
										allowNegative: false,
										allowDecimals: true,
										decimalPrecision: 2,
										id: 'giftcardbalance',
										name: 'giftcardbalance',
										fieldLabel: "{/literal}{#str_LabelGiftCardBalance#}{literal}",
										value: "{/literal}{$giftcardbalance}{literal}",
										post: false,
										width: 190,
										readOnly: true,
										style: { background: "#dee9f6"},
										setValue: setNumber,
										fixPrecision:setFixPrecision
									}]
								}
						]},
						{
							xtype : 'container',
							layout : 'form',
							labelWidth: 150,
							items: [
								{
									xtype: 'fieldset',
									title: "{/literal}{#str_PaymentSettings#}{literal}",
									width: 445,
									height: 310,
									defaultType: 'checkbox',
									items: [
								
								new Ext.form.CheckboxGroup({
									columns: 1,  
									autoWidth:false, 
									layout:'column', 
									style:'margin-bottom:10px',
									fieldLabel:"{/literal}{#str_LabelPaymentMethods#}{literal}",
									hideLabel:false,
								items: [
									new Ext.form.Checkbox({
										boxLabel: "{/literal}{#str_LabelUseDefaultPaymentMethods#}{literal}",
										name: 'usedefaultpaymentmethods',
										id: 'usedefaultpaymentmethods',
										checked: ("{/literal}{$usedefaultpaymentmethodschecked}{literal}" == 'checked') ? true : false,
										hideLabel: true,
										listeners: {'check': function(){setPaymentOptions();}}
										}),
										paymentOptions
								]}),								
								{
									xtype: 'radiogroup',
									columns: 1,
									fieldLabel: currencyTitle_tx,
									autoWidth:true,
									style:'margin-top:5px; ',
									items:
									[
										{
											boxLabel: labelDefault_tx +' (<span id="dcurrlabel"></span>)',
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
											width:250,
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
													width:300,
													items : new Ext.form.ComboBox({ id: 'currencylist', name: 'currencylist', hiddenName:'currencylist_hn',	hiddenId:'currencylist_hi',	mode: 'local', editable: false,
														   forceSelection: true,width:250, valueField: 'currency_id', displayField: 'currency_name', useID: true, post: true, hideLabel:true,	allowBlank: false,
														store: new Ext.data.ArrayStore({ id: 0, fields: ['currency_id', 'currency_name'],	data: currencyArray }), triggerAction: 'all', validationEvent:false	})
													}
											]
										}
									]
								}
							]},
							{
								xtype: 'fieldset',
								title: "{/literal}{#str_LabelTitleVouchersGiftCards#}{literal}",
								width: 445,
								height: 145,
								defaultType: 'checkbox',
								items: [
									new Ext.form.Checkbox({
										boxLabel: "{/literal}{#str_LabelUseDefault#}{literal}",
										name: 'usedefaultgiftvouchersettings',
										id: 'usedefaultgiftvouchersettings',
										checked: ("{/literal}{$usedefaultgiftvouchersettings}{literal}" == 'checked' ? true : false),
										hideLabel: true,
										listeners: {
											'check': function() { setGiftAndVoucherOptions(true); }
										} 
									}),
									voucherOptions
								]
							}
					]}
					]}
				]
			},
			{
				title: "{/literal}{#str_AddressDetails#}{literal}",
				defaults:{xtype: 'textfield'},
				items: [
					{
						xtype: 'container',
						layout: 'column',
						width: 911,
						items: [
							{
								xtype : 'container',
								layout : 'form',
								style: 'margin-right: 10px',
								items: [
									{
										xtype: 'fieldset',
										title: "{/literal}{#str_AddressDetails#}{literal}",
										width: 445,
										height: 450,
										defaults:{xtype: 'textfield', width:190},
										items: [
											new Ext.taopix.AddressPanel({
												id: 'addressForm',
												options: {	ref: {/literal}{$ref}{literal}, excludeFields: 'telephonenumber', editMode: 0, strict: 1, fieldWidth: 190},
												data: {
													{/literal}
													contactFirstName: "{$contactfname}",
													contactLastName: "{$contactlname}",
													companyName: "{$companyname}",
													countryCode:"{$country}",
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
											})
										]
									}
							]},
						{
							xtype : 'container',
							layout : 'form',
							items: [
								{
									xtype: 'fieldset',
									title: "{/literal}{#str_LabelContactInformation#}{literal}",
									width: 445,
									height: 450,
									defaults:{
										xtype: 'textfield',
										width: 190
									},
									items: [
										{
											id: 'telephonenumber',
											name: 'telephonenumber',
											fieldLabel: "{/literal}{#str_LabelTelephoneNumber#}{literal}",
											value: "{/literal}{$telephonenumber}{literal}",
											validateOnBlur: true,
											listeners: {'blur': {fn: function(obj){CJKHalfWidthFullWidthToASCII(obj.getEl().dom, false)}}},
											post: true,
											allowBlank: true
										},
										{
											id: 'email',
											name: 'email',
											fieldLabel: "{/literal}{#str_LabelEmailAddress#}{literal}",
											value: "{/literal}{$email}{literal}",
											post: true,
											vtype: 'customerEmail',
											validator: function(v){ return emailValidation(v);  },
											validateOnBlur:true, allowBlank: false
										},
										{
											id: 'email2',
											name: 'email2',
											fieldLabel: "{/literal}{#str_LabelRetypeEmailAddress#}{literal}",
											value: "{/literal}{$email}{literal}",
											post: true,
											vtype: 'customerEmail',
											validator: function(v){ return emailValidation(v);  },
											validateOnBlur:true, allowBlank: false
										},
										new Ext.form.Checkbox(
										{
											boxLabel: "{/literal}{#str_LabelSendMarketingInformation#}{literal}",
											name: 'sendmarketinginfo',
											id: 'sendmarketinginfo',
											hideLabel: true,
											autoWidth: true,
											checked: ("{/literal}{$sendmarketinginfochecked}{literal}" == 'checked') ? true : false,
											autoHeight: true
										})
							]}
						]}
					]}
				]
			},
			{
				title: "{/literal}{#str_LabelSettings#}{literal}",
				defaults:{xtype: 'textfield', width: 300},
				items: [
					{ xtype: 'fieldset', title: "{/literal}{#str_AddressSettings#}{literal}",  width:900, height: 220,  defaults:{xtype: 'textfield', width:450}, items: [
					new Ext.form.CheckboxGroup({columns: 1, width: 700, layout:'column', style:'margin-bottom:10px',hideLabel:true,
						items: [
							new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelUseLicenceKeyAddressControl#}{literal}", name: 'defaultaddresscontrol', id: 'defaultaddresscontrol',hideLabel:true, checked: ("{/literal}{$defaultaddresscontrolchecked}{literal}" == 'checked') ? true : false, listeners: { check: refreshLicenceSettings } }),
							new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelUseLicenseKeyAddressForShipping#}{literal}", name: 'uselicensekeyforshippingaddress', id: 'uselicensekeyforshippingaddress',hideLabel:true, checked: ("{/literal}{$uselicensekeyforshippingaddresschecked}{literal}" == 'checked') ? true : false, listeners: { check: refreshLicenceSettings } }),
							new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelModifyShippingAddress#}{literal}", name: 'canmodifyshippingaddress', id: 'canmodifyshippingaddress',hideLabel:true, checked: ("{/literal}{$canmodifyshippingaddresschecked}{literal}" == 'checked') ? true : false, style:'margin-left:17px', listeners: { check: refreshLicenceSettings } }),
							new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelModifyShippingContactDetails#}{literal}", name: 'canmodifyshippingcontactdetails', id: 'canmodifyshippingcontactdetails',hideLabel:true, style:'margin-left:17px', checked: ("{/literal}{$canmodifyshippingcontactdetailschecked}{literal}" == 'checked') ? true : false, listeners: { check: refreshLicenceSettings } }),
							new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelUseLicenseKeyAddressForBilling#}{literal}", name: 'uselicensekeyforbillingaddress', id: 'uselicensekeyforbillingaddress',hideLabel:true, checked: ("{/literal}{$uselicensekeyforbillingaddresschecked}{literal}" == 'checked') ? true : false, listeners: { check: refreshLicenceSettings } }),
							new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelModifyBillingAddress#}{literal}", name: 'canmodifybillingaddress', id: 'canmodifybillingaddress',hideLabel:true, style:'margin-left:17px', checked: ("{/literal}{$canmodifybillingaddresschecked}{literal}" == 'checked') ? true : false, listeners: { check: refreshLicenceSettings } })
						]
					}),

					new Ext.form.ComboBox({id: 'useremaildestination', name: 'useremaildestination', hiddenName:'useremaildestination_hn', validationEvent: false,
						displayField: 'email_name', mode: 'local', editable: false, forceSelection: true, hiddenId:'useremaildestination_hi', valueField: 'email_id',
						useID: true, post: true, fieldLabel: "{/literal}{#str_LabelUserEmailDestination#}{literal}", allowBlank: false, triggerAction: 'all', labelStyle:"width: 150px;", value: "{/literal}{$useremaildestination}{literal}",
						store: new Ext.data.ArrayStore({ id: 0, fields: ['email_id', 'email_name'],	data: emailDestArray })
					})
					]},
					{xtype: 'fieldset', labelWidth: 190, title: "{/literal}{#str_LabelTaxSettings#}{literal}",  width:900, height: 195,  defaults:{xtype: 'textfield', width:190}, items: [

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
								}
					]}
				]
			}
		]
	};

	var dialogFormPanelObj = new Ext.taopix.FormPanel({
		id: 'mainform',
		header: false,
		frame:true,
		width: 775,
		layout: 'form',
		defaultType: 'textfield',
		bodyStyle:'border-bottom: 1px solid #96bde7;',
		autoHeight: true,
		items: [ topPanel, tabPanel ],
		baseParams:	{ ref: '{/literal}{$ref}{literal}' }
	});

	gDialogObj = new Ext.Window({
		id: 'dialog',
		closable:false,
		plain:true,
		title: "{/literal}{$title}{literal}",
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		width: 953,
		items: dialogFormPanelObj,
		listeners: {
			'close': {
				fn: function(){
					customersEditWindowExists = false;
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
				cls: 'x-btn-right',
				/*
				 *	Use listener instead of handler, as handler seems to have an issue where the hover class is not removed from the button when opening a new window
				 *  which also stops the dialog opening for a second time.
				 */
				listeners:
				{
					click:
					{
						fn: function()
						{
							// before calling the showAdminReauthDialogue function we need to check to make sure a
							// currency has been selected if the custome currency radio button was selected.
							if ((Ext.getCmp('currCustom').checked) && (Ext.getCmp('currencylist').getValue() == ''))
							{
								Ext.getCmp('maintabpanel').activate('accountsettingstab');
								Ext.getCmp('currencylist').markInvalid();
								return false;
							}

							/* Check the form is valid before authenticating. */
							if (Ext.getCmp('mainform').getForm().isValid())
							{
								var reason = '';
								{/literal}{if $customerid < 1}{literal}
								reason = 'CUSTOMER-ADD';
								{/literal}{else}{literal}
								reason = 'CUSTOMER-EDIT';
								{/literal}{/if}{literal}

								/* Reauthenticate the logged in user to make the changes. */
								showAdminReauthDialogue({
									ref: {/literal}{$ref}{literal},
									reason: reason,
									title: Ext.taopix.ReauthenticationDialog.strings.titleAuthenticateToSave,
									success: editSaveHandler
								});
							}
						}
					}
				},
				{/literal}{if $customerid < 1}{literal}
					text: "{/literal}{#str_ButtonAdd#}{literal}"
				{/literal}{else}{literal}
					text: "{/literal}{#str_ButtonUpdate#}{literal}"
				{/literal}{/if}{literal}
			}
		]
	});

	gDialogObj.show();
	init();

	if ("{/literal}{$groupcode}{literal}" == '')
	{
		Ext.getCmp('licenseKeyList').setValue(Ext.getCmp('licenseKeyList').store.getAt(0).data.id);
	}
	else
	{
		Ext.getCmp('licenseKeyList').setValue("{/literal}{$groupcode}{literal}");
	}

	setPaymentOptions();
	refreshLicenceSettings();
	setGiftAndVoucherOptions(true);

	Ext.getCmp('isactive').setValue("{/literal}{$activechecked}{literal}" == 'checked' ? true : false);
}
{/literal}