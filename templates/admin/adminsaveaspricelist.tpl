{literal}
var adminPriceListGlobalFlag = false;
var adminPriceListEditGlobalFlag = false;
var adminPriceListEditScreen = false;

function initializeSaveAsPriceList(pParams)
{
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
	
	Ext.QuickTips.init();
	Ext.form.Field.prototype.msgTarget = 'side';
	
	if (!Ext.getCmp('componentpricinggrid'))
	{
		var gridObj = Ext.getCmp('productgrid');
		var selRecords = gridObj.selModel.getSelections();
		var pricingModel = selRecords[0].data.pricingmodel;
		category = 'PRODUCT';
		
			var companyCombo = new Ext.taopix.CompanyCombo({
			id: 'pricelistcompanycode',
			name: 'pricelistcompanycode',
			width:380,
			fieldLabel: "{/literal}{#str_LabelCompany#}{literal}",
			hideLabel:false,
			allowBlank:false,
			defvalue: '{/literal}{$company}{literal}',
			options: {
				ref: '{/literal}{$ref}{literal}', 
				storeId: 'companyStore', 
				{/literal}{if $companyLogin}{literal}
				includeGlobal: '0', 
				{/literal}{else}{literal}
				includeGlobal: '1', 
				{/literal}{/if}{literal}
				includeShowAll: '0', 
				onchange: function(){var companyCode = companyCombo.getValue(); if (companyCode == 'GLOBAL') companyCode = '';}
			}
		});
	
	}
	else
	{
		var gridObj = Ext.getCmp('maingrid');
		var selRecords = gridObj.selModel.getSelections();
		var pricingModel = selRecords[0].data.pricingmodel;
		var category = selRecords[0].data.code;
		
			var companyCombo = new Ext.taopix.CompanyCombo({
			id: 'pricelistcompanycode',
			name: 'pricelistcompanycode',
			width:380,
			fieldLabel: "{/literal}{#str_LabelCompany#}{literal}",
			hideLabel:false,
			allowBlank:false,
			defvalue: '{/literal}{$company}{literal}',
			options: {
				ref: '{/literal}{$ref}{literal}', 
				storeId: 'companyStore', 
				{/literal}{if $companyLogin}{literal}
				includeGlobal: '0', 
				{/literal}{else}{literal}
				includeGlobal: '1', 
				{/literal}{/if}{literal}
				includeShowAll: '0', 
				onchange: function(){var companyCode = companyCombo.getValue(); if (companyCode == 'GLOBAL') companyCode = '';}
			}
		});
	}
	
	if (Ext.getCmp('productConfigPricingGridWindow'))
	{
		category = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.categorycode;
		pricingModel = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.pricingmodel;
		
		var companyCode = '';
		var gridObj = Ext.getCmp('productgrid');
		var selRecords = gridObj.selModel.getSelections();
		var productCompanyCode = selRecords[0].data.companycode;
		var includeGlobalAndSpecificCompany = '';
		var priceListIncludeGlobal = '0';
		var disabled = false;
		
		selectedComponentCompanyCode = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.companycode;
		
		{/literal}{if $optionms}{literal}
			{/literal}{if $companyLogin}{literal}
				priceListIncludeGlobal = '0';
				disabled = true;
				companyCode = '{/literal}{$company}{literal}';
			{/literal}{else}{literal}
				if (productCompanyCode != '')
				{
					companyCode = productCompanyCode;
					includeGlobalAndSpecificCompany = companyCode;
					
				}
				else
				{
					companyCode = selectedComponentCompanyCode;
					includeGlobalAndSpecificCompany = Ext.getCmp('company').getValue();
				}
				
				if (selectedComponentCompanyCode == '')
				{
					priceListIncludeGlobal = '1';
					disabled = false;
				}
				else
				{
					priceListIncludeGlobal = '0';
					disabled = true;
				}
			{/literal}{/if}{literal}		
		{/literal}{/if}{literal}
		
		var companyCombo = new Ext.taopix.CompanyCombo({
		id: 'pricelistcompanycode',
		name: 'pricelistcompanycode',
		width:380,
		fieldLabel: "{/literal}{#str_LabelCompany#}{literal}",
		hideLabel:false,
		allowBlank:false,
		defvalue: companyCode,
		disabled: disabled,
		options: {
			ref: '{/literal}{$ref}{literal}', 
			storeId: 'companyStore', 
			includeGlobal: priceListIncludeGlobal, 
			includeShowAll: '0',
			includeGlobalAndSpecificCompany: includeGlobalAndSpecificCompany, 
			onchange: function(){var companyCode = companyCombo.getValue(); if (companyCode == 'GLOBAL') companyCode = '';}
		}
	});
	}
	
	var priceListStore = new Ext.data.Store({
		id: 'priceListStore',
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AjaxAPI.callback&ref={/literal}{$ref}{literal}&cmd=PRICELISTS&category='+category+'&company=' + companyCode + '&displayCustom=0'}),
		reader: new Ext.data.ArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
		    {name: 'id', mapping: 0},
			{name: 'code', mapping: 1},
			{name: 'name', mapping: 2},
			{name: 'active', mapping: 4},
			{name: 'decimalplaces', mapping: 5}
			
			])
		)
	});
	
	priceListStore.load();
	
	var adminPriceListGridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(adminPriceListGridCheckBoxSelectionModelObj) 
			{
				var selectionCount = adminPriceListGridCheckBoxSelectionModelObj.getCount();
				
				if (selectionCount == 1)
				{
					adminPriceListGrid.adminPriceListEditButton.enable();
				}
				else
				{
					adminPriceListGrid.adminPriceListEditButton.disable();
				}
				
				var canDelete = true;
				
				if (adminPriceListGrid)
				{
					var selRecords = adminPriceListGrid.getSelectionModel().getSelections();
				}

				if ((selectionCount > 0) && (canDelete))
				{
					adminPriceListGrid.adminPriceListActiveButton.enable();
					adminPriceListGrid.adminPriceListInactiveButton.enable();
					adminPriceListGrid.adminPriceListDeleteButton.enable();
				} 
				else
				{
					adminPriceListGrid.adminPriceListActiveButton.disable();
					adminPriceListGrid.adminPriceListInactiveButton.disable();
					adminPriceListGrid.adminPriceListDeleteButton.disable();
				}
			}
		}
	});
	
	var adminPriceListGrid =  new Ext.grid.GridPanel({
		id: 'adminPriceListGrid',
		style:'border:1px solid #B5B8C8; margin-bottom:5px',
		width: 463,
		height: 250,
		deferRowRender:false,
		border: true,
		store: priceListStore,
		colModel: new Ext.grid.ColumnModel({
			defaults: {
				sortable: true, 
				resizable: true
			},
			columns: [ 
			     adminPriceListGridCheckBoxSelectionModelObj,
				{header: 'ID', width: 30, dataIndex: 'id', sortable: true, hidden: true},
				{header: "{/literal}{#str_LabelCode#}{literal}", renderer: generalColumnRenderer, width: 80, dataIndex: 'code'},
				{header: "{/literal}{#str_LabelName#}{literal}", renderer: generalColumnRenderer, width: 200, dataIndex: 'name'},
				{header: "{/literal}{#str_LabelStatus#}{literal}", renderer: statusRenderer, width: 100, dataIndex: 'active'}
			]
		}),
		selModel: adminPriceListGridCheckBoxSelectionModelObj,
		tbar: [ 
				{
					ref: '../adminPriceListEditButton',
					text: "{/literal}{#str_ButtonEdit#}{literal}",
					iconCls: 'silk-pencil',
					handler: onAdminEditPriceList,
					disabled: true
				},'-', 
				{
					ref: '../adminPriceListDeleteButton',
					text: "{/literal}{#str_ButtonDelete#}{literal}",
					iconCls: 'silk-delete',
					handler: onAdminDeletePriceList,
					disabled: true
				},'-',
				{ 
					id:'adminPriceListActiveButton',
					ref: '../adminPriceListActiveButton', 
					text: "{/literal}{#str_LabelMakeActive#}{literal}", 
					iconCls: 'silk-lightbulb',
					handler: activatePriceList,
					disabled: true
				}, '-',
				{ 
					id:'adminPriceListInactiveButton', 
					ref: '../adminPriceListInactiveButton', 
					text: "{/literal}{#str_LabelMakeInactive#}{literal}", 
					iconCls: 'silk-lightbulb-off',
					handler: activatePriceList,
					disabled: true	
				}
			],
		enableColLock:true,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		trackMouseOver:false,
		stripeRows:true,
		columnLines:true,
		autoExpandColumn:2
	});
		
	var priceListFormPanelObj = new Ext.FormPanel({
		id: 'saveAsPriceListForm',
        labelAlign: 'left',
        labelWidth:60,
        autoHeight: true,
        frame:true,
        layout:'form',
        cls: 'left-right-buttons',
        bodyStyle:'padding-left:5px;',
        items: [
                adminPriceListGrid,
                {/literal}{if $optionms}{literal}
            		companyCombo,
                {/literal}{/if}{literal}
            { 
              xtype: 'textfield', 
              id: 'pricelistcode', 
              name: 'pricelistcode', 
              allowBlank: false,
              maxLength: 50,
              style: {textTransform: "uppercase"},
              width:380,
              fieldLabel: "{/literal}{#str_LabelCode#}{literal}",
             listeners:{
  				blur:{
  					fn: forceAlphaNumeric
  				}
  			},
              post: true
  			},
  			{ 
                xtype: 'textfield', 
                id: 'pricelistname', 
                name: 'pricelistname', 
                allowBlank: false,
                maxLength: 50,
                width:380,
                fieldLabel: "{/literal}{#str_LabelName#}{literal}", 
                post: true
    		}
  			
        ]
    });
		
	
	/* create modal window for add and edit */
	gSaveAsPriceListWindowObj = new Ext.Window({
		  id: 'saveAsPriceListDialog',
		  closable:false,
		  closeAction: 'hide',
		  plain:true,
		  modal:true,
		  draggable:true,
		  resizable:false,
		  listeners: {
				'hide': function() { if (adminPriceListGlobalFlag) adminPriceListGlobalFlag = false; },
				'show': function(){ this.doLayout(false, true); }
		  },
		  layout: 'fit',
		  height: 'auto',
		  width: 500,
		  items: [],
		  buttons: 
			[
			{
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: closeSaveAsPriceListHandler
			},
			{
				text: "{/literal}{#str_ButtonSave#}{literal}",
				id: 'addPriceListButton',
				handler: saveAsPriceList
			}
		]
	});

	var mainPanel = Ext.getCmp('saveAsPriceListDialog');
	mainPanel.setTitle('{/literal}{#str_TitleSavePriceAsPriceList#}{literal}');
	mainPanel.add(priceListFormPanelObj);
	gSaveAsPriceListWindowObj.show();
};	

