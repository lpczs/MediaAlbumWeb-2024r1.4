<?php
### Determining Device Status
## @author Matthew Partington
# Version 5 - August 18th 2014


/*Information in the url or cookie is set to the flag mawdd - media album web device data
 * The information is of the form = v1s12.8o114.28571428571429o256o154.8o6d0o1o3o547.8857142857144
 * v1 = version1
 * s section contains the screen data
 * d section contains the device data
 * o is used to separate the information within each section
 * see function cookieCheckData() below for use.
*/

class UtilsDeviceDetection
{
    //Check for the data in the URL if present sets urlstatus=true and the my_urldata in $urlDataArray then returns this.
	static function getURL_data()
	{
		$urlstatus = false;
		$my_urldata = 0;

        //The data we require should have been appened to the URL in the following manner ?mawdd= ...
		if (isset($_GET['dd']))
		{
			$my_urldata = $_GET['dd'];
			$urlstatus = true;
		}

		$urlDataArray = array(
    		'urlstatus' => $urlstatus ,
    		'my_urldata' => $my_urldata
		);

		return $urlDataArray;
	}

    //Check for the data in the Cookie if present sets urlstatus=true and the my_urldata in $urlDataArray then returns this.
	static function getCookie_data()
	{
		$cookiestatus = false;
		$cookieData = 0;

        //The data we require should have been referenced by mawdd= ...
		if (isset($_COOKIE['mawdd']))
		{
			$cookieData = $_COOKIE['mawdd'];
			$cookiestatus = true;
		}

		$cookieDataArray = array(
    		'cookiestatus' => $cookiestatus ,
    		'cookieData' => $cookieData
		);

		return $cookieDataArray;
	}

    //Check for the data in the User Agent if present sets urlstatus=true and the my_urldata in $urlDataArray then returns this.
	static function getUserAgentData()
	{
		$uastatus = false;
		$uadata = 0;

		if (isset($_SERVER['HTTP_USER_AGENT']))
		{
			$uadata = (string) $_SERVER['HTTP_USER_AGENT'];
			$uastatus = true;
		}

		$uadataArray = array(
    		'uastatus' => $uastatus ,
    		'uadata' => $uadata
		);

		return $uadataArray;
	}

    //Checks the data in the URL
	static function urlCheckData($pdataIn)
	{
		$deviceDataArrayTemp = UtilsDeviceDetection::cookieCheckData($pdataIn);

		return $deviceDataArrayTemp;
	}

