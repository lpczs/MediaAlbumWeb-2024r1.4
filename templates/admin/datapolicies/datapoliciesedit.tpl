{literal}

var policy = {/literal}{$policy}{literal};

var configArray =
{
	'purge':
	[
		{'header': '', 'key': ['projects', 'assets'], style: 'width: 160px;'},
		{'header': "{/literal}{#str_LabelDormant#}{literal}", 'key': ['age'], style: 'text-align: center; width: 80px; '},
		{'header': "{/literal}{#str_LabelSendEmails#}{literal}", 'key': ['email'], style: 'text-align: center; width: 80px; '},
		{'header': "{/literal}{#str_LabelDaysBeforePurge#}{literal} ({/literal}{#str_LabelDays#}{literal})", 'key': ['days'], style: 'text-align: center; width: 120px; '},
		{'header': "{/literal}{#str_LabelEmailFrequency#}{literal} ({/literal}{#str_LabelDays#}{literal})", 'key': ['emailfrequency'], style: 'text-align: center; width: 120px; '},
		{'header': "{/literal}{#str_LabelTotalDaysDormantBeforeDeletion#}{literal}", 'key': ['total'], style: 'text-align: center; width: 130px; '},
	],
	'archive':
	[
		{'header': '', 'key': ['archiveactive'], style: 'width: 160px; min-height: 30px;'},
		{'header': "{/literal}{#str_LabelDormant#}{literal}", 'key': ['archivedays'], style: 'text-align: center; width: 128px;'}
	],
	'order':
	[
		{'policykey': 'ordered', 'label': "{/literal}{#str_LabelOrderedProjects#}{literal}" },
		{'policykey': 'notordered', 'label': "{/literal}{#str_LabelUnorderedProjects#}{literal}" },
		{'policykey': 'unsaved', 'label': "{/literal}{#str_LabelUnsavedProjects#}{literal}" },
		{'policykey': 'guest', 'label': "{/literal}{#str_LabelGuestProjects#}{literal}" },
		{'policykey': 'orderedunused', 'label': "{/literal}{#str_LabelOrderedUnusedAssets#}{literal}" },
		{'policykey': 'notorderedunused', 'label': "{/literal}{#str_LabelUnorderedUnusedAssets#}{literal}" }
	]
};

var elementsArray = ['age', 'email'];
var archiveElementsArray = ['days'];
var elementDependsArray = ['frequency', 'days'];
var checkBoxElementsArray = [
	'orderedarchiveactive', 'orderedprojects', 'orderedemail',
	'notorderedarchiveactive', 'notorderedprojects', 'notorderedemail',
	'unsavedprojects', 'unsavedemail', 'guestprojects', 'orderedunusedassets', 'notorderedunusedassets'];

var policyIsValid = new Object();

