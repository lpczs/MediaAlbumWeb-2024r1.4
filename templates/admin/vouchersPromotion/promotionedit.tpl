{literal}
function initialize(pParams)
{	
	var gDateFormat = "{/literal}{$dateformat}{literal}";
	var assignedToProd = {/literal}{$vouchersAssignedToProd}{literal};
	var companyGlobalValue = '';
	var gEarliestDate = "{/literal}{$earliestdate}{literal}";
	var gLatestDate = "{/literal}{$latestdate}{literal}";
	var gPromotionID = {/literal}{$promotionid}{literal};
	
	function onCompanyChange()
	{ 
		if (assignedToProd*1 > 0)
   	 	{
   	 		Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorVouchersAssignedToProductionSite#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
	   		Ext.getCmp('companylist').setValue(companyGlobalValue);
    	}
	};


	function editSaveHandler()
	{
		if(Ext.getCmp('mainform').getForm().isValid()) 
		{
			var parameter = [];
	   	 	parameter['startdateformat'] = formatPHPDate(Ext.getCmp('startdate').getRawValue(), gDateFormat, "yyyy-MM-dd HH:mm") + ":00";
	   		parameter['enddateformat'] = formatPHPDate(Ext.getCmp('enddate').getRawValue(), gDateFormat, "yyyy-MM-dd HH:mm") + ":00";
		
			parameter['isactive'] = (Ext.getCmp('isactivePromo').checked) ? '1' : '0';
		
			var fp = Ext.getCmp('mainform'), form = fp.getForm();
		
			if (gPromotionID > 0)
	  	  	{
	       	 	parameter['id'] = gPromotionID;
				Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=AdminVouchersPromotion.edit', "{/literal}{#str_MessageSaving#}{literal}", onCallbackPromo); 
	    	}
	    	else
	   	 	{
	        	Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=AdminVouchersPromotion.add', "{/literal}{#str_MessageSaving#}{literal}", onCallbackPromo); 
	    	}
		}
		else
		{
			return false;
		}
	};


	function dateValidation(elementId){
		var start = Ext.getCmp('startdate'); 
    	var end = Ext.getCmp('enddate');     

		if (elementId == 'startdate')
		{
			if (! parsePHPDate(start.getRawValue(), gDateFormat))
	   	 	{
				return("{/literal}{#str_ErrorInvalidStartDate#}{literal}");
	    	}
	    
	    	if (comparePHPDates(start.getRawValue(), gEarliestDate, gDateFormat) != 1)
	    	{
				return("{/literal}{#str_ErrorInvalidStartDate#}{literal}");
			}
	    
	    	if (comparePHPDates(gLatestDate, start.getRawValue(), gDateFormat) != 1)
	    	{
				return("{/literal}{#str_ErrorInvalidStartDate#}{literal}");
	    	}
    	}
    
    	if (elementId == 'enddate')
		{
			if (! parsePHPDate(end.getRawValue(), gDateFormat))
	    	{
				return("{/literal}{#str_ErrorInvalidEndDate#}{literal}");
	    	}
	    
	   	 	if (comparePHPDates(end.getRawValue(), start.getRawValue(), gDateFormat) != 1)
	    	{
				return("{/literal}{#str_ErrorInvalidEndDate#}{literal}");
	    	}
	    
	   	 	if (comparePHPDates(end.getRawValue(), gEarliestDate, gDateFormat) != 1)
	    	{
				return("{/literal}{#str_ErrorInvalidEndDate#}{literal}");
	    	}
	    
	    	if (comparePHPDates(gLatestDate, end.getRawValue(), gDateFormat) != 1)
	    	{
				return("{/literal}{#str_ErrorInvalidEndDate#}{literal}");
	    	}
		}
   
   	 	start.clearInvalid(); 
   	 	end.clearInvalid(); 
    	return true;
	};
	
	
	var topPanel = new Ext.Panel({ 
		id: 'topPanel', 
		layout: 'form',
		style:'background: #c9d8ed; padding: 3px 0; border:1px solid #8ca9cf', 
		plain:true, 
		bodyBorder: false, 
		border: false, 
		defaults: {xtype: 'textfield', labelWidth: 120, width: 300},  
		labelWidth: 125,
		bodyStyle:'padding:5px 5px 0; border-top: 0px',
		items: [
			{ 
				id: 'code', 
				name: 'code',
				fieldLabel: "{/literal}{#str_LabelCode#}{literal}", 
				validateOnBlur: true, 
				post: true, 
				allowBlank: false, 
				maskRe: /^\w+$/,
				maxLength: 50, 
				{/literal}{if $promotionid==0}{literal}
					readOnly: false,
					style: {textTransform: "uppercase"}
				{/literal}{else}{literal}
					value: "{/literal}{$code}{literal}",
					readOnly: true,
					style: 'background:#c9d8ed; textTransform: uppercase'
				{/literal}{/if}{literal} 
			}
		]
	});	
	
	var mainPanel = new Ext.Panel({ 
		id: 'mainPanel', 
		layout: 'form',
		plain:true, 
		bodyBorder: false, 
		border: false, 
		defaults: {xtype: 'textfield', labelWidth: 120, width: 300},  
		labelWidth: 125,
		bodyStyle:'padding:7px 5px 0; ',
		items: [
			{ 
				id: 'name', 
				name: 'name',
				fieldLabel: "{/literal}{#str_LabelName#}{literal}", 
				validateOnBlur: true, 
				post: true, 
				allowBlank: false, 
				maxLength: 50
				{/literal}{if $promotionid != 0}{literal}
					,value: "{/literal}{$name}{literal}"
				{/literal}{/if}{literal} 
			},
			
			{/literal}{if $optionMS == 1 && $userType == 0 }{literal}
			new Ext.taopix.CompanyCombo({ validationEvent:false, editable: false, forceSelection: true, allowBlank: false, fieldLabel: "{/literal}{#str_LabelCompanyName#}{literal}" , 
				hideLabel: false, defvalue: {/literal}{if $companyCodeSelected == ''} 'GLOBAL' {else} "{$companyCodeSelected}" {/if}{literal},
				id: 'companylist', name: 'companylist', emptyText:'', options:{ ref: sessionIdPromo, includeGlobal: '1', includeShowAll:'0', onchange: onCompanyChange },
				listeners: {
					'beforeselect': function(combo, record, index)
					{ 
						companyGlobalValue = combo.getValue();
					}
				}
			}),
			{/literal}{/if}{literal} 
			
			new Ext.form.DateField({ fieldLabel: "{/literal}{#str_LabelStartDate#}{literal}", name: 'startdate', id: 'startdate', validator: function(v){ return dateValidation('startdate');  }, validateOnBlur:true, endDateField: 'enddate',   format: gDateFormat, value: "{/literal}{$startdate}{literal}" }), 
			new Ext.form.DateField({ fieldLabel: "{/literal}{#str_LabelEndDate#}{literal}",   name: 'enddate',   id: 'enddate', validator: function(v){ return dateValidation('enddate');  }, validateOnBlur:true, startDateField: 'startdate', format: gDateFormat, value: "{/literal}{$enddate}{literal}" })
		]
	});	

	var dialogFormPanelObj = new Ext.taopix.FormPanel({
		id: 'mainform',
		header: false,
		frame:true,
		width: 500,
		layout: 'form',
		defaultType: 'textfield',
		autoHeight: true,
		items: [ topPanel, mainPanel ],
		baseParams:	{ ref: '{/literal}{$ref}{literal}' }
	});

	var gDialogObjPromo = new Ext.Window({
		id: 'dialogPromo',
		closable:false,
		plain:true,
		title: "{/literal}{$title}{literal}",
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		width: 490,
		items: dialogFormPanelObj,
		listeners: {
			'close': {   
				fn: function(){
		voucherPromotionEditWindowExists = false;
				}
			}
		},
		cls: 'left-right-buttons',
		buttons: 
		[
			new Ext.form.Checkbox({
				id: 'isactivePromo',
				name: 'isactivePromo',
				boxLabel: "{/literal}{#str_LabelActive#}{literal}",
				post: true,
				cls: 'x-btn-left', 
				ctCls: 'width_100'
			}),
			{	
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(btn, ev){ gDialogObjPromo.close(); },
				cls: 'x-btn-right'
			},
			{
				text: "{/literal}{$actionbutton}{literal}",
				id: 'updateButtonPromo',
				handler: editSaveHandler,
				cls: 'x-btn-right'
			}
		]
	});
	
	gDialogObjPromo.show();	
	
	Ext.getCmp('isactivePromo').setValue("{/literal}{$activechecked}{literal}" == 'checked' ? true : false);  
	
	{/literal}{if $promotionid == 0}{literal}
	Ext.getCmp('mainform').getForm().clearInvalid();
	{/literal}{/if}{literal} 
}

{/literal}