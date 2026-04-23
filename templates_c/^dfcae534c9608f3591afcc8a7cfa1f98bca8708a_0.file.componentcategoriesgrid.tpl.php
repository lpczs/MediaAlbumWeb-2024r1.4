<?php
/* Smarty version 4.5.3, created on 2026-04-09 05:48:15
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\componentcategories\componentcategoriesgrid.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69d73d9fc74e85_94193171',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'dfcae534c9608f3591afcc8a7cfa1f98bca8708a' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\componentcategories\\componentcategoriesgrid.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69d73d9fc74e85_94193171 (Smarty_Internal_Template $_smarty_tpl) {
?>
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
		return "<i><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_Global');?>
</i>";
	}
	else
	{
		return value;
	}
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
	function clearGrouping(v)
	{ 
		if(v.checked)
		{
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?> gridDataStoreObj.groupBy('companycode'); <?php }?>
		}
		else 
		{
			gridDataStoreObj.clearGrouping(); 
		}		
	}
	
	componentCategoriesEditWindowExists = false;
	componentsWindowExists = false;
	
	/* add handler */
	function onAdd(btn, ev)
	{	
		if(!componentCategoriesEditWindowExists)
		{
			componentCategoriesEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainComponentWindowObj, '', 'index.php?fsaction=AdminComponentCategories.addDisplay&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', '', '', 'initialize', false);
		}
	}
	
	/* edit handler */
	function onEdit(btn, ev)
	{
		/* server parameters are sent to the server */
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(Ext.getCmp('maingrid'));
		serverParams['id'] = id;
		
		if(!componentCategoriesEditWindowExists)
		{
			componentCategoriesEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainComponentWindowObj, '', 'index.php?fsaction=AdminComponentCategories.editDisplay&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
		}
	}
	
	/* delete handler */	  
	function onDelete(btn, ev)
	{
		var gridObj = Ext.getCmp('maingrid');
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
	
		var message = nlToBr("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DeleteConfirmation');?>
");
		message = message.replace("^0", codeList);

		dataStore.reload();
		Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", message, onDeleteResult);
	}

	function onDeleteResult(btn)
	{
		if (btn == "yes")
		{
			var paramArray = new Object();
				
			var gridObj = Ext.getCmp('maingrid');
			var selRecords = gridObj.selModel.getSelections();
			var codeList = '';
			var displayType = '';
		
			for (var rec = 0; rec < selRecords.length; rec++) 
			{	
				codeList = codeList + selRecords[rec].data.code;
				displayType = displayType + selRecords[rec].data.islist;
				
				if (rec != selRecords.length - 1)
				{
					codeList = codeList + ',';
					displayType = displayType + ',';
				}	
			}
		
			paramArray['idlist'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('maingrid'));
			paramArray['codelist'] = codeList;
			paramArray['displaytype'] = displayType;
		
			Ext.taopix.formPost(gMainComponentWindowObj, paramArray, 'index.php?fsaction=AdminComponentCategories.delete', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleting');?>
", onDeleteCallback);	
		}
	}

	function onDeleteCallback(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated == true)
		{
			var gridObj = Ext.getCmp('maingrid');
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
		serverParams['ids'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('maingrid'));	
		var active = 0; 

		switch (btn.id)
		{
			case 'categoriesActiveButton':
				active = 1;
				break;
			case 'categoriesInactiveButton':
				active = 0;
				break;
		}

		var gridObj = Ext.getCmp('maingrid');
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

		serverParams['codelist'] = codeList;
		serverParams['active'] = active;

		Ext.taopix.formPost(gMainComponentWindowObj, serverParams, 'index.php?fsaction=AdminComponentCategories.activate&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageUpdating');?>
", updateImagaeServerGridCallback);	
	}
	
	function componentActivateCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var maingridObj = gComponentsDialogObj.findById('maingrid');
			var dataStore = maingridObj.store;
	
			Ext.taopix.updateDataStore(dataStore, pActionData.result.data);
		}
	}

	function updateImagaeServerGridCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var gridObj = Ext.getCmp('maingrid');
			var dataStore = gridObj.store;
	
			Ext.taopix.updateDataStore(dataStore, pActionData.result.data);
			dataStore.reload();
		}
	}
	
	function componentHandler(btn, ev)
	{
		if(!componentsWindowExists)
		{
			componentsWindowExists = true;
			Ext.taopix.loadJavascript(gMainComponentWindowObj, '', 'index.php?fsaction=AdminComponents.initialize&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', '', '', 'initialize', false);
		}
	}
	
	
	function pricingModelRenderer(value, p, record)
	{
		
		className =  'class = "inactive"';
		
		if (record.data.status == 0)
		{
			className =  'class = "inactive"';
		}
		else
		{
			className =  'class = " "';
		}
		
		switch(value)
		{
			case '0':
				return '<span '+className+"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNoPricing');?>
</span>";
			break;
			case '1':
				return '<span '+className+"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPerOrder');?>
</span>";
			break;
			case '2':
				return '<span '+className+"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPerLine');?>
</span>";
			break;
			case '3':
				return '<span '+className+"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPerQty');?>
</span>";
			break;
			case '4':
				return '<span '+className+"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPerPageQty');?>
</span>";
			break;
			case '5':
				return '<span '+className+"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPerPageQty');?>
</span>";
			break;
			case '6':
				return '<span '+className+"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPerCharacter');?>
</span>";
			break;
			case '7':
				return '<span '+className+"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPerProductCmpQty');?>
</span>";
			break;
			case '8':
				return '<span '+className+"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPerPageProductCmpQty');?>
</span>";
			break;
		}
	}
	
	function displayTypeRenderer(value, p, record)
	{
		className =  'class = "inactive"';
		
		if (record.data.status == 0)
		{
			className =  'class = "inactive"';
		}
		else
		{
			className =  'class = " "';
		}
		
		switch(value)
		{
			case '0':
				return '<span '+className+"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCheckBox');?>
</span>";
			break;
			case '1':
				return '<span '+className+"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelList');?>
</span>";
			break;
		}
	}

	function componentTypeRenderer(value, p, record)	
	{
		switch(value)
		{
			case 'R':
				return "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRootComponent');?>
";
			break;
			case 'L':
				return "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelComponentList');?>
";
			break;
			case 'S':
				return "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelComponentStructure');?>
";
			break;
		}
	}

	function onHideInactive(btn, ev)
	{
		// get the datastore and default hide inactive to false
		var gridDataStore = Ext.getCmp('maingrid').store;
		var hideInactive = 0;

		if (btn.pressed)
		{
			// set to true and update tooltip
			hideInactive = 1;
			Ext.getCmp('hideInactiveCategoriesButton').setTooltip('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipShowInactiveItems');?>
');
		}
		else
		{
			Ext.getCmp('hideInactiveCategoriesButton').setTooltip('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipHideInactiveItems');?>
');			
		}

		// manually set the last options to allow reload to be passed hide inactive
		gridDataStore.lastOptions.params['hideInactive'] = hideInactive;

		gridDataStore.reload({params: gridDataStore.lastOptions.params});
	}

	function checkHideInactiveButton (pStore, pOptions)
	{
		var hideInactiveButton = Ext.getCmp('hideInactiveCategoriesButton');

		if (typeof hideInactiveButton !== 'undefined')
		{
			// search field has been emptied for the first time since it was last filled
			if (hideInactiveButton.disabled == true)
			{
				hideInactiveButton.enable();
				var gridDataStore = Ext.getCmp('maingrid').store;

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
	
	var gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj) 
			{
				var selectionCount = gridCheckBoxSelectionModelObj.getCount();
				
				if (selectionCount == 1)
				{
					grid.editButton.enable();
					grid.componentsButton.enable();
				}
				else
				{
					grid.editButton.disable();
					grid.componentsButton.disable();
				}
				
				if (grid)
				{
					var selRecords = grid.getSelectionModel().getSelections();
				}
                
                if (selectionCount == 1 || selectionCount > 1)
				{
                    var selectID = Ext.taopix.gridSelection2IDList(gMainComponentWindowObj.findById('maingrid'));
					var idList = selectID.split(',');
					for (i = 0; i < idList.length; i++)
					{
						record = Ext.getCmp('maingrid').store.getById(idList[i]);
                        
						<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
                            <?php if ($_smarty_tpl->tpl_vars['companyLogin']->value) {?>
                                if (record.data['companycode'] == '' && idList[i] > 0)
                                {
                                    grid.editButton.disable();
                                    grid.componentsButton.disable();
                                    grid.deleteButton.disable();
                                    grid.activeButton.disable();
                                    grid.inactiveButton.disable();
                                    break;
                                }
                                else
                                {
                                    if(selectionCount == 1)
                                    {
                                        grid.editButton.enable();
                                        grid.componentsButton.enable();
                                    }
                                    grid.activeButton.enable();
                                    grid.inactiveButton.enable();
                                    if (record.data['code'] != 'COVER' && record.data['code'] != "PAPER")
                                    {
                                        grid.deleteButton.enable();
                                    } else {
                                        grid.deleteButton.disable();
                                        break;
                                    }
                                }
                            <?php } else { ?>
                                if (record.data['code'] == '' && record.data['companycode'] == '' && idList[i] > 0)
                                {
                                    grid.editButton.disable();
                                    grid.componentsButton.disable();
                                    grid.deleteButton.disable();
                                    grid.activeButton.disable();
                                    grid.inactiveButton.disable();
                                    break;
                                }
                                else
                                {
                                    grid.activeButton.enable();
                                    grid.inactiveButton.enable();
                                    if (record.data['code'] != 'COVER' && record.data['code'] != "PAPER")
                                    {
                                        grid.deleteButton.enable();
                                    } else {
                                        grid.deleteButton.disable();
                                        break;
                                    }
                                }
                            <?php }?>
                        <?php } else { ?>
                            if (record.data['code'] == '' && idList[i] > 0)
                            {
                                grid.editButton.disable();
                                grid.componentsButton.disable();
                                grid.deleteButton.disable();
                                grid.activeButton.disable();
                                grid.inactiveButton.disable();
                                break;
                            }
                            else
                            {
                                grid.activeButton.enable();
                                grid.inactiveButton.enable();
                                if (record.data['code'] != 'COVER' && record.data['code'] != "PAPER")
                                {
                                    grid.deleteButton.enable();
                                } else {
                                    grid.deleteButton.disable();
                                    break;
                                }
                            }
                        <?php }?>
                    }
                }
                else
                {
                    grid.editButton.disable();
                    grid.componentsButton.disable();
                    grid.deleteButton.disable();
					grid.activeButton.disable();
					grid.inactiveButton.disable();
                }
			}
		}
	});
			
	var gridDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			groupField: 'companycode',
			remoteGroup: true,
		<?php }?>
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminComponentCategories.getGridData&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
		    {name: 'id', mapping: 0},
		    {name: 'companycode', mapping: 1},
			{name: 'code', mapping: 2},
			{name: 'name', mapping: 3},
			{name: 'prompt', mapping: 4},
			{name: 'pricingmodel', mapping: 5},
			{name: 'islist', mapping: 6},
			{name: 'status', mapping: 7},
			{name: 'requirespagecount', mapping: 8},
			{name: 'decimalplaces', mapping: 9}
			])
		),
		listeners: {'beforeload' : checkHideInactiveButton},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});
		
	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
			sortable: false, 
			resizable: true
		},
		columns: [
			gridCheckBoxSelectionModelObj,
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", width: 200, renderer: companyRenderer, dataIndex: 'companycode'},			
			<?php }?>
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCode');?>
", width: 170, dataIndex: 'code', renderer: generalColumnRenderer},
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
", width: 300, dataIndex: 'name', renderer: generalColumnRenderer},
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPrompt');?>
", width: 300, dataIndex: 'prompt', renderer: generalColumnRenderer},
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPricingModel');?>
", renderer: pricingModelRenderer, width: 100, dataIndex: 'pricingmodel'},
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisplayType');?>
", renderer: displayTypeRenderer, width: 100, dataIndex: 'islist'},
			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
