<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$langCode}" xml:lang="{$langCode}" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="content-language" content="{$langCode}" />
        <meta http-equiv="content-style-type" content="text/css" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <title>{$appname}</title>

		{if $googletagmanagercccode ne ''}
            {include file="includes/googletagmanager.tpl" googletagmanagercccode=$googletagmanagercccode}
        {/if}
		
        {include file="includes/customerinclude_large.tpl"}
    </head>
    <body>
         <div id="header" class="headertop">
                <div class="headerinside">
                    {include file="$header"}
                </div>
            </div>
		<div class="unsupported-browser-container">
			<h1>{#str_titleBrowserNotSupported#}</h1>
			<p>{#str_titleRecommendBrowsers#}</p>

			<div class="panel-wrap">
				
				{if !$hidechrome}	
					<a class="browser-box" href="http://www.google.com/chrome">
						<img src="{$webroot}/images/chrome.png" alt="Chrome" />
						<h3>{#str_labelBrowserChrome#}</h3>
						<p>{$minchrome}</p>
						{if !$hidechromedownload}
							<div class="link-wrap">
								<p>{#str_LabelDownload#}</p>
							</div>
						{/if}
					</a>		
				{/if}
				
				{if !$hidefirefox}
					<a class="browser-box" href="http://www.mozilla.org">
						<img src="{$webroot}/images/firefox.png" alt="Firefox" />
						<h3>{#str_labelBrowserFirefox#}</h3>
						<p>{$minff}</p>
						{if !$hidefirefoxdownload}
							<div class="link-wrap">
								<p>{#str_LabelDownload#}</p>
							</div>
						{/if}
					</a>
				{/if}

				{if !$hidesafari}
					<div class="browser-box">
						<img src="{$webroot}/images/safari.png" alt="Safari" />
						<h3>{#str_labelBrowserSafari#}</h3>
						<p>{$minsafari}</p>
					</div>
				{/if}

				{if !$hideedge}
					<a class="browser-box" href="https://www.microsoft.com/en-gb/windows/microsoft-edge">
						<img src="{$webroot}/images/edge.png" alt="Edge" />
						<h3>{#str_labelBrowserEdge#}</h3>
						<p>{#str_LabelAllVersions#}</p>
						{if !$hideedgedownload}
							<div class="link-wrap">
								<p>{#str_LabelDownload#}</p>
							</div>
						{/if}					
					</a>
				{/if}
			</div>
		</div>
    </body>
</html>