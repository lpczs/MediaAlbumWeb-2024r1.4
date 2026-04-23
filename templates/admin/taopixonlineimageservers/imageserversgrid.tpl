{literal}

function initialize(pParams)
{	
	volumesWindowExists = false;
	serverEditWindowExists = false;

	function volumesHandler(btn, ev)
	{
		if(!volumesWindowExists)
		{
			volumesWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminTaopixOnlineVolumesAdmin.initialize&ref={/literal}{$ref}{literal}', '', '', 'initialize', false);
		}
	}
	
	function onServerAdd(btn, ev)
	{	
		if(!serverEditWindowExists)
		{
			serverEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminTaopixOnlineImageServersAdmin.adddisplay&ref={/literal}{$ref}{literal}', '', '', 'initialize', false);
		}
	}
	
	function onServerEdit(btn, ev)
	{	
		if(!serverEditWindowExists)
		{
			var gridObj = Ext.getCmp('maingrid');
			var selRecords = gridObj.selModel.getSelections();
			var selectedServerID = selRecords[0].data.id;
			
			serverEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminTaopixOnlineImageServersAdmin.editdisplay&ref={/literal}{$ref}{literal}&serverid=' + selectedServerID, '', '', 'initialize', false);
		}
	}
	
	function onActivate(btn, ev)
	{ 
		/* server parameters are sent to the server */
		var serverParams = new Object();
		serverParams['ids'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('maingrid'));	
		var active = 0; 

		switch (btn.id)
		{
			case 'imageServerActiveButton':
			{
				active = 1;
				break;
			}
			case 'imageServerInactiveButton':
			{
				active = 0;
				break;
			}
		}
		
		serverParams['active'] = active;

		Ext.taopix.formPost(gMainWindowObj, serverParams, 'index.php?fsaction=AdminTaopixOnlineImageServersAdmin.activate&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageUpdating#}{literal}", updateImagaeServerGridCallback);	
	}

	function onDelete(btn, ev)
	{
		var message = "{/literal}{#str_MessageDeleteImageServer#}{literal}";
		
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", message, onDeleteResult);
	}

	function onDeleteResult(btn)
	{
		if (btn == "yes")
		{
			/* server parameters are sent to the server */
			var serverParams = new Object();
			serverParams['ids'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('maingrid'));	

			Ext.taopix.formPost(gMainWindowObj, serverParams, 'index.php?fsaction=AdminTaopixOnlineImageServersAdmin.delete&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageUpdating#}{literal}", updateImagaeServerGridCallback);
		}
	}

	function updateImagaeServerGridCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated == true)
		{
			var gridObj = Ext.getCmp('maingrid');
			var dataStore = gridObj.store;
		
			dataStore.load();
		}
	}
	
	function localTimeColumnRenderer(value, p, record)
	{
		var className = '';

		if (record.data.active == 0)
		{
			className = ' class="inactive"';
		}
		
		if (value != '0000-00-00 00:00:00')
		{
			var parsedDateArray = value.split(/[- :]/);

			// Apply each element to the Date function
			var date = new Date(Date.UTC(parsedDateArray[0], parsedDateArray[1]-1, parsedDateArray[2], parsedDateArray[3], parsedDateArray[4], parsedDateArray[5]));

			value = date.format("d-m-Y H:i:s");
		}

		return '{/literal}<span'+className+'>'+value+'</span>{literal}';
	}

	function generalColumnRenderer(value, p, record)
	{
		if (record.data.active == 0)
		{
			className =  'class = "inactive"';
			return '{/literal}<span '+className+'>'+value+'</span>{literal}';
		}
		else
		{
			return '{/literal}<span class="">'+value+'</span>{literal}';
		}	
	}
	
	function activeRenderer(value, p, record)
	{
		if (value == 0)
		{
			className =  'class = "inactive"';
			return '{/literal}<span '+className+">{#str_LabelInactive#}</span>{literal}";
		}
		else
		{
			return "{/literal}{#str_LabelActive#}{literal}";
		}	
	}
	
	function pingColumnRenderer(value, p, record)
	{
		className = 'class = ""';
		
		if (record.data.active == 0)
		{
			className =  'class = "inactive"';
		}

		var pingStatus = '';

		if (record.data.lastconnection == '0000-00-00 00:00:00')
		{
			pingStatus = '';
		}
		else if (record.data.ping == 0)
		{
			pingStatus = '{/literal}{#str_LabelUnreachable#}{literal}';
		}
		else
		{
			pingStatus = '{/literal}{#str_LabelAlive#}{literal}';
		}

		return '<span ' + className + '>' + pingStatus + '</span>';

	}
	
	var gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj) 
			{
				var selectionCount = gridCheckBoxSelectionModelObj.getCount();
				
				if (selectionCount == 1)
				{
					grid.editButton.enable();
					grid.volumesButton.enable();
					grid.imageServerActiveButton.enable();
					grid.imageServerInactiveButton.enable();
				}
				else
				{
					grid.editButton.disable();
					grid.volumesButton.disable();
					grid.imageServerActiveButton.disable();
					grid.imageServerInactiveButton.enable();
				}

				grid.deleteButton.enable();
			}
		}
	});
			
	var gridDataStoreObj = new Ext.data.Store({
		remoteSort: true, 
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminTaopixOnlineImageServersAdmin.getgriddata&ref={/literal}{$ref}{literal}'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
		    {name: 'id', mapping: 0},
			{name: 'code', mapping: 1},
			{name: 'url', mapping: 2},
			{name: 'preference', mapping: 3},
			{name: 'lastconnection', mapping: 4},
			{name: 'lastsuccess', mapping: 5},
			{name: 'ping', mapping: 6},
			{name: 'error', mapping: 7},
			{name: 'active', mapping: 8},
			{name: 'errormsg', mapping: 9}
			])
		),
		listeners:
		{
			'load': function(store, records, successful, eOpts)
			{
				if ((records.length == 1) && (records[0].data['errormsg'] != ''))
				{
					grid.addButton.disable();

					Ext.MessageBox.show(
					{
						title: "{/literal}{#str_TitleWarning#}{literal}", 
						msg: records[0].data['errormsg'],
						buttons: Ext.MessageBox.OK,
						icon: Ext.MessageBox.WARNING
					});

					store.removeAll();
				}
				else
				{
					grid.addButton.enable();
				}
			}
		}
	});
		
	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
			sortable: false, 
			resizable: true
		},
		columns: [
			gridCheckBoxSelectionModelObj,
			{header: "{/literal}{#str_LabelCode#}{literal}", width: 100, dataIndex: 'code', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelServerURL#}{literal}", width: 300, dataIndex: 'url', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelPreference#}{literal}", width: 80, dataIndex: 'preference', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelLastConnection#}{literal}", width: 150, dataIndex: 'lastconnection', renderer: localTimeColumnRenderer},
			{header: "{/literal}{#str_LabelLastSuccess#}{literal}", width: 150, dataIndex: 'lastsuccess', renderer: localTimeColumnRenderer},
			{header: "{/literal}{#str_LabelPing#}{literal}", width: 80, dataIndex: 'ping', renderer: pingColumnRenderer},
			{header: "{/literal}{#str_LabelErrorMessage#}{literal}", width: 100, dataIndex: 'error', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelActive#}{literal}", width: 80, dataIndex: 'active', renderer: activeRenderer}
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
				ref: '../addButton',
				text: "{/literal}{#str_ButtonAdd#}{literal}",
				iconCls: 'silk-add',
				handler: onServerAdd,
				disabled: true
			}, '-', 
			{
				ref: '../editButton',
				text: "{/literal}{#str_ButtonEdit#}{literal}",
				iconCls: 'silk-pencil',
				handler: onServerEdit,
				disabled: true
			}
			,'-', {
				ref: '../deleteButton',
				text: "{/literal}{#str_ButtonDelete#}{literal}",
				iconCls: 'silk-delete',
				handler: onDelete,
				disabled: true
			},'-',
			{
				id:'imageServerActiveButton',
				ref: '../imageServerActiveButton', 
				text: "{/literal}{#str_LabelMakeActive#}{literal}", 
				iconCls: 'silk-lightbulb',
				disabled: true,
				handler: onActivate			
			}, '-',
			{
				id:'imageServerInactiveButton', 
				ref: '../imageServerInactiveButton', 
				text: "{/literal}{#str_LabelMakeInactive#}{literal}", 
				iconCls: 'silk-lightbulb-off',
				disabled: true,
				handler: onActivate
			
			}, '-',
			{ 
                id: 'volumesButton',
				ref: '../volumesButton', 
				text: "{/literal}{#str_TitleVolumes#}{literal}", 
				iconCls: 'silk-volumes',
				handler: volumesHandler,
				disabled:true
			}
		]
	});
	gridDataStoreObj.load();

	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_SectionTitleServerManagement#}{literal}",
		items: grid,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose(); accordianWindowInitialized = false}, qtip: "{/literal}{#str_LabelCloseWindow#}{literal}" }],
		baseParams: { ref: "{/literal}{$ref}{literal}" }
	});
		
	centreRegion.add(gMainWindowObj);
	centreRegion.doLayout();
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
