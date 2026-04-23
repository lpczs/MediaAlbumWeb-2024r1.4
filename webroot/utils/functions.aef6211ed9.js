// Event.composepath polyfill
(function(E, d, w) {
    if(!E.composedPath) {
      E.composedPath = function() {
        if (this.path) {
          return this.path;
        }
        var target = this.target;
        this.path = [];
        while (target.parentNode !== null) {
          this.path.push(target);
          target = target.parentNode;
        }
        this.path.push(d, w);
        return this.path;
      }
    }
  })(Event.prototype, document, window);

function correctField(fieldRef, stripSpaces)
{
    var newValue = replace(fieldRef.value, "\\", "");
    newValue = replace(newValue, "'", "");
    newValue = replace(newValue, '"', "");

    if (stripSpaces)
        newValue = replace(newValue, " ", "");

    fieldRef.value = newValue;

    return false;
}

function string2integer(integerstring)
// make sure base for conversion is 10
// otherwise 035 is read as 29
// 35oct = 29dec
{
    return parseInt(integerstring, 10)
}

function formatNumber(number, dp)
{
    var newNumber = number.toFixed(dp);

    if (newNumber.substring(0, 1) == ".")
    {
        newNumber = "0" + newNumber;
    }

    return newNumber;
}

function isNumeric(strString, allowDecimal, allowNegative)
{
    var strValidChars = "0123456789";
    var strChar;
    var blnResult = true;

    if (allowDecimal)
    {
        strValidChars = strValidChars + ".";
        strValidChars = strValidChars + ",";
    }

    if (allowNegative)
    {
        strValidChars = strValidChars + "-";
    }

    if (strString.length == 0) return false;

    for (i = 0; i < strString.length && blnResult == true; i++)
    {
        strChar = strString.charAt(i);
        if (strValidChars.indexOf(strChar) == -1)
        {
            blnResult = false;
        }
    }
    return blnResult;
}

function forceUpperCase(field)
{
    field.value = field.value.toUpperCase();

    return false;
}

function forceUpperAlphaNumeric(field)
{
    var temp = field.value;

    temp = temp.toUpperCase();
    temp = temp.replace(/[^A-Z_0-9\-]+/g, "");

    field.value = temp;
}

function forceUpperAlphaNumericMetaData(field)
{
    var temp = field.value;

    temp = temp.toUpperCase();

    field.value = temp;
}

function forceAlphaNumeric(field)
{
    var temp = field.value;

    temp = temp.replace(/[^a-zA-Z_0-9\-]+/g, "");

    field.value = temp;
}

function forceNumeric(field, allowDecimal, allowNegative)
{
    if (! isNumeric(field.value, allowDecimal, allowNegative))
    {
        field.value = "0";
    }

    return false;
}

function forceNumericOrEmpty(field, allowDecimal, allowNegative)
{
    if (! isNumeric(field.value, allowDecimal, allowNegative))
    {
        field.value = "";
    }

    return false;
}

function forceNumericOrMinValue(field, allowDecimal, allowNegative, defaultValue)
{
    if (! isNumeric(field.value, allowDecimal, allowNegative))
    {
        field.value = defaultValue;
    }
    else
    {
        var theNumber = parseFloat(field.value);
        if (theNumber < defaultValue)
        {
            field.value = defaultValue;
        }
    }

    return false;
}

function forceNumericOrValue(field, allowDecimal, allowNegative, defaultValue)
{
    if (! isNumeric(field.value, allowDecimal, allowNegative))
    {
        field.value = defaultValue;
    }

    return false;
}

function roundNumber(Num, Places)
{
    if (Places > 0)
    {
        var numAsString = Num.toString();
        if (numAsString.lastIndexOf('.') == -1)
        {
            var theNumber = parseFloat(Num);
            return theNumber.toFixed(Places);
        }
        if ((numAsString.length - numAsString.lastIndexOf('.')) > (Places + 1))
        {
            var Rounder = Math.pow(10, Places);
            var theNumber = Math.round(Num * Rounder) / Rounder;
            return theNumber.toFixed(Places);
        }
        else if ((numAsString.length - numAsString.lastIndexOf('.')) < (Places + 1))
        {
            var theNumber = parseFloat(Num);
            return theNumber.toFixed(Places);
        }
        else
            return Num;
    }
    else
        return Math.round(Num);
}