", renderer: statusRenderer, width: 200, dataIndex: 'status', align: 'right'}
		]
	});

	var grid = new Ext.grid.GridPanel({
		id: 'maingrid',
		store: gridDataStoreObj,
		cm: gridColumnModelObj,
		view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ComponentTitleComponentCategories');?>
" : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelComponentCategory');?>
"]})' }),
		enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		enableHdMenu:false,
		trackMouseOver:false,
		stripeRows:true,
		columnLines:true,
		autoExpandColumn: 6,
		ctCls: 'grid',
		selModel: gridCheckBoxSelectionModelObj,
		tbar: [
			{
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
",
				iconCls: 'silk-add',
				handler: onAdd
			}, '-', 
			{
				ref: '../editButton',
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonEdit');?>
",
				iconCls: 'silk-pencil',
				handler: onEdit,
				disabled: true
			}
			,'-', {
				ref: '../deleteButton',
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDelete');?>
",
				iconCls: 'silk-delete',
				handler: onDelete,
				disabled: true
			},{ 
				id:'categoriesActiveButton',
				ref: '../activeButton', 
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeActive');?>
", 
				iconCls: 'silk-lightbulb',
				handler: onActivate, 
				disabled: true
			}, '-',
			{ 
				id:'categoriesInactiveButton', 
				ref: '../inactiveButton', 
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeInactive');?>
", 
				iconCls: 'silk-lightbulb-off',
				handler: onActivate, 
				disabled: true	
			},'-',
			{ 
                id: 'componentsButton',
				ref: '../componentsButton', 
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleComponents');?>
", 
				iconCls: 'silk-wrench',
				handler: componentHandler,
				disabled:true
			}, '-'
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			,
			new Ext.form.Checkbox({boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnableGrouping');?>
", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }})
			<?php }?>
			,{xtype:'tbfill'},
			{
				id:'hideInactiveCategoriesButton',
				ref: '../hideInactiveCategoriesButton',
				tooltip: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipHideInactiveItems');?>
',
				iconCls: 'hideInactiveButton',
				handler: onHideInactive,
				enableToggle: true,
				xtype: 'button',
				ctCls:'x-toolbar-standardbutton'
				},
				{xtype: 'tbspacer', width: 10} 
		]
	});
	
	gridDataStoreObj.load({params: {hideInactive : 0}});

	gMainComponentWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ComponentTitleComponentCategories');?>
",
		items: grid,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose(); accordianWindowInitialized = false;}, qtip: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCloseWindow');?>
' }],
		baseParams: { ref: '<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
' }
	});
		
	centreRegion.add(gMainComponentWindowObj);
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
	if (Ext.getCmp('dialog'))
	{
		Ext.getCmp('dialog').close();
	}
}

<?php }
}
