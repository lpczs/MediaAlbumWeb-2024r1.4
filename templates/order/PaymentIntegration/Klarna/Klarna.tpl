var availableCategories = [];

function closeKlarnaPaymentWindow()
{
    closeDialog();

    var classToRemove = 'thirdPartyPaymentScreen klarna';
    dialogOuter.className = dialogOuter.className.replace(classToRemove, '');
}

function klarnaPaymentMethodClick(e)
{
    var targetID = 'klarna_' + e.id.slice(0, -6) + '_outercontainer';

    var radios = document.querySelectorAll('.klarna-radio-button');
    var collapsableContainers = document.querySelectorAll('.collapsable-content');

    // IE returns multiple selector results as a NodeList rather than an array
    // so we need to call a new method for each object.
    Array.prototype.forEach.call(collapsableContainers, function (container){

        if (container.id == targetID)
        {
            container.classList.remove('hidden');
            container.classList.add('visible');
        }
        else
        {
            container.classList.add('hidden');
            container.classList.remove('visible');
        }
    });

    // IE returns multiple selector results as a NodeList rather than an array
    // so we need to call a new method for each object.
    Array.prototype.forEach.call(radios, function (radio){
        radio.checked = false;
    });

    e.checked = true;
}

function getSelectedCategory()
{
    var radios = document.querySelectorAll('.klarna-radio-button');
    var selectedCategory = '';

    // IE returns multiple selector results as a NodeList rather than an array
    // so we need to call a new method for each object.
    Array.prototype.forEach.call(radios, function (radio){
        if (radio.checked)
        {
            selectedCategory =  radio.id.slice(0, -6);
        }
    });

    return selectedCategory;
}

function processKlarnaPaymentTokenCallback(pResponse)
{
    if (pResponse.error == '')
    {
        location.href = pResponse.redirecturl;
        return false;
    }
    else
    {
        document.getElementById('innerdialogshim').style.display = 'none';
        alert(pResponse.errormessage);
    }
}

function klarnaPaymentTokenHandler(pPaymentToken)
{
    var postParams = 'token=' + pPaymentToken + '&ccitype=Klarna';
    processAjax('processpaymenttoken','.?fsaction=AjaxAPI.callback&cmd=PROCESSPAYMENTTOKEN', 'POST', postParams, processKlarnaPaymentTokenCallback);
}

function loadPaymentCategory(pCategory)
{
    Klarna.Payments.load({
        container: "#klarna_" + pCategory + "_container",
        payment_method_category: pCategory,
        instance_id : "klarna-payments-instance-" + pCategory
        }, function(res) {

            if (res.show_form)
            {
                /*
                * this payment method category can be used, allow the customer
                * to choose it in your interface.
                */
               availableCategories.push(pCategory);
               document.getElementById('klarna_' + pCategory + '_radio').classList.remove('hidden');
			   document.getElementById("klarna-pay-button").classList.remove('hidden');
            }
        });
}

