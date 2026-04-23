<?php
/* Smarty version 4.5.3, created on 2026-03-06 03:48:10
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\customers\customers.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa4e7a6387a2_22664726',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3aa1206d118e2663db79fec4a25e065b18be76ea' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\customers\\customers.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aa4e7a6387a2_22664726 (Smarty_Internal_Template $_smarty_tpl) {
?>

function initialize(pParams)
{
	var sessionId = "<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
";
	var gridPageSize = 100;
	var lastLoggedInFilterDays = 30;
	var companyCode = '';
	var redactionCode = '';
	var redactionStatus = 0;
	customersEditWindowExists = false;

	gridDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		remoteGroup:true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminCustomers.customerList&ref=' + sessionId }),
		method:'POST',
		groupField:'headertext',
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([
				{name:'recordid', mapping: 0},
				{name: 'login', mapping: 1},
				{name: 'headertext', mapping: 2},
				{name: 'groupcode', mapping: 3},
				{name: 'accountcode', mapping: 4},
				{name: 'companyname', mapping: 5},
				{name: 'postcode', mapping: 6},
				{name: 'countryname', mapping: 7},
				{name: 'emailaddress', mapping: 8},
				{name: 'contactname', mapping: 9},
				{name: 'creditlimit', mapping: 10},
				{name: 'accountbalance', mapping: 11},
				{name: 'redactionprogress', mapping: 12},
				{name: 'lastlogindate', mapping: 13},
				{name: 'isactive', mapping: 14},
				{name: 'accountlocked', mapping: 15}
			])
		),
		sortInfo:{field: 'groupcode', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()},
		listeners:
		{
    		'beforeload':function(pStore, pOptions)
    		{
				var appGrid = Ext.getCmp('customersGrid');

				// only apply the filters on a refresh, if the grid is not displayed, do not filter the data
				if (appGrid)
				{
					var companyFilterObj = Ext.getCmp('companyFilter');
					var redactionStatusObj = Ext.getCmp('redactionFilter');

					if (companyFilterObj)
					{
						companyCode = companyFilterObj.getValue();
					}
					this.lastOptions.params['companyCode'] = companyCode;

					// get redaction status filter value
					var redactionStatusObj = Ext.getCmp('redactionFilter');
					if (redactionStatusObj)
					{
						redactionStatus = redactionStatusObj.getValue();
					}
					this.lastOptions.params['redactionStatus'] = redactionStatus;

					appGrid.store.setBaseParam('redactionStatus', redactionStatus);

					checkHideInactiveButton(pStore, pOptions);
				}
			}
    	}
	});
	gridDataStoreObj.load({params:{ companyCode: companyCode, start: 0, limit: gridPageSize, redactionStatus: redactionStatus, hideInactive: 0, lastloggedinfilterdays: lastLoggedInFilterDays, lastloggedinfilteron: 0}});

	gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj)
			{
				if (gridCheckBoxSelectionModelObj.getCount() > 0)
				{
					if (gridCheckBoxSelectionModelObj.getCount() == 1)
					{
						gridObj.editButton.enable();

						var itemID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('customersGrid'));
						var redactionprogress = gridCheckBoxSelectionModelObj.selections.map[itemID].data.redactionprogress;

						if (redactionprogress == 1)
						{
							gridObj.redactDeclineButton.enable();
							gridObj.redactButton.enable();
						}
						else if(redactionprogress == 0)
						{
							gridObj.redactButton.enable();
						}
					}
					else
					{
						gridObj.editButton.disable();
						gridObj.redactDeclineButton.disable();
						gridObj.redactButton.enable();
					}
					gridObj.activeButton.enable();
					gridObj.inactiveButton.enable();
					gridObj.deleteButton.enable();
				}
				else
				{
					gridObj.activeButton.disable();
					gridObj.inactiveButton.disable();
					gridObj.editButton.disable();
					gridObj.deleteButton.disable();
					gridObj.redactButton.disable();
					gridObj.redactDeclineButton.disable();
				}
			}
		}
	});

	var columnRenderer = function(value, p, record, rowIndex, colIndex, store)
	{
		var className = '';

		if (record.data.isactive == 0)
		{
			if (colIndex == 13) value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInactive');?>
";
			className = 'class = "inactive"'
		}
		else
		{
			if (colIndex == 13) value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
";
		}

		if (colIndex == 2)
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
		}

		if (colIndex == 12)
		{
			if (record.data.lastlogindate == '')
			{
				value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLastLoggedInUnknown');?>
";
			}
			else if (record.data.lastlogindate == 0)
			{
				value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLastLoggedInLessThan1Day');?>
";
			}
			else if (record.data.lastlogindate == 1)
			{
				value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLastLoggedInDayAgo');?>
".replace("^0", value);
			}
			else
			{
				value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLastLoggedInDaysAgo');?>
".replace("^0", value);
			}
		}

		if (colIndex == 11)
		{
			switch (Number(record.data.redactionprogress))
			{
				case 0:
				{
					value = "";
					break;
				}
				case 1: // TPX_REDACTION_REQUESTED
				{
					value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedactionRequested');?>
";
					break;
				}
				case 2: // TPX_REDACTION_DECLINED
				{
					value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedactionDeclined');?>
";
					break;
				}
				case 3: // TPX_REDACTION_AUTHORISED_BY_LICENSEE
				{
					value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedactionAuthorised');?>
";
					break;
				}
				case 4: // TPX_REDACTION_AUTHORISED_BY_USER
				{
					value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedactionAuthorised');?>
";
					break;
				}
				case 5: // TPX_REDACTION_QUEUED
				{
					value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedactionQueued');?>
";
					break;
				}
				case 6: // TPX_REDACTION_IN_PROGRESS
				{
					value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedactionInProgress');?>
";
					break;
				}
				case 7: // TPX_REDACTION_CONTROL_CENTRE
				{
					value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedactionInProgress');?>
";
					break;
				}
				case 8: // TPX_REDACTION_COMPLETE
				{
					value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedactionComplete');?>
";
					break;
				}
				case 99: // TPX_REDACTION_ERROR
				{
					value = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedactionError');?>
";
					break;
				}
			}
		}

		return '<span '+className+'>'+value+'</span>';
	};

	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {	sortable: true, resizable: true },
		columns: [gridCheckBoxSelectionModelObj,
	    	{ id:'headertext', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelBrand');?>
", dataIndex: 'headertext', hidden: true },
			Ext.taopix.buildUnlockAccountGridObj(columnRenderer, function()
			{
				var itemID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('customersGrid'));
				var accountLocked = gridCheckBoxSelectionModelObj.selections.map[itemID].data.accountlocked;

				if (accountLocked == 1)
				{
					var recordID = gridCheckBoxSelectionModelObj.selections.map[itemID].data.recordid;

					unlockAccount(recordID);
				}
			}),
	    	{ id: 'groupcode', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKey');?>
", dataIndex: 'groupcode', width:200, sortable: false, menuDisabled: true, renderer: columnRenderer},
        	{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAccountCode');?>
", dataIndex: 'accountcode', width:130, renderer: columnRenderer},
        	{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompanyName');?>
", dataIndex: 'companyname', width:200, renderer: columnRenderer},
        	{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
", dataIndex: 'contactname', width:200, renderer: columnRenderer},
        	{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPostCode');?>
", dataIndex: 'postcode', width:120, renderer: columnRenderer},
        	{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCountry');?>
", dataIndex: 'countryname', width:150, renderer: columnRenderer},
        	{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLoginID');?>
", dataIndex: 'login', width:175, renderer: columnRenderer},
        	{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailAddress');?>
", dataIndex: 'emailaddress', width:190, renderer: columnRenderer},
        	{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAccountStatus');?>
", dataIndex: 'redactionprogress', sortable: false, menuDisabled: true, renderer: columnRenderer, width:140},
        	{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLastLoggedIn');?>
", dataIndex: 'lastlogindate', width:120, sortable: false,  renderer: columnRenderer, menuDisabled: true},
        	{ header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
", dataIndex: 'isactive', renderer: columnRenderer, align: 'right', width:80}
		]
	});

	var onAdd = function()
	{
		var paramArray = [];

		if (!customersEditWindowExists)
		{
			customersEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminCustomers.addDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}
	};

	var onEdit = function()
	{
		var paramArray = {};
		paramArray['id'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('customersGrid'));

		if (!customersEditWindowExists)
		{
			customersEditWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminCustomers.editDisplay&ref='+sessionId, paramArray, '', 'initialize', false);
		}
	};

	onCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
			Ext.getCmp('dialog').close();
			gridDataStoreObj.reload();
		}
	};

	onDeleteCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
			gridDataStoreObj.reload();
		}
	};

	onRedactCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
			gridDataStoreObj.reload();

			gridObj.redactButton.disable();
			gridObj.redactDeclineButton.disable();
		}
	};

	onRedactDeclineCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
			gridDataStoreObj.reload();
		}
	};

	onActivateCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}

			gridDataStoreObj.reload();
		}
	};

	var onActivate = function(btn, ev)
	{
		var gridObj = gMainWindowObj.findById('customersGrid');
		var paramArray = {};
		var active = 0;

		if (btn.id == 'activeButton') 
		{
			active = 1;
		}
		
		paramArray['active'] = active;
		paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gridObj);

		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminCustomers.customerActivate', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageUpdating');?>
", onActivateCallback);
	};

	var onExportCustomers = function(btn, ev)
	{
		Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminCustomers.exportDisplay&ref='+sessionId, [], '', 'initialize', false);
	}

	var onDelete = function(btn, ev)
	{
		var onDeleteConfirmed = function(btn)
		{
			if (btn == "yes") 
			{
				/* Reauthenticate the logged in user to make the changes */
				showAdminReauthDialogue(
				{
					ref: <?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
,
					reason: 'CUSTOMER-DELETE',
					title: Ext.taopix.ReauthenticationDialog.strings.titleAuthenticateToSave,
					success: function()
					{
						var paramArray = {};
						paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('customersGrid'));

						Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminCustomers.delete', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleting');?>
", onDeleteCallback);
					}
				});
			}
		};

		var gridObj = gMainWindowObj.findById('customersGrid');
		var selRecords = gridObj.selModel.getSelections();
		var codeList = [];

		for (var rec = 0; rec < selRecords.length; rec++) {	codeList.push("'"+selRecords[rec].data.contactname+"'");}
		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DeleteConfirmation');?>
