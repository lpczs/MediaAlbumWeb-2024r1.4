function lightBoxGatewayCheckOrderMetadataValidity()
{
	// check if all order metadata fields are validated before trying to initialise the payment
	var metaDataValid = validateOrderMetaData(true);

	if (metaDataValid)
	{
		lightBoxGatewaySaveMetadata();
	}

	return metaDataValid;
}

if (window.addEventListener)
{
    window.addEventListener("DOMContentLoaded", function()
	{
		// Default gRequestPaymentParamsRemotely to be false, we update this if we are using a lightbox gateway.
		gRequestPaymentParamsRemotely = false;

		// Check if payment option list is visible.
		var paymentOptionsVisible = (document.getElementById('paymenttableobj').style.display == 'none' ? false : true);

		// If the payment method options are visible then we need to see if we need to redirect the action.
		if (paymentOptionsVisible)
		{
			/**
			* We need to check what the default payment is when we load the page
			* if it is Card then we need to set the action of the complete order button
			* to be the callEndPoint function
			*/

			var paymentMethod = '';
			var paymentMethodRadios = document.querySelectorAll("div#paymentMethodsList input[name='paymentmethods']");
			
			// IE returns multiple selector results as a NodeList rather than an array
			// so we need to call a new methid for each object.
			Array.prototype.forEach.call(paymentMethodRadios, function (radio){
				if (radio.checked)
				{
					// If the payment method is set to card we need to adjust the action of the ordercontinuebutton.
					gRequestPaymentParamsRemotely = (radio.getAttribute("data-requestparamsremotley") == 'true') ? true : false;
					paymentMethod =  radio.value;
				}
			});
		}

		/**
		 * We need to detect when the user changes payment methods so we can change the
		 * action of the complete order button:
		 * callEndPoint for pagseguro
		 * acceptDataEntry for all other methods
		 */

		var paymentMethodsList = document.getElementById('paymentMethodsList');

		paymentMethodsList.addEventListener('change', function(event)
		{
			var target = event.target;

			if (event.target.id === 'paymentgatewaycode') {
				target = document.querySelector('input[name="paymentmethods"]:checked');
			}
			gRequestPaymentParamsRemotely = ((target.checked == true) && ((target.getAttribute("data-requestparamsremotley") == 'true') ? true : false));
		});
    });
}

/**
 * Save metadata to the server.
 */
function lightBoxGatewaySaveMetadata()
{
	// Metadata is valid so update the metadata in the session.
	postParams = ['stage=payment'];

	var orderFormCount = document.orderform.elements.length;

	for (var i = 0; i < orderFormCount; i++)
	{
		var inputElement = document.orderform.elements[i];

		if (inputElement.id.indexOf('keyword') === 0)
		{
			switch (inputElement.type)
			{
				case 'text':
				case 'textarea':
				{
					postParams.push(inputElement.name + '=' + encodeURIComponent(inputElement.value));
					break;
				}
				case 'radio':
				{
					if (inputElement.checked)
					{
						postParams.push(inputElement.name + '=' + encodeURIComponent(inputElement.value));
					}
					break;
				}
				case 'select-one':
				{
					postParams.push(inputElement.name + '=' + encodeURIComponent(inputElement.options[inputElement.selectedIndex].value));
					break;
				}
				case 'checkbox':
				{
					postParams.push(inputElement.name + '=' + ((inputElement.checked) ? '1' : '0'));
					break;
				}
			}
		}
	}

	postParams = postParams.join('&');
	processAjax("savemetadata",".?fsaction=AjaxAPI.callback&cmd=SAVETEMPMETADATA", 'POST', postParams);
};