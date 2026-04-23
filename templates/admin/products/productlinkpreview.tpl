{literal}
function initialize(pParams)
{
    var gridObj = Ext.getCmp('productgrid');
	var selRecords = gridObj.selModel.getSelections();
    var productcode = selRecords[0].data.code;

    var previewRecordTemplate = Ext.data.Record.create([
            {name: 'id', mapping: 0},
            {name: 'code', mapping: 1},
            {name: 'name', mapping: 2},
            {name: 'companycode', mapping: 3}
    ]);

    function columnRenderer(value, p, record, rowIndex, colIndex, store)
	{
		return '<span>' + value + '</span>';
	};


    var previewGridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
			sortable: false,
			resizable: true
		},
		columns: [
            {/literal}{if $optionms}{literal}
                {header: "{/literal}{#str_LabelCompany#}{literal}", width: 200, dataIndex: 'companycode', renderer: columnRenderer},
            {/literal}{/if}{literal}
			{header: "{/literal}{#str_LabelCode#}{literal}", width: 200, dataIndex: 'code', renderer: columnRenderer},
			{header: "{/literal}{#str_LabelName#}{literal}", width: 200, dataIndex: 'name', renderer: columnRenderer}
		]
	});

    var previewGridDataStoreObj = new Ext.data.Store({
    remoteSort: true,
    proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminProducts.getLinkingPreviewGridData&ref={/literal}{$ref}{literal}&productcode=' + productcode }),
    reader: new Ext.taopix.PagedArrayReader({
            idIndex: 0
        },
        previewRecordTemplate
    )
    });

    previewGridDataStoreObj.load();

    var previewGridPanel = new Ext.grid.GridPanel({
        autoExpandMax: 5000, // Set the max width an auto expand column can be over the 1000px default.
        {/literal}{if $optionms}
		    autoExpandColumn: 2,
        {else}
            autoExpandColumn: 1,
        {/if}{literal}
		id: 'previewgrid',
        height: 455,
        autoWidth: true,
		store: previewGridDataStoreObj,
		cm: previewGridColumnModelObj,
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
		id: 'linkingForm',
        labelAlign: 'left',
		labelWidth: 130,
		autoHeight: true,
        frame: true,
        bodyStyle: 'padding-left:5px;',
        items: [
			previewGridPanel
		]
    });

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
        title: '{/literal}{#str_TitleProductLinkingPreview#}{literal}',
		width: 1000,
		items: dialogFormPanelObj,
		buttons:
		[
			{
				text: "{/literal}{#str_ButtonOk#}{literal}",
				handler: function()
				{
					gDialogObj.close();
                    productLinkingPreviewWindowExists = false;
				}
            }
		]
	});

	gDialogObj.show();
}

{/literal}