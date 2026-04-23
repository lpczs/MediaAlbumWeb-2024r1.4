<?php
/* Smarty version 4.5.3, created on 2026-03-12 08:25:50
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\productpricing\productspricelistgrid.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b2788e98a1e4_19232371',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd8bc831733dc5bb4f89be7b830931988f746992b' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\productpricing\\productspricelistgrid.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b2788e98a1e4_19232371 (Smarty_Internal_Template $_smarty_tpl) {
?>
function initialize(pParams)
{
	var category = 'PRODUCT';
	productPriceListEditWindowExists = false;

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

	function onAddPriceList(btn, ev)
	{
		var serverParams = new Object();
		serverParams['pricingmodel'] = 3;

		if (!productPriceListEditWindowExists)
		{
			productPriceListEditWindowExists = true;
			Ext.taopix.loadJavascript(gComponentsPriceListDialogObj, '', 'index.php?fsaction=AdminProductPricing.priceListAddDisplay&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
		}
	}

	function onEditPriceList(btn, ev)
	{
		var serverParams = new Object();
		var priceListID = Ext.taopix.gridSelection2IDList(Ext.getCmp('pricelistgrid'));
		serverParams['pricelistid'] = priceListID;


		if (!productPriceListEditWindowExists)
		{
			productPriceListEditWindowExists = true;
			Ext.taopix.loadJavascript(gComponentsPriceListDialogObj, '', 'index.php?fsaction=AdminProductPricing.priceListEditDisplay&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
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

				Ext.taopix.formPost(Ext.getCmp('componentPriceListDialog'), paramArray, 'index.php?fsaction=AdminProductPricing.priceListDelete&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleting');?>
", onDeletePriceListCallback);
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

		var message = nlToBr("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DeleteProductPriceListConfirmation');?>
".replace("^0", codeList));
		dataStore.load();
		Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", message, onDeletePriceListResult);
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
		Ext.taopix.formPost(Ext.getCmp('componentPriceListDialog'), serverParams, 'index.php?fsaction=AdminProductPricing.activatePriceList&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageUpdating');?>
", priceListActivateCallback);
	}

	function clearGrouping(v)
	{
		if(v.checked)
		{
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				gridPriceListDataStoreObj.groupBy('companycodehidden');
			<?php } else { ?>
				gridPriceListDataStoreObj.groupBy('active');
			<?php }?>
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

						<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
							<?php if ($_smarty_tpl->tpl_vars['companyLogin']->value) {?>
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
							<?php }?>
						<?php }?>
					}
				}
			}
		}
	});

	var gridPriceListDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			groupField: 'companycodehidden',
		<?php }?>
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminProductPricing.getPriceListGridData&category='+category+'&pricingmodel=<?php echo $_smarty_tpl->tpl_vars['pricingModel']->value;?>
&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
		    <?php if ($_smarty_tpl->tpl_vars['pricingModel']->value == 3) {?>
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
			<?php } elseif ($_smarty_tpl->tpl_vars['pricingModel']->value == 5) {?>
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
			<?php }?>
			])
		),
		sortInfo:{field: 'code', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()},
	});

	var priceListGridColumnModel = new Ext.grid.ColumnModel({
		defaults: { sortable: false, resizable: true },
		columns: [
			priceListGridCheckBoxSelectionModelObj,
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", width: 150, renderer: companyRenderer, dataIndex: 'companycode'},
			<?php }?>
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCode');?>
", width: 150, dataIndex: 'code', renderer: generalColumnRenderer},
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
", width: 150, dataIndex: 'name', renderer: generalColumnRenderer},
			<?php if ($_smarty_tpl->tpl_vars['pricingModel']->value == 3) {?>
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeStart');?>
", width: 105, dataIndex: 'qrs', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeEnd');?>
", width: 105, dataIndex: 'qre', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeBasePrice');?>
", width: 95, dataIndex: 'bp', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeUnitPrice');?>
", width: 95, dataIndex: 'up', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLineSubtract');?>
", width: 95, dataIndex: 'ls', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelIncludesTax');?>
", width: 180, dataIndex: 'it', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
", renderer: statusRenderer, width: 45, dataIndex: 'active'}
			<?php } elseif ($_smarty_tpl->tpl_vars['pricingModel']->value == 5) {?>
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeStart');?>
", width: 90, dataIndex: 'qrs', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeEnd');?>
", width: 90, dataIndex: 'qre', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SidePriceRangeStart');?>
", width: 90, dataIndex: 'srs', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SidePriceRangeEnd');?>
", width: 90, dataIndex: 'sre', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeBasePrice');?>
", width: 80, dataIndex: 'bp', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeUnitPrice');?>
", width: 80, dataIndex: 'up', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLineSubtract');?>
", width: 80, dataIndex: 'ls', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelIncludesTax');?>
", width: 180, dataIndex: 'it', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
", renderer: statusRenderer, width: 45, dataIndex: 'active'}
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				,{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", width: 170, dataIndex: 'companycodehidden', hidden:true, renderer: companyCodeRenderer}
			<?php }?>
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
		<?php if ($_smarty_tpl->tpl_vars['pricingModel']->value == 3) {?>
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				autoExpandColumn: 10,
			<?php } else { ?>
				autoExpandColumn: 9,
			<?php }?>
		<?php } elseif ($_smarty_tpl->tpl_vars['pricingModel']->value == 5) {?>
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				autoExpandColumn: 12,
			<?php } else { ?>
				autoExpandColumn: 10,
			<?php }?>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleComponents');?>
" : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelComponent');?>
"]})' }),
		<?php }?>
		height:400,
		selModel: priceListGridCheckBoxSelectionModelObj,
		tbar:
		[
			{
				id:'priceListAddButton',
				ref: '../priceListAddButton',
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
",
				iconCls: 'silk-add',
				handler: onAddPriceList,
				enabled: true
			}, '-',
			{
				ref: '../priceListEditButton',
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonEdit');?>
",
				iconCls: 'silk-pencil',
				handler: onEditPriceList,
				disabled: true
			},'-',
			{
				ref: '../priceListdeleteButton',
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDelete');?>
",
				iconCls: 'silk-delete',
				handler: onDeletePriceList,
				disabled: true
			},'-',
			{
				id:'priceListActiveButton',
				ref: '../priceListActiveButton',
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeActive');?>
",
				iconCls: 'silk-lightbulb',
				handler: activatePriceList,
				disabled: true
			}, '-',
			{
				id:'priceListInactiveButton',
				ref: '../priceListInactiveButton',
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeInactive');?>
",
				iconCls: 'silk-lightbulb-off',
				handler: activatePriceList,
				disabled: true
			}
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			,'-',
			new Ext.form.Checkbox({boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnableGrouping');?>
", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }})
			<?php }?>
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
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPriceLists');?>
",
		closable:false,
		closeAction: 'close',
		tools:
		[
		  	{
		   		id:'clse',
		   	 	qtip: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCloseWIndow');?>
',
		    	handler: function(){ Ext.getCmp('componentPriceListDialog').close(); productPriceListGridWindowExists = false;}
			}
		],
	  	plain:true,
	  	modal:true,
	  	autoHeight:true,
	  	draggable:true,
	  	resizable:false,
	  	layout: 'fit',
	  	height: 'auto',
	  	 <?php if ($_smarty_tpl->tpl_vars['pricingModel']->value == 3) {?>
			width: 1200,
        <?php } else { ?>
			width: 1200,
		<?php }?>
	  	items: priceListDialogFormPanelObj,
	  	cls: 'left-right-buttons'
	});
	gComponentsPriceListDialogObj.show();
}
<?php }
}
