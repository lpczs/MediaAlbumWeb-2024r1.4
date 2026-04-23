{literal}

function initialize(pParams)
{	
	var useDefaultIpAccessList = "{/literal}{$usedefaultipaccesslist}{literal}";
	var ipAccessList = "{/literal}{$ipaccesslist}{literal}";
	var defaultIpAccessList = "{/literal}{$defaultipaccesslist}{literal}";
	
	/* save functions */
	function editSaveHandler(btn, ev)
	{
		var selectID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
		var submitURL = 'index.php?fsaction=AdminSitesCompanies.edit&ref={/literal}{$ref}{literal}&id=' + selectID;
		var fp = Ext.getCmp('companyForm'), form = fp.getForm();
		var taxAddress = (Ext.getCmp('taxAddressBill').checked) ? '0' : '1';
		var parameter = Ext.getCmp('addressForm').getAddressValues();
		parameter['taxaddress'] = taxAddress;
		parameter['useDefaultIpAddressList'] = (Ext.getCmp('useDefaultIpAddressList').checked) ? '1' : '0';
	
		Ext.taopix.formPanelPost(fp, form, parameter, submitURL, "{/literal}{#str_MessageSaving#}{literal}", saveCallback);
	}

	function saveCallback(pUpdated, pActionForm, pActionData)
	{	
		if (pUpdated)
		{
			var gridObj = gMainWindowObj.findById('maingrid');
			var dataStore = gridObj.store;	
			gridObj.store.reload();
			gDialogObj.close();
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

	function forceAlphaNumeric()
	{
		var code = Ext.getCmp('code').getValue();
   		code = code.toUpperCase();
    	code = code.replace(/[^A-Z_0-9\-]+/g, "");
       	Ext.getCmp('code').setValue(code);
	}
	
	function useDafaultAccessIpList()
    {
    	var ipAccessListObj = Ext.getCmp('ipaccesslist');
		if (Ext.getCmp('useDefaultIpAddressList').checked)
		{
			ipAccessListObj.setValue(defaultIpAccessList);
			ipAccessListObj.disable();
		}
		else
		{
			ipAccessListObj.setValue(ipAccessList);
			ipAccessList = '';
			ipAccessListObj.enable();
		}
    }
    
	var dialogFormPanelObj = new Ext.taopix.FormPanel({
		id: 'companyForm',
        labelAlign: 'left',
        labelWidth:100,
        height:354,
		width: 645,
        frame:true,
		layout: 'form',
		defaultType: 'textfield',
        cls: 'left-right-buttons',
        bodyStyle:' border-bottom: 1px solid #96bde7;',
        items: [
			{ 
			xtype: 'panel', 
			id: 'topPanel', 
			layout: 'column',
			style:'background: #c9d8ed; padding: 3px 0; border:1px solid #8ca9cf', 
			columns: 2, 
			plain:true, 
			bodyBorder: false, 
			border: false, 
			defaults: {labelWidth: 60},  
			bodyStyle:'padding:5px 5px 0; border-top: 0px',
			items: [
				new Ext.Container({ 
					layout: 'form', 
					defaults: {xtype: 'textfield', width: 185}, 
					width:255,
					items:[ 
						{	
							xtype: 'textfield', 
							id: 'code', 
							name: 'code', 
							allowBlank: false,
							readOnly: true,
							style: 'background:#c9d8ed; textTransform: uppercase',
							value: "{/literal}{$companycode}{literal}", 
							fieldLabel: "{/literal}{#str_LabelCode#}{literal}", 
							listeners:{	blur:{ fn: forceAlphaNumeric } },
				    		validateOnBlur: true,
				    		post: true
			   			}
					]
				}),
				new Ext.Container({ 
					layout: 'form', 
					defaults: {xtype: 'textfield', width: 230}, 
					style:'padding-left:25px', 
					width:340,
					items: [
						{	
							xtype: 'textfield', 
				   			id: 'name', 
				   			name: 'name', 
				   			allowBlank: false,
				   			value: '{/literal}{$companyname}{literal}', 
				   			fieldLabel: "{/literal}{#str_LabelName#}{literal}", 
							validateOnBlur: true,
				   			post: true
						}
					]
				})
			]},	
	
			{ 
				xtype: 'tabpanel',
				id: 'maintabpanel',
				deferredRender: false,
				layoutOnTabChange: true,
				activeTab: 0,
				autoWidth: true,
				height: 285,
				shadow: true,
				plain:true,
				bodyBorder: false,
				border: false,
				style:'margin-top:6px; ',
				bodyStyle:'border-right: 1px solid #96bde7; border-left: 1px solid #96bde7; ',
				defaults:{frame: false, autoScroll: true, hideMode:'offsets', layout: 'form', labelWidth: 150, bodyStyle:'padding:10px; border-top: 0px; background-color: #eaf0f8;'},
				items: [
					{ title: "{/literal}{#str_LabelContactInformation#}{literal}",
						defaults:{xtype: 'textfield', width: 350},
						items: [
							{ xtype: 'panel', layout: 'form', autoWidth: true, defaults:{xtype: 'textfield', width: 350}, style:'margin-top:5px',
								items: [
									{ xtype: 'textfield', 
				   						id: 'contactFirstName', 
				   						name: 'companyContactFirstName', 
				   						value: "{/literal}{$contactfirstname}{literal}", 
				   						fieldLabel: "{/literal}{#str_LabelFirstName#}{literal}", 
				   						post: true
									},
									{ xtype: 'textfield', 
				   						id: 'contactLastName', 
				   						name: 'companyContactLastName', 
				   						value: '{/literal}{$contactlastname}{literal}', 
				   						fieldLabel: "{/literal}{#str_LabelLastName#}{literal}", 
				   						post: true
									}	
								]
							},
							{ xtype: 'panel', layout: 'form', autoWidth: true, style: 'margin-bottom:17px', defaults:{xtype: 'textfield', width: 350},
								items: [
									{ xtype: 'textfield', 
				   						id: 'emailAddress', 
				   						name: 'emailAddress', 
				   						value: '{/literal}{$emailaddress}{literal}', 
				   						fieldLabel: "{/literal}{#str_LabelEmailAddress#}{literal}", 
										validateOnBlur: true,
										vtype: 'email',
				   						post: true
									},
									{ xtype: 'textfield', 
				   						id: 'phoneNumber', 
				   						name: 'phoneNumber', 
				   						value: '{/literal}{$telephonenumber}{literal}', 
				   						fieldLabel: "{/literal}{#str_LabelTelephoneNumber#}{literal}",
										listeners: {'blur': {fn: function(obj){CJKHalfWidthFullWidthToASCII(obj.getEl().dom, false)}}},
				   						post: true
									}
								]
							},
							{ xtype: 'radiogroup', columns: 1,  fieldLabel: "{/literal}{#str_LabelTaxCalculationBy#}{literal}", autoWidth:true,  
				  				items: [
									{boxLabel: "{/literal}{#str_LabelBillingAddress#}{literal}", name: 'taxaddress', inputValue: 0, id: 'taxAddressBill', post: true,  checked: {/literal}{if $taxaddress==0}true{else}false{/if}{literal}},
									{boxLabel: "{/literal}{#str_LabelShippingAddress#}{literal}", name: 'taxaddress', inputValue: 1, id: 'taxAddressShip', post: true,  checked: {/literal}{if $taxaddress==1}true{else}false{/if}{literal}}
				  				]
							}
						]
					},					
					{ title: "{/literal}{#str_TitleSectionAddress#}{literal}",
						defaults:{xtype: 'textfield', width: 230}, hideMode:'offsets',
						listeners: { 'beforeshow': function(){ Ext.getCmp('maintabpanel').doLayout(); }},
						items: [
							new Ext.taopix.AddressPanel({ 
								id: 'addressForm',
								options: { 
									ref: {/literal}{$ref}{literal},
					    			excludeFields: 'firstname,lastname,company,regtaxnum,regtaxnumtype', 
					    			editMode: 0, 
					    			strict: 1,
					    			fieldWidth: 230
		        				},
								data: {
									{/literal}
					    			countryCode:"{$companycountrycode}", 
					        		address1:"{$companyaddress1}", 
					        		address2:"{$companyaddress2}", 
					        		address3:"{$companyaddress3}", 
					        		address4:"{$companyaddress4}",                 
					        		add41:"{$companyadd41}",                 
					        		add42:"{$companyadd42}",                 
					        		add43:"{$companyadd43}",                 
					        		city:"{$companycity}", 
					    			countyName: "{$companycounty}", 
					    			countyCode: "{$companyregioncode}", 
					    			stateName: "{$companystate}", 
					    			stateCode: "{$companyregioncode}", 
						    		postCode: "{$companypostcode}"
									{literal}
								}
							})
						]
					},
					
					{ title: "{/literal}{#str_TitleAccessRestrictions#}{literal}",
						defaults:{xtype: 'textarea', width: 2300, labelWidth: 132}, hideMode:'offsets',
						listeners: { 'beforeshow': function(){ Ext.getCmp('maintabpanel').doLayout(); }},
						items: [
							{xtype:'textarea', labelWidth: 110, id: 'defultipaccesslist', name: 'defultipaccesslist', fieldLabel: '{/literal}{#str_LabelDefaultIPAccessList#}{literal}', width: 435, height: 60, post: true, disabled: true, value: defaultIpAccessList }, 
        
							
							{ xtype: 'checkbox', id: 'useDefaultIpAddressList', name: 'useDefaultIpAddressList', hideLabel: true, boxLabel: "{/literal}{#str_LabelUseDefaultAccessIPList#}{literal}",
								checked: (useDefaultIpAccessList == 1) ? true : false,
								listeners: {'check': useDafaultAccessIpList	}	
							},
							
							{ xtype: 'container', style: { padding: '0 0 0 20px' }, layout: 'form', autoWidth: true, 
								items:[
									{xtype:'textarea', id: 'ipaccesslist', name: 'ipaccesslist', fieldLabel: '{/literal}{#str_LabelIPAccessList#}{literal}', width:435, height: 60, 
										post: true,	maskRe: /^(\d{1,3})|(\.)|(,)|(\s)|(\*)$/, value: "{/literal}{$defaultipaccesslist}{literal}", enableKeyEvents: true,
										post: true
									}
								]
							}
						]
					}
				]
			}
        ]
    });
    
    /* create modal window for add and edit */
	gDialogObj = new Ext.Window({
		id: 'dialog',
	  	closable:false,
	  	plain:true,
	  	title: "{/literal}{#str_TitleEditCompany#}{literal}",
	  	modal:true,
	  	draggable:true,
	  	resizable:false,
	 	layout: 'fit',
	  	height: 'auto',
	  	width: 645,
	  	height:410,
	  	items: dialogFormPanelObj,
	  	listeners: {
			'close': {   
				fn: function(){
		companyEditWindowExists = false;
				}
			}
		},
		buttons: 
		[
			{
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(){ gDialogObj.close(); }
			},
			{
				text: "{/literal}{#str_ButtonUpdate#}{literal}",
				handler: editSaveHandler
			}
		]
	});
    
    useDafaultAccessIpList(); 
	gDialogObj.show();	  


}
{/literal}