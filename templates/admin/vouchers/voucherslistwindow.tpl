{literal}

{/literal}{if $promotionid == 0}{literal}

function initialize(pParams)
{
	windowLoaded();
}

{/literal}{/if}{literal}

function windowLoaded()
{
	var panelContents = windowInitialize();
	
	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: panelContents[0],
		items: panelContents[1],
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
	
	if (Ext.getCmp('dialogVouchers'))
	{
		Ext.getCmp('dialogVouchers').close();
	}
	
	if (Ext.getCmp('dialogVouchersResults'))
	{
		Ext.getCmp('dialogVouchersResults').close();
	}
	
}


var windowInitialize = function()
{
	var sessionId = "{/literal}{$ref}{literal}";
	var gridPageSize = 100;
	gPromotionID = {/literal}{$promotionid}{literal};
	showResultWindow = false;
	var companyCode = '';
	singleVoucherEditWindowExists = false;
	
	gridDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		{/literal}{if $promotionid == 0}{literal} 
		remoteGroup:true,
		groupField:'companyCode',
		{/literal}{/if}{literal} 
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminVouchers.listVouchers&ref=' + sessionId + '&promotionid='+gPromotionID }),
		method:'POST',
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([ 
				{name:'recordid', mapping: 0},
				{name: 'companyCode', mapping: 1},
				{name: 'voucherCode', mapping: 2},
				{name: 'voucherName', mapping: 3},
				{name: 'voucherDescription', mapping: 4},
                {name: 'voucherType', mapping: 5},
                {name: 'defaultDiscount', mapping: 6},
				{name: 'startDate', mapping: 7},
				{name: 'endDate', mapping: 8},
				{name: 'productCode', mapping: 9},
				{name: 'productName', mapping: 10},
				{name: 'groupCode', mapping: 11},
				{name: 'userId', mapping: 12},
				{name: 'userName', mapping: 13},
				{name: 'repeatType', mapping: 14},
				{name: 'discountSection', mapping: 15},
				{name: 'discountType', mapping: 16},
				{name: 'discountValue', mapping: 17},
				{name: 'usageCount', mapping: 18},
				{name: 'status', mapping: 19},
				{name: 'isActive', mapping: 20}
			])
		),
		sortInfo:{field: 'companyCode', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()},
		listeners:
		{
        	'beforeload':function(pStore, pOptions)
        	{ 
				var companyFilterCmb = Ext.getCmp('companyFilter');
				var appGrid = Ext.getCmp('vouchersGrid');
				if(companyFilterCmb)
				{
					appGrid.store.lastOptions.params['companyCode'] = companyFilterCmb.getValue();
					appGrid.store.setBaseParam('companyCode', companyFilterCmb.getValue());
				}

				checkHideInactiveButton(pStore, pOptions);
    		}
        }
	}); 
	gridDataStoreObj.load({params:{start:0, limit:gridPageSize, companyCode: ''}});
	
	gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel(
	{ 
		listeners: 
		{
			selectionchange: function(gridCheckBoxSelectionModelObj) 
			{ 
				var gridObj = Ext.getCmp('vouchersGrid');
				
				if (gridCheckBoxSelectionModelObj.getCount() > 0)
				{
					if (gridCheckBoxSelectionModelObj.getCount() == 1) 
						gridObj.editButton.enable(); 
					else 
						gridObj.editButton.disable(); 
					
					gridObj.activeButton.enable(); gridObj.inactiveButton.enable(); gridObj.deleteButton.enable();
				}
				else 
				{ 
					gridObj.activeButton.disable(); gridObj.inactiveButton.disable(); gridObj.editButton.disable();  gridObj.deleteButton.disable(); 
				}
			}
		}
	});
	
	var columnRenderer = function(value, p, record, rowIndex, colIndex, store)
	{
		var className = '';
		
		if (colIndex == 14)
		{
			if (value * 1 == 0)
			{
				value = '';
			}
		}
		
		if (colIndex == 15)
		{
			if (record.data.status == 0)
			{
				value = "{/literal}{#str_Error#}{literal}";
			}
			else
			{
				if (record.data.isActive == 0) 
				{
					value = "{/literal}{#str_LabelInactive#}{literal}";
					className = 'class = "inactive"'
				}
				else
				{
					value = "{/literal}{#str_LabelActive#}{literal}";
				}
			}
			
		}
		
		
		if(colIndex == 6)
		{
		    if(record.data.defaultDiscount == 1)
		    {
		        value = "{/literal}{#str_LabelYes#}{literal}";
		    }
		    else
		    {
                value = "{/literal}{#str_LabelNo#}{literal}";
		    }
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
		    { id:'companyCode', header: "{/literal}{#str_LabelCompany#}{literal}", dataIndex: 'companyCode', hidden:true },
		    { header: "{/literal}{#str_LabelCode#}{literal}", dataIndex: 'voucherCode', width:230, renderer: columnRenderer },
	        { id:'voucherName', header: "{/literal}{#str_LabelName#}{literal}", dataIndex: 'voucherName', width:200, renderer: columnRenderer, sortable: false, menuDisabled: true },
			{ header: "{/literal}{#str_LabelDescription#}{literal}", dataIndex: 'voucherDescription', width:200, renderer: columnRenderer, sortable: false, menuDisabled: true },
            { header: "{/literal}{#str_LabelVoucherType#}{literal}", dataIndex: 'voucherType', width:100, renderer: columnRenderer },
            { header: "{/literal}{#str_LabelDefaultDiscount#}{literal}", dataIndex: 'defaultDiscount', width:100, renderer: columnRenderer },
	        { header: "{/literal}{#str_LabelStartDate#}{literal}", dataIndex: 'startDate', width:120, renderer: columnRenderer {/literal}{if $promotionid > 0}{literal}, hidden:true {/literal}{/if}{literal}  },
	        { header: "{/literal}{#str_LabelEndDate#}{literal}", dataIndex: 'endDate', width:120, renderer: columnRenderer {/literal}{if $promotionid > 0}{literal}, hidden:true {/literal}{/if}{literal}  },
	        { header: "{/literal}{#str_LabelLicenseKey#}{literal}", dataIndex: 'groupCode', width:150, renderer: columnRenderer },
	        { header: "{/literal}{#str_LabelCustomer#}{literal}", dataIndex: 'userName', width:120, renderer: columnRenderer },
	        { header: "{/literal}{#str_LabelRepeatType#}{literal}", dataIndex: 'repeatType', width:100, renderer: columnRenderer },
	        { header: "{/literal}{#str_LabelDiscountSection#}{literal}", dataIndex: 'discountSection', width:100, renderer: columnRenderer },
	        { header: "{/literal}{#str_LabelDiscountType#}{literal}", dataIndex: 'discountType', renderer: columnRenderer, width:100, sortable: false, menuDisabled: true},
	        { header: "{/literal}{#str_LabelUsedTimes#}{literal}", dataIndex: 'usageCount', width:60, renderer: columnRenderer, align: 'right' },
	        { header: "{/literal}{#str_LabelStatus#}{literal}", dataIndex: 'isActive', renderer: columnRenderer, align: 'right', width:80}
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
			
			if ((Ext.getCmp('dialogVouchers')) && (Ext.getCmp('dialogVouchers').isVisible()))
			{
				Ext.getCmp('dialogVouchers').close();
			}
			
			if (showResultWindow)
			{
				Ext.getCmp('dialogVouchersResults').show();
				Ext.getCmp('vouchersResultGrid').store.reload();
				showResultWindow = false;
			}

			Ext.getCmp('vouchersGrid').store.reload();
		}
	};

	function onDelete()
	{
		var onDeleteConfirmed = function(btn)
		{
			if (btn == "yes") {
				var paramArray = {};
				paramArray['idlist'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('vouchersGrid'));
				paramArray['promotionId'] = gPromotionID;
				Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminVouchers.delete', "{/literal}{#str_MessageDeleting#}{literal}", onCallback);
			}
		};

		var gridObj = Ext.getCmp('vouchersGrid');
		var selRecords = gridObj.selModel.getSelections();
		var codeList = [];
		
		for (var rec = 0; rec < selRecords.length; rec++) {	codeList.push("'"+selRecords[rec].data.voucherCode+"'");}
		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_DeleteVoucherConfirmation#}{literal}".replace("'^0'", codeList.join(', ')), onDeleteConfirmed);
	};

	
	var onActivate = function(btn, ev)
	{
		var gridObj = Ext.getCmp('vouchersGrid');
		var paramArray = {};
		paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gridObj);
		var active = 0; 
		if (btn.id == 'activeButton') active = 1;
		paramArray['active'] = active;
		paramArray['promotionId'] = gPromotionID;
		
		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminVouchers.voucherActivate', "{/literal}{#str_MessageUpdating#}{literal}", onCallback);
	};

	
	function onDeleteExpired()
	{
		var onDeleteConfirmed = function(btn)
		{
			if (btn == "yes") {
				var paramArray = {};
				paramArray['promotionId'] = gPromotionID;
				Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminVouchersSingle.deleteExpired', "{/literal}{#str_MessageDeleting#}{literal}", onCallback);
			}
		};

		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_DeleteExpiredVoucherConfirmation#}{literal}", onDeleteConfirmed);
	};


	function onNew()
	{
		var paramArray = [];
		paramArray['promotionId'] = gPromotionID;
		
		if (!singleVoucherEditWindowExists)
		{
			singleVoucherEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminVouchers.addDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}
	};

	function onEdit()
	{
		var paramArray = [];
		paramArray['promotionId'] = gPromotionID;
		paramArray['id'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('vouchersGrid'));
		
		if (!singleVoucherEditWindowExists)
		{
			singleVoucherEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminVouchers.editDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}
	};

	function onCreate()
	{
		var paramArray = [];
		paramArray['promotionId'] = gPromotionID;
		showResultWindow = true;
		
		if(!singleVoucherEditWindowExists)
		{
			singleVoucherEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminVouchers.createDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}
	};

	function onImport()
	{
		var paramArray = [];
		paramArray['promotionId'] = gPromotionID;

		if(!singleVoucherEditWindowExists)
		{
			singleVoucherEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminVouchers.importDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}
	};
	
	function onExport()
	{
		var paramArray = {};
		
		location.replace('index.php?fsaction=AdminVouchers.export&ref='+sessionId+'&promotionid='+gPromotionID);
		return false;
	}
	
	function onCompanyChange()
	{
		var companyCombo = Ext.getCmp('companyFilter');
		var appGrid = Ext.getCmp('vouchersGrid');
		appGrid.getBottomToolbar().changePage(1);
	}

	function onHideInactive(btn, ev)
	{
		// get the datastore and default hide inactive to false
		var gridDataStore = Ext.getCmp('vouchersGrid').store;
		var hideInactive = 0;

		if (btn.pressed)
		{
			// set to true and update tooltip
			hideInactive = 1;
			Ext.getCmp('hideInactiveButton').setTooltip('{/literal}{#str_TooltipShowInactiveItems#}{literal}');
		}
		else
		{
			Ext.getCmp('hideInactiveButton').setTooltip('{/literal}{#str_TooltipHideInactiveItems#}{literal}');			
		}

		// manually set the last options to allow reload to be passed hide inactive
		gridDataStore.lastOptions.params['hideInactive'] = hideInactive;

		gridDataStore.reload({params: gridDataStore.lastOptions.params});
	}

	function checkHideInactiveButton (pStore, pOptions)
	{
		var hideInactiveButton = Ext.getCmp('hideInactiveButton');

		if (typeof hideInactiveButton !== 'undefined')
		{
			// on detected query turn button off
			if ((pStore.baseParams.query != '') && (typeof pStore.baseParams.query != 'undefined'))
			{
				hideInactiveButton.toggle(false);
				hideInactiveButton.disable();
				hideInactiveButton.setTooltip({text: '{/literal}{#str_TooltipHideInactiveItemsIsDisabledForSearchResults#}{literal}', autoHide: true, id: 'hideInactiveDisabledTooltip'});
			}
			else
			{
				// search field has been emptied for the first time since it was last filled
				if (hideInactiveButton.disabled == true)
				{
					hideInactiveButton.enable();
					var gridDataStore = Ext.getCmp('vouchersGrid').store;

					// restore button to its state before the search 
					if (gridDataStore.lastOptions.params['hideInactive'] == 1)
					{
						hideInactiveButton.toggle(true);
						hideInactiveButton.setTooltip('{/literal}{#str_TooltipShowInactiveItems#}{literal}');					
					}
					else
					{
						hideInactiveButton.toggle(false);
						hideInactiveButton.setTooltip('{/literal}{#str_TooltipHideInactiveItems#}{literal}');					
					}
				}
			}
		}
	}

	function carryHideInactiveIntoPagingToolbarRefresh(pToolbar, pParams)
	{
		var hideInactiveButton = Ext.getCmp('hideInactiveButton');

		if ((typeof hideInactiveButton.pressed != 'undefined') && (hideInactiveButton.pressed == true))
		{
			pParams.hideInactive = 1;
		}
		else
		{
			pParams.hideInactive = 0;
		}
	}
	
	var gridObj = {
		xtype: 'grid',
	   	id: 'vouchersGrid',
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
		view: new Ext.grid.GroupingView({ 
			forceFit:false, 
			groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_LabelItems#}{literal}" : "{/literal}{#str_LabelItem#}{literal}"]})',
			getRowClass: function(record, rowIndex, rowParams, dataStore)
			{
				if (record.data.status == 0)
				{
					return 'error';
				}
			}}),
		columnLines:true,
	    stateId: 'vouchersGrid',
	    tbar: [	{ref: '../addButton',	text: "{/literal}{#str_ButtonNew#}{literal}",	iconCls: 'silk-add',	handler: onNew	}, '-',
	            {ref: '../editButton',	text: "{/literal}{#str_ButtonEdit#}{literal}",	iconCls: 'silk-pencil',	handler: onEdit, disabled: true	}, '-',
	           	{ref: '../deleteButton', text: "{/literal}{#str_ButtonDelete#}{literal}", iconCls: 'silk-delete', handler: onDelete, disabled: true, id:'deleteButton' }, '-',
	            {ref: '../activeButton', text: "{/literal}{#str_LabelMakeActive#}{literal}", iconCls: 'silk-lightbulb', handler: onActivate, disabled: true, id:'activeButton'}, '-', 
	      	    {ref: '../inactiveButton', text: "{/literal}{#str_LabelMakeInactive#}{literal}", iconCls: 'silk-lightbulb-off',  handler: onActivate, disabled: true, id:'inactiveButton'}, '-',

	            {/literal}{if $promotionid==0}{literal}
	            {ref: '../deleteExpiredButton',	text: "{/literal}{#str_ButtonDeleteExpired#}{literal}",	iconCls: 'silk-date-delete',	handler: onDeleteExpired }, '-',
	            {/literal}{/if}{literal}
	            {ref: '../createButton',	text: "{/literal}{#str_ButtonCreate#}{literal}",	iconCls: 'silk-page_white_copy',	handler: onCreate	}, '-',
	            {ref: '../importButton',	text: "{/literal}{#str_ButtonImportCodes#}{literal}",	iconCls: 'silk-page-white-get',	handler: onImport }, '-',
	            {ref: '../exportButton',	text: "{/literal}{#str_ButtonExport#}{literal}",	iconCls: 'silk-page-white-put',	handler: onExport }
	            ,{xtype:'tbfill'}

	            {/literal}{if $optionMS == true and $userType==0 and $promotionid == 0}{literal} 
        		,new Ext.taopix.CompanyCombo({id:'companyFilter',name: 'companyFilter', emptyText:"{/literal}{#str_LabelCompanyName#}{literal}", options: {ref: sessionId, includeGlobal: '1', includeShowAll:'1', onchange: onCompanyChange} })
        		,{xtype: 'tbspacer', width: 10} 
        		{/literal}{/if}{literal}
				,{
				id:'hideInactiveButton',
				ref: '../hideInactiveButton',
				tooltip: '{/literal}{#str_TooltipHideInactiveItems#}{literal}',
				iconCls: 'hideInactiveButton',
				handler: onHideInactive,
				enableToggle: true,
				xtype: 'button',
				ctCls:'x-toolbar-standardbutton'
				}
        ],

		plugins: [
			new Ext.ux.grid.Search({
				iconCls: 'silk-zoom',
				minChars: 3,
				width: 200,
				autoFocus: true,
				disableIndexes:['recordid', 'voucherType', 'companyCode','startDate', 'endDate', 'userId', 'repeatType', 'discountSection', 'discountType', 'defaultDiscount', 'discountValue', 'isActive', 'usageCount'],
			})
		],
		bbar: new Ext.PagingToolbar({ pageSize: gridPageSize, store: gridDataStoreObj, displayInfo: true, listeners: { beforechange: carryHideInactiveIntoPagingToolbarRefresh } })    
	};
	
	return ["{/literal}{$title}{literal}", gridObj];

};

{/literal}

