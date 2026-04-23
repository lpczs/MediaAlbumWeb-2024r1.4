{literal}

function initialize(pParams)
{
	function forceAlphaNumeric()
	{
		var code = Ext.getCmp('imageservercode').getValue();

    	code = code.toUpperCase();
    	code = code.replace(/[^A-Z_0-9\-]+/g, "");

    	Ext.getCmp('imageservercode').setValue(code);
	}

	/* save functions */
	function saveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminTaopixOnlineImageServersAdmin.addeditserver&ref={/literal}{$ref}{literal}';
		var fp = Ext.getCmp('imageServerForm');
		var submit = true;

		var paramArray = new Object();
		paramArray['isactive'] = '{/literal}{$active}{literal}';

		if (Ext.getCmp('imageservercode').getValue() == '')
		{
			Ext.getCmp('imageservercode').markInvalid();
			submit = false;
		}

		if (!validateUrl(Ext.getCmp('serverurl')))
		{
			submit = false;
		}

		if (submit)
		{
			Ext.taopix.formPanelPost(fp, fp.getForm(), paramArray, submitURL, "{/literal}{#str_MessageSaving#}{literal}", saveCallback);
		}
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

	function validateUrl(obj)
	{
		if ((obj.getValue() == 'http://') || (obj.getValue() == ''))
		{
			obj.markInvalid();
			return false;
		}
		else
		{
			if ((obj.getValue() != 'http://') && (obj.getValue() != ''))
			{
				var domainValid = (Ext.form.VTypes.url(obj.getValue()));
				var regexp = /^((([hH][tT][tT][pP][sS]?|[fF][tT][pP])\:\/\/)?([\w\.\-]+(\:[\w\.\&%\$\-]+)*@)?((([^\s\(\)\<\>\\\"\.\[\]\,@;:]+)(\.[^\s\(\)\<\>\\\"\.\[\]\,@;:]+)*(\.[a-zA-Z]{2,4}))|((([01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}([01]?\d{1,2}|2[0-4]\d|25[0-5])))(\b\:(6553[0-5]|655[0-2]\d|65[0-4]\d{2}|6[0-4]\d{3}|[1-5]\d{4}|[1-9]\d{0,3}|0)\b)?((\/[^\/][\w\.\,\?\'\\\/\+&%\$#\=~_\-@]*)*[^\.\,\?\"\'\(\)\[\]!;<>{}\s\x7F-\xFF])?)$/;
				var ipValid = regexp.test(obj.getValue());

				if (domainValid || ipValid)
				{
					obj.clearInvalid();
					return true;
				}
				else
				{
					obj.markInvalid();
					return false;
				}
			}
		}
	};

	var initializeControls = function()
	{
		{/literal}
			{if $serverurl==''}
				Ext.getCmp('serverurl').setValue('http://');
			{/if}
		{literal}
	};

	var dialogFormPanelObj = new Ext.FormPanel({
		id: 'imageServerForm',
        labelAlign: 'left',
        labelWidth:120,
        autoHeight: true,
        frame:true,
        layout:'form',
        cls: 'left-right-buttons',
        bodyStyle:'padding-left:5px;',
        items: [
             {
         		xtype: 'textfield',
               	id: 'imageservercode',
               	name: 'imageservercode',
               	maxLength: 255,
 				width:400,
                value: '{/literal}{$code}{literal}',
                fieldLabel: "{/literal}{#str_LabelCode#}{literal}",
                {/literal}{if $serverID > 0}{literal}
                disabled: true,
                {/literal}{/if}{literal}
                listeners:{
					blur:{
						fn: forceAlphaNumeric
					}
  				},
               	post: true
              },
              {
				xtype: 'textfield',
				id: 'serverurl',
				name: 'serverurl',
				fieldLabel: "{/literal}{#str_LabelServerURL#}{literal}",
				value: '{/literal}{$serverurl}{literal}',
				width: 400,
				listeners:{
					blur:{
						fn: validateUrl
					}
  				},
				post: true
			},
			{
				xtype: 'numberfield',
				id: 'preference',
				name: 'preference',
				value: '{/literal}{$preference}{literal}',
				fieldLabel: "{/literal}{#str_LabelPreference#}{literal}",
				width: 40,
				post: true,
				allowBlank: false,
				allowNegative: false,
				minValue: 0
			},
		    { xtype: 'hidden', id: 'serverid', name: 'serverid', value: "{/literal}{$serverID}{literal}",  post: true}

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
	  	autoHeight:false,
	  	autoHeight: true,
	  	width: 610,
	  	items: dialogFormPanelObj,
	  	listeners:
	  	{
			'close':
			{
				fn: function()
				{
    				serverEditWindowExists = false;
				}
			}
		},
	  	title: "{/literal}{$title}{literal}",
	  	buttons:
		[
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
				{/literal}{if $serverID == 0}{literal}
					text: "{/literal}{#str_ButtonAdd#}{literal}"
				{/literal}{else}{literal}
					text: "{/literal}{#str_ButtonUpdate#}{literal}"
				{/literal}{/if}{literal}
			}
		]
	});
	initializeControls();
	gDialogObj.show();
}

{/literal}