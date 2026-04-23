<?php
/* Smarty version 4.5.3, created on 2026-03-17 00:53:40
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\autoupdate\autoupdateapplication.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b8a614c6ffb8_43605538',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '19d14dcba6e2b78f0fedcb1eb0e1a2d8587dd95c' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\autoupdate\\autoupdateapplication.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b8a614c6ffb8_43605538 (Smarty_Internal_Template $_smarty_tpl) {
?>var applicationUpdateTitle = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_AutoUpdateTitleApplication');?>
";
var brandingTitle_tx       = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleBranding');?>
";
var osTitle_tx             = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOperatingSystem');?>
";
var versionTitle_tx        = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelVersion');?>
";
var archiveTitle_tx        = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelArchiveName');?>
";
var executableTitle_tx     = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelExeName');?>
";
var savingLabel_txt        = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
";
var addLabel_txt           = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonEdit');?>
";
var buttonDelete_txt       = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDelete');?>
";
var delteLabel_txt         = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
";
var delteConformation_txt  = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DeleteApplicationConfirmation');?>
";
var deletingLabel_txt      = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleting');?>
"; 
var removeLabel_txt        = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRemove');?>
"; 
var deleteSuccess_txt      = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DeleteApplications');?>
";
var selectCompany_txt      = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSelectCompany');?>
";
var closeWindow_txt      = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCloseWindow');?>
";
var priorityLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPriority');?>
";
var priorityNormalLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPriorityNormal');?>
";
var priorityCriticalLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPriorityCritical');?>
";
var changingPriorityLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageChangingPriority');?>
";


var session_id      = "<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
";
var userType        = "<?php echo $_smarty_tpl->tpl_vars['userType']->value;?>
";
var optionMS        = "<?php echo $_smarty_tpl->tpl_vars['optionMS']->value;?>
";
var companyCode     = "<?php echo $_smarty_tpl->tpl_vars['companyCode']->value;?>
";
	
var gridPageSize = 100;
	


function initialize(pParams)
{
	function onDeleteCallback(pUpdated, pTheForm, pActionData) 
	{
		if (pUpdated) 
		{
			Ext.MessageBox.show({ title: delteLabel_txt, msg: deleteSuccess_txt, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO });  
			grid.store.reload(); 
		}
		else 
		{ 
			Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING }); 
		}
	};
	
	var onDeleteResult = function(btn) 
	{
		if (btn == "yes") 
		{
			var paramArray = new Object();
			var selRecords = gridCheckBoxSelectionModelObj.getSelections();
			var ids = new Array();	
			var oss = new Array();
			
			for (var rec = 0, brand, os; rec < selRecords.length; rec++) 
			{	
				ids.push(selRecords[rec].data.brandCode);	
				oss.push(selRecords[rec].data.osCode); 
			}
			var iDList = ids.join(','); 
			var OSList = oss.join(',');

			paramArray['idlist'] = iDList;  
			paramArray['oslist'] = OSList;
			Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminAutoUpdate.deleteApplication', deletingLabel_txt, onDeleteCallback);	
		}
	};
	
	var onDelete = function(btn, ev)
	{ 
		Ext.MessageBox.confirm(delteLabel_txt, delteConformation_txt, onDeleteResult); 
	};

	
	gridDataStoreObj = new Ext.data.GroupingStore(
	{
		remoteGroup:true,
		remoteSort: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminAutoUpdate.listApplication&ref='+session_id }),
		method:'POST',
		groupField:'brandName',
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([ 
				{name:'appId', mapping: 0},
				{name: 'brandCode', mapping: 1},
				{name: 'brandName', mapping: 2}, 
				{name: 'osCode', mapping: 3}, 
				{name: 'osName', mapping: 4},  
				{name: 'versionCode', mapping: 5},  
				{name: 'archiveName', mapping: 6}, 
				{name: 'exeName', mapping: 7},
				{name: 'priority', mapping: 8}
			])
		),
		sortInfo:{field: 'brandName', direction: "ASC"},
		listeners:
		{
        	'beforeload':function()
        	{ 
				var companyFilterCmb = Ext.getCmp('companyFilter');
				var appGrid = Ext.getCmp('applicationUpdateGrid');
				if(companyFilterCmb)
				{
					appGrid.store.lastOptions.params['companyCode'] = companyFilterCmb.getValue();
					appGrid.store.setBaseParam('companyCode', companyFilterCmb.getValue());
				}
    			
        	}
        },
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	}); 
	gridDataStoreObj.load({params:{start:0, limit:gridPageSize, companyCode: companyCode}});
	
	gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel(
	{ 
		listeners: 
		{
			selectionchange: function(gridCheckBoxSelectionModelObj) 
			{ 
				if (gridCheckBoxSelectionModelObj.getCount() > 0)
				{ 
					grid.deleteButton.enable();	
					grid.highPriorityButton.enable();
					grid.lowPriorityButton.enable(); 
				} 
				else  
				{
					grid.deleteButton.disable();
					grid.highPriorityButton.disable(); 
					grid.lowPriorityButton.disable(); 
				}
			}
		}
	});
	
	var gridColumnModelObj = new Ext.grid.ColumnModel(
	{
		defaults: {	sortable: false, resizable: true, menuDisabled: true },
		columns: [gridCheckBoxSelectionModelObj,
            { id:'brandName', header: brandingTitle_tx, dataIndex: 'brandName'},
            { header: osTitle_tx, dataIndex: 'osName', width:150 },
            { header: versionTitle_tx, dataIndex: 'versionCode', width:120},
            { header: archiveTitle_tx, dataIndex: 'archiveName', width:150},
            { header: executableTitle_tx, dataIndex: 'exeName', width:150},
			{ header: priorityLabel_txt, id: 'priority', dataIndex: 'priority', width:85, renderer: priorityRenderer, align:'right'}
        ]
	});

	var changePriority = function(btn, ev)
	{  
		var maskText = changingPriorityLabel_txt;
		var command = 0;
		if(btn.id == 'highPriorityButton') {maskText = changingPriorityLabel_txt; command = 1000;}
		
		var paramArray = new Object();
		var selRecords = gridCheckBoxSelectionModelObj.getSelections();
		var ids = new Array();
		var oss = new Array();

		for (var rec = 0, brand, os; rec < selRecords.length; rec++) 
		{	
			ids.push(selRecords[rec].data.brandCode);	
			oss.push(selRecords[rec].data.osCode); 
		}

		var iDList = ids.join(',');
		var OSList = oss.join(',');

		paramArray['idlist'] = iDList;
		paramArray['command'] = command;
		paramArray['oslist'] = OSList;

		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminAutoUpdate.changeApplicationPriority', maskText, onPriorityChangeCallback);
	};
	
	var onCompanyChange = function()
	{
		var companyCombo = Ext.getCmp('companyFilter');
		var appGrid = Ext.getCmp('applicationUpdateGrid');
		appGrid.getBottomToolbar().changePage(1);
	};
	
	var grid = new Ext.grid.GridPanel(
	{
    	id: 'applicationUpdateGrid',
        store: gridDataStoreObj,
        selModel: gridCheckBoxSelectionModelObj,
        cm: gridColumnModelObj,
        stripeRows: true,
        stateful: true,
        view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItems');?>
" : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItem');?>
"]})' }),
        draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		trackMouseOver:false,
		autoExpandColumn: 'brandName',
		ctCls: 'grid',
		columnLines:true,
        stateId: 'applicationUpdateGrid',
        tbar: [	
               	{ref: '../deleteButton', text: removeLabel_txt, iconCls: 'silk-delete', handler: onDelete, disabled: true, id:'deleteButton'},
   				{id:'highPriorityButton', iconCls: 'silk-flag-red', ref: '../highPriorityButton', text: priorityCriticalLabel_txt, handler: changePriority, disabled: true}, '-',
       	    	{id:'lowPriorityButton', iconCls: 'silk-flag-green', ref: '../lowPriorityButton', text: priorityNormalLabel_txt, handler: changePriority, disabled: true}
               	<?php if ($_smarty_tpl->tpl_vars['optionMS']->value == true && $_smarty_tpl->tpl_vars['userType']->value == 0) {?>
				,{xtype:'tbfill'}
               	,new Ext.taopix.CompanyCombo({id:'companyFilter',name: 'companyFilter', emptyText: selectCompany_txt, options: {ref: session_id, includeGlobal: '0', includeShowAll:'1', onchange: onCompanyChange} })
               	,{xtype: 'tbspacer', width: 10}
               	<?php }?>
              ], 
        bbar: new Ext.PagingToolbar({ pageSize: gridPageSize, store: gridDataStoreObj, displayInfo: true})    
    }); 

	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: applicationUpdateTitle,
		items: grid,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose(); accordianWindowInitialized = false;}, qtip: closeWindow_txt }],
		baseParams: { ref: '<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
' }
	});
		
	centreRegion.add(gMainWindowObj);
	centreRegion.doLayout();

	function priorityRenderer(value, p, record, rowIndex, colIndex, store) 
	{
		if (record.data.priority == 0) 
		{
			return '<img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/flag_green.png" />';
		}
		else
		{
			return '<img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/flag_red.png" />';
		}
		
	};

	function onPriorityChangeCallback(pUpdated, pTheForm, pActionData) 
	{
		if(pUpdated)
		{ 
			grid.store.reload();
		} 
		else 
		{ 
			Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
		}
	};
}

/* close this window panel */
function windowClose()
{
	centreRegion.remove('MainWindow', true);
	centreRegion.doLayout();
}

<?php }
}
