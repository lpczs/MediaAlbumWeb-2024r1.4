{literal}
function onVouchersShow()
{
	var panelContents = windowInitialize();
	
	var gDialogVouchersPromo = new Ext.Window({
		id: 'dialogVouchersPromo',
		closable:false,
		plain:true,
		title: panelContents[0],
		items: panelContents[1],
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		width: 1200,
		height: 500,
		cls: 'left-right-buttons',
		buttons: 
		[
			{	
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(btn, ev){ gDialogVouchersPromo.close(); },
				cls: 'x-btn-right'
			}
		],
		listeners: {
			'close': function(){Ext.getCmp('promotionsGrid').getStore().reload(); }
		}
	});
	
	gDialogVouchersPromo.show();	
}

function initialize(pParams)
{
	sessionIdPromo = "{/literal}{$ref}{literal}";
	var gridPageSize = 100;
	voucherPromotionEditWindowExists = false;
	
	gridDataStoreObjPromo = new Ext.data.GroupingStore({
		remoteSort: true,
		remoteGroup:true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminVouchersPromotion.listPromotions&ref=' + sessionIdPromo }),
		method:'POST',
		groupField:'companyCode',
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([ 
				{name:'recordid', mapping: 0},
				{name: 'companyCode', mapping: 1},
				{name: 'promoCode', mapping: 2},
				{name: 'promoName', mapping: 3},
				{name: 'startDate', mapping: 4}, 
				{name: 'endDate', mapping: 5}, 
				{name: 'promoVoucherCount', mapping: 6}, 
				{name: 'isActive', mapping: 7}
			])
		),
		sortInfo:{field: 'companyCode', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	}); 
	gridDataStoreObjPromo.load({params:{start:0, limit:gridPageSize}});


	gridCheckBoxSelectionModelObjPromo = new Ext.grid.CheckboxSelectionModel({ 
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObjPromo) { 
				if (gridCheckBoxSelectionModelObjPromo.getCount() > 0){
					if (gridCheckBoxSelectionModelObjPromo.getCount() == 1) 
					{
						gridObjPromo.editButton.enable();
						gridObjPromo.voucherButton.enable();
					}
					else 
					{
						gridObjPromo.editButton.disable(); 
						gridObjPromo.voucherButton.disable();
					}
					gridObjPromo.activeVoucherPromoButton.enable(); gridObjPromo.inactiveVoucherPromoButton.enable(); gridObjPromo.deleteVoucherPromoButton.enable(); 
				}
				else 
				{ 
					gridObjPromo.activeVoucherPromoButton.disable();
					gridObjPromo.inactiveVoucherPromoButton.disable();
					gridObjPromo.editButton.disable();
					gridObjPromo.deleteVoucherPromoButton.disable();
					gridObjPromo.voucherButton.disable();
				}
			}
		}
	});


	var columnRendererPromo = function(value, p, record, rowIndex, colIndex, store)
	{
		var className = '';
		if (record.data.isActive == 0) 
		{
			if (colIndex == 7) value = "{/literal}{#str_LabelInactive#}{literal}";
			className = 'class = "inactive"';
		}
		else
		{
			if (colIndex == 7) value = "{/literal}{#str_LabelActive#}{literal}";
		}
		return '<span '+className+'>'+value+'</span>';
	};

	var gridColumnModelObjPromo = new Ext.grid.ColumnModel({
		defaults: {	sortable: true, resizable: true },
		columns: [gridCheckBoxSelectionModelObjPromo,
		    { id:'companyCode', header: "{/literal}{#str_LabelCompany#}{literal}", dataIndex: 'companyCode', hidden:true },
		    { header: "{/literal}{#str_LabelCode#}{literal}", dataIndex: 'promoCode', width:230, renderer: columnRendererPromo },
	        { id:'promoName', header: "{/literal}{#str_LabelName#}{literal}", dataIndex: 'promoName', width:250, renderer: columnRendererPromo },
	        { header: "{/literal}{#str_LabelStartDate#}{literal}", dataIndex: 'startDate', width:150, renderer: columnRendererPromo },
	        { header: "{/literal}{#str_LabelEndDate#}{literal}", dataIndex: 'endDate', width:150, renderer: columnRendererPromo },
	        { header: "{/literal}{#str_LabelVoucherCount#}{literal}", dataIndex: 'promoVoucherCount', width:120, align: 'right', renderer: columnRendererPromo },
	        { header: "{/literal}{#str_LabelActive#}{literal}", dataIndex: 'isActive', renderer: columnRendererPromo, align: 'right', width:100}
	    ]
	});

	onCallbackPromo = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{	
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
			gridDataStoreObjPromo.reload();
			if (Ext.getCmp('dialogPromo') && (Ext.getCmp('dialogPromo').isVisible()))
			{
				Ext.getCmp('dialogPromo').close();
			}
		}
	};
	
	function onNewPromo()
	{
		var paramArray = [];
		
		if(!voucherPromotionEditWindowExists)
		{
			voucherPromotionEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminVouchersPromotion.addDisplay&ref='+sessionIdPromo, paramArray, '', 'initialize', false);
		}
	};

	
	function onEditPromo()
	{
		var paramArray = {};
		paramArray['id'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('promotionsGrid'));
		
		if(!voucherPromotionEditWindowExists)
		{
			voucherPromotionEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminVouchersPromotion.editDisplay&ref='+sessionIdPromo, paramArray, '', 'initialize', false);
		}
	};

	
	function onDeletePromo()
	{
		var onDeleteConfirmedPromo = function(btn)
		{
			if (btn == "yes") {
				var paramArray = {};
				paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('promotionsGrid'));
				Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminVouchersPromotion.delete', "{/literal}{#str_MessageDeleting#}{literal}", onCallbackPromo);
			}
		};

		var gridObjPromo = gMainWindowObj.findById('promotionsGrid');
		var selRecords = gridObjPromo.selModel.getSelections();
		var codeList = [];
		
		for (var rec = 0; rec < selRecords.length; rec++) {	codeList.push("'"+selRecords[rec].data.promoName.toUpperCase()+"'");}
		Ext.MessageBox.minWidth = 380;
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_DeletePromotionConfirmation#}{literal}".replace("'^0'", codeList.join(', ')), onDeleteConfirmedPromo);
	};
	

	var onActivatePromo = function(btn, ev)
	{
		var gridObjPromo = gMainWindowObj.findById('promotionsGrid');
		var paramArray = {};
		paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gridObjPromo);

		var active = 0; 
		if (btn.id == 'activeVoucherPromoButton') active = 1;
		paramArray['active'] = active;
		
		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminVouchersPromotion.promotionActivate', "{/literal}{#str_MessageUpdating#}{literal}", onCallbackPromo);
	};


	function onVouchersPromo()
	{
		var paramArray = {};
		paramArray['id'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('promotionsGrid'));

		Ext.taopix.loadJavascript('', '', 'index.php?fsaction=AdminVouchers.displayVouchersWindow&ref={/literal}{$ref}{literal}&promotionid='+paramArray['id'], [], '', 'onVouchersShow', false);
	};
	
	
	gridObjPromo = new Ext.grid.GridPanel({
	   	id: 'promotionsGrid',
	   	store: gridDataStoreObjPromo,
	    selModel: gridCheckBoxSelectionModelObjPromo,
	    cm: gridColumnModelObjPromo,
	    stripeRows: true,
	    stateful: true,
	    enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		trackMouseOver:false,
		autoExpandColumn: 'promoName',
		view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_LabelItems#}{literal}" : "{/literal}{#str_LabelItem#}{literal}"]})' }),
		columnLines:true,
		ctCls: 'grid',
	    stateId: 'promotionsGrid',
	    tbar: [	{ref: '../addButton', name: 'addButton',	text: "{/literal}{#str_ButtonNew#}{literal}",	iconCls: 'silk-add', handler: onNewPromo	}, '-',
	            {ref: '../editButton',name: 'editButton',	text: "{/literal}{#str_ButtonEdit#}{literal}",	iconCls: 'silk-pencil',	handler: onEditPromo, disabled: true	}, '-',
	           	{ref: '../deleteVoucherPromoButton', name: 'deleteVoucherPromoButton', text: "{/literal}{#str_ButtonDelete#}{literal}", iconCls: 'silk-delete', handler: onDeletePromo, disabled: true, id:'deleteVoucherPromoButton' }, '-',
	            {ref: '../activeVoucherPromoButton', name: 'activeVoucherPromoButton', text: "{/literal}{#str_LabelMakeActive#}{literal}", iconCls: 'silk-lightbulb', handler: onActivatePromo, disabled: true, id:'activeVoucherPromoButton'}, '-', 
	      	    {ref: '../inactiveVoucherPromoButton', name: 'inactiveVoucherPromoButton', text: "{/literal}{#str_LabelMakeInactive#}{literal}", iconCls: 'silk-lightbulb-off',  handler: onActivatePromo, disabled: true, id:'inactiveVoucherPromoButton'}, '-',
	            {ref: '../voucherButton', name: 'voucherButton', text: "{/literal}{#str_SectionTitleVouchers#}{literal}",	iconCls: 'silk-bricks',	handler: onVouchersPromo, id:'voucherButton', disabled: true }
		],
		plugins: [
		  	new Ext.ux.grid.Search({
		  		iconCls: 'silk-zoom',
		  		minChars: 3,
		  		width: 200,
		  		autoFocus: true,
		  		disableIndexes:['recordid', 'companyCode', 'startDate', 'endDate', 'promoVoucherCount', 'isActive']
		  	})
		],
	    bbar: new Ext.PagingToolbar({ pageSize: gridPageSize, store: gridDataStoreObjPromo, displayInfo: true })     
	});
	
	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_VoucherTitleVoucherPromotions#}{literal}",
		items: gridObjPromo,
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
	if (Ext.getCmp('dialogPromo'))
	{
		Ext.getCmp('dialogPromo').close();
	}
	if (Ext.getCmp('dialogVouchersPromo'))
	{
		Ext.getCmp('dialogVouchersPromo').close();
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
{/literal}