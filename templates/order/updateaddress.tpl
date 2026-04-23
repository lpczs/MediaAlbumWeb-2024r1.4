    var gAddressType = "{$addresstype}";
    var gAlerts = 0;
    var gMessage = '';
    var gAs_jsonCity = '';
    var gAs_jsonCounty = '';
    var gAs_jsonState = '';
    var gSession = "{$session}";
    var gIsMobile = "{$issmallscreen}";
    var gSSOToken = "{$ssotoken}";
    var firstname = "{$contactfname_script}";
    var lastname = "{$contactlname_script}";
    var company = "{$companyname_script}";
    var add1 = "{$address1_script}";
    var add2 = "{$address2_script}";
    var add3 = "{$address3_script}";
    var add4 = "{$address4_script}";
    var add41 = "{$add41_script}";
    var add42 = "{$add42_script}";
    var add43 = "{$add43_script}";
    var city = "{$city_script}";
    var county = "{$county_script}";
    var state = "{$state_script}";
    var regioncode = "{$regioncode}";
    var region = "";
    var postcode = "{$postcode_script}";
    var country = "{$country}";
    var telephonenumber = "{$telephonenumber_script}";
    var email = "{$email_script}";
    var registeredtaxnumbertype = "{$registeredtaxnumbertype}";
    var registeredtaxnumber = "{$registeredtaxnumber_script}";
    var TPX_REGISTEREDTAXNUMBERTYPE_NA = {$TPX_REGISTEREDTAXNUMBERTYPE_NA};
    var TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL = {$TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL};
    var TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE = {$TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE};
    var countryChanged = false;
    var gBackButtonDisplay = '';
	var lastSuccesfulCountry = country;


    window.addEventListener('DOMContentLoaded', function(event) {
        document.body.onresize = function(e) {
            resizeApp();
        };
        
        document.body.addEventListener('keyup', decoratorListener);
        document.body.addEventListener('change', decoratorListener);
        document.body.addEventListener('click', decoratorListener);
    });

