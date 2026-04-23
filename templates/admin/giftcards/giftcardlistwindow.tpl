{literal}

function initialize(pParams)
{
	windowLoaded();
}

function windowLoaded()
{
	var panelContents = windowInitialize();
	
	gMainWindowObj = new Ext.Panel(
	{
		id: 'MainWindow',
		title: panelContents[0],
		items: panelContents[1],
		layout: 'fit',
		anchor: '100% 100%',
		tools: 
		[
			{
					id: 'close', 
					handler: function(event, toolEl, panel)
					{ 
						windowClose(); 
						accordianWindowInitialized = false;
					}, 
					qtip: '{/literal}{#str_LabelCloseWindow#}{literal}' 
			}
		],
		baseParams: 
		{ 
			ref: '{/literal}{$ref}{literal}' 
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
	
	if (Ext.getCmp('dialogGiftcards'))
	{
		Ext.getCmp('dialogGiftcards').close();
	}
	
	if (Ext.getCmp('dialogGiftcardsResults'))
	{
		Ext.getCmp('dialogGiftcardsResults').close();
	}
	
}


var windowInitialize = function()
{
	var sessionId = "{/literal}{$ref}{literal}";
	var gridPageSize = 100;
	showResultWindow = false;
	var companyCode = '';
	gitcardEditWindowExists = false;
	
	gridDataStoreObj = new Ext.data.GroupingStore(
	{
		remoteSort: true,
		remoteGroup:true,
		groupField:'companycode',
		proxy: new Ext.data.HttpProxy(
		{
				url: 'index.php?fsaction=AdminGiftCards.listGiftCards&ref=' + sessionId 
		}),
		method:'POST',
		reader: new Ext.taopix.PagedArrayReader(
			{idIndex: 0},
			Ext.data.Record.create(
			[ 
				{name: 'recordid', mapping: 0},
				{name: 'companycode', mapping: 1},
				{name: 'code', mapping: 2},
				{name: 'name', mapping: 3},
				{name: 'startdate', mapping: 4}, 
				{name: 'enddate', mapping: 5}, 
				{name: 'groupcode', mapping: 6}, 
				{name: 'userid', mapping: 7},  
				{name: 'username', mapping: 8}, 
				{name: 'redeemuserid', mapping: 9}, 
				{name: 'redeemusername', mapping: 10}, 
                {name: 'giftcardvalue', mapping: 11}, 
                {name: 'redeemeddate', mapping: 12}, 
				{name: 'isactive', mapping: 13}
			])
		),
		sortInfo:
		{
			field: 'companycode', 
			direction: "ASC"
		},
		listeners:
		{
        	'beforeload':function()
        	{ 
				var companyFilterCmb = Ext.getCmp('companyFilter');
				var appGrid = Ext.getCmp('giftcardGrid');
				if(companyFilterCmb)
				{
					appGrid.store.setBaseParam('companycode', companyFilterCmb.getValue());
				}
    		}
       },
       baseParams:
       {
       		start: 0, 
       		limit: gridPageSize, 
       		companyCode: '',
			csrf_token: Ext.taopix.getCSRFToken()
       },
       autoLoad: true
	});
	
	gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel(
	{ 
		listeners: 
		{
			selectionchange: function(gridCheckBoxSelectionModelObj) 
			{ 
				var gridObj = Ext.getCmp('giftcardGrid');
				
				if (gridCheckBoxSelectionModelObj.getCount() > 0)
				{	
					if (gridCheckBoxSelectionModelObj.getCount() == 1) 
						gridObj.editButton.enable(); 
					else
						 gridObj.editButton.disable(); 
					
					gridObj.activeButton.enable(); 
					gridObj.inactiveButton.enable(); 
					gridObj.deleteButton.enable();
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
	
	var columnRenderer = function(value, p, record, rowIndex, colIndex, store)
	{
		var className = '';
		
		if (record.data.isactive == 0) 
		{
			if (colIndex == 11) 
				value = "{/literal}{#str_LabelInactive#}{literal}";
			
			className = 'class = "inactive"'
		}
		else
		{
			if (colIndex == 11) 
				value = "{/literal}{#str_LabelActive#}{literal}";
		}
		
		return '<span '+className+'>'+value+'</span>';
	};
	
	var gridColumnModelObj = new Ext.grid.ColumnModel(
	{
		defaults:
		{	
			sortable: true, 
			resizable: true 
		},
		columns: 
		[
			gridCheckBoxSelectionModelObj,
		    { id:'companycode', header: "{/literal}{#str_LabelCompany#}{literal}", dataIndex: 'companycode', hidden:true },
		    { header: "{/literal}{#str_LabelCode#}{literal}", dataIndex: 'code', width:150, renderer: columnRenderer },
	        { header: "{/literal}{#str_LabelName#}{literal}", dataIndex: 'name', width:230, renderer: columnRenderer, sortable: false, menuDisabled: true },
	        { header: "{/literal}{#str_LabelGiftcardValue#}{literal}", dataIndex: 'giftcardvalue', width:100, renderer: columnRenderer  },
	        { header: "{/literal}{#str_LabelStartDate#}{literal}", dataIndex: 'startdate', width:100, renderer: columnRenderer  },
	        { header: "{/literal}{#str_LabelEndDate#}{literal}", dataIndex: 'enddate', width:100, renderer: columnRenderer },
	        { header: "{/literal}{#str_LabelLicenseKey#}{literal}", dataIndex: 'groupcode', width:150, renderer: columnRenderer },
	        { header: "{/literal}{#str_LabelCustomer#}{literal}", dataIndex: 'username', width:120, renderer: columnRenderer },
            { header: "{/literal}{#str_LabelRedeemedBy#}{literal}", dataIndex: 'redeemusername', width:120, renderer: columnRenderer },
            { header: "{/literal}{#str_LabelRedeemedDate#}{literal}", dataIndex: 'redeemeddate', width:100, renderer: columnRenderer },
	        { header: "{/literal}{#str_LabelActive#}{literal}", dataIndex: 'isactive', renderer: columnRenderer, align: 'right', width:80}
	    ]
	});
	
	onCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{	
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
			gridDataStoreObj.reload();
			
			if ((Ext.getCmp('dialogGiftcards')) && (Ext.getCmp('dialogGiftcards').isVisible()))
			{
				Ext.getCmp('dialogGiftcards').close();
			}
			
			if (showResultWindow)
			{
				Ext.getCmp('dialogGiftcardsResults').show();
				Ext.getCmp('giftcardsResultGrid').store.reload();
				showResultWindow = false;
			}
		}
	};

	function onDelete()
	{
		var onDeleteConfirmed = function(btn)
		{
			if (btn == "yes") 
			{
				var paramArray = {};
				paramArray['idlist'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('giftcardGrid'));
				Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminGiftCards.delete', "{/literal}{#str_MessageDeleting#}{literal}", onCallback);
			}
		};

		var selRecords = Ext.getCmp('giftcardGrid').selModel.getSelections();
		var codeList = [];
		
		for (var rec = 0; rec < selRecords.length; rec++) 
		{	
			codeList.push("'"+selRecords[rec].data.code+"'");
		}
		
		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_DeleteGiftcardConfirmation#}{literal}".replace("'^0'", codeList.join(', ')), onDeleteConfirmed);
	};

	var onActivate = function(btn, ev)
	{
		var gridObj = Ext.getCmp('giftcardGrid');
		var paramArray = {};
		paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gridObj);
		var active = 0; 
		
		if (btn.id == 'activeButton') 
			active = 1;
		
		paramArray['active'] = active;
		
		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminGiftCards.giftcardActivate', "{/literal}{#str_MessageUpdating#}{literal}", onCallback);
	};

	function onNew()
	{
		var paramArray = [];
		
		if (!gitcardEditWindowExists)
		{
			gitcardEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminGiftCards.addDisplay&ref='+sessionId, paramArray, '', 'initialize', false);            
		}
	};

	function onEdit()
	{
		var paramArray = [];
		paramArray['giftcardid'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('giftcardGrid'));
		
		if (!gitcardEditWindowExists)
		{
			gitcardEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminGiftCards.editDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}
	};

	function onCreate()
	{
		var paramArray = [];
		
		if(!gitcardEditWindowExists)
		{
			gitcardEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminGiftCards.createDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}
	};

	function onImport()
	{
		var paramArray = [];

		if(!gitcardEditWindowExists)
		{
			gitcardEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminGiftCards.importDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}
	};
	
	function onExport()
	{
		var paramArray = {};
		
		location.replace('index.php?fsaction=AdminGiftCards.export&ref='+sessionId);
		return false;
	}
	
	function onCompanyChange()
	{
		var companyCombo = Ext.getCmp('companyFilter');
		var appGrid = Ext.getCmp('giftcardGrid');
		appGrid.getBottomToolbar().changePage(1);
	}
	
	var gridObj = 
	{
		xtype: 'grid',
	   	id: 'giftcardGrid',
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
		ctCls: 'grid',
		view: new Ext.grid.GroupingView(
		{ 
			forceFit:false, 
			groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_LabelItems#}{literal}" : "{/literal}{#str_LabelItem#}{literal}"]})' 
		}),
		columnLines:true,
	    stateId: 'giftcardGrid',
	    tbar: 
	    [	
	    	{ref: '../addButton',	text: "{/literal}{#str_ButtonNew#}{literal}",	iconCls: 'silk-add',	handler: onNew	}, '-',
            {ref: '../editButton',	text: "{/literal}{#str_ButtonEdit#}{literal}",	iconCls: 'silk-pencil',	handler: onEdit, disabled: true	}, '-',
           	{ref: '../deleteButton', text: "{/literal}{#str_ButtonDelete#}{literal}", iconCls: 'silk-delete', handler: onDelete, disabled: true, id:'deleteButton' }, '-',
            {ref: '../activeButton', text: "{/literal}{#str_LabelMakeActive#}{literal}", iconCls: 'silk-lightbulb', handler: onActivate, disabled: true, id:'activeButton'}, '-', 
      	    {ref: '../inactiveButton', text: "{/literal}{#str_LabelMakeInactive#}{literal}", iconCls: 'silk-lightbulb-off',  handler: onActivate, disabled: true, id:'inactiveButton'}, '-',
            {ref: '../createButton',	text: "{/literal}{#str_ButtonCreate#}{literal}",	iconCls: 'silk-page_white_copy',	handler: onCreate	}, '-',
            {ref: '../importButton',	text: "{/literal}{#str_ButtonImportCodes#}{literal}",	iconCls: 'silk-page-white-get',	handler: onImport }, '-',
            {ref: '../exportButton',	text: "{/literal}{#str_ButtonExport#}{literal}",	iconCls: 'silk-page-white-put',	handler: onExport }	            
            {/literal}{if $optionMS == true and $userType==0}{literal} 
			,{xtype:'tbfill'}
    		,new Ext.taopix.CompanyCombo({id:'companyFilter',name: 'companyFilter', emptyText:"{/literal}{#str_LabelCompanyName#}{literal}", options: {ref: sessionId, includeGlobal: '1', includeShowAll:'1', onchange: onCompanyChange} })
    		,{xtype: 'tbspacer', width: 10} 
    		{/literal}{/if}{literal}
        ],
		plugins: 
		[
			new Ext.ux.grid.Search(
			{
				iconCls: 'silk-zoom',
				minChars: 3,
				width: 200,
				autoFocus: true,
				disableIndexes:['recordid', 'companycode','startdate', 'enddate', 'isactive','userid','redeemid','redeemeddate']
			})
		],
		bbar: new Ext.PagingToolbar({ pageSize: gridPageSize, store: gridDataStoreObj, displayInfo: true })    
	};
	
	return ["{/literal}{$title}{literal}", gridObj];

};

{/literal}

