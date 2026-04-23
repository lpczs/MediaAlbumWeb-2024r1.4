{literal}
var gDateFormat = "{/literal}{$dateformat}{literal}";
var gEarliestDate = "{/literal}{$earliestdate}{literal}";
var gLatestDate = "{/literal}{$latestdate}{literal}";
var gProductCount = {/literal}{$productcount}{literal};
var gPromotionID = {/literal}{$promotionid}{literal};
var gVoucherID = {/literal}{$voucherid}{literal};
var gVoucherUsedInOrder = {/literal}{$voucherusedinorder}{literal};
companyGlobalValue = '';
var gDescription = '{/literal}{$description}{literal}';


function unescapeJS(pStr)
{
    return pStr.replace(/<br>/g, '\r\n');
}

var paramsGlobal = [];

function forceAlphaNumeric()
{
	var codeprefix = Ext.getCmp('codeprefix');
	codeprefix.setValue(codeprefix.getValue().toUpperCase().replace(/[^A-Z_0-9\-]+/g, ""));
}

var onCreateConfirmed = function(btn)
{
	if (btn == "yes")
	{
		var fp = Ext.getCmp('mainform'), form = fp.getForm();
		paramsGlobal['promotionid'] = gPromotionID;
		Ext.taopix.formPanelPost(fp, form, paramsGlobal, 'index.php?fsaction=AdminVouchers.create', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
	}
};

var onImportConfirmed = function(btn)
{
	if (btn == "yes")
	{
		var startDateValue = formatPHPDate(Ext.getCmp('startdate').getRawValue(), gDateFormat, "yyyy-MM-dd HH:mm") + ":00";
		var endDateValue = formatPHPDate(Ext.getCmp('enddate').getRawValue(), gDateFormat, "yyyy-MM-dd HH:mm") + ":00";

		var hiddenProductCodeValue;
		var productList = Ext.getCmp('productsList');
		var hiddenProductGroupValue;

		if (Ext.getCmp('productGroupRadio').checked)
		{
			hiddenProductGroupValue = Ext.getCmp("productGroupList").getValue();
			hiddenProductCodeValue = '';
		}
		else if(Ext.getCmp('specificProductRadio').checked)
		{
			hiddenProductCodeValue = productList.getValue();
			hiddenProductGroupValue = 0;
		}
		else
		{
			hiddenProductGroupValue = 0;
			hiddenProductCodeValue = '';
		}

		var mainForm = Ext.getCmp('mainform').getForm();

		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenRepeatType', value: Ext.getCmp('repeattypelist').getValue()});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenDiscountSection', value: Ext.getCmp('discountsectionlist').getValue()});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenDiscountType', value: Ext.getCmp('discounttypelist').getValue() });
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenName', value: Ext.getCmp('langPanel').convertTableToString()});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenDescription', value: Ext.getCmp('voucherDescription').getValue()});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenType', value: Ext.getCmp('type').getValue()});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenGroupCode', value: Ext.getCmp('grouplist').getValue()});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenCustomer', value: Ext.getCmp('userlist').getValue() });
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddeStartDate', value: startDateValue});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenEndDate', value: endDateValue});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenProductCode', value: hiddenProductCodeValue });
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenLockOrderQty', value: ((Ext.getCmp('lockqty').checked) ? '1' : '0') });
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenProductionSite', value: ((Ext.getCmp('productionsitelist')) ? Ext.getCmp('productionsitelist').getValue() : '{/literal}{$prodSiteValue}{literal}') });
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenActive', value: ((Ext.getCmp('isactive').checked) ? '1' : '0')});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenPromoCode', value: "{/literal}{$promotioncode}{literal}" });
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenId', value: (Ext.taopix.gridSelection2IDList(Ext.getCmp('vouchersGrid')))});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenPromoId', value: gPromotionID});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenDiscountValue', value: Ext.getCmp('discountvalue').getValue()});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenSellPrice', value: Ext.getCmp('sellprice').getValue()});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenAgentFee', value: Ext.getCmp('agentfee').getValue()});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenDiscountMethod', value: Ext.getCmp('discountmethodlist').getValue()});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenDiscountApplyToQty', value: Ext.getCmp('discountapplytoqty').getValue()});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenProductGroupID', value: hiddenProductGroupValue});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenminordervalue', value: Ext.getCmp('minordervalue').getValue()});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenminordervalueincludesshipping', value: ((Ext.getCmp('minordervalueincludesshipping').checked) ? '1' : '0') });
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenminordervalueincludestax', value: ((Ext.getCmp('minordervalueincludestax').checked) ? '1' : '0') });
		mainForm.el.createChild({tag:'input', type:'hidden', name:'csrf_token', value: Ext.taopix.getCSRFToken()});

		mainForm.submit({
        	url: 'index.php?fsaction=AdminVouchers.import&promotionid='+gPromotionID,
            waitMsg: '{/literal}{#str_MessageSaving#}{literal}',
            success: function(form, action)
            {
            	gridDataStoreObj.reload();
            	var dialogVouchers = Ext.getCmp('dialogVouchers');
            	if (dialogVouchers.isVisible())
				{
					dialogVouchers.close();
				}
				Ext.getCmp('dialogVouchersResults').show();
				Ext.getCmp('vouchersResultGrid').store.reload();
			},
            failure: function(form, action)
            {
            	Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: action.result.msg, buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
	        }
        });

	}
};

var setProductsRestriction = function()
	{
		if (Ext.getCmp('productGroupRadio').checked)
		{
			Ext.getCmp('productGroupList').enable();
			Ext.getCmp('productsList').disable();
			Ext.getCmp('productsList').clearValue();
			Ext.getCmp('productsList').clearInvalid();
		}
		else if(Ext.getCmp('specificProductRadio').checked)
		{
			Ext.getCmp('productsList').enable();
			Ext.getCmp('productGroupList').disable();
			Ext.getCmp('productGroupList').clearValue();
			Ext.getCmp('productGroupList').clearInvalid();
		}
		else
		{
			Ext.getCmp('productGroupList').disable();
			Ext.getCmp('productGroupList').clearValue();
			Ext.getCmp('productsList').disable();
			Ext.getCmp('productsList').clearValue();
		}
	};

