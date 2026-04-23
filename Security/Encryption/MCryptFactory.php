<?php

namespace Security\Encryption;

class MCryptFactory
{
    /**
    * Create a MCrypt object
    *
    * @return {object}
    */
	public static function build($pCipher, $pMode)
	{
		return new MCrypt($pCipher, $pMode);
	}

}