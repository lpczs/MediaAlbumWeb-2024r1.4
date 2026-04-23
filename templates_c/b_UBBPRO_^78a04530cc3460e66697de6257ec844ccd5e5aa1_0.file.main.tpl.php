<?php
/* Smarty version 4.5.3, created on 2026-03-12 10:50:57
  from 'C:\TAOPIX\MediaAlbumWeb\templates\customer\main.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b29a91263ba4_53370169',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '78a04530cc3460e66697de6257ec844ccd5e5aa1' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\customer\\main.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b29a91263ba4_53370169 (Smarty_Internal_Template $_smarty_tpl) {
?> 

var session = "<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
";
var gSession = "<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
";
var gIsMobile = "<?php echo $_smarty_tpl->tpl_vars['issmallscreen']->value;?>
";
var ssotoken = "<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
";
var gSSOToken = "<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
";
var unshareArray = '';
var gAlerts = 0;
var addressUpdated = "<?php echo $_smarty_tpl->tpl_vars['addressupdated']->value;?>
";
var countryChanged = false;
var addToAnyIntialised = false;
var processingAjax = false;
var isHighLevel = <?php echo $_smarty_tpl->tpl_vars['ishighlevel']->value;?>
;
var basketRef = "<?php echo $_smarty_tpl->tpl_vars['basketref']->value;?>
";
var languageCode = "<?php echo $_smarty_tpl->tpl_vars['languagecode']->value;?>
";


/* ACCOUNT DETAILS */


<?php if ($_smarty_tpl->tpl_vars['section']->value == 'accountdetails') {?>

    

var gAs_jsonCity = '';
var gAs_jsonCounty = '';
var firstname = "<?php echo $_smarty_tpl->tpl_vars['contactfname']->value;?>
";
var lastname = "<?php echo $_smarty_tpl->tpl_vars['contactlname']->value;?>
";
var company = "<?php echo $_smarty_tpl->tpl_vars['companyname']->value;?>
";
var add1 = "<?php echo $_smarty_tpl->tpl_vars['address1']->value;?>
";
var add2 = "<?php echo $_smarty_tpl->tpl_vars['address2']->value;?>
";
var add3 = "<?php echo $_smarty_tpl->tpl_vars['address3']->value;?>
";
var add4 = "<?php echo $_smarty_tpl->tpl_vars['address4']->value;?>
";
var add41 = "<?php echo $_smarty_tpl->tpl_vars['add41']->value;?>
";
var add42 = "<?php echo $_smarty_tpl->tpl_vars['add42']->value;?>
";
var add43 = "<?php echo $_smarty_tpl->tpl_vars['add43']->value;?>
";
var city = "<?php echo $_smarty_tpl->tpl_vars['city']->value;?>
";
var county = "<?php echo $_smarty_tpl->tpl_vars['county']->value;?>
";
var state = "<?php echo $_smarty_tpl->tpl_vars['state']->value;?>
";
var regioncode = "<?php echo $_smarty_tpl->tpl_vars['regioncode']->value;?>
";
var region = "";
var postcode = "<?php echo $_smarty_tpl->tpl_vars['postcode']->value;?>
";
var country = "<?php echo $_smarty_tpl->tpl_vars['country']->value;?>
";
var countryName = "<?php echo $_smarty_tpl->tpl_vars['countryname']->value;?>
";
var registeredtaxnumbertype = "<?php echo $_smarty_tpl->tpl_vars['registeredtaxnumbertype']->value;?>
";
var registeredtaxnumber = "<?php echo $_smarty_tpl->tpl_vars['registeredtaxnumber']->value;?>
";
var TPX_REGISTEREDTAXNUMBERTYPE_NA = "<?php echo $_smarty_tpl->tpl_vars['TPX_REGISTEREDTAXNUMBERTYPE_NA']->value;?>
";
var TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL = "<?php echo $_smarty_tpl->tpl_vars['TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL']->value;?>
";
var TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE = "<?php echo $_smarty_tpl->tpl_vars['TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE']->value;?>
";
var lastSuccesfulCountry = country;
var originalEmail = "<?php echo $_smarty_tpl->tpl_vars['email']->value;?>
";
var isCustomerAuthEnabled = "<?php echo $_smarty_tpl->tpl_vars['customerupdateauthrequired']->value;?>
";

    

<?php }?>



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
				showLoadingDialog('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageLoading');?>
')
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
                        showVerificationFailedMessage("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorLoginHasExpired');?>
", function() {
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
								confirmationBoxTextObj.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SuccessUnshare');?>
";
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
							confirmationBoxTextObj.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorConnectFailure');?>
";
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
								confirmationBoxTextObj.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageEmailSent');?>
";

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
							confirmationBoxTextObj.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorConnectFailure');?>
";
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
								confirmationBoxTextObj.innerHTML = nlToBr("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCheckEmailSoftware');?>
");

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
							confirmationBoxTextObj.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorConnectFailure');?>
";
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

                            showConfirmationBox("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorMaintenanceMode');?>
");
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

						

						<?php if ($_smarty_tpl->tpl_vars['section']->value != 'yourorders') {?> 
							

						var shimObj = document.getElementById('shim');
						shimObj.style.zIndex = 200;

							

						<?php }?> 
						

						showConfirmationBox("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorConnectFailure');?>
");
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

                            showConfirmationBox("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorMaintenanceMode');?>
");
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

                    		showConfirmationBox("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorMaintenanceMode');?>
");

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

                    		showConfirmationBox("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorMaintenanceMode');?>
");

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

                    		showConfirmationBox("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorMaintenanceMode');?>
");

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
						 <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>
						hideLoadingDialog();
						<?php }?>

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
	 					
	 					<?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>
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
	 					<?php } else { ?>
						 /* pass localised timestamp */
						 var timestamp = getTimestamp();

						 document.submitform.tzoffset.value = timestamp;
						 document.submitform.fsaction.value = 'Customer.yourOrders';
						 document.submitform.submit();
						 return false;
						 <?php }?>
						 
	 				}
	 				else
	 				{
					 	// Close the open dialog as we will be showing another here.
					 	closeDialog();
					 	
						showErrorDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleUnableToPerformRequest');?>
", "<p><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorOrderInProduction');?>
</p><p><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePleaseTryAgain');?>
</p>", function(e) {
							closeDialog();
							return false;
						});
					 	
	 				}
	 				break;
				case 'keeponlineproject':
					// Keep the selected project.
					window.clearTimeout(timeoutID);
					
					<?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>
						closeLoadingDialog();
					<?php } else { ?>
						hideLoadingDialog();
					<?php }?>
					
					var response = parseJson(xmlhttp.responseText);

					if (response.status)
					{
						// Project is kept successfully remove the details saying it's going to be purged.
						var purgeClassName = 'dateofpurge';
						var isSmallScreen = false;
						
						<?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>
						isSmallScreen = true;
						var purgeMessageContainer = document.getElementById('contentContainer');
						var listContainer = document.getElementById('contentExistingProject');
						var projectContainer = document.getElementById('contentItemBloc' + response.projectref);
						<?php } else { ?>
						var purgeMessageContainer = document.getElementById('page');
						var listContainer = document.getElementById('existingOnlineProjectList');
						var projectContainer = document.getElementById(response.projectref);
						<?php }?>
						
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
						
						showErrorDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleUnableToPerformRequest');?>
", "<p><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePleaseTryAgain');?>
</p>", function(e) {
							closeDialog();
							return false;
						});
						
					}
					break;
				case 'purgeflaggedprojects':
					// Purge all flagged projects.
					window.clearTimeout(timeoutID);
					var isSmallScreen = false;
					
					<?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>
						isSmallScreen = true;
						closeLoadingDialog();
					<?php } else { ?>
						hideLoadingDialog();
					<?php }?>
 					
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
					
 						showErrorDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleUnableToPerformRequest');?>
