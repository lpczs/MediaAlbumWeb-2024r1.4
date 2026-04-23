<html>
<head>
<script language="javascript"> 
	function makeDevCookie()
	{
		var screenWidth = parseInt(screen.width)/100;
		var screenHeight = parseInt(screen.height)/7;
		var screenAvailableWidth = parseInt(screen.availWidth)/5;
		var screenAvailableHeight = parseInt(screen.availHeight)/5;
		var pixRatio = 1; 
		
		if('deviceXDPI' in screen)
		{ 
			pixRatio = screen.deviceXDPI / screen.logicalXDPI;
		} 
		else if (window.hasOwnProperty('devicePixelRatio'))
		{
			pixRatio = window.devicePixelRatio;
		}
		
		if(isNaN(pixRatio))
		{
			pixRatio = 1;
		}

		pixRatio = pixRatio *3;

		var the_cookie = "v1s"+ screenWidth +"o"+ screenHeight + "o" + screenAvailableWidth +"o"+ screenAvailableHeight +"o"+ pixRatio +"d"; 

		//Is it a touch
		var touchType = 0; 
		//Is it a mobile
		var mobileType = 0;
		//What is the device
		var deviceType = 0;

		if ("ontouchstart" in window || navigator.msMaxTouchPoints)
		{
			touchType = 1;
		} 

		if(navigator.userAgent.match(/Mobile| mobile/i))
		{
			mobileType = 1;
		}

		//Apple
		if(navigator.userAgent.match(/Intel Mac/i))
		{
			mobileType = touchType;
			deviceType = 1;
		}

		if(navigator.userAgent.match(/iPad/i))
		{
			mobileType = 1;
			deviceType = 2;
		}

		if(navigator.userAgent.match(/iPhone/i))
		{
			mobileType = 1;
			deviceType = 3;
		}

		if(navigator.userAgent.match(/iPod/i))
		{
			mobileType = 1;
			deviceType = 4;
		}

		//Android
		if(navigator.userAgent.match(/Android/i) && navigator.userAgent.match(/Mobile| mobile/i))
		{
			mobileType = 1;
			deviceType = 5;
		}

		if(navigator.userAgent.match(/Android/i) && !navigator.userAgent.match(/Mobile| mobile/i))
		{
			mobileType = 0;
			deviceType = 6;
		}

		//Windows
		if(navigator.userAgent.match(/(Windows)/i))
		{
			mobileType = 0;
			deviceType = 7;
		}

		if(navigator.userAgent.match(/(Windows Phone OS|Windows CE|Windows Mobile|IEMobile)/i))
		{
			mobileType = 1;
			deviceType = 8;
		}

		//Blackberry
		if(navigator.userAgent.match(/BlackBerry/i))
		{
			mobileType = 1;
			deviceType = 9;
		}

		//Palm
		if(navigator.userAgent.match(/(palm)/i))
		{
			mobileType = 1;
			deviceType = 10;
		}

		//Linux
		if(navigator.userAgent.match(/(Linux|X11)/i))
		{
			mobileType = 1;
			deviceType = 11;
		}

		//WebOS
		if(navigator.userAgent.match(/webOS/i))
		{
			mobileType = 1;
			deviceType = 12;
		}

		//Opera Mini
		if(navigator.userAgent.match(/Opera Mini/i))
		{
			mobileType = 1;
			deviceType = 13;
		}

		sumCheck = screenWidth + screenHeight + screenAvailableWidth + screenAvailableHeight + pixRatio + touchType + mobileType + deviceType;
		the_cookie = the_cookie + touchType + "o" + mobileType+ "o" + deviceType + "o" + sumCheck;

		return(the_cookie);
	} 

	window.onload = function() 
	{ 
		var url = window.location.href;
		document.detectform.action = url;
		document.detectform.dd.value = makeDevCookie();
		document.detectform.submit();
	};
</script>
</head>
<body>
<form id="detectform" name="detectform" action="" method="POST" accept-charset="utf-8">
	<?php 
		foreach ($_POST as $name => $value){
			echo '<input type="hidden" name="' . $name . '" value="' . $value .'">';
		}
		echo '<input type="hidden" name="dd" value="">';
	?>
</form>
</body>
</html>