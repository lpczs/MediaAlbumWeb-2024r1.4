<?php
/* Smarty version 4.5.3, created on 2026-03-14 01:34:28
  from 'C:\TAOPIX\MediaAlbumWeb\templates\order\jobticket.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69b4bb24e28d14_50187066',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b0a49341b4b6a079c8b5913e6f220b6dbfe8309f' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\order\\jobticket.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69b4bb24e28d14_50187066 (Smarty_Internal_Template $_smarty_tpl) {
?>        /* set a cookie to store the local time */
        var theDate = new Date();
        createCookie("mawebtz", Math.round(theDate.getTime() / 1000), 2);

        var gOrderStage = "<?php echo $_smarty_tpl->tpl_vars['currentstage']->value;?>
";

        var gVoucherSection = "<?php echo $_smarty_tpl->tpl_vars['vouchersection']->value;?>
";
        var discountName = "<?php echo $_smarty_tpl->tpl_vars['itemdiscountname']->value;?>
";
        var proceedAjax = 0;

        var gSession = "<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
";
        var gSSOToken = "<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
";
        var gLoadedComponentsImagesCount = 0;
        var gComponentImagesCount = 0;
		var gContinueOrderTimeout;
        var gRequestPaymentParamsRemotely = false;
        var gAjaxRunning = false;
        var gIsMobile = <?php echo $_smarty_tpl->tpl_vars['issmallscreen']->value;?>
;

        // cache load the spinner graphic since it is used on demand and needs to display as soon as the confirm button is pressed
        var spinnerCacheLoad = new Image();
        spinnerCacheLoad.src = "images/Waiting-Spinner.svg";

<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'companionselection') {?>

    <?php $_smarty_tpl->_assignInScope('modalHeight', '348' ,false ,8);?>
    <?php $_smarty_tpl->_assignInScope('modalWidth', '650' ,false ,8);?>

        var gOrderCanContinue = new Object();
        gOrderCanContinue.ordercancontinue = <?php echo $_smarty_tpl->tpl_vars['ordercancontinue']->value;?>
;
        var gOrderData = new Array();
        var gOrderComponentData = new Array();
        var gProductToUpdate = '';
        var gComponentToUpdate = '';
        var gCountProduct = 0;
        var gCountComponent = 0;
		var gAjaxInProgress = false;

<?php }?> 

<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

    <?php $_smarty_tpl->_assignInScope('modalHeight', '348' ,false ,8);?>
    <?php $_smarty_tpl->_assignInScope('modalWidth', '650' ,false ,8);?>

        var gOrderCanContinue = new Object();
        gOrderCanContinue.ordercancontinue = <?php echo $_smarty_tpl->tpl_vars['ordercancontinue']->value;?>
;
        var gOrderData = new Array();
        var gOrderComponentData = new Array();
        var gProductToUpdate = '';
        var gComponentToUpdate = '';
        var gCountProduct = 0;
        var gCountComponent = 0;
		var gAjaxInProgress = false;

<?php }?> 

<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'shipping') {?>

        var gShippingRateCode = "<?php echo $_smarty_tpl->tpl_vars['shippingratecode']->value;?>
";
        var gAddressesMatch = <?php echo $_smarty_tpl->tpl_vars['addressesmatch']->value;?>
;
        var gCollectFromStore = <?php echo $_smarty_tpl->tpl_vars['collectFromStore']->value;?>
;
        var gCollectFromStoreCode = "<?php echo $_smarty_tpl->tpl_vars['collectFromStoreCode']->value;?>
";
		var gChangeMethodInPorgress = false;
        var gStoreAddresses = new Object();
        var gStoreFixed = new Object();
        var gStoreCodes = new Object();

	<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['storeaddresses']->value, 'v', false, 'k', 'stores', array (
));
$_smarty_tpl->tpl_vars['v']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->do_else = false;
?>

        gStoreAddresses['<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
'] = "<?php echo $_smarty_tpl->tpl_vars['v']->value;?>
";

	<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
	<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['storefixedlist']->value, 'v', false, 'k', 'stores2', array (
));
$_smarty_tpl->tpl_vars['v']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->do_else = false;
?>

        gStoreFixed['<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
'] = '<?php echo $_smarty_tpl->tpl_vars['v']->value;?>
';

	<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
	<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['storecodelist']->value, 'v', false, 'k', 'stores3', array (
));
$_smarty_tpl->tpl_vars['v']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->do_else = false;
?>

        gStoreCodes['<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
'] = '<?php echo $_smarty_tpl->tpl_vars['v']->value;?>
';

	<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?> 
    document.addEventListener("DOMContentLoaded", function(event) {
        if (gCollectFromStoreCode != '') {
            if (document.getElementById('changeCollectionDetailsButton')) {
                document.getElementById('changeCollectionDetailsButton').setAttribute("data-decorator","fnChangeCollectionDetails");

                var btnElements = document.getElementById('changeCollectionDetailsButton').children;

                for (var i = 0; i < btnElements.length; i++)
                {
                    var oldClass = btnElements[i].className;
                    var newClass = oldClass.replace("-disabled-", "-white-");

                    btnElements[i].className = newClass;
                }   
            }
        }
    });

<?php }?> 
<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

        var gPaymentMethodCode = "<?php echo $_smarty_tpl->tpl_vars['paymentmethodcode']->value;?>
";
        var gCanUseAccount = <?php echo $_smarty_tpl->tpl_vars['canuseaccount']->value;?>
;

<?php }?> 


<?php if (($_smarty_tpl->tpl_vars['stage']->value == 'qty') || ($_smarty_tpl->tpl_vars['stage']->value == 'payment')) {?>

    

        function checkMetaData()
        {
            var componentHighLight = '';

            var elems = document.getElementsByTagName("*");
            var matches = [];
            var i = 0;
            for (i=0, m=elems.length; i < m; i++)
            {
                if (elems[i].id && elems[i].id.indexOf("keyword") != -1)
                {
                    matches.push(elems[i]);
                }
            }

            var radios = [];
            var selects = [];

            for (i=0; i < matches.length; i++)
            {
                if (matches[i].type=='radio')
                {
                    if( !(Object.prototype.toString.call( radios[matches[i].name] ) === '[object Array]' ))
                    {
                        radios[matches[i].name] = [];
                    }
                    radios[matches[i].name].push(matches[i]);
                }
                else if (matches[i].type=='select-one')
                {
                    if( !(Object.prototype.toString.call( selects[matches[i].name] ) === '[object Array]' ))
                    {
                        selects[matches[i].name] = [];
                    }
                    selects[matches[i].name].push(matches[i]);
                }
            }

            var defaultstillselected = [];

            for (radio in radios)
            {
                var add = true;
                for (var x=0; x < radios[radio].length; x++)
                {
                    if (radios[radio][x].checked)
                    {
                        add = false;
                    }
                }

                if (add)
                {
                    defaultstillselected.push(radios[radio]);
                }
            }

            for (select in selects)
            {
                var add = true;
                for (var x=0; x < selects[select].length; x++)
                {
                    if (selects[select][x].value!="")
                    {
                        add = false;
                    }
                }

                if (add)
                {
                    defaultstillselected.push(selects[select]);
                }
            }

            return !(defaultstillselected.length > 0);

        }

        var overElement = '';
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

        function showInfo(objId)
        {
            var elemObj = document.getElementById('description_component_' + objId);
            var iconObj = document.getElementById('img_info_' + objId);
            if (elemObj.style.display == 'block')
            {
                elemObj.style.display = 'none';
                iconObj.className = iconObj.className.replace(' activeIconeInfo', '');
            }
            else
            {
                elemObj.style.display = 'block';
                iconObj.className = iconObj.className + ' activeIconeInfo';
            }
        }

        function toggleWaitingSpinner()
        {
            var confirmButtonID = 'btn-confirm-right';
            
            if (gIsMobile)
            {
                confirmButtonID = 'btnContinueContentFinal';
            }

            var confirmButton = document.getElementById(confirmButtonID);
           
            var existingClass = confirmButton.className;
            
            if (existingClass.indexOf('waiting-spinner') == -1)
            {
                confirmButton.className += ' waiting-spinner';
            }
            else
            {
                confirmButton.className = confirmButton.className.replace(' waiting-spinner', '');
            }
        }

    

<?php }?> 
<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'companionselection') {?>
    
		var continueClicked = false;

        function acceptDataEntry()
        {
			var qtyInputFields = document.getElementsByClassName("companionQtyValue");
			var qtyFieldsCount = qtyInputFields.length;
			var allQuantitiesValid = true;

			for (i = 0; i < qtyFieldsCount; i++)
			{
				qtyInputFields[i].className = "companionQtyValue";
				var qty = string2integer(qtyInputFields[i].value);

				if (isNaN(qty) || (qty < 0))
				{
					allQuantitiesValid = false;
					qtyInputFields[i].className = "companionQtyValue error";
				}
			}

			if (allQuantitiesValid)
			{
				continueClicked = true;
				document.getElementById('ordercontinuebutton').setAttribute("disabled","disabled");
            	document.submitform.fsaction.value = "Order.continue";
				document.submitform.submit();
			}
			else
			{
				alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidQty');?>
");
			}

			return false;
		}

		function incrementCompanionQTY(pCompanionOptionCode, pTargetUniqueCompanionID, pParentOrderLineID)
		{
			if (! continueClicked)
			{
				var qtyIsDropDown =  document.getElementById("targetuniquecompanionid_" + pTargetUniqueCompanionID).getAttribute("data-companionqtyisdropdown");
				var qtyBoxValue =  document.getElementById('qty_' + pTargetUniqueCompanionID).value;

				if (qtyIsDropDown == 1)
				{
					var qtyDropDownRangesArray = document.getElementById("targetuniquecompanionid_" + pTargetUniqueCompanionID).getAttribute("data-qtydropdownvalues").split(',');
					var qtyRangeCount = qtyDropDownRangesArray.length;
					var currentQtyArrayIndex = qtyDropDownRangesArray.indexOf(qtyBoxValue);

					// if we are at the end of the ranges then we cannot add anymore so set the qty to the end limit
					// otherwise set the qty to the next range value in the array
					if (currentQtyArrayIndex == (qtyRangeCount - 1))
					{
						newQty = string2integer(qtyBoxValue);
					}
					else
					{
						newQty = string2integer(qtyDropDownRangesArray[currentQtyArrayIndex + 1]);
					}
				}
				else
				{
					newQty = string2integer(qtyBoxValue) + 1;
				}

				updateCompanionQty(pCompanionOptionCode, pTargetUniqueCompanionID, pParentOrderLineID, newQty);
			}
		}

		function decrementCompanionQTY(pCompanionOptionCode, pTargetUniqueCompanionID, pParentOrderLineID)
		{
			if (! continueClicked)
			{
				var qtyIsDropDown =  document.getElementById("targetuniquecompanionid_" + pTargetUniqueCompanionID).getAttribute("data-companionqtyisdropdown");
				var qtyBoxValue =  document.getElementById('qty_' + pTargetUniqueCompanionID).value;

				if (qtyIsDropDown == 1)
				{
					var qtyDropDownRangesArray = document.getElementById("targetuniquecompanionid_" + pTargetUniqueCompanionID).getAttribute("data-qtydropdownvalues").split(',');
					var qtyRangeCount = qtyDropDownRangesArray.length;
					var currentQtyArrayIndex = qtyDropDownRangesArray.indexOf(qtyBoxValue);

					// if we are at the start of the ranges then we cannot reduce the qty anymore so set the qty to 0 which will remove the item
					// otherwise set the qty to the previous range value in the array
					if (currentQtyArrayIndex == 0)
					{
						newQty = 0;
					}
					else
					{
						newQty = string2integer(qtyDropDownRangesArray[currentQtyArrayIndex - 1]);
					}
				}
				else
				{
					var lowestQtyValue = document.getElementById("targetuniquecompanionid_" + pTargetUniqueCompanionID).getAttribute("data-lowestqty");

					newQty = string2integer(qtyBoxValue) - 1;

					if (newQty < lowestQtyValue)
					{
						newQty = 0;
					}
				}

				updateCompanionQty(pCompanionOptionCode, pTargetUniqueCompanionID, pParentOrderLineID, newQty);
			}
		}

		function manualChangeCompanionQty(pCompanionOptionCode, pTargetUniqueCompanionID, pParentOrderLineID)
		{
			if (! continueClicked)
			{
				var validQty = true;

				var qtyIsDropDown =  document.getElementById("targetuniquecompanionid_" + pTargetUniqueCompanionID).getAttribute("data-companionqtyisdropdown");
				var qtyBoxValue =  document.getElementById('qty_' + pTargetUniqueCompanionID).value;

				var newQty = string2integer(qtyBoxValue);

				if (isNaN(newQty) || (newQty < 0))
				{
					validQty = false;
				}

				if (validQty)
				{
					if (qtyIsDropDown == 1)
					{
						var qtyDropDownRangesArray = document.getElementById("targetuniquecompanionid_" + pTargetUniqueCompanionID).getAttribute("data-qtydropdownvalues").split(',');
						var qtyRangeCount = qtyDropDownRangesArray.length;
						var lastQtyRangeValue = qtyDropDownRangesArray[qtyRangeCount - 1];

						// first check to see if the value entered is 0. If it is then we can use 0 which will remove the companion.
						// we then need to check to see if the value entered is actually a value already in the ranges. If it is then we can use that qty value.
						// we must then check to see if the value entered is higher than the last qty range value. If it is then use the last range value.
						// if the value entered is not in the array then we must work out which value we should use in the given ranges. We try to find the highest matching
						// range. For example if the ranges are 5, 10, 15 and the user enters 11 we must set the qty to 15. Or if they enters 4 we would use 5.
						if (string2integer(qtyBoxValue) == 0)
						{
							newQty = 0;
						}
						else if (qtyDropDownRangesArray.indexOf(qtyBoxValue) != -1)
						{
							newQty = string2integer(qtyBoxValue);
						}
						else if (string2integer(qtyBoxValue) > string2integer(lastQtyRangeValue))
						{
							newQty = string2integer(lastQtyRangeValue);
						}
						else
						{

							for (i = 0; i < qtyRangeCount; i++)
							{
								if (string2integer(qtyBoxValue) < string2integer(qtyDropDownRangesArray[i]))
								{
									newQty = string2integer(qtyDropDownRangesArray[i]);
									break;
								}
							}
						}
					}
				}

				if (validQty)
				{
					document.getElementById('qty_' + pTargetUniqueCompanionID).className = "companionQtyValue";
					updateCompanionQty(pCompanionOptionCode, pTargetUniqueCompanionID, pParentOrderLineID, newQty);
				}
				else
				{
					document.getElementById('qty_' + pTargetUniqueCompanionID).className = "companionQtyValue error";
					alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidQty');?>
");
				}
			}
		}

		function updateCompanionQty(pCompanionOptionCode, pTargetUniqueCompanionID, pParentOrderLineID, pNewQty)
		{
			var obj = 'updatecompanionqty';
			var serverPage = '.?fsaction=AjaxAPI.callback&cmd=UPDATECOMPANIONQTY';

			var companionOrderLineID = document.getElementById("targetuniquecompanionid_" + pTargetUniqueCompanionID).getAttribute("data-companionorderlineid");

			var postParams = [];
			postParams.push('companioncode=' + pCompanionOptionCode);
			postParams.push('targetuniquecompanionid=' + pTargetUniqueCompanionID);
			postParams.push('parentorderlineid=' + pParentOrderLineID);
			postParams.push('companionorderlineid=' + companionOrderLineID);
			postParams.push('qtytoadd=' + pNewQty);

			var params = postParams.join('&');

            processAjax(obj, serverPage, 'POST', params);
		}

		function setCompanionQty(pResponse)
		{
			var companionID = pResponse.targetuniquecompanionid;
			var companionOrderLineID = document.getElementById("targetuniquecompanionid_" + companionID);
			companionOrderLineID.setAttribute('data-companionorderlineid', pResponse.companionorderlineid);

			// create the element names based on the id passed
			var addSectionID = 'addBtnContainer_' + companionID;
			var qtyInputID = 'qty_' + companionID;
			var setQtySectionID = 'setQtyContainer_' + companionID;
			var inCartID = 'inCart_' + companionID;
			var inCartString = '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompanionInCart');?>
';

			// link to the elements
			var addSection = document.getElementById(addSectionID);
			var setQtySection = document.getElementById(setQtySectionID);
			var inCartIDSection = document.getElementById(inCartID);
			var qtyInputIDSection = document.getElementById(qtyInputID);

			// set the values in the message and qty input
			qtyInputIDSection.value = pResponse.qty;
			inCartIDSection.innerHTML = inCartString.replace('^0', pResponse.qty);

			if (pResponse.qty === 0)
			{
				// hide the qty edit section, show the add button
				addSection.style.display = 'block';
				setQtySection.style.display = 'none';
				document.getElementById('qty_' + companionID).classList.remove('error');
			}
			else
			{
				if ((setQtySection.offsetWidth === 0) && (setQtySection.offsetHeight === 0))
				{
					// hide the add button, show the qty edit section
					addSection.style.display = 'none';
					setQtySection.style.display = 'block';
				}
			}

            document.getElementById('ordersummarypanel').innerHTML = pResponse.htmlCartSummary;


		}

	

