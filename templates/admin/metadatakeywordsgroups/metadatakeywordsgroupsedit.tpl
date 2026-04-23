{literal}
function initialize(pParams)
{
	var sectionChecked = false;

	onCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});

				if (pActionData.result.action == 'getassociatedcomponentlist')
				{
					Ext.getCmp('keywordSection').setValue('COMPONENT');
					sectionChecked = false;
				}
			}
			else
			{
				if (pActionData.result.action != 'getassociatedcomponentlist')
				{
					Ext.getCmp('keywordsGroupsGrid').getStore().reload();

					if (Ext.getCmp('dialog').isVisible())
					{
						Ext.getCmp('dialog').close();
					}
				}
			}
		}
	};

	var editSaveHandler = function()
	{
		if (dialogFormPanelObj.getForm().isValid())
		{
			var parameter = [];
			var keywordsCodes = [];
			var defaultValues = [];
			var sortOrder = [];
			var productCodes = [];

			keywordSection = Ext.getCmp('keywordSection').getValue();

			var storeKeywords = Ext.getCmp('groupKeywords').getStore();
			Ext.each(storeKeywords.data.items, function(record, index)
			{
				keywordsCodes.push(record.data.kwCode);
				if (record.data.kwType == 'CHECKBOX')
				{
					defaultValues.push((record.data.kwDefault) ? record.data.kwDefault * 1 : 0);
				}
				else
				{
					defaultValues.push((record.data.kwDefault) ? record.data.kwDefault + '' : '');
				}

				 sortOrder.push(record.data.kwSortOrder);
			});

			if (keywordsCodes.length == 0)
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoKeywords#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
	    		return false;
			}

			var storeProducts = '';

			if (keywordSection == 'ORDER')
			{
				storeAllProductsCheck = Ext.getCmp('allProductsCheck');
				if (storeAllProductsCheck.checked)
				{
					productCodes.push('**ALL**');
				}
				else
				{
					storeProducts = Ext.getCmp('productGrid').getSelectionModel().getSelections();
					Ext.each(storeProducts, function(record, index)
					{
						 productCodes.push(record.data.code);
					});
				}

				if (productCodes.length == 0)
				{
					Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoProducts#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
					return false;
				}
			}

			/* check of keywords list changed */
			oldKeywords = [
				{/literal}{section name=index loop=$keywordslist}
					{if $smarty.section.index.last}
						{literal}{kwId: "{/literal}{$keywordslist[index].id}{literal}", kwCode: "{/literal}{$keywordslist[index].code}{literal}", kwType: "{/literal}{$keywordslist[index].type}{literal}", kwItems: "{/literal}{$keywordslist[index].values}{literal}", kwDefault: "{/literal}{$keywordslist[index].defaultValue}{literal}", kwSortOrder: "{/literal}{$keywordslist[index].sortOrder}{literal}" }{/literal}
					{else}
						{literal}{kwId: "{/literal}{$keywordslist[index].id}{literal}", kwCode: "{/literal}{$keywordslist[index].code}{literal}", kwType: "{/literal}{$keywordslist[index].type}{literal}", kwItems: "{/literal}{$keywordslist[index].values}{literal}", kwDefault: "{/literal}{$keywordslist[index].defaultValue}{literal}", kwSortOrder: "{/literal}{$keywordslist[index].sortOrder}{literal}" }{/literal},
					{/if}
				{/section}{literal}
			];

			var keywordsModified = 0;
			if (oldKeywords.length != keywordsCodes.length)
			{
				keywordsModified = 1;
			}
			else
			{
				outerloop:
				for (var i = 0, keyword, keywordExists; i < oldKeywords.length; i++)
				{
					keyword = oldKeywords[i];

					keywordExists = 0;
					for (j = 0; j < keywordsCodes.length; j++)
					{
						if (keywordsCodes[j] == keyword.kwCode)
						{
							keywordExists = 1;

							/* then check for the contents of keyword */
							if ((keyword.kwDefault != defaultValues[j]) || (keyword.kwSortOrder != sortOrder[j]))
							{
								keywordsModified = 1;
								break outerloop;
							}
							break;
						}
					}
					if (keywordExists == 0)
					{
						keywordsModified = 1;
						break;
					}
				}
			}

			parameter['keywordsCodes'] = keywordsCodes.join(',');
			parameter['defaultValues'] = defaultValues.join(',');
			parameter['sortOrder'] = sortOrder.join(',');
			parameter['products'] = productCodes.join(',');

			parameter['keywordsModified'] = keywordsModified;

			parameter['id'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('keywordsGroupsGrid'));
			{/literal}{if $keywordGroupId < 1}{literal}
				Ext.taopix.formPanelPost(dialogFormPanelObj, dialogFormPanelObj.getForm(), parameter, 'index.php?fsaction=AdminMetadataKeywordsGroups.add', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
    		{/literal}{else}{literal}
				Ext.taopix.formPanelPost(dialogFormPanelObj, dialogFormPanelObj.getForm(), parameter, 'index.php?fsaction=AdminMetadataKeywordsGroups.edit', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
    		{/literal}{/if}{literal}
		}

		return false;
	};

	var selectProducts = function()
	{
		var productStore = Ext.getCmp('productGrid').getStore();

		var productIds = [];
		for (var i = 0, recs; i < selectedProducts.length; i++)
		{
			recs = productStore.query('code', selectedProducts[i], false, true).items[0];
			productIds.push(productStore.indexOf(recs));
		}

		Ext.getCmp('productGrid').getSelectionModel().selectRows(productIds);
	};


	var productDataStore = new Ext.data.GroupingStore({
		remoteSort: true,
		remoteGroup: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminMetadataKeywordsGroups.getProductList&ref={/literal}{$ref}{literal}'}),
		baseParams: {'csrf_token': Ext.taopix.getCSRFToken()},
		method:'POST',
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
			{name: 'id', mapping: 0},
			{name: 'code', mapping: 1},
			{name: 'name', mapping: 2},
			{name: 'active', mapping: 3}
			])
		)
	});

	productDataStore.on('load', function()
	{
		// select products after the data store has loaded

		selectProducts();
	});

	productDataStore.load();


	var topPanel = new Ext.Panel({
		id: 'topPanel',
		layout: 'form',
		style:'background: #c9d8ed; padding: 3px 0; border:1px solid #8ca9cf',
		plain:true,
		bodyBorder: false,
		border: false,
		defaults: {xtype: 'textfield', labelWidth: 120},
		bodyStyle:'padding:5px 5px 0; border-top: 0px',
		items:
		[
			{
				xtype: 'combo',
				id: 'keywordSection',
				name: 'keywordSection',
				mode: 'local',
				editable: false,
				forceSelection: true,
				selectOnFocus: true,
				triggerAction: 'all',
				fieldLabel: "{/literal}{#str_LabelSection#}{literal}",
				width: 275,
				store: new Ext.data.ArrayStore({
					id: 0,
					fields: ['id', 'name'],
					data: [['ORDER', "{/literal}{#str_LabelOrder#}{literal}"], ['COMPONENT', "{/literal}{#str_LabelComponent#}{literal}"]]
				}),
				valueField: 'id',
				displayField: 'name',
				useID: true,
				allowBlank: false,
				{/literal}{if $keywordGroupId > 0}{literal}
					value: "{/literal}{$keywordSection}{literal}",
				{/literal}{/if}{literal}
				post: true,
				listeners: {
					'select': function(combo, record, index)
					{
						if (combo.getValue() == 'ORDER')
						{
							// if we are editing a group make sure is not attached to components
							{/literal}{if ($keywordGroupId > 0) && ($keywordSection == 'COMPONENT')}{literal}

								// sectionChecked is false when need to be tested
								// sectionChecked is true when tested and group not attached to a component
								if (sectionChecked == false)
								{
									sectionChecked = true;

									var paramArray = {};
									paramArray['headergroupid'] = {/literal}{$keywordGroupId}{literal};
									Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminMetadataKeywordsGroups.getAssociatedComponentList', "{/literal}{#str_str_MessageUpdating#}{literal}", onCallback);
								}

							{/literal}{/if}{literal}

							Ext.getCmp('productsTab').enable();
						}
						else
						{
							Ext.getCmp('productsTab').disable();
						}
					}
				}
			}
		]
	});


	var setDefaultValuePanel = function(mode, items, defaultVal)
	{
		var defValueTextObj = Ext.getCmp('defValueText');
		var defValueComboObj = Ext.getCmp('defValueCombo');
		var sortOrderObj = Ext.getCmp('sortOrder');

		defValueTextObj.enable();
		sortOrderObj.enable();

		defValueTextObj.setValue('');
		defValueComboObj.setValue('');

		if (!defaultVal)
		{
			defaultVal = '';
		}

		switch(mode)
		{
			case 'CHECKBOX':
				defValueTextObj.hide();
				defValueComboObj.show();
				defValueComboObj.getStore().loadData([[1, "{/literal}{#str_LabelChecked#}{literal}"], [0, "{/literal}{#str_LabelNotChecked#}{literal}"]]);
				if (defaultVal != '')
				{
					defValueComboObj.setValue(defaultVal);
				}
				else
				{
					defValueComboObj.setValue(0);
				}
				break;
			case 'POPUP':
			case 'RADIOGROUP':
				defValueTextObj.hide();
				defValueComboObj.show();
                var sItem = String(items);
                sItem =  '[["", "{/literal}{#str_LabelNoDefault#}{literal}"],' + sItem.substr(1, sItem.length -1);
				defValueComboObj.getStore().loadData(eval('(' + sItem + ')'));
				defValueComboObj.setValue(defaultVal);
				break;
			default:
				defValueTextObj.show();
				defValueComboObj.hide();
				defValueTextObj.setValue(defaultVal);
		}

		if (mode == '')
		{
			defValueTextObj.disable();
			sortOrderObj.disable();
		}
	};

	var defaultColRenderer = function(value, p, record, rowIndex, colIndex, store)
    {
    	if ((record.data.kwType == 'CHECKBOX') && (!record.data.kwDefault))
		{
			return 0;
		}
		return value;
    };

    /* Draggable grids */
	var groupKeywordsGrid = new Ext.grid.GridPanel({
		id: 'groupKeywords',
		title: "{/literal}{#str_SectionTitleMetaDataKeyWords#}{literal}",
		enableDragDrop: true,
		ddGroup: 'allKeywordsDDGroup',
		ddText: 'drag and drop items to remove',
		stripeRows: true,
		stateful: true,
		enableColLock:false,
		columnLines:true,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		trackMouseOver:false,
		height: 272,
		layout: 'fit',
		clicksToEdit: 1,
		bodyStyle: 'border: 1px solid #b4b8c8',
		ctCls: 'grid',
		store: new Ext.data.JsonStore({
			fields:
			[
				{name: 'kwId', mapping: 'kwId'},
				{name: 'kwCode', mapping: 'kwCode'},
				{name: 'kwType', mapping: 'kwType'},
				{name: 'kwItems', mapping: 'kwItems'},
				{name: 'kwDefault', mapping: 'kwDefault'},
				{name: 'kwSortOrder', mapping: 'kwSortOrder'}
			],
			data:
			{
				records :
				[
					{/literal}
					{section name=index loop=$keywordslist}
						{if $smarty.section.index.last}
							{literal}{kwId: "{/literal}{$keywordslist[index].id}{literal}", kwCode: "{/literal}{$keywordslist[index].code}{literal}", kwType: "{/literal}{$keywordslist[index].type}{literal}", kwItems: "{/literal}{$keywordslist[index].values}{literal}", kwDefault: "{/literal}{$keywordslist[index].defaultValue}{literal}", kwSortOrder: "{/literal}{$keywordslist[index].sortOrder}{literal}" }{/literal}
						{else}
							{literal}{kwId: "{/literal}{$keywordslist[index].id}{literal}", kwCode: "{/literal}{$keywordslist[index].code}{literal}", kwType: "{/literal}{$keywordslist[index].type}{literal}", kwItems: "{/literal}{$keywordslist[index].values}{literal}", kwDefault: "{/literal}{$keywordslist[index].defaultValue}{literal}", kwSortOrder: "{/literal}{$keywordslist[index].sortOrder}{literal}" }{/literal},
						{/if}
					{/section}
					{literal}
				]
			},
			root: 'records',
			sortInfo:{field: 'kwSortOrder', direction: "ASC"}
		}),
		columns:
		[
			{
				id:'codeCol',
				sortable: false,
				menuDisabled: true,
				header: "{/literal}{#str_LabelKeywordCode#}{literal}",
				dataIndex: 'kwCode',
				width: 140
			},
			{
				id: 'typeCol',
				header: "{/literal}{#str_LabelKeywordType#}{literal}",
				width: 110,
				sortable: false,
				dataIndex: 'kwType',
				menuDisabled: true
			},
			{
				id: 'defaultCol',
				header: "{/literal}{#str_LabelDefaultValue#}{literal}",
				width: 150,
				sortable: false,
				dataIndex: 'kwDefault',
				menuDisabled: true,
				renderer: defaultColRenderer
			},
			{
				id: 'sortCol',
				header: "{/literal}{#str_LabelSort#}{literal}",
				width: 70,
				sortable: false,
				dataIndex: 'kwSortOrder',
				menuDisabled: true,
				align: 'center'
			}
		],
		listeners:
		{
			'rowclick': function(grid, rowIndex, e)
			{
				var store = Ext.getCmp('groupKeywords').getStore();
				var count = store.getCount();
				var record = store.getAt(rowIndex);

				if (record)
				{
					var kwType = record.data.kwType;
					var elemCode = record.data.kwCode;
					var kwSortOrder = record.data.kwSortOrder;

					Ext.getCmp('sortOrder').setValue((kwSortOrder) ? kwSortOrder : 0);

					setDefaultValuePanel(kwType, record.data.kwItems, record.data.kwDefault);

					var target = e.getTarget();
					if (target.tagName.toLowerCase() == 'img')
					{
						switch (target.className)
						{
							case 'down':
								if (count > rowIndex + 1)
								{
									var nextRecord = store.getAt(rowIndex + 1);
									record.data.kwSortOrder += 1;
									nextRecord.data.kwSortOrder -= 1;
								}
								break;
							case 'up':
								if (rowIndex - 1 >= 0)
								{
									var nextRecord = store.getAt(rowIndex - 1);
									record.data.kwSortOrder -= 1;
									nextRecord.data.kwSortOrder += 1;
								}
								break;
							case 'last':
								Ext.each(store.data.items, function(record, index){
									if (record.data.kwCode != elemCode)
									{
										record.data.kwSortOrder -= 1;
									}
									else
									{
										record.data.kwSortOrder = count;
									}
								});
								break;
							case 'first':
								Ext.each(store.data.items, function(record, index){
									if (record.data.kwCode != elemCode)
									{
										record.data.kwSortOrder += 1;
									}
									else
									{
										record.data.kwSortOrder = 0;
									}
								});
								break;
						}
						store.singleSort('kwSortOrder', 'ASC');
					}
				}
			}
		}
	});

	var defaultValuePanel = {
		xtype: 'panel',
		layout: 'hbox',
		frame: true,
		style: 'margin-top: 3px; margin-bottom: 3px;',
        height: 65,
		items:
		[
			{
				xtype: 'panel',
				layout: 'form',
				defaults: {xtype: 'textfield', labelWidth: "125", width: 200},
                width: 400,
				items:
				[
					{
						xtype: 'textfield',
						id: 'defValueText',
						fieldLabel:"{/literal}{#str_LabelDefaultValue#}{literal}",
                        labelStyle:"width:125px;",
                        width: 150,
						enableKeyEvents: true,
						listeners: {
							'keyup': function(textfield, e)
							{
								var recordId = groupKeywordsGrid.getSelectionModel().getSelected().data.kwId;
								var store = groupKeywordsGrid.getStore();
								var storeRec = store.query('kwId', recordId, false, true).items[0];
								storeRec.data.kwDefault = textfield.getValue();
								store.commitChanges();
								store.singleSort('kwSortOrder', 'ASC');
							}
						}
					},
                    {
						xtype: 'numberfield',
						id: 'sortOrder',
						labelStyle:"width:125px;",
						width: 40,
						allowNegative: false,
						allowDecimals: false,
						fieldLabel: "{/literal}{#str_LabelSortOrder#}{literal}",
						enableKeyEvents: true,
						listeners: {
							'keyup': function(numberfield, e)
							{
								var recordId = groupKeywordsGrid.getSelectionModel().getSelected().data.kwId;
								var store = groupKeywordsGrid.getStore();
								var storeRec = store.query('kwId', recordId, false, true).items[0];
								storeRec.data.kwSortOrder = numberfield.getValue();
								store.commitChanges();
								store.singleSort('kwSortOrder', 'ASC');
							}
						}
					},
					{
						xtype: 'combo',
						id: 'defValueCombo',
                        labelStyle:"width:125px;",
						mode: 'local',
						editable: false,
						forceSelection: true,
						selectOnFocus: true,
						triggerAction: 'all',
						fieldLabel: "{/literal}{#str_LabelDefaultValue#}{literal}",
						width: 200,
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data: []
						}),
						valueField: 'id',
						displayField: 'name',
						useID: true,
						post: true,
						listeners: {
							'select': function(combo, record, index)
							{
								var recordId = groupKeywordsGrid.getSelectionModel().getSelected().data.kwId;
								var store = groupKeywordsGrid.getStore();
								var storeRec = store.query('kwId', recordId, false, true).items[0];
								storeRec.data.kwDefault = combo.getValue();
								store.commitChanges();
								store.singleSort('kwSortOrder', 'ASC');
							}
						}
					}
				]
			}
		]
	};


	var keywordsGridPanel = {
		xtype: 'panel',
		flex : 2,
		items: [groupKeywordsGrid, defaultValuePanel]
	};


	var allKeywordsGrid = new Ext.grid.GridPanel({
		id: 'allKeywords',
		title: "{/literal}{#str_SectionTitleMetaDataAllKeyWords#}{literal}",
		enableDragDrop: true,
		ddGroup: 'groupKeywordsDDGroup',
		ddText: 'drag and drop items to add',
		stripeRows: true,
		stateful: true,
		enableColLock:false,
		columnLines:true,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		trackMouseOver:false,
		width: 360,
		bodyStyle: 'border: 1px solid #b4b8c8',
		ctCls: 'grid',
		store: new Ext.data.JsonStore({
			fields:
			[
				{name: 'kwId', mapping: 'kwId'},
				{name: 'kwCode', mapping: 'kwCode'},
				{name: 'kwType', mapping: 'kwType'},
				{name: 'kwItems', mapping: 'kwItems'},
				{name: 'kwSortOrder', mapping: 'kwSortOrder'}
			],
			data:
			{
				records :
				[
					{/literal}
					{section name=index loop=$allkeywordslist}
						{if $smarty.section.index.last}
							{literal}{kwId: "{/literal}{$allkeywordslist[index].id}{literal}", kwCode: "{/literal}{$allkeywordslist[index].code}{literal}", kwType: "{/literal}{$allkeywordslist[index].type}{literal}", kwItems: "{/literal}{$allkeywordslist[index].values}{literal}", kwSortOrder: "0" }{/literal}
						{else}
							{literal}{kwId: "{/literal}{$allkeywordslist[index].id}{literal}", kwCode: "{/literal}{$allkeywordslist[index].code}{literal}", kwType: "{/literal}{$allkeywordslist[index].type}{literal}", kwItems: "{/literal}{$allkeywordslist[index].values}{literal}", kwSortOrder: "0" }{/literal},
						{/if}
					{/section}
					{literal}
				]
			},
			root: 'records'
		}),
		columns:
		[
			{
				id:'codeCol',
				sortable: false,
				menuDisabled: true,
				header: "{/literal}{#str_LabelKeywordCode#}{literal}",
				dataIndex: 'kwCode',
				width: 211
			},
			{
				id: 'typeCol',
				header: "{/literal}{#str_LabelKeywordType#}{literal}",
				width: 120,
				sortable: false,
				dataIndex: 'kwType',
				menuDisabled: true
			}
		]
	});

	var productCheckboxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({checkOnly: true, checked: true});

	var activeColRenderer = function(value, p, record, rowIndex, colIndex, store)
	{
		var className = '';
		if (record.data.active == 0)
		{
			if (colIndex == 4) value = "{/literal}{#str_LabelInactive#}{literal}";
			className = 'class="inactive"';
		}
		else
		{
			if (colIndex == 4) value = "{/literal}{#str_LabelActive#}{literal}";
		}

		return '<span ' + className + '>' + value + '</span>';
	};

	var productGrid = new Ext.grid.EditorGridPanel(
	{
		id: 'productGrid',
		width: 835,
		height: 290,
		style: 'border:1px solid #b4b8c8',
		deferRowRender:false,
		ctCls: 'grid',
		store: productDataStore,
		sm: productCheckboxSelectionModelObj,
		view: new Ext.grid.GroupingView({forceFit:false}),
		colModel: new Ext.grid.ColumnModel({
			defaults: {
				sortable: true,
				resizable: true,
				renderer: activeColRenderer
			},
			columns: [
				productCheckboxSelectionModelObj,
				{header: 'id', width: 30, dataIndex: 'id', sortable: true, hidden : true },
				{header: "{/literal}{#str_LabelProductCode#}{literal}", width: 200, dataIndex: 'code' },
				{header: "{/literal}{#str_LabelProductName#}{literal}", width: 180, dataIndex: 'name'},
				{header: "{/literal}{#str_LabelActive#}{literal}", width: 80, dataIndex: 'active', align: 'right'}
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
	});

	var allProductsCheckBoxPanel = new Ext.Panel(
	{
		style:
		{
			marginBottom: '4px'
		},
		items:
		[
			new Ext.form.Checkbox(
			{
				id: 'allProductsCheck',
				name: 'allProductsCheck',
				boxLabel: "{/literal}{#str_LabelSelectAll#}{literal}",
				hideLabel: true,
				checked: false,

				listeners:
				{
					'check':
					{
						fn: function(cb, checked)
						{
							if (checked)
							{
								Ext.getCmp('productGrid').disable();
							}
							else
							{
								Ext.getCmp('productGrid').enable();
							}
						}
					}
				}
			})
		]
	});


	var tabPanel = {
		xtype: 'tabpanel',
		id: 'mainTabPanel',
		deferredRender: false,
		activeTab: 0,
		width: 852,
		height: 380,
		shadow: true,
		plain:true,
		style:'margin-top:6px; ',
		defaults:{ autoScroll: true, hideMode:'offsets', bodyStyle:'padding:5px 0 0 0; border-top: 0px; background-color: #eaf0f8;'},
		items:
		[
			{
				title: "{/literal}{#str_SectionTitleMetaDataKeyWords#}{literal}",
				items:
				[
					{
						xtype: 'panel',
						width        : 850,
						height       : 340,
						layout       : 'hbox',
						defaults     : { style:'padding: 0 5px; '},
						layoutConfig : { align : 'stretch' },
						items        : [ keywordsGridPanel, allKeywordsGrid ]
					}
				]
			},
			{
				title: "{/literal}{#str_SectionTitleProducts#}{literal}",
				defaults: {xtype: 'textfield'},
				bodyStyle:'padding:5px 8px; background-color: #eaf0f8;',
				id: 'productsTab',
				items:
				[
					{ xtype: 'panel', layout: 'form', style: 'margin-bottom: 5px',
						items: [
							{
								xtype: 'combo',
								id: 'groupCode',
								name: 'groupCode',
								mode: 'local',
								editable: false,
								forceSelection: true,
								selectOnFocus: true,
								triggerAction: 'all',
								fieldLabel:  "{/literal}{#str_LabelLicenseKey#}{literal}",
								width: 375,
								store: new Ext.data.ArrayStore({
									id: 0,
									fields: ['id', 'name'],
									data: [
										{/literal}
										["", "{#str_LabelAll#}"]
										{section name=index loop=$licensekeylist}
											,["{$licensekeylist[index].id}", "{$licensekeylist[index].id} - {$licensekeylist[index].name}"]
										{/section}
										{literal}
									]
								}),
								valueField: 'id',
								displayField: 'name',
								useID: true,
								allowBlank: false,
								{/literal}{if $keywordGroupId > 0}{literal}
									value: "{/literal}{$licenseKey}{literal}",
								{/literal}{else}{literal}
									value: "",
								{/literal}{/if}{literal}
								post: true,
								listeners:{
									select:{
										fn: function(comboBox, record, index){

											var groupCode = Ext.getCmp('groupCode').getValue();

											var prodGrid = Ext.getCmp('productGrid');

											prodGrid.store.reload({params: {'groupcode': groupCode}});
										}
									}
								}
							}
						]
					},
					allProductsCheckBoxPanel,
					productGrid
				]
			}
		]
	};


	var dialogFormPanelObj = new Ext.taopix.FormPanel({
		id: 'mainForm',
		header: false,
		frame:true,
		layout: 'form',
		height: 515,
		defaultType: 'textfield',
		items: [ topPanel, tabPanel ],
		baseParams:	{ ref: '{/literal}{$ref}{literal}' }
	});


	gDialogObj = new Ext.Window({
		id: 'dialog',
		plain:true,
		title: "{/literal}{$title}{literal}",
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		width: 880,
		height: 505,
		items: dialogFormPanelObj,
		listeners: {
			'close': {
				fn: function(){
		keywordGroupsEditWindowExists = false;
				}
			}
		},
		buttons:
		[
			{
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(btn, ev){ gDialogObj.close(); },
				cls: 'x-btn-right'
			},
			{
				id: 'updateButton',
				handler: editSaveHandler,
				cls: 'x-btn-right',
				{/literal}{if $keywordGroupId < 1}{literal}
					text: "{/literal}{#str_ButtonAdd#}{literal}"
				{/literal}{else}{literal}
					text: "{/literal}{#str_ButtonUpdate#}{literal}"
				{/literal}{/if}{literal}
			}
		]
	});

	gDialogObj.show();
	setDefaultValuePanel('', '', '');


	/* setup drag and drop */
	var allKeywordsGridDropTarget = new Ext.dd.DropTarget(allKeywordsGrid.getView().scroller.dom, {
		ddGroup: 'allKeywordsDDGroup',
		notifyDrop : function(ddSource, e, data)
		{
			var records =  ddSource.dragData.selections;
			Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);

			allKeywordsGrid.store.add(records);
			allKeywordsGrid.store.sort('name', 'ASC');

			setDefaultValuePanel('', '', '');

			return true;
		}
	});

	var groupKeywordsGridDropTarget = new Ext.dd.DropTarget(groupKeywordsGrid.getView().scroller.dom, {
		ddGroup: 'groupKeywordsDDGroup',
		notifyDrop : function(ddSource, e, data)
		{
			var records =  ddSource.dragData.selections;
			Ext.each(records, function(record, index){ ddSource.grid.store.remove(record);}, ddSource.grid.store);

			groupKeywordsGrid.store.add(records);
			groupKeywordsGrid.store.sort('kwSortOrder', 'ASC');

			return true;
		}
	});

	/* select the products */
	var selectedProducts = [
		{/literal}
		{section name=index loop=$acceptedproducts}
			{if $smarty.section.index.last}
				"{$acceptedproducts[index]}"
			{else}
				"{$acceptedproducts[index]}",
			{/if}
		{/section}
		{literal}
	];

	if ((selectedProducts.length == 1) && (selectedProducts[0] == '**ALL**'))
	{
		Ext.getCmp('allProductsCheck').setValue(true);
		Ext.getCmp('productGrid').disable();
	}

	if (Ext.getCmp('keywordSection').getValue() == 'ORDER')
	{
		Ext.getCmp('productsTab').enable();
	}
	else
	{
		Ext.getCmp('productsTab').disable();
	}
}
{/literal}