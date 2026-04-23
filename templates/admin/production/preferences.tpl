{literal}

function initialize(pParams)
{	
	var gridObj = Ext.getCmp('productionmaingrid');
	var columnsArray = gridObj.colModel.columns;
	var excludeArray = ['orderid', 'id', '']
	var preferenceCheckboxes = [];
	var TPX_COORDINATE_SCALE_INCHES = {/literal}{$TPX_COORDINATE_SCALE_INCHES}{literal};
	var TPX_COORDINATE_SCALE_MILLIMETRES = {/literal}{$TPX_COORDINATE_SCALE_MILLIMETRES}{literal};

	function inArray(needle, haystack) {
		var length = haystack.length;
		for(var i = 0; i < length; i++) {
			if(haystack[i] == needle) return true;
		}
		return false;
	}

	for (var rec = 0; rec < columnsArray.length; rec++)
	{	
		var isChecked = true;

		if (columnsArray[rec].hidden == true)
		{
			isChecked = false;
		}

		if ((!inArray(columnsArray[rec].dataIndex, excludeArray)))
		{
			var obj = ({
				xtype: 'checkbox',
				id: columnsArray[rec].dataIndex,
				name: columnsArray[rec].dataIndex,
				boxLabel: columnsArray[rec].header,
				width: 250,
				hidden: false,
				checked: isChecked
			});

			preferenceCheckboxes.push(obj);
		}
	}

 	var okFunction = function(btn,ev)
 	{
		var itemsArray = container.items.items;
		var gridObj = Ext.getCmp('productionmaingrid');
		var colModel = gridObj.getColumnModel();
		var checkedCount = 0;
		var data = {};
		data['displaycolumns'] = [];
		data['measurementunit'] = '';

		for (var i = 0; i < itemsArray.length; i++)
		{
			data['displaycolumns'].push({index:itemsArray[i].id, checked:itemsArray[i].checked});

			if (itemsArray[i].checked)
			{
				checkedCount++;
			}
		}

		if (checkedCount == 0)
		{
			var invalidSelectionMess = "{/literal}{#str_InvalidSelectionError#}{literal}".replace('^0', '{/literal}{#str_TitleColumns#}{literal}'.toLowerCase());

			Ext.MessageBox.show(
			{
				title: "{/literal}{#str_TitleError#}{literal}",
				msg: invalidSelectionMess,
				buttons: Ext.MessageBox.OK,
				icon: Ext.MessageBox.WARNING
			});
		}
		else
		{
			data['measurementunit'] = Ext.getCmp('measurementunit').getValue();
			dataJSON = JSON.stringify(data);

			var fp = Ext.getCmp('preferencesForm');
			var form = fp.getForm();

			var params = [];
			params['data'] = dataJSON;
			params['ref'] = '{/literal}{$ref}{literal}';
			
			Ext.taopix.formPanelPost(fp, form, params, 'index.php?fsaction=AdminProduction.updatePreferences', "{/literal}{#str_MessageSaving#}{literal}", onPreferencesCallback);

			measurementunit = Ext.getCmp('measurementunit').getValue();
			preferencesWindowExists = false; 
			prefData = dataJSON;
			gDialogObj.close();
		}
	};
	
	var cancelFunction = function(btn,ev)
	{ 
		preferencesWindowExists = false; 
		gDialogObj.close();
	};

	var container = new Ext.form.FieldSet({
		xtype: 'fieldset',
		id: 'preferenceFieldset',
		title: "{/literal}{#str_TitleColumns#}{literal}",
		items: 
			preferenceCheckboxes
		,
		layout: 'table',
		defaults: {
			style: 'margin: 4px 0px 2px 0px; text-align: left;'
		},
		layoutConfig: {
			columns: 2
		}
	});

	var measurementPreference = new Ext.form.ComboBox({
		id: 'measurementunit',
		name: 'measurementunit',
		mode: 'local',
		editable: false,
		forceSelection: true,
		selectOnFocus: true,
		triggerAction: 'all',
		fieldLabel: "{/literal}{#str_PrefsMeasurementsLabel#}{literal}",
		store: new Ext.data.ArrayStore({
			id: 0,
			fields: ['id', 'name'],
			data:
			[
				[TPX_COORDINATE_SCALE_INCHES, "{/literal}{#str_MeasurementUnitInches#}{literal}"],
				[TPX_COORDINATE_SCALE_MILLIMETRES, "{/literal}{#str_MeasurementUnitMillimetres#}{literal}"]
			]
		}),
		valueField: 'id',
		value: measurementunit,
		displayField: 'name',
		width: 130,
		useID: true,
		post: true
	});

	var preferencesFormTabPanelObj = new Ext.FormPanel({
        id: 'preferencesForm',
		header: false,
        frame: true,
        layout: 'form',
		autoWidth: true,
		bodyBorder: false,
		border: false,
        items: [ 
			container, measurementPreference
		]
	});

	/* create modal window for add and edit */
	gDialogObj = new Ext.Window({
		id: 'dialog',
		closable:false,
		title: "{/literal}{#str_SectionPreferences#}{literal}",
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		height: 520,
		width: 550,
		items: preferencesFormTabPanelObj,
		buttons:
		[
			{ text: '{/literal}{#str_ButtonCancel#}{literal}', handler: cancelFunction},
			{ text: "{/literal}{#str_ButtonOk#}{literal}", handler: okFunction, cls: 'x-btn-right' }
		]
	});

	measurementPreference.setValue(measurementunit);
	var mainPanel = Ext.getCmp('dialog');
	mainPanel.show();	
}

{/literal}