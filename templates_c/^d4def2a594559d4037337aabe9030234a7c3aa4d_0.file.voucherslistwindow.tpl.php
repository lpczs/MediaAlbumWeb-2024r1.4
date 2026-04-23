<?php
/* Smarty version 4.5.3, created on 2026-03-25 09:06:21
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\vouchers\voucherslistwindow.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69c3a58d401c16_32552130',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd4def2a594559d4037337aabe9030234a7c3aa4d' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\vouchers\\voucherslistwindow.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69c3a58d401c16_32552130 (Smarty_Internal_Template $_smarty_tpl) {
?>

<?php if ($_smarty_tpl->tpl_vars['promotionid']->value == 0) {?>

function initialize(pParams)
{
	windowLoaded();
}

<?php }?>

function windowLoaded()
{
	var panelContents = windowInitialize();
	
	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: panelContents[0],
		items: panelContents[1],
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose(); accordianWindowInitialized = false;}, qtip: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCloseWindow');?>
' }],
		baseParams: { ref: '<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
' }
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
	var sessionId = "<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
";
	var gridPageSize = 100;
	gPromotionID = <?php echo $_smarty_tpl->tpl_vars['promotionid']->value;?>
;
	showResultWindow = false;
	var companyCode = '';
	singleVoucherEditWindowExists = false;
	
	gridDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		<?php if ($_smarty_tpl->tpl_vars['promotionid']->value == 0) {?> 
		remoteGroup:true,
		groupField:'companyCode',
		<?php }?> 
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
				value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_Error');?>
";
			}
			else
			{
				if (record.data.isActive == 0) 
				{
					value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInactive');?>
";
					className = 'class = "inactive"'
				}
				else
				{
					value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
";
				}
			}
			
		}
		
		
		if(colIndex == 6)
		{
		    if(record.data.defaultDiscount == 1)
		    {
		        value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelYes');?>
";
		    }
		    else
		    {
                value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNo');?>
";
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
		    { id:'companyCode', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", dataIndex: 'companyCode', hidden:true },
		    { header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCode');?>
", dataIndex: 'voucherCode', width:230, renderer: columnRenderer },
	        { id:'voucherName', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
", dataIndex: 'voucherName', width:200, renderer: columnRenderer, sortable: false, menuDisabled: true },
			{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDescription');?>
", dataIndex: 'voucherDescription', width:200, renderer: columnRenderer, sortable: false, menuDisabled: true },
            { header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelVoucherType');?>
", dataIndex: 'voucherType', width:100, renderer: columnRenderer },
            { header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDefaultDiscount');?>
", dataIndex: 'defaultDiscount', width:100, renderer: columnRenderer },
	        { header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStartDate');?>
", dataIndex: 'startDate', width:120, renderer: columnRenderer <?php if ($_smarty_tpl->tpl_vars['promotionid']->value > 0) {?>, hidden:true <?php }?>  },
	        { header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEndDate');?>
", dataIndex: 'endDate', width:120, renderer: columnRenderer <?php if ($_smarty_tpl->tpl_vars['promotionid']->value > 0) {?>, hidden:true <?php }?>  },
	        { header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKey');?>
", dataIndex: 'groupCode', width:150, renderer: columnRenderer },
	        { header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCustomer');?>
", dataIndex: 'userName', width:120, renderer: columnRenderer },
	        { header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRepeatType');?>
", dataIndex: 'repeatType', width:100, renderer: columnRenderer },
	        { header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDiscountSection');?>
", dataIndex: 'discountSection', width:100, renderer: columnRenderer },
	        { header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDiscountType');?>
", dataIndex: 'discountType', renderer: columnRenderer, width:100, sortable: false, menuDisabled: true},
	        { header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUsedTimes');?>
", dataIndex: 'usageCount', width:60, renderer: columnRenderer, align: 'right' },
	        { header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
", dataIndex: 'isActive', renderer: columnRenderer, align: 'right', width:80}
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
				Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminVouchers.delete', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleting');?>
", onCallback);
			}
		};

		var gridObj = Ext.getCmp('vouchersGrid');
		var selRecords = gridObj.selModel.getSelections();
		var codeList = [];
		
		for (var rec = 0; rec < selRecords.length; rec++) {	codeList.push("'"+selRecords[rec].data.voucherCode+"'");}
		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DeleteVoucherConfirmation');?>
".replace("'^0'", codeList.join(', ')), onDeleteConfirmed);
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
		
		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminVouchers.voucherActivate', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageUpdating');?>
", onCallback);
	};

	
	function onDeleteExpired()
	{
		var onDeleteConfirmed = function(btn)
		{
			if (btn == "yes") {
				var paramArray = {};
				paramArray['promotionId'] = gPromotionID;
				Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminVouchersSingle.deleteExpired', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleting');?>
", onCallback);
			}
		};

		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DeleteExpiredVoucherConfirmation');?>
", onDeleteConfirmed);
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
			Ext.getCmp('hideInactiveButton').setTooltip('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipShowInactiveItems');?>
');
		}
		else
		{
			Ext.getCmp('hideInactiveButton').setTooltip('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipHideInactiveItems');?>
');			
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
				hideInactiveButton.setTooltip({text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipHideInactiveItemsIsDisabledForSearchResults');?>
', autoHide: true, id: 'hideInactiveDisabledTooltip'});
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
						hideInactiveButton.setTooltip('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipShowInactiveItems');?>
');					
					}
					else
					{
						hideInactiveButton.toggle(false);
						hideInactiveButton.setTooltip('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipHideInactiveItems');?>
');					
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
			groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItems');?>
" : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItem');?>
"]})',
			getRowClass: function(record, rowIndex, rowParams, dataStore)
			{
				if (record.data.status == 0)
				{
					return 'error';
				}
			}}),
		columnLines:true,
	    stateId: 'vouchersGrid',
	    tbar: [	{ref: '../addButton',	text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonNew');?>
",	iconCls: 'silk-add',	handler: onNew	}, '-',
	            {ref: '../editButton',	text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonEdit');?>
",	iconCls: 'silk-pencil',	handler: onEdit, disabled: true	}, '-',
	           	{ref: '../deleteButton', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDelete');?>
", iconCls: 'silk-delete', handler: onDelete, disabled: true, id:'deleteButton' }, '-',
	            {ref: '../activeButton', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeActive');?>
", iconCls: 'silk-lightbulb', handler: onActivate, disabled: true, id:'activeButton'}, '-', 
	      	    {ref: '../inactiveButton', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeInactive');?>
", iconCls: 'silk-lightbulb-off',  handler: onActivate, disabled: true, id:'inactiveButton'}, '-',

	            <?php if ($_smarty_tpl->tpl_vars['promotionid']->value == 0) {?>
	            {ref: '../deleteExpiredButton',	text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDeleteExpired');?>
",	iconCls: 'silk-date-delete',	handler: onDeleteExpired }, '-',
	            <?php }?>
	            {ref: '../createButton',	text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCreate');?>
",	iconCls: 'silk-page_white_copy',	handler: onCreate	}, '-',
	            {ref: '../importButton',	text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonImportCodes');?>
",	iconCls: 'silk-page-white-get',	handler: onImport }, '-',
	            {ref: '../exportButton',	text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonExport');?>
",	iconCls: 'silk-page-white-put',	handler: onExport }
	            ,{xtype:'tbfill'}

	            <?php if ($_smarty_tpl->tpl_vars['optionMS']->value == true && $_smarty_tpl->tpl_vars['userType']->value == 0 && $_smarty_tpl->tpl_vars['promotionid']->value == 0) {?> 
        		,new Ext.taopix.CompanyCombo({id:'companyFilter',name: 'companyFilter', emptyText:"<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompanyName');?>
", options: {ref: sessionId, includeGlobal: '1', includeShowAll:'1', onchange: onCompanyChange} })
        		,{xtype: 'tbspacer', width: 10} 
        		<?php }?>
				,{
				id:'hideInactiveButton',
				ref: '../hideInactiveButton',
				tooltip: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipHideInactiveItems');?>
',
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
	
	return ["<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
", gridObj];

};



<?php }
}
