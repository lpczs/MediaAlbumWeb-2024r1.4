 {* COMMON JAVSCRIPT (LARGE/SMALL) *}

{literal}
var session = "{/literal}{$session}{literal}";
var unshareArray = '';
var gAlerts = 0;
var addressUpdated = "{/literal}{$addressupdated}{literal}";
var countryChanged = false;
var addToAnyIntialised = false;

/* ACCOUNT DETAILS */
{/literal}

{if $section=='accountdetails'}

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
function processAjax(obj, serverPage, async)
{
    /* get an XMLHttpRequest object for use */
    /* make xmlhttp local so we can run simlutaneous requests */
    var xmlhttp = getxmlhttp();
    xmlhttp.open("GET", serverPage+"&dummy=" + new Date().getTime(), async);
    xmlhttp.onreadystatechange = function()
    {
        if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
        {
            switch (obj)
            {
                case 'verify':
                    updateAccountDetails(xmlhttp.responseText);
                    break;
                case 'unshare':
                    var unshareResult = eval('(' + xmlhttp.responseText + ')');
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
                    break;
                case 'shareByEmail':
                    var shareResult = eval('(' + xmlhttp.responseText + ')');
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
                    break;
                case 'mailToLink':
                    var shareResult = eval('(' + xmlhttp.responseText + ')');
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
                    break;
                case 'unsharelist':
                    return xmlhttp.responseText;
                break;
                case 'shareurl':
                    return xmlhttp.responseText;
                break;
				case 'duplicateonlineproject':
					var duplicateResult = JSON.parse(xmlhttp.responseText);

					hideLoadingDialog();

					if (duplicateResult.error == '')
					{
						if (duplicateResult.projectexists == false)
						{
							removeDeletedProject(duplicateResult.projectref, true);
						}
						else if (duplicateResult.nameexists != '')
						{
							var shimObj = document.getElementById('shim');
							shimObj.style.zIndex = 201;
							showConfirmationBox(duplicateResult.nameexists);
						}
						else
						{
							var projectListContainer = document.getElementById('existingOnlineProjectList');
							projectListContainer.innerHTML = duplicateResult.html + projectListContainer.innerHTML;
							closeDialogBox();
						}
					}
					break;
                case 'renameonlineproject':
                    var renameResult = JSON.parse(xmlhttp.responseText);

                    hideLoadingDialog();

                    if (renameResult.error == '')
                    {
						var projectDetails = renameResult.projectdetails;
						if (projectDetails.projectexists == false)
						{
							removeDeletedProject(projectDetails.projectref, true);
						}
						else if (projectDetails.nameexists != '')
						{
							var shimObj = document.getElementById('shim');
							shimObj.style.zIndex = 201;
							showConfirmationBox(projectDetails.nameexists);
						}
						else
						{
							var renameID = 'name_' + projectDetails.projectref;
							var projectName = projectDetails.projectname;
							document.getElementById(renameID).innerHTML = projectName;
							document.getElementById('projectnamehidden').value = projectName;
							document.getElementById(projectDetails.projectref).setAttribute("data-projectname", projectName);
							closeDialogBox();
						}
                    }
                break;
				case 'checkDeleteSessionediting':
					var response = JSON.parse(xmlhttp.responseText);
					if (response.error == '')
                    {
						if (response.canmodify == 0)
						{
							hideLoadingDialog();
							changeCanModify(response.projectref);
						}
						else
						{
							if (response.sessionactive == true)
							{
								hideLoadingDialog();
								displayTerminateSessionConfirmation(response.sessiontype, 'editing');
							}
							else
							{
								if (response.projectexists == true)
								{
									openExistingOnlineProject();
								}
								else
								{
									hideLoadingDialog();
									removeDeletedProject(response.projectref, true);
								}
							}
						}
					}
					break;
				case 'checkDeleteSessioncomplete':
					var response = JSON.parse(xmlhttp.responseText);
					if (response.error == '')
                    {
						if (response.canmodify == 0)
						{
							hideLoadingDialog();
							changeCanModify(response.projectref);
						}
						else
						{
							if (response.sessionactive == true)
							{
								hideLoadingDialog();
								displayTerminateSessionConfirmation(response.sessiontype, 'complete');
							}
							else
							{
								if (response.projectexists == true)
								{
									completeOrder();
								}
								else
								{
									hideLoadingDialog();
									removeDeletedProject(response.projectref, true);
								}
							}
						}
					}
					break;
				case 'checkDeleteSessiondelete':
					var response = JSON.parse(xmlhttp.responseText);
					if (response.error == '')
                    {
						hideLoadingDialog();
						if (response.canmodify == 0)
						{
							changeCanModify(response.projectref);
						}
						else
						{
							if (response.sessionactive == true)
							{
								displayTerminateSessionConfirmation(response.sessiontype, 'delete');
							}
							else
							{
								if (response.projectexists == true)
								{
									removeDeletedProject(response.projectref, false);
								}
								else
								{
									removeDeletedProject(response.projectref, true);
								}
							}
						}
					}
					break;
				case 'openonlineproject':

					var response = JSON.parse(xmlhttp.responseText);

					if (response.errorparam == '')
					{
						window.location.href =  response.brandurl;
					}
					else
					{
						var shimObj = document.getElementById('shim');

						if (shimObj)
						{
							shimObj.style.display = 'none';
						}

						alert(response.errorparam);
					}
					break;
				case 'completeorder':
					if (xmlhttp.responseText != '')
					{
						window.location.href =  xmlhttp.responseText;
					}
					break;
				default:
					document.getElementById(obj).innerHTML = xmlhttp.responseText;
					restoreFields();

					{/literal}{if $autosuggestavailable == 1}{literal}
					var as_city = {
						script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=city&country="+ country + "&statecode=" + regioncode,
						varname:"&input",
						cache:false,
						offsety:0,
						json:true,
						shownoresults:false,
						maxresults:20
					};

					var as_county = {
						script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=county&country="+ country + "&statecode=" + regioncode,
						varname:"&input",
						cache:false,
						offsety:0,
						json:true,
						shownoresults:false,
						maxresults:20
					};

					var as_state = {
						script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=state&country="+ country + "&statecode=" + regioncode,
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


                {literal}
            }

        }
    };
    xmlhttp.send(null);

    if (!async)
    {
        return xmlhttp.responseText;
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
    processAjax("ajaxdiv",".?fsaction=AjaxAPI.callback&cmd=ADDRESSFORM&ref=" + session + "&country=" + country + "&strict=1", true);
}

function verifyAddress()
{
    saveFields();

    {/literal}

    {if $autosuggestavailable == 1}

        {literal}

        processAjax("verify",".?fsaction=AjaxAPI.callback&cmd=ADDRESSVERIFICATION&ref=" + session + "&city=" + city + "&county=" + county + "&statecode=" + regioncode +
        "&statevalue=" + state + "&postcode=" + postcode + "&region=" + region + "&country=" + country, true);

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
        script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=city&country="+ country + "&statecode=" + regioncode,
        varname:"&input",
        cache:false,
        offsety:0,
        json:true,
        shownoresults:false,
        maxresults:20
    };
    var as_county = {
        script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=county&country="+ country+ "&statecode=" + regioncode,
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
                message = "{/literal}' + customMessage + '{literal}";
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
            createDialog("{/literal}{#str_TitleWarning#}{literal}", nlToBr(message), "closeDialog()");
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
    createCookie("mawebtz", Math.round(theDate.getTime() / 1000), 2);
{/literal}

{if $showgiftcardsbalance == true}

    {literal}

    var giftCardElm = document.getElementById('giftcardid');

    if (giftCardElm)
    {
        giftCardElm.onfocus = function(){
            if (this.value == "{/literal}{#str_LabelEnterCode#}{literal}")
            {
                this.value = '';
                this.className = 'inputGiftCard';
            }
        };

        giftCardElm.onblur = function(){
            if (this.value == '')
            {
                this.value = "{/literal}{#str_LabelEnterCode#}{literal}";
                this.className = 'inputGiftCard falseLabelColor';
            }
        };
    }

    {/literal}

{/if}

{literal}

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

function updateAccountDetails(pVerify)
{
    /* save address fields to javascript variables */
    saveFields();
    gAlerts = 0;
    var message = "{/literal}{#str_ErrorCompulsoryInformationMissing#}{literal}";
    /* test to see if address verification failed on something */
    if (pVerify != 'match')
    {
        section = pVerify.split(',');
        for (i in section)
        {
            highlight(section[i]+ 'compulsory');
        }
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
        highlight("email2");
    }
    if (document.getElementById('email_account').value != document.getElementById('email2').value)
    {
        message += "\n" + "{/literal}{#str_MessageCompulsoryEmailMismatch#}{literal}";
        highlight("email2");
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
        createDialog("{/literal}{#str_TitleWarning#}{literal}", nlToBr(message), "closeDialog()");
    }
    else
    {
        // show loading dialog
        showLoadingDialog();

        var postParams = '';
        postParams += '&contactfname=' + firstname;
        postParams += '&contactlname=' + lastname;

        postParams += '&companyname=' + company;
        postParams += '&address1=' + add1;
        postParams += '&address2=' + add2;
        postParams += '&address3=' + add3;
        postParams += '&address4=' + add4;
        postParams += '&add41=' + add41;
        postParams += '&add42=' + add42;
        postParams += '&add43=' + add43;
        postParams += '&city=' + city;
        postParams += '&county=' + county;
        postParams += '&state=' + state;
        postParams += '&regioncode=' + regioncode;
        postParams += '&region=' + region;
        postParams += '&postcode=' + postcode;
        postParams += '&countrycode=' + country;

        {/literal}

        {if $edit == 0}

            {literal}

        postParams += '&countryname=' + document.getElementById('countrylist').options[document.getElementById('countrylist').selectedIndex].text;

            {/literal}

        {else}

            {literal}

        postParams += '&countryname=' + countryName;
        postParams += '&countrycode=' + "{/literal}{$country}{literal}";
        postParams += '&regioncode=' + "{/literal}{$regioncode}{literal}";

            {/literal}

        {/if}

        {literal}

        postParams += '&telephonenumber=' + document.getElementById('telephonenumber_account').value;
        postParams += '&email=' + document.getElementById('email_account').value;
        postParams += '&registeredtaxnumbertype=' + registeredTaxNumberType;
        postParams += '&registeredtaxnumber=' + registeredTaxNumber;

        processAjaxSmallScreen("updateAction",".?fsaction=Customer.updateAccountDetails&ref=" + gSession, 'POST', postParams);
    }

    {/literal}

{else}

    {literal}

    if (gAlerts > 0)
    {
        alert(message);
        return false;
    }
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
    form.registeredtaxnumbertype.value = registeredTaxNumberType;
    form.registeredtaxnumber.value = registeredTaxNumber;
    form.fsaction.value = "Customer.updateAccountDetails";
    form.submit();
    return false;

    {/literal}

{/if}

{literal}

}

{/literal}

/* OPEN EXISTING PROJECT */

{if $section == 'existingonlineprojects'}

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
    theResult = theResult.replace(/\:/g, "");
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

function checkBrowserType()
{
    var browserOk = false;

    //Get the user agent of the browser.
    var browserUA = navigator.userAgent;

    if ((browserUA.match('OPR')=='OPR') || (browserUA.match('opera')=='opera'))
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

    if ((browserUA.match('Android')=='Android') && ((browserUA.match('Chrome')=='Chrome') == false))
    {
        browserOk = false;
    }

    if ((browserUA.match('Chrome')== 'Chrome') && ((browserUA.match('Android')=='Android') == false))
    {
        browserOk = true;

        if ((browserUA.match('iPhone')=='iPhone'))
        {
            browserOk = false;
        }
        else if ((browserUA.match('iPod')=='iPod'))
        {
            browserOk = false;
        }
        else if ((browserUA.match('iPad')=='iPad'))
        {
            browserOk = false;
        }
    }

    if ((browserUA.match('Safari')=='Safari') && ((browserUA.match('Chrome')== 'Chrome')==false))
    {
        browserOk = true;

        if ((browserUA.match('iPhone')=='iPhone'))
        {
            browserOk = true;
        }
        else if ((browserUA.match('iPod')=='iPod'))
        {
            browserOk = true;
        }
        else if ((browserUA.match('iPad')=='iPad'))
        {
            browserOk = true;
        }

        //If less than version 5 dont allow
        if( parseInt((browserUA.split('Version/')[1]).split(' ')[0]) > 5 )
        {
            browserOk = true;
        }
        else
        {
            browserOk = false;
        }
    }

    if (((browserUA.match('msie')=='msie') || (browserUA.match('MSIE') == 'MSIE')) && ((browserUA.match('opera')=='opera') == false) )
    {
        browserOk = false;

        // Internet Explorer versions before 11
        if (parseInt((browserUA.split('MSIE')[1]).split(';')[0]) >= 10)
        {
            browserOk = true;
        }
    }

    if ((browserUA.match('Trident')=='Trident') && (browserUA.match('rv:')=='rv:'))
    {
        // Internet Explorer versions 11+
        browserOk = true;
    }

    return browserOk;
}

{/literal}

{/if}

 /* END OPEN EXISTING PROJECT */

/* CHANGE PASSWORD */

{if $section=='changepassword'}

    {literal}

function checkFormChangePassword()
{
    gAlerts = 0;
    var message = "{/literal}{#str_ErrorCompulsoryInformationMissing#}{literal}";
    var oldpassword = document.getElementById('oldpassword');
    var newpassword = document.getElementById('newpassword');
    var newpassword2 = document.getElementById('newpassword2');

    oldpassword.className = oldpassword.className.replace("errorInput", "");
    newpassword.className = newpassword.className.replace("errorInput", "");
    newpassword2.className = newpassword2.className.replace("errorInput", "");

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

    if (newpassword2.value.length == 0)
    {
        message += "\n" + "{/literal}{#str_ErrorNoNewPasswordConfirmation#}{literal}";
        highlight("newpassword2");
    }

    if (newpassword.value != newpassword2.value)
    {
        message += "\n" + "{/literal}{#str_MessageCompulsoryPasswordMismatch#}{literal}";
        highlight("newpassword");
        highlight("newpassword2");
    }

    if (oldpassword.value == newpassword.value)
    {
        message += "\n" + "{/literal}{#str_ErrorPasswordsSame#}{literal}";
        highlight("newpassword");
    }

    {/literal}

    {if $issmallscreen == 'true'}

        {literal}

    if (gAlerts > 0)
    {
        createDialog("{/literal}{#str_TitleWarning#}{literal}", nlToBr(message), "closeDialog()");
    }
    else
    {
        var format = ((document.location.protocol != 'https:') ? 0 : 0); /*lpc*/

        // show loading dialog
        showLoadingDialog();

        // send the ajax
        postParams = '&format=' + format;
        postParams += '&data1=' + ((format == 0) ? oldpassword.value : hex_md5(oldpassword.value));
        postParams += '&data2=' + ((format == 0) ? newpassword.value : hex_md5(newpassword.value));
        processAjaxSmallScreen("updateAction",".?fsaction=Customer.updatePassword&ref=" + gSession, 'POST', postParams);
    }

    {/literal}

	{else}

		{literal}

    if (gAlerts > 0)
    {
        alert(message);
        return false;
    }

    var format = ((document.location.protocol != 'https:') ? 0 : 0);

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


/* END GENERIC */

{if $issmallscreen == 'true'}

    {* SMALL SPECIFIC FUNCTION *}

    {if ($section=='menu') || ($section == 'accountdetails')}

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
                processAjaxSmallScreen("menuAjaxAction",".?fsaction=Customer.accountDetails&ref=" + gSession, 'POST', '');
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
            postParams = '&giftcardcode=' + giftcardtext;
            postParams += '&giftcardaction=' +  "{/literal}{$section}{literal}";
            postParams += '&showgiftcardmessage=' +  1;

            // show loading dialog
            showLoadingDialog();

            // send the ajax
            processAjaxSmallScreen("redeemgiftcard",".?fsaction=Customer.updateGiftCard&ref=" + gSession, 'POST', postParams);
        }
    }

    function menuAction(pActionUrl, pActionIn)
    {
        pAction = pActionIn // Added for use with eventlistener in mobilefunction.tpl

        // show loading dialog
        showLoadingDialog();

        // send the ajax
        processAjaxSmallScreen("menuAjaxAction",".?fsaction=" + pActionUrl + "&ref=" + gSession, 'POST', '');

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

        // tick content
        var contentTick = document.getElementsByClassName('checkboxImage')[0];
        var styleTick = contentTick.currentStyle || window.getComputedStyle(contentTick);
        var width = gOuterBoxContentBloc - parseIntStyle(styleTick.width) - parseIntStyle(styleTick.marginLeft) - parseInt(styleTick.marginRight);

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
        if ((document.getElementById('subscribeYes').checked) || (document.getElementById('subscribeNo').checked))
        {
            // show loading dialog
            showLoadingDialog();

            // send the ajax
            postParams = '&sendmarketinginfo=' + (document.getElementById('subscribeYes').checked ? '1' : '0');

            processAjaxSmallScreen("updateAction",".?fsaction=Customer.updatePreferences&ref=" + gSession, 'POST', postParams);
        }
        else
        {
            createDialog("{/literal}{#str_TitleWarning#}{literal}", "{/literal}{#str_MessagePleaseSelectAnOption#}{literal}", "closeDialog()");
        }
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

        processAjaxSmallScreen("addressForm",".?fsaction=AjaxAPI.callback&cmd=ADDRESSFORM&ref=" + session + "&country=" + country + "&hideconfigfields=" + hideConfigFields +"&strict=1&edit={/literal}{$edit}{literal}", 'GET', '');
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

    function checkPasswordsMatch()
    {
        if (document.getElementById('sharepassword').checked)
        {
            var previewPasswordObj = document.getElementById('previewPassword');
            var previewPassword2Obj = document.getElementById('previewPassword2');

            if (previewPasswordObj && previewPassword2Obj)
            {
                if (((previewPasswordObj.value != '') || (previewPassword2Obj.value != '')) && (previewPasswordObj.value != previewPassword2Obj.value))
                {
                    highlight("previewPassword");
                    highlight("previewPassword2");
                    return false;
                }
                else
                {
                    if ((previewPasswordObj.value == '') || (previewPassword2Obj.value == ''))
                    {
                        highlight("previewPassword");
                        highlight("previewPassword2");
                        return false;
                    }
                }
            }
        }
        return true;
    }

    function executeButtonAction(obj, windowTarget, pProjectName, pProductName)
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
        else if (windowTarget == 3)
        {
            openOnlineProject();
        }
        else
        {
            if (addToAnyIntialised == false)
            {
                reinitAddtoany();
            }

            shareObjLink = obj;
            shareName = pProjectName + ' (' + pProductName + ')';
            showSharePanel(pProjectName);
        }
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
        document.getElementById('previewPassword2').value = '';

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
        processAjaxSmallScreen("unshare", ".?fsaction=Share.unshare&ref=" + session + '&orderItemId='+ orderItemID, 'GET', '');
    }

    function shareByEmail()
    {
        gAlerts = 0;
        var messageError = "{/literal}{#str_ErrorCompulsoryInformationMissing#}{literal}";
        if (!checkPasswordsMatch())
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

        if (gAlerts == 0)
        {
            var message = '<img src="{/literal}{$webroot}{literal}/images/loading_shoppingcart.gif" class="loading-icon" alt="{/literal}{#str_MessageLoading#}{literal}" />';
            message += "&nbsp;{/literal}{#str_MessageSendingEmail#}{literal}";

            createDialog("{/literal}{#str_LabelConfirmation#}{literal}", message, "closeDialogShare()");
            document.getElementById('dialogBtn').style.display = 'none';

            var previewPasswordValue = '';
            if (document.getElementById('sharepassword').checked)
            {
                var previewPasswordObj = document.getElementById('previewPassword');
                if ((previewPasswordObj) && (previewPasswordObj.value != ''))
                {
                    previewPasswordValue = hex_md5(previewPasswordObj.value);
                }
            }

        {/literal}

            {if $sharebyemailmethod == 1}

                {literal}

                    // email send by control center
                    processAjaxSmallScreen("shareByEmail", ".?fsaction=Share.shareByEmail&ref=" + session + '&orderItemId='+ orderItemID + '&title='+encodeURIComponent(emailTitle) + '&recipients='+encodeURIComponent(emailRecipients) + '&message='+encodeURIComponent(shareByEmailText) + '&previewPassword='+encodeURIComponent(previewPasswordValue), 'GET', '');

                {/literal}

            {else}

                {literal}

                    // mailto link
                    processAjaxSmallScreen("mailToLink", ".?fsaction=Share.mailTo&ref=" + session + '&orderItemId='+ orderItemID + '&title='+encodeURIComponent(emailTitle) + '&recipients='+encodeURIComponent(emailRecipients) + '&message='+encodeURIComponent(shareByEmailText) + '&previewPassword='+encodeURIComponent(previewPasswordValue), 'GET', '');

                {/literal}

            {/if}

        {literal}

        }
        else
        {
            createDialog("{/literal}{#str_TitleWarning#}{literal}", messageError, "closeDialog()");
        }
    }

    function closeDialogShare()
    {
        closeDialog();
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

    function checkWizardDevice(pWizStageIn, pWizModeIn, pWorkTypeIn)
    {
        var wizCheck = false;
        var largeScreen = true;
        //Check to see if we are on a large or small screen
        if ( Math.min(parseInt(screen.width) , parseInt(screen.height)) > 700 )
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
        if ((parseInt(pWorkTypeIn) == 0) && (parseInt(pWizModeIn) <2))
        {
            if(largeScreen)
            {
                wizCheck = true;
            }
            else
            {
                wizCheck = false;
            }
        }
        if ((pWorkTypeIn == 0) && (pWizModeIn >=2))
        {
            wizCheck = true;
        }
        return wizCheck;
    }

    function onlineProjectsButtonAction(pButtonClicked, pWizStage, pWizMode, pWorkType)
    {
        if ((pButtonClicked == 'completeorder') || (pButtonClicked == 'continueediting'))
        {
            var browserCompatability = checkBrowserType();
            var wizOK = checkWizardDevice(pWizStage, pWizMode, pWorkType);

            if (browserCompatability == false)
            {
                createDialog("{/literal}{#str_TitleBrowserCompatibilityIssue#}{literal}", "{/literal}{#str_ErrorBrowserCompatibilityIssue#}{literal}", "closeDialog()");
            }
            else
            {
                if (wizOK == 0)
                {
                    createDialog("{/literal}{#str_TitleDeviceCompatibilityIssue#}{literal}", "{/literal}{#str_ErrorDeviceCompatibilityIssue#}{literal}", "closeDialog()");
                }
            }
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
                var name = document.getElementById('pageLabel' + gActiveProductOnline).innerHTML;
                name = name.trim();
                showOnlineNameForm(true, "{/literal}{#str_LabelRenameProject#}{literal}", "{/literal}{#str_ButtonRenameProject#}{literal}", name);
            break;
            case 'duplicate':
                gNameFormAction = 'duplicate';
                var name = document.getElementById('pageLabel' + gActiveProductOnline).innerHTML;
                name = name.trim();
                showOnlineNameForm(true, "{/literal}{#str_LabelDuplicateProject#}{literal}", "{/literal}{#str_ButtonDuplicateProject#}{literal}", name);
            break;
            case 'delete':
                var confirmDeleteMessage = "{/literal}{#str_MessageDeleteProjectConfirmation#}{literal}";
                var name = document.getElementById('pageLabel' + gActiveProductOnline).innerHTML;
                name = name.trim();
                confirmDeleteMessage = confirmDeleteMessage.replace('^0', "'" + name + "'");
                showConfirmDialog("{/literal}{#str_LabelDeleteProject#}{literal}", confirmDeleteMessage, "checkDeleteSession(0, 'delete');");
            break;
        }
    }

    function completeOrder()
    {
        var divObject = document.getElementById('onlineProjectDetail' + gActiveProductOnline);

        var projectRef = gActiveProductOnline;
        var workflowType = divObject.getAttribute("data-workflowtype");
        var productIndent = divObject.getAttribute("data-productident");

        processAjaxSmallScreen("completeorder", ".?fsaction=AjaxAPI.callback&cmd=COMPLETEORDER&ref=" + session + '&projectref='+ projectRef + '&workflowtype=' + workflowType + '&productindent=' + productIndent, 'GET', '');
    }

    function checkDeleteSession(pForceKill, pAction)
    {
		closeDialog();
        showLoadingDialog();

        var projectRef = gActiveProductOnline;
        processAjaxSmallScreen("checkDeleteSession" + pAction, ".?fsaction=AjaxAPI.callback&cmd=CHECKDELETESESSION&ref=" + session + '&projectref='+ projectRef + '&forcekill=' + pForceKill + '&action=' + pAction, "GET", "");
    }

	function removeDeletedProject(pProjectref, pDisplayMessage)
	{
		closeLoadingDialog();

		if (pDisplayMessage)
		{
			createDialog("{/literal}{#str_TitleWarning#}{literal}", "{/literal}{#str_ErrorProjectHasBeenDeleted#}{literal}", "closeDialog()");
		}

		var selectedProject = document.getElementById('contentItemBloc' + gActiveProductOnline);
		selectedProject.parentNode.removeChild(selectedProject);
		showOnlineOptions(false, '');
	}

	function changeCanModify(pProjectref)
	{
		closeLoadingDialog();

		createDialog("{/literal}{#str_TitleWarning#}{literal}", "{/literal}{#str_ErrorOrderInProduction#}{literal}", "closeDialog()");

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
		showConfirmDialog("{/literal}{#str_TitlePleaseConfirm#}{literal}", message, "checkDeleteSession(1, '" + pAction + "');");
    }


    function openExistingOnlineProject()
    {
        var divObject = document.getElementById('onlineProjectDetail' + gActiveProductOnline);

        var projectRef = gActiveProductOnline;
        var workflowType = divObject.getAttribute("data-workflowtype");
        var productIndent = divObject.getAttribute("data-productident");

        processAjaxSmallScreen("openonlineproject", ".?fsaction=AjaxAPI.callback&cmd=OPENONLINEPROJECT&ref=" + session + '&projectref='+ projectRef + '&workflowtype=' + workflowType + '&productindent=' + productIndent, 'GET', '');
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
                processAjaxSmallScreen("renameonlineproject", ".?fsaction=AjaxAPI.callback&cmd=RENAMEONLINEPROJECT&ref=" + session + '&projectref='+ gActiveProductOnline + '&projectname=' + encodeURIComponent(projectName), 'GET', '');
            }
            else
            {
                showLoadingDialog();
                var divObject = document.getElementById('onlineProjectDetail' + gActiveProductOnline);
                var workflowType = divObject.getAttribute("data-workflowtype");
                var productIndent = divObject.getAttribute("data-productident");
                processAjaxSmallScreen("duplicateonlineproject", ".?fsaction=AjaxAPI.callback&cmd=DUPLICATEONLINEPROJECT&ref=" + session + '&projectref='+ gActiveProductOnline + '&projectname=' + encodeURIComponent(projectName) + '&workflowtype=' + workflowType, 'GET', '');
            }
        }
        else
        {
            var errorMessage = document.getElementById('onlineNameFormError');
            errorMessage.innerHTML = "{/literal}{#str_ErrorNoProjectName#}{literal}";
            errorMessage.style.display = 'block';
        }
    }

        {/literal}

    {/if} {* end {if $section == 'existingonlineprojects'} *}

    {* END SMALL SPECIFIC FUNCTION *}

{else} {* else {if $issmallscreen == 'true'} *}

    {* LARGE SPECIFIC FUNCTION *}

    {literal}

    window.onload = function()
    {

    {/literal}

        {if ($section == 'existingonlineprojects') && ($projects|@sizeof > 0)}

            {literal}

        calcualteScrollableView();

            {/literal}

        {/if}

    {literal}

        onloadWindow()

        /* ACCOUNT DETAILS */

    {/literal}

        {if $section=='accountdetails'}

            {literal}

        processAjax("ajaxdiv",".?fsaction=AjaxAPI.callback&cmd=ADDRESSFORM&ref=" + session + "&country=" + country + "&hideconfigfields=" + hideConfigFields +"&strict=1&edit={/literal}{$edit}{literal}", true);
        document.getElementById('updateButton').onclick = verifyAddress;

            {/literal}

        {/if}

        /* END ACCOUNT DETAILS */

        /* CHANGE PASSWORD */

        {if $section=='changepassword'}

            {literal}

        document.getElementById('updateButton').onclick = checkFormChangePassword;

            {/literal}

        {/if}

        /* END CHANGE PASSWORD */

        /* CHANGE PREFERENCES */

        {if $section=='changepreferences'}

            {literal}

        document.getElementById('updateButton').onclick = checkFormChangePreferences;

            {/literal}

        {/if}

        {literal}

        if (document.getElementById('backButton'))
        {
            document.getElementById('backButton').onclick = function(){
                document.submitform.fsaction.value = 'Customer.initialize';
                document.submitform.submit();
                return false;
            }
        }

        var homeLink = document.getElementById('homeLink');
        if (homeLink)
        {
            document.getElementById('homeLink').onclick = function(){
                document.submitform.fsaction.value = 'Customer.initialize';
                document.submitform.submit();
                return false;
            }
        }

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
        changeSystemLanguage("{/literal}{$refreshaction}{literal}");
    }


    function showLoadingDialog(pTitle)
    {
        document.getElementById('loadingTitle').innerHTML = pTitle;
        var loadingBoxObj = document.getElementById('loadingBox');
        var shimObj = document.getElementById('shimLoading');

        loadingBoxObj.style.display = 'block';
        if (shimObj)
        {
            shimObj.style.display = 'block';
            shimObj.style.height = document.body.offsetHeight + 'px';
            document.body.className +=' hideSelects';
        }

        loadingBoxObj.style.left = Math.round((shimObj.offsetWidth / 2) - (loadingBoxObj.offsetWidth / 2)) + 'px';
        var windowHeight = document.documentElement.clientHeight;
        loadingBoxObj.style.top = Math.round((windowHeight - loadingBoxObj.offsetHeight) / 2) + 'px';
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
        if ((document.getElementById('subscribeYes').checked) || (document.getElementById('subscribeNo').checked)) {
            document.submitform.fsaction.value = 'Customer.updatePreferences';
            document.submitform.sendmarketinginfo.value = (document.getElementById('subscribeYes').checked) ? '1' : '0';
            document.submitform.submit();
        }
        else
        {
            alert("{/literal}{#str_MessagePleaseSelectAnOption#}{literal}");
        }
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
        if (!checkPasswordsMatch())
        {
            alert("{/literal}{#str_MessagePasswordInformation#}{literal}");

            return{
                stop: true
            }
        }
        else
        {
            var previewPasswordValue = '';
            if( document.getElementById('sharepassword').checked)
            {
                var previewPasswordObj = document.getElementById('previewPassword');
                if ((previewPasswordObj) && (previewPasswordObj.value != ''))
                {
                    previewPasswordValue = hex_md5(previewPasswordObj.value);
                }
            }

            closeConfirmationBox();

            if (shareObjLink)
            {
                var prevSibling = shareObjLink.previousSibling;
                while(prevSibling && prevSibling.nodeType != 1)
                {
                    prevSibling = prevSibling.previousSibling;
                }
                prevSibling.style.display = 'block';
            }

            var newURl = processAjax("shareurl",".?fsaction=Share.shareAddToAny&ref=" + session +'&orderItemId=' + orderItemID + '&method=' + encodeURIComponent(pData.service) + '&previewPassword='+ encodeURIComponent(previewPasswordValue), false);

			return{
                url: newURl,
                title: shareName
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
    }

    function checkPasswordsMatch()
    {

        if (document.getElementById('sharepassword').checked)
        {
            var previewPasswordObj = document.getElementById('previewPassword');
            var previewPassword2Obj = document.getElementById('previewPassword2');

            if (previewPasswordObj && previewPassword2Obj)
            {
                if (((previewPasswordObj.value != '') || (previewPassword2Obj.value != '')) && (previewPasswordObj.value != previewPassword2Obj.value))
                {
                    highlight("previewPassword");
                    highlight("previewPassword2");
                    return false;
                }
                else
                {
                    if ((previewPasswordObj.value == '') || (previewPassword2Obj.value == ''))
                    {
                        highlight("previewPassword");
                        highlight("previewPassword2");
                        return false;
                    }
                }
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

		processAjax("unshare", ".?fsaction=Share.unshare&ref=" + session + '&orderItemId='+ orderItemID, true);
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
            shimObj.style.display = 'none';
            shimObj.style.zIndex = 100;
        }
        if (dialogBoxObj)
        {
            dialogBoxObj.style.display = 'none';
        }
        if (confirmationBoxObj)
        {
            confirmationBoxObj.style.display = 'none';
        }
        if (shimIframe)
        {
            shimIframe.style.display = 'none';
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

    function openDialogBox(pProjectName)
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
        document.getElementById('previewPassword2').setAttribute("disabled","disabled");
        document.getElementById('previewPassword').value = '';
        document.getElementById('previewPassword2').value = '';
        document.getElementById('previewPasswordcompulsory2').style.display = "none";
        document.getElementById('previewPassword2compulsory2').style.display = "none";
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
        if (!checkPasswordsMatch())
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
            confirmationBoxTextObj.innerHTML = '<img src="{/literal}{$webroot}{literal}/images/loading_shoppingcart.gif" class="loading-icon" alt="{/literal}{#str_MessageLoading#}{literal}" />' +  "&nbsp;{/literal}{#str_MessageSendingEmail#}{literal}";

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

            var previewPasswordValue = '';
            if (document.getElementById('sharepassword').checked)
            {
                var previewPasswordObj = document.getElementById('previewPassword');
                if ((previewPasswordObj) && (previewPasswordObj.value != ''))
                {
                    previewPasswordValue = hex_md5(previewPasswordObj.value);
                }
            }

        {/literal}

            {if $sharebyemailmethod == 1}

                {literal}

                    // email send by control center
					processAjax("shareByEmail", ".?fsaction=Share.shareByEmail&ref=" + session + '&orderItemId='+ orderItemID + '&title='+encodeURIComponent(emailTitle) + '&recipients='+encodeURIComponent(emailRecipients) + '&message='+encodeURIComponent(shareByEmailText) + '&previewPassword='+encodeURIComponent(previewPasswordValue), true);

                {/literal}

            {else}

                {literal}

                    // mailto link
					processAjax("mailToLink", ".?fsaction=Share.mailTo&ref=" + session + '&orderItemId='+ orderItemID + '&title='+encodeURIComponent(emailTitle) + '&recipients='+encodeURIComponent(emailRecipients) + '&message='+encodeURIComponent(shareByEmailText) + '&previewPassword='+encodeURIComponent(previewPasswordValue), true);

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
            document.getElementById('previewPassword2').disabled = false;
            document.getElementById('previewPasswordcompulsory2').style.display = "inline-block";
            document.getElementById('previewPassword2compulsory2').style.display = "inline-block";
        }
        else
        {
            document.getElementById('previewPassword').setAttribute("disabled","disabled");
            document.getElementById('previewPassword2').setAttribute("disabled","disabled");
            document.getElementById('previewPasswordcompulsory2').style.display = "none";
            document.getElementById('previewPassword2compulsory2').style.display = "none";
            document.getElementById('previewPassword').className = document.getElementById('previewPassword').className.replace(" errorInput", "");
            document.getElementById('previewPassword2').className = document.getElementById('previewPassword2').className.replace(" errorInput", "");
        }
    }

    function executeButtonAction(obj, pOrderItemID, windowTarget, pProjectName, pProductName)
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
        else if (windowTarget == 3)
        {
            openOnlineProject();
        }
        else
        {
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
                shimObj.style.display = 'block';
            }

            shareObjLink = obj;
            shareName = pProjectName + ' (' + pProductName + ')';

            openDialogBox(pProjectName);
        }

        return false;
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
    }

    function selectProject(pSelectedProject)
    {
        var projectRef = pSelectedProject.id;
        var projectName = document.getElementById(projectRef).getAttribute("data-projectname");
        var projectWorkflowType = document.getElementById(projectRef).getAttribute("data-workflowtype");
        var productIndent = document.getElementById(projectRef).getAttribute("data-productident");

        document.getElementById('projectnamehidden').value = projectName;
        document.getElementById('projectrefhidden').value = projectRef;
        document.getElementById('projectworkflowtype').value = projectWorkflowType;
        document.getElementById('productindent').value = productIndent;

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
        }
        document.getElementById('renameBtnLeft').className = 'btn-white-left';
        document.getElementById('renameBtnMiddle').className = 'btn-white-middle btnOnlineMiddle';
        document.getElementById('renameBtnRight').className = 'btn-white-right';

        document.getElementById('duplicateBtn').onclick = function()
        {
            onlineProjectsButtonAction('duplicateproject');
        }
        document.getElementById('duplicateBtnLeft').className = 'btn-white-left';
        document.getElementById('duplicateBtnMiddle').className = 'btn-white-middle btnOnlineMiddle';
        document.getElementById('duplicateBtnRight').className = 'btn-white-right';

        pSelectedProject.className = 'contentRow selectedRow';
    }

	function setActiveButtonsFromStatus(pCaneEdit, pCanDelete, pCancompleteOrder)
	{
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
        }

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
           // document.getElementById('completeBtnLeft').className = 'btn-green-left';
            document.getElementById('completeBtnMiddle').className = 'btn-green-middle btnOnlineMiddle';
            //document.getElementById('completeBtnRight').className = 'btn-green-right';
        }

	}

    function closeDialogBox()
    {
        var shimObj = document.getElementById('shim');
        var dialogBoxObj = document.getElementById('dialogBox');

        if (shimObj)
        {
            shimObj.style.display = 'none';
            shimObj.style.zIndex = 100;
        }
        if (dialogBoxObj)
        {
            dialogBoxObj.style.display = 'none';
        }
    }

    function onlineProjectsButtonAction(pButtonClicked)
    {
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

                var dilaogBox = document.getElementById('dialogBox');
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
            openDialogBox(pButtonClicked);
        }

        return false;
    }


    function openDialogBox(pButtonClicked)
    {
        if (pButtonClicked == 'continueediting')
        {
            checkDeleteSession(0, 'editing');
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

            var dilaogBox = document.getElementById('dialogBox');
            var shimObj = document.getElementById('shim');
            if ((pButtonClicked == 'rename') || (pButtonClicked == 'duplicateproject'))
            {
                document.getElementById('projectname_container').innerHTML = '<label for="projectname">' + "{/literal}{#str_LabelProjectName#}{literal}:" + '</label><input type="text" name="projectname" id="projectname" maxlength="75"/>';
                var projectNameElement = document.getElementById('projectname');
				projectNameElement.value = document.getElementById('projectnamehidden').value;

                document.getElementById('projectacceptbutton').innerHTML = '<div class="btn-green-left" ></div>';
                document.getElementById('projectcancelbutton').innerHTML = '<div class="btn-red-cross-left" ></div><div class="btn-red-right"></div>';

				if (pButtonClicked == 'duplicateproject')
				{
					projectNameElement.addEventListener("keydown", function(e)
					{
						if (e.keyCode == 13)
						{
							duplicateProject();
						}
					}, false);

					document.getElementById('projectacceptbutton').onclick = function()
					{
						duplicateProject();
					}
				}
				else
				{
					projectNameElement.addEventListener("keydown", function(e)
					{
						if (e.keyCode == 13)
						{
							renameExistingOnlineProject();
						}
					}, false);

					document.getElementById('projectacceptbutton').onclick = function()
					{
						renameExistingOnlineProject();
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


    function checkDeleteSession(pForceKill, pAction)
    {
        showLoadingDialog('{/literal}{#str_MessageLoading#}{literal}');
        var projectRef = document.getElementById('projectrefhidden').value;

		processAjax("checkDeleteSession" + pAction, ".?fsaction=AjaxAPI.callback&cmd=CHECKDELETESESSION&ref=" + session + '&projectref='+ projectRef + '&forcekill=' + pForceKill + '&action=' + pAction, true);
    }


    function completeOrder()
    {
        var projectRef = document.getElementById('projectrefhidden').value;
        var workflowType = document.getElementById('projectworkflowtype').value;
        var productIndent = document.getElementById('productindent').value;
		processAjax("completeorder", ".?fsaction=AjaxAPI.callback&cmd=COMPLETEORDER&ref=" + session + '&projectref='+ projectRef + '&workflowtype=' + workflowType + '&productindent=' + productIndent, true);
    }


    function duplicateProject()
    {
        var projectName = document.getElementById('projectname').value;
        var projectRef = document.getElementById('projectrefhidden').value;

        projectName = correctFileName(projectName);
        document.getElementById('projectname').value = projectName;
        if (projectName != '')
        {
            showLoadingDialog("{/literal}{#str_TitleDuplicatingProject#}{literal}");
			processAjax("duplicateonlineproject", ".?fsaction=AjaxAPI.callback&cmd=DUPLICATEONLINEPROJECT&ref=" + session + '&projectref='+ projectRef + '&projectname=' + encodeURIComponent(projectName), true);
        }
        else
        {
            var shimObj = document.getElementById('shim');
            shimObj.style.zIndex = 201;
            showConfirmationBox("{/literal}{#str_ErrorNoProjectName#}{literal}");
        }
    }


	function removeDeletedProject(pProjectref, pDisplayMessage)
	{
		closeDialogBox();

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
		//document.getElementById('completeBtnLeft').className = 'btn-disabled-left';
		document.getElementById('completeBtnMiddle').className = 'btn-disabled-middle btnOnlineMiddle';
		//document.getElementById('completeBtnRight').className = 'btn-disabled-right';

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

		document.getElementById('duplicateBtn').onclick = '';
		document.getElementById('duplicateBtnLeft').className = 'btn-disabled-left';
		document.getElementById('duplicateBtnMiddle').className = 'btn-disabled-middle btnOnlineMiddle';
		document.getElementById('duplicateBtnRight').className = 'btn-disabled-right';
	}


	function changeCanModify(pProjectref)
	{
		closeDialogBox();

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
		document.getElementById('projectacceptbutton').onclick = function(){
			checkDeleteSession(1, pAction);
		};

		document.getElementById('projectacceptbutton').innerHTML = '<div class="btn-green-middle" >' + "{/literal}{#str_ButtonContinue#}{literal}" + '</div>';
		document.getElementById('projectcancelbutton').innerHTML = '<div class="btn-red-cross-left" ></div><div class="btn-red-middle">' + "{/literal}{#str_ButtonDoNotContinue#}{literal}" + '</div><div class="btn-red-right"></div>';

		var dilaogBox = document.getElementById('dialogBox');
		var shimObj = document.getElementById('shim');
		if (dilaogBox && shimObj)
		{
			dilaogBox.style.display = 'block';
			dilaogBox.style.left = Math.round(shimObj.offsetWidth / 2 - dilaogBox.offsetWidth / 2) + 'px';
			var windowHeight = document.documentElement.clientHeight;
			dilaogBox.style.top = Math.round((windowHeight - dilaogBox.offsetHeight) / 2) + 'px';
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
		document.getElementById('projectacceptbutton').innerHTML = '<div class="btn-green-middle" >' + "{/literal}{#str_ButtonYes#}{literal}" + '</div>';
		document.getElementById('projectcancelbutton').innerHTML = '<div class="btn-red-cross-left" ></div><div class="btn-red-middle">' + "{/literal}{#str_ButtonNo#}{literal}" + '</div><div class="btn-red-right"></div>';
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
			processAjax("renameonlineproject", ".?fsaction=AjaxAPI.callback&cmd=RENAMEONLINEPROJECT&ref=" + session + '&projectref='+ projectRef + '&projectname=' + encodeURIComponent(projectName), true);
        }
        else
        {
            var shimObj = document.getElementById('shim');
            shimObj.style.zIndex = 201;
            showConfirmationBox("{/literal}{#str_ErrorNoProjectName#}{literal}");
        }
    }


    function openExistingOnlineProject()
    {
        var projectWorkflowType = document.getElementById('projectworkflowtype').value;
        var projectRef = document.getElementById('projectrefhidden').value;
        var productIndent = document.getElementById('productindent').value;
		processAjax("openonlineproject", ".?fsaction=AjaxAPI.callback&cmd=OPENONLINEPROJECT&ref=" + session + '&projectref='+ projectRef + '&workflowtype=' + projectWorkflowType + '&productindent=' + productIndent, true);
    }

        {/literal}

    {/if}

    /* END OPEN EXISTING PROJECT */

    {* END LARGE SPECIFIC FUNCTION *}

{/if} {* end {if $issmallscreen == 'true'} *}