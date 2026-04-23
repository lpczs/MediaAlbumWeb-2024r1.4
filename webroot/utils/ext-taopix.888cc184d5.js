/* TAOPIX™ Web ExtJS NameSpace                                        */
/* Version: 1.0 - Wednesday, 6th January 2010                         */
/* Copyright (c) 2006 - 2010 TAOPIX™ Limited. All rights reserved     */
/* This source code may only be used by organisations with a valid    */
/* TAOPIX™ Web license in conjunction with normal TAOPIX™ Web usage.  */
/* No portion of it may be redistributed or modified without the      */
/* written permission of TAOPIX™ Limited.                             */
/* See the TAOPIX™ Software License Agreement for more information.   */

Ext.taopix = {
	cgiEncode: function(pSourceString)
	{
		/* encode a string so that it can be added to a url */
		var encodedInputString = escape(pSourceString);

		encodedInputString = encodedInputString.replace('+', '%2B');
		encodedInputString = encodedInputString.replace('/', '%2F');

		return encodedInputString;
	},

	gridSelection2List: function(pTheGrid, pField, pQuote)
	{
		/* convert the grid selection to a list of id's that can be sent to the server */
		var returnList = '';

		if (pTheGrid)
		{
			var selRecords = pTheGrid.getSelectionModel().getSelections();

			for (var rec = 0; rec < selRecords.length; rec++)
			{
				returnList = returnList + pQuote + (selRecords[rec].data[pField]) + pQuote + ',';
			}
			returnList = returnList.slice(0, -1);
		}

		return returnList;
	},

	gridSelection2IDList: function(pTheGrid)
	{
		/* convert the grid selection to a list of id's that can be sent to the server */
		var iDList = '';

		if (pTheGrid)
		{
			var selRecords = pTheGrid.getSelectionModel().getSelections();
			for (var rec = 0; rec < selRecords.length; rec++)
			{
				iDList = iDList + (selRecords[rec].id) + ',';
			}
			iDList = iDList.slice(0, -1);
		}

		return iDList;
	},

	gridSelection2CodeList: function(pTheGrid)
	{
		/* convert the grid selection to a list of codes that can be sent to the server */
		var codeList = '';

		if (pTheGrid)
		{
			var selRecords = pTheGrid.getSelectionModel().getSelections();
			for (var rec = 0; rec < selRecords.length; rec++)
			{
				console.log(selRecords[rec]);
				codeList = codeList + (selRecords[rec].data.code) + ',';
			}
			codeList = codeList.slice(0, -1);
		}

		return codeList;
	},

	gridSelection2LiceseKeyList: function(pTheGrid)
	{
		/* convert the grid selection to a list of licensekey that can be sent to the server */
		var licenseKeyList = '';

		if (pTheGrid)
		{
			var selRecords = pTheGrid.getSelectionModel().getSelections();
			for (var rec = 0; rec < selRecords.length; rec++)
			{
				if (rec != selRecords.length -1)
				{
					licenseKeyList = licenseKeyList + selRecords[rec].data.lkey + '<br>';
				}
				else
				{
					licenseKeyList = licenseKeyList + selRecords[rec].data.lkey;
				}

			}
		}

		return licenseKeyList;
	},

	addURLParam: function(pURLString, pParamString)
	{
		/* add the cgi encoded parameter string to the end of the url string */
		if (pURLString.indexOf('?') == -1)
		{
			pURLString = pURLString + '?';
		}
		else
		{
			pURLString = pURLString + '&';
		}

		pURLString = pURLString + pParamString;

		return pURLString;
	},

	loadJavascript: function(pWindow, pMessage, pURL, pServerParams, pLocalParams, pCallback, pCacheURL)
	{

		/* dynamically load and execute a javascript */
		/* this can be used to request a form from the server */

		var head = document.getElementsByTagName('head')[0];
		var done = false;

		/* add the window base parameters if any have been provided */
		if (pWindow.baseParams)
		{
			var paramString = '';
			for (i in pWindow.baseParams)
			{
				paramString = paramString + i + '=' + this.cgiEncode(pWindow.baseParams[i]) + '&';
			}
			paramString = paramString.slice(0, -1);
			pURL = this.addURLParam(pURL, paramString);
		}

		/* add the server params if any have been provided */
		if (pServerParams)
		{
			var paramString = '';
			for (var i in pServerParams)
			{
				paramString = paramString + i + '=' + this.cgiEncode(pServerParams[i]) + '&';
			}
			paramString = paramString.slice(0, -1);
			pURL = this.addURLParam(pURL, paramString);
		}

		/* if we shouldn't cache the url add a unique parameter */
		if (! pCacheURL)
		{
			pURL = this.addURLParam(pURL, '_dc=' + new Date().getTime());
		}

		pURL = this.addURLParam(pURL, '_lj=1');

		/* mask the window while the call takes place */
		if ((pWindow) && (pMessage != ''))
		{
			pWindow.el.mask(pMessage, 'x-mask-loading');
		}

		/* create a cookie to store the local time */
		createTimeZoneCookie();

		var fileRef = document.createElement('script');
		fileRef.setAttribute('type', 'text/javascript');
		fileRef.setAttribute('src', pURL);

		fileRef.onload = fileRef.onreadystatechange = function()
			{
				if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete'))
				{
					if (pWindow)
					{
						pWindow.el.unmask();
					}

					done = true;

					eval(pCallback+'(pLocalParams)');
					head.removeChild(fileRef);
				}
			};
		if (typeof fileRef != 'undefined')
		{
			head.appendChild(fileRef);
		}
	},

	formPanelPostSuper: function(pFormPanel, pTheForm, pAdditionalParams, pURL, pMessage, pCallback, pClientValidation, pInstance)
	{
		/* main function for handling ajax form posts via extjs */
		/* form objects must have the 'post' parameter set to be included */
		/* additional parameters & baseParams are also included */
		/* the response is handled by the provided callback */
		var isValid = true;

		if (pTheForm)
		{
			isValid = pTheForm.isValid();
		}

		if (isValid)
		{
			/* create a new form panel to handle the form submit */
			/* this is instead of using a data connection */
			/* as it handles some of the low level processing for us */
			var submitFormPanel = new Ext.form.FormPanel({
					waitMsgTarget: true,
					maximized: true,
					header: false,
					border: false,
					closable: false,
					draggable: false,
					resizable: false,
					timeout: 200
				});
			submitFormPanel.render(document.body);

			var submitForm = submitFormPanel.getForm();

			/* add the form fields that need to be submitted */
			if (pTheForm)
			{
				var formFieldLength = pTheForm.items.length;

				for (var i =0; i < formFieldLength; i++)
				{
					var fieldObj = pFormPanel.findById(pTheForm.items.items[i].id);

					if (fieldObj)
					{
						if (fieldObj.post)
						{
							var fieldValue = fieldObj.getValue();
							submitFormPanel.add({
								name: fieldObj.name,
								xtype: 'hidden',
								value: typeof(fieldValue == 'string') ?  Ext.util.Format.trim(fieldValue): fieldValue
							});
						}
					}
				}

				if (pFormPanel)
				{
					customPanels = pFormPanel.findByType('taopixLangPanel');
					customPanels = customPanels.concat(pFormPanel.findByType('taopixCountryPanel'), pFormPanel.findByType('taopixInputPanel'), pFormPanel.findByType('taopixOldFormatInputPanel'), pFormPanel.findByType('ComponentPricePanel'), pFormPanel.findByType('taopixMultiLineLangPanel'));

					for (var i =0; i < customPanels.length; i++)
					{
						var fieldObj = customPanels[i];

						if ((fieldObj) && (fieldObj.post))
						{
							var fieldValue = fieldObj.convertTableToString();
							submitFormPanel.add({
								name: fieldObj.name,
								xtype: 'hidden',
								value: fieldValue
							});
						}
					}
				}
			}

			/* add the base parameters if any have been provided */
			if (pFormPanel)
			{
				if (pFormPanel.baseParams)
				{
					for (i in pFormPanel.baseParams)
					{
						submitFormPanel.add({
							name: i,
							xtype: 'hidden',
							value: typeof(pFormPanel.baseParams[i] == 'string') ?  Ext.util.Format.trim(pFormPanel.baseParams[i]): pFormPanel.baseParams[i]
						});
					}
				}
			}

			//*add any additional parameters if they have been provided */
			if (pAdditionalParams)
			{
				for (var i in pAdditionalParams)
				{
					submitFormPanel.add({
						name: i,
						xtype: 'hidden',
						value: typeof(pAdditionalParams[i] == 'string') ?  Ext.util.Format.trim(pAdditionalParams[i]): pAdditionalParams[i]
					});
				}
			}

			/* CSRF */
			submitFormPanel.add(
			{
				name: 'csrf_token',
				xtype: 'hidden',
				value: Ext.taopix.getCSRFToken()
			});

			/* mask the panel while the call takes place */
			if ((pFormPanel) && (pFormPanel.el) && (pMessage != ''))
			{
				pFormPanel.el.mask(pMessage, 'x-mask-loading');
			}

			/* create a cookie to store the local time */
			createTimeZoneCookie();
			submitFormPanel.doLayout();
			submitForm.submit({
				clientValidation: pClientValidation,
				url: pURL,
				success: function(pActionForm, pActionData)
					{
						/* unmask the panel and execute the callback */
						if (pFormPanel.el)
						{
							pFormPanel.el.unmask();
						}
						pCallback(true, pActionForm, pActionData, pInstance);
					},
				failure: function(pActionForm, pActionData)
					{
						/* unmask the panel */
						if (pFormPanel.el)
						{
							pFormPanel.el.unmask();
						}

						if ((pActionData) && (pActionData.result) && (pActionData.result.errorcode == 0))
						{
							if (logOut) logOut();
							return;
						}

						/* copy and validation errors back to the form */
						if ((pActionForm) && (pActionData.result) && (pActionData.result.errors) && (pTheForm))
						{
							pTheForm.markInvalid(pActionData.result.errors);
						}

						/* display the error */
						switch (pActionData.failureType)
						{
							case Ext.form.Action.CLIENT_INVALID:
								Ext.MessageBox.show({ title: Ext.Msg.taopixErrorText,	msg: Ext.Msg.taopixErrorTextClientInvalid, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING	});
								break;
							case Ext.form.Action.CONNECT_FAILURE:
								Ext.MessageBox.show({ title: Ext.Msg.taopixErrorText,	msg: Ext.Msg.taopixErrorTextConnectFailure, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING	});
								break;
							case Ext.form.Action.SERVER_INVALID:
								Ext.MessageBox.show({ title: Ext.Msg.taopixErrorText,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING	});
								break;
						}

						/* execute the callback */
						pCallback(false, pActionForm, pActionData, pInstance);
					}
			});
		}
	},

	formPanelPost: function(pFormPanel, pTheForm, pAdditionalParams, pURL, pMessage, pCallback, pInstance)
	{
		/* function to handle posting of formpanels */
		this.formPanelPostSuper(pFormPanel, pTheForm, pAdditionalParams, pURL, pMessage, pCallback, true, pInstance);
	},

	formPost: function(pTheWindow, pAdditionalParams, pURL, pMessage, pCallback)
	{
		/* function to handle other posting of values to the server */
		this.formPanelPostSuper(pTheWindow, null, pAdditionalParams, pURL, pMessage, pCallback, false);
	},

	updateDataStore: function(pDataStore, pNewData)
	{
		/* function to handle updating of existing records in the data store */
		for (rec = 0; rec < pNewData.length; rec++)
		{
			var dataRec = pDataStore.getById(pNewData[rec].id);
			if (dataRec)
			{
				var newRecord = new pDataStore.recordType(pNewData[rec]);

				for (var i in newRecord.data)
				{
					dataRec.data[i] = newRecord.data[i];
				}
				dataRec.commit();
			}
		}
	},

	getCSRFToken: function()
	{
		var token = '';
		var metaTag =  document.querySelector('html > head > meta[name="csrf-token"]');

		if (metaTag !== null)
		{
			token = metaTag.getAttribute('content');
		}

		return token;
	},

	/**
	 * Unlocks a user account. By default will prompt with the reauthentication dialog only. If the feature is turned off then a confirmation dialog is shown instead.
	 *
	 * @param {int} pRef Session ID.
	 * @param {int} pRecordID ID of the account to unlock.
	 * @param {string} pReason Reason to be logged in the activity log.
	 * @param {string} pDialogMessage Message to display in the reauthentication dialog.
	 * @param {string} pUnlockMessage Message to show in the loading dialog.
	 * @param {string} pConfirmatation Message Message to show on the confiramtion dialog.
	 * @param {object} pMainWindowObj Reference to the main window object.
	 * @param {object} pGridDataStoreObj Reference to the grid data store.
	 */
	unlockAccount: function(pRef, pRecordID, pReason, pDialogMessage, pUnlockMessage, pConfirmatationMessage, pMainWindowObj, pGridDataStoreObj)
	{
		showAdminReauthDialogue(
		{
			ref: pRef,
			reason: pReason,
			title: Ext.taopix.ReauthenticationDialog.strings.titleAuthenticate,
			message: pDialogMessage,
			showConfirm: true,
			confirmMessage: pConfirmatationMessage,
			confirmPositiveActionLabel: gButtonUnlock,
			success: function()
			{
				var paramArray = {};
				paramArray['id'] = pRecordID;

				Ext.taopix.formPost(pMainWindowObj, paramArray, 'index.php?fsaction=Admin.unlockAccount', pUnlockMessage, onUnlockCustomerCallback);
			}
		});

		function onUnlockCustomerCallback(pUpdated, pTheForm, pActionData)
		{
			if (pUpdated)
			{
				if (pActionData.result.msg)
				{
					Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
				}

				pGridDataStoreObj.reload();
			}
		};
	},

	/**
	 * Builds the unlock accounts grid item.
	 *
	 * @param function pRenderer Renderer to use.
	 * @param function pOnClick Event to execute on click action.
	 * @returns Ext JS component.
	 */
	buildUnlockAccountGridObj: function(pRenderer, pOnClick)
	{
		var grid =
		{
			id:'accountlocked',
			header: '<img src="/utils/ext/ext-3.3.0/resources/images/default/grid/hmenu-lock.png" alt="" />',
			dataIndex: 'accountlocked',
			width: 32,
			sortable: false,
			menuDisabled: true,
			renderer: pRenderer,
			listeners:
			{
				click: function()
				{
					if (typeof pOnClick == 'function')
					{
						pOnClick();
					}
				}
			}
		};

		return grid;
	}
};

Ext.taopix.PagedArrayReader = Ext.extend(Ext.data.JsonReader, {
/* class to allow arrays to be returned as paged data */
/* the first row contains the total row count available */

    readRecords : function(o){
        this.arrayData = o;

        var totalRecords = 0;

        var s = this.meta,
            sid = s ? Ext.num(s.idIndex, s.id) : null,
            recordType = this.recordType,
            fields = recordType.prototype.fields,
            records = [],
            v;

        if(!this.getRoot) {
            this.getRoot = s.root ? this.getJsonAccessor(s.root) : function(p) {return p;};
            if(s.totalProperty) {
                this.getTotal = this.getJsonAccessor(s.totalProperty);
            }
        }

        var root = this.getRoot(o);

		if (root.length > 0)
		{
			totalRecords = root[0][0];

			for(var i = 1, len = root.length; i < len; i++) {
				var n = root[i],
					values = {},
					id = ((sid || sid === 0) && n[sid] !== undefined && n[sid] !== "" ? n[sid] : null);
				for(var j = 0, jlen = fields.length; j < jlen; j++) {
					var f = fields.items[j],
						k = f.mapping !== undefined && f.mapping !== null ? f.mapping : j;
					v = n[k] !== undefined ? n[k] : f.defaultValue;
					v = f.convert(v, n);
					values[f.name] = v;
				}
				var record = new recordType(values, id);
				record.json = n;
				records[records.length] = record;
			}
        }

        return {
            records : records,
            totalRecords : totalRecords
        };
    }
});





/**
 * Fix the webkit design problem
 **/

if (!Ext.isDefined(Ext.webKitVersion)) {
    Ext.webKitVersion = Ext.isWebKit ? parseFloat(/AppleWebKit\/([\d.]+)/.exec(navigator.userAgent)[1], 10) : NaN;
}

/*
* Box-sizing was changed beginning with Chrome v19. For background information, see:
* http://code.google.com/p/chromium/issues/detail?id=124816
* https://bugs.webkit.org/show_bug.cgi?id=78412
* https://bugs.webkit.org/show_bug.cgi?id=87536
* http://www.sencha.com/forum/showthread.php?198124-Grids-are-rendered-differently-in-upcoming-versions-of-Google-Chrome&p=824367
*
* */
if (Ext.isWebKit && Ext.webKitVersion >= 535.2) { // probably not the exact version, but the issues started appearing in chromium 19
    Ext.override(Ext.grid.ColumnModel, {
        getTotalWidth: function (includeHidden) {
            if (!this.totalWidth) {
                var boxsizeadj = 2;
                this.totalWidth = 0;
                for (var i = 0, len = this.config.length; i < len; i++) {
                    if (includeHidden || !this.isHidden(i)) {
                        this.totalWidth += (this.getColumnWidth(i) + boxsizeadj);
                    }
                }
            }
            return this.totalWidth;
        }
    });


    Ext.onReady(function() {
        Ext.get(document.body).addClass('ext-chrome-fixes');
        Ext.util.CSS.createStyleSheet('@media screen and (-webkit-min-device-pixel-ratio:0) {.x-grid3-cell{box-sizing: border-box !important;}}', 'chrome-fixes-box-sizing');
    });
}



/**
 * @file
 *
 * Taopix custom ExtJS components.
 *
 * Description and copywrite information.!!!
 *
 * @author Dasha Salo
 * @version 3.0
 *
 * Created: 07 April 2010
 * Last modified: 20 December 2010
 */