function mountKlarnaSnippet(pRemotePaymentParams)
{
    var wrapperContainer = document.getElementById('dialogContent');

    // build the container for the payment categories
    var containerWrap = document.createElement('DIV');
    containerWrap.classList.add("klarna-payments-container-wrap");

    // build the payment button
    var paymentButtton = document.createElement('BUTTON');
    paymentButtton.setAttribute('id', 'klarna-pay-button');
	paymentButtton.classList.add('hidden');
    paymentButtton.innerHTML = '{#str_ButtonConfirmOrder#}';

    // build the cancel dialig
    var cancelLink = document.createElement('A');
    cancelLink.classList.add("cancel");
    cancelLink.setAttribute('data-decorator', 'closeKlarnaPaymentWindow');
    cancelLink.innerHTML = '{#str_ButtonCancel#}';

    // build the container for the buttons
    var buttonWrap = document.createElement('DIV');
    buttonWrap.classList.add("klarna-button-wrap");

    // attach the buttons to the button container
    buttonWrap.appendChild(cancelLink);
    buttonWrap.appendChild(paymentButtton);

    // attach the klarna payments container and the button container to the dialog content
    wrapperContainer.appendChild(containerWrap);
    wrapperContainer.appendChild(buttonWrap);

    if (pRemotePaymentParams.errormessage == '')
    {
        if (pRemotePaymentParams['paymentmethodcount'] > 0)
        {
            // Initialize the SDK
            Klarna.Payments.init({
                client_token: pRemotePaymentParams['clienttoken'],
            });

            // Load the widget for each payment method category:
            // - pay_later
            // - pay_over_time
            // - pay_now
            availableCategories = [];
            for (var category in pRemotePaymentParams['paymentmethodcategories'])
            {
                var paymentMethod = pRemotePaymentParams['paymentmethodcategories'][category];

                // for reach payment category build the radio container
                var radioContainer = document.createElement('DIV');
                radioContainer.id = 'klarna_' + category + '_radio';
                radioContainer.classList.add("radio-container");
                radioContainer.classList.add("hidden");
                radioContainer.innerHTML = '<label><input type="radio" class="klarna-radio-button" id="'+ category +'-radio" data-decorator="klarnaPaymentMethodClick"/>' + paymentMethod.name + '</label><img class="logo" src="'+paymentMethod.asseturl+'"/>';

                var categoryContainer = document.createElement('DIV');
                categoryContainer.id = 'klarna_' + category + '_outercontainer';
                categoryContainer.classList.add("collapsable-content");
                categoryContainer.classList.add('hidden');

                var categoryInnerContainer = document.createElement('DIV');
                categoryInnerContainer.id = 'klarna_' + category + '_container';
                categoryInnerContainer.classList.add("collapsable-content-inner");

                categoryContainer.appendChild(categoryInnerContainer);

                containerWrap.appendChild(radioContainer);
                containerWrap.appendChild(categoryContainer);

                loadPaymentCategory(category);
            }

            setTimeout(function(){
                if (availableCategories.length == 1)
                {
                    document.getElementById(availableCategories[0] + '-radio').checked = true;
                    document.getElementById('klarna_' + availableCategories[0] + '_outercontainer').classList.remove('hidden');
                    document.getElementById('klarna_' + availableCategories[0] + '_outercontainer').classList.add('visible');

                }

				if (availableCategories.length == 0)
                {
					alert('{#str_ErrorNoPaymentMethodsAvailable#}');
                }

           }, 3000);


            document.getElementById("klarna-pay-button").addEventListener("click", function(){
                var selectedCategory = getSelectedCategory();

                if (selectedCategory == '')
                {
                    alert('{#str_OrderNoKlarnaPaymentMethodsAvailable#}');
                }
                else
                {
                    document.getElementById('innerdialogshim').style.display = 'block';

                    // Submit the payment for authorization with the selected category
                    Klarna.Payments.authorize({
                        instance_id : "klarna-payments-instance-" + selectedCategory
                    }, function(res)
                    {
                        if (res.approved)
                        {
                            // Payment has been authorized
                            klarnaPaymentTokenHandler(res.authorization_token);
                        }
                        else
                        {
                            if (res.error)
                            {
                                // Payment not authorized or an error has occurred
                                document.getElementById('innerdialogshim').style.display = 'none';
                            }
                            else
                            {
                                // handle other states
                                document.getElementById('innerdialogshim').style.display = 'none';
                            }
                        }
                    });
                }
            });
        }
        else
        {
            alert(pRemotePaymentParams.errormessage);
            closeDialog();

            var classToRemove = 'thirdPartyPaymentScreen klarna';
            dialogOuter.className = dialogOuter.className.replace(classToRemove, '');
        }

    }
    else
    {
        alert(pRemotePaymentParams.errormessage);
        closeDialog();

        var classToRemove = 'thirdPartyPaymentScreen klarna';
        dialogOuter.className = dialogOuter.className.replace(classToRemove, '');
    }
}

function callKlarnaEndPoint()
{
    // check if all order metadata fields are validated before trying to initialise the payment
    if (lightBoxGatewayCheckOrderMetadataValidity())
    {
        toggleWaitingSpinner();

        createDialog('{#str_OrderPayWithKlarna#}', '', closeDialog(), 'klarna');

        var processingShim = document.createElement('div');
        processingShim.id = 'innerdialogshim';
        processingShim.setAttribute('class', 'innerdialogshim');

        document.getElementById('dialogContent').appendChild(processingShim);

        var postParams = [];

        postParams.push('paymentmethodcode=' + 'KLARNA');
        postParams.push('paymentgatewaycode=' + '');
        postParams = postParams.join('&');

        setTimeout(function(){
            processAjax("requestpaymentparams",".?fsaction=Order.requestPaymentParams", "POST", postParams, klarnaInitPayment);
       }, 500);
    }
}

function klarnaInitPayment(response)
{
    var data = JSON.parse(response);

    if (data.result == 1)
    {
        mountKlarnaSnippet(data);
    }
    else
    {
        location.href = data.redirecturl;
    }
}