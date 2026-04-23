<?php
/* Smarty version 4.5.3, created on 2026-03-06 05:33:47
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\users\useredit.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa673b1a2668_65644742',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ea9529e4778dbe29a2edd68fd26fdbb6368aaf05' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\users\\useredit.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aa673b1a2668_65644742 (Smarty_Internal_Template $_smarty_tpl) {
?>
function initialize(pParams)
{
	Ext.layout.FormLayout.prototype.trackLabels = true;

	var TPX_LOGIN_SYSTEM_ADMIN = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_SYSTEM_ADMIN']->value;?>
;
	var TPX_LOGIN_COMPANY_ADMIN = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_COMPANY_ADMIN']->value;?>
;
	var TPX_LOGIN_SITE_ADMIN = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_SITE_ADMIN']->value;?>
;
	var TPX_LOGIN_CREATOR_ADMIN = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_CREATOR_ADMIN']->value;?>
;
	var TPX_LOGIN_PRODUCTION_USER = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_PRODUCTION_USER']->value;?>
;
	var TPX_LOGIN_DISTRIBUTION_CENTRE_USER = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_DISTRIBUTION_CENTRE_USER']->value;?>
;
	var TPX_LOGIN_STORE_USER = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_STORE_USER']->value;?>
;
	var TPX_LOGIN_BRAND_OWNER = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_BRAND_OWNER']->value;?>
;
	var TPX_LOGIN_API = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_API']->value;?>
;
	var TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER = <?php echo $_smarty_tpl->tpl_vars['TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER']->value;?>
;
	var storeDistOwner = "<?php echo $_smarty_tpl->tpl_vars['owner']->value;?>
";

	var ipAccessType = "<?php echo $_smarty_tpl->tpl_vars['ipaccesstype']->value;?>
";
	var ipAccessList = "<?php echo $_smarty_tpl->tpl_vars['ipaccesslist']->value;?>
";
	var defaultIpAccessList = "<?php echo $_smarty_tpl->tpl_vars['defaultipaccesslist']->value;?>
";

	function setLoginForm(comboBox, record, index)
	{
		loginTypeId = comboBox.getValue();

		switch(parseInt(loginTypeId)){
			case TPX_LOGIN_SYSTEM_ADMIN:
			case TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER:
				<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
					Ext.getCmp('productionsite').hide();
					Ext.getCmp('productionsite').disable();
					Ext.getCmp('company').hide();
					Ext.getCmp('company').disable();
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['optioncfs']->value) {?>
					Ext.getCmp('store').hide();
					Ext.getCmp('store').disable();
				<?php }?>
				Ext.getCmp('brand').hide();
				Ext.getCmp('brand').disable();
				break;
			case TPX_LOGIN_COMPANY_ADMIN:
				Ext.getCmp('company').store.reload();
				Ext.getCmp('company').show();
				Ext.getCmp('company').enable();
				<?php if ($_smarty_tpl->tpl_vars['optioncfs']->value) {?>
					Ext.getCmp('store').hide();
					Ext.getCmp('store').disable();
				<?php }?>
				Ext.getCmp('brand').hide();
				Ext.getCmp('brand').disable();
				Ext.getCmp('productionsite').hide();
				Ext.getCmp('productionsite').disable();
				break;
			case TPX_LOGIN_SITE_ADMIN:
				<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
					<?php if ($_smarty_tpl->tpl_vars['loggedInAs']->value == $_smarty_tpl->tpl_vars['TPX_LOGIN_COMPANY_ADMIN']->value) {?>
						Ext.getCmp('productionsite').show();
						Ext.getCmp('productionsite').enable();
					<?php }?>
					Ext.getCmp('productionsite').store.reload({ params: { siteAdmin: 1}	});
					Ext.getCmp('productionsite').store.on({
						'load': function() {
							if (Ext.getCmp('productionsite').store.findExact('id', "<?php echo $_smarty_tpl->tpl_vars['userprodsite']->value;?>
") > -1)
							{
								Ext.getCmp('productionsite').setValue("<?php echo $_smarty_tpl->tpl_vars['userprodsite']->value;?>
");
							}
							else
							{
								Ext.getCmp('productionsite').setValue(Ext.getCmp('productionsite').store.getAt(0).data.id);
							}
						}
					});
					Ext.getCmp('productionsite').show();
					Ext.getCmp('productionsite').enable();
					Ext.getCmp('company').hide();
					Ext.getCmp('company').disable();
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['optioncfs']->value) {?>
					Ext.getCmp('store').hide();
					Ext.getCmp('store').disable();
				<?php }?>
				Ext.getCmp('brand').hide();
				Ext.getCmp('brand').disable();
				break;
			case TPX_LOGIN_CREATOR_ADMIN:
				<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
					Ext.getCmp('productionsite').hide();
					Ext.getCmp('productionsite').disable();
					Ext.getCmp('company').hide();
					Ext.getCmp('company').disable();
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['optioncfs']->value) {?>
					Ext.getCmp('store').hide();
					Ext.getCmp('store').disable();
				<?php }?>
				Ext.getCmp('brand').hide();
				Ext.getCmp('brand').disable();
				break;
			case TPX_LOGIN_PRODUCTION_USER:
				<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
					<?php if ($_smarty_tpl->tpl_vars['loggedInAs']->value == TPX_LOGIN_SITE_ADMIN) {?>
						Ext.getCmp('productionsite').store.reload({	params: { siteAdmin: 1}	});
						Ext.getCmp('productionsite').store.on({
							'load': function() {
								if (Ext.getCmp('productionsite').store.findExact('id', "<?php echo $_smarty_tpl->tpl_vars['userprodsite']->value;?>
") > -1)
								{
									Ext.getCmp('productionsite').setValue("<?php echo $_smarty_tpl->tpl_vars['userprodsite']->value;?>
");
								}
							}
						});
						Ext.getCmp('productionsite').show();
						Ext.getCmp('productionsite').enable();
					<?php } else { ?>
						Ext.getCmp('productionsite').store.reload({	params: { siteAdmin: 2}	});
						Ext.getCmp('productionsite').store.on({
							'load': function() {
								if (Ext.getCmp('productionsite').store.findExact('id', "<?php echo $_smarty_tpl->tpl_vars['userprodsite']->value;?>
") > -1)
								{
									Ext.getCmp('productionsite').setValue("<?php echo $_smarty_tpl->tpl_vars['userprodsite']->value;?>
");
								}
								else
								{
									Ext.getCmp('productionsite').setValue(Ext.getCmp('productionsite').store.getAt(0).data.id);
								}
							}
						});
						Ext.getCmp('productionsite').show();
						Ext.getCmp('productionsite').enable();
					<?php }?>
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
					Ext.getCmp('company').hide();
					Ext.getCmp('company').disable();
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['optioncfs']->value) {?>
					Ext.getCmp('store').hide();
					Ext.getCmp('store').disable();
				<?php }?>
				Ext.getCmp('brand').hide();
				Ext.getCmp('brand').disable();
				break;
			case TPX_LOGIN_DISTRIBUTION_CENTRE_USER:
				Ext.getCmp('store').store.reload({
					params: { distributionCentre: 1}
				});
				Ext.getCmp('store').store.on({
					'load': function(){
						if (Ext.getCmp('store').store.findExact('id', storeDistOwner) > -1)
						{
							Ext.getCmp('store').setValue(storeDistOwner);
						}
						else
						{
							Ext.getCmp('store').setValue(Ext.getCmp('store').store.getAt(0).data.id);
						}
						Ext.getCmp('store').label.update("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_SiteTypeDistributionCentre');?>
");
					}
				});
				Ext.getCmp('store').show();
				Ext.getCmp('store').enable();

				<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
					Ext.getCmp('productionsite').hide();
					Ext.getCmp('productionsite').disable();
					Ext.getCmp('company').hide();
					Ext.getCmp('company').disable();
				<?php }?>
				Ext.getCmp('brand').hide();
				Ext.getCmp('brand').disable();
				break;
			case TPX_LOGIN_STORE_USER:
				<?php if ($_smarty_tpl->tpl_vars['optioncfs']->value) {?>
					Ext.getCmp('store').store.reload({
						params: { distributionCentre: 0}
					});
					Ext.getCmp('store').store.on({'load': function(){
						if (Ext.getCmp('store').store.findExact('id', storeDistOwner) > -1)
						{
							Ext.getCmp('store').setValue(storeDistOwner);
						}
						else
						{
							Ext.getCmp('store').setValue(Ext.getCmp('store').store.getAt(0).data.id);
						}
						Ext.getCmp('store').label.update("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStoreFieldLabel');?>
");
						}
					});
					Ext.getCmp('store').show();
					Ext.getCmp('store').enable();
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
					Ext.getCmp('productionsite').hide();
					Ext.getCmp('productionsite').disable();
					Ext.getCmp('company').hide();
					Ext.getCmp('company').disable();
				<?php }?>
				Ext.getCmp('brand').hide();
				Ext.getCmp('brand').disable();
				break;
			case TPX_LOGIN_BRAND_OWNER:
				Ext.getCmp('brand').store.reload();
				Ext.getCmp('brand').store.on({'load': function(){
					if (Ext.getCmp('brand').store.findExact('id', "<?php echo $_smarty_tpl->tpl_vars['brandcode']->value;?>
") > -1)
					{
						Ext.getCmp('brand').setValue("<?php echo $_smarty_tpl->tpl_vars['brandcode']->value;?>
");
					}
					else
					{
						Ext.getCmp('brand').setValue(Ext.getCmp('brand').store.getAt(0).data.id);
					}
				} });
				Ext.getCmp('brand').show();
				Ext.getCmp('brand').enable();
				<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
					Ext.getCmp('productionsite').hide();
					Ext.getCmp('productionsite').disable();
					Ext.getCmp('company').hide();
					Ext.getCmp('company').disable();
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['optioncfs']->value) {?>
					Ext.getCmp('store').hide();
					Ext.getCmp('store').disable();
				<?php }?>
				break;
			case TPX_LOGIN_API:
				<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
					Ext.getCmp('productionsite').hide();
					Ext.getCmp('productionsite').disable();
					Ext.getCmp('company').hide();
					Ext.getCmp('company').disable();
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['optioncfs']->value) {?>
					Ext.getCmp('store').hide();
					Ext.getCmp('store').disable();
				<?php }?>
				Ext.getCmp('brand').hide();
				Ext.getCmp('brand').disable();
			   break;
		}
	}

	function md5HashFunction(comboBox, record, index)
	{
		var password = Ext.getCmp('password_user').getValue();
		if (password != '')
		{
			if (password != "**UNCHANGED**")
	   	 	{
				var encPassword = hex_md5(password);
				Ext.getCmp('password_user').setValue(encPassword);
	   	 	}
	    	else
	    	{
	    		Ext.getCmp('password_user').setValue(password);
	    	}
		}
	}

	var loginTypeStore = new Ext.data.ArrayStore({
		id: 'logintypestore',
		fields: ['id', 'name'],
		data: [
			
			<?php
$__section_index_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['userlogintypes']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_index_0_total = $__section_index_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_index'] = new Smarty_Variable(array());
if ($__section_index_0_total !== 0) {
for ($__section_index_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] = 0; $__section_index_0_iteration <= $__section_index_0_total; $__section_index_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']++){
$_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] = ($__section_index_0_iteration === $__section_index_0_total);
?>
			<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['last'] : null)) {?>
					["<?php echo $_smarty_tpl->tpl_vars['userlogintypes']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['userlogintypes']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"]
			<?php } else { ?>
					["<?php echo $_smarty_tpl->tpl_vars['userlogintypes']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['id'];?>
", "<?php echo $_smarty_tpl->tpl_vars['userlogintypes']->value[(isset($_smarty_tpl->tpl_vars['__smarty_section_index']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_index']->value['index'] : null)]['name'];?>
"],
			<?php }?>
			<?php
}
}
?>
			
		]
	});

	<?php if ($_smarty_tpl->tpl_vars['optioncfs']->value) {?>
	var storeList = new Ext.data.Store({
		id: 'storeList',
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AjaxAPI.callback&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&cmd=STORESCOMBO', method: 'GET'}),
		reader: new Ext.data.ArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
		    {name: 'id', mapping: 2},
			{name: 'name', mapping: 1}
			])
		)
	});
	<?php }?>

	<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
	var productionSiteStore = new Ext.data.Store({
		id: 'productionSiteStore',
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AjaxAPI.callback&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&cmd=PRODSITESCOMBO', method: 'GET'}),
		reader: new Ext.data.ArrayReader({
			idIndex: 0},
			Ext.data.Record.create([
		   		{name: 'id', mapping: 2},
				{name: 'name', mapping: 1}
			])
		)
	});
	<?php }?>

	var brandStore = new Ext.data.Store({
		id: 'brandStore',
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AjaxAPI.callback&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&cmd=BRANDCOMBO&userpage=1'}),
		reader: new Ext.data.ArrayReader({
			idIndex: 0},
			Ext.data.Record.create([
		    	{name: 'id', mapping: 0},
				{name: 'name', mapping: 1}
			])
		)
	});

	var format = ((document.location.protocol != 'https:') ? 1 : 0);

	var dialogFormPanelObj = new Ext.taopix.FormPanel({
		id: 'usersForm',
		header: false,
		frame:true,
		width: 550,
		labelWidth: 110,
		defaultType: 'textfield',
		autoHeight: true,
		items: [
			{
				xtype: 'tabpanel',
				id: 'maintabpanel',
				deferredRender: false,
				layoutOnTabChange: true,
				activeTab: 0,
				autoWidth: true,
				height: 280,
				shadow: true,
				plain:true,
				defaults:{frame: false, autoScroll: true, hideMode:'offsets', layout: 'form', labelWidth: 140, bodyStyle:'padding:10px; background-color: #eaf0f8;'},
				items: [
					{ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDetails');?>
",
						items:
						[
							// Prevent Safari 7.0xxx to populated the main password field
							{
								xtype: 'textfield',
								width: 350,
            					fieldLabel: "",
            					id:'fakepasswordforsafari',
            					name: 'fakepasswordforsafari',
            					inputType: 'password',
            					allowBlank: true,
            					post: false,
								style: 'position: absolute; top: -5000px;'
        					},
							// End -> Prevent Safari 7.0xxx to populated the main password field
							{
								xtype: 'textfield',
								width: 350,
            					fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelFirstName');?>
",
            					id: 'contactfname',
            					name: 'contactfname',
            					allowBlank: false,
            					<?php if ($_smarty_tpl->tpl_vars['isEdit']->value == 1) {?>
              						value: "<?php echo $_smarty_tpl->tpl_vars['contactfname']->value;?>
",
            					<?php }?>
            					width: 275,
            					maxLength: 200,
            					post: true
        					},
        					{
								xtype: 'textfield',
								width: 350,
            					fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLastName');?>
",
            					id: 'contactlname',
           						name: 'contactlname',
            					value:"<?php echo $_smarty_tpl->tpl_vars['contactlname']->value;?>
",
            					width: 275,
            					maxLength: 200,
            					post: true
        					},
        					{
								xtype: 'textfield',
								width: 350,
            					fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUserName');?>
",
            					id: 'login_user',
            					name: 'login_user',
            					allowBlank: false,
            					<?php if ($_smarty_tpl->tpl_vars['isEdit']->value == 1) {?>
              						value: "<?php echo $_smarty_tpl->tpl_vars['login']->value;?>
",
            					<?php }?>
            					width: 275,
            					maxLength: 50,
            					post: true
        					},
        					{
								xtype: 'textfield',
								width: 350,
            					fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelPassword');?>
",
            					id:'password_user',
            					name: 'password_user',
            					inputType: 'password',
            					allowBlank:false,
            					<?php if ($_smarty_tpl->tpl_vars['isEdit']->value == 1) {?>
              						value: "<?php echo $_smarty_tpl->tpl_vars['password']->value;?>
",
            					<?php }?>
            					width: 275,
            					listeners:{
									blur:{
										fn: function()
										{
											if (format == 1)
											{
												md5HashFunction();
											}
										}
									}
								},
            					post: true
        					},
							{
            					xtype: 'checkbox',
            					id: 'canmodifypassword',
								name: 'canmodifypassword',
								boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelModifyPassword');?>
",
								<?php if ($_smarty_tpl->tpl_vars['canmodifypasswordchecked']->value == 1) {?>
									checked: true,
								<?php } else { ?>
									checked: false,
								<?php }?>
								post: true
        					},
							{
								xtype: 'textfield',
								width: 350,
            					fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEmailAddress');?>
",
            					id: 'email',
            					name: 'email',
            					vtype:'email',
            					value:"<?php echo $_smarty_tpl->tpl_vars['email']->value;?>
",
            					validationDelay: 7000,
            					width: 275,
            					post: true
        					},
        					new Ext.form.ComboBox({
								id: 'logintype',
								name: 'logintype',
								width:275,
								mode: 'local',
								editable: false,
								hideLabel: false,
								forceSelection: true,
								selectOnFocus: true,
								triggerAction: 'all',
								fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelTypeLogin');?>
",
								store: loginTypeStore,
								listeners:{
									select:{
										fn: function()
										{
											setLoginForm(this, '', '');
											storeDistOwner = '';
											Ext.getCmp('dialog').syncShadow();
										}
									}
								},
								valueField: 'id',
								displayField: 'name',
								useID: true,
								<?php if ($_smarty_tpl->tpl_vars['isEdit']->value == 0) {?>
        							value: "<?php echo $_smarty_tpl->tpl_vars['defaultLoginTypeValue']->value;?>
",
								<?php } else { ?>
									value: "<?php echo $_smarty_tpl->tpl_vars['usertype']->value;?>
",
        						<?php }?>
								allowBlank: false,
								post: true
							}),

							<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			 				new Ext.taopix.CompanyCombo({
				 				id: 'company',
								name: 'company',
								width:275,
								fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
",
								hideLabel:false,
								allowBlank:false,
								displayField: 'name',
       							valueField: 'code',
								<?php if ($_smarty_tpl->tpl_vars['isEdit']->value == 1) {?>
									defvalue: "<?php echo $_smarty_tpl->tpl_vars['companycode']->value;?>
",
			 					<?php }?>
								options: {ref: "<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
", storeId: 'companyStore', includeShowAll: '0', onchange: function(){}}
							}),
							<?php }?>
							<?php if ($_smarty_tpl->tpl_vars['optioncfs']->value) {?>
							new Ext.form.ComboBox({
								id: 'store',
								name: 'store',
								width:275,
								mode: 'local',
								editable: false,
								hideLabel: false,
								forceSelection: true,
								allowBlank:false,
								selectOnFocus: true,
								triggerAction: 'all',
								<?php if ($_smarty_tpl->tpl_vars['optioncfs']->value) {?>
									<?php if ($_smarty_tpl->tpl_vars['isEdit']->value == 1) {?>
										<?php if ($_smarty_tpl->tpl_vars['usertype']->value == $_smarty_tpl->tpl_vars['TPX_LOGIN_DISTRIBUTION_CENTRE_USER']->value) {?>
											fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDistributionCentre');?>
",
										<?php } else { ?>
											fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStoreFieldLabel');?>
",
										<?php }?>
									<?php }?>
								<?php }?>
								store: storeList,
								valueField: 'id',
								displayField: 'name',
								useID: true,
								post: true
							}),
							<?php }?>
							new Ext.form.ComboBox({
								id: 'brand',
								name: 'brand',
								width:275,
								mode: 'local',
								editable: false,
								hideLabel: false,
								forceSelection: true,
								allowBlank:false,
								selectOnFocus: true,
								triggerAction: 'all',
								fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelBrand');?>
",
								store: brandStore,
								valueField: 'id',
								displayField: 'name',
								useID: true,
								allowBlank: false,
								post: true
							})
							<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
        					,new Ext.form.ComboBox({
								id: 'productionsite',
								name: 'productionsite',
								width:275,
								mode: 'local',
								editable: false,
								hideLabel: false,
								allowBlank:false,
								forceSelection: true,
								selectOnFocus: true,
								triggerAction: 'all',
								fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProductionSite');?>
",
								store: productionSiteStore,
								valueField: 'id',
								displayField: 'name',
								useID: true,
								post: true
							})
       						<?php }?>
        				]
					},
					{ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleAccessRestrictions');?>
",
						items:
						[
							{
								xtype:'textarea',
								id: 'defultipaccesslist',
								name: 'defultipaccesslist',
								fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDefaultIPAccessList');?>
",
								width: 360,
								height: 55,
								post: true,
								disabled: true,
								value: defaultIpAccessList
							},
							{
								xtype: 'combo',
								id: 'ipAccessType',
								name: 'ipAccessType',
								width:275,
								mode: 'local',
								editable: false,
								forceSelection: true,
								selectOnFocus: true,
								triggerAction: 'all',
								fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelIPAccessType');?>
",
								store: new Ext.data.ArrayStore({
        							fields: [
           								{name: 'id'},
           								{name: 'name'}
        							],
        							data: [[0, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUseDefault');?>
"], [1, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelAppendDefault');?>
"], [2, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelOverrideDefault');?>
"]]
        						}),
        						post: true,
								valueField: 'id',
								displayField: 'name',
								useID: true,
								post: true,
								listeners: { 'select': setIpAccessList },
								value: ipAccessType
							},
							{
								xtype:'textarea',
								id: 'ipaccesslist',
								name: 'ipaccesslist',
								fieldLabel: '',
								width: 360,
								height: 55,
								post: true,
								maskRe: /^(\d{1,3})|(\.)|(,)|(\s)|(\*)$/, enableKeyEvents: true,
								listeners: {
        							render: function(p) {
           		 						p.getEl().dom.parentNode.parentNode.previousSibling.style.marginBottom = '1px';
        							},
        							keyup: function(field, event)
        							{
        								var userIPAccessListObj = Ext.getCmp('useripaccesslist');
        								if (Ext.getCmp('ipAccessType').getValue() == 1)
										{
											userIPAccessListObj.setValue(defaultIpAccessList+', '+field.getValue());
										}
										else
										{
											userIPAccessListObj.setValue(field.getValue());
        								}
        							}
    							}
							},
							{
								xtype:'textarea',
								id: 'useripaccesslist',
								name: 'useripaccesslist',
								fieldLabel:  "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelUserIPAccessList');?>
",
								width: 360,
								height: 55,
								post: true,
								disabled: true
							}
						]
					}
				]
			}
		]
	});


	function setIpAccessList()
	{
		var ipAccessListObj = Ext.getCmp('ipaccesslist');
		var userIPAccessListObj = Ext.getCmp('useripaccesslist');

		if (Ext.getCmp('ipAccessType').getValue() == 0)
		{
			userIPAccessListObj.setValue(defaultIpAccessList);
			ipAccessListObj.setValue(defaultIpAccessList);
			ipAccessListObj.disable();
			ipAccessList = '';
		}
		else
		{
			ipAccessListObj.setValue(ipAccessList);
			ipAccessListObj.enable();

			if (Ext.getCmp('ipAccessType').getValue() == 1)
			{
				userIPAccessListObj.setValue(defaultIpAccessList + ', ' + ipAccessListObj.getValue());
			}
			else
			{
				userIPAccessListObj.setValue(ipAccessListObj.getValue());
			}
		}
	}

	/* save functions */
	function addsaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminUsers.add&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
