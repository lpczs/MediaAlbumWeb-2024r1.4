{literal}
function initializeReturn()
{
    /*post the data using a timer to allow the progress bar to animate under firefox*/
    setTimeout(transfer, 50);

    return false;
}

function transfer()
{
    document.paymentform.submit();

    /*refresh the progress image for ie*/
    if (navigator.userAgent.indexOf("MSIE") != -1)
    {
        document.getElementById("progress").src = "{/literal}{$brandroot}{literal}/images/progress.gif";
    }

    /*show the progress image*/
    document.getElementById("progress").style.visibility = "visible";

    return false;
}

window.addEventListener('DOMContentLoaded', function(event) {
	initializeReturn();
});
{/literal}

