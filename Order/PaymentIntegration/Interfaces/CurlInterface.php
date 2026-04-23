<?php

/**
 * Basic interface for curl methods required by payment gateways. Gateways that do not require curl
 * should not implement this interface
 * 
 * @author Anthony Dodds <anthony.dodds@taopix.com>
 * @version 1
 * @date 28th March 2017
 * @since
 */
interface CurlInterface
{
	/**
	 * 
	 * @param string $pSendMethod format of data to be sent to and from the curlHandler
	 * @param array $pParams default params to configure curl with for this provider
	 */
	public function __construct($pSendMethod, $pParams = []);
	
	/**
	 * Close the connection
	 */
	public function connectionClose();
	
	/**
	 * Create and configure the curl interface
	 * 
	 * @param array $pOptions
	 */
	public function connectionInit($pOptions);
	
	/**
	 * Send the curl request
	 * 
	 * @param string $pServer Server name
	 * @param string $pEndpoint Endpoint to use
	 * @param string $pMethod HTTP Method
	 * @param array $pParams Array of curl setopt key => values
	 * @param int $pRetries Number of times to retry this request
	 * @return string curl response
	 */
	public function connectionSend($pServer, $pEndpoint, $pMethod, $pParams, $pRetries);
	
	/**
	 * Returns unformatted data in the data format required
	 * 
	 * @param array $pData data to be formatted
	 * @return mixed formatted data
	 */
	public function formatData($pData);
	
	/**
	 * Returns connection
	 */
	public function getConnection();
	
	/**
	 * 
	 * @param string $pMethod sets the format of data used in the gateway
	 */
	public function setSendMethod($pMethod);
	
}
?>