var timer = null;
var counterInUse = false;
var counter = 0;
var inProgress = false;
var isMobile = false;

function closePaymentWindow()
{
    closeDialog();
    counterInUse = true;
    counter = 3;
    var classToRemove = 'thirdPartyPaymentScreen unlimit';
    dialogOuter.className = dialogOuter.className.replace(classToRemove, '');
}

function callEndPoint(refresh = false)
{
    var continueon = true;

    // if not a token refresh check if all order metadata fields are validated before trying to initialise the payment
    if (refresh === false) {
        continueon = lightBoxGatewayCheckOrderMetadataValidity();
    }

    if (continueon)
    {
        if (document.getElementById('paymentgatewaycode').value == '') {
            validatePayType('{$str_DropDownPleaseSelectAPaymentType}');
        } else {
            toggleWaitingSpinner();

            var postParams = [];

            postParams.push("paymentmethodcode=" + "CARD");
            postParams.push("refresh=" + refresh);
            postParams.push("paymentgatewaycode=" + document.getElementById('paymentgatewaycode').value);
            postParams = postParams.join("&");

            setTimeout(function(){
                processAjax("requestpaymentparams",".?fsaction=Order.requestPaymentParams", "POST", postParams, getPaymentParamsRemotely);             
            }, 500);
        }
    }
}