{literal}
    function initializeAddress(pIsSmallScreen, pBackButton)
    {
        gBackButtonDisplay = pBackButton;

        {/literal}{if ($useraddressupdated == 0 || $useraddressupdated == 2) && ($addresstype == 'shipping')}{literal}
            var hideConfigFields = 0;
        {/literal}{else}{literal}
            var hideConfigFields = 1;
        {/literal}{/if}{literal}

        {/literal}{$initlanguage}{literal}

        processAjaxAddress("ajaxdiv",".?fsaction=AjaxAPI.callback&cmd=ADDRESSFORM&country=" + country + "&hideconfigfields=" + hideConfigFields + "&addresstype=" + gAddressType + "&strict=1&edit={/literal}{$edit}{literal}", {/literal}{$useraddressupdated}{literal}, 'GET', '');

        var loginTitleElement = document.getElementById("logintitle");
        if (loginTitleElement)
        {
            document.getElementById("logintitle").style.display = "none";
            document.getElementById("loginpassword").style.display = "none";
            document.getElementById("blocTitle").style.display = "none";
        }

        document.getElementById("blocContent").className = document.getElementById("blocContent").className.replace('currentBloc outerBoxPadding','');

		{/literal}{if $useraddressupdated != 2}{literal}
        document.getElementById("useremail").className = document.getElementById("useremail").className.replace('currentBloc outerBoxPadding','');
		{/literal}{/if}{literal}
			
        if (pIsSmallScreen)
        {
            document.getElementById("addressBlocFirst").className = document.getElementById("addressBlocFirst").className.replace('outerBox outerBoxMarginTop','');
            document.getElementById("logintable").className = document.getElementById("logintable").className.replace('outerBox','');
            closeLoadingDialog();
        }
    }

    function setSystemLanguage()
    {
        changeSystemLanguage("{/literal}{$refreshaction}{literal}", "submitformaddress", 'post');
    }

    function cancelDataEntry()
    {
        {/literal}

        {if $issmallscreen == 'true'}

            {literal}

            // open the loading box
            showLoadingDialog();

            var postParams = '&previousstage={/literal}{$previousstage}{literal}';
            postParams += '&stage={/literal}{$stage}{literal}';

            processAjaxAddress("cancel",".?fsaction=AjaxAPI.callback&cmd=CHANGEADDRESSCANCEL", 1, 'POST', postParams);

            {/literal}

        {else}

            {literal}

            document.submitformaddress.fsaction.value = "Order.changeAddressCancel";
            document.submitformaddress.submit();

            return false;

            {/literal}

        {/if}

        {literal}

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

    function acceptDataAddressEntry(pVerify)
    {
        /* save address fields to javascript variables */
        saveFields();

        gAlerts = 0;
        var message = "{/literal}{#str_ErrorCompulsoryInformationMissing#}{literal}";
        var theForm = document.mainformaddress;

        /* test to see if address verification failed on something */
        resetInvalidAddressFields('mainformaddress');

        if (pVerify != 'match')
        {
            // The address verification API found invalid data in the address form.
    		// We must highlight which fields have failed. 
    		message += highlightVerificationFailures(pVerify);
        }

        if (firstname.length == 0)
        {
            highlight("maincontactfname");
            message += "\n" + "{/literal}{#str_MessageCompulsoryFirstNameMandatory#}{literal}";
        }

        if (lastname.length == 0)
        {
            message += "\n" + "{/literal}{#str_MessageCompulsoryLastNameMandatory#}{literal}";
            highlight("maincontactlname");
        }

        // Make sure the email address is populated and valid.
        if (email.length == 0)
        {
            message += "\n" + "{/literal}{#str_ErrorNoEmailAddress#}{literal}";
            highlight("email");
        }
        else
        {
            if (! validateEmailAddress(email))
            {
                message += "\n" + "{/literal}{#str_MessageCompulsoryEmaiInvalid#}{literal}";
                highlight("email");
            }
        }

{/literal}

{if $edit == 0}

    {literal}

        if (document.getElementById("companycompulsory"))
        {
            if (company.length == 0)
            {
                message += "\n" + "{/literal}{#str_MessageCompulsoryCompanyMandatory#}{literal}";
                highlight("maincompanyname");
            }
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

        if (regioncode == '--')
        {
            regioncode = "";
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

    {/literal}

{/if}

{literal}
        if ((document.getElementById("telephonenumbercompulsory")) && (document.getElementById("telephonenumbercompulsory").src.indexOf("/images/asterisk.png") != -1))
        {
            if (telephonenumber.length == 0)
            {
                message += "\n" + "{/literal}{#str_MessageCompulsoryPhoneMandatory#}{literal}";
                highlight("telephonenumber");
            }
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

        /* copy the values into the form we will submit and then submit it to the server */
        document.submitformaddress.contactfname.value = firstname;
        document.submitformaddress.contactlname.value = lastname;
{/literal}

{if $edit == 0}

{literal}
        document.submitformaddress.companyname.value = company;
        document.submitformaddress.address1.value = add1;
        document.submitformaddress.address2.value = add2;
        document.submitformaddress.address3.value = add3;
        document.submitformaddress.address4.value = add4;
        document.submitformaddress.add41.value = add41;
        document.submitformaddress.add42.value = add42;
        document.submitformaddress.add43.value = add43;
        document.submitformaddress.city.value = city;
        document.submitformaddress.county.value = county;
        document.submitformaddress.state.value = state;
        document.submitformaddress.regioncode.value = regioncode;
        document.submitformaddress.region.value = region;
        document.submitformaddress.postcode.value = postcode;
        document.submitformaddress.countrycode.value = country;
        document.submitformaddress.countryname.value = theForm.countrylist.options[theForm.countrylist.selectedIndex].text;
{/literal}

{/if}

{literal}
        document.submitformaddress.telephonenumber.value = telephonenumber;

        {/literal}{if $useraddressupdated != 2}{literal}
            document.submitformaddress.email.value = email;
        {/literal}{else}{literal}
            document.submitformaddress.email.value = "{/literal}{$email}{literal}";
        {/literal}{/if}{literal}

        document.submitformaddress.registeredtaxnumbertype.value = registeredTaxNumberType;
        document.submitformaddress.registeredtaxnumber.value = registeredTaxNumber;

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

        {/if}

        {literal}

        }
        else
        {
        	{/literal}

        	{if ($useraddressupdated == 0 || $useraddressupdated == 2)}

        		{literal}
        			document.submitformaddress.fsaction.value = "Order.updateAccountDetails";
        		{/literal}

        	{else}

				{literal}
				if (gAddressType == "shipping")
				{
					document.submitformaddress.fsaction.value = "Order.changeShippingAddress";
				}
				else
				{
					document.submitformaddress.fsaction.value = "Order.changeBillingAddress";
				}
				{/literal}
        	{/if}

        {if $issmallscreen == 'true'}

            {literal}

            showLoadingDialog()

            if (gCurrentSource == 'jobticket')
            {


                var postParams = '&contactfname=' + encodeURIComponent(firstname);
                postParams += '&contactlname=' + encodeURIComponent(lastname);

                {/literal}

                {if $edit == 0}

                    {literal}

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
                postParams += '&countryname=' + encodeURIComponent(document.submitformaddress.countryname.value);

                    {/literal}

                {/if}

                {literal}

                postParams += '&telephonenumber=' + encodeURIComponent(telephonenumber);
                postParams += '&email=' + encodeURIComponent(document.submitformaddress.email.value);
                postParams += '&registeredtaxnumbertype=' + encodeURIComponent(registeredTaxNumberType);
                postParams += '&registeredtaxnumber=' + encodeURIComponent(registeredTaxNumber);

                postParams += '&previousstage={/literal}{$previousstage}{literal}';
                postParams += '&stage={/literal}{$stage}{literal}';
                postParams += '&fsactionorig=' + document.submitformaddress.fsaction.value;

                if (document.getElementById("shippingcfscontact"))
                {
                    var shippingcfscontact = document.getElementById("shippingcfscontact").value;
                    postParams += '&shippingcfscontact=' + shippingcfscontact;
                }
              
				{/literal}

				{if ($useraddressupdated == 0 || $useraddressupdated == 2)}

					{literal}

                        processAjaxAddress("refreshshippingPanel",".?fsaction=AjaxAPI.callback&cmd=UPDATEACCOUNTDETAILS", 0, 'POST', postParams);
					{/literal}

				{else}

					{literal}
					if (gAddressType == "shipping")
					{
						processAjaxAddress("refreshshippingPanel",".?fsaction=AjaxAPI.callback&cmd=CHANGESHIPPINGADDRESS", 0, 'POST', postParams);
					}
					else
					{
						processAjaxAddress("refreshshippingPanel",".?fsaction=AjaxAPI.callback&cmd=CHANGEBILLINGADDRESS", 0, 'POST', postParams);
					}
					{/literal}
				{/if}
				{literal}
            }
            else
            {
                document.submitformaddress.submit();
                return false;
            }

            {/literal}

        {else}

            {literal}

            document.submitformaddress.submit();
            return false;

            {/literal}

        {/if}

        {literal}

        }

    }

    function setCountry()
    {
        {/literal}{if $addresstype == 'shipping' && $useraddressupdated == 1}{literal}
            var hideConfigFields = 1;
        {/literal}{elseif ($addresstype == 'shipping') && ($useraddressupdated == 0 || $useraddressupdated == 2)}{literal}
            var hideConfigFields = 0;
        {/literal}{else}{literal}
            var hideConfigFields = 0;
        {/literal}{/if}{literal}

        /* save field data so it can be restored afterwards */
        saveFields();
        countryChanged = true;
		processAjaxAddress("ajaxdivupdate",".?fsaction=AjaxAPI.callback&cmd=ADDRESSFORM&country=" + country + "&hideconfigfields=" + hideConfigFields +"&addresstype=" + gAddressType + "&strict=1&edit={/literal}{$edit}{literal}", 1, 'GET', '');
	}

    function changeState()
    {
        saveFields();

        {/literal}

        {if $autosuggestavailable == 1}

            {literal}

        var as_city = {
            script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=city&country="+ country + "&statecode=" + regioncode + "&addresstype=" + gAddressType,
            varname:"&input",
            cache:false,
            offsety:0,
            json:true,
            shownoresults:false,
            maxresults:20
        };

        var as_county = {
            script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=county&country="+ country+ "&statecode=" + regioncode + "&addresstype=" + gAddressType,
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

        {/if}

        {literal}
    }


    function verifyAddress()
    {
        saveFields();

        {/literal}

        {if $autosuggestavailable == 1}

            {literal}

        processAjaxAddress("verify",".?fsaction=AjaxAPI.callback&cmd=ADDRESSVERIFICATION&city=" + city +
        "&county=" + county + "&statecode=" + regioncode + "&statevalue=" + state + "&postcode=" + postcode + "&region=" + region +
        "&country=" + country +
        "&addresstype=" + gAddressType, 1, 'GET', '');

            {/literal}

        {else}

            {literal}

            acceptDataAddressEntry('match');

            {/literal}

        {/if}

        {literal}

        return false;
    }

    function saveFields()
    {

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
        var elRegion = document.getElementById("region");
        var elPostcode = document.getElementById("mainpostcode");
        var elCountry = document.getElementById("countrylist");
        var elCountylist = document.getElementById("countylist");
        var elStatelist = document.getElementById("statelist");
        var elTelephoneNumber = document.getElementById("telephonenumber");
        var elEmail = document.getElementById("email");


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
        telephonenumber = '';
        email = '';
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

        if (elTelephoneNumber)
        {
            telephonenumber = elTelephoneNumber.value;
        }

        if (elEmail)
        {
            email = elEmail.value;
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
        var elTelephoneNumber = document.getElementById("telephonenumber");
        var elEmail = document.getElementById("email");
        var elRegisteredTaxNumberType = document.getElementById("regtaxnumtype");
        var elRegisteredTaxNumber = document.getElementById("regtaxnum");

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
        if (elTelephoneNumber)
        {
            elTelephoneNumber.value = telephonenumber;
        }
        if (elEmail)
        {
            elEmail.value = email;
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

			elCountylist.addEventListener('change', function(event)
				{
					return changeState();
				}
			);
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

			elStatelist.addEventListener('change', function(event)
				{
					return changeState();
				}
			);
        }
    }

    /* A J A X */

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
    function processAjaxAddress(obj, serverPage, addressUpdated, pRequestMethod, pPostParams)
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

        if ('POST' === pRequestMethod) {
            // Add CSRF token to post submissions
            var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
            if (csrfMeta) {
                
                var csrfToken = csrfMeta.getAttribute('content');

                if (typeof pPostParams !== 'undefined' && null !== pPostParams && pPostParams.length > 0) {
                    pPostParams += '&csrf_token=' + csrfToken;
                } else {
                    pPostParams = 'csrf_token=' + csrfToken;
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

        if (pRequestMethod == 'GET')
        {
            xmlhttp.open("GET", serverPage+"&dummy=" + new Date().getTime(), true);
        }
        else
        {   
            xmlhttp.open("POST", serverPage, false);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
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

        if (pRequestMethod == 'GET')
        {
            xmlhttp.onreadystatechange = function()
            {

                if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
                {
                    if (addressUpdated == 0)
                    {

                    {/literal}

                    {if $issmallscreen == 'true'}

                        {literal}

                        createDialog("{/literal}{#str_TitleWarning#}{literal}", "{/literal}{#str_MessageUpdateAddressDetails#}{literal}", function(e) {
                            closeDialog(e);
                        });

                        {/literal}

                    {else}

                        {literal}

                        alert("{/literal}{#str_MessageUpdateAddressDetails#}{literal}");

                        {/literal}

                    {/if}

                    {literal}

                    }

                    /* address verification */
                    switch (obj)
                    {
                        case 'verify':
                            acceptDataAddressEntry(xmlhttp.responseText);
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
							
                            document.getElementById(obj).innerHTML = xmlhttp.responseText;
                            if (gMessage != '')
                            {
                                document.getElementById("message").innerHTML = gMessage;
                            }
                            restoreFields();

                            {/literal}

                            {if $autosuggestavailable == 1}

                                {literal}

                            var as_city = {
                                script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=city&country="+ country + "&statecode=" + regioncode + "&addresstype=" + gAddressType,
                                varname:"&input",
                                cache:false,
                                offsety:0,
                                json:true,
                                shownoresults:false,
                                maxresults:20
                            };

                            var as_county = {
                                script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=county&country="+ country + "&statecode=" + regioncode + "&addresstype=" + gAddressType,
                                varname:"&input",
                                cache:false,
                                offsety:0,
                                json:true,
                                shownoresults:false,
                                maxresults:20
                            };

                            var as_state = {
                                script:".?fsaction=AjaxAPI.callback&cmd=AUTOSUGGEST&limit=20&field=state&country="+ country + "&statecode=" + regioncode + "&addresstype=" + gAddressType,
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

                            {/if} {* end {if $autosuggestavailable == 1} *}

                            {if $issmallscreen == 'true'}

                                {literal}

                                setScrollAreaHeight('contentRightScrollAddress', gBackButtonDisplay);

                                {/literal}

                            {/if} {* end {if $issmallscreen == 'true'} *}
							{* If communication successful update the lastSuccessfulCOuntry variable with the selected country *}
                            {literal}
							if (obj == 'ajaxdiv')
							{
                                if (document.getElementById("countrylist"))
                                {
								    lastSuccesfulCountry = document.getElementById("countrylist").value;
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
            xmlhttp.send(null);
        }
        else
        {
            xmlhttp.onreadystatechange = function()
            {
                if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
                {
                    switch (obj)
                    {
                        case 'refreshshippingPanel':
                            var jsonObj = parseJson(xmlhttp.responseText);
                            document.getElementById('contentPanelShipping').innerHTML = jsonObj.template;

                            //Update the shipping methods
                            document.getElementById('contentPanelMethodList').innerHTML = jsonObj.template2;
                            // change javascript
                            toggleJs('mainjavascript', jsonObj.javascript, true, '', '{/literal}{if isset($nonceraw)}{$nonceraw}{else}{literal}[nonce]{/literal}{/if}{literal}');
                            window.history.back();
                        break;
                        case 'cancel':
                            var jsonObj = parseJson(xmlhttp.responseText);
                            document.getElementById('contentPanelShipping').innerHTML = jsonObj.template;
                            window.history.back();
                        break;

                    }
                }
            }
            xmlhttp.send(pPostParams);
        }
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

    // wrapper for countryChange
    function fnCountryChange(pElement, pEvent)
    {
        if ((!pElement) || (pEvent.type != 'change')) {
            return false;
        }

        setCountry();
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
        {/literal}{if $issmallscreen == 'true'}{literal}

        /* set a cookie to store the local time */
        var theDate = new Date();
        createCookie("mawebtz", Math.round(theDate.getTime() / 1000), 2);
        initializeSmallScreenVersion(true);

        {/literal}{/if}{literal}

        initializeAddress(false, '');

        // Add listener to the cancel button.
        var cancelBtn = document.getElementById('cancel');
        if (cancelBtn)
        {
            cancelBtn.addEventListener('click', function() {
                cancelDataEntry();
            });
        }

        function collectionContactDetails()
        {
            //acceptDataAddressEntry(pVerify);
            verifyAddress();
        }

        // Add listener to the ok button.
        var okBtn = document.getElementById('ok');
        if (okBtn)
        {
            okBtn.addEventListener('click', function() {
                {/literal}{if $shippingcfscontact == '1'}{literal}
                    collectionContactDetails();
                {/literal}{else}{literal}
                    verifyAddress();
                {/literal}{/if}{literal}
            });
        }

        // Add listener to langauge select.
        var systemlanguagelist = document.getElementById('systemlanguagelist');
        if(systemlanguagelist)
        {
            systemlanguagelist.addEventListener('change', function() {
                return setSystemLanguage();
            });
        }
    }

{/literal}