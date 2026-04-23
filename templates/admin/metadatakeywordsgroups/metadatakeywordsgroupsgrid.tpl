{literal}
function initialize(pParams)
{
	var sessionId = "{/literal}{$ref}{literal}";
	keywordGroupsEditWindowExists = false;
	var onAdd = function()
	{
		var paramArray = [];

		if (!keywordGroupsEditWindowExists)
		{
			keywordGroupsEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminMetadataKeywordsGroups.addDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}
	};

	var onEdit = function()
	{
		var paramArray = {};
		paramArray['id'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('keywordsGroupsGrid'));

		if (!keywordGroupsEditWindowExists)
		{
			keywordGroupsEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminMetadataKeywordsGroups.editDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}
	};

	gridDataStoreObj = new Ext.data.GroupingStore({
		remoteGroup:true,
		remoteSort: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminMetadataKeywordsGroups.getGridData&ref=' + sessionId }),
		method:'POST',
		groupField:'keywordSection',
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([
				{name: 'groupId', mapping: 0},
				{name: 'groupCode', mapping: 1},
				{name: 'productCodes', mapping: 2},
				{name: 'keywordCode', mapping: 3},
				{name: 'keywordSection', mapping: 4}
			])
		),
		sortInfo:{field: 'code', direction: "ASC"},
		baseParams:{csrf_token: Ext.taopix.getCSRFToken()}
	});
	gridDataStoreObj.load();

	gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj) {
				if (gridCheckBoxSelectionModelObj.getCount() > 0)
				{
					if (gridCheckBoxSelectionModelObj.getCount() == 1) gridObj.editButton.enable(); else gridObj.editButton.disable();
				}
				else
				{
					gridObj.editButton.disable();
				}
			}
		}
	});

	var columnRenderer = function(value, p, record, rowIndex, colIndex, store)
	{
		// if the value is empty
		if ((value == '') || (value == '**ALL**'))
		{
			switch (colIndex)
			{
				// if column is liscense keys
				case 3:
				{
					value = "{/literal}{#str_LabelAll#}{literal}";
					break;
				}

				// if column is products
				case 4:
				{
					// if empty, and keyword section is 'ORDER'
					if (record.data.keywordSection == 'ORDER')
					{
						value = "{/literal}{#str_LabelAll#}{literal}";
					}
					break;
				}
			}
		}

		return value;
	};

	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {	sortable: true, resizable: true },
		columns: [gridCheckBoxSelectionModelObj,
	    	{ header: "groupId", dataIndex: 'groupId', width:100, menuDisabled:true, hidden:true },
	    	{ header: "{/literal}{#str_LabelSection#}{literal}", dataIndex: 'keywordSection', width:100, menuDisabled:true, hidden:true },
	    	{ header: "{/literal}{#str_LabelLicenseKey#}{literal}", dataIndex: 'groupCode', width:200, menuDisabled:  true, renderer: columnRenderer },
	    	{ header: "{/literal}{#str_SectionTitleProducts#}{literal}", dataIndex: 'productCodes', width:300, menuDisabled:  true, renderer: columnRenderer},
	    	{ id: 'keywordsCol', header: "{/literal}{#str_SectionTitleMetaDataKeyWords#}{literal}", dataIndex: 'keywordCode', width:300, menuDisabled:  true}
        ]
	});



	var gridObj = new Ext.grid.GridPanel({
   		id: 'keywordsGroupsGrid',
   		store: gridDataStoreObj,
    	selModel: gridCheckBoxSelectionModelObj,
    	cm: gridColumnModelObj,
    	stripeRows: true,
    	stateful: true,
    	enableColLock: false,
		draggable: false,
		enableColumnHide: false,
		enableColumnMove: false,
		trackMouseOver: false,
		columnLines:true,
    	stateId: 'keywordsGroupsGrid',
    	ctCls: 'grid',
    	autoExpandColumn: 'keywordsCol',
    	view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_LabelItems#}{literal}" : "{/literal}{#str_LabelItem#}{literal}"]})' }),
    	tbar:
    	[
    		{ref: '../addButton',	text: "{/literal}{#str_ButtonAdd#}{literal}",	iconCls: 'silk-add',	handler: onAdd	}, '-',
            {ref: '../editButton',	text: "{/literal}{#str_ButtonEdit#}{literal}",	iconCls: 'silk-pencil',	handler: onEdit, disabled: true	}
        ]
	});

	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_SectionTitleMetaDataKeyWordGroups#}{literal}",
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
