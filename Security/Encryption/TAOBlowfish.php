<?php

namespace Security\Encryption;

class TAOBlowfish
{
    /**
     * Return blowfish encrypted string
     *
     * @param {string} $pData Data you wish to encrypt.
     * @param {string} $pKey The encryption key.
     * @param {string} $pIV Initialization string.
     * @param {string} $pFormat The format for the output. Empty will be text. hex will convert the data to hex
     * @return {string}
     */
    static function blowfishEncrypt($pData, $pKey, &$pIV, $pFormat = "")
    {
        $mcrypt = MCryptFactory::build(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC);

        if (empty($pIV))
        {
            $ivSize = $mcrypt->getIVSize();

            $pIV = $mcrypt->createIV($ivSize);
        }

        $return = $mcrypt->encrypt($pKey, $pData, $pIV);

        if (!empty($pFormat))
        {
            switch ($pFormat)
            {
                case "hex":
                {
                    $return = bin2hex($return);
                    break;
                }
                case "base64":
                {
                    $return = base64_encode($return);
                    break;
                }
            }
        }

        return $return;
    }

    /**
     * Return blowfish a decrypt string
     *
     * @param {string} $pData Data you wish to decrypt.
     * @param {string} $pKey The encryption key.
     * @param {string} $pIV Initialization string.
     * @return {string}
     */
    static function blowfishDecrypt($pData, $pKey, $pIV)
    {
        $mcrypt = MCryptFactory::build(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC);
        return $mcrypt->decrypt($pKey, $pData, $pIV);
    }

}