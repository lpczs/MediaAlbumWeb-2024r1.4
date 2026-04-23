{literal}
function initialize(pParams)
{
    var gridObj = Ext.getCmp('maingrid');
	var selRecords = gridObj.selModel.getSelections();
    var groupID = selRecords[0].data.id;



    var previewRecordTemplate = Ext.data.Record.create([
            {name: 'id', mapping: 0},
            {name: 'code', mapping: 1},
            {name: 'name', mapping: 2},
            {name: 'iscollection', mapping: 3},
            {name: 'parentid', mapping: 4}
    ]);

    function columnRenderer(value, p, record, rowIndex, colIndex, store)
	{
		return '<span>' + value + '</span>';
	};

    var previewGridView = new Ext.grid.GridView({
        getRowClass: function(record, rowIndex, rowParams, dataStore)
        {
            var cssClass = '';

            if (record.data.iscollection == false)
            {
                cssClass =  'tpx-components-subgrid';

                if (record.data.code == "{/literal}{#str_LabelAllLayouts#}{literal}")
                {
                    cssClass = cssClass + " tpx-components-alllayoutstext";
                }

                return cssClass;
            }
        }
    });

    var previewGridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
			sortable: false,
			resizable: true
		},
		columns: [
			{header: "{/literal}{#str_LabelCode#}{literal}", width: 200, dataIndex: 'code', renderer: columnRenderer},
			{header: "{/literal}{#str_LabelName#}{literal}", width: 200, dataIndex: 'name', renderer: columnRenderer}
		]
	});

    var previewGridDataStoreObj = new Ext.data.Store({
    remoteSort: true,
    proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminProductGroups.getPreviewGridData&ref={/literal}{$ref}{literal}&groupid=' + groupID }),
    reader: new Ext.taopix.PagedArrayReader({
            idIndex: 0
        },
        previewRecordTemplate
    )
    });

    previewGridDataStoreObj.load();

    var previewGridPanel = new Ext.grid.GridPanel({
        autoExpandMax: 5000, // Set the max width an auto expand column can be over the 1000px default.
		autoExpandColumn: 1,
		id: 'previewgrid',
        height: 455,
        autoWidth: true,
		store: previewGridDataStoreObj,
		cm: previewGridColumnModelObj,
        view: previewGridView,
        disableSelection: true,
        style:'border:1px solid #99BBE8; margin-top:6px;',
		enableColLock: false,
		draggable: false,
		enableColumnHide: false,
		enableColumnMove: false,
		enableHdMenu: false,
		trackMouseOver: false,
		stripeRows: true,
		columnLines: true,
		ctCls: 'grid'
    });

    var dialogFormPanelObj = new Ext.FormPanel(
	{
		id: 'productGroupForm',
        labelAlign: 'left',
		labelWidth: 130,
		autoHeight: true,
        frame: true,
        bodyStyle: 'padding-left:5px;',
        items: [
			previewGridPanel
		]
    });

    /* create modal window for add and edit */
	var gDialogObj = new Ext.Window({
		id: 'dialog',
		closable: false,
		plain: true,
		modal: true,
		draggable: true,
		title: "{/literal}{$title}{literal}",
		resizable: false,
		layout: 'fit',
		height: 'auto',
        title: '{/literal}{#str_LabelGroupPreview#}{literal}',
		width: 1000,
		items: dialogFormPanelObj,
		listeners: {
			'close': {
				fn: function(){
					groupEditWindowExists = false;
				}
			}
		},
		buttons:
		[
			{
				text: "{/literal}{#str_ButtonOk#}{literal}",
				handler: function()
				{
					gDialogObj.close();
				}
            }
		]
	});

	gDialogObj.show();
}

{/literal}