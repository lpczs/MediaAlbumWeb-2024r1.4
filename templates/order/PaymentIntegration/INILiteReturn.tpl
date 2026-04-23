<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>{$appname} - {#str_MessageTransferring#}</title>
{include file="includes/maininclude.tpl"}

{literal}
<script type="text/javascript" {/literal}{$nonce}{literal}>
function initialize()
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
	initialize();
});

</script>
{/literal}

</head>
<body>
{include file="header_large.tpl"}
<p>
<table id="paymenttable" width="100%">
<tr align="center"><td class="text3">{#str_MessageTransferring#}<p></td></tr>
<tr align="center"><td class="text3">{#str_MessagePleaseWait#}<p></td></tr>
<tr align="center"><td><img id="progress" src="{$brandroot}/images/progress.gif" style="visibility:hidden"></td></tr>
</table>

<form id="paymentform" name="paymentform" action="{$server}" method="post" accept-charset="utf-8">
{foreach from=$parameters key=name item=value}
<input type="hidden" name="{$name}" value="{$value}">
{/foreach}
</form>
</body>
</html>