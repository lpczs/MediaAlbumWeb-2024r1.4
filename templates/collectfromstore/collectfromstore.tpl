	var collectFromStoreTitle = "{#str_TitleCollectFromStore#}";
	var editLabel_txt = "{#str_ButtonEdit#}";
	var showDetailsLabel_txt = "{#str_ButtonDetails#}";
	var markCollectedLabel_txt = "{#str_MarkCollected#}"; 
	var markCollectedNotLabel_txt = "{#str_MarkNotCollected#}"; 
	var str_ExtJsPagingToolbarRefresh = "{#str_ExtJsPagingToolbarRefresh#}"; 
	var buttonCancel_tx = "{#str_ButtonCancel#}";
	var buttonSave_tx = "{#str_ButtonUpdate#}";
	var statusFilterLabel_tx = "Status";
	var noLabel_tx = "{#str_ButtonNo#}";
	var yesLabel_tx = "{#str_ButtonYes#}";
	var orderDetailsTitle = "{#str_OrderDetails#}"; 
	var buttonOk_tx = "{#str_ButtonOk#}";
	var orderInfoLabel_txt = "{#str_OrderInformation#}"; 
	var billingInfoLabel_txt = "{#str_BillingInformation#}";
	var orderDateLabel_tx = "{#str_LabelOrderDate#}";  
	var orderNumberLabel_tx = "{#str_LabelOrderNumber#}";
	var productNameLabel_tx = "{#str_LabelProductName#}";
	var qtyLabel_tx = "{#str_Qty#}";
	var orderTotalLabel_tx = "{#str_LabelOrderTotal#}";
	var companyNameLabel_tx = "{#str_LabelCompanyName#}";
	var addressLabel_tx = "{#str_Address#}";
	var cityLabel_tx = "{#str_LabelTownCity#}";
	var countryLabel_tx = "{#str_LabelCountry#}";
	var telephoneLabel_tx = "{#str_LabelTelephoneNumber#}";
	var emailLabel_tx = "{#str_LabelEmailAddress#}";
	var firstnameLabel_tx = "{#str_LabelFirstName#}";
	var lastnameLabel_tx = "{#str_LabelLastName#}";
	var paymentConfirmedLabel_tx = "{#str_PaymentConfirmed#}";
	var paymentDateLabel_tx = "{#str_PaymentDate#}";
	var countyLabel_tx = "{#str_LabelCounty#}";
	var stateLabel_tx = "{#str_LabelState#}";
	var postcodeLabel_tx = "{#str_LabelPostCode#}";
	var dateConfirmedLabel_tx = "{#str_Date#}";
	var bookedConfirmation_txt = "{#str_ReceivedConfirmation#}";
	var collectedConfirmation_txt = "{#str_CollectedConfirmation#}";
	var bookedNotConfirmation_txt = "{#str_NotReceivedConfirmation#}";
	var collectedNotConfirmation_txt = "{#str_NotCollectedConfirmation#}";
	var notReceivedLabel_tx = "{#str_NotReceived#}";
	var receivedLabel_tx = "{#str_Received#}";
	var collectedByCustomerLabel_tx = "{#str_Collected#}"; 
	var shippedToStoreLabel_tx = "{#str_ShippedToStore#}";
	var contactInfoLabel_tx = "{#str_LabelContactInformation#}";
	var updatingLabel_tx = "{#str_MessageUpdating#}";
	var confirmPaymentLabel_tx = "{#str_ConfirmPayment#}";
	var paymentConfirmationLabel_tx = "{#str_CollectedNotPaidConfirmation#}";
	var collectedLabel_tx = "{#str_Collected#}";
	var collectedNotLabel_tx = "{#str_NotCollected#}";
	
	var paymentInfo_tx = "{#str_PaymentInfo#}";
	var quantityLabel_tx = "{#str_LabelQuantity#}";
	var totalLabel_tx = "{#str_LabelOrderTotal#}";
	var paidLabel_tx = "{#str_Paid#}";
	var markPaidLabel_tx = "{#str_MarkAsPaid#}";
	var markNotPaid_tx = "{#str_MarkAsNotPaid#}";
	var markRecievedLabel_tx = "{#str_MarkReceived#}";
	var markRecievedNotLabel_tx = "{#str_MarkNotReceived#}";
	var shippedConfirmationLabel_tx = "{#str_ShippedConfirmation#}";
	var shippedNotConfirmationLabel_tx = "{#str_NotShippedConfirmation#}";
	var markShippedLabel_tx = "{#str_MarkAsShipped#}";
	var markNotShippedLabel_tx = "{#str_MarkAsNotShipped#}";
	var shippedLabel_tx = "{#str_Shipped#}";
	var storeLabel_tx = "{#str_Store#}";
	var statusLabel_tx = "{#str_LabelStatus#}";
	var distributionCentreLabel_tx = "{#str_DistributionCentre#}";
	var shippingRefLabel_tx = "{#str_ShippingReference#}";
	var confirmationLabel_tx = "{#str_LabelConfirmation#}";
	
	var sessionId   = "{$ref}";
	var userType    = "{$userType}";
	var optionMS    = "{$optionMS}";
	var companyCode = "{$companyCode}";
	var gDateFormat = "{$dateformat}";
	var todayDate   = "{$todayDate}";
	var todayTime   = "{$todayTime}";
	var timeFormat  = "{$timeFormat}";
	var siteType    = "{$siteType}";
	var minTime     = "{$minTime}";
	var maxTime     = "{$maxTime}";
	
	var gridPageSize = 30;
	var yesNoList = [['0', noLabel_tx],['1', yesLabel_tx]];
	var receivedList = [['0', notReceivedLabel_tx],['1', receivedLabel_tx]];
	var collectedList = [['0', collectedNotLabel_tx],['1', collectedLabel_tx]];

{literal}

