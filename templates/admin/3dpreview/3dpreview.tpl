{literal}

var productIDList = "";
var notAllowedProductsCodesArray = [];

function initialize(pParams)
{
	Ext.QuickTips.init();
	Ext.form.Field.prototype.msgTarget = 'side';

	modelPreviewEditWindowExists = false;

	var modelGridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel(
	{
		listeners:
		{
			selectionchange: function(modelGridCheckBoxSelectionModelObj)
			{
				var selectionCount = modelGridCheckBoxSelectionModelObj.getCount();

				if (selectionCount == 1)
				{
					var selectID = Ext.taopix.gridSelection2IDList(gDialogObj.findById('modelGrid'));
					var idList = selectID.split(',');

					for (i = 0; i < idList.length; i++)
					{
						var record = Ext.getCmp('modelGrid').store.getById(idList[i]);

						modelGrid.admin3DModelListeditButton.enable();
						modelGrid.admin3DModelListdeleteButton.enable();
						modelGrid.admin3DModelListActiveButton.enable();
						modelGrid.admin3DModelListInactiveButton.enable();
					}
				}
				else if (selectionCount > 1)
				{
					modelGrid.admin3DModelListeditButton.disable();

					modelGrid.admin3DModelListActiveButton.enable();
					modelGrid.admin3DModelListInactiveButton.enable();
				}
				else
				{
					modelGrid.admin3DModelListeditButton.disable();
					modelGrid.admin3DModelListdeleteButton.disable();
					modelGrid.admin3DModelListActiveButton.disable();
					modelGrid.admin3DModelListInactiveButton.disable();
				}
			}
		}
	});

	var modelGridDataStoreObj = new Ext.data.GroupingStore(
	{
		id: 'threedpreviewmodelsstore',
		remoteSort: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=Admin3DPreview.getGridData&ref={/literal}{$ref}{literal}'}),
		reader: new Ext.taopix.PagedArrayReader(
		{
			idIndex: 0
		},
		Ext.data.Record.create(
		[
			{name: 'id', mapping: 0},
			{name: 'resourcecode', mapping: 1},
			{name: 'resourcename', mapping: 2},
			{name: 'active', mapping: 3},
			{name: 'modeltype', mapping: 4},
			{name: 'hasfileerror', mapping: 5}
		])),
		sortInfo: 
		{
			field: 'resourcename',
			direction: "ASC"
		},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});

	var modelGridColumnModelObj = new Ext.grid.ColumnModel(
	{
		defaults:
		{
			sortable: true,
			resizable: true
		},
		columns: [
			modelGridCheckBoxSelectionModelObj,
			{header: '{/literal}{#str_LabelCode#}{literal}', renderer: generalColumnRenderer, dataIndex: 'resourcecode', width: 300},
			{header: '{/literal}{#str_LabelName#}{literal}', renderer: generalColumnRenderer,  dataIndex: 'resourcename', width: 300},
			{header: '{/literal}{#str_LabelStatus#}{literal}', renderer: statusRenderer,  dataIndex: 'active', width: 100},
			{header: '{/literal}{#str_Label3DModelType#}{literal}', renderer: modelTypeRenderer,  dataIndex: 'modeltype', width: 100},
			{header: '{/literal}{#str_LabelStatus#}{literal}', renderer: generalColumnRenderer, dataIndex: 'hasfilerror', hidden: true}
		]
	});

	var modelGrid = new Ext.grid.GridPanel(
	{
		id: 'modelGrid',
		store: modelGridDataStoreObj,
		cm: modelGridColumnModelObj,
		height: 350,
		enableColLock: false,
		border: false,
		draggable: false,
		enableColumnHide: false,
		enableColumnMove: false,
		enableHdMenu: false,
		trackMouseOver: false,
		stripeRows: true,
		columnLines: true,
		ctCls: 'grid',
		style: 'border:1px solid #99BBE8;',
		autoExpandColumn:2,
		selModel: modelGridCheckBoxSelectionModelObj,
		tbar: [
			{
				text: "{/literal}{#str_ButtonAdd#}{literal}",
				iconCls: 'silk-add',
				handler: onAdd
			},
			'-',
			{
				ref: '../admin3DModelListeditButton',
				text: "{/literal}{#str_ButtonEdit#}{literal}",
				iconCls: 'silk-pencil',
				handler: onEdit,
				disabled: true
			},
			'-',
			{
				ref: '../admin3DModelListdeleteButton',
				text: "{/literal}{#str_ButtonDelete#}{literal}",
				iconCls: 'silk-delete',
				handler: onDelete,
				disabled: true
			},
			'-',
			{
				id: 'admin3DModelListActiveButton',
				ref: '../admin3DModelListActiveButton',
				text: "{/literal}{#str_LabelMakeActive#}{literal}",
				iconCls: 'silk-lightbulb',
				handler: setModelActiveStatus,
				disabled: true
			},
			'-',
			{
				id: 'admin3DModelListInactiveButton',
				ref: '../admin3DModelListInactiveButton',
				text: "{/literal}{#str_LabelMakeInactive#}{literal}",
				iconCls: 'silk-lightbulb-off',
				handler: setModelActiveStatus,
				disabled: true
			}
		]
	});

	modelGridDataStoreObj.load();

	function clearGrouping(v)
	{
		if(v.checked)
		{
			modelGridDataStoreObj.groupBy('active');
		}
		else
		{
			modelGridDataStoreObj.clearGrouping();
		}
	}

	var modelFormPanelObj = new Ext.FormPanel(
	{
		id: 'modelformpanel',
        labelAlign: 'left',
        labelWidth: 60,
        height: 400,
        frame: true,
        bodyStyle: 'padding:0px 0px 0px 0px;',
        items: [
			modelGrid
        ]
    });

	gDialogObj = new Ext.Window(
	{
		id: 'threeDPreviewListDialog',
	  	closable: false,
	  	title: "{/literal}{#str_Title3DPreview#}{literal}",
	  	plain: true,
	  	modal: true,
	  	draggable: true,
	 	resizable: false,
	  	layout: 'fit',
	  	height: 400,
	  	width: 800,
	  	items: modelFormPanelObj,
	  	tools:[
		{
			id: 'clse',
		    qtip: "{/literal}{#str_LabelCloseWindow#}{literal}",
		    handler: function() 
			{
				Ext.getCmp('threeDPreviewListDialog').close();
				product3DPreviewWindowExists = false;
			}
		}]
	});

	Ext.getCmp('threeDPreviewListDialog').show();
}

