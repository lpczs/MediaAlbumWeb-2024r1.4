{literal}

var productIDList = "";
var notAllowedProductsCodesArray = [];

function initialize(pParams)
{
	Ext.QuickTips.init();
	Ext.form.Field.prototype.msgTarget = 'side';

	productIDList = Ext.taopix.gridSelection2List(gMainWindowObj.findById('productgrid'), 'productid', '');
	var btnAssignText;
	var btnAssignDisabled = true;
	var selectedProductsLength = (productIDList != '') ? productIDList.split(',').length : 0;

	if (selectedProductsLength > 0)
	{
		btnAssignText = "{/literal}{#str_ButtonAssignToProducts#}{literal}";
	}
	else
	{
		btnAssignText = "{/literal}{#str_ButtonAssignToProduct#}{literal}";
	}

	var selRecords = (gMainWindowObj.findById('productgrid')).getSelectionModel().getSelections();
	var codeList = [];
	var needWarning = false;
	var totalCount = 0;

	for (var rec = 0; rec < selRecords.length; rec++)
	{
		if (selRecords[rec].data.productcount > 1)
		{
			needWarning = true;
		}
		totalCount = totalCount + parseInt(selRecords[rec].data.productcount);
	}
	
	var warningArray = [];
	var warningHeight = 0;

	//if multiple products use any of the selected layout codes then we need to warn that all will be updated
	if (needWarning)
	{
		warningHeight += 27;

		warningArray.push({
			xtype: 'panel',
			style: { backgroundColor: "#fdfcd2" },
			ctCls: "warning-bar",
			height: warningHeight,
			tpl: new Ext.XTemplate('<tpl for="."><div class="warning-message">{error}</div></tpl>'),
			data: [
				{error: "{/literal}{#str_LabelUpdateWarning3d#}{literal}".replace('^0', codeList.join(","))}
			]
		});
	}

	var gWarningPanel = new Ext.Panel({
		id: 'warningpanel',
		height: warningHeight,
		items: warningArray
	});

	modelPreviewEditWindowExists = false;

	var modelsComboBox = new Ext.form.ComboBox(
	{
		id: 'modelslist',
		name: 'modelcode',
		mode: 'local',
		anchor: '100%',
		editable: false,
		forceSelection: true,
		selectOnFocus: true,
		allowBlank: false,
		triggerAction: 'all',
		fieldLabel: "",
		hideLabel: true,
		store: new Ext.data.ArrayStore(
		{
			id: 'modelsstore',
			fields: ['modelid', 'modelcode', 'modelname', 'modelcodemodelname'],
			data: [
					["", "", "", "{/literal}{#str_labelSelectA3DPreview#}{literal}"],
				{/literal}
				{section name=index loop=$modellist}
				{if $smarty.section.index.last}
					["{$modellist[index].modelid}", "{$modellist[index].modelcode}", "{$modellist[index].modelname}", "{$modellist[index].modelcode} - {$modellist[index].modelname}"]
				{else}
					["{$modellist[index].modelid}", "{$modellist[index].modelcode}", "{$modellist[index].modelname}", "{$modellist[index].modelcode} - {$modellist[index].modelname}"],
				{/if}
				{/section}
				{literal}
			]
		}),
		valueField: 'modelcode',
		displayField: 'modelcodemodelname',
		value: '',
		useID: true,
		allowBlank: false,
		post: true
	});

	var modelFormPanelObj = new Ext.FormPanel(
	{
		id: 'modelformpanel',
        labelAlign: 'left',
        labelWidth: 1,
        height: 400,
        frame: true,
        bodyStyle: 'padding: 10px;',
        items: 
		[
			modelsComboBox
		],
		post: true
    });

	gDialogObj = new Ext.Window(
	{
		id: 'threeDPreviewAssignToProductDialog',
	  	closable: false,
	  	title: "{/literal}{#str_Title3DPreviewAssignToProducts#}{literal}",
	  	plain: true,
	  	modal: true,
	  	draggable: true,
	 	resizable: false,
	  	height: 150 + warningHeight,
	  	width: 550,
	  	items: [gWarningPanel,modelFormPanelObj],
	  	tools:[
		{
			id: 'clse',
		    qtip: "{/literal}{#str_LabelCloseWindow#}{literal}",
		    handler: function() 
			{
				Ext.getCmp('threeDPreviewAssignToProductDialog').close();
				product3DPreviewWindowExists = false;
			}
		}],
		buttons:
		[
			{
				text: "{/literal}{#str_Label3DPreviewAssignToSelectedProduct#}{literal}",
				handler: onAssign
			}
		]
	});

	Ext.getCmp('threeDPreviewAssignToProductDialog').show();
}

