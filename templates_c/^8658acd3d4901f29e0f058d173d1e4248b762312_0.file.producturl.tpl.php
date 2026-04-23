<?php
/* Smarty version 4.5.3, created on 2026-03-06 03:12:25
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\taopixonlineproducturl\producturl.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa4619bc7ea9_28726111',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8658acd3d4901f29e0f058d173d1e4248b762312' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\taopixonlineproducturl\\producturl.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aa4619bc7ea9_28726111 (Smarty_Internal_Template $_smarty_tpl) {
?>

function initialize(pParams)
{
	var gDecryptURLWindowExists = false;

	function forceAlphaNumeric(pObj)
	{
		var string = pObj.getValue();

    	string = string.toUpperCase();

    	string = string.replace(/[^a-zA-Z_0-9\-]+/g, "");

    	pObj.setValue(string);
	}

	var validateUrl = function(obj)
	{
		if ((obj.getValue() != 'http://') && (obj.getValue() != 'https://')&& (obj.getValue() != ''))
		{
			var domainValid = (Ext.form.VTypes.url(obj.getValue()));
			var regexp = /^((([hH][tT][tT][pP][sS]?|[fF][tT][pP])\:\/\/)?([\w\.\-]+(\:[\w\.\&%\$\-]+)*@)?((([^\s\(\)\<\>\\\"\.\[\]\,@;:]+)(\.[^\s\(\)\<\>\\\"\.\[\]\,@;:]+)*(\.[a-zA-Z]{2,4}))|((([01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}([01]?\d{1,2}|2[0-4]\d|25[0-5])))(\b\:(6553[0-5]|655[0-2]\d|65[0-4]\d{2}|6[0-4]\d{3}|[1-5]\d{4}|[1-9]\d{0,3}|0)\b)?((\/[^\/][\w\.\,\?\'\\\/\+&%\$#\=~_\-@]*)*[^\.\,\?\"\'\(\)\[\]!;<>{}\s\x7F-\xFF])?)$/;
			var ipValid = regexp.test(obj.getValue());

			if (domainValid || ipValid)
			{
			 	Ext.getCmp('decryptbutton').enable();
			 	return true;
			}
			else
			{
				Ext.getCmp('decryptbutton').disable();
				return false;
			}

		}
		else
		{
			Ext.getCmp('decryptbutton').disable();
		}
		obj.clearInvalid();

		return true;
	};

	var urlOptionsData = {
		companycode: '',
		groupcode: 'NOTPRICED',
		collectioncode: '-1',
		encryptmethods: [0, 0, 0],
		groupdata: '',
		customparameters: [],
		uioverridemode: -1,
		aimodeoverride: -1,
		wizardparams: ''
	};

	var paramOptionGroup = 0;
	var paramOptionCustom = 0;

 	var companyCombo = new Ext.taopix.CompanyCombo({
		id: 'company',
		name: 'company',
		width:500,
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
",
		hideLabel:false,
		allowBlank:false,
        labelStyle: 'width:120px',
		options:
		{
			ref: "<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
",
			storeId: 'companyStore',
			includeGlobal: "<?php echo $_smarty_tpl->tpl_vars['includeglobal']->value;?>
",
			includeShowAll: "<?php echo $_smarty_tpl->tpl_vars['showall']->value;?>
",
			onchange: function(){}
		},
		listeners:
		{
			select:
			{
				fn: function(comboBox, record, index)
				{
					urlOptionsData.companycode = comboBox.getValue();
					if (urlOptionsData.companycode == 'GLOBAL')
					{
						urlOptionsData.companycode = '';
					}

					Ext.getCmp('licenseKeyList').store.load({params: {companyCode: urlOptionsData.companycode}});
					Ext.getCmp('productcollectionlist').store.load({params: {companyCode: urlOptionsData.companycode}});
				}
			}
		}
	});

	<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
	Ext.getCmp('company').store.on(
	{
		'load': function()
		{
			Ext.getCmp('company').setValue(Ext.getCmp('company').store.getAt(0).get('code'));
		}
	});
 	<?php }?>


 	var licenseKeyDataStore = new Ext.data.Store(
	{
		id: 'licenseKeyDataStore',
		proxy: new Ext.data.HttpProxy(
		{
			url: 'index.php?fsaction=AjaxAPI.callback&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&cmd=LICENSECOMBO&companyCode=' + urlOptionsData.companycode,
			method: 'GET'
		}),
		reader: new Ext.data.ArrayReader(
			{ idIndex: 0 },
			Ext.data.Record.create([
				{name: 'id', mapping: 0},
				{name: 'name', mapping: 1}
			])
		)
	});

	var productCollectionDataStore = new Ext.data.Store(
	{
		id: 'productCollectionDataStore',
		proxy: new Ext.data.HttpProxy(
		{
			url: 'index.php?fsaction=AjaxAPI.callback&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&cmd=PRODUCTCOLLECTIONCOMBO&companyCode=' + urlOptionsData.companycode,
			method: 'GET'
		}),
		reader: new Ext.data.ArrayReader(
			{ idIndex: 0 },
			Ext.data.Record.create([
				{name: 'id', mapping: 0},
				{name: 'name', mapping: 1}
			])
		)
	});


	var gridDataStore = new Ext.data.GroupingStore(
	{
		remoteSort: true,
		remoteGroup: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminTaopixOnlineProductURLAdmin.getGridData&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
'}),
		method:'GET',
		groupField:'collectioncode',
		reader: new Ext.taopix.PagedArrayReader(
			{ idIndex: 0 },
			Ext.data.Record.create([
				{name: 'id', mapping: 0},
				{name: 'collectioncode', mapping: 1},
				{name: 'layoutcode', mapping: 2},
				{name: 'layoutname', mapping: 3},
				{name: 'url', mapping: 4}
			])
		),
		baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
	});

	gridDataStore.on(
	{
		'load': function()
		{
			if (this.getCount() > 0)
			{
				Ext.getCmp('exportButton').enable();
			}
			else
			{
				Ext.getCmp('exportButton').disable();
			}
		}
	});


	var urlGrid = new Ext.grid.EditorGridPanel({
		id: 'urlgrid',
		style:'border:1px solid #B5B8C8;',
		height: 290,
		deferRowRender:false,
		hidden:false,
		width: 950,
		store: gridDataStore,
		autoExpandColumn: 3,
		ctCls:'grid',
		viewConfig: { enableTextSelection: true },
		view: new Ext.grid.GroupingView({forceFit:false}),
		colModel: new Ext.grid.ColumnModel(
		{
			id: 'urlcolmodel',
			defaults:
			{
				sortable: false,
				resizable: true
			},
			columns:
			[
				{
					header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCollectionCode');?>
",
					id: 'collectioncode',
					dataIndex: 'collectioncode',
					hidden: true
				},
				{
					header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLayoutCode');?>
",
					width: 150,
					dataIndex: 'layoutcode'
				},
				{
					header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLayoutName');?>
",
					width: 150,
					dataIndex: 'layoutname'
				},
				{
					header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOnlineURL');?>
",
					width: 80,
					dataIndex: 'url',
					editable: true,
					editor: new Ext.form.TextField(
					{
						allowBlank: false,
						readOnly: true
					})
				}
			]
		}),
		enableColLock:true,
		draggable:false,
		enableColumnHide:false,
		enableColumnMove:false,
		trackMouseOver:false,
		stripeRows:true,
		columnLines:true
	});

	var filterDataStore = new Ext.data.ArrayStore(
	{
		id: 'filterdatastore',
		fields: ['id', 'name'],
		data: [
			['ALL', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ShowAll');?>
"],
			['ACTIVE', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowActiveOnly');?>
"],
			['INACTIVE', "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShowInActiveOnly');?>
"]
		]
	});

    var filterCombo = new Ext.form.ComboBox(
	{
		id: 'filtercombo',
		name: 'filtercombo',
		width:500,
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelFilter');?>
",
		labelStyle: 'width:120px',
		mode: 'local',
		editable: false,
		hideLabel: false,
		forceSelection: true,
		selectOnFocus: true,
		triggerAction: 'all',
		store: filterDataStore,
		valueField: 'id',
		displayField: 'name',
		useID: true,
		value: 'ALL',
		post: true,
		listeners:
		{
			select:
			{
				fn: function(comboBox, record, index)
				{
					gridDataStore.load(
					{
						params:
						{
							companycode: urlOptionsData.companycode,
							groupcode: urlOptionsData.groupcode,
							collectioncode: urlOptionsData.collectioncode,
							filter: comboBox.getValue(),
							groupdatastatus: urlOptionsData.encryptmethods[0],
							groupdata: urlOptionsData.groupdata,
							cpstatus: urlOptionsData.encryptmethods[1],
							cpdata: JSON.stringify(urlOptionsData.customparameters),
							wizstatus: urlOptionsData.encryptmethods[2],
							wizparams: urlOptionsData.wizardparams,
							uioverridemode: urlOptionsData.uioverridemode,
							aimodeoverride: urlOptionsData.aimodeoverride
						}
					});
				}
			}
		}
	});

 	var urlFormPanel = new Ext.FormPanel(
	{
        frame:true,
        id:'productURLForm',
        layout: 'form',
        padding: '3px 5px',
        defaults: { labelWidth: 170 },
        items:
        [
			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			companyCombo,
			<?php }?>
			new Ext.form.ComboBox(
			{
				id: 'licenseKeyList',
				name: 'licenseKeyList',
				width: 500,
				mode: 'local',
				editable: false,
				forceSelection: true,
				selectOnFocus: true,
				triggerAction: 'all',
				fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKey');?>
",
				labelStyle: 'width:120px',
				store: licenseKeyDataStore,
				valueField: 'id',
				displayField: 'name',
				useID: true,
				value: "",
				post: true,
				listeners:
				{
					select:
					{
						fn: function(comboBox, record, index)
						{
							if (record.data.id == 'NOTPRICED')
							{
								Ext.getCmp('filtercombo').disable();
								Ext.getCmp('filtercombo').setValue('ALL');
							}
							else
							{
								Ext.getCmp('filtercombo').enable();
							}

							urlOptionsData.groupcode = comboBox.getValue();

							gridDataStore.load(
							{
								params:
								{
									companycode: urlOptionsData.companycode,
									groupcode: urlOptionsData.groupcode,
									collectioncode: urlOptionsData.collectioncode,
									filter: Ext.getCmp('filtercombo').getValue(),
									groupdatastatus: urlOptionsData.encryptmethods[0],
									groupdata: urlOptionsData.groupdata,
									cpstatus: urlOptionsData.encryptmethods[1],
									cpdata: JSON.stringify(urlOptionsData.customparameters),
									wizstatus: urlOptionsData.encryptmethods[2],
									wizparams: urlOptionsData.wizardparams,
									uioverridemode: urlOptionsData.uioverridemode,
									aimodeoverride: urlOptionsData.aimodeoverride
								}
							});
						}
					}
				}
			}),
			new Ext.form.ComboBox(
			{
				id: 'productcollectionlist',
				name: 'productcollectionlist',
				width: 500,
				mode: 'local',
				editable: false,
				forceSelection: true,
				selectOnFocus: true,
				triggerAction: 'all',
				fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProductCollectionCode');?>
",
				labelStyle: 'width:120px',
				store: productCollectionDataStore,
				valueField: 'id',
				displayField: 'name',
				useID: true,
				value: "",
				post: true,
				listeners:
				{
					select:
					{
						fn: function(comboBox, record, index)
						{
							urlOptionsData.collectioncode = comboBox.getValue();

							gridDataStore.load(
							{
								params:
								{
									companycode: urlOptionsData.companycode,
									groupcode: urlOptionsData.groupcode,
									collectioncode: urlOptionsData.collectioncode,
									filter: Ext.getCmp('filtercombo').getValue(),
									groupdatastatus: urlOptionsData.encryptmethods[0],
									groupdata: urlOptionsData.groupdata,
									cpstatus: urlOptionsData.encryptmethods[1],
									cpdata: JSON.stringify(urlOptionsData.customparameters),
									wizstatus: urlOptionsData.encryptmethods[2],
									wizparams: urlOptionsData.wizardparams,
									uioverridemode: urlOptionsData.uioverridemode,
									aimodeoverride: urlOptionsData.aimodeoverride
								}
							});
						}
					}
				}
			}),
			filterCombo,
			{
				id: 'configureURLParametersMessage',
				xtype: 'label',
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfigreURLParametersMessage');?>
"
			},
			new Ext.Button(
			{
				id: 'configureURLParameters',
				name: 'configureURLParameters',
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfigreURLParameters');?>
",
				minWidth: 100,
				style: { marginTop: '8px', marginBottom: '8px' },
				listeners: { click: showConfigureURLWindow }
			}),
			urlGrid
        ]
    });
    licenseKeyDataStore.load();
    productCollectionDataStore.load();


    var productURlExport = function()
 	{
 		var companyCode = '';

 		<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			companyCode = Ext.getCmp('company').getValue();

			if (companyCode == 'GLOBAL')
			{
				companyCode = '';
			}
 		<?php }?>

 		var groupCode = Ext.getCmp('licenseKeyList').getValue();
 		var collectionCode = Ext.getCmp('productcollectionlist').getValue();
 		var filter = Ext.getCmp('filtercombo').getValue();


 		location.replace('index.php?fsaction=AdminTaopixOnlineProductURLAdmin.productURLExport&ref='+sessionId+'&companycode='+ companyCode +
			'&groupcode=' + groupCode + '&collectioncode=' + collectionCode + '&filter=' + filter +
			'&groupdatastatus=' + urlOptionsData.encryptmethods[0] + '&groupdata=' + urlOptionsData.groupdata +
			'&cpstatus=' + urlOptionsData.encryptmethods[1] + '&cpdata=' + JSON.stringify(urlOptionsData.customparameters) +
			'&wizstatus=' + urlOptionsData.encryptmethods[2] + '&wizparams=' + encodeURIComponent(urlOptionsData.wizardparams) +
			'&uioverridemode=' + urlOptionsData.uioverridemode + '&aimodeoverride=' + urlOptionsData.aimodeoverride);

		return false;
 	};

 	var decryptHandler = function()
 	{
 		var urlToDecrypt = Ext.getCmp('decrypturl').getValue();
 		Ext.Ajax.request(
		{
			url: 'index.php?fsaction=AdminTaopixOnlineProductURLAdmin.productURLDecrypt&ref='+ sessionId,
			method: 'POST',
			params:
			{
				decrypturl: urlToDecrypt,
				csrf_token: Ext.taopix.getCSRFToken()
			},
			success: function(response)
			{
				Ext.getCmp('decrypteddata').setValue('');
				var jsonObj = eval('(' + response.responseText + ')');

				var dataString = '';

				if (jsonObj.success == 'true')
				{
					if (jsonObj.uioverridemode == '-1')
					{
						jsonObj.uioverridemode = '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNone');?>
';
					}
					else if(jsonObj.uioverridemode == '0')
					{
						jsonObj.uioverridemode = '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEasyEditor');?>
';
					}
					else
					{
						jsonObj.uioverridemode = '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAdvancedEditor');?>
';
					}

					if (jsonObj.aimodeoverride == '-1')
					{
						jsonObj.aimodeoverride = '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNone');?>
';
					}
					else if(jsonObj.aimodeoverride == '0')
					{
						jsonObj.aimodeoverride = '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOff');?>
';
					}
					else if(jsonObj.aimodeoverride == '1')
					{
						jsonObj.aimodeoverride = '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOptional');?>
';
					}
					else
					{
						jsonObj.aimodeoverride = '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOn');?>
';
					}

					if (jsonObj.groupdatastatus == '0')
					{
						jsonObj.groupdatastatus = '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNone');?>
';
					}
					else if(jsonObj.groupdatastatus == '1')
					{
						jsonObj.groupdatastatus = '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelParameter');?>
';
					}
					else
					{
						jsonObj.groupdatastatus = '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEncrypted');?>
';
					}

					if (jsonObj.customparastatus == '0')
					{
						jsonObj.customparastatus = '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNone');?>
';
					}
					else if(jsonObj.customparastatus == '1')
					{
						jsonObj.customparastatus = '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelParameter');?>
';
					}
					else
					{
						jsonObj.customparastatus = '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEncrypted');?>
';
					}

					if (jsonObj.wizardoverridestatus == '0')
					{
						jsonObj.wizardoverridestatus = '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNone');?>
';
					}
					else if(jsonObj.wizardoverridestatus == '1')
					{
						jsonObj.wizardoverridestatus = '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelParameter');?>
';
					}
					else
					{
						jsonObj.wizardoverridestatus = '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEncrypted');?>
';
					}

					dataString += "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLargeScreenEditorOverride');?>
: " + jsonObj.uioverridemode + "\n";
					dataString += "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSmartDesignOverride');?>
: " + jsonObj.aimodeoverride + "\n";
					dataString += "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProductCollectionCode');?>
: " + jsonObj.collectioncode + "\n";
					dataString += "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProductLayoutCode');?>
: " + jsonObj.layoutcode + "\n";
					dataString += "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKeyCode');?>
: " + jsonObj.groupcode + "\n";
					dataString += "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKeyGroupDataStatus');?>
: " + jsonObj.groupdatastatus + "\n";
					dataString += "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKeyGroupData');?>
: " + jsonObj.groupdata + "\n";
					dataString += "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCustomParameterStatus');?>
: " + jsonObj.customparastatus + "\n";
					dataString += "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCustomParameters');?>
: \n";

					var paramObj = JSON.parse(jsonObj.customparamdata);

					for (var name in paramObj)
					{
						if (paramObj.hasOwnProperty(name))
						{
							dataString += "\t\t" + name + ": " + paramObj[name] + "\n";
						}
					}

					dataString += "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelWizardModeOverrideStatus');?>
: " + jsonObj.wizardoverridestatus + "\n";

					paramObj = JSON.parse(jsonObj.wizardoverrideparams);

					for (var name in paramObj)
					{
						if (paramObj.hasOwnProperty(name))
						{
							dataString += "\t\t" + name + ": " + paramObj[name] + "\n";
						}
					}
				}
				else
				{
					dataString += jsonObj.error;
				}

			  	Ext.getCmp('decrypteddata').setRawValue(dataString);
			  	Ext.getCmp('decrypteddata').enable();
			}
		});
 	};

 	var decryptURLWindowHandler = function()
 	{
		var decryptURLPanelObj = new Ext.FormPanel(
		{
			id: 'decrypturlgridpanel',
			labelAlign: 'left',
			labelWidth:120,
			autoHeight: true,
			frame:true,
			layout:'form',
			cls: 'left-right-buttons',
			items:
			[
				{
					xtype: 'textfield',
					id: 'decrypturl',
					name: 'decrypturl',
					width:700,
					fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelURL');?>
",
					validateOnBlur: true,
					post: true,
					validator: function(v){ return validateUrl(this);  }
				},
				{
					xtype:'textarea',
					id: 'decrypteddata',
					name: 'decrypteddata',
					fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDecryptedData');?>
",
					disabled: true,
					width: 700,
					height: 150
				}
			]
		});

		var gDecryptURLWindow = new Ext.Window(
		{
			id: 'decrypturlwindow',
			closable:true,
			plain:true,
			modal:true,
			draggable:true,
			title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDecryptURL');?>
",
			resizable:false,
			layout: 'fit',
			height: 'auto',
			width: 870,
			items: decryptURLPanelObj,
			listeners:
			{
				'close':
				{
					fn: function()
					{
						gDecryptURLWindowExists = false;
					}
				}
			},
			buttons: [{ id: 'decryptbutton', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDecrypt');?>
", handler: decryptHandler, disabled:true}]
		});

		gDecryptURLWindowExists = true;

		gDecryptURLWindow.show();
 	};

	function showConfigureURLWindow()
	{
		var newGroupParamOption = 0;
		var newCustomParamOption = 0;
		var newWizParamOption = 0;

		var deleteImg = '<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/delete.png';
		var addimg = '<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/add.png';

		var columnWidth = [150, 340];
		var columnList = [
			{ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelParameter');?>
" },
			{ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelValue');?>
" }
		];

		var fieldsList = [
			{
				fieldType: 'text',
				emptyText: '',
				allowBlank: false,
				width: columnWidth[0],
				hideLabel: true,
				fieldLabel: ''
			},
			{
				fieldType: 'text',
				emptyText: '',
				allowBlank: true,
				width: columnWidth[1],
				hideLabel: true,
				fieldLabel: ''
			}
		];

		var paramGrid = new Ext.taopix.urlParamPanel(
		{
			id: 'paramgrid',
			name:'paramgrid',
			height: 180,
			post: true,
			width: 560,
			data: urlOptionsData,
			disabled: true,
			config:
			{
				outputFormat: 'NEWPARAMS',
				fieldList: fieldsList,
				columnList: columnList,
				columnWidth: columnWidth,
				addPic: addimg,
				delPic: deleteImg
			}
		});
		
		var systemPanel = new Ext.Panel({
			xtype: 'fieldset',
			id: 'systemPanel',
			title: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSystemParameterSettings');?>
',
			collapsible: false,
			autoHeight: true,
			style: 'position: relative;',
			items: [
				{
					xtype : 'fieldset',
					title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleEditorModeOverride');?>
",
					items:
					[
						new Ext.Panel(
						{
							layout: 'form',
							plain: true,
							bodyBorder: false,
							border: false,
							items:
							[
								{
									fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLargeScreenEditorOverride');?>
",
									xtype: 'combo',
									id: 'uioverridemode',
									name: 'uioverridemode',
									mode: 'local',
									editable: false,
									triggerAction: 'all',
									width: 200,
									store: new Ext.data.ArrayStore({
										id: 0,
										fields: ['id', 'name'],
										data: [
											[-1, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNone');?>
"],
											[0, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEasyEditor');?>
"],
											[1, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAdvancedEditor');?>
"]
										]
									}),
									listeners:
									{
										select:
										{
											fn: function(comboBox, record, index)
											{
												urlOptionsData.uioverridemode = comboBox.getValue();
											}
										}
									},
									valueField: 'id',
									displayField: 'name',
									useID: true,
									value: urlOptionsData.uioverridemode,
									post: true
								}
							]
						})
					]
				},
				{
					xtype : 'fieldset',
					title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKeyGroupDataStatus');?>
",
					items:
					[
						new Ext.Panel(
						{
							layout: 'form',
							plain: true,
							bodyBorder: false,
							border: false,
							items:
							[
								{
									xtype: 'radiogroup',
									fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelURLParameterMethod');?>
",
									id: 'paramoptionsradio',
									columns: 1,
									autowidth: true,
									items:
									[
										{
											boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNone');?>
",
											name: 'parametermethod',
											id: 'noparams',
											inputValue: 0,
											listeners:
											{
												check: function (cb, checked)
												{
													if (checked)
													{
														Ext.getCmp('lkgroupdata').disable();
														newGroupParamOption = 0;
													}
												}
											}
										},
										{
											boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelParameter');?>
",
											name: 'parametermethod',
											id: 'addparams',
											inputValue: 1,
											listeners:
											{
												check: function (cb, checked)
												{
													if (checked)
													{
														Ext.getCmp('lkgroupdata').disable();
														newGroupParamOption = 1;

													}
												}
											}
										},
										{
											boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEncrypted');?>
",
											name: 'parametermethod',
											id: 'encparams',
											inputValue: 2,
											listeners:
											{
												check: function (cb, checked)
												{
													if (checked)
													{
														Ext.getCmp('lkgroupdata').enable();
														newGroupParamOption = 2;
													}
												}
											}
										}
									]
								},
								{
									xtype: 'textfield',
									id: 'lkgroupdata',
									name: 'lkgroupdata',
									allowBlank: true,
									fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelValue');?>
",
									maxLength: 50,
									width: 300,
									value: urlOptionsData.groupdata,
									disabled: true,
									listeners:
									{
										'blur':
										{
											fn: function(obj)
											{
												forceAlphaNumeric(obj);
											}
										}
									}
								}
							]
						})
					]
				},
				{
					xtype : 'fieldset',
					title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelWizardModeOverride');?>
",
					items:
					[
						new Ext.Panel(
						{
							layout: 'form',
							plain: true,
							bodyBorder: false,
							border: false,
							items:
							[
								{
									xtype: 'radiogroup',
									fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelURLParameterMethod');?>
",
									id: 'wizardparamoptionsradio',
									columns: 1,
									autowidth: true,
									items:
									[
										{
											boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNone');?>
",
											name: 'wizardparametermethod',
											id: 'wizardnoparams',
											inputValue: 0,
											listeners:
											{
												check: function (cb, checked)
												{
													if (checked)
													{
														Ext.getCmp('wizardparams').disable();
														newWizParamOption = 0;
													}
												}
											}
										},
										{
											boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelParameter');?>
",
											name: 'wizardparametermethod',
											id: 'wizardaddparams',
											inputValue: 1,
											listeners:
											{
												check: function (cb, checked)
												{
													if (checked)
													{
														Ext.getCmp('wizardparams').disable();
														newWizParamOption = 1;

													}
												}
											}
										},
										{
											boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEncrypted');?>
",
											name: 'wizardparametermethod',
											id: 'wizardencparams',
											inputValue: 2,
											listeners:
											{
												check: function (cb, checked)
												{
													if (checked)
													{
														Ext.getCmp('wizardparams').enable();
														newWizParamOption = 2;
													}
												}
											}
										}
									]
								},
								{
									xtype: 'textfield',
									id: 'wizardparams',
									name: 'wizardparams',
									allowBlank: true,
									fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelValue');?>
",
									maxLength: 50,
									width: 300,
									value: urlOptionsData.wizardparams,
									disabled: true
								}
							]
						})
					]
				}
				<?php if ($_smarty_tpl->tpl_vars['optionai']->value) {?>
				,
				{
					xtype : 'fieldset',
					title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSmartDesign');?>
",
					items:
					[
						new Ext.Panel(
						{
							layout: 'form',
							plain: true,
							bodyBorder: false,
							border: false,
							items:
							[
								{
									fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSmartDesignOverride');?>
",
									xtype: 'combo',
									id: 'aimodeoverride',
									name: 'aimodeoverride',
									mode: 'local',
									editable: false,
									triggerAction: 'all',
									width: 200,
									store: new Ext.data.ArrayStore({
										id: 0,
										fields: ['id', 'name'],
										data: [
											[-1, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNone');?>
"],
											[0, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOff');?>
"],
											[1, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOptional');?>
"],
											[2, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOn');?>
"]
										]
									}),
									listeners:
									{
										select:
										{
											fn: function(comboBox, record, index)
											{
												urlOptionsData.aimodeoverride = comboBox.getValue();
											}
										}
									},
									valueField: 'id',
									displayField: 'name',
									useID: true,
									value: urlOptionsData.aimodeoverride,
									post: true
								}
							]
						})
					]
				}

				<?php }?>
			]
		});
		
		var customPanel = new Ext.Panel({
			xtype: 'fieldset',
			id: 'customPanel',
			title: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCustomParameterSettings');?>
',
			collapsible: false,
			autoHeight: true,
			style: 'position: relative;',
			items: [
				{
					xtype : 'fieldset',
					border: false,
					items:
					[
						{
							xtype: 'radiogroup',
							fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelURLParameterMethod');?>
",
							id: 'customparamoptionsradio',
							columns: 1,
							autowidth: true,
							items:
							[
								{
									boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNone');?>
",
									name: 'customparametermethod',
									id: 'customnoparams',
									inputValue: 0,
									listeners:
									{
										check: function (cb, checked)
										{
											if (checked)
											{
												Ext.getCmp('paramgrid').disable();
												newCustomParamOption = 0;
											}
										}
									}
								},
								{
									boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelParameter');?>
",
									name: 'customparametermethod',
									id: 'customaddparams',
									inputValue: 1,
									listeners:
									{
										check: function (cb, checked)
										{
											if (checked)
											{
												Ext.getCmp('paramgrid').disable();
												newCustomParamOption = 1;

											}
										}
									}
								},
								{
									boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEncrypted');?>
",
									name: 'customparametermethod',
									id: 'customencparams',
									inputValue: 2,
									listeners:
									{
										check: function (cb, checked)
										{
											if (checked)
											{
												Ext.getCmp('paramgrid').enable();
												newCustomParamOption = 2;
											}
										}
									}
								}
							]
						},
						paramGrid
					]
				}
			]
		});

		

		<?php if ($_smarty_tpl->tpl_vars['optionai']->value) {?>
				
		// Screen includes AI mode override.
		var panelHeight = 455;

		<?php } else { ?>

		var panelHeight = 400;
		
		<?php }?>

		var tabPanels = {
			xtype: 'tabpanel',
			id: 'maintabpanel',
			deferredRender: false,
			enableTabScroll: true,
			activeTab: 0,
			height: panelHeight,
			shadow: true,
			plain: true,
			bodyBorder: false,
			border: false,
			style: 'margin-top:6px; ',
			bodyStyle: 'border-right: 1px solid #96bde7; border-left: 1px solid #96bde7; border-bottom: 1px solid #96bde7; background-color: #dee9f6;',
			defaults: {
				frame: false,
				autoScroll: true,
				hideMode:'offsets',
				labelWidth: 230,
				bodyStyle: 'padding:5px 10px 0 10px; border-top: 0px;'
			},
			items:
			[
				systemPanel,
				customPanel
			]
		};

		var panels = new Ext.Panel({
			id: 'configureUrlPanel',
			layout: 'form',
			style: 'padding: 5px 5px 3px 5px; border:1px none #8ca9cf; margin: 4px 0px 0px 0px',
			plain: true,
			bodyBorder: false,
			border: false,
			labelWidth: 80,
			items:
			[
				tabPanels
			]
		});

		var dialogFormPanelObj = new Ext.FormPanel(
		{
			id: 'dialogFormPanel',
			labelAlign: 'left',
			labelWidth: 130,
			autoHeight: true,
			frame: true,
			bodyStyle: 'padding-left:5px;',
			items: [
				panels
			]
		});

		var gConfigureURLWindow = new Ext.Window(
		{
			id: 'configureurlwindow',
			closable: true,
			plain: true,
			modal: true,
			draggable: true,
			title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfigreURLParameters');?>
",
			resizable: false,
			layout: 'fit',
			height: 'auto',
			width: 650,
			items: [
				dialogFormPanelObj
			],
			listeners: {
				'close': {
					fn: function(){
						gConfigureURLWindowExists = false;
					}
				}
			},
			buttons: [
				{
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
",
					handler: function()
					{
						// use existing url settings
						gConfigureURLWindow.close();
					}
				},
				{
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonOk');?>
",
					handler: function()
					{
						// on ok :-
						// remember the selected configuration
						urlOptionsData.encryptmethods[0] = newGroupParamOption;
						urlOptionsData.encryptmethods[1] = newCustomParamOption;
						urlOptionsData.encryptmethods[2] = newWizParamOption;

						// remember the updated values
						urlOptionsData.uioverridemode = Ext.getCmp("uioverridemode").getValue();
						urlOptionsData.groupdata = Ext.getCmp("lkgroupdata").getValue();
						urlOptionsData.wizardparams = Ext.getCmp("wizardparams").getValue();

						<?php if ($_smarty_tpl->tpl_vars['optionai']->value) {?>

						urlOptionsData.aimodeoverride = Ext.getCmp("aimodeoverride").getValue();

						<?php }?>

						urlOptionsData.customparameters = [];
						var theGrid = Ext.getCmp("paramgrid");

						for (var i = 0; i < theGrid.store.data.items.length; i++)
						{
							var theRow = [];
							for (var j = 0; j < theGrid.store.data.items[i].fields.items.length - 1; j++)
							{
								theRow.push(theGrid.store.data.items[i].data[theGrid.store.data.items[i].fields.items[j].name]);
							}
							urlOptionsData.customparameters.push(theRow);
						}

						// set new url configuration and recalculate urls
						gridDataStore.load(
						{
							params:
							{
								companycode: urlOptionsData.companycode,
								groupcode: urlOptionsData.groupcode,
								collectioncode: urlOptionsData.collectioncode,
								filter: filterCombo.getValue(),
								groupdatastatus: urlOptionsData.encryptmethods[0],
								groupdata: urlOptionsData.groupdata,
								cpstatus: urlOptionsData.encryptmethods[1],
								cpdata: JSON.stringify(urlOptionsData.customparameters),
								wizstatus: urlOptionsData.encryptmethods[2],
								wizparams: urlOptionsData.wizardparams,
								uioverridemode: urlOptionsData.uioverridemode,
								aimodeoverride: urlOptionsData.aimodeoverride
							}
						});

						// close form
						gConfigureURLWindow.close();
					}
				}
			]
		});

		// set the value of the URL Encrytion method selection
		Ext.getCmp("paramoptionsradio").setValue(urlOptionsData.encryptmethods[0]);
		Ext.getCmp("customparamoptionsradio").setValue(urlOptionsData.encryptmethods[1]);
		Ext.getCmp("wizardparamoptionsradio").setValue(urlOptionsData.encryptmethods[2]);

		gConfigureURLWindowExists = true;

		gConfigureURLWindow.show();
	}

    Ext.getCmp('licenseKeyList').store.on(
	{
		'load': function()
		{
    		urlOptionsData.companycode = '';

			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				urlOptionsData.companycode = Ext.getCmp('company').getValue();
				if (urlOptionsData.companycode == 'GLOBAL')
				{
					urlOptionsData.companycode = '';
				}
			<?php }?>

			// add new records to data store
			var defaultData =
			{
				id: 'NOTPRICED',
				name: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNotPriced');?>
"
			};
			Ext.getCmp('filtercombo').disable();
			var lkeyDataStore = Ext.getCmp('licenseKeyList').getStore();

			var newRecord = new lkeyDataStore.recordType(defaultData);
			newRecord.commit();
			lkeyDataStore.insert(0, newRecord);
			lkeyDataStore.commitChanges();

    		lkeyCount = Ext.getCmp('licenseKeyList').store.getTotalCount();
    		lKeyVal = '';

    		if (lkeyCount > 0)
    		{
    			lKeyVal = Ext.getCmp('licenseKeyList').store.getAt(0).get('id');
    		}

			Ext.getCmp('licenseKeyList').setValue(lKeyVal);

			// set urlOptionsData.groupcode correctly as this has been reset
			urlOptionsData.groupcode = Ext.getCmp('licenseKeyList').getValue();

			// set urlOptionsData.collectioncode to be -1 (all products)
			urlOptionsData.collectioncode = '-1'

			gridDataStore.load(
			{
				params:
				{
					companycode: urlOptionsData.companycode,
					groupcode: urlOptionsData.groupcode,
					collectioncode: urlOptionsData.collectioncode,
					filter: filterCombo.getValue(),
					groupdatastatus: urlOptionsData.encryptmethods[0],
					groupdata: urlOptionsData.groupdata,
					cpstatus: urlOptionsData.encryptmethods[1],
					cpdata: JSON.stringify(urlOptionsData.customparameters),
					wizstatus: urlOptionsData.encryptmethods[2],
					wizparams: urlOptionsData.wizardparams,
					uioverridemode: urlOptionsData.uioverridemode,
					aimodeoverride: urlOptionsData.aimodeoverride
				}
			});
		}
	});

	Ext.getCmp('productcollectionlist').store.on(
	{
		'load': function()
		{
    		urlOptionsData.companycode = '';

			<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				urlOptionsData.companycode = Ext.getCmp('company').getValue();
				if (urlOptionsData.companycode == 'GLOBAL')
				{
					urlOptionsData.companycode = '';
				}
			<?php }?>

			var defaultData =
			{
				id: '-1',
				name: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ShowAll');?>
"
			};

			var productCollectionDataStore = Ext.getCmp('productcollectionlist').getStore();

			var newRecord = new productCollectionDataStore.recordType(defaultData);
			newRecord.commit();
			productCollectionDataStore.insert(0, newRecord);
			productCollectionDataStore.commitChanges();

    		productCollectionCount = Ext.getCmp('productcollectionlist').store.getTotalCount();
    		prodCollectionVal = '';

    		if (productCollectionCount > 0)
    		{
    			prodCollectionVal = Ext.getCmp('productcollectionlist').store.getAt(0).get('id');
    		}

			Ext.getCmp('productcollectionlist').setValue(prodCollectionVal);

			gridDataStore.load(
			{
				params:
				{
					companycode: urlOptionsData.companycode,
					groupcode: urlOptionsData.groupcode,
					collectioncode: urlOptionsData.collectioncode,
					filter: filterCombo.getValue(),
					groupdatastatus: urlOptionsData.encryptmethods[0],
					groupdata: urlOptionsData.groupdata,
					cpstatus: urlOptionsData.encryptmethods[1],
					cpdata: JSON.stringify(urlOptionsData.customparameters),
					wizstatus: urlOptionsData.encryptmethods[2],
					wizparams: urlOptionsData.wizardparams,
					uioverridemode: urlOptionsData.uioverridemode,
					aimodeoverride: urlOptionsData.aimodeoverride
				}
			});
		}
	});

	gMainWindowObj = new Ext.Window(
	{
		id: 'MainWindow',
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleTaopixOnlineProductURL');?>
",
		closable:true,
		width:985,
		height:550,
		layout: 'fit',
		resizable:false,
		padding:0, margin:0,
		baseParams: { ref: "<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
" },
		items: urlFormPanel,
		listeners:
		{
			'close':
			{
				fn: function()
				{
					accordianWindowInitialized = false;
				}
			}
		},
		buttons: [{ id: 'exportButton', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonExport');?>
", disabled: true, handler: productURlExport},
				  { id: 'decryptButton', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDecryptURL');?>
", disabled: false, handler: decryptURLWindowHandler}]
	});

	gMainWindowObj.show();
}

/* close this window panel */
function windowClose()
{
	if ((gMainWindowObj) && (gMainWindowObj.close))
	{
		gMainWindowObj.close();
	}
}

<?php }
}
