{literal}

function initialize(pParams)
{	
	gMainWindowObj = new Ext.Panel({
		id: 'MainWindow',
		layout: 'fit',
		anchor: '100% 100%',
		html: '<iframe src="https://home.taopix.com?wvs={/literal}{$webversionstring}{literal}" title="description"  height="100%" width="100%" "allow-scripts allow-same-origin allow-popups allow-top-navigation-by-user-activation allow-forms"></iframe>',
		baseParams: { ref: "{/literal}{$ref}{literal}" }
	});
		
	centreRegion.add(gMainWindowObj);
	centreRegion.doLayout();
} 


/* close this window panel */
function windowClose()
{
	if (Ext.getCmp('MainWindow'))
	{
		centreRegion.remove('MainWindow', true);
		centreRegion.doLayout();
	}	
}

{/literal}
