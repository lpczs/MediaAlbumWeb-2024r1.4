{literal}

function initialize(pParams)
{	
	var gridObj = Ext.getCmp('maingrid');
	var selRecords = gridObj.selModel.getSelections();
	var selectedServerID = selRecords[0].data.id;
	
	volumeEditWindowExists = false;

	
	function onVolumeAdd(btn, ev)
	{	
		if(!volumeEditWindowExists)
		{
			volumeEditWindowExists = true;
			Ext.taopix.loadJavascript(gVolumesDialogObj, '', 'index.php?fsaction=AdminTaopixOnlineVolumesAdmin.adddisplay&ref={/literal}{$ref}{literal}&serverid=' + selectedServerID, '', '', 'initialize', false);
		}
	}
	
	function onVolumeEdit(btn, ev)
	{	
		if(!volumeEditWindowExists)
		{
			var gridObj = Ext.getCmp('volumesgrid');
			var selRecords = gridObj.selModel.getSelections();
			var selectedVolumeID = selRecords[0].data.id;
			
			volumeEditWindowExists = true;
			Ext.taopix.loadJavascript(gVolumesDialogObj, '', 'index.php?fsaction=AdminTaopixOnlineVolumesAdmin.editdisplay&ref={/literal}{$ref}{literal}&volumeid=' + selectedVolumeID + '&serverid='+selectedServerID, '', '', 'initialize', false);
		}
	}
	
	function onActivate(btn, ev)
	{ 
		/* server parameters are sent to the server */
		var serverParams = new Object();
		serverParams['ids'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('volumesgrid'));	
		var active = 0; 

		switch (btn.id)
		{
			case 'volumesActiveButton':
			{
				active = 1;
				break;
			}
			case 'volumesInactiveButton':
			{
				active = 0;
				break;
			}
		}
		
		serverParams['active'] = active;

		Ext.taopix.formPost(gVolumesDialogObj, serverParams, 'index.php?fsaction=AdminTaopixOnlineVolumesAdmin.activate&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageUpdating#}{literal}", updateImagaeServerGridCallback);	
	}
	
	function onDelete(btn, ev)
	{
		var message = "{/literal}{#str_MessageDeleteVolumes#}{literal}";
		
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", message, onDeleteResult);
	}

	function onDeleteResult(btn)
	{
		if (btn == "yes")
		{
			/* server parameters are sent to the server */
			var serverParams = new Object();
			serverParams['ids'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('volumesgrid'));	

			Ext.taopix.formPost(gVolumesDialogObj, serverParams, 'index.php?fsaction=AdminTaopixOnlineVolumesAdmin.delete&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageUpdating#}{literal}", updateImagaeServerGridCallback);
		}
	}
	
	function updateImagaeServerGridCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated == true)
		{
			var gridObj = Ext.getCmp('volumesgrid');
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

	function volumeTypeRenderer(value, p, record)
	{
		className = "";
		if (record.data.active == 0)
		{
			className =  'class = "inactive"';
		}

		if (value == 8)
		{
			return "{/literal}<span "+className+">{#str_LabelVolumeTypeArchive#}</span>{literal}";
		}
		else if (value == 16)
		{
			return "{/literal}<span "+className+">{#str_LabelVolumeTypeSystemResource#}</span>{literal}";
		}
		else if (value == 32)
		{
			return "{/literal}<span "+className+">{#str_LabelVolumeTypeProjectResource#}</span>{literal}";
		}
		else
		{
			return "{/literal}<span "+className+">{#str_LabelVolumeTypeImage#}</span>{literal}";
		}
	}

	function activeRenderer(value, p, record)
	{
		if (value == 0)
		{
			className =  'class = "inactive"';
			return "{/literal}<span "+className+">{#str_LabelInactive#}</span>{literal}";
		}
		else
		{
			return "{/literal}{#str_LabelActive#}{literal}";
		}
	}

	var gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj) 
			{
				var selectionCount = gridCheckBoxSelectionModelObj.getCount();
				
				if (selectionCount == 1)
				{
					volumesGrid.editButton.enable();
					volumesGrid.volumesActiveButton.enable();
					volumesGrid.volumesInactiveButton.enable();
				}
				else
				{
					volumesGrid.editButton.disable();
					volumesGrid.volumesActiveButton.disable();
					volumesGrid.volumesInactiveButton.disable();
				}
				
				volumesGrid.deleteButton.enable();
			}
		}
	});
			
	var gridDataStoreObj = new Ext.data.Store({
		remoteSort: true, 
		groupField:'assettype',
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminTaopixOnlineVolumesAdmin.getgriddata&ref={/literal}{$ref}{literal}&serverid=' + selectedServerID}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
		    {name: 'id', mapping: 0},
			{name: 'code', mapping: 1},
			{name: 'root', mapping: 2},
			{name: 'assettype', mapping: 3},
			{name: 'headroom', mapping: 4},
			{name: 'free', mapping: 5},
			{name: 'size', mapping: 6},
			{name: 'lastupdated', mapping: 7},
			{name: 'preference', mapping: 8},
			{name: 'active', mapping: 9},
			{name: 'errormsg', mapping: 10}
			])
		),
		listeners:
		{
			'load': function(store, records, successful, eOpts)
			{
				if ((records.length == 1) && (records[0].data['errormsg'] != ''))
				{
					volumesGrid.addButton.disable();

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
					volumesGrid.addButton.enable();
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
			{header: "{/literal}{#str_LabelCode#}{literal}", width: 130, dataIndex: 'code', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelRoot#}{literal}", width: 150, dataIndex: 'root', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelVolumeType#}{literal}", width: 120, dataIndex: 'assettype', renderer: volumeTypeRenderer},
			{header: "{/literal}{#str_LabelHeadroom#}{literal}", width: 100, dataIndex: 'headroom', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelFree#}{literal}", width: 100, dataIndex: 'free', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelSize#}{literal}", width: 100, dataIndex: 'size', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelLastUpdated#}{literal}", width: 120, dataIndex: 'lastupdated', renderer: localTimeColumnRenderer},
			{header: "{/literal}{#str_LabelPreference#}{literal}", width: 80, dataIndex: 'preference', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelActive#}{literal}", width: 80, dataIndex: 'active', renderer: activeRenderer}
		]
	});

	var volumesGrid = new Ext.grid.GridPanel({
		id: 'volumesgrid',
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
		height:400,
		ctCls: 'grid',
		selModel: gridCheckBoxSelectionModelObj,
		tbar: [
			{
				ref: '../addButton',
				text: "{/literal}{#str_ButtonAdd#}{literal}",
				iconCls: 'silk-add',
				handler: onVolumeAdd
			}, '-', 
			{
				ref: '../editButton',
				text: "{/literal}{#str_ButtonEdit#}{literal}",
				iconCls: 'silk-pencil',
				handler: onVolumeEdit,
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
				id:'volumesActiveButton',
				ref: '../volumesActiveButton', 
				text: "{/literal}{#str_LabelMakeActive#}{literal}", 
				iconCls: 'silk-lightbulb',
				disabled: true,
				handler: onActivate			
			}, '-',
			{
				id:'volumesInactiveButton', 
				ref: '../volumesInactiveButton', 
				text: "{/literal}{#str_LabelMakeInactive#}{literal}", 
				iconCls: 'silk-lightbulb-off',
				disabled: true,
				handler: onActivate
			}
		]
	});
	gridDataStoreObj.load();
		
	var dialogFormPanelObj = new Ext.FormPanel({
		id: 'volumesGrid',
        labelAlign: 'left',
        labelWidth:20,
        autoHeight: true,
        frame:true,
        layout:'form',
        cls: 'left-right-buttons',
        items: volumesGrid
    });
	
	
	var gVolumesDialogObj = new Ext.Window({
		id: 'volumesDialog',
		closable:false,
		plain:true,
		modal:true,
		draggable:true,
		title: "{/literal}{#str_TitleVolumes#}{literal}",
		resizable:false,
		height: 'auto',
	  	width: 1200,
		tools:[{
			id:'clse',
		    qtip: '{/literal}{#str_LabelCloseWIndow#}{literal}',
		    handler: function(){ gVolumesDialogObj.close(); }
		}],
		items: dialogFormPanelObj,
		listeners: {
		'close': {   
			fn: function(){
		volumesWindowExists = false;
			}
		}
	}
	});
	
	gVolumesDialogObj.show();	 

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