function forceDP(field, allowNegative, Places)
{
    forceNumeric(field, true, allowNegative);
    field.value = roundNumber(field.value, Places);
}

// create trim function for IE
if (typeof String.prototype.trim !== 'function')
{
    String.prototype.trim = function() {
      return this.replace(/^\s+|\s+$/g, '');
    }
}

function validateEmailAddress(pEmailAddress)
{
    var reg = /^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/g;
    pEmailAddress = pEmailAddress.trim();

    if (reg.test(pEmailAddress)){
        return true;
    } else {
        return false;
    }
}

/*Array.indexOf( value, begin, strict ) - Return index of the first element that matches value*/
function ArrayIndexOf(source, v)
{
    for (var i = 0, l = source.length; i < l; i++)
    {
        if (source[i] == v)
        {
            return i;
        }
    }
    return -1;
}

function ltrim(str, chars)
{
    chars = chars || "\\s";
    return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}

function rtrim(str, chars)
{
    chars = chars || "\\s";
    return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}

function trim(str, chars)
{
    return ltrim(rtrim(str, chars), chars);
}

function pad(number, length)
{

    var str = '' + number;
    while (str.length < length)
    {
        str = '0' + str;
    }

    return str;
}

function enterKeyPressed(e)
{
    var characterCode
    if(e && e.which)
    {           // NN4 specific code
        e = e
        characterCode = e.which
    }
    else
    {
        e = event
        characterCode = e.keyCode // IE specific code
    }
    if (characterCode == 13)
    {
        return true;   // Enter key is 13
    }
    else
    {
        return false;
    }
}

function nlToBr(pStr)
{
    return pStr.replace(/(?:\r\n|\r|\n)/g, '<br />');
}

function changeSystemLanguage(pFuseBoxAction, pFormID, pMethod)
{
    var theList = document.getElementById("systemlanguagelist");
	var theForm = document.getElementById(pFormID);

    if (theList.selectedIndex > -1)
    {
        createCookie("maweblocale", theList.options[theList.selectedIndex].value, 24 * 365);

        // Remove the CSRF token from GET submissions
        if ('GET' === pMethod.toUpperCase()) {
            var csrfTokenField = theForm.querySelector('input[name="csrf_token"]');

            if (null !== csrfTokenField) {
                csrfTokenField.parentNode.removeChild(csrfTokenField);
            }
        }

        theForm.method = pMethod;
        theForm.fsaction.value = pFuseBoxAction;
        theForm.submit();

        return false;
    }

    return true;
}

function debugObjectValues(objName, obj)
{
    var output = "";
    for (var prop in obj)
    {
        output += objName + "." + prop + " = " + obj[prop] + ", ";
    }

    alert(output);
}

function EncodeEmailPassword(pPassword)
{

    var Base64 = {

        // private property
        _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

        // public method for encoding
        encode : function (input) {
            var output = "";
            var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
            var i = 0;

            while (i < input.length) {

                chr1 = input.charCodeAt(i++);
                chr2 = input.charCodeAt(i++);
                chr3 = input.charCodeAt(i++);

                enc1 = chr1 >> 2;
                enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                enc4 = chr3 & 63;

                if (isNaN(chr2)) {
                    enc3 = enc4 = 64;
                } else if (isNaN(chr3)) {
                    enc4 = 64;
                }

                output = output +
                this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

            }

            return (output);
        }

    }
    return(Base64.encode(pPassword));
}



function getViewPortSize()
{
    var viewportwidth;
    var viewportheight;

    /*Standards compliant browsers (mozilla/netscape/opera/IE7)*/
    if (typeof window.innerWidth != 'undefined')
    {
        viewportwidth = window.innerWidth,
        viewportheight = window.innerHeight
    }

    /* IE6 */
    else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth !='undefined' && document.documentElement.clientWidth != 0)
    {
        viewportwidth = document.documentElement.clientWidth,
        viewportheight = document.documentElement.clientHeight
    }

    /* Older IE */
    else
    {
        viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
        viewportheight = document.getElementsByTagName('body')[0].clientHeight
    }

    return [viewportwidth, viewportheight];
}


