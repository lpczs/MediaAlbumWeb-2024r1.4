<?php
/* Smarty version 4.5.3, created on 2026-04-08 07:20:21
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\autoupdate\autoupdatelicensekeys.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69d601b5542ab0_02322379',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'de8a976ad7493f798ec8949e8328ca691e464689' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\autoupdate\\autoupdatelicensekeys.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69d601b5542ab0_02322379 (Smarty_Internal_Template $_smarty_tpl) {
?>

function initialize(pParams)
{
	
	var licenceUpdateTitle = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_AutoUpdateTitleLicenseKeys');?>
";
	var licenceCodeTitle_tx = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCode');?>
";
	var licenceNameTitle_tx = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelName');?>
";
	var licenceFilenameTitle_tx = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelFileName');?>
";
	var licenceVersionTitle_tx = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelVersion');?>
";
	var licenceBrandingTitle_tx = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleBranding');?>
";
	var licenceCurrencyTitle_tx = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCurrencyCode');?>
";
	var licenceActiveTitle_tx = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
";
	var savingLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
";
	var addLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonEdit');?>
";
	var buttonDelete_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRemove');?>
";
	var delteLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
";
	var delteConformation_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DeleteLicenseKeyConfirmation');?>
";
	var deletingLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleting');?>
"; 
	var activatingLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_Activating');?>
";
	var deactivatingLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_Deactivating');?>
";
	var changingPriorityLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageChangingPriority');?>
";
	var savingLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
";
	var licenseKeyUsed_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_WarningUsedLicenseKey');?>
";
	var deleteContinue_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_WarningContinue');?>
";
	var deleteSuccess_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_DeleteLicenseKey');?>
";
	var errorLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
";
	var addressVerificationFailedLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAddressVerificationFailed');?>
";
	var statusLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
";
	var errorConnectLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorConnectFailure');?>
";
	var enableGroupingLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnableGrouping');?>
";
	var activeLabel_tx ="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
"; 
	var enabledLabel_tx = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnabled');?>
";
	var disabledLabel_tx = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisabled');?>
";
	var inactivelabel_tx = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInactive');?>
";
	var editLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleEditLicenseKey');?>
";
	var buttonCancel_tx ="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
"; 
	var makeInactiveLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeInactive');?>
"; 
	var makeActiveLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeActive');?>
"; 
	var statusLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
";
	var globalLabel = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_Global');?>
";
	var telephoneTitle_tx ="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelTelephoneNumber');?>
";
	var emailTitle_tx ="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailAddress');?>
";
	var retypeemailTitle_tx ="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRetypeEmailAddress');?>
";
	var priorityLabel_txt ="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPriority');?>
";
	var priorityNormalLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPriorityNormal');?>
";
	var priorityCriticalLabel_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPriorityCritical');?>
";
	var selectCompany_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSelectCompany');?>
";
	var closeWindow_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCloseWindow');?>
";
	var availableOnline_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAvailableOnline');?>
";
	var enableOnline_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnableOnline');?>
";
	var disableOnline_txt = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDisableOnline');?>
";

	var session_id = "<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
";
	var gridPageSize = 100;

	var userType = "<?php echo $_smarty_tpl->tpl_vars['userType']->value;?>
";
	var optionMS = "<?php echo $_smarty_tpl->tpl_vars['optionMS']->value;?>
";
	var companyCode = "<?php echo $_smarty_tpl->tpl_vars['companyCode']->value;?>
";
	licenseKeyEditWindowExists = false;
	

	var clearAllInvalid = function(formId)
	{
		Ext.getCmp(formId).getForm().items.each(function(item){ if (item.clearInvalid) item.clearInvalid(); });
	};
	
	function onDeleteCallback(pUpdated, pTheForm, pActionData) {
		if(pUpdated){ Ext.MessageBox.show({ title: delteLabel_txt, msg: deleteSuccess_txt, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO });  grid.store.reload();} else { Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.ERROR });}
	};

	var deleteRecords = function(btn)
	{
		var paramArray = new Object();
		var selRecords = gridCheckBoxSelectionModelObj.getSelections();
		var ids = new Array();	var codes = new Array();

		for (var rec = 0; rec < selRecords.length; rec++) { if(selRecords[rec].data.canDelete>0){ids.push(selRecords[rec].data.id);	codes.push(selRecords[rec].data.groupcode);} }
		var iDList = ids.join(','); var codeList = codes.join(',');
		paramArray['idlist'] = iDList;  paramArray['codelist'] = codeList;

		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminAutoUpdate.deleteLicenseKey', deletingLabel_txt, onDeleteCallback);	
	};

	var deleteRecordsYes = function(btn)
	{ 
		if (btn == "yes") 
		{	
			deleteRecords();
		}
	};

	var onDeleteResult = function(btn) 
	{
		if (btn == "yes") {
			var selRecords = gridCheckBoxSelectionModelObj.getSelections();
			var warningMes = "";
			var cantDeleteCount = 0;
			for (var rec = 0; rec < selRecords.length; rec++) {	if(selRecords[rec].data.canDelete==0){ warningMes+='<br>'+selRecords[rec].data.groupcode; cantDeleteCount++; }}
			if(warningMes!='') 
			{
				if (cantDeleteCount == selRecords.length)
				{
					Ext.MessageBox.show({ title: delteLabel_txt, msg: licenseKeyUsed_txt + ":"+warningMes, buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
				}
				else
				{
					Ext.MessageBox.confirm(delteLabel_txt, licenseKeyUsed_txt + ":"+warningMes + '<br><br>'+deleteContinue_txt, deleteRecordsYes);
				}
			}
			else 
			{
				deleteRecords();
			}
		}
	};
	
	var onDelete = function(btn, ev)
	{ 
		Ext.MessageBox.confirm(delteLabel_txt, delteConformation_txt, onDeleteResult); 
	};

	function isActiveColumnRenderer(value, p, record, rowIndex, colIndex, store) 
	{
		var className = '';
		if (record.data.active == 0) 
		{
			className = 'class = "inactive"'
		}
		return '<span '+className+'>'+value+'</span>';
	}

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

	function activeColumnRenderer(value, p, record, rowIndex, colIndex, store) 
	{
		var value = '', className = '';
		if (record.data.active == 0) 
		{
			value = inactivelabel_tx; className = 'class = "inactive"';
		}	
		else 
		{
			value = activeLabel_tx;
		}
		return '<span '+className+'>'+value+'</span>';
	};
	
	function onlineActiveColumnRenderer(value, p, record, rowIndex, colIndex, store) 
	{
		var value = '', className = '';
		
		if (record.data.active == 0) 
		{
			if (record.data.availableonline == 0) 
			{
				value = disabledLabel_tx;
			}	
			else 
			{
				value = enabledLabel_tx;
			}
			
			className = 'class = "inactive"';
			return '<span '+className+'>'+value+'</span>';
		}
		else
		{
			if (record.data.availableonline == 0) 
			{
				value = disabledLabel_tx;
			}	
			else 
			{
				value = enabledLabel_tx;
			}
			return '<span '+className+'>'+value+'</span>';
		}
	};

	function onActivateCallback(pUpdated, pTheForm, pActionData) 
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
	
	function companyColumnRenderer(value, p, record, rowIndex, colIndex, store)
	{
		var value = '', className = '';
		if (record.data.company == '') 
		{
			return globalLabel;
		}	
		else 
		{
			return record.data.company;
		}
	};

	var onActivate = function(btn, ev)
	{  
		var maskText = deactivatingLabel_txt;
		var command = 0;
		if(btn.id == 'activeButton') {maskText = activatingLabel_txt; command = 1;}
		
		var paramArray = new Object();
		var selRecords = gridCheckBoxSelectionModelObj.getSelections();
		var ids = new Array();	

		for (var rec = 0; rec < selRecords.length; rec++) { ids.push(selRecords[rec].data.id); }
		var iDList = ids.join(','); paramArray['idlist'] = iDList;  paramArray['command'] = command;

		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminAutoUpdate.activateLicenseKey', maskText, onActivateCallback);
	};
	
	var onActivateOnline = function(btn, ev)
	{  
		var maskText = deactivatingLabel_txt;
		var command = 0;
		if (btn.id == 'onlineActiveButton') {maskText = activatingLabel_txt; command = 1;}
		
		var paramArray = new Object();
		var selRecords = gridCheckBoxSelectionModelObj.getSelections();
		var ids = new Array();	

		for (var rec = 0; rec < selRecords.length; rec++) 
		{ 
			ids.push(selRecords[rec].data.id); 
		}

		var iDList = ids.join(','); paramArray['idlist'] = iDList;  paramArray['command'] = command;

		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminAutoUpdate.activateLicenseKeyOnline', maskText, onActivateCallback);
	};
	
	var changePriority = function(btn, ev)
	{  
		var maskText = changingPriorityLabel_txt;
		var command = 0;
		if(btn.id == 'highPriorityButton') {maskText = changingPriorityLabel_txt; command = 1000;}
		
		var paramArray = new Object();
		var selRecords = gridCheckBoxSelectionModelObj.getSelections();
		var ids = new Array();	

		for (var rec = 0; rec < selRecords.length; rec++) { ids.push(selRecords[rec].data.id); }
		var iDList = ids.join(','); paramArray['idlist'] = iDList;  paramArray['command'] = command;

		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminAutoUpdate.changeLicenseKeyPriority', maskText, onActivateCallback);
	};

	var onEdit = function(btn, ev)
	{ 
		var serverParams = new Object();
		var id = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('licenceUpdateGrid'));
		serverParams['id'] = id;
		
		if (!licenseKeyEditWindowExists)
		{
			licenseKeyEditWindowExists = true;
		
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminAutoUpdate.editLicenseKeyDisplay&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
		}
	};

	function onHideInactive(btn, ev)
	{
		// get the datastore and default hide inactive to false
		var gridDataStore = Ext.getCmp('licenceUpdateGrid').store;
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
					var gridDataStore = Ext.getCmp('licenceUpdateGrid').store;

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

	gridDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		remoteGroup:true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminAutoUpdate.listLicenseKeys&ref='+session_id }),
		method:'POST',
		groupField:'company',
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([ 
				<?php if ($_smarty_tpl->tpl_vars['optiondesol']->value == true && $_smarty_tpl->tpl_vars['optiondesdt']->value == true) {?>
					{name: 'id', mapping: 0},
					{name: 'canDelete', mapping: 1},
					{name: 'groupcode', mapping: 2},
					{name: 'groupname', mapping: 3}, 
					{name: 'filename', mapping: 4}, 
					{name: 'version', mapping: 5},  
					{name: 'webbrandcode', mapping: 6},  
					{name: 'currencycode', mapping: 7}, 
					{name: 'availableonline', mapping: 8},
					{name: 'active', mapping: 9},
					{name: 'priority', mapping: 10},
					{name: 'company', mapping: 11}   
				<?php } elseif ($_smarty_tpl->tpl_vars['optiondesol']->value == true) {?>
					{name: 'id', mapping: 0},
					{name: 'canDelete', mapping: 1},
					{name: 'groupcode', mapping: 2},
					{name: 'groupname', mapping: 3}, 
					{name: 'filename', mapping: 4}, 
					{name: 'version', mapping: 5},  
					{name: 'webbrandcode', mapping: 6},  
					{name: 'currencycode', mapping: 7}, 
					{name: 'availableonline', mapping: 8},
					{name: 'priority', mapping: 9},
					{name: 'company', mapping: 10} 
				<?php } elseif ($_smarty_tpl->tpl_vars['optiondesdt']->value == true) {?>
					{name: 'id', mapping: 0},
					{name: 'canDelete', mapping: 1},
					{name: 'groupcode', mapping: 2},
					{name: 'groupname', mapping: 3}, 
					{name: 'filename', mapping: 4}, 
					{name: 'version', mapping: 5},  
					{name: 'webbrandcode', mapping: 6},  
					{name: 'currencycode', mapping: 7}, 
					{name: 'active', mapping: 8},
					{name: 'priority', mapping: 9},
					{name: 'company', mapping: 10} 
				<?php }?>
			])
		),
		sortInfo:{field: 'groupcode', direction: "ASC"},
		listeners:{
        	'beforeload':function(pStore, pOptions){ 
				var companyFilterCmb = Ext.getCmp('companyFilter');
				var lkeyGrid = Ext.getCmp('licenceUpdateGrid');
				if(companyFilterCmb)
				{
					companyCode = companyFilterCmb.getValue();
					lkeyGrid.store.lastOptions.params['companyCode'] = companyCode;
					lkeyGrid.store.setBaseParam('companyCode', companyCode);
				}

				checkHideInactiveButton(pStore, pOptions);
        	}
        },
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	}); gridDataStoreObj.load({params:{start:0, limit:gridPageSize}});
	
	gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({ 
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj) { 
				if (gridCheckBoxSelectionModelObj.getCount() > 0){
					if (gridCheckBoxSelectionModelObj.getCount() == 1)
					{ 
						grid.editButton.enable(); 
						
						 <?php if ($_smarty_tpl->tpl_vars['optiondesdt']->value == true) {?>
						 	grid.activeButton.enable(); 
						 	grid.inactiveButton.enable(); 
						 <?php }?>
						 
						 <?php if ($_smarty_tpl->tpl_vars['optiondesol']->value == true) {?>
						 	grid.onlineActiveButton.enable(); 
						 	grid.onlineInactiveButton.enable();  
						 <?php }?>
						 
						 grid.deleteButton.enable(); 
						 grid.highPriorityButton.enable(); 
						 grid.lowPriorityButton.enable();
					}
					else
					{
						 grid.editButton.disable(); 
						 
						 <?php if ($_smarty_tpl->tpl_vars['optiondesdt']->value == true) {?>
						 	grid.activeButton.enable(); 
						 	grid.inactiveButton.enable(); 
						 <?php }?>
						 
						 <?php if ($_smarty_tpl->tpl_vars['optiondesol']->value == true) {?>
						 	grid.onlineActiveButton.enable(); 
						 	grid.onlineInactiveButton.enable();  
						 <?php }?>
						 
						 grid.deleteButton.enable(); 
						 grid.highPriorityButton.enable(); 
						 grid.lowPriorityButton.enable();
					}
				} 
				else 
				{ 
					grid.editButton.disable();  
					
					<?php if ($_smarty_tpl->tpl_vars['optiondesdt']->value == true) {?>
						grid.activeButton.disable(); 
						grid.inactiveButton.disable(); 
					<?php }?>
					
					<?php if ($_smarty_tpl->tpl_vars['optiondesol']->value == true) {?>
						grid.onlineActiveButton.disable(); 
						grid.onlineInactiveButton.disable(); 
					<?php }?>
					
					grid.deleteButton.disable(); 
					grid.highPriorityButton.disable(); 
					grid.lowPriorityButton.disable(); 
				}
			}
		}
	});

	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {	sortable: true, resizable: true },
		columns: [gridCheckBoxSelectionModelObj,
			<?php if ($_smarty_tpl->tpl_vars['optiondesol']->value == true && $_smarty_tpl->tpl_vars['optiondesdt']->value == true) {?>
				{id:'id', header: licenceCodeTitle_tx, dataIndex: 'groupcode', width:200, renderer: isActiveColumnRenderer},
            	{id:'groupname',header: licenceNameTitle_tx, dataIndex: 'groupname', width:200, renderer: isActiveColumnRenderer},
            	{header: licenceFilenameTitle_tx, dataIndex: 'filename', width:150, renderer: isActiveColumnRenderer},
            	{header: licenceVersionTitle_tx, dataIndex: 'version', width:120, renderer: isActiveColumnRenderer, align:'right'},
            	{header: licenceBrandingTitle_tx, dataIndex: 'webbrandcode', width:180, renderer: isActiveColumnRenderer},
            	{header: licenceCurrencyTitle_tx, dataIndex: 'currencycode', width:120, renderer: isActiveColumnRenderer},
            	{header: availableOnline_txt, dataIndex: 'availableonline', width:80, renderer: onlineActiveColumnRenderer, align:'right'},
            	{header: statusLabel_txt, dataIndex: 'active', width:80, renderer: activeColumnRenderer, align:'right'},
            	{header: priorityLabel_txt, dataIndex: 'priority', width:80, renderer: priorityRenderer, align:'right'},
        		{id:'company', header: 'Company', dataIndex: 'company', hidden:true, renderer: companyColumnRenderer}
			<?php } elseif ($_smarty_tpl->tpl_vars['optiondesol']->value == true) {?>
				{id:'id', header: licenceCodeTitle_tx, dataIndex: 'groupcode', width:200, renderer: isActiveColumnRenderer},
            	{id:'groupname',header: licenceNameTitle_tx, dataIndex: 'groupname', width:200, renderer: isActiveColumnRenderer},
            	{header: licenceFilenameTitle_tx, dataIndex: 'filename', width:150, renderer: isActiveColumnRenderer},
            	{header: licenceVersionTitle_tx, dataIndex: 'version', width:120, renderer: isActiveColumnRenderer, align:'right'},
            	{header: licenceBrandingTitle_tx, dataIndex: 'webbrandcode', width:180, renderer: isActiveColumnRenderer},
            	{header: licenceCurrencyTitle_tx, dataIndex: 'currencycode', width:120, renderer: isActiveColumnRenderer},
            	{header: availableOnline_txt, dataIndex: 'availableonline', width:80, renderer: onlineActiveColumnRenderer, align:'right'},
            	{header: priorityLabel_txt, dataIndex: 'priority', width:80, renderer: priorityRenderer, align:'right'},
        		{id:'company', header: 'Company', dataIndex: 'company', hidden:true, renderer: companyColumnRenderer}
			<?php } elseif ($_smarty_tpl->tpl_vars['optiondesdt']->value == true) {?>
				{id:'id', header: licenceCodeTitle_tx, dataIndex: 'groupcode', width:200, renderer: isActiveColumnRenderer},
            	{id:'groupname',header: licenceNameTitle_tx, dataIndex: 'groupname', width:200, renderer: isActiveColumnRenderer},
            	{header: licenceFilenameTitle_tx, dataIndex: 'filename', width:150, renderer: isActiveColumnRenderer},
            	{header: licenceVersionTitle_tx, dataIndex: 'version', width:120, renderer: isActiveColumnRenderer, align:'right'},
            	{header: licenceBrandingTitle_tx, dataIndex: 'webbrandcode', width:180, renderer: isActiveColumnRenderer},
            	{header: licenceCurrencyTitle_tx, dataIndex: 'currencycode', width:120, renderer: isActiveColumnRenderer},
            	{header: statusLabel_txt, dataIndex: 'active', width:80, renderer: activeColumnRenderer, align:'right'},
            	{header: priorityLabel_txt, dataIndex: 'priority', width:80, renderer: priorityRenderer, align:'right'},
        		{id:'company', header: 'Company', dataIndex: 'company', hidden:true, renderer: companyColumnRenderer}
			<?php }?>
        ]
	});

	function clearGrouping(v){ if(v.checked) gridDataStoreObj.groupBy('company'); else gridDataStoreObj.clearGrouping(); }

	var onCompanyChange = function()
	{
		var licenseGrid = Ext.getCmp('licenceUpdateGrid');
		var companyFilterObj = Ext.getCmp('companyFilter');

		if (companyFilterObj)
		{
			companyCode = companyFilterObj.getValue();
		}
		licenseGrid.store.lastOptions.params['companyCode'] = companyCode;

		licenseGrid.store.reload({params: licenseGrid.store.lastOptions.params});		
	};
	
	grid = new Ext.grid.GridPanel({
    	id: 'licenceUpdateGrid',
    	store: gridDataStoreObj,
        selModel: gridCheckBoxSelectionModelObj,
        cm: gridColumnModelObj,
        stripeRows: true,
        height: 380, width:900, 
        stateful: true,
        enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		trackMouseOver:false,
		ctCls: 'grid',
		<?php if ($_smarty_tpl->tpl_vars['optionMS']->value == true) {?> 
		view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItems');?>
" : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItem');?>
"]})' }),
		<?php }?>
		autoExpandColumn: 'groupname',
		columnLines:true,
        stateId: 'licenceUpdateGrid',
        tbar: [	
        		<?php if ($_smarty_tpl->tpl_vars['optiondesol']->value == true && $_smarty_tpl->tpl_vars['optiondesdt']->value == true) {?>
        			{ref: '../editButton',	text: addLabel_txt,	iconCls: 'silk-pencil',	handler: onEdit, disabled: true	}, '-',
               		{ref: '../deleteButton', text: buttonDelete_txt, iconCls: 'silk-delete', handler: onDelete, disabled: true, id:'deleteButton' }, '-',
                	{ref: '../activeButton', iconCls: 'silk-lightbulb', text: makeActiveLabel_txt, handler: onActivate, disabled: true, id:'activeButton'}, '-', 
        	    	{id:'inactiveButton', iconCls: 'silk-lightbulb-off', ref: '../inactiveButton', text: makeInactiveLabel_txt, handler: onActivate, disabled: true}, '-',
        	    	{id:'onlineActiveButton', iconCls: 'silk-lightbulb', ref: '../onlineActiveButton', text: enableOnline_txt, handler: onActivateOnline,  disabled: true },	'-',
        	    	{id:'onlineinactiveButton', iconCls: 'silk-lightbulb-off', ref: '../onlineInactiveButton', text: disableOnline_txt, handler: onActivateOnline, disabled: true }, '-',
        	    	{id:'highPriorityButton', iconCls: 'silk-flag-red', ref: '../highPriorityButton', text: priorityCriticalLabel_txt, handler: changePriority, disabled: true}, '-',
        	    	{id:'lowPriorityButton', iconCls: 'silk-flag-green', ref: '../lowPriorityButton', text: priorityNormalLabel_txt, handler: changePriority, disabled: true}
        		<?php } elseif ($_smarty_tpl->tpl_vars['optiondesol']->value == true) {?>
        			{ref: '../editButton',	text: addLabel_txt,	iconCls: 'silk-pencil',	handler: onEdit, disabled: true	}, '-',
               		{ref: '../deleteButton', text: buttonDelete_txt, iconCls: 'silk-delete', handler: onDelete, disabled: true, id:'deleteButton' }, '-',
        	    	{id:'onlineActiveButton', iconCls: 'silk-lightbulb', ref: '../onlineActiveButton', text: enableOnline_txt, handler: onActivateOnline,  disabled: true },	'-',
        	    	{id:'onlineinactiveButton', iconCls: 'silk-lightbulb-off', ref: '../onlineInactiveButton', text: disableOnline_txt, handler: onActivateOnline, disabled: true }, '-',
        	    	{id:'highPriorityButton', iconCls: 'silk-flag-red', ref: '../highPriorityButton', text: priorityCriticalLabel_txt, handler: changePriority, disabled: true}, '-',
        	    	{id:'lowPriorityButton', iconCls: 'silk-flag-green', ref: '../lowPriorityButton', text: priorityNormalLabel_txt, handler: changePriority, disabled: true}
        		<?php } elseif ($_smarty_tpl->tpl_vars['optiondesdt']->value == true) {?>
        			{ref: '../editButton',	text: addLabel_txt,	iconCls: 'silk-pencil',	handler: onEdit, disabled: true	}, '-',
               		{ref: '../deleteButton', text: buttonDelete_txt, iconCls: 'silk-delete', handler: onDelete, disabled: true, id:'deleteButton' }, '-',
                	{ref: '../activeButton', iconCls: 'silk-lightbulb', text: makeActiveLabel_txt, handler: onActivate, disabled: true, id:'activeButton'}, '-', 
        	    	{id:'inactiveButton', iconCls: 'silk-lightbulb-off', ref: '../inactiveButton', text: makeInactiveLabel_txt, handler: onActivate, disabled: true}, '-',
        	    	{id:'highPriorityButton', iconCls: 'silk-flag-red', ref: '../highPriorityButton', text: priorityCriticalLabel_txt, handler: changePriority, disabled: true}, '-',
        	    	{id:'lowPriorityButton', iconCls: 'silk-flag-green', ref: '../lowPriorityButton', text: priorityNormalLabel_txt, handler: changePriority, disabled: true}
        		<?php }?>
				, {xtype:'tbfill'}
        	    <?php if ($_smarty_tpl->tpl_vars['optionMS']->value == true && $_smarty_tpl->tpl_vars['userType']->value == 0) {?> 
                    ,new Ext.taopix.CompanyCombo({id:'companyFilter',name: 'companyFilter', emptyText: selectCompany_txt, options: {ref: session_id, includeGlobal: '1',includeShowAll:'1', onchange: onCompanyChange} })
                <?php }?>
				,{xtype: 'tbspacer', width: 10},
				<?php if ($_smarty_tpl->tpl_vars['optiondesdt']->value == true) {?>
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
				<?php }?>
				{xtype: 'tbspacer', width: 10}
        ],
		plugins: [new Ext.ux.grid.Search({
			iconCls: 'silk-zoom',
			minChars: 3,
			width: 200,
			disableIndexes:['status','priority','id','canDelete','version','currencycode','active','company'<?php if ($_smarty_tpl->tpl_vars['optiondesol']->value == true) {?>,'availableonline'<?php }?>],
			autoFocus: true
		})],        
        bbar: new Ext.PagingToolbar({ pageSize: gridPageSize, store: gridDataStoreObj, displayInfo: true })     
    }); 

	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: licenceUpdateTitle,
		items: grid,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose(); accordianWindowInitialized = false;}, qtip: closeWindow_txt }],
		baseParams: { ref: '<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
' }
	});
	
	centreRegion.add(gMainWindowObj);
	centreRegion.doLayout();
}

/* close this window panel */
function windowClose()
{
	centreRegion.remove('MainWindow', true);
	centreRegion.doLayout();
}

<?php }
}
