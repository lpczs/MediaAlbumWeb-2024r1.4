<?php
/*
TAOPIX™ External Customer Account Scripting API Example
Version 5.4.1 - Tuesday, 9th February 2021
Copyright 2013 - 2021 TAOPIX Limited

This version requires TAOPIX™ Control Centre v2021r1 or later

Release History:
24-02-2013: v1.00: Initial Release
01-11-2013: v2.00: Changed from External Login Scripting to External Customer Account Scripting API
01-11-2013: v2.00: Changed some existing function names to reflect the new purpose of the API
01-11-2013: v2.00: Updated for customer account changes
01-11-2013: v2.00: Added the array parameters 'id' and 'status' to the login function
01-11-2013: v2.00: Added the following array parameters to the updateAccountDetails function:
						'id', 'origgroupcode', 'origbrandcode', 'origlogin', 'origaccountcode', 'newgroupcode', 'newbrandcode', 'newlogin', 'newaccountcode'
						'passwordchanged', 'passwordformat', 'password', status', 'isadmin', 'usedefaultpaymentmethods', 'paymentmethods', 'taxcode',
						'shippingtaxcode', 'uselicensekeyforshippingaddress', 'canmodifyshippingaddress', 'canmodifyshippingcontactdetails',
						'uselicensekeyforbillingaddress', 'canmodifybillingaddress', 'useremaildestination', 'defaultaddresscontrol', 'canmodifypassword',
						'creditlimit', 'accountbalancedifference', 'giftcardbalancedifference', 'sendmarketinginfo', 'isactive'
01-11-2013: v2.00: Added the array parameter 'id' to the 'updatePassword' function
01-11-2013: v2.00: Added the array parameter 'id' to the 'updateAccountPrefs' function
01-11-2013: v2.00: Added the array parameter 'id' to the 'updateAccountBalance' function
01-11-2013: v2.00: Added the array parameter 'id' to the 'updateGiftCardBalance' function
01-11-2013: v2.00: Added the array parameter 'id' to the 'resetPassword' function
01-11-2013: v2.00: Added the 'createAccount' function
01-11-2013: v2.00: Added the 'deleteAccount' function
01-11-2013: v2.00: Added the 'updateActiveStatus' function
15-01-2014: v3.00: Added the 'loginExists' function
08-07-2016: v4.00: Added the 'ssoLogin' function
08-07-2016: v4.00: Added the 'ssoLogout' function
08-07-2016: v4.00: Added the 'ssoGetBrandDefaultGroupCode' function
08-07-2016: v4.00: Added the 'ssoSetHomeURL' function
08-07-2016: v4.00: Added the 'ssoCallback' function
05-09-2018: v5.00: Changed the password reset process. Implemeneted the resetPasswordInit function
05-09-2018: v5.1: Added 'ssotoken' and 'ssoprivatedata' to the loging and createAccount function return parameters
05-09-2018: v5.2: Added usedefaultcurrency and currencycode to the useraccount array as described in the output parameters in the ssoLogin section.
05-09-2018: v5.3: Added method ‘authenticate’ to re-validate user passwords.
05-09-2018: v5.4: Added login field to SSO.
*/


class ExternalCustomerAccountObj
{

	private static $API_URI = "https://www.ubabybaby.com/en/inchoo-api/inchoo/json";
    const API_USER = "apiTPX";
	const API_PWD  = 'ouAdZjaHiQ$Um6XbV^:N';


	/*
	* Processes a request to create an account within an external system
	*
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'languagecode'							- varchar(50)
	*		'isadmin'								- integer
	*		'groupcode'								- varchar(50)
	*		'brandcode'								- varchar(50)
	*		'login'									- varchar(50)
	*		'accountcode'							- varchar(50)
	*		'passwordformat'						- integer
	*		'password'								- varchar(50)
	*		'status'								- integer
	*		'useraccount[]'
	*
	*	useraccount is an array that contains the user account data that could be updated
	*		'companyname'							- varchar(200)
	*		'address1'								- varchar(200)
	*		'address2'								- varchar(200)
	*		'address3'								- varchar(200)
	*		'address4'								- varchar(200)
	*		'city'									- varchar(200)
	*		'county'								- varchar(50)
	*		'state'									- varchar(200)
	*		'regioncode'							- varchar(20)
	*		'region'								- varchar(10)
	*		'postcode'								- varchar(200)
	*		'countrycode'							- varchar(10)
	*		'countryname'							- varchar(50)
	*		'telephonenumber'						- varchar(50)
	*		'emailaddress'							- varchar(50)
	*		'contactfirstname'						- varchar(200)
	*		'contactlastname'						- varchar(200)
	*		'registeredtaxnumbertype'				- integer
	*		'registeredtaxnumber'					- varchar(50)
	*		'usedefaultpaymentmethods'				- integer
	*		'paymentmethods'						- varchar(100)
	*		'taxcode'								- varchar(20)
	*		'shippingtaxcode'						- varchar(20)
	*		'uselicensekeyforshippingaddress'		- integer
	*		'canmodifyshippingaddress'				- integer
	*		'canmodifyshippingcontactdetails'		- integer
	*		'uselicensekeyforbillingaddress'		- integer
	*		'canmodifybillingaddress'				- integer
	*		'useremaildestination'					- integer
	*		'defaultaddresscontrol'					- integer
	*		'canmodifypassword'						- integer
	*		'creditlimit'							- decimal
	*		'accountbalance'						- decimal
	*		'giftcardbalance'						- decimal
	*		'sendmarketinginfo'						- integer
	*		'isactive'								- integer
	*
	* @return array
	*   the array will contain:
	*	   	string 'result' - empty if the account was successfully created or not empty to prevent the record from being created in taopix and display an error
	*		array 'accountcode' - the modified user account code
	*
	* @author Kevin Gale
	* @since Version 4.1.0
	*/
	
	static function createAccount($pParamArray)
	{
		$resultArray = Array();
		$result = '';
		$userArray = Array();
		$apiSession = '';

		
		$apiResult = self::apiLogin(self::API_USER,self::API_PWD);
		$apiSession = $apiResult['result'];
		$apiReqId =  $apiResult['id'];
	
        
		
		$password = $pParamArray['password'];
		$username = $pParamArray['login'];
		$email = $pParamArray['useraccount']['emailaddress'];
		$credit_amount = $pParamArray['useraccount']['creditlimit'];
		$firstname = $pParamArray['useraccount']['contactfirstname'];
		$lastname = $pParamArray['useraccount']['contactlastname'];
         
		$useraccount = Array(
		  
		  "email"     => $email,
		  "firstname" => $firstname,
		  "lastname"  => $lastname,
		  "password"  => $password,
		  "website_id" => 1,
		  "store_id"  => 1,
		  "group_id"  => 1,
		  "username"  => $username,
		  "credit_amount" => $credit_amount,
          "giftcardbalance" => $pParamArray['useraccount']['giftcardbalance'],
          "accountbalance"=>$pParamArray['useraccount']['accountbalance'],  
		  "canmodifypassword" => $pParamArray['useraccount']['canmodifypassword'],
		  "useremaildestination" => $pParamArray['useraccount']['useremaildestination'],
		  "canmodifybilladdr" => $pParamArray['useraccount']['canmodifybillingaddress'],
		  "uselicensekeybilladdr" => $pParamArray['useraccount']['uselicensekeyforbillingaddress'],
		  "canmodifyshipcontact" => $pParamArray['useraccount']['canmodifyshippingcontactdetails'],
		  "canmodifyshippingaddr" => $pParamArray['useraccount']['canmodifyshippingaddress'],
		  "uselicensekeyshipaddr" => $pParamArray['useraccount']['uselicensekeyforshippingaddress'],
		  "defaultaddresscontrol " => $pParamArray['useraccount']['defaultaddresscontrol'],
		  "shippingtaxcode" =>  $pParamArray['useraccount']['shippingtaxcode'],
		  "taxcode" =>  $pParamArray['useraccount']['taxcode'],
		  "paymentmethods" =>  $pParamArray['useraccount']['paymentmethods'],
		  "usedefaultpaymethods" =>  $pParamArray['useraccount']['usedefaultpaymentmethods'],
 		  "registeredtaxnumber" =>  $pParamArray['useraccount']['registeredtaxnumber'],
		  "registeredtaxtype" =>  $pParamArray['useraccount']['registeredtaxnumbertype'],
          "groupcode" =>  $pParamArray['groupcode'],
		  "accountcode" =>  $pParamArray['accountcode'],
		  "brandcode" =>  $pParamArray['brandcode']
		  

		  
		
		);
		

        $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer.create",['.json_encode($useraccount).']],"id":"'.$apiReqId.'"}';

		
		
		
        $data = self::zCurl(self::$API_URI, $post);
        $apiResult=json_decode($data, true);
        $customerId = $apiResult['result'];		
		$apiReqId = $apiResult['id'];	
		if ($apiResult['error']['code'] == '-32000')  {
		
	     //$result = 'str_ErrorDuplicateUserName';	
		 $result = 'str_ErrorDuplicateEmail';
		}
		
		else {
		   /*$region_id 
		   $region */
		   
		   $region = $pParamArray['useraccount']['state'];
		   $street[] =  $pParamArray['useraccount']['address1'];
		   $street[] =  $pParamArray['useraccount']['address2'];
		   $street[] =  $pParamArray['useraccount']['address3'];
		   $street[] =  $pParamArray['useraccount']['address4'];
		   $street = json_encode(array_filter($street));
		   
		   
		   $country_id = $pParamArray['useraccount']['countrycode']; 
		
		  $city = $pParamArray['useraccount']['city'];
		  $company  = $pParamArray['useraccount']['companyname'];
		  $postcode = $pParamArray['useraccount']['postcode'];
		  $county = $pParamArray['useraccount']['county'];
		  

		  $telephone = $pParamArray['useraccount']['telephonenumber'];
		  $is_default_billing = $is_default_shipping = 1;
              				  
		  $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer_address.create",["'.$customerId.'",{"city":"'.$city.'","company":"'.$company.'","country_id":"'.$country_id.'","firstname":"'.$firstname.'","lastname":"'.$lastname.'","postcode":"'.$postcode.'","region":"'.$region.'","street":'.$street.',"telephone":"'.$telephone.'","is_default_billing":"'.$is_default_billing.'","is_default_shipping":"'.$is_default_shipping.'"}]],"id":"'.$apiReqId.'"}';
          $data = self::zCurl(self::$API_URI, $post);
          $apiResult=json_decode($data, true);		
             		  



		 

		
		}
		
		
		

		
		
		
		
		
		
		/*
		here is where we perform whatever action is required to create the account within the external system
		*/
		
		$userAccountArray = $pParamArray['useraccount'];
		$accountCode = $pParamArray['accountcode'];
		
		
		/*
		in this example we just return an empty string to allow the action to continue
		and also return the same account code that was passed across
		*/
		$resultArray['result'] = $result;
		$resultArray['accountcode'] = $accountCode;
		$resultArray['useraccount'] ['isactive'] = 1; 
		
		return $resultArray;		
	}


