<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui"/> <!--number-based width or "device-width" -->
	<style>
		body, html
		{
			margin: 0;
			padding: 0;
			height: 100%;
		}

		iframe
		{
			border: none;
			padding: 0;
			margin: 0;
			width: 100%;
			height: 100%;
			position: fixed;

		}
	</style>

	<script type="text/javascript" src="<?php echo $controlCentreURL; ?>/utils/jquery.js"></script>
	<script type="text/javascript" src="<?php echo $controlCentreURL; ?>/Connectors/Shopify/scripts/cart.min.js"></script>
	<script>
		window.addEventListener("message", (event) => 
		{
			if (event.origin !== "<?php echo $designerDomain; ?>" && event.origin !== "<?php echo $controlCentreURL; ?>")
			{
				return;
			}

			if (event.projectref !== '')
			{
				CartJS.getCart().then(function(pCartData)
				{
					pCartData.items.map(function(pCartItem)
					{
						if ((pCartItem.properties !== null) && (pCartItem.properties.hasOwnProperty('__taopix_project_id')))
						{
							if (pCartItem.properties.__taopix_project_id === event.data.projectref)
							{
								// Remove project already in the cart.
								CartJS.removeItemById(pCartItem.id);
							}
						}
					});

					if (event.data.variantid)
					{
						var timestamp = Date.now();
						CartJS.addItem(event.data.variantid, event.data.quantity, {
							"__taopix_project_id": event.data.projectref,
							"__taopix_product_code": event.data.productcode,
							"__taopix_product_sku": event.data.productskucode,
							"__taopix_product_name": event.data.productname,
							"__taopix_product_collection_code": event.data.collectioncode,
							"__taopix_product_collection_name": event.data.collectionname,
							"__taopix_project_name": event.data.projectname,
							"__taopix_project_thumbnail": event.data.projectthumbnail,
							"__taopix_project_quantityprotected": event.data.quantityprotected,
							"__taopix_project_quantity": event.data.quantity,
							"__taopix_project_guestproject": event.data.guestproject,
							"__taopix_project_expires": timestamp + (event.data.purgedays * 24 * 60 * 60 * 1000)
						}, {
							success: function(data, textStatus, jqXHR) {
								window.location = event.data.redirecturl;
							},
							error: function(jqXHR, textStatus, errorThrown) {
								alert('Error: ' + errorThrown + '!');
							}
						});
					}
				});
			}
		}, false);
	</script>
</head>

<body>
	<iframe src="<?php echo $designURL; ?>" name="designer" sandbox="allow-scripts
				allow-same-origin
				allow-popups
				allow-top-navigation-by-user-activation
				allow-forms"></iframe>
</body>
</html>
