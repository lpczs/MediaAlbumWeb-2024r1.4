<?php
/* Smarty version 4.5.3, created on 2026-03-06 03:42:56
  from 'C:\TAOPIX\MediaAlbumWeb\templates\devicedetect.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa4d40bdbec4_49751537',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b2dbae021ea8c8b249ebcad4a3919021bb37b1ea' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\devicedetect.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aa4d40bdbec4_49751537 (Smarty_Internal_Template $_smarty_tpl) {
?>
<html>
<head>
<?php echo '<script'; ?>
 language="javascript" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
> 

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
				// iPadOS reports it's an Intel Mac so test for touch support.
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

		function createCookie(name, value, hours) 
		{
			if (hours) 
			{
				var date = new Date();
				date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
				var expires = "; expires=" + date.toGMTString();
			}
			else 
		    {
				var expires = "";
		    }
		    
			document.cookie = name + "=" + value + expires + "; path=/";
		}

		var cookString = makeDevCookie();
		createCookie("mawdd", cookString, 2);

		window.location.assign(window.location + "?dd=" + cookString);

		<?php echo '</script'; ?>
>
</head>
<body>
	Device Detection Error [13:04] please enable javascript and cookies in your browser.
</body>
</html>	
<?php }
}
