<?php
/* Smarty version 4.5.3, created on 2026-03-14 01:34:24
  from 'C:\TAOPIX\MediaAlbumWeb\Branding\\ubbpro\templates\order\jobticket_large.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b4bb20b94bf4_65440624',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2cf603ca5d631e27554213b3f4455cf608776426' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\Branding\\\\ubbpro\\templates\\order\\jobticket_large.tpl',
      1 => 1649492534,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/googletagmanager.tpl' => 1,
    'file:includes/customerinclude_large.tpl' => 1,
    'file:order/jobticket.tpl' => 1,
    'file:order/companions.tpl' => 1,
  ),
),false)) {
function content_69b4bb20b94bf4_65440624 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.csrf_token.php','function'=>'smarty_function_csrf_token',),));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" xml:lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" dir="ltr">
    <head>
        <meta name="csrf-token" content="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo $_smarty_tpl->tpl_vars['appname']->value;?>
 - <?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title>

        <?php if ($_smarty_tpl->tpl_vars['googletagmanagercccode']->value != '') {?>
            <?php $_smarty_tpl->_subTemplateRender("file:includes/googletagmanager.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('googletagmanagercccode'=>$_smarty_tpl->tpl_vars['googletagmanagercccode']->value), 0, false);
?>
        <?php }?>
        
        <?php $_smarty_tpl->_subTemplateRender("file:includes/customerinclude_large.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

        <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>
            <?php $_smarty_tpl->_assignInScope('modalHeight', '348' ,false ,32);?>
            <?php $_smarty_tpl->_assignInScope('modalWidth', '650' ,false ,32);?>
        <?php }?>
    <style type="text/css">

        #componentChangeBox {
            width: <?php echo $_smarty_tpl->tpl_vars['modalWidth']->value;?>
px;
        }
        #componentChangeBox .content {
            height: <?php echo $_smarty_tpl->tpl_vars['modalHeight']->value;?>
px;
        }

    </style>
    <?php echo '<script'; ?>
 type="text/javascript" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
>
        //<![CDATA[

<?php $_smarty_tpl->_subTemplateRender("file:order/jobticket.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>



        window.addEventListener('DOMContentLoaded', function(e) {
            document.body.addEventListener('click', decoratorListener);
            document.body.addEventListener('change', decoratorListener);
            document.body.addEventListener('keypress', decoratorListener);
            document.body.addEventListener('keyup', decoratorListener);
            document.body.addEventListener('submit', decoratorListener);
        });

    
    <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty' || $_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>
    

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
							this.src = '<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/no_image-2x.jpg';
						}
					});
				}
			}
		});

	
	<?php }?>
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
	
	<?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>
	
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

    
    <?php } elseif (($_smarty_tpl->tpl_vars['stage']->value == 'shipping')) {?>

        <?php $_smarty_tpl->_assignInScope('showAllCode', '***ALL***');?>
        

        var gShowAllCode = "<?php echo $_smarty_tpl->tpl_vars['showAllCode']->value;?>
";
        var gCountryCode = "<?php echo $_smarty_tpl->tpl_vars['countrylist']->value[0]['code'];?>
";
        var gSearchCountry = "<?php echo $_smarty_tpl->tpl_vars['initialfilter']->value['country'];?>
";
        var gSearchRegion = "<?php echo $_smarty_tpl->tpl_vars['initialfilter']->value['region'];?>
";
        var gSearchStoreGroup = "<?php echo $_smarty_tpl->tpl_vars['initialfilter']->value['storeGroup'];?>
";
        var gSearchText = "<?php echo $_smarty_tpl->tpl_vars['initialfilter']->value['filter'];?>
";
        var gPrivateSearchText = "<?php echo $_smarty_tpl->tpl_vars['initialfilter']->value['privateFilter'];?>
";
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
                selectObject.options[selectObject.length] = new Option("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_RegionOptional');?>
", gShowAllCode, true, false);
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
                                selectObject.options[selectObject.length] = new Option("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_RegionOptional');?>
", gShowAllCode, true, false);
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
                selectObject.options[0] = new Option("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SiteGroupOptional');?>
", gShowAllCode, true, false);
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
                                selectObject.options[selectObject.length] = new Option("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SiteGroupOptional');?>
", gShowAllCode, true, false);
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
                selectObject.options[selectObject.length] = new Option("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SiteGroupOptional');?>
", gShowAllCode, true, false);
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
                                selectObject.options[selectObject.length] = new Option("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_RegionOptional');?>
", gShowAllCode, true, false);
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
                alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoStore');?>
");
                return false;
            }
            else
            {
                var fields = '&storecode=' + storeCode + '&externalstore=' + isExternalStore + '&country=' + gSearchCountry + '&region=' + gSearchRegion + 
                    '&storegroup=' + gSearchStoreGroup + '&filter=' + gSearchText + '&privatefilter=' + gPrivateSearchText + '&payinstoreallowed=' + gPayInStoreOption +
                    '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
&shippingratecode=<?php echo $_smarty_tpl->tpl_vars['shippingratecode']->value;?>
&sameshippingandbillingaddress=<?php echo $_smarty_tpl->tpl_vars['sameshippingandbillingaddress']->value;?>
&shippingcfscontact=<?php echo $_smarty_tpl->tpl_vars['shippingcfscontact']->value;?>
';

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

    
    <?php }?>
    

        // Show / hide companion product options link.
        function fnToggleGeneric(pElement)
        {
            toggleGeneric(pElement.getAttribute("data-lineid"), pElement.getAttribute("data-idelm"), pElement.getAttribute("data-colour"));
        }

        window.onload = function()
        {
    

        <?php echo $_smarty_tpl->tpl_vars['custominit']->value;?>


    <?php if ((($_smarty_tpl->tpl_vars['showgiftcardmessage']->value == 1) && ($_smarty_tpl->tpl_vars['stage']->value == 'payment'))) {?>
            displayGiftCardAlert('<?php echo $_smarty_tpl->tpl_vars['voucherstatusResult']->value;?>
', '<?php echo $_smarty_tpl->tpl_vars['vouchercustommessage']->value;?>
');
    <?php }?>

        <?php echo $_smarty_tpl->tpl_vars['initlanguage']->value;?>


        <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'companionselection')) {?>
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
        
        <?php } elseif (($_smarty_tpl->tpl_vars['stage']->value == 'shipping')) {?>
        
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
        
        <?php } elseif (($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>
        
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

            
            <?php if ($_smarty_tpl->tpl_vars['showtermsandconditions']->value == 1) {?>
            
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
            
            <?php }?>
        <?php }?>
        
        
            // Add listener to the bottom continue button.
            var topContinuebuttonElement = document.getElementById('ordercontinuebutton');
            if (topContinuebuttonElement)
            {
				var listenerCallFunction = '';
				<?php if ($_smarty_tpl->tpl_vars['stage']->value != 'payment') {?>
                listenerCallFunction = acceptDataEntry;
				<?php } else { ?>
				listenerCallFunction = orderButtonCompleteOrder;
				<?php }?>

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
        
        <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>
        
			if ((componentChangeBox) && (shimObj) && (componentChangeBox.style.display == "block"))
			{
				shimObj.style.height = document.body.offsetHeight + 'px';

				componentChangeBox.style.left = Math.round((shimObj.offsetWidth / 2) - (<?php echo $_smarty_tpl->tpl_vars['modalWidth']->value;?>
/2)) + 'px';
				windowHeight = document.documentElement.clientHeight;
				componentChangeBox.style.top = Math.round((windowHeight - componentChangeBox.offsetHeight) / 2) + 'px';
			}
        
        <?php }?>
        
		}

    
	<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>
    

        function previousOrderStage()
        {
			saveTempMetadata();
			document.submitform.fsaction.value = "Order.back";
            document.submitform.submit();
            return false;
        }

	
	<?php }?>

    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'shipping') {?>
    

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
    
    <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>

    
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
    
    <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>

    
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

            document.getElementById('labelShippingStoreAddress').innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShippingAddress');?>
";
            document.getElementById('shippingStoreAddress').innerHTML = "<?php echo $_smarty_tpl->tpl_vars['encodedshippingaddress']->value;?>
";
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

            document.getElementById('labelShippingStoreAddress').innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStoreAddress');?>
";
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
    
    <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>

    

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
            processAjax('storeLocatorForm', ".?fsaction=Order.selectStoreDisplay&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
&shippingratecode="+shippingCode+
                "&sameshippingandbillingaddress=" + sameshippingandbillingaddress + "&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
"+
                "&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
", 'GET', '');
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

    
    <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>

    

            document.submitform.fsaction.value = "Order.back";
            document.submitform.submit();
            return false;
        }

    
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
	

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
                                alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorValueRequired');?>
");
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
                                alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorValueRequired');?>
");
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
                            alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorValueRequired');?>
");
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
                            alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorValueRequired');?>
");
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
                    alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorAcceptTermsAndConditions');?>
");

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
    
    <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>

    
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

    
    <?php }?>
        //]]>
    <?php echo '</script'; ?>
>
    </head>
    <!--[if IE 6]><body onload="initialize();" style="position: relative" class="ie6" id="shoppingCart" onresize="resizePopup();"><![endif]-->
    <!--[if gt IE 6]><!-->
    <body style="position: relative" id="shoppingCart">
    <!--<![endif]-->
        <!-- store locator code -->
<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'shipping') {?>
        <div id="shim">&nbsp;</div>
        <div id="storeLocator" class="section">
            <div class="dialogTop">
                <h2 class="title-bar">
                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSelectStore');?>

                </h2>
            </div>
            <div id="storeLocatorForm" class="contentStoreLocator"></div>
        </div>
        <div id="storeInfo" class="section"></div>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
        <div id="dialogOuter" class="dialogOuter"></div>
        <div id="shim">&nbsp;</div>
        <div id="ordersTermsAndCondtions" class="section">
            <div class="dialogTop">
                <h2 class="title-bar">
                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleTermsAndConditions');?>

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
					<img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/loading_shoppingcart_v2.gif" class="loading-icon" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageLoading');?>
" />
				</div>
			</div>
		</div>
		<div id="shimLoading">&nbsp;</div>
<?php }?>
<!--  end of store locator -->

<!-- component change box -->
<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>
        <div id="shim">&nbsp;</div>
        <div id="componentChangeBox" class="section"></div>
<?php }?>
        <!--  end of component change box -->
        <!-- START OF SHOPPING CART-->
        <div id="outerPage" class="order-section outer-page<?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?> fullsize-outer-page<?php }?>">
            <div id="header" class="headerScroll">
                <div class="headerinside">
                    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['header']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                </div>
            </div>
            <div class="contentNavigation <?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?> fullsize-navigation<?php }?>">
	<?php if ($_smarty_tpl->tpl_vars['hasCompanions']->value == 'YES') {?>
                <div class="contentNavigationImageCompanion">
	<?php } else { ?>
                <div class="contentNavigationImage">
	<?php }
if ($_smarty_tpl->tpl_vars['stage']->value == 'companionselection') {?>
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
<?php } elseif ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>
                    <div class="navigationLongBloc">
	<?php if ($_smarty_tpl->tpl_vars['hasCompanions']->value == 'YES') {?>
                        <div class="navigationActiveRight"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="navigationLongBloc">
                        <div class="navigationActiveLeftInactiveRight"></div>
	<?php } else { ?>
                        <div class="navigationActiveInactiveRight"></div>
	<?php }?>
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
<?php } elseif ($_smarty_tpl->tpl_vars['stage']->value == 'shipping') {?>
                    <div class="navigationLongBloc">
                        <div class="navigationActiveRight"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
	<?php if ($_smarty_tpl->tpl_vars['hasCompanions']->value == 'YES') {?>
                    <div class="navigationLongBloc">
                        <div class="navigationActiveMiddle"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
	<?php }?>
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
<?php } elseif ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
                    <div class="navigationLongBloc">
                        <div class="navigationActiveRight"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
	<?php if ($_smarty_tpl->tpl_vars['hasCompanions']->value == 'YES') {?>
                    <div class="navigationLongBloc">
                        <div class="navigationActiveMiddle"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
	<?php }?>
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
<?php }?>
                    <div class="clear"></div>
                </div>
	<?php if ($_smarty_tpl->tpl_vars['hasCompanions']->value == 'YES') {?>
                <div class="contentNavigationTextCompanion">
                    <div class="labelNavigation"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNavigationCompanionSelection');?>
</div>
	<?php } else { ?>
                <div class="contentNavigationText">
	<?php }?>
                    <div class="labelNavigation"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCartSummary');?>
</div>
                    <div class="labelNavigation"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNavigationShippingBilling');?>
</div>
                    <div class="labelNavigation"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNavigationPayment');?>
</div>
                    <div class="labelNavigation"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelNavigationConfirmation');?>
</div>
                    <div class="clear"></div>
                </div>
            </div>
            <div id="contentScroll" class="contentScrollCart">
              <div id="contentHolder">
                    <form id="orderform" name="orderform" method="post" action="#" data-decorator="fnPreventFormSubmit" data-trigger="submit">
                        <div <?php if (($_smarty_tpl->tpl_vars['stage']->value != 'qty') && ($_smarty_tpl->tpl_vars['stage']->value != 'payment')) {?>id="pageFooterHolder"<?php }?>>
                            <div id="page" class="section backgroundGrey">
<?php if ($_smarty_tpl->tpl_vars['metadatalayout']->value != '' && $_smarty_tpl->tpl_vars['stage']->value != 'payment') {?>
                                <h2 class="title-bar <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>marginBloc<?php }?>">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAdditionalInformation');?>

                                </h2>
                                <div class="content contentBloc">
                                    <div id="metadatatableobj" class="metadataadditional">
                                        <?php echo $_smarty_tpl->tpl_vars['metadatalayout']->value;?>

                                    </div>
                                </div>
<?php }
if (($_smarty_tpl->tpl_vars['stage']->value == 'companionselection')) {?>
                                <div id="orderContent">
									<div id="companionOptionsHeader">
										<div id="companionQuestionText">
											<?php echo $_smarty_tpl->tpl_vars['companionheadertitle']->value;?>

										</div>
										<div class="btnRight">
											<div class="contentBtn" id="topordercontinuebutton">
												<div class="btn-green-left"></div>
												<div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonContinue');?>
</div>
												<div class="btn-green-arrow-right"></div>
											</div>
										</div>
									</div>

									<!-- companion book selection start -->
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderitems']->value, 'orderitem', false, 'itemIndex', 'orderItemsLoop', array (
));
$_smarty_tpl->tpl_vars['orderitem']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['itemIndex']->value => $_smarty_tpl->tpl_vars['orderitem']->value) {
$_smarty_tpl->tpl_vars['orderitem']->do_else = false;
?>
	    <?php if ($_smarty_tpl->tpl_vars['orderitem']->value['parentorderitemid'] == 0 && $_smarty_tpl->tpl_vars['orderitem']->value['itemhascompanions']) {?>
	    	<?php $_smarty_tpl->_subTemplateRender("file:order/companions.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('orderline'=>$_smarty_tpl->tpl_vars['orderitem']->value,'companions'=>$_smarty_tpl->tpl_vars['companionOptions']->value[$_smarty_tpl->tpl_vars['itemIndex']->value],'parentLineID'=>$_smarty_tpl->tpl_vars['itemIndex']->value), 0, true);
?>
	    <?php }?>
	<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
									<!-- companion book selection end -->
                                </div>
<?php } elseif (($_smarty_tpl->tpl_vars['stage']->value == 'qty') || ($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>
								<div id="orderContent">
                               <h2 class="title-bar">
	<?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty')) {?>
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCartSummary');?>

	<?php } else { ?>
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderSummary');?>

	<?php }?>
                                </h2>

    <!-- order lines start -->
		<?php $_smarty_tpl->_assignInScope('lineCounter', 1);?>

			<?php $_smarty_tpl->_assignInScope('closeLineItemContainer', false);?>

		<?php $_smarty_tpl->_assignInScope('closeFinalContainer', false);?>

	    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orderitems']->value, 'orderitem', false, NULL, 'orderItemsLoop', array (
));
$_smarty_tpl->tpl_vars['orderitem']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['orderitem']->value) {
$_smarty_tpl->tpl_vars['orderitem']->do_else = false;
?>

				<?php if ($_smarty_tpl->tpl_vars['orderitem']->value['parentorderitemid'] == 0) {?>
						<?php $_smarty_tpl->_assignInScope('isCompanion', false);?>
			<?php if ($_smarty_tpl->tpl_vars['lineCounter']->value == 1) {?>
								<?php $_smarty_tpl->_assignInScope('closeLineItemContainer', false);?>
			<?php } else { ?>
								<?php $_smarty_tpl->_assignInScope('closeLineItemContainer', true);?>
			<?php }?>
		<?php } else { ?>
						<!-- Companion item. -->
			<?php $_smarty_tpl->_assignInScope('isCompanion', true);?>

						<?php $_smarty_tpl->_assignInScope('closeLineItemContainer', false);?>
		<?php }?>

		<?php if ($_smarty_tpl->tpl_vars['closeLineItemContainer']->value == true) {?>
						</div> <!-- End of line item. -->
		<?php }?>

		<?php if ($_smarty_tpl->tpl_vars['isCompanion']->value == false) {?>
						<div id='orderlinecontainer_<?php echo $_smarty_tpl->tpl_vars['lineCounter']->value;?>
' class='orderlinecontainer'> <!-- New line item. -->

						<?php $_smarty_tpl->_assignInScope('closeFinalContainer', true);?>
		<?php }?>

				<?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['orderline']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('orderline'=>$_smarty_tpl->tpl_vars['orderitem']->value), 0, true);
?>

				<?php $_smarty_tpl->_assignInScope('lineCounter', $_smarty_tpl->tpl_vars['lineCounter']->value+1);?>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

		<?php if ($_smarty_tpl->tpl_vars['closeFinalContainer']->value == true) {?>
			</div>
	<?php }?>
    <!-- order lines end -->
                                    <!-- order footer start -->
                                    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['orderfooter']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                                    <!-- order footer end -->
                                </div>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
    <?php if ($_smarty_tpl->tpl_vars['metadatalayout']->value != '') {?>
                                <div class="contentPaymentMetaDataBloc">
                                    <h2 class="title-bar">
                                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAdditionalInformation');?>

                                    </h2>
                                    <div class="content contentBloc" id="orderContent">
                                        <div id="metadatatableobj" class="metadataadditional">
                                            <?php echo $_smarty_tpl->tpl_vars['metadatalayout']->value;?>

                                        </div>
                                    </div>
                                </div>
    <?php }?>
                                <div class="contentPaymentBloc">
                                <h2 class="title-bar">
                                    <?php echo $_smarty_tpl->tpl_vars['ordertitle']->value;?>

                                </h2>
                                <div class="content contentPayment">
                                                                        <div class="backgroundShipping">
                                        <div class="contentAddress">
                                            <div class="contentAddressHeader">
                                                <div class="titleAddressLeft">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingStoreAddressLabel']->value;?>

                                                </div>
                                                <div class="titleAddressRight">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelBillingAddress');?>

                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="contentAddressBody">
                                                <div class="shippingSummary">
                                                    <div class="shippingPadding">
                                                        <?php echo $_smarty_tpl->tpl_vars['shippingaddress']->value;?>

                                                    </div>
                                                </div>
                                                <div class="billingSummary">
                                                    <div class="shippingPadding">
                                                        <?php echo $_smarty_tpl->tpl_vars['billingaddress']->value;?>

                                                    </div>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                     </div>
                                    <div class="line-total">

                                        <div class="line-sub-total">
                                            <span class="total-heading">
                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShipping');?>
 (<?php echo $_smarty_tpl->tpl_vars['shippingmethodname']->value;?>
):
                                            </span>
                                            <span class="order-line-price">
                                                <?php echo $_smarty_tpl->tpl_vars['ordertotalshipping']->value;?>

                                            </span>
                                            <div class="clear"></div>
                                        </div>

<!-- VOUCHER -->
<?php if (($_smarty_tpl->tpl_vars['vouchersection']->value == 'SHIPPING') || (($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value == true) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>

    <!-- SHIPPING VOUCHER  -->
    <?php if (($_smarty_tpl->tpl_vars['vouchersection']->value == 'SHIPPING')) {?>
        <?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>

                                        <div class="line-sub-total-nopadding">
                                            <span class="total-heading">
                                                <?php echo $_smarty_tpl->tpl_vars['shippingdiscountname']->value;?>
:
                                            </span>
                                            <span class="order-line-price">
                                                <?php echo $_smarty_tpl->tpl_vars['shippingdiscountvalue']->value;?>

                                            </span>
                                            <div class="clear"></div>
                                        </div>
        <?php }?>

        <!-- SHOW SHIPPING TAX  -->
        <?php if (($_smarty_tpl->tpl_vars['showshippingtax']->value)) {?>

            <!-- SHOW PRICES WITH TAX  -->
            <?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
                <?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                                                </span>
                                                <span class="order-line-price">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>

                                                </span>
                                                <div class="clear"></div>
                                            </div>
                <?php }?>
                                            <div class="line-sub-total-small-bottom">
                                                <?php echo $_smarty_tpl->tpl_vars['includesshippingtaxtext']->value;?>

                                            </div>
            <?php } else { ?>
                <?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:
                                                </span>
                                                <span class="order-line-price">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>

                                                </span>
                                                <div class="clear"></div>
                                            </div>
                <?php }?>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingtaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['shippingtaxrate']->value;?>
%):
                                                </span>
                                                <span class="order-line-price">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingtaxtotal']->value;?>

                                                </span>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                                                </span>
                                                <span class="order-line-price">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>

                                                </span>
                                                <div class="clear"></div>
                                            </div>
            <?php }?>

        <?php } else { ?>
                <?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                                                </span>
                                                <span class="order-line-price">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>

                                                </span>
                                                <div class="clear"></div>
                                            </div>
                <?php }?>
        <?php }?>

    <?php }?>

    <!-- TOTAL VOUCHER  -->
    <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value == true) && (!$_smarty_tpl->tpl_vars['specialvouchertype']->value))) {?>

        <?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>
                                        <div class="line-sub-total">
                                            <span class="total-heading">
                                                <?php echo $_smarty_tpl->tpl_vars['shippingdiscountname']->value;?>
:
                                            </span>
                                            <span class="order-line-price">
                                                <?php echo $_smarty_tpl->tpl_vars['shippingdiscountvalue']->value;?>

                                            </span>
                                            <div class="clear"></div>
                                        </div>
        <?php }?>

        <!-- SHOW SHIPPING TAX  -->
        <?php if (($_smarty_tpl->tpl_vars['showshippingtax']->value)) {?>

            <!-- SHOW PRICES WITH TAX  -->
            <?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
                <?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                                                </span>
                                                <span class="order-line-price">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>

                                                </span>
                                                <div class="clear"></div>
                                            </div>
                <?php }?>
                                            <div class="line-sub-total-small-bottom">
                                                <?php echo $_smarty_tpl->tpl_vars['includesshippingtaxtext']->value;?>

                                            </div>
            <?php } else { ?>
                <?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:
                                                </span>
                                                <span class="order-line-price">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>

                                                </span>
                                                <div class="clear"></div>
                                            </div>
                <?php }?>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingtaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['shippingtaxrate']->value;?>
%):
                                                </span>
                                                <span class="order-line-price">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingtaxtotal']->value;?>

                                                </span>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                                                </span>
                                                <span class="order-line-price">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>

                                                </span>
                                                <div class="clear"></div>
                                            </div>
            <?php }?>

        <?php } else { ?>
                <?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                                                </span>
                                                <span class="order-line-price">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>

                                                </span>
                                                <div class="clear"></div>
                                            </div>
                <?php }?>
        <?php }?>
    <?php }?>

<?php } else { ?>

    <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value == true) && ($_smarty_tpl->tpl_vars['specialvouchertype']->value)) || ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value)) {?>
        <?php if (($_smarty_tpl->tpl_vars['shippingdiscountvalueraw']->value > 0)) {?>
                                        <div class="line-sub-total">
                                            <span class="total-heading">
                                                <?php echo $_smarty_tpl->tpl_vars['shippingdiscountname']->value;?>
:
                                            </span>
                                            <span class="order-line-price">
                                                <?php echo $_smarty_tpl->tpl_vars['shippingdiscountvalue']->value;?>

                                            </span>
                                            <div class="clear"></div>
                                        </div>
                    <?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>

                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                                                </span>
                                                <span class="order-line-price">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>

                                                </span>
                                                <div class="clear"></div>
                                            </div>
                    <?php } else { ?>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSubTotal');?>
:
                                                </span>
                                                <span class="order-line-price">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingdiscountedvalue']->value;?>

                                                </span>
                                                <div class="clear"></div>
                                            </div>
                    <?php }?>

        <?php }?>
        <!-- SHOW SHIPPING TAX  -->
        <?php if (($_smarty_tpl->tpl_vars['showshippingtax']->value)) {?>

            <!-- SHOW PRICES WITH TAX  -->
            <?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
                                            <div class="line-sub-total-small-bottom">
                                                <?php echo $_smarty_tpl->tpl_vars['includesshippingtaxtext']->value;?>

                                            </div>
            <?php } else { ?>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingtaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['shippingtaxrate']->value;?>
%):
                                                </span>
                                                <span class="order-line-price">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingtaxtotal']->value;?>

                                                </span>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                                                </span>
                                                <span class="order-line-price">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>

                                                </span>
                                                <div class="clear"></div>
                                            </div>
            <?php }?>

        <?php }?>
    <?php } else { ?>

        <!-- SHOW SHIPPING TAX  -->
        <?php if (($_smarty_tpl->tpl_vars['showshippingtax']->value)) {?>

            <!-- SHOW PRICES WITH TAX  -->
            <?php if (($_smarty_tpl->tpl_vars['showpriceswithtax']->value)) {?>
                                            <div class="line-sub-total-small-bottom">
                                                <?php echo $_smarty_tpl->tpl_vars['includesshippingtaxtext']->value;?>

                                            </div>
            <?php } else { ?>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingtaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['shippingtaxrate']->value;?>
%):
                                                </span>
                                                <span class="order-line-price">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingtaxtotal']->value;?>

                                                </span>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="line-sub-total">
                                                <span class="total-heading">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingTotal');?>
:
                                                </span>
                                                <span class="order-line-price">
                                                    <?php echo $_smarty_tpl->tpl_vars['shippingtotal']->value;?>

                                                </span>
                                                <div class="clear"></div>
                                            </div>
            <?php }?>

        <?php }?>
    <?php }?>

<?php }?>

                                    </div>
                                    <div class="clear"></div>
                                            <h2 class="title-bar-inside">
                                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelTotalTitle');?>

                                    </h2>
        <?php if ($_smarty_tpl->tpl_vars['showvouchers']->value == true) {?>
                                    <div class="orderSummaryVoucher" id="ordertotalsummary">
                                        <div class="contentvoucher">
                                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnterOrderVoucher');?>
<br /><br />
        <?php if ($_smarty_tpl->tpl_vars['voucherstatus']->value != '') {?>
                                            <b><?php echo $_smarty_tpl->tpl_vars['voucherstatus']->value;?>
</b><br /><br />
        <?php }?>

        <?php if (($_smarty_tpl->tpl_vars['defaultdiscountactive']->value == false) && ($_smarty_tpl->tpl_vars['vouchercode']->value != '')) {?>
                                            <input type="text" id="vouchercode" name="vouchercode" placeholder="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelVoucherCode');?>
" class="voucherinput" value="<?php echo $_smarty_tpl->tpl_vars['vouchercode']->value;?>
" readonly="readonly" />
        <?php } else { ?>
                                            <input type="text" id="vouchercode" name="vouchercode" placeholder="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelVoucherCode');?>
" class="voucherinput" value="" />
        <?php }?>                                         

        <?php if ($_smarty_tpl->tpl_vars['vouchercode']->value == '' || ($_smarty_tpl->tpl_vars['vouchercode']->value != '' && $_smarty_tpl->tpl_vars['defaultdiscountactive']->value == true)) {?>
                                            <div class="contentBtn" id="setvoucher">
                                                <div class="btn-white-left" ></div>
                                                <div class="btn-white-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedeem');?>
</div>
                                                <div class="btn-white-right"></div>
                                            </div>
                                            <div class="clear"></div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['vouchercode']->value != '' && $_smarty_tpl->tpl_vars['defaultdiscountactive']->value == false) {?>
                                            <div class="contentBtn" id="removevoucher">
                                                <div class="btn-white-left" ></div>
                                                <div class="btn-white-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRemove');?>
</div>
                                                <div class="btn-white-right"></div>
                                            </div>
                                            <div class="clear"></div>
        <?php }?>
                                        </div>
                                    </div>
    <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['showgiftcardsbalance']->value == true) {?>
                                    <div class="<?php if ($_smarty_tpl->tpl_vars['showvouchers']->value == true) {?>orderSummaryGift<?php } else { ?>orderSummaryGiftNoDot<?php }?>">
                                        <div class="contentvoucher">
                                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnterOrderGiftCard');?>
<br /><br />
        <?php if ($_smarty_tpl->tpl_vars['giftcardstatus']->value != '') {?>
                                            <b><?php echo $_smarty_tpl->tpl_vars['giftcardstatus']->value;?>
</b><br /><br />
        <?php }?>
                                            <div class="clear"></div>
                                            <input type="text" id="giftcardcode" name="giftcardcode" placeholder="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGiftCardCode');?>
" value="" class="voucherinput" />
                                            <div class="contentBtn" id="setgiftcard">
                                                <div class="btn-white-left" ></div>
                                                <div class="btn-white-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRedeem');?>
</div>
                                                <div class="btn-white-right"></div>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
    <?php }?>
                                    <div class="<?php if ($_smarty_tpl->tpl_vars['showgiftcardsbalance']->value == true || $_smarty_tpl->tpl_vars['showvouchers']->value == true) {?>line-total<?php } else { ?>line-total-no-dot<?php }?>">

    <?php if (((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value == false)) || (($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value) && ($_smarty_tpl->tpl_vars['specialvouchertype']->value)))) {?>
        <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value == false) && ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value == false))) {?>
                                                <div class="line-sub-total-small">
                                            <span class="total-heading">
                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderSubTotal');?>
:
                                            </span>
                                            <span class="order-line-price-small">
                                                <?php echo $_smarty_tpl->tpl_vars['orderbeforediscounttotalvalue']->value;?>

                                            </span>
                                            <div class="clear"></div>
                                        </div>
                                                <div class="line-sub-total-small">
                                            <span class="total-heading">
                                                <?php echo $_smarty_tpl->tpl_vars['orderaftertotaldiscountname']->value;?>
:
                                            </span>
                                            <span class="order-line-price-small">
                                                <?php echo $_smarty_tpl->tpl_vars['ordertotaldiscountvalue']->value;?>

                                            </span>
                                            <div class="clear"></div>
                                        </div>
        <?php } else { ?>
            <?php if ((($_smarty_tpl->tpl_vars['vouchersection']->value == 'TOTAL') && ($_smarty_tpl->tpl_vars['differenttaxrates']->value == false) && ($_smarty_tpl->tpl_vars['applyVoucherAsLineDiscount']->value == true))) {?>
                                        <div class="line-sub-total-small">
                                            <span class="total-heading">
                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderSubTotal');?>
:
                                            </span>
                                            <span class="order-line-price-small">
                                                <?php echo $_smarty_tpl->tpl_vars['ordersubtotal']->value;?>

                                            </span>
                                            <div class="clear"></div>
                                        </div>
            <?php }?>
        <?php }?>
    <?php } else { ?>
        <?php if (($_smarty_tpl->tpl_vars['differenttaxrates']->value == false) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value == false) && (($_smarty_tpl->tpl_vars['hastotaltax']->value == true) || ($_smarty_tpl->tpl_vars['showzerotax']->value == true))) {?>
                                            <div class="line-sub-total-small">
                                            <span class="total-heading">
                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderSubTotal');?>
:
                                            </span>
                                            <span class="order-line-price-small">
                                                <?php echo $_smarty_tpl->tpl_vars['ordersubtotal']->value;?>

                                            </span>
                                            <div class="clear"></div>
                                        </div>
        <?php }?>
    <?php }?>
    <?php if (($_smarty_tpl->tpl_vars['differenttaxrates']->value == false) && ($_smarty_tpl->tpl_vars['showpriceswithtax']->value == false) && (($_smarty_tpl->tpl_vars['hastotaltax']->value == true) || ($_smarty_tpl->tpl_vars['showzerotax']->value == true))) {?>
                                            <div class="line-sub-total-small">
                                            <span class="total-heading">
                                                <?php echo $_smarty_tpl->tpl_vars['itemtaxname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['itemtaxrate']->value;?>
%):
                                            </span>
                                            <span class="order-line-price-small">
                                                <?php echo $_smarty_tpl->tpl_vars['ordertotaltax']->value;?>

                                            </span>
                                            <div class="clear"></div>
                                        </div>
    <?php }?>
        <?php if (($_smarty_tpl->tpl_vars['ordergiftcardtotal']->value > 0)) {?>
                                        <div class="line-sub-total-small">
                                            <span class="total-heading">
                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderTotal');?>
:
                                            </span>
                                            <span class="order-line-price-small">
                                                <?php echo $_smarty_tpl->tpl_vars['ordertotal']->value;?>

                                            </span>
                                            <div class="clear"></div>
                                        </div>
        
        <?php if (((($_smarty_tpl->tpl_vars['showalwaystaxtotal']->value == true) || ($_smarty_tpl->tpl_vars['showtaxbreakdown']->value == true) || ($_smarty_tpl->tpl_vars['showzerotax']->value == true)) && ($_smarty_tpl->tpl_vars['includestaxtotaltext']->value != ''))) {?>
                                            <div <?php if (($_smarty_tpl->tpl_vars['ordergiftcardtotal']->value == 0 || $_smarty_tpl->tpl_vars['disabled_giftcard']->value == 'disabled')) {?> style="display:none" <?php }?> class="line-sub-total-small" id="includetaxtextwithgiftcard">
                                                <?php echo $_smarty_tpl->tpl_vars['includestaxtotaltext']->value;?>

                                            </div>
        <?php }?>
                                        <div id="giftcard" class="line-sub-total-small gift-card-box-button <?php echo $_smarty_tpl->tpl_vars['disabled_giftcard']->value;?>
">
                                            <span class="total-heading">
                                                <span id="giftbutton" title="<?php echo $_smarty_tpl->tpl_vars['tooltipGiftcardButton']->value;?>
" class="button-voucher class_gift_<?php echo $_smarty_tpl->tpl_vars['add_delete_giftcard']->value;?>
"></span>
                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGiftCard');?>
:
                                            </span>
                                            <span class="order-line-price-small" id="giftcardamount">
                                                <?php echo $_smarty_tpl->tpl_vars['ordergiftcardtotalvalue']->value;?>

                                            </span>
                                            <div class="clear"></div>
                                        </div>
    <?php }?>
                                            <div class="line-sub-total">
                                            <span class="total-heading">
                                                <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAmountToPay');?>
:
                                            </span>
                                            <span class="order-line-price" id="ordertotaltopayvalue">
                                                <?php echo $_smarty_tpl->tpl_vars['ordertotaltopayvalue']->value;?>

                                            </span>
                                            <div class="clear"></div>
                                        </div>


        <?php if ((($_smarty_tpl->tpl_vars['showalwaystaxtotal']->value == true) || ($_smarty_tpl->tpl_vars['showtaxbreakdown']->value == true) || ($_smarty_tpl->tpl_vars['showzerotax']->value == true))) {?>
											<div <?php if ($_smarty_tpl->tpl_vars['ordergiftcardtotal']->value > 0 && $_smarty_tpl->tpl_vars['disabled_giftcard']->value != 'disabled') {?> style="display:none" <?php }?> class="line-sub-total-small-bottom" id="includetaxtextwithoutgiftcard">
												<?php echo $_smarty_tpl->tpl_vars['includestaxtotaltext']->value;?>

											</div>
		<?php }?>


                                    </div>
                                            <div <?php if ($_smarty_tpl->tpl_vars['hidepayments']->value) {?>style="display:none"<?php }?> id="paymenttableobj">
                                        <h2 class="title-bar-inside">
                                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPaymentMethod');?>

                                        </h2>
                                        <div id="paymentMethodsList">
                                            <?php echo $_smarty_tpl->tpl_vars['paymentmethodslist']->value;?>

                                        </div>
                                    </div>
                                    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment' && $_smarty_tpl->tpl_vars['showtermsandconditions']->value == 1) {?>
									<h2 class="title-bar-inside"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleTermsAndConditions');?>
</h2>
									<div id="ordertermsandconditionscontainer">
										<input type="checkbox" name="ordertermsandconditions" id="ordertermsandconditions">
                                        <label for="ordertermsandconditions"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelTermsAndConditionsAgreement');?>
 <a id="ordertermsandconditionslink" href="#" class="termsAndConditionsLink"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleTermsAndConditions');?>
</a></label>
									</div>
								<?php }?>
                                </div>
    </div>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'shipping') {?>
                                <h2 class="title-bar <?php if (($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>marginBloc<?php }?>">
                                    <?php echo $_smarty_tpl->tpl_vars['ordertitle']->value;?>

                                </h2>
                                <div class="content contentBloc">
                                    <div id="shippingtableobj">
                                        <div id="addressHolder">
                                            <div class="contentHeaderShipping">
                                                <h2 id="labelShippingStoreAddress" class="shippingHeader">
                                                    <?php echo $_smarty_tpl->tpl_vars['initialShippingStoreAddressLabel']->value;?>

                                                </h2>
                                                <h2 class="shippingHeader">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelBillingAddress');?>

                                                </h2>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="contentShipping">
                                                <div id="shippingAddress" class="shippingAddress<?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?> fullsize-outer-page<?php }?>">
                                                    <div id="shippingStoreAddress" class="shippingPadding">
                                                        <?php if ((($_smarty_tpl->tpl_vars['collectFromStoreCode']->value != '') || ($_smarty_tpl->tpl_vars['collectFromStore']->value != 1))) {?>
                                                            <?php echo $_smarty_tpl->tpl_vars['initialShippingStoreAddress']->value;?>

                                                        <?php }?>
                                                    </div>
                                                </div>
                                                <div id="billingAddress" class="billingAddress<?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?> fullsize-outer-page<?php }?>">
                                                    <div class="shippingPadding">
                                                        <?php echo $_smarty_tpl->tpl_vars['billingaddress']->value;?>

                                                    </div>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="contentShippingBottom">

    <?php if ((($_smarty_tpl->tpl_vars['canmodifyshipping']->value == true) || ($_smarty_tpl->tpl_vars['canmodifybilling']->value == true) || ($_smarty_tpl->tpl_vars['optionCFS']->value))) {?>
        <?php if ($_smarty_tpl->tpl_vars['canmodifyshipping']->value == true) {?>
                                                <div id="changeShippingDiv" class="alignBottom" <?php if ($_smarty_tpl->tpl_vars['collectFromStore']->value == 1) {?>style="display:none"<?php }?>>
                                                    <div class="contentBtn" id="changeshipping">
                                                        <div class="btn-white-left" ></div>
                                                        <div class="btn-white-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonChange');?>
</div>
                                                        <div class="btn-white-right"></div>
                                                    </div>
                                                </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['storeisfixed']->value == 0) {?>
                                                <div id="selectStoreDiv" class="alignBottom" <?php if ($_smarty_tpl->tpl_vars['collectFromStore']->value == 0) {?>style="display:none"<?php }?>>
                                                    <div class="contentBtn" id="selectStoreButton" data-decorator="fnSelectStore" data-method="shippingmethod<?php echo $_smarty_tpl->tpl_vars['shippingratecode']->value;?>
">
                                                        <div class="btn-white-left" ></div>
                                                        <div class="btn-white-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonSelectStore');?>
</div>
                                                        <div class="btn-white-right"></div>
                                                    </div>

                                                    <div class="contentBtn" id="changeCollectionDetailsButton">
                                                        <div class="btn-disabled-left" ></div>
                                                        <div class="btn-disabled-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonEditCollectionDetails');?>
</div>
                                                        <div class="btn-disabled-right"></div>
                                                    </div>
                                                </div>                                               
        <?php }?>
    <?php }?>
                                                <div class="alignBottomRight">
                                                    <span id="sameasshippingaddressobj" <?php if ((($_smarty_tpl->tpl_vars['canmodifyshipping']->value == false) || ($_smarty_tpl->tpl_vars['canmodifybilling']->value == false))) {?>style="display:none"<?php }?>>
                                                        <input type="checkbox" id="sameasshippingaddress" name="sameasshippingaddress" <?php if (($_smarty_tpl->tpl_vars['sameshippingandbillingaddress']->value == true)) {?>checked="checked"<?php }?> <?php if (($_smarty_tpl->tpl_vars['collectFromStore']->value == 1)) {?>disabled="disabled"<?php }?> />
                                                        <label for="sameasshippingaddress">
                                                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSameAsShippingAddress');?>

                                                        </label>
                                                    </span>
                                                    <?php if ((($_smarty_tpl->tpl_vars['sameshippingandbillingaddress']->value == true) && ($_smarty_tpl->tpl_vars['canmodifyshipping']->value == true))) {?>

                                                    <div class="contentBtn" id="changebilling" disabled="disabled" <?php if (($_smarty_tpl->tpl_vars['canmodifybilling']->value == false)) {?>style="display:none"<?php }?>>
                                                        <div id="changeBillingBtnLeft" class="btn-disabled-left" ></div>
                                                        <div id="changeBillingBtnMiddle" class="btn-disabled-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonChange');?>
</div>
                                                        <div id="changeBillingBtnRight" class="btn-disabled-right"></div>
                                                    </div>

                                                    <?php } else { ?>

                                                    <div class="contentBtn" id="changebilling" <?php if (($_smarty_tpl->tpl_vars['canmodifybilling']->value == false)) {?>style="display:none"<?php }?>>
                                                        <div id="changeBillingBtnLeft" class="btn-white-left" ></div>
                                                        <div id="changeBillingBtnMiddle" class="btn-white-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonChange');?>
</div>
                                                        <div id="changeBillingBtnRight" class="btn-white-right"></div>
                                                    </div>

                                                    <?php }?>

                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                        <div id="shippingMethods">
                                            <h2 class="shippingMethodHeader">
                                                <span class="shippingTextHeader">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShippingMethod');?>

                                                </span>
                                                <span class="shippingCurrency">
                                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingCost');?>
 (<?php echo $_smarty_tpl->tpl_vars['currencyname']->value;?>
)
                                                </span>
                                            </h2>
                                            <ul id="shippingMethodsList">
                                                <?php echo $_smarty_tpl->tpl_vars['shippingmethodslist']->value;?>

                                            </ul>
                                        </div>
                                        <div class="line-total">
                                            <div class="line-sub-total">
                                                <span class="total-heading"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelItemTotalShipping');?>
:</span>
                                                <span class="order-line-price" id="itemsubtotalwithshipping"><?php echo $_smarty_tpl->tpl_vars['ordertotal']->value;?>
</span>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
<?php }?>
                            </div> <!-- content -->
                         </div><!-- pageFooterHolder -->
                    </form>
                    <div class="clear"></div>
                    <div class="buttonBottom">
                        <div id="cancelOrderButton" class="contentBtn">
                            <div class="btn-red-cross-left" ></div>
                            <div class="btn-red-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
</div>
                            <div class="btn-red-right"></div>
                        </div>
                        <div class="btnRight">
<?php if (!(($_smarty_tpl->tpl_vars['stage']->value == 'companionselection') || ($_smarty_tpl->tpl_vars['stage']->value == 'qty' && $_smarty_tpl->tpl_vars['hasCompanions']->value == 'NO'))) {?>
                            <div class="contentBtn" id="backButton">
                                <div class="btn-blue-arrow-left" ></div>
                                <div class="btn-blue-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonBack');?>
</div>
                                <div class="btn-blue-right"></div>
                            </div>
<?php }?>

                            <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
                                <div class="contentBtn" id="ordercontinuebutton">
                                    <div id="btn-confirm-left" class="btn-green-left"></div>
                                    <div id="btn-confirm-middle"class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonConfirmOrder');?>
</div>
                                    <div id="btn-confirm-right" class="btn-accept-right"></div>
                                </div>
                            <?php } else { ?>
                                <div class="contentBtn" id="ordercontinuebutton">
                                    <div class="btn-green-left" ></div>
                                    <div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonContinue');?>
</div>
                                    <div class="btn-green-arrow-right"></div>
                                </div>
                            <?php }?>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div> <!--  contentHolder -->
                <div class="clear"></div>
                <div id="side-outer-panel" class="side-outer-panel cart-side-outer-panel-scroll">
                    <div class="side-panel section blocfixed">
                        <h2 class="title-bar title-bar-panel">
                            <div class="textIcon"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCartSummary');?>
</div>
                            <img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/basket_summary_icon.png" alt="" />
                            <div class="clear"></div>
                        </h2>
                        <div class="content contentBloc panelQty" id="ordersummarypanel">
                            <div class="contentDotted">
                                <div class="titleDetailPanel">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderItemListItemTotal');?>
:
                                </div>
                                <div class="sidebaraccount_gap priceBold">
                                    <?php echo $_smarty_tpl->tpl_vars['orderitemstotalsell']->value;?>

                                </div>
                                <div class="contentDottedImage"></div>
                            </div>
                             <div class="contentDotted">
                                <div class="titleDetailPanel">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderShippingCost');?>
:
                                </div>
                                <div class="sidebaraccount_gap priceBold">
                                    <?php echo $_smarty_tpl->tpl_vars['ordershippingcost']->value;?>

                                </div>
                                <div class="contentDottedImage"></div>
                            </div>
                            <div class="content">
                            <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>
                                <div class="titleDetailPanelBold">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAmountToPay');?>
:
                                </div>
                                <div class="order-line-price-panel sidebaraccount_gap" id="ordertotaltopayvalueside">
                                    <?php echo $_smarty_tpl->tpl_vars['ordertotaltopayvalue']->value;?>

                                </div>
                            <?php } else { ?>
                                <div class="titleDetailPanelBold">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderTotal');?>
:
                                </div>
                                <div class="order-line-price-panel sidebaraccount_gap" id="ordertotaltopayvalueside">
                                    <?php echo $_smarty_tpl->tpl_vars['ordertotal']->value;?>

                                </div>
                            <?php }?>
                            </div>
                        </div>
                    </div>
                    <?php if ($_smarty_tpl->tpl_vars['showgiftcardsbalance']->value == true) {?>
                        <div class="side-panel section blocfixed">
                            <h2 class="title-bar title-bar-panel">
                                <div class="textIcon"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SectionTitleGiftCards');?>
</div>
                                <img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/gift_card_icon.png" alt="" />
                                <div class="clear"></div>
                            </h2>
							<div class="content">
                                <div class="titleDetailPanel">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGiftCardRemaining');?>
:
                                </div>
                                <div class="sidebaraccount_gap priceBold" id="giftcardbalanceside">
                                    <?php echo $_smarty_tpl->tpl_vars['giftcardbalance']->value;?>

                                </div>
                            </div>
                        </div>
                    <?php }?>
                    <div class="contactPanel">
                    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebarcontactdetails']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div> <!-- outer-page -->
        <div style="display:none">
            <form id="submitform" name="submitform" method="post" accept-charset="utf-8" action="#">
                <input type="hidden" id="ref" name="ref" value="<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
" />
                <input type="hidden" id="fsaction" name="fsaction" value="" />
                <input type="hidden" id="ssotoken" name="ssotoken" value="<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
" />
                <input type="hidden" name="itemqty" value="<?php echo $_smarty_tpl->tpl_vars['itemqty']->value;?>
"/>
                <input type="hidden" name="sameshippingandbillingaddress" value=""/>
                <input type="hidden" name="shippingratecode" value=""/>
                <input type="hidden" name="shippingcfscontact" value=""/>
                <input type="hidden" name="paymentmethodcode" value=""/>
                <input type="hidden" name="paymentgatewaycode" value=""/>
                <input type="hidden" name="requiresdelivery" value=""/>
                <input type="hidden" name="vouchercode" value=""/>
                <input type="hidden" name="previousstage" value="<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
"/>
                <input type="hidden" name="stage" value="<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
"/>
                <input type="hidden" name="section" value=""/>
                <input type="hidden" name="orderlineid" value=""/>
                <input type="hidden" name="giftcardcode" value=""/>
                <input type="hidden" name="showgiftcardmessage" value="0"/>
				<input type="hidden" name="ispaypalplus" value="0"/>
                <input type="hidden" name="ispagsegurolb" value="0"/>
                <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
                <?php echo $_smarty_tpl->tpl_vars['metadataform']->value;?>

            </form>
        </div>

<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['paymentgatewayjavascriptarray']->value, 'gatewayscriptarray');
$_smarty_tpl->tpl_vars['gatewayscriptarray']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['gatewayscriptarray']->value) {
$_smarty_tpl->tpl_vars['gatewayscriptarray']->do_else = false;
?>
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['gatewayscriptarray']->value['scripturl'];?>
" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>

    <?php if ($_smarty_tpl->tpl_vars['gatewayscriptarray']->value['form'] != '') {?>

        <?php echo '<script'; ?>
 type="text/javascript" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
>
            <?php echo $_smarty_tpl->tpl_vars['gatewayscriptarray']->value['form'];?>

        <?php echo '</script'; ?>
>

    <?php }?>

<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

    </body>
</html><?php }
}
