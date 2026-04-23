{literal}

function initialize(pParams)
{	
 	{/literal}
	var gBrandingAndLicenseCode = '{$brandinglicensekeycode}';
	var gBrandingAndLicenseCodeSelectedDisplay = '{$brandinglicensekeycodedisplay}';
	var gConnectorID = '{$connectorid}';
	var gConnectorChildID = '{$connectorchildid}';
	var gConnectorURL = '{$connectorurl}';
	var gConnectorPrimaryDomain = '{$connectorprimarydomain}';
	var gOnlineConnectorKey = '{$connectorkey}';
	var gOnlineConnectorSecret = '{$connectorsecret}';
	var gOnlineConnectorInstallURL = '{$connectorinstallurl}';
	var gConnectorShopifyURL = '.myshopify.com';
	var gPricesIncludeTax = ("{$pricesincludetax}" == '1') ? true : false;
	var desktopFieldsArray = [
		'desktopconnectorkey',
		'desktopconnectorsecret',
		'desktopconnectoraccesstoken'
	];
	var gridObj = Ext.getCmp('connectorsGrid');
	var connectorRecords = gridObj.store.reader.arrayData;
	var windowTitle = "{#str_SectionTitleAddConnector#}";
	if (gConnectorID > 0)
	{
		windowTitle = "{#str_SectionTitleEditConnector#}";
	}

	{literal}

	var brandValidation = function(v,obj,text) 
	{
		var brandAndKeyVal = document.getElementById(obj.hiddenId).value;
		var brandAndKeyArray = brandAndKeyVal.split('@@');
		var brandIdVal = brandAndKeyArray[0];

		var i;

		//no brand selected
		if (brandIdVal == 'N') 
		{
			return false;
		}

		//brand already has connector
		for (i=1; i < connectorRecords.length; i++) 
		{
			var thisBrandID = connectorRecords[i][1];

			if (thisBrandID == '')
			{
				thisBrandID = '{/literal}{#str_LabelDefault#}{literal}';
			}

			if (thisBrandID == brandIdVal)
			{
				return text;
			}
		}

		return true;
	}

	var validateUrl = function(obj)
	{
		var url;
		url = obj.getValue();
		
		if (obj.id == 'connectorurl')
		{
			url += gConnectorShopifyURL;
		} 

		if (url != '')
		{
			var domainValid = (Ext.form.VTypes.url(url));
			var regexp = /^((([hH][tT][tT][pP][sS]?|[fF][tT][pP])\:\/\/)?([\w\.\-]+(\:[\w\.\&%\$\-]+)*@)?((([^\s\(\)\<\>\\\"\.\[\]\,@;:]+)(\.[^\s\(\)\<\>\\\"\.\[\]\,@;:]+)*(\.[a-zA-Z]{2,4}))|((([01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}([01]?\d{1,2}|2[0-4]\d|25[0-5])))(\b\:(6553[0-5]|655[0-2]\d|65[0-4]\d{2}|6[0-4]\d{3}|[1-5]\d{4}|[1-9]\d{0,3}|0)\b)?((\/[^\/][\w\.\,\?\'\\\/\+&%\$#\=~_\-@]*)*[^\.\,\?\"\'\(\)\[\]!;<>{}\s\x7F-\xFF])?)$/;
			var ipValid = regexp.test(url);
			if (domainValid || ipValid) return true; else return false;
		} else 
		{
			;return true;
		}

		obj.clearInvalid();
		return true;
	};

	function editSaveHandler()
	{	
		var parameter = [];
    	parameter['connectorname'] = 'SHOPIFY';
		parameter['pricesincludetax'] = (Ext.getCmp('pricesincludetax').checked) ? 1 : 0;

		var fp = Ext.getCmp('mainform'), form = fp.getForm();

		if (gConnectorID > 0)
		{
			parameter['id'] = gConnectorID;
      		parameter['childid'] = gConnectorChildID;

			Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=AdminConnectors.edit', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
		}
		else
		{
			Ext.taopix.formPanelPost(fp, form, parameter, 'index.php?fsaction=AdminConnectors.add', "{/literal}{#str_MessageSaving#}{literal}", onCallback);
		}
	}

  function onCallback(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.success === false)
			{
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
  
			gridDataStoreObj.reload();
			if ((Ext.getCmp('dialog')) && (Ext.getCmp('dialog').isVisible()))
			{
				Ext.getCmp('dialog').close();
			}
		}
	};

	var dialogFormPanelObj = new Ext.taopix.FormPanel({
		id: 'mainform',
		header: false,
		frame: true,
		width: 685,
		height: 333,
		layout: 'form',
		defaultType: 'textfield',
		padding: 15,
		items: [
				new Ext.form.ComboBox({ 
					id: 'webbrandinglist', 
					name: 'webbrandinglist', 
					hiddenName:'webbrandinglist_hn', 
					hiddenId:'webbrandinglist_hi', 
					mode: 'local', 
					editable: false, 
					forceSelection: true, 
					fieldLabel:'{/literal}{#str_SelectLicenseKey#}{literal}',
					store: new Ext.data.ArrayStore({ 
						id: 0, 
						fields: ['webbrandinglist_id', 'webbrandinglist_name', ''],	
						data: {/literal}{$webbrandinglist}{literal} 
					}),
					valueField: 'webbrandinglist_id', 
					displayField: 'webbrandinglist_name', 
					useID: true, 
					post: true, 
					width:425,
					allowBlank: false, 
					triggerAction: 'all',
					validator: function(v){ return brandValidation(v, this, "{/literal}{#str_LabelConnectorExistsOnBrandValidationText#}{literal}"); }
				}),
				{
					xtype: 'hidden',
					id: 'brandid',
					name: 'brandid',
					value: 0,
					post: true,
					width: 424
				},
				{
					xtype: 'container', layout: 'column', width: 650,
					id: 'urlcontainer',
					items: [
						{
							width: 375,
							xtype: 'panel',
							layout: 'form',
							defaults: {xtype: 'textfield', width: 270},
							items: [
								{
									xtype: 'textfield',
									id: 'connectorurl',
									name: 'connectorurl',
									fieldLabel: "{/literal}{#str_LabelConnectorURL#}{literal}",
									value: gConnectorURL,
									validateOnBlur: true,
									post: true,
									validator: function(v){ return validateUrl(this); }
								}
							]
						},
						{
							width: 225,
							height: 25,
							xtype: 'panel',
							layout: 'form',
							style: '{ padding-top:5px;}',
							defaults: {xtype: 'container', width: 170},
							items:
							[
								{
									id: 'myshopify',
									name: 'myshopify',
									html: ".myshopify.com",
									cls: "",
									style: ""
								},
              ]
            },
			{
				width: 375,
				xtype: 'panel',
				layout: 'form',
				defaults: {xtype: 'textfield', width: 270},
				items: [
					{
						xtype: 'textfield',
						id: 'connectorprimarydomain',
						name: 'connectorprimarydomain',
						fieldLabel: "{/literal}{#str_LabelConnectorPrimaryDomain#}{literal}",
						value: gConnectorPrimaryDomain,
						validateOnBlur: true,
						post: true,
						validator: function(v){ return validateUrl(this); }
					}
				]
			},
            {
							width: 424,
							xtype: 'panel',
							layout: 'form',
							style: '{ padding-top:5px;}',
						  	defaults: {xtype: 'textfield', width: 270},
							items:
							[
                {
                  xtype: 'textfield',
                  id: 'connectorkey',
                  name: 'connectorkey',
                  fieldLabel: "{/literal}{#str_LabelConnectorKey#}{literal}",
                  value: gOnlineConnectorKey,
                  validationEvent: false,
                  post: true,
                  width: 270
                }
              ]
            },
            {
							width: 424,
							xtype: 'panel',
							layout: 'form',
							style: '{ padding-top:5px;}',
						  	defaults: {xtype: 'textfield', width: 270},
							items:
							[
                {
                  xtype: 'textfield',
                  id: 'connectorsecret',
                  name: 'connectorsecret',
				  inputType: 'password',
                  fieldLabel: "{/literal}{#str_LabelConnectorSecret#}{literal}",
                  value: gOnlineConnectorSecret,
                  validateOnBlur: true,
                  post: true,
                  width: 270
                }
              ]
            },
            {
							width: 424,
							xtype: 'panel',
							layout: 'form',
							style: '{ padding-top:5px;}',
							defaults: {xtype: 'textfield', width: 270},
							items:
							[
                {
                  xtype: 'textfield',
                  id: 'connectorinstallurl',
                  name: 'connectorinstallurl',
                  fieldLabel: "{/literal}{#str_LabelConnectorInstallURL#}{literal}",
                  value: gOnlineConnectorInstallURL,
                  validateOnBlur: true,
                  post: true,
                  width: 270,
                  validator: function(v){ return validateUrl(this); }
                }
              ]
            },
            {
							width: 424,
							xtype: 'panel',
							layout: 'form',
							style: '{ padding-top:5px;}',
							defaults: {xtype: 'textfield', width: 170},
							items:
							[
                {
                  xtype: 'checkbox',
                  id: 'pricesincludetax',
                  name: 'pricesincludetax',
                  fieldLabel: "{/literal}{#str_LabelPricingIncludesTax#}{literal}",
                  checked: gPricesIncludeTax,
                  validateOnBlur: true,
                  post: true,
                  width: 270
                }
							]
						}
					]
				}
		],
		baseParams:	{ ref: "{/literal}{$ref}{literal}" }
	});

	var warningPanelItems = [];
	var warningTemplate = new Ext.XTemplate('<tpl for="."><div class="warning-message">{error}</div></tpl>');

	warningPanelItems.push({
		xtype: 'panel',
		style: { height: "27px" },
		flex: true,
		ctCls: "warning-bar",
		height: 27,
		tpl: warningTemplate,
		data: [
			{error: "{/literal}{#str_ConnectorWarning#}{literal}"}
		]
	});

	gWarningPanel = new Ext.Panel({
		id: 'warningpanel',
		height: 29,
		items: warningPanelItems
	});

	var dialogueHeight = 375;
	{/literal}{if $connectorid < 1}{literal}
	dialogueHeight += 25;
	{/literal}{/if}{literal}

	gDialogObj = new Ext.Window({
		id: 'dialog',
		closable:true,
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		title: windowTitle,
		width: 700,
		height: dialogueHeight,
		items: [
				{/literal}{if $connectorid < 1}{literal}gWarningPanel,{/literal}{/if}{literal}
				dialogFormPanelObj
			],
		listeners: {
			'close': {
				fn: function(){
					connectorsEditWindowExists = false;
				}
			}
		},
		cls: 'right-buttons',
		buttons:
		[
			{
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(btn, ev){ gDialogObj.close(); },
				cls: 'x-btn-right'
			},
			{	
				id: 'updateButton',
				{/literal}{if $connectorid < 1}{literal}
					text: "{/literal}{#str_ButtonAdd#}{literal}",
				{/literal}{else}{literal}
					text: "{/literal}{#str_ButtonUpdate#}{literal}",
				{/literal}{/if}{literal}
				handler: editSaveHandler,
				cls: 'x-btn-right'
			}
		]
	});

	/* Override the events to make grid fire editcompete even if value wasnt modified */
	Ext.grid.EditorGridPanel.prototype.onEditComplete = function(ed, value, startValue)
	{
		this.editing = false;
		this.lastActiveEditor = this.activeEditor;
		this.activeEditor = null;

		var r = ed.record,
		field = this.colModel.getDataIndex(ed.col);
		value = this.postEditValue(value, startValue, r, field);
		if(this.forceValidation === true || String(value) !== String(startValue)){
			var e = {
				grid: this,
				record: r,
				field: field,
				originalValue: startValue,
				value: value,
				row: ed.row,
				column: ed.col,
				cancel:false
			};
			if(this.fireEvent("validateedit", e) !== false && !e.cancel){
				r.set(field, e.value);
				delete e.cancel;
				this.fireEvent("afteredit", e);
			}
		}
		this.view.focusCell(ed.row, ed.col);
	};

	gDialogObj.show();
	dialogFormPanelObj.getForm().clearInvalid();

	if (gConnectorID == 0) 
	{
		Ext.getCmp('webbrandinglist').setValue('{/literal}{#str_SelectLicenseKey#}{literal}');
		document.getElementById(Ext.getCmp('webbrandinglist').hiddenId).value = 'N'
	}
	else 
	{
		document.getElementById(Ext.getCmp('webbrandinglist').hiddenId).value = gBrandingAndLicenseCode;
		Ext.getCmp('webbrandinglist').setValue(gBrandingAndLicenseCodeSelectedDisplay);
	}

  function enableDisableDesktopFields(pComponent)
  {
    var desktopFieldsArrayLength = desktopFieldsArray.length;

    for (var i = 0; i < desktopFieldsArrayLength; i++) 
		{
			var component =  Ext.getCmp(desktopFieldsArray[i]);

			if (pComponent.checked)
			{
				component.enable();
			}
			else
			{
				component.disable();
				component.setValue('');
			}
		}
  }

	if (gConnectorID > 0)
	{
		Ext.getCmp('webbrandinglist').disable();
	}

	dialogFormPanelObj.getForm().clearInvalid();
}

{/literal}