", "<p><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePleaseTryAgain');?>
</p>", function(e) {
					 		closeDialog();
					 		return false;
					 	});
					
					}
					break;
				default:
					// disable the loading screen event if it has not yet fired off. clearTimeout does not cause errors if called on an expired or
					// non existent timeoutID so no checks are needed surrounding it
					window.clearTimeout(timeoutID);
					
										<?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>
							closeLoadingDialog();
					<?php } else { ?>
							hideLoadingDialog();
					<?php }?>
					

					var formWrapper = '<form id="mainform" name="mainform" action="#">' + xmlhttp.responseText + '</form>';
					document.getElementById(obj).innerHTML = formWrapper;

					restoreFields();

					<?php if ($_smarty_tpl->tpl_vars['autosuggestavailable']->value == 1) {?>
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
                <?php }?>

                <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

                    

                setScrollAreaHeight('contentRightScrollForm', 'contentNavigationForm');

                    

                <?php }?> 

                				
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

			
			<?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>
				closeLoadingDialog();
			<?php } else { ?>
				hideLoadingDialog();
			<?php }?>
			alert('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorCommunicationFailedPleaseTryAgain');?>
');
        	
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





<?php if ($_smarty_tpl->tpl_vars['section']->value == 'accountdetails') {?>

    

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
				showVerificationFailedMessage("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoPassword');?>
");
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
		title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_Error');?>
",
		content: "<p>" + message + "</p>",
		buttons: {
			right: {
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonOk');?>
",
				action: callback
			}
		}
	}).show();
}

function showVerifyAccountDialog()
{
	var dialog = new TPXSimpleDialog({
		'title' : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleConfirmChanges');?>
",
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
			label.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRenterPassword');?>
";
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
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonConfirm');?>
",
				action: function() {
					verifyAccount()
				}
			},
			left: {
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
",
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

    

    <?php if ($_smarty_tpl->tpl_vars['autosuggestavailable']->value == 1) {?>

        

        processAjax("verify",".?fsaction=AjaxAPI.callback&cmd=ADDRESSVERIFICATION&city=" + city + "&county=" + county + "&statecode=" + regioncode +
        "&statevalue=" + state + "&postcode=" + postcode + "&region=" + region + "&country=" + country + "&addresstype=billing", 'POST', '', true);

        

    <?php } else { ?>

        

         updateAccountDetails('match');

        

    <?php }?>

    

    return false;
}

function changeState()
{
    saveFields();

    

    <?php if ($_smarty_tpl->tpl_vars['autosuggestavailable']->value == 1) {?>

        

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

        

    <?php }?>

    
}

    

<?php }?>

/* END ACCOUNT DETAILS */


/* ORDERS */

<?php if ($_smarty_tpl->tpl_vars['section']->value == 'yourorders') {?>

    

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



<?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

    

        closeLoadingDialog();

    

<?php }?>



    }

	function executePayNow(pSessionRef)
    {
		<?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>
        showLoadingDialog();
		<?php }?>

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

    

<?php }?> 


/* END ORDERS */

/* GENERIC */

function displayGiftCardAlert(giftCardResult, customMessage)
{
    var message = '';
    switch(giftCardResult)
    {
        case 'str_LabelGiftCardAccepted':
        {
            message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGiftCardAccepted');?>
";
            break;
        }
        case 'str_LabelGiftCardUsed':
        {
            message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGiftCardUsed');?>
";
            break;
        }
        case 'str_LabelInvalidGiftCard':
        {
            message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInvalidGiftCard');?>
";
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
                message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInvalidVoucher');?>
";
            }
            break;
        }
    }

    if (message != '')
    {


<?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>


        if (giftCardResult != 'str_LabelGiftCardAccepted')
        {
            createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", nlToBr(message), function(e) {
                closeDialog(e);
            });
        }


<?php } else { ?>


        alert(message);


<?php }?>


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

    var message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorCompulsoryInformationMissing');?>
