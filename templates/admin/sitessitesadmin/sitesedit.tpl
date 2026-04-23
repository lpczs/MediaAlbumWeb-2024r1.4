{literal}

function initialize(pParams)
{
	var gOpeningTimes = new Object();
	var isProductionSite = {/literal}{$isproductionsite}{literal};
	var gCurrentLanguage = "{/literal}{$defaultlang}{literal}";
	var removedDistrCentreInfo = '';
	var productCheckboxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({checkOnly: true, sortable: true});

	{/literal}
	{section name=index loop=$openingtimeslist}
		gOpeningTimes["{$openingtimeslist[index].code}"] = "{$openingtimeslist[index].name}".replace(/\\n/ig, '\r');
	{/section}
	{literal}

	/* save functions */
	function editHandler(btn, ev)
	{
		var fp = Ext.getCmp('mainform'), form = fp.getForm();

		/* server parameters are sent to the server */
		var parameter = Ext.getCmp('addressForm').getAddressValues();

		if (Ext.getCmp('active').checked)
		{
			parameter['active'] = '1';
		}
		else
		{
			parameter['active'] = '0';
		}

		if (isProductionSite == 1) {
			if (Ext.getCmp('acceptallproducts').checked)
			{
				parameter['acceptallproducts'] = '1';
			}
			else
			{
				parameter['acceptallproducts'] = '0';
				parameter['acceptedproductcodes'] = Ext.taopix.gridSelection2CodeList(gDialogObj.findById('productgrid'));
			}
		}

		var openingTimes = '';
		for (x in gOpeningTimes)
		{
			if (gOpeningTimes[x] != '')
			{
				if (openingTimes != '')
				{
					openingTimes = openingTimes + '<p>';
				}
				openingTimes = openingTimes + x + ' ' + gOpeningTimes[x];
			}
		}
		parameter['openingtimes'] = openingTimes;
		parameter['siteid'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
		Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=AdminSitesSitesAdmin.edit', "{/literal}{#str_MessageSaving#}{literal}", editCallback);
	}

	function editCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var gridObj = gMainWindowObj.findById('maingrid');
			var dataStore = gridObj.store;
			Ext.taopix.updateDataStore(dataStore, pActionData.result.data);
			dataStore.reload();
			gDialogObj.close();
		}
		else
		{
			if (pActionData.result.resultparam == '-1')
			{
				icon = Ext.MessageBox.WARNING;

				Ext.MessageBox.show({
					title: pActionData.result.title,
					msg: pActionData.result.msg,
					buttons: Ext.MessageBox.OK,
					icon: icon
				});
				Ext.getCmp('sitetype').markInvalid("{/literal}{#str_ErrorDistCentreAssignedToStore#}{literal}");
			}
		}
	}

	function addHandler(btn, ev)
	{
		var fp = Ext.getCmp('mainform'), form = fp.getForm();

		/* server parameters are sent to the server */
		var parameter = Ext.getCmp('addressForm').getAddressValues();

		if (Ext.getCmp('active').checked)
		{
			parameter['active'] = '1';
		}
		else
		{
			parameter['active'] = '0';
		}

		{/literal}{if $isproductionsite == 1}{literal}
		if (Ext.getCmp('acceptallproducts').checked)
		{
			parameter['acceptallproducts'] = '1';
		}
		else
		{
			parameter['acceptallproducts'] = '0';
			parameter['id'] = Ext.taopix.gridSelection2IDList(gDialogObj.findById('productgrid'));
		}
		{/literal}{/if}{literal}

		var openingTimes = '';
		for (x in gOpeningTimes)
		{
			if (gOpeningTimes[x] != '')
			{
				if (openingTimes != '')
				{
					openingTimes = openingTimes + '<p>';
				}
				openingTimes = openingTimes + x + ' ' + gOpeningTimes[x];
			}
		}
		parameter['openingtimes'] = openingTimes;
		parameter['siteid'] = '-1';
		Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=AdminSitesSitesAdmin.add', "{/literal}{#str_MessageSaving#}{literal}", addCallback);
	}


	function addCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var gridObj = gMainWindowObj.findById('maingrid');
			gridObj.store.reload();
			gDialogObj.close();
		}
	}

	function forceAlphaNumeric()
	{
		var code = Ext.getCmp('sitecode').getValue();
    	code = code.toUpperCase();
    	code = code.replace(/[^A-Z_0-9\-]+/g, "");
    	Ext.getCmp('sitecode').setValue(code);
	}

	var topPanel = new Ext.Panel({
		id: 'topPanel',
		layout: 'form',
		style:'background: #c9d8ed; padding: 3px 0; border:1px solid #8ca9cf',
		plain:true,
		bodyBorder: false,
		border: false,
		defaults: {labelWidth: 140},
		bodyStyle:'padding:5px 5px 0; border-top: 0px',
		items: [
			new Ext.Container({
				layout: 'form',
				defaults: {xtype: 'textfield', width: 230},
				width:700,
				items:[
					{
						id: 'sitecode',
						name: 'sitecode',
						fieldLabel: "{/literal}{#str_LabelSiteCode#}{literal}",
						{/literal}{if $id eq -1}{literal}
							readOnly: false,
							maxLength: 50,
							style: {textTransform: "uppercase"},
						{/literal}{else}{literal}
							readOnly: true,
							style: 'background:#c9d8ed; textTransform: uppercase',
							value: "{/literal}{$sitecode}{literal}",
						{/literal}{/if}{literal}
						allowBlank: false,
						listeners:{
  							blur:{	fn: forceAlphaNumeric }
  						},
						post: true,
                        labelStyle: 'text-align:right;'
					},
					{
                    xtype: 'hidden',
                    id: 'company',
                    name: 'company',
                    value: '{/literal}{$companycode}{literal}',
                    post: true
                    },
					{
						id: 'sitename',
						name: 'sitename',
						fieldLabel: "{/literal}{#str_LabelSiteName#}{literal}",
						allowBlank: false,
						{/literal}{if $id != -1}{literal}
							value: "{/literal}{$sitename}{literal}",
						{/literal}{/if}{literal}
						post: true,
                        labelStyle: 'text-align:right;'
					}
					{/literal}{if $optionCFS}{literal}
					,
						new Ext.form.ComboBox({
							id: 'sitetype',
							name: 'sitetype',
							hiddenName: 'sitetype_hn',
							hiddenId: 'sitetype_hi',
							width:175,
							allowBlank: false,
							mode: 'local',
							editable: false,
							forceSelection: true,
                            labelStyle: 'text-align:right;',
							{/literal}{if $isproductionsite == 1}{literal}
								store: new Ext.data.ArrayStore({
									id: 0,
									fields: ['id', 'name'],
									data: [
											['0', "{/literal}{#str_SiteTypeProductionOnly#}{literal}"],
											['2', "{/literal}{#str_SiteTypeProductionAndStore#}{literal}"]
									]
								}),
							{/literal}{else}{literal}
								store: new Ext.data.ArrayStore({
									id: 0,
									fields: ['id', 'name'],
									data: [
											['1', "{/literal}{#str_SiteTypeDistributionCentre#}{literal}"],
											['2', "{/literal}{#str_SiteTypeStore#}{literal}"]
									]
								}),
							{/literal}{/if}{literal}
							value: '{/literal}{$sitetype}{literal}',
							validationEvent:false,
							post: true,
							valueField: 'id',
							displayField: 'name',
							useID: true,
							post: true,
							fieldLabel: "{/literal}{#str_LabelSiteType#}{literal}",
							triggerAction: 'all',
							listeners: {
								'beforeselect': function(combo, record, index){
									if (record.id != combo.getValue())
									{
										{/literal}{if $isproductionsite == 1 && $sitetype == 2 && $usersassigned > 0 }{literal}
											Ext.MessageBox.show({title:"{/literal}{#str_TitleError#}{literal}" ,	msg: "{/literal}{#str_ErrorOnChangeProductionStoreUsed#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
											return false;
										{/literal}{/if}{literal}

										{/literal}{if $isproductionsite == 0 && $sitetype == 2 && $usersassigned > 0 }{literal}
											Ext.MessageBox.show({title:"{/literal}{#str_TitleError#}{literal}" ,	msg: "{/literal}{#str_ErrorOnChangeStoreUsed#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
											return false;
										{/literal}{/if}{literal}

										{/literal}{if $isproductionsite == 0 && $sitetype == 1 && $usersassigned > 0 }{literal}
											Ext.MessageBox.show({title:"{/literal}{#str_TitleError#}{literal}" ,	msg: "{/literal}{#str_ErrorOnChangeDistrCentreUsed#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
											return false;
										{/literal}{/if}{literal}

										{/literal}{if $id != -1 && $isproductionsite == 0 }{literal}
										var distributioncentreSel = Ext.getCmp('distributioncentre');
										if (distributioncentreSel)
										{
											if (combo.getValue() == 1)
											{
												/* remove distribution cenre record from the datastore */
												/*removedDistrCentreInfo*/
												var sitecodeSel = Ext.getCmp('sitecode');
												if (sitecodeSel)
												{
													var pos = distributioncentreSel.store.findExact('id',sitecodeSel.getValue());
													removedDistrCentreInfo = distributioncentreSel.store.getAt(pos);
													distributioncentreSel.store.removeAt(pos);
												}
											}
											else
											{
												/* insert distribution cenre record to the datastore */
												if (removedDistrCentreInfo)
												{
													distributioncentreSel.store.add(removedDistrCentreInfo);
												}

											}
										}
										{/literal}{/if}{literal}
									}
								},
								'select': {
									fn: function(comboBox, record, index){
											Ext.getCmp('storeSettings').setDisabled(comboBox.value != '2');
											Ext.getCmp('sitegroup').allowBlank= (comboBox.value != '2');

											{/literal}{if $isproductionsite == 1}{literal}
												if (this.getValue() == 0)
												{
													Ext.getCmp('siteonline').disable();
												}
												else
												{
													Ext.getCmp('siteonline').enable();
												}
											{/literal}{/if}{literal}
									}
								}
							}
					})
					{/literal}{/if}{literal}
				]
			})
		]
	});


	var dialogFormPanelObj = new Ext.taopix.FormPanel({
		id: 'mainform',
		header: false,
		frame:true,
		width: 550,
		layout: 'form',
		defaultType: 'textfield',
		bodyStyle:'border-bottom: 1px solid #96bde7;',
		autoHeight: true,
		items: [ topPanel,

		{ /* tabpanel */
			xtype: 'tabpanel',
			id: 'maintabpanel',
			deferredRender: false,
			activeTab: 0,
			autoWidth: 200,
			height: 270,
			shadow: true,
			plain:true,
			bodyBorder: false,
			border: false,
			style:'margin-top:6px; ',
			bodyStyle:'border-right: 1px solid #96bde7; border-left: 1px solid #96bde7; ',
			defaults:{frame: false, autoScroll: true, hideMode:'offsets', layout: 'form', labelWidth: 150, bodyStyle:'padding:5px 10px 0 10px; border-top: 0px; background-color: #eaf0f8;'},
			items: [
				{ /* fields */
					title: "{/literal}{#str_TitleSectionSiteContact#}{literal}",
					defaults:{xtype: 'textfield', width: 230},
					items: [
							{
								id: 'firstname',
								name: 'firstname',
								fieldLabel: "{/literal}{#str_LabelFirstName#}{literal}",
								value: '{/literal}{$firstname}{literal}',
								post: true
							},
							{
								id: 'lastname',
								name: 'lastname',
								fieldLabel: "{/literal}{#str_LabelLastName#}{literal}",
								value: '{/literal}{$lastname}{literal}',
								post: true
							},
							{
								id: 'telephone',
								name: 'telephone',
								fieldLabel: "{/literal}{#str_LabelTelephoneNumber#}{literal}",
								value: '{/literal}{$telephone}{literal}',
								listeners: {'blur': {fn: function(obj){CJKHalfWidthFullWidthToASCII(obj.getEl().dom, false)}}},
								post: true
							},
							{
								id: 'email',
								name: 'email',
								fieldLabel: "{/literal}{#str_LabelEmailAddress#}{literal}",
								validateOnBlur: true,
								value: '{/literal}{$email}{literal}',
								vtype: 'email',
								post: true
							},

							{/literal}{if $optionCFS}{literal}
							{
								xtype: 'panel', layout: 'form', autoWidth: true, defaults:{xtype: 'textfield', width: 230},
								items: [
									{
										id: 'storeurl',
										name: 'storeurl',
										fieldLabel: "{/literal}{#str_LabelSiteURL#}{literal}",
										value: '{/literal}{$storeurl}{literal}',
										validateOnBlur: true,
										vtype: 'url',
										post: true
									}
								]
							},
							{/literal}{/if}{literal}

							{
								id: 'smtpname',
								name: 'smtpname',
								fieldLabel: "{/literal}{#str_LabelSmtpName#}{literal}",
								width: 230,
								value: '{/literal}{$smtpname}{literal}',
								post: true
							},
							{
								id: 'smtpemail',
								name: 'smtpemail',
								fieldLabel: "{/literal}{#str_LabelSmtpAddress#}{literal}",
								width: 230,
								value: '{/literal}{$smtpemail}{literal}',
								vtype: 'email',
								post: true
							},

							new Ext.form.Checkbox({
								id: 'siteonline',
								name: 'siteonline',
								boxLabel: "{/literal}{#str_LabelSiteOnline#}{literal}",
								hideLabel: true,
								{/literal}{if $siteonline == 1}{literal}
									checked: true,
								{/literal}{else}{literal}
									checked: false,
								{/literal}{/if}{literal}
								{/literal}{if $sitetype == 0}{literal}
									disabled: true,
								{/literal}{/if}{literal}
								post: true
							})
						]
					},

					{
						title: "{/literal}{#str_TitleSectionAddress#}{literal}",
						id: 'addressTab',
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
					        		countryCode:"{$countrycode}",
					        		address1:"{$address1}",
					        		address2:"{$address2}",
					        		address3:"{$address3}",
					        		address4:"{$address4}",
					        		add41:"{$add41}",
					        		add42:"{$add42}",
					        		add43:"{$add43}",
					        		city:"{$city}",
					    			countyName: "{$county}",
					    	        countyCode: "{$regioncode}",
					    	        stateName: "{$state}",
					    	        stateCode: "{$regioncode}",
						            postCode: "{$postcode}"
								{literal}
								}
						     })
						]
					}

					{/literal}{if $isproductionsite == 1}{literal}
						,{
						title: "{/literal}{#str_TitleSectionProductionSettings#}{literal}",
						layout: 'form',
						defaults:{xtype: 'textfield'},
						items: [
							new Ext.form.Checkbox({
								id: 'acceptallproducts',
								name: 'acceptallproducts',
								boxLabel: "{/literal}{#str_LabelAcceptAllProducts#}{literal}",
								hideLabel: true,
								{/literal}{if $acceptallproducts == 1}{literal}
									checked: true,
								{/literal}{else}{literal}
									checked: false,
								{/literal}{/if}{literal}
								listeners: {
									'check': {
										/* this 'scope' value is CRITICAL so that the event is fired in */
										/* the scope of the component, not the anonymous function...    */

										scope: this,

										fn: function(cb, checked){
											if (checked)
											{
												Ext.getCmp('productgrid').hide();
											}
											else
											{
												Ext.getCmp('productgrid').show();
											}
										}
									}
								}
							}),
							new Ext.grid.GridPanel({
								id: 'productgrid',
								width: 650,
								height: 205,
								style: 'border:1px solid #b4b8c8',
								deferRowRender:false,
								{/literal}{if $acceptallproducts == 1}{literal}
									hidden: true,
								{/literal}{else}{literal}
									hidden: false,
								{/literal}{/if}{literal}
								store: new Ext.data.ArrayStore({
									id: 0,
									fields: ['id', 'code', 'name', 'active'],
									data: [
									{/literal}
									{section name=index loop=$productlist}
										{if $smarty.section.index.last}
											["{$productlist[index].id}", "{$productlist[index].code}", "{$productlist[index].name}", "{$productlist[index].active}"]
										{else}
											["{$productlist[index].id}", "{$productlist[index].code}", "{$productlist[index].name}", "{$productlist[index].active}"],
										{/if}
									{/section}
									{literal}
									]
								}),
								sm: productCheckboxSelectionModelObj,
								colModel: new Ext.grid.ColumnModel({
									defaults: {
										sortable: true,
										resizable: true
									},
									columns: [
										productCheckboxSelectionModelObj,
										{header: 'id', width: 30, dataIndex: 'id', hidden : true},
										{header: "{/literal}{#str_LabelProductCode#}{literal}", width: 150, dataIndex: 'code', renderer: logicColumnRenderer},
										{header: "{/literal}{#str_LabelProductName#}{literal}", width: 180, dataIndex: 'name', renderer: logicColumnRenderer},
										{header: "{/literal}{#str_LabelActive#}{literal}", width: 80, dataIndex: 'active', renderer: logicColumnRenderer, align: 'right'}
									]
								}),
								enableColLock:false,
								draggable:false,
								enableColumnHide:false,
								enableColumnMove:false,
								trackMouseOver:false,
								stripeRows:true,
								columnLines:true,
								autoExpandColumn: 3
							})
						]
					}
					{/literal}{/if}{literal}

					{/literal}{if $optionCFS}{literal}
					,{
						title: "{/literal}{#str_TitleSectionStoreSettings#}{literal}",
						id:'storeSettings',
						listeners: {
							'disable': function()
							{
								var tabpanel = Ext.getCmp('maintabpanel');
								if (tabpanel.getActiveTab().getId() == 'storeSettings')
								{
									tabpanel.activate(tabpanel.items.items[0].getId());
								}
							}
						},
						{/literal}{if $sitetype == 2}
							disabled: false,
						{else}
							disabled: true,
						{/if}{literal}
						defaults:{xtype: 'textfield', width: 430},
						items: [
							{/literal}{if $isproductionsite == 0}{literal}
						    new Ext.form.ComboBox({
								id: 'distributioncentre',
								name: 'distributioncentre',
								mode: 'local',
								editable: false,
								forceSelection: true,
								selectOnFocus: true,
								triggerAction: 'all',
								fieldLabel: "{/literal}{#str_LabelDistributionCentre#}{literal}",
								store: new Ext.data.ArrayStore({
									id: 0,
									fields: ['id', 'name'],
									data: [
										['', "- {/literal}{#str_LabelNone#}{literal} -"]
										{/literal}
										{section name=index loop=$distributioncentres}
											{if $smarty.section.index.last}
												,["{$distributioncentres[index].code}", "{$distributioncentres[index].name}"]
											{else}
												,["{$distributioncentres[index].code}", "{$distributioncentres[index].name}"]
											{/if}
										{/section}
										{literal}
									]
								}),
								valueField: 'id',
								displayField: 'name',
								useID: true,
								value: '{/literal}{$distributioncentrecode}{literal}',
								post: true
							}),
							{/literal}{/if}{literal}
						    new Ext.form.ComboBox({
								id: 'sitegroup',
								name: 'sitegroup',
								mode: 'local',
								editable: false,
								forceSelection: true,
								selectOnFocus: true,
								triggerAction: 'all',
								fieldLabel: "{/literal}{#str_LabelStoreGroup#}{literal}",
								store: new Ext.data.ArrayStore({
									id: 0,
									fields: ['id', 'name'],
									data: [
										{/literal}
										{section name=index loop=$sitegroups}
											{if $smarty.section.index.last}
												["{$sitegroups[index].code}", "{$sitegroups[index].name}"]
											{else}
												["{$sitegroups[index].code}", "{$sitegroups[index].name}"],
											{/if}
										{/section}
										{literal}
									]
								}),
								valueField: 'id',
								displayField: 'name',
								blankText: "{/literal}{#str_ErrorNoSiteGroups#}{literal}",
								{/literal}{if $sitetype == 2}
									allowBlank: false,
								{else}
									allowBlank: true,
								{/if}{literal}
								useID: true,
								value: '{/literal}{$sitegroup}{literal}',
								post: true
							}),

						    {
						        xtype:'fieldset',
						        collapsed: false,
						        layout: 'form',
						        title: "{/literal}{#str_LabelOpeningTimes#}{literal}",
						        height: 158,
						        width: 630,
								style: 'margin-top:15px',
						        defaultType: 'textfield',
								defaults:{labelWidth: 140},

						        items :[
									{xtype: 'panel', layout: 'form', defaults:{labelWidth: 140}, autoWidth: true,
									 items: [
									 	new Ext.form.ComboBox({
											id: 'language',
											name: 'language',
											mode: 'local',
											width: 250,
											editable: false,
											forceSelection: true,
											selectOnFocus: true,
											triggerAction: 'all',
											fieldLabel: "{/literal}{#str_LabelLanguage#}{literal}",
											store: new Ext.data.ArrayStore({
												id: 0,
												fields: ['id', 'name'],
												data: [
													{/literal}
													{section name=index loop=$languagelist}
														{if $smarty.section.index.last}
															['{$languagelist[index].code}', '{$languagelist[index].name}']
														{else}
															['{$languagelist[index].code}', '{$languagelist[index].name}'],
														{/if}
													{/section}
													{literal}
												]
											}),
											listeners: {
												'select': {
													/* this 'scope' value is CRITICAL so that the event is fired in */
													/* the scope of the component, not the anonymous function...    */
													scope: this,

													fn: function(comboBox, record, index){
														gCurrentLanguage = Ext.getCmp('language').value;
														Ext.getCmp('otimes').setValue(gOpeningTimes[gCurrentLanguage]);
													}
												}
											},
											valueField: 'id',
											displayField: 'name',
											useID: true,
											value: gCurrentLanguage,
											post: true
										}),

										{
											id: 'otimes',
											name: 'otimes',
											width: 575,
											height: 105,
											xtype: 'textarea',
											hideLabel: true,
											listeners: {
												'blur': {
													scope: this,
													fn: function()
													{
														gOpeningTimes[gCurrentLanguage] = Ext.getCmp('otimes').getValue();
													}
												}
											},
											value: gOpeningTimes[gCurrentLanguage]
										}
									 ]}
						        ]
						    }
						]
					}
					{/literal}{/if}{literal}
				]
			}
		],

		baseParams:
		{
			ref: '{/literal}{$ref}{literal}'
		}
	});

	gDialogObj = new Ext.Window({
		id: 'dialog',
		{/literal}{if $id eq -1}{literal}
			title: "{/literal}{#str_TitleSiteAdministrationSiteAdd#}{literal}",
		{/literal}{else}{literal}
			title: "{/literal}{#str_TitleSiteAdministrationEdit#}{literal}",
		{/literal}{/if}{literal}
		closable:false,
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		width:750,
		autoHeight:true,
		items: dialogFormPanelObj,
		cls: 'left-right-buttons',
		buttons:
		[
			new Ext.form.Checkbox({
				id: 'active',
				name: 'active',
				cls: 'x-btn-left',
				ctCls: 'width_100',
				boxLabel: "{/literal}{#str_LabelActive#}{literal}",
				post: true,
				{/literal}{if $isactive == 1}{literal}
					checked: true
				{/literal}{else}{literal}
					checked: false
				{/literal}{/if}{literal}
			}),
			{
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(){ gDialogObj.close(); },
				cls: 'x-btn-right'
			},
			{
				id: 'addEditButton',
				cls: 'x-btn-right',
				{/literal}{if $id == -1}{literal}
					handler: addHandler,
					text: "{/literal}{#str_ButtonAdd#}{literal}"
				{/literal}{else}{literal}
					handler: editHandler,
					text: "{/literal}{#str_ButtonUpdate#}{literal}"
				{/literal}{/if}{literal}
			}
		]
	});

	var acceptedProducts = new Array(
		{/literal}
		{section name=index loop=$acceptedproducts}
			{if $smarty.section.index.last}
				"{$acceptedproducts[index]}"
			{else}
				"{$acceptedproducts[index]}",
			{/if}
		{/section}
		{literal}
	);

	gDialogObj.show();

    var mainTabPanel = Ext.getCmp('maintabpanel');
    mainTabPanel.setActiveTab(1);
    mainTabPanel.setActiveTab(2);
    mainTabPanel.setActiveTab(3);
    mainTabPanel.setActiveTab(0);

	{/literal}{if $isproductionsite == 1}{literal}

		var acceptedProductRowIndexes = [];
		var gridObj = gDialogObj.findById('productgrid');

		var dataStore = gridObj.store;
		var storeTotalLength = dataStore.data.items.length;

		for (i = 0; i < storeTotalLength; i++)
		{
			var tempCode = dataStore.data.items[i].data.code;

			if (acceptedProducts.indexOf(tempCode) != -1)
			{
				acceptedProductRowIndexes.push(i);
			}
		}

		productCheckboxSelectionModelObj.selectRows(acceptedProductRowIndexes);

	{/literal}{/if}{literal}
}

{/literal}
