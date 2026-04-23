var gStoreCode = "{$storecode}";

{assign var='showAllCode' value='***ALL***'}

gPayInStoreOption = {$payInStoreAllowed};

var gShowAllCode = "{$showAllCode}";
var gCountryCode = "{$countrylist[0].code}";
var gSearchCountry = "{$initialfilter.country}";
var gSearchRegion = "{$initialfilter.region}";
var gSearchStoreGroup = "{$initialfilter.storeGroup}";
var gSearchText = "{$initialfilter.filter}";
var gPrivateSearchText = "{$initialfilter.privateFilter}";

var gInfoHeight = 400;
var gInfoWidth = 600;

{section name=index loop=$storelocations}

    {if $smarty.section.index.first}

        var gCountryCodes = [];
        var gCountryNames = [];
        var gRegionCodes = [];
        var gRegionNames = [];
        var gSiteGroupCodes = [];
        var gSiteGroupNames = [];

    {/if}

    gCountryCodes.push("{$storelocations[index].countryCode}");
    gCountryNames.push("{$storelocations[index].countryName}");
    gRegionCodes.push("{$storelocations[index].regionCode}");
    gRegionNames.push("{$storelocations[index].regionName}");
    gSiteGroupCodes.push("{$storelocations[index].siteGroupCode}");
    gSiteGroupNames.push("{$storelocations[index].siteGroupName}");

{/section}

{if $showcountrylist == 0 && $issmallscreen == false}

	changeCountry("{$countrylist[0].code}");

{/if}
	selectOptionByValue('countries', "{$initialfilter.country}");

{if $initialfilter.country != ""}

	changeCountry("{$initialfilter.country}");

{/if}
    selectOptionByValue('regions', "{$initialfilter.region}");
    changeRegion("{$initialfilter.region}");
    selectOptionByValue('storegroups', "{$initialfilter.storegroup}");
    document.getElementById('searchText').value = "{$initialfilter.filter}";

{literal}

    if (gStoreCode + gSearchText != '')
    {
        searchForStore();
    }

    if (document.getElementById('countries')){
        document.getElementById('countries').onchange = function(){
            changeCountry(this[this.selectedIndex].value);
        };
    }
    if (document.getElementById('regions')){
        document.getElementById('regions').onchange = function(){
            changeRegion(this[this.selectedIndex].value);
        };
    }

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
            addClass('click_disabled', selectObject);
            addClass('click_disabled', selectObject.parentNode);
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
                            removeClass('click_disabled', selectObject);
                            removeClass('click_disabled', selectObject.parentNode);
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
            addClass('click_disabled', selectObject);
            addClass('click_disabled', selectObject.parentNode);
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
                            addClass('click_disabled', selectObject);
                            addClass('click_disabled', selectObject.parentNode);
                            selectObject.disabled = true;
                        }
                        selectObject.disabled = false;
                        selectObject.options[selectObject.length] = new Option(gSiteGroupNames[i], gSiteGroupCodes[i], false, false);
                        removeClass('click_disabled', selectObject);
                        removeClass('click_disabled', selectObject.parentNode);
                        var selectedCountry = countryObject.options[countryObject.selectedIndex].value;
                        if(selectedCountry != gShowAllCode && selectedCountry != '')
                        {
                            selectObject.disabled = false;
                            removeClass('click_disabled', selectObject);
                            removeClass('click_disabled', selectObject.parentNode);
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
            addClass('click_disabled', selectObject.parentNode);
            addClass('click_disabled', selectObject);
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
                        removeClass('click_disabled', selectObject.parentNode);
                        removeClass('click_disabled', selectObject);
                        
                        if (countryObject)
                        {
							var selectedCountry = countryObject.options[countryObject.selectedIndex].value;
							if(selectedCountry != gShowAllCode && selectedCountry != gShowAllCode)
							{
								selectObject.disabled = false;
								removeClass('click_disabled', selectObject);
								removeClass('click_disabled', selectObject.parentNode);
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

        gStoreCode = gCollectFromStoreCode;

        searchText = "&country=" + countryCode + "&region=" + regionCode + "&storegroup=" + storeGroupCode + "&filter=" + encodeURIComponent(addressSearch) + "&privatefilter=" + gPrivateSearchText;
        if (gStoreCode != '')
        {
            searchText = searchText + "&store=" + gStoreCode;
        }
		gPrivateSearchText = origPrivateSearch;
        processAjax("storeListAjaxDiv",".?fsaction=AjaxAPI.callback&cmd={/literal}{$ajaxCommand}{literal}" + searchText, 'GET', '', searchForStore);
        document.getElementById('storeList').style.display = "block";
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

        {/literal}

        {if $issmallscreen == 'true'}

            {literal}
                var contentPanelMethodList = document.getElementById('contentPanelMethodList');
                contentPanelMethodList.getElementsByClassName('optionSelected')[0].getElementsByTagName('label')[0].getElementsByTagName('span')[0].innerHTML = '';
                if (!document.querySelector('input[name="store"]:checked'))
                {
                    gCollectFromStoreCode = '';
                }
				window.history.back();
				return true;
            {/literal}

        {else}

            {literal}

            alert("{/literal}{#str_ErrorNoStore#}{literal}");
            return false;

            {/literal}

        {/if}

        {literal}

        }
        else
        {

            var fields = '&storecode=' + storeCode + '&externalstore='+isExternalStore+'&country='+gSearchCountry + '&region='+gSearchRegion+'&storegroup='+gSearchStoreGroup + '&filter='+gSearchText +
            '&privatefilter='+gPrivateSearchText+'&payinstoreallowed='+gPayInStoreOption +
            '&stage={/literal}{$stage}&previousstage={$previousstage}&shippingratecode={$shippingratecode}$sameshippingandbillingaddress={$sameshippingandbillingaddress}{literal}';

        {/literal}

        {if $issmallscreen == 'true'}

            {literal}

            showLoadingDialog();
            document.getElementById('editStoreContactDetailsDiv').display = 'block';
            processAjaxSmallScreen("selectStore",".?fsaction=AjaxAPI.callback&cmd=SELECTSTORE" + fields, 'POST', '');

            {/literal}

        {else}

            {literal}

            var csrfToken = fetchCsrfToken();
            processAjax("selectStore",".?fsaction=Order.selectStore" + fields, 'POST', '');

            {/literal}

        {/if}

        {literal}

        }

        return false;
    }


    function resizeFormElement()
    {
        // make sure the html is loaded before calculate the size
        setTimeout(function()
        {
            //var searchInput = document.getElementById('searchText');
            //var searchContainer = searchInput.parentNode;

            //document.getElementById('searchText').style.width = searchContainer.offsetWidth + 'px';

            //searchInput.style.width = width + 'px';

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

    function addClass(pClassName, pElement)
    {
        {/literal}

        {if $issmallscreen == 'true'}

            {literal}

                var currentClassNames = pElement.className;

                if(currentClassNames.indexOf(pClassName) != -1)
                {
                    return false;
                }

                pElement.className = currentClassNames + ' ' + pClassName;

            {/literal}

        {/if}

        {literal}
    }

    function removeClass(pClassName, pElement)
    {

    {/literal}

        {if $issmallscreen == 'true'}

            {literal}

                var cn = pElement.className;
                var rxp = new RegExp( "\\s?\\b"+pClassName+"\\b", "g" );
                cn = cn.replace( rxp, '' );
                pElement.className = cn;

            {/literal}

        {/if}

    {literal}

    }

{/literal}