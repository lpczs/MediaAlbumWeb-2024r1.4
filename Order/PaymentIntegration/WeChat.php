<?php
require_once __DIR__ . '/TaopixAbstractGateway.php';
require_once __DIR__ . '/Request/CurlHandler.php';
require_once __DIR__ . '/wechat/WxPay.NativePay.php';
require_once __DIR__ . '/wechat/Tao.WxPay.php';

class WeChat extends TaopixAbstractGateway 
{
    public function configure()
    {
        $resultArray = [
			'active' => true,
			'form' => '',
			'scripturl' => '',
			'script' => '',
			'action' => '',
            'gateways' => [],
			'orderstatusfile' => true,
			'requestpaymentparamsremotely' => true
        ];
        

        AuthenticateObj::clearSessionCCICookie();

        //Only allow the currencies that are in the config file

        if (strpos($this->config['CURRENCYLIST'], $this->session['order']['currencycode']) === false)
        {
            $resultArray['active'] = false;
        }

        if($this->session['ismobile'] == true)
        {
            $resultArray['active'] = false;
        }
        
        if ($resultArray['active'])
        {
            $smarty = SmartyObj::newSmarty('Order', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

            /**
             * WeChat uses seperate templates for all of it's javascript and functions
             * WE need to send across the the variables so the template has access to them
             */

            $templateDataArray = array();
            $templateDataArray['ref'] = $this->session['ref'];
			$templateDataArray['statusurl'] = UtilsObj::getOrderStatusCacheURL($this->session['weburl'], $this->session['items'][0]['itemuploadbatchref']);
            $templateDataArray['cancelurl'] = UtilsObj::correctPath($this->session['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];
            
            $smarty->assign('wechatparams', $templateDataArray);

            $script = $smarty->fetchLocale('order/PaymentIntegration/WeChat/WeChat_large.tpl');

            $resultArray['script'] = $script;
        }
        
        return $resultArray;
    }

    public function initialize()
    {
        $resultArray = [];

        $smarty = SmartyObj::newSmarty('CreditCardPayment', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

        $fixedUrlPath = UtilsObj::correctPath($this->session['webbrandweburl']);
        $cancelURL = $fixedUrlPath . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];

        AuthenticateObj::defineSessionCCICookie();
       
        if ($this->session['order']['ccidata'] == '')
        {
            $manualCallBackUrl = $fixedUrlPath . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'];

            $notifyPayment = new TaoNativePay();

            try{
                $paymentOptions = new TaoWxPayUnifiedOrder();
                //WeChat App secret
                $paymentOptions->SetKey($this->config['APPSECRET']);
                //WEChat app id 
                $paymentOptions->SetAppid($this->config['APPID']);
                //WeChat merchant Id
                $paymentOptions->SetMch_id($this->config['MERCHANTID']);
                //Product name that is sent to wechat
                $paymentOptions->SetBody(LocalizationObj::getLocaleString($this->session['items'][0]['itemproductname'], $this->session['browserlanguagecode'], true));
                //Product name that is sent to wechat
                $paymentOptions->SetAttach(LocalizationObj::getLocaleString($this->session['items'][0]['itemproductname'], $this->session['browserlanguagecode'], true));
                //Currency code sent to wechat
                $paymentOptions->SetFee_type($this->session['order']['currencycode']);
                //Session ref is used for the automatic, appened with a timestamp
                $paymentOptions->SetOut_trade_no($this->session['ref'] . '_' . date("YmdHis"));
                //WeChat requires the total to be in cents
                $paymentOptions->SetTotal_fee($this->session['order']['ordertotaltopay'] * 100);
                $paymentOptions->SetTime_start(date("YmdHis"));
                $paymentOptions->SetTime_expire(date("YmdHis", time() + 6000));
                $paymentOptions->SetGoods_tag(LocalizationObj::getLocaleString($this->session['items'][0]['itemproductname'], $this->session['browserlanguagecode'], true));
                $paymentOptions->SetNotify_url($this->config['NOTIFICATIONURL'] . '/PaymentIntegration/WeChat/WeChatCallback.php');
                //For the qr code payment the trade type must be NATIVE
                $paymentOptions->SetTrade_type("NATIVE");
                //Set the product ID to be the product code
                $paymentOptions->SetProduct_id($this->session['items'][0]['itemproductcode']);
            
                //Create the payment for WeChat
                $payment = $notifyPayment->GetPayUrl($paymentOptions);
                
                //Initialse the qrcode
                $barCode = new \Com\Tecnick\Barcode\Barcode();

                //Build the QrCode
                $qrCodeObj = $barCode->getBarCodeObj(
                    //Barcode type
                    'QRCODE',
                    //Data for the qrcode
                   $payment['code_url'],
                    //Height (use absolute or negative value as multiplication factor)
                    -5,
                    //Width (use absolute or negative value as multiplication factor)
                    -5,
                    //Qr code colour
                    'black',
                    // padding (use absolute or negative values as multiplication factors)
                    array(-2,-2,-2,-2)
                )->setBackgroundColor('white');

                //get png data returns the base64 data
                $qrCode = base64_encode($qrCodeObj->getPngData());

                //Build the html for the dialog
                $dialogContent = '<div class="col2 dialog-text">';
                $dialogContent .= '<img src="' . UtilsObj::correctPath($this->session['webbrandweburl']) . "/images/WeChat-app-icon.svg" .'" height="32" width="32" alt="WeChat app icon"/>';
                $dialogContent .= '<p>' . $smarty->get_config_vars('str_OrderScanQrCode') . '</p>';
                $dialogContent .= '<p class="advice">' . $smarty->get_config_vars('str_KeepBrowserOpen') . '</p>';
                $dialogContent .= '<a data-decorator="closeDialog" class="cancel-string">' . $smarty->get_config_vars('str_ButtonCancel') . '</a>';
                $dialogContent .= '</div>';
                $dialogContent .= '<div class="col2 qr-code">';
                $dialogContent .= '<img class="qr-code" src="data:image/png;base64,' . $qrCode . '" ' . '  ></img>';
                $dialogContent .= '</div>';

                //Return the 
                $resultArray = [
                    'result' => 1,
                    'manualCallback' => $manualCallBackUrl,
                    'content' => $dialogContent,
                    'title' => $smarty->get_config_vars('str_OrderPayWithWeChat')
                ];

                return json_encode($resultArray);

            }catch(Exception $e){  
                AuthenticateObj::clearSessionCCICookie();

                $smarty->assign('server', $cancelURL);
            }
        }
        else
        {
            AuthenticateObj::clearSessionCCICookie();

            $cancelReturnPath = UtilsObj::correctPath($this->session['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];
            $smarty->assign('cancel_url', $cancelReturnPath);
            $smarty->displayLocale('order/PaymentIntegration/PaymentRequest_large.tpl');
        }
    }

    public function confirm($pCallbackType)
    {
        
		$resultArray = $this->cciEmptyResultArray();
		$serverTimeStamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime = date('H:i:s');
		$this->loadSession = true;
        $this->session['order']['ccicachefileneeded'] = true;
        
		if($pCallbackType == 'automatic')
		{
            $this->loadSession = false;
            
            // Write the order status cache file
            Order_model::writeOrderStatusCacheFile('', false);
            
            //WEChat send their responses via curl so we need to capture it
            $xmlResponse = file_get_contents('php://input');
			$parsedXML = new SimpleXMLElement($xmlResponse);
			$authorisedstatus = 0;
			$authorised = 0;
            $update = false;
            
            //Parse the details from the XML returned

            $ref = $_GET['ref'];
            $orderTotal = (string) $parsedXML->total_fee;
            $transactionID = (string) $parsedXML->transaction_id;
            $formattedtransactionid = (string) $parsedXML->transaction_id;
            $formattedauthorisationid = (string) $parsedXML->transaction_id;
            $currencyCode = (string) $parsedXML->fee_type;
            $orderID = (string) $parsedXML->out_trade_no;
            $responseCode = (string) $parsedXML->result_code;
           

			if($parsedXML->result_code == "SUCCESS")
			{
				$authorised = 1;
				$authorisedstatus = 1;
			}
			else
			{
				$authorised = 0;
				$authorisedstatus = 0;
			}
        }
        else if($pCallbackType == 'manual' )
        {
            //We need to get the details from the CCI table
            $cciRef = $this->session['ref'];
            $cciEntry = PaymentIntegrationObj::getCciLogEntry($cciRef);

            $orderTotal = $cciEntry['formattedamount'];
            $currencyCode = $cciEntry['currencycode'];
            $ref = $cciEntry['sessionid'];
            $transactionID = $cciEntry['formattedtransactionid'];
            $formattedtransactionid = $cciEntry['formattedtransactionid'];
            $formattedauthorisationid = $cciEntry['formattedtransactionid'];
            $orderID = $cciEntry['orderid'];
            $authorised = $cciEntry['authorised'];
            $responseCode = $cciEntry['responsecode'];
            $update = true;
           
            Order_model::deleteOrderStatusCacheFile($this->session['items'][0]['itemuploadbatchref']);
        }

        $this->updateStatus = false;
        $resultArray['result'] = '';
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $orderTotal;
        $resultArray['formattedamount'] = $orderTotal;
        $resultArray['addressstatus'] = '';
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] = '';
        $resultArray['authorised'] = $authorised;
        $resultArray['authorisedstatus'] = $authorised;
        $resultArray['transactionid'] = $transactionID;
        $resultArray['formattedtransactionid'] = $formattedtransactionid;
        $resultArray['responsedescription'] = '';
        $resultArray['authorisationid'] = $transactionID;
        $resultArray['formattedauthorisationid'] = $formattedauthorisationid;
        $resultArray['bankresponsecode'] = '';
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['addressstatus'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = '';
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = '';
        $resultArray['business'] = '';
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = '';
        $resultArray['settleamount'] = '';
        $resultArray['script'] = '';
        $resultArray['scripturl'] = '';
        $resultArray['charityflag'] = '';
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymentmeans'] = '';
        $resultArray['paymenttime'] = $serverTime;
        $resultArray['paymentreceived'] = 1;
        $resultArray['formattedpaymentdate'] = $serverDate;
        $resultArray['settleamount'] = '';
        $resultArray['currencycode'] = $currencyCode;
        $resultArray['webbrandcode'] = '';
        $resultArray['update'] = $update;
        $resultArray['orderid'] = $orderID;
        $resultArray['parentlogid'] = 0;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = array();
        $resultArray['showerror'] = false;
        $resultArray['responsecode'] = $responseCode;
        	
		return $resultArray;
		
    }

    public function hashString($pParams, $pType)
	{
        return null;
	}

	public function verifyHash($pSuppliedHash, $pParams, $pType)
	{
		return null;
	}

	public function generateHash($pString)
	{
        return null;
	}

}

?>