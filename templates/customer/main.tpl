 {* COMMON JAVSCRIPT (LARGE/SMALL) *}

{literal}
var session = "{/literal}{$session}{literal}";
var gSession = "{/literal}{$session}{literal}";
var gIsMobile = "{/literal}{$issmallscreen}{literal}";
var ssotoken = "{/literal}{$ssotoken}{literal}";
var gSSOToken = "{/literal}{$ssotoken}{literal}";
var unshareArray = '';
var gAlerts = 0;
var addressUpdated = "{/literal}{$addressupdated}{literal}";
var countryChanged = false;
var addToAnyIntialised = false;
var processingAjax = false;
var isHighLevel = {/literal}{$ishighlevel}{literal};
var basketRef = "{/literal}{$basketref}{literal}";
var languageCode = "{/literal}{$languagecode}{literal}";


/* ACCOUNT DETAILS */
{/literal}

{if $section == 'accountdetails'}

    {literal}

var gAs_jsonCity = '';
var gAs_jsonCounty = '';
var firstname = "{/literal}{$contactfname}{literal}";
var lastname = "{/literal}{$contactlname}{literal}";
var company = "{/literal}{$companyname}{literal}";
var add1 = "{/literal}{$address1}{literal}";
var add2 = "{/literal}{$address2}{literal}";
var add3 = "{/literal}{$address3}{literal}";
var add4 = "{/literal}{$address4}{literal}";
var add41 = "{/literal}{$add41}{literal}";
var add42 = "{/literal}{$add42}{literal}";
var add43 = "{/literal}{$add43}{literal}";
var city = "{/literal}{$city}{literal}";
var county = "{/literal}{$county}{literal}";
var state = "{/literal}{$state}{literal}";
var regioncode = "{/literal}{$regioncode}{literal}";
var region = "";
var postcode = "{/literal}{$postcode}{literal}";
var country = "{/literal}{$country}{literal}";
var countryName = "{/literal}{$countryname}{literal}";
var registeredtaxnumbertype = "{/literal}{$registeredtaxnumbertype}{literal}";
var registeredtaxnumber = "{/literal}{$registeredtaxnumber}{literal}";
var TPX_REGISTEREDTAXNUMBERTYPE_NA = "{/literal}{$TPX_REGISTEREDTAXNUMBERTYPE_NA}{literal}";
var TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL = "{/literal}{$TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL}{literal}";
var TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE = "{/literal}{$TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE}{literal}";
var lastSuccesfulCountry = country;
var originalEmail = "{/literal}{$email}{literal}";
var isCustomerAuthEnabled = "{/literal}{$customerupdateauthrequired}{literal}";

    {/literal}

{/if}

{literal}

if (addressUpdated == 1)
{
    var hideConfigFields = 1;
}
else
{
    var hideConfigFields = 0;
}

