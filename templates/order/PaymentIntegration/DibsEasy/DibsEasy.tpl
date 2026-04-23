function closeDibsPaymentWindow()
{
    closeDialog();
    
    var classToRemove = 'thirdPartyPaymentScreen dibs-easy';
    dialogOuter.className = dialogOuter.className.replace(classToRemove, '');				
}

function callEndPoint()
{
    // check if all order metadata fields are validated before trying to initialise the payment
    if (lightBoxGatewayCheckOrderMetadataValidity())
    {
        toggleWaitingSpinner();

        var dibsPlaceHolder = '<div id="dibs-checkout-content"></div>'
        dibsPlaceHolder += '<a class="cancel" data-decorator="closeDibsPaymentWindow" class="cancel-string">{#str_ButtonCancel#}</a>';
        
        createDialog('{#str_OrderWithCard#}', dibsPlaceHolder, closeDialog(), 'dibs-easy');
        
        //While the Dialog is open resize the box and Dibs will fill the content
       
        var adjustPosition = setInterval(function(){
            var checkoutContent = document.getElementById('dibs-checkout-content');
            
            if(checkoutContent && checkoutContent.clientHeight <= 600)
            {
                resizePopup();  
            }
            else
            {
                clearInterval(adjustPosition);
            }
        }, 2000);

        var shim = document.getElementById('shim');    
        shim.style.display = 'block';
        shim.className = 'pageloading-shim';

        var postParams = [];

        postParams.push("paymentmethodcode=" + "CARD");
        postParams.push("paymentgatewaycode=" + "");
        postParams = postParams.join("&");
                
        setTimeout(function(){
            processAjax("requestpaymentparams",".?fsaction=Order.requestPaymentParams", "POST", postParams, getPaymentParamsRemotely);
            //remove the pageloading-shim class once the dialog has displayed
            document.getElementById('shim').className = '';  
             
       }, 500)
    }
}

function getPaymentParamsRemotely(response)
{                
    var data = JSON.parse(response);

    var paymentOptions = {
        checkoutKey: data.checkoutkey,
        paymentId: data.paymentid,
        language: data.language
    }        

    var checkout = new Dibs.Checkout(paymentOptions);

    //This is for the invoice payments
    checkout.on('payment-completed', function(response) { 
        window.location = data.manualurl + '&paymentId=' + response.paymentId;
    });
}