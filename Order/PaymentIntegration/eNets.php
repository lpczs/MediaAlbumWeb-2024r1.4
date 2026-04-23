<?php
/**
 * eNets payment gateway integration 
 *
 * @author James Moore <james.moore@taopix.com>
 * @version 1
 * @since 2017r3
 */

require_once 'TaopixAbstractGateway.php';
require_once 'eNets/enets2.php';

class eNets extends TaopixAbstractGateway
{
	/**
	 * Configure the payment gateway
	 *
	 * Carry out any required checks of the gateway configuration.
	 * This may include testing:
	 *  - The currency of the order is accepted by the gateway.
	 *  - That the correct security protocols are in place.
	 *
	 */
	public function configure()
	{
		$resultArray = [
			'active' => true,
			'form' => '',
			'scripturl' => '',
			'script' => '',
			'action' => '',
			'gateways' => []
		];

		AuthenticateObj::clearSessionCCICookie();

		/**
		 * ISO number from the order.
		 * 'currencycode' can be substituted with 'currencyisonumber' if the gateway uses the ISO number in the list of currencies
		 */
		$currency = $this->session['order']['currencycode'];

		/**
		 * Set the gateway as active if the order currency is in the list of accepted currencies
		 */
		if (strpos($this->config['CURRENCIES'], $currency) === false)
		{
			$currencyAccepted = false;
		}
		else
		{
			$currencyAccepted = true;
		}

		/**
		 * set the payment gateway as active based on previous tests
		 */
        $resultArray['active'] = $currencyAccepted;

		return $resultArray;
	}