function getScrollXY() {
    var scrOfX = 0, scrOfY = 0;
    if (typeof( window.pageYOffset ) == 'number')
    {
        /* Netscape compliant */
        scrOfY = window.pageYOffset;
        scrOfX = window.pageXOffset;
    }
    else
    if (document.body && ( document.body.scrollLeft || document.body.scrollTop ))
    {
        /* DOM compliant */
        scrOfY = document.body.scrollTop;
        scrOfX = document.body.scrollLeft;
    }
    else
    if ( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) )
    {
        /* IE6 standards compliant mode */
        scrOfY = document.documentElement.scrollTop;
        scrOfX = document.documentElement.scrollLeft;
    }
    return [ scrOfX, scrOfY ];
}

/**
 * return the value ccs for a style in stylesheet
 */
function getStyle(pEl, pStyleProp)
{
    var y = null;
	var x = document.getElementById(pEl);
	if (x.currentStyle)
    {
		var y = x.currentStyle[pStyleProp];
        var a = x.currentStyle;
        for (key in a) {
            // "Good enough" for most cases
//            alert(key);
        }
    }
	else if (window.getComputedStyle)
    {
		var y = document.defaultView.getComputedStyle(x,null).getPropertyValue(pStyleProp);
    }
	return y;
}

function detectionIEBrowser(pMinVersion)
{
    var browserValid = true;
    if (/MSIE (\d+\.\d+);/.test(navigator.userAgent))
    {
       var ieversion=new Number(RegExp.$1);
       if (ieversion <= pMinVersion)
       {
          browserValid = false;
       }
    }
    return browserValid;
}


// IE8 exception
if (!Array.prototype.indexOf)
{
    Array.prototype.indexOf = function(elt /*, from*/)
    {
        var len = this.length >>> 0;

        var from = Number(arguments[1]) || 0;
        from = (from < 0)
             ? Math.ceil(from)
             : Math.floor(from);
        if (from < 0)
          from += len;

        for (; from < len; from++)
        {
          if (from in this &&
              this[from] === elt)
            return from;
        }
        return -1;
    };
}

if (!Array.prototype.filter)
{
	Array.prototype.filter = function(func, thisArg)
	{
		'use strict';
		if ( ! ((typeof func === 'Function' || typeof func === 'function') && this) )
			throw new TypeError();

		var len = this.length >>> 0,
			res = new Array(len), // preallocate array
			t = this, c = 0, i = -1;

		if (thisArg === undefined)
		{
			while (++i !== len)
			{
				// checks to see if the key was set
				if (i in this)
				{
					if (func(t[i], i, t))
					{
						res[c++] = t[i];
					}
				}
			}
		}
		else
		{
			while (++i !== len)
			{
				// checks to see if the key was set
				if (i in this)
				{
					if (func.call(thisArg, t[i], i, t))
					{
						res[c++] = t[i];
					}
				}
			}
		}

		res.length = c; // shrink down array to proper size
		return res;
	};
}

function CJKHalfWidthFullWidthToASCII(pFormField, pForceUpperCase)
{
	var inputString = pFormField.value;

	var CJKArray = ["　","！","＂","＃","＄","％","＆","＇","（","）","＊","＋","，","、","､","－","．","。","｡",
				"／","０","１","２","３","４","５","６","７","８","９","：","；","＜","〈","＝","＞","〉",
				"？","＠","Ａ","Ｂ","Ｃ","Ｄ","Ｅ","Ｆ","Ｇ","Ｈ","Ｉ","Ｊ","Ｋ","Ｌ","Ｍ","Ｎ","Ｏ","Ｐ",
				"Ｑ","Ｒ","Ｓ","Ｔ","Ｕ","Ｖ","Ｗ","Ｘ","Ｙ","Ｚ","［","＼","］","＾","＿","｀","ａ","ｂ",
				"ｃ","ｄ","ｅ","ｆ","ｇ","ｈ","ｉ","ｊ","ｋ","ｌ","ｍ","ｎ","ｏ","ｐ","ｑ","ｒ","ｓ","ｔ",
				"ｕ","ｖ","ｗ","ｘ","ｙ","ｚ","｛","｜","｝","～"];

	var ASCIIArray = [" ","!","\"","#","$","%","&","'","(",")","*","+",",",",",",","-",".",".",".",
				  "/","0","1","2","3","4","5","6","7","8","9",":",";","<","<","=",">",">",
				  "?","@","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P",
				  "Q","R","S","T","U","V","W","X","Y","Z","[","\\","]","^","_","`","a","b",
				  "c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t",
				  "u","v","w","x","y","z","{","|","}","~"];

	for (var i = 0; i < inputString.length; i++)
	{
	  foundPosition = 0;

	  var foundPosition = CJKArray.indexOf(inputString.charAt(i));

	  if (foundPosition != -1)
	  {
		inputString = inputString.replace(CJKArray[foundPosition], ASCIIArray[foundPosition]);
	  }

	}

	if (pForceUpperCase)
	{
		inputString = inputString.toUpperCase();
	}

	document.getElementById(pFormField.id).value = inputString;
}

