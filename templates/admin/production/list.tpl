{literal}
var orderDetailsWindowExists = false;
var onHoldWindowExists = false;
var confirmPaymentWindowExists = false;
var preferencesWindowExists = false;
var shipWindowExists = false;
var languagecode = '{/literal}{$languagecode}{literal}';
var gridPageSize = {/literal}{$gridpagesize}{literal};
var prefData = {/literal}{$prefdata}{literal};
var datelastmodified = 0;
var measurementunit = '';
var gDateFormat = "{/literal}{$dateformat}{literal}";
var dataArray = [];

if (prefData != 0) 
{
	dataArray = JSON.parse(prefData);
	measurementunit = dataArray['measurementunit'];
}

//constants
var TPX_ORDER_STATUS_IN_PROGRESS = {/literal}{$TPX_ORDER_STATUS_IN_PROGRESS}{literal};
var TPX_ORDER_STATUS_CANCELLED = {/literal}{$TPX_ORDER_STATUS_CANCELLED}{literal};
var TPX_ORDER_STATUS_COMPLETED = {/literal}{$TPX_ORDER_STATUS_COMPLETED}{literal};
var TPX_ORDER_STATUS_CONVERTED = {/literal}{$TPX_ORDER_STATUS_CONVERTED}{literal};
var TPX_ITEM_STATUS_PRINTED = {/literal}{$TPX_ITEM_STATUS_PRINTED}{literal};
var TPX_ITEM_STATUS_FINISHING_COMPLETE = {/literal}{$TPX_ITEM_STATUS_FINISHING_COMPLETE}{literal};
var TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER = {/literal}{$TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER}{literal};
var TPX_ITEM_STATUS_SHIPPED_TO_DISTRIBUTION_CENTRE = {/literal}{$TPX_ITEM_STATUS_SHIPPED_TO_DISTRIBUTION_CENTRE}{literal};
var TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_DISTRIBUTION_CENTRE = {/literal}{$TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_DISTRIBUTION_CENTRE}{literal};
var TPX_ITEM_STATUS_SHIPPED_TO_STORE_FROM_DISTRIBUTION_CENTRE = {/literal}{$TPX_ITEM_STATUS_SHIPPED_TO_STORE_FROM_DISTRIBUTION_CENTRE}{literal};
var TPX_ITEM_STATUS_SHIPPED_TO_STORE_DIRECTLY = {/literal}{$TPX_ITEM_STATUS_SHIPPED_TO_STORE_DIRECTLY}{literal};
var TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_STORE = {/literal}{$TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_STORE}{literal};
var TPX_ITEM_STATUS_SHIPPED_COLLECTED_BY_CUSTOMER = {/literal}{$TPX_ITEM_STATUS_SHIPPED_COLLECTED_BY_CUSTOMER}{literal};
var TPX_ITEM_STATUS_DOWNLOAD_FILES_FTP_ERROR = {/literal}{$TPX_ITEM_STATUS_DOWNLOAD_FILES_FTP_ERROR}{literal};
var TPX_ITEM_STATUS_IMPORT_FILES_ERROR = {/literal}{$TPX_ITEM_STATUS_IMPORT_FILES_ERROR}{literal};
var TPX_ITEM_STATUS_DECRYPTING_FILES_ERROR = {/literal}{$TPX_ITEM_STATUS_DECRYPTING_FILES_ERROR}{literal};
var TPX_ITEM_STATUS_RAW_FILES_RENDER_ERROR = {/literal}{$TPX_ITEM_STATUS_RAW_FILES_RENDER_ERROR}{literal};
var TPX_ITEM_STATUS_CONVERTING_FILES_ERROR = {/literal}{$TPX_ITEM_STATUS_CONVERTING_FILES_ERROR}{literal};
var TPX_ITEM_STATUS_PRINTING_FILES_ERROR = {/literal}{$TPX_ITEM_STATUS_PRINTING_FILES_ERROR}{literal};

var ActiveStatus = 0;
var OrderStatus = 0;

