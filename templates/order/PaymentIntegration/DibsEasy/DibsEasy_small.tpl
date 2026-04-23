function resizePopup()
{
    var storeLocator = document.getElementById('storeLocator');
    var storeInfo = document.getElementById('storeInfo');
    var ordersTermsAndCondtions = document.getElementById('ordersTermsAndCondtions');
    var componentChangeBox = document.getElementById('componentChangeBox');
    var shimObj = document.getElementById('shim');
    var windowHeight = document.documentElement.clientHeight;
    var dialogBox = document.getElementById('dialogOuter');

    if ((storeLocator) && (shimObj) && (storeLocator.style.display == "block"))
    {
        shimObj.style.height = document.body.offsetHeight + 'px';

        storeLocator.style.left = Math.round((shimObj.offsetWidth / 2) - (storeLocator.offsetWidth / 2)) + 'px';

        var finalPosition = (document.documentElement.clientHeight - storeLocator.offsetHeight) / 2;
        storeLocator.style.top = Math.round(finalPosition) + 'px';
    }

    if ((storeInfo) && (shimObj) && (storeInfo.style.display == "block"))
    {
        var viewportWidth =  Math.max(
            Math.max(document.body.offsetWidth, document.documentElement.offsetWidth),
            Math.max(document.body.clientWidth, document.documentElement.clientWidth)
        );

        windowHeight = document.documentElement.clientHeight;
        finalPosition = (windowHeight - storeInfo.offsetHeight) / 2;

        storeInfo.style.top = Math.round(finalPosition) + 'px';

        storeInfo.style.left = Math.round(viewportWidth * 1/2 - storeInfo.offsetWidth * 1/2) + 'px';
    }

    if ((ordersTermsAndCondtions) && (shimObj) && (ordersTermsAndCondtions.style.display == "block"))
    {
        shimObj.style.height = document.body.offsetHeight + 'px';

        ordersTermsAndCondtions.style.left = Math.round(shimObj.offsetWidth / 2 - ordersTermsAndCondtions.offsetWidth/2)+'px';

        var viewportHeight =  Math.max(
        Math.max(document.body.offsetHeight, document.documentElement.offsetHeight),
        Math.max(document.body.clientHeight, document.documentElement.clientHeight)
        );
        viewportHeight = document.documentElement.clientHeight;
        ordersTermsAndCondtions.style.top = Math.round(viewportHeight / 2 - ordersTermsAndCondtions.offsetHeight/2) + 'px';
    }

    if ((dialogBox) && (shimObj) && (dialogBox.style.display == "block"))
    {
        shimObj.style.height = document.body.offsetHeight + 'px';

        dialogBox.style.left = Math.round(shimObj.offsetWidth / 2 - dialogBox.offsetWidth/2)+'px';

        var viewportHeight =  Math.max(
            Math.max(document.body.offsetHeight, document.documentElement.offsetHeight),
            Math.max(document.body.clientHeight, document.documentElement.clientHeight)
        );
        viewportHeight = document.documentElement.clientHeight;
        dialogBox.style.top = Math.round(viewportHeight / 2 - dialogBox.offsetHeight/2) + 'px';
    }
}

{include file="order/PaymentIntegration/DibsEasy/DibsEasy.tpl"}
{include file="order/PaymentIntegration/lightboxgatewaycommon_small.tpl"}