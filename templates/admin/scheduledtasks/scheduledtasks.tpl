{literal}

function initialize(pParams)
{
	scheduledTasksEditWindowExists = false;

	var sessionId = "{/literal}{$ref}{literal}";
	var gridPageSize = 100;

	gridDataStoreObj = new Ext.data.Store({
		remoteSort: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminScheduledTasks.displayList&ref=' + sessionId }),
		method:'POST',
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([
				{name:'recordid', mapping: 0},
				{name: 'code', mapping: 1},
				{name: 'name', mapping: 2},
				{name: 'lastRunTime', mapping: 3},
				{name: 'status', mapping: 4},
				{name: 'nextRunTime', mapping: 5},
				{name: 'runStatus', mapping: 6},
				{name: 'internal', mapping: 7},
				{name: 'active', mapping: 8},
				{name: 'statusCode', mapping: 9}
			])
		),
		sortInfo:{field: 'recordid', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});
	gridDataStoreObj.load({params:{ start:0, limit:gridPageSize }});

	gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj) {

				if (gridCheckBoxSelectionModelObj.getCount() > 0)
				{

					var selectID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('tasksGrid'));
					var idList = selectID.split(',');

					for (i = 0; i < idList.length; i++)
					{
						record = Ext.getCmp('tasksGrid').store.getById(idList[i]);

						if (record.data['internal'] == 1 && idList[i] > 0)
						{
							gridObj.deleteButton.disable();
							break;
						}
						else
						{
							gridObj.deleteButton.enable();
						}
					}

					if (gridCheckBoxSelectionModelObj.getCount() == 1)
					{
						gridObj.editButton.enable();
					}
					else
					{
						gridObj.editButton.disable();
					}

					gridObj.activeButton.enable();
					gridObj.inactiveButton.enable();
					gridObj.runNowButton.enable();
				}
				else
				{
					gridObj.activeButton.disable();
					gridObj.inactiveButton.disable();
					gridObj.editButton.disable();
					gridObj.deleteButton.disable();
					gridObj.runNowButton.disable();
				}
			}
		}
	});

	var columnRenderer = function(value, p, record, rowIndex, colIndex, store)
	{
		/*var className = [];*/
		var className2 = '';
		if (record.data.active == 0)
		{
			if (colIndex == 7) value = "{/literal}{#str_LabelInactive#}{literal}";
			/*className.push('inactive');*/
			className2 = 'inactive';
		}
		else
		{
			if (colIndex == 7) value = "{/literal}{#str_LabelActive#}{literal}";
		}

		if ((colIndex == 4) && (record.data.statusCode == 1))
		{
			/*className.push('errorrecord');*/
			if (className2 == '')
			{
				className2 = 'errorrecord';
			}
		}

		if (colIndex == 6)
		{
			if (record.data.internal == 0)
			{
				value = "{/literal}{#str_LabelNo#}{literal}";
			}
			else
			{
				value = "{/literal}{#str_LabelYes#}{literal}";
			}
		}
		/*className = className.join(' ');
		if (className.length > 0)
		{
			className = 'class="'+className+'"';
		}
		*/
		if (className2 != '')
		{
			className2 = 'class="'+className2+'"';
		}
		return '<span '+className2+'>'+value+'</span>';
	};

	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {	sortable: true, resizable: true },
		columns: [gridCheckBoxSelectionModelObj,
	    	{ header: "{/literal}{#str_TaskCode#}{literal}", dataIndex: 'code', width:150, renderer: columnRenderer },
	    	{ id:'name', header: "{/literal}{#str_TaskName#}{literal}", dataIndex: 'name', width:200, renderer: columnRenderer},
        	{ header: "{/literal}{#str_TaskLastRunTime#}{literal}", dataIndex: 'lastRunTime', width:120, renderer: columnRenderer},
        	{ header: "{/literal}{#str_TaskExecutionStatus#}{literal}", dataIndex: 'status', width:200, renderer: columnRenderer, sortable: false, menuDisabled: true},
        	{ header: "{/literal}{#str_TaskNextRunTime#}{literal}", dataIndex: 'nextRunTime', width:150, renderer: columnRenderer},
        	{ header: "{/literal}{#str_TaskInternal#}{literal}", dataIndex: 'internal', width:100, align: 'right', renderer: columnRenderer},
        	{ header: "{/literal}{#str_LabelActive#}{literal}", dataIndex: 'active', width:100, align: 'right', renderer: columnRenderer}
    	]
	});

	var onAdd = function()
	{
		var paramArray = [];

		if (!scheduledTasksEditWindowExists)
		{
			scheduledTasksEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminScheduledTasks.addDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}
	};

	var onEdit = function()
	{
		var paramArray = {};
		paramArray['id'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('tasksGrid'));

		if (!scheduledTasksEditWindowExists)
		{
			scheduledTasksEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminScheduledTasks.editDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}
	};

	onCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO});
			}
			else
			{
				// only close the window if there was no error
				
				scheduledTasksEditWindowExists = false;

				if (typeof gDialogObj != "undefined")
				{
					gDialogObj.close();
				}
			}

			gridDataStoreObj.reload();
		}
		else
		{
			scheduledTasksEditWindowExists = false;
			gDialogObj.close();
		}
	};

	var onActivate = function(btn, ev)
	{
		var gridObj = gMainWindowObj.findById('tasksGrid');
		var paramArray = {};
		paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gridObj);

		var active = 0;
		if (btn.id == 'activeButton') active = 1;
		paramArray['active'] = active;

		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminScheduledTasks.taskActivate', "{/literal}{#str_MessageUpdating#}{literal}", onCallback);
	};


	var onDelete = function(btn, ev)
	{
		var onDeleteConfirmed = function(btn)
		{
			if (btn == "yes") {
				var paramArray = {};
				paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('tasksGrid'));
				Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminScheduledTasks.taskDelete', "{/literal}{#str_MessageDeleting#}{literal}", onCallback);
			}
		};

		var gridObj = gMainWindowObj.findById('tasksGrid');
		var selRecords = gridObj.selModel.getSelections();
		var codeList = [];

		for (var rec = 0; rec < selRecords.length; rec++) {	codeList.push("'"+selRecords[rec].data.code+"'");}
		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_DeleteConfirmation#}{literal}".replace("'^0'", codeList.join(', ')), onDeleteConfirmed);
	};


	var onRunNow = function()
	{
		var gridObj = gMainWindowObj.findById('tasksGrid');
		var paramArray = {};
		paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gridObj);
		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminScheduledTasks.taskRunNow', "{/literal}{#str_MessageUpdating#}{literal}", onCallback);
	};


	gridObj = new Ext.grid.GridPanel({
   		id: 'tasksGrid',
   		flex:1,
   		store: gridDataStoreObj,
    	selModel: gridCheckBoxSelectionModelObj,
    	cm: gridColumnModelObj,
    	stripeRows: true,
    	stateful: true,
    	enableColLock:false,
    	disabled: !({/literal}{$schedulerActive}{literal}),
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		trackMouseOver:false,
		autoExpandColumn: 'name',
		columnLines:true,
    	stateId: 'tasksGrid',
    	ctCls: 'grid',
    	tbar: [
    		{/literal}{if $optionwscrp}{literal}
    			{ref: '../addButton',	text: "{/literal}{#str_ButtonAdd#}{literal}",	iconCls: 'silk-add', handler: onAdd	}, '-',
    		{/literal}{/if}{literal}
            {ref: '../editButton',	text: "{/literal}{#str_ButtonEdit#}{literal}",	iconCls: 'silk-pencil',	handler: onEdit, disabled: true	}, '-',
           	{ref: '../deleteButton', text: "{/literal}{#str_ButtonDelete#}{literal}", iconCls: 'silk-delete', handler: onDelete, disabled: true, id:'deleteButton' }, '-',
            {ref: '../activeButton', text: "{/literal}{#str_LabelMakeActive#}{literal}", iconCls: 'silk-lightbulb', handler: onActivate, disabled: true, id:'activeButton'}, '-',
      	    {ref: '../inactiveButton', text: "{/literal}{#str_LabelMakeInactive#}{literal}", iconCls: 'silk-lightbulb-off',  handler: onActivate, disabled: true, id:'inactiveButton'}, '-',
    		{ref: '../runNowButton', text: "{/literal}{#str_LabelRunNow#}{literal}", iconCls: 'silk-clock', handler: onRunNow, disabled: true, id:'runNowButton'}
    	]
    	,
		plugins: [
			new Ext.ux.grid.Search({
				iconCls: 'silk-zoom',
				minChars: 3,
				width: 200,
				autoFocus: true,
				disableIndexes:['recordid', 'lastRunTime','status', 'nextRunTime', 'runStatus', 'internal', 'active']
			})
		],
		bbar: new Ext.PagingToolbar({ pageSize: gridPageSize, store: gridDataStoreObj, displayInfo: true })
	});

	var bottomPanel = new Ext.Panel({
		id: 'bottomPanel',
		layout: 'column',
		layoutConfig: { align : 'stretch', pack  : 'start' },
		frame:true,
		bodyBorder: false,
		border: false,
		height: 35,
		defaults: {xtype: 'textfield', labelWidth: 60, width: 300},
		items:
		[
			{
				xtype:'container',
				height: 35,
				columnWidth: .5,
				items:
				[
					{
						xtype: 'checkbox',
						id: 'schedulerActive',
						name: 'schedulerActive',
						boxLabel: "{/literal}{#str_LabelSchedulerActive#}{literal}",
						checked: {/literal}{$schedulerActive}{literal},
						listeners: {
							'check': function(cbx, checked)
							{
								var conn = new Ext.data.Connection();

								checked = checked + 0;
								conn.request({
		    						url: './?fsaction=AdminScheduledTasks.taskSchedulerActivate&ref='+sessionId,
		    						method: 'POST',
		    						params: {'active':checked, csrf_token: Ext.taopix.getCSRFToken()},
		    						success: function(responseObject)
		    						{
		    							var updateResult = eval('(' + responseObject.responseText + ')');

		    							if (!updateResult.success)
		    							{
		    								Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: updateResult.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING});
										}
		    							else
		    							{
		    								Ext.MessageBox.show({ title: "{/literal}{#str_TitleConfirmation#}{literal}", msg: "{/literal}{#str_LabelShedulerUpdated#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO});
		    								if (!checked)
											{
												gridObj.disable();
											}
											else
											{
												gridObj.enable();
											}
		    							}
		    						},
		    						failure: function()
		    						{
		    							Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorConnectFailure#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING});
									}
								});
							}
						}
					}
				]
			},
			{
				xtype:'container',
				height: 35,
				columnWidth: .49,
				style: 'text-align: right',
				items:
				[
					{
						xtype: 'label',
						text: "{/literal}{#str_LabelLastRun#}{literal}",
						style: 'margin-right: 7px'
					},
					{
						xtype: 'label',
						text: "{/literal}{$schedulerLastRunTime}{literal}"

					}
				]
			}
		]
	});

	var centerPanel = new Ext.Panel({
		id: 'centerPanel',
		layout: 'vbox',
		layoutConfig: { align : 'stretch', pack  : 'start' },
		plain:true,
		bodyBorder: false,
		border: false,
		items: [ gridObj, bottomPanel ]
	});

	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_SectionTitleScheduledTasks#}{literal}",
		items: centerPanel,
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
