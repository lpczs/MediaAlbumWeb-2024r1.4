function callEndPointGeneric()
{
	// check if all order metadata fields are validated before trying to initialise the payment
    if (lightBoxGatewayCheckOrderMetadataValidity())
    {
		toggleWaitingSpinner();

		var postParams = [];

		postParams.push('paymentmethodcode=' + 'CARD');
        postParams.push('paymentgatewaycode=' + '');
        postParams = postParams.join('&');

		setTimeout(function(){
            processAjax("requestpaymentparams",".?fsaction=Order.requestPaymentParams", "POST", postParams, getPaymentParamsRemotely);    
       }, 500);
	}
}

function getPaymentParamsRemotely(response)
{
	var data = JSON.parse(response);

	var handler = {
		responseHandler: function(BOLT)
		{
			//this will never be called but needs to be declared
		},
		catchException: function(BOLT)
		{
			//do nothing, if it fails at this stage we don't want to fail the order
		}
	}

	bolt.launch(data, handler);
}