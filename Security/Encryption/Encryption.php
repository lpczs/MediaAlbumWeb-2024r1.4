<?php

namespace Security\Encryption;

interface Encryption
{
    /**
    * Return the size of the IV for the cipher and mode
    *
    * @return {number}
    */
	public function getIVSize();

    /**
    * Create and IV base on the size passed in
    *
    * @param {string} $pSize The size of the iv.
    * @return {string}
    */
	public function createIV($pSize);

    /**
    * Encrypt data with a key and IV
    *
    * @param {string} $pKey The encryption key.
    * @param {string} $pData Data you wish to encrypt.
    * @param {string} $pIV Initialization string.
    * @return {string}
    */
	public function encrypt($pKey, $pData, $pIV);

    /**
    * Decrypt data with a key and IV
    *
    * @param {string} $pKey The encryption key.
    * @param {string} $pData Data you wish to decrypt.
    * @param {string} $pIV Initialization string.
    * @return {string}
    */
	public function decrypt($pKey, $pData, $pIV);
}