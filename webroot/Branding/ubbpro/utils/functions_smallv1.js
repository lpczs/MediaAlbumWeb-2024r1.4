// laoding image
var gLoadingImage = '';
var gDialogIsOpen = false;

/**
* function to animate the loading image
*
*/
(function() {
    SpriteSpinner = function(el, options){
        var img = el.children[0];
        this.interval = options.interval || 10;
        this.diameter = options.diameter || img.width;
        this.iteration = options.iteration || 20;
        this.count = 0;
        this.el = el;
        return this;
    };
    SpriteSpinner.prototype.start = function(){
        var self = this,
        count = 0,
        img = this.el.children[0];
        self.loop = setInterval(function(){
            if(count == self.iteration){
                count = 0;
            }
            img.style.top = (-self.diameter*count)+"px";
            count++;
        }, this.interval);
    };
    SpriteSpinner.prototype.stop = function(){
        clearInterval(this.loop);
    };
    document.SpriteSpinner = SpriteSpinner;
})();

/**
* toggleJs
*
* include or remove JavaScript form the head
*/
function toggleJs(pID, pScript, pInclude, pSrc, pScriptAfterLoad)
{
    var oHead = document.getElementsByTagName('HEAD').item(0);
    if (pInclude)
    {
        var contentCustomScript = document.getElementById(pID);
        if (contentCustomScript)
        {
            oHead.removeChild(document.getElementById(pID));
        }

        var oScript = document.createElement('script');
        oScript.language = 'javascript';
        oScript.type = 'text/javascript';
        oScript.defer = true;
        oScript.id = pID;
        if (pScript != '')
        {
            oScript.text = pScript;
        }
        else
        {
            var r = false;

            oScript.src = pSrc;

            oScript.onload = oScript.onreadystatechange = function() {
                if ( !r && (!this.readyState || this.readyState == 'complete') )
                {
                    r = true;
                    toggleJs('additionnaljavascript', pScriptAfterLoad, true, '');
                }
            };

        }
        oHead.appendChild(oScript);
    }
    else
    {
        oHead.removeChild(document.getElementById(pID));
    }
}

/**
* showLoadingDialog
*
* show a loading message on screen
*/
function showLoadingDialog()
{
    var headerHeight = document.getElementById('headerSmall').offsetHeight;
    var dialogLoading = document.getElementById('dialogLoading');
    if (gLoadingImage == '')
    {
        gLoadingImage = new SpriteSpinner(dialogLoading, {
            interval: 60,
            iteration: 10,
            diameter: 40
        });
    }

    //Hide the div here
    document.getElementById('loadingGif').style.display = 'none';

    dialogLoading.style.display = 'block';

    var windowHeight = document.documentElement.clientHeight;
    var finalPosition = ((windowHeight - dialogLoading.offsetHeight) / 2) + headerHeight;

    var viewportWidth =  Math.max(
        Math.max(document.body.offsetWidth, document.documentElement.offsetWidth),
        Math.max(document.body.clientWidth, document.documentElement.clientWidth)
        );

    dialogLoading.style.top = Math.round(finalPosition) + 'px';
    dialogLoading.style.left = ((viewportWidth - dialogLoading.offsetWidth) / 2) + 'px';


    var shim = document.getElementById('shimSpinner');
    if (shim)
    {
        var docHeight =  Math.max(
            Math.max(document.body.scrollHeight, document.documentElement.scrollHeight),
            Math.max(document.body.offsetHeight, document.documentElement.offsetHeight),
            Math.max(document.body.clientHeight, document.documentElement.clientHeight)
        );
        shim.style.top = headerHeight + 'px';
        shim.style.height = docHeight + 'px';
        shim.style.display = 'block';
    }

    gLoadingImage.start();
}

/**
* closeLoadingDialog
*
* close the loading message displayed on screen
*/
function closeLoadingDialog()
{
    // delay the close function
    setTimeout(function()
    {
        var shimObj = document.getElementById('shimSpinner');
        var dialogLoading = document.getElementById('dialogLoading');

        if (shimObj)
        {
            shimObj.style.display = 'none';
        }

        if (dialogLoading)
        {
            dialogLoading.style.display = 'none';
            gLoadingImage.stop();
        }
    }, 500);
}

var gDialogStatus = 'close';
function createDialog(pTitle, pContent, pClickAction)
{
    var dialogContent = '<div id="dialogTop" class="dialogTop">';
    dialogContent += '<div class="dialogTitle">';
    dialogContent += pTitle;
    dialogContent +='</div>';
    dialogContent +='</div>';
    dialogContent +='<div>';
    dialogContent +='<div id="dialogContent" class="dialogContent">';
    dialogContent += pContent;
    dialogContent +='</div>';
    dialogContent +='<div id="dialogBtn" class="btnRightSection btnInside" onclick="' + pClickAction + '">';
    dialogContent +='<div class="btnAction btnAccept">';
    dialogContent +='<div class="btnConfirmTickLeftImage">';
    dialogContent +='</div>';
    dialogContent +='</div>';
    dialogContent +='<div class="clear"></div>';
    dialogContent +='</div>';
    dialogContent +='</div>';

    openDialog(dialogContent);
}

