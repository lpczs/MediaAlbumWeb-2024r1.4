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

	formPanelPostSuper: function(pFormPanel, pTheForm, pAdditionalParams, pURL, pMessage, pCallback, pClientValidation)
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
								value: fieldValue
							});
						}
					}
				}

				if (pFormPanel)
				{
					customPanels = pFormPanel.findByType('taopixLangPanel');
					customPanels = customPanels.concat(pFormPanel.findByType('taopixCountryPanel'), pFormPanel.findByType('taopixInputPanel'), pFormPanel.findByType('taopixOldFormatInputPanel'), pFormPanel.findByType('ComponentPricePanel'));

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
							value: pFormPanel.baseParams[i]
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
						value: pAdditionalParams[i]
					});
				}
			}

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
						pCallback(true, pActionForm, pActionData);
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
						pCallback(false, pActionForm, pActionData);
					}
			});
		}
	},

	formPanelPost: function(pFormPanel, pTheForm, pAdditionalParams, pURL, pMessage, pCallback)
	{
		/* function to handle posting of formpanels */
		this.formPanelPostSuper(pFormPanel, pTheForm, pAdditionalParams, pURL, pMessage, pCallback, true);
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




