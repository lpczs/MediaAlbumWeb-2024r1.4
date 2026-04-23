{if $orderdata.googleanalyticscode != ''}

    {literal}

    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', '{/literal}{$orderdata.googleanalyticscode}{literal}', 'auto');
	{/literal}
	{if $orderdata.googleanalyticsuseridtracking}
		{literal}
		ga('set', 'userId', {/literal}{$orderdata.userid}{literal}); // Set the user ID using signed-in userid.
		{/literal}
	{/if}
	{literal}
    ga('send', 'pageview');
    ga('require', 'ecommerce', 'ecommerce.js');

    ga('ecommerce:addTransaction', {
    'id': '{/literal}{$ordernumber}{literal}',	// Transaction ID. Required
    'affiliation': '{/literal}{$orderdata.brandcode}{literal}', // Affiliation or store name
    'revenue': '{/literal}{$orderdata.total}{literal}', // Grand Total
    'shipping': '{/literal}{$orderdata.shippingtotal}{literal}', // Shipping
    'tax': '{/literal}{$orderdata.ordertaxtotal}{literal}', // Tax
    'currency': '{/literal}{$orderdata.ordercurrency}{literal}' // Currency code
    });

    {/literal}

    {foreach from=$orderdata.orderlines item=line}
        // add item might be called for every item in the shopping cart
        // loop through each item in the cart and
        // print out addItem for each
        {literal}
            ga('ecommerce:addItem', {
            'id': '{/literal}{$ordernumber}{literal}', // Transaction ID. Required
            'name': '{/literal}{$line.productname}{literal}', // Product name. Required
            'sku': '{/literal}{$line.productcode}{literal}', // SKU/code
            'category': '', // Category or variation
            'price': '{/literal}{$line.price}{literal}', // Unit price
            'quantity': '{/literal}{$line.qty}{literal}' // Quantity
            });
        {/literal}

    {/foreach}

    ga('ecommerce:send'); //submits transaction to the Analytics servers

{/if}

// stop the back button from working to prevent the user from being taken back into the shopping cart
history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    }; 