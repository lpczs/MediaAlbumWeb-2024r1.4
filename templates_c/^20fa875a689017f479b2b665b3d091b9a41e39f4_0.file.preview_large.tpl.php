<?php
/* Smarty version 4.5.3, created on 2026-03-06 05:53:38
  from 'C:\TAOPIX\MediaAlbumWeb\templates\share\preview_large.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa6be2aa4622_98067741',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '20fa875a689017f479b2b665b3d091b9a41e39f4' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\share\\preview_large.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:includes/googletagmanager.tpl' => 1,
    'file:includes/customerinclude_large.tpl' => 1,
    'file:includes/pageflip_controlbar.tpl' => 1,
  ),
),false)) {
function content_69aa6be2aa4622_98067741 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.csrf_token.php','function'=>'smarty_function_csrf_token',),1=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.asset.php','function'=>'smarty_function_asset',),));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" xml:lang="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" dir="ltr">
    <head>
        <meta name="csrf-token" content="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="<?php echo $_smarty_tpl->tpl_vars['langCode']->value;?>
" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title><?php echo $_smarty_tpl->tpl_vars['projectname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['productname']->value;?>
)</title>

        <?php if ($_smarty_tpl->tpl_vars['googletagmanagercccode']->value != '') {?>
            <?php $_smarty_tpl->_subTemplateRender("file:includes/googletagmanager.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('googletagmanagercccode'=>$_smarty_tpl->tpl_vars['googletagmanagercccode']->value), 0, false);
?>
        <?php }?>

		<link type="text/css" rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/css/pageflip.css'),$_smarty_tpl);?>
" media="screen" />
        <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/css/lightbox.css'),$_smarty_tpl);?>
" type="text/css" media="screen" />
        <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/pageturning/galleria/themes/classic/galleria.classic.css" type="text/css" media="screen" />
		
        <?php $_smarty_tpl->_subTemplateRender("file:includes/customerinclude_large.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

		<?php if (($_smarty_tpl->tpl_vars['displaytype']->value == 1) && ($_smarty_tpl->tpl_vars['previewlicensekey']->value != '')) {?>
            <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/pageturning/pageflip5/js/jquery-1.11.1.min.js" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>
            <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/pageturning/pageflip5/js/pageflip5-min.js" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>

		<?php } else { ?>
            <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/jquery.js" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>
            <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/pageturning/galleria/galleria-1.2.5.js" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>
            <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/pageturning/galleria/themes/classic/galleria.classic.min.js" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>
		<?php }?>

        <?php echo '<script'; ?>
 type="text/javascript" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
>

        <?php if ($_smarty_tpl->tpl_vars['googleanalyticscode']->value != '') {?>

			
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', '<?php echo $_smarty_tpl->tpl_vars['googleanalyticscode']->value;?>
', 'auto');
			ga('send', 'pageview');
			
				
		<?php }?>

            //<![CDATA[

            var previewType = <?php echo $_smarty_tpl->tpl_vars['displaytype']->value;?>
;
			var previewLicenseKey = "<?php echo $_smarty_tpl->tpl_vars['previewlicensekey']->value;?>
";
            var session = <?php echo $_smarty_tpl->tpl_vars['session']->value;?>
;
            var addToAnyIntialised = false;

            var gSession = "<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
";
            var gSSOToken = "<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
";

            $(document).ready(function()
			{
                /* if preview type is photobook then show page turning */
                if ((previewType == 1) && (previewLicenseKey != ''))
                {
					var isSinglePageProject = <?php echo $_smarty_tpl->tpl_vars['pageflipsettings']->value['productpageformat'];?>
;
					var verticalMode = <?php echo $_smarty_tpl->tpl_vars['pageflipsettings']->value['verticalmode'];?>
;

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

					var pageWidth =  <?php echo $_smarty_tpl->tpl_vars['pageflipsettings']->value['pagewidth'];?>
;
					var pageHeight = <?php echo $_smarty_tpl->tpl_vars['pageflipsettings']->value['pageheight'];?>
;
					var coverWidth = <?php echo $_smarty_tpl->tpl_vars['pageflipsettings']->value['coverwidth'];?>
;
					var coverHeight = <?php echo $_smarty_tpl->tpl_vars['pageflipsettings']->value['coverheight'];?>
;
					var previewVisible = <?php echo $_smarty_tpl->tpl_vars['pageflipsettings']->value['contentpreviewenabled'];?>
;
					var previewDisplayedAtTheBottom = <?php echo $_smarty_tpl->tpl_vars['pageflipsettings']->value['buttonthumbnailenabled'];?>
;
					var hasCover = <?php echo $_smarty_tpl->tpl_vars['pageflipsettings']->value['hascover'];?>
;
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
						AlwaysOpened: <?php echo $_smarty_tpl->tpl_vars['pageflipsettings']->value['alwaysopened'];?>
,
						ControlbarToFront: true,
						HashControl: true,
						ZoomEnabled: true,
						FullScreenEnabled: true,
						Margin: 10,
						AutoScale: <?php echo $_smarty_tpl->tpl_vars['pageflipsettings']->value['pagescale'];?>
,
						AutoStageHeight: false,
						StartAutoFlip: <?php echo $_smarty_tpl->tpl_vars['pageflipsettings']->value['startautoflip'];?>
,
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
							pageFlipSettings.HardCover = <?php echo $_smarty_tpl->tpl_vars['pageflipsettings']->value['hardcover'];?>
;
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
						
						pageFlipSettings.ThumbnailWidth = <?php echo $_smarty_tpl->tpl_vars['pageflipsettings']->value['thumbnailwidth'];?>
;
						pageFlipSettings.ThumbnailHeight = <?php echo $_smarty_tpl->tpl_vars['pageflipsettings']->value['thumbnailheight'];?>
;
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

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['pages']->value, 'pageInfo', false, 'pageName');
$_smarty_tpl->tpl_vars['pageInfo']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['pageName']->value => $_smarty_tpl->tpl_vars['pageInfo']->value) {
$_smarty_tpl->tpl_vars['pageInfo']->do_else = false;
?>
		<?php if (($_smarty_tpl->tpl_vars['pageName']->value != "noinsideleft") && ($_smarty_tpl->tpl_vars['pageName']->value != "nooutsideright")) {?>
                    pagesArray.push("<?php echo $_smarty_tpl->tpl_vars['thumbnailpath']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['uploadref']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['pageName']->value;?>
");
                    $('#pageflip').append('<img src="<?php echo $_smarty_tpl->tpl_vars['thumbnailpath']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['uploadref']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['pageName']->value;?>
.jpg" alt="Preview" />');
		<?php }?>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    <?php if ($_smarty_tpl->tpl_vars['galleria']->value == 'singleprint') {?>
        
                    $('#pageflip').galleria({
                        width:940,
                        height:500,
                        maxScaleRatio: 1
                    });
        
    <?php } else { ?>
        
                    $('#pageflip').galleria({
                        width:940,
                        height:500
                    });
        
    <?php }?>

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
                                        confirmationBoxTextObj.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageEmailSent');?>
";
                                    }
                                    else
                                    {
                                        confirmationBoxTextObj.innerHTML = shareResult['result'] + '. ' + shareResult['resultparam'];
                                    }
                                }
                                else
                                {
                                    confirmationBoxTextObj.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorConnectFailure');?>
";
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
                                        confirmationBoxTextObj.innerHTML = nlToBr("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCheckEmailSoftware');?>
");

                                        window.location.href = shareResult['resultparam'];
                                    }
                                    else
                                    {
                                        confirmationBoxTextObj.innerHTML = shareResult['result'] + '. ' + shareResult['resultparam'];
                                    }
                                }
                                else
                                {
                                    confirmationBoxTextObj.innerHTML = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorConnectFailure');?>
";
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
                var title = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareProject');?>
";
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
                var sMessageError = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorCompulsoryInformationMissing');?>
";

                if (!checkPassword())
                {
                    sMessageError +="\n<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePasswordInformation');?>
";
                }

                var emailTitle = document.getElementById('shareByEmailTitle').value;
                var emailRecipients = document.getElementById('shareByEmailTo').value;
                var shareByEmailText = document.getElementById('shareByEmailText').value;

                if (emailTitle == '')
                {
                    sMessageError += "\n<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageEnterMessageTitle');?>
";
                    highlight("shareByEmailTitle");

                }
                if (emailRecipients == '')
                {
                    sMessageError += "\n<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageEnterAtLeastOneEmail');?>
";
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
                            sMessageError += "\n<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageInvalidEmail');?>
";
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
                    confirmationBoxTextObj.innerHTML = '<img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/loading_shoppingcart_v2.gif" class="loading-icon" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageLoading');?>
" />' +  "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSendingEmail');?>
";

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

                
                    <?php if ($_smarty_tpl->tpl_vars['sharebyemailmethod']->value == 1) {?>
                        
                            // email send by control center
                            processAjax("shareByEmail", ".?fsaction=Share.shareByEmail", "orderItemId=<?php echo $_smarty_tpl->tpl_vars['orderitemid']->value;?>
&title="+encodeURIComponent(emailTitle) + "&recipients="+encodeURIComponent(emailRecipients) + "&message="+encodeURIComponent(shareByEmailText) + "&previewPassword="+encodeURIComponent(previewPasswordValue) + '&format=' + format, true);
                        
                    <?php } else { ?>
                        
                            // mailto link
                            processAjax("mailToLink", ".?fsaction=Share.mailTo", "orderItemId=<?php echo $_smarty_tpl->tpl_vars['orderitemid']->value;?>
&title="+encodeURIComponent(emailTitle) + "&recipients="+encodeURIComponent(emailRecipients) + "&message="+encodeURIComponent(shareByEmailText) + "&previewPassword="+encodeURIComponent(previewPasswordValue) + '&format=' + format, true);
                        
                    <?php }?>
                

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
                    alert("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePasswordInformation');?>
");
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

                    var newURl = processAjax("shareurl",".?fsaction=Share.shareAddToAny","orderItemId=<?php echo $_smarty_tpl->tpl_vars['orderitemid']->value;?>
&method=" + encodeURIComponent(pData.service) + "&previewPassword="+ encodeURIComponent(previewPasswordValue) + '&format=' + format, false);

                    return {
                        url: newURl,
                        title: "<?php echo $_smarty_tpl->tpl_vars['projectname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['webbrandapplicationname']->value;?>
)"
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

            //]]>
        <?php echo '</script'; ?>
>
    </head>
    <body>
<!-- Send to a friend windows -->
<?php if ((($_smarty_tpl->tpl_vars['previewowner']->value == 0) || ($_smarty_tpl->tpl_vars['ordersource']->value == 1)) && $_smarty_tpl->tpl_vars['result']->value == '') {?>
        <div id="shim">&nbsp;</div>
        <div id="confirmationBox" class="section">
            <div class="dialogTop">
                <h2 class="title-bar"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
</h2>
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
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageHowWouldYouLikeToShare');?>

                    </h2>
                    <div class="shareContent">
                        <div id="shareMethodsHolder" class="shareContentLeft">
                            <input type="radio" name="shareMethod" id="shareMethodsSocial" checked="checked" value="social" />
                            <label for="shareMethodsSocial">
                                <img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/share_via_social.png" alt="Social Media" />
                            </label><br /><br />
                            <?php if ($_smarty_tpl->tpl_vars['sharebyemailmethod']->value > 0) {?>
                            <input type="radio" name="shareMethod" id="shareMethodsEmail" value="email"/>
                            <label for="shareMethodsEmail">
                                <img src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/images/icons/share_via_email.png" alt="Email" />
                            </label>
                            <?php }?>
                        </div>
                        <div id="prefiewPasswordHolder" class="shareContentRight">
                            <div class="passwordProtectionCheckBoxBloc">
                                <input type="checkbox" id="sharepassword" name="sharepassword" />
                                <label for="sharepassword">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSharePasswordProtection');?>

                                </label>
                            </div>
                            <div class="passwordProtectionBloc">
                                <label for="previewPassword">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSharePassword');?>
:
                                </label>
                                <img id="previewPasswordcompulsory2" class="imgMessage" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
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
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSelectAService');?>

                        </h2>
                        <div id="a2a_menu_container">
                            <div class="clear"></div>
                        </div>
                        <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['serverprotocol']->value;?>
static.addtoany.com/menu/page.js" <?php echo $_smarty_tpl->tpl_vars['nonce']->value;?>
><?php echo '</script'; ?>
>
                    </div>
                </div>
                <div id="shareEmail">
                    <div class="lessPaddingTop shareBlocEmail">
                        <h2 class="title-bar-inside">
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelByEmail');?>

                        </h2>
                        <form id="popupBox2Form" method="post" action="#" >
                            <div>
                                <label for="shareByEmailTitle">
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMessageTitle');?>

                                </label>
                                <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                                <input type="text" id="shareByEmailTitle" name="shareByEmailTitle"/>
                                <div class="clear"></div>
                            </div>
                            <?php if ($_smarty_tpl->tpl_vars['sharebyemailmethod']->value == 1) {?>
                                <div class="top_gap">
                                    <label for="shareByEmailTo">
                                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareWithEmails');?>

                                    </label>
                                    <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                                    <textarea id="shareByEmailTo" name="shareByEmailTo" cols="50" rows="2" class="shareByEmailToTextarea"></textarea>
                                    <div class="clear"></div>
                                </div>
                                <div class="top_gap">
                                    <label for="shareByEmailText">
                                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareMessageText');?>

                                    </label>
                                    <div class="gap-label-mandatory"></div>
                                    <textarea id="shareByEmailText" cols="50" rows="5" class="shareByEmailTextTextarea"></textarea>
                                    <div class="clear"></div>
                                </div>
                            <?php } else { ?>
                                <div class="top_gap">
                                    <label for="shareByEmailTo">
                                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareWithEmail');?>

                                    </label>
                                    <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*"/>
                                    <input type="text" id="shareByEmailTo" class="shareByEmailToInput"/>
                                    <div class="clear"></div>
                                </div>
                                <div class="top_gap">
                                    <label for="shareByEmailText">
                                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShareMessageText');?>

                                    </label>
                                    <div class="gap-label-mandatory"></div>
                                    <textarea id="shareByEmailText" class="shareByEmailTextInput" cols="50" rows="5"></textarea>
                                    <div class="clear"></div>
                                </div>
                            <?php }?>
                        </form>
                        <div class="note">
                            <img src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/asterisk.png" alt="*" />
                            <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompulsoryFields');?>

                        </div>
                    </div>
                </div>
                <div class="buttonShare">
                    <div class="btnLeft">
                        <div class="contentBtn" id="shareDialogCancel">
                            <div class="btn-red-cross-left" ></div>
                            <div class="btn-red-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
</div>
                            <div class="btn-red-right"></div>
                        </div>
                    </div>
                    <div class="btnRight">
                        <div class="contentBtn" id="shareByEmailBtn">
                            <div class="btn-green-left" ></div>
                            <div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShare');?>
</div>
                            <div class="btn-accept-right"></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
<?php }?>
<!-- End send to a friend -->

        <div id="outerPage" class="outer-page<?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?> fullsize-outer-page<?php }?> outerpagePreview">
            <div id="header" class="headertop headerScroll">
                <div class="headerinside">
                    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['header']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                </div>
            </div>
 <?php if ((!$_smarty_tpl->tpl_vars['ordercancelled']->value) && ($_smarty_tpl->tpl_vars['temporder']->value == 0)) {?>
    <?php if ((($_smarty_tpl->tpl_vars['canOrder']->value == 1) || ($_smarty_tpl->tpl_vars['previewowner']->value == 0))) {?>
            <div class="contentNavigation">
        <?php if (($_smarty_tpl->tpl_vars['canOrder']->value == 1)) {?>
                <div class="btnRight">
                    <div class="contentBtn" id="reorderTopButton">
                        <div class="btn-green-left" ></div>
                        <div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderBookNow');?>
</div>
                        <div class="btn-accept-right"></div>
                    </div>
                </div>
        <?php }?>
        <?php if (($_smarty_tpl->tpl_vars['previewowner']->value == 0)) {?>
                <div class="btnRight">
                    <div id="shareprojectfrompreview" data-projectname="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['projectname']->value, ENT_QUOTES, 'UTF-8', true);?>
">
                        <div class="btn-blue-left" ></div>
                        <div class="btn-blue-middle"><?php echo $_smarty_tpl->tpl_vars['staractionlabel']->value;?>
</div>
                        <div class="btn-blue-share-right"></div>
                    </div>
                </div>
        <?php }?>
                <div class="clear"></div>
            </div>
        <div class="contentScrollPreview">
    <?php } else { ?>
          <?php if (($_smarty_tpl->tpl_vars['ordersource']->value == 1) && ($_smarty_tpl->tpl_vars['previewowner']->value == -1)) {?>
			  <div class="contentNavigation">
				<div class="btnRight">
						<div id="shareprojectfrompreview" data-projectname="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['projectname']->value, ENT_QUOTES, 'UTF-8', true);?>
">
							<div class="btn-blue-left" ></div>
							<div class="btn-blue-middle"><?php echo $_smarty_tpl->tpl_vars['staractionlabel']->value;?>
</div>
							<div class="btn-blue-share-right"></div>
						</div>
				</div>
				<div class="clear"></div>
			  </div>
			   <div class="contentScrollPreview">
        <?php } else { ?>
        	<div class="contentScroll">
        <?php }?>
    <?php }
} else { ?>
        <div class="contentScroll">
<?php }?>


<?php if ($_smarty_tpl->tpl_vars['sidebarleft']->value != '') {?>
    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebarleft']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}?>

                <div>
                    <div id="pageFooterHolder" <?php if (($_smarty_tpl->tpl_vars['sidebaraccount']->value == '') && ($_smarty_tpl->tpl_vars['sidebarcontactdetails']->value == '')) {?>class="fullsizepage"<?php }?>>
                        <div id="page" class="section">
                            <h2 class="title-bar"><?php echo $_smarty_tpl->tpl_vars['projectname']->value;?>
 (<?php echo $_smarty_tpl->tpl_vars['productname']->value;?>
)</h2>
                            <div class="content">
<?php if ($_smarty_tpl->tpl_vars['result']->value == '') {?>
    <?php if (($_smarty_tpl->tpl_vars['canOrder']->value == 0 && $_smarty_tpl->tpl_vars['previewowner']->value == 1 && $_smarty_tpl->tpl_vars['ordersource']->value != 1)) {?>
                                <p>
                                    <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePreviewExpire');?>

                                </p>
	<?php } else { ?>
                                <div id="saleMessage">
                                    <?php echo $_smarty_tpl->tpl_vars['promomessagelabel']->value;?>

                                </div>
    <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['displaytype']->value == 4) {?>
                                <iframe id="tframe" style="width:990px; height:500px; border:0;" scrolling="no" seamless="seamless" src="<?php echo $_smarty_tpl->tpl_vars['externalpreviewurl']->value;?>
" >
								</iframe>
    <?php } else { ?>
								<div id="pageflip">

			<?php if (($_smarty_tpl->tpl_vars['displaytype']->value == 1) && ($_smarty_tpl->tpl_vars['previewlicensekey']->value != '')) {?>

				<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['pages']->value, 'pageInfo', false, 'pageName', 'pagedata', array (
));
$_smarty_tpl->tpl_vars['pageInfo']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['pageName']->value => $_smarty_tpl->tpl_vars['pageInfo']->value) {
$_smarty_tpl->tpl_vars['pageInfo']->do_else = false;
?>

					<?php if (($_smarty_tpl->tpl_vars['pageName']->value == "fcfr") || ($_smarty_tpl->tpl_vars['pageName']->value == "fcbk") || ($_smarty_tpl->tpl_vars['pageName']->value == "fc") || ($_smarty_tpl->tpl_vars['pageName']->value == "fcsp") || ($_smarty_tpl->tpl_vars['pageName']->value == "fcff") || ($_smarty_tpl->tpl_vars['pageName']->value == "fcbf") || ($_smarty_tpl->tpl_vars['pageName']->value == "bc")) {?>

									<div class="cover" data-thumbnail-image="<?php echo $_smarty_tpl->tpl_vars['thumbnailpath']->value;?>
/<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['uploadref']->value);?>
/<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['pageName']->value);?>
.jpg" data-page-name="<?php echo $_smarty_tpl->tpl_vars['pageInfo']->value['pagename'];?>
" data-page-label="<?php echo $_smarty_tpl->tpl_vars['pageName']->value;?>
" data-transparent-page="true">
										<img class="imgcover" src="<?php echo $_smarty_tpl->tpl_vars['thumbnailpath']->value;?>
/<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['uploadref']->value);?>
/<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['pageName']->value);?>
.jpg" alt="Preview" />
									</div>

					<!-- if it's none accessible page just use the default picture -->
					<?php } elseif (($_smarty_tpl->tpl_vars['pageName']->value == "noinsideleft") || ($_smarty_tpl->tpl_vars['pageName']->value == "nooutsideright")) {?>


									<div class="page" data-thumbnail-image="/images/Thumbnails/blank.png" data-page-number="0">
										<img class="imgcover" src="/images/Thumbnails/blank.png" alt="Preview" />
									</div>


					<!-- if it's a spread page the second page name is empty so for the page number to be 0 this will prevent the title to be displayed -->
					<?php } elseif ($_smarty_tpl->tpl_vars['pageInfo']->value['pagename'] == '') {?>

									<div class="page" data-thumbnail-image="<?php echo $_smarty_tpl->tpl_vars['thumbnailpath']->value;?>
/<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['uploadref']->value);?>
/<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['pageName']->value);?>
.jpg" data-page-number="0">
										<img class="imgpage" src="<?php echo $_smarty_tpl->tpl_vars['thumbnailpath']->value;?>
/<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['uploadref']->value);?>
/<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['pageName']->value);?>
.jpg" alt="Preview" />
									</div>

					<?php } else { ?>

									<div class="page" data-thumbnail-image="<?php echo $_smarty_tpl->tpl_vars['thumbnailpath']->value;?>
/<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['uploadref']->value);?>
/<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['pageName']->value);?>
.jpg" data-page-name="<?php echo $_smarty_tpl->tpl_vars['pageInfo']->value['pagename'];?>
">
										<img class="imgpage" src="<?php echo $_smarty_tpl->tpl_vars['thumbnailpath']->value;?>