function initialize(pParams)
{	

/* Fix to TimeField minValue infinite loop issue */
Ext.override(Ext.form.TimeField, {
    /* *** no longer required */
    /* by @mystix -- http://extjs.com/forum/showthread.php?p=337620#post337620 */
    beforeBlur: Ext.form.TimeField.superclass.beforeBlur,

    initDateFormat: 'j/n/Y',
    
    initComponent: function() {
        /* since minValue / maxValue are only used to setup the TimeField's store, it should be /* 
        /* safe to ignore them (i.e. avoid parsing them for validity) if a store has already been defined */

        if (!this.store) {
            /* combine vars to save code */
            var dt = Date.parseDate(this.initDate, this.initDateFormat).clearTime(),
                min = this.minValue = this.parseDate(this.minValue) || dt,
                max = this.maxValue = this.parseDate(this.maxValue) || dt.add('mi', (24 * 60) - 1),
                times = [];

            while (min <= max) {
                times.push(min.dateFormat(this.format));
                min = min.add('mi', this.increment);
            }
            this.store = times;
        }
        Ext.form.TimeField.superclass.initComponent.call(this);
    },
    
    parseDate: function(value) {
        if (!value || Ext.isDate(value)) {
            return value;
        }
        
        var id = this.initDate + ' ',
            idf = this.initDateFormat + ' ',
            v = Date.parseDate(id + value, idf + this.format), /* handle DST */
            af = this.altFormats;
            
        if (!v && af) {
            if (!this.altFormatsArray) {
                this.altFormatsArray = af.split("|");
            }
            for (var i = 0, afa = this.altFormatsArray, len = afa.length; i < len && !v; i++) {
                v = Date.parseDate(id + value, idf + afa[i]);
            }
        }
        
        return v;
    }
});

	var initialStatus = '';
	if (siteType == '1')
	{
		initialStatus = 'S_SHIPPED_NOT_RECEIVED';
	}
	else
	{
		initialStatus = 'DC_SHIPPED_NOT_RECEIVED';
	}
	
	/* different statuses for store, distribution center and production */
	var statusFilterList = [];
	if (siteType == '1')
	{
		statusFilterList = [['S_SHIPPED_NOT_RECEIVED', notReceivedLabel_tx], ['S_RECEIVED_NOT_COLLECTED', receivedLabel_tx], ['S_COLLECTED',collectedByCustomerLabel_tx]];
	}
	else
	{
		statusFilterList = [['DC_SHIPPED_NOT_RECEIVED', notReceivedLabel_tx], ['DC_RECEIVED', receivedLabel_tx], ['DC_SHIPPED_TO_STORE',shippedToStoreLabel_tx]];
	}
	

	/**************** Show details **********************/
	var topPanel = new Ext.Panel({ id: 'topPanel', layout: 'form',style:'background:#c9d8ed; padding: 3px 0; border:1px solid #8ca9cf', plain:true, bodyBorder: false, border: false, 
		defaults:{labelWidth: 50, xtype: 'textfield'}, bodyStyle:'padding:5px 5px 0; border-top: 0px; ', 
		items: [
			{ id: 'orderStatus', name: 'orderStatus',	fieldLabel: statusLabel_tx, readOnly: true, post: true, style:'background:#c9d8ed;',  width: 290 }			
		]
	});	

	var orderInfoTab = { title: orderInfoLabel_txt, id:'orderInfoTab', hideMode:'offsets', items: [
		new Ext.Panel({ layout: 'form',  autoWidth:true, defaults:{xtype: 'textfield', width: 270, labelWidth: 120}, bodyBorder: false, border: false,
	    	items: [    
				{ id: 'detailsOrderDate',   name: 'detailsOrderDate', 	fieldLabel: orderDateLabel_tx, 	 	  readOnly: true, value: '', post: true, width: 240 },
				{ id: 'detailsOrderNumber', name: 'detailsOrderNumber', fieldLabel: orderNumberLabel_tx, 	  readOnly: true, value: '', post: true, width: 240 },
				{ id: 'detailsProductName', name: 'detailsProductName', fieldLabel: productNameLabel_tx, 	  readOnly: true, value: '', post: true, width: 240 },
				{ id: 'detialsQty', 		name: 'detialsQty', 		fieldLabel: qtyLabel_tx, 		 	  readOnly: true, value: '', post: true, width: 240 },
				{ id: 'detailsOrderTotal', 	name: 'detailsOrderTotal', 	fieldLabel: orderTotalLabel_tx,  	  readOnly: true, value: '', post: true, width: 240 },
				{ id: 'paymentStatus', 		name: 'paymentStatus', 		fieldLabel: paymentConfirmedLabel_tx, readOnly: true, value: '', post: true, width: 240 },
				{ id: 'detailsPaymentDate', name: 'detailsPaymentDate', fieldLabel: paymentDateLabel_tx, 	  readOnly: true, value: '', post: true, width: 240 },
				{ id: 'trackingRef', 		name: 'trackingRef', 		fieldLabel: shippingRefLabel_tx,      readOnly: true, value: '', post: true, width: 240 }
	    	]
	    })
	] };

	var billingInfoTab = { title: billingInfoLabel_txt, id:'billingInfoTab', hideMode:'offsets', 
		listeners: { 'beforeshow': function(){ Ext.getCmp('detailsTabPanel').doLayout(); }},
		items: [
	   	new Ext.Panel({ layout: 'form',  autoWidth:true, defaults:{xtype: 'textfield', width: 270, labelWidth: 120}, bodyBorder: false, border: false, 
	      	items: [    
				{ id: 'detailsCompanyName', name: 'detailsCompanyName', fieldLabel: companyNameLabel_tx, readOnly: true, value: '', post: true, width: 240 },
				{ id: 'detailsAddress1', 	name: 'detailsAddress1', 	fieldLabel: addressLabel_tx,     readOnly: true, value: '', post: true, width: 240 },
				{ id: 'detailsAddress2', 	name: 'detailsAddress2', 	fieldLabel: '', 				 readOnly: true, value: '', post: true, width: 240 },
				{ id: 'detailsAddress3', 	name: 'detailsAddress3', 	fieldLabel: '', 				 readOnly: true, value: '', post: true, width: 240 },
				{ id: 'detailsAddress4', 	name: 'detailsAddress4', 	fieldLabel: '', 				 readOnly: true, value: '', post: true, width: 240 },
				{ id: 'detailsCity', 		name: 'detailsCity', 		fieldLabel: cityLabel_tx, 		 readOnly: true, value: '', post: true, width: 240 },
				{ id: 'detailsCounty', 		name: 'detailsCounty', 		fieldLabel: countyLabel_tx, 	 readOnly: true, value: '', post: true, width: 240 },
				{ id: 'detailsState', 		name: 'detailsState', 		fieldLabel: stateLabel_tx, 		 readOnly: true, value: '', post: true, width: 240 },
				{ id: 'detailsPostCode', 	name: 'detailsPostCode', 	fieldLabel: postcodeLabel_tx, 	 readOnly: true, value: '', post: true, width: 240 },
				{ id: 'detailsCountry', 	name: 'detailsCountry', 	fieldLabel: countryLabel_tx, 	 readOnly: true, value: '', post: true, width: 240 }		
	    	]
	   })
	] };

	var contactInfoTab = { title: contactInfoLabel_tx, id:'contactInfoTab', hideMode:'offsets', 
		listeners: { 'beforeshow': function(){ Ext.getCmp('detailsTabPanel').doLayout(); }},
		items: [
		new Ext.Panel({ layout: 'form',  autoWidth:true, defaults:{xtype: 'textfield', width: 270, labelWidth: 120}, bodyBorder: false, border: false,
    		items: [    
				{ id: 'detialsFirstname', 	name: 'detialsFirstname', 	fieldLabel: firstnameLabel_tx, 	 readOnly: true, value: '', post: true, width: 240 },
				{ id: 'detailsLastname', 	name: 'detailsLastname', 	fieldLabel: lastnameLabel_tx, 	 readOnly: true, value: '', post: true, width: 240 },
				{ id: 'detailsTelephone', 	name: 'detailsTelephone', 	fieldLabel: telephoneLabel_tx, 	 readOnly: true, value: '', post: true, width: 240 },
				{ id: 'detailsEmail', 		name: 'detailsEmail', 		fieldLabel: emailLabel_tx, 		 readOnly: true, value: '', post: true, width: 240 }
  			]
 		})
	] };	                                                                                              	   

	var tabPanel = new Ext.TabPanel({
		id:'detailsTabPanel', activeTab: 0, autoWidth: true, height:310, shadow: true, plain:true, bodyBorder: false, border: false, 
		style:'margin-top:6px; ', bodyStyle:'border-right: 1px solid #96bde7; border-left: 1px solid #96bde7; border-bottom: 1px solid #96bde7;', layoutOnTabChange: true, deferredRender: false,
		defaults:{frame: false, autoScroll: true, hideMode:'display', layout: 'form', labelWidth: 120, bodyStyle:'border-top: 0px;', style:'padding:10px; background-color: #eaf0f8;'},
		items: [ orderInfoTab, billingInfoTab, contactInfoTab ]
	});
	
	detailsPanel = new Ext.FormPanel({
        frame:false, id:'detailsPanel', layout:'form', padding: 5,
        items: [ topPanel, tabPanel ]
    });

	gDetailsWindowObj = new Ext.Window({
		id: 'DetailsWindow',
		title: orderDetailsTitle,
		closable: false,
		width:450,
		height:440,
		plain: true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		items: detailsPanel,
		buttons: [ { text: buttonOk_tx, handler: function(){ gDetailsWindowObj.hide(); }}],
		baseParams: { ref: sessionId }
	});
		
	var onDetails = function()
	{
		gDetailsWindowObj.show();

		var record = gridCheckBoxSelectionModelObj.getSelected();
		
		var orderStatusVal	 	= record.data.status;
		var orderDateVal 		= record.data.orderDate;
		var orderNumberVal 		= record.data.orderNumber;
		var productNameVal 		= record.data.productName;
		var qtyVal 				= record.data.qty;
		var orderTotalVal 		= record.data.orderTotal;
		var paymentDateVal 		= record.data.paymentConfirmedDate;
		var paymentConfirmedVal = record.data.paymentConfirmed;
		var companyNameVal 		= record.data.billingCompany;
		var address1Val 		= record.data.billingAddress1;
		var address2Val 		= record.data.billingAddress2;
		var address3Val 		= record.data.billingAddress3;
		var address4Val 		= record.data.billingAddress4;
		var cityVal 			= record.data.billingCity;
		var countyVal 			= record.data.billingCounty;
		var stateVal 			= record.data.billingState;
		var postcodeVal 		= record.data.billingPostCode;
		var countryVal 			= record.data.billingCountry;
		var telephoneVal 		= record.data.billingTelephone;
		var emailVal 			= record.data.billingEmail;
		var firstnameVal 		= record.data.billingContactFirstName;
		var lastnameVal 		= record.data.billingContactLastName;
		var trackingRefVal 		= record.data.shippingRef;

		Ext.getCmp('orderStatus').setValue(orderStatusVal);
		Ext.getCmp('detailsOrderDate').setValue(orderDateVal);
		Ext.getCmp('detailsOrderNumber').setValue(orderNumberVal);
		Ext.getCmp('detailsProductName').setValue(productNameVal);
		Ext.getCmp('detialsQty').setValue(qtyVal);
		Ext.getCmp('detailsOrderTotal').setValue(orderTotalVal);
		Ext.getCmp('paymentStatus').setValue((paymentConfirmedVal == 1) ? yesLabel_tx : noLabel_tx);
		Ext.getCmp('detailsPaymentDate').setValue((paymentConfirmedVal == 1) ? paymentDateVal : '');
		Ext.getCmp('detailsCompanyName').setValue(companyNameVal);
		Ext.getCmp('detailsAddress1').setValue(address1Val);
		Ext.getCmp('detailsAddress2').setValue(address2Val);
		Ext.getCmp('detailsAddress3').setValue(address3Val);
		Ext.getCmp('detailsAddress4').setValue(address4Val);
		Ext.getCmp('detailsCity').setValue(cityVal);
		Ext.getCmp('detailsCounty').setValue(countyVal);
		Ext.getCmp('detailsState').setValue(stateVal);
		Ext.getCmp('detailsPostCode').setValue(postcodeVal);
		Ext.getCmp('detailsCountry').setValue(countryVal);
		Ext.getCmp('detailsTelephone').setValue(telephoneVal);
		Ext.getCmp('detailsEmail').setValue(emailVal);
		Ext.getCmp('detialsFirstname').setValue(firstnameVal);
		Ext.getCmp('detailsLastname').setValue(lastnameVal);
		Ext.getCmp('trackingRef').setValue(trackingRefVal);
	};
	/**************** END Show details **********************/

	
	/**************** Mark as booked **********************/
	var onBookedSaveConfirmed = function(btn)
	{ 
		var onBookedCallback = function()
		{
			gridDataStoreObj.reload();
			gBookedWindowObj.hide();
		};

		if (btn == "yes") 
		{
			var paramArray = [];
			var selRecords = gridCheckBoxSelectionModelObj.getSelections();
			var ids = [];	
			paramArray['ref'] = sessionId;
				
			for (var rec = 0; rec < selRecords.length; rec++) 
			{	
				ids.push(selRecords[rec].data.id);	
			}
			paramArray['ids'] = ids.join(',');  


			if (Ext.getCmp('statusFilter').getValue() == 'S_SHIPPED_NOT_RECEIVED')
			{
				paramArray['bookedIn'] = '1';
			}
			else
			{
				paramArray['bookedIn'] = '0';
			}
			var dateStr = '';
			var timeStr = '';
			if (paramArray['bookedIn'] == '1')
			{
				dateStr = formatPHPDate(Ext.get('bookedDate').getValue(), gDateFormat, "yyyy-MM-dd");
				timeStr = Ext.getCmp('bookedTime').getValue();
			}

			paramArray['bookedInDate'] = dateStr;
			paramArray['bookedInTime'] = timeStr;
			paramArray['storeType'] = siteType;
				
		    Ext.taopix.formPost(gBookedWindowObj, paramArray, 'index.php?fsaction=CollectFromStore.callback&cmd=BOOKEDIN', updatingLabel_tx, onBookedCallback);
		}
	};
	
	
	var onBookedSave = function()
	{
		if (Ext.getCmp('bookedPanel').getForm().isValid())
		{
			Ext.MessageBox.confirm(confirmationLabel_tx, bookedConfirmation_txt, onBookedSaveConfirmed); 
		}
	};

		
	var onBook = function()
	{
		gBookedWindowObj.setTitle(this.getText());

		if (Ext.getCmp('statusFilter').getValue() == 'S_SHIPPED_NOT_RECEIVED')
		{
			gBookedWindowObj.show();
		}
		else
		{
			Ext.MessageBox.confirm(confirmationLabel_tx, bookedNotConfirmation_txt, onBookedSaveConfirmed); 
			return;
		}

		Ext.getCmp('bookedDate').reset();
		Ext.getCmp('bookedTime').reset();
		
		if ((gridCheckBoxSelectionModelObj.getCount() == 1) && (gridCheckBoxSelectionModelObj.getSelected().data.bookedConfirmed == '1'))
		{		
			Ext.getCmp('bookedDate').setValue(gridCheckBoxSelectionModelObj.getSelected().data.bookedConfirmedDate);
			Ext.getCmp('bookedTime').setValue(gridCheckBoxSelectionModelObj.getSelected().data.bookedConfirmedTime);
		}
	};


	var bookedPanel = new Ext.FormPanel({
        frame:true, id:'bookedPanel', layout:'form', cls: 'left-right-buttons', padding: 10,  bodyBorder: false,  border: false, autoHeight: true, defaults: {labelWidth:60},
        items: 
        [
         	{ xtype: 'panel', layout: 'column', columns: 2, plain:true, bodyBorder: false, border: false,  
				items: [
					new Ext.Container({ layout: 'form', width:270,
						items: new Ext.form.DateField({ fieldLabel: dateConfirmedLabel_tx, allowBlank: false, name: 'bookedDate', id: 'bookedDate', validateOnBlur:true, format: gDateFormat, width: 180, maxValue : (new Date()).clearTime() }) 
					}),
					new Ext.Container({ layout: 'form', width:95,
						items: new Ext.form.TimeField({ name: 'bookedTime', id: 'bookedTime', increment: 10, width: 75, hideLabel: true, allowBlank: false, format: timeFormat, minValue: minTime, maxValue: maxTime })
					})
				]
			}
        ]
    });
    
	gBookedWindowObj = new Ext.Window({
		id: 'bookedWindow',
		width:415, autoHeight: true, modal: true,frame:true,
		plain: true, draggable:true, resizable:false, layout: 'fit', closable: false, title: markRecievedLabel_tx,
		items: bookedPanel,
		baseParams: { ref: sessionId },
		buttons: 
		[ { text: buttonCancel_tx, handler: function(){ gBookedWindowObj.hide(); } }, { text: buttonSave_tx, handler: onBookedSave }	],
		listeners: {
			'show': function(){  Ext.getCmp('bookedPanel').getForm().items.each(function(item){ if (item.clearInvalid) item.clearInvalid(); }); this.doLayout(false, true); }
		}
	});
	/**************** END Mark as booked **********************/
	
	/**************** Mark as collected **********************/
	var onCollectedSave = function()
	{
		var onCollectedSaveConfirmed = function(btn)
		{ 
			var onCollectedCallback = function()
			{
				gridDataStoreObj.reload();
				gCollectedWindowObj.hide();
			};
			
			if (btn == "yes") 
			{
				var paramArray = [];
				var selRecords = gridCheckBoxSelectionModelObj.getSelections();
				var ids = [];
				var notpaidIds = [];	
				paramArray['ref'] = sessionId;

				for (var rec = 0; rec < selRecords.length; rec++) 
				{	
					ids.push(selRecords[rec].data.id);	
				}
				paramArray['ids'] = ids.join(','); 

				var unPaidGridStore = Ext.getCmp('unpaidGrid').store;
				var ids = [];
				for (var i = 0; i < unPaidGridStore.data.items.length; i++)
				{
					if (unPaidGridStore.data.items[i].data.paymentConfirmed == 1)
					{
						ids.push(unPaidGridStore.data.items[i].data.orderNumber);	
					}
					else
					{
						notpaidIds.push(unPaidGridStore.data.items[i].data.orderNumber);
					}
				}
				paramArray['markAsPaid'] = ids.join(','); 
				paramArray['markAsNotPaid'] = notpaidIds.join(','); 

				if (Ext.getCmp('statusFilter').getValue() == 'S_COLLECTED')
				{
					paramArray['collected'] = '0';
				}
				else
				{
					paramArray['collected'] = '1';
				}
				
				var dateStr = '';
				var timeStr = '';
				if (paramArray['collected'] == '1')
				{
					dateStr = formatPHPDate(Ext.get('collectedDate').getValue(), gDateFormat, "yyyy-MM-dd");
					timeStr = Ext.getCmp('collectedTime').getValue();
				}

				paramArray['collectedDate'] = dateStr;
				paramArray['collectedTime'] = timeStr;
				
				Ext.taopix.formPost(gCollectedWindowObj, paramArray, 'index.php?fsaction=CollectFromStore.callback&cmd=COLLECTED', updatingLabel_tx, onCollectedCallback);
			}
		};

		var onPaymentProceed = function(showConfirmation)
		{
			var message = collectedConfirmation_txt;
			if (Ext.getCmp('statusFilter').getValue() == 'S_COLLECTED')
			{
				message = collectedNotConfirmation_txt;
			}
			if (showConfirmation)
			{
				Ext.MessageBox.confirm(confirmationLabel_tx, message, onCollectedSaveConfirmed);
			}
			else
			{
				onCollectedSaveConfirmed("yes");
			}
		};

		var onPaymentConfirmed = function(btn)
		{
			if (btn == "yes") 
			{
				onPaymentProceed(false);
			}
			else
			{
				return false;
			}
		};

		if (((Ext.getCmp('collectedPanel').getForm().isValid()) && (Ext.getCmp('statusFilter').getValue() == 'S_RECEIVED_NOT_COLLECTED')) || (Ext.getCmp('statusFilter').getValue() == 'S_COLLECTED'))
		{
			var selRecords = Ext.getCmp('unpaidGrid').store.data.items;
			var allPaid = true;
			
			for (var rec = 0; rec < selRecords.length; rec++) 
			{
				if (selRecords[rec].data.paymentConfirmed != 1)
				{
					allPaid = false; break;
				}
			}
			if ((allPaid == false) && ((Ext.getCmp('statusFilter').getValue() == 'S_RECEIVED_NOT_COLLECTED')))
			{
				Ext.MessageBox.confirm(confirmationLabel_tx, paymentConfirmationLabel_tx, onPaymentConfirmed);
			}
			else
			{
				onPaymentProceed(true);
			}
		}
	};
	
	var onCollected = function()
	{
		gCollectedWindowObj.setTitle(this.getText());

		Ext.getCmp('collectedDate').reset();
		Ext.getCmp('collectedTime').reset();
		
		if ((gridCheckBoxSelectionModelObj.getCount() == 1) && (gridCheckBoxSelectionModelObj.getSelected().data.collectedConfirmed == '1') && (Ext.getCmp('statusFilter').getValue() == 'S_RECEIVED_NOT_COLLECTED'))
		{		
			Ext.getCmp('collectedDate').setValue(gridCheckBoxSelectionModelObj.getSelected().data.collectedConfirmedDate);
			Ext.getCmp('collectedTime').setValue(gridCheckBoxSelectionModelObj.getSelected().data.collectedConfirmedTime);
		}

		if (Ext.getCmp('statusFilter').getValue() == 'S_COLLECTED')
		{
			Ext.getCmp('collectedDateHolder').hide();
		}
		else
		{
			Ext.getCmp('collectedDateHolder').show();
		}
		
		var selRecords = gridCheckBoxSelectionModelObj.getSelections();
		var unpaidItemsArray = [];
	
		for (var rec = 0, bufArray = []; rec < selRecords.length; rec++) 
		{	
			bufArray = [selRecords[rec].data.id, selRecords[rec].data.paymentConfirmed, selRecords[rec].data.orderDate, selRecords[rec].data.orderNumber, selRecords[rec].data.productName, selRecords[rec].data.qty, selRecords[rec].data.orderTotal];
			unpaidItemsArray.push(bufArray);
		}
		Ext.getCmp('unpaidGrid').store.loadData(unpaidItemsArray);

		gCollectedWindowObj.show(); 
	};

	unpaidGridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({ 
		listeners: {
			selectionchange: function(unpaidGridCheckBoxSelectionModelObj) { 
				if (unpaidGridCheckBoxSelectionModelObj.getCount() > 0) 
				{ 
					Ext.getCmp('unpaidGrid').markPaidButton.enable(); Ext.getCmp('unpaidGrid').markNotPaidButton.enable();
				}
				else 
				{
					Ext.getCmp('unpaidGrid').markPaidButton.disable(); Ext.getCmp('unpaidGrid').markNotPaidButton.disable();
				}
			}
		}
	});

	var onMarkPaid = function()
	{
		var selRecords = unpaidGridCheckBoxSelectionModelObj.getSelections();
		for (var rec = 0; rec < selRecords.length; rec++) 
		{	
			selRecords[rec].data.paymentConfirmed = 1; 
		}
		Ext.getCmp('unpaidGrid').getView().refresh();
	};

	var onMarkNotPaid = function()
	{
		var selRecords = unpaidGridCheckBoxSelectionModelObj.getSelections();
		for (var rec = 0; rec < selRecords.length; rec++) 
		{	
			selRecords[rec].data.paymentConfirmed = 0; 
		}
		Ext.getCmp('unpaidGrid').getView().refresh();
	};

	var renderPaid = function(value, p, record, rowIndex, colIndex, store)
	{
		if (record.data.paymentConfirmed == 1)
		{
			return value;
		}
		else
		{
			return '<span style="color:red">'+value+'</span>';
		}
	};

	var renderPaidYesNo = function(value, p, record, rowIndex, colIndex, store)
	{
		if (record.data.paymentConfirmed == 1)
		{
			return yesLabel_tx;
		}
		else
		{
			return '<span style="color:red">'+noLabel_tx+'</span>';
		}
	};

	
	gCollectedWindowObj = new Ext.Window({
		id: 'collectedWindow',
		width:545, autoHeight: true, modal: true,frame:true, defaults: {labelWidth: 85},
		plain: true, draggable:true, resizable:false, layout: 'fit', closable: false, title: markCollectedLabel_txt,
		items: new Ext.FormPanel({
	        frame:true, id:'collectedPanel', layout:'form', cls: 'left-right-buttons', padding: 10,  bodyBorder: false,  border: false, autoHeight: true,
	        items: 
	        [
				{ xtype: 'panel', layout: 'column', columns: 2, plain:true, bodyBorder: false, border: false, id:'collectedDateHolder', 
					items: [
						new Ext.Container({ layout: 'form', width:290,
							items: new Ext.form.DateField({ fieldLabel: dateConfirmedLabel_tx, allowBlank: false, name: 'collectedDate', id: 'collectedDate', validateOnBlur:true, format: gDateFormat, width: 180, maxValue : (new Date()).clearTime() }) 
						}),
						new Ext.Container({ layout: 'form', width:95,
							items: new Ext.form.TimeField({ name: 'collectedTime', id: 'collectedTime', increment: 10, width: 75, hideLabel: true, allowBlank: false, format: timeFormat, minValue: minTime, maxValue: todayTime })
						})
					]
				},
				{ xtype: 'panel', bodyBorder: false, border:false, fieldLabel: paymentInfo_tx,
					items: [
						new Ext.grid.GridPanel({
							id: 'unpaidGrid', 
							style: 'border:1px solid #b4b8c8; margin-top: 8px',
							ctCls: 'grid',
    						store: new Ext.data.ArrayStore({
        						fields: 
        							[
										{name: 'id'},
										{name: 'paymentConfirmed'},
           								{name: 'orderDate'},
            							{name: 'orderNumber'},
            							{name: 'productName' },
            							{name: 'qty'},
            							{name: 'orderTotal'}
        							]
    						}),
    						selModel: unpaidGridCheckBoxSelectionModelObj,
    						cm: new Ext.grid.ColumnModel({
								defaults: {	sortable: false, resizable: false },
								columns: [unpaidGridCheckBoxSelectionModelObj,
           							{ header: orderNumberLabel_tx, dataIndex: 'orderNumber', width:130, renderer: renderPaid, menuDisabled: true },
            						{ header: quantityLabel_tx, dataIndex: 'qty', width:70, renderer: renderPaid, align: 'right', menuDisabled: true  },
            						{ id: 'productName', header: totalLabel_tx,	 dataIndex: 'orderTotal',width:80, renderer: renderPaid, align: 'right', menuDisabled: true  },
            						{ header: paidLabel_tx, dataIndex: 'paymentConfirmed', width:70, renderer: renderPaidYesNo, align: 'right', menuDisabled: true  }
        						]
							}),
    						autoExpandColumn: 'productName', height: 150,  width: 400, stateId: 'unpaidGrid',
    						stripeRows: true,  stateful: true, enableColLock:false, draggable:false, enableColumnHide:false, enableColumnMove:false, trackMouseOver:false, columnLines:true,
    						tbar: [	
								{ref: '../markPaidButton', iconCls: 'silk-money-add', text: markPaidLabel_tx, handler: onMarkPaid, disabled: true},'-',
								{ref: '../markNotPaidButton', iconCls: 'silk-money-delete', text: markNotPaid_tx, handler: onMarkNotPaid, disabled: true}
							]  	   
						})
		    		]
		    	}
	        ]
	    }),
		baseParams: { ref: sessionId },
		buttons: 
		[ { text: buttonCancel_tx, handler: function(){ gCollectedWindowObj.hide(); } }, { text: buttonSave_tx, handler: onCollectedSave }	],
		listeners: {
			'show': function(){  Ext.getCmp('collectedPanel').getForm().items.each(function(item){ if (item.clearInvalid) item.clearInvalid(); }); this.doLayout(false, true); }
		}
	});
	/**************** END Mark as collected **********************/
	
		
	/**************** Mark as received in DC **********************/
	var onBookedDCSaveConfirmed = function(btn)
	{ 
		var onBookedDCCallback = function()
		{
			gridDataStoreObj.reload();
			gBookedDCWindowObj.hide();
		};
			
		if (btn == "yes") 
		{
			var paramArray = [];
			var selRecords = gridCheckBoxSelectionModelObj.getSelections();
			var ids = [];	
			paramArray['ref'] = sessionId;
				
			for (var rec = 0; rec < selRecords.length; rec++) 
			{	
				ids.push(selRecords[rec].data.id);	
			}
			paramArray['ids'] = ids.join(',');  

			if (Ext.getCmp('statusFilter').getValue() == 'DC_SHIPPED_NOT_RECEIVED')
			{
				paramArray['bookedIn'] = '1';
			}
			else
			{
				paramArray['bookedIn'] = '0';
			}
			
			var dateStr = '';
			var timeStr = '';
			if (paramArray['bookedIn'] == '1')
			{
				dateStr = formatPHPDate(Ext.get('bookedDCDate').getValue(), gDateFormat, "yyyy-MM-dd");
				timeStr = Ext.getCmp('bookedDCTime').getValue();
			}

			paramArray['bookedInDate'] = dateStr;
			paramArray['bookedInTime'] = timeStr;
			paramArray['storeType'] = '0';
			Ext.taopix.formPost(gBookedDCWindowObj, paramArray, 'index.php?fsaction=CollectFromStore.callback&cmd=BOOKEDIN', updatingLabel_tx, onBookedDCCallback);
		}
	};

	
	var onBookedDCSave = function()
	{
		if (Ext.getCmp('bookedDCPanel').getForm().isValid())
		{
			Ext.MessageBox.confirm(confirmationLabel_tx, bookedConfirmation_txt, onBookedDCSaveConfirmed); 
		}
	};


	var onDCBook = function()
	{
		if (Ext.getCmp('statusFilter').getValue() == 'DC_SHIPPED_NOT_RECEIVED')
		{
			gBookedDCWindowObj.setTitle(this.getText());
			gBookedDCWindowObj.show(); 
		}
		else
		{
			Ext.MessageBox.confirm(confirmationLabel_tx, bookedNotConfirmation_txt, onBookedDCSaveConfirmed); 
			return;
		}

		Ext.getCmp('bookedDCDate').reset();
		Ext.getCmp('bookedDCTime').reset();
	
		if (gridCheckBoxSelectionModelObj.getCount() == 1)
		{		
			/* if 1 record is selected then prepopulate all the fields */
			if (gridCheckBoxSelectionModelObj.getSelected().data.bookedDCConfirmed == '1')
			{
				Ext.getCmp('bookedDCDate').setValue(gridCheckBoxSelectionModelObj.getSelected().data.bookedDCConfirmedDate);
				Ext.getCmp('bookedDCTime').setValue(gridCheckBoxSelectionModelObj.getSelected().data.bookedDCConfirmedTime);
			}
		}
	};

	
	var bookedDCPanel = new Ext.FormPanel({
        frame:true, id:'bookedDCPanel', layout:'form', cls: 'left-right-buttons', padding: 10,  bodyBorder: false,  border: false, autoHeight: true, defaults: {labelWidth:60},
        items: 
        [
			{ xtype: 'panel', layout: 'column', columns: 2, plain:true, bodyBorder: false, border: false,  
				items: [
					new Ext.Container({ layout: 'form', width:270,
						items: new Ext.form.DateField({ fieldLabel: dateConfirmedLabel_tx, allowBlank: false, name: 'bookedDCDate', id: 'bookedDCDate', validateOnBlur:true, format: gDateFormat, width: 180, maxValue : (new Date()).clearTime() }) 
					}),
					new Ext.Container({ layout: 'form', width:95,
						items: new Ext.form.TimeField({ name: 'bookedDCTime', id: 'bookedDCTime', increment: 10, width: 75, hideLabel: true, allowBlank: false, format: timeFormat, minValue: minTime, maxValue: maxTime })
					})
				]
			}
        ]
    });
    
	gBookedDCWindowObj = new Ext.Window({
		id: 'bookedDCWindow',
		width:415, autoHeight: true, modal: true,frame:true,
		plain: true, draggable:true, resizable:false, layout: 'fit', closable: false, title: markRecievedLabel_tx,
		items: bookedDCPanel,
		baseParams: { ref: sessionId },
		buttons: 
		[ { text: buttonCancel_tx, handler: function(){ gBookedDCWindowObj.hide(); } }, { text: buttonSave_tx, handler: onBookedDCSave }	],
		listeners: {
			'show': function(){  Ext.getCmp('bookedDCPanel').getForm().items.each(function(item){ if (item.clearInvalid) item.clearInvalid(); }); this.doLayout(false, true); }
		}
	});
	
	/**************** END Mark as received in DC **********************/
	  

	/**************** Mark as shipped in DC **********************/
	var onShippedSaveConfirmed = function(btn)
	{ 
		var onShippedDCCallback = function()
		{
			gridDataStoreObj.reload();
			gShippedDCWindowObj.hide();
		};
		
		if (btn == "yes") 
		{
			var paramArray = [];
			var selRecords = gridCheckBoxSelectionModelObj.getSelections();
			var ids = [];	
			paramArray['ref'] = sessionId;
			
			for (var rec = 0; rec < selRecords.length; rec++) 
			{	
				ids.push(selRecords[rec].data.id);	
			}
			paramArray['ids'] = ids.join(',');  

			if (Ext.getCmp('statusFilter').getValue() == 'DC_RECEIVED')	
			{
				paramArray['shipped'] = '1';
			}
			else
			{
				paramArray['shipped'] = '0';
			}		
			
			var dateStr = '';
			var timeStr = '';
			if (paramArray['shipped'] == '1')
			{
				dateStr = formatPHPDate(Ext.get('shippedDCDate').getValue(), gDateFormat, "yyyy-MM-dd");
				timeStr = Ext.getCmp('shippedDCTime').getValue();
			}

			paramArray['shippedDate'] = dateStr;
			paramArray['shippedTime'] = timeStr;
			paramArray['storeType'] = '0';
				
		    Ext.taopix.formPost(gShippedDCWindowObj, paramArray, 'index.php?fsaction=CollectFromStore.callback&cmd=SHIPPED', updatingLabel_tx, onShippedDCCallback);
		}
	};

	
	var onShippedDCSave = function()
	{
		if (Ext.getCmp('shippedDCPanel').getForm().isValid())
		{
			Ext.MessageBox.confirm(confirmationLabel_tx, shippedConfirmationLabel_tx, onShippedSaveConfirmed); 
		}
	};

	
	var onDCShip = function()
	{
		if (Ext.getCmp('statusFilter').getValue() == 'DC_RECEIVED')
		{
			gShippedDCWindowObj.setTitle(this.getText());
			gShippedDCWindowObj.show(); 
		}
		else
		{
			Ext.MessageBox.confirm(confirmationLabel_tx, shippedNotConfirmationLabel_tx, onShippedSaveConfirmed); 
			return;
		}

		Ext.getCmp('shippedDCDate').reset();
		Ext.getCmp('shippedDCTime').reset();
	
		if ((gridCheckBoxSelectionModelObj.getCount() == 1) && (gridCheckBoxSelectionModelObj.getSelected().data.shippedToStoreConfirmed == '1'))
		{		
			Ext.getCmp('shippedDCDate').setValue(gridCheckBoxSelectionModelObj.getSelected().data.shippedToStoreConfirmedDate);
			Ext.getCmp('shippedDCTime').setValue(gridCheckBoxSelectionModelObj.getSelected().data.shippedToStoreConfirmedTime);
		}
	};

	var shippedDCPanel = new Ext.FormPanel({
        frame:true, id:'shippedDCPanel', layout:'form', cls: 'left-right-buttons', padding: 10,  bodyBorder: false,  border: false, autoHeight: true, defaults: {labelWidth:60},
        items: 
        [
			{ xtype: 'panel', layout: 'column', columns: 2, plain:true, bodyBorder: false, border: false,  
				items: [
					new Ext.Container({ layout: 'form', width:270,
						items: new Ext.form.DateField({ fieldLabel: dateConfirmedLabel_tx, allowBlank: false, name: 'shippedDCDate', id: 'shippedDCDate', validateOnBlur:true, format: gDateFormat, width: 180, maxValue : (new Date()).clearTime() }) 
					}),
					new Ext.Container({ layout: 'form', width:95,
						items: new Ext.form.TimeField({ name: 'shippedDCTime', id: 'shippedDCTime', increment: 10, width: 75, hideLabel: true, allowBlank: false, format: timeFormat, minValue: minTime, maxValue: maxTime })
					})
				]
			}
        ]
    });
    
	gShippedDCWindowObj = new Ext.Window({
		id: 'shippedDCWindow',
		width:415,  autoHeight: true, modal: true,frame:true,
		plain: true, draggable:true, resizable:false, layout: 'fit', closable: false, title: markShippedLabel_tx,
		items: shippedDCPanel,
		baseParams: { ref: sessionId },
		buttons: 
		[ { text: buttonCancel_tx, handler: function(){ gShippedDCWindowObj.hide(); } }, { text: buttonSave_tx, handler: onShippedDCSave }	],
		listeners: {
			'show': function(){  Ext.getCmp('shippedDCPanel').getForm().items.each(function(item){ if (item.clearInvalid) item.clearInvalid(); }); this.doLayout(false, true); }
		}
	});
	
	/**************** END Mark as shipped in DC **********************/
	
	gridDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=CollectFromStore.listOrders&ref='+sessionId }),
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([ 
				{name:'id', 					  mapping: 0},
				{name: 'orderDate', 			  mapping: 1},
				{name: 'orderNumber', 			  mapping: 2},
				{name: 'productName', 			  mapping: 3}, 
				{name: 'qty', 					  mapping: 4}, 
				{name: 'paymentConfirmed',		  mapping: 5},  
				{name: 'status', 				  mapping: 6},  
				{name: 'orderTotal', 			  mapping: 7}, 
				{name: 'billingCompany', 		  mapping: 8},    
				{name: 'billingAddress', 		  mapping: 9},
				{name: 'billingAddress1', 		  mapping: 10},
				{name: 'billingAddress2', 		  mapping: 11},
				{name: 'billingAddress3', 		  mapping: 12},
				{name: 'billingAddress4', 		  mapping: 13},
				{name: 'billingCity', 		  	  mapping: 14},
				{name: 'billingCounty', 		  mapping: 15},
				{name: 'billingState', 		  	  mapping: 16},
				{name: 'billingPostCode', 		  mapping: 17},
				{name: 'billingCountry', 		  mapping: 18},
				{name: 'billingTelephone', 		  mapping: 19}, 
				{name: 'billingEmail', 			  mapping: 20}, 
				{name: 'billingContactFirstName', mapping: 21}, 
				{name: 'billingContactLastName',  mapping: 22}, 
				{name: 'paymentConfirmedDate',    mapping: 23},
				{name: 'bookedConfirmed',	      mapping: 24},
				{name: 'bookedConfirmedDate',	  mapping: 25},
				{name: 'bookedConfirmedTime',	  mapping: 26},
				{name: 'collectedConfirmed',	  mapping: 27},
				{name: 'collectedConfirmedDate',  mapping: 28},
				{name: 'collectedConfirmedTime',  mapping: 29},
				{name: 'storeCode', 			  mapping: 30},
				{name: 'bookedDCConfirmed',	      mapping: 31},
				{name: 'bookedDCConfirmedDate',	  mapping: 32},
				{name: 'bookedDCConfirmedTime',	  mapping: 33},
				{name: 'shippedToStoreConfirmed',	      mapping: 34},
				{name: 'shippedToStoreConfirmedDate',	  mapping: 35},
				{name: 'shippedToStoreConfirmedTime',	  mapping: 36},
				{name: 'statusOriginal',	  mapping: 37},
				{name: 'shippingRef',	  mapping: 38}
				
			])
		),
		sortInfo:{field: 'orderDate', direction: "ASC"},
		listeners:
		{
        	'beforeload':function()
        	{ 
				var statusFilterCmb = Ext.getCmp('statusFilter');
				var storeGrid = Ext.getCmp('grid');
				if(statusFilterCmb && storeGrid)
				{
					/*storeGrid.getStore().lastOptions.params['statusFilter'] = statusFilterCmb.getValue();*/
					storeGrid.getStore().setBaseParam('statusFilter', statusFilterCmb.getValue());
				}
				
				/*var statusVal = initialStatus;
				if(statusFilterCmb)
				{
					statusVal = statusFilterCmb.getValue();
				}
				this.lastOptions.params['statusFilter'] = statusVal;*/
        	}
        },
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	}); 
	gridDataStoreObj.load({params:{statusFilter: initialStatus}});
	
	
	gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({ 
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj) { 
				if (gridCheckBoxSelectionModelObj.getCount() == 1) 
				{ 
					if (grid.detailsButton) grid.detailsButton.enable();
					if (grid.collectedButton) grid.collectedButton.enable();
					if (grid.bookButton) grid.bookButton.enable();

					if (grid.dcShippedButton) grid.dcShippedButton.enable();
					if (grid.dcBookButton) grid.dcBookButton.enable();
				}
				else 
				{
					if (gridCheckBoxSelectionModelObj.getCount() > 1) 
					{
						if (grid.bookButton) grid.bookButton.enable();
						if (grid.collectedButton) grid.collectedButton.enable();

						if (grid.dcBookButton) grid.dcBookButton.enable();
						if (grid.dcShippedButton) grid.dcShippedButton.enable();
						
						if (grid.detailsButton) grid.detailsButton.disable();
					}
					else
					{
						if (grid.bookButton) grid.bookButton.disable();
						if (grid.detailsButton) grid.detailsButton.disable();
						if (grid.collectedButton) grid.collectedButton.disable();

						if (grid.dcBookButton) grid.dcBookButton.disable();
						if (grid.dcShippedButton) grid.dcShippedButton.disable();
					}
				}

				switch(Ext.getCmp('statusFilter').getValue())
				{
					case 'S_SHIPPED_NOT_RECEIVED': /* hide shipped to store */
						if (grid.collectedButton) grid.collectedButton.disable();
						break;
					case 'S_COLLECTED': /* hide shipped to store */
						if (grid.bookButton) grid.bookButton.disable();
						break;
					case 'DC_SHIPPED_NOT_RECEIVED': /* hide shipped to store */
						if (grid.dcShippedButton) grid.dcShippedButton.disable();
						break;
					case 'DC_SHIPPED_TO_STORE': /* hide shipped to store */
						if (grid.dcBookButton) grid.dcBookButton.disable();
						break;
				}
			}
		}
	});

	function isYesNo(value, p, record, rowIndex, colIndex, store) 
	{
		var text = '';
		if (record.data.paymentConfirmed == 0) 
		{
			text = noLabel_tx; 
			{/literal}{if $siteType == '1'}{literal} 
				text = '<span style="color:red">'+text+'</span>';
			{/literal}{/if}{literal}
		}
		else 
		{
			text = yesLabel_tx;
		}
		return text;
	};

	function isPaid(value, p, record, rowIndex, colIndex, store) 
	{
		text = value;
		if (record.data.paymentConfirmed == 0) 
		{
			{/literal}{if $siteType == '1'}{literal} 
				text = '<span style="color:red">'+value+'</span>';
			{/literal}{/if}{literal}
		}
		return text;
	};
	
	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {	sortable: true, resizable: true },
		columns: [gridCheckBoxSelectionModelObj,
            { header: orderDateLabel_tx, 				 	dataIndex: 'orderDate', 				width:100, renderer: isPaid },
            { header: orderNumberLabel_tx, 			     	dataIndex: 'orderNumber', 				width:120, renderer: isPaid },
            {/literal}{if $siteType == '1'}{literal}
            { header: firstnameLabel_tx, 	     			dataIndex: 'billingContactFirstName',	width:100, renderer: isPaid },
            { header: lastnameLabel_tx, 		     		dataIndex: 'billingContactLastName', 	width:100, renderer: isPaid },
            {/literal}{/if}{literal}
            { header: productNameLabel_tx, 			     	dataIndex: 'productName', 				width:200, 	id: 'productName', renderer: isPaid },
            { header: quantityLabel_tx, 					dataIndex: 'qty', 						width:90, 	align: 'right', renderer: isPaid  },
            {/literal}{if $siteType == '1'}{literal}
            { header: paymentConfirmedLabel_tx, 		 	dataIndex: 'paymentConfirmed', 			width:120,  renderer: isYesNo, 	align: 'right'  },
            {/literal}{/if}{literal}
            {/literal}{if $siteType == '0'}{literal}
            { header: storeLabel_tx, 		 				dataIndex: 'storeCode', 				width:250, renderer: isPaid },
            {/literal}{/if}{literal}
            { header: shippingRefLabel_tx, 				 	dataIndex: 'shippingRef', 				width:100, renderer: isPaid },
            { header: statusLabel_tx, 				 	 	dataIndex: 'status', 					width:250, renderer: isPaid }
        ]
	});

	var updateGridButtons = function()
	{
		var statusFilter = Ext.getCmp('statusFilter');
		switch (statusFilter.getValue())
		{
			case 'S_SHIPPED_NOT_RECEIVED':
				if (grid.bookButton) grid.bookButton.setText(markRecievedLabel_tx);
				if (grid.collectedButton) grid.collectedButton.setText(markCollectedLabel_txt);
				break;
			case 'S_RECEIVED_NOT_COLLECTED':
				if (grid.bookButton) grid.bookButton.setText(markRecievedNotLabel_tx);
				if (grid.collectedButton) grid.collectedButton.setText(markCollectedLabel_txt);
				break;
			case 'S_COLLECTED':
				if (grid.bookButton) grid.bookButton.setText(markRecievedLabel_tx);
				if (grid.collectedButton) grid.collectedButton.setText(markCollectedNotLabel_txt);
				break;

			case 'DC_SHIPPED_NOT_RECEIVED':
				if (grid.dcBookButton) grid.dcBookButton.setText(markRecievedLabel_tx);
				if (grid.dcShippedButton) grid.dcShippedButton.setText(markCollectedLabel_txt);
				break;
			case 'DC_RECEIVED':
				if (grid.dcBookButton) grid.dcBookButton.setText(markRecievedNotLabel_tx);
				if (grid.dcShippedButton) grid.dcShippedButton.setText(markShippedLabel_tx);
				break;
			case 'DC_SHIPPED_TO_STORE':
				if (grid.dcBookButton) grid.dcBookButton.setText(markRecievedLabel_tx);
				if (grid.dcShippedButton) grid.dcShippedButton.setText(markNotShippedLabel_tx);
				break;
		}
	};

	grid = new Ext.grid.GridPanel({
    	id: 'grid',
    	store: gridDataStoreObj,
        selModel: gridCheckBoxSelectionModelObj,
        cm: gridColumnModelObj,
        stripeRows: true,
        stateful: true,
        enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		trackMouseOver:false,
		loadMask: true,
		autoExpandColumn: 'productName',
		columnLines:true,
        stateId: 'grid',
        ctCls: 'grid',
        plugins: [new Ext.ux.grid.Search({
			iconCls: 'silk-zoom',
			minChars: 3,
			width: 200,
			autoFocus: true,
			disableIndexes:['qty','orderTotal','status', 'paymentConfirmed', 'orderDate']
		})],

		tbar: [	
        	{ref: '../detailsButton', iconCls: 'silk-book-open', text: showDetailsLabel_txt, handler: onDetails, disabled: true},'-',
        	{/literal}{if $siteType == '0'}{literal}
            	{ref: '../dcBookButton', iconCls: 'silk-package', text: markRecievedLabel_tx, handler: onDCBook, disabled: true},'-',
            	{ref: '../dcShippedButton', iconCls: 'silk-lorry', text: markShippedLabel_tx, handler: onDCShip, disabled: true},
            {/literal}{else}{literal}
            	{ref: '../bookButton', iconCls: 'silk-package', text: markRecievedLabel_tx, handler: onBook, disabled: true},'-',
            	{ref: '../collectedButton', iconCls: 'silk-group', text: markCollectedLabel_txt, handler: onCollected, disabled: true},
            {/literal}{/if}{literal}
            { xtype:'tbfill'},
            new Ext.form.ComboBox({ id: 'statusFilter', name: 'statusFilter', hiddenName:'statusFilter_hn',	allowBlank: false,
				mode: 'local', editable: false,	forceSelection: true, hiddenId:'statusFilter_hi', 
				store: new Ext.data.ArrayStore({ id: 0, fields: ['id', 'name'],	data: statusFilterList }), validationEvent:false, valueField: 'id', displayField: 'name', useID: true, 
				post: true, fieldLabel: statusFilterLabel_tx, triggerAction: 'all', width: 280,
				listeners: { 
					'select': function(combo, record, index)
					{ 
						/*gridDataStoreObj.reload({params: gridDataStoreObj.lastOptions.params}); updateGridButtons(); */
						gridDataStoreObj.load();
						updateGridButtons();
					} 
				}
			}),
			{ xtype: 'tbspacer', width: 20}, 
			{ xtype: 'button', text: str_ExtJsPagingToolbarRefresh, tooltip: str_ExtJsPagingToolbarRefresh, overflowText: str_ExtJsPagingToolbarRefresh, iconCls: 'x-tbar-loading', handler: function(){ gridDataStoreObj.reload({params: gridDataStoreObj.lastOptions.params}) }, scope: this}, 
        	{ xtype: 'tbspacer', width: 20} 
        ],
        bbar: new Ext.Toolbar({ items: [ {xtype:'tbfill'} ]  }) 
    }); 

	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		{/literal}{if $siteType == '0'}{literal}
			title: distributionCentreLabel_tx,
		{/literal}{else}{literal}
			title: storeLabel_tx,
		{/literal}{/if }{literal}
		items: grid,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose(); }, qtip: '{/literal}{#str_LabelCloseWindow#}{literal}' }],
		baseParams: { ref: '{/literal}{$ref}{literal}' }
	});
		
	centreRegion.add(gMainWindowObj);
	centreRegion.doLayout();

	if (Ext.getCmp('statusFilter')) Ext.getCmp('statusFilter').setValue(initialStatus);
	
}

/* close this window panel */
function windowClose()
{
	if (Ext.getCmp('MainWindow'))
	{
		centreRegion.remove('MainWindow', true);
		centreRegion.doLayout();
	}
}
{/literal}