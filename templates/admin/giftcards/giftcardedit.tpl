{literal}
var gDateFormat = "{/literal}{$dateformat}{literal}";
var gEarliestDate = "{/literal}{$earliestdate}{literal}";
var gLatestDate = "{/literal}{$latestdate}{literal}";
var gGiftCardID = {/literal}{$giftcardid}{literal};
companyGlobalValue = '';


var paramsGlobal = [];

function forceAlphaNumeric()
{
	var codeprefix = Ext.getCmp('codeprefix');
	codeprefix.setValue(codeprefix.getValue().toUpperCase().replace(/[^A-Z_0-9\-]+/g, ""));
}

var onCreateConfirmed = function(btn)
{
	if (btn == "yes")
	{
    	showResultWindow = true;
		var fp = Ext.getCmp('mainform'), form = fp.getForm();
		Ext.taopix.formPanelPost(fp, form, paramsGlobal, 'index.php?fsaction=AdminGiftCards.create', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
	}
};

var onImportConfirmed = function(btn)
{
	if (btn == "yes")
	{
		var startDateValue = formatPHPDate(Ext.getCmp('startdate').getRawValue(), gDateFormat, "yyyy-MM-dd HH:mm") + ":00";
		var endDateValue = formatPHPDate(Ext.getCmp('enddate').getRawValue(), gDateFormat, "yyyy-MM-dd HH:mm") + ":00";

		var mainForm = Ext.getCmp('mainform').getForm();

		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenName', value: Ext.getCmp('langPanel').convertTableToString()});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenGroupCode', value: Ext.getCmp('licenseKeyList').getValue()});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenCustomer', value: Ext.getCmp('customers').getValue() });
{/literal}{if $optionms==1}{literal}
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenCompanyCode', value: Ext.getCmp('companyCombo').getValue() });
{/literal}{else}{literal}
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenCompanyCode', value: '{/literal}{$companycode}{literal}' });
{/literal}{/if}{literal}
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddeStartDate', value: startDateValue});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenEndDate', value: endDateValue});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenActive', value: ((Ext.getCmp('isactive').checked) ? '1' : '0')});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenId', value: (Ext.taopix.gridSelection2IDList(Ext.getCmp('giftcardGrid')))});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'hiddenGiftcardValue', value: Ext.getCmp('giftcardvalue').getValue()});
		mainForm.el.createChild({tag:'input', type:'hidden', name:'csrf_token', value: Ext.taopix.getCSRFToken()});

		mainForm.submit(
		{
        	url: 'index.php?fsaction=AdminGiftCards.import',
            waitMsg: '{/literal}{#str_MessageSaving#}{literal}',
            success: function(form, action)
            {
            	gridDataStoreObj.reload();
            	var dialogGiftcards = Ext.getCmp('dialogGiftcards');

            	if (dialogGiftcards.isVisible())
				{
					dialogGiftcards.close();
				}

				Ext.getCmp('dialogGiftcardsResults').show();
				Ext.getCmp('giftcardsResultGrid').store.reload();
			},
            failure: function(form, action)
            {
            	Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: action.result.msg, buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
	        }
        });
	}
};