	/**
	 * Initialize
	 *
	 * Build the data to be sent to the payment gateway
	 */
	public function initialize()
	{
		$resultArray = [];
		$enets = new enets2();
		$smarty = SmartyObj::newSmarty('Order', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

		$automaticURLParams = UtilsObj::correctPath($this->session['webbrandwebroot']) . '/PaymentIntegration/eNets/eNetsAuto.php';
		$manualURLParams = UtilsObj::correctPath($this->session['webbrandwebroot']) . '/PaymentIntegration/eNets/eNetsMan.php';
		$cancelReturnPath = UtilsObj::correctPath($this->session['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];

		$environment = $this->config['TESTMODE'] === '0' ? 'LIVE' : 'TEST';

		// first check if we have any ccidata. this is set when the call is made the first time.
		// if the data is set then the user must have hit the back button on their browser
		if ($this->session['order']['ccidata'] == '')
		{
			$enets->setUmid($this->config['MERCHANTID']);
			$enets->setSecretKey($this->config['SECRETKEY']);
			$enets->setKeyId($this->config['KEYID']);
			$enets->setCurrency($this->session['order']['currencycode']);
			$enets->setAmount($this->session['order']['ordertotaltopay']);
			$enets->setMerchantReference($this->session['ref'] . '_' . time());

			$enets->setReturnUrl($manualURLParams);
			$enets->setReturnUrlParam($this->session['ref']);
			$enets->setNotifyUrl($automaticURLParams);
			$enets->setNotifyUrlParam($this->session['ref']);
			$enets->setSubmissionMode('B');
			$enets->setPaymentType('SALE');
			$enets->setPaymentMode('CC');
			$enets->setClientType('H');
			$enets->setEnvironment($environment);

			$enets2Output = $enets->getTransactionRequest();
			$txnReqJson = $enets->getPayload($enets2Output);
			$mac = $enets->getHmac($txnReqJson);

			$keyId = $this->config['KEYID'];

			$parameters = array(
				'payload' => $txnReqJson,
				'apiKey' => $keyId,
				'hmac' => $mac
			);

			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('payment_url', $this->config['SERVER']);

			$smarty->assign('parameter', $parameters);
			$smarty->assign('method', 'post');

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $this->session['ref']);
			$smarty->assign('ccicookievalue', $this->session['order']['ccicookie']);

			// set the ccidata to remember we have started
			$this->session['order']['ccidata'] = 'start';
			DatabaseObj::updateSession();
		}
		else
		{
			// automatic cancel action from back button press
			AuthenticateObj::clearSessionCCICookie();

			$smarty->assign('server', $cancelReturnPath);
		}

		// mobile browser check then return appropriate content
		if ($this->session['ismobile'] === true)
		{
			$resultArray = [
				'template' => $smarty->fetchLocale('order/PaymentIntegration/eNets_small.tpl'),
				'javascript' => $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest.tpl')
			];
		   return $resultArray;
		}
		else
		{
			$smarty->displayLocale('order/PaymentIntegration/eNets.tpl');
		}
	}

	/**
	 *
	 * {@inheritDoc}
	 */
	public function confirm($pCallbackType)
	{
		global $gSession;

		$enets2 = new Enets2();
		$enets2->setSecretKey($this->config['SECRETKEY']);
		$errorMessage = '';
		$errorCode = 0;

		// build default array to return
		$resultArray = $this->cciEmptyResultArray();
		$resultArray['showerror'] = false;

		$cciRef = $this->session['ref'];
		$resultArray['ref'] = $cciRef;

		if ($cciRef != 0)
		{
			$cciEntry = PaymentIntegrationObj::getCciLogEntry($cciRef);

			if ($cciEntry === [])
			{
				// empty first callback
				$resultArray['webbrandcode'] = $this->session['webbrandcode'];
				$resultArray['currencycode'] = $this->session['order']['currencycode'];
				$resultArray['amount'] = $this->session['order']['ordertotaltopay'];
				$resultArray['parentlogid'] = -1;
				$resultArray['orderid'] = -1;
				$resultArray['update'] = false;
				$this->updateStatus = false;
			}
			else
			{
				// additional callback
				$resultArray['webbrandcode'] = $cciEntry['webbrandcode'];
				$resultArray['currencycode'] = $cciEntry['currencycode'];
				$resultArray['amount'] = $cciEntry['formattedamount'];
				$resultArray['parentlogid'] = $cciEntry['id'];
				$resultArray['orderid'] = $cciEntry['orderid'];
				$resultArray['update'] = true;
				$this->updateStatus = true;
			}

			$validChecksum = true;

			try
			{
				if ($pCallbackType == 'automatic')
				{
					$eNetsTxnRes = $enets2->getBackEndResponse();
				}
				else
				{
					$eNetsTxnRes = $enets2->getFrontEndResponse();
				}
			}
			catch (Exception $e)
			{
				$validChecksum = false;
				$errorCode = $e->getCode();
				$errorMessage = $e->getMessage();
			}

			if ($validChecksum)
			{
				// check the response code returned by the gateway for a success
				$responseStatus = $eNetsTxnRes['netsTxnStatus'];

				if ($responseStatus == 0)
				{
					$resultArray['authorised'] = true;
					$resultArray['authorisedstatus'] = 1;
					$resultArray['paymentreceived'] = 1;
					$resultArray['receiverid'] = UtilsObj::getArrayParam($eNetsTxnRes, 'netsMid', 0);
					$resultArray['responsecode'] = 0;
					$resultArray['transactionid'] = UtilsObj::getArrayParam($eNetsTxnRes,'netsTxnRef', 0);
					$resultArray['paymentdate'] = UtilsObj::getArrayParam($eNetsTxnRes,'netsTxnDtm', date('Y-m-d'));

					// fill array to be written to the ccilog
					$resultArray['currencycode'] = $eNetsTxnRes['currencyCode'];

					// bank auth id is conditional so only attempt to assign it if it exists
					if (isset($eNetsTxnRes['bankAuthId']))
					{
						$resultArray['authorisationid'] = $eNetsTxnRes['bankAuthId'];
					}
					$resultArray['amount'] = UtilsObj::getArrayParam($eNetsTxnRes, 'netsAmountDeducted', '0.00');
					$resultArray['formattedamount'] = number_format(UtilsObj::getArrayParam($eNetsTxnRes, 'netsAmountDeducted', '0.00') / 100, 2);
					$resultArray['responsedescription'] = UtilsObj::getArrayParam($eNetsTxnRes, 'netsTxnMsg', '');
					$resultArray['bankresponsecode'] = UtilsObj::getArrayParam($eNetsTxnRes,'stageRespCode', 0);
					$resultArray['paymentmeans'] = UtilsObj::getArrayParam($eNetsTxnRes,'paymentMode', 'CC');
				}
				else
				{
					$resultArray['showerror'] = true;

					// fail transaction
					$errorCode = $eNetsTxnRes['stageRespCode'];
					$errorMessage = SmartyObj::getParamValue('', 'str_Error', $gSession['browserlanguagecode']) . ': ' . $eNetsTxnRes['netsTxnMsg'];
				}
			}
			else
			{
				// fail hash check
				$resultArray['showerror'] = true;
			}
		}
		else
		{
			// No session data returned
			$resultArray['showerror'] = true;

			$errorMessage = SmartyObj::getParamValue('', 'str_ErrorNoSessionRef', $gSession['browserlanguagecode']);
		}

		if ($resultArray['showerror'])
		{
			$resultArray['authorised'] = false;
			$resultArray['authorisedstatus'] = 0;

			// Build the error page.
			$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname'],$gSession['browserlanguagecode']);
			$smarty->assign('data1', SmartyObj::getParamValueLocale('Order', 'str_LabelErrorCode', $gSession['browserlanguagecode']) . ': '. $errorCode);
			$smarty->assign('data2', SmartyObj::getParamValueLocale('Order', 'str_LabelErrorMessage', $gSession['browserlanguagecode']) . ': ' . $errorMessage);
			$smarty->assign('data3', SmartyObj::getParamValueLocale('Order', 'str_LabelTransactionID', $gSession['browserlanguagecode']) . ': ' . $cciRef);
			$smarty->assign('data4', '');
			$smarty->assign('homeurl', $gSession['webbrandwebroot']);

			if ($gSession['ismobile'] == true)
			{
				$returnHTML = $smarty->fetchLocale('order/PaymentIntegration/externalerror_small.tpl', $gSession['browserlanguagecode']);
				$cssFile = $smarty->getLocaleCss('csscustomerexternal_small');
			}
			else
			{
				$returnHTML = $smarty->fetchLocale('order/PaymentIntegration/externalerror_large.tpl', $gSession['browserlanguagecode']);
				$cssFile = $smarty->getLocaleCss('csscustomerexternal_large');
			}

			// Add inline styles and replace relative paths (images, css, etc) with absolute paths
			$formattedReturnHTML = str_replace("../", UtilsObj::correctPath($gSession['webbrandweburl']), self::formatReturnHTML($returnHTML, $cssFile));

			// get rid of image tags and convert to unicode code points where necessary
			$gSession['order']['confirmationhtml'] = UtilsObj::cleanHTML($formattedReturnHTML, false, true);
		}

		return $resultArray;
	}


	/**
	 *
	 * {@inheritDoc}
	 */
	public function generateHash($pString)
	{
		return null;
	}

	/**
	 *
	 * {@inheritDoc}
	 * @param object pParams Depending on the signature check pParams will either be an array or an object
	 * @param string pType Defines the type of hash string being generated
	 */
	public function hashString($pParams, $pType)
	{
		return null;
	}

	/**
	 *
	 * {@inheritDoc}
	 */
	public function verifyHash($pSuppliedHash, $pParams, $pType = '')
	{
		return null;
	}

	static function formatReturnHTML($html, $cssFile)
	{
		require_once('../libs/external/EmailContent/css_to_inline_styles.php');

		// Load the css file
		$cssContent = UtilsObj::readTextFile($cssFile);
		$cssContent = str_replace("\r\n", "\n", $cssContent);
		$cssContent = str_replace("\r", "\n", $cssContent);

		// Convert css to inline styles
		$cssToInlineStyles = new CSSToInlineStyles($html, $cssContent);
		$cssToInlineStyles->setUseInlineStylesBlock(false);
		$cssToInlineStyles->setCleanup(false);
		$htmlWithInlineStyles = $cssToInlineStyles->convert(false, false);

		// Restore the <order_number/> tag (convert() causes this to be formatted)
		$htmlWithInlineStyles = str_replace("<order_number></order_number>", '<order_number/>', $htmlWithInlineStyles);

		return $htmlWithInlineStyles;
	}
}
?>