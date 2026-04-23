{literal}

function initialize(pParams)
{		
	var panel = { xtype: 'panel', layout: 'form', frame: true, padding: 5,
		items: [
			{ xtype:'textfield', id: 'versionString', value: buildInfo, readOnly: true, fieldLabel: '{/literal}{#str_LabelBuidInformation#}{literal}', width: 330}
		]
	};
	
	gMainWindowObj = new Ext.Window({
		  id: 'MainWindow',
		  title:'{/literal}{#str_SectionTitleAbout#}{literal}',
		  closable:true,
		  width:480,
		  height:310,
		  layout: 'fit',
		  resizable:false,
		  padding:0, margin:0,
		  baseParams: { ref: '{/literal}{$ref}{literal}' },
		  listeners: {
				'close': {   
					fn: function(){
			  			accordianWindowInitialized = false;
					}
				}
			},
		  items: panel
	});

	gMainWindowObj.show();
    
    oObject = document.getElementById('versionString');
    oObject.onfocus = function(){
        this.blur();
    }
 
}

/* close this window panel */
function windowClose()
{
	if ((gMainWindowObj) && (gMainWindowObj.close))
	{
		gMainWindowObj.close();
	}
}

{/literal}