<?php }?>

<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

    

        function acceptDataEntry(alertOff)
        {
	

	<?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value != 'true') {?>

		

			if (gAjaxInProgress == true)
			{
				alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageOptionsOrItemChanged');?>
");
				return false;
			}

		

	<?php }?>

    <?php if ($_smarty_tpl->tpl_vars['lockqty']->value != true) {?>

        

            var updated = false;
            /* check to see if quantity has changes */
            /* loop over global order data array */
            if (document.getElementsByClassName)
            {
                var aInput = document.getElementsByClassName("hiddeqty");
                for (i = 0; i < aInput.length; i++)
                {
                    gOrderData[aInput[i].id] = aInput[i].value;
                };
            }
            else
            {
                i = 0;
                aInput = document.getElementsByTagName("input");
                while (element = aInput[i++])
                {
                    if (element.className == "hiddeqty")
                    {
                        gOrderData[aInput[i-1].id] = aInput[i-1].value;
                    }
                }
            }

            for (var idx in gOrderData)
            {
                var field = document.getElementById(idx.replace('hiddeqty', 'itemqty'));
                if( field)
                {
                    if ((field.type == 'text') || (field.type == 'number'))
                    {
                        var newQty = string2integer(field.value);
                    }
                    else
                    {
                        var newQty = string2integer(field.options[field.selectedIndex].value);
                    }

                    if (newQty != gOrderData[idx])
                    {
                        /* show error message only once */
                        if (!updated)
                        {

            

            <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

                

                            createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInformation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageQuantityChanged');?>
", function(e) {
                                closeDialog(e);
                            });

                

            <?php } else { ?>

                

                            alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageQuantityChanged');?>
");

                

            <?php }?>

            

                            updated = true;
                        }
                        updateOrderQty(idx.replace('hiddeqty_', ''), newQty, false);
                    }
                }
            }

            if (document.getElementsByClassName) {

                var aInput = document.getElementsByClassName("hiddeqtyCpt");
                for (i = 0; i < aInput.length; i++)
                {
                    gOrderComponentData[aInput[i].id] = aInput[i].value;
                }

            } else {

                i = 0;
                aInput = document.getElementsByTagName("input");
                while (element = aInput[i++])
                {
                    if (element.className == "hiddeqtyCpt")
                    {
                        gOrderComponentData[aInput[i-1].id] = aInput[i-1].value;
                    }
                }

            }

            for (var idx in gOrderComponentData)
            {
                if (idx != -1)
                {
                    /* has it changed? */
                    var field = document.getElementById(idx.replace('hiddeqty', 'itemqty'));
                    if( field)
                    {
                        if ((field.type == 'text') || (field.type == 'number'))
                        {
                            var newQty = string2integer(field.value);
                        }
                        else
                        {
                            var newQty = string2integer(field.options[field.selectedIndex].value);
                        }
                        if (newQty != gOrderComponentData[idx])
                        {
                            /* show error message only once */
                            if (!updated)
                            {

                                

                                <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

                                    

                                createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInformation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageQuantityChanged');?>
", function(e) {
                                    closeDialog(e);
                                });

                                    

                                <?php } else { ?>

                                    

                                alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageQuantityChanged');?>
");

                                    

                                <?php }?>

                                

                                updated = true;
                            }
                            proceedAjax++;
                            componentOrderLineID = idx.replace('hiddeqty_', '');
                            orderLinePrefix = componentOrderLineID.substr(0,1);
                            if (orderLinePrefix != '-')
                            {
                                aId = idx.split('_');
                                updateComponentQty(componentOrderLineID, document.getElementById('itemqty_' + aId[1]).value, newQty, false);
                            }
                            else
                            {
                                updateComponentQty(componentOrderLineID, 0, newQty, false);
                            }
                        }
                    }
                }
            }

            if (updated)
            {
                /* temp save metadata */
                saveTempMetadata();

        

                <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>


                <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'false') {?>

                    

            	postParams = '&product=[' + gProductToUpdate + ']';
                postParams += '&component=[' + gComponentToUpdate + ']';
                processAjax('updateorderqtyall',".?fsaction=AjaxAPI.callback&cmd=UPDATEORDERQTYALLLARGE", 'POST', postParams);


                    

                <?php } else { ?> 
                    

                showLoadingDialog();

            	postParams = '&product=[' + gProductToUpdate + ']';
                postParams += '&component=[' + gComponentToUpdate + ']';
                processAjaxSmallScreen("updateorderqtyall",".?fsaction=AjaxAPI.callback&cmd=UPDATEORDERQTYALLSMALL", 'POST', postParams);

                    

                <?php }?>

        

                gProductToUpdate = '';
                gComponentToUpdate = '';
                return false;
            }

        

    <?php }?> 
    

            /* test if all order items have a price */
            /* loop over global order data array */
            for (var idx in gOrderData)
            {
                if (idx != -1)
                {
                    /* is there a product price? */
                    if (gOrderData[idx].hasproductprice == "0")
                    {

    

    <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

        

                        createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoPrice');?>
", function(e) {
                            closeDialog(e);
                        });

        

    <?php } else { ?>

        

                        alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoPrice');?>
");

        

    <?php }?>

    

                        return false;
                    }
                }
            }

            /* test if all required components have a price */
            if (gOrderCanContinue.ordercancontinue == 0)
            {

    

    <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

        

                createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoComponent');?>
", function(e) {
                    closeDialog(e);
                });

        

    <?php } else { ?>

        

                alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoComponent');?>
");

        

    <?php }?>

    

                return false;
            }

    

    <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

        

            var metadataValidity = checkMetadataValidity('contentAjaxQty', true);
            if (metadataValidity.hasAnError == false)
            {
                // open the loading box
                showLoadingDialog();

                setHashUrl('shipping');
            }

        

    <?php } else { ?> 
        <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>


        

            //Limits the number of times the alert is called i.e. once for one or more empty meta entries
            var alertOn = true;

			// auto expand the hidden components which require completing
            var showHidden = true;

            //If come from onblur event then turn the error messaging off.
            if(alertOff == false)
            {
                alertOn = false;

				// prevent auto expand any hidden sections
				showHidden = false;
            }

            //Allows the form submitting if required meta data is present.
            var goToSubmit = true;

            /* on submit get all metadata fields and submit to the server */
            var orderContentHolder = document.getElementById('orderContent');
            if (orderContentHolder)
            {
                var lis = orderContentHolder.getElementsByTagName('div');

                var liCount = lis.length;
                var submitForm = document.getElementById('submitform');
                for (var i = 0, inputs, selects, textareas, hiddenField, li; i < liCount; i++)
                {
                    li = lis[i];

                    if (li.className.indexOf('component-metadata') > -1)
                    {
                        inputs = li.getElementsByTagName('input');
                        selects = li.getElementsByTagName('select');
                        textareas = li.getElementsByTagName('textarea');

                        /* get metadata values for all inputs on the page */
                        for (var j = 0, hiddenFieldValue; j < inputs.length; j++)
                        {
                            switch (inputs[j].type)
                            {
                                case 'text':

                                    //Get the input and the grandparent node
                                    var txtInput = inputs[j];
                                    var grandDiv = ((txtInput.parentNode).parentNode).parentNode;

                                    //Get the current style of the input and the grandparent node
                                    var txtInputStyle = txtInput.currentStyle || window.getComputedStyle(txtInput);
                                    var grandStyle = grandDiv.currentStyle || window.getComputedStyle(grandDiv);

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
                                        goToSubmit = false;
                                        if(alertOn)
                                        {
                                            alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorValueRequired');?>
");
                                            alertOn = false;
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
										else
										{
											// only show the hidden elements on continue, not on blur
											if (showHidden)
											{
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
											}
										}


                                        //Make the text box a little smaller if necessary
                                        if((parseInt(txtInputStyle.width)) >= 750)
                                        {
                                            var mySize = (parseInt(txtInputStyle.width)-20)+'px';
                                            txtInput.setAttribute("style","width:"+mySize);
                                        }
                                    }

                                    hiddenField = document.createElement('input');
                                    hiddenField.setAttribute('name', inputs[j].name);
                                    hiddenField.setAttribute('type', 'hidden');
                                    hiddenField.setAttribute('value', inputs[j].value);
                                    submitForm.appendChild(hiddenField);

                                    break;

                               case 'radio':
                                    //Need to know the grand parent of the radio input
                                    var radioGrandParent = (inputs[j].parentNode).parentNode;
                                    var grandDiv = ((inputs[j].parentNode).parentNode).parentNode;
                                    var radioInputs = radioGrandParent.getElementsByTagName('input')
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
										if (inputs[j].checked)
                                    	{
											hiddenField = document.createElement('input');
											hiddenField.setAttribute('name', inputs[j].name);
											hiddenField.setAttribute('type', 'hidden');
											hiddenField.setAttribute('value', inputs[j].value);
											submitForm.appendChild(hiddenField);
										}
                                        //Removes the red box around the completed divs
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
                                        goToSubmit = false;

                                        if(alertOn)
                                        {
                                            alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorValueRequired');?>
");
                                            alertOn = false;
                                            window.scrollTo(0,0);
                                        }

                                        var txtInput = inputs[j];

                                        var styleContentScrollCart = txtInput.currentStyle || window.getComputedStyle(txtInput);

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

                                            //If at least one input is checked then radioChecker = true
                                            for(var k = 0; k < radioInputs.length; k++)
                                            {
                                                radioInputs[k].onchange = function() {
                                                    acceptDataEntry(false);
                                                };
                                            }
                                        }
										else
										{
											// only show the hidden elements on continue, not on blur
											if (showHidden)
											{
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
											}
										}
                                    }
                                    break;

                                case 'checkbox':
                                    hiddenField = document.createElement('input');
                                    hiddenField.setAttribute('name', inputs[j].name);
                                    hiddenField.setAttribute('type', 'hidden');
                                    hiddenField.setAttribute('value', (inputs[j].checked) ? '1' : '0');
                                    submitForm.appendChild(hiddenField);
                                    break;
                            }
                        }

                        /* get metadata values for all selects on the page */
                        for (var j = 0, hiddenFieldValue; j < selects.length; j++)
                        {
                            var txtInput = selects[j];
                            var grandDiv = ((txtInput.parentNode).parentNode).parentNode;

                            //Check to see if any option has been selected
                            if(selects[j].options[selects[j].selectedIndex].value.length >0)
                            {

                                hiddenField = document.createElement('input');
                                hiddenField.setAttribute('name', selects[j].name);
                                hiddenField.setAttribute('type', 'hidden');
                                hiddenField.setAttribute('value', selects[j].options[selects[j].selectedIndex].value);
                                submitForm.appendChild(hiddenField);

                                //Removes the red box around the completed divs
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
                                goToSubmit = false;

                                if(alertOn)
                                {
                                    alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorValueRequired');?>
");
                                    alertOn = false;
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
								else
								{
									// only show the hidden elements on continue, not on blur
									if (showHidden)
									{
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
									}
								}
                            }
                        }

                        /* get metadata values for all textareas on the page */
                        for (var j = 0, hiddenFieldValue; j < textareas.length; j++)
                        {
                            var txtInput = textareas[j];
                            var grandDiv = ((txtInput.parentNode).parentNode).parentNode;

                            //Get the current style of the input and the grandparent node
                            var styleContentScrollCart = txtInput.currentStyle || window.getComputedStyle(txtInput);
                            var grandStyle = grandDiv.currentStyle || window.getComputedStyle(grandDiv);

                            //Checks if the value of the input has been set, if so then unhighlight the element.
                            if ((textareas[j].className.indexOf('required') > -1) && (textareas[j].value != ''))
                            {
                                //Removes the red box around the completed divs
                                //Check if the class 'metadata-Highlighted' has been assigned to it. If so then remove the red box.
                                var classStringTemp = String (grandDiv.className);
                                var classStringTemp2 = classStringTemp.split(/\s/);
                                var classStringTemp3 = classStringTemp2.indexOf('metadata-Highlighted');

                                if(classStringTemp3 >=0)
                                {
                                    var classContent = grandDiv.className;
                                    grandDiv.className = classContent.replace('metadata-Highlighted', "").trim();
                                }

								// reset width
								txtInput.setAttribute("style","");
                            }
                            else if(textareas[j].className.indexOf('required') > -1)
                            {
                                goToSubmit = false;

                                if(alertOn)
                                {
                                    alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorValueRequired');?>
");
                                    alertOn = false;
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
								else
								{
									// only show the hidden elements on continue, not on blur
									if (showHidden)
									{
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
									}
								}

                                //Make the text box a little smaller
                                if(parseInt(grandStyle.width) <= parseInt(styleContentScrollCart.width))
                                {
                                    //Make the text box a little smaller;
                                    var mySize = (parseInt(grandStyle.width)-10)+'px';
                                    txtInput.setAttribute("style","width:"+mySize);
                                }
                            }
                            hiddenField = document.createElement('input');
                            hiddenField.setAttribute('name', textareas[j].name);
                            hiddenField.setAttribute('type', 'hidden');
                            hiddenField.setAttribute('value', textareas[j].value);
                            submitForm.appendChild(hiddenField);
                        }
                    }
                }
            }

            if(goToSubmit && alertOff != false)
            {
                document.submitform.itemqty.value = "";
                document.submitform.fsaction.value = "Order.continue";
                document.submitform.submit();
            }

        

    <?php }?> 
    

            return false;
        }


        /* change a component in the session by Ajax   */
        /* component has been selected from drop-down */
        /* then refresh the entire orderline by Ajax   */
        function updateComponent(orderlineid, section, sectioncode)
        {
            var componentCode = "";
            var selectionId = 'component_dropdown_'+section;
            var selectionObj = document.getElementById(selectionId);

            if (selectionObj)
            {
                var componentCode = selectionObj.value;
            }

            if (componentCode == "")
            {
                alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoComponent2');?>
");
                return false;
            }

            /* temp save metadata */
            saveTempMetadata();

    

    <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>


    

            /* refresh order line */
            processAjax("ordertableobj_" + orderlineid,".?fsaction=AjaxAPI.callback&cmd=UPDATECOMPONENTLARGE&orderlineid=" + orderlineid + "&section=" + sectioncode + "&code=" + componentCode, 'POST', '');
            closeWindow();
            return false;
        }

        function updateOrderQty(orderLineId, quantityValue, processExecute)
        {
            if (document.submitform.itemqty.value != "-")
            {
                var validQty = true;
                if (quantityValue == undefined)
                {
                   var newQty = string2integer(document.getElementById('itemqty_' + orderLineId).value);
                }
                else
                {
                    var newQty = string2integer(quantityValue);
                }

                if (isNaN(newQty))
                {
                    validQty = false;
                }
                else if ((newQty < 1) || (newQty > 99999999))
                {
                    validQty = false;
                }

                if (validQty)
                {
                    if( processExecute == undefined)
                    {
                        /* temp save metadata */
                        saveTempMetadata();
        

        <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

            

                        gOrderlineidActive = orderLineId;

                        if (gActivePanel == '')
                        {
                            gScrollRefreshPosition = document.getElementById('contentLeftScrollQty').scrollTop;
                        }
                        else
                        {
                            gScrollRefreshPosition = document.getElementById('contentRightScrollComponentDetail_' + gOrderlineidActive).scrollTop;
                        }

                        processAjaxSmallScreen("updateQty",".?fsaction=AjaxAPI.callback&cmd=UPDATEQTYSMALL&orderlineid=" + orderLineId + "&itemqty=" + newQty, 'POST', '');

            

        <?php } else { ?> 
            <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>


            
                        processAjax("ordertableobj_" + orderLineId,".?fsaction=AjaxAPI.callback&cmd=UPDATEQTYLARGE&orderlineid=" + orderLineId + "&itemqty=" + newQty, 'POST', '');

            

        <?php }?> 
        

                    }
                    else
                    {
                    	if( gProductToUpdate != '')
                    	{
                        	 gProductToUpdate += ',';
                        }
                        gProductToUpdate += '{"content":"' + 'ordertableobj_' + orderLineId + '",' + '"orderline":"' + orderLineId + '","qty":' + newQty + '}';
                    }
                }
                else
                {

        

        <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

            

                    createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidQty');?>
", function(e) {
                        closeDialog(e);
                    });

            

        <?php } else { ?> 
            

                    alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidQty');?>
");

            

        <?php }?> 
        

                }
            }
            else
            {

        

        <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

            

                    createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoPrice');?>
", function(e) {
                        closeDialog(e);
                    });

            

        <?php } else { ?> 
            

                    alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoPrice');?>
");

            

        <?php }?> 
        

            }
            return false;
        }

        function onKeyPressUpdateComponentQty(event, componentOrderLineID, prodQty, qtyValue)
        {
            if (enterKeyPressed(event))
            {
                updateComponentQty(componentOrderLineID, prodQty, qtyValue);
            }
        }

        // Update component action function
        function fnChangeComponent(pElement)
        {
            return changeComponent(pElement.getAttribute("data-orderlineid"), pElement.getAttribute("data-sectionlineid"));
        };

        // Update quantity input function.
        function fnUpdateOrderQty(pElement, pEvent)
        {
            var lineItemID = pElement.getAttribute("data-lineid");

            switch (pEvent.type)
            {
                case 'change':
                  // Select box.


                  // Change event can be fired by input boxes so make sure it's a select box.
                  if (pElement.tagName === 'SELECT')
                  {
                    updateOrderQty(lineItemID, pElement.options[pElement.selectedIndex].value);
                  }
                  break;
                case 'keypress': 
                case 'keyup':
                  // Input box.

                  if (enterKeyPressed(pEvent))
                  {
                      updateOrderQty(lineItemID, pElement.value);
                  }

                  break;
                case 'click': 
                  if (pElement.tagName === 'IMG')
                  {
                    // Refresh image clicked.
                    updateOrderQty(lineItemID, document.getElementById('itemqty_' + lineItemID).value);
                  }
                  break;
            }

            return false;
        }

        // Update the component quantity
        function fnUpdateComponentQty(pElement, pEvent)
        {
            var lineItemID = pElement.getAttribute("data-lineid");
            var lineItemQty = pElement.getAttribute("data-itemqty");

            switch (pElement.getAttribute("data-trigger"))
            {
                case 'change': // Select
                    updateComponentQty(lineItemID, lineItemQty, pElement.options[pElement.selectedIndex].value);

                    break;

                case 'keyup': // Input box
                case 'keypress': // Input box
                    if (enterKeyPressed(pEvent))
                    {
                        updateComponentQty(lineItemID, lineItemQty, pElement.value);
                    }

                    break;

                case 'click': // Refresh image clicked
                    updateComponentQty(lineItemID, lineItemQty, document.getElementById('itemqty_' + lineItemID).value);

                    break;
            }

            return false;
        }

    

