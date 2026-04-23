function closeBancardPaymentWindow()
{
    closeDialog();
    
    var classToRemove = 'thirdPartyPaymentScreen bancard';
    dialogOuter.className = dialogOuter.className.replace(classToRemove, '');				
}

function callEndPoint()
{
    // check if all order metadata fields are validated before trying to initialise the payment
    if (lightBoxGatewayCheckOrderMetadataValidity())
    {
        toggleWaitingSpinner();
        
        var shim = document.getElementById('shim');    
        shim.style.display = 'block';
        shim.className = 'pageloading-shim';

        var postParams = [];

        postParams.push("paymentmethodcode=" + "CARD");
        postParams.push("paymentgatewaycode=" + "");
        postParams = postParams.join("&");
                
        setTimeout(function(){
            processAjax("requestpaymentparams",".?fsaction=Order.requestPaymentParams", "POST", postParams, getPaymentParamsRemotely);             
       }, 500)
    }
}

function getPaymentParamsRemotely(response)
{                
    //remove the pageloading-shim class once the dialog has displayed
    document.getElementById('shim').className = '';  

    if (response != '')
    {
        var data = JSON.parse(response);

        var bancardPlaceHolder = '<div id="bancard-checkout-content"></div>'
        bancardPlaceHolder += '<a class="cancel" data-decorator="closeBancardPaymentWindow" class="cancel-string">{#str_ButtonCancel#}</a>';
        
        createDialog('{#str_OrderWithCard#}', bancardPlaceHolder, closeDialog(), 'bancard');

        var styles =
        {
            "form-background-color": "{$styles.formbackgroundcolor}",
            "button-background-color": "{$styles.buttonbackgroundcolor}",
            "button-text-color": "{$styles.buttontextcolor}",
            "button-border-color": "{$styles.buttonbordercolor}",
            "input-background-color": "{$styles.inputbackgroundcolor}",
            "input-text-color": "{$styles.inputtextcolor}",
            "input-placeholder-color": "{$styles.inputplaceholdercolor}"
        }

        if (data.processid != '')
        {
            Bancard.Checkout.createForm(
                                        'bancard-checkout-content', 
                                        data.processid,
                                        {
                                            styles: styles
                                        });

            // Once the iframe is loaded then dispatch a window resize event.
            // This will trigger the lightbox to position centrally.
            var checkFrameInt = setInterval(function()
            {
                var div = document.getElementById("bancard-checkout-content");
                if (div.innerHTML != "")
                {
                    var iframe = div.getElementsByTagName('iframe');

                    if (iframe.length > 0)
                    {
                        clearInterval(checkFrameInt);

                        iframe[0].onload = function()
                        {
                            setTimeout(function(){
                                global.dispatchEvent(new Event('resize'));
                            }, 500)
                        }
                    }
                }

            }, 1000);
        }
    }
    else
    {
        document.getElementById('shim').style.display = 'none';
        alert("{#str_ErrorConnectFailure#}");
    }
}