function statusRenderer(value, p, record)
{
	var className = "";
	if (record.data.hasfileerror == 1)
	{
		className = ' error';
	}

	if (value == 0)
	{
		className = 'class = "inactive ' + className + '"';
		return '{/literal}<span ' + className + '>{#str_LabelInactive#}</span>{literal}';
	}
	else
	{
		return '{/literal}<span class="' + className + '">{#str_LabelActive#}</span>{literal}';
	}
}

function modelTypeRenderer(value, p, record)
{
	var modelTypes = [
		'JSON',
		'DAE',
		'GLTF'
	];

	return '<span>' + modelTypes[value] + '</span>';
}

function generalColumnRenderer(value, p, record)
{
	var className = "";
	if (record.data.hasfileerror == 1)
	{
		className = ' error"';
	}

	if (record.data.active == 0)
	{
		className = 'class = "inactive ' + className + '"';
		return '{/literal}<span ' + className + '>'+value+'</span>{literal}';
	}
	else
	{
		return '{/literal}<span class="' + className + '">' + value + '</span>{literal}';
	}
}

/* add handler */
function onAdd(btn, ev)
{
	if (! modelPreviewEditWindowExists)
	{
		modelPreviewEditWindowExists = true;
		Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=Admin3DPreview.addDisplay&ref={/literal}{$ref}{literal}', '', '', 'initialize', false);
	}
	return false;
}

/* edit handler */
function onEdit(btn, ev)
{
	if (! modelPreviewEditWindowExists)
	{
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(gDialogObj.findById('modelGrid'));
		serverParams['modelid'] = id;

		modelPreviewEditWindowExists = true;
		Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=Admin3DPreview.addDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
	}
	return false;
}

/* edit handler */
function onDelete(btn, ev)
{
	var paramArray = new Object();
	var codes = Ext.taopix.gridSelection2List(gDialogObj.findById('modelGrid'), 'resourcecode', '');

	paramArray['resourcecodelist'] = codes;

	var submitURL = 'index.php?fsaction=Admin3DPreview.deleteModel&ref={/literal}{$ref}{literal}';

	Ext.taopix.formPost(Ext.getCmp('threeDPreviewListDialog'), paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", deleteCallback);
}

function deleteCallback(pDeleted, pActionForm, pActionData)
{
	if (pDeleted)
	{
		Ext.MessageBox.show(
		{
			title: pActionData.result.title,
			msg: "{/literal}{#str_MessageModelsDeleted#}{literal}",
			buttons: Ext.MessageBox.OK,
			icon: Ext.MessageBox.INFO
		});
	}
	else
	{
		Ext.MessageBox.show(
		{
			title: pActionData.result.title,
			msg: pActionData.result.msg,
			buttons: Ext.MessageBox.OK,
			icon: Ext.MessageBox.ERROR
		});
	}

	Ext.getCmp('modelGrid').store.reload();
	Ext.getCmp('productgrid').store.reload();
}

function setModelActiveStatus(btn, ev)
{
	var serverParams = new Object();
	serverParams['ids'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('modelGrid'));
	var active = 0;

	switch (btn.id)
	{
		case 'admin3DModelListActiveButton':
		{
			active = 1;
			break;
		}
		case 'admin3DModelListInactiveButton':
		{
			active = 0;
			break;
		}
	}

	serverParams['active'] = active;

	Ext.taopix.formPost(Ext.getCmp('threeDPreviewListDialog'), serverParams, 'index.php?fsaction=Admin3DPreview.setModelActivateStatus&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageUpdating#}{literal}", modelActivateCallback);
}

function modelActivateCallback(pUpdated, pActionForm, pActionData)
{
	if (pUpdated)
	{
		var gridObj = gDialogObj.findById('modelGrid');
		var dataStore;

		if (gridObj)
		{
			dataStore = gridObj.store;
		}

		if (dataStore)
		{
			dataStore.reload();
		}
	}
}

{/literal}