Ext.taopix.AddressPanel = Ext.extend(Ext.Panel, {
    options: {
        excludeFields: '',
        editMode: '0',
        strict: '0',
        fieldWidth: '100',
        ref: '0'
    },

    data: {
        countryCode: '',
        countryName: '',
        contactFirstName: '',
        contactLastName: '',
        companyName: '',
        address1: '',
        address2: '',
        address3: '',
        address4: '',
        add41: '',
        add42: '',
        add43: '',
        city: '',
        countyCode: '',
        countyName: '',
        stateCode: '',
        stateName: '',
        postCode: '',
        region: '',
        regionCode: '',
        telephonenumber: '',
        regtaxnumtype: 0,
        regtaxnum: ''
    },

    getAddressValues: function()
    {
        this.saveFields(1);

        var fields = new Object();
        var exFields = ','+this . options.excludeFields + ',';

        for (var i in this.data)
        {
            if (exFields.indexOf(',' + i + ',') == -1)
            {
                fields[i] = this.data[i];
            }
        }

        return fields;
    },

    restoreFields: function()
    {
        var elFirstname = Ext.getCmp('maincontactfname');
        var elLastname = Ext.getCmp('maincontactlname');
        var elCompany = Ext.getCmp('maincompanyname');
        var elAdd1 = Ext.getCmp('mainaddress1');
        var elAdd2 = Ext.getCmp('mainaddress2');
        var elAdd3 = Ext.getCmp('mainaddress3');
        var elAdd4 = Ext.getCmp('mainaddress4');
        var elAdd41 = Ext.getCmp('mainadd41');
        var elAdd42 = Ext.getCmp('mainadd42');
        var elAdd43 = Ext.getCmp('mainadd43');
        var elCity = Ext.getCmp('maincity');
        var elCounty = Ext.getCmp("maincounty");
        var elState = Ext.getCmp("mainstate");
        var elPostcode = Ext.getCmp("mainpostcode");
        var elCountry = Ext.getCmp("countrylist");
        var elCountylist = Ext.getCmp("countylist");
        var elStatelist = Ext.getCmp("statelist");
        var elTelephonenumber = Ext.getCmp('maintelephonenumber');
        var elRegisteredTaxNumberType = Ext.getCmp('regtaxnumtype');
		var elRegisteredTaxNumber = Ext.getCmp('regtaxnum');

        if (elFirstname) {
            elFirstname.setValue(this.data.contactFirstName);
        }
        if (elLastname) {
            elLastname.setValue(this.data.contactLastName);
        }
        if (elCompany) {
            elCompany.setValue(this.data.companyName);
        }
        if (elAdd1) {
            elAdd1.setValue(this.data.address1);
        }
        if (elAdd2) {
            elAdd2.setValue(this.data.address2);
        }
        if (elAdd3) {
            elAdd3.setValue(this.data.address3);
        }
        if (elAdd4) {
            elAdd4.setValue(this.data.address4);
        }
        if (elAdd41) {
            elAdd41.setValue(this.data.add41);
        }
        if (elAdd42) {
            elAdd42.setValue(this.data.add42);
        }
        if (elAdd43) {
            elAdd43.setValue(this.data.add43);
        }
        if (elCity) {
            elCity.setValue(this.data.city);
        }
        if (elPostcode) {
            elPostcode.setValue(this.data.postCode);
        }
        if (elRegisteredTaxNumberType) {
            elRegisteredTaxNumberType.setValue(this.data.regtaxnumtype);
        }
        if (elRegisteredTaxNumber) {
            elRegisteredTaxNumber.setValue(this.data.regtaxnum);
        }
        if (elCountry) {
            elCountry.setValue(this.data.countryCode);
        }

        if (elCounty) {
            elCounty.setValue(this.data.countyName);
        }
        if (elState) {
            elState.setValue(this.data.stateName);
        }
        if (elCountylist)
        {
            elCountylist.setValue(this.data.countyCode);
        }
        if (elStatelist)
        {
            elStatelist.setValue(this.data.stateCode);
        }
        this.data.countyCode = '';
        this.data.stateCode = '';

        if (elTelephonenumber) {
            elTelephonenumber.setValue(this.data.telephonenumber);
        }

        /* removes validation errors on page load from the component */
        if (this)
        {
            var formPanel = this.findParentBy(
                function(p)
                {
                    if (p.isXType('form'))
                    {
                        p.getForm().items.each(function(item){
                            if (item.clearInvalid) item.clearInvalid();
                        });
                    }
                }
                );
        }
    },

    saveFields: function(pIsFinalSave)
    {
        var elFirstname = Ext.getCmp('maincontactfname');
        var elLastname = Ext.getCmp('maincontactlname');
        var elCompany = Ext.getCmp('maincompanyname');
        var elAdd1 = Ext.getCmp('mainaddress1');
        var elAdd2 = Ext.getCmp('mainaddress2');
        var elAdd3 = Ext.getCmp('mainaddress3');
        var elAdd4 = Ext.getCmp('mainaddress4');
        var elAdd41 = Ext.getCmp('mainadd41');
        var elAdd42 = Ext.getCmp('mainadd42');
        var elAdd43 = Ext.getCmp('mainadd43');
        var elCity = Ext.getCmp('maincity');
        var elCounty = Ext.getCmp("maincounty");
        var elState = Ext.getCmp("mainstate");
        var elPostcode = Ext.getCmp("mainpostcode");
        var elCountry = Ext.getCmp("countrylist");
        var elCountylist = Ext.getCmp("countylist");
        var elStatelist = Ext.getCmp("statelist");
		var elTelephonenumber = Ext.getCmp('maintelephonenumber');
		var elRegisteredTaxNumberType = Ext.getCmp('regtaxnumtype');
		var elRegisteredTaxNumber = Ext.getCmp('regtaxnum');

        if (elFirstname) {
            this.data.contactFirstName = elFirstname.getValue();
        } else {
            this.data.contactFirstName = '';
        }
        if (elLastname) {
            this.data.contactLastName = elLastname.getValue();
        } else {
            this.data.contactLastName = '';
        }
        if (elCompany) {
            this.data.companyName = elCompany.getValue();
        } else {
            this.data.companyName = '';
        }
        if (elAdd1) {
            this.data.address1 = elAdd1.getValue();
        } else {
            this.data.address1 = '';
        }
        if (elAdd2) {
            this.data.address2 = elAdd2.getValue();
        } else {
            this.data.address2 = '';
        }
        if (elAdd3) {
            this.data.address3 = elAdd3.getValue();
        } else {
            this.data.address3 = '';
        }
        if (elAdd4) {
            this.data.address4 = elAdd4.getValue();
        } else {
            this.data.address4 = '';
        }
        if (elAdd41) {
            this.data.add41 = elAdd41.getValue();
        } else {
            this.data.add41 = '';
        }
        if (elAdd42) {
            this.data.add42 = elAdd42.getValue();
        } else {
            this.data.add42 = '';
        }
        if (elAdd43) {
            this.data.add43 = elAdd43.getValue();
        } else {
            this.data.add43 = '';
        }
        if (elCity) {
            this.data.city = elCity.getValue();
        } else {
            this.data.city = '';
        }
        if (elCounty) {
            this.data.countyName = elCounty.getValue();
        } else {
            this.data.countyName = '';
        }
        if (elState) {
            this.data.stateName = elState.getValue();
        } else {
            this.data.stateName = '';
        }
        if (elPostcode) {
            this.data.postCode = elPostcode.getValue();
        } else {
            this.data.postCode = '';
        }
        if (elRegisteredTaxNumberType) {
            this.data.regtaxnumtype = elRegisteredTaxNumberType.getValue();
        } else {
            this.data.regtaxnumtype = 0;
        }
        if (elRegisteredTaxNumber) {
            this.data.regtaxnum = elRegisteredTaxNumber.getValue();
        } else {
            this.data.regtaxnum = '';
        }
        if (elCountry)
        {
            this.data.countryCode = elCountry.getValue();
            this.data.countryName = elCountry.getRawValue();
        }
        else
        {
            this.data.countryCode = '';
            this.data.countryName = '';
        }

        this.data.region = 'STATE';
        this.data.regionCode = '';
        if (elCountylist)
        {
            this.data.countyName = elCountylist.getRawValue();
            this.data.region = 'COUNTY';
            this.data.regionCode = elCountylist.getValue()/*this.data.countyCode*/;
            this.data.stateCode = '';
        }
        if (elStatelist)
        {
            this.data.stateName = elStatelist.getRawValue();
            this.data.region = 'STATE';
            this.data.regionCode = elStatelist.getValue() /*this.data.stateCode*/;
            this.data.countyCode = '';
        }

        if (elTelephonenumber) {
            this.data.telephonenumber = elTelephonenumber.getValue();
        } else {
            this.data.telephonenumber = '';
        }

        if (pIsFinalSave == 0)
        {
            this.data.countyName = '';
            this.data.stateName = '';
        }
    },

    init: function(){
        this.restoreFields();
        this.saveFields(0);

        var countryDiv = Ext.query('#'+this.id+' .x-form-item');
        if (countryDiv) countryDiv[0].style.marginBottom = '7px';

        countrySel =  Ext.getCmp('countrylist');
        if (countrySel) {
            countrySel.on('select', function(combo, record, index) {
                this.saveFields(0);
                this.getExtjsFields();
            },this);
        }
    },

    constructor: function(config)
    {
        Ext.apply(this, {
            autoWidth:true
        });
        Ext.taopix.AddressPanel.superclass.constructor.apply(this, arguments);
    },

    successCallBack: function(responseObject)
    {
        addressFields = eval(responseObject.responseText);
        this.removeAll(true);
        this.add(addressFields);
        this.doLayout();
        this.init();

    },

    failureCallback: function()
    {
        Ext.Msg.alert(Ext.Msg.taopixErrorText, Ext.Msg.taopixErrorTextConnectFailure);
    },

    getExtjsFields: function()
    {
        var conn = new Ext.data.Connection({
            extraParams: this.options
        });
        conn.request({
            url: '.?fsaction=AjaxAPI.getAddressForm',
            method: 'GET',
            params: {
                countryCode: this.data.countryCode
            },
            success: this.successCallBack,
            failure: this.failureCallback,
            scope: this
        });
    },

    initComponent: function()
    {
        Ext.taopix.AddressPanel.superclass.initComponent.apply(this,arguments);
        if(this.data.countryCode==undefined) this.data.countryCode = '';
        if(this.data.contactFirstName==undefined) this.data.contactFirstName = '';
        if(this.data.contactLastName==undefined) this.data.contactLastName = '';
        if(this.data.companyName==undefined) this.data.companyName = '';
        if(this.data.address1==undefined) this.data.address1 = '';
        if(this.data.address2==undefined) this.data.address2 = '';
        if(this.data.address3==undefined) this.data.address3 = '';
        if(this.data.address4==undefined) this.data.address4 = '';
        if(this.data.add41==undefined) this.data.add41 = '';
        if(this.data.add42==undefined) this.data.add42 = '';
        if(this.data.add43==undefined) this.data.add43 = '';
        if(this.data.city==undefined) this.data.city = '';
        if(this.data.countyCode==undefined) this.data.countyCode = '';
        if(this.data.countyName==undefined) this.data.countyName = '';
        if(this.data.stateCode==undefined) this.data.stateCode = '';
        if(this.data.stateName==undefined) this.data.stateName = '';
        if(this.data.postCode==undefined) this.data.postCode = '';
        if(this.data.telephonenumber==undefined) this.data.telephonenumber = '';

        if(this.data.regtaxnumtype==undefined) this.data.regtaxnumtype = '';
        if(this.data.regtaxnum==undefined) this.data.regtaxnum = '';

        if(this.options.excludeFields==undefined) this.options.excludeFields= '';
        if(this.options.fieldWidth==undefined) this.options.fieldWidth= '100';
        if(this.options.editMode==undefined) this.options.editMode = '0';
        if(this.options.strict==undefined) this.options.strict = '0';
        if(this.options.ref==undefined) this.options.ref = '0';

        this.on('onRender', function() {
            Ext.taopix.AddressPanel.superclass.onRender.apply(this, arguments);
        }, this);
        this.on('afterRender', function() {
            Ext.taopix.AddressPanel.superclass.afterRender.apply(this, arguments);
            this.getExtjsFields();
        }, this);
    }
});



Ext.taopix.CompanyCombo = Ext.extend(Ext.form.ComboBox,
{
    onchangeFunction: function(){},

    constructor: function(config)
    {
        var objId = '0';
        var objValue = '';

        if(config.options.includeGlobal==undefined) config.options.includeGlobal= '0';
        if(config.options.includeShowAll==undefined) config.options.includeShowAll= '0';
        if(config.options.allLabel==undefined) config.options.allLabel='';
        if(config.options.includeGlobalAndSpecificCompany==undefined) config.options.includeGlobalAndSpecificCompany= '';
        if(config.options.ref==undefined) config.options.ref = '0';
        if(config.options.onchange==undefined) this.onchangeFunction = this.onchangeFunction; else this.onchangeFunction = config.options.onchange;
        if(config.id == undefined) objId = '0'; else objId = config.id;
        if(config.defvalue == undefined) objValue = ''; else objValue = config.defvalue;

        var companyStore = new Ext.data.Store({
            proxy: new Ext.data.HttpProxy({
                url: 'index.php?fsaction=AjaxAPI.callback&ref='+config.options.ref+'&cmd=COMPANIESCOMBO', method: 'GET'
            }),
            reader: new Ext.data.ArrayReader({
                idIndex: 0
            },
            Ext.data.Record.create([
            {
                name: 'id'
            },

            {
                name: 'name'
            },

            {
                name: 'code'
            }
            ])
            ),

            listeners:{
                'beforeload':function(){
                    if (this.lastOptions.params != undefined) this.lastOptions.params['includeGlobal'] = config.options.includeGlobal;
                    if (this.lastOptions.params != undefined) this.lastOptions.params['includeShowAll'] = config.options.includeShowAll;
                    if (this.lastOptions.params != undefined) this.lastOptions.params['allLabel'] = config.options.allLabel;
                    if (this.lastOptions.params != undefined) this.lastOptions.params['includeGlobalAndSpecificCompany'] = config.options.includeGlobalAndSpecificCompany;
                },

                'load':function(){
                    var companyFilterSel = Ext.getCmp(objId);
                    companyFilterSel.setValue(objValue);
                }
            }
        });
        companyStore.load({
            params:{
                includeGlobal: config.options.includeGlobal,
                includeShowAll: config.options.includeShowAll,
                allLabel: config.options.allLabel,
                includeGlobalAndSpecificCompany: config.options.includeGlobalAndSpecificCompany
            }
        });

        config = Ext.apply({
            valueField: 'code',
            displayField: 'name',
            mode: 'remote',
            store: companyStore,
            editable: false,
            forceSelection: true,
            triggerAction: 'all',
            hideLabel:true,
            selectOnFocus:true,
            width:200,
            useID: true,
            post: true,
            hiddenName:'companyName_hn',
            hiddenId:'companyName_hn'
        }, config);

        Ext.taopix.CompanyCombo.superclass.constructor.call(this, config);
    },

    initComponent: function(){
        Ext.taopix.CompanyCombo.superclass.initComponent.apply(this,arguments);
        this.on('onRender', function() {
            Ext.taopix.CompanyCombo.superclass.onRender.apply(this, arguments);
        }, this);
        this.on('afterRender', function() {
            Ext.taopix.CompanyCombo.superclass.afterRender.apply(this, arguments);
        }, this);
        this.on('select', this.onchangeFunction, this);
    }
});

Ext.reg('taopixCompanyCombo', Ext.taopix.CompanyCombo );

var BrandRecordDefinition = Ext.data.Record.create([
    { name: 'id' },
    { name: 'name' },
    { name: 'code' }
]);

Ext.taopix.BrandCombo = Ext.extend(Ext.form.ComboBox,
    {
        onchangeFunction: function(){},

        constructor: function(config)
        {
            var objId = '0';
            var objValue = '';
            var companyCode = '';
            var userPage = '0';

            if(config.options.companyCode !== undefined && config.options.companyCode !== '')
            {
                companyCode = config.options.companyCode;
                userPage = 1;
            }

            if(config.options.includeShowAll==undefined) config.options.includeShowAll= '0';
            if(config.options.allLabel==undefined) config.options.allLabel='';
            if(config.options.ref==undefined) config.options.ref = '0';
            if(config.options.onchange==undefined) this.onchangeFunction = this.onchangeFunction; else this.onchangeFunction = config.options.onchange;
            if(config.id == undefined) objId = '0'; else objId = config.id;
            if(config.defvalue == undefined) objValue = ''; else objValue = config.defvalue;

            var brandStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({
                    url: 'index.php?fsaction=AjaxAPI.callback&ref='+config.options.ref+'&companyCode='+companyCode+'&userpage='+userPage+'&cmd=BRANDCOMBO',
					method: 'GET'
				}),
                reader: new Ext.data.ArrayReader({
                        idIndex: 0
                    },
                    BrandRecordDefinition
                ),

                listeners:{
                    'beforeload':function(){
                        if (this.lastOptions.params != undefined) this.lastOptions.params['includeShowAll'] = config.options.includeShowAll;
                        if (this.lastOptions.params != undefined) this.lastOptions.params['allLabel'] = config.options.allLabel;
                    },

                    'load':function(){
                        if (this.lastOptions.params['includeShowAll'] && this.lastOptions.params['allLabel']) {
                            brandStore.insert(0, new BrandRecordDefinition({id: -1, name: this.lastOptions.params['allLabel'], code: ''}));
                        }

                        var brandFilterSel = Ext.getCmp(objId);
                        brandFilterSel.setValue(objValue);
                    }
                }
            });

            config = Ext.apply({
                valueField: 'id',
                displayField: 'name',
                mode: 'remote',
                store: brandStore,
                editable: false,
                forceSelection: true,
                triggerAction: 'all',
                hideLabel:true,
                selectOnFocus:true,
                width:200,
                useID: true,
                post: true,
                hiddenName:'brandName_hn',
                hiddenId:'brandName_hn'
            }, config);

            Ext.taopix.BrandCombo.superclass.constructor.call(this, config);
        },

        initComponent: function(){
            Ext.taopix.BrandCombo.superclass.initComponent.apply(this,arguments);
            this.on('onRender', function() {
                Ext.taopix.BrandCombo.superclass.onRender.apply(this, arguments);
            }, this);
            this.on('afterRender', function(cmp) {
                Ext.taopix.BrandCombo.superclass.afterRender.apply(this, arguments);

                if (this.options['includeShowAll'] && this.options['allLabel']) {
                    cmp.store.insert(0, new BrandRecordDefinition({id: -1, name: this.options['allLabel'], code: ''}));
                    cmp.setValue(-1);
                }
            }, this);
            this.on('select', this.onchangeFunction, this);
        }
    });

Ext.reg('taopixBrandCombo', Ext.taopix.BrandCombo );

var LicenseKeyRecordDefinition = Ext.data.Record.create([
    { name: 'id' },
    { name: 'name' },
    { name: 'code' }
]);

Ext.taopix.LicenseKeyCombo = Ext.extend(Ext.form.ComboBox,
    {
        onchangeFunction: function(){},

        constructor: function(config)
        {
            var objId = '0';
            var objValue = '';
            var companyCode = '';
            var userPage = '0';

            if(config.options.companyCode !== undefined && config.options.companyCode !== '')
            {
                companyCode = config.options.companyCode;
                userPage = 1;
            }

            if(config.options.includeShowAll==undefined) config.options.includeShowAll= '0';
            if(config.options.allLabel==undefined) config.options.allLabel='';
            if(config.options.ref==undefined) config.options.ref = '0';
            if(config.options.onchange==undefined) this.onchangeFunction = this.onchangeFunction; else this.onchangeFunction = config.options.onchange;
            if(config.id == undefined) objId = '0'; else objId = config.id;
            if(config.defvalue == undefined) objValue = ''; else objValue = config.defvalue;

            var licenseKeyStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({
                    url: 'index.php?fsaction=AjaxAPI.callback&ref='+config.options.ref+'&companyCode='+companyCode+'&userpage='+userPage+'&cmd=LICENSECOMBO',
					method: 'GET'
                }),
                reader: new Ext.data.ArrayReader({
                        idIndex: 0
                    },
                    LicenseKeyRecordDefinition
                ),
                listeners:{
                    'beforeload':function(){
                        if (this.lastOptions.params != undefined) this.lastOptions.params['includeShowAll'] = config.options.includeShowAll;
                        if (this.lastOptions.params != undefined) this.lastOptions.params['allLabel'] = config.options.allLabel;
                    },

                    'load':function(){
                        if (this.lastOptions.params['includeShowAll'] && this.lastOptions.params['allLabel']) {
                            licenseKeyStore.insert(0, new LicenseKeyRecordDefinition({id: '', name: this.lastOptions.params['allLabel']}));
                        }

                        var licenseKeyFilterSel = Ext.getCmp(objId);
                        licenseKeyFilterSel.setValue(objValue);
                    }
                }
            });

            config = Ext.apply({
                valueField: 'id',
                displayField: 'name',
                mode: 'remote',
                store: licenseKeyStore,
                editable: false,
                forceSelection: true,
                triggerAction: 'all',
                hideLabel:true,
                selectOnFocus:true,
                width:200,
                useID: true,
                post: true,
                hiddenName:'licenseKeyName_hn',
                hiddenId:'licenseKeyName_hn'
            }, config);

            Ext.taopix.LicenseKeyCombo.superclass.constructor.call(this, config);
        },

        initComponent: function(){
            Ext.taopix.LicenseKeyCombo.superclass.initComponent.apply(this,arguments);
            this.on('onRender', function() {
                Ext.taopix.LicenseKeyCombo.superclass.onRender.apply(this, arguments);
            }, this);
            this.on('afterRender', function(cmp) {
                Ext.taopix.LicenseKeyCombo.superclass.afterRender.apply(this, arguments);

                if (this.options['includeShowAll'] && this.options['allLabel']) {
                    cmp.store.insert(0, new LicenseKeyRecordDefinition({id: '', name: this.options['allLabel']}));
                    cmp.setValue('');
                }
            }, this);
            this.on('select', this.onchangeFunction, this);
        }
    });

Ext.reg('taopixLicenseKeyCombo', Ext.taopix.LicenseKeyCombo );

var CountryDefinition = Ext.data.Record.create([
    { name: 'id' },
    { name: 'name' }
]);

Ext.taopix.CountryCombo = Ext.extend(Ext.form.ComboBox, {
        onchangeFunction: function(){},

        constructor: function(config)
        {
            var objId = '0';
            var objValue = '';

            if(config.options.includeShowAll==undefined) config.options.includeShowAll= '0';
            if(config.options.allLabel==undefined) config.options.allLabel='';
            if(config.options.ref==undefined) config.options.ref = '0';
            if(config.options.onchange==undefined) this.onchangeFunction = this.onchangeFunction; else this.onchangeFunction = config.options.onchange;
            if(config.id == undefined) objId = '0'; else objId = config.id;
            if(config.defvalue == undefined) objValue = ''; else objValue = config.defvalue;

            var countryStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({
                    url: 'index.php?fsaction=AjaxAPI.callback&ref='+config.options.ref+'&cmd=COUNTRYCOMBO', method: 'GET',
					method: 'GET'
                }),
                reader: new Ext.data.ArrayReader({
                        idIndex: 0
                    },
                    CountryDefinition
                ),
                listeners:{
                    'beforeload':function(){
                        if (this.lastOptions.params != undefined) this.lastOptions.params['includeShowAll'] = config.options.includeShowAll;
                        if (this.lastOptions.params != undefined) this.lastOptions.params['allLabel'] = config.options.allLabel;
                    },

                    'load':function(){
                        if (this.lastOptions.params['includeShowAll'] && this.lastOptions.params['allLabel']) {
                            countryStore.insert(0, new CountryDefinition({id: -1, name: this.lastOptions.params['allLabel'], code: ''}));
                        }

                        var countryFilterSel = Ext.getCmp(objId);
                        countryFilterSel.setValue(objValue);
                    }
                }
            });

            config = Ext.apply({
                valueField: 'id',
                displayField: 'name',
                mode: 'remote',
                store: countryStore,
                editable: false,
                forceSelection: true,
                triggerAction: 'all',
                hideLabel:true,
                selectOnFocus:true,
                width:200,
                useID: true,
                post: true,
                hiddenName:'countryName_hn',
                hiddenId:'countryName_hn'
            }, config);

            Ext.taopix.CountryCombo.superclass.constructor.call(this, config);
        },

        initComponent: function(){
            Ext.taopix.CountryCombo.superclass.initComponent.apply(this,arguments);
            this.on('onRender', function() {
                Ext.taopix.CountryCombo.superclass.onRender.apply(this, arguments);
            }, this);
            this.on('afterRender', function(cmp) {
                Ext.taopix.CountryCombo.superclass.afterRender.apply(this, arguments);

                if (this.options['includeShowAll'] && this.options['allLabel']) {
                    cmp.store.insert(0, new CountryDefinition({id: -1, name: this.options['allLabel']}));
                    cmp.setValue(-1);
                }
            }, this);
            this.on('select', this.onchangeFunction, this);
        }
    });

Ext.reg('taopixCountryCombo', Ext.taopix.CountryCombo );

Ext.taopix.BasicForm = Ext.extend(Ext.form.BasicForm,
{
    isValid: function(e)
    {
        var valid = true;
        var firstInvalidField = null;

        this.items.each(function(f)
        {
            if(!f.validate())
            {
                valid = false;
                f.markInvalid();
                if (firstInvalidField == null) firstInvalidField = f;
            }
        });

        if (firstInvalidField)
        {
            var tab;
            var tabbedPanel = firstInvalidField.findParentBy(
                function(p)
                {
                    if (p.isXType('tabpanel'))
                    {
                        return true;
                    }
                    tab = p;
                }
                );
            if ((!Ext.isEmpty(tab,false)) && (!Ext.isEmpty(tabbedPanel,false)))
            {
                tabbedPanel.activate(tab.getId());
            }
        }
        return valid;
    }
});

Ext.reg('taopixBasicForm', Ext.taopix.BasicForm );

Ext.taopix.FormPanel = Ext.extend(Ext.form.FormPanel,
{
    createForm : function()
    {
        var config = Ext.applyIf({listeners: {}}, this.initialConfig);
        var form = null;

        for (i = 0; i < this.items.length; i++)
        {
            if (this.items[i].xtype == 'tabpanel')
            {
                form = new Ext.taopix.BasicForm(null, config);
                break;
            }
        }

        if (form == null)
        {
            form = new Ext.form.BasicForm(null, config);
        }

        return form;
    },
    initComponent: function()
    {
        Ext.taopix.FormPanel.superclass.initComponent.apply(this,arguments);
    }
});

Ext.reg('taopixFormPanel', Ext.taopix.FormPanel );



/**
 * Taopix Language selector component
 *
 * @param array data.langList
 * @param array data.dataList
 *
 * @param array settings.headers
 * @param array settings.defaultText
 * @param array settings.columnWidth
 * @param array settings.fieldWidth
 * @param array settings.fieldWidth
 * @param array settings.errorMsg
 *
 * @author Dasha Salo
 * @version 1.0
 * @since Version 1.0
 */
