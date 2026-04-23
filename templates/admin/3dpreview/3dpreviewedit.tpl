{literal}
var editModelDialog;

function initialize(pParams)
{
	/* save functions */
	function saveHandler(pBtn, pEv)
	{
		var submit = true;

		var addModelForm = Ext.getCmp('addModelForm').getForm();
	
		if (addModelForm.isValid())
		{
			var action = '{/literal}{if $isEdit == 1}{literal}edit{/literal}{else}{literal}add{/literal}{/if}{literal}';

			addModelForm.submit(
			{
				url: './?fsaction=Admin3DPreview.upload3DPreviewModel&ref={/literal}{$ref}{literal}&action=' + action,
				waitMsg: "{/literal}{#str_MessageUploadingPreviewModelFile#}{literal}",
				success: function(pForm, pAction)
				{
					var gridObj = gDialogObj.findById('modelGrid');
					var dataStore = gridObj.store;

					gridObj.store.reload();
					editModelDialog.close();
				},
				failure: function(pForm, pAction)
				{
					var gridObj = gDialogObj.findById('modelGrid');
					var dataStore = gridObj.store;

					gridObj.store.reload();

					{/literal}
					{if $isEdit == 0}
					Ext.getCmp('modelid').setValue('-1');
					{/if}
					{literal}

					Ext.MessageBox.show(
					{
						title: pAction.result.title,
						msg: pAction.result.msg,
						buttons: Ext.MessageBox.OK,
						icon: Ext.MessageBox.ERROR
					});
				}
			});
		}
	}

	function validate(pValue, pAllowDecimal, pAllowNegative)
	{
		var valid = false;
		
		if (! isNumeric(pValue, true, false))
		{
			valid = false;
		}
		else
		{
			valid = true;
		}

		return valid;
	}

	function forceAlphaNumeric()
	{
		var code = Ext.getCmp('modelcode').getValue();

   		code = code.toUpperCase();
    	code = code.replace(/[^A-Z_0-9\-]+/g, "");

   	 	Ext.getCmp('modelcode').setValue(code);
	}

	function validateFileExtension(pFileName)
	{
		var exp = /^.*\.(zip|gltf)$/;
		return exp.test(pFileName);
	}

	function validateModelUpload()
	{
		var modelFileValid = true;
		var fileName = Ext.getCmp('modelfile').getValue().toLowerCase();

		if (fileName != '')
		{
			if (! validateFileExtension(fileName))
			{
				Ext.MessageBox.show(
				{
					title: "{/literal}{#str_TitleWarning#}{literal}",
					msg: "{/literal}{#str_MessageSelectModel#}{literal}",
					buttons: Ext.MessageBox.OK,
					icon: Ext.MessageBox.WARNING
				});
				
				modelFileValid = false;

				Ext.getCmp('modelfile').setValue('');
			}
		}

		return modelFileValid;
	}

	var dialogFormPanelObj = new Ext.FormPanel(
	{
		id: 'addModelForm',
        labelAlign: 'left',
        labelWidth: 120,
        autoHeight: true,
        frame: true,
        layout: 'form',
        cls: 'left-right-buttons',
        bodyStyle: 'padding-left: 5px;',
		fileUpload: true,
		plain: true,
        items: 
        [
			{
				xtype: 'hidden',
				id: 'modelid',
				name: 'modelid',
				allowBlank: false,
				maxLength: 50,
			{/literal}{if $isEdit == 1}{literal}
				value: '{/literal}{$modelID}{literal}',
			{/literal}{else}{literal}
				value: -1
			{/literal}{/if}{literal}
			},
			{
				xtype: 'hidden',
				id: 'active',
				name: 'active',
				allowBlank: false,
      		{/literal}{if $isActive == 1}{literal}
				value: 1
			{/literal}{else}{literal}
				checked: 0
			{/literal}{/if}{literal}
			},
			{
				xtype: 'textfield',
				id: 'modelcode',
				name: 'modelcode',
				allowBlank: false,
				maxLength: 50,
				width:300,
			{/literal}{if $isEdit == 1}{literal}
				value: '{/literal}{$modelCode}{literal}',
				style: {textTransform: "uppercase", background: "#dee9f6"},
				readOnly: true,
			{/literal}{else}{literal}
				style: {textTransform: "uppercase"},
			{/literal}{/if}{literal}
				fieldLabel: "{/literal}{#str_LabelCode#}{literal}",
				listeners:
				{
					blur: forceAlphaNumeric,
					change: function()
					{
						if (Ext.getCmp('modelid').getValue() != '')
						{
							Ext.getCmp('modelid').setValue('-1');
						}
					}
				}
			},
			{
				xtype: 'textfield',
				id: 'modelname',
				name: 'modelname',
				allowBlank: false,
				maxLength: 200,
				width:300,
			{/literal}{if $isEdit == 1}{literal}
				value: '{/literal}{$modelName}{literal}',
			{/literal}{/if}{literal}
				fieldLabel: "{/literal}{#str_LabelName#}{literal}"
			},
			{
				xtype: 'fileuploadfield',
				id: 'modelfile',
				fieldLabel: "{/literal}{#str_Label3DModel#}{literal}",
				name: 'modelfile',
				buttonText: "{/literal}{#str_LabelBrowse#}{literal}",
				buttonCfg: {},
				height: 25,
				width: 200,
				allowBlank: false,
				validateOnBlur: false,
			{/literal}{if $isEdit == 1 && $modelFilename != ''}{literal}
				value: "{/literal}{$modelFilename}{literal}",
			{/literal}{/if}{literal}
				validator: function()
				{
					return validateModelUpload();
				},
				listeners:
				{
					fileselected: function()
					{
						// chrome adds "c:\fakepath\" to the model filename so strip it out
						Ext.getCmp('modelfile').setValue(Ext.getCmp('modelfile').getValue().replace('C:\\fakepath\\', ''));
					}
				}
			}
		],
		baseParams:
		{
			ref: "{/literal}{$ref}{literal}",
			csrf_token: Ext.taopix.getCSRFToken()
		}
    });

    /* create modal window for add and edit */
    editModelDialog = new Ext.Window(
	{
		id: 'edit3DPreviewModelDialog',
		title: "{/literal}{$title}{literal}",
	  	closable: false,
	  	plain: true,
	  	modal: true,
	  	draggable: true,
	  	resizable: false,
	  	layout: 'fit',
	  	autoHeight: false,
	  	autoHeight: true,
	  	width: 610,
	  	items: dialogFormPanelObj,
	  	listeners: {
			'close': {
				fn: function()
				{
					modelPreviewEditWindowExists = false;
				}
			}
		},
	  	cls: 'left-right-buttons',
	  	buttons:
		[
			new Ext.form.Checkbox(
			{
				id: 'isactive',
				name: 'isactive',
				boxLabel: "{/literal}{#str_LabelActive#}{literal}",
				post: true,
				cls: 'x-btn-left',
      			ctCls: 'width_100',
				listeners:
				{
					check: function()
					{
						if (Ext.getCmp('isactive').checked)
						{
							Ext.getCmp('active').setValue(1);
						}
						else
						{
							Ext.getCmp('active').setValue(0);
						}
					}
				},
      			{/literal}{if $isActive == 1}{literal}
				checked: true
				{/literal}{else}{literal}
				checked: false
				{/literal}{/if}{literal}
			}),
			{
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function()
				{
					Ext.getCmp('edit3DPreviewModelDialog').close();
				},
				cls: 'x-btn-right'
			},
			{
				text: "{/literal}{#str_ButtonAdd#}{literal}",
				id: 'addEditButton',
				cls: 'x-btn-right',
				handler: saveHandler,
				{/literal}{if $isEdit == 0}{literal}
				text: "{/literal}{#str_ButtonAdd#}{literal}"
				{/literal}{else}{literal}
				text: "{/literal}{#str_ButtonUpdate#}{literal}"
				{/literal}{/if}{literal}
			}
		]
	});

	editModelDialog.show();
}

{/literal}