<?php }?> 
<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

    

        function displayGiftCardAlert(giftCardResult, customMessage)
        {
            var message = '';
            var isAnAlert = true;
            switch(giftCardResult)
            {
                case 'str_LabelGiftCardAccepted':
                {
                    message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelGiftCardAccepted');?>
";
                    isAnAlert = false;
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

            

                if (isAnAlert)
                {
                    showAlertBar(message);
                }
                else
                {
                    showLoadingNotificationBar(message);
                }

            

        <?php } else { ?>

            

                    alert(message);

            

        <?php }?>

        

            }

        }

        function acceptDataEntry(alertOff)
        {
            if (gContinueOrderTimeout != undefined)
			{
				clearTimeout(gContinueOrderTimeout);
			}
        

            <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'false') {?>

                

            //Limits the number of times the alert is called i.e. once for one or more empty meta entries
            var alertOn = true;

            //If come from onblur event then turn the error messaging off.
            if(alertOff == false)
            {
                alertOn = false;
            }

            //Allows the form submitting if required meta data is present.
            var goToSubmit = true;

            document.submitform.fsaction.value = "Order.continue";

            // check if all metadata fields are validated  
            if(! validateOrderMetaData(alertOn))
            {
                return false;
            }

            if(alertOff == false)
            {
                return false;
            }

                

                <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>


            <?php }?> 
        

            toggleWaitingSpinner();

            if (gPaymentMethodCode != "NONE")
            {
                var paymentMethodCode = getPaymentMethodCode();

                if (paymentMethodCode.length == 0)
                {

        

        <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

            

                    createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoPaymentMethod');?>
", function(e) {
                        closeDialog(e);
                    });

            

        <?php } else { ?>

            

                    alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoPaymentMethod');?>
");

            

        <?php }?>

        
                    // if there is an error we must turn off the waiting spinner on the confirm order button
                    toggleWaitingSpinner();
                    
                    return false;
                }

                if ((gCanUseAccount == false) && (paymentMethodCode == "ACCOUNT"))
                {

        

        <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

            

                    createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorOverCreditLimit');?>
", function(e){
                        closeDialog(e);
                    });

            

        <?php } else { ?>

            

                    alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorOverCreditLimit');?>
");

            

        <?php }?>

        
                    // if there is an error we must turn off the waiting spinner on the confirm order button
                    toggleWaitingSpinner();

                    return false;
                }

                document.submitform.paymentmethodcode.value = paymentMethodCode;
                document.submitform.paymentgatewaycode.value = getPaymentGatewayCode();

                var paymentMethodAction = getPaymentMethodAction();

                if (paymentMethodAction != '')
                {
                    // paymentMethodAction is a function, this needs to be ran so break down the string that is given as a function name and its argument.
					if (paymentMethodAction.indexOf('validatePayType') !== -1)
					{
						var message = paymentMethodAction.replace("validatePayType('", "").replace("')", "");

						validatePayType(message);
					}
					else if (paymentMethodAction.indexOf('validatePaymentMethod') !== -1)
					{
						validatePaymentMethod();
					}
                }
                else
                {

    

        <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

        

                    var metadataValidity = checkMetadataValidity('contentPanelPayment', true);
                    if (metadataValidity.hasAnError == false)
                    {
                        // open the loading box
                        showLoadingDialog();

                        var postParams = metadataValidity.postParams;

                        postParams += '&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
';
                        postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';
                        postParams += '&paymentmethodcode=' + paymentMethodCode;
                        postParams += '&paymentgatewaycode=' + getPaymentGatewayCode();


                        if ((paymentMethodCode == 'CARD') || (paymentMethodCode == 'PAYPAL') || (paymentMethodCode == 'KLARNA'))
                        {
                            processAjaxSmallScreen('showPaymentgateway', ".?fsaction=AjaxAPI.callback&cmd=ORDERCONTINUE", 'POST', postParams);
                        }
                        else
                        {
                            processAjaxSmallScreen('showConfirmation', ".?fsaction=AjaxAPI.callback&cmd=ORDERCONTINUE", 'POST', postParams);
                        }
                    }
                    else
                    {
                        // if there is an error we must turn off the waiting spinner on the confirm order button
                        toggleWaitingSpinner();
                    }

        

    <?php } else { ?> 

        

			showLoadingDialog('<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageLoading');?>
');

			// disable the continue order on the final stage to prevent multi clicks/orders
			document.getElementById('ordercontinuebutton').onclick = function() { return false; };
			document.getElementById('btn-confirm-left').className = 'btn-disabled-left';
			document.getElementById('btn-confirm-middle').className = 'btn-disabled-middle';
			document.getElementById('btn-confirm-right').className = 'btn-disabled-right-tick';

            document.submitform.submit();
        

    <?php }?> 
                }

    

            }
            else
            {

    

    <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

        

                var metadataValidity = checkMetadataValidity('contentPanelPayment', true);
                if (metadataValidity.hasAnError == false)
                {
                    // open the loading box
                    showLoadingDialog();

                    var postParams = metadataValidity.postParams;

                    postParams += '&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
';
                    postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';
                    postParams += '&paymentmethodcode=' + gPaymentMethodCode;
                    postParams += '&paymentgatewaycode=' + getPaymentGatewayCode();

                    processAjaxSmallScreen('showConfirmation', ".?fsaction=AjaxAPI.callback&cmd=ORDERCONTINUE", 'POST', postParams);
                }
                else
                {
                    // if there is an error we must turn off the waiting spinner on the confirm order button
                    toggleWaitingSpinner();
                }

        

    <?php } else { ?> 
        <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>


        
                document.getElementById('ordercontinuebutton').setAttribute("disabled","disabled");
                document.submitform.paymentmethodcode.value = gPaymentMethodCode;
                document.submitform.paymentgatewaycode.value = getPaymentGatewayCode();
                document.submitform.submit();
         

    <?php }?> 
    

            }
            return false;
        }

        function getPaymentMethodCode()
        {
            var code = getPaymentMethodCodeRaw();
            if (code.substr(0,5) == "CARD_")
            {
                code = "CARD";
            }
            return code;
        }

        function getPaymentGatewayCode()
        {
            var code = getPaymentMethodCodeRaw();
            if (code.substr(0,5) == "CARD_")
            {
                code = code.substr(5,code.length);
            }
            else
            {
                code = "";
            }
            return code;
        }

        function forceSelectCard()
		{
			var radio = document.getElementsByName('paymentmethods');
			for(var i =0;i < radio.length; i++)
			{
				if(radio[i].value == 'CARD')
				{
					radio[i].click();
				}
			}
		}

        function payType(name, id)
        {
            this.name = name;
            this.id = id;
        }

    

