{* COMMON JAVSCRIPT (LARGE/SMALL *}
        /* set a cookie to store the local time */
        var theDate = new Date();
        createCookie("mawebtz", Math.round(theDate.getTime() / 1000), 2);

        var gOrderStage = "{$currentstage}";

        var gVoucherSection = "{$vouchersection}";
        var discountName = "{$itemdiscountname}";
        var proceedAjax = 0;

        var gSession = "{$ref}";
        var gLoadedComponentsImagesCount = 0;
        var gComponentImagesCount = 0;
		var gContinueOrderTimeout;

{if $stage == 'qty'}

    {assign var='modalHeight' value='348' scope='root'}
    {assign var='modalWidth' value='650' scope='root'}

        var gOrderCanContinue = new Object();
        gOrderCanContinue.ordercancontinue = {$ordercancontinue};
        var gOrderData = new Array();
        var gOrderComponentData = new Array();
        var gProductToUpdate = '';
        var gComponentToUpdate = '';
        var gCountProduct = 0;
        var gCountComponent = 0;
		var gAjaxInProgress = false;

{/if} {* end {if $stage == 'qty'} *}


{if $stage == 'shipping'}

        var gShippingRateCode = "{$shippingratecode}";
        var gAddressesMatch = {$addressesmatch};
        var gCollectFromStore = {$collectFromStore};
        var gCollectFromStoreCode = "{$collectFromStoreCode}";
		var gChangeMethodInPorgress = false;

	{foreach from=$storeaddresses key=k item=v name=stores}

        {if $smarty.foreach.stores.first}

        var gStoreAddresses = new Object();

        {/if} {* end {if $smarty.foreach.stores.first} *}

        gStoreAddresses['{$k}'] = "{$v}";

	{/foreach} {* end {foreach from=$storeaddresses key=k item=v name=stores} *}

	{foreach from=$storefixedlist key=k item=v name=stores2}

        {if $smarty.foreach.stores2.first}

        var gStoreFixed = new Object();

        {/if} {* end {if $smarty.foreach.stores2.first} *}

        gStoreFixed['{$k}'] = '{$v}';

	{/foreach} {* end {foreach from=$storefixedlist key=k item=v name=stores2} *}

	{foreach from=$storecodelist key=k item=v name=stores3}

        {if $smarty.foreach.stores3.first}

        var gStoreCodes = new Object();

        {/if}  {* end {if $smarty.foreach.stores3.first} *}

        gStoreCodes['{$k}'] = '{$v}';

	{/foreach} {* end {foreach from=$storecodelist key=k item=v name=stores3} *}

{/if} {* end {if $stage == 'shipping'} *}

{if $stage == 'payment'}

        var gPaymentMethodCode = "{$paymentmethodcode}";
        var gCanUseAccount = {$canuseaccount};

{/if} {* end {if $stage == 'payment'} *}

{if ($stage == 'qty') or ($stage == 'payment')}

    {literal}

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

            for( radio in radios)
            {
                var add = true;
                for (var x=0; x < radios[radio].length; x++)
                {
                    if (radios[radio][x].checked)
                    {
                        add = false;
                    }
                }

                if(add)
                {
                    defaultstillselected.push(radios[radio]);
                }

            }

            for( select in selects)
            {
                var add = true;
                for (var x=0; x < selects[select].length; x++)
                {
                    if (selects[select][x].value!="")
                    {
                        add = false;
                    }
                }

                if(add)
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

    {/literal}

{/if} {* end {if $stage == 'qty' or $stage == 'payment'} *}

{if $stage == 'qty'}

    {literal}

        function acceptDataEntry(alertOff)
        {

	{/literal}

	{if $issmallscreen != 'true'}

		{literal}

			if (gAjaxInProgress == true)
			{
				alert("{/literal}{#str_MessageOptionsOrItemChanged#}{literal}");
				return false;
			}

		{/literal}

	{/if}

    {if $lockqty != true}

        {literal}

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

            {/literal}

            {if $issmallscreen == 'true'}

                {literal}

                            createDialog("{/literal}{#str_LabelInformation#}{literal}", "{/literal}{#str_MessageQuantityChanged#}{literal}", "closeDialog()");

                {/literal}

            {else}

                {literal}

                            alert("{/literal}{#str_MessageQuantityChanged#}{literal}");

                {/literal}

            {/if}

            {literal}

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

                                {/literal}

                                {if $issmallscreen == 'true'}

                                    {literal}

                                createDialog("{/literal}{#str_LabelInformation#}{literal}", "{/literal}{#str_MessageQuantityChanged#}{literal}", "closeDialog()");

                                    {/literal}

                                {else}

                                    {literal}

                                alert("{/literal}{#str_MessageQuantityChanged#}{literal}");

                                    {/literal}

                                {/if}

                                {literal}

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

        {/literal}

                {$metadatasubmit}

                {if $issmallscreen == 'false'}

                    {literal}

            	postParams = '&product=[' + gProductToUpdate + ']';
                postParams += '&component=[' + gComponentToUpdate + ']';
                processAjax('updateorderqtyall','.?fsaction=AjaxAPI.callback&cmd=UPDATEORDERQTYALLLARGE&ref=' + gSession, 'POST', postParams);


                    {/literal}

                {else} {* if $issmallscreen == 'false'*}

                    {literal}

                showLoadingDialog();

            	postParams = '&product=[' + gProductToUpdate + ']';
                postParams += '&component=[' + gComponentToUpdate + ']';
                processAjaxSmallScreen("updateorderqtyall",".?fsaction=AjaxAPI.callback&cmd=UPDATEORDERQTYALLSMALL&ref=" + gSession, 'POST', postParams);

                    {/literal}

                {/if}

        {literal}

                gProductToUpdate = '';
                gComponentToUpdate = '';
                return false;
            }

        {/literal}

    {/if} {* {if $lockqty != true} *}

    {literal}

            /* test if all order items have a price */
            /* loop over global order data array */
            for (var idx in gOrderData)
            {
                if (idx != -1)
                {
                    /* is there a product price? */
                    if (gOrderData[idx].hasproductprice == "0")
                    {

    {/literal}

    {if $issmallscreen == 'true'}

        {literal}

                        createDialog("{/literal}{#str_TitleWarning#}{literal}", "{/literal}{#str_ErrorNoPrice#}{literal}", "closeDialog()");

        {/literal}

    {else}

        {literal}

                        alert("{/literal}{#str_ErrorNoPrice#}{literal}");

        {/literal}

    {/if}

    {literal}

                        return false;
                    }
                }
            }

            /* test if all required components have a price */
            if (gOrderCanContinue.ordercancontinue == 0)
            {

    {/literal}

    {if $issmallscreen == 'true'}

        {literal}

                createDialog("{/literal}{#str_TitleWarning#}{literal}", "{/literal}{#str_ErrorNoComponent#}{literal}", "closeDialog()");

        {/literal}

    {else}

        {literal}

                alert("{/literal}{#str_ErrorNoComponent#}{literal}");

        {/literal}

    {/if}

    {literal}

                return false;
            }

    {/literal}

    {if $issmallscreen == 'true'}

        {literal}

            var metadataValidity = checkMetadataValidity('contentAjaxQty', true);
            if (metadataValidity.hasAnError == false)
            {
                // open the loading box
                showLoadingDialog();

                setHashUrl('shipping');
            }

        {/literal}

    {else} {* else {if $issmallscreen == 'true'} *}

        {$metadatasubmit}

        {literal}

            //Limits the number of times the alert is called i.e. once for one or more empty meta entries
            var alertOn = true;

            //If come from onblur event then turn the error messaging off.
            if(alertOff == false)
            {
                alertOn = false;
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
                                            alert("{/literal}{#str_ErrorValueRequired#}{literal}");
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

                                            txtInput.onblur = function() {
                                                acceptDataEntry(false);
                                            };
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
                                            alert("{/literal}{#str_ErrorValueRequired#}{literal}");
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

                                            //If at least one input is checked then radioChecker = true
                                            for(var k = 0; k < radioInputs.length; k++)
                                            {
                                                radioInputs[k].onchange = function() {
                                                    acceptDataEntry(false);
                                                };
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
                                    alert("{/literal}{#str_ErrorValueRequired#}{literal}");
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

                                    txtInput.onchange = function() {
                                        acceptDataEntry(false);
                                    };
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
                                    alert("{/literal}{#str_ErrorValueRequired#}{literal}");
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

                                    txtInput.onblur = function() {
                                        acceptDataEntry(false);
                                    };
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

        {/literal}

    {/if} {* end {if $issmallscreen == 'true'} *}

    {literal}

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
                alert("{/literal}{#str_ErrorNoComponent2#}{literal}");
                return false;
            }

            /* temp save metadata */
            saveTempMetadata();

    {/literal}

    {$metadatasubmit}

    {literal}

            /* refresh order line */
            processAjax("ordertableobj_" + orderlineid,".?fsaction=AjaxAPI.callback&cmd=UPDATECOMPONENTLARGE&ref=" + gSession + "&orderlineid=" + orderlineid + "&section=" + sectioncode + "&code=" + componentCode, 'GET', '');
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
                else if ((newQty < 1) || (newQty > 9999))
                {
                    validQty = false;
                }

                if (validQty)
                {
                    if( processExecute == undefined)
                    {
                        /* temp save metadata */
                        saveTempMetadata();
        {/literal}

        {if $issmallscreen == 'true'}

            {literal}

                        if (gActivePanel == '')
                        {
                            gScrollRefreshPosition = document.getElementById('contentLeftScrollQty').scrollTop;
                        }
                        else
                        {
                            gScrollRefreshPosition = document.getElementById('contentRightScrollComponentDetail').scrollTop;
                        }

                        gOrderlineidActive = orderLineId;

                        processAjaxSmallScreen("updateQty",".?fsaction=AjaxAPI.callback&cmd=UPDATEQTYSMALL&ref=" + gSession + "&orderlineid=" + orderLineId + "&itemqty=" + newQty, 'GET', '');

            {/literal}

        {else} {* else {if $issmallscreen == 'true'} *}

            {$metadatasubmit}

            {literal}
                        processAjax("ordertableobj_" + orderLineId,".?fsaction=AjaxAPI.callback&cmd=UPDATEQTYLARGE&ref=" + gSession + "&orderlineid=" + orderLineId + "&itemqty=" + newQty, 'GET', '');

            {/literal}

        {/if} {* end {if $issmallscreen == 'true'} *}

        {literal}

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

        {/literal}

        {if $issmallscreen == 'true'}

            {literal}

                    createDialog("{/literal}{#str_TitleWarning#}{literal}", "{/literal}{#str_ErrorInvalidQty#}{literal}", "closeDialog()");

            {/literal}

        {else} {* else {if $issmallscreen == 'true'} *}

            {literal}

                    alert("{/literal}{#str_ErrorInvalidQty#}{literal}");

            {/literal}

        {/if} {* end {if $issmallscreen == 'true'} *}

        {literal}

                }
            }
            else
            {

        {/literal}

        {if $issmallscreen == 'true'}

            {literal}

                    createDialog("{/literal}{#str_TitleWarning#}{literal}", "{/literal}{#str_ErrorNoPrice#}{literal}", "closeDialog()");

            {/literal}

        {else} {* else {if $issmallscreen == 'true'} *}

            {literal}

                    alert("{/literal}{#str_ErrorNoPrice#}{literal}");

            {/literal}

        {/if} {* end {if $issmallscreen == 'true'} *}

        {literal}

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

    {/literal}

{/if} {* end {if $stage == 'qty'} *}

{if $stage == 'payment'}

    {literal}

        function displayGiftCardAlert(giftCardResult, customMessage)
        {
            var message = '';
            var isAnAlert = true;
            switch(giftCardResult)
            {
                case 'str_LabelGiftCardAccepted':
                {
                    message = "{/literal}{#str_LabelGiftCardAccepted#}{literal}";
                    isAnAlert = false;
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

                if (isAnAlert)
                {
                    showAlertBar(message);
                }
                else
                {
                    showLoadingNotificationBar(message);
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

        function onKeyPressVoucher()
        {
            if (enterKeyPressed(event))
            {
                setVoucher();
            }
        }

        function acceptDataEntry(alertOff)
        {
			if (gContinueOrderTimeout != undefined)
			{
				clearTimeout(gContinueOrderTimeout);
			}
        {/literal}

            {if $issmallscreen == 'false'}

                {literal}

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
                            goToSubmit = false;

                            if(alertOn)
                            {
                                alert("{/literal}{#str_ErrorValueRequired#}{literal}");
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

                                txtInput.onblur = function() {
                                    acceptDataEntry(false);
                                };
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
                            goToSubmit = false;

                            if(alertOn)
                            {
                                alert("{/literal}{#str_ErrorValueRequired#}{literal}");
                                alertOn = false;
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
                        goToSubmit = false;

                        if(alertOn)
                        {
                            alert("{/literal}{#str_ErrorValueRequired#}{literal}");
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

                            txtInput.onblur = function() {
                                acceptDataEntry(false);
                            };
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
                        goToSubmit = false;

                        if(alertOn)
                        {
                            alert("{/literal}{#str_ErrorValueRequired#}{literal}");
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

                            txtInput.onchange = function() {
                                acceptDataEntry(false);
                            };
                        }
                    }
                }
            }



            if(goToSubmit == false)
            {
                return false;
            }

            if(alertOff == false)
            {
                return false;
            }

                {/literal}

                {$metadatasubmit}

            {/if} {* end {if $issmallscreen == 'false'} *}

        {literal}

            if (gPaymentMethodCode != "NONE")
            {
                var paymentMethodCode = getPaymentMethodCode();

                if (paymentMethodCode.length == 0)
                {

        {/literal}

        {if $issmallscreen == 'true'}

            {literal}

                    createDialog("{/literal}{#str_TitleWarning#}{literal}", "{/literal}{#str_ErrorNoPaymentMethod#}{literal}", "closeDialog()");

            {/literal}

        {else}

            {literal}

                    alert("{/literal}{#str_ErrorNoPaymentMethod#}{literal}");

            {/literal}

        {/if}

        {literal}

                    return false;
                }

                if ((gCanUseAccount == false) && (paymentMethodCode == "ACCOUNT"))
                {

        {/literal}

        {if $issmallscreen == 'true'}

            {literal}

                    createDialog("{/literal}{#str_TitleWarning#}{literal}", "{/literal}{#str_ErrorOverCreditLimit#}{literal}", "closeDialog()");

            {/literal}

        {else}

            {literal}

                    alert("{/literal}{#str_ErrorOverCreditLimit#}{literal}");

            {/literal}

        {/if}

        {literal}

                    return false;
                }

                document.submitform.paymentmethodcode.value = paymentMethodCode;
                document.submitform.paymentgatewaycode.value = getPaymentGatewayCode();

                var paymentMethodAction = getPaymentMethodAction();

                if (paymentMethodAction != '')
                {
                    var temp = eval(paymentMethodAction);
                }
                else
                {

    {/literal}

        {if $issmallscreen == 'true'}

        {literal}

                    var metadataValidity = checkMetadataValidity('contentPanelPayment', true);
                    if (metadataValidity.hasAnError == false)
                    {
                        // open the loading box
                        showLoadingDialog();

                        var postParams = metadataValidity.postParams;

                        postParams += '&previousstage={/literal}{$previousstage}{literal}';
                        postParams += '&stage={/literal}{$stage}{literal}';
                        postParams += '&paymentmethodcode=' + paymentMethodCode;
                        postParams += '&paymentgatewaycode=' + getPaymentGatewayCode();


                        if ((paymentMethodCode == 'CARD') || (paymentMethodCode == 'PAYPAL'))
                        {
                            processAjaxSmallScreen('showPaymentgateway', ".?fsaction=AjaxAPI.callback&cmd=ORDERCONTINUE&ref=" + gSession, 'POST', postParams);
                        }
                        else
                        {
                            processAjaxSmallScreen('showConfirmation', ".?fsaction=AjaxAPI.callback&cmd=ORDERCONTINUE&ref=" + gSession, 'POST', postParams);
                        }
                    }

        {/literal}

    {else} {* else {if $issmallscreen == 'true'} *}


        {literal}
			// disable the continue order on the final stage to prevent multi clicks/orders
			document.getElementById('ordercontinuebutton').onclick = function() { return false; };
		   document.getElementById('btn-confirm-left').className = 'btn-disabled-left';
			document.getElementById('btn-confirm-middle').className = 'btn-disabled-middle';
			//document.getElementById('btn-confirm-right').className = 'btn-disabled-right-tick';

			// wait 10 seconds to reenable the continue order button
			gContinueOrderTimeout = setTimeout(function()
			{
				document.getElementById('ordercontinuebutton').onclick = acceptDataEntry;
				//document.getElementById('btn-confirm-left').className = 'btn-green-left';
				document.getElementById('btn-confirm-middle').className = 'btn-green-middle';
				document.getElementById('btn-confirm-right').className = 'btn-accept-right-tick';
			}, 10000);

            document.submitform.submit();
        {/literal}

    {/if} {* end {if $issmallscreen == 'true'} *}

                }

    {literal}

            }
            else
            {

    {/literal}

    {if $issmallscreen == 'true'}

        {literal}

                var metadataValidity = checkMetadataValidity('contentPanelPayment', true);
                if (metadataValidity.hasAnError == false)
                {
                    // open the loading box
                    showLoadingDialog();

                    var postParams = metadataValidity.postParams;

                    postParams += '&previousstage={/literal}{$previousstage}{literal}';
                    postParams += '&stage={/literal}{$stage}{literal}';
                    postParams += '&paymentmethodcode=' + gPaymentMethodCode;
                    postParams += '&paymentgatewaycode=' + getPaymentGatewayCode();

                    processAjaxSmallScreen('showConfirmation', ".?fsaction=AjaxAPI.callback&cmd=ORDERCONTINUE&ref=" + gSession, 'POST', postParams);
                }

        {/literal}

    {else} {* else {if $issmallscreen == 'true'} *}

        {$metadatasubmit}

        {literal}
                document.getElementById('ordercontinuebutton').setAttribute("disabled","disabled");
                document.submitform.paymentmethodcode.value = gPaymentMethodCode;
                document.submitform.paymentgatewaycode.value = getPaymentGatewayCode();
                document.submitform.submit();
         {/literal}

    {/if} {* end {if $issmallscreen == 'true'} *}

    {literal}

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

    {/literal}

{/if} {* end {if $stage == 'payment'} *}

{literal}

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
        function processAjax(obj, serverPage, requestMethod, params)
        {
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
                            case 'changegiftcard':
                                var jsonObj = eval('(' + xmlhttp.responseText + ')');
                                if (jsonObj.success)
                                {
                                    gCanUseAccount = jsonObj.canuseaccount;

                                    if (document.getElementById)
                                    {
                                        var giftcard = document.getElementById("giftcard");
                                        var giftcardamount = document.getElementById("giftcardamount");
                                        var giftcardremain = document.getElementById("giftcard-remain");
                                        var giftcardbutton = document.getElementById("giftbutton");
                                        var ordertotaltopayvalue = document.getElementById("ordertotaltopayvalue");
                                        var ordertotaltopayside = document.getElementById("ordertotaltopayvalueside");
                                        var paymenttableobj = document.getElementById("paymenttableobj");
                                        var giftcardbalanceside = document.getElementById("giftcardbalanceside");
                                        var giftcardbalance = document.getElementById("giftcardbalance");
										var includetaxtextwithgiftcard = document.getElementById("includetaxtextwithgiftcard");
										var includetaxtextwithoutgiftcard = document.getElementById("includetaxtextwithoutgiftcard");

                                        if (giftcardbalanceside)
                                        {
                                            giftcardbalanceside.innerHTML = jsonObj.giftcardtotalremaining;
                                            giftcardbalance.innerHTML = jsonObj.giftcardtotalremaining;
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
                                            }
                                        }

                                        if (ordertotaltopayvalue)
                                        {
                                            ordertotaltopayvalue.innerHTML = jsonObj.ordertotaltopay;
                                            ordertotaltopayside.innerHTML = jsonObj.ordertotaltopay;
                                        }

                                        if (jsonObj.giftcardstate=='add')
                                        {
                                            giftcard.className = 'line-sub-total-small gift-card-box-button disabled';
                                            giftcardremain.className = 'line-sub-total-small disabled';
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
                                            giftcardremain.className = 'line-sub-total-small';
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
                            case 'topmost':
                                document.getElementById(obj).innerHTML = xmlhttp.responseText;
                                break;
                            case 'componentChangeBox':
{/literal}

{if $stage == 'qty'}

    {literal}
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
                                    componentChangeBoxObj.style.left = Math.round((shimObj.offsetWidth / 2) - ({/literal}{$modalWidth}{literal}/2)) + 'px';
                                    windowHeight = document.documentElement.clientHeight;
                                    finalPosition = (windowHeight - componentChangeBoxObj.offsetHeight) / 2;
                                    componentChangeBoxObj.style.top = Math.round(finalPosition) + 'px';
                                }
                                changeComponentImageLoaded();
    {/literal}

{/if} {* end {if $stage == 'qty'} *}

{literal}
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
                                    eval(storeLocatorObj.getElementsByTagName('script')[0].innerHTML);
                                }
                                break;
                            case 'storeListAjaxDiv':
                                document.getElementById(obj).innerHTML = xmlhttp.responseText;
                                document.getElementById('storeListAjaxDiv').focus();

                                {/literal}

                                {if $issmallscreen == 'true'}

                                    {literal}

                                    // fix the size of the container for scrollbar option
                                    setScrollAreaHeight('contentStoreList', 'contentNavigationStore');

									// resize stores list
									resizeResultElement();

                                    {/literal}

                                {/if} {* end {if $issmallscreen == 'true'} *}

                                {literal}

                                break;
                            case 'selectStore':
                                var responseData = eval('(' + xmlhttp.responseText + ')');
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
                            case 'ordersummarypanel':
                                responseObject = eval('(' + xmlhttp.responseText + ')');
                                document.getElementById(obj).innerHTML = responseObject.htmlCartSummary;
                                break;
                            case 'cfschangeshippingmethod':
                                document.getElementById('itemsubtotalwithshipping').innerHTML = xmlhttp.responseText;
								gChangeMethodInPorgress = false;
                                processAjax("ordersummarypanel",".?fsaction=AjaxAPI.callback&cmd=UPDATEORDERSUMMARY&ref=" + gSession, 'GET', '');
                                break;
                            case 'termsandconditionswindow':
                            	window.scroll(0,0);
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
                            default:
                                var response = eval(xmlhttp.responseText);
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
								processAjax("ordersummarypanel",".?fsaction=AjaxAPI.callback&cmd=UPDATEORDERSUMMARY&ref=" + gSession, 'GET', '');
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
                            case 'updateorderqtyall':
                                var response = eval(xmlhttp.responseText);
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
                                processAjax("ordersummarypanel",".?fsaction=AjaxAPI.callback&cmd=UPDATEORDERSUMMARY&ref=" + gSession, 'GET', '');
                            break;
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
            var objImgToggle = document.getElementById('img_' + idLink);
            if( objElement.style.display == 'none')
            {
                objElement.style.display = 'block';
                objImgToggle.src = "{/literal}{$brandroot}{literal}/images/icons/toggle_down_" + color + ".png";
                objImgToggle.alt = "{/literal}{#str_OrderHide#}{literal}";
                objImgToggle.title = "{/literal}{#str_OrderHide#}{literal}";
                objLinkToggle.innerHTML = "{/literal}{#str_OrderHide#}{literal}";
                return
            }
            else
            {
                objElement.style.display = 'none';
                objImgToggle.src = "{/literal}{$brandroot}{literal}/images/icons/toggle_up_" + color + ".png";
                objImgToggle.alt = "{/literal}{#str_OrderShow#}{literal}";
                objImgToggle.title = "{/literal}{#str_OrderShow#}{literal}";
                objLinkToggle.innerHTML = "{/literal}{#str_OrderShow#}{literal}";
            }
        }

        function toggleSummary()
        {
            var objElement = document.getElementById('contentOrderSummary');
            var objLinkToggle = document.getElementById('link_toggle');
            var objImgToggle = document.getElementById('img_toggle');
            if( objElement.style.display == 'none')
            {
                objElement.style.display = 'block';
                objImgToggle.src = "{/literal}{$brandroot}{literal}/images/icons/toggle_down_white.png";
                objImgToggle.alt = "{/literal}{#str_OrderHide#}{literal}";
                objImgToggle.title = "{/literal}{#str_OrderHide#}{literal}";
                objLinkToggle.innerHTML = "{/literal}{#str_OrderHide#}{literal}";
            }
            else
            {
                objElement.style.display = 'none';
                objImgToggle.src = "{/literal}{$brandroot}{literal}/images/icons/toggle_up_white.png";
                objImgToggle.alt = "{/literal}{#str_OrderShow#}{literal}";
                objImgToggle.title = "{/literal}{#str_OrderShow#}{literal}";
                objLinkToggle.innerHTML = "{/literal}{#str_OrderShow#}{literal}";
            }
        }
{/literal}

{if $stage == 'payment'}

    {literal}
            function removeFalseLabel(pObj, pText){
                if (pObj.value == pText)
                {
                    pObj.value = '';
                    pObj.className = 'voucherinput';
                }
            }

            function addFalseLabel(pObj, pText){
                if (pObj.value == '')
                {
                    pObj.value = pText;
                    pObj.className = 'voucherinput falseLabelColor';
                }
            }

    {/literal}

{/if} {* end {if $stage == 'payment'} *}

{* END COMMON JAVSCRIPT (LARGE/SMALL) *}

{if $issmallscreen == 'true'}

    {* SMALL SCREEN SPECIFIC FUNCTION *}

    {literal}

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

        processAjaxSmallScreen('showCancellation', ".?fsaction=AjaxAPI.callback&cmd=ORDERCANCEL&ref=" + gSession, 'POST', '');

    }

    function cancelOrderConfirmation()
    {

    {/literal}

    {if $sessionrevived == true}

        {literal}

        cancelOrder();

        {/literal}

    {else} {* else {if $sessionrevived == true} *}

        {literal}

        showConfirmDialog("{/literal}{#str_LabelConfirmation#}{literal}", nlToBr("{/literal}{#str_ConfirmCancellation#}{literal}"), "cancelOrder();");

        {/literal}

    {/if} {* end {if $sessionrevived == true} *}

    {literal}

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

    {/literal}

    {if $stage == 'qty'}

        {literal}

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

        {/literal}

    {/if} {* end {if $stage == 'qty'} *}

    {literal}


                        }
                        else
                        {
                            postParams += '&' + inputs[j].name + '=' + inputs[j].value;
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
                        postParams += '&' + radios[radio][x].name + '=' + radios[radio][x].value;
                        checked = true;
                    }
                }

                if(checked == false)
                {
                    x = x -1;

                    hasAnError = true;

                    highlightBoxes.push('metadataItem' + radios[radio][x].name);

    {/literal}

    {if $stage == 'qty'}

        {literal}

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


         {/literal}

    {/if} {* end {if $stage == 'qty'} *}

    {literal}

                }

            }

            /* get metadata values for all selects on the page */
            for (var j = 0; j < selects.length; j++)
            {
                if (selects[j].options[selects[j].selectedIndex].value == '')
                {
                    hasAnError = true;

                    highlightBoxes.push('metadataItem' + selects[j].name);

    {/literal}

    {if $stage == 'qty'}

        {literal}
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

        {/literal}

    {/if} {* end {if $stage == 'qty'} *}

    {literal}

                }
                else
                {
                    postParams += '&' + selects[j].name + '=' + selects[j].options[selects[j].selectedIndex].value;
                }
            }

            /* get metadata values for all textareas on the page */
            for (var j = 0; j < textareas.length; j++)
            {
                if ((textareas[j].className.indexOf('required') > -1) && (textareas[j].value == ''))
                {
                    hasAnError = true;

                    highlightBoxes.push('metadataItem' + textareas[j].name);
    {/literal}

        {if $stage == 'qty'}

            {literal}

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

            {/literal}

    {/if} {* end {if $stage == 'qty'} *}

    {literal}

                }
                else
                {
                    postParams += '&' + textareas[j].name + '=' + textareas[j].value;
                }
            }
        }

        // display the highlight effect
        setHighlightAllBoxes(highlightBoxes);

        if (pDisplayMessage == true)
        {
            if (hasAnError == true)
            {
                createDialog("{/literal}{#str_TitleWarning#}{literal}", "{/literal}{#str_ErrorValueRequired#}{literal}", "closeDialog()");
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
    * Set teh width of teh components or subcomponenets
    */
    function resizeComponentDesign(pWidth, pContainerActive, pClassWithPreview, pClassWithoutPreview)
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
            if (elm.parentNode.parentNode.parentNode.className.indexOf('componentHighLight') != -1)
            {
                widthDiv = width - gHighLightBorderSizeDifference;
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
            if (elm.parentNode.parentNode.parentNode.className.indexOf('componentHighLight') != -1)
            {
                widthDiv = width - gHighLightBorderSizeDifference;
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
        var width = pWidth - gComponentPreview;

        for (var i = 0; i < classLength; i++)
        {
            var elm = containerActive.getElementsByClassName(pClassWithPreview)[i];

            widthDiv = width;
            if (elm.parentNode.parentNode.parentNode.className.indexOf('componentHighLight') != -1)
            {
                widthDiv = width - gHighLightBorderSizeDifference;
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
        width = pWidth;
        classLength = containerActive.getElementsByClassName(pClassWithoutPreview).length;

        for (var i = 0; i < classLength; i++)
        {
            var elm = containerActive.getElementsByClassName(pClassWithoutPreview)[i];

            widthDiv = width;
            if (elm.parentNode.parentNode.parentNode.className.indexOf('componentHighLight') != -1)
            {
                widthDiv = width - gHighLightBorderSizeDifference;
            }

            // if it's a checkbox the width of the button need to be removed
            if(elm.parentNode.className == 'checkboxBloc')
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
        postParams += '&previousstage={/literal}{$previousstage}{literal}';
        postParams += '&stage={/literal}{$stage}{literal}';

        processAjaxSmallScreen(pAction, ".?fsaction=AjaxAPI.callback&cmd=ORDERCONTINUE&ref=" + gSession, 'POST', postParams);
    }

    function loadPaymentPanel()
    {
        var postParams = '&previousstage={/literal}{$previousstage}{literal}';
        postParams += '&stage={/literal}{$stage}{literal}';
        postParams += '&shippingratecode=' + getShippingRateCode();

        processAjaxSmallScreen('showPayment', ".?fsaction=AjaxAPI.callback&cmd=ORDERCONTINUE&ref=" + gSession, 'POST', postParams);
    }

    {/literal}

    {if $stage == 'qty'}

        {literal}

        /**
         * initializeStage
         *
         * set size of container on the qty stage
         */
        function initializeStage()
        {

            {/literal}

                {$custominit}

                {$initlanguage}

            {literal}

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
                resizeComponentDesign(gInnerBoxContentBloc, 'contentLeftScrollQty', 'componentContentText', 'componentContentTextLong');
            }

            document.getElementById('contentPanelQty').style.display = 'block';

            document.getElementById('contentLeftScrollQty').scrollTop = gScrollRefreshPosition;

            var classLength = document.getElementsByClassName('singlePrintList').length;
            if (classLength > 0)
            {
                // force width of containers
                for (var i = 0; i < classLength; i++)
                {
                    document.getElementsByClassName('singlePrintList')[i].style.width = gInnerBoxContentBloc + 'px';
                }

                var singlePrintQty = document.getElementsByClassName('singlePrintQty')[0];
                var styleSinglePrintQty = singlePrintQty.currentStyle || window.getComputedStyle(singlePrintQty);
                var singlePrintQtyWidth =  parseIntStyle(styleSinglePrintQty.width);

                var singlePrintPrice = document.getElementsByClassName('singlePrintPrice')[0];
                var styleSinglePrintPrice = singlePrintPrice.currentStyle || window.getComputedStyle(singlePrintPrice);
                var singlePrintPriceWidth =  parseIntStyle(styleSinglePrintPrice.width);

                var widthExternal = singlePrintQtyWidth + singlePrintPriceWidth;

                var classLength = document.getElementsByClassName('singlePrintLabel').length;
                for (var i = 0; i < classLength; i++)
                {
                    var elm = document.getElementsByClassName('singlePrintLabel')[i];

                    if (i == 0)
                    {
                        var styleLabel = elm.currentStyle || window.getComputedStyle(elm);
                        widthExternal = widthExternal + parseIntStyle(styleLabel.marginLeft) + parseIntStyle(styleLabel.marginRight);
                    }

                    elm.style.width = (gInnerBoxContentBloc - widthExternal) + 'px';
                }
            }

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
            resizeComponentDesign(gOuterBoxContentBloc, pDivID, 'componentDetailContentText', 'componentDetailContentTextLong');

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
            processAjaxSmallScreen("updateCheckBox",".?fsaction=AjaxAPI.callback&cmd=UPDATECHECKBOXSMALL&ref=" + gSession + "&orderlineid=" + orderLineId + "&componentid=" + componentId, 'GET', '');
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
                document.getElementById('choiceBackButton').onclick=function()
                {
                    setHashUrl('componentView|' + gOrderlineidActive + '|' + gComponentActive);
                };
            }
            else
            {
                document.getElementById('choiceBackButton').onclick=function()
                {
                    setHashUrl('subComponentView|' + gOrderlineidActive + '|' + gComponentActive + '|' + gSubComponentActive);
                };
            }

            // send the ajax
            processAjaxSmallScreen("componentChangeList",".?fsaction=AjaxAPI.callback&cmd=CHANGECOMPONENTSMALL&ref=" + gSession + "&item=" + item + "&section=" + section, 'GET', '');
        }

        /**
         * showInfoComponent
         *
         * Display a dialog box with the detail of a component or a subcomponent
         */
        function showInfoComponent(pName, pDescription)
        {
            createDialog(pName, pDescription, "closeDialog()");
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

            document.getElementById('updateChoiceBtn').onclick=function(){
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
            processAjaxSmallScreen('updateComponent', ".?fsaction=AjaxAPI.callback&cmd=UPDATECOMPONENTSMALL&ref=" + gSession +
                                "&orderlineid=" + gOrderlineidActive + "&section=" + pSection +
                                "&code=" + componentCode + "&localcode=" + localComponentCode, "GET", "");
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
                var orderLinePrefix = componentOrderLineID.substr(0,1);

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
                else if (newQty > 9999)
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
                    processAjaxSmallScreen("updateComponentQty",".?fsaction=AjaxAPI.callback&cmd=UPDATECOMPONENTQTYSMALL&ref=" + gSession +
                                                                "&orderlineid=" + orderLineId + "&componentorderlineid=" + componentOrderLineID +
                                                                "&componentitemqty=" + newQty + "&itemqty=" + prodQty, 'GET', '');
                }
                else
                {
                    createDialog("{/literal}{#str_TitleWarning#}{literal}", "{/literal}{#str_ErrorInvalidQty#}{literal}", "closeDialog()");
                }
            }
            else
            {
                createDialog("{/literal}{#str_TitleWarning#}{literal}", "{/literal}{#str_ErrorNoPrice#}{literal}", "closeDialog()");
            }
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
            processAjax("savemetadata",".?fsaction=AjaxAPI.callback&cmd=SAVETEMPMETADATA&ref=" + gSession, 'POST', postParams);
        }


        {/literal}

    {/if} {* end {if $stage == 'qty'} *}

    {if $stage == 'shipping'}

        {literal}

        /**
         * initializeStage
         *
         * Set the size of html elements on shipping stage
         */
        function initializeStage()
        {
            {/literal}

                {$custominit}

                {$initlanguage}

            {literal}

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

            var postParams = '&previousstage={/literal}{$previousstage}{literal}';
            postParams += '&stage={/literal}{$stage}{literal}';
            processAjaxSmallScreen('backQty', ".?fsaction=AjaxAPI.callback&cmd=ORDERBACK&ref=" + gSession, 'POST', postParams);
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

                    var postParams = '&previousstage={/literal}{$previousstage}{literal}';
                    postParams += '&stage={/literal}{$stage}{literal}';
                    postParams += '&sameshippingandbillingaddress=' + document.getElementById("sameasshippingaddress").checked;

                    processAjaxSmallScreen('copyShippingAddress', ".?fsaction=AjaxAPI.callback&cmd=COPYSHIPPINGADDRESS&ref=" + gSession, 'POST', postParams);
                }
            }
        }

        /**
         * showStoreInfo
         *
         * Show store information
         */
        function showStoreInfo(storeCode)
        {
            var addressSearch = document.getElementById('searchText').value;

        {/literal}

        {if $external == 0}

            {literal}

        	processAjaxSmallScreen("storeInfo",".?fsaction=AjaxAPI.callback&cmd=STOREINFORMATION&ref={/literal}{$ref}{literal}&store=" + storeCode, 'GET', '');

            {/literal}

        {else} {* else {if $external == 0} *}

            {literal}

        	processAjaxSmallScreen("storeInfo",".?fsaction=AjaxAPI.callback&cmd=STOREINFORMATIONEXTERNAL&ref={/literal}{$ref}{literal}&store=" + storeCode + "&filter=" + addressSearch, 'GET', '');

            {/literal}

        {/if} {* end {if $external == 0} *}

        {literal}

        }

        /**
         * changeShippingAddress
         *
         * load the shipping address form
         */
        function changeShippingAddress()
        {
            var postParams = '&shippingratecode=' + getShippingRateCode();
            postParams += '&previousstage={/literal}{$previousstage}{literal}';
            postParams += '&stage={/literal}{$stage}{literal}';
            if (document.getElementById("sameasshippingaddress").checked == true)
            {
                 postParams += '&sameshippingandbillingaddress=1';
            }
            else
            {
                 postParams += '&sameshippingandbillingaddress=0';
            }

            processAjaxSmallScreen("changeShippingAddressDisplay",".?fsaction=AjaxAPI.callback&cmd=CHANGESHIPPINGADDRESSDISPLAY&ref=" + gSession, 'POST', postParams);
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

            postParams += '&previousstage={/literal}{$previousstage}{literal}';
            postParams += '&stage={/literal}{$stage}{literal}';

            processAjaxSmallScreen("changeBillingAddressDisplay",".?fsaction=AjaxAPI.callback&cmd=CHANGEBILLINGADDRESSDISPLAY&ref=" + gSession, 'POST', postParams);
        }


        /**
         * shippingMethodClick
         *
         * Select the option clicked and update the session shipping details
         */
        function shippingMethodClick()
        {
            // show loading dialog
            showLoadingDialog();

            gCollectFromStore = 0;

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
            postParams += '&previousstage={/literal}{$previousstage}{literal}';
            postParams += '&stage={/literal}{$stage}{literal}';
            postParams += '&fsactionorig=Order.changeShippingMethod';

            processAjaxSmallScreen("changeshippingmethod",".?fsaction=AjaxAPI.callback&cmd=CHANGESHIPPINGMETHOD&ref=" + gSession, 'POST', postParams);
        }

        /**
         * selectStore
         *
         * Select the option clicked and update the session shipping details
         */
        function selectStore(pCode)
        {
            gCollectFromStore = 1;
            gCollectFromStoreCode = gStoreCodes[pCode];

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

                    if (gStoreAddresses[pCode] != '')
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

            var sameshippingandbillingaddress = document.submitform.sameshippingandbillingaddress.value;
            var getParams = '&shippingratecode=' + selectedShippingRateCode;
            getParams += '&previousstage={/literal}{$previousstage}{literal}';
            getParams += '&stage={/literal}{$stage}{literal}';
            getParams += '&fsactionorig=Order.changeShippingMethod';
            getParams += '&refreshshipping=' + ((gActivePanel == 'contentPanel1') ? 'false' : 'true');
            getParams += '&sameshippingandbillingaddress=' + sameshippingandbillingaddress;

            // send an ajax query
            processAjaxSmallScreen('storeLocatorForm', ".?fsaction=AjaxAPI.callback&cmd=SELECTSTOREDISPLAY&ref=" + gSession + getParams, 'GET');
        }

        function acceptDataEntry()
        {
            var shippingRateCode = getShippingRateCode();

            if (shippingRateCode.length == 0)
            {
                createDialog("{/literal}{#str_TitleConfirmation#}{literal}", "{/literal}{#str_ErrorNoShippingRate#}{literal}", "closeDialog()");
                return false;
            }

    {/literal}

    {if $optionCFS}

        {literal}

            if ((gCollectFromStore == 1) && (gCollectFromStoreCode == ''))
            {
                createDialog("{/literal}{#str_TitleConfirmation#}{literal}", "{/literal}{#str_ErrorNoStore#}{literal}", "closeDialog()");
                return false;
            }

        {/literal}

    {/if} {* end {if $optionCFS} *}

        {literal}
            // open the loading box
            showLoadingDialog();

            setHashUrl('payment');

            return false;
        }

        {/literal}

    {/if} {* end {if $stage == 'shipping'} *}

    {if $stage == 'payment'}

        {literal}

            /**
             * initializeStage
             *
             * set the size of html elments on payment stage
             */
            function initializeStage(pInitialize)
            {

                {/literal}

                    {$custominit}

                    {$initlanguage}

                {literal}

                // only two panels can be slide at one point
                document.getElementById('contentAjaxPayment').style.width = (gScreenWidth * 2) + 'px';

                // main panels width
                document.getElementById('contentPanelPayment').style.width = gScreenWidth + 'px';

                // set the component size
                var componentBloc = document.getElementsByClassName('componentBloc')[0];
                if (componentBloc)
                {
                    resizeComponentDesign(gInnerBoxContentBloc, 'contentLeftScrollPayment', 'componentContentText', 'componentContentTextLong');
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

                {/literal}

                    {if (($showgiftcardmessage == 1) && ($stage == 'payment'))}

                displayGiftCardAlert('{$voucherstatusResult}', '{$vouchercustommessage}');

                    {/if}

                {literal}

            }

            /**
             * orderTermsAndConditions
             *
             * get the template to display for terms and conditions
             */
            function orderTermsAndConditions()
            {
                processAjaxSmallScreen('termsandconditionswindow', ".?fsaction=AjaxAPI.callback&cmd=TERMSANDCONDITIONS&template=ORDER&ref=" + gSession, 'GET', '');
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

                var postParams = '&previousstage={/literal}{$previousstage}{literal}';
                postParams += '&stage={/literal}{$stage}{literal}';
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

                // send teh query
                processAjaxSmallScreen('backShipping', ".?fsaction=AjaxAPI.callback&cmd=ORDERBACK&ref=" + gSession, 'POST', postParams);
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

                var postParams = '&previousstage={/literal}{$previousstage}{literal}';
                postParams += '&stage={/literal}{$stage}{literal}';
                postParams += '&paymentmethodcode=' + getPaymentMethodCode();
                postParams += '&paymentgatewaycode=' + getPaymentGatewayCode();
                postParams += '&giftcardcode=' + document.getElementById('giftcardcode').value;
                postParams += '&showgiftcardmessage=' + 1;

                // send an ajax query
                processAjaxSmallScreen('setGiftCard', ".?fsaction=AjaxAPI.callback&cmd=SETGIFTCARD&ref=" + gSession, 'POST', postParams);
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
                    processAjaxSmallScreen("changegiftcard",".?fsaction=AjaxAPI.callback&cmd=CHANGEGIFTCARD&action=" + add_delete + "&ref=" + gSession, 'GET', '');
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

                var postParams = '&previousstage={/literal}{$previousstage}{literal}';
                postParams += '&stage={/literal}{$stage}{literal}';
                postParams += '&paymentmethodcode=' + getPaymentMethodCode();
                postParams += '&paymentgatewaycode=' + getPaymentGatewayCode();
                postParams += '&vouchercode=' + document.getElementById('vouchercode').value;
                postParams += '&showgiftcardmessage=' + 1;

                // send an ajax query
                processAjaxSmallScreen('setVoucher', ".?fsaction=AjaxAPI.callback&cmd=SETVOUCHER&ref=" + gSession, 'POST', postParams);
            }


            /**
             * setVoucher
             *
             * Remove a voucher
             */
            function removeVoucher()
            {
                // show the loading dialog
                showLoadingDialog();

                // save metadata
                saveTempMetadata();

                var postParams = '&previousstage={/literal}{$previousstage}{literal}';
                postParams += '&stage={/literal}{$stage}{literal}';
                postParams += '&paymentmethodcode=' + getPaymentMethodCode();
                postParams += '&paymentgatewaycode=' + getPaymentGatewayCode();
                postParams += '&vouchercode=';
                postParams += '&showgiftcardmessage=' + 1;

                // send an ajax query
                processAjaxSmallScreen('setVoucher', ".?fsaction=AjaxAPI.callback&cmd=SETVOUCHER&ref=" + gSession, 'POST', postParams);
            }

            function validatePayType(pAlertMessage)
            {
                var paytypeObject = document.getElementById('paymentgatewaycode');
                var paytypeValue =  paytypeObject.options[paytypeObject.selectedIndex].value;

                if (paytypeValue == '')
                {
                    createDialog("{/literal}{#str_TitleWarning#}{literal}", pAlertMessage, "closeDialog()");
                }
                else
                {
                    var metadataValidity = checkMetadataValidity('contentPanelPayment', true);
                    if (metadataValidity.hasAnError == false)
                    {
                        // open the loading box
                        showLoadingDialog();

                        var postParams = metadataValidity.postParams;

                        postParams += '&previousstage={/literal}{$previousstage}{literal}';
                        postParams += '&stage={/literal}{$stage}{literal}';
                        postParams += '&paymentmethodcode=' + getPaymentMethodCode();
                        postParams += '&paymentgatewaycode=' + paytypeValue;

                        processAjaxSmallScreen('showPaymentgateway', ".?fsaction=AjaxAPI.callback&cmd=ORDERCONTINUE&ref=" + gSession, 'POST', postParams);
                    }
                }
            }

        {/literal}

    {/if} {* end {if $stage == 'payment'} *}

    {* END SMALL SCREEN SPECIFIC FUNCTION *}

{else} {* else {if $issmallscreen == 'true'} *}

    {* LARGE SCREEN SPECIFIC FUNCTION *}

    {literal}

    function cancelOrder()
    {

    {/literal}

    {if $sessionrevived == true}

        {literal}

            document.submitform.fsaction.value = "Order.cancel";
            document.submitform.submit();

        {/literal}

    {else} {* else {if $sessionrevived == true} *}

        {literal}

            var confirmCancel = confirm("{/literal}{#str_ConfirmCancellation#}{literal}");
            if (confirmCancel)
            {
                document.submitform.fsaction.value = "Order.cancel";
                document.submitform.submit();
            }

        {/literal}

    {/if} {* end {if $sessionrevived == true} *}

    {literal}

            return false;
    }

    function setSystemLanguage()
    {
        changeSystemLanguage("Order.refresh");
    }

    {/literal}

    {if $stage == 'qty'}

        {literal}

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

        {/literal}

            {$metadatasubmit}

        {literal}

            document.submitform.action = "index.php";
            document.submitform.itemqty.value = "";
            document.submitform.orderlineid.value = pOrderLineId;
            document.submitform.fsaction.value = "Order.duplicate";
            document.submitform.submit();
            return false;
        }

        function removeOrderLine(pOrderLineId)
        {
            var confirmRemove = confirm("{/literal}{#str_ConfirmRemove#}{literal}");
            if (confirmRemove)
            {

        {/literal}

            {$metadatasubmit}

        {literal}
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
                alert("{/literal}{#str_ErrorNoComponent2#}{literal}");
                return false;
            }

        {/literal}

            {$metadatasubmit}

        {literal}

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
            processAjax(obj, ".?fsaction=AjaxAPI.callback&cmd=UPDATECOMPONENTLARGE&ref=" + gSession + "&orderlineid=" + orderlineid + "&section=" + section +
                            "&code=" + componentCode + "&localcode=" + localComponentCode, "GET", "");
            closeWindow();
            return false;
        }

        function updateCheckbox(orderLineId, componentId)
        {
            /* temp save metadata */
            saveTempMetadata();

    {/literal}

    {$metadatasubmit}

    {literal}

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
            processAjax(obj,".?fsaction=AjaxAPI.callback&cmd=UPDATECHECKBOXLARGE&ref=" + gSession + "&orderlineid=" + orderLineId + "&componentid=" + componentId, 'GET', '');
            return false;
        }

        /* display a "pop up" that lists all available components so the user can make a choice */
        function changeComponent(item, section)
        {

    {/literal}

    {$metadatasubmit}

    {literal}

            /* temp save metadata */
            saveTempMetadata();
            gLoadedComponentsImagesCount = 0;
            gComponentImagesCount = 0;

            processAjax("componentChangeBox",".?fsaction=AjaxAPI.callback&cmd=CHANGECOMPONENTLARGE&ref=" + gSession + "&item=" + item + "&section=" + section, 'GET', '');
            return false;
        }

        function updateComponentQty(componentOrderLineID, prodQty, quantityValue, processExecute)
        {
            if (document.submitform.itemqty.value != "-")
            {
                var validQty = true;
                var itemQtyField = 'itemqty_' + componentOrderLineID;

                /* check to see if we are dealing witht the order footer */
                var orderLinePrefix = componentOrderLineID.substr(0,1);

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
                else if (newQty > 9999)
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

        {/literal}

        {$metadatasubmit}

        {literal}
                         processAjax(obj,".?fsaction=AjaxAPI.callback&cmd=UPDATECOMPONENTQTYLARGE&ref=" + gSession + "&orderlineid=" + orderLineId + "&componentorderlineid=" + componentOrderLineID + "&componentitemqty=" + newQty + "&itemqty=" + prodQty, 'GET', '');

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
                    alert("{/literal}{#str_ErrorInvalidQty#}{literal}");
                }
            }
            else
            {
                alert("{/literal}{#str_ErrorNoPrice#}{literal}");
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
                processAjax("savemetadata",".?fsaction=AjaxAPI.callback&cmd=SAVETEMPMETADATA&ref=" + gSession, 'POST', postParams);
            }
        }

        {/literal}

    {/if} {* end {if $stage == 'qty'} *}

    {if $stage == 'shipping'}

        {literal}

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
    {/literal}

    {$metadatasubmit}

    {literal}
                    document.submitform.sameshippingandbillingaddress.value = document.getElementById("sameasshippingaddress").checked;
                    document.submitform.fsaction.value = "Order.copyShippingAddress";
                    document.submitform.submit();
                    return false;
                }
            }
        }

        function showStoreInfo(storeCode)
        {
            var addressSearch = document.getElementById('searchText').value;

        {/literal}

            {if $external == 0}

                {literal}

        	processAjax("storeInfo",".?fsaction=AjaxAPI.callback&cmd=STOREINFORMATION&ref={$ref}&store=" + storeCode, 'GET', '');

                {/literal}

            {else} {* else {if $external == 0} *}

                {literal}

        	processAjax("storeInfo",".?fsaction=AjaxAPI.callback&cmd=STOREINFORMATIONEXTERNAL&ref={$ref}&store=" + storeCode + "&filter=" + addressSearch, 'GET', '');

                {/literal}

            {/if} {* end {if $external == 0} *}

        {literal}

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
				alert("{/literal}{#str_ErrorNoShippingRate#}{literal}");
                return false;
			}

            var shippingRateCode = getShippingRateCode();

            if (shippingRateCode.length == 0)
            {
                alert("{/literal}{#str_ErrorNoShippingRate#}{literal}");
                return false;
            }

    {/literal}

    {if $optionCFS}

        {literal}

            if ((gCollectFromStore == 1) && (gCollectFromStoreCode == ''))
            {
                alert("{/literal}{#str_ErrorNoStore#}{literal}");
                return false;
            }

        {/literal}

    {/if} {* end {if $optionCFS} *}

        {$metadatasubmit}

    {literal}

            document.submitform.shippingratecode.value = shippingRateCode;
            document.submitform.fsaction.value = "Order.continue";
            document.submitform.submit();

            return false;
        }

    {/literal}

    {/if} {* end {if $stage == 'shipping'} *}

    {if $stage == 'payment'}

        {$paymentscript}

        {literal}

            function orderTermsAndConditions()
            {
                processAjax('termsandconditionswindow', ".?fsaction=AjaxAPI.callback&cmd=TERMSANDCONDITIONS&template=ORDER&ref=" + gSession, 'GET', '');
            }

            function setVoucher()
            {

        {/literal}

                {$metadatasubmit}

        {literal}
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
                    var add_delete = (giftcard.className.indexOf('disabled')>-1) ? 'add' : 'delete';
                    processAjax("changegiftcard",".?fsaction=Order."+add_delete+"GiftCard&ref=" + gSession, 'GET', '');
                }
            }

            function setGiftCard()
            {

        {/literal}

                {$metadatasubmit}

        {literal}

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

        {/literal}

        {$metadatasubmit}

        {literal}

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

        {/literal}

    {/if} {* end {if $stage == 'payment'} *}

	{literal}
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
	{/literal}

	{* END LARGE SCREEN SPECIFIC FUNCTION *}

{/if} {* end {if $issmallscreen == 'true'} *}
