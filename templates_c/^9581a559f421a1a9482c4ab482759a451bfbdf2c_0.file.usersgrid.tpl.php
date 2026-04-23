<?php
/* Smarty version 4.5.3, created on 2026-03-06 05:33:40
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\users\usersgrid.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa6734e40942_69378300',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9581a559f421a1a9482c4ab482759a451bfbdf2c' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\users\\usersgrid.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aa6734e40942_69378300 (Smarty_Internal_Template $_smarty_tpl) {
?>

function initialize(pParams)
{
	userEditWindowExists = false;
	
	var gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj) 
			{
				var selectionCount = gridCheckBoxSelectionModelObj.getCount();
				
				if (selectionCount == 1)
				{
					grid.editButton.enable();
				}
				else
				{
					grid.editButton.disable();
				}
				
				if (selectionCount > 0)
				{
					grid.activeButton.enable();
					grid.inactiveButton.enable();
				}
				else
				{
					grid.activeButton.disable();
					grid.inactiveButton.disable();
					grid.deleteButton.disable();
				}
				
				if (selectionCount == 1 || selectionCount > 1)
				{
					var selectID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
					var idList = selectID.split(',');
					
					for (i = 0; i < idList.length; i++)
					{
						record = Ext.getCmp('maingrid').store.getById(idList[i]);
						
						if (record.data['login'] == 'administrator' && idList[i] > 0)
						{
							grid.deleteButton.disable();
							break;
						}
						else
						{
							grid.deleteButton.enable();
						}
					}
				}
			}
		}
	});
		
	var gridDataStoreObj = new Ext.data.GroupingStore({
		id: 'userstore',
		remoteSort: true,
		remoteGroup:true,
		<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			groupField: 'companycodeHidden',
		<?php }?>

		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminUsers.getGridData&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', method: 'POST'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
			Ext.data.Record.create([
				<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>		    
		    	{name: 'id', mapping: 0},
		    	{name: 'companycode', mapping: 1},
				{name: 'contactfirstname', mapping: 2},
				{name: 'contactlastname', mapping: 3},
				{name: 'login', mapping: 4},
				{name: 'emailaddress', mapping: 5},
				{name: 'usertype', mapping: 6},
				{name: 'active', mapping: 7},
				{name: 'webbrandcode', mapping: 8},
				{name: 'usertypename', mapping: 9},
				{name: 'owner', mapping: 10},
				{name: 'companycodeHidden', mapping: 11},
				{name: 'accountlocked', mapping: 12}
				<?php } else { ?>
				{name: 'id', mapping: 0},
				{name: 'contactfirstname', mapping: 1},
				{name: 'contactlastname', mapping: 2},
				{name: 'login', mapping: 3},
				{name: 'emailaddress', mapping: 4},
				{name: 'usertype', mapping: 5},
				{name: 'active', mapping: 6},
				{name: 'webbrandcode', mapping: 7},
				{name: 'usertypename', mapping: 8},
				{name: 'owner', mapping: 9},
				{name: 'accountlocked', mapping: 10}
				<?php }?>
			])
		),
		sortInfo:{field: 'fname', direction: "ASC"},
		listeners: {beforeload: checkHideInactiveButton},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});
		
	var gridColumnModelObj = new Ext.grid.ColumnModel({
		id: 'columnModel',
		defaults: {
			sortable: true, 
			resizable: true
		},
		columns: [
			gridCheckBoxSelectionModelObj,
			Ext.taopix.buildUnlockAccountGridObj(lockedAccountRenderer, function()
			{
				var gridObj = gMainWindowObj.findById('maingrid');
				var itemID = Ext.taopix.gridSelection2IDList(gridObj);
				var accountLocked = gridCheckBoxSelectionModelObj.selections.map[itemID].data.accountlocked;

				if (accountLocked == 1)
				{
					var recordID = gridCheckBoxSelectionModelObj.selections.map[itemID].data.id;

					var selRecords = gridObj.selModel.getSelections();
					var userList = [];
					for (var rec = 0; rec < selRecords.length; rec++) {userList.push("'"+selRecords[rec].data.login+"'");}

					var msg = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_UnlockUser');?>
".replace('^0', userList.join(','));

					Ext.taopix.unlockAccount(<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
, recordID, "USER-UNLOCK", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePleaseReenterYourAdministratorPasswordToUnlockUserAccounts');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageUnlocking');?>
", msg, gMainWindowObj, gridDataStoreObj);
				}
			}),
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", renderer: companyRenderer, width: 180, dataIndex: 'companycode'},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelFirstName');?>
", width: 180, dataIndex: 'contactfirstname', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLastName');?>
", width: 180, dataIndex: 'contactlastname', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUserName');?>
", width: 180,  dataIndex: 'login', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailAddress');?>
", width: 180, dataIndex: 'emailaddress', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelTypeLogin');?>
", sortable: false, renderer: loginTypeRenderer, width: 250, dataIndex: 'usertype'},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
", renderer: yesNoRenderer, width: 120, dataIndex: 'active', align: 'right'},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", width: 200, dataIndex: 'companycodeHidden', hidden: true, renderer: companyCodeRenderer}
			<?php } else { ?>
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelFirstName');?>
", width: 200, dataIndex: 'contactfirstname', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLastName');?>
", width: 200, dataIndex: 'contactlastname', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUserName');?>
", width: 200,  dataIndex: 'login', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailAddress');?>
", width: 200, dataIndex: 'emailaddress', renderer: generalColumnRenderer},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelTypeLogin');?>
", sortable: false,renderer: loginTypeRenderer, width: 100, dataIndex: 'usertype'},
				{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
", renderer: yesNoRenderer, width: 120, dataIndex: 'active', renderer: yesNoRenderer, align: 'right'}
			<?php }?>
		]
	});

	var grid = new Ext.grid.GridPanel({
		id: 'maingrid',
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
			autoFocus: true,
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				disableIndexes:['accountlocked', 'active', 'usertype','companycodeHidden']
				})],
			<?php } else { ?>
				disableIndexes:['accountlocked', 'active', 'usertype']
				})],
			<?php }?>
		columnLines:true,
		<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleUsers');?>
" : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUser');?>
"]})' }),
			autoExpandColumn: 7	,
		<?php } else { ?>
			autoExpandColumn: 5,
		<?php }?>
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
			},'-',{ 
				id:'activeButton',
				ref: '../activeButton', 
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeActive');?>
", 
				iconCls: 'silk-lightbulb',
				handler: onActivate, 
				disabled: true
			}, '-',
			{ 
				id:'inactiveButton', 
				ref: '../inactiveButton', 
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeInactive');?>
", 
				iconCls: 'silk-lightbulb-off',
				handler: onActivate, 
				disabled: true	
			},'-'			
			<?php if (($_smarty_tpl->tpl_vars['optionms']->value) && (($_smarty_tpl->tpl_vars['usertype']->value != $_smarty_tpl->tpl_vars['TPX_LOGIN_COMPANY_ADMIN']->value) && ($_smarty_tpl->tpl_vars['usertype']->value != TPX_LOGIN_SITE_ADMIN))) {?>
			,
			new Ext.form.Checkbox({boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnableGrouping');?>
", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }})
			<?php }?>
			,
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
		bbar: new Ext.PagingToolbar({ pageSize: 100, store: gridDataStoreObj, displayInfo: true, listeners: {beforechange: carryHideInactiveIntoPagingToolbarRefresh}})
	});
	
	gridDataStoreObj.load({	params: { start: 0, limit: 100,	fields: '', query: '' }	});
	
	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleUsers');?>
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

	/* column rendering functions */	
	function columnRenderer(value, p, record)
	{
		if (record.data.active == 1)
		{
			return value;
		}
		else
		{
			return '<i>' + value + '</i>';	
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
		if (value == '')
		{
			return "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_Global');?>
";
		}
		else
		{
			return value;
		}
	}

	function lockedAccountRenderer(value, p, record)
	{
		switch (Number(record.data.accountlocked))
		{
			case 0:
			default:
			{
				p.cellAttr = '';
				p.cellAttr = p.cellAttr.replace('tooltip-delayed', '');
				value = '';
				break;
			}
			case 1:
			{
				p.cellAttr = 'data-tooltip="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TooltipThisAccountIsTemporarilyLocked');?>
"';
				p.css = p.css + ' tooltip-delayed';
				value = '<img src="/utils/ext/ext-3.3.0/resources/images/default/grid/hmenu-lock.png" id="account-lock-' + record.data.recordid + '" class="account_lock" alt="" />';

				break;
			}
		}

		return value;
	}

	/* column rendering functions */	
	function loginTypeRenderer(value, metaData, record, rowIndex, colIndex, store)	
	{
		value = parseInt(value);
	
		var TPX_LOGIN_SYSTEM_ADMIN = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_SYSTEM_ADMIN']->value;?>
;
		var TPX_LOGIN_COMPANY_ADMIN = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_COMPANY_ADMIN']->value;?>
;
		var TPX_LOGIN_SITE_ADMIN = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_SITE_ADMIN']->value;?>
;
		var TPX_LOGIN_CREATOR_ADMIN = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_CREATOR_ADMIN']->value;?>
;
		var TPX_LOGIN_PRODUCTION_USER = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_PRODUCTION_USER']->value;?>
;
		var TPX_LOGIN_DISTRIBUTION_CENTRE_USER = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_DISTRIBUTION_CENTRE_USER']->value;?>
;
		var TPX_LOGIN_STORE_USER = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_STORE_USER']->value;?>
;
		var TPX_LOGIN_BRAND_OWNER = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_BRAND_OWNER']->value;?>
;
		var TPX_LOGIN_API = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_API']->value;?>
;
		var TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER']->value;?>
;
		
		switch (value)
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				 loginType = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSystemAdministrator');?>
";
				 <?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
					 assignedTo = record.data['companycode'];
					 value =  loginType + '<br />' + assignedTo;	
				 <?php } else { ?>
					 value = loginType;
				 <?php }?>
			break;
			case TPX_LOGIN_COMPANY_ADMIN:
				loginType = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompanyAdmin');?>