function initialize(pParams)
{
	/* validate input */
	function forceNumeric(pObj)
	{
		var string = pObj.getValue();

		string = string.replace(/[^0-9]+/g, "");

    	pObj.setValue(string);
	}

	function forceAlphaNumeric(pObj, pForceUpperCase, pAllowSpace)
	{
		var string = pObj.getValue();

    	if (pForceUpperCase)
    	{
    		string = string.toUpperCase();
    	}

		if (pAllowSpace)
		{
			string = string.replace(/[^a-zA-Z_0-9 \-]+/g, "");
		}
		else
		{
			string = string.replace(/[^a-zA-Z_0-9\-]+/g, "");
		}

    	pObj.setValue(string);
	}

	/* save functions */
	function saveCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var gridObj = gMainWindowObj.findById('maingrid');
			var dataStore = gridObj.store;
			gridObj.store.reload();
			Ext.getCmp('dialog').close();
		}
		else
		{
			Ext.MessageBox.show({ title: pActionData.result.title, msg: pActionData.result.msg, buttons: Ext.MessageBox.OK,	icon: Ext.MessageBox.WARNING });
		}
	}


	/* save functions */
	function saveHandler(pSubmitUrl)
	{
		var fp = Ext.getCmp('policyForm');
		var form = fp.getForm();
		var canSubmit = false;

		var paramArray = new Object();

		for (var i = 0; i < checkBoxElementsArray.length; i++)
		{
			paramArray[checkBoxElementsArray[i]] = (Ext.getCmp(checkBoxElementsArray[i]).checked) ? 1 : 0;
		}

		// Check if any of the rules in the policy have warnings.
		if (policyIsValid.length == 0)
		{
			// No issues found, allow the policy to be saved
			canSubmit = true;
		}
		else
		{
			// Check each of the rules of the policy, making sure no issues are still outstanding.
			for (key in policyIsValid)
			{
				if (policyIsValid[key] == 1)
				{
					// No problems, allow the policy to be submitted.
					canSubmit = true;
				}
				else
				{
					// A problem has been found, do not allow the policy to be submitted, no need to check later rules.
					canSubmit = false;
					break;
				}
			}
		}

		// No problems found, all the form to be submitted.
		if (canSubmit)
		{
			paramArray['active'] = (Ext.getCmp('isactive').checked) ? 1 : 0;

			// Ask for confirmation if the ordered, unordered or unsaved projects option is selected.
			if ((1 === paramArray['orderedprojects']) || (1 === paramArray['unsavedprojects']) || (1 === paramArray['notorderedprojects']))
			{
				// Set the save handler function.
				confirmationDialogConfig.fn = function(btn)
				{
					if (btn === 'ok')
					{
						Ext.taopix.formPanelPost(fp, form, paramArray, pSubmitUrl, "{/literal}{#str_MessageSaving#}{literal}", saveCallback);
					}
				};
				Ext.MessageBox.show(confirmationDialogConfig);
			}
			else
			{
				Ext.taopix.formPanelPost(fp, form, paramArray, pSubmitUrl, "{/literal}{#str_MessageSaving#}{literal}", saveCallback);
			}
		}
		else
		{
			// At least 1 of the rules has a problem, display an error.
			Ext.MessageBox.show(errorDialogConfig);
		}
	}

	function addsaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminDataRetentionAdmin.add&ref={/literal}{$ref}{literal}';

		// Trigger the save handler with the correct endpoint.
		saveHandler(submitURL);
	}


	function editSaveHandler(btn, ev)
	{
		var selectID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));
		var submitURL = 'index.php?fsaction=AdminDataRetentionAdmin.edit&ref={/literal}{$ref}{literal}&id=' + selectID;

		// Trigger the save handler with the correct endpoint.
		saveHandler(submitURL);
	}

	function setOptionValue(pElement, pSection, pKeyName)
	{
		var prefix = pSection;
		var keyName = pKeyName;

		if (prefix.indexOf('archive') !== -1)
		{
			prefix = prefix.replace('archive', '');
			keyName = 'archive' + keyName;
		}
		else if (prefix.indexOf('assets') !== -1)
		{
			prefix = prefix.replace('assets', '');
			keyName = 'assets' + keyName;
		}
		else if (prefix.indexOf('email') !== -1)
		{
			prefix = prefix.replace('email', '');

			if (keyName == 'frequency')
			{
				keyName = 'email' + keyName;
			}
		}

		pElement.setValue(policy[prefix][keyName]);
	}

	function setOptionActiveState(pCheckElement)
	{
		var prefix = pCheckElement.replace('projects', '');
		prefix = prefix.replace('active', '');
		var checkElements = (prefix.indexOf('archive') == -1) ? elementsArray : archiveElementsArray;

		var elementObj = Ext.getCmp(pCheckElement);

		for (var i = 0; i < checkElements.length; i++)
		{
			var changeElementName = prefix + checkElements[i];

			var changeElementObj = Ext.getCmp(changeElementName);

			if (changeElementObj != undefined)
			{
				var elementValue = '';
				if (elementsArray[i] == 'email')
				{
					var emailElementObj = Ext.getCmp(changeElementName);
					elementValue = emailElementObj.checked;

					setOptionActiveEmailState(changeElementName, elementObj.checked);
				}

				if (elementObj.checked)
				{
					changeElementObj.enable();
					setOptionValue(changeElementObj, prefix, checkElements[i]);
				}
				else
				{
					changeElementObj.disable();
					changeElementObj.setValue('');

					if (elementValue != '')
					{
						setPolicyValue(changeElementName, elementValue);
					}
				}
			}
		}

		if (! elementObj.checked)
		{
			setPolicyValue(pCheckElement, 0);
		}
		else
		{
			setPolicyValue(pCheckElement, 1);
		}
	}

	function setOptionActiveEmailState(pCheckElement, pForce)
	{
		var prefix = pCheckElement;

		var elementObj = Ext.getCmp(pCheckElement);

		for (var i = 0; i < elementDependsArray.length; i++)
		{
			var changeElementName = prefix + elementDependsArray[i];

			var changeElementObj = Ext.getCmp(changeElementName);

			if (changeElementObj == undefined)
			{
				changeElementName = prefix.replace('email', '') + elementDependsArray[i];
				changeElementObj = Ext.getCmp(changeElementName);
			}

			if (changeElementObj != undefined)
			{
				if (pForce)
				{
					if (elementObj.checked)
					{
						changeElementObj.enable();
						setOptionValue(changeElementObj, prefix, elementDependsArray[i]);
					}
					else
					{
						changeElementObj.disable();
						changeElementObj.setValue('');
					}
				}
				else
				{
					changeElementObj.disable();
					changeElementObj.setValue('');
				}
			}
		}
	}

	function calculateTotalDaysToPurge(pPurgeOption)
	{
		if (pPurgeOption.indexOf('archive') == -1)
		{
			var prefix = pPurgeOption.replace('projects', '');
			prefix = prefix.replace('assets', '');

			var policyDetails = policy[prefix];
			var newTotal = '';

			if (prefix == 'notorderedunused' || prefix == 'orderedunused')
			{
				if (policyDetails.assets == 0)
				{
					newTotal = '';
				}
				else
				{
					newTotal = parseInt(policyDetails.assetsage);
				}
			}
			else
			{
				if (policyDetails.projects == 0)
				{
					newTotal = '';
				}
				else
				{
					newTotal = parseInt(policyDetails.age);

					if (prefix != 'guest')
					{
						if (policyDetails.email == 1)
						{
							newTotal += parseInt(policyDetails.days);
						}
					}
				}
			}
			policy[prefix].total = newTotal;

			var element = Ext.getCmp(prefix + 'Total');
			if (element !== undefined)
			{
				element.setValue(newTotal);
			}
		}
	}

	function validateArchiveInterval(pOption)
	{
		// Determine if the rule is configured to archive before purge, not after.
		var policyDetails = policy[pOption];

		var archElementID = pOption + 'archivedays';
		var archElement = Ext.getCmp(archElementID);

		var delElementID = pOption + 'Total';
		var delElement = Ext.getCmp(delElementID);

		if ((policyDetails.archiveactive == 1) && (policyDetails.projects == 1))
		{
			if (policyDetails.total < policyDetails.archivedays)
			{
				// Mark the Values of the row as having an error
				archElement.markInvalid("{/literal}{#str_ErrorArchiveDaysInvalid#}{literal}");
				delElement.markInvalid("{/literal}{#str_ErrorDeletionDaysInvalid#}{literal}");

				// Store that the rule has an issue to prevent it from being saved.
				policyIsValid[pOption] = 0;
			}
			else
			{
				var bothValid = archiveAndPurgeAreValid(archElement, delElement);

				// If both archive and purge are valid set that the policy is valid.
				if (bothValid)
				{
					// Store that the rule no longer has an error, and allow it to be saved.
					policyIsValid[pOption] = 1;
				}
			}
		}
		else
		{
			var bothValid = archiveAndPurgeAreValid(archElement, delElement);

			// If both archive and purge are valid set that the policy is valid.
			if (bothValid)
			{
				// Store that the rule no longer has an error, and allow it to be saved.
				policyIsValid[pOption] = 1;
			}
		}
	}

	/**
	 * Checks if the archive element and purge element are valid, and clears any error if they are.
	 *
	 * @param pArchiveElement Archive element to check.
	 * @param pPurgeElement Purge element to check.
	 * @returns boolean True if both elements are valid, false otherwise.
	 */
	function archiveAndPurgeAreValid(pArchiveElement, pPurgeElement)
	{
		var archiveValid = pArchiveElement.validate();
		var purgeValid = pPurgeElement.validate();

		// Check if archive element is valid. If so remove any error.
		if (archiveValid)
		{
			pArchiveElement.clearInvalid();
		}

		// Check if purge element is valid. If so remove any error.
		if (purgeValid)
		{
			pPurgeElement.clearInvalid();
		}

		return ((archiveValid) && (purgeValid));
	}

	function setOptionMinimums(pOption)
	{
		var daysElementID = pOption.replace('email', 'days');

		var emailCheckObj = Ext.getCmp(pOption);

		if (emailCheckObj.checked)
		{
			Ext.getCmp(daysElementID).minValue = {/literal}{$minWarningDays}{literal};
		}
		else
		{
			Ext.getCmp(daysElementID).minValue = {/literal}{$minPurgeDays}{literal};
		}
		validateValueRange(daysElementID);
	}

	function validateValueRange(pObj)
	{
		var editObj = Ext.getCmp(pObj);

		var minVal = editObj.minValue;
		var maxVal = editObj.maxValue;
		var currentVal = editObj.value;

		if ((currentVal < minVal) && (! editObj.disabled))
		{
			editObj.setValue(minVal);
		}
		else if ((currentVal > maxVal) && (! editObj.disabled))
		{
			editObj.setValue(maxVal);
		}

		// Update current value.
		currentVal = editObj.value;

		// If the current value is not blank set this in the policy object.
		if (currentVal != '')
		{
			setPolicyValue(pObj, currentVal);
		}
	}

	function setPolicyValue(pObjId, pValue)
	{
		var section = '';
		var key = '';

		// Set the section we are working with.
		if (pObjId.indexOf('notordered') !== -1)
		{
			section = pObjId.indexOf('assets') === -1 ? 'notordered' : 'notorderedunused';
		}
		else if (pObjId.indexOf('ordered') !== -1)
		{
			section = pObjId.indexOf('assets') === -1 ? 'ordered' : 'orderedunused';
		}
		else if (pObjId.indexOf('unsaved') !== -1)
		{
			section = 'unsaved';
		}
		else if (pObjId.indexOf('guest') !== -1)
		{
			section = 'guest';
		}

		// Work out what key we are updating.
		key = pObjId.replace(section, '');

		if ((key == 'email') || (key == 'projects') || (key == 'archiveactive'))
		{
			pValue = pValue ? 1 : 0;
		}

		policy[section][key] = parseInt(pValue);

		calculateTotalDaysToPurge(section);

		// Validate that the new values of the rules do not have problems with the archive date being after the purge date.
		// Only applies to Ordered and Not ordered projects.
		if ((section == 'ordered') || (section == 'notordered'))
		{
			validateArchiveInterval(section);
		}
	}

	function setOptionCell(pOptName, pOptValue, pOptMin, pOptMax)
	{
		if ((pOptName == 'orderedarchivedays') || ((pOptName == 'notorderedarchivedays')))
		{
			var optionContainer = new Ext.Container({
				items:
				[
					{
						xtype: 'numberfield',
						id: pOptName,
						name: pOptName,
						allowBlank: false,
						value: pOptValue,
						validator: function(Value) { return fullValidation(pOptName, Value, pOptMin, pOptMax) },
						post: true,
						width: 50,
						ctCls: 'center-item x-form-field-wrap',
						msgTarget: 'side',
						minValue: pOptMin,
						maxValue: pOptMax,
						listeners: {'blur': {fn: function() { validateValueRange(pOptName) }}},
					}
				]
			});
		}
		else
		{
			var optionContainer = new Ext.Container({
				items:
				[
					{
						xtype: 'numberfield',
						minValue: pOptMin,
						maxValue: pOptMax,
						id: pOptName,
						name: pOptName,
						allowBlank: false,
						value: pOptValue,
						listeners: {'blur': {fn: function() {validateValueRange(pOptName)}}},
						post: true,
						width: 50,
						ctCls: 'center-item x-form-field-wrap',
						msgTarget: 'side'
					}
				]
			});
		}

		return optionContainer;
	}


	// Custom validation for the 'orderedarchivedays' and 'notorderedarchivedays' edit fields.
	// Incorporates the min max value check and validateValueRange function.
	function fullValidation(pOptionName, Value, pOptMin, pOptMax)
	{
		var archiveEditValue = Value / 1;

		var archElementID = pOptionName;
		var archElement = Ext.getCmp(archElementID);

		// Set the section we are working with.
		if (pOptionName.indexOf('notordered') !== -1)
		{
			section = 'notordered';
		}
		else if (pOptionName.indexOf('ordered') !== -1)
		{
			section = 'ordered';
		}

		var delElementID = section + 'Total';
		var delElement = Ext.getCmp(delElementID);

		if ((archiveEditValue < pOptMin) && (! archElement.disabled))
		{
			policyIsValid[pOptionName] = 1;
			archiveEditValue = pOptMin;
		}
		else if ((archiveEditValue > pOptMax) && (! archElement.disabled))
		{
			policyIsValid[pOptionName] = 1;
			archiveEditValue = pOptMax;
		}

		policy[section].archivedays = archiveEditValue;

		// Determine if the rule is configured to archive before purge, not after.
		var policyDetails = policy[section];

		if ((policyDetails.archiveactive == 1) && (policyDetails.projects == 1))
		{
			if (policyDetails.total < archiveEditValue)
			{
				// Store that the rule has an issue to prevent it from being saved.
				policyIsValid[section] = 0;
			}
			else
			{
				// Store that the rule no longer has an error, and allow it to be saved.
				policyIsValid[section] = 1;
			}
		}
		else
		{
			// Store that the rule no longer has an error, and allow it to be saved.
			policyIsValid[section] = 1;
		}

		if (policyIsValid[section] == 0)
		{
			// Mark the Values of the row as having an error
			archElement.markInvalid("{/literal}{#str_ErrorArchiveDaysInvalid#}{literal}");
			delElement.markInvalid("{/literal}{#str_ErrorDeletionDaysInvalid#}{literal}");

			//archElement.maxvalue = policyDetails.total;
			return "{/literal}{#str_ErrorArchiveDaysInvalid#}{literal}";
		}
		else
		{
			// Clear the error for the rule, if it has one
			archElement.clearInvalid();
			delElement.clearInvalid();

			//archElement.maxvalue = 1460;
			return true;
		}
	}

	var confirmationDialogConfig =
	{
		title: "{/literal}{#str_LabelConfirmation#}{literal}",
		msg: "{/literal}{#str_MessagePolicyConfirm#}{literal}",
		buttons:
		{
			cancel: "{/literal}{#str_ButtonCancel#}{literal}",
			ok: "{/literal}{#str_ButtonConfirm#}{literal}"
		},
		icon: Ext.MessageBox.QUESTION
	};

	var errorDialogConfig =
	{
		title: "{/literal}{#str_TitleError#}{literal}",
		msg: "{/literal}{#str_ErrorPolicyInvalid#}{literal}",
		buttons:
		{
			ok: "{/literal}{#str_ButtonOk#}{literal}"
		},
		icon: Ext.MessageBox.WARNING
	};


	var topPanel = new Ext.Panel({
		id: 'topPanel',
		layout: 'form',
		style: 'background: #c9d8ed; padding: 5px 5px 3px 5px; border:1px solid #8ca9cf; margin: 0px',
		plain: true,
		bodyBorder: false,
		border: false,
		labelWidth: 80,
		items:
		[
            {
				xtype: 'textfield',
				id: 'code',
				name: 'code',
				allowBlank:false,
				maxLength: 20,
				width: 100,
				{/literal}{if $isEdit == 1}{literal}
					value: "{/literal}{$code}{literal}",
					style: {textTransform: "uppercase", background: "#dee9f6"},
					readOnly: true,
				{/literal}{else}{literal}
					style: {textTransform: "uppercase"},
				{/literal}{/if}{literal}
				fieldLabel: "{/literal}{#str_LabelCode#}{literal}",
				listeners: {'blur': {fn: function(obj){forceAlphaNumeric(obj, true, false)}}},
				post: true
			}
		]
	});

	function panelItems(pPanel)
	{
		var headers = configArray[pPanel];

		var containerStyleDefault = {};
		var containerColumns = 0;
		var containerTitle = '';
		var items = [];
		var purgeWarning = {
					autoScroll: true,
					height: 156,
					html: "{/literal}{#str_MessagePolicyWarning#}{literal}",
					style: 'padding: 10px 0px; text-align: left; overflow-x: none;'
				};

		var archiveWarning = {
					html: "{/literal}{#str_MessagePolicyArchiveWarning#}{literal}",
					style: 'padding: 10px 0px 20px; text-align: left;'
				};

		if (pPanel === 'purge')
		{
			items.push(purgeWarning);
			containerColumns = 6;
			containerStyleDefault = {
				style: 'margin: 4px 0px 2px 0px; text-align: left; '
			};
			containerTitle = "{/literal}{#str_TitlePolicyPurgeRules#}{literal}";
		}
		else
		{
			items.push(archiveWarning);
			containerColumns = 2;
			containerStyleDefault = {
				style: 'margin: 4px 0px 2px 0px; text-align: left; '
			};
			containerTitle = "{/literal}{#str_TitlePolicyArchiveRules#}{literal}";
		}

		var containerItems = [];
		var headerCount = headers.length;
		var orderCount = configArray['order'].length;
		var i = 0;

		for (i = 0; i < headerCount; i++)
		{
			containerItems.push({html: headers[i].header, style: headers[i].style, ctCls: 'cell-header' });
		}

		for (i = 0; i < orderCount; i++)
		{
			var keyName = configArray['order'][i].policykey;
			var policyDetails = policy[keyName];

			if (pPanel == 'archive')
			{
				if (policyDetails.archiveactive != undefined)
				{
					var itemID = keyName + 'archiveactive';
					containerItems.push(
						{
							xtype: 'checkbox',
							id: itemID,
							name: itemID,
							checked: (policyDetails.archiveactive == 1),
							boxLabel: configArray['order'][i].label,
							post: true,
							listeners:
							{
								check:
								{
									fn: function(pSelf, pChecked)
									{
										setOptionActiveState(pSelf.id);
									}
								}
							}
						});

					itemID = keyName + 'archivedays';
					containerItems.push(setOptionCell(itemID, policyDetails.archivedays, {/literal}{$minDormantDays}{literal}, 1460));
				}
			}
			else
			{
				var typeControl = 'projects';
				var controlChecked = policyDetails.projects;
				var ageKey = 'age';
				var ageValue = policyDetails.age;
				var minDormantDaysValue = {/literal}{$minDormantDays}{literal};
				if (policyDetails.projects == undefined)
				{
					typeControl = 'assets';
				    controlChecked = policyDetails.assets;
					ageKey = 'assetsage';
					ageValue = policyDetails.assetsage;
				}
				else
				{
					if (keyName == 'ordered')
					{
						minDormantDaysValue = {/literal}{$minOrderedDormantDays}{literal};
					}
				}
				var controlItemID = keyName + typeControl;

				containerItems.push(
					{
						xtype: 'checkbox',
						id: controlItemID,
						name: controlItemID,
						checked: (controlChecked == 1),
						boxLabel: configArray['order'][i].label,
						post: true,
						listeners:
						{
							check:
							{
								fn: function(pSelf, pChecked)
								{
									setOptionActiveState(pSelf.id);
								}
							}
						}
					});

				var ageItemID = keyName + ageKey;
				containerItems.push(setOptionCell(ageItemID, ageValue, minDormantDaysValue, 1460));

				if (policyDetails.email != undefined)
				{
					var emailItemID = keyName + 'email';
					containerItems.push(
						{
							xtype: 'checkbox',
							id: emailItemID,
							name: emailItemID,
							checked: (policyDetails.email == 1),
							boxLabel: "",
							post: true,
							style: 'margin-left: 50%;',
							listeners:
							{
								check:
								{
									fn: function(pSelf, pChecked)
									{
										setPolicyValue(pSelf.id, (pChecked ? 1 : 0));
										setOptionActiveEmailState(pSelf.id, true);
										setOptionMinimums(pSelf.id);
									}
								}
							}
						});


					if ((policyDetails.days != undefined) && (keyName != 'guest'))
					{
						var minDaysValue = {/literal}{$minWarningDays}{literal};
						var dayItemID = keyName + 'days';
						var daysValue = policyDetails.days;
						containerItems.push(setOptionCell(dayItemID, daysValue, minDaysValue, 730));
					}
					else
					{
						containerItems.push({});
					}

					var emailFreqItemID = keyName + 'emailfrequency';
					containerItems.push(setOptionCell(emailFreqItemID, policyDetails.emailfrequency, 1, 365));

					containerItems.push({
						xtype: 'numberfield',
						id: keyName + 'Total',
						value: policyDetails.total,
						ctCls: 'center-item x-form-field-wrap dormatDays',
						msgTarget: 'side',
						disabled: true,
						style: 'text-align: center'
					});
				}
				else
				{
					containerItems.push({});
					containerItems.push({});
					containerItems.push({});

					containerItems.push({
						xtype: 'numberfield',
						id: keyName + 'Total',
						value: policyDetails.total,
						ctCls: 'center-item x-form-field-wrap dormatDays',
						msgTarget: 'side',
						disabled: true,
						style: 'text-align: center'
					});
				}
			}
		}

		var container = new Ext.form.FieldSet({
			xtype: 'fieldset',
			id: pPanel,
			title: containerTitle,
			items: containerItems,
			layout: 'table',
			defaults: containerStyleDefault,
			layoutConfig: {
				columns: containerColumns
			}
		});

		items.push(container);

		return items;
	}

	var archivePanel = new Ext.Panel({
		xtype: 'fieldset',
		id: 'dataArchivingPanel',
		title: '{/literal}{#str_LabelFieldsetDataArchiving#}{literal}',
		collapsible: false,
		autoHeight: true,
		style: 'position: relative;',
		items: panelItems('archive'),
		cellCls: 'top-align'
	});

	var purgePanel = new Ext.Panel({
		xtype: 'fieldset',
		id: 'purgePanel',
		title: '{/literal}{#str_LabelFieldsetDataDeletion#}{literal}',
		collapsible: false,
		autoHeight: true,
		style: 'position: relative;',
		items: panelItems('purge')
	});

	var tabPanel = {
		xtype: 'tabpanel',
		id: 'maintabpanel',
		deferredRender: false,
		enableTabScroll: true,
		activeTab: 0,
		height: 455,
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
			purgePanel,
			archivePanel
		]
	}

	var usageString = "{/literal}{#str_MessagePolicyWillNotRun#}{literal}";

	{/literal}{if $assignedtobrandslist != ''}{literal}
		usageString = "{/literal}{$assignedtobrandslist}{literal}";
	{/literal}{/if}{literal}

	var settingsPanel = new Ext.Panel({
		id: 'settingsPanel',
		layout: 'form',
		style: 'padding: 5px 5px 3px 5px; border:1px none #8ca9cf; margin: 4px 0px 0px 0px',
		plain: true,
		bodyBorder: false,
		border: false,
		labelWidth: 80,
		items:
		[
			{
				xtype: 'textfield',
				id: 'name',
				name: 'name',
				allowBlank:false,
				maxLength: 50,
				width: 200,
				{/literal}{if $isEdit == 1}{literal}
					value: "{/literal}{$name}{literal}",
				{/literal}{/if}{literal}
				fieldLabel: "{/literal}{#str_LabelName#}{literal}",
				listeners: {'blur': {fn: function(obj){forceAlphaNumeric(obj, false, true)}}},
				post: true
			},
			{
				id: 'brands',
				fieldLabel: "{/literal}{#str_LabelBrands#}{literal}",
				html: "<div style='padding-top: 3px;'>" + usageString + "</div>",
				height: 42,
				autoScroll: true,
				style: { 'margin-bottom': '10px', 'overflow-x': 'none' }
			},
			tabPanel
		]
	});


	var deleteImg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png';
	var addimg = '{/literal}{$webroot}{literal}/utils/ext/images/silk/add.png';

	var dataList = [];

	var dialogFormPanelObj = new Ext.FormPanel(
	{
		id: 'policyForm',
        labelAlign: 'left',
		labelWidth: 130,
		autoHeight: true,
        frame: true,
        bodyStyle: 'padding-left:5px;',
        items: [
			topPanel,
			settingsPanel
		]
    });

    /* create modal window for add and edit */
	var gDialogObj = new Ext.Window({
		id: 'dialog',
		closable: false,
		plain: true,
		modal: true,
		draggable: true,
		title: "{/literal}{$title}{literal}",
		resizable: false,
		layout: 'fit',
		height: 'auto',
		width: 850,
		items: dialogFormPanelObj,
		listeners: {
			'close': {
				fn: function(){
					policyEditWindowExists = false;
				}
			}
		},
		cls: 'left-right-buttons',
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
				handler: function()
				{
					gDialogObj.close();
				}
			},
			{
				text: "{/literal}{#str_ButtonAdd#}{literal}",
				id: 'addEditButton',
				{/literal}{if $isEdit == 0}{literal}
					handler: addsaveHandler,
					text: "{/literal}{#str_ButtonAdd#}{literal}"
				{/literal}{else}{literal}
					handler: editSaveHandler,
					text: "{/literal}{#str_ButtonUpdate#}{literal}"
				{/literal}{/if}{literal}
			}
		]
	});

	gDialogObj.show();

{/literal}{if $optionDESOL}{literal}
	setOptionActiveState('orderedunusedassets');
	setOptionActiveState('notorderedunusedassets');
	setOptionActiveState('guestprojects');
	setOptionActiveState('unsavedprojects');
	setOptionActiveState('notorderedprojects');
{/literal}{/if}{literal}
	setOptionActiveState('orderedprojects');

	setOptionActiveState('orderedarchiveactive');
	setOptionActiveState('notorderedarchiveactive');
}

{/literal}