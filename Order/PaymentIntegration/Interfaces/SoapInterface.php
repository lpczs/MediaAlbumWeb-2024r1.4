<?php

/**
 * Description of SoapInterface
 *
 * @author anthonydodds
 */
interface SoapInterface
{
	/**
	 * Soap Client constructor
	 * @param string|null $pUrl soap url or null is not using wsdl
	 */
	public function __construct($pUrl);
	
	/**
	 * Make a soap call
	 * @param string $pEndPoint end point to call
	 * @param array $pParams array of parameters to pass to the client
	 * @return mixed return from soap call
	 */
	public function soapSend($pEndPoint, $pParams);
	
	/**
	 * Return the soap client
	 */
	public function getClient();
	
	/**
	 * Set the client property
	 * @param object $pClient SoapClient object
	 */
	public function setClient($pClient);
	
}
