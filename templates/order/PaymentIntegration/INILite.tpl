<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>{$appname} - {#str_MessageTransferring#}</title>

{include file="includes/maininclude.tpl"}

{literal}

<script language=javascript src="{/literal}{$serverprotocol}{literal}plugin.inicis.com/pay40.js" {/literal}{$nonce}{literal}></script>

<script language=javascript {/literal}{$nonce}{literal}>
StartSmartUpdate();
</script>

<script type="text/javascript" {/literal}{$nonce}{literal}>
/* define an empty onunload event to force the onload event to be called when clicking the back button */
window.onunload = function(){};

function initialize()
{
	/* a cookie is used to detect if the user has clicked on the back button */
	var cookieName = "{/literal}{$ccicookiename}{literal}";
	var cookieValue = "{/literal}{$ccicookievalue}{literal}";

	if ((cookieValue != "") && (readCookie(cookieName) == cookieValue))
	{
		document.ini.action = "{/literal}{$cancel_url}{literal}";
		document.ini.method = "POST";
		eraseCookie(cookieName);

		setTimeout(transfer, 50);

		return false;
	}
	else
	{
		createCookie(cookieName, cookieValue, 2);

		if (pay(ini))
		{
			document.ini.action = "{/literal}{$returnPath}{literal}";
			document.ini.method = "POST";

			document.ini.submit();
		}

		return true;
	}
}

function showProgress()
{
	/*refresh the progress image for ie*/
	if (navigator.userAgent.indexOf("MSIE") != -1)
	{
		document.getElementById("progress").src = "{/literal}{$brandroot}{literal}/images/progress.gif";
	}

	/*show the progress image*/
	document.getElementById("progress").style.visibility = "visible";
}

function transfer()
{
	document.ini.submit();

	return true;
}

function pay(frm)
{
	if (document.INIpay == null || document.INIpay.object == null)
	{
		alert("\n{/literal}{$failureMessage1}{literal}\n\n{/literal}{$failureMessage2}{literal}\n\n{/literal}{$failureMessage3}{literal}");
		return false;
	}
	else
	{
		if (MakePayMessage(frm))
		{
			return true;
		}
		else
		{
			alert({/literal}{$cancelMessage}{literal});
			initialize();
			return false;
		}
	}
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
<table id="requesttable" width="100%">
<tr align="center"><td><img id="progress" src="{$brandroot}/images/progress.gif" style="visibility:hidden"></td></tr>
</table>

<form id="ini" name="ini" action="" method="" accept-charset="utf-8">
{foreach from=$parameter key=name item=value}
<input type="hidden" name="{$name}" value="{$value}">
{/foreach}
</form>
</body>
</html>