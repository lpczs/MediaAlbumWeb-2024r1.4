{literal}
var height = 0;
var gCurrentPosition = 0;
var countImages = {/literal}{$pages|@sizeof}{literal};
var gCurrentTumbnailPosition = 0;
var gVisbileThumbnails = 0;
var gPreviewVisible = 0;
var previewWidth = 0;
var gThumbnailNavigationDisplay = false;
var gThumbnailWidth = 0;
var gSession = '{/literal}{$session}{literal}';
var gSSOToken = '{/literal}{$ssotoken}{literal}';

{/literal}

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



/* Not Online Preview */

{if $displaytype != 4}

{literal}

var previewType = {/literal}{$displaytype}{literal};
var previewLicenseKey = '{/literal}{$previewlicensekey}{literal}';

function initPreview(pInit)
{
    /** Height of the scroll bloc **/
    // window height
    height = window.innerHeight;

    // size of the navigation
    var contentNavigation = document.getElementById('contentNavigationPreview');
    var styleContentNavigation = contentNavigation.currentStyle || window.getComputedStyle(contentNavigation);
    height = height - (parseInt(styleContentNavigation.height) + parseInt(styleContentNavigation.paddingTop));
    height = height - (parseInt(styleContentNavigation.paddingBottom) + parseInt(styleContentNavigation.marginTop) + parseInt(styleContentNavigation.marginBottom));

    // size of the content
    var contentRightScroll = document.getElementById('contentRightScrollPreview');
    var styleContentRightScroll = contentRightScroll.currentStyle || window.getComputedStyle(contentRightScroll);
    height = height - (parseInt(styleContentRightScroll.paddingTop) + parseInt(styleContentRightScroll.paddingBottom));

    contentRightScroll.style.height = height + 'px';

    /** End  Height of the scroll bloc **/

	{/literal}
	// pageflip active
	{if ($displaytype == 1) && ($previewlicensekey != '')}
	{literal}

		// create page flip book
		if (pInit)
		{
			createPageFlipBook(height, gSiteContainer, pInit);
		}
		else
		{
			gCurrentPosition = $('#pageflip').pageflip().getPN();

			// page flip handle the resize itself
			$('#pageflip').pageflip().closePageflip(function()
			{
				createPageFlipBook(height, gSiteContainer, pInit);
			});
		}
	

	{/literal}
	{else}
	{literal}

	// slide show active

    // force the size of the slide show container
    var slideImageContainer = document.getElementById('slideImageContainer');
    slideImageContainer.style.width = gSiteContainer + 'px';

    /** height and width of the slide show **/

    // slideshow container size
    var previewList = document.getElementsByClassName('pagePreviewImage');
    var previewListLength = previewList.length;
    var preview = previewList[0];
    var stylePreview = preview.currentStyle || window.getComputedStyle(preview);
    previewWidth = parseInt(stylePreview.width);

    // add the arrow to the calcutaion
    var navBtn = document.getElementById('navPrev');
    var styleNavBtn = navBtn.currentStyle || window.getComputedStyle(navBtn);
    previewWidthFull = previewWidth + (parseInt(styleNavBtn.width) * 2);

    //height of the container
    var thumbnailContainer = document.getElementById('thumbnailContainer');
    var styleThumbnailContainer = thumbnailContainer.currentStyle || window.getComputedStyle(thumbnailContainer);
    var heightContainer = height - parseInt(styleThumbnailContainer.height);

    var styleSlideImageContainer = slideImageContainer.currentStyle || window.getComputedStyle(slideImageContainer);
    heightContainer = heightContainer - parseInt(styleSlideImageContainer.marginTop) - parseInt(styleSlideImageContainer.marginBottom);

    var slideshow = document.getElementById('slideshow');
    var styleSlideShow = slideshow.currentStyle || window.getComputedStyle(slideshow);
    heightContainer = heightContainer - parseInt(styleSlideShow.marginTop) - parseInt(styleSlideShow.marginBottom);

    // check if the default width is bigger than the screen size
    if (previewWidthFull > gSiteContainer)
    {
        previewWidth = gSiteContainer - (parseInt(styleNavBtn.width) * 2);

        for (var i = 0; i < previewListLength; i++)
        {
            var imageObj = document.getElementsByClassName('pagePreviewImage')[i];
            imageObj.style.width = previewWidth + 'px';
            var childImage = imageObj.firstElementChild;
            childImage.style.maxWidth = previewWidth + 'px';
            childImage.style.maxHeight = heightContainer + 'px';
        }
    }
    else
    {
        // force the height of the slideshow images
        for (var i = 0; i < previewListLength; i++)
        {
            var imageObj = document.getElementsByClassName('pagePreviewImage')[i];
            var childImage = imageObj.firstElementChild;
            childImage.style.maxHeight = heightContainer + 'px';
        }
    }

    /** End height and width of the slide show **/

    /** Center the slideshow **/

    //make sure the first image is loaded
    setTimeout(function()
    {
        if (height > (parseInt(styleSlideShow.height) + parseInt(styleSlideShow.marginTop) + parseInt(styleSlideShow.marginBottom)))
        {
            slideshow.className += ' slideshowCentre';
        }
        else
        {
            slideshow.className = slideshow.className.replace(' slideshowCentre', '');
        }
    }, 300);

    /** Thumbnail size **/
    // force size of the preview slide show
    document.getElementById('slideImageVisible').style.width = previewWidth + 'px';
    document.getElementById('listPagePreview').style.width = (previewWidth * countImages) + 'px';

    // selected thubnail size
    var thumbnailSelected = document.getElementsByClassName('thumbnailActive')[0];
    styleThumbnailSelected = thumbnailSelected.currentStyle || window.getComputedStyle(thumbnailSelected);

    gThumbnailWidth = parseInt(styleThumbnailSelected.width) + parseInt(styleThumbnailSelected.marginLeft) + parseInt(styleThumbnailSelected.marginRight);
    var listThumbnailWidth = (gThumbnailWidth * countImages) + parseInt(styleThumbnailSelected.borderLeftWidth) + parseInt(styleThumbnailSelected.borderRightWidth);

    document.getElementById('listTumbnail').style.width = listThumbnailWidth + 'px';

    // if needed remove the navigation
    if (gSiteContainer > listThumbnailWidth)
    {
        document.getElementById('thumbnailContainer').style.width = listThumbnailWidth + 'px';
        document.getElementById('thumbnailPreviewVisible').style.margin = 0;
        document.getElementById('thumbnailPreviewPrev').style.display = 'none';
        document.getElementById('thumbnailPreviewNext').style.display = 'none';
        gThumbnailNavigationDisplay = false;
    }
    else
    {
        var thumbnailPreviewVisible = document.getElementById('thumbnailPreviewVisible');

        document.getElementById('thumbnailContainer').style.width = gSiteContainer + 'px';
        document.getElementById('thumbnailPreviewPrev').style.display = 'Block';
        document.getElementById('thumbnailPreviewNext').style.display = 'Block';
        thumbnailPreviewVisible.style.margin = '0 30px';

        styleThumbnailPreviewVisible = thumbnailPreviewVisible.currentStyle || window.getComputedStyle(thumbnailPreviewVisible);
        gPreviewVisible = gSiteContainer - (parseInt(styleThumbnailPreviewVisible.marginLeft) + parseInt(styleThumbnailPreviewVisible.marginRight));
        gVisbileThumbnails = Math.floor((gPreviewVisible / gThumbnailWidth));
        gThumbnailNavigationDisplay = true;
    }

    /** End Thumbnail size **/

    // don't change the buttons action on resize
    if (pInit == true)
    {
        /** Page Actions **/

        document.querySelector('.next').addEventListener('click', function()
        {
            // check if it's the last image
            if (gCurrentPosition != countImages -1)
            {
                showCurrent(gCurrentPosition + 1);
            }
            else
            {
                showCurrent(0);
            }

        }, false);

        document.querySelector('.prev').addEventListener('click', function()
        {
            // check if it's the first image
            if (gCurrentPosition != 0)
            {
                showCurrent(gCurrentPosition - 1);
            }
            else
            {
                showCurrent(countImages -1);
            }

        }, false);

        /** End Pages actions **/

        /** Thumbnail Actions **/

        document.getElementById('thumbnailPreviewPrev').addEventListener('click', function()
        {
            if (gCurrentTumbnailPosition != 0)
            {
                // If the first thumbnail is actived the left arrow is disabled
                gCurrentTumbnailPosition += gThumbnailWidth;
                if (gCurrentTumbnailPosition >= 0)
                {
                    gCurrentTumbnailPosition = 0;
                    document.getElementById('thumbnailPreviewPrev').className += ' thumbnailPreviewDisabled';
                }
                else
                {
                    document.getElementById('thumbnailPreviewNext').className = document.getElementById('thumbnailPreviewNext').className.replace(' thumbnailPreviewDisabled', '');
                }

                document.getElementById('listTumbnail').style.marginLeft = gCurrentTumbnailPosition + 'px';
            }

        }, false);

        document.getElementById('thumbnailPreviewNext').addEventListener('click', function()
        {
            //test if we are on the last thumbnail
            if (gCurrentTumbnailPosition > (gPreviewVisible - listThumbnailWidth))
            {
                gCurrentTumbnailPosition -= gThumbnailWidth;

                if (gCurrentTumbnailPosition <= (gPreviewVisible - listThumbnailWidth))
                {
                    document.getElementById('thumbnailPreviewNext').className += ' thumbnailPreviewDisabled';
                }

                document.getElementById('listTumbnail').style.marginLeft = gCurrentTumbnailPosition + 'px';
                document.getElementById('thumbnailPreviewPrev').className = document.getElementById('thumbnailPreviewPrev').className.replace(' thumbnailPreviewDisabled', '');
            }
        }, false);

    }

    /** End Thumbnail Actions **/

    document.getElementById('listPagePreview').style.display = 'Block';
    document.getElementById('thumbnailPreviewVisible').style.display = 'Block';

	closeLoadingDialog();

	{/literal}
	{/if}
	{literal}

}