function getPaymentParamsRemotely(response)
{      
    if (response != '')
    {
        var data = JSON.parse(response);

        //remove the pageloading-shim class once the dialog has displayed
        document.getElementById('shim').className = '';

        if (!data.refresh) 
        {
            isMobile = data.isMobile;

            //disable the order continue button
            document.getElementById('ordercontinuebutton').setAttribute("disabled","disabled");
            document.getElementById('ordercontinuebutton').removeEventListener('click', orderButtonCompleteOrder);
            if (!isMobile) 
            {
                if (document.getElementById('btn-confirm-left')) 
                {
                    document.getElementById('btn-confirm-left').className = 'btn-disabled-left';
                    document.getElementById('btn-confirm-middle').className = 'btn-disabled-middle';
                    document.getElementById('btn-confirm-right').className = 'btn-disabled-right-tick';
                    document.getElementById('btn-confirm-middle').innerHTML = '{#str_MessagePleaseWait#}';
                }
            } 
            else 
            {
                var btnConf = document.getElementById('btnConfirm');
                if (btnConf) 
                {
                    btnConf.classList.add('disabled');
                    document.getElementById('btnContinueContentFinal').innerHTML = '{#str_MessagePleaseWait#}';
                }
                document.getElementById('ordercontinuebutton').setAttribute('data-decorator', "");
            }

            //disable the back button
            if (!isMobile) 
            {
                var oldBackButton = document.getElementById('backButton');
                if (oldBackButton) 
                {
                    var backButton = oldBackButton.cloneNode(true);
                    backButton.setAttribute("disabled","disabled");
                    backButton.onclick = function() { return false; };
                    if (backButton.getElementsByClassName('btn-blue-arrow-left').length > 0) 
                    {
                        backButton.getElementsByClassName('btn-blue-arrow-left')[0].className = 'btn-disabled-left';
                        backButton.getElementsByClassName('btn-blue-middle')[0].className = 'btn-disabled-middle';
                        backButton.getElementsByClassName('btn-blue-right')[0].className = 'btn-disabled-right';
                    }

                    oldBackButton.parentNode.replaceChild(backButton,oldBackButton);
                }
            } 
            else 
            {
                var paymentBack = document.getElementById('paymentBack');
                if (paymentBack) 
                {
                    paymentBack.getElementsByClassName('backImage')[0].classList.add('disabled');
                    paymentBack.getElementsByClassName('btnDone')[0].classList.add('disabled');

                    var backButton = paymentBack.getElementsByClassName('btnDoneTop')[0];
                    if (backButton) 
                    {
                        backButton.removeAttribute('data-decorator');
                        backButton.removeAttribute('data-hash-url');
                    }
                }
            }

            var placeHolder = '<div id="checkout-content" class="form-row">';
            //placeHolder += '<a class="cancel" style="padding-top: -15px !important;" data-decorator="closePaymentWindow">{#str_ButtonCancel#}</a>';
            placeHolder += '</div>';

            createDialog('{#str_OrderWithCard#}', placeHolder, closeDialog(), 'unlimit');
        }
        else
        {
            //if its a refresh we need to destroy the iframe and display a message
            var errorDiv = document.createElement("div");
            errorDiv.className = "error";
            errorDiv.innerHTML = '{#str_OrderGenericError#}';
            
            document.getElementById("checkout-content").innerHTML = "";
            document.getElementById("checkout-content").appendChild(errorDiv);
        }

        //Close button could allow end user to close before confirmation recorded
        var button = document.createElement("button");
        button.className = "cancel cancel-string unlimitClose";
        button.id = "closeButton";
        button.setAttribute('data-decorator', 'closePaymentWindow');
        button.disabled = '';
        button.style.display = 'block';
        button.innerHTML = "{#str_ButtonClose#}";

        document.getElementById("checkout-content").appendChild(button);

        if (data.error == '') 
        {
            var ifrm = document.createElement("iframe");
            ifrm.setAttribute("src", data.payment_url);
            ifrm.setAttribute("name", "unlimitFrame");
            //make room for the error message if refresh
            if (data.refresh) {
                ifrm.style = "height: calc(100% - 225px) !important";
            }
            ifrm.className = "unlimitFrame";
            ifrm.sandbox = "allow-scripts allow-same-origin allow-popups allow-top-navigation-by-user-activation allow-forms";
        
            document.getElementById("checkout-content").appendChild(ifrm);

            // Once the iframe is loaded then dispatch a window resize event.
            // This will trigger the lightbox to position centrally.
            var checkFrameInt = setInterval(function()
            {
                var div = document.getElementById("checkout-content");
                if (div.innerHTML != "")
                {
                    var iframe = div.getElementsByTagName('iframe');

                    if (iframe.length > 0)
                    {
                        clearInterval(checkFrameInt);

                        iframe[0].onload = function()
                        {
                            setTimeout(function(){
                                try { 
                                    resizePopup();
                                } catch(e) {
                                    //console.log(e);
                                }
                            }, 500)
                        }
                    }
                }

            }, 1000);

            timer = setInterval(function(){

                if (counterInUse) {
                    counter--;
                    if (counter == 0) {
                        clearInterval(timer);

                        document.getElementById('ordercontinuebutton').removeAttribute("disabled");

                        //re-enable the order continue button
                        if (!isMobile)
                        {
                            document.getElementById('ordercontinuebutton').addEventListener('click', orderButtonCompleteOrder);

                            if (document.getElementById('btn-confirm-left')) 
                            {
                                document.getElementById('btn-confirm-left').className = 'btn-green-left';
                                document.getElementById('btn-confirm-middle').className = 'btn-green-middle';
                                document.getElementById('btn-confirm-right').className = 'btn-accept-right';
                                document.getElementById('btn-confirm-middle').innerHTML = '{#str_ButtonConfirmOrder#}';
                            }
                        }
                        else 
                        {
                            var btnConf = document.getElementById('btnConfirm');
                            if (btnConf) 
                            {
                                btnConf.classList.remove('disabled');
                                document.getElementById('btnContinueContentFinal').innerHTML = '{#str_ButtonConfirmOrder#}';
                            }
                            document.getElementById('ordercontinuebutton').setAttribute('data-decorator', 'orderButtonCompleteOrder');
                        }

                        //re enable the backButton
                        if (!isMobile)
                        {
                            var backButton = document.getElementById('backButton');
                            if (backButton) 
                            {
                                backButton.removeAttribute("disabled");
                                backButton.addEventListener('click', function() {
                                    previousOrderStage();
                                });
                                backButton.onclick = function() { return false; };
                                if (document.getElementsByClassName('btn-disabled-left').length > 0) 
                                {
                                    backButton.getElementsByClassName('btn-disabled-left')[0].className = 'btn-blue-arrow-left';
                                    backButton.getElementsByClassName('btn-disabled-middle')[0].className = 'btn-blue-middle';
                                    backButton.getElementsByClassName('btn-disabled-right')[0].className = 'btn-blue-right';
                                } 
                            }
                        } 
                        else 
                        {
                            var paymentBack = document.getElementById('paymentBack');
                            if (paymentBack) 
                            {
                                paymentBack.getElementsByClassName('backImage')[0].classList.remove('disabled');
                                paymentBack.getElementsByClassName('btnDone')[0].classList.remove('disabled');

                                var backButton = paymentBack.getElementsByClassName('btnDoneTop')[0];
                                if (backButton) 
                                {
                                    backButton.setAttribute('data-decorator', 'fnSetHashUrl');
                                    backButton.setAttribute('data-hash-url', 'shipping');
                                }
                            }
                        }
                    }
                }

                var xmlhttp = getxmlhttp();
                var serverPage = data.statusEndpoint;

                params = "request_id=" + data.requestId 
                params += "&merchant_order_id=" + data.merchantOrderId 
                params += "&access_token=" + data.accessToken
                params += "&unlimit_server=" + data.unlimitServer;

                var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
                if (csrfMeta) {
                    var csrfToken = csrfMeta.getAttribute('content');

                    if (typeof params !== 'undefined' && null !== params && params.length > 0) {
                        params += '&csrf_token=' + csrfToken;
                    } else {
                        params = 'csrf_token=' + csrfToken;
                    }
                }

                xmlhttp.open("POST", serverPage, true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                const paymenthMethod = data.paymentMethod;

                xmlhttp.onreadystatechange = function()
                {
                    if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
                    {
                        var response = xmlhttp.responseText;
                        var responseObj = JSON.parse(response);
                        if ( responseObj.hasOwnProperty("response")) {
                            responseObj = JSON.parse(responseObj["response"]);
                            if ( responseObj.hasOwnProperty("data")) {
                                if (responseObj["data"].hasOwnProperty(0)) {
                                    if (responseObj["data"][0].hasOwnProperty("payment_data")) {
                                        if (responseObj["data"][0]["payment_data"]["status"] === 'COMPLETED' 
                                        || responseObj["data"][0]["payment_data"]["status"] === 'AUTHORIZED' 
                                        || responseObj["data"][0]["payment_data"]["status"] === 'DECLINED') {
                                            complete(JSON.stringify(responseObj["data"][0]), data.manualCallbackURL, data.cancelURL, paymenthMethod, responseObj["data"][0]["payment_data"]["status"]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //need a new token
                    else if (xmlhttp.status == 401)
                    {
                        if (inProgress == false) {
                            inProgress = true;
                            clearInterval(timer);
                            callEndPoint(true);
                            return;
                        }
                    }
                }
                xmlhttp.send(params);

            }, 10000);
        }
        else
        {
            var errorDiv = document.createElement("div");
            errorDiv.className = "error";
            errorDiv.innerHTML = data.error;
            
            document.getElementById("checkout-content").appendChild(errorDiv);
            document.getElementById("checkout-content").appendChild(button);
        }
    }
    inProgress = false;
}

function dropdown()
{
    var selectorOuterDiv = document.createElement('div');
    selectorOuterDiv.setAttribute('class', 'wizard-dropdown');

    var selector = document.createElement('select');
    selector.id = 'paymentgatewaycode';
    selector.name = 'paymentgatewaycode';
    selector.setAttribute('class', 'wizard-dropdown');
    selector.setAttribute('data-decorator', 'forceSelectCard');
    selector.addEventListener('change', function(event) {
        forceSelectCard();
    });

    selectorOuterDiv.appendChild(selector);
    creditCardContainer.appendChild(selectorOuterDiv);

    var option = document.createElement('option');
    option.value = '';
    option.appendChild(document.createTextNode('-- {$str_DropDownPleaseSelectAPaymentType} --'));
    selector.appendChild(option);

    //Assign the array of PaymentMethodList from the config file
    payTypeArray = new Array();

    var paymentMethods = '{$paymentMethodList}';

    var paymentMethodList = [];
    if (paymentMethods !== '') {
        paymentMethodList = paymentMethods.split(',');
    }

    for (let x = 0; x < paymentMethodList.length; x++) {
        payTypeArray.push(new payType(paymentMethodList[x], paymentMethodList[x]));
    }

    if (payTypeArray)
    {
        for (var i = 0; i < payTypeArray.length; i++)
        {
            var option = document.createElement('option');
            option.value = payTypeArray[i].id;

            if (option.value == '{$paymentgatewaycode}')
            {
                option.selected = 'selected';
            }

            option.appendChild(document.createTextNode(payTypeArray[i].name));
            selector.appendChild(option);

        }
    }
}

function complete(responseObj, url, cancelURL, paymenthMethod, status) {
    clearInterval(timer);
    buildForm((status === 'DECLINED') ? cancelURL : url, responseObj);

    var btn = document.getElementById('closeButton');

    //if we don't have a button then the lightbox is closed so redirect automatically
    if (btn) {
        btn.disabled = '';
        btn.style.display = 'block';
        btn.setAttribute("data-decorator", "submitUnlimit");
    } else {
        submitUnlimit();
    }
}

function submitUnlimit() {
    try {
        document.unlimitform.submit();
    } catch(e) {
        closePaymentWindow();
    }
}

function buildForm(url, payloadVal) {
    var form = document.createElement('form');
    form.name = "unlimitform";
    form.id = "unlimitform";
    form.action = url;
    form.method = 'post';

    var inputPayload = document.createElement('input');
    inputPayload.type = 'hidden';
    inputPayload.name = 'payload';
    inputPayload.value = payloadVal;

    form.appendChild(inputPayload);
    document.body.appendChild(form);
}