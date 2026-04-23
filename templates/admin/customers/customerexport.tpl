{literal}

var exportHandler = function()
{
    onExportStartedCallback = function() {
        gDialogObj.close();
    
        var dialogPanelObj = new Ext.Container({
            html: "{/literal}{#str_MessageDataIsBeingExported#}{literal}<br><br>{/literal}{#str_MessageDataExportEmailUponCompletion#}{literal}",
            style: {
                padding: '10px'
            }
        });

        gDialogObj = new Ext.Window({
            id: 'dialog',
            plain: true,
            title: "{/literal}{$title}{literal}",
            modal: true,
            closable: false,
            draggable: true,
            resizable: false,
            layout: 'fit',
            items: [ dialogPanelObj ],
            width: 415,
            buttons: [{
                text: "{/literal}{#str_ButtonDone#}{literal}",
                handler: function(btn, ev){ gDialogObj.close(); }
            }]
        });

        gDialogObj.show();
    }

	/* Reauthenticate the logged in user to make the changes. */
	showAdminReauthDialogue(
	{
		ref: {/literal}{$ref}{literal},
		reason: 'CUSTOMER-EXPORT',
		title: Ext.taopix.ReauthenticationDialog.strings.titleAuthenticateToSave,
		success: function()
		{
			// Pass form fields to backend via parameter object
			var paramArray = {
				companyCode: Ext.getCmp('companyCode').getValue(),
				brandCode: Ext.getCmp('brandCode').getValue(),
				groupCode: Ext.getCmp('groupCode').getValue(),
				countryCode: Ext.getCmp('country').getValue(),

				contactEmail: Ext.getCmp('contactEmail').getValue(),
				contactLastName: Ext.getCmp('contactLastName').getValue(),

				exportFileFormat: Ext.getCmp('exportFileFormat').getValue()
			};

			Ext.taopix.formPost(
				gMainWindowObj,
				paramArray,
				'index.php?fsaction=AdminCustomers.export',
				"{/literal}{#str_MessageExporting#}{literal}",
				onExportStartedCallback
			);
		}
	});
}

function initialize(pParams)
{
    var companyCombo = new Ext.taopix.CompanyCombo({
        id: 'companyCode',
        name: 'companyCode',
        fieldLabel: "{/literal}{#str_LabelCompany#}{literal}",
        hideLabel:false,
        allowBlank:false,
        defvalue: '{/literal}{$company}{literal}',
        options: {
            ref: '{/literal}{$ref}{literal}',
            storeId: 'companyStore',
            {/literal}{if $companyLogin}{literal}
            includeGlobal: '0',
            {/literal}{else}{literal}
            includeGlobal: '1',
            {/literal}{/if}{literal}
            includeShowAll: '1',
            allLabel: '{/literal}{#str_LabelAll#}{literal}',
            onchange: function(){var companyCode = companyCombo.getValue(); if (companyCode == 'GLOBAL') companyCode = '';}
        },
        width: 257
    });

    var webBrandCombo = new Ext.taopix.BrandCombo({
        id: 'brandCode',
        name: 'brandCode',
        fieldLabel: '{/literal}{#str_LabelBrand#}{literal}',
        hideLabel:false,
        options: {
            ref: '{/literal}{$ref}{literal}',
            includeShowAll: '1',
            allLabel: '{/literal}{#str_LabelAll#}{literal}',
            {/literal}{if $companyLogin}{literal}
            companyCode: '{/literal}{$company}{literal}'
            {/literal}{else}{literal}
            companyCode: ''
            {/literal}{/if}{literal}
        },
        width: 257
    });

    var licenseKeyCombo = new Ext.taopix.LicenseKeyCombo({
        id: 'groupCode',
        name: 'groupCode',
        fieldLabel: "{/literal}{#str_LabelLicenseKey#}{literal}",
        hideLabel:false,
        options: {
            ref: '{/literal}{$ref}{literal}',
            includeShowAll: '1',
            allLabel: '{/literal}{#str_LabelAll#}{literal}',
            {/literal}{if $companyLogin}{literal}
            companyCode: '{/literal}{$company}{literal}'
            {/literal}{else}{literal}
            companyCode: ''
            {/literal}{/if}{literal}
        },
        width: 257
    });

    var countryCombo = new Ext.taopix.CountryCombo({
        id: 'country',
        name: 'country',
        fieldLabel: '{/literal}{#str_LabelCountry#}{literal}',
        hideLabel:false,
        options: {
            includeShowAll: '1',
            allLabel: '{/literal}{#str_LabelAll#}{literal}',
        },
        width: 257
    });

    var emailTextField = new Ext.form.TextField({
        id: 'contactEmail',
        name: 'contactEmail',
        fieldLabel: '{/literal}{#str_LabelEmailAddress#}{literal}',
        width: 257
    });

    var contactLastNameTextField = new Ext.form.TextField({
        id: 'contactLastName',
        name: 'contactLastName',
        fieldLabel: '{/literal}{#str_LabelLastName#}{literal}',
        width: 257
    });

    var exportFileFormat = new Ext.form.ComboBox({
        id: 'exportFileFormat',
        name: 'exportFileFormat',
        fieldLabel: "{/literal}{#str_LabelExportFileFormat#}{literal}",
        width: 257,
        editable: false,
        forceSelection: true,
  		value: 'csv',
        store: new Ext.data.ArrayStore({
			id: 'formatstore',
			fields: ['id', 'format'],
			data: [
					['csv', 'CSV'],
                    ['tsv', 'TSV']
			]
		}),
		valueField: 'id',
		displayField: 'format',
		mode: 'local',
		triggerAction: 'all',
		useID: true
    });

    var horizontalRule = {
        xtype: 'box',
        autoEl: {tag: 'hr'}
    };

    var dialogFormPanelObj = new Ext.taopix.FormPanel({
        id: 'mainform',
        header: false,
        frame:true,
        width: 400,
        labelWidth: 125,
        layout: 'form',
        defaultType: 'textfield',
        autoHeight: true,
        items: [
        new Ext.form.Label(
			{
				fieldLabel: "{/literal}{#str_LabelSelectFilters#}{literal}",
				style:
				{
					marginTop: '5px',
					marginBottom: '10px'
				}
			}),
            companyCombo,
            webBrandCombo,
            licenseKeyCombo,
            countryCombo,
            emailTextField,
            contactLastNameTextField,
            horizontalRule,
            exportFileFormat
        ],
        baseParams:	{ ref: '{/literal}{$ref}{literal}' }
    });

    gDialogObj = new Ext.Window({
        id: 'customerexport_dialog',
        plain: true,
        title: "{/literal}{$title}{literal}",
        modal:true,
        closable: false,
        draggable: true,
        resizable: false,
        layout: 'fit',
        border: false,
        bodyBorder: false,
        items: [
            new Ext.Container(
			{
				html: "{/literal}{#str_LabelExportedDataFolder#} {$dataExportPath}{literal}",
				boxMaxHeight: 42,
				style:
				{
                    lineHeight: '1.5em',
					marginTop: '5px',
					marginBottom: '10px'
				}
			}),
            dialogFormPanelObj
        ],
        width: 415,
        buttons: [
            {
                text: "{/literal}{#str_ButtonCancel#}{literal}",
                handler: function(btn, ev){ gDialogObj.close(); }
            },
            {
                text: "{/literal}{#str_ButtonRunExport#}{literal}",
                handler: exportHandler
            }
        ]
    });

    gDialogObj.show();
}
{/literal}
