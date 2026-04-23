{literal}
function initialize(pParams)
{		
	function priceListSaveCallback(pUpdated, pActionForm, pActionData)
	{	
		if (pUpdated)
		{
			var gridObj = Ext.getCmp('pricelistgrid');
			var dataStore = gridObj.store;	
			gridObj.store.reload();
			Ext.getCmp('pricelistadddialog').close();
		}
		else
		{
			icon = Ext.MessageBox.WARNING;
			
			Ext.MessageBox.show({
				title: pActionData.result.title,
				msg: pActionData.result.msg,
				buttons: Ext.MessageBox.OK,
				icon: icon
			});
		}
	}
	
	function priceListAddSaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminComponentsPricing.addPriceList&ref={/literal}{$ref}{literal}&id=0';
		var fp = Ext.getCmp('pricelistaddform'), form = fp.getForm();
		var submit = true;
	
		var paramArray = new Object();
		paramArray['isactive'] = '';
	
		if (Ext.getCmp('ispricelistactive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}
	
		var gridObj = Ext.getCmp('maingrid');
		var selRecords = gridObj.selModel.getSelections();
		var pricingModel = selRecords[0].data.pricingmodel;
		var category = selRecords[0].data.code;
		
		if ((pricingModel == 7) || (pricingModel == 8))
		{
			if (Ext.getCmp('fixedquantityrange').checked)
			{
				paramArray['quantitytypeisdropdown'] = '1';
			}
			else
			{
				paramArray['quantitytypeisdropdown'] = '0';
			}
		}
		else
		{
			paramArray['quantitytypeisdropdown'] = '0';
		}
		
		paramArray['pricingmodel'] = pricingModel;
		paramArray['categorycode'] = category;
		
		if (Ext.getCmp('price').isValid() != 0)
		{    	
    		switch (Ext.getCmp('price').isValid())
    		{
    			case 1:
    				ERRORMSG = "{/literal}{#str_ErrorRangeStartError1#}{literal}";
    				break;
    			case 2:
    				ERRORMSG = "{/literal}{#str_ErrorRangeStartError1#}{literal}";
        			break;
    			case 3:
    				ERRORMSG = "{/literal}{#str_ErrorRangeEndError#}{literal}";
        			break;
    			case 4:
    				ERRORMSG = "{/literal}{#str_ErrorRangeStartError2#}{literal}";
        			break;
    			case 5:
    				ERRORMSG = "{/literal}{#str_ErrorComponentPriceError#}{literal}";
        			break;
    			case 6:
    				ERRORMSG = "{/literal}{#str_ErrorProductPriceError#}{literal}";
        			break;
    			case 7:
    				ERRORMSG = "{/literal}{#str_ErrorRangeEndLimitError#}{literal}";
        			break;        			
	    	}
    	
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: ERRORMSG, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
		
			submit = false;
		}
		
		if (!Ext.getCmp('pricelistaddform').getForm().isValid())
		{
			submit = false;
		}
		
		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", priceListSaveCallback);
		}
	}

	function priceListEditSaveHandler(btn, ev)
	{
		var priceListID = Ext.taopix.gridSelection2IDList(Ext.getCmp('pricelistgrid'));
		
		var submitURL = 'index.php?fsaction=AdminComponentsPricing.priceListEdit&ref={/literal}{$ref}{literal}&id=' + priceListID;
		var fp = Ext.getCmp('pricelistaddform'), form = fp.getForm();
		var submit = true;
	
		var paramArray = new Object();
		paramArray['isactive'] = '';
		
		if (Ext.getCmp('ispricelistactive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}
		
		var pricingModel = selRecords[0].data.pricingmodel;
		
		if ((pricingModel == 7) || (pricingModel == 8))
		{
			if (Ext.getCmp('fixedquantityrange').checked)
			{
				paramArray['quantitytypeisdropdown'] = '1';
			}
			else
			{
				paramArray['quantitytypeisdropdown'] = '0';
			}
		}
		else
		{
			paramArray['quantitytypeisdropdown'] = '0';
		}
		
		if (Ext.getCmp('price').isValid() != 0)
		{    	
    		switch (Ext.getCmp('price').isValid())
    		{
    			case 1:
    				ERRORMSG = "{/literal}{#str_ErrorRangeStartError1#}{literal}";
    				break;
    			case 2:
    				ERRORMSG = "{/literal}{#str_ErrorRangeStartError1#}{literal}";
        			break;
    			case 3:
    				ERRORMSG = "{/literal}{#str_ErrorRangeEndError#}{literal}";
        			break;
    			case 4:
    				ERRORMSG = "{/literal}{#str_ErrorRangeStartError2#}{literal}";
        			break;
    			case 5:
    				ERRORMSG = "{/literal}{#str_ErrorComponentPriceError#}{literal}";
        			break;        			
    			case 6:
    				ERRORMSG = "{/literal}{#str_ErrorProductPriceError#}{literal}";
        			break;        			
    			case 7:
    				ERRORMSG = "{/literal}{#str_ErrorRangeEndLimitError#}{literal}";
        			break;        			
	    	}
    	
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: ERRORMSG, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
		
			submit = false;
		}
		
		if (!Ext.getCmp('pricelistaddform').getForm().isValid())
		{
			submit= false;
		}
		
		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", priceListSaveCallback);
		}	
	}
	
	function validate(value, allowDecimal, allowNegative)
	{
		if (! isNumeric(value, true, false))
		{
			valid = false;
		}
		else
		{
			valid = true;
		}
	
		return valid;
	}

	function forceAlphaNumeric()
	{
		var code = Ext.getCmp('componentpricelistcode').getValue();
    	code = code.toUpperCase();
   	 	code = code.replace(/[^A-Z_0-9\-]+/g, "");
    	Ext.getCmp('componentpricelistcode').setValue(code);
	}
		
	var companyCombo = new Ext.taopix.CompanyCombo({
		id: 'companycode',
		name: 'companycode',
		width:300,
		fieldLabel: "{/literal}{#str_LabelCompany#}{literal}",
		hideLabel:false,
		listeners:{
			select:{
				fn: function(comboBox, record, index){
					
			Ext.getCmp('licenseKeyGrid').store.reload({
						params: { companycode: comboBox.getValue() }
						});	
					}
				}
		},
		allowBlank:false,
		disabled: '{/literal}{$controldisabled}{literal}',
		{/literal}{if $company == ""}{literal}
			defvalue: 'GLOBAL',
		{/literal}{else}{literal}
			defvalue: '{/literal}{$company}{literal}',
		{/literal}{/if}{literal}
		options: {
			ref: '{/literal}{$ref}{literal}', 
			storeId: 'companyStore', 
			includeGlobal: '{/literal}{$includeglobal}{literal}', 
			includeShowAll: '0', 
			onchange: function(){var companyCode = companyCombo.getValue(); if (companyCode == 'GLOBAL') companyCode = '';}
		}
	});
	
	
	var deleteImg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png';
	var addimg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/add.png';

	{/literal}{if $pricingModel == 3}{literal}
		pricePanelWidth = 625;
    {/literal}{elseif $pricingModel == 5}{literal}
		pricePanelWidth = 625;
	{/literal}{else}{literal}
	 	pricePanelWidth = 835;
	{/literal}{/if}{literal}

	var pricePanel = new Ext.taopix.ComponentPricePanel({
        id: 'price', 
        name:'price', 
        height: 200, 
        post: true,
        width: pricePanelWidth,
        pricingModel: '{/literal}{$pricingModel}{literal}',
        pricingDecimalPlaces: '{/literal}{$decimalplaces}{literal}',
        addPic: addimg,
        delPic: deleteImg,
        data: '{/literal}{$price}{literal}',   
        {/literal}{if $pricingModel == 3}{literal}
		columnWidth: [112,112,112,112,112,112], 
        fieldWidth: [92,92,92,92,92,92],
        {/literal}{elseif $pricingModel == 5}{literal}
		columnWidth: [80,80,80,80,80,80,80,80], 
        fieldWidth: [60,60,60,60,60,60,60,60],
        {/literal}{elseif $pricingModel == 7}{literal}
        columnWidth: [107,107,107,107,107,107,107,107,107],
        fieldWidth: [88,88,88,88,88,88,88,88,88],
        {/literal}{else}{literal}
	 	columnWidth: [83,83,83,83,83,83,83,83,83], 
        fieldWidth: [63,63,63,63,63,63,63,63,63],
        {/literal}{/if}{literal}
		errorTitle: "{/literal}{#str_TitleError#}{literal}",
        errorMessage1: "{/literal}{#str_ErrorRangeStartError1#}{literal}",
    	errorMessage2: "{/literal}{#str_ErrorRangeStartError2#}{literal}",
    	errorMessage3: "{/literal}{#str_ErrorRangeEndError#}{literal}",
    	errorMessage4: "{/literal}{#str_ErrorComponentPriceError#}{literal}",    	
    	errorMessage5: "{/literal}{#str_ErrorProductPriceError#}{literal}",
    	errorMessage6: "{/literal}{#str_ErrorEnterValidPricing#}{literal}",
    	errorMessage7: "{/literal}{#str_ErrorRangeEndLimitError#}{literal}",    	
		errorMessage6Title: "{/literal}{#str_ErrorTitleInvalidPricing#}{literal}"     	    	
        
    });
    
    var taxCodeStore =  new Ext.data.Store({
	id: 'taxcodestore',
	proxy: new Ext.data.HttpProxy({
		url:'index.php?fsaction=AjaxAPI.callback&ref='+self.sessionRef+'&cmd=GETTAXCODELIST'
	}),
	reader: new Ext.data.ArrayReader({
		idIndex: 0
	},
	Ext.data.Record.create([
		{name: 'id', mapping: 0},
		{name: 'code', mapping: 1},
		{name: 'name', mapping: 2}
	])
	)
	});
	
	var taxCodeCombo =  new Ext.form.ComboBox({
		id: 'taxcode',
		name: 'taxcode',
		width:300,
		fieldLabel: gLabelTaxRate,
		mode: 'local',
		editable: false,
		forceSelection: true,
		store: taxCodeStore,
		selectOnFocus: true,
		triggerAction: 'all',
		valueField: 'code',
		displayField: 'name',
		useID: true,
		allowBlank: false,
		post: true
	});
	
	var priceListDialogFormPanelObj = new Ext.FormPanel({
		id: 'pricelistaddform',
        labelAlign: 'left',
        labelWidth:140,
        autoHeight: true,
        frame:true,
        layout:'form',
        cls: 'left-right-buttons',
        bodyStyle:'padding-left:5px;',
        items: [
            {/literal}{if $optionms}{literal} 
            	{/literal}{if !$companyLogin}{literal}
            		companyCombo,
            	{/literal}{/if}{literal}
            {/literal}{/if}{literal}
            { xtype: 'textfield', 
              id: 'componentpricelistcode', 
              name: 'componentpricelistcode', 
              allowBlank: false,
              maxLength: 50,
			  width:300,
			  {/literal}{if $ID > 0}{literal}
                  value: '{/literal}{$code}{literal}', 
                  style: {textTransform: "uppercase", background: "#dee9f6"},
                  readOnly: true,
              {/literal}{else}{literal}    
				style: {textTransform: "uppercase"},
		      {/literal}{/if}{literal}
              fieldLabel: "{/literal}{#str_LabelCode#}{literal}", 
              listeners:{
  				blur:{
  					fn: forceAlphaNumeric
  				}
  			},
              post: true
            },
            { 
            	 xtype: 'textfield', 
            	 id: 'name', 
            	 name: 'name',
            	 {/literal}{if $ID > 0}{literal}
            	 	value: '{/literal}{$name}{literal}',
                 {/literal}{/if}{literal}    
            	 fieldLabel: "{/literal}{#str_LabelName#}{literal}",
            	 allowBlank: false,
            	 maxLength: 50,
            	 post: true,
            	 width:300
            },
            taxCodeCombo,
            {/literal}{if $pricingModel == 7 || $pricingModel == 8}{literal} 
            { 
				xtype: 'checkbox', 
				id: 'fixedquantityrange',
				name: 'fixedquantityrange',
				boxLabel: "{/literal}{#str_LabelFixedQuantityRanges#}{literal}",
				{/literal}{if $quantityIsDropDown == 1}{literal}
					checked: true
				{/literal}{else}{literal}
					checked: false
				{/literal}{/if}{literal}
			},
			{/literal}{/if}{literal}
            pricePanel
        ]
    });
	
	var gridObj = Ext.getCmp('maingrid');
	var selRecords = gridObj.selModel.getSelections();
	var category = selRecords[0].data.code;
	
	{/literal}{if $pricingModel == 3}{literal}
		windowWidth = 660;
    {/literal}{elseif $pricingModel == 5}{literal}
		windowWidth = 660;
	{/literal}{else}{literal}
	 	windowWidth = 870;
	{/literal}{/if}{literal}
	
	var gPriceListAddDialogObj = new Ext.Window({
		id: 'pricelistadddialog',
		title: "{/literal}{#str_LabelAddPriceLists#}{literal}",
	  	closable:false,
		plain:true,
		modal:true,
		autoHeight:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		height: 'auto',
		width: windowWidth,
		items: priceListDialogFormPanelObj,
		listeners: {
			'close': {   
				fn: function(){
			pricelistEditWindowExists = false;
				}
			}
		},
		cls: 'left-right-buttons',
		buttons: 
		[
			{
				xtype: 'checkbox',
				id: 'ispricelistactive',
				name: 'ispricelistactive',
				boxLabel: "{/literal}{#str_LabelActive#}{literal}",
				post: true,
				cls: 'x-btn-left', 
				ctCls: 'width_100',
				{/literal}{if $isActive == 1}{literal}
					checked: true
				{/literal}{else}{literal}
					checked: false
				{/literal}{/if}{literal}
			},
			{
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(){ Ext.getCmp('pricelistadddialog').close(); },
				cls: 'x-btn-right'
			},
			{
				id: 'priceListAddEditButton',
				cls: 'x-btn-right',
				{/literal}{if $ID == 0}{literal}
					handler: priceListAddSaveHandler,
					text: "{/literal}{#str_ButtonAdd#}{literal}"
				{/literal}{else}{literal}
					handler: priceListEditSaveHandler,
					text: "{/literal}{#str_ButtonUpdate#}{literal}"
				{/literal}{/if}{literal}
			}
		]
	});
	
	Ext.getCmp('taxcode').store.on({
            'load': function() { 
                Ext.getCmp('taxcode').setValue('{/literal}{$taxcode}{literal}');
            } 
        });
	
	taxCodeStore.load();
	
	gPriceListAddDialogObj.show();	
}
{/literal}