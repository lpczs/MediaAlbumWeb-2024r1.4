{literal}
function companyRenderer(value, p, record)
{
	if (value == '')
	{
		value = "<i>{/literal}{#str_Global#}{literal}</i>";
	}

	if (record.data.status == 0)
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
	if (record.data.status == 0)
	{
		className =  'class = "inactive"';
		return '{/literal}<span '+className+'>'+value+'</span>{literal}';
	}
	else
	{
		return '{/literal}<span class="">'+value+'</span>{literal}';
	}	
}
	
function initialize(pParams)
{	
	var gridObj = Ext.getCmp('maingrid');
	var selRecords = gridObj.selModel.getSelections();
	var category = selRecords[0].data.code;
	var categoryCompanyCode = selRecords[0].data.companycode;

	componentAddEditWindowExists = false;
	componentPricingGridWindowExists = false;
	componentPricelistGridWindowExists = false;
	
	function onComponentPricing(btn, ev)
	{		
		/* server parameters are sent to the server */
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(Ext.getCmp('componentgrid'));
		var gridObj = Ext.getCmp('maingrid');
		var selRecords = gridObj.selModel.getSelections();
		var pricingModel = selRecords[0].data.pricingmodel;
		serverParams['id'] = id;
		serverParams['pricingmodel'] = pricingModel;

		if(!componentPricingGridWindowExists)
		{
			componentPricingGridWindowExists = true;
			Ext.taopix.loadJavascript(gComponentsDialogObj, '', 'index.php?fsaction=AdminComponentsPricing.initialize&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}
	
	function onComponentPriceLists(btn, ev)
	{		
		var serverParams = new Object();
		var gridObj = Ext.getCmp('maingrid');
		var selRecords = gridObj.selModel.getSelections();
		var pricingModel = selRecords[0].data.pricingmodel;
		serverParams['pricingmodel'] = pricingModel;

		if(!componentPricelistGridWindowExists)
		{
			componentPricelistGridWindowExists = true;
			Ext.taopix.loadJavascript(gComponentsDialogObj, '', 'index.php?fsaction=AdminComponentsPricing.priceListsInitialize&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}
	
	function onEditComponent(btn, ev)
	{
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(Ext.getCmp('componentgrid'));
		serverParams['id'] = id;
		serverParams['categorycompanycode'] = categoryCompanyCode;
		
		if (!componentAddEditWindowExists)
		{
			componentAddEditWindowExists = true;
			Ext.taopix.loadJavascript(gComponentsDialogObj, '', 'index.php?fsaction=AdminComponents.editDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}
	
	function onAddComponent(btn, ev)
	{		
		var serverParams = new Object();
		serverParams['categorycode'] = category;
		serverParams['categorycompanycode'] = categoryCompanyCode;
		
		if (!componentAddEditWindowExists)
		{
			componentAddEditWindowExists = true;
			Ext.taopix.loadJavascript(gComponentsDialogObj, '', 'index.php?fsaction=AdminComponents.addDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}
	
	/* delete handler */	  
	function onDeleteComponent(btn, ev)
	{
		function onDeleteComponentCallback(pUpdated, pTheForm, pActionData)
		{
			if (pUpdated == true)
			{
				var gridObj = Ext.getCmp('componentgrid');
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
					Ext.MessageBox.show({
						title: pActionData.result.title,
						msg: pActionData.result.msg,
						buttons: Ext.MessageBox.OK,
						icon: icon
					});
				}
				dataStore.reload();
			}
		}
		
		function onDeleteComponentResult(btn)
		{
			if (btn == "yes")
			{
				var paramArray = new Object();
				var gridObj = Ext.getCmp('componentgrid');
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
				paramArray['idlist'] = Ext.taopix.gridSelection2IDList( Ext.getCmp('componentgrid'));
				paramArray['codelist'] = codeList;

				Ext.taopix.formPost(gComponentsDialogObj, paramArray, 'index.php?fsaction=AdminComponents.delete&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageDeleting#}{literal}", onDeleteComponentCallback);	
			}
		}
		
		var gridObj = Ext.getCmp('componentgrid');
		var dataStore = gridObj.store;
		var selRecords = gridObj.selModel.getSelections();
		var codeList = '';
	
		for (var rec = 0; rec < selRecords.length; rec++) 
		{	
			codeList = codeList + selRecords[rec].data.localcode;
		
			if (rec != selRecords.length - 1)
			{
				codeList = codeList + ',';
			}	
		}
	
		var message = nlToBr("{/literal}{#str_DeleteConfirmation#}{literal}");
		message = message.replace("^0", codeList);

		dataStore.reload();
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", message, onDeleteComponentResult);
	}
	
	function activateComponent(btn, ev)
	{ 
		function componentActivateCallback(pUpdated, pActionForm, pActionData)
		{
			if (pUpdated)
			{
				var dataStore = Ext.getCmp('componentgrid').store;
				Ext.taopix.updateDataStore(dataStore, pActionData.result.data);
				dataStore.reload();
			}
		}
		
		/* server parameters are sent to the server */
		var serverParams = new Object();
		serverParams['ids'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('componentgrid'));	
		var active = 0; 

		switch (btn.id)
		{
			case 'componentActiveButton':
				active = 1;
				break;
			case 'componentInctiveButton':
				active = 0;
				break;
		}

		var componentGridObj = Ext.getCmp('componentgrid');
		var selRecords = componentGridObj.selModel.getSelections();
		var codeList = '';
	
		for (var rec = 0; rec < selRecords.length; rec++) 
		{	
			codeList = codeList + selRecords[rec].data.code;
		
			if (rec != selRecords.length - 1)
			{
				codeList = codeList + ',';
			}	
		}

		serverParams['codelist'] = codeList;
		serverParams['active'] = active;

		Ext.taopix.formPost(gComponentsDialogObj, serverParams, 'index.php?fsaction=AdminComponents.activate&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageUpdating#}{literal}", componentActivateCallback);	
	}

	function clearGrouping(v)
	{ 
		if(v.checked)
		{
			{/literal}{if $optionms}{literal}
				gridDataStoreObj.groupBy('companycode');
			{/literal}{else}{literal}
				gridDataStoreObj.groupBy('active');
			{/literal}{/if}{literal}
		}
		else 
		{
			gridDataStoreObj.clearGrouping(); 
		}
	}

	function onHideInactive(btn, ev)
	{
		// get the datastore and default hide inactive to false
		var gridDataStore = Ext.getCmp('componentgrid').store;
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
					var gridDataStore = Ext.getCmp('componentgrid').store;

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
	
	var gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj) 
			{
				var selectionCount = gridCheckBoxSelectionModelObj.getCount();
				if (selectionCount == 1)
				{
					componentGrid.editButton.enable();
					componentGrid.componentsDefaultPricingButton.enable();
				}
				else
				{
					componentGrid.editButton.disable();
					componentGrid.componentsDefaultPricingButton.disable();
				}
				
				if (componentGrid)
				{
					var selRecords = componentGrid.getSelectionModel().getSelections();
				}

                
                if (selectionCount == 1 || selectionCount > 1)
				{
                    var selectID = Ext.taopix.gridSelection2IDList(Ext.getCmp('componentgrid'));
					var idList = selectID.split(',');
					for (i = 0; i < idList.length; i++)
					{
						record = Ext.getCmp('componentgrid').store.getById(idList[i]);
						{/literal}{if $optionms}{literal}
                            {/literal}{if $companyLogin}{literal}
                                if (record.data['companycode'] == '' && idList[i] > 0)
                                {
                                    componentGrid.editButton.disable();
                                    componentGrid.componentsDefaultPricingButton.disable();
                                    componentGrid.deleteButton.disable();
                                    componentGrid.activeButton.disable();
                                    componentGrid.inactiveButton.disable();
                                    break;
                                }
                                else
                                {
                                    if(selectionCount == 1)
                                    {
                                        componentGrid.editButton.enable();
                                        componentGrid.componentsDefaultPricingButton.enable();
                                    }
                                    componentGrid.deleteButton.enable();
                                    componentGrid.activeButton.enable();
                                    componentGrid.inactiveButton.enable();
                                }
                            {/literal}{else}{literal}
                                if (record.data['code'] == '' && record.data['companycode'] == '' && idList[i] > 0)
                                {
                                    componentGrid.editButton.disable();
                                    componentGrid.componentsDefaultPricingButton.disable();
                                    componentGrid.deleteButton.disable();
                                    componentGrid.activeButton.disable();
                                    componentGrid.inactiveButton.disable();
                                    break;
                                }
                                else
                                {
                                    componentGrid.deleteButton.enable();
                                    componentGrid.activeButton.enable();
                                    componentGrid.inactiveButton.enable();
                                }
                            {/literal}{/if}{literal}
                        {/literal}{else}{literal}
                            if (record.data['code'] == '' && idList[i] > 0)
                            {
                                componentGrid.editButton.disable();
                                componentGrid.componentsDefaultPricingButton.disable();
                                componentGrid.deleteButton.disable();
                                componentGrid.activeButton.disable();
                                componentGrid.inactiveButton.disable();
                                break;
                            }
                            else
                            {
                                componentGrid.deleteButton.enable();
                                componentGrid.activeButton.enable();
                                componentGrid.inactiveButton.enable();
                            }
                        {/literal}{/if}{literal}
                    }
                }
                else
                {
                    componentGrid.editButton.disable();
                    componentGrid.componentsDefaultPricingButton.disable();
                    componentGrid.deleteButton.disable();
                    componentGrid.activeButton.disable();
                    componentGrid.inactiveButton.disable();
                }
            }
        }
              
	});
	
	var gridDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		{/literal}{if $optionms}{literal}
			groupField: 'companycodehidden',
		{/literal}{/if}{literal} 
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminComponents.getGridData&category=' + category + '&categorycompanycode=' + categoryCompanyCode + '&ref={/literal}{$ref}{literal}'}),
		reader: new Ext.taopix.PagedArrayReader({idIndex: 0},
		Ext.data.Record.create([
		    {/literal}{if $optionms}{literal}
		    	{name: 'id', mapping: 0},
			    {name: 'companycode', mapping: 1},
			    {name: 'code', mapping: 2},
			    {name: 'localcode', mapping: 3},
				{name: 'skucode', mapping: 4},
				{name: 'name', mapping: 5},			
				{name: 'status', mapping: 6},
				{name: 'category', mapping: 7},
				{name: 'companycodehidden', mapping: 8}
		    {/literal}{else}{literal}
			    {name: 'id', mapping: 0},
			    {name: 'code', mapping: 1},
			    {name: 'localcode', mapping: 2},
				{name: 'skucode', mapping: 3},
				{name: 'name', mapping: 4},			
				{name: 'status', mapping: 5},
				{name: 'category', mapping: 6}
			{/literal}{/if}{literal}
			])
		),
		sortInfo:{field: 'code', direction: "ASC"},
		listeners : { beforeload : checkHideInactiveButton },
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});
		
	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: 
		{
			sortable: false, 
			resizable: true
		},
		columns: [
			gridCheckBoxSelectionModelObj,
			{/literal}{if $optionms}{literal}
			{header: "{/literal}{#str_LabelCompany#}{literal}", width: 150, renderer: companyRenderer, dataIndex: 'companycode'},
			{header: "{/literal}{#str_LabelCode#}{literal}", width: 200, renderer: generalColumnRenderer, dataIndex: 'localcode'},
			{header: "{/literal}{#str_LabelSKUCode#}{literal}", width: 150,  renderer: generalColumnRenderer, dataIndex: 'skucode'},
			{header: "{/literal}{#str_LabelName#}{literal}", width: 300,  renderer: generalColumnRenderer, dataIndex: 'name'},
			{header: "{/literal}{#str_LabelStatus#}{literal}",renderer: statusRenderer, width: 50, align:'right', dataIndex: 'status'},
			{hidden: true, width: 50, align:'right', dataIndex: 'category'},
			{header: "{/literal}{#str_LabelCompany#}{literal}", width: 170, dataIndex: 'companycodehidden', hidden:true, renderer: companyCodeRenderer}
			{/literal}{else}{literal}
			{header: "{/literal}{#str_LabelCode#}{literal}", width: 200, renderer: generalColumnRenderer, hidden: true, dataIndex: 'code'},
			{header: "{/literal}{#str_LabelCode#}{literal}", width: 200, renderer: generalColumnRenderer, dataIndex: 'localcode'},
			{header: "{/literal}{#str_LabelSKUCode#}{literal}", width: 150,  renderer: generalColumnRenderer, dataIndex: 'skucode'},
			{header: "{/literal}{#str_LabelName#}{literal}", width: 300,  renderer: generalColumnRenderer, dataIndex: 'name'},
			{header: "{/literal}{#str_LabelStatus#}{literal}",renderer: statusRenderer, width: 50, align:'right', dataIndex: 'status'},
			{hidden: true, width: 50, align:'right', dataIndex: 'category'}
			{/literal}{/if}{literal}
		]
	});
	
	var componentGrid = new Ext.grid.GridPanel({
		id: 'componentgrid',
		store: gridDataStoreObj,
		cm: gridColumnModelObj,
		enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		border:false,
		enableHdMenu:false,
		trackMouseOver:false,
		stripeRows:true,
		columnLines:true,
		plugins: [new Ext.ux.grid.Search({
			iconCls: 'silk-zoom',
			minChars: 3,
			width: 200,
			{/literal}{if $optionms}{literal}
				disableIndexes:['category','status','companycode', 'companycodehidden'],
			{/literal}{else}{literal}
				disableIndexes:['category','status'],
			{/literal}{/if}{literal}
				autoFocus: true
		})],
		{/literal}{if $optionms}{literal}
			view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_SectionTitleComponents#}{literal}" : "{/literal}{#str_LabelComponent#}{literal}"]})' }),
		{/literal}{/if}{literal}
        autoExpandColumn: 5,
		height:400,
		ctCls: 'grid',
		style:'border:1px solid #99BBE8;',
		selModel: gridCheckBoxSelectionModelObj,
		tbar: [
			{
				id:'componentAddButton',
				ref: '../componentAddButton',
				text: "{/literal}{#str_ButtonAdd#}{literal}",
				iconCls: 'silk-add',
				handler: onAddComponent,
				enabled: true
			}, '-', 
			{
				ref: '../editButton',
				text: "{/literal}{#str_ButtonEdit#}{literal}",
				iconCls: 'silk-pencil',
				handler: onEditComponent,
				disabled: true
			},'-', 
			{
				ref: '../deleteButton',
				text: "{/literal}{#str_ButtonDelete#}{literal}",
				iconCls: 'silk-delete',
				handler: onDeleteComponent,
				disabled: true
			},'-',
			{ 
				id:'componentActiveButton',
				ref: '../activeButton', 
				text: "{/literal}{#str_LabelMakeActive#}{literal}", 
				iconCls: 'silk-lightbulb',
				handler: activateComponent, 
				disabled: true
			}, '-',
			{ 
				id:'componentInactiveButton', 
				ref: '../inactiveButton', 
				text: "{/literal}{#str_LabelMakeInactive#}{literal}", 
				iconCls: 'silk-lightbulb-off',
				handler: activateComponent, 
				disabled: true	
			},'-',
			{ 
				id:'componentsDefaultPricingButton', 
				ref: '../componentsDefaultPricingButton', 
				text: "{/literal}{#str_LabelDefaultPricing#}{literal}", 
				iconCls: 'silk-money',
				handler: onComponentPricing,
				disabled: true
			},'-',
			{ 
				id:'componentsPriceListsButton', 
				ref: '../componentsPriceListsButton', 
				text: "{/literal}{#str_LabelPriceLists#}{literal}", 
				iconCls: 'silk-money',
				handler: onComponentPriceLists
			}
			{/literal}{if $optionms}{literal}
			,'-',
			new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelEnableGrouping#}{literal}", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }})
			 {/literal}{/if}{literal}
			,{xtype:'tbfill'},
			{
				id:'hideInactiveButton',
				ref: '../hideInactiveButton',
				tooltip: '{/literal}{#str_TooltipHideInactiveItems#}{literal}',
				iconCls: 'hideInactiveButton',
				handler: onHideInactive,
				enableToggle: true,
				xtype: 'button',
				ctCls:'x-toolbar-standardbutton'
				},
				{xtype: 'tbspacer', width: 10} 
			],
			bbar: new Ext.PagingToolbar({ pageSize: 100, store: gridDataStoreObj, displayInfo: true, listeners: { beforechange: carryHideInactiveIntoPagingToolbarRefresh }})
	});
	gridDataStoreObj.load({ params: { start: 0, limit: 100, fields: '', query: ''}});
	
	var dialogFormPanelObj = new Ext.FormPanel({
		id: 'componentTypeGrid',
        labelAlign: 'left',
        labelWidth:20,
        autoHeight: true,
        frame:true,
        layout:'form',
        cls: 'left-right-buttons',
        items: componentGrid
    });
		
	var gComponentsDialogObj = new Ext.Window({
		id: 'componentsDialog',
		closable:false,
		plain:true,
		modal:true,
		draggable:true,
		title: "{/literal}{#str_SectionWindowTitleComponents#}{literal}".replace("^0", category),
		resizable:false,
		height: 'auto',
	  	width: 1200,
		tools:[{
			id:'clse',
		    qtip: '{/literal}{#str_LabelCloseWIndow#}{literal}',
		    handler: function(){ gComponentsDialogObj.close(); }
		}],
		items: dialogFormPanelObj,
		listeners: {
		'close': {   
			fn: function(){
		componentsWindowExists = false;
			}
		}
	}
	});
	gComponentsDialogObj.show();	 
}

{/literal}