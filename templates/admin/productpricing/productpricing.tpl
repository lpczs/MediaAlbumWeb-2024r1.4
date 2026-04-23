{literal}

function initialize(pParams)
{
	Ext.QuickTips.init();
	Ext.form.Field.prototype.msgTarget = 'side';

	productPricingEditWindowExists = false;

	var gridObj = gMainWindowObj.findById('productgrid');
	var selRecord = gridObj.selModel.getSelections();
	var productCount = selRecord[0].data.productcount;
	var warningArray = [];
	var warningHeight = 0;

	//if multiple products use this layout code the we need to warn that all will be updated
	if (productCount > 1)
	{
		warningArray.push({
			xtype: 'panel',
			style: { height: "27px", backgroundColor: "#fdfcd2" },
			flex: true,
			ctCls: "warning-bar",
			height: 27,
			tpl: new Ext.XTemplate('<tpl for="."><div class="warning-message">{error}</div></tpl>'),
			data: [
				{error: "{/literal}{#str_LabelUpdateWarningPricing#}{literal}".replace('^0', '{/literal}{$layoutcode}{literal}').replace('^1', productCount)}
			]
		});

		warningHeight += 29;
	}

	var gWarningPanel = new Ext.Panel({
		id: 'warningpanel',
		style: { height: warningHeight },
		height: warningHeight,
		items: warningArray
	});

	var pricingGridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(pricingGridCheckBoxSelectionModelObj)
			{
				var selectionCount = pricingGridCheckBoxSelectionModelObj.getCount();

				if (selectionCount == 1)
				{
					pricingGrid.editButton.enable();
				}
				else
				{
					pricingGrid.editButton.disable();
				}

				if (selectionCount > 0)
				{
					pricingGrid.activeButton.enable();
					pricingGrid.inactiveButton.enable();
					pricingGrid.deleteButton.enable();
				}
				else
				{
					pricingGrid.activeButton.disable();
					pricingGrid.inactiveButton.disable();
					pricingGrid.deleteButton.disable();
				}

				var canDelete = true;

				if (selectionCount == 1 || selectionCount > 1)
				{
					var selectID = Ext.taopix.gridSelection2IDList(gDialogObj.findById('pricingGrid'));
					var idList = selectID.split(',');

					for (i = 0; i < idList.length; i++)
					{
						record = Ext.getCmp('pricingGrid').store.getById(idList[i]);

						{/literal}{if $optionms}{literal}
							{/literal}{if $companyLogin}{literal}
								if (record.data['company'] == '' && idList[i] > 0)
								{
									pricingGrid.editButton.disable();
									pricingGrid.deleteButton.disable();
									pricingGrid.activeButton.disable();
									pricingGrid.inactiveButton.disable();
									break;
								}
								else
								{
									if(selectionCount == 1)
									{
										pricingGrid.editButton.enable();
									}
									pricingGrid.deleteButton.enable();
								}
							{/literal}{/if}{literal}
						{/literal}{/if}{literal}
					}
				}
			}
		}
	});

	var pricingGridDataStoreObj = new Ext.data.GroupingStore({
		id:'pricingdatastore',
		remoteSort: true,
		{/literal}{if $optionms}{literal}
			groupField: 'companycodeHidden',
		{/literal}{/if}{literal}
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminProductPricing.getGridData&ref={/literal}{$ref}{literal}&id={/literal}{$id}{literal}'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
			{/literal}{if $optionms}{literal}
				{name: 'id', mapping: 0},
			    {name: 'company', mapping: 1},
				{name: 'lkey', mapping: 2},
				{name: 'pricedesc', mapping: 3},
				{name: 'qtyrangestart', mapping: 4},
				{name: 'qtyrangeend', mapping: 5},
				{name: 'baseprice', mapping: 6},
				{name: 'unitprice', mapping: 7},
				{name: 'linesubtract', mapping: 8},
				{name: 'it', mapping: 9},
				{name: 'active', mapping: 10},
				{name: 'companycodeHidden', mapping: 11}
			{/literal}{else}{literal}
				{name: 'id', mapping: 0},
				{name: 'lkey', mapping: 1},
				{name: 'pricedesc', mapping: 2},
				{name: 'qtyrangestart', mapping: 3},
				{name: 'qtyrangeend', mapping: 4},
				{name: 'baseprice', mapping: 5},
				{name: 'unitprice', mapping: 6},
				{name: 'linesubtract', mapping: 7},
				{name: 'it', mapping: 8},
				{name: 'active', mapping: 9}
			{/literal}{/if}{literal}
			])
		),
		sortInfo:{field: 'lkey', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});

	var pricingGridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
			sortable: false,
			resizable: true
		},
		columns: [
			pricingGridCheckBoxSelectionModelObj,
			{/literal}{if $optionms}{literal}
			{header: '{/literal}{#str_LabelCompany#}{literal}', width: 140, renderer: companyRenderer, dataIndex: 'company'},
			{/literal}{/if}{literal}
			{header: '{/literal}{#str_LabelLicenseKey#}{literal}', width: 160, renderer: generalColumnRenderer, dataIndex: 'lkey'},
			{header: '{/literal}{#str_LabelClientPriceDescription#}{literal}', renderer: generalColumnRenderer, width: 220, dataIndex: 'pricedesc'},
			{header: '{/literal}{#str_QtyPriceRangeStart#}{literal}', width: 80, renderer: generalColumnRenderer, dataIndex: 'qtyrangestart'},
			{header: '{/literal}{#str_QtyPriceRangeEnd#}{literal}', width: 80, renderer: generalColumnRenderer, dataIndex: 'qtyrangeend'},
			{header: '{/literal}{#str_PriceRangeBasePrice#}{literal}', width: 80, renderer: generalColumnRenderer, dataIndex: 'baseprice'},
			{header: '{/literal}{#str_PriceRangeUnitPrice#}{literal}', width: 80, renderer: generalColumnRenderer, dataIndex: 'unitprice'},
			{header: '{/literal}{#str_LabelLineSubtract#}{literal}', width: 100, renderer: generalColumnRenderer, dataIndex: 'linesubtract'},
			{header: "{/literal}{#str_LabelIncludesTax#}{literal}", width: 110, dataIndex: 'it', renderer: generalColumnRenderer},
			{header: '{/literal}{#str_LabelStatus#}{literal}', renderer: statusRenderer, width: 50, dataIndex: 'active'},
			{header: "{/literal}{#str_LabelCompany#}{literal}", width: 200, dataIndex: 'companycodeHidden', hidden: true, renderer: companyCodeRenderer}
		]
	});


	var pricingGrid = new Ext.grid.GridPanel({
		id: 'pricingGrid',
		store: pricingGridDataStoreObj,
		cm: pricingGridColumnModelObj,
		height: 385,
		enableColLock:false,
		border: false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		enableHdMenu:false,
		trackMouseOver:false,
		stripeRows:true,
		columnLines:true,
		ctCls: 'grid',
		style:'border:1px solid #99BBE8',
		{/literal}{if $optionms}{literal}
			view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_LabelPrices#}{literal}" : "{/literal}{#str_LabelPrice#}{literal}"]})' }),
			autoExpandColumn: 10,
		{/literal}{else}{literal}
		autoExpandColumn:9,
		{/literal}{/if}{literal}
		selModel: pricingGridCheckBoxSelectionModelObj,
		tbar: [
			{
				text: '{/literal}{#str_ButtonAdd#}{literal}',
				iconCls: 'silk-add',
				handler: onAdd
			}, '-',
			{
				ref: '../editButton',
				text: '{/literal}{#str_ButtonEdit#}{literal}',
				iconCls: 'silk-pencil',
				handler: onEditPricing,
				disabled: true
			}
			,'-', {
				ref: '../deleteButton',
				text: '{/literal}{#str_ButtonDelete#}{literal}',
				iconCls: 'silk-delete',
				handler: onDeletePricing,
				disabled: true
			}, '-',
			{
				id:'activeButton',
				ref: '../activeButton',
				text: '{/literal}{#str_LabelMakeActive#}{literal}',
				iconCls: 'silk-lightbulb',
				handler: onPricingActivate,
				disabled: true
			}, '-',
			{
				id:'inactiveButton',
				ref: '../inactiveButton',
				text: '{/literal}{#str_LabelMakeInactive#}{literal}',
				iconCls: 'silk-lightbulb-off',
				handler: onPricingActivate,
				disabled: true
			}
				{/literal}{if $optionms}{literal}
				,'-',
					new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelEnableGrouping#}{literal}", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }})
				{/literal}{/if}{literal}
			]
	});

	pricingGridDataStoreObj.load();

	function clearGrouping(v){
		if(v.checked)
		{
			{/literal}{if $optionms}{literal}
				pricingGridDataStoreObj.groupBy('companycodeHidden');
			{/literal}{else}{literal}
				pricingGridDataStoreObj.groupBy('active');
			{/literal}{/if}{literal}
		}
		else
		{
			pricingGridDataStoreObj.clearGrouping();
		}
	}

	var pricingFormPanelObj = new Ext.FormPanel({
		id: 'pricingformpanel',
        labelAlign: 'left',
        labelWidth:60,
        height: 400 + warningHeight,
		cls: 'x-panel-body',
        frame:true,
        bodyStyle:'padding:0px 0px 0px 0px',
        items: [
				gWarningPanel,
                pricingGrid
        ]
    });

	gDialogObj = new Ext.Window({
		id: 'pricingWindow',
	  	closable:false,
	  	title: "{/literal}{$title}{literal}",
	  	plain:true,
	  	modal:true,
	  	draggable:true,
	 	resizable:false,
	  	layout: 'fit',
	  	height: 'auto',
	  	width: 1200,
	  	cls: 'left-right-buttons',
	  	items: pricingFormPanelObj,
	  	tools:[{
			id:'clse',
		    qtip: '{/literal}{#str_LabelCloseWIndow#}{literal}',
		    handler: function(){ Ext.getCmp('pricingWindow').close(); productPricingGridWindowExists = false;}
		}]
	});

	var mainPanel = Ext.getCmp('pricingWindow');
	mainPanel.show();
}