function forceAlphaNumeric()
{
	var code = Ext.getCmp('pricelistcode').getValue();

	code = code.toUpperCase();
	code = code.replace(/[^A-Z_0-9\-]+/g, "");

	Ext.getCmp('pricelistcode').setValue(code);
}

function saveAsPriceList(btn, ev)
{
	var submitURL = 'index.php?fsaction=Admin.priceListAdd&ref={/literal}{$ref}{literal}';
	var fp = Ext.getCmp('saveAsPriceListForm'), form = fp.getForm();
	var submit = true;
	var paramArray = new Object();
	
	if (!Ext.getCmp('componentpricinggrid'))
	{
		var gridObj = Ext.getCmp('productgrid');
		var pricingModel = 3;
		
		if (Ext.getCmp('productConfigPricingGridWindow'))
		{
			category = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.categorycode;
			pricingModel = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.pricingmodel;
		}
		else
		{
			category = 'PRODUCT';
		}
		
		if (category == 'PRODUCT')
		{
			if (Ext.getCmp('fixedquantityrange').checked)
			{
				paramArray['quantitytypeisdropdown'] = '1';
			}
			else
			{
				paramArray['quantitytypeisdropdown'] = '0';
			}
		}
		else
		{
			paramArray['quantitytypeisdropdown'] = '0';
		}
	}
	else
	{
		var gridObj = Ext.getCmp('maingrid');
		var selRecords = gridObj.selModel.getSelections();
		var pricingModel = selRecords[0].data.pricingmodel;
		var category = selRecords[0].data.code;
		
		if ((pricingModel == 7) || (pricingModel == 8))
		{
			if (Ext.getCmp('fixedquantityrange').checked)
			{
				paramArray['quantitytypeisdropdown'] = '1';
			}
			else
			{
				paramArray['quantitytypeisdropdown'] = '0';
			}
		}
		else
		{
			paramArray['quantitytypeisdropdown'] = '0';
		}
	}

	
	var pricingGrid = Ext.getCmp('price');
	var price = pricingGrid.convertTableToString();
	var taxCode = Ext.getCmp('taxcode').getValue();

	
	paramArray['taxcode'] = taxCode;
	paramArray['price'] = price;
	paramArray['pricingmodel'] = pricingModel;
	paramArray['categorycode'] = category;
		
	if (submit)
	{
		Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", savePriceListCallback);
	}
}

