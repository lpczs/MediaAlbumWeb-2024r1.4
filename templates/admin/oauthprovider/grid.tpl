var providerList = {$providerList};

function initialize(params)
{
    providerWindowExists = false;
    var sessionId = "{$ref}";

    var onAdd = function() {
        if (providerWindowExists)
        {
            return;
        }

        providerWindowExists = true;
        var serverParams = {
            id: 0
        };
        Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminOAuthProvider.getProvider&ref={$ref}', serverParams, '', 'initialize', false);
    };

    var onEdit = function() {
        if (providerWindowExists) {
            return;
        }

        var id = Ext.taopix.gridSelection2IDList(Ext.getCmp('oauthProvider'));

        // We do not want to try and open the edit window if the id is empty or a selection.
        if ('' === id || -1 !== id.indexOf(',')) {
            return;
        }
        providerWindowExists = true;
        var serverParams = {
            id: id
        };
        Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminOAuthProvider.getProvider&ref={$ref}', serverParams, '', 'initialize', false);
    };

    var onDelete = function() {
        var gridObj = Ext.getCmp('oauthProvider');
		var dataStore = gridObj.store;

		var selRecords = gridObj.selModel.getSelections();
		var codeList = '';

		for (var rec = 0; rec < selRecords.length; rec++)
		{
			codeList = codeList + selRecords[rec].data.providerName;

			if (rec != selRecords.length - 1)
			{
				codeList = codeList + ',';
			}
		}

		var message = "{#str_MessageProviderDeleteConfirmation#}";
		message = message.replace("^0", codeList);

		dataStore.load();
		Ext.MessageBox.confirm("{#str_LabelConfirmation#}", message, onDeleteResult);
    };

    function onDeleteResult(btn)
	{
		if (btn == "yes")
		{
			var params = {
			    id: Ext.taopix.gridSelection2IDList(Ext.getCmp('oauthProvider'))
            };

			Ext.taopix.formPost(gMainWindowObj, params, 'index.php?fsaction=AdminOAuthProvider.delete', "{#str_MessageDeleting#}", onDeleteCallback);
		}
	}

	function onDeleteCallback(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated == true)
		{
			var gridObj = Ext.getCmp('oauthProvider');
			var dataStore = gridObj.store;
			dataStore.reload();
		}

        if (pActionData.result.hasOwnProperty('msg') && pActionData.result.msg != '') {
            Ext.MessageBox.show({ title: "",	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
        }
	}

    var providerRenderer = function(value, p, record, rowIndex, colIndex, store) {
        if (providerList.hasOwnProperty(value)) {
            return providerList[value];
        }

        return "{#str_LabelProviderUnknown#}";
    }

    var columnRenderer = function(value, p, record, rowIndex, colIndex, store) {
        return value;
    };

    gridDataStoreObj = new Ext.data.Store({
        remoteSort: true,
        proxy: new Ext.data.HttpProxy({
            url: 'index.php?fsaction=AdminOAuthProvider.getGridData&ref=' + sessionId
        }),
        method:'POST',
        reader: new Ext.taopix.PagedArrayReader(
            {
                idIndex: 0
            },
            Ext.data.Record.create([
                {
                    name:'id',
                    mapping: 0
                },
                {
                    name: 'providerName',
                    mapping: 1
                },
                {
                    name: 'provider',
                    mapping: 2
                }
            ])
        ),
        baseParams:{
            csrf_token: Ext.taopix.getCSRFToken()
        }
    });
    gridDataStoreObj.load();

    gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
        listeners: {
            selectionchange: function(gridCheckBoxSelectionModelObj) {
                if (gridCheckBoxSelectionModelObj.getCount() > 0) {
                    if (gridCheckBoxSelectionModelObj.getCount() == 1) {
                        gridObj.editButton.enable();
                        gridObj.deleteButton.enable();
                    } else {
                        gridObj.editButton.disable();
                    }
                } else {
                    gridObj.editButton.disable();
                    gridObj.deleteButton.disable();
                }
            }
        }
    });

    var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
            sortable: true,
            resizable: true
        },
		columns: [
		    gridCheckBoxSelectionModelObj,
	    	{
	    	    header: "{#str_LabelProviderName#}",
	    	    dataIndex: 'providerName',
	    	    width:270,
	    	    renderer: columnRenderer,
	    	    menuDisabled: true,
	    	    sortable: false
            },
        	{
        	    header: "{#str_LabelProvider#}",
                dataIndex: 'provider',
                width:270,
                renderer: providerRenderer,
                menuDisabled: true,
                sortable: false
            }
        ]
	});

    var gridObj = new Ext.grid.GridPanel({
        id: 'oauthProvider',
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
        stateId: 'oauthProviderId',
        ctCls: 'grid',
        tbar:
        [
            {
                ref: '../addButton',
                text: "{#str_ButtonAdd#}",
                iconCls: 'silk-add',
                handler: onAdd
            },
            '-',
            {
                ref: '../editButton',
                text: "{#str_ButtonEdit#}",
                iconCls: 'silk-pencil',
                handler: onEdit,
                disabled: true
            },
            '-',
            {
                ref: '../deleteButton',
                text: "{#str_ButtonDelete#}",
                iconCls: 'silk-delete',
                handler: onDelete,
                disabled: true,
            }
        ]
    });

    gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{#str_SectionTitleAdminOAuthProvider#}",
		items: gridObj,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{
		    id: 'close',
		    handler: function(event, toolEl, panel) {
		        windowClose();
		        accordianWindowInitialized = false;
            },
            qtip: '{#str_LabelCloseWindow#}'
        }],
		baseParams: {
            ref: '{$ref}'
        }
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