	/*
	* Processes a request to update the details of an external account
	*
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'languagecode'							- varchar(50)
	*		'isadmin'								- integer
	*		'id'									- integer
	*		'origgroupcode'							- varchar(50)
	*		'origbrandcode'							- varchar(50)
	*		'origlogin'								- varchar(50)
	*		'origaccountcode'						- varchar(50)
	*		'newgroupcode'							- varchar(50)
	*		'newbrandcode'							- varchar(50)
	*		'newlogin'								- varchar(50)
	*		'newaccountcode'						- varchar(50)
	*		'passwordchanged'						- integer
	*		'passwordformat'						- integer
	*		'password'								- varchar(50)
	*		'status'								- integer
	*		'useraccount[]'
	*
	*	useraccount is an array that contains the user account data that could be updated
	*		'companyname'							- varchar(200)
	*		'address1'								- varchar(200)
	*		'address2'								- varchar(200)
	*		'address3'								- varchar(200)
	*		'address4'								- varchar(200)
	*		'city'									- varchar(200)
	*		'county'								- varchar(50)
	*		'state'									- varchar(200)
	*		'regioncode'							- varchar(20)
	*		'region'								- varchar(10)
	*		'postcode'								- varchar(200)
	*		'countrycode'							- varchar(10)
	*		'countryname'							- varchar(50)
	*		'telephonenumber'						- varchar(50)
	*		'emailaddress'							- varchar(50)
	*		'contactfirstname'						- varchar(200)
	*		'contactlastname'						- varchar(200)
	*		'registeredtaxnumbertype'				- integer
	*		'registeredtaxnumber'					- varchar(50)
	*		'usedefaultpaymentmethods'				- integer
	*		'paymentmethods'						- varchar(100)
	*		'taxcode'								- varchar(20)
	*		'shippingtaxcode'						- varchar(20)
	*		'uselicensekeyforshippingaddress'		- integer
	*		'canmodifyshippingaddress'				- integer
	*		'canmodifyshippingcontactdetails'		- integer
	*		'uselicensekeyforbillingaddress'		- integer
	*		'canmodifybillingaddress'				- integer
	*		'useremaildestination'					- integer
	*		'defaultaddresscontrol'					- integer
	*		'canmodifypassword'						- integer
	*		'creditlimit'							- decimal
	*		'accountbalancedifference'				- decimal
	*		'giftcardbalancedifference'				- decimal
	*		'sendmarketinginfo'						- integer
	*		'isactive'								- integer
	*
	* @return string - empty for a successful update or not empty to display an error
	*
	* @author Kevin Gale
	* @since Version 3.5.0
	*/
	static function updateAccountDetails($pParamArray)
	{
	

		$result = '';
		$accountGroupCode = $pParamArray['accountgroupcode'];
		if ($accountGroupCode !== 'STSTEPHENS') {
		$apiResult = self::apiLogin(self::API_USER,self::API_PWD);
		$apiSession = $apiResult['result'];
		$apiReqId =  $apiResult['id'];     
		$postInfo = array(
          "username"=>$pParamArray['origlogin']
		
		);
		
		$post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer.list",['.json_encode($postInfo).']],"id":"'.$apiReqId.'"}';
		

		
		
        $data = self::zCurl(self::$API_URI, $post);
        $apiResult=json_decode($data, true);
		$email = $apiResult['result'][0]['email'];
		$accountbalance =  $apiResult['result'][0]['accountbalance'];
	    $customerId = $apiResult['result'][0]['customer_id'];
	
		
		if ($customerId > 0) {
		
		 if ($pParamArray["passwordchanged"]) {
          $newSalt =  base64_encode(md5(uniqid()));
          $newPassword_hased = md5($newSalt.$pParamArray["password"]).':'.$newSalt;  
		  
		  
             
         }
		
		
		 $postInfo = array(
		 
		  "email"     => $pParamArray['useraccount']['emailaddress'],
		  "firstname" =>  $pParamArray['useraccount']['contactfirstname'],
		  "lastname"  => $pParamArray['useraccount']['contactlastname'],
		  "website_id" => 1,
		  "store_id"  => 1,
		  "group_id"  => 1,
		  "username"  => $pParamArray["newlogin"],
		  "credit_amount" =>  $pParamArray['useraccount']['creditlimit'],
          "giftcardbalance" => $pParamArray['useraccount']['giftcardbalancedifference'],
          "accountbalance"=>$pParamArray['useraccount']['accountbalancedifference']+$accountbalance,  
		  "canmodifypassword" => $pParamArray['useraccount']['canmodifypassword'],
		  "useremaildestination" => $pParamArray['useraccount']['useremaildestination'],
		  "canmodifybilladdr" => $pParamArray['useraccount']['canmodifybillingaddress'],
		  "uselicensekeybilladdr" => $pParamArray['useraccount']['uselicensekeyforbillingaddress'],
		  "canmodifyshipcontact" => $pParamArray['useraccount']['canmodifyshippingcontactdetails'],
		  "canmodifyshippingaddr" => $pParamArray['useraccount']['canmodifyshippingaddress'],
		  "uselicensekeyshipaddr" => $pParamArray['useraccount']['uselicensekeyforshippingaddress'],
		  "defaultaddresscontrol" => $pParamArray['useraccount']['defaultaddresscontrol'],
		  "shippingtaxcode" =>  $pParamArray['useraccount']['shippingtaxcode'],
		  "taxcode" =>  $pParamArray['useraccount']['taxcode'],
		  "paymentmethods" =>  $pParamArray['useraccount']['paymentmethods'],
		  "usedefaultpaymethods" =>  $pParamArray['useraccount']['usedefaultpaymentmethods'],
 		  "registeredtaxnumber" =>  $pParamArray['useraccount']['registeredtaxnumber'],
		  "registeredtaxtype" =>  $pParamArray['useraccount']['registeredtaxnumbertype'],
          "groupcode" =>  $pParamArray['newgroupcode'],
		  "accountcode" =>  $pParamArray['newaccountcode'],
		  "brandcode" =>  $pParamArray['newbrandcode']						
						
						
						
						
					
					
					);
		if ($newPassword_hased) { $postInfo["password_hash"] = $newPassword_hased;}
			

         $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer.update",["'.$customerId.'",'.json_encode($postInfo).']],"id":"'.$apiReqId.'"}';		
         $data = self::zCurl(self::$API_URI, $post);
         $apiResult=json_decode($data, true);         
         

		 
		 
         if ($apiResult['result']) {
		 
              $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer_address.list",['.$customerId.']],"id":"'.$apiReqId.'"}';
		
              $data = self::zCurl(self::$API_URI, $post);
              $apiResult=json_decode($data, true);
		      $addresses = $apiResult['result'];

	             foreach($addresses as $address) {
		     
			          if ($address['is_default_billing']) {
               
			             $address_id = $address['customer_address_id'];
						 break;
			 
			            }
		            }		 
		 
		 
		 
		 
		 
		  
		   $region = $pParamArray['useraccount']['state'];
		   $street[] =  $pParamArray['useraccount']['address1'];
		   $street[] =  $pParamArray['useraccount']['address2'];
		   $street[] =  $pParamArray['useraccount']['address3'];
		   $street[] =  $pParamArray['useraccount']['address4'];
		   $street = array_filter($street);
		   
		   
		   $country_id = $pParamArray['useraccount']['countrycode']; 
		
		  $city = $pParamArray['useraccount']['city'];
		  $company  = $pParamArray['useraccount']['companyname'];
		  $postcode = $pParamArray['useraccount']['postcode'];

		  $telephone = $pParamArray['useraccount']['telephonenumber'];
		  $is_default_billing = $is_default_shipping = 1;		 
         
          $postInfo = array(
             "city" => $city,
			 "postcode" => (empty($postcode)) ? 'NIL' : $postcode,
			 "country_id" => $country_id,
			 "telephone" => $telephone,
			 "region" => $region,
			 "company" => $company,
             "street" => $street,
			 "firstname" => $pParamArray['useraccount']['contactfirstname'],
			 "lastname" =>  $pParamArray['useraccount']['contactlastname']
                 

          );		  

		 $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","region.list",["'.$country_id.'"]],"id":"'.$apiReqId.'"}';		
   		 $data = self::zCurl(self::$API_URI, $post);
         $apiResult=json_decode($data, true);  
	     $regionData = self::search($apiResult['result'],'name',$region);
		 if (isset($regionData[0]) ) {
		 
		 $postInfo["region_id"]=$regionData[0]["region_id"];
		 
		 }

		  
        // $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer_address.update",["'.$address_id.'",'.json_encode($postInfo).']],"id":"'.$apiReqId.'"}';		
         if (strval($address_id) > 0) {
          $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer_address.update",["'.$address_id.'",'.json_encode($postInfo).']],"id":"'.$apiReqId.'"}';		
         }

         else {
            
           $postInfo["is_default_billing"] =  $postInfo["is_default_billing"] = 1;

           $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer_address.create",["'.$customerId.'",'.json_encode($postInfo).']],"id":"'.$apiReqId.'"}';


         }
 		 $data = self::zCurl(self::$API_URI, $post);
         $apiResult=json_decode($data, true);  
		   

		 
	    	$result = '';
			
			
			
			}
		 else 
		   $result = 'str_ErrorAccountTaskNotAllowed';
		   
		   
		}
		
		else {
		
		$result = 'str_ErrorAccountTaskNotAllowed';
		}		
		
		
		
		
	}
		

	
		
		return $result;		
	}