function savePriceListCallback(pUpdated, pActionForm, pActionData)
{	
	if (pUpdated)
	{	
		var priceListCombo = Ext.getCmp('pricelistid');
		priceListCombo.store.reload();
		
		priceListCombo.store.on({'load': function() { 
			priceListCombo.setValue(pActionData.result.data.id);
		
		Ext.getCmp('price').reload(pActionData.result.data.price);
	    Ext.getCmp('price').disable();
			
		} 
		});
		
		gSaveAsPriceListWindowObj.close();
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

function createAdminPriceListEditWindow()
{
	
	if (Ext.getCmp('productConfigPricingGridWindow'))
	{
		pricingModel = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.pricingmodel;
	}
	else
	{
		if (!Ext.getCmp('componentpricinggrid'))
		{
			var gridObj = gMainWindowObj.findById('productgrid');
			var pricingModel = 3;
		}
		else
		{
			var gridObj = Ext.getCmp('maingrid');
			var selRecords = gridObj.selModel.getSelections();
			var pricingModel = selRecords[0].data.pricingmodel;
		}
	}
	
	if ((pricingModel == 7) || (pricingModel == 8))
	{
		windowWidth = 835;
	}
	else if (pricingModel == 3)
	{
		windowWidth = 500;	
	}
	else
	{
		windowWidth = 665;
	}
	
	gAdminPriceListAddDialogObj = new Ext.Window({
		  id: 'adminpricelistadddialog',
		  closable:false,
		  closeAction: 'hide',
		  plain:true,
		  modal:true,
		  autoHeight:true,
		  draggable:true,
		  resizable:false,
		  listeners: {
				'hide': function() { if (adminPriceListEditScreen) adminPriceListEditScreen = false; },
				'show': function(){ this.doLayout(false, true); }
		  },
		  layout: 'fit',
		  height: 'auto',
		  width: windowWidth,
		  items: [],
		  cls: 'left-right-buttons',
		  buttons: 
			[new Ext.form.Checkbox({
				id: 'isadminpricelistactive',
				name: 'isadminpricelistactive',
				boxLabel: "{/literal}{#str_LabelActive#}{literal}",
				post: true,
				cls: 'x-btn-left', 
				ctCls: 'width_100'
			}),
			{
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: closeAdminPriceListAdd,
				cls: 'x-btn-right'
			},
			{
				text: "{/literal}{#str_ButtonUpdate#}{literal}",
				id: 'adminPriceListAddEditButton',
				handler: adminPriceListEditSaveHandler,
				cls: 'x-btn-right'
			}
		]
	});
	
	var editPriceListPanel = Ext.getCmp('adminpricelistadddialog');
	editPriceListPanel.doLayout();

}

function onAdminEditPriceList(btn, ev)
{			
	createAdminPriceListEditWindow();
	
	var serverParams = new Object();
	var priceListID = Ext.taopix.gridSelection2IDList(gSaveAsPriceListWindowObj.findById('adminPriceListGrid'));
	var gridObj = Ext.getCmp('adminPriceListGrid');
	var selRecords = gridObj.selModel.getSelections();
	var decimalPlaces = selRecords[0].data.decimalplaces;

	serverParams['pricelistid'] = priceListID;
	serverParams['decimalplaces'] = decimalPlaces;
	
	if (adminPriceListEditScreen == false)
	{
		adminPriceListEditScreen = true;
		Ext.taopix.loadJavascript(gSaveAsPriceListWindowObj, '', 'index.php?fsaction=Admin.priceListEditDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initializePriceListEdit', false);
	}
}

function adminPriceListEditSaveHandler(btn, ev)
{
	var priceListID = Ext.taopix.gridSelection2IDList(gSaveAsPriceListWindowObj.findById('adminPriceListGrid'));
		
	var submitURL = 'index.php?fsaction=Admin.priceListEdit&ref={/literal}{$ref}{literal}&id=' + priceListID;
	var fp = Ext.getCmp('adminpricelistaddform'), form = fp.getForm();
	var submit = true;
	
	var paramArray = new Object();
	paramArray['isactive'] = '';
		
	if (Ext.getCmp('isadminpricelistactive').checked)
	{
		paramArray['isactive'] = '1';
	}
	else
	{
		paramArray['isactive'] = '0';
	}
	
	if (Ext.getCmp('productConfigPricingGridWindow'))
	{
		category = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.categorycode;
		pricingModel = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.pricingmodel;
		
		paramArray['pricingmodel'] = pricingModel;
		paramArray['taxcodepricelist'] = Ext.getCmp('taxcodepricelist').getValue();
			
		if ((pricingModel == 7) || (pricingModel == 8))
		{
			if (Ext.getCmp('fixedquantityranges').checked)
			{
				paramArray['quantitytypeisdropdown'] = '1';
			}
			else
			{
				paramArray['quantitytypeisdropdown'] = '0';
			}
		}
		else
		{
			paramArray['quantitytypeisdropdown'] = '0';
		}
	}
	else
	{
		category = 'PRODUCT';
		
		if (!Ext.getCmp('componentpricinggrid'))
		{
			
			paramArray['pricingmodel'] = 3;
			
			if (Ext.getCmp('fixedquantityranges').checked)
			{
				paramArray['quantitytypeisdropdown'] = '1';
			}
			else
			{
				paramArray['quantitytypeisdropdown'] = '0';
			}
		}
		else
		{
			var gridObj = Ext.getCmp('maingrid');
			var selRecords = gridObj.selModel.getSelections();
			var pricingModel = selRecords[0].data.pricingmodel;
			
			paramArray['pricingmodel'] = pricingModel;
			
			if ((pricingModel == 7) || (pricingModel == 8))
			{
				if (Ext.getCmp('fixedquantityranges').checked)
				{
					paramArray['quantitytypeisdropdown'] = '1';
				}
				else
				{
					paramArray['quantitytypeisdropdown'] = '0';
				}
			}
			else
			{
				paramArray['quantitytypeisdropdown'] = '0';
			}
		}
	}
	
	if (submit)
	{
		Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", adminPriceListSaveCallback);
	}	
}

function adminPriceListSaveCallback(pUpdated, pActionForm, pActionData)
{	
	if (pUpdated)
	{
		var gridObj = Ext.getCmp('adminPriceListGrid');
		var dataStore = gridObj.store;	
		
		gridObj.store.reload();
		gAdminPriceListAddDialogObj.close();
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

/* delete handler */	  
function onAdminDeletePriceList(btn, ev)
{
	var gridObj = Ext.getCmp('adminPriceListGrid');
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
	
	var message = "{/literal}{#str_DeleteConfirmation#}{literal}";
	message = message.replace("^0", codeList);
		
	dataStore.load();
	Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", message, onAdminDeletePriceListResult);
}

function onAdminDeletePriceListResult(btn)
{
	if (btn == "yes")
	{
		var paramArray = new Object();
				
		var gridObj = Ext.getCmp('adminPriceListGrid');
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
		
		paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gSaveAsPriceListWindowObj.findById('adminPriceListGrid'));
		paramArray['codelist'] = codeList;
		
		Ext.taopix.formPost(Ext.getCmp('saveAsPriceListDialog'), paramArray, 'index.php?fsaction=Admin.adminPriceListDelete&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageDeleting#}{literal}", onAdminDeletePriceListCallback);	
	}
}

function onAdminDeletePriceListCallback(pUpdated, pTheForm, pActionData)
{
	if (pUpdated == true)
	{
		var gridObj = Ext.getCmp('adminPriceListGrid');
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

function activatePriceList(btn, ev)
{ 
	
	/* server parameters are sent to the server */
	var serverParams = new Object();
	serverParams['ids'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('adminPriceListGrid'));	
	var active = 0; 
	
	switch (btn.id)
	{
	case 'adminPriceListActiveButton':
		active = 1;
		break;
	case 'adminPriceListInActiveButton':
		active = 0;
		break;
	}

	serverParams['active'] = active;

	Ext.taopix.formPost(Ext.getCmp('saveAsPriceListDialog'), serverParams, 'index.php?fsaction=Admin.priceListActivate&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageUpdating#}{literal}", priceListActivateCallback);	
}

function priceListActivateCallback(pUpdated, pActionForm, pActionData)
{
	if (pUpdated)
	{
		var componentGridObj = Ext.getCmp('adminPriceListGrid');
		var dataStore = componentGridObj.store;
	
		dataStore.reload();
	}
}


/* close functions */
function closeSaveAsPriceListHandler(btn, ev)
{
	gSaveAsPriceListWindowObj.close();
	adminPriceListGlobalFlag = false;
}

function closeAdminPriceListAdd(btn, ev)
{
	gAdminPriceListAddDialogObj.close();
	adminPriceListEditScreen = false;
}

{/literal}
