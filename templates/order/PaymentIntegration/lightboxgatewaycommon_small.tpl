function lightBoxGatewayCheckOrderMetadataValidity()
{
	// check if all order metadata fields are validated before trying to initialise the payment
	var metadataValidity = checkMetadataValidity('contentPanelPayment', true);
	var metadataValid = !metadataValidity.hasAnError;

	if (metadataValid)
	{
		saveTempMetadata();
	}

	return metadataValid;
}

if (window.addEventListener)
{
	gRequestPaymentParamsRemotely = false;
	
	var paymentOptionsVisible = (document.getElementById('paymenttableobj').style.display == 'none' ? false : true);

	// If the payment method options are visible then we need to see if we need to redirect the action.
	if (paymentOptionsVisible)
	{
		var paymentMethodsLength = document.getElementsByName('paymentmethods').length;
		for (var i = 0; i < paymentMethodsLength; i++)
		{
			var elm = document.getElementsByName('paymentmethods')[i];
			if ((elm.checked) && ((elm.value === 'CARD') || (elm.value === 'KLARNA')))
			{
				// Set the gRequestPaymentParamsRemotely property.
				gRequestPaymentParamsRemotely = (elm.getAttribute("data-requestparamsremotley") == 'true') ? true : false;
			}
		}
	}

	window['paymentMethodClick'] = function ()
	{
		/* loop through all the shpping methods to see which one has been selected */
		var paymentMethodsLength = document.getElementsByName('paymentmethods').length;
		for (var i = 0; i < paymentMethodsLength; i++)
		{
			var elm = document.getElementsByName('paymentmethods')[i];
			if (elm.checked)
			{
				elm.parentNode.classList.add('optionSelected');

				// Set the gRequestPaymentParamsRemotely property.
				gRequestPaymentParamsRemotely = (elm.getAttribute("data-requestparamsremotley") == 'true') ? true : false;
			}
			else
			{
				elm.parentNode.classList.remove('optionSelected');
			}
		}

		// Set correct action on button.
		acceptTermsAndConditions();
	}
}