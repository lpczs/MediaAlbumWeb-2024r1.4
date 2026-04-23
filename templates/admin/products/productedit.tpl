{literal}

function initialize(pParams)
{
    var usedefault_txt = "{/literal}{#str_LabelUseDefault#}{literal}";
    var imagescalingbefore_txt = "{/literal}{#str_LabelEnableImageScalingBefore#}{literal}";
    var imagescalingbeforeenabled_txt = "{/literal}{#str_LabelEnableImageScalingBefore#}{literal}";
    var maxmegapixels_txt = "{/literal}{#str_LabelMaxMegaPixels#}{literal}";
    var imagescalingbeforefield_txt ="{/literal}{#str_LabelImageScalingBefore#}{literal}";
    var usedefaultimagescalingbeforeVal = ("{/literal}{$usedefaultimagescalingbefore}{literal}" == '1') ? true : false;
    var imagescalingBeforeVal = "{/literal}{$imagescalingbefore}{literal}";
    var imagescalingBeforeEnabledVal = ("{/literal}{$imagescalingbeforeenabled}{literal}" == '1') ? true : false;

	function forceAlphaNumeric()
	{
		var code = Ext.getCmp('skucode').getValue();
    	code = code.replace(/[^A-Z_0-9a-z-.\-]+/g, "");
    	Ext.getCmp('skucode').setValue(code);
	}
	
	function validate(value, allowDecimal, allowNegative)
	{
		if (! isNumeric(value, true, false))
		{
			valid = false;
		}
		else
		{
			valid = true;
		}
		return valid;
	}
	
	function editsaveHandler(btn, ev)
	{
		var selectID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('productgrid'));
		var submitURL = 'index.php?fsaction=AdminProducts.edit&ref={/literal}{$ref}{literal}&id=' + selectID;
		var fp = Ext.getCmp('productForm'), form = fp.getForm();
		
		var paramArray = new Object();
		paramArray['isactive'] = '';
		paramArray['id'] = selectID;
		paramArray['previewupdate'] = gLogoUpdate;
		paramArray['previewremove'] = gLogoRemove;

        {/literal}{if $allowimagescalingbefore && $producttype == 2}{literal}

            if (Ext.getCmp('imagescalingbeforeenabled').checked)
            {
                paramArray['usedefaultimagescalingbefore'] = '0';
                paramArray['imagescalingbeforeenabled'] = '1';
                paramArray['imagescalingbefore'] = Ext.getCmp('imagescalingbefore').getValue();
            }
            else if (Ext.getCmp('usedefaultimagescalingbefore').checked)
            {
                paramArray['usedefaultimagescalingbefore'] = '1';
                paramArray['imagescalingbeforeenabled'] = '0';
                paramArray['imagescalingbefore'] = '0.00';
            }
            else
            {
                paramArray['usedefaultimagescalingbefore'] = Ext.getCmp('usedefaultimagescalingbefore').checked ? '1' : '0';
                paramArray['imagescalingbeforeenabled'] = Ext.getCmp('imagescalingbeforeenabled').checked ? '1': '0';
                paramArray['imagescalingbefore'] = Ext.getCmp('imagescalingbefore').getValue();
            }
        {/literal}{else}{literal}
            paramArray['usedefaultimagescalingbefore'] = usedefaultimagescalingbeforeVal ? '1' : '0';
            paramArray['imagescalingbeforeenabled'] = imagescalingBeforeEnabledVal ? '1' : '0';
            paramArray['imagescalingbefore'] = imagescalingBeforeVal;
        {/literal}{/if}{literal}

		paramArray['retroprints'] = Ext.getCmp('retroprints').getValue();

		if (Ext.getCmp('isactive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}
		
		if (Ext.getCmp('cancreatenewprojects').checked)
		{
			paramArray['cancreatenewprojects'] = '1';
		}
		else
		{
			paramArray['cancreatenewprojects'] = '0';
		}
		
		if (Ext.getCmp('previewautoflip').checked)
		{
			paramArray['previewautoflip'] = '1';
		}
		else
		{
			paramArray['previewautoflip'] = '0';
		}
		
		if (Ext.getCmp('previewthumbnails').checked)
		{
			paramArray['previewthumbnails'] = '1';
		}
		else
		{
			paramArray['previewthumbnails'] = '0';
		}
		
		if (Ext.getCmp('previewthumbnailsview').checked)
		{
			paramArray['previewthumbnailsview'] = '1';
		}
		else
		{
			paramArray['previewthumbnailsview'] = '0';
		}

		/* Photo print price exception */
		{/literal}{if $producttype == 2}{literal}
		if (Ext.getCmp('pricetransformationstage').getValue())
		{
			paramArray['pricetransformationstage'] = Ext.getCmp('pricetransformationstage').getValue().inputValue;
		}
		{/literal}{/if}{literal}

		paramArray['fontlisttype'] = '';
		paramArray['fontlist'] = '';

		{/literal}{if $hasonlinedesigner == 1}{literal}
		// Font list options
		paramArray['fontlisttype'] = Ext.getCmp('fontlisttype').getValue().value;
		paramArray['fontlist'] = Ext.getCmp('fontlist').getValue();
		{/literal}{/if}{literal}

		Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, '{/literal}{#str_MessageSaving#}{literal}', saveCallback);
	}
	
	function saveCallback(pUpdated, pActionForm, pActionData)
	{	
		if (pUpdated)
		{
			var gridObj = gMainWindowObj.findById('productgrid');
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
	
	var gLogoUpdate = 0;
	var gLogoRemove = 0;
	
	function validateFileExtension(fileName)
	{
		var exp = /^.*\.(jpg|jpeg|png|gif)$/;
		return exp.test(fileName);
	}
	
	function onRemoveLogo()
	{
		gLogoRemove = 1; 
		gLogoUpdate = 0; 
		Ext.getDom('previewimage').src = 'images/admin/nopreview.gif';
		Ext.getCmp('resetLogoButton').enable();
	}

	function onResetLogo()
	{
		gLogoRemove = 0; 
		gLogoUpdate = 0; 
		var d = new Date();
		Ext.getDom('previewimage').src = '{/literal}{$productpreview}{literal}';		
		Ext.getCmp('resetLogoButton').disable();
	}
	
	function onUploadLogo()
	{
		var theForm = Ext.getCmp('uploadform').getForm();
		theForm.submit({
			params: {code: Ext.getCmp('code').value},
			scope: this,
			success: function(form,action)
			{
				Ext.getCmp('uploaddialog').close();
				var d = new Date();
				Ext.getDom('previewimage').src = './?fsaction=AdminProducts.getPreviewImage&no=1&id=' + Ext.getCmp('code').value + '&ref={/literal}{$ref}{literal}&tmp=1&version=' + d.getTime();
				if (action.result.msg != '')
				{
					Ext.MessageBox.show({ title: "{/literal}{#str_TitleWarning#}{literal}", msg: action.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				}
				gLogoUpdate = 1;
				gLogoRemove = 0;
				Ext.getCmp('resetLogoButton').enable();
			},
			failure: function(form, action) 
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorCompulsoryFields#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
			},
			waitMsg: {/literal}"{#str_AlertUploading#}"{literal}
		});
	}
	
	function uploadLogo(btn, ev)
	{
		var fileName = Ext.getDom('preview').value.toLowerCase();
		if (!validateFileExtension(fileName)) 
		{
			if (fileName == '')
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleWarning#}{literal}", msg: "{/literal}{#str_MessageLogoSelectImage#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				return;
			}
			else
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleWarning#}{literal}", msg: "{/literal}{#str_MessageLogoFileTypes#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
				return;
			}
		}
		onUploadLogo();
	}
	

 function createUploadDialog()
 {
	 var uploadFormPanelObj = new Ext.FormPanel({
			id: 'uploadform',
			frame: true,
			autoWidth: true,
			autoHeight: true,
			layout: 'column',
			bodyBorder: false,
			border: false,
			url: './?fsaction=AdminProducts.uploadPreviewImage&ref={/literal}{$ref}{literal}',
			method: 'POST',
			fileUpload: true,
			baseParams: {csrf_token: Ext.taopix.getCSRFToken()},
			items: 
			[	    
				{
					xtype: 'hidden',
					id: 'clear',
					value: 0
				},
				{
					xtype: 'box',
			    	autoEl: {
						tag: 'div',
						html: "{/literal}{$previewImageText}{literal}"
					},
					style:'padding-bottom:5px'
				},
				{
					  xtype: 'spacer',
					  height: 5
				},
				{
					xtype: 'textfield',
					hideLabel: true,
					name: 'preview',
					id: 'preview',
					inputType: 'file'
				}
			],
			buttons:
			[
				{	
					text: "{/literal}{#str_ButtonCancel#}{literal}",
					id: 'cancelUpload',
					handler: function(){ Ext.getCmp('uploaddialog').close(); }
				},
				{
					text: "{/literal}{#str_ButtonUpdate#}{literal}",
					handler: uploadLogo
				}
			]
		});
	 
	 var gUploadObj = new Ext.Window({
		id: 'uploaddialog',
		title: "{/literal}{#str_TitleSelectPreviewImage#}{literal}",
		closable:false,
		plain:true,
		modal:true,
		draggable:true,
		resizable:false,
		bodyBorder: false,
		layout: 'fit',
		autoHeight:true,
		width: 490,
		items: uploadFormPanelObj
	});
	
	gUploadObj.show();
 }		

	var retroPrints =
	{
		xtype: 'fieldset',
		title: "{/literal}{#str_TitleShowAsPrints#}{literal}",
		autoHeight: true,
		defaultType: 'textfield',
		labelWidth: 293,
		items:
		[
			new Ext.Container(
			{
				html: "{/literal}{#str_MessageShowAsPrints#}{literal}",
				style: { padding: '10px 0px 5px 0px' }
			}),
			{
				xtype : 'container',
				layout : 'form',
				width: 500,
				items : [
					new Ext.form.ComboBox({
						fieldLabel: "{/literal}{#str_LabelShowAsPrints#}{literal}",
						id: 'retroprints',
						name: 'retroprints',
						mode: 'local',
						editable: false,
						forceSelection: true,
						width: 200,
						useID: true,
						post: true,
						valueField: 'id',
						displayField: 'name',
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data: [
								[0, "{/literal}{#str_LabelOff#}{literal}"],
								[1, "{/literal}{#str_LabelOn#}{literal}"]
							]
						}),
						triggerAction: 'all',
						value: "{/literal}{$retroprints}{literal}"
					})
				]
			},
			{
				xtype: 'numberfield',
				id: 'minimumprintsperproject',
				name: 'minimumprintsperproject',
				fieldLabel: "{/literal}{#str_LabelPriceMinimumNumberOfPrintsPerProject#}{literal}",
				width: 100,
				minValue: 1,
				value: '{/literal}{$minimumprintsperproject}{literal}',
				validateOnBlur: true,
				post: true,
				allowBlank: false,
				allowNegative: false,
				allowDecimals: false,
				readOnly:false
			}
		]
	};

	{/literal}{if $hasonlinedesigner == 1}{literal}
	var fontList =
	{
		xtype: 'fieldset',
		title: "{/literal}{#str_TitleFontSection#}{literal}",
		autoHeight: true,
		defaultType: 'textfield',
		labelWidth: 293,
		items:
		[
			{
				xtype: 'radiogroup',
				id: 'fontlisttype',
				name: 'fontlisttype',
				fieldLabel: "{/literal}{#str_LabelFontListSelection#}{literal}",
				title: '',
				layout: 'form',
				columns: 1,
				items: [
					new Ext.form.Radio(
					{
						name: 'fontlistselection',
						value: -1,
						id: 'usedefaultfonts',
						checked: {/literal}{if -1 === $selectedfontlist}1{else}0{/if}{literal},
						boxLabel: "{/literal}{#str_LabelUseDefault#}{literal}"
					}),
					new Ext.form.Radio(
					{
						name: 'fontlistselection',
						value: 1,
						id: 'useallfonts',
						checked: {/literal}{if null === $selectedfontlist}1{else}0{/if}{literal},
						boxLabel: "{/literal}{#str_LabelAll#}{literal}"
					}),
					new Ext.form.Radio(
					{
						name: 'fontlistselection',
						value: 0,
						id: 'useselectedfonts',
						checked: {/literal}{if !is_null($selectedfontlist) && -1 !== $selectedfontlist}1{else}0{/if}{literal},
						boxLabel: "{/literal}{#str_TitleFontList#}{literal}"
					})
				],
				listeners: {
					change: function() {
						var list = Ext.getCmp('fontlist');
						if (-1 === this.getValue().value || 1 === this.getValue().value) {
							list.setDisabled(true);
						} else {
							list.setDisabled(false).setValue({/literal}{if !is_null($selectedfontlist) && -1 !== $selectedfontlist}{$selectedfontlist}{else}null{/if}{literal});
						}
					}
				}
			},
			{
				xtype : 'container',
				layout : 'form',
				width: 500,
				items : new Ext.form.ComboBox({
					id: 'fontlist',
					name: 'fontlist',
					mode: 'local',
					disabled: {/literal}{if is_null($selectedfontlist) || -1 === $selectedfontlist}true{else}false{/if}{literal},
					value: {/literal}{if is_null($selectedfontlist) || -1 === $selectedfontlist}null{else}{$selectedfontlist}{/if}{literal},
					editable: false,
					forceSelection: true,
					width: 200,
					valueField: 'fontlist_id',
					displayField: 'fontlist_name',
					useID: true,
					hideLabel: false,
					allowBlank: true,
					store: new Ext.data.ArrayStore({
						id: 'fontlistvalues',
						fields: ['fontlist_id', 'fontlist_name'],
						data: {/literal}{$fontlists}{literal}
					}),
					triggerAction: 'all',
					validationEvent: false
				})
			}
		]
	};
	{/literal}{/if}{literal}

	{/literal}{if $producttype == 2}{literal}
	var imageScalingPanel = {
		xtype:'fieldset',
		columnWidth: 0.5,
		title: "{/literal}{#str_LabelImageScaling#}{literal}",
		collapsible: false,
		autoHeight: true,
		defaultType: 'textfield',
		items: [
			new Ext.form.Checkbox({
				name: 'usedefaultimagescalingbefore',
				id: 'usedefaultimagescalingbefore',
				checked: usedefaultimagescalingbeforeVal,
				boxLabel: usedefault_txt,
				fieldLabel: imagescalingbeforefield_txt,
				disabled: imagescalingBeforeEnabledVal,
				listeners:{
					check: function() {
						Ext.getCmp('imagescalingbeforeenabled').setDisabled(Ext.getCmp('usedefaultimagescalingbefore').checked);
						if(Ext.getCmp('usedefaultimagescalingbefore').checked) {
							Ext.getCmp('imagescalingbeforeenabled').setValue(false);
						}
					}
				}
			}),
			new Ext.form.Checkbox({
				name: 'imagescalingbeforeenabled',
				id: 'imagescalingbeforeenabled',
				checked: imagescalingBeforeEnabledVal,
				boxLabel: imagescalingbefore_txt,
				disabled: usedefaultimagescalingbeforeVal,
				listeners: {
					check: function(){
						Ext.getCmp('imagescalingbefore').setDisabled(!Ext.getCmp('imagescalingbeforeenabled').checked || Ext.getCmp('usedefaultimagescalingbefore').checked);
						Ext.getCmp('usedefaultimagescalingbefore').setDisabled(Ext.getCmp('imagescalingbeforeenabled').checked);
					}
				}
			}),
			{
				id: 'imagescalingbefore',
				name: 'imagescalingbefore',
				xtype: 'numberfield',
				value: imagescalingBeforeVal,
				disabled: !imagescalingBeforeEnabledVal,
				post: true,
				validateOnBlur: true,
				maxValue: 999.99,
				minValue: 2.00,
				decimalPrecision: 2,
				forcePrecision: true,
				width: 50,
				fieldLabel: maxmegapixels_txt,
				validator: function(v)
				{
					v = String(v).replace(this.decimalSeparator, ".");
					return ((v <= 999.99) && (v >= 2));
				},
				setValue: function(v)
				{
					if (v < 2)
					{
						v = 2;
					}
					else if (v > 999.99)
					{
						v = 999.99;
					}

					var dp = this.decimalPrecision;

					if (dp < 0 || !this.allowDecimals)
					{
						dp = 0;
					}

					v = this.fixPrecision(v);
					v = Ext.isNumber(v) ? v : parseFloat(String(v).replace(this.decimalSeparator, "."));
					v = isNaN(v) ? '' : String(v.toFixed(dp)).replace(".", this.decimalSeparator);

					return Ext.form.NumberField.superclass.setValue.call(this, v);
				}
			}
		]
	};
	{/literal}{/if}{literal}

	var d = new Date();
	var gDate = d.getTime();
	
	var customJobTicketContainer = {
            xtype: 'panel',
            layout: 'form',
            style:'border:1px solid #B5B8C8',
            bodyStyle:'padding:5px 5px 5px 5px',
            labelWidth: 100,
            width: 520,
            fieldLabel: '{/literal}{#str_LabelJobTicketFields#}{literal}',
            items: [
                    	{ xtype: 'textfield', id: 'jobticket1name', name: 'jobticket1name', width: 200, maxLength: 100, value: '{/literal}{$jobticket1name}{literal}', fieldLabel: '{/literal}{#str_LabelName#}{literal}', post: true},
                    	{ xtype: 'textfield', id: 'jobticket1value', name: 'jobticket1value', width: 400, maxLength: 200, value: '{/literal}{$jobticket1value}{literal}', fieldLabel: '{/literal}{#str_LabelInformation#}{literal}', post: true},
                    	{ xtype: 'textfield', id: 'jobticket2name', name: 'jobticket2name', width: 200, maxLength: 100, value: '{/literal}{$jobticket2name}{literal}', fieldLabel: '{/literal}{#str_LabelName#}{literal}', post: true},
                    	{ xtype: 'textfield', id: 'jobticket2value', name: 'jobticket2value', width: 400, maxLength: 200, value: '{/literal}{$jobticket2value}{literal}', fieldLabel: '{/literal}{#str_LabelInformation#}{literal}', post: true},
                    	{ xtype: 'textfield', id: 'jobticket3name', name: 'jobticket3name', width: 200, maxLength: 100, value: '{/literal}{$jobticket3name}{literal}', fieldLabel: '{/literal}{#str_LabelName#}{literal}', post: true},
                    	{ xtype: 'textfield', id: 'jobticket3value', name: 'jobticket3value', width: 400, maxLength: 200, value: '{/literal}{$jobticket3value}{literal}', fieldLabel: '{/literal}{#str_LabelInformation#}{literal}', post: true},
                    	{ xtype: 'textfield', id: 'jobticket4name', name: 'jobticket4name', width: 200, maxLength: 100, maxLength: 100, value: '{/literal}{$jobticket4name}{literal}', fieldLabel: '{/literal}{#str_LabelName#}{literal}', post: true},
                    	{ xtype: 'textfield', id: 'jobticket4value', name: 'jobticket4value', width: 400,maxLength: 200, value: '{/literal}{$jobticket4value}{literal}', fieldLabel: '{/literal}{#str_LabelInformation#}{literal}', post: true},
                    	{ xtype: 'textfield', id: 'jobticket5name', name: 'jobticket5name', width: 200, maxLength: 100, value: '{/literal}{$jobticket5name}{literal}', fieldLabel: '{/literal}{#str_LabelName#}{literal}', post: true},
                    	{ xtype: 'textfield', id: 'jobticket5value', name: 'jobticket5value', width: 400,maxLength: 200, value: '{/literal}{$jobticket5value}{literal}', fieldLabel: '{/literal}{#str_LabelInformation#}{literal}', post: true}
                 ]
        };
	
	var taxLevelCombo = new Ext.form.ComboBox({
		id: 'taxlevel',
		name: 'taxlevel',
		mode: 'local',
		width:250,
		editable: false,
		forceSelection: true,
		selectOnFocus: true,
		allowBlank: false,
		triggerAction: 'all',
		fieldLabel: "{/literal}{#str_LabelTaxLevel#}{literal}",
		store: new Ext.data.ArrayStore({
			id: 'taxlevelstore',
			fields: ['id', 'name'],
			data: [
				{/literal}
				{section name=index loop=$taxlevellist}
				{if $smarty.section.index.last}
					["{$taxlevellist[index].id}", "{$taxlevellist[index].name}"]
				{else}
					["{$taxlevellist[index].id}", "{$taxlevellist[index].name}"],
				{/if}
				{/section}
				{literal}
			]
		}),
		valueField: 'id',
		displayField: 'name',
		value: '{/literal}{$taxlevel}{literal}',
		useID: true,
		post: true
	});
	
	var dialogFormPanelObj = new Ext.FormPanel({
		id: 'productForm',
        labelAlign: 'left',
        labelWidth:150,
        autoHeight: true,
        frame:true,
        bodyStyle:'padding:3px 5px 0;',
        items: [{ 
			xtype: 'panel', id: 'topPanel', layout: 'column',style:'background:#c9d8ed; padding: 3px 0; border:1px solid #8ca9cf', columns: 2, plain:true, 
			bodyBorder: false, border: false, 	defaults:{labelWidth: 70}, bodyStyle:'padding:5px 5px 0; border-top: 0px',
			items: 
			[
				new Ext.Container({ layout: 'form', defaults:{xtype: 'textfield', width: 195}, width:290,
				items: 
					{
					 xtype: 'textfield', id: 'code', name: 'code', width: 200, disabled:true, maxLength: 50, value: '{/literal}{$code}{literal}', allowBlank:false, fieldLabel: '{/literal}{#str_LabelCode#}{literal}', readOnly:true, post: true
					}
				})
				
				{/literal}{if $optionms}{literal}
					,
					new Ext.Container({ layout: 'form', defaults:{xtype: 'textfield', width: 225}, width:350, items: { xtype: 'textfield', id: 'company', disabled:true, fieldLabel: "{/literal}{#str_LabelCompany#}{literal}", readOnly:true, name: 'company', value: '{/literal}{$companycode}{literal}',  post: true} })
				{/literal}{/if}{literal}
			]
		},
		
		{ /* tabpanel */
			xtype: 'tabpanel',
			id: 'maintabpanel',
			deferredRender: false,
			activeTab: 0,
			height: 510,
			shadow: true,
			plain:true,
			bodyBorder: false,
			layoutOnTabChange: true,
			border: true,
			style: 'margin-top: 7px',
			bodyStyle:'border-right: 1px solid #96bde7;',		
			defaults:{frame: false, autoScroll: true, hideMode:'offsets', layout: 'form', labelWidth: 140,  style:'padding:10px; background-color: #eaf0f8;'},
			items: 
			[
				{
					title: "{/literal}{#str_LabelSettings#}{literal}",
					id: 'settingsTab',
					items: 
					[
						{ xtype: 'textfield', id: 'skucode', name: 'skucode', width: 250, maxLength: 50, value: '{/literal}{$skucode}{literal}', fieldLabel: '{/literal}{#str_LabelSKUCode#}{literal}', listeners:{	blur:{	fn: forceAlphaNumeric }	}, post: true},
						{ xtype: 'textfield', id: 'name', name: 'name', width: 250, disabled:true, value: '{/literal}{$name}{literal}', allowBlank:false, fieldLabel: '{/literal}{#str_LabelName#}{literal}', readOnly:true, post: true},
						{ xtype: 'textfield', id: 'category', name: 'category', disabled:true, width: 250, value: '{/literal}{$category}{literal}', allowBlank:false, fieldLabel: '{/literal}{#str_LabelCategory#}{literal}', readOnly:true, post: true},
						taxLevelCombo,
						{ xtype: 'textfield', id: 'cost', name: 'cost', width: 100, value: '{/literal}{$cost}{literal}', allowBlank:false, validator: function(v){ return validate(v,true,false);  }, fieldLabel: '{/literal}{#str_LabelCost#}{literal}', readOnly:false, post: true},
						{ xtype: 'textfield', id: 'weight', name: 'weight', width: 100, value: '{/literal}{$weight}{literal}', allowBlank:false, validator: function(v){ return validate(v,true,false); }, fieldLabel: '{/literal}{#str_LabelShippingWeight#}{literal}', readOnly:false, post: true},
						customJobTicketContainer,
						{ 
							xtype: 'checkbox', 
							id: 'cancreatenewprojects',
							name: 'cancreatenewprojects',
							boxLabel: '{/literal}{#str_LabelCanCreateNewProjects#}{literal}',
							{/literal}{if $createnewprojectschecked == 1}{literal}
								checked: true
							{/literal}{else}{literal}
								checked: false
							{/literal}{/if}{literal}
						}
					]
				},
				{
					title: "{/literal}{#str_ButtonPreview#}{literal}",
					id: 'previewTab',
					layout: 'form',
					listeners: { 'beforeshow': function(){ Ext.getCmp('maintabpanel').doLayout(); }},
					items: 
					[
						{
        					xtype:'fieldset',
        					columnWidth: 0.5,
					        title: "{/literal}{#str_LabelProductPreviewIcon#}{literal}",
					        collapsible: false,
					        autoHeight:true,
					        defaultType: 'textfield',
					        items :[
					        	{
								    xtype: 'buttongroup',
								    frame: false,
								    columns: 5,
								    items: 
									[
										{
											text: "{/literal}{#str_ButtonUpdatePreviewImage#}{literal}",
											handler: function() 
											{
												createUploadDialog();												
												Ext.getDom('preview').value = '';
											}
										},
										{
											xtype: 'spacer',
											width: 5
										},
										{
											text: "{/literal}{#str_ButtonRemovePreviewImage#}{literal}",
											handler: onRemoveLogo
										},
										{
											xtype: 'spacer',
											width: 5
										},
										{
											text: "{/literal}{#str_ButtonResetPreviewImage#}{literal}",
											id: 'resetLogoButton',
											disabled: true,
											handler: onResetLogo
										}
									]
								},
								{
									xtype: 'spacer',
									height: 5
								},
								{
									xtype: 'box',
								   	autoEl: 
								   	{
										tag: 'div',  
								        html: '<img style="border: 1px solid;" id="previewimage" name="previewimage" src="{/literal}{$productpreview}{literal}">'
								    }
								}
					        ]
					    },
					    
					    {
        					xtype:'fieldset',
        					columnWidth: 0.5,
					        title: "{/literal}{#str_LabelPageTurningPreview#}{literal}",
					        collapsible: false,
					        autoHeight:true,
					        defaultType: 'textfield',
					        items :[
					        	new Ext.form.ComboBox({id: 'previewtype', name: 'previewtype', hiddenName:'previewType_hn', width:270, allowBlank: false,
									forceSelection: true, editable: false, mode: 'local', 	hiddenId:'previewType_hi', validationEvent:false,
									store: new Ext.data.ArrayStore({ id: 0, fields: ['previewType_id', 'previewType_name'], data: [[1, '{/literal}{#str_LabelHorizontalPageTurning#}{literal}'], [2, '{/literal}{#str_LabelVerticalPageTurning#}{literal}'], [3, '{/literal}{#str_LabelSlideshow#}{literal}']] }), 
									valueField: 'previewType_id', displayField: 'previewType_name', useID: true, post: true, fieldLabel: "{/literal}{#str_LabelPreviewType#}{literal}", triggerAction: 'all',
									value: '{/literal}{$previewtype}{literal}'
								}),
					        	new Ext.form.ComboBox({id: 'previewcovertype', name: 'previewcovertype', hiddenName:'coverType_hn', width:270, allowBlank: false,
									forceSelection: true, editable: false, mode: 'local', 	hiddenId:'coverType_hi', validationEvent:false,
									store: new Ext.data.ArrayStore({ id: 0, fields: ['coverType_id', 'coverType_name'], data: [[0, '{/literal}{#str_LabelCoverHard#}{literal}'], [1, '{/literal}{#str_LabelCoverSoft#}{literal}']] }), 
									valueField: 'coverType_id', displayField: 'coverType_name', useID: true, post: true, fieldLabel: "{/literal}{#str_LabelCoverType#}{literal}", triggerAction: 'all',
									value: '{/literal}{$previewcovertype}{literal}'
								}),
						    	{ 
									xtype: 'checkbox', 
									id: 'previewautoflip',
									name: 'previewautoflip',
									boxLabel: '{/literal}{#str_LabelAutoFlip#}{literal}',
									hideLabel: true,
									{/literal}{if $previewautoflip == 1}{literal}
										checked: true
									{/literal}{else}{literal}
										checked: false
									{/literal}{/if}{literal}
								}, 
								{ 
									xtype: 'checkbox', 
									id: 'previewthumbnails',
									name: 'previewthumbnails',
									hideLabel: true,
									boxLabel: "{/literal}{#str_LabelThumbnailsVisible#}{literal}",
									{/literal}{if $previewthumbnails == 1}{literal}
										checked: true,
									{/literal}{else}{literal}
										checked: false,
									{/literal}{/if}{literal}
									listeners: { check: refreshThumbnailsDisplay }
								},
								{
									xtype: 'checkbox',
									id: 'previewthumbnailsview',
									name: 'previewthumbnailsview',
									hideLabel: true,
									boxLabel: "{/literal}{#str_LabelThumbnailsDisplayedAtTheBottom#}{literal}",
									{/literal}{if $previewthumbnailsview == 1}{literal}
										checked: true
									{/literal}{else}{literal}
										checked: false
									{/literal}{/if}{literal}
								}
					        ]
					    }	
					]
				}
				{/literal}{if $producttype == 2}{literal}
				,{
					title: "{/literal}{#str_LabelPhotoPrintsOptions#}{literal}",
					layout: 'form',
					id: 'photoPrintTab',
					labelWidth: 240,
					items:
					[
						{/literal}{if $allowimagescalingbefore}{literal}
                        imageScalingPanel,
                        {/literal}{/if}{literal}
						{
							xtype:'fieldset',
							columnWidth: 0.5,
							title: "{/literal}{#str_LabelPricingOptions#}{literal}",
							collapsible: false,
							autoHeight:true,
							defaultType: 'textfield',
							items :[
								new Ext.form.ComboBox({
									id: 'productoptions',
									name: 'productoptions',
									width: 250,
									fieldLabel: "{/literal}{#str_LabelPricingModel#}{literal}",
									mode: 'local',
									editable: false,
									forceSelection: true,
									store: new Ext.data.ArrayStore({
										id: 'sppos',
										fields: ['id', 'name'],
										data: [
											[1, "{/literal}{#str_LabelProductOptionPricingPerPicture#}{literal}"],
											[2, "{/literal}{#str_LabelProductOptionPricingPerComponentSubComponent#}{literal}"]
										]
									}),
									selectOnFocus: true,
									triggerAction: 'all',
									valueField: 'id',
									displayField: 'name',
									useID: true,
									allowBlank: false,
									value: {/literal}{$productoptions}{literal},
									post: true
								}),
								new Ext.form.RadioGroup({
									id: 'pricetransformationstage',
									name: 'pricetransformationstage',
									width: 250,
									fieldLabel: "{/literal}{#str_LabelCalculationMethod#}{literal}",
									columns: 2,
									layout:'row',
									items: [
										{boxLabel: "{/literal}{#str_LabelPriceTransformationStagePre#}{literal}", name: 'pricetransformationstage', inputValue: 1},
										{boxLabel: "{/literal}{#str_LabelPriceTransformationStagePost#}{literal}", name: 'pricetransformationstage', inputValue: 2},
									],
									value: {/literal}{$pricetransformationstage}{literal},
								})
							]
						}
						/* Option only displays if online is active*/
						{/literal}{if $hasonlinedesigner == 1}{literal}
						,
						{
							xtype:'fieldset',
							columnWidth: 0.5,
							title: "{/literal}{#str_LabelOnlineOptions#}{literal}",
							collapsible: false,
							autoHeight:true,
							defaultType: 'textfield',
							items :[
							{
								xtype: 'numberfield',
								id: 'minimumprintsperproject',
								name: 'minimumprintsperproject',
								fieldLabel: "{/literal}{#str_LabelPriceMinimumNumberOfPrintsPerProject#}{literal}",
								width: 100,
								minValue: 1,
								value: '{/literal}{$minimumprintsperproject}{literal}',
								validateOnBlur: true,
								post: true,
								allowBlank: false,
								allowNegative: false,
								allowDecimals: false,
								readOnly:false
							}]
						}
						{/literal}{/if}{literal}
					]
				}
				{/literal}{/if}{literal}
                {/literal}{if $hasonlinedesigner == 1}{literal}
                ,
                {
                    title: "{/literal}{#str_TitleOnlineDesignerSettings#}{literal}",
                    layout: 'form',
                    id: 'onlineDesignerSettingsTab',
                    labelWidth: 280,
                    items: [
						fontList
						{/literal}{if ($allowretroprints) && ($producttype == 0) && ($hasonlinedesigner == 1)}{literal}
						, retroPrints
						{/literal}{/if}{literal}
                    ]
                }
                {/literal}{/if}{literal}
			]
		}]
	});
		
	 gDialogObj = new Ext.Window({
			id: 'dialog',
		  	closable:false,
		  	title: "{/literal}{$title}{literal}",
		  	plain:true,
		  	modal:true,
		  	draggable:true,
		 	resizable:false,
		  	layout: 'fit',
		  	height: 'auto',
		  	width: 750,
		  	cls: 'left-right-buttons',
		  	items: dialogFormPanelObj,
		  	listeners: {
				'close': {   
					fn: function(){
		                productEditWindowExists = false;
					}
				}
			},
		  	buttons: 
			[
				new Ext.form.Checkbox({
					id: 'isactive',
					name: 'isactive',
					boxLabel: "{/literal}{#str_LabelActive#}{literal}",
					post: true,
					cls: 'x-btn-left', 
							ctCls: 'width_100',
							{/literal}{if $activechecked == 1}{literal}
						checked: true
					{/literal}{else}{literal}
						checked: false
					{/literal}{/if}{literal}
				}),
				{ text: "{/literal}{#str_ButtonCancel#}{literal}", handler: function(){ gDialogObj.close();} },
				{ text: "{/literal}{#str_ButtonAdd#}{literal}", id: 'addEditButton',
				handler: editsaveHandler,
				text: "{/literal}{#str_ButtonUpdate#}{literal}"
				}
			]
		});

		refreshThumbnailsDisplay();
		var mainPanel = Ext.getCmp('dialog');
		mainPanel.show();
}
		
function ArrayIndexOf(source, v) 
{
    for (var i = 0, l = source.length; i < l; i++) 
    {
        if (source[i] == v) 
        {
            return i;
        }
    }
    return -1;
}

function refreshThumbnailsDisplay()
{
	var previewThumbnailsVisible = Ext.getCmp('previewthumbnails');
	var previewthumbnailsatbottom = Ext.getCmp('previewthumbnailsview');

	if (previewThumbnailsVisible.checked)
	{
		previewthumbnailsatbottom.enable();
	}
	else
	{
		previewthumbnailsatbottom.setValue(false);
		previewthumbnailsatbottom.disable();
	}
}

{/literal}