	/*
	* Processes a request to change the active status of an external account
	*
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'languagecode'							- varchar(50)
	*		'groupcode'								- varchar(50)
	*		'brandcode'								- varchar(50)
	*		'id'									- integer
	*		'login'									- varchar(50)
	*		'accountcode'							- varchar(50)
	*		'isactive'								- integer
	*
	* @return string - empty for a successful update or not empty to display an error and rollback the change within taopix
	*
	* @author Kevin Gale
	* @since Version 4.1.0
	*/
	static function updateActiveStatus($pParamArray)
	{

		$apiResult = self::apiLogin(self::API_USER,self::API_PWD);
		$apiSession = $apiResult['result'];
		$apiReqId =  $apiResult['id'];     
		$postInfo = array(
          "username"=>$pParamArray['login']
		
		);
		
		$post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer.list",['.json_encode($postInfo).']],"id":"'.$apiReqId.'"}';
		
		
        $data = self::zCurl(self::$API_URI, $post);
        $apiResult=json_decode($data, true);
		$customerId = $apiResult['result'][0]['customer_id'];
       
		
		
		if ($customerId > 0) {
		


		
		
		 $postInfo = array("is_active"=>$pParamArray['isactive']);

         $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer.update",["'.$customerId.'",'.json_encode($postInfo).']],"id":"'.$apiReqId.'"}';		
         $data = self::zCurl(self::$API_URI, $post);
         $apiResult=json_decode($data, true);         


		 
		 
         if ($apiResult['result'])
	    	$result = '';

		
		}
	
	
	
		
		return $result;	
	}


	/*
	* Processes a request to delete an account from the external system
	*
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'languagecode'							- varchar(50)
	*		'groupcode'								- varchar(50)
	*		'brandcode'								- varchar(50)
	*		'id'									- integer
	*		'login'									- varchar(50)
	*		'accountcode'							- varchar(50)
	*
	* @return string - empty for a successful deletion or not empty to display an error and rollback the change within taopix
	*
	* @author Kevin Gale
	* @since Version 4.1.0
	*/
	static function deleteAccount($pParamArray)
	{
	
	
		$apiResult = self::apiLogin(self::API_USER,self::API_PWD);
		$apiSession = $apiResult['result'];
		$apiReqId =  $apiResult['id'];     
		$postInfo = array(
          "username"=>$pParamArray['login']
		
		);
		
		$post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer.list",['.json_encode($postInfo).']],"id":"'.$apiReqId.'"}';
		
				
        $data = self::zCurl(self::$API_URI, $post);
        $apiResult=json_decode($data, true);
		$customer_id = $apiResult['result'][0]['customer_id'];	
	
	    if ($customer_id) {
		  $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer.delete",['.$customer_id.']],"id":"'.$apiReqId.'"}';
		
				
          $data = self::zCurl(self::$API_URI, $post);
          $apiResult=json_decode($data, true);		
		  
		  if ($apiResult['result']) 
		    $result = '';

		  
		
		
		}
	  
	 /* else 
		   $result = 'str_CustomerNoExists';*/
	
	
		
		return $result;	
	}


	/*
	* Processes a request to check if a login exists in the external system
	*
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'id'									- integer
	*		'login'									- varchar(50)
	*
	* @return boolean - false if login does not exist in the external system or true if the login already exists in the external system
	*
	* @author Kevin Gale
	* @since Version 4.2.0
	*/
	// static function loginExists($pParamArray)
	// {
	// 	$result = false;


	// 	here is where we perform whatever action is required to check to see if the login exists in the external system


	// 	return $result;
	// }


	/*
	* Processes to determine if Taopix or the external script is responsible for the request to reset the password of an external account
	*
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'languagecode'							- varchar(50)
	*		'groupcode'								- varchar(50)
	*		'brandcode'								- varchar(50)
	*		'id'									- integer
	*		'login'									- varchar(50)
	*		'accountcode'							- varchar(50)
	*		'emailaddress'							- varchar(50)
	*		'isordersession'						- integer
	*
	*
	* @return array
	*   the array will contain:
	*	   	string 'result' - NOTHANDLED the script hasn't handled the process and that processing should continue as normal
	*						- REDIRECT the script will handle the reset password process.  Taopix will not send the password reset email that contains the reset link.
	*								   If a redirect URL has been provided Taopix will redirect to this URL for the user to complete the password reset process.
	*								   If a redirect URL has not been provided TAOPIX will show the Taopix confirmation page but the script must still handle the reset password process.
	*		string 'redirecturl' - a valid URL to redirect the user to in order to complete the rest password process. REDIRECT must be returned as the result in order to redirect the user.
	*
	* @author Stuart Milne
	* @since Version 2018r5
	*/
	static function resetPasswordInit($pParamArray)
	{
		$resultArray = Array();
		$result = '';
		$lang = (strpos(pParamArray['languagecode'], 'zh') !== false) ? 'zh' : 'en';
		
		$redirectURL = "https://www.ubabybaby.com/$lang/customer/account/forgotpassword/";

		// 	/*
		// 	here is where we perform whatever action is required to determine if Taopix or the script is to handle the password reset request.
		// 	this should at least involve checking the login, email address and brand to find an account that matches
		// 	*/



		// 	in this example we just state that the script hasn't handled the process and that processing should continue as normal
		$result = 'REDIRECT';

		$resultArray['result'] = $result;
		$resultArray['redirecturl'] = $redirectURL;

		return $resultArray;
	 }

	/*
	* Processes a request to reset the password of an external account
	*
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'languagecode'							- varchar(50)
	*		'groupcode'								- varchar(50)
	*		'brandcode'								- varchar(50)
	*		'id'									- integer
	*		'login'									- varchar(50)
	*		'newpassword'							- varchar(50)
	*		'passwordformat'						- integer
	*		'accountcode'							- varchar(50)
	*		'emailaddress'							- varchar(50)
	*		'isordersession'						- integer
	*
	*
	* @return array
	*   the array will contain:
	*	   	string 'result' - empty for a successful password reset or not empty to display an error
	*	   	boolean 'sendnotification' - controls if the standard taopix reset password confirmation email should be generated on success
	*		boolean 'storepassword' - controls if the users password should be updated within the Taopix database
	*		string 'contactfirstname' - the first name of the user if the taopix reset password email is generated
	*		string 'contactlastname' - the last name of the user if the taopix reset password email is generated
	*
	* @author Stuart Milne
	* @since Version 2018r5
	*/
	 static function resetPassword($pParamArray)
	 {
	 	$resultArray = Array();
	 	$result = '';
	 	$sendNotification = false;
	 	$storePassword = true;
		$newPassword = '';
		//$newpassword = $pParamArray['newpassword'];
	 	$contactFirstName = '';
	 	$contactLastName = '';

	    $apiResult = self::apiLogin(self::API_USER,self::API_PWD);
		$apiSession = $apiResult['result'];
		$apiReqId =  $apiResult['id'];     
		$postInfo = array(
          "username"=> $pParamArray['login'],
		  "email"   => $pParamArray['emailaddress']
		
		);
		
		$post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer.list",['.json_encode($postInfo).']],"id":"'.$apiReqId.'"}';
		

		
		
        $data = self::zCurl(self::$API_URI, $post);
        $apiResult=json_decode($data, true);
		$customer_id = $apiResult['result'][0]['customer_id'];		
		
		

		
		if ($customer_id)
		{
		
		
		 
		    $newPassword = self::generateStrongPassword(10);		
			$contactFirstName =  $apiResult['result'][0]['firstname'];
			$contactLastName = $apiResult['result'][0]['lastname'];
			$sendNotification = true;
			
		if ($customer_id > 0 && is_null($apiResult['error'])) {
		
		
         $newSalt =  base64_encode(md5(uniqid()));
         $newPassword_hased = md5($newSalt.$newPassword).':'.$newSalt;  
             

		
		
		 $postInfo = array("password_hash"=>$newPassword_hased);

         $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer.update",["'.$customer_id.'",'.json_encode($postInfo).']],"id":"'.$apiReqId.'"}';		
         $data = self::zCurl(self::$API_URI, $post);
         $apiResult=json_decode($data, true);         


		 
		 
         if ($apiResult['result'])
	    	$result = '';
		 else 
		   $result = 'str_ErrorCannotChangePassword';
		
		}			
			
			
			
			
		}
		else
		{
			/*
			we have not matched so return an error
			this must be the name of a taopix string either in the # Common section or the [Login] section
			*/
			
			$result = 'str_ErrorNoAccount';
		}
		

	 	$resultArray['result'] = $result;
	 	$resultArray['sendnotification'] = $sendNotification;
	 	$resultArray['storepassword'] = $storePassword;
	 	$resultArray['contactfirstname'] = $contactFirstName;
	 	$resultArray['contactlastname'] = $contactLastName;

	 	return $resultArray;
	 }


