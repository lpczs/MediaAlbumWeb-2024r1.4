<?php
define('__CCROOT__', dirname(__FILE__, 2));

class newOnlineConfig extends ExternalScript
{
    public function run()
    {
        $currentConfig = UtilsObj::readConfigFile(__CCROOT__.'/config/mediaalbumweb.conf');
        $newOnlineConfigFile = __CCROOT__.'/config/.env.local';

        $content = \implode(PHP_EOL, [
            'APP_SECRET='.\bin2hex(\openssl_random_pseudo_bytes(32)),
            'DATABASE_URL="mysql://'.$currentConfig['DBUSER'].':'.\urlencode($currentConfig['DBPASS']).'@'.$currentConfig['DBHOST'].':3306/'.$currentConfig['DBNAME'].'?serverVersion=8"'
        ]);

        if (!\file_put_contents($newOnlineConfigFile, $content)) {
            $this->setResult('Unable to create config file');
            return;
        }

        $this->setResult('');
    }
}