var addSaveHandler = function()
{
	if(Ext.getCmp('mainform').getForm().isValid())
	{
		Ext.MessageBox.minWidth = 250;

{/literal}{if $displayMode == TPX_CREATE_FORM_TYPE}{literal}

		var giftcardStartNumber = 0;

		if (!Ext.getCmp('israndom').checked)
		{
			giftcardStartNumber = Ext.getCmp('startnumber').getValue();

    		if (Ext.getCmp('codeprefix').getValue() == '')
    		{
        		Ext.getCmp('codeprefix').markInvalid("{/literal}{#str_ErrorNoGiftcardCodePrefix#}{literal}");
				return false;
    		}
		}

		var giftcardQty = Ext.getCmp('qty').getValue();
		if ((giftcardQty < 2) || (giftcardQty > 3000))
		{
    		Ext.getCmp('qty').markInvalid("{/literal}{#str_ErrorInvalidQuantity#}{literal}");
    		return false;
		}

{/literal}{/if}{literal}


		var startdate = Ext.getCmp('startdate');
		var enddate = Ext.getCmp('enddate');

		if (! parsePHPDate(startdate.getRawValue(), gDateFormat))
    	{
			startdate.markInvalid("{/literal}{#str_ErrorInvalidStartDate#}{literal}");
			return false;
    	}

    	if (! parsePHPDate(enddate.getRawValue(), gDateFormat))
    	{
			enddate.markInvalid("{/literal}{#str_ErrorInvalidEndDate#}{literal}");
			return false;
    	}

    	if (comparePHPDates(enddate.getRawValue(), startdate.getRawValue(), gDateFormat) != 1)
	    {
			enddate.markInvalid("{/literal}{#str_ErrorInvalidEndDate#}{literal}");
			return false;
	    }

    	/* make sure the dates are in range */
	    if (comparePHPDates(startdate.getRawValue(), gEarliestDate, gDateFormat) != 1)
	    {
			startdate.markInvalid("{/literal}{#str_ErrorInvalidStartDate#}{literal}");
			return false;
	    }

	    if (comparePHPDates(gLatestDate, startdate.getRawValue(), gDateFormat) != 1)
	    {
			startdate.markInvalid("{/literal}{#str_ErrorInvalidStartDate#}{literal}");
			return false;
	    }

	    if (comparePHPDates(enddate.getRawValue(), gEarliestDate, gDateFormat) != 1)
	    {
			enddate.markInvalid("{/literal}{#str_ErrorInvalidEndDate#}{literal}");
			return false;
	    }

	    if (comparePHPDates(gLatestDate, enddate.getRawValue(), gDateFormat) != 1)
	    {
			enddate.markInvalid("{/literal}{#str_ErrorInvalidEndDate#}{literal}");
			return false;
	    }

	    var giftcardvalue = Ext.getCmp('giftcardvalue');
	    if (giftcardvalue.getValue()<=0)
	    {
			giftcardvalue.markInvalid("{/literal}{#str_ErrorGiftcardValue#}{literal}");
			return false;
	    }

		var customers = Ext.getCmp('customers');
		if (!customers.valid)
		{
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorCustomer#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
			customers.markInvalid("{/literal}{#str_ErrorCustomer#}{literal}");
	    	return false;
		}

		if (!Ext.getCmp('langPanel').isValid())
		{
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoName#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
	    	return false;
		}

	    var parameter = [];

	    parameter['startdatevalue'] = formatPHPDate(startdate.getRawValue(), gDateFormat, "yyyy-MM-dd HH:mm") + ":00";
	    parameter['enddatevalue'] = formatPHPDate(enddate.getRawValue(), gDateFormat, "yyyy-MM-dd HH:mm") + ":00";
	    parameter['isactive'] = (Ext.getCmp('isactive').checked) ? '1' : '0';

		var fp = Ext.getCmp('mainform'), form = fp.getForm();

/* add or edit */
{/literal}{if $displayMode == TPX_ADD_EDIT_FORM_TYPE || $displayMode == TPX_READONLY_FORM_TYPE}{literal}
	    if (gGiftCardID > 0)
	    {
	    	parameter['giftcardid'] = gGiftCardID;
			Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction={/literal}{$destaction}{literal}.edit', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
	    }
	    else
	    {
			Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction={/literal}{$destaction}{literal}.add', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
	    }
{/literal}{/if}{literal}

/* create */
{/literal}{if $displayMode == TPX_CREATE_FORM_TYPE}{literal}

		parameter['israndom'] = (Ext.getCmp('israndom').checked) ? '1' : '0';
		parameter['startnumber'] = giftcardStartNumber;

		paramsGlobal = parameter;
		var message = "{/literal}{#str_CreateGiftcardConfirmation#}{literal}".replace("^0", giftcardQty);
		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", message, onCreateConfirmed);

{/literal}{/if}{literal}


{/literal}{if $displayMode == TPX_IMPORT_FORM_TYPE}{literal}

		paramsGlobal = parameter;
		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_ImportGiftcardConfirmation#}{literal}", onImportConfirmed);

{/literal}{/if}{literal}

	}
	else
    {
		return false;
	}
};

