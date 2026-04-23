<?php
/* Smarty version 4.5.3, created on 2026-03-23 02:01:34
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\updateaddress.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69c09efecd76e6_93875554',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5536c2e3e14c5abb2013877ff98c5740e558b9bc' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\updateaddress.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69c09efecd76e6_93875554 (Smarty_Internal_Template $_smarty_tpl) {
?>    var gAddressType = "<?php echo $_smarty_tpl->tpl_vars['addresstype']->value;?>
";
    var gAlerts = 0;
    var gMessage = '';
    var gAs_jsonCity = '';
    var gAs_jsonCounty = '';
    var gAs_jsonState = '';
    var gSession = "<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
";
    var gIsMobile = "<?php echo $_smarty_tpl->tpl_vars['issmallscreen']->value;?>
";
    var gSSOToken = "<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
";
    var firstname = "<?php echo $_smarty_tpl->tpl_vars['contactfname_script']->value;?>
";
    var lastname = "<?php echo $_smarty_tpl->tpl_vars['contactlname_script']->value;?>
";
    var company = "<?php echo $_smarty_tpl->tpl_vars['companyname_script']->value;?>
";
    var add1 = "<?php echo $_smarty_tpl->tpl_vars['address1_script']->value;?>
";
    var add2 = "<?php echo $_smarty_tpl->tpl_vars['address2_script']->value;?>
";
    var add3 = "<?php echo $_smarty_tpl->tpl_vars['address3_script']->value;?>
";
    var add4 = "<?php echo $_smarty_tpl->tpl_vars['address4_script']->value;?>
";
    var add41 = "<?php echo $_smarty_tpl->tpl_vars['add41_script']->value;?>
";
    var add42 = "<?php echo $_smarty_tpl->tpl_vars['add42_script']->value;?>
";
    var add43 = "<?php echo $_smarty_tpl->tpl_vars['add43_script']->value;?>
";
    var city = "<?php echo $_smarty_tpl->tpl_vars['city_script']->value;?>
";
    var county = "<?php echo $_smarty_tpl->tpl_vars['county_script']->value;?>
";
    var state = "<?php echo $_smarty_tpl->tpl_vars['state_script']->value;?>
";
    var regioncode = "<?php echo $_smarty_tpl->tpl_vars['regioncode']->value;?>
";
    var region = "";
    var postcode = "<?php echo $_smarty_tpl->tpl_vars['postcode_script']->value;?>
";
    var country = "<?php echo $_smarty_tpl->tpl_vars['country']->value;?>
";
    var telephonenumber = "<?php echo $_smarty_tpl->tpl_vars['telephonenumber_script']->value;?>
";
    var email = "<?php echo $_smarty_tpl->tpl_vars['email_script']->value;?>
";
    var registeredtaxnumbertype = "<?php echo $_smarty_tpl->tpl_vars['registeredtaxnumbertype']->value;?>
";
    var registeredtaxnumber = "<?php echo $_smarty_tpl->tpl_vars['registeredtaxnumber_script']->value;?>
";
    var TPX_REGISTEREDTAXNUMBERTYPE_NA = <?php echo $_smarty_tpl->tpl_vars['TPX_REGISTEREDTAXNUMBERTYPE_NA']->value;?>
;
    var TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL = <?php echo $_smarty_tpl->tpl_vars['TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL']->value;?>
;
    var TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE = <?php echo $_smarty_tpl->tpl_vars['TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE']->value;?>
;
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


    function initializeAddress(pIsSmallScreen, pBackButton)
    {
        gBackButtonDisplay = pBackButton;

        <?php if (($_smarty_tpl->tpl_vars['useraddressupdated']->value == 0 || $_smarty_tpl->tpl_vars['useraddressupdated']->value == 2) && ($_smarty_tpl->tpl_vars['addresstype']->value == 'shipping')) {?>
            var hideConfigFields = 0;
        <?php } else { ?>
            var hideConfigFields = 1;
        <?php }?>

        <?php echo $_smarty_tpl->tpl_vars['initlanguage']->value;?>


        processAjaxAddress("ajaxdiv",".?fsaction=AjaxAPI.callback&cmd=ADDRESSFORM&country=" + country + "&hideconfigfields=" + hideConfigFields + "&addresstype=" + gAddressType + "&strict=1&edit=<?php echo $_smarty_tpl->tpl_vars['edit']->value;?>
", <?php echo $_smarty_tpl->tpl_vars['useraddressupdated']->value;?>
, 'GET', '');

        var loginTitleElement = document.getElementById("logintitle");
        if (loginTitleElement)
        {
            document.getElementById("logintitle").style.display = "none";
            document.getElementById("loginpassword").style.display = "none";
            document.getElementById("blocTitle").style.display = "none";
        }

        document.getElementById("blocContent").className = document.getElementById("blocContent").className.replace('currentBloc outerBoxPadding','');

		<?php if ($_smarty_tpl->tpl_vars['useraddressupdated']->value != 2) {?>
        document.getElementById("useremail").className = document.getElementById("useremail").className.replace('currentBloc outerBoxPadding','');
		<?php }?>
			
        if (pIsSmallScreen)
        {
            document.getElementById("addressBlocFirst").className = document.getElementById("addressBlocFirst").className.replace('outerBox outerBoxMarginTop','');
            document.getElementById("logintable").className = document.getElementById("logintable").className.replace('outerBox','');
            closeLoadingDialog();
        }
    }

    function setSystemLanguage()
    {
        changeSystemLanguage("<?php echo $_smarty_tpl->tpl_vars['refreshaction']->value;?>
", "submitformaddress", 'post');
    }

    function cancelDataEntry()
    {
        

        <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

            

            // open the loading box
            showLoadingDialog();

            var postParams = '&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
';
            postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';

            processAjaxAddress("cancel",".?fsaction=AjaxAPI.callback&cmd=CHANGEADDRESSCANCEL", 1, 'POST', postParams);

            

        <?php } else { ?>

            

            document.submitformaddress.fsaction.value = "Order.changeAddressCancel";
            document.submitformaddress.submit();

            return false;

            

        <?php }?>

        

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
        var message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorCompulsoryInformationMissing');?>
";
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
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryFirstNameMandatory');?>
";
        }

        if (lastname.length == 0)
        {
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryLastNameMandatory');?>
";
            highlight("maincontactlname");
        }

        // Make sure the email address is populated and valid.
        if (email.length == 0)
        {
            message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoEmailAddress');?>
";
            highlight("email");
        }
        else
        {
            if (! validateEmailAddress(email))
            {
                message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryEmaiInvalid');?>
";
                highlight("email");
            }
        }



<?php if ($_smarty_tpl->tpl_vars['edit']->value == 0) {?>

    

        if (document.getElementById("companycompulsory"))
        {
            if (company.length == 0)
            {
                message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryCompanyMandatory');?>
";
                highlight("maincompanyname");
            }
        }

        if (document.getElementById("add1compulsory"))
        {
            if (add1.length == 0)
            {
                message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryAdd1Mandatory');?>
";
                highlight("mainaddress1");
            }
        }

        if (document.getElementById("add2compulsory"))
        {
            if (add2.length == 0)
            {
                message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryAdd2Mandatory');?>
";
                highlight("mainaddress2");
            }
        }

        if (document.getElementById("add3compulsory"))
        {
            if (add3.length == 0)
            {
                message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryAdd3Mandatory');?>
";
                highlight("mainaddress3");
            }
        }

        if (document.getElementById("add4compulsory"))
        {
            if (add4.length == 0)
            {
                message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryAdd4Mandatory');?>
";
                highlight("mainaddress4");
            }
        }

        if (document.getElementById("add41compulsory"))
        {
            if (add41.length == 0)
            {
                message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryAdd1Mandatory');?>
";
                highlight("mainadd41");
            }
        }

        if (document.getElementById("add42compulsory"))
        {
            if (add42.length == 0)
            {
                message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryAdd42Mandatory');?>
";
                highlight("mainadd42");
            }
        }

        if (document.getElementById("add43compulsory"))
        {
            if (add43.length == 0)
            {
                message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryAdd3Mandatory');?>
";
                highlight("mainadd43");
            }
        }

        if ((document.getElementById("citycompulsory")) && (document.getElementById("citycompulsory").src.indexOf("/images/asterisk.png") != -1))
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

        if ((document.getElementById("countycompulsory")) && (document.getElementById("countycompulsory").src.indexOf("/images/asterisk.png") != -1))
        {
            if (county.length == 0)
            {
                message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryCountyMandatory');?>
";
                highlight("maincounty");
                highlight("countylist");
            }
        }

        if ((document.getElementById("statecompulsory")) && (document.getElementById("statecompulsory").src.indexOf("/images/asterisk.png") != -1))
        {
            if (state.length == 0)
            {
                message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryStateMandatory');?>
";
                highlight("mainstate");
                highlight("statelist");
            }
        }

        if ((document.getElementById("postcodecompulsory")) && (document.getElementById("postcodecompulsory").src.indexOf("/images/asterisk.png") != -1))
        {
            if (postcode.length == 0)
            {
                message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryPostCodeMandatory');?>
";
                highlight("mainpostcode");
            }
        }

    

<?php }?>


        if ((document.getElementById("telephonenumbercompulsory")) && (document.getElementById("telephonenumbercompulsory").src.indexOf("/images/asterisk.png") != -1))
        {
            if (telephonenumber.length == 0)
            {
                message += "\n" + "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCompulsoryPhoneMandatory');?>
";
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

        /* copy the values into the form we will submit and then submit it to the server */
        document.submitformaddress.contactfname.value = firstname;
        document.submitformaddress.contactlname.value = lastname;


