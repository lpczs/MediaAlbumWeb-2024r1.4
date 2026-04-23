<?php
use Security\ControlCentreCSP;

class INILiteObj
{
    static function configure()
    {
        global $gSession;

        $resultArray = Array();
        $active = true;

        AuthenticateObj::clearSessionCCICookie();
        $smarty = SmartyObj::newSmarty('CreditCardPayment');
        $activate = 0;
        // test for INILite supported currencies
        $currencyList = '410';
        if (strpos($currencyList, $gSession['order']['currencyisonumber']) === false)
        {
            $active = false;
        }
        else
        {
            $locale = strtolower($gSession['browserlanguagecode']);
			$locale = substr($locale, 0, 2);

            // read in payment methods
			$INILiteConfig = PaymentIntegrationObj::readCCIConfigFile('../config/INILite.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
			$paymentMethodsArray = explode(',', $INILiteConfig['PAYMENTMETHODS']);

			foreach ($paymentMethodsArray as $method)
			{
				if ($method == 'Card')
				{
					$gateways[$method] = $smarty->get_config_vars('str_OrderINILITE_CARD');
				}

				if ($method == 'DirectBank')
				{
					$gateways[$method] = $smarty->get_config_vars('str_OrderINILITE_DIRECTBANK');
				}

				if ($method == 'VBank')
				{
					$gateways[$method] = $smarty->get_config_vars('str_OrderINILITE_VBANK');
				}

				if ($method == 'HPP')
				{
					$gateways[$method] = $smarty->get_config_vars('str_OrderINILITE_HPP');
				}
			}
        }

		$resultArray['gateways'] = $gateways;
        $resultArray['active'] = $active;
        $resultArray['form'] = '';
        $resultArray['scripturl'] = '';
        $resultArray['script'] = '';
        $resultArray['action'] = '';

        return $resultArray;
    }

    static function initialize()
    {
        global $gConstants;
        global $gSession;
		global $ac_config;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		// create an encoded failure message, use 3 strings, these will be displayed on different lines, separated using "\n" in template
		// the "\n" creates an encoding issue is used within the source string for mb_convert_encoding function
		$failureMessage1 = mb_convert_encoding("이니페이 플러그인 128이 설치되지 않았습니다. ", 'euc-kr', 'UTF8');
		$failureMessage2 = mb_convert_encoding("안전한 결제를 위하여 이니페이 플러그인 128의 설치가 필요합니다. ", 'euc-kr', 'UTF8');
		$failureMessage3 = mb_convert_encoding("다시 설치하시려면 Ctrl + F5키를 누르시거나 메뉴의 [보기/새로고침]을 선택하여 주십시오.", 'euc-kr', 'UTF8');
		$smarty->assign('failureMessage1', $failureMessage1);
		$smarty->assign('failureMessage2', $failureMessage2);
		$smarty->assign('failureMessage3', $failureMessage3);

		// create an encoded cancel message
		$cancelMessage = mb_convert_encoding("결제를 취소하셨습니다.", 'euc-kr', 'UTF8');
		$smarty->assign('cancelMessage', $cancelMessage);

		$cspActive = true;

		if (array_key_exists('CONTENTSECURITYPOLICY', $ac_config))
        {
            if ($ac_config['CONTENTSECURITYPOLICY'] === 'DISABLED')
            {
                $cspActive = false;
            }
        }

		if ($cspActive)
		{
			$cspBuilder = ControlCentreCSP::getInstance($ac_config);
			self::addCSPDetails($cspBuilder);
		}

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$INILiteConfig = PaymentIntegrationObj::readCCIConfigFile('../config/INILite.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' .$gSession['ref'];
			$successReturnPath = UtilsObj::correctPath($gSession['webbrandwebroot']) . '/PaymentIntegration/INILite/INILiteCallBack.php?ref='.$gSession['ref'];

			$merchant = $INILiteConfig['MERCHANTID'];
			$noInterest = $INILiteConfig['NOINTEREST'];
			$quotabase = $INILiteConfig['QUOTABASE'];

			$orderID = $gSession['ref'] . '_'. time();

			// INILite requires order text
			$orderData = $gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);
			$orderData = mb_convert_encoding($orderData, 'euc-kr', 'UTF8');


			$amount = $gSession['order']['ordertotaltopay'];
			$customerName = $gSession['order']['billingcontactlastname'].' '.$gSession['order']['billingcontactfirstname'];
			$customerName = mb_convert_encoding($customerName, 'euc-kr', 'UTF8');

			$email = $gSession['order']['billingcustomeremailaddress'];
			$currency = $gSession['order']['currencycode'];
			$telephonenumber = $gSession['order']['billingcustomertelephonenumber'];

			// selected payment gateway
			$paymentMethod = $gSession['order']['paymentgatewaycode'];

			$parameters = array(
				'gopaymethod'	=> $paymentMethod,
				'goodname'		=> $orderData,
				'price'		=> $amount,
				'buyername'			=> $customerName,
				'buyeremail'		=> $email,
				'parentemail'		=> $email,
				'buyertel'		=> $telephonenumber,
				'mid'		=> $merchant,
				'currency'		=> 'WON',
				'nointerest'		=> $noInterest,
				'quotabase'		=> $quotabase,
				'acceptmethod'		=> 'SKIN(ORIGINAL):HPP(2):OCB',
				'oid'		=> $orderID,
				'quotainterest'		=> '',
				'paymethod'		=> $paymentMethod,
				'cardcode'		=> '',
				'cardquota'		=> '',
				'rbankcode'		=> '',
				'reqsign'		=> 'DONE',
				'encrypted'		=> '',
				'sessionkey'	=> '',
				'uid'		=> '',
				'sid'		=> '',
				'version'		=> '4000',
				'clickcontrol'		=> ''
			);

			// define Smarty variables
			$smarty->assign('returnPath', $successReturnPath);
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('parameter', $parameters);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to DIBS
			$gSession['order']['ccidata'] = 'start';
			DatabaseObj::updateSession();

			$smarty->cachePage = true; // allow the page to be cached so that the browser back button works correctly
			$smarty->displayLocale('order/PaymentIntegration/INILite.tpl');
    	}
        else
        {
            // the user has clicked the back button
            AuthenticateObj::clearSessionCCICookie();

            $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
            $smarty->assign('server', $cancelReturnPath);

            $smarty->displayLocale('order/PaymentIntegration/INILite.tpl');
        }
    }