function htmlDecode(input)
{
    var e = document.createElement('div');
    e.innerHTML = input;
    return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
}

function resetInvalidAddressFields(pFormName)
{	
	var i = 0;
	var aInput = document.forms[pFormName].getElementsByTagName("input");
	var aSelect = document.forms[pFormName].getElementsByTagName("select");
		
	while (element = aInput[i++])
	{
		if (element.className.indexOf('errorInput') !== -1 || element.className.indexOf('errorInput') === true)
        {
            element.className = element.className.replace(' errorInput', '');
        }
	}
	
	i = 0;
	
	while (element = aSelect[i++])
	{
		if (element.className.indexOf('errorInput') !== -1 || element.className.indexOf('errorInput') === true)
        {
            element.className = element.className.replace(' errorInput', '');
        }
	}
}

function highlightVerificationFailures(pVerify)
{
    var returnMessage = '';
    var invalidFields = JSON.parse(pVerify);

	for (var field in invalidFields)
    {
		var inputObj = document.getElementById('main' + field);

		if (!inputObj) {
			// If a text based field was not found, try to find the field as a list instead
			inputObj = document.getElementById(field + 'list');
		}

		if (inputObj)
		{
			// check if input already has this class
			if (inputObj.className.indexOf('errorInput') === -1 || inputObj.className.indexOf('errorInput') === false)
			{
				inputObj.className = inputObj.className + ' errorInput';
				returnMessage += "\n" + invalidFields[field];
			}
			gAlerts = 1;
		}
    }
    
    return returnMessage;
}

function showLoadingDialog(pTitle)
{
	document.getElementById('loadingTitle').innerHTML = pTitle;
	var loadingBoxObj = document.getElementById('loadingBox');
	var shimObj = document.getElementById('shimLoading');

	loadingBoxObj.style.display = 'block';
	if (shimObj)
	{
		shimObj.style.display = 'block';
		shimObj.style.height = document.body.offsetHeight + 'px';
		document.body.className +=' hideSelects';
	}

	loadingBoxObj.style.left = Math.round((shimObj.offsetWidth / 2) - (loadingBoxObj.offsetWidth / 2)) + 'px';
	var windowHeight = document.documentElement.clientHeight;
	loadingBoxObj.style.top = Math.round((windowHeight - loadingBoxObj.offsetHeight) / 2) + 'px';
}

function fetchCsrfToken()
{
    var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
    return csrfMeta ? csrfMeta.getAttribute('content') : null;
}

/**
 * Shows the reauthentication window if enabled. If not enabled then a confirmation dialog can be shown, otherwise defaults to executing the success callback.
 *
 * @param {object} pConfig
 * @property {int} pConfig.Ref Session ref.
 * @property {string} pConfig.reason Reason for the reauthentication request, used for logging the activity log.
 * @property {string} pConfig.title String to display as the reauthentication dialog title.
 * @property {string} pConfig.message Message to display in the reauthentication dialog.
 * @property {boolean} pConfig.showConfirm Show a confirmation dialog if gAdminAuthentificationEnabled = 0.
 * @property {string} pConfig.confirmTitle String to display as the title of the confirmation dialog.
 * @property {string} pConfig.confirmMessage Message to show in the confirmation dialog.
 * @property {string} pConfig.confirmPositiveActionLabel Label of the "ok" button.
 * @property {string} pConfig.confirmCancelActionLabel Label of the "cancel" button.
 * @property {function} pConfig.success Action to execute on successfuly authentication/confirmation/default.
 */