function statusRenderer(value, p, record)
{
	if (value == 0)
	{
		className =  'class = "inactive"';
		return '{/literal}<span '+className+">{#str_LabelInactive#}</span>{literal}";
	}
	else
	{
		return "{/literal}{#str_LabelActive#}{literal}";
	}
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

/* add handler */
function onAdd(btn, ev)
{
	if(!productPricingEditWindowExists)
	{
		productPricingEditWindowExists = true;
		Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProductPricing.addDisplay&ref={/literal}{$ref}{literal}&productid={/literal}{$id}{literal}', '', '', 'initialize', false);
	}
}

function onEditPricing(btn, ev)
{
	/* server parameters are sent to the server */
	var serverParams = new Object();

	var id = Ext.taopix.gridSelection2IDList(Ext.getCmp('pricingGrid'));
	var gridObj = Ext.getCmp('pricingGrid');
	var selRecords = gridObj.selModel.getSelections();
	var companycode = selRecords[0].data.company;

	serverParams['pricingid'] = id;
	serverParams['pricecompanycode'] = companycode;

	if (!productPricingEditWindowExists)
	{
		productPricingEditWindowExists = true;
		Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProductPricing.editDisplay&ref={/literal}{$ref}{literal}&productid={/literal}{$id}{literal}', serverParams, '', 'initialize', false);
	}
}

function onDeletePricing(btn, ev)
{
	var gridObj = Ext.getCmp('pricingGrid');
	var dataStore = gridObj.store;

	var selRecords = gridObj.selModel.getSelections();
	var codeList = '';

	for (var rec = 0; rec < selRecords.length; rec++)
	{
		codeList = codeList + selRecords[rec].data.lkey;

		if (rec != selRecords.length - 1)
		{
			codeList = codeList + ',';
		}
	}

	var message = nlToBr("{/literal}{#str_DeletePricingConfirmation#}{literal}");
	message = message.replace("^0", codeList);

	dataStore.load();
	Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", message, onDeletePricingResult);
}

function onDeletePricingResult(btn)
{
	if (btn == "yes")
	{
		var paramArray = new Object();

		paramArray['idlist'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('pricingGrid'));
		paramArray['lkeylist'] = Ext.taopix.gridSelection2LiceseKeyList(Ext.getCmp('pricingGrid'));

		Ext.taopix.formPost(Ext.getCmp('pricingWindow'), paramArray, 'index.php?fsaction=AdminProductPricing.delete&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageDeleting#}{literal}", onDeletePricingCallback);
	}
}

function onDeletePricingCallback(pUpdated, pTheForm, pActionData)
{
	if (pUpdated == true)
	{
		var gridObj = Ext.getCmp('pricingGrid');
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
		dataStore.load();
		Ext.getCmp('productgrid').store.reload();
	}
	gridObj.deleteButton.disable();
}

function onPricingActivate(btn, ev)
{
	/* server parameters are sent to the server */
	var serverParams = new Object();
	serverParams['ids'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('pricingGrid'));

	var gridObj = Ext.getCmp('pricingGrid');
	var dataStore = gridObj.store;

	var selRecords = gridObj.selModel.getSelections();
	var lkeyList = '';

	for (var rec = 0; rec < selRecords.length; rec++)
	{
		lkeyList = lkeyList + selRecords[rec].data.lkey.replace('<br>', ',');

		if (rec != selRecords.length - 1)
		{
			lkeyList = lkeyList + ',';
		}
	}

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
	serverParams['licensekeylist'] = lkeyList;

	Ext.taopix.formPost(Ext.getCmp('pricingWindow'), serverParams, 'index.php?fsaction=AdminProductPricing.pricingActivate&ref={/literal}{$ref}{literal}', '{/literal}{#str_MessageUpdating#}{literal}', pricingActivateCallback);
}

function pricingActivateCallback(pUpdated, pActionForm, pActionData)
{
	if (pUpdated)
	{
		var pricingGridObj = Ext.getCmp('pricingGrid');
		var dataStore = pricingGridObj.store;

		Ext.taopix.updateDataStore(dataStore, pActionData.result.data);
	}
}

function saveCallback(pUpdated, pActionForm, pActionData)
{
	if (pUpdated)
	{
		var gridObj = Ext.getCmp('pricingGrid');
		var dataStore = gridObj.store;

		gridObj.store.reload();
		gPricingEditObj.close();
	}
	else
	{
		icon = Ext.MessageBox.WARNING;

		Ext.MessageBox.show({
			title: pActionData.result.title,
			msg: pActionData.result.msg,
			buttons: Ext.MessageBox.OK,
			icon: icon
		});
	}

}

/* close functions */
function closePricingEditHandler(btn, ev)
{
	gPricingEditObj.close();
}
{/literal}