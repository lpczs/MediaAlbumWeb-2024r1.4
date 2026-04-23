{literal}
function initialize(pParams)
{
	Ext.layout.FormLayout.prototype.trackLabels = true;

	var TPX_LOGIN_SYSTEM_ADMIN = {/literal}{$TPX_LOGIN_SYSTEM_ADMIN}{literal};
	var TPX_LOGIN_COMPANY_ADMIN = {/literal}{$TPX_LOGIN_COMPANY_ADMIN}{literal};
	var TPX_LOGIN_SITE_ADMIN = {/literal}{$TPX_LOGIN_SITE_ADMIN}{literal};
	var TPX_LOGIN_CREATOR_ADMIN = {/literal}{$TPX_LOGIN_CREATOR_ADMIN}{literal};
	var TPX_LOGIN_PRODUCTION_USER = {/literal}{$TPX_LOGIN_PRODUCTION_USER}{literal};
	var TPX_LOGIN_DISTRIBUTION_CENTRE_USER = {/literal}{$TPX_LOGIN_DISTRIBUTION_CENTRE_USER}{literal};
	var TPX_LOGIN_STORE_USER = {/literal}{$TPX_LOGIN_STORE_USER}{literal};
	var TPX_LOGIN_BRAND_OWNER = {/literal}{$TPX_LOGIN_BRAND_OWNER}{literal};
	var TPX_LOGIN_API = {/literal}{$TPX_LOGIN_API}{literal};
	var TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER = {/literal}{$TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER}{literal};
	var storeDistOwner = "{/literal}{$owner}{literal}";

	var ipAccessType = "{/literal}{$ipaccesstype}{literal}";
	var ipAccessList = "{/literal}{$ipaccesslist}{literal}";
	var defaultIpAccessList = "{/literal}{$defaultipaccesslist}{literal}";

	function setLoginForm(comboBox, record, index)
	{
		loginTypeId = comboBox.getValue();

		switch(parseInt(loginTypeId)){
			case TPX_LOGIN_SYSTEM_ADMIN:
			case TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER:
				{/literal}{if $optionms}{literal}
					Ext.getCmp('productionsite').hide();
					Ext.getCmp('productionsite').disable();
					Ext.getCmp('company').hide();
					Ext.getCmp('company').disable();
				{/literal}{/if}{literal}
				{/literal}{if $optioncfs}{literal}
					Ext.getCmp('store').hide();
					Ext.getCmp('store').disable();
				{/literal}{/if}{literal}
				Ext.getCmp('brand').hide();
				Ext.getCmp('brand').disable();
				break;
			case TPX_LOGIN_COMPANY_ADMIN:
				Ext.getCmp('company').store.reload();
				Ext.getCmp('company').show();
				Ext.getCmp('company').enable();
				{/literal}{if $optioncfs}{literal}
					Ext.getCmp('store').hide();
					Ext.getCmp('store').disable();
				{/literal}{/if}{literal}
				Ext.getCmp('brand').hide();
				Ext.getCmp('brand').disable();
				Ext.getCmp('productionsite').hide();
				Ext.getCmp('productionsite').disable();
				break;
			case TPX_LOGIN_SITE_ADMIN:
				{/literal}{if $optionms}{literal}
					{/literal}{if $loggedInAs == $TPX_LOGIN_COMPANY_ADMIN}{literal}
						Ext.getCmp('productionsite').show();
						Ext.getCmp('productionsite').enable();
					{/literal}{/if}{literal}
					Ext.getCmp('productionsite').store.reload({ params: { siteAdmin: 1}	});
					Ext.getCmp('productionsite').store.on({
						'load': function() {
							if (Ext.getCmp('productionsite').store.findExact('id', "{/literal}{$userprodsite}{literal}") > -1)
							{
								Ext.getCmp('productionsite').setValue("{/literal}{$userprodsite}{literal}");
							}
							else
							{
								Ext.getCmp('productionsite').setValue(Ext.getCmp('productionsite').store.getAt(0).data.id);
							}
						}
					});
					Ext.getCmp('productionsite').show();
					Ext.getCmp('productionsite').enable();
					Ext.getCmp('company').hide();
					Ext.getCmp('company').disable();
				{/literal}{/if}{literal}
				{/literal}{if $optioncfs}{literal}
					Ext.getCmp('store').hide();
					Ext.getCmp('store').disable();
				{/literal}{/if}{literal}
				Ext.getCmp('brand').hide();
				Ext.getCmp('brand').disable();
				break;
			case TPX_LOGIN_CREATOR_ADMIN:
				{/literal}{if $optionms}{literal}
					Ext.getCmp('productionsite').hide();
					Ext.getCmp('productionsite').disable();
					Ext.getCmp('company').hide();
					Ext.getCmp('company').disable();
				{/literal}{/if}{literal}
				{/literal}{if $optioncfs}{literal}
					Ext.getCmp('store').hide();
					Ext.getCmp('store').disable();
				{/literal}{/if}{literal}
				Ext.getCmp('brand').hide();
				Ext.getCmp('brand').disable();
				break;
			case TPX_LOGIN_PRODUCTION_USER:
				{/literal}{if $optionms}{literal}
					{/literal}{if $loggedInAs == TPX_LOGIN_SITE_ADMIN}{literal}
						Ext.getCmp('productionsite').store.reload({	params: { siteAdmin: 1}	});
						Ext.getCmp('productionsite').store.on({
							'load': function() {
								if (Ext.getCmp('productionsite').store.findExact('id', "{/literal}{$userprodsite}{literal}") > -1)
								{
									Ext.getCmp('productionsite').setValue("{/literal}{$userprodsite}{literal}");
								}
							}
						});
						Ext.getCmp('productionsite').show();
						Ext.getCmp('productionsite').enable();
					{/literal}{else}{literal}
						Ext.getCmp('productionsite').store.reload({	params: { siteAdmin: 2}	});
						Ext.getCmp('productionsite').store.on({
							'load': function() {
								if (Ext.getCmp('productionsite').store.findExact('id', "{/literal}{$userprodsite}{literal}") > -1)
								{
									Ext.getCmp('productionsite').setValue("{/literal}{$userprodsite}{literal}");
								}
								else
								{
									Ext.getCmp('productionsite').setValue(Ext.getCmp('productionsite').store.getAt(0).data.id);
								}
							}
						});
						Ext.getCmp('productionsite').show();
						Ext.getCmp('productionsite').enable();
					{/literal}{/if}{literal}
				{/literal}{/if}{literal}
				{/literal}{if $optionms}{literal}
					Ext.getCmp('company').hide();
					Ext.getCmp('company').disable();
				{/literal}{/if}{literal}
				{/literal}{if $optioncfs}{literal}
					Ext.getCmp('store').hide();
					Ext.getCmp('store').disable();
				{/literal}{/if}{literal}
				Ext.getCmp('brand').hide();
				Ext.getCmp('brand').disable();
				break;
			case TPX_LOGIN_DISTRIBUTION_CENTRE_USER:
				Ext.getCmp('store').store.reload({
					params: { distributionCentre: 1}
				});
				Ext.getCmp('store').store.on({
					'load': function(){
						if (Ext.getCmp('store').store.findExact('id', storeDistOwner) > -1)
						{
							Ext.getCmp('store').setValue(storeDistOwner);
						}
						else
						{
							Ext.getCmp('store').setValue(Ext.getCmp('store').store.getAt(0).data.id);
						}
						Ext.getCmp('store').label.update("{/literal}{#str_SiteTypeDistributionCentre#}{literal}");
					}
				});
				Ext.getCmp('store').show();
				Ext.getCmp('store').enable();

				{/literal}{if $optionms}{literal}
					Ext.getCmp('productionsite').hide();
					Ext.getCmp('productionsite').disable();
					Ext.getCmp('company').hide();
					Ext.getCmp('company').disable();
				{/literal}{/if}{literal}
				Ext.getCmp('brand').hide();
				Ext.getCmp('brand').disable();
				break;
			case TPX_LOGIN_STORE_USER:
				{/literal}{if $optioncfs}{literal}
					Ext.getCmp('store').store.reload({
						params: { distributionCentre: 0}
					});
					Ext.getCmp('store').store.on({'load': function(){
						if (Ext.getCmp('store').store.findExact('id', storeDistOwner) > -1)
						{
							Ext.getCmp('store').setValue(storeDistOwner);
						}
						else
						{
							Ext.getCmp('store').setValue(Ext.getCmp('store').store.getAt(0).data.id);
						}
						Ext.getCmp('store').label.update("{/literal}{#str_LabelStoreFieldLabel#}{literal}");
						}
					});
					Ext.getCmp('store').show();
					Ext.getCmp('store').enable();
				{/literal}{/if}{literal}
				{/literal}{if $optionms}{literal}
					Ext.getCmp('productionsite').hide();
					Ext.getCmp('productionsite').disable();
					Ext.getCmp('company').hide();
					Ext.getCmp('company').disable();
				{/literal}{/if}{literal}
				Ext.getCmp('brand').hide();
				Ext.getCmp('brand').disable();
				break;
			case TPX_LOGIN_BRAND_OWNER:
				Ext.getCmp('brand').store.reload();
				Ext.getCmp('brand').store.on({'load': function(){
					if (Ext.getCmp('brand').store.findExact('id', "{/literal}{$brandcode}{literal}") > -1)
					{
						Ext.getCmp('brand').setValue("{/literal}{$brandcode}{literal}");
					}
					else
					{
						Ext.getCmp('brand').setValue(Ext.getCmp('brand').store.getAt(0).data.id);
					}
				} });
				Ext.getCmp('brand').show();
				Ext.getCmp('brand').enable();
				{/literal}{if $optionms}{literal}
					Ext.getCmp('productionsite').hide();
					Ext.getCmp('productionsite').disable();
					Ext.getCmp('company').hide();
					Ext.getCmp('company').disable();
				{/literal}{/if}{literal}
				{/literal}{if $optioncfs}{literal}
					Ext.getCmp('store').hide();
					Ext.getCmp('store').disable();
				{/literal}{/if}{literal}
				break;
			case TPX_LOGIN_API:
				{/literal}{if $optionms}{literal}
					Ext.getCmp('productionsite').hide();
					Ext.getCmp('productionsite').disable();
					Ext.getCmp('company').hide();
					Ext.getCmp('company').disable();
				{/literal}{/if}{literal}
				{/literal}{if $optioncfs}{literal}
					Ext.getCmp('store').hide();
					Ext.getCmp('store').disable();
				{/literal}{/if}{literal}
				Ext.getCmp('brand').hide();
				Ext.getCmp('brand').disable();
			   break;
		}
	}

	function md5HashFunction(comboBox, record, index)
	{
		var password = Ext.getCmp('password_user').getValue();
		if (password != '')
		{
			if (password != "**UNCHANGED**")
	   	 	{
				var encPassword = hex_md5(password);
				Ext.getCmp('password_user').setValue(encPassword);
	   	 	}
	    	else
	    	{
	    		Ext.getCmp('password_user').setValue(password);
	    	}
		}
	}

	var loginTypeStore = new Ext.data.ArrayStore({
		id: 'logintypestore',
		fields: ['id', 'name'],
		data: [
			{/literal}
			{section name=index loop=$userlogintypes}
			{if $smarty.section.index.last}
					["{$userlogintypes[index].id}", "{$userlogintypes[index].name}"]
			{else}
					["{$userlogintypes[index].id}", "{$userlogintypes[index].name}"],
			{/if}
			{/section}
			{literal}
		]
	});

	{/literal}{if $optioncfs}{literal}
	var storeList = new Ext.data.Store({
		id: 'storeList',
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AjaxAPI.callback&ref={/literal}{$ref}{literal}&cmd=STORESCOMBO', method: 'GET'}),
		reader: new Ext.data.ArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
		    {name: 'id', mapping: 2},
			{name: 'name', mapping: 1}
			])
		)
	});
	{/literal}{/if}{literal}

	{/literal}{if $optionms}{literal}
	var productionSiteStore = new Ext.data.Store({
		id: 'productionSiteStore',
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AjaxAPI.callback&ref={/literal}{$ref}{literal}&cmd=PRODSITESCOMBO', method: 'GET'}),
		reader: new Ext.data.ArrayReader({
			idIndex: 0},
			Ext.data.Record.create([
		   		{name: 'id', mapping: 2},
				{name: 'name', mapping: 1}
			])
		)
	});
	{/literal}{/if}{literal}

	var brandStore = new Ext.data.Store({
		id: 'brandStore',
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AjaxAPI.callback&ref={/literal}{$ref}{literal}&cmd=BRANDCOMBO&userpage=1'}),
		reader: new Ext.data.ArrayReader({
			idIndex: 0},
			Ext.data.Record.create([
		    	{name: 'id', mapping: 0},
				{name: 'name', mapping: 1}
			])
		)
	});

	var format = ((document.location.protocol != 'https:') ? 1 : 0);

	var dialogFormPanelObj = new Ext.taopix.FormPanel({
		id: 'usersForm',
		header: false,
		frame:true,
		width: 550,
		labelWidth: 110,
		defaultType: 'textfield',
		autoHeight: true,
		items: [
			{
				xtype: 'tabpanel',
				id: 'maintabpanel',
				deferredRender: false,
				layoutOnTabChange: true,
				activeTab: 0,
				autoWidth: true,
				height: 280,
				shadow: true,
				plain:true,
				defaults:{frame: false, autoScroll: true, hideMode:'offsets', layout: 'form', labelWidth: 140, bodyStyle:'padding:10px; background-color: #eaf0f8;'},
				items: [
					{ title: "{/literal}{#str_LabelDetails#}{literal}",
						items:
						[
							// Prevent Safari 7.0xxx to populated the main password field
							{
								xtype: 'textfield',
								width: 350,
            					fieldLabel: "",
            					id:'fakepasswordforsafari',
            					name: 'fakepasswordforsafari',
            					inputType: 'password',
            					allowBlank: true,
            					post: false,
								style: 'position: absolute; top: -5000px;'
        					},
							// End -> Prevent Safari 7.0xxx to populated the main password field
							{
								xtype: 'textfield',
								width: 350,
            					fieldLabel: "{/literal}{#str_LabelFirstName#}{literal}",
            					id: 'contactfname',
            					name: 'contactfname',
            					allowBlank: false,
            					{/literal}{if $isEdit == 1}{literal}
              						value: "{/literal}{$contactfname}{literal}",
            					{/literal}{/if}{literal}
            					width: 275,
            					maxLength: 200,
            					post: true
        					},
        					{
								xtype: 'textfield',
								width: 350,
            					fieldLabel: "{/literal}{#str_LabelLastName#}{literal}",
            					id: 'contactlname',
           						name: 'contactlname',
            					value:"{/literal}{$contactlname}{literal}",
            					width: 275,
            					maxLength: 200,
            					post: true
        					},
        					{
								xtype: 'textfield',
								width: 350,
            					fieldLabel: "{/literal}{#str_LabelUserName#}{literal}",
            					id: 'login_user',
            					name: 'login_user',
            					allowBlank: false,
            					{/literal}{if $isEdit == 1}{literal}
              						value: "{/literal}{$login}{literal}",
            					{/literal}{/if}{literal}
            					width: 275,
            					maxLength: 50,
            					post: true
        					},
        					{
								xtype: 'textfield',
								width: 350,
            					fieldLabel: "{/literal}{#str_LabelPassword#}{literal}",
            					id:'password_user',
            					name: 'password_user',
            					inputType: 'password',
            					allowBlank:false,
            					{/literal}{if $isEdit == 1}{literal}
              						value: "{/literal}{$password}{literal}",
            					{/literal}{/if}{literal}
            					width: 275,
            					listeners:{
									blur:{
										fn: function()
										{
											if (format == 1)
											{
												md5HashFunction();
											}
										}
									}
								},
            					post: true
        					},
							{
            					xtype: 'checkbox',
            					id: 'canmodifypassword',
								name: 'canmodifypassword',
								boxLabel: "{/literal}{#str_LabelModifyPassword#}{literal}",
								{/literal}{if $canmodifypasswordchecked == 1}{literal}
									checked: true,
								{/literal}{else}{literal}
									checked: false,
								{/literal}{/if}{literal}
								post: true
        					},
							{
								xtype: 'textfield',
								width: 350,
            					fieldLabel: "{/literal}{#str_LabelEmailAddress#}{literal}",
            					id: 'email',
            					name: 'email',
            					vtype:'email',
            					value:"{/literal}{$email}{literal}",
            					validationDelay: 7000,
            					width: 275,
            					post: true
        					},
        					new Ext.form.ComboBox({
								id: 'logintype',
								name: 'logintype',
								width:275,
								mode: 'local',
								editable: false,
								hideLabel: false,
								forceSelection: true,
								selectOnFocus: true,
								triggerAction: 'all',
								fieldLabel: "{/literal}{#str_LabelTypeLogin#}{literal}",
								store: loginTypeStore,
								listeners:{
									select:{
										fn: function()
										{
											setLoginForm(this, '', '');
											storeDistOwner = '';
											Ext.getCmp('dialog').syncShadow();
										}
									}
								},
								valueField: 'id',
								displayField: 'name',
								useID: true,
								{/literal}{if $isEdit == 0}{literal}
        							value: "{/literal}{$defaultLoginTypeValue}{literal}",
								{/literal}{else}{literal}
									value: "{/literal}{$usertype}{literal}",
        						{/literal}{/if}{literal}
								allowBlank: false,
								post: true
							}),

							{/literal}{if $optionms}{literal}
			 				new Ext.taopix.CompanyCombo({
				 				id: 'company',
								name: 'company',
								width:275,
								fieldLabel: "{/literal}{#str_LabelCompany#}{literal}",
								hideLabel:false,
								allowBlank:false,
								displayField: 'name',
       							valueField: 'code',
								{/literal}{if $isEdit == 1}{literal}
									defvalue: "{/literal}{$companycode}{literal}",
			 					{/literal}{/if}{literal}
								options: {ref: "{/literal}{$ref}{literal}", storeId: 'companyStore', includeShowAll: '0', onchange: function(){}}
							}),
							{/literal}{/if}{literal}
							{/literal}{if $optioncfs}{literal}
							new Ext.form.ComboBox({
								id: 'store',
								name: 'store',
								width:275,
								mode: 'local',
								editable: false,
								hideLabel: false,
								forceSelection: true,
								allowBlank:false,
								selectOnFocus: true,
								triggerAction: 'all',
								{/literal}{if $optioncfs}{literal}
									{/literal}{if $isEdit == 1}{literal}
										{/literal}{if $usertype == $TPX_LOGIN_DISTRIBUTION_CENTRE_USER}{literal}
											fieldLabel: "{/literal}{#str_LabelDistributionCentre#}{literal}",
										{/literal}{else}{literal}
											fieldLabel: "{/literal}{#str_LabelStoreFieldLabel#}{literal}",
										{/literal}{/if}{literal}
									{/literal}{/if}{literal}
								{/literal}{/if}{literal}
								store: storeList,
								valueField: 'id',
								displayField: 'name',
								useID: true,
								post: true
							}),
							{/literal}{/if}{literal}
							new Ext.form.ComboBox({
								id: 'brand',
								name: 'brand',
								width:275,
								mode: 'local',
								editable: false,
								hideLabel: false,
								forceSelection: true,
								allowBlank:false,
								selectOnFocus: true,
								triggerAction: 'all',
								fieldLabel: "{/literal}{#str_LabelBrand#}{literal}",
								store: brandStore,
								valueField: 'id',
								displayField: 'name',
								useID: true,
								allowBlank: false,
								post: true
							})
							{/literal}{if $optionms}{literal}
        					,new Ext.form.ComboBox({
								id: 'productionsite',
								name: 'productionsite',
								width:275,
								mode: 'local',
								editable: false,
								hideLabel: false,
								allowBlank:false,
								forceSelection: true,
								selectOnFocus: true,
								triggerAction: 'all',
								fieldLabel: "{/literal}{#str_LabelProductionSite#}{literal}",
								store: productionSiteStore,
								valueField: 'id',
								displayField: 'name',
								useID: true,
								post: true
							})
       						{/literal}{/if}{literal}
        				]
					},
					{ title: "{/literal}{#str_TitleAccessRestrictions#}{literal}",
						items:
						[
							{
								xtype:'textarea',
								id: 'defultipaccesslist',
								name: 'defultipaccesslist',
								fieldLabel: "{/literal}{#str_LabelDefaultIPAccessList#}{literal}",
								width: 360,
								height: 55,
								post: true,
								disabled: true,
								value: defaultIpAccessList
							},
							{
								xtype: 'combo',
								id: 'ipAccessType',
								name: 'ipAccessType',
								width:275,
								mode: 'local',
								editable: false,
								forceSelection: true,
								selectOnFocus: true,
								triggerAction: 'all',
								fieldLabel: "{/literal}{#str_LabelIPAccessType#}{literal}",
								store: new Ext.data.ArrayStore({
        							fields: [
           								{name: 'id'},
           								{name: 'name'}
        							],
        							data: [[0, "{/literal}{#str_LabelUseDefault#}{literal}"], [1, "{/literal}{#str_LabelAppendDefault#}{literal}"], [2, "{/literal}{#str_LabelOverrideDefault#}{literal}"]]
        						}),
        						post: true,
								valueField: 'id',
								displayField: 'name',
								useID: true,
								post: true,
								listeners: { 'select': setIpAccessList },
								value: ipAccessType
							},
							{
								xtype:'textarea',
								id: 'ipaccesslist',
								name: 'ipaccesslist',
								fieldLabel: '',
								width: 360,
								height: 55,
								post: true,
								maskRe: /^(\d{1,3})|(\.)|(,)|(\s)|(\*)$/, enableKeyEvents: true,
								listeners: {
        							render: function(p) {
           		 						p.getEl().dom.parentNode.parentNode.previousSibling.style.marginBottom = '1px';
        							},
        							keyup: function(field, event)
        							{
        								var userIPAccessListObj = Ext.getCmp('useripaccesslist');
        								if (Ext.getCmp('ipAccessType').getValue() == 1)
										{
											userIPAccessListObj.setValue(defaultIpAccessList+', '+field.getValue());
										}
										else
										{
											userIPAccessListObj.setValue(field.getValue());
        								}
        							}
    							}
							},
							{
								xtype:'textarea',
								id: 'useripaccesslist',
								name: 'useripaccesslist',
								fieldLabel:  "{/literal}{#str_LabelUserIPAccessList#}{literal}",
								width: 360,
								height: 55,
								post: true,
								disabled: true
							}
						]
					}
				]
			}
		]
	});


	function setIpAccessList()
	{
		var ipAccessListObj = Ext.getCmp('ipaccesslist');
		var userIPAccessListObj = Ext.getCmp('useripaccesslist');

		if (Ext.getCmp('ipAccessType').getValue() == 0)
		{
			userIPAccessListObj.setValue(defaultIpAccessList);
			ipAccessListObj.setValue(defaultIpAccessList);
			ipAccessListObj.disable();
			ipAccessList = '';
		}
		else
		{
			ipAccessListObj.setValue(ipAccessList);
			ipAccessListObj.enable();

			if (Ext.getCmp('ipAccessType').getValue() == 1)
			{
				userIPAccessListObj.setValue(defaultIpAccessList + ', ' + ipAccessListObj.getValue());
			}
			else
			{
				userIPAccessListObj.setValue(ipAccessListObj.getValue());
			}
		}
	}

	/* save functions */
	function addsaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminUsers.add&ref={/literal}{$ref}{literal}';
		var fp = Ext.getCmp('usersForm'), form = fp.getForm();
		var paramArray = new Object();
		paramArray['isactive'] = '';

		if (Ext.getCmp('isactive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}

		paramArray['format'] = ((document.location.protocol != 'https:') ? 1 : 0);

		Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", saveCallback);
	}

	/* save functions */
	function editsaveHandler(btn, ev)
	{
		var selectID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));

		var submitURL = 'index.php?fsaction=AdminUsers.edit&ref={/literal}{$ref}{literal}&id=' + selectID;
		var fp = Ext.getCmp('usersForm'), form = fp.getForm();
		var paramArray = new Object();
		paramArray['isactive'] = '';
		if (Ext.getCmp('isactive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}

		paramArray['format'] = ((document.location.protocol != 'https:') ? 1 : 0);

		Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", saveCallback);
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

	/* create modal window for add and edit */
	gDialogObj = new Ext.Window({
		id: 'dialog',
	  	closable:false,
	  	plain:true,
	  	modal:true,
	  	draggable:true,
	  	resizable:false,
	  	layout: 'fit',
	  	autoHeight:false,
	  	autoHeight: true,
	  	width: 560,
	  	items: dialogFormPanelObj,
	  	listeners: {
			'close': {
				fn: function(){
					userEditWindowExists = false;
				}
			}
		},
	  	cls: 'left-right-buttons',
	  	title: "{/literal}{$title}{literal}",
	  	buttons:
		[
			new Ext.form.Checkbox({
				id: 'isactive',
				name: 'isactive',
				boxLabel: "{/literal}{#str_LabelActive#}{literal}",
				post: true,
				cls: 'x-btn-left',
      			ctCls: 'width_100',
      			{/literal}{if $activechecked == 1}{literal}
					checked: true
				{/literal}{else}{literal}
					checked: false
				{/literal}{/if}{literal}
			}),
			{
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(){ Ext.getCmp('dialog').close();},
				cls: 'x-btn-right'
			},
			{
				text: "{/literal}{#str_ButtonAdd#}{literal}",
				id: 'addEditButton',
				cls: 'x-btn-right',
				/*
				 *	Use listener instead of handler, as handler seems to have an issue where the hover class is not removed from the button when opening a new window
				 *  which also stops the dialog opening for a second time.
				 */
				listeners:
				{
					click:
					{
						fn: function()
						{
							/* Check the form is valid before authenticating. */
							if (Ext.getCmp('usersForm').getForm().isValid())
							{
								/* Reauthenticate the logged in user to make the changes */
								var reason = '';
								var successCallback = function() {};
								
								{/literal}{if $isEdit == 0}{literal}
									reason = 'USER-ADD';
									successCallback = addsaveHandler;
								{/literal}{else}{literal}
									reason = 'USER-EDIT';
									successCallback = editsaveHandler;
								{/literal}{/if}{literal}

								showAdminReauthDialogue(
								{
									ref: {/literal}{$ref}{literal},
									reason: reason,
									title: Ext.taopix.ReauthenticationDialog.strings.titleAuthenticateToSave,
									success: successCallback
								});
							}
						}
					}
				},
				{/literal}{if $isEdit == 0}{literal}
				text: "{/literal}{#str_ButtonAdd#}{literal}"
				{/literal}{else}{literal}
				text: "{/literal}{#str_ButtonUpdate#}{literal}"
				{/literal}{/if}{literal}
			}
		]
	});

	{/literal}{if $optionms}{literal}
	{/literal}{if $isEdit == 0}{literal}
		Ext.getCmp('company').store.on({'load': function(){
			Ext.getCmp('company').setValue(Ext.getCmp('company').store.getAt(0).get('code'));
			}
		});
	{/literal}{/if}{literal}
	{/literal}{/if}{literal}

	var mainPanel = Ext.getCmp('dialog');
	Ext.getCmp('brand').hide();
	{/literal}{if $isEdit == 0}{literal}
		{/literal}{if $optionms}{literal}
			Ext.getCmp('productionsite').hide();
			Ext.getCmp('company').hide();
		{/literal}{/if}{literal}
		{/literal}{if $optioncfs}{literal}
			Ext.getCmp('store').hide();
		{/literal}{/if}{literal}

		{/literal}{if $loggedInAs == $TPX_LOGIN_SYSTEM_ADMIN}{literal}
			var loginTypeCombo = Ext.getCmp('logintype');
			setLoginForm(loginTypeCombo, '', '');
		{/literal}{/if}{literal}

		{/literal}{if $loggedInAs == $TPX_LOGIN_COMPANY_ADMIN}{literal}
			var loginTypeCombo = Ext.getCmp('logintype');
			setLoginForm(loginTypeCombo, '', '');
		{/literal}{/if}{literal}

		{/literal}{if $loggedInAs == $TPX_LOGIN_SITE_ADMIN}{literal}
			var loginTypeCombo = Ext.getCmp('logintype');
			setLoginForm(loginTypeCombo, '', '');
		{/literal}{/if}{literal}

	{/literal}{else}{literal}
		{/literal}{if $usertype != $TPX_LOGIN_STORE_USER || $usertype != $TPX_LOGIN_DISTRIBUTION_CENTRE_USER}{literal}
				var loginTypeCombo = Ext.getCmp('logintype');
				setLoginForm(loginTypeCombo, '', '');
		{/literal}{/if}{literal}
		{/literal}{if $userid == 1}{literal}
			Ext.getCmp('logintype').disable();
			Ext.getCmp('isactive').disable();
		{/literal}{/if}{literal}
	{/literal}{/if}{literal}

 	setIpAccessList();

	mainPanel.show();
}


{/literal}
