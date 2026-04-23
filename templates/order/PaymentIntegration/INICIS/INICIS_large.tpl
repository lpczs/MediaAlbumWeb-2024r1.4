{include file="order/PaymentIntegration/lightboxgatewaycommon_large.tpl"}

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

        setTimeout(function() {
            processAjax("requestpaymentparams",".?fsaction=Order.requestPaymentParams", "POST", postParams, getPaymentParamsRemotely);
       }, 500)
    }
}

function getPaymentParamsRemotely(response)
{
	// Parse the json response.
	var data = JSON.parse(response);

    if (data.result == 1)
    {
		var form = document.getElementById('PaymentForm');
		var appendForm = false;

		// If the form element does not exist create it and say that we need to append it to the document.
		if (form === null)
		{
			form = document.createElement('form');
			form.id = 'PaymentForm';
			appendForm = true;
		}

		// Loop over each data variable sent back and build the payment form that INICIS use.
		for (var key in data)
		{
			// Do not include the result key.
			if (key !== 'result')
			{
				var elementId = 'payment-' + key;
				var appendToForm = false;

				// Get the element if it exists or create it if it does not.
				var element = document.getElementById(elementId);
				if (element === null)
				{
					element = document.createElement('input');
					element.type = 'hidden';
					element.name = key;
					element.id = 'payment-' + key;
					appendToForm = true;
				}

				// Set the value for the element.
				element.value = data[key];

				// If we need to append the element to the form do so.
				if (appendToForm)
				{
					form.appendChild(element);
				}
			}
		}

		// Attach the form element to the document if we need to.
		if (appendForm)
		{
			// Append the payment form
			document.getElementsByTagName('body')[0].appendChild(form);
		}

		// Initialise the INICIS payment option.
		INIStdPay.pay('PaymentForm');
		
		//remove the pageloading-shim class once the dialog has displayed
		var shim = document.getElementById('shim');
		shim.className = '';
		shim.style.display = 'none';
	}
}