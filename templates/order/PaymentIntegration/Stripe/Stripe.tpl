function closeStripePaymentWindow()
{
    closeDialog();

    var classToRemove = 'thirdPartyPaymentScreen stripe';
    dialogOuter.className = dialogOuter.className.replace(classToRemove, '');
}

function mountStripeCardElement(pRemotePaymentParams)
{
    var stripe = Stripe('{$stripeparams.stripepublishablekey}');
    var options = {
        clientSecret: pRemotePaymentParams['clientsecret'],
      };
    var elements = stripe.elements(options);

    var style = {
        base: {
            color: '#32325d',
            lineHeight: '18px',
            fontFamily: 'Helvetica Neue, Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };

    // Create an instance of the card Element.
    var paymentElements = elements.create('payment', {literal}{defaultValues: { 
      billingDetails: {
      address: {
          line1: '{/literal}{$stripeparams.addressline1}{literal}',
          line2: '{/literal}{$stripeparams.addressline2}{literal}',
          city: '{/literal}{$stripeparams.addresscity}{literal}',
          state: '{/literal}{$stripeparams.addressstate}{literal}',
          postal_code : '{/literal}{$stripeparams.addresszip}{literal}',
          country: '{/literal}{$stripeparams.addresscountrycode}{literal}'
      },
      name: '{/literal}{$stripeparams.name}{literal}'  }}}{/literal});

  
    // Add an instance of the card Element into the card-element <div>.
    paymentElements.mount('#payment-element');

    paymentElements.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error)
        {
        displayError.textContent = event.error.message;
        }
        else
        {
        displayError.textContent = '';
        }
    });

    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        // handle card validation errors. If there are no errors then we can
        // show the shim and attempt to charge the card
        var displayError = document.getElementById('card-errors').textContent;

        if (displayError != '')
        {
            return false;
        }
        else
        {
            document.getElementById('innerdialogshim').style.display = 'block';
        }

        stripe.confirmPayment({
          elements,
          confirmParams: {
            return_url: pRemotePaymentParams.redirecturl,
          },
          redirect: "if_required"
        }).then(function(result) {
           document.getElementById('innerdialogshim').style.display = 'none';
          if (result.error) {
                // Show error to your customer (e.g., insufficient funds)
                alert(result.error.message);
            } else {
              // The payment has been processed!
                if (result.paymentIntent.status === 'succeeded') {
                // redireect to the manual callback URL
                  location.href = pRemotePaymentParams.redirecturl;
                  return false;
              }
            }
          });
    });

    var paymentRequest = stripe.paymentRequest({
        country: '{$stripeparams.addresscountrycode}',
        currency: '{$stripeparams.currencycode}',
        total: {
        label: '{$stripeparams.orderdescription}',
        amount: parseInt(pRemotePaymentParams.amount)
        },
        requestPayerName: true,
        requestPayerEmail: true
    });

    var prButton = elements.create('paymentRequestButton', {
        paymentRequest: paymentRequest
    });

    // Check the availability of the Payment Request API first.
    paymentRequest.canMakePayment().then(function(result) {
        if (result)
        {
            prButton.mount('#payment-request-button');
            document.getElementById('formheader').innerHTML = '{#str_OrderOrEnterYourPaymentDetail#}';

            paymentRequest.on('paymentmethod', function(ev) {
                // Confirm the PaymentIntent without handling potential next actions (yet).
                stripe.confirmCardPayment(
                  pRemotePaymentParams['clientsecret'],
                  {
                      payment_method: ev.paymentMethod.id
                  },
                  {
                      handleActions: false
                  }
                ).then(function(confirmResult) {
                  if (confirmResult.error) {
                    // Report to the browser that the payment failed, prompting it to
                    // re-show the payment interface, or show an error message and close
                    // the payment interface.
                    ev.complete('fail');
                  } else {
                    // Report to the browser that the confirmation was successful, prompting
                    // it to close the browser payment method collection interface.
                    ev.complete('success');
                    // Check if the PaymentIntent requires any actions and if so let Stripe.js
                    // handle the flow.
                    if (confirmResult.paymentIntent.status === "requires_action") {
                      // Let Stripe.js handle the rest of the payment flow.
                      stripe.confirmCardPayment(clientSecret).then(function(result) {
                        if (result.error) {
                          // The payment failed -- ask your customer for a new payment method.
                        } else {
                          // The payment has succeeded.
                          location.href = pRemotePaymentParams.redirecturl;
                        }
                      });
                    } else {
                      // The payment has succeeded.
                      location.href = pRemotePaymentParams.redirecturl;
                    }
                  }
                });
              });
        }
        else
        {
            document.getElementById('payment-request-button').style.display = 'none';
        }
    });
}

function callEndPoint()
{
    // check if all order metadata fields are validated before trying to initialise the payment
    if (lightBoxGatewayCheckOrderMetadataValidity())
    {
        toggleWaitingSpinner();

        var paymentForm = '<div id="payment-request-button" class="stripe-button"></div>';
        paymentForm += '<form action="charge" method="post" id="payment-form" class="formWrap">';
        paymentForm += '<p id="formheader" class="formHeader">{#str_OrderEnterYourPaymentDetail#}</p>';
        paymentForm += '<div class="form-row">';
        paymentForm += '<div id="payment-element" class="card-element">';
        paymentForm += '</div>';
        paymentForm += '<div id="card-errors" role="alert" class="error"></div>';
        paymentForm +=  '</div>';
        paymentForm += '<button type="submit">{#str_OrderSubmitPayment#}</button>';
        paymentForm += '</form>';
        paymentForm += '<a class="cancel" data-decorator="closeStripePaymentWindow" class="cancel-string">{#str_ButtonCancel#}</a>';

        createDialog('{#str_OrderWithCard#}', paymentForm, closeDialog(), 'stripe');

        var processingShim = document.createElement('div');
        processingShim.id = 'innerdialogshim';
        processingShim.setAttribute('class', 'innerdialogshim');

        document.getElementById('dialogContent').appendChild(processingShim);

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

    if (data.result == 1)
    {
        mountStripeCardElement(data);
    }
    else
    {
        location.href = data.redirecturl;
    }

}