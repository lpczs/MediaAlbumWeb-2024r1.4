{literal}

function initialize(pParams)
{	
	paymentMethodsEditWindowExists = false;

	var gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj) 
			{
				var selectionCount = gridCheckBoxSelectionModelObj.getCount();
				
				if (selectionCount == 1)
				{
					grid.editButton.enable();
				}
				else
				{
					grid.editButton.disable();
				}
				
				if (selectionCount > 0)
				{
					grid.activeButton.enable();
					grid.inactiveButton.enable();
				}
				else
				{
					grid.activeButton.disable();
					grid.inactiveButton.disable();
				}
															
				if (grid)
				{
					var selRecords = grid.getSelectionModel().getSelections();
				}
			}
		}
	});
			
	var gridDataStoreObj = new Ext.data.Store({
		remoteSort: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminPaymentMethods.getGridData&ref={/literal}{$ref}{literal}'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([	                        
		    {name: 'id', mapping: 0},
			{name: 'code', mapping: 1},
			{name: 'name', mapping: 2},
			{name: 'availablewhenshipping', mapping: 3},
			{name: 'availablewhennotshipping', mapping: 4},
			{name: 'active', mapping: 5}
			])
		),
		sortInfo:{field: 'code', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});
		
	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
			sortable: false, 
			resizable: true
		},
		columns: [
			gridCheckBoxSelectionModelObj,
			{header: "{/literal}{#str_LabelCode#}{literal}", width: 200, dataIndex: 'code', renderer: generalColumnRenderer },
			{header: "{/literal}{#str_LabelName#}{literal}", width: 300, dataIndex: 'name', renderer: generalColumnRenderer },
			{header: "{/literal}{#str_LabelAvailableWhenShipping#}{literal}", renderer: YesNoColumnRenderer, width: 200, dataIndex: 'availablewhenshipping', align:'right'},
			{header: "{/literal}{#str_LabelAvailableWhenNotShipping#}{literal}", renderer: YesNoColumnRenderer, width: 200, dataIndex: 'availablewhennotshipping', align:'right'},
			{header: "{/literal}{#str_LabelStatus#}{literal}", renderer: logicColumnRenderer, width: 150, dataIndex: 'active', align:'right'}
		]
	});

	var grid = new Ext.grid.GridPanel({
		id: 'maingrid',
		store: gridDataStoreObj,
		cm: gridColumnModelObj,
		enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		enableHdMenu:false,
		trackMouseOver:false,
		stripeRows:true,
		columnLines:true,
		autoExpandColumn: 2,
		ctCls: 'grid',
		selModel: gridCheckBoxSelectionModelObj,
		tbar: [
			{
				ref: '../editButton',
				text: "{/literal}{#str_ButtonEdit#}{literal}",
				iconCls: 'silk-pencil',
				handler: onEdit,
				disabled: true
			}
			,'-',
			{ 
				id:'activeButton',
				ref: '../activeButton', 
				text: "{/literal}{#str_LabelMakeActive#}{literal}", 
				iconCls: 'silk-lightbulb',
				handler: onActivate, 
				disabled: true
			}, '-',
			{ 
				id:'inactiveButton', 
				ref: '../inactiveButton', 
				text: "{/literal}{#str_LabelMakeInactive#}{literal}", 
				iconCls: 'silk-lightbulb-off',
				handler: onActivate, 
				disabled: true	
			}
			]
	});
	
	gridDataStoreObj.load();
	
	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_LabelPaymentMethods#}{literal}",
		items: grid,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose(); accordianWindowInitialized = false;}, qtip: '{/literal}{#str_LabelCloseWindow#}{literal}' }],
		baseParams: { ref: '{/literal}{$ref}{literal}' }
	});
		
	centreRegion.add(gMainWindowObj);
	centreRegion.doLayout();
	
}


function generalColumnRenderer(pValue,pP,pRecord)
{
	if (pRecord.data.active == 0)
	{
		return '<span class="inactive">' + pValue + '</span>';
	}
	else
	{
		return pValue;
	}
}

function logicColumnRenderer(pValue, pP, pRecord)
{
	if (pValue == 0)
	{
		pValue = "{/literal}{#str_LabelInactive#}{literal}";
	}
	else
	{
		pValue = "{/literal}{#str_LabelActive#}{literal}";
	}

	if (pRecord.data.active == 0)
	{
		return '<span class="inactive">' + pValue + '</span>';
	}
	else
	{
		return pValue;
	}
}


function YesNoColumnRenderer(pValue, pP, pRecord)
{
	if (pValue == 0)
	{
		pValue = "{/literal}{#str_LabelNo#}{literal}";
	}
	else
	{
		pValue = "{/literal}{#str_LabelYes#}{literal}";
	}

	if (pRecord.data.active == 0)
	{
		return '<span class="inactive">' + pValue + '</span>';
	}
	else
	{
		return pValue;
	}
}

