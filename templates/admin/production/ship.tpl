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

	function getOrderIds(pTheGrid) 
	{
		var iDList = '';

		if (pTheGrid)
		{
			var selRecords = pTheGrid.getSelectionModel().getSelections();
			for (var rec = 0; rec < selRecords.length; rec++)
			{
				iDList = iDList + (selRecords[rec].data.orderid) + ':' + (selRecords[rec].data.id) + ',';
			}
			iDList = iDList.slice(0, -1);
		}

		return iDList;
	}

 	var okFunction = function(btn,ev)
 	{
		var fp = Ext.getCmp('shippingForm');
		var form = fp.getForm();
		var gridObj = Ext.getCmp('productionmaingrid');
		var idlist = getOrderIds(gridObj);
		var timeVal = Ext.getCmp('itemshippingdatetime').getRawValue();
		var dateVal = Ext.getCmp('itemshippingdate').getRawValue();

		var params = [];
		params['idlist'] = idlist;
		params['ref'] = '{/literal}{$ref}{literal}';
		params['itemshippingdate'] = formatPHPDate(dateVal, gDateFormat, "yyyy-MM-dd") + ' ' + timeVal;
		Ext.taopix.formPanelPost(fp, form, params, 'index.php?fsaction=AdminProduction.updateItemShippingStatus', "{/literal}{#str_MessageSaving#}{literal}", onShipCallback);
	};
	
	var cancelFunction = function(btn,ev)
	{ 
		shipWindowExists = false; 
		gDialogObj.close();
	};

	var shipFormTabPanelObj = new Ext.FormPanel({
        id: 'shippingForm',
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
			{
				xtype: 'container', layout: 'column', width: 450,
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
									id: 'itemshippingdate',
									name: 'itemshippingdate',
									fieldLabel: "{/literal}{#str_LabelProductionShippedDate#}{literal}",
									width: 130,
									showToday: true,
									format: gDateFormat,
									value: todayDate
								})
						]
					},
					{
						width: 150,
						height: 25,
						xtype: 'panel',
						layout: 'form',
						defaults: {width: 130},
						items:
						[
							new Ext.form.TimeField({
								id: 'itemshippingdatetime',
								name: 'itemshippingdatetime',
								width: 130,
								hideLabel: true,
								showToday: true,
								format: gTimeFormat,
								value: nowTime
							})
						]
					}
				]
			},
			{
				xtype: 'textfield',
				width: 275,
				id: 'shippingtrackingreference',
				name: 'shippingtrackingreference',
				readOnly: false,
				disabled: false,
				fieldLabel: '{/literal}{#str_LabelProductionTrackingRef#}{literal}',
				post: true
			}
		]
	});

	/* create modal window for add and edit */
	gDialogObj = new Ext.Window({
		id: 'dialog',
		closable:false,
		title: "{/literal}{#str_ButtonShip#}{literal}",
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		height: 200,
		width: 450,
		items: shipFormTabPanelObj,
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