	/*
	* Processes a request to update the password of an external account
	*
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'languagecode'							- varchar(50)
	*		'groupcode'								- varchar(50)
	*		'brandcode'								- varchar(50)
	*		'id'									- integer
	*		'login'									- varchar(50)
	*		'accountcode'							- varchar(50)
	*		'passwordformat'						- integer
	*		'origpassword'							- varchar(50)
	*		'newpassword'							- varchar(50)
	*
	* @return string - empty for a successful password update or not empty to display an error
	*
	* @author Kevin Gale
	* @since Version 3.5.0
	*/
	// static function updatePassword($pParamArray)
	// {
	// 	$result = '';


	// 	here is where we perform whatever action is required to update the password in the external account
	// 	before an account is updated some basic validation (such as checking the brandcode / login / accountcode / origpassword) should be performed


	// 	/*
	// 	in this example we just state that the script hasn't handled the process and that processing should continue as normal
	// 	*/
	// 	$result = 'NOTHANDLED';

	// 	return $result;
	// }


	/*
	* Processes a request to update the account preferences of an external account
	*
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'languagecode'							- varchar(50)
	*		'groupcode'								- varchar(50)
	*		'brandcode'								- varchar(50)
	*		'id'									- integer
	*		'login'									- varchar(50)
	*		'accountcode'							- varchar(50)
	*		'sendmarketinginfo'						- integer
	*
	* @return string - empty for a successful update or not empty to display an error
	*
	* @author Kevin Gale
	* @since Version 3.5.0
	*/
	// static function updateAccountPrefs($pParamArray)
	// {
	// 	$result = '';


	// 	here is where we perform whatever action is required to update the external account preferences
	// 	before an account is updated some basic validation (such as checking the brandcode / login / accountcode) should be performed


	// 	// in this example we just return an empty string to allow the action to continue
	// 	$result = '';

	// 	return $result;
	// }


	/*
	* Processes a request to update the account balance of an external account
	*
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'languagecode'							- varchar(50)
	*		'groupcode'								- varchar(50)
	*		'brandcode'								- varchar(50)
	*		'id'									- integer
	*		'login'									- varchar(50)
	*		'accountcode'							- varchar(50)
	*		'accountbalancedifference'				- decimal
	*
	* @author Kevin Gale
	* @since Version 3.5.0
	*/
	static function updateAccountBalance($pParamArray)
	{
        $accountGroupCode = $pParamArray['accountgroupcode'];
		if ($accountGroupCode !== 'STSTEPHENS') { 
		$apiResult = self::apiLogin(self::API_USER,self::API_PWD);
		$apiSession = $apiResult['result'];
		$apiReqId =  $apiResult['id'];     
		$postInfo = array(
          "username"=>$pParamArray['login']
		
		);
		
		$post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer.list",['.json_encode($postInfo).']],"id":"'.$apiReqId.'"}';
		
		
        $data = self::zCurl(self::$API_URI, $post);
        $apiResult=json_decode($data, true);
		$customerId = $apiResult['result'][0]['customer_id'];
        $accountbalance =  $apiResult['result'][0]['accountbalance'];
       
		
		
		if ($customerId > 0) {
		

         $accountbalance  = floatval($accountbalance) + floatval($pParamArray['accountbalancedifference']);   

		
		
		 $postInfo = array("accountbalance"=>$accountbalance);

         $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer.update",["'.$customerId.'",'.json_encode($postInfo).']],"id":"'.$apiReqId.'"}';		
         $data = self::zCurl(self::$API_URI, $post);
         $apiResult=json_decode($data, true);         

          
		 
		 
         if ($apiResult['result'])
	    	$result = '';

		}
		
		else {
		
		//$result = 'str_ErrorCannotChangePassword';
		}	
	
	} else {
		$result = '';
	}
	
	}


	/*
	* Processes a request to update the gift card balance of an external account
	*
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'languagecode'							- varchar(50)
	*		'groupcode'								- varchar(50)
	*		'brandcode'								- varchar(50)
	*		'id'									- integer
	*		'login'									- varchar(50)
	*		'accountcode'							- varchar(50)
	*		'isordersession'						- integer
	*		'giftcardbalancedifference'				- decimal
	*
	* @author Kevin Gale
	* @since Version 3.5.0
	*/
	static function updateGiftCardBalance($pParamArray)
	{
		$apiResult = self::apiLogin(self::API_USER,self::API_PWD);
		$apiSession = $apiResult['result'];
		$apiReqId =  $apiResult['id'];     
		$postInfo = array(
          "username"=>$pParamArray['login']
		
		);
		
		$post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer.list",['.json_encode($postInfo).']],"id":"'.$apiReqId.'"}';
		
		
        $data = self::zCurl(self::$API_URI, $post);
        $apiResult=json_decode($data, true);
		$customerId = $apiResult['result'][0]['customer_id'];
        $giftcardbalance =  $apiResult['result'][0]['giftcardbalance'];
       
		
		
		if ($customerId > 0) {
		

         $giftcardbalance  = floatval($giftcardbalance) + floatval($pParamArray['giftcardbalancedifference']);   

		
		
		 $postInfo = array("giftcardbalance"=>$giftcardbalance);

         $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer.update",["'.$customerId.'",'.json_encode($postInfo).']],"id":"'.$apiReqId.'"}';		
         $data = self::zCurl(self::$API_URI, $post);
         $apiResult=json_decode($data, true);         


		 
		 
         if ($apiResult['result'])
	    	$result = '';

		}
		
		else {
		
		//$result = 'str_ErrorCannotChangePassword';
		}	
	
	
	

	
	
	}


