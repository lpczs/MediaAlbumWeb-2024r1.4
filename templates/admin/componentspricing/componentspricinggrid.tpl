{literal}
function initialize(pParams)
{	
	var gridObj = Ext.getCmp('componentgrid');
	var selRecords = gridObj.selModel.getSelections();
	var id = selRecords[0].data.id;
	var componentcode = selRecords[0].data.code;
	var category = selRecords[0].data.category;

	componentPricingAddEditWindowExists = false;
	
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
	
	
	function activateDefaultComponentPrice(btn, ev)
	{ 
		function defaultPricingActivateCallback(pUpdated, pActionForm, pActionData)
		{
			if (pUpdated)
			{
				var componentGridObj = Ext.getCmp('componentpricinggrid');
				var dataStore = componentGridObj.store;
				dataStore.reload();
			}
		}
		
		var serverParams = new Object();
		serverParams['ids'] = Ext.taopix.gridSelection2IDList(gComponentsPricingDialogObj.findById('componentpricinggrid'));	
		var active = 0; 
	
		switch (btn.id)
		{
			case 'activeButton':
				active = 1;
				break;
			case 'inactiveButton':
				active = 0;
				break;
		}
		serverParams['active'] = active;
		Ext.taopix.formPost(gComponentsPricingDialogObj, serverParams, 'index.php?fsaction=AdminComponentsPricing.activate&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageUpdating#}{literal}", defaultPricingActivateCallback);	
	}
	
	function onEditPricing(btn, ev)
	{			
		var serverParams = new Object();
		var componentID = Ext.taopix.gridSelection2IDList(Ext.getCmp('componentgrid'));
		var pricingID = Ext.taopix.gridSelection2IDList(Ext.getCmp('componentpricinggrid'));
		var gridObj = Ext.getCmp('maingrid');
		var selRecords = gridObj.selModel.getSelections();
		var pricingModel = selRecords[0].data.pricingmodel;
		
		var gridObj = Ext.getCmp('componentpricinggrid');
		var selRecords = gridObj.selModel.getSelections();
		var priceCompanyCode = selRecords[0].data.companycode;
	
		serverParams['componentid'] = componentID;
		serverParams['pricingmodel'] = pricingModel;
		serverParams['pricingid'] = pricingID;
		serverParams['pricecompanycode'] = priceCompanyCode;
		
		
		if(!componentPricingAddEditWindowExists)
		{
			componentPricingAddEditWindowExists = true;
			Ext.taopix.loadJavascript(Ext.getCmp('componentsDialog'), '', 'index.php?fsaction=AdminComponentsPricing.editDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}
	
	function onAddPricing(btn, ev)
	{			
		var serverParams = new Object();
		var componentID = Ext.taopix.gridSelection2IDList(Ext.getCmp('componentgrid'));
	
		var gridObj = Ext.getCmp('maingrid');
		var selRecords = gridObj.selModel.getSelections();
		var pricingModel = selRecords[0].data.pricingmodel;
		var category = selRecords[0].data.code;

		serverParams['componentid'] = componentID;
		serverParams['pricingmodel'] = pricingModel;
		serverParams['componentcategory'] = category;
	
		if(!componentPricingAddEditWindowExists)
		{
			componentPricingAddEditWindowExists = true;
			Ext.taopix.loadJavascript(Ext.getCmp('componentsDialog'), '', 'index.php?fsaction=AdminComponentsPricing.addDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}
	
	/* delete handler */	  
	function onDelete(btn, ev)
	{
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", nlToBr("{/literal}{#str_DeleteDefaultConfirmation#}{literal}"), onDeleteResult);
	}
	
	function onDeleteResult(btn)
	{
		if (btn == "yes")
		{
			var paramArray = new Object();
			paramArray['idlist'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('componentpricinggrid'));

			Ext.taopix.formPost(gComponentsPricingDialogObj, paramArray, 'index.php?fsaction=AdminComponentsPricing.defaultPriceDelete&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageDeleting#}{literal}", onDeleteCallback);	
		}
	}
	
	function onDeleteCallback(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated == true)
		{
			var gridObj = Ext.getCmp('componentpricinggrid');
			var dataStore = gridObj.store;
			var selRecords = gridObj.getSelectionModel().getSelections();
			var icon = Ext.MessageBox.INFO;
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
				Ext.MessageBox.show({ title: pActionData.result.title, msg: pActionData.result.msg,	buttons: Ext.MessageBox.OK,	icon: icon });
			}
			dataStore.load();
		}
	}
	
	function clearGrouping(v)
	{ 
		if(v.checked)
		{
			{/literal}{if $optionms}{literal}
				gridPricingDataStoreObj.groupBy('companycode');
			{/literal}{else}{literal}
				gridPricingDataStoreObj.groupBy('active');
			{/literal}{/if}{literal}
		}
		else 
		{
			gridPricingDataStoreObj.clearGrouping(); 
		}
	}
	
	var pricingGridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(pricingGridCheckBoxSelectionModelObj) 
			{
				var selectionCount = pricingGridCheckBoxSelectionModelObj.getCount();
				
				if (selectionCount == 1)
				{
					componentPricingGrid.editButton.enable();
				}
				else
				{
					componentPricingGrid.editButton.disable();
				}
				
				var canDelete = true;
				
				if (componentPricingGrid)
				{
					var selRecords = componentPricingGrid.getSelectionModel().getSelections();
				}

				if ((selectionCount > 0) && (canDelete))
				{
					componentPricingGrid.activeButton.enable();
					componentPricingGrid.inactiveButton.enable();
					componentPricingGrid.deleteButton.enable();
				} 
				else
				{
					componentPricingGrid.activeButton.disable();
					componentPricingGrid.inactiveButton.disable();
					componentPricingGrid.deleteButton.disable();
				}
				
				if (selectionCount == 1 || selectionCount > 1)
				{
					var selectID = Ext.taopix.gridSelection2IDList(gComponentsPricingDialogObj.findById('componentpricinggrid'));
					var idList = selectID.split(',');
					
					for (i = 0; i < idList.length; i++)
					{
						record = Ext.getCmp('componentpricinggrid').store.getById(idList[i]);
						
						{/literal}{if $optionms}{literal}
							{/literal}{if $companyLogin}{literal}
								if (record.data['companycode'] == '' && idList[i] > 0)
								{
									componentPricingGrid.editButton.disable();
									componentPricingGrid.deleteButton.disable();
									componentPricingGrid.activeButton.disable();
									componentPricingGrid.inactiveButton.disable();
									break;
								}
								else
								{
									if(selectionCount == 1)
									{
										componentPricingGrid.editButton.enable();
									}
									componentPricingGrid.deleteButton.enable();
								}
							{/literal}{/if}{literal}
						{/literal}{/if}{literal}
					}
				}
			}
		}
	});
	
	var gridPricingDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		{/literal}{if $optionms}{literal}
			groupField: 'companycodehidden',
		{/literal}{/if}{literal} 
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminComponentsPricing.getGridData&category='+category+'&code='+componentcode+'&pricingmodel={/literal}{$pricingModel}{literal}&ref={/literal}{$ref}{literal}'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
		    {/literal}{if $pricingModel == 3}{literal}
		    	{name: 'id', mapping: 0},
			    {name: 'companycode', mapping: 1},
			    {name: 'lkey', mapping: 2},
			    {name: 'qrs', mapping: 3},
			    {name: 'qre', mapping: 4},
			    {name: 'bp', mapping: 5},
			    {name: 'up', mapping: 6},
			    {name: 'ls', mapping: 7},
			    {name: 'it', mapping: 8},
				{name: 'active', mapping: 9},
				{name: 'companycodehidden', mapping: 10}
			{/literal}{elseif $pricingModel == 5}{literal}
		    	{name: 'id', mapping: 0},
			    {name: 'companycode', mapping: 1},
			    {name: 'lkey', mapping: 2},
			    {name: 'qrs', mapping: 3},
			    {name: 'qre', mapping: 4},
			    {name: 'srs', mapping: 5},
			    {name: 'sre', mapping: 6},
			    {name: 'bp', mapping: 7},
			    {name: 'up', mapping: 8},
			    {name: 'ls', mapping: 9},
			    {name: 'it', mapping: 10},
				{name: 'active', mapping: 11},
				{name: 'companycodehidden', mapping: 12}
			{/literal}{elseif $pricingModel == 7}{literal}
		    	{name: 'id', mapping: 0},
			    {name: 'companycode', mapping: 1},
			    {name: 'lkey', mapping: 2},
			    {name: 'qrs', mapping: 3},
			    {name: 'qre', mapping: 4},
			    {name: 'crs', mapping: 5},
			    {name: 'cre', mapping: 6},
			    {name: 'bp', mapping: 7},
			    {name: 'up', mapping: 8},
			    {name: 'ls', mapping: 9},
			    {name: 'it', mapping: 10},
				{name: 'active', mapping: 11},
				{name: 'companycodehidden', mapping: 12}
			{/literal}{elseif $pricingModel == 8}{literal}
		    	{name: 'id', mapping: 0},
			    {name: 'companycode', mapping: 1},
			    {name: 'lkey', mapping: 2},
			    {name: 'qrs', mapping: 3},
			    {name: 'qre', mapping: 4},
			    {name: 'crs', mapping: 5},
			    {name: 'cre', mapping: 6},
			    {name: 'srs', mapping: 7},
			    {name: 'sre', mapping: 8},
			    {name: 'bp', mapping: 9},
			    {name: 'up', mapping: 10},
			    {name: 'ls', mapping: 11},
			    {name: 'it', mapping: 12},
				{name: 'active', mapping: 13},
				{name: 'companycodehidden', mapping: 14}
			{/literal}{/if}{literal}
			])
		),
		sortInfo:{field: 'code', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});
	
	var gridPricingColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {	sortable: false, resizable: true },
		columns: 
		[
			pricingGridCheckBoxSelectionModelObj,
			{/literal}{if $optionms}{literal}
				{header: "{/literal}{#str_LabelCompany#}{literal}", width: 130, renderer: companyRenderer, dataIndex: 'companycode'},
			{/literal}{/if}{literal}
			{/literal}{if $pricingModel == 3}{literal}
				{header: "{/literal}{#str_LabelLicenseKey#}{literal}", width: 150, dataIndex: 'lkey', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_QtyPriceRangeStart#}{literal}", width: 180, dataIndex: 'qrs', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_QtyPriceRangeEnd#}{literal}", width: 180, dataIndex: 'qre', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_PriceRangeBasePrice#}{literal}", width: 150, dataIndex: 'bp', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_PriceRangeUnitPrice#}{literal}", width: 140, dataIndex: 'up', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelLineSubtract#}{literal}", width: 140, dataIndex: 'ls', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelIncludesTax#}{literal}", width: 100, dataIndex: 'it', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelStatus#}{literal}", renderer: statusRenderer, width: 55, dataIndex: 'active'}
			{/literal}{elseif $pricingModel == 5}{literal}
				{header: "{/literal}{#str_LabelLicenseKey#}{literal}", width: 135, dataIndex: 'lkey', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_QtyPriceRangeStart#}{literal}", width: 120, dataIndex: 'qrs', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_QtyPriceRangeEnd#}{literal}", width: 120, dataIndex: 'qre', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_SidePriceRangeStart#}{literal}", width: 120, dataIndex: 'srs', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_SidePriceRangeEnd#}{literal}", width: 120, dataIndex: 'sre', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_PriceRangeBasePrice#}{literal}", width: 110, dataIndex: 'bp', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_PriceRangeUnitPrice#}{literal}", width: 110, dataIndex: 'up', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelLineSubtract#}{literal}", width: 110, dataIndex: 'ls', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelIncludesTax#}{literal}", width: 100, dataIndex: 'it', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelStatus#}{literal}", renderer: statusRenderer, width: 50, dataIndex: 'active'}
			{/literal}{elseif $pricingModel == 7}{literal}
				{header: "{/literal}{#str_LabelLicenseKey#}{literal}", width: 150, dataIndex: 'lkey', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_QtyPriceRangeStart#}{literal}", width: 115, dataIndex: 'qrs', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_QtyPriceRangeEnd#}{literal}", width: 115, dataIndex: 'qre', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_ComponentPriceRangeStart#}{literal}", width: 130, dataIndex: 'crs', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_ComponentPriceRangeEnd#}{literal}", width: 130, dataIndex: 'cre', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_PriceRangeBasePrice#}{literal}", width: 100, dataIndex: 'bp', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_PriceRangeUnitPrice#}{literal}", width: 100, dataIndex: 'up', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelLineSubtract#}{literal}", width: 100, dataIndex: 'ls', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelIncludesTax#}{literal}", width: 100, dataIndex: 'it', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelStatus#}{literal}", renderer: statusRenderer, width: 50, dataIndex: 'active'}
			{/literal}{elseif $pricingModel == 8}{literal}
				{header: "{/literal}{#str_LabelLicenseKey#}{literal}", width: 130, dataIndex: 'lkey', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_QtyPriceRangeStart#}{literal}", width: 85, dataIndex: 'qrs', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_QtyPriceRangeEnd#}{literal}", width: 85, dataIndex: 'qre', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_ComponentPriceRangeStart#}{literal}", width: 100, dataIndex: 'crs', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_ComponentPriceRangeEnd#}{literal}", width: 100, dataIndex: 'cre', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_SidePriceRangeStart#}{literal}", width: 95, dataIndex: 'srs', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_SidePriceRangeEnd#}{literal}", width: 95, dataIndex: 'sre', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_PriceRangeBasePrice#}{literal}", width: 85, dataIndex: 'bp', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_PriceRangeUnitPrice#}{literal}", width: 85, dataIndex: 'up', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelLineSubtract#}{literal}", width: 80, dataIndex: 'ls', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelIncludesTax#}{literal}", width: 100, dataIndex: 'it', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelStatus#}{literal}", renderer: statusRenderer, width: 50, dataIndex: 'active'}
			{/literal}{/if}{literal}
			{/literal}{if $optionms}{literal}
				,{header: "{/literal}{#str_LabelCompany#}{literal}", width: 170, dataIndex: 'companycodehidden', hidden:true, renderer: companyCodeRenderer}
			{/literal}{/if}{literal}
		]
	});
	
	var componentPricingGrid = new Ext.grid.GridPanel({
		id: 'componentpricinggrid',
		store: gridPricingDataStoreObj,
		cm: gridPricingColumnModelObj,
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
				autoExpandColumn: 9,
			{/literal}{else}{literal}
				autoExpandColumn: 8,
			{/literal}{/if}{literal}
		{/literal}{elseif $pricingModel == 5}{literal}
			{/literal}{if $optionms}{literal}
				autoExpandColumn: 11,
			{/literal}{else}{literal}
				autoExpandColumn: 9,
			{/literal}{/if}{literal}
		{/literal}{elseif $pricingModel == 8}{literal}
			{/literal}{if $optionms}{literal}
				autoExpandColumn: 14,
			{/literal}{else}{literal}
				autoExpandColumn: 12,
			{/literal}{/if}{literal}
		{/literal}{/if}{literal}
		{/literal}{if $optionms}{literal}
			view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_SectionTitleComponents#}{literal}" : "{/literal}{#str_LabelComponent#}{literal}"]})' }),
		{/literal}{/if}{literal}
		height:400,
		selModel: pricingGridCheckBoxSelectionModelObj,
		tbar: 
		[
			{
				id:'componentPriceAddButton',
				ref: '../componentAddButton',
				text: "{/literal}{#str_ButtonAdd#}{literal}",
				iconCls: 'silk-add',
				handler: onAddPricing,
				enabled: true
			}, '-', 
			{
				ref: '../editButton',
				text: "{/literal}{#str_ButtonEdit#}{literal}",
				iconCls: 'silk-pencil',
				handler: onEditPricing,
				disabled: true
			},'-', {
				ref: '../deleteButton',
				text: "{/literal}{#str_ButtonDelete#}{literal}",
				iconCls: 'silk-delete',
				handler: onDelete,
				disabled: true
			},
			{ 
				id:'activeButton',
				ref: '../activeButton', 
				text: "{/literal}{#str_LabelMakeActive#}{literal}", 
				iconCls: 'silk-lightbulb',
				handler: activateDefaultComponentPrice, 
				disabled: true
			}, '-',
			{ 
				id:'inactiveButton', 
				ref: '../inactiveButton', 
				text: "{/literal}{#str_LabelMakeInactive#}{literal}", 
				iconCls: 'silk-lightbulb-off',
				handler: activateDefaultComponentPrice, 
				disabled: true	
			}
			{/literal}{if $optionms}{literal}
			,'-',
			new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelEnableGrouping#}{literal}", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }})
			 {/literal}{/if}{literal}
		]
	});
	gridPricingDataStoreObj.load();
	
	var dialogFormPanelObj = new Ext.FormPanel({
		id: 'componentTypePricingGrid',
        labelAlign: 'left',
        labelWidth:20,
        autoHeight: true,
        frame:true,
        layout:'form',
        cls: 'left-right-buttons',
        items: componentPricingGrid
    });
    
    var gComponentsPricingDialogObj = new Ext.Window({
		id: 'componentPricingDialog',
		closable:false,
		plain:true,
		modal:true,
		draggable:true,
		title: "{/literal}{$title}{literal}",
		resizable:false,
		layout: 'fit',
		height: 'auto',
		width: 1200,
		items: dialogFormPanelObj,
		listeners: {
			'close': {   
				fn: function(){
                    componentPricingGridWindowExists = false;
				}
			}
		},
		tools:[{
		    id:'clse',
		    qtip: '{/literal}{#str_LabelCloseWIndow#}{literal}',
		    handler: function(){Ext.getCmp('componentPricingDialog').close(); }
		}]
	});
	gComponentsPricingDialogObj.show();	
}
{/literal}