";
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
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryFirstNameMandatory');?>
";
        }
    }
    if (document.getElementById('lastnamecompulsory'))
    {
        if (lastname.length == 0)
        {
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryLastNameMandatory');?>
";
            highlight("maincontactlname");
        }
    }
    if (document.getElementById('companycompulsory'))
    {
        if (company.length == 0)
        {
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryCompanyMandatory');?>
";
            highlight("maincompanyname");
        }
    }
    if (document.getElementById('add1compulsory'))
    {
        if (add1.length == 0)
        {
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryAdd1Mandatory');?>
";
            highlight("mainaddress1");
        }
    }
    if (document.getElementById('add2compulsory'))
    {
        if (add2.length == 0)
        {
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryAdd2Mandatory');?>
";
            highlight("mainaddress2");
        }
    }
    if (document.getElementById('add3compulsory'))
    {
        if (add3.length == 0)
        {
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryAdd3Mandatory');?>
";
            highlight("mainaddress3");
        }
    }
    if (document.getElementById('add4compulsory'))
    {
        if (add4.length == 0)
        {
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryAdd4Mandatory');?>
";
            highlight("mainaddress4");
        }
    }
    if (document.getElementById('add41compulsory'))
    {
        if (add41.length == 0)
        {
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryAdd1Mandatory');?>
";
            highlight("mainadd41");
        }
    }
    if (document.getElementById('add42compulsory'))
    {
        if (add42.length == 0)
        {
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryAdd42Mandatory');?>
";
            highlight("mainadd42");
        }
    }
    if (document.getElementById('add43compulsory'))
    {
        if (add43.length == 0)
        {
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryAdd3Mandatory');?>
";
            highlight("mainadd43");
        }
    }
    if (document.getElementById('citycompulsory') && document.getElementById('citycompulsory').src.indexOf("/images/asterisk.png") != -1)
    {
        if (city.length == 0)
        {
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryCityMandatory');?>
";
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
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryCountyMandatory');?>
";
            highlight("maincounty");
            highlight("countylist");
        }
    }
    if (document.getElementById('statecompulsory') && document.getElementById('statecompulsory').src.indexOf("/images/asterisk.png") != -1)
    {
        if (state.length == 0)
        {
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryStateMandatory');?>
";
            highlight("mainstate");
            highlight("statelist");
        }
    }
    if (document.getElementById('postcodecompulsory') && document.getElementById('postcodecompulsory').src.indexOf("/images/asterisk.png") != -1)
    {
        if (postcode.length == 0)
        {
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryPostCodeMandatory');?>
";
            highlight("mainpostcode");
        }
    }
    if (document.getElementById('telephonenumber_account').value.length == 0)
    {
        message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryPhoneMandatory');?>
";
        highlight("telephonenumber_account");
    }
    if (! validateEmailAddress(document.getElementById('email_account').value))
    {
        message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryEmaiInvalid');?>
";
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
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeSelection');?>
";
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
                message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageInvalidPersonalTaxNumber');?>
";
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
                message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryCorporateTaxNumberLength');?>
";
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
                message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageInvalidCorporateTaxNumber');?>
";
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
            var registeredTaxNumber = "<?php echo $_smarty_tpl->tpl_vars['registeredtaxnumber']->value;?>
";
            var registeredTaxNumberType = "<?php echo $_smarty_tpl->tpl_vars['registeredtaxnumbertype']->value;?>
";
        }
    }

    

    <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

        

    if (gAlerts > 0)
    {
        createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", nlToBr(message), function(e) {
            closeDialog(e);
        });
    }
    else
    {
        if (document.getElementById('email_account').value != "<?php echo $_smarty_tpl->tpl_vars['email']->value;?>
")
        {
            var messageText = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageEmailUpdateRequest');?>
";
            var emailUpdatedText = messageText.replace("^0", document.getElementById('email_account').value);
			emailUpdatedText += "<p class='delay-message-text'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ConfirmationResetMessage');?>
</p>";

            createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleChangeEmailAddress');?>
", emailUpdatedText, function(e) {
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

        

        <?php if ($_smarty_tpl->tpl_vars['edit']->value == 0) {?>

            

        postParams += '&countryname=' + encodeURIComponent(document.getElementById('countrylist').options[document.getElementById('countrylist').selectedIndex].text);

            

        <?php } else { ?>

            

        postParams += '&countryname=' + encodeURIComponent(countryName);
        postParams += '&countrycode=' + "<?php echo $_smarty_tpl->tpl_vars['country']->value;?>
";
        postParams += '&regioncode=' + "<?php echo $_smarty_tpl->tpl_vars['regioncode']->value;?>
";

            

        <?php }?>

        

        postParams += '&telephonenumber=' + encodeURIComponent(document.getElementById('telephonenumber_account').value);
        postParams += '&email=' + encodeURIComponent(document.getElementById('email_account').value);
        postParams += '&originalemail=' + encodeURIComponent("<?php echo $_smarty_tpl->tpl_vars['email']->value;?>
");
        postParams += '&registeredtaxnumbertype=' + encodeURIComponent(registeredTaxNumberType);
        postParams += '&registeredtaxnumber=' + encodeURIComponent(registeredTaxNumber);

		
			<?php if ($_smarty_tpl->tpl_vars['customerupdateauthrequired']->value) {?>
				
					var isHttps = (document.location.protocol === 'https:');
					passwordValue = ((isHttps) ? window['confirmValue'] : hex_md5(confirmValue))
					postParams += '&confirmpassword=' + passwordValue;
					postParams += '&confirmformat=' + ((isHttps) ? 0 : 1);
				
			<?php }?>
		

        processAjaxSmallScreen("updateAction",".?fsaction=Customer.updateAccountDetails&ishighlevel=" + isHighLevel + '&mawebhlbr=' + basketRef, 'POST', postParams);
    }

    

<?php } else { ?>

    

    if (gAlerts > 0)
    {
        var missingDataNoticeDialog = new TPXSimpleDialog({
            title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
",
            content: "<p>" + message.trim().replace(/(?:\r\n|\r|\n)/g, "</p><p>") + "</p>",
            buttons: {
                right: {
                    text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonOk');?>
",
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

            

            <?php if ($_smarty_tpl->tpl_vars['edit']->value == 0) {?>

                

        form.countryname.value = document.getElementById('countrylist').options[document.getElementById('countrylist').selectedIndex].text;

                

            <?php } else { ?>

                

        form.countryname.value = countryName;
        form.countrycode.value = "<?php echo $_smarty_tpl->tpl_vars['country']->value;?>
";
        form.regioncode.value = "<?php echo $_smarty_tpl->tpl_vars['regioncode']->value;?>
";

                

            <?php }?>

            

        form.telephonenumber.value = document.getElementById('telephonenumber_account').value;
        form.email.value = document.getElementById('email_account').value;
        form.originalemail.value = "<?php echo $_smarty_tpl->tpl_vars['email']->value;?>
";
        form.registeredtaxnumbertype.value = registeredTaxNumberType;
        form.registeredtaxnumber.value = registeredTaxNumber;
        form.fsaction.value = "Customer.updateAccountDetails";

        if (document.getElementById('email_account').value != "<?php echo $_smarty_tpl->tpl_vars['email']->value;?>
")
        {
            var messageText = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageEmailUpdateRequest');?>
";
            var emailUpdatedText = messageText.replace("^0", document.getElementById('email_account').value);
			emailUpdatedText += "<p class='delay-message-text'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ConfirmationResetMessage');?>
</p>";

            var emailNoticeDialog = new TPXSimpleDialog({
                title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleChangeEmailAddress');?>
", //'Changes saved', //
                content: "<p>" + emailUpdatedText + "</p>",
                buttons: {
                    right: {
                        text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonOk');?>
",
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
    

<?php }?>



}



/* OPEN EXISTING PROJECT */

<?php if ($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects' || $_smarty_tpl->tpl_vars['section']->value == 'yourorders') {?>

    

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




<?php }?>

 /* END OPEN EXISTING PROJECT */

/* CHANGE PASSWORD */

<?php if ($_smarty_tpl->tpl_vars['section']->value == 'changepassword') {?>

    

	var passwordStrength = new TPXPasswordStrength({
		minStrength: <?php echo $_smarty_tpl->tpl_vars['passwordstrengthmin']->value;?>
,
		weakString: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorPasswordTooWeak');?>
",
		strengthStrings: {
			0: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStartTyping');?>
",
			1: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPasswordVeryWeak');?>
",
			2: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPasswordWeak');?>
",
			3: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPasswordMedium');?>
",
			4: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPasswordStrong');?>
",
			5: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPasswordVeryStrong');?>
"
		}
	});
	

function checkFormChangePassword()
{
    gAlerts = 0;
    var message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorCompulsoryInformationMissing');?>
";
    var oldpassword = document.getElementById('oldpassword');
    var newpassword = document.getElementById('newpassword');
    var passwordStrengthErrorText = passwordStrength.getErrorText();

    oldpassword.className = oldpassword.className.replace("errorInput", "");
    newpassword.className = newpassword.className.replace("errorInput", "");

    if (oldpassword.value.length == 0)
    {
        message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoCurrentPassword');?>
";
        highlight("oldpassword");
    }

    if (oldpassword.value.length < 5)
    {
        message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryPasswordLength');?>
";
        message = message.replace("^0", '5');
        highlight("oldpassword");
    }

    if (newpassword.value.length == 0)
    {
        message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoNewPassword');?>
";
        highlight("newpassword");
    }

    if (newpassword.value.length < 5)
    {
        message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryPasswordLength');?>
";
        message = message.replace("^0", '5');
        highlight("newpassword");
    }

    if (oldpassword.value == newpassword.value)
    {
        message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorPasswordsSame');?>
";
        highlight("newpassword");
    }

    if (passwordStrengthErrorText != '')
    {
        message += passwordStrengthErrorText;
        highlight("newpassword");
    }

    

    <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

        

    if (gAlerts > 0)
    {
        createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", nlToBr(message), function(e) {
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

    

	<?php } else { ?>

		

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

        

   <?php }?>

   

}

    

<?php }?>

/* END CHANGE PASSWORD */

/* PERSONAL DATA DELETION */



function dataDeletion(pRedactionMode, pRedactionDays)
{
	// display a warning
	if (pRedactionMode == 2)
	{
		showRedactionConfirmationBox("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_WarningConfirmRedactionRequest');?>
");
	}
	else
	{
		showRedactionConfirmationBox("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_WarningConfirmRedaction');?>
");
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
        // Only allow browser version <?php echo $_smarty_tpl->tpl_vars['minSafariVersion']->value;?>
 or higher.
		if (parseInt((browserUA.split('Version/')[1]).split(' ')[0]) >= <?php echo $_smarty_tpl->tpl_vars['minSafariVersion']->value;?>
)
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



/* END PERSONAL DATA DELETION */

<?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

    
	<?php if (($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects') || ($_smarty_tpl->tpl_vars['section']->value == 'yourorders')) {?>

        

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
					createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleBrowserCompatibilityIssue');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorBrowserCompatibilityIssue');?>
", function(e) {
                        closeDialog(e);
                    });
				}
				else
				{
					if (wizOK == 0)
					{
						createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleDeviceCompatibilityIssue');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorDeviceCompatibilityIssue');?>
", function(e) {
                            closeDialog(e);
                        });
					}
				}

				

				<?php if (($_smarty_tpl->tpl_vars['section']->value == 'yourorders')) {?>

					

					if ((browserCompatability == false) || (wizOK == 0))
					{
						showOPActionPanel(false);
						closeLoadingDialog();
					}

					

				<?php }?>

				
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
						title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRenameProject');?>
",
						labelBtn: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonRenameProject');?>
",
						label: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnterNewNameForTheProject');?>
',
						value: name,
						fn: validateNameForm
					});
				break;
				case 'duplicate':
					gNameFormAction = 'duplicate';
					gDuplicateProjectWizardMode = pWizMode;
					gDuplicateWorkflowType = pWorkType;
					

					<?php if (($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects')) {?>

						

					var projectdiv = document.getElementById('onlineProjectDetail' + gActiveProductOnline);
					var name = projectdiv.getAttribute('data-projectname').trim() + " - <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCopy');?>
";

						

					<?php } else { ?>

						

					var name = document.getElementById('onlineProjectOrderLabel' + gActiveProductOnline).getAttribute("data-projectname")  + " - <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCopy');?>
";

						

					<?php }?>

					

					name = name.trim();
					showOPActionPanel(true, {
						panel: 'duplicate',
						title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDuplicateProject');?>
",
						labelBtn: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDuplicateProject');?>
",
						label: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnterNewNameForTheProject');?>
',
						value: name,
						fn: validateNameForm
					});
					break;
				case 'share':
						processAjaxSmallScreen("getshareonlineprojecturl", ".?fsaction=AjaxAPI.callback&cmd=GETSHAREONLINEPROJECTURL&projectref=" + gActiveProductOnline, 'GET', '', true);
					break;
				case 'delete':
					var confirmDeleteMessage = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleteProjectConfirmation');?>
";
					var name = document.getElementById('pageLabel' + gActiveProductOnline).innerHTML;
					name = name.trim();
					confirmDeleteMessage = confirmDeleteMessage.replace('^0', "'" + name + "'");
					showConfirmDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDeleteProject');?>
", confirmDeleteMessage, function(e) { return checkDeleteSession(0, 'delete');});
				break;
			}
		}

		function openExistingOnlineProject(pEditingType)
		{
			

			<?php if (($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects')) {?>

				

					var divObject = document.getElementById('onlineProjectDetail' + gActiveProductOnline);

				

			<?php } else { ?>

				

					var divObject = document.getElementById('onlineProjectOrderDetail' + gOriginalProductOnline);

				

			<?php }?>

			

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
				createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorProjectHasBeenDeleted');?>
", function(e) {
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
				emptyBoxContainer.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNoOnlineProject');?>
";

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

			if (parseInt(pWorkTypeIn) == <?php echo $_smarty_tpl->tpl_vars['kProducTypeSinglePrints']->value;?>
)
			{
				wizCheck = true;
			}
			else if (parseInt(pWorkTypeIn) == <?php echo $_smarty_tpl->tpl_vars['kProducTypePhotoPrints']->value;?>
)
			{
				wizCheck = true;
			}
			else if (<?php echo $_smarty_tpl->tpl_vars['easyEditorModeActive']->value;?>
)
			{
				// All Devices are supported by the easy editor mode.
				wizCheck = true;
			}
			else
			{
				if (((parseInt(pWorkTypeIn) == <?php echo $_smarty_tpl->tpl_vars['kProducTypePhotobook']->value;?>
) || (parseInt(pWorkTypeIn) == <?php echo $_smarty_tpl->tpl_vars['kProducTypeCalendar']->value;?>
))
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

				if (((parseInt(pWorkTypeIn) == <?php echo $_smarty_tpl->tpl_vars['kProducTypePhotobook']->value;?>
) || (parseInt(pWorkTypeIn) == <?php echo $_smarty_tpl->tpl_vars['kProducTypeCalendar']->value;?>
))
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

					

					<?php if (($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects')) {?>

						

							var divObject = document.getElementById('onlineProjectDetail' + gActiveProductOnline);

						

					<?php } else { ?>

						

							var divObject = document.getElementById('onlineProjectOrderDetail' + gActiveProductOnline);

						

					<?php }?>

					

					var workflowType = divObject.getAttribute("data-workflowtype");
					var productIdent = divObject.getAttribute("data-productident");
					var tzoffset = getTimestamp();

					processAjaxSmallScreen("duplicateonlineproject", ".?fsaction=AjaxAPI.callback&cmd=DUPLICATEONLINEPROJECT&projectref=" + gActiveProductOnline + '&projectname=' + encodeURIComponent(projectName) + '&ishighlevel=' + isHighLevel + '&mawebhlbr=' + basketRef + '&productident=' + productIdent + '&workflowtype=' + workflowType + '&tzoffset=' + tzoffset, 'POST', '');
				}
			}
			else
			{
				createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoProjectName');?>
", function(e) {
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

				

				<?php if (($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects')) {?>

					

						document.getElementById('onlineDetailPanel').style.marginLeft = '-' + gScreenWidth + 'px';

					

				<?php } else { ?>

					

						document.getElementById('orderDetailPanel').style.marginLeft = '-' + gScreenWidth + 'px';

					

				<?php }?>

				

				gNameForm = true;
			}
			else
			{
				

				<?php if (($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects')) {?>

					

						document.getElementById('onlineDetailPanel').style.marginLeft = 0;

					

				<?php } else { ?>

					

						document.getElementById('orderDetailPanel').style.marginLeft = 0;

					

				<?php }?>

				

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
			

			<?php if (($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects')) {?>

				

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

				

			<?php } else { ?>

				

					gActiveProductOnline = pDuplicateResult.projectref;
					onlineProjectsButtonAction('continueediting', gDuplicateProjectWizardMode, gDuplicateWorkflowType);

				

			<?php }?>

			

		}

		function changeCanModify(pProjectref)
		{
			closeLoadingDialog();

			createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorOrderInProduction');?>
", function(e) {
                closeDialog(e);
            });

			var statusDescription = document.getElementById('statusDescription' + pProjectref);
			if (statusDescription)
			{
				statusDescription.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusInProduction');?>
";
				document.getElementById('detailStatusDescription' + pProjectref).innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusInProduction');?>
";
			}
			else
			{
				// create status div
				var contentStatus = '<br /> <span class="orderLabelMedium">' + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
:" + '</span>';
				contentStatus += '<span class="statusInProduction">' + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusInProduction');?>
" + '</span>';

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
					message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_WarningProjectOpenInShoppingCart');?>
";
					break;
				}
				case 'taopixonline':
				{
					if (pAction == 'delete')
					{
						message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_WarningDeleteTerminateOtherSession');?>
";
					}
					else
					{
						message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_WarningTerminateOtherSession');?>
";
					}
					break;
				}
			}
			
			showConfirmDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitlePleaseConfirm');?>
", message, function(e) { 
				return checkDeleteSession(1, pAction); 
			});
		}


		

	<?php }?>

<?php } else { ?> 
    <?php if (($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects') || ($_smarty_tpl->tpl_vars['section']->value == 'yourorders')) {?>

        

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
          this.src = '<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/no_image-2x.jpg';
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
					document.getElementById('renameProjectTitle').innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelBrowserCompatibilityIssue');?>
";
					document.getElementById('projectname_container').innerHTML = '<div class="confirmationText">' + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorBrowserCompatibilityIssue');?>
" + '</div>';
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
				var title = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRenameProject');?>
";

				//change the title of the popup
				if (pButtonClicked == 'delete')
				{
					var title = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitlePleaseConfirm');?>
";
				}
				else if(pButtonClicked == 'duplicateproject')
				{
					var title = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProjectName');?>
";
				}

				document.getElementById('renameProjectTitle').innerHTML = title;
				var dilaogBox = document.getElementById('dialogBoxOnlineAction');
				var shimObj = document.getElementById('shim');
				if ((pButtonClicked == 'rename') || (pButtonClicked == 'duplicateproject'))
				{
					document.getElementById('projectname_container').innerHTML = '<label for="projectname">' + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProjectName');?>
:" + '</label><input type="text" name="projectname" id="projectname" maxlength="75"/>';
					var projectNameElement = document.getElementById('projectname');

					projectNameElement.value = document.getElementById('projectnamehidden').value + ' - <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCopy');?>
';
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
				showLoadingDialog('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleting');?>
');
			}
			else
			{
				showLoadingDialog('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageLoading');?>
');
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
				showLoadingDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleDuplicatingProject');?>
");
				processAjax("duplicateonlineproject", ".?fsaction=AjaxAPI.callback&cmd=DUPLICATEONLINEPROJECT&projectref=" + projectRef + '&projectname=' + encodeURIComponent(projectName) + '&ishighlevel=' + isHighLevel + '&mawebhlbr=' + basketRef + '&productident=' + productIdent + '&tzoffset=' + tzoffset, 'POST', '', true);
			}
			else
			{
				var shimObj = document.getElementById('shim');
				shimObj.style.zIndex = 201;
				showConfirmationBox("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoProjectName');?>
");
			}
		}

		function duplicateOnlineProjectCallBack(pDuplicateResult)
		{
			

			<?php if ($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects') {?>

				

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
				

			<?php } else { ?>

				

					showLoadingDialog('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageLoading');?>
');
					document.getElementById('projectrefhidden').value = pDuplicateResult.projectref;
					document.getElementById('projectworkflowtype').value = pDuplicateResult.workflowtype;
					document.getElementById('productindent').value = pDuplicateResult.productident;
					openExistingOnlineProject(1);

				

			<?php }?>

			
		}


		function changeCanModify(pProjectref)
		{
			closeDialogBoxOnlineAction();

			var shimObj = document.getElementById('shim');
			shimObj.style.zIndex = 200;
			showConfirmationBox("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorOrderInProduction');?>
");

			var selectedProject = document.getElementById(pProjectref);
			setActiveButtonsFromStatus(0, 0, 0);

			document.getElementById('statusDescription' + pProjectref).innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatusInProduction');?>
";
		}


		function displayTerminateSessionConfirmation(pSessionType, pAction)
		{
			var message = "";
			var title = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitlePleaseConfirm');?>
";

			document.getElementById('renameProjectTitle').innerHTML = title;

			switch (pSessionType)
			{
				case 'shoppingcart':
				{
					message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_WarningProjectOpenInShoppingCart');?>
";
					break;
				}
				case 'taopixonline':
				{
					if (pAction == 'delete')
					{
						message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_WarningDeleteTerminateOtherSession');?>
";
					}
					else
					{
						message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_WarningTerminateOtherSession');?>
";
					}
					break;
				}
			}

			document.getElementById('projectname_container').innerHTML = '<div class="confirmationText">' + message + '</div>';
			document.getElementById('projectacceptbutton').onclick = function()
			{
				checkDeleteSession(1, pAction);
			};

			document.getElementById('projectacceptbutton').innerHTML = '<div class="btn-green-left" ></div><div class="btn-green-middle" >' + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonContinue');?>
" + '</div><div class="btn-accept-right"></div>';
			document.getElementById('projectcancelbutton').innerHTML = '<div class="btn-red-cross-left" ></div><div class="btn-red-middle">' + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDoNotContinue');?>
" + '</div><div class="btn-red-right"></div>';

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

		

	<?php }?>

<?php }?>


/* END GENERIC */

<?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

    
    <?php if (($_smarty_tpl->tpl_vars['section']->value == 'menu') || ($_smarty_tpl->tpl_vars['section']->value == 'accountdetails')) {?>

        

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

        

        <?php if ($_smarty_tpl->tpl_vars['section']->value == 'menu') {?>

            

            // close loading dialog
            closeLoadingDialog();

            

        <?php } else { ?>

            

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

            

        <?php }?>

        

        setScrollAreaHeight('contentLeftScrollMenu', '');
    }

    function redeemGiftCard()
    {
        var giftcardtext =  document.getElementById("giftcardid").value;
        if((giftcardtext != '') && (giftcardtext != "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnterCode');?>
"))
        {
			var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
			if (csrfMeta) {
				var csrfToken = csrfMeta.getAttribute('content');
			}

            postParams = '&giftcardcode=' + giftcardtext;
            postParams += '&giftcardaction=' +  "<?php echo $_smarty_tpl->tpl_vars['section']->value;?>
";
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

        

    <?php }?> 
    /* END MENU */

    /* CHANGE PREFERENCES */

    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'changepreferences') {?>

        

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
        

    <?php }?>

    /* CHANGE PASSWORD */

    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'changepassword') {?>

        

    function initializePanel()
    {
        document.getElementById('contentPanelAjax').style.width = gScreenWidth + 'px';
    }

        

    <?php }?>

    /* END CHANGE PASSWORD */

    /* ACCOUNT DETAILS */

    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'accountdetails') {?>

        

    function initializePanel()
    {
        document.getElementById('contentPanelAjax').style.width = gScreenWidth + 'px';

        processAjaxSmallScreen("addressForm",".?fsaction=AjaxAPI.callback&cmd=ADDRESSFORM&country=" + country + "&addresstype=billing&hideconfigfields=" + hideConfigFields +"&strict=1&edit=<?php echo $_smarty_tpl->tpl_vars['edit']->value;?>
&ishighlevel=" + isHighLevel + "&mawebhlbr=" + basketRef , 'GET', '');
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
                images[t].src = "<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/asterisk.png";
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

        

    <?php }?> 
    /* END ACCOUNT DETAILS */


    /* YOUR ORDERS */

    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'yourorders') {?>

        

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
        var title = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareProject');?>
";
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
        var messageError = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorCompulsoryInformationMissing');?>
";
        if (!checkPassword())
        {
            messageError +="<br /><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePasswordInformation');?>
";
        }
        var emailTitle = document.getElementById('shareByEmailTitle').value;
        var emailRecipients = document.getElementById('shareByEmailTo').value;
        var shareByEmailText = document.getElementById('shareByEmailText').value;
        if (emailTitle == '')
        {
            messageError += "<br /><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageEnterMessageTitle');?>
";
            highlight("shareByEmailTitle");

        }
        if (emailRecipients == '')
        {
            messageError += "<br /><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageEnterAtLeastOneEmail');?>
";
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
                    messageError += "<br /><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageInvalidEmail');?>
";
                    document.getElementById('shareByEmailTo').focus();
                    highlight("shareByEmailTo");
                }
            }
        }

		var format = ((document.location.protocol != 'https:') ? 1 : 0);

        if (gAlerts == 0)
        {
            var message = '<img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/loading_shoppingcart_v2.gif" class="loading-icon" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageLoading');?>
" />';
            message += "&nbsp;<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSendingEmail');?>
";

            createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", message, function(e) {
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

        

            <?php if ($_smarty_tpl->tpl_vars['sharebyemailmethod']->value == 1) {?>

                

                    // email send by control center
                    processAjaxSmallScreen("shareByEmail", '/?ishighlevel=' + isHighLevel + '&mawebhlbr=' + basketRef, 'POST', "fsaction=Share.shareByEmail&orderItemId="+ orderItemID + '&title='+encodeURIComponent(emailTitle) + '&recipients='+encodeURIComponent(emailRecipients) + '&message='+encodeURIComponent(shareByEmailText) + '&previewPassword='+encodeURIComponent(previewPasswordValue) + '&format=' + format);

                

            <?php } else { ?>

                

                    // mailto link
                    processAjaxSmallScreen("mailToLink", '/?ishighlevel=' + isHighLevel + '&mawebhlbr=' + basketRef, 'POST', "fsaction=Share.mailTo&orderItemId="+ orderItemID + '&title='+encodeURIComponent(emailTitle) + '&recipients='+encodeURIComponent(emailRecipients) + '&message='+encodeURIComponent(shareByEmailText) + '&previewPassword='+encodeURIComponent(previewPasswordValue) + '&format=' + format);

                

            <?php }?>

        

        }
        else
        {
            createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", messageError, function(e) {
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

        

    <?php }?> 
    /* END YOUR ORDERS */

    /* OPEN EXISTING PROJECT */

    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects') {?>

        

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
		showConfirmDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleKeepProject');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageKeepProjectWarningMessage');?>
", function(event) {
			closeDialog(event);
			event.preventDefault();
			event.stopImmediatePropagation();
			showLoadingDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleKeepingProject');?>
");
			processAjax("keeponlineproject", ".?fsaction=AjaxAPI.callback&cmd=KEEPONLINEPROJECT&projectref=" + projectRef, 'POST', '', true);
		});
	}

	function purgeFlaggedProjects()
	{
		showConfirmDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitlePleaseConfirm');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageConfirmPurgeProjectsDeletionMessage');?>
", function(event) {
			closeDialog(event);
			event.preventDefault();
			event.stopImmediatePropagation();
			showLoadingDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitlePurgingProjects');?>
");
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

        

    <?php }?> 
    
<?php } else { ?> 
    
    

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

    <?php if ($_smarty_tpl->tpl_vars['showgiftcardsbalance']->value) {?>
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
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['redactionmode']->value >= 2) {?>
        // Add listener to the redaction request link.
        var redactionRequestLink = document.getElementById('dataDeletionOptionLink');
        if (redactionRequestLink)
        {
            redactionRequestLink.addEventListener('click', function() {
                return dataDeletion(<?php echo $_smarty_tpl->tpl_vars['redactionmode']->value;?>
, <?php echo $_smarty_tpl->tpl_vars['redactiondays']->value;?>
);
            });
        }

        // Add listener to the redaction confirmation dialog cancel button.
        document.getElementById('closeRedactionConfirmButton').addEventListener('click', function() {
            closeRedactionConfirmationBox();
        });

        // Add listener to the redaction confirmation dialog confirm button.
        document.getElementById('confirmRedactionButton').addEventListener('click', function() {
            redactionConfirmation(<?php echo $_smarty_tpl->tpl_vars['redactionmode']->value;?>
, <?php echo $_smarty_tpl->tpl_vars['redactiondays']->value;?>
);
        });
    <?php }?>

    <?php if (($_smarty_tpl->tpl_vars['section']->value == 'menu')) {?>
        // Add listeners to account pages menu buttons.
        var classname = document.getElementsByClassName('menuActionButton');
        for (var i = 0; i < classname.length; i++)
        {
            classname[i].addEventListener('click', function() {
                var menuActionValue = this.getAttribute("data-action");
                return menuAction(menuActionValue);
            });
        }
    <?php } else { ?>
        // Add the event listener to the home link.
        var homeLink = document.getElementById('homeLink');
        if (homeLink)
        {
            homeLink.addEventListener('click', function() {
                document.submitform.fsaction.value = '<?php echo $_smarty_tpl->tpl_vars['homebuttonfuseaction']->value;?>
';
                document.submitform.submit();
                return false;
            });
        }

        // Add the event listener to the back buttons.
        var backButton = document.getElementById('backButton');
        if (backButton)
        {
            backButton.addEventListener('click', function() {
                document.submitform.fsaction.value = '<?php echo $_smarty_tpl->tpl_vars['homebuttonfuseaction']->value;?>
';
                document.submitform.submit();
                return false;
            });
        }
    <?php }?>


    

        <?php if (($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects') && (sizeof($_smarty_tpl->tpl_vars['projects']->value) > 0)) {?>

            

        calcualteScrollableView();

            

        <?php }?>

    

        onloadWindow()

        /* YOUR ORDERS */

    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'yourorders') {?>
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
			var confirmDeleteMessageTemplate = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleteProjectConfirmation');?>
";
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
					showConfirmDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDeleteProject');?>
", confirmDeleteMessage, function(e) {
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

    <?php }?>

        /* END YOUR ORDERS */

        /* ONLINE PROJECTS */

    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects') {?>
        // Add listeners to account pages menu buttons.
        var classname = document.getElementsByClassName('contentRow');
        for (var i = 0; i < classname.length; i++)
        {
            classname[i].addEventListener('click', function() {
                return selectProject(this);
            });
        }
    <?php }?>

        /* END ONLINE PROJECTS */

        /* YOUR ORDERS & ONLINE PROJECTS */

    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'yourorders' || $_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects') {?>
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
    <?php }?>

        /* END YOUR ORDERS & ONLINE PROJECTS */

        /* ACCOUNT DETAILS */

    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'accountdetails') {?>
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

        processAjax("ajaxdiv",".?fsaction=AjaxAPI.callback&cmd=ADDRESSFORM&country=" + country + "&addresstype=billing&hideconfigfields=" + hideConfigFields +"&strict=1&edit=<?php echo $_smarty_tpl->tpl_vars['edit']->value;?>
", 'GET', '', true);

    <?php }?>

        /* END ACCOUNT DETAILS */

        /* CHANGE PASSWORD */

    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'changepassword') {?>
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
        
    <?php }?>

        /* END CHANGE PASSWORD */

        /* CHANGE PREFERENCES */

    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'changepreferences') {?>
        // Add listener to the update button on the change preferences form.
        document.getElementById('updateButton').addEventListener('click', function(event) {
            return checkFormChangePreferences();
        });
    <?php }?>

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

        if ("<?php echo $_smarty_tpl->tpl_vars['message']->value;?>
".length > 0)
        {
            document.getElementById('message').style.display = 'block';
        }

            
            <?php if ($_smarty_tpl->tpl_vars['showgiftcardmessage']->value == 1) {?>
            

        displayGiftCardAlert("<?php echo $_smarty_tpl->tpl_vars['giftcardresult']->value;?>
", "");

            
            <?php }?>
            
    }

    function redeemGiftCard()
    {
        var giftcardtext =  document.getElementById("giftcardid").value;
        if((giftcardtext != '') && (giftcardtext != "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnterCode');?>
"))
        {
            /* pass localised timestamp */
            var timestamp = getTimestamp();

            document.submitform.tzoffset.value = timestamp;
            document.submitform.giftcardcode.value = giftcardtext;
            document.submitform.giftcardaction.value = "<?php echo $_smarty_tpl->tpl_vars['section']->value;?>
";
            document.submitform.showgiftcardmessage.value = 1;
            document.submitform.fsaction.value = 'Customer.updateGiftCard';
            document.submitform.submit();
        }
        return false;
    }

    function setSystemLanguage()
    {
        changeSystemLanguage("<?php echo $_smarty_tpl->tpl_vars['refreshaction']->value;?>
", "submitform", 'post');
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

    


    /* MENU */

    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'menu') {?>

        

    function menuAction(actionUrl)
    {
		/* pass localised timestamp */
		var timestamp = getTimestamp();

		document.submitform.tzoffset.value = timestamp;
        document.submitform.fsaction.value = actionUrl;
        document.submitform.submit();
        return false;
    }

        

    <?php }?> 
    /* END MENU */

    /* ACCOUNT DETAILS */

    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'accountdetails') {?>

        

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
                images[t].src = "<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/asterisk.png";
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


        

    <?php }?>

    /* END ACCOUNT DETAILS */

    /* CHANGE PREFERENCES */

    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'changepreferences') {?>

        

    function checkFormChangePreferences()
    {
    	document.submitform.fsaction.value = 'Customer.updatePreferences';
        document.submitform.sendmarketinginfo.value = (document.getElementById('subscribed').checked) ? '1' : '0';
        document.submitform.submit();

        return false;
    }

        

    <?php }?>

    /* END CHANGE PREFERENCES */

    /* YOUR ORDERS */

    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'yourorders') {?>

        

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
            alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePasswordInformation');?>
");

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
        var title = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareProject');?>
";
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
        var messageError = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorCompulsoryInformationMissing');?>
";
        if (!checkPassword())
        {
            messageError +="\n"  + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePasswordInformation');?>
";
        }
        var emailTitle = document.getElementById('shareByEmailTitle').value;
        var emailRecipients = document.getElementById('shareByEmailTo').value;
        var shareByEmailText = document.getElementById('shareByEmailText').value;
        if (emailTitle == '')
        {
            messageError += "\n"  + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageEnterMessageTitle');?>
";
            highlight("shareByEmailTitle");

        }
        if (emailRecipients == '')
        {
            messageError += "\n"  + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageEnterAtLeastOneEmail');?>
";
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
                    messageError += "\n"  + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageInvalidEmail');?>
";
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
            confirmationBoxTextObj.innerHTML = '<img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/loading_shoppingcart_v2.gif" class="loading-icon" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageLoading');?>
" />' +  "&nbsp;<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSendingEmail');?>
";

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



        

            <?php if ($_smarty_tpl->tpl_vars['sharebyemailmethod']->value == 1) {?>

                

                    // email send by control center
					processAjax("shareByEmail", '/', 'POST', "fsaction=Share.shareByEmail&orderItemId="+ orderItemID + '&title='+encodeURIComponent(emailTitle) + '&recipients='+encodeURIComponent(emailRecipients) + '&message='+encodeURIComponent(shareByEmailText) + '&previewPassword='+encodeURIComponent(previewPasswordValue) + '&format=' + format, true);

                

            <?php } else { ?>

                

                    // mailto link
					processAjax("mailToLink", '/', 'POST', "fsaction=Share.mailTo&orderItemId="+ orderItemID + '&title='+encodeURIComponent(emailTitle) + '&recipients='+encodeURIComponent(emailRecipients) + '&message='+encodeURIComponent(shareByEmailText) + '&previewPassword='+encodeURIComponent(previewPasswordValue) + '&format=' + format, true);

                

            <?php }?>

        
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
			showConfirmationBox("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorProjectHasBeenDeleted');?>
");
		}

		var selectedProject = document.getElementById(pProjectref);
		selectedProject.parentNode.removeChild(selectedProject);
	}


        

    <?php } else { ?> 
        

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

        

    <?php }?> 
    /* END YOUR ORDERS */

    /* OPEN EXISTING PROJECT */

    <?php if ($_smarty_tpl->tpl_vars['section']->value == 'existingonlineprojects') {?>

        

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
			showConfirmationBox("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorProjectHasBeenDeleted');?>
");
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
			emptyBoxContainer.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNoOnlineProject');?>
";

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

		var confirmDeleteMessage = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageDeleteProjectConfirmation');?>
";
		document.getElementById('projectname_container').innerHTML = '<div class="confirmationText" style="display: block;">' + confirmDeleteMessage.replace('^0', "'" + projectName + "'"); + '</div>';
		document.getElementById('projectacceptbutton').onclick = function(){
			checkDeleteSession(0, 'delete');
		};
		document.getElementById('projectacceptbutton').innerHTML = '<div class="btn-green-left" ></div><div class="btn-green-middle" >' + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonYes');?>
" + '</div><div class="btn-accept-right"></div>';
		document.getElementById('projectcancelbutton').innerHTML = '<div class="btn-red-cross-left" ></div><div class="btn-red-middle">' + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonNo');?>
" + '</div><div class="btn-red-right"></div>';
    }

	/**
	 * Show the share project dialog on large screen
	 * @param pShareURL
	 */
	function showShareProjectDialogLarge(pShareURL)
	{
		var shareProjectDialog = new TPXSimpleDialog(
		{
			title : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonShareProject');?>
",
			content: function() {
				this.clearContent();
				var container = document.createElement('div');
				var shareProjectDialogHTML = '' +
					'<div id="sharelink-tip" class="tip-popout">' +
						'<p><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ToolTipLinkCopied');?>
</p>' +
					'</div>' +
					'<div class="sharelink_link_container clearfix">' +
						'<label for="sharelink-url"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareLink');?>
</label>' +
						'<input type="text" name="sharelink-url" value="' + pShareURL + '" id="sharelink-url" maxlength="75" readonly="readonly"/>' +
					'</div>';

				container.innerHTML = shareProjectDialogHTML;
				return container;
			},
			buttons:
			{
				left:
				{
					text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonClose');?>
',
					action: function() {
						return false;
					}
				},
				right:
				{
					text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCopyLink');?>
',
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
            showLoadingDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleRenamingProject');?>
");
			processAjax("renameonlineproject", ".?fsaction=AjaxAPI.callback&cmd=RENAMEONLINEPROJECT&projectref=" + projectRef + '&projectname=' + encodeURIComponent(projectName), 'POST', '', true);
        }
        else
        {
            var shimObj = document.getElementById('shim');
            shimObj.style.zIndex = 201;
            showConfirmationBox("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoProjectName');?>
");
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
				title : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleKeepProject');?>
",
				content: function() {
					this.clearContent();
					var container = document.createElement('div');
					var dialogHTML = "<div><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageKeepProjectWarningMessage');?>
</div>";
					container.innerHTML = dialogHTML;
					return container;
				},
				buttons: {
					left: {
						text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
",
						action: function() {
							return false;
						}
					},
					right: {
						text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleKeepProject');?>
",
						action: function(event) {
							this.close();
							// set preventDefault() on event to prevent TPXSimpleDialog default behaviour
							event.preventDefault();
							showLoadingDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleKeepingProject');?>
");
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
				title : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitlePleaseConfirm');?>
",
				content: function() {
					this.clearContent();
					var container = document.createElement('div');
					var dialogHTML = "<div><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageConfirmPurgeProjectsDeletionMessage');?>
</div>";
					container.innerHTML = dialogHTML;
					return container;
				},
				buttons: {
					left: {
						text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
",
						action: function() {
							return false;
						}
					},
					right: {
						text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDeleteNow');?>
",
						classes: [
							'btn-red-left',
							'btn-red-middle',
							'btn-red-right'
						],
						action: function(event) {
							this.close();
							// set preventDefault() on event to prevent TPXSimpleDialog default behaviour
							event.preventDefault();
							showLoadingDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePleaseWait');?>
");
							processAjax("purgeflaggedprojects", ".?fsaction=AjaxAPI.callback&cmd=PURGEFLAGGEDPROJECTS", 'POST', '', true);
						}
					}
				}
			}).show();
		}
        

    <?php }?>

    /* END OPEN EXISTING PROJECT */

    
<?php }?> <?php }
}
