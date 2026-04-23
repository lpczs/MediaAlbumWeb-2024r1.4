<?php
/* Smarty version 4.5.3, created on 2026-04-09 05:53:16
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\componentspricing\componentspricinggrid.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69d73ecce9f5c1_60254444',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '90e480005fa0dbbc2e5e3c051d2de732db9fe32c' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\componentspricing\\componentspricinggrid.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69d73ecce9f5c1_60254444 (Smarty_Internal_Template $_smarty_tpl) {
?>
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
		Ext.taopix.formPost(gComponentsPricingDialogObj, serverParams, 'index.php?fsaction=AdminComponentsPricing.activate&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageUpdating');?>
", defaultPricingActivateCallback);	
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
			Ext.taopix.loadJavascript(Ext.getCmp('componentsDialog'), '', 'index.php?fsaction=AdminComponentsPricing.editDisplay&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
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
			Ext.taopix.loadJavascript(Ext.getCmp('componentsDialog'), '', 'index.php?fsaction=AdminComponentsPricing.addDisplay&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
		}
	}
	
	/* delete handler */	  
	function onDelete(btn, ev)
	{
		Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", nlToBr("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DeleteDefaultConfirmation');?>
"), onDeleteResult);
	}
	
	function onDeleteResult(btn)
	{
		if (btn == "yes")
		{
			var paramArray = new Object();
			paramArray['idlist'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('componentpricinggrid'));

			Ext.taopix.formPost(gComponentsPricingDialogObj, paramArray, 'index.php?fsaction=AdminComponentsPricing.defaultPriceDelete&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleting');?>
", onDeleteCallback);	
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
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				gridPricingDataStoreObj.groupBy('companycode');
			<?php } else { ?>
				gridPricingDataStoreObj.groupBy('active');
			<?php }?>
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
						
						<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
							<?php if ($_smarty_tpl->tpl_vars['companyLogin']->value) {?>
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
							<?php }?>
						<?php }?>
					}
				}
			}
		}
	});
	
	var gridPricingDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			groupField: 'companycodehidden',
		<?php }?> 
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminComponentsPricing.getGridData&category='+category+'&code='+componentcode+'&pricingmodel=<?php echo $_smarty_tpl->tpl_vars['pricingModel']->value;?>
&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
		    <?php if ($_smarty_tpl->tpl_vars['pricingModel']->value == 3) {?>
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
			<?php } elseif ($_smarty_tpl->tpl_vars['pricingModel']->value == 5) {?>
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
			<?php } elseif ($_smarty_tpl->tpl_vars['pricingModel']->value == 7) {?>
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
			<?php } elseif ($_smarty_tpl->tpl_vars['pricingModel']->value == 8) {?>
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
			<?php }?>
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
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", width: 130, renderer: companyRenderer, dataIndex: 'companycode'},
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['pricingModel']->value == 3) {?>
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKey');?>
", width: 150, dataIndex: 'lkey', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeStart');?>
", width: 180, dataIndex: 'qrs', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeEnd');?>
", width: 180, dataIndex: 'qre', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeBasePrice');?>
", width: 150, dataIndex: 'bp', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeUnitPrice');?>
", width: 140, dataIndex: 'up', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLineSubtract');?>
", width: 140, dataIndex: 'ls', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelIncludesTax');?>
", width: 100, dataIndex: 'it', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
", renderer: statusRenderer, width: 55, dataIndex: 'active'}
			<?php } elseif ($_smarty_tpl->tpl_vars['pricingModel']->value == 5) {?>
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKey');?>
", width: 135, dataIndex: 'lkey', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeStart');?>
", width: 120, dataIndex: 'qrs', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeEnd');?>
", width: 120, dataIndex: 'qre', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SidePriceRangeStart');?>
", width: 120, dataIndex: 'srs', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SidePriceRangeEnd');?>
", width: 120, dataIndex: 'sre', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeBasePrice');?>
", width: 110, dataIndex: 'bp', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeUnitPrice');?>
", width: 110, dataIndex: 'up', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLineSubtract');?>
", width: 110, dataIndex: 'ls', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelIncludesTax');?>
", width: 100, dataIndex: 'it', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
", renderer: statusRenderer, width: 50, dataIndex: 'active'}
			<?php } elseif ($_smarty_tpl->tpl_vars['pricingModel']->value == 7) {?>
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKey');?>
", width: 150, dataIndex: 'lkey', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeStart');?>
", width: 115, dataIndex: 'qrs', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeEnd');?>
", width: 115, dataIndex: 'qre', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ComponentPriceRangeStart');?>
", width: 130, dataIndex: 'crs', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ComponentPriceRangeEnd');?>
", width: 130, dataIndex: 'cre', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeBasePrice');?>
", width: 100, dataIndex: 'bp', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeUnitPrice');?>
", width: 100, dataIndex: 'up', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLineSubtract');?>
", width: 100, dataIndex: 'ls', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelIncludesTax');?>
", width: 100, dataIndex: 'it', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
", renderer: statusRenderer, width: 50, dataIndex: 'active'}
			<?php } elseif ($_smarty_tpl->tpl_vars['pricingModel']->value == 8) {?>
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKey');?>
", width: 130, dataIndex: 'lkey', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeStart');?>
", width: 85, dataIndex: 'qrs', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeEnd');?>
", width: 85, dataIndex: 'qre', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ComponentPriceRangeStart');?>
", width: 100, dataIndex: 'crs', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ComponentPriceRangeEnd');?>
", width: 100, dataIndex: 'cre', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SidePriceRangeStart');?>
", width: 95, dataIndex: 'srs', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SidePriceRangeEnd');?>
", width: 95, dataIndex: 'sre', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeBasePrice');?>
", width: 85, dataIndex: 'bp', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeUnitPrice');?>
", width: 85, dataIndex: 'up', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLineSubtract');?>
", width: 80, dataIndex: 'ls', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelIncludesTax');?>
", width: 100, dataIndex: 'it', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
", renderer: statusRenderer, width: 50, dataIndex: 'active'}
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				,{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", width: 170, dataIndex: 'companycodehidden', hidden:true, renderer: companyCodeRenderer}
			<?php }?>
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
		<?php if ($_smarty_tpl->tpl_vars['pricingModel']->value == 3) {?>
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				autoExpandColumn: 9,
			<?php } else { ?>
				autoExpandColumn: 8,
			<?php }?>
		<?php } elseif ($_smarty_tpl->tpl_vars['pricingModel']->value == 5) {?>
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				autoExpandColumn: 11,
			<?php } else { ?>
				autoExpandColumn: 9,
			<?php }?>
		<?php } elseif ($_smarty_tpl->tpl_vars['pricingModel']->value == 8) {?>
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				autoExpandColumn: 14,
			<?php } else { ?>
				autoExpandColumn: 12,
			<?php }?>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleComponents');?>
" : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelComponent');?>
"]})' }),
		<?php }?>
		height:400,
		selModel: pricingGridCheckBoxSelectionModelObj,
		tbar: 
		[
			{
				id:'componentPriceAddButton',
				ref: '../componentAddButton',
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
",
				iconCls: 'silk-add',
				handler: onAddPricing,
				enabled: true
			}, '-', 
			{
				ref: '../editButton',
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonEdit');?>
",
				iconCls: 'silk-pencil',
				handler: onEditPricing,
				disabled: true
			},'-', {
				ref: '../deleteButton',
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDelete');?>
",
				iconCls: 'silk-delete',
				handler: onDelete,
				disabled: true
			},
			{ 
				id:'activeButton',
				ref: '../activeButton', 
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeActive');?>
", 
				iconCls: 'silk-lightbulb',
				handler: activateDefaultComponentPrice, 
				disabled: true
			}, '-',
			{ 
				id:'inactiveButton', 
				ref: '../inactiveButton', 
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeInactive');?>
", 
				iconCls: 'silk-lightbulb-off',
				handler: activateDefaultComponentPrice, 
				disabled: true	
			}
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			,'-',
			new Ext.form.Checkbox({boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnableGrouping');?>
", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }})
			 <?php }?>
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
		title: "<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
",
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
		    qtip: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCloseWIndow');?>
',
		    handler: function(){Ext.getCmp('componentPricingDialog').close(); }
		}]
	});
	gComponentsPricingDialogObj.show();	
}
<?php }
}