	/*
	* Requests an external login
	*
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'languagecode'							- varchar(50)
	*		'designergroupcode'						- varchar(50)
	*		'accountgroupcode'						- varchar(50)
	*		'brandcode'								- varchar(50)
	*		'id'									- integer
	*		'login'									- varchar(50)
	*		'accountcode'							- varchar(50)
	*		'passwordformat'						- integer
	*		'password'								- varchar(50)
	*		'status'								- integer
	*		'isordersession'						- integer
	*		'useraccount[]'
	*
	*	useraccount is an array that defines a standard taopix user account.
	*	items within the array that are not listed here should be ignored.
	*		'groupcode'								- varchar(50)
	*		'accountcode'							- varchar(50)
	*		'companyname'							- varchar(200)
	*		'address1'								- varchar(200)
	*		'address2'								- varchar(200)
	*		'address3'								- varchar(200)
	*		'address4'								- varchar(200)
	*		'city'									- varchar(200)
	*		'county'								- varchar(50)
	*		'state'									- varchar(200)
	*		'regioncode'							- varchar(20)
	*		'region'								- varchar(10)
	*		'postcode'								- varchar(200)
	*		'countrycode'							- varchar(10)
	*		'countryname'							- varchar(50)
	*		'telephonenumber'						- varchar(50)
	*		'emailaddress'							- varchar(50)
	*		'contactfirstname'						- varchar(200)
	*		'contactlastname'						- varchar(200)
	*		'registeredtaxnumbertype'				- integer
	*		'registeredtaxnumber'					- varchar(50)
	*		'usedefaultpaymentmethods'				- integer
	*		'paymentmethods'						- varchar(100)
	*		'taxcode'								- varchar(20)
	*		'shippingtaxcode'						- varchar(20)
	*		'uselicensekeyforshippingaddress'		- integer
	*		'canmodifyshippingaddress'				- integer
	*		'canmodifyshippingcontactdetails'		- integer
	*		'uselicensekeyforbillingaddress'		- integer
	*		'canmodifybillingaddress'				- integer
	*		'useremaildestination'					- integer
	*		'defaultaddresscontrol'					- integer
	*		'canmodifypassword'						- integer
	*		'creditlimit'							- decimal
	*		'accountbalance'						- decimal
	*		'giftcardbalance'						- decimal
	*		'sendmarketinginfo'						- integer
	*		'isactive'								- integer
	*
	* @return array
	*   the array will contain:
	*	   	string 'result' - empty for a successful authorisation, NOTHANDLED if the script does not handle the login or a Taopix string to display an error
	*		array 'useraccount' - the modified version of the supplied user account array
	*		boolean 'updateaccountbalancelimit' - false to leave credit limit / account balance untouched  true - to update
	*		boolean 'updategiftcardbalance' - false to leave gift card balance untouched  true - to update
	*
	* @author Kevin Gale
	* @since Version 3.5.0
	*/
	static function login($pParamArray)
	{
		$resultArray = Array();
		$result = '';
		$updateAccountBalance = true;
		$updateGiftCardBalance = false;
		
		$userAccountArray = $pParamArray['useraccount'];

		$accountGroupCode = $pParamArray['accountgroupcode'];
		if ($accountGroupCode !== 'STSTEPHENS') {

		$apiResult = self::apiLogin(self::API_USER,self::API_PWD);
		$apiSession = $apiResult['result'];
		$apiReqId =  $apiResult['id']; 

		//check whether login is email or username
		if (filter_var($pParamArray['login'], FILTER_VALIDATE_EMAIL)) {    
		$postInfo = array(
          "email"=>$pParamArray['login']
		
		); }
		else {
			$postInfo = array(
				"username"=>$pParamArray['login']
			  
			  ); 
		}
		
		$post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer.list",['.json_encode($postInfo).']],"id":"'.$apiReqId.'"}';
		


		
        $data = self::zCurl(self::$API_URI, $post);
        $apiResult=json_decode($data, true);
		$email = $apiResult['result'][0]['email'];

		
		$postInfo = array(
		
		"Login",
		"base",
		$email,
		$pParamArray["password"]
		
		);
	
		

        $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","CustomerLogin_Folder.customerlogin",'.json_encode($postInfo).'],"id":"'.$apiReqId.'"}';
		

        $data = self::zCurl(self::$API_URI, $post);
        $apiResult=json_decode($data, true);
		$customerId = $apiResult['result']['customer_id'];

		if ($customerId > 0 && is_null($apiResult['error'])) {
		 $userAccountArray["login"] =  $apiResult['result']['customerobject']['username'];
		 $userAccountArray["password"] =  $apiResult['result']['customerobject']['password'];
		 $userAccountArray["passwordformat"] =  $apiResult['result']['customerobject']['passwordformat'];
		 $userAccountArray["emailaddress"] =  $apiResult['result']['customerobject']['email'];
         $userAccountArray["giftcardbalance"] = ($apiResult['result']['customerobject']['giftcardbalance']) ? $apiResult['result']['customerobject']['giftcardbalance'] : 0;
         $userAccountArray["accountbalance"] = ($apiResult['result']['customerobject']['accountbalance']) ? $apiResult['result']['customerobject']['accountbalance'] : 0;
         $userAccountArray["canmodifypassword"] = ($apiResult['result']['customerobject']['canmodifypassword'])  ? $apiResult['result']['customerobject']['canmodifypassword'] : 1;
         $userAccountArray["useremaildestination"] = ($apiResult['result']['customerobject']['useremaildestination']) ? $apiResult['result']['customerobject']['useremaildestination'] : 0;
         $userAccountArray["canmodifybillingaddress"] = ($apiResult['result']['customerobject']['canmodifybilladdr']) ? $apiResult['result']['customerobject']['canmodifybilladdr'] : 1;
         $userAccountArray["canmodifyshippingcontactdetails"] = ($apiResult['result']['customerobject']['canmodifyshipcontact']) ? $apiResult['result']['customerobject']['canmodifyshipcontact'] : 1;
         $userAccountArray["canmodifyshippingaddress"] = ($apiResult['result']['customerobject']['canmodifyshippingaddr']) ? $apiResult['result']['customerobject']['canmodifyshippingaddr'] : 0;
         $userAccountArray["uselicensekeyforshippingaddress"] = ($apiResult['result']['customerobject']['uselicensekeyshipaddr']) ? $apiResult['result']['customerobject']['uselicensekeyshipaddr'] : 0;
         $userAccountArray["defaultaddresscontrol"] = ($apiResult['result']['customerobject']['defaultaddresscontrol']) ? $apiResult['result']['customerobject']['defaultaddresscontrol'] : 1;
         $userAccountArray["shippingtaxcode"] = ($apiResult['result']['customerobject']['shippingtaxcode']) ? $apiResult['result']['customerobject']['shippingtaxcode'] : '';
         $userAccountArray["taxcode"] = ($apiResult['result']['customerobject']['taxcode']) ? $apiResult['result']['customerobject']['taxcode'] : '';
         $userAccountArray["paymentmethods"] = ($apiResult['result']['customerobject']['paymentmethods']) ? $apiResult['result']['customerobject']['paymentmethods'] : '';
         $userAccountArray["usedefaultpaymentmethods"] = ($apiResult['result']['customerobject']['usedefaultpaymethods']) ? $apiResult['result']['customerobject']['usedefaultpaymethods'] : '';

         $userAccountArray["registeredtaxnumber"] = ($apiResult['result']['customerobject']['registeredtaxnumber']) ? $apiResult['result']['customerobject']['registeredtaxnumber'] : '';
         $userAccountArray["registeredtaxnumbertype"] = ($apiResult['result']['customerobject']['registeredtaxtype']) ? $apiResult['result']['customerobject']['registeredtaxtype'] : 0;
         $userAccountArray["creditlimit"] = ($apiResult['result']['customerobject']['credit_amount']) ? $apiResult['result']['customerobject']['credit_amount'] : 0;
         $userAccountArray["groupcode"] = $apiResult['result']['customerobject']['groupcode'];
         $userAccountArray["accountcode"] = ($apiResult['result']['customerobject']['accountcode']) ? $apiResult['result']['customerobject']['accountcode'] : '';
         $userAccountArray["sendmarketinginfo"] = 1; 
		 $userAccountArray["isactive"] = 1; 
		 $resultArray['isactive'] = 1;



           $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer_address.list",['.$customerId.']],"id":"'.$apiReqId.'"}';
		
           $data = self::zCurl(self::$API_URI, $post);
           $apiResult=json_decode($data, true);
		   $addresses = $apiResult['result'];
	       foreach($addresses as $address) {
		     
			 if ($address['is_default_billing']) {
               
			   $userAccountArray["companyname"] = ($address["company"]) ? $address["company"] : '';
			   $userAccountArray["contactfirstname"] = $address["firstname"];
			   $userAccountArray["contactlastname"] = $address["lastname"];
			   
			   
			   $streets = explode("\n", $address["street"]);
			   
			   
               $userAccountArray["address1"] =  ($streets[0]) ? $streets[0] : '';
               $userAccountArray["address2"] =  ($streets[1]) ? $streets[1] : '';
               $userAccountArray["address3"] =  ($streets[2]) ? $streets[2] : '' ;
               $userAccountArray["address4"] =  ($streets[3]) ? $streets[3] : '' ;
			   
               $userAccountArray["city"] =  $address["city"];
			   if (!empty($address["region"])) {
			     $userAccountArray["region"] =  'STATE';
                 $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","region.list",["'.$address["country_id"].'"]],"id":"'.$apiReqId.'"}';		
   		        $data = self::zCurl(self::$API_URI, $post);
                $apiResult=json_decode($data, true);  
	            $regionData = self::search($apiResult['result'],'name',$address["region"]);
		         if (isset($regionData[0]) ) {
		 
		           $userAccountArray["regioncode"] =$regionData[0]["code"];
		 
		         }			   
			   
			   }
			   
			   
			   
               $userAccountArray["state"] =  ($address["region"]) ? $address["region"] : '' ;
               $userAccountArray["postcode"] = ($address["postcode"]) ? $address["postcode"] : '';
               $userAccountArray["countrycode"] =  $address["country_id"];
               //$userAccountArray["countryname"] =  $apiResult['result']['customerobject']["countryname"];
               $userAccountArray["telephonenumber"] =  $address["telephone"];			 
			   
			 
			 
			 
			 }
		   
		   
		   
		    }
		     
		
		  

		
		
		}
		
		else {
		 
		  $result = 'str_ErrorPreviewPasswordWrong';
		
		}
		

		}

		else {
			// non external accounts
			$result = 'NOTHANDLED';
		}

		$resultArray['result'] = $result;
		$resultArray['useraccount'] = $userAccountArray;
		$resultArray['updateaccountbalance'] = $updateAccountBalance;
		$resultArray['updategiftcardbalance'] = $updateGiftCardBalance;		
		$resultArray['passwordformat'] = $userAccountArray['passwordformat'];
	
		return $resultArray;
		
	}
  /*
    * Validate the password of the current user
    *
    * @param array $pParamArray
    *   the array will contain:
    *        'languagecode'                         - varchar(50)
    *        'login'                                - varchar(200)
    *        'password'                             - varchar(200)
    *        'passwordformat'                       - tinyint(1)
    *        'designergroupcode'                    - varchar(50)
    *        'accountgroupcode'                     - varchar(50)
    *        'brandcode'                            - varchar(50)
    *        'accountcode'                          - varchar(50)
    *        'useraccount'                          - array
    *
    *	useraccount is an array that defines a standard taopix user account.
    *	items within the array that are not listed here should be ignored.
    *		'groupcode'                             - varchar(50)
    *		'accountcode'                           - varchar(50)
    *		'companyname'                           - varchar(200)
    *		'address1'                              - varchar(200)
    *		'address2'                              - varchar(200)
    *		'address3'                              - varchar(200)
    *		'address4'                              - varchar(200)
    *		'city'                                  - varchar(200)
    *		'county'                                - varchar(50)
    *		'state'	                                - varchar(200)
    *		'regioncode'                            - varchar(20)
    *		'region'								- varchar(10)
    *		'postcode'                              - varchar(200)
    *		'countrycode'                           - varchar(10)
    *		'countryname'                           - varchar(50)
    *		'telephonenumber'                       - varchar(50)
    *		'emailaddress'                          - varchar(50)
    *		'contactfirstname'                      - varchar(200)
    *		'contactlastname'                       - varchar(200)
    *		'registeredtaxnumbertype'               - integer
    *		'registeredtaxnumber'                   - varchar(50)
    *		'usedefaultpaymentmethods'              - integer
    *		'paymentmethods'                        - varchar(100)
    *		'taxcode'                               - varchar(20)
    *		'shippingtaxcode'                       - varchar(20)
    *		'uselicensekeyforshippingaddress'       - integer
    *		'canmodifyshippingaddress'              - integer
    *		'canmodifyshippingcontactdetails'       - integer
    *		'uselicensekeyforbillingaddress'        - integer
    *		'canmodifybillingaddress'               - integer
    *		'useremaildestination'                   - integer
    *		'defaultaddresscontrol'                 - integer
    *		'canmodifypassword'                     - integer
    *		'creditlimit'                           - decimal
    *		'accountbalance'                        - decimal
    *		'giftcardbalance'                       - decimal
    *		'sendmarketinginfo'                     - integer
    *		'isactive'                              - integer
    *
    * @return array
    *   the array will contain:
    *       string 'result' - empty for successful validation or the name of the language string for the error (str_MessageAuthMode_Password)
    *
    * @author Rob Bunker
    * @since Version v2020r1
    */
    static function authenticate($pParamArray)
    {
        $resultArray = Array();
        $result = 'NOTHANDLED';

        // 	/*
        // 	here is where we validate the user password
        // 	this could be performed by an api call or database connection to the external system
        // 	whatever the method the result should be a confirmation / rejection that we can return to taopix
        // 	*/


        // 	/*
        // 	in this example we just state that the script hasn't handled the process and that processing should continue as normal
        // 	*/

        $resultArray['result'] = $result;

        return $resultArray;
    }
	
	
	/*
	* Processes a request to update the password of an external account
	*
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'languagecode'							- varchar(50)
	*		'groupcode'								- varchar(50)
	*		'brandcode'								- varchar(50)
	*		'id'									- integer		
	*		'login'									- varchar(50)
	*		'accountcode'							- varchar(50)
	*		'passwordformat'						- integer
	*		'origpassword'							- varchar(50)
	*		'newpassword'							- varchar(50)
	*
	* @return string - empty for a successful password update or not empty to display an error
	*
	* @author Kevin Gale
	* @since Version 3.5.0
	*/		
	static function updatePassword($pParamArray)
	{
		
		$apiResult = self::apiLogin(self::API_USER,self::API_PWD);
		$apiSession = $apiResult['result'];
		$apiReqId =  $apiResult['id'];     
		$postInfo = array(
          "username"=>$pParamArray['login']
		
		);
		
		$post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer.list",['.json_encode($postInfo).']],"id":"'.$apiReqId.'"}';
		

		
		
        $data = self::zCurl(self::$API_URI, $post);
        $apiResult=json_decode($data, true);
		$email = $apiResult['result'][0]['email'];
		
		
		$postInfo = array(
		
		"Login",
		"base",
		$email,
		$pParamArray["origpassword"]
		
		);
	
		

        $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","CustomerLogin_Folder.customerlogin",'.json_encode($postInfo).'],"id":"'.$apiReqId.'"}';
		

        $data = self::zCurl(self::$API_URI, $post);
        $apiResult=json_decode($data, true);
		$customerId = $apiResult['result']['customer_id'];
		

		
		
		
		if ($customerId > 0 && is_null($apiResult['error'])) {
		
		
         $newSalt =  base64_encode(md5(uniqid()));
         $newPassword_hased = md5($newSalt.$pParamArray["newpassword"]).':'.$newSalt;  
             

		
		
		 $postInfo = array("password_hash"=>$newPassword_hased);

         $post = '{"jsonrpc":"2.0","method":"call","params":["'.$apiSession.'","customer.update",["'.$customerId.'",'.json_encode($postInfo).']],"id":"'.$apiReqId.'"}';		
         $data = self::zCurl(self::$API_URI, $post);
         $apiResult=json_decode($data, true);         


		 
		 
         if ($apiResult['result'])
	    	$result = '';
		 else 
		   $result = 'str_ErrorCannotChangePassword';
		
		}
		
		else {
		
		$result = 'str_ErrorCannotChangePassword';
		}
		
		return $result;	
	}	