    //Chceks the data in the cookie
	static function cookieCheckData($pdataIn)
	{
		//device settings
		$isActive = 0;
		$screenWidth = 0;
		$screenHeight = 0;
		$deviceTeype = 0;
		$mobileStatus = 0;
		$screenSize = 0; //Is it >=600px
		$touchScreen = 0;

		//screen data
		$screenWidth = 0;
		$screenHeight = 0;
		$devicePixels = 0;

        //Explode on the letter 'd' to get the device data in the string
		$deviceData = explode('d', $pdataIn);

        //Explode on the letter 's' to get the screen data in the string
		$screenData = explode('s', $deviceData[0]);
		$versionData = $screenData[0];
		$screenData = $screenData[1];
		$deviceData = $deviceData[1];

		//check the version number of the cookie or the url tag
		if ($versionData == 'v1')
		{
            $isActive = 1;
            
            //Explode on 'o' to get the components values of the $screenData and $deviceData
            $screenComponents = explode('o', $screenData);
			$deviceComponents = explode('o', $deviceData);
			$hashValues = array_merge($screenComponents, $deviceComponents);
			$hashTotal = array_pop($hashValues);

            //Check the hash - i.e. is the last value of the info string equal to the sum of the other values? If so then ok!!!
			if (array_sum($hashValues) == $hashTotal)
			{
				//Getting the screen data
				$screenWidth = $screenComponents[0]*100;
				$screenHeight = $screenComponents[1]* 7;
				$devicePixels = $screenComponents[4]/3;

				//Set the screen size
				if ($screenWidth >= 600)
				{
					$screenSize = 1;
				}
				else
				{
					$screenSize = 0;
				}

				//Getting the device touch data
				if ($deviceComponents[0] != '0')
				{
					$touchScreen = 1;
				}

				//Getting the device mobile status
				if ($deviceComponents[1] != '0')
				{
					$mobileStatus = 1;
				}

				//Getting the device type data
				$dTmp = $deviceComponents[2];

				if($dTmp != '0')
				{
					switch ($dTmp)
					{
					    case '1':
					        $deviceTeype = 'Intel Mac';
					        break;
					    case '2':
					        $deviceTeype = 'iPad';
					        break;
					    case '3':
					        $deviceTeype = 'iPhone';
					        break;
				        case '4':
					        $deviceTeype = 'iPod';
					        break;
					    case '5':
					        $deviceTeype = 'Android Desktop';
					        break;
					    case '6':
					        $deviceTeype = 'Android Mobile';
					        break;
					    case '7':
					        $deviceTeype = 'Windows';
					        break;
				        case '8':
					        $deviceTeype = 'Windows Phone';
					        break;
					    case '9':
					        $deviceTeype = 'Blackberry';
					        break;
					    case '10':
					        $deviceTeype = 'Palm';
					        break;
				        case '11':
					        $deviceTeype = 'Windows Phone';
					        break;
					    case '12':
					        $deviceTeype = 'webOS';
					        break;
				        case '13':
					        $deviceTeype = 'Opera Mini';
					        break;
					}
				}
			}
		}

		$deviceDataArrayTemp = array(
			'screenwidth' => $screenWidth,
			'screenheight' => $screenHeight,
			'srceenpix' => $devicePixels,
			'devicetype' => $deviceTeype,
			'ismobiledevice' => $mobileStatus,
			'touchstatus' => $touchScreen,
			'screensize' => $screenSize,
			'isactive' => $isActive
		);

		return $deviceDataArrayTemp;
	}

    //Chceks the data in the User Agent
	static function userAgentCheckData($pdataIn)
	{
		$deviceType = 0;
		$mobileStatus = 0;
		$screenSize = 0;

		//##########################################
		// Check the generic mobile status
		if(strpos($pdataIn, 'Mobile') || strpos($pdataIn, 'mobile'))
		{
			$mobileStatus = 1;
		}

		//##########################################
		// Apple device testing
		//Check to see if its an intel mac desktop
		if(strpos($pdataIn,'Intel Mac'))
		{
			$deviceType = 'Intel Mac';
			$mobileStatus = 0;
			$screenSize = 1;
		}

		//Check to see if its an iPad
		if(strpos($pdataIn,'iPad'))
		{
			$deviceType = 'iPad';
			$mobileStatus = 1;
			$screenSize = 1;
		}

		//Check to see if its an iPhone
		if(strpos($pdataIn,'iPhone'))
		{
			$deviceType = 'iPhone';
			$mobileStatus = 1;
			$screenSize = 0;
		}

		//Check to see if its an iPod
		if(strpos($pdataIn,'iPod'))
		{
			$deviceType = 'iPod';
			$mobileStatus = 1;
			$screenSize = 0;
		}

		//###############################isact###########
		// Linux device testing
		if(strpos($pdataIn,'Linux') || strpos($pdataIn,'X11'))
		{
			$deviceType = 'Linux';
			$mobileStatus = 'not';
			$screenSize = 1;
		}

		//##########################################
		// Android device testing
		if(strpos($pdataIn,'Android'))
		{
			$deviceType = 'Android';

			if(strpos($pdataIn, 'Mobile') || strpos($pdataIn, 'mobile') )
			{
				$mobileStatus = 1;
				$screenSize = 0;
			}
			else
			{
				$mobileStatus = 1;
				$screenSize = 1;
			}

		}

		//##########################################
		// Windows device testing
		if(strpos($pdataIn, 'Windows'))
		{
			$deviceType = 'Windows';
			$mobileStatus = 0;
			$screenSize = 1;

			if(strpos($pdataIn, 'Mobile') || strpos($pdataIn, 'mobile') || strpos($pdataIn, 'IEMobile') || strpos($pdataIn, 'Phone') || strpos($pdataIn, 'CE'))
			{
				$mobileStatus = 1;
				$screenSize = 0;
			}
		}

		//##########################################
		// Blackberry device testing
		if(strpos($pdataIn,'Blackberry'))
		{
			$deviceType = 'Blackberry';
			$mobileStatus = 1;
			$screenSize = 0;
		}

		//##########################################
		// Palm device testing
		if(strpos($pdataIn,'palm'))
		{
			$deviceType = 'palm';
			$mobileStatus = 1;
			$screenSize = 0;
		}

		//##########################################
		// WebOS device testing
		if(strpos($pdataIn,'webOS'))
		{
			$deviceType = 'webOS';
			$mobileStatus = 1;
			$screenSize = 0;
		}

		//##########################################
		// Opera device testing
		if(strpos($pdataIn,'Opera Mini'))
		{
			$deviceType = 'Opera Mini';
			$mobileStatus = 1;
			$screenSize = 0;
		}

        //Setting the information we have to the returned array.
		$deviceDataArrayTemp = array(
    		'screenwidth' => 0,
    		'screenheight' => 0,
    		'srceenpix' => 0,
    		'devicetype' => $deviceType,
    		'ismobiledevice' => $mobileStatus,
    		'touchstatus' => 0,
    		'screensize' => $screenSize,
    		'isactive' => 1
		);

		return $deviceDataArrayTemp;
	}

