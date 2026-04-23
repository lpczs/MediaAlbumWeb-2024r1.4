function callEndPoint()
{
    // check if all order metadata fields are validated before trying to initialise the payment
    if (lightBoxGatewayCheckOrderMetadataValidity())
    {
        toggleWaitingSpinner();

        var shim = document.getElementById('shim');

        shim.style.display = 'block';

        var postParams = [];

        postParams.push('paymentmethodcode=' + 'CARD');
        postParams.push('paymentgatewaycode=' + '');
        postParams = postParams.join('&');

        setTimeout(function(){
        processAjax('requestpaymentparams','.?fsaction=Order.requestPaymentParams', 'POST', postParams, getPaymentParamsRemotely);
        }, 500)
    }
}

function getPaymentParamsRemotely(response)
{

    var data = JSON.parse(response);

    if (data.result == 1)
    {
        var url = '{$fallbackurl}';

        if(!data.sslenabled || data.ismobile)
        {
            location.href = url + data.transactioncode
        }
        else
        {

            function PagAbort()
            {
                //Check if the shim exists
                var shim = document.getElementById('shim');

                if(document.getElementById('shim'))
                {
                    shim.style.display = 'none';
                }
            }

            PagSeguroLightbox(data.transactioncode, {
                success: function(){
                    location.href = data.manualurl + '&transaction_id=' + arguments[0]
                },
                abort: PagAbort
            });
        }
    }
    else
    {
        //Check if the shim exists
        var shim = document.getElementById('shim');

        if(document.getElementById('shim'))
        {
            shim.style.display = 'none';
        }

        var errorString = '<p>{#str_ErrorPaymentFailed1#}</p>';

        if ((Array.isArray(data.errors)) && (data.errors.length > 0))
        {
            var errorCodes = '';

            for (var i = 0; i < data.errors.length; i++)
            {
            errorCodes += data.errors[i].code + ', ';
            }

            errorCodeEnd = (errorCodes.length - 2);

            errorCodes = errorCodes.substring(0, errorCodeEnd);

            errorString += '<p>{#str_ErrorPaymentFailed2#} ' + errorCodes + '</p>';
        }

        errorString += '<div class="contentBtn" data-decorator="closeDialog"><div class="btn-green-left"></div><div class="btn-green-middle">{#str_ButtonOk#}</div><div class="btn-green-right"></div></div>';

        createDialog('<p>{#str_TitleError#}</p>', errorString);
    }
}
