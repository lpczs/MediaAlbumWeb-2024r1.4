{literal}

function initializePriceListEdit(pParams)
{		
	var companyCombo = new Ext.taopix.CompanyCombo({
		id: 'pricelistcompany',
		name: 'pricelistcompany',
		width:300,
		fieldLabel: "{/literal}{#str_LabelCompany#}{literal}",
		hideLabel:false,
		allowBlank:false,
		{/literal}{if $ID >= 1 || $companyLogin}{literal}
			{/literal}{if $company == ""}{literal}
				defvalue: 'GLOBAL',
			{/literal}{else}{literal}
				defvalue: '{/literal}{$company}{literal}',
				disabled: true,
			{/literal}{/if}{literal}
		{/literal}{/if}{literal}
		options: {
			ref: '{/literal}{$ref}{literal}', 
			storeId: 'companyStore', 
			{/literal}{if $companyLogin}{literal}
			includeGlobal: '0', 
			{/literal}{else}{literal}
			includeGlobal: '1', 
			{/literal}{/if}{literal}
			includeShowAll: '0', 
			onchange: function(){var companyCode = companyCombo.getValue(); if (companyCode == 'GLOBAL') companyCode = '';}
		}
	});
	
	var deleteImg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png';
	var addimg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/add.png';
	
	{/literal}{if ($pricingModel == 7) || ($pricingModel == 8)}{literal} 
		pricePanelWidth = 800;
	{/literal}{elseif $pricingModel == 3}{literal}
		pricePanelWidth = 465;
	{/literal}{else}{literal}
		pricePanelWidth = 630;
	{/literal}{/if}{literal}
	
	var pricePanel = new Ext.taopix.ComponentPricePanel({
        id: 'pricelisteditprice', 
        name:'pricelisteditprice', 
        height: 200, 
        post: true, 
        width: pricePanelWidth, 
        pricingModel: '{/literal}{$pricingModel}{literal}',
        pricingDecimalPlaces: '{/literal}{$decimalplaces}{literal}',
        addPic: addimg,
        delPic: deleteImg,
        data: '{/literal}{$price}{literal}', 
        columnWidth: [80, 80, 80,80,80,80,80,80,80], 
        fieldWidth: [60, 60, 60,60,60,60,60,60,60],
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
    
    var taxCodeSaveAsPriceListStore =  new Ext.data.Store({
	id: 'taxcodestorepricelist',
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
	
	var taxCodeComboSaveAsPriceList =  new Ext.form.ComboBox({
		id: 'taxcodepricelist',
		name: 'taxcodepricelist',
		width:300,
		fieldLabel: gLabelTaxRate,
		mode: 'local',
		editable: false,
		forceSelection: true,
		store: taxCodeSaveAsPriceListStore,
		selectOnFocus: true,
		triggerAction: 'all',
		valueField: 'code',
		displayField: 'name',
		useID: true,
		allowBlank: false,
		post: true
	});

	var priceListEditDialogFormPanelObj = new Ext.FormPanel({
		id: 'adminpricelistaddform',
        labelAlign: 'left',
        labelWidth:60,
        autoHeight: true,
        frame:true,
        layout:'form',
        cls: 'left-right-buttons',
        bodyStyle:'padding-left:5px;',
        items: [
        	{/literal}{if $optionms}{literal}
            companyCombo,
            {/literal}{/if}{literal}
            { xtype: 'textfield', 
              id: 'adminpricelistcode', 
              name: 'adminpricelistcode', 
              allowBlank: false,
              maxLength: 50,
			  width:300,
              value: '{/literal}{$code}{literal}', 
              style: {textTransform: "uppercase", background: "#dee9f6"},
              readOnly: true,
              fieldLabel: "{/literal}{#str_LabelCode#}{literal}", 
              post: true
            },
            { 
            	 xtype: 'textfield', 
            	 id: 'adminpricelistname', 
            	 name: 'adminpricelistname', 
            	 value: '{/literal}{$name}{literal}',
            	 fieldLabel: "{/literal}{#str_LabelName#}{literal}",
            	 allowBlank: false,
            	 maxLength: 50,
            	 post: true,
            	 width:300
            },
            taxCodeComboSaveAsPriceList,
            {/literal}{if $pricingModel == 7 || $pricingModel == 8 || $categorycodelist == 'PRODUCT'}{literal} 
            { 
				xtype: 'checkbox', 
				id: 'fixedquantityranges',
				name: 'fixedquantityranges',
				boxLabel: "{/literal}{#str_LabelFixedQuantityRanges#}{literal}",
				{/literal}{if $quantityisdropdown == 1}{literal}
					checked: true
				{/literal}{else}{literal}
					checked: false
				{/literal}{/if}{literal}
			},
			{/literal}{/if}{literal}
            pricePanel,
            { xtype: 'hidden', id: 'categorycodelist', name: 'categorycodelist', value: '{/literal}{$categorycodelist}{literal}',  post: true}
        ]
    });
	
	{/literal}{if $ID == 0 && ! $companyLogin}{literal}
		Ext.getCmp('company').store.on({'load': function(){
			Ext.getCmp('company').setValue('GLOBAL');
			} 
		});	
	{/literal}{/if}{literal}
	
	var isactiveObj = Ext.getCmp('isadminpricelistactive');
	if (isactiveObj)
	{
		{/literal}{if $isActive == 1}{literal}
			isactiveObj.setValue(true);
		{/literal}{else}{literal}
			isactiveObj.setValue(false);
		{/literal}{/if}{literal}
	}
	
	if (!Ext.getCmp('componentpricinggrid'))
	{
		var gridObj = gMainWindowObj.findById('productgrid');
		var pricingModel = 3;
		category = 'PRODUCT';
	}
	else
	{
		var gridObj = Ext.getCmp('maingrid');
		var selRecords = gridObj.selModel.getSelections();
		var pricingModel = selRecords[0].data.pricingmodel;
		var category = selRecords[0].data.code;
	}	
	
	Ext.getCmp('taxcodepricelist').store.on({
            'load': function() { 
                Ext.getCmp('taxcodepricelist').setValue('{/literal}{$taxcode}{literal}');
            } 
        });
	
	taxCodeSaveAsPriceListStore.load();
	
	var windowTitle = "{/literal}{#str_LabelEditPriceLists#}{literal}";
	
	var editPriceListPanel = Ext.getCmp('adminpricelistadddialog');
	editPriceListPanel.setTitle(windowTitle);
	editPriceListPanel.add(priceListEditDialogFormPanelObj);
	editPriceListPanel.show();	  
	editPriceListPanel.center();
	editPriceListPanel.doLayout();
		
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
}
{/literal}