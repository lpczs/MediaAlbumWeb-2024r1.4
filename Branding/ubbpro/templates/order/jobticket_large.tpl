<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="csrf-token" content="{csrf_token}" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{$appname} - {$title}</title>

        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}
        
        {include file="includes/customerinclude_large.tpl"}

        {if ($stage=='qty')}
            {assign var='modalHeight' value='348' scope='global'}
            {assign var='modalWidth' value='650' scope='global'}
        {/if}
    <style type="text/css">
{literal}
        #componentChangeBox {
            width: {/literal}{$modalWidth}{literal}px;
        }
        #componentChangeBox .content {
            height: {/literal}{$modalHeight}{literal}px;
        }
{/literal}
    </style>
    <script type="text/javascript" {$nonce}>
        //<![CDATA[

{include file="order/jobticket.tpl"}

{literal}

        window.addEventListener('DOMContentLoaded', function(e) {
            document.body.addEventListener('click', decoratorListener);
            document.body.addEventListener('change', decoratorListener);
            document.body.addEventListener('keypress', decoratorListener);
            document.body.addEventListener('keyup', decoratorListener);
            document.body.addEventListener('submit', decoratorListener);
        });

    {/literal}
    {if ($stage == 'qty' || $stage == 'payment')}
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

			for (var i = 0; i < productPreviewElementsLength; i++)
			{
				var projectThumbnail = productPreviewElements[i];

				// Only attach listener to the parent order item and not the companions.
				if (projectThumbnail.dataset.asset !== undefined)
				{
					projectThumbnail.addEventListener('error', function()
					{
						if (this.dataset.asset !== '')
						{
							// Load the product thumbnail image.
							this.src = this.dataset.asset;
						}
						else
						{
							// Revert to the default blank image if there is no project or product thumbnail available.
							this.src = '{/literal}{$brandroot}{literal}/images/no_image-2x.jpg';
						}
					});
				}
			}
		});

	{/literal}
	{/if}
		/**
		 * Prevents a form from being submitted.
		 *
		 * @param pElement HTMLElement Element that fired the event.
		 * @param pEvent Event Event object.
		 */
		function fnPreventFormSubmit(pElement, pEvent) 
		{
			pEvent.preventDefault();
		};
	
	{if ($stage == 'qty')}
	{literal}
        // Update checkbox action function
        function fnUpdateCheckbox(pElement)
        {
            return updateCheckbox(pElement.getAttribute("data-orderlineid"), pElement.getAttribute("data-sectionlineid"));
        };

        // Set component selected from change component dialog window.
        function fnSelectComponent(pElement)
        {
            selectComponent(pElement.getAttribute("data-lineid"), pElement.getAttribute("data-section"));
        }

        // Close change component dialog window.
        function fnCloseWindow()
        {
            closeWindow();
        }

        // Set the active component in the change component dialog.
        function fnSetItemActive(pElement)
        {
            setItemActive(pElement);
        }

        // Show or hide the component information in the component dialog.
        function fnShowInfo(pElement)
        {
            showInfo(pElement.getAttribute("data-previewindex"));
        }

        // Set the active component of radio button inputs
        function fnSetComponentActive(pElement)
        {
            setComponentItemActive(pElement.getAttribute("id"));
        }

        // Force input entry to be uppercase. 
        function fnForceUpperAlphaNumericMetaData(pElement)
        {
            forceUpperAlphaNumericMetaData(pElement);
        }

    {/literal}
    {elseif ($stage == 'shipping')}

        {assign var='showAllCode' value='***ALL***'}
        {literal}

        var gShowAllCode = "{/literal}{$showAllCode}{literal}";
        var gCountryCode = "{/literal}{$countrylist[0].code}{literal}";
        var gSearchCountry = "{/literal}{$initialfilter.country}{literal}";
        var gSearchRegion = "{/literal}{$initialfilter.region}{literal}";
        var gSearchStoreGroup = "{/literal}{$initialfilter.storeGroup}{literal}";
        var gSearchText = "{/literal}{$initialfilter.filter}{literal}";
        var gPrivateSearchText = "{/literal}{$initialfilter.privateFilter}{literal}";
        var gStoreLocatorType = 'STORELOCATOR'

        function changeCountry(countryCode)
        {
            gCountryCode = countryCode;
            var countryObject = document.getElementById('countries');
            /* update region drop-down */
            var selectObject = document.getElementById('regions');
            if (selectObject)
            {
                var usedRegions = {};
                selectObject.options.length=0;
                selectObject.options[selectObject.length] = new Option("{/literal}{#str_RegionOptional#}{literal}", gShowAllCode, true, false);
                selectObject.disabled = true;
                for (var i=0, len=gCountryCodes.length; i < len; ++i)
                {
                    if (gCountryCodes[i] == countryCode)
                    {
                        if (!(gRegionCodes[i] in usedRegions) && (gRegionCodes[i] != ''))
                        {
                            usedRegions[gRegionCodes[i]] = i;
                            if (selectObject.length == 0)
                            {
                                selectObject.options[selectObject.length] = new Option("{/literal}{#str_RegionOptional#}{literal}", gShowAllCode, true, false);
                            }
                            selectObject.disabled = true;
                            selectObject.options[selectObject.length] = new Option(gRegionNames[i], gRegionCodes[i], false, false);
                            var selectedCountry = countryObject.options[countryObject.selectedIndex].value;
                            if(selectedCountry != gShowAllCode && selectedCountry != gShowAllCode)
                            {
                                selectObject.disabled = false;
                            }
                        }
                    }
                }
                if (selectObject.length == 2)
                {
                    selectObject.selectedIndex = 1;
                    selectObject.disabled = true;
                }
            }

            /* update store group drop-down list */
            var selectObject = document.getElementById('storegroups');
            if (selectObject)
            {
                var usedStoreGroups = {};
                var countryObject = document.getElementById('countries');
                selectObject.options.length=0;
                selectObject.options[0] = new Option("{/literal}{#str_SiteGroupOptional#}{literal}", gShowAllCode, true, false);
                selectObject.disabled = true;
                for (var i=0, len=gCountryCodes.length; i < len; ++i)
                {
                    if (gCountryCodes[i] == countryCode)
                    {
                        if (!(gSiteGroupCodes[i] in usedStoreGroups))
                        {
                            usedStoreGroups[gSiteGroupCodes[i]] = i;
                            if (selectObject.length == 0)
                            {
                                selectObject.options[selectObject.length] = new Option("{/literal}{#str_SiteGroupOptional#}{literal}", gShowAllCode, true, false);
                                selectObject.disabled = true;
                            }
                            selectObject.disabled = false;
                            selectObject.options[selectObject.length] = new Option(gSiteGroupNames[i], gSiteGroupCodes[i], false, false);
                            var selectedCountry = countryObject.options[countryObject.selectedIndex].value;
                            if(selectedCountry != gShowAllCode && selectedCountry != '')
                            {
                                selectObject.disabled = false;
                            }
                        }
                    }
                }
                if (selectObject.length == 2)
                {
                    selectObject.selectedIndex = 1;
                    selectObject.disabled = true;
                }
            }
        }

        function changeRegion(regionCode)
        {
            /* get country code */
            var countryCode = gCountryCode;
            if (countryCode == gShowAllCode)
            {
                countryCode = "";
            }

            /* update store group drop-down list */
            var selectObject = document.getElementById('storegroups');
            if (selectObject)
            {
                var countryObject = document.getElementById('countries');
                var usedStoreGroups = {};
                selectObject.options.length=0;
                selectObject.options[selectObject.length] = new Option("{/literal}{#str_SiteGroupOptional#}{literal}", gShowAllCode, true, false);
                selectObject.disabled = true;
                for (var i=0, len=gCountryCodes.length; i < len; ++i)
                {
                    if ((gCountryCodes[i] == countryCode) && ((gRegionCodes[i] == regionCode) || (regionCode == gShowAllCode)))
                    {
                        if (!(gSiteGroupCodes[i] in usedStoreGroups))
                        {
                            usedStoreGroups[gSiteGroupCodes[i]] = i;
                            if (selectObject.length == 0)
                            {
                                selectObject.options[selectObject.length] = new Option("{/literal}{#str_RegionOptional#}{literal}", gShowAllCode, true, false);
                                selectObject.classList.remove("otherclass");
                                selectObject.disabled = true;
                            }
                            selectObject.disabled = false;
                            selectObject.options[selectObject.length] = new Option(gSiteGroupNames[i], gSiteGroupCodes[i], false, false);
                            
                            if (countryObject)
                            {
                                var selectedCountry = countryObject.options[countryObject.selectedIndex].value;
                                if(selectedCountry != gShowAllCode && selectedCountry != gShowAllCode)
                                {
                                    selectObject.disabled = false;
                                }
                            }
                        }
                    }
                }
                if (selectObject.length == 2)
                {
                    selectObject.selectedIndex = 1;
                    selectObject.disabled = true;
                }
            }
        }

        function selectOptionByValue(sel, val)
        {
            var selObj = document.getElementById(sel);
            if (selObj)
            {
                var opt = selObj.options, len = opt.length;
                while(len)
                {
                    if (opt[--len].value == val)
                    {
                        selObj.selectedIndex = len;
                        len = 0;
                    }
                }
            }
        }

        function resizeFormElement()
        {
            // make sure the html is loaded before calculate the size
            setTimeout(function()
            {
                // force size of the image container if an image is displayed
                var storeLogoImg = document.getElementById('storeLogoImg');
                if (storeLogoImg)
                {
                    var styleLogoImg = storeLogoImg.currentStyle || window.getComputedStyle(storeLogoImg);
                    var imgHeight = parseIntStyle(styleLogoImg.height);
                    var imgWidth = parseIntStyle(styleLogoImg.width);

                    var storeLogo = document.getElementById('storeLogo');
                    var styleStoreLogo = storeLogo.currentStyle || window.getComputedStyle(storeLogo);
                    var maxHeight = parseIntStyle(styleStoreLogo.maxHeight);

                    storeLogoImg.style.maxWidth = (parseIntStyle(styleStoreLogo.width) - (parseIntStyle(styleStoreLogo.marginLeft) + parseIntStyle(styleStoreLogo.marginRight))) + 'px';
                }

                closeLoadingDialog();
            }, 500);
        }

        function resizeResultElement()
        {
            // size of container
            var container = document.getElementById('storeList');
            var contentStore = document.getElementsByClassName('contentStoreDetail')[0];
            if (contentStore)
            {
                // tick image width
                if (gTickImageWidth == 0 )
                {
                    var checkboxImage = container.getElementsByClassName('checkboxImage')[0];
                    var styleCheckboxImage = checkboxImage.currentStyle || window.getComputedStyle(checkboxImage);
                    gTickImageWidth =  parseIntStyle(styleCheckboxImage.width) + parseIntStyle(styleCheckboxImage.marginLeft) + parseIntStyle(styleCheckboxImage.marginRight);
                }

                var width = gOuterBoxContentBloc - gTickImageWidth;

                // size of the info image
                var contentInfo = container.getElementsByClassName('imgInfo')[0];
                widthBtnInfo = 0;
                if (contentInfo)
                {
                    var styleInfo = contentInfo.currentStyle || window.getComputedStyle(contentInfo);
                    widthBtnInfo = parseIntStyle(styleInfo.width) + parseIntStyle(styleInfo.marginLeft) + parseIntStyle(styleInfo.marginRight);
                }

                var classLength = container.getElementsByClassName('listLabel').length;
                for (var i = 0; i < classLength; i++)
                {
                    var elm = container.getElementsByClassName('listLabel')[i];

                    if (i == 0)
                    {
                        var styleLabel = elm.currentStyle || window.getComputedStyle(elm);
                        width = width - parseIntStyle(styleLabel.paddingLeft) - parseIntStyle(styleLabel.paddingRight);
                    }

                    elm.style.width = (width - widthBtnInfo) + 'px';
                }
            }
        }

        // Set the active component of radio button inputs
        function fnShippingMethodClick(pElement)
        {
            return shippingMethodClick(pElement.getAttribute("data-cfs"));
        }

        function fnShippingMethodCfsClick(pElement)
        {
            return shippingMethodCfsClick(pElement.getAttribute("data-ratecode"), pElement.getAttribute("data-script"), true);
        }

        function fnSelectStore(pElement)
        {
            return selectStore(pElement.getAttribute("data-method"));
        }

        function fnChangeCollectionDetails(pElement)
        {
            changeShippingAddress('CFS');
        }

        function fnShowStoreInfo(pElement)
        {
            return showStoreInfo(pElement.getAttribute("data-storecode"), pElement.getAttribute("data-externalstore"));
        }

        function fnCloseStoreInfo(pElement)
        {
            return closeStoreInfo();
        }

        function fnActivateStore(pElement)
        { 
            return activeStore(pElement.getAttribute("data-storecode"), pElement.getAttribute("data-payinstore"));
        }

        // Close the store selection.
        function fnDoneSelectStoreButton(pElement)
        {
            var shimObj = document.getElementById('shim');
            var storeLocatorObj = document.getElementById('storeLocator');
            if (shimObj)
            {
                shimObj.style.display = 'none';
            }
            if (storeLocatorObj)
            {
                storeLocatorObj.style.display = 'none';
            }
            if (document.documentElement.style.overflow = 'hidden')
            {
                document.documentElement.style.overflow = '';
            }
            document.body.className = document.body.className.replace(' hideSelects', '');
            
            return false;
        }

        function acceptDataEntryStoreLocator()
        {
            var storeCode = '';
            var isExternalStore = 0;
            
            var storeObj = document.mainformstore.store;
            if (storeObj)
            {
                var objLength = storeObj.length;
                if (objLength == undefined)
                {
                    if (storeObj.checked)
                    {
                        storeCode = storeObj.value;
                        isExternalStore = storeObj.dataset.externalstore;
                    }
                }
                else
                {
                    for (var i = 0; i < objLength; i++)
                    {
                        if (storeObj[i].checked)
                        {
                            storeCode = storeObj[i].value;
                            isExternalStore = storeObj[i].dataset.externalstore;
                            break;
                        }
                    }
                }
            }

            if (storeCode == "")
            {
                alert("{/literal}{#str_ErrorNoStore#}{literal}");
                return false;
            }
            else
            {
                var fields = '&storecode=' + storeCode + '&externalstore=' + isExternalStore + '&country=' + gSearchCountry + '&region=' + gSearchRegion + 
                    '&storegroup=' + gSearchStoreGroup + '&filter=' + gSearchText + '&privatefilter=' + gPrivateSearchText + '&payinstoreallowed=' + gPayInStoreOption +
                    '&stage={/literal}{$stage}&previousstage={$previousstage}&shippingratecode={$shippingratecode}&sameshippingandbillingaddress={$sameshippingandbillingaddress}&shippingcfscontact={$shippingcfscontact}{literal}';

                var csrfToken = fetchCsrfToken();
                processAjax("selectStore",".?fsaction=Order.selectStore" + fields, 'POST', '');
            }

            return false;
        }

        // wrapper for search for store
        function fnSearchForStore(pElement, pEvent)
        {
            switch (pElement.getAttribute("data-trigger"))
            {
                case 'keypress': // Input box
                    if (enterKeyPressed(pEvent))
                    {
                        pEvent.preventDefault();

                        searchForStore();
                    }

                    break;

                case 'click': // Refresh image clicked
                    searchForStore();

                    break;
            }

            return false;
        }     

        function searchForStore(pPrivateSearch)
        {
            var countryObj = document.getElementById('countries');
            var regionObj = document.getElementById('regions');
            var storeGroupObj = document.getElementById('storegroups');
            var countryCode = '';
            var regionCode = '';
            var storeGroupCode = '';
            var addressSearch = document.getElementById('searchText').value;

            if (countryObj)
            {
                countryCode = countryObj.value;
                if (countryCode == gShowAllCode)
                {
                    countryCode = "";
                }
            }
            if (regionObj)
            {
                regionCode = regionObj.value;
                if (regionCode == gShowAllCode)
                {
                    regionCode = "";
                }
            }
            if (storeGroupObj)
            {
                storeGroupCode = storeGroupObj.value;
                if (storeGroupCode == gShowAllCode)
                {
                    storeGroupCode = "";
                }
            }
            gSearchCountry = countryCode;
            gSearchRegion = regionCode;
            gSearchStoreGroup = storeGroupCode;
            gSearchText = addressSearch;
            
            origPrivateSearch = gPrivateSearchText;
            
            if (typeof pPrivateSearch != 'undefined')
            {
                gPrivateSearchText = pPrivateSearch;
            }

            searchText = "&country=" + countryCode + "&region=" + regionCode + "&storegroup=" + storeGroupCode + "&filter=" + encodeURIComponent(addressSearch) + "&privatefilter=" + gPrivateSearchText;

            if (gCollectFromStoreCode != '')
            {
                searchText = searchText + "&store=" + gCollectFromStoreCode;
            }
            gPrivateSearchText = origPrivateSearch;

            processAjax("storeListAjaxDiv",".?fsaction=AjaxAPI.callback&cmd=" + gStoreLocatorType + searchText, 'GET', '', searchForStore);
            document.getElementById('storeList').style.display = "block";
            return false;
        }

    {/literal}
    {/if}
    {literal}

        // Show / hide companion product options link.
        function fnToggleGeneric(pElement)
        {
            toggleGeneric(pElement.getAttribute("data-lineid"), pElement.getAttribute("data-idelm"), pElement.getAttribute("data-colour"));
        }

        window.onload = function()
        {
    {/literal}

        {$custominit}

    {if (($showgiftcardmessage == 1) && ($stage == 'payment'))}{literal}
            displayGiftCardAlert('{/literal}{$voucherstatusResult}{literal}', '{/literal}{$vouchercustommessage}{literal}');
    {/literal}{/if}

        {$initlanguage}

        {if ($stage == 'companionselection')}{literal}
            // Add listener to the top continue button on companion albums.
            var topContinuebuttonElement = document.getElementById('topordercontinuebutton');
            if (topContinuebuttonElement)
            {
                topContinuebuttonElement.addEventListener('click', function() {
                    acceptDataEntry();
                });
            }

            // Add listeners to companion item add buttons.
            var classname = document.getElementsByClassName('companionAddButton');
            for (var i = 0; i < classname.length; i++)
            {
                classname[i].addEventListener('click', function() {
                    var companionOptionCode = this.getAttribute("data-productcode");
                    var targetUniqueCompanionID = this.getAttribute("data-companionid");
                    var parentOrderLineID = this.getAttribute("data-parentlineitem");

                    incrementCompanionQTY(companionOptionCode, targetUniqueCompanionID, parentOrderLineID);
                });
            }

            // Add listeners to companion item change quantity buttons.
            var classname = document.getElementsByClassName('companionBtnQtyChange');
            for (var i = 0; i < classname.length; i++)
            {
                classname[i].addEventListener('click', function() {
                    var companionOptionCode = this.getAttribute("data-productcode");
                    var targetUniqueCompanionID = this.getAttribute("data-companionid");
                    var parentOrderLineID = this.getAttribute("data-parentlineitem");
                    var actionButtonMode = this.getAttribute("data-mode");

                    if ("1" == actionButtonMode)
                    {
                        incrementCompanionQTY(companionOptionCode, targetUniqueCompanionID, parentOrderLineID);
                    }
                    else
                    {
                        decrementCompanionQTY(companionOptionCode, targetUniqueCompanionID, parentOrderLineID);
                    }
                });
            }

            // Add listeners to companion item add buttons.
            var classname = document.getElementsByClassName('companionQtyValue');
            for (var i = 0; i < classname.length; i++)
            {
                classname[i].addEventListener('change', function() {
                    var companionOptionCode = this.getAttribute("data-productcode");
                    var targetUniqueCompanionID = this.getAttribute("data-companionid");
                    var parentOrderLineID = this.getAttribute("data-parentlineitem");
                    
                    manualChangeCompanionQty(companionOptionCode, targetUniqueCompanionID, parentOrderLineID);
                });
        
                // prevent the enter key triggering a form submit.
                classname[i].addEventListener('keypress', function(event) {
                    if (enterKeyPressed(event))
                    {
                        event.preventDefault();

                        var companionOptionCode = this.getAttribute("data-productcode");
                        var targetUniqueCompanionID = this.getAttribute("data-companionid");
                        var parentOrderLineID = this.getAttribute("data-parentlineitem");
                        
                        manualChangeCompanionQty(companionOptionCode, targetUniqueCompanionID, parentOrderLineID);
                    }
                });
            }
        {/literal}
        {elseif ($stage == 'shipping')}
        {literal}
            // Add listeners to change shipping address button.
            var changeShippingAddressButton = document.getElementById('changeshipping');
            if (changeShippingAddressButton)
            {
                changeShippingAddressButton.addEventListener('click', function() {
                    changeShippingAddress();
                });
            }

            // Add listeners to change billing address button.
            var changeBillingAddressButton = document.getElementById('changebilling');
            if (changeBillingAddressButton)
            {
                changeBillingAddressButton.addEventListener('click', function() {
                    changeBillingAddress();
                });
            }

            // Add listeners to change billing address button.
            var sameAddressButton = document.getElementById('sameasshippingaddress');
            if (sameAddressButton)
            {
                sameAddressButton.addEventListener('click', function() {
                    return setSameAddress();
                });
            }
        {/literal}
        {elseif ($stage == 'payment')}
        {literal}
            // Add listeners to the voucher input.
            var voucherInputElement = document.getElementById('vouchercode');
            if (voucherInputElement)
            {
                voucherInputElement.addEventListener('keyup', function(event) {
                    if (voucherInputElement.value != '')
                    {
                        forceUpperAlphaNumeric(this);

                        if (enterKeyPressed(event))
                        {
                            setVoucher();
                        }
                    }
            
                    return false;
                });
            }

            // Add listener to the redeem voucher button.
            var setVoucherButton = document.getElementById('setvoucher');
            if (setVoucherButton)
            {
                setVoucherButton.addEventListener('click', function(event) {
                    if (document.getElementById('vouchercode').value != '')
                    {
                        setVoucher();
                    }
                });
            }

            // Add listener to the remove voucher button.
            var removeVoucherButton = document.getElementById('removevoucher');
            if (removeVoucherButton)
            {
                removeVoucherButton.addEventListener('click', function(event) {
                    removeVoucher();
                });
            }

            // Add listeners to the gift card input.
            var giftCardInputElement = document.getElementById('giftcardcode');
            if (giftCardInputElement)
            {
                giftCardInputElement.addEventListener('keyup', function(event) {
                    if (giftCardInputElement.value != '')
                    {
                        forceUpperAlphaNumeric(this);

                        if (enterKeyPressed(event))
                        {
                            setGiftCard();
                        }
                    }
            
                    return false;
                });
            }

            // Add listener to the redeem gift card button.
            var setGiftCardButton = document.getElementById('setgiftcard');
            if (setGiftCardButton)
            {
                setGiftCardButton.addEventListener('click', function(event) {
                    if (document.getElementById('giftcardcode').value != '')
                    {
                        setGiftCard();
                    }
                });
            }

            // Add listener to the use/remove gift card button.
            var changeGiftCardElement = document.getElementById('giftbutton');
            if (changeGiftCardElement)
            {
                changeGiftCardElement.addEventListener('click', function(event) {
                    changeGiftCard();
                });
            }

            {/literal}
            {if $showtermsandconditions == 1}
            {literal}
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

                // Add listener to the close terms and conditions window button.
                var termsCloseButton = document.getElementById('closetermsandconditionswindow');
                if (termsCloseButton)
                {
                    termsCloseButton.addEventListener('click', function(event) {
                        closeTermsAndCondition();
                    });
                }

                // Add listener to the close terms and conditions window button.
                var acceptTermsCheck = document.getElementById('ordertermsandconditions');
                if (acceptTermsCheck)
                {
                    acceptTermsCheck.addEventListener('click', function(event) {
                        acceptTermsAndConditions();
                    });
                }
            {/literal}
            {/if}
        {/if}
        
        {literal}
            // Add listener to the bottom continue button.
            var topContinuebuttonElement = document.getElementById('ordercontinuebutton');
            if (topContinuebuttonElement)
            {
				var listenerCallFunction = '';
				{/literal}{if $stage != 'payment'}{literal}
                listenerCallFunction = acceptDataEntry;
				{/literal}{else}{literal}
				listenerCallFunction = orderButtonCompleteOrder;
				{/literal}{/if}{literal}

				// If we have a function to call on click of the ordercontinuebutton attach it.
				if (listenerCallFunction !== '')
				{
					topContinuebuttonElement.addEventListener('click', listenerCallFunction);
				}
            }

            // Add listener to the back button.
            var backButtonElement = document.getElementById('backButton');
            if (backButtonElement)
            {
                backButtonElement.addEventListener('click', function() {
                    previousOrderStage();
                });
            }

            // Add listener to the cancel order button.
            document.getElementById('cancelOrderButton').addEventListener('click', function() {
                cancelOrder();
            });

            // Add listener to langauge select.
            document.getElementById('systemlanguagelist').addEventListener('change', function() {
                return setSystemLanguage();
            });

            // Add listener to window resize.
            window.addEventListener('resize', function() {
                resizePopup();
            });

			if (typeof(acceptTermsAndConditions) === 'function')
			{
				acceptTermsAndConditions();
			}
        }

		function resizePopup()
		{
            var storeLocator = document.getElementById('storeLocator');
			var storeInfo = document.getElementById('storeInfo');
			var ordersTermsAndCondtions = document.getElementById('ordersTermsAndCondtions');
			var componentChangeBox = document.getElementById('componentChangeBox');
			var shimObj = document.getElementById('shim');
			var windowHeight = document.documentElement.clientHeight;
            var dialogBox = document.getElementById('dialogOuter');

			if ((storeLocator) && (shimObj) && (storeLocator.style.display == "block"))
			{
				shimObj.style.height = document.body.offsetHeight + 'px';

				storeLocator.style.left = Math.round((shimObj.offsetWidth / 2) - (storeLocator.offsetWidth / 2)) + 'px';

				var finalPosition = (document.documentElement.clientHeight - storeLocator.offsetHeight) / 2;
				storeLocator.style.top = Math.round(finalPosition) + 'px';
			}

			if ((storeInfo) && (shimObj) && (storeInfo.style.display == "block"))
			{
				var viewportWidth =  Math.max(
					Math.max(document.body.offsetWidth, document.documentElement.offsetWidth),
					Math.max(document.body.clientWidth, document.documentElement.clientWidth)
				);

				windowHeight = document.documentElement.clientHeight;
				finalPosition = (windowHeight - storeInfo.offsetHeight) / 2;

				storeInfo.style.top = Math.round(finalPosition) + 'px';

				storeInfo.style.left = Math.round(viewportWidth * 1/2 - storeInfo.offsetWidth * 1/2) + 'px';
			}

			if ((ordersTermsAndCondtions) && (shimObj) && (ordersTermsAndCondtions.style.display == "block"))
			{
				shimObj.style.height = document.body.offsetHeight + 'px';

				ordersTermsAndCondtions.style.left = Math.round(shimObj.offsetWidth / 2 - ordersTermsAndCondtions.offsetWidth/2)+'px';

				var viewportHeight =  Math.max(
					Math.max(document.body.offsetHeight, document.documentElement.offsetHeight),
					Math.max(document.body.clientHeight, document.documentElement.clientHeight)
				);
				viewportHeight = document.documentElement.clientHeight;
				ordersTermsAndCondtions.style.top = Math.round(viewportHeight / 2 - ordersTermsAndCondtions.offsetHeight/2) + 'px';
			}
            if ((dialogBox) && (shimObj) && (dialogBox.style.display == "block"))
            {
                shimObj.style.height = document.body.offsetHeight + 'px';

				dialogBox.style.left = Math.round(shimObj.offsetWidth / 2 - dialogBox.offsetWidth/2)+'px';

				var viewportHeight =  Math.max(
					Math.max(document.body.offsetHeight, document.documentElement.offsetHeight),
					Math.max(document.body.clientHeight, document.documentElement.clientHeight)
				);
				viewportHeight = document.documentElement.clientHeight;
				dialogBox.style.top = Math.round(viewportHeight / 2 - dialogBox.offsetHeight/2) + 'px';
            }
        {/literal}
        {if ($stage=='qty')}
        {literal}
			if ((componentChangeBox) && (shimObj) && (componentChangeBox.style.display == "block"))
			{
				shimObj.style.height = document.body.offsetHeight + 'px';

				componentChangeBox.style.left = Math.round((shimObj.offsetWidth / 2) - ({/literal}{$modalWidth}{literal}/2)) + 'px';
				windowHeight = document.documentElement.clientHeight;
				componentChangeBox.style.top = Math.round((windowHeight - componentChangeBox.offsetHeight) / 2) + 'px';
			}
        {/literal}
        {/if}
        {literal}
		}

    {/literal}
	{if $stage == 'qty'}
    {literal}

        function previousOrderStage()
        {
			saveTempMetadata();
			document.submitform.fsaction.value = "Order.back";
            document.submitform.submit();
            return false;
        }

	{/literal}
	{/if}

    {if $stage == 'shipping'}
    {literal}

        function getShippingRateCode()
        {
            var shippingRateCode = "";
            var radioObj = document.orderform.shippingmethods;
            if (gShippingRateCode.length != 0)
            {
                var radioLength = radioObj.length;
                if (radioLength == undefined)
                {
                    if (radioObj.checked)
                    {
                        shippingRateCode = radioObj.value;
                    }
                }
                else
                {
                    for (i = 0; i < radioLength; i++)
                    {
                        if (radioObj[i].checked)
                        {
                            shippingRateCode = radioObj[i].value;
                            break;
                        }
                    }
                }
            }

            return shippingRateCode;
        }

        function getShippingRateScript()
        {
            var shippingRateScript = "";
            var radioObj = document.orderform.shippingmethods;
            if (gShippingRateCode.length != 0)
            {
                var radioLength = radioObj.length;
                if (radioLength == undefined)
                {
                    if (radioObj.checked)
                    {
                        shippingRateScript = radioObj.getAttribute('data-script');
                    }
                }
                else
                {
                    for (i = 0; i < radioLength; i++)
                    {
                        if (radioObj[i].checked)
                        {
                            shippingRateScript = radioObj[i].getAttribute('data-script');
                            break;
                        }
                    }
                }
            }

            return shippingRateScript;
        }

        function changeBillingAddress()
        {

            if (document.getElementById('changebilling').getAttribute('disabled'))
            {
                return false;
            }

            if (document.getElementById("sameasshippingaddress").checked == true)
            {
                document.submitform.sameshippingandbillingaddress.value = 1;
            }
            else
            {
                document.submitform.sameshippingandbillingaddress.value = 0;
            }
    {/literal}
    {$metadatasubmit}
    {literal}
            document.submitform.shippingratecode.value = getShippingRateCode();
            document.submitform.fsaction.value = "Order.changeBillingAddressDisplay";
            document.submitform.submit();
            return false;
        }

        function changeShippingAddress(pMode)
        {
            if (document.getElementById("sameasshippingaddress").checked == true)
            {
                document.submitform.sameshippingandbillingaddress.value = 1;
            }
            else
            {
                document.submitform.sameshippingandbillingaddress.value = 0;
            }
    {/literal}
    {$metadatasubmit}
    {literal}
            document.submitform.shippingratecode.value = getShippingRateCode();
            document.submitform.fsaction.value = "Order.changeShippingAddressDisplay";
            if (pMode == 'CFS')
            {
                document.submitform.shippingcfscontact.value = 1;
            }
            document.submitform.submit();
            return false;
        }

        function shippingMethodClick()
        {
			gChangeMethodInPorgress = true;

            document.getElementById('labelShippingStoreAddress').innerHTML = "{/literal}{#str_LabelShippingAddress#}{literal}";
            document.getElementById('shippingStoreAddress').innerHTML = "{/literal}{$encodedshippingaddress}{literal}";
            var csButton = document.getElementById('changeShippingDiv');
            if(csButton)
            {
                csButton.style.display = 'block';
            }
            var csButton = document.getElementById('selectStoreDiv');
            if(csButton)
            {
                csButton.style.display = 'none';
            }
            document.getElementById('sameasshippingaddress').removeAttribute("disabled");
            gCollectFromStore = 0;

            /* loop through all the shpping methods to see which one has been selected */
            for (var i = 0; i < document.getElementsByName('shippingmethods').length; i++)
            {
                if (document.getElementsByName('shippingmethods')[i].checked)
                {
                    var selectedShippingRateCode =  document.getElementsByName('shippingmethods')[i].value;
                }
            }

            document.submitform.shippingratecode.value = selectedShippingRateCode;
            document.submitform.fsaction.value = "Order.changeShippingMethod";
            document.submitform.submit();
            return false;
        }

        function shippingMethodCfsClick(code, script, removeStore, pCallback)
        {
			gChangeMethodInPorgress = true;
            if (typeof gStoreFixed !== 'undefined') 
            {
                if (gStoreFixed[code] != '1')
                {
                    if (document.getElementById('changeCollectionDetailsButton')) {
                        document.getElementById('changeCollectionDetailsButton').setAttribute("data-decorator","");

                        var btnElements = document.getElementById('changeCollectionDetailsButton').children;

                        for (var i = 0; i < btnElements.length; i++)
                        {
                            var oldClass = btnElements[i].className;
                            var newClass = oldClass.replace("-white-", "-disabled-");

                            btnElements[i].className = newClass;
                        }
                    }
                }
            }

            document.getElementById('labelShippingStoreAddress').innerHTML = "{/literal}{#str_LabelStoreAddress#}{literal}";
            var storeaddress = gStoreAddresses[code];

            if (removeStore)
            {
                storeaddress = '';
            }

            document.getElementById('shippingStoreAddress').innerHTML = storeaddress;

            var csButton = document.getElementById('changeShippingDiv');
            if (csButton)
            {
                csButton.style.display = 'none';
            }
            var csButton = document.getElementById('selectStoreDiv');
            if (csButton)
            {
                if (typeof gStoreFixed !== 'undefined') 
                {
                    if (gStoreFixed[code] == '1')
                    {
                        csButton.style.display = 'block';
                    }
                    else
                    {
                        csButton.style.display = 'none';
                    }
                }
            }
            document.getElementById('sameasshippingaddress').setAttribute("disabled","disabled");
            gCollectFromStore = 1;
            gCollectFromStoreCode = '';

            /* loop through all the shpping methods to see which one has been selected */
            for (var i = 0; i < document.getElementsByName('shippingmethods').length; i++)
            {
                if (document.getElementsByName('shippingmethods')[i].checked)
                {
                    var selectedShippingRateCode = document.getElementsByName('shippingmethods')[i].value;
                }
            }
            processAjax("cfschangeshippingmethod",".?fsaction=AjaxAPI.callback&cmd=CFSCHANGESHIPPINGMETHOD&shippingratecode=" + selectedShippingRateCode + "&removestore=" + removeStore, 'POST', '', pCallback);
            return false;
        }

        function selectStore(radioButton)
        {
    {/literal}
    {$metadatasubmit}
    {literal}

            var alreadyChecked = false;
            if (radioButton != '')
            {
                alreadyChecked = document.getElementById(radioButton).checked;
                if (!alreadyChecked) {
                    document.getElementById(radioButton).checked = true;
                }
            }

            var shippingCode = getShippingRateCode();
            var shippingScript = getShippingRateScript();
            gStoreLocatorType = 'STORELOCATOR'

            if (shippingScript == 1) 
            {
                gStoreLocatorType = 'STORELOCATOREXTERNAL'
            }
            
            var sameshippingandbillingaddress = document.submitform.sameshippingandbillingaddress.value;
            if (alreadyChecked)
            {
                doSelectStoreDisplay(shippingCode, sameshippingandbillingaddress);
            }
            else 
            {
                shippingMethodCfsClick(shippingCode, shippingScript, false, function()
                {
                    doSelectStoreDisplay(shippingCode, sameshippingandbillingaddress);
                });
            }

			 document.documentElement.style.overflow = 'hidden';

            return false;
        }

        function doSelectStoreDisplay(shippingCode, sameshippingandbillingaddress) 
        {
            processAjax('storeLocatorForm', ".?fsaction=Order.selectStoreDisplay&stage={/literal}{$stage}{literal}&shippingratecode="+shippingCode+
                "&sameshippingandbillingaddress=" + sameshippingandbillingaddress + "&previousstage={/literal}{$previousstage}{literal}"+
                "&stage={/literal}{$stage}{literal}", 'GET', '');
        }

        function activeStore(iId, payInStoreOption)
        {
            gPayInStoreOption = payInStoreOption;
            document.getElementById(iId).checked = true;
            setStoreActive();
        }

        function setStoreActive()
        {
            var popupBoxContentElem = document.getElementById('storeListAjaxDiv');
            var checkboxes = popupBoxContentElem.getElementsByTagName('input');
            var elemCheck = "";
            for (var i = 0; i < checkboxes.length; i++)
            {
                var elemBox = checkboxes[i].parentNode;
                if (checkboxes[i].checked)
                {
                    elemCheck = elemBox;
                }

                elemBox.className = elemBox.className.replace(' selected', '');
            }
            elemCheck.className = elemCheck.className + ' selected';
        }

        function getShippingRateCode()
        {
            var shippingRateCode = "";
            var radioObj = document.orderform.shippingmethods;
            if (gShippingRateCode.length != 0)
            {
                var radioLength = radioObj.length;
                if (radioLength == undefined)
                {
                    if (radioObj.checked)
                    {
                        shippingRateCode = radioObj.value;
                    }
                }
                else
                {
                    for (i = 0; i < radioLength; i++)
                    {
                        if (radioObj[i].checked)
                        {
                            shippingRateCode = radioObj[i].value;
                            break;
                        }
                    }
                }
            }

            return shippingRateCode;
        }

        function previousOrderStage()
        {

    {/literal}
    {$metadatasubmit}
    {literal}

            document.submitform.fsaction.value = "Order.back";
            document.submitform.submit();
            return false;
        }

    {/literal}
    {/if}

    {if $stage == 'payment'}
	{literal}

        function validateOrderMetaData(pAlertOn)
        {
            var valid = true;
            
            var metadatatablObj = document.getElementById('metadatatableobj');
            if (metadatatablObj)
            {
                var inputs = metadatatablObj.getElementsByTagName('input');
                var textareas = metadatatablObj.getElementsByTagName('textarea');
                var selects = metadatatablObj.getElementsByTagName('select');

                /* get metadata values for all inputs on the page */
                for (var j = 0; j < inputs.length; j++)
                {
                    var txtInput = inputs[j];
                    var grandDiv = (txtInput.parentNode).parentNode;

                    //Get the current style of the input and the grandparent node
                    var styleContentScrollCart = txtInput.currentStyle || window.getComputedStyle(txtInput);
                    var grandStyle = grandDiv.currentStyle || window.getComputedStyle(grandDiv);

                    if (txtInput.type == 'text')
                    {
                        grandDiv = (grandDiv).parentNode;
                        styleContentScrollCart = txtInput.currentStyle || window.getComputedStyle(txtInput);
                        grandStyle = grandDiv.currentStyle || window.getComputedStyle(grandDiv);

                        //Checks if the value of the input has been set, if so then unhighlight the element.
                        if ((txtInput.className.indexOf('required') > -1) && (txtInput.value != ''))
                        {
                            //Check if the class 'metadata-Highlighted' has been assigned to it. If so then remove the red box.
                            var classStringTemp = String (grandDiv.className);
                            var classStringTemp2 = classStringTemp.split(/\s/);
                            var classStringTemp3 = classStringTemp2.indexOf('metadata-Highlighted');

                            if(classStringTemp3 >=0)
                            {
                                var classContent = grandDiv.className;
                                grandDiv.className = classContent.replace('metadata-Highlighted', "").trim();
                            }
                        }
                        else if (txtInput.className.indexOf('required') > -1)
                        {
                            valid = false;

                            if(pAlertOn)
                            {
                                alert("{/literal}{#str_ErrorValueRequired#}{literal}");
                                pAlertOn = false;
                                window.scrollTo(0,0);
                            }

                            //Check if the class 'metadata-Highlighted' has been assigned to it. If so then remove the red box.
                            var classStringTemp = String (grandDiv.className);
                            var classStringTemp2 = classStringTemp.split(/\s/);
                            var classStringTemp3 = classStringTemp2.indexOf('metadata-Highlighted');

                            if(!(classStringTemp3 >=0))
                            {
                            //Draw a red box around the uncompleted divs
                                grandDiv.className += ' metadata-Highlighted';

                                // determine which line item contains the incomplete element
                                grandDivIDArray = grandDiv.id.split("_");

                                if (grandDivIDArray[1] == '-1')
                                {
                                    // order footer - if hidden, toggle open
                                    if(document.getElementById('contentFooter').style.display == "none")
                                    {
                                        toggleGeneric('footer', 'contentFooter', 'white');
                                    }
                                }
                                else
                                {
                                    // line item
                                    var itemElement = "contentCustomise_" + grandDivIDArray[1];

                                    // if hidden, toggle open
                                    if(document.getElementById(itemElement).style.display == "none")
                                    {
                                        // trigger the click to display the line item details
                                        toggleGeneric(grandDivIDArray[1], itemElement, 'grey');
                                    }
                                }

                                if (txtInput.addEventListener)
                                {
                                    txtInput.addEventListener('blur', function()
                                    {
                                        acceptDataEntry(false);
                                    });
                                }
                                else
                                {
                                    txtInput.attachEvent('onblur', function()
                                    {
                                        acceptDataEntry(false);
                                    });
                                }
                            }

                            //Make the text box a little smaller
                            if((parseInt(styleContentScrollCart.width)) >= 750)
                            {
                                var mySize = (parseInt(grandStyle.width)-20)+'px';
                                txtInput.setAttribute("style","width:"+mySize);
                            }
                        }
                    }

                    else if (inputs[j].type == 'radio')
                    {
                        //Need to know the grand parent of the radio input
                        var radioGrandParent = ((inputs[j].parentNode).parentNode).parentNode;

                        //Then get the inputs
                        var radioInputs = radioGrandParent.getElementsByTagName('input');
                        var radioChecker = false;

                        //If at least one input is checked then radioChecker = true
                        for(var k = 0; k < radioInputs.length; k++)
                        {
                            if(radioInputs[k].checked)
                            {
                                radioChecker = true;
                            }
                        }

                        if (radioChecker)
                        {
                            //Check if the class 'metadata-Highlighted' has been assigned to it. If so then remove the red box.
                            var classStringTemp = String (radioGrandParent.className);
                            var classStringTemp2 = classStringTemp.split(/\s/);
                            var classStringTemp3 = classStringTemp2.indexOf('metadata-Highlighted');

                            if(classStringTemp3 >=0)
                            {
                                var classContent = radioGrandParent.className;
                                radioGrandParent.className = classContent.replace('metadata-Highlighted', "").trim();
                            }
                        }
                        else
                        {
                            valid = false;

                            if(pAlertOn)
                            {
                                alert("{/literal}{#str_ErrorValueRequired#}{literal}");
                                pAlertOn = false;
                                window.scrollTo(0,0);
                            }

                            var txtInput = inputs[j];
                            //var styleContentScrollCart = txtInput.currentStyle || window.getComputedStyle(txtInput);
                            //txtInput.style.width = parseInt(styleContentScrollCart.width) + 'px';

                            //Check if the class 'metadata-Highlighted' has been assigned to it. If so then remove the red box.
                            var classStringTemp = String (radioGrandParent.className);
                            var classStringTemp2 = classStringTemp.split(/\s/);
                            var classStringTemp3 = classStringTemp2.indexOf('metadata-Highlighted');

                            if(!(classStringTemp3 >=0))
                            {
                                //Draw a red box around the uncompleted divs
                                radioGrandParent.className += ' metadata-Highlighted';

                                if (radioGrandParent.id.indexOf('_') != -1)
                                {
                                    // determine which line item contains the incomplete element
                                    grandDivIDArray = radioGrandParent.id.split("_");

                                    if (grandDivIDArray[1] == '-1')
                                    {
                                        // order footer - if hidden, toggle open
                                        if(document.getElementById('contentFooter').style.display == "none")
                                        {
                                            toggleGeneric('footer', 'contentFooter', 'white');
                                        }
                                    }
                                    else
                                    {
                                        // line item
                                        var itemElement = "contentCustomise_" + grandDivIDArray[1];

                                        // if hidden, toggle open
                                        if(document.getElementById(itemElement).style.display == "none")
                                        {
                                            // trigger the click to display the line item details
                                            toggleGeneric(grandDivIDArray[1], itemElement, 'grey');
                                        }
                                    }
                                }

                                //If at least one input is checked then radioChecker = true
                                for(var k = 0; k < radioInputs.length; k++)
                                {
                                    radioInputs[k].onchange = function() {
                                        acceptDataEntry(false);
                                    };
                                }
                            }
                        }
                    }
                }

                /* get metadata values for all textareas on the page */
                for (var j = 0; j < textareas.length; j++)
                {
                    var txtInput = textareas[j];
                    var grandDiv = ((txtInput.parentNode).parentNode).parentNode;

                    //Get the current style of the input and the grandparent node
                    var styleContentScrollCart = txtInput.currentStyle || window.getComputedStyle(txtInput);
                    var grandStyle = grandDiv.currentStyle || window.getComputedStyle(grandDiv);

                    //Checks if the value of the input has been set, if so then unhighlight the element.
                    if ((textareas[j].className.indexOf('required') > -1) && (textareas[j].value != ''))
                    {
                        //Check if the class 'metadata-Highlighted' has been assigned to it. If so then remove the red box.
                        var classStringTemp = String (grandDiv.className);
                        var classStringTemp2 = classStringTemp.split(/\s/);
                        var classStringTemp3 = classStringTemp2.indexOf('metadata-Highlighted');

                        if(classStringTemp3 >=0)
                        {
                            var classContent = grandDiv.className;
                            grandDiv.className = classContent.replace('metadata-Highlighted', "").trim();
                        }
                    }
                    else if (textareas[j].className.indexOf('required') > -1)
                    {
                        valid = false;

                        if(pAlertOn)
                        {
                            alert("{/literal}{#str_ErrorValueRequired#}{literal}");
                            pAlertOn = false;
                            window.scrollTo(0,0);
                        }

                        //Check if the class 'metadata-Highlighted' has been assigned to it. If so then remove the red box.
                        var classStringTemp = String (grandDiv.className);
                        var classStringTemp2 = classStringTemp.split(/\s/);
                        var classStringTemp3 = classStringTemp2.indexOf('metadata-Highlighted');

                        if(!(classStringTemp3 >=0))
                        {
                            //Draw a red box around the uncompleted divs
                            grandDiv.className += ' metadata-Highlighted';

                            // determine which line item contains the incomplete element
                            grandDivIDArray = grandDiv.id.split("_");

                            if (grandDivIDArray[1] == '-1')
                            {
                                // order footer - if hidden, toggle open
                                if(document.getElementById('contentFooter').style.display == "none")
                                {
                                    toggleGeneric('footer', 'contentFooter', 'white');
                                }
                            }
                            else
                            {
                                // line item
                                var itemElement = "contentCustomise_" + grandDivIDArray[1];

                                // if hidden, toggle open
                                if(document.getElementById(itemElement).style.display == "none")
                                {
                                    // trigger the click to display the line item details
                                    toggleGeneric(grandDivIDArray[1], itemElement, 'grey');
                                }
                            }

                            if (txtInput.addEventListener)
                            {
                                txtInput.addEventListener('blur', function()
                                {
                                    acceptDataEntry(false);
                                });
                            }
                            else
                            {
                                txtInput.attachEvent('onblur', function()
                                {
                                    acceptDataEntry(false);
                                });
                            }
                        }

                        //Make the text box a little smaller
                        if(parseInt(grandStyle.width) < (parseInt(styleContentScrollCart.width)-20))
                        {
                            var mySize = (parseInt(grandStyle.width)-20)+'px';
                            txtInput.setAttribute("style","width:"+mySize);
                        }
                    }
                }


                /* get metadata values for all selects on the page */
                for (var j = 0; j < selects.length; j++)
                {
                    var txtInput = selects[j];
                    var grandDiv = ((txtInput.parentNode).parentNode).parentNode    ;
                    var styleContentScrollCart = txtInput.currentStyle || window.getComputedStyle(txtInput);

                    //Check to see if any option has been selected
                    if(selects[j].options[selects[j].selectedIndex].value.length >0)
                    {
                    //Check if the class 'metadata-Highlighted' has been assigned to it. If so then remove the red box.
                        var classStringTemp = String (grandDiv.className);
                        var classStringTemp2 = classStringTemp.split(/\s/);
                        var classStringTemp3 = classStringTemp2.indexOf('metadata-Highlighted');

                        if(classStringTemp3 >=0)
                        {
                            var classContent = grandDiv.className;
                            grandDiv.className = classContent.replace('metadata-Highlighted', "").trim();
                        }
                    }
                    else
                    {
                        valid = false;

                        if(pAlertOn)
                        {
                            alert("{/literal}{#str_ErrorValueRequired#}{literal}");
                            pAlertOn = false;
                            window.scrollTo(0,0);
                        }

                        //Check if the class 'metadata-Highlighted' has been assigned to it. If so then remove the red box.
                        var classStringTemp = String (grandDiv.className);
                        var classStringTemp2 = classStringTemp.split(/\s/);
                        var classStringTemp3 = classStringTemp2.indexOf('metadata-Highlighted');

                        if(!(classStringTemp3 >=0))
                        {
                            //Draw a red box around the uncompleted divs
                            grandDiv.className += ' metadata-Highlighted';

                            // determine which line item contains the incomplete element
                            grandDivIDArray = grandDiv.id.split("_");

                            if (grandDivIDArray[1] == '-1')
                            {
                                // order footer - if hidden, toggle open
                                if(document.getElementById('contentFooter').style.display == "none")
                                {
                                    toggleGeneric('footer', 'contentFooter', 'white');
                                }
                            }
                            else
                            {
                                // line item
                                var itemElement = "contentCustomise_" + grandDivIDArray[1];

                                // if hidden, toggle open
                                if(document.getElementById(itemElement).style.display == "none")
                                {
                                    // trigger the click to display the line item details
                                    toggleGeneric(grandDivIDArray[1], itemElement, 'grey');
                                }
                            }

                            txtInput.onchange = function() {
                                acceptDataEntry(false);
                            };
                        }
                    }
                }
            }
            
            return valid;
        }


        function orderButtonCompleteOrder()
        {
			var tsandcsCheckBox = document.getElementById('ordertermsandconditions');
			var paymentsVisible = (document.getElementById('paymenttableobj').style.display != 'none');

			// check if the terms and conditions option exists
			if (tsandcsCheckBox)
			{
				// has the terms and conditions option been checked?
				if (! tsandcsCheckBox.checked)
				{
                    // Mark the terms and conditions confirmation check box as required.
                    document.getElementById('ordertermsandconditionscontainer').className = 'metadata-Highlighted';

                    // Display Message asking for confirmation of reading terms and conditions.
                    alert("{/literal}{#str_ErrorAcceptTermsAndConditions#}{literal}");

					return false;
				}
			}

			// Process the appropriate action.
			if ((paymentsVisible) && (gRequestPaymentParamsRemotely))
			{
                var paymentMethod = '';
                var paymentMethodRadios = document.querySelectorAll("div#paymentMethodsList input[name='paymentmethods']");
                
                // IE returns multiple selector results as a NodeList rather than an array
                // so we need to call a new methid for each object.
                Array.prototype.forEach.call(paymentMethodRadios, function (radio){
                    if (radio.checked)
                    {
                        paymentMethod =  radio.value;
                    }
                });

                if (paymentMethod == "KLARNA")
                {
                    callKlarnaEndPoint();
                }
                else
                {
                    callEndPoint();
                }                
			}
			else
			{
				acceptDataEntry();
			}
        }

		function acceptTermsAndConditions()
		{
			var tsandcsCheckBox = document.getElementById('ordertermsandconditions');

			// check if the terms and conditions option exists
			if (tsandcsCheckBox)
			{
				// has the terms and conditions option been checked?
				if (tsandcsCheckBox.checked)
				{
                    // Remove the required field indicator.
                    document.getElementById('ordertermsandconditionscontainer').className = '';
				}
			}
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
			if (document.documentElement.style.overflow == 'hidden')
			{
				document.documentElement.style.overflow = '';
			}
			document.body.className = document.body.className.replace(' hideSelects', '');
			return false;
		}

        function previousOrderStage()
        {
    {/literal}
    {$metadatasubmit}
    {literal}
            document.submitform.paymentmethodcode.value = getPaymentMethodCode();
            document.submitform.paymentgatewaycode.value = getPaymentGatewayCode();
            document.submitform.fsaction.value = "Order.back";
            document.submitform.submit();
            return false;
        }

        function getPaymentMethodCodeRaw()
        {
            var paymentMethodCode = "";

            if (gPaymentMethodCode != "NONE")
            {
                var radioObj = document.orderform.paymentmethods;

                if (gPaymentMethodCode.length != 0 && radioObj)
                {
                    var radioLength = radioObj.length;

                    if (radioLength == undefined)
                    {
                        if (radioObj.checked)
                        {
                            paymentMethodCode = radioObj.value;
                        }
                    }
                    else
                    {
                        for (i = 0; i < radioLength; i++)
                        {
                            if (radioObj[i].checked)
                            {
                                paymentMethodCode = radioObj[i].value;
                                break;
                            }
                        }
                    }
                }
            }
            else
            {
                paymentMethodCode = gPaymentMethodCode;
            }
            return paymentMethodCode;
        }

        function getPaymentMethodAction()
        {
            var paymentMethodAction = "";
            var radioObj = document.orderform.paymentmethods;
            if (gPaymentMethodCode.length != 0)
            {
                var radioLength = radioObj.length;
                if (radioLength == undefined)
                {
                    if (radioObj.checked)
                    {
                        paymentMethodAction = radioObj.getAttribute("action");
                    }
                }
                else
                {
                    for (i = 0; i < radioLength; i++)
                    {
                        if (radioObj[i].checked)
                        {
                            paymentMethodAction = radioObj[i].getAttribute("action");
                            break;
                        }
                    }
                }
            }
            return paymentMethodAction;
        }

    {/literal}
    {/if}
        //]]>
    </script>
    </head>
    <!--[if IE 6]><body onload="initialize();" style="position: relative" class="ie6" id="shoppingCart" onresize="resizePopup();"><![endif]-->
    <!--[if gt IE 6]><!-->
    <body style="position: relative" id="shoppingCart">
    <!--<![endif]-->
        <!-- store locator code -->
{if $stage=='shipping'}
        <div id="shim">&nbsp;</div>
        <div id="storeLocator" class="section">
            <div class="dialogTop">
                <h2 class="title-bar">
                    {#str_LabelSelectStore#}
                </h2>
            </div>
            <div id="storeLocatorForm" class="contentStoreLocator"></div>
        </div>
        <div id="storeInfo" class="section"></div>
{/if}

{if $stage=='payment'}
        <div id="dialogOuter" class="dialogOuter"></div>
        <div id="shim">&nbsp;</div>
        <div id="ordersTermsAndCondtions" class="section">
            <div class="dialogTop">
                <h2 class="title-bar">
                    {#str_TitleTermsAndConditions#}
                </h2>
            </div>
            <div class="contentTermsAndConditions">
                <div id="termsandconditionswindow" class="contentFormTermsAndCondition"></div>
            </div>
            <div class="buttonBottomInside">
                <div class="btnRight">
                     <div id="closetermsandconditionswindow" class="contentBtn">
                         <div class="btn-green-left" ></div>
                         <div class="btn-accept-right"></div>
                     </div>
                 </div>
                 <div class="clear"></div>
             </div>
        </div>

		<div id="loadingBox" class="section maw_dialog">
			<div class="dialogTop">
				<h2 id="loadingTitle" class="title-bar"></h2>
			</div>
			<div class="content">
				<div class="loadingMessage">
					<img src="{$webroot}/images/loading_shoppingcart_v2.gif" class="loading-icon" alt="{#str_MessageLoading#}" />
				</div>
			</div>
		</div>
		<div id="shimLoading">&nbsp;</div>
{/if}
<!--  end of store locator -->

<!-- component change box -->
{if $stage=='qty'}
        <div id="shim">&nbsp;</div>
        <div id="componentChangeBox" class="section"></div>
{/if}
        <!--  end of component change box -->
        <!-- START OF SHOPPING CART-->
        <div id="outerPage" class="order-section outer-page{if $sidebarleft != ''} fullsize-outer-page{/if}">
            <div id="header" class="headerScroll">
                <div class="headerinside">
                    {include file="$header"}
                </div>
            </div>
            <div class="contentNavigation {if $sidebarleft != ''} fullsize-navigation{/if}">
	{if $hasCompanions == 'YES'}
                <div class="contentNavigationImageCompanion">
	{else}
                <div class="contentNavigationImage">
	{/if}
{if $stage=='companionselection'}
                    <div class="navigationLongBloc">
                        <div class="navigationActiveInactiveRight"></div>
                        <div class="navigationLineInactive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationLongBloc">
                        <div class="navigationInactiveMiddle"></div>
                        <div class="navigationLineInactive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationLongBloc">
                        <div class="navigationInactiveMiddle"></div>
                        <div class="navigationLineInactive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationLongBloc">
                        <div class="navigationInactiveMiddle"></div>
                        <div class="navigationLineInactive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationBloc">
                        <div class="navigationInactiveLeft"></div>
                        <div class="clear"></div>
                    </div>
{elseif $stage=='qty'}
                    <div class="navigationLongBloc">
	{if $hasCompanions == 'YES'}
                        <div class="navigationActiveRight"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationLongBloc">
                        <div class="navigationActiveLeftInactiveRight"></div>
	{else}
                        <div class="navigationActiveInactiveRight"></div>
	{/if}
                        <div class="navigationLineInactive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationLongBloc">
                        <div class="navigationInactiveMiddle"></div>
                        <div class="navigationLineInactive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationLongBloc">
                        <div class="navigationInactiveMiddle"></div>
                        <div class="navigationLineInactive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationBloc">
                        <div class="navigationInactiveLeft"></div>
                        <div class="clear"></div>
                    </div>
{elseif $stage=='shipping'}
                    <div class="navigationLongBloc">
                        <div class="navigationActiveRight"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
	{if $hasCompanions == 'YES'}
                    <div class="navigationLongBloc">
                        <div class="navigationActiveMiddle"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
	{/if}
                    <div class="navigationLongBloc">
                        <div class="navigationActiveLeftInactiveRight"></div>
                        <div class="navigationLineInactive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationLongBloc">
                        <div class="navigationInactiveMiddle"></div>
                        <div class="navigationLineInactive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationBloc">
                        <div class="navigationInactiveLeft"></div>
                        <div class="clear"></div>
                    </div>
{elseif $stage=='payment'}
                    <div class="navigationLongBloc">
                        <div class="navigationActiveRight"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
	{if $hasCompanions == 'YES'}
                    <div class="navigationLongBloc">
                        <div class="navigationActiveMiddle"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
	{/if}
                    <div class="navigationLongBloc">
                        <div class="navigationActiveMiddle"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationLongBloc">
                        <div class="navigationActiveLeftInactiveRight"></div>
                        <div class="navigationLineInactive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationBloc">
                        <div class="navigationInactiveLeft"></div>
                        <div class="clear"></div>
                    </div>
{/if}
                    <div class="clear"></div>
                </div>
	{if $hasCompanions == 'YES'}
                <div class="contentNavigationTextCompanion">
                    <div class="labelNavigation">{#str_LabelNavigationCompanionSelection#}</div>
	{else}
                <div class="contentNavigationText">
	{/if}
                    <div class="labelNavigation">{#str_LabelCartSummary#}</div>
                    <div class="labelNavigation">{#str_LabelNavigationShippingBilling#}</div>
                    <div class="labelNavigation">{#str_LabelNavigationPayment#}</div>
                    <div class="labelNavigation">{#str_LabelNavigationConfirmation#}</div>
                    <div class="clear"></div>
                </div>
            </div>
            <div id="contentScroll" class="contentScrollCart">
{*
{if $sidebarleft != ''}
            {include file="$sidebarleft"}
{/if}
  *}              <div id="contentHolder">
                    <form id="orderform" name="orderform" method="post" action="#" data-decorator="fnPreventFormSubmit" data-trigger="submit">
                        <div {if ($stage !='qty') && ($stage != 'payment')}id="pageFooterHolder"{/if}>
                            <div id="page" class="section backgroundGrey">
{if $metadatalayout!='' && $stage !='payment'}
                                <h2 class="title-bar {if ($stage=='payment')}marginBloc{/if}">
                                    {#str_LabelAdditionalInformation#}
                                </h2>
                                <div class="content contentBloc">
                                    <div id="metadatatableobj" class="metadataadditional">
                                        {$metadatalayout}
                                    </div>
                                </div>
{/if}
{if ($stage == 'companionselection')}
                                <div id="orderContent">
									<div id="companionOptionsHeader">
										<div id="companionQuestionText">
											{$companionheadertitle}
										</div>
										<div class="btnRight">
											<div class="contentBtn" id="topordercontinuebutton">
												<div class="btn-green-left"></div>
												<div class="btn-green-middle">{#str_ButtonContinue#}</div>
												<div class="btn-green-arrow-right"></div>
											</div>
										</div>
									</div>

									<!-- companion book selection start -->
    {foreach from=$orderitems item=orderitem name=orderItemsLoop key=itemIndex}
	    {if $orderitem.parentorderitemid == 0 && $orderitem.itemhascompanions}
	    	{include file="order/companions.tpl" orderline=$orderitem companions=$companionOptions[$itemIndex] parentLineID=$itemIndex}
	    {/if}
	{/foreach}
									<!-- companion book selection end -->
                                </div>
{elseif ($stage=='qty') || ($stage=='payment')}
								<div id="orderContent">
                               <h2 class="title-bar">
	{if ($stage=='qty')}
                                    {#str_LabelCartSummary#}
	{else}
                                    {#str_LabelOrderSummary#}
	{/if}
                                </h2>

    <!-- order lines start -->
	{* Track which line item is being displayed. *}
	{assign var="lineCounter" value=1}

	{* Check if the line item, including any companion items, has been fully displayed. *}
	{* Each item container will contain a primary item and any linked companion items. *}
	{assign var="closeLineItemContainer" value=false}

	{* Make sure the final item container is closed, if required. *}
	{assign var="closeFinalContainer" value=false}

	{* Loop around line items *}
    {foreach from=$orderitems item=orderitem name=orderItemsLoop}

		{* If the parentorderitemid == 0, the line is a primary item. *}
		{if $orderitem.parentorderitemid == 0}
			{* If the previous item was a companion item, the previous item container needs to be closed. *}
			{assign var="isCompanion" value=false}
			{if $lineCounter == 1}
				{* This is the first time around the loop, no item containers are open, so do not close any. *}
				{assign var="closeLineItemContainer" value=false}
			{else}
				{* The previous item container needs to be closed, before the new one is opened. *}
				{assign var="closeLineItemContainer" value=true}
			{/if}
		{else}
			{* If the parentorderitemid != 0, the line is a companion item. *}
			<!-- Companion item. -->
			{assign var="isCompanion" value=true}

			{* The companion item needs to be included in the current item container, do not close the current item container. *}
			{assign var="closeLineItemContainer" value=false}
		{/if}

		{if $closeLineItemContainer == true}
			{* Close any open item containers before opening a new container. *}
			</div> <!-- End of line item. -->
		{/if}

		{if $isCompanion == false}
			{* Open a new item container before adding any new items. *}
			<div id='orderlinecontainer_{$lineCounter}' class='orderlinecontainer'> <!-- New line item. -->

			{* Make sure the final item container is closed after all items are displayed. *}
			{assign var="closeFinalContainer" value=true}
		{/if}

		{* Include the item information. *}
		{include file="$orderline" orderline=$orderitem}

		{* Move onto the next line item. *}
		{assign var="lineCounter" value=$lineCounter+1}
    {/foreach}

	{* Close the final item container, if required. *}
	{if $closeFinalContainer == true}
			</div>
	{/if}
    <!-- order lines end -->
                                    <!-- order footer start -->
                                    {include file="$orderfooter"}
                                    <!-- order footer end -->
                                </div>
{/if}

{if $stage=='payment'}
    {if $metadatalayout!=''}
                                <div class="contentPaymentMetaDataBloc">
                                    <h2 class="title-bar">
                                        {#str_LabelAdditionalInformation#}
                                    </h2>
                                    <div class="content contentBloc" id="orderContent">
                                        <div id="metadatatableobj" class="metadataadditional">
                                            {$metadatalayout}
                                        </div>
                                    </div>
                                </div>
    {/if}
                                <div class="contentPaymentBloc">
                                <h2 class="title-bar">
                                    {$ordertitle}
                                </h2>
                                <div class="content contentPayment">
                                    {* shipping *}
                                    <div class="backgroundShipping">
                                        <div class="contentAddress">
                                            <div class="contentAddressHeader">
                                                <div class="titleAddressLeft">
                                                    {$shippingStoreAddressLabel}
                                                </div>
                                                <div class="titleAddressRight">
                                                    {#str_LabelBillingAddress#}
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="contentAddressBody">
                                                <div class="shippingSummary">
                                                    <div class="shippingPadding">
                                                        {$shippingaddress}
                                                    </div>
                                                </div>
                                                <div class="billingSummary">
                                                    <div class="shippingPadding">
                                                        {$billingaddress}
                                                    </div>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                     </div>
                                    <div class="line-total">

                                        <div class="line-sub-total">
                                            <span class="total-heading">
                                                {#str_LabelOrderShipping#} ({$shippingmethodname}):
                                            </span>
                                            <span class="order-line-price">
                                                {$ordertotalshipping}
                                            </span>
                                            <div class="clear"></div>
                                        </div>

<!-- VOUCHER -->
{if ($vouchersection=='SHIPPING')||(($vouchersection=='TOTAL')&&($differenttaxrates==true)&&(!$specialvouchertype))}

    <!-- SHIPPING VOUCHER  -->
    {if ($vouchersection=='SHIPPING')}
        {if ($shippingdiscountvalueraw > 0)}

                                        <div class="line-sub-total-nopadding">
                                            <span class="total-heading">
                                                {$shippingdiscountname}:
                                            </span>
                                            <span class="order-line-price">
                                                {$shippingdiscountvalue}
                                            </span>
                                            <div class="clear"></div>
                                        </div>
        {/if}

        <!-- SHOW SHIPPING TAX  -->
        {if ($showshippingtax)}

            <!-- SHOW PRICES WITH TAX  -->
            {if ($showpriceswithtax)}
                {if ($shippingdiscountvalueraw > 0)}
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    {#str_LabelOrderShippingTotal#}:
                                                </span>
                                                <span class="order-line-price">
                                                    {$shippingdiscountedvalue}
                                                </span>
                                                <div class="clear"></div>
                                            </div>
                {/if}
                                            <div class="line-sub-total-small-bottom">
                                                {$includesshippingtaxtext}
                                            </div>
            {else}
                {if ($shippingdiscountvalueraw > 0)}
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    {#str_LabelSubTotal#}:
                                                </span>
                                                <span class="order-line-price">
                                                    {$shippingdiscountedvalue}
                                                </span>
                                                <div class="clear"></div>
                                            </div>
                {/if}
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    {$shippingtaxname} ({$shippingtaxrate}%):
                                                </span>
                                                <span class="order-line-price">
                                                    {$shippingtaxtotal}
                                                </span>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    {#str_LabelOrderShippingTotal#}:
                                                </span>
                                                <span class="order-line-price">
                                                    {$shippingtotal}
                                                </span>
                                                <div class="clear"></div>
                                            </div>
            {/if}

        {else}
                {if ($shippingdiscountvalueraw > 0)}
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    {#str_LabelOrderShippingTotal#}:
                                                </span>
                                                <span class="order-line-price">
                                                    {$shippingtotal}
                                                </span>
                                                <div class="clear"></div>
                                            </div>
                {/if}
        {/if}

    {/if}

    <!-- TOTAL VOUCHER  -->
    {if (($vouchersection == 'TOTAL') && ($differenttaxrates == true) && (! $specialvouchertype))}

        {if ($shippingdiscountvalueraw > 0)}
                                        <div class="line-sub-total">
                                            <span class="total-heading">
                                                {$shippingdiscountname}:
                                            </span>
                                            <span class="order-line-price">
                                                {$shippingdiscountvalue}
                                            </span>
                                            <div class="clear"></div>
                                        </div>
        {/if}

        <!-- SHOW SHIPPING TAX  -->
        {if ($showshippingtax)}

            <!-- SHOW PRICES WITH TAX  -->
            {if ($showpriceswithtax)}
                {if ($shippingdiscountvalueraw > 0)}
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    {#str_LabelOrderShippingTotal#}:
                                                </span>
                                                <span class="order-line-price">
                                                    {$shippingdiscountedvalue}
                                                </span>
                                                <div class="clear"></div>
                                            </div>
                {/if}
                                            <div class="line-sub-total-small-bottom">
                                                {$includesshippingtaxtext}
                                            </div>
            {else}
                {if ($shippingdiscountvalueraw > 0)}
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    {#str_LabelSubTotal#}:
                                                </span>
                                                <span class="order-line-price">
                                                    {$shippingdiscountedvalue}
                                                </span>
                                                <div class="clear"></div>
                                            </div>
                {/if}
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    {$shippingtaxname} ({$shippingtaxrate}%):
                                                </span>
                                                <span class="order-line-price">
                                                    {$shippingtaxtotal}
                                                </span>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    {#str_LabelOrderShippingTotal#}:
                                                </span>
                                                <span class="order-line-price">
                                                    {$shippingtotal}
                                                </span>
                                                <div class="clear"></div>
                                            </div>
            {/if}

        {else}
                {if ($shippingdiscountvalueraw > 0)}
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    {#str_LabelOrderShippingTotal#}:
                                                </span>
                                                <span class="order-line-price">
                                                    {$shippingtotal}
                                                </span>
                                                <div class="clear"></div>
                                            </div>
                {/if}
        {/if}
    {/if}

{else}

    {if (($vouchersection=='TOTAL') && ($differenttaxrates==true) && ($specialvouchertype)) || ($applyVoucherAsLineDiscount)}
        {if ($shippingdiscountvalueraw > 0)}
                                        <div class="line-sub-total">
                                            <span class="total-heading">
                                                {$shippingdiscountname}:
                                            </span>
                                            <span class="order-line-price">
                                                {$shippingdiscountvalue}
                                            </span>
                                            <div class="clear"></div>
                                        </div>
                    {if ($showpriceswithtax)}

                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    {#str_LabelOrderShippingTotal#}:
                                                </span>
                                                <span class="order-line-price">
                                                    {$shippingdiscountedvalue}
                                                </span>
                                                <div class="clear"></div>
                                            </div>
                    {else}
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    {#str_LabelSubTotal#}:
                                                </span>
                                                <span class="order-line-price">
                                                    {$shippingdiscountedvalue}
                                                </span>
                                                <div class="clear"></div>
                                            </div>
                    {/if}

        {/if}
        <!-- SHOW SHIPPING TAX  -->
        {if ($showshippingtax)}

            <!-- SHOW PRICES WITH TAX  -->
            {if ($showpriceswithtax)}
                                            <div class="line-sub-total-small-bottom">
                                                {$includesshippingtaxtext}
                                            </div>
            {else}
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    {$shippingtaxname} ({$shippingtaxrate}%):
                                                </span>
                                                <span class="order-line-price">
                                                    {$shippingtaxtotal}
                                                </span>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    {#str_LabelOrderShippingTotal#}:
                                                </span>
                                                <span class="order-line-price">
                                                    {$shippingtotal}
                                                </span>
                                                <div class="clear"></div>
                                            </div>
            {/if}

        {/if}
    {else}

        <!-- SHOW SHIPPING TAX  -->
        {if ($showshippingtax)}

            <!-- SHOW PRICES WITH TAX  -->
            {if ($showpriceswithtax)}
                                            <div class="line-sub-total-small-bottom">
                                                {$includesshippingtaxtext}
                                            </div>
            {else}
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    {$shippingtaxname} ({$shippingtaxrate}%):
                                                </span>
                                                <span class="order-line-price">
                                                    {$shippingtaxtotal}
                                                </span>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    {#str_LabelOrderShippingTotal#}:
                                                </span>
                                                <span class="order-line-price">
                                                    {$shippingtotal}
                                                </span>
                                                <div class="clear"></div>
                                            </div>
            {/if}

        {/if}
    {/if}

{/if}

                                    </div>
                                    <div class="clear"></div>
    {* end shipping *}
    {*payment details*}
                                    <h2 class="title-bar-inside">
                                        {#str_LabelTotalTitle#}
                                    </h2>
    {*voucher*}
    {if $showvouchers == true}
                                    <div class="orderSummaryVoucher" id="ordertotalsummary">
                                        <div class="contentvoucher">
                                            {#str_LabelEnterOrderVoucher#}<br /><br />
        {if $voucherstatus != ''}
                                            <b>{$voucherstatus}</b><br /><br />
        {/if}

        {if ($defaultdiscountactive == false) && ($vouchercode != '')}
                                            <input type="text" id="vouchercode" name="vouchercode" placeholder="{#str_LabelVoucherCode#}" class="voucherinput" value="{$vouchercode}" readonly="readonly" />
        {else}
                                            <input type="text" id="vouchercode" name="vouchercode" placeholder="{#str_LabelVoucherCode#}" class="voucherinput" value="" />
        {/if}                                         

        {if $vouchercode == '' || ($vouchercode !='' && $defaultdiscountactive==true)}
                                            <div class="contentBtn" id="setvoucher">
                                                <div class="btn-white-left" ></div>
                                                <div class="btn-white-middle">{#str_LabelRedeem#}</div>
                                                <div class="btn-white-right"></div>
                                            </div>
                                            <div class="clear"></div>
        {/if}
        {if $vouchercode != '' && $defaultdiscountactive==false}
                                            <div class="contentBtn" id="removevoucher">
                                                <div class="btn-white-left" ></div>
                                                <div class="btn-white-middle">{#str_LabelRemove#}</div>
                                                <div class="btn-white-right"></div>
                                            </div>
                                            <div class="clear"></div>
        {/if}
                                        </div>
                                    </div>
    {/if}
    {* end voucher*}
    {if $showgiftcardsbalance == true}
                                    <div class="{if $showvouchers == true}orderSummaryGift{else}orderSummaryGiftNoDot{/if}">
                                        <div class="contentvoucher">
                                            {#str_LabelEnterOrderGiftCard#}<br /><br />
        {if $giftcardstatus != ''}
                                            <b>{$giftcardstatus}</b><br /><br />
        {/if}
                                            <div class="clear"></div>
                                            <input type="text" id="giftcardcode" name="giftcardcode" placeholder="{#str_LabelGiftCardCode#}" value="" class="voucherinput" />
                                            <div class="contentBtn" id="setgiftcard">
                                                <div class="btn-white-left" ></div>
                                                <div class="btn-white-middle">{#str_LabelRedeem#}</div>
                                                <div class="btn-white-right"></div>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
    {/if}
                                    <div class="{if $showgiftcardsbalance == true || $showvouchers == true}line-total{else}line-total-no-dot{/if}">

    {if ((($vouchersection=='TOTAL')&&($differenttaxrates==false)) || (($vouchersection=='TOTAL')&&($differenttaxrates)&&($specialvouchertype)))}
        {if (($vouchersection=='TOTAL')&&($differenttaxrates==false) && ($applyVoucherAsLineDiscount == false))}
        {* order before discount total row *}
                                        <div class="line-sub-total-small">
                                            <span class="total-heading">
                                                {#str_LabelOrderSubTotal#}:
                                            </span>
                                            <span class="order-line-price-small">
                                                {$orderbeforediscounttotalvalue}
                                            </span>
                                            <div class="clear"></div>
                                        </div>
        {* order total discount row *}
                                        <div class="line-sub-total-small">
                                            <span class="total-heading">
                                                {$orderaftertotaldiscountname}:
                                            </span>
                                            <span class="order-line-price-small">
                                                {$ordertotaldiscountvalue}
                                            </span>
                                            <div class="clear"></div>
                                        </div>
        {else}
            {if (($vouchersection=='TOTAL')&&($differenttaxrates==false) && ($applyVoucherAsLineDiscount == true))}
                                        <div class="line-sub-total-small">
                                            <span class="total-heading">
                                                {#str_LabelOrderSubTotal#}:
                                            </span>
                                            <span class="order-line-price-small">
                                                {$ordersubtotal}
                                            </span>
                                            <div class="clear"></div>
                                        </div>
            {/if}
        {/if}
    {else}
        {if ($differenttaxrates==false)&&($showpriceswithtax==false)&&(($hastotaltax==true)||($showzerotax==true))}
    {* order subtotal row *}
                                        <div class="line-sub-total-small">
                                            <span class="total-heading">
                                                {#str_LabelOrderSubTotal#}:
                                            </span>
                                            <span class="order-line-price-small">
                                                {$ordersubtotal}
                                            </span>
                                            <div class="clear"></div>
                                        </div>
        {/if}
    {/if}
    {if ($differenttaxrates==false)&&($showpriceswithtax==false)&&(($hastotaltax==true)||($showzerotax==true))}
    {* order tax row *}
                                        <div class="line-sub-total-small">
                                            <span class="total-heading">
                                                {$itemtaxname} ({$itemtaxrate}%):
                                            </span>
                                            <span class="order-line-price-small">
                                                {$ordertotaltax}
                                            </span>
                                            <div class="clear"></div>
                                        </div>
    {/if}
    {* order total rows *}
    {if ($ordergiftcardtotal > 0)}
                                        <div class="line-sub-total-small">
                                            <span class="total-heading">
                                                {#str_LabelOrderTotal#}:
                                            </span>
                                            <span class="order-line-price-small">
                                                {$ordertotal}
                                            </span>
                                            <div class="clear"></div>
                                        </div>
        {* order tax row *}

        {if ((($showalwaystaxtotal == true) || ($showtaxbreakdown == true) || ($showzerotax == true)) && ($includestaxtotaltext != ''))}
                                            <div {if ($ordergiftcardtotal == 0 || $disabled_giftcard == 'disabled') } style="display:none" {/if} class="line-sub-total-small" id="includetaxtextwithgiftcard">
                                                {$includestaxtotaltext}
                                            </div>
        {/if}
                                        <div id="giftcard" class="line-sub-total-small gift-card-box-button {$disabled_giftcard}">
                                            <span class="total-heading">
                                                <span id="giftbutton" title="{$tooltipGiftcardButton}" class="button-voucher class_gift_{$add_delete_giftcard}"></span>
                                                {#str_LabelGiftCard#}:
                                            </span>
                                            <span class="order-line-price-small" id="giftcardamount">
                                                {$ordergiftcardtotalvalue}
                                            </span>
                                            <div class="clear"></div>
                                        </div>
    {/if}
    {* order total row *}
                                        <div class="line-sub-total">
                                            <span class="total-heading">
                                                {#str_LabelAmountToPay#}:
                                            </span>
                                            <span class="order-line-price" id="ordertotaltopayvalue">
                                                {$ordertotaltopayvalue}
                                            </span>
                                            <div class="clear"></div>
                                        </div>


        {if (($showalwaystaxtotal == true) || ($showtaxbreakdown == true) || ($showzerotax == true))}
											<div {if $ordergiftcardtotal > 0 && $disabled_giftcard != 'disabled'} style="display:none" {/if} class="line-sub-total-small-bottom" id="includetaxtextwithoutgiftcard">
												{$includestaxtotaltext}
											</div>
		{/if}


                                    </div>
    {*end payment details*}
    {*payment methods*}
                                    <div {if $hidepayments}style="display:none"{/if} id="paymenttableobj">
                                        <h2 class="title-bar-inside">
                                            {#str_LabelPaymentMethod#}
                                        </h2>
                                        <div id="paymentMethodsList">
                                            {$paymentmethodslist}
                                        </div>
                                    </div>
    {*end payment methods*}
                                {if $stage == 'payment' && $showtermsandconditions == 1}
									<h2 class="title-bar-inside">{#str_TitleTermsAndConditions#}</h2>
									<div id="ordertermsandconditionscontainer">
										<input type="checkbox" name="ordertermsandconditions" id="ordertermsandconditions">
                                        <label for="ordertermsandconditions">{#str_LabelTermsAndConditionsAgreement#} <a id="ordertermsandconditionslink" href="#" class="termsAndConditionsLink">{#str_TitleTermsAndConditions#}</a></label>
									</div>
								{/if}
                                </div>
    </div>
{/if}

{if $stage=='shipping'}
                                <h2 class="title-bar {if ($stage=='payment')}marginBloc{/if}">
                                    {$ordertitle}
                                </h2>
                                <div class="content contentBloc">
                                    <div id="shippingtableobj">
                                        <div id="addressHolder">
                                            <div class="contentHeaderShipping">
                                                <h2 id="labelShippingStoreAddress" class="shippingHeader">
                                                    {$initialShippingStoreAddressLabel}
                                                </h2>
                                                <h2 class="shippingHeader">
                                                    {#str_LabelBillingAddress#}
                                                </h2>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="contentShipping">
                                                <div id="shippingAddress" class="shippingAddress{if $sidebarleft != ''} fullsize-outer-page{/if}">
                                                    <div id="shippingStoreAddress" class="shippingPadding">
                                                        {if (($collectFromStoreCode != '') || ($collectFromStore!=1))}
                                                            {$initialShippingStoreAddress}
                                                        {/if}
                                                    </div>
                                                </div>
                                                <div id="billingAddress" class="billingAddress{if $sidebarleft != ''} fullsize-outer-page{/if}">
                                                    <div class="shippingPadding">
                                                        {$billingaddress}
                                                    </div>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="contentShippingBottom">

    {if (($canmodifyshipping==true)||($canmodifybilling==true)||($optionCFS))}
        {if $canmodifyshipping==true}
                                                <div id="changeShippingDiv" class="alignBottom" {if $collectFromStore==1}style="display:none"{/if}>
                                                    <div class="contentBtn" id="changeshipping">
                                                        <div class="btn-white-left" ></div>
                                                        <div class="btn-white-middle">{#str_ButtonChange#}</div>
                                                        <div class="btn-white-right"></div>
                                                    </div>
                                                </div>
        {/if}
        {if $storeisfixed==0}
                                                <div id="selectStoreDiv" class="alignBottom" {if $collectFromStore==0}style="display:none"{/if}>
                                                    <div class="contentBtn" id="selectStoreButton" data-decorator="fnSelectStore" data-method="shippingmethod{$shippingratecode}">
                                                        <div class="btn-white-left" ></div>
                                                        <div class="btn-white-middle">{#str_ButtonSelectStore#}</div>
                                                        <div class="btn-white-right"></div>
                                                    </div>

                                                    <div class="contentBtn" id="changeCollectionDetailsButton">
                                                        <div class="btn-disabled-left" ></div>
                                                        <div class="btn-disabled-middle">{#str_ButtonEditCollectionDetails#}</div>
                                                        <div class="btn-disabled-right"></div>
                                                    </div>
                                                </div>                                               
        {/if}
    {/if}
                                                <div class="alignBottomRight">
                                                    <span id="sameasshippingaddressobj" {if (($canmodifyshipping==false)||($canmodifybilling==false))}style="display:none"{/if}>
                                                        <input type="checkbox" id="sameasshippingaddress" name="sameasshippingaddress" {if ($sameshippingandbillingaddress==true)}checked="checked"{/if} {if ($collectFromStore==1)}disabled="disabled"{/if} />
                                                        <label for="sameasshippingaddress">
                                                            {#str_LabelSameAsShippingAddress#}
                                                        </label>
                                                    </span>
                                                    {if (($sameshippingandbillingaddress==true) && ($canmodifyshipping==true))}

                                                    <div class="contentBtn" id="changebilling" disabled="disabled" {if ($canmodifybilling==false)}style="display:none"{/if}>
                                                        <div id="changeBillingBtnLeft" class="btn-disabled-left" ></div>
                                                        <div id="changeBillingBtnMiddle" class="btn-disabled-middle">{#str_ButtonChange#}</div>
                                                        <div id="changeBillingBtnRight" class="btn-disabled-right"></div>
                                                    </div>

                                                    {else}

                                                    <div class="contentBtn" id="changebilling" {if ($canmodifybilling==false)}style="display:none"{/if}>
                                                        <div id="changeBillingBtnLeft" class="btn-white-left" ></div>
                                                        <div id="changeBillingBtnMiddle" class="btn-white-middle">{#str_ButtonChange#}</div>
                                                        <div id="changeBillingBtnRight" class="btn-white-right"></div>
                                                    </div>

                                                    {/if}

                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                        <div id="shippingMethods">
                                            <h2 class="shippingMethodHeader">
                                                <span class="shippingTextHeader">
                                                    {#str_LabelShippingMethod#}
                                                </span>
                                                <span class="shippingCurrency">
                                                    {#str_LabelOrderShippingCost#} ({$currencyname})
                                                </span>
                                            </h2>
                                            <ul id="shippingMethodsList">
                                                {$shippingmethodslist}
                                            </ul>
                                        </div>
                                        <div class="line-total">
                                            <div class="line-sub-total">
                                                <span class="total-heading">{#str_LabelItemTotalShipping#}:</span>
                                                <span class="order-line-price" id="itemsubtotalwithshipping">{$ordertotal}</span>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
{/if}
                            </div> <!-- content -->
                         </div><!-- pageFooterHolder -->
                    </form>
                    <div class="clear"></div>
                    <div class="buttonBottom">
                        <div id="cancelOrderButton" class="contentBtn">
                            <div class="btn-red-cross-left" ></div>
                            <div class="btn-red-middle">{#str_ButtonCancel#}</div>
                            <div class="btn-red-right"></div>
                        </div>
                        <div class="btnRight">
{if not (($stage == 'companionselection') || ($stage == 'qty' && $hasCompanions == 'NO'))}
                            <div class="contentBtn" id="backButton">
                                <div class="btn-blue-arrow-left" ></div>
                                <div class="btn-blue-middle">{#str_ButtonBack#}</div>
                                <div class="btn-blue-right"></div>
                            </div>
{/if}

                            {if $stage=='payment'}
                                <div class="contentBtn" id="ordercontinuebutton">
                                    <div id="btn-confirm-left" class="btn-green-left"></div>
                                    <div id="btn-confirm-middle"class="btn-green-middle">{#str_ButtonConfirmOrder#}</div>
                                    <div id="btn-confirm-right" class="btn-accept-right"></div>
                                </div>
                            {else}
                                <div class="contentBtn" id="ordercontinuebutton">
                                    <div class="btn-green-left" ></div>
                                    <div class="btn-green-middle">{#str_ButtonContinue#}</div>
                                    <div class="btn-green-arrow-right"></div>
                                </div>
                            {/if}
                        </div>
                        <div class="clear"></div>
                    </div>
                </div> <!--  contentHolder -->
                <div class="clear"></div>
                <div id="side-outer-panel" class="side-outer-panel cart-side-outer-panel-scroll">
                    <div class="side-panel section blocfixed">
                        <h2 class="title-bar title-bar-panel">
                            <div class="textIcon">{#str_LabelCartSummary#}</div>
                            <img src="{$webroot}/images/icons/basket_summary_icon.png" alt="" />
                            <div class="clear"></div>
                        </h2>
                        <div class="content contentBloc panelQty" id="ordersummarypanel">
                            <div class="contentDotted">
                                <div class="titleDetailPanel">
                                    {#str_LabelOrderItemListItemTotal#}:
                                </div>
                                <div class="sidebaraccount_gap priceBold">
                                    {$orderitemstotalsell}
                                </div>
                                <div class="contentDottedImage"></div>
                            </div>
                             <div class="contentDotted">
                                <div class="titleDetailPanel">
                                    {#str_LabelOrderShippingCost#}:
                                </div>
                                <div class="sidebaraccount_gap priceBold">
                                    {$ordershippingcost}
                                </div>
                                <div class="contentDottedImage"></div>
                            </div>
                            <div class="content">
                            {if $stage == 'payment'}
                                <div class="titleDetailPanelBold">
                                    {#str_LabelAmountToPay#}:
                                </div>
                                <div class="order-line-price-panel sidebaraccount_gap" id="ordertotaltopayvalueside">
                                    {$ordertotaltopayvalue}
                                </div>
                            {else}
                                <div class="titleDetailPanelBold">
                                    {#str_LabelOrderTotal#}:
                                </div>
                                <div class="order-line-price-panel sidebaraccount_gap" id="ordertotaltopayvalueside">
                                    {$ordertotal}
                                </div>
                            {/if}
                            </div>
                        </div>
                    </div>
                    {if $showgiftcardsbalance == true}
                        <div class="side-panel section blocfixed">
                            <h2 class="title-bar title-bar-panel">
                                <div class="textIcon">{#str_SectionTitleGiftCards#}</div>
                                <img src="{$webroot}/images/icons/gift_card_icon.png" alt="" />
                                <div class="clear"></div>
                            </h2>
							<div class="content">
                                <div class="titleDetailPanel">
                                    {#str_LabelGiftCardRemaining#}:
                                </div>
                                <div class="sidebaraccount_gap priceBold" id="giftcardbalanceside">
                                    {$giftcardbalance}
                                </div>
                            </div>
                        </div>
                    {/if}
                    <div class="contactPanel">
                    {include file="$sidebarcontactdetails"}
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div> <!-- outer-page -->
        <div style="display:none">
            <form id="submitform" name="submitform" method="post" accept-charset="utf-8" action="#">
                <input type="hidden" id="ref" name="ref" value="{$ref}" />
                <input type="hidden" id="fsaction" name="fsaction" value="" />
                <input type="hidden" id="ssotoken" name="ssotoken" value="{$ssotoken}" />
                <input type="hidden" name="itemqty" value="{$itemqty}"/>
                <input type="hidden" name="sameshippingandbillingaddress" value=""/>
                <input type="hidden" name="shippingratecode" value=""/>
                <input type="hidden" name="shippingcfscontact" value=""/>
                <input type="hidden" name="paymentmethodcode" value=""/>
                <input type="hidden" name="paymentgatewaycode" value=""/>
                <input type="hidden" name="requiresdelivery" value=""/>
                <input type="hidden" name="vouchercode" value=""/>
                <input type="hidden" name="previousstage" value="{$previousstage}"/>
                <input type="hidden" name="stage" value="{$stage}"/>
                <input type="hidden" name="section" value=""/>
                <input type="hidden" name="orderlineid" value=""/>
                <input type="hidden" name="giftcardcode" value=""/>
                <input type="hidden" name="showgiftcardmessage" value="0"/>
				<input type="hidden" name="ispaypalplus" value="0"/>
                <input type="hidden" name="ispagsegurolb" value="0"/>
                <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
                {$metadataform}
            </form>
        </div>

{foreach from=$paymentgatewayjavascriptarray item=gatewayscriptarray}
    <script type="text/javascript" src="{$gatewayscriptarray.scripturl}" {$nonce}></script>

    {if $gatewayscriptarray.form != ''}

        <script type="text/javascript" {$nonce}>
            {$gatewayscriptarray.form}
        </script>

    {/if}

{/foreach}

    </body>
</html>