{literal}

function initialize(pParams)
{
	{/literal}
	var formTitle_tx = "{#str_ExportTitleManual#}";
	var startDate_tx = "{#str_LabelStartDate#}";
	var endDate_tx = "{#str_LabelEndDate#}";
	var searchFor_tx = "{#str_LabelDateType#}";
	var filterBy_tx = "{#str_LabelFilter#}";
	var ordersReceived_tx = "{#str_LabelOrdersReceived#}";
	var ordersPrinted_tx = "{#str_LabelOrdersPrinted#}";
	var ordersShipped_tx = "{#str_LabelOrdersShipped#}";
	var filterNone_tx = "{#str_LabelNone#}";
	var filterBrand_tx = "{#str_LabelBrand#}";
	var filterLicence_tx = "{#str_LabelLicenseKey#}";
	var language_tx = "{#str_LabelLanguage#}";
	var defaultLanguageName_tx = "{$defaultlanguagename}";
	var defaultLanguage_tx = "{#str_LabelLangDefault#}";
	var orderLanguage_tx = "{#str_LabelLangOrder#}";
	var exportFormat_tx = "{#str_LabelFormat#}";
	var formatXML_tx = "{#str_LabelFormatXML#}";
	var formatTXT_tx = "{#str_LabelFormatTXT#}";
	var paymentData_tx = "{#str_LabelPayment#}";
	var beautifyXML_tx = "{#str_LabelXmlBeautify#}";
	var buttonReset_tx = "{#str_ButtonReset#}";
	var buttonExport_tx = "{#str_ButtonExport#}";
	var buttonSummary_tx = "{#str_ButtonSummary#}";
	var otherLabel_txt = "{#str_Other#}";
	var startDateLaterEndDateLabel_txt = "{#str_LabelStartDateLaterEndDate#}";
	var invalidDateFormatLabel_txt = "{#str_LabelInvalidDateFormat#}";
	var runReportLabel_txt = "{#str_LabelRunReport#}";
	var noItemsFoundLabel_txt = "{#str_LabelNoItemsFound#}";
	var orderNumber_tx = "{#str_LabelOrderNumber#}";
	var orderDate_tx = "{#str_LabelOrderDate#}";
	var shippingDate_tx = "{#str_LabelShippedDate#}";
	var productName_tx = "{#str_LabelProduct#}";
	var quantity_tx = "{#str_LabelQuantity#}";
	var branding_tx = "{#str_SectionTitleBranding#}";
	var orderTotal_tx = "{#str_LabelOrderTotal#}";
	var billingAddress_tx = "{#str_LabelBillingAddress#}";
	var shippingAddress_tx = "{#str_LabelShippingAddress#}";
	var exportParamsLabel_txt = "{#str_LabelExportParameters#}";
	var exportFormatsLabel_txt = "{#str_LabelExportFormats#}";
	companyCode_tx = "{#str_LabelCompanyName#}";
	var startDate_val = "{$startdate}";
	var endDate_val = "{$enddate}";
	var actionMessage = "{$message}";
	var gDateFormat = "{$dateformat}";
	var session_id = "{$ref}";
	var brandArray = {$brandlist};
	var licenceArray = {$licensekeylist};
	var langArray = {$languagelist};
	var searchOptions = [];
	var companyList = {$companyList};
	var statusLabel_txt = "{#str_LabelStatus#}";
	var errorConnectLabel_txt = "{#str_ErrorConnectFailure#}";
	var exportSearch = [['OR',ordersReceived_tx],['OP',ordersPrinted_tx],['OS',ordersShipped_tx]];
	var exportFormats = [['XML',formatXML_tx],['BXML',beautifyXML_tx],['TXT',formatTXT_tx]];
	{literal}
	
	var init = function() 
	{
		var startdt = document.getElementById('startdt');
		var enddt = document.getElementById('enddt');
		var dateOR = document.getElementById('dateOR');
		var filN = document.getElementById('filN');
		var langD = document.getElementById('langD');
		if (startdt) startdt.value = startDate_val;
		if (enddt) enddt.value = endDate_val;
		if (filN) filN.checked = true;
		if (langD) langD.checked = true;
		var brandlistSel = Ext.ComponentMgr.get('brandlist');
 		var licensekeylistSel = Ext.ComponentMgr.get('licensekeylist');
 		var languagelistSel = Ext.ComponentMgr.get('languagelist');
 		if (brandlistSel) brandlistSel.disable(); 
  		
 		licensekeylistSel.disable(); 
 		languagelistSel.disable();   

 		var datetypeSel = Ext.ComponentMgr.get('datetype');
 		datetypeSel.setValue('OR');

		var companyFilterSel = Ext.getCmp('companyFilter');
		if (companyFilterSel)
		{
			companyFilterSel.store.reload();
			
		}
 	};
 	
 	var resetForm_fnc = function()
 	{
 		document.getElementById('exportForm').getElementsByTagName('form')[0].reset();
 		Ext.getCmp('exportForm').getForm().clearInvalid();
 		init();
 	};
 	
 	var filterClicked = function()
 	{
 		var brandlistSel = Ext.ComponentMgr.get('brandlist');
 		var licensekeylistSel = Ext.ComponentMgr.get('licensekeylist');
 		if(this.id == 'brand'){ if (brandlistSel) brandlistSel.enable(); licensekeylistSel.disable(); licensekeylistSel.clearValue(); }
 		else if(this.id == 'groupcode'){ if (brandlistSel) brandlistSel.disable(); licensekeylistSel.enable(); if (brandlistSel) brandlistSel.clearValue(); }
 		else { if (brandlistSel) brandlistSel.disable(); licensekeylistSel.disable(); if (brandlistSel) brandlistSel.clearValue(); licensekeylistSel.clearValue(); }
 		if (brandlistSel) brandlistSel.clearInvalid();
 		licensekeylistSel.clearInvalid();
 	};
 	
 	var langClicked = function()
 	{
 		var languagelistSel = Ext.ComponentMgr.get('languagelist');
 		if(this.id == 'langO') languagelistSel.enable();
 		else { languagelistSel.disable(); languagelistSel.clearValue(); }
 		languagelistSel.clearInvalid();
 	};
 	
 	var formatClicked = function()
 	{
 		var beautifyxmlCbx = Ext.ComponentMgr.get('beautifyxml');
 		if(document.getElementById('formatXML').checked) beautifyxmlCbx.enable();
 		else beautifyxmlCbx.disable();
 	};

	function dateValidation()
	{
		var start = Ext.get('startdt'); 
    	var end = Ext.get('enddt');     

		try
		{
    		var startVal = formatPHPDate(start.getValue(), gDateFormat, "yyyy-MM-dd").split('-'); 
        	var endVal = formatPHPDate(end.getValue(), gDateFormat, "yyyy-MM-dd").split('-');     
        	var startDate = new Date(startVal[0],startVal[1],startVal[2]);
        	var endDate = new Date(endVal[0],endVal[1],endVal[2]);

        	if(startDate>endDate) return startDateLaterEndDateLabel_txt;  
        	else {Ext.getCmp('startdt').clearInvalid(); Ext.getCmp('enddt').clearInvalid(); return true;}
		} 
		catch(err)
		{
			return invalidDateFormatLabel_txt.replace('^0',gDateFormat);
		}

    	start.clearInvalid(); end.clearInvalid(); return true;
	};
	
	var summaryForm_fnc = function(btn,ev)
	{ 
		var form = Ext.ComponentMgr.get('exportForm').getForm(); 
 		if ((form.isValid()) && (Ext.ComponentMgr.get('licensekeylist').validate()) && (Ext.ComponentMgr.get('languagelist').validate()))
 		{
 			if ((Ext.ComponentMgr.get('brandlist')) && (!Ext.ComponentMgr.get('brandlist').validate())) 
			{
				return false;
			}
			
 			var filterSummaryString = Ext.get('filN').up('div.x-form-check-wrap').child('label', true).innerHTML;
 			var brandCbx = document.getElementById('brand'); 
			var groupcodeCbx = document.getElementById('groupcode'); 
			var brandlistVal = ''; if (Ext.get('brandlist_hi')) brandlistVal = Ext.get('brandlist_hi').getValue();
			
			var licensekeylistVal = Ext.get('licensekeylist_hi').getValue();

			brandListText = ''; 
			if (Ext.get('brandlist')) 
			{
				brandListText = Ext.get('brandlist').getValue();
			}
			
			if ((brandCbx) && (brandCbx.checked)) 
			{ 
				if (Ext.get('brand')) 
				{
					filterSummaryString =  Ext.get('brand').up('div.x-form-check-wrap').child('label', true).innerHTML + ' ('+ brandListText + ')';
				}
			}
			else 
			{
				if (groupcodeCbx.checked)  
				{ 
					filterSummaryString = Ext.get('groupcode').up('div.x-form-check-wrap').child('label', true).innerHTML +' ('+Ext.get('licensekeylist').getValue() +')'; 
				}
			}

			grid.store.load();
 			gDialogObj.setTitle(buttonSummary_tx + ' - ' + Ext.get('datetype').getValue() + ' | '+filterBy_tx+': ' +filterSummaryString+ '') ;
			gDialogObj.show();	
			gDialogObj.center(); 
			if (Ext.get('maingrid'))Ext.get('maingrid').unmask();

			var startDateLabel = document.getElementById('startDateLabel'); 
			var endEndLabel = document.getElementById('endEndLabel'); 
			startDateLabel.innerHTML = '&nbsp;&nbsp;<b>'+Ext.get('startdt').getValue() +'</b>';
			endEndLabel.innerHTML = '&nbsp;&nbsp;<b>'+Ext.get('enddt').getValue() +'</b>';
            return true;
		}
        else
        {
            return false;
        }    
 	};


	var exportForm_fnc = function(btn,ev)
	{
 		var startdateVal = Ext.get('startdt').getValue();
 		var enddateVal = Ext.get('enddt').getValue();
 		startdateVal = formatPHPDate(startdateVal, gDateFormat, "yyyy-MM-dd");
    	enddateVal = formatPHPDate(enddateVal, gDateFormat, "yyyy-MM-dd");
 	
 		var filtertype = '', filtervalue = '';
 		var brandCbx = document.getElementById('brand'); 
 		var groupcodeCbx = document.getElementById('groupcode'); 
 		var brandlistVal = ''; if (Ext.get('brandlist_hi')) brandlistVal = Ext.get('brandlist_hi').getValue();

 		var licensekeylistVal = Ext.get('licensekeylist_hi').getValue();
 		if ((brandCbx) && (brandCbx.checked)) { filtertype = brandCbx.value; filtervalue = brandlistVal; } 
    	else if (groupcodeCbx.checked)  { filtertype = groupcodeCbx.value; filtervalue = licensekeylistVal; }
 	
 		var languagecode = '00';
 		var languagelistVal = Ext.get('languagelist_hi').getValue();
 		var langOCbx = document.getElementById('langO'); 
 		var langDCbx = document.getElementById('langD'); 
 		if (langOCbx.checked) { languagecode = languagelistVal; } 
    	else if (langDCbx.checked)  { languagecode = defaultLanguageName_tx; }
 	 	
 		var datetype = Ext.get('datetype_hi').getValue();
 	 	
 	 	var beautifyxml = 0;
 	 	switch(this.id)
 	 	{
 	 		case 'formatXML': exportformat = 'XML';break;
 	 		case 'formatBXML': exportformat = 'XML'; beautifyxml = 1; break;
 	 		case 'formatTXT': exportformat = 'TXT'; break;
 	 	}
 	 	
 	 	var includepaymentdataCbx = document.getElementById('includepaymentdata'); 
 		var includepaymentdata = includepaymentdataCbx.checked ? 1 : 0; 

 		var companyCode = '';
		var companyCombo = Ext.getCmp('companyFilter');
		if (companyCombo) companyCode = companyCombo.getValue();
		
 		var params = 'startdate='+escape(startdateVal)+'&enddate='+escape(enddateVal) + '&filtertype='+escape(filtertype) + '&filtervalue='+escape(filtervalue) + 
 			 '&languagecode='+escape(languagecode) + '&datetype='+escape(datetype) + '&exportformat='+escape(exportformat) + '&includepaymentdata='+escape(includepaymentdata)+
 			 '&beautifyxml='+escape(beautifyxml) + '&companyFilter='+escape(companyCode);
 		location.replace('index.php?fsaction=AdminExportManual.export&ref='+session_id+'&'+params);
 		
  	};
  	
  	var onCompanyChange = function(value)
  	{
		var conn = new Ext.data.Connection();
		var companyFilter = Ext.getCmp('companyFilter');
		
		conn.request({
		    url: "./?fsaction=AjaxAPI.callback&cmd=BRANDCOMBO&ref="+session_id+"&companyCode=" + companyFilter.getValue()+ "&rand=" + Math.floor(Math.random()*11),
		    method: 'GET',
		    params: {},
		    success: function(responseObject) 
		    {
				var brandList = eval('(' + responseObject.responseText + ')');
				if (Ext.getCmp('brandlist'))
				{ 
					Ext.getCmp('brandlist').reset();
					Ext.getCmp('brandlist').store.loadData(brandList);
				}
		    },
		    failure: function() { Ext.MessageBox.show({ title: statusLabel_txt, msg: errorConnectLabel_txt, buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR }); }
		});

		conn.request({
		    url: "./?fsaction=AjaxAPI.callback&cmd=LICENSECOMBO&ref="+session_id+"&companyCode=" + companyFilter.getValue()+ "&rand=" + Math.floor(Math.random()*11),
		    method: 'GET',
		    params: {},
		    success: function(responseObject) 
		    {
				var licenseList = eval('(' + responseObject.responseText + ')');
				Ext.getCmp('licensekeylist').reset();
				Ext.getCmp('licensekeylist').store.loadData(licenseList);
		    },
		    failure: function() {Ext.MessageBox.show({ title: statusLabel_txt, msg: errorConnectLabel_txt, buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR }); }
		});

  	};
	
  	exportFormPanel = new Ext.FormPanel({
        frame:true, id:'exportForm', layout:'form', padding:0, bodyStyle:'padding:0; margin:0;', margins:0, defaults: { labelWidth: 120 },
        items: [  
			new Ext.Panel({	layout: 'form',	plain:true,	bodyBorder: false, border: false, defaults:{xtype: 'textfield', width: 200, labelWidth: 120}, bodyStyle:'padding:5px 2px 5px 4px; border-top: 0px;',
				items:[	
					new Ext.form.DateField({ fieldLabel: startDate_tx, name: 'startdate', id: 'startdt',validator: function(v){ return dateValidation();  }, validateOnBlur:true, endDateField: 'enddt',     format: gDateFormat }), 
					new Ext.form.DateField({ fieldLabel: endDate_tx,   name: 'enddate',   id: 'enddt',  validator: function(v){ return dateValidation();  }, validateOnBlur:true, startDateField: 'startdt', format: gDateFormat })
				]
			}),

			new Ext.Panel({	layout: 'form',	plain:true,	bodyBorder: false, border: false, defaults:{xtype: 'textfield', width: 270}, bodyStyle:'padding:12px 5px 0; border-top: 0px;',
				items:[	
					new Ext.form.ComboBox({ id: 'datetype', name: 'datetype', hiddenName:'datetype_hn',	width:170, allowBlank: false,
						forceSelection: true, editable: false,
						mode: 'local', 	hiddenId:'datetype_hi', 
						store: new Ext.data.ArrayStore({ id: 0, fields: ['format_id', 'format_name'], data: exportSearch }), validationEvent:false,
						valueField: 'format_id', displayField: 'format_name', useID: true, post: true, fieldLabel:searchFor_tx, triggerAction: 'all'
					})
				]
			}),

			/* editable: false,  */
			{/literal}{if $optionMS == true and $userType==TPX_LOGIN_SYSTEM_ADMIN}{literal} 
			new Ext.Panel({	layout: 'form',	plain:true,	bodyBorder: false, border: false, defaults:{xtype: 'textfield', width: 270}, bodyStyle:'padding:12px 5px 0; border-top: 0px;',
				items:[	
					new Ext.taopix.CompanyCombo({ validationEvent:false, editable: false, forceSelection: true, allowBlank: false, fieldLabel: companyCode_tx, hideLabel: false, 
						id: 'companyFilter', name: 'companyFilter', emptyText:'', options:{storeId: 'companystore', ref: session_id, includeGlobal: '0', includeShowAll:'1', 
						onchange: onCompanyChange }
					})
				]
			}),
			{/literal}{/if}{literal}
			
			new Ext.Panel({	layout: 'form',	plain:true,	bodyBorder: false, border: false, defaults:{xtype: 'textfield', width: 200}, bodyStyle:'padding:12px 5px 0; border-top: 0px;', autoHeight:true,
				items:[	
					{ xtype: 'radiogroup', columns: 1,  fieldLabel: filterBy_tx, autoWidth:true, 
					items: [
    					{boxLabel: filterNone_tx, name: 'filter', inputValue: 'N', id: 'filN', checked: true},

    					{/literal}{if $userType != TPX_LOGIN_BRAND_OWNER}{literal} 
    					{ xtype : 'container', border : false, layout : 'column', labelStyle :'width:auto;', 
						items : [
							{ xtype : 'container', layout : 'form', width:100, style:'margin-right:10px;',
								items : new Ext.form.Radio({ hideLabel:true, boxLabel: filterBrand_tx, name: 'filter', inputValue: 'BRAND', id: 'brand' })  
							},
							{ xtype : 'container', layout : 'form', width:340,
								items : new Ext.form.ComboBox({ id: 'brandlist', name: 'brandlist', hiddenName:'brandlist_hn',	validationEvent:false,
									mode: 'local', editable: false,	forceSelection: true, hideLabel:true, hiddenId:'brandlist_hi',
									store: new Ext.data.ArrayStore({ id: 0, fields: ['brand_id', 'brand_name'],	data: brandArray }),
									valueField: 'brand_id', displayField: 'brand_name',	useID: true, post: true, width:320,
									allowBlank: false, triggerAction: 'all'
								})
							}
						]},
						{/literal}{/if}{literal}
						
						{ xtype : 'container', border : false, layout : 'column', autoHeight:true,
						items : [
							{ xtype : 'container', layout : 'form', width:100, style:'margin-right:10px;', autoHeight:true,
								items : new Ext.form.Radio({ hideLabel:true, boxLabel: filterLicence_tx, name: 'filter', inputValue: 'GROUPCODE', id: 'groupcode',autoHeight:true })  
							},
							{ xtype : 'container', layout : 'form', width:340,
								items : new Ext.form.ComboBox({ id: 'licensekeylist', name: 'licensekeylist', hiddenName:'licensekeylist_hn', validationEvent:false, 
									hiddenId:'licensekeylist_hi', mode: 'local', editable: false, forceSelection: true, hideLabel:true,
									store: new Ext.data.ArrayStore({ id: 0, fields: ['licence_id', 'licence_name'],	data: licenceArray }),
									valueField: 'licence_id', displayField: 'licence_name', useID: true, post: true, width:320,
									allowBlank: false, triggerAction: 'all'
								})
							}
						]}
					]}
				]
			}),
			new Ext.Panel({	layout: 'form',	plain:true,	bodyBorder: false, border: false, defaults:{xtype: 'textfield', width: 150}, bodyStyle:'padding:12px 5px 0; border-top: 0px;',
				items:[	
					{ xtype: 'radiogroup', fieldLabel:language_tx ,columns: 1, autoWidth:true,
					items: [
    					{boxLabel: defaultLanguageName_tx +  defaultLanguage_tx, name: 'language', id:'langD', inputValue: 'D', checked: true},
						{boxLabel: orderLanguage_tx, name: 'language', id:'langC', inputValue: 'C'},
						{ xtype : 'container', border : false, layout : 'column',  autoHeight:true,
						items : [
							{ xtype : 'container', layout : 'form', width:100, style:'margin-right:10px;',
								items : new Ext.form.Radio({ boxLabel: otherLabel_txt, hideLabel:true, name: 'language', id:'langO', inputValue: 'O' })  
							},
							{ xtype : 'container', layout : 'form', width:340,
								items : new Ext.form.ComboBox({ id: 'languagelist', name: 'languagelist', hiddenName:'languagelist_hn', validationEvent:false,
								hiddenId:'languagelist_hi',	mode: 'local', editable: false,	 forceSelection: true,width:320,
								store: new Ext.data.ArrayStore({ id: 0, fields: ['lang_id', 'lang_name'],	data: langArray }),
								valueField: 'lang_id', displayField: 'lang_name', useID: true, post: true, hideLabel:true,
								allowBlank: false, triggerAction: 'all'
								})
							}
						]}
					]}
				]
			})
        ]
    });
    
	
	gMainWindowObj = new Ext.Window({
		  id: 'MainWindow',
		  title: formTitle_tx,
		  closable:true,
		  width:615,
		  height:405,
		  layout: 'fit',
		  resizable:false,
		  padding:0, margin:0,
		  listeners: {
				'close': {   
					fn: function(){
			  			accordianWindowInitialized = false;
			  			if (Ext.getCmp('dialog'))
						{
							Ext.getCmp('dialog').close();
						}
					}
				}
			},
		  baseParams: { ref: '{/literal}{$ref}{literal}' },
		  items: [exportFormPanel ],
	      buttons: [ { text: buttonReset_tx, handler:resetForm_fnc },{ text: runReportLabel_txt, handler:summaryForm_fnc }]
	});

	gMainWindowObj.show();
 	

	init();
	
 	document.getElementById('filN').onclick = document.getElementById('groupcode').onclick = filterClicked;
 	if (document.getElementById('brand')) document.getElementById('brand').onclick = document.getElementById('filN').onclick ;
	document.getElementById('langD').onclick = document.getElementById('langC').onclick = document.getElementById('langO').onclick = langClicked;
	
	if (Ext.get('brand')) Ext.get('brand').up('div.x-form-item').dom.style.width = Ext.get('langO').up('div.x-form-item').dom.style.width = Ext.get('groupcode').up('div.x-form-item').dom.offsetWidth;

	
	var dsOnLoad = function()
	{
		if (this.getCount()==0) 
		{
			var grid = Ext.get('maingrid');
			if (grid)
			{ 
				var mask = Ext.get('maingrid').mask(noItemsFoundLabel_txt,'maskClass'); 
			}
		}
		else 
		{
			if (Ext.get('maingrid')) Ext.get('maingrid').unmask();
		}
	};
	
	var dsOnBeforeLoad = function(store, options)
	{
		var startdateVal = Ext.get('startdt').getValue();
		var enddateVal = Ext.get('enddt').getValue();
		startdateVal = formatPHPDate(startdateVal, gDateFormat, "yyyy-MM-dd");
		enddateVal = formatPHPDate(enddateVal, gDateFormat, "yyyy-MM-dd");
 		var datetype = Ext.get('datetype_hi').getValue();
 		var licensekeylistVal = Ext.get('licensekeylist_hi').getValue();
		
		var companyCode = '';
		var companyCombo = Ext.getCmp('companyFilter');
		if (companyCombo)
		{
			companyCode = companyCombo.getValue();
		}
		else
		{
			companyCode = '{/literal}{$companycode}{literal}';
		}
		
		var filtertype = '', filtervalue = '';
		var brandCbx = document.getElementById('brand'); 
		var groupcodeCbx = document.getElementById('groupcode');
		var brandlistVal = ''; if (Ext.get('brandlist_hi')) brandlistVal = Ext.get('brandlist_hi').getValue();
		if ((brandCbx) && (brandCbx.checked)) 
		{ 
			filtertype = brandCbx.value; 
			filtervalue = brandlistVal;  
		}
		else 
		{
			if (groupcodeCbx.checked)  
			{ 
				filtertype = groupcodeCbx.value; 
				filtervalue = licensekeylistVal; 
			}
		}
		
		this.baseParams['limit'] = 50;
		this.baseParams['startDate'] = startdateVal;
		this.baseParams['endDate'] = enddateVal;
		this.baseParams['dateType'] = datetype;
		this.baseParams['filterType'] = filtertype;
		this.baseParams['filterValue'] = filtervalue;
		this.baseParams['companyFilter'] = companyCode;
		
		return true;
			
	};

		
 	var gridDataStoreObj = new Ext.data.Store({
		remoteSort: true, 
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminExportManual.report&ref='+session_id }),
		method:'POST',
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([ 
				{name: 'ordernumber', mapping: 0},
				{name: 'orderdate', mapping: 1 },  
				{name: 'productname', mapping: 2},
				{name: 'webbrandcode', mapping: 3},
				{name: 'total', mapping: 4},
				{name: 'shippeddate', mapping: 5},
				{name: 'qty', mapping: 6},
				{name: 'billingaddress', mapping: 7},
				{name: 'shippingaddress', mapping: 8}
			])
		),
		sortInfo:{field: 'ordernumber', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});
    gridDataStoreObj.on("load", dsOnLoad);
    gridDataStoreObj.on("beforeload", dsOnBeforeLoad);
    
	var gridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: { sortable: true, resizable: true },
		columns: [
			{header: orderNumber_tx, dataIndex: 'ordernumber', width:100},
			{header: orderDate_tx,dataIndex: 'orderdate', align: "right"},
			{header: productName_tx, dataIndex: 'productname', width:150},
			{header: branding_tx, dataIndex: 'webbrandcode'},
			{header: orderTotal_tx, dataIndex: 'total', align: "RIGHT", width:80},
			{header: shippingDate_tx, dataIndex: 'shippeddate', align: "right"},
			{header: quantity_tx, dataIndex: 'qty', width:80},
			{header: billingAddress_tx, dataIndex: 'billingaddress', sortable: false, width:150, menuDisabled: true	 },
			{header: shippingAddress_tx, dataIndex: 'shippingaddress', sortable: false, width:150, menuDisabled: true}
		]
	});
	gridColumnModelObj.defaultSortable= true;
		 
 	grid = new Ext.grid.GridPanel({
		id: 'maingrid', 
		store: gridDataStoreObj,
		cm: gridColumnModelObj,
		enableColLock:false,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		trackMouseOver:false,
		stripeRows:true,
		columnLines:true,
		ctCls: 'grid',
		cls: 'summaryGrid',
		tbar: [{
				xtype: 'buttongroup',
				cls: 'x-btn-group-ribbonstyle',
				/*fbar: [exportParamsLabel_txt],*/
				columns: 1,height:65,
            	defaults: { scale: 'small' },
            	html: '<div class="tbarText" id="tbarText"><p style="padding:10px"><span style="text-align:left; width:145px; "><label>'+startDate_tx+':</label><label id="startDateLabel"></label></span><span class="float" style="text-align:right;width:145px"><label>'+endDate_tx+':</label><label id="endEndLabel"></label></span></p><p style="clear:both; padding-left:10px"><input type="checkbox" name="includepaymentdata" id="includepaymentdata"><label for="includepaymentdata" style="margin-left:5px">'+paymentData_tx+'</label></p></div>'
    		},
    		{
				xtype: 'buttongroup',
				cls: 'x-btn-group-ribbonstyle',
				/*fbar: [exportFormatsLabel_txt],*/
				layout: 'column',columns: 3, height:65, defaults: { scale: 'large', width: 100, height:28 }, width: 640, style:"padding-left:5px;",
				items: [
            			{ xtype: 'tbbutton', text: formatXML_tx, iconCls: 'xml_pic', handler : exportForm_fnc, id:'formatXML', style:"padding-right:20px; margin-top:10px" },
            			{ xtype: 'tbbutton', text: beautifyXML_tx, iconCls: 'bxml_pic', handler : exportForm_fnc, id:'formatBXML', style:"padding-right:20px; margin-top:10px" }, 
            			{ xtype: 'tbbutton', text: formatTXT_tx, iconCls: 'txt_pic', handler : exportForm_fnc, id:'formatTXT', style:"padding-right:20px; margin-top:10px" }
            		]
    		}
		],
		bbar: new Ext.PagingToolbar({ 
			pageSize: 50,
			store: gridDataStoreObj,
			displayInfo: true
		})
	});
	
	var tabs = new Ext.Panel({
    	region: 'center',
    	layout:'fit',
        items:grid
   });

   var gDialogObj = new Ext.Window({
	  id: 'dialog',
	  title: buttonSummary_tx,
	  plain:true,
	  modal:true,
	  closeable:false,
	  draggable:true,
	  resizable: false,
	  layout: 'border',
	  width:1060,
	  height:550,
	  closeAction:'hide',
	  shadow:false,
	  items: [tabs]
	});
}

/* close this window panel */
function windowClose()
{
	if (Ext.getCmp('dialog'))
	{
		Ext.getCmp('dialog').close();
	}
	
	if ((gMainWindowObj) && (gMainWindowObj.close))
	{
		gMainWindowObj.close();
	}
}
{/literal}
