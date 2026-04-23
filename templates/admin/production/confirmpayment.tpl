{literal}

// For todays date;
Date.prototype.today = function () { 
    return ((this.getDate() < 10)?"0":"") + this.getDate() +"-"+(((this.getMonth()+1) < 10)?"0":"") + (this.getMonth()+1) +"-"+ this.getFullYear();
}

// For the time now
Date.prototype.timeNow = function () {
     return ((this.getHours() < 10)?"0":"") + this.getHours() +":"+ ((this.getMinutes() < 10)?"0":"") + this.getMinutes();
}

function initialize(pParams)
{	
	var newDate = new Date();
	var todayDate = newDate.today();
	var nowTime = newDate.timeNow();
	var gDateFormat = "{/literal}{$dateformat}{literal}";
	var gTimeFormat = "{/literal}{$timeformat}{literal}";

	function enableDate()
	{
		var paymentReceived = Ext.getCmp('paymentreceived').getValue();

		if (paymentReceived)
		{
			Ext.getCmp('paymentreceivedtime').enable();
			Ext.getCmp('paymentreceiveddate').enable();
		} 
		else
		{
			Ext.getCmp('paymentreceivedtime').disable();
			Ext.getCmp('paymentreceiveddate').disable();
		}
	}

	function getOrderIds(pTheGrid) 
	{
		var iDList = '';

		if (pTheGrid)
		{
			var selRecords = pTheGrid.getSelectionModel().getSelections();
			for (var rec = 0; rec < selRecords.length; rec++)
			{
				iDList = iDList + (selRecords[rec].data.orderid) + ',';
			}
			iDList = iDList.slice(0, -1);
		}

		return iDList;
	}

 	var okFunction = function(btn,ev)
 	{
		var fp = Ext.getCmp('confirmPaymentForm');
		var form = fp.getForm();
		var gridObj = Ext.getCmp('productionmaingrid');
		var idlist = getOrderIds(gridObj);
		var timeVal = '00:00:00';
		var dateVal = '0000-00-00';
		var paymentReceived = Ext.getCmp('paymentreceived').getValue();
		var paymentReceivedDate = dateVal + ' ' + timeVal;

		if (paymentReceived)
		{
			timeVal = Ext.getCmp('paymentreceivedtime').getRawValue();
			dateVal = Ext.getCmp('paymentreceiveddate').getRawValue();

			paymentReceivedDate = formatPHPDate(dateVal, gDateFormat, "yyyy-MM-dd") + ' ' + timeVal;
		}

		var params = [];
		params['orderidlist'] = idlist;
		params['ref'] = '{/literal}{$ref}{literal}';
		params['paymentreceiveddate'] = paymentReceivedDate

		Ext.taopix.formPanelPost(fp, form, params, 'index.php?fsaction=AdminProduction.updateOrderPaymentStatus', "{/literal}{#str_MessageSaving#}{literal}", onConfirmPaymentCallback);
	};
	
	var cancelFunction = function(btn,ev)
	{ 
		confirmPaymentWindowExists = false; 
		gDialogObj.close();
	};

	var paymentConfirmationFormTabPanelObj = new Ext.FormPanel({
        id: 'confirmPaymentForm',
		header: false,
        frame: true,
        layout: 'form',
		autoWidth: true,
		bodyBorder: false,
		border: true,
		labelWidth: 130,
        items: [ 
			{
				id: 'itemspacer',
				name: 'itemspacer',
				cls: "",
				style: "margin-top:6px;color:#a0a0a0"
			},
			new Ext.form.ComboBox({
				id: 'paymentreceived',
				name: 'paymentreceived',
				mode: 'local',
				editable: false,
				forceSelection: true,
				selectOnFocus: true,
				triggerAction: 'all',
				fieldLabel: "{/literal}{#str_LabelProductionPaymentReceived#}{literal}",
				store: new Ext.data.ArrayStore({
					id: 0,
					fields: ['id', 'name'],
					data:
					[
						[0, "{/literal}{#str_LabelNo#}{literal}"],
						[1, "{/literal}{#str_LabelYes#}{literal}"]
					]
				}),
				valueField: 'id',
				value: 0,
				displayField: 'name',
				width: 130,
				useID: true,
				post: true,
				listeners: { 'select': function(){enableDate();} },
			}),
			{
				xtype: 'container', layout: 'column', width: 600,
				id: 'datetimecontainer',
				items: [
					{
						width: 280,
						height: 25,
						xtype: 'panel',
						layout: 'form',
						defaults: {width: 130},
						items: [
								new Ext.form.DateField({
									id: 'paymentreceiveddate',
									name: 'paymentreceiveddate',
									fieldLabel: "{/literal}{#str_LabelProductionPaymentConfirmedDate#}{literal}",
									width: 130,
									showToday: true,
									format: gDateFormat,
									value: todayDate,
									disabled: true
								})
						]
					},
					{
						width: 280,
						height: 25,
						xtype: 'panel',
						layout: 'form',
						defaults: {width: 130},
						items:
						[
							new Ext.form.TimeField({
								id: 'paymentreceivedtime',
								name: 'paymentreceivedtime',
								width: 130,
								hideLabel: true,
								showToday: true,
								format: gTimeFormat,
								value: nowTime,
								disabled: true
							})
						]
					}
				]
			}
		]
	});

	/* create modal window for add and edit */
	gDialogObj = new Ext.Window({
		id: 'dialog',
		closable:false,
		title: "{/literal}{#str_SectionTitlePaymentConfirmation#}{literal}",
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		height: 220,
		width: 550,
		items: paymentConfirmationFormTabPanelObj,
		buttons:
		[
			{ text: '{/literal}{#str_ButtonCancel#}{literal}', handler: cancelFunction},
			{ text: "{/literal}{#str_ButtonOk#}{literal}", handler: okFunction, cls: 'x-btn-right' }
		]
	});

	var mainPanel = Ext.getCmp('dialog');
	mainPanel.show();	
}

{/literal}