function showAdminReauthDialogue(pConfig)
{
	var config = extend(
	{
		ref: '',
		reason: '',
		title: '',
		message: '',
		showConfirm: false,
		confirmTitle: gLabelConfirmation,
		confirmMessage: '',
		confirmPositiveActionLabel: Ext.taopix.ReauthenticationDialog.strings.buttonOK,
		confirmCancelActionLabel: Ext.taopix.ReauthenticationDialog.strings.buttonCancel,
		success: function() {}
	}, pConfig);


	// Make sure the feature is turned on.
	if (gAdminAuthentificationEnabled)
	{
		/* Reauthenticate the logged in user to make the changes. */
		var reauthenticationDialog = new Ext.taopix.ReauthenticationDialog(
		{
			ref: config.ref,
			reason: config.reason,
			message: config.message,
			title: config.title,
			success: config.success
		});

		reauthenticationDialog.show();
	}
	else
	{
		if (config.showConfirm)
		{
			Ext.Msg.show(
			{
				title: config.confirmTitle,
				msg: config.confirmMessage,
				buttons:
				{
					ok: config.confirmPositiveActionLabel,
					cancel: config.confirmCancelActionLabel
				},
				icon: Ext.MessageBox.QUESTION,
				fn: function(pBtn)
				{
					if (pBtn === "ok")
					{
						if (typeof config.success == 'function')
						{
							config.success();
						}
					}
				}
			});
		}
		else
		{
			// If the feature is turned off just execute the callback without reauth.
			if (typeof config.success == 'function')
			{
				config.success();
			}
		}
	}
};

/*
 * Extends an object.
 *
 * @param {object} pA Object to expand.
 * @param {object} pB Object to expand from.
 * @return The extended object.
 */
function extend(pA, pB)
{
	for(var key in pB)
	{
        if(pB.hasOwnProperty(key))
		{
			pA[key] = pB[key];
		}
	}

    return pA;
};


var gDialogStatus = 'close';
function createDialog(pTitle, pContent, pClickAction, pUniqueClass, pContentClass)
{
	var contentClass = ((typeof pContentClass !== 'undefined') ? ' ' + pContentClass : '');
    var dialogContent = '<div id="dialogTop" class="dialogTop">';
    dialogContent += '<div class="dialogTitle">';
    dialogContent += pTitle;
    dialogContent +='</div>';
    dialogContent +='</div>';
    dialogContent +='<div id="dialogContent" class="dialogContent' + contentClass + '">';
    dialogContent += pContent;
    dialogContent +='</div>';

    if (typeof pUniqueClass != 'undefined')
    {
        var outerDialog = document.getElementById('dialogOuter')
        var existingClass = outerDialog.className;
        var classToAdd = 'thirdPartyPaymentScreen ' + pUniqueClass;

        if (existingClass.indexOf(classToAdd) == -1)
        {
            outerDialog.className = existingClass + ' ' + classToAdd;
        }
    }

    openDialog(dialogContent);
}


function openDialog(pContent)
{
    gDialogStatus = 'open';
    var dialogOuter = document.getElementById('dialogOuter');
    if (dialogOuter)
    {
        dialogOuter.innerHTML = pContent;
        dialogOuter.style.display = 'block';

        setDialogPosition();
    }
}

function setDialogPosition()
{
    var dialogOuter = document.getElementById('dialogOuter');
    // calculate the width of the viewport
    var viewportWidth =  Math.max(
        Math.max(document.body.offsetWidth, document.documentElement.offsetWidth),
        Math.max(document.body.clientWidth, document.documentElement.clientWidth)
    );

    if (viewportWidth < 580)
    {
        dialogOuter.style.width = (viewportWidth - 20) + 'px';
    }
    else
    {
        dialogOuter.style.width = '460px';
    }

    var windowHeight = document.documentElement.clientHeight;

    var finalPosition = (windowHeight - dialogOuter.offsetHeight) / 2;

    // make sure the popup will not be out of the page
    if (finalPosition < 10)
    {
        var dialogTop = document.getElementById('dialogTop');
        var styleDialogTop = dialogTop.currentStyle || window.getComputedStyle(dialogTop);
        
        var height = windowHeight - parseInt(styleDialogTop.height);
        height = height - (parseInt(styleDialogTop.paddingTop) + parseInt(styleDialogTop.paddingBottom));

        var dialogContent = document.getElementById('dialogContent');
        var styleDialogContent = dialogContent.currentStyle || window.getComputedStyle(dialogContent);
        height = height - (parseInt(styleDialogContent.marginTop) + parseInt(styleDialogContent.marginBottom));
        height = height - (parseInt(styleDialogContent.paddingTop) + parseInt(styleDialogContent.paddingBottom));

        var dialogBtn = document.getElementById('dialogBtn');
        if (dialogBtn)
        {
            var styleDialogBtn = dialogBtn.currentStyle || window.getComputedStyle(dialogBtn);
            height = height - parseInt(styleDialogBtn.height);
            height = height - (parseInt(styleDialogBtn.marginTop) + parseInt(styleDialogBtn.marginBottom));
        }

        dialogContent.style.height = (height - 20) + 'px';

        finalPosition = 10;
    }

    // position the dialog
    dialogOuter.style.top = Math.round(finalPosition) + 'px';
    dialogOuter.style.left = ((viewportWidth - dialogOuter.offsetWidth) / 2) + 'px';

    displayShim();
}

