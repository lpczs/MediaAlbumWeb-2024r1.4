<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta name="csrf-token" content="{csrf_token}" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$projectname} ({$productname})</title>

        {if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}

		<link type="text/css" rel="stylesheet" href="{$webroot}{asset file='/css/pageflip.css'}" media="screen" />
        <link rel="stylesheet" href="{$webroot}{asset file='/css/lightbox.css'}" type="text/css" media="screen" />
        <link rel="stylesheet" href="{$webroot}/utils/pageturning/galleria/themes/classic/galleria.classic.css" type="text/css" media="screen" />
		
        {include file="includes/customerinclude_large.tpl"}

		{if ($displaytype == 1) && ($previewlicensekey != '')}
            <script type="text/javascript" src="{$webroot}/utils/pageturning/pageflip5/js/jquery-1.11.1.min.js" {$nonce}></script>
            <script type="text/javascript" src="{$webroot}/utils/pageturning/pageflip5/js/pageflip5-min.js" {$nonce}></script>

		{else}
            <script type="text/javascript" src="{$webroot}/utils/jquery.js" {$nonce}></script>
            <script type="text/javascript" src="{$webroot}/utils/pageturning/galleria/galleria-1.2.5.js" {$nonce}></script>
            <script type="text/javascript" src="{$webroot}/utils/pageturning/galleria/themes/classic/galleria.classic.min.js" {$nonce}></script>
		{/if}

        <script type="text/javascript" {$nonce}>

        {if $googleanalyticscode != ''}

			{literal}
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', '{/literal}{$googleanalyticscode}{literal}', 'auto');
			ga('send', 'pageview');
			{/literal}
				
		{/if}

            //<![CDATA[
{literal}
            var previewType = {/literal}{$displaytype}{literal};
			var previewLicenseKey = "{/literal}{$previewlicensekey}{literal}";
            var session = {/literal}{$session}{literal};
            var addToAnyIntialised = false;

            var gSession = "{/literal}{$session}{literal}";
            var gSSOToken = "{/literal}{$ssotoken}{literal}";

            $(document).ready(function()
			{
                /* if preview type is photobook then show page turning */
                if ((previewType == 1) && (previewLicenseKey != ''))
                {
					var isSinglePageProject = {/literal}{$pageflipsettings.productpageformat}{literal};
					var verticalMode = {/literal}{$pageflipsettings.verticalmode}{literal};

					// make sure the size of the thumbnail is not bigger than the maximum size
					var maxWidth = 970;
					var maxHeight = 500;

					// if it's a spread project the width need to be divided by 2 because it's the width of a page
					if (! isSinglePageProject)
					{
						maxWidth = maxWidth / 2;
					}
					else
					{
						// book is not centred the same way on single page project so the width need to be changed
						if (! verticalMode)
						{
							// book is not centred the same way on single page project so the width need to be changed
							maxWidth = 400;
						}
						else
						{
							// prevent the page to be over the top of the container
							maxHeight = 300;
						}
					}

					var pageWidth =  {/literal}{$pageflipsettings.pagewidth}{literal};
					var pageHeight = {/literal}{$pageflipsettings.pageheight}{literal};
					var coverWidth = {/literal}{$pageflipsettings.coverwidth}{literal};
					var coverHeight = {/literal}{$pageflipsettings.coverheight}{literal};
					var previewVisible = {/literal}{$pageflipsettings.contentpreviewenabled}{literal};
					var previewDisplayedAtTheBottom = {/literal}{$pageflipsettings.buttonthumbnailenabled}{literal};
					var hasCover = {/literal}{$pageflipsettings.hascover}{literal};
					var ratio = 1;

					// make sure the page size is not bigger than the available space for the page flip
					
					// if the cover is not active, ignore it
					if (hasCover)
					{
						if (coverWidth > maxWidth)
						{
							ratio = maxWidth / coverWidth;
							coverWidth = maxWidth;
							coverHeight = coverHeight * ratio;
							pageWidth = pageWidth * ratio;
							pageHeight = pageHeight * ratio;
						}

						if (coverHeight > maxHeight)
						{
							ratio = maxHeight / coverHeight;
							coverHeight = maxHeight;
							coverWidth = coverWidth * ratio;
							pageWidth = pageWidth * ratio;
							pageHeight = pageHeight * ratio;
						}
					}

					if (pageWidth > maxWidth)
					{
						ratio = maxWidth / pageWidth;
						pageWidth = maxWidth;
						pageHeight = pageHeight * ratio;
						coverWidth = coverWidth * ratio;
						coverHeight = coverHeight * ratio;
					}

					if (pageHeight > maxHeight)
					{
						ratio = maxHeight / pageHeight;
						pageHeight = maxHeight;
						pageWidth = pageWidth * ratio;
						coverWidth = coverWidth * ratio;
						coverHeight = coverHeight * ratio;
					}

					// resize page and cover picture via javascript this cannot be done via CSS attributeon the object itself
					// because page flip copy the object and this will remove teh CSS porperties attached to it
					$('#pageflip .imgcover').width(coverWidth).height(coverHeight);
					$('#pageflip .imgpage').width(pageWidth).height(pageHeight);

					// create page flip settings
					var pageFlipSettings = {
						StartPage: 0,
						PageWidth: pageWidth,
						PageHeight: pageHeight,
						AlwaysOpened: {/literal}{$pageflipsettings.alwaysopened}{literal},
						ControlbarToFront: true,
						HashControl: true,
						ZoomEnabled: true,
						FullScreenEnabled: true,
						Margin: 10,
						AutoScale: {/literal}{$pageflipsettings.pagescale}{literal},
						AutoStageHeight: false,
						StartAutoFlip: {/literal}{$pageflipsettings.startautoflip}{literal},
						VerticalMode: verticalMode,
						SinglePageMode: isSinglePageProject,
						DisableSelection: true,
						FlipDuration: 500,
						AutoFlipLoop: 1,
						Copyright: "Taopix Limited",
						Key: previewLicenseKey
					}

					// margin are changed if the thumbnail are displayed at the bottom of the book
					if (previewDisplayedAtTheBottom)
					{
						$("#pageflip").addClass('thumbnailsAtTheBottom');

						pageFlipSettings.MarginTop = 30;
						pageFlipSettings.MarginBottom = 164;
					}
					else
					{
						pageFlipSettings.MarginBottom = 64;
					}

					// include cover properties only if a cover is active
					if (hasCover)
					{
						pageFlipSettings.CoverWidth = coverWidth;
						pageFlipSettings.CoverHeight = coverHeight;

						// if the cover size is different to the page size, force hard cover can be used because of a limitation in pageflip
						if ((coverWidth != pageWidth) || (coverHeight != pageHeight))
						{
							pageFlipSettings.HardCover = true
						}
						else
						{
							pageFlipSettings.HardCover = {/literal}{$pageflipsettings.hardcover}{literal};
						}
					}

					// include thumbnail properties only if thumbnail will be displayed
					if (previewVisible)
					{
						pageFlipSettings.Thumbnails = previewVisible;

						// if the thumbnail are displayed at the bottom of the book, they cannot be hidden
						if (previewDisplayedAtTheBottom)
						{
							pageFlipSettings.ThumbnailsAutoHide = 0;
							pageFlipSettings.ThumbnailsHidden = false;
						}
						else
						{
							pageFlipSettings.ThumbnailsAutoHide = 2000;
							pageFlipSettings.ThumbnailsHidden = true;
						}
						
						pageFlipSettings.ThumbnailWidth = {/literal}{$pageflipsettings.thumbnailwidth}{literal};
						pageFlipSettings.ThumbnailHeight = {/literal}{$pageflipsettings.thumbnailheight}{literal};
					}

					// initialise page flip
					var pageflip = $('#pageflip');
					var pageflipContext = pageflip.pageflip();

					var pageFlipLoadingWait = setInterval(function()
					{
						if ($('#pf-pagerin').is(':visible'))
						{
							// remove the search option from the page name
							$('#pf-pagerin').attr('disabled', 'disabled');

							// remove the title for the tumbnail because it's incorrect compare to the page name.
							$('#pf-thumbnail-container .pf-thumbnail-button').each(function()
							{
								$(this).attr('title', '');
							});

							// force the preview to be at the bottom of the book in visible
							if (previewDisplayedAtTheBottom)
							{
								// remove the option into the control bar to hide the thumbnail
								$('#b-thumbs').addClass('pf-disabled').unbind('click');
							}
							
							clearInterval(pageFlipLoadingWait);
						}
					}, 150);

					pageflip.pageflipInit(pageFlipSettings, 'book');
                }
                /* else show slideshow */
                else
                {
					var pagesArray = [];
{/literal}
    {foreach from=$pages key=pageName item=pageInfo}
		{if ($pageName != "noinsideleft") && ($pageName != "nooutsideright")}
                    pagesArray.push("{$thumbnailpath}/{$uploadref}/{$pageName}");
                    $('#pageflip').append('<img src="{$thumbnailpath}/{$uploadref}/{$pageName}.jpg" alt="Preview" />');
		{/if}
    {/foreach}
    {if $galleria == 'singleprint'}
        {literal}
                    $('#pageflip').galleria({
                        width:940,
                        height:500,
                        maxScaleRatio: 1
                    });
        {/literal}
    {else}
        {literal}
                    $('#pageflip').galleria({
                        width:940,
                        height:500
                    });
        {/literal}
    {/if}
{literal}
                    $('#pageflip').addClass('slideshow');
                }

                // Add the event listener to the share buttons.
                var shareButton = document.getElementById('shareprojectfrompreview');
                if (shareButton)
                {
                    shareButton.addEventListener('click', function() {
                        return openDialogBox(this.getAttribute("data-projectname"));
                    });
                }

                // Add the event listener to the add-2-any icons.
                var shareButton = document.getElementById('shareprojectfrompreviewA2AImage');
                if (shareButton)
                {
                    shareButton.addEventListener('click', function() {
                        return openDialogBox(this.getAttribute("data-projectname"));
                    });
                }

                // Add listener to the top order now button.
                var reorderButton = document.getElementById('reorderTopButton');
                if (reorderButton)
                {
                    reorderButton.addEventListener('click', function() {
                        return reorder();
                    });
                }            
                
                // Add listener to the bottom order now button.
                var reorderButton = document.getElementById('reorderBottomButton');
                if (reorderButton)
                {
                    reorderButton.addEventListener('click', function() {
                        return reorder();
                    });
                }

                // Add listener to the bottom order now button.
                var backButton = document.getElementById('backButton');
                if (backButton)
                {
                    backButton.addEventListener('click', function() {
                        return closeConfirmationBox();
                    });
                }

                // Add listener to the cancel share button.
                var closeShareDialog = document.getElementById('shareDialogCancel');
                if (closeShareDialog)
                {
                    closeShareDialog.addEventListener('click', function() {
                        return closeConfirmationBox();
                    });
                }

                // Add listener to the cancel share button.
                var shareByEmailButton = document.getElementById('shareByEmailBtn');
                if (shareByEmailButton)
                {
                    shareByEmailButton.addEventListener('click', function() {
                        return shareByEmail();
                    });
                }

                // Add listener to the social share radio button.
                var socialShareRadio = document.getElementById('shareMethodsSocial');
                if (socialShareRadio)
                {
                    socialShareRadio.addEventListener('click', function() {
                        changeShareMethod();
                    });
                }

                // Add listener to the share by email radio button.
                var emailShareRadio = document.getElementById('shareMethodsEmail');
                if (emailShareRadio)
                {
                    emailShareRadio.addEventListener('click', function() {
                        changeShareMethod();
                    });
                }

                // Add listener to the password protect check box.
                var passwordProtectCheck = document.getElementById('sharepassword');
                if (passwordProtectCheck)
                {
                    passwordProtectCheck.addEventListener('click', function() {
                        passwordDisplay();
                    });
                }
                
                // Add listener to the windows download link.
                var winDownloadLink = document.getElementById('download-win');
                if (winDownloadLink)
                {
                    winDownloadLink.addEventListener('click', function(pEvent) {
                        pEvent.preventDefault();
                        window.open(this.getAttribute("data-url"));

                        return false;
                    });
                }
                
                // Add listener to the mac download link.
                var macDownloadLink = document.getElementById('download-mac');
                if (macDownloadLink)
                {
                    macDownloadLink.addEventListener('click', function(pEvent) {
                        pEvent.preventDefault();
                        window.open(this.getAttribute("data-url"));

                        return false;
                    });
                }

				// Add listener to show/hide password.
				var togglePreviewPasswordElement = document.getElementById('togglepreviewpassword');
				if (togglePreviewPasswordElement)
				{
					togglePreviewPasswordElement.addEventListener('click', function() {
						togglePasswordVisibility(togglePreviewPasswordElement, 'previewPassword');
					});
				}
            });

            /* A J A X */
            /* function to create an XMLHttp Object */
            function getxmlhttp()
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

            /* function to process an XMLHttpRequest */
            function processAjax(obj, serverPage, pParams, async)
            {
            	var params = pParams;

                // add the ref and ssotoken onto the URL if it is missing
                if ((serverPage.indexOf('&ref=') == -1) || (serverPage.indexOf('?ref=') == -1))
                {
                    if (serverPage.indexOf('?') != -1)
                    {
                        serverPage += '&ref=' + gSession;
                    }
                    else
                    {
                        serverPage += '?ref=' + gSession;
                    }
                }

                if ((serverPage.indexOf('&ssotoken=') == -1) || (serverPage.indexOf('?ssotoken=') == -1))
                {
                    if (serverPage.indexOf('?') != -1)
                    {
                        serverPage += '&ssotoken=' + gSSOToken;
                    }
                    else
                    {
                        serverPage += '?ssotoken=' + gSSOToken;
                    }
                }

				// Add CSRF token to post submissions
				var csrfMeta = document.querySelector('html > head > meta[name="csrf-token"]');
				if (csrfMeta) 
				{
					var csrfToken = csrfMeta.getAttribute('content');

					if (typeof params !== 'undefined' && null !== params && params.length > 0)
					{
						params += '&csrf_token=' + csrfToken;
					}
					else
					{
						params = 'csrf_token=' + csrfToken;
					}
				}

                /* get an XMLHttpRequest object for use */
                /* make xmlhttp local so we can run simlutaneous requests */
                var xmlhttp = getxmlhttp();

                xmlhttp.open('POST', serverPage+"&dummy=" + new Date().getTime(), async);

				xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xmlhttp.onreadystatechange = function()
                {
                    if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
                    {
                        switch (obj)
                        {
                            case 'shareByEmail':
                                var shareResult = parseJson(xmlhttp.responseText);

                                var confirmationBoxTextObj = document.getElementById('confirmationBoxText');
                                if (shareResult)
                                {
                                    if (shareResult['result'] == '')
                                    {
                                        confirmationBoxTextObj.innerHTML = "{/literal}{#str_MessageEmailSent#}{literal}";
                                    }
                                    else
                                    {
                                        confirmationBoxTextObj.innerHTML = shareResult['result'] + '. ' + shareResult['resultparam'];
                                    }
                                }
                                else
                                {
                                    confirmationBoxTextObj.innerHTML = "{/literal}{#str_ErrorConnectFailure#}{literal}";
                                }
                                var buttonsHolderConfirmationObj = document.getElementById('buttonsHolderConfirmation');
                                if (buttonsHolderConfirmationObj)
                                {
                                    buttonsHolderConfirmationObj.style.display = 'block';
                                }
                                break;
                             case 'mailToLink':
                                var shareResult = parseJson(xmlhttp.responseText);
                                
                                var confirmationBoxTextObj = document.getElementById('confirmationBoxText');
                                if (shareResult)
                                {
                                    if (shareResult['result'] == '')
                                    {
                                        confirmationBoxTextObj.className = confirmationBoxTextObj.className.replace(' confirmationText', '') + ' confirmationText';
                                        confirmationBoxTextObj.innerHTML = nlToBr("{/literal}{#str_MessageCheckEmailSoftware#}{literal}");

                                        window.location.href = shareResult['resultparam'];
                                    }
                                    else
                                    {
                                        confirmationBoxTextObj.innerHTML = shareResult['result'] + '. ' + shareResult['resultparam'];
                                    }
                                }
                                else
                                {
                                    confirmationBoxTextObj.innerHTML = "{/literal}{#str_ErrorConnectFailure#}{literal}";
                                }
                                var buttonsHolderConfirmationObj = document.getElementById('buttonsHolderConfirmation');
                                if (buttonsHolderConfirmationObj)
                                {
                                    buttonsHolderConfirmationObj.style.display = 'block';
                                }
                                break;
                        }
                    }
                };
                xmlhttp.send(params);

                if (!async)
                {
                    return xmlhttp.responseText;
                }
            }

            var a2a_config = {
                num_services: 16,
                show_menu: {
                    position: "static",
                    top: "0px",
                    left: "0px"
                },
                color_link_text: "333333",
                color_link_text_hover: "333333"
            };

			a2a_config.callbacks = a2a_config.callbacks || [];
			a2a_config.callbacks.push({
                ready: reinitAddtoany,
                share: my_addtoany_onshare
            });

            function reinitAddtoany()
            {
                // we need to do it two time, one for the tab and one for the link in list
                if(document.getElementById('a2apage_EMAIL'))
                {
                    document.getElementById('a2apage_EMAIL').parentNode.removeChild(document.getElementById('a2apage_EMAIL'));
                }

                if(document.getElementById('a2apage_email'))
                {
                    document.getElementById('a2apage_email').parentNode.removeChild(document.getElementById('a2apage_email'));
                }
            }

            var reorder = function()
            {
                var form = document.getElementById('submitform');
                form.submit();

                return false;
            };

            function changeShareMethod()
            {
                var findSource = '';
                aInput = document.getElementsByName("shareMethod");
                for (var i = 0; i < aInput.length; i++)
                {
                    element = aInput[i];
                    if (element.checked) {
                        findSource = element.value;
                    }
                }
                if( findSource != '')
                {
                    if (findSource == 'email')
                    {
                        document.getElementById('shareMethods').style.display = 'none';
                        document.getElementById('shareEmail').style.display = 'block';
                        document.getElementById('shareByEmailBtn').style.display = 'block';
                    }
                    else
                    {
                        document.getElementById('shareMethods').style.display = 'block';
                        document.getElementById('shareEmail').style.display = 'none';
                        document.getElementById('shareByEmailBtn').style.display = 'none';
                    }
                }
                else
                {
                    document.getElementById('shareMethods').style.display = 'none';
                    document.getElementById('shareEmail').style.display = 'none';
                    document.getElementById('shareByEmailBtn').style.display = 'none';
                }
            }

            function closeDialogBox()
            {
                var dialogBox = document.getElementById('dialogBox');
                if (dialogBox)
                {
                    dialogBox.style.display = 'none';
                }
            }

            function openDialogBox(pProjectName)
            {
                if (addToAnyIntialised == false)
                {
                	reinitAddtoany();
                }

                /*hide panel */
                changeShareMethod();

                //change the title of the popup
                var title = "{/literal}{#str_LabelShareProject#}{literal}";
                document.getElementById('shareProjectTitle').innerHTML = title.replace('^0', pProjectName);

                var dilaogBox = document.getElementById('dialogBox');
                var shimObj = document.getElementById('shim');

                if (shimObj)
                {
                    shimObj.style.display = 'block';
                    var docHeight =  Math.max(
                        Math.max(document.body.scrollHeight, document.documentElement.scrollHeight),
                        Math.max(document.body.offsetHeight, document.documentElement.offsetHeight),
                        Math.max(document.body.clientHeight, document.documentElement.clientHeight)
                    );
                    shimObj.style.height = docHeight + 'px';
                    document.body.className +=' hideSelects';
                }

                if (dilaogBox && shimObj)
                {
                    dilaogBox.style.display = 'block';
                    dilaogBox.style.left = Math.round(shimObj.offsetWidth / 2 - dilaogBox.offsetWidth/2) + 'px';

                    var windowHeight = document.documentElement.clientHeight;
                    dilaogBox.style.top = Math.round((windowHeight - dilaogBox.offsetHeight) / 2) + 'px';
                }
                /* reset form */
                document.getElementById('popupBox2Form').reset();
                document.getElementById('shareMethodsSocial').checked = true;
                changeShareMethod();
                /* disabled password protection*/
                document.getElementById('previewPassword').setAttribute("disabled","disabled");
                document.getElementById('previewPassword').value = '';
                document.getElementById('previewPasswordcompulsory2').style.display = "none";
				document.getElementById('togglepreviewpassword').style.display = "none";
            }

            function highlight(field)
            {
                var inputObj = document.getElementById(field);
                if (inputObj)
                {
                    inputObj.className = inputObj.className + ' errorInput';
                    gAlerts = 1;
                }
            }

            function shareByEmail()
            {
                gAlerts = 0;
                var sMessageError = "{/literal}{#str_ErrorCompulsoryInformationMissing#}{literal}";

                if (!checkPassword())
                {
                    sMessageError +="\n{/literal}{#str_MessagePasswordInformation#}{literal}";
                }

                var emailTitle = document.getElementById('shareByEmailTitle').value;
                var emailRecipients = document.getElementById('shareByEmailTo').value;
                var shareByEmailText = document.getElementById('shareByEmailText').value;

                if (emailTitle == '')
                {
                    sMessageError += "\n{/literal}{#str_MessageEnterMessageTitle#}{literal}";
                    highlight("shareByEmailTitle");

                }
                if (emailRecipients == '')
                {
                    sMessageError += "\n{/literal}{#str_MessageEnterAtLeastOneEmail#}{literal}";
                    highlight("shareByEmailTo");
                }
                else
                {
                    var emailAddressArray = new Array();
                    var emailddressIsValid = true;
                    emailAddressArray = emailRecipients.split(',');
                    for (i = 0; i < emailAddressArray.length; i++)
                    {
                        recipient = emailAddressArray[i].replace(" ", "");
                        emailddressIsValid = validateEmailAddress(recipient);
                        if (!emailddressIsValid)
                        {
                            sMessageError += "\n{/literal}{#str_MessageInvalidEmail#}{literal}";
                            document.getElementById('shareByEmailTo').focus();
                            highlight("shareByEmailTo");
                        }
                    }
                }
                if (gAlerts == 0)
                {
                    var confirmationBoxObj = document.getElementById('confirmationBox');
                    var confirmationBoxTextObj = document.getElementById('confirmationBoxText');
                    var shimObj = document.getElementById('shim');

                    confirmationBoxObj.style.display = 'block';
                    confirmationBoxTextObj.style.display = 'block';

                    if (shimObj)
                    {
                        shimObj.style.zIndex = 250;
                    }

                    confirmationBoxObj.style.left = Math.round(shimObj.offsetWidth / 2 - confirmationBoxObj.offsetWidth/2) + 'px';
                    var viewportHeight = getViewPortSize()[1];
                    var scrollYOffset = getScrollXY()[1];

                    var docHeight =  Math.max(
                        Math.max(document.body.scrollHeight, document.documentElement.scrollHeight),
                        Math.max(document.body.offsetHeight, document.documentElement.offsetHeight),
                        Math.max(document.body.clientHeight, document.documentElement.clientHeight)
                    );

                    if (docHeight < scrollYOffset + confirmationBoxObj.offsetHeight)
                    {
                        confirmationBoxObj.style.top = scrollYOffset - confirmationBoxObj.offsetHeight + 20 + 'px';
                    }
                    else
                    {
                        confirmationBoxObj.style.top = scrollYOffset + (Math.round(viewportHeight / 2 - confirmationBoxObj.offsetHeight/2)) + 'px';
                    }

                    confirmationBoxTextObj.className = confirmationBoxTextObj.className.replace(' confirmationText', '') + ' confirmationText';
                    confirmationBoxTextObj.innerHTML = '<img src="{/literal}{$webroot}{literal}/images/loading_shoppingcart_v2.gif" class="loading-icon" alt="{/literal}{#str_MessageLoading#}{literal}" />' +  "{/literal}{#str_MessageSendingEmail#}{literal}";

                    var buttonsHolderConfirmationObj = document.getElementById('buttonsHolderConfirmation');
                    if (buttonsHolderConfirmationObj)
                    {
                        buttonsHolderConfirmationObj.style.display = 'none';
                    }

					var format = ((document.location.protocol != 'https:') ? 1 : 0);
                    var previewPasswordValue = '';
                    if( document.getElementById('sharepassword').checked)
                    {
                        var previewPasswordObj = document.getElementById('previewPassword');
                        if ((previewPasswordObj) && (previewPasswordObj.value != ''))
                        {
                            previewPasswordValue = (format == 0) ? previewPasswordObj.value : hex_md5(previewPasswordObj.value);
                        }
                    }

                {/literal}
                    {if $sharebyemailmethod == 1}
                        {literal}
                            // email send by control center
                            processAjax("shareByEmail", ".?fsaction=Share.shareByEmail", "orderItemId={/literal}{$orderitemid}{literal}&title="+encodeURIComponent(emailTitle) + "&recipients="+encodeURIComponent(emailRecipients) + "&message="+encodeURIComponent(shareByEmailText) + "&previewPassword="+encodeURIComponent(previewPasswordValue) + '&format=' + format, true);
                        {/literal}
                    {else}
                        {literal}
                            // mailto link
                            processAjax("mailToLink", ".?fsaction=Share.mailTo", "orderItemId={/literal}{$orderitemid}{literal}&title="+encodeURIComponent(emailTitle) + "&recipients="+encodeURIComponent(emailRecipients) + "&message="+encodeURIComponent(shareByEmailText) + "&previewPassword="+encodeURIComponent(previewPasswordValue) + '&format=' + format, true);
                        {/literal}
                    {/if}
                {literal}

                }
                else
                {
                    alert(sMessageError);
                }
            }

            function passwordDisplay()
            {
                var shimPasswordProtection = document.getElementById('shimPasswordProtection');
                if ((document.getElementById('sharepassword').checked))
                {
                    document.getElementById('previewPassword').disabled = false;
                    document.getElementById('previewPasswordcompulsory2').style.display = "inline-block";
					document.getElementById('togglepreviewpassword').style.display = "block";
                }
                else
                {
                    document.getElementById('previewPassword').setAttribute("disabled","disabled");
					document.getElementById('previewPassword').value = '';
                    document.getElementById('previewPasswordcompulsory2').style.display = "none";
					document.getElementById('togglepreviewpassword').style.display = "none";
                    document.getElementById('previewPassword').className = document.getElementById('previewPassword').className.replace(" errorInput", "");
                }
            }

            function closeConfirmationBox()
            {
                var shimObj = document.getElementById('shim');
                var dialogBoxObj = document.getElementById('dialogBox');
                var confirmationBoxObj = document.getElementById('confirmationBox');
                if (shimObj)
                {
                    shimObj.style.display = 'none';
                    shimObj.style.zIndex = 100;
                }
                if (dialogBoxObj)
                {
                    dialogBoxObj.style.display = 'none';
                }
                if (confirmationBoxObj)
                {
                    confirmationBoxObj.style.display = 'none';
                }
                document.body.className = document.body.className.replace(' hideSelects', '');
            }

            function my_addtoany_onshare(pData)
            {
                if (!checkPassword())
                {
                    alert("{/literal}{#str_MessagePasswordInformation#}{literal}");
                    return{
                        stop: true
                    };
                }
                else
                {
					var format = ((document.location.protocol != 'https:') ? 1 : 0);
                    var previewPasswordValue = '';
                    if( document.getElementById('sharepassword').checked)
                    {
                        var previewPasswordObj = document.getElementById('previewPassword');
                        if ((previewPasswordObj) && (previewPasswordObj.value != ''))
                        {
                            previewPasswordValue = (format == 1) ? hex_md5(previewPasswordObj.value) : previewPasswordObj.value;
                        }
                    }

                    closeConfirmationBox();

                    var newURl = processAjax("shareurl",".?fsaction=Share.shareAddToAny","orderItemId={/literal}{$orderitemid}{literal}&method=" + encodeURIComponent(pData.service) + "&previewPassword="+ encodeURIComponent(previewPasswordValue) + '&format=' + format, false);

                    return {
                        url: newURl,
                        title: "{/literal}{$projectname} ({$webbrandapplicationname}){literal}"
                    }
                }
            }

            function checkPassword()
            {
                if( document.getElementById('sharepassword').checked)
                {
                    var previewPasswordObj = document.getElementById('previewPassword');

                    if ((previewPasswordObj) && (previewPasswordObj.value == ''))
					{
						highlight("previewPassword");
						return false;
					}
                }
                return true;
            }
{/literal}
            //]]>
        </script>
    </head>
    <body>
