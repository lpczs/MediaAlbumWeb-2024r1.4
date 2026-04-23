<?php

/**
 * Default payment gateway encryption interface
 * Concrete classes must implement methods listed in this class
 * unless they are defined in an abstract class that they extend.
 * Payment gateways that do not require data encryption should not implement this interface
 * 
 * Any method not needed should be defined and return null 
 * 
 * @author Anthony Dodds <anthony.dodds@taopix.com>
 * @version 1
 * @date 28th March 2017
 * @since
 */
interface EncryptionInterface
{
	/**
	 * Method used to decrypt data using the required method and mode
	 * 
	 * @param type $pEncryptedString The encrypted string to decrypt
	 * @return string
	 */
	public function decryptData($pEncryptedString);
	
	/**
	 * Method used to encrypt data using the required method and mode
	 * 
	 * @param string|array $pData A string or array of the data to be encrypted
	 */
	public function encryptData($pData);
	
	/**
	 * Call to get the block size for the required encryption method and mode
	 * 
	 * @return int Block size
	 */
	public function getBlockSize();
	
	/**
	 * PKCS7 Padding will generate the correct padding for PKCS5 compliance
	 * PKCS5 padding for block sizes of up to 8 byte
	 * PKCS7 padding for block sizes of up to 255 byte
	 * 
	 * Blocksize - (len(string) % Blocksize) | string . rep(chr(pad), pad)
	 * 
	 * @param int $pBlockSize
	 * @param string $pString
	 * @return string Padded string
	 */
	public function pkcs7Padding($pBlockSize, $pString);
	
	/**
	 * Removes PKCS5 & PKCS7 padding from the end of a decrypted string
	 * 
	 * len(string) % blocksize == 0 | pad = ord(substr(string, -1)) | pad <= blocksize | substr(string, 0, len(string) - pad)
	 * 
	 * @param int $pBlockSize
	 * @param string $pString
	 * @return string String with padding removed
	 */
	public function pkcs7Remove($pBlockSize, $pString);
}
?>