<?php
/* Smarty version 4.5.3, created on 2026-04-23 01:30:22
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\vouchers\voucheredit.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69e9762ece1777_46339044',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fe8bc09834f08535b2be05c00a58a0c0bfcc494b' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\vouchers\\voucheredit.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69e9762ece1777_46339044 (Smarty_Internal_Template $_smarty_tpl) {
?>
var gDateFormat = "<?php echo $_smarty_tpl->tpl_vars['dateformat']->value;?>
";
var gEarliestDate = "<?php echo $_smarty_tpl->tpl_vars['earliestdate']->value;?>
";
var gLatestDate = "<?php echo $_smarty_tpl->tpl_vars['latestdate']->value;?>
";
var gProductCount = <?php echo $_smarty_tpl->tpl_vars['productcount']->value;?>
;
var gPromotionID = <?php echo $_smarty_tpl->tpl_vars['promotionid']->value;?>
;
var gVoucherID = <?php echo $_smarty_tpl->tpl_vars['voucherid']->value;?>
;
var gVoucherUsedInOrder = <?php echo $_smarty_tpl->tpl_vars['voucherusedinorder']->value;?>
;
companyGlobalValue = '';
var gDescription = '<?php echo $_smarty_tpl->tpl_vars['description']->value;?>
';


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
		Ext.taopix.formPanelPost(fp, form, paramsGlobal, 'index.php?fsaction=AdminVouchers.create', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
", onCallback);
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
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenProductionSite', value: ((Ext.getCmp('productionsitelist')) ? Ext.getCmp('productionsitelist').getValue() : '<?php echo $_smarty_tpl->tpl_vars['prodSiteValue']->value;?>
') });
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenActive', value: ((Ext.getCmp('isactive').checked) ? '1' : '0')});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenPromoCode', value: "<?php echo $_smarty_tpl->tpl_vars['promotioncode']->value;?>
" });
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
            waitMsg: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
',
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
            	Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: action.result.msg, buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
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

		<?php if ($_smarty_tpl->tpl_vars['displayMode']->value == 1) {?>
			var voucherStartNumber = 0;

			if (!Ext.getCmp('israndom').checked)
			{
				voucherStartNumber = Ext.getCmp('startnumber').getValue();

        		if (Ext.getCmp('codeprefix').getValue() == '')
        		{
            		Ext.getCmp('codeprefix').markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoVoucherCodePrefix');?>
");
            		maintabpanel.setActiveTab(0);
					return false;
        		}
			}

			var voucherQty = Ext.getCmp('qty').getValue();
    		if ((voucherQty < 2) || (voucherQty > 3000))
    		{
        		Ext.getCmp('qty').markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidQuantity');?>
");
        		maintabpanel.setActiveTab(0);
	    		return false;
    		}
		<?php }?>

		var startdate = Ext.getCmp('startdate');
		var enddate = Ext.getCmp('enddate');

		<?php if ($_smarty_tpl->tpl_vars['promotionid']->value == 0) {?>


		if (! parsePHPDate(startdate.getRawValue(), gDateFormat))
    	{
			startdate.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidStartDate');?>
");
			maintabpanel.setActiveTab(0);
			return false;
    	}

    	if (! parsePHPDate(enddate.getRawValue(), gDateFormat))
    	{
			enddate.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidEndDate');?>
");
			maintabpanel.setActiveTab(0);
			return false;
    	}

    	if (comparePHPDates(enddate.getRawValue(), startdate.getRawValue(), gDateFormat) != 1)
	    {
			enddate.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidEndDate');?>
");
			maintabpanel.setActiveTab(0);
			return false;
	    }

    	/* make sure the dates are in range */
	    if (comparePHPDates(startdate.getRawValue(), gEarliestDate, gDateFormat) != 1)
	    {
			startdate.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidStartDate');?>
");
			maintabpanel.setActiveTab(0);
			return false;
	    }

	    if (comparePHPDates(gLatestDate, startdate.getRawValue(), gDateFormat) != 1)
	    {
			startdate.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidStartDate');?>
");
			maintabpanel.setActiveTab(0);
			return false;
	    }

	    if (comparePHPDates(enddate.getRawValue(), gEarliestDate, gDateFormat) != 1)
	    {
			enddate.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidEndDate');?>
");
			maintabpanel.setActiveTab(0);
			return false;
	    }

	    if (comparePHPDates(gLatestDate, enddate.getRawValue(), gDateFormat) != 1)
	    {
			enddate.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidEndDate');?>
");
			maintabpanel.setActiveTab(0);
			return false;
	    }

		<?php }?>

		var voucherType = Ext.getCmp('type');

		if(voucherType.store.getById(voucherType.value).data.id==<?php echo $_smarty_tpl->tpl_vars['voucherTypeDiscount']->value;?>
)
		{

			var discountType = Ext.getCmp('discounttypelist');
			var discountValue = Ext.getCmp('discountvalue');
			var invalid = false;

	    	if ((discountType.getValue() == "VALUE") || (discountType.getValue() == "VALUESET") || (discountType.getValue() == "BOGVOFF"))
	    	{
	        	if (discountValue.getValue() <= 0.00)
	        	{
	            	discountValue.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidDiscountValue');?>
");
	            	maintabpanel.setActiveTab(0);
	            	invalid = true;
	        	}
	    	}
	    	else if ((discountType.getValue() == "PERCENT") || (discountType.getValue() == "BOGPOFF"))
	    	{
	        	if ((discountValue.getValue() <= 0.00) || (discountValue.getValue() >= 100.00))
	        	{
	            	discountValue.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidDiscountValue');?>
");
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
	    else if(voucherType.store.getById(voucherType.value).data.id==<?php echo $_smarty_tpl->tpl_vars['voucherTypePrepaid']->value;?>
)
	    {

	    	var discountValue = Ext.getCmp('discountvalue');

	    	if (discountValue.getValue()<=0)
	    	{
	    		discountValue.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidDiscountValue');?>
");
	    		maintabpanel.setActiveTab(0);
	    		return false;
	    	}

	    	var sellprice = Ext.getCmp('sellprice');

	    	if (sellprice.getValue()<=0)
	    	{
	    		sellprice.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidSellPrice');?>
");
	    		maintabpanel.setActiveTab(0);
	    		return false;
	    	}

	    	var licenseevalue = Ext.getCmp('licenseevalue');

    		if (licenseevalue.getValue()<0)
			{
				var agentfee = Ext.getCmp('agentfee');

				if (sellprice.getValue()>agentfee.getValue())
				{
					agentfee.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidAgentFee');?>
");
				}
				else
				{
					licenseevalue.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidLicenseeValue');?>
");
				}

				maintabpanel.setActiveTab(0);

				return false;
			}
		}

		if (!Ext.getCmp('langPanel').isValid())
		{
			Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoName');?>
", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
			maintabpanel.setActiveTab(0);
	    	return false;
		}

		/* second tab */

		var userlist = Ext.getCmp('userlist');
		if (!userlist.valid)
		{
			Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorCustomer');?>
", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
			userlist.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorCustomer');?>
");
			maintabpanel.setActiveTab(1);
	    	return false;
		}

		var minOrderQty = Ext.getCmp('minqty');
    	if ((minOrderQty.getValue() < 1) || (minOrderQty.getValue() > 9999))
    	{
        	minOrderQty.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidMinQuantity');?>
");
        	maintabpanel.setActiveTab(1);
        	return false;
    	}

    	var maxOrderQty = Ext.getCmp('maxqty');
	    if ((maxOrderQty.getValue() < 1) || (maxOrderQty.getValue() > 9999))
	    {
			maxOrderQty.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidMaxQuantity');?>
");
			maintabpanel.setActiveTab(1);
	        return false;
	    }
	    if (minOrderQty.getValue() > maxOrderQty.getValue())
	    {
	        minOrderQty.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidMinQuantity2');?>
");
	        maintabpanel.setActiveTab(1);
	        return false;
	    }

        var discountapplytoqty = Ext.getCmp('discountapplytoqty');
        if ((discountapplytoqty.getValue() < 1) || (discountapplytoqty.getValue() > 9999))
	    {
			discountapplytoqty.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidMaxDiscountQuantity');?>
");
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

	    parameter['productionsitecode'] = (productionsitelist) ? productionsitelist.getValue() : '<?php echo $_smarty_tpl->tpl_vars['prodSiteValue']->value;?>
';
	    parameter['isactive'] = (Ext.getCmp('isactive').checked) ? '1' : '0';

	    parameter['promotioncode'] = "<?php echo $_smarty_tpl->tpl_vars['promotioncode']->value;?>
";

		var fp = Ext.getCmp('mainform'), form = fp.getForm();

	    /* add or edit */
		<?php if ($_smarty_tpl->tpl_vars['displayMode']->value == 0) {?>
	    if (gVoucherID > 0)
	    {
	    	parameter['id'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('vouchersGrid'));
			parameter['promotionid'] = gPromotionID;
			Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=<?php echo $_smarty_tpl->tpl_vars['destaction']->value;?>
.edit', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
", onCallback);
	    }
	    else
	    {
	        parameter['promotionid'] = gPromotionID;
			Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=<?php echo $_smarty_tpl->tpl_vars['destaction']->value;?>
.add', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
", onCallback);
	    }
	    <?php }?>

	    /* create */
		<?php if ($_smarty_tpl->tpl_vars['displayMode']->value == 1) {?>
			parameter['israndom'] = (Ext.getCmp('israndom').checked) ? '1' : '0';
			parameter['startnumber'] = voucherStartNumber;

			paramsGlobal = parameter;
			var message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_CreateVoucherConfirmation');?>
".replace("^0", voucherQty);
			Ext.MessageBox.minWidth = 350;
			Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", message, onCreateConfirmed);
		<?php }?>

		<?php if ($_smarty_tpl->tpl_vars['displayMode']->value == 2) {?>
			paramsGlobal = parameter;
			Ext.MessageBox.minWidth = 350;
			Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ImportVoucherConfirmation');?>
", onImportConfirmed);
		<?php }?>
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
        case <?php echo $_smarty_tpl->tpl_vars['voucherTypeDiscount']->value;?>
:
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

        case <?php echo $_smarty_tpl->tpl_vars['voucherTypePrepaid']->value;?>
:
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

        case <?php echo $_smarty_tpl->tpl_vars['voucherTypeScript']->value;?>
:
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

    if ((discountsection == 'SHIPPING') || (voucherType == <?php echo $_smarty_tpl->tpl_vars['voucherTypeScript']->value;?>
))
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
				Ext.taopix.formPost(dialogVouchersResultsObj, paramArray, 'index.php?fsaction=AdminVouchers.deleteNew&ref='+sessionId, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleting');?>
", onResultCallback);
			}
		};
		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DeleteNewVoucherConfirmation');?>
", onDeleteConfirmed);
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
			if (colIndex == 11) value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInactive');?>
";
			className = 'class = "inactive"'
		}
		else
		{
			if (colIndex == 11) value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
";
		}
		return '<span '+className+'>'+value+'</span>';
	};

	dialogVouchersResultsObj = new Ext.Window({
		id: 'dialogVouchersResults',
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleVoucherCreationResult');?>
",
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
		    			{ id:'companyCode', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", dataIndex: 'companyCode', hidden:true },
		    			{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCode');?>
", dataIndex: 'voucherCode', width:230, renderer: columnRendererResult },
		    			{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelVoucherType');?>
", dataIndex: 'voucherType', width:150, renderer: columnRendererResult },
	        			{ id:'voucherName', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
", dataIndex: 'voucherName', width:200, renderer: columnRendererResult, sortable: false, menuDisabled: true },
						{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDescription');?>
", dataIndex: 'voucherDescription', width:150, renderer: columnRendererResult },
	        			{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStartDate');?>
", dataIndex: 'startDate', width:120, renderer: columnRendererResult },
	        			{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEndDate');?>
", dataIndex: 'endDate', width:120, renderer: columnRendererResult },
	        			{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProduct');?>
", dataIndex: 'productName', width:150, renderer: columnRendererResult },
	        			{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKey');?>
", dataIndex: 'groupCode', width:150, renderer: columnRendererResult },
	        			{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCustomer');?>
", dataIndex: 'userName', width:120, renderer: columnRendererResult },
	        			{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRepeatType');?>
", dataIndex: 'repeatType', width:100, renderer: columnRendererResult },
	        			{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDiscountSection');?>
", dataIndex: 'discountSection', width:100, renderer: columnRendererResult },
	        			{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDiscountType');?>
", dataIndex: 'discountType', renderer: columnRendererResult, width:100, sortable: false, menuDisabled: true},
	        			{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
", dataIndex: 'isActive', renderer: columnRendererResult, align: 'right', width:80}
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
	            	{ref: '../deleteButton', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDelete');?>
", iconCls: 'silk-delete', handler: onResultDelete }, '-',
	            	{ref: '../exportButton',	text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonExport');?>
",	iconCls: 'silk-page-white-put',	handler: onResultExport }
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
			<?php if ($_smarty_tpl->tpl_vars['displayMode']->value == 0) {?>
			{
				id: 'code',
				name: 'code',
				fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCode');?>
",
				value: "<?php echo $_smarty_tpl->tpl_vars['code']->value;?>
",
				validateOnBlur: true,
				post: true,
				allowBlank: false,
				maskRe: /^\w+$/,
				maxLength: 50,
				<?php if ($_smarty_tpl->tpl_vars['voucherid']->value == 0) {?>
					readOnly: false,
					style: {textTransform: "uppercase"}
				<?php } else { ?>
					readOnly: true,
					style: 'background:#c9d8ed; textTransform: uppercase'
				<?php }?>
			}
			<?php }?>

			/* create */
			<?php if ($_smarty_tpl->tpl_vars['displayMode']->value == 1) {?>
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
								fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelQuantity');?>
",
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
								fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCodePrefix');?>
",
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
								boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGenerateRandomCode');?>
",
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
								fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStartNumber');?>
",
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
								fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelVoucherCodeLength');?>
",
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
			<?php }?>

			/* import vouchers */
			<?php if ($_smarty_tpl->tpl_vars['displayMode']->value == 2) {?>
			{
            	xtype: 'fileuploadfield',
            	id: 'importcodes',
            	fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonImportCodes');?>
",
            	name: 'importcodes',
            	buttonText: '',
            	buttonCfg: { iconCls: 'silk-upload-icon'	},
            	height: 20,
            	allowBlank: false,
            	validateOnBlur: true
        	}
			<?php }?>
		]
	});

	var langListStore = <?php echo $_smarty_tpl->tpl_vars['langList']->value;?>
;
	var dataList = <?php echo $_smarty_tpl->tpl_vars['dataList']->value;?>
;

	var deleteImg = '<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/delete.png';
	var addimg = '<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/add.png';

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
			headers:     {langLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLanguageName');?>
",  textLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
", deletePic: deleteImg, addPic: addimg},
			defaultText: {langBlank: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSelectLanguage');?>
",  textBlank: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTypeValue');?>
", defaultValue: "<?php echo $_smarty_tpl->tpl_vars['defaultlanguagecode']->value;?>
"},
			columnWidth: {langCol: 200, textCol: 217, delCol: 35},
			fieldWidth:  {langField: 185, textField: 202},
			errorMsg:    {blankValue: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTextFieldBlank');?>
"}
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
				title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDetails');?>
",
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
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelVoucherType');?>
",
						width: 270,
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data:<?php echo $_smarty_tpl->tpl_vars['voucherTypes']->value;?>

						}),
						valueField: 'id',
						displayField: 'name',
						useID: true,
						value: "<?php echo $_smarty_tpl->tpl_vars['type']->value;?>
",
						post: true,
						selectOnFocus: true,
						listeners:
						{
					    	select: function(combo, rec, index)
				            {
				                if((prevVoucherType != combo.value) && (prevVoucherType == <?php echo $_smarty_tpl->tpl_vars['voucherTypePrepaid']->value;?>
))
				                {
				                    Ext.Msg.show(
				                    {
                                        title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleVoucherTypeChange');?>
",
                                        msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleVoucherTypeMessage');?>
",
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
                        value: "<?php echo $_smarty_tpl->tpl_vars['defaultdiscount']->value;?>
",
                        validateOnBlur: true,
                        boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDefaultDiscount');?>
",
                        post: true,
                        allowBlank: false,
                        checked: "<?php echo $_smarty_tpl->tpl_vars['defaultdiscountchecked']->value;?>
" == 'checked' ? true : false,
                        mode: 'local',
                        listeners:
                        {
                            'check': function(checkbox, checked)
                            {
                                setDefaultDiscountOptions();
                            }
                        }
                    },
					new Ext.form.DateField({ fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStartDate');?>
", name: 'startdate', id: 'startdate', validateOnBlur:true, endDateField: 'enddate', format: gDateFormat, value: "<?php echo $_smarty_tpl->tpl_vars['startdate']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['promotionid']->value > 0) {?>, disabled: true <?php }?> }),
					new Ext.form.DateField({ fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEndDate');?>
",   name: 'enddate',   id: 'enddate', validateOnBlur:true, startDateField: 'startdate', format: gDateFormat, value: "<?php echo $_smarty_tpl->tpl_vars['enddate']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['promotionid']->value > 0) {?>, disabled: true <?php }?> }),
					{
						xtype: 'combo',
						id: 'repeattypelist',
						name: 'repeattype',
						mode: 'local',
						editable: false,
						forceSelection: true,
						selectOnFocus: true,
						triggerAction: 'all',
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRepeatType');?>
",
						width: 270,
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data: [
								
								<?php
$__section_index_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['repeattypelist']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_0_total = $__section_index_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_0_total !== 0) {
for ($__section_index_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_0_iteration <= $__section_index_0_total; $__section_index_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_0_iteration === $__section_index_0_total);
?>
									<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
										["<?php echo $_smarty_tpl->tpl_vars['repeattypelist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['repeattypelist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"]
									<?php } else { ?>
										["<?php echo $_smarty_tpl->tpl_vars['repeattypelist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['repeattypelist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
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
						value: "<?php echo $_smarty_tpl->tpl_vars['repeattypecode']->value;?>
",
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
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDiscountType');?>
",
						width: 270,
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data: [
								
								<?php
$__section_index_1_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['discounttypelist']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_1_total = $__section_index_1_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_1_total !== 0) {
for ($__section_index_1_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_1_iteration <= $__section_index_1_total; $__section_index_1_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_1_iteration === $__section_index_1_total);
?>
									<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
										["<?php echo $_smarty_tpl->tpl_vars['discounttypelist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['discounttypelist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"]
									<?php } else { ?>
										["<?php echo $_smarty_tpl->tpl_vars['discounttypelist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['discounttypelist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"],
									<?php }?>
								<?php
}
}
?>
								
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
						value: "<?php echo $_smarty_tpl->tpl_vars['discounttypecode']->value;?>
",
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
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDiscountSection');?>
",
						width: 270,
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data: [
								
								<?php
$__section_index_2_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['discountsectionlist']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_2_total = $__section_index_2_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_2_total !== 0) {
for ($__section_index_2_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_2_iteration <= $__section_index_2_total; $__section_index_2_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_2_iteration === $__section_index_2_total);
?>
									<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
										["<?php echo $_smarty_tpl->tpl_vars['discountsectionlist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['discountsectionlist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"]
									<?php } else { ?>
										["<?php echo $_smarty_tpl->tpl_vars['discountsectionlist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['discountsectionlist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"],
									<?php }?>
								<?php
}
}
?>
								
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
						value: "<?php echo $_smarty_tpl->tpl_vars['discountsectioncode']->value;?>
",
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
										fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDiscountValue');?>
",
										value: "<?php echo $_smarty_tpl->tpl_vars['discountvalue']->value;?>
",
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
										fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSellPrice');?>
",
										value: "<?php echo $_smarty_tpl->tpl_vars['sellprice']->value;?>
",
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
										fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAgentFee');?>
",
										value: "<?php echo $_smarty_tpl->tpl_vars['agentfee']->value;?>
",
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
										fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseeValue');?>
",
										value: "<?php echo $_smarty_tpl->tpl_vars['licenseevalue']->value;?>
",
										post: true,
										width: 100,
										validateOnBlur: true
									}
								]
							},
							{ xtype: 'label', id: 'percentLabel', text: '%', style:'margin-left: -35px' <?php if ($_smarty_tpl->tpl_vars['discounttypecode']->value == 'PERCENT' || $_smarty_tpl->tpl_vars['discounttypecode']->value == 'BOGPOFF') {?>, hidden: false <?php } else { ?>, hidden: true	<?php }?> }
						]
					},
					{
						xtype: 'panel',
	    				width: 500,
	    				fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
",
	    				items: langPanel
					},
					{
						xtype: 'textarea',
						id: 'voucherDescription',
						name: 'description',
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDescription');?>
",
						width: 270,
						height: 60,
						value: unescapeJS(gDescription),
						maxLength: 512,
						post: true
					}
				]
			},
			{
				title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSettings');?>
",
                id: "settingsTab",
				defaults: {xtype: 'textfield', width: 450},
				items: [
					{ xtype: 'radiogroup', fieldLabel:"<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleProducts');?>
" ,columns: 1, autoWidth:true,
					items: [
    					{
							boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAll');?>
", 
							name: 'productsRadio', 
							id:'allProducts', 
							inputValue: 'D', 
							checked: true,
							listeners: {'check': setProductsRestriction}
						},
						{ xtype : 'container', border : false, layout : 'column',  autoHeight:true,
						items : [
							{ xtype : 'container', layout : 'form', style:'margin-right:10px;',
								items : new Ext.form.Radio({ boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProductGroup');?>
", 
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
								
										<?php
$__section_index_3_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['productgroupslist']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_3_total = $__section_index_3_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_3_total !== 0) {
for ($__section_index_3_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_3_iteration <= $__section_index_3_total; $__section_index_3_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_3_iteration === $__section_index_3_total);
?>
											<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
												["<?php echo $_smarty_tpl->tpl_vars['productgroupslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['productgroupslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"]
											<?php } else { ?>
												["<?php echo $_smarty_tpl->tpl_vars['productgroupslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['productgroupslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"],
											<?php }?>
										<?php
}
}
?>
								] }),
								valueField: 'productGroupID', displayField: 'productGroupName', useID: true, post: true, hideLabel:true,
								allowBlank: false, triggerAction: 'all', width: 250
								})
							}
						]},
						{ xtype : 'container', border : false, layout : 'column',  autoHeight:true,
						items : [
							{ xtype : 'container', layout : 'form', style:'margin-right:10px;',
								items : new Ext.form.Radio({
									boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSpecificProduct');?>
",
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
										
										<?php
$__section_index_4_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['productlist']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_4_total = $__section_index_4_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_4_total !== 0) {
for ($__section_index_4_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_4_iteration <= $__section_index_4_total; $__section_index_4_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_4_iteration === $__section_index_4_total);
?>
											<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
												["<?php echo $_smarty_tpl->tpl_vars['productlist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['productlist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"]
											<?php } else { ?>
												["<?php echo $_smarty_tpl->tpl_vars['productlist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['productlist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"],
											<?php }?>
										<?php
}
}
?>
										
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
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKey');?>
",
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data: [
								
								<?php
$__section_index_5_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['grouplist']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_5_total = $__section_index_5_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_5_total !== 0) {
for ($__section_index_5_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_5_iteration <= $__section_index_5_total; $__section_index_5_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_5_iteration === $__section_index_5_total);
?>
									<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
										["<?php echo $_smarty_tpl->tpl_vars['grouplist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['grouplist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"]
									<?php } else { ?>
										["<?php echo $_smarty_tpl->tpl_vars['grouplist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['grouplist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
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
						value: "<?php echo $_smarty_tpl->tpl_vars['selectedLicenseCode']->value;?>
",
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

					<?php if ($_smarty_tpl->tpl_vars['optionms']->value == 1 && $_smarty_tpl->tpl_vars['showProd']->value == 1) {?>
					{
						xtype: 'combo',
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
							data: [
								
								<?php
$__section_index_6_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['productionsiteslist']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_6_total = $__section_index_6_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_6_total !== 0) {
for ($__section_index_6_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_6_iteration <= $__section_index_6_total; $__section_index_6_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_6_iteration === $__section_index_6_total);
?>
									<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
										["<?php echo $_smarty_tpl->tpl_vars['productionsiteslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['productionsiteslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"]
									<?php } else { ?>
										["<?php echo $_smarty_tpl->tpl_vars['productionsiteslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['productionsiteslist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
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
						value: "<?php echo $_smarty_tpl->tpl_vars['prodSiteCode']->value;?>
",
						post: true,
						listeners:
						{
							'select': function(combo, newValue, oldValue)
							{
								if (gVoucherUsedInOrder*1 > 0)
    							{
    								Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorVouchersAssignedToOrder');?>
", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
	   								Ext.getCmp('productionsitelist').setValue(companyGlobalValue);
    							}
							},
							'beforeselect': function(combo, record, index)
							{
								companyGlobalValue = combo.getValue();
							}
						}
					},
					<?php }?>

					{
						xtype: 'combo',
						id: 'userlist',
						name: 'userid',
						store: new Ext.data.Store(
			            {
			                url: '?fsaction=Admin.searchCustomers&ref=' + sessionId,
			                baseParams:
			                {
			                	group:  '<?php echo $_smarty_tpl->tpl_vars['selectedLicenseCode']->value;?>
',
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
			                        	var record = store.getById(<?php echo $_smarty_tpl->tpl_vars['userid']->value;?>
);
			                            Ext.getCmp('userlist').setValue(record.get('id'));
			                        }

			                    }
			                }
			            }),
			            displayField:'displayname',
			            valueField: 'id',
			            typeAhead: false,
			            loadingText: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSearching');?>
",
			            pageSize:10,
			            hideTrigger:false,
			            selectOnFocus: true,
                        post: true,
                        useID: true,
			            valid: true,
                        fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCustomer');?>
",
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
									       		combo.markInvalid("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorCustomer');?>
");
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
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMinOrderQty');?>
",
						value: "<?php echo $_smarty_tpl->tpl_vars['minqty']->value;?>
",
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
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMaxOrderQty');?>
",
						value: "<?php echo $_smarty_tpl->tpl_vars['maxqty']->value;?>
",
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
						value: "<?php echo $_smarty_tpl->tpl_vars['maxqty']->value;?>
",
						validateOnBlur: true,
						boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLockOrderQty');?>
",
						post: true,
						allowBlank: false,
						checked: "<?php echo $_smarty_tpl->tpl_vars['lockqtychecked']->value;?>
" == 'checked' ? true : false
					},
					{
						xtype: 'numberfield',
						id: 'minordervalue',
						name: 'minordervalue',
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMinimumOrderValue');?>
",
						value: "<?php echo $_smarty_tpl->tpl_vars['minimumordervalue']->value;?>
",
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
						boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelIncludesShipping');?>
",
						post: true,
						allowBlank: false,
						checked: "<?php echo $_smarty_tpl->tpl_vars['minordervalueincludesshipping']->value;?>
" == 'checked' ? true : false
					},
					{
						xtype: 'checkbox',
						id: 'minordervalueincludestax',
						fieldLabel: "",
						validateOnBlur: true,
						boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelIncludesTax');?>
",
						post: true,
						allowBlank: false,
						checked: "<?php echo $_smarty_tpl->tpl_vars['minordervalueincludestax']->value;?>
" == 'checked' ? true : false
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
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDiscountMethod');?>
",
						width: 270,
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['key', 'name'],
							data: [
								
								<?php
$__section_index_7_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['discountapplicationmethodlist']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_7_total = $__section_index_7_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_7_total !== 0) {
for ($__section_index_7_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_7_iteration <= $__section_index_7_total; $__section_index_7_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_7_iteration === $__section_index_7_total);
?>
									<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
										["<?php echo $_smarty_tpl->tpl_vars['discountapplicationmethodlist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['discountapplicationmethodlist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"]
									<?php } else { ?>
										["<?php echo $_smarty_tpl->tpl_vars['discountapplicationmethodlist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['discountapplicationmethodlist']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"],
									<?php }?>
								<?php
}
}
?>
								
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
						value: "<?php echo $_smarty_tpl->tpl_vars['discountapplicationmethodcode']->value;?>
",
						post: true
                    },
					{
						xtype: 'numberfield',
						id: 'discountapplytoqty',
						name: 'discountapplytoqty',
						fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDiscountQty');?>
",
						value: "<?php echo $_smarty_tpl->tpl_vars['discountapplytoqty']->value;?>
",
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
		
		<?php if ($_smarty_tpl->tpl_vars['selectedproductsradio']->value == 'PRODUCTGROUP') {?>
			Ext.getCmp('productGroupRadio').setValue(true);
			Ext.getCmp('productGroupList').setValue(<?php echo $_smarty_tpl->tpl_vars['selectedproductgroup']->value;?>
);
		<?php } elseif ($_smarty_tpl->tpl_vars['selectedproductsradio']->value == 'PRODUCT') {?>
			Ext.getCmp('specificProductRadio').setValue(true);
			Ext.getCmp('productsList').setValue("<?php echo $_smarty_tpl->tpl_vars['selectedProduct']->value;?>
");
		<?php } else { ?>
			Ext.getCmp('allProducts').setValue(true);
		<?php }?>
		
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
		baseParams:	{ ref: '<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
' }
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
		title: "<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
",
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
				handler: function(btn, ev)
				{
					showResultWindow = false;
					gDialogObjVouchers.close();
				},
				cls: 'x-btn-right'
			},
			{
				text: "<?php echo $_smarty_tpl->tpl_vars['actionbutton']->value;?>
",
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

	Ext.getCmp('isactive').setValue("<?php echo $_smarty_tpl->tpl_vars['activechecked']->value;?>
" == 'checked' ? true : false);

	Ext.getCmp('userlist').store.load({params: { id: <?php echo $_smarty_tpl->tpl_vars['userid']->value;?>
}});

};

<?php }
}
