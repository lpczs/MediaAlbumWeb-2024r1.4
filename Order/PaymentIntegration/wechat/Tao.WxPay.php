<?php

/**
 * We have had to subclass WeChat's SDk because be default you are unable
 * to specify a App key 
 */

class TaoNativePay extends NativePay
{
	/**
	 * This function is a modified version of WeChats default GetPayUrl function
	 * this function returns either the URL for small screen or the qr code
	 * needed to generate the qr code
	 */
	
	public function GetPayUrl($input)
	{
		if($input->GetTrade_type() == "NATIVE")
		{
			try{
				$config = new TaoWxPayConfig();

				$config->SetKey($input->GetKey());
				
				$result = WxPayApi::unifiedOrder($config, $input);

				return $result;
			} catch(Exception $e) {
				error_log(json_encode($e));
			}
		}
		return false;
	}
}

/**
 * We have had to create our own config class as by default there is not way
 * to dynamicaly set an api key the only way is to hard code it in the config.php file
 * by introducing these class methods we can get and set the key
 */

class TaoWxPayConfig extends WxPayConfig
{
	private $apiId = "";

	public function GetKey()
	{
		return $this->apiId;
	}

	public function SetKey($apiId)
	{
		$this->apiId = $apiId ;
	}
}

?>