".replace("'^0'", codeList.join(', ')), onDeleteConfirmed);
	};


	var onRedact = function(btn, ev)
	{
		var onRedactConfirmed = function(btn)
		{
			if (btn == "yes")
			{
				/* Reauthenticate the logged in user to make the changes */
				showAdminReauthDialogue(
				{
					ref: <?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
,
					reason: 'CUSTOMER-REDACT',
					title: Ext.taopix.ReauthenticationDialog.strings.titleAuthenticateToSave,
					success: function()
					{
						var paramArray = {};
						paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('customersGrid'));
						Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminCustomers.redact', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleting');?>
", onRedactCallback);
					}
				});
			}
		};

		var gridObj = gMainWindowObj.findById('customersGrid');
		var selRecords = gridObj.selModel.getSelections();
		var codeList = [];

		for (var rec = 0; rec < selRecords.length; rec++) {	codeList.push("'"+selRecords[rec].data.contactname+"'");}
		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_RedactionConfirmation');?>
".replace("'^0'", codeList.join(', ')), onRedactConfirmed);
	};

	var onRedactDecline = function(btn, ev)
	{
		var onRedactDeclineConfirmed = function(btn)
		{
			if (btn == "yes")
			{
				var paramArray = {};
				paramArray['idlist'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('customersGrid'));
				Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminCustomers.redactdecline', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleting');?>
", onRedactDeclineCallback);
			}
		};

		var gridObj = gMainWindowObj.findById('customersGrid');
		var selRecords = gridObj.selModel.getSelections();
		var codeList = [];

		for (var rec = 0; rec < selRecords.length; rec++) {	codeList.push("'"+selRecords[rec].data.contactname+"'");}
		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_RedactionDeclineConfirmation');?>
