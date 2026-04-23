{include file="order/PaymentIntegration/lightboxgatewayform.tpl"}

/**
* Initialise the light box.
*/
var lightBoxFormPopup = new lightBoxFormPopup({
    contentClass: 'authorizenet',
    fields: {
        nameOnCard: {
            show: false, 
            required: false
        }
    },
    executePayment: sendPaymentDataToAnet,
    validateCardNumber: validateCardnumber,
    validateExpiryDate: validateExpiry
});

/**
* Validate the card number.
* 
* @param pCardNumber Card number to be tested.
*/
function validateCardnumber(pCardNumber) {
    // Set card data.
    var cardData = {
        cardNumber: pCardNumber
    };

    processPayment(cardData, function(pResponse){
        // Check an error.
        if (pResponse.messages.resultCode === "Error") {
            showFormError(pResponse.messages.message, ['E_WC_05']);
        }
    });
}

/**
* Validate the expiry date.
* 
* @param pExpiryDate Expiry date to be tested.
*/
function validateExpiry(pExpiryDate) {
    // Set card data.
    var cardData = {
        month: pExpiryDate.substr(0, 2),
        year: pExpiryDate.substr(2, 2)
    };

    processPayment(cardData, function(pResponse){
        // Check an error.
        if (pResponse.messages.resultCode === "Error") {
            showFormError(pResponse.messages.message, ['E_WC_06', 'E_WC_07', 'E_WC_08']);
        }
    });
}


/**
* Function called when the submit button gets clicked. 
*/
function sendPaymentDataToAnet(pCardData) {

    // Set card data.
    var cardData = {
        cardNumber: pCardData.cardNumber,
        month: pCardData.expiry.substr(0, 2),
        year: pCardData.expiry.substr(2, 2),
        cardCode: pCardData.securityCode
    };

    processPayment(cardData, responseHandler)
}

/**
* Function called the paymentgateway. 
*/
function processPayment(pCardData, pCallBack) {

    // Set auth data.
    var authData = {
        clientKey: "{$apipublickey}",
        apiLoginID: "{$apiloginid}"
    };

    var secureData = {
        authData: authData,
        cardData: pCardData
    };

    // Validate the form data via the API.
    Accept.dispatchData(secureData, pCallBack);
}

/**
* Response handler for the form validation
*
* @param obj pResponse Response object from the validation process. 
*/
function responseHandler(pResponse) {
    // Check errors.
    if (pResponse.messages.resultCode === "Error") {
        // Rebuild the form with user data
        lightBoxFormPopup.rebuildFormContent();

        // Highlight the field with an error.
        showFormError(pResponse.messages.message);
    } else {
        // Generate the payment on AuthorizeNet server.
        var postParams = 'datadescriptor=' + pResponse.opaqueData.dataDescriptor + '&datavalue=' + pResponse.opaqueData.dataValue + '&token=AuthorizeNet&ccitype=AuthorizeNet';
        processAjax('processpaymenttoken','.?fsaction=AjaxAPI.callback&cmd=PROCESSPAYMENTTOKEN', 'POST', postParams, processRequestCallback);
    }
}

/**
* Function executed to detecte an error from the paymentgateway.
*
* @param pMessages Error message list.
* @param pErrorToCheck Specifc type of error to be checked.
*/
function showFormError(pMessages, pErrorToCheck) {

    if (pErrorToCheck === undefined) {
        pErrorToCheck = [];
    }

    var i = 0;
    var messageLength = pMessages.length

    while (i < messageLength) {
        // Show the error.
        switch(pMessages[i].code) {
            case 'E_WC_05': {
                if ((pErrorToCheck.length === 0) || (pErrorToCheck.indexOf('E_WC_05') !== -1)) {
                    lightBoxFormPopup.setCardNumberError();
                }
                break;
            }
            case 'E_WC_06': {
                if ((pErrorToCheck.length === 0) || (pErrorToCheck.indexOf('E_WC_06') !== -1)) {
                    lightBoxFormPopup.setExpiryError(true);
                }
                break;
            }
            case 'E_WC_07': {
                if ((pErrorToCheck.length === 0) || (pErrorToCheck.indexOf('E_WC_07') !== -1)) {
                    lightBoxFormPopup.setExpiryError(true);
                }
                break;
            }
            case 'E_WC_08': {
                if ((pErrorToCheck.length === 0) || (pErrorToCheck.indexOf('E_WC_08') !== -1)) {
                    lightBoxFormPopup.setExpiryError(true);
                }
                break;
            }
        }
        i = i + 1;
    }
}

/**
* Callback from the server call.
* Redirect the end user if the payment succeed.
*
* @param pResponse paymentgateway response.
*/
function processRequestCallback(pResponse) {
    // Complete the order if the payment succeed
    if (pResponse.error === '') {
        // Call manual callback.
        location.href = pResponse.redirecturl;
    } else {
        // Rebuild the from with user data.
        lightBoxFormPopup.rebuildFormContent();

        // Display a generic error.
        lightBoxFormPopup.setGenericError(pResponse);
    }
}

/**
* Entry point for the lighbox fomr content. 
*/
function callEndPoint() {
    // Use generic form.
    lightBoxFormPopup.showGenericForm();
}