<?php }?> 


        /*A J A X */

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

        function getScrollingPosition()
        {
            var position = [0, 0];
            if (typeof window.pageYOffset != 'undefined')
            {
                position = [
                    window.pageXOffset,
                    window.pageYOffset
                ];
            }
            else if (typeof document.documentElement.scrollTop != 'undefined' && document.documentElement.scrollTop > 0)
            {
                position = [
                    document.documentElement.scrollLeft,
                    document.documentElement.scrollTop
                ];
            }
            else if (typeof document.body.scrollTop != 'undefined')
            {
                position = [
                    document.body.scrollLeft,
                    document.body.scrollTop
                ];
            }

            return position;
        }

        /* function to process an XMLHttpRequest */
        function processAjax(obj, serverPage, requestMethod, params, pCallback)
        {
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
            var xmlhttp = getxmlhttp();
            if (requestMethod == 'GET')
            {
                xmlhttp.open("GET", serverPage+"&dummy=" + new Date().getTime(), true);
            }
            else
            {
                xmlhttp.open("POST", serverPage, false);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            }

            if (requestMethod == 'GET')
            {
                xmlhttp.onreadystatechange = function()
                {
                    if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
                    {
                        switch (obj)
                        {
                            case 'topmost':
                                document.getElementById(obj).innerHTML = xmlhttp.responseText;
                                break;
                            case 'componentChangeBox':


<?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

    
                                document.getElementById(obj).innerHTML = xmlhttp.responseText;
                                var contentWrapperObj = document.getElementById('outerPage');
                                var shimObj = document.getElementById('shim');
                                var componentChangeBoxObj = document.getElementById('componentChangeBox');
                                shimObj.style.display = 'block';
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
                                componentChangeBoxObj.style.display = 'block';
                                if (contentWrapperObj)
                                {
									sizeDialog(componentChangeBoxObj, 'previewHolderCartHolder');
                                    componentChangeBoxObj.style.left = Math.round((shimObj.offsetWidth / 2) - (<?php echo $_smarty_tpl->tpl_vars['modalWidth']->value;?>
/2)) + 'px';
                                    windowHeight = document.documentElement.clientHeight;
                                    finalPosition = (windowHeight - componentChangeBoxObj.offsetHeight) / 2;
                                    componentChangeBoxObj.style.top = Math.round(finalPosition) + 'px';
                                }
                                changeComponentImageLoaded();
    

<?php }?> 

                                break;
                            case 'storeLocatorForm':
                                var response = xmlhttp.responseText;
                                document.getElementById(obj).innerHTML = unescape(response);
                                var contentWrapperObj = document.getElementById('outerPage');
                                var shimObj = document.getElementById('shim');
								var storeLocatorObj = document.getElementById('storeLocator');

                                // force size of image container if an image is displayed
                                var imgHeight = document.getElementById('storeLogoImgHeight').value;
                                if (imgHeight > 0)
                                {
                                    var storeLogo = document.getElementById('storeLogo');
                                    var browserValid = detectionIEBrowser(8);
                                    if (browserValid == false)
                                    {
                                        var maxHeight = parseInt(getStyle('storeLogoImg', 'maxHeight'));
                                    }
                                    else
                                    {
                                        var maxHeight = parseInt(getStyle('storeLogoImg', 'max-height'));
                                    }

                                    if (imgHeight > maxHeight)
                                    {
                                        storeLogo.style.height = maxHeight + 'px';
                                    }
                                    else
                                    {
                                        storeLogo.style.height = imgHeight + 'px';
                                    }
                                }

                                if (contentWrapperObj)
                                {
                                    shimObj.style.display = 'block';
                                    storeLocatorObj.style.display = 'block';
                                    storeLocatorObj.style.left = Math.round((shimObj.offsetWidth / 2) - (storeLocatorObj.offsetWidth / 2)) + 'px';

									sizeDialog(storeLocatorObj, 'storeLocatorForm');

                                    finalPosition = (document.documentElement.clientHeight - storeLocatorObj.offsetHeight) / 2;

                                    storeLocatorObj.style.top = Math.round(finalPosition) + 'px';
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

                                if ((storeLocatorObj.getElementsByTagName('script')) && (storeLocatorObj.getElementsByTagName('script')[0]))
                                {
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

                                    configureStoreLocator(JSON.parse(storeLocatorObj.getElementsByTagName('script')[0].innerHTML));
                                }
                                break;
                            case 'storeListAjaxDiv':
                                document.getElementById(obj).innerHTML = xmlhttp.responseText;
                                document.getElementById('storeListAjaxDiv').focus();

								var groupedObject = document.querySelectorAll('.collectfromstoregroupsearch');
								var groupedItemCount = groupedObject.length;

								for (i = 0; i < groupedItemCount; i++)
								{
									var groupedItem = groupedObject[i];

									groupedItem.onclick = function()
									{
										pCallback(this.dataset.privatesearch);
        								return false;
									}
								}

                                

                                <?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

                                    

                                    // fix the size of the container for scrollbar option
                                    setScrollAreaHeight('contentStoreList', 'contentNavigationStore');

									// resize stores list
									resizeResultElement();

                                    

                                <?php }?> 
                                

                                break;
                            case 'storeInfo':
                                window.scroll(0,0);
                                var storeLocator = document.getElementById('storeLocator');
                                if (storeLocator)
                                {
                                    storeLocator.style.zIndex = 15;
                                }

                                var storeInfo = document.getElementById('storeInfo');
                                if (storeInfo)
                                {
                                    storeInfo.innerHTML = unescape(xmlhttp.responseText);
                                    storeInfo.style.display = 'block';

                                    var viewportHeight =  Math.max(
                                        Math.max(document.body.offsetHeight, document.documentElement.offsetHeight),
                                        Math.max(document.body.clientHeight, document.documentElement.clientHeight)
                                    );

                                    var viewportWidth =  Math.max(
                                        Math.max(document.body.offsetWidth, document.documentElement.offsetWidth),
                                        Math.max(document.body.clientWidth, document.documentElement.clientWidth)
                                    );

                                    var contentHeight = storeInfo.offsetHeight;
                                    if (contentHeight > 600)
                                    {
                                        document.getElementById('storeInfoContent').style.height =  '600px';
                                        document.getElementById('storeInfoContent').style.overflow =  'auto';

                                        var title = document.getElementById('storeDetailLabel');
                                        var styleTitle = title.currentStyle || window.getComputedStyle(title);
                                        title.style.width = (parseInt(styleTitle.width) - 10) + 'px';
                                        document.getElementById('storeOpeningTime').style.width = (parseInt(styleTitle.width) - 10) + 'px';

                                        var content = document.getElementById('storeInfoDetails');
                                        var styleContent = content.currentStyle || window.getComputedStyle(content);
                                        content.style.width = (parseInt(styleContent.width) - 10) + 'px';
                                        content.style.paddingBottom = '10px';

                                        var openingTime = document.getElementById('storeInfoOpeningTimes');
                                        openingTime.style.width = (parseInt(styleContent.width) - 10) + 'px';
                                        openingTime.style.paddingBottom = '10px';
                                    }

                                    windowHeight = document.documentElement.clientHeight;
                                    finalPosition = (windowHeight - storeInfo.offsetHeight) / 2;

                                    storeInfo.style.top = Math.round(finalPosition) + 'px';

                                    storeInfo.style.left = Math.round(viewportWidth * 1/2 - storeInfo.offsetWidth * 1/2) + 'px';
                                }
                                break;
                            case 'termsandconditionswindow':
                            	window.scroll(0,0);
								document.documentElement.style.overflow = 'hidden';
                                var response = xmlhttp.responseText;
                                document.getElementById(obj).innerHTML = unescape(response);
                                var contentWrapperObj = document.getElementById('outerPage');
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
                            	break;
                            /* checkbox update */
							case 'paymentoptions':
							{
								var paymentOptionsObj = document.getElementById("paymentOptions");

								if (contentWrapperObj)
                                {
									paymentOptionsObj.style.display = "block";
									paymentOptionsObj.style.position = "absolute";
									paymentOptionsObj.style.zIndex = 999;
									document.getElementById("shim").style.display = "block";

									paymentOptionsObj.style.left = Math.round(shimObj.offsetWidth / 2 - termsAndConditionsObj.offsetWidth/2)+'px';

                                    var viewportHeight =  Math.max(
                                        Math.max(document.body.offsetHeight, document.documentElement.offsetHeight),
                                        Math.max(document.body.clientHeight, document.documentElement.clientHeight)
                                    );
                                    viewportHeight = document.documentElement.clientHeight;

                                    paymentOptionsObj.style.top = Math.round(viewportHeight / 2 - termsAndConditionsObj.offsetHeight/2) + 'px';

									break;
								}
							}
                        }
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
                        switch(obj)
                        {
                        	case 'savemetadata':
                        		// do nothing so that we do not fall into the default case statement
                        		break
                            case 'ordersummarypanel':
                                var responseObject = parseJson(xmlhttp.responseText);
                                document.getElementById(obj).innerHTML = responseObject.htmlCartSummary;
                                break;
                            case 'cfschangeshippingmethod':
                                document.getElementById('itemsubtotalwithshipping').innerHTML = xmlhttp.responseText;
                                gChangeMethodInPorgress = false;
                                processAjax("ordersummarypanel",".?fsaction=AjaxAPI.callback&cmd=UPDATEORDERSUMMARY", 'POST', '');

                                if (typeof pCallback != 'undefined')
                                {
                                    pCallback();
                                }

                                break;

                            case 'changegiftcard':
                                var jsonObj = parseJson(xmlhttp.responseText);
                                if (jsonObj.success)
                                {
                                    gCanUseAccount = jsonObj.canuseaccount;

                                    if (document.getElementById)
                                    {
                                        var giftcard = document.getElementById("giftcard");
                                        var giftcardamount = document.getElementById("giftcardamount");
                                        var giftcardbutton = document.getElementById("giftbutton");
                                        var ordertotaltopayvalue = document.getElementById("ordertotaltopayvalue");
                                        var ordertotaltopayside = document.getElementById("ordertotaltopayvalueside");
                                        var paymenttableobj = document.getElementById("paymenttableobj");
                                        var giftcardbalanceside = document.getElementById("giftcardbalanceside");
                                        var includetaxtextwithgiftcard = document.getElementById("includetaxtextwithgiftcard");
                                        var includetaxtextwithoutgiftcard = document.getElementById("includetaxtextwithoutgiftcard");

                                        if (giftcardbalanceside)
                                        {
                                            giftcardbalanceside.innerHTML = jsonObj.giftcardtotalremaining;
                                        }

                                        if (paymenttableobj)
                                        {
                                            if (jsonObj.hidepayment)
                                            {
                                                paymenttableobj.style.display = 'none';
                                                gPaymentMethodCode = 'NONE';
                                            }
                                            else
                                            {
                                                paymenttableobj.style.display = '';
                                                gPaymentMethodCode = "{$paymentmethodcode}";
                                                gRequestPaymentParamsRemotely = false;

                                                // reinitialise PayPal, this is because we send the ordertotalamount on the first page load to paypal
                                                // if a giftcard has been applied then we need to reinitialise paypal with the new ordertotalamount
                                                var payPalFunction = window['reinitializePayPal'];
                                                if (typeof payPalFunction === 'function')
                                                {
                                                    payPalFunction();
                                                }

                                                /**
                                                * We need to check what the default payment is when we load the page
                                                * if it is Card then we need to set the action of the complete order button
                                                * to be the callEndPoint function
                                                */
                                                var paymentMethodRadios = document.querySelectorAll("div#paymentMethodsList input[name='paymentmethods']");
                                                
                                                // IE returns multiple selector results as a NodeList rather than an array
                                                // so we need to call a new method for each object.
                                                Array.prototype.forEach.call(paymentMethodRadios, function (radio){
                                                    if (radio.checked)
                                                    {
                                                        // If the payment method is set to card we need to adjust the action of the ordercontinuebutton.
                                                        gRequestPaymentParamsRemotely = (radio.getAttribute("data-requestparamsremotley") == 'true') ? true : false;
                                                    }
                                                });
                                            }
                                        }

                                        if (ordertotaltopayvalue)
                                        {
                                            ordertotaltopayvalue.innerHTML = jsonObj.ordertotaltopay;
                                            ordertotaltopayside.innerHTML = jsonObj.ordertotaltopay;
                                        }

                                        if (jsonObj.giftcardstate == 'add')
                                        {
                                            giftcard.className = 'line-sub-total-small gift-card-box-button disabled';
                                            giftcardbutton.className = "button-voucher class_gift_add";

                                            if (includetaxtextwithgiftcard)
                                            {
                                                includetaxtextwithgiftcard.style.display = 'none';
                                            }

                                            if (includetaxtextwithoutgiftcard)
                                            {
                                                includetaxtextwithoutgiftcard.style.display = '';
                                            }
                                        }
                                        else
                                        {
                                            giftcard.className = 'line-sub-total-small gift-card-box-button';
                                            giftcardbutton.className = "button-voucher class_gift_delete";

                                            if (includetaxtextwithgiftcard)
                                            {
                                                includetaxtextwithgiftcard.style.display = '';
                                            }

                                            if (includetaxtextwithoutgiftcard)
                                            {
                                                includetaxtextwithoutgiftcard.style.display = 'none';
                                            }

                                        }

                                        if (giftcardamount)
                                        {
                                            giftcardamount.innerHTML = jsonObj.giftcardamount;
                                        }
                                    }
                                }
                                break;
                            case 'selectStore':
                                var responseData = parseJson(xmlhttp.responseText);

                                var storeAddress = responseData['storeaddress'];
                                var storeId = responseData['storeid'];
                                var shippingCode = getShippingRateCode();
                                gStoreAddresses[shippingCode] = storeAddress;
                                gStoreCodes[shippingCode] = storeId;
                                shippingMethodCfsClick(shippingCode);
                                var shimObj = document.getElementById('shim');
                                var storeLocatorObj = document.getElementById('storeLocator');
                                if (shimObj && storeLocatorObj)
                                {
                                    shimObj.style.display = 'none';
                                    storeLocatorObj.style.display = 'none';
                                }
                                if (document.documentElement.style.overflow == 'hidden')
                                {
                                    document.documentElement.style.overflow = '';
                                }

                                document.submitform.fsaction.value = "Order.refresh";
                                document.submitform.submit();

                                break;
                            case 'updateorderqtyall':
                                var response = parseJson(xmlhttp.responseText);
                                count = response.length;
                                for( i = 0; i < count; i++)
                                {
                                    document.getElementById(response[i].content).innerHTML = unescape(decodeURIComponent(response[i].orderLineHTML));
                                    document.getElementById('orderFooter').innerHTML = unescape(decodeURIComponent(response[i].orderFooterHTML));
                                    gOrderData[response[i].data.orderlineid] = response[i].data;
                                    gOrderCanContinue.ordercancontinue = response[i].ordercancontinue;
                                    if( response[i].vouchermessage != null && response[i].vouchermessage.length > 0 )
                                    {
                                        alert(response[i].vouchermessage);
                                    }
                                }
                                processAjax("ordersummarypanel",".?fsaction=AjaxAPI.callback&cmd=UPDATEORDERSUMMARY", 'POST', '');
                            break;

							case 'updatecompanionqty':
								var response = parseJson(xmlhttp.responseText);

								if (response.result)
								{
									// added ok
									setCompanionQty(response);
								}
								else
								{
									// error
								}

								break;
                            case 'requestpaymentparams':
                                /*
                                * This will check if the gateway is using a callback so that it can mount the necessary ui elements
                                *  we than call that callback function with the result of the ajax call which was to the initialise gateway function
                                */
                                if(typeof pCallback === "function")
								{
                                    pCallback(xmlhttp.responseText);
                                }
                                
                                toggleWaitingSpinner();
                               
                                break;
                            case 'queryccitable':
                                pCallback(xmlhttp.responseText);
							    break;   
                            case 'processpaymenttoken':
                                var response = parseJson(xmlhttp.responseText);
                                pCallback(response);
                                break; 
                            default:
                                var response = parseJson(xmlhttp.responseText);

                                var orderFooter = document.getElementById('orderFooter');
                                document.getElementById(obj).innerHTML = unescape(decodeURIComponent(response.orderLineHTML));
                                if(orderFooter)
                                {
                                    document.getElementById('orderFooter').innerHTML = unescape(decodeURIComponent(response.orderFooterHTML));
                                }
                                gOrderData[response.data.orderlineid] = response.data;
                                gOrderCanContinue.ordercancontinue = response.ordercancontinue;
                                if( response.vouchermessage != null && response.vouchermessage.length > 0 )
                                {
                                    alert(response.vouchermessage);
                                }
                                gAjaxInProgress = false;
                                processAjax("ordersummarypanel",".?fsaction=AjaxAPI.callback&cmd=UPDATEORDERSUMMARY", 'POST', '');
                        }
                    }
                }
                xmlhttp.send(params);
            }
        }

        function changeComponentImageLoaded(obj)
        {
            if (obj)
            {
                gLoadedComponentsImagesCount++;
            }

            var previewHolderCartObj = document.getElementById('previewHolderCart');

            if (previewHolderCartObj)
            {
                if (gComponentImagesCount == 0)
                {
                    var allImgElems = previewHolderCartObj.getElementsByTagName('img');

                    for (var i = 0, loopElem; i < allImgElems.length; i++)
                    {
                        loopElem = allImgElems[i];
                        if (loopElem.className.indexOf('previewItemImg') > -1)
                        {
                            gComponentImagesCount++;
                        }
                    }
                }

                /* what if there are no images? */
                if (gComponentImagesCount == gLoadedComponentsImagesCount)
                {
                    var allElems = previewHolderCartObj.getElementsByTagName('span');
                    var previewItemHolderElems = [];

                    for (var i = 0, loopElem; i < allElems.length; i++)
                    {
                        loopElem = allElems[i];
                        if (loopElem.className.indexOf('previewItemHolder') > -1)
                        {
                            previewItemHolderElems.push(loopElem);
                        }
                    }
                    for (var i = 1, loopElem, maxHeight = 0, elemNum = 0; i <= previewItemHolderElems.length; i++)
                    {
                        loopElem = previewItemHolderElems[i-1];
                        maxHeight = Math.max(loopElem.offsetHeight, maxHeight);

                        if (((i >= 0) && (i%3 == 0)) || (i == previewItemHolderElems.length))
                        {
                            if (i == previewItemHolderElems.length)
                            {
                                if (i % 3 == 0)
                                {
                                    elemNum = 3;
                                }
                                else
                                {
                                    elemNum = i % 3;
                                }
                            }
                            else
                            {
                                elemNum = 3;
                            }
                            for (var j = 0; j < elemNum; j++)
                            {
                                if (previewItemHolderElems[i-1 - j])
                                {
                                    previewItemHolderElems[i-1 - j].style.height = maxHeight + 'px';
                                }
                            }
                            maxHeight = 0;
                        }
                    }
                }
            }
        }

        function setItemActive(objSelected)
        {
            var popupBoxContentElem = document.getElementById('previewHolderCart');
            var checkboxes = popupBoxContentElem.getElementsByTagName('input');
            var elemCheck = objSelected.getElementsByTagName('input');
            for (var i = 0; i < checkboxes.length; i++)
            {
                var elemBox = checkboxes[i].parentNode.parentNode;
                if (checkboxes[i] == elemCheck[0])
                {
                     checkboxes[i].checked = true;
                }
                elemBox.className = elemBox.className.replace(' selected', '');
            }
            objSelected.className = objSelected.className + ' selected';
        }

        function toggleGeneric(idLink, idElement, color)
        {
            var objElement = document.getElementById(idElement);
            var objLinkToggle = document.getElementById('link_' + idLink);

            if (objElement.style.display == 'none')
            {
                objElement.style.display = 'block';
                objLinkToggle.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_OrderHide');?>
";
                return;
            }
            else
            {
                objElement.style.display = 'none';
                objLinkToggle.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_OrderShow');?>
";
            }
        }

        function toggleSummary()
        {
            var objElement = document.getElementById('contentOrderSummary');
            var objLinkToggle = document.getElementById('link_toggle');
            if( objElement.style.display == 'none')
            {
                objElement.style.display = 'block';
                objLinkToggle.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_OrderHide');?>
";
            }
            else
            {
                objElement.style.display = 'none';
                objLinkToggle.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_OrderShow');?>
";
            }
        }

        function configureStoreLocator(pStoreDataObj)
        {
            gStoreCode = pStoreDataObj.storecode;

            gPayInStoreOption = pStoreDataObj.payInStoreAllowed;

            var gShowAllCode = pStoreDataObj.showAllCode;
            var gCountryCode = pStoreDataObj.countryCode;
            var gSearchCountry = pStoreDataObj.searchCountry;
            var gSearchRegion = pStoreDataObj.searchRegion;
            var gSearchStoreGroup = pStoreDataObj.searchStoreGroup;
            var gSearchText = pStoreDataObj.searchText;
            var gPrivateSearchText = pStoreDataObj.privateSearchText;

            var gInfoHeight = 400;
            var gInfoWidth = 600;

            gCountryCodes = pStoreDataObj.storeLoacationData.countrycode;
            gCountryNames = pStoreDataObj.storeLoacationData.countryname;
            gRegionCodes = pStoreDataObj.storeLoacationData.regioncode;
            gRegionNames = pStoreDataObj.storeLoacationData.regionname;
            gSiteGroupCodes = pStoreDataObj.storeLoacationData.sitegroupcode;
            gSiteGroupNames = pStoreDataObj.storeLoacationData.sitegroupname;


            if (pStoreDataObj.showcountrylist == 0)
            {
                changeCountry(pStoreDataObj.countryCode);
            }

            selectOptionByValue('countries', pStoreDataObj.searchCountry);

            if (pStoreDataObj.searchCountry != "")
            {
                changeCountry(pStoreDataObj.searchCountry);
            }

            selectOptionByValue('regions', pStoreDataObj.searchRegion);
            changeRegion(pStoreDataObj.searchRegion);
            selectOptionByValue('storegroups', pStoreDataObj.searchStoreGroup);
            document.getElementById('searchText').value = pStoreDataObj.searchText;

            if (gStoreCode + gSearchText != '')
            {
            searchForStore();
            }
        }