".replace("'^0'", codeList.join(', ')), onRedactDeclineConfirmed);
	};

	function onCompanyChange()
	{
		var appGrid = Ext.getCmp('customersGrid');
		var redactionStatusObj = Ext.getCmp('redactionFilter');

		// get company filter value
		var companyFilterObj = Ext.getCmp('companyFilter');
		if (companyFilterObj)
		{
			companyCode = companyFilterObj.getValue();
		}
		appGrid.store.lastOptions.params['companyCode'] = companyCode;

		// get redaction status filter value
		var redactionStatusObj = Ext.getCmp('redactionFilter');
		if (redactionStatusObj)
		{
			redactionStatus = redactionStatusObj.getValue();
		}

		appGrid.store.lastOptions.params['redactionStatus'] = redactionStatus;

		appGrid.store.reload({params: appGrid.store.lastOptions.params});
	}

	function onHideInactive(btn, evn)
	{
		var gridDataStore = Ext.getCmp('customersGrid').store;
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

		pParams.lastloggedinfilterdays = lastLoggedInFilterDays;

		if (Ext.getCmp('filtermenucheckitem').checked)
		{
			pParams.lastloggedinfilteron = 1;
		}
		else
		{
			pParams.lastloggedinfilteron = 0;
		}
	}

	function openEditUserLastLoggedInFilter()
	{
		var filterPanel = new Ext.FormPanel(
		{
			id: 'filterpanel',
			labelAlign: 'left',
			frame: true,
			layout: 'form',
			bodyStyle: 'padding-left: 5px;',
			items: [
				new Ext.Container(
				{
					html: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowUsersWhoHaveNotLoggedInFor');?>
",
					style:
					{
						marginBottom: '5px'
					}
				}),
				{
					xtype: 'compositefield',
					boxMinHeight: 30,
					hideLabel: true,
					items: [
						{
							xtype: 'numberfield',
							id: 'lastloggedinfilterdays',
							name: 'lastloggedinfilterdays',
							post: true,
							validateOnBlur: true,
							width: 50,
							allowBlank: false,
							allowNegative: false,
							allowDecimals: false,
							maxValue: 3650,
							hideLabel: true,
							value: lastLoggedInFilterDays,
							style:
							{
								marginTop: '0px'
							}
						},
						new Ext.Container(
						{
							html: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDays');?>
",
							style:
							{
								marginTop: '5px'
							}
						})
					]
				}
			]
		});

		var editUserLastLoggedInFilterWindow = new Ext.Window(
		{
			id: 'editUserLastLoggedInFilterWindow',
			closable: false,
			plain: true,
			modal: true,
			draggable: true,
			resizable: false,
			title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowUsersLastLoggedEditFilter');?>
",
			items: [
				filterPanel
			],
			buttons:
			[
				{
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
",
					handler: function(btn, ev)
					{
						Ext.getCmp('editUserLastLoggedInFilterWindow').close();
					},
					cls: 'x-btn-right'
				},
				{
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUpdateFilter');?>
",
					handler: function(btn, ev)
					{

						if (Ext.getCmp('lastloggedinfilterdays').isValid())
						{
							lastLoggedInFilterDays = Ext.getCmp('lastloggedinfilterdays').value;

							gridDataStoreObj.lastOptions.params['lastloggedinfilterdays'] = lastLoggedInFilterDays;

							if (Ext.getCmp('filtermenucheckitem').checked)
							{
								gridDataStoreObj.lastOptions.params['lastloggedinfilteron'] = 1;
							}
							else
							{
								gridDataStoreObj.lastOptions.params['lastloggedinfilteron'] = 0;
							}

							gridDataStoreObj.reload({params: gridDataStoreObj.lastOptions.params});

							Ext.getCmp('filtermenucheckitem').setText("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowUsersLastLoggedInMoreThanDays');?>
".replace("^0", Ext.getCmp('lastloggedinfilterdays').value));
							Ext.getCmp('editUserLastLoggedInFilterWindow').close();
						}
					},
					cls: 'x-btn-right'
				}
			],
			width: 400
		});
		editUserLastLoggedInFilterWindow.show();
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
				var gridDataStore = Ext.getCmp('customersGrid').store;

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


	var redactionCombo = new Ext.form.ComboBox({
		id: 'redactionFilter',
		name: 'redactionFilter',
		typeAhead: true,
		triggerAction: 'all',
		lazyRender: true,
		mode: 'local',
		store: new Ext.data.ArrayStore({
			id: 0,
			fields: ['redactID', 'displayText'],
			data: [
				[0, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ShowAll');?>
"],
				[1, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedactionRequested');?>
"],
				[2, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedactionDeclined');?>
"],
				[3, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedactionAuthorised');?>
"],
				[5, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedactionQueued');?>
"],
				[6, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedactionInProgress');?>
"],
				[8, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedactionComplete');?>
"],
				[99, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedactionError');?>
"]
			]
		}),
		valueField: 'redactID',
		displayField: 'displayText',
		listeners: {
			'select': onCompanyChange,
			'render':function()
			{
				this.setValue(0);
            }
        }
	});

	gridObj = new Ext.grid.GridPanel({
   		id: 'customersGrid',
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
		columnLines:true,
    	stateId: 'customersGrid',
    	ctCls: 'grid',
    	tbar: [	{ref: '../addButton',	text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
",	iconCls: 'silk-add',	handler: onAdd	}, '-',
            {ref: '../editButton',	text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonEdit');?>
",	iconCls: 'silk-pencil',	handler: onEdit, disabled: true	}, '-',
           	{ref: '../deleteButton', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDelete');?>
", iconCls: 'silk-delete', handler: onDelete, disabled: true, id:'deleteButton' }, '-',
           	{ref: '../redactButton', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonRedact');?>
", iconCls: 'silk-disconnect', handler: onRedact, disabled: true, id:'redactButton' }, '-',
           	{ref: '../redactDeclineButton', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDeclineRedact');?>
", iconCls: 'silk-disconnect', handler: onRedactDecline, disabled: true, id:'redactDeclineButton' }, '-',
            {ref: '../activeButton', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeActive');?>
", iconCls: 'silk-lightbulb', handler: onActivate, disabled: true, id:'activeButton'}, '-',
      	    {ref: '../inactiveButton', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeInactive');?>
", iconCls: 'silk-lightbulb-off',  handler: onActivate, disabled: true, id:'inactiveButton'}
			<?php if ($_smarty_tpl->tpl_vars['userType']->value == 0 || $_smarty_tpl->tpl_vars['userType']->value == 1) {?>, '-',{ref: '../exportCustomers', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelExportCustomers');?>
", iconCls: 'silk-database-go', handler: onExportCustomers, id: 'exportCustomers'}<?php }?>
			,{xtype:'tbfill'}
			, new Ext.form.Label({text:'<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelFilterByStatus');?>
'})
        	,{xtype: 'tbspacer', width: 10}
			,redactionCombo
			<?php if ($_smarty_tpl->tpl_vars['optionMS']->value == true && $_smarty_tpl->tpl_vars['userType']->value == 0) {?>
        	,'-',{xtype: 'tbspacer', width: 10}
			, new Ext.form.Label({text:'<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelFilterByCompany');?>
'})
        	,{xtype: 'tbspacer', width: 10}
        	,new Ext.taopix.CompanyCombo({id:'companyFilter',name: 'companyFilter', emptyText:"<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompanyName');?>
", options: {ref: sessionId, includeGlobal: '1', includeShowAll:'1', onchange: onCompanyChange} })
        	<?php }?>
        	,{xtype: 'tbspacer', width: 10}
			,{
				id:'lastloggedinfilterbutton',
				ref: '../lastloggedinfilterbutton',
				iconCls: 'silk-status-away',
				xtype: 'splitbutton',
				tooltip: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelFilterLastLoginToolTip');?>
",
				menu: new Ext.menu.Menu(
				{
					id: 'lastloggedinfiltermenu',
					items:
					[
						{
							text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowUsersLastLoggedInMoreThanDays');?>
".replace("^0", lastLoggedInFilterDays),
							ref: '../filterlastlogincheckitem',
							id: 'filtermenucheckitem',
							name: 'filtermenucheckitem',
							xtype: 'menucheckitem',
							cls: 'menucheckitemfix',
							listeners: {
								checkchange: function(checkbox, checked)
								{
									gridDataStoreObj.lastOptions.params['lastloggedinfilterdays'] = lastLoggedInFilterDays;

									if (checked)
									{
										Ext.getCmp('lastloggedinfilterbutton').toggle(true);
										gridDataStoreObj.lastOptions.params['lastloggedinfilteron'] = 1;
									}
									else
									{
										Ext.getCmp('lastloggedinfilterbutton').toggle(false);
										gridDataStoreObj.lastOptions.params['lastloggedinfilteron'] = 0;
									}

									gridDataStoreObj.reload({params: gridDataStoreObj.lastOptions.params});
								}
							}
						},
						{
							text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowUsersLastLoggedEditFilter');?>
",
							iconCls: '',
							ref: '../previewmanage',
							handler: openEditUserLastLoggedInFilter
						}
					]
				})
			}
			,{xtype: 'tbspacer', width: 10}
			,{
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
		plugins: [
			new Ext.ux.grid.Search({
				iconCls: 'silk-zoom',
				minChars: 3,
				width: 200,
				autoFocus: true,
				disableIndexes:['recordid', 'accountlocked', 'groupcode','companyname', 'countryname', 'creditlimit', 'accountbalance', 'isactive', 'lastlogindate', 'redactionprogress']
			})
		],
		bbar: new Ext.PagingToolbar({ pageSize: gridPageSize, store: gridDataStoreObj, displayInfo: true, listeners: {beforechange: carryHideInactiveIntoPagingToolbarRefresh} })
	});

	function unlockAccount(pRecordID)
	{
		var gridObj = gMainWindowObj.findById('customersGrid');
		var selRecords = gridObj.selModel.getSelections();
		var userList = [];
		for (var rec = 0; rec < selRecords.length; rec++) {	userList.push("'"+selRecords[rec].data.contactname+"'");}

		var msg = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_UnlockCustomer');?>
".replace('^0', userList.join(','));

		Ext.taopix.unlockAccount(<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
, pRecordID, "CUSTOMER-UNLOCK", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePleaseReenterYourAdministratorPasswordToUnlockCustomerAccounts');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageUnlocking');?>
", msg, gMainWindowObj, gridDataStoreObj)
	};

	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleCustomers');?>
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
}

<?php }
}
