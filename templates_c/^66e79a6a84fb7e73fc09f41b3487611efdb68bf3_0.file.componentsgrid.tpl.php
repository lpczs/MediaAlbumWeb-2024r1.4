<?php
/* Smarty version 4.5.3, created on 2026-04-09 05:48:44
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\components\componentsgrid.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69d73dbc089775_27703888',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '66e79a6a84fb7e73fc09f41b3487611efdb68bf3' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\components\\componentsgrid.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69d73dbc089775_27703888 (Smarty_Internal_Template $_smarty_tpl) {
?>
function companyRenderer(value, p, record)
{
	if (value == '')
	{
		value = "<i><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_Global');?>
</i>";
	}

	if (record.data.status == 0)
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
	if (record.data.status == 0)
	{
		className =  'class = "inactive"';
		return '<span '+className+'>'+value+'</span>';
	}
	else
	{
		return '<span class="">'+value+'</span>';
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
			Ext.taopix.loadJavascript(gComponentsDialogObj, '', 'index.php?fsaction=AdminComponentsPricing.initialize&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
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
			Ext.taopix.loadJavascript(gComponentsDialogObj, '', 'index.php?fsaction=AdminComponentsPricing.priceListsInitialize&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
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
			Ext.taopix.loadJavascript(gComponentsDialogObj, '', 'index.php?fsaction=AdminComponents.editDisplay&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
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
			Ext.taopix.loadJavascript(gComponentsDialogObj, '', 'index.php?fsaction=AdminComponents.addDisplay&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
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

				Ext.taopix.formPost(gComponentsDialogObj, paramArray, 'index.php?fsaction=AdminComponents.delete&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleting');?>
", onDeleteComponentCallback);	
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
	
		var message = nlToBr("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DeleteConfirmation');?>
");
		message = message.replace("^0", codeList);

		dataStore.reload();
		Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", message, onDeleteComponentResult);
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

		Ext.taopix.formPost(gComponentsDialogObj, serverParams, 'index.php?fsaction=AdminComponents.activate&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageUpdating');?>
", componentActivateCallback);	
	}

	function clearGrouping(v)
	{ 
		if(v.checked)
		{
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				gridDataStoreObj.groupBy('companycode');
			<?php } else { ?>
				gridDataStoreObj.groupBy('active');
			<?php }?>
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
			Ext.getCmp('hideInactiveButton').setTooltip('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipShowInactiveItems');?>
');
		}
		else
		{
			Ext.getCmp('hideInactiveButton').setTooltip('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipHideInactiveItems');?>
');			
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
				hideInactiveButton.setTooltip({text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipHideInactiveItemsIsDisabledForSearchResults');?>
', autoHide: true, id: 'hideInactiveDisabledTooltip'});
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
						<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
                            <?php if ($_smarty_tpl->tpl_vars['companyLogin']->value) {?>
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
                            <?php } else { ?>
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
                            <?php }?>
                        <?php } else { ?>
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
                        <?php }?>
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
		<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			groupField: 'companycodehidden',
		<?php }?> 
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminComponents.getGridData&category=' + category + '&categorycompanycode=' + categoryCompanyCode + '&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
'}),
		reader: new Ext.taopix.PagedArrayReader({idIndex: 0},
		Ext.data.Record.create([
		    <?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
		    	{name: 'id', mapping: 0},
			    {name: 'companycode', mapping: 1},
			    {name: 'code', mapping: 2},
			    {name: 'localcode', mapping: 3},
				{name: 'skucode', mapping: 4},
				{name: 'name', mapping: 5},			
				{name: 'status', mapping: 6},
				{name: 'category', mapping: 7},
				{name: 'companycodehidden', mapping: 8}
		    <?php } else { ?>
			    {name: 'id', mapping: 0},
			    {name: 'code', mapping: 1},
			    {name: 'localcode', mapping: 2},
				{name: 'skucode', mapping: 3},
				{name: 'name', mapping: 4},			
				{name: 'status', mapping: 5},
				{name: 'category', mapping: 6}
			<?php }?>
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
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", width: 150, renderer: companyRenderer, dataIndex: 'companycode'},
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCode');?>
", width: 200, renderer: generalColumnRenderer, dataIndex: 'localcode'},
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSKUCode');?>
", width: 150,  renderer: generalColumnRenderer, dataIndex: 'skucode'},
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
", width: 300,  renderer: generalColumnRenderer, dataIndex: 'name'},
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
",renderer: statusRenderer, width: 50, align:'right', dataIndex: 'status'},
			{hidden: true, width: 50, align:'right', dataIndex: 'category'},
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", width: 170, dataIndex: 'companycodehidden', hidden:true, renderer: companyCodeRenderer}
			<?php } else { ?>
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCode');?>
", width: 200, renderer: generalColumnRenderer, hidden: true, dataIndex: 'code'},
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCode');?>
", width: 200, renderer: generalColumnRenderer, dataIndex: 'localcode'},
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSKUCode');?>
", width: 150,  renderer: generalColumnRenderer, dataIndex: 'skucode'},
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
", width: 300,  renderer: generalColumnRenderer, dataIndex: 'name'},
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
",renderer: statusRenderer, width: 50, align:'right', dataIndex: 'status'},
			{hidden: true, width: 50, align:'right', dataIndex: 'category'}
			<?php }?>
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
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				disableIndexes:['category','status','companycode', 'companycodehidden'],
			<?php } else { ?>
				disableIndexes:['category','status'],
			<?php }?>
				autoFocus: true
		})],
		<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleComponents');?>
" : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelComponent');?>
"]})' }),
		<?php }?>
        autoExpandColumn: 5,
		height:400,
		ctCls: 'grid',
		style:'border:1px solid #99BBE8;',
		selModel: gridCheckBoxSelectionModelObj,
		tbar: [
			{
				id:'componentAddButton',
				ref: '../componentAddButton',
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
",
				iconCls: 'silk-add',
				handler: onAddComponent,
				enabled: true
			}, '-', 
			{
				ref: '../editButton',
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonEdit');?>
",
				iconCls: 'silk-pencil',
				handler: onEditComponent,
				disabled: true
			},'-', 
			{
				ref: '../deleteButton',
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDelete');?>
",
				iconCls: 'silk-delete',
				handler: onDeleteComponent,
				disabled: true
			},'-',
			{ 
				id:'componentActiveButton',
				ref: '../activeButton', 
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeActive');?>
", 
				iconCls: 'silk-lightbulb',
				handler: activateComponent, 
				disabled: true
			}, '-',
			{ 
				id:'componentInactiveButton', 
				ref: '../inactiveButton', 
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeInactive');?>
", 
				iconCls: 'silk-lightbulb-off',
				handler: activateComponent, 
				disabled: true	
			},'-',
			{ 
				id:'componentsDefaultPricingButton', 
				ref: '../componentsDefaultPricingButton', 
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDefaultPricing');?>
", 
				iconCls: 'silk-money',
				handler: onComponentPricing,
				disabled: true
			},'-',
			{ 
				id:'componentsPriceListsButton', 
				ref: '../componentsPriceListsButton', 
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPriceLists');?>
", 
				iconCls: 'silk-money',
				handler: onComponentPriceLists
			}
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			,'-',
			new Ext.form.Checkbox({boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnableGrouping');?>
", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }})
			 <?php }?>
			,{xtype:'tbfill'},
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
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionWindowTitleComponents');?>
".replace("^0", category),
		resizable:false,
		height: 'auto',
	  	width: 1200,
		tools:[{
			id:'clse',
		    qtip: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCloseWIndow');?>
',
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

<?php }
}