/* assign handler */
function onAssign(btn, ev)
{
	if (Ext.getCmp('modelslist').getValue() != "")
	{
		var paramArray = new Object();

		var productStore = Ext.getCmp('productgrid').store;
		var productIDList = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('productgrid'));
		var productIDArray = productIDList.split(',');
		var productIDArrayLength = (productIDList != "") ? productIDArray.length : 0;
		var productCodesArray = [];
		var productHas3DModelLink = false;
		var productInMultipleCollections = false;

		for (i = 0; i < productIDArrayLength; i++)
		{
			var record = productStore.getById(productIDArray[i]);
			if (record.data.collectiontype == 0)
			{
				productCodesArray.push(record.data.code);
			}
			else
			{
				notAllowedProductsCodesArray.push(record.data.code);
			}

			if (record.data.resourcecode != '')
			{
				productHas3DModelLink = true;
			}
		}

		paramArray['productcodes'] = productCodesArray;

		if (productHas3DModelLink)
		{
			Ext.MessageBox.show(
			{
				title: "{/literal}{#str_TitleThisWillReplaceCurrentSelections#}{literal}",
				msg: "{/literal}{#str_MessageProductsAlreadyHave3DPreviewsAssigned#}{literal}",
				buttons: Ext.MessageBox.OKCANCEL,
				fn: function(pBtn)
				{
					if (pBtn == 'ok')
					{
						apply3DModelToProduct(paramArray);
					}
					else if (pBtn == 'cancel')
					{
						Ext.getCmp('threeDPreviewAssignToProductDialog').close();
					}
				},
				icon: Ext.MessageBox.QUESTION
			});
		}
		else if ((productCodesArray.length > 0) && (notAllowedProductsCodesArray.length >= 0))
		{
			apply3DModelToProduct(paramArray);
		}
		else if ((productCodesArray.length == 0) && (notAllowedProductsCodesArray.length > 0))
		{
			// if any non-photobook products have been selected, call the assigncallback to display the error message and skip calling the server
			// if no valid products have been selected
			assignCallback(true);
		}
	}
	else
	{
		Ext.MessageBox.show(
		{
			title: "{/literal}{#str_labelSelectA3DPreview#}{literal}",
			msg: "{/literal}{#str_labelSelectA3DPreview#}{literal}",
			buttons: Ext.MessageBox.OK,
			icon: Ext.MessageBox.WARNING
		});
	}

	return false;
}

function apply3DModelToProduct(pParamArray)
{
	var fp = Ext.getCmp('modelformpanel');
	var form = fp.getForm();

	var submitURL = 'index.php?fsaction=Admin3DPreview.link3DPreviewModelToProducts&ref={/literal}{$ref}{literal}';

	Ext.taopix.formPanelPost(fp, form, pParamArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", assignCallback);
}

function assignCallback(pSuccess, pActionForm, pActionData)
{
	if (pSuccess)
	{
		var title = '';
		var message = '';

		if (notAllowedProductsCodesArray.length > 0)
		{
			title = "{/literal}{#str_Title3DPreviewCanOnlyBeAssignedToPhotobooks#}{literal}";
			msg = "{/literal}{#str_Message3DPreviewsCanOnlyBeAssignedToPhotobooks#}{literal}";
		}
		else
		{
			title = "{/literal}{#str_Title3DSuccessfullyApplied#}{literal}";
			msg = "{/literal}{#str_Message3DPreviewHasBeenSuccessfullyApplied#}{literal}";
		}

		Ext.MessageBox.show(
		{
			title: title,
			msg: msg,
			buttons: Ext.MessageBox.OK,
			icon: Ext.MessageBox.INFO
		});

		var gridObj = gMainWindowObj.findById('productgrid');
		var dataStore = gridObj.store;
		dataStore.reload();

		gMainWindowObj.findById('productgrid').getSelectionModel().clearSelections();

		Ext.getCmp('threeDPreviewAssignToProductDialog').close();
	}
}
{/literal}
