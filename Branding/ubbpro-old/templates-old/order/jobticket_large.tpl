<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname} - {$title}</title>
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
    <script type="text/javascript">
        //<![CDATA[

        {include file="order/jobticket.tpl"}

        {literal}

        function initialize()
        {
{/literal}

            {$custominit}

    {if (($showgiftcardmessage == 1) && ($stage == 'payment'))}

            displayGiftCardAlert('{$voucherstatusResult}', '{$vouchercustommessage}');
    {/if}


{literal}
            {/literal}{$initlanguage}{literal}

        }

		function resizePopup()
		{
			var storeLocator = document.getElementById('storeLocator');
			var storeInfo = document.getElementById('storeInfo');
			var ordersTermsAndCondtions = document.getElementById('ordersTermsAndCondtions');
			var componentChangeBox = document.getElementById('componentChangeBox');
			var shimObj = document.getElementById('shim');
			var windowHeight = document.documentElement.clientHeight;

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

        function changeShippingAddress()
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

        function shippingMethodCfsClick(code)
        {
			gChangeMethodInPorgress = true;

            document.getElementById('labelShippingStoreAddress').innerHTML = "{/literal}{#str_LabelStoreAddress#}{literal}";
            var storeaddress = gStoreAddresses[code];
            document.getElementById('shippingStoreAddress').innerHTML = storeaddress;

            var csButton = document.getElementById('changeShippingDiv');
            if (csButton)
            {
                csButton.style.display = 'none';
            }
            var csButton = document.getElementById('selectStoreDiv');
            if (csButton)
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
            document.getElementById('sameasshippingaddress').setAttribute("disabled","disabled");
            gCollectFromStore = 1;
            gCollectFromStoreCode = gStoreCodes[code];

            /* loop through all the shpping methods to see which one has been selected */
            for (var i = 0; i < document.getElementsByName('shippingmethods').length; i++)
            {
                if (document.getElementsByName('shippingmethods')[i].checked)
                {
                    var selectedShippingRateCode = document.getElementsByName('shippingmethods')[i].value;
                }
            }
            processAjax("cfschangeshippingmethod",".?fsaction=AjaxAPI.callback&cmd=CFSCHANGESHIPPINGMETHOD&ref=" + gSession + '&shippingratecode=' + selectedShippingRateCode, 'GET', '');
            return false;
        }

        function selectStore(radioButton)
        {
    {/literal}
    {$metadatasubmit}
    {literal}
            if (radioButton != '')
            {
                document.getElementById(radioButton).checked = true;
            }
            var shippingCode = getShippingRateCode();
            shippingMethodCfsClick(shippingCode);
            var sameshippingandbillingaddress = document.submitform.sameshippingandbillingaddress.value;
            processAjax('storeLocatorForm', ".?fsaction=Order.selectStoreDisplay&ref=" + gSession + '&stage={/literal}{$stage}{literal}&shippingratecode='+shippingCode+
                    '&sameshippingandbillingaddress=' + sameshippingandbillingaddress + '&previousstage={/literal}{$previousstage}{literal}'+
                    '&stage={/literal}{$stage}{literal}', 'GET', '');
            return false;
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

        function acceptTermsAndConditions()
		{
			if (document.getElementById('ordertermsandconditions').checked)
			{
				document.getElementById('ordercontinuebutton').onclick = acceptDataEntry;

				//document.getElementById('btn-confirm-left').className = 'btn-green-left';
				document.getElementById('btn-confirm-middle').className = 'btn-green-middle';
				//document.getElementById('btn-confirm-right').className = 'btn-accept-right';
			}
			else
			{
				document.getElementById('ordercontinuebutton').onclick = function () { return false; };

				//document.getElementById('btn-confirm-left').className = 'btn-disabled-left';
				document.getElementById('btn-confirm-middle').className = 'btn-disabled-middle';
				//document.getElementById('btn-confirm-right').className = 'btn-disabled-right-tick';
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
    <body onload="initialize();" style="position: relative" id="shoppingCart" onresize="resizePopup();">
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
                     <div class="contentBtn" onclick="closeTermsAndCondition();">
                         <div class="btn-green-left" ></div>
                         <div class="btn-accept-right"></div>
                     </div>
                 </div>
                 <div class="clear"></div>
             </div>
        </div>
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
                <div class="contentNavigationImage">
{if $stage=='qty'}
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
                    <div class="navigationBloc">
                        <div class="navigationInactiveLeft"></div>
                        <div class="clear"></div>
                    </div>
{/if}
{if $stage=='shipping'}
                    <div class="navigationLongBloc">
                        <div class="navigationActiveRight"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
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
{/if}
{if $stage=='payment'}
                    <div class="navigationLongBloc">
                        <div class="navigationActiveRight"></div>
                        <div class="navigationLineActive"></div>
                        <div class="clear"></div>
                    </div>
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
                <div class="contentNavigationText">
                    <div class="labelNavigation">{#str_LabelNavigationCart#}</div>
                    <div class="labelNavigation">{#str_LabelNavigationShippingBilling#}</div>
                    <div class="labelNavigation">{#str_LabelNavigationPayment#}</div>
                    <div class="labelNavigation">{#str_LabelNavigationConfirmation#}</div>
                    <div class="clear"></div>
                </div>
            </div>
            <div id="contentScroll" class="contentScrollCart">
{if $sidebarleft != ''}
            {include file="$sidebarleft"}
{/if}
                <div id="contentHolder">
                    <form id="orderform" name="orderform" method="post" action="#" onsubmit="return false;">
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
                                <div id="orderContent">
{if ($stage=='qty')||($stage=='payment')}
    <!-- order lines start -->
    {foreach from=$orderitems item=orderitem name=orderItemsLoop}
                                    {include file="$orderline" orderline=$orderitem}
    {/foreach}
    <!-- order lines end -->
                                    <!-- order footer start -->
                                    {include file="$orderfooter"}
                                    <!-- order footer end -->
{/if}
                                </div>
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
        {if $defaultdiscountactive==false}
            {if $vouchercode == ''}
                                            <input type="text" id="vouchercode" name="vouchercode" value="{#str_LabelVoucherCode#}" class="voucherinput falseLabelColor" {literal}onkeypress="if (enterKeyPressed(event)) {setVoucher(); return false;}"{/literal} onkeyup="return forceUpperAlphaNumeric(this);" onfocus="removeFalseLabel(this, '{#str_LabelVoucherCode#}');" onblur="addFalseLabel(this, '{#str_LabelVoucherCode#}');"/>
            {else}
                                            <input type="text" id="vouchercode" name="vouchercode" value="{$vouchercode}" readonly="readonly" class="voucherinput" {literal}onkeypress="if (enterKeyPressed(event)) {return false;}"{/literal} onfocus="removeFalseLabel(this, '{#str_LabelVoucherCode#}');" onblur="addFalseLabel(this, '{#str_LabelVoucherCode#}');"/>
            {/if}
        {else}
                                            <input type="text" id="vouchercode" name="vouchercode" value="{#str_LabelVoucherCode#}" class="voucherinput falseLabelColor" {literal}onKeyPress="if (enterKeyPressed(event)) {setVoucher(); return false;}"{/literal} onkeyup="return forceUpperAlphaNumeric(this);" onfocus="removeFalseLabel(this, '{#str_LabelVoucherCode#}');" onblur="addFalseLabel(this, '{#str_LabelVoucherCode#}');"/>
        {/if}

        {if $vouchercode == '' || ($vouchercode !='' && $defaultdiscountactive==true)}
                                            <div class="contentBtn" id="setvoucher" onclick="setVoucher();">
                                                <div class="btn-white-left" ></div>
                                                <div class="btn-white-middle">{#str_LabelRedeem#}</div>
                                                <div class="btn-white-right"></div>
                                            </div>
                                            <div class="clear"></div>
        {/if}
        {if $vouchercode != '' && $defaultdiscountactive==false}
                                            <div class="contentBtn" id="removevoucher" onclick="removeVoucher();">
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
                                            <input type="text" id="giftcardcode" name="giftcardcode" value="{#str_LabelGiftCardCode#}" class="voucherinput falseLabelColor" {literal}onkeypress="if (enterKeyPressed(event)) {setGiftCard(); return false;}"{/literal} onkeyup="return forceUpperAlphaNumeric(this);" onfocus="removeFalseLabel(this, '{#str_LabelGiftCardCode#}');" onblur="addFalseLabel(this, '{#str_LabelGiftCardCode#}');"/>
                                            <div class="contentBtn" id="setgiftcard" onclick="setGiftCard();">
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
                                                <span id="giftbutton" title="{$tooltipGiftcardButton}" class="button-voucher class_gift_{$add_delete_giftcard}" onclick="changeGiftCard()"></span>
                                                {#str_LabelGiftCard#}:
                                            </span>
                                            <span class="order-line-price-small" id="giftcardamount">
                                                {$ordergiftcardtotalvalue}
                                            </span>
                                            <div class="clear"></div>
                                        </div>
                                        <div id="giftcard-remain" class="line-sub-total-small {$disabled_giftcard}">
                                            <span class="total-heading">
                                                {#str_LabelGiftCardRemaining#}:
                                            </span>
                                            <span class="order-line-price-small" id="giftcardbalance">
                                                {$giftcardbalance}
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
									<div>
										<input type="checkbox" name="ordertermsandconditions" id="ordertermsandconditions" onclick="acceptTermsAndConditions()">
                                        <label for="ordertermsandconditions">{#str_LabelTermsAndConditionsAgreement#} <a id="ordertermsandconditionslink" href="#" onclick="orderTermsAndConditions();return false;" class="termsAndConditionsLink">{#str_TitleTermsAndConditions#}</a></label>
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
                                                        {$initialShippingStoreAddress}
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
                                                    <div class="contentBtn" id="changeshipping" onclick="changeShippingAddress();">
                                                        <div class="btn-white-left" ></div>
                                                        <div class="btn-white-middle">{#str_ButtonChange#}</div>
                                                        <div class="btn-white-right"></div>
                                                    </div>
                                                </div>
        {/if}
        {if $storeisfixed==0}
                                                <div id="selectStoreDiv" class="alignBottom" {if $collectFromStore==0}style="display:none"{/if}>
                                                    <div class="contentBtn" id="selectStoreButton" onclick="selectStore('');">
                                                        <div class="btn-white-left" ></div>
                                                        <div class="btn-white-middle">{#str_ButtonSelectStore#}</div>
                                                        <div class="btn-white-right"></div>
                                                    </div>
                                                </div>
        {/if}
    {/if}
                                                <div class="alignBottomRight">
                                                    <span id="sameasshippingaddressobj" {if (($canmodifyshipping==false)||($canmodifybilling==false))}style="display:none"{/if}>
                                                        <input type="checkbox" id="sameasshippingaddress" name="sameasshippingaddress" {if ($sameshippingandbillingaddress==true)}checked="checked"{/if} onclick="return setSameAddress();" {if ($collectFromStore==1)}disabled="disabled"{/if} />
                                                        <label for="sameasshippingaddress">
                                                            {#str_LabelSameAsShippingAddress#}
                                                        </label>
                                                    </span>
                                                    {if (($sameshippingandbillingaddress==true) && ($canmodifyshipping==true))}

                                                    <div class="contentBtn" id="changebilling" disabled="disabled" {if ($canmodifybilling==false)}style="display:none"{/if} onclick="changeBillingAddress();">
                                                        <div id="changeBillingBtnLeft" class="btn-disabled-left" ></div>
                                                        <div id="changeBillingBtnMiddle" class="btn-disabled-middle">{#str_ButtonChange#}</div>
                                                        <div id="changeBillingBtnRight" class="btn-disabled-right"></div>
                                                    </div>

                                                    {else}

                                                    <div class="contentBtn" id="changebilling" {if ($canmodifybilling==false)}style="display:none"{/if} onclick="changeBillingAddress();">
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
                        <div class="contentBtn" onclick="cancelOrder();">
                            <div class="btn-red-cross-left" ></div>
                            <div class="btn-red-middle">{#str_ButtonCancel#}</div>
                            <div class="btn-red-right"></div>
                        </div>
                        <div class="btnRight">
{if $stage ne 'qty'}
                            <div class="contentBtn" id="backButton" onclick="previousOrderStage();">
                                <div class="btn-blue-arrow-left" ></div>
                                <div class="btn-blue-middle">{#str_ButtonBack#}</div>
                                <div class="btn-blue-right"></div>
                            </div>
{/if}


                                {if $stage=='payment'}
                                    {if $showtermsandconditions == 1}
                                    	<div class="contentBtn" id="ordercontinuebutton">
                                    	<div id="btn-confirm-left" class="btn-disabled-left"></div>
                                    	<div id="btn-confirm-middle"class="btn-disabled-middle">{#str_ButtonConfirmOrder#}</div>
                                    	<div id="btn-confirm-right" class="btn-disabled-right-tick"></div>
                                    	</div>
                                    {else}
                                    	<div class="contentBtn" id="ordercontinuebutton" onclick="acceptDataEntry();">
                                    	<div id="btn-confirm-middle"class="btn-green-middle">{#str_ButtonConfirmOrder#}</div>
                                    	</div>
                                    {/if}
                                {else}
                                	<div class="contentBtn" id="ordercontinuebutton" onclick="acceptDataEntry();">
                                    
                                    <div class="btn-green-middle">{#str_ButtonContinue#}</div>
                                   
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
                             <div class="contentDotted">
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
                <input type="hidden" name="itemqty" value="{$itemqty}"/>
                <input type="hidden" name="sameshippingandbillingaddress" value=""/>
                <input type="hidden" name="shippingratecode" value=""/>
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
                {$metadataform}
            </form>
        </div>

{if $paymentscriptexternalurl != ''}

        <script type="text/javascript" src="{$paymentscriptexternalurl}"></script>

{/if}

{if $paymentform != ''}

        <script type="text/javascript">
            {$paymentform}
        </script>

{/if}

    </body>
</html>