// Wrapper for the showCurrent function used when changing page within the preview.
function fnShowCurrent(pElement)
{
    if (!pElement) {
        return false;
    }

    return showCurrent(parseInt(pElement.getAttribute('data-index')));
}

function showCurrent(pPosition)
{
	{/literal}
	// pageflip active
	{if ($displaytype == 1) && ($previewlicensekey != '')}
	{literal}

		// done part of the initialise function

	{/literal}
	{else}
	{literal}

    // remove current selection
    document.getElementById('thumbnail' + gCurrentPosition).className = 'previewThumbnail';

    // store the current position
    gCurrentPosition = pPosition;

    // add new selection
    document.getElementById('listPagePreview').style.marginLeft = ((pPosition * previewWidth) * -1) + 'px';

     // slide to the new position
    document.getElementById('thumbnail' + gCurrentPosition).className = 'previewThumbnail thumbnailActive';

    // Thumbnail's naviagtion is displayed the thubnais list is shifted

    if (gThumbnailNavigationDisplay)
    {
        //check if the thumbnail is inside the visible part
        var positionThumbnail = (gThumbnailWidth * pPosition ) * -1;
        var lastHidden =  Math.floor((gCurrentTumbnailPosition / gThumbnailWidth) * -1);
        var needToMove = false;

        // test if the last immage has been past so just to the first image
        if (lastHidden > pPosition)
        {
            gCurrentTumbnailPosition = positionThumbnail;
            needToMove = true;
        }
        else
        {
            // test if the last image visible has been past
            if ((lastHidden + gVisbileThumbnails) <= pPosition)
            {
                gCurrentTumbnailPosition = (positionThumbnail + gPreviewVisible - gThumbnailWidth);
                needToMove = true;
            }
        }

        // if the first image is requested, reset the preview button
        if (pPosition == 0)
        {
            gCurrentTumbnailPosition = 0;
            document.getElementById('thumbnailPreviewPrev').className += ' thumbnailPreviewDisabled';
            document.getElementById('thumbnailPreviewNext').className = document.getElementById('thumbnailPreviewNext').className.replace(' thumbnailPreviewDisabled', '');
            needToMove = true;
        }
        else
        {
            //if the last image has been resquested, reset the next button
            if (pPosition == countImages -1)
            {
                document.getElementById('thumbnailPreviewNext').className += ' thumbnailPreviewDisabled';
                document.getElementById('thumbnailPreviewPrev').className = document.getElementById('thumbnailPreviewPrev').className.replace(' thumbnailPreviewDisabled', '');
            }
            else
            {
                document.getElementById('thumbnailPreviewNext').className = document.getElementById('thumbnailPreviewNext').className.replace(' thumbnailPreviewDisabled', '');
                document.getElementById('thumbnailPreviewPrev').className = document.getElementById('thumbnailPreviewPrev').className.replace(' thumbnailPreviewDisabled', '');
            }
        }

        if (needToMove)
        {
            document.getElementById('listTumbnail').style.marginLeft = gCurrentTumbnailPosition + 'px';
        }
    }

	{/literal}
	{/if}
	{literal}

}

