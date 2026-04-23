{literal}

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
		{/literal}{if $optionms}{literal}
			groupField: 'companycodeHidden',
		{/literal}{/if}{literal}

		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminUsers.getGridData&ref={/literal}{$ref}{literal}', method: 'POST'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
			Ext.data.Record.create([
				{/literal}{if $optionms}{literal}		    
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
				{/literal}{else}{literal}
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
				{/literal}{/if}{literal}
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

					var msg = "{/literal}{#str_UnlockUser#}{literal}".replace('^0', userList.join(','));

					Ext.taopix.unlockAccount({/literal}{$ref}{literal}, recordID, "USER-UNLOCK", "{/literal}{#str_MessagePleaseReenterYourAdministratorPasswordToUnlockUserAccounts#}{literal}", "{/literal}{#str_MessageUnlocking#}{literal}", msg, gMainWindowObj, gridDataStoreObj);
				}
			}),
			{/literal}{if $optionms}{literal}
				{header: "{/literal}{#str_LabelCompany#}{literal}", renderer: companyRenderer, width: 180, dataIndex: 'companycode'},
				{header: "{/literal}{#str_LabelFirstName#}{literal}", width: 180, dataIndex: 'contactfirstname', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelLastName#}{literal}", width: 180, dataIndex: 'contactlastname', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelUserName#}{literal}", width: 180,  dataIndex: 'login', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelEmailAddress#}{literal}", width: 180, dataIndex: 'emailaddress', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelTypeLogin#}{literal}", sortable: false, renderer: loginTypeRenderer, width: 250, dataIndex: 'usertype'},
				{header: "{/literal}{#str_LabelStatus#}{literal}", renderer: yesNoRenderer, width: 120, dataIndex: 'active', align: 'right'},
				{header: "{/literal}{#str_LabelCompany#}{literal}", width: 200, dataIndex: 'companycodeHidden', hidden: true, renderer: companyCodeRenderer}
			{/literal}{else}{literal}
				{header: "{/literal}{#str_LabelFirstName#}{literal}", width: 200, dataIndex: 'contactfirstname', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelLastName#}{literal}", width: 200, dataIndex: 'contactlastname', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelUserName#}{literal}", width: 200,  dataIndex: 'login', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelEmailAddress#}{literal}", width: 200, dataIndex: 'emailaddress', renderer: generalColumnRenderer},
				{header: "{/literal}{#str_LabelTypeLogin#}{literal}", sortable: false,renderer: loginTypeRenderer, width: 100, dataIndex: 'usertype'},
				{header: "{/literal}{#str_LabelStatus#}{literal}", renderer: yesNoRenderer, width: 120, dataIndex: 'active', renderer: yesNoRenderer, align: 'right'}
			{/literal}{/if}{literal}
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
			{/literal}{if $optionms}{literal}
				disableIndexes:['accountlocked', 'active', 'usertype','companycodeHidden']
				})],
			{/literal}{else}{literal}
				disableIndexes:['accountlocked', 'active', 'usertype']
				})],
			{/literal}{/if}{literal}
		columnLines:true,
		{/literal}{if $optionms}{literal}
			view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_SectionTitleUsers#}{literal}" : "{/literal}{#str_LabelUser#}{literal}"]})' }),
			autoExpandColumn: 7	,
		{/literal}{else}{literal}
			autoExpandColumn: 5,
		{/literal}{/if}{literal}
		selModel: gridCheckBoxSelectionModelObj,
		tbar: [
			{
				text: "{/literal}{#str_ButtonAdd#}{literal}",
				iconCls: 'silk-add',
				handler: onAdd
			}, '-', 
			{
				ref: '../editButton',
				text: "{/literal}{#str_ButtonEdit#}{literal}",
				iconCls: 'silk-pencil',
				handler: onEdit,
				disabled: true
			}
			,'-', {
				ref: '../deleteButton',
				text: "{/literal}{#str_ButtonDelete#}{literal}",
				iconCls: 'silk-delete',
				handler: onDelete,
				disabled: true
			},'-',{ 
				id:'activeButton',
				ref: '../activeButton', 
				text: "{/literal}{#str_LabelMakeActive#}{literal}", 
				iconCls: 'silk-lightbulb',
				handler: onActivate, 
				disabled: true
			}, '-',
			{ 
				id:'inactiveButton', 
				ref: '../inactiveButton', 
				text: "{/literal}{#str_LabelMakeInactive#}{literal}", 
				iconCls: 'silk-lightbulb-off',
				handler: onActivate, 
				disabled: true	
			},'-'			
			{/literal}{if ($optionms) && (($usertype != $TPX_LOGIN_COMPANY_ADMIN) && ($usertype != TPX_LOGIN_SITE_ADMIN))}{literal}
			,
			new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelEnableGrouping#}{literal}", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }})
			{/literal}{/if}{literal}
			,
			{xtype:'tbfill'},
			{
				id:'hideInactiveButton',
				ref: '../hideInactiveButton',
				tooltip: '{/literal}{#str_TooltipHideInactiveItems#}{literal}',
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
		title: "{/literal}{#str_SectionTitleUsers#}{literal}",
		items: grid,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose(); accordianWindowInitialized = false;}, qtip: '{/literal}{#str_LabelCloseWindow#}{literal}' }],
		baseParams: { ref: '{/literal}{$ref}{literal}' }
	});
		
	centreRegion.add(gMainWindowObj);
	centreRegion.doLayout();
	

	function clearGrouping(v)
	{ 
		if(v.checked)
		{
			{/literal}{if $optionms}{literal}
				gridDataStoreObj.groupBy('companycodeHidden');
			{/literal}{else}{literal}
				gridDataStoreObj.groupBy('active');
			{/literal}{/if}{literal}
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
			return '{/literal}<span '+className+'>'+value+'</span>{literal}';
		}
		else
		{
			return '{/literal}<span class="">'+value+'</span>{literal}';
		}	
	}

	function companyRenderer(value, p, record)
	{
		if (value == '')
		{
			value = "<i>{/literal}{#str_Global#}{literal}</i>";
		}
		
		if (record.data.active == 0)
		{
			className =  'class = "inactive"';
			return '{/literal}<span '+className+'>'+value+'</span>{literal}';
		}
		else
		{
			return '{/literal}<span class="">'+value+'</span>{literal}';
		}	
	}

	function companyCodeRenderer(value, p, record)
	{
		if (value == '')
		{
			return "{/literal}{#str_Global#}{literal}";
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
				p.cellAttr = 'data-tooltip="{/literal}{#str_TooltipThisAccountIsTemporarilyLocked#}{literal}"';
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
	
		var TPX_LOGIN_SYSTEM_ADMIN = {/literal}{$TPX_LOGIN_SYSTEM_ADMIN}{literal};
		var TPX_LOGIN_COMPANY_ADMIN = {/literal}{$TPX_LOGIN_COMPANY_ADMIN}{literal};
		var TPX_LOGIN_SITE_ADMIN = {/literal}{$TPX_LOGIN_SITE_ADMIN}{literal};
		var TPX_LOGIN_CREATOR_ADMIN = {/literal}{$TPX_LOGIN_CREATOR_ADMIN}{literal};
		var TPX_LOGIN_PRODUCTION_USER = {/literal}{$TPX_LOGIN_PRODUCTION_USER}{literal};
		var TPX_LOGIN_DISTRIBUTION_CENTRE_USER = {/literal}{$TPX_LOGIN_DISTRIBUTION_CENTRE_USER}{literal};
		var TPX_LOGIN_STORE_USER = {/literal}{$TPX_LOGIN_STORE_USER}{literal};
		var TPX_LOGIN_BRAND_OWNER = {/literal}{$TPX_LOGIN_BRAND_OWNER}{literal};
		var TPX_LOGIN_API = {/literal}{$TPX_LOGIN_API}{literal};
		var TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER = {/literal}{$TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER}{literal};
		
		switch (value)
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				 loginType = "{/literal}{#str_LabelSystemAdministrator#}{literal}";
				 {/literal}{if $optionms}{literal}
					 assignedTo = record.data['companycode'];
					 value =  loginType + '<br />' + assignedTo;	
				 {/literal}{else}{literal}
					 value = loginType;
				 {/literal}{/if}{literal}
			break;
			case TPX_LOGIN_COMPANY_ADMIN:
				loginType = "{/literal}{#str_LabelCompanyAdmin#}{literal}";
				{/literal}{if $optionms}{literal}
					assignedTo = record.data['companycode'] + ' - ' + record.data['usertypename'];
					value = loginType + '<br />' + assignedTo;
				{/literal}{else}{literal}
					value = loginType;
				{/literal}{/if}{literal}
			break;
			case TPX_LOGIN_SITE_ADMIN:
				loginType = "{/literal}{#str_LabelSiteAdmin#}{literal}";
				{/literal}{if $optionms}{literal}
					assignedTo = record.data['owner'] + ' - ' + record.data['usertypename'] ;
					value = loginType + '<br />' + assignedTo;
				{/literal}{else}{literal}
					value = loginType;
				{/literal}{/if}{literal}
			break;
			case TPX_LOGIN_CREATOR_ADMIN:
				value = "{/literal}{#str_LabelCreatorAdmin#}{literal}";
			break;
			case TPX_LOGIN_PRODUCTION_USER:
				loginType = "{/literal}{#str_LabelProductionUser#}{literal}";
				{/literal}{if $optionms}{literal}
					if (record.data['owner'] == '')
					{
						assignedTo = "{/literal}{#str_LabelNone#}{literal}";
					}
					else
					{
						assignedTo = record.data['owner'] + ' - ' + record.data['usertypename'];
					}
					value = loginType + '<br />' + assignedTo;
				{/literal}{else}{literal}
					value = loginType;
				{/literal}{/if}{literal}
			break;
			case TPX_LOGIN_DISTRIBUTION_CENTRE_USER:
				loginType = "{/literal}{#str_LabelDistributionCentreLogin#}{literal}";
				{/literal}{if $optionms || $optioncfs}{literal}
					assignedTo = record.data['owner'] + ' - ' + record.data['usertypename'];
					value = loginType + '<br />' + assignedTo;
				{/literal}{else}{literal}
					value = loginType;
				{/literal}{/if}{literal}
			break;
			case TPX_LOGIN_STORE_USER:
				loginType = "{/literal}{#str_LabelStoreUser#}{literal}";
				{/literal}{if $optionms || $optioncfs}{literal}
					assignedTo = record.data['owner'] + ' - ' + record.data['usertypename'];
					value = loginType + '<br />' + assignedTo;
				{/literal}{else}{literal}
					value = loginType;
				{/literal}{/if}{literal}
			break;
			case TPX_LOGIN_BRAND_OWNER:
				loginType = "{/literal}{#str_LabelBrandOwner#}{literal}";
				assignedTo = record.data['usertypename'];
				value = loginType + '<br />' + assignedTo;
			break;
			case TPX_LOGIN_API:
				 loginType = "{/literal}{#str_LabelAPILogin#}{literal}";
				 value = loginType;
			break;
			case TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER:
				loginType = "{/literal}{#str_LabelUnlockSystemAccountUser#}{literal}";
				value = loginType;
			break;
		}

		if (record.data.active == 0)
		{
			className =  'class = "inactive"';
			return '{/literal}<span '+className+'>'+value+'</span>{literal}';
		}
		else
		{
			return '{/literal}<span class="">'+value+'</span>{literal}';
		}	
	}

	function yesNoRenderer(value, p, record)
	{
		if (value == 0)
		{
			className =  'class = "inactive"';
			return '{/literal}<span '+className+">{#str_LabelInactive#}</span>{literal}";
		}
		else
		{
			return "{/literal}{#str_LabelActive#}{literal}";
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
		Ext.taopix.formPost(gMainWindowObj, serverParams, 'index.php?fsaction=AdminUsers.userActivate', "{/literal}{#str_MessageUpdating#}{literal}", activateCallback);
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
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminUsers.addDisplay&ref={/literal}{$ref}{literal}', '', '', 'initialize', false);
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
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminUsers.editDisplay&ref={/literal}{$ref}{literal}', serverParams, '', 'initialize', false);
		}
	}

	/* delete handler */	  
	function onDelete(btn, ev)
	{
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_ConfirmationDeleteUser#}{literal}", onDeleteResult);
	}

	function onDeleteResult(btn)
	{
		if (btn == "yes")
		{
			/* Reauthenticate the logged in user to make the changes */
			showAdminReauthDialogue(
			{
				ref: {/literal}{$ref}{literal},
				reason: 'USER-DELETE',
				title: Ext.taopix.ReauthenticationDialog.strings.titleAuthenticateToSave,
				success: function()
				{
					var paramArray = new Object();
					paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));

					Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminUsers.delete', "{/literal}{#str_MessageDeleting#}{literal}", onDeleteCallback);
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
			Ext.getCmp('hideInactiveButton').setTooltip('{/literal}{#str_TooltipShowInactiveItems#}{literal}');
		}
		else
		{
			Ext.getCmp('hideInactiveButton').setTooltip('{/literal}{#str_TooltipHideInactiveItems#}{literal}');			
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
			hideInactiveButton.setTooltip({text: '{/literal}{#str_TooltipHideInactiveItemsIsDisabledForSearchResults#}{literal}', autoHide: true, id: 'hideInactiveDisabledTooltip'});
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
					hideInactiveButton.setTooltip('{/literal}{#str_TooltipShowInactiveItems#}{literal}');					
				}
				else
				{
					hideInactiveButton.toggle(false);
					hideInactiveButton.setTooltip('{/literal}{#str_TooltipHideInactiveItems#}{literal}');					
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

{/literal}