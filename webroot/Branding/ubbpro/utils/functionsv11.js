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

function changeSystemLanguage(pFuseBoxAction)
{
    var theList = document.getElementById("systemlanguagelist");

    if (theList.selectedIndex > -1)
    {
        createCookie("maweblocale", theList.options[theList.selectedIndex].value, 24 * 365);

        document.submitform.fsaction.value = pFuseBoxAction;
        document.submitform.submit();

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