var addSaveHandler = function()
{
	var maintabpanel = Ext.getCmp('maintabpanel');

	if(Ext.getCmp('mainform').getForm().isValid())
	{
		Ext.MessageBox.minWidth = 250;

		{/literal}{if $displayMode == 1}{literal}
			var voucherStartNumber = 0;

			if (!Ext.getCmp('israndom').checked)
			{
				voucherStartNumber = Ext.getCmp('startnumber').getValue();

        		if (Ext.getCmp('codeprefix').getValue() == '')
        		{
            		Ext.getCmp('codeprefix').markInvalid("{/literal}{#str_ErrorNoVoucherCodePrefix#}{literal}");
            		maintabpanel.setActiveTab(0);
					return false;
        		}
			}

			var voucherQty = Ext.getCmp('qty').getValue();
    		if ((voucherQty < 2) || (voucherQty > 3000))
    		{
        		Ext.getCmp('qty').markInvalid("{/literal}{#str_ErrorInvalidQuantity#}{literal}");
        		maintabpanel.setActiveTab(0);
	    		return false;
    		}
		{/literal}{/if}{literal}

		var startdate = Ext.getCmp('startdate');
		var enddate = Ext.getCmp('enddate');

		{/literal}{if $promotionid==0}{literal}


		if (! parsePHPDate(startdate.getRawValue(), gDateFormat))
    	{
			startdate.markInvalid("{/literal}{#str_ErrorInvalidStartDate#}{literal}");
			maintabpanel.setActiveTab(0);
			return false;
    	}

    	if (! parsePHPDate(enddate.getRawValue(), gDateFormat))
    	{
			enddate.markInvalid("{/literal}{#str_ErrorInvalidEndDate#}{literal}");
			maintabpanel.setActiveTab(0);
			return false;
    	}

    	if (comparePHPDates(enddate.getRawValue(), startdate.getRawValue(), gDateFormat) != 1)
	    {
			enddate.markInvalid("{/literal}{#str_ErrorInvalidEndDate#}{literal}");
			maintabpanel.setActiveTab(0);
			return false;
	    }

    	/* make sure the dates are in range */
	    if (comparePHPDates(startdate.getRawValue(), gEarliestDate, gDateFormat) != 1)
	    {
			startdate.markInvalid("{/literal}{#str_ErrorInvalidStartDate#}{literal}");
			maintabpanel.setActiveTab(0);
			return false;
	    }

	    if (comparePHPDates(gLatestDate, startdate.getRawValue(), gDateFormat) != 1)
	    {
			startdate.markInvalid("{/literal}{#str_ErrorInvalidStartDate#}{literal}");
			maintabpanel.setActiveTab(0);
			return false;
	    }

	    if (comparePHPDates(enddate.getRawValue(), gEarliestDate, gDateFormat) != 1)
	    {
			enddate.markInvalid("{/literal}{#str_ErrorInvalidEndDate#}{literal}");
			maintabpanel.setActiveTab(0);
			return false;
	    }

	    if (comparePHPDates(gLatestDate, enddate.getRawValue(), gDateFormat) != 1)
	    {
			enddate.markInvalid("{/literal}{#str_ErrorInvalidEndDate#}{literal}");
			maintabpanel.setActiveTab(0);
			return false;
	    }

		{/literal}{/if}{literal}

		var voucherType = Ext.getCmp('type');

		if(voucherType.store.getById(voucherType.value).data.id=={/literal}{$voucherTypeDiscount}{literal})
		{

			var discountType = Ext.getCmp('discounttypelist');
			var discountValue = Ext.getCmp('discountvalue');
			var invalid = false;

	    	if ((discountType.getValue() == "VALUE") || (discountType.getValue() == "VALUESET") || (discountType.getValue() == "BOGVOFF"))
	    	{
	        	if (discountValue.getValue() <= 0.00)
	        	{
	            	discountValue.markInvalid("{/literal}{#str_ErrorInvalidDiscountValue#}{literal}");
	            	maintabpanel.setActiveTab(0);
	            	invalid = true;
	        	}
	    	}
	    	else if ((discountType.getValue() == "PERCENT") || (discountType.getValue() == "BOGPOFF"))
	    	{
	        	if ((discountValue.getValue() <= 0.00) || (discountValue.getValue() >= 100.00))
	        	{
	            	discountValue.markInvalid("{/literal}{#str_ErrorInvalidDiscountValue#}{literal}");
	            	maintabpanel.setActiveTab(0);
	            	invalid = true;
	        	}
	    	}
	    	else
	    	{
				discountValue.setValue(0.00);
				invalid = false;
	    	}

	    	if (invalid)
	    	{
            	Ext.each(Ext.getCmp('discountvalue').el.dom.parentElement.children,function(el)
            	{
            		if (el.className == "x-form-invalid-icon")
            		{
            			el.style.left = "260px";
            		}
            	});
				maintabpanel.setActiveTab(0);
	    		return false;
	    	}

	    }
	    else if(voucherType.store.getById(voucherType.value).data.id=={/literal}{$voucherTypePrepaid}{literal})
	    {

	    	var discountValue = Ext.getCmp('discountvalue');

	    	if (discountValue.getValue()<=0)
	    	{
	    		discountValue.markInvalid("{/literal}{#str_ErrorInvalidDiscountValue#}{literal}");
	    		maintabpanel.setActiveTab(0);
	    		return false;
	    	}

	    	var sellprice = Ext.getCmp('sellprice');

	    	if (sellprice.getValue()<=0)
	    	{
	    		sellprice.markInvalid("{/literal}{#str_ErrorInvalidSellPrice#}{literal}");
	    		maintabpanel.setActiveTab(0);
	    		return false;
	    	}

	    	var licenseevalue = Ext.getCmp('licenseevalue');

    		if (licenseevalue.getValue()<0)
			{
				var agentfee = Ext.getCmp('agentfee');

				if (sellprice.getValue()>agentfee.getValue())
				{
					agentfee.markInvalid("{/literal}{#str_ErrorInvalidAgentFee#}{literal}");
				}
				else
				{
					licenseevalue.markInvalid("{/literal}{#str_ErrorInvalidLicenseeValue#}{literal}");
				}

				maintabpanel.setActiveTab(0);

				return false;
			}
		}

		if (!Ext.getCmp('langPanel').isValid())
		{
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoName#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
			maintabpanel.setActiveTab(0);
	    	return false;
		}

		/* second tab */

		var userlist = Ext.getCmp('userlist');
		if (!userlist.valid)
		{
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorCustomer#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
			userlist.markInvalid("{/literal}{#str_ErrorCustomer#}{literal}");
			maintabpanel.setActiveTab(1);
	    	return false;
		}

		var minOrderQty = Ext.getCmp('minqty');
    	if ((minOrderQty.getValue() < 1) || (minOrderQty.getValue() > 9999))
    	{
        	minOrderQty.markInvalid("{/literal}{#str_ErrorInvalidMinQuantity#}{literal}");
        	maintabpanel.setActiveTab(1);
        	return false;
    	}

    	var maxOrderQty = Ext.getCmp('maxqty');
	    if ((maxOrderQty.getValue() < 1) || (maxOrderQty.getValue() > 9999))
	    {
			maxOrderQty.markInvalid("{/literal}{#str_ErrorInvalidMaxQuantity#}{literal}");
			maintabpanel.setActiveTab(1);
	        return false;
	    }
	    if (minOrderQty.getValue() > maxOrderQty.getValue())
	    {
	        minOrderQty.markInvalid("{/literal}{#str_ErrorInvalidMinQuantity2#}{literal}");
	        maintabpanel.setActiveTab(1);
	        return false;
	    }

        var discountapplytoqty = Ext.getCmp('discountapplytoqty');
        if ((discountapplytoqty.getValue() < 1) || (discountapplytoqty.getValue() > 9999))
	    {
			discountapplytoqty.markInvalid("{/literal}{#str_ErrorInvalidMaxDiscountQuantity#}{literal}");
			maintabpanel.setActiveTab(1);
	        return false;
	    }

	    var parameter = [];

	    parameter['startdatevalue'] = formatPHPDate(startdate.getRawValue(), gDateFormat, "yyyy-MM-dd HH:mm") + ":00";
	    parameter['enddatevalue'] = formatPHPDate(enddate.getRawValue(), gDateFormat, "yyyy-MM-dd HH:mm") + ":00";

	    var productsList = Ext.getCmp('productsList');

		if (Ext.getCmp('productGroupRadio').checked)
		{
			if (Ext.getCmp("productGroupList").getValue() !== '')
			{
				parameter['groupid'] = Ext.getCmp("productGroupList").getValue();
				parameter['productcode'] = '';
			}
			else
			{
				Ext.getCmp("productGroupList").markInvalid();
				return false;
			}
		}
		else if(Ext.getCmp('specificProductRadio').checked)
		{
			if (productsList.getValue() !== '')
			{
				parameter['productcode'] = productsList.getValue();
				parameter['groupid'] = 0;
			}
			else
			{
				productsList.markInvalid();
				return false;
			}
		}
		else
		{
			parameter['groupid'] = 0;
			parameter['productcode'] = '';
		}

        parameter['lockqtyvalue'] = (Ext.getCmp('lockqty').checked) ? '1' : '0';
		parameter['minordervalueincludesshipping'] = (Ext.getCmp('minordervalueincludesshipping').checked) ? '1' : '0';
		parameter['minordervalueincludestax'] = (Ext.getCmp('minordervalueincludestax').checked) ? '1' : '0';
        parameter['defaultdiscountvalue'] = (Ext.getCmp('defaultdiscount').checked) ? '1' : '0';

	    var productionsitelist = Ext.getCmp('productionsitelist');

	    parameter['productionsitecode'] = (productionsitelist) ? productionsitelist.getValue() : '{/literal}{$prodSiteValue}{literal}';
	    parameter['isactive'] = (Ext.getCmp('isactive').checked) ? '1' : '0';

	    parameter['promotioncode'] = "{/literal}{$promotioncode}{literal}";

		var fp = Ext.getCmp('mainform'), form = fp.getForm();

	    /* add or edit */
		{/literal}{if $displayMode == 0}{literal}
	    if (gVoucherID > 0)
	    {
	    	parameter['id'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('vouchersGrid'));
			parameter['promotionid'] = gPromotionID;
			Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction={/literal}{$destaction}{literal}.edit', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
	    }
	    else
	    {
	        parameter['promotionid'] = gPromotionID;
			Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction={/literal}{$destaction}{literal}.add', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
	    }
	    {/literal}{/if}{literal}

	    /* create */
		{/literal}{if $displayMode == 1}{literal}
			parameter['israndom'] = (Ext.getCmp('israndom').checked) ? '1' : '0';
			parameter['startnumber'] = voucherStartNumber;

			paramsGlobal = parameter;
			var message = "{/literal}{#str_CreateVoucherConfirmation#}{literal}".replace("^0", voucherQty);
			Ext.MessageBox.minWidth = 350;
			Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", message, onCreateConfirmed);
		{/literal}{/if}{literal}

		{/literal}{if $displayMode == 2}{literal}
			paramsGlobal = parameter;
			Ext.MessageBox.minWidth = 350;
			Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_ImportVoucherConfirmation#}{literal}", onImportConfirmed);
		{/literal}{/if}{literal}
	}
	else
    {
		return false;
	}
};