function initialize(pParams)
{

	giftcardResultGridDataStoreObj = new Ext.data.Store(
	{
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminGiftCards.listGiftCards&ref=' + sessionId + '&resultgiftcards=1' }),
		method:'POST',
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0},
			Ext.data.Record.create([
				{name: 'recordid', mapping: 0},
				{name: 'companycode', mapping: 1},
				{name: 'code', mapping: 2},
				{name: 'name', mapping: 3},
				{name: 'startdate', mapping: 4},
				{name: 'enddate', mapping: 5},
				{name: 'groupcode', mapping: 6},
				{name: 'userid', mapping: 7},
				{name: 'username', mapping: 8},
				{name: 'redeemuserid', mapping: 9},
				{name: 'redeemusername', mapping: 10},
				{name: 'giftcardvalue', mapping: 11},
				{name: 'isactive', mapping: 12}
			])
		),
		sortInfo:{field: 'recordid', direction: "ASC"},
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});

	onResultCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
			gridDataStoreObj.reload();

			if (Ext.getCmp('dialogGiftcardsResults').isVisible())
			{
				Ext.getCmp('dialogGiftcardsResults').close();
			}
		}
	};

	function onResultDelete()
	{
		var onDeleteConfirmed = function(btn)
		{
			if (btn == "yes")
			{
				Ext.taopix.formPost(dialogGiftcardsResultsObj, [], 'index.php?fsaction=AdminGiftCards.deleteNew&ref='+sessionId, "{/literal}{#str_MessageDeleting#}{literal}", onResultCallback);
			}
		};
		Ext.MessageBox.minWidth = 350;
		Ext.MessageBox.confirm("{/literal}{#str_LabelConfirmation#}{literal}", "{/literal}{#str_DeleteNewGiftcardConfirmation#}{literal}", onDeleteConfirmed);
	};

	function onResultExport()
	{
		location.replace('index.php?fsaction=AdminGiftCards.export&ref='+sessionId+'&useCached=1');
	};

	var columnRendererResult = function(value, p, record, rowIndex, colIndex, store)
	{
		var className = '';

		if (record.data.isActive == 0)
		{
			if (colIndex == 8)
				value = "{/literal}{#str_LabelInactive#}{literal}";

			className = 'class = "inactive"'
		}
		else
		{
			if (colIndex == 8)
				value = "{/literal}{#str_LabelActive#}{literal}";
		}
		return '<span '+className+'>'+value+'</span>';
	};

	dialogGiftcardsResultsObj = new Ext.Window(
	{
		id: 'dialogGiftcardsResults',
		title: "{/literal}{#str_TitleGiftcardCreationResult#}{literal}",
		closable:true,
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		width: 680,
		height: 430,
		ctCls: 'grid',
		items:
		[
			{
				xtype: 'grid',
	   			id: 'giftcardsResultGrid',
	   			store: giftcardResultGridDataStoreObj,
	    		cm: new Ext.grid.ColumnModel(
	    		{
					defaults:
					{
						sortable: true,
						resizable: true
					},
					columns:
					[
					    { id:'companycode', header: "{/literal}{#str_LabelCompany#}{literal}", dataIndex: 'companycode', hidden:true },
					    { header: "{/literal}{#str_LabelCode#}{literal}", dataIndex: 'code', width:150, renderer: columnRendererResult },
				        { header: "{/literal}{#str_LabelName#}{literal}", dataIndex: 'name', width:230, renderer: columnRendererResult, sortable: false, menuDisabled: true },
				        { header: "{/literal}{#str_LabelGiftcardValue#}{literal}", dataIndex: 'giftcardvalue', width:100, renderer: columnRendererResult  },
				        { header: "{/literal}{#str_LabelStartDate#}{literal}", dataIndex: 'startdate', width:100, renderer: columnRendererResult  },
				        { header: "{/literal}{#str_LabelEndDate#}{literal}", dataIndex: 'enddate', width:100, renderer: columnRendererResult },
				        { header: "{/literal}{#str_LabelLicenseKey#}{literal}", dataIndex: 'groupcode', width:150, renderer: columnRendererResult },
				        { header: "{/literal}{#str_LabelCustomer#}{literal}", dataIndex: 'username', width:120, renderer: columnRendererResult },
				        { header: "{/literal}{#str_LabelActive#}{literal}", dataIndex: 'isactive', renderer: columnRendererResult, align: 'right', width:80}
	    			]
				}),
	    		stripeRows: true,
	    		stateful: true,
	    		enableColLock:false,
				draggable:false,
				enableColumnHide:false,
				enableColumnMove:false,
				trackMouseOver:false,
				columnLines:true,
	    		stateId: 'giftcardsResultGrid',
	    		tbar: [
	            	{ref: '../deleteButton', text: "{/literal}{#str_ButtonDelete#}{literal}", iconCls: 'silk-delete', handler: onResultDelete }, '-',
	            	{ref: '../exportButton',	text: "{/literal}{#str_ButtonExport#}{literal}",	iconCls: 'silk-page-white-put',	handler: onResultExport }
	    		]
			}
		]
	});

	var panelWidth = 640;
	var windowWidth = 670;

	var topPanel = new Ext.Panel(
	{
		id: 'topPanel',
		layout: 'form',
		style:'background: #c9d8ed; padding: 3px 0; border:1px solid #8ca9cf',
		plain:true,
		bodyBorder: false,
		border: false,
		defaults:
		{
				xtype: 'textfield',
				labelWidth: 120,
				width: 270
		},
		labelWidth: 125,
		width: panelWidth,
		bodyStyle:'padding:5px 5px 0; border-top: 0px',
		items:
		[
			/* add or edit */
{/literal}{if $displayMode == TPX_ADD_EDIT_FORM_TYPE || $displayMode == TPX_READONLY_FORM_TYPE}{literal}
			{
				id: 'code',
				name: 'code',
				fieldLabel: "{/literal}{#str_LabelCode#}{literal}",
				value: "{/literal}{$code}{literal}",
				validateOnBlur: true,
				post: true,
				allowBlank: false,
				maskRe: /^\w+$/,
				maxLength: 50,
			{/literal}{if $giftcardid==0}{literal}
				readOnly: false,
				style: {textTransform: "uppercase"}
			{/literal}{else}{literal}
				readOnly: true,
				style: 'background:#c9d8ed; textTransform: uppercase'
			{/literal}{/if}{literal}
			}
{/literal}{/if}{literal}

			/* create */
{/literal}{if $displayMode == TPX_CREATE_FORM_TYPE}{literal}
			{
				xtype: 'container',
				layout: 'column',
				width: panelWidth,
				items:
				[
					{
						width: 285,
						xtype: 'panel',
						layout: 'form',
						items:
						[
							{
								xtype: 'numberfield',
								id: 'qty',
								name: 'qty',
								fieldLabel: "{/literal}{#str_LabelQuantity#}{literal}",
								value: "2",
								validateOnBlur: true,
								post: true,
								allowBlank: false,
								width: 100,
								allowNegative: false,
								allowDecimals: false,
								minValue: 2,
								maxValue: 3000
							}
						]
					},
					{
						width: 345,
						xtype: 'panel',
						layout: 'form',
						items:
						[
							{
								xtype: 'textfield',
								id: 'codeprefix',
								name: 'codeprefix',
								fieldLabel: "{/literal}{#str_LabelCodePrefix#}{literal}",
								value: "",
								validateOnBlur: true,
								post: true,
								width: 200,
								listeners:
								{
									blur:
									{
										fn: forceAlphaNumeric
									}
								}
							}
						]
					}
				]
			},
			{
				xtype: 'container',
				layout: 'column',
				width: panelWidth,
				items:
				[
					{
						width: 285,
						xtype: 'panel',
						layout: 'form',
						items:
						[
							{
								xtype: 'checkbox',
								id: 'israndom',
								name: 'israndom',
								hideLabel: true,
								boxLabel: "{/literal}{#str_LabelGenerateRandomCode#}{literal}",
								post: true,
								checked: true,
								listeners: {
									'check': function(combo, checked)
									{
										if (checked)
										{
											Ext.getCmp('startnumber').disable();
										}
										else
										{
											Ext.getCmp('startnumber').enable();
										}
									}
								}
							}
						]
					},
					{
						width: 345,
						xtype: 'panel',
						layout: 'form',
						items: [
							{
								xtype: 'numberfield',
								id: 'startnumber',
								name: 'startnumber',
								fieldLabel: "{/literal}{#str_LabelStartNumber#}{literal}",
								value: "1",
								validateOnBlur: true,
								post: true,
								allowBlank: false,
								width: 200,
								allowNegative: false,
								allowDecimals: false,
								disabled: true
							}
						]
					}
				]
			}
{/literal}{/if}{literal}

			/* import vouchers */
{/literal}{if $displayMode == TPX_IMPORT_FORM_TYPE}{literal}
			{
            	xtype: 'fileuploadfield',
            	id: 'importcodes',
            	fieldLabel: "{/literal}{#str_ButtonImportCodes#}{literal}",
            	name: 'importcodes',
            	buttonText: '',
            	buttonCfg: {iconCls: 'silk-upload-icon'},
            	height: 20,
            	allowBlank: false,
            	validateOnBlur: true
        	}
{/literal}{/if}{literal}
		]
	});

	var deleteImg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png';
	var addimg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/add.png';

	var prevLicenseID = '';

	var mainPanel =
	{
		xtype: 'panel',
		id: 'mainpanel',
		deferredRender: false,
		width: panelWidth,
		height: 350,
		shadow: true,
		plain:true,
		bodyBorder: false,
		border: false,
		style:'margin-top:6px; ',
		bodyStyle:'border-right: 1px solid #96bde7; border-left: 1px solid #96bde7; ',
		defaults:{frame: false, autoScroll: true, hideMode:'offsets', layout: 'form', labelWidth: 135, bodyStyle:'padding:5px 10px 0 10px; border-top: 0px; background-color: #eaf0f8;'},
		items:
		[
			{
				title: "{/literal}{#str_LabelDetails#}{literal}",
				defaults:{xtype: 'textfield', width: 150},
				items:
				[
					{/literal}{if $optionms==1}{literal}
					{
						xtype: 'taopixCompanyCombo',
						id: 'companyCombo',
						name: 'companyCombo',
						width:450,
						fieldLabel: "{/literal}{#str_LabelCompany#}{literal}",
						hideLabel:false,
						allowBlank:false,
						defvalue: '{/literal}{$companycode}{literal}',
{/literal}{if $displayMode == TPX_READONLY_FORM_TYPE}{literal}
						disabled: true,
{/literal}{/if}{literal}
						{/literal}{if $companyadmin==1}{literal}
						disabled: true,
						{/literal}{/if}{literal}
						options:
						{
							ref: '{/literal}{$ref}{literal}',
							storeId: 'companyStore',

						{/literal}{if $companyadmin==1}{literal}
							includeGlobal: '0',
						{/literal}{else}{literal}
							includeGlobal: '1',
						{/literal}{/if}{literal}

							includeShowAll: '0',
							onchange: function()
							{
								var companyCode = this.getValue();
								var customers = Ext.getCmp('customers');
								var licenseKeyList = Ext.getCmp('licenseKeyList');

								prevLicenseID = '';

								licenseKeyList.setValue('{/literal}{$all}{literal}');
								customers.setValue(0);
								customers.disable();
								var licenseKeyListStore = licenseKeyList.store;

								if(companyCode=='GLOBAL')
									companyCode ='';

								licenseKeyList.enable();
								licenseKeyListStore.filterBy(function(record){return ((record.get('companycode')==companyCode) || (record.get('companycode')=='ALL'));})
							}
						}
					},
					{/literal}{/if}{literal}
					{
						xtype:'combo',
						id: 'licenseKeyList',
						name: 'licenseKeyList',
						mode: 'local',
						editable: false,
						forceSelection: true,
						width: 450,
{/literal}{if $displayMode == TPX_READONLY_FORM_TYPE}{literal}
						disabled: true,
{/literal}{/if}{literal}
						selectOnFocus: true,
						triggerAction: 'all',
						lastQuery: '',
						fieldLabel: "{/literal}{#str_LabelLicenseKey#}{literal}",
						displayField: 'name',
						store: new Ext.data.ArrayStore(
						{
							id: 0,
							fields: ['id', 'name', 'companycode'],
							data: {/literal}{$groupList}{literal},
							listeners:
							{
								load: function(store, records, options)
								{
									var companyCode = '{/literal}{$companycode}{literal}';

									if(companyCode=='GLOBAL')
										companyCode ='';

									store.filterBy(function(record){return ((record.get('companycode')==companyCode) || (record.get('companycode')=='ALL'));})

								}
							}
						}),
						listeners:
						{
							select: function(combo, record)
							{
								var customers = Ext.getCmp('customers');
								var customersStore = customers.store;

								if (prevLicenseID!=record.get('id'))
								{
									prevLicenseID = record.get('id');

									if(record.get('id')!='ALL')
									{
										customers.enable();
										customersStore.baseParams.group = record.get('id');
										customersStore.load({params: { group: record.get('id')}});
									}
									else
									{
										customers.disable();
									}

									customers.setValue(0);
								}

							}
						},
						valueField: 'id',
						displayField: 'name',
						useID: true,
						value: "{/literal}{$groupcode}{literal}",
						post: true
					},
					{
						xtype: 'combo',
						id: 'customers',
						name: 'customers',
						store: new Ext.data.Store(
			            {
			                url: '?fsaction=Admin.searchCustomers&ref=' + sessionId,
			                baseParams:
			                {
			                	group:  '{/literal}{$groupcode}{literal}',
			               		limit: 10,
			                	start: 0,
								csrf_token: Ext.taopix.getCSRFToken()
			                },
			                reader: new Ext.data.JsonReader(
			                {
			                    root: 'users',
			                    totalProperty: 'totalcount',
			                    id: 'id'
			                },
			                [
			                    {name: 'id'},
                                {name: 'login'},
                                {name: 'firstname'},
			                    {name: 'lastname'},
			                    {name: 'emailaddress'},
			                    {name: 'displayname',
			                        convert : function(value, record)
			                        {
			                            if (record.id==0)
			                            {
			                                return record.firstname;
			                            }
			                            else
			                            {
			                                return record.firstname + " " + record.lastname + " (" + record.login + ")";
			                            }
			                        }
			                    },
                                {name: 'address1'},
                                {name: 'postcode'},
                                {name: 'telephonenumber'}
			                ]),
			                listeners:
			                {
			                    load: function(store, records, index)
			                    {
			                        if (store.lastOptions.params.id!=undefined)
			                        {
			                        	var record = store.getById({/literal}{$userid}{literal});
			                            Ext.getCmp('customers').setValue(record.get('id'));
			                        }
			                    }
			                }
			            }),
			            displayField:'displayname',
			            valueField: 'id',
			            typeAhead: false,
			            loadingText: "{/literal}{#str_LabelSearching#}{literal}",
			            pageSize:10,
			            hideTrigger:false,
			            selectOnFocus: true,
						width:450,
{/literal}{if $displayMode == TPX_READONLY_FORM_TYPE}{literal}
						disabled: true,
{/literal}{else}{literal}
					{/literal}{if $userid>0}{literal}
						disabled: false,
					{/literal}{else}{literal}
						disabled: true,
					{/literal}{/if}{literal}
{/literal}{/if}{literal}
                        post: true,
                        useID: true,
			            valid: true,
                        fieldLabel: "{/literal}{#str_LabelCustomer#}{literal}",
                        triggerAction: 'all',
                        tpl: new Ext.XTemplate(
                            '<tpl for=".">',
                                '<div class="search-item" style="padding: 2px 20px 2px 4px; width:422px; display:table; border-bottom: thin solid #999999;">',
                                    '<div style="display:block; float:left"><div><h3>{firstname} {lastname}</h3></div><div>{emailaddress}</div><div>{telephonenumber}</div></div>',
                                    '<div style="display:block; float:right; text-align:right;"><div>{login}</div><div>{address1}</div><div>{postcode}</div></div>',
                                '</div>',
                            '</tpl>'
                        ),
			            itemSelector: 'div.search-item',
			            listeners:
			            {
			            	select: function()
			            	{
			            		this.valid = true;
			            	},
			            	blur: function(combo)
			            	{
			            		combo.valid = true;

			            		if (combo.value == '')
								{
			            			combo.setValue('{/literal}{$all}{literal}');
			            		}
								else if ((combo.value != '{/literal}{$all}{literal}') && (combo.value!=0))
			            		{
									Ext.Ajax.request(
									{
										url: '?fsaction=Admin.searchCustomers&ref=' + sessionId,
									    params:
									    {
									    	id: combo.value,
									    	group: combo.store.baseParams.group,
											csrf_token: Ext.taopix.getCSRFToken()
									    },
									    success: function(response)
									    {
									       var jsonObj = eval('(' + response.responseText + ')');

									       if (jsonObj.totalcount==0)
									       {
									       		combo.markInvalid("{/literal}{#str_ErrorCustomer#}{literal}");
									       		combo.valid = false;
									       }

									    }
									});
			            		}

			            	}
			            }
                    },
					new Ext.form.DateField({ fieldLabel: "{/literal}{#str_LabelStartDate#}{literal}", {/literal}{if $displayMode == TPX_READONLY_FORM_TYPE}{literal}disabled: true,{/literal}{/if}{literal} name: 'startdate', id: 'startdate', validateOnBlur:true, endDateField: 'enddate', format: gDateFormat, value: "{/literal}{$startdate}{literal}"}),
					new Ext.form.DateField({ fieldLabel: "{/literal}{#str_LabelEndDate#}{literal}",   {/literal}{if $displayMode == TPX_READONLY_FORM_TYPE}{literal}disabled: true,{/literal}{/if}{literal} name: 'enddate',   id: 'enddate', validateOnBlur:true, startDateField: 'startdate', format: gDateFormat, value: "{/literal}{$enddate}{literal}"}),
					{
						xtype: 'container',
						layout: 'column',
						width: 500,
						items:
						[
							{
								width: 280,
								xtype: 'panel',
								layout: 'form',
								items:
								[
									{
										xtype:"numberfield",
										allowNegative: false,
										allowDecimals: true,
										decimalPrecision: 2,
{/literal}{if $displayMode == TPX_READONLY_FORM_TYPE}{literal}
										disabled: true,
{/literal}{/if}{literal}
										id: 'giftcardvalue',
										name: 'giftcardvalue',
										fieldLabel: "{/literal}{#str_LabelGiftcardValue#}{literal}",
										value: "{/literal}{$giftcardvalue}{literal}",
										post: true,
										width: 100,
										validateOnBlur: true
									}
								]
							}
						]
					},
					{
						xtype: 'panel',
	    				width: 465,
	    				fieldLabel: "{/literal}{#str_LabelName#}{literal}",
	    				items:
						[
							{
								id: 'langPanel',
								xtype: 'taopixLangPanel',
								name: 'name',
								height:153,
								width: 465,
								post: true,
{/literal}{if $displayMode == TPX_READONLY_FORM_TYPE}{literal}
								disabled: true,
{/literal}{/if}{literal}
								style: 'border:1px solid #b4b8c8',
								data:
								{
									langList: {/literal}{$langList}{literal},
									dataList: {/literal}{$dataList}{literal}
								},
								settings:
								{
									headers:     {langLabel: "{/literal}{#str_LabelLanguageName#}{literal}",  textLabel: "{/literal}{#str_LabelName#}{literal}", deletePic: deleteImg, addPic: addimg},
									defaultText: {langBlank: "{/literal}{#str_LabelSelectLanguage#}{literal}",  textBlank: "{/literal}{#str_ExtJsTypeValue#}{literal}", defaultValue: "{/literal}{$defaultlanguagecode}{literal}"},
									columnWidth: {langCol: 200, textCol: 210, delCol: 35},
									fieldWidth:  {langField: 185, textField: 178},
									errorMsg:    {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"}
								}
							}
						]
					}
				]
			}
		]
	};


	var dialogFormPanelObj = new Ext.taopix.FormPanel({
		id: 'mainform',
		header: false,
		frame: true,
		width: windowWidth,
		layout: 'form',
		fileUpload: true,
		defaultType: 'textfield',
		bodyStyle:'border-bottom: 1px solid #96bde7;',
		autoHeight: true,
		items: [ topPanel, mainPanel ],
		baseParams:
		{
			ref: '{/literal}{$ref}{literal}',
			csrf_token: Ext.taopix.getCSRFToken()
		}
	});


	var gDialogObjGiftcards = new Ext.Window(
	{
		id: 'dialogGiftcards',
		closable: false,
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		width: windowWidth,
		autoHeight: true,
		items: dialogFormPanelObj,
		listeners:
		{
			'close':
			{
				fn: function()
				{
					gitcardEditWindowExists = false;
				}
			}
		},
		title: "{/literal}{$title}{literal}",
		cls: 'left-right-buttons',
		buttons:
		[
			new Ext.form.Checkbox({
				id: 'isactive',
				name: 'isactive',
				boxLabel: "{/literal}{#str_LabelActive#}{literal}",
				post: true,
				cls: 'x-btn-left',
{/literal}{if $displayMode == TPX_READONLY_FORM_TYPE}{literal}
				disabled: true,
{/literal}{/if}{literal}
				ctCls: 'width_100'
			}),
			{
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(btn, ev){ gDialogObjGiftcards.close(); },
				cls: 'x-btn-right'
			},
			{
				text: "{/literal}{$actionbutton}{literal}",
				id: 'updateButton',
{/literal}{if $displayMode == TPX_READONLY_FORM_TYPE}{literal}
				disabled: true,
{/literal}{/if}{literal}
				handler: addSaveHandler,
				cls: 'x-btn-right'
			}
		]
	});

	gDialogObjGiftcards.show();

	var customers = Ext.getCmp('customers');

{/literal}{if $displayMode != TPX_READONLY_FORM_TYPE}{literal}
    if ('{/literal}{$groupcode}{literal}'!='ALL')
        customers.enable();
{/literal}{/if}{literal}

	Ext.getCmp('isactive').setValue("{/literal}{$activechecked}{literal}" == 'checked' ? true : false);

	customers.store.load({params: { id: {/literal}{$userid}{literal}}});

};

{/literal}