<?php
/* Smarty version 4.5.3, created on 2026-03-07 03:43:15
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\productpricing\productpricing.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69ab9ed3886156_18425455',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9b23b7db9da98548726a7afa8639df9632284cb2' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\productpricing\\productpricing.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69ab9ed3886156_18425455 (Smarty_Internal_Template $_smarty_tpl) {
?>

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
				{error: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUpdateWarningPricing');?>
".replace('^0', '<?php echo $_smarty_tpl->tpl_vars['layoutcode']->value;?>
').replace('^1', productCount)}
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

						<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
							<?php if ($_smarty_tpl->tpl_vars['companyLogin']->value) {?>
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
							<?php }?>
						<?php }?>
					}
				}
			}
		}
	});

	var pricingGridDataStoreObj = new Ext.data.GroupingStore({
		id:'pricingdatastore',
		remoteSort: true,
		<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			groupField: 'companycodeHidden',
		<?php }?>
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminProductPricing.getGridData&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&id=<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
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
			<?php } else { ?>
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
			<?php }?>
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
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
', width: 140, renderer: companyRenderer, dataIndex: 'company'},
			<?php }?>
			{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKey');?>
', width: 160, renderer: generalColumnRenderer, dataIndex: 'lkey'},
			{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelClientPriceDescription');?>
', renderer: generalColumnRenderer, width: 220, dataIndex: 'pricedesc'},
			{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeStart');?>
', width: 80, renderer: generalColumnRenderer, dataIndex: 'qtyrangestart'},
			{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeEnd');?>
', width: 80, renderer: generalColumnRenderer, dataIndex: 'qtyrangeend'},
			{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeBasePrice');?>
', width: 80, renderer: generalColumnRenderer, dataIndex: 'baseprice'},
			{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeUnitPrice');?>
', width: 80, renderer: generalColumnRenderer, dataIndex: 'unitprice'},
			{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLineSubtract');?>
', width: 100, renderer: generalColumnRenderer, dataIndex: 'linesubtract'},
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelIncludesTax');?>
", width: 110, dataIndex: 'it', renderer: generalColumnRenderer},
			{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
', renderer: statusRenderer, width: 50, dataIndex: 'active'},
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", width: 200, dataIndex: 'companycodeHidden', hidden: true, renderer: companyCodeRenderer}
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
		<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPrices');?>
" : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPrice');?>
"]})' }),
			autoExpandColumn: 10,
		<?php } else { ?>
		autoExpandColumn:9,
		<?php }?>
		selModel: pricingGridCheckBoxSelectionModelObj,
		tbar: [
			{
				text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
',
				iconCls: 'silk-add',
				handler: onAdd
			}, '-',
			{
				ref: '../editButton',
				text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonEdit');?>
',
				iconCls: 'silk-pencil',
				handler: onEditPricing,
				disabled: true
			}
			,'-', {
				ref: '../deleteButton',
				text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDelete');?>
',
				iconCls: 'silk-delete',
				handler: onDeletePricing,
				disabled: true
			}, '-',
			{
				id:'activeButton',
				ref: '../activeButton',
				text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeActive');?>
',
				iconCls: 'silk-lightbulb',
				handler: onPricingActivate,
				disabled: true
			}, '-',
			{
				id:'inactiveButton',
				ref: '../inactiveButton',
				text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeInactive');?>
',
				iconCls: 'silk-lightbulb-off',
				handler: onPricingActivate,
				disabled: true
			}
				<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				,'-',
					new Ext.form.Checkbox({boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnableGrouping');?>
", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }})
				<?php }?>
			]
	});

	pricingGridDataStoreObj.load();

	function clearGrouping(v){
		if(v.checked)
		{
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				pricingGridDataStoreObj.groupBy('companycodeHidden');
			<?php } else { ?>
				pricingGridDataStoreObj.groupBy('active');
			<?php }?>
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
	  	title: "<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
",
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
		    qtip: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCloseWIndow');?>
',
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
		return '<span '+className+"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInactive');?>
</span>";
	}
	else
	{
		return "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
";
	}
}

function generalColumnRenderer(value, p, record)
{
	if (record.data.active == 0)
	{
		className =  'class = "inactive"';
		return '<span '+className+'>'+value+'</span>';
	}
	else
	{
		return '<span class="">'+value+'</span>';
	}
}

function companyRenderer(value, p, record)
{
	if (value == '')
	{
		value = "<i><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_Global');?>
</i>";
	}

	if (record.data.active == 0)
	{
		className =  'class = "inactive"';
		return '<span '+className+'>'+value+'</span>';
	}
	else
	{
		return '<span class="">'+value+'</span>';
	}
}

function companyCodeRenderer(value, p, record)
{
	<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
	if (value == '')
	{
		return "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_Global');?>
";
	}
	else
	{
		return value;
	}
	<?php } else { ?>
		return value;
	<?php }?>
}

/* add handler */
function onAdd(btn, ev)
{
	if(!productPricingEditWindowExists)
	{
		productPricingEditWindowExists = true;
		Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProductPricing.addDisplay&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&productid=<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
', '', '', 'initialize', false);
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
		Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProductPricing.editDisplay&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&productid=<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
', serverParams, '', 'initialize', false);
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

	var message = nlToBr("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DeletePricingConfirmation');?>
");
	message = message.replace("^0", codeList);

	dataStore.load();
	Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", message, onDeletePricingResult);
}

function onDeletePricingResult(btn)
{
	if (btn == "yes")
	{
		var paramArray = new Object();

		paramArray['idlist'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('pricingGrid'));
		paramArray['lkeylist'] = Ext.taopix.gridSelection2LiceseKeyList(Ext.getCmp('pricingGrid'));

		Ext.taopix.formPost(Ext.getCmp('pricingWindow'), paramArray, 'index.php?fsaction=AdminProductPricing.delete&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleting');?>
", onDeletePricingCallback);
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

	Ext.taopix.formPost(Ext.getCmp('pricingWindow'), serverParams, 'index.php?fsaction=AdminProductPricing.pricingActivate&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageUpdating');?>
', pricingActivateCallback);
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
<?php }
}
