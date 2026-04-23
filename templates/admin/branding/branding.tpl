{literal}

function initialize(pParams)
{
	var sessionId = "{/literal}{$ref}{literal}";
	var gridPageSize = 100;
	var companyCode = '';

	brandingEditWindowExists = false;

	onCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
			gridDataStoreObj.reload();
			if ((Ext.getCmp('dialog')) && (Ext.getCmp('dialog').isVisible()))
			{
				Ext.getCmp('dialog').close();
			}
		}
	};

	var onAdd = function()
	{
		var paramArray = [];

		if (!brandingEditWindowExists)
		{
			brandingEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminBranding.addDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}
	};

	var onEdit = function()
	{
		var paramArray = {};
		paramArray['id'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('brandingGrid'));

		if (!brandingEditWindowExists)
		{
			brandingEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminBranding.editDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}

	};

	var onDelete = function(btn, ev)
	{
		var onDeleteConfirmed = function(btn)
		{
			if (btn == "yes") {
				var paramArray = {};
				paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('brandingGrid'));
				Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminBranding.delete', "{/literal}{#str_MessageDeleting#}{literal}", onCallback);
			}
		};

		var gridObj = gMainWindowObj.findById('brandingGrid');
		var selRecords = gridObj.selModel.getSelections();
		var codeList = [];

		for (var rec = 0; rec < selRecords.length; rec++) {	codeList.push("'"+selRecords[rec].data.foldername+"'");}
		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_DeleteConfirmation#}{literal}".replace("'^0'", codeList.join(', ')), onDeleteConfirmed);
	};

	var onActivate = function(btn, ev)
	{
		var gridObj = gMainWindowObj.findById('brandingGrid');
		var paramArray = {};
		paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gridObj);

		var active = 0;
		if (btn.id == 'activeButton') active = 1;
		paramArray['active'] = active;

		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminBranding.brandingActivate', "{/literal}{#str_MessageUpdating#}{literal}", onCallback);
	};

	var onCompanyChange = function()
	{
		var appGrid = Ext.getCmp('brandingGrid');

		var companyFilterObj = Ext.getCmp('companyFilter');
		if (companyFilterObj)
		{
			companyCode = companyFilterObj.getValue();
		}
		appGrid.store.lastOptions.params['companyCode'] = companyCode;

		appGrid.store.reload({params: appGrid.store.lastOptions.params});
	};

	var columnRenderer = function(value, p, record, rowIndex, colIndex, store)
	{
		var className = '';
		if (record.data.isactive == 0)
		{
			if (colIndex == 5) value = "{/literal}{#str_LabelInactive#}{literal}";
			className = 'class = "inactive"'
		}
		else
		{
			if (colIndex == 5) value = "{/literal}{#str_LabelActive#}{literal}";
		}
		return '<span '+className+'>'+value+'</span>';
	};

	gridDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		remoteGroup: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminBranding.list&ref=' + sessionId }),
		method:'POST',
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()},
		{/literal}{if $optionMS}{literal}
			groupField:'company',
		{/literal}{/if}{literal}
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([
				{name:'recordid', mapping: 0},
				{name:'code', mapping: 1},
				{name:'company', mapping: 2},
				{name: 'foldername', mapping: 3},
				{name: 'appname', mapping: 4},
				{name: 'displayurl', mapping: 5},
				{name: 'isactive', mapping: 6}
			])
		),
		sortInfo:{field: 'foldername', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});
	gridDataStoreObj.load({params:{start:0, limit:gridPageSize, companyCode: companyCode}});

	gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj)
			{
				var records = gridCheckBoxSelectionModelObj.getSelections();
				var hasDefault = false;
				for (var i = 0; i < records.length; i++)
				{
					if (records[i].data.code == '')
					{
						hasDefault = true;
						break;
					}
				}
				if (gridCheckBoxSelectionModelObj.getCount() > 0)
				{
					if (gridCheckBoxSelectionModelObj.getCount() == 1)
					{
						gridObj.editButton.enable();
					}
					else
					{
						gridObj.editButton.disable();
					}
					if (hasDefault)
					{
						gridObj.activeButton.disable(); gridObj.inactiveButton.disable(); gridObj.deleteButton.disable();
					}
					else
					{
						gridObj.activeButton.enable(); gridObj.inactiveButton.enable(); gridObj.deleteButton.enable();
					}
				}
				else
				{
					gridObj.activeButton.disable();
					gridObj.inactiveButton.disable();
					gridObj.editButton.disable();
					gridObj.deleteButton.disable();
				}
			}
		}
	});

	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {	sortable: true, resizable: true },
		columns: [gridCheckBoxSelectionModelObj,
			{ id:'company', header: "{/literal}{#str_LabelCompanyName#}{literal}", dataIndex: 'company', hidden:true },
	    	{ id: 'foldername', header: "{/literal}{#str_LabelFolderName#}{literal}", width: 250, dataIndex: 'foldername', renderer: columnRenderer },
	    	{ header: "{/literal}{#str_LabelApplicationName#}{literal}", dataIndex: 'appname', width:250, renderer: columnRenderer},
        	{ header: "{/literal}{#str_LabelDisplayURL#}{literal}", dataIndex: 'displayurl', width:550, renderer: columnRenderer},
        	{ id: 'isactive', header: "{/literal}{#str_LabelActive#}{literal}", dataIndex: 'isactive', width:120, renderer: columnRenderer, align: 'right'}
        ]
	});

	gridObj = new Ext.grid.GridPanel({
   		id: 'brandingGrid',
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
		view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_LabelItems#}{literal}" : "{/literal}{#str_LabelItem#}{literal}"]})' }),
		autoExpandColumn: 'isactive',
		columnLines:true,
    	stateId: 'brandingGrid',
    	ctCls: 'grid',
    	tbar: [	{
    				ref: '../addButton',
    				text: "{/literal}{#str_ButtonAdd#}{literal}",
    				iconCls: 'silk-add',
    				handler: onAdd,
    				{/literal}{if $bc}{literal}
    					disabled: false
    				{/literal}{else}{literal}
    					disabled: true
    				{/literal}{/if}{literal}
    			}, '-',
        	    {ref: '../editButton',	text: "{/literal}{#str_ButtonEdit#}{literal}",	iconCls: 'silk-pencil',	handler: onEdit, disabled: true	}, '-',
           		{ref: '../deleteButton', text: "{/literal}{#str_ButtonDelete#}{literal}", iconCls: 'silk-delete', handler: onDelete, disabled: true, id:'deleteButton' }, '-',
          	  	{ref: '../activeButton', text: "{/literal}{#str_LabelMakeActive#}{literal}", iconCls: 'silk-lightbulb', handler: onActivate, disabled: true, id:'activeButton'}, '-',
      	   		{ref: '../inactiveButton', text: "{/literal}{#str_LabelMakeInactive#}{literal}", iconCls: 'silk-lightbulb-off',  handler: onActivate, disabled: true, id:'inactiveButton'}

      	   		{/literal}{if $optionMS == true and $userType==0}{literal}
				,{xtype:'tbfill'}
               	,new Ext.taopix.CompanyCombo({id:'companyFilter',name: 'companyFilter', emptyText:"{/literal}{#str_LabelCompanyName#}{literal}", options: {ref: sessionId, includeGlobal: '1', includeShowAll:'1', onchange: onCompanyChange} })
               	,{xtype: 'tbspacer', width: 10}
               	{/literal}{/if}{literal}
    	],
    	plugins: [
			new Ext.ux.grid.Search({
				iconCls: 'silk-zoom',
				minChars: 3,
				width: 230,
				autoFocus: true,
				disableIndexes: ['displayurl', 'company', 'isactive']
			})
		],
		bbar: new Ext.PagingToolbar({ pageSize: gridPageSize, store: gridDataStoreObj, displayInfo: true })
	});

	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_SectionTitleBranding#}{literal}",
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
	if (Ext.getCmp('dialog'))
	{
		Ext.getCmp('dialog').close();
	}
}

{/literal}
