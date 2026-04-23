var gOrderFound = false;
var gOrderContentLength = 0;
var gRetryCount = 40;
var gAPIRetryCount = 40;
var gApiOrderFound = false;

function callEndPoint()
{
    // check if all order metadata fields are validated before trying to initialise the payment
    if (lightBoxGatewayCheckOrderMetadataValidity())
    {
        toggleWaitingSpinner();

        var shim = document.getElementById('shim');    
        shim.style.display = 'block';
        shim.className = 'pageloading-shim';

        var postParams = [];

        postParams.push("paymentmethodcode=" + "CARD");
        postParams.push("paymentgatewaycode=" + "");
        postParams = postParams.join("&");
                
        setTimeout(function(){
            processAjax("requestpaymentparams",".?fsaction=Order.requestPaymentParams", "POST", postParams, getPaymentParamsRemotely);
            //remove the pageloading-shim class once the dialog has displayed
            document.getElementById('shim').className = '';   
       }, 500)
    }
}

/*
* This function will make a head request or post request to the server to 
* try and see if the order has been inserted. It will carry out 20 head requests
* to the status url if it is not found it will wait 3 seconds and call itself again
* if all the 20 head requests fail it will then call the api using a post request
* containing the session id. When the order is found the user is redirected to the confirm page
*/

function getCacheFile(manualUrl){

    var statusurl = "{$wechatparams.statusurl}";
    var sessionRef = "{$wechatparams.ref}";
    
    var http = new XMLHttpRequest();

    setTimeout(function(){
        http.open("HEAD", statusurl, true);
        http.send();

        if(!gAjaxRunning)
        {
            http.abort();
            return;
        }

        http.onreadystatechange = function(){
            if(this.status == 404){
                gOrderFound = false;
            }
            else if(this.status == 200)
            {
                gOrderFound = true;
            }
            
            gOrderContentLength = parseInt(this.getResponseHeader("Content-Length"));
                
        }

        /*
            * if the file has been found and the content length is greater than 0
            * Then we know that the order has been inserted and we can redirect
            */

        if(gOrderFound && gOrderContentLength > 0)
        {
            gRetryCount = 0;
            location.href = manualUrl;
            return;
        }
        else
        {
            //We dont have the order so decrement the count and try again
            
            gRetryCount--;

            if(gRetryCount > 0)
            {
                getCacheFile(manualUrl);
            }
            else if(gRetryCount == 0)
            {
                /*
                * The head request has returned nothing so try the api end point
                */
                var postParams = [];

                postParams.push("cciref=" + sessionRef);
                postParams = postParams.join("&");

                //Call the API to check if there is a record in the CCI table
                processAjax("queryccitable",".?fsaction=AjaxAPI.callback&cmd=QUERYCCITABLE", "POST", postParams, processAPI);

                function processAPI(response)
                {
                    var orderFound = JSON.parse(response);
                    
                    if(orderFound.error != "")
                    {  
                        location.href = "{$wechatparams.cancelurl}";
                    }
                    
                    if(orderFound.orderfound)
                    {
                        //Redirect here
                        gAPIRetryCount = true;
                        gAPIRetryCount = 0;
                        location.href = manualUrl;
                    }
                    else
                    {
                        gAPIRetryCount--;
                        
                        if(gAPIRetryCount > 0)
                        {
                            
                            setTimeout(function(){
                                if(!gAjaxRunning)
                                {
                                    return false;
                                }
                                else
                                {
                                    processAjax("queryccitable",".?fsaction=AjaxAPI.callback&cmd=QUERYCCITABLE", "POST", postParams, processAPI);
                                }
                            }, 3000)
                        }
                    }
                }    
            }
        }
    }, 3000);
} 

function getPaymentParamsRemotely(response)
{                
    var data = JSON.parse(response);
    
    if (data.result == 1)
    {
        //Allow the ajax calls
        gAjaxRunning = true;

        //Reset the retry count
        if(gRetryCount < 40 || gAPIRetryCount < 40)
        {
            gRetryCount = 40;
            gAPIRetryCount = 40;
        }
        
        createDialog(data.title, data.content, '', 'WeChat');
        getCacheFile(data.manualCallback)
    }
    else
    {
        location.href = data.redirecturl;
    }
    
}