    static function cancel()
    {
        global $gSession;

        $resultArray = Array();

		$resultArray['result'] = '';
        $resultArray['ref'] = $gSession['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

    static function confirm($callBack)
    {
     	global $gSession;

		$resultArray = Array();
        $result = '';
        $authorised = false;
        $authorisedStatus = 0;
        $showError = false;
        $orderid = 0;
        $update = false;
		$cciCompletionMessage = '';
		$responseDescription = '';
 		$accountHolderName = '';
 		$senderName = '';
 		$responseCode = '';
 		$transactionRef = '';

		$smarty = SmartyObj::newSmarty('CreditCardPayment');

		$ref = $gSession['ref'];

		$INILiteConfig = PaymentIntegrationObj::readCCIConfigFile('../config/INILite.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
		$INILiteKey = $INILiteConfig['INILITEKEY'];

		$INILiteLibsPath = UtilsObj::getTaopixWebInstallPath('Order/PaymentIntegration/INILite/libs/INILiteLib.php');
		$INILiteDirectory =  UtilsObj::getTaopixWebInstallPath('Order/PaymentIntegration/INILite/');

		if (file_exists($INILiteLibsPath))
		{
			require_once($INILiteLibsPath);
		}
		else
		{

		}

		$inipay = new INILite;

		// Set Payment Information
		$inipay->m_inipayHome = $INILiteDirectory; //상점 수정 필요
		$inipay->m_key = $INILiteKey; //상점 수정 필요
		$inipay->m_ssl = "true"; 				//ssl지원하면 true로 셋팅해 주세요.
		$inipay->m_type = "securepay"; 			// 고정 (절대 수정 불가)
		$inipay->m_pgId = "INlite".$pgid; 		// 고정 (절대 수정 불가)
		$inipay->m_log = "true";              // true로 설정하면 로그가 생성됨(적극권장)
		$inipay->m_debug = "true";              // 로그모드("true"로 설정하면 상세로그가 생성됨. 적극권장)
		$inipay->m_mid = $mid; 					// 상점아이디
		$inipay->m_uid = $uid; 					// INIpay User ID (절대 수정 불가)
		$inipay->m_uip = getenv("REMOTE_ADDR"); 		// 고정 (절대 수정 불가)
		$inipay->m_goodName = iconv("utf-8", "euc-kr", $goodname);			// 상품명
		$inipay->m_currency = $currency;			// 화폐단위
		$inipay->m_price = $price;				// 결제금액
		$inipay->m_buyerName = iconv("utf-8", "euc-kr", $buyername);			// 구매자 명
		$inipay->m_buyerTel = $buyertel;			// 구매자 연락처(휴대폰 번호 또는 유선전화번호)
		$inipay->m_buyerEmail = $buyeremail;			// 구매자 이메일 주소
		$inipay->m_payMethod = $paymethod;			// 지불방법 (절대 수정 불가)
		$inipay->m_encrypted = $encrypted;			// 암호문
		$inipay->m_sessionKey = $sessionkey;			// 암호문
		$inipay->m_url = UtilsObj::correctPath($gSession['webbrandweburl']); 	// 실제 서비스되는 상점 SITE URL로 변경할것
		$inipay->m_cardcode = $cardcode; 			// 카드코드 리턴
		$inipay->m_ParentEmail = $parentemail; 			// 보호자 이메일 주소(핸드폰 , 전화결제시에 14세 미만의 고객이 결제하면  부모 이메일로 결제 내용통보 의무, 다른결제 수단 사용시에 삭제 가능)

		//Start Payment Request;
		$inipay->startAction();

		$inipay->m_resultCode = iconv("euc-kr", "utf-8", $inipay->m_resultCode);
		$senderName = iconv("euc-kr", "utf-8", $inipay->m_nminput);
		$accountHolderName = iconv("euc-kr", "utf-8", $inipay->m_nmvacct);

		if ($inipay->m_resultCode == '00')
		{
			$responseCode = $inipay->m_resultCode;
			$authorised = true;
			$authorisedStatus = 1;

			if($inipay->m_payMethod == "Card")
			{
				$transactionRef = $inipay->m_authCode;
			}
			elseif ($inipay->m_payMethod == "DirectBank")
			{
				$transactionRef = $inipay->m_rcash_rslt;
			}
			else if($inipay->m_payMethod == "VBank")
			{
              	$responseDescription = self::getBankName($inipay->m_vcdbank);
				$responseCode = $inipay->m_vacct;
				$expiryDate = date('Y년 n월 d일', strtotime($inipay->m_dtinput));

                //Build up notice message to display to the user on the confirmation page. This is only displayed when payment method VBank has been selected.
                $cciCompletionMessage = '</br>';
                $cciCompletionMessage .= $smarty->get_config_vars('str_OrderINILITE_VBANK_AdditionalInfo') . '</br></br>';
				$cciCompletionMessage .= $smarty->get_config_vars('str_OrderINILITE_VBANK_AccountNumber') . ' ' . $responseCode . '</br></br>';
				$cciCompletionMessage .= $smarty->get_config_vars('str_OrderINILITE_VBANK_BankName') . ' ' . $responseDescription . '</br></br>';
				$cciCompletionMessage .= $smarty->get_config_vars('str_OrderINILITE_VBANK_AccountHolderName') . ' ' . $accountHolderName . '</br></br>';
				$cciCompletionMessage .= $smarty->get_config_vars('str_OrderINILITE_VBANK_SenderName') . ' ' . $senderName . '</br></br>';
				$cciCompletionMessage .= $smarty->get_config_vars('str_OrderINILITE_VBANK_WireTransferDate') . ' ' . $expiryDate . '</br></br>';
				$cciCompletionMessage .= $smarty->get_config_vars('str_OrderINILITE_VBANK_TransactionReference') . ' ' . $inipay->m_oid;
				$transactionRef = $inipay->m_oid;
			}
          	else if($inipay->m_payMethod == "HPP")
			{
			   $transactionRef = $inipay->m_nohpp;
          	}
		}
		else
		{
			$responseCode = $inipay->m_resultCode;
			$responseDescription = $inipay->m_resultMsg;
			$authorised = false;
			$authorisedStatus = 0;
		}

        $smarty = SmartyObj::newSmarty('Order', '', '');

       	$INILiteConfig = PaymentIntegrationObj::readCCIConfigFile('../config/INILite.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

		$logFilePath = $INILiteConfig['LOGFILEPATH'];
        $logOutput = $INILiteConfig['LOGOUTPUT'];

       	$amount = $gSession['order']['ordertotaltopay'];
		$formatted_payment_date = DatabaseObj::getServerTime();

        $serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime =  date("H:i:s");

       	PaymentIntegrationObj::logPaymentGatewayData($INILiteConfig, $serverTimestamp);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $transactionRef;
        $resultArray['formattedtransactionid'] = $transactionRef;
        $resultArray['responsecode'] = $responseCode;
        $resultArray['responsedescription'] = $responseDescription;
        $resultArray['authorisationid'] = $transactionRef;  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $transactionRef;
        $resultArray['bankresponsecode'] = '';
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentdate'] = $formatted_payment_date;
        $resultArray['paymentmeans'] = $inipay->m_payMethod;
        $resultArray['paymenttime'] = '';
		$resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $formatted_payment_date;
        $resultArray['addressstatus'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = '';
        $resultArray['payerstatus'] = $senderName;
        $resultArray['payeremail'] = '';
        $resultArray['business'] = $accountHolderName;
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = '';
        $resultArray['settleamount'] = '';
        $resultArray['currencycode'] = $gSession['order']['currencycode'];
        $resultArray['webbrandcode'] = $gSession['webbrandcode'];

        $resultArray['charityflag'] = '';
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['update'] = $update;
        $resultArray['orderid'] = $orderid;
        $resultArray['parentlogid'] = 0;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
    	$resultArray['showerror'] = $showError;
    	$resultArray['ccicompletionmessage'] = $cciCompletionMessage;

        return $resultArray;
    }

    static function getBankName($pBankCode)
    {
		$smarty = SmartyObj::newSmarty('CreditCardPayment');

		$bankCodeArray = array('03','04','05','06','07','11','12','20','21','23','32','71','81','88');

    	$bankNameArray = array($smarty->get_config_vars('str_OrderINILITE_Bank_01'),
    		$smarty->get_config_vars('str_OrderINILITE_Bank_02'),
    		$smarty->get_config_vars('str_OrderINILITE_Bank_03'),
    		$smarty->get_config_vars('str_OrderINILITE_Bank_04'),
    		$smarty->get_config_vars('str_OrderINILITE_Bank_05'),
    		$smarty->get_config_vars('str_OrderINILITE_Bank_06'),
    		$smarty->get_config_vars('str_OrderINILITE_Bank_07'),
    		$smarty->get_config_vars('str_OrderINILITE_Bank_08'),
    		$smarty->get_config_vars('str_OrderINILITE_Bank_09'),
    		$smarty->get_config_vars('str_OrderINILITE_Bank_10'),
    		$smarty->get_config_vars('str_OrderINILITE_Bank_11'),
    		$smarty->get_config_vars('str_OrderINILITE_Bank_12'),
    		$smarty->get_config_vars('str_OrderINILITE_Bank_13'),
    		$smarty->get_config_vars('str_OrderINILITE_Bank_14'));


    	if ($key = array_search($pBankCode, $bankCodeArray))
    	{
    		$bankName = $bankNameArray[$key];
    	}
    	else
    	{
    		$bankName = 'Unknown';
    	}

    	return $bankName;
    }

	public static function addCSPDetails($pCspBuilder)
	{
		$cspRules = [
			'script-src' => [
				'plugin.inicis.com'
			],
		];

		// Loop over each item in the csp rules.
		foreach ($cspRules as $directive => $directiveItems)
		{
			$directiveCount = count($directiveItems);

			// Loop over each item and add it.
			for ($i = 0; $i < $directiveCount; $i++)
			{
				if ($directiveItems[$i] === 'unsafe-eval')
				{
					$pCspBuilder->getBuilder()->setAllowUnsafeEval($directive, true);
				}
				else if ($directiveItems[$i] === 'self')
				{
					$pCspBuilder->getBuilder()->setSelfAllowed($directive, true);
				}
				else
				{
					$pCspBuilder->getBuilder()->addSource($directive, $directiveItems[$i]);
				}
			}
		}
	}
}

?>