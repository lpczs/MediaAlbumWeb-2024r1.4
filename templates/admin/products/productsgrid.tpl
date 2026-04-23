{literal}

function initialize(pParams) 
{
	productEditWindowExists = false;
	productPricingGridWindowExists = false;
	productPriceListGridWindowExists = false;
	productCongfigWindowExists = false;
	product3DPreviewWindowExists = false;
	productLinkingPreviewWindowExists = false;
	
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

	function statusRenderer(value, p, record)
	{
		if (value == 0)
		{
			className =  'class = "inactive"';
			
			if (record.data.hasprice == 0)
			{
				return '{/literal}<span '+className+'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{#str_LabelInactive#}</span>{literal}';
			}
			else
			{
				return '{/literal}<img src="{$webroot}/utils/ext/images/silk/money_dollar.png" /><span '+className+'>{#str_LabelInactive#}</span>{literal}';
			}
			
		}
		else
		{
			if (record.data.hasprice == 0)
			{
				return '{/literal}<span class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{#str_LabelActive#}</span>{literal}';
			}
			else
			{
				return '{/literal}<img src="{$webroot}/utils/ext/images/silk/money_dollar.png" /><span class="">{#str_LabelActive#}</span>{literal}';
			}
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
	
	function clearGrouping(v)
	{ 
		if(v.checked)
		{
			{/literal}{if $optionms}{literal}
				gridDataStoreObj.groupBy('companycodeHidden');
			{/literal}{else}{literal}
				gridDataStoreObj.groupBy('active');
			{/literal}{/if}{literal}
		}
		else 
		{
			gridDataStoreObj.clearGrouping(); 
		}
	}
			
	/* edit handler */
	function onEdit(btn, ev)
	{		 
		/* server parameters are sent to the server */
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('productgrid'));
		serverParams['id'] = id;

        if (!productEditWindowExists)
		{
			productEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProducts.editDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}
	
	/*product config handler*/
	function onProductConfig(btn, ev)
	{		 
		/* server parameters are sent to the server */
		var gridObj = gMainWindowObj.findById('productgrid');
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('productgrid'));
		serverParams['id'] = id;
		
		if (!productCongfigWindowExists)
		{
			productCongfigWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProducts.productConfigDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}

	function onLinkingPreview(btn, ev)
	{
		var serverParams = new Object();

		if (! productLinkingPreviewWindowExists)
		{
			productLinkingPreviewWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProducts.linkingPreviewDisplay.&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}

	/* 3d preview handler */
	function on3DPreviewConfig(btn, ev)
	{
		/* server parameters are sent to the server */
		var gridObj = gMainWindowObj.findById('productgrid');
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('productgrid'));
		serverParams['id'] = id;

		if (!product3DPreviewWindowExists)
		{
			product3DPreviewWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=Admin3DPreview.modelListGrid&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}

	function onAssign3DPreviewToProduct()
	{
		var serverParams = new Object();
		Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=Admin3DPreview.get3DModelList&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
	}

	function onUnAssign3DPreviewToProduct()
	{
		new Ext.FormPanel(
		{
			id: 'modelformpanel',
			frame: false,
			post: false,
			hidden: true
		});

		Ext.MessageBox.show(
		{
			title: "{/literal}{#str_TitleRemove3DPreviewFromSelectedProducts#}{literal}",
			msg: "{/literal}{#str_MessageConfirmRemove3DPreviewFromProducts#}{literal}",
			buttons: {
				ok: '{/literal}{#str_LabelRemove3DPreviewsFromSelectedProducts#}{literal}',
				cancel: true
			},
			fn: function(pBtn)
			{
				if (pBtn == 'ok')
				{
					onUnAssign();
				}
			},
			icon: Ext.MessageBox.QUESTION
		});
	}

	/* assign handler */
	function onUnAssign(btn, ev)
	{
		var fp = Ext.getCmp('modelformpanel');
		var form = fp.getForm();

		var paramArray = new Object();

		var productStore = Ext.getCmp('productgrid').store;
		var productIDList = Ext.taopix.gridSelection2IDList(Ext.getCmp('productgrid'));
		var productIDArray = productIDList.split(',');
		var productIDArrayLength = (productIDList != "") ? productIDArray.length : 0;
		var productCodesArray = [];
		var productsAssignedArray = [];

		for (i = 0; i < productIDArrayLength; i++)
		{
			var record = productStore.getById(productIDArray[i]);
			productCodesArray.push(record.data.code);
		}

		paramArray['productcodes'] = productCodesArray;

		var submitURL = 'index.php?fsaction=Admin3DPreview.unLink3DPreviewModelToProducts&ref={/literal}{$ref}{literal}';

		Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", unAssignCallback);

		return false;
	}

	function unAssignCallback(pSuccess, pActionForm, pActionData)
	{
		if (pSuccess)
		{
			var gridObj = gMainWindowObj.findById('productgrid');
			var dataStore = gridObj.store;
			dataStore.reload();

			gridObj.getSelectionModel().clearSelections();

			Ext.MessageBox.show(
			{
				title: "{/literal}{#str_Title3DPreviewSuccessullyRemoved#}{literal}",
				msg: "{/literal}{#str_MessageAny3DPreviewsHaveBeenRemoved#}{literal}",
				buttons: Ext.MessageBox.OK,
				icon: Ext.MessageBox.INFO
			});
		}
		else
		{
			Ext.MessageBox.show(
			{
				title: pActionData.result.title,
				msg: pActionData.result.msg,
				buttons: Ext.MessageBox.OK,
				icon: icon
			});
		}
	}

	/* pricing handler */
	function onPricing(btn, ev)
	{		 
		/* server parameters are sent to the server */
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('productgrid'));
		serverParams['id'] = id;
		
		if (!productPricingGridWindowExists)
		{
			productPricingGridWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProductPricing.initialize&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}
	
	function onProductPriceLists(btn, ev)
	{		
		var serverParams = new Object();
		serverParams['pricingmodel'] = 3;
		
		if (!productPriceListGridWindowExists)
		{
			productPriceListGridWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProductPricing.priceListsInitialize&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}

	/* delete handler */	  
	function onDelete(btn, ev)
	{
		var gridObj = gMainWindowObj.findById('productgrid');
		var dataStore = gridObj.store;
		var paramArray = {};
		
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
			
		paramArray['productcodes'] = codeList;

		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminProducts.checkProductDeletionWarnings', "{/literal}{#str_MessageUpdating#}{literal}", onCheckDeleteCallback);
	}

	function onCheckDeleteCallback(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated == true)
		{
			if (pActionData.result.msg)
			{
				Ext.MessageBox.confirm('{/literal}{#str_LabelConfirmation#}{literal}', nlToBr(pActionData.result.msg), onDeleteResult);
			}
		}
	}

	function onDeleteResult(btn)
	{
		if (btn == "yes")
		{
			var paramArray = new Object();
					
			var gridObj = gMainWindowObj.findById('productgrid');
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
			
			paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('productgrid'));
			paramArray['codelist'] = codeList;
			
			Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminProducts.delete', '{/literal}{#str_MessageDeleting#}{literal}', onDeleteCallback);	
		}
	}

	function onDeleteCallback(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated == true)
		{
			var gridObj = gMainWindowObj.findById('productgrid');
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
	
	function onActivate(btn, ev)
	{ 
		/* server parameters are sent to the server */
		var serverParams = new Object();
		serverParams['ids'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('productgrid'));	
		
		var active = 0; 

		switch (btn.id)
		{
		case 'productActiveButton':
			active = 1;
			break;
		case 'productInctiveButton':
			active = 0;
			break;
		}	

		serverParams['active'] = active;

		Ext.taopix.formPost(gMainWindowObj, serverParams, 'index.php?fsaction=AdminProducts.productActivate', '{/literal}{#str_MessageUpdating#}{literal}', activateCallback);
	}

	function activateCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var gridObj = gMainWindowObj.findById('productgrid');
			var dataStore = gridObj.store;
		
			Ext.taopix.updateDataStore(dataStore, pActionData.result.data);
			dataStore.reload();
		}
	}

	function onHideInactive(btn, ev)
	{
		var gridDataStore = Ext.getCmp('productgrid').store;
		var hideInactive = 0;

		if (btn.pressed)
		{
			hideInactive = 1;
			Ext.getCmp('hideInactiveButton').setTooltip('{/literal}{#str_TooltipShowInactiveItems#}{literal}');
		}
		else
		{
			Ext.getCmp('hideInactiveButton').setTooltip('{/literal}{#str_TooltipHideInactiveItems#}{literal}');			
		}
	
		gridDataStore.lastOptions.params['hideInactive'] = hideInactive;

		gridDataStore.reload({params: gridDataStore.lastOptions.params});
	}

	function checkHideInactiveButton(pStore, pOptions)
	{
		var hideInactiveButton = Ext.getCmp('hideInactiveButton');

		if ((pStore.baseParams.query != '') && (typeof pStore.baseParams.query != 'undefined'))
		{
			hideInactiveButton.toggle(false);
			hideInactiveButton.disable();
			hideInactiveButton.setTooltip({text: '{/literal}{#str_TooltipHideInactiveItemsIsDisabledForSearchResults#}{literal}', autoHide: true, id: 'hideInactiveDisabledTooltip'});
		}
		else
		{
			if (hideInactiveButton.disabled == true)
			{
				hideInactiveButton.enable();
				var gridDataStore = Ext.getCmp('productgrid').store;

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
		
	Ext.QuickTips.init();
	Ext.form.Field.prototype.msgTarget = 'side';
	
	var gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj) 
			{
				var selectionCount = gridCheckBoxSelectionModelObj.getCount();

				var btnThreeDPreviewAssign = Ext.getCmp('threedpreviewassign');
				var btnThreeDPreviewUnassign = Ext.getCmp('threedpreviewunassign');

				if (selectionCount == 1)
				{
                    grid.editButton.enable();
                    grid.deleteButton.enable();
                    grid.pricingButton.enable();
                    grid.productconfig.enable();
                    grid.productActiveButton.enable();
                    grid.productInctiveButton.enable();
					grid.linkingPreviewButton.enable();

					if (btnThreeDPreviewAssign)
					{
						btnThreeDPreviewAssign.enable();
					}

					if (btnThreeDPreviewUnassign)
					{
						Ext.getCmp('threedpreviewunassign').enable();
					}
				}
				else
				{
					grid.editButton.disable();
                    grid.deleteButton.disable();
                    grid.productconfig.disable();
                    grid.pricingButton.disable();
                    grid.productActiveButton.disable();
                    grid.productInctiveButton.disable();
					grid.linkingPreviewButton.disable();

					if (selectionCount > 1)
					{
						if (btnThreeDPreviewAssign)
						{
							btnThreeDPreviewAssign.enable();
						}

						if (btnThreeDPreviewUnassign)
						{
							btnThreeDPreviewUnassign.enable();
						}
					}
					else
					{
						if (btnThreeDPreviewAssign)
						{
							btnThreeDPreviewAssign.disable();
						}

						if (btnThreeDPreviewUnassign)
						{
							btnThreeDPreviewUnassign.disable();
						}
					}
				}
                
                if (grid)
                {
                    var selRecords = grid.getSelectionModel().getSelections();
                }
				
				if (selectionCount == 1 || selectionCount > 1)
				{
                    var selectID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('productgrid'));
					var idList = selectID.split(',');
					for (i = 0; i < idList.length; i++)
					{
						record = Ext.getCmp('productgrid').store.getById(idList[i]);
						{/literal}{if $optionms}{literal}
								{/literal}{if $companyLogin}{literal}
									if (record.data['companycode'] == '' && idList[i] > 0)
									{
										grid.editButton.disable();
                                        grid.deleteButton.disable();
                                        grid.productActiveButton.disable();
                                        grid.productInctiveButton.disable();
										break;
									}
									else
									{
										if(selectionCount == 1)
										{
											grid.editButton.enable();
                                            grid.pricingButton.enable();
                                            grid.productconfig.enable();
										}
										grid.deleteButton.enable();
                                        grid.productActiveButton.enable();
                                        grid.productInctiveButton.enable();
									}
								{/literal}{else}{literal}
									if (record.data['code'] == '' && record.data['companycode'] == '' && idList[i] > 0)
									{
										grid.editButton.disable();
                                        grid.deleteButton.disable();
                                        grid.pricingButton.disable();
                                        grid.productconfig.disable();
                                        grid.productActiveButton.disable();
                                        grid.productInctiveButton.disable();
										break;
									}
									else
									{
										grid.deleteButton.enable();
                                        grid.productActiveButton.enable();
                                        grid.productInctiveButton.enable();
									}
							{/literal}{/if}{literal}
						{/literal}{else}{literal}
							if (record.data['code'] == '' && idList[i] > 0)
							{
								grid.editButton.disable();
                                grid.deleteButton.disable();
                                grid.pricingButton.disable();
                                grid.productconfig.disable();
                                grid.productActiveButton.disable();
                                grid.productInctiveButton.disable();
								break;
							}
							else
							{
								grid.deleteButton.enable();
                                grid.productActiveButton.enable();
                                grid.productInctiveButton.enable();
							}
					 {/literal}{/if}{literal}
					}
				}
				else
				{
					grid.editButton.disable();
                    grid.deleteButton.disable();
                    grid.pricingButton.disable();
                    grid.productconfig.disable();
                    grid.pricingButton.disable();
                    grid.productActiveButton.disable();
                    grid.productInctiveButton.disable();
				}
			}
		}
	});
			
	var gridDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		{/literal}{if $optionms}{literal}
			groupField: 'companycodeHidden',
		{/literal}{/if}{literal} 
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminProducts.getGridData&ref={/literal}{$ref}{literal}'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
			{/literal}{if $optionms}{literal}		    
				{name: 'id', mapping: 0},
				{name: 'companycode', mapping: 1},
			    {name: 'category', mapping: 2},
				{name: 'code', mapping: 3},
				{name: 'name', mapping: 4},
				{name: 'productcollection', mapping: 5},
				{name: 'active', mapping: 6},
				{name: 'companycodeHidden', mapping: 7},
				{name: 'hasprice', mapping: 8},
				{/literal}{if $threedpreviewavailable}{literal}
				{name: 'collectiontype', mapping: 9},
				{name: 'resourcecode', mapping: 10}
			{/literal}{/if}{else}{literal}
				{name: 'id', mapping: 0},
			    {name: 'category', mapping: 1},
				{name: 'code', mapping: 2},
				{name: 'name', mapping: 3},
				{name: 'productcollection', mapping: 4},
				{name: 'active', mapping: 5},
				{name: 'hasprice', mapping: 6},
				{/literal}{if $threedpreviewavailable}{literal}
				{name: 'collectiontype', mapping: 7},
				{name: 'resourcecode', mapping: 8}
				{/literal}{/if}{literal}
				{/literal}{/if}{literal}
			])
		),
		sortInfo:{field: 'code', direction: "ASC"},
		listeners: {beforeload: checkHideInactiveButton},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});

	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
			sortable: true, 
			resizable: true
		},
		columns: [
			gridCheckBoxSelectionModelObj,
			{/literal}{if $optionms}{literal}
				{header: '{/literal}{#str_LabelCompany#}{literal}', width: 100, dataIndex: 'companycode', renderer: companyRenderer},
				{header: '{/literal}{#str_LabelCode#}{literal}', width: 200, dataIndex: 'code', renderer: generalColumnRenderer},
				{header: '{/literal}{#str_LabelName#}{literal}', width: 250, sortable: false, dataIndex: 'name', renderer: generalColumnRenderer},
				{header: '{/literal}{#str_AutoUpdateTitleProductCollections#}{literal}', width: 220, sortable: false, dataIndex: 'productcollection',renderer: generalColumnRenderer},
				{/literal}{if $threedpreviewavailable}{literal}
				{header: '{/literal}{#str_Label3DPreview#}{literal}', renderer: generalColumnRenderer, width: 100, dataIndex: 'resourcecode'},
				{/literal}{/if}{literal}
				{header: '{/literal}{#str_LabelStatus#}{literal}', renderer: statusRenderer, width: 250, dataIndex: 'active'},				
				{header: "{/literal}{#str_LabelCompany#}{literal}", width: 200, dataIndex: 'companycodeHidden', hidden: true, renderer: companyCodeRenderer}
			{/literal}{else}{literal}
				{header: '{/literal}{#str_LabelCode#}{literal}', width: 250, dataIndex: 'code', renderer: generalColumnRenderer},
				{header: '{/literal}{#str_LabelName#}{literal}', width: 300, sortable:false, renderer: generalColumnRenderer, dataIndex: 'name'},
				{header: '{/literal}{#str_AutoUpdateTitleProductCollections#}{literal}', width: 250, sortable: false, dataIndex: 'productcollection',renderer: generalColumnRenderer},
				{/literal}{if $threedpreviewavailable}{literal}
				{header: '{/literal}{#str_Label3DPreview#}{literal}', renderer: generalColumnRenderer, width: 300, dataIndex: 'resourcecode'},
				{/literal}{/if}{literal}
				{header: '{/literal}{#str_LabelStatus#}{literal}', renderer: statusRenderer, width: 250, dataIndex: 'active'}
			{/literal}{/if}{literal}
		]
	});

	var grid = new Ext.grid.GridPanel({
		id: 'productgrid',
		store: gridDataStoreObj,
		cm: gridColumnModelObj,
		enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		enableHdMenu:false,
		trackMouseOver:false,
		stripeRows:true,
		ctCls: 'grid',
		plugins: [new Ext.ux.grid.Search({
			iconCls: 'silk-zoom',
			minChars: 3,
			width: 200,
			disableIndexes:['active','companycode','companycodeHidden','category','productcollection'],
			autoFocus: true
		})],

		columnLines:true,
		{/literal}{if $optionms}{literal}
			view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_SectionTitleProducts#}{literal}" : "{/literal}{#str_LabelDiscountSectionPRODUCT#}{literal}"]})' }),
			{/literal}{if $threedpreviewavailable}{literal}
			autoExpandColumn: 3,
			{/literal}{else}{literal}
			autoExpandColumn: 3,
			{/literal}{/if}
		{else}
			{if $threedpreviewavailable}{literal}
			autoExpandColumn: 2,
			{/literal}{else}{literal}
			autoExpandColumn: 2,
			{/literal}{/if}
		{/if}{literal}
		selModel: gridCheckBoxSelectionModelObj,
		tbar: [
			{
				ref: '../editButton',
				text: '{/literal}{#str_ButtonEdit#}{literal}',
				iconCls: 'silk-pencil',
				handler: onEdit,
				disabled: true
			}
			,'-',
			{
				ref: '../deleteButton',
				text: '{/literal}{#str_ButtonDelete#}{literal}',
				iconCls: 'silk-delete',
				handler: onDelete,
				disabled: true
			},'-', 
			{ 
				id:'pricingButton', 
				ref: '../pricingButton', 
				text: '{/literal}{#str_ButtonPricing#}{literal}', 
				iconCls: 'silk-money', 
				handler: onPricing,
				disabled: true	
			}, '-',
			{ 
				id:'productPriceListsButton', 
				ref: '../productPriceListsButton', 
				text: "{/literal}{#str_LabelPriceLists#}{literal}", 
				iconCls: 'silk-money',
				handler: onProductPriceLists
			}, '-',
			{ 
				id:'productconfig', 
				ref: '../productconfig', 
				text: "{/literal}{#str_LabelProductConfiguration#}{literal}",  
				iconCls: 'silk-chart-organisation',
				handler: onProductConfig,
				disabled: true	
			}, '-',
			{/literal}{if $threedpreviewavailable}{literal}
			{
				xtype: 'splitbutton',
				id: 'threedpreview',
				ref: '../threedpreview',
				text: "{/literal}{#str_Label3DPreview#}{literal}",
				iconCls: 'threeDPreview',
				menu: new Ext.menu.Menu(
				{
					id: 'threedpreviewsmenu',
					items:
					[
						{
							text: "{/literal}{#str_LabelCreateAndManage3DPreviews#}{literal}",
							handler: on3DPreviewConfig,
							iconCls: '',
							ref: '../previewmanage',
						},
						{
							id: 'threedpreviewassign',
							text: "{/literal}{#str_LabelAssign3DPreviewToSelectedProducts#}{literal}",
							handler: onAssign3DPreviewToProduct,
							iconCls: '',
							ref: '../threedpreviewassign',
							disabled: true
						},
						{
							id: 'threedpreviewunassign',
							text: "{/literal}{#str_LabelRemove3DPreviewFromSelectedProducts#}{literal}",
							handler: onUnAssign3DPreviewToProduct,
							iconCls: '',
							ref: '../previewunassign',
							disabled: true
						}
					]
				})
			},
			'-',
			{/literal}{/if}{literal}
			{ 
				id:'productActiveButton',
				ref: '../productActiveButton', 
				text: '{/literal}{#str_LabelMakeActive#}{literal}', 
				iconCls: 'silk-lightbulb',
				handler: onActivate, 
				disabled: true
			}, '-',
			{ 
				id:'productInctiveButton', 
				ref: '../productInctiveButton', 
				text: '{/literal}{#str_LabelMakeInactive#}{literal}', 
				iconCls: 'silk-lightbulb-off',
				handler: onActivate, 
				disabled: true	
			}, '-',
			{
				id:'linkingPreviewButton', 
				ref: '../linkingPreviewButton', 
				text: '{/literal}{#str_LabelLinkingPreview#}{literal}', 
				iconCls: 'silk-link',
				handler: onLinkingPreview, 
				disabled: true
			}
			{/literal}{if $optionms}{literal}
				, '-',
				new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelEnableGrouping#}{literal}", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }})
			{/literal}{/if}{literal},
			{xtype:'tbfill'},
			{
				id:'hideInactiveButton',
				ref: '../hideInactiveButton',
				tooltip: '{/literal}{#str_TooltipHideInactiveItems#}{literal}',
				iconCls: 'hideInactiveButton',
				handler: onHideInactive,
				enableToggle: true,
				xtype: 'button',
				ctCls:'x-toolbar-standardbutton'
			}
			,{xtype: 'tbspacer', width: 10} 
			],
			bbar: new Ext.PagingToolbar({ pageSize: 100, store: gridDataStoreObj, displayInfo: true, listeners: { beforechange: carryHideInactiveIntoPagingToolbarRefresh}})
	});
				
	gridDataStoreObj.load({
		params: {
			start: 0,
			limit: 100,
			fields: '',
			query: '',
			hideInactive: 0
		}
	});
	
	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "{/literal}{#str_SectionTitleProducts#}{literal}",
		items: grid,
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
}

{/literal}