/<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['uploadref']->value);?>
/<?php echo rawurlencode((string)$_smarty_tpl->tpl_vars['pageName']->value);?>
.jpg" alt="Preview" />
									</div>

					<?php }?>

				<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

				<?php $_smarty_tpl->_subTemplateRender("file:includes/pageflip_controlbar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

			<?php }?>

								</div>
    <?php }?>
                        </div>
                    </div>
                    <div class="previewBoxLeft">

    <?php if ($_smarty_tpl->tpl_vars['macdownloadurl']->value != '' || $_smarty_tpl->tpl_vars['win32downloadurl']->value != '') {?>
                        <div class="top_gap">
                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePreviewOurSoftware');?>

                            <ul class="ulPreview">
        <?php if ($_smarty_tpl->tpl_vars['win32downloadurl']->value != '') {?>
                                <li>
                                    <span class="previewSoftwareText"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePreviewDownloadWindows');?>
</span>
                                    <a href="" id="download-win" data-url="<?php echo $_smarty_tpl->tpl_vars['win32downloadurl']->value;?>
" class="linkGreen">
                                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDownload');?>

                                    </a>
                                </li>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['macdownloadurl']->value != '') {?>
                                <li>
                                    <span class="previewSoftwareText"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePreviewDownloadMac');?>
</span>
                                    <a href="" id="download-mac" data-url="<?php echo $_smarty_tpl->tpl_vars['macdownloadurl']->value;?>
" class="linkGreen">
                                        <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDownload');?>

                                    </a>
                                </li>
        <?php }?>
                            </ul>
                        </div>
    <?php }?>
    <?php if ((($_smarty_tpl->tpl_vars['previewowner']->value == 0) && ($_smarty_tpl->tpl_vars['temporder']->value == 0))) {?>
                        <div id="addtoany_container">
                            <img class="pointer" src="<?php echo $_smarty_tpl->tpl_vars['brandroot']->value;?>
/images/icons/share_icons.png" alt="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelShare');?>
" id="shareprojectfrompreviewA2AImage" data-projectname="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['projectname']->value, ENT_QUOTES, 'UTF-8', true);?>
" />
                        </div>
    <?php }
} else { ?>
                        <p>
                            <?php echo $_smarty_tpl->tpl_vars['resultparam']->value;?>

                        </p>
<?php }?>
                    </div>
                    <div class="previewBoxRight">
