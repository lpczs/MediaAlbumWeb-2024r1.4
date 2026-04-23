<?php
/* Smarty version 4.5.3, created on 2026-04-21 08:40:11
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\branding\branding.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69e737eb46d5f3_55787061',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ecfcc1cde03f23d1d2b5bc0f4abb5883c5c07ad9' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\branding\\branding.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69e737eb46d5f3_55787061 (Smarty_Internal_Template $_smarty_tpl) {
?>

function initialize(pParams)
{
	var sessionId = "<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
";
	var gridPageSize = 100;
	var companyCode = '';

	brandingEditWindowExists = false;

	onCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
			gridDataStoreObj.reload();
			if ((Ext.getCmp('dialog')) && (Ext.getCmp('dialog').isVisible()))
			{
				Ext.getCmp('dialog').close();
			}
		}
	};

	var onAdd = function()
	{
		var paramArray = [];

		if (!brandingEditWindowExists)
		{
			brandingEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminBranding.addDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}
	};

	var onEdit = function()
	{
		var paramArray = {};
		paramArray['id'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('brandingGrid'));

		if (!brandingEditWindowExists)
		{
			brandingEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminBranding.editDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}

	};

	var onDelete = function(btn, ev)
	{
		var onDeleteConfirmed = function(btn)
		{
			if (btn == "yes") {
				var paramArray = {};
				paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('brandingGrid'));
				Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminBranding.delete', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleting');?>
", onCallback);
			}
		};

		var gridObj = gMainWindowObj.findById('brandingGrid');
		var selRecords = gridObj.selModel.getSelections();
		var codeList = [];

		for (var rec = 0; rec < selRecords.length; rec++) {	codeList.push("'"+selRecords[rec].data.foldername+"'");}
		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DeleteConfirmation');?>
".replace("'^0'", codeList.join(', ')), onDeleteConfirmed);
	};

	var onActivate = function(btn, ev)
	{
		var gridObj = gMainWindowObj.findById('brandingGrid');
		var paramArray = {};
		paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gridObj);

		var active = 0;
		if (btn.id == 'activeButton') active = 1;
		paramArray['active'] = active;

		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminBranding.brandingActivate', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageUpdating');?>
", onCallback);
	};

	var onCompanyChange = function()
	{
		var appGrid = Ext.getCmp('brandingGrid');

		var companyFilterObj = Ext.getCmp('companyFilter');
		if (companyFilterObj)
		{
			companyCode = companyFilterObj.getValue();
		}
		appGrid.store.lastOptions.params['companyCode'] = companyCode;

		appGrid.store.reload({params: appGrid.store.lastOptions.params});
	};

	var columnRenderer = function(value, p, record, rowIndex, colIndex, store)
	{
		var className = '';
		if (record.data.isactive == 0)
		{
			if (colIndex == 5) value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInactive');?>
";
			className = 'class = "inactive"'
		}
		else
		{
			if (colIndex == 5) value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
";
		}
		return '<span '+className+'>'+value+'</span>';
	};

	gridDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		remoteGroup: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminBranding.list&ref=' + sessionId }),
		method:'POST',
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()},
		<?php if ($_smarty_tpl->tpl_vars['optionMS']->value) {?>
			groupField:'company',
		<?php }?>
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([
				{name:'recordid', mapping: 0},
				{name:'code', mapping: 1},
				{name:'company', mapping: 2},
				{name: 'foldername', mapping: 3},
				{name: 'appname', mapping: 4},
				{name: 'displayurl', mapping: 5},
				{name: 'isactive', mapping: 6}
			])
		),
		sortInfo:{field: 'foldername', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});
	gridDataStoreObj.load({params:{start:0, limit:gridPageSize, companyCode: companyCode}});

	gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj)
			{
				var records = gridCheckBoxSelectionModelObj.getSelections();
				var hasDefault = false;
				for (var i = 0; i < records.length; i++)
				{
					if (records[i].data.code == '')
					{
						hasDefault = true;
						break;
					}
				}
				if (gridCheckBoxSelectionModelObj.getCount() > 0)
				{
					if (gridCheckBoxSelectionModelObj.getCount() == 1)
					{
						gridObj.editButton.enable();
					}
					else
					{
						gridObj.editButton.disable();
					}
					if (hasDefault)
					{
						gridObj.activeButton.disable(); gridObj.inactiveButton.disable(); gridObj.deleteButton.disable();
					}
					else
					{
						gridObj.activeButton.enable(); gridObj.inactiveButton.enable(); gridObj.deleteButton.enable();
					}
				}
				else
				{
					gridObj.activeButton.disable();
					gridObj.inactiveButton.disable();
					gridObj.editButton.disable();
					gridObj.deleteButton.disable();
				}
			}
		}
	});

	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {	sortable: true, resizable: true },
		columns: [gridCheckBoxSelectionModelObj,
			{ id:'company', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompanyName');?>
", dataIndex: 'company', hidden:true },
	    	{ id: 'foldername', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelFolderName');?>
", width: 250, dataIndex: 'foldername', renderer: columnRenderer },
	    	{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelApplicationName');?>
", dataIndex: 'appname', width:250, renderer: columnRenderer},
        	{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisplayURL');?>
", dataIndex: 'displayurl', width:550, renderer: columnRenderer},
        	{ id: 'isactive', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
", dataIndex: 'isactive', width:120, renderer: columnRenderer, align: 'right'}
        ]
	});

	gridObj = new Ext.grid.GridPanel({
   		id: 'brandingGrid',
   		store: gridDataStoreObj,
    	selModel: gridCheckBoxSelectionModelObj,
    	cm: gridColumnModelObj,
    	stripeRows: true,
    	stateful: true,
    	enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		trackMouseOver:false,
		view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItems');?>
" : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItem');?>
"]})' }),
		autoExpandColumn: 'isactive',
		columnLines:true,
    	stateId: 'brandingGrid',
    	ctCls: 'grid',
    	tbar: [	{
    				ref: '../addButton',
    				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
",
    				iconCls: 'silk-add',
    				handler: onAdd,
    				<?php if ($_smarty_tpl->tpl_vars['bc']->value) {?>
    					disabled: false
    				<?php } else { ?>
    					disabled: true
    				<?php }?>
    			}, '-',
        	    {ref: '../editButton',	text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonEdit');?>
",	iconCls: 'silk-pencil',	handler: onEdit, disabled: true	}, '-',
           		{ref: '../deleteButton', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDelete');?>
", iconCls: 'silk-delete', handler: onDelete, disabled: true, id:'deleteButton' }, '-',
          	  	{ref: '../activeButton', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeActive');?>
", iconCls: 'silk-lightbulb', handler: onActivate, disabled: true, id:'activeButton'}, '-',
      	   		{ref: '../inactiveButton', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeInactive');?>
", iconCls: 'silk-lightbulb-off',  handler: onActivate, disabled: true, id:'inactiveButton'}

      	   		<?php if ($_smarty_tpl->tpl_vars['optionMS']->value == true && $_smarty_tpl->tpl_vars['userType']->value == 0) {?>
				,{xtype:'tbfill'}
               	,new Ext.taopix.CompanyCombo({id:'companyFilter',name: 'companyFilter', emptyText:"<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompanyName');?>
", options: {ref: sessionId, includeGlobal: '1', includeShowAll:'1', onchange: onCompanyChange} })
               	,{xtype: 'tbspacer', width: 10}
               	<?php }?>
    	],
    	plugins: [
			new Ext.ux.grid.Search({
				iconCls: 'silk-zoom',
				minChars: 3,
				width: 230,
				autoFocus: true,
				disableIndexes: ['displayurl', 'company', 'isactive']
			})
		],
		bbar: new Ext.PagingToolbar({ pageSize: gridPageSize, store: gridDataStoreObj, displayInfo: true })
	});

	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleBranding');?>
",
		items: gridObj,
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
	if (Ext.getCmp('dialog'))
	{
		Ext.getCmp('dialog').close();
	}
}


<?php }
}
