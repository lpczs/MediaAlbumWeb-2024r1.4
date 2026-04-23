<?php
/* Smarty version 4.5.3, created on 2026-03-07 03:44:13
  from 'C:\TAOPIX\MediaAlbumWeb\templates\admin\products\productsconfig.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69ab9f0d7ffa24_93106461',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fc968b1ee1a7e45c369be85f53869489e4647e2d' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\admin\\products\\productsconfig.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69ab9f0d7ffa24_93106461 (Smarty_Internal_Template $_smarty_tpl) {
echo $_smarty_tpl->tpl_vars['localizedcodesjavascript']->value;?>

<?php echo $_smarty_tpl->tpl_vars['localizednamesjavascript']->value;?>

<?php echo $_smarty_tpl->tpl_vars['languagecodesjavascript']->value;?>

<?php echo $_smarty_tpl->tpl_vars['languagenamesjavascript']->value;?>


var gLastPricingModel = -1;
var gPricingWindowExists = false;
var gPricingEditWindowExists = false;
var gAjaxCallRequired = false;
var gInEditMode = false;
var paramArray = {};
var newNodeID = 0;
var dummyPriceLinkID = 0;
var gDeletedItemsArray = new Array();
var processedNodeIDs = [];
var selectedProductLinkID = <?php echo $_smarty_tpl->tpl_vars['linkedproductid']->value;?>

<?php if ($_smarty_tpl->tpl_vars['canlink']->value || $_smarty_tpl->tpl_vars['companycode']->value != '') {?>
	var hidePreview = true;
<?php } else { ?>
	var hidePreview = false;
<?php }?>



var getLocalisedData = function(populateLangs, gLocalizedNamesArray, gLocalizedCodesArray)
{
	var langListStore = [], dataList = [];
	populateLangs = 1;

	for (var i =0; i < gAllLanguageCodesArray.length; i++)
	{
		var languageName = "";
		var languageCode = "";
		var languageNameIndex = ArrayIndexOf(gAllLanguageCodesArray, gLocalizedCodesArray[i]);
		if (languageNameIndex > -1)
		{
			languageName = gAllLanguageNamesArray[languageNameIndex];
			languageCode = gAllLanguageCodesArray[languageNameIndex];
		}
		if ((languageName) && (languageName!=undefined)) dataList.push([languageCode,languageName,gLocalizedNamesArray[i]]);

		if (populateLangs == 1)
		{
			if (ArrayIndexOf(gLocalizedCodesArray, gAllLanguageCodesArray[i]) == -1)
			{
				langListStore.push([gAllLanguageCodesArray[i],gAllLanguageNamesArray[i]]);
			}
		}
	}
	return {'langListStore': langListStore, 'dataList': dataList};
};

function initialize(pParams)
{
	function ajaxFailure()
	{
		Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "Could Not Connect", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
	}

	/* Create Pricing Window */
	function clearGrouping(v)
	{
		if(v.checked)
		{
			gridProductConfigPricingDataStoreObj.groupBy('productcode');
		}
		else
		{
			gridProductConfigPricingDataStoreObj.clearGrouping();
		}
	}

	function forceAlphaNumeric()
	{
		var code = Ext.getCmp('sectioncode').getValue();

   		code = code.toUpperCase();
    	code = code.replace(/[^A-Z_0-9\-]+/g, "");

   	 	Ext.getCmp('sectioncode').setValue(code);
	}

	function readTree(pChildren, pPath, pDataArray, pSortOrder)
	{
		var len = pChildren.length;

		for (var i = 0; i < len; ++i)
        {
        	var theChild = pChildren[i];

        	if (theChild.attributes.issection == false)
            {
				var priceLinkIds = theChild.attributes.pricelinkparentids.split(',');
				
				var isTreeNodeIDInPriceLinkIDs = false;
				var dummyRecordRequired = true;
				
				/* For each component get the pricelink ids assigned to it. Then loop through each price link to see if the price data has been modified. */
        		for (var j = 0; j < priceLinkIds.length; j++)
        		{
        			/* If the price data has been modified then add it to the pDataArray to be posted across to the server */
	    			if (theChild.attributes.initialized == '1' && theChild.attributes.pricelinkparentids != '' && paramArray[priceLinkIds[j]] != undefined)
	    			{
						var isDummyNode = 0;
	    				/* This is a saved tree node */
	    				if (theChild.id > 0)
	    				{
							if (paramArray[priceLinkIds[j]].productcode == "")
							{
								// we are processing a default price record. We do not want to update the sort order so we can ignore it.
								continue;							
							}

							var pricelistid = paramArray[priceLinkIds[j]].pricelistid;

							/* check to see if this is real saved price data and if it is set the action to 1 to update current price data */
							if (priceLinkIds[j] > 0)
							{
								/* Flag to update existing record */
								var action = 1;
							}
							else if (priceLinkIds[j] < 0)
							{
								/* Flag to insert new record */
								var action = 0;

								// if the pricelinkid is less than 0 then we need to check to see if the record in paramarray was added as a dummy node
								// rather than a record from adding a new price. If it is then we know we must add the record as a dummy node
								if (paramArray[priceLinkIds[j]].dummypricelinkrecordfornode == 1)
								{
									isDummyNode = 1;
								}
							}

							/* check to see if the prcicelink ID actually matches that of the tree node. */
							/* if it is then set the flag to true so we no that the price record on the node is not just a default price */
							if (theChild.attributes.parentid == priceLinkIds[j])
							{
								isTreeNodeIDInPriceLinkIDs = true;
							}
						}
						else
						{
							if (paramArray[priceLinkIds[j]].productcode == "")
							{
								var action = 0;
								var pricelistid = -1;
								isDummyNode = 1;
								
								// if the product code is empty then we are processing a dummynode
								// therefore we do not want to create another.
								if (dummyRecordRequired)
								{
									dummyRecordRequired = false;
								}
							}
							else
							{
								var action = 0;
								var pricelistid =  paramArray[priceLinkIds[j]].pricelistid;
								isDummyNode = 0;

								// if dummyRecordRequired is true then we know that we are adding a price for a componet that has been dragged onto
								// the tree for the first therefore the first record we create must be inserted as a dummy record for default pricing
								if (dummyRecordRequired)
								{
									isDummyNode = 1;
									dummyRecordRequired = false;
								}
							}
						}

	    				pDataArray[componentCounter] = {};
	    				pDataArray[componentCounter].id = priceLinkIds[j];
		        		pDataArray[componentCounter].path = pPath;
		        		pDataArray[componentCounter].componentcode = theChild.attributes.code;
		        		pDataArray[componentCounter].isdefault = theChild.attributes.isdefault;
		        		pDataArray[componentCounter].action = action;
		        		pDataArray[componentCounter].categorycode = theChild.attributes.categorycode;
		        		pDataArray[componentCounter].sectioncode = theChild.attributes.sectioncode;
						pDataArray[componentCounter].groupcodes = paramArray[priceLinkIds[j]].groupcodes;
		        		pDataArray[componentCounter].price = paramArray[priceLinkIds[j]].price;
		        		pDataArray[componentCounter].quantityisdropdown = paramArray[priceLinkIds[j]].quantityisdropdown;
		        		pDataArray[componentCounter].pricingmodel = paramArray[priceLinkIds[j]].pricingmodel;
		        		pDataArray[componentCounter].pricedescription = paramArray[priceLinkIds[j]].pricedescription;
		        		pDataArray[componentCounter].priceadditionalinfo = paramArray[priceLinkIds[j]].priceladditionalinfo;
		        		pDataArray[componentCounter].ispricelist = paramArray[priceLinkIds[j]].ispricelist;
		        		pDataArray[componentCounter].pricelistid = pricelistid;
		        		pDataArray[componentCounter].inispricelist = paramArray[priceLinkIds[j]].inispricelist;
		        		pDataArray[componentCounter].inpricelistid = paramArray[priceLinkIds[j]].inpricelistid;
		        		pDataArray[componentCounter].taxcode = paramArray[priceLinkIds[j]].taxcode;
		        		pDataArray[componentCounter].active = paramArray[priceLinkIds[j]].active;
		        		pDataArray[componentCounter].sortorder = pSortOrder;
		        		pDataArray[componentCounter].isdummynode = isDummyNode;
		        		pDataArray[componentCounter].modified = paramArray[priceLinkIds[j]].modified;
		        		pDataArray[componentCounter].inheritparentqty = paramArray[priceLinkIds[j]].inheritparentqty;
		        		componentCounter++;
						pSortOrder++;
						processedNodeIDs.push(priceLinkIds[j]);
	    			}
	    			else if ((theChild.attributes.initialized == '0') && ((theChild.attributes.pricelinkparentids == '0') || (priceLinkIds[j] < 0)))
	    			{
						pDataArray[componentCounter] = {};
	    				pDataArray[componentCounter].id = priceLinkIds[j];
		        		pDataArray[componentCounter].path = pPath;
		        		pDataArray[componentCounter].componentcode = theChild.attributes.code;
		        		pDataArray[componentCounter].isdefault = theChild.attributes.isdefault;
		        		pDataArray[componentCounter].action = 0;
		        		pDataArray[componentCounter].categorycode = theChild.attributes.categorycode;
		        		pDataArray[componentCounter].sectioncode = theChild.attributes.sectioncode;
		        		pDataArray[componentCounter].groupcodes = '';
		        		pDataArray[componentCounter].price = '';
		        		pDataArray[componentCounter].quantityisdropdown = 0;
		        		pDataArray[componentCounter].pricingmodel = theChild.attributes.pricingmodel;
		        		pDataArray[componentCounter].pricedescription = '';
		        		pDataArray[componentCounter].priceadditionalinfo = '';
		        		pDataArray[componentCounter].ispricelist = 0;
		        		pDataArray[componentCounter].pricelistid = -1;
		        		pDataArray[componentCounter].inispricelist = 0;
		        		pDataArray[componentCounter].inpricelistid = 0;
		        		pDataArray[componentCounter].taxcode = '';
		        		pDataArray[componentCounter].active = 1;
		        		pDataArray[componentCounter].sortorder = pSortOrder;
		        		pDataArray[componentCounter].isdummynode = 1;
		        		pDataArray[componentCounter].modified = 0;
		        		pDataArray[componentCounter].inheritparentqty = theChild.attributes.inheritparentqty;
		        		componentCounter++;
						pSortOrder++;
						processedNodeIDs.push(priceLinkIds[j]);
	    			}
	    			else
	    			{
						/* if not modified we still need to update the sortorders of all pricelinks */
	    				pDataArray[componentCounter] = {};
	    				pDataArray[componentCounter].id = priceLinkIds[j];
	    				pDataArray[componentCounter].action = 2;
		        		pDataArray[componentCounter].sortorder = pSortOrder;
		        		pDataArray[componentCounter].isdefault = theChild.attributes.isdefault;
		        		pDataArray[componentCounter].inheritparentqty = theChild.attributes.inheritparentqty;
		        		componentCounter++;
						pSortOrder++;
						processedNodeIDs.push(priceLinkIds[j]);
	    			}
        		}

        		/* if the priceLinkIdIsTreeNodeId is still set to false then we know the node only has a default price record */
				/* therefore we only need to update the sortorder of the node */
				/* we must also make sure we have not processed the id before to prevent a new record being added which also causes the sortorders to become corrupt */
        		if ((! isTreeNodeIDInPriceLinkIDs) && (processedNodeIDs.indexOf('' + theChild.attributes.parentid) == -1))
        		{
        				pDataArray[componentCounter] = {};
	    				pDataArray[componentCounter].id = '' + theChild.attributes.parentid;
	    				pDataArray[componentCounter].action = 2;
		        		pDataArray[componentCounter].sortorder = pSortOrder;
		        		pDataArray[componentCounter].isdefault = theChild.attributes.isdefault;
		        		pDataArray[componentCounter].inheritparentqty = theChild.attributes.inheritparentqty;
		        		componentCounter++;
						pSortOrder++;						
        		}
            }

        	if (theChild.childNodes)
        	{
        		if (theChild.attributes.issection == true)
        		{
        			pSortOrder = readTree(theChild.childNodes, pPath + '$' + theChild.attributes.sectioncode + '\\', pDataArray, pSortOrder);
        		}
        		else
        		{
        			pSortOrder = readTree(theChild.childNodes, pPath + theChild.attributes.componentcode + '\\' , pDataArray, pSortOrder);
        		}
        	}

        }

		return pSortOrder;
   	}

	/* save functions */
	function addsaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminProducts.saveProductConfig&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
