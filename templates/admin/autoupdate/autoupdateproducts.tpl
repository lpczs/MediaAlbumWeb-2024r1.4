{literal}

function initialize(pParams)
{
	{/literal}	
	var productUpdateTitle = "{#str_AutoUpdateTitleProductCollections#}";
	var codeTitle_tx = "{#str_LabelCode#}";
	var nameTitle_tx = "{#str_LabelName#}";
	productTitle_tx = "{#str_SectionTitleProducts#}";
	var versionTitle_tx = "{#str_LabelVersion#}";
	var buttonDelete_txt = "{#str_ButtonDelete#}";
	var delteLabel_txt = "{#str_LabelConfirmation#}";
	var delteConformation_txt = "{#str_DeleteProductConfirmation#}";
	var deletingLabel_txt = "{#str_MessageDeleting#}"; 
	var removeLabel_txt = "{#str_LabelRemove#}"; 
	var deleteSuccess_txt      = "{#str_DeleteProducts#}";
	var globalLabel = "{#str_Global#}";
	var priorityLabel_txt ="{#str_LabelPriority#}";
	var priorityNormalLabel_txt = "{#str_LabelPriorityNormal#}";
	var priorityCriticalLabel_txt = "{#str_LabelPriorityCritical#}";
	var statusLabel_txt = "{#str_LabelStatus#}";
	var activeLabel_tx ="{#str_LabelActive#}"; 
	var inactivelabel_tx = "{#str_LabelInactive#}";
	var activatingLabel_txt = "{#str_Activating#}";
	var deactivatingLabel_txt = "{#str_Deactivating#}";
	var changingPriorityLabel_txt = "{#str_MessageChangingPriority#}";
	var makeInactiveLabel_txt = "{#str_LabelMakeInactive#}"; 
	var makeActiveLabel_txt = "{#str_LabelMakeActive#}"; 
	var closeWindow_txt = "{#str_LabelCloseWindow#}";
	var selectCompany_txt = "{#str_LabelSelectCompany#}";
	var gridPageSize = 100;
	var session_id = "{$ref}";
	var userType = "{$userType}";
	var optionMS = "{$optionMS}";
	var companyCode = "{$companyCode}";
	{literal}
	
	/*** Deleting ***/
	var onDelete = function(btn, ev){ Ext.MessageBox.confirm(delteLabel_txt, delteConformation_txt, onDeleteResult);  };    

	function onDeleteCallback(pUpdated, pTheForm, pActionData) {
		if(pUpdated)
		{
			Ext.MessageBox.show({ title: delteLabel_txt, msg: deleteSuccess_txt, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO });  
			grid.store.reload(); 
		}
		else Ext.MessageBox.show({ title: pActionData.result.title, msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
	}

	var onDeleteResult = function(btn) {
		if (btn == "yes") {
			var paramArray = new Object();
			var selRecords = gridCheckBoxSelectionModelObj.getSelections(); var ids = new Array();	
			
			for (var rec = 0, product; rec < selRecords.length; rec++)
			{
				ids.push(selRecords[rec].data.code);
			}
			 	
			var iDList = ids.join(','); 

			paramArray['collectioncodes'] = iDList;  

			Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminAutoUpdate.deleteProduct', deletingLabel_txt, onDeleteCallback);	
		}
	};
	/*** end Deleting ***/
	
	gridDataStoreObj = new Ext.data.GroupingStore({
		remoteSort: true,
		remoteGroup:true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminAutoUpdate.listProducts&ref='+session_id }),
		method:'POST',
		groupField:'company',
		reader: new Ext.taopix.PagedArrayReader( { idIndex: 0}, Ext.data.Record.create([ {name:'id'},{name: 'code'},{name: 'name'}, {name: 'products'}, {name: 'version'},{name: 'isactive'},{name: 'priority'},{name: 'company'} ]) ),
		sortInfo:{field: 'code', direction: "ASC"},
		listeners:{
        	'beforeload':function(pStore, pOptions){ 
				var companyFilterCmb = Ext.getCmp('companyFilter');
				var productsGrid = Ext.getCmp('productUpdateGrid');
				if(companyFilterCmb)
				{
					companyCode = companyFilterCmb.getValue();
					productsGrid.store.lastOptions.params['companyCode'] = companyFilterCmb.getValue();
					productsGrid.store.setBaseParam('companyCode', companyFilterCmb.getValue());
				}

				checkHideInactiveButton(pStore, pOptions);
        	}
        },
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	}); 
	gridDataStoreObj.load({params:{start:0, limit:gridPageSize}});

	gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({ 
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj) { 
				if (gridCheckBoxSelectionModelObj.getCount() > 0) 
				{ 
					grid.deleteButton.enable(); grid.activeButton.enable(); grid.inactiveButton.enable(); grid.highPriorityButton.enable(); grid.lowPriorityButton.enable(); 
				} 
				else  
				{
				   grid.deleteButton.disable(); grid.activeButton.disable(); grid.inactiveButton.disable(); grid.highPriorityButton.disable(); grid.lowPriorityButton.disable();
				}
		}
	}
	});

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
	
	function priorityRenderer(value, p, record, rowIndex, colIndex, store) 
	{
		if (record.data.priority == 0) 
		{
			return '{/literal}<img src="{$webroot}/utils/ext/images/silk/flag_green.png" />{literal}';
		}
		else
		{
			return '{/literal}<img src="{$webroot}/utils/ext/images/silk/flag_red.png" />{literal}';
		}
		
	};
	
	function activeColumnRenderer(value, p, record, rowIndex, colIndex, store) 
	{
		var value = '', className = '';
		if (record.data.isactive == 0) 
		{
			value = inactivelabel_tx; className = 'class = "inactive"';
		}	
		else 
		{
			value = activeLabel_tx;
		}
		return '<span '+className+'>'+value+'</span>';
	};
	
	function isActiveColumnRenderer(value, p, record, rowIndex, colIndex, store) 
	{
		var className = '';
		if (record.data.isactive == 0) 
		{
			className = 'class = "inactive"'
		}
		return '<span '+className+'>'+value+'</span>';
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

		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminAutoUpdate.activateProductCollection', maskText, onActivateCallback);
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

		Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminAutoUpdate.changeProductCollectionPriority', maskText, onActivateCallback);
	};

	function onHideInactive(btn, ev)
	{
		// get the datastore and default hide inactive to false
		var gridDataStore = Ext.getCmp('productUpdateGrid').store;
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
					var gridDataStore = Ext.getCmp('productUpdateGrid').store;

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

	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {	sortable: true, resizable: true },
		columns: [gridCheckBoxSelectionModelObj,
            { header: codeTitle_tx, dataIndex: 'code', width:200, renderer: isActiveColumnRenderer},
            { id:'nameName', header: nameTitle_tx, dataIndex: 'name', width:450, renderer: isActiveColumnRenderer},
            { id:'products', header: productTitle_tx, dataIndex: 'products', width:320, renderer: isActiveColumnRenderer, sortable: false},
            { header: versionTitle_tx, dataIndex: 'version', width:120, align: 'right', renderer: isActiveColumnRenderer},
            { header: statusLabel_txt, dataIndex: 'isactive', width:70, renderer: activeColumnRenderer, align:'right'},
            { header: priorityLabel_txt, id: 'priority', dataIndex: 'priority', width:85, renderer: priorityRenderer, align:'right'}
            , { id:'company', header: 'Company', dataIndex: 'company', hidden:true, renderer: companyColumnRenderer}
        ]
	});

	var onCompanyChange = function()
	{
		var productGrid = Ext.getCmp('productUpdateGrid');
		productGrid.getBottomToolbar().changePage(1);
		productGrid.reload();
	};

	var grid = new Ext.grid.GridPanel({
    	id: 'productUpdateGrid',
        store: gridDataStoreObj,
        selModel: gridCheckBoxSelectionModelObj,
        cm: gridColumnModelObj,
        stripeRows: true,
        stateful: true,
        autoExpandColumn: 'priority',
        enableColLock:false,
        /*loadMask: true,*/
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		trackMouseOver:false,
		columnLines:true,
        stateId: 'productUpdateGrid',
		ctCls: 'grid',
        {/literal}{if $optionMS == true}{literal} 
    	view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_LabelItems#}{literal}" : "{/literal}{#str_LabelItem#}{literal}"]})' }),
    	{/literal}{/if}{literal}
    		
        tbar: [	
        		{ref: '../deleteButton', text: removeLabel_txt, iconCls: 'silk-delete', handler: onDelete, disabled: true, id:'deleteButton' },
        		{ref: '../activeButton', iconCls: 'silk-lightbulb', text: makeActiveLabel_txt, handler: onActivate, disabled: true, id:'activeButton'}, '-', 
       	    	{id:'inactiveButton', iconCls: 'silk-lightbulb-off', ref: '../inactiveButton', text: makeInactiveLabel_txt, handler: onActivate, disabled: true}, '-',
       	    	{id:'highPriorityButton', iconCls: 'silk-flag-red', ref: '../highPriorityButton', text: priorityCriticalLabel_txt, handler: changePriority, disabled: true}, '-',
       	    	{id:'lowPriorityButton', iconCls: 'silk-flag-green', ref: '../lowPriorityButton', text: priorityNormalLabel_txt, handler: changePriority, disabled: true},
        		{xtype:'tbfill'}
            	{/literal}{if $optionMS == true and $userType==0}{literal} 
            		,new Ext.taopix.CompanyCombo({id:'companyFilter',name: 'companyFilter', emptyText: selectCompany_txt, options: {ref: session_id, includeGlobal: '1', includeShowAll:'1', onchange: onCompanyChange} })   
            	{/literal}{/if}{literal}
				,{xtype: 'tbspacer', width: 10},
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
				{xtype: 'tbspacer', width: 10} 
        ],
        plugins: [
			new Ext.ux.grid.Search({
				iconCls: 'silk-zoom',
				minChars: 3,
				width: 200,
				autoFocus: true,
				disableIndexes:['name', 'version', 'isactive', 'priority', 'company']
			})
		],
        bbar: new Ext.PagingToolbar({ pageSize: gridPageSize, store: gridDataStoreObj,displayInfo: true, listeners: { beforechange: carryHideInactiveIntoPagingToolbarRefresh }})         
    });

	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: productUpdateTitle,
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