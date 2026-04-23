{literal}

var gAs_jsonCity = '';
var gAs_jsonCounty = '';
var gSession = "{/literal}{$session}{literal}";
var gIsMobile = "{/literal}{$issmallscreen}{literal}";
var gSSOToken = "{/literal}{$ssotoken}{literal}";
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
var gAlerts = 0;
var showTermsAndConditions = "{/literal}{$showtermsandconditions}{literal}";

var TPX_REGISTEREDTAXNUMBERTYPE_NA = "{/literal}{$TPX_REGISTEREDTAXNUMBERTYPE_NA}{literal}";
var TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL = "{/literal}{$TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL}{literal}";
var TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE = "{/literal}{$TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE}{literal}";
var lastSuccesfulCountry = country;

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

window.addEventListener('DOMContentLoaded', function(event) {
    document.body.addEventListener('keyup', decoratorListener);
    document.body.addEventListener('change', decoratorListener);
});

/*		A J A X */
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
function processAjax(obj, serverPage)
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

    xmlhttp.open("GET", serverPage+"&dummy=" + new Date().getTime(), true);

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
            if (obj == 'termsandconditionswindow')
            {
                window.scroll(0,0);
                document.getElementById(obj).innerHTML = unescape(xmlhttp.responseText);

                var contentWrapperObj = document.getElementById('contentHolder');
                var shimObj = document.getElementById('shim');
                var termsAndConditionsObj = document.getElementById('ordersTermsAndCondtions');

                if (contentWrapperObj)
                {
                    shimObj.style.display = 'block';
                    termsAndConditionsObj.style.display = 'block';
                    termsAndConditionsObj.style.left = Math.round(shimObj.offsetWidth / 2 - termsAndConditionsObj.offsetWidth/2)+'px';

                    var viewportHeight =  Math.max(
                        Math.max(document.body.offsetHeight, document.documentElement.offsetHeight),
                        Math.max(document.body.clientHeight, document.documentElement.clientHeight)
                    );
                    viewportHeight = document.documentElement.clientHeight;
                    termsAndConditionsObj.style.top = Math.round(viewportHeight / 2 - termsAndConditionsObj.offsetHeight/2) + 'px';
                }

                if (shimObj)
                {
                    var docHeight =  Math.max(
                        Math.max(document.body.scrollHeight, document.documentElement.scrollHeight),
                        Math.max(document.body.offsetHeight, document.documentElement.offsetHeight),
                        Math.max(document.body.clientHeight, document.documentElement.clientHeight)
                    );
                    shimObj.style.height = docHeight + 'px';
                    document.body.className +=' hideSelects';
                }

            }
            else if(obj == 'verify')
            {
                updateAccountDetails(xmlhttp.responseText);
            }
            else
            {
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

                document.getElementById(obj).innerHTML = xmlhttp.responseText;
                restoreFields();

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

                gAs_jsonCity = new bsn.AutoSuggest('maincity', as_city);
                gAs_jsonCounty = new bsn.AutoSuggest('maincounty', as_county);
                gAs_jsonState = new bsn.AutoSuggest('mainstate', as_state);

        {/literal}

    {/if}

    {if $issmallscreen == 'true'}

        {literal}

                initializeNewAccountSmallScreen();

        {/literal}

    {/if} {* end {if $issmallscreen == 'true'} *}

 {* If communication successful update the lastSuccessfulCOuntry variable with the selected country *}
			{literal}
				if (obj == 'ajaxdiv')
				{
					lastSuccesfulCountry = document.getElementById("countrylist").value;
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
    xmlhttp.send(null);
}

function setCountry()
{
    /* save field data so it can be restored afterwards */
    saveFields();

    processAjax("ajaxdivupdate",".?fsaction=AjaxAPI.callback&cmd=ADDRESSFORM&country=" + country + "&addresstype=billing&strict=1&ishighlevel={/literal}{$ishighlevel}{literal}&tablewidth={/literal}{$tablewidth}{literal}");
}

function saveFields()
{
    /* remove message */
    var message = document.getElementById("message");
    if (message)
    {
        message.innerHTML = '';
    }

    /* set all missing.png back to asterisk.png */
    var images = document.getElementsByTagName("img");
    for ( var t = 0; t < images.length; ++t )
    {
        if (images[t].src.indexOf("/images/missing.png") != -1)
        {
            images[t].src = "{/literal}{$webroot}{literal}/images/asterisk.png";
        }
    }

    /* save field data before AJAX call or submission */
    var elFirstname = document.getElementById("maincontactfname");
    var elLastname = document.getElementById("maincontactlname");
    var elCompany = document.getElementById("maincompanyname");
    var elAdd1 = document.getElementById("mainaddress1");
    var elAdd2 = document.getElementById("mainaddress2");
    var elAdd3 = document.getElementById("mainaddress3");
    var elAdd4 = document.getElementById("mainaddress4");
    var elAdd41 = document.getElementById("mainadd41");
    var elAdd42 = document.getElementById("mainadd42");
    var elAdd43 = document.getElementById("mainadd43");
    var elCity = document.getElementById("maincity");
    var elCounty = document.getElementById("maincounty");
    var elState = document.getElementById("mainstate");
    var elPostcode = document.getElementById("mainpostcode");
    var elRegion = document.getElementById("region");
    var elCountry = document.getElementById("countrylist");
    var elCountylist = document.getElementById("countylist");
    var elStatelist = document.getElementById("statelist");

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

function restoreFields()
{
    /* restore field data after AJAX call */
    var elFirstname = document.getElementById("maincontactfname");
    var elLastname = document.getElementById("maincontactlname");
    var elCompany = document.getElementById("maincompanyname");
    var elAdd1 = document.getElementById("mainaddress1");
    var elAdd2 = document.getElementById("mainaddress2");
    var elAdd3 = document.getElementById("mainaddress3");
    var elAdd4 = document.getElementById("mainaddress4");
    var elAdd41 = document.getElementById("mainadd41");
    var elAdd42 = document.getElementById("mainadd42");
    var elAdd43 = document.getElementById("mainadd43");
    var elCity = document.getElementById("maincity");
    var elCounty = document.getElementById("maincounty");
    var elState = document.getElementById("mainstate");
    var elPostcode = document.getElementById("mainpostcode");
    var elCountry = document.getElementById("countrylist");
    var elCountylist = document.getElementById("countylist");
    var elStatelist = document.getElementById("statelist");

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

		elPostcode.addEventListener('blur', function(event)
			{
				return CJKHalfWidthFullWidthToASCII(this, true);
			}
		);
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

		elCountylist.addEventListener('change', function(event)
			{
				return changeState();
			}
		);
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

		elStatelist.addEventListener('change', function(event)
			{
				return changeState();
			}
		);
    }
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

    gAs_jsonCity = new bsn.AutoSuggest('maincity', as_city);
    gAs_jsonCounty = new bsn.AutoSuggest('maincounty', as_county);

        {/literal}

    {/if} {* end {if $autosuggestavailable == 1} *}

    {literal}

}

function verifyAddress()
{
    // make sure the terms and conditions are checked if required
    if (showTermsAndConditions == 1)
    {
        if (!document.getElementById('ordertermsandconditions').checked)
        {
            return false;
        }
    }

    saveFields();

    {/literal}

        {if $autosuggestavailable == 1}

            {literal}

    processAjax("verify",".?fsaction=AjaxAPI.callback&cmd=ADDRESSVERIFICATION&city=" + city + "&county=" + county + "&statecode=" + regioncode +
    "&statevalue=" + state + "&postcode=" + postcode + "&region=" + region + "&country=" + country + "&addresstype=billing");

            {/literal}

        {else}

            {literal}

   updateAccountDetails('match');

            {/literal}

        {/if} {*end {if $autosuggestavailable == 1}*}

    {literal}

    return false;
}

function initiliazeNewAccount(pIsMobile)
{
    if (pIsMobile == false)
    {

        if ("{/literal}{$error}{literal}".length > 0)
        {
            document.getElementById('message').style.display = 'block';
        }

        document.getElementById('backButton').onclick = cancelDataEntry;
    }

    if (showTermsAndConditions == 0)
    {
        document.getElementById('confirmButton').onclick = verifyAddress;
    }
    else
    {
        document.getElementById('ordertermsandconditions').onclick = acceptTermsAndConditions;
    }

    processAjax("ajaxdiv",".?fsaction=AjaxAPI.callback&cmd=ADDRESSFORM&country=" + country + "&addresstype=billing&strict=1&ishighlevel={/literal}{$ishighlevel}{literal}");
};

// wrapper for countryChange
function fnCountryChange(pElement, pEvent)
{
    if ((!pElement) || (pEvent.type != 'change')) {
        return false;
    }

    setCountry();
}

function updateAccountDetails(pVerify)
{
    /* save address fields to javascript variables */
    saveFields();
    gAlerts = 0;
    var registeredTaxNumber = '';
    var registeredTaxNumberType = 0;
    var message = "{/literal}{#str_ErrorCompulsoryInformationMissing#}{literal}";
    var theForm = document.mainform;
    var passwordStrengthErrorText = passwordStrength.getErrorText();
	
    resetInvalidAddressFields('mainform');

    {/literal}{if $showusernameinput == 1}{literal}
    if (document.getElementById('login').value.length < 5)
    {
        message += "\n" + "{/literal}{#str_MessageCompulsoryUserNameLength#}{literal}";
        message = message.replace("^0", '5');
        highlight("login");
    }
    {/literal}{/if}{literal}

    if (! validateEmailAddress(document.getElementById('email').value))
    {
        message += "\n" + "{/literal}{#str_MessageCompulsoryEmaiInvalid#}{literal}";
        highlight("email");
    }

    if (document.getElementById('password').value.length < 5)
    {
        message += "\n" + "{/literal}{#str_MessageCompulsoryPasswordLength#}{literal}";
        message = message.replace("^0", '5');
        highlight("password");
        highlight("password2");
    }

    if (passwordStrengthErrorText != '')
    {
        message += passwordStrengthErrorText;
        highlight("password");
    }

    if (document.getElementById("firstnamecompulsory"))
    {
        if (firstname.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryFirstNameMandatory#}{literal}";
            highlight("maincontactfname");
        }
    }

    if (document.getElementById("lastnamecompulsory"))
    {
        if (lastname.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryLastNameMandatory#}{literal}";
            highlight("maincontactlname");
        }
    }

    if (document.getElementById("companycompulsory"))
    {
        if (company.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryCompanyMandatory#}{literal}";
            highlight("maincompanyname");
        }
    }

    /* test to see if address verification failed on something */
    if (pVerify != 'match')
    {
        // The address verification API found invalid data in the address form.
    	// We must highlight which fields have failed.
    	message += highlightVerificationFailures(pVerify);
    }

    if (document.getElementById("add1compulsory"))
    {
        if (add1.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryAdd1Mandatory#}{literal}";
            highlight("mainaddress1");
        }
    }

    if (document.getElementById("add2compulsory"))
    {
        if (add2.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryAdd2Mandatory#}{literal}";
            highlight("mainaddress2");
        }
    }

    if (document.getElementById("add3compulsory"))
    {
        if (add3.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryAdd3Mandatory#}{literal}";
            highlight("mainaddress3");
        }
    }

    if (document.getElementById("add4compulsory"))
    {
        if (add4.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryAdd4Mandatory#}{literal}";
            highlight("mainaddress4");
        }
    }

    if (document.getElementById("add41compulsory"))
    {
        if (add41.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryAdd1Mandatory#}{literal}";
            highlight("mainadd41");
        }
    }

    if (document.getElementById("add42compulsory"))
    {
        if (add42.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryAdd42Mandatory#}{literal}";
            highlight("mainadd42");
        }
    }

    if (document.getElementById("add43compulsory"))
    {
        if (add43.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryAdd3Mandatory#}{literal}";
            highlight("mainadd43");
        }
    }

    if ((document.getElementById("citycompulsory")) && (document.getElementById("citycompulsory").src.indexOf("/images/asterisk.png") != -1))
    {
        if (city.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryCityMandatory#}{literal}";
            highlight("maincity");
        }
    }

    if ((document.getElementById("countycompulsory")) && (document.getElementById("countycompulsory").src.indexOf("/images/asterisk.png") != -1))
    {
        if (county.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryCountyMandatory#}{literal}";
            highlight("maincounty");
            highlight("countylist");
        }
    }

    if ((document.getElementById("statecompulsory")) && (document.getElementById("statecompulsory").src.indexOf("/images/asterisk.png") != -1))
    {
        if (state.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryStateMandatory#}{literal}";
            highlight("mainstate");
            highlight("statelist");
        }
    }

    if ((document.getElementById("postcodecompulsory")) && (document.getElementById("postcodecompulsory").src.indexOf("/images/asterisk.png") != -1))
    {
        if (postcode.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryPostCodeMandatory#}{literal}";
            highlight("mainpostcode");
        }
    }

    if (regioncode == '--')
    {
        regioncode = "";
    }

    if (document.getElementById('telephonenumber').value.length == 0)
    {
        message += "\n" + "{/literal}{#str_MessageCompulsoryPhoneMandatory#}{literal}";
        highlight("telephonenumber");
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

    if (gAlerts > 0)
    {

{/literal}

{if $issmallscreen == 'true'}

    {literal}

        createDialog("{/literal}{#str_TitleWarning#}{literal}", nlToBr(message), function(e) {
            closeDialog(e);
        });

    {/literal}

{else}

    {literal}

        alert(message);
        return false;

    {/literal}

{/if} {*end {if $issmallscreen == 'true'}*}

 {literal}

    }
    else
    {
        acceptDataEntry(registeredTaxNumber);
    }
}

{/literal}

{if $issmallscreen == 'true'}

    {* SMALL SCREEN SPECIFIC FUNCTION *}

    {literal}

    /**
    * acceptTermsAndConditions
    *
    * Change the order status button when the terms and conditions are clicked
    */
    function acceptTermsAndConditions()
    {
        if (document.getElementById('ordertermsandconditions').checked)
        {
            document.getElementById('confirmButton').onclick = verifyAddress;
            document.getElementById('confirmButton').className = 'btnAction btnContinue';
        }
        else
        {
            document.getElementById('confirmButton').onclick = function () { return false; };
            document.getElementById('confirmButton').className = 'btnAction btnContinue disabled';
        }
    }

    // wrapper for orderTermsAndConditions
    function fnOrderTermsAndConditions(pElement, pEvent)
    {
        pEvent.preventDefault();
        return orderTermsAndConditions();
    }

    /**
    * orderTermsAndConditions
    *
    * get the template to display for terms and conditions
    */
    function orderTermsAndConditions()
    {
        processAjaxSmallScreen('termsandconditionswindow', ".?fsaction=AjaxAPI.callback&cmd=TERMSANDCONDITIONS&template=NEWACCOUNT&mobile=1", 'GET', '');
        return false;
    }

    function acceptDataEntry(pRegisteredTaxNumber)
    {
        var format = ((document.location.protocol != 'https:') ? 1 : 0);
        var password = ((format == 0) ? document.getElementById('password').value : hex_md5(document.getElementById('password').value));

        /* copy the values into the form we will submit and then submit it to the server */
        form = document.submitformNewAccount;

        form.login.value = document.getElementById("{/literal}{$usernamefieldid}{literal}").value;

        form.password.value = password;

        var postParams = '&ref={/literal}{$ref}{literal}';
        postParams += '&login=' + document.getElementById("{/literal}{$usernamefieldid}{literal}").value;
        postParams += '&password=' + password;
        postParams += '&format=' +  format;
        postParams += '&contactfname=' +  encodeURIComponent(firstname);
        postParams += '&contactlname=' +  encodeURIComponent(lastname);
        postParams += '&companyname=' +  encodeURIComponent(company);
        postParams += '&address1=' +  encodeURIComponent(add1);
        postParams += '&address2=' +  encodeURIComponent(add2);
        postParams += '&address3=' +  encodeURIComponent(add3);
        postParams += '&address4=' +  encodeURIComponent(add4);
        postParams += '&add41=' +  encodeURIComponent(add41);
        postParams += '&add42=' +  encodeURIComponent(add42);
        postParams += '&add43=' +  encodeURIComponent(add43);
        postParams += '&city=' +  encodeURIComponent(city);
        postParams += '&county=' +  encodeURIComponent(county);
        postParams += '&state=' +  encodeURIComponent(state);
        postParams += '&regioncode=' +  encodeURIComponent(regioncode);
        postParams += '&region=' +  encodeURIComponent(region);
        postParams += '&postcode=' +  encodeURIComponent(postcode);
        postParams += '&countrycode=' +  encodeURIComponent(country);
        postParams += '&countryname=' +  encodeURIComponent(document.getElementById('countrylist').options[document.getElementById('countrylist').selectedIndex].text);
        postParams += '&telephonenumber=' +  encodeURIComponent(document.getElementById('telephonenumber').value);
        postParams += '&email=' +  encodeURIComponent(document.getElementById('email').value);

        if (document.getElementById('regtaxnumtype'))
        {
            postParams += '&registeredtaxnumbertype=' +  encodeURIComponent(document.getElementById('regtaxnumtype').options[document.getElementById('regtaxnumtype').selectedIndex].value);
        }
        else
        {
            postParams += '&registeredtaxnumbertype=' +  0;
        }

        if (document.getElementById('regtaxnum'))
        {
            postParams += '&registeredtaxnumber=' +  encodeURIComponent(pRegisteredTaxNumbe);
        }
        else
        {
            postParams += '&registeredtaxnumber=' +  '';
        }

        if (document.getElementById('subscribed').checked)
        {
            postParams += '&sendmarketinginfo=' +  1;
        }
        else
        {
            postParams += '&sendmarketinginfo=' +  0;
        }


        // Add CSRF token to post submissions
        var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
        if (csrfMeta) {
            var csrfToken = csrfMeta.getAttribute('content');
        }

        {/literal}{if $ishighlevel == 1}{literal}
            		postParams += "&groupcode={/literal}{$groupcode}{literal}";
		{/literal}{/if}{literal}

		postParams += "&prtz={/literal}{$prtz}{literal}";
		postParams += "&mawebhluid={/literal}{$mawebhluid}{literal}";
		postParams += "&mawebhlbr={/literal}{$mawebhlbr}{literal}";
		postParams += "&ishighlevel={/literal}{$ishighlevel}{literal}";
        postParams += "&fromregisterlink={/literal}{$fromregisterlink}{literal}";
        postParams += '&csrf_token=' + csrfToken;

        processAjaxSmallScreen("createaccountsubmit",".?fsaction={/literal}{$registerfsaction}{literal}&mobile=true", 'POST', postParams);
    }

    {/literal}

    {* END SMALL SCREEN  SPECIFIC FUNCTION *}

{else}

    {* LARGE SCREEN SPECIFIC FUNCTION *}

    {literal}

    function setSystemLanguage()
    {
        changeSystemLanguage("Welcome.initNewAccount", "submitform", 'post');
    }

    function closeConfirmationBox()
    {
        var confirmationBoxObj = document.getElementById('confirmationBox');
        if (confirmationBoxObj)
        {
            confirmationBoxObj.style.display = 'none';
        }
        return false;
    }

    function highlight(field)
    {
        var inputObj = document.getElementById(field);
        if (inputObj)
        {
            inputObj.className = inputObj.className + ' errorInput';
            gAlerts = 1;
        }
    }

    function acceptDataEntry(pRegisteredTaxNumber)
    {
        var format = ((document.location.protocol != 'https:') ? 1 : 0);
        var password = ((format == 0) ? document.getElementById('password').value : hex_md5(document.getElementById('password').value));

        /* copy the values into the form we will submit and then submit it to the server */
        form = document.submitform;

        form.login.value = document.getElementById("{/literal}{$usernamefieldid}{literal}").value;

        form.password.value = password;

        form.format.value = format;

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
        form.countryname.value = document.getElementById('countrylist').options[document.getElementById('countrylist').selectedIndex].text;
        form.telephonenumber.value = document.getElementById('telephonenumber').value;
        form.email.value = document.getElementById('email').value;

        if (document.getElementById('regtaxnumtype'))
        {
            form.registeredtaxnumbertype.value = document.getElementById('regtaxnumtype').options[document.getElementById('regtaxnumtype').selectedIndex].value;
        }
        else
        {
            form.registeredtaxnumbertype.value = 0;
        }

        if (document.getElementById('regtaxnum'))
        {
            form.registeredtaxnumber.value = pRegisteredTaxNumber;
        }
        else
        {
            form.registeredtaxnumber.value = '';
        }

        if (document.getElementById('subscribed').checked)
        {
            form.sendmarketinginfo.value = 1;
        }
        else
        {
            form.sendmarketinginfo.value = 0;
        }

        form.fsaction.value = "{/literal}{$registerfsaction}{literal}";

        form.submit();

        return false;
    }

    /**
    * acceptTermsAndConditions
    *
    * Change the order status button when the terms and conditions are clicked
    */
    function acceptTermsAndConditions()
    {
        if (document.getElementById('ordertermsandconditions').checked)
        {
            document.getElementById('confirmButton').onclick = verifyAddress;

            document.getElementById('btn-confirm-left').className = 'btn-green-left';
            document.getElementById('btn-confirm-middle').className = 'btn-green-middle';
            document.getElementById('btn-confirm-right').className = 'btn-accept-right';
        }
        else
        {
            document.getElementById('confirmButton').onclick = function () { return false; };

            document.getElementById('btn-confirm-left').className = 'btn-disabled-left';
            document.getElementById('btn-confirm-middle').className = 'btn-disabled-middle';
            document.getElementById('btn-confirm-right').className = 'btn-disabled-right-tick';
        }
    }

    function cancelDataEntry()
    {
        document.getElementById("fsaction").value = "{/literal}{$cancelfsaction}{literal}";
        document.getElementById("submitform").submit();
        return false;
    }

    /**
    * orderTermsAndConditions
    *
    * get the template to display for terms and conditions
    */
    function orderTermsAndConditions()
    {
        processAjax('termsandconditionswindow', ".?fsaction=AjaxAPI.callback&cmd=TERMSANDCONDITIONS&template=NEWACCOUNT", 'GET', '');
        return false;
    }

    function closeTermsAndCondition()
    {
        var shimObj = document.getElementById('shim');
        var componentChangeBoxObj = document.getElementById('ordersTermsAndCondtions');
        if (shimObj)
        {
            shimObj.style.display = 'none';
        }
        if (componentChangeBoxObj)
        {
            componentChangeBoxObj.style.display = 'none';
        }
        document.body.className = document.body.className.replace(' hideSelects', '');
        return false;
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

    // wrapper for password strength meter
    function fnHandlePasswordStrength(pElement)
    {
        if (!pElement) {
            return false;
        }

        return passwordStrength.scorePassword(pElement.value, 'strengthvalue', 'strengthtext');
    }

    // wrapper for CJKHalfWidthFullWidthToASCII
    function fnCJKHalfWidthFullWidthToASCII(pElement)
    {
        if (!pElement) {
            return false;
        }

        return CJKHalfWidthFullWidthToASCII(pElement, JSON.parse(pElement.getAttribute('data-force-uppercase')));
    }

    window.onload = function()
    {
        initiliazeNewAccount(false);

        // Add listener to langauge select.
        document.getElementById('systemlanguagelist').addEventListener('change', function() {
            return setSystemLanguage();
        });

        // Add listener to the terms and conditions link.
        var termsLinkElement = document.getElementById('ordertermsandconditionslink');
        if (termsLinkElement)
        {
            termsLinkElement.addEventListener('click', function(event) {
                event.preventDefault();
                orderTermsAndConditions();
                return false;
            });
        }

        var closeTermsAndConditionsButton = document.getElementById('closeTermsAndConditionsButton');
        if (closeTermsAndConditionsButton)
        {
            closeTermsAndConditionsButton.addEventListener('click', function(event) {
                closeTermsAndCondition();
                return false;
            });
        }

        var confirmationCloseButton = document.getElementById('confirmationCloseButton');
        if (confirmationCloseButton)
        {
            confirmationCloseButton.addEventListener('click', function(event) {
                return closeConfirmationBox();
            });
        }

		var togglePasswordElement = document.getElementById('togglepassword');
		if (togglePasswordElement)
		{
			togglePasswordElement.addEventListener('click', function() {
				togglePasswordVisibility(togglePasswordElement, 'password');
			});
		}
    }

    {/literal}

    {* END LARGE SCREEN SPECIFIC FUNCTION *}

{/if} {*end {if $issmallscreen == 'true'}*}