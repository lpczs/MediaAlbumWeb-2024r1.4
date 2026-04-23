{literal}
function initialize(pParams)
{	
	pricelistEditWindowExists = false;
	var gridObj = Ext.getCmp('maingrid');
	var selRecords = gridObj.selModel.getSelections();
	var category = selRecords[0].data.code;
	
	function generalColumnRenderer(value, p, record)
	{
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

	function companyCodeRenderer(value, p, record)
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
	
	function onAddPriceList(btn, ev)
	{		
		var serverParams = new Object();
		var gridObj = Ext.getCmp('maingrid');
		var selRecords = gridObj.selModel.getSelections();
		var pricingModel = selRecords[0].data.pricingmodel;
		var companyCode = selRecords[0].data.companycode;
		var decimalPlaces = selRecords[0].data.decimalplaces;
		
		serverParams['pricingmodel'] = pricingModel;
		serverParams['companycode'] = companyCode;
		serverParams['decimalplaces'] = decimalPlaces;
		
		if (!pricelistEditWindowExists)
		{
			pricelistEditWindowExists = true;
			Ext.taopix.loadJavascript(gComponentsPriceListDialogObj, '', 'index.php?fsaction=AdminComponentsPricing.priceListAddDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}

	function onEditPriceList(btn, ev)
	{		
		var serverParams = new Object();
		var priceListID = Ext.taopix.gridSelection2IDList(Ext.getCmp('pricelistgrid'));
		
		var gridObj = Ext.getCmp('maingrid');
		var selRecords = gridObj.selModel.getSelections();
		var decimalPlaces = selRecords[0].data.decimalplaces;
		
		serverParams['decimalplaces'] = decimalPlaces;
		serverParams['pricelistid'] = priceListID;
			
		if (!pricelistEditWindowExists)
		{
			pricelistEditWindowExists = true;
			Ext.taopix.loadJavascript(gComponentsPriceListDialogObj, '', 'index.php?fsaction=AdminComponentsPricing.priceListEditDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}

	/* delete handler */	  
	function onDeletePriceList(btn, ev)
	{
		function onDeletePriceListCallback(pUpdated, pTheForm, pActionData)
		{
			if (pUpdated == true)
			{
				var gridObj = Ext.getCmp('pricelistgrid');
				var dataStore = gridObj.store;
				var selRecords = gridObj.getSelectionModel().getSelections();
				var icon = Ext.MessageBox.WARNING;
				var idList = ',' + pActionData.result.idlist + ',';

				for (rec = 0; rec < selRecords.length; rec++)
				{
					/* only delete from selection what has been deleted */
					if (idList.indexOf(',' + selRecords[rec]['id'] + ',') !=-1)
					{
						dataStore.remove(selRecords[rec]);
					}
				}
				if (pActionData.result.msg)
				{	
					Ext.MessageBox.show({
						title: pActionData.result.title,
						msg: pActionData.result.msg,
						buttons: Ext.MessageBox.OK,
						icon: icon
					});
				}
				dataStore.load();
			}
		}

		function onDeletePriceListResult(btn)
		{
			if (btn == "yes")
			{
				var paramArray = new Object();
				var gridObj = Ext.getCmp('pricelistgrid');
				var selRecords = gridObj.selModel.getSelections();
				var codeList = '';
		
				for (var rec = 0; rec < selRecords.length; rec++) 
				{	
					codeList = codeList + selRecords[rec].data.code;
					if (rec != selRecords.length - 1)
					{
						codeList = codeList + ',';
					}	
				}
				paramArray['idlist'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('pricelistgrid'));
				paramArray['codelist'] = codeList;
				Ext.taopix.formPost(Ext.getCmp('componentPriceListDialog'), paramArray, 'index.php?fsaction=AdminComponentsPricing.priceListDelete&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageDeleting#}{literal}", onDeletePriceListCallback);	
			}
		}
		
		var gridObj = Ext.getCmp('pricelistgrid');
		var dataStore = gridObj.store;
		var selRecords = gridObj.selModel.getSelections();
		var codeList = '';
	
		for (var rec = 0; rec < selRecords.length; rec++) 
		{	
			codeList = codeList + selRecords[rec].data.code;
		
			if (rec != selRecords.length - 1)
			{
				codeList = codeList + ',';
			}	
		}
		var message = nlToBr("{/literal}{#str_DeleteConfirmation#}{literal}".replace("^0", codeList));

		dataStore.load();
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", message, onDeletePriceListResult);
	}

	function activatePriceList(btn, ev)
	{ 
		function priceListActivateCallback(pUpdated, pActionForm, pActionData)
		{
			if (pUpdated)
			{
				var componentGridObj = Ext.getCmp('pricelistgrid');
				var dataStore = componentGridObj.store;
				dataStore.reload();
			}
		}

		/* server parameters are sent to the server */
		var serverParams = new Object();
		serverParams['ids'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('pricelistgrid'));	
		var active = 0; 
	
		switch (btn.id)
		{
			case 'priceListActiveButton':
				active = 1;
				break;
			case 'priceListInactiveButton':
				active = 0;
				break;
		}

		serverParams['active'] = active;
		Ext.taopix.formPost(Ext.getCmp('componentPriceListDialog'), serverParams, 'index.php?fsaction=AdminComponentsPricing.activatePriceList&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageUpdating#}{literal}", priceListActivateCallback);	
	}
	
	function clearGrouping(v)
	{ 
		if(v.checked)
		{
			{/literal}{if $optionms}{literal}
				gridPriceListDataStoreObj.groupBy('companycode');
			{/literal}{else}{literal}
				gridPriceListDataStoreObj.groupBy('active');
			{/literal}{/if}{literal}
		}
		else 
		{
			gridPriceListDataStoreObj.clearGrouping(); 
		}
	}

	var priceListGridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: 
		{
			selectionchange: function(priceListGridCheckBoxSelectionModelObj) 
			{
				var selectionCount = priceListGridCheckBoxSelectionModelObj.getCount();
				
				if (selectionCount == 1)
				{
					componentPriceListGrid.priceListEditButton.enable();
				}
				else
				{
					componentPriceListGrid.priceListEditButton.disable();
				}
				
				var canDelete = true;
				
				if (componentPriceListGrid)
				{
					var selRecords = componentPriceListGrid.getSelectionModel().getSelections();
				}

				if ((selectionCount > 0) && (canDelete))
				{
					componentPriceListGrid.priceListActiveButton.enable();
					componentPriceListGrid.priceListInactiveButton.enable();
					componentPriceListGrid.priceListdeleteButton.enable();
				} 
				else
				{
					componentPriceListGrid.priceListActiveButton.disable();
					componentPriceListGrid.priceListInactiveButton.disable();
					componentPriceListGrid.priceListdeleteButton.disable();
				}
				
				if (selectionCount == 1 || selectionCount > 1)
				{
					var selectID = Ext.taopix.gridSelection2IDList(gComponentsPriceListDialogObj.findById('pricelistgrid'));
					var idList = selectID.split(',');
					
					for (i = 0; i < idList.length; i++)
					{
						record = Ext.getCmp('pricelistgrid').store.getById(idList[i]);
						
						{/literal}{if $optionms}{literal}
							{/literal}{if $companyLogin}{literal}
								if (record.data['companycode'] == '' && idList[i] > 0)
								{
									componentPriceListGrid.priceListEditButton.disable();
									componentPriceListGrid.priceListdeleteButton.disable();
									componentPriceListGrid.priceListActiveButton.disable();
									componentPriceListGrid.priceListInactiveButton.disable();
									break;
								}
								else
								{
									if(selectionCount == 1)
									{
										componentPriceListGrid.priceListEditButton.enable();
									}
									componentPriceListGrid.priceListdeleteButton.enable();
								}
							{/literal}{/if}{literal}
						{/literal}{/if}{literal}
					}
				}
			}
		}
	});

	var gridPriceListDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		{/literal}{if $optionms}{literal}
			groupField: 'companycodehidden',
		{/literal}{/if}{literal} 
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminComponentsPricing.getPriceListGridData&category='+category+'&pricingmodel={/literal}{$pricingModel}{literal}&ref={/literal}{$ref}{literal}'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
		    {/literal}{if $pricingModel == 3}{literal}
		    	{name: 'id', mapping: 0},
			    {name: 'companycode', mapping: 1},
			    {name: 'code', mapping: 2},
			    {name: 'name', mapping: 3},
			    {name: 'qrs', mapping: 4},
			    {name: 'qre', mapping: 5},
			    {name: 'bp', mapping: 6},
			    {name: 'up', mapping: 7},
			    {name: 'ls', mapping: 8},
			    {name: 'it', mapping: 9},
				{name: 'active', mapping: 10},
				{name: 'companycodehidden', mapping: 11}
			{/literal}{elseif $pricingModel == 5}{literal}
		    	{name: 'id', mapping: 0},
			    {name: 'companycode', mapping: 1},
			    {name: 'code', mapping: 2},
			    {name: 'name', mapping: 3},
			    {name: 'qrs', mapping: 4},
			    {name: 'qre', mapping: 5},
			    {name: 'srs', mapping: 6},
			    {name: 'sre', mapping: 7},
			    {name: 'bp', mapping: 8},
			    {name: 'up', mapping: 9},
			    {name: 'ls', mapping: 10},
			    {name: 'it', mapping: 11},
				{name: 'active', mapping: 12},
				{name: 'companycodehidden', mapping: 13}
			{/literal}{elseif $pricingModel == 7}{literal}
		    	{name: 'id', mapping: 0},
			    {name: 'companycode', mapping: 1},
			    {name: 'code', mapping: 2},
			    {name: 'name', mapping: 3},
			    {name: 'qrs', mapping: 4},
			    {name: 'qre', mapping: 5},
			    {name: 'crs', mapping: 6},
			    {name: 'cre', mapping: 7},
			    {name: 'bp', mapping: 8},
			    {name: 'up', mapping: 9},
			    {name: 'ls', mapping: 10},
			    {name: 'it', mapping: 11},
				{name: 'active', mapping: 12},
				{name: 'companycodehidden', mapping: 13}
			{/literal}{elseif $pricingModel == 8}{literal}
		    	{name: 'id', mapping: 0},
			    {name: 'companycode', mapping: 1},
			    {name: 'code', mapping: 2},
			    {name: 'name', mapping: 3},
			    {name: 'qrs', mapping: 4},
			    {name: 'qre', mapping: 5},
			    {name: 'crs', mapping: 6},
			    {name: 'cre', mapping: 7},
			    {name: 'srs', mapping: 8},
			    {name: 'sre', mapping: 9},
			    {name: 'bp', mapping: 10},
			    {name: 'up', mapping: 11},
			    {name: 'ls', mapping: 12},
			    {name: 'it', mapping: 13},
				{name: 'active', mapping: 14},
				{name: 'companycodehidden', mapping: 15}
			{/literal}{/if}{literal}
			])
		),
		sortInfo:{field: 'id', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});
		
	var priceListGridColumnModel = new Ext.grid.ColumnModel({
		defaults: { sortable: false, resizable: true },
		columns: [
			priceListGridCheckBoxSelectionModelObj,
			{/literal}{if $optionms}{literal}
				{header: "{/literal}{#str_LabelCompany#}{literal}", width: 150, renderer: companyRenderer, dataIndex: 'companycode'},
			{/literal}{/if}{literal}
			{header: "{/literal}{#str_LabelCode#}{literal}", width: 150, dataIndex: 'code', renderer: generalColumnRenderer},
			{header: "{/literal}{#str_LabelName#}{literal}", width: 150, dataIndex: 'name', renderer: generalColumnRenderer},
			{/literal}{if $pricingModel == 3}{literal}
				{header: "{/literal}{#str_QtyPriceRangeStart#}{literal}", width: 95, dataIndex: 'qrs', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_QtyPriceRangeEnd#}{literal}", width: 95, dataIndex: 'qre', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_PriceRangeBasePrice#}{literal}", width: 95, dataIndex: 'bp', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_PriceRangeUnitPrice#}{literal}", width: 95, dataIndex: 'up', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelLineSubtract#}{literal}", width: 95, dataIndex: 'ls', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelIncludesTax#}{literal}", width: 180, dataIndex: 'it', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelStatus#}{literal}", renderer: statusRenderer, width: 50, dataIndex: 'active'}
			{/literal}{elseif $pricingModel == 5}{literal}
				{header: "{/literal}{#str_QtyPriceRangeStart#}{literal}", width: 70, dataIndex: 'qrs', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_QtyPriceRangeEnd#}{literal}", width: 70, dataIndex: 'qre', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_SidePriceRangeStart#}{literal}", width: 70, dataIndex: 'srs', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_SidePriceRangeEnd#}{literal}", width: 70, dataIndex: 'sre', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_PriceRangeBasePrice#}{literal}", width: 70, dataIndex: 'bp', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_PriceRangeUnitPrice#}{literal}", width: 70, dataIndex: 'up', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelLineSubtract#}{literal}", width: 70, dataIndex: 'ls', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelIncludesTax#}{literal}", width: 180, dataIndex: 'it', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelStatus#}{literal}", renderer: statusRenderer, width: 50, dataIndex: 'active'}
			{/literal}{elseif $pricingModel == 7}{literal}
				{header: "{/literal}{#str_QtyPriceRangeStart#}{literal}", width: 70, dataIndex: 'qrs', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_QtyPriceRangeEnd#}{literal}", width: 70, dataIndex: 'qre', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_ComponentPriceRangeStart#}{literal}", width: 70, dataIndex: 'crs', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_ComponentPriceRangeEnd#}{literal}", width: 70, dataIndex: 'cre', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_PriceRangeBasePrice#}{literal}", width: 70, dataIndex: 'bp', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_PriceRangeUnitPrice#}{literal}", width: 70, dataIndex: 'up', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelLineSubtract#}{literal}", width: 70, dataIndex: 'ls', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelIncludesTax#}{literal}", width: 180, dataIndex: 'it', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelStatus#}{literal}", renderer: statusRenderer, width: 50, dataIndex: 'active'}
			{/literal}{elseif $pricingModel == 8}{literal}
				{header: "{/literal}{#str_QtyPriceRangeStart#}{literal}", width: 70, dataIndex: 'qrs', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_QtyPriceRangeEnd#}{literal}", width: 70, dataIndex: 'qre', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_ComponentPriceRangeStart#}{literal}", width: 70, dataIndex: 'crs', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_ComponentPriceRangeEnd#}{literal}", width: 70, dataIndex: 'cre', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_SidePriceRangeStart#}{literal}", width: 70, dataIndex: 'srs', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_SidePriceRangeEnd#}{literal}", width: 70, dataIndex: 'sre', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_PriceRangeBasePrice#}{literal}", width: 70, dataIndex: 'bp', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_PriceRangeUnitPrice#}{literal}", width: 70, dataIndex: 'up', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelLineSubtract#}{literal}", width: 70, dataIndex: 'ls', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelIncludesTax#}{literal}", width: 180, dataIndex: 'it', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelStatus#}{literal}", renderer: statusRenderer, width: 50, dataIndex: 'active'}
			{/literal}{/if}{literal}
			{/literal}{if $optionms}{literal}
				,{header: "{/literal}{#str_LabelCompany#}{literal}", width: 170, dataIndex: 'companycodehidden', hidden:true, renderer: companyCodeRenderer}
			{/literal}{/if}{literal}
		]
	});
	
	var componentPriceListGrid = new Ext.grid.GridPanel({
		id: 'pricelistgrid',
		store: gridPriceListDataStoreObj,
		cm: priceListGridColumnModel,
		enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		enableHdMenu:false,
		trackMouseOver:false,
		stripeRows:true,
		columnLines:true,
		border: false,
		ctCls: 'grid',
		style:'border:1px solid #99BBE8',
		{/literal}{if $pricingModel == 3}{literal}
            {/literal}{if $optionms}{literal}
                autoExpandColumn: 10,
            {/literal}{else}{literal}
				autoExpandColumn: 9,
			{/literal}{/if}{literal}    
		{/literal}{elseif $pricingModel == 5}{literal}
			{/literal}{if $optionms}{literal}
				autoExpandColumn: 12,
			{/literal}{else}{literal}
				autoExpandColumn: 10,
			{/literal}{/if}{literal}
		{/literal}{elseif $pricingModel == 7}{literal}
			{/literal}{if $optionms}{literal}
				autoExpandColumn: 12,
			{/literal}{else}{literal}
				autoExpandColumn: 10,
			{/literal}{/if}{literal}
		{/literal}{elseif $pricingModel == 8}{literal}
			{/literal}{if $optionms}{literal}
				autoExpandColumn: 13,
			{/literal}{else}{literal}
				autoExpandColumn: 11,
			{/literal}{/if}{literal}
		{/literal}{/if}{literal}
		{/literal}{if $optionms}{literal}
			view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_SectionTitleComponents#}{literal}" : "{/literal}{#str_LabelComponent#}{literal}"]})' }),
		{/literal}{/if}{literal}
		height:400,
		selModel: priceListGridCheckBoxSelectionModelObj,
		tbar: 
		[
			{
				id:'priceListAddButton',
				ref: '../priceListAddButton',
				text: "{/literal}{#str_ButtonAdd#}{literal}",
				iconCls: 'silk-add',
				handler: onAddPriceList,
				enabled: true
			}, '-', 
			{
				ref: '../priceListEditButton',
				text: "{/literal}{#str_ButtonEdit#}{literal}",
				iconCls: 'silk-pencil',
				handler: onEditPriceList,
				disabled: true
			},'-', 
			{
				ref: '../priceListdeleteButton',
				text: "{/literal}{#str_ButtonDelete#}{literal}",
				iconCls: 'silk-delete',
				handler: onDeletePriceList,
				disabled: true
			},'-',
			{ 
				id:'priceListActiveButton',
				ref: '../priceListActiveButton', 
				text: "{/literal}{#str_LabelMakeActive#}{literal}", 
				iconCls: 'silk-lightbulb',
				handler: activatePriceList,
				disabled: true
			}, '-',
			{ 
				id:'priceListInactiveButton', 
				ref: '../priceListInactiveButton', 
				text: "{/literal}{#str_LabelMakeInactive#}{literal}", 
				iconCls: 'silk-lightbulb-off',
				handler: activatePriceList,
				disabled: true	
			}
			{/literal}{if $optionms}{literal}
			,'-',
			new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelEnableGrouping#}{literal}", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }})
			{/literal}{/if}{literal}
		]
	});
	gridPriceListDataStoreObj.load();
	
	var priceListDialogFormPanelObj = new Ext.FormPanel({
		id: 'componentpricelistgrid',
        labelAlign: 'left',
        labelWidth:20,
        autoHeight: true,
        frame:true,
        layout:'form',
        cls: 'left-right-buttons',
        items: componentPriceListGrid
    });
	
	var gComponentsPriceListDialogObj = new Ext.Window({
		id: 'componentPriceListDialog',
		title: "{/literal}{#str_LabelPriceLists#}{literal}".replace("^0", category),
		closable:false,
		closeAction: 'close',
		tools:
		[
		  	{
		   		id:'clse',
		   	 	qtip: '{/literal}{#str_LabelCloseWIndow#}{literal}',
		    	handler: function(){ Ext.getCmp('componentPriceListDialog').close(); }
			}
		],
	  	plain:true,
	  	modal:true,
	  	autoHeight:true,
	  	draggable:true,
	  	resizable:false,
	  	layout: 'fit',
	  	height: 'auto',
		width: 1260,
	  	items: priceListDialogFormPanelObj,
	  	listeners: {
			'close': {   
				fn: function(){
			componentPricelistGridWindowExists = false;
				}
			}
		},
	  	cls: 'left-right-buttons'
	});
	gComponentsPriceListDialogObj.show();	  
}
{/literal}