<?php if (!$_smarty_tpl->tpl_vars['ordercancelled']->value) {?>
        <?php if ($_smarty_tpl->tpl_vars['canOrder']->value == 1) {?>
                        <div class="buttonBottomInside">
                            <div class="contentBtn" id="reorderBottomButton">
                                <div class="btn-green-left" ></div>
                                <div class="btn-green-middle"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOrderBookNow');?>
</div>
                                <div class="btn-accept-right"></div>
                            </div>
                        </div>
                        <div class="clear"></div>
        <?php }?>
    <?php }?>
                    </div>
                    <div class="clear"></div>
<?php if ($_smarty_tpl->tpl_vars['sidebaradditionalinfo']->value != '') {?>
                    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebaradditionalinfo']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}?>

<?php if ($_smarty_tpl->tpl_vars['sidebaraccount']->value != '' || $_smarty_tpl->tpl_vars['sidebarcontactdetails']->value != '') {?>
                    <div class="side-outer-panel">
    <?php if ($_smarty_tpl->tpl_vars['sidebaraccount']->value != '') {?>
                            <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebaraccount']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
    <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['sidebarcontactdetails']->value != '') {?>
                            <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['sidebarcontactdetails']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
    <?php }?>
                    </div>
<?php }?>
                </div>
            </div>
        </div>
        <form id="submitform" name="submitform" method="post" action="#" accept-charset="utf-8">
            <input type="hidden" id="ref" name="ref" value="<?php echo $_smarty_tpl->tpl_vars['session']->value;?>
" />
            <input type="hidden" id="fsaction" name="fsaction" value="Share.reorder" />
            <input type="hidden" id="ssotoken" name="ssotoken" value="<?php echo $_smarty_tpl->tpl_vars['ssotoken']->value;?>
" />
            <input type="hidden" id="ref2" name="ref2" value="<?php echo $_smarty_tpl->tpl_vars['ref2']->value;?>
" />
            <input type="hidden" id="method" name="method" value="<?php echo $_smarty_tpl->tpl_vars['method']->value;?>
" />
            <input type="hidden" id="orderitemid" name="orderitemid" value="<?php echo $_smarty_tpl->tpl_vars['orderitemid']->value;?>
" />
            <input type="hidden" id="recipient" name="recipient" value="<?php echo $_smarty_tpl->tpl_vars['recipient']->value;?>
" />
            <input type="hidden" id="action" name="action" value="<?php echo $_smarty_tpl->tpl_vars['reorderaction']->value;?>
" />
            <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo smarty_function_csrf_token(array(),$_smarty_tpl);?>
" />
        </form>
    </body>
</html><?php }
}