function displayShim()
{
    // calculate the size of the shim
    var shim = document.getElementById('shim');
    if (shim)
    {
        var docHeight =  Math.max(
            Math.max(document.body.scrollHeight, document.documentElement.scrollHeight),
            Math.max(document.body.offsetHeight, document.documentElement.offsetHeight),
            Math.max(document.body.clientHeight, document.documentElement.clientHeight)
        );
        shim.style.height = docHeight + 'px';
        shim.style.display = 'block';
    }
}

/** 
* closeDialog
*
* close the dialog box
*/
function closeDialog(e)
{
    
    gDialogStatus = 'close';
   
    //We have closed the dialog so stop any ongoing ajax requests
    gAjaxRunning = false;
    gDialogStatus = 'close';

    var shimObj = document.getElementById('shim');
    var dialogOuter = document.getElementById('dialogOuter');
    if (shimObj)
    {
        shimObj.style.display = 'none';
    }

    if (dialogOuter)
    {
        dialogOuter.innerHTML = '';

        dialogOuter.style.display = 'none';
        dialogOuter.style.top = '0px';
        dialogOuter.style.left = '0px';
    }
}

/**
 * Check the strength of the password using zxcvbn.
 * 
 * @param pPasswordConfig Configuration to use to check password.
 */
function TPXPasswordStrength(pPasswordConfig)
{
    this.score = 0;
    this.errorText = '';
    this.strengthText = '';

    this.minStrength = pPasswordConfig.minStrength;
    this.tooWeakString = pPasswordConfig.weakString;
    this.strengthResults = pPasswordConfig.strengthStrings;

    // Test the password and set the score.
    this.calculatePasswordScore = function(pPasswordString)
    {
        // Set defaults for an empty password.
        var passwordScore = 0;

        // Check a value for the password has been passed.
        if (pPasswordString != '')
        {
            // Test the password and get the zxcvbn score.
            var zxcvbnResult = zxcvbn(pPasswordString);
            passwordScore = zxcvbnResult.score;

            // Increment the score to display the correct colour progress bar and text.
            passwordScore++;
        }

        this.score = passwordScore;
    };

    // Set the text for the current score of the password.
    this.setStrengthText = function()
    {
        this.strengthText = this.strengthResults[this.score];
    };

    // Set the error message for the current score of the password.
    this.setErrorText = function()
    {
        var messageText = '';

        // Check a minimum strength has been set and the password score is acceptable.
        if ((this.minStrength > 0) && (this.score < (this.minStrength + 1)))
        {
            messageText = "\n" + this.tooWeakString;
            messageText = messageText.replace("^0", this.strengthResults[this.minStrength + 1]);
        }

        this.errorText = messageText; 
    };

    // Update the progress gauge and text used to feed back to the user.
    this.updateGauge = function(pProgressID, pTextID)
    {
        // Set the value of the prgress bar and change the text based on the score.
        document.getElementById(pProgressID).value = this.score;
        document.getElementById(pTextID).innerHTML = this.strengthText;
    };

    // Score the password and set the results.
    this.scorePassword = function(pPasswordString, pProgressID, pTextID)
    {
        this.calculatePasswordScore(pPasswordString);
        this.setStrengthText();
        this.setErrorText();

        this.updateGauge(pProgressID, pTextID);
    };

    // Get the text to display as an error if the password is not strong enough
    this.getErrorText = function()
    {
        return this.errorText;
    }
};