<?php if ($_smarty_tpl->tpl_vars['issmallscreen']->value == 'true') {?>

    
    

    /**
    * changeSystemLanguageSmallScreen
    *
    * Set the new language and reload the page
    */
    function changeSystemLanguageSmallScreen(pLanguageCode)
    {
        createCookie("maweblocale", pLanguageCode, 24 * 365);
        document.submitform.fsaction.value = "Order.refresh";
        document.submitform.submit();
    }

    function cancelOrder()
    {
        // close the confirmation message
        closeDialog();

        // open the loading box
        showLoadingDialog();

        processAjaxSmallScreen('showCancellation', ".?fsaction=AjaxAPI.callback&cmd=ORDERCANCEL", 'POST', '');

    }

    function cancelOrderConfirmation()
    {

    

    <?php if ($_smarty_tpl->tpl_vars['sessionrevived']->value == true) {?>

        

        cancelOrder();

        

    <?php } else { ?> 
        

        showConfirmDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", nlToBr("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ConfirmCancellation');?>
"), function(e) {
            cancelOrder();
        });

        

    <?php }?> 
    

    }

    // Wrapper for checkMetadataValidity function
    function fnCheckMetadataValidity(pElement)
    {
        return checkMetadataValidity(pElement.getAttribute('data-divid'), pElement.getAttribute('data-displaymessage') === 'true');
    }

    function checkMetadataValidity(pDivId, pDisplayMessage)
    {
        /* on submit get all metadata fields and submit to the server */

        var postParams = '';
        var hasAnError = false;
        var componentHighLight = '';
        var highlightBoxes = [];
        var radios = [];

        var contentMetadata = document.getElementById(pDivId);
        var metadataLegnth = contentMetadata.getElementsByClassName('componentMetadata').length;

        for (var i = 0; i < metadataLegnth; i++)
        {
            var componentMetadata = contentMetadata.getElementsByClassName('componentMetadata')[i];
            var inputs = componentMetadata.getElementsByTagName('input');
            var selects = componentMetadata.getElementsByTagName('select');
            var textareas = componentMetadata.getElementsByTagName('textarea');

            /* get metadata values for all inputs on the page */
            for (var j = 0; j < inputs.length; j++)
            {
                switch (inputs[j].type)
                {
                    case 'text':

                        if ((inputs[j].className.indexOf('required') > -1) && (inputs[j].value == ''))
                        {
                            hasAnError = true;

                            highlightBoxes.push('metadataItem' + inputs[j].name);

    

    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

        

                            componentHighLight = inputs[j].parentNode.parentNode.parentNode.parentNode.parentNode;
                            var componentID = componentHighLight.className.replace('componentMetadata outerBoxPadding metadataId', '');
                            var componentIDs = componentID.split("_");

                            if (componentIDs.length == 2)
                            {
                                highlightBoxes.push('componentContent_' + componentID);
                            }
                            else
                            {
                                // add component and subcomponent
                                highlightBoxes.push('componentContent_' +  componentIDs[0] + '_' + componentIDs[1]);
                                highlightBoxes.push('componentContent_' + componentID);
                            }

        

    <?php }?> 
    


                        }
                        else
                        {
                            postParams += '&' + inputs[j].name + '=' + encodeURIComponent(inputs[j].value);
                        }

                        break;
                    case 'radio':
                        if( !(Object.prototype.toString.call( radios[inputs[j].name] ) === '[object Array]' ))
                        {
                            radios[inputs[j].name] = [];
                        }
                        radios[inputs[j].name].push(inputs[j]);
                        break;
                    case 'checkbox':
                        postParams += '&' + inputs[j].name + '=' + ((inputs[j].checked) ? '1' : '0');
                        break;
                }
            }

            for(var radio in radios)
            {
                var checked = false;
                for (var x=0; x < radios[radio].length; x++)
                {
                    if (radios[radio][x].checked)
                    {
                        postParams += '&' + radios[radio][x].name + '=' + encodeURIComponent(radios[radio][x].value);
                        checked = true;
                    }
                }

                if(checked == false)
                {
                    x = x -1;

                    hasAnError = true;

                    highlightBoxes.push('metadataItem' + radios[radio][x].name);

    

    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

        

                    componentHighLight = radios[radio][x].parentNode.parentNode.parentNode.parentNode.parentNode;
                    var componentID = componentHighLight.className.replace('componentMetadata outerBoxPadding metadataId', '');
                    var componentIDs = componentID.split("_");
                    if (componentIDs.length == 2)
                    {
                        highlightBoxes.push('componentContent_' + componentID);
                    }
                    else
                    {
                        // add component and subcomponent
                        highlightBoxes.push('componentContent_' + componentIDs[0] + '_' + componentIDs[1]);
                        highlightBoxes.push('componentContent_' + componentID);
                    }


         

    <?php }?> 
    

                }

            }

            /* get metadata values for all selects on the page */
            for (var j = 0; j < selects.length; j++)
            {
                if (selects[j].options[selects[j].selectedIndex].value == '')
                {
                    hasAnError = true;

                    highlightBoxes.push('metadataItem' + selects[j].name);

    

    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

        
                    componentHighLight = selects[j].parentNode.parentNode.parentNode.parentNode.parentNode;
                    var componentID = componentHighLight.className.replace('componentMetadata outerBoxPadding metadataId', '');
                    var componentIDs = componentID.split("_");

                    if (componentIDs.length == 2)
                    {
                        highlightBoxes.push('componentContent_' + componentID);
                    }
                    else
                    {
                        // add component and subcomponent
                        highlightBoxes.push('componentContent_' + componentIDs[0] + '_' + componentIDs[1]);
                        highlightBoxes.push('componentContent_' + componentID);
                    }

        

    <?php }?> 
    

                }
                else
                {
                    postParams += '&' + selects[j].name + '=' + encodeURIComponent(selects[j].options[selects[j].selectedIndex].value);
                }
            }

            /* get metadata values for all textareas on the page */
            for (var j = 0; j < textareas.length; j++)
            {
                if ((textareas[j].className.indexOf('required') > -1) && (textareas[j].value == ''))
                {
                    hasAnError = true;

                    highlightBoxes.push('metadataItem' + textareas[j].name);
    

        <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

            

                    componentHighLight = textareas[j].parentNode.parentNode.parentNode.parentNode.parentNode;
                    var componentID = componentHighLight.className.replace('componentMetadata outerBoxPadding metadataId', '');
                    var componentIDs = componentID.split("_");

                    if (componentIDs.length == 2)
                    {
                        highlightBoxes.push('componentContent_' + componentID);
                    }
                    else
                    {
                        // add component and subcomponent
                        highlightBoxes.push('componentContent_' + componentIDs[0] + '_' + componentIDs[1]);
                        highlightBoxes.push('componentContent_' + componentID);
                    }

            

    <?php }?> 
    

                }
                else
                {
                    postParams += '&' + textareas[j].name + '=' + encodeURIComponent(textareas[j].value);
                }
            }
        }

        // display the highlight effect
        setHighlightAllBoxes(highlightBoxes);

        if (pDisplayMessage == true)
        {
            if (hasAnError == true)
            {
                createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorValueRequired');?>
", function(e) {
                    closeDialog(e);
                });
            }

            return {'hasAnError': hasAnError, 'postParams': postParams};
        }
    }


    /**
    * initializeApplication
    *
    * set size of elments needed for the application
    */
   function initializeApplication(pInitialize)
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

       // if it's the first tile the application is loaded, container width are calculated
       if (pInitialize == true)
       {
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

            var innerBox = document.getElementById('innerBox');
            var styleInnerBox = innerBox.currentStyle || window.getComputedStyle(innerBox);
            gInnerBox = parseIntStyle(styleInnerBox.paddingLeft) + parseIntStyle(styleInnerBox.paddingRight);
            gInnerBox += parseIntStyle(styleInnerBox.marginLeft) + parseIntStyle(styleInnerBox.marginRight);
            gInnerBox += parseIntStyle(styleInnerBox.borderLeftWidth) + parseIntStyle(styleInnerBox.borderRightWidth);

            var innerBoxPadding = document.getElementById('innerBoxPadding');
            var styleInnerBoxPadding = innerBoxPadding.currentStyle || window.getComputedStyle(innerBoxPadding);
            gInnerBoxPadding = parseIntStyle(styleInnerBoxPadding.paddingLeft) + parseIntStyle(styleInnerBoxPadding.paddingRight);

            var containerHighLight = document.getElementById('containerHighLight');
            var styleContainerHighLight = containerHighLight.currentStyle || window.getComputedStyle(containerHighLight);
            gHighLightBorderSizeDifference = parseIntStyle(styleContainerHighLight.borderLeftWidth) + parseIntStyle(styleContainerHighLight.borderRightWidth);
            gHighLightBorderSizeDifference -= (parseIntStyle(styleInnerBox.borderLeftWidth) + parseIntStyle(styleInnerBox.borderRightWidth));
        }

        if (gScreenWidth > gMaxWidth)
        {
            gOuterBoxContentBloc = gMaxWidth - gOuterBox - gOuterBoxPadding;
        }
        else
        {
            gOuterBoxContentBloc = gScreenWidth - gContentScrollCart - gOuterBox - gOuterBoxPadding;
        }

        gInnerBoxContentBloc = gOuterBoxContentBloc - gInnerBox - gInnerBoxPadding;

        var contentSite = document.getElementById('contentBlocSite');
        contentSite.style.width = (width * 8) + 'px'; // force the content of the site to contains all panels
    }

    /**
    * resizeComponentDesign
    *
    * Set the width of components or subcomponents
    */
    function resizeComponentDesign(pContainerActive, pClassWithPreview, pClassWithoutPreview)
    {
        var containerActive = document.getElementById(pContainerActive);

        if (gComponentPreview == 0)
        {
            // remove preview image width to to the full width
            var componentPreview = containerActive.getElementsByClassName('componentPreview')[0];
            if (componentPreview)
            {
                var styleComponentPreview = componentPreview.currentStyle || window.getComputedStyle(componentPreview);
                gComponentPreview = parseIntStyle(styleComponentPreview.width);

                // remove the preview image padding
                gComponentPreview += parseIntStyle(styleComponentPreview.marginLeft) + parseIntStyle(styleComponentPreview.marginRight);
            }
        }

        // set all components label
        var widthPrice = 0;
        var classLength = containerActive.getElementsByClassName('componentLabel').length;
        for (var i = 0; i < classLength; i++)
        {
            var elm = containerActive.getElementsByClassName('componentLabel')[i];

            widthDiv = gInnerBoxContentBloc;

            if (elm.parentNode.parentNode.className.indexOf('componentHighLight') != -1)
            {
                widthDiv -= gHighLightBorderSizeDifference;
            }

            var nextElm = elm.nextElementSibling || nextElementSibling(elm);

            if (nextElm.className == 'componentPrice')
            {
                if (widthPrice == 0)
                {
                    var componentPrice = containerActive.getElementsByClassName('componentPrice')[0];
                    var styleComponentPrice = componentPrice.currentStyle || window.getComputedStyle(componentPrice);
                    widthPrice = parseIntStyle(styleComponentPrice.width);
                }

                 elm.style.width = (widthDiv - widthPrice) + 'px';
            }
            else
            {
                elm.style.width = widthDiv + 'px';
            }
        }


        // set all subcomponents label
        var widthPrice = 0;
        var classLength = containerActive.getElementsByClassName('subcomponentLabel').length;
        for (var i = 0; i < classLength; i++)
        {
            var elm = containerActive.getElementsByClassName('subcomponentLabel')[i];

            widthDiv = gOuterBoxContentBloc;

            if (elm.parentNode.parentNode.className.indexOf('componentHighLight') != -1)
            {
                widthDiv -= gHighLightBorderSizeDifference;
            }

            var nextElm = elm.nextElementSibling || nextElementSibling(elm);

            if (nextElm.className == 'subcomponentPrice')
            {
                if (widthPrice == 0)
                {
                    var subcomponentPrice = containerActive.getElementsByClassName('subcomponentPrice')[0];
                    var styleSubcomponentPrice = subcomponentPrice.currentStyle || window.getComputedStyle(subcomponentPrice);
                    widthPrice = parseIntStyle(styleSubcomponentPrice.width);
                }

				elm.style.width = (widthDiv - widthPrice) + 'px';
            }
            else
            {
                elm.style.width = widthDiv + 'px';
            }
        }


        // set all text with preview to get the same width
        var classLength = containerActive.getElementsByClassName(pClassWithPreview).length;
        var widthCheckbox = 0;

        for (var i = 0; i < classLength; i++)
        {
            var elm = containerActive.getElementsByClassName(pClassWithPreview)[i];

			if (elm.parentNode.parentNode.parentNode.className.indexOf('contentSubComponentBloc') != -1)
			{
				widthDiv = gOuterBoxContentBloc - gComponentPreview;
			}
			else
			{
				widthDiv = gInnerBoxContentBloc - gComponentPreview;
			}

			if ((elm.parentNode.parentNode.parentNode.className.indexOf('componentHighLight') != -1) || (elm.parentNode.parentNode.className.indexOf('componentHighLight') != -1))
			{
				widthDiv -= gHighLightBorderSizeDifference;
			}

            // if it's a checkbox the width of the button need to be removed
            if (elm.parentNode.className == 'checkboxBloc')
            {
                if (widthCheckbox == 0)
                {
                    var checkBoxBtn = containerActive.getElementsByClassName('onOffSwitch')[0];
                    var stylecheckBoxBtn = checkBoxBtn.currentStyle || window.getComputedStyle(checkBoxBtn);
                    widthChekcbox = parseIntStyle(stylecheckBoxBtn.width);
                }

                elm.style.width = widthDiv - widthChekcbox + 'px';
            }
            else
            {
                elm.style.width = widthDiv + 'px';
            }
        }

        // set all text without preview to get the same width
        classLength = containerActive.getElementsByClassName(pClassWithoutPreview).length;
        for (var i = 0; i < classLength; i++)
        {
            var elm = containerActive.getElementsByClassName(pClassWithoutPreview)[i];

			if (elm.parentNode.parentNode.parentNode.className.indexOf('contentSubComponentBloc') != -1)
			{
				widthDiv = gOuterBoxContentBloc;
			}
			else
			{
				widthDiv = gInnerBoxContentBloc;
			}

			if ((elm.parentNode.parentNode.parentNode.className.indexOf('componentHighLight') != -1) || (elm.parentNode.parentNode.className.indexOf('componentHighLight') != -1))
			{
				widthDiv -= gHighLightBorderSizeDifference;
			}

			// if it's a checkbox the width of the button need to be removed
            if (elm.parentNode.className == 'checkboxBloc')
            {
                if (widthCheckbox == 0)
                {
                    var checkBoxBtn = containerActive.getElementsByClassName('onOffSwitch')[0];
                    var stylecheckBoxBtn = checkBoxBtn.currentStyle || window.getComputedStyle(checkBoxBtn);
                    widthChekcbox = parseIntStyle(stylecheckBoxBtn.width);
                }

                elm.style.width = widthDiv - widthChekcbox + 'px';
            }
            else
            {
                elm.style.width = widthDiv + 'px';
            }
        }
    }

    function loadShippingPanel(pAction)
    {
        var metadataValidity = checkMetadataValidity('contentAjaxQty', true);

        var postParams = metadataValidity.postParams;
        postParams += '&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
';
        postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';
        processAjaxSmallScreen(pAction, ".?fsaction=AjaxAPI.callback&cmd=ORDERCONTINUE", 'POST', postParams);
    }

    function loadPaymentPanel()
    {
        var postParams = '&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
';
        postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';
        postParams += '&shippingratecode=' + getShippingRateCode();

        processAjaxSmallScreen('showPayment', ".?fsaction=AjaxAPI.callback&cmd=ORDERCONTINUE", 'POST', postParams);
    }

	/**
	* saveTempMetadata
	*
	* Save the metadata in the current session
	*/
	function saveTempMetadata()
	{
	   // on submit get all metadata fields and submit to the server
	   var postParams = [];
	   var metadataLegnth = document.getElementsByClassName('componentMetadata').length;

	   for (var i = 0; i < metadataLegnth; i++)
	   {
		   componentMetadata = document.getElementsByClassName('componentMetadata')[i];
		   inputs = componentMetadata.getElementsByTagName('input');
		   selects = componentMetadata.getElementsByTagName('select');
		   textareas = componentMetadata.getElementsByTagName('textarea');

		   /* get metadata values for all inputs on the page */
		   for (var j = 0; j < inputs.length; j++)
		   {
			   switch (inputs[j].type)
			   {
				   case 'text':
					   postParams.push(inputs[j].name + '=' + encodeURIComponent(inputs[j].value));
					   break;
				   case 'radio':
					   if (inputs[j].checked)
					   {
						   postParams.push(inputs[j].name + '=' + encodeURIComponent(inputs[j].value));
					   }
					   break;
				   case 'checkbox':
					   postParams.push(inputs[j].name + '=' + ((inputs[j].checked) ? '1' : '0'));
					   break;
			   }
		   }

		   /* getdata values for all selects on the page */
		   for (var j = 0; j < selects.length; j++)
		   {
			   postParams.push(selects[j].name + '=' + encodeURIComponent(selects[j].options[selects[j].selectedIndex].value));
		   }

		   /* get metadata values for all textareas on the page */
		   for (var j = 0; j < textareas.length; j++)
		   {
			   postParams.push(textareas[j].name + '=' + encodeURIComponent(textareas[j].value));
		   }
	   }

	   postParams = postParams.join('&');
	   postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';
	   processAjax("savemetadata",".?fsaction=AjaxAPI.callback&cmd=SAVETEMPMETADATA", 'POST', postParams);
	}

    

    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

        

        /**
         * initializeStage
         *
         * set size of container on the qty stage
         */
        function initializeStage()
        {

            

                <?php echo $_smarty_tpl->tpl_vars['custominit']->value;?>


                <?php echo $_smarty_tpl->tpl_vars['initlanguage']->value;?>


            

            // set the size of the container
            document.getElementById('contentAjaxQty').style.width = (gScreenWidth * 4) + 'px';

            // main panel
            document.getElementById('contentPanelQty').style.width = gScreenWidth + 'px';

            // component panel
            document.getElementById('contentPanelComponent').style.width = gScreenWidth + 'px';

            // subcomponent panel
            document.getElementById('contentPanelSubComponent').style.width = gScreenWidth + 'px';

            // component choice panel
            document.getElementById('contentPanelComponentChoice').style.width = gScreenWidth + 'px';
        }

        /**
         * initializeQtyPanel
         *
         * set size of html elments on the qty stage
         */
        function initializeQtyPanel()
        {
            // set product title size
            var productPrice = document.getElementById('productPriceqty');
            var styleProductPrice = productPrice.currentStyle || window.getComputedStyle(productPrice);
            var productName = document.getElementById('productNameqty');
            var productNameWidth = gOuterBoxContentBloc - parseIntStyle(styleProductPrice.width);
            productNameWidth = productNameWidth - parseIntStyle(styleProductPrice.paddingLeft) - parseIntStyle(styleProductPrice.paddingRight);
            productName.style.width = productNameWidth + 'px';

            // check if a component exists
            var componentBloc = document.getElementsByClassName('componentBloc')[0];
            if (componentBloc)
            {
                resizeComponentDesign('contentLeftScrollQty', 'componentContentText', 'componentContentTextLong');
            }

            document.getElementById('contentPanelQty').style.display = 'block';

            document.getElementById('contentLeftScrollQty').scrollTop = gScrollRefreshPosition;

            setScrollAreaHeight('contentLeftScrollQty', '');

            // close the loading box
            closeLoadingDialog();
        }

        /**
         * initializeComponentView
         *
         * Set size of html elments on a component or a subcomponent panel
         */
        function initializeComponentView(pDivID)
        {
            // set subcomponents design
            resizeComponentDesign(pDivID, 'componentDetailContentText', 'componentDetailContentTextLong');

            /** end component design **/

            // set the metadata design
            setMetadataDesign(pDivID);

            // close the loading box
            closeLoadingDialog();
        }

        /**
         * initializeComponentChoice
         *
         * Set size of html elments on component choice panel
         */
        function initializeComponentChoice()
        {
            // choice list container
            var choiceList = document.getElementById('choiceList');

            // tick image width
            if (gTickImageWidth == 0 )
            {
                var checkboxImage = choiceList.getElementsByClassName('checkboxImage')[0];
                var styleCheckboxImage = checkboxImage.currentStyle || window.getComputedStyle(checkboxImage);
                gTickImageWidth =  parseIntStyle(styleCheckboxImage.width) + parseIntStyle(styleCheckboxImage.marginLeft) + parseIntStyle(styleCheckboxImage.marginRight);
            }

            var widthContent = gOuterBoxContentBloc - gTickImageWidth;

            // size of the arrow for collect from store
            var imgInfo = choiceList.getElementsByClassName('imgInfo')[0];
            var widthElemExternal = 0;
            if (imgInfo)
            {
                var styleInfo = imgInfo.currentStyle || window.getComputedStyle(imgInfo);
                widthElemExternal = parseIntStyle(styleInfo.width) + parseIntStyle(styleInfo.marginLeft) + parseIntStyle(styleInfo.marginRight);
            }

            var classLength = choiceList.getElementsByClassName('listLabelChoice').length;
            for (var i = 0; i < classLength; i++)
            {
                var elm = choiceList.getElementsByClassName('listLabelChoice')[i];

                if (i == 0)
                {
                    var styleLabel = elm.currentStyle || window.getComputedStyle(elm);
                    widthContent = widthContent - parseIntStyle(styleLabel.paddingLeft) - parseIntStyle(styleLabel.paddingRight);
                    widthContent = widthContent - widthElemExternal;
                }

                elm.style.width = widthContent + 'px';
            }

            var widthPreview = 0;
            var classLength = choiceList.getElementsByClassName('choiceDescription').length;
            for (var i = 0; i < classLength; i++)
            {
                var elm = choiceList.getElementsByClassName('choiceDescription')[i];
                if (i == 0)
                {
                    var componentPreview = choiceList.getElementsByClassName('componentPreview')[0];
                    var styleComponentPreview = componentPreview.currentStyle || window.getComputedStyle(componentPreview);
                    widthPreview = parseIntStyle(styleComponentPreview.width) + parseIntStyle(styleComponentPreview.marginLeft) + parseIntStyle(styleComponentPreview.marginRight);
                }

                var previousElm = elm.previousElementSibling || previousElementSibling(elm);
                if (previousElm.className == 'componentPreview')
                {
                     elm.style.width = (widthContent - widthPreview) + 'px';
                }
                else
                {
                    elm.style.width = widthContent + 'px';
                }
            }
        }

        /**
         * updateCheckbox
         *
         * Send an ajax to activate or desactivate a checkbox
         */
        function updateCheckbox(orderLineId, componentId)
        {
            // show loading dialog
            showLoadingDialog();

            // temp save metadata
            saveTempMetadata();

            // get teh id of teh line de refresh
            gOrderlineidActive = orderLineId;

            // refresh order line
            processAjaxSmallScreen("updateCheckBox",".?fsaction=AjaxAPI.callback&cmd=UPDATECHECKBOXSMALL&orderlineid=" + orderLineId + "&componentid=" + componentId, 'POST', '');
        }

        /**
         * changeComponent
         *
         * Send an ajax to get component or a subcomponent list
         */
        function changeComponent(item, section)
        {
            // show loading dialog
            showLoadingDialog();

            // temp save metadata
            saveTempMetadata();

            // init the back action
            if ((gActivePanel == 'contentPanelComponent') || (gActivePanel == 'contentPanelComponentBack'))
            {
                document.getElementById('choiceBackButton').onclick = function()
                {
                    setHashUrl('componentView|' + gOrderlineidActive + '|' + gComponentActive);
                };
            }
            else
            {
                document.getElementById('choiceBackButton').onclick = function()
                {
                    setHashUrl('subComponentView|' + gOrderlineidActive + '|' + gComponentActive + '|' + gSubComponentActive);
                };
            }

            // send the ajax
            processAjaxSmallScreen("componentChangeList",".?fsaction=AjaxAPI.callback&cmd=CHANGECOMPONENTSMALL&item=" + item + "&section=" + section, 'GET', '');
        }

        // Wrapper for showInfoComponent.
        function fnShowInfoComponent(pElement)
        {
            showInfoComponent(pElement.getAttribute("data-name"), pElement.getAttribute("data-description"));
        }

        /**
         * showInfoComponent
         *
         * Display a dialog box with the detail of a component or a subcomponent
         */
        function showInfoComponent(pName, pDescription)
        {
            createDialog(pName, pDescription, function(e) {
                closeDialog(e);
            });
        }

        // Wrapper for componentChoiceClick
        function fnComponentChoiceClick(pElement)
        {
            return componentChoiceClick(pElement.getAttribute("data-sectionlineid"));
        }

        /**
         * componentChoiceClick
         *
         * Select the option clicked and change the action on update button
         */
        function componentChoiceClick(pSection)
        {
            // loop through all the components to see which one has been selected
            var componentsChoiceLength = document.getElementsByName('componentsChoice').length;
            for (var i = 0; i < componentsChoiceLength; i++)
            {
                var radioBox = document.getElementsByName('componentsChoice')[i];
                var parent = radioBox.parentNode;
                if (radioBox.checked)
                {
                    parent.classList.add('optionSelected');
                }
                else
                {
                    parent.classList.remove('optionSelected');
                }
            }

            document.getElementById('updateChoiceBtn').onclick = function() {
                updateChoice(pSection);
            };
        }

        /**
         * updateChoice
         *
         * Send an ajax call to change a component or a subcomponent
         */
        function updateChoice(pSection)
        {
            var selectedOption = '';
            var componentCode = '';
            var localComponentCode = '';

            // show loading dialog
            showLoadingDialog();

            // loop through all the components to see which one has been selected
            var componentsChoiceLength = document.getElementsByName('componentsChoice').length;
            for (var i = 0; i < componentsChoiceLength; i++)
            {
                var radioBox = document.getElementsByName('componentsChoice')[i];
                if (radioBox.checked)
                {
                    selectedOption = document.getElementById(radioBox.id);
                    componentCode = selectedOption.value;
                    localComponentCode = selectedOption.getAttribute("localcode");
                }
            }

            // save temp metadata
            saveTempMetadata();

            // refresh order line
            processAjaxSmallScreen('updateComponent', ".?fsaction=AjaxAPI.callback&cmd=UPDATECOMPONENTSMALL&orderlineid=" + gOrderlineidActive +
                                "&section=" + pSection + "&code=" + componentCode + "&localcode=" + localComponentCode, "POST", "");
        }

        /**
         * updateComponentQty
         *
         * Send an ajax call to change a component or a subcomponent quantity
         */
        function updateComponentQty(componentOrderLineID, prodQty, quantityValue)
        {
            if (document.submitform.itemqty.value != "-")
            {
                var validQty = true;
                var itemQtyField = 'itemqty_' + componentOrderLineID;

                // check to see if we are dealing witht the order footer
                var orderLinePrefix = componentOrderLineID.substr(0,componentOrderLineID.indexOf('_'));

                if (orderLinePrefix == '-')
                {
                    var orderLineId = -1;
                }
                else
                {
                    var orderLineId = orderLinePrefix;
                }

                if ((quantityValue == undefined) || (quantityValue == ''))
                {
                    var newQty = string2integer(document.getElementById('itemqty_' + componentOrderLineID).value);
                }
                else
                {
                    var newQty = string2integer(quantityValue);
                }

                if (isNaN(newQty))
                {
                    validQty = false;
                }
                else if (newQty > 99999999)
                {
                    validQty = false;
                }

                if (validQty)
                {
                    // show loading dialog
                    showLoadingDialog();

                    // save temp metadata
                    saveTempMetadata();

                    // send ajax query
                    processAjaxSmallScreen("updateComponentQty",".?fsaction=AjaxAPI.callback&cmd=UPDATECOMPONENTQTYSMALL&orderlineid=" + orderLineId +
                                                                "&componentorderlineid=" + componentOrderLineID + "&componentitemqty=" + newQty +
                                                                "&itemqty=" + prodQty, 'POST', '');
                }
                else
                {
                    createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidQty');?>
", function(e) {
                        closeDialog(e);
                    });
                }
            }
            else
            {
                createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoPrice');?>
", function(e) {
                    closeDialog(e);
                });
            }
        }


        

    <?php }?> 
    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'shipping') {?>

        

        /**
         * initializeStage
         *
         * Set the size of html elements on shipping stage
         */
        function initializeStage()
        {
            

                <?php echo $_smarty_tpl->tpl_vars['custominit']->value;?>


                <?php echo $_smarty_tpl->tpl_vars['initlanguage']->value;?>


            

            // only three panel can be slide at one point
            document.getElementById('contentAjaxShipping').style.width = (gScreenWidth * 3) + 'px';

            // shipping panel
            var contentPanelShipping = document.getElementById('contentPanelShipping');
            contentPanelShipping.style.width = gScreenWidth + 'px';

            // method List panel
            var contentPanelMethodList = document.getElementById('contentPanelMethodList');
            contentPanelMethodList.style.width = gScreenWidth + 'px';

            // select store panel
            var contentPanelSelectStore = document.getElementById('contentPanelSelectStore');
            contentPanelSelectStore.style.width = gScreenWidth + 'px';

            // update address panel
            var contentPanelUpdateAddress = document.getElementById('contentPanelUpdateAddress');
            contentPanelUpdateAddress.style.width = gScreenWidth + 'px';

            // close the loading box
            closeLoadingDialog();
        }

        function resizeQtyPanel()
        {
            // slide the qty panel
            var contentAjaxQty = document.getElementById('contentAjaxQty');
            contentAjaxQty.style.width = gScreenWidth + 'px';
            contentAjaxQty.style.marginLeft = '-' + gScreenWidth + 'px';
            // main panel
            document.getElementById('contentPanelQty').style.width = gScreenWidth + 'px';
            // component panel
            document.getElementById('contentPanelComponent').style.width = gScreenWidth + 'px';
            // subcomponent panel
            document.getElementById('contentPanelSubComponent').style.width = gScreenWidth + 'px';
            // component choice panel
            document.getElementById('contentPanelComponentChoice').style.width = gScreenWidth + 'px';
        }

        /**
         * showPreviousQty
         *
         * Reload the qty panel
         */
        function showPreviousQty()
        {
            // open the loading box
            showLoadingDialog();

            var postParams = '&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
';
            postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';
            processAjaxSmallScreen('backQty', ".?fsaction=AjaxAPI.callback&cmd=ORDERBACK", 'POST', postParams);
        }

        /**
         * setSameAddress
         *
         * Copy the shipping address
         */
        function setSameAddress()
        {
            if (document.getElementById("sameasshippingaddress").checked == false)
            {
                document.getElementById("changebilling").removeAttribute('disabled');
                document.getElementById("changeBillingLabel").className = "changeBtnText";
                document.getElementById("changeBillingImage").className = "changeBtnImg";
                document.getElementById("changebilling").onclick = function()
                {
                    return showChangeBillingAddress();
                };
                return true;
            }
            else
            {
                if (gAddressesMatch == true)
                {
                    document.getElementById("changebilling").setAttribute("disabled","disabled");
                    document.getElementById("changeBillingLabel").className = "changeBtnText disabled";
                    document.getElementById("changeBillingImage").className = "changeBtnImg disabled";

                    document.getElementById("changebilling").onclick = function()
                    {
                        return false;
                    };

                    return true;
                }
                else
                {
                    // open the loading box
                    showLoadingDialog();

                    var postParams = '&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
';
                    postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';
                    postParams += '&sameshippingandbillingaddress=' + document.getElementById("sameasshippingaddress").checked;

                    processAjaxSmallScreen('copyShippingAddress', ".?fsaction=AjaxAPI.callback&cmd=COPYSHIPPINGADDRESS", 'POST', postParams);
                }
            }
        }

        /**
         * showStoreInfo
         *
         * Show store information
         */
        function showStoreInfo(storeCode, pExternalStore)
        {
            var addressSearch = document.getElementById('searchText').value;

       		if (pExternalStore == 0)
			{
        		processAjaxSmallScreen("storeInfo",".?fsaction=AjaxAPI.callback&cmd=STOREINFORMATION&store=" + storeCode, 'GET', '');
			}
			else
			{
        		processAjaxSmallScreen("storeInfo",".?fsaction=AjaxAPI.callback&cmd=STOREINFORMATIONEXTERNAL&store=" + storeCode + "&filter=" + addressSearch, 'GET', '');
			}

        }

        /**
         * changeShippingAddress
         *
         * load the shipping address form
         */
        function changeShippingAddress(pMode)
        {
            var postParams = '&shippingratecode=' + getShippingRateCode();
            postParams += '&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
';
            postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';
            if (document.getElementById("sameasshippingaddress").checked == true)
            {
                 postParams += '&sameshippingandbillingaddress=1';
            }
            else
            {
                 postParams += '&sameshippingandbillingaddress=0';
            }

            if (document.getElementById("shippingcfscontact") && pMode == 'CFS')
            {
               var shippingcfscontact = document.getElementById("shippingcfscontact").value;
               postParams += '&shippingcfscontact=' + shippingcfscontact;
            }

            processAjaxSmallScreen("changeShippingAddressDisplay",".?fsaction=AjaxAPI.callback&cmd=CHANGESHIPPINGADDRESSDISPLAY", 'POST', postParams);
        }


        /**
         * changeBillingAddress
         *
         * load the billing address form
         */
        function showChangeBillingAddress()
        {
            if (document.getElementById('changebilling').getAttribute('disabled'))
            {
                return false;
            }
            else
            {
                setHashUrl('changeBillingAddress');
                return true;
            }
        }

        /**
         * changeBillingAddress
         *
         * load the billing address form
         */
        function changeBillingAddress()
        {
            var postParams = '&shippingratecode=' + getShippingRateCode();
            if (document.getElementById("sameasshippingaddress").checked == true)
            {
                postParams += '&sameshippingandbillingaddress=1';
            }
            else
            {
                postParams += '&sameshippingandbillingaddress=0';
            }

            postParams += '&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
';
            postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';

            processAjaxSmallScreen("changeBillingAddressDisplay",".?fsaction=AjaxAPI.callback&cmd=CHANGEBILLINGADDRESSDISPLAY", 'POST', postParams);
        }


        // Set the active component of radio button inputs
        function fnShippingMethodClick(pElement)
        {
            return shippingMethodClick(pElement.getAttribute("data-cfs") === 'true');
        }

        /**
         * shippingMethodClick
         *
         * Select the option clicked and update the session shipping details
         */
        function shippingMethodClick(pCollectFromStore)
        {
            // show loading dialog
            showLoadingDialog();
			var ajaxProcessCommand = 'changeshippingmethod';

			if (pCollectFromStore)
			{
            	gCollectFromStore = 1;
				ajaxProcessCommand = 'cfsfixedchangeshippingmethod';
			}
			else
			{
				gCollectFromStore = 0;
			}

            /* loop through all the shpping methods to see which one has been selected */
            var selectedShippingRateCode = '';
            var shippingmethodsLenghth = document.getElementsByName('shippingmethods').length;
            for (var i = 0; i < shippingmethodsLenghth; i++)
            {
                var elm = document.getElementsByName('shippingmethods')[i];
                var parent = elm.parentNode;
                if (elm.checked)
                {
                    selectedShippingRateCode = elm.value;
                    parent.classList.add('optionSelected');
                }
                else
                {
                    parent.classList.remove('optionSelected');
                }
                parent.getElementsByTagName('label')[0].getElementsByTagName('span')[0].innerHTML = '';
            }

            var postParams = '&shippingratecode=' + selectedShippingRateCode;
            postParams += '&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
';
            postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';
            postParams += '&fsactionorig=Order.changeShippingMethod';

            processAjaxSmallScreen(ajaxProcessCommand,".?fsaction=AjaxAPI.callback&cmd=CHANGESHIPPINGMETHOD", 'POST', postParams);
        }

        /**
         * selectStore
         *
         * Select the option clicked and update the session shipping details
         */
        function selectStore(pCode)
        {
            gCollectFromStore = 1;
            gCollectFromStoreCode = '';
            if (typeof gStoreCodes !== 'undefined')
            {
                gCollectFromStoreCode = gStoreCodes[pCode];
            }
            var previouslySelected = '';

            var selectedShippingRateCode = '';
            var shippingmethodsLenghth = document.getElementsByName('shippingmethods').length;
            for (var i = 0; i < shippingmethodsLenghth; i++)
            {
                var elm = document.getElementsByName('shippingmethods')[i];
                var parent = elm.parentNode;

                if (parent.classList.contains('optionSelected')) 
                {
                    previouslySelected = elm.value;
                }

                if (elm.checked)
                {
                    selectedShippingRateCode = elm.value;
                    parent.classList.add('optionSelected');

                    if ((gStoreAddresses[pCode] != '') && (pCode == previouslySelected))
                    {
                        parent.getElementsByTagName('label')[0].getElementsByTagName('span')[0].innerHTML = gStoreAddresses[pCode] + '<br />';
                    }
                }
                else
                {
                    parent.classList.remove('optionSelected');
                    parent.getElementsByTagName('label')[0].getElementsByTagName('span')[0].innerHTML = '';
                }
            }

            var removeStore = 'false';
            if ((pCode != previouslySelected))
            {
                gCollectFromStoreCode = '';
                if (document.getElementById('editStoreContactDetailsDiv')) {
                    document.getElementById('editStoreContactDetailsDiv').display = 'none';
                }
                removeStore = 'true';
                gStoreCodes[previouslySelected] = '';
                gStoreAddresses[previouslySelected] = '';
                gStoreCode = '';
                gCollectFromStoreCode = '';
            }
            
            var sameshippingandbillingaddress = document.submitform.sameshippingandbillingaddress.value;
            var getParams = '&shippingratecode=' + selectedShippingRateCode;
            getParams += '&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
';
            getParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';
            getParams += '&fsactionorig=Order.changeShippingMethod';
            getParams += '&refreshshipping=' + ((gActivePanel == 'contentPanel1') ? 'false' : 'true');
            getParams += '&sameshippingandbillingaddress=' + sameshippingandbillingaddress;
            getParams += '&removestore=' + removeStore;

            // send an ajax query
            processAjaxSmallScreen('storeLocatorForm', ".?fsaction=AjaxAPI.callback&cmd=SELECTSTOREDISPLAY" + getParams, 'POST');
        }

        function acceptDataEntry()
        {
			var shippingRateCode = getShippingRateCode();

            if (shippingRateCode.length == 0)
            {
                createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoShippingRate');?>
", function(e) {
                    closeDialog(e);
                });
                return false;
            }

    

    <?php if ($_smarty_tpl->tpl_vars['optionCFS']->value) {?>

        

            if ((gCollectFromStore == 1) && ((gCollectFromStoreCode == '') || (typeof gCollectFromStoreCode == 'undefined')))
            {
                createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleConfirmation');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoStore');?>
", function(e) {
                    closeDialog(e);
                });
                return false;
            }

        

    <?php }?> 
        
            // open the loading box
            showLoadingDialog();

            setHashUrl('payment');

            return false;
        }

        

    <?php }?> 
    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

        

            /**
             * initializeStage
             *
             * set the size of html elments on payment stage
             */
            function initializeStage(pInitialize)
            {

                

                    <?php echo $_smarty_tpl->tpl_vars['custominit']->value;?>


                    <?php echo $_smarty_tpl->tpl_vars['initlanguage']->value;?>


                

                // only two panels can be slide at one point
                document.getElementById('contentAjaxPayment').style.width = (gScreenWidth * 2) + 'px';

                // main panels width
                document.getElementById('contentPanelPayment').style.width = gScreenWidth + 'px';

                // set the component size
                var componentBloc = document.getElementsByClassName('componentBloc')[0];
                if (componentBloc)
                {
                    resizeComponentDesign('contentLeftScrollPayment', 'componentContentText', 'componentContentTextLong');
                }

                var container = document.getElementById('contentLeftScrollPayment');

                // method list width
                var paymentMethodList = container.getElementsByClassName('paymentMethodList')[0];
                if (paymentMethodList)
                {
                    // terms and condition label
                    var termsAndConditions = document.getElementById('labelTermsAndConditions');
                    if (termsAndConditions)
                    {
                        var orderTermsAndConditions = document.getElementById('ordertermsandconditions');
                        var styleOrderTermsAndConditions = orderTermsAndConditions.currentStyle || window.getComputedStyle(orderTermsAndConditions);
                        var checkBoxWidth = parseIntStyle(styleOrderTermsAndConditions.width);
                        if (styleOrderTermsAndConditions.paddingLeft != 'auto')
                        {
                            checkBoxWidth = checkBoxWidth + parseIntStyle(styleOrderTermsAndConditions.paddingLeft) + parseIntStyle(styleOrderTermsAndConditions.paddingRight);
                        }

                        if (styleOrderTermsAndConditions.marginLeft != 'auto')
                        {
                            checkBoxWidth = checkBoxWidth + parseIntStyle(styleOrderTermsAndConditions.marginLeft) + parseIntStyle(styleOrderTermsAndConditions.marginRight);
                        }

                        document.getElementById('labelTermsAndConditions').style.width = (gOuterBoxContentBloc - checkBoxWidth) + 'px';
                    }

                    // tick image width
                    if (gTickImageWidth == 0 )
                    {
                        var checkboxImage = container.getElementsByClassName('checkboxImage')[0];
                        var styleCheckboxImage = checkboxImage.currentStyle || window.getComputedStyle(checkboxImage);
                        gTickImageWidth =  parseIntStyle(styleCheckboxImage.width) + parseIntStyle(styleCheckboxImage.marginLeft) + parseIntStyle(styleCheckboxImage.marginRight);
                    }

                    // set the width of the payment list text
                    var width = gOuterBoxContentBloc - gTickImageWidth;
                    var classLength = container.getElementsByClassName('paymentMethod').length;
                    for (var i = 0; i < classLength; i++)
                    {
                        var elm = container.getElementsByClassName('paymentMethod')[i];

                        if (i == 0)
                        {
                            var styleLabel = elm.currentStyle || window.getComputedStyle(elm);
                            width = width - parseIntStyle(styleLabel.paddingLeft) - parseIntStyle(styleLabel.paddingRight);
                        }
                        elm.style.width = width + 'px';
                    }
                }
                else
                {
                    // terms and condition label
                    var termsAndConditions = document.getElementById('labelTermsAndConditions');
                    if (termsAndConditions)
                    {
                        var orderTermsAndConditions = document.getElementById('ordertermsandconditions');
                        var styleOrderTermsAndConditions = orderTermsAndConditions.currentStyle || window.getComputedStyle(orderTermsAndConditions);
                        var checkBoxWidth = parseIntStyle(styleOrderTermsAndConditions.width);
                        if (styleOrderTermsAndConditions.paddingLeft != 'auto')
                        {
                            checkBoxWidth = checkBoxWidth + parseIntStyle(styleOrderTermsAndConditions.paddingLeft) + parseIntStyle(styleOrderTermsAndConditions.paddingRight);
                        }

                        if (styleOrderTermsAndConditions.marginLeft != 'auto')
                        {
                            checkBoxWidth = checkBoxWidth + parseIntStyle(styleOrderTermsAndConditions.marginLeft) + parseIntStyle(styleOrderTermsAndConditions.marginRight);
                        }

                        // set the width of terms and condition lable
                        document.getElementById('labelTermsAndConditions').style.width = (gOuterBoxContentBloc - checkBoxWidth) + 'px';
                    }
                }

                // product label
                var productPrice = document.getElementById('productPricepayment');
                var styleProductPrice = productPrice.currentStyle || window.getComputedStyle(productPrice);
                var productPriceWidth = parseIntStyle(styleProductPrice.width);
                document.getElementById('productNamepayment').style.width = (gOuterBoxContentBloc - productPriceWidth) + 'px';

                // shipping label
                var shippingPrice = document.getElementById('shippingPrice');
                var styleShippingPrice = shippingPrice.currentStyle || window.getComputedStyle(shippingPrice);
                var shippingPriceWidth = parseIntStyle(styleProductPrice.width);
                document.getElementById('shippingLabel').style.width = (gOuterBoxContentBloc - shippingPriceWidth) + 'px';

                // total section label
                var classLength = container.getElementsByClassName('totalLabel').length;
                var width = gOuterBoxContentBloc;
                for (var i = 0; i < classLength; i++)
                {
                    var elm = container.getElementsByClassName('totalLabel')[i];
                    if (i == 0)
                    {
                        var totalNumber = container.getElementsByClassName('totalNumber')[0];
                        var styleNumber = totalNumber.currentStyle || window.getComputedStyle(totalNumber);
                        width = width - parseIntStyle(styleNumber.width);
                    }
                    elm.style.width = width + 'px';
                }

                document.getElementById('contentPanelPayment').style.display = 'block';

                setScrollAreaHeight('contentLeftScrollPayment', 'paymentBack');

                // set the metadata design
                setMetadataDesign('contentAjaxPayment');

				// close loading dialog
				closeLoadingDialog();

                

                    <?php if ((($_smarty_tpl->tpl_vars['showgiftcardmessage']->value == 1) && ($_smarty_tpl->tpl_vars['stage']->value == 'payment'))) {?>

                displayGiftCardAlert('<?php echo $_smarty_tpl->tpl_vars['voucherstatusResult']->value;?>
', '<?php echo $_smarty_tpl->tpl_vars['vouchercustommessage']->value;?>
');

                    <?php }?>

                

            }

            /**
             * orderTermsAndConditions
             *
             * get the template to display for terms and conditions
             */
            function orderTermsAndConditions()
            {
                processAjaxSmallScreen('termsandconditionswindow', ".?fsaction=AjaxAPI.callback&cmd=TERMSANDCONDITIONS&template=ORDER", 'GET', '');
                return false;
            }

            /**
             * showPreviousShipping
             *
             * Save the current datas before loading the shipping panel
             */
            function showPreviousShipping()
            {
                // open the loading box
                showLoadingDialog();

                var postParams = '&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
';
                postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';
                postParams += '&paymentmethodcode=' + getPaymentMethodCode();
                postParams += '&paymentgatewaycode=' + getPaymentGatewayCode();

                // get all metadata fields and submit to the server
                var contentPanelPayment = document.getElementById('contentPanelPayment');
                var metadataLegnth = contentPanelPayment.getElementsByClassName('componentMetadata').length;
                for (var i = 0; i < metadataLegnth; i++)
                {
                    componentMetadata = contentPanelPayment.getElementsByClassName('componentMetadata')[i];
                    inputs = componentMetadata.getElementsByTagName('input');
                    selects = componentMetadata.getElementsByTagName('select');
                    textareas = componentMetadata.getElementsByTagName('textarea');

                    // get metadata values for all inputs on the page
                    for (var j = 0; j < inputs.length; j++)
                    {
                        switch (inputs[j].type)
                        {
                            case 'text':
                                postParams += '&' + inputs[j].name + '=' + inputs[j].value;
                                break;
                            case 'radio':
                                postParams += '&' + inputs[j].name + '=' + inputs[j].value;
                                break;
                            case 'checkbox':
                                postParams += '&' + inputs[j].name + '=' + (inputs[j].checked) ? '1' : '0';
                                break;
                        }
                    }

                    // get metadata values for all selects on the page
                    for (var j = 0; j < selects.length; j++)
                    {
                        postParams += '&' + selects[j].name + '=' + selects[j].options[selects[j].selectedIndex].value;
                    }

                    // get metadata values for all textareas on the page
                    for (var j = 0; j < textareas.length; j++)
                    {
                        postParams += '&' + textareas[j].name + '=' + textareas[j].value;
                    }
                }

                // send the query
                processAjaxSmallScreen('backShipping', ".?fsaction=AjaxAPI.callback&cmd=ORDERBACK", 'POST', postParams);
            }


            /**
             * initializeConfimation
             *
             * Set html elemnts on confimation panel
             */
            function initializeConfimation()
            {
                setScrollAreaHeight('contentConfirmation', '');

                closeLoadingDialog();
            }

            /**
             * setGiftCard
             *
             * Apply a gift card
             */
            function setGiftCard()
            {
                // show the loading dialog
                showLoadingDialog();

                // save metadata
                saveTempMetadata();

                var postParams = '&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
';
                postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';
                postParams += '&paymentmethodcode=' + getPaymentMethodCode();
                postParams += '&paymentgatewaycode=' + getPaymentGatewayCode();
                postParams += '&giftcardcode=' + document.getElementById('giftcardcode').value;
                postParams += '&showgiftcardmessage=' + 1;

                // send an ajax query
                processAjaxSmallScreen('setGiftCard', ".?fsaction=AjaxAPI.callback&cmd=SETGIFTCARD", 'POST', postParams);
            }

            /**
             * changeGiftCard
             *
             * Remove or add a gift card
             */
            function changeGiftCard()
            {
                var giftcard = document.getElementById("giftcard");
                if (giftcard)
                {
                    // show the loading dialog
                    showLoadingDialog();

                    // save metadata
                    saveTempMetadata();

                    var add_delete = (giftcard.className.indexOf('disabled') > -1) ? 'add' : 'delete';
                    processAjaxSmallScreen("changegiftcard",".?fsaction=AjaxAPI.callback&cmd=CHANGEGIFTCARD&action=" + add_delete, 'POST', '');
                }
            }

            /**
             * setVoucher
             *
             * Apply a voucher
             */
            function setVoucher()
            {
                // show the loading dialog
                showLoadingDialog();

                // save metadata
                saveTempMetadata();

                var postParams = '&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
';
                postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';
                postParams += '&paymentmethodcode=' + getPaymentMethodCode();
                postParams += '&paymentgatewaycode=' + getPaymentGatewayCode();
                postParams += '&vouchercode=' + document.getElementById('vouchercode').value;
                postParams += '&showgiftcardmessage=' + 1;

                // send an ajax query
                processAjaxSmallScreen('setVoucher', ".?fsaction=AjaxAPI.callback&cmd=SETVOUCHER", 'POST', postParams);
            }


            /**
             * removeVoucher
             *
             * Remove a voucher
             */
            function removeVoucher()
            {
                // show the loading dialog
                showLoadingDialog();

                // save metadata
                saveTempMetadata();

                var postParams = '&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
';
                postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';
                postParams += '&paymentmethodcode=' + getPaymentMethodCode();
                postParams += '&paymentgatewaycode=' + getPaymentGatewayCode();
                postParams += '&vouchercode=';
                postParams += '&showgiftcardmessage=' + 1;

                // send an ajax query
                processAjaxSmallScreen('setVoucher', ".?fsaction=AjaxAPI.callback&cmd=SETVOUCHER", 'POST', postParams);
            }

            function validatePayType(pAlertMessage)
            {
                var paytypeObject = document.getElementById('paymentgatewaycode');
                var paytypeValue =  paytypeObject.options[paytypeObject.selectedIndex].value;

                if (paytypeValue == '')
                {
                    createDialog("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleWarning');?>
", pAlertMessage, function(e) {
                        closeDialog(e);
                    });
                }
                else
                {
                    var metadataValidity = checkMetadataValidity('contentPanelPayment', true);
                    if (metadataValidity.hasAnError == false)
                    {
                        // open the loading box
                        showLoadingDialog();

                        var postParams = metadataValidity.postParams;

                        postParams += '&previousstage=<?php echo $_smarty_tpl->tpl_vars['previousstage']->value;?>
';
                        postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';
                        postParams += '&paymentmethodcode=' + getPaymentMethodCode();
                        postParams += '&paymentgatewaycode=' + paytypeValue;

						setHashUrl('paymentgateway');

                        processAjaxSmallScreen('showPaymentgateway', ".?fsaction=AjaxAPI.callback&cmd=ORDERCONTINUE", 'POST', postParams);
                    }
                }
            }

        

    <?php }?> 
    