';
		var fp = Ext.getCmp('usersForm'), form = fp.getForm();
		var paramArray = new Object();
		paramArray['isactive'] = '';

		if (Ext.getCmp('isactive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}

		paramArray['format'] = ((document.location.protocol != 'https:') ? 1 : 0);

		Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
", saveCallback);
	}

	/* save functions */
	function editsaveHandler(btn, ev)
	{
		var selectID = Ext.taopix.gridSelection2IDList(gMainWindowObj.findById('maingrid'));

		var submitURL = 'index.php?fsaction=AdminUsers.edit&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&id=' + selectID;
		var fp = Ext.getCmp('usersForm'), form = fp.getForm();
		var paramArray = new Object();
		paramArray['isactive'] = '';
		if (Ext.getCmp('isactive').checked)
		{
			paramArray['isactive'] = '1';
		}
		else
		{
			paramArray['isactive'] = '0';
		}

		paramArray['format'] = ((document.location.protocol != 'https:') ? 1 : 0);

		Ext.taopix.formPanelPost(fp, form, paramArray, submitURL, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
", saveCallback);
	}

	function saveCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var gridObj = gMainWindowObj.findById('maingrid');
			var dataStore = gridObj.store;

			gridObj.store.reload();
			gDialogObj.close();
		}
		else
		{
			icon = Ext.MessageBox.WARNING;

			Ext.MessageBox.show({
				title: pActionData.result.title,
				msg: pActionData.result.msg,
				buttons: Ext.MessageBox.OK,
				icon: icon
			});
		}
	}

	/* create modal window for add and edit */
	gDialogObj = new Ext.Window({
		id: 'dialog',
	  	closable:false,
	  	plain:true,
	  	modal:true,
	  	draggable:true,
	  	resizable:false,
	  	layout: 'fit',
	  	autoHeight:false,
	  	autoHeight: true,
	  	width: 560,
	  	items: dialogFormPanelObj,
	  	listeners: {
			'close': {
				fn: function(){
					userEditWindowExists = false;
				}
			}
		},
	  	cls: 'left-right-buttons',
	  	title: "<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
",
	  	buttons:
		[
			new Ext.form.Checkbox({
				id: 'isactive',
				name: 'isactive',
				boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
",
				post: true,
				cls: 'x-btn-left',
      			ctCls: 'width_100',
      			<?php if ($_smarty_tpl->tpl_vars['activechecked']->value == 1) {?>
					checked: true
				<?php } else { ?>
					checked: false
				<?php }?>
			}),
			{
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
",
				handler: function(){ Ext.getCmp('dialog').close();},
				cls: 'x-btn-right'
			},
			{
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
",
				id: 'addEditButton',
				cls: 'x-btn-right',
				/*
				 *	Use listener instead of handler, as handler seems to have an issue where the hover class is not removed from the button when opening a new window
				 *  which also stops the dialog opening for a second time.
				 */
				listeners:
				{
					click:
					{
						fn: function()
						{
							/* Check the form is valid before authenticating. */
							if (Ext.getCmp('usersForm').getForm().isValid())
							{
								/* Reauthenticate the logged in user to make the changes */
								var reason = '';
								var successCallback = function() {};
								
								<?php if ($_smarty_tpl->tpl_vars['isEdit']->value == 0) {?>
									reason = 'USER-ADD';
									successCallback = addsaveHandler;
								<?php } else { ?>
									reason = 'USER-EDIT';
									successCallback = editsaveHandler;
								<?php }?>

								showAdminReauthDialogue(
								{
									ref: <?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
,
									reason: reason,
									title: Ext.taopix.ReauthenticationDialog.strings.titleAuthenticateToSave,
									success: successCallback
								});
							}
						}
					}
				},
				<?php if ($_smarty_tpl->tpl_vars['isEdit']->value == 0) {?>
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
"
				<?php } else { ?>
				text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdate');?>
"
				<?php }?>
			}
		]
	});

	<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
	<?php if ($_smarty_tpl->tpl_vars['isEdit']->value == 0) {?>
		Ext.getCmp('company').store.on({'load': function(){
			Ext.getCmp('company').setValue(Ext.getCmp('company').store.getAt(0).get('code'));
			}
		});
	<?php }?>
	<?php }?>

	var mainPanel = Ext.getCmp('dialog');
	Ext.getCmp('brand').hide();
	<?php if ($_smarty_tpl->tpl_vars['isEdit']->value == 0) {?>
		<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			Ext.getCmp('productionsite').hide();
			Ext.getCmp('company').hide();
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['optioncfs']->value) {?>
			Ext.getCmp('store').hide();
		<?php }?>

		<?php if ($_smarty_tpl->tpl_vars['loggedInAs']->value == $_smarty_tpl->tpl_vars['TPX_LOGIN_SYSTEM_ADMIN']->value) {?>
			var loginTypeCombo = Ext.getCmp('logintype');
			setLoginForm(loginTypeCombo, '', '');
		<?php }?>

		<?php if ($_smarty_tpl->tpl_vars['loggedInAs']->value == $_smarty_tpl->tpl_vars['TPX_LOGIN_COMPANY_ADMIN']->value) {?>
			var loginTypeCombo = Ext.getCmp('logintype');
			setLoginForm(loginTypeCombo, '', '');
		<?php }?>

		<?php if ($_smarty_tpl->tpl_vars['loggedInAs']->value == $_smarty_tpl->tpl_vars['TPX_LOGIN_SITE_ADMIN']->value) {?>
			var loginTypeCombo = Ext.getCmp('logintype');
			setLoginForm(loginTypeCombo, '', '');
		<?php }?>

	<?php } else { ?>
		<?php if ($_smarty_tpl->tpl_vars['usertype']->value != $_smarty_tpl->tpl_vars['TPX_LOGIN_STORE_USER']->value || $_smarty_tpl->tpl_vars['usertype']->value != $_smarty_tpl->tpl_vars['TPX_LOGIN_DISTRIBUTION_CENTRE_USER']->value) {?>
				var loginTypeCombo = Ext.getCmp('logintype');
				setLoginForm(loginTypeCombo, '', '');
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['userid']->value == 1) {?>
			Ext.getCmp('logintype').disable();
			Ext.getCmp('isactive').disable();
		<?php }?>
	<?php }?>

 	setIpAccessList();

	mainPanel.show();
}



<?php }
}
