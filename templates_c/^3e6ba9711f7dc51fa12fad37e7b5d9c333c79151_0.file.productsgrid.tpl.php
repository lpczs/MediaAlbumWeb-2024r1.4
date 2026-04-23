<?php
/* Smarty version 4.5.3, created on 2026-03-06 03:48:04
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\products\productsgrid.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa4e74425231_63802192',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3e6ba9711f7dc51fa12fad37e7b5d9c333c79151' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\products\\productsgrid.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aa4e74425231_63802192 (Smarty_Internal_Template $_smarty_tpl) {
?>

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
			
			if (record.data.hasprice == 0)
			{
				return '<span '+className+'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInactive');?>
</span>';
			}
			else
			{
				return '<img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/money_dollar.png" /><span '+className+'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInactive');?>
</span>';
			}
			
		}
		else
		{
			if (record.data.hasprice == 0)
			{
				return '<span class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
</span>';
			}
			else
			{
				return '<img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/money_dollar.png" /><span class=""><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
</span>';
			}
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
	
	function clearGrouping(v)
	{ 
		if(v.checked)
		{
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				gridDataStoreObj.groupBy('companycodeHidden');
			<?php } else { ?>
				gridDataStoreObj.groupBy('active');
			<?php }?>
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
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProducts.editDisplay&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
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
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProducts.productConfigDisplay&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
		}
	}

	function onLinkingPreview(btn, ev)
	{
		var serverParams = new Object();

		if (! productLinkingPreviewWindowExists)
		{
			productLinkingPreviewWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProducts.linkingPreviewDisplay.&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
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
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=Admin3DPreview.modelListGrid&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
		}
	}

	function onAssign3DPreviewToProduct()
	{
		var serverParams = new Object();
		Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=Admin3DPreview.get3DModelList&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
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
			title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleRemove3DPreviewFromSelectedProducts');?>
",
			msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageConfirmRemove3DPreviewFromProducts');?>
",
			buttons: {
				ok: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRemove3DPreviewsFromSelectedProducts');?>
',
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

		var submitURL = 'index.php?fsaction=Admin3DPreview.unLink3DPreviewModelToProducts&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
';

		Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
", unAssignCallback);

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
				title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_Title3DPreviewSuccessullyRemoved');?>
",
				msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageAny3DPreviewsHaveBeenRemoved');?>
",
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
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProductPricing.initialize&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
		}
	}
	
	function onProductPriceLists(btn, ev)
	{		
		var serverParams = new Object();
		serverParams['pricingmodel'] = 3;
		
		if (!productPriceListGridWindowExists)
		{
			productPriceListGridWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProductPricing.priceListsInitialize&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
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

		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminProducts.checkProductDeletionWarnings', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageUpdating');?>
", onCheckDeleteCallback);
	}

	function onCheckDeleteCallback(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated == true)
		{
			if (pActionData.result.msg)
			{
				Ext.MessageBox.confirm('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
', nlToBr(pActionData.result.msg), onDeleteResult);
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
			
			Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminProducts.delete', '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleting');?>
', onDeleteCallback);	
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

		Ext.taopix.formPost(gMainWindowObj, serverParams, 'index.php?fsaction=AdminProducts.productActivate', '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageUpdating');?>
', activateCallback);
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
			Ext.getCmp('hideInactiveButton').setTooltip('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipShowInactiveItems');?>
');
		}
		else
		{
			Ext.getCmp('hideInactiveButton').setTooltip('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipHideInactiveItems');?>
');			
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
			hideInactiveButton.setTooltip({text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipHideInactiveItemsIsDisabledForSearchResults');?>
', autoHide: true, id: 'hideInactiveDisabledTooltip'});
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
					hideInactiveButton.setTooltip('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipShowInactiveItems');?>
');					
				}
				else
				{
					hideInactiveButton.toggle(false);
					hideInactiveButton.setTooltip('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipHideInactiveItems');?>
');					
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
						<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
								<?php if ($_smarty_tpl->tpl_vars['companyLogin']->value) {?>
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
								<?php } else { ?>
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
							<?php }?>
						<?php } else { ?>
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
					 <?php }?>
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
		<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			groupField: 'companycodeHidden',
		<?php }?> 
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminProducts.getGridData&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>		    
				{name: 'id', mapping: 0},
				{name: 'companycode', mapping: 1},
			    {name: 'category', mapping: 2},
				{name: 'code', mapping: 3},
				{name: 'name', mapping: 4},
				{name: 'productcollection', mapping: 5},
				{name: 'active', mapping: 6},
				{name: 'companycodeHidden', mapping: 7},
				{name: 'hasprice', mapping: 8},
				<?php if ($_smarty_tpl->tpl_vars['threedpreviewavailable']->value) {?>
				{name: 'collectiontype', mapping: 9},
				{name: 'resourcecode', mapping: 10}
			<?php }
} else { ?>
				{name: 'id', mapping: 0},
			    {name: 'category', mapping: 1},
				{name: 'code', mapping: 2},
				{name: 'name', mapping: 3},
				{name: 'productcollection', mapping: 4},
				{name: 'active', mapping: 5},
				{name: 'hasprice', mapping: 6},
				<?php if ($_smarty_tpl->tpl_vars['threedpreviewavailable']->value) {?>
				{name: 'collectiontype', mapping: 7},
				{name: 'resourcecode', mapping: 8}
				<?php }?>
				<?php }?>
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
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
', width: 100, dataIndex: 'companycode', renderer: companyRenderer},
				{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCode');?>
', width: 200, dataIndex: 'code', renderer: generalColumnRenderer},
				{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
', width: 250, sortable: false, dataIndex: 'name', renderer: generalColumnRenderer},
				{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_AutoUpdateTitleProductCollections');?>
', width: 220, sortable: false, dataIndex: 'productcollection',renderer: generalColumnRenderer},
				<?php if ($_smarty_tpl->tpl_vars['threedpreviewavailable']->value) {?>
				{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_Label3DPreview');?>
', renderer: generalColumnRenderer, width: 100, dataIndex: 'resourcecode'},
				<?php }?>
				{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
', renderer: statusRenderer, width: 250, dataIndex: 'active'},				
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", width: 200, dataIndex: 'companycodeHidden', hidden: true, renderer: companyCodeRenderer}
			<?php } else { ?>
				{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCode');?>
', width: 250, dataIndex: 'code', renderer: generalColumnRenderer},
				{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
', width: 300, sortable:false, renderer: generalColumnRenderer, dataIndex: 'name'},
				{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_AutoUpdateTitleProductCollections');?>
', width: 250, sortable: false, dataIndex: 'productcollection',renderer: generalColumnRenderer},
				<?php if ($_smarty_tpl->tpl_vars['threedpreviewavailable']->value) {?>
				{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_Label3DPreview');?>
', renderer: generalColumnRenderer, width: 300, dataIndex: 'resourcecode'},
				<?php }?>
				{header: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
', renderer: statusRenderer, width: 250, dataIndex: 'active'}
			<?php }?>
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
		<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleProducts');?>
" : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDiscountSectionPRODUCT');?>
"]})' }),
			<?php if ($_smarty_tpl->tpl_vars['threedpreviewavailable']->value) {?>
			autoExpandColumn: 3,
			<?php } else { ?>
			autoExpandColumn: 3,
			<?php }?>
		<?php } else { ?>
			<?php if ($_smarty_tpl->tpl_vars['threedpreviewavailable']->value) {?>
			autoExpandColumn: 2,
			<?php } else { ?>
			autoExpandColumn: 2,
			<?php }?>
		<?php }?>
		selModel: gridCheckBoxSelectionModelObj,
		tbar: [
			{
				ref: '../editButton',
				text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonEdit');?>
',
				iconCls: 'silk-pencil',
				handler: onEdit,
				disabled: true
			}
			,'-',
			{
				ref: '../deleteButton',
				text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDelete');?>
',
				iconCls: 'silk-delete',
				handler: onDelete,
				disabled: true
			},'-', 
			{ 
				id:'pricingButton', 
				ref: '../pricingButton', 
				text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonPricing');?>
', 
				iconCls: 'silk-money', 
				handler: onPricing,
				disabled: true	
			}, '-',
			{ 
				id:'productPriceListsButton', 
				ref: '../productPriceListsButton', 
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPriceLists');?>
", 
				iconCls: 'silk-money',
				handler: onProductPriceLists
			}, '-',
			{ 
				id:'productconfig', 
				ref: '../productconfig', 
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProductConfiguration');?>
",  
				iconCls: 'silk-chart-organisation',
				handler: onProductConfig,
				disabled: true	
			}, '-',
			<?php if ($_smarty_tpl->tpl_vars['threedpreviewavailable']->value) {?>
			{
				xtype: 'splitbutton',
				id: 'threedpreview',
				ref: '../threedpreview',
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_Label3DPreview');?>
",
				iconCls: 'threeDPreview',
				menu: new Ext.menu.Menu(
				{
					id: 'threedpreviewsmenu',
					items:
					[
						{
							text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCreateAndManage3DPreviews');?>
",
							handler: on3DPreviewConfig,
							iconCls: '',
							ref: '../previewmanage',
						},
						{
							id: 'threedpreviewassign',
							text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAssign3DPreviewToSelectedProducts');?>
",
							handler: onAssign3DPreviewToProduct,
							iconCls: '',
							ref: '../threedpreviewassign',
							disabled: true
						},
						{
							id: 'threedpreviewunassign',
							text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRemove3DPreviewFromSelectedProducts');?>
",
							handler: onUnAssign3DPreviewToProduct,
							iconCls: '',
							ref: '../previewunassign',
							disabled: true
						}
					]
				})
			},
			'-',
			<?php }?>
			{ 
				id:'productActiveButton',
				ref: '../productActiveButton', 
				text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeActive');?>
', 
				iconCls: 'silk-lightbulb',
				handler: onActivate, 
				disabled: true
			}, '-',
			{ 
				id:'productInctiveButton', 
				ref: '../productInctiveButton', 
				text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeInactive');?>
', 
				iconCls: 'silk-lightbulb-off',
				handler: onActivate, 
				disabled: true	
			}, '-',
			{
				id:'linkingPreviewButton', 
				ref: '../linkingPreviewButton', 
				text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLinkingPreview');?>
', 
				iconCls: 'silk-link',
				handler: onLinkingPreview, 
				disabled: true
			}
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				, '-',
				new Ext.form.Checkbox({boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnableGrouping');?>
", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }})
			<?php }?>,
			{xtype:'tbfill'},
			{
				id:'hideInactiveButton',
				ref: '../hideInactiveButton',
				tooltip: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipHideInactiveItems');?>
',
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
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleProducts');?>
",
		items: grid,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose(); accordianWindowInitialized = false;}, qtip: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCloseWindow');?>
' }],
		baseParams: { ref: '<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
' }
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

<?php }
}