function initialize(pParams)
{
	/* A J A X */
	/* function to create an XMLHttp Object */
	function getxmlhttp()
	{
		/* create a boolean variable to check for a valid Microsoft ActiveX instance */
		var xmlhttp = false;
		/* check if we are using Internet Explorer */
		try
		{
			/* if the Javascript version is greater then 5 */
			xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			/* if not, then use the older ActiveX object */
			try
			{
				/* if we are using Internet Explorer */
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				/* else we must be using a non-Internet Explorer browser */
				xmlhttp = false;
			}
		}

		/* if we are not using IE, create a JavaScript instance of the object */
		if (!xmlhttp && typeof XMLHttpRequest != 'undefined')
		{
			xmlhttp = new XMLHttpRequest();
		}
		return xmlhttp;
	}

	/* function to process an XMLHttpRequest */
	function processAjax(serverPage, pParams, async)
	{
		var params = pParams;

		/* get an XMLHttpRequest object for use */
		/* make xmlhttp local so we can run simlutaneous requests */
		var xmlhttp = getxmlhttp();

		xmlhttp.open('POST', serverPage+"&dummy=" + new Date().getTime(), async);

		xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

		xmlhttp.onreadystatechange = function()
		{
			if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
			{
				
			}
		};
		xmlhttp.send(params);

		if (!async)
		{
			return xmlhttp.responseText;
		}
	}

	function getDateTimeStamp()
	{
		var newDate = new Date();
		var timestamp = newDate.getTime();
		timestamp = Math.round(timestamp / 1000);

		return timestamp;
	}

	function generalColumnRenderer(value, p, record)
	{
		var returnVal = value;

		//if error display red
		switch(parseInt(record.data.statusid)) 
		{
			case TPX_ITEM_STATUS_DOWNLOAD_FILES_FTP_ERROR:
			case TPX_ITEM_STATUS_IMPORT_FILES_ERROR:
			case TPX_ITEM_STATUS_DECRYPTING_FILES_ERROR: 
			case TPX_ITEM_STATUS_RAW_FILES_RENDER_ERROR:
			case TPX_ITEM_STATUS_CONVERTING_FILES_ERROR: 
			case TPX_ITEM_STATUS_PRINTING_FILES_ERROR:
				returnVal = '<span style="color:#FF0000">'+returnVal+'</span>';
				break;
		}

		//if onhold display grey
		if (record.data.onhold == 'true')
		{
			returnVal = '<span style="color:#808080">'+returnVal+'</span>';
		}

		//if expired italic and grey
		if (record.data.expired == 'true')
		{
			returnVal = '<span style="color:#808080"><i>'+returnVal+'</i></span>';
		}

		return returnVal;
	}

	function dateColumnRenderer(value, p, record)
	{
		var thisdate = new Date(value.replace(/-/g, "/"));
		var returnVal = value; 
		var jsDateFormat = convertPHPDateFormat(gDateFormat);
		jsDateFormat = jsDateFormat.replace('d-','dd-');
		jsDateFormat = jsDateFormat.replace('M-','MM-');

		if (thisdate != 'Invalid Date')
		{
			returnVal = formatDate(thisdate, jsDateFormat);
		}

		returnVal = generalColumnRenderer(returnVal, p, record);

		return returnVal;
	}

	function setColumnDisplay()
	{
		if (prefData != 0)
		{
			var gridObj = Ext.getCmp('productionmaingrid');
			var colModel = gridObj.getColumnModel();
			var data = [];
			var dataArray = JSON.parse(prefData);
			var itemsArray = dataArray['displaycolumns'];

			for (var i = 0; i < itemsArray.length; i++)
			{
				var index = colModel.findColumnIndex(itemsArray[i].index);
				var hidden = itemsArray[i].checked ? false : true;
				colModel.setHidden(index, hidden);

				data.push({index:itemsArray[i].id, checked:itemsArray[i].checked});
			}
		}
	}

	function checkStatus()
	{
		var gridObj = Ext.getCmp('productionmaingrid');
		var selRecords = gridObj.selModel.getSelections();
		var returnVal = false;
		var paramString = '';
		var data = [];

		for (var i = 0; i < selRecords.length; i++) 
		{
			var id = selRecords[i].data.id;
			var statusid = selRecords[i].data.statusid;
			var itemactivestatus = selRecords[0].data.orderstatus;

			data.push({id: id, statusid: statusid, activestatus: itemactivestatus});
		}

		var jsonData = JSON.stringify(data);
		paramString = 'data='+jsonData+'&csrf_token='+Ext.taopix.getCSRFToken()+'&ref={/literal}{$ref}{literal}';

		var result = processAjax('index.php?fsaction=AdminProduction.statusCheck', paramString, false);

		if (parseJson(result).success)
		{
			returnVal = true;
		}
		else
		{
			Ext.MessageBox.show({ title: '{/literal}{#str_TitleError#}{literal}', msg: '{/literal}{#str_ErrorThereWasAProblemWithYourRequest#}{literal}', buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO });
			gridReload();
			returnVal = false;
		}

		return returnVal;
	}

	onPreferencesCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{			
			if ((Ext.getCmp('dialog')) && (Ext.getCmp('dialog').isVisible()))
			{
				Ext.getCmp('dialog').close();
			}
			
			if (preferencesWindowExists)
			{
				preferencesWindowExists = false;
			}

			Ext.getCmp('productionmaingrid').store.baseParams['measurementunit'] = measurementunit;
			Ext.getCmp('productionmaingrid').store.reload({});
			setColumnDisplay();
		}
	};

	onHoldCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{			
			if ((Ext.getCmp('dialog')) && (Ext.getCmp('dialog').isVisible()))
			{
				Ext.getCmp('dialog').close();
			}
			
			if (onHoldWindowExists)
			{
				Ext.getCmp('productionmaingrid').store.reload({});
				onHoldWindowExists = false;
			}
		}
	};

	onShipCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{			
			if ((Ext.getCmp('dialog')) && (Ext.getCmp('dialog').isVisible()))
			{
				Ext.getCmp('dialog').close();
			}
			
			if (shipWindowExists)
			{
				Ext.getCmp('productionmaingrid').store.reload({});
				shipWindowExists = false;
			}
		}
	};

	onConfirmPaymentCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{			
			if ((Ext.getCmp('dialog')) && (Ext.getCmp('dialog').isVisible()))
			{
				Ext.getCmp('dialog').close();
			}
			
			if (confirmPaymentWindowExists)
			{
				Ext.getCmp('productionmaingrid').store.reload({});
				confirmPaymentWindowExists = false;
			}
		}
	};

	var onClear = function()
	{
		datelastmodified = 0;
		var appGrid = Ext.getCmp('productionmaingrid');
		appGrid.store.baseParams['itemstatusonhold'] = 0;
		appGrid.store.baseParams['orderstatuswaitingforpayment'] = 0;
		appGrid.store.baseParams['itemactivestatus'] = 0;
		appGrid.store.baseParams['itemstatus'] = '';
		appGrid.store.baseParams['datelastmodifed'] = datelastmodified;
		statusDropdown.setValue('{/literal}{#str_LabelAllActiveOrders#}{literal}');
	}

	var onSearch = function(searchVal)
	{
		datelastmodified = 0;
		var appGrid = Ext.getCmp('productionmaingrid');
		appGrid.store.baseParams['datelastmodifed'] = datelastmodified;
		if (searchVal != '')
		{
			statusDropdown.setValue('{/literal}{#str_LabelProductionSearchResults#}{literal}');
		}
	}	

	/* hold handler */
	function onHold(btn, ev)
	{		 
		/* server parameters are sent to the server */
		var serverParams = new Object();
		var gridObj = Ext.getCmp('productionmaingrid');

		var id = Ext.taopix.gridSelection2IDList(gridObj);
		
		serverParams['id'] = id;
		serverParams['ref'] = '{/literal}{$ref}{literal}';

        if (!onHoldWindowExists)
		{
			onHoldWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProduction.onHoldDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}	

	/* ship handler */
	function onShip(btn, ev)
	{		 
		var status = checkStatus();
		/* server parameters are sent to the server */
		var serverParams = new Object();
		var gridObj = Ext.getCmp('productionmaingrid');

		var id = Ext.taopix.gridSelection2IDList(gridObj);
		
		serverParams['id'] = id;
		serverParams['ref'] = '{/literal}{$ref}{literal}';

		if (status) 
		{
			if (!shipWindowExists)
			{
				shipWindowExists = true;
				Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProduction.shippingDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
			}
		}
	}

	var onCancel = function(btn, ev)
	{
		ActiveStatus = TPX_ORDER_STATUS_CANCELLED;
		onActiveStatus(btn, ev);
	};

	var onActiveStatus = function(btn, ev)
	{
		var status = checkStatus();
		var progressMsg = "{/literal}{#str_MessagePleaseWait#}{literal}";
		var confirmMsg = "";
		var idlist = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('productionmaingrid'));

		switch (ActiveStatus)
		{
			case TPX_ORDER_STATUS_IN_PROGRESS:
				confirmMsg = "{/literal}{#str_ReActivateItemsConfirmation#}{literal}";
				break;

			case TPX_ORDER_STATUS_CANCELLED:
				confirmMsg = "{/literal}{#str_CancelItemsConfirmation#}{literal}";
				break;

			case TPX_ORDER_STATUS_COMPLETED:
				confirmMsg = "{/literal}{#str_CompleteItemsConfirmation#}{literal}";
				break;
		}

		var onActiveStatusConfirmed = function(btn)
		{
			if (btn == "yes")
			{
				var paramArray = {};
				paramArray['idlist'] = idlist;
				paramArray['itemactivestatus'] = ActiveStatus;
				paramArray['ref'] = '{/literal}{$ref}{literal}';

				Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminProduction.updateItemActiveStatus', progressMsg, onActiveStatusCallback);
			}
		};

		if (status)
		{
			Ext.MessageBox.minWidth = 350;
			Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", confirmMsg, onActiveStatusConfirmed);
		}
	};

	function onActiveStatusCallback()
	{
		datelastmodified = 0;
		var appGrid = Ext.getCmp('productionmaingrid');
		appGrid.store.reload({});
	}

	var onOrderStatus = function(btn, ev)
	{
		var status = checkStatus();
		var progressMsg = "{/literal}{#str_MessagePleaseWait#}{literal}";
		var confirmMsg = "";

		switch (OrderStatus)
		{
			case TPX_ITEM_STATUS_FINISHING_COMPLETE:
				confirmMsg = "{/literal}{#str_FinishItemsConfirmation#}{literal}";
				break;
		}

		var onOrderStatusConfirmed = function(btn)
		{
			if (btn == "yes")
			{
				var paramArray = {};
				paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('productionmaingrid'));
				paramArray['itemstatus'] = OrderStatus;
				paramArray['ref'] = '{/literal}{$ref}{literal}';

				Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminProduction.updateItemStatus', progressMsg, onOrderStatusCallback);
			}
		};

		if (status)
		{
			Ext.MessageBox.minWidth = 350;
			Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", confirmMsg, onOrderStatusConfirmed);
		}
	};

	function onOrderStatusCallback()
	{
		datelastmodified = 0;
		var appGrid = Ext.getCmp('productionmaingrid');
		appGrid.store.reload({});
	}

	/* details handler */
	function onDetails(btn, ev)
	{		 
		/* server parameters are sent to the server */
		var serverParams = new Object();
		var gridObj = Ext.getCmp('productionmaingrid');
		var selRecords = gridObj.selModel.getSelections();

		serverParams['id'] = selRecords[0].data.id;
		serverParams['ref'] = '{/literal}{$ref}{literal}';
		serverParams['orderid'] = selRecords[0].data.orderid;
		serverParams['langcode'] = languagecode;
		serverParams['status'] = selRecords[0].data.status;
		serverParams['statusid'] = selRecords[0].data.statusid;
		serverParams['itemactivestatus'] = selRecords[0].data.orderstatus;
		serverParams['measurementunit'] = measurementunit;

        if (!orderDetailsWindowExists)
		{
			orderDetailsWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProduction.orderDetailsDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}	

	/* details handler */
	function onConfirmPayment(btn, ev)
	{		 
		/* server parameters are sent to the server */
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('productionmaingrid'));

		serverParams['id'] = id;

        if (!confirmPaymentWindowExists)
		{
			confirmPaymentWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProduction.confirmPaymentDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}		

	/* preferences handler */
	function onPreferences(btn, ev)
	{		 
        if (!preferencesWindowExists)
		{
			preferencesWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProduction.preferencesDisplay&ref={/literal}{$ref}{literal}', [], '', 'initialize', false);
		}
	}

	function onStatusChange()
	{
		var appGrid = Ext.getCmp('productionmaingrid');

		searchPlugin.field.setValue('');

		// get production status filter value
		var productionStatusObj = Ext.getCmp('itemstatus');
		var selectedVal = parseInt(productionStatusObj.getValue())

		appGrid.store.baseParams['itemstatusonhold'] = 0;
		appGrid.store.baseParams['orderstatuswaitingforpayment'] = 0;
		appGrid.store.baseParams['itemactivestatus'] = 0;
		appGrid.store.baseParams['itemstatus'] = '';
		appGrid.store.baseParams['datelastmodified'] = 0;
		appGrid.store.baseParams['query'] = '';
		
		if (selectedVal >= 100)
		{
			switch(selectedVal)
			{
				case 100:
					appGrid.store.baseParams['itemstatusonhold'] = 1;
					break;
				case 200:
					appGrid.store.baseParams['orderstatuswaitingforpayment'] = 1;
					break;
				case 300:
					appGrid.store.baseParams['itemactivestatus'] = 1
					break;
				case 400:
					appGrid.store.baseParams['itemactivestatus'] = 2
					break;
			}
		}
		else
		{
			appGrid.store.baseParams['itemstatus'] = productionStatusObj.getValue();
		}

		appGrid.store.reload({params: appGrid.store.baseParams});
		appGrid.store.baseParams['datelastmodified'] = datelastmodified;
	}

	function onSiteChange()
	{
		var appGrid = Ext.getCmp('productionmaingrid');

		// get production status filter value
		var productionSiteObj = Ext.getCmp('owner');
		if (productionSiteObj)
		{
			productionSiteObj = productionSiteObj.getValue();
		}

		appGrid.store.baseParams['owner'] = productionSiteObj;
		appGrid.store.baseParams['datelastmodified'] = 0;

		appGrid.store.reload({params: appGrid.store.baseParams});
		appGrid.store.baseParams['datelastmodified'] = datelastmodified;
	}

	function onSelectionChange()
	{
		var selectedItems = gridCheckBoxSelectionModelObj.getSelections();
		var selectionCount = gridCheckBoxSelectionModelObj.getCount();
		var canCancel = true;
		var contextMode = '';
		var lastContextMode = '';
		var diffStatus = false;
		var canHoldOrConfirm = true;

		//Disable all first before deciding what to display
		grid.confirmPaymentButton.disable();
		grid.holdButton.disable();
		grid.detailsButton.disable();
		grid.cancelButton.disable();
		grid.contextButton.disable();

		{/literal}{if $defaultowner != '**ALL**' || $optionms == 'false'}{literal}
		
		for (var rec = 0; rec < selectedItems.length; rec++)
		{
			var itemStatus = -1;
			var statusid = -1;
			var expired = '';
			contextMode = '';

			itemStatus = (selectedItems[rec].data.orderstatus);
			statusid = (selectedItems[rec].data.statusid);
			expired = (selectedItems[rec].data.expired);

			if (itemStatus != TPX_ORDER_STATUS_IN_PROGRESS)
			{
				canCancel = false;
				canHoldOrConfirm = false;
			}

			if (expired == 'true')
			{
				canHoldOrConfirm = false;
			}

			switch(parseInt(statusid))
			{
				case TPX_ITEM_STATUS_PRINTED:
					contextMode = 'FINISH';
					break;
				
				case TPX_ITEM_STATUS_FINISHING_COMPLETE:
					contextMode = 'SHIP';
					break;

				case TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER:
				case TPX_ITEM_STATUS_SHIPPED_TO_DISTRIBUTION_CENTRE:
				case TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_DISTRIBUTION_CENTRE:
				case TPX_ITEM_STATUS_SHIPPED_TO_STORE_FROM_DISTRIBUTION_CENTRE:
				case TPX_ITEM_STATUS_SHIPPED_TO_STORE_DIRECTLY:
				case TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_STORE:
				case TPX_ITEM_STATUS_SHIPPED_COLLECTED_BY_CUSTOMER:
					contextMode = 'COMPLETE';
					break;
			}

			switch(parseInt(itemStatus))
			{
				case TPX_ORDER_STATUS_CANCELLED:
				case TPX_ORDER_STATUS_COMPLETED:
					contextMode = 'ACTIVATE';
					break;
			}

			if ((rec > 0) && (contextMode != lastContextMode))
			{
				diffStatus = true;
			}

			lastContextMode = contextMode;
		}

		if (diffStatus)
		{
			contextMode = '';
			grid.contextButton.disable();
		}

		if (selectionCount >= 1)
		{
			if (canHoldOrConfirm)
			{
				grid.confirmPaymentButton.enable();
				grid.holdButton.enable();
			}

			grid.detailsButton.disable();

			if (canCancel)
			{
				grid.cancelButton.enable();
			}

			if (contextMode != '')
			{
				grid.contextButton.enable();

				switch (contextMode)
				{
					case 'ACTIVATE':
						grid.contextButton.setText("{/literal}{#str_ButtonActivateOrder#}{literal}");
						grid.contextButton.handler = onActiveStatus;
						grid.contextButton.setIconClass('silk-control-play-blue');
						ActiveStatus = TPX_ORDER_STATUS_IN_PROGRESS;
						break;
					
					case 'COMPLETE':
						grid.contextButton.setText("{/literal}{#str_ButtonCompleteOrder#}{literal}");
						grid.contextButton.handler = onActiveStatus;
						grid.contextButton.setIconClass('silk-tick');
						ActiveStatus = TPX_ORDER_STATUS_COMPLETED;
						break;

					case 'FINISH':
						grid.contextButton.setText("{/literal}{#str_ButtonFinishOrder#}{literal}");
						grid.contextButton.handler = onOrderStatus;
						grid.contextButton.setIconClass('silk-page-white-star');
						OrderStatus = TPX_ITEM_STATUS_FINISHING_COMPLETE;
						break;

					case 'SHIP':
						grid.contextButton.setText("{/literal}{#str_ButtonShip#}{literal}");
						grid.contextButton.handler = onShip;
						grid.contextButton.setIconClass('silk-lorry');
						OrderStatus = TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER;
						break;
				}
			}

			if (selectionCount == 1)
			{
				grid.detailsButton.enable();
			}
		}
		else
		{
			grid.confirmPaymentButton.disable();
			grid.holdButton.disable();
			grid.detailsButton.disable();
			grid.cancelButton.disable();
			grid.contextButton.disable();
		}
		{/literal}{else}{literal}
			if (selectionCount == 1)
			{
				grid.detailsButton.enable();
			}
			else
			{
				grid.detailsButton.disable();
				
			}
		{/literal}{/if}{literal}
	}

	var gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function() { onSelectionChange(); }
		}
	});

	gWarningPanel = new Ext.Panel({
		id: 'warningpanel',
		style: "width:inherit",
		items: [
			{
				xtype: 'panel',
				flex: true,
				ctCls: "warning-bar",
				tpl: new Ext.XTemplate('<tpl for="."><div class="warning-message">{error}</div></tpl>'),
				data: [{error: "{/literal}{#str_LabelProductionMaxRecords#}{literal}".replace("^0", gridPageSize)}]
			}
		]
	});

	var gridDataStoreObj = new Ext.data.GroupingStore(
		{
			remoteSort: false,
			proxy: new Ext.data.HttpProxy({
				url: 'index.php?fsaction=AdminProduction.getListData&ref={/literal}{$ref}{literal}', 
				method: 'POST'
			}),
			reader: new Ext.taopix.PagedArrayReader(
			{
				idIndex: 0
			},
			Ext.data.Record.create(
			[
				{name: 'id', mapping: 0},
				{name: 'orderdate', mapping: 1},
				{name: 'ordernumber', mapping: 2},
				{name: 'contactname', mapping: 3},
				{name: 'productname', mapping: 4},
				{name: 'qty', mapping: 5},
				{name: 'paymentreceived', mapping: 6},
				{name: 'projectname', mapping: 7},
				{name: 'status', mapping: 8},
				{name: 'source', mapping: 9},
				{name: 'accountcode', mapping: 10},
				{name: 'brandcode', mapping: 11},
				{name: 'companyname', mapping: 12},
				{name: 'covername', mapping: 13},
				{name: 'dataformat', mapping: 14},
				{name: 'temporderexpirydate', mapping: 15},
				{name: 'filesreceivedtimestamp', mapping: 16},
				{name: 'groupcode', mapping: 17},
				{name: 'papername', mapping: 18},
				{name: 'productdimensions', mapping: 19},
				{name: 'orderlinenumber', mapping: 20},
				{name: 'productoutputformatcode', mapping: 21},
				{name: 'uploadmethod', mapping:22},
				{name: 'uploadref', mapping: 23},
				{name: 'orderid', mapping: 24},
				{name: 'orderstatus', mapping: 25},
				{name: 'statusid', mapping: 26},
				{name: 'expired', mapping: 27},
				{name: 'onhold', mapping: 28}
			])),
			sortInfo: 
			{
				field: 'orderdate',
				direction: "DESC"
			},
			listeners:
			{
				'load': onSelectionChange
			},
			baseParams: {	csrf_token: Ext.taopix.getCSRFToken(),
							langcode:languagecode, 
							owner:'{/literal}{$defaultowner}{literal}',
							clusternodecount: 1,
							clusternodeindex: 1,
							ordernumber: '',
							orderstatuswaitingforpayment: 0,
							itemstatus: '',
							itemactivestatus: 0,
							itemstatusonhold: 0,
							searchstring: '',
							datelastmodified: 0,
							queuecount: 0,
							limit:gridPageSize,
							measurementunit: measurementunit
						}
		});

	var gridColumnModelObj = new Ext.grid.ColumnModel(
		{
			defaults: {
				sortable: true,
				resizable: true
			},
			columns: [
				gridCheckBoxSelectionModelObj,
				{header: "{/literal}{#str_LabelProductionOrderDate#}{literal}", renderer: dateColumnRenderer, width: 100, dataIndex: 'orderdate', sortType: 'asDate'},
				{header: "{/literal}{#str_LabelProductionOrderNumber#}{literal}", renderer: generalColumnRenderer, width: 150, dataIndex: 'ordernumber'},
				{header: "{/literal}{#str_LabelProductionContactName#}{literal}", renderer: generalColumnRenderer, width: 225, dataIndex: 'contactname'},
				{header: "{/literal}{#str_LabelProductionProductName#}{literal}", renderer: generalColumnRenderer, width: 300, dataIndex: 'productname'},
				{header: "{/literal}{#str_LabelProductionQty#}{literal}", renderer: generalColumnRenderer, width: 100, dataIndex: 'qty'},
				{header: "{/literal}{#str_LabelProductionPaymentConfirmed#}{literal}", renderer: generalColumnRenderer, width: 100, dataIndex: 'paymentreceived'},
				{header: "{/literal}{#str_LabelProductionProjectName#}{literal}", renderer: generalColumnRenderer, width: 250, dataIndex: 'projectname'},
				{header: "{/literal}{#str_LabelProductionExpiryDate#}{literal}", renderer: dateColumnRenderer, width: 100, dataIndex: 'temporderexpirydate', sortType: 'asDate'},
				{header: "{/literal}{#str_LabelStatus#}{literal}", renderer: generalColumnRenderer, width: 250, dataIndex: 'status'},
				{header: "{/literal}{#str_LabelProductionSource#}{literal}", renderer: generalColumnRenderer, width: 100, dataIndex: 'source'},

				//by default hide these, user preferences can show them
				{header: "{/literal}{#str_LabelAccountCode#}{literal}", hidden:true, renderer: generalColumnRenderer, width: 100, dataIndex: 'accountcode'},
				{header: "{/literal}{#str_LabelWebBrandCode#}{literal}", hidden:true, renderer: generalColumnRenderer, width: 200, dataIndex: 'brandcode'},
				{header: "{/literal}{#str_LabelCompanyName#}{literal}", hidden:true, renderer: generalColumnRenderer, width: 200, dataIndex: 'companyname'},
				{header: "{/literal}{#str_LabelProductionCoverName#}{literal}", hidden:true, renderer: generalColumnRenderer, width: 250, dataIndex: 'covername'},
				{header: "{/literal}{#str_LabelProductionDataFormat#}{literal}", hidden:true, renderer: generalColumnRenderer, width: 100, dataIndex: 'dataformat'},
				{header: "{/literal}{#str_LabelProductionFilesReceivedDate#}{literal}", hidden:true, renderer: dateColumnRenderer, width: 100, dataIndex: 'filesreceivedtimestamp', sortType: 'asDate'},
				{header: "{/literal}{#str_LabelProductionLicenseKeyCode#}{literal}", hidden:true, renderer: generalColumnRenderer, width: 200, dataIndex: 'groupcode'},
				{header: "{/literal}{#str_LabelProductionPaperName#}{literal}", hidden:true, renderer: generalColumnRenderer, width: 200, dataIndex: 'papername'},
				{header: "{/literal}{#str_LabelProductionProductDimensions#}{literal}", hidden:true, renderer: generalColumnRenderer, width: 150, dataIndex: 'productdimensions'},
				{header: "{/literal}{#str_LabelProductionOrderLineID#}{literal}", hidden:true, renderer: generalColumnRenderer, width: 100, dataIndex: 'orderlinenumber', sortType: 'asInt'},
				{header: "{/literal}{#str_LabelProductionOutputFormat#}{literal}", hidden:true, renderer: generalColumnRenderer, width: 100, dataIndex: 'productoutputformatcode'},
				{header: "{/literal}{#str_LabelProductionUploadMethod#}{literal}", hidden:true, renderer: generalColumnRenderer, width: 100, dataIndex: 'uploadmethod'},
				{header: "{/literal}{#str_LabelProductionUploadRef#}{literal}", hidden:true, renderer: generalColumnRenderer, width: 250, dataIndex: 'uploadref'},
				{header: "orderid", hidden:true, dataIndex: 'orderid'}
			]
		});

	var sitesDropdown = new Ext.form.ComboBox({
		id: 'owner',
		name: 'owner',
		width:200,
		labelWidth: 150,
		maxHeight:400,
		fieldLabel: "{/literal}{#str_LabelProductionSite#}{literal}",
		mode: 'local',
		editable: false,
		hideLabel: false,
		forceSelection: true,
		selectOnFocus: true,
		triggerAction: 'all',
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
		{/literal}{if $defaultowner != '**ALL**'}{literal}
			disabled: true,
		{/literal}{/if}{literal}
		value: '{/literal}{$defaultowner}{literal}',
		allowBlank: false,
		post: true,
		listeners: {
			'select': onSiteChange,
			'render':function()
			{
				this.setValue(0);
            }
        }
	});
		
	var statusDropdown = new Ext.form.ComboBox({
		id: 'itemstatus',
		name: 'itemstatus',
		width:200,
		maxHeight:400,
		mode: 'local',
		editable: false,
		fieldLabel: "{/literal}{#str_LabelStatus#}{literal}",
		hideLabel: false,
		forceSelection: true,
		selectOnFocus: true,
		triggerAction: 'all',
		tpl: '<tpl for="."><div class="x-combo-list-item">{name}</div><tpl if="xindex == 10 || xindex == 12 || xindex == 14"><hr /></tpl></tpl>',
		store: new Ext.data.ArrayStore({
			id: 0,
			fields: ['id', 'name'],
			data:
			[
				{/literal}
				{section name=index loop=$statuslist}
					{if $smarty.section.index.last}
						["{$statuslist[index].id}", "{$statuslist[index].name}"]
					{else}
						["{$statuslist[index].id}", "{$statuslist[index].name}"],
					{/if}
				{/section}
				{literal}
			]
		}),
		valueField: 'id',
		displayField: 'name',
		useID: true,
		allowBlank: false,
		post: true,
		listeners: {
			'select': onStatusChange,
			'render':function()
			{
				this.setValue(0);
            }
        }
	});	

	var setPagingItemsVisible = function (pt, visible, hideMode) {
		var method = visible ? "show" : "hide";

		pt.items.each(function (item, index) {
			if (index < 11 && item.tooltip != 'Refresh') { 
				item.hideMode = hideMode; 
				item[method]();
				item.destroy();
			} else {
				return false; 
			}
		});
			
		if (pt.displayItem) {
			pt.displayItem.destroy();
		}

	};

	var PagingToolbar1 = new Ext.PagingToolbar({ pageSize: gridPageSize, store: gridDataStoreObj, displayInfo: true });
	setPagingItemsVisible(PagingToolbar1, false, "visibility");

	var searchPlugin = new Ext.ux.grid.Search({
		iconCls: 'silk-zoom',
		minChars: 3,
		width: 230,
		autoFocus: true,
		onClearFunc: onClear,
		onSearchFunc: onSearch,
		disableIndexes:	[
							'id', 'orderdate', 'ordernumber', 'contactname', 'productname', 'qty', 'paymentreceived',
							'projectname', 'status', 'source', 'accountcode', 'brandcode', 'companyname', 'covername', 'dataformat', 
							'temporderexpirydate', 'filesreceivedtimestamp', 'groupcode', 'papername', 'productdimensions', 'orderlinenumber', 
							'productoutputformatcode', 'uploadmethod', 'uploadref', 'orderid', 'orderstatus', 'statusid'
						]
	});

	var tbar1 = new Ext.Toolbar({
		items: [gWarningPanel],
		layout: 'column'
	});

	var grid = new Ext.grid.GridPanel(
		{
			id: 'productionmaingrid',
			store: gridDataStoreObj,
			cm: gridColumnModelObj,
			enableColLock: false,
			draggable: false,
			enableColumnHide: false,
			enableColumnMove: false,
			enableHdMenu: false,
			trackMouseOver: false,
			stripeRows: true,
			columnLines: true,
			selModel: gridCheckBoxSelectionModelObj,
			ctCls: 'grid',
			anchor: '100% 100%',
			plugins: [
				searchPlugin
			],
			tbar: [
				{/literal}{if $optionms == 'true'}{literal}
				{xtype: 'tbspacer', width: 2},
				new Ext.form.Label({text:'{/literal}{#str_LabelProductionSite#}{literal}'})
				,{xtype: 'tbspacer', width: 5},
				sitesDropdown, {xtype: 'tbspacer', width: 10},
				{/literal}{/if}{literal}
				new Ext.form.Label({text:'{/literal}{#str_LabelStatus#}{literal}'})
				,{xtype: 'tbspacer', width: 5},
				statusDropdown, {xtype: 'tbspacer', width: 10},
				{
					ref: '../detailsButton',
					text: '{/literal}{#str_ButtonDetails#}{literal}',
					iconCls: 'silk-application-form-magnify',
					handler: onDetails,
					disabled: true
				}
				,'-',
				{
					ref: '../holdButton',
					text: '{/literal}{#str_ButtonHold#}{literal}',
					iconCls: 'silk-stop',
					handler: onHold,
					disabled: true
				},'-', 
				{
					ref: '../confirmPaymentButton',
					text: '{/literal}{#str_ButtonConfirmPayment#}{literal}',
					iconCls: 'silk-money',
					handler: onConfirmPayment,
					disabled: true
				},'-',
				{
					ref: '../contextButton',
					text: '{/literal}{#str_ButtonActivateOrder#}{literal}',
					iconCls: 'silk-control-play-blue',
					disabled: true
				}
				, '-',
				{
					ref: '../cancelButton',
					text: '{/literal}{#str_ButtonCancelOrder#}{literal}',
					iconCls: 'silk-cancel',
					handler: onCancel,
					disabled: true
				}, '-',
				{
					ref: '../refreshButton',
					text: '{/literal}{#str_ExtJsPagingToolbarRefresh#}{literal}',
					iconCls: 'silk-arrow-refresh',
					handler: gridReload,
					disabled: false
				},
				{xtype:'tbfill'},
				{
					ref: '../preferencesButton',
					iconCls: 'silk-cog',
					handler: onPreferences,
					disabled: false
				}
			],
			bbar: PagingToolbar1,
			listeners : {
				'render' : function() { tbar1.render(grid.tbar) }
			}
		});
	
	function gridReload() 
	{
		gridDataStoreObj.baseParams['datelastmodified'] = datelastmodified;
		gridDataStoreObj.reload({});	
	}

	gridReload();
	
	gMainWindowObj = new Ext.Panel(
		{
			id: 'MainWindow',
			title: "{/literal}{#str_LabelProduction#}{literal}",
			items: [grid],
			layout: 'anchor',
			anchor: '100% 100%',
			tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose();  accordianWindowInitialized = false;}, qtip: '{/literal}{#str_LabelCloseWindow#}{literal}' }],
		});

	centreRegion.add(gMainWindowObj);
	centreRegion.doLayout();
	Ext.getCmp('owner').setValue('{/literal}{$defaultowner}{literal}');
	setColumnDisplay();
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
