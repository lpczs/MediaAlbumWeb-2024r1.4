{literal}

function initialize(pParams)
{	
 	{/literal}
	var gProductsActive = '{$productsactive}';
	var gNewCount = '{$newcount}';
	var gUpdateCount = '{$updatecount}';
	var gInProgress = '{$inprogress}';
	var gShopURL = '{$shopurl}';

	{literal}

    function onCallbackSync(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.success === false)
			{
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: pActionData.result.msg, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
  
			gridDataStoreObj.reload();
		}
	};
	
	var productsActiveChecked = false;

	if (gProductsActive == 1) {
		productsActiveChecked = true;
	}

	var displayHTML = "";
	boxTitle = "{/literal}{#str_LabelConfirmation#}{literal}";


	if((gUpdateCount > 0 || gNewCount > 0) && gInProgress != 'true')
	{
		msg = "{/literal}{#str_MsgProductsToSync#}{literal}".replace("'^0'", gShopURL);

		displayHTML += msg;
		displayHTML += "<br /><br />";
		displayHTML += "<input type='radio' name='productsactiveCheckbox' id='productDraftCheckbox' value='0' ";
		if (!productsActiveChecked)
		{
			displayHTML += "checked"
		}
		displayHTML += "/> ";
		displayHTML += "<label for='productDraftCheckbox'>{/literal}{#str_LabelSyncProductsDraft#}{literal}</label> ";
		displayHTML += "<br />";
		
		displayHTML += "<input type='radio' name='productsactiveCheckbox' id='productsactiveCheckbox' value='1' ";
		if (productsActiveChecked)
		{
			displayHTML += "checked"
		}
		displayHTML += "/> ";
		displayHTML += "<label for='productsactiveCheckbox'>{/literal}{#str_LabelSyncProductsActive#}{literal}</label> ";

		displayHTML += "<br />";
		
		Ext.MessageBox.buttonText = {no: "{/literal}{#str_ButtonCancel#}{literal}", yes: "{/literal}{#str_ButtonTextStartPublish#}{literal}"};
	}

	if (gInProgress == 'true') {
		displayHTML = "{/literal}{#str_LabelInProgressSyncMessage#}{literal}";
		boxTitle = "{/literal}{#str_BoxTitleActionUnavailable#}{literal}";
	}
	else if(displayHTML == "")
	{
		displayHTML = "{/literal}{#str_LabelNothingToSyncMessage#}{literal}";
		boxTitle = "{/literal}{#str_BoxTitleNoProducts#}{literal}";
	}

	var doSync = function()
	{
		var onSyncConfirmed = function(btn)
		{
			if (btn == "yes") {
				var paramArray = [];
				var productsactive = 0; 
				var retainnames = 0; 
				var productsActiveCheckbox = document.getElementById('productsactiveCheckbox');

				if (productsActiveCheckbox)
				{
					if (productsActiveCheckbox.checked)
					{
						productsactive = 1;
					}
				}

				paramArray['retainnames'] = retainnames;
				paramArray['productsactive'] = productsactive;
				paramArray['id'] = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('connectorsGrid'));

				Ext.taopix.formPost(gMainWindowObj, paramArray, 'index.php?fsaction=AdminConnectors.sync', "{/literal}{#str_MessageSyncing#}{literal}", onCallbackSync);
			}
		};
		Ext.MessageBox.minWidth = 350;

		if((gNewCount > 0 && gInProgress != 'true') || (gUpdateCount > 0 && gInProgress != 'true'))
		{
			Ext.MessageBox.show({ title: boxTitle, msg: displayHTML, buttons: Ext.MessageBox.YESNO, icon: Ext.MessageBox.INFO, fn: onSyncConfirmed});
		}
		else
		{
			Ext.MessageBox.buttonText = {cancel: "{/literal}{#str_ButtonClose#}{literal}"};
			Ext.MessageBox.show({ title: boxTitle, msg: displayHTML, buttons: Ext.MessageBox.CANCEL, icon: Ext.MessageBox.INFO, fn: onSyncConfirmed});
		}
		connectorsSyncProductsWindowExists = false;

		Ext.getCmp('MainWindow').el.unmask();

		if(Ext.MessageBox){
			Ext.MessageBox.buttonText = {
				ok     : "{/literal}{#str_ButtonOk#}{literal}",
				cancel : "{/literal}{#str_ButtonCancel#}{literal}",
				yes    : "{/literal}{#str_ButtonYes#}{literal}",
				no     : "{/literal}{#str_ButtonNo#}{literal}"
			};
		}

	};

	doSync();

	
}

{/literal}