function showConfirmDialog(pTitle, pContent, pCallBack)
{
    var dialogContent = '<div id="dialogTop" class="dialogTop">';
    dialogContent += '<div class="dialogTitle">';
    dialogContent += pTitle;
    dialogContent +='</div>';
    dialogContent +='</div>';
    dialogContent +='<div>';
    dialogContent +='<div id="dialogContent" class="dialogContent">';
    dialogContent += pContent;
    dialogContent +='</div>';
    dialogContent +='<div class="btnLeftSection btnInside" onclick="closeDialog();">';
    dialogContent +='<div class="btnAction btnCancelGrey">';
    dialogContent +='<div class="btnCrossImage">';
    dialogContent +='</div>';
    dialogContent +='</div>';
    dialogContent +='<div class="clear"></div>';
    dialogContent +='</div>';
    dialogContent +='<div id="dialogBtn" class="btnRightSection btnInside" onclick="' + pCallBack + '">';
    dialogContent +='<div class="btnAction btnAccept">';
    dialogContent +='<div class="btnConfirmTickLeftImage">';
    dialogContent +='</div>';
    dialogContent +='</div>';
    dialogContent +='<div class="clear"></div>';
    dialogContent +='</div>';
    dialogContent +='</div>';

    openDialog(dialogContent);
}

/**
* openDialog
*
* open a center dialogbox
*/
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
        dialogOuter.style.width = '580px';
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

/**
* displayShim
*
* display the background for dialog boxes
*/
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
function closeDialog()
{
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

function parseIntStyle(pWidthStyle)
{
    return Math.ceil(pWidthStyle.replace('px', ''));
}

/**
* listOptionClick
*
* Select the option clicked
*/
function listOptionClick(pObject)
{
    /* loop through all the shpping methods to see which one has been selected */
    var elem = pObject.parentNode.parentNode;
    var inputs = elem.getElementsByTagName('input');

    for (var i = 0; i < inputs.length; i++)
    {
        if (inputs[i].checked)
        {
            inputs[i].parentNode.classList.add('optionSelected');
        }
        else
        {
            inputs[i].parentNode.classList.remove('optionSelected');
        }
    }
}


/*A J A X */

/* function to create an XMLHttp Object */
function getxmlhttpSmallScreen()
{
    /* create a boolean variable to check for a valid Microsoft ActiveX instance */
    var xmlhttp = false;
    /* check if we are using Internet Explorer */
    try
    {
        /* if the Javascript version is greater then 5 */
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch (e)
    {
        /* if not, then use the older ActiveX object */
        try
        {
            /* if we are using Internet Explorer */
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        catch (e)
        {
            /* else we must be using a non-Internet Explorer browser */
            xmlhttp = false;
        }
    }

    /* if we are not using IE, create a JavaScript instance of the object */
    if (!xmlhttp && typeof XMLHttpRequest != 'undefined')
    {
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}

var gKeyboardOpen = false;
var gResizeInProgress = false;
var gKeyboardHeight = 0;
var gScreenHeight = 0;
var gActiveResizeFormID = 0;
var gCurrentResizeFormID = 0;


/**
* setScrollAreaHeight
*
* Set the height of the scroll area for main panels
*/
function setScrollAreaHeight(pScrollDiv, pNavigationDiv)
{
    // resize elments on page
    resizeFormElements(pScrollDiv);

    // size of the header
    var contentHeader = document.getElementById('headerSmall');
    var styleContentHeader = contentHeader.currentStyle || window.getComputedStyle(contentHeader);
    var height = gScreenHeight - (parseInt(styleContentHeader.height));

    // remove back button height
    if (pNavigationDiv != '')
    {
        var contentNavigation = document.getElementById(pNavigationDiv);
        var styleContentNavigation = contentNavigation.currentStyle || window.getComputedStyle(contentNavigation);
        height = height - parseInt(styleContentNavigation.height);

        // remove naviagtion padding
        if (styleContentNavigation.paddingTop != "auto")
        {
            height = height - (parseInt(styleContentNavigation.paddingTop) + parseInt(styleContentNavigation.paddingBottom));
        }

        // remove naviagtion margin
        if (styleContentNavigation.marginTop != "auto")
        {
            height = height - (parseInt(styleContentNavigation.marginTop) + parseInt(styleContentNavigation.marginBottom));
        }
    }

    var contentScroll = document.getElementById(pScrollDiv);
    var styleContentScroll = contentScroll.currentStyle || window.getComputedStyle(contentScroll);
    var oldHeight = parseInt(styleContentScroll.height);

    //reste the content visible height;
    var contentVisible = contentScroll.getElementsByClassName('contentVisible')[0];
    contentVisible.style.height = 'auto';

    // remove container padding
    if (styleContentScroll.paddingTop != "auto")
    {
        height = height - (parseInt(styleContentScroll.paddingTop) + parseInt(styleContentScroll.paddingBottom));
        oldHeight = oldHeight - (parseInt(styleContentScroll.paddingTop) + parseInt(styleContentScroll.paddingBottom));
    }

    // remove container margin
    if (styleContentScroll.marginTop != "auto")
    {
        height = height - (parseInt(styleContentScroll.marginTop) + parseInt(styleContentScroll.marginBottom));
        oldHeight = oldHeight - (parseInt(styleContentScroll.marginTop) + parseInt(styleContentScroll.marginBottom));
    }

    // test if the scroll need to be active
    var styleContentVisible = contentVisible.currentStyle || window.getComputedStyle(contentVisible);
    var currentHeight = parseInt(styleContentVisible.height);

    if (oldHeight != height)
    {
        contentScroll.style.height = height + 'px';
    }

    // fix the scroll postion if needed
    if (currentHeight > height)
    {
        contentScroll.style.overflowY = "scroll";
    }
    else
    {
        contentScroll.style.overflowY = "hidden";
    }
}

function resizeFormElements(pContainer)
{
    var container = document.getElementById(pContainer);
    var inputLength = container.getElementsByTagName("input").length;
    var selectLength = container.getElementsByTagName("select").length;
    var textareaLength = container.getElementsByTagName("textarea").length;

    if ((inputLength > 0) || (textareaLength > 0) || (selectLength > 0))
    {
        var i = 0;
        var inputObj = '';
        var styleInput = '';
        var widthInput = gOuterBoxContentBloc;

        // call a blur to remove keyboard
		document.body.scrollTop = 0;
        document.activeElement.blur();

        // loop through all inputs
        var calculatedWidth = false;
        for (i = 0; i < inputLength; i++)
        {
            inputObj = container.getElementsByTagName("input")[i];
            if ((inputObj.type == 'text') || (inputObj.type == 'password') || (inputObj.type == 'email') || (inputObj.type == 'tel'))
            {
                if (calculatedWidth == false)
                {
                    styleInput = inputObj.currentStyle || window.getComputedStyle(inputObj);
                    widthInput = widthInput - (parseIntStyle(styleInput.borderLeftWidth) + parseIntStyle(styleInput.borderRightWidth));
                    widthInput = widthInput - (parseIntStyle(styleInput.paddingLeft) + parseIntStyle(styleInput.paddingRight));

                    calculatedWidth = true;
                }
                inputObj.style.width = widthInput + 'px';
            }
        }

        // loop through all select option
        var widthSelect = gOuterBoxContentBloc;
        for (i = 0; i < selectLength; i++)
        {
            var elm = container.getElementsByTagName("select")[i];
            if (i == 0)
            {
                inputObj = container.getElementsByTagName("select")[0];
                styleInput = inputObj.currentStyle || window.getComputedStyle(inputObj);
                widthSelect = widthSelect - (parseIntStyle(styleInput.borderLeftWidth) + parseIntStyle(styleInput.borderRightWidth));
                widthSelect = widthSelect - (parseIntStyle(styleInput.paddingLeft) + parseIntStyle(styleInput.paddingRight));

                var dropDiv = inputObj.parentNode;

                if (dropDiv && (dropDiv.className == 'wizard-dropdown'))
                {
                    var dropDownHide = document.getElementById('dropDownGeneric');
                    var styleDropDiv = dropDownHide.currentStyle || window.getComputedStyle(dropDownHide);
                    widthSelect = widthSelect + parseIntStyle(styleDropDiv.width);
                }
            }
            elm.style.width = widthSelect + 'px';
        }

        // loop through all textarea
        var widthTextarea = gOuterBoxContentBloc;
        for (i = 0; i < textareaLength; i++)
        {
            elm = container.getElementsByTagName("textarea")[i];

            if (i == 0)
            {
                inputObj = container.getElementsByTagName("textarea")[0];
                styleInput = inputObj.currentStyle || window.getComputedStyle(inputObj);
                widthTextarea = widthTextarea - (parseIntStyle(styleInput.borderLeftWidth) + parseIntStyle(styleInput.borderRightWidth));
                widthTextarea = widthTextarea - (parseIntStyle(styleInput.paddingLeft) + parseIntStyle(styleInput.paddingRight));
            }
            elm.style.width = widthTextarea + 'px';
        }
    }
}

/**
* resizeApp
*
* resize the application when the device is rotated
*/
function resizeApp()
{
    var width = document.body.offsetWidth;

    // detect if the orientation of the screen have changed
    if ((gScreenWidth != width) && (gResizeInProgress == false))
    {
        // force scrollbar back to top
        document.body.scrollTop = 0;
        document.body.style.overflowY = "hidden";

        gResizeInProgress = true;
        //prompt the loading dialog
        showLoadingDialog();

        var focused = document.activeElement;

        // test if the keyborad is open
        if (gKeyboardOpen == true)
        {
            focused.blur();
			window.setScrollTop = 0;

            setTimeout(function()
            {
                resizeApplication();
                gResizeInProgress = false;
            }, 1500);
        }
        else
        {
            resizeApplication();
            gResizeInProgress = false;
        }
    }
    else
    {
        // open the keyboard
        if ((gScreenWidth == width) && (gResizeInProgress == false))
        {
            if (gScreenHeight != document.body.offsetHeight)
            {
                // if keyboard already opened make sure we revert the incrementation of the visible container
                // this mean the type of keyboard displayed changed (number, prediction, password ...)
                if (gKeyboardOpen == true)
                {
                    gKeyboardOpen = false;
                    onKeyboardAction();
                }

                // delay for keyboard action
                setTimeout(function()
                {
                    // keyboard include in the document
                    gKeyboardHeight = gScreenHeight - document.body.offsetHeight;
                    gKeyboardOpen = true;
                    onKeyboardAction();
                }, 200);
            }
            else
            {
                // delay for keyboard action
                setTimeout(function()
                {
                    gKeyboardOpen = false;
                    onKeyboardAction();
                }, 200);
            }
        }
    }
}

function setContentVisibleHeight(pContainer)
{
    var container = document.getElementById(pContainer);
    var styleContainer = container.currentStyle || window.getComputedStyle(container);
    var containerHeight = parseInt(styleContainer.height);

    var contentVisible = container.getElementsByClassName('contentVisible')[0];
    var styleContentVisible = contentVisible.currentStyle || window.getComputedStyle(contentVisible);
    var currentHeight = parseInt(styleContentVisible.height);

    var contentVisibleHeight = 0;

    // increment the size to be able to displayed the form correctly
    if (gKeyboardOpen == true)
    {
        contentVisibleHeight = currentHeight + gKeyboardHeight;
        // add keyboard height to the container
        contentVisible.style.height = contentVisibleHeight + 'px';

        // get the position of the field into the container
        var focused = document.activeElement;
        var styleFocused = focused.currentStyle || window.getComputedStyle(focused);
        var fieldClicked = parseInt(styleFocused.height) + focused.offsetTop + focused.parentNode.offsetTop;

        // height of visible part on screen
        var visibleHeight = containerHeight - gKeyboardHeight;

        var scrollTop = container.scrollTop;
        // change scroll position if the input field will be out of screen
        if ((fieldClicked - scrollTop) > visibleHeight)
        {
            container.scrollTop = fieldClicked - (visibleHeight / 2);
        }

        if (contentVisibleHeight > containerHeight)
        {
            container.style.overflowY = "scroll";
        }
        gCurrentResizeFormID = gActiveResizeFormID;
    }
    else
    {

        // make sure we still are in smae panel as the action which open the keyboard
        if (gCurrentResizeFormID == gActiveResizeFormID)
        {
            // revert size of the container
            contentVisibleHeight = currentHeight - gKeyboardHeight;
            contentVisible.style.height = contentVisibleHeight + 'px';

            if (containerHeight > contentVisibleHeight)
            {
                 container.style.overflowY = "hidden";
            }
        }
    }
}

function showLoadingNotificationBar(pText)
{
    document.getElementById('notificationText').innerHTML = pText;
    document.getElementById('notificationBar').style.marginTop = '0';
    setTimeout(function(){
        document.getElementById('notificationBar').style.marginTop = '-40px';
    }, 2500);
}

function showAlertBar(pText)
{
    document.getElementById('alertText').innerHTML = pText;
    document.getElementById('alertBar').style.marginTop = '0';
    setTimeout(function(){
        document.getElementById('alertBar').style.marginTop = '-40px';
    }, 2500);
}

function nextElementSibling( el )
{
    do {
        el = el.nextSibling
    } while ( el && el.nodeType !== 1 );

    return el;
}

function previousElementSibling( el )
{
    do {
        el = el.previousSibling
    } while ( el && el.nodeType !== 1 );

    return el;
}






