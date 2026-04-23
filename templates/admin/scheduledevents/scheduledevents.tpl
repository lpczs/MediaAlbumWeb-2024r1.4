{literal}

function initialize(pParams)
{
	var sessionId = "{/literal}{$ref}{literal}";
	var gridPageSize = 100;
	scheduledEventsWindowExists = false;
	
	gridDataStoreObj = new Ext.data.Store({
		remoteSort: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminScheduledEvents.displayList&ref=' + sessionId }),
		method:'POST',
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([ 
				{name:'recordid', mapping: 0},
				{name: 'taskcode', mapping: 1},
				{name: 'runcount', mapping: 2},
				{name: 'maxruncount', mapping: 3}, 
				{name: 'lastruntime', mapping: 4}, 
				{name: 'nextRunTime', mapping: 5},  
				{name: 'status', mapping: 6},
				{name: 'statuscode', mapping: 7},
				{name: 'active', mapping: 8},
				{name: 'priority', mapping: 9}
			])
		),
		sortInfo:{field: 'nextRunTime', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()},
		listeners:
		{
        	'beforeload':function()
        	{ 
				var eventStatusCmb = Ext.getCmp('eventStatus');
				var eventsGrid = Ext.getCmp('eventsGrid');
				if(eventStatusCmb)
				{
					eventsGrid.store.lastOptions.params['eventStatus'] = eventStatusCmb.getValue();
					eventsGrid.store.setBaseParam('eventStatus', eventStatusCmb.getValue());
				}
    		},
    		'load':function()
    		{
    			gridCheckBoxSelectionModelObj.fireEvent('selectionchange', gridCheckBoxSelectionModelObj);
    		}
        }
	}); 
	gridDataStoreObj.load({params:{ start:0, limit:gridPageSize, eventStatus: 0 }});

	gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({ 
		listeners: 
		{
			selectionchange: function(gridCheckBoxSelectionModelObj) 
			{ 
				if (gridCheckBoxSelectionModelObj.getCount() > 0)
				{
					if (gridCheckBoxSelectionModelObj.getCount() == 1) 
					{ 
						gridObj.detailsButton.enable(); 
						
						var selected = gridCheckBoxSelectionModelObj.getSelected();
						if (selected.data.active == 1) 
						{
							gridObj.runNowButton.enable();
						}
						else
						{
							gridObj.runNowButton.disable();
						}
					} 
					else 
					{ 
						gridObj.detailsButton.disable(); 
						gridObj.runNowButton.disable();
					}
					gridObj.activeButton.enable(); 
					gridObj.inactiveButton.enable(); 
					gridObj.deleteButton.enable(); 
				} 
				else 
				{ 
					gridObj.activeButton.disable(); 
					gridObj.inactiveButton.disable(); 
					gridObj.detailsButton.disable();  
					gridObj.deleteButton.disable(); 
					gridObj.runNowButton.disable(); 
				}
			}
		}
	});

	var columnRenderer = function(value, p, record, rowIndex, colIndex, store)
	{
		var className2 = '';
		if (record.data.active == 0) 
		{
			if (colIndex == 7) value = "{/literal}{#str_LabelInactive#}{literal}";
			className2 = 'inactive';
		}
		else
		{
			if (colIndex == 7) value = "{/literal}{#str_LabelActive#}{literal}";
		}
		
		if ((colIndex == 3) && (record.data.statuscode == 1)) 
		{
			if (className2 == '')
			{
				className2 = 'errorrecord';
			}
		}
		
		if (colIndex == 8)
		{
			if (record.data.priority > 0)
			{
				className2 = 'highPriority';
			}
			value = '';
		}
		
		if (className2 != '')
		{
			className2 = 'class="'+className2+'"';
		}
		return '<span '+className2+'>'+value+'</span>';
	};

	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {	sortable: true, resizable: true },
		columns: [gridCheckBoxSelectionModelObj,
	    	{ header: "{/literal}{#str_LabelTaskCode#}{literal}", dataIndex: 'taskcode', renderer: columnRenderer, width:150 },
	    	{ header: "{/literal}{#str_EventLastRunTime#}{literal}", dataIndex: 'lastruntime', width:150, renderer: columnRenderer},
        	{ id: 'status', header: "{/literal}{#str_EventExecutionStatus#}{literal}", dataIndex: 'status', width:150, renderer: columnRenderer, sortable: false, menuDisabled: true},
	    	{ header: "{/literal}{#str_EventRunCount#}{literal}", dataIndex: 'runcount', width:95, renderer: columnRenderer, align: 'right'},
        	{ header: "{/literal}{#str_EventMaxRunCount#}{literal}", dataIndex: 'maxruncount', width:95, renderer: columnRenderer, align: 'right'},
        	{ id: 'nextRunTime', header: "{/literal}{#str_EventNextRunTime#}{literal}", dataIndex: 'nextRunTime', width:150, renderer: columnRenderer},
        	{ header: "{/literal}{#str_LabelActive#}{literal}", dataIndex: 'active', width:100, align: 'right', renderer: columnRenderer},
        	{ header: "{/literal}{#str_LabelPriority#}{literal}", dataIndex: 'priority', width:80, align: 'right', renderer: columnRenderer, sortable: false, menuDisabled: true}
    	]
	});
				
 
	var onEdit = function()
	{
		var paramArray = {};
		paramArray['id'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('eventsGrid'));
		
		if(!scheduledEventsWindowExists)
		{
			scheduledEventsWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminScheduledEvents.detailsDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}
	};

	onCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{	
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
			gridDataStoreObj.reload();
			if ( (Ext.getCmp('dialog')) && (Ext.getCmp('dialog').isVisible()))
			{
				Ext.getCmp('dialog').close();
			}
		}
	};

	var onActivate = function(btn, ev)
	{
		var gridObj = gMainWindowObj.findById('eventsGrid');
		var paramArray = {};
		paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gridObj);

		var active = 0; 
		if (btn.id == 'activeButton') active = 1;
		paramArray['active'] = active;
	
		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminScheduledEvents.eventActivate', "{/literal}{#str_MessageUpdating#}{literal}", onCallback);
	};


	var onDelete = function(btn, ev)
	{
		var onDeleteConfirmed = function(btn)
		{
			if (btn == "yes") {
				var paramArray = {};
				paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('eventsGrid'));
				Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminScheduledEvents.eventDelete', "{/literal}{#str_MessageDeleting#}{literal}", onCallback);
			}
		};

		var gridObj = gMainWindowObj.findById('eventsGrid');
		var selRecords = gridObj.selModel.getSelections();
		var codeList = [];
	
		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_DeleteConfirmation#}{literal}", onDeleteConfirmed);
	};


	var onRunNow = function()
	{
		var gridObj = gMainWindowObj.findById('eventsGrid');
		var paramArray = {};
		paramArray['id'] = Ext.taopix.gridSelection2IDList(gridObj);
		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminScheduledEvents.eventRun', "{/literal}{#str_MessageUpdating#}{literal}", onCallback);
		
	};

		
	gridObj = new Ext.grid.GridPanel({
   		id: 'eventsGrid',
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
		autoExpandColumn: 'status',
		columnLines:true,
    	stateId: 'eventsGrid',
    	ctCls: 'grid',
    	tbar: [	
    		{ref: '../detailsButton',	text: "{/literal}{#str_ButtonEdit#}{literal}",	iconCls: 'silk-pencil',	handler: onEdit, disabled: true	}, '-',
           	{ref: '../deleteButton', text: "{/literal}{#str_ButtonDelete#}{literal}", iconCls: 'silk-delete', handler: onDelete, disabled: true, id:'deleteButton' }, '-',
            {ref: '../activeButton', text: "{/literal}{#str_LabelMakeActive#}{literal}", iconCls: 'silk-lightbulb', handler: onActivate, disabled: true, id:'activeButton'}, '-', 
      	    {ref: '../inactiveButton', text: "{/literal}{#str_LabelMakeInactive#}{literal}", iconCls: 'silk-lightbulb-off',  handler: onActivate, disabled: true, id:'inactiveButton'}, '-',
    		{ref: '../runNowButton', text: "{/literal}{#str_LabelRunNow#}{literal}", iconCls: 'silk-clock', handler: onRunNow, disabled: true, id:'runNowButton'},
    		{xtype:'tbfill'},
    		{
				xtype: 'combo',
				id: 'eventStatus',
				name: 'eventStatus',
				mode: 'local',
				editable: false,
				forceSelection: true,
				selectOnFocus: true,
				triggerAction: 'all',
				hideLabel: true,
				width: 150,
				store: new Ext.data.ArrayStore({
					id: 0,
					fields: ['id', 'name'],
					data: [
						["0", "{/literal}{#str_LabelActiveEvents#}{literal}"],
						["1", "{/literal}{#str_LabelFailedEvents#}{literal}"],
						["2", "{/literal}{#str_LabelCompletedEvents#}{literal}"]
					]
				}),
				valueField: 'id',
				displayField: 'name',
				useID: true,
				allowBlank: false,
				validateOnBlur:true, 
				value: "0",
				post: true,
				listeners:
				{
					'select': function()
					{
						var eventsGrid = Ext.getCmp('eventsGrid');
						eventsGrid.getBottomToolbar().changePage(1);
					}
				}
			},
    		{xtype: 'tbspacer', width: 10} 
    	],
		plugins: [
			new Ext.ux.grid.Search({
				iconCls: 'silk-zoom',
				minChars: 3,
				width: 200,
				autoFocus: true,
				disableIndexes:['recordid', 'lastruntime','status', 'nextRunTime', 'statuscode', 'priority', 'active']
			})
		],
		bbar: new Ext.PagingToolbar({ pageSize: gridPageSize, store: gridDataStoreObj, displayInfo: true })     
	}); 
	
	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_SectionTitleScheduledEvents#}{literal}",
		items: gridObj,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose(); accordianWindowInitialized = false;}, qtip: '{/literal}{#str_LabelCloseWindow#}{literal}' }],
		baseParams: { ref: '{/literal}{$ref}{literal}' }
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
