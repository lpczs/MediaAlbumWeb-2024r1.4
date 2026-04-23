<?php

/**
 * Default payment gateway interface
 * Concrete classes must implement methods listed in this class
 * unless they are defined in an abstract class that they extend.
 * 
 * Any method not needed should be defined and return null 
 * 
 * @author Anthony Dodds <anthony.dodds@taopix.com>
 * @version 1
 * @date 24th March 2017
 * @since
 */
interface PaymentInterface
{
	/**
	 * Payment gateway constructor Session, Get, and, Post Vars are passed by ref
	 * this will modify values in these arrays should it be needed.
	 * 
	 * @param array $pConfig Loaded cci config from PaymentIntegration::readCCIConfigFile
	 * @param reference|array $pSession pass by ref $gSession means we loose calls to global $gSession
	 * @param reference|array $getVars pass by ref for get Variables
	 * @param reference|array $postVars pass by ref for post Variables
	 */
	public function __construct($pConfig, &$pSession, &$pGetVars, &$pPostVars);
	
	/**
	 * Method called when a payment is unsuccessful or not completed by navigating
	 * back to the checkout
	 * 
	 * @return array Array used by view, consists of the following definition 
	 * [ 
	 * result => string,
	 * ref => string,
	 * transactionid => string,
	 * authorised => boolean,
	 * showerror => boolean
	 * ]
	 * In some gateways the additional keys are added
	 * authorisedstatus, data[1-4], errorform, script, scripturl, amount,
	 * formattedamount, charityflag, responsecode, responsedescription
	 */
	public function cancel();
	
	/**
	 * Method used to supply a configured but empty array for the cciResult
	 * which is used for logging information from the payment gateway
	 * 
	 * @return array
	 */
	public function cciEmptyResultArray();
	
	/**
	 * Method called to configure the payment gateway
	 * 
	 * @return array Array used to configure view, consists of the following definition
	 * [
	 * active => boolean,
	 * form => string,
	 * scripturl => string,
	 * script => string,
	 * action => string,
	 * gateways => string|array
	 * ]
	 */
	public function configure();

	/**
	 * Method called when a successful payment notification is provided from the
	 * gateway to the Taopix cart
	 * 
	 * @param string $pCallbackType Call back type this is normally automatic or manual
	 * @return array Array with structure that at minimum matches the defined structure from TaopixAbstractGateway see structure returned from ../TaopixAbstractGateway::cciEmptyResultArray
	 */
	public function confirm($pCallbackType);
	
	/**
	 * Method called to generate any security hash mechanism used by the gateway
	 * 
	 * @param string $pString the string to be hashed
	 * @return string The hashed string
	 */
	public function generateHash($pString);
	
	/**
	 * Returns a bool which determines if we update the ccilog
	 * @return bool
	 */
	public function getCCIUpdate();
	
	/**
	 * Returns a bool which determines if we load the session after gateway processing
	 * @return bool
	 */
	public function getLoadSession();
	
	/**
	 * Returns a bool which determins if we update or not after gateway processing
	 * @return bool
	 */
	public function getUpdateStatus();
	
	/**
	 * System to generate the appropriate pre hashed string as request/response hashes use different keys
	 * 
	 * @param array $pParams Array containing the parameters used to generate the hash, these could be get,post, or session values
	 * @param string $pType some gateways hash will be based on different endpoints use this to specify the hash we are generating so we can build the correct hash
	 * @return string pre hashed string
	 */
	public function hashString($pParams, $pType);
	
	/**
	 * Method used to render the correct smarty template
	 * 
	 * @return array|string Returns an array of smarty items or a smarty item
	 */
	public function initialize();
	
	/**
	 * Method used to determine if the hash supplied is correct
	 * 
	 * @param string $pSuppliedHash Hash supplied to verify
	 * @param array $pParams Parameters passed to generateHash
	 * @param string $pType Hash we are verifying
	 * @return bool
	 */
	public function verifyHash($pSuppliedHash, $pParams, $pType);
	
	/**
	 * Method used to process a payment token
	 * 
	 * @param string $pPaymentToken supplied from gateway in the browser to generate a charge on a card.
	 * @return array Returns an array of data used to continue with the payment process.
	 * [
	 * error => string,
	 * errormessage => string,
	 * data => string|array
	 * ]
	 */
	public function processPaymentToken($pPaymentToken);

}
?>