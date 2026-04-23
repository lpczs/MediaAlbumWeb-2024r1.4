{include file="order/PaymentIntegration/PayUMoney/PayUMoney.tpl"}
{include file="order/PaymentIntegration/lightboxgatewaycommon_small.tpl"}

function callEndPoint()
{
	var lightboxScriptTag = document.createElement("script");

	lightboxScriptTag.src = '{$boltUrl}';
	lightboxScriptTag.setAttribute("bolt-color" , "e34524");
	lightboxScriptTag.setAttribute("bolt-logo", "");
	lightboxScriptTag.setAttribute("nonce", "[nonce]");
	lightboxScriptTag.id = "bolt";
	
	// Add callback to callEndPointGeneric after the script is loaded so we do not trigger this before everything is ready.
	if(lightboxScriptTag.readyState)
	{
		lightboxScriptTag.onreadystatechange = function()
		{
			if (lightboxScriptTag.readyState === "loaded" || lightboxScriptTag.readyState === "complete")
			{
				lightboxScriptTag.onreadystatechange = null;
				callEndPointGeneric();
			}
		};
	}
	else
	{
		lightboxScriptTag.onload = function()
		{
			callEndPointGeneric();
		};
	}

	document.getElementsByTagName('head')[0].appendChild(lightboxScriptTag);
}