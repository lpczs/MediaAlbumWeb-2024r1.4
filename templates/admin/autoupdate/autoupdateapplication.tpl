var applicationUpdateTitle = "{#str_AutoUpdateTitleApplication#}";
var brandingTitle_tx       = "{#str_SectionTitleBranding#}";
var osTitle_tx             = "{#str_LabelOperatingSystem#}";
var versionTitle_tx        = "{#str_LabelVersion#}";
var archiveTitle_tx        = "{#str_LabelArchiveName#}";
var executableTitle_tx     = "{#str_LabelExeName#}";
var savingLabel_txt        = "{#str_MessageSaving#}";
var addLabel_txt           = "{#str_ButtonEdit#}";
var buttonDelete_txt       = "{#str_ButtonDelete#}";
var delteLabel_txt         = "{#str_LabelConfirmation#}";
var delteConformation_txt  = "{#str_DeleteApplicationConfirmation#}";
var deletingLabel_txt      = "{#str_MessageDeleting#}"; 
var removeLabel_txt        = "{#str_LabelRemove#}"; 
var deleteSuccess_txt      = "{#str_DeleteApplications#}";
var selectCompany_txt      = "{#str_LabelSelectCompany#}";
var closeWindow_txt      = "{#str_LabelCloseWindow#}";
var priorityLabel_txt = "{#str_LabelPriority#}";
var priorityNormalLabel_txt = "{#str_LabelPriorityNormal#}";
var priorityCriticalLabel_txt = "{#str_LabelPriorityCritical#}";
var changingPriorityLabel_txt = "{#str_MessageChangingPriority#}";


var session_id      = "{$ref}";
var userType        = "{$userType}";
var optionMS        = "{$optionMS}";
var companyCode     = "{$companyCode}";
	
var gridPageSize = 100;
	
{literal}

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
        view: new Ext.grid.GroupingView({ forceFit:false, groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "{/literal}{#str_LabelItems#}{literal}" : "{/literal}{#str_LabelItem#}{literal}"]})' }),
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
               	{/literal}{if $optionMS == true and $userType==0}{literal}
				,{xtype:'tbfill'}
               	,new Ext.taopix.CompanyCombo({id:'companyFilter',name: 'companyFilter', emptyText: selectCompany_txt, options: {ref: session_id, includeGlobal: '0', includeShowAll:'1', onchange: onCompanyChange} })
               	,{xtype: 'tbspacer', width: 10}
               	{/literal}{/if}{literal}
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
		baseParams: { ref: '{/literal}{$ref}{literal}' }
	});
		
	centreRegion.add(gMainWindowObj);
	centreRegion.doLayout();

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

{/literal}