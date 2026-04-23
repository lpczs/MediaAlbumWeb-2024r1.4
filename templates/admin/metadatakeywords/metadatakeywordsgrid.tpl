{literal}
function initialize(pParams)
{	
	var sessionId = "{/literal}{$ref}{literal}";
	metaKeywordsWindowExists = false;
	var onAdd = function()
	{
		var paramArray = [];
		
		if(!metaKeywordsWindowExists)
		{
			metaKeywordsWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminMetadataKeywords.addDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}
	};

	var onEdit = function()
	{
		var paramArray = {};
		paramArray['id'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('keywordsGrid'));
		
		if(!metaKeywordsWindowExists)
		{
			metaKeywordsWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminMetadataKeywords.editDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
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
			if (Ext.getCmp('dialog').isVisible())
			{
				Ext.getCmp('dialog').close();
			}
		}
	};
	
	var columnRenderer = function(value, p, record, rowIndex, colIndex, store)
	{
		/*var className = '';
		if (record.data.isactive == 0) 
		{
			if (colIndex == 10) value = "{/literal}{#str_LabelInactive#}{literal}";
			className = 'class = "inactive"'
		}
		else
		{
			if (colIndex == 10) value = "{/literal}{#str_LabelActive#}{literal}";
		}
		return '<span '+className+'>'+value+'</span>';*/
		return value;
	};
	
	gridDataStoreObj = new Ext.data.Store({
		remoteSort: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminMetadataKeywords.getGridData&ref=' + sessionId }),
		method:'POST',
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([ 
				{name:'recordid', mapping: 0},
				{name: 'ref', mapping: 1},
				{name: 'code', mapping: 2},
				{name: 'name', mapping: 3}, 
				{name: 'description', mapping: 4}, 
				{name: 'type', mapping: 5},  
				{name: 'maxlength', mapping: 6},  
				{name: 'height', mapping: 7}, 
				{name: 'width', mapping: 8},    
				{name: 'values', mapping: 9}
			])
		),
		sortInfo:{field: 'groupcode', direction: "ASC"},
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

	
	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {	sortable: true, resizable: true },
		columns: [gridCheckBoxSelectionModelObj,
	    	{ header: "{/literal}{#str_LabelCode#}{literal}", dataIndex: 'code', width:200, menuDisabled:  true, sortable: false },
	    	{ header: "{/literal}{#str_LabelType#}{literal}", dataIndex: 'type', width:120, renderer: columnRenderer, menuDisabled:  true, sortable: false},
	    	{ header: "{/literal}{#str_LabelName#}{literal}", dataIndex: 'name', width:270, renderer: columnRenderer, menuDisabled:  true, sortable: false},
        	{ header: "{/literal}{#str_LabelDescription#}{literal}", dataIndex: 'description', width:270, renderer: columnRenderer, menuDisabled:  true, sortable: false},
        	{ header: "{/literal}{#str_LabelValue#}{literal}",dataIndex: 'values', width:270, renderer: columnRenderer, menuDisabled:  true, sortable: false},
        	{ header: "{/literal}{#str_LabelMaxLength#}{literal}", dataIndex: 'maxlength', width:100, renderer: columnRenderer, align: 'right', menuDisabled:  true, sortable: false},
        	{ header: "{/literal}{#str_LabelHeight#}{literal}",dataIndex: 'height', width:100, renderer: columnRenderer, align: 'right', menuDisabled:  true, sortable: false},
        	{ header: "{/literal}{#str_LabelWidth#}{literal}", dataIndex: 'width', width:100, renderer: columnRenderer, align: 'right', menuDisabled:  true, sortable: false}
        ]
	});

	var gridObj = new Ext.grid.GridPanel({
   		id: 'keywordsGrid',
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
    	stateId: 'keywordsGrid',
    	ctCls: 'grid',
    	tbar: 
    	[	
    		{ref: '../addButton',	text: "{/literal}{#str_ButtonAdd#}{literal}",	iconCls: 'silk-add',	handler: onAdd	}, '-',
            {ref: '../editButton',	text: "{/literal}{#str_ButtonEdit#}{literal}",	iconCls: 'silk-pencil',	handler: onEdit, disabled: true	}
        ] 
	});
	
	
	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_SectionTitleMetaDataKeyWords#}{literal}",
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