";
				<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
					assignedTo = record.data['companycode'] + ' - ' + record.data['usertypename'];
					value = loginType + '<br />' + assignedTo;
				<?php } else { ?>
					value = loginType;
				<?php }?>
			break;
			case TPX_LOGIN_SITE_ADMIN:
				loginType = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSiteAdmin');?>
";
				<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
					assignedTo = record.data['owner'] + ' - ' + record.data['usertypename'] ;
					value = loginType + '<br />' + assignedTo;
				<?php } else { ?>
					value = loginType;
				<?php }?>
			break;
			case TPX_LOGIN_CREATOR_ADMIN:
				value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCreatorAdmin');?>
";
			break;
			case TPX_LOGIN_PRODUCTION_USER:
				loginType = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProductionUser');?>
";
				<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
					if (record.data['owner'] == '')
					{
						assignedTo = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNone');?>
";
					}
					else
					{
						assignedTo = record.data['owner'] + ' - ' + record.data['usertypename'];
					}
					value = loginType + '<br />' + assignedTo;
				<?php } else { ?>
					value = loginType;
				<?php }?>
			break;
			case TPX_LOGIN_DISTRIBUTION_CENTRE_USER:
				loginType = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDistributionCentreLogin');?>
";
				<?php if ($_smarty_tpl->tpl_vars['optionms']->value || $_smarty_tpl->tpl_vars['optioncfs']->value) {?>
					assignedTo = record.data['owner'] + ' - ' + record.data['usertypename'];
					value = loginType + '<br />' + assignedTo;
				<?php } else { ?>
					value = loginType;
				<?php }?>
			break;
			case TPX_LOGIN_STORE_USER:
				loginType = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStoreUser');?>