/* AJAX */
/* function to create an XMLHttp Object */
function getxmlhttp()
{
    /* create a boolean variable to check for a valid Microsoft ActiveX instance */
    var xmlhttp = false;
    /* check if we are using Internet Explorer */
    try
    {
        /* if the Javascript version is greater then 5 */
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch (e)
    {
        /* if not, then use the older ActiveX object */
        try
        {
            /* if we are using Internet Explorer */
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        catch (e)
        {
            /* else we must be using a non-Internet Explorer browser */
            xmlhttp = false;
        }
    }

    /* if we are not using IE, create a JavaScript instance of the object */
    if (!xmlhttp && typeof XMLHttpRequest != 'undefined')
    {
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}


/* function to process an XMLHttpRequest */
function processAjax(obj, serverPage, requestMethod, params, async)
{
	// initialise the timout id ready for the loading screen display
	var timeoutID = 0;

    // add the ref and ssotoken onto the URL if it is missing
    if ((serverPage.indexOf('&ref=') == -1) || (serverPage.indexOf('?ref=') == -1))
    {
        if (serverPage.indexOf('?') != -1)
        {
            serverPage += '&ref=' + gSession;
        }
        else
        {
            serverPage += '?ref=' + gSession;
        }
    }

    if ((serverPage.indexOf('&ssotoken=') == -1) || (serverPage.indexOf('?ssotoken=') == -1))
    {
        if (serverPage.indexOf('?') != -1)
        {
            serverPage += '&ssotoken=' + gSSOToken;
        }
        else
        {
            serverPage += '?ssotoken=' + gSSOToken;
        }
    }

	if ('POST' === requestMethod) {
		// Add CSRF token to post submissions
		var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
		if (csrfMeta) {
			var csrfToken = csrfMeta.getAttribute('content');

			if (typeof params !== 'undefined' && null !== params && params.length > 0) {
				params += '&csrf_token=' + csrfToken;
			} else {
				params = 'csrf_token=' + csrfToken;
			}
		}
	}

    /* get an XMLHttpRequest object for use */
    /* make xmlhttp local so we can run simlutaneous requests */
	if (obj == 'ajaxdivupdate')
	{
		var xmlhttp = new XMLHttpRequest();
	}
	else
	{
    	var xmlhttp = getxmlhttp();
	}

    xmlhttp.open(requestMethod, serverPage+"&dummy=" + new Date().getTime(), async);

	if ('POST' === requestMethod) {
		xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	}

	if (obj == 'ajaxdivupdate')
	{
		// update the obj so that it can be used in html operations later
		obj = 'ajaxdiv';
		// set a loading screen to appear 1 second after the option has been selected
		// Store the ID so that it can be turned off it is returned before 1 second has elapsed
		timeoutID = window.setTimeout(function()
		{
				showLoadingDialog('{/literal}{#str_MessageLoading#}{literal}')
		}, 1000);
		// set the XHR request timeout to 10 seconds
		xmlhttp.timeout = 10000;
	}

    xmlhttp.onreadystatechange = function()
    {
        if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
        {
            switch (obj)
            {
                case 'verify':
                    updateAccountDetails(xmlhttp.responseText);
                    break;
	 			case 'verifyPassword':
                    try {
                        var response = parseJson(xmlhttp.responseText);
                        if (response.valid)
                        {
                            verifyAddress();
                        }
                        else
                        {
                            showVerificationFailedMessage(response.result)
                        }
                        break;
                    } catch (e) {
                        showVerificationFailedMessage("{/literal}{#str_ErrorLoginHasExpired#}{literal}", function() {
                            window.location = window.location.href.replace('#', '');
                        });
                        break;
                    }
				case 'getshareonlineprojecturl':
					var response = JSON.parse(xmlhttp.responseText);

					if (response.shareurl)
					{
						showShareProjectDialogLarge(response.shareurl);
					}

					break;
                case 'unshare':
					try
					{
					    var unshareResult = parseJson(xmlhttp.responseText);

						var confirmationBoxObj = document.getElementById('confirmationBox');
						var confirmationBoxTextObj = document.getElementById('confirmationBoxText');
						var shimObj = document.getElementById('shim');

						confirmationBoxObj.style.display = 'block';
						confirmationBoxTextObj.style.display = 'block';

						if (shimObj)
						{
							shimObj.style.display = 'block';
							shimObj.style.zIndex = 200;
							shimObj.style.height = document.body.offsetHeight + 'px';
							document.body.className +=' hideSelects';
						}

						confirmationBoxObj.style.left = Math.round(shimObj.offsetWidth / 2 - confirmationBoxObj.offsetWidth/2) + 'px';

						var windowHeight = document.documentElement.clientHeight;
						confirmationBoxObj.style.top = Math.round((windowHeight - confirmationBoxObj.offsetHeight) / 2) + 'px';

						confirmationBoxTextObj.className = confirmationBoxTextObj.className.replace(' confirmationText', '');
						if (unshareResult['success'])
						{
							if (unshareResult['title'] == '')
							{
								confirmationBoxTextObj.className = confirmationBoxTextObj.className + ' confirmationText';
								confirmationBoxTextObj.innerHTML = "{/literal}{#str_SuccessUnshare#}{literal}";
								if (unshareObjLink)
								{
									unshareObjLink.style.display = 'none';
								}
							}
							else
							{
								confirmationBoxTextObj.innerHTML = unshareResult['title'] + '. ' + unshareResult['msg'];
							}
						}
						else
						{
							confirmationBoxTextObj.innerHTML = "{/literal}{#str_ErrorConnectFailure#}{literal}";
						}

						var buttonsHolderConfirmationObj = document.getElementById('buttonsHolderConfirmation');
						var buttonsHolderQuestionObj = document.getElementById('buttonsHolderQuestion');
						if (buttonsHolderConfirmationObj)
						{
							buttonsHolderConfirmationObj.style.display = 'block';
						}
						if (buttonsHolderQuestionObj)
						{
							buttonsHolderQuestionObj.style.display = 'none';
						}
					}
					catch(e)
					{
						window.location.replace(window.location.href.replace('#', ''));
					}
                    break;
                case 'shareByEmail':
					try
					{
						var shareResult = parseJson(xmlhttp.responseText);

						var confirmationBoxTextObj = document.getElementById('confirmationBoxText');
						if (shareResult)
						{
							if (shareResult['result'] == '')
							{
								confirmationBoxTextObj.className = confirmationBoxTextObj.className.replace(' confirmationText', '') + ' confirmationText';
								confirmationBoxTextObj.innerHTML = "{/literal}{#str_MessageEmailSent#}{literal}";

								if (shareObjLink)
								{
									var prevSibling = shareObjLink.previousSibling;

									while(prevSibling && prevSibling.nodeType != 1)
									{
										prevSibling = prevSibling.previousSibling;
									}
									prevSibling.style.display = 'block';
								}
							}
							else
							{
								confirmationBoxTextObj.innerHTML = shareResult['result'] + '. ' + shareResult['resultparam'];
							}
						}
						else
						{
							confirmationBoxTextObj.innerHTML = "{/literal}{#str_ErrorConnectFailure#}{literal}";
						}

						var buttonsHolderConfirmationObj = document.getElementById('buttonsHolderConfirmation');
						var buttonsHolderQuestionObj = document.getElementById('buttonsHolderQuestion');

						if (buttonsHolderConfirmationObj)
						{
							buttonsHolderConfirmationObj.style.display = 'block';
						}

						if (buttonsHolderQuestionObj)
						{
							buttonsHolderQuestionObj.style.display = 'none';
						}
					}
					catch(e)
					{
						window.location.replace(window.location.href.replace('#', ''));
					}
                    break;
                case 'mailToLink':
					try
					{
						var shareResult = parseJson(xmlhttp.responseText);

						var confirmationBoxTextObj = document.getElementById('confirmationBoxText');
						if (shareResult)
						{
							if (shareResult['result'] == '')
							{
								confirmationBoxTextObj.className = confirmationBoxTextObj.className.replace(' confirmationText', '') + ' confirmationText';
								confirmationBoxTextObj.innerHTML = nlToBr("{/literal}{#str_MessageCheckEmailSoftware#}{literal}");

								if (shareObjLink)
								{
									var prevSibling = shareObjLink.previousSibling;

									while(prevSibling && prevSibling.nodeType != 1)
									{
										prevSibling = prevSibling.previousSibling;
									}
									prevSibling.style.display = 'block';
								}

								window.location.href = shareResult['resultparam'];
							}
							else
							{
								confirmationBoxTextObj.innerHTML = shareResult['result'] + '. ' + shareResult['resultparam'];
							}
						}
						else
						{
							confirmationBoxTextObj.innerHTML = "{/literal}{#str_ErrorConnectFailure#}{literal}";
						}

						var buttonsHolderConfirmationObj = document.getElementById('buttonsHolderConfirmation');
						var buttonsHolderQuestionObj = document.getElementById('buttonsHolderQuestion');
						if (buttonsHolderConfirmationObj)
						{
							buttonsHolderConfirmationObj.style.display = 'block';
						}

						if (buttonsHolderQuestionObj)
						{
							buttonsHolderQuestionObj.style.display = 'none';
						}
					}
					catch(e)
					{
						window.location.replace(window.location.href.replace('#', ''));
					}
                    break;
                case 'unsharelist':
                    return xmlhttp.responseText;
                break;
                case 'shareurl':
					if (xmlhttp.responseText == 'str_ErrorSessionExpired')
					{
						var url = '';
						if (window.location.hash == '')
						{
							url = window.location.href.replace('#', '');
						}
						else
						{
							url = window.location.href.replace(window.location.hash, '');
						}

						window.location.replace(url);
					}
					return xmlhttp.responseText;
                break;
				case 'duplicateonlineproject':
					var duplicateResult = parseJson(xmlhttp.responseText);

					var error = duplicateResult.error;
					var resultMessage = duplicateResult.error;
					var nameExistsError = duplicateResult.nameexists;
					var restoreMessage = duplicateResult.restoremessage;
					var projectExists = duplicateResult.projectexists;

					if (isHighLevel)
					{
						resultMessage = duplicateResult.resultmessage;
						nameExistsError = resultMessage;
						projectExists = (duplicateResult.result != 5) ? true : false;
						restoreMessage = duplicateResult.resultmessage;
						error = '';
					}

					if (error == '')
					{
                        if (duplicateResult.maintenancemode)
                        {
                        	closeDialogBoxOnlineAction();
							hideLoadingDialog();

                            var shimObj = document.getElementById('shim');
                            shimObj.style.zIndex = 200;

                            showConfirmationBox("{/literal}{#str_ErrorMaintenanceMode#}{literal}");
                        }
                        else
                        {
							if (restoreMessage != "")
							{
								// the response has come from a restore action - problem during restore
								closeDialogBoxOnlineAction();
								hideLoadingDialog();

								showConfirmationBox(restoreMessage);
							}
							else if (projectExists == false)
							{
								removeDeletedProject(duplicateResult.projectref, true);
							}
							else if (nameExistsError != '')
							{
								hideLoadingDialog();
								var shimObj = document.getElementById('shim');
								shimObj.style.zIndex = 201;
								showConfirmationBox(nameExistsError);
							}
							else
							{
								duplicateOnlineProjectCallBack(duplicateResult);
								closeDialogBoxOnlineAction();
								hideLoadingDialog();
							}
                        }
					}
					else
					{
						closeDialogBoxOnlineAction();
						hideLoadingDialog();

						{/literal}

						{if $section != 'yourorders'} {* {if $section != 'yourorders'} *}

							{literal}

						var shimObj = document.getElementById('shim');
						shimObj.style.zIndex = 200;

							{/literal}

						{/if} {* end {if $section != 'yourorders'} *}

						{literal}

						showConfirmationBox("{/literal}{#str_ErrorConnectFailure#}{literal}");
					}
					break;
                case 'renameonlineproject':
                    var renameResult = parseJson(xmlhttp.responseText);

                    if (renameResult.error == '')
                    {
                        if (renameResult.maintenancemode)
                        {
                            closeDialogBoxOnlineAction();

                            var shimObj = document.getElementById('shim');
                            shimObj.style.zIndex = 200;

                            showConfirmationBox("{/literal}{#str_ErrorMaintenanceMode#}{literal}");
                        }
                        else
                        {
							var projectDetails = renameResult.projectdetails;
							var restoreMessage = projectDetails.restoremessage;

							if (restoreMessage != "")
							{
								// the response has come from a restore action - problem during restore
								closeDialogBoxOnlineAction();
								hideLoadingDialog();

								showConfirmationBox(restoreMessage);
							}
							else if (projectDetails.projectexists == false)
							{
								removeDeletedProject(projectDetails.projectref, true);
							}
							else if (projectDetails.nameexists != '')
							{
								hideLoadingDialog();
								var shimObj = document.getElementById('shim');
								shimObj.style.zIndex = 201;
								showConfirmationBox(projectDetails.nameexists);
							}
							else
							{
								// clear archived status
								var projectStatus = document.getElementById(projectDetails.projectref).getAttribute("data-projectstatus");
								if (projectStatus == 5)
								{
									var statusDescriptionID =  'statusDescription' + projectDetails.projectref;
									document.getElementById(statusDescriptionID).innerHTML = '';
									document.getElementById(projectDetails.projectref).setAttribute("data-projectstatus", 3);
								}

								// set project name
								var renameID = 'name_' + projectDetails.projectref;
								var projectName = projectDetails.projectname;
								document.getElementById(renameID).innerHTML = projectName;
								document.getElementById('projectnamehidden').value = projectName;
								document.getElementById(projectDetails.projectref).setAttribute("data-projectname", projectName);
								closeDialogBoxOnlineAction();
								hideLoadingDialog();
							}
                        }
                    }
					else if (response.error == 'str_ErrorSessionExpired')
					{
						var url = '';
						if (window.location.hash == '')
						{
							url = window.location.href.replace('#', '');
						}
						else
						{
							url = window.location.href.replace(window.location.hash, '');
						}

						window.location.replace(url);
					}
					break;
                case 'checkDeleteSessioncontinueediting':
				case 'checkDeleteSessionediting':
					var response = parseJson(xmlhttp.responseText);

                    var editingType = 0;

                    if (obj == 'checkDeleteSessioncontinueediting');
                    {
                        editingType = 1;
                    }

					if (response.error == '')
                    {
                    	if (response.maintenancemode)
                    	{
                    		hideLoadingDialog();

                    		showConfirmationBox("{/literal}{#str_ErrorMaintenanceMode#}{literal}");

							var shimObj = document.getElementById('shim');

							if (shimObj)
							{
	    						shimObj.style.zIndex = 200;
	    					}
                    	}
                    	else
                    	{
							var targetProjectRef = {};

							for (var key in response.projectitemarray)
							{
								targetProjectRef = response.projectitemarray[key];
							}

							if (targetProjectRef.canmodify == 0)
							{
								hideLoadingDialog();
								changeCanModify(targetProjectRef.projectref);
							}
							else
							{
								if (targetProjectRef.sessionactive == true)
								{
									hideLoadingDialog();
									displayTerminateSessionConfirmation(targetProjectRef.sessiontype, 'editing');
								}
								else
								{
									if (targetProjectRef.projectexists == true)
									{
										openExistingOnlineProject(editingType);
									}
									else
									{
										hideLoadingDialog();
										removeDeletedProject(targetProjectRef.projectref, true);
									}
								}
							}
						}
					}
					else if (response.error == 'str_ErrorSessionExpired')
					{
						var url = '';
						if (window.location.hash == '')
						{
							url = window.location.href.replace('#', '');
						}
						else
						{
							url = window.location.href.replace(window.location.hash, '');
						}

						window.location.replace(url);
					}
					break;
				case 'checkDeleteSessioncomplete':
					var response = parseJson(xmlhttp.responseText);

					if (response.error == '')
                    {
                    	if (response.maintenancemode)
                    	{
                    		hideLoadingDialog();

                    		showConfirmationBox("{/literal}{#str_ErrorMaintenanceMode#}{literal}");

							var shimObj = document.getElementById('shim');

							if (shimObj)
							{
	    						shimObj.style.zIndex = 200;
	    					}
                    	}
                    	else
                    	{
							var targetProjectRef = {};

							for (var key in response.projectitemarray)
							{
								targetProjectRef = response.projectitemarray[key];
							}

							if (targetProjectRef.canmodify == 0)
							{
								hideLoadingDialog();
								changeCanModify(targetProjectRef.projectref);
							}
							else
							{
								if (targetProjectRef.sessionactive == true)
								{
									hideLoadingDialog();
									displayTerminateSessionConfirmation(targetProjectRef.sessiontype, 'complete');
								}
								else
								{
									if (targetProjectRef.projectexists == true)
									{
										completeOrder();
									}
									else
									{
										hideLoadingDialog();
										removeDeletedProject(targetProjectRef.projectref, true);
									}
								}
							}
						}
					}
					else if (response.error == 'str_ErrorSessionExpired')
					{
						var url = '';
						if (window.location.hash == '')
						{
							url = window.location.href.replace('#', '');
						}
						else
						{
							url = window.location.href.replace(window.location.hash, '');
						}

						window.location.replace(url);
					}
					break;
				case 'checkDeleteSessiondelete':
					var response = parseJson(xmlhttp.responseText);

					if (response.error == '')
                    {
                    	if (response.maintenancemode)
                    	{
                    		closeDialogBoxOnlineAction();

                    		hideLoadingDialog();

                    		showConfirmationBox("{/literal}{#str_ErrorMaintenanceMode#}{literal}");

							var shimObj = document.getElementById('shim');

							if (shimObj)
							{
	    						shimObj.style.zIndex = 200;
	    					}
                    	}
                    	else
                    	{
							var targetProjectRef = {};

							for (var key in response.projectitemarray)
							{
								targetProjectRef = response.projectitemarray[key];
							}

							hideLoadingDialog();
							if (targetProjectRef.canmodify == 0)
							{
								changeCanModify(targetProjectRef.projectref);
							}
							else
							{
								if (targetProjectRef.sessionactive == true)
								{
									displayTerminateSessionConfirmation(targetProjectRef.sessiontype, 'delete');
								}
								else
								{
									if (targetProjectRef.projectexists == true)
									{
										removeDeletedProject(targetProjectRef.projectref, false);
									}
									else
									{
										removeDeletedProject(targetProjectRef.projectref, true);
									}
								}
							}
						}
					}
					else if (response.error == 'str_ErrorSessionExpired')
					{
						var url = '';
						if (window.location.hash == '')
						{
							url = window.location.href.replace('#', '');
						}
						else
						{
							url = window.location.href.replace(window.location.hash, '');
						}

						window.location.replace(url);
					}
					break;
				case 'openonlineproject':
					var response = parseJson(xmlhttp.responseText);

					var resultMessage = '';
					var redirectURL = '';
					var restoreMessage = '';

					if (typeof response.projectdetails != "undefined")
					{
						var projectDetails = response.projectdetails;
						restoreMessage = projectDetails.restoremessage;
					}

					if (isHighLevel)
					{
						resultMessage = response.resultmessage;
						redirectURL = response.designurl;
					}
					else
					{
						resultMessage = response.errorparam;
						redirectURL = response.brandurl;
					}

					if (resultMessage == '')
					{
						if (restoreMessage != "")
						{
							// the response has come from a restore action - problem during restore
							closeDialogBoxOnlineAction();
							hideLoadingDialog();

							showConfirmationBox(restoreMessage);
						}
						else
						{
							window.location.href = redirectURL;
						}
					}
					else
					{
						hideLoadingDialog();

						var shimObj = document.getElementById('shim');

						if (shimObj)
						{
    						shimObj.style.zIndex = 200;
    					}

						showConfirmationBox(resultMessage);
					}

					break;
				case 'completeorder':
                    var response = parseJson(xmlhttp.responseText);

                    if (response.errorparam == '')
                    {
                        window.location.href =  response.brandurl;
                    }
                    else
                    {
                        hideLoadingDialog();

                        var shimObj = document.getElementById('shim');

                        if (shimObj)
                        {
                            shimObj.style.zIndex = 200;
                        }

                        showConfirmationBox(response.errorparam);
                    }

                    break;
				case 'redactUserAccount':
					var response = parseJson(xmlhttp.responseText);

					// if no error, sign the user out
					if (response.result == '')
					{
						document.getElementById('headerform').submit();
					}
					else
					{
						// reset the shim
						var shimObj = document.getElementById('shim');
						shimObj.style.zIndex = 200;
						shimObj.style.display = 'none';

						// display the error
                        showConfirmationBox(response.error);
					}

					break;
				case 'payNow':
					var response = parseJson(xmlhttp.responseText);

					// if no error, go to the shopping cart
					if (response.error == '')
					{
						window.location.replace(response.data.shoppingcarturl);
					}
					else
					{
						{/literal} {if $issmallscreen == 'true'}
						hideLoadingDialog();
						{/if}{literal}

                        var shimObj = document.getElementById('shim');

                        if (shimObj)
                        {
                            shimObj.style.zIndex = 200;
                        }

                        showConfirmationBox(response.error);
					}

					break;
	 			case 'orderdelete':
	 				var response = parseJson(xmlhttp.responseText);

	 				if (response.status)
	 				{
	 					{/literal}
	 					{if $issmallscreen == 'true'}
							/* pass localised timestamp */
							var timestamp = getTimestamp();

							// show loading dialog
							showLoadingDialog();

							var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
							if (csrfMeta) {
							var csrfToken = csrfMeta.getAttribute('content');
							}

							// send the ajax
							processAjaxSmallScreen("menuAjaxAction",".?fsaction=Customer.yourOrders&tzoffset=" + timestamp + '&ishighlevel=' + isHighLevel + '&mawebhlbr=' + basketRef, 'POST', 'csrf_token=' + csrfToken);
							closeDialog();
							gActiveAction = pAction;

							window.location.hash = gActiveAction;
	 					{else}
						 /* pass localised timestamp */
						 var timestamp = getTimestamp();

						 document.submitform.tzoffset.value = timestamp;
						 document.submitform.fsaction.value = 'Customer.yourOrders';
						 document.submitform.submit();
						 return false;
						 {/if}
						 {literal}
	 				}
	 				else
	 				{
					 	// Close the open dialog as we will be showing another here.
					 	closeDialog();
					 	{/literal}
						showErrorDialog("{#str_TitleUnableToPerformRequest#}", "<p>{#str_ErrorOrderInProduction#}</p><p>{#str_MessagePleaseTryAgain#}</p>", function(e) {
							closeDialog();
							return false;
						});
					 	{literal}
	 				}
	 				break;
				case 'keeponlineproject':
					// Keep the selected project.
					window.clearTimeout(timeoutID);
					{/literal}
					{if $issmallscreen == 'true'}
						closeLoadingDialog();
					{else}
						hideLoadingDialog();
					{/if}
					{literal}
					var response = parseJson(xmlhttp.responseText);

					if (response.status)
					{
						// Project is kept successfully remove the details saying it's going to be purged.
						var purgeClassName = 'dateofpurge';
						var isSmallScreen = false;
						{/literal}
						{if $issmallscreen == 'true'}
						isSmallScreen = true;
						var purgeMessageContainer = document.getElementById('contentContainer');
						var listContainer = document.getElementById('contentExistingProject');
						var projectContainer = document.getElementById('contentItemBloc' + response.projectref);
						{else}
						var purgeMessageContainer = document.getElementById('page');
						var listContainer = document.getElementById('existingOnlineProjectList');
						var projectContainer = document.getElementById(response.projectref);
						{/if}
						{literal}
						var purgeDetails = projectContainer.querySelector('.' + purgeClassName);
						purgeDetails.parentNode.removeChild(purgeDetails);

						var stillToPurge = getChildElementsByClass(purgeClassName, listContainer);
						if (0 === stillToPurge.length) {
							var purgeMessage = document.getElementById('purgeAllMessage');
							purgeMessage.remove();

							// If we are on small screen remove the flagged for purge message from the main screen.
							if (isSmallScreen) {
								var flaggedMessage = document.getElementById('flaggedForPurge');
								flaggedMessage.remove();
							}
						}
					}
					else
					{
						{/literal}
						showErrorDialog("{#str_TitleUnableToPerformRequest#}", "<p>{#str_MessagePleaseTryAgain#}</p>", function(e) {
							closeDialog();
							return false;
						});
						{literal}
					}
					break;
				case 'purgeflaggedprojects':
					// Purge all flagged projects.
					window.clearTimeout(timeoutID);
					var isSmallScreen = false;
					{/literal}
					{if $issmallscreen == 'true'}
						isSmallScreen = true;
						closeLoadingDialog();
					{else}
						hideLoadingDialog();
					{/if}
 					{literal}
					var response = parseJson(xmlhttp.responseText);

					if (response.success)
					{
						if (!isSmallScreen) {
							// Projects have been flagged for purge now.
							var listContainer = document.getElementById('existingOnlineProjectList');
							var purgeDetails = getChildElementsByClass('dateofpurge', listContainer);
							purgeDetails.forEach(function(item) {
								var parentContainer = item.closest('.contentRow');
								parentContainer.remove();
							});
						} else {
							var listContainer = document.getElementById('contentExistingProject');
							var purgeDetails = getChildElementsByClass('dateofpurge', listContainer);
							purgeDetails.forEach(function(item) {
								var parentContainer = item.closest('.clickable');
								parentContainer.remove();
							});
							var flaggedMessage = document.getElementById('flaggedForPurge');
							flaggedMessage.remove();
						}
						var purgeMessage = document.getElementById('purgeAllMessage');
						purgeMessage.remove();
					}
					else
					{
					{/literal}
 						showErrorDialog("{#str_TitleUnableToPerformRequest#}", "<p>{#str_MessagePleaseTryAgain#}</p>", function(e) {
					 		closeDialog();
					 		return false;
					 	});
					{literal}
					}
					break;
				default:
					// disable the loading screen event if it has not yet fired off. clearTimeout does not cause errors if called on an expired or
					// non existent timeoutID so no checks are needed surrounding it
					window.clearTimeout(timeoutID);
					{/literal}
					{* the small screen and big screen loading dialogs are closed seperately, only call the appropriate one *}
					{if $issmallscreen == 'true'}
							closeLoadingDialog();
					{else}
							hideLoadingDialog();
					{/if}
					{literal}

					var formWrapper = '<form id="mainform" name="mainform" action="#">' + xmlhttp.responseText + '</form>';
					document.getElementById(obj).innerHTML = formWrapper;

					restoreFields();

					{/literal}{if $autosuggestavailable == 1}{literal}
					var as_city = {
						script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=city&country="+ country + "&statecode=" + regioncode + "&addresstype=billing",
						varname:"&input",
						cache:false,
						offsety:0,
						json:true,
						shownoresults:false,
						maxresults:20
					};

					var as_county = {
						script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=county&country="+ country + "&statecode=" + regioncode + "&addresstype=billing",
						varname:"&input",
						cache:false,
						offsety:0,
						json:true,
						shownoresults:false,
						maxresults:20
					};

					var as_state = {
						script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=state&country="+ country + "&statecode=" + regioncode + "&addresstype=billing",
						varname:"&input",
						cache:false,
						offsety:0,
						json:true,
						shownoresults:false,
						maxresults:20
					};
					gAs_jsonCity = new bsn.AutoSuggest('maincity', as_city, "content");
					gAs_jsonCounty = new bsn.AutoSuggest('maincounty', as_county, "content");
					gAs_jsonState = new bsn.AutoSuggest('mainstate', as_state, "content");
                {/literal}{/if}

                {if $issmallscreen == 'true'}

                    {literal}

                setScrollAreaHeight('contentRightScrollForm', 'contentNavigationForm');

                    {/literal}

                {/if} {* end {if $issmallscreen == 'true'} *}


                {* If communication successful update the lastSuccessfulCountry variable with the selected country *}
				{literal}
				if (obj == 'ajaxdiv')
				{
					lastSuccesfulCountry = document.getElementById("countrylist").value;

                    // Add listener to the post code input.
                    var mainPostCodeElement = document.getElementById('mainpostcode');
                    if (mainPostCodeElement)
                    {
                        mainPostCodeElement.addEventListener('blur', function(event) {
                            return CJKHalfWidthFullWidthToASCII(this, true);
                        });
                    }

                    // Add listener to the change country select on the account details form.
                    document.getElementById('countrylist').addEventListener('change', function(event) {
                        return setCountry();
                    });

                    var stateListElement = document.getElementById('statelist');
                    if (stateListElement)
                    {
                        // Add listener to the change state on the account details form.
                        stateListElement.addEventListener('change', function(event) {
                            return changeState();
                        });
                    }
				}
            }
        }
		else if((xmlhttp.readyState == 4) && (xmlhttp.status != 200))
		{
			// prevent the loading screen appearing if it has failed in under 1 seconds
			window.clearTimeout(timeoutID);

			{/literal}
			{if $issmallscreen == 'true'}
				closeLoadingDialog();
			{else}
				hideLoadingDialog();
			{/if}
			alert('{#str_ErrorCommunicationFailedPleaseTryAgain#}');
        	{literal}
			// Reset the selected country back to what it was before the failed selection to avoid address corruption issues
			document.getElementById("countrylist").value = lastSuccesfulCountry;
        }
    };
    xmlhttp.send(params);

    if (!async)
    {
        return xmlhttp.responseText;
    }
}

function getChildElementsByClass(pClassName, pParent)
{
	if (document.querySelectorAll)
	{
		return pParent.querySelectorAll('.' + pClassName);
	}
	else
	{
		var childNodes = pParent.childNodes;
		var childNodesLength = childNodes.length;
		var elements = [];

		for (var i = 0; i < childNodesLength; i++)
		{
			if (childNodes[i].className.indexOf(pClassName) != -1)
			{
				elements[elements.length] = childNodes[i];
			}
		}

		return elements;
	}
}

function fnVisitCheckProjects(event)
{
	if ('1' === event.getAttribute('data-internal')) {
		// Trigger menu action
		menuAction(event.getAttribute('data-link'));
	}
	else
	{
		window.location.href = event.getAttribute('data-link')
	}
}

/* get local time with timezone offset from the browser */
function getTimestamp()
{
	var theDate = new Date();
	var timestamp = Math.round((theDate.getTime()) / 1000);
	timestamp += -((theDate.getTimezoneOffset() * 60));

	return timestamp;
}

/**
 * Copy the value of target element to clipboard
 * @param pElementID
 */
function copyValueToClipboard(pElementID)
{
	var inputElement = document.querySelector('#'+pElementID);

	// iOS devices use setSelectionRange()
	if (navigator.userAgent.match(/ipad|iphone/i))
	{
		inputElement.setSelectionRange(0,999);
	}
	else
	{
		inputElement.select();
	}
	document.execCommand("copy");
	inputElement.selectionStart = inputElement.selectionEnd;
	inputElement.blur();
}

/**
 * Briefly display toolip.
 * @param pTooltipID string -- 'id' of the tooltip to be shown
 * @param pTooltipClass string -- 'class' of toolip class appllied
 * @param pCancel boolean -- used to cancel toolip only
 */
function flashTooltip(pTooltipID, pTooltipClass, pCancel)
{
	var shareLinkTip = document.querySelector('#' + pTooltipID);
	if (shareLinkTip.className.indexOf(pTooltipClass))
	{
		shareLinkTip.classList.remove(pTooltipClass);
	}

    if(! pCancel)
	{
		window.setTimeout(function() {
			shareLinkTip.classList.add(pTooltipClass);
		}, 0);
	}
}

/**
 * Determine whether [clipboardAPI] document.execCommand('copy') is supported
 * Supported Browsers:
 * Firefox 41+, Chrome 42+, Safari 11+, Edge 16+ (76?), IE 10+
 * @param
 */
function getSupportsExecCommand()
{
	// This should be feasible -- but testing on various browsers was inconsistent
	// return document.queryCommandSupported && document.queryCommandSupported("copy");

	var userAgent = navigator.userAgent;

	// Version included for Safari
	var versionMatch = userAgent.match(/Version\/\d*/);

	var chromeMatch = userAgent.match(/Chrome\/\d*/);

	// Chrome on iOS uses CriOS and no Version Number
	var chromeIOSMatch = userAgent.match(/CriOS\/\d*/);

	var safariMatch = userAgent.match(/Safari\/\d*/);

	var firefoxMatch = userAgent.match(/Firefox\/\d*/);

	// Edge browser currently includes 'Chrome'
	var edgeMatch = userAgent.match(/Edge\/\d*/);

	// Unclear how stable this check is for IE
	var ieMatch = userAgent.match(/rv:\d*/);

	if (chromeMatch && !edgeMatch)
	{
		return parseInt(chromeMatch[0].slice(7)) > 42;
	}
	else if (firefoxMatch)
	{
		return parseInt(firefoxMatch[0].slice(8)) > 41;
	}
	else if (edgeMatch)
	{
		return parseInt(edgeMatch[0].slice(5)) > 17;
	}
	else if (chromeIOSMatch)
	{
		return parseInt(chromeIOSMatch[0].slice(6)) > 42;
	}
	else if (safariMatch && versionMatch && !edgeMatch)
	{
		return parseInt(versionMatch[0].slice(8)) > 11;
	}
	else if (ieMatch)
	{
		return parseInt(ieMatch[0].slice(3)) > 10;
	}
	else
	{
		return false;
	}
}



{/literal}

{if $section=='accountdetails'}

    {literal}

function setCountry()
{
    /* save field data so it can be restored afterwards */
    saveFields();
    countryChanged = true;
    processAjax("ajaxdivupdate",".?fsaction=AjaxAPI.callback&cmd=ADDRESSFORM&country=" + country + "&strict=1&addresstype=billing", 'GET', '', true);
}

function verifyAccount()
{
	var passwordObj = document.getElementById('password');
	if (passwordObj)
	{
		var value = passwordObj.value;
		if (value.length)
		{
			var format = ((document.location.protocol != 'https:') ? 1 : 0);
			var passwordValue = ((format == 0) ? value : hex_md5(value))
			var postParams = 'password=' + passwordValue + '&format=' + format;
			processAjax("verifyPassword", ".?fsaction=Customer.verifyPassword", 'POST', postParams, true);

			document.submitform.confirmpassword.value = passwordValue;
			document.submitform.confirmformat.value = format;
		}
		else
		{
			// Wrap this in a short timeout so previous dialog has a chance to close correctly.
			setTimeout(function()
			{
				showVerificationFailedMessage("{/literal}{#str_ErrorNoPassword#}{literal}");
			}, 1);
		}
	}
}


function showVerificationFailedMessage(message, callback)
{
    if(typeof callback === 'undefined')
    {
        callback = function() {
            return false;
        }
    }
	var dialog = new TPXSimpleDialog({
		title: "{/literal}{#str_Error#}{literal}",
		content: "<p>" + message + "</p>",
		buttons: {
			right: {
				text: "{/literal}{#str_ButtonOk#}{literal}",
				action: callback
			}
		}
	}).show();
}

function showVerifyAccountDialog()
{
	var dialog = new TPXSimpleDialog({
		'title' : "{/literal}{#str_TitleConfirmChanges#}{literal}",
		content: function() {
			this.clearContent();
			var container = document.createElement('div');
			container.style.fontFamily = 'ArialMT, Arial';
			container.style.fontWeight = '400';
			container.style.fontStyle = 'normal';
			container.style.backgroundColor = 'rgba(242, 242, 242, 1)';
			container.style.border = 'none';
			container.style.borderRadius = '0px';
			container.style.padding = '20px 10px 20px 10px';
			container.style.position = 'relative';

			var label = document.createElement('label');
			label.innerHTML = "{/literal}{#str_LabelRenterPassword#}{literal}";
			label.style.fontSize = '14px';
			label.style.color = '#666666';
			label.style.display = 'block';
			label.style.margin = '0 15px 10px 15px';
			container.appendChild(label);

			var input = document.createElement('input');
			input.setAttribute('type', 'password');
			input.setAttribute('id', 'password');
			input.style.width = '400px';
			input.style.margin = '0 15px';
			container.appendChild(input);
			
			var button = document.createElement('button');
			button.className = 'password-visibility password-show';
			button.setAttribute('id', 'togglepassword');
			button.addEventListener('click', function()
			{
				togglePasswordVisibility(button, 'password');
			});
			container.appendChild(button);
			
			return container;
		},
		buttons: {
			right: {
				text: "{/literal}{#str_ButtonConfirm#}{literal}",
				action: function() {
					verifyAccount()
				}
			},
			left: {
				text: "{/literal}{#str_ButtonCancel#}{literal}",
				action: function() {
					return false;
				}
			}
		}
	}).show();

    // close the dialog if the end user hits enter
    addEventListener('keydown', function(event) {
        var key = event.key || event.keyCode;
        if(key === 'Enter' || key === 13) {
            verifyAccount();
        }
    });
}

function verifyAddress()
{
    saveFields();

    {/literal}

    {if $autosuggestavailable == 1}

        {literal}

        processAjax("verify",".?fsaction=AjaxAPI.callback&cmd=ADDRESSVERIFICATION&city=" + city + "&county=" + county + "&statecode=" + regioncode +
        "&statevalue=" + state + "&postcode=" + postcode + "&region=" + region + "&country=" + country + "&addresstype=billing", 'POST', '', true);

        {/literal}

    {else}

        {literal}

         updateAccountDetails('match');

        {/literal}

    {/if}

    {literal}

    return false;
}

function changeState()
{
    saveFields();

    {/literal}

    {if $autosuggestavailable == 1}

        {literal}

    var as_city = {
        script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=city&country="+ country + "&statecode=" + regioncode + "&addresstype=billing",
        varname:"&input",
        cache:false,
        offsety:0,
        json:true,
        shownoresults:false,
        maxresults:20
    };
    var as_county = {
        script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=county&country="+ country+ "&statecode=" + regioncode + "&addresstype=billing",
        varname:"&input",
        cache:false,
        offsety:0,
        json:true,
        shownoresults:false,
        maxresults:20
    };
    gAs_jsonCity = new bsn.AutoSuggest('maincity', as_city, "content");
    gAs_jsonCounty = new bsn.AutoSuggest('maincounty', as_county, "content");

        {/literal}

    {/if}

    {literal}
}

    {/literal}

{/if}

/* END ACCOUNT DETAILS */


/* ORDERS */

{if $section == 'yourorders'}

    {literal}

    function productReorder()
    {
        var form = document.submitform;
        var fsactionField = form.fsaction;
        var orderItemIdField = form.orderitemid;
        var actionField = form.action;

        if (fsactionField)
        {
            fsactionField.value = 'Share.reorder';
        }

        if (orderItemIdField)
        {
            orderItemIdField.value = orderItemID;
        }

        if (actionField)
        {
            actionField.value = 'CUSTOMER REORDER';
        }

        form.submit();

{/literal}

{if $issmallscreen == 'true'}

    {literal}

        closeLoadingDialog();

    {/literal}

{/if}

{literal}

    }

	function executePayNow(pSessionRef)
    {
		{/literal}{if $issmallscreen == 'true'}
        showLoadingDialog();
		{/if}{literal}

		processAjax("payNow", ".?fsaction=Order.initPayNowOrder", 'POST', 'origref=' + pSessionRef, true);
	}

    function displayBrowserWarning()
    {
        var w = window;
        d = document;
        e = d.documentElement;
        g = d.getElementsByTagName('body')[0];
        x = w.innerWidth || e.clientWidth || g.clientWidth;
        x2 = x/2;
        document.getElementById('browserConfirmBox').style.left = x2-250;
        document.getElementById('browserConfirmBox').style.display = "block";
    }

    function hideBrowserWarning()
    {
        document.getElementById('browserConfirmBox').style.display = "none";
    }

    function checkBrowserPreview(pActionIn)
    {
        var browCheck = checkBrowserType();

        if(browCheck)
        {
			var form = document.getElementById('showPreviewForm');
			form.action = pActionIn;
            form.submit();
        }
        else
        {
            displayBrowserWarning();
        }
    }

    {/literal}

{/if} {* end {if $section == 'yourorders'} *}

{literal}

/* END ORDERS */

/* GENERIC */

function displayGiftCardAlert(giftCardResult, customMessage)
{
    var message = '';
    switch(giftCardResult)
    {
        case 'str_LabelGiftCardAccepted':
        {
            message = "{/literal}{#str_LabelGiftCardAccepted#}{literal}";
            break;
        }
        case 'str_LabelGiftCardUsed':
        {
            message = "{/literal}{#str_LabelGiftCardUsed#}{literal}";
            break;
        }
        case 'str_LabelInvalidGiftCard':
        {
            message = "{/literal}{#str_LabelInvalidGiftCard#}{literal}";
            break;
        }
        case 'str_LabelInvalidVoucher':
        {
            if(customMessage != '')
            {
                message = customMessage;
            }
            else
            {
                message = "{/literal}{#str_LabelInvalidVoucher#}{literal}";
            }
            break;
        }
    }

    if (message != '')
    {

{/literal}
{if $issmallscreen == 'true'}
{literal}

        if (giftCardResult != 'str_LabelGiftCardAccepted')
        {
            createDialog("{/literal}{#str_TitleWarning#}{literal}", nlToBr(message), function(e) {
                closeDialog(e);
            });
        }

{/literal}
{else}
{literal}

        alert(message);

{/literal}
{/if}
{literal}

    }
}

function onloadWindow()
{
    /* create a cookie to store the local time */
    var theDate = new Date();
	var timestamp = Math.round((theDate.getTime()) / 1000);
    createCookie("mawebtz", timestamp, 2);
}

function highlight(field)
{
    var inputObj = document.getElementById(field);

    if (inputObj)
    {
        // check if input already has this class
        if (inputObj.className.indexOf('errorInput') === -1 || inputObj.className.indexOf('errorInput') === false)
        {
            inputObj.className = inputObj.className + ' errorInput';
        }
        gAlerts = 1;
    }
}

function unhighlight(field)
{
	var inputObj = document.getElementById(field);

	if (inputObj)
	{
		// check if input already has this class
        if (inputObj.className.indexOf('errorInput') !== -1 || inputObj.className.indexOf('errorInput') === true)
        {
            inputObj.className = inputObj.className.replace(' errorInput', '');
        }
		 gAlerts = 0;
	}
}

function updateAccountDetails(pVerify)
{
    /* save address fields to javascript variables */
    saveFields();
    gAlerts = 0;

    var message = "{/literal}{#str_ErrorCompulsoryInformationMissing#}{literal}";
    /* test to see if address verification failed on something */
    resetInvalidAddressFields('mainform');

    if (pVerify != 'match')
    {
    	// The address verification API found invalid data in the address form.
    	// We must highlight which fields have failed.
    	message += highlightVerificationFailures(pVerify);
    }

    if (document.getElementById('firstnamecompulsory'))
    {
        if (firstname.length == 0)
        {
            highlight("maincontactfname");
            message += "\n" + "{/literal}{#str_MessageCompulsoryFirstNameMandatory#}{literal}";
        }
    }
    if (document.getElementById('lastnamecompulsory'))
    {
        if (lastname.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryLastNameMandatory#}{literal}";
            highlight("maincontactlname");
        }
    }
    if (document.getElementById('companycompulsory'))
    {
        if (company.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryCompanyMandatory#}{literal}";
            highlight("maincompanyname");
        }
    }
    if (document.getElementById('add1compulsory'))
    {
        if (add1.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryAdd1Mandatory#}{literal}";
            highlight("mainaddress1");
        }
    }
    if (document.getElementById('add2compulsory'))
    {
        if (add2.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryAdd2Mandatory#}{literal}";
            highlight("mainaddress2");
        }
    }
    if (document.getElementById('add3compulsory'))
    {
        if (add3.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryAdd3Mandatory#}{literal}";
            highlight("mainaddress3");
        }
    }
    if (document.getElementById('add4compulsory'))
    {
        if (add4.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryAdd4Mandatory#}{literal}";
            highlight("mainaddress4");
        }
    }
    if (document.getElementById('add41compulsory'))
    {
        if (add41.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryAdd1Mandatory#}{literal}";
            highlight("mainadd41");
        }
    }
    if (document.getElementById('add42compulsory'))
    {
        if (add42.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryAdd42Mandatory#}{literal}";
            highlight("mainadd42");
        }
    }
    if (document.getElementById('add43compulsory'))
    {
        if (add43.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryAdd3Mandatory#}{literal}";
            highlight("mainadd43");
        }
    }
    if (document.getElementById('citycompulsory') && document.getElementById('citycompulsory').src.indexOf("/images/asterisk.png") != -1)
    {
        if (city.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryCityMandatory#}{literal}";
            highlight("maincity");
        }
    }
    if (regioncode == '--')
    {
        regioncode = "";
    }
    if (document.getElementById('countycompulsory') && document.getElementById('countycompulsory').src.indexOf("/images/asterisk.png") != -1)
    {
        if (county.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryCountyMandatory#}{literal}";
            highlight("maincounty");
            highlight("countylist");
        }
    }
    if (document.getElementById('statecompulsory') && document.getElementById('statecompulsory').src.indexOf("/images/asterisk.png") != -1)
    {
        if (state.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryStateMandatory#}{literal}";
            highlight("mainstate");
            highlight("statelist");
        }
    }
    if (document.getElementById('postcodecompulsory') && document.getElementById('postcodecompulsory').src.indexOf("/images/asterisk.png") != -1)
    {
        if (postcode.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryPostCodeMandatory#}{literal}";
            highlight("mainpostcode");
        }
    }
    if (document.getElementById('telephonenumber_account').value.length == 0)
    {
        message += "\n" + "{/literal}{#str_MessageCompulsoryPhoneMandatory#}{literal}";
        highlight("telephonenumber_account");
    }
    if (! validateEmailAddress(document.getElementById('email_account').value))
    {
        message += "\n" + "{/literal}{#str_MessageCompulsoryEmaiInvalid#}{literal}";
        highlight("email_account");
    }

    var elRegisteredTaxNumberType = document.getElementById("regtaxnumtype");
    var elRegisteredTaxNumber = document.getElementById("regtaxnum");

    if (elRegisteredTaxNumberType && elRegisteredTaxNumber)
    {
        var taxNumberInvalid = false;

        var registeredTaxNumber = elRegisteredTaxNumber.value.replace(/[A-Z\-\.]+/g, "");
        var registeredTaxNumberType = elRegisteredTaxNumberType.options[document.getElementById('regtaxnumtype').selectedIndex].value;

        if (registeredTaxNumberType == TPX_REGISTEREDTAXNUMBERTYPE_NA)
        {
            message += "\n" + "{/literal}{#str_LabelMakeSelection#}{literal}";
            highlight("regtaxnumtype");
        }

        if (registeredTaxNumberType == TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL)
        {
            var CPF = registeredTaxNumber;

            if (CPF.length != 11 || CPF == "00000000000" || CPF == "11111111111" || CPF == "22222222222" || CPF == "33333333333" || CPF == "44444444444" || CPF == "55555555555" || CPF == "66666666666" || CPF == "77777777777" || CPF == "88888888888" || CPF == "99999999999")
            {
                taxNumberInvalid = true;
                highlight("regtaxnum");
            }

            add = 0;

            for (i = 0; i < 9; i++)
            {
                add += parseInt(CPF.charAt(i)) * (10 - i);
            }

            rev = 11 - (add % 11);

            if (rev == 10 || rev == 11)
            {
                rev = 0;
            }

            if (rev != parseInt(CPF.charAt(9)))
            {
                taxNumberInvalid = true;
                highlight("regtaxnum");
            }

            add = 0;

            for (i = 0; i < 10; i++)
            {
                add += parseInt(CPF.charAt(i)) * (11 - i);
            }

            rev = 11 - (add % 11);

            if (rev == 10 || rev == 11)
            {
                rev = 0;
            }

            if (rev != parseInt(CPF.charAt(10)))
            {
                taxNumberInvalid = true;
                highlight("regtaxnum");
            }

            if (taxNumberInvalid)
            {
                message += "\n" + "{/literal}{#str_MessageInvalidPersonalTaxNumber#}{literal}";
            }
        }
        else
        {
            var CNPJ = registeredTaxNumber;
            var i = 0;
            var l = 0;
            var strNum = "";
            var strMul = "6543298765432";
            var character = "";
            var iValido = 1;
            var iSoma = 0;
            var strNum_base = "";
            var iLenNum_base = 0;
            var iLenMul = 0;
            var iSoma = 0;
            var strNum_base = 0;
            var iLenNum_base = 0;
            var taxNumberInvalid = false;

            if (CNPJ == "")
            {
                 taxNumberInvalid = true;
                 highlight("regtaxnum");
            }

            l = CNPJ.length;

            for (i = 0; i < l; i++)
            {
                character = CNPJ.substring(i, i + 1);
                if ((character >= '0') && (character <= '9'))
                {
                   strNum = strNum + character;
                }
            };

            if (strNum.length != 14)
            {
                taxNumberInvalid = true;
                message += "\n" + "{/literal}{#str_MessageCompulsoryCorporateTaxNumberLength#}{literal}";
                message = message.replace("^0", '14');
                highlight("regtaxnum");
            }

            strNum_base = strNum.substring(0, 12);
            iLenNum_base = strNum_base.length - 1;
            iLenMul = strMul.length - 1;

            for (i = 0;i < 12; i++)
            {
                iSoma = iSoma + parseInt(strNum_base.substring((iLenNum_base-i),(iLenNum_base-i) + 1), 10) * parseInt(strMul.substring((iLenMul - i),(iLenMul - i) + 1), 10);
            }

            iSoma = 11 - (iSoma - Math.floor(iSoma/11) * 11);

            if (iSoma == 11 || iSoma == 10)
            {
                iSoma = 0;
            }

            strNum_base = strNum_base + iSoma;
            iSoma = 0;
            iLenNum_base = strNum_base.length - 1;

            for (i = 0; i < 13; i++)
            {
                iSoma = iSoma + parseInt(strNum_base.substring((iLenNum_base-i),(iLenNum_base-i) + 1), 10) * parseInt(strMul.substring((iLenMul-i),(iLenMul-i) + 1), 10);
            }

            iSoma = 11 - (iSoma - Math.floor(iSoma/11) * 11);

            if (iSoma == 11 || iSoma == 10)
            {
                iSoma = 0;
            }

            strNum_base = strNum_base + iSoma;

            if (strNum != strNum_base)
            {
                taxNumberInvalid = true;
                message += "\n" + "{/literal}{#str_MessageInvalidCorporateTaxNumber#}{literal}";
                highlight("regtaxnum");
            }

            if (!taxNumberInvalid)
            {
                registeredTaxNumber = strNum;
            }
        }
    }
    else
    {
        if (countryChanged)
        {
            var registeredTaxNumber = '';
            var registeredTaxNumberType = 0;
        }
        else
        {
            var registeredTaxNumber = "{/literal}{$registeredtaxnumber}{literal}";
            var registeredTaxNumberType = "{/literal}{$registeredtaxnumbertype}{literal}";
        }
    }

    {/literal}

    {if $issmallscreen == 'true'}

        {literal}

    if (gAlerts > 0)
    {
        createDialog("{/literal}{#str_TitleWarning#}{literal}", nlToBr(message), function(e) {
            closeDialog(e);
        });
    }
    else
    {
        if (document.getElementById('email_account').value != "{/literal}{$email}{literal}")
        {
            var messageText = "{/literal}{#str_MessageEmailUpdateRequest#}{literal}";
            var emailUpdatedText = messageText.replace("^0", document.getElementById('email_account').value);
			emailUpdatedText += "<p class='delay-message-text'>{/literal}{#str_ConfirmationResetMessage#}{literal}</p>";

            createDialog("{/literal}{#str_TitleChangeEmailAddress#}{literal}", emailUpdatedText, function(e) {
                    closeDialog(e);
                }, undefined, 'dialogContent-multipleLines');
        }

        // show loading dialog
        showLoadingDialog();

		var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
		if (csrfMeta) {
			var csrfToken = csrfMeta.getAttribute('content');
		}

        var postParams = '';
        postParams += '&contactfname=' + encodeURIComponent(firstname);
        postParams += '&contactlname=' + encodeURIComponent(lastname);

        postParams += '&companyname=' + encodeURIComponent(company);
        postParams += '&address1=' + encodeURIComponent(add1);
        postParams += '&address2=' + encodeURIComponent(add2);
        postParams += '&address3=' + encodeURIComponent(add3);
        postParams += '&address4=' + encodeURIComponent(add4);
        postParams += '&add41=' + encodeURIComponent(add41);
        postParams += '&add42=' + encodeURIComponent(add42);
        postParams += '&add43=' + encodeURIComponent(add43);
        postParams += '&city=' + encodeURIComponent(city);
        postParams += '&county=' + encodeURIComponent(county);
        postParams += '&state=' + encodeURIComponent(state);
        postParams += '&regioncode=' + encodeURIComponent(regioncode);
        postParams += '&region=' + encodeURIComponent(region);
        postParams += '&postcode=' + encodeURIComponent(postcode);
        postParams += '&countrycode=' + encodeURIComponent(country);
		postParams += '&csrf_token=' + csrfToken

        {/literal}

        {if $edit == 0}

            {literal}

        postParams += '&countryname=' + encodeURIComponent(document.getElementById('countrylist').options[document.getElementById('countrylist').selectedIndex].text);

            {/literal}

        {else}

            {literal}

        postParams += '&countryname=' + encodeURIComponent(countryName);
        postParams += '&countrycode=' + "{/literal}{$country}{literal}";
        postParams += '&regioncode=' + "{/literal}{$regioncode}{literal}";

            {/literal}

        {/if}

        {literal}

        postParams += '&telephonenumber=' + encodeURIComponent(document.getElementById('telephonenumber_account').value);
        postParams += '&email=' + encodeURIComponent(document.getElementById('email_account').value);
        postParams += '&originalemail=' + encodeURIComponent("{/literal}{$email}{literal}");
        postParams += '&registeredtaxnumbertype=' + encodeURIComponent(registeredTaxNumberType);
        postParams += '&registeredtaxnumber=' + encodeURIComponent(registeredTaxNumber);

		{/literal}
			{if $customerupdateauthrequired}
				{literal}
					var isHttps = (document.location.protocol === 'https:');
					passwordValue = ((isHttps) ? window['confirmValue'] : hex_md5(confirmValue))
					postParams += '&confirmpassword=' + passwordValue;
					postParams += '&confirmformat=' + ((isHttps) ? 0 : 1);
				{/literal}
			{/if}
		{literal}

        processAjaxSmallScreen("updateAction",".?fsaction=Customer.updateAccountDetails&ishighlevel=" + isHighLevel + '&mawebhlbr=' + basketRef, 'POST', postParams);
    }

    {/literal}

{else}

    {literal}

    if (gAlerts > 0)
    {
        var missingDataNoticeDialog = new TPXSimpleDialog({
            title: "{/literal}{#str_TitleWarning#}{literal}",
            content: "<p>" + message.trim().replace(/(?:\r\n|\r|\n)/g, "</p><p>") + "</p>",
            buttons: {
                right: {
                    text: "{/literal}{#str_ButtonOk#}{literal}",
                    action: function() {
                        return false;
                    }
                }
            }
        });

        missingDataNoticeDialog.show();
    }
    else
    {


        /* copy the values into the form we will submit and then submit it to the server */
        form = document.submitform;
        form.contactfname.value = firstname;
        form.contactlname.value = lastname;

        form.companyname.value = company;
        form.address1.value = add1;
        form.address2.value = add2;
        form.address3.value = add3;
        form.address4.value = add4;
        form.add41.value = add41;
        form.add42.value = add42;
        form.add43.value = add43;
        form.city.value = city;
        form.county.value = county;
        form.state.value = state;
        form.regioncode.value = regioncode;
        form.region.value = region;
        form.postcode.value = postcode;
        form.countrycode.value = country;

            {/literal}

            {if $edit == 0}

                {literal}

        form.countryname.value = document.getElementById('countrylist').options[document.getElementById('countrylist').selectedIndex].text;

                {/literal}

            {else}

                {literal}

        form.countryname.value = countryName;
        form.countrycode.value = "{/literal}{$country}{literal}";
        form.regioncode.value = "{/literal}{$regioncode}{literal}";

                {/literal}

            {/if}

            {literal}

        form.telephonenumber.value = document.getElementById('telephonenumber_account').value;
        form.email.value = document.getElementById('email_account').value;
        form.originalemail.value = "{/literal}{$email}{literal}";
        form.registeredtaxnumbertype.value = registeredTaxNumberType;
        form.registeredtaxnumber.value = registeredTaxNumber;
        form.fsaction.value = "Customer.updateAccountDetails";

        if (document.getElementById('email_account').value != "{/literal}{$email}{literal}")
        {
            var messageText = "{/literal}{#str_MessageEmailUpdateRequest#}{literal}";
            var emailUpdatedText = messageText.replace("^0", document.getElementById('email_account').value);
			emailUpdatedText += "<p class='delay-message-text'>{/literal}{#str_ConfirmationResetMessage#}{literal}</p>";

            var emailNoticeDialog = new TPXSimpleDialog({
                title: "{/literal}{#str_TitleChangeEmailAddress#}{literal}", //'Changes saved', //
                content: "<p>" + emailUpdatedText + "</p>",
                buttons: {
                    right: {
                        text: "{/literal}{#str_ButtonOk#}{literal}",
                        action: function() {
                            form.submit();
                            return false;
                        }
                    }
                },
				contentClasses: [
					'dialogContent-multipleLines'
				]
            });

            emailNoticeDialog.show();
        }
        else
        {
            form.submit();
            return false;
        }
    }
    {/literal}

{/if}

{literal}

}

{/literal}

/* OPEN EXISTING PROJECT */

{if $section == 'existingonlineprojects' || $section == 'yourorders'}

    {literal}

function correctFileName(pFileName)
{
    var theResult = '';

    theResult = stripControlCharacters(pFileName, false);

    // remove invalid characters from filenames
    theResult = theResult.replace(/\>/g, "");
    theResult = theResult.replace(/\</g, "");
    theResult = theResult.replace(/\"/g, "");
    theResult = theResult.replace(/\\/g, "");
    theResult = theResult.replace(/\//g, "");
    theResult = theResult.replace(/\*/g, "");
    theResult = theResult.replace(/\?/g, "");
    theResult = theResult.replace(/\|/g, "");

    return theResult.trim();
}

function stripControlCharacters(pSourceFileName, pMultiLine)
{
    // convert all carriage characters to the one with ASCII code 13
    if (pMultiLine == true)
    {
        var kcr = String.fromCharCode(13);
        pSourceFileName = pSourceFileName.replace(/[\n\r]/g, kcr);
    }

    // strip control character
    var theResult = '';
    var stringLen = pSourceFileName.length;
    for (var i = 0; i < stringLen; i++)
    {
        var theCharCode = pSourceFileName.charCodeAt(i);
        if ((theCharCode > 31) || ((theCharCode == 13) && (pMultiLine == true)))
        {
            theResult = theResult + pSourceFileName.charAt(i);
        }
    }
    theResult = theResult.replace(/\<br\>/g,"");
    theResult = theResult.replace(/\<br \/\>/g,"");
    theResult = theResult.replace(/\<br\/\>/g,"");
    theResult = theResult.replace(/\<body\>/g,"");
    theResult = theResult.replace(/\<\/body\>/g,"");
    theResult = theResult.replace(/\<p\>/g,"");
    theResult = theResult.replace(/\<eol\>/g,"");
    theResult = theResult.replace(/\<eof\>/g,"");
    theResult = theResult.replace(/\\n/g,"");
    theResult = theResult.replace(/\\r/g,"");
    theResult = theResult.replace(/\\r\\n/g,"");

    return theResult;
}


{/literal}

{/if}

 /* END OPEN EXISTING PROJECT */

/* CHANGE PASSWORD */

{if $section == 'changepassword'}

    {literal}

	var passwordStrength = new TPXPasswordStrength({
		minStrength: {/literal}{$passwordstrengthmin}{literal},
		weakString: "{/literal}{#str_ErrorPasswordTooWeak#}{literal}",
		strengthStrings: {
			0: "{/literal}{#str_LabelStartTyping#}{literal}",
			1: "{/literal}{#str_LabelPasswordVeryWeak#}{literal}",
			2: "{/literal}{#str_LabelPasswordWeak#}{literal}",
			3: "{/literal}{#str_LabelPasswordMedium#}{literal}",
			4: "{/literal}{#str_LabelPasswordStrong#}{literal}",
			5: "{/literal}{#str_LabelPasswordVeryStrong#}{literal}"
		}
	});
	

function checkFormChangePassword()
{
    gAlerts = 0;
    var message = "{/literal}{#str_ErrorCompulsoryInformationMissing#}{literal}";
    var oldpassword = document.getElementById('oldpassword');
    var newpassword = document.getElementById('newpassword');
    var passwordStrengthErrorText = passwordStrength.getErrorText();

    oldpassword.className = oldpassword.className.replace("errorInput", "");
    newpassword.className = newpassword.className.replace("errorInput", "");

    if (oldpassword.value.length == 0)
    {
        message += "\n" + "{/literal}{#str_ErrorNoCurrentPassword#}{literal}";
        highlight("oldpassword");
    }

    if (oldpassword.value.length < 5)
    {
        message += "\n" + "{/literal}{#str_MessageCompulsoryPasswordLength#}{literal}";
        message = message.replace("^0", '5');
        highlight("oldpassword");
    }

    if (newpassword.value.length == 0)
    {
        message += "\n" + "{/literal}{#str_ErrorNoNewPassword#}{literal}";
        highlight("newpassword");
    }

    if (newpassword.value.length < 5)
    {
        message += "\n" + "{/literal}{#str_MessageCompulsoryPasswordLength#}{literal}";
        message = message.replace("^0", '5');
        highlight("newpassword");
    }

    if (oldpassword.value == newpassword.value)
    {
        message += "\n" + "{/literal}{#str_ErrorPasswordsSame#}{literal}";
        highlight("newpassword");
    }

    if (passwordStrengthErrorText != '')
    {
        message += passwordStrengthErrorText;
        highlight("newpassword");
    }

    {/literal}

    {if $issmallscreen == 'true'}

        {literal}

    if (gAlerts > 0)
    {
        createDialog("{/literal}{#str_TitleWarning#}{literal}", nlToBr(message), function(e) {
            closeDialog(e);
        });
    }
    else
    {
        var format = ((document.location.protocol != 'https:') ? 1 : 0);

        // show loading dialog
        showLoadingDialog();

		var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
		if (csrfMeta) {
			var csrfToken = csrfMeta.getAttribute('content');
		}

        // send the ajax
        postParams = '&format=' + format;
        postParams += '&data1=' + ((format == 0) ? oldpassword.value : hex_md5(oldpassword.value));
        postParams += '&data2=' + ((format == 0) ? newpassword.value : hex_md5(newpassword.value));
		postParams += '&csrf_token=' + csrfToken

        processAjaxSmallScreen("updateAction",".?fsaction=Customer.updatePassword&ishighlevel=" + isHighLevel + '&mawebhlbr=' + basketRef, 'POST', postParams);
    }

    {/literal}

	{else}

		{literal}

    if (gAlerts > 0)
    {
        alert(message);
        return false;
    }

    var format = ((document.location.protocol != 'https:') ? 1 : 0);

    /* copy the values into the form we will submit and then submit it to the server */
    form = document.submitform;
    form.data1.value = ((format == 0) ? oldpassword.value : hex_md5(oldpassword.value));
    form.data2.value = ((format == 0) ? newpassword.value : hex_md5(newpassword.value));
    form.fsaction.value = "Customer.updatePassword";
    document.getElementById("format").value = format;
    form.submit();

    return false;

        {/literal}

   {/if}

   {literal}

}

    {/literal}

{/if}

/* END CHANGE PASSWORD */

/* PERSONAL DATA DELETION */

{literal}

function dataDeletion(pRedactionMode, pRedactionDays)
{
	// display a warning
	if (pRedactionMode == 2)
	{
		showRedactionConfirmationBox("{/literal}{#str_WarningConfirmRedactionRequest#}{literal}");
	}
	else
	{
		showRedactionConfirmationBox("{/literal}{#str_WarningConfirmRedaction#}{literal}");
	}

	return false;
}

function redactionConfirmation(pRedactionMode, pRedactionDays)
{
	// send redaction request to server
	var requestAction = ".?fsaction=DataRedactionAPI.requestRedaction&mode=" + pRedactionMode + "&days=" + pRedactionDays;

	var requestRegistered = processAjax("redactUserAccount", requestAction, 'GET', '', true);

	// hide the redaction confirmation dialog
	var confirmationBoxObj = document.getElementById('redactConfirmBox');
	if (confirmationBoxObj)
	{
		confirmationBoxObj.style.display = 'none';
	}
}

function closeRedactionConfirmationBox()
{
	// exit the redaction process, do nothing.
	var confirmationBoxObj = document.getElementById('redactConfirmBox');
	var shimObj = document.getElementById('shim');

	// hide the shim
	shimObj.style.zIndex = 200;
	shimObj.style.display = 'none';

	// hide the redaction confirmation dialog
	if (confirmationBoxObj)
	{
		confirmationBoxObj.style.display = 'none';
	}
}

function checkBrowserType()
{
    var browserOk = false;

    // Get the user agent of the browser.
    var browserUA = navigator.userAgent;

    if ((browserUA.match('OPR') == 'OPR') || (browserUA.match('opera') == 'opera'))
    {
        browserOk = true;
    }

    if ((browserUA.match('Firefox') == 'Firefox'))
    {
        browserOk = true;
    }

    if ((browserUA.match('Android') == 'Android') && (browserUA.match('Chrome') == 'Chrome'))
    {
        browserOk = true;
    }

    if ((browserUA.match('Android') == 'Android') && ((browserUA.match('Chrome') == 'Chrome') == false))
    {
		if (browserUA.match('SamsungBrowser') == 'SamsungBrowser')
		{
			var browserVersion = parseFloat((browserUA.split('SamsungBrowser/')[1]).split(' ')[0]);
			var androidVersion = parseFloat((browserUA.split('Android ')[1]).split(';')[0]);

			if (((androidVersion >= 6) && (browserVersion > 5)) || ((androidVersion >= 7) && (browserVersion >= 5)))
			{
				browserOk = true;
			}
			else
			{
				browserOk = false;
			}

		}
		else
		{
			browserOk = false;
		}
    }

    if ((browserUA.match('Chrome') == 'Chrome') && ((browserUA.match('Android') == 'Android') == false))
    {
        browserOk = true;

		if ((browserUA.match('iPhone') == 'iPhone'))
        {
            browserOk = false;
        }
        else if ((browserUA.match('iPod') == 'iPod'))
        {
            browserOk = false;
        }
        else if ((browserUA.match('iPad') == 'iPad'))
        {
            browserOk = false;
        }
    }

    if ((browserUA.match('Safari') == 'Safari') && ((browserUA.match('CriOS') == 'CriOS') == false) && ((browserUA.match('Chrome') == 'Chrome') == false))
    {
        // Only allow browser version {/literal}{$minSafariVersion}{literal} or higher.
		if (parseInt((browserUA.split('Version/')[1]).split(' ')[0]) >= {/literal}{$minSafariVersion}{literal})
        {
            browserOk = true;
        }
        else
        {
            browserOk = false;
        }
    }

	if (browserUA.match('CriOS') == 'CriOS')
    {
		browserOk = true;
    }

    if (((browserUA.match('msie') == 'msie') || (browserUA.match('MSIE') == 'MSIE')) && ((browserUA.match('opera') == 'opera') == false) )
    {
        // Match on Internet Explorer versions before 11.

        // Internet Explorer is not supported.
        browserOk = false;
    }

    if ((browserUA.match('Trident') == 'Trident') && (browserUA.match('rv:') == 'rv:'))
    {
        // Match on Internet Explorer versions 11+.

        // Internet Explorer is not supported.
        browserOk = false;
    }

    return browserOk;
}

{/literal}

/* END PERSONAL DATA DELETION */

{if $issmallscreen == 'true'}

    {* SMALL SPECIFIC FUNCTION *}

	{if ($section == 'existingonlineprojects') || ($section == 'yourorders')}

        {literal}

		function executeButtonAction(obj, windowTarget, pProjectName, pApplicationName, pProjectRef, pWorkType, pProductIndent, pWizMode)
		{
			orderItemID = gActiveProduct;

			if (windowTarget == 1) /* reorder */
			{
				showLoadingDialog();
				productReorder();
			}
			else if (windowTarget == 2) /* unshare */
			{
				unshareObjLink = obj;
				productUnshare();
			}
			else if (windowTarget == 3) /* continue editing */
			{
				gActiveProductOnline = pProjectRef;
				gOriginalProductOnline = pProjectRef;
				onlineProjectsButtonAction('continueediting', pWizMode, pWorkType);
			}
			else if (windowTarget == 4) /* duplicate */
			{
				gActiveProductOnline = pProjectRef;
				gOriginalProductOnline = pProjectRef;
				onlineProjectsButtonAction('duplicate', pWizMode, pWorkType);
			}
			else
			{
				/* share */
				if (addToAnyIntialised == false)
				{
					reinitAddtoany();
				}

				shareObjLink = obj;
				shareName = pProjectName + ' (' + pApplicationName + ')';
				showSharePanel(pProjectName);
			}
		}

		function onlineProjectsButtonAction(pButtonClicked, pWizMode, pWorkType)
		{
			if ((pButtonClicked == 'completeorder') || (pButtonClicked == 'continueediting'))
			{
				var browserCompatability = checkBrowserType();
				var wizOK = checkWizardDevice(pWizMode, pWorkType);

				if (browserCompatability == false)
				{
					createDialog("{/literal}{#str_TitleBrowserCompatibilityIssue#}{literal}", "{/literal}{#str_ErrorBrowserCompatibilityIssue#}{literal}", function(e) {
                        closeDialog(e);
                    });
				}
				else
				{
					if (wizOK == 0)
					{
						createDialog("{/literal}{#str_TitleDeviceCompatibilityIssue#}{literal}", "{/literal}{#str_ErrorDeviceCompatibilityIssue#}{literal}", function(e) {
                            closeDialog(e);
                        });
					}
				}

				{/literal}

				{if ($section == 'yourorders')}

					{literal}

					if ((browserCompatability == false) || (wizOK == 0))
					{
						showOPActionPanel(false);
						closeLoadingDialog();
					}

					{/literal}

				{/if}

				{literal}
			}

			switch(pButtonClicked)
			{
				case 'completeorder':
					if ((browserCompatability) && (wizOK))
					{
						checkDeleteSession(0, 'complete');
					}
				break;
				case 'continueediting':
					if ((browserCompatability) && (wizOK))
					{
						checkDeleteSession(0, 'editing');
					}
				break;
				case 'rename':
					gNameFormAction = 'rename';
					var projectdiv = document.getElementById('onlineProjectDetail' + gActiveProductOnline);
					name = projectdiv.getAttribute('data-projectname').trim();
					showOPActionPanel(true, {
						panel: 'rename',
						title: "{/literal}{#str_LabelRenameProject#}{literal}",
						labelBtn: "{/literal}{#str_ButtonRenameProject#}{literal}",
						label: '{/literal}{#str_LabelEnterNewNameForTheProject#}{literal}',
						value: name,
						fn: validateNameForm
					});
				break;
				case 'duplicate':
					gNameFormAction = 'duplicate';
					gDuplicateProjectWizardMode = pWizMode;
					gDuplicateWorkflowType = pWorkType;
					{/literal}

					{if ($section == 'existingonlineprojects')}

						{literal}

					var projectdiv = document.getElementById('onlineProjectDetail' + gActiveProductOnline);
					var name = projectdiv.getAttribute('data-projectname').trim() + " - {/literal}{#str_ButtonCopy#}{literal}";

						{/literal}

					{else}

						{literal}

					var name = document.getElementById('onlineProjectOrderLabel' + gActiveProductOnline).getAttribute("data-projectname")  + " - {/literal}{#str_ButtonCopy#}{literal}";

						{/literal}

					{/if}

					{literal}

					name = name.trim();
					showOPActionPanel(true, {
						panel: 'duplicate',
						title: "{/literal}{#str_LabelDuplicateProject#}{literal}",
						labelBtn: "{/literal}{#str_ButtonDuplicateProject#}{literal}",
						label: '{/literal}{#str_LabelEnterNewNameForTheProject#}{literal}',
						value: name,
						fn: validateNameForm
					});
					break;
				case 'share':
						processAjaxSmallScreen("getshareonlineprojecturl", ".?fsaction=AjaxAPI.callback&cmd=GETSHAREONLINEPROJECTURL&projectref=" + gActiveProductOnline, 'GET', '', true);
					break;
				case 'delete':
					var confirmDeleteMessage = "{/literal}{#str_MessageDeleteProjectConfirmation#}{literal}";
					var name = document.getElementById('pageLabel' + gActiveProductOnline).innerHTML;
					name = name.trim();
					confirmDeleteMessage = confirmDeleteMessage.replace('^0', "'" + name + "'");
					showConfirmDialog("{/literal}{#str_LabelDeleteProject#}{literal}", confirmDeleteMessage, function(e) { return checkDeleteSession(0, 'delete');});
				break;
			}
		}

		function openExistingOnlineProject(pEditingType)
		{
			{/literal}

			{if ($section == 'existingonlineprojects')}

				{literal}

					var divObject = document.getElementById('onlineProjectDetail' + gActiveProductOnline);

				{/literal}

			{else}

				{literal}

					var divObject = document.getElementById('onlineProjectOrderDetail' + gOriginalProductOnline);

				{/literal}

			{/if}

			{literal}

			var projectRef = gActiveProductOnline;
			var workflowType = divObject.getAttribute("data-workflowtype");
			var productIndent = divObject.getAttribute("data-productident");

			processAjaxSmallScreen("openonlineproject", ".?fsaction=AjaxAPI.callback&cmd=OPENONLINEPROJECT&projectref=" + projectRef + '&workflowtype=' + workflowType + '&productindent=' + productIndent + '&ishighlevel=' + isHighLevel + '&mawebhlbr=' + basketRef + '&editingtype=' + pEditingType + '&l=' + languageCode, 'POST', '');
		}

        // wrapper method for checkDeleteSession
        function fnCheckDeleteSession(pElement)
        {
            if (!pElement)
            {
               return false;
            }

            return checkDeleteSession(pElement.getAttribute('data-force-kill'), pElement.getAttribute('data-action'));
        }

		function checkDeleteSession(pForceKill, pAction)
		{
			closeDialog();
			showLoadingDialog();

			var projectRef = gActiveProductOnline;
			processAjaxSmallScreen("checkDeleteSession" + pAction, ".?fsaction=AjaxAPI.callback&cmd=CHECKDELETESESSION&projectref=" + projectRef + '&forcekill=' + pForceKill + '&action=' + pAction + '&ishighlevel=' + isHighLevel + '&mawebhlbr=' + basketRef, "POST", "");
		}

		function removeDeletedProject(pProjectref, pDisplayMessage)
		{
			closeLoadingDialog();

			if (pDisplayMessage)
			{
				createDialog("{/literal}{#str_TitleWarning#}{literal}", "{/literal}{#str_ErrorProjectHasBeenDeleted#}{literal}", function(e) {
                    closeDialog(e);
                });
			}

			var selectedProject = document.getElementById('contentItemBloc' + gActiveProductOnline);
			selectedProject.parentNode.removeChild(selectedProject);
			showOnlineOptions(false, '');

			// display no online projects message if there are none
			var contentExistingProject = document.getElementById('contentExistingProject');
			if ((contentExistingProject !== null) && (contentExistingProject.children.length === 0))
			{
				var emptyBoxContainer = document.createElement('div');
				emptyBoxContainer.className = 'outerBox outerBoxPadding';
				emptyBoxContainer.innerHTML = "{/literal}{#str_LabelNoOnlineProject#}{literal}";

				var panel = document.getElementById('onlineMainPanel').querySelectorAll('.contentVisible')[0];

				if (panel)
				{
					panel.appendChild(emptyBoxContainer);
				}
			}
		}

		function checkWizardDevice(pWizModeIn, pWorkTypeIn)
		{
			var wizCheck = false;
			var largeScreen = true;

			//Check to see if we are on a large or small screen
			if (Math.min(parseInt(screen.width), parseInt(screen.height)) >= 600)
			{
				largeScreen = true;
			}
			else
			{
				largeScreen = false;
			}

			if (parseInt(pWorkTypeIn) == {/literal}{$kProducTypeSinglePrints}{literal})
			{
				wizCheck = true;
			}
			else if (parseInt(pWorkTypeIn) == {/literal}{$kProducTypePhotoPrints}{literal})
			{
				wizCheck = true;
			}
			else if ({/literal}{$easyEditorModeActive}{literal})
			{
				// All Devices are supported by the easy editor mode.
				wizCheck = true;
			}
			else
			{
				if (((parseInt(pWorkTypeIn) == {/literal}{$kProducTypePhotobook}{literal}) || (parseInt(pWorkTypeIn) == {/literal}{$kProducTypeCalendar}{literal}))
					&& (parseInt(pWizModeIn) <2))
				{
					if (largeScreen)
					{
						wizCheck = true;
					}
					else
					{
						wizCheck = false;
					}
				}

				if (((parseInt(pWorkTypeIn) == {/literal}{$kProducTypePhotobook}{literal}) || (parseInt(pWorkTypeIn) == {/literal}{$kProducTypeCalendar}{literal}))
					&& (pWizModeIn >=2))
				{
					wizCheck = true;
				}
			}

			return wizCheck;
		}

		function validateNameForm()
		{

			var projectName = document.getElementById('projectname').value;
			projectName = correctFileName(projectName);
			if (projectName != '')
			{
				if (gNameFormAction == 'rename')
				{
					showLoadingDialog();
					processAjaxSmallScreen("renameonlineproject", ".?fsaction=AjaxAPI.callback&cmd=RENAMEONLINEPROJECT&projectref=" + gActiveProductOnline + '&projectname=' + encodeURIComponent(projectName) + '&ishighlevel=' + isHighLevel + '&mawebhlbr=' + basketRef, 'POST', '');
				}
				else if (gNameFormAction === 'share')
				{
					showLoadingDialog();
					processAjaxSmallScreen("getshareonlineprojecturl", ".?fsaction=AjaxAPI.callback&cmd=GETSHAREONLINEPROJECTURL&projectref=" + gActiveProductOnline, 'GET', '', true);
				}
				else
				{
					showLoadingDialog();

					{/literal}

					{if ($section == 'existingonlineprojects')}

						{literal}

							var divObject = document.getElementById('onlineProjectDetail' + gActiveProductOnline);

						{/literal}

					{else}

						{literal}

							var divObject = document.getElementById('onlineProjectOrderDetail' + gActiveProductOnline);

						{/literal}

					{/if}

					{literal}

					var workflowType = divObject.getAttribute("data-workflowtype");
					var productIdent = divObject.getAttribute("data-productident");
					var tzoffset = getTimestamp();

					processAjaxSmallScreen("duplicateonlineproject", ".?fsaction=AjaxAPI.callback&cmd=DUPLICATEONLINEPROJECT&projectref=" + gActiveProductOnline + '&projectname=' + encodeURIComponent(projectName) + '&ishighlevel=' + isHighLevel + '&mawebhlbr=' + basketRef + '&productident=' + productIdent + '&workflowtype=' + workflowType + '&tzoffset=' + tzoffset, 'POST', '');
				}
			}
			else
			{
				createDialog("{/literal}{#str_TitleWarning#}{literal}", "{/literal}{#str_ErrorNoProjectName#}{literal}", function(e) {
                    closeDialog(e);
                });
			}
		}


		/**
		 * show the slide-in Online Projects Action Panel (small screen -- loaded in mainajax_small.tpl)
		 * @param pShow boolean
		 * @param pPanelObject (optional) [properties: 'panel', 'title','labelBtn','label','fn','value']
		 */
		function showOPActionPanel(pShow, pPanelObject)
		{
			if (!pPanelObject)
			{
				pPanelObject = {};
				var cancel = true;
				flashTooltip('sharelink-tip', 'tip-popout-visible', cancel);
			}

		gActiveResizeFormID = new Date().getTime();
			if (pShow)
			{
				document.getElementById('onlineNameFormPanel').style.display = 'block';
				document.getElementById('opActionPanelTitle').innerHTML = pPanelObject.title;
				document.getElementById('opActionPanelBtn').innerHTML = pPanelObject.labelBtn;
				var inputElement = document.getElementById('projectname');

				// Older browsers do not support execCommand('copy') -> hide the button
				if (pPanelObject.panel === 'share'  && !getSupportsExecCommand())
				{
					document.querySelector('#opActionPanelBtnAction').style.display = 'none';
				}
				else if (pPanelObject.panel === 'share'  && getSupportsExecCommand())
				{
					// Implement 'readonly' to prevent keyboard popup as small screen default = 'readonly = false'
					inputElement.setAttribute('readonly','readonly');
				}

				document.getElementById('opActionPanelLabel').innerHTML = pPanelObject.label;
				document.getElementById('opActionPanelBtnAction').addEventListener('click',pPanelObject.fn);
				inputElement.value = pPanelObject.hasOwnProperty('value') ? pPanelObject.value : '';
				setScrollAreaHeight('contentRightScrollNameForm', 'contentNavigationNameForm');

				{/literal}

				{if ($section == 'existingonlineprojects')}

					{literal}

						document.getElementById('onlineDetailPanel').style.marginLeft = '-' + gScreenWidth + 'px';

					{/literal}

				{else}

					{literal}

						document.getElementById('orderDetailPanel').style.marginLeft = '-' + gScreenWidth + 'px';

					{/literal}

				{/if}

				{literal}

				gNameForm = true;
			}
			else
			{
				{/literal}

				{if ($section == 'existingonlineprojects')}

					{literal}

						document.getElementById('onlineDetailPanel').style.marginLeft = 0;

					{/literal}

				{else}

					{literal}

						document.getElementById('orderDetailPanel').style.marginLeft = 0;

					{/literal}

				{/if}

				{literal}

				// delay to hide the panel to make sure the other panel is displayed (css animation)
				setTimeout(function()
				{
					document.getElementById('onlineNameFormPanel').style.display = 'none';
					gNameForm = false;
				}, 300);
			}
		}


		function duplicateOnlineProjectCallBack(pDuplicateResult)
		{
			{/literal}

			{if ($section == 'existingonlineprojects')}

				{literal}

					closeLoadingDialog();

                    var createNodeFromString = function(str) {
                        var div = document.createElement('div');
                        div.innerHTML = str.trim();
                        return div.firstChild;
                    }

					var contentExistingProject = document.getElementById('contentExistingProject');
                    contentExistingProject.prepend(createNodeFromString(pDuplicateResult.html));

					var contentRightScrollDetailVisible = document.getElementById('contentRightScrollDetailVisible');
                    contentRightScrollDetailVisible.prepend(createNodeFromString(pDuplicateResult.htmloption));

					showOPActionPanel(false);

					showOnlineOptions(false, '');

					document.getElementById('contentRightScrollForm').scrollTop = 0;

				{/literal}

			{else}

				{literal}

					gActiveProductOnline = pDuplicateResult.projectref;
					onlineProjectsButtonAction('continueediting', gDuplicateProjectWizardMode, gDuplicateWorkflowType);

				{/literal}

			{/if}

			{literal}

		}

		function changeCanModify(pProjectref)
		{
			closeLoadingDialog();

			createDialog("{/literal}{#str_TitleWarning#}{literal}", "{/literal}{#str_ErrorOrderInProduction#}{literal}", function(e) {
                closeDialog(e);
            });

			var statusDescription = document.getElementById('statusDescription' + pProjectref);
			if (statusDescription)
			{
				statusDescription.innerHTML = "{/literal}{#str_LabelStatusInProduction#}{literal}";
				document.getElementById('detailStatusDescription' + pProjectref).innerHTML = "{/literal}{#str_LabelStatusInProduction#}{literal}";
			}
			else
			{
				// create status div
				var contentStatus = '<br /> <span class="orderLabelMedium">' + "{/literal}{#str_LabelStatus#}{literal}:" + '</span>';
				contentStatus += '<span class="statusInProduction">' + "{/literal}{#str_LabelStatusInProduction#}{literal}" + '</span>';

				var orderDetail = document.getElementById('orderDetail');
				var content = orderDetail.innerHTML;
				orderDetail.innerHTML = content + contentStatus;

				var detailOrderDetail = document.getElementById('detailOrderDetail');
				content = detailOrderDetail.innerHTML;
				detailOrderDetail.innerHTML = content + contentStatus;
			}

			var completeOrderButton = document.getElementById("completeOrderButton");
			if (completeOrderButton)
			{
				completeOrderButton.parentNode.removeChild(completeOrderButton);
			}

			var continueOrderButton = document.getElementById("continueOrderButton");
			if (continueOrderButton)
			{
				continueOrderButton.parentNode.removeChild(continueOrderButton);
			}

			var deleteOrderButton = document.getElementById("deleteOrderButton");
			if (deleteOrderButton)
			{
				deleteOrderButton.parentNode.removeChild(deleteOrderButton);
			}
		}

		function displayTerminateSessionConfirmation(pSessionType, pAction)
		{
			closeLoadingDialog();

			var message = '';
			var forceQuit = 0;

			switch (pSessionType)
			{
				case 'shoppingcart':
				{
					message = "{/literal}{#str_WarningProjectOpenInShoppingCart#}{literal}";
					break;
				}
				case 'taopixonline':
				{
					if (pAction == 'delete')
					{
						message = "{/literal}{#str_WarningDeleteTerminateOtherSession#}{literal}";
					}
					else
					{
						message = "{/literal}{#str_WarningTerminateOtherSession#}{literal}";
					}
					break;
				}
			}
			
			showConfirmDialog("{/literal}{#str_TitlePleaseConfirm#}{literal}", message, function(e) { 
				return checkDeleteSession(1, pAction); 
			});
		}


		{/literal}

	{/if}

{else} {* else {if $issmallscreen == 'true'} *}

    {if ($section == 'existingonlineprojects') || ($section == 'yourorders')}

        {literal}

    window.addEventListener('DOMContentLoaded', function(e)
		{
			/* 
			If the project thumbnail either doesn't exist or fails to load then
			attempt to replace it with the product preview image. 
		 	If that doesn't exist then hide the image tag. 
			*/
			var productPreviewElements = document.getElementsByClassName('product-preview-image');
			var productPreviewElementsLength = productPreviewElements.length;

      var fallbackHandler = function()
      {
        if ((this.dataset.asset !== '') && (typeof this.dataset.asset !== 'undefined'))
        {
          // Load the product thumbnail image.
          this.src = this.dataset.asset;
          this.dataset.asset = '';
        }
        else
        {
          // Revert to the default blank image if there is no project or product thumbnail available.
          this.src = '{/literal}{$brandroot}{literal}/images/no_image-2x.jpg';
          this.removeEventListener('error', fallbackHandler);
        }
      };

			for (var i = 0; i < productPreviewElementsLength; i++)
			{
        productPreviewElements[i].addEventListener('error', fallbackHandler);
			}
		});

		function onlineProjectsButtonAction(pButtonClicked)
		{
			if (pButtonClicked === 'share')
			{
				var projectRef = document.querySelector('#projectrefhidden').value;

				// Use TPXSimpleDialog WORKFLOW so therefore return and leave this workflow
				return processAjax("getshareonlineprojecturl", ".?fsaction=AjaxAPI.callback&cmd=GETSHAREONLINEPROJECTURL&projectref=" + projectRef, 'GET', '', false);
			}

			var browserNotCompatible = false;

			var shimObj = document.getElementById('shim');
			/* get product details */
			if (shimObj)
			{
				shimObj.style.height = document.body.offsetHeight + 'px';
				document.body.className +=' hideSelects';
				shimObj.style.display = 'block';
			}

			if ((pButtonClicked == 'continueediting') || (pButtonClicked == 'completeorder'))
			{
				var browserCompatability = checkBrowserType();
				if (browserCompatability == false)
				{
					document.getElementById('renameProjectTitle').innerHTML = "{/literal}{#str_LabelBrowserCompatibilityIssue#}{literal}";
					document.getElementById('projectname_container').innerHTML = '<div class="confirmationText">' + "{/literal}{#str_ErrorBrowserCompatibilityIssue#}{literal}" + '</div>';
					document.getElementById('projectcancelbutton').innerHTML = '<div class="btn-red-cross-left" ></div><div class="btn-red-right"></div>';

					var dilaogBox = document.getElementById('dialogBoxOnlineAction');
					if (dilaogBox && shimObj)
					{
						dilaogBox.style.display = 'block';
						dilaogBox.style.left = Math.round(shimObj.offsetWidth / 2 - dilaogBox.offsetWidth/2) + 'px';
						var windowHeight = document.documentElement.clientHeight;
						dilaogBox.style.top = Math.round((windowHeight - dilaogBox.offsetHeight) / 2) + 'px';
					}
					browserNotCompatible = true;
				}
			}

			if (browserNotCompatible == false)
			{
				openDialogBoxOnlineAction(pButtonClicked);
			}

			return false;
		}


		function openDialogBoxOnlineAction(pButtonClicked)
		{
			if (pButtonClicked == 'continueediting')
			{
				checkDeleteSession(0, 'continueediting');
			}
			else if (pButtonClicked == 'completeorder')
			{
				checkDeleteSession(0, 'complete');
			}
			else
			{
				var title = "{/literal}{#str_LabelRenameProject#}{literal}";

				//change the title of the popup
				if (pButtonClicked == 'delete')
				{
					var title = "{/literal}{#str_TitlePleaseConfirm#}{literal}";
				}
				else if(pButtonClicked == 'duplicateproject')
				{
					var title = "{/literal}{#str_LabelProjectName#}{literal}";
				}

				document.getElementById('renameProjectTitle').innerHTML = title;
				var dilaogBox = document.getElementById('dialogBoxOnlineAction');
				var shimObj = document.getElementById('shim');
				if ((pButtonClicked == 'rename') || (pButtonClicked == 'duplicateproject'))
				{
					document.getElementById('projectname_container').innerHTML = '<label for="projectname">' + "{/literal}{#str_LabelProjectName#}{literal}:" + '</label><input type="text" name="projectname" id="projectname" maxlength="75"/>';
					var projectNameElement = document.getElementById('projectname');

					projectNameElement.value = document.getElementById('projectnamehidden').value + ' - {/literal}{#str_ButtonCopy#}{literal}';
					document.getElementById('projectacceptbutton').innerHTML = '<div class="btn-green-left" ></div><div class="btn-accept-right"></div>';
					document.getElementById('projectcancelbutton').innerHTML = '<div class="btn-red-cross-left" ></div><div class="btn-red-right"></div>';

					if (pButtonClicked == 'duplicateproject')
					{
						projectNameElement.addEventListener("keydown", function(e)
						{
							if (e.keyCode == 13)
							{
								if (! processingAjax)
								{
									processingAjax = true;
									duplicateProject();
								}
							}
						}, false);

						document.getElementById('projectacceptbutton').onclick = function()
						{
							if (! processingAjax)
							{
								processingAjax = true;
								duplicateProject();
							}
						}
					}
					else
					{
						projectNameElement.addEventListener("keydown", function(e)
						{
							if (e.keyCode == 13)
							{
								if (! processingAjax)
								{
									processingAjax = true;
									renameExistingOnlineProject();
								}
							}
						}, false);

						document.getElementById('projectacceptbutton').onclick = function()
						{
							if (! processingAjax)
							{
								processingAjax = true;
								renameExistingOnlineProject();
							}
						}

					}
				}
				else if (pButtonClicked == 'delete')
				{
					displayDeleteProjectPrompt(0);
				}

				if (dilaogBox && shimObj)
				{
					dilaogBox.style.display = 'block';
					dilaogBox.style.left = Math.round(shimObj.offsetWidth / 2 - dilaogBox.offsetWidth/2) + 'px';
					var windowHeight = document.documentElement.clientHeight;
					dilaogBox.style.top = Math.round((windowHeight - dilaogBox.offsetHeight) / 2) + 'px';
				}
			}
		}


		function closeDialogBoxOnlineAction()
		{
			var shimObj = document.getElementById('shim');
			var dialogBoxObj = document.getElementById('dialogBoxOnlineAction');

			if (shimObj)
			{
				shimObj.style.display = 'none';
				shimObj.style.zIndex = 200;
			}

			if (dialogBoxObj)
			{
				dialogBoxObj.style.display = 'none';
			}

			if (processingAjax === true)
			{
				processingAjax = false;
			}
		}


		function checkDeleteSession(pForceKill, pAction)
		{
			if (pAction == 'delete')
			{
				showLoadingDialog('{/literal}{#str_MessageDeleting#}{literal}');
			}
			else
			{
				showLoadingDialog('{/literal}{#str_MessageLoading#}{literal}');
			}
			var projectRef = document.getElementById('projectrefhidden').value;

			processAjax("checkDeleteSession" + pAction, ".?fsaction=AjaxAPI.callback&cmd=CHECKDELETESESSION&projectref=" + projectRef + '&forcekill=' + pForceKill + '&action=' + pAction, 'POST', '', true);
		}


		function openExistingOnlineProject(pEditingType)
		{
			var projectWorkflowType = document.getElementById('projectworkflowtype').value;
			var projectRef = document.getElementById('projectrefhidden').value;
			var productIndent = document.getElementById('productindent').value;

			processAjax("openonlineproject", ".?fsaction=AjaxAPI.callback&cmd=OPENONLINEPROJECT&projectref=" + projectRef + '&workflowtype=' + projectWorkflowType + '&productindent=' + productIndent + '&ishighlevel=' + isHighLevel + '&mawebhlbr=' + basketRef + '&editingtype=' + pEditingType + '&l=' + languageCode, 'POST', '', true);
		}

		function duplicateProject()
		{
			var projectName = document.getElementById('projectname').value;
			var projectRef = document.getElementById('projectrefhidden').value;
			var tzoffset = document.getElementById('tzoffset').value;
			var productIdent = document.getElementById('productindent').value;

			projectName = correctFileName(projectName);
			document.getElementById('projectname').value = projectName;
			if (projectName != '')
			{
				showLoadingDialog("{/literal}{#str_TitleDuplicatingProject#}{literal}");
				processAjax("duplicateonlineproject", ".?fsaction=AjaxAPI.callback&cmd=DUPLICATEONLINEPROJECT&projectref=" + projectRef + '&projectname=' + encodeURIComponent(projectName) + '&ishighlevel=' + isHighLevel + '&mawebhlbr=' + basketRef + '&productident=' + productIdent + '&tzoffset=' + tzoffset, 'POST', '', true);
			}
			else
			{
				var shimObj = document.getElementById('shim');
				shimObj.style.zIndex = 201;
				showConfirmationBox("{/literal}{#str_ErrorNoProjectName#}{literal}");
			}
		}

		function duplicateOnlineProjectCallBack(pDuplicateResult)
		{
			{/literal}

			{if $section == 'existingonlineprojects'}

				{literal}

				var projectListContainer = document.getElementById('existingOnlineProjectList');
				projectListContainer.innerHTML = pDuplicateResult.html + projectListContainer.innerHTML;

                // Add listeners to account pages menu buttons.
                var classname = document.getElementsByClassName('contentRow');
                for (var i = 0; i < classname.length; i++)
                {
                    classname[i].addEventListener('click', function() {
                        return selectProject(this);
                    });
                }

				// Reattach the keep project link events
				var keepProjectLinks = getChildElementsByClass('keepProjectLink', projectListContainer);
				if (keepProjectLinks.length > 0) {
					keepProjectLinks.forEach(function(item) {
						item.addEventListener('click', function(event) {
							keepOnlineProject(event.target.getAttribute('data-projectref'));
						});
					});
				}
				{/literal}

			{else}

				{literal}

					showLoadingDialog('{/literal}{#str_MessageLoading#}{literal}');
					document.getElementById('projectrefhidden').value = pDuplicateResult.projectref;
					document.getElementById('projectworkflowtype').value = pDuplicateResult.workflowtype;
					document.getElementById('productindent').value = pDuplicateResult.productident;
					openExistingOnlineProject(1);

				{/literal}

			{/if}

			{literal}
		}


		function changeCanModify(pProjectref)
		{
			closeDialogBoxOnlineAction();

			var shimObj = document.getElementById('shim');
			shimObj.style.zIndex = 200;
			showConfirmationBox("{/literal}{#str_ErrorOrderInProduction#}{literal}");

			var selectedProject = document.getElementById(pProjectref);
			setActiveButtonsFromStatus(0, 0, 0);

			document.getElementById('statusDescription' + pProjectref).innerHTML = "{/literal}{#str_LabelStatusInProduction#}{literal}";
		}


		function displayTerminateSessionConfirmation(pSessionType, pAction)
		{
			var message = "";
			var title = "{/literal}{#str_TitlePleaseConfirm#}{literal}";

			document.getElementById('renameProjectTitle').innerHTML = title;

			switch (pSessionType)
			{
				case 'shoppingcart':
				{
					message = "{/literal}{#str_WarningProjectOpenInShoppingCart#}{literal}";
					break;
				}
				case 'taopixonline':
				{
					if (pAction == 'delete')
					{
						message = "{/literal}{#str_WarningDeleteTerminateOtherSession#}{literal}";
					}
					else
					{
						message = "{/literal}{#str_WarningTerminateOtherSession#}{literal}";
					}
					break;
				}
			}

			document.getElementById('projectname_container').innerHTML = '<div class="confirmationText">' + message + '</div>';
			document.getElementById('projectacceptbutton').onclick = function()
			{
				checkDeleteSession(1, pAction);
			};

			document.getElementById('projectacceptbutton').innerHTML = '<div class="btn-green-left" ></div><div class="btn-green-middle" >' + "{/literal}{#str_ButtonContinue#}{literal}" + '</div><div class="btn-accept-right"></div>';
			document.getElementById('projectcancelbutton').innerHTML = '<div class="btn-red-cross-left" ></div><div class="btn-red-middle">' + "{/literal}{#str_ButtonDoNotContinue#}{literal}" + '</div><div class="btn-red-right"></div>';

			var dilaogBox = document.getElementById('dialogBoxOnlineAction');
			var shimObj = document.getElementById('shim');
			if (dilaogBox && shimObj)
			{
				dilaogBox.style.display = 'block';
				dilaogBox.style.left = Math.round(shimObj.offsetWidth / 2 - dilaogBox.offsetWidth / 2) + 'px';
				var windowHeight = document.documentElement.clientHeight;
				dilaogBox.style.top = Math.round((windowHeight - dilaogBox.offsetHeight) / 2) + 'px';
			}
		}

		{/literal}

	{/if}

{/if}


/* END GENERIC */

{if $issmallscreen == 'true'}

    {* SMALL SPECIFIC FUNCTION *}

    {if ($section == 'menu') || ($section == 'accountdetails')}

        {literal}

    function initilializeApp(pInit)
    {
        // check if the width of the screen need to be calculated
        if (gScreenWidth == 0)
        {
            // main Bloc Size
            var width = document.body.offsetWidth;

            // store the screen size
            gScreenWidth = width;

            gScreenHeight = document.body.offsetHeight;
        }
        else
        {
            var width = gScreenWidth;
        }

        var contentScrollCart = document.getElementById('contentScrollCart');
        var styleContentScrollCart = contentScrollCart.currentStyle || window.getComputedStyle(contentScrollCart);
        gContentScrollCart = parseIntStyle(styleContentScrollCart.paddingLeft) + parseIntStyle(styleContentScrollCart.paddingRight);

        var outerBox = document.getElementById('outerBox');
        var styleOuterBox = outerBox.currentStyle || window.getComputedStyle(outerBox);
        gOuterBox = parseIntStyle(styleOuterBox.paddingLeft) + parseIntStyle(styleOuterBox.paddingRight);
        gOuterBox += parseIntStyle(styleOuterBox.borderLeftWidth) + parseIntStyle(styleOuterBox.borderRightWidth);

        var outerBoxPadding = document.getElementById('outerBoxPadding');
        var styleOuterBoxPadding = outerBoxPadding.currentStyle || window.getComputedStyle(outerBoxPadding);
        gOuterBoxPadding = parseIntStyle(styleOuterBoxPadding.paddingLeft) + parseIntStyle(styleOuterBoxPadding.paddingRight);
        gOuterBoxPadding += parseIntStyle(styleOuterBoxPadding.marginLeft) + parseIntStyle(styleOuterBoxPadding.marginRight);

        if (gScreenWidth > gMaxWidth)
        {
            gOuterBoxContentBloc = gMaxWidth - gOuterBox - gOuterBoxPadding;
        }
        else
        {
            gOuterBoxContentBloc = gScreenWidth - gContentScrollCart - gOuterBox - gOuterBoxPadding;
        }

        gSiteContainer = gScreenWidth - gContentScrollCart;

        var contentSite = document.getElementById('contentBlocSite');
        contentSite.style.width = (width * 5) + 'px'; // force the content of the site to contains all panels

        // set the size of containers
        document.getElementById('contentPanelMain').style.width = width + 'px';
        document.getElementById('contentPanelRedeemGiftCard').style.width = width + 'px';
        document.getElementById('contentPanelAjax').style.width = width + 'px';
        document.getElementById('contentPanelShare').style.width = width + 'px';
        document.getElementById('contentPanelShareDetail').style.width = width + 'px';

        // redeem panel design

        document.getElementById('contentPanelMain').style.display = 'block';

        {/literal}

        {if $section=='menu'}

            {literal}

            // close loading dialog
            closeLoadingDialog();

            {/literal}

        {else}

            {literal}

            if (pInit == true)
            {

                gActiveAction = 'accountDetails';
                processAjaxSmallScreen("menuAjaxAction",".?fsaction=Customer.accountDetails&ishighlevel=" + isHighLevel + '&mawebhlbr=' + basketRef, 'POST', '');
            }
            else
            {
                // close loading dialog
                closeLoadingDialog();
            }

            {/literal}

        {/if}

        {literal}

        setScrollAreaHeight('contentLeftScrollMenu', '');
    }

    function redeemGiftCard()
    {
        var giftcardtext =  document.getElementById("giftcardid").value;
        if((giftcardtext != '') && (giftcardtext != "{/literal}{#str_LabelEnterCode#}{literal}"))
        {
			var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
			if (csrfMeta) {
				var csrfToken = csrfMeta.getAttribute('content');
			}

            postParams = '&giftcardcode=' + giftcardtext;
            postParams += '&giftcardaction=' +  "{/literal}{$section}{literal}";
            postParams += '&showgiftcardmessage=' +  1;
			postParams += '&csrf_token=' + csrfToken

            // show loading dialog
            showLoadingDialog();

            // send the ajax
            processAjaxSmallScreen("redeemgiftcard",".?fsaction=Customer.updateGiftCard&ishighlevel=" + isHighLevel + '&mawebhlbr=' + basketRef, 'POST', postParams);
        }
    }

    function menuAction(pActionUrl, pActionIn)
    {
		/* pass localised timestamp */
		var timestamp = getTimestamp();

        pAction = pActionIn // Added for use with eventlistener in mobilefunction.tpl

        // show loading dialog
        showLoadingDialog();

		var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
		if (csrfMeta) {
		    var csrfToken = csrfMeta.getAttribute('content');
		}

        // send the ajax
        processAjaxSmallScreen("menuAjaxAction",".?fsaction=" + pActionUrl + "&tzoffset=" + timestamp + '&ishighlevel=' + isHighLevel + '&mawebhlbr=' + basketRef, 'POST', 'csrf_token=' + csrfToken);

        gActiveAction = pAction;

        window.location.hash = gActiveAction;
    }

        {/literal}

    {/if} {* end {if ($section=='menu') || ($section == 'accountdetails')} *}

    /* END MENU */

    /* CHANGE PREFERENCES */

    {if $section=='changepreferences'}

        {literal}

    function initializePanel()
    {
        document.getElementById('contentPanelAjax').style.width = gScreenWidth + 'px';

		var width = gOuterBoxContentBloc;
        // set the width of the payment list text
        var classLength = document.getElementsByClassName('listLabel').length;
        for (var i = 0; i < classLength; i++)
        {
            var elm = document.getElementsByClassName('listLabel')[i];

            if (i == 0)
            {
                var styleLabel = elm.currentStyle || window.getComputedStyle(elm);
                width = width - parseIntStyle(styleLabel.paddingLeft) - parseIntStyle(styleLabel.paddingRight);
            }

            elm.style.width = width + 'px';
        }
    }

    function checkFormChangePreferences()
    {
		// show loading dialog
		showLoadingDialog();

		var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
		if (csrfMeta) {
			var csrfToken = csrfMeta.getAttribute('content');
		}

		// send the ajax
		postParams = '&sendmarketinginfo=' + (document.getElementById('subscribed').checked ? '1' : '0');
		postParams += '&csrf_token=' + csrfToken

		processAjaxSmallScreen("updateAction",".?fsaction=Customer.updatePreferences&ishighlevel=" + isHighLevel + '&mawebhlbr=' + basketRef, 'POST', postParams);

        return false;
    }
        {/literal}

    {/if}

    /* CHANGE PASSWORD */

    {if $section == 'changepassword'}

        {literal}

    function initializePanel()
    {
        document.getElementById('contentPanelAjax').style.width = gScreenWidth + 'px';
    }

        {/literal}

    {/if}

    /* END CHANGE PASSWORD */

    /* ACCOUNT DETAILS */

    {if $section == 'accountdetails'}

        {literal}

    function initializePanel()
    {
        document.getElementById('contentPanelAjax').style.width = gScreenWidth + 'px';

        processAjaxSmallScreen("addressForm",".?fsaction=AjaxAPI.callback&cmd=ADDRESSFORM&country=" + country + "&addresstype=billing&hideconfigfields=" + hideConfigFields +"&strict=1&edit={/literal}{$edit}{literal}&ishighlevel=" + isHighLevel + "&mawebhlbr=" + basketRef , 'GET', '');
    }

    function restoreFields()
    {
        /* restore field data after AJAX call */
        var elFirstname = document.getElementById('maincontactfname');
        var elLastname = document.getElementById('maincontactlname');
        var elCompany = document.getElementById('maincompanyname');
        var elAdd1 = document.getElementById('mainaddress1');
        var elAdd2 = document.getElementById('mainaddress2');
        var elAdd3 = document.getElementById('mainaddress3');
        var elAdd4 = document.getElementById('mainaddress4');
        var elAdd41 = document.getElementById('mainadd41');
        var elAdd42 = document.getElementById('mainadd42');
        var elAdd43 = document.getElementById('mainadd43');
        var elCity = document.getElementById('maincity');
        var elCounty = document.getElementById('maincounty');
        var elState = document.getElementById('mainstate');
        var elPostcode = document.getElementById('mainpostcode');
        var elCountry = document.getElementById('countrylist');
        var elCountylist = document.getElementById('countylist');
        var elStatelist = document.getElementById('statelist');
        var elRegisteredTaxNumberType = document.getElementById('regtaxnumtype');
        var elRegisteredTaxNumber = document.getElementById('regtaxnum');

        if (elFirstname)
        {
            elFirstname.value = firstname;
        }
        if (elLastname)
        {
            elLastname.value = lastname;
        }
        if (elCompany)
        {
            elCompany.value = company;
        }
        if (elAdd1)
        {
            elAdd1.value = add1;
        }
        if (elAdd2)
        {
            elAdd2.value = add2;
        }
        if (elAdd3)
        {
            elAdd3.value = add3;
        }
        if (elAdd4)
        {
            elAdd4.value = add4;
        }
        if (elAdd41)
        {
            elAdd41.value = add41;
        }
        if (elAdd42)
        {
            elAdd42.value = add42;
        }
        if (elAdd43)
        {
            elAdd43.value = add43;
        }
        if (elCity)
        {
            elCity.value = city;
        }
        if (elCounty)
        {
            elCounty.value = county;
        }
        if (elState)
        {
            elState.value = state;
        }
        if (elPostcode)
        {
            elPostcode.value = postcode;
        }
        if (elCountry)
        {
            elCountry.options[elCountry.selectedIndex].value = country;
        }
        if (elCountylist)
        {
            for (var i=0; i<elCountylist.options.length; i++)
            {
                if (elCountylist.options[i].value==regioncode)
                {
                    elCountylist.selectedIndex = i;
                    break
                }
            }
        }
        if (elStatelist)
        {
            for (var i=0; i<elStatelist.options.length; i++)
            {
                if (elStatelist.options[i].value==regioncode)
                {
                    elStatelist.selectedIndex = i;
                    break
                }
            }
        }

        if (elRegisteredTaxNumberType)
        {
            for (var i=0; i<elRegisteredTaxNumberType.options.length; i++)
            {
                if (elRegisteredTaxNumberType.options[i].value==registeredtaxnumbertype)
                {
                    elRegisteredTaxNumberType.selectedIndex = i;
                    break
                }
            }
        }

        if (elRegisteredTaxNumber)
        {
            elRegisteredTaxNumber.value = registeredtaxnumber;
        }
    }

    function saveFields()
    {
        /* set all missing.png back to asterisk.png */
        var images = document.getElementsByTagName("img");
        for ( var t = 0; t < images.length; ++t )
        {
            if (images[t].src.indexOf("/images/missing.png") !=-1)
            {
                images[t].src = "{/literal}{$webroot}{literal}/images/asterisk.png";
            }
        }
        /* save field data before AJAX call or submission */
        var elFirstname = document.getElementById('maincontactfname');
        var elLastname = document.getElementById('maincontactlname');
        var elCompany = document.getElementById('maincompanyname');
        var elAdd1 = document.getElementById('mainaddress1');
        var elAdd2 = document.getElementById('mainaddress2');
        var elAdd3 = document.getElementById('mainaddress3');
        var elAdd4 = document.getElementById('mainaddress4');
        var elAdd41 = document.getElementById('mainadd41');
        var elAdd42 = document.getElementById('mainadd42');
        var elAdd43 = document.getElementById('mainadd43');
        var elCity = document.getElementById('maincity');
        var elCounty = document.getElementById('maincounty');
        var elState = document.getElementById('mainstate');
        var elPostcode = document.getElementById('mainpostcode');
        var elRegion = document.getElementById('region');
        var elCountry = document.getElementById('countrylist');
        var elCountylist = document.getElementById('countylist');
        var elStatelist = document.getElementById('statelist');

        /* set variables to '' if field not present so it doesn't get saved */
        firstname = '';
        lastname = '';
        company = '';
        add1 = '';
        add2 = '';
        add3 = '';
        add4 = '';
        add41 = '';
        add42 = '';
        add43 = '';
        city = '';
        county = '';
        state = '';
        postcode = '';
        region = '';
        country = '';
        regioncode = '';
        if (elFirstname)
        {
            firstname = elFirstname.value;
        }
        if (elLastname)
        {
            lastname = elLastname.value;
        }
        if (elCompany)
        {
            company = elCompany.value;
        }
        if (elAdd1)
        {
            add1 = elAdd1.value;
        }
        if (elAdd2)
        {
            add2 = elAdd2.value;
        }
        if (elAdd3)
        {
            add3 = elAdd3.value;
        }
        if (elAdd4)
        {
            add4 = elAdd4.value;
        }
        if (elAdd41)
        {
            add41 = elAdd41.value;
        }
        if (elAdd42)
        {
            add42 = elAdd42.value;
        }
        if (elAdd43)
        {
            add43 = elAdd43.value;
        }
        if (elCity)
        {
            city = elCity.value;
        }
        if (elCounty)
        {
            county = elCounty.value;
        }
        if (elState)
        {
            state = elState.value;
        }
        if (elPostcode)
        {
            postcode = elPostcode.value;
        }
        if (elRegion)
        {
            region = elRegion.value;
        }
        if (elCountry)
        {
            country = elCountry.options[elCountry.selectedIndex].value;
        }
        if (elCountylist)
        {
            county = elCountylist.options[elCountylist.selectedIndex].text;
            regioncode = elCountylist.options[elCountylist.selectedIndex].value;

            if (regioncode == '--')
            {
                county = '';
            }
        }

        if (elStatelist)
        {
            state = elStatelist.options[elStatelist.selectedIndex].text;
            regioncode = elStatelist.options[elStatelist.selectedIndex].value;

            if (regioncode == '--')
            {
                state = '';
            }
        }
    }

        {/literal}

    {/if} {* end {if $section == 'accountdetails'} *}

    /* END ACCOUNT DETAILS */


    /* YOUR ORDERS */

    {if $section == 'yourorders'}

        {literal}

    function initializePanel()
    {
        document.getElementById('contentPanelAjax').style.width = (gScreenWidth * 3) + 'px';
        document.getElementById('orderMainPanel').style.width = gScreenWidth + 'px';
        document.getElementById('orderDetailPanel').style.width = gScreenWidth + 'px';
		document.getElementById('orderPreviewPanel').style.width = gScreenWidth + 'px';
        document.getElementById('onlineNameFormPanel').style.width = gScreenWidth + 'px';

        var innerBox = document.getElementsByClassName('innerBox')[0];
        // test if an order is displayed
        if (innerBox)
        {
            var styleInnerBox = innerBox.currentStyle || window.getComputedStyle(innerBox);
            var width = gOuterBoxContentBloc - parseIntStyle(styleInnerBox.borderLeftWidth) - parseInt(styleInnerBox.borderRightWidth);

            var container = document.getElementsByClassName('orderLabel')[0];
            var styleContainer = container.currentStyle || window.getComputedStyle(container);
            width = width - parseIntStyle(styleContainer.paddingLeft) - parseInt(styleContainer.paddingRight);

            // arrow image
            var arrow = document.getElementsByClassName('orderProductBtnDetail')[0];
            var styleArrow = arrow.currentStyle || window.getComputedStyle(arrow);
            width = width - parseIntStyle(styleArrow.width) - parseIntStyle(styleArrow.marginLeft) - parseInt(styleArrow.marginRight);

            // set the width of the project header
            var classLength = document.getElementsByClassName('orderProductLabel').length;
            for (var i = 0; i < classLength; i++)
            {
                var elm = document.getElementsByClassName('orderProductLabel')[i];

                if (i == 0)
                {
                    var styleLabel = elm.currentStyle || window.getComputedStyle(elm);
                    width = width - parseIntStyle(styleLabel.paddingLeft) - parseIntStyle(styleLabel.paddingRight);
                }

                elm.style.width = width + 'px';
            }
        }

    }

    function initializeSharePanel()
    {
        setScrollAreaHeight('contentRightScrollShare', 'contentNavigationShare');
    }

    function initializeShareSocial()
    {
        var container = document.getElementById('a2apage_dropdown');

        container.style.width = gOuterBoxContentBloc + gOuterBoxPadding + 'px';

        var a2aiLength = container.getElementsByClassName('a2a_i').length;
        for (var i = 0; i < a2aiLength; i++)
        {
            container.getElementsByClassName('a2a_i')[i].style.width = gOuterBoxContentBloc + 'px';
        }
    }

    function checkPassword()
    {
        if (document.getElementById('sharepassword').checked)
        {
            var previewPasswordObj = document.getElementById('previewPassword');

            if ((previewPasswordObj) && (previewPasswordObj.value == ''))
            {
                 highlight("previewPassword");
                return false;
            }
        }
        return true;
    }


    function showSharePanel(pProjectName)
    {
        //change the title of the popup
        var title = "{/literal}{#str_LabelShareProject#}{literal}";
        document.getElementById('shareProjectTitle').innerHTML = title.replace('^0', pProjectName);

        showPanelAccount(true, 'contentPanelShare');

        setTimeout(function(){
            document.getElementById('passwordForm').style.display = 'block';
        }, 300);

        document.getElementById('passwordForm').style.marginTop = '-150px';
        document.getElementById('sharepassword').checked = false;
        document.getElementById('previewPassword').value = '';

        window.location.hash = "share";
    }

    function passwordDisplay()
    {
        if (document.getElementById('sharepassword').checked)
        {
            document.getElementById('passwordForm').style.marginTop = '-0';
        }
        else
        {
            document.getElementById('passwordForm').style.marginTop = '-150px';
        }
    }

    function productUnshare()
    {
        processAjaxSmallScreen("unshare", '.?ishighlevel=' + isHighLevel + '&mawebhlbr=' + basketRef, 'POST', 'fsaction=Share.unshare&orderItemId='+ orderItemID);
    }

    function shareByEmail()
    {
        gAlerts = 0;
        var messageError = "{/literal}{#str_ErrorCompulsoryInformationMissing#}{literal}";
        if (!checkPassword())
        {
            messageError +="<br />{/literal}{#str_MessagePasswordInformation#}{literal}";
        }
        var emailTitle = document.getElementById('shareByEmailTitle').value;
        var emailRecipients = document.getElementById('shareByEmailTo').value;
        var shareByEmailText = document.getElementById('shareByEmailText').value;
        if (emailTitle == '')
        {
            messageError += "<br />{/literal}{#str_MessageEnterMessageTitle#}{literal}";
            highlight("shareByEmailTitle");

        }
        if (emailRecipients == '')
        {
            messageError += "<br />{/literal}{#str_MessageEnterAtLeastOneEmail#}{literal}";
            highlight("shareByEmailTo");
        }
        else
        {
            var emailAddressArray = new Array();
            var emailddressIsValid = true;
            emailAddressArray = emailRecipients.split(',');
            for (i = 0; i < emailAddressArray.length; i++)
            {
                recipient = emailAddressArray[i].replace(" ", "");
                emailddressIsValid = validateEmailAddress(recipient);
                if (!emailddressIsValid)
                {
                    messageError += "<br />{/literal}{#str_MessageInvalidEmail#}{literal}";
                    document.getElementById('shareByEmailTo').focus();
                    highlight("shareByEmailTo");
                }
            }
        }

		var format = ((document.location.protocol != 'https:') ? 1 : 0);

        if (gAlerts == 0)
        {
            var message = '<img src="{/literal}{$webroot}{literal}/images/loading_shoppingcart_v2.gif" class="loading-icon" alt="{/literal}{#str_MessageLoading#}{literal}" />';
            message += "&nbsp;{/literal}{#str_MessageSendingEmail#}{literal}";

            createDialog("{/literal}{#str_LabelConfirmation#}{literal}", message, function(e) {
                closeDialogShare(e)
            });

            document.getElementById('dialogBtn').style.display = 'none';

            var previewPasswordValue = '';
            if (document.getElementById('sharepassword').checked)
            {
                var previewPasswordObj = document.getElementById('previewPassword');
                if ((previewPasswordObj) && (previewPasswordObj.value != ''))
                {
					if (format == 1)
					{
						previewPasswordValue = hex_md5(previewPasswordObj.value);
					}
					else
					{
						previewPasswordValue = previewPasswordObj.value;
					}
                }
            }

        {/literal}

            {if $sharebyemailmethod == 1}

                {literal}

                    // email send by control center
                    processAjaxSmallScreen("shareByEmail", '/?ishighlevel=' + isHighLevel + '&mawebhlbr=' + basketRef, 'POST', "fsaction=Share.shareByEmail&orderItemId="+ orderItemID + '&title='+encodeURIComponent(emailTitle) + '&recipients='+encodeURIComponent(emailRecipients) + '&message='+encodeURIComponent(shareByEmailText) + '&previewPassword='+encodeURIComponent(previewPasswordValue) + '&format=' + format);

                {/literal}

            {else}

                {literal}

                    // mailto link
                    processAjaxSmallScreen("mailToLink", '/?ishighlevel=' + isHighLevel + '&mawebhlbr=' + basketRef, 'POST', "fsaction=Share.mailTo&orderItemId="+ orderItemID + '&title='+encodeURIComponent(emailTitle) + '&recipients='+encodeURIComponent(emailRecipients) + '&message='+encodeURIComponent(shareByEmailText) + '&previewPassword='+encodeURIComponent(previewPasswordValue) + '&format=' + format);

                {/literal}

            {/if}

        {literal}

        }
        else
        {
            createDialog("{/literal}{#str_TitleWarning#}{literal}", messageError, function(e) {
                closeDialog(e);
            });
        }
    }

    function closeDialogShare(e)
    {
        closeDialog(e);
        showPanelAccount(false, '');
        showShareDetails(false, '', false);
    }

        {/literal}

    {/if} {* end {if $section == 'yourorders'} *}

    /* END YOUR ORDERS */

    /* OPEN EXISTING PROJECT */

    {if $section == 'existingonlineprojects'}

        {literal}

    function initializePanel()
    {
        document.getElementById('contentPanelAjax').style.width = (gScreenWidth * 3) + 'px';
        document.getElementById('onlineMainPanel').style.width = gScreenWidth + 'px';
        document.getElementById('onlineDetailPanel').style.width = gScreenWidth + 'px';
        document.getElementById('onlineNameFormPanel').style.width = gScreenWidth + 'px';

        // make sure we got online projects
        var contentExistingProject = document.getElementById('contentExistingProject');
        if (contentExistingProject)
        {
            // rename and duplicate form
            var projectname = document.getElementById('projectname');
            var styleInputText = projectname.currentStyle || window.getComputedStyle(projectname);
            var widthInputText = gOuterBoxContentBloc - parseIntStyle(styleInputText.borderLeftWidth) - parseIntStyle(styleInputText.borderRightWidth);
            widthInputText = widthInputText - parseIntStyle(styleInputText.paddingLeft) - parseIntStyle(styleInputText.paddingRight);
            widthInputText = widthInputText - parseIntStyle(styleInputText.marginLeft) - parseIntStyle(styleInputText.marginRight);
            projectname.style.width = widthInputText + 'px';

            // arrow image
            var arrow = document.getElementsByClassName('orderProductBtnDetail')[0];
            var styleArrow = arrow.currentStyle || window.getComputedStyle(arrow);
            var width = gOuterBoxContentBloc - parseIntStyle(styleArrow.width) - parseIntStyle(styleArrow.marginLeft) - parseInt(styleArrow.marginRight);

            // set the width of the project header
            var classLength = document.getElementsByClassName('orderProductLabel').length;
            for (var i = 0; i < classLength; i++)
            {
                var elm = document.getElementsByClassName('orderProductLabel')[i];

                if (i == 0)
                {
                    var styleLabel = elm.currentStyle || window.getComputedStyle(elm);
                    width = width - parseIntStyle(styleLabel.paddingLeft) - parseIntStyle(styleLabel.paddingRight);
                }

                elm.style.width = width + 'px';
            }

            // set the width of the project description
            var classLength = document.getElementsByClassName('descriptionProduct').length;
            for (var i = 0; i < classLength; i++)
            {
                var elm = document.getElementsByClassName('descriptionProduct')[i];

                if (i == 0)
                {
                    var styleLabel = elm.currentStyle || window.getComputedStyle(elm);
                    width = gOuterBoxContentBloc - parseIntStyle(styleLabel.paddingLeft) - parseIntStyle(styleLabel.paddingRight);
                }

                elm.style.width = width + 'px';
            }
        }
    }

	function fnKeepOnlineProject(event)
	{
			keepOnlineProject(event.getAttribute('data-projectref'));
	}

	function keepOnlineProject(projectRef)
	{
		showConfirmDialog("{/literal}{#str_TitleKeepProject#}{literal}", "{/literal}{#str_MessageKeepProjectWarningMessage#}{literal}", function(event) {
			closeDialog(event);
			event.preventDefault();
			event.stopImmediatePropagation();
			showLoadingDialog("{/literal}{#str_TitleKeepingProject#}{literal}");
			processAjax("keeponlineproject", ".?fsaction=AjaxAPI.callback&cmd=KEEPONLINEPROJECT&projectref=" + projectRef, 'POST', '', true);
		});
	}

	function purgeFlaggedProjects()
	{
		showConfirmDialog("{/literal}{#str_TitlePleaseConfirm#}{literal}", "{/literal}{#str_MessageConfirmPurgeProjectsDeletionMessage#}{literal}", function(event) {
			closeDialog(event);
			event.preventDefault();
			event.stopImmediatePropagation();
			showLoadingDialog("{/literal}{#str_TitlePurgingProjects#}{literal}");
			processAjax("purgeflaggedprojects", ".?fsaction=AjaxAPI.callback&cmd=PURGEFLAGGEDPROJECTS", 'POST', '', true);
		});
	}

	function completeOrder()
	{
		var divObject = document.getElementById('onlineProjectDetail' + gActiveProductOnline);

		var projectRef = gActiveProductOnline;
		var workflowType = divObject.getAttribute("data-workflowtype");
		var productIndent = divObject.getAttribute("data-productident");

		processAjaxSmallScreen("completeorder", ".?fsaction=AjaxAPI.callback&cmd=COMPLETEORDER&projectref=" + projectRef + '&workflowtype=' + workflowType + '&productindent=' + productIndent + '&ishighlevel=' + isHighLevel + '&mawebhlbr=' + basketRef, 'POST', '');
	}

        {/literal}

    {/if} {* end {if $section == 'existingonlineprojects'} *}

    {* END SMALL SPECIFIC FUNCTION *}

{else} {* else {if $issmallscreen == 'true'} *}

    {* LARGE SPECIFIC FUNCTION *}

    {literal}

    window.onload = function()
    {
        // Add listener to langauge select.
        var langSelectElement = document.getElementById('systemlanguagelist');
        if (langSelectElement)
        {
            langSelectElement.addEventListener('change', function() {
                return setSystemLanguage();
            });
        }

		// Add listener to viewOnlineProjects link
		var checkOnlineProjectList = document.getElementById('viewOnlineProjects');
		if (checkOnlineProjectList !== null) {
			checkOnlineProjectList.addEventListener('click', function(event) {
				fnVisitCheckProjects(event.target);
			});
		}

        // Add listener to sign out button.
        document.getElementById('logoutButton').addEventListener('click', function() {
            document.getElementById('headerform').submit();
        });

    {/literal}{if $showgiftcardsbalance}{literal}
        var giftCardInputElement = document.getElementById('giftcardid');
        if (giftCardInputElement)
        {
            // Add listener to the redeem gift card input.
            giftCardInputElement.addEventListener('keyup', function(event) {
                if (giftCardInputElement.value != '')
                {
                    forceUpperAlphaNumeric(this);

                    // Check for enter key. 
                    if (enterKeyPressed(event))
                    {
                        redeemGiftCard();
                    }
                }

                return false;
            });
        }

        // Add listener to the redeem gift card button.
        document.getElementById('setGiftCardButton').addEventListener('click', function() {
            return redeemGiftCard();
        });
    {/literal}{/if}{literal}

    {/literal}{if $redactionmode >= 2}{literal}
        // Add listener to the redaction request link.
        var redactionRequestLink = document.getElementById('dataDeletionOptionLink');
        if (redactionRequestLink)
        {
            redactionRequestLink.addEventListener('click', function() {
                return dataDeletion({/literal}{$redactionmode}{literal}, {/literal}{$redactiondays}{literal});
            });
        }

        // Add listener to the redaction confirmation dialog cancel button.
        document.getElementById('closeRedactionConfirmButton').addEventListener('click', function() {
            closeRedactionConfirmationBox();
        });

        // Add listener to the redaction confirmation dialog confirm button.
        document.getElementById('confirmRedactionButton').addEventListener('click', function() {
            redactionConfirmation({/literal}{$redactionmode}{literal}, {/literal}{$redactiondays}{literal});
        });
    {/literal}{/if}{literal}

    {/literal}{if ($section == 'menu')}{literal}
        // Add listeners to account pages menu buttons.
        var classname = document.getElementsByClassName('menuActionButton');
        for (var i = 0; i < classname.length; i++)
        {
            classname[i].addEventListener('click', function() {
                var menuActionValue = this.getAttribute("data-action");
                return menuAction(menuActionValue);
            });
        }
    {/literal}{else}{literal}
        // Add the event listener to the home link.
        var homeLink = document.getElementById('homeLink');
        if (homeLink)
        {
            homeLink.addEventListener('click', function() {
                document.submitform.fsaction.value = '{/literal}{$homebuttonfuseaction}{literal}';
                document.submitform.submit();
                return false;
            });
        }

        // Add the event listener to the back buttons.
        var backButton = document.getElementById('backButton');
        if (backButton)
        {
            backButton.addEventListener('click', function() {
                document.submitform.fsaction.value = '{/literal}{$homebuttonfuseaction}{literal}';
                document.submitform.submit();
                return false;
            });
        }
    {/literal}{/if}{literal}


    {/literal}

        {if ($section == 'existingonlineprojects') && ($projects|@sizeof > 0)}

            {literal}

        calcualteScrollableView();

            {/literal}

        {/if}

    {literal}

        onloadWindow()

        /* YOUR ORDERS */

    {/literal}{if $section == 'yourorders'}{literal}
        // Add listeners to the continue editing buttons.
        var classname = document.getElementsByClassName('yourOrderActionButton');
        for (var i = 0; i < classname.length; i++)
        {
            classname[i].addEventListener('click', function() {
                var productID = this.getAttribute("data-productid");
                var action = this.getAttribute("data-buttonaction");
                var projectName = this.getAttribute("data-projectname");
                var applicationName = this.getAttribute("data-webbrandapplicationname");
                var projectRef = this.getAttribute("data-projectref");
                var workflowType = this.getAttribute("data-workflowtype");
                var productIdent = this.getAttribute("data-indent");

                return executeButtonAction(this, productID, action, projectName, applicationName, projectRef, workflowType, productIdent);
            });
        }

        // Add listeners to the preview buttons.
        var classname = document.getElementsByClassName('browserPreviewButton');
        for (var i = 0; i < classname.length; i++)
        {
            classname[i].addEventListener('click', function() {
                var productID = this.getAttribute("data-productid");
                var baseurl = this.getAttribute("data-baseurl");
                var ref = this.getAttribute("data-ref");
                var ssotoken = this.getAttribute("data-ssotoken");
                var productIdent = this.getAttribute("data-indent");

                var previewURL = baseurl + '&ref=' + ref + '&id=' + productID + '&ssotoken=' + ssotoken;

                return checkBrowserPreview(previewURL);
            });
        }

		// Check for the delete option on the orders section.
		var deleteOptionButtons = document.getElementsByClassName('deleteOrderButton');

		if (deleteOptionButtons.length > 0)
		{
			var confirmDeleteMessageTemplate = "{/literal}{#str_MessageDeleteProjectConfirmation#}{literal}";
			for (var i = 0; i < deleteOptionButtons.length; i++)
			{
				deleteOptionButtons[i].addEventListener('click', function(evt) {
					var orderNumber = '';
					var eventParams = '';

					if (evt.target.classList.contains('deleteOrderButton'))
					{
						orderNumber = evt.target.dataset.ordernumber;
						eventParams = Object.keys(evt.target.dataset).map(function(key) {
							return key + '=' + encodeURIComponent(evt.target.dataset[key]);
						}).join('&');
					}
					else
					{
						orderNumber = evt.target.parentElement.dataset.ordernumber;
						eventParams = Object.keys(evt.target.parentElement.dataset).map(function(key) {
							return key + '=' + encodeURIComponent(evt.target.parentElement.dataset[key]);
						}).join('&');
					}

					orderNumber = orderNumber.trim();
					var confirmDeleteMessage = confirmDeleteMessageTemplate.replace('^0', "'" + orderNumber + "'");
					showConfirmDialog("{/literal}{#str_LabelDeleteProject#}{literal}", confirmDeleteMessage, function(e) {
						processAjax('orderdelete', '?fsaction=Customer.deleteOrder', 'POST', eventParams, false);
						return false;
					});
				});
			}
		}

        // Add listener to the share by email button.
        document.getElementById('shareByEmailBtn').addEventListener('click', function(event) {
            shareByEmail();
        });

        // Add listener to the browser confirmation buttom.
        document.getElementById('browserConfirmContentBtn').addEventListener('click', function(event) {
            hideBrowserWarning();
        });

        // Add listener to the share method option.
        document.getElementById('shareMethodsSocial').addEventListener('click', function(event) {
            changeShareMethod();
        });

		if (document.getElementById('shareMethodsEmail') !== null)
		{
			// Add listener to the share by email option.
			document.getElementById('shareMethodsEmail').addEventListener('click', function(event) {
				changeShareMethod();
			});
		}

        // Add listener to the activate password protection checkbox.
        document.getElementById('sharepassword').addEventListener('click', function(event) {
            passwordDisplay();
        });
        
        // Add listener to show/hide password.
		var togglePreviewPasswordElement = document.getElementById('togglepreviewpassword');
		if (togglePreviewPasswordElement)
		{
			togglePreviewPasswordElement.addEventListener('click', function() {
					togglePasswordVisibility(togglePreviewPasswordElement, 'previewPassword');
			});
		}

        // Add listener to the browser confirmation buttom.
        var payNowButton = document.getElementById('executePayNowButton');
        if (payNowButton)
        {
            payNowButton.addEventListener('click', function(event) {
                var ref = this.getAttribute("data-ref");

                executePayNow(ref);
            });
        }

    {/literal}{/if}

        /* END YOUR ORDERS */

        /* ONLINE PROJECTS */

    {if $section == 'existingonlineprojects'}{literal}
        // Add listeners to account pages menu buttons.
        var classname = document.getElementsByClassName('contentRow');
        for (var i = 0; i < classname.length; i++)
        {
            classname[i].addEventListener('click', function() {
                return selectProject(this);
            });
        }
    {/literal}{/if}

        /* END ONLINE PROJECTS */

        /* YOUR ORDERS & ONLINE PROJECTS */

    {if $section == 'yourorders' || $section == 'existingonlineprojects'}{literal}
        // Add listener to the close button of the confirmdialog box.
        document.getElementById('projectcancelbutton').addEventListener('click', function(event) {
            return closeDialogBoxOnlineAction();
        });

        // Add listeners to the close confirmation dialog buttons.
        var classname = document.getElementsByClassName('closeConfirmationContainer');
        for (var i = 0; i < classname.length; i++)
        {
            classname[i].addEventListener('click', function() {
                return closeConfirmationBox();
            });
        }

        // Add listeners to unshare confirmation dialog buttons.
        var classname = document.getElementsByClassName('unshareConfirmContainer');
        for (var i = 0; i < classname.length; i++)
        {
            classname[i].addEventListener('click', function() {
                return unshareConfirmContainer();
            });
        }
    {/literal}{/if}

        /* END YOUR ORDERS & ONLINE PROJECTS */

        /* ACCOUNT DETAILS */

    {if $section == 'accountdetails'}{literal}
        // Add listener to the exit of the telephone number input.
        document.getElementById('telephonenumber_account').addEventListener('blur', function(event) {
            return CJKHalfWidthFullWidthToASCII(this, false);
        });


        // Add listener to the update button on the change preferences form.
        document.getElementById('updateButton').addEventListener('click', function(event) {
            if (isCustomerAuthEnabled)
            {
                return showVerifyAccountDialog();
            }
            else
            {
                return verifyAddress();
            }
        });

        processAjax("ajaxdiv",".?fsaction=AjaxAPI.callback&cmd=ADDRESSFORM&country=" + country + "&addresstype=billing&hideconfigfields=" + hideConfigFields +"&strict=1&edit={/literal}{$edit}{literal}", 'GET', '', true);

    {/literal}{/if}{literal}

        /* END ACCOUNT DETAILS */

        /* CHANGE PASSWORD */

    {/literal}{if $section == 'changepassword'}{literal}
        // Add the listener to the new password input for the password strength.
        document.getElementById('newpassword').addEventListener('keyup', function() {
            passwordStrength.scorePassword(this.value, 'strengthvalue', 'strengthtext');
            return false;
        });

        // Add listener to the update button on the change preferences form.
        document.getElementById('updateButton').addEventListener('click', function(event) {
            return checkFormChangePassword();
        });
    
		var toggleNewPasswordElement = document.getElementById('togglenewpassword');
		if (toggleNewPasswordElement)
		{
			toggleNewPasswordElement.addEventListener('click', function() {
				togglePasswordVisibility(toggleNewPasswordElement, 'newpassword');
			});
		}
        
    {/literal}{/if}{literal}

        /* END CHANGE PASSWORD */

        /* CHANGE PREFERENCES */

    {/literal}{if $section == 'changepreferences'}{literal}
        // Add listener to the update button on the change preferences form.
        document.getElementById('updateButton').addEventListener('click', function(event) {
            return checkFormChangePreferences();
        });
    {/literal}{/if}{literal}

        /* END CHANGE PREFERENCES */

        // Add listener to the confirmation box accept.
        var confirmAcceptButton = document.getElementById('confirmationBoxAcceptButton');
        if (confirmAcceptButton)
        {
            confirmAcceptButton.addEventListener('click', function() {
                closeConfirmationBox();
            });
        }

        // Add listener to window resize.
        window.addEventListener('resize', function() {
            resizePopup();
        });

        if ("{/literal}{$message}{literal}".length > 0)
        {
            document.getElementById('message').style.display = 'block';
        }

            {/literal}
            {if $showgiftcardmessage == 1}
            {literal}

        displayGiftCardAlert("{/literal}{$giftcardresult}{literal}", "");

            {/literal}
            {/if}
            {literal}
    }

    function redeemGiftCard()
    {
        var giftcardtext =  document.getElementById("giftcardid").value;
        if((giftcardtext != '') && (giftcardtext != "{/literal}{#str_LabelEnterCode#}{literal}"))
        {
            /* pass localised timestamp */
            var timestamp = getTimestamp();

            document.submitform.tzoffset.value = timestamp;
            document.submitform.giftcardcode.value = giftcardtext;
            document.submitform.giftcardaction.value = "{/literal}{$section}{literal}";
            document.submitform.showgiftcardmessage.value = 1;
            document.submitform.fsaction.value = 'Customer.updateGiftCard';
            document.submitform.submit();
        }
        return false;
    }

    function setSystemLanguage()
    {
        changeSystemLanguage("{/literal}{$refreshaction}{literal}", "submitform", 'post');
    }


    function hideLoadingDialog()
    {
        var loadingBoxObj = document.getElementById('loadingBox');
        var shimObj = document.getElementById('shimLoading');

        if (shimObj)
        {
            shimObj.style.display = 'none';
        }

        if (loadingBoxObj)
        {
            loadingBoxObj.style.display = 'none';
        }

		return true;
    }

	function showErrorBox(pErrorMessage, pCallBack)
	{
		var boxObj = document.getElementById('errorBox');
		var boxTextObj = document.getElementById('errorDialogContent');
		var shimObj = document.getElementById('shim');

		boxObj.style.display = 'block';
		boxTextObj.style.display = 'block';

		if (shimObj)
		{
			shimObj.style.display = 'block';
			shimObj.style.height = document.body.offsetHeight + 'px';
			document.body.className +=' hideSelects';
		}

		boxObj.style.left = Math.round((shimObj.offsetWidth / 2) - (boxObj.offsetWidth / 2)) + 'px';

		var windowHeight = document.documentElement.clientHeight;
		boxObj.style.top = Math.round((windowHeight - boxObj.offsetHeight) / 2) + 'px';
		boxTextObj.innerHTML = pErrorMessage;

		var buttonsHolderObj = document.getElementById('errorDialogButton');
	}

    function showConfirmationBox(pConfirmMessage, pCallBack)
    {
        var confirmationBoxObj = document.getElementById('confirmationBox');
        var confirmationBoxTextObj = document.getElementById('confirmationBoxText');
        var shimObj = document.getElementById('shim');

        confirmationBoxObj.style.display = 'block';
        confirmationBoxTextObj.style.display = 'block';

        if (shimObj)
        {
            shimObj.style.display = 'block';
            shimObj.style.height = document.body.offsetHeight + 'px';
            document.body.className +=' hideSelects';
        }

        confirmationBoxObj.style.left = Math.round((shimObj.offsetWidth / 2) - (confirmationBoxObj.offsetWidth / 2)) + 'px';

        var windowHeight = document.documentElement.clientHeight;
        confirmationBoxObj.style.top = Math.round((windowHeight - confirmationBoxObj.offsetHeight) / 2) + 'px';

        confirmationBoxTextObj.className = confirmationBoxTextObj.className.replace(' confirmationText', '');

        confirmationBoxTextObj.className = confirmationBoxTextObj.className + ' confirmationText';
        confirmationBoxTextObj.innerHTML = pConfirmMessage;

        var buttonsHolderConfirmationObj = document.getElementById('buttonsHolderConfirmation');
        var buttonsHolderQuestionObj = document.getElementById('buttonsHolderQuestion');
        if (buttonsHolderConfirmationObj)
        {
            buttonsHolderConfirmationObj.style.display = 'block';
        }

        if (buttonsHolderQuestionObj)
        {
            buttonsHolderQuestionObj.style.display = 'none';
        }
    }


	function showRedactionConfirmationBox(pConfirmMessage, pCallBack)
	{
        var confirmationBoxObj = document.getElementById('redactConfirmBox');
        var confirmationBoxTextObj = document.getElementById('redactConfirmBoxText');
        var shimObj = document.getElementById('shim');

        confirmationBoxObj.style.display = 'block';
        confirmationBoxTextObj.style.display = 'block';

        if (shimObj)
        {
            shimObj.style.display = 'block';
            shimObj.style.height = document.body.offsetHeight + 'px';
            document.body.className +=' hideSelects';
        }

        confirmationBoxObj.style.left = Math.round((shimObj.offsetWidth / 2) - (confirmationBoxObj.offsetWidth / 2)) + 'px';

        var windowHeight = document.documentElement.clientHeight;
        confirmationBoxObj.style.top = Math.round((windowHeight - confirmationBoxObj.offsetHeight) / 2) + 'px';

        confirmationBoxTextObj.className = confirmationBoxTextObj.className.replace(' confirmationText', '');

        confirmationBoxTextObj.className = confirmationBoxTextObj.className + ' confirmationText';
        confirmationBoxTextObj.innerHTML = pConfirmMessage;

        var buttonsHolderConfirmationObj = document.getElementById('buttonsHolderRefactor');
        if (buttonsHolderConfirmationObj)
        {
            buttonsHolderConfirmationObj.style.display = 'block';
        }
	}


	function resizePopup()
	{
        var dialogBox = document.getElementById('dialogBox');
		var confirmationBoxObj = document.getElementById('confirmationBox');
		var shimObj = document.getElementById('shim');
		var windowHeight = document.documentElement.clientHeight;

		if ((dialogBox) && (shimObj) && (dialogBox.style.display == "block"))
        {
            shimObj.style.height = document.body.offsetHeight + 'px';

			dialogBox.style.display = 'block';
			dialogBox.style.left = Math.round(shimObj.offsetWidth / 2 - dialogBox.offsetWidth/2) + 'px';
			dialogBox.style.top = Math.round((windowHeight - dialogBox.offsetHeight) / 2) + 'px';
        }

		if ((confirmationBoxObj) && (shimObj) && (confirmationBoxObj.style.display == "block"))
        {
            shimObj.style.height = document.body.offsetHeight + 'px';

			confirmationBoxObj.style.left = Math.round(shimObj.offsetWidth / 2 - confirmationBoxObj.offsetWidth/2) + 'px';

            var windowHeight = document.documentElement.clientHeight;
            confirmationBoxObj.style.top = Math.round((windowHeight - confirmationBoxObj.offsetHeight) / 2) + 'px';
		}
	}

    {/literal}


    /* MENU */

    {if $section=='menu'}

        {literal}

    function menuAction(actionUrl)
    {
		/* pass localised timestamp */
		var timestamp = getTimestamp();

		document.submitform.tzoffset.value = timestamp;
        document.submitform.fsaction.value = actionUrl;
        document.submitform.submit();
        return false;
    }

        {/literal}

    {/if} {* end {if $section=='menu'} *}

    /* END MENU */

    /* ACCOUNT DETAILS */

    {if $section=='accountdetails'}

        {literal}

    function restoreFields()
    {
        /* restore field data after AJAX call */
        form = document.mainform;
        var elFirstname = form.maincontactfname;
        var elLastname = form.maincontactlname;
        var elCompany = form.maincompanyname;
        var elAdd1 = form.mainaddress1;
        var elAdd2 = form.mainaddress2;
        var elAdd3 = form.mainaddress3;
        var elAdd4 = form.mainaddress4;
        var elAdd41 = form.mainadd41;
        var elAdd42 = form.mainadd42;
        var elAdd43 = form.mainadd43;
        var elCity = form.maincity;
        var elCounty = form.maincounty;
        var elState = form.mainstate;
        var elPostcode = form.mainpostcode;
        var elCountry = form.countrylist;
        var elCountylist = form.countylist;
        var elStatelist = form.statelist;
        var elRegisteredTaxNumberType = form.regtaxnumtype;
        var elRegisteredTaxNumber = form.regtaxnum;

        if (elFirstname)
        {
            elFirstname.value = firstname;
        }
        if (elLastname)
        {
            elLastname.value = lastname;
        }
        if (elCompany)
        {
            elCompany.value = company;
        }
        if (elAdd1)
        {
            elAdd1.value = add1;
        }
        if (elAdd2)
        {
            elAdd2.value = add2;
        }
        if (elAdd3)
        {
            elAdd3.value = add3;
        }
        if (elAdd4)
        {
            elAdd4.value = add4;
        }
        if (elAdd41)
        {
            elAdd41.value = add41;
        }
        if (elAdd42)
        {
            elAdd42.value = add42;
        }
        if (elAdd43)
        {
            elAdd43.value = add43;
        }
        if (elCity)
        {
            elCity.value = city;
        }
        if (elCounty)
        {
            elCounty.value = county;
        }
        if (elState)
        {
            elState.value = state;
        }
        if (elPostcode)
        {
            elPostcode.value = postcode;
        }
        if (elCountry)
        {
            elCountry.options[elCountry.selectedIndex].value = country;
        }
        if (elCountylist)
        {
            for (var i=0; i < elCountylist.options.length; i++)
            {
                if (elCountylist.options[i].value==regioncode)
                {
                    elCountylist.selectedIndex = i;
                    break
                }
            }
        }
        if (elStatelist)
        {
            for (var i=0; i < elStatelist.options.length; i++)
            {
                if (elStatelist.options[i].value==regioncode)
                {
                    elStatelist.selectedIndex = i;
                    break
                }
            }
        }

        if (elRegisteredTaxNumberType)
        {
            for (var i=0; i < elRegisteredTaxNumberType.options.length; i++)
            {
                if (elRegisteredTaxNumberType.options[i].value==registeredtaxnumbertype)
                {
                    elRegisteredTaxNumberType.selectedIndex = i;
                    break
                }
            }
        }

        if (elRegisteredTaxNumber)
        {
            elRegisteredTaxNumber.value = registeredtaxnumber;
        }
    }

    function saveFields()
    {
        /* set all missing.png back to asterisk.png */
        var images = document.getElementsByTagName("img");
        for ( var t = 0; t < images.length; ++t )
        {
            if (images[t].src.indexOf("/images/missing.png") !=-1)
            {
                images[t].src = "{/literal}{$webroot}{literal}/images/asterisk.png";
            }
        }
        /* save field data before AJAX call or submission */
        var theForm = document.getElementById("mainform");
        var elFirstname = theForm.elements.maincontactfname;
        var elLastname = theForm.elements.maincontactlname;
        var elCompany = theForm.elements.maincompanyname;
        var elAdd1 = theForm.elements.mainaddress1;
        var elAdd2 = theForm.elements.mainaddress2;
        var elAdd3 = theForm.elements.mainaddress3;
        var elAdd4 = theForm.elements.mainaddress4;
        var elAdd41 = theForm.elements.mainadd41;
        var elAdd42 = theForm.elements.mainadd42;
        var elAdd43 = theForm.elements.mainadd43;
        var elCity = theForm.elements.maincity;
        var elCounty = theForm.elements.maincounty;
        var elState = theForm.elements.mainstate;
        var elPostcode = theForm.elements.mainpostcode;
        var elRegion = theForm.elements.region;
        var elCountry = theForm.elements.countrylist;
        var elCountylist = theForm.elements.countylist;
        var elStatelist = theForm.elements.statelist;

        /* set variables to '' if field not present so it doesn't get saved */
        firstname = '';
        lastname = '';
        company = '';
        add1 = '';
        add2 = '';
        add3 = '';
        add4 = '';
        add41 = '';
        add42 = '';
        add43 = '';
        city = '';
        county = '';
        state = '';
        postcode = '';
        region = '';
        country = '';
        regioncode = '';
        if (elFirstname)
        {
            firstname = elFirstname.value;
        }
        if (elLastname)
        {
            lastname = elLastname.value;
        }
        if (elCompany)
        {
            company = elCompany.value;
        }
        if (elAdd1)
        {
            add1 = elAdd1.value;
        }
        if (elAdd2)
        {
            add2 = elAdd2.value;
        }
        if (elAdd3)
        {
            add3 = elAdd3.value;
        }
        if (elAdd4)
        {
            add4 = elAdd4.value;
        }
        if (elAdd41)
        {
            add41 = elAdd41.value;
        }
        if (elAdd42)
        {
            add42 = elAdd42.value;
        }
        if (elAdd43)
        {
            add43 = elAdd43.value;
        }
        if (elCity)
        {
            city = elCity.value;
        }
        if (elCounty)
        {
            county = elCounty.value;
        }
        if (elState)
        {
            state = elState.value;
        }
        if (elPostcode)
        {
            postcode = elPostcode.value;
        }
        if (elRegion)
        {
            region = elRegion.value;
        }
        if (elCountry)
        {
            country = elCountry.options[elCountry.selectedIndex].value;
        }
        if (elCountylist)
        {
            county = elCountylist.options[elCountylist.selectedIndex].text;
            regioncode = elCountylist.options[elCountylist.selectedIndex].value;

            if (regioncode == '--')
            {
                county = '';
            }
        }

        if (elStatelist)
        {
            state = elStatelist.options[elStatelist.selectedIndex].text;
            regioncode = elStatelist.options[elStatelist.selectedIndex].value;

            if (regioncode == '--')
            {
                state = '';
            }
        }
    }

    function scrollHandler()
    {
        /* handle the main area being scrolled under ie when the auto suggest is active */
        var scrollOffset = 0;
        var parentObject = document.getElementById("content");
        if (parentObject)
        {
            scrollOffset = initilializeApp;
        }

        if (gAs_jsonCity)
        {
            gAs_jsonCity.onScroll(scrollOffset);
        }

        if (gAs_jsonCounty)
        {
            gAs_jsonCounty.onScroll(scrollOffset);
        }

        if (gAs_jsonState)
        {
            gAs_jsonState.onScroll(scrollOffset);
        }
        return true;
    }


        {/literal}

    {/if}

    /* END ACCOUNT DETAILS */

    /* CHANGE PREFERENCES */

    {if $section == 'changepreferences'}

        {literal}

    function checkFormChangePreferences()
    {
    	document.submitform.fsaction.value = 'Customer.updatePreferences';
        document.submitform.sendmarketinginfo.value = (document.getElementById('subscribed').checked) ? '1' : '0';
        document.submitform.submit();

        return false;
    }

        {/literal}

    {/if}

    /* END CHANGE PREFERENCES */

    /* YOUR ORDERS */

    {if $section == 'yourorders'}

        {literal}

    var unshareObjLink = '';
    var shareObjLink = '';
    var shareName = '';
    var unshareItemId = 0;
    var overElement = '';
    var orderItemID = -1;

    var a2a_config = {
        num_services: 16,
        show_menu: {
            position: "static",
            top: "0px",
            left: "0px"
        },
        color_link_text: "333333",
        color_link_text_hover: "333333"
    };


	a2a_config.callbacks = a2a_config.callbacks || [];
	a2a_config.callbacks.push({
		ready: reinitAddtoany,
		share: my_addtoany_onshare
	});

    a2a_config.exclude_services = ["email"];

    function reinitAddtoany()
    {
        // we need to do it two times, one for the tab and one for the link in list
        if (document.getElementById('a2apage_EMAIL'))
        {
            document.getElementById('a2apage_EMAIL').parentNode.removeChild(document.getElementById('a2apage_EMAIL'));
        }

        if (document.getElementById('a2apage_email'))
        {
            document.getElementById('a2apage_email').parentNode.removeChild(document.getElementById('a2apage_email'));
        }
    }

    function my_addtoany_onshare(pData)
    {
        if (! checkPassword())
        {
            alert("{/literal}{#str_MessagePasswordInformation#}{literal}");

            return{
                stop: true
            }
        }
        else
        {
			var format = ((document.location.protocol != 'https:') ? 1 : 0);
            var previewPasswordValue = '';
            if( document.getElementById('sharepassword').checked)
            {
                var previewPasswordObj = document.getElementById('previewPassword');
                if ((previewPasswordObj) && (previewPasswordObj.value != ''))
                {
					if (format == 1)
					{
						previewPasswordValue = hex_md5(previewPasswordObj.value);
					}
					else
					{
						previewPasswordValue = previewPasswordObj.value;
					}
                }
            }

            if (shareObjLink)
            {
                var prevSibling = shareObjLink.previousSibling;
                while(prevSibling && prevSibling.nodeType != 1)
                {
                    prevSibling = prevSibling.previousSibling;
                }
				if (null !== prevSibling) {
                	prevSibling.style.display = 'block';
				}
            }

            var newUrl = processAjax("shareurl", '', 'POST', "fsaction=Share.shareAddToAny&orderItemId=" + orderItemID + '&method=' + encodeURIComponent(pData.service) + '&previewPassword='+ encodeURIComponent(previewPasswordValue) + '&format=' + format, false);

			if (newUrl == 'str_ErrorSessionExpired')
			{
				return{
					stop: true
				}
			}
			else
			{
                // due to a bug in add to any we must update the first node on the add to any object with the new project name and share url
				a2a_config.linkurl = newUrl;
				a2a_config.linkname = shareName;

				var shareLinks = document.getElementsByClassName('a2a_i');
				if (shareLinks.length > 0) {
					for (var i = 0; i < shareLinks.length; i++) {
						if (pData.service !== shareLinks[i].innerText.trim()) {
							continue;
						}
						if ('/#' + pData.service === shareLinks[i].href) {
							continue;
						}

						var initLink = shareLinks[i].href;
						var shareUrl = new URL(shareLinks[i].href);
						shareUrl.searchParams.set('linkurl', newUrl);
						shareUrl.searchParams.set('linkname', shareName);
						shareLinks[i].setAttribute('href', shareUrl.href);
					}
				}

				setTimeout(function() {
					return {
						url: newUrl,
						title: shareName
					}
				}, 150);
			}
        }
    }

    function mouseOverEffect(obj)
    {
        if (overElement != '')
        {
            overElement.className = overElement.className.replace(' activeOver', '');
        }

        if (typeof obj != 'undefined')
        {
            obj.className = obj.className + ' activeOver';
            overElement = obj;
        }
        else
        {
            overElement = '';
        }
    }

    function closeDialogBox()
    {
        var dialogBox = document.getElementById('dialogBox');
        if (dialogBox)
        {
            dialogBox.style.display = 'none';
        }

		if (processingAjax === true)
		{
			processingAjax = false;
		}
    }

    function checkPassword()
    {
        if (document.getElementById('sharepassword').checked)
        {
            var previewPasswordObj = document.getElementById('previewPassword');

            if ((previewPasswordObj) && (previewPasswordObj.value == ''))
            {
                highlight("previewPassword");
                return false;
            }
        }
        return true;
    }

    function productUnshare()
    {
        var fsactionField = document.submitform.fsaction;
        var orderItemIdField = document.submitform.orderitemid;
        var actionField = document.submitform.action;
        if (fsactionField)
        {
            fsactionField.value = 'Share.unshare';
        }
        if (orderItemIdField)
        {
            orderItemIdField.value = orderItemID;
        }
        if (actionField)
        {
            actionField.value = 'CUSTOMER UNSHARE';
        }

		processAjax("unshare", '/', 'POST', "fsaction=Share.unshare&orderItemId=" + orderItemID, true);
    }

    function closeConfirmationBox(pOnBeforeClose)
    {
        if (typeof pOnBeforeClose != 'undefined')
        {
            pOnBeforeClose();
        }

        var shimObj = document.getElementById('shim');
        var dialogBoxObj = document.getElementById('dialogBox');
        var confirmationBoxObj = document.getElementById('confirmationBox');
        var shimIframe = document.getElementById('a2apage_shim');

        if (shimObj)
        {
			if (shimObj.style.zIndex == 200)
			{
				shimObj.style.display = 'none';
			}
			else
			{
				// reset the ShimObj's zIndex back to original when user close any box
				shimObj.style.zIndex = 200;
			}
        }

        if ((dialogBoxObj) && (dialogBoxObj.style.display == 'block'))
        {
            dialogBoxObj.style.display = 'none';
			shimObj.style.display = 'none';
			shimObj.style.zIndex = 200;
        }

        if (confirmationBoxObj)
        {
            confirmationBoxObj.style.display = 'none';
        }

        if (shimIframe)
        {
            shimIframe.style.display = 'none';
        }

		if (processingAjax === true)
		{
			processingAjax = false;
		}

        document.body.className = document.body.className.replace(' hideSelects', '');
    }

    function changeShareMethod()
    {
        var findSource = '';
        aInput = document.getElementsByName("shareMethod");
        for (var i = 0; i < aInput.length; i++)
        {
            element = aInput[i];
            if (element.checked)
            {
                findSource = element.value;
            }
        }

        if (findSource != '')
        {
            if (findSource == 'email')
            {
                document.getElementById('shareMethods').style.display = 'none';
                document.getElementById('shareEmail').style.display = 'block';
                document.getElementById('shareByEmailBtn').style.display = 'inline-block';
            }
            else
            {
                document.getElementById('shareMethods').style.display = 'block';
                document.getElementById('shareEmail').style.display = 'none';
                document.getElementById('shareByEmailBtn').style.display = 'none';
            }
        }
        else
        {
            document.getElementById('shareMethods').style.display = 'none';
            document.getElementById('shareEmail').style.display = 'none';
            document.getElementById('shareByEmailBtn').style.display = 'none';
        }
    }

    function openDialogBoxShareProject(pProjectName)
    {
        /*hide panel */
        changeShareMethod();

        //change the title of the popup
        var title = "{/literal}{#str_LabelShareProject#}{literal}";
        document.getElementById('shareProjectTitle').innerHTML = title.replace('^0', pProjectName);

        var dialogBox = document.getElementById('dialogBox');
		var dialogContentContainer = getChildElementByClass('dialogContentContainer', dialogBox);
		var dialogContent = document.getElementById('shareMethodsTitle');
        var shimObj = document.getElementById('shim');
		var windowHeight = document.documentElement.clientHeight;

		// reset dialog sizes
		dialogBox.style.height = '';
		dialogBox.style.width = '';
		dialogContentContainer.style.height = '';

        if (dialogBox && shimObj)
        {
            shimObj.style.display = 'block';
            shimObj.style.height = document.body.offsetHeight + 'px';

			dialogBox.style.maxHeight = '';
			dialogBox.style.height = '';
			dialogBox.style.display = 'block';

			if (dialogBox.offsetHeight > windowHeight)
			{
				// dialog is bigger than browser window height so size it down and scroll content

				dialogBox.style.maxHeight = windowHeight + 'px';

				// calculate required height for the container
				var dialogTopObj = getChildElementByClass('dialogTop', dialogBox);
				var dialogButtonObj = getChildElementByClass('buttonShare', dialogBox);
				var dialogContentObj = document.getElementById('shareMethodsTitle');
				var dialogContentObjStyle = dialogContent.currentStyle || window.getComputedStyle(dialogContent);
				var dialogContentObjMargin = (parseInt(dialogContentObjStyle.marginTop) + parseInt(dialogContentObjStyle.marginBottom)) * 2;
				dialogContentContainer.style.height = (dialogBox.offsetHeight - dialogTopObj.offsetHeight - dialogButtonObj.offsetHeight - dialogContentObjMargin) + 'px';
				dialogContentContainer.scrollTop = 0;

				// increase the width of the dialog box in case we need scrollbars
				// to prevent scrollbars breaking the layout on Windows
				var scrollbarWidth = dialogContentContainer.offsetWidth - dialogContentContainer.clientWidth;
				dialogBox.style.width = (parseInt(dialogBox.offsetWidth, 10) + (scrollbarWidth)) + 'px';
			}

			dialogBox.style.display = 'block';
			dialogBox.style.left = Math.round(shimObj.offsetWidth / 2 - dialogBox.offsetWidth/2) + 'px';
			dialogBox.style.top = Math.round((windowHeight - dialogBox.offsetHeight) / 2) + 'px';
        }

        /* reset form */
        document.getElementById('popupBox2Form').reset();
        document.getElementById('shareMethodsSocial').checked = true;
        changeShareMethod();

        /* disabled password protection*/
        document.getElementById('sharepassword').checked = false;
        document.getElementById('previewPassword').setAttribute("disabled","disabled");
        document.getElementById('previewPassword').value = '';
        document.getElementById('previewPasswordcompulsory2').style.display = "none";
        document.getElementById('togglepreviewpassword').style.display = "none";

		/* remove highlight */
		unhighlight('previewPassword');
		unhighlight('shareByEmailTitle');
		unhighlight('shareByEmailTo');
    }

	function getChildElementByClass(pClassName, pParent)
	{
		if (document.querySelectorAll)
		{
			return pParent.querySelectorAll('.' + pClassName)[0];
		}
		else
		{
			var childNodes = pParent.childNodes;
			var childNodesLength = childNodes.length;

			for (var i = 0; i < childNodesLength; i++)
			{
				if (childNodes[i].className.indexOf(pClassName) != -1)
				{
					return childNodes[i];
				}
			}
		}
	}

    function shareByEmail()
    {
        gAlerts = 0;
        var messageError = "{/literal}{#str_ErrorCompulsoryInformationMissing#}{literal}";
        if (!checkPassword())
        {
            messageError +="\n"  + "{/literal}{#str_MessagePasswordInformation#}{literal}";
        }
        var emailTitle = document.getElementById('shareByEmailTitle').value;
        var emailRecipients = document.getElementById('shareByEmailTo').value;
        var shareByEmailText = document.getElementById('shareByEmailText').value;
        if (emailTitle == '')
        {
            messageError += "\n"  + "{/literal}{#str_MessageEnterMessageTitle#}{literal}";
            highlight("shareByEmailTitle");

        }
        if (emailRecipients == '')
        {
            messageError += "\n"  + "{/literal}{#str_MessageEnterAtLeastOneEmail#}{literal}";
            highlight("shareByEmailTo");
        }
        else
        {
            var emailAddressArray = new Array();
            var emailddressIsValid = true;
            emailAddressArray = emailRecipients.split(',');
            for (i = 0; i < emailAddressArray.length; i++)
            {
                recipient = emailAddressArray[i].replace(" ", "");
                emailddressIsValid = validateEmailAddress(recipient);
                if (!emailddressIsValid)
                {
                    messageError += "\n"  + "{/literal}{#str_MessageInvalidEmail#}{literal}";
                    document.getElementById('shareByEmailTo').focus();
                    highlight("shareByEmailTo");
                }
            }
        }

        if (gAlerts == 0)
        {
            var confirmationBoxObj = document.getElementById('confirmationBox');
            var confirmationBoxTextObj = document.getElementById('confirmationBoxText');
            var shimObj = document.getElementById('shim');

            confirmationBoxObj.style.display = 'block';
            confirmationBoxTextObj.style.display = 'block';

            if (shimObj)
            {
                shimObj.style.zIndex = 250;
            }

            confirmationBoxObj.style.left = Math.round(shimObj.offsetWidth / 2 - confirmationBoxObj.offsetWidth/2) + 'px';

            var windowHeight = document.documentElement.clientHeight;
            confirmationBoxObj.style.top = Math.round((windowHeight - confirmationBoxObj.offsetHeight) / 2) + 'px';

            confirmationBoxTextObj.className = confirmationBoxTextObj.className.replace(' confirmationText', '') + ' confirmationText';
            confirmationBoxTextObj.innerHTML = '<img src="{/literal}{$webroot}{literal}/images/loading_shoppingcart_v2.gif" class="loading-icon" alt="{/literal}{#str_MessageLoading#}{literal}" />' +  "&nbsp;{/literal}{#str_MessageSendingEmail#}{literal}";

            var buttonsHolderConfirmationObj = document.getElementById('buttonsHolderConfirmation');
            var buttonsHolderQuestionObj = document.getElementById('buttonsHolderQuestion');
            if (buttonsHolderConfirmationObj)
            {
                buttonsHolderConfirmationObj.style.display = 'none';
            }
            if (buttonsHolderQuestionObj)
            {
                buttonsHolderQuestionObj.style.display = 'none';
            }

			var format = ((document.location.protocol != 'https:') ? 1 : 0);

            var previewPasswordValue = '';
            if (document.getElementById('sharepassword').checked)
            {
                var previewPasswordObj = document.getElementById('previewPassword');
                if ((previewPasswordObj) && (previewPasswordObj.value != ''))
                {
					if (format == 1)
					{
						previewPasswordValue = hex_md5(previewPasswordObj.value);
					}
					else
					{
						previewPasswordValue = previewPasswordObj.value;
					}
                }
            }



        {/literal}

            {if $sharebyemailmethod == 1}

                {literal}

                    // email send by control center
					processAjax("shareByEmail", '/', 'POST', "fsaction=Share.shareByEmail&orderItemId="+ orderItemID + '&title='+encodeURIComponent(emailTitle) + '&recipients='+encodeURIComponent(emailRecipients) + '&message='+encodeURIComponent(shareByEmailText) + '&previewPassword='+encodeURIComponent(previewPasswordValue) + '&format=' + format, true);

                {/literal}

            {else}

                {literal}

                    // mailto link
					processAjax("mailToLink", '/', 'POST', "fsaction=Share.mailTo&orderItemId="+ orderItemID + '&title='+encodeURIComponent(emailTitle) + '&recipients='+encodeURIComponent(emailRecipients) + '&message='+encodeURIComponent(shareByEmailText) + '&previewPassword='+encodeURIComponent(previewPasswordValue) + '&format=' + format, true);

                {/literal}

            {/if}

        {literal}
        }
        else
        {
            alert(messageError);
        }
    }

    function passwordDisplay()
    {
        if (document.getElementById('sharepassword').checked)
        {
            document.getElementById('previewPassword').disabled = false;
            document.getElementById('previewPasswordcompulsory2').style.display = "inline-block";
            document.getElementById('togglepreviewpassword').style.display = "block";
            
        }
        else
        {
            document.getElementById('previewPassword').setAttribute("disabled","disabled");
            document.getElementById('previewPassword').value = '';
            document.getElementById('previewPasswordcompulsory2').style.display = "none";
            document.getElementById('togglepreviewpassword').style.display = "none";
            document.getElementById('previewPassword').className = document.getElementById('previewPassword').className.replace(" errorInput", "");
        }
    }

    function executeButtonAction(obj, pOrderItemID, windowTarget, pProjectName, pApplicationName, pProjectRef, pWorkflowType, pProductIndent)
    {
        orderItemID = pOrderItemID;

        if (windowTarget == 1) /* reorder */
        {
            productReorder();
        }
        else if (windowTarget == 2) /* unshare */
        {
            unshareObjLink = obj;
            productUnshare();
        }
		else if (windowTarget == 3) /* continue editing */
        {
			document.getElementById('projectrefhidden').value = pProjectRef;
			document.getElementById('projectworkflowtype').value = pWorkflowType;
			document.getElementById('productindent').value = pProductIndent;

            onlineProjectsButtonAction('continueediting');
        }
		else if (windowTarget == 4) /* duplicate */
        {
			var projectName = document.getElementById('orderitemid' + orderItemID).getAttribute('data-projectname');
			document.getElementById('projectrefhidden').value = pProjectRef;
			document.getElementById('projectnamehidden').value = projectName;
			document.getElementById('productindent').value = pProductIndent;

            onlineProjectsButtonAction('duplicateproject');
        }
        else
        {
			/* share */
            if (addToAnyIntialised == false)
            {
                reinitAddtoany();
            }

            var shimObj = document.getElementById('shim');
            /* get product details */
            if (shimObj)
            {
                shimObj.style.height = document.body.offsetHeight + 'px';
                document.body.className +=' hideSelects';
				shimObj.style.zIndex = 200;
                shimObj.style.display = 'block';
            }

            shareObjLink = obj;
            shareName = pProjectName + ' (' + pApplicationName + ')';

            openDialogBoxShareProject(pProjectName);
        }

        return false;
    }

	function removeDeletedProject(pProjectref, pDisplayMessage)
	{
		closeDialogBoxOnlineAction();
		hideLoadingDialog();

		if (pDisplayMessage)
		{
			var shimObj = document.getElementById('shim');
			shimObj.style.zIndex = 200;
			showConfirmationBox("{/literal}{#str_ErrorProjectHasBeenDeleted#}{literal}");
		}

		var selectedProject = document.getElementById(pProjectref);
		selectedProject.parentNode.removeChild(selectedProject);
	}


        {/literal}

    {else} {* {if $section == 'yourorders'} *}

        {literal}

    function closeConfirmationBox()
    {
        var shimObj = document.getElementById('shim');
        var confirmationBoxObj = document.getElementById('confirmationBox');
        if (confirmationBoxObj)
        {
			if (shimObj.style.zIndex == 200)
			{
				shimObj.style.display = 'none';
			}
			else
			{
				// reset the ShimObj's zIndex back to original when user close any box
				shimObj.style.zIndex = 200;
			}

            confirmationBoxObj.style.display = 'none';
        }

		if (processingAjax === true)
		{
			processingAjax = false;
		}
    }

        {/literal}

    {/if} {* end {if $section == 'yourorders'} *}

    /* END YOUR ORDERS */

    /* OPEN EXISTING PROJECT */

    {if $section == 'existingonlineprojects'}

        {literal}

    function calcualteScrollableView()
    {
        var windowHeight  = window.innerHeight;
        var existingOnlineProjectView = document.getElementById('existingOnlineProjectList');

        // 340 is the total height of elements above & below the project list.
        newHeight = windowHeight - 340 ;

        if (newHeight > 320)
        {
           existingOnlineProjectView.style.height = newHeight + "px";
        }

		var keepProjectLinks = getChildElementsByClass('keepProjectLink', existingOnlineProjectView);
		if (keepProjectLinks.length > 0) {
			keepProjectLinks.forEach(function(item) {
				item.addEventListener('click', function(event) {
					keepOnlineProject(event.target.getAttribute('data-projectref'));
				});
			});
		}

		var purgeAll = document.getElementById('purgeAllLink');
		if (purgeAll !== undefined && purgeAll !== null) {
			purgeAll.addEventListener('click', function(event) {
				purgeFlaggedProjects();
			});
		}
    }

    function selectProject(pSelectedProject)
    {
        var projectRef = pSelectedProject.id;
        var projectName = document.getElementById(projectRef).getAttribute("data-projectname");
        var projectWorkflowType = document.getElementById(projectRef).getAttribute("data-workflowtype");
        var productIndent = document.getElementById(projectRef).getAttribute("data-productident");
        var projectStatus = document.getElementById(projectRef).getAttribute("data-projectstatus");

        document.getElementById('projectnamehidden').value = projectName;
        document.getElementById('projectrefhidden').value = projectRef;
        document.getElementById('projectworkflowtype').value = projectWorkflowType;
        document.getElementById('productindent').value = productIndent;
        document.getElementById('projectstatus').value = projectStatus;

        if (document.getElementsByClassName)
        {
            var projects = document.getElementsByClassName('selectedRow');
            for (var i = 0; i < projects.length; i++ )
            {
                projects[i].className = 'contentRow';
            }
        }
        else
        {
            i = 0;
            var existingOnlineProjectList = document.getElementById('existingOnlineProjectList');
            aInput = existingOnlineProjectList.getElementsByTagName("div");
            while (element = aInput[i++])
            {
                if (element.className == 'contentRow selectedRow')
                {
                    element.className = 'contentRow';
                }
            }
        }

        setActiveButtonsFromStatus(pSelectedProject.getAttribute("data-canedit"), pSelectedProject.getAttribute("data-candelete"), pSelectedProject.getAttribute("data-cancompleteorder"));

        document.getElementById('renameBtn').onclick = function()
        {
            onlineProjectsButtonAction('rename');
        };
        document.getElementById('renameBtnLeft').className = 'btn-white-left';
        document.getElementById('renameBtnMiddle').className = 'btn-white-middle btnOnlineMiddle';
        document.getElementById('renameBtnRight').className = 'btn-white-right';

        document.getElementById('duplicateBtn').onclick = function()
        {
            onlineProjectsButtonAction('duplicateproject');
        };
        document.getElementById('duplicateBtnLeft').className = 'btn-white-left';
        document.getElementById('duplicateBtnMiddle').className = 'btn-white-middle btnOnlineMiddle';
        document.getElementById('duplicateBtnRight').className = 'btn-white-right';

        pSelectedProject.className = 'contentRow selectedRow';
    }

	function setActiveButtonsFromStatus(pCaneEdit, pCanDelete, pCancompleteOrder)
	{
        var shareButtonClass = {
            shareBtnLeft: 'btn-disabled-left',
            shareBtnMiddle: 'btn-disabled-middle btnOnlineMiddle',
            shareBtnRight: 'btn-disabled-right'
        };
        
        var shareFn = '';

        if (pCaneEdit == 0)
        {
            document.getElementById('editBtn').onclick = '';
            document.getElementById('editBtnLeft').className = 'btn-disabled-left';
            document.getElementById('editBtnMiddle').className = 'btn-disabled-middle btnOnlineMiddle';
            document.getElementById('editBtnRight').className = 'btn-disabled-right';
        }
        else
        {
            document.getElementById('editBtn').onclick = function()
            {
                onlineProjectsButtonAction('continueediting');
            }
            document.getElementById('editBtnLeft').className = 'btn-blue-left';
            document.getElementById('editBtnMiddle').className = 'btn-blue-middle btnOnlineMiddle';
            document.getElementById('editBtnRight').className = 'btn-blue-right';

            // use pCanEdit to designate whether a project can be shared.
            
            shareButtonClass = {
                shareBtnLeft: 'btn-white-left',
                shareBtnMiddle: 'btn-white-middle btnOnlineMiddle',
                shareBtnRight: 'btn-white-right'
            };
            
            shareFn = function()
            {
                onlineProjectsButtonAction('share');
            };
        }

        buildButton(
            {	id: 'shareBtn',
                fn: shareFn,
                appliedClasses: shareButtonClass
            });

		if (pCanDelete == 0)
        {
            document.getElementById('deleteBtn').onclick = '';
            document.getElementById('deleteBtnLeft').className = 'btn-disabled-left';
            document.getElementById('deleteBtnMiddle').className = 'btn-disabled-middle btnOnlineMiddle';
            document.getElementById('deleteBtnRight').className = 'btn-disabled-right';
        }
        else
        {
            document.getElementById('deleteBtn').onclick = function()
            {
                onlineProjectsButtonAction('delete');
            }
            document.getElementById('deleteBtnLeft').className = 'btn-white-left';
            document.getElementById('deleteBtnMiddle').className = 'btn-white-middle deleteBtnText btnOnlineMiddle';
            document.getElementById('deleteBtnRight').className = 'btn-white-right';
        }

		if (pCancompleteOrder == 0)
        {
            document.getElementById('completeBtn').onclick = '';
            document.getElementById('completeBtnLeft').className = 'btn-disabled-left';
            document.getElementById('completeBtnMiddle').className = 'btn-disabled-middle btnOnlineMiddle';
            document.getElementById('completeBtnRight').className = 'btn-disabled-right';
        }
        else
        {
            document.getElementById('completeBtn').onclick = function()
            {
                onlineProjectsButtonAction('completeorder');
            }
            document.getElementById('completeBtnLeft').className = 'btn-green-left';
            document.getElementById('completeBtnMiddle').className = 'btn-green-middle btnOnlineMiddle';
            document.getElementById('completeBtnRight').className = 'btn-green-right';
        }

	}

	/**
	 * Build the action buttons -- Started moving this common functionality into a separate function so that
	 * any modifications are done in a single location.
	 * @param pDetails ({id: string, fn: function, appliedClasses: object)}
	 */
	function buildButton(pDetails)
	{
		document.getElementById(pDetails.id).onclick = pDetails.fn;
		var appliedClasses = Object.keys(pDetails.appliedClasses);
		for (var i = 0, iLength = appliedClasses.length; i < iLength; i++)
		{
			document.getElementById(appliedClasses[i]).className = pDetails.appliedClasses[appliedClasses[i]];
		}
	}

	function removeDeletedProject(pProjectref, pDisplayMessage)
	{
		closeDialogBoxOnlineAction();

		if (pDisplayMessage)
		{
			var shimObj = document.getElementById('shim');
			shimObj.style.zIndex = 200;
			showConfirmationBox("{/literal}{#str_ErrorProjectHasBeenDeleted#}{literal}");
		}

		var selectedProject = document.getElementById(pProjectref);
		selectedProject.parentNode.removeChild(selectedProject);

		// disabled buttons
		document.getElementById('completeBtn').onclick = '';
		document.getElementById('completeBtnLeft').className = 'btn-disabled-left';
		document.getElementById('completeBtnMiddle').className = 'btn-disabled-middle btnOnlineMiddle';
		document.getElementById('completeBtnRight').className = 'btn-disabled-right';

		document.getElementById('editBtn').onclick = '';
		document.getElementById('editBtnLeft').className = 'btn-disabled-left';
		document.getElementById('editBtnMiddle').className = 'btn-disabled-middle btnOnlineMiddle';
		document.getElementById('editBtnRight').className = 'btn-disabled-right';

		document.getElementById('deleteBtn').onclick = '';
		document.getElementById('deleteBtnLeft').className = 'btn-disabled-left';
		document.getElementById('deleteBtnMiddle').className = 'btn-disabled-middle btnOnlineMiddle';
		document.getElementById('deleteBtnRight').className = 'btn-disabled-right';

		document.getElementById('renameBtn').onclick = '';
		document.getElementById('renameBtnLeft').className = 'btn-disabled-left';
		document.getElementById('renameBtnMiddle').className = 'btn-disabled-middle btnOnlineMiddle';
		document.getElementById('renameBtnRight').className = 'btn-disabled-right';

		document.getElementById('shareBtn').onclick = '';
		document.getElementById('shareBtnLeft').className = 'btn-disabled-left';
		document.getElementById('shareBtnMiddle').className = 'btn-disabled-middle btnOnlineMiddle';
		document.getElementById('shareBtnRight').className = 'btn-disabled-right';


		document.getElementById('duplicateBtn').onclick = '';
		document.getElementById('duplicateBtnLeft').className = 'btn-disabled-left';
		document.getElementById('duplicateBtnMiddle').className = 'btn-disabled-middle btnOnlineMiddle';
		document.getElementById('duplicateBtnRight').className = 'btn-disabled-right';

		// display no online projects message if there are none
		var existingOnlineProject = document.getElementById('existingOnlineProjectList');
		if ((existingOnlineProject !== null) && (document.getElementById('existingOnlineProjectList').children.length === 0))
		{
			existingOnlineProject.style.display = 'none';

			var emptyBoxContainer = document.createElement('div');
			emptyBoxContainer.className = 'emptyBox';
			emptyBoxContainer.innerHTML = "{/literal}{#str_LabelNoOnlineProject#}{literal}";

			document.getElementById('content').appendChild(emptyBoxContainer);

			// hide action buttons
			var onlineProjectsButtonContainer = document.getElementsByClassName('onlineproject_btnLinks');
			if (onlineProjectsButtonContainer.length === 1)
			{
				onlineProjectsButtonContainer[0].style.display = 'none';
			}
		}
	}


    function displayDeleteProjectPrompt(pForceQuit)
    {
        var projectName = document.getElementById('projectnamehidden').value;

		var confirmDeleteMessage = "{/literal}{#str_MessageDeleteProjectConfirmation#}{literal}";
		document.getElementById('projectname_container').innerHTML = '<div class="confirmationText" style="display: block;">' + confirmDeleteMessage.replace('^0', "'" + projectName + "'"); + '</div>';
		document.getElementById('projectacceptbutton').onclick = function(){
			checkDeleteSession(0, 'delete');
		};
		document.getElementById('projectacceptbutton').innerHTML = '<div class="btn-green-left" ></div><div class="btn-green-middle" >' + "{/literal}{#str_ButtonYes#}{literal}" + '</div><div class="btn-accept-right"></div>';
		document.getElementById('projectcancelbutton').innerHTML = '<div class="btn-red-cross-left" ></div><div class="btn-red-middle">' + "{/literal}{#str_ButtonNo#}{literal}" + '</div><div class="btn-red-right"></div>';
    }

	/**
	 * Show the share project dialog on large screen
	 * @param pShareURL
	 */
	function showShareProjectDialogLarge(pShareURL)
	{
		var shareProjectDialog = new TPXSimpleDialog(
		{
			title : "{/literal}{#str_ButtonShareProject#}{literal}",
			content: function() {
				this.clearContent();
				var container = document.createElement('div');
				var shareProjectDialogHTML = '' +
					'<div id="sharelink-tip" class="tip-popout">' +
						'<p>{/literal}{#str_ToolTipLinkCopied#}{literal}</p>' +
					'</div>' +
					'<div class="sharelink_link_container clearfix">' +
						'<label for="sharelink-url">{/literal}{#str_LabelShareLink#}{literal}</label>' +
						'<input type="text" name="sharelink-url" value="' + pShareURL + '" id="sharelink-url" maxlength="75" readonly="readonly"/>' +
					'</div>';

				container.innerHTML = shareProjectDialogHTML;
				return container;
			},
			buttons:
			{
				left:
				{
					text: '{/literal}{#str_ButtonClose#}{literal}',
					action: function() {
						return false;
					}
				},
				right:
				{
					text: '{/literal}{#str_ButtonCopyLink#}{literal}',
					action: function(event){
						// set preventDefault() on event to prevent TPXSimpleDialog default behaviour
						event.preventDefault();
						handleCopyLinkClick();
					}
				}
			}
		});

		shareProjectDialog.show();
		modifyDialogButtonStyles();

		// Apply some custom styles to the buttons
		function modifyDialogButtonStyles()
		{
			var rightButtonAcceptNode =  document.querySelector('#modalButtonRight > .btn-accept-right');
			if (rightButtonAcceptNode)
			{
				rightButtonAcceptNode.classList.remove('btn-accept-right');
				rightButtonAcceptNode.classList.add('btn-green-right');
			}

			if (!getSupportsExecCommand())
			{
				document.querySelector('#modalButtonRight').style.display = 'none';
				document.querySelector('#sharelink-url').removeAttribute('readonly');
			}

		}

		function handleCopyLinkClick()
		{
			copyValueToClipboard('sharelink-url');
			flashTooltip('sharelink-tip','tip-popout-visible');
		}
	}

    function renameExistingOnlineProject()
    {
        var projectName = document.getElementById('projectname').value;
        var projectRef = document.getElementById('projectrefhidden').value;

        projectName = correctFileName(projectName);
        document.getElementById('projectname').value = projectName;

        if (projectName != '')
        {
            showLoadingDialog("{/literal}{#str_TitleRenamingProject#}{literal}");
			processAjax("renameonlineproject", ".?fsaction=AjaxAPI.callback&cmd=RENAMEONLINEPROJECT&projectref=" + projectRef + '&projectname=' + encodeURIComponent(projectName), 'POST', '', true);
        }
        else
        {
            var shimObj = document.getElementById('shim');
            shimObj.style.zIndex = 201;
            showConfirmationBox("{/literal}{#str_ErrorNoProjectName#}{literal}");
        }
    }

	function completeOrder()
	{
		var projectRef = document.getElementById('projectrefhidden').value;
		var workflowType = document.getElementById('projectworkflowtype').value;
		var productIndent = document.getElementById('productindent').value;
		processAjax("completeorder", ".?fsaction=AjaxAPI.callback&cmd=COMPLETEORDER&projectref=" + projectRef + '&workflowtype=' + workflowType + '&productindent=' + productIndent, 'POST', '', true);
	}

		function keepOnlineProject(projectRef)
		{
			var dialog = new TPXSimpleDialog({
				title : "{/literal}{#str_TitleKeepProject#}{literal}",
				content: function() {
					this.clearContent();
					var container = document.createElement('div');
					var dialogHTML = "<div>{/literal}{#str_MessageKeepProjectWarningMessage#}{literal}</div>";
					container.innerHTML = dialogHTML;
					return container;
				},
				buttons: {
					left: {
						text: "{/literal}{#str_ButtonCancel#}{literal}",
						action: function() {
							return false;
						}
					},
					right: {
						text: "{/literal}{#str_TitleKeepProject#}{literal}",
						action: function(event) {
							this.close();
							// set preventDefault() on event to prevent TPXSimpleDialog default behaviour
							event.preventDefault();
							showLoadingDialog("{/literal}{#str_TitleKeepingProject#}{literal}");
							processAjax("keeponlineproject", ".?fsaction=AjaxAPI.callback&cmd=KEEPONLINEPROJECT&projectref=" + projectRef, 'POST', '', true);
						},
						classes: [
							'btn-green-left',
							'btn-green-middle',
							'btn-green-right'
						]
					}
				}
			}).show();
		}

		function purgeFlaggedProjects()
		{
			var dialog = new TPXSimpleDialog({
				title : "{/literal}{#str_TitlePleaseConfirm#}{literal}",
				content: function() {
					this.clearContent();
					var container = document.createElement('div');
					var dialogHTML = "<div>{/literal}{#str_MessageConfirmPurgeProjectsDeletionMessage#}{literal}</div>";
					container.innerHTML = dialogHTML;
					return container;
				},
				buttons: {
					left: {
						text: "{/literal}{#str_ButtonCancel#}{literal}",
						action: function() {
							return false;
						}
					},
					right: {
						text: "{/literal}{#str_ButtonDeleteNow#}{literal}",
						classes: [
							'btn-red-left',
							'btn-red-middle',
							'btn-red-right'
						],
						action: function(event) {
							this.close();
							// set preventDefault() on event to prevent TPXSimpleDialog default behaviour
							event.preventDefault();
							showLoadingDialog("{/literal}{#str_MessagePleaseWait#}{literal}");
							processAjax("purgeflaggedprojects", ".?fsaction=AjaxAPI.callback&cmd=PURGEFLAGGEDPROJECTS", 'POST', '', true);
						}
					}
				}
			}).show();
		}
        {/literal}

    {/if}

    /* END OPEN EXISTING PROJECT */

    {* END LARGE SPECIFIC FUNCTION *}

{/if} {* end {if $issmallscreen == 'true'} *}
