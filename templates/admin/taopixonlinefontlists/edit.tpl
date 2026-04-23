function initialize(pParams)
{

    function getRules()
    {
        var returnData = [];

        Ext.getCmp('ruleGrid').store.each(function(el, key) {
            returnData.push(el.data);
        });

        return JSON.stringify(returnData);
    }

    function saveHandler()
    {
        var submitURL = 'index.php?fsaction=AdminTaopixOnlineFontLists.savefontlist&ref={$ref}';
		var fp = Ext.getCmp('fontListForm');
        var paramArray = {literal}{}{/literal};
        paramArray.fonts = Ext.getCmp('fonts').getChecked('id');
        paramArray.rules = getRules();

		var submit = true;

        if (Ext.getCmp('name').getValue() == '')
        {
            Ext.getCmp('name').markInvalid();
            submit = false;
        }

        if (submit && paramArray.fonts.length < 1)
        {
            icon = Ext.MessageBox.ERROR;

            Ext.MessageBox.show({
                title: "{#str_TitleError#}",
                msg: "{#str_InvalidSelectionError#}".replace('^0', "{#str_TitleFontSection#}".toLowerCase()),
                buttons: Ext.MessageBox.OK,
                icon: icon
            });
            submit = false;
        }

		if (submit)
		{
			Ext.taopix.formPanelPost(fp, fp.getForm(), paramArray, submitURL, "{#str_MessageSaving#}", saveCallback);
		}
    }

    function saveCallback(pUpdated, pActionForm, pActionData)
    {
        if (pUpdated)
        {
            var gridObj = gMainWindowObj.findById('maingrid');

            // Disable edit, copy & delete buttons on save until the reload has completed.
            if (gridObj.selModel.getCount() > 0)
            {
                gridObj.editButton.disable();
                gridObj.deleteButton.disable();
            }

            gridObj.store.reload({
                callback: function(r, options, success) {
                    if (gridObj.selModel.getCount() > 0)
                    {
                        gridObj.editButton.enable();
                        gridObj.deleteButton.enable();
                    }
                }
            });
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

    /*
     * Override the TreeNodeUI methods for click/double click
     * Add toggle of checkboxes when selecting a parent element or removing a child element
     */
    Ext.tree.TreeNodeUI.override({
        onClick : function (e) {
            e.preventDefault();
            if (this.disabled) {
                return;
            }
            if (this.fireEvent("beforeclick", this.node, e) !== false) {
                if (this.checkbox) {
                    this.toggleCheck();
                }

                this.fireEvent("click", this.node, e);
            }
        },
        onDblClick: function(e) {
            e.preventDefault();
            if (this.disabled) {
                return;
            }
            if (this.fireEvent("beforedblclick", this.node, e) !== false) {
                if (this.checkbox) {
                    this.toggleCheck();
                }

                this.fireEvent("dblclick", this.node, e);
            }
        },
        onCheckChange: function(e) {
            var checked = this.checkbox.checked;
            this.checkbox.defaultChecked = checked;
            this.node.attributes.checked = checked;

            if (this.node.hasChildNodes()) {
                var selected = this.node.ownerTree.getChecked('id', this.node);

                // Make sure that events are not suspended for this item, no eventSuspended prop means we aren't the parent node.
                if ((this.node.eventsSuspended === undefined || false === this.node.eventsSuspended) && (selected.indexOf(this.node.attributes.id) !== -1 || checked === false)) {
                    this.node.eachChild(function(n) {
                        if (null === n.ui) {
                            n.attributes.checked = checked;
                        }
                        else
                        {
                            n.ui.toggleCheck(checked);
                        }
                    });
                }

                if (e !== undefined) {
                    this.fireEvent('checkchange', this.node, checked);
                }
            }
            else
            {
                if (!checked && this.node.parentNode.ui.rendered && this.node.parentNode.attributes.checked) {
                    this.node.parentNode.suspendEvents(false);
                    this.node.parentNode.ui.toggleCheck(checked);
                    this.node.parentNode.resumeEvents();
                }

                if(checked && !this.node.parentNode.attributes.checked) {
                    var selected = this.node.ownerTree.getChecked('id', this.node.parentNode);

                    if (selected.length == this.node.parentNode.childNodes.length)
                    {
                        this.node.parentNode.ui.toggleCheck(true);
                    }
                }
            }

            updateFontsSelected();
        },
        renderElements: function(n, a, targetNode, bulkRender) {
            this.indentMarkup = n.parentNode ? n.parentNode.ui.getChildIndent() : '';

            var cb = Ext.isBoolean(a.checked),
            nel,
            href = this.getHref(a.href),
            buf = ['<li class="x-tree-node"><div ext:tree-node-id="',n.id,'" class="x-tree-node-el x-tree-node-leaf x-unselectable ', a.cls,'" unselectable="on">',
                    '<span class="x-tree-node-indent">',this.indentMarkup,"</span>",
                    '<img alt="" src="', this.emptyIcon, '" class="x-tree-ec-icon x-tree-elbow" />',
                    cb ? ('<input class="x-tree-node-cb" type="checkbox" ' + (a.checked ? 'checked="checked" />' : '/>')) : '',
                    '<img alt="" src="', a.icon || this.emptyIcon, '" class="x-tree-node-icon',(a.icon ? " x-tree-node-inline-icon" : ""),(a.iconCls ? " "+a.iconCls : ""),'" unselectable="on" />',
                    '<a hidefocus="on" class="x-tree-node-anchor" href="',href,'" tabIndex="1" ',
                    a.hrefTarget ? ' target="'+a.hrefTarget+'"' : "", '><span unselectable="on">',n.text,"</span></a></div>",
                    '<ul class="x-tree-node-ct" style="display:none;"></ul>',
            "</li>"].join('');

            if(bulkRender !== true && n.nextSibling && (nel = n.nextSibling.ui.getEl())){
                this.wrap = Ext.DomHelper.insertHtml("beforeBegin", nel, buf);
            } else {
                this.wrap = Ext.DomHelper.insertHtml("beforeEnd", targetNode, buf);
            }

            this.elNode = this.wrap.childNodes[0];
            this.ctNode = this.wrap.childNodes[1];
            var cs = this.elNode.childNodes;
            this.indentNode = cs[0];
            this.ecNode = cs[1];
            this.iconNode = cs[3];
            var index = 3;
            if(cb){
                this.checkbox = cs[2];
                this.checkbox.defaultChecked = this.checkbox.checked;
                index++;
            }
            this.anchor = cs[index];
            this.textNode = cs[index].firstChild;
        }
    });

    function updateFontsSelected()
    {
        var list = Ext.getCmp('fonts').getChecked();
        var outerList = [];
        var familyMap = {literal}{}{/literal};
        var ulEl = document.createElement('ul');
        ulEl.classList.add('font-view');

        if (0 < list.length) {
            for (var i = 0; i < list.length; i++) {
                if (true === list[i].leaf) {
                    var li = document.createElement('li');
                    var parentName = "{#str_LabelAll#}" === list[i].parentNode.attributes.text ? '' : list[i].parentNode.attributes.text + ' ';

                    li.appendChild(document.createTextNode(parentName + list[i].attributes.text));
                    ulEl.appendChild(li);
                }
            }
        }

        Ext.getCmp('fontListSelection').render().body.dom.style.overflow = 'auto';
        Ext.getCmp('fontListSelection').render().body.dom.innerHTML = '<h1>{#str_LabelSelectedFonts#}</h1>' + ulEl.outerHTML;
    }

    function showProductSelection()
    {
        Ext.getCmp('productSelect').getChecked().forEach(function(item) {
            if (! item.leaf) {
                item.ui.toggleCheck(false);
            }
            else
            {
                if (item.rendered && item.attributes.checked) {
                    item.ui.toggleCheck(false)
                }
            }
        });
        Ext.getCmp('bb-brand').setValue(null);
        Ext.getCmp('bb-license').setValue(null);
        Ext.getCmp('productSelectionWindow').show();
    }

    function buildComboBox(pFieldName, pLabel, pAllLabel, pData, pAddAll)
    {
        var comboItems = pData;
        if (pAddAll) {
            comboItems.unshift([pAllLabel]);
        }

        return {
            xtype: 'combo',
            id: 'bb-' + pFieldName,
            name: 'bb-' + pFieldName,
            emptyText: pAllLabel,
            fieldLabel: pLabel,
            mode: 'local',
            editable: false,
            forceSelection: true,
            store: new Ext.data.ArrayStore({
                id: 0,
                fields: [
                    'code'
                ],
                data: pData
            }),
            selectOnFocus: true,
            triggerAction: 'all',
            value: pAllLabel,
            valueField: 'code',
            displayField: 'code',
            useID: true,
            allowBlank: true,
            post: false,
            minWidth: 800,
            maxWidth: 800,
            style: {
                width: '90%',
                marginBottom: '5px',
                marginLeft: '0px'
            }
        };
    }

    function generalColumnRenderer(value, p, record)
    {
        return null === value ? "<span class='inactive'>**{#str_LabelAll#}**</span>" : ('__DEFAULT__' === value ? "{#str_LabelDefault#}" : value);
    }

    function deleteRules() {
        var gridObj = Ext.getCmp('ruleGrid');
        var selectedList = [];

        gridObj.selModel.each(function(item) {
            gridObj.store.remove(item);
        });
    }

    var tree = new Ext.tree.TreePanel({
        id: 'fonts',
        baseCls: 'x-plain',
        width: 'auto',
        height: 500,
        rootVisible: false,
        useArrows: true,
        animate: true,
        enableDD: false,
        autoScroll: true,
        border: false,
        root: new Ext.tree.AsyncTreeNode({
            expanded: true,
            children: {$fontlist}
        }),
        post: true
    });

    var brandCombo = buildComboBox('brand', '{#str_LabelBrandCode#}', '{#str_LabelAllBrands#}', {$brandCodes}, true);
    var licenseCombo = buildComboBox('license', '{#str_LabelLicenseKeyCode#}', '{#str_LabelAllLicenseKeys#}', {$groupCodes}, true);

    var ruleGridDataStore = new Ext.data.ArrayStore({
        autoDestroy: true,
        storeId: 'ruleStore',
        idIndex: 0,
        fields: [
            'id',
            'brandcode',
            'licensecode',
            'collectioncode',
            'productcode'
        ]
    });
    ruleGridDataStore.loadData({$rulelist});

    var ruleGridSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
        listeners: {
            selectionchange: function(ruleGridSelectionModelObj)
            {
                var selectionCount = ruleGridSelectionModelObj.getCount();
                if (0 === selectionCount)
                {
                    ruleGridObj.deleteButton.disable();
                }
                else
                {
                    ruleGridObj.deleteButton.enable();
                }
            }
        }
    });

    var ruleGridColumns = new Ext.grid.ColumnModel({
        defaults: {
            sortable: false,
            resizable: true
        },
        columns: [
            {
                header: "{#str_LabelBrandCode#}",
                dataIndex: 'brandcode',
                renderer: generalColumnRenderer
            },
            {
                header: "{#str_LabelLicenseKeyCode#}",
                dataIndex: 'licensecode',
                renderer: generalColumnRenderer
            },
            {
                header: "{#str_LabelProduct#}",
                dataIndex: 'productcode',
                renderer: generalColumnRenderer
            }
        ]
    });

    var ruleGridObj = new Ext.grid.GridPanel({
        id: 'ruleGrid',
        store: ruleGridDataStore,
        cm: ruleGridColumns,
        autoExpandColumn: 2,
        enableColLock: false,
        draggable: false,
        enableColumnHide: false,
        enableColumnMove: false,
        enableHdMenu: false,
        trackMouseOver: false,
        stripeRows: true,
        columnLines: true,
        ctCls: 'grid',
        //selModel: ruleGridSelectionModelObj,
        width: 'auto',
        autoHeight: true
        /*tbar: [
            {
                ref: '../addButton',
                text: "{#str_ButtonAdd#}",
                iconCls: 'silk-add',
                handler: showProductSelection,
                disabled: false
            }, '-',
            {
                ref: '../deleteButton',
                text: "{#str_ButtonDelete#}",
                iconCls: 'silk-delete',
                handler: deleteRules
            }
        ]*/
    });

    var productSelectionTree = new Ext.tree.TreePanel({
        title: "{#str_LabelSelectProducts#}",
        id: 'productSelect',
        baseCls: 'x-plain',
        width: 600,
        height: 400,
        rootVisible: false,
        useArrows: true,
        animate: true,
        enabledDD: false,
        autoScroll: true,
        border: false,
        root: new Ext.tree.AsyncTreeNode({
            expanded: false,
            children: {$collectionList}
        }),
        post: false
    });

    var productSelectionWindow = new Ext.Window({
        id: 'productSelectionWindow',
        closable: false,
        plain: true,
        modal: true,
        draggable: true,
        resizable: false,
        layout: 'fit',
        autoHeight: true,
        width: 610,
        closeAction: 'hide',
        items: [
            {
                xtype: 'fieldset',
                layout: 'form',
                items: [
                    brandCombo,
                    licenseCombo,
                    productSelectionTree
                ],
                autoHeight: true
            }
        ],
        title: "{#str_LabelSelectedProducts#}",
        buttons: [
            {
                id: 'closeButton',
                cls: 'x-btn-right',
                text: '{#str_LabelClose#}',
                handler: function() {
                    Ext.getCmp('productSelectionWindow').hide();
                }
            },
            {
                text: "{#str_LabelCreateRule#}",
                handler: function() {
                    var brand = Ext.getCmp('bb-brand').value;
                    var license = Ext.getCmp('bb-license').value;

                    if (undefined === brand || "" === brand) {
                        brand = null;
                    }

                    if (undefined === license || "" === license) {
                        license = null;
                    }

                    var selectedProducts = Ext.getCmp('productSelect').getChecked();
                    var products = [];
                    var collections = [];

                    var currRuleStore = Ext.getCmp('ruleGrid').store;
                    var newId = -1;

                    // Fix values so we do not send collection code and all product ids to the store.
                    if (0 < selectedProducts.length) {
                        for (var i = 0; i < selectedProducts.length; i++) {
                            // If we are a leaf node and the parent is in the list skip this item
                            if (selectedProducts[i].leaf && -1 !== collections.indexOf(selectedProducts[i].parentNode.attributes.id.substr(2))) {
                                continue;
                            }

                            if (selectedProducts[i].leaf) {
                                products.push(selectedProducts[i].id);
                            }
                            else
                            {
                                collections.push(selectedProducts[i].id.substring(2));
                            }
                        }

                        if (0 < collections.length) {
                            collections.forEach(function(item) {
                                var storeItemData = {
                                    id: --newId,
                                    brandcode: brand,
                                    licensecode: license,
                                    collectioncode: item,
                                    productcode: null
                                }
                                var exists = false;
                                currRuleStore.each(function(rec) {
                                    if (rec.get('brandcode') === storeItemData.brandcode && rec.get('licensecode') === storeItemData.licensecode
                                        && rec.get('collectioncode') === storeItemData.collectioncode && rec.get('productcode') === storeItemData.productcode)
                                    {
                                        exists = true;
                                        return;
                                    }
                                });

                                if (! exists) {
                                    var storeItem = new currRuleStore.recordType(storeItemData, null);
                                    currRuleStore.add(storeItem);
                                }
                            });
                        }

                        if (0 < products.length) {
                            products.forEach(function(item) {
                                var storeItemData = {
                                    id: --newId,
                                    brandcode: brand,
                                    licensecode: license,
                                    collectioncode: null,
                                    productcode: item
                                }

                                var exists = false;
                                currRuleStore.each(function(rec) {
                                    if (rec.get('brandcode') === storeItemData.brandcode && rec.get('licensecode') === storeItemData.licensecode
                                    && rec.get('collectioncode') === storeItemData.collectioncode && rec.get('productcode') === storeItemData.productcode)
                                    {
                                        exists = true;
                                        return;
                                    }
                                });

                                if (! exists) {
                                    var storeItem = new currRuleStore.recordType(storeItemData, null);
                                    currRuleStore.add(storeItem);
                                }
                            });
                        }
                    }
                    else
                    {
                        var storeItemData = {
                            id: --newId,
                            brandcode: brand,
                            licensecode: license,
                            collectioncode: null,
                            productcode: null
                        }

                        var exists = false;
                        currRuleStore.each(function(rec) {
                            if (rec.get('brandcode') === storeItemData.brandcode && rec.get('licensecode') === storeItemData.licensecode
                            && rec.get('collectioncode') === storeItemData.collectioncode && rec.get('productcode') === storeItemData.productcode)
                            {
                                exists = true;
                                return;
                            }
                        });

                        if (! exists) {
                            var storeItem = new currRuleStore.recordType(storeItemData, null);
                            currRuleStore.add(storeItem);
                        }
                    }

                    Ext.getCmp('productSelectionWindow').hide();
                },
                cls: 'x-btn-right'
            }
        ]
    });

    var productSelectionPanel = {
        id: 'assignmentPanel',
        xtype: 'panel',
        layout: 'form',
        title: 'Assignment',
        autoHeight: false,
        height: 'auto',
        width: 550,
        frame: true,
        collapsable: false,
        hideLabel: true,
        cls: 'white-panel',
        items: [
            ruleGridObj
        ]
    }

    var treeData = {
        id: 'fontListSelection',
        xtype: 'panel',
        layout: 'form',
        height: 500,
        autoHeight: false,
        html: '',
        cls: 'bordered-left'
    };

    var fontSelectionPanel = {
        id: 'fontList',
        xtype: 'panel',
        layout: 'form',
        title: '{#str_TitleFontSection#}',
        autoHeight: false,
        height: 'auto',
        frame: true,
        collapsable: false,
        hideLabel: true,
        style: {
            backgroundColor: 'white'
        },
        cls: 'white-panel',
        items: [
            {
                layout: 'column',
                items: [
                    {
                        items: [ tree ],
                        columnWidth: .5
                    },
                    {
                        items: [ treeData ],
                        columnWidth: .5
                    }
                ]
            }
        ]
    };

    var mainPanel = {
        xtype: 'tabpanel',
        id: 'addEditPanel',
        deferredRender: false,
        enableTabScroll: true,
        activeTab: 0,
        height: 600,
        shadow: true,
        plain: true,
        bodyBorder: false,
        defaults: {
            frame: true,
            autoScroll: true,
            hideMode: 'offsets',
            labelWidth: 200,
            bodyStyle: 'padding: 5px 10px 0px; border-top: 0px;'
        },
        items: [
            fontSelectionPanel,
            productSelectionPanel
        ]
    };

    var dialogFormPanelObj = new Ext.FormPanel({
		id: 'fontListForm',
        labelAlign: 'left',
        labelWidth:120,
        autoHeight: false,
        height: 600,
        frame:true,
        layout:'form',
        cls: 'left-right-buttons',
        bodyStyle:'padding-left:5px;',
        items: [
            {
                xtype: 'textfield',
                id: 'name',
                name: 'name',
                maxLength: 255,
                width:400,
                value: '{$name}',
                fieldLabel: "{#str_LabelName#}",
                post: true,
                listeners: {
                    blur: function(item) {
                        // Trim any white space from the value.
                        var value = item.getValue();
                        item.setValue(value.trim());
                    }
                }
            },
            {
                xtype: 'hidden',
                id: 'id',
                name: 'id',
                value: "{$id}",
                post: true
            },
            mainPanel
        ]
    });

    var gDialogObj = new Ext.Window({
		id: 'dialog',
	  	closable:false,
	  	plain:true,
	  	modal:true,
	  	draggable:true,
	  	resizable:false,
	  	layout: 'fit',
	  	autoHeight:false,
	  	autoHeight: true,
	  	width: 810,
	  	items: dialogFormPanelObj,
	  	listeners:
	  	{
			close:
			{
				fn: function()
				{
                    var psw = Ext.getCmp('productSelectionWindow');
                    // If the product selection window exists remove it.
                    if (undefined !== psw)
                    {
                        psw.close();
                    }
    				editWindow = false;
				}
			}
		},
	  	title: "{$title}",
	  	buttons:
		[
			{
				text: "{#str_ButtonCancel#}",
				handler: function() {
				    Ext.getCmp('dialog').close();
				},
				cls: 'x-btn-right'
			},
			{
				id: 'addEditButton',
				cls: 'x-btn-right',
				handler: saveHandler,
                text: "{if $id == 0}{#str_ButtonAdd#}{else}{#str_ButtonUpdate#}{/if}"
			}
		]
	});
	gDialogObj.show();
    updateFontsSelected();
}