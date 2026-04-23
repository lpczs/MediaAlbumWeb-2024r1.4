{literal}

function initialize(pParams)
{
    groupEditWindowExists = false;

    // Configure listeners to enable/disable buttons in the grid based on how many items are selected.
	var gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj)
			{
				var selectionCount = gridCheckBoxSelectionModelObj.getCount();

				// If there is one item selected enable the edit and duplicate button.
				if (selectionCount == 1)
				{
					{/literal}{if $companyLogin}
					if (gridCheckBoxSelectionModelObj.getSelected().data.companycode == '')
					{
						grid.editButton.disable();
						grid.duplicateButton.disable();
						grid.deleteButton.disable();
					}
					else
					{
						grid.editButton.enable();
						grid.duplicateButton.enable();
						grid.deleteButton.enable();
					}
					{else}
						grid.editButton.enable();
						grid.duplicateButton.enable();
						grid.deleteButton.enable();
					{/if}{literal}
					
					grid.previewButton.enable();
				}
				else
				{
					grid.editButton.disable();
                    grid.duplicateButton.disable();
					grid.deleteButton.disable();
					grid.previewButton.disable();
				}
			}
		},
        singleSelect: true
    });

    var gridDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		groupField: 'companycodeHidden',
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminProductGroups.getGridData&ref={/literal}{$ref}{literal}'}),
		reader: new Ext.taopix.PagedArrayReader({
				idIndex: 0
			},
			Ext.data.Record.create([
				{name: 'id', mapping: 0},
				{name: 'name', mapping: 1},
				{name: 'active', mapping: 2},
				{name: 'error', mapping: 3},
				{name: 'companycode', mapping: 4},
				{name: 'companycodeHidden', mapping: 4}
			])
		)
    });

	function companyRenderer(value, p, record)
	{
		if (value == '')
		{
			value = "<i>{/literal}{#str_Global#}{literal}</i>";
		}

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

	function hiddenCompanyRenderer(value, p, record)
	{
		{/literal}{if $optionms}{literal}
		if (value == '')
		{
			return "{/literal}{#str_Global#}{literal}";
		}
		else
		{
			return value;
		}
		{/literal}{else}{literal}
			return value;
		{/literal}{/if}{literal}
	}

	function clearGrouping(v)
	{ 
		if(v.checked)
		{
			{/literal}{if $optionms}{literal}
				gridDataStoreObj.groupBy('companycodeHidden');
			{/literal}{else}{literal}
				gridDataStoreObj.groupBy('active');
			{/literal}{/if}{literal}
		}
		else 
		{
			gridDataStoreObj.clearGrouping(); 
		}
	}

    var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
			sortable: false,
			resizable: true
		},
		columns: [
			gridCheckBoxSelectionModelObj,
			{/literal}{if $optionms}{literal}
			{header: '{/literal}{#str_LabelCompany#}{literal}', width: 100, dataIndex: 'companycode', renderer: companyRenderer},
			{header: "{/literal}{#str_LabelCompany#}{literal}", width: 200, dataIndex: 'companycodeHidden', hidden: true, renderer: hiddenCompanyRenderer},
			{/literal}{/if}{literal}
			{header: "{/literal}{#str_LabelGroupName#}{literal}", width: 200, dataIndex: 'name', renderer: columnRenderer}
		]
	});

    var grid = new Ext.grid.GridPanel({
		autoExpandMax: 5000, // Set the max width an auto expand column can be over the 1000px default.
		{/literal}{if $optionms}{literal}
		autoExpandColumn: 3,
		view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_TitleProductGroups#}" : "{#str_LabelProductGroup#}"]})' }),
		{else}
		autoExpandColumn: 1,
		{/if}{literal}
		id: 'maingrid',
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
			}, '-',
            {
				ref: '../duplicateButton',
				text: "{/literal}{#str_LabelDuplicate#}{literal}",
				iconCls: 'silk-page-copy',
				handler: onDuplicate,
				disabled: true
			}, '-',
			{
				ref: '../deleteButton',
				text: "{/literal}{#str_ButtonDelete#}{literal}",
				iconCls: 'silk-delete',
				handler: onDelete,
				disabled: true
			}, '-',
			{
				ref: '../previewButton',
				text: "{/literal}{#str_ButtonPreview#}{literal}",
				iconCls: 'silk-application-view-list',
				handler: onPreview,
				disabled: true
			}
			{/literal}{if $optionms}{literal}
				, '-',
				new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelEnableGrouping#}{literal}", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }})
			{/literal}{/if}{literal},
		],
        plugins: [new Ext.ux.grid.Search({
			iconCls: 'silk-zoom',
			minChars: 3,
			width: 200,
			disableIndexes:['active','companycode','companycodeHidden'],
			autoFocus: true
		})],
        bbar: new Ext.PagingToolbar({ pageSize: 100, store: gridDataStoreObj, displayInfo: true})
	});

	gridDataStoreObj.load({
		params: {
			start: 0,
			limit: 100,
			fields: '',
			query: ''
		}
	});

    /* add handler */
	function onAdd(btn, ev)
	{
		if (!groupEditWindowExists)
		{
			groupEditWindowExists = true;

			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProductGroups.addDisplay&ref={/literal}{$ref}{literal}', '', '', 'initialize', false);
		}
	}

	/* edit handler */
	function onEdit(btn, ev)
	{
		/* server parameters are sent to the server */
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(Ext.getCmp('maingrid'));
		serverParams['id'] = id;

		if (!groupEditWindowExists)
		{
			groupEditWindowExists = true;

			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProductGroups.editDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}

	/* edit handler */
	function onPreview(btn, ev)
	{
		/* server parameters are sent to the server */
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(Ext.getCmp('maingrid'));
		serverParams['id'] = id;

		if (!groupEditWindowExists)
		{
			groupEditWindowExists = true;

			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProductGroups.previewDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}

    /* delete handler */
	function onDelete(btn, ev)
	{
		var gridObj = Ext.getCmp('maingrid');
		var dataStore = gridObj.store;

		var selRecords = gridObj.selModel.getSelections();
		var message = "{/literal}{#str_MessageDeleteProductGroupsConfirmation#}{literal}";

		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", message, onDeleteResult);
	}

    function onDeleteResult(btn)
	{
		if (btn == "yes")
		{
			var paramArray = new Object();

			var gridObj = Ext.getCmp('maingrid');
			var selRecords = gridObj.selModel.getSelections();
			var IDToCheck = selRecords[0].data.id;

			paramArray['id'] = IDToCheck;

			Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminProductGroups.checkDelete', "{/literal}{#str_MessageUpdating#}{literal}", onCheckDeleteCallback);
		}
	}

	function onDeleteConfirmCallback(buttonid, text, opt)
	{
		if (buttonid == 'ok')
		{
			var gridObj = Ext.getCmp('maingrid');
			var selRecords = gridObj.selModel.getSelections();
			var IDToCheck = selRecords[0].data.id;
			var paramArray = new Object();

			paramArray['id'] = IDToCheck;

			Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminProductGroups.delete', "{/literal}{#str_MessageDeleting#}{literal}", onDeleteCallback);
		}
	}

	function onCheckDeleteCallback(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated == true)
		{
			if (pActionData.result.msg)
			{
				Ext.MessageBox.show({
					title: pActionData.result.title,
					msg: pActionData.result.msg,
					id: 'deleteConfirmationWarningMessageBox',
					buttons:
					{
						ok:{/literal}"{#str_ButtonDelete#}"{literal},
						cancel:{/literal}"{#str_ButtonDoNotDelete#}"{literal}

					},
					fn:onDeleteConfirmCallback,
					icon: Ext.MessageBox.WARNING
				});
			}
			else
			{
				onDeleteConfirmCallback("ok");
			}
		}
	}
	var previewRecordTemplate = Ext.data.Record.create([
            {name: 'id', mapping: 0},
            {name: 'code', mapping: 1},
            {name: 'name', mapping: 2},
            {name: 'iscollection', mapping: 3},
            {name: 'parentid', mapping: 4}
    ]);

	function onDeleteCallback(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated == true)
		{
			gridDataStoreObj.reload();
		}

	}

    function onDuplicate(btn, ev)
    {
        var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(Ext.getCmp('maingrid'));
		serverParams['id'] = id;

		if (!groupEditWindowExists)
		{
			groupEditWindowExists = true;

			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProductGroups.duplicate&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
    }

    function columnRenderer(value, p, record, rowIndex, colIndex, store)
	{
		return '<span>' + value + '</span>';
	};

    var pageItemsArray = [];
    pageItemsArray.push(grid);



    gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_TitleProductGroups#}{literal}",
		items: pageItemsArray,
		layout: 'anchor',
		anchorSize: '100% 100%',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose();  accordianWindowInitialized = false;}, qtip: "{/literal}{#str_LabelCloseWindow#}{literal}" }],
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