 static function zCurl($url,$post,$outtime=20)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 2.0.50727; InfoPath.1; CIBA)");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $outtime);

		$parsedURL = parse_url($url);
		
		if (isset($parsedURL["scheme"]) && $parsedURL["scheme"] == 'https'  ) {
		  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   
		
		}
		
		
        if ($post){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        if ($followlocation=='on'){
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        }
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_NOBODY, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data=curl_exec($ch);
        curl_close($ch);
        return $data;
    }


    static function apiLogin($user,$password)
    {
        
		$post = array(
		   "jsonrpc" => "2.0",
		   "method" => "login",
		   "params" => array($user,$password),
		   "id" => md5(md5(time()))
		   
		       
		
		);
		$post = json_encode($post);

		
		
        $data = self::zCurl(self::$API_URI, $post);
        return json_decode($data, true);
    }

	/*
	* Retrieve the default group code for a brand when processing a single sign-on login request
	*
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'brandcode'	- varchar(50)
	*
	* @return string - the default group code for new user accounts
	*
	* @author Chris Mitchell
	* @since Version v2016r2
	*/
	static function ssoGetBrandDefaultGroupCode($pParamArray)
	{
		$brandCode = $pParamArray['brandcode'];
		
		
		switch($brandCode) {
	     case 'UBBPRO':
		       $groupCode = 'UBBPRO';
               break;	
		 case 'PHOTOBOOKAPP':
				$groupCode = 'KAN-PHOTO';
				break;				   		   			   
         case 'DEFAULT':
		       $groupCode = 'UBABYHK';
               break;			 
		 
         default:		 
			$groupCode = '';
		}

		return $groupCode;
	}


	/*
	* Process a single sign-on login request
	*
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'languagecode'							- varchar(50)
	*		'designergroupcode'						- varchar(50)
	*		'brandcode'								- varchar(50)
	*		'isordersession'						- integer
	*		'reason'								- integer
	*		'sourceurl'								- varchar(200)
	*		'ssostage'								- integer
	*		'ssotoken'								- varchar(200)
	*		'ssoprivatedata[]'						- array
	*		'useraccount[]'
	*
	*	useraccount is an array that defines a standard taopix user account.
	*	items within the array that are not listed here should be ignored.
	*		'groupcode'								- varchar(50)
	*		'accountcode'							- varchar(50)
	*		'companyname'							- varchar(200)
	*		'address1'								- varchar(200)
	*		'address2'								- varchar(200)
	*		'address3'								- varchar(200)
	*		'address4'								- varchar(200)
	*		'city'									- varchar(200)
	*		'county'								- varchar(50)
	*		'state'									- varchar(200)
	*		'regioncode'							- varchar(20)
	*		'region'								- varchar(10)
	*		'postcode'								- varchar(200)
	*		'countrycode'							- varchar(10)
	*		'countryname'							- varchar(50)
	*		'telephonenumber'						- varchar(50)
	*		'emailaddress'							- varchar(50)
	*		'contactfirstname'						- varchar(200)
	*		'contactlastname'						- varchar(200)
	*		'registeredtaxnumbertype'				- integer
	*		'registeredtaxnumber'					- varchar(50)
	*		'usedefaultpaymentmethods'				- integer
	*		'paymentmethods'						- varchar(100)
	*		'taxcode'								- varchar(20)
	*		'shippingtaxcode'						- varchar(20)
	*		'uselicensekeyforshippingaddress'		- integer
	*		'canmodifyshippingaddress'				- integer
	*		'canmodifyshippingcontactdetails'		- integer
	*		'uselicensekeyforbillingaddress'		- integer
	*		'canmodifybillingaddress'				- integer
	*		'useremaildestination'					- integer
	*		'defaultaddresscontrol'					- integer
	*		'canmodifypassword'						- integer
	*		'creditlimit'							- decimal
	*		'accountbalance'						- decimal
	*		'giftcardbalance'						- decimal
	*		'sendmarketinginfo'						- integer
	*		'isactive'								- integer
	*
	* @return array
	*   the array will contain:
	*	   	string 'result' - empty if a token could be retrieved and the useraccount array has been populated
	*						  SSOREDIRECT to re-direct to an url if the token is stored elsewhere
	*						  NOTHANDLED to ignore the single sign-on process
	*		string 'resultparam' - the url to redirect to if the result is SSOREDIRECT, otherwise empty
	*		array 'useraccount' - the modified version of the supplied user account array when the result is empty
	*		boolean 'updateaccountdetails' - controls if the main details of an existing taopix account are updated
	*		boolean 'updateaccountbalancelimit' - false to leave credit limit / account balance untouched  true - to update
	*		boolean 'updategiftcardbalance' - false to leave gift card balance untouched  true - to update
	*		boolean 'updategroupcode' - false to leave the group code untouched  true - update the users group code
	*		string 'ssotoken' - the token obtained from the licensees system for the sso session
	*		array 'ssoprivatedata' - the data the licensee required to store for the session. this will be fed back into this
									function when ssostage is 1
	*		string 'ssoexpiredate' - the data and time the ssotoken expires in MYSQL date format. this data is passed to the
									online designer and will be used as the expire date time for the online session
	*		array 'assetservicedata' - if image led workflow is being used this will contain information about the asset service to link
										to and the assets to add from it. this is only valid for online designer.
	*
	* @author Chris Mitchell
	* @since Version v2016r2
	*/

	static function ssoLogin($pParamArray)
	{

		$resultArray = array();
		$result = '';
		$resultParam = '';

		$updateAccountDetails = false;
		$updateAccountBalance = false;
		$updateGiftcardBalance = false;
		$updateGroupCode = false;

		$userAccountArray = $pParamArray['useraccount'];
		$ssoToken = $pParamArray['ssotoken'];
		$ssoStage = $pParamArray['ssostage'];
		$ssoPrivateData = $pParamArray['ssoprivatedata'];
		$assetServiceData = array();
		$ssoExpireDate = '';

		if (isset($_GET['nosso']) || $pParamArray['brandcode'] !== 'UBBPRO')
		{
           $result = 'NOTHANDLED';
	    } else {
			$provider = self::getOauth2Provider($pParamArray['brandcode']); 
			if ($ssoStage == 1 ) {
				if (isset($ssoPrivateData['ssotoken']))  {
					$tokens['accessToken'] = $ssoPrivateData['ssotoken'];

                    try {
						$request = $provider->getAuthenticatedRequest(
							'GET',
							'https://oauth2.ubb-pro.com/userinfo' ,
						   $tokens['accessToken']
						);
		 
						$response = $provider->getParsedResponse($request);
						unset( $ssoPrivateData['code']);

					} catch(\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

						UtilsObj::writeToDebugFileInLogsFolder('edl.txt',$e);
	   
					  }



                     
				} else {

				$result = "SSOREDIRECT";
				$resultParam = self::getSsoAuthUrl($pParamArray['brandcode']);
				}


			} else if ($ssoStage == 2) {
               $code = $ssoPrivateData['code'];
			   
              
			   try {
                    if (isset($ssoPrivateData['ssotoken']))  {
						$tokens['accessToken'] = $ssoPrivateData['ssotoken'];

					}
					else {
				       
				         // Try to get an access token using the authorization code grant.
					   $accessToken = $provider->getAccessToken('authorization_code', [
							'code' => $code
						]);
			  
						$tokens['accessToken'] = $accessToken->getToken();
						$tokens['refreshToken'] = $accessToken->getRefreshToken();
						$tokens['expires'] = $accessToken->getExpires();
						$tokens['idToken'] =  $accessToken->getValues()['id_token'];
						unset($ssoPrivateData['code']);

					}
                       $request = $provider->getAuthenticatedRequest(
					   'GET',
                       'https://oauth2.ubb-pro.com/userinfo' ,
                      $tokens['accessToken']
                   );
    
                   $response = $provider->getParsedResponse($request);
                   UtilsObj::writeToDebugFileInLogsFolder('edl.txt','',$response);
				   		  


			   } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

				 UtilsObj::writeToDebugFileInLogsFolder('edl.txt',$e);

			   }



			}
            if (isset($response)) {
				$groupCode = 'UBABYHK';  
				if (!empty($response['metadatapublic']['groupcode'])) {
				   $groupCode = $response['metadatapublic']['groupcode'];
				}	else if ($pParamArray['brandcode'] == 'UBBPRO') {
					$groupCode = 'UBBPRO';
					
				}		 
							   
				$userAccountArray['groupcode'] = $groupCode;
				$userAccountArray['login'] = is_null($response['username']) ? $response['email'] : $response['username'];
				$userAccountArray['emailaddress'] = $response['email'];
				$userAccountArray['accountbalance'] = $response['metadatapublic']['accountbalance'];
				$userAccountArray['accountcode'] = ($response['metadatapublic']['accountcode']) ? $response['metadatapublic']['accountcode'] : $response['id'] ;
				$userAccountArray['brandcode'] = ($response['metadatapublic']['brandcode']) ? $response['metadatapublic']['brandcode'] : $pParamArray['brandcode'];
				$userAccountArray['canmodifybillingaddress'] = is_null($response['metadatapublic']['canmodifybilladdr']) ? 1 : $response['metadatapublic']['canmodifybilladdr'];
				$userAccountArray['canmodifypassword'] = $response['metadatapublic']['canmodifypassword'];
				$userAccountArray['canmodifyshippingcontactdetails'] = $response['metadatapublic']['canmodifyshipcontact'];
				$userAccountArray['canmodifyshippingaddress'] = $response['metadatapublic']['canmodifyshippingaddr'];
				$userAccountArray['creditlimit'] = $response['metadatapublic']['credit_amount'];
				$userAccountArray['currencycode'] = is_null($response['metadatapublic']['currencycode']) ? 'HKD' : $response['metadatapublic']['currencycode'];
				$userAccountArray['defaultaddresscontrol'] = $response['metadatapublic']['defaultaddresscontrol'];
				$userAccountArray['giftcardbalance'] = $response['metadatapublic']['giftcardbalance'];
				$userAccountArray['paymentmethods'] = ($response['metadatapublic']['paymentmethods']) ? $response['metadatapublic']['paymentmethods'] :  'CHEQUE,PAYPAL';
				$userAccountArray['registeredtaxnumbertype'] = is_null($response['metadatapublic']['registeredtaxnumber']) ? '' : $response['metadatapublic']['registeredtaxnumber'] ;
				$userAccountArray['registeredtaxtype'] = is_null($response['metadatapublic']['registeredtaxtype']) ? '' : $response['metadatapublic']['registeredtaxtype'];
				$userAccountArray['shippingtaxcode'] = $response['metadatapublic']['shippingtaxcode'];
				$userAccountArray['usedefaultcurrencycode'] = $response['metadatapublic']['usedefaultcurrency'];
				$userAccountArray['usedefaultpaymenthods'] = $response['metadatapublic']['usedefaultpaymenthods'];
				$userAccountArray['uselicensekeyforbillingaddress'] = $response['metadatapublic']['uselicensekeybilladdr'];
				$userAccountArray['uselicensekeyforshippingaddress'] = $response['metadatapublic']['uselicensekeyshipaddr'];
				$userAccountArray['useremaildestination'] = $response['metadatapublic']['useremaildestination'];
				$userAccountArray['contactfirstname'] = $response['name']['first'];
				$userAccountArray['contactlastname'] = $response['name']['last'];	
				$userAccountArray["companyname"]  = '';
				$userAccountArray["address1"] = 'Hong Kong';
				// $userAccountArray["address2"] = 'HK';
				// $userAccountArray["address3"] = 'HK';
				// $userAccountArray["address4"] = 'HK';
				$userAccountArray["city"]= 'Hong Kong';
				// $userAccountArray["state"] = 'NIL';
				$userAccountArray["postcode"] = '00000';
				$userAccountArray["countrycode"] = 'HK';
				$userAccountArray["countryname"] = 'Hong Kong';
				$userAccountArray["regioncode"] = 'HK';
				$userAccountArray["region"] = 'Hong Kong';
				$userAccountArray["taxcode"] = "ZERO";
				$userAccountArray["shippingtaxcode"] = "NIL";
				$userAccountArray["telephonenumber"]= 'NIL';
				$userAccountArray["passwordformat"] = TPX_PASSWORDFORMAT_CLEARTEXT;
				$userAccountArray["password"] = UtilsObj::createRandomString(32);
				
				$userAccountArray['isactive'] = true;
				$resultArray['passwordformat'] = $userAccountArray['passwordformat'];
				//$userAccountArray['ssotoken'] = $tokens['accessToken'];
				$ssoToken = $tokens['accessToken'];
				$ssoPrivateData['ssotoken'] = $tokens['accessToken'];
			  
			  }

		}

		// perform some processing
		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;
		$resultArray['useraccount'] = $userAccountArray;
		$resultArray['updateaccountdetails'] = $updateAccountDetails;
		$resultArray['updateaccountbalance'] = $updateAccountBalance;
		$resultArray['updategiftcardbalance'] = $updateGiftcardBalance;
		$resultArray['updategroupcode'] = $updateGroupCode;
		$resultArray['ssotoken'] = $ssoToken;
		$resultArray['ssoprivatedata'] = $ssoPrivateData;
		$resultArray['assetservicedata'] = $assetServiceData;
		$resultArray['ssoexpiredate'] = $ssoExpireDate;
		
		return $resultArray;

	}

	/*
	* Process a single sign-on logout request
	*
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'reason'								- integer
	*		'ssotoken'								- varchar(200)
	*		'ssoprivatedata[]'						- array
	*
	* @return string - the URL which the user needs to be redirected to once logged out of the
	* TAOPIX system.
	*
	* @author Chris Mitchell
	* @since Version v2016r2
	*/

	static function ssoLogout($pParamArray)
	{
		$logoutURL = '';

		return $logoutURL;
	}

	/*
	* Process a single sign-on callback request
	*
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'ssotoken'								- varchar(200)
	*		'ssoprivatedata[]'						- array
	*
	* @return array
	*   the array will contain:
	*	   	string 'result' - the name of the language string for the title text (str_SSOErrorTitleCustom) or the text of the title
									(Login Error) for single sign-on error page
	*		string 'resultparam' - the name of the language string for the message text (str_SSOErrorMessageCustom) or the text of
									the message (Unable to perform login) for single sign-on error page
	*		string 'ssotoken' - the token posted from the licensees system for the single sign-on session
	*		array 'ssoprivate' - any data which has been obtained from the licensees system for the single sign-on session
	*
	* @author Chris Mitchell
	* @since Version v2016r2
	*/


	static function ssoCallback($pParamArray)
	{
		global $gSession,$gDefaultSiteBrandingCode;
		$errorCode = UtilsObj::getGETParam('error', '');
		$errorDescription = UtilsObj::getGETParam('error_description', '');
		$pParamArray['ssoprivatedata']['code'] =  UtilsObj::getGETParam('code', '');
		$state =  UtilsObj::getGETParam('state', '');
		$resultArray = array();
        $dataRecord = AuthenticateObj::getSSODataRecord($state);       
		$code = $pParamArray['ssoprivatedata']['code'];
			   
		$provider = self::getOauth2Provider($gSession['webbrandcode']);
		try {
				  // Try to get an access token using the authorization code grant.
				$accessToken = $provider->getAccessToken('authorization_code', [
					 'code' => $code
				 ]);
	   
				 $pParamArray['ssotoken'] = $pParamArray['ssoprivatedata']['ssotoken'] =$accessToken->getToken();


			// 	$request = $provider->getAuthenticatedRequest(
			// 	'GET',
			// 	'https://oauth2.ubb-pro.com/userinfo' ,
			//    $tokens['accessToken']
			// );

			// $response = $provider->getParsedResponse($request);
			
			// if (isset($response)) {
			//   $groupCode = 'UBABYHK';  
			//   if (!empty($response['metadatapublic']['groupcode'])) {
			// 	 $groupCode = $response['metadatapublic']['groupcode'];
			//   }	else if ($gSession['webbrandcode'] == 'UBBPRO') {
			// 	  $groupCode = 'UBBPRO';
				  
			//   }		 
							 
			//   $userAccountArray['groupcode'] = $groupCode;
			//   $userAccountArray['login'] = $response['email'];
			//   $userAccountArray['emailaddress'] = $response['email'];
			//   $userAccountArray['accountbalance'] = $response['metadatapublic']['accountbalance'];
			//   $userAccountArray['accountcode'] = ($response['metadatapublic']['accountcode']) ? $response['metadatapublic']['accountcode'] : $response['id'] ;
			//   $userAccountArray['brandcode'] = ($response['metadatapublic']['brandcode']) ? $response['metadatapublic']['brandcode'] : $gSession['webbrandcode'];
			//   $userAccountArray['canmodifybillingaddress'] = $response['metadatapublic']['canmodifybilladdr'];
			//   $userAccountArray['canmodifypassword'] = $response['metadatapublic']['canmodifypassword'];
			//   $userAccountArray['canmodifyshippingcontactdetails'] = $response['metadatapublic']['canmodifyshipcontact'];
			//   $userAccountArray['canmodifyshippingaddress'] = $response['metadatapublic']['canmodifyshippingaddr'];
			//   $userAccountArray['creditlimit'] = $response['metadatapublic']['credit_amount'];
			//   $userAccountArray['currencycode'] = $response['metadatapublic']['currencycode'];
			//   $userAccountArray['defaultaddresscontrol'] = $response['metadatapublic']['defaultaddresscontrol'];
			//   $userAccountArray['giftcardbalance'] = $response['metadatapublic']['giftcardbalance'];
			//   $userAccountArray['paymentmethods'] = ($response['metadatapublic']['paymentmethods']) ? $response['metadatapublic']['paymentmethods'] :  'CHEQUE,PAYPAL';
			//   $userAccountArray['registeredtaxnumbertype'] = $response['metadatapublic']['registeredtaxnumber'];
			//   $userAccountArray['registeredtaxtype'] = $response['metadatapublic']['registeredtaxtype'];
			//   $userAccountArray['shippingtaxcode'] = $response['metadatapublic']['shippingtaxcode'];
			//   $userAccountArray['usedefaultcurrencycode'] = $response['metadatapublic']['usedefaultcurrency'];
			//   $userAccountArray['usedefaultpaymenthods'] = $response['metadatapublic']['usedefaultpaymenthods'];
			//   $userAccountArray['uselicensekeyforbillingaddress'] = $response['metadatapublic']['uselicensekeybilladdr'];
			//   $userAccountArray['uselicensekeyforshippingaddress'] = $response['metadatapublic']['uselicensekeyshipaddr'];
			//   $userAccountArray['useremaildestination'] = $response['metadatapublic']['useremaildestination'];
			//   $userAccountArray['contactfirstname'] = $response['name']['first'];
			//   $userAccountArray['contactlastname'] = $response['name']['last'];	
			//   $userAccountArray["companyname"]  = '';
			//   $userAccountArray["address1"] = 'Hong Kong';
			//   // $userAccountArray["address2"] = 'HK';
			//   // $userAccountArray["address3"] = 'HK';
			//   // $userAccountArray["address4"] = 'HK';
			//   $userAccountArray["city"]= 'Hong Kong';
			//   // $userAccountArray["state"] = 'NIL';
			//   $userAccountArray["postcode"] = '00000';
			//   $userAccountArray["countrycode"] = 'HK';
			//   $userAccountArray["countryname"] = 'Hong Kong';
			//   $userAccountArray["regioncode"] = 'HK';
			//   $userAccountArray["region"] = 'Hong Kong';
			//   $userAccountArray["taxcode"] = "ZERO";
			//   $userAccountArray["shippingtaxcode"] = "NIL";
			//   $userAccountArray["telephonenumber"]= 'NIL';
			//   $userAccountArray["passwordformat"] = TPX_PASSWORDFORMAT_CLEARTEXT;
			//   $userAccountArray["password"] = UtilsObj::createRandomString(32);
			  
			//   $userAccountArray['isactive'] = true;
			//   $userAccountArray['ssotoken'] = $tokens['accessToken'];
			//   $pParamArray['ssotoken'] = $tokens['accessToken'];
			//   $pParamArray['ssoprivatedata']['ssotoken'] = $tokens['accessToken'];
			//   $pParamArray['ssoprivatedata']['useraccount'] = $userAccountArray;
			
			// }


		} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

		  UtilsObj::writeToDebugFileInLogsFolder('edl.txt',$e);

		}



		$resultArray['ssotoken'] = $pParamArray['ssotoken'];
		$resultArray['ssoprivatedata'] = $pParamArray['ssoprivatedata'];

		$resultArray['result'] = '';
		$resultArray['resultparam'] = '';

		return $resultArray;
	}
	
	/*
	* Set the home URL which will be used in e-mail templates
	*
	* @param array $pParamArray
	*   the array will contain:
	*		'ssotoken'								- varchar(200)
	*		'ssoprivatedata[]'						- array
	*		'brandcode'								- string
	*		'url'									- string
	*
	* @param string - the modified home URL
	*
	* @author Chris Mitchell
	* @since Version v2016r2
	*/
	static function ssoSetHomeURL($pParamArray)
	{
		$homeURL = '';

		return $homeURL;
	}

	static function getSsoAuthUrl($groupCode) {
    
        $provider = self::getOauth2Provider($groupCode);    
        
        
        $authorizationUrl = str_replace('%2C','+',$provider->getAuthorizationUrl(array('max_age'=>0)));   
		

        //save state code to session ,it will be verified in callback page.
        setcookie('massosd',  $provider->getState(), 0, '/', '', UtilsObj::needSecureCookies());
		$key = AuthenticateObj::createSSODataRecord(array('oauth2_state' => $provider->getState()),'','',0,-1);
		AuthenticateObj::updateSSODataRecord($key['authkey'], $provider->getState(),'');
		return $authorizationUrl;
      

	}

	static function getOauth2Provider($groupCode) {
		$clientsConfig = array(
			'UBBPRO' => array(
				'clientId'                => "3c27be1d-f514-41f5-b6ac-d8aa7fcb65bf",   // The client ID assigned to you by the provider
				'clientSecret'            =>  "B51ys72dWZuvjq.Sh~Gt0LnoJB",   // The client password assigned to you by the provider
				'redirectUri'             => "https://photobook.ubb-pro.com/sso/callback.php",
				'urlAuthorize'            => "https://oauth2.ubb-pro.com/oauth2/auth",
				'urlAccessToken'          => "https://oauth2.ubb-pro.com/oauth2/token",
				'urlResourceOwnerDetails' => "https://oauth2.ubb-pro.com/userinfo",
				'scopes' => array('openid','offline_access','metadatapublic','email','name','id','username')

		    ),
			'UBABYHK' => array(
				'clientId'                => "",    // The client ID assigned to you by the provider
				'clientSecret'            =>  "",   // The client password assigned to you by the provider
				'redirectUri'             => "",
				'urlAuthorize'            => "",
				'urlAccessToken'          => "",
				'urlResourceOwnerDetails' => "",
				'scopes' => array()

		    )
			

		);
		

		$httpBasicOptionProvider = new \League\OAuth2\Client\OptionProvider\HttpBasicAuthOptionProvider();
        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => $clientsConfig[$groupCode]['clientId'],    // The client ID assigned to you by the provider
            'clientSecret'            =>  $clientsConfig[$groupCode]['clientSecret'],   // The client password assigned to you by the provider
            'redirectUri'             => $clientsConfig[$groupCode]['redirectUri'],
            'urlAuthorize'            => $clientsConfig[$groupCode]['urlAuthorize'],
            'urlAccessToken'          => $clientsConfig[$groupCode]['urlAccessToken'],
            'urlResourceOwnerDetails' => $clientsConfig[$groupCode]['urlResourceOwnerDetails'],
            'scopes' => $clientsConfig[$groupCode]['scopes'],
            //'scopeSeparator' => ""
        ],array('optionProvider'=>$httpBasicOptionProvider));

		return $provider;
	}


	static function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds')
{
	$sets = array();
	if(strpos($available_sets, 'l') !== false)
		$sets[] = 'abcdefghjkmnpqrstuvwxyz';
	if(strpos($available_sets, 'u') !== false)
		$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
	if(strpos($available_sets, 'd') !== false)
		$sets[] = '23456789';
	if(strpos($available_sets, 's') !== false)
		$sets[] = '!@#$%&*?';

	$all = '';
	$password = '';
	foreach($sets as $set)
	{
		$password .= $set[array_rand(str_split($set))];
		$all .= $set;
	}

	$all = str_split($all);
	for($i = 0; $i < $length - count($sets); $i++)
		$password .= $all[array_rand($all)];

	$password = str_shuffle($password);

	if(!$add_dashes)
		return $password;

	$dash_len = floor(sqrt($length));
	$dash_str = '';
	while(strlen($password) > $dash_len)
	{
		$dash_str .= substr($password, 0, $dash_len) . '-';
		$password = substr($password, $dash_len);
	}
	$dash_str .= $password;
	return $dash_str;
}

static function search($array,$key,$value)
{
    $results = array();

    if (is_array($array)) {
        if (isset($array[$key]) && $array[$key] == $value) {
            $results[] = $array;
        }

        foreach ($array as $subarray) {
            $results = array_merge($results, self::search($subarray, $key, $value));
        }
    }

    return $results;
}

}