Ext.taopix.LangPanel = Ext.extend(Ext.grid.EditorGridPanel, {
    data:
    {
        langList: [],
        dataList: [[],'']
    },

    initialData:
    {
        langList: [],
        dataList: [[],'']
    },

    settings:
    {
        headers:     {
            langLabel: gLabelLanguageName,
            textLabel: gLabelName,
            deletePic: '',
            addPic:''
        },
        defaultText: {
            langBlank: gLabelSelectLanguage,
            textBlank: gExtJsTypeValue,
            defaultValue: ''
        },
        columnWidth: {
            langCol: 100,
            textCol: 100,
            delCol: 35
        },
        fieldWidth:  {
            langField: 100,
            textField: 100
        },
        errorMsg:    {
            blankValue: gTextFieldBlank
        }
    },


    /* Get locolized values from the grid */
    convertTableToString: function()
    {
        var localizedString = "";
        var resultArray = [];
        this.stopEditing(false);

        for (var i=0; i<this.store.data.items.length; i++)
        {
            resultArray.push(this.store.data.items[i].data.langCode + ' ' + this.store.data.items[i].data.textValue.replace(/\t/g, ''));
        }
        localizedString = resultArray.join('<p>');
        return localizedString;
    },


    /* Reset grid to initial values */
    reset: function()
    {
        this.data = this.initialData;
        this.store.loadData(this.data.dataList);
    },

    loadData: function(newData)
    {
        this.data = newData;
        this.initialData = newData;
        this.store.loadData(this.data.dataList);
    },


    /* Add new record to the grid */
    addLang_fnc: function()
    {
        var langGrid = this.findParentByType('taopixLangPanel');
        var langListCombo = langGrid.getBottomToolbar().items.items[0];
        var localizedValueInput = langGrid.getBottomToolbar().items.items[1];
        var langListComboValue = langListCombo.getValue();
        var langListComboText = langListCombo.getRawValue();
        var localizedValueInputValue = localizedValueInput.getValue().replace(/<p>/gi, '');

        if (langListComboValue == '')
        {
            langListCombo.markInvalid(langGrid.settings.errorMsg.blankValue);
            return false;
        }
        if (localizedValueInputValue == '')
        {
            localizedValueInput.markInvalid(langGrid.settings.errorMsg.blankValue);
            return false;
        }

        var defaultData = {
            langCode: langListComboValue,
            langName: langListComboText,
            textValue: localizedValueInputValue
        };
        var r = new langGrid.store.recordType(defaultData);
        r.commit();
        langGrid.store.add(r);

        langListCombo.store.removeAt(langListCombo.store.findExact('lang_id',langListComboValue));

		if (langListComboValue==langGrid.settings.defaultText.defaultValue)
			langListCombo.setValue('');
		else
		{
			if (langGrid.store.findExact('langCode',langGrid.settings.defaultText.defaultValue)>-1)
				langListCombo.setValue('');
			else
				langListCombo.setValue(langGrid.settings.defaultText.defaultValue);
		}

        localizedValueInput.reset();
    },


    /* Component constructor */
    constructor: function(config)
    {
        if (config.data.langList != undefined) this.data.langList = config.data.langList;
        if (config.data.dataList != undefined) this.data.dataList = config.data.dataList;
        if (config.settings.headers != undefined) this.settings.headers = config.settings.headers;
        if (config.settings.defaultText != undefined) this.settings.defaultText = config.settings.defaultText;
        if (config.settings.columnWidth != undefined) this.settings.columnWidth = config.settings.columnWidth;
        if (config.settings.fieldWidth != undefined) this.settings.fieldWidth = config.settings.fieldWidth;
        if (config.settings.errorMsg != undefined) this.settings.errorMsg = config.settings.errorMsg;

        Ext.taopix.LangPanel.superclass.constructor.call(this, config);
    },


    /* Delete record from he grid */
    deleteRecord: function(pKey)
    {
        var langGrid = this;
        var langListCombo = langGrid.getBottomToolbar().items.items[0];
        var recordPos = langGrid.store.findExact('langCode',pKey);
        var record = langGrid.store.getAt(recordPos);

        var newData = {
            lang_id: pKey,
            lang_name: record.data.langName
        };
        var r = new langListCombo.store.recordType(newData);
        r.commit();
        langListCombo.store.add(r);
        langListCombo.store.sort('lang_id','ASC');

		if (pKey==langGrid.settings.defaultText.defaultValue)
			langListCombo.setValue(pKey);

        langGrid.store.removeAt(recordPos);
        return false;
    },


    /* Delete column renderer that shows delete icon and delete link */
    deleteColRenderer: function(pPanel, pRecord)
    {
        return '<div onClick="Ext.getCmp(\''+pPanel.id+'\').deleteRecord(\''+pRecord.data.langCode+'\'); return false;" OnMouseOver="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\') + \' x-btn-over\';" OnMouseOut="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\');"><table cellspacing="0" class="x-btn  x-btn-icon"><tbody class="x-btn-small x-btn-icon-small-left"><tr><td class="x-btn-tl"><i>&nbsp;</i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-ml"><i>&nbsp;</i></td><td class="x-btn-mc"><em unselectable="on" class=""><button type="button" class="x-btn-text " style="background-image: url(&quot;'+pPanel.settings.headers.deletePic+'&quot;);">&nbsp;</button></em></td><td class="x-btn-mr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-bl"><i>&nbsp;</i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i>&nbsp;</i></td></tr></tbody></table></div>';
    },

    isValid: function()
    {
        if (this.convertTableToString() == '') return false;
        return true;
    },


    /* Component initialization */
    initComponent: function()
    {
        var self = this;

        Ext.apply(Ext.util.Format, {
            htmlEncode : function(value){
                return !value ? value : String(value).replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
            },

            htmlDecode : function(value){
                return !value ? value : String(value).replace(/&gt;/g, ">").replace(/&lt;/g, "<").replace(/&quot;/g, '"').replace(/&#039;/g, "'").replace(/&amp;/g, "&");
            }
        });

        Ext.apply(this, {
            store: new Ext.data.ArrayStore({
                data: this.data.dataList,
                fields: [{
                    name: 'langCode'
                }, {
                    name: 'langName'
                }, {
                    name: 'textValue'
                }],
                listeners:
                {
                	load: function(str, recs, options)
                	{
						if (str.getCount()>0)
						{
							if (this.getBottomToolbar())
							{
								var langListCombo = this.getBottomToolbar().items.items[0];

								if (langListCombo)
								{
									if (str.findExact('langCode',this.settings.defaultText.defaultValue)>-1)
										langListCombo.setValue('');
									else
										langListCombo.setValue(this.settings.defaultText.defaultValue);
								}
							}
                		}
                	},
                	scope:this
                }
            }),
			style:
        	{
        		border: '1px solid #b4b8c8'
        	},
            columns: [
            {
                resizable: false,
                header: this.settings.headers.langLabel,
                width: this.settings.columnWidth.langCol,
                sortable: false,
                dataIndex: 'langName',
                menuDisabled: true
            },
            {
                resizable: false,
                id:'textValue',
                header: this.settings.headers.textLabel,
                width: this.settings.columnWidth.textCol,
                sortable: false,
                dataIndex: 'textValue',
                menuDisabled: true,
                editor: new Ext.form.TextField({
                    allowBlank: false
                })
            },
            {
                resizable: false,
                id:'deleteIcon',
                width: this.settings.columnWidth.delCol,
                sortable: false,
                renderer: function(value, p, record, rowIndex, colIndex, store){
                    return self.deleteColRenderer(self, record);
                },
                menuDisabled: true
            }
            ],
            stripeRows: true,
            mode: 'local',
            stateful: true,
            stateId: 'grid',
            columnLines:true,
            trackMouseOver: false,
            clicksToEdit: 1,
            enableColumnMove: false,
            autoEncode: true,
            bbar:
            {
            	xtype:'toolbar',
            	style:
            	{
            		padding: '7px 3px',
            		border: 0
            	},
            	items:
            	[
		            {
		                xtype:'combo',
		                width: this.settings.fieldWidth.langField,
		                mode: 'local',
		                editable: false,
		                forceSelection: true,
		                emptyText:this.settings.defaultText.langBlank,
		                store: new Ext.data.ArrayStore({
		                    id: 0,
		                    fields: ['lang_id', 'lang_name'],
		                    data: this.data.langList,
		                    sortInfo:{
		                        field: 'lang_id',
		                        direction: "ASC"
		                    },
		                    listeners:
		                    {
		                    	load: function(str,recs, options)
		                    	{
		                    		if (this.getBottomToolbar())
		                    		{
			                    		var langListCombo = this.getBottomToolbar().items.items[0];

										if (str.findExact('langCode',this.settings.defaultText.defaultValue)>-1)
											langListCombo.setValue('');
										else
										{
											if (this.store.findExact('langCode',this.settings.defaultText.defaultValue)>-1)
												langListCombo.setValue('');
											else
												langListCombo.setValue(this.settings.defaultText.defaultValue);
										}
									}

		                    	},scope: this
		                    }
		                }),
		                validationEvent:false,
		                post: true,
		                valueField: 'lang_id',
		                displayField: 'lang_name',
		                useID: true,
		                post: true,
		                hideLabel:true,
		                triggerAction: 'all'
		            },


		            {
		                xtype:'textfield',
		                width: this.settings.fieldWidth.textField,
		                emptyText: this.settings.defaultText.textBlank,
		                hideLabel: true,
		                validationEvent:false,
		                style: 'margin-right:15px; margin-left:22px',
		                msgTarget: 'qtip'
		            },
		            new Ext.Button(
		            {
		                handler:this.addLang_fnc,
		                icon: this.settings.headers.addPic,
		                minWidth:30,
		                width: '24px',
		                listeners:
		                {
		                	'afterrender': function()
		                	{
		                		this.getEl().addClass('x-btn-over');
		                	},
		                    'mouseout': function()
		                    {
		                        this.getEl().addClass('x-btn-over');
		                    }
		                }
		            })
	            ]
	    	}
        });

        Ext.taopix.LangPanel.superclass.initComponent.apply(this,arguments);
        this.on('afterRender', function() {
            Ext.taopix.LangPanel.superclass.afterRender.apply(this, arguments);
        }, this);

        this.on('validateedit', function(e) {
            e.value = e.value.replace(/<p>/gi, '').replace(/&lt;p&gt;/gi, '');
        });

		var langListCombo = this.getBottomToolbar().items.items[0];

		if (this.store.getCount()>0)
		{
			if (this.store.findExact('langCode',this.settings.defaultText.defaultValue)>-1)
				langListCombo.setValue('');
			else
				langListCombo.setValue(this.settings.defaultText.defaultValue);
		}
		else
			langListCombo.setValue(this.settings.defaultText.defaultValue);

    }
});
Ext.reg('taopixLangPanel', Ext.taopix.LangPanel);


/**
 * Taopix Language selector component (multiline)
 *
 * @param array data.langList
 * @param array data.dataList
 *
 * @param array settings.headers
 * @param array settings.defaultText
 * @param array settings.columnWidth
 * @param array settings.fieldWidth
 * @param array settings.fieldWidth
 * @param array settings.errorMsg
 */
 Ext.taopix.MultiLineLangPanel = Ext.extend(Ext.grid.EditorGridPanel, {
    data:
    {
        langList: [],
        dataList: [[],'']
    },

    initialData:
    {
        langList: [],
        dataList: [[],'']
    },

    settings:
    {
        headers:     {
            langLabel: gLabelLanguageName,
            textLabel: gLabelName,
            deletePic: '',
            addPic:''
        },
        defaultText: {
            langBlank: gLabelSelectLanguage,
            textBlank: gExtJsTypeValue,
            defaultValue: ''
        },
        columnWidth: {
            langCol: 100,
            textCol: 100,
            delCol: 35
        },
        fieldWidth:  {
            langField: 100,
            textField: 100
        },
        errorMsg:    {
            blankValue: gTextFieldBlank
        }
    },

    /* Get locolized values from the grid */
    convertTableToString: function()
    {
        var localizedString = "";
        var resultArray = [];
        this.stopEditing(false);

        for (var i=0; i<this.store.data.items.length; i++)
        {
            resultArray.push(this.store.data.items[i].data.langCode + ' ' + this.store.data.items[i].data.textValue.replace(/\t/g, ''));
        }
        localizedString = resultArray.join('<p>');
        return localizedString;
    },


    /* Reset grid to initial values */
    reset: function()
    {
        this.data = this.initialData;
        this.store.loadData(this.data.dataList);

		// Reset language list.
		this.getBottomToolbar().find('valueField', 'lang_id')[0].store.loadData(this.data.langList);
    },

    loadData: function(newData)
    {
        this.data = newData;
        this.initialData = newData;
        this.store.loadData(this.data.dataList);
		
		// Update language list.
		this.getBottomToolbar().find('valueField', 'lang_id')[0].store.loadData(this.data.langList);
    },


    /* Add new record to the grid */
    addLang_fnc: function()
    {
        var langGrid = this.findParentByType('taopixMultiLineLangPanel');
        var langListCombo = langGrid.getBottomToolbar().items.items[0];
        var localizedValueInput = langGrid.getBottomToolbar().items.items[1];
        var langListComboValue = langListCombo.getValue();
        var langListComboText = langListCombo.getRawValue();
        var localizedValueInputValue = localizedValueInput.getValue().replace(/<p>/gi, '');

        if (langListComboValue == '')
        {
            langListCombo.markInvalid(langGrid.settings.errorMsg.blankValue);
            return false;
        }
        if (localizedValueInputValue == '')
        {
            localizedValueInput.markInvalid(langGrid.settings.errorMsg.blankValue);
            return false;
        }

        var defaultData = {
            langCode: langListComboValue,
            langName: langListComboText,
            textValue: localizedValueInputValue
        };
        var r = new langGrid.store.recordType(defaultData);
        r.commit();
        langGrid.store.add(r);

        langListCombo.store.removeAt(langListCombo.store.findExact('lang_id',langListComboValue));

		if (langListComboValue==langGrid.settings.defaultText.defaultValue)
			langListCombo.setValue('');
		else
		{
			if (langGrid.store.findExact('langCode',langGrid.settings.defaultText.defaultValue)>-1)
				langListCombo.setValue('');
			else
				langListCombo.setValue(langGrid.settings.defaultText.defaultValue);
		}

        localizedValueInput.reset();
    },


    /* Component constructor */
    constructor: function(config)
    {
        if (config.data.langList != undefined) this.data.langList = config.data.langList;
        if (config.data.dataList != undefined) this.data.dataList = config.data.dataList;
        if (config.settings.headers != undefined) this.settings.headers = config.settings.headers;
        if (config.settings.defaultText != undefined) this.settings.defaultText = config.settings.defaultText;
        if (config.settings.columnWidth != undefined) this.settings.columnWidth = config.settings.columnWidth;
        if (config.settings.fieldWidth != undefined) this.settings.fieldWidth = config.settings.fieldWidth;
        if (config.settings.errorMsg != undefined) this.settings.errorMsg = config.settings.errorMsg;

        Ext.taopix.MultiLineLangPanel.superclass.constructor.call(this, config);
    },


    /* Delete record from he grid */
    deleteRecord: function(pKey)
    {
        var langGrid = this;
        var langListCombo = langGrid.getBottomToolbar().items.items[0];
        var recordPos = langGrid.store.findExact('langCode',pKey);
        var record = langGrid.store.getAt(recordPos);

        var newData = {
            lang_id: pKey,
            lang_name: record.data.langName
        };
        var r = new langListCombo.store.recordType(newData);
        r.commit();
        langListCombo.store.add(r);
        langListCombo.store.sort('lang_id','ASC');

		if (pKey==langGrid.settings.defaultText.defaultValue)
			langListCombo.setValue(pKey);

        langGrid.store.removeAt(recordPos);
        return false;
    },


    /* Delete column renderer that shows delete icon and delete link */
    deleteColRenderer: function(pPanel, pRecord)
    {
        return '<div onClick="Ext.getCmp(\''+pPanel.id+'\').deleteRecord(\''+pRecord.data.langCode+'\'); return false;" OnMouseOver="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\') + \' x-btn-over\';" OnMouseOut="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\');"><table cellspacing="0" class="x-btn  x-btn-icon"><tbody class="x-btn-small x-btn-icon-small-left"><tr><td class="x-btn-tl"><i>&nbsp;</i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-ml"><i>&nbsp;</i></td><td class="x-btn-mc"><em unselectable="on" class=""><button type="button" class="x-btn-text " style="background-image: url(&quot;'+pPanel.settings.headers.deletePic+'&quot;);">&nbsp;</button></em></td><td class="x-btn-mr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-bl"><i>&nbsp;</i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i>&nbsp;</i></td></tr></tbody></table></div>';
    },

    isValid: function()
    {
        if (this.convertTableToString() == '') return false;
        return true;
    },


    /* Component initialization */
    initComponent: function()
    {
        var self = this;

        Ext.apply(Ext.util.Format, {
            htmlEncode : function(value){
                return !value ? value : String(value).replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
            },

            htmlDecode : function(value){
                return !value ? value : String(value).replace(/&gt;/g, ">").replace(/&lt;/g, "<").replace(/&quot;/g, '"').replace(/&#039;/g, "'").replace(/&amp;/g, "&");
            }
        });

        Ext.apply(this, {
            store: new Ext.data.ArrayStore({
                data: this.data.dataList,
                fields: [{
                    name: 'langCode'
                }, {
                    name: 'langName'
                }, {
                    name: 'textValue'
                }],
                listeners:
                {
                	load: function(str, recs, options)
                	{
						if (str.getCount()>0)
						{
							if (this.getBottomToolbar())
							{
								var langListCombo = this.getBottomToolbar().items.items[0];

								if (langListCombo)
								{
									if (str.findExact('langCode',this.settings.defaultText.defaultValue)>-1)
										langListCombo.setValue('');
									else
										langListCombo.setValue(this.settings.defaultText.defaultValue);
								}
							}
                		}
                	},
                	scope:this
                }
            }),
			style:
        	{
        		border: '1px solid #b4b8c8'
        	},
            columns: [
            {
                resizable: false,
                header: this.settings.headers.langLabel,
                width: this.settings.columnWidth.langCol,
                sortable: false,
                dataIndex: 'langName',
                menuDisabled: true
            },
            {
                resizable: false,
                id:'textValue',
                header: this.settings.headers.textLabel,
                width: this.settings.columnWidth.textCol,
                sortable: false,
                dataIndex: 'textValue',
                menuDisabled: true,
                editor: new Ext.form.TextArea({
                    allowBlank: false,
					listeners:
					{
						'blur': function ()
						{
							console.log('blur');
						}
					}
                })
            },
            {
                resizable: false,
                id:'deleteIcon',
                width: this.settings.columnWidth.delCol,
                sortable: false,
                renderer: function(value, p, record, rowIndex, colIndex, store){
                    return self.deleteColRenderer(self, record);
                },
                menuDisabled: true
            }
            ],
            stripeRows: true,
            mode: 'local',
            stateful: true,
            stateId: 'grid',
            columnLines: true,
            trackMouseOver: false,
            clicksToEdit: 1,
            enableColumnMove: false,
            autoEncode: true,
            bbar:
            {
            	xtype: 'toolbar',
            	style:
            	{
            		padding: '7px 3px',
            		border: 0
				},
            	items:
            	[
		            {
		                xtype: 'combo',
		                width: this.settings.fieldWidth.langField,
		                mode: 'local',
		                editable: false,
		                forceSelection: true,
		                emptyText: this.settings.defaultText.langBlank,
		                store: new Ext.data.ArrayStore({
		                    id: 0,
		                    fields: ['lang_id', 'lang_name'],
		                    data: this.data.langList,
		                    sortInfo:{
		                        field: 'lang_id',
		                        direction: "ASC"
		                    },
		                    listeners:
		                    {
		                    	load: function(str,recs, options)
		                    	{
		                    		if (this.getBottomToolbar())
		                    		{
			                    		var langListCombo = this.getBottomToolbar().items.items[0];

										if (str.findExact('langCode',this.settings.defaultText.defaultValue)>-1)
											langListCombo.setValue('');
										else
										{
											if (this.store.findExact('langCode',this.settings.defaultText.defaultValue)>-1)
												langListCombo.setValue('');
											else
												langListCombo.setValue(this.settings.defaultText.defaultValue);
										}
									}

		                    	},scope: this
		                    }
		                }),
		                validationEvent: false,
		                post: true,
		                valueField: 'lang_id',
		                displayField: 'lang_name',
		                useID: true,
		                post: true,
		                hideLabel: true,
		                triggerAction: 'all'
		            },
		            {
		                xtype: 'textarea',
		                width: this.settings.fieldWidth.textField,
						height: 50,
		                emptyText: this.settings.defaultText.textBlank,
		                hideLabel: true,
		                validationEvent: false,
		                style: 'margin-right:15px; margin-left:22px',
		                msgTarget: 'qtip'
					},
		            new Ext.Button(
		            {
		                handler: this.addLang_fnc,
		                icon: this.settings.headers.addPic,
		                minWidth: 30,
		                width: '24px',
						style: 'margin-top:0px',
		                listeners:
		                {
		                	'afterrender': function()
		                	{
		                		this.getEl().addClass('x-btn-over');
		                	},
		                    'mouseout': function()
		                    {
		                        this.getEl().addClass('x-btn-over');
		                    }
		                }
		            })
	            ]
	    	}
        });

        Ext.taopix.MultiLineLangPanel.superclass.initComponent.apply(this,arguments);
        this.on('afterRender', function() {
            Ext.taopix.MultiLineLangPanel.superclass.afterRender.apply(this, arguments);
        }, this);

        this.on('validateedit', function(e) {
            e.value = e.value.replace(/<p>/gi, '').replace(/&lt;p&gt;/gi, '');
            e.value = e.value.replace(/&lt;b&gt;/gi, '<b>').replace(/&lt;\/b&gt;/gi, '</b>');
            e.value = e.value.replace(/&lt;i&gt;/gi, '<i>').replace(/&lt;\/i&gt;/gi, '</i>');
            e.value = e.value.replace(/&lt;em&gt;/gi, '<em>').replace(/&lt;\/em&gt;/gi, '</em>');
            e.value = e.value.replace(/&lt;strong&gt;/gi, '<strong>').replace(/&lt;\/strong&gt;/gi, '</strong>');
            e.value = e.value.replace(/&lt;br&gt;/gi, '<br>').replace(/&lt;\br\/&gt;/gi, '<br/>').replace(/&lt;\br \/&gt;/gi, '<br />');
        });

		var langListCombo = this.getBottomToolbar().items.items[0];

		if (this.store.getCount()>0)
		{
			if (this.store.findExact('langCode',this.settings.defaultText.defaultValue)>-1)
				langListCombo.setValue('');
			else
				langListCombo.setValue(this.settings.defaultText.defaultValue);
		}
		else
			langListCombo.setValue(this.settings.defaultText.defaultValue);

    }
});
Ext.reg('taopixMultiLineLangPanel', Ext.taopix.MultiLineLangPanel);


/**
 * Taopix Country selector component
 *
 * @param array data.langList
 * @param array data.dataList
 *
 * @param array settings.headers
 * @param array settings.defaultText
 * @param array settings.columnWidth
 * @param array settings.fieldWidth
 * @param array settings.fieldWidth
 * @param array settings.errorMsg
 *
 * @author Dasha Salo
 * @version 1.0
 * @since Version 1.0
 */
Ext.taopix.CountryPanel = Ext.extend(Ext.grid.GridPanel, {

    regionsUrl: 'index.php?fsaction=AjaxAPI.getRegionList&',
    dataUrl: 'index.php?fsaction=AjaxAPI.callback&cmd=COUNTRYPANEL&',

    initialData: {
        countryList: [],
        dataList: [],
        removeList: '',
        noAll: ''
    },

    bufData: [],

    data: {
        countryList: [],
        dataList: [],
        removeList: '',
        noAll: '',
        usedInRecord: []
    },

    settings: {
        headers:     {
            coutryCodeLabel: gLabelCode,
            textLabel: gLabelName,
            deletePic: '',
            addPic:''
        },
        defaultText: {
            coutryBlank: gLabelSelectCountry
        },
        columnWidth: {
            codeCol: 100,
            delCol: 35
        },
        fieldWidth:  {
            coutryField: 100,
            regionField: 100
        },
        errorMsg:    {
            blankValue: gTextFieldBlank
        },
        ref: 0,
        global: true,
        requestType: 'SHIPPINGZONES',
        requestParam : 1,
        requestMode: 'EDIT',
        companyCode: ''
    },

    mode:0,
    recordId: 13,


    convertTableToString: function()
    {
        var localizedString = "";

        this.stopEditing(false);
        var resultArray = [];
        for (var i=0; i<this.store.data.items.length; i++)
        {
            resultArray.push(this.store.data.items[i].data.cCode);
        }
        localizedString = resultArray.join(',');

        return localizedString;
    },


    reset: function()
    {
        var countryCombo = this.getBottomToolbar().items.items[0];
        var regionCombo = this.getBottomToolbar().items.items[2];

        this.data = this.initialData;

        this.store.loadData(this.data.dataList);
        countryCombo.store.loadData(this.data.countryList);
        regionCombo.store.loadData([]);

        countryCombo.reset();
        this.init(this);
    },


    getRecordByCountryCode: function(arrayObj, countryCode)
    {
        for (var i=0; i < arrayObj.length; i++) {
            if (arrayObj[i].code == countryCode)
            {
                return arrayObj[i];
                break;
            }
        }
    },


    onDeleteRecord: function(countryCode)
    {
        var countryCombo = this.getBottomToolbar().items.items[0];
        var regionCombo = this.getBottomToolbar().items.items[2];

        var shortCode = countryCode.split('_')[0];

        var countryInfo = this.getRecordByCountryCode(this.bufData, shortCode);

        var recordPos = this.store.findExact('cCode',countryCode);
        var record = this.store.getAt(recordPos);

        this.store.removeAt(recordPos);
        this.store.sort('cCode','ASC');

        regionCombo.store.loadData(this.filterRegionList(shortCode, this.getRecordByCountryCode(this.bufData, shortCode).regions));
        this.removeGlobalRegions(this.data.removeList.split(','), regionCombo);
        regionCombo.reset();
        this.addAll(this, shortCode);

        if ((countryCombo.store.findExact('id',shortCode) < 0))
        {
            if ((countryInfo.hasRegions <= 0) || ((countryInfo.hasRegions > 0) && (regionCombo.store.data.items.length > 0)))
            {
                var r = new countryCombo.store.recordType({
                    no: countryInfo.no,
                    id: shortCode,
                    name: countryInfo.name,
                    blankText: countryInfo.regionBlankText,
                    hasRegions: countryInfo.hasRegions
                });
                r.commit();
                countryCombo.store.add(r);
                countryCombo.store.sort('no', 'ASC');
            }
        }

        if (countryCode.indexOf("_") < 0)
        {
            var noAllArray = this.data.noAll.split(',');

            for (var j=0; j < noAllArray.length; j++)
            {
                if (noAllArray[j] == shortCode) noAllArray.splice(j,1);
            }
            this.data.noAll = noAllArray.join(',');
        }

        return false;
    },


    isDeleteCountry: function(countryCode)
    {
        var countryInfo = this.getRecordByCountryCode(this.bufData, countryCode);

        if (countryInfo.hasRegions == 0)
        {
            return true;
        }

        var countryGridStore = this.store;
        var regionsList = countryInfo.regions.slice(0);

        for (var i=0, codes; i < countryGridStore.data.items.length; i++)
        {
            codes = countryGridStore.data.items[i].data.cCode.split('_');
            if (codes[0] == countryCode)
            {
                for (var j=0; j < regionsList.length; j++)
                {
                    if (regionsList[j][0] == codes[1]) regionsList.splice(j,1);
                }
            }
        }
        var usedList = this.data.removeList.split(',');
        if (this.settings.global == true)
        {
            for (var i = 0, code=''; i < usedList.length; i++)
            {
                code = usedList[i];
                if (code.indexOf('_') > -1)
                {
                    code = code.split('_')[1];
                }
                for (var j=0; j < regionsList.length; j++)
                {
                    if (regionsList[j][0] == code) regionsList.splice(j,1);
                }
            }
        }

        if (regionsList.length == 0)
        {
            return true;
        }
        return false;
    },


    onAddCountry: function()
    {
        var panel = this.findParentByType('taopixCountryPanel');
        var countryCombo = panel.getBottomToolbar().items.items[0];
        var regionCombo = panel.getBottomToolbar().items.items[2];

        var countryValue = countryCombo.getValue();
        var countryText = countryCombo.getRawValue();
        var regionValue = regionCombo.getValue();
        var regionText = regionCombo.getRawValue();

        if (countryValue == '')
        {
            countryCombo.markInvalid(panel.settings.errorMsg.blankValue);
            return false;
        }

        if ((regionValue == '') && (!regionCombo.disabled))
        {
            countryCombo.markInvalid(panel.settings.errorMsg.blankValue);
            return false;
        }

        if (regionValue == '0') regionValue = '';

        var newCode = countryValue;
        var newText = countryText;
        if (regionValue != '')
        {
            newCode+=('_' + regionValue);
            newText+=('<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + regionText);
        }

        var r = new panel.store.recordType({
            cCode: newCode,
            cName: newText,
            deleteIconCountryPanel: ''
        });
        r.commit();
        panel.store.add(r);

        panel.store.sort('cCode','ASC');

        regionCombo.store.loadData(panel.filterRegionList(countryValue, panel.getRecordByCountryCode(panel.bufData, countryValue).regions));
        panel.removeGlobalRegions(panel.data.removeList.split(','), regionCombo);

        regionCombo.reset();
        regionCombo.collapse();


        if (panel.isDeleteCountry(countryValue))
        {
            countryCombo.store.removeAt(countryCombo.store.findExact('id',countryValue));
            regionCombo.emptyText = '';
            regionCombo.disable();
            countryCombo.reset();
        }

        panel.addAll(panel, countryValue);
    },


    getRegions: function(countryCode)
    {
        var self = this;

        var conn = new Ext.data.Connection();
        conn.request({
            url: this.regionsUrl,
            method: 'GET',
            params: {
                "country": countryCode,
                "ref": this.settings.ref
            },
            success: function(responseObject) {
                var result = eval('(' + responseObject.responseText + ')');

                var countryInfo = self.getRecordByCountryCode(self.bufData, result.code);

                countryInfo.regions = result.regions;
                countryInfo.updated = true;

                self.setupRegion(result.code);
            },
            failure: function() {
                Ext.Msg.alert('Status', 'Unable to process request. Please try again later.');
            }
        });
    },


    filterRegionList: function(countryCode, regionListToFilter)
    {
        var countryGridStore2 = this.store;
        var filteredList = regionListToFilter.slice(0);

        for (var i=0, codes; i < countryGridStore2.data.items.length; i++)
        {
            codes = countryGridStore2.data.items[i].data.cCode.split('_');
            if ((codes[0] == countryCode) && (codes[1]))
            {
                for (var j=0; j < filteredList.length; j++)
                {
                    if (filteredList[j][0] == codes[1]) {
                        filteredList.splice(j,1);
                        break;
                    }
                }
            }
        }
        return filteredList;
    },


    addAll: function(obj, countryCode)
    {
        var noAllCountries = obj.data.noAll.split(',');
        var includeAll = true;
        for (var i = 0; i < noAllCountries.length; i++)
        {
            if (noAllCountries[i] == countryCode)
            {
                includeAll = false;
                break;
            }
        }

        if (includeAll)
        {
            var countryInfo = obj.getRecordByCountryCode(obj.bufData, countryCode);
            if (countryInfo.hasRegions > 0)
            {
                var regionCombo = obj.getBottomToolbar().items.items[2];

                if ((obj.store.findExact('cCode',countryCode) < 0) && (regionCombo.store.data.items.length > 0))
                {
                    var r = new regionCombo.store.recordType({
                        id: '0',
                        name: '--- ALL ---'
                    });
                    r.commit();
                    regionCombo.store.add(r);
                    regionCombo.store.sort('id','ASC');
                }
            }
        }
    },


    setupRegion: function(countryCode)
    {
        var regionCombo = this.getBottomToolbar().items.items[2];

        var countryInfo = this.getRecordByCountryCode(this.bufData, countryCode);

        if (countryInfo.hasRegions > 0)
        {
            regionCombo.emptyText = countryInfo.regionBlankText;
            regionCombo.reset();
            regionCombo.store.loadData(this.filterRegionList(countryCode, countryInfo.regions));

            regionCombo.enable();
        }
        else
        {
            regionCombo.emptyText = '';
            regionCombo.disable();
            regionCombo.reset();
        }

        this.removeGlobalRegions(this.data.removeList.split(','), regionCombo);
        this.addAll(this, countryCode);
    },



    onCountryChange: function(combo, record, index)
    {
        var countryInfo = this.getRecordByCountryCode(this.bufData, record.data.id);

        if ((countryInfo.hasRegions > 0) && (countryInfo.updated == false))
        {
            this.getRegions(record.data.id);
        }
        else
        {
            this.setupRegion(record.data.id);
        }
    },


    deleteColRenderer: function(pPanel, pRecord)
    {
        return '<div onClick="Ext.getCmp(\''+pPanel.id+'\').onDeleteRecord(\''+pRecord.data.cCode+'\'); return false;" OnMouseOver="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\') + \' x-btn-over\';" OnMouseOut="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\');"><table cellspacing="0" class="x-btn  x-btn-icon"><tbody class="x-btn-small x-btn-icon-small-left"><tr><td class="x-btn-tl"><i>&nbsp;</i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-ml"><i>&nbsp;</i></td><td class="x-btn-mc"><em unselectable="on" class=""><button type="button" class="x-btn-text " style="background-image: url(&quot;'+pPanel.settings.headers.deletePic+'&quot;);">&nbsp;</button></em></td><td class="x-btn-mr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-bl"><i>&nbsp;</i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i>&nbsp;</i></td></tr></tbody></table></div>';
    },


    afterRenderEvent: function()
    {
        Ext.query("#"+this.getId()+" .x-btn")[0].className = Ext.query("#"+this.getId()+" .x-btn")[0].className + ' x-btn-over';
        Ext.query("#"+this.getId()+" .x-toolbar")[0].setAttribute('style', 'padding:7px 3px !important; border:0  ');
        Ext.query("#"+this.getId())[0].setAttribute('style', 'border:1px solid #b4b8c8 !important; ');
    },

    removeGlobalRegions: function(usedList, regionsCombo)
    {
        if (this.settings.global == true)
        {
            for (var i = 0, code=''; i < usedList.length; i++)
            {
                code = usedList[i];
                if (code.indexOf('_') > -1)
                {
                    code = code.split('_')[1];
                }
                regionsCombo.store.removeAt(regionsCombo.store.findExact('id', code));
            }
        }
    },


    showWarning: function(obj, countryCode)
    {
        if (obj.data.removeList)
        {
            var usedList = obj.data.removeList.split(',');

            for (var i=0, codes; i < obj.store.data.items.length; i++)
            {
                if (obj.store.data.items[i].data.cCode != countryCode)
                {
                    /* if the country has been used twice */
                    codes = obj.store.data.items[i].data.cCode.split('_')[0];
                    if (codes == countryCode)
                    {
                        return 2;
                        break;
                    }

                    /* if the country has been used in the removedCountries for another company code */
                    if (obj.data.usedInRecord.length == 0)
                    {
                        for (var j = 0; j < usedList.length; j++)
                        {
                            if (usedList[j] == countryCode)
                            {
                                return 3;
                                break;
                            }
                        }
                    }
                }
            }

        }
        return 0;
    },


    isValid: function()
    {
        for (var i=0, showWarningRes = 0; i < this.store.data.items.length; i++)
        {
            showWarningRes = this.showWarning(this, this.store.data.items[i].data.cCode);
            if (showWarningRes > 0)
            {
                return showWarningRes;
                break;
            }
        }

        if (this.convertTableToString() == '') return 1;

        return 0;
    },

    reload: function(companyCode)
    {
        this.settings.companyCode = companyCode;

        var countryCombo = this.getBottomToolbar().items.items[0];
        var regionCombo = this.getBottomToolbar().items.items[2];

        if (countryCombo) countryCombo.reset();
        if (regionCombo) regionCombo.reset();

        this.getData('reset');
    },


    getData: function(mode)
    {
        var self = this;

        var conn = new Ext.data.Connection();

        conn.request({
            url: this.dataUrl,
            method: 'GET',
            params: {
                "companyCode":self.settings.companyCode,
                "isGlobal": self.settings.global,
                "requestType": self.settings.requestType,
                "requestParam": self.settings.requestParam,
                "requestMode": self.settings.requestMode
            },
            success: function(responseObject) {

                var obj = eval('(' + responseObject.responseText + ')');

                if ((mode == 'reset') && (obj[0].length == 0))
                {
                    self.data.removeList = obj[1];
                    self.data.noAll = obj[2]; /*was not here*/
                    self.data.usedInRecord = obj[3];

                    var countryCombo = self.getBottomToolbar().items.items[0];/*was not here*/
                    countryCombo.store.loadData(self.data.countryList);/*was not here*/
                    self.init(self);
                }
                else
                {
                    self.data.dataList = obj[0];
                    self.data.noAll = obj[2];
                    self.store.loadData(self.data.dataList);
                    self.initialData = self.data;
                    self.data.removeList = obj[1];
                    self.data.usedInRecord = 'smth';
                    self.init(self);

                }
                self.getView().refresh();
            },
            failure: function() {
                Ext.Msg.alert('Status', 'Unable to process request. Please try again later.');
            }
        });
    },

    init: function(obj)
    {
        var countryCombo = obj.getBottomToolbar().items.items[0];
        var regionsCombo = obj.getBottomToolbar().items.items[2];

        for (var i=0; i< obj.data.countryList.length; i++ )
        {
            obj.bufData.push({
                no: obj.data.countryList[i][0],
                code: obj.data.countryList[i][1],
                name: obj.data.countryList[i][2],
                regionBlankText: obj.data.countryList[i][3],
                hasRegions: obj.data.countryList[i][4],
                regions: [],
                updated: false
            });
        }

        var removeCountries = obj.data.removeList.split(',');
        var removeCountriesCopy = removeCountries.slice(0);
        for (var elem in removeCountriesCopy)
        {
            countryCombo.store.removeAt(countryCombo.store.findExact('id',removeCountriesCopy[elem]));

            if (obj.store.findExact('cCode',removeCountriesCopy[elem]) > -1)
            {
                for(var i=0; i < removeCountries.length; i++)
                {
                    if(removeCountries[i] == removeCountriesCopy[elem])
                    {
                        removeCountries.splice(i,1);
                        break;
                    }
                }
            }
        }

        var regionCombo = obj.getBottomToolbar().items.items[2];
        regionCombo.emptyText = '';
        regionCombo.disable();
        regionCombo.reset();
    },

    constructor: function(config)
    {
        if (config.data.countryList != undefined) this.data.countryList = config.data.countryList;
        /*if (config.data.dataList != undefined) this.data.dataList = config.data.dataList;*/
        if (config.settings.headers != undefined) this.settings.headers = config.settings.headers;
        if (config.settings.defaultText != undefined) this.settings.defaultText = config.settings.defaultText;
        if (config.settings.columnWidth != undefined) this.settings.columnWidth = config.settings.columnWidth;
        if (config.settings.fieldWidth != undefined) this.settings.fieldWidth = config.settings.fieldWidth;
        if (config.settings.errorMsg != undefined) this.settings.errorMsg = config.settings.errorMsg;
        if (config.settings.global != undefined) this.settings.global = config.settings.global;
        if (config.settings.ref != undefined) this.settings.ref = config.settings.ref;
        if (config.settings.requestType != undefined) this.settings.requestType = config.settings.requestType;
        if (config.settings.requestParam != undefined) this.settings.requestParam = config.settings.requestParam;
        if (config.settings.requestMode != undefined) this.settings.requestMode = config.settings.requestMode;
        if (config.settings.companyCode != undefined) this.settings.companyCode = config.settings.companyCode;

        Ext.taopix.CountryPanel.superclass.constructor.call(this, config);
    },

    initComponent: function()
    {
        var self = this;

        Ext.apply(this, {
            store: new Ext.data.ArrayStore({
                fields: [{
                    name: 'cCode'
                }, {
                    name: 'cName'
                }, {
                    name: 'deleteIconCountryPanel'
                }],
                data: []
            }),
            columns: [
            {
                header: this.settings.headers.coutryCodeLabel,
                width: this.settings.columnWidth.codeCol,
                sortable: false,
                dataIndex: 'cCode',
                menuDisabled: true
            },
            {
                id:'cName',
                header: this.settings.headers.textLabel,
                sortable: false,
                dataIndex: 'cName',
                menuDisabled: true
            },
            {
                id:'deleteIconCountryPanel',
                width: this.settings.columnWidth.delCol,
                sortable: false ,
                renderer: function(value, p, record, rowIndex, colIndex, store){
                    return self.deleteColRenderer(self, record);
                },
                menuDisabled: true
            }
            ],
            stripeRows: true,
            autoExpandColumn: 'cName',
            mode: 'local',
            stateful: true,
            stateId: 'grid',
            columnLines:true,
            trackMouseOver: false,
            bbar: [
            {
                xtype:'combo',
                width: this.settings.fieldWidth.coutryField,
                mode: 'local',
                editable: false,
                forceSelection: true,
                emptyText:this.settings.defaultText.coutryBlank,
                store: new Ext.data.ArrayStore({
                    id: 0,
                    fields: ['no','id', 'name', 'blankText', 'hasRegions'],
                    data: this.data.countryList
                }),
                listeners: {
                    'select': function(combo, record, index){
                        self.onCountryChange(combo, record, index);
                    }
                },
                validationEvent:false,
                post: true,
                valueField: 'id',
                displayField: 'name',
                useID: true,
                post: true,
                hideLabel:true,
                triggerAction: 'all'
            },

            {
                xtype: 'tbspacer',
                width: 22
            },

            {
                xtype:'combo',
                width: this.settings.fieldWidth.regionField,
                mode: 'local',
                editable: false,
                forceSelection: true,
                emptyText:'',
                store: new Ext.data.ArrayStore({
                    id: 0,
                    fields: ['id', 'name'],
                    data: [],
                    sortInfo:{
                        field: 'id',
                        direction: "ASC"
                    },
                    remoteSort: false
                }),
                validationEvent:false,
                post: true,
                valueField: 'id',
                displayField: 'name',
                useID: true,
                post: true,
                hideLabel:true,
                triggerAction: 'all'
            },

            {
                xtype: 'tbspacer',
                width: 22
            },

            new Ext.Button({
                handler:this.onAddCountry,
                icon: this.settings.headers.addPic,
                minWidth:20,
                width:24,
                listeners: {
                    'mouseout': function(){
                        this.getEl().addClass('x-btn-over');
                    }
                }
            })
            ]
        });

        Ext.taopix.CountryPanel.superclass.initComponent.apply(this,arguments);

        this.on('afterRender', function() {
            Ext.taopix.CountryPanel.superclass.afterRender.apply(this, arguments);
            this.afterRenderEvent();
        }, this);

        this.getView().getRowClass = function(r, idx, rowParams, ds)
        {
            if (self.showWarning(self, r.data.cCode) > 0)
            {
                return 'processed-row';
            }
            return '';
        };

        this.getData('');
    }
});
Ext.reg('taopixCountryPanel', Ext.taopix.CountryPanel);




/**
 * Taopix Inputs component
 *
 * @author Dasha Salo
 * @version 1.0
 * @since Version 1.0
 *
 * TO DO:
 * - add components validation rules
 * - think output function - formats may vary... need an option how to specify output formats
 */
Ext.taopix.InputPanel = Ext.extend(Ext.grid.EditorGridPanel, {
    initialData: {
        dataList: []
    },

    data:   {
        dataList: []
    },

    config: {
        fieldList: [],
        columnList: [
        {
            title: 'Qty Range Start'
        },
        {
            title: 'Qty Range End'
        },
        {
            title: 'Base Price'
        },
        {
            title: 'Unit Price'
        },
        {
            title: 'Line Subtract'
        }
        ],
        columnWidth: [80, 80, 80, 80, 100],
        addPic: '',
        delPic: '',
        outputFormat: 'NEWPRICES'
    },


    /* Get locolized values from the grid */
    convertTableToString: function()
    {
        var resultString = "";
        var resultArray = [];
        var bufArray = [];
        this.stopEditing(false);

        switch (this.config.outputFormat)
        {
            case 'NEWPRICES':
                for (var i=0; i<this.store.data.items.length; i++)
                {
                    bufArray = [];
                    for( var j = 0; j < this.store.data.items[i].fields.items.length-1; j++)
                    {
                        bufArray.push(this.store.data.items[i].data[this.store.data.items[i].fields.items[j].name]+'');
                    }
                    resultArray.push(bufArray.join('-'));
                }
                resultString = resultArray.join(' ');
                break;
            default:
                resultString = '';
        }

        return resultString;
    },


    /* Validating the fields */
    isValid: function()
    {
        for (var i=0, val='', el; i < this.store.data.items.length; i++)
        {
            for( var j = 0; j < this.store.data.items[i].fields.items.length-1; j++)
            {
                val = this.store.data.items[i].data[this.store.data.items[i].fields.items[j].name];
                el = this.config.fieldList[j];
                if ((el.minValue) && (val*1 <= el.minValue))
                {
                    return false;
                }
            }
        }
        if (this.convertTableToString() == '') return false;
        return true;
    },


    /* Add new record to the grid */
    onAddRecord: function()
    {
        var panel = this.findParentByType('taopixInputPanel');
        var components = panel.getBottomToolbar().items.items;

        for (var i = 0; i < components.length; i++)
        {
            if ((components[i].isValid) && (!components[i].isValid()))
            {
                return false;
            }
        }
        for (var i = 0, cmp, record={};
            i < components.length - 1; i++)

            {
            cmp = components[i];
            record['field'+i] = cmp.getValue();
            cmp.reset();
        }
        record['deleteIcon'] = '';

        var r = new panel.store.recordType(record);
        r.commit();
        panel.store.add(r);
        panel.getView().refresh();
    },


    /* Delete record fromt he grid */
    onDeleteRecord: function(pCode)
    {
        this.store.removeAt(this.store.findExact('field0',pCode));
        return false;
    },


    /* Reset grid to initial values */
    reset: function()
    {
        this.data = this.initialData;
        this.store.loadData(this.data.dataList);
    },

    refresh: function()
    {
        this.getView().refresh();
    },


    /* Delete column renderer that shows delete icon and delete link */
    deleteColRenderer: function(pPanel, pRecord)
    {
        return '<div onClick="Ext.getCmp(\''+pPanel.id+'\').onDeleteRecord(\''+pRecord.data.field0+'\'); return false;" OnMouseOver="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\') + \' x-btn-over\';" OnMouseOut="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\');"><table cellspacing="0" class="x-btn  x-btn-icon"><tbody class="x-btn-small x-btn-icon-small-left"><tr><td class="x-btn-tl"><i>&nbsp;</i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-ml"><i>&nbsp;</i></td><td class="x-btn-mc"><em unselectable="on" class=""><button type="button" class="x-btn-text " style="background-image: url(&quot;'+pPanel.config.delPic+'&quot;);">&nbsp;</button></em></td><td class="x-btn-mr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-bl"><i>&nbsp;</i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i>&nbsp;</i></td></tr></tbody></table></div>';
    },

    checkWarning: function(obj, value, metaData, record, rowIndex, colIndex, store)
    {
        comp = obj.config.fieldList[colIndex];
        if ((comp.minValue) && (comp.minValue >= value*1))
        {
            return '<div class="processed-row">'+value+'</div>';
        }
        else
        {
            return value;
        }
    },

    afterRenderEvent: function()
    {
        Ext.query("#"+this.getId()+" .x-btn")[0].className = Ext.query("#"+this.getId()+" .x-btn")[0].className + ' x-btn-over';
        Ext.query("#"+this.getId()+" .x-toolbar")[0].setAttribute('style', 'padding:5px 3px !important; border:0  ');
        Ext.query("#"+this.getId())[0].setAttribute('style', 'border:1px solid #b4b8c8 !important; ');
    },

    constructor: function(config)
    {
        if (config.data.dataList != undefined) this.data.dataList = config.data.dataList;
        if (config.config.fieldList != undefined) this.config.fieldList = config.config.fieldList;
        if (config.config.columnList != undefined) this.config.columnList = config.config.columnList;
        if (config.config.columnWidth != undefined) this.config.columnWidth = config.config.columnWidth;
        if (config.config.addPic != undefined) this.config.addPic = config.config.addPic;
        if (config.config.delPic != undefined) this.config.delPic = config.config.delPic;
        if (config.config.outputFormat != undefined) this.config.outputFormat = config.config.outputFormat;

        Ext.taopix.InputPanel.superclass.constructor.call(this, config);
    },

    /* Component initialization */
    initComponent: function()
    {
        var self = this;

        var storeFields = [];
        var gridColumns = [];
        var bbarFields  = [];

        for (var i = 0, comp; i < this.config.fieldList.length; i++)
        {
            comp = this.config.fieldList[i];
            storeFields.push({
                name: 'field'+i
            });

            switch(comp.fieldType)
            {
                case 'text':
                    gridColumns.push({
                        header: this.config.columnList[i].title,
                        width: this.config.columnWidth[i],
                        dataIndex: 'field'+i,
                        menuDisabled: true,
                        editor: new Ext.form.TextField({
                            allowBlank: false
                        })
                    });
                    bbarFields.push({
                        xtype:'textfield',
                        width: comp.width,
                        emptyText: comp.emptyText,
                        allowBlank: comp.allowBlank,
                        hideLabel: comp.hideLabel,
                        fieldLabel: comp.fieldLabel,
                        minLength: comp.minLength,
                        maxLength: comp.maxLength,
                        style: 'margin-right:7px'
                    });
                    break;
                case 'number':
                    gridColumns.push(
                    {
                        header: this.config.columnList[i].title,
                        width: this.config.columnWidth[i],
                        dataIndex: 'field'+i,
                        menuDisabled: true,
                        editor: new Ext.form.NumberField(
                        {
                            allowBlank: false,
                            minValue: comp.minValue, /*maxValue: comp.maxValue, */
                            allowDecimals: comp.allowDecimals,
                            decimalPrecision: comp.decimalPrecision,
                            decimalSeparator: comp.decimalSeparator,
                            allowNegative: comp.allowNegative
                        }
                        ),
                        renderer: function(value, metaData, record, rowIndex, colIndex, store)
                        {
                            return self.checkWarning(self,value, metaData, record, rowIndex, colIndex, store);
                        }
                    }
                    );
                    bbarFields.push(
                    {
                        xtype:'numberfield',
                        width: comp.width,
                        emptyText: comp.emptyText,
                        allowBlank: comp.allowBlank,
                        hideLabel: comp.hideLabel,
                        fieldLabel: comp.fieldLabel,
                        minValue: comp.minValue, /*maxValue: comp.maxValue,*/
                        allowDecimals: comp.allowDecimals,
                        decimalPrecision: comp.decimalPrecision,
                        decimalSeparator: comp.decimalSeparator,
                        allowNegative: comp.allowNegative,
                        style: 'margin-right:7px'
                    }
                    );
                    break;
                default:
                    gridColumns.push({
                        header: this.config.columnList[i].title,
                        width: this.config.columnWidth[i],
                        dataIndex: 'field'+i,
                        menuDisabled: true,
                        editor: new Ext.form.TextField({
                            allowBlank: false
                        })
                    });
                    bbarFields.push({
                        xtype:'textfield',
                        width: comp.width,
                        emptyText: comp.emptyText,
                        allowBlank: comp.allowBlank,
                        hideLabel: comp.hideLabel,
                        fieldLabel: comp.fieldLabel,
                        minLength: comp.minLength,
                        maxLength: comp.maxLength,
                        style: 'margin-right:7px'
                    });
            }
        }
        bbarFields.push({
            xtype:'button',
            handler:this.onAddRecord,
            icon: this.config.addPic,
            minWidth:24,
            width:24,
            listeners: {
                'mouseout': function(){
                    this.getEl().addClass('x-btn-over');
                }
            }
        });
        storeFields.push({
            name: 'fieldDel'
        });
        gridColumns.push({
            id:'deleteIcon',
            width: 35,
            sortable: false,
            renderer: function(value, p, record, rowIndex, colIndex, store){
                return self.deleteColRenderer(self, record);
            },
            menuDisabled: true
        });

        Ext.apply(Ext.util.Format, {
            htmlEncode : function(value){
                return !value ? value : String(value).replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
            },

            htmlDecode : function(value){
                return !value ? value : String(value).replace(/&gt;/g, ">").replace(/&lt;/g, "<").replace(/&quot;/g, '"').replace(/&#039;/g, "'").replace(/&amp;/g, "&");
            }
        });

        Ext.apply(this, {
            store: new Ext.data.ArrayStore({
                data: this.data.dataList,
                fields: storeFields
            }),
            cm: new Ext.grid.ColumnModel({
                columns: gridColumns
            }),
            stripeRows: true,
            mode: 'local',
            stateful: true,
            stateId: 'grid',
            columnLines:true,
            clicksToEdit: 1,
            trackMouseOver: false,
            autoEncode: true,
            bbar: [bbarFields]
        });

        this.on('validateedit', function(e) {
            this.getView().refresh();
        });

        Ext.taopix.InputPanel.superclass.initComponent.apply(this,arguments);
        this.on('afterRender', function() {
            Ext.taopix.InputPanel.superclass.afterRender.apply(this, arguments);
            this.afterRenderEvent();
        }, this);

        this.initialData = this.data;
    }
});
Ext.reg('taopixInputPanel', Ext.taopix.InputPanel);


/**
 * Taopix Inputs component - Old format
 *
 * @author Dasha Salo
 * @version 1.0
 * @since Version 1.0
 *
 * TO DO:
 * - add components validation rules
 * - think output function - formats may vary... need an option how to specify output formats
 */
Ext.taopix.InputOldFormatPanel = Ext.extend(Ext.grid.EditorGridPanel, {
    initialData: [],
    data:   [],

    config: {
        fieldList: [],
        columnList: [],
        columnWidth: [],
        addPic: '',
        delPic: '',
        outputFormat: 'OLDPRICES',
        startMinValue:0
    },

    convertTableToString: function()
    {
        var resultString = ""; /*trailingDecimals*/
        var resultArray = [];
        var bufArray = [];

        this.stopEditing(false);

        for (var i = 0; i < this.store.data.items.length; i++)
        {
            bufArray = [];
            for( var j = 1, cmp; j < this.store.data.items[i].fields.items.length-1; j++)
            {
                var cmp = this.config.fieldList[j-1];
                bufArray.push(((this.store.data.items[i].data[this.store.data.items[i].fields.items[j].name])*1).toFixed(cmp.trailingDecimals) + '');
            }
            resultArray.push(bufArray.join('-'));
        }
        resultString = resultArray.join(' ');

        return resultString;
    },


    onDeleteRecord: function(pCode)
    {
        this.store.removeAt(this.store.findExact('idfield',pCode*1));
        return false;
    },


    reset: function()
    {
        this.data = this.initialData;
        this.store.loadData(this.data);
    },


    refresh: function()
    {
        this.getView().refresh();
    },


    onAddRecord: function()
    {
        var panel = this.findParentByType('taopixOldFormatInputPanel');
        var components = panel.getBottomToolbar().items.items;
        var ranges = [];

        for (var i = 0; i < components.length - 1; i++)
        {
            if (components[i].range != false)
            {
                if (components[i].rangeStart)
                {
                    ranges[components[i].range] = components[i].getValue();
                }
                else
                {
                    components[i].minValue = ranges[components[i].range];
                }
            }

            if ((components[i].isValid) && (!components[i].isValid()))
            {
                return false;
            }
        }

        var maxId = 0;
        for (var i = 0; i < panel.store.data.items.length; i++)
        {
            if (panel.store.data.items[i].data.idfield > maxId)
            {
                maxId = panel.store.data.items[i].data.idfield;
            }
        }

        var record={};
        record['idfield'] = maxId + 1;
        for (var i = 0, cmp; i < components.length - 1; i++)
        {
            cmp = components[i];
            record['field'+i] = cmp.getValue();
            cmp.reset();
        }
        record['deleteIcon'] = '';

        var r = new panel.store.recordType(record);
        r.commit();
        panel.store.add(r);
        panel.getView().refresh();
    },


    deleteColRenderer: function(pPanel, pRecord)
    {
        return '<div onClick="Ext.getCmp(\''+pPanel.id+'\').onDeleteRecord(\''+pRecord.data.idfield+'\'); return false;" OnMouseOver="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\') + \' x-btn-over\';" OnMouseOut="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\');"><table cellspacing="0" class="x-btn  x-btn-icon"><tbody class="x-btn-small x-btn-icon-small-left"><tr><td class="x-btn-tl"><i>&nbsp;</i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-ml"><i>&nbsp;</i></td><td class="x-btn-mc"><em unselectable="on" class=""><button type="button" class="x-btn-text " style="background-image: url(&quot;'+pPanel.config.delPic+'&quot;);">&nbsp;</button></em></td><td class="x-btn-mr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-bl"><i>&nbsp;</i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i>&nbsp;</i></td></tr></tbody></table></div>';
    },


    checkWarning: function(obj, value, metaData, record, rowIndex, colIndex, store)
    {
        var comp = obj.config.fieldList[colIndex];
        var rangeMin = comp.minValue;


        if ((comp) && (comp.range) && (comp.rangeEnd))
        {
            if ((obj.config.fieldList[colIndex-1]) && (obj.config.fieldList[colIndex-1].range == comp.range) && (obj.config.fieldList[colIndex-1].rangeStart))
            {
                rangeMin = record.data['field'+(colIndex-1)];
            }
            else
            {
            /* if range inputs don't go one after another then implement the loop here */
            }
        }

        if ((comp) && (comp.range) && (comp.rangeStart))
        {
            if ((obj.config.fieldList[colIndex+1]) && (obj.config.fieldList[colIndex+1].range == comp.range) && (obj.config.fieldList[colIndex+1].rangeEnd))
            {
                if (store.data.items[rowIndex-1])
                {
                    if (store.data.items[rowIndex].data['field'+colIndex] *1 < store.data.items[rowIndex-1].data['field'+(colIndex + 1)] * 1)
                    {
                        return '<div class="processed-row">'+(value*1).toFixed(comp.trailingDecimals) +'</div>';
                    }
                }
            }
        }

        if (((comp.minValue) && (comp.minValue > value*1)) || (rangeMin > value*1))
        {
            return '<div class="processed-row">'+(value*1).toFixed(comp.trailingDecimals) +'</div>';
        }
        else
        {
            return (value*1).toFixed(comp.trailingDecimals);
        }
    },


    isValid: function()
    {
        this.stopEditing(false);

        if (this.convertTableToString() == '') return 1;

        if (this.store.data.items[0])
        {
            var firstValue =  this.store.data.items[0].data[this.store.data.items[0].fields.items[1].name];
            if (firstValue*1 != this.config.startMinValue)
            {
                return 2;
            }
        }

        for (var i=0, val='', el; i < this.store.data.items.length; i++)
        {
            for( var j = 1, elemIndex = 0; j < this.store.data.items[i].fields.items.length-1; j++)
            {
                val = this.store.data.items[i].data[this.store.data.items[i].fields.items[j].name];

                elemIndex = j - 1;
                el = this.config.fieldList[elemIndex];

                var rangeMin = el.minValue;

                if ((el.range) && (el.rangeEnd))
                {
                    if ((this.config.fieldList[elemIndex-1]) && (this.config.fieldList[elemIndex-1].range == el.range) && (this.config.fieldList[elemIndex-1].rangeStart))
                    {
                        rangeMin = this.store.data.items[i].data[this.store.data.items[i].fields.items[j-1].name];
                    }
                    else
                    {
                    /* if range inputs don't go one after another then implement the loop here */
                    }
                }

                if ((el) && (el.range) && (el.rangeStart))
                {
                    if ((this.config.fieldList[elemIndex+1]) && (this.config.fieldList[elemIndex+1].range == el.range) && (this.config.fieldList[elemIndex+1].rangeEnd))
                    {
                        if (this.store.data.items[i-1])
                        {
                            if (this.store.data.items[i].data['field'+elemIndex] *1 < this.store.data.items[i-1].data['field'+(elemIndex + 1)] * 1)
                            {
                                return 4;
                            }
                        }
                    }
                }

                if (((el.minValue) && (val*1 < el.minValue)) || (rangeMin*1 > val*1))
                {
                    return 3;
                }
            }
        }

        return 0;
    },

    afterRenderEvent: function()
    {
        Ext.query("#"+this.getId()+" .x-btn")[0].className = Ext.query("#"+this.getId()+" .x-btn")[0].className + ' x-btn-over';
        Ext.query("#"+this.getId()+" .x-toolbar")[0].setAttribute('style', 'padding:5px 3px !important; border:0  ');
        Ext.query("#"+this.getId())[0].setAttribute('style', 'border:1px solid #b4b8c8 !important; ');
    },


    constructor: function(config)
    {
        if (config.data != undefined) this.data = config.data;
        if (config.config.fieldList != undefined) this.config.fieldList = config.config.fieldList;
        if (config.config.columnList != undefined) this.config.columnList = config.config.columnList;
        if (config.config.columnWidth != undefined) this.config.columnWidth = config.config.columnWidth;
        if (config.config.addPic != undefined) this.config.addPic = config.config.addPic;
        if (config.config.delPic != undefined) this.config.delPic = config.config.delPic;
        if (config.config.outputFormat != undefined) this.config.outputFormat = config.config.outputFormat;
        if (config.config.startMinValue != undefined) this.config.startMinValue = config.config.startMinValue;

        Ext.taopix.InputOldFormatPanel.superclass.constructor.call(this, config);
    },

    /* Component initialization */
    initComponent: function()
    {
        var self = this;

        var storeFields = [];
        var gridColumns = [];
        var bbarFields  = [];

        storeFields.push({
            name: 'idfield'
        });

        var fixedPrecisionNumberField = Ext.extend(Ext.form.NumberField, {
            setValue : function(v){
                v = typeof v == 'number' ? v : String(v).replace(this.decimalSeparator, ".");
                v = isNaN(v) ? '' : String(v).replace(".", this.decimalSeparator);
                v = isNaN(v) ? '' : this.fixPrecision(String(v).replace(".", this.decimalSeparator));
                return Ext.form.NumberField.superclass.setValue.call(this, v);
            }
        });

        for (var i = 0, comp, range, rangeStart, rangeEnd; i < this.config.fieldList.length; i++)
        {
            comp = this.config.fieldList[i];
            storeFields.push({
                name: 'field'+i
            });

            range      = false;
            rangeStart = false;
            rangeEnd   = false;
            if (comp.range)
            {
                range = comp.range;
                if (comp.rangeStart) rangeStart = comp.rangeStart;
                if (comp.rangeEnd)   rangeEnd   = comp.rangeEnd;
            }
            switch(comp.fieldType)
            {
                case 'text':
                    gridColumns.push({
                        resizable: false,
                        fixed: true,
                        header: this.config.columnList[i].title,
                        width: this.config.columnWidth[i],
                        dataIndex: 'field'+i,
                        menuDisabled: true,
                        align:'right',
                        editor: new Ext.form.TextField({
                            allowBlank: false
                        })
                    });
                    bbarFields.push({
                        xtype:'textfield',
                        width: comp.width,
                        emptyText: comp.emptyText,
                        allowBlank: comp.allowBlank,
                        hideLabel: comp.hideLabel,
                        fieldLabel: comp.fieldLabel,
                        minLength: comp.minLength,
                        maxLength: comp.maxLength,
                        style: 'margin-right:10px'
                    });
                    break;

                case 'number':
                    gridColumns.push({
                        header: this.config.columnList[i].title,
                        width: this.config.columnWidth[i],
                        dataIndex: 'field'+i,
                        menuDisabled: true,
                        align:'right',
                        resizable: false,
                        fixed: true,
                        editor: new fixedPrecisionNumberField({
                            allowBlank: false,
                            minValue: comp.minValue,
                            maxValue: comp.maxValue,
                            allowDecimals: comp.allowDecimals,
                            decimalPrecision: comp.decimalPrecision,
                            decimalSeparator: comp.decimalSeparator,
                            allowNegative: comp.allowNegative,
                            trailingDecimals: comp.trailingDecimals
                        }),
                        renderer: function(value, metaData, record, rowIndex, colIndex, store){
                            return self.checkWarning(self,value, metaData, record, rowIndex, colIndex, store);
                        }
                    });

                    bbarFields.push({
                        xtype:'numberfield',
                        range: range,
                        rangeStart: rangeStart,
                        rangeEnd: rangeEnd,
                        width: comp.width,
                        emptyText: comp.emptyText,
                        allowBlank: comp.allowBlank,
                        hideLabel: comp.hideLabel,
                        fieldLabel: comp.fieldLabel,
                        minValue: comp.minValue,
                        maxValue: comp.maxValue,
                        allowDecimals: comp.allowDecimals,
                        decimalPrecision: comp.decimalPrecision,
                        decimalSeparator: comp.decimalSeparator,
                        allowNegative: comp.allowNegative,
                        msgTarget: 'qtip',
                        style: 'margin-right:20px'
                    });
                    break;
                default:
                    gridColumns.push({
                        resizable: false,
                        fixed: true,
                        header: this.config.columnList[i].title,
                        width: this.config.columnWidth[i],
                        dataIndex: 'field'+i,
                        menuDisabled: true,
                        editor: new Ext.form.TextField({
                            allowBlank: false
                        })
                    });
                    bbarFields.push({
                        xtype:'textfield',
                        width: comp.width,
                        emptyText: comp.emptyText,
                        allowBlank: comp.allowBlank,
                        hideLabel: comp.hideLabel,
                        fieldLabel: comp.fieldLabel,
                        minLength: comp.minLength,
                        maxLength: comp.maxLength,
                        style: 'margin-right:10px'
                    });
            }
        }
        bbarFields.push({
            xtype:'button',
            handler:this.onAddRecord,
            icon: this.config.addPic,
            minWidth:24,
            width:24,
            listeners: {
                'mouseout': function(){
                    this.getEl().addClass('x-btn-over');
                }
            }
        });
        storeFields.push({
            name: 'fieldDel'
        });
        gridColumns.push({
            id:'deleteIcon',
            width: 35,
            sortable: false,
            renderer: function(value, p, record, rowIndex, colIndex, store){
                return self.deleteColRenderer(self, record);
            },
            menuDisabled: true
        });

        Ext.apply(Ext.util.Format, {
            htmlEncode : function(value){
                return !value ? value : String(value).replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
            },

            htmlDecode : function(value){
                return !value ? value : String(value).replace(/&gt;/g, ">").replace(/&lt;/g, "<").replace(/&quot;/g, '"').replace(/&#039;/g, "'").replace(/&amp;/g, "&");
            }
        });

        for (var i=0; i < this.data.length; i++)
        {
            this.data[i].unshift(i);
        }


        Ext.apply(this, {
            store: new Ext.data.ArrayStore({
                data: this.data,
                fields: storeFields
            }),
            cm: new Ext.grid.ColumnModel({
                columns: gridColumns
            }),
            stripeRows: true,
            mode: 'local',
            stateful: true,
            stateId: 'grid',
            columnLines:true,
            clicksToEdit: 1,
            trackMouseOver: false,
            autoEncode: true,
            enableColumnMove: false,
            bbar: [bbarFields]
        });

        this.on('afteredit', function(e)
        {
            this.getView().refresh();
        });

        Ext.taopix.InputOldFormatPanel.superclass.initComponent.apply(this,arguments);
        this.on('afterRender', function() {
            Ext.taopix.InputOldFormatPanel.superclass.afterRender.apply(this, arguments);
            this.afterRenderEvent();
        }, this);

        this.initialData = this.data;
    }
});
Ext.reg('taopixOldFormatInputPanel', Ext.taopix.InputOldFormatPanel);


/*************************************************/

/**
 * Taopix Component's pricing
 *
 * @author Dasha Salo
 * @version 1.0
 * @since Version 3.0
 *
 *
<style type='text/css'>
	.processed-row { background: white url('{/literal}{$webroot}{literal}/utils/ext/images/silk/error.png') no-repeat 10% center; }
    #componentPrice .x-grid3-row td, .x-grid3-summary-row td { vertical-align: middle !important;}
 </style>

var dataList1 = [[1.12, 2.15, 4.18],[2.54,9.84,6.25],[1.23, 7.45, 6.45]];
	var dataList2 = [[55], [2.15], [4.18],[66],[9.84],[6.25],[77], [7.45], [6.45]];
	var dataList3 = [[1.12, 2.15, 4.18, 6.7, 3.98],[2.54,9.84,6.25, 6.89, 4.23],[1.23, 7.45, 6.45, 5.78, 2.90]];
	var dataList4 = [[1.12, 2.15, 4.18, 6.7, 3.98, 0.45, 0,90],[2.54,9.84,6.25, 6.89, 4.23, 0.50, 0.23],[1.23, 7.45, 6.45, 5.78, 2.90, 0.8, 0.23]];
	var columnNames1 = ['Base Price', 'Unit Price', 'Line Substract'];
	var columnNames2 = ['Base Price'];
	var columnNames3 = ['Qty Range Start', 'Qty Range End', 'Base Price', 'Unit Price', 'Line Substract'];
	var columnNames4 = ['Qty Range Start', 'Qty Range End', 'Page Range Start', 'Page Range End',  'Base Price', 'Unit Price', 'Line Substract'];

	var columnWidth = [150, 150, 150];
	var fieldWidth = [100, 100, 100];
	var fieldEmptyText = [];

	var pricingPanel = new Ext.taopix.ComponentPricePanel({
		id: 'componentPrice', name:'componentPrice', height: 255, post: true, width: 619, pricingModel:'6', addPic:'{/literal}{$webroot}{literal}/utils/ext/images/silk/add.png',
		data: dataList1, delPic: '{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png',
		columnNames: columnNames1, columnWidth: columnWidth, fieldWidth: fieldWidth, fieldEmptyText: fieldEmptyText
	});

	testButton = new Ext.Button({text:'rebuild', handler: function(){pricingPanel.rebuild('4', dataList4, columnNames4, fieldEmptyText, columnWidth, fieldWidth); } });

	testWindowObj = new Ext.Window({
		id: 'TestWindow',
		title: "Some window",
		plain: true,
		layout: 'fit',
		items: [pricingPanel]
	});
	testWindowObj.show();

 */
Ext.taopix.ComponentPricePanel = Ext.extend(Ext.grid.EditorGridPanel, {
    pricingModel: '0',
    dataList:   [],
    addPic: '',
    delPic: '',
    initialData: [],
    startMinValue:1,
    columnNames: [],
    columnWidth: [],
    fieldWidth: [],
    fieldEmptyText: [],
    columnLabels: {},
    pricingDecimalPlaces: 4,
    isProduct: 0,
    errorTitle: '',
    errorMessage1: '',
    errorMessage2: '',
    errorMessage3: '',
    errorMessage4: '',
    errorMessage5: '',
    errorMessage7: '',
    models: {
        '3': {
            bbarFields:[
            {
                xtype:'numberfield',
                range: 'qtyRange',
                rangeStart: true,
                rangeEnd: false,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: false,
                decimalPrecision: 0,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 0
            },
            {
                xtype:'numberfield',
                range: 'qtyRange',
                rangeStart: false,
                rangeEnd: true,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: false,
                decimalPrecision: 0,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 0
            },
            {
                xtype:'numberfield',
                range: false,
                rangeStart: false,
                rangeEnd: false,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: true,
                decimalPrecision: 4,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 4
            },
            {
                xtype:'numberfield',
                range: false,
                rangeStart: false,
                rangeEnd: false,
                width: 123,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: true,
                decimalPrecision: 4,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 4
            },
            {
                xtype:'numberfield',
                range: false,
                rangeStart: false,
                rangeEnd: false,
                width: 123,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: true,
                decimalPrecision: 4,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 4
            }],
            columnList: [
            {
                header: 'qrs',
                width: 100,
                dataIndex: 'field0',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'qrs'
            },

            {
                header: 'qre',
                width: 100,
                dataIndex: 'field1',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'qre'
            },

            {
                header: 'bp',
                width: 100,
                dataIndex: 'field2',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'bp'
            },

            {
                header: 'up',
                width: 100,
                dataIndex: 'field3',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'up'
            },

            {
                header: 'ls',
                width: 100,
                dataIndex: 'field4',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'ls'
            }]
        }, /* per qty */
        '5': {
            bbarFields:[
            {
                xtype:'numberfield',
                range: 'qtyRange',
                rangeStart: true,
                rangeEnd: false,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: false,
                decimalPrecision: 0,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 0
            },
            {
                xtype:'numberfield',
                range: 'qtyRange',
                rangeStart: false,
                rangeEnd: true,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: false,
                decimalPrecision: 0,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 0
            },
            {
                xtype:'numberfield',
                range: 'pageRange',
                rangeStart: true,
                rangeEnd: false,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: false,
                decimalPrecision: 0,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 0
            },
            {
                xtype:'numberfield',
                range: 'pageRange',
                rangeStart: false,
                rangeEnd: true,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: false,
                decimalPrecision: 0,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 0
            },
            {
                xtype:'numberfield',
                range: false,
                rangeStart: false,
                rangeEnd: false,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: true,
                decimalPrecision: 2,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 2
            },
            {
                xtype:'numberfield',
                range: false,
                rangeStart: false,
                rangeEnd: false,
                width: 123,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: true,
                decimalPrecision: 2,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 2
            },
            {
                xtype:'numberfield',
                range: false,
                rangeStart: false,
                rangeEnd: false,
                width: 123,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: true,
                decimalPrecision: 2,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 2
            } ],
            columnList: [
            {
                header: 'qrs',
                width: 100,
                dataIndex: 'field0',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'qrs'
            },

            {
                header: 'qre',
                width: 100,
                dataIndex: 'field1',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'qre'
            },

            {
                header: 'srs',
                width: 100,
                dataIndex: 'field2',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'srs'
            },

            {
                header: 'sre',
                width: 100,
                dataIndex: 'field3',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'sre'
            },

            {
                header: 'bp',
                width: 100,
                dataIndex: 'field4',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'bp'
            },

            {
                header: 'up',
                width: 100,
                dataIndex: 'field5',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'up'
            },

            {
                header: 'ls',
                width: 100,
                dataIndex: 'field6',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'ls'
            }]
        }, /* per side and qty */
		'7': {
            bbarFields:[
            {
                xtype:'numberfield',
                range: 'qtyRange',
                rangeStart: true,
                rangeEnd: false,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: false,
                decimalPrecision: 0,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 0
            },
            {
                xtype:'numberfield',
                range: 'qtyRange',
                rangeStart: false,
                rangeEnd: true,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: false,
                decimalPrecision: 0,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 0
            },
            {
                xtype:'numberfield',
                range: 'pageRange',
                rangeStart: true,
                rangeEnd: false,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: false,
                decimalPrecision: 0,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 0
            },
            {
                xtype:'numberfield',
                range: 'pageRange',
                rangeStart: false,
                rangeEnd: true,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: false,
                decimalPrecision: 0,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 0
            },
            {
                xtype:'numberfield',
                range: false,
                rangeStart: false,
                rangeEnd: false,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: true,
                decimalPrecision: 2,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 2
            },
            {
                xtype:'numberfield',
                range: false,
                rangeStart: false,
                rangeEnd: false,
                width: 123,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: true,
                decimalPrecision: 2,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 2
            },
            {
                xtype:'numberfield',
                range: false,
                rangeStart: false,
                rangeEnd: false,
                width: 123,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: true,
                decimalPrecision: 2,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 2
            } ],
            columnList: [
            {
                header: 'qrs',
                width: 100,
                dataIndex: 'field0',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'qrs'
            },

            {
                header: 'qre',
                width: 100,
                dataIndex: 'field1',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'qre'
            },

            {
                header: 'crs',
                width: 100,
                dataIndex: 'field2',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'crs'
            },

            {
                header: 'cre',
                width: 100,
                dataIndex: 'field3',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'cre'
            },

            {
                header: 'bp',
                width: 100,
                dataIndex: 'field4',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'bp'
            },

            {
                header: 'up',
                width: 100,
                dataIndex: 'field5',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'up'
            },

            {
                header: 'ls',
                width: 100,
                dataIndex: 'field6',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'ls'
            }]
        },
        '8': {
            bbarFields:[
            {
                xtype:'numberfield',
                range: 'qtyRange',
                rangeStart: true,
                rangeEnd: false,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: false,
                decimalPrecision: 0,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 0
            },
            {
                xtype:'numberfield',
                range: 'qtyRange',
                rangeStart: false,
                rangeEnd: true,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: false,
                decimalPrecision: 0,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 0
            },
            {
                xtype:'numberfield',
                range: 'componentRange',
                rangeStart: true,
                rangeEnd: false,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: false,
                decimalPrecision: 0,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 0
            },
            {
                xtype:'numberfield',
                range: 'componentRange',
                rangeStart: false,
                rangeEnd: true,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: false,
                decimalPrecision: 0,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 0
            },
            {
                xtype:'numberfield',
                range: 'sideRange',
                rangeStart: true,
                rangeEnd: false,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: false,
                decimalPrecision: 0,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 0
            },
            {
                xtype:'numberfield',
                range: 'sideRange',
                rangeStart: false,
                rangeEnd: true,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: false,
                decimalPrecision: 0,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 0
            },
            {
                xtype:'numberfield',
                range: false,
                rangeStart: false,
                rangeEnd: false,
                width: 122,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: true,
                decimalPrecision: 2,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 2
            },
            {
                xtype:'numberfield',
                range: false,
                rangeStart: false,
                rangeEnd: false,
                width: 123,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: true,
                decimalPrecision: 2,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 2
            },
            {
                xtype:'numberfield',
                range: false,
                rangeStart: false,
                rangeEnd: false,
                width: 123,
                emptyText: '',
                allowBlank: false,
                hideLabel: true,
                fieldLabel: '',
                minValue: Number.NEGATIVE_INFINITY,
                maxValue: Number.POSITIVE_INFINITY,
                allowDecimals: true,
                decimalPrecision: 2,
                decimalSeparator: '.',
                allowNegative: true,
                msgTarget: 'qtip',
                style: 'margin-right:20px',
                trailingDecimals: 2
            } ],
            columnList: [
            {
                header: 'qrs',
                width: 100,
                dataIndex: 'field0',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'qrs'
            },
            {
                header: 'qre',
                width: 100,
                dataIndex: 'field1',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'qre'
            },
            {
                header: 'crs',
                width: 100,
                dataIndex: 'field2',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'crs'
            },
            {
                header: 'cre',
                width: 100,
                dataIndex: 'field3',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'cre'
            },
            {
                header: 'srs',
                width: 100,
                dataIndex: 'field4',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'srs'
            },

            {
                header: 'sre',
                width: 100,
                dataIndex: 'field5',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'sre'
            },
            {
                header: 'bp',
                width: 100,
                dataIndex: 'field6',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'bp'
            },
            {
                header: 'up',
                width: 100,
                dataIndex: 'field7',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'up'
            },
            {
                header: 'ls',
                width: 100,
                dataIndex: 'field8',
                menuDisabled: true,
                align:'right',
                resizable: false,
                fixed: true,
                shortName: 'ls'
            }]
        }
    },
    convertTableToString: function()
    {
        var resultString = "";
        var resultArray = [];
        var bufArray = [];
        var modelData = this.dataInitWithModel();

        this.stopEditing(false);
        for (var i = 0; i < this.store.data.items.length; i++)
        {
            bufArray = [];
            for( var j = 1, cmp; j < this.store.data.items[i].fields.items.length-1; j++)
            {
                var cmp = modelData['bbarFields'][j-1];

                if (cmp.allowDecimals)
                {
                	bufArray.push( ((this.store.data.items[i].data[this.store.data.items[i].fields.items[j].name])*1).toFixed(cmp.trailingDecimals) + '' );
                }
                else
                {
                	bufArray.push( ((this.store.data.items[i].data[this.store.data.items[i].fields.items[j].name])*1).toFixed(0) + '' );
                }
            }
            resultArray.push(bufArray.join('*'));
        }
        resultString = resultArray.join(' ');

        return resultString;
    },


    reset: function()
    {
        this.data = this.initialData;
        this.store.loadData(this.data);
    },


    refresh: function()
    {
        this.getView().refresh();
    },


    onAddRecord: function()
    {
        var panel = this.findParentByType('ComponentPricePanel');
        var components = panel.getBottomToolbar().items.items;
        var ranges = [];
        var iSizeAttribute = components.length - 1;
        var iSizeItem = panel.store.data.items.length;
        var iComponentMin;
        var iComponentMax;
        var bError = 0;

		if(panel.pricingModel == 3)
		{
			iMin = components[0].getValue();
			iMax = components[1].getValue();

			// do not allow minus prices
			if (iMin <= 0 || iMax <= 0 )
			{
				bError = 1;
			}

			// make sure QtyEnd > QtyStart
			if (iMax < iMin)
			{
				bError = 3;
			}

			// For Products: qty start has to be greater than 0
			if (iSizeItem == 0 && iMin <= 0)
			{
				bError = 5;
			}


			// make sure the next range does not overlap the previous range
			for( var j = 0; j < iSizeItem; j++)
			{
				// Min & Max of the previous records added
				iComponentMin = panel.store.data.items[j]['data']['field0'];
				iComponentMax = panel.store.data.items[j]['data']['field1'];

				if( (iMin < iComponentMin && iMax <= iComponentMax && iMax >= iComponentMin) || (iMin <= iComponentMax && iMax >= iComponentMin))
				{
					bError = 2;
				}
			}

			// maximum value must be lower than / equal to 99999999 (max value that RealBasic can handle)
			if (iMax > 99999999)
			{
				bError = 7;
			}

		}
		else if(panel.pricingModel == 5 || panel.pricingModel == 7)
		{

			newMinProdQty = components[0].getValue();
			newMaxProdQty = components[1].getValue();

			newMinComponentQty = components[2].getValue();
			newMaxComponentQty = components[3].getValue();

			// validate the very first record
			if (iSizeItem == 0)
			{
				// if pricing model 5, 0 page is not allowed
				if(panel.pricingModel == 5 && newMinComponentQty != 1)
				{
					bError = 1;
				}

				// product qty start has to be 1
				if (newMinProdQty != 1)
				{
					bError = 1;
				}
			}

			// make sure QtyEnd > QtyStart
			if (newMaxProdQty < newMinProdQty || newMaxComponentQty < newMinComponentQty)
			{
				bError = 3;
			}

			// Component price can start from 0 but not < 0
			if ((newMinComponentQty < 0 || newMaxComponentQty < 0) && panel.pricingModel == 7)
			{
				bError = 4;
			}

			if(iSizeItem > 0)
			{
				// get the last record inserted
				var j = iSizeItem - 1;
				previousMinProdQty = panel.store.data.items[j]['data']['field0'];
				previousMaxProdQty = panel.store.data.items[j]['data']['field1'];
				previousMinComponentQty = panel.store.data.items[j]['data']['field2'];
				previousMaxComponentQty   = panel.store.data.items[j]['data']['field3'];

				// If product quantity range stay the same, component qty range can't overlap
				if( newMinProdQty == previousMinProdQty && newMaxProdQty == previousMaxProdQty )
				{
					if(newMinComponentQty <= previousMaxComponentQty)
					{
						bError = 2;
					}
				}

				// Do not allow minus prices
				if (newMinProdQty <= 0 || newMaxProdQty<=0 || newMaxComponentQty <=0 || (newMinComponentQty < 0 && panel.pricingModel == 7))
				{
					bError = 1;
				}

				// Check new prod start qty agains previous prod start qty
				if (newMinProdQty < previousMinProdQty)
				{
					bError = 2;
				}

				if (newMinProdQty <= previousMaxProdQty && newMinComponentQty <= previousMaxComponentQty)
				{
					bError = 2;
				}
			}

			// maximum value must be lower than / equal to 99999999 (max value that RealBasic can handle)
			if (newMaxProdQty > 99999999 || newMaxComponentQty > 99999999)
			{
				bError = 7;
			}
		}
		else if( panel.pricingModel == 8 )
		{

			newMinProdQty = components[0].getValue();
			newMaxProdQty = components[1].getValue();

			newMinComponentQty = components[2].getValue();
			newMaxComponentQty = components[3].getValue();

			newMinSideQty = components[4].getValue();
			newMaxSideQty = components[5].getValue();

			// product & side qty start has to be 1 if not using Fixed Quantity Ranges
			if (iSizeItem == 0 && (newMinProdQty != 1 || newMinSideQty != 1))
			{
				bError = 1;
			}

			// make sure QtyEnd > QtyStart
			if ((newMaxProdQty < newMinProdQty) || (newMaxComponentQty < newMinComponentQty) || (newMaxSideQty < newMinSideQty))
			{
				bError = 3;
			}

			// block negative prices
			if (newMinProdQty <= 0 || newMaxProdQty<=0 || newMinSideQty <= 0 || newMaxSideQty <= 0)
			{
				bError = 1;
			}

			// Component price can start from 0 but not <0
			if (newMinComponentQty < 0 || newMaxComponentQty < 0)
			{
				bError = 4;
			}

			if(iSizeItem > 0)
			{
				// get the latest record inserted
				var j = iSizeItem - 1;

				previousMinProdQty = panel.store.data.items[j]['data']['field0'];
				previousMaxProdQty = panel.store.data.items[j]['data']['field1'];
				previousMinComponentQty = panel.store.data.items[j]['data']['field2'];
				previousMaxComponentQty   = panel.store.data.items[j]['data']['field3'];
				previousMinSideQty = panel.store.data.items[j]['data']['field4'];
				previousMaxSideQty   = panel.store.data.items[j]['data']['field5'];

				// If product quantity range stay the same, component qty range can't overlap
				if( newMinProdQty == previousMinProdQty && newMaxProdQty == previousMaxProdQty )
				{
					// if Component quantity range stay the same, side can't over lap.
					if( newMinComponentQty == previousMinComponentQty && newMaxComponentQty == previousMaxComponentQty )
					{
						// Side can't over lap.
						if(newMinSideQty <= previousMaxSideQty)
						{
							bError = 2;
						}
					}
				}

				// Check new prod start qty agains previous prod start qty
				if( newMinProdQty < previousMinProdQty)
				{
					bError = 2;
				}

				if( newMinComponentQty <= previousMaxComponentQty && newMinProdQty <= previousMaxProdQty &&  newMinSideQty <= previousMaxSideQty)
				{
					bError = 2;
				}
			}

			// maximum value must be lower than / equal to 99999999 (max value that RealBasic can handle)
			if (newMaxProdQty > 99999999 || newMaxComponentQty > 99999999 || newMaxSideQty > 99999999)
			{
				bError = 7;
			}

		}

        if(bError!=0){
        	var message = '';
        	switch(bError)
        	{
        		case 1:
					message = panel.errorMessage1;
        		break;
        		case 2:
					message = panel.errorMessage2;
			    break;
        		case 3:
					message = panel.errorMessage3;
        		break;
        		case 4:
					message = panel.errorMessage4;
        		break;
        		case 5:
					message = panel.errorMessage5;
        		break;
        		case 7:
					message = panel.errorMessage7;
        		break;


        	}

        	Ext.MessageBox.show({
			title: panel.errorTitle,
			msg: message,
			buttons: Ext.MessageBox.OK,
			icon: Ext.MessageBox.WARNING});
            return false;
        }

        var maxId = 0;
        for (var i = 0; i < iSizeItem; i++)
        {
            if (panel.store.data.items[i].data.idfield > maxId) {
                maxId = panel.store.data.items[i].data.idfield;
            }
        }

        var record={};
        record['idfield'] = maxId + 1;
        for (var i = 0, cmp; i < components.length - 1; i++)
        {
            cmp = components[i];
            record['field'+i] = cmp.getValue();
            cmp.reset();
        }
        record['deleteIcon'] = '';

        var r = new panel.store.recordType(record);
        r.commit();
        panel.store.add(r);
    },

    setError: function( sTitle){
        this.errorTitle = sTitle;
        this.errorMessage = sMessage;
    },
    onDeleteRecord: function(pCode)
    {
        this.store.removeAt(this.store.findExact('idfield',pCode*1));
        return false;
    },


    deleteColRenderer: function(pPanel, pRecord)
    {
        return '<div onClick="Ext.getCmp(\''+pPanel.id+'\').onDeleteRecord(\''+pRecord.data.idfield+'\'); return false;" OnMouseOver="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\') + \' x-btn-over\';" OnMouseOut="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\');"><table cellspacing="0" class="x-btn  x-btn-icon"><tbody class="x-btn-small x-btn-icon-small-left"><tr><td class="x-btn-tl"><i>&nbsp;</i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-ml"><i>&nbsp;</i></td><td class="x-btn-mc"><em unselectable="on" class=""><button type="button" class="x-btn-text " style="background-image: url(&quot;'+pPanel.delPic+'&quot;);">&nbsp;</button></em></td><td class="x-btn-mr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-bl"><i>&nbsp;</i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i>&nbsp;</i></td></tr></tbody></table></div>';
    },

    isValid: function()
    {
        this.stopEditing(false);
        if (this.convertTableToString() == '') {
            return 1;
        }

		var modelData = this.dataInitWithModel();

		for (var i=0; i < this.store.data.items.length; i++)
		{
			if(this.pricingModel == 3)
			{
				iPreviousMin = 0;
				iPreviousMax = 0;

				currentMin = parseInt(this.store.data.items[i].data[this.store.data.items[i].fields.items[1].name]);
				currentMax = parseInt(this.store.data.items[i].data[this.store.data.items[i].fields.items[2].name]);

				if ( i > 0 )
				{
					iPreviousMin = parseInt(this.store.data.items[i-1].data[this.store.data.items[i-1].fields.items[1].name]);
					iPreviousMax = parseInt(this.store.data.items[i-1].data[this.store.data.items[i-1].fields.items[2].name]);
				}

				// do not allow minus prices
				if (currentMin <=0 || currentMax <=0)
				{
					return 6;
				}

				// make sure QtyEnd > QtyStart
				if (currentMax < currentMin)
				{
					return 3;
				}


				// For Product: qty start has to be greater than 0
				if (i==0 && currentMin <= 0)
				{
					return 6;
				}

				if( currentMin <= iPreviousMax && i > 0)
				{
					return 4;
				}

				// maximum value must be lower than / equal to 99999999 (max value that RealBasic can handle)
				if (currentMax > 99999999)
				{
					return 7;
				}
			}
			else if(this.pricingModel == 5 || this.pricingModel == 7)
			{

				iPreviousProdMin = 0;
				iPreviousProdMax = 0;
				iPreviousCompMin = 0;
				iPreviousCompMax = 0;

				currentProdMin = parseInt(this.store.data.items[i].data[this.store.data.items[i].fields.items[1].name]);
				currentProdMax = parseInt(this.store.data.items[i].data[this.store.data.items[i].fields.items[2].name]);
				currentCompMin = parseInt(this.store.data.items[i].data[this.store.data.items[i].fields.items[3].name]);
				currentCompMax = parseInt(this.store.data.items[i].data[this.store.data.items[i].fields.items[4].name]);

				if (i > 0)
				{
					iPreviousProdMin = parseInt(this.store.data.items[i-1].data[this.store.data.items[i-1].fields.items[1].name]);
					iPreviousProdMax = parseInt(this.store.data.items[i-1].data[this.store.data.items[i-1].fields.items[2].name]);
					iPreviousCompMin = parseInt(this.store.data.items[i-1].data[this.store.data.items[i-1].fields.items[3].name]);
					iPreviousCompMax = parseInt(this.store.data.items[i-1].data[this.store.data.items[i-1].fields.items[4].name]);
				}

				// if pricing model 5, component qty has to start from 1 for the first row.
				if(this.pricingModel == 5 && currentCompMin != 1 && i == 0)
				{
					return 1;
				}

				// product qty has to start from 1 for the first row.
				if(currentProdMin != 1 && i == 0)
				{
					return 1;
				}

				// Start qty has to be lower than End qty in any cases
				if(currentProdMax < currentProdMin || currentCompMax < currentCompMin)
				{
					return 3;
				}

				// if pricing model 7, allows 0 qty for component.
				if ((currentCompMin < 0 || currentCompMin < 0) && this.pricingModel == 7)
				{
					return 5;
				}

				// If product quantity range stay the same, component qty range can't overlap
				if( currentProdMin == iPreviousProdMin && currentProdMax == iPreviousProdMax )
				{
					if(currentCompMin <= iPreviousCompMax)
					{
						return 4;
					}
				}

				// do not allow both ragnes to overlap
				if (currentCompMin <= iPreviousCompMax && currentProdMin <= iPreviousProdMax)
				{
					return 4;
				}

				// check current start Qty agains previous start Qty
				if (currentProdMin < iPreviousProdMin)
				{
					return 4;
				}

				// Do not allow minus prices
				if (currentProdMin <= 0 || currentProdMax <=0 || currentCompMax <=0 || (currentCompMin < 0 && this.pricingModel == 7))
				{
					return 1;
				}

				// maximum value must be lower than / equal to 99999999 (max value that RealBasic can handle)
				if (currentCompMax > 99999999 || currentProdMax > 99999999)
				{
					return 7;
				}

			}
			else if(this.pricingModel == 8)
			{
				iPreviousProdMin = 0;
				iPreviousProdMax = 0;
				iPreviousCompMin = 0;
				iPreviousCompMax = 0;
				iPreviousSideMin = 0;
				iPreviousSideMax = 0;

				currentProdMin = parseInt(this.store.data.items[i].data[this.store.data.items[i].fields.items[1].name]);
				currentProdMax = parseInt(this.store.data.items[i].data[this.store.data.items[i].fields.items[2].name]);
				currentCompMin = parseInt(this.store.data.items[i].data[this.store.data.items[i].fields.items[3].name]);
				currentCompMax = parseInt(this.store.data.items[i].data[this.store.data.items[i].fields.items[4].name]);
				currentSideMin = parseInt(this.store.data.items[i].data[this.store.data.items[i].fields.items[5].name]);
				currentSideMax = parseInt(this.store.data.items[i].data[this.store.data.items[i].fields.items[6].name]);

				if (i > 0)
				{
					iPreviousProdMin = parseInt(this.store.data.items[i-1].data[this.store.data.items[i-1].fields.items[1].name]);
					iPreviousProdMax = parseInt(this.store.data.items[i-1].data[this.store.data.items[i-1].fields.items[2].name]);
					iPreviousCompMin = parseInt(this.store.data.items[i-1].data[this.store.data.items[i-1].fields.items[3].name]);
					iPreviousCompMax = parseInt(this.store.data.items[i-1].data[this.store.data.items[i-1].fields.items[4].name]);
					iPreviousSideMin = parseInt(this.store.data.items[i-1].data[this.store.data.items[i-1].fields.items[5].name]);
					iPreviousSideMax = parseInt(this.store.data.items[i-1].data[this.store.data.items[i-1].fields.items[6].name]);
				}

				// product & side qty have to start from 1
				if(i == 0 && currentProdMin != 1 && currentSideMin != 1 )
				{
					return 1;
				}

				// make sure QtyEnd > QtyStart
				if(currentProdMax < currentProdMin || currentCompMax < currentCompMin || currentSideMax < currentSideMin)
				{
					return 3;
				}

				// block minus price
				if(currentProdMin <= 0 || currentProdMax <=0 || currentSideMin <= 0 || currentSideMax <= 0)
				{
					return 1;
				}

				// blog minus price but allow 0 for component
				if(currentCompMin < 0 || currentCompMax < 0)
				{
					return 5;
				}

				if (currentCompMin <= iPreviousCompMax && currentProdMin <= iPreviousProdMax && currentSideMin <= iPreviousSideMax)
				{
					return 4;
				}

				// check current start Qty agains previous start Qty
				if (currentProdMin < iPreviousProdMin)
				{
					return 4;
				}

				if(currentProdMin == iPreviousProdMin && currentProdMax == iPreviousProdMax)
				{
					// if Component quantity range stay the same, side can't over lap.
					if( currentCompMin == iPreviousCompMin && currentCompMax == iPreviousCompMax )
					{
						// Side can't over lap.
						if(currentSideMin <= iPreviousSideMax)
						{
							return 4;
						}
					}
					else
					{
						// Component Can't over lap.
						if(currentCompMin <= iPreviousCompMax)
						{
							return 4;
						}
					}
				}


				// maximum value must be lower than / equal to 99999999 (max value that RealBasic can handle)
				if (currentCompMax > 99999999 || currentProdMax > 99999999 || currentSideMax > 99999999)
				{
					return 7;
				}
			}
		}
        return 0;
    },


    checkWarning: function(obj, value, metaData, record, rowIndex, colIndex, store)
    {
        var modelData = this.dataInitWithModel();
        var comp = modelData['bbarFields'][colIndex];
        var rangeMin = comp.minValue;

        if ((comp) && (comp.range) && (comp.rangeEnd))
        {
            if ((modelData['bbarFields'][colIndex-1]) && (modelData['bbarFields'][colIndex-1].range == comp.range) && (modelData['bbarFields'][colIndex-1].rangeStart))
            {
                rangeMin = record.data['field'+(colIndex-1)];
            }
        }

        if ((comp) && (comp.range) && (comp.rangeStart))
        {
            if ((modelData['bbarFields'][colIndex+1]) && (modelData['bbarFields'][colIndex+1].range == comp.range) && (modelData['bbarFields'][colIndex+1].rangeEnd))
            {
                if (store.data.items[rowIndex-1])
                {
                /*if (store.data.items[rowIndex].data['field'+colIndex] *1 < store.data.items[rowIndex-1].data['field'+(colIndex + 1)] * 1)
					{
						return '<div class="processed-row">'+(value*1).toFixed(comp.trailingDecimals) +'</div>';
					}*/
                }
            }
        }

        if (((comp.minValue) && (comp.minValue > value*1)) || (rangeMin > value*1))
        {
            return '<div class="processed-row">'+(value*1).toFixed(comp.trailingDecimals) +'</div>';
        }
        else
        {
        	if (comp.allowDecimals)
        	{
        		return (value*1).toFixed(comp.trailingDecimals);
        	}
        	else
        	{
        		return (value*1).toFixed(0);
        	}
        }
    },

    constructor: function(config)
    {
        if (config.pricingModel != undefined) this.pricingModel = config.pricingModel + '';
        if (config.data != undefined && config.data != '') {
            var pricingArray = config.data.split(' ');
            for (var i = 0; i < pricingArray.length; i++)
            {
                pricingArray[i] = pricingArray[i].split('*');

            }

            this.dataList = pricingArray;
        }

        if (config.addPic != undefined) this.addPic = config.addPic;
        if (config.delPic != undefined) this.delPic = config.delPic;
        if (config.startMinValue != undefined) this.startMinValue = config.startMinValue;
        if (config.columnWidth != undefined) this.columnWidth = config.columnWidth;
        if (config.fieldWidth != undefined) this.fieldWidth = config.fieldWidth;
        if (config.pricingDecimalPlaces != undefined) this.pricingDecimalPlaces = config.pricingDecimalPlaces;
        this.errorTitle = config.errorTitle;
        this.errorMessage1 = config.errorMessage1;
        this.errorMessage2 = config.errorMessage2;
        this.errorMessage3 = config.errorMessage3;
        this.errorMessage4 = config.errorMessage4;
        this.errorMessage5 = config.errorMessage5;
        this.errorMessage7 = config.errorMessage7;
        /*if (config.fieldEmptyText != undefined) this.fieldEmptyText = config.fieldEmptyText;*/
        this.fieldEmptyText = [];

        Ext.taopix.ComponentPricePanel.superclass.constructor.call(this, config);
    },

    dataInitWithModel: function()
    {
        var storeFields = [];
        var gridDataArray = [];
        var self = this;

        /* prepare storeFields and gridColumns for each pricing model */
        var pricingModelData = this.models[this.pricingModel];
        var gridColumns = pricingModelData['columnList'].slice(0);
        var bbarFields = pricingModelData['bbarFields'].slice(0);

        for (var i=0; i<this.dataList.length; i++)
        {
            gridDataArray.push(this.dataList[i].slice(0));
        }

        this.initialData = this.dataList;

        var fixedPrecisionNumberField = Ext.extend(Ext.form.NumberField, {
            setValue : function(v){
                v = typeof v == 'number' ? v : String(v).replace(this.decimalSeparator, ".");
                v = isNaN(v) ? '' : String(v).replace(".", this.decimalSeparator);
                v = isNaN(v) ? '' : this.fixPrecision(String(v).replace(".", this.decimalSeparator));
                return Ext.form.NumberField.superclass.setValue.call(this, v);
            }
        });

        storeFields.push({
            name: 'idfield'
        });

        for (var i = 0; i < gridColumns.length; i++)
        {
            storeFields.push({
                name: 'field'+i
            });
            gridColumns[i].renderer = function(value, metaData, record, rowIndex, colIndex, store){
                return self.checkWarning(self,value, metaData, record, rowIndex, colIndex, store);
            }
            gridColumns[i].header = this.columnLabels[gridColumns[i].shortName];
            bbarFields[i].emptyText = '';
            bbarFields[i].decimalPrecision = this.pricingDecimalPlaces;
            bbarFields[i].trailingDecimals = this.pricingDecimalPlaces;
            gridColumns[i].width = (this.columnWidth[i]) ? this.columnWidth[i] : 100;
            bbarFields[i].width = (this.fieldWidth[i]) ? this.fieldWidth[i] : 100;
            gridColumns[i].editor = new fixedPrecisionNumberField({
                allowBlank: bbarFields[i].allowBlank,
                minValue: bbarFields[i].minValue,
                maxValue: bbarFields[i].maxValue,
                allowDecimals: bbarFields[i].allowDecimals,
                decimalPrecision: this.pricingDecimalPlaces,
                decimalSeparator: bbarFields[i].decimalSeparator,
                allowNegative: bbarFields[i].allowNegative,
                trailingDecimals: bbarFields[i].trailingDecimals
            });
        }
        storeFields.push({
            name: 'fieldDel'
        });

        bbarFields.push({
            xtype:'button',
            handler:this.onAddRecord,
            icon: this.addPic,
            minWidth:24,
            width:24,
            listeners:
            {
            	'afterrender': function()
            	{
            		this.getEl().addClass('x-btn-over');
            	},
                'mouseout': function(){
                    this.getEl().addClass('x-btn-over');
                }
            }
        });
        gridColumns.push({
            id:'deleteIcon',
            renderer: function(value, p, record, rowIndex, colIndex, store){
                return self.deleteColRenderer(self, record);
            },
            width: 35,
            sortable: false,
            menuDisabled: true
        });

        for (var i=0; i < gridDataArray.length; i++) {
            gridDataArray[i].unshift(i);
        }

        return {
            'storeFields': storeFields,
            'gridColumns': gridColumns,
            'bbarFields': bbarFields,
            'gridData': gridDataArray
        };
    },

    rebuild: function(pricingModel, gridData, fieldEmptyText, columnWidth, fieldWidth)
    {
        this.pricingModel = pricingModel;
        var pricingArray = gridData.split(' ');
        for (var i = 0; i < pricingArray.length; i++)
        {
            pricingArray[i] = pricingArray[i].split('*');
        }
        this.dataList = pricingArray;

        this.fieldEmptyText = fieldEmptyText;
        this.columnWidth = columnWidth;
        this.fieldWidth = fieldWidth;

        var modelData = this.dataInitWithModel();

        var store = new Ext.data.ArrayStore({
            data: modelData['gridData'],
            fields: modelData['storeFields']
        });
        var columnModel = new Ext.grid.ColumnModel({
            columns: modelData['gridColumns']
        });
        this.reconfigure(store, columnModel);

        /* this.store.sort('field0','ASC'); */

        this.getBottomToolbar().removeAll();
        this.getBottomToolbar().add(modelData['bbarFields']);
        this.getBottomToolbar().doLayout();
    },

    reload: function(gridData)
    {
        var pricingArray = gridData.split(' ');

        for (var i = 0; i < pricingArray.length; i++)
        {
            buffer = pricingArray[i].split('*');
            buffer.unshift(i);
            pricingArray[i] = buffer;
        }

        this.dataList = pricingArray;

        this.store.loadData(this.dataList);
    },

    /* Component initialization */
    initComponent: function()
    {
        var self = this;

        var modelData = this.dataInitWithModel();

        Ext.apply(this, {
            store: new Ext.data.ArrayStore({
                data: modelData['gridData'],
                fields: modelData['storeFields']
            }),
            cm: new Ext.grid.ColumnModel({
                columns: modelData['gridColumns']
            }),
            stripeRows: true,
            mode: 'local',
            stateful: true,
            stateId: 'grid',
            columnLines:true,
            clicksToEdit: 1,
            trackMouseOver: false,
            autoEncode: true,
            enableColumnMove: false,
            autoScroll: true,
            overflow: 'auto',
            style:
			{
				border:'1px solid #b4b8c8'
			},
            bbar:
            {
            	xtype: 'toolbar',
            	items: modelData['bbarFields'],
            	listeners:
            	{
            		afterrender: function()
            		{
            			this.el.applyStyles(
            			{
            				'padding': '5px 3px !important',
            				'border': '0'
            			});
            		}
            	}
            }
        });

        Ext.apply(Ext.util.Format, {
            htmlEncode : function(value){
                return !value ? value : String(value).replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
            },
            htmlDecode : function(value){
                return !value ? value : String(value).replace(/&gt;/g, ">").replace(/&lt;/g, "<").replace(/&quot;/g, '"').replace(/&#039;/g, "'").replace(/&amp;/g, "&");
            }
        });

        this.on('afteredit', function(e) {
            this.getView().refresh();
        });
        Ext.taopix.ComponentPricePanel.superclass.initComponent.apply(this,arguments);

        this.on('afterRender', function() {
            Ext.taopix.ComponentPricePanel.superclass.afterRender.apply(this, arguments);
        }, this);
    }
});
Ext.reg('ComponentPricePanel', Ext.taopix.ComponentPricePanel);



Ext.taopix.FixedPrecisionNumberField = Ext.extend(Ext.form.NumberField, {
    /* Component initialization */
    initComponent: function()
    {
        /*Ext.apply(Ext.util.Format, {
			htmlEncode : function(value){
				return !value ? value : String(value).replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
	    	},
	    	htmlDecode : function(value){
	    		return !value ? value : String(value).replace(/&gt;/g, ">").replace(/&lt;/g, "<").replace(/&quot;/g, '"').replace(/&#039;/g, "'").replace(/&amp;/g, "&");
	    	}
		});*/

        Ext.apply(this, {
            setValue: function(pValue)
            {
                pValue = typeof pValue == 'number' ? pValue : String(pValue).replace(this.decimalSeparator, ".");
				pValue = isNaN(pValue) ? '' : this.fixPrecision(pValue);
				pValue = isNaN(pValue) ? '' : String(pValue).replace(".", this.decimalSeparator);

				return Ext.form.NumberField.superclass.setValue.call(this, pValue);
            },

            fixPrecision: function(pValue)
            {
                var nan = isNaN(pValue);
                if(!this.allowDecimals || this.decimalPrecision == -1 || nan || !pValue)
                {
                      return nan ? '' : pValue;
                }
                return parseFloat(pValue).toFixed(this.decimalPrecision);
            }
        });

        Ext.taopix.FixedPrecisionNumberField.superclass.initComponent.apply(this,arguments);
    }
});
Ext.reg('fixedPrecisionNumberField', Ext.taopix.FixedPrecisionNumberField);


/**
 * Taopix pricing component
 *
 * @author Stuart Milne
 * @version 1.0
 * @since Version 1.0
 */
Ext.taopix.pricingPanel = Ext.extend(Ext.Panel, {

    sessionRef: 0,
    licenseKeyDataStoreURL: '',
    companyCode: '',
    assignedLicenseKeys:[],
    category: '',
    isPriceList: 0,
    priceListID: -1,
    windowToMask: {},
    isProduct: '',
    qtyIsDropDown: 0,
    inheritParentQty: 0,
    allowInherit: 0,
    isEdit: 0,
    useExternalShoppingCart: 0,
    useExternalShoppingCartChecked: 0,
    decimalPlaces: 2,
    errorTitle: '',
    errorMessage1: '',
    errorMessage2: '',
    errorMessage3: '',
    errorMessage4: '',
    errorMessage5: '',
    errorMessage6: '',
    errorMessage7: '',
    errorMessage6Title: '',
    defaultLanguage: '',
    /* Component constructor */
    constructor: function(config)
    {
        this.sessionRef = config.ref;
        this.windowToMask = config.windowToMask;
        this.category = config.category;
        this.isProduct = config.isProduct;
        this.isEdit = config.isEdit;
        this.decimalPlaces = config.pricingDecimalPlaces;
        this.price = config.pricing.price;
        this.pricingModel = config.pricing.pricingModel;
        this.isPriceList = config.pricing.isPriceList;
        this.priceListID = config.pricing.priceListID;
        this.qtyIsDropDown = config.pricing.qtyIsDropDown;
        this.inheritParentQty = config.pricing.inheritParentQty;
        this.allowInherit = config.pricing.allowInherit;
        this.taxCode = config.pricing.taxCode;
        this.productType = config.pricing.productType;
        this.useExternalShoppingCart = config.pricing.useExternalShoppingCart;
        this.useExternalShoppingCartChecked = config.pricing.useExternalShoppingCartChecked;
        this.licenseKeyDataStoreURL = config.licenseKeyStoreURL;
        this.assignedLicenseKeys = config.LicenseKeys.assignedLicenseKeys;
        this.defaultChecked = config.LicenseKeys.defaultChecked;
        this.companyCode = config.company;
        this.additionalInfoDataList = config.additionalInfo.dataList;
        this.additionalInfoLangList = config.additionalInfo.langList;
        this.defaultLanguage = config.defaultLanguage;

        this.priceDescriptionDataList = config.priceDescription.dataList;
        this.priceDescriptionLangList = config.priceDescription.langList;

        this.addImg = config.images.addimg;
        this.deleteImg = config.images.deleteImg;

        this.errorTitle = config.errorTitle;
        this.errorMessage1 = config.errorMessage1;
        this.errorMessage2 = config.errorMessage2;
        this.errorMessage3 = config.errorMessage3;
        this.errorMessage4 = config.errorMessage4;
        this.errorMessage5 = config.errorMessage5;
        this.errorMessage6 = config.errorMessage6;
        this.errorMessage6Title = config.errorMessage6Title;
        this.errorMessage7 = config.errorMessage7;

        Ext.taopix.pricingPanel.superclass.constructor.call(this, config);
    },

    reloadLicenseKeyStore: function(pCompanyCode)
    {
        Ext.getCmp('licenseKeyGrid').getStore().reload({
            params: {
                companycode: pCompanyCode
            }
        });
    },

    covertLicenseKeySelectionToString: function()
    {
        var licenseKeyGridObj = Ext.getCmp('licenseKeyGrid');
        var selRecords = licenseKeyGridObj.selModel.getSelections();

        var codeList = '';

        for (var rec = 0; rec < selRecords.length; rec++)
        {
            codeList = codeList + selRecords[rec].data.code;

            if (rec != selRecords.length - 1)
            {
                codeList = codeList + ',';
            }
        }

        return codeList;
    },
    /* Component initialization */
    initComponent: function()
    {
        var useExternalCartChecked = false;
        var self = this;

        var licenseKeysCheckboxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
            listeners: {
                selectionchange: function(licenseKeysCheckboxSelectionModelObj)
                {
                    var selectionCount = licenseKeysCheckboxSelectionModelObj.getCount();
                }
            }
        });

        var licenseKeyStore =  new Ext.data.Store({
            id: 'lKeyStore',
            proxy: new Ext.data.HttpProxy({
                url:self.licenseKeyDataStoreURL,
                method: 'GET'
            }),
            reader: new Ext.data.ArrayReader({
                idIndex: 0
            },
            Ext.data.Record.create([
            {
                name: 'id',
                mapping: 0
            },

            {
                name: 'code',
                mapping: 1
            },

            {
                name: 'active',
                mapping: 3
            }
            ])
            )
        });

        var priceListStore =  new Ext.data.Store({
            id: 'priceListStore',
            proxy: new Ext.data.HttpProxy({
                url:'index.php?fsaction=AjaxAPI.callback&ref='+self.sessionRef+'&cmd=PRICELISTS&category='+this.category+'&company='+this.companyCode+'&displayCustom=1'
            }),
            reader: new Ext.data.ArrayReader({
                idIndex: 0
            },
            Ext.data.Record.create([
            {name: 'id',
                mapping: 0
            },

            {
                name: 'code',
                mapping: 1
            },

            {
                name: 'name',
                mapping: 2
            },

            {
                name: 'price',
                mapping: 3
            },

            {
                name: 'qtyisdropdown',
                mapping: 6
            },
            {
                name: 'taxcode',
                mapping: 7
            }

            ])
            )
        });

        var priceListCombo =  new Ext.form.ComboBox({
            id: 'pricelistid',
            name: 'pricelistid',
            width:300,
            fieldLabel: gLabelPriceList,
            mode: 'local',
            editable: false,
            forceSelection: true,
            store:priceListStore,
            listeners: {
				'select': function(combo, record, index)
				{
				  if (record.id != '-1')
				  {
					Ext.getCmp('saveAsPriceList').disable();
					Ext.getCmp('price').reload(record.data.price);
					Ext.getCmp('price').disable();
					if (Ext.getCmp('fixedquantityrange'))
					{
						Ext.getCmp('fixedquantityrange').disable();
					}

					Ext.getCmp('taxcode').setValue(record.data.taxcode);
					Ext.getCmp('taxcode').disable();
				  }
				  else
				  {
					Ext.getCmp('saveAsPriceList').enable();
					Ext.getCmp('price').enable();

					if (Ext.getCmp('fixedquantityrange'))
					{
						Ext.getCmp('fixedquantityrange').enable();
					}

					Ext.getCmp('taxcode').enable();
				  }

				  if (record.data.qtyisdropdown == '1')
				  {
					Ext.getCmp('fixedquantityrange').setValue(true);
				  }
				  else
				  {
					Ext.getCmp('fixedquantityrange').setValue(false);
				  }
				}
		    },
            selectOnFocus: true,
            triggerAction: 'all',
            valueField: 'id',
            displayField: 'name',
            useID: true,
            allowBlank: false,
            post: true
        });

        var taxCodeStore =  new Ext.data.Store({
            id: 'taxcodestore',
            proxy: new Ext.data.HttpProxy({
                url:'index.php?fsaction=AjaxAPI.callback&ref='+self.sessionRef+'&cmd=GETTAXCODELIST&company='+this.companyCode
            }),
            reader: new Ext.data.ArrayReader({
                idIndex: 0
            },
            Ext.data.Record.create([
				{name: 'id', mapping: 0},
				{name: 'code', mapping: 1},
				{name: 'name', mapping: 2}
            ])
            )
        });

        var taxCodeCombo = new Ext.form.ComboBox({
            id: 'taxcode',
            name: 'taxcode',
            width:300,
            fieldLabel: gLabelTaxRate,
            mode: 'local',
            editable: false,
            forceSelection: true,
            store: taxCodeStore,
            selectOnFocus: true,
            triggerAction: 'all',
            valueField: 'code',
            displayField: 'name',
            useID: true,
            allowBlank: false,
            post: true
        });

        /* Should be products only */
        if (this.qtyIsDropDown == '1')
        {
            quantityIsDropDownChecked = true;
        }
        else
        {
            quantityIsDropDownChecked = false;
        }

        /* Should be sub components only */
        if (this.inheritParentQty == '1')
        {
            inheritParentQtyChecked = true;
        }
        else
        {
            inheritParentQtyChecked = false;
        }

        if (this.useExternalShoppingCart == '1')
        {
            if (this.useExternalShoppingCartChecked == '1')
            {
                useExternalCartChecked = true;
            }
        }

		if ((this.pricingModel == 7) || (this.pricingModel == 8) || (this.pricingModel == 5))
		{
			gridWindowWidth = 915;
            widthLicensekeygridCode = 788;
            widthLanguageLangCol = 300;
            widthLanguageTextCol = 553;
            widthLanguageLangField = 280;
            widthLanguageTextField = 538;
		}
		else
		{
			gridWindowWidth = 637;
            widthLicensekeygridCode = 510;
            widthLanguageLangCol = 260;
            widthLanguageTextCol = 315;
            widthLanguageLangField = 235;
            widthLanguageTextField = 307;
		}

        var columnWidth="";
        var fieldWidth="";
        if (this.pricingModel == 3){
            columnWidth= [115,115,115,115,115,115];
            fieldWidth= [96,96,97,97,97,97];
        }
        else
        {
            if(this.pricingModel == 5){
                columnWidth= [122,122,122,122,122,122,121,121];
                fieldWidth= [103,103,102,102,102,102,102,102];
            }
            else
            {
                if(this.pricingModel == 7){
                    columnWidth= [122,122,122,122,122,122,121,121,121];
                    fieldWidth= [103,103,102,102,102,102,102,102,102];
                }
                else
                {
                    columnWidth= [95,95,94,94,95,95,95,95,95];
                    fieldWidth= [75,75,75,75,75,75,75,74,74];
                }
            }
        }

        Ext.apply(this, {
            items: [{
                xtype:'panel',
                layout: 'form',
                id:'mainpricepanel',
                labelWidth: 70,
                border:true,
                frame: true,
                autoScroll: true,
                height: 390,
                items:[
                {
                    xtype: 'container',
                    layout: 'column',
                    items: [

                    {
                        xtype : 'container',
                        layout : 'form',
                        labelWidth: 130,
                        items: [priceListCombo,taxCodeCombo,
                        {
                            xtype: 'checkbox',
                            id: 'fixedquantityrange',
                            name: 'fixedquantityrange',
                            boxLabel: gLabelFixedQuantityRanges,
                            checked: quantityIsDropDownChecked
                        }, {
                            xtype: 'checkbox',
                            id: 'inheritparentqty',
                            name: 'inheritparentqty',
                            boxLabel: gLabelInheritParentQty,
                            checked: inheritParentQtyChecked
                        }, {
                            xtype: 'checkbox',
                            id: 'useexternalshoppingcart',
                            name: 'useexternalshoppingcart',
                            boxLabel: gLabelUseExternalShoppingCart,
                            checked: useExternalCartChecked
                        }]
                    },

                    {
                        xtype : 'container',
                        layout : 'form',
                        items: [{
                            xtype: 'tbbutton',
                            toolTip: 'Save as Pricelist',
                            iconCls: 'silk-disk',
                            id:'saveAsPriceList',
                            handler: onSaveAsPriceList,
                            columnWidth: 0.32,
                            style:"margin-left:7px"
                        } ]
                    }
                    ]
                },
                {
                    xtype: 'panel',
                    labelWidth: 60,
                    items: [
                    {
                        xtype: 'tabpanel',
                        id: 'componenttabpanel',
                        deferredRender: false,
                        activeTab: 0,
                        height: 255,
                        shadow: true,
                        plain:true,
                        bodyBorder: true,
                        layoutOnTabChange: true,
                        border: true,
                        style: 'margin-top: 7px',
                        bodyStyle:'border-right: 1px solid #96bde7;',
                        defaults:{
                            frame: false,
                            autoScroll: true,
                            hideMode:'offsets',
                            layout: 'form',
                            labelWidth: 150,
                            style:'padding:10px; background-color: #eaf0f8;'
                        },
                        items: [
                        {
                            title: gLabelPricingTab,
                            id: "priceTab",
                            autoScroll: true,
                            items:[
                            new Ext.taopix.ComponentPricePanel({
                                id: 'price',
                                name:'price',
                                isProduct: this.isProduct,
                                height: 188,
                                post: true,
                                width: gridWindowWidth,
                                pricingModel: this.pricingModel,
                                addPic: this.addImg,
                                delPic: this.deleteImg,
                                data: this.price,
                                columnWidth: columnWidth,
                                fieldWidth: fieldWidth,
                                pricingDecimalPlaces: this.decimalPlaces,
                                errorTitle: this.errorTitle,
                                errorMessage1: this.errorMessage1,
                                errorMessage2: this.errorMessage2,
                                errorMessage3: this.errorMessage3,
                                errorMessage4: this.errorMessage4,
                                errorMessage5: this.errorMessage5,
								errorMessage7: this.errorMessage7
                            })
                            ]
                        },
                        {
                            title: gLabelLicenseKeyTab,
                            id: "licenseKeyTab",
                            height: 255,
                            items: [
                            new Ext.form.Checkbox({
                                boxLabel: gLabelDefault,
                                id: 'defaultLicenseKeys',
                                name: 'defaultLicenseKeys',
                                hideLabel:true,
                                checked: false,
                                listeners: {
                                    'check': {
                                        /* this 'scope' value is CRITICAL so that the event is fired in */
                                        /* the scope of the component, not the anonymous function...    */

                                        scope: this,

                                        fn: function(cb, checked){
                                            if (checked)
                                            {
                                                Ext.getCmp('licenseKeyGrid').disable();
                                                licenseKeysCheckboxSelectionModelObj.selectAll();
                                            }
                                            else
                                            {
                                                licenseKeysCheckboxSelectionModelObj.clearSelections();
                                                Ext.getCmp('licenseKeyGrid').enable();
                                            }
                                        }
                                    }
                                }
                            }),
                            new Ext.grid.GridPanel({
                                id: 'licenseKeyGrid',
                                style:'border:1px solid #B5B8C8;',
                                height: 170,
                                deferRowRender:false,
                                hidden:false,
                                width: gridWindowWidth,
                                store: licenseKeyStore,
                                selModel: licenseKeysCheckboxSelectionModelObj,
                                colModel: new Ext.grid.ColumnModel({
                                    id: 'lKeyColModel',
                                    defaults: {
                                        sortable: true,
                                        resizable: true
                                    },
                                    columns: [
                                        licenseKeysCheckboxSelectionModelObj,
                                        {
                                            header: 'ID',
                                            dataIndex: 'id',
                                            sortable: true,
                                            hidden: true
                                        },

                                        {
                                            header: gLabelCode,
                                            width: widthLicensekeygridCode,
                                            dataIndex: 'code'
                                        },

                                        {
                                            header: gLabelStatus,
                                            width: 80,
                                            dataIndex: 'active',
                                            align: 'right',
                                            renderer:licenseActiveRenderer
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
                            })]
                        },
                        {
                            title: gLabelPriceDescriptionTab,
                            id: 'priceDescriptionTabStrip',
                            height: 255,
                            items: [
                            new Ext.taopix.LangPanel({
                                id: 'pricedescription',
                                name:'pricedescription',
                                height: 190,
                                post: true,
                                width: gridWindowWidth,
                                style: 'border:1px solid #b4b8c8',
                                data: {
                                    langList: this.priceDescriptionLangList,
                                    dataList: this.priceDescriptionDataList
                                },
                                settings:
                                {
                                    headers:     {
                                        langLabel: gLabelLanguageName,
                                        textLabel: gLabelInformation,
                                        deletePic: this.deleteImg,
                                        addPic: this.addImg,
                                        startMinValue:0
                                    },
                                    defaultText: {
                                        langBlank: gLabelSelectLanguage,
                                        textBlank: gExtJsTypeValue,
                                        defaultValue: this.defaultLanguage
                                    },
                                    columnWidth: {
                                        langCol: 215,
                                        textCol: 230,
                                        delCol: 35
                                    },
                                    fieldWidth:  {
                                        langField: 215,
                                        textField: 316
                                    },
                                    errorMsg:    {
                                        blankValue: gTextFieldBlank
                                    }
                                }
                            })
                            ]
                        },
                        {
                            title: gLabelAdditionalInfoTab,
                            height: 255,
                            items:[
                                new Ext.taopix.LangPanel({
                                id: 'priceadditionalinfo',
                                name:'priceadditionalinfo',
                                height: 190,
                                post: true,
                                width: gridWindowWidth,
                                style: 'border:1px solid #b4b8c8',
                                data: {
                                    langList: this.additionalInfoLangList,
                                    dataList: []
                                },
                                settings:
                                {
                                    headers:     {
                                        langLabel: gLabelLanguageName,
                                        textLabel: gLabelInformation,
                                        deletePic: this.deleteImg,
                                        addPic: this.addImg,
                                        startMinValue:0
                                    },
                                    defaultText: {
                                        langBlank: gLabelSelectLanguage,
                                        textBlank: gExtJsTypeValue,
                                        defaultValue: this.defaultLanguage
                                    },
                                    columnWidth: {
                                        langCol: widthLanguageLangCol,
                                        textCol: widthLanguageTextCol,
                                        delCol: 35
                                    },
                                    fieldWidth:  {
                                        langField: widthLanguageLangField,
                                        textField: widthLanguageTextField
                                    },
                                    errorMsg:    {
                                        blankValue: gTextFieldBlank
                                    }
                                }
                            })]
                        }
                        ]
                    }
                    ]

                }]
            }]
        });

        function licenseActiveRenderer(value, p, record)
        {
            if (record.data.code == '')
            {
                return '';
            }
            else
            {
                if (value == 0)
                {
                    return gLabelInactive;
                }
                else
                {
                    return gLabelActive;
                }
            }
        }

        function onSaveAsPriceList(btn, ev)
        {
            if (Ext.getCmp('price').isValid() == 0)
            {
				Ext.taopix.loadJavascript(self.windowToMask, '', 'index.php?fsaction=Admin.saveAsPriceList&ref='+self.sessionRef, '', '', 'initializeSaveAsPriceList', false);
            }
            else
            {
            	Ext.MessageBox.show({ title: self.errorMessage6Title, msg: self.errorMessage6, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
            }
        }

        licenseKeyStore.load();
        priceListStore.load();

        if (this.defaultChecked == '1')
        {

            Ext.getCmp('licenseKeyGrid').store.on({
                'load': function(){
                    Ext.getCmp('defaultLicenseKeys').setValue(true);
                    licenseKeysCheckboxSelectionModelObj.selectAll();
                }
            });
        }
        else
        {
            if (this.isEdit == '1')
            {
                Ext.getCmp('licenseKeyGrid').store.on({
                    'load': function(){

                        var keyIndexes = new Array();
                        for (i = 0; i < self.assignedLicenseKeys.length; i++)
                        {
                            index = Ext.getCmp('licenseKeyGrid').store.findExact('code', self.assignedLicenseKeys[i]);
                            keyIndexes.push(index);
                        }

                        licenseKeysCheckboxSelectionModelObj.selectRows(keyIndexes);

                    }
                });
            }
            else
            {

                Ext.getCmp('licenseKeyGrid').store.on({
                    'load': function(){
                        licenseKeysCheckboxSelectionModelObj.selectRows(self.assignedLicenseKeys);
                    }
                });
            }
        }

        Ext.getCmp('pricelistid').store.on({
            'load': function() {
                if (self.isPriceList == '1')
                {
					Ext.getCmp('pricelistid').setValue(self.priceListID);
					Ext.getCmp('saveAsPriceList').disable();
					Ext.getCmp('price').disable();
					if (Ext.getCmp('fixedquantityrange'))
					{
						Ext.getCmp('fixedquantityrange').disable();
					}

					Ext.getCmp('taxcode').disable();
                }
                else
                {
                    defaultValue = Ext.getCmp('pricelistid').store.getAt(0);
                    Ext.getCmp('pricelistid').setValue(defaultValue.data['id']);
                }
            }
        })

        Ext.getCmp('taxcode').store.on({
            'load': function() {
                Ext.getCmp('taxcode').setValue(self.taxCode);
            }
        });

        taxCodeStore.load();

        Ext.taopix.pricingPanel.superclass.initComponent.apply(this, arguments);

        if (this.isProduct == '0')
        {
            Ext.getCmp('componenttabpanel').remove('priceDescriptionTabStrip');
            Ext.getCmp('useexternalshoppingcart').destroy();

            if ((this.pricingModel != '7') && (this.pricingModel != '8'))
            {
            	Ext.getCmp('fixedquantityrange').destroy();
            	Ext.getCmp('componentPricingPanel').setHeight(355);
                Ext.getCmp('mainpricepanel').setHeight(355);
            }

			if (this.allowInherit !== 1)
			{
				Ext.getCmp('inheritparentqty').destroy();
			}
        }
        else
        {
            if (this.useExternalShoppingCart == '1')
            {
                Ext.getCmp('mainpricepanel').setHeight(380);
            }
            else
            {
                Ext.getCmp('useexternalshoppingcart').destroy();
            }

			// products can not inherit from parent
			Ext.getCmp('inheritparentqty').destroy();
        }

        if (this.additionalInfoDataList != '')
        {
            var values = this.additionalInfoDataList.split('<p>');
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
            Ext.getCmp('priceadditionalinfo').loadData({
                dataList: localisedData.dataList
            });
            Ext.getCmp('priceadditionalinfo').getBottomToolbar().items.items[0].getStore().loadData(localisedData.langListStore);
        }
    }
});
Ext.reg('taopixPricingPanel', Ext.taopix.pricingPanel);




Ext.taopix.urlParamPanel = Ext.extend(Ext.grid.EditorGridPanel, {
    initialData: {
        customparameters: []
    },

    data:   {
        customparameters: []
    },

    config: {
        fieldList: [],
        columnList: [
        {
            title: 'Parameter'
        },
        {
            title: 'Value'
        }
        ],
        columnWidth: [100, 150],
        addPic: '',
        delPic: '',
        outputFormat: 'NEWPARAMS'
    },


    /* Get locolized values from the grid */
    convertTableToString: function()
    {
        var resultString = "";
        var resultArray = [];
        var bufArray = [];
        this.stopEditing(false);

        switch (this.config.outputFormat)
        {
            case 'NEWPARAMS':
                for (var i=0; i<this.store.data.items.length; i++)
                {
                    bufArray = [];
                    for( var j = 0; j < this.store.data.items[i].fields.items.length-1; j++)
                    {
                        bufArray.push(this.store.data.items[i].data[this.store.data.items[i].fields.items[j].name]+'');
                    }
                    resultArray.push(bufArray.join('-'));
                }
                resultString = resultArray.join(' ');
                break;
            default:
                resultString = '';
        }

        return resultString;
    },


    /* Validating the fields */
    isValid: function()
    {
        for (var i=0, val='', el; i < this.store.data.items.length; i++)
        {
            for( var j = 0; j < this.store.data.items[i].fields.items.length-1; j++)
            {
                val = this.store.data.items[i].data[this.store.data.items[i].fields.items[j].name];
                el = this.config.fieldList[j];
                if ((el.minValue) && (val*1 <= el.minValue))
                {
                    return false;
                }
            }
        }
        if (this.convertTableToString() == '') return false;
        return true;
    },


    /* Add new record to the grid */
    onAddRecord: function()
    {
        var panel = this.findParentByType('taopixURLParamPanel');
        var components = panel.getBottomToolbar().items.items;

        for (var i = 0; i < components.length; i++)
        {
            if ((components[i].isValid) && (!components[i].isValid()))
            {
                return false;
            }
        }
        for (var i = 0, cmp, record={};
            i < components.length - 1; i++)

            {
            cmp = components[i];
            record['field'+i] = cmp.getValue();
            cmp.reset();
        }
        record['deleteIcon'] = '';

        var r = new panel.store.recordType(record);
        r.commit();
        panel.store.add(r);
        panel.getView().refresh();
    },


    /* Delete record fromt he grid */
    onDeleteRecord: function(pCode)
    {
        this.store.removeAt(this.store.findExact('field0',pCode));
        return false;
    },


    /* Reset grid to initial values */
    reset: function()
    {
        this.data = this.initialData;
        this.store.loadData(this.data.customparameters);
    },

    refresh: function()
    {
        this.getView().refresh();
    },


    /* Delete column renderer that shows delete icon and delete link */
    deleteColRenderer: function(pPanel, pRecord)
    {
        return '<div onClick="Ext.getCmp(\''+pPanel.id+'\').onDeleteRecord(\''+pRecord.data.field0+'\'); return false;" OnMouseOver="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\') + \' x-btn-over\';" OnMouseOut="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\');"><table cellspacing="0" class="x-btn  x-btn-icon"><tbody class="x-btn-small x-btn-icon-small-left"><tr><td class="x-btn-tl"><i>&nbsp;</i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-ml"><i>&nbsp;</i></td><td class="x-btn-mc"><em unselectable="on" class=""><button type="button" class="x-btn-text " style="background-image: url(&quot;'+pPanel.config.delPic+'&quot;);">&nbsp;</button></em></td><td class="x-btn-mr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-bl"><i>&nbsp;</i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i>&nbsp;</i></td></tr></tbody></table></div>';
    },

    checkWarning: function(obj, value, metaData, record, rowIndex, colIndex, store)
    {
        comp = obj.config.fieldList[colIndex];
        if ((comp.minValue) && (comp.minValue >= value*1))
        {
            return '<div class="processed-row">'+value+'</div>';
        }
        else
        {
            return value;
        }
    },

    afterRenderEvent: function()
    {
        Ext.query("#"+this.getId()+" .x-btn")[0].className = Ext.query("#"+this.getId()+" .x-btn")[0].className + ' x-btn-over';
        Ext.query("#"+this.getId()+" .x-toolbar")[0].setAttribute('style', 'padding:5px 3px !important; border:0  ');
        Ext.query("#"+this.getId())[0].setAttribute('style', 'border:1px solid #b4b8c8 !important; ');
    },

    constructor: function(config)
    {
        if (config.data.customparameters != undefined) this.data.customparameters = config.data.customparameters;
        if (config.config.fieldList != undefined) this.config.fieldList = config.config.fieldList;
        if (config.config.columnList != undefined) this.config.columnList = config.config.columnList;
        if (config.config.columnWidth != undefined) this.config.columnWidth = config.config.columnWidth;
        if (config.config.addPic != undefined) this.config.addPic = config.config.addPic;
        if (config.config.delPic != undefined) this.config.delPic = config.config.delPic;
        if (config.config.outputFormat != undefined) this.config.outputFormat = config.config.outputFormat;

        Ext.taopix.urlParamPanel.superclass.constructor.call(this, config);
    },

    /* Component initialization */
    initComponent: function()
    {
        var self = this;

        var storeFields = [];
        var gridColumns = [];
        var bbarFields  = [];

        for (var i = 0, comp; i < this.config.fieldList.length; i++)
        {
            comp = this.config.fieldList[i];
            storeFields.push({
                name: 'field'+i
            });

            switch(comp.fieldType)
            {
                case 'text':
                    gridColumns.push({
                        header: this.config.columnList[i].title,
                        width: this.config.columnWidth[i],
                        dataIndex: 'field'+i,
						align: 'left',
                        menuDisabled: true,
                        editor: new Ext.form.TextField({
                            allowBlank: false
                        })
                    });
                    bbarFields.push({
                        xtype:'textfield',
                        width: comp.width,
                        emptyText: comp.emptyText,
                        allowBlank: comp.allowBlank,
                        hideLabel: comp.hideLabel,
                        fieldLabel: comp.fieldLabel,
                        minLength: comp.minLength,
                        maxLength: comp.maxLength,
						align: 'left',
                        style: 'margin-right:7px'
                    });
                    break;
                case 'number':
                    gridColumns.push(
                    {
                        header: this.config.columnList[i].title,
                        width: this.config.columnWidth[i],
                        dataIndex: 'field'+i,
                        menuDisabled: true,
                        editor: new Ext.form.NumberField(
                        {
                            allowBlank: false,
                            minValue: comp.minValue, /*maxValue: comp.maxValue, */
                            allowDecimals: comp.allowDecimals,
                            decimalPrecision: comp.decimalPrecision,
                            decimalSeparator: comp.decimalSeparator,
                            allowNegative: comp.allowNegative
                        }
                        ),
                        renderer: function(value, metaData, record, rowIndex, colIndex, store)
                        {
                            return self.checkWarning(self,value, metaData, record, rowIndex, colIndex, store);
                        }
                    }
                    );
                    bbarFields.push(
                    {
                        xtype:'numberfield',
                        width: comp.width,
                        emptyText: comp.emptyText,
                        allowBlank: comp.allowBlank,
                        hideLabel: comp.hideLabel,
                        fieldLabel: comp.fieldLabel,
                        minValue: comp.minValue, /*maxValue: comp.maxValue,*/
                        allowDecimals: comp.allowDecimals,
                        decimalPrecision: comp.decimalPrecision,
                        decimalSeparator: comp.decimalSeparator,
                        allowNegative: comp.allowNegative,
                        style: 'margin-right:7px'
                    }
                    );
                    break;
                default:
                    gridColumns.push({
                        header: this.config.columnList[i].title,
                        width: this.config.columnWidth[i],
                        dataIndex: 'field'+i,
                        menuDisabled: true,
                        editor: new Ext.form.TextField({
                            allowBlank: false
                        })
                    });
                    bbarFields.push({
                        xtype:'textfield',
                        width: comp.width,
                        emptyText: comp.emptyText,
                        allowBlank: comp.allowBlank,
                        hideLabel: comp.hideLabel,
                        fieldLabel: comp.fieldLabel,
                        minLength: comp.minLength,
                        maxLength: comp.maxLength,
                        style: 'margin-right:7px'
                    });
            }
        }
        bbarFields.push({
            xtype:'button',
            handler:this.onAddRecord,
            icon: this.config.addPic,
            minWidth:24,
            width:24,
            listeners: {
                'mouseout': function(){
                    this.getEl().addClass('x-btn-over');
                }
            }
        });
        storeFields.push({
            name: 'fieldDel'
        });
        gridColumns.push({
            id:'deleteIcon',
            width: 35,
            sortable: false,
            renderer: function(value, p, record, rowIndex, colIndex, store){
                return self.deleteColRenderer(self, record);
            },
            menuDisabled: true
        });

        Ext.apply(Ext.util.Format, {
            htmlEncode : function(value){
                return !value ? value : String(value).replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
            },

            htmlDecode : function(value){
                return !value ? value : String(value).replace(/&gt;/g, ">").replace(/&lt;/g, "<").replace(/&quot;/g, '"').replace(/&#039;/g, "'").replace(/&amp;/g, "&");
            }
        });

        Ext.apply(this, {
            store: new Ext.data.ArrayStore({
                data: this.data.customparameters,
                fields: storeFields
            }),
            cm: new Ext.grid.ColumnModel({
                columns: gridColumns
            }),
            stripeRows: true,
            mode: 'local',
            stateful: true,
            stateId: 'grid',
            columnLines:true,
            clicksToEdit: 1,
            trackMouseOver: false,
            autoEncode: true,
            bbar: [bbarFields]
        });

        this.on('validateedit', function(e) {
            this.getView().refresh();
        });

        Ext.taopix.urlParamPanel.superclass.initComponent.apply(this,arguments);
        this.on('afterRender', function() {
            Ext.taopix.urlParamPanel.superclass.afterRender.apply(this, arguments);
            this.afterRenderEvent();
        }, this);

        this.initialData = this.data;
    }
});
Ext.reg('taopixURLParamPanel', Ext.taopix.urlParamPanel);

/**
 * Creates an authentication window to reautheniticate actions that may involve sensitive data.
 *
 * @param pConfig Object containing configuration parameters.
 * @param pConfig.sessionRef Ref of the current main session.
 * @param pConfig.onSuccess Callback of action to execute on successful authentication.
 * @param pConfig.message Optional. Message to display in the authentication dialog.
 * @returns Ext.Window EXTJS Window object
 */
Ext.taopix.ReauthenticationDialog = Ext.extend(Ext.Window,
{
	reason: '',
	sessionRef: 0,
	onSuccess: function(){},
	format: ((document.location.protocol !== 'https:') ? 1 : 0),
	reauthDialogID: 'reauthdialog',
	reauthFormPanelID: 'reauthform',

	initComponent: function(pConfig)
	{
		Ext.taopix.ReauthenticationDialog.superclass.initComponent.apply(this, []);
	},

	constructor: function(pConfig)
    {
		var self = this;
		this.reason = pConfig.reason;
		this.sessionRef = pConfig.ref;
		this.onSuccess = pConfig.success;
		var messageBox = {};
		var FormPanelItems = [];
		var title = pConfig.title;

		if ((typeof pConfig.message != 'undefined') && (pConfig.message != ''))
		{
			 FormPanelItems.push({
				xtype: 'box',
				autoEl:
				{
					tag: 'p'
				},
				html: pConfig.message,
				style: 'margin-bottom: 1em; font-size: 12px;'
			});
		}

		FormPanelItems.push(
		{
			fieldLabel: Ext.taopix.ReauthenticationDialog.strings.labelPassword,
			id: 'reauth_password',
			name: 'reauth_password',
			inputType: 'password',
			width: 270,
			allowBlank: false,
			labelStyle: 'text-align: right;',
			enableKeyEvents: true,
			post: false,
			listeners:
			{
				keypress:
				{
					fn: function(pElement, pEvent)
					{
						 var keyPressed = (pEvent.keyCode || pEvent.which);

						// Trigger form submit if the enter key is pressed.
						if (keyPressed === 13)
						{
							self.authenticateHandler();
						}
					}
				}
			}
		});

		var reauthPanelObj = new Ext.taopix.FormPanel(
		{
			id: self.reauthFormPanelID,
			header: false,
			frame: true,
			width: 490,
			labelWidth: 180,
			defaultType: 'textfield',
			autoHeight: true,
			bodyStyle: 'padding: 10px 20px;',
			items: FormPanelItems
		});

		var config =
		{
			id: self.reauthDialogID,
			closable: false,
			plain: true,
			modal: true,
			draggable: true,
			resizable: false,
			layout: 'fit',
			autoHeight: true,
			width: 550,
			items: reauthPanelObj,
			cls: 'left-right-buttons',
			title: title,
			buttons:
			[
				{
					text: Ext.taopix.ReauthenticationDialog.strings.buttonCancel,
					cls: 'x-btn-left',
					listeners:
					{
						click:
						{
							fn: function()
							{
								Ext.getCmp(self.reauthDialogID).close();
							}
						}
					}
				},
				{
					text: Ext.taopix.ReauthenticationDialog.strings.buttonAuthenticate,
					id: 'addEditButton',
					cls: 'x-btn-right',
					listeners:
					{
						click:
						{
							fn: function()
							{
								self.authenticateHandler();
							}
						}
					}
				}
			],
			onHide: function()
			{
				// Reset field values on hide.
				this.resetFields();
			},
			onClose: function()
			{
				// Reset field values on close.
				this.resetFields();
			}
		};

        Ext.taopix.ReauthenticationDialog.superclass.constructor.call(this, config);
    },
	authenticateHandler: function()
	{
		var submitURL = 'index.php?fsaction=Admin.reauthenticate&ref=' + this.sessionRef;
		var fp = Ext.getCmp(this.reauthFormPanelID)
		var form = fp.getForm();

		if (form.isValid())
		{
			// Secure the password before sending if not on HTTPS.
			var password = Ext.getCmp('reauth_password').getValue();

			if (this.format == 1)
			{
				password = hex_md5(password);
			}

			var paramArray = new Object();
			paramArray['reason'] = this.reason;
			paramArray['format'] = this.format;
			paramArray['reauth_password'] = password;

			Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, Ext.taopix.ReauthenticationDialog.messageSaving, this.authenticateCallback, this);
		}
	},
	authenticateCallback: function(pUpdated, pActionForm, pActionData, pInstance)
	{
		if ((pUpdated) && (pActionData.result.success === 'true'))
		{
			pInstance.close();

			if (typeof pInstance.onSuccess === 'function')
			{
				pInstance.onSuccess();
			}
		}
		else
		{
			Ext.MessageBox.show(
			{
				title: pActionData.result.title,
				msg: pActionData.result.msg,
				buttons: Ext.MessageBox.OK,
				icon: Ext.MessageBox.WARNING
			});
		}
	},
	resetFields: function()
	{
		Ext.getDom('reauth_password').value = "";
	}
});

Ext.reg('ReauthenticationDialog', Ext.taopix.ReauthenticationDialog);