<?php if ($_smarty_tpl->tpl_vars['edit']->value == 0) {?>


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


<?php }?>


        document.submitformaddress.telephonenumber.value = telephonenumber;

        <?php if ($_smarty_tpl->tpl_vars['useraddressupdated']->value != 2) {?>
            document.submitformaddress.email.value = email;
        <?php } else { ?>
            document.submitformaddress.email.value = "<?php echo $_smarty_tpl->tpl_vars['email']->value;?>
";
        <?php }?>

        document.submitformaddress.registeredtaxnumbertype.value = registeredTaxNumberType;
        document.submitformaddress.registeredtaxnumber.value = registeredTaxNumber;

        if (gAlerts > 0)
        {

        

        <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

            

            createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", nlToBr(message), function(e) {
                closeDialog(e);
            });

            

        <?php } else { ?>

            

            alert(message);
            return false;

            

        <?php }?>

        

        }
        else
        {
        	

        	<?php if (($_smarty_tpl->tpl_vars['useraddressupdated']->value == 0 || $_smarty_tpl->tpl_vars['useraddressupdated']->value == 2)) {?>

        		
        			document.submitformaddress.fsaction.value = "Order.updateAccountDetails";
        		

        	<?php } else { ?>

				
				if (gAddressType == "shipping")
				{
					document.submitformaddress.fsaction.value = "Order.changeShippingAddress";
				}
				else
				{
					document.submitformaddress.fsaction.value = "Order.changeBillingAddress";
				}
				
        	<?php }?>

        <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

            

            showLoadingDialog()

            if (gCurrentSource == 'jobticket')
            {


                var postParams = '&contactfname=' + encodeURIComponent(firstname);
                postParams += '&contactlname=' + encodeURIComponent(lastname);

                

                <?php if ($_smarty_tpl->tpl_vars['edit']->value == 0) {?>

                    

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

                    

                <?php }?>

                

                postParams += '&telephonenumber=' + encodeURIComponent(telephonenumber);
                postParams += '&email=' + encodeURIComponent(document.submitformaddress.email.value);
                postParams += '&registeredtaxnumbertype=' + encodeURIComponent(registeredTaxNumberType);
                postParams += '&registeredtaxnumber=' + encodeURIComponent(registeredTaxNumber);

                postParams += '&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
';
                postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';
                postParams += '&fsactionorig=' + document.submitformaddress.fsaction.value;

                if (document.getElementById("shippingcfscontact"))
                {
                    var shippingcfscontact = document.getElementById("shippingcfscontact").value;
                    postParams += '&shippingcfscontact=' + shippingcfscontact;
                }
              
				

				<?php if (($_smarty_tpl->tpl_vars['useraddressupdated']->value == 0 || $_smarty_tpl->tpl_vars['useraddressupdated']->value == 2)) {?>

					

                        processAjaxAddress("refreshshippingPanel",".?fsaction=AjaxAPI.callback&cmd=UPDATEACCOUNTDETAILS", 0, 'POST', postParams);
					

				<?php } else { ?>

					
					if (gAddressType == "shipping")
					{
						processAjaxAddress("refreshshippingPanel",".?fsaction=AjaxAPI.callback&cmd=CHANGESHIPPINGADDRESS", 0, 'POST', postParams);
					}
					else
					{
						processAjaxAddress("refreshshippingPanel",".?fsaction=AjaxAPI.callback&cmd=CHANGEBILLINGADDRESS", 0, 'POST', postParams);
					}
					
				<?php }?>
				
            }
            else
            {
                document.submitformaddress.submit();
                return false;
            }

            

        <?php } else { ?>

            

            document.submitformaddress.submit();
            return false;

            

        <?php }?>

        

        }

    }

    function setCountry()
    {
        <?php if ($_smarty_tpl->tpl_vars['addresstype']->value == 'shipping' && $_smarty_tpl->tpl_vars['useraddressupdated']->value == 1) {?>
            var hideConfigFields = 1;
        <?php } elseif (($_smarty_tpl->tpl_vars['addresstype']->value == 'shipping') && ($_smarty_tpl->tpl_vars['useraddressupdated']->value == 0 || $_smarty_tpl->tpl_vars['useraddressupdated']->value == 2)) {?>
            var hideConfigFields = 0;
        <?php } else { ?>
            var hideConfigFields = 0;
        <?php }?>

        /* save field data so it can be restored afterwards */
        saveFields();
        countryChanged = true;
		processAjaxAddress("ajaxdivupdate",".?fsaction=AjaxAPI.callback&cmd=ADDRESSFORM&country=" + country + "&hideconfigfields=" + hideConfigFields +"&addresstype=" + gAddressType + "&strict=1&edit=<?php echo $_smarty_tpl->tpl_vars['edit']->value;?>
", 1, 'GET', '');
	}

    function changeState()
    {
        saveFields();

        

        <?php if ($_smarty_tpl->tpl_vars['autosuggestavailable']->value == 1) {?>

            

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

            

        <?php }?>

        
    }


    function verifyAddress()
    {
        saveFields();

        

        <?php if ($_smarty_tpl->tpl_vars['autosuggestavailable']->value == 1) {?>

            

        processAjaxAddress("verify",".?fsaction=AjaxAPI.callback&cmd=ADDRESSVERIFICATION&city=" + city +
        "&county=" + county + "&statecode=" + regioncode + "&statevalue=" + state + "&postcode=" + postcode + "&region=" + region +
        "&country=" + country +
        "&addresstype=" + gAddressType, 1, 'GET', '');

            

        <?php } else { ?>

            

            acceptDataAddressEntry('match');

            

        <?php }?>

        

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
                images[t].src = "<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/asterisk.png";
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
						showLoadingDialog('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageLoading');?>
')
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

                    

                    <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

                        

                        createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageUpdateAddressDetails');?>
", function(e) {
                            closeDialog(e);
                        });

                        

                    <?php } else { ?>

                        

                        alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageUpdateAddressDetails');?>
");

                        

                    <?php }?>

                    

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
							
														<?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>
									closeLoadingDialog();
							<?php } else { ?>
									hideLoadingDialog();
							<?php }?>
							
							
                            document.getElementById(obj).innerHTML = xmlhttp.responseText;
                            if (gMessage != '')
                            {
                                document.getElementById("message").innerHTML = gMessage;
                            }
                            restoreFields();

                            

                            <?php if ($_smarty_tpl->tpl_vars['autosuggestavailable']->value == 1) {?>

                                

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

                                

                            <?php }?> 
                            <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

                                

                                setScrollAreaHeight('contentRightScrollAddress', gBackButtonDisplay);

                                

                            <?php }?> 							                            
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
                            toggleJs('mainjavascript', jsonObj.javascript, true, '', '<?php if ((isset($_smarty_tpl->tpl_vars['nonceraw']->value))) {
echo $_smarty_tpl->tpl_vars['nonceraw']->value;
} else { ?>[nonce]<?php }?>');
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
        <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

        /* set a cookie to store the local time */
        var theDate = new Date();
        createCookie("mawebtz", Math.round(theDate.getTime() / 1000), 2);
        initializeSmallScreenVersion(true);

        <?php }?>

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
                <?php if ($_smarty_tpl->tpl_vars['shippingcfscontact']->value == '1') {?>
                    collectionContactDetails();
                <?php } else { ?>
                    verifyAddress();
                <?php }?>
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

<?php }
}
