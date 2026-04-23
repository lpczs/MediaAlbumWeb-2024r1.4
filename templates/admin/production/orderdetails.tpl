{literal}

function initialize(pParams)
{	
	var currentcanuploadfiles = '{/literal}{$canuploadfiles}{literal}';
	var currentcanuploadenablesaveoverride = '{/literal}{$canuploadenablesaveoverride}{literal}';
	var currentcanmodify = '{/literal}{$canmodify}{literal}';
	var currentcanuploadproductcodeoverride = '{/literal}{$canuploadproductcodeoverride}{literal}';
	var currentcanuploadpagecountoverride = '{/literal}{$canuploadpagecountoverride}{literal}';
	var itemactivestatus = '{/literal}{$itemactivestatus}{literal}';

	function onCopyEmail(inputId, btnId)
	{
		var emailInput = document.getElementById(inputId);		
		var copyBtn = Ext.getCmp(btnId);
		var copyBtnTxt = copyBtn.getText(); 

		emailInput.select();
		emailInput.setSelectionRange(0, 99999); /* For mobile devices */

		navigator.clipboard.writeText(emailInput.value);

		// Remove the selection.
		var selection = window.getSelection();
		selection.removeAllRanges();

		copyBtn.setText('&check; {/literal}{#str_ButtonCopied#}{literal}');
		copyBtn.disable();
		
		setTimeout(function()
		{
			copyBtn.setText(copyBtnTxt);
			copyBtn.enable();
		}, 2000);
	}

	function getOrderItemCount()
	{
		var gridObj = Ext.getCmp('productionmaingrid');
		var selRecords = gridObj.selModel.getSelections();
		var orderId = selRecords[0].data.orderid;
		var allGridRecords = gridObj.store.reader.arrayData;
		var orderItemCount = 0;

		for (i=1; i < allGridRecords.length; i++) 
		{
			var thisRecordOrderID = allGridRecords[i][24];

			if (thisRecordOrderID == orderId) 
			{
				orderItemCount++;
			}
			
		}

		return orderItemCount;
	}
	
	var cancelFunction = function(btn,ev)
	{ 
		orderDetailsWindowExists = false; 
		gDialogObj.close();
	};

	var onCallback = function()
	{
		
	};

	/* page count check override button handler */
	function pageCountOverride(btn, ev)
	{		 
		/* server parameters are sent to the server */
		var params = new Object();
		var fp = Ext.getCmp('orderDetailsForm');
		var form = fp.getForm();
		var pageCountOverride = 0;
		var buttonText = '{/literal}{#str_LabelIgnorePageCount#}{literal}';
		var value = '{/literal}{#str_LabelYes#}{literal}';

		if (currentcanuploadpagecountoverride == 0)
		{
			pageCountOverride = 1;
			buttonText = '{/literal}{#str_LabelCheckPageCount#}{literal}';
			value = '{/literal}{#str_LabelNo#}{literal}';
		}

		currentcanuploadpagecountoverride = pageCountOverride;

		params['idlist'] = '{/literal}{$orderlineid}{literal}';
		params['ref'] = '{/literal}{$ref}{literal}';
		params['overridepagecount'] = pageCountOverride;
		Ext.taopix.formPanelPost(fp, form, params, 'index.php?fsaction=AdminProduction.updateItemCanUploadFilesOverridePageCountStatus', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
		btn.setText(buttonText);
		Ext.getCmp('canuploadpagecountoverride').setValue(value);
	}	
	
	/* product code check override button handler */
	function productCodeOverride(btn, ev)
	{		 
		/* server parameters are sent to the server */
		var params = new Object();
		var fp = Ext.getCmp('orderDetailsForm');
		var form = fp.getForm();
		var productCodeOverride = 0;
		var buttonText = '{/literal}{#str_LabelIgnoreProductCode#}{literal}';
		var value = '{/literal}{#str_LabelYes#}{literal}';

		if (currentcanuploadproductcodeoverride == 0)
		{
			productCodeOverride = 1;
			buttonText = '{/literal}{#str_LabelCheckProductCode#}{literal}';
			value = '{/literal}{#str_LabelNo#}{literal}';
		}

		currentcanuploadproductcodeoverride = productCodeOverride;

		params['idlist'] = '{/literal}{$orderlineid}{literal}';
		params['ref'] = '{/literal}{$ref}{literal}';
		params['overrideproductcode'] = productCodeOverride;
		Ext.taopix.formPanelPost(fp, form, params, 'index.php?fsaction=AdminProduction.updateItemCanUploadFilesOverrideProductCodeStatus', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
		btn.setText(buttonText);
		Ext.getCmp('canuploadproductcodeoverride').setValue(value);
	}

	/* can modify button handler */
	function canModifyHandler(btn, ev)
	{		 
		/* server parameters are sent to the server */
		var params = new Object();
		var fp = Ext.getCmp('orderDetailsForm');
		var form = fp.getForm();
		var canModify = 0;
		var buttonText = '{/literal}{#str_LabelEnable#}{literal}';
		var value = '{/literal}{#str_LabelNo#}{literal}';

		if (currentcanmodify == 0)
		{
			canModify = 1;
			buttonText = '{/literal}{#str_LabelDisable#}{literal}';
			value = '{/literal}{#str_LabelYes#}{literal}';
		}

		currentcanmodify = canModify;

		params['idlist'] = '{/literal}{$orderlineid}{literal}';
		params['ref'] = '{/literal}{$ref}{literal}';
		params['canmodify'] = canModify;
		Ext.taopix.formPanelPost(fp, form, params, 'index.php?fsaction=AdminProduction.updateItemCanModifyStatus', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
		btn.setText(buttonText);
		Ext.getCmp('canmodify').setValue(value);
	}

	/* save override button handler */
	function saveOverrideHandler(btn, ev)
	{		 
		/* server parameters are sent to the server */
		var params = new Object();
		var fp = Ext.getCmp('orderDetailsForm');
		var form = fp.getForm();
		var saveOverride = 0;
		var buttonText = '{/literal}{#str_LabelEnable#}{literal}';
		var value = '{/literal}{#str_LabelNo#}{literal}';

		if (currentcanuploadenablesaveoverride == 0)
		{
			saveOverride = 1;
			buttonText = '{/literal}{#str_LabelDisable#}{literal}';
			value = '{/literal}{#str_LabelYes#}{literal}';
		}

		currentcanuploadenablesaveoverride = saveOverride;

		params['idlist'] = '{/literal}{$orderlineid}{literal}';
		params['ref'] = '{/literal}{$ref}{literal}';
		params['canuploadenablesaveoverride'] = saveOverride;
		Ext.taopix.formPanelPost(fp, form, params, 'index.php?fsaction=AdminProduction.updateOverrideSaveStatus', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
		btn.setText(buttonText);
		Ext.getCmp('canuploadenablesaveoverride').setValue(value);
	}	

	/* canuploadfiles button handler */
	function canuploadfilesHandler(btn, ev)
	{		 
		/* server parameters are sent to the server */
		var params = new Object();
		var fp = Ext.getCmp('orderDetailsForm');
		var form = fp.getForm();
		var canUploadFiles = 0;
		var buttonText = '{/literal}{#str_LabelEnable#}{literal}';
		var value = '{/literal}{#str_LabelNo#}{literal}';

		if (currentcanuploadfiles == 0)
		{
			canUploadFiles = 1;
			buttonText = '{/literal}{#str_LabelDisable#}{literal}';
			value = '{/literal}{#str_LabelYes#}{literal}';
		}

		currentcanuploadfiles = canUploadFiles;

		params['idlist'] = '{/literal}{$orderlineid}{literal}';
		params['ref'] = '{/literal}{$ref}{literal}';
		params['canuploadfiles'] = canUploadFiles;
		Ext.taopix.formPanelPost(fp, form, params, 'index.php?fsaction=AdminProduction.updateItemCanUploadFilesStatus', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
		btn.setText(buttonText);
		Ext.getCmp('canuploadfiles').setValue(value);
	}	

	var otherInfoGrid = new Ext.grid.GridPanel({
		id: 'otherinfogrid',
		stripeRows: true,
		columnLines:false,
		hideHeaders: true,
		width: 600,
		height: 320,
		bodyStyle: 'margin-top: 10px; border: 1px solid #b4b8c8;',
		ctCls: 'grid',
		store: new Ext.data.JsonStore({
			fields:
			[
				{name: 'label', mapping: 'label'},
				{name: 'data', mapping: 'data'}
			],
			data:
			{
				records :
				[
					{/literal}
					{section name=index loop=$otherinfolist}
						{if $smarty.section.index.last}
							{literal}{label: "{/literal}{$otherinfolist[index].label}{literal}:", data: "{/literal}{$otherinfolist[index].data}{literal}"}{/literal}
						{else}
							{literal}{label: "{/literal}{$otherinfolist[index].label}{literal}:", data: "{/literal}{$otherinfolist[index].data}{literal}"}{/literal},
						{/if}
					{/section}
					{literal}
				]
			},
			root: 'records'
		}),
		columns:
		[
			{
				id:'label',
				sortable: false,
				menuDisabled: true,
				dataIndex: 'label',
				width: 200
			},
			{
				id: 'data',
				width: 375,
				sortable: false,
				dataIndex: 'data',
				menuDisabled: true
			}
		]
	});

	var tabPanel = {
	xtype: 'tabpanel',
	id: 'maintabpanel',
	deferredRender: false,
	activeTab: 0,
	height: 575,
	width: 627,
	shadow: true,
	plain:true,
	bodyBorder: true,
	border: true,
	style:'margin-top:6px; ',
	bodyStyle:'border-right: 1px solid #96bde7; border-left: 1px solid #96bde7; ',
	defaults:{frame: false, autoScroll: true, hideMode:'offsets', layout: 'form', labelWidth: 175, bodyStyle:'padding:5px 10px 0 10px; border-top: 0px; background-color: #eaf0f8;'},
	items: [
				{
					title: "{/literal}{#str_TitleOrderInformation#}{literal}",
					defaults:{style: 'color: #000; opacity: 1'},
					items: [
						{
							xtype: 'textfield',
							width: 300,
							id: 'orderdate',
							name: 'orderdate',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$orderdate}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionOrderDate#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'ordernumber',
							name: 'ordernumber',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$ordernumber}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionOrderNumber#}{literal}'
						},
						{
							xtype: 'container', layout: 'column', width: 425,
							id: 'container_itemnumber',
							defaults:{style: 'padding-left:3px; color: #000; opacity: 1'},
							fieldLabel: '{/literal}{#str_LabelProductionOrderLine#}{literal}',
							items: [
									{
										xtype: 'textfield',
										width: 147.5,
										id: 'itemnumber',
										name: 'itemnumber',
										readOnly: true,
										disabled: true,
										value: "{/literal}{$itemnumber}{literal}"
									},
									{
										id: 'itemspacer',
										name: 'itemspacer',
										html: " ... ",
										cls: "",
										style: "margin-top:6px;color:#a0a0a0"
									},
									{
										xtype: 'textfield',
										width: 147.5,
										id: 'itemtotal',
										name: 'itemtotal',
										readOnly: true,
										disabled: true,
										value: getOrderItemCount()
									}
								]
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'orderlineid',
							name: 'orderlineid',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$orderlineid}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionOrderLineID#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'originalorder',
							name: 'originalorder',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$originalordernumber}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionOriginalOrderNumber#|escape}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'groupcode',
							name: 'groupcode',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$groupcode}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionLicenseKeyCode#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'groupdata',
							name: 'groupdata',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$groupdata}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionLicenseKeyData#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'brand',
							name: 'brand',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$brand}{literal}',
							fieldLabel: '{/literal}{#str_LabelBrand#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'vouchercode',
							name: 'vouchercode',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$vouchercode}{literal}',
							fieldLabel: '{/literal}{#str_LabelVoucherCode#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'projectname',
							name: 'projectname',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$projectname}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionProjectName#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'productcode',
							name: 'productcode',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$productcode}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionProductCode#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'productskucode',
							name: 'productskucode',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$productskucode}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionProductSKUCode#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'productname',
							name: 'productname',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$productname}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionProductName#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'productdimensions',
							name: 'productdimensions',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$productdimensions}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionProductDimensions#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'pagecountpurchased',
							name: 'pagecountpurchased',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$pagecountpurchased}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionPageCountPurchased#|escape}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'pagecount',
							name: 'pagecount',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$pagecount}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionPageCount#|escape}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'qty',
							name: 'qty',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$qty}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionQty#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'ordertotal',
							name: 'ordertotal',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$ordertotal}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionOrderTotal#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'ordergiftcardtotal',
							name: 'ordergiftcardtotal',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$ordergiftcardtotal}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionGiftCardAmount#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'ordertotaltopay',
							name: 'ordertotaltopay',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$ordertotaltopay}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionAmountToPay#}{literal}'
						}
					]
				},
				{
					title: "{/literal}{#str_TitleShippingInfo#}{literal}",
					defaults:{style: 'color: #000; opacity: 1;'},
					items: [
						{
							xtype: 'textfield',
							width: 300,
							id: 'shippingcustomername',
							name: 'shippingcustomername',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$shippingcustomername}{literal}',
							fieldLabel: '{/literal}{#str_LabelCompanyName#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'shippingcustomeraddress1',
							name: 'shippingcustomeraddress1',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$shippingcustomeraddress1}{literal}',
							fieldLabel: '{/literal}{#str_LabelAddress#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'shippingcustomeraddress2',
							name: 'shippingcustomeraddress2',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$shippingcustomeraddress2}{literal}',
							fieldLabel: ''
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'shippingcustomeraddress3',
							name: 'shippingcustomeraddress3',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$shippingcustomeraddress3}{literal}',
							fieldLabel: ''
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'shippingcustomeraddress4',
							name: 'shippingcustomeraddress4',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$shippingcustomeraddress4}{literal}',
							fieldLabel: ''
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'shippingcustomercity',
							name: 'shippingcustomercity',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$shippingcustomercity}{literal}',
							fieldLabel: '{/literal}{#str_LabelTownCity#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'shippingcustomercounty',
							name: 'shippingcustomercounty',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$shippingcustomercounty}{literal}',
							fieldLabel: '{/literal}{#str_LabelCounty#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'shippingcustomerstate',
							name: 'shippingcustomerstate',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$shippingcustomerstate}{literal}',
							fieldLabel: '{/literal}{#str_LabelState#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'shippingcustomerpostcode',
							name: 'shippingcustomerpostcode',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$shippingcustomerpostcode}{literal}',
							fieldLabel: '{/literal}{#str_LabelPostCode#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'shippingcustomercountryname',
							name: 'shippingcustomercountryname',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$shippingcustomercountryname}{literal}',
							fieldLabel: '{/literal}{#str_LabelCountry#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'shippingcustomertelephonenumber',
							name: 'shippingcustomertelephonenumber',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$shippingcustomertelephonenumber}{literal}',
							fieldLabel: '{/literal}{#str_LabelTelephoneNumber#}{literal}'
						},
						{
							xtype: 'container', layout: 'column', width: 405,
							defaults:{style: 'color: #000; opacity: 1;'},
							fieldLabel: '{/literal}{#str_LabelEmailAddress#}{literal}',
							items: [
								{
									xtype: 'textfield',
									width: 227,
									style: 'color: #000; opacity: 1; padding: 1px 3px;',
									id: 'shippingcustomeremailaddress',
									name: 'shippingcustomeremailaddress',
									readOnly: true,
									disabled: true,
									value: '{/literal}{$shippingcustomeremailaddress}{literal}',
								},
								new Ext.Button({
									id: 'shippingcopyemailbtn',
									text: "{/literal}{#str_ButtonCopy#}{literal}",
									minWidth: 75,
									disabled: false,
									handler: function() { onCopyEmail('shippingcustomeremailaddress', 'shippingcopyemailbtn') }
								}),
							]
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'shippingcontactfirstname',
							name: 'shippingcontactfirstname',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$shippingcontactfirstname}{literal}',
							fieldLabel: '{/literal}{#str_LabelFirstName#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'shippingcontactlastname',
							name: 'shippingcontactlastname',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$shippingcontactlastname}{literal}',
							fieldLabel: '{/literal}{#str_LabelLastName#}{literal}'
						},
						{
							xtype: 'spacer',
							style:'padding-bottom:10px;border-bottom:1px solid #ccc; margin-bottom:7px;'
					  	},
						{
							xtype: 'textfield',
							width: 300,
							id: 'shippingmethod',
							name: 'shippingmethod',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$shippingmethod}{literal}',
							fieldLabel: '{/literal}{#str_LabelShippingMethod#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'shippingstatus',
							name: 'shippingstatus',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$shippingstatus}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionShipped#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'shippingtrackingreference',
							name: 'shippingtrackingreference',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$shippingtrackingreference}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionTrackingRef#}{literal}'
						},
					]
				},
				{
					title: "{/literal}{#str_TitleBillingInfo#}{literal}",
					defaults:{style: 'color: #000; opacity: 1;'},
					items: [
						{
							xtype: 'textfield',
							width: 300,
							id: 'billingcustomeraccountcode',
							name: 'billingcustomeraccountcode',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$billingcustomeraccountcode}{literal}',
							fieldLabel: '{/literal}{#str_LabelAccountCode#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'billingcustomername',
							name: 'billingcustomername',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$billingcustomername}{literal}',
							fieldLabel: '{/literal}{#str_LabelCompanyName#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'billingcustomeraddress1',
							name: 'billingcustomeraddress1',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$billingcustomeraddress1}{literal}',
							fieldLabel: '{/literal}{#str_LabelAddress#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'billingcustomeraddress2',
							name: 'billingcustomeraddress2',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$billingcustomeraddress2}{literal}',
							fieldLabel: ''
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'billingcustomeraddress3',
							name: 'billingcustomeraddress3',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$billingcustomeraddress3}{literal}',
							fieldLabel: ''
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'billingcustomeraddress4',
							name: 'billingcustomeraddress4',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$billingcustomeraddress4}{literal}',
							fieldLabel: ''
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'billingcustomercity',
							name: 'billingcustomercity',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$billingcustomercity}{literal}',
							fieldLabel: '{/literal}{#str_LabelTownCity#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'billingcustomercounty',
							name: 'billingcustomercounty',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$billingcustomercounty}{literal}',
							fieldLabel: '{/literal}{#str_LabelCounty#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'billingcustomerstate',
							name: 'billingcustomerstate',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$billingcustomerstate}{literal}',
							fieldLabel: '{/literal}{#str_LabelState#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'billingcustomerpostcode',
							name: 'billingcustomerpostcode',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$billingcustomerpostcode}{literal}',
							fieldLabel: '{/literal}{#str_LabelPostCode#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'billingcustomercountryname',
							name: 'billingcustomercountryname',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$billingcustomercountryname}{literal}',
							fieldLabel: '{/literal}{#str_LabelCountry#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'billingcustomertelephonenumber',
							name: 'billingcustomertelephonenumber',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$billingcustomertelephonenumber}{literal}',
							fieldLabel: '{/literal}{#str_LabelTelephoneNumber#}{literal}'
						},
						{
							xtype: 'container', layout: 'column', width: 405,
							defaults:{style: 'color: #000; opacity: 1;'},
							fieldLabel: '{/literal}{#str_LabelEmailAddress#}{literal}',
							items: [
								{
									xtype: 'textfield',
									width: 227,
									style: 'color: #000; opacity: 1; padding: 1px 3px;',
									id: 'billingcustomeremailaddress',
									name: 'billingcustomeremailaddress',
									readOnly: true,
									disabled: true,
									value: '{/literal}{$billingcustomeremailaddress}{literal}',
								},
								new Ext.Button({
									id: 'billingcopyemailbtn',
									text: "{/literal}{#str_ButtonCopy#}{literal}",
									minWidth: 75,
									disabled: false,
									handler: function() { onCopyEmail('billingcustomeremailaddress', 'billingcopyemailbtn') }
								}),
							]
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'billingcontactfirstname',
							name: 'billingcontactfirstname',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$billingcontactfirstname}{literal}',
							fieldLabel: '{/literal}{#str_LabelFirstName#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'billingcontactlastname',
							name: 'billingcontactlastname',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$billingcontactlastname}{literal}',
							fieldLabel: '{/literal}{#str_LabelLastName#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'billingcustomerregisteredtaxnumber',
							name: 'billingcustomerregisteredtaxnumber',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$billingcustomerregisteredtaxnumber}{literal}',
							fieldLabel: '{/literal}{#str_TaxNumber#}{literal}'
						},
						{
							xtype: 'spacer',
							height: 5
					  	},
						{
							xtype: 'textfield',
							width: 300,
							id: 'billingcustomerregisteredtaxnumber',
							name: 'billingcustomerregisteredtaxnumber',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$billingcustomerregisteredtaxnumber}{literal}',
							fieldLabel: '{/literal}{#str_TaxNumber#}{literal}'
						},
						{
							xtype: 'spacer',
							style:'padding-bottom:10px;border-bottom:1px solid #ccc; margin-bottom:7px;'
					  	},
						{
							xtype: 'textfield',
							width: 300,
							id: 'paymentmethodname',
							name: 'paymentmethodname',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$paymentmethodname}{literal}',
							fieldLabel: '{/literal}{#str_LabelPaymentMethod#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'ccitype',
							name: 'ccitype',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$ccitype}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionPaymentProcessor#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'ccitransactionid',
							name: 'ccitransactionid',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$ccitransactionid}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionTransactionID#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'cciresponsecode',
							name: 'cciresponsecode',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$cciresponsecode}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionPaymentStatusCode#|escape}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'cciresponsedescription',
							name: 'cciresponsedescription',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$cciresponsedescription}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionPaymentStatusDescription#|escape}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'paymentreceiveddate',
							name: 'paymentreceiveddate',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$paymentreceiveddate}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionPaymentConfirmed#}{literal}'
						}
					]
				},
				{
					title: "{/literal}{#str_TitleOtherInfo#}{literal}",
					defaults:{style: 'color: #000; opacity: 1;'},
					items: [
						{
							xtype: 'textfield',
							width: 300,
							id: 'source',
							name: 'source',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$sourcetext}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionSource#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'uploaddatatype',
							name: 'uploaddatatype',
							readOnly: true,
							disabled: true,
							value: '{/literal}{$uploaddatatype}{literal}',
							fieldLabel: '{/literal}{#str_LabelProductionDataFormat#}{literal}'
						},
						{
							xtype: 'textfield',
							width: 300,
							id: 'uploadmethod',
							name: 'uploadmethod',
							readOnly: true,
							disabled: true,
							{/literal}{if $source == 1}{literal}
								value: "{/literal}{#str_NotApplicable#}{literal}",
							{/literal}{else}{literal}
								value: '{/literal}{$uploadmethod}{literal}',
							{/literal}{/if}{literal}
							fieldLabel: '{/literal}{#str_LabelProductionUploadMethod#}{literal}'
						},
						{
							xtype: 'container', layout: 'column', width: 425,
							id: 'container_canupload',
							fieldLabel: '{/literal}{#str_LabelProductionCanUploadFiles#|escape}{literal}',
							defaults:{style: 'color: #000; opacity: 1;'},
							items: [
									{
										xtype: 'textfield',
										width: 125,
										id: 'canuploadfiles',
										name: 'canuploadfiles',
										readOnly: true,
										disabled: true,
										{/literal}{if $source == 1}{literal}
											value: "{/literal}{#str_NotApplicable#}{literal}"
										{/literal}{else}{literal}
											{/literal}{if $canuploadfiles == 1}{literal}
												value: "{/literal}{#str_LabelYes#}{literal}"
											{/literal}{else}{literal}
												value: "{/literal}{#str_LabelNo#}{literal}"
											{/literal}{/if}{literal}
										{/literal}{/if}{literal}
									},
									{
										id: 'itemspacer',
										html: "&nbsp",
										cls: ""
									},
									new Ext.Button({
										id: 'canuploadbutton',
										name: 'canuploadbutton',
										{/literal}{if $canuploadfiles == 0}{literal}
											text: "{/literal}{#str_LabelEnable#}{literal}",
										{/literal}{else}{literal}
											text: "{/literal}{#str_LabelDisable#}{literal}",
										{/literal}{/if}{literal}
										minWidth: 75,
										{/literal}{if $source == 1 || $itemactivestatus > 0}{literal}
											text: "{/literal}{#str_LabelDisable#}{literal}",
											disabled: true,
										{/literal}{/if}{literal}
										handler: canuploadfilesHandler
									}),
								]
							},
						{
							xtype: 'container', layout: 'column', width: 425,
							id: 'container_saveoverride',
							fieldLabel: '{/literal}{#str_LabelProductionSaveOverride#}{literal}',
							defaults:{style: 'color: #000; opacity: 1;'},
							items: [
									{
										xtype: 'textfield',
										width: 125,
										id: 'canuploadenablesaveoverride',
										name: 'canuploadenablesaveoverride',
										readOnly: true,
										disabled: true,
										{/literal}{if $source == 1}{literal}
											value: "{/literal}{#str_NotApplicable#}{literal}"
										{/literal}{else}{literal}
											{/literal}{if $canuploadenablesaveoverride == 1}{literal}
												value: "{/literal}{#str_LabelYes#}{literal}"
											{/literal}{else}{literal}
												value: "{/literal}{#str_LabelNo#}{literal}"
											{/literal}{/if}{literal}
										{/literal}{/if}{literal}
									},
									{
										id: 'itemspacer',
										html: "&nbsp",
										cls: ""
									},
									new Ext.Button({
										id: 'canuploadenablesaveoverridebutton',
										name: 'canuploadenablesaveoverridebutton',
										{/literal}{if $canuploadenablesaveoverride == 0}{literal}
											text: "{/literal}{#str_LabelEnable#}{literal}",
										{/literal}{else}{literal}
											text: "{/literal}{#str_LabelDisable#}{literal}",
										{/literal}{/if}{literal}
										minWidth: 75,
										{/literal}{if $source == 1 || $itemactivestatus > 0}{literal}
											disabled: true,
											text: "{/literal}{#str_LabelDisable#}{literal}",
										{/literal}{/if}{literal}
										handler: saveOverrideHandler
									}),
								]
						},
						{
							xtype: 'container', layout: 'column', width: 425,
							id: 'container_canmodify',
							fieldLabel: '{/literal}{#str_LabelProductionCanModifyProject#|escape}{literal}',
							defaults:{style: 'color: #000; opacity: 1;'},
							items: [
									{
										xtype: 'textfield',
										width: 125,
										id: 'canmodify',
										name: 'canmodify',
										readOnly: true,
										disabled: true,
										{/literal}{if $canmodify == 1}{literal}
											value: "{/literal}{#str_LabelYes#}{literal}"
										{/literal}{else}{literal}
											value: "{/literal}{#str_LabelNo#}{literal}"
										{/literal}{/if}{literal}
									},
									{
										id: 'itemspacer',
										html: "&nbsp",
										cls: ""
									},
									new Ext.Button({
										id: 'redactionmodebutton',
										name: 'redactionmodebutton',
										{/literal}{if $canmodify == 0}{literal}
											text: "{/literal}{#str_LabelEnable#}{literal}",
										{/literal}{else}{literal}
										text: "{/literal}{#str_LabelDisable#}{literal}",
										{/literal}{/if}{literal}
										{/literal}{if $itemactivestatus > 0}{literal}
											disabled: true,
										{/literal}{/if}{literal}
										minWidth: 75,
										handler: canModifyHandler
									}),
								]
						},
						{
							xtype: 'container', layout: 'column', width: 425,
							id: 'container_checkproductcode',
							fieldLabel: '{/literal}{#str_LabelCheckProductCode#}{literal}',
							defaults:{style: 'color: #000; opacity: 1;'},
							items: [
									{
										xtype: 'textfield',
										width: 125,
										id: 'canuploadproductcodeoverride',
										name: 'canuploadproductcodeoverride',
										readOnly: true,
										disabled: true,
										{/literal}{if $canuploadproductcodeoverride == 0}{literal}
											value: "{/literal}{#str_LabelYes#}{literal}",
										{/literal}{else}{literal}
											value: "{/literal}{#str_LabelNo#}{literal}",
										{/literal}{/if}{literal}
									},
									{
										id: 'itemspacer',
										html: "&nbsp",
										cls: ""
									},
									new Ext.Button({
										id: 'checkproductcodebutton',
										name: 'checkproductcodebutton',
										{/literal}{if $canuploadproductcodeoverride == 0}{literal}
											text: "{/literal}{#str_LabelIgnoreProductCode#}{literal}",
										{/literal}{else}{literal}
										text: "{/literal}{#str_LabelCheckProductCode#}{literal}",
										{/literal}{/if}{literal}
										{/literal}{if $itemactivestatus > 0}{literal}
											disabled: true,
										{/literal}{/if}{literal}
										minWidth: 125,
										handler: productCodeOverride
									}),
								]
						},
						{
							xtype: 'container', layout: 'column', width: 425,
							id: 'container_checkpagecount',
							fieldLabel: '{/literal}{#str_LabelCheckPageCount#}{literal}',
							defaults:{style: 'color: #000; opacity: 1;'},
							items: [
									{
										xtype: 'textfield',
										width: 125,
										id: 'canuploadpagecountoverride',
										name: 'canuploadpagecountoverride',
										readOnly: true,
										disabled: true,
										{/literal}{if $canuploadpagecountoverride == 0}{literal}
											value: "{/literal}{#str_LabelYes#}{literal}",
										{/literal}{else}{literal}
											value: "{/literal}{#str_LabelNo#}{literal}",
										{/literal}{/if}{literal}
									},
									{
										id: 'itemspacer',
										html: "&nbsp",
										cls: ""
									},
									new Ext.Button({
										id: 'checkpagecountbutton',
										name: 'checkpagecountbutton',
										{/literal}{if $canuploadpagecountoverride == 0}{literal}
											text: "{/literal}{#str_LabelIgnorePageCount#}{literal}",
										{/literal}{else}{literal}
										text: "{/literal}{#str_LabelCheckPageCount#}{literal}",
										{/literal}{/if}{literal}
										{/literal}{if $itemactivestatus > 0}{literal}
											disabled: true,
										{/literal}{/if}{literal}
										minWidth: 125,
										handler: pageCountOverride
									}),
								]
						},
						otherInfoGrid
					]
				}
			]
	};	

	var topPanel = new Ext.Panel({
	id: 'topPanel',
	layout: 'form',
	style:'background: #c9d8ed; padding: 3px 0; border:1px solid #8ca9cf; width:670px;',
	plain:true,
	bodyBorder: false,
	border: false,
	width: 627,
	defaults: {xtype: 'textfield', labelWidth: 120, width: 470},
	labelWidth: 125,
	bodyStyle:'padding:5px 5px 0; border-top: 0px',
	items: [ 
				{
					id: 'status',
					name: 'status',
					fieldLabel: "{/literal}{#str_LabelStatus#}{literal}",
					value: "{/literal}{$statustext}{literal}",
					post: true,
					allowBlank: true,
					readOnly: true,
					style: 'background:#c9d8ed;'
				}
			]
	});

	var orderDetailsFormTabPanelObj = new Ext.FormPanel({
        id: 'orderDetailsForm',
		header: false,
        frame: true,
        layout: 'form',
		autoWidth: true,
		bodyBorder: false,
		border: true,
        items: [ topPanel, tabPanel ]
	});

	/* create modal window for add and edit */
	gDialogObj = new Ext.Window({
		id: 'dialog',
		closable:false,
		title: "{/literal}{#str_SectionTitleOrderDetails#}{literal}",
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		height: 700,
		width: 655,
		items: orderDetailsFormTabPanelObj,
		buttons:
		[
			{ text: "{/literal}{#str_ButtonClose#}{literal}", handler: cancelFunction, cls: 'x-btn-right' }
		]
	});

	var mainPanel = Ext.getCmp('dialog');
	mainPanel.show();	
}

{/literal}