{literal}

function initialize(pParams)
{
	var systemCharacterError = "{#str_ErrorSystemCharacter#}";

	function forceAlphaNumeric(pObj, pForceUpperCase, pAlphaNumericSlashPlus)
	{
		var string = pObj.getValue();

    	if (pForceUpperCase)
    	{
    		string = string.toUpperCase();
    	}

    	if (pAlphaNumericSlashPlus)
    	{
    		string = string.replace(/[^a-zA-Z_0-9\-\+/]+/g, "");
    	}
    	else
    	{
    		string = string.replace(/[^a-zA-Z_0-9\-]+/g, "");
    	}

    	pObj.setValue(string);
	}

	function validateBucketName(pObj)
	{
		var string = pObj.getValue();

    	string = string.toLowerCase();
    	string = string.replace(/[^a-z_0-9\.-]+/g, "");

    	pObj.setValue(string);

    	var lastCharacter = string.charAt(string.length - 1);
    	var labelSeperatorValid = true;

    	if (string.indexOf("..") != -1)
    	{
    		labelSeperatorValid = false;
    	}

    	if ((string.charAt(0) == '.') || ((string.charAt(0) == '-') || (lastCharacter == '.') || (lastCharacter == '-') ||
    		(!labelSeperatorValid) || (string.length < 3)))
    	{
    		pObj.markInvalid();
    		return false;
    	}
	}

	function checkFilePath(pValue)
    {
		if ((pValue.indexOf('"') != -1) || (pValue.indexOf("'") != -1) || (pValue.indexOf('?') != -1) || (pValue.indexOf('<') != -1) || (pValue.indexOf('>') != -1) || (pValue.indexOf('|') != -1) || (pValue.indexOf('*') != -1))
		{
			return systemCharacterError;
		}
		return true;
    }

	/* save functions */
	function saveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminTaopixOnlineVolumesAdmin.addeditvolume&ref={/literal}{$ref}{literal}';
		var fp = Ext.getCmp('volumeForm'), form = fp.getForm();
		var submit = true;
		
		var volumeType = Ext.getCmp('volumetype').getValue();
		
		if (Ext.getCmp('volumecode').getValue() == '')
		{
			Ext.getCmp('volumecode').markInvalid();
			submit = false;
		}
		
		if (volumeType == 2)
		{
			if (Ext.getCmp('storagename').getValue() == '')
			{
				Ext.getCmp('storagename').markInvalid();
				submit = false;
			}
			
			if (Ext.getCmp('accesskey').getValue() == '')
			{
				Ext.getCmp('accesskey').markInvalid();
				submit = false;
			}
			
			if (Ext.getCmp('secret').getValue() == '')
			{
				Ext.getCmp('secret').markInvalid();
				submit = false;
			}
		}
		else
		{
			if (Ext.getCmp('root').getValue() == '')
			{
				Ext.getCmp('root').markInvalid();
				submit = false;
			}
			
			if (Ext.getCmp('headroom').getValue() <= 0)
			{
				Ext.getCmp('headroom').markInvalid();
				submit = false;
			}
		}
		
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

		// convert the storeage class id into a value
		if (Ext.getCmp('storageclassstandard').checked)
		{
			paramArray['storageclass'] = 'storageclassstandard';
		}
		else if (Ext.getCmp('storageclassreduced').checked)
		{
			paramArray['storageclass'] = 'storageclassreduced';
		}
		else
		{
			paramArray['storageclass'] = 'storageclassinfrequent';
		}

		var assetType = 0;


		var objValue = Ext.getCmp('objectgroup').getValue().inputValue;

		if (objValue == 8)
		{
			assetType = 8;
		}
		else if (objValue == 16)
		{
			assetType = 16;
		}
		else if (objValue == 32)
		{
			assetType = 32;
		}
		else
		{
			if (Ext.getCmp('userassets').checked)
			{
				assetType += 1;
			}

			if (Ext.getCmp('globalassets').checked)
			{
				assetType += 2;
			}

			if (Ext.getCmp('productcollectionassets').checked)
			{
				assetType += 4;
			}
		}

		paramArray['assettype'] = assetType;
		
		if (assetType == 0)
		{
			Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorNoAssetTypeSelected#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING }); 
			submit = false;
		}	
		
		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", saveCallback);
		}
	}

	function saveCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var gridObj = Ext.getCmp('volumesgrid');
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

	var validateUrl = function(obj)
	{
		if ((obj.getValue() != 'http://') && (obj.getValue() != 'https://')&& (obj.getValue() != ''))
		{
			var domainValid = (Ext.form.VTypes.url(obj.getValue()));
			var regexp = /^((([hH][tT][tT][pP][sS]?|[fF][tT][pP])\:\/\/)?([\w\.\-]+(\:[\w\.\&%\$\-]+)*@)?((([^\s\(\)\<\>\\\"\.\[\]\,@;:]+)(\.[^\s\(\)\<\>\\\"\.\[\]\,@;:]+)*(\.[a-zA-Z]{2,4}))|((([01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}([01]?\d{1,2}|2[0-4]\d|25[0-5])))(\b\:(6553[0-5]|655[0-2]\d|65[0-4]\d{2}|6[0-4]\d{3}|[1-5]\d{4}|[1-9]\d{0,3}|0)\b)?((\/[^\/][\w\.\,\?\'\\\/\+&%\$#\=~_\-@]*)*[^\.\,\?\"\'\(\)\[\]!;<>{}\s\x7F-\xFF])?)$/;
			var ipValid = regexp.test(obj.getValue());
			if (domainValid || ipValid) return true; else return false;
		}
		obj.clearInvalid();
		return true;
	};

	function refreshAssetTypeSettings()
	{
		var assetType = {/literal}{$assettype}{literal};

		var globalAssetCbx = Ext.getCmp('globalassets');
		var productionCollectionAssetsCbx = Ext.getCmp('productcollectionassets');
		var userAssetsCbx = Ext.getCmp('userassets');

		var objectRadioGroup = Ext.getCmp('objectgroup');

		switch(assetType)
		{
			case 8:
			case 16:
      case 32:
				// If archive (8), resources (16) or project resources (32) volume, set the radio group.
				objectRadioGroup.setValue(assetType);
				break;
			default:
				// If it's an image (7) resource type, set the type of assets that go on the volume.
				objectRadioGroup.setValue(7);

				userAssetsCbx.setValue(((assetType & 1) === 1));
				globalAssetCbx.setValue(((assetType & 2) === 2));
				productionCollectionAssetsCbx.setValue(((assetType & 4) === 4));
				break;
		}
	}

	function refreshStorageClassSettings()
	{
		var storageClass = {/literal}{$storageclass}{literal};

		if (storageClass == 0)
		{
			Ext.getCmp('storageclassreduced').setDisabled(true)
			Ext.getCmp('storageclassstandard').setValue(true);
		}
		else if (storageClass == 1)
		{
			Ext.getCmp('storageclassreduced').setValue(true);
		}
		else
		{
			Ext.getCmp('storageclassreduced').setDisabled(true)
			Ext.getCmp('storageclassinfrequent').setValue(true);
		}

	}

	function buildForm()
	{
		var volumeType = Ext.getCmp('volumetype').getValue();

		if (volumeType == 2)
		{
			{/literal}{if $storageregion != ''}{literal}
			Ext.getCmp('storageregion').setValue('{/literal}{$storageregion}{literal}');
			{/literal}{/if}{literal}
			Ext.getCmp('storagename').setValue('{/literal}{$storagename}{literal}');
			Ext.getCmp('accesskey').setValue('{/literal}{$accesskey}{literal}');
			Ext.getCmp('secret').setValue('{/literal}{$secret}{literal}');
			Ext.getCmp('headroom').setValue(10);
			Ext.getCmp('root').setValue('C:\\');
			Ext.getCmp('root').hide();
			Ext.getCmp('headroom').hide();
			{/literal}{if $volumeid > 0}{literal}
			Ext.getCmp('free').hide();
			Ext.getCmp('size').hide();
			{/literal}{/if}{literal}
			Ext.getCmp('storageregion').show();
			Ext.getCmp('storagename').show();
			Ext.getCmp('accesskey').show();
			Ext.getCmp('secret').show();
			Ext.getCmp('uploadurl').show();
			Ext.getCmp('downloadurl').show();
			Ext.getCmp('storageclassgroup').show();
		}
		else
		{
			Ext.getCmp('headroom').setValue('{/literal}{$headroom}{literal}');
			Ext.getCmp('root').setValue('{/literal}{$root}{literal}');
			Ext.getCmp('storageregion').hide();
			Ext.getCmp('storagename').hide();
			Ext.getCmp('accesskey').hide();
			Ext.getCmp('secret').hide();
			Ext.getCmp('uploadurl').hide();
			Ext.getCmp('downloadurl').hide();
			Ext.getCmp('storageclassgroup').hide();
			Ext.getCmp('root').show();
			Ext.getCmp('headroom').show();
			{/literal}{if $volumeid > 0}{literal}
			Ext.getCmp('free').show();
			Ext.getCmp('size').show();
			{/literal}{/if}{literal}
		}
	}

	function changeVolumeObjectType()
	{
		var objectRadioGroup = Ext.getCmp('objectgroup');
		var selectedObjectType = objectRadioGroup.getValue();

		var assetTypeGroupObj = Ext.getCmp('assettypegroup');

		switch (selectedObjectType.inputValue)
		{
			case 8:
			case 16:
			case 32:
			{
				assetTypeGroupObj.disable();
				break;
			}
			default:
			{
				assetTypeGroupObj.enable();
				break;
			}
		}
	}

	var dialogFormPanelObj = new Ext.FormPanel({
		id: 'volumeForm',
        labelAlign: 'left',
        labelWidth: 90,
        autoHeight: false,
        height: 454,
        frame:true,
        layout:'form',
        cls: 'left-right-buttons',
        bodyStyle:'padding-left:5px;',
        items: [
             {
				xtype: 'panel', id: 'topPanel', layout: 'column',style:'background:#c9d8ed; padding: 3px 0; border:1px solid #8ca9cf', columns: 1, plain:true,
				bodyBorder: false, border: false, bodyStyle:'padding:5px 5px 0; border-top: 0px',
				items:
				[
					new Ext.Container({ layout: 'form', defaults:{xtype: 'textfield'}, width:300,
					items:
						[
							{
								xtype: 'textfield',
								id: 'volumecode',
								name: 'volumecode',
								maxLength: 255,
								width: 180,
								value: "{/literal}{$code}{literal}",
								fieldLabel: "{/literal}{#str_LabelCode#}{literal}",
								{/literal}{if $volumeid > 0}{literal}
								disabled: true,
								{/literal}{/if}{literal}
								listeners: {'blur': {fn: function(obj){forceAlphaNumeric(obj, true, false)}}},
								post: true
							},
							new Ext.form.RadioGroup({
								columns: 1,
								fieldLabel: "{/literal}{#str_LabelVolumeObjectType#}{literal}",
								width:500,
								layout:'column',
								id:'objectgroup',
								{/literal}{if $volumeid > 0}{literal}
								disabled: true,
								{/literal}{/if}{literal}
								style:'margin-bottom:10px',
								listeners: {'change': {fn: function(obj){changeVolumeObjectType(obj, true, false)}}},
								items:
								[
									{boxLabel: "{/literal}{#str_LabelVolumeTypeImage#}{literal}", name: 'volumeObjectType', inputValue: 7},
									new Ext.form.CheckboxGroup({
										columns: 1,
										fieldLabel: "{/literal}{#str_LabelAssetType#}{literal}",
										width:500,
										layout:'column',
										id:'assettypegroup',
										style:'margin-left:30px',
										items:
										[
											new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelGlobalAssets#}{literal}", name: 'globalassets', id: 'globalassets'}),
											new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelProductCollectionAssets#}{literal}", name: 'productcollectionassets', name: 'productcollectionassets', id: 'productcollectionassets'}),
											new Ext.form.Checkbox({boxLabel: "{/literal}{#str_LabelUserAssets#}{literal}", name: 'userassets', id: 'userassets'})
										]
									}),
									{boxLabel: "{/literal}{#str_LabelVolumeTypeArchive#}{literal}", name: 'volumeObjectType', inputValue: 8},
									{boxLabel: "{/literal}{#str_LabelVolumeTypeSystemResource#}{literal}", name: 'volumeObjectType', inputValue: 16},
									{boxLabel: "{/literal}{#str_LabelVolumeTypeProjectResource#}{literal}", name: 'volumeObjectType', inputValue: 32}
								]
							})
						]
					}),
					new Ext.Container({
						layout: 'form',
						style:'padding-left:15px',
						width:400,
						items: [
								{
								xtype: 'combo',
								id: 'volumetype',
								name: 'volumetype',
								mode: 'local',
								width: 290,
								editable: false,
								forceSelection: true,
								selectOnFocus: true,
								{/literal}{if $volumetype == 2}{literal}
								disabled:true,
								{/literal}{/if}{literal}

								triggerAction: 'all',
								fieldLabel: "{/literal}{#str_LabelVolumeType#}{literal}",
								store: new Ext.data.ArrayStore({
									id: 0,
									fields: ['id', 'name'],
									data: [{/literal}{$volumetypedata}{literal}]
								}),
								listeners:{
									select:{
										fn: function()
										{
											buildForm();
										}
									}
								},
								valueField: 'id',
								displayField: 'name',
								useID: true,
								allowBlank: false,
								validateOnBlur:true,
								value: {/literal}{$volumetype}{literal},
								post: true
							},
							{
								xtype: 'numberfield',
								id: 'preference',
								name: 'preference',
								fieldLabel: "{/literal}{#str_LabelPreference#}{literal}",
								width: 40,
								post: true,
								allowBlank: false,
								allowNegative: false,
								minValue: 0,
								value: '{/literal}{$preference}{literal}'
							}


						]
					})
				]
			},
			new Ext.Container({ layout: 'form', width: 717, style:'padding-top: 10px;', labelWidth: 200,
					items:
						[
							  {
								xtype: 'combo',
								id: 'storageregion',
								name: 'storageregion',
								width: 500,
								fieldLabel: "{/literal}{#str_LabelStorageRegion#}{literal}",
								post: true,
								mode: 'local',
								editable: false,
								forceSelection: true,
								selectOnFocus: true,
								triggerAction: 'all',
								{/literal}{if $volumetype == 2}{literal}
								disabled:true,
								{/literal}{/if}{literal}
								store: new Ext.data.ArrayStore({
									id: "aws-regions",
									fields: ["id", "name"],
									data: [{/literal}{$awsregions}{literal}]
								}),
								valueField: 'id',
								displayField: 'name',
								useID: true,
								allowBlank: false,
								value: 'us-east-1'
							  },
							  {
								xtype: 'textfield',
								id: 'storagename',
								name: 'storagename',
								maxLength: 63,
								width: 500,
								fieldLabel: "{/literal}{#str_LabelStorageName#}{literal}",
								post: true,
								value: '{/literal}{$storagename}{literal}',
								listeners: {'blur': {fn: function(obj){validateBucketName(obj)}}}
							  },
							  {
								xtype: 'textfield',
								id: 'accesskey',
								name: 'accesskey',
								maxLength: 20,
								width: 500,
								fieldLabel: "{/literal}{#str_LabelAccessKey#}{literal}",
								value: '{/literal}{$accesskey}{literal}',
								post: true,
								inputType: 'password',
								listeners: {'blur': {fn: function(obj){forceAlphaNumeric(obj, false, false)}}}
							  },
							  {
								xtype: 'textfield',
								id: 'secret',
								name: 'secret',
								maxLength: 40,
								width: 500,
								fieldLabel: "{/literal}{#str_LabelSecret#}{literal}",
								value: '{/literal}{$secret}{literal}',
								post: true,
								inputType: 'password',
								listeners: {'blur': {fn: function(obj){forceAlphaNumeric(obj, false, true)}}}
							  },
							  {
								xtype: 'textfield',
								id: 'root',
								name: 'root',
								maxLength: 255,
								width: 500,
								value: '{/literal}{$root}{literal}',
								fieldLabel: "{/literal}{#str_LabelRoot#}{literal}",
								post: true,
								validateOnBlur:true,
								{/literal}{if $volumeid > 0}{literal}
								disabled: true,
								{/literal}{/if}{literal}
								validator: checkFilePath
							  },
							  {
								xtype: 'textfield',
								id: 'uploadurl',
								name: 'uploadurl',
								width: 500,
								fieldLabel: "{/literal}{#str_LabelUploadURL#}{literal}",
								value: '{/literal}{$uploadurl}{literal}',
								validateOnBlur: true,
								post: true,
								validator: function(v){ return validateUrl(this);  }
							  },
							  {
								xtype: 'textfield',
								id: 'downloadurl',
								name: 'downloadurl',
								width: 500,
								fieldLabel: "{/literal}{#str_LabelDownloadURL#}{literal}",
								value: '{/literal}{$downloadurl}{literal}',
								validateOnBlur:true,
								post: true,
								validator: function(v){ return validateUrl(this);  }
							  },
							  { xtype: 'radiogroup', fieldLabel:'Storage Class', id: 'storageclassgroup', columns: 1, cls: 'x-check-group-alt',
								items:
								[
									{boxLabel: "{/literal}{#str_LabelStorageClassStandard#}{literal}", name:'storageclass', id:'storageclassstandard'},
									{boxLabel: "{/literal}{#str_LabelStorageClassReduced#}{literal}", name:'storageclass', id: 'storageclassreduced'},
									{boxLabel: "{/literal}{#str_LabelStorageClassInfrequent#}{literal}", name:'storageclass', id: 'storageclassinfrequent'}
								]
							  },
							  {
								xtype: 'numberfield',
								id: 'headroom',
								name: 'headroom',
								fieldLabel: "{/literal}{#str_LabelHeadroom#}{literal}",
								width: 80,
								post: true,
								allowBlank: false,
								allowNegative: false,
								value: '{/literal}{$headroom}{literal}'
							},
							{/literal}{if $volumeid > 0}{literal}
							{
								xtype: 'textfield',
								id: 'size',
								name: 'size',
								fieldLabel: "{/literal}{#str_LabelSize#}{literal}",
								width: 80,
								post: false,
								disabled: true,
								value: '{/literal}{$size}{literal}'
							},
							{/literal}{/if}{literal}
							{/literal}{if $volumeid > 0}{literal}
							{
								xtype: 'textfield',
								id: 'free',
								name: 'free',
								fieldLabel: "{/literal}{#str_LabelFree#}{literal}",
								width: 80,
								post: false,
								value: '{/literal}{$freespace}{literal}',
								disabled: true
							},
							{/literal}{/if}{literal}
							{ xtype: 'hidden', id: 'volumeid', name: 'volumeid', value: "{/literal}{$volumeid}{literal}",  post: true},
							{ xtype: 'hidden', id: 'serverid', name: 'serverid', value: "{/literal}{$serverid}{literal}",  post: true}
						]
					})
        ]
    });

    /* create modal window for add and edit */
    var gDialogObj = new Ext.Window({
		id: 'dialog',
	  	closable:false,
	  	plain:true,
	  	modal:true,
	  	draggable:true,
	  	resizable:false,
	  	layout: 'fit',
	  	autoHeight: true,
	  	width: 750,
	  	items: dialogFormPanelObj,
	  	listeners: {
			'close': {
				fn: function(){
    				volumeEditWindowExists = false;
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
      			{/literal}{if $active == 1}{literal}
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
				handler: saveHandler,
				{/literal}{if $volumeid == 0}{literal}
					text: "{/literal}{#str_ButtonAdd#}{literal}"
				{/literal}{else}{literal}
					text: "{/literal}{#str_ButtonUpdate#}{literal}"
				{/literal}{/if}{literal}
			}
		]
	});

	buildForm();

	gDialogObj.show();
	refreshAssetTypeSettings();
	refreshStorageClassSettings();
}

{/literal}