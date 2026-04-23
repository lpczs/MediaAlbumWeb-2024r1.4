<?php
ini_set('max_execution_time', '0');
chdir('../../');

require_once '../libs/external/vendor/autoload.php';

use Taopix\Connector\Shopify\ShopifyConnector;

$shopifyConnector = new ShopifyConnector($_GET['shop']);

if ($shopifyConnector->verifyHash())
{
	$msgHTML = '';

	try
	{
		$shopifyConnector->installCallback();
		$msgHTML .= '<h1>Installation Complete</h1>';

		$themeErrors = $shopifyConnector->getThemeErrors();

		if (count($themeErrors) > 0)
		{
			$msgHTML .= '<p>Encountered the following error(s) when modifying theme templates, changes may need to be applied manually.</p>';
			$msgHTML .= '<ul>';
			foreach ($themeErrors as $error)
			{
				$msgHTML .= sprintf('<li>%s</li>', $error);
			}
			$msgHTML .= '</ul>';
		} else
		{
			$msgHTML .= '<p>Your Taopix Shopify app has been installed successfully.</p>';
		}
	}
	catch (Throwable $pError)
	{
		$msgHTML .= '<h1>Install failed</h1>';
		$msgHTML .= sprintf('<p>Error: %s in file %s on line %d</p>', $pError->getMessage(), $pError->getFile(),  $pError->getLine());
	}

	$HTML = '<html>
				<head>
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<style>
						body
						{
							background: #efefef;
						}
						main
						{
							background: #fff;
							border-radius: 25px;
							margin: 0 auto;
							text-align: center;
							font-family: Arial, Helvetica, sans-serif;
							width: 440px;
							padding: 40px;
							margin-top: 20px;
							max-width: calc(100vw - 40px);
							box-sizing: border-box;
						}

						h1
						{
							font-size: 28px;
						}
						p
						{
							font-size: 14px;
						}

						img 
						{
							margin-top: 40px;
						}
					</style>
				</head>
				<body>
					<main>';

		$HTML .= $msgHTML;

		$HTML .= '<p><a href="' . $shopifyConnector->getShopURL() .'">Return to your Shopify Store admin page</a></p>
					<img src="/images/logo_v2.png" alt="Taopix Logo" width="200" />
				</main>
			</body>
			</html>';
		
		echo $HTML;
}
else
{
	throw new Exception('HMAC not valid');
}