';
		var fp = Ext.getCmp('productConfigForm'), form = fp.getForm();
		var submit = true;

		var postParamArray = {};
		var treeDataArray = {};
		var treeRootNode = Ext.getCmp('tree').getRootNode();

		// only save the component tree if we have not linked a product
		if (selectedProductLinkID == 0)
		{
			componentCounter = 0;
			sortOrder = 1;
			readTree(treeRootNode.childNodes, '', treeDataArray, sortOrder);
			processedNodeIDs = [];

			/* serialize the tree and javascript pricelink array data */
			var serializedTreeData = Ext.encode(treeDataArray);
			postParamArray['serializedtreedata'] = serializedTreeData;
			postParamArray['pricelinkidstodelete'] = gDeletedItemsArray;
			postParamArray['linkedproductcode'] = '';
		}
		else
		{
			var linkedProductCode = productLinkingCombo.getValue();
			postParamArray['serializedtreedata'] = '{}';
			postParamArray['pricelinkidstodelete'] = '';
			postParamArray['linkedproductcode'] = linkedProductCode;
		}

		if (submit)
		{
			Ext.taopix.formPanelPost(fp, form, postParamArray, submitURL, "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
", saveCallback);
		}
	}

	function saveCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			if(typeof(gPricingEditWindow) !== 'undefined')
			{
				gPricingEditWindow.close();
			}


			if(typeof(Ext.getCmp('productConfigPricingGridWindow')) !== 'undefined')
			{
				Ext.getCmp('productConfigPricingGridWindow').close();
			}

			gPricingWindowExists = false;
			editConfigHandler('saveConfiguration');
		}
		else
		{
			icon = Ext.MessageBox.WARNING;
			Ext.MessageBox.show({ title: pActionData.result.title, msg: pActionData.result.msg,	buttons: Ext.MessageBox.OK, icon: icon });
		}
	}

	function treeLoadCallback(pNode)
	{

	}

	function onDeleteTreeItems()
	{
		var nodeToRemove = Ext.getCmp('tree').getSelectionModel().getSelectedNode();

		if (nodeToRemove.attributes.issection)
		{
			var message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ConfirmDeleteSection');?>
";
		}
		else
		{
			var message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ConfirmDeleteComponent');?>
";
		}

		Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", message, onDeleteTreeItemsResult);
	}

	function onDeleteTreeItemsResult(btn)
	{
		if (btn == 'yes')
		{
			var selectedNode = Ext.getCmp('tree').getSelectionModel().getSelectedNode();
			var parentNode = selectedNode.parentNode;
			var nextSibling = selectedNode.nextSibling;
			var previousSibling = selectedNode.previousSibling;
			var childNodes = selectedNode.childNodes;

			/*If we are removing a section we need to loop through the childNodes marking them as removed and marking any childnodes they hasve as removed too*/
			if (selectedNode.attributes.issection)
			{
				markNodesAsRemoved(childNodes);
				selectedNode.remove(true);
			}
			else
			{
				if (selectedNode.attributes.islist == '0')
				{
					if (selectedNode.attributes.pricelinkparentids != '0')
					{
						gDeletedItemsArray.push(selectedNode.attributes.pricelinkparentids.split(','));
					}

					selectedNode.remove(true);
				}
				else
				{
					/* if the selectedNode has children loop through and remove all childNodes and grandChildrenNodes. */
					if (childNodes.length > 0)
					{
						markNodesAsRemoved(childNodes);

						/* If this was the only node in the section then remove the node and its parent section node otherwise just remove the selected node */
						if (nextSibling == null && previousSibling == null)
						{
							if (selectedNode.attributes.pricelinkparentids != '0')
							{
								gDeletedItemsArray.push(selectedNode.attributes.pricelinkparentids.split(','));
							}

							selectedNode.remove(true);
							parentNode.remove(true);
						}
						else
						{
							if (selectedNode.attributes.pricelinkparentids != '0')
							{
								gDeletedItemsArray.push(selectedNode.attributes.pricelinkparentids.split(','));
							}

							selectedNode.remove(true);
						}
					}
					else
					{
						/* If the selected node did not have any children and it was the only node in the section then remove the node and its parent section. Otherwise remove selectedNode */
						if (nextSibling == null && previousSibling == null)
						{
							if (selectedNode.attributes.pricelinkparentids != '0')
							{
								gDeletedItemsArray.push(selectedNode.attributes.pricelinkparentids.split(','));
							}

							selectedNode.remove(true);
							parentNode.remove(true);

						}
						else
						{
							if (selectedNode.attributes.pricelinkparentids != '0')
							{
								gDeletedItemsArray.push(selectedNode.attributes.pricelinkparentids.split(','));
							}

							selectedNode.remove(true);
						}
					}
				}
			}
		}
	}

	function markNodesAsRemoved(pChildren)
	{
		var len = pChildren.length;

		for (var i = 0; i < len; ++i)
        {
        	var theChild = pChildren[i];

        	if (theChild.attributes.pricelinkparentids != '0' && !theChild.attributes.issection)
			{
        		gDeletedItemsArray.push(theChild.attributes.pricelinkparentids.split(','));
			}

        	if (theChild.childNodes)
        	{
        		gDeletedItemsArray = markNodesAsRemoved(theChild.childNodes);
        	}
        }

		return gDeletedItemsArray;
	}

	function onDeleteCallback()
	{

	}

	function onDeletePriceRecord()
	{
		var message = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ConfirmDeleteComponentPrice');?>
";

		Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelConfirmation');?>
", message, onDeletePriceResult);
	}

	function onDeletePriceResult(btn)
	{
		if (btn == 'yes')
		{
			var gridObj = Ext.getCmp('productConfigPricingGrid');
			var dataStore = gridObj.store;
			var selRecords = gridObj.getSelectionModel().getSelections();
			var selRecordsCount = selRecords.length;
			var selectedNodeExistingPriceLinkIdsArray = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.pricelinkparentids.split(',');

			for (rec = 0; rec < selRecordsCount; rec++)
			{
				// find the pricelink that we are removing
				var priceLinkIndex = selectedNodeExistingPriceLinkIdsArray.indexOf('' + selRecords[rec].id);

				// if we have found the id then we must remove it from the current pricelink id that are currently attached to the node
				if (priceLinkIndex > -1)
				{
					selectedNodeExistingPriceLinkIdsArray.splice(priceLinkIndex, 1);
				}
				
				// we need to push the record ids into the deleted items array so that they are deleted from the database.
				gDeletedItemsArray.push(selRecords[rec].id);

				dataStore.remove(selRecords[rec]);
			}
			
			// re build the data store for the grid with the new correct filtered pricelink ids
			buildPriceStoreData(selectedNodeExistingPriceLinkIdsArray);

			// update the node with the new correct filtered pricelink ids
			Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.pricelinkparentids = selectedNodeExistingPriceLinkIdsArray.join(',');
		}
	}

	function onNodeClick(obj)
	{
		treeRootNode = tree.getRootNode();

		if (selectedProductLinkID == 0)
		{
			Ext.getCmp('deletesectionbutton').enable();
		}

		if (!obj.attributes.issection)
		{
			Ext.getCmp('pricingbutton').enable();

			if (selectedProductLinkID == 0)
			{
				Ext.getCmp('send_to_top').enable();
				Ext.getCmp('send_to_Bottom').enable();
				Ext.getCmp('move_up').enable();
				Ext.getCmp('move_down').enable();
				Ext.getCmp('defaultItemButton').enable();

				if (obj.attributes.islist == '1')
				{
					/*check if the node is already set as default if it is disable the set default button*/
					if (obj.attributes.isdefault == '1')
					{
						Ext.getCmp('defaultItemButton').disable();
					}
				}
			}
		}
		else
		{
			Ext.getCmp('defaultItemButton').disable();
			Ext.getCmp('pricingbutton').disable();

			if ((obj.attributes.sectioncode == 'ORDERFOOTER' || obj.attributes.sectioncode == 'LINEFOOTER') || (selectedProductLinkID != 0))
			{
				Ext.getCmp('send_to_top').disable();
				Ext.getCmp('send_to_Bottom').disable();
				Ext.getCmp('move_up').disable();
				Ext.getCmp('move_down').disable();
			}
			else
			{
				Ext.getCmp('send_to_top').enable();
				Ext.getCmp('send_to_Bottom').enable();
				Ext.getCmp('move_up').enable();
				Ext.getCmp('move_down').enable();
			}
		}

		if (obj == treeRootNode)
		{
			Ext.getCmp('deletesectionbutton').disable();
			Ext.getCmp('pricingbutton').disable();
			Ext.getCmp('defaultItemButton').disable();
		}
	}

	function editConfigHandler(btn)
	{
		if (btn.id == 'editconfig')
		{
			paramArray = {};

			var priceLinkCorruptionDetected = false;
			var productIDToSend = 0;
			var selectedCompanyCode = Ext.getCmp('company').getValue();

			tree.root.cascade(function (node)
			{
				// we do not need to process the root node or a section node
				if ((! node.hasOwnProperty('isRoot')) && (! node.attributes.issection))
				{
					// check each node in the tree to make sure if we need to create a dummy node.
					if (node.attributes.pricelinkparentids.indexOf('-1') != -1)
					{
						priceLinkCorruptionDetected = true;
						var newDummyPriceLinkID = createDummyPriceLinkeRecordForNode(node);
						// once the new dummy id has been created update the pricelinkids on the node to have the new dummy id
						node.attributes.pricelinkparentids.replace('-1', newDummyPriceLinkID);
					}
				}
				
			});
			
			if (priceLinkCorruptionDetected)
			{
				Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorPriceLinkCorruptionDetected');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING, maxWidth: 500});
			}

			gInEditMode = true;
			Ext.getCmp('company').disable();
			Ext.getCmp('editconfig').disable();
			Ext.getCmp('cancelconfig').enable();
			Ext.getCmp('addEditButton').enable();

			Ext.getCmp('categoriescombo').store.reload({
				params: { companycode: companyCombo.getValue() }
				});

			productLinkingCombo.store.load({
				params: { companycode: companyCombo.getValue() }
			});

			// do not enable the edit buttons if the product is linked
			if (selectedProductLinkID == 0)
			{
				categoriesCombo.enable();
				tree.enable();
				tree2.enable();
			}
			else
			{
				disableTreeForLinkedProduct();
			}
			
			 
						<?php if ($_smarty_tpl->tpl_vars['canlink']->value != 0) {?>
				if (selectedCompanyCode === 'GLOBAL')
				{
					productLinkingCombo.enable();
				}
			<?php }?>
			
		}
		else
		{
			gInEditMode = false;

			var selectedCompanyCode = Ext.getCmp('company').getValue();

			<?php if ($_smarty_tpl->tpl_vars['companycode']->value != '') {?>
			    	companyCombo.disable();
			<?php } else { ?>
				Ext.getCmp('company').enable();
			<?php }?>

			Ext.getCmp('editconfig').enable();
			Ext.getCmp('cancelconfig').disable();
			Ext.getCmp('send_to_top').disable();
			Ext.getCmp('send_to_Bottom').disable();
			Ext.getCmp('move_up').disable();
			Ext.getCmp('move_down').disable();
			Ext.getCmp('addEditButton').disable();
			tree.disable();
			tree2.disable();
			categoriesCombo.disable();
			productLinkingCombo.disable();

			Ext.Ajax.request({
				url: 'index.php?fsaction=AdminProducts.getLinkedProductCode&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
',
				success: getLinkedProductCodeSuccess,
				failure: getLinkedProductCodeFailure,
				params: { productcode: '<?php echo $_smarty_tpl->tpl_vars['productcode']->value;?>
'},
				method: 'GET'
			});

			Ext.Ajax.request({
				   url: 'index.php?fsaction=AdminProducts.refreshProductTree&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
',
				   success: productTreeRefreshSuccess,
				   failure: productTreeRefreshFailure,
				   params: { productid: <?php echo $_smarty_tpl->tpl_vars['productid']->value;?>
, companycode: selectedCompanyCode, getLinkedTree: 1},
				   method: 'GET'
				});
		}
	}

	function setDefaultHandler()
	{
		var selectedNode = Ext.getCmp('tree').getSelectionModel().getSelectedNode();
		var parentNode = selectedNode.parentNode;
		var defaultExists = false;

		if (selectedNode.attributes.islist == '1')
		{
			if (selectedNode.attributes.isdefault != '1')
			{
				var childNodes = parentNode.childNodes;
				var len = childNodes.length;

				for (var i = 0; i < len; i++)
				{
					if (childNodes[i].attributes.islist != '0')
					{
						if (childNodes[i].attributes.isdefault == '1')
						{
							defaultExists = true;
						}
					}
				}

				if (defaultExists)
				{
					for (var i = 0; i < len; i++)
					{
						if (!childNodes[i].attributes.issection)
						{
							if (childNodes[i].attributes.isdefault == '1')
							{
								/*set is default to 0 on the node as this node will no longer be the default. */
								childNodes[i].attributes.isdefault = 0;
								childNodes[i].setCls('default-list-component-normal');

								selectedNode.setCls('default-list-component-bold');
								selectedNode.attributes.isdefault = 1;

								break;
							}
						}
					}
				}
				else
				{
					selectedNode.setCls('default-list-component-bold');
					selectedNode.attributes.isdefault = 1;
				}
			}
		}
		else
		{
			if (selectedNode.attributes.isdefault == '1')
			{
				selectedNode.attributes.isdefault = 0;
				/* need to update the price javascript arrays to say that they have been modified as this childNode is no longer default*/
				selectedNode.setIconCls('checkboxComponentUnchecked');
			}
			else
			{
				selectedNode.attributes.isdefault = 1;
				selectedNode.setIconCls('checkboxComponentChecked');
			}
		}
	}

	function priceScreenEditSaveHandler()
	{
		submit = true;

		if (!Ext.getCmp('defaultLicenseKeys').checked)
		{
			if (! Ext.getCmp('licenseKeyGrid').selModel.getCount() >= 1)
		    {
		   	 	Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoLicenseKeysSelected');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });

				submit = false;
	   		 	var tabpanel = Ext.getCmp('componenttabpanel');
	   		 	tabpanel.activate('licenseKeyTab');
	    		return false;
	    	}
		}

		if (Ext.getCmp('price').isValid() != 0)
		{
    		switch (Ext.getCmp('price').isValid())
    		{
    			case 1:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeStartError1');?>
";
    				break;
    			case 2:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeStartError1');?>
";
        			break;
    			case 3:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeEndError');?>
";
        			break;
    			case 4:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeStartError2');?>
";
        			break;
    			case 5:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorComponentPriceError');?>
";
        			break;
    			case 6:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorProductPriceError');?>
";
        			break;
	    	}

			Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: ERRORMSG, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });

			submit = false;
			var tabpanel = Ext.getCmp('componenttabpanel');
    		tabpanel.activate('priceTab');
    		return false;
		}

		if (submit)
		{
			var selectedPriceLinkID = Ext.taopix.gridSelection2IDList(Ext.getCmp('productConfigPricingGrid'));
			updatePricingForNode(selectedPriceLinkID ,true);

			var pricingGridDataStore = Ext.getCmp('productConfigPricingGrid').store;
			var gridRecordCount = pricingGridDataStore.getCount();
			var gridRecordIDArray = new Array();

			/* We need to get a list of record ids that are currently in the grid so that we do not lose the default price if there is one */
			for (i = 0; i < gridRecordCount; i++)
			{
				gridRecordIDArray.push(pricingGridDataStore.data.items[i].id);
			}

			buildPriceStoreData(gridRecordIDArray);

			Ext.getCmp('pricingEditWindow').close();
		}
	}

	function priceScreenSaveHandler()
	{
		submit = true;

		if (!Ext.getCmp('defaultLicenseKeys').checked)
		{
			if (! Ext.getCmp('licenseKeyGrid').selModel.getCount() >= 1)
		    {
		   	 	Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorNoLicenseKeysSelected');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });

				submit = false;
	   		 	var tabpanel = Ext.getCmp('componenttabpanel');
	   		 	tabpanel.activate('licenseKeyTab');
	    		return false;
	    	}
		}

		if (Ext.getCmp('price').isValid() != 0)
		{
    		switch (Ext.getCmp('price').isValid())
    		{
    			case 1:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeStartError1');?>
";
    				break;
    			case 2:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeStartError1');?>
";
        			break;
    			case 3:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeEndError');?>
";
        			break;
    			case 4:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeStartError2');?>
";
        			break;
    			case 5:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorComponentPriceError');?>
";
        			break;
    			case 6:
    				ERRORMSG = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorProductPriceError');?>
";
        			break;
	    	}

			Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: ERRORMSG, buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });

			submit = false;
			var tabpanel = Ext.getCmp('componenttabpanel');
    		tabpanel.activate('priceTab');
    		return false;
		}

		if (submit)
		{
			/*Create place holder using a dummyPriceLinkID to Hold the new price informatiom */
			updatePricingForNode(--dummyPriceLinkID ,false);

			var pricingGridDataStore = Ext.getCmp('productConfigPricingGrid').store;
			var gridRecordCount = pricingGridDataStore.getCount();
			var gridRecordIDArray = new Array();

			/* Push the new dummyitem into the array so we can build the price grid */
			gridRecordIDArray.push(dummyPriceLinkID);

			/* We need to get a list of record ids that are currently in the grid so that we do not lose the default price if there is one */
			for (i = 0; i < gridRecordCount; i++)
			{
				gridRecordIDArray.push(pricingGridDataStore.data.items[i].id);
			}

			buildPriceStoreData(gridRecordIDArray);

			Ext.getCmp('pricingEditWindow').close();
		}
	}

	function onPricingEdit(btn, ev)
	{
		var localisedData = getLocalisedData(1, gLocalizedNamesArray, gLocalizedCodesArray);
		var langListStore = localisedData.langListStore;
		var dataList = localisedData.dataList;
		var parentid = 0;
		var windowTitle = Ext.getCmp('tree').getSelectionModel().getSelectedNode().text;
		var selectedPricingModel = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.pricingmodel;
		var componentCategory = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.categorycode;
		var defaultChecked = 0;
		var price = '';
		var quantityIsDropDown = 0;
		var isPriceList = 0;
		var priceListID = 0;
		var priceAdditionalInfo = '';
		var priceDescription = '';
		var assignedKeys = new Array();
		var filterKeyList = '';
		var isEdit = 0;
		var taxCode = '';
		var inheritParentQty = 0;

		if (btn.id == 'productConfigPriceEditButton')
		{
			isEdit = 1;
			parentid = Ext.taopix.gridSelection2IDList(Ext.getCmp('productConfigPricingGrid'));
			price = paramArray[parentid].price;
			quantityIsDropDown = paramArray[parentid].quantityisdropdown;
			taxCode = paramArray[parentid].taxcode;
			isPriceList = paramArray[parentid].ispricelist;
			priceListID = paramArray[parentid].pricelistid;
			priceAdditionalInfo = paramArray[parentid].priceladditionalinfo;
			priceDescription = paramArray[parentid].pricedescription;
			isActive = paramArray[parentid].active;
			groupCodes = paramArray[parentid].groupcodes;
			groupCodesArray = groupCodes.split(',');
			inheritParentQty = paramArray[parentid].inheritparentqty;

			/* Convert the groupcodes string into an array */
			assignedKeys = '([';
			for (i = 0; i < groupCodesArray.length; i++)
			{
				if (groupCodesArray[i] == '')
				{
					defaultChecked = 1;
				}

				assignedKeys = assignedKeys + "'" + groupCodesArray[i] + "'";

				if (i != groupCodesArray.length - 1)
				{
					assignedKeys = assignedKeys + ',';
				}
			}
			assignedKeys = assignedKeys + '])';
			assignedKeys = eval(assignedKeys);
		}

		if ((selectedPricingModel == 7) || (selectedPricingModel == 8) || (selectedPricingModel == 5))
		{
			windowHeight = 415;
			windowWidth = 980;
		}
		else
		{
			windowHeight = 400;
			windowWidth = 700;
		}

		/*Create Window*/
		gPricingEditWindow = new Ext.Window({
				id: 'pricingEditWindow',
			  	closable:false,
			  	plain:true,
			  	modal:true,
			  	draggable:true,
			 	resizable:false,
			  	layout: 'fit',
			  	cls: 'left-right-buttons',
			  	height: windowHeight,
			  	width: windowWidth,
			  	title: windowTitle,
			  	buttons:
				[
					{
						xtype: 'checkbox',
						id: 'isPriceActive',
						name: 'isPriceActive',
						boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
",
						post: true,
						cls: 'x-btn-left',
						ctCls: 'width_100'
					},
					{ text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
", handler: function(){ gPricingEditWindow.close();} },
					{ text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
", id: 'nodePricingAddEditButton', handler: priceScreenSaveHandler }
				]
			});

		filterKeyList = '';
		var componentCode = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.code;
		var decimalPlaces = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.decimalplaces;
		var canInherit = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.allowinherit;
		var pathDepth = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.pathdepth;

		var newPricingTaopixPanel =
	    {
	        id: 'componentPricingPanel',
	        name:'componentPricingPanel',
	        xtype:'taopixPricingPanel',
	        ref: '<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
',
	        windowToMask: Ext.getCmp('productConfigPricingGrid'),
	        isProduct: '0',
	        isEdit: isEdit,
	        company: '<?php echo $_smarty_tpl->tpl_vars['companycode']->value;?>
',
	        category: componentCategory,
	        decimalPlaces: decimalPlaces,
	        pricing:{pricingModel: selectedPricingModel, price: price, isPriceList: isPriceList, priceListID: priceListID, qtyIsDropDown: quantityIsDropDown, taxCode: taxCode, productType: '0'},
	        licenseKeyStoreURL: 'index.php?fsaction=AdminProductPricing.getLicenseKeyFromCompany&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&isedit='+isEdit+'&filterkeylist='+filterKeyList+'&componentcode='+componentCode+'&productcode=<?php echo $_smarty_tpl->tpl_vars['productcode']->value;?>
&id='+ parentid +'&companycode=<?php echo $_smarty_tpl->tpl_vars['companycode']->value;?>
',
	        LicenseKeys: {assignedLicenseKeys: assignedKeys, defaultChecked: defaultChecked},
	        additionalInfo: {langList: langListStore, dataList: priceAdditionalInfo},
	        priceDescription: {langList: '', dataList: []},
	        images: {deleteImg: '<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/delete.png', addimg: '<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/add.png'},
			errorMessage1: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeStartError1');?>
",
			errorMessage2: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeStartError2');?>
",
			errorMessage3: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeEndError');?>
",
			errorMessage4: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorComponentPriceError');?>
",
			errorMessage5: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorProductPriceError');?>
",
			errorMessage6: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorEnterValidPricing');?>
",
			errorMessage6Title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorTitleInvalidPricing');?>
",
			errorMessage7: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorRangeEndLimitError');?>
",
            defaultLanguage: '<?php echo $_smarty_tpl->tpl_vars['defaultlanguagecode']->value;?>
',
			inheritParentQty: inheritParentQty,
			allowInherit: canInherit,
			pathDepth: pathDepth
	    };

		if (btn.id == 'productConfigPriceEditButton')
		{
			if (isActive == 1)
			{
				Ext.getCmp('isPriceActive').setValue(true);
			}
			else
			{
				Ext.getCmp('isPriceActive').setValue(false);
			}
		}

		if (parentid == 0)
		{
			Ext.getCmp('nodePricingAddEditButton').text = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
";
			Ext.getCmp('nodePricingAddEditButton').setHandler(priceScreenSaveHandler);
		}
		else
		{
			Ext.getCmp('nodePricingAddEditButton').text = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonUpdate');?>
";
			Ext.getCmp('nodePricingAddEditButton').setHandler(priceScreenEditSaveHandler);
		}

		gPricingEditWindow.add(newPricingTaopixPanel);
		gPricingEditWindow.show();
	}

	function onActivate(btn, ev)
	{
		var selectedPriceIDs = Ext.taopix.gridSelection2IDList(Ext.getCmp('productConfigPricingGrid'));
		var active = 0;

		switch (btn.id)
		{
			case 'productConfigPriceActiveButton':
				active = 1;
				break;
			case 'productConfigPriceInactiveButton':
				active = 0;
				break;
		}

		/*update the active flag in the paramArray for the price record selected*/
		var priceIDArray = selectedPriceIDs.split(',');

		for (i = 0; i < priceIDArray.length; i++)
		{
			paramArray[priceIDArray[i]].active = active;
		}

		var pricingGridDataStore = Ext.getCmp('productConfigPricingGrid').store;
		var gridRecordCount = pricingGridDataStore.getCount();
		var gridRecordIDArray = new Array();

		/* We need to get a list of record ids that are currently in the grid so that we do not lose the default price if there is one */
		for (i = 0; i < gridRecordCount; i++)
		{
			gridRecordIDArray.push(pricingGridDataStore.data.items[i].id);
		}

		Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.modified = 1;

		buildPriceStoreData(gridRecordIDArray);
	}

	function buildPriceStoreData(pPriceLinkParentIDArray)
	{
		var selectedPricingModel = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.pricingmodel;
		var len = pPriceLinkParentIDArray.length;

		if (len > 0)
		{
			/* Build the data to apply to the grid */
			gridPriceData = '[';

			for (var i = 0; i < len; i++)
			{
				var groupCodesString = '"';
				var pricingString = '';

				// dont build add anything to the data store if there is no pricing attached or the price link paraent id references 
				// a dummy pricelink entrrey (i.e price is empty)
				if ((paramArray[pPriceLinkParentIDArray[i]] != undefined) && (paramArray[pPriceLinkParentIDArray[i]].price != ''))
				{
					gridPriceData = gridPriceData + '[' + pPriceLinkParentIDArray[i] + ',' + '"' + paramArray[pPriceLinkParentIDArray[i]].productcode + '",';
					gridPriceData += '"' + paramArray[pPriceLinkParentIDArray[i]].companycode + '",';

					var groupCodesArray = paramArray[pPriceLinkParentIDArray[i]].groupcodes.split(',');

					for (var j = 0; j < groupCodesArray.length; j++)
					{
						if (groupCodesArray[j] == '')
						{
							groupCodesArray[j] = '<i><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDefault');?>
</i>';
						}

						groupCodesString = groupCodesString + groupCodesArray[j] + '<br>';
					}

					/* split the price string so that it fits into the data store and column model  */
					var priceString = paramArray[pPriceLinkParentIDArray[i]].price;
					var priceLineData = priceStringToArray(selectedPricingModel, priceString);

					gridPriceData = gridPriceData + groupCodesString + '",' + priceLineData +  ', "' + paramArray[pPriceLinkParentIDArray[i]].includestax + '",' + paramArray[pPriceLinkParentIDArray[i]].active + ']';

					if (i != pPriceLinkParentIDArray.length - 1)
					{
						gridPriceData = gridPriceData + ',';
					}
				}
			}

			gridPriceData = gridPriceData + ']';
		}
		else
		{
			gridPriceData = '[]';
		}

		gridPriceData = eval(gridPriceData);
		gridProductConfigPricingDataStoreObj.loadData(gridPriceData);
	}

	function priceStringToArray(pPricingModel, pPriceString)
	{
		var priceStartHTML = '';
        var priceEndHTML = '';
		var priceStartQtyHTML = '';
		var priceEndQtyHTML = '';
		var priceStartpageCountHTML = '';
		var priceEndPageCountHTML = '';
		var priceBaseHTML = '';
		var priceUnitSellHTML = '';
		var priceTotalDiscountHTML = '';
		var returnString = '';
		var priceStartComponentCountHTML = '';
		var priceEndComponentCountHTML = '';

		var priceItem = {};
		var priceList = pPriceString.split(' ');

		for (var i = 0; i < priceList.length; i++)
		{
			priceItem[i] = {};
			var priceItemList = priceList[i].split('*');

			switch (pPricingModel)
    		{
	    		case 3:
	    			/* the price string data is in the format 'startqty-endqty-baseprice-unitsell-linesubtract'*/
    				priceItem[i].start = priceItemList[0];
            		priceItem[i].end = priceItemList[1];
            		priceItem[i].baseprice = priceItemList[2];
            		priceItem[i].unitsell = priceItemList[3];
            		priceItem[i].linesubtract = priceItemList[4];
    				break;
    			case 5:
    				/* the price string data is in the format 'startqty-endqty--startpagecount-endpagecount-baseprice-unitsell-linesubtract' */
    				priceItem[i].startqty = priceItemList[0];
            		priceItem[i].endqty = priceItemList[1];
            		priceItem[i].startpagecount = priceItemList[2];
            		priceItem[i].endpagecount = priceItemList[3];
            		priceItem[i].baseprice = priceItemList[4];
            		priceItem[i].unitsell = priceItemList[5];
            		priceItem[i].linesubtract = priceItemList[6];
    			break;
    			case 7:
    				/* the price string data is in the format 'startqty-endqty--startpagecount-endpagecount-baseprice-unitsell-linesubtract' */
    				priceItem[i].startqty = priceItemList[0];
            		priceItem[i].endqty = priceItemList[1];
            		priceItem[i].startcmpqty = priceItemList[2];
            		priceItem[i].endcmpqty = priceItemList[3];
            		priceItem[i].baseprice = priceItemList[4];
            		priceItem[i].unitsell = priceItemList[5];
            		priceItem[i].linesubtract = priceItemList[6];
    			break;
    			case 8:
    				/* the price string data is in the format 'startqty-endqty--startpagecount-endpagecount-baseprice-unitsell-linesubtract' */
    				priceItem[i].startqty = priceItemList[0];
            		priceItem[i].endqty = priceItemList[1];
            		priceItem[i].startcmpqty = priceItemList[2];
            		priceItem[i].endcmpqty = priceItemList[3];
            		priceItem[i].startpagecount = priceItemList[4];
            		priceItem[i].endpagecount = priceItemList[5];
            		priceItem[i].baseprice = priceItemList[6];
            		priceItem[i].unitsell = priceItemList[7];
            		priceItem[i].linesubtract = priceItemList[8];
    			break;
    		}
		}

		for (var index in priceItem)
		{
			if (priceItem.hasOwnProperty(index) && typeof priceItem[index] !== "function")
			{
				if (index == 0)
				{
					openingQuote = '"';
				}
				else
				{
					openingQuote = '';
				}

				if (pPricingModel == 3)
	            {
		            priceStartHTML = openingQuote + priceStartHTML + priceItem[index].start + '<br>';
		            priceEndHTML = openingQuote + priceEndHTML + priceItem[index].end + '<br>';
        	    	priceBaseHTML =  openingQuote + priceBaseHTML + priceItem[index].baseprice + '<br>';
        	    	priceUnitSellHTML = openingQuote + priceUnitSellHTML  + priceItem[index].unitsell + '<br>';
        	    	priceTotalDiscountHTML = openingQuote + priceTotalDiscountHTML + priceItem[index].linesubtract + '<br>';
	            }
	            else if (pPricingModel == 5)
	            {
	            	priceStartQtyHTML = openingQuote + priceStartQtyHTML + priceItem[index].startqty + '<br>';
	            	priceEndQtyHTML = openingQuote + priceEndQtyHTML + priceItem[index].endqty + '<br>';
		            priceStartpageCountHTML = openingQuote + priceStartpageCountHTML + priceItem[index].startpagecount + '<br>';
		            priceEndPageCountHTML = openingQuote + priceEndPageCountHTML + priceItem[index].endpagecount + '<br>';
		            priceBaseHTML = openingQuote + priceBaseHTML + priceItem[index].baseprice + '<br>';
		            priceUnitSellHTML = openingQuote+ priceUnitSellHTML + priceItem[index].unitsell + '<br>';
		            priceTotalDiscountHTML = openingQuote + priceTotalDiscountHTML + priceItem[index].linesubtract + '<br>';
	            }
	            else if (pPricingModel == 7)
	            {
	            	priceStartQtyHTML = openingQuote + priceStartQtyHTML + priceItem[index].startqty + '<br>';
	            	priceEndQtyHTML = openingQuote + priceEndQtyHTML + priceItem[index].endqty + '<br>';
		            priceStartComponentCountHTML = openingQuote + priceStartComponentCountHTML + priceItem[index].startcmpqty + '<br>';
		            priceEndComponentCountHTML = openingQuote + priceEndComponentCountHTML + priceItem[index].endcmpqty + '<br>';
		            priceBaseHTML = openingQuote + priceBaseHTML + priceItem[index].baseprice + '<br>';
		            priceUnitSellHTML = openingQuote+ priceUnitSellHTML + priceItem[index].unitsell + '<br>';
		            priceTotalDiscountHTML = openingQuote + priceTotalDiscountHTML + priceItem[index].linesubtract + '<br>';
	            }
	            else if (pPricingModel == 8)
	            {
	            	priceStartQtyHTML = openingQuote + priceStartQtyHTML + priceItem[index].startqty + '<br>';
	            	priceEndQtyHTML = openingQuote + priceEndQtyHTML + priceItem[index].endqty + '<br>';
	            	priceStartComponentCountHTML = openingQuote + priceStartComponentCountHTML + priceItem[index].startcmpqty + '<br>';
		            priceEndComponentCountHTML = openingQuote + priceEndComponentCountHTML + priceItem[index].endcmpqty + '<br>';
		            priceStartpageCountHTML = openingQuote + priceStartpageCountHTML + priceItem[index].startpagecount + '<br>';
		            priceEndPageCountHTML = openingQuote + priceEndPageCountHTML + priceItem[index].endpagecount + '<br>';
		            priceBaseHTML = openingQuote + priceBaseHTML + priceItem[index].baseprice + '<br>';
		            priceUnitSellHTML = openingQuote+ priceUnitSellHTML + priceItem[index].unitsell + '<br>';
		            priceTotalDiscountHTML = openingQuote + priceTotalDiscountHTML + priceItem[index].linesubtract + '<br>';
	            }
			}
		}

		if (pPricingModel == '3')
        {
			returnString = priceStartHTML + '",' + priceEndHTML + '",'  + priceBaseHTML + '",' + priceUnitSellHTML + '",' + priceTotalDiscountHTML + '"';
        }
        else if (pPricingModel == '5')
        {
        	returnString = priceStartQtyHTML + '",' + priceEndQtyHTML + '",' + priceStartpageCountHTML + '",' + priceEndPageCountHTML + '",' + priceBaseHTML + '",' + priceUnitSellHTML + '",' + priceTotalDiscountHTML + '"';
        }
        else if (pPricingModel == '7')
        {
        	returnString = priceStartQtyHTML + '",' + priceEndQtyHTML + '",' + priceStartComponentCountHTML + '",' + priceEndComponentCountHTML + '",' + priceBaseHTML + '",' + priceUnitSellHTML + '",' + priceTotalDiscountHTML + '"';
        }
        else if (pPricingModel == '8')
        {
        	returnString = priceStartQtyHTML + '",' + priceEndQtyHTML + '",' + priceStartComponentCountHTML + '",' + priceEndComponentCountHTML + '",' + priceStartpageCountHTML + '",' + priceEndPageCountHTML + '",' + priceBaseHTML + '",' + priceUnitSellHTML + '",' + priceTotalDiscountHTML + '"';
        }

		return returnString;
	}

	function ajaxSuccess(result, request)
	{
		/*update the javascript array so that each pricelink array key now holds the groupcode and price data */
		var response = eval(result.responseText);
		var hasItemsToProcess = false;

		priceData = {};
		priceData = response.rows;
		priceLinkParentIDArray = new Array();

		for(var index in priceData)
		{
			if (priceData.hasOwnProperty(index) && typeof priceData[index] !== "function")
			{
				hasItemsToProcess = true;

				paramArray[priceData[index].parentid] = {};
				paramArray[priceData[index].parentid].initialized = 1;
				paramArray[priceData[index].parentid].modified = 0;
				paramArray[priceData[index].parentid].pricelinkids = priceData[index].pricelinkids;
				paramArray[priceData[index].parentid].groupcodes = priceData[index].groupcodes;
				paramArray[priceData[index].parentid].pricingmodel = priceData[index].pricingmodel;
				paramArray[priceData[index].parentid].price = priceData[index].price;
				paramArray[priceData[index].parentid].quantityisdropdown = priceData[index].quantityisdropdown;
				paramArray[priceData[index].parentid].categorycode = priceData[index].categorycode;
				paramArray[priceData[index].parentid].ispricelist = priceData[index].ispricelist;
				paramArray[priceData[index].parentid].pricelistid = priceData[index].pricelistid;
				paramArray[priceData[index].parentid].inispricelist = priceData[index].inispricelist;
				paramArray[priceData[index].parentid].inpricelistid = priceData[index].inpricelistid;
				paramArray[priceData[index].parentid].pricedescription = priceData[index].pricedescription;
				paramArray[priceData[index].parentid].priceladditionalinfo = priceData[index].priceladditionalinfo;
				paramArray[priceData[index].parentid].productcode = priceData[index].productcode;
				paramArray[priceData[index].parentid].active = priceData[index].active;
				paramArray[priceData[index].parentid].taxcode = priceData[index].taxcode;
				paramArray[priceData[index].parentid].includestax = priceData[index].includestax;
				paramArray[priceData[index].parentid].inheritparentqty = priceData[index].inheritparentqty;
				paramArray[priceData[index].parentid].allowinherit = priceData[index].allowinherit;
				paramArray[priceData[index].parentid].companycode = priceData[index].companycode;
				paramArray[priceData[index].parentid].dummypricelinkrecordfornode = 0;

				priceLinkParentIDArray.push(priceData[index].parentid);
			}
		}

		var selectedNode = Ext.getCmp('tree').getSelectionModel().getSelectedNode();
		selectedNode.attributes.initialized = '1';

		if (hasItemsToProcess)
		{
			// we need to check to see if any dummy records have been added to the node when the tree first displays.
			// if there are any dummy nodes (ids less than 0) then we know the tree was previously corrupted and we have
			// add the record to fix the missing dummy. 
			// we need this dummy id kept on the node so it persists after the pricing window has been initialised.
			var currentNodePriceLinkIDsArray = selectedNode.attributes.pricelinkparentids.split(',');

			for (key in currentNodePriceLinkIDsArray)
			{
				// if we detect that there has been a dummy record added then push it back on to the node along with any records from the database.
				if ((currentNodePriceLinkIDsArray[key] < 0) && (priceLinkParentIDArray.indexOf(currentNodePriceLinkIDsArray[key]) == -1))
				{
					priceLinkParentIDArray.push(currentNodePriceLinkIDsArray[key]);
				}
			}

			selectedNode.attributes.pricelinkparentids = priceLinkParentIDArray.join(',');
			createPricingGridWindow(priceLinkParentIDArray);
		}
		else
		{
			// if there are not items to process (i.e no pricing currently set) then we need to add a dummy pricelink id
			// and attach the dummypricelink id to the node. This is so that when processing the tree later on a save a 
			// dummy pricelink record will be created
			priceLinkParentIDArray = [];

			if (selectedNode.attributes.pricelinkparentids == '0')
			{
				var newDummyPriceLinkID = createDummyPriceLinkeRecordForNode(selectedNode);
				selectedNode.attributes.pricelinkparentids = String(newDummyPriceLinkID);
				
				priceLinkParentIDArray.push(newDummyPriceLinkID);
			}

			createPricingGridWindow(priceLinkParentIDArray);
		}
	}

	function createDummyPriceLinkeRecordForNode(pNode)
	{
		--dummyPriceLinkID;

		paramArray[dummyPriceLinkID] = {};
		paramArray[dummyPriceLinkID].initialized = 1;
		paramArray[dummyPriceLinkID].modified = 1;
		paramArray[dummyPriceLinkID].pricelinkids = -1;
		paramArray[dummyPriceLinkID].groupcodes = '';
		paramArray[dummyPriceLinkID].pricingmodel = pNode.attributes.pricingmodel;
		paramArray[dummyPriceLinkID].price = '';
		paramArray[dummyPriceLinkID].quantityisdropdown = 0;
		paramArray[dummyPriceLinkID].categorycode = pNode.attributes.categorycode;
		paramArray[dummyPriceLinkID].ispricelist = 0;
		paramArray[dummyPriceLinkID].pricelistid = -1;
		paramArray[dummyPriceLinkID].inispricelist = 0;
		paramArray[dummyPriceLinkID].inpricelistid = 0;
		paramArray[dummyPriceLinkID].pricedescription = '';
		paramArray[dummyPriceLinkID].priceladditionalinfo = '';
		paramArray[dummyPriceLinkID].productcode = '<?php echo $_smarty_tpl->tpl_vars['productcode']->value;?>
';
		paramArray[dummyPriceLinkID].active = 1;
		paramArray[dummyPriceLinkID].taxcode = '';
		paramArray[dummyPriceLinkID].includestax = 0;
		paramArray[dummyPriceLinkID].inheritparentqty = pNode.attributes.inheritparentqty;
		paramArray[dummyPriceLinkID].allowinherit = pNode.attributes.allowinherit;
		paramArray[dummyPriceLinkID].companycode = pNode.attributes.companycode;
		paramArray[dummyPriceLinkID].dummypricelinkrecordfornode = 1;

		return dummyPriceLinkID;
	}

	function defaultPriceRenderer(value, p, record)
	{
		var companyLogin = false;

		<?php if ($_smarty_tpl->tpl_vars['companyLogin']->value) {?>
			companyLogin = true;
		<?php }?>

		if (value == '')
		{
			className =  'class = "inactive"';
			return '<span '+className+">Default Component Price</span>";
		}
		else
		{
			if ((record.data.companycode == '') && (companyLogin))
			{
				className =  'class = "inactive"';
				return '<span '+className+'>'+value+'</span>';
			}
			else
			{
				return value;
			}
		}
	}

	function companyCodeRenderer(value, p, record)
	{
		var companyLogin = false;

		<?php if ($_smarty_tpl->tpl_vars['companyLogin']->value) {?>
			companyLogin = true;
		<?php }?>

		if (value == '')
		{
			value = "<i><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_Global');?>
</i>";
		}

		if (record.data.active == 0)
		{
			className =  'class = "inactive"';
			return '<span '+className+'>'+value+'</span>';
		}
		else
		{
			if (record.data.productcode == '')
			{
				className =  'class = "inactive"';
				return '<span '+className+'>'+value+'</span>';
			}
			else
			{
				if ((record.data.companycode == '') && (companyLogin))
				{
					className =  'class = "inactive"';
					return '<span '+className+'>'+value+'</span>';
				}
				else
				{
					return '<span class="">'+value+'</span>';
				}
			}
		}
	}

	function generalColumnRenderer(value, p, record)
	{
		var companyLogin = false;

		<?php if ($_smarty_tpl->tpl_vars['companyLogin']->value) {?>
			companyLogin = true;
		<?php }?>

		if (record.data.active == 0)
		{
			className =  'class = "inactive"';
			return '<span '+className+'>'+value+'</span>';
		}
		else
		{
			if (record.data.productcode == '')
			{
				className =  'class = "inactive"';
				return '<span '+className+'>'+value+'</span>';
			}
			else
			{
				if ((record.data.companycode == '') && (companyLogin))
				{
					className =  'class = "inactive"';
					return '<span '+className+'>'+value+'</span>';
				}
				else
				{
					return '<span class="">'+value+'</span>';
				}
			}
		}
	}

	function statusRenderer(value, p, record)
	{
		var companyLogin = false;

		<?php if ($_smarty_tpl->tpl_vars['companyLogin']->value) {?>
			companyLogin = true;
		<?php }?>

		if (value == 0)
		{
			className =  'class = "inactive"';
			return '<span '+className+"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelInactive');?>
</span>";
		}
		else
		{
			if (record.data.productcode == '')
			{
				className =  'class = "inactive"';
				return '<span '+className+"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
</span>";
			}
			else
			{
				if ((record.data.companycode == '') && (companyLogin))
				{
					className =  'class = "inactive"';
					return '<span '+className+'><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
</span>';
				}
				else
				{
					return "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelActive');?>
";
				}
			}
		}
	}

	function updatePricingForNode(pPriceLinkID, pUpDateToCurrentPriceRecord)
	{
		var selectedCompanyCode = Ext.getCmp('company').getValue();
		var groupCodes = '';
		var isPriceList = 0;
		var priceListID = 0;
		var price = '';
		var productCode = '<?php echo $_smarty_tpl->tpl_vars['productcode']->value;?>
';
		var categoryCode = '';
		var pricingModel = '';
		var priceAdditionalInfo = '';
		var inheritParentQty = '0';

		if (Ext.getCmp('isPriceActive').checked)
		{
			active =  1;
		}
		else
		{
			active =  0;
		}

		if (Ext.getCmp('defaultLicenseKeys').checked)
		{
			groupCodes = '';
		}
		else
		{
			groupCodes = Ext.getCmp('componentPricingPanel').covertLicenseKeySelectionToString();
		}

		priceListID = Ext.getCmp('pricelistid').getValue();
		price = Ext.getCmp('price').convertTableToString();
		priceAdditionalInfo = Ext.getCmp('priceadditionalinfo').convertTableToString();
		pricingModel = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.pricingmodel;
		pathDepth = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.pathDepth;
		taxCode = Ext.getCmp('taxcode').getValue();
		taxCodeRawValue = Ext.getCmp('taxcode').getRawValue();

		if (taxCode != '')
		{
			newTaxCode = taxCodeRawValue.substr(0,taxCodeRawValue.indexOf(' '));
			newTaxRate = taxCodeRawValue.substr(taxCodeRawValue.lastIndexOf("-"),taxCodeRawValue.length);
			newRawIncludesTaxValue = newTaxCode + ' ' + newTaxRate;
		}
		else
		{
			newRawIncludesTaxValue = taxCodeRawValue;
		}

		if (pricingModel == 7 || pricingModel == 8)
		{
			if (Ext.getCmp('fixedquantityrange').checked)
			{
				quantityIsDropDown = '1';
			}
			else
			{
				quantityIsDropDown = '0';
			}
		}
		else
		{
			quantityIsDropDown = '0';
		}

		var inheritObj = Ext.getCmp('inheritparentqty');

		if (inheritObj)
		{
			if (Ext.getCmp('inheritparentqty').checked)
			{
				inheritParentQty = '1';
			}
		}

		// update the product tree
		Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.inheritparentqty = inheritParentQty;

		if (priceListID != '-1')
		{
			isPriceList = 1;
		}

		if (pUpDateToCurrentPriceRecord)
		{
			paramArray[pPriceLinkID].modified = 1;
			paramArray[pPriceLinkID].groupcodes = groupCodes;
			paramArray[pPriceLinkID].price = price;
			paramArray[pPriceLinkID].quantityisdropdown = quantityIsDropDown;
			paramArray[pPriceLinkID].ispricelist = isPriceList;
			paramArray[pPriceLinkID].pricelistid = priceListID;
			paramArray[pPriceLinkID].priceladditionalinfo = priceAdditionalInfo;
			paramArray[pPriceLinkID].taxcode = taxCode;
			paramArray[pPriceLinkID].active = active;
			paramArray[pPriceLinkID].includestax = newRawIncludesTaxValue;
			paramArray[pPriceLinkID].inheritparentqty = inheritParentQty;
		}
		else
		{
			if (selectedCompanyCode == 'GLOBAL')
			{
				selectedCompanyCode = '';
			}

			categoryCode = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.categorycode;

			paramArray[pPriceLinkID] = {};
			paramArray[pPriceLinkID].initialized = 1;
			paramArray[pPriceLinkID].modified = 1;
			paramArray[pPriceLinkID].pricelinkids = pPriceLinkID;
			paramArray[pPriceLinkID].groupcodes = groupCodes;
			paramArray[pPriceLinkID].categorycode = categoryCode;
			paramArray[pPriceLinkID].price = price;
			paramArray[pPriceLinkID].quantityisdropdown = quantityIsDropDown;
			paramArray[pPriceLinkID].pricingmodel = pricingModel;
			paramArray[pPriceLinkID].ispricelist = isPriceList;
			paramArray[pPriceLinkID].pricelistid = priceListID;
			paramArray[pPriceLinkID].priceladditionalinfo = priceAdditionalInfo;
			paramArray[pPriceLinkID].productcode = productCode;
			paramArray[pPriceLinkID].taxcode = taxCode;
			paramArray[pPriceLinkID].active = active;
			paramArray[pPriceLinkID].includestax = newRawIncludesTaxValue;
			paramArray[pPriceLinkID].inheritparentqty = inheritParentQty;
			paramArray[pPriceLinkID].companycode = selectedCompanyCode;
			paramArray[pPriceLinkID].dummypricelinkrecordfornode = 0;

			if (Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.pricelinkparentids == '0')
			{
				Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.pricelinkparentids = String(pPriceLinkID);
			}
			else
			{
				var currentPriceLinkParentIds = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.pricelinkparentids;
				Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.pricelinkparentids = currentPriceLinkParentIds +','+pPriceLinkID;
			}
			Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.initialized = '1';
		}
	}

	function createPricingGridWindow(pPriceLinkParentIDArray)
	{
		/* get the pricingmodel and the list of parent ids for the node that has been selected. */
		var selectedNode = Ext.getCmp('tree').getSelectionModel().getSelectedNode();
		var selectedPricingModel = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.pricingmodel;
		var windowTitle = selectedNode.text;

		<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
			var companyColumnHidden = false;
		<?php } else { ?>
			var companyColumnHidden = true;
		<?php }?>



		/* Now populate the grid with the pricing data using the data stored in the javascript array*/
		if (gPricingWindowExists)
		{
			if (selectedPricingModel != gLastPricingModel)
			{
				Ext.getCmp('productConfigPricingGridWindow').close();
				gPricingWindowExists = false;
			}
		}

		if (gPricingWindowExists)
		{
			if (selectedNode.attributes.pricelinkparentids == 0)
			{
				var emptyArray = new Array();
				buildPriceStoreData(emptyArray);
			}
			else
			{
				 buildPriceStoreData(pPriceLinkParentIDArray);
			}

			// make sure the add button is set correctly for linking
			if (selectedProductLinkID == 0)
			{
				Ext.getCmp('productConfigPriceAddButton').enable();
			}
			else
			{
				Ext.getCmp('productConfigPriceAddButton').disable();
			}

			Ext.getCmp('productConfigPricingGridWindow').setTitle(windowTitle);
			Ext.getCmp('productConfigPricingGridWindow').show();
		}
		else
		{
			/* define the checkbox selection model for the pricing grid */
			var productConfigPricingGridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
				listeners: {
					selectionchange: function(productConfigPricingGridCheckBoxSelectionModelObj)
					{
						if (selectedProductLinkID == 0)
						{
							var selectionCount = productConfigPricingGridCheckBoxSelectionModelObj.getCount();

							if (selectionCount == 1)
							{
								productConfigPricingGrid.productConfigPriceEditButton.enable();
								productConfigPricingGrid.deletePriceRecordButton.enable();
							}
							else
							{
								productConfigPricingGrid.productConfigPriceEditButton.disable();
							}

							if (productConfigPricingGrid)
							{
								var selRecords = productConfigPricingGrid.getSelectionModel().getSelections();
							}

							if (selectionCount > 0)
							{
								var selectID = Ext.taopix.gridSelection2IDList(Ext.getCmp('productConfigPricingGrid'));
								var idList = selectID.split(',');

								for (i = 0; i < idList.length; i++)
								{
										record = Ext.getCmp('productConfigPricingGrid').store.getById(idList[i]);

										if (record.data['productcode'] == '')
										{
											productConfigPricingGrid.productConfigPriceActiveButton.disable();
											productConfigPricingGrid.productConfigPriceInactiveButton.disable();
											productConfigPricingGrid.productConfigPriceEditButton.disable();
											productConfigPricingGrid.deletePriceRecordButton.disable();

											break;
										}
										else
										{
											// if we are a company login we must make sure that the user cannot modify global prices
											<?php if ($_smarty_tpl->tpl_vars['companyLogin']->value) {?>
												if (record.data['companycode'] == '' && idList[i] > 0)
												{
													productConfigPricingGrid.productConfigPriceEditButton.disable();
													productConfigPricingGrid.productConfigPriceActiveButton.disable();
													productConfigPricingGrid.productConfigPriceInactiveButton.disable();
													productConfigPricingGrid.deletePriceRecordButton.disable();
													break;
												}
												else
												{
													productConfigPricingGrid.productConfigPriceActiveButton.enable();
													productConfigPricingGrid.productConfigPriceInactiveButton.enable();
													productConfigPricingGrid.deletePriceRecordButton.enable();
												}
											<?php } else { ?>
												productConfigPricingGrid.productConfigPriceActiveButton.enable();
												productConfigPricingGrid.productConfigPriceInactiveButton.enable();
												productConfigPricingGrid.deletePriceRecordButton.enable();
											<?php }?>
										}
								}

							}
							else
							{
								productConfigPricingGrid.productConfigPriceActiveButton.disable();
								productConfigPricingGrid.productConfigPriceInactiveButton.disable();
								productConfigPricingGrid.productConfigPriceEditButton.disable();
								productConfigPricingGrid.deletePriceRecordButton.disable();
							}
						}
					}
				}
			});

			/* generate the correct column model and data store model for the grid depending on the pricing model */
			if (selectedPricingModel == 3)
			{
				storeRecords = [{name: 'id', mapping: 0},
				{name: 'productcode', mapping: 1},
				{name: 'companycode', mapping: 2},
			    {name: 'lkey', mapping: 3},
			    {name: 'qrs', mapping: 4},
			    {name: 'qre', mapping: 5},
			    {name: 'bp', mapping: 6},
			    {name: 'up', mapping: 7},
			    {name: 'ls', mapping: 8},
			    {name: 'it', mapping: 9},
				{name: 'active', mapping: 10}];

				gridColumns = [
				               	productConfigPricingGridCheckBoxSelectionModelObj,
				               	{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProduct');?>
", width: 150, hidden: true, renderer: defaultPriceRenderer, dataIndex: 'productcode'},
				               	{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", width: 150, hidden: companyColumnHidden, renderer: companyCodeRenderer, dataIndex: 'companycode'},
				    			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKey');?>
", width: 150, dataIndex: 'lkey', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeStart');?>
", width: 130, dataIndex: 'qrs', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeEnd');?>
", width: 130, dataIndex: 'qre', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeBasePrice');?>
", width: 100, dataIndex: 'bp', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeUnitPrice');?>
", width: 100, dataIndex: 'up', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLineSubtract');?>
", width: 100, dataIndex: 'ls', renderer: generalColumnRenderer},
								{id: 'includestax', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelIncludesTax');?>
", width: 160, dataIndex: 'it', renderer: generalColumnRenderer},
								{id: 'active', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
", renderer: statusRenderer, width: 50, dataIndex: 'active'}
				              ];
			}
			else if (selectedPricingModel == 5)
			{
				storeRecords = [{name: 'id', mapping: 0},
				{name: 'productcode', mapping: 1},
				{name: 'companycode', mapping: 2},
			    {name: 'lkey', mapping: 3},
			    {name: 'qrs', mapping: 4},
			    {name: 'qre', mapping: 5},
			    {name: 'prs', mapping: 6},
			    {name: 'pre', mapping: 7},
			    {name: 'bp', mapping: 8},
			    {name: 'up', mapping: 9},
			    {name: 'ls', mapping: 10},
			    {name: 'it', mapping: 11},
				{name: 'active', mapping: 12}];

				gridColumns = [
				               	productConfigPricingGridCheckBoxSelectionModelObj,
				               	{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProduct');?>
", width: 150, hidden: true, renderer: defaultPriceRenderer, dataIndex: 'productcode'},
				               	{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", width: 150, hidden: companyColumnHidden, renderer: companyCodeRenderer, dataIndex: 'companycode'},
				    			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKey');?>
", width: 150, dataIndex: 'lkey', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeStart');?>
", width: 105, dataIndex: 'qrs', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeEnd');?>
", width: 105, dataIndex: 'qre', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PageCountRangeStart');?>
", width: 120, dataIndex: 'prs', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PageCountRangeEnd');?>
", width: 120, dataIndex: 'pre', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeBasePrice');?>
", width: 70, dataIndex: 'bp', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeUnitPrice');?>
", width: 70, dataIndex: 'up', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLineSubtract');?>
", width: 90, dataIndex: 'ls', renderer: generalColumnRenderer},
								{id: 'includestax', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelIncludesTax');?>
", width: 160, dataIndex: 'it', renderer: generalColumnRenderer},
								{id: 'active', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
", renderer: statusRenderer, width: 50, dataIndex: 'active'}
				              ];
			}
			else if (selectedPricingModel == 7)
			{
				storeRecords = [{name: 'id', mapping: 0},
				{name: 'productcode', mapping: 1},
				{name: 'companycode', mapping: 2},
			    {name: 'lkey', mapping: 3},
			    {name: 'qrs', mapping: 4},
			    {name: 'qre', mapping: 5},
			    {name: 'crs', mapping: 6},
			    {name: 'cre', mapping: 7},
			    {name: 'bp', mapping: 8},
			    {name: 'up', mapping: 9},
			    {name: 'ls', mapping: 10},
			    {name: 'it', mapping: 11},
				{name: 'active', mapping: 12}];

				gridColumns = [
				               	productConfigPricingGridCheckBoxSelectionModelObj,
				               	{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProduct');?>
", width: 150, hidden: true, renderer: defaultPriceRenderer, dataIndex: 'productcode'},
				               	{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", width: 150, hidden: companyColumnHidden, renderer: companyCodeRenderer, dataIndex: 'companycode'},
				    			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKey');?>
", width: 150, dataIndex: 'lkey', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeStart');?>
", width: 90, dataIndex: 'qrs', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeEnd');?>
", width: 90, dataIndex: 'qre', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ComponentPriceRangeStart');?>
", width: 125, dataIndex: 'crs', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ComponentPriceRangeEnd');?>
", width: 125, dataIndex: 'cre', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeBasePrice');?>
", width: 70, dataIndex: 'bp', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeUnitPrice');?>
", width: 70, dataIndex: 'up', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLineSubtract');?>
", width: 90, dataIndex: 'ls', renderer: generalColumnRenderer},
								{id: 'includestax', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelIncludesTax');?>
", width: 160, dataIndex: 'it', renderer: generalColumnRenderer},
								{id: 'active', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
", renderer: statusRenderer, width: 50, dataIndex: 'active'}
				              ];
			}
			else if (selectedPricingModel == 8)
			{
				storeRecords = [{name: 'id', mapping: 0},
				{name: 'productcode', mapping: 1},
				{name: 'companycode', mapping: 2},
			    {name: 'lkey', mapping: 3},
			    {name: 'qrs', mapping: 4},
			    {name: 'qre', mapping: 5},
			    {name: 'crs', mapping: 6},
			    {name: 'cre', mapping: 7},
			    {name: 'prs', mapping: 8},
			    {name: 'pre', mapping: 9},
			    {name: 'bp', mapping: 10},
			    {name: 'up', mapping: 11},
			    {name: 'ls', mapping: 12},
			    {name: 'it', mapping: 13},
				{name: 'active', mapping: 14}];

				gridColumns = [
				               	productConfigPricingGridCheckBoxSelectionModelObj,
				               	{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelProduct');?>
", width: 150, hidden: true, renderer: defaultPriceRenderer, dataIndex: 'productcode'},
				               	{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
", width: 150, hidden: companyColumnHidden, renderer: companyCodeRenderer, dataIndex: 'companycode'},
				    			{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLicenseKey');?>
", width: 150, dataIndex: 'lkey', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeStart');?>
", width: 80, dataIndex: 'qrs', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeEnd');?>
", width: 80, dataIndex: 'qre', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ComponentPriceRangeStart');?>
", width: 80, dataIndex: 'crs', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ComponentPriceRangeEnd');?>
", width: 80, dataIndex: 'cre', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PageCountRangeStart');?>
", width: 80, dataIndex: 'prs', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PageCountRangeEnd');?>
", width: 80, dataIndex: 'pre', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeBasePrice');?>
", width: 60, dataIndex: 'bp', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeUnitPrice');?>
", width: 60, dataIndex: 'up', renderer: generalColumnRenderer},
								{header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLineSubtract');?>
", width: 70, dataIndex: 'ls', renderer: generalColumnRenderer},
								{id: 'includestax', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelIncludesTax');?>
", width: 160, dataIndex: 'it', renderer: generalColumnRenderer},
								{id: 'active', header: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelStatus');?>
", renderer: statusRenderer, width: 50, dataIndex: 'active'}
				              ];
			}

			gridProductConfigPricingDataStoreObj = new Ext.data.GroupingStore(
			{
				groupField: 'productcode',
				proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminProducts.getProductsConfigPricingGridData&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
'}),
				baseParams: {csrf_token: Ext.taopix.getCSRFToken()},
				reader: new Ext.data.ArrayReader(
					{
						idIndex: 0
					},
					Ext.data.Record.create(storeRecords)
				)
			});

			var gridProductConfigPricingColumnModelObj = new Ext.grid.ColumnModel({
				defaults: {	sortable: false, resizable: true },
				columns: gridColumns
			});

			var productConfigPricingGrid = new Ext.grid.GridPanel({
				id: 'productConfigPricingGrid',
				store: gridProductConfigPricingDataStoreObj,
				cm: gridProductConfigPricingColumnModelObj,
				enableColLock:false,
				draggable:false,
				enableColumnHide:false,
				enableColumnMove:false,
				border:false,
				enableHdMenu:false,
				trackMouseOver:false,
				stripeRows:true,
				columnLines:true,
				ctCls: 'grid',
				style:'border:1px solid #99BBE8',
				autoExpandColumn: (companyColumnHidden ? 'active' : 'includestax'),
					view: new Ext.grid.GroupingView({forceFit:false, showGroupName: false, groupTextTpl: '{text}'}),
				height:400,
				selModel: productConfigPricingGridCheckBoxSelectionModelObj,
				tbar:
				[
					{
						id:'productConfigPriceAddButton',
						ref: '../productConfigPriceAddButton',
						text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAdd');?>
",
						iconCls: 'silk-add',
						handler: onPricingEdit,
						disabled: ( selectedProductLinkID > 0)
					}, '-',
					{
						id: 'productConfigPriceEditButton',
						ref: '../productConfigPriceEditButton',
						text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonEdit');?>
",
						iconCls: 'silk-pencil',
						handler: onPricingEdit,
						disabled: true
					},'-',
					 {
						id: 'deletePriceRecordButton',
						ref: '../deletePriceRecordButton',
						text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonDelete');?>
",
						iconCls: 'silk-delete',
						handler: onDeletePriceRecord,
						disabled: true
					},'-',
					{
						id:'productConfigPriceActiveButton',
						ref: '../productConfigPriceActiveButton',
						text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeActive');?>
",
						iconCls: 'silk-lightbulb',
						handler: onActivate,
						disabled: true
					}, '-',
					{
						id:'productConfigPriceInactiveButton',
						ref: '../productConfigPriceInactiveButton',
						text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelMakeInactive');?>
",
						iconCls: 'silk-lightbulb-off',
						handler: onActivate,
						disabled: true
					}
					,'-',
					new Ext.form.Checkbox({boxLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelEnableGrouping');?>
", id: 'grouping',hideLabel:true, checked:true, listeners: { check: clearGrouping }})

				]
			});

			var productConfigGridPanelObj = new Ext.FormPanel({
				id: 'productConfigPricingGridPanel',
		        labelAlign: 'left',
		        labelWidth:20,
		        autoHeight: true,
		        frame:true,
		        layout:'form',
		        cls: 'left-right-buttons',
		        items: productConfigPricingGrid
		    });

			var gProductConfigPricingGridWindow = new Ext.Window({
				id: 'productConfigPricingGridWindow',
				closable:false,
				plain:true,
				modal:true,
				draggable:true,
				title: windowTitle,
				resizable:false,
				layout: 'fit',
				height: 'auto',
				width: 1200,
				items: productConfigGridPanelObj,
				listeners: {
					'close': {
						fn: function(){
							productCongfigWindowExists = false;
						}
					}
				},
				tools:[{
				    id:'clse',
				    qtip: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCloseWIndow');?>
',
				    handler: function(){Ext.getCmp('productConfigPricingGridWindow').hide(); }
				}]
			});
			gProductConfigPricingGridWindow.setTitle(windowTitle);
			gLastPricingModel = selectedPricingModel;
			gPricingWindowExists = true;

			if (selectedNode.attributes.initialized == '1')
			{
				buildPriceStoreData(pPriceLinkParentIDArray);
			}

			gProductConfigPricingGridWindow.show();
		}
	}

	function pricingHandler()
	{
		/* get the pricingmodel and the list of parent ids for the node that has been selected. */
		var selectedNode = Ext.getCmp('tree').getSelectionModel().getSelectedNode();
		var selectedPricingModel = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.pricingmodel;
		var windowTitle = selectedNode.text;
		var selectedCompanyCode = Ext.getCmp('company').getValue();

		if (selectedNode.attributes.initialized == '0')
		{

			/*We need to perform an ajax call to udate the javascript array with the groupcode and price data.*/
			var componentCode = Ext.getCmp('tree').getSelectionModel().getSelectedNode().attributes.code;

			Ext.Ajax.request({
			   url: 'index.php?fsaction=AdminProducts.getProductsConfigPricingGridData&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
',
			   success: ajaxSuccess,
			   failure: ajaxFailure,
			   params: { pricelinkparentids: selectedNode.attributes.pricelinkparentids,
				   		 componentcode: componentCode,
				   		 companycode: selectedCompanyCode,
						 csrf_token: Ext.taopix.getCSRFToken()
			   			}
			});
		}
		else
		{
			createPricingGridWindow(selectedNode.attributes.pricelinkparentids.split(','));
		}

	}

	function productTreeRefreshSuccess(result, request)
	{
		/*get the response text back from the ajax call - the response text consists of the length of the tree string - the tree string - and the javascript array all seperated with a space */
		/* i.e. 2088 treeData javaScriptArray */
		var response = result.responseText;

		var matchPos1 = response.search(" ");

		var treeLength = parseInt(response.substr(0,matchPos1));

		var treeString = response.substr(matchPos1 + 1, treeLength);
		var newTree = eval('('+ treeString + ')');

		var newRootNode = new Ext.tree.AsyncTreeNode({
			text: '<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
',
	    	sectioncode: 'PRODUCT',
			iconCls: 'silk-folder',
			expanded: true,
			singleClickExpand: true,
			children: newTree
		});

		tree.setRootNode(newRootNode);
	}

	function getLinkedProductCodeSuccess(result, request)
	{
		var resultJSON = JSON.parse(result.responseText);
		var productLinkStore = productLinkingCombo.getStore();
		var selectedProductLinkIndex = 0;

		if (resultJSON.success)
		{
			productLinkingCombo.setValue(resultJSON.msg);
			selectedProductLinkIndex = productLinkStore.findExact("productcode", resultJSON.msg);
			selectedProductLinkID = productLinkStore.getAt(selectedProductLinkIndex).data.id;
		}
		else
		{
			getLinkedProductCodeFailure();
		}
	}

	function closeConfigWindow()
	{
		if (!gInEditMode)
		{
			gDialogObj.close();
			if (gPricingWindowExists)
			{
				Ext.getCmp('productConfigPricingGridWindow').close();
			}
		}
		else
		{
			Ext.MessageBox.confirm("<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCurrentlyInEditMode');?>
", "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageCurrentlyInEditMode');?>
", processResult);
		}

		function processResult(btn)
		{
			if (btn == "yes")
			{
				gDialogObj.close();
				if (gPricingWindowExists)
				{
					Ext.getCmp('productConfigPricingGridWindow').close();
				}
			}
		}
	}

	function productTreeRefreshFailure()
	{
		Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorCouldNotRefreshProductTree');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
	}

	function getLinkedProductCodeFailure()
	{
		Ext.MessageBox.show({ title: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
", msg: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorCouldNotGetLinkedProduct');?>
", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.WARNING });
	}

	function changeNodeSortOrder(btn, ev)
	{
		var treeRootNode = tree.getRootNode();
		var nodeToMove = Ext.getCmp('tree').getSelectionModel().getSelectedNode();
		var parentNode = nodeToMove.parentNode;
		var firstChild = parentNode.firstChild;
		var lastChild = parentNode.lastChild;
		var nextSibling = nodeToMove.nextSibling;
		var previousSibling = nodeToMove.previousSibling;

		if (nextSibling != null || previousSibling != null)
		{
			switch(btn.id)
			{
				case 'send_to_top':
						parentNode.insertBefore(nodeToMove, firstChild);
						nodeToMove.select();
				break;
				case 'send_to_Bottom':
					if (lastChild.attributes.sectioncode == "ORDERFOOTER")
					{
						if (lastChild.previousSibling.attributes.sectioncode == "LINEFOOTER")
						{
							/* we know there is a linefooter so insert new node before linefooter node */
							nodeID = lastChild.previousSibling.id;
						    lineFooterNode = Ext.getCmp('tree').getNodeById(nodeID);
							parentNode.insertBefore(nodeToMove, lineFooterNode);
							nodeToMove.select();
						}
						else
							{
								/* LINEFFOTER does not exist so insert before ORDERFOOTER */
								parentNode.insertBefore(nodeToMove, lastChild);
								nodeToMove.select();
							}
					}
					else if (lastChild.attributes.sectioncode == "LINEFOOTER")
					{
							/* we know there is a linefooter so insert new node before linefooter node */
							nodeID = lastChild.id;
						    lineFooterNode = Ext.getCmp('tree').getNodeById(nodeID);
							parentNode.insertBefore(nodeToMove, lineFooterNode);
							nodeToMove.select();
					}
					else
					{
						parentNode.appendChild(nodeToMove);
						nodeToMove.select();

					}
				break;
				case 'move_up':
					if (previousSibling == null)
					{
						if (lastChild.attributes.sectioncode == "ORDERFOOTER")
						{
							if (lastChild.previousSibling.attributes.sectioncode == "LINEFOOTER")
							{
								/* we know there is a linefooter so insert new node before linefooter node */
								nodeID = lastChild.previousSibling.id;
								lineFooterNode = Ext.getCmp('tree').getNodeById(nodeID);
								parentNode.insertBefore(nodeToMove, lineFooterNode);
								nodeToMove.select();
							}
							else
							{
								/* LINEFFOTER does not exist so insert before ORDERFOOTER */
								parentNode.insertBefore(nodeToMove, lastChild);
								nodeToMove.select();
							}
						}
						else if (lastChild.attributes.sectioncode == "LINEFOOTER")
						{
								/* we know there is a linefooter so insert new node before linefooter node */
								nodeID = lastChild.id;
								lineFooterNode = Ext.getCmp('tree').getNodeById(nodeID);
								parentNode.insertBefore(nodeToMove, lineFooterNode);
								nodeToMove.select();
						}
						else
						{
							parentNode.insertBefore(nodeToMove, previousSibling);
							nodeToMove.select();
						}
					}
					else
					{
						parentNode.insertBefore(nodeToMove, previousSibling);
						nodeToMove.select();
					}

				break;
				case 'move_down':
					if (nodeToMove == lastChild)
					{
						parentNode.insertBefore(nodeToMove, firstChild);
						nodeToMove.select();
					}
					else
					{
						if (nextSibling.attributes.sectioncode == 'ORDERFOOTER' || nextSibling.attributes.sectioncode == 'LINEFOOTER')
						{
							parentNode.insertBefore(nodeToMove, firstChild);
							nodeToMove.select();

						}
						else
						{
							parentNode.insertBefore(nextSibling, nodeToMove);
							nodeToMove.select();
						}
					}
				break;
			}
		}
	}

	var companyCombo = new Ext.taopix.CompanyCombo({
		id: 'company',
		name: 'company',
		width:300,
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCompany');?>
",
		hideLabel:false,
		listeners:{
			select:{
				fn: function(comboBox, record, index){
					selectedCompanyCode = companyCombo.getValue();

						Ext.Ajax.request({
							   url: 'index.php?fsaction=AdminProducts.refreshProductTree&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
',
							   success: productTreeRefreshSuccess,
							   failure: productTreeRefreshFailure,
							   params: { productid: '<?php echo $_smarty_tpl->tpl_vars['productid']->value;?>
', companycode: selectedCompanyCode, getLinkedTree: 1},
							   method: 'GET'
							});

						productLinkingCombo.store.load({
							params: { companycode: companyCombo.getValue() }
						});

						Ext.getCmp('tree2').loader.on("beforeload", function(treeLoader, node) {
							categoryCode = categoriesCombo.getValue();
							Ext.getCmp('tree2').loader.baseParams.selection = categoryCode;
							Ext.getCmp('tree2').loader.baseParams.companycode = selectedCompanyCode;
					    }, this);
						Ext.getCmp('tree2').loader.load(Ext.getCmp('tree2').getRootNode(), treeLoadCallback);
					}
				}
		},
		allowBlank:false,
			<?php if ($_smarty_tpl->tpl_vars['companycode']->value == '') {?>
				defvalue: 'GLOBAL',
			<?php } else { ?>
				defvalue: '<?php echo $_smarty_tpl->tpl_vars['companycode']->value;?>
',
			<?php }?>
		options: {
			ref: '<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
',
			storeId: 'companyStore',
			<?php if ($_smarty_tpl->tpl_vars['companyLogin']->value) {?>
			includeGlobal: '0',
			<?php } else { ?>
			includeGlobal: '1',
			<?php }?>
			includeShowAll: '0',
			onchange: function(){var companyCode = companyCombo.getValue(); if (companyCode == 'GLOBAL') companyCode = '';}
		}
	});

	function disableTreeForLinkedProduct()
	{
		tree.enable();
		Ext.getCmp("deletesectionbutton").disable();
		Ext.getCmp("defaultItemButton").disable();
	}

	var selectedCompanyCode = Ext.getCmp('company').getValue();

	var productLinkingStore = new Ext.data.Store({
		id: "productlinkingstore",
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminProducts.productLinkingList&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', method: 'GET'}),
		reader: new Ext.taopix.PagedArrayReader({
			idIndex: 0},
			Ext.data.Record.create([
				{name: 'id', mapping: 0},
				{name: 'productcode', mapping: 1},
				{name: 'selectable', mapping: 2}
			])
		),
		baseParams: { companycode: selectedCompanyCode, productcode: '<?php echo $_smarty_tpl->tpl_vars['productcode']->value;?>
' }
	});

	var productLinkingCombo = new Ext.form.ComboBox({
		id: 'productlinkingcombo',
		name: 'productlinkingcombo',
		mode: 'local',
		width: 300,
		editable: false,
		forceSelection: false,
		store: productLinkingStore,
		valueField: 'productcode',
		displayField: 'productcode',
		disabled: true,
		triggerAction: 'all',
		selectOnFocus: true,
		listeners:{ 'select': function(theComboBox, selectedRecord, theIndex){
				selectedProductLinkID = selectedRecord.data.id;

				
				gDeletedItemsArray = [];

				if (selectedProductLinkID == 0)
				{
					Ext.Ajax.request({
							   url: 'index.php?fsaction=AdminProducts.refreshProductTree&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
',
							   success: productTreeRefreshSuccess,
							   failure: productTreeRefreshFailure,
							   params: { productid: '<?php echo $_smarty_tpl->tpl_vars['productid']->value;?>
', companycode: companyCombo.getValue()},
							   method: 'GET'
							});

						Ext.getCmp('tree2').loader.on("beforeload", function(treeLoader, node) {
							categoryCode = categoriesCombo.getValue();
							Ext.getCmp('tree2').loader.baseParams.selection = categoryCode;
							Ext.getCmp('tree2').loader.baseParams.companycode = companyCombo.getValue();
					    }, this);
						Ext.getCmp('tree2').loader.load(Ext.getCmp('tree2').getRootNode(), treeLoadCallback);

						
						Ext.getCmp('send_to_top').enable();
						Ext.getCmp('send_to_Bottom').enable();
						Ext.getCmp('move_up').enable();
						Ext.getCmp('move_down').enable();
						tree.enable();
						tree2.enable();
						categoriesCombo.enable();
						Ext.getCmp('pricingbutton').disable();
				}
				else
				{
					Ext.Ajax.request({
							   url: 'index.php?fsaction=AdminProducts.refreshProductTree&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
',
							   success: productTreeRefreshSuccess,
							   failure: productTreeRefreshFailure,
							   params: { productid: selectedProductLinkID, companycode: companyCombo.getValue()},
							   method: 'GET'
							});

						Ext.getCmp('tree2').loader.on("beforeload", function(treeLoader, node) {
							categoryCode = categoriesCombo.getValue();
							Ext.getCmp('tree2').loader.baseParams.selection = categoryCode;
							Ext.getCmp('tree2').loader.baseParams.companycode = companyCombo.getValue();
					    }, this);
						Ext.getCmp('tree2').loader.load(Ext.getCmp('tree2').getRootNode(), treeLoadCallback);

						
						Ext.getCmp('send_to_top').disable();
						Ext.getCmp('send_to_Bottom').disable();
						Ext.getCmp('move_up').disable();
						Ext.getCmp('move_down').disable();
						disableTreeForLinkedProduct();
						tree2.disable();
						categoriesCombo.disable();
						Ext.getCmp('pricingbutton').disable();
				}

				
				if (gPricingWindowExists)
				{
					Ext.getCmp('productConfigPricingGridWindow').close();
					gPricingWindowExists = false;
				}
        	},
			'beforeselect': function(theComboBox, selectedRecord, theIndex)
			{
				
				if (selectedRecord.data.selectable == 0)
				{
					return false;
				}
				else
				{
					return true;
				}
			}
		},
		value: '<?php echo $_smarty_tpl->tpl_vars['linkedproductcode']->value;?>
',
		tpl: '<tpl for="."><tpl if="selectable == 1"> <div class="x-combo-list-item">{productcode}</div></tpl><tpl if="selectable == 0"><div class="x-combo-list-item inactive">{productcode}</div></tpl></tpl>'
	});

	productLinkingStore.load();

	var categoriesStore = new Ext.data.Store({
		id: 'categoriesStore',
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AjaxAPI.callback&cmd=COMPONETCATEGORYLIST&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
&companycode='+selectedCompanyCode, method: 'GET'}),
		reader: new Ext.data.ArrayReader({
			idIndex: 0},
		Ext.data.Record.create([
		    {name: 'id', mapping: 0},
			{name: 'companycode', mapping: 1},
		    {name: 'code', mapping: 2},
		    {name: 'name', mapping: 3},
		    {name: 'prompt', mapping: 4},
		    {name: 'pricingmodel', mapping: 5},
		    {name: 'islist', mapping: 6},
		    {name: 'active', mapping: 7},
		    {name: 'requirespagecount', mapping: 8}
			])
		),
		listeners:
		{
        	'beforeload':function()
        	{
					categoriesStore.setBaseParam('companycode', selectedCompanyCode);
    		}
        }
	});

	var categoriesCombo = new Ext.form.ComboBox({
		id: 'categoriescombo',
		name: 'categoriescombo',
		mode: 'local',
		width: 430,
		editable: false,
		forceSelection: true,
		selectOnFocus: true,
		triggerAction: 'all',
		fieldLabel: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelTaxCode');?>
",
		store: categoriesStore,
		valueField: 'code',
		displayField: 'name',
		listeners:{
        	'select': function(){
				categoryCode = categoriesCombo.getValue();
				Ext.getCmp('tree2').loader.on("beforeload", function(treeLoader, node) {
					Ext.getCmp('tree2').loader.baseParams.selection = categoryCode;
					Ext.getCmp('tree2').loader.baseParams.companycode = companyCombo.getValue();
			    }, this);
				Ext.getCmp('tree2').loader.load(Ext.getCmp('tree2').getRootNode(), treeLoadCallback);

        	}
		},
		value: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSections');?>
',
		useID: true,
		post: true
	});

	Ext.getCmp('categoriescombo').store.load();

	Ext.getCmp('categoriescombo').store.on({'load': function() {
		var categoriesComboVal = Ext.getCmp('categoriescombo').store.getAt(0);
		Ext.getCmp('categoriescombo').setValue(categoriesComboVal.data['code']);
		}
	});

	var tree = new Ext.tree.TreePanel({
		 id: 'tree',
		 tbar: [
				 {
					id: 'deletesectionbutton',
					ref: '../deletesectionbutton',
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelRemove');?>
",
					iconCls: 'silk-delete',
					handler: onDeleteTreeItems,
					disabled: true
				},'-',
				{
					id: 'pricingbutton',
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonPricing');?>
",
					iconCls: 'silk-money',
					disabled: true,
					handler: pricingHandler
				},'-',
				{
					id: 'defaultItemButton',
					text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelDefault');?>
",
					iconCls: 'silk-tick',
					disabled: true,
					handler: setDefaultHandler
				}
		  ],
		 animate:true,
         autoScroll:true,
		 loader: new Ext.tree.TreeLoader({
             dataUrl:'index.php?fsaction=AdminProducts.refreshProductTree&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
',
             preLoadChildren: true
         }),
         enableDD: true,
         containerScroll: true,
         collapsed: false,
         border: false,
         style:'background:#fff; border:1px solid #8ca9cf',
         width: 430,
         height: 403,
         dropConfig: {appendOnly:false},
         listeners: {
        	 	'click': {fn: function(obj){onNodeClick(obj)}
         		},
				'nodedragover': {
		        	 fn: function(obj){

        	 			var dropNodeOwnerTree = obj.dropNode.getOwnerTree();
        	 			var targetOwnerTree = obj.target.getOwnerTree();
        	 			var treeRootNode = tree.getRootNode();

			        	/* are we dropping a section or component */
			        	if (obj.dropNode.attributes.issection)
			        	{
			        		if (dropNodeOwnerTree == targetOwnerTree)
			        		{
			        			return false;
			        		}

			        		/* check to see if the node we are trying to drop is either the order footer or line footer */
				        	if (obj.dropNode.attributes.sectioncode == 'ORDERFOOTER' || obj.dropNode.attributes.sectioncode == 'LINEFOOTER')
				        	{
				        		/*the node we drop the order footer or line footer must be the tree root node */
				        		if (obj.target != treeRootNode)
				        		{
				        			return false;
				        		}
				        		else
				        		{
				        			/*check to see if a order footer or line footer already exists */
				        			if (treeRootNode.firstChild !== null)
					        		{
				        				var nodeExists = doesNodeExist(obj.target, true, obj.dropNode.attributes.sectioncode);

				        				if (nodeExists == 1)
				        				{
				        					return false;
				        				}
					        		}
				        		}
				        	}
				        	else
				        	{
				        		/*Check to see if the target node has any grandparents to make sure that sections are only 1 level deep*/
				        		if ((!obj.target.attributes.issection) && (obj.target != treeRootNode))
				        		{
				        			if (obj.target.attributes.islist == '0')
				        			{
				        				return false;
				        			}

				        			var parent = obj.target.parentNode;
				        			var grandParent = '';

				        			if (parent.parentNode !== null)
				        			{
				        				grandParent = parent.parentNode;
				        			}
				        			else
				        			{
				        				grandParent = parent;
				        			}

				        			if (grandParent != treeRootNode)
				        			{
				        				if (grandParent.attributes.sectioncode != "ORDERFOOTER" && grandParent.attributes.sectioncode != 'LINEFOOTER')
				        				{
				        					if (grandParent.attributes.issection || obj.target.attributes.islist == '1')
				        					{
				        						return false;
				        					}
				        				}
				        			}
				        		}
				        		else if (obj.target.attributes.issection && obj.dropNode.attributes.issection)
				        		{
				        			if (obj.target.attributes.sectioncode != 'ORDERFOOTER' && obj.target.attributes.sectioncode != 'LINEFOOTER')
				        			{
				        				return false;
				        			}
				        		}

				        		/*handle the rest of section types*/
				        		if (obj.target.firstChild !== null)
				        		{
				        			var nodeExists = doesNodeExist(obj.target, true, obj.dropNode.attributes.sectioncode, true);

			        				if (nodeExists == 1)
			        				{
			        					return false;
			        				}
				        		}
				        	}
			        	}
			        	else if (obj.dropNode.attributes.islist == '0')
			        	{
			        		if (dropNodeOwnerTree == targetOwnerTree)
			        		{
			        			return false;
			        		}

			        		if (obj.target.attributes.islist == '0')
			        		{
			        			return false;
			        		}

			        		/*handle checkbox components */
			        		if (obj.target != treeRootNode)
			        		{
			        			var parent = obj.target.parentNode;
			        			var grandParent = '';

			        			if ((obj.target.attributes.issection) && (obj.target.parentNode.attributes.islist == '1'))
			        			{
			        				return false;
			        			}

			        			if (parent.parentNode !== null)
			        			{
			        				grandParent = parent.parentNode;
			        			}
			        			else
			        			{
			        				grandParent = parent;
			        			}

			        			if (grandParent != treeRootNode)
			        			{
			        				if (grandParent.attributes.islist == '1' && !grandParent.attributes.issection)
			        				{
			        					return false;
			        				}
			        			}

			        			if (obj.target.attributes.sectioncode != 'ORDERFOOTER' && obj.target.attributes.sectioncode != 'LINEFOOTER')
				        		{
				        			/*handle the rest of section types*/
					        		if (obj.target.firstChild !== null)
					        		{
					        			var nodeExists = doesNodeExist(obj.target, false, obj.dropNode.attributes.componentcode);

				        				if (nodeExists == 1)
				        				{
				        					return false;
				        				}
					        		}
				        		}

				        		if (obj.target.attributes.sectioncode == 'ORDERFOOTER' || obj.target.attributes.sectioncode == 'LINEFOOTER')
				        		{
				        			/*handle the rest of section types*/
					        		if (obj.target.firstChild !== null)
					        		{
					        			var nodeExists = doesNodeExist(obj.target, false, obj.dropNode.attributes.componentcode);

				        				if (nodeExists == 1)
				        				{
				        					return false;
				        				}
					        		}
				        		}


			        		}
			        		else
			        		{
			        			if (obj.target.firstChild !== null)
				        		{
			        				var nodeExists = doesNodeExist(obj.target, false, obj.dropNode.attributes.componentcode);

			        				if (nodeExists == 1)
			        				{
			        					return false;
			        				}
				        		}
			        		}
			        	}
			        	else
			        	{
			        		if ((obj.target.attributes.sectioncode == 'ORDERFOOTER' || obj.target.attributes.sectioncode == 'LINEFOOTER') && (!obj.dropNode.attributes.issection))
				        	{
			        			return false;
				        	}

			        		if (!obj.target.attributes.issection && !obj.dropNode.attributes.issection)
		        			{
		        				return false;
		        			}

			        		if (dropNodeOwnerTree == targetOwnerTree)
			        		{
			        			if (obj.target.attributes.parentpath != obj.dropNode.attributes.parentpath)
			        			{
			        				return false;
			        			}
			        		}

			        		/*handle components*/
			        		if (obj.target == treeRootNode)
			        		{
			        			return false;
			        		}

			        		if (obj.target.attributes.sectioncode != 'ORDERFOOTER' && obj.target.attributes.sectioncode != 'LINEFOOTER')
			        		{
			        			if (obj.target.attributes.sectioncode != obj.dropNode.attributes.sectioncode)
				        		{
			        				return false;
				        		}
			        		}

			        		if (obj.target.firstChild !== null)
			        		{
		        				var nodeExists = doesNodeExist(obj.target, false, obj.dropNode.attributes.componentcode);

		        				if (nodeExists == 1)
		        				{
		        					return false;
		        				}
			        		}
			        	}

			        	function doesNodeExist(pTarget, pIsSection, pItem, pCheckRootForCheckBoxItems)
			        	{
			        		var hasChildren = true;
			        		var nodeExists = 0;
	        				var child = pTarget.firstChild;
	        				var lastChild = pTarget.lastChild;

	        				/*check to see if the function has been called with an optional parameter */
	        				if (pCheckRootForCheckBoxItems === undefined)
	        				{
	        					pCheckRootForCheckBoxItems = false;
	        				}

	        				while (hasChildren)
			        		{
	        					if (pIsSection)
	        					{
	        						var targetChildItem = child.attributes.sectioncode;
	        					}
	        					else
	        					{
	        						var targetChildItem = child.attributes.componentcode;
	        					}

	        					if (child == lastChild)
	        					{
	        						hasChildren = false;
	        					}

	        					if (targetChildItem == pItem)
	        					{
	        						/*if there are checkboxitems assigned to the tree at root level and then drag a section over to the root that has the same section code as the checboxitem*/
	        						/*we need to perfrom an extra check to make sure that the item found is not a section. If it is then prevent adding another section of the same sectioncode*/
	        						if (pCheckRootForCheckBoxItems)
	        						{
	        							if (child.attributes.issection)
	        							{
	        								nodeExists = 1;
	        							}
	        						}
	        						else
	        						{
	        							nodeExists = 1;
	        						}
	        					}

	        					child = child.nextSibling;
			        		}

	        				return nodeExists;
			        	}
					}
				},
				'beforenodedrop': {
		        	 fn: function(obj){
							var processNode	= false;
							var dropNodeOwnerTree = obj.dropNode.getOwnerTree();
    	 					var targetOwnerTree = obj.target.getOwnerTree();
    	 					var appendNode = '';
							var icon = obj.dropNode.attributes.iconCls;
							var isSection = true;
							var companyCode = '';
							var componentCode = '';
							var rootFirstChildNode = '';
							var treeRootNode = tree.getRootNode();
							var parentPath = '';
							var pathDepth = 0;
							var allowInherit = 0;

							cls = '';

							if (obj.dropNode.attributes.active == '0')
							{
								cls = 'list-component-inactive';
							}

							if (obj.dropNode.attributes.issection)
							{
								isSection = true;
								componentCode = '';
								categoryCode = '';
								pricingModel = '';
								var sectionCode = obj.dropNode.attributes.sectioncode;
							}
							else
							{
								isSection = false;
								displayText = obj.dropNode.attributes.componentcode;
								companyCode = obj.dropNode.attributes.companycode;
								componentCode = obj.dropNode.attributes.componentcode;
								categoryCode = obj.dropNode.attributes.categorycode;
								pricingModel = obj.dropNode.attributes.pricingmodel;
								var sectionCode = obj.target.attributes.sectioncode;

								// find out how deep the path goes
								pathDepth = 0;

								// adjust the path depth test if the component is part of the order or line footer
								var footerAdjustment = 0;

								// do not display option to inherit if the product is single prints or calendar customisation options
								if ((sectionCode != 'SINGLEPRINTOPTION') && (sectionCode != 'CALENDARCUSTOMISATION'))
								{
									var tNode = obj.target;
									var nParent = tNode.parentNode;

									// loop through parents until the parentNode is the root node (has no parent)
									while (nParent != null)
									{
										// component is part of the footer, change the path depth test to take this into account
										if ((nParent.attributes.sectioncode == 'ORDERFOOTER') || (nParent.attributes.sectioncode == 'LINEFOOTER'))
										{
											footerAdjustment = 1;
										}

										nParent = nParent.parentNode;

										pathDepth++;
									}
								}

								// use the path depth and type of sub-component to determine if it can inherit the parent qty
								if (((pathDepth == (3 + footerAdjustment)) && (obj.dropNode.attributes.islist == 1)) ||
									((pathDepth == (2 + footerAdjustment)) && (obj.dropNode.attributes.islist == 0)))
								{
									// only sub component with pricing models of 7 or 8 can inherit parent qty
									if ((pricingModel == 7) || (pricingModel == 8))
									{
										allowInherit = 1;
									}
								}

								if (obj.dropNode.attributes.islist == '0')
								{
									if (obj.target == treeRootNode)
									{
										if (obj.target.firstChild !== null)
						        		{
											rootFirstChildNode = obj.target.firstChild;
											beforeNode = rootFirstChildNode;
						        		}
									}
								}
							}

							/* newNodeID is set to 0 on the page load. Decrement the newNodeID to protect against duplicate ids */
							newNodeID = --newNodeID;
							appendNode = {
											id: newNodeID,
											text: obj.dropNode.text,
											children: [],
											removed: false,
											hasdefaultprice: obj.dropNode.attributes.hasdefaultprice,
											pricelinkparentids: '0',
											initialized: '0',
											listeners:
											{
												'click':
												{
													fn: function(obj)
													{
														onNodeClick(obj)
													}
												}
											},
											issection: isSection,
											companycode: companyCode,
											componentcode: componentCode,
											code: obj.dropNode.attributes.code,
											sectioncode: sectionCode,
											pricingmodel: pricingModel,
											categorycode: categoryCode,
											enabled: true,
											expandable: obj.dropNode.expandable,
											leaf: false,
											islist: obj.dropNode.attributes.islist,
											decimalplaces: obj.dropNode.attributes.decimalplaces,
											iconCls: obj.dropNode.attributes.iconCls,
											isdefault: '0',
											cls: cls,
											pathdepth: pathDepth,
											allowinherit: allowInherit,
											inheritparentqty: 0
										};

							/*when assigning ORDERFOOTER OR LINEFOOTER we need to add them in order so that LINEFOOTER always comes before ORDERFOOTER  */
							if (obj.dropNode.attributes.sectioncode == 'ORDERFOOTER' || obj.dropNode.attributes.sectioncode == 'LINEFOOTER')
							{
								/*Does the target node have any children if it does look to see if ORDERFOOTER already exists*/
								if (obj.target.firstChild !== null)
								{
									var newNode = '';
									var hasChildren = true;
									var orderFooterExists = false;
									var orderFooterNode = '';
			        				var child = obj.target.firstChild;
			        				var lastChild = obj.target.lastChild;

			        				while (hasChildren)
					        		{
			        					if (child == lastChild)
			        					{
			        						hasChildren = false;
			        					}

			        					if (child.attributes.sectioncode == 'ORDERFOOTER')
			        					{
			        						orderFooterExists = true;
			        						orderFooterNode = child;
			        					}

			        					child = child.nextSibling;
					        		}
								}

								/*if ORDERFOOTER exists and the dropNode is LINEFOOTER then insert LINEFOOTER before ORDERFOOTER. Other wise just append the dropNode */
								if ((orderFooterExists) && (obj.dropNode.attributes.sectioncode == 'LINEFOOTER'))
								{
									processNode = true;
									newNode = obj.target.insertBefore(appendNode, orderFooterNode);
								}
								else
								{
									processNode = true;
									newNode = obj.target.appendChild(appendNode);
								}
							}
							else
							{
								if (rootFirstChildNode != '')
								{
									processNode = true;
									newNode = obj.target.insertBefore(appendNode, rootFirstChildNode);

								}
								else
								{
									if (obj.target.firstChild !== null)
									{
										var newNode = '';
										var hasChildren = true;
										var orderFooterExists = false;
										var lineFooterExists = false;
										var orderFooterNode = '';
										var child = obj.target.firstChild;
										var lastChild = obj.target.lastChild;

										while (hasChildren)
										{
											if (child == lastChild)
											{
												hasChildren = false;
											}

											if (child.attributes.sectioncode == 'ORDERFOOTER')
											{
												orderFooterExists = true;
												orderFooterNode = child;
											}

											if (child.attributes.sectioncode == 'LINEFOOTER')
											{
												lineFooterExists = true;
												lineFooterNode = child;
											}

											if (hasChildren)
											{
												child = child.nextSibling;
											}
										}
									}

									if (dropNodeOwnerTree != targetOwnerTree )
									{
										/*if ORDERFOOTER exists and LINEFOOTER does not exist then insert before orderfooter */
										if ((orderFooterExists) && (!lineFooterExists))
										{
											processNode = true;
											newNode = obj.target.insertBefore(appendNode, orderFooterNode);
										}
										/*if LINEFOOTER exists then insert before LINEFOOTER */
										else if (lineFooterExists)
										{
											processNode = true;
											newNode = obj.target.insertBefore(appendNode, lineFooterNode);
										}
										else
										{
											processNode = true;
											newNode = obj.target.appendChild(appendNode);
										}
									}
								}
							}

							if (processNode)
							{
								/* when adding a new parent node we must expand it otherwise we cannot any child nodes added are not rendered*/
								newNode.expand(true, false);
								beforeNode = '';

								return false;
							}
					}
				}
			}
     });

	function onLinkingPreview(btn, ev)
	{
		var serverParams = new Object();

		if (! productLinkingPreviewWindowExists)
		{
			productLinkingPreviewWindowExists = true;
			Ext.taopix.loadJavascript(gMainWindowObj, '', 'index.php?fsaction=AdminProducts.linkingPreviewDisplay.&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
', serverParams, '', 'initialize', false);
		}
	};

	var root = new Ext.tree.AsyncTreeNode({
    	text: '<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
',
    	sectioncode: 'PRODUCT',
    	iconCls: 'silk-folder',
    	listeners: {'click': {fn: function(obj){onNodeClick(obj)}}},
		expanded: true,
		singleClickExpand: true,
    	children:<?php echo $_smarty_tpl->tpl_vars['tree']->value;?>

     });

     tree.setRootNode(root);

     var tree2 = new Ext.tree.TreePanel({
    	 id: 'tree2',
		 animate:true,
         autoScroll:true,
		 rootVisible: false,
		 cls: "component-options-column",
         lines: false,
         loader: new Ext.tree.TreeLoader({
             dataUrl:'index.php?fsaction=AdminProducts.getComponentsFromCategory&ref=<?php echo $_smarty_tpl->tpl_vars['ref']->value;?>
',
             preLoadChildren: true,
			 baseParams: {csrf_token: Ext.taopix.getCSRFToken()}
         }),
         enableDrop:false,
         enableDrag:true,
         containerScroll: true,
         collapsed: false,
         border: true,
         style:'background:#fff; border:1px solid #8ca9cf; ; border-top: 0px',
         width: 430,
         height: 381,
         dropConfig: {appendOnly:true}
     });

     Ext.getCmp('tree2').loader.on("beforeload", function(treeLoader, node) {
        Ext.getCmp('tree2').loader.baseParams.companycode = '<?php echo $_smarty_tpl->tpl_vars['companycode']->value;?>
';
    }, this);

     /* add a tree sorter in folder mode */
     new Ext.tree.TreeSorter(tree2, {folderSort:true});

     var root2 = new Ext.tree.AsyncTreeNode({
    	text: '<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
',
    	expanded: true
     });

     tree2.setRootNode(root2);

     Ext.getCmp('tree2').loader.on("beforeload", function(treeLoader, node) {
    	 <?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
				<?php if ($_smarty_tpl->tpl_vars['companyLogin']->value) {?>
					companyCode = '<?php echo $_smarty_tpl->tpl_vars['companycode']->value;?>
';
				<?php } else { ?>
					companyCode = companyCombo.getValue();
				<?php }?>
			<?php } else { ?>
				companyCode = '';
			<?php }?>

 		Ext.getCmp('tree2').loader.baseParams.selection = 'SECTIONS';
     }, this);


    var sendToTopButton = { xtype: 'button', iconCls: 'send_to_top', id:'send_to_top', disabled: true, handler: changeNodeSortOrder, style:"margin: 105px 0px 7px 0px"};
    var sendToBottomButton = { xtype: 'button', iconCls: 'send_to_Bottom', id:'send_to_Bottom', disabled: true, handler: changeNodeSortOrder, style:"margin: 0px 0px 7px 0px"};
    var moveUpButton = { xtype: 'button', iconCls: 'move_up', id:'move_up', disabled: true, handler: changeNodeSortOrder, style:"margin: 0px 0px 7px 0px"};
    var moveDownButton = { xtype: 'button', iconCls: 'move_down', id:'move_down', disabled: true, handler: changeNodeSortOrder, style:"margin: 0px 0px 7px 0px"};

    var dialogFormPanelObj = new Ext.FormPanel({
		id: 'productConfigForm',
        labelAlign: 'left',
        labelWidth:60,
        autoHeight: true,
        frame:true,
        cls: 'left-right-buttons',
        bodyStyle:'padding-left:1px;',
        items: [
            {
            xtype: 'panel',
            id: 'topPanel',
            layout: 'form',
            style:'background:#c9d8ed; padding: 3px 0; border:1px solid #8ca9cf; margin-bottom:7px;',
            plain:true,
            bodyBorder: false,
        	border: false,
        	defaults:{labelWidth: 70},
        	width: 927,
        	bodyStyle:'padding:0px 4px 0; border-top: 0px',

        	items:[
				<?php if ($_smarty_tpl->tpl_vars['optionms']->value) {?>
					companyCombo, {xtype: 'tbspacer', width: 10}, 
				<?php }?>
				{
					xtype: 'container',
					layout: 'hbox',
					align: 'middle',
					items: [
						{xtype: 'label', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLinkedProductConfiguration');?>
:", forId: 'productlinkingcombo', cls: 'x-form-item', style: 'padding: 2px'},
						{xtype: 'tbspacer', width: 10},
						productLinkingCombo,
						{xtype: 'tbspacer', width: 10},
						{
							id:'configLinkingPreviewButton', 
							ref: '../configLinkingPreviewButton', 
							text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLinkingPreview');?>
', 
							iconCls: 'silk-link',
							handler: onLinkingPreview, 
							disabled: false,
							xtype: 'button',
							hidden: hidePreview
						}
					]
				}
				],
            },
            {
            xtype: 'container',
            layout:'column',
            width: 960,
            items:[{
                xtype: 'container',
                width: 435,
                items: [tree]
            },{
                xtype: 'container',
                style: "padding-left: 20px",
				cls: 'tpx-component-button-wrap-outer',
                width:64,
                items: [{
					xtype: 'container',
					cls: 'tpx_component_button_wrap',
					width: 64,
					items: [sendToTopButton, moveUpButton, moveDownButton, sendToBottomButton]
				}]
            },
            {
                xtype: 'container',
                width: 435,
                items: [categoriesCombo,tree2]
            },
            { xtype: 'hidden',
              id: 'productcode',
              name: 'productcode',
              value: "<?php echo $_smarty_tpl->tpl_vars['productcode']->value;?>
",
              post: true
             }
            ]
        }]
    });

    gDialogObj = new Ext.Window({
		id: 'productConfigDialog',
	  	closable:false,
	  	title: "<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
",
	  	plain:true,
	  	modal:true,
	  	draggable:true,
	 	resizable:false,
	  	layout: 'fit',
	  	height: 'auto',
	  	width: 960,
	  	items: dialogFormPanelObj,
	  	listeners: {
			'close': {
				fn: function(){
    				productCongfigWindowExists = false;
				}
			},
			'show': {
				fn: function(){
					tree2.disable();
				}
			}
		},
	  	cls: 'left-right-buttons tpx-components-window',
	  	tools:[{
		    id:'clse',
		    qtip: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelCloseWIndow');?>
',
		    handler: closeConfigWindow
		}],
	  	buttons:
		[
		 	{id:'editconfig', text: '<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonEdit');?>
', iconCls: 'silk-pencil', handler: editConfigHandler, cls: 'x-btn-left', ctCls: 'width_100'},
			{ text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
", id: 'cancelconfig', handler: editConfigHandler, disabled: true},
			{ id: 'addEditButton', text: "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonSave');?>
", disabled: true, handler: addsaveHandler}
		]
	});

    tree.disable();
    categoriesCombo.disable();

    <?php if ($_smarty_tpl->tpl_vars['companycode']->value != '') {?>
    	companyCombo.disable();
    <?php }?>

	var mainPanel = Ext.getCmp('productConfigDialog');
	mainPanel.show();
}
<?php }
}