	static function determineDevice($pRequestFromLowLevelAPI)
	{
        //Define the array
        $deviceDataArray = array(
			'screenWidth' => 0,
			'screenHeight' => 0,
			'srceenPix' => 0,
			'deviceType' => 0,
			'ismobiledevice' => 0,
			'touchStatus' => 0,
			'screensize' => 0,
			'checkedBy' => 0,
			'isactive' => 0
		);

        //###################################
        // Step 1 determine if the information we need is on the url - i.e. we have been passed it by a friend.
        $urlGot = UtilsDeviceDetection::getURL_data();

        if ($urlGot['urlstatus'] == true) //True then set the variables
        {
        	$deviceDataArray = UtilsDeviceDetection::urlCheckData($urlGot['my_urldata']);
        	//Is everything ok?
        	//We need to create or update a cookie. But can move on as we have the info we want.
        }
        else if(($pRequestFromLowLevelAPI == false) && ($urlGot['urlstatus'] == false))
        {
        	//####################################
        	// Step 2 determine if the information is in the cookie - i.e. the user has been here before.
        	$cookieGot = UtilsDeviceDetection::getCookie_data();

        	if ($cookieGot['cookiestatus'] == true) //True then set the variables
        	{
        		$deviceDataArray = UtilsDeviceDetection::cookieCheckData($cookieGot['cookieData']);
        	}
        	else
        	{
        		//#####################################
        		// Step 3 determine if the information is in the user agent - i.e. the user has been here before.
        		$userAgentGot = UtilsDeviceDetection::getUserAgentData();

        		if ($userAgentGot['uastatus'] == true)
        		{
        			$deviceDataArray = UtilsDeviceDetection::userAgentCheckData($userAgentGot['uadata']);
        		}
        	}
		}
		
        return $deviceDataArray;
	}

    /*This function sends a small piece of javascript to the device which then gets the information we want
     * and returns it within the url and sets a cookie.
	*/
    static function pingJS()
	{
		global $gSession;
		$smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		$smarty->displayLocale('devicedetect.tpl');
		// we need to exit the script as the devicedetect.tpl will redirect and if we do not exit we will get errors when later displaylocales are called
		die();
	}
}

?>