<?php } else { ?> 
    
    

	/**
	 * This function also exists in the PayPalPlusPaymentWall.tpl template.
	 * Any changes made to this function will also need to be applied to that function.
	 */
    function cancelOrder()
    {

    

    <?php if ($_smarty_tpl->tpl_vars['sessionrevived']->value == true) {?>

        

            document.submitform.fsaction.value = "Order.cancel";
            document.submitform.submit();

        

    <?php } else { ?> 
        

            var confirmCancel = confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ConfirmCancellation');?>
");
            if (confirmCancel)
            {
                document.submitform.fsaction.value = "Order.cancel";
                document.submitform.submit();
            }

        

    <?php }?> 
    

            return false;
    }

    function setSystemLanguage()
    {
        changeSystemLanguage("Order.refresh", "submitform", 'post');
    }

    

    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'qty') {?>

        

        function setComponentItemActive(objId)
        {
            var elemObj = document.getElementById(objId);
            var allElems = document.getElementById('previewHolderCart').getElementsByTagName('div');
            for (var i = 0, loopElem; i < allElems.length; i++)
            {
                loopElem = allElems[i];
                if (loopElem.className.indexOf('previewItemHolder') > -1)
                {
                    loopElem.className = loopElem.className.replace(' selected', '');
                }
            }
            var elemBox = document.getElementById('holder_' + objId);
            if (elemBox)
            {
                elemBox.className = elemBox.className + ' selected';
            }
        }

        function duplicateOrderLine(pOrderLineId)
        {

        

            <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>


        

            document.submitform.action = "index.php";
            document.submitform.itemqty.value = "";
            document.submitform.orderlineid.value = pOrderLineId;
            document.submitform.fsaction.value = "Order.duplicate";
            document.submitform.submit();
            return false;
        }

        function removeOrderLine(pOrderLineId)
        {
            var confirmRemove = confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ConfirmRemove');?>
");
            if (confirmRemove)
            {

        

            <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>


        
                document.submitform.action = "index.php";
                document.submitform.itemqty.value = "";
                document.submitform.orderlineid.value = pOrderLineId;
                document.submitform.fsaction.value = "Order.remove";
                document.submitform.submit();
            }
        }

        /* close component selection modal "window" */
        function closeWindow()
        {
            var shimObj = document.getElementById('shim');
            var componentChangeBoxObj = document.getElementById('componentChangeBox');
            if (shimObj)
            {
                shimObj.style.display = 'none';
            }
            if (componentChangeBoxObj)
            {
                componentChangeBoxObj.style.display = 'none';
            }
            document.body.className = document.body.className.replace(' hideSelects', '');

			if (document.documentElement.style.overflow == 'hidden')
			{
				document.documentElement.style.overflow = '';
			}

            return false;
        }

        /* change a component in the session by Ajax   */
        /* component has been selected in the "pop up" */
        /* then refresh the entire orderline by Ajax   */
        function selectComponent(orderlineid, section)
        {
			gAjaxInProgress = true;

            var componentCode = "";
            var localComponentCode = "";
            var radioId = "components_" + orderlineid;
            var arr = new Array();
            arr = document.getElementsByName(radioId);
            for (var i = 0; i < arr.length; i++)
            {
                var obj = document.getElementsByName(radioId).item(i);
                if (obj.checked)
                {
                    componentCode = obj.value;
                    localComponentCode = obj.getAttribute("localcode");
                    break;
                }
            }
            if (componentCode == "")
            {
                alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoComponent2');?>
");
                return false;
            }

        

            <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>


        

            /* check to see if we are updating the orderfooter */
            if (orderlineid == -1)
            {
                obj = "orderFooter";
            }
            else
            {
                obj = "ordertableobj_" + orderlineid;
            }
            /* refresh order line */
            processAjax(obj, ".?fsaction=AjaxAPI.callback&cmd=UPDATECOMPONENTLARGE&orderlineid=" + orderlineid + "&section=" + section + "&code=" + componentCode + "&localcode=" + localComponentCode, "POST", "");
			document.documentElement.style.overflow = '';
            closeWindow();
            return false;
        }

        function updateCheckbox(orderLineId, componentId)
        {
            /* temp save metadata */
            saveTempMetadata();

    

    <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>


    

            if (orderLineId == -1)
            {
                obj = "orderFooter";
            }
            else
            {
                obj = "ordertableobj_" + orderLineId;
            }

			gAjaxInProgress = true;

            /* refresh order line */
            processAjax(obj,".?fsaction=AjaxAPI.callback&cmd=UPDATECHECKBOXLARGE&orderlineid=" + orderLineId + "&componentid=" + componentId, 'POST', '');
            return false;
        }

        /* display a "pop up" that lists all available components so the user can make a choice */
        function changeComponent(item, section)
        {

    

    <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>


    

            /* temp save metadata */
            saveTempMetadata();
            gLoadedComponentsImagesCount = 0;
            gComponentImagesCount = 0;

			/* prevent scrolling main document while dialog is open */
			document.documentElement.style.overflow = 'hidden';

            processAjax("componentChangeBox",".?fsaction=AjaxAPI.callback&cmd=CHANGECOMPONENTLARGE&item=" + item + "&section=" + section, 'GET', '');
            return false;
        }

        function updateComponentQty(componentOrderLineID, prodQty, quantityValue, processExecute)
        {
            if (document.submitform.itemqty.value != "-")
            {
                var validQty = true;
                var itemQtyField = 'itemqty_' + componentOrderLineID;

                /* check to see if we are dealing witht the order footer */
                var orderLinePrefix = componentOrderLineID.substr(0,componentOrderLineID.indexOf('_'));

                if (orderLinePrefix == '-')
                {
                    var orderLineId = -1;
                }
                else
                {
                    var orderLineId = orderLinePrefix;
                }

                if (quantityValue == undefined)
                {
                    var newQty = string2integer(document.getElementById('itemqty_' + componentOrderLineID).value);
                }
                else
                {
                    var newQty = string2integer(quantityValue);
                }

                if (isNaN(newQty))
                {
                    validQty = false;
                }
                else if (newQty > 99999999)
                {
                    validQty = false;
                }

                if (orderLineId == -1)
                {
                    obj = "orderFooter";
                }
                else
                {
                    obj = "ordertableobj_" + orderLineId;
                }

                if (validQty)
                {
                    /* refresh order line */
                    if(processExecute == undefined)
                    {
                        saveTempMetadata();

        

        <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>


        
                         processAjax(obj,".?fsaction=AjaxAPI.callback&cmd=UPDATECOMPONENTQTYLARGE&orderlineid=" + orderLineId + "&componentorderlineid=" + componentOrderLineID + "&componentitemqty=" + newQty + "&itemqty=" + prodQty, 'POST', '');

                    }
                    else
                    {
                    	if( gComponentToUpdate != '')
                    	{
                        	 gComponentToUpdate += ',';
                        }
                        gComponentToUpdate += '{"content":"' + obj + '",' + '"orderline":"' + orderLineId + '","qty":' + newQty + ',"componentorderlineid":"' + componentOrderLineID + '", "prodqty":' + prodQty + '}';
                    }
                }
                else
                {
                    alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorInvalidQty');?>
");
                }
            }
            else
            {
                alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoPrice');?>
");
            }
            return false;
        }

        function saveTempMetadata()
        {
            /* on submit get all metadata fields and submit to the server */
            var postParams = [];
            var orderContentHolder = document.getElementById('orderContent');

            if (orderContentHolder)
            {
                var lis = orderContentHolder.getElementsByTagName('div');
                var liCount = lis.length;
                for (var i = 0, inputs, selects, textareas, hiddenField, li; i < liCount; i++)
                {
                    li = lis[i];
                    if (li.className.indexOf('component-metadata') > -1)
                    {
                        inputs = li.getElementsByTagName('input');
                        selects = li.getElementsByTagName('select');
                        textareas = li.getElementsByTagName('textarea');

                        /* get metadata values for all inputs on the page */
                        for (var j = 0, hiddenFieldValue; j < inputs.length; j++)
                        {
                            switch (inputs[j].type)
                            {
                                case 'text':
                                    postParams.push(inputs[j].name + '=' + encodeURIComponent(inputs[j].value));
                                    break;
                                case 'radio':
                                    if (inputs[j].checked)
                                    {
                                        postParams.push(inputs[j].name + '=' + encodeURIComponent(inputs[j].value));
                                    }
                                    break;
                                case 'checkbox':
                                    postParams.push(inputs[j].name + '=' + ((inputs[j].checked) ? '1' : '0'));
                                    break;
                            }
                        }

                        /* get metadata values for all selects on the page */
                        for (var j = 0, hiddenFieldValue; j < selects.length; j++)
                        {
                            postParams.push(selects[j].name + '=' + encodeURIComponent(selects[j].options[selects[j].selectedIndex].value));
                        }

                        /* get metadata values for all textareas on the page */
                        for (var j = 0, hiddenFieldValue; j < textareas.length; j++)
                        {
                            postParams.push(textareas[j].name + '=' + encodeURIComponent(textareas[j].value));
                        }
                    }
                }

                postParams = postParams.join('&');
				postParams += '&stage=<?php echo $_smarty_tpl->tpl_vars['stage']->value;?>
';
                processAjax("savemetadata",".?fsaction=AjaxAPI.callback&cmd=SAVETEMPMETADATA", 'POST', postParams);
            }
        }

        

    <?php }?> 
    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'shipping') {?>

        

        function setSameAddress()
        {
            if (document.getElementById("sameasshippingaddress").checked == false)
            {
                document.getElementById("changebilling").removeAttribute('disabled');

                document.getElementById("changeBillingBtnLeft").className = "btn-white-left";
                document.getElementById("changeBillingBtnMiddle").className = "btn-white-middle";
                document.getElementById("changeBillingBtnRight").className =  "btn-white-right";

                return true;
            }
            else
            {
                if (gAddressesMatch == true)
                {
                    document.getElementById("changebilling").setAttribute("disabled","disabled");

                    document.getElementById("changeBillingBtnLeft").className = "btn-disabled-left";
                    document.getElementById("changeBillingBtnMiddle").className = "btn-disabled-middle";
                    document.getElementById("changeBillingBtnRight").className =  "btn-disabled-right";

                    return true;
                }
                else
                {
    

    <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>


    
                    document.submitform.sameshippingandbillingaddress.value = document.getElementById("sameasshippingaddress").checked;
                    document.submitform.fsaction.value = "Order.copyShippingAddress";
                    document.submitform.submit();
                    return false;
                }
            }
        }

        function showStoreInfo(storeCode, pExternalStore)
        {
            var addressSearch = document.getElementById('searchText').value;

			if (pExternalStore == 0)
			{
        		processAjax("storeInfo",".?fsaction=AjaxAPI.callback&cmd=STOREINFORMATION&store=" + storeCode, 'GET', '');
			}
			else
			{
        		processAjax("storeInfo",".?fsaction=AjaxAPI.callback&cmd=STOREINFORMATIONEXTERNAL&store=" + storeCode + "&filter=" + addressSearch, 'GET', '');
			}
        }

        function closeStoreInfo()
        {
            var storeInfo = document.getElementById('storeInfo');
            if (storeInfo)
            {
                storeInfo.style.display = 'none';
            }
            var storeLocator = document.getElementById('storeLocator');
            if (storeLocator)
            {
                storeLocator.style.zIndex = 200;
            }
            return false;
        }

        function acceptDataEntry()
        {
			if (gChangeMethodInPorgress == true)
			{
				alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageChangedShippingMethod');?>
");
                return false;
			}

            var shippingRateCode = getShippingRateCode();

            if (shippingRateCode.length == 0)
            {
                alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoShippingRate');?>
");
                return false;
            }

    

    <?php if ($_smarty_tpl->tpl_vars['optionCFS']->value) {?>

        

            if ((gCollectFromStore == 1) && (gCollectFromStoreCode == '' || gStoreAddresses[gShippingRateCode] == ''))
            {
                alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoStore');?>
");
                return false;
            }

        

    <?php }?> 
        <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>


    

            document.submitform.shippingratecode.value = shippingRateCode;
            document.submitform.fsaction.value = "Order.continue";
            document.submitform.submit();

            return false;
        }

    

    <?php }?> 
    <?php if ($_smarty_tpl->tpl_vars['stage']->value == 'payment') {?>

        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['paymentgatewayjavascriptarray']->value, 'gatewayscriptarray');