function onActivate(btn, ev)
{ 
	/* server parameters are sent to the server */
	var serverParams = new Object();
	serverParams['ids'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));	
	var active = 0; 

	switch (btn.id)
	{
	case 'activeButton':
		active = 1;
		break;
	case 'inactiveButton':
		active = 0;
		break;
	}
	
	var gridObj = gMainWindowObj.findById('maingrid');
	var selRecords = gridObj.selModel.getSelections();
	var codeList = '';
	
	for (var rec = 0; rec < selRecords.length; rec++) 
	{	
		codeList = codeList + selRecords[rec].data.code;
		
		if (rec != selRecords.length - 1)
		{
			codeList = codeList + ',';
		}	
	}
	
	serverParams['codelist'] = codeList;
	serverParams['active'] = active;

	Ext.taopix.formPost(gMainWindowObj, serverParams, 'index.php?fsaction=AdminPaymentMethods.paymentMethodActivate', "{/literal}{#str_MessageUpdating#}{literal}", activateCallback);	
}

function activateCallback(pUpdated, pActionForm, pActionData)
{
	if (pUpdated)
	{
		var gridObj = gMainWindowObj.findById('maingrid');
		var dataStore = gridObj.store;
	
		Ext.taopix.updateDataStore(dataStore, pActionData.result.data);
	}
}
	
/* edit handler */
function onEdit(btn, ev)
{	 
	/* server parameters are sent to the server */
	var serverParams = new Object();
	var id = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
	serverParams['id'] = id;

	if (!paymentMethodsEditWindowExists)
	{
		paymentMethodsEditWindowExists = true;
		Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminPaymentMethods.editDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
	}
}

function onDeleteCallback(pUpdated, pTheForm, pActionData)
{
	if (pUpdated == true)
	{
		var gridObj = gMainWindowObj.findById('maingrid');
		var dataStore = gridObj.store;
		var selRecords = gridObj.getSelectionModel().getSelections();
		var icon = Ext.MessageBox.INFO;
		var idList = ',' + pActionData.result.idlist + ',';

		for (rec = 0; rec < selRecords.length; rec++)
		{
			/* only delete from selection what has been deleted */
			if (idList.indexOf(',' + selRecords[rec]['id'] + ',') !=-1)
			{
				dataStore.remove(selRecords[rec]);
			}
		}
		
		if (pActionData.result.msg)
		{	
			Ext.MessageBox.show({
				title: pActionData.result.title,
				msg: pActionData.result.msg,
				buttons: Ext.MessageBox.OK,
				icon: icon
			});
		}
		dataStore.load();
	}
}

/* save functions */
function editsaveHandler(btn, ev)
{
	var selectID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
		
	var submitURL = 'index.php?fsaction=AdminPaymentMethods.edit&ref={/literal}{$ref}{literal}&id=' + selectID;
	var fp = Ext.getCmp('paymentMethodsForm'), form = fp.getForm();
	var submit = true;
	var paramArray = new Object();
	paramArray['isactive'] = '';
	paramArray['availablewhenshipping'] = '';
	paramArray['availablewhennotshipping'] = '';
	
	if (Ext.getCmp('availablewhenshipping').checked)
	{
		paramArray['availablewhenshipping'] = '1';
	}
	else
	{
		paramArray['availablewhenshipping'] = '0';
	}
	
	if (Ext.getCmp('availablewhennotshipping').checked)
	{
		paramArray['availablewhennotshipping'] = '1';
	}
	else
	{
		paramArray['availablewhennotshipping'] = '0';
	}
	
	paramArray['isactive'] = '';
	
	if (Ext.getCmp('isactive').checked)
	{
		paramArray['isactive'] = '1';
	}
	else
	{
		paramArray['isactive'] = '0';
	}
	
	if (!Ext.getCmp('langPanel').isValid())
	{
		Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ExtJsErrorNoName#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING});
		submit = false;
	}	
	
	if (submit)
	{
		Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", saveCallback);
	}
}

function saveCallback(pUpdated, pActionForm, pActionData)
{	
	if (pUpdated)
	{
		var gridObj = gMainWindowObj.findById('maingrid');
		var dataStore = gridObj.store;	
		
		gridObj.store.reload();
		gDialogObj.close();
	}
	else
	{
		icon = Ext.MessageBox.WARNING;
				
		Ext.MessageBox.show({
			title: pActionData.result.title,
			msg: pActionData.result.msg,
			buttons: Ext.MessageBox.OK,
			icon: icon
		});
	}
	
}
/* close functions */
function closeHandler(btn, ev)
{
	gDialogObj.close();
}

/* close this window panel */
function windowClose()
{
	centreRegion.remove('MainWindow', true);
	centreRegion.doLayout();
}


{/literal}