function validateDiscountSection()
{
    var discountType = Ext.getCmp('discounttypelist').getValue();
    var discountSection = Ext.getCmp('discountsectionlist').getValue();

	//change the data of the combo in function of the discount type selected
	var discountCombo = Ext.getCmp('discountsectionlist');
    var discountMethodCombo = Ext.getCmp('discountmethodlist');

	if ((discountType == "BOGOF" || discountType == "BOGPOFF" || discountType == "BOGVOFF"))
    {
    	discountCombo.setValue("PRODUCT");
    	discountCombo.disable();

        if (discountMethodCombo.getValue() == 1)
        {
            discountMethodCombo.setValue(0);
        }
    }
   	else
   	{
   		discountCombo.enable();
        discountMethodCombo.enable();
   	}


    if ((discountType == 'PERCENT') || (discountType== 'BOGPOFF'))
	{
		Ext.getCmp('percentLabel').show();
	}
	else
	{
		Ext.getCmp('percentLabel').hide();
	}

	if ((discountType == 'FOC') || (discountType == 'BOGOF'))
	{
		Ext.getCmp('discountvalue').disable();
	}
	else
	{
		Ext.getCmp('discountvalue').enable();
	}

	if ((discountType != 'VALUE') && (discountType != 'VALUESET') && (discountType != 'BOGVOFF') && (discountType != 'PERCENT') && (discountType != 'BOGPOFF'))
	{
		Ext.getCmp('discountvalue').setValue(0.00);
	}

    // determine which options to display
    setDefaultDiscountApplyOptions();
}

function setVoucherOptions(pNewVoucherType)
{
    var sellprice = Ext.getCmp('sellprice');
    var agentfee = Ext.getCmp('agentfee');
    var licenseevalue = Ext.getCmp('licenseevalue');
    var discounttypelist = Ext.getCmp('discounttypelist');
    var discountsectionlist = Ext.getCmp('discountsectionlist');
    var repeattypelist = Ext.getCmp('repeattypelist');
    var discountvalue = Ext.getCmp('discountvalue');
    var defaultdiscount = Ext.getCmp('defaultdiscount');
    var applyMethod = Ext.getCmp('discountmethodlist');
    var applyToQty = Ext.getCmp('discountapplytoqty');

    switch(parseInt(pNewVoucherType))
    {
        case {/literal}{$voucherTypeDiscount}{literal}:
        {
            sellprice.setValue("0.00");
            sellprice.disable();

            agentfee.setValue("0.00");
            agentfee.disable();

            licenseevalue.setValue("0.00");
            licenseevalue.disable();

            discounttypelist.enable();

            discountsectionlist.enable();

            repeattypelist.enable();

            defaultdiscount.enable();

            validateDiscountSection();

            break;
        }

        case {/literal}{$voucherTypePrepaid}{literal}:
        {
            sellprice.enable();

            agentfee.enable();

            licenseevalue.enable();

            discountvalue.enable();

			discounttypelist.setValue('VALUE');
            discounttypelist.disable();

            //discountsectionlist.setValue('TOTAL');
            discountsectionlist.enable();

            repeattypelist.setValue('SINGLE');
            repeattypelist.disable();

            Ext.getCmp('percentLabel').hide();

            defaultdiscount.setValue(0);
            defaultdiscount.disable();

            break;
        }

        case {/literal}{$voucherTypeScript}{literal}:
        {
            sellprice.setValue("0.00");
            sellprice.disable();

            agentfee.setValue("0.00");
            agentfee.disable();

            licenseevalue.setValue("0.00");
            licenseevalue.disable();

            discountvalue.setValue("0.00");
            discountvalue.disable();

            Ext.getCmp('percentLabel').hide();

            discounttypelist.disable();

            discountsectionlist.setValue('PRODUCT');
            discountsectionlist.disable();

            repeattypelist.enable();

            defaultdiscount.enable();

            break;
        }
    }

    // enable or disable the discount application method
    if (discountsectionlist.value == 'SHIPPING')
    {
        Ext.getCmp('discountmethodlist').setValue(0);
        Ext.getCmp('discountmethodlist').disable();
    }
    else
    {
        // discount section is 'PRODUCT'
        setDefaultDiscountApplyOptions();
    }


}

function setDefaultDiscountOptions()
{
    var defaultdiscount = Ext.getCmp('defaultdiscount');
    var repeattypelist = Ext.getCmp('repeattypelist');
    var lockqty = Ext.getCmp('lockqty');

    var repeatlist = repeattypelist.store;

    if(defaultdiscount.checked)
    {
        // disable single option in repeat type
        if(repeattypelist.value == 'SINGLE')
        {
            repeattypelist.setValue('MULTI');
        }

        repeatlist.filterBy(function(record){return (record.get('id') != 'SINGLE');});

        // disable lock quantity option
        lockqty.setValue(0);
        lockqty.disable();
    }
    else
    {
        // enable single option in repeat type
        repeatlist.clearFilter();

        // enable lock quantity option
        lockqty.enable();
    }
}

