<?php

require_once __DIR__ . '/../Interfaces/SoapInterface.php';

/**
 * SoapHandler Wrapper class for soap calls, allows mocking of this object
 *
 * @author anthonydodds
 */
class SoapHandler implements SoapInterface
{
	protected $soapClient = null;
	
	/**
	 * {@inheritDoc}
	 */
	public function __construct($pUrl)
	{
		$this->setClient(new SoapClient($pUrl));
	}
	
	/*
	 * {@inheritDoc}
	 */
	public function getClient()
	{
		return $this->soapClient;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setClient($pClient)
	{
		$this->soapClient = $pClient;
	}

	/**
	 * {@inheritDoc}
	 */
	public function soapSend($pEndPoint, $pParams)
	{
		$return = '';
		
		if($this->getClient() !== null)
		{
			$return = $this->getClient()->__soapCall($pEndPoint, $pParams);
		}
		
		return $return;
	}
}
?>