{literal}

/**
* Re-centres and syncs the shadow of the dialog.
*/
function repositionDialog()
{
	Ext.getCmp('itemsDialog').center(); 
};

function initialize(pParams)
{	
	{/literal}{$languagecodesjavascript}{literal}
	{/literal}{$languagenamesjavascript}{literal}

	var defaultImage = 'images/admin/nopreview.gif';

  // List of item images to remove.
	var imagesToRemove = [];

  // List of images to remove when the type is changed.
  // This is so if an image is reset, the newly uploaded images will still be removed.
	var imagesToRemoveOnTypeChange = [];

	var getLocalisedData = function(populateLangs, gLocalizedNamesArray, gLocalizedCodesArray)
	{
		var langListStore = [], dataList = [];
		populateLangs = 1;
		
		for (var i =0; i < gAllLanguageCodesArray.length; i++)
		{
			var languageName = "";
			var languageCode = "";
			var languageNameIndex = ArrayIndexOf(gAllLanguageCodesArray, gLocalizedCodesArray[i]);
			if (languageNameIndex > -1)
			{
				languageName = gAllLanguageNamesArray[languageNameIndex];
				languageCode = gAllLanguageCodesArray[languageNameIndex];
			}
			if ((languageName) && (languageName!=undefined)) dataList.push([languageCode,languageName,gLocalizedNamesArray[i]]);
	
			if (populateLangs == 1)
			{
				if (ArrayIndexOf(gLocalizedCodesArray, gAllLanguageCodesArray[i]) == -1)
				{
					langListStore.push([gAllLanguageCodesArray[i],gAllLanguageNamesArray[i]]);
				}
			}
		}
		return {'langListStore': langListStore, 'dataList': dataList};
	};
	
	{/literal}{$localizedcodesjavascript}{literal}
	{/literal}{$localizednamesjavascript}{literal}
	
	var localisedData = getLocalisedData(1, gLocalizedNamesArray, gLocalizedCodesArray);
	var langListStore = localisedData.langListStore;
	var dataList = localisedData.dataList;
	
	var deleteImg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png';
	var addimg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/add.png';

	/**
	 * Formats an image path.
	 *
	 * @param string pImagePath Image path to format.
	 * @return string Formatted image path. 
	 */
	var formatImagePath = function(pImagePath)
	{
		return pImagePath.replace('[WEBROOT]', '');
	}

	/**
	 * Adds a timestamp to the given image URL.
	 * 
	 * @param string pSrc The image src to add the timestamp to.
	 * @return string The image src with the timestamp.
	 */
	var getImageSrc = function(pSrc)
	{
		return pSrc + '?t=' + new Date().getTime();
	};
	
	/* Items grid and functions */
	var itemsNameRenderer = function(value, p, record, rowIndex, colIndex, store)
	{
		var values = record.data.name.split('<p>');
		var resArray = [];
		
		for (var i = 0, value='', languageNameIndex; i < values.length; i++)
		{
			value = values[i].split(' ');
			if (value.length > 0)
			{
				languageNameIndex = ArrayIndexOf(gAllLanguageCodesArray, value.shift());
				if (languageNameIndex > -1)
				{
					languageName = gAllLanguageNamesArray[languageNameIndex];
				}
				resArray.push('<tr><td style="width: 100px">'+ languageName + '</td><td>' + value.join(' ')+'</td></tr>');
			}
		}
		
		return '<table>' + resArray.join('') + '</table>';
	};
	
	var itemSaveHandler = function()
	{
		var store = Ext.getCmp('itemsGrid').getStore();
		var fp = Ext.getCmp('itemsDialogForm'), form = fp.getForm();
		
		var codeField = Ext.getCmp('itemCode');
		var langItemsPanelField = Ext.getCmp('langItemsPanel');
		var itemImagePathField = Ext.getCmp('itemImagePath');
		var currentImagePath = '';
		var selectedItem = itemsCheckBoxSelectionModelObj.getSelected();

		if (typeof selectedItem !== 'undefined')
		{
			currentImagePath = selectedItem.data.image;
		}
		
		if (codeField.validate())
		{
			if (!langItemsPanelField.isValid())
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ExtJsErrorNoName#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
	    		return false;
			}
			
			var codeRecords = store.query('code', codeField.getValue(), false, true).items;
			var codeRecordsCount = codeRecords.length;
			
			if (itemsEditDialogObj.mode == 0) /* add */
			{
				if (codeRecordsCount > 0)
				{
					Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "Item with this code already exsists", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
	    			return false;
				}
				var newData = 
				{
					code: codeField.getValue().toUpperCase(),
					name: langItemsPanelField.convertTableToString(),
					image: itemImagePathField.getValue(),
					previousimage: ''
				};
				var r = new store.recordType(newData); 
				r.commit();
				store.add(r);
			}
			else /* edit */
			{
				codeRecords = codeRecords[0];
				codeRecords.data.name = langItemsPanelField.convertTableToString();
				codeRecords.data.image = itemImagePathField.getValue();

				if ((currentImagePath !== '') && (itemImagePathField.getValue() !== currentImagePath))
				{
					// Image has changed, mark it for deletion.
					codeRecords.data.previousimage = currentImagePath;
				}
			}
			
			store.commitChanges();
			Ext.getCmp('itemsGrid').getView().refresh();

      // Call sever to clear image path.
      clearImagePath(function()
      {
        itemsEditDialogObj.hide();
      });
		}

		return false;
	};
	
	var onItemAdd = function()
	{
		itemsEditDialogObj.show();
		
		itemsEditDialogObj.setTitle("{/literal}{#str_LabelNewItem#}{literal}");
		Ext.getCmp('updateItemButton').setText("{/literal}{#str_ButtonAdd#}{literal}");
		Ext.getCmp('itemCode').getEl().dom.removeAttribute('readOnly');		
		Ext.getCmp('itemCode').getEl().setStyle('background','#fff');
		itemsEditDialogObj.mode = 0;

		// Show/hide the image controls.
		toggleImageControls();

		if (Ext.getCmp('keywordType').getValue() === 'RADIOGROUP')
		{
			document.getElementById('previewimage').src = defaultImage;
		}
					
		Ext.getCmp('itemCode').setValue('');
		Ext.getCmp('itemImagePath').setValue('');
		Ext.getCmp('langItemsPanel').loadData({dataList: []});
        var gLocalizedCodesArray = [], gLocalizedNamesArray = [];
        localisedData = getLocalisedData(0, gLocalizedNamesArray, gLocalizedCodesArray);
		Ext.getCmp('langItemsPanel').getBottomToolbar().items.items[0].getStore().loadData(localisedData.langListStore);
		
		Ext.getCmp('itemCode').clearInvalid();
		Ext.getCmp('itemImagePath').clearInvalid();
		
		itemsEditDialogObj.syncShadow();
	};
	
	var onItemEdit = function()
	{
		itemsEditDialogObj.show();
		
		itemsEditDialogObj.setTitle('Edit Item');
		Ext.getCmp('updateItemButton').setText("{/literal}{#str_ButtonUpdate#}{literal}");
		
		Ext.getCmp('itemCode').getEl().dom.setAttribute('readOnly', true);
		Ext.getCmp('itemCode').getEl().setStyle('background','#c9d8ed');
		itemsEditDialogObj.mode = 1;
		
		// Show/hide the image controls.
		toggleImageControls();
		
		var store = Ext.getCmp('itemsGrid').getStore();
		var selected = itemsCheckBoxSelectionModelObj.getSelected();
		
		Ext.getCmp('itemCode').setValue(selected.data.code);
		Ext.getCmp('itemImagePath').setValue(selected.data.image);

		if (Ext.getCmp('keywordType').getValue() === 'RADIOGROUP')
		{
			if (selected.data.image !== '')
			{
				document.getElementById('previewimage').src = getImageSrc(formatImagePath(selected.data.image));
			}
			else
			{
				document.getElementById('previewimage').src = defaultImage;
			}
		}

		var values = selected.data.name.split('<p>');
		var gLocalizedCodesArray = [], gLocalizedNamesArray = [];
		for (var i = 0, value=''; i < values.length; i++)
		{
			value = values[i].split(' ');
			if (value.length > 0)
			{
				gLocalizedCodesArray.push(value.shift());
				gLocalizedNamesArray.push(value.join(' '));
			}
		}
		var localisedData = getLocalisedData(1, gLocalizedNamesArray, gLocalizedCodesArray);
		Ext.getCmp('langItemsPanel').loadData({dataList: localisedData.dataList});
		
		Ext.getCmp('langItemsPanel').getBottomToolbar().items.items[0].getStore().loadData(localisedData.langListStore);
		
		itemsEditDialogObj.syncShadow();
	};
	
	var onItemDelete = function()
	{
		var store = Ext.getCmp('itemsGrid').getStore();
		var selected = itemsCheckBoxSelectionModelObj.getSelections();
		
		for (var i = 0, pos = 0; i < selected.length; i++)
		{
      var item = selected[i];
			pos = store.findExact('code', item.data.code, 0);

      if (Ext.getCmp('keywordType').getValue() === 'RADIOGROUP')
      {
        if (item.data.image !== '')
        {
          imagesToRemove.push(item.data.image);
        }

        if (item.data.previousimage !== '')
        {
          imagesToRemove.push(item.data.previousimage);
        }
      }

			store.removeAt(pos);
			store.commitChanges();
		}
	};

  var clearImagePath = function(pCallback)
  {
    Ext.Ajax.request(
    {
      url: 'index.php?fsaction=AdminMetadataKeywords.clearImagePath&ref={/literal}{$ref}{literal}',
      method: 'POST',
      params: {csrf_token: Ext.taopix.getCSRFToken()},
      success: function()
      {
        if (typeof pCallback === 'function')
        {
          pCallback();
        }
      },
      failure: function(result, request)
      {
        // Fail silently.
        if (typeof pCallback === 'function')
        {
          pCallback();
        }
      }
    });
  };

	var itemsCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({ 
		listeners: 
		{
			selectionchange: function(gridCheckBoxSelectionModelObj) 
			{ 
				var itemsGridObj = Ext.getCmp('itemsGrid');
				if (gridCheckBoxSelectionModelObj.getCount() > 0)
				{
					if (gridCheckBoxSelectionModelObj.getCount() == 1) 
					{
						itemsGridObj.editButton.enable();
					}	
					else 
					{
						itemsGridObj.editButton.disable();  
					}
					itemsGridObj.deleteButton.enable();
				} 
				else 
				{ 
					itemsGridObj.editButton.disable();  itemsGridObj.deleteButton.disable(); 
				}
			}
		}
	});

	/**
	 * Removes the metadata keyword image.
	 */
	var onRemoveImage = function()
	{
    var itemImagePath = Ext.getCmp('itemImagePath');
    imagesToRemove.push(itemImagePath.getValue());
		itemImagePath.setValue('');
		Ext.getDom('previewimage').src = defaultImage;
		Ext.getCmp('resetImageButton').enable();

		repositionDialog();
	}

	/**
	 * Resets the metadata keyword image.
	 */
	var onResetImage = function()
	{
		var selected = itemsCheckBoxSelectionModelObj.getSelected();
		var imagePath = '';
		var previewImage = defaultImage;

		if ((typeof selected !== 'undefined') && (selected.data.image !== ''))
		{
			imagePath = selected.data.image;
			previewImage = formatImagePath(selected.data.image);

      // Remove the image from the to delete list.
      var imagesToRemovePos = imagesToRemove.indexOf(imagePath);

      if (imagesToRemovePos > -1)
      {
        imagesToRemove.splice(imagesToRemovePos, 1);
      }
		}

		var itemImagePath = Ext.getCmp('itemImagePath');
		imagesToRemove.push(itemImagePath.getValue());
		itemImagePath.setValue(imagePath);
		Ext.getDom('previewimage').src = getImageSrc(previewImage);
		Ext.getCmp('resetImageButton').disable();
	};
	
	var itemsEditDialogObj = new Ext.Window({
		id: 'itemsDialog',
		closeAction: 'hide',
		title: "{/literal}{#str_LabelEditItems#}{literal}",
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		width: 660,
		frame: true,
		autoHeight: true,
		items: 
		[
			{
				xtype: 'form',
				layout: 'form',
				frame: true,
				id: 'itemsDialogForm',
				autoHeight: true,
				items: 
				[
					{ 
						xtype: 'panel',
						layout: 'form',
						style:'background: #c9d8ed; padding: 3px 0; border:1px solid #8ca9cf; margin-bottom: 7px', 
						bodyBorder: false, 
						border: false, 
						labelWidth: 95,
						defaults: {xtype: 'textfield', labelWidth: 95, width: 480},  
						bodyStyle:'padding:5px 5px 0; border-top: 0px',
						items: 
						[
							{ 
								xtype: 'textfield',
								id: 'itemCode', 
								labelWidth: 95,
								fieldLabel: "{/literal}{#str_LabelCode#}{literal}", 
								validateOnBlur: true, 
								post: true, 
								allowBlank: false, 
								maxLength: 50, 
								readOnly: false,
								style: {textTransform: "uppercase"}
							}
						]
					},	
					{
						xtype: 'panel',
						width: 497,
						fieldLabel: "{/literal}{#str_LabelName#}{literal}",
						items: 
						[
							new Ext.taopix.LangPanel({
								id: 'langItemsPanel',
								name: 'langItemsPanel',
								height:153,
								width: 497,
								post: true,
								style: 'border:1px solid #b4b8c8',
								data: {langList: langListStore, dataList: []},
								settings: 
								{ 
									headers:     {langLabel: "{/literal}{#str_LabelLanguageName#}{literal}",  textLabel: "{/literal}{#str_LabelName#}{literal}", deletePic: deleteImg, addPic: addimg},
									defaultText: {langBlank: "{/literal}{#str_LabelSelectLanguage#}{literal}",  textBlank: "{/literal}{#str_ExtJsTypeValue#}{literal}", defaultValue: "{/literal}{$defaultlanguagecode}{literal}"},
									columnWidth: {langCol: 200, textCol: 234, delCol: 35},
                                    fieldWidth:  {langField: 195, textField: 213},
									errorMsg:    {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"}
								}
							})
						]
					},
					{
						xtype: 'buttongroup',
						id: 'imageButtonGroup',
						fieldLabel: "{/literal}{#str_LabelImagePath#}{literal}",
						frame: false,
						columns: 6,
						items: 
						[
							{
								xtype: 'hidden',
								id: 'itemImagePath'
							},
							{
								text: "{/literal}{#str_ButtonUpdatePreviewImage#}{literal}",
								handler: function() 
								{
									createUploadDialog();
									Ext.getDom('keywordimage').value = '';
								}
							},
							{
								xtype: 'spacer',
								width: 5
							},
							{
								text: "{/literal}{#str_ButtonRemovePreviewImage#}{literal}",
								handler: onRemoveImage
							},
							{
								xtype: 'spacer',
								width: 5
							},
							{
								text: "{/literal}{#str_ButtonResetPreviewImage#}{literal}",
								id: 'resetImageButton',
								disabled: true,
								handler: onResetImage
							}
						]
					},
					{
						xtype: 'spacer',
						id: 'imageSpacer',
						height: 5
					},
					{
						xtype: 'box',
						id: 'previewImageContainer',
						autoEl:
						{
							tag: 'div',
							html: '<img style="border: 1px solid; max-height: 150px;" id="previewimage" name="previewimage" src="' + defaultImage + '" onload="repositionDialog();" onerror="repositionDialog();">'
						},
						style: {'padding-left': '104px'}
					}
				]
			}
		],
		buttons: 
		[
			{	
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(btn, ev){ Ext.getCmp('itemsDialog').hide(); },
				cls: 'x-btn-right'
			},
			{
				text: "{/literal}{#str_ButtonUpdate#}{literal}",
				id: 'updateItemButton',
				handler: itemSaveHandler,
				cls: 'x-btn-right'
			}
		],
		listeners: {
			'show': function(){Ext.getCmp('itemsDialog').doLayout(); }
		}
	});

	var createUploadDialog = function()
	{
		var uploadFormPanelObj = new Ext.FormPanel({
			id: 'uploadcomponentform',
			frame: true,
			autoWidth: true,
			autoHeight: true,
			layout: 'column',
			bodyBorder: false,
			border: false,
			url: './?fsaction=AdminMetadataKeywords.uploadImage&ref={/literal}{$ref}{literal}',
			method: 'POST',
			baseParams: {csrf_token: Ext.taopix.getCSRFToken()},
			fileUpload: true,
			items: 
			[	    
				{
					xtype: 'box',
			    	autoEl: {
						tag: 'div',
						html: "{/literal}{$previewImageText}{literal}"
					},
					style:'padding-bottom: 5px'
				},
				{
					  xtype: 'spacer',
					  height: 5
				},
				{
					xtype: 'textfield',
					hideLabel: true,
					name: 'keywordimage',
					id: 'keywordimage',
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
					handler: uploadImage
				}
			]
		});	
		
		var gUploadObj = new Ext.Window({
			id: 'uploaddialog',
			title: "{/literal}{#str_TitleSelectPreviewImage#}{literal}",
			closable: false,
			plain: true,
			modal: true,
			draggable: true,
			resizable: false,
			bodyBorder: false,
			layout: 'fit',
			autoHeight: true,
			width: 490,
			items: uploadFormPanelObj
		});
	
		gUploadObj.show();
	}

	var uploadImage = function(pButton, pEvent)
	{
		var fileName = Ext.getDom('keywordimage').value.toLowerCase();
		if (! validateFileExtension(fileName)) 
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

		onUploadImage();
	};

	var onUploadImage = function()
	{
		var theForm = Ext.getCmp('uploadcomponentform').getForm();
		theForm.submit({
			scope: this,
			clientValidation: false,
			success: function(form, action)
			{
				Ext.getCmp('uploaddialog').close();

				Ext.getCmp('itemImagePath').setValue(action.result.path);
				Ext.getCmp('resetImageButton').enable();
				document.getElementById('previewimage').src = getImageSrc(formatImagePath(action.result.path));
			},
			failure: function(form, action)
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: 'Failed', buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
			},
			waitMsg: {/literal}"{#str_AlertUploading#}"{literal}
		});
	};

	var toggleImageControls = function()
	{
		if (Ext.getCmp('keywordType').getValue() !== 'RADIOGROUP')
		{
			Ext.getCmp('imageButtonGroup').hide();
			Ext.getCmp('imageSpacer').hide();
			Ext.getCmp('previewImageContainer').hide();
		}
		else
		{
			Ext.getCmp('imageButtonGroup').show();
			Ext.getCmp('imageSpacer').show();
			Ext.getCmp('previewImageContainer').show();
		}

		Ext.getCmp('itemsDialog').doLayout();
	}

	var validateFileExtension = function(pFileName)
	{
		var exp = /^.*\.(jpg|jpeg|png|gif)$/;
		return exp.test(pFileName);
	};

	/* Keyword edit/add window and functions */
	var onCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{	
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
			Ext.getCmp('itemsGrid').getStore().reload();
			if (gDialogObj.isVisible())
			{
				gDialogObj.close();
			}
		}
	};
	
	var onDialogCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.msg)
			{	
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
			Ext.getCmp('keywordsGrid').getStore().reload();
			if (Ext.getCmp('dialog').isVisible())
			{
				Ext.getCmp('itemsDialog').close();
				Ext.getCmp('dialog').close();
			}
		}
	};
	
	var editSaveHandler = function()
	{
		var store = Ext.getCmp('itemsGrid').getStore();
		var storeRecNum = store.getCount();
		var langNamePanelObj = Ext.getCmp('langNamePanel');
		var langDescPanelObj = Ext.getCmp('langDescPanel');
		var keywordType = Ext.getCmp('keywordType').getValue();
		var parameter = [];
		
		function saveData() 
		{
			var namesValue = [];
			var flagsValue = [];
			namesValue.push(langNamePanelObj.convertTableToString());
			var flags = '';

			if ((keywordType == 'POPUP') || (keywordType == 'RADIOGROUP'))
			{
				for (var i = 0, flag; i < storeRecNum; i++)
				{
					var item = store.data.items[i];
					namesValue.push(item.data.name);
					
					flag = item.data.code;
					if ((keywordType == 'RADIOGROUP') && (item.data.image != ''))
					{
						flag += ('<p>' + item.data.image);
					}
					flagsValue.push(flag);

					if ((keywordType === 'RADIOGROUP') && (item.data.previousimage !== ''))
					{
						imagesToRemove.push(item.data.previousimage);
					}
				}
				flags = flagsValue.join('<br>');
			}
			if ((keywordType == 'SINGLELINE') || (keywordType == 'MULTILINE'))
			{
				var flagsArray = [];
				if (Ext.getCmp('valueUppercase').checked)
				{
					flagsArray.push('U');
				}
				if (Ext.getCmp('valueRequired').checked)
				{
					flagsArray.push('M');
				}
				flags = flagsArray.join('<br>');
			}

      // Combine both images to remove lists.
      imagesToRemove = imagesToRemove.concat(imagesToRemoveOnTypeChange);
			parameter['name'] = namesValue.join('<br>');
			parameter['flags'] = flags;
			parameter['imagestoremove'] = imagesToRemove.join('<br>');
			
			parameter['kwRef'] = "{/literal}{$kwRef}{literal}";
			
			parameter['id'] = Ext.taopix.gridSelection2IDList(Ext.getCmp('keywordsGrid'));
			
			if ((keywordType == 'SINGLELINE') || (keywordType == 'MULTILINE'))
			{
				if (Ext.getCmp('maxLength').getValue() == 0)
				{
					var tabpanel = Ext.getCmp('mainTabPanel');
	   		 		tabpanel.activate('optionsTab');
					Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ErrorMaxLengthMustBeGreaterThanZero#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
					return false;
				}
			}
			
			{/literal}{if $keywordId < 1}{literal}
				Ext.taopix.formPanelPost(dialogFormPanelObj, dialogFormPanelObj.getForm(), parameter, 'index.php?fsaction=AdminMetadataKeywords.add', "{/literal}{#str_MessageSaving#}{literal}", onDialogCallback); 
    		{/literal}{else}{literal}
				Ext.taopix.formPanelPost(dialogFormPanelObj, dialogFormPanelObj.getForm(), parameter, 'index.php?fsaction=AdminMetadataKeywords.edit', "{/literal}{#str_MessageSaving#}{literal}", onDialogCallback); 
    		{/literal}{/if}{literal}
		}
		
		function onMaxLengthChangeCallback(btn) 
		{
			if (btn == "yes") 
			{
				saveData();
			}
		}
		
		if (dialogFormPanelObj.getForm().isValid())
		{
			if (!langNamePanelObj.isValid())
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ExtJsErrorNoName#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
	    		return false;
			}
			
			if (!langDescPanelObj.isValid())
			{
				Ext.MessageBox.show({ title: "{/literal}{#str_TitleError#}{literal}", msg: "{/literal}{#str_ExtJsErrorNoDescription#}{literal}", buttons: Ext.MessageBox.OK, animEl: 'mb9', icon: Ext.MessageBox.ERROR });
	    		return false;
			}
			
			parameter['oldMaxLength'] = "{/literal}{$maxlength}{literal}";
			
			{/literal}{if $keywordId != 0}{literal}
			if ((keywordType == 'SINGLELINE') && (parameter['oldMaxLength'] * 1 != Ext.getCmp('maxLength').getValue() * 1))
			{
				Ext.MessageBox.confirm("{/literal}{#str_TitleWarning#}{literal}", "{/literal}{#str_WarningMaxLengthChanged#}{literal}", onMaxLengthChangeCallback); 
				return false;
			}
			else
			{
				saveData();
			}
			{/literal}{else}{literal}
				saveData();
			{/literal}{/if}{literal}
		}
		return false;
	};
	
	var onTypeChanged = function(combo)
	{
		var typeValue = combo.getValue();
		
		var maxLengthField = Ext.getCmp('maxLength');
		var heightField = Ext.getCmp('height');
		var itemsPanelObj = Ext.getCmp('itemsPanel');
		var valueUppercaseField = Ext.getCmp('valueUppercase');
		var optionsTabObj = Ext.getCmp('optionsTab');
		var grid = Ext.getCmp('itemsGrid');
		var valueRequiredField = Ext.getCmp('valueRequired');
		
		maxLengthField.hide();
		heightField.hide();
		itemsPanelObj.hide();
		valueUppercaseField.hide();
		optionsTabObj.enable();
		grid.getColumnModel().setHidden(3, false);
		valueRequiredField.hide();

		switch (typeValue)
		{
			case 'SINGLELINE':
				valueUppercaseField.show();
				maxLengthField.show();
				valueRequiredField.show();
				break;
			case 'MULTILINE':
				valueUppercaseField.show();
				heightField.show();
				valueRequiredField.show();
				maxLengthField.show();
				break;
			case 'POPUP':
				itemsPanelObj.show();
				grid.getColumnModel().setHidden(3, true);
				valueRequiredField.hide();
				break;
			case 'RADIOGROUP':
				itemsPanelObj.show();
				valueRequiredField.hide();
				break;
			default:
				optionsTabObj.disable();
		}

    if (typeValue !== 'RADIOGROUP')
    {
      // If changed from a radio group, delete the images.

      var store = Ext.getCmp('itemsGrid').getStore();
      var storeItems = store.data.items;
      var storeItemsLength = storeItems.length;
  
      for (var i = 0; i < storeItemsLength; i++)
      {
        var storeItem = storeItems[i];

        if (storeItem.data.image !== '')
        {
          imagesToRemoveOnTypeChange.push(storeItem.data.image);
        }

        if (storeItem.data.previousimage !== '')
        {
          imagesToRemoveOnTypeChange.push(storeItem.data.previousimage);
        }
      }
    }
    else
    {
      // Reset the array if it was changed back to radio group.
      imagesToRemoveOnTypeChange = [];
    }
	};
	
	var keywordTypeChanged = function(combo, record, index)
	{
		onTypeChanged(combo);
	};

	var topPanel = new Ext.Panel({ 
		id: 'topPanel', 
		layout: 'form',
		style:'background: #c9d8ed; padding: 3px 0; border:1px solid #8ca9cf', 
		plain:true, 
		bodyBorder: false, 
		border: false, 
		defaults: {xtype: 'textfield', labelWidth: 120, width: 300},  
		bodyStyle:'padding:5px 5px 0; border-top: 0px',
		items: 
		[
			{ 
				id: 'code', 
				name: 'code',
				fieldLabel: "{/literal}{#str_LabelCode#}{literal}", 
				validateOnBlur: true, 
				post: true, 
				allowBlank: false, 
				maxLength: 50, 
				maskRe: /^\w+$/,
				{/literal}{if $keywordId==0}{literal}
					readOnly: false,
					style: {textTransform: "uppercase"}
				{/literal}{else}{literal}
					readOnly: true,
					style: 'background:#c9d8ed; textTransform: uppercase',	
					value: "{/literal}{$code}{literal}"
				{/literal}{/if}{literal} 
			}
		]
	});	
	
	var keywordName = "{/literal}{$name}{literal}".split('<p>');
	gLocalizedCodesArray = [], gLocalizedNamesArray = [];
	for (var i = 0, value=''; i < keywordName.length; i++)
	{
		value = keywordName[i].split(' ');
		if (value.length > 0)
		{
			gLocalizedCodesArray.push(value.shift());
			gLocalizedNamesArray.push(value.join(' '));
		}
	}
	localisedData = getLocalisedData(0, gLocalizedNamesArray, gLocalizedCodesArray);
	
	var langNamePanel = new Ext.taopix.LangPanel({
		id: 'langNamePanel',
		name: 'name',
		height:153,
		width: 570,
		post: false,
		style: 'border:1px solid #b4b8c8',
		data: {langList: localisedData.langListStore, dataList: localisedData.dataList},
		settings: 
		{ 
			headers: {langLabel: "{/literal}{#str_LabelLanguageName#}{literal}",  textLabel: "{/literal}{#str_LabelName#}{literal}", deletePic: deleteImg, addPic: addimg},
			defaultText: {langBlank: "{/literal}{#str_LabelSelectLanguage#}{literal}",  textBlank: "{/literal}{#str_ExtJsTypeValue#}{literal}", defaultValue: "{/literal}{$defaultlanguagecode}{literal}"},
			columnWidth: {langCol: 200,   textCol: 307, delCol: 35},
			fieldWidth: {langField: 190, textField: 286},
			errorMsg: {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"}
		}
	});
	
	
	var keywordDesc = "{/literal}{$description}{literal}".split('<p>');
	gLocalizedCodesArray = [], gLocalizedNamesArray = [];
	for (var i = 0, value=''; i < keywordDesc.length; i++)
	{
		value = keywordDesc[i].split(' ');
		if (value.length > 0)
		{
			gLocalizedCodesArray.push(value.shift());
			gLocalizedNamesArray.push(value.join(' '));
		}
	}
	localisedData = getLocalisedData(0, gLocalizedNamesArray, gLocalizedCodesArray);
	
	var langDescPanel = new Ext.taopix.LangPanel({
		id: 'langDescPanel',
		name: 'desc',
		height:153,
		width: 570,
		post: true,
		style: 'border:1px solid #b4b8c8',
		data: {langList: localisedData.langListStore, dataList: localisedData.dataList},
		settings: 
		{ 
			headers: {langLabel: "{/literal}{#str_LabelLanguageName#}{literal}",  textLabel: "{/literal}{#str_LabelName#}{literal}", deletePic: deleteImg, addPic: addimg},
			defaultText: {langBlank: "{/literal}{#str_LabelSelectLanguage#}{literal}",  textBlank: "{/literal}{#str_ExtJsTypeValue#}{literal}", defaultValue: "{/literal}{$defaultlanguagecode}{literal}"},
			columnWidth: {langCol: 200,   textCol: 307, delCol: 35},
			fieldWidth: {langField: 190, textField: 286},
			errorMsg: {blankValue: "{/literal}{#str_ExtJsTextFieldBlank#}{literal}"}
		}
	});
		
	var tabPanel = { 
		xtype: 'tabpanel',
		id: 'mainTabPanel',
		deferredRender: false,
		activeTab: 0,
		width: 710,
		height: 390,
		shadow: true,
		plain:true,
		style:'margin-top:6px; ',
		defaults:{ autoScroll: true, hideMode:'offsets', layout: 'form', bodyStyle:'padding:5px 10px 0 10px; border-top: 0px; background-color: #eaf0f8;'},
		items: 
		[
			{
				title: "{/literal}{#str_LabelSettings#}{literal}",
				defaults:{xtype: 'textfield'},
				items: 
				[
					{
						xtype: 'combo',	
						id: 'keywordType',
						name: 'keywordType',
						mode: 'local',
						editable: false,
						forceSelection: true,
						selectOnFocus: true,
						triggerAction: 'all',
						fieldLabel:"{/literal}{#str_LabelType#}{literal}",
						width: 300,
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: ['id', 'name'],
							data: [
								{/literal}
								{section name=index loop=$typelist}
									{if $smarty.section.index.last}
										["{$typelist[index].id}", "{$typelist[index].name}"]
									{else}
										["{$typelist[index].id}", "{$typelist[index].name}"],
									{/if}
								{/section}
								{literal}
							]
						}),
						listeners: { 'select': keywordTypeChanged },
						valueField: 'id',
						displayField: 'name',
						useID: true,
						value: "{/literal}{$keywordType}{literal}",
						post: true
					},
					{
						xtype: 'panel',
	    				width: 570,
	    				fieldLabel: "{/literal}{#str_LabelName#}{literal}",
	    				items: langNamePanel
					},
					{
						xtype: 'panel',
	    				width: 570,
	    				fieldLabel: "{/literal}{#str_LabelDescription#}{literal}",
	    				items: langDescPanel
					}
				]
			},
			{
				title: "{/literal}{#str_LabelOptions#}{literal}",
				id: 'optionsTab',
				defaults:{xtype: 'textfield'},
				items: 
				[
					{
						xtype: 'numberfield',
						fieldLabel: "{/literal}{#str_LabelMaxLength#}{literal}",
						post: true,
						id: 'maxLength',
						name: 'maxLength',
						allowDecimals: false,
						allowNegative: false,
						value: "{/literal}{$maxlength}{literal}"
					},
					{
						xtype: 'numberfield',
						fieldLabel: "{/literal}{#str_LabelHeight#}{literal}",
						post: true,
						id: 'height',
						name: 'height',
						allowDecimals: false,
						allowNegative: false,
						value: "{/literal}{$height}{literal}"
					},
					{
						xtype: 'numberfield',
						fieldLabel: "{/literal}{#str_LabelWidth#}{literal}",
						post: true,
						id: 'width',
						name: 'width',
						allowDecimals: false,
						allowNegative: false,
						value: "{/literal}{$width}{literal}"
					},
					{
						xtype: 'checkbox',
						hideLabel: true,
						boxLabel: '{/literal}{#str_LabelUppercaseValue#}{literal}',
						post: false,
						id: 'valueUppercase',
						checked: (({/literal}{$uppsercase}{literal} == 1) ? true : false)
					},
					{
						xtype: 'checkbox',
						hideLabel: true,
						boxLabel: "{/literal}{#str_LabelValueRequired#}{literal}",
						post: false,
						id: 'valueRequired',
						checked: (({/literal}{$valuerequired}{literal} == 1) ? true : false)
					},
					{
						xtype: 'panel',
	    				width:570,
	    				id: 'itemsPanel',
	    				fieldLabel: "{/literal}{#str_LabelItems#}{literal}",
	    				items: 
	    				[
	    					new Ext.grid.GridPanel({
   					 			id: 'itemsGrid',
   					 			ctCls: 'grid',
   					 			style: 'border: 1px solid #b4b8c8',
        						store: new Ext.data.Store({
        							reader: new Ext.data.ArrayReader({}, [
       									{name: 'code' },
       									{name: 'name' },
       									{name: 'image' },
										{name: 'previousimage'}
       								])
        						}),
        						columns: 
        						[
            						itemsCheckBoxSelectionModelObj,
            						{
            							header: "{/literal}{#str_LabelCode#}{literal}",
            							dataIndex: 'code', 
            							sortable: false, 
            							menuDisabled: true,
            							width: 130, 
            							hidden: false 
            						},
           							{
            							id: 'nameCol',
            							header: "{/literal}{#str_LabelName#}{literal}",
            							width: 395, 
            							sortable: false, 
            							dataIndex: 'name', 
            							menuDisabled: true,
            							renderer: itemsNameRenderer
            						},
            						{
            							header: "{/literal}{#str_LabelImagePath#}{literal}",
            							width: 150, 
            							sortable: false, 
            							dataIndex: 'image', 
            							menuDisabled: true
            						}
            					],
            					selModel: itemsCheckBoxSelectionModelObj,
        						width: 570,
        						height: 290,
        						iconCls: 'icon-grid',
        						stripeRows: true,
    							stateful: true,
    							enableColLock:false,
								draggable:false,
								enableColumnHide:false,
								enableColumnMove:false,
								trackMouseOver:false,
								tbar: 
    							[	
    								{ref: '../addButton',	text: "{/literal}{#str_ButtonAdd#}{literal}",	iconCls: 'silk-add',	handler: onItemAdd	}, '-',
     						       	{ref: '../editButton',	text: "{/literal}{#str_ButtonEdit#}{literal}",	iconCls: 'silk-pencil',	handler: onItemEdit, disabled: true	}, '-',
     						       	{ref: '../deleteButton', text: "{/literal}{#str_ButtonDelete#}{literal}", iconCls: 'silk-delete', handler: onItemDelete, disabled: true }
           				    	]
							})
	    				]
					}
				]
			}
		]
	};	
		
	var dialogFormPanelObj = new Ext.taopix.FormPanel({
		id: 'mainForm',
		header: false,
		frame:true,
		layout: 'form',
		height: 515,
		defaultType: 'textfield',
		items: [ topPanel, tabPanel ],
		baseParams:	{ ref: '{/literal}{$ref}{literal}' }
	});
	
	gDialogObj = new Ext.Window({
		id: 'dialog',
		plain:true,
		title: "{/literal}{$title}{literal}",
		modal:true,
		draggable:true,
		resizable:false,
		layout: 'fit',
		width: 740,
		height: 515,
		items: dialogFormPanelObj,
		listeners: {
			'close': {   
				fn: function(){
						metaKeywordsWindowExists = false;
				}
			}
		},
		buttons: 
		[
			{	
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				handler: function(btn, ev){ Ext.getCmp('itemsDialog').close(); gDialogObj.close(); },
				cls: 'x-btn-right'
			},
			{
				id: 'updateButton',
				handler: editSaveHandler,
				cls: 'x-btn-right',
				{/literal}{if $keywordId < 1}{literal}
					text: "{/literal}{#str_ButtonAdd#}{literal}"
				{/literal}{else}{literal}
					text: "{/literal}{#str_ButtonUpdate#}{literal}"
				{/literal}{/if}{literal}
			}
		]
	});

	gDialogObj.show();	  
	
	onTypeChanged(Ext.getCmp('keywordType'));
	
	var itemsValues = {/literal}{$values}{literal};
	Ext.getCmp('itemsGrid').getStore().loadData(itemsValues);

}
{/literal}