$_smarty_tpl->tpl_vars['gatewayscriptarray']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['gatewayscriptarray']->value) {
$_smarty_tpl->tpl_vars['gatewayscriptarray']->do_else = false;
?>
            <?php echo $_smarty_tpl->tpl_vars['gatewayscriptarray']->value['script'];?>

        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

        

            function orderTermsAndConditions()
            {
                processAjax('termsandconditionswindow', ".?fsaction=AjaxAPI.callback&cmd=TERMSANDCONDITIONS&template=ORDER", 'GET', '');
            }

            function setVoucher()
            {

        

                <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>


        
                document.submitform.paymentmethodcode.value = getPaymentMethodCode();
                document.submitform.paymentgatewaycode.value = getPaymentGatewayCode();
                document.submitform.vouchercode.value = document.orderform.vouchercode.value;
                document.submitform.fsaction.value = "Order.setVoucher";
                document.submitform.showgiftcardmessage.value = 1;
                document.submitform.submit();
                return false;
            }

            function changeGiftCard()
            {
                var giftcard = document.getElementById("giftcard");
                if (giftcard)
                {
                    var csrfToken = fetchCsrfToken();
                    var add_delete = (giftcard.className.indexOf('disabled')>-1) ? 'add' : 'delete';
                    processAjax("changegiftcard",".?fsaction=Order."+add_delete+"GiftCard", 'POST', '');
                }
            }

            function setGiftCard()
            {

        

                <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>


        

                document.submitform.paymentmethodcode.value = getPaymentMethodCode();
                document.submitform.paymentgatewaycode.value = getPaymentGatewayCode();
                document.submitform.giftcardcode.value = document.orderform.giftcardcode.value;
                document.submitform.fsaction.value = "Order.setGiftCard";
                document.submitform.showgiftcardmessage.value = 1;
                document.submitform.submit();

                return false;
            }

            function removeVoucher()
            {

        

        <?php echo $_smarty_tpl->tpl_vars['metadatasubmit']->value;?>


        

                document.submitform.vouchercode.value = "";
                document.submitform.fsaction.value = "Order.setVoucher";
                document.submitform.submit();
                return false;
            }


            function validatePayType(pAlertMessage)
            {
                paytype = document.getElementsByName('paymentgatewaycode').item(0);

                if (paytype.value == '')
                {
                    alert(pAlertMessage);
                }
                else
                {
                    document.submitform.paymentgatewaycode.value = paytype.value;
                    document.submitform.submit();
                }
            }

        

    <?php }?> 
	
		function sizeDialog(pDialogObj, pDialogContentID)
		{
			if (pDialogObj == null)
			{
				return;
			}

			var contentContainerObj = getDialogElementByClass('dialogContentContainer', pDialogObj);

			if (contentContainerObj == null)
			{
				return;
			}

			var windowHeight = document.documentElement.clientHeight;

			// reset height styles
			pDialogObj.style.maxHeight = '';
			contentContainerObj.style.height = '';

			if (pDialogObj.clientHeight > windowHeight)
			{
				// dialog is bigger than browser window height so size it down and scroll content

				pDialogObj.style.maxHeight = windowHeight + 'px';

				// calculate required height for the container
				var dialogTopObj = getDialogElementByClass('dialogTop', pDialogObj);
				var dialogButtonObj = getDialogElementByClass('buttonBottomInside', pDialogObj);
				var dialogContentObj = document.getElementById(pDialogContentID);
				var dialogContentObjStyle = dialogContentObj.currentStyle || window.getComputedStyle(dialogContentObj);
				var dialogContentObjMargin = (parseInt(dialogContentObjStyle.marginTop) + parseInt(dialogContentObjStyle.marginBottom)) * 2;
				contentContainerObj.style.height = (pDialogObj.offsetHeight - dialogTopObj.offsetHeight - dialogButtonObj.offsetHeight - dialogContentObjMargin) + 'px';
				contentContainerObj.scrollTop = 0;

				// increase the width of the dialog box in case we need scrollbars
				// to prevent scrollbars breaking the layout on Windows
				var scrollbarWidth = contentContainerObj.offsetWidth - contentContainerObj.clientWidth;

				// needed to size dialogs correctly
				pDialogObj.style.width = (pDialogObj.offsetWidth) + 'px';
				contentContainerObj.style.width = (contentContainerObj.offsetWidth) + 'px';

				if (scrollbarWidth > 0)
				{
					pDialogObj.style.width = (pDialogObj.offsetWidth + scrollbarWidth) + 'px';
					contentContainerObj.style.width = (contentContainerObj.offsetWidth + scrollbarWidth) + 'px';
					dialogButtonObj.style.width = contentContainerObj.offsetWidth;
				}
			}
			else
			{
				// remove height if we have enough room

				pDialogObj.style.maxHeight = '';
				pDialogObj.style.width = '';
				contentContainerObj.style.height = '';
				contentContainerObj.style.width = '';
				getDialogElementByClass('buttonBottomInside', pDialogObj).style.width = '';
			}
		}

		function getDialogElementByClass(pClassName, pDialog)
		{
			if (document.querySelectorAll)
			{
				return pDialog.querySelectorAll('.' + pClassName)[0];
			}
			else
			{
				var childNodes = pDialog.childNodes;
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
	

	
<?php }?> <?php }
}
