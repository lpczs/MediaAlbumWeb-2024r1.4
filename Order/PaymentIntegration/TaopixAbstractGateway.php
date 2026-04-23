<?php

require_once 'Interfaces/PaymentInterface.php';

use Security\ControlCentreCSP;
/**
 * Abstract class for payment gateways payment gateways should extend this class
 * and implement the remaining methods from TaopixPaymentInterface
 *
 * @author Anthony Dodds <anthony.dodds@taopix.com>
 * @version 1
 * @date 27th March 2017
 * @since
 */
abstract class TaopixAbstractGateway implements PaymentInterface
{
	//Config For this gateway
	protected $config;

	//Get Variables
	protected $get;

	//Posted Variables
	protected $post;

	//Global Session Variables
	protected $session;

	//default cciLog to true
	protected $cciLogUpdate = true;

	//Default loadSession to false
	protected $loadSession = false;

	//Default updateStatus to be false
	protected $updateStatus = false;

	//Default statusForInformationOnly to be false
	protected $statusForInformationOnly = false;

	//Default logCCIEntryForSameTransactionID to be false
	protected $logCCIEntryForSameTransactionID = false;

	protected $cspBuilder = null;

	/**
	 *
	 * {@inheritDoc}
	 */
	public function __construct($pConfig, &$pSession, &$pGetVars, &$pPostVars)
	{
		$this->config = $pConfig;
		$this->session = &$pSession;
		$this->get = &$pGetVars;
		$this->post = &$pPostVars;

		// Initialize the CSP Builder.
		$this->initCSPBuilder();
	}

	/**
	 * Create default result array for the configure action.
	 */
	public function getConfigureDefaultResultArray()
	{
		return array(
            'active' => true,
			'form' => '',
			'scripturl' => '',
			'script' => '',
            'action' => '',
			'gateways' => [],
            'requestpaymentparamsremotely' => false);
	}

	/**
	 * Cancel Payment Call
	 *
	 * @return array Default array that is passed back when cancel has been called
	 * The array definition is as follows
	 * [
	 *		result => blank string,
	 *		ref => int session reference,
	 *		transactionid => blank string
	 *		authorised => boolean payment has been authorised,
	 *		showerror => boolean Show errors to end user
	 * ]
	 * Additional keys/values may be added to this when required by a gateway
	 */
	public function cancel()
	{
		$this->cciLogUpdate = false;
		return [
			'result' => '',
			'ref' => $this->session['ref'],
			'transactionid' => '',
			'authorised' => false,
			'showerror' => false,
		];
	}

	/**
	 * Generate empty default array
	 * Additional keys of data[1-4], errorform, paymentreceived, update, resultisarray
	 * resultlist, result, ref, authorised
	 * these can be added as required but do not form part of the default array structure.
	 *
	 * @return array
	 */
	public function cciEmptyResultArray()
	{
		return [
			"orderid" => '',
			"parentlogid" => '',
			"type" => '',
			"amount" => '',
			"charges" => '',
			"authorisedstatus" => '',
			"transactionid" => '',
			"responsecode" => '',
			"responsedescription" => '',
			"authorisationid" => '',
			"bankresponsecode" => '',
			"cardnumber" => '',
			"cvvflag" => '',
			"cvvresponsecode" => '',
			"paymentcertificate" => '',
			"paymentdate" => '',
			"paymenttime" => '',
			"paymentmeans" => '',
			"addressstatus" => '',
			"postcodestatus" => '',
			"payeremail" => '',
			"payerid" => '',
			"payerstatus" => '',
			"business" => '',
			"receiveremail" => '',
			"receiverid" => '',
			"pendingreason" => '',
			"transactiontype" => '',
			"settleamount" => '',
			"threedsecurestatus" => '',
			"cavvresponsecode" => '',
			"charityflag" => '',
			"currencycode" => '',
			"webbrandcode" => '',
			"formattedpaymentdate" => '',
			"formattedtransactionid" => '',
			"formattedauthorisationid" => '',
			"formattedcardnumber" => '',
			"formattedamount" => '',
			"formattedcharges" => '',
			"acknowledgement" => ''
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public function getCCIUpdate()
	{
		return $this->cciLogUpdate;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLoadSession()
	{
		return $this->loadSession;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUpdateStatus()
	{
		return $this->updateStatus;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getStatusForInformationOnly()
	{
		return $this->statusForInformationOnly;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getStatusForLoggingCCIEntryForSameTransactionID()
	{
		return $this->logCCIEntryForSameTransactionID;
	}

	/**
	 * Used for testing
	 * @param reference $get
	 */
	public function setGetVars(&$get)
	{
		$this->get = &$get;
	}

	public function setConfig($config)
	{
		$this->config = $config;
		if(property_exists($this, 'keySuffix'))
		{
			$this->keySuffix = ($this->config['TRANSACTIONMODE'] === 'TEST') ? 'TEST' : '';
		}
	}

	public function setSession(&$session)
	{
		$this->session = &$session;
	}

	public function setPostVars(&$post)
	{
		$this->post = &$post;
	}

	/**
	 * {@inheritDoc}
	 */
	public function processPaymentToken($pPaymentToken)
	{
		return [
			"error" => '',
			"errormessage" => '',
			"redirecturl" => '',
			"data" => array()
		];
	}

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

	/**
	 * Returns CSP information for the gateway.
	 *
	 * @returns array
	 */
	public function getCSPDetails()
	{
		return [];
	}

	/**
	 * Adds specified directives to the csp builder.
	 *
	 * @returns void
	 */
	public function addCSPDetails()
	{
		// Only do this action if the cspBuilder is set.
		if ($this->cspBuilder !== null)
		{
			$cspRules = $this->getCSPDetails();

			// Loop over each item in the csp rules.
			foreach ($cspRules as $directive => $directiveItems)
			{
				$directiveCount = count($directiveItems);

				// Loop over each item and add it.
				for ($i = 0; $i < $directiveCount; $i++)
				{
					if ($directiveItems[$i] === 'unsafe-eval')
					{
						$this->cspBuilder->getBuilder()->setAllowUnsafeEval($directive, true);
					}
					else if ($directiveItems[$i] === 'self')
					{
						$this->cspBuilder->getBuilder()->setSelfAllowed($directive, true);
					}
					else
					{
						$this->cspBuilder->getBuilder()->addSource($directive, $directiveItems[$i]);
					}
				}
			}
		}
	}

	/**
	 * Initialises the CSP builder for the gateway if CSP is active.
	 *
	 * @returns void
	 */
	public function initCSPBuilder(): void
	{
		$cspActive = true;

		$ac_config = UtilsObj::getGlobalValue('ac_config', []);

		if (array_key_exists('CONTENTSECURITYPOLICY', $ac_config))
        {
            if ($ac_config['CONTENTSECURITYPOLICY'] === 'DISABLED')
            {
                $cspActive = false;
            }
        }

		// Only initialise the CSP Builder for the gateway if it is active.
		if ($cspActive)
		{
			$this->cspBuilder = ControlCentreCSP::getInstance($ac_config);
		}
	}
}
?>