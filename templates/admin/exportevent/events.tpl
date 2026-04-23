{literal}

function initialize(pParams)
{
	{/literal}
	var session_id = "{$ref}";
	var exportEventsTitle_txt = "{#str_ExportTitleEvent#}";
	var exportWebhook1URLTitle_txt = "{#str_LabelWebhook1URL#}";
	var exportWebhook2URLTitle_txt = "{#str_LabelWebhook2URL#}";
	var eventLang_tx = "{#str_LabelLanguage#}";
	var exportFormat_tx = "{#str_LabelExportFormat#}";
	var labelActive_tx = "{#str_LabelActive#}";
	var beautifiesXML_txt = "{#str_LabelXmlBeautify#}"; 
	var editLabel_txt = "{#str_LabelEditEvent#}";
	var buttonCancel_tx ="{#str_ButtonCancel#}"; 
	var statusLabel_txt = "{#str_LabelStatus#}";
	var statusLabel_tx ="{#str_LabelStatus#}"; 
	var connectionErrorLabel_tx ="{#str_ErrorConnectFailure#}"; 
	var activatingLabel_tx ="{#str_Activating#}"; 
	var deactivatingLabel_tx ="{#str_Deactivating#}"; 
	var buttonUpdate_tx ="{#str_ButtonUpdate#}"; 
	var eventCode_tx ="{#str_LabelEventCode#}"; 
	var filePath_tx ="{#str_LabelFilePath#}"; 
	var filenameFormat_tx ="{#str_LabelFilenameFormat#}"; 
	var exportLang_tx ="{#str_LabelLanguage#}"; 
	var defaultLang_tx = "{$defaultlanguagename}"; 
	var langDefaultLabel_tx ="{#str_LabelLangDefault#}"; 
	var orderLang_tx ="{#str_LabelLangOrder#}"; 
	var formatLabel_tx ="{#str_LabelFormat#}"; 
	var formatXMLLabel_tx ="{#str_LabelFormatXML#}"; 
	var formatTXTLabel_tx ="{#str_LabelFormatTXT#}"; 
	var paymentLabel_tx ="{#str_LabelPayment#}"; 
	var beautifulXMLLabel_tx ="{#str_LabelXmlBeautify#}"; 
	var activeLabel_tx ="{#str_LabelActive#}"; 
	var inactivelabel_tx = "{#str_LabelInactive#}";
	var systemCharacterError = "{#str_ErrorSystemCharacter#}";
	var otherLabel_txt = "{#str_Other#}";
	var makeInactiveLabel_txt = "{#str_LabelMakeInactive#}"; 
	var makeActiveLabel_txt = "{#str_LabelMakeActive#}"; 
	var savingLabel_txt = "{#str_MessageSaving#}";
	var addLabel_txt = "{#str_ButtonEdit#}";
	var session_id = "{$ref}";
	var eventData = {$rows};
	var langArray = {$languagesList};
	var exportFormats = [['TXT',formatTXTLabel_tx],['XML',formatXMLLabel_tx],['BXML',beautifulXMLLabel_tx]];
	{literal}
	
	var clearAllInvalid = function(formId)
	{
		Ext.getCmp(formId).getForm().items.each(function(item){ if (item.clearInvalid) item.clearInvalid(); });
	};

	var ajaxRequest = function(method, url, params, successCallBack, failureCallback, maskText){
		var conn = new Ext.data.Connection();
		grid.getGridEl().mask(maskText);
		conn.request({ url: url, method: method, params: params, success: successCallBack, failure: failureCallback });
	};

	var activateSuccessCallback = function(responseObject){ grid.getGridEl().unmask(true);  grid.store.reload();};
	var activateFailureCallback = function(responseObject)
	{ 
		grid.getGridEl().unmask(true); 
		Ext.MessageBox.show({ title: statusLabel_tx, msg: connectionErrorLabel_tx, buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
		grid.store.reload(); 
	};
	
	var onActivate = function(btn, ev){ 
		var records = gridCheckBoxSelectionModelObj.getSelections();
		var ids = []; 
		var active = 0, maskText = ''; 
		switch(btn.id) { 
            case 'activeButton': 
                active = 1; 
                maskText = activatingLabel_tx; 
                break; 	
            case 'inactiveButton': 
                active = 0; 
                maskText = deactivatingLabel_tx; 
                break;	
        }
        for( i = 0; i < records.length; i++) {
            if((active) && (records[i].data.filePath != '' && records[i].data.filenameFormat != '') || (records[i].data.webhook1url != '' || records[i].data.webhook2url != '')){
                ids.push(records[i].data.eventId);
            }
            else 
            {
                if( !active){
                    ids.push(records[i].data.eventId);
                }
                else
                {
                    Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ExtJsErrorInvalidTrigger#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING }); 
                }
            }
        }
		
        if (ids.length)
        {
            var params = {
                    "ids": ids.join(','), 
                    "active": active,
					"csrf_token": Ext.taopix.getCSRFToken()
            };
            ajaxRequest('POST', 'index.php?fsaction=AdminExportEvent.eventActivate&ref='+session_id, params, activateSuccessCallback, activateFailureCallback, maskText);
        }
    };

	function activeColumnRenderer(value, p, record, rowIndex, colIndex, store) {
		var className = '';
		if (record.data.active == 0) {className = 'class = "inactive"'; value =  inactivelabel_tx;}  else value=  activeLabel_tx;
		return '<span '+className+'>'+value+'</span>';
	};
	
	function isActiveColumnRenderer(value, p, record, rowIndex, colIndex, store) {
		var className = '';
		if (record.data.active == 0) {className = 'class = "inactive"'}
		return '<span '+className+'>'+value+'</span>';
	};

	function webhookActiveColumnRenderer(value, p, record, rowIndex, colIndex, store) {
		var className = '';
		if (value == '') {value =  inactivelabel_tx;}  else value=  activeLabel_tx;
		if (record.data.active == 0) {className = 'class = "inactive"'}
		return '<span '+className+'>'+value+'</span>';
	};

	function onEdit(btn, ev) {
		var record = gridCheckBoxSelectionModelObj.getSelected();
		var isOrder = 1;
		switch(record.data.eventCode){
			case 'CUSTOMERADD': isOrder = 0; break; 	case 'CUSTOMEREDIT': isOrder = 0; break;        case 'PASSWORDRESET': isOrder = 0; break; 
			case 'CUSTOMERDELETE': isOrder = 0; break; 	case 'CUSTOMERACTIVATE': isOrder = 0; break; 	default: isOrder = 1;  
		}
		var exportFormatString = 'XML';
		if ((record.data.exportFormat == 'XML')){ exportFormatString = 'XML'; if(record.data.beautified == 1) exportFormatString = 'BXML'; } else exportFormatString = 'TXT';
		
		gDialogObj.show();	 gDialogObj.center();
		
		document.getElementById('langD').onclick = document.getElementById('langC').onclick = document.getElementById('langO').onclick = langClicked;
		
		var eventCode = Ext.ComponentMgr.get('eventCode');	eventCode.setValue(record.data.eventCode); 
		var filePath = Ext.ComponentMgr.get('filePath'); filePath.setValue(record.data.filePath); 
		var filenameFormat = Ext.ComponentMgr.get('filenameFormat'); filenameFormat.setValue(record.data.filenameFormat);
		var activeCbx = Ext.ComponentMgr.get('activeCbx');	activeCbx.setValue(record.data.active);
		var includepaymentdata = Ext.ComponentMgr.get('includepaymentdata'); includepaymentdata.setValue(record.data.paymentdata);
		if(isOrder==0) includepaymentdata.disable(); else includepaymentdata.enable();
		
		if (record.data.webhook1url == '')
		{
			Ext.getCmp('webhook1url').setValue('https://');
		}
		else
		{
			Ext.getCmp('webhook1url').setValue(record.data.webhook1url);
		}

		if (record.data.webhook2url == '')
		{
			Ext.getCmp('webhook2url').setValue('https://');
		}
		else
		{
			Ext.getCmp('webhook2url').setValue(record.data.webhook2url);
		}
		
		var langD = Ext.get('langD');
		var langC = Ext.ComponentMgr.get('langC');
		var languagelist = Ext.ComponentMgr.get('languagelist');

		switch(record.data.originalLang){
			case '00': document.getElementById('langC').checked = true; break;
			case 'Default': document.getElementById('langD').checked = true; break;
			default:  {Ext.ComponentMgr.get('languagelist').setValue(record.data.originalLang); document.getElementById('langO').checked = true; }
		} 
		langClicked();
		if(isOrder==0) Ext.ComponentMgr.get('langC').disable();	else Ext.ComponentMgr.get('langC').enable();
		
		Ext.ComponentMgr.get('formatlist').setValue(exportFormatString);

		clearAllInvalid('exportForm');
	};

	var resetForm_fnc = function(){ gDialogObj.hide(); };

	var langClicked = function(){ 
		var languagelistSel = Ext.ComponentMgr.get('languagelist');	
		if(document.getElementById('langO').checked) languagelistSel.enable(); 
		else { languagelistSel.disable(); languagelistSel.clearValue();} 
	};

	function saveCallback(pUpdated, pActionForm, pActionData) 
	{
 		if (pUpdated) {
 			grid.store.reload();
 			gDialogObj.hide();
 		} 
 	};

 	var updateForm_fnc = function(btn, ev)
 	{ 
 		var fp = Ext.getCmp('exportForm'), form = fp.getForm();
		
    	if ((form.isValid())&&(Ext.ComponentMgr.get('languagelist').validate()))
    	{
 			var parameter = new Object();
    		var record = gridCheckBoxSelectionModelObj.getSelected();
			var eventId = record.data.eventId;
			var eventCode = record.data.eventCode;
			var filePath = Ext.ComponentMgr.get('filePath').getValue();
			var filenameFormat = Ext.ComponentMgr.get('filenameFormat').getValue();
			var activeCbx = (Ext.ComponentMgr.get('activeCbx').getValue()==true) ? 1 : 0;
			var includepaymentdata = (Ext.ComponentMgr.get('includepaymentdata').getValue() == true) ? 1 : 0;
			var webhook1url = Ext.getCmp('webhook1url').getValue();
			var webhook2url = Ext.getCmp('webhook2url').getValue();

			webhook1url = (webhook1url === 'http://' || webhook1url === 'https://') ? '' : webhook1url;
			webhook2url = (webhook2url === 'http://' || webhook2url === 'https://') ? '' : webhook2url;
			parameter['webhook1url'] = webhook1url;
			parameter['webhook2url'] = webhook2url;



			var exportFormat = Ext.get('formatlist_hi').getValue();
			parameter['exportformat'] = 'TXT';
			parameter['beautifyxml'] = '0';
			switch(exportFormat){
				case 'XML': parameter['exportformat'] = 'XML'; break;
				case 'TXT': parameter['exportformat'] = 'TXT'; break;
				case 'BXML': parameter['exportformat'] = 'XML';  parameter['beautifyxml'] = 1; break;
			}
		
			parameter['id'] = eventId;
			parameter['eventcode'] = eventCode;
			parameter['subfolder'] = filePath;
			parameter['filename'] = filenameFormat;
			parameter['includepaymentdata'] = includepaymentdata;
			parameter['isactive'] = activeCbx;
			
		
			if(document.getElementById('langC').checked) parameter['languagecode'] = '00';
			else if(document.getElementById('langD').checked) parameter['languagecode'] = 'Default';
			else parameter['languagecode'] = Ext.get('languagelist_hi').getValue();

			if (filePath != '')
			{
				if (!checkFileName(filenameFormat))
				{
					Ext.ComponentMgr.get('filenameFormat').markInvalid();
					return false;
				}

			}
			else if (webhook1url == '' && webhook2url == '')
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoFilePathOrWebhookSpecified#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				return false;
			}
		
			Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=AdminExportEvent.edit&ref='+session_id, savingLabel_txt, saveCallback);
		}
    };

    var gridDataStoreObj = new Ext.data.Store({
		remoteSort: true, 
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminExportEvent.getEventList&ref='+session_id }),
		method:'POST',

		reader: new Ext.data.ArrayReader( { idIndex: 0},
				Ext.data.Record.create([ 
					{name: 'eventId'}, 
					{name: 'eventCode'},  
					{name: 'eventLang'},  
					{name: 'exportFormat'}, 
					{name: 'filePath'},
					{name: 'filenameFormat'},
					{name: 'webhook1url'},
          			{name: 'webhook2url'},
					{name: 'active'}, 
					{name: 'paymentdata'}, 
					{name: 'beautified'}, 
					{name: 'originalLang'}
				])
			)
	}); gridDataStoreObj.load({ });
    
	gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({ 
		listeners: {
			selectionchange: function(gridCheckBoxSelectionModelObj) 
			{ 
				if (gridCheckBoxSelectionModelObj.getCount() > 0)
				{
					if (gridCheckBoxSelectionModelObj.getCount() == 1) 
					{
						grid.editButton.enable(); 
					}
					else 
					{
						grid.editButton.disable(); 
					}
					
					grid.activeButton.enable(); 
					grid.inactiveButton.enable(); 
				} 
				else 
				{
					grid.activeButton.disable(); 
					grid.inactiveButton.disable(); 
					grid.editButton.disable(); 
				}
			}
		}
	});

	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {	sortable: true, resizable: true },
		columns: [gridCheckBoxSelectionModelObj,
            { id:'eventId', header: eventCode_tx, dataIndex: 'eventCode',  width:100,  menuDisabled: true, sortable: false, renderer: isActiveColumnRenderer},
            { header: eventLang_tx, dataIndex: 'eventLang', width:100, menuDisabled: true, sortable: false, renderer: isActiveColumnRenderer},
            { header: exportFormat_tx, dataIndex: 'exportFormat', width:100, menuDisabled: true, sortable: false, renderer: isActiveColumnRenderer},
            { header: filePath_tx, dataIndex: 'filePath', width:200, menuDisabled: true, sortable: false, renderer: isActiveColumnRenderer},
            { header: filenameFormat_tx, dataIndex: 'filenameFormat', width:150, menuDisabled: true, sortable: false, renderer: isActiveColumnRenderer},
            { header: exportWebhook1URLTitle_txt, dataIndex: 'webhook1url', renderer: webhookActiveColumnRenderer, width:100, menuDisabled: true, sortable: false },
            { header: exportWebhook2URLTitle_txt, dataIndex: 'webhook2url', renderer: webhookActiveColumnRenderer, width:100, menuDisabled: true, sortable: false },
            { header: statusLabel_txt, dataIndex: 'active', renderer: activeColumnRenderer, width:80, align:'right', menuDisabled: true, sortable: false }
        ]
	});

	var grid = new Ext.grid.GridPanel({
    	id: 'maingrid',
        store: gridDataStoreObj,
        selModel: gridCheckBoxSelectionModelObj,
        cm: gridColumnModelObj,
        stripeRows: true,
        height: 380, width:900, autoExpandColumn: 6,
        stateful: true,
        enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		autoExpandColumn: 'eventId',
		enableColumnMove:false,
		trackMouseOver:false,
		columnLines:true,
        stateId: 'grid',
        ctCls: 'grid',
        tbar: [{ ref: '../editButton',	text: addLabel_txt,	iconCls: 'silk-pencil',	handler: onEdit, disabled: true	}, '-',
        	   { ref: '../activeButton', text: makeActiveLabel_txt, handler: onActivate, disabled: true, id:'activeButton', iconCls: 'silk-lightbulb'}, '-',
        	   { id:'inactiveButton', ref: '../inactiveButton', text: makeInactiveLabel_txt, handler: onActivate, disabled: true,iconCls: 'silk-lightbulb-off'	}	
        ]      
    });

    function checkFilePath(pValue)
    {
		if ((pValue.indexOf('"') != -1) || (pValue.indexOf("'") != -1) || (pValue.indexOf('?') != -1) || (pValue.indexOf('<') != -1) || (pValue.indexOf('>') != -1) || (pValue.indexOf('|') != -1) || (pValue.indexOf('*') != -1))
		{
			return systemCharacterError;
		}

		return true;
    }
    
    function checkFileName(pValue)
    {
        var rExp = new RegExp("^[A-Za-z0-9 _\.\[\\]\(\)-]+$", "g");
       
        if (pValue != '')
        {
			if(rExp.test(pValue))
			{
				return true;
			} else 
			{
				return systemCharacterError;
			}
		}
		else
		{
			return false;
		}
    }

    gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		title: exportEventsTitle_txt,
		items: grid,
		layout: 'fit',
		anchor: '100% 100%',
		tools: [{id: 'close', handler: function(event, toolEl, panel){ windowClose();  accordianWindowInitialized = false;}, qtip: '{/literal}{#str_LabelCloseWindow#}{literal}' }],
		baseParams: { ref: '{/literal}{$ref}{literal}' }
	});
		
	centreRegion.add(gMainWindowObj);
	centreRegion.doLayout();
	
	editFormPanel = new Ext.FormPanel({
        frame:true, id:'exportForm', layout:'form', width: 500, autoHeight:true, bodyStyle:'padding-bottom: 10px',
        items: [
        	new Ext.Panel({	layout: 'form',	plain:true,	bodyBorder: false, border: false, defaults:{xtype: 'textfield', width: 340}, bodyStyle:'padding:12px 5px 0; border-top: 0px',
				items:[	{ id: 'eventCode', name: 'eventCode', fieldLabel: eventCode_tx,	value: 'eventCode',	post: true,  readOnly:true, allowBlank: false, style:'background:#eaf0f8;',
						  validationEvent:false	} ]
			}),
			new Ext.Panel({ layout: 'form', plain:true,	bodyBorder: false, border: false, defaults:{xtype: 'textfield', width: 340}, bodyStyle:'padding:12px 5px 0; border-top: 0px',
				items:[
        			{ id: 'filePath', name: 'filePath', fieldLabel: filePath_tx, post: true, allowBlank: true, validateOnBlur:true, validator: checkFilePath	}, 
        			{ id: 'filenameFormat', name: 'filenameFormat', fieldLabel: filenameFormat_tx, post: true, allowBlank: true, validateOnBlur:true}
            	]
			}),
			new Ext.Panel({ layout: 'form',	plain:true,	bodyBorder: false, border: false, defaults:{xtype: 'textfield', width: 310}, bodyStyle:'padding:12px 5px 0; border-top: 0px',
				items:[
        			{ xtype: 'radiogroup', fieldLabel: exportLang_tx, columns: 1, width:330,
            		items: [{boxLabel: defaultLang_tx +  langDefaultLabel_tx, name: 'language', id:'langD', inputValue: 'D', checked: true},
            			{boxLabel: orderLang_tx, name: 'language', id:'langC', inputValue: 'C'},

            			{ xtype : 'container', border : false, layout : 'column',  autoHeight:true, width:300,
							items : [ 
								{ xtype : 'container', layout : 'form',  style:'margin-right:10px', width:70,
									items : 
										new Ext.form.Radio({ boxLabel: otherLabel_txt, hideLabel:true, name: 'language', id:'langO', inputValue: 'O' }) 
								},
										{ xtype : 'container', layout : 'form', width:240, 
							    			items : 
								    			new Ext.form.ComboBox({ id: 'languagelist', name: 'languagelist', hiddenName:'languagelist_hn',	hiddenId:'languagelist_hi',	mode: 'local', editable: false,					
							    				forceSelection: true,width:220, valueField: 'lang_id', displayField: 'lang_name', useID: true, post: true, hideLabel:true,	disabled:true,
												store: new Ext.data.ArrayStore({ id: 0, fields: ['lang_id', 'lang_name'],	data: langArray }), triggerAction: 'all', allowBlank: false, validationEvent:false })
							  			}
								]}
            			] } 
            	]
			}),
			new Ext.Panel({	layout: 'form',	plain:true,	bodyBorder: false, border: false, defaults:{xtype: 'textfield', width: 300}, bodyStyle:'padding:5px 5px 0; border-top: 0px',
				items:[
        			new Ext.form.ComboBox({ id: 'formatlist', name: 'formatlist', hiddenName:'formatlist_hn', hiddenId:'formatlist_hi', mode: 'local', fieldLabel:formatLabel_tx,				
						forceSelection: true,width:160, valueField: 'format_id', displayField: 'format_name', useID: true, post: true, allowBlank: false,
						store: new Ext.data.ArrayStore({ id: 0, fields: ['format_id', 'format_name'],	data: exportFormats }), editable:false, triggerAction: 'all', validationEvent:false }),
            		new Ext.form.Checkbox({	fieldLabel: '', id:'includepaymentdata', boxLabel: paymentLabel_tx,   name: 'includepaymentdata' })
				]
			}),
			
			new Ext.Panel({	layout: 'form',	plain:true,	bodyBorder: false, border: false, defaults:{xtype: 'textfield', width: 300}, bodyStyle:'padding:5px 5px 0; border-top: 0px',
				items:
				[
					{
						xtype: 'textfield',
						id: 'webhook1url',
						name: 'webhook1url',
						fieldLabel: exportWebhook1URLTitle_txt,
						value: '',
						validateOnBlur: true,
						post: true,
						validator: function(v){ return validateUrl(this); }
					},
					{
						xtype: 'textfield',
						id: 'webhook2url',
						name: 'webhook2url',
						fieldLabel: exportWebhook2URLTitle_txt,
						value: '',
						validateOnBlur: true,
						post: true,
						validator: function(v){ return validateUrl(this); }
					},
       			]
			})
		]
    });

 	var validateUrl = function(obj)
	{
		if (obj.getValue() != '' && obj.getValue() != 'http://' && obj.getValue() != 'https://')
		{
			var domainValid = (Ext.form.VTypes.url(obj.getValue()));
			var regexp = /^((([hH][tT][tT][pP][sS]?|[fF][tT][pP])\:\/\/)?([\w\.\-]+(\:[\w\.\&%\$\-]+)*@)?((([^\s\(\)\<\>\\\"\.\[\]\,@;:]+)(\.[^\s\(\)\<\>\\\"\.\[\]\,@;:]+)*(\.[a-zA-Z]{2,4}))|((([01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}([01]?\d{1,2}|2[0-4]\d|25[0-5])))(\b\:(6553[0-5]|655[0-2]\d|65[0-4]\d{2}|6[0-4]\d{3}|[1-5]\d{4}|[1-9]\d{0,3}|0)\b)?((\/[^\/][\w\.\,\?\'\\\/\+&%\$#\=~_\-@]*)*[^\.\,\?\"\'\(\)\[\]!;<>{}\s\x7F-\xFF])?)$/;
			var ipValid = regexp.test(obj.getValue());
			if (domainValid || ipValid) return true; else return false;
		}
		obj.clearInvalid();
		return true;
	};

	gDialogObj = new Ext.Window({
		  id: 'dialog',
		  title: editLabel_txt,
		  closable:false,
		  closeAction: 'hide',
		  frame: true,
		  resizable:false,
		  modal:true,
		  layout: 'fit',
		  width:500,
		  autoHeight:true,
		  items: editFormPanel,
		  cls: 'left-right-buttons',
		  buttons: [ new Ext.form.Checkbox({ fieldLabel: '', id:'activeCbx', boxLabel: activeLabel_tx,   name: 'activeCbx' , hideLabel:true, cls: 'x-btn-left', ctCls: 'width_100'}), { text: buttonCancel_tx, handler:resetForm_fnc, cls: 'x-btn-right' },{ text: buttonUpdate_tx, handler:updateForm_fnc, cls: 'x-btn-right' }]
	});

}

/* close this window panel */
function windowClose()
{
	if (Ext.getCmp('dialog'))
	{
		Ext.getCmp('dialog').close();
	}
	centreRegion.remove('MainWindow', true);
	centreRegion.doLayout();
}

{/literal}