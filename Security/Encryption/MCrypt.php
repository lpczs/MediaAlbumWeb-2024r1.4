<?php

namespace Security\Encryption;

class MCrypt implements Encryption
{
	protected $cipher = MCRYPT_BLOWFISH;
	protected $mode = MCRYPT_MODE_CBC;

	function __construct($pCipher = MCRYPT_BLOWFISH, $pMode = MCRYPT_MODE_CBC)
	{
		$this->cipher = $pCipher;
		$this->mode = $pMode;
	}

	/*{@inheritDoc}*/
	public function getIVSize()
	{
		return mcrypt_get_iv_size($this->cipher, $this->mode);
	}

	/*{@inheritDoc}*/
	public function createIV($pSize)
	{
		return mcrypt_create_iv($pSize, MCRYPT_RAND);
	}

	/*{@inheritDoc}*/
	public function encrypt($pKey, $pData, $pIV)
	{
		return mcrypt_encrypt($this->cipher, $pKey, $pData, $this->mode, $pIV);
	}
	
	/*{@inheritDoc}*/
	public function decrypt($pKey, $pData, $pIV)
	{
		return mcrypt_decrypt($this->cipher, $pKey, $pData, $this->mode, $pIV);
	}
}