function parseJson(pString)
{
    var response = {};
    try {
        response = JSON.parse(pString);
    } catch (e) {
        console.log(e);
        window.location.replace(window.location.href.replace(window.location.hash, ''));
    }
    return response;
};


// Simple dialog box.
function TPXSimpleDialog(pDialogConfig)
{
    this.dialog = null;

    this.title = pDialogConfig.title;
    this.content = pDialogConfig.content;
    this.buttons = pDialogConfig.buttons;
	this.classes = (pDialogConfig.hasOwnProperty('classes') ? pDialogConfig.classes : []);
	this.contentClasses = (pDialogConfig.hasOwnProperty('contentClasses') ? pDialogConfig.contentClasses : []);

	this.defaultButtonClasses = {
	    left: [
	        'btn-white-left',
            'btn-white-middle',
            'btn-white-right'
        ],
        right: [
            'btn-green-left',
            'btn-green-middle',
            'btn-accept-right'
        ]
    }

    // Populate the content of the dialog box using the settings passed.
    this.createDialog = function()
    {
        this.dialog = document.getElementById("simpleDialog");

        if (this.dialog)
        {
            // Populate the title of the dialog.
            this.setDialogTitle();

            // Populate the content area, including the buttons
            this.setDialogContent();

			this.additionalClasses(true, 'container');
			this.additionalClasses(true, 'content');

            this.dialog = document.getElementById("simpleDialog");
        }
    };

    // Set the title area of the dialog box.
    this.setDialogTitle = function()
    {
        var titleContainer = document.getElementById('simpleDialogTitleContainer');
        var titleElm = document.getElementById('simpleDialogTitle');

        if (this.title !== '')
        {
            titleElm.innerHTML = this.title;
            titleContainer.style.display = 'block';
        }
        else
        {
            // If the dialog has no title, hide the title bar.
            titleElm.innerHTML = '';
            titleContainer.style.display = 'none';
        }
    };

    // Set the content area of the dialog box.
    this.setDialogContent = function()
    {
        var contentElm = document.getElementById('simpleDialogContentContainer');
        if(typeof this.content === 'function')
        {
            contentElm.appendChild(this.content.call(this))
        }
        else
        {
            contentElm.innerHTML = this.content;
        }

        // Create the buttons on the dialog.
        if(this.buttons.hasOwnProperty('left'))
        {
            this.setButton(this.buttons.left, 'modalButtonLeft');
        }
        else
        {
            this.setButton({}, 'modalButtonLeft');
        }

        if(this.buttons.hasOwnProperty('right'))
        {
            this.setButton(this.buttons.right, 'modalButtonRight');
        }
        else
        {
            this.setButton({}, 'modalButtonRight');
        }
    };

    // Set the action and text of the buttons on the dialog.
    this.setButton = function(pButton, pLocation)
    {
        var buttonElm = document.getElementById(pLocation);
        var buttonTextElm = document.getElementById(pLocation + 'Text');

        if (Object.keys(pButton).length)
        {
            self = this;

            buttonElm.style.display = 'block';
            buttonTextElm.innerHTML = pButton.text;

            var buttonSide = 'modalButtonLeft' === pLocation ? 'left' : 'right'
            var classes = pButton.hasOwnProperty('classes') ? pButton.classes : this.defaultButtonClasses[buttonSide];

            for (var i = 0; i < classes.length; i++)
            {
                buttonElm.children[i].className = classes[i];
            }

            if (null === buttonElm.getAttribute('data-click-attached'))
            {
                buttonElm.setAttribute('data-click-attached', 'true');
                buttonElm.addEventListener('click', function (event) {

                    // Call the function assigned to the button.
                    self.buttons[buttonSide].action.call(self, event);

                    // Allow calling function to have the opportunity to prevent default behaviour
                    if (!event.defaultPrevented) {
                        // Close the dialog.
                        self.close();
                    }

                });
            }
        }
        else
        {
            // If the button has not been set, hide the button.
            buttonElm.style.display = 'none';
            buttonTextElm.innerHTML = '';
            buttonElm.onclick = function() {};
        }
    };

	/**
	 * Adds or removes additional classes from the dialog box.
	 * 
	 * @param boolean pAdd Determines if we add or remove the classes.
	 * @param string pContainer Name of the container we are working with.
	 */
	this.additionalClasses = function(pAdd, pContainer)
	{
		var classes = this.classes;
		var htmlElement = this.dialog;

		if (pContainer === 'content') {
			classes = this.contentClasses;
			htmlElement = document.getElementById('simpleDialogContentContainer');
		}

		var classLength = classes.length;

		if (classLength > 0)
		{
			for (var i = 0; i < classLength; i++)
			{
				if (pAdd)
				{
					htmlElement.classList.add(classes[i]);
				}
				else
				{
					htmlElement.classList.remove(classes[i]);
				}
			}
		}
	};

    // Display the dialog
    this.show = function()
    {
        // Make sure the dialog has been populated and configured.
        this.createDialog();

        // display the shim to prevent clicking on objects behind the dialog.
        displayShim();

        // Set the dialog to be visible.
        this.dialog.style.display = 'block';
    };
    
    // Remove the dialog from display
    this.close = function()
    {
        // Hide the dialog box.
        this.dialog.style.display = 'none';

		// Remove any additional classes
		this.additionalClasses(false, 'container');
		this.additionalClasses(false, 'content');

        // Hide the shim.
        var shimObj = document.getElementById('shim');
        if (shimObj)
        {
            shimObj.style.display = 'none';
        }
    };

    // clear the content of the dialog
    this.clearContent = function()
    {
        var contentElm = document.getElementById('simpleDialogContentContainer');
        while(contentElm.firstChild && contentElm.removeChild(contentElm.firstChild));
    };
}