function resetPreview()
{
	{/literal}
	// pageflip active
	{if ($displaytype == 1) && ($previewlicensekey != '')}
	{literal}

		// nothing to do for page flip

	{/literal}
	{else}
	{literal}

    // reset position
    document.getElementById('listTumbnail').style.marginLeft =  '0px';
    document.getElementById('listPagePreview').style.marginLeft = '0px';

    // reset preview size
    var previewList = document.getElementsByClassName('pagePreviewImage');
    var previewListLength = previewList.length;
    for (var i = 0; i < previewListLength; i++)
    {
        document.getElementsByClassName('pagePreviewImage')[i].style.width = '514px';
    }

	{/literal}
	{/if}
	{literal}
}

/**
* createPageFlipBook
*
* set the page flip configuration and load the external component
*
* @param int pHeight // height max fo the component
* @param int pWidth // width max fo the component
* @param boolean pInit // true if we initilise the page or flase if it's a risze
**/
function createPageFlipBook(pHeight, pWidth, pInit)
{
	var isSinglePageProject = {/literal}{$pageflipsettings.productpageformat}{literal};
	var verticalMode = {/literal}{$pageflipsettings.verticalmode}{literal};
	var pageWidth =  {/literal}{$pageflipsettings.pagewidth}{literal};
	var pageHeight = {/literal}{$pageflipsettings.pageheight}{literal};
	var coverWidth = {/literal}{$pageflipsettings.coverwidth}{literal};
	var coverHeight = {/literal}{$pageflipsettings.coverheight}{literal};
	var previewVisible = {/literal}{$pageflipsettings.contentpreviewenabled}{literal};
	var previewDisplayedAtTheBottom = {/literal}{$pageflipsettings.buttonthumbnailenabled}{literal};
	var hasCover = {/literal}{$pageflipsettings.hascover}{literal};
	var ratio = 1;

	// make sure the size of the thumbnail is not bigger than the maximum size
	var maxWidth = pWidth;
	var maxHeight = pHeight;

	var htmlPageFlipContainer = document.getElementById('pageflip');
	var marginBottom = 64;
	var windowWidth = window.innerWidth;
	var height  = pHeight;

	// fix container height
	htmlPageFlipContainer.style.height = height - 10 + 'px';

	if (previewDisplayedAtTheBottom)
	{
		if (height > windowWidth)
		{
			marginBottom = 164;
		}
		else
		{
			marginBottom = 114;
		}

		maxHeight -= marginBottom // margin hardecode in javascript;
	}

	// if it's a spread project the width need to be divided by 2 because it's the width of a page
	if (! isSinglePageProject)
	{
		maxWidth = maxWidth / 2;
	}
	else 
	{
		if (! verticalMode)
		{
			// book is not centred the same way on single page project so the width need to be changed
			maxWidth = (maxWidth - 70) / 2;
		}
		else
		{
			// prevent the page to be over the top of the container
			maxHeight = (maxHeight - 85) / 2;
		}
	}


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

	// resize page and cover picture via javascript this cannot be done via CSS attribute on the object itself
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
		ZoomEnabled: false,
		FullScreenEnabled: false,
		Margin: 10,
		MarginBottom: marginBottom,
		AutoScale: {/literal}{$pageflipsettings.pagescale}{literal},
		AutoStageHeight: false,
		StartAutoFlip: {/literal}{$pageflipsettings.startautoflip}{literal},
		VerticalMode: verticalMode,
		SinglePageMode: isSinglePageProject,
		DisableSelection: true,
		AutoFlipLoop: 1,
		FlipDuration: 350,
		Copyright: 'Taopix Limited',
		Key: previewLicenseKey
	};

	// margin are changed if the thumbnail are displayed at the bottom of the book
	if (previewDisplayedAtTheBottom)
	{
		$('#pageflip').addClass('thumbnailsAtTheBottom');

		pageFlipSettings.MarginTop = 30;
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

		var thumbnailWidth =  {/literal}{$pageflipsettings.thumbnailwidth}{literal};
		var thumbnailheight =  {/literal}{$pageflipsettings.thumbnailheight}{literal};

		if (height < windowWidth)
		{
			thumbnailWidth = thumbnailWidth / 2;
			thumbnailheight = thumbnailheight / 2;
		}

		pageFlipSettings.ThumbnailWidth = thumbnailWidth;
		pageFlipSettings.ThumbnailHeight = thumbnailheight;
	}

	// initialise page flip
	var pageflip = $('#pageflip');
	var pageflipContext = pageflip.pageflip();

	var pageFlipLoadingWait = setInterval(function()
	{
		if ($('#pf-pagerin').length > 0)
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
				$('#b-thumbs').addClass('pf-disabled').unbind('click touchstart touchend');
			}

			if (! pInit)
			{
				pageflipContext.gotoPage(gCurrentPosition, true);
			}

			closeLoadingDialog();

			clearInterval(pageFlipLoadingWait);
		}
	}, 150);

	pageflip.pageflipInit(pageFlipSettings, 'book');
}

