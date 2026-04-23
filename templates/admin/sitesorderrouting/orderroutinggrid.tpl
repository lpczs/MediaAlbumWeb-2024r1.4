{literal}

function initialize(pParams)
{
	orderRoutingEditWindowExists = false;
	
	function togglePriorityRenderer(value, p, record)
	{
		var gridObj = gMainWindowObj.findById('maingrid');
		var dataStore = gridObj.store;
		var index = dataStore.indexOf(record);
		var count = dataStore.getCount();
	
		if (index == 0)
		{
			return '<a href="javascript:toggleClickHandler(' + index + ',\'last\');" onmouseover="self.status=\'\'; return true;"><img src="{/literal}{$webroot}{literal}/utils/ext/images/taopix/taopix_arrow_last.png" /></a><a href="javascript:toggleClickHandler(' + index + ',\'down\');" onmouseover="self.status=\'\'; return true;"><img src="{/literal}{$webroot}{literal}/utils/ext/images/taopix/taopix_arrow_down.png" /></a>';
		}
		else if (index == count - 1)
		{
			return '<a href="javascript:toggleClickHandler(' + index + ',\'up\');" onmouseover="self.status=\'\'; return true;"><img src="{/literal}{$webroot}{literal}/utils/ext/images/taopix/taopix_arrow_up.png" /></a><a href="javascript:toggleClickHandler(' + index + ',\'first\');" onmouseover="self.status=\'\'; return true;"><img src="{/literal}{$webroot}{literal}/utils/ext/images/taopix/taopix_arrow_first.png" /></a>';
		}
		else
		{
			return '<a href="javascript:toggleClickHandler(' + index + ',\'up\');" onmouseover="self.status=\'\'; return true;"><img src="{/literal}{$webroot}{literal}/utils/ext/images/taopix/taopix_arrow_up.png" /></a><a href="javascript:toggleClickHandler(' + index + ',\'down\');" onmouseover="self.status=\'\'; return true;"><img src="{/literal}{$webroot}{literal}/utils/ext/images/taopix/taopix_arrow_down.png" /></a>';	
		}
	}

	function conditionColumnRenderer(value, p, record)
	{
		if (value == 0)
		{
			if(record.data.rule == 4)
		 	{
		 		return '';	
		 	}
		 	else
		 	{
		 		return "{/literal}{#str_LabelIs#}{literal}";
		 	}
		}
		else
		{
			if(record.data.rule == 4)
		 	{
		 		return '';	
		 	}
		 	else
			{
		 		return "{/literal}{#str_LabelIsNot#}{literal}";
		 	}		 
		}
	}

	function siteColumnRenderer(value, p, record)
	{
		if (record.data.sitecode == '' && record.data.rule != 4 )
		{
			return "{/literal}{#str_LabelNone#}{literal}";
		}
		else
		{
			return record.data.sitecode;
		}
	}

	/* add handler */
	function onAdd(btn, ev)
	{		
		if (!orderRoutingEditWindowExists)
		{
			orderRoutingEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminSitesOrderRouting.addDisplay&ref={/literal}{$ref}{literal}', '', '', 'initialize', false);
		}
	}	
	
	/* edit handler */
	function onEdit(btn, ev)
	{
		/* server parameters are sent to the server */
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
		serverParams['id'] = id;
		
		if (!orderRoutingEditWindowExists)
		{
			orderRoutingEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminSitesOrderRouting.editDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}	
	}	
	
	/* delete handler */	  
	function onDelete(btn, ev)
	{
		var gridObj = gMainWindowObj.findById('maingrid');
		var dataStore = gridObj.store;
		dataStore.load();
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_ConfirmationDeleteRule#}{literal}", onDeleteResult);
	}

	function onDeleteResult(btn)
	{
		if (btn == "yes")
		{
			var paramArray = new Object();
			paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
			Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminSitesOrderRouting.delete', "{/literal}{#str_MessageDeleting#}{literal}", onDeleteCallback);	
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
	
	/* column rendering functions */	
	function ruleColumnRenderer(value, p, record)
	{
		switch(value)
		{
			case '0':
				return "{/literal}{#str_LabelBrandCode#}{literal}";
	 			break;
			case '1':
	 			return "{/literal}{#str_LabelLicenseKeyCode#}{literal}";
	 			break;
			case '2':
	 			return "{/literal}{#str_LabelProductCode#}{literal}";
	 			break;
			case '3':
		 		return "{/literal}{#str_LabelShippingCountryCode#}{literal}";
	 			break;
			case '4':
	 			return "{/literal}{#str_LabelVoucherSiteCode#}{literal}";
	 			break;
		}
	}

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
				
				var canDelete = true;
				
				if (grid)
				{
					var selRecords = grid.getSelectionModel().getSelections();
				}

				if ((selectionCount > 0) && (canDelete))
				{
					grid.deleteButton.enable();
				} 
				else
				{
					grid.deleteButton.disable();
				}
			}
		}
	});
			
	var gridDataStoreObj = new Ext.data.Store({
		remoteSort: true, 
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminSitesOrderRouting.getGridData&ref={/literal}{$ref}{literal}'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
		    {name: 'id', mapping: 0},
			{name: 'rule', mapping: 1},
			{name: 'condition', mapping: 2},
			{name: 'value', mapping: 3},
			{name: 'sitecode', mapping: 4},
			{name: 'priority', mapping: 5}
			])
		)
	});
		
	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
			sortable: false, 
			resizable: true
		},
		columns: [
			gridCheckBoxSelectionModelObj,
			{header: "{/literal}{#str_LabelRule#}{literal}", width: 250, renderer: ruleColumnRenderer, dataIndex: 'rule'},
			{header: "{/literal}{#str_LabelCondition#}{literal}", width: 100, renderer: conditionColumnRenderer, dataIndex: 'condition'},
			{header: "{/literal}{#str_LabelValue#}{literal}", width: 300, dataIndex: 'value'},
			{header: "{/literal}{#str_LabelSiteCode#}{literal}", width: 330, renderer: siteColumnRenderer, dataIndex: 'sitecode'},
			{header: "{/literal}{#str_LabelPriority#}{literal}", width: 100, renderer: togglePriorityRenderer, dataIndex: 'priority', align: 'center'}
		]
	});

	var grid = new Ext.grid.GridPanel({
		id: 'maingrid',
		store: gridDataStoreObj,
		cm: gridColumnModelObj,
		enableColLock:false,
		draggable:false,
		width: 950,
		ctCls: 'grid',
		enableColumnHide:false,
		enableColumnMove:false,
		enableHdMenu:false,
		trackMouseOver:false,
		stripeRows:true,
		columnLines:true,
		
		selModel: gridCheckBoxSelectionModelObj,
		tbar: [
			{
				text: "{/literal}{#str_ButtonAdd#}{literal}",
				iconCls: 'silk-add',
				handler: onAdd
			}, '-', 
			{
				ref: '../editButton',
				text: "{/literal}{#str_ButtonEdit#}{literal}",
				iconCls: 'silk-pencil',
				handler: onEdit,
				disabled: true
			}
			,'-', {
				ref: '../deleteButton',
				text: "{/literal}{#str_ButtonDelete#}{literal}",
				iconCls: 'silk-delete',
				handler: onDelete,
				disabled: true
			}, '-'
			]
	});
	
	gridDataStoreObj.load();
	
	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_TitleSitesOrderRouting#}{literal}",
		items: grid,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose(); accordianWindowInitialized = false;}, qtip: '{/literal}{#str_LabelCloseWindow#}{literal}' }],
		baseParams: { ref: '{/literal}{$ref}{literal}' }
	});
		
	centreRegion.add(gMainWindowObj);
	centreRegion.doLayout();
}


function toggleClickHandler(pID, pDirection)
{		
	/* toggle priority handler */
	function toggleCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var gridObj = gMainWindowObj.findById('maingrid');
			var dataStore = gridObj.store;
		
			if (pUpdated)
			{
				var gridObj = gMainWindowObj.findById('maingrid');
				var dataStore = gridObj.store;
					
				if (pActionData.result.updated == '0')
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
			dataStore.load();
		}
	}
	
	var gridObj = gMainWindowObj.findById('maingrid');
	var dataStore = gridObj.store;
	var count = dataStore.getCount();
	var storeId = '';
	for (var rec = 0; rec < count; rec++)
	{
		dataRec = dataStore.getAt(rec);
		storeId = storeId + dataRec.data['id'];	
		
		if(rec != count - 1)
		{
			storeId = storeId + ',';
		}
	}
	
	var paramArray = new Object();
	paramArray['toggleId'] = pID;
	paramArray['direction'] = pDirection;
	paramArray['storeIdList'] = storeId;
		
	Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminSitesOrderRouting.toggle', "{/literal}{#str_MessageUpdating#}{literal}", toggleCallback);	
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
