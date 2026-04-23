{literal}

function initialize(pParams)
{
	{/literal}
	var filesUpdateTitle = "{$title}";
	var brandingTitle_tx = "{#str_SectionTitleBranding#}";
	var categoryTitle_tx = "{#str_LabelCategory#}";
	var nameTitle_tx = "{#str_LabelName#}";
	var fileNameTitle_tx = "{#str_LabelFileName#}";
	var versionTitle_tx = "{#str_LabelVersion#}";
	var privateTitle_tx = "{#str_LabelPrivate#}";
	var activeTitle_tx = "{#str_LabelActive#}";
	var statusLabel_txt = "{#str_LabelStatus#}";
	var buttonDelete_txt = "{#str_ButtonDelete#}";
	var delteLabel_txt = "{#str_LabelConfirmation#}";
	var filestype = "{$filestype}";
	var deleteSuccess_txt  = '';      
	var delteConformation_txt = '';
	var priorityLabel_txt ="{#str_LabelPriority#}";
	var priorityNormalLabel_txt = "{#str_LabelPriorityNormal#}";
	var priorityCriticalLabel_txt = "{#str_LabelPriorityCritical#}";
	var changingPriorityLabel_txt = "{#str_MessageChangingPriority#}";
	var selectCompany_txt      = "{#str_LabelSelectCompany#}";
	var closeWindow_txt      = "{#str_LabelCloseWindow#}";
	var enableOnline_txt = "{#str_LabelEnableOnline#}";
	var disableOnline_txt = "{#str_LabelDisableOnline#}";
	var availableOnline_txt = "{#str_LabelAvailableOnline#}";

	switch(filestype)
	{literal}{{/literal}
		case '1': delteConformation_txt = "{#str_DeleteMaskConfirmation#}"; 		    deleteSuccess_txt = "{#str_DeleteMaskFiles#}";       break;
		case '2': delteConformation_txt = "{#str_DeleteBackgroundConfirmation#}";       deleteSuccess_txt = "{#str_DeleteBackgroundFiles#}"; break;
		case '3': delteConformation_txt = "{#str_DeleteScrapbookPictureConfirmation#}"; deleteSuccess_txt = "{#str_DeleteScrapbookFiles#}";  break;
		case '4': delteConformation_txt = "{#str_DeleteFramesConfirmation#}"; 			deleteSuccess_txt = "{#str_DeleteFramesFiles#}";     break;
	{literal} } {/literal}

	var deletingLabel_txt = "{#str_MessageDeleting#}"; 
	var activatingLabel_txt = "{#str_Activating#}";
	var deactivatingLabel_txt = "{#str_Deactivating#}";
	var activeLabel_tx ="{#str_LabelActive#}"; 
	var inactivelabel_tx = "{#str_LabelInactive#}";
	var removeLabel_txt = "{#str_LabelRemove#}"; 
	var yesLabel_tx = "{#str_LabelYes#}";
	var noLabel_tx = "{#str_LabelNo#}";
	var session_id = "{$ref}";
	var makeInactiveLabel_txt = "{#str_LabelMakeInactive#}"; 
	var makeActiveLabel_txt = "{#str_LabelMakeActive#}"; 
	var gridPageSize = 100;
	var userType = "{$userType}";
	var optionMS = "{$optionMS}";
	var companyCode = "{$companyCode}";
	{literal}

	function activeColumnRenderer(value, p, record, rowIndex, colIndex, store) 
	{
		var value = '', className = '';
		if (record.data.activeName == 0)
		{ 
			value = inactivelabel_tx; className = 'class = "inactive"';
		}	
		else 
		{
			value = activeLabel_tx;
		}
		return '<span '+className+'>'+value+'</span>';
	}
	

	function onlineActiveColumnRenderer(value, p, record, rowIndex, colIndex, store) 
	{
		var value = '', className = '';
		if (record.data.onlineActive == 0)
		{ 
			value = inactivelabel_tx; className = 'class = "inactive"';
		}	
		else 
		{
			value = activeLabel_tx;
		}
		return '<span '+className+'>'+value+'</span>';
	}
	
	function priorityRenderer(value, p, record, rowIndex, colIndex, store) 
	{
		if (record.data.updatePriority == 0)
		{
			return '{/literal}<img src="{$webroot}/utils/ext/images/silk/flag_green.png" />{literal}';
		}
		else
		{
			return '{/literal}<img src="{$webroot}/utils/ext/images/silk/flag_red.png" />{literal}';
		}
	};
	
	function privateColumnRenderer(value, p, record, rowIndex, colIndex, store) 
	{
		var value = '';
		var className = '';
		var activeColumn = 1;
		
		if (record.data.privateName == 0)
		{ 
			value = noLabel_tx;
		}
		else
		{
			value = yesLabel_tx;
		}
		
		{/literal}{if $optiondesol == true && $optiondesdt == true}{literal}
		
			if (record.data.onlineActive == 0 && record.data.activeName == 0)
			{
				className = 'class = "inactive"';
			}
			
			spanData = '<span ' + className + '>' + value + '</span>';
		
		{/literal}{else}{literal}
			{/literal}{if  $optiondesol == true && $optiondesdt == false}{literal}
				activeColumn = record.data.onlineActive;
			{/literal}{elseif  $optiondesol == false && $optiondesdt == true}{literal}
				activeColumn = record.data.activeName;
			{/literal}{/if}{literal}
		{/literal}{/if}{literal}
		
		if (activeColumn == 0) 
		{
			className = 'class = "inactive"';
		}
		return '<span ' + className + '>' + value + '</span>';
	}
	
	
	function isActiveColumnRenderer(value, p, record, rowIndex, colIndex, store) 
	{
		var className = '';
		var spanData = '';
		var activeColumn = '';
		
		{/literal}{if $optiondesol == true && $optiondesdt == true}{literal}
		
			if (record.data.onlineActive == 0 && record.data.activeName == 0)
			{
				className = 'class = "inactive"';
			}
			
			spanData = '<span ' + className + '>' + value + '</span>';
		
		{/literal}{else}{literal}
		
			{/literal}{if  $optiondesol == true}{literal}
				activeColumn = record.data.onlineActive;
			{/literal}{elseif  $optiondesdt == true}{literal}
				activeColumn = record.data.activeName;
			{/literal}{/if}{literal}
		
			if (activeColumn == 0) 
			{
				className = 'class = "inactive"';
			}
			
			spanData = '<span ' + className + '>' + value + '</span>';
		
		{/literal}{/if}{literal}

		return spanData;
	};
	
	function getBrandName(value, p, record, rowIndex, colIndex, store)
	{
		return value;
	}

	function onDelete(btn, ev)
	{ 
		try
		{ 
			Ext.MessageBox.confirm(delteLabel_txt, delteConformation_txt, onDeleteResult); 
		}
		catch(e){} 
	};
	
	function onDeleteCallback(pUpdated, pTheForm, pActionData) 
	{
		if(pUpdated)
		{ 
			Ext.MessageBox.show({ title: delteLabel_txt, msg: deleteSuccess_txt, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO }); 
			grid.store.reload();
		} 
		else { Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING }); }
	};
	
	function onDeleteResult(btn) 
	{
		if (btn == "yes") 
		{
			var paramArray = new Object();
			var selRecords = gridCheckBoxSelectionModelObj.getSelections();
			var ids = new Array();	for (var rec = 0; rec < selRecords.length; rec++) {	ids.push(selRecords[rec].data.fileId); } var iDList = ids.join(','); 

			paramArray['idlist'] = iDList; 
			Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminAutoUpdate.deleteApplicationFile', deletingLabel_txt, onDeleteCallback);	
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
	
	var onActivate = function(btn, ev)
	{  
		var maskText = deactivatingLabel_txt;
		var command = 0;
		if (btn.id == 'activeButton') {maskText = activatingLabel_txt; command = 1;}
		
		var paramArray = new Object();
		var selRecords = gridCheckBoxSelectionModelObj.getSelections();
		var ids = new Array();	

		for (var rec = 0; rec < selRecords.length; rec++) { ids.push(selRecords[rec].data.fileId); }
		var iDList = ids.join(','); paramArray['idlist'] = iDList;  paramArray['command'] = command;

		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminAutoUpdate.activateApplicationFile', maskText, onActivateCallback);
	};
	

	var onActivateOnline = function(btn, ev)
	{  
		var maskText = deactivatingLabel_txt;
		var command = 0;
		if (btn.id == 'onlineActiveButton') {maskText = activatingLabel_txt; command = 1;}
		
		var paramArray = new Object();
		var selRecords = gridCheckBoxSelectionModelObj.getSelections();
		var ids = new Array();	

		for (var rec = 0; rec < selRecords.length; rec++) { ids.push(selRecords[rec].data.fileId); }
		var iDList = ids.join(','); paramArray['idlist'] = iDList;  paramArray['command'] = command;
		paramArray['filetype'] = filestype;

		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminAutoUpdate.activateApplicationFileOnline', maskText, onActivateCallback);
	};
	
	var changeApplicationFilesPriority = function(btn, ev)
	{  
		var maskText = changingPriorityLabel_txt;
		var command = 0;
		if(btn.id == 'highPriorityButton') {maskText = changingPriorityLabel_txt; command = 1000;}
		
		var paramArray = new Object();
		var selRecords = gridCheckBoxSelectionModelObj.getSelections();
		var ids = new Array();	

		for (var rec = 0; rec < selRecords.length; rec++) { ids.push(selRecords[rec].data.fileId); }
		var iDList = ids.join(','); paramArray['idlist'] = iDList;  paramArray['command'] = command;

		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminAutoUpdate.changeApplicationFilePriority', maskText, onActivateCallback);
	};

	{/literal}{if $optiondesdt == true}{literal}
	function onHideInactive(btn, ev)
	{
		// get the datastore and default hide inactive to false
		var gridDataStore = Ext.getCmp('filesUpdateGrid').store;
		var hideInactive = 0;

		if (btn.pressed)
		{
			// set to true and update tooltip
			hideInactive = 1;
			Ext.getCmp('hideInactiveButton').setTooltip('{/literal}{#str_TooltipShowInactiveItems#}{literal}');
		}
		else
		{
			Ext.getCmp('hideInactiveButton').setTooltip('{/literal}{#str_TooltipHideInactiveItems#}{literal}');			
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
				hideInactiveButton.setTooltip({text: '{/literal}{#str_TooltipHideInactiveItemsIsDisabledForSearchResults#}{literal}', autoHide: true, id: 'hideInactiveDisabledTooltip'});
			}
			else
			{
				// search field has been emptied for the first time since it was last filled
				if (hideInactiveButton.disabled == true)
				{
					hideInactiveButton.enable();
					var gridDataStore = Ext.getCmp('filesUpdateGrid').store;

					// restore button to its state before the search 
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
	{/literal}{/if}{literal}

	gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({ 
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj) 
			{ 
				if (gridCheckBoxSelectionModelObj.getCount() > 0)
				{
					{/literal}{if $optiondesdt == true}{literal}
						grid.activeButton.enable(); 
						grid.inactiveButton.enable(); 
					{/literal}{/if}{literal}
					
					grid.deleteButton.enable(); 
					grid.highPriorityButton.enable(); 
					grid.lowPriorityButton.enable();
					
					{/literal}{if $optiondesol == true}{literal}
						grid.onlineActiveButton.enable(); 
						grid.onlineInactiveButton.enable();
					{/literal}{/if}{literal}
				} 
				else 
				{ 
					{/literal}{if $optiondesdt == true}{literal}
						grid.activeButton.disable(); 
						grid.inactiveButton.disable(); 
					{/literal}{/if}{literal}
					 
					grid.deleteButton.disable(); 
					grid.highPriorityButton.disable(); 
					grid.lowPriorityButton.disable();
					
					{/literal}{if $optiondesol == true}{literal}
						grid.onlineActiveButton.disable(); 
						grid.onlineInactiveButton.disable();
					{/literal}{/if}{literal}
				}
			}
		}
	});
	
	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {	sortable: true, resizable: true },
		columns: [gridCheckBoxSelectionModelObj,
			{/literal}{if $optiondesol == true && $optiondesdt == true}{literal}
				{header: brandingTitle_tx, dataIndex: 'brandName', width:150, renderer: isActiveColumnRenderer, groupRenderer: getBrandName },
            	{header: categoryTitle_tx, dataIndex: 'categoryName', width:150, renderer: isActiveColumnRenderer },
            	{id:'nameName', header: nameTitle_tx, dataIndex: 'nameName', renderer: isActiveColumnRenderer },
            	{header: fileNameTitle_tx, dataIndex: 'fileName', width:150, renderer: isActiveColumnRenderer },
            	{header: versionTitle_tx, dataIndex: 'versionName', width:125, align:'right', renderer: isActiveColumnRenderer },
            	{header: privateTitle_tx, dataIndex: 'privateName', width:100, renderer: privateColumnRenderer, align:'right'},
            	{header: statusLabel_txt, dataIndex: 'activeName', width:100, renderer: activeColumnRenderer, align:'right'},
            		{/literal}{if $filestype != 4}{literal}
            			{header: availableOnline_txt, dataIndex: 'onlineActiveName', width:100, renderer: onlineActiveColumnRenderer, align:'right'},
            		{/literal}{/if}{literal}
				{header: priorityLabel_txt, dataIndex: 'updatePriority', width:80, renderer: priorityRenderer, align:'right'}
			{/literal}{elseif  $optiondesol == true}{literal}
				{header: brandingTitle_tx, dataIndex: 'brandName', width:150, renderer: isActiveColumnRenderer, groupRenderer: getBrandName },
            	{header: categoryTitle_tx, dataIndex: 'categoryName', width:150, renderer: isActiveColumnRenderer },
            	{id:'nameName', header: nameTitle_tx, dataIndex: 'nameName', renderer: isActiveColumnRenderer },
            	{header: fileNameTitle_tx, dataIndex: 'fileName', width:150, renderer: isActiveColumnRenderer },
            	{header: versionTitle_tx, dataIndex: 'versionName', width:125, align:'right', renderer: isActiveColumnRenderer },
            	{header: privateTitle_tx, dataIndex: 'privateName', width:100, renderer: privateColumnRenderer, align:'right'},
            		{/literal}{if $filestype != 4}{literal}
            			{header: availableOnline_txt, dataIndex: 'onlineActiveName', width:100, renderer: onlineActiveColumnRenderer, align:'right'},
            		{/literal}{/if}{literal}
				{header: priorityLabel_txt, dataIndex: 'updatePriority', width:80, renderer: priorityRenderer, align:'right'}
			{/literal}{elseif  $optiondesdt == true}{literal}
				{header: brandingTitle_tx, dataIndex: 'brandName', width:150, renderer: isActiveColumnRenderer, groupRenderer: getBrandName },
            	{header: categoryTitle_tx, dataIndex: 'categoryName', width:150, renderer: isActiveColumnRenderer },
            	{id:'nameName', header: nameTitle_tx, dataIndex: 'nameName', renderer: isActiveColumnRenderer },
            	{header: fileNameTitle_tx, dataIndex: 'fileName', width:150, renderer: isActiveColumnRenderer },
            	{header: versionTitle_tx, dataIndex: 'versionName', width:125, align:'right', renderer: isActiveColumnRenderer },
            	{header: privateTitle_tx, dataIndex: 'privateName', width:100, renderer: privateColumnRenderer, align:'right'},
            	{header: statusLabel_txt, dataIndex: 'activeName', width:100, renderer: activeColumnRenderer, align:'right'},
				{header: priorityLabel_txt, dataIndex: 'updatePriority', width:80, renderer: priorityRenderer, align:'right'}
			{/literal}{/if}{literal}
        ]
	});
	
	gridDataStoreObj = new Ext.data.GroupingStore({
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminAutoUpdate.getFileList&ref='+session_id + '&filetype='+filestype }),
		method:'POST',
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()},
		groupField:'brandName',
		remoteGroup:true,
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([
			
				{/literal}{if $optiondesol == true && $optiondesdt == true}{literal}
					{name:'fileId'},
					{name: 'brandName'},
					{name: 'categoryName'},
					{name: 'nameName'}, 
					{name: 'fileName'}, 
					{name: 'versionName'},  
					{name: 'privateName'},  
					{name: 'activeName'},
					{/literal}{if $filestype != 4}{literal}
						{name: 'onlineActive'},
					{/literal}{/if}{literal}
					{name: 'updatePriority'}
				{/literal}{elseif  $optiondesol == true}{literal}
					{name:'fileId'},
					{name: 'brandName'},
					{name: 'categoryName'},
					{name: 'nameName'}, 
					{name: 'fileName'}, 
					{name: 'versionName'},  
					{name: 'privateName'},  
					{/literal}{if $filestype != 4}{literal}
						{name: 'onlineActive'},
					{/literal}{/if}{literal}
					{name: 'updatePriority'}
				{/literal}{elseif  $optiondesdt == true}{literal}
					{name:'fileId'},
					{name: 'brandName'},
					{name: 'categoryName'},
					{name: 'nameName'}, 
					{name: 'fileName'}, 
					{name: 'versionName'},  
					{name: 'privateName'},  
					{name: 'activeName'},
					{name: 'updatePriority'}
				{/literal}{/if}{literal}
			])
		),
		sortInfo:{field: 'activeName', direction: "DESC"},
		
		listeners:{
        	'beforeload':function(pStore, pOptions)
        	{ 
				var companyFilterCmb = Ext.getCmp('companyFilter');
				var fileGrid = Ext.getCmp('filesUpdateGrid');

				if (companyFilterCmb)
				{
					fileGrid.store.lastOptions.params['companyCode'] = companyFilterCmb.getValue();
					fileGrid.store.setBaseParam('companyCode', companyFilterCmb.getValue());
				}
				{/literal}{if $optiondesdt == true}{literal}
				checkHideInactiveButton(pStore, pOptions);
				{/literal}{/if}{literal}
    		}
        },
        remoteSort: true
    }); 
	gridDataStoreObj.load({params:{start:0, limit:gridPageSize, companyCode: companyCode}});
	
	var onCompanyChange = function()
	{
		var companyCombo = Ext.getCmp('companyFilter');
		var fileGrid = Ext.getCmp('filesUpdateGrid');
		fileGrid.getBottomToolbar().changePage(1);
	};
	
	var grid = new Ext.grid.GridPanel({
    	id: 'filesUpdateGrid',
        store: gridDataStoreObj,
        selModel: gridCheckBoxSelectionModelObj,
        cm: gridColumnModelObj,
        stripeRows: true,
        stateful: true,
        enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		autoExpandColumn: 'nameName',
		view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_LabelItems#}{literal}" : "{/literal}{#str_LabelItem#}{literal}"]})' }),
		trackMouseOver:false,
		columnLines:true,
		ctCls: 'grid',
        stateId: 'filesUpdateGrid',
        tbar: [	
        		{/literal}{if $optiondesol == true && $optiondesdt == true}{literal}
        			{ref: '../deleteButton', text: removeLabel_txt, iconCls: 'silk-delete', handler: onDelete, disabled: true, id:'deleteButton' }, '-',
                	{ref: '../activeButton', iconCls: 'silk-lightbulb', text: makeActiveLabel_txt, handler: onActivate, disabled: true, id:'activeButton'},  '-',
        	    	{id:'inactiveButton', iconCls: 'silk-lightbulb-off', ref: '../inactiveButton', text: makeInactiveLabel_txt, handler: onActivate, disabled: true },
        			{/literal}{if $filestype != 4}{literal}
        	    		{id:'onlineActiveButton', iconCls: 'silk-lightbulb', ref: '../onlineActiveButton', text: enableOnline_txt, handler: onActivateOnline, disabled: true },	'-', 
        	    		{id:'onlineinactiveButton', iconCls: 'silk-lightbulb-off', ref: '../onlineInactiveButton', text: disableOnline_txt, handler: onActivateOnline, disabled: true }, '-',
        	    	{/literal}{/if}{literal}
        	    	{id:'highPriorityButton', iconCls: 'silk-flag-red', ref: '../highPriorityButton', text: priorityCriticalLabel_txt, handler: changeApplicationFilesPriority, disabled: true}, '-',
        	    	{id:'lowPriorityButton', iconCls: 'silk-flag-green', ref: '../lowPriorityButton', text: priorityNormalLabel_txt, handler: changeApplicationFilesPriority, disabled: true} 
        		{/literal}{elseif  $optiondesol == true}{literal}
        			{ref: '../deleteButton', text: removeLabel_txt, iconCls: 'silk-delete', handler: onDelete, disabled: true, id:'deleteButton' }, '-',
        			{/literal}{if $filestype != 4}{literal}
        	    		{id:'onlineActiveButton', iconCls: 'silk-lightbulb', ref: '../onlineActiveButton', text: enableOnline_txt, handler: onActivateOnline, disabled: true },	'-', 
        	    		{id:'onlineinactiveButton', iconCls: 'silk-lightbulb-off', ref: '../onlineInactiveButton', text: disableOnline_txt, handler: onActivateOnline, disabled: true }, '-',
        	    	{/literal}{/if}{literal}
        	    	{id:'highPriorityButton', iconCls: 'silk-flag-red', ref: '../highPriorityButton', text: priorityCriticalLabel_txt, handler: changeApplicationFilesPriority, disabled: true}, '-',
        	    	{id:'lowPriorityButton', iconCls: 'silk-flag-green', ref: '../lowPriorityButton', text: priorityNormalLabel_txt, handler: changeApplicationFilesPriority, disabled: true}
        		{/literal}{elseif  $optiondesdt == true}{literal}
        			{ref: '../deleteButton', text: removeLabel_txt, iconCls: 'silk-delete', handler: onDelete, disabled: true, id:'deleteButton' }, '-',
                	{ref: '../activeButton', iconCls: 'silk-lightbulb', text: makeActiveLabel_txt, handler: onActivate, disabled: true, id:'activeButton'},  '-',
        	    	{id:'inactiveButton', iconCls: 'silk-lightbulb-off', ref: '../inactiveButton', text: makeInactiveLabel_txt, handler: onActivate, disabled: true },
        	    	{id:'highPriorityButton', iconCls: 'silk-flag-red', ref: '../highPriorityButton', text: priorityCriticalLabel_txt, handler: changeApplicationFilesPriority, disabled: true}, '-',
        	    	{id:'lowPriorityButton', iconCls: 'silk-flag-green', ref: '../lowPriorityButton', text: priorityNormalLabel_txt, handler: changeApplicationFilesPriority, disabled: true}
        		{/literal}{/if}{literal}
        		,{xtype:'tbfill'}
                {/literal}{if $optionMS == true and $userType==0}{literal} 
                    ,new Ext.taopix.CompanyCombo({id:'companyFilter',name: 'companyFilter', emptyText: selectCompany_txt, options: {ref: session_id, includeGlobal: '0', includeShowAll:'1', onchange: onCompanyChange} }),
					{xtype: 'tbspacer', width: 10} 
                {/literal}{/if}{literal}
				,
				{/literal}{if $optiondesdt == true}{literal}
				{
				id:'hideInactiveButton',
				ref: '../hideInactiveButton',
				tooltip: '{/literal}{#str_TooltipHideInactiveItems#}{literal}',
				iconCls: 'hideInactiveButton',
				handler: onHideInactive,
				enableToggle: true,
				xtype: 'button',
				ctCls:'x-toolbar-standardbutton'
				},
				{/literal}{/if}{literal}
				{xtype: 'tbspacer', width: 10} 
        ], 
        plugins: [
            new Ext.ux.grid.Search({
                iconCls: 'silk-zoom',
                minChars: 3,
                width: 200,
                autoFocus: true,
                disableIndexes:['companycode', 'brandName', 'privateName', 'activeName', 'onlineActiveName', 'updatePriority', 'versionName']
            })
        ],
        bbar: new Ext.PagingToolbar({
			pageSize: gridPageSize,
			store: gridDataStoreObj,
			displayInfo: true
			{/literal}{if $optiondesdt == true}{literal}
			,listeners: { beforechange: carryHideInactiveIntoPagingToolbarRefresh }
			{/literal}{/if}{literal}
			})
    }); 
    
	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: filesUpdateTitle,
		items: grid,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose(); accordianWindowInitialized = false;}, qtip: closeWindow_txt }],
		baseParams: { ref: '{/literal}{$ref}{literal}' }
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

{/literal}