{/literal}

{else} {* else {if $displaytype != 4} *}

    {literal}

function initPreview(pInit)
{
    var onlinePreviewFrame = document.getElementById('onlinePreviewFrame');

    /** Height of the scroll bloc **/
    // window height
    height = document.body.offsetHeight;

    // size of the navigation
    var contentNavigation = document.getElementById('contentNavigationPreview');
    var styleContentNavigation = contentNavigation.currentStyle || window.getComputedStyle(contentNavigation);
    height = height - (parseInt(styleContentNavigation.height) + parseInt(styleContentNavigation.paddingTop));
    height = height - (parseInt(styleContentNavigation.paddingBottom) + parseInt(styleContentNavigation.marginTop) + parseInt(styleContentNavigation.marginBottom));

    // size of the content
    var contentRightScroll = document.getElementById('contentRightScrollPreview');
    var styleContentRightScroll = contentRightScroll.currentStyle || window.getComputedStyle(contentRightScroll);
    height = height - (parseInt(styleContentRightScroll.paddingTop) + parseInt(styleContentRightScroll.paddingBottom));

    onlinePreviewFrame.style.height = (height - 10) + 'px';
    onlinePreviewFrame.style.width = gSiteContainer + 'px';

    closeLoadingDialog();
}

function showCurrent(pPosition){}

function resetPreview(){}

    {/literal}

{/if} {* end {if $displaytype != 4}*}
