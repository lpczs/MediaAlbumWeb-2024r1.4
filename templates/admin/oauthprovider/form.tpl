/**
* Re-centres and syncs the shadow of the dialog.
*/
function repositionDialog()
{
    Ext.getCmp('dialog').center();
};

function initialize()
{
	var providerDetails = {$providerData};
	var providerList = {$formProviderList};
	var haveItem = providerDetails.hasOwnProperty('id') && 0 !== providerDetails.id;

    var dialogTitle = haveItem ? "{#str_TitleEditProvider#} (" + providerDetails.providername + ")" : "{#str_TitleAddProvider#}";
	var saveButtonTitle = haveItem ? "{#str_ButtonUpdate#}" : "{#str_ButtonAdd#}";

	var closeHandler = function() {
		var gridObj = gMainWindowObj.findById('oauthProvider');
		var dataStore = gridObj.store;
		gridObj.store.reload();
		Ext.getCmp('dialog').close();
	};

	var onCallback = function(updated, form, resultData) {
		if (!updated) {
			return;
		}

		if (false === resultData.result.success) {
			Ext.MessageBox.show({
				title: resultData.result.title,
				msg: resultData.result.msg,
				buttons: Ext.MessageBox.OK,
				icon: Ext.MessageBox.INFO
			});
			return;
		}

		closeHandler();
	};

	var saveHandler = function() {
		var fp = Ext.getCmp('mainform');
		var form = fp.getForm();
		var params = {
		};

		if (form.isValid()) {
			Ext.taopix.formPanelPost(fp, form, params, 'index.php?fsaction=AdminOAuthProvider.save&ref={$ref}', "{#str_MessageSaving#}", onCallback);
		}
	};

	var optionalFieldRequiredStatus = function(value) {
		var providerField = Ext.getCmp('provider');
		if ("League\\OAuth2\\Client\\Provider\\GenericProvider" === providerField.getValue() && '' === value.trim()) {
			return false;
		}

		var domainValid = (Ext.form.VTypes.url(value));
		var ipRegex = {literal}/^((([hH][tT][tT][pP][sS]?|[fF][tT][pP])\:\/\/)?([\w\.\-]+(\:[\w\.\&%\$\-]+)*@)?((([^\s\(\)\<\>\\\"\.\[\]\,@;:]+)(\.[^\s\(\)\<\>\\\"\.\[\]\,@;:]+)*(\.[a-zA-Z]{2,4}))|((([01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}([01]?\d{1,2}|2[0-4]\d|25[0-5])))(\b\:(6553[0-5]|655[0-2]\d|65[0-4]\d{2}|6[0-4]\d{3}|[1-5]\d{4}|[1-9]\d{0,3}|0)\b)?((\/[^\/][\w\.\,\?\'\\\/\+&%\$#\=~_\-@]*)*[^\.\,\?\"\'\(\)\[\]!;<>{}\s\x7F-\xFF])?)$/;{/literal}
		var ipTest = ipRegex.test(value);

		return domainValid || ipTest;
	};

	var scopeFieldRequiredStatus = function(value) {
		var providerField = Ext.getCmp('provider');
		if (("League\\OAuth2\\Client\\Provider\\GenericProvider" === providerField.getValue() || "TheNetworg\\OAuth2\\Client\\Provider\\Azure" === providerField.getValue()) && '' === value.trim()) {
			return false;
		}

		return true;
	}

	var providerSelectionUpdate = function(box, record, provider) {
		providerFieldUpdates(record.data.provider, true);
	};

	var providerFieldUpdates = function (provider, setValues) {
		var scopes = Ext.getCmp('scopes');
		var authUrl = Ext.getCmp('authUrl');
		var tokenUrl = Ext.getCmp('tokenUrl');
		var ownerUrl = Ext.getCmp('ownerUrl');
		var tenantId = Ext.getCmp('tenantId');

		switch (provider) {
			case "League\\OAuth2\\Client\\Provider\\Google":
				scopes.disable();
				authUrl.disable();
				tokenUrl.disable();
				ownerUrl.disable();
				tenantId.disable();
				if (setValues) {
					scopes.setValue('https://mail.google.com');
					authUrl.setValue('');
					tokenUrl.setValue('');
					ownerUrl.setValue('');
					tenantId.setValue('');
				}
				break;
			case "TheNetworg\\OAuth2\\Client\\Provider\\Azure":
				scopes.enable();
				tenantId.enable();
				authUrl.disable();
				tokenUrl.disable();
				ownerUrl.disable();
				if (setValues) {
					scopes.setValue('').clearInvalid();
					tenantId.setValue('').clearInvalid();
					authUrl.setValue('');
					tokenUrl.setValue('');
					ownerUrl.setValue('');
				}
				break;
			default:
				tenantId.enable();
				scopes.enable();
				authUrl.enable();
				tokenUrl.enable();
				ownerUrl.enable();
				if (setValues) {
					scopes.setValue('').clearInvalid();
					authUrl.setValue('').clearInvalid();
					tokenUrl.setValue('').clearInvalid();
					ownerUrl.setValue('').clearInvalid();
					tenantId.setValue('').clearInvalid();
				}
		}
	};

	var providerFieldSet =new Ext.taopix.FormPanel({
		id: 'mainform',
		header: false,
		frame: true,
		width: 685,
		height: 333,
		layout: 'form',
		defaultType: 'textfield',
		padding: 15,
		items: [
			{
				xtype: 'hidden',
				id: 'id',
				name: 'id',
				value: haveItem ? providerDetails.id : 0,
				post: true
			},
			{
				xtype: 'hidden',
				id: 'initialSecret',
				name: 'initialsecret',
				value: haveItem ? providerDetails.clientsecret : '',
				post: true
			},
			{
				xtype: 'textfield',
				id: 'providerName',
				name: 'providername',
				allowBlank: false,
				post: true,
				width: 400,
				fieldLabel: "{#str_LabelName#}",
				validateOnBlur: true
			},
			new Ext.form.ComboBox({
				id: 'provider',
				name: 'provider',
				mode: 'local',
				editable: false,
				forceSelection: true,
				fieldLabel: "{#str_LabelProvider#}",
				store: new Ext.data.ArrayStore({
					id: 0,
					fields: ['provider', 'name'],
					data: providerList
				}),
				valueField: 'provider',
				value: haveItem ? providerDetails.provider : '',
				displayField: 'name',
				useID: true,
				post: true,
				width:425,
				allowBlank: false,
				triggerAction: 'all',
				listeners: {
					'select': providerSelectionUpdate
				}
			}),
			{
				xtype: 'textfield',
				id: 'clientId',
				name: 'clientid',
				allowBlank: false,
				post: true,
				width: 400,
				fieldLabel: "{#str_LabelClientId#}",
				validateOnBlur: true
			},
			{
				xtype: 'textfield',
				inputType: haveItem ? 'password' : 'text',
				id: 'clientSecret',
				name: 'clientsecret',
				allowBlank: false,
				post: true,
				width: 400,
				fieldLabel: "{#str_LabelClientSecret#}",
				validateOnBlur: true
			},
			{
				xtype: 'textfield',
				id: 'scopes',
				name: 'scopes',
				post: true,
				width: 400,
				fieldLabel: "{#str_LabelScopes#}",
				validator: scopeFieldRequiredStatus,
				validateOnBlur: true
			},
			{
				xtype: 'textfield',
				id: 'tenantId',
				name: 'tenantid',
				post: true,
				width: 400,
				fieldLabel: "{#str_LabelTenantId#}",
			},
			{
				xtype: 'textfield',
				id: 'authUrl',
				name: 'authurl',
				post: true,
				width: 400,
				fieldLabel: "{#str_LabelAuthURL#}",
				validator: optionalFieldRequiredStatus,
				validateOnBlur: true
			},
			{
				xtype: 'textfield',
				id: 'tokenUrl',
				name: 'tokenurl',
				post: true,
				width: 400,
				fieldLabel: "{#str_LabelTokenURL#}",
				validator: optionalFieldRequiredStatus,
				validateOnBlur: true
			},
			{
				xtype: 'textfield',
				id: 'ownerUrl',
				name: 'ownerurl',
				post: true,
				width: 400,
				fieldLabel: "{#str_LabelOwnerURL#}",
				validator: optionalFieldRequiredStatus,
				validateOnBlur: true
			}
		]
	});

    var gDialogObj = new Ext.Window({
        id: 'dialog',
		closable: false,
		plain: true,
		modal: true,
		draggable: true,
		title: dialogTitle,
		resizable: false,
		layout: 'fit',
		height: 'auto',
		width: 600,
		items: providerFieldSet,
		listeners: {
			'close': {
				fn: function() {
					providerWindowExists = false;
				}
			}
		},
		cls: 'right-buttons',
		buttons: [
			{
				text: "{#str_ButtonCancel#}",
				handler: closeHandler
			},
			{
				id: 'addEditButton',
				handler: saveHandler,
				text: saveButtonTitle
			}
		]
    });

	var populateFields = function() {
		Ext.getCmp('providerName').setValue(providerDetails.providername);
		Ext.getCmp('provider').setValue(providerDetails.provider);
		Ext.getCmp('clientId').setValue(providerDetails.clientid);
		Ext.getCmp('clientSecret').setValue(providerDetails.clientsecret);
		Ext.getCmp('scopes').setValue(providerDetails.scopes);
		Ext.getCmp('authUrl').setValue(providerDetails.authurl);
		Ext.getCmp('tokenUrl').setValue(providerDetails.tokenurl);
		Ext.getCmp('ownerUrl').setValue(providerDetails.ownerurl);
		Ext.getCmp('tenantId').setValue(providerDetails.tenantid);
	}
	if (haveItem) {
		populateFields();
		providerFieldUpdates(providerDetails.provider, false);
	}

	gDialogObj.show();
}