/**
 * Convert an input type password to an input type text and change the class attached to the show/hide password icon.
 *
 * @param DOMObject Icon clicked.
 * @param string pInputID Password input id.
 * */
function togglePasswordVisibility(pButton, pInputID)
{
	var input =  document.getElementById(pInputID);
	var newInputType = (input.type === "password") ? 'text' : 'password';

	// Change the icon.
	if (pButton.className.indexOf('password-show') !== -1 || pButton.className.indexOf('password-show') === true)
	{
		pButton.className = pButton.className.replace('password-show', 'password-hide');
	}
	else
	{
		pButton.className = pButton.className.replace('password-hide', 'password-show');
	}

	// Change input type and class.
	input.type = newInputType;
}

function generateDialogContent(pTitle, pContent, pHasCancel)
{
    var dialogContent = '<div id="dialogTop" class="dialogTop">';
    dialogContent += '<div class="dialogTitle"><h2 class="title-bar">';
    dialogContent += pTitle;
    dialogContent +='</h2></div>';
    dialogContent +='</div>';
    dialogContent +='<div>';
    dialogContent +='<div id="dialogContent" class="dialogContent">';
    dialogContent += pContent;
    dialogContent +='</div>';

    if (pHasCancel)
    {
        dialogContent +='<div class="btnLeftSection btnInside" id="dialogCancelBtn">';
        dialogContent +='<div class="btnAction btnCancelGrey">';
        dialogContent +='<div class="btnCrossImage">';
        dialogContent +='</div>';
        dialogContent +='</div>';
        dialogContent +='<div class="clear"></div>';
        dialogContent +='</div>';
    }

    dialogContent +='<div id="dialogBtn" class="btnRightSection btnInside">';
    dialogContent +='<div class="btnAction btnAccept">';
    dialogContent +='<div class="btnConfirmTickLeftImage">';
    dialogContent +='</div>';
    dialogContent +='</div>';
    dialogContent +='<div class="clear"></div>';
    dialogContent +='</div>';
    dialogContent +='</div>';

    return dialogContent;
}

function showConfirmDialog(pTitle, pContent, pCallback)
{
    var dialogContent = generateDialogContent(pTitle, pContent, true);

    openDialog(dialogContent);

    document.getElementById('dialogCancelBtn').addEventListener('click', function(evt) {
        closeDialog(evt);
    });

    document.getElementById('dialogBtn').addEventListener('click', pCallback);
}

function showErrorDialog(pTitle, pContent, pCallback)
{
    var dialogContent = generateDialogContent(pTitle, pContent, false);

    openDialog(dialogContent);

    document.getElementById('dialogBtn').addEventListener('click', pCallback);
}