";
				<?php if ($_smarty_tpl->tpl_vars['optionms']->value || $_smarty_tpl->tpl_vars['optioncfs']->value) {?>
					assignedTo = record.data['owner'] + ' - ' + record.data['usertypename'];
					value = loginType + '<br />' + assignedTo;
				<?php } else { ?>
					value = loginType;
				<?php }?>
			break;
			case TPX_LOGIN_BRAND_OWNER:
				loginType = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelBrandOwner');?>
";
				assignedTo = record.data['usertypename'];
				value = loginType + '<br />' + assignedTo;
			break;
			case TPX_LOGIN_API:
				 loginType = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAPILogin');?>
";
				 value = loginType;
			break;
			case TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER:
				loginType = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUnlockSystemAccountUser');?>
";
				value = loginType;
			break;
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

	function yesNoRenderer(value, p, record)
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

	function onActivate(btn, ev)
	{ 
		/* server parameters are sent to the server */
		var serverParams = new Object();
		serverParams['ids'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));	
	
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
		Ext.taopix.formPost(gMainWindowObj, serverParams, 'index.php?fsaction=AdminUsers.userActivate', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageUpdating');?>
", activateCallback);
	}

	function activateCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var gridObj = gMainWindowObj.findById('maingrid');
			var dataStore = gridObj.store;

			Ext.taopix.updateDataStore(dataStore, pActionData.result.data);
			dataStore.reload();
		}
	}

	/* add handler */
	function onAdd(btn, ev)
	{		
		if(!userEditWindowExists)
		{
			userEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminUsers.addDisplay&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', '', '', 'initialize', false);
		}
	}	
	
	/* edit handler */
	function onEdit(btn, ev)
	{
		/* server parameters are sent to the server */
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
		serverParams['id'] = id;
		
		if(!userEditWindowExists)
		{
			userEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminUsers.editDisplay&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
		}
	}

	/* delete handler */	  
	function onDelete(btn, ev)
	{
		Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ConfirmationDeleteUser');?>
", onDeleteResult);
	}

	function onDeleteResult(btn)
	{
		if (btn == "yes")
		{
			/* Reauthenticate the logged in user to make the changes */
			showAdminReauthDialogue(
			{
				ref: <?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
,
				reason: 'USER-DELETE',
				title: Ext.taopix.ReauthenticationDialog.strings.titleAuthenticateToSave,
				success: function()
				{
					var paramArray = new Object();
					paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));

					Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminUsers.delete', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleting');?>
", onDeleteCallback);
				}
			});
		}
	}

	function onDeleteCallback(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated == true)
		{
			var gridObj = gMainWindowObj.findById('maingrid');
			var dataStore = gridObj.store;
			var selRecords = gridObj.getSelectionModel().getSelections();
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
				Ext.MessageBox.show({ title: pActionData.result.title, msg: pActionData.result.msg,	buttons: Ext.MessageBox.OK,	icon: Ext.MessageBox.INFO });
			}
			dataStore.reload();
		}
		gridObj.deleteButton.disable();
	}

	function onHideInactive(btn, evn)
	{
		var gridDataStore = Ext.getCmp('maingrid').store;
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
				var gridDataStore = Ext.getCmp('maingrid').store;

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
