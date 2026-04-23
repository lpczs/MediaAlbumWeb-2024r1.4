{literal}
/* define an empty onunload event to force the onload event to be called when clicking the back button */
window.onunload = function(){};
var doSubmit = true;

function initializePaymentGateway()
{
    /* a cookie is used to detect if the user has clicked on the back button */
    var cookieName = "{/literal}{$ccicookiename}{literal}";
    var cookieValue = "{/literal}{$ccicookievalue}{literal}";

    if ((cookieValue != "") && (readCookie(cookieName) == cookieValue))
    {
        document.requestform.action = "{/literal}{$cancel_url}{literal}";
        document.requestform.method = "POST";
        eraseCookie(cookieName);
    }
    else
    {
        document.requestform.action = "{/literal}{$payment_url}{literal}";
        document.requestform.method = "{/literal}{$method}{literal}";
        createCookie(cookieName, cookieValue, 2);

		{/literal}
		{if $ispaypalplus == 'true'}
			{literal}
				PAYPAL.apps.PPP.doCheckout();
				doSubmit = false;
			{/literal}
		{/if}
		{literal}
    }

	/* post the data using a timer to allow the progress bar to animate under firefox */
	setTimeout(transfer, 50);

	return false;
}

function transfer()
{
	if (doSubmit)
	{
		document.requestform.submit();
	}

    /* refresh the progress image for ie */
    if (navigator.userAgent.indexOf("MSIE") != -1)
    {
        document.getElementById("progress").src = "{/literal}{$brandroot}{literal}/images/progress.gif";
    }

    /* show the progress image */
    document.getElementById("progress").style.visibility = "visible";

    return false;
}
{/literal}