<!-- Send to a friend windows -->
{if (($previewowner == 0) or ($ordersource == 1)) and $result == ''}
        <div id="shim">&nbsp;</div>
        <div id="confirmationBox" class="section">
            <div class="dialogTop">
                <h2 class="title-bar">{#str_LabelConfirmation#}</h2>
            </div>
            <div class="content confirmationBoxContent">
                <div id="confirmationBoxText" class="message"></div>
                <div class="buttonBottomInside btnRight" id="buttonsHolderConfirmation">
                    <div class="contentBtn" id="backButton">
                        <div class="btn-green-left" ></div>
                        <div class="btn-accept-right"></div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div id="dialogBox" class="section">
            <div class="dialogTop">
                <h2 class="title-bar" id="shareProjectTitle"></h2>
            </div>
            <div class="content">
                <div class="lessPaddingTop" id="shareMethodsTitle">
                    <h2 class="title-bar-inside">
                        {#str_MessageHowWouldYouLikeToShare#}
                    </h2>
                    <div class="shareContent">
                        <div id="shareMethodsHolder" class="shareContentLeft">
                            <input type="radio" name="shareMethod" id="shareMethodsSocial" checked="checked" value="social" />
                            <label for="shareMethodsSocial">
                                <img src="{$webroot}/images/icons/share_via_social.png" alt="Social Media" />
                            </label><br /><br />
                            {if $sharebyemailmethod > 0}
                            <input type="radio" name="shareMethod" id="shareMethodsEmail" value="email"/>
                            <label for="shareMethodsEmail">
                                <img src="{$webroot}/images/icons/share_via_email.png" alt="Email" />
                            </label>
                            {/if}
                        </div>
                        <div id="prefiewPasswordHolder" class="shareContentRight">
                            <div class="passwordProtectionCheckBoxBloc">
                                <input type="checkbox" id="sharepassword" name="sharepassword" />
                                <label for="sharepassword">
                                    {#str_LabelSharePasswordProtection#}
                                </label>
                            </div>
                            <div class="passwordProtectionBloc">
                                <label for="previewPassword">
                                    {#str_LabelSharePassword#}:
                                </label>
                                <img id="previewPasswordcompulsory2" class="imgMessage" src="{$brandroot}/images/asterisk.png" alt="*"/>
                                <div class="password-input-wrap">
                                    <div class="password-background">
                                        <input id="previewPassword" name="previewPassword" type="password" disabled="disabled" />
                                        <button type="button" id="togglepreviewpassword" class="password-visibility password-show"></button>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div id="shareMethods">
                    <div class="lessPaddingTop">
                        <h2 class="title-bar-inside">
                            {#str_MessageSelectAService#}
                        </h2>
                        <div id="a2a_menu_container">
                            <div class="clear"></div>
                        </div>
                        <script type="text/javascript" src="{$serverprotocol}static.addtoany.com/menu/page.js" {$nonce}></script>
                    </div>
                </div>
                <div id="shareEmail">
                    <div class="lessPaddingTop shareBlocEmail">
                        <h2 class="title-bar-inside">
                            {#str_LabelByEmail#}
                        </h2>
                        <form id="popupBox2Form" method="post" action="#" >
                            <div>
                                <label for="shareByEmailTitle">
                                    {#str_LabelMessageTitle#}
                                </label>
                                <img src="{$brandroot}/images/asterisk.png" alt="*"/>
                                <input type="text" id="shareByEmailTitle" name="shareByEmailTitle"/>
                                <div class="clear"></div>
                            </div>
                            {if $sharebyemailmethod == 1}
                                <div class="top_gap">
                                    <label for="shareByEmailTo">
                                        {#str_LabelShareWithEmails#}
                                    </label>
                                    <img src="{$brandroot}/images/asterisk.png" alt="*"/>
                                    <textarea id="shareByEmailTo" name="shareByEmailTo" cols="50" rows="2" class="shareByEmailToTextarea"></textarea>
                                    <div class="clear"></div>
                                </div>
                                <div class="top_gap">
                                    <label for="shareByEmailText">
                                        {#str_LabelShareMessageText#}
                                    </label>
                                    <div class="gap-label-mandatory"></div>
                                    <textarea id="shareByEmailText" cols="50" rows="5" class="shareByEmailTextTextarea"></textarea>
                                    <div class="clear"></div>
                                </div>
                            {else}
                                <div class="top_gap">
                                    <label for="shareByEmailTo">
                                        {#str_LabelShareWithEmail#}
                                    </label>
                                    <img src="{$brandroot}/images/asterisk.png" alt="*"/>
                                    <input type="text" id="shareByEmailTo" class="shareByEmailToInput"/>
                                    <div class="clear"></div>
                                </div>
                                <div class="top_gap">
                                    <label for="shareByEmailText">
                                        {#str_LabelShareMessageText#}
                                    </label>
                                    <div class="gap-label-mandatory"></div>
                                    <textarea id="shareByEmailText" class="shareByEmailTextInput" cols="50" rows="5"></textarea>
                                    <div class="clear"></div>
                                </div>
                            {/if}
                        </form>
                        <div class="note">
                            <img src="{$brandroot}/images/asterisk.png" alt="*" />
                            {#str_LabelCompulsoryFields#}
                        </div>
                    </div>
                </div>
                <div class="buttonShare">
                    <div class="btnLeft">
                        <div class="contentBtn" id="shareDialogCancel">
                            <div class="btn-red-cross-left" ></div>
                            <div class="btn-red-middle">{#str_ButtonCancel#}</div>
                            <div class="btn-red-right"></div>
                        </div>
                    </div>
                    <div class="btnRight">
                        <div class="contentBtn" id="shareByEmailBtn">
                            <div class="btn-green-left" ></div>
                            <div class="btn-green-middle">{#str_LabelShare#}</div>
                            <div class="btn-accept-right"></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
{/if}
<!-- End send to a friend -->

        <div id="outerPage" class="outer-page{if $sidebarleft != ''} fullsize-outer-page{/if} outerpagePreview">
            <div id="header" class="headertop headerScroll">
                <div class="headerinside">
                    {include file="$header"}
                </div>
            </div>
 {if (! $ordercancelled) && ($temporder == 0)}
    {if (($canOrder == 1) or ($previewowner == 0))}
            <div class="contentNavigation">
        {if ($canOrder == 1)}
                <div class="btnRight">
                    <div class="contentBtn" id="reorderTopButton">
                        <div class="btn-green-left" ></div>
                        <div class="btn-green-middle">{#str_LabelOrderBookNow#}</div>
                        <div class="btn-accept-right"></div>
                    </div>
                </div>
        {/if}
        {if ($previewowner == 0)}
                <div class="btnRight">
                    <div id="shareprojectfrompreview" data-projectname="{$projectname|escape}">
                        <div class="btn-blue-left" ></div>
                        <div class="btn-blue-middle">{$staractionlabel}</div>
                        <div class="btn-blue-share-right"></div>
                    </div>
                </div>
        {/if}
                <div class="clear"></div>
            </div>
        <div class="contentScrollPreview">
    {else}
          {if ($ordersource == 1) && ($previewowner == -1)}
			  <div class="contentNavigation">
				<div class="btnRight">
						<div id="shareprojectfrompreview" data-projectname="{$projectname|escape}">
							<div class="btn-blue-left" ></div>
							<div class="btn-blue-middle">{$staractionlabel}</div>
							<div class="btn-blue-share-right"></div>
						</div>
				</div>
				<div class="clear"></div>
			  </div>
			   <div class="contentScrollPreview">
        {else}
        	<div class="contentScroll">
        {/if}
    {/if}
{else}
        <div class="contentScroll">
{/if}


{if $sidebarleft != ''}
    {include file="$sidebarleft"}
{/if}

                <div>
                    <div id="pageFooterHolder" {if ($sidebaraccount == '') && ($sidebarcontactdetails == '')}class="fullsizepage"{/if}>
                        <div id="page" class="section">
                            <h2 class="title-bar">{$projectname} ({$productname})</h2>
                            <div class="content">
{if $result == ''}
    {if ($canOrder == 0 and $previewowner == 1 and $ordersource != 1)}
                                <p>
                                    {#str_MessagePreviewExpire#}
                                </p>
	{else}
                                <div id="saleMessage">
                                    {$promomessagelabel}
                                </div>
    {/if}
    {if $displaytype == 4}
                                <iframe id="tframe" style="width:990px; height:500px; border:0;" scrolling="no" seamless="seamless" src="{$externalpreviewurl}" >
								</iframe>
    {else}
								<div id="pageflip">

			{if ($displaytype == 1) && ($previewlicensekey != '')}

				{foreach from=$pages key=pageName item=pageInfo name=pagedata}

					{if ($pageName == "fcfr") || ($pageName == "fcbk") || ($pageName == "fc")  || ($pageName == "fcsp") || ($pageName == "fcff") || ($pageName == "fcbf") || ($pageName == "bc")}

									<div class="cover" data-thumbnail-image="{$thumbnailpath}/{$uploadref|escape:'url'}/{$pageName|escape:'url'}.jpg" data-page-name="{$pageInfo.pagename}" data-page-label="{$pageName}" data-transparent-page="true">
										<img class="imgcover" src="{$thumbnailpath}/{$uploadref|escape:'url'}/{$pageName|escape:'url'}.jpg" alt="Preview" />
									</div>

					<!-- if it's none accessible page just use the default picture -->
					{elseif ($pageName == "noinsideleft") || ($pageName == "nooutsideright")}


									<div class="page" data-thumbnail-image="/images/Thumbnails/blank.png" data-page-number="0">
										<img class="imgcover" src="/images/Thumbnails/blank.png" alt="Preview" />
									</div>


					<!-- if it's a spread page the second page name is empty so for the page number to be 0 this will prevent the title to be displayed -->
					{elseif $pageInfo.pagename == ''}

									<div class="page" data-thumbnail-image="{$thumbnailpath}/{$uploadref|escape:'url'}/{$pageName|escape:'url'}.jpg" data-page-number="0">
										<img class="imgpage" src="{$thumbnailpath}/{$uploadref|escape:'url'}/{$pageName|escape:'url'}.jpg" alt="Preview" />
									</div>

					{else}

									<div class="page" data-thumbnail-image="{$thumbnailpath}/{$uploadref|escape:'url'}/{$pageName|escape:'url'}.jpg" data-page-name="{$pageInfo.pagename}">
										<img class="imgpage" src="{$thumbnailpath}/{$uploadref|escape:'url'}/{$pageName|escape:'url'}.jpg" alt="Preview" />
									</div>

					{/if}

				{/foreach}

				{include file="includes/pageflip_controlbar.tpl"}

			{/if}

								</div>
    {/if}
                        </div>
                    </div>
                    <div class="previewBoxLeft">

    {if $macdownloadurl != '' or $win32downloadurl != '' }
                        <div class="top_gap">
                        {#str_MessagePreviewOurSoftware#}
                            <ul class="ulPreview">
        {if $win32downloadurl != '' }
                                <li>
                                    <span class="previewSoftwareText">{#str_MessagePreviewDownloadWindows#}</span>
                                    <a href="" id="download-win" data-url="{$win32downloadurl}" class="linkGreen">
                                        {#str_LabelDownload#}
                                    </a>
                                </li>
        {/if}
        {if $macdownloadurl != '' }
                                <li>
                                    <span class="previewSoftwareText">{#str_MessagePreviewDownloadMac#}</span>
                                    <a href="" id="download-mac" data-url="{$macdownloadurl}" class="linkGreen">
                                        {#str_LabelDownload#}
                                    </a>
                                </li>
        {/if}
                            </ul>
                        </div>
    {/if}
    {if (($previewowner == 0) && ($temporder == 0))}
                        <div id="addtoany_container">
                            <img class="pointer" src="{$brandroot}/images/icons/share_icons.png" alt="{#str_LabelShare#}" id="shareprojectfrompreviewA2AImage" data-projectname="{$projectname|escape}" />
                        </div>
    {/if}
{else}
                        <p>
                            {$resultparam}
                        </p>
{/if}
                    </div>
                    <div class="previewBoxRight">
{if !$ordercancelled}
        {if $canOrder == 1}
                        <div class="buttonBottomInside">
                            <div class="contentBtn" id="reorderBottomButton">
                                <div class="btn-green-left" ></div>
                                <div class="btn-green-middle">{#str_LabelOrderBookNow#}</div>
                                <div class="btn-accept-right"></div>
                            </div>
                        </div>
                        <div class="clear"></div>
        {/if}
    {/if}
                    </div>
                    <div class="clear"></div>
{if $sidebaradditionalinfo != ''}
                    {include file="$sidebaradditionalinfo"}
{/if}

{if $sidebaraccount != '' or $sidebarcontactdetails != ''}
                    <div class="side-outer-panel">
    {if $sidebaraccount != ''}
                            {include file="$sidebaraccount"}
    {/if}
    {if $sidebarcontactdetails != ''}
                            {include file="$sidebarcontactdetails"}
    {/if}
                    </div>
{/if}
                </div>
            </div>
        </div>
        <form id="submitform" name="submitform" method="post" action="#" accept-charset="utf-8">
            <input type="hidden" id="ref" name="ref" value="{$session}" />
            <input type="hidden" id="fsaction" name="fsaction" value="Share.reorder" />
            <input type="hidden" id="ssotoken" name="ssotoken" value="{$ssotoken}" />
            <input type="hidden" id="ref2" name="ref2" value="{$ref2}" />
            <input type="hidden" id="method" name="method" value="{$method}" />
            <input type="hidden" id="orderitemid" name="orderitemid" value="{$orderitemid}" />
            <input type="hidden" id="recipient" name="recipient" value="{$recipient}" />
            <input type="hidden" id="action" name="action" value="{$reorderaction}" />
            <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
        </form>
    </body>
</html>