// show or hide the Distribute over order option based on discount type selected
function setDefaultDiscountApplyOptions()
{
    var voucherTypeList = Ext.getCmp('type');
    var voucherType = voucherTypeList.getValue();
    var discountmethodlist = Ext.getCmp('discountmethodlist');
    var discountMethods = discountmethodlist.store;
    var discounttypelist = Ext.getCmp('discounttypelist');
    var discountType = discounttypelist.getValue();
    var discountsectionlist = Ext.getCmp('discountsectionlist');
    var discountsection = discountsectionlist.getValue();
    var discountapplyQty = Ext.getCmp('discountapplytoqty');


    if ((discountType == "BOGOF") || (discountType == "BOGPOFF") || (discountType == "BOGVOFF") || (discountType == "PERCENT") || (discountType == "FOC"))
    {
        if (discountmethodlist.getValue() == 1)
        {
            discountmethodlist.setValue(0);
        }

        discountMethods.filterBy(function(record){return (record.get('key') != 1);});
    }
    else
    {
        discountMethods.clearFilter();
    }

    if ((discountsection == 'SHIPPING') || (voucherType == {/literal}{$voucherTypeScript}{literal}))
    {
        discountmethodlist.setValue(0);
        discountmethodlist.disable();
        discountapplyQty.setValue(9999);
        discountapplyQty.disable();
    }
    else
    {
        discountmethodlist.enable();
        if (discountType == "VALUE")
        {
            discountapplyQty.setValue(9999);
            discountapplyQty.disable();
        }
        else
        {
            discountapplyQty.enable();
        }
    }
}

function initialize(pParams)
{
	voucherResultGridDataStoreObj = new Ext.data.Store({
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminVouchers.listVouchers&ref=' + sessionId + '&promotionid='+gPromotionID + '&resultvouchers=1' }),
		method:'POST',
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([
				{name:'recordid', mapping: 0},
				{name: 'companyCode', mapping: 1},
				{name: 'voucherCode', mapping: 2},
                {name: 'voucherType', mapping: 3},
                {name: 'defaultDiscount', mapping: 4},
				{name: 'voucherName', mapping: 5},
				{name: 'voucherDescription', mapping: 6},
				{name: 'startDate', mapping: 7},
				{name: 'endDate', mapping: 8},
				{name: 'productCode', mapping: 9},
				{name: 'productName', mapping: 10},
				{name: 'groupCode', mapping: 11},
				{name: 'userId', mapping: 12},
				{name: 'userName', mapping: 13},
				{name: 'repeatType', mapping: 14},
				{name: 'discountSection', mapping: 15},
				{name: 'discountType', mapping: 16},
				{name: 'discountValue', mapping: 17},
				{name: 'isActive', mapping: 18}
			])
		),
		sortInfo:{field: 'recordid', direction: "ASC"},
		baseParams:{csrf_token: Ext.taopix.getCSRFToken()}
	});

	onResultCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
			gridDataStoreObj.reload();

			if (Ext.getCmp('dialogVouchersResults').isVisible())
			{
				Ext.getCmp('dialogVouchersResults').close();
			}
		}
	};

	function onResultDelete()
	{
		var onDeleteConfirmed = function(btn)
		{
			if (btn == "yes")
			{
				var paramArray = [];
				paramArray['promotionid'] = gPromotionID;
				Ext.taopix.formPost(dialogVouchersResultsObj, paramArray, 'index.php?fsaction=AdminVouchers.deleteNew&ref='+sessionId, "{/literal}{#str_MessageDeleting#}{literal}", onResultCallback);
			}
		};
		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_DeleteNewVoucherConfirmation#}{literal}", onDeleteConfirmed);
	};

	function onResultExport()
	{
		location.replace('index.php?fsaction=AdminVouchers.export&ref='+sessionId+'&promotionid='+gPromotionID+'&useCached=1');
	};

	var columnRendererResult = function(value, p, record, rowIndex, colIndex, store)
	{
		var className = '';

		if (record.data.isActive == 0)
		{
			if (colIndex == 11) value = "{/literal}{#str_LabelInactive#}{literal}";
			className = 'class = "inactive"'
		}
		else
		{
			if (colIndex == 11) value = "{/literal}{#str_LabelActive#}{literal}";
		}
		return '<span '+className+'>'+value+'</span>';
	};

	dialogVouchersResultsObj = new Ext.Window({
		id: 'dialogVouchersResults',
		title: "{/literal}{#str_TitleVoucherCreationResult#}{literal}",
		closable:true,
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		width: 680,
		height: 430,
		ctCls: 'grid',
		items:
		[
			{
				xtype: 'grid',
	   			id: 'vouchersResultGrid',
	   			store: voucherResultGridDataStoreObj,
	    		cm: new Ext.grid.ColumnModel({
					defaults: {	sortable: true, resizable: true },
					columns: [
		    			{ id:'companyCode', header: "{/literal}{#str_LabelCompany#}{literal}", dataIndex: 'companyCode', hidden:true },
		    			{ header: "{/literal}{#str_LabelCode#}{literal}", dataIndex: 'voucherCode', width:230, renderer: columnRendererResult },
		    			{ header: "{/literal}{#str_LabelVoucherType#}{literal}", dataIndex: 'voucherType', width:150, renderer: columnRendererResult },
	        			{ id:'voucherName', header: "{/literal}{#str_LabelName#}{literal}", dataIndex: 'voucherName', width:200, renderer: columnRendererResult, sortable: false, menuDisabled: true },
						{ header: "{/literal}{#str_LabelDescription#}{literal}", dataIndex: 'voucherDescription', width:150, renderer: columnRendererResult },
	        			{ header: "{/literal}{#str_LabelStartDate#}{literal}", dataIndex: 'startDate', width:120, renderer: columnRendererResult },
	        			{ header: "{/literal}{#str_LabelEndDate#}{literal}", dataIndex: 'endDate', width:120, renderer: columnRendererResult },
	        			{ header: "{/literal}{#str_LabelProduct#}{literal}", dataIndex: 'productName', width:150, renderer: columnRendererResult },
	        			{ header: "{/literal}{#str_LabelLicenseKey#}{literal}", dataIndex: 'groupCode', width:150, renderer: columnRendererResult },
	        			{ header: "{/literal}{#str_LabelCustomer#}{literal}", dataIndex: 'userName', width:120, renderer: columnRendererResult },
	        			{ header: "{/literal}{#str_LabelRepeatType#}{literal}", dataIndex: 'repeatType', width:100, renderer: columnRendererResult },
	        			{ header: "{/literal}{#str_LabelDiscountSection#}{literal}", dataIndex: 'discountSection', width:100, renderer: columnRendererResult },
	        			{ header: "{/literal}{#str_LabelDiscountType#}{literal}", dataIndex: 'discountType', renderer: columnRendererResult, width:100, sortable: false, menuDisabled: true},
	        			{ header: "{/literal}{#str_LabelActive#}{literal}", dataIndex: 'isActive', renderer: columnRendererResult, align: 'right', width:80}
	    			]
				}),
	    		stripeRows: true,
	    		stateful: true,
	    		enableColLock:false,
				draggable:false,
				enableColumnHide:false,
				enableColumnMove:false,
				trackMouseOver:false,
				columnLines:true,
	    		stateId: 'vouchersResultGrid',
	    		tbar: [
	            	{ref: '../deleteButton', text: "{/literal}{#str_ButtonDelete#}{literal}", iconCls: 'silk-delete', handler: onResultDelete }, '-',
	            	{ref: '../exportButton',	text: "{/literal}{#str_ButtonExport#}{literal}",	iconCls: 'silk-page-white-put',	handler: onResultExport }
	    		]
			}
		]
	});


	var topPanel = new Ext.Panel({
		id: 'topPanel',
		layout: 'form',
		style:'background: #c9d8ed; padding: 3px 0; border:1px solid #8ca9cf',
		plain:true,
		bodyBorder: false,
		border: false,
		defaults: {xtype: 'textfield', labelWidth: 120, width: 270},
		labelWidth: 125,
		bodyStyle:'padding:5px 5px 0; border-top: 0px',
		items: [
			/* add or edit */
			{/literal}{if $displayMode == 0}{literal}
			{
				id: 'code',
				name: 'code',
				fieldLabel: "{/literal}{#str_LabelCode#}{literal}",
				value: "{/literal}{$code}{literal}",
				validateOnBlur: true,
				post: true,
				allowBlank: false,
				maskRe: /^\w+$/,
				maxLength: 50,
				{/literal}{if $voucherid==0}{literal}
					readOnly: false,
					style: {textTransform: "uppercase"}
				{/literal}{else}{literal}
					readOnly: true,
					style: 'background:#c9d8ed; textTransform: uppercase'
				{/literal}{/if}{literal}
			}
			{/literal}{/if}{literal}

			/* create */
			{/literal}{if $displayMode == 1}{literal}
			{
				xtype: 'container', layout: 'column', width: 650,
				items: [
					{
						width: 285,
						xtype: 'panel',
						layout: 'form',
						items: [
							{
								xtype: 'numberfield',
								id: 'qty',
								name: 'qty',
								fieldLabel: "{/literal}{#str_LabelQuantity#}{literal}",
								value: "2",
								validateOnBlur: true,
								post: true,
								allowBlank: false,
								width: 100,
								allowNegative: false,
								allowDecimals: false,
								minValue: 2,
								maxValue: 3000
							}
						]
					},
					{
						width: 345,
						xtype: 'panel',
						layout: 'form',
						items: [
							{
								xtype: 'textfield',
								id: 'codeprefix',
								name: 'codeprefix',
								fieldLabel: "{/literal}{#str_LabelCodePrefix#}{literal}",
								value: "",
								validateOnBlur: true,
								post: true,
								width: 200,
								listeners:
								{
									blur:
									{
										fn: forceAlphaNumeric
									}
								},
                                maxLength: 30
							}
						]
					}
				]
			},

			{
				xtype: 'container', layout: 'column', width: 650,
				items: [
					{
						width: 285,
						xtype: 'panel',
						layout: 'form',
						items: [
							{
								xtype: 'checkbox',
								id: 'israndom',
								name: 'israndom',
								hideLabel: true,
								boxLabel: "{/literal}{#str_LabelGenerateRandomCode#}{literal}",
								post: true,
								checked: true,
								listeners: {
									'check': function(combo, checked)
									{
										if (checked)
										{
											Ext.getCmp('vouchercodelength').enable();
											Ext.getCmp('startnumber').disable();
										}
										else
										{
											Ext.getCmp('vouchercodelength').disable();
											Ext.getCmp('startnumber').enable();
										}
									}
								}
							}
						]
					},
					{
						width: 345,
						xtype: 'panel',
						layout: 'form',
						items: [
							{
								xtype: 'numberfield',
								id: 'startnumber',
								name: 'startnumber',
								fieldLabel: "{/literal}{#str_LabelStartNumber#}{literal}",
								value: "1",
								validateOnBlur: true,
								post: true,
								allowBlank: false,
								width: 200,
								allowNegative: false,
								allowDecimals: false
							}
						]
					},
                    {
						width: 345,
						xtype: 'panel',
						layout: 'form',
						items: [
							{
								xtype: 'numberfield',
								id: 'vouchercodelength',
								name: 'vouchercodelength',
								fieldLabel: "{/literal}{#str_LabelVoucherCodeLength#}{literal}",
								value: "12",
								validateOnBlur: true,
								post: true,
								allowBlank: false,
								width: 100,
								allowNegative: false,
								allowDecimals: false,
                                itemCls: 'toolbar-dock-right',
                                minValue: 10,
								maxValue: 20
							}
						]
					}
				]
			}
			{/literal}{/if}{literal}

			/* import vouchers */
			{/literal}{if $displayMode == 2}{literal}
			{
            	xtype: 'fileuploadfield',
            	id: 'importcodes',
            	fieldLabel: "{/literal}{#str_ButtonImportCodes#}{literal}",
            	name: 'importcodes',
            	buttonText: '',
            	buttonCfg: { iconCls: 'silk-upload-icon'	},
            	height: 20,
            	allowBlank: false,
            	validateOnBlur: true
        	}
			{/literal}{/if}{literal}
		]
	});

	var langListStore = {/literal}{$langList}{literal};
	var dataList = {/literal}{$dataList}{literal};

	var deleteImg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png';
	var addimg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/add.png';

	var langPanel = new Ext.taopix.LangPanel({
		id: 'langPanel',
		name: 'name',
		height:153,
		width: 480,
		post: true,
		style: 'border:1px solid #b4b8c8',
		data: {langList: langListStore, dataList: dataList},
		settings:
		{
			headers:     {langLabel: "{/literal}{#str_LabelLanguageName#}{literal}",  textLabel: "{/literal}{#str_LabelName#}{literal}", deletePic: deleteImg, addPic: addimg},
			defaultText: {langBlank: "{/literal}{#str_LabelSelectLanguage#}{literal}",  textBlank: "{/literal}{#str_ExtJsTypeValue#}{literal}", defaultValue: "{/literal}{$defaultlanguagecode}{literal}"},
			columnWidth: {langCol: 200, textCol: 217, delCol: 35},
			fieldWidth:  {langField: 185, textField: 202},
			errorMsg:    {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"}
		}
	});

	var prevLicenseID = '';

	var tabPanel = {
		xtype: 'tabpanel',
		id: 'maintabpanel',
		deferredRender: false,
		activeTab: 0,
		width: 680,
		height: 585,
		shadow: true,
		plain:true,
		bodyBorder: false,
		border: false,
		style:'margin-top:6px; ',
		bodyStyle:'border-right: 1px solid #96bde7; border-left: 1px solid #96bde7; ',
		defaults:{frame: false, autoScroll: true, hideMode:'offsets', layout: 'form', labelWidth: 135, bodyStyle:'padding:5px 10px 0 10px; border-top: 0px; background-color: #eaf0f8;'},
		//defaults:{frame: false, autoScroll: true, hideMode:'offsets', layout: 'form', labelWidth: 135, bodyStyle:'padding:0px 0px 0 0px; border-top: 0px; background-color: #eaf0f8;'},
		items: [
			{
				title: "{/literal}{#str_LabelDetails#}{literal}",
				defaults:{xtype: 'textfield', width: 150},
				items: [
					{
						xtype: 'combo',
						id: 'type',
						name: 'type',
						mode: 'local',
						editable: false,
						forceSelection: true,
						selectOnFocus: true,
						triggerAction: 'all',
						fieldLabel: "{/literal}{#str_LabelVoucherType#}{literal}",
						width: 270,
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data:{/literal}{$voucherTypes}{literal}
						}),
						valueField: 'id',
						displayField: 'name',
						useID: true,
						value: "{/literal}{$type}{literal}",
						post: true,
						selectOnFocus: true,
						listeners:
						{
					    	select: function(combo, rec, index)
				            {
				                if((prevVoucherType != combo.value) && (prevVoucherType == {/literal}{$voucherTypePrepaid}{literal}))
				                {
				                    Ext.Msg.show(
				                    {
                                        title: "{/literal}{#str_TitleVoucherTypeChange#}{literal}",
                                        msg: "{/literal}{#str_TitleVoucherTypeMessage#}{literal}",
                                        buttons: Ext.Msg.YESNO,
                                        fn: function(buttonid)
                                        {
                                            if(buttonid == 'no')
                                            {
                                                // reset to previous voucher type
                                                combo.setValue(prevVoucherType);
                                            }
                                            else
                                            {
                                                // switch to new voucher type
                                                prevVoucherType = combo.value;
                                                setVoucherOptions(prevVoucherType);
                                            }
                                        }
				                    });
				                }
				                else
				                {
				                    // switch to new voucher type
                                    prevVoucherType = combo.value;
                                    setVoucherOptions(prevVoucherType);
				                }

				            }
						}
					},
                    {
                        xtype: 'checkbox',
                        id: 'defaultdiscount',
                        fieldLabel: "",
                        value: "{/literal}{$defaultdiscount}{literal}",
                        validateOnBlur: true,
                        boxLabel: "{/literal}{#str_LabelDefaultDiscount#}{literal}",
                        post: true,
                        allowBlank: false,
                        checked: "{/literal}{$defaultdiscountchecked}{literal}" == 'checked' ? true : false,
                        mode: 'local',
                        listeners:
                        {
                            'check': function(checkbox, checked)
                            {
                                setDefaultDiscountOptions();
                            }
                        }
                    },
					new Ext.form.DateField({ fieldLabel: "{/literal}{#str_LabelStartDate#}{literal}", name: 'startdate', id: 'startdate', validateOnBlur:true, endDateField: 'enddate', format: gDateFormat, value: "{/literal}{$startdate}{literal}" {/literal}{if $promotionid > 0}{literal}, disabled: true {/literal}{/if}{literal} }),
					new Ext.form.DateField({ fieldLabel: "{/literal}{#str_LabelEndDate#}{literal}",   name: 'enddate',   id: 'enddate', validateOnBlur:true, startDateField: 'startdate', format: gDateFormat, value: "{/literal}{$enddate}{literal}" {/literal}{if $promotionid > 0}{literal}, disabled: true {/literal}{/if}{literal} }),
					{
						xtype: 'combo',
						id: 'repeattypelist',
						name: 'repeattype',
						mode: 'local',
						editable: false,
						forceSelection: true,
						selectOnFocus: true,
						triggerAction: 'all',
						fieldLabel: "{/literal}{#str_LabelRepeatType#}{literal}",
						width: 270,
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data: [
								{/literal}
								{section name=index loop=$repeattypelist}
									{if $smarty.section.index.last}
										["{$repeattypelist[index].id}", "{$repeattypelist[index].name}"]
									{else}
										["{$repeattypelist[index].id}", "{$repeattypelist[index].name}"],
									{/if}
								{/section}
								{literal}
							]
						}),
						valueField: 'id',
						displayField: 'name',
						useID: true,
						value: "{/literal}{$repeattypecode}{literal}",
                        lastQuery: '',
						post: true
					},

					{
						xtype: 'combo',
						id: 'discounttypelist',
						name: 'discounttype',
						mode: 'local',
						editable: false,
						forceSelection: true,
						selectOnFocus: true,
						triggerAction: 'all',
						fieldLabel: "{/literal}{#str_LabelDiscountType#}{literal}",
						width: 270,
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data: [
								{/literal}
								{section name=index loop=$discounttypelist}
									{if $smarty.section.index.last}
										["{$discounttypelist[index].id}", "{$discounttypelist[index].name}"]
									{else}
										["{$discounttypelist[index].id}", "{$discounttypelist[index].name}"],
									{/if}
								{/section}
								{literal}
							]
						}),
						listeners:
						{
							select:
							{
								fn: function()
								{
									validateDiscountSection();
								}
							}
						},
						valueField: 'id',
						displayField: 'name',
						useID: true,
						value: "{/literal}{$discounttypecode}{literal}",
						post: true
					},

					{
						xtype: 'combo',
						id: 'discountsectionlist',
						name: 'discountsection',
						mode: 'local',
						editable: false,
						forceSelection: true,
						selectOnFocus: true,
						triggerAction: 'all',
						fieldLabel: "{/literal}{#str_LabelDiscountSection#}{literal}",
						width: 270,
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data: [
								{/literal}
								{section name=index loop=$discountsectionlist}
									{if $smarty.section.index.last}
										["{$discountsectionlist[index].id}", "{$discountsectionlist[index].name}"]
									{else}
										["{$discountsectionlist[index].id}", "{$discountsectionlist[index].name}"],
									{/if}
								{/section}
								{literal}
							]
						}),
						listeners:
						{
							select:
							{
								fn: function()
								{
									validateDiscountSection();
								}
							}
						},
						valueField: 'id',
						displayField: 'name',
						useID: true,
						value: "{/literal}{$discountsectioncode}{literal}",
						post: true
					},
					{
						xtype: 'container', layout: 'column', width: 600,
						items: [
							{
								width: 280,
								xtype: 'panel',
								layout: 'form',
								items: [
									{
										xtype:"numberfield",
										allowNegative: false,
										allowDecimals: true,
										decimalPrecision: 2,
										id: 'discountvalue',
										name: 'discountvalue',
										fieldLabel: "{/literal}{#str_LabelDiscountValue#}{literal}",
										value: "{/literal}{$discountvalue}{literal}",
										post: true,
										width: 100,
										validateOnBlur: true
									},
									{
										xtype:"fixedPrecisionNumberField",
										allowNegative: false,
										allowDecimals: true,
										decimalPrecision: 2,
										id: 'sellprice',
										name: 'sellprice',
										fieldLabel: "{/literal}{#str_LabelSellPrice#}{literal}",
										value: "{/literal}{$sellprice}{literal}",
										post: true,
										width: 100,
										validateOnBlur: true,
										triggerAction: 'all',
										listeners:
										{
											blur: function(tb)
											{
                                                Ext.getCmp('licenseevalue').setValue(tb.value - Ext.getCmp('agentfee').value);
											}
										}
									},
									{
										xtype:"fixedPrecisionNumberField",
										allowNegative: false,
										allowDecimals: true,
										decimalPrecision: 2,
										id: 'agentfee',
										name: 'agentfee',
										fieldLabel: "{/literal}{#str_LabelAgentFee#}{literal}",
										value: "{/literal}{$agentfee}{literal}",
										post: true,
										width: 100,
										validateOnBlur: true,
										triggerAction: 'all',
										listeners:
										{
											blur: function(tb, e)
											{
                                                Ext.getCmp('licenseevalue').setValue(Ext.getCmp('sellprice').value - tb.value);
											}
										}
									},
									{
										xtype:"fixedPrecisionNumberField",
										allowNegative: false,
										allowDecimals: true,
										decimalPrecision: 2,
										readOnly: true,
										style: { background: "#dee9f6"},
										id: 'licenseevalue',
										name: 'licenseevalue',
										fieldLabel: "{/literal}{#str_LabelLicenseeValue#}{literal}",
										value: "{/literal}{$licenseevalue}{literal}",
										post: true,
										width: 100,
										validateOnBlur: true
									}
								]
							},
							{ xtype: 'label', id: 'percentLabel', text: '%', style:'margin-left: -35px' {/literal}{if $discounttypecode == 'PERCENT' || $discounttypecode == 'BOGPOFF'}{literal}, hidden: false {/literal}{else}{literal}, hidden: true	{/literal}{/if}{literal} }
						]
					},
					{
						xtype: 'panel',
	    				width: 500,
	    				fieldLabel: "{/literal}{#str_LabelName#}{literal}",
	    				items: langPanel
					},
					{
						xtype: 'textarea',
						id: 'voucherDescription',
						name: 'description',
						fieldLabel: "{/literal}{#str_LabelDescription#}{literal}",
						width: 270,
						height: 60,
						value: unescapeJS(gDescription),
						maxLength: 512,
						post: true
					}
				]
			},
			{
				title: "{/literal}{#str_LabelSettings#}{literal}",
                id: "settingsTab",
				defaults: {xtype: 'textfield', width: 450},
				items: [
					{ xtype: 'radiogroup', fieldLabel:"{/literal}{#str_SectionTitleProducts#}{literal}" ,columns: 1, autoWidth:true,
					items: [
    					{
							boxLabel: "{/literal}{#str_LabelAll#}{literal}", 
							name: 'productsRadio', 
							id:'allProducts', 
							inputValue: 'D', 
							checked: true,
							listeners: {'check': setProductsRestriction}
						},
						{ xtype : 'container', border : false, layout : 'column',  autoHeight:true,
						items : [
							{ xtype : 'container', layout : 'form', style:'margin-right:10px;',
								items : new Ext.form.Radio({ boxLabel: "{/literal}{#str_LabelProductGroup#}{literal}", 
								hideLabel:true, 
								name: 'productsRadio', 
								id:'productGroupRadio', 
								inputValue: 'O',
								listeners: {'check': setProductsRestriction}
								})  
							},
							{ xtype : 'container', layout : 'form',
								items : new Ext.form.ComboBox({ id: 'productGroupList', name: 'productGroupList', hiddenName:'productgrouplist_hn', validationEvent:false,
								hiddenId:'productgrouplist_hi',	mode: 'local', editable: false,	 forceSelection: true,
								store: new Ext.data.ArrayStore({ id: 0, fields: ['productGroupID', 'productGroupName'],	data:[
								{/literal}
										{section name=index loop=$productgroupslist}
											{if $smarty.section.index.last}
												["{$productgroupslist[index].id}", "{$productgroupslist[index].name}"]
											{else}
												["{$productgroupslist[index].id}", "{$productgroupslist[index].name}"],
											{/if}
										{/section}
								{literal}] }),
								valueField: 'productGroupID', displayField: 'productGroupName', useID: true, post: true, hideLabel:true,
								allowBlank: false, triggerAction: 'all', width: 250
								})
							}
						]},
						{ xtype : 'container', border : false, layout : 'column',  autoHeight:true,
						items : [
							{ xtype : 'container', layout : 'form', style:'margin-right:10px;',
								items : new Ext.form.Radio({
									boxLabel: "{/literal}{#str_LabelSpecificProduct#}{literal}",
									hideLabel:true,
									name: 'productsRadio',
									id:'specificProductRadio',
									inputValue: 'O',
									listeners: {'check': setProductsRestriction}
									})  
							},
							{ xtype : 'container', layout : 'form',
								items : {
								xtype: 'combo',
								id: 'productsList',
								name: 'productsList',
								mode: 'local',
								editable: false,
								forceSelection: true,
								hideLabel: true,
								selectOnFocus: true,
								triggerAction: 'all',
								width: 250,
								store: new Ext.data.ArrayStore({
									id: 0,
									fields: ['id', 'name'],
									data: [
										{/literal}
										{section name=index loop=$productlist}
											{if $smarty.section.index.last}
												["{$productlist[index].id}", "{$productlist[index].name}"]
											{else}
												["{$productlist[index].id}", "{$productlist[index].name}"],
											{/if}
										{/section}
										{literal}
									]
								}),
								displayField:'name',
								valueField:'id'
								}
							}
						]}
					]},
					{
						xtype: 'combo',
						id: 'grouplist',
						name: 'groupcode',
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
						valueField: 'id',
						displayField: 'name',
						useID: true,
						value: "{/literal}{$selectedLicenseCode}{literal}",
						post: true,
						listeners:
						{
							'select': function(combo, record, index)
							{

								var userlist = Ext.getCmp('userlist');
								var userlistStore = userlist.store;

								if (prevLicenseID!=record.get('id'))
								{
									prevLicenseID = record.get('id');

									if(record.get('id')!='ALL')
									{
										userlist.enable();
										userlistStore.baseParams.group = record.get('id');
										userlistStore.load({params: { group: record.get('id')}});
									}

									userlist.setValue(0);
								}
							}
						}
					},

					{/literal}{if $optionms == 1 && $showProd == 1}{literal}
					{
						xtype: 'combo',
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
							data: [
								{/literal}
								{section name=index loop=$productionsiteslist}
									{if $smarty.section.index.last}
										["{$productionsiteslist[index].id}", "{$productionsiteslist[index].name}"]
									{else}
										["{$productionsiteslist[index].id}", "{$productionsiteslist[index].name}"],
									{/if}
								{/section}
								{literal}
							]
						}),
						valueField: 'id',
						displayField: 'name',
						useID: true,
						value: "{/literal}{$prodSiteCode}{literal}",
						post: true,
						listeners:
						{
							'select': function(combo, newValue, oldValue)
							{
								if (gVoucherUsedInOrder*1 > 0)
    							{
    								Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorVouchersAssignedToOrder#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
	   								Ext.getCmp('productionsitelist').setValue(companyGlobalValue);
    							}
							},
							'beforeselect': function(combo, record, index)
							{
								companyGlobalValue = combo.getValue();
							}
						}
					},
					{/literal}{/if}{literal}

					{
						xtype: 'combo',
						id: 'userlist',
						name: 'userid',
						store: new Ext.data.Store(
			            {
			                url: '?fsaction=Admin.searchCustomers&ref=' + sessionId,
			                baseParams:
			                {
			                	group:  '{/literal}{$selectedLicenseCode}{literal}',
			                	limit: 10,
			                	start: 0,
								csrf_token: Ext.taopix.getCSRFToken()
			                },
			                reader: new Ext.data.JsonReader(
			                {
			                    root: 'users',
			                    totalProperty: 'totalcount',
			                    id: 'id'
			                },
			                [
			                    {name: 'id'},
			                    {name: 'login'},
			                    {name: 'firstname'},
			                    {name: 'lastname'},
			                    {name: 'emailaddress'},
			                    {name: 'displayname',
			                        convert : function(value, record)
			                        {
			                            if (record.id==0)
			                            {
			                                return record.firstname;
			                            }
			                            else
			                            {
			                                return record.firstname + " " + record.lastname + " (" + record.login + ")";
			                            }
			                        }
			                    },
                                {name: 'address1'},
                                {name: 'postcode'},
                                {name: 'telephonenumber'}
			                ]),
			                listeners:
			                {
			                    load: function(store, records, index)
			                    {
			                        if (store.lastOptions.params.id!=undefined)
			                        {
			                        	var record = store.getById({/literal}{$userid}{literal});
			                            Ext.getCmp('userlist').setValue(record.get('id'));
			                        }

			                    }
			                }
			            }),
			            displayField:'displayname',
			            valueField: 'id',
			            typeAhead: false,
			            loadingText: "{/literal}{#str_LabelSearching#}{literal}",
			            pageSize:10,
			            hideTrigger:false,
			            selectOnFocus: true,
                        post: true,
                        useID: true,
			            valid: true,
                        fieldLabel: "{/literal}{#str_LabelCustomer#}{literal}",
                        triggerAction: 'all',
                        tpl: new Ext.XTemplate(
                            '<tpl for=".">',
                                '<div class="search-item" style="padding: 2px 20px 2px 4px; width:422px; display:table; border-bottom: thin solid #999999;">',
                                    '<div style="display:block; float:left"><div><h3>{firstname} {lastname}</h3></div><div>{emailaddress}</div><div>{telephonenumber}</div></div>',
                                    '<div style="display:block; float:right; text-align:right;"><div>{login}</div><div>{address1}</div><div>{postcode}</div></div>',
                                '</div>',
                            '</tpl>'
                        ),
			            itemSelector: 'div.search-item',
			            listeners:
			            {
			            	select: function(combo)
			            	{
			            		combo.valid = true;
			            	},
			            	blur: function(combo)
			            	{
			            		combo.valid = true;

			            		if (combo.value=='')
			            		{
			            			combo.setValue('All');
			            		}
			            		else if (combo.value!='All' && combo.value!=0)
			            		{
									Ext.Ajax.request(
									{
										url: '?fsaction=Admin.searchCustomers&ref=' + sessionId,
									    params:
									    {
									    	id: combo.value,
									    	group: combo.store.baseParams.group
									    },
									    success: function(response)
									    {
									       var jsonObj = eval('(' + response.responseText + ')');

									       if (jsonObj.totalcount==0)
									       {
									       		combo.markInvalid("{/literal}{#str_ErrorCustomer#}{literal}");
									       		combo.valid = false;
									       }

									    }
									});
			            		}

			            	}
			            }
                    },
					{
						xtype: 'numberfield',
						id: 'minqty',
						name: 'minqty',
						fieldLabel: "{/literal}{#str_LabelMinOrderQty#}{literal}",
						value: "{/literal}{$minqty}{literal}",
						validateOnBlur: true,
						post: true,
						allowBlank: false,
						allowNegative: false,
						allowDecimals: false,
						width: 100
					},

					{
						xtype: 'numberfield',
						id: 'maxqty',
						name: 'maxqty',
						fieldLabel: "{/literal}{#str_LabelMaxOrderQty#}{literal}",
						value: "{/literal}{$maxqty}{literal}",
						validateOnBlur: true,
						post: true,
						allowBlank: false,
						allowNegative: false,
						allowDecimals: false,
						width: 100
					},
					{
						xtype: 'checkbox',
						id: 'lockqty',
						fieldLabel: "",
						value: "{/literal}{$maxqty}{literal}",
						validateOnBlur: true,
						boxLabel: "{/literal}{#str_LabelLockOrderQty#}{literal}",
						post: true,
						allowBlank: false,
						checked: "{/literal}{$lockqtychecked}{literal}" == 'checked' ? true : false
					},
					{
						xtype: 'numberfield',
						id: 'minordervalue',
						name: 'minordervalue',
						fieldLabel: "{/literal}{#str_LabelMinimumOrderValue#}{literal}",
						value: "{/literal}{$minimumordervalue}{literal}",
						validateOnBlur: true,
						post: true,
						allowBlank: false,
						allowNegative: false,
						allowDecimals: true,
						width: 100,
						decimalPrecision: 2
					},
					{
						xtype: 'checkbox',
						id: 'minordervalueincludesshipping',
						fieldLabel: "",
						validateOnBlur: true,
						boxLabel: "{/literal}{#str_LabelIncludesShipping#}{literal}",
						post: true,
						allowBlank: false,
						checked: "{/literal}{$minordervalueincludesshipping}{literal}" == 'checked' ? true : false
					},
					{
						xtype: 'checkbox',
						id: 'minordervalueincludestax',
						fieldLabel: "",
						validateOnBlur: true,
						boxLabel: "{/literal}{#str_LabelIncludesTax#}{literal}",
						post: true,
						allowBlank: false,
						checked: "{/literal}{$minordervalueincludestax}{literal}" == 'checked' ? true : false
					},
                    {
						xtype: 'combo',
						id: 'discountmethodlist',
						name: 'discountmethod',
						mode: 'local',
						editable: false,
						forceSelection: true,
						selectOnFocus: true,
						triggerAction: 'all',
						fieldLabel: "{/literal}{#str_LabelDiscountMethod#}{literal}",
						width: 270,
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['key', 'name'],
							data: [
								{/literal}
								{section name=index loop=$discountapplicationmethodlist}
									{if $smarty.section.index.last}
										["{$discountapplicationmethodlist[index].id}", "{$discountapplicationmethodlist[index].name}"]
									{else}
										["{$discountapplicationmethodlist[index].id}", "{$discountapplicationmethodlist[index].name}"],
									{/if}
								{/section}
								{literal}
							]
						}),
						listeners:
						{
							select:
							{
								fn: function()
								{
									validateDiscountSection();
								}
							},
                            expand:
                            {
                                fn: function()
								{
                                    validateDiscountSection();
                                }
                            }
						},
						valueField: 'key',
						displayField: 'name',
						useID: true,
						value: "{/literal}{$discountapplicationmethodcode}{literal}",
						post: true
                    },
					{
						xtype: 'numberfield',
						id: 'discountapplytoqty',
						name: 'discountapplytoqty',
						fieldLabel: "{/literal}{#str_LabelDiscountQty#}{literal}",
						value: "{/literal}{$discountapplytoqty}{literal}",
						validateOnBlur: true,
						post: true,
						allowBlank: false,
						allowNegative: false,
						allowDecimals: false,
						width: 100
					}
				]
			}
		]
	};

	function setInitialProductsRadioButton()
	{
		{/literal}
		{if $selectedproductsradio == 'PRODUCTGROUP'}
			Ext.getCmp('productGroupRadio').setValue(true);
			Ext.getCmp('productGroupList').setValue({$selectedproductgroup});
		{elseif $selectedproductsradio == 'PRODUCT'}
			Ext.getCmp('specificProductRadio').setValue(true);
			Ext.getCmp('productsList').setValue("{$selectedProduct}");
		{else}
			Ext.getCmp('allProducts').setValue(true);
		{/if}
		{literal}
	}
	

	var dialogFormPanelObj = new Ext.taopix.FormPanel({
		id: 'mainform',
		header: false,
		frame:true,
		width: 700,
		layout: 'form',
		fileUpload: true,
		defaultType: 'textfield',
		bodyStyle:'border-bottom: 1px solid #96bde7;',
		autoHeight: true,
		items: [ topPanel, tabPanel ],
		baseParams:	{ ref: '{/literal}{$ref}{literal}' }
	});


	var gDialogObjVouchers = new Ext.Window(
	{
		id: 'dialogVouchers',
		closable: false,
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		width: 710,
		autoHeight: true,
		items: dialogFormPanelObj,
		listeners:
		{
			'close':
			{
				fn: function()
				{
					singleVoucherEditWindowExists = false;
				}
			}
		},
		title: "{/literal}{$title}{literal}",
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
				handler: function(btn, ev)
				{
					showResultWindow = false;
					gDialogObjVouchers.close();
				},
				cls: 'x-btn-right'
			},
			{
				text: "{/literal}{$actionbutton}{literal}",
				id: 'updateButton',
				handler: addSaveHandler,
				cls: 'x-btn-right'
			}
		]
	});


	gDialogObjVouchers.show();
	setInitialProductsRadioButton();
	setProductsRestriction();

	setVoucherOptions(Ext.getCmp('type').value);
	setDefaultDiscountOptions();

	var prevVoucherType = Ext.getCmp('type').value;
	var startnumber = Ext.getCmp('startnumber');

	if (startnumber)
	{
		startnumber.disable();
	}

	Ext.getCmp('isactive').setValue("{/literal}{$activechecked}{literal}" == 'checked' ? true : false);

	Ext.getCmp('userlist').store.load({params: { id: {/literal}{$userid}{literal}}});

};

{/literal}