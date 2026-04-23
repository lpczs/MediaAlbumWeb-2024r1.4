{literal}
var collectionGridIndexCache = new Map();
var layoutRuleCache = [];

function findLayoutRecordInPreviewGrid(pPreviewGrid, pLayoutCode, pStartPosition)
{
    return pPreviewGrid.store.findBy(
        function(record, id)
        {
            if ((record.data.iscollection == 0) && (record.data.code == pLayoutCode))
            {
                return true;
            }
            else
            {
                return false;
            }
        }, pPreviewGrid.store, pStartPosition);
}

var onDeleteRecord = function(pRecordID)
{
    var previewGrid = Ext.getCmp('previewgrid');
    var collectionGrid = Ext.getCmp('collectiongrid');
    var layoutGrid = Ext.getCmp('layoutgrid');
    var layoutGridRecord = layoutGrid.store.getById(pRecordID);
    var currentPreviewRecordIndex = -1;
    var layoutCode = layoutGridRecord.data.code;
    var previewGridStore = previewGrid.store;
    var currentPreviewRecordParent;
    var currentPreviewRecordParentIndex = 0;
    var currentPreviewRecordParentCode = '';
    var currentPreviewRecord;
    var currentPreviewLayoutCode
    var recordsToDeleteArray = [];
    var parentRecordArray = [];
    var collectionRecordIndex = 0;
    var currentCollectionsRecord;
    var deleteRecord = false;
    var parentRecordArrayLength = 0;

    layoutGridRecord.store.remove(layoutGridRecord);
    layoutRuleCache = layoutRuleCache.filter(function(element){return element !== layoutCode});

    {/literal}{*we have to scan the collectiongrid to make sure that we do not remove any collections or layouts that are still selected in the grid*}{literal}
    do
    {
        currentPreviewRecordIndex = findLayoutRecordInPreviewGrid(previewGrid, layoutCode, currentPreviewRecordIndex + 1);

        if (currentPreviewRecordIndex > -1)
        {
            deleteRecord = false;
            currentPreviewRecord = previewGrid.store.getAt(currentPreviewRecordIndex);
            currentPreviewLayoutCode = currentPreviewRecord.data.code;
            currentPreviewRecordParentIndex = previewGrid.store.indexOfId(currentPreviewRecord.data.parentid);
            currentPreviewRecordParent = previewGrid.store.getAt(currentPreviewRecordParentIndex);
            currentPreviewRecordParentCode = currentPreviewRecordParent.data.code;

            collectionRecordIndex = collectionGridIndexCache.get(currentPreviewRecordParentCode + '.' + currentPreviewLayoutCode);

            if (collectionRecordIndex == undefined)
            {
                {/literal}{*if the cache is undefined, we can be sure that the record is not selected as records are only cached if
                    they have been selected*}{literal}
                    deleteRecord = true;
            }
            else
            {
                {/literal}{*we only check against the layout record as a set collection record will have an all layouts record
                    instead of a record with the specific layout code*}{literal}
                currentCollectionsRecord = collectionGrid.store.getAt(collectionRecordIndex);
                
                {/literal}{*invert the result of the is selected as we do not want to delete records that are selected*}{literal}
                deleteRecord = ! collectionGrid.getSelectionModel().isSelected(currentCollectionsRecord);
            }

            if (deleteRecord === true)
            {
                recordsToDeleteArray.push(currentPreviewRecord);
                parentRecordArray.push(currentPreviewRecordParent)
            }
        }

    } while (currentPreviewRecordIndex > -1);

    if (recordsToDeleteArray.length > 0)
    {
        previewGrid.store.remove(recordsToDeleteArray);
        recordsToDeleteArray = [];

        {/literal}{*test if we need to delete the collection preview record*}{literal}
        parentRecordArrayLength = parentRecordArray.length;
        for (var i = 0; i < parentRecordArrayLength; i++)
        {
            currentPreviewRecordParent = parentRecordArray[i];
            if (findChildRecord(currentPreviewRecordParent.id, previewGrid, 0) == -1)
            {
                recordsToDeleteArray.push(currentPreviewRecordParent);
            }
        }
        
        if (recordsToDeleteArray.length > 0)
        {
            previewGrid.store.remove(recordsToDeleteArray);
        }

        previewGrid.getView().refresh();
    }
};

function findChildRecord(pParentRecordID, pGrid, pStartPosition)
{

    return childRecordID = pGrid.store.findBy(
        function(record, id)
        {
            if ((record.data.iscollection == 0) && (record.data.parentid == pParentRecordID))
            {
                return true;
            }
            else
            {
                return false;
            }
        }, pGrid.Store, pStartPosition); 
}

function initialize(pParams)
{
    var gridObj = Ext.getCmp('maingrid');
	var selRecords = gridObj.selModel.getSelections();
    var productGroupCode = '';
    var cumulativeRecordID = 0;
    var cumulativeLayoutRecordID = 0;
    var groupID = 0;
    var dontRemoveOnNextUncheck = false;
    var isedit = {/literal}{$isedit}{literal}
    var isduplicate = {/literal}{$isduplicate}{literal}
    var deferPreviewRender = true;

    if (isedit == 1)
    {
        productGroupCode = selRecords[0].data.name;
        groupID = selRecords[0].data.id;
    }

    if (isduplicate == 1)
    {
        groupID = selRecords[0].data.id;
    }

    var previewRecordTemplate = Ext.data.Record.create([
            {name: 'id', mapping: 0},
            {name: 'code', mapping: 1},
            {name: 'name', mapping: 2},
            {name: 'iscollection', mapping: 3},
            {name: 'parentid', mapping: 4}
    ]);

    var layoutCodeRecordTemplate = Ext.data.Record.create([
            {name: 'id', mapping: 0},
            {name: 'code', mapping: 1},
            {name: 'summary', mapping: 2}
    ]);

    function forceAlphaNumericLayoutCode()
	{
		var code = Ext.getCmp('layoutCodeEntry').getValue();
    	code = code.replace(/[^A-Z_0-9a-z -.\-]+/g, "");
        code = code.toUpperCase();
    	Ext.getCmp('layoutCodeEntry').setValue(code);
	}

    function forceAlphaNumericCollectionSearch()
    { 
        var code = Ext.getCmp('collectionSearchField').getValue();
    	code = code.replace(/[^A-Z_0-9a-z -.\-]+/g, "");
        code = code.toUpperCase();
    	Ext.getCmp('collectionSearchField').setValue(code);
    }

    function removeLayoutSelection(sm, pRecord)
    {
        sm.each(function(pSelectionRecord, rowNumber)
            {
                if (pRecord.data.id == pSelectionRecord.data.parentid)
                {
                    sm.deselectRow(pSelectionRecord.store.indexOf(pSelectionRecord));
                }
        });
    }

    function unselectCollectionRow(sm, pRecord)
    {
        {/literal}{*the next uncheck event will be on unchecking the checkbox, we do not want to process this removal as it will uncheck everything*}{literal}
        dontRemoveOnNextUncheck = true;
        sm.deselectRow(pRecord.store.indexOfId(pRecord.data.parentid));
    }

    function removeChildLayoutRecordsAndCheckParentCanBeRemoved(pParentRecordID, pPreviewGrid)
    {
        var parentCanBeDeleted = true;
        var childRecordIndex;
        var nextStartPosition;
        do
        {
            childRecordIndex = findChildRecord(pParentRecordID, pPreviewGrid, nextStartPosition)
            if (childRecordIndex != -1)
            {
                if (checkLayoutCodeAdded(pPreviewGrid.store.getAt(childRecordIndex).data.code))
                {
                    {/literal}{*we have a layout *.layout rule for this code, we cannot remove it or the parent record*}{literal}
                    parentCanBeDeleted = false;
                    nextStartPosition = childRecordIndex;
                }
                else
                {
                    nextStartPosition = childRecordIndex;
                }

                pPreviewGrid.store.removeAt(childRecordIndex);
            }
        }
        while (childRecordIndex != -1);

        return parentCanBeDeleted;
    }

    function findCollectionRecordInPreviewGrid(pPreviewGrid, pCollectionCode)
    {
        return pPreviewGrid.store.findBy(
            function(record, id)
            {
                if ((record.data.iscollection == 1) && (record.data.code == pCollectionCode))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            });
    }

    function findPreexistingLayoutRecord(pPreviewGrid, pLayoutCode, pParentRecordID)
    {
            return pPreviewGrid.store.findBy(
            function(record, id)
            {
                {/literal}{*we check for "all layouts" as we do not want to display the layout if all layouts already applies to the collection*}{literal}
                if ((record.data.iscollection == 0) && (record.data.parentid == pParentRecordID) && ((record.data.code == pLayoutCode) || (record.data.code == "{/literal}{#str_LabelAllLayouts#}{literal}")))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            });
    }

    function checkLayoutCodeAdded(pLayoutCode)
    {
        var layoutGrid = Ext.getCmp('layoutgrid');
        var layoutRecordPosition = layoutGrid.store.findExact('code', pLayoutCode);
        return (layoutRecordPosition > -1);
    }

    function addToPreviewFromCodes(pCollectionCode, pCollectionName, pLayoutCode, pLayoutName)
    {
        var previewGrid = Ext.getCmp('previewgrid');
        var collectionRecordID = -1;
        var collectionRecordIndex = -1;
        var preexistingLayoutRecordID = -1;
        var insertedCollectionRecordID = -1;

        collectionRecordIndex = findCollectionRecordInPreviewGrid(previewGrid, pCollectionCode);

        if (collectionRecordIndex !== -1)
        {
            collectionRecordID = previewGrid.store.getAt(collectionRecordIndex).data.id;

            {/literal}{*check if we already have a record for this layout from a different rule, if we do we don't have to do anything*}{literal}
            preexistingLayoutRecordID = findPreexistingLayoutRecord(previewGrid, pLayoutCode, collectionRecordID);

            if (preexistingLayoutRecordID == -1)
            {
                cumulativeRecordID++;
                newPreviewLayoutRecord = new previewRecordTemplate({'id':cumulativeRecordID, 'code':pLayoutCode, 'name':pLayoutName, 'iscollection':0, 'parentid':collectionRecordID}, cumulativeRecordID);
                previewGrid.store.insert(collectionRecordIndex + 1, newPreviewLayoutRecord);
            }
        }
        else
        {
            {/literal}{*if we don't have a collection record we will not have a layout record either so we can just add the records without any more checks*}{literal}
            cumulativeRecordID++;
            newPreviewCollectionRecord = new previewRecordTemplate({'id': cumulativeRecordID, 'code':pCollectionCode, 'name':pCollectionName, 'iscollection':1, 'parentid':-1}, cumulativeRecordID);
            previewGrid.store.add(newPreviewCollectionRecord);
            insertedCollectionRecordID = cumulativeRecordID;

            cumulativeRecordID++;
            newPreviewLayoutRecord = new previewRecordTemplate({'id':cumulativeRecordID, 'code':pLayoutCode, 'name':pLayoutName, 'iscollection':0, 'parentid':insertedCollectionRecordID}, cumulativeRecordID);
            previewGrid.store.add(newPreviewLayoutRecord);
        }
    }

    function findAllLayoutsRecord(pPreviewGrid, pParentRecordID)
    {
        return pPreviewGrid.store.findBy(
            function(record, id)
            {
                if ((record.data.iscollection == 0) && (record.data.parentid == pParentRecordID) && (record.data.code == "{/literal}{#str_LabelAllLayouts#}{literal}"))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            });
    }

    function addRecordToPreview(pRecord)
    {
        var previewGrid = Ext.getCmp('previewgrid');
        var collectionRecordID = -1;
        var parentCollectionRecord;
        var collectionCode;
        var collectionName;
        var insertedCollectionRecordID;
        var newPreviewLayoutRecord;
        var newPreviewCollectionRecord;
        var allLayoutsRecordID;
        var previewGridParentRecordID = -1;
        var previewGridParentRecordIndex = -1;
        var paramArray = [];

        if (pRecord.data.iscollection == false)
        {
            parentRecord = pRecord.store.getById(pRecord.data.parentid);
            collectionCode = parentRecord.data.code;
            collectionName = parentRecord.data.name;
        }
        else
        {
            collectionCode = pRecord.data.code;
            collectionName = pRecord.data.name;
        }

        previewGridParentRecordIndex = findCollectionRecordInPreviewGrid(previewGrid, collectionCode);

        if (previewGridParentRecordIndex !== -1)
        {
            previewGridParentRecordID = previewGrid.store.getAt(previewGridParentRecordIndex).data.id;
        }

        if (pRecord.data.iscollection == false)
        {
            {/literal}{*insert the collection grid cache record*}{literal}
            collectionGridIndexCache.set(collectionCode + '.' + pRecord.data.code, pRecord.store.indexOf(pRecord));


            if (previewGridParentRecordID === -1)
            {
                cumulativeRecordID++;
                newPreviewCollectionRecord = new previewRecordTemplate({'id': cumulativeRecordID, 'code':collectionCode, 'name':collectionName, 'iscollection':1, 'parentid':-1}, cumulativeRecordID);
                previewGrid.store.add(newPreviewCollectionRecord);
                insertedCollectionRecordID = cumulativeRecordID;

                cumulativeRecordID++;
                newPreviewLayoutRecord = new previewRecordTemplate({'id':cumulativeRecordID, 'code':pRecord.data.code, 'name':pRecord.data.name, 'iscollection':0, 'parentid':insertedCollectionRecordID}, cumulativeRecordID);
                previewGrid.store.add(newPreviewLayoutRecord);
            }
            else
            {
                {/literal}{*check if we have an all layouts record which we will need to delete*}{literal}
                allLayoutsRecordID = findAllLayoutsRecord(previewGrid, previewGridParentRecordID);

                if (allLayoutsRecordID !== -1)
                {
                    previewGrid.store.removeAt(allLayoutsRecordID);
                }

                if ((allLayoutsRecordID === -1) && (checkLayoutCodeAdded(pRecord.data.code)))
                {
                    {/literal}{*we need to check if we have any layout codes to reinsert*}{literal}
                    paramArray['layoutcodes'] = layoutRuleCache.join(',');
                    paramArray['collectioncode'] = collectionCode;
                    Ext.taopix.formPost(gDialogObj, paramArray, 'index.php?fsaction=AdminProductGroups.getMultipleLayoutPreviewData&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageUpdating#}{literal}", onLayoutGridLoadCallback);
                }
                else if (allLayoutsRecordID !== -1)
                {
                    {/literal}{*we still need to check if we have any layout records to insert as their individual entries will have been surpressed by the all layouts record*}{literal}
                    cumulativeRecordID++;
                    newPreviewLayoutRecord = new previewRecordTemplate({'id':cumulativeRecordID, 'code':pRecord.data.code, 'name':pRecord.data.name, 'iscollection':0, 'parentid':previewGridParentRecordID}, cumulativeRecordID);
                    previewGrid.store.insert(previewGridParentRecordIndex + 1, [newPreviewLayoutRecord]);

                    paramArray['layoutcodes'] = layoutRuleCache.join(',');
                    paramArray['collectioncode'] = collectionCode;
                    Ext.taopix.formPost(gDialogObj, paramArray, 'index.php?fsaction=AdminProductGroups.getMultipleLayoutPreviewData&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageUpdating#}{literal}", onLayoutGridLoadCallback);
                }
                else
                {
                    {/literal}{*we only need to add this specific record, no need to check for layout rules as they will not have been surpressed by an all layouts record*}{literal}
                    cumulativeRecordID++;
                    newPreviewLayoutRecord = new previewRecordTemplate({'id':cumulativeRecordID, 'code':pRecord.data.code, 'name':pRecord.data.name, 'iscollection':0, 'parentid':previewGridParentRecordID}, cumulativeRecordID);
                    previewGrid.store.insert(previewGridParentRecordIndex + 1, [newPreviewLayoutRecord]);
                }
            }
        }
        else
        {
            {/literal}{*insert the collection grid cache record*}{literal}
            collectionGridIndexCache.set(collectionCode + '.*', pRecord.store.indexOf(pRecord));

            {/literal}{*check if we have a preview record, if we do then a layout has been selected and we will need to remove the layouts*}{literal}
            if (previewGridParentRecordID == -1)
            {
                cumulativeRecordID++;
                newPreviewCollectionRecord = new previewRecordTemplate({'id': cumulativeRecordID, 'code':collectionCode, 'name':collectionName, 'iscollection':1, 'parentid':-1}, cumulativeRecordID);
                previewGrid.store.add([newPreviewCollectionRecord]);
                insertedCollectionRecordID = cumulativeRecordID;

                cumulativeRecordID++
                newPreviewLayoutRecord = new previewRecordTemplate({'id':cumulativeRecordID, 'code':"{/literal}{#str_LabelAllLayouts#}{literal}", 'name':'', 'iscollection':0, 'parentid':insertedCollectionRecordID}, cumulativeRecordID);
                previewGrid.store.add([newPreviewLayoutRecord]); 
            }
            else
            {
                removeChildLayoutRecordsAndCheckParentCanBeRemoved(previewGridParentRecordID, previewGrid, true);
                
                cumulativeRecordID++;
                newPreviewLayoutRecord = new previewRecordTemplate({'id':cumulativeRecordID, 'code':"{/literal}{#str_LabelAllLayouts#}{literal}", 'name':'', 'iscollection':0, 'parentid':previewGridParentRecordID}, cumulativeRecordID);
                previewGrid.store.insert(previewGridParentRecordIndex + 1, [newPreviewLayoutRecord]);
            }
        }
    }

    function removeFromPreview(pRecord)
    {
        var previewGrid = Ext.getCmp('previewgrid');
        var layoutGrid = Ext.getCmp('layoutgrid');
        var recordToRemoveIndex;
        var parentRecordIndex;
        var layoutRuleArray = [];
        var parentRecordID;
        var canRemoveCollectionRecord = true;
        var allLayoutsRecordIndex;
        var paramArray = [];

        if (pRecord.data.iscollection == false)
        {
            if (checkLayoutCodeAdded(pRecord.data.code) == false)
            {
                recordToRemoveIndex = findLayoutRecordInPreviewGrid(previewGrid, pRecord.data.code, 0);

                {/literal}{*sanity check that we haven't already deleted the record*}{literal}
                if (recordToRemoveIndex !== -1)
                {
                    parentRecordID = previewGrid.store.getAt(recordToRemoveIndex).data.parentid;
                    parentRecordIndex = previewGrid.store.indexOfId(parentRecordID);
                    previewGrid.store.removeAt(recordToRemoveIndex);

                    {/literal}{*check if there any remaining records for this collection and delete the collection record if not*}{literal}
                    if (findChildRecord(parentRecordID, previewGrid, 0) == -1)
                    {
                        previewGrid.store.removeAt(parentRecordIndex);
                    }
                }
            }

            previewGrid.getView().refresh();
        }
        else
        {
            recordToRemoveIndex = previewGrid.store.findBy(
            function(record, id)
            {
                if ((record.data.iscollection == 1) && (record.data.code == pRecord.data.code))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            });

            {/literal}{*we will always have at least one child record to remove as the all layouts selected message is a child record*}{literal}
            allLayoutsRecordIndex = findAllLayoutsRecord(previewGrid, previewGrid.store.getAt(recordToRemoveIndex).data.id);
            canRemoveCollectionRecord = removeChildLayoutRecordsAndCheckParentCanBeRemoved(previewGrid.store.getAt(recordToRemoveIndex).data.id, previewGrid, false);

            if (canRemoveCollectionRecord)
            {
                previewGrid.store.removeAt(recordToRemoveIndex);
            }
            
            layoutGrid.store.each(function(record)
            {
                layoutRuleArray.push(record.data.code);
            });
            
            paramArray['layoutcodes'] = layoutRuleArray.join(',');
            Ext.taopix.formPost(gDialogObj, paramArray, 'index.php?fsaction=AdminProductGroups.getMultipleLayoutPreviewData&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageUpdating#}{literal}", onLayoutGridLoadCallback);
        }
    }

    // Configure listeners to enable/disable buttons in the grid based on how many items are selected.
	var gridCheckBoxSelectionModelObj = new Ext.grid.CheckboxSelectionModel({
        checkOnly: true,
        listeners: {'rowselect': 
            function(sm, rowindex, rowrecord)
                {
                    addRecordToPreview(rowrecord);

                    if (rowrecord.data.iscollection == true)
                    {
                        removeLayoutSelection(sm, rowrecord);
                    }
                    else
                    {
                        unselectCollectionRow(sm, rowrecord)
                    }

                    if (deferPreviewRender == false)
                    {
                        Ext.getCmp("previewgrid").getView().refresh();
                    }
                }
            ,'rowdeselect': 
                function(sm, rowindex, rowrecord)
                    {
                        if (dontRemoveOnNextUncheck)
                        {
                            {/literal}{*set the ignore back to off so we remove on any further deliberate actions*}{literal}
                            dontRemoveOnNextUncheck = false;
                        }
                        else
                        {
                            removeFromPreview(rowrecord);
                        }
                        
                    }
        }
    });

    /* save functions */
	function saveCallback(pUpdated, pActionForm, pActionData)
	{
		if (pUpdated)
		{
			var gridObj = gMainWindowObj.findById('maingrid');
			var dataStore = gridObj.store;
			gridObj.store.reload();
			Ext.getCmp('dialog').close();
		}
	}

    /* save functions */
	function saveHandler(pSubmitUrl)
	{
        var collectionGridStore = Ext.getCmp('collectiongrid').store;
        var collectionGridSelectionModel = Ext.getCmp("collectiongrid").getSelectionModel();
		var fp = Ext.getCmp('productGroupForm');
		var form = fp.getForm();

        var groupName = Ext.getCmp('groupname').getValue();
        groupName = groupName.trim();
        groupName = groupName.replace(/['"]+/g, "");
        {/literal}{*set the trimmed value back into the name field in case it is purely whitespace*}{literal}
        Ext.getCmp('groupname').setValue(groupName);

        if (form.isValid())
        {
            var canSubmit = false;
            var paramArray = new Object();
            var collectionRuleArray = [];  

            for (const [rule, recordIndex] of collectionGridIndexCache)
            {
                if (collectionGridSelectionModel.isSelected(recordIndex))
                {
                    collectionRuleArray.push(rule);
                }
            }

            if ((collectionRuleArray.length > 0) || (layoutRuleCache.length > 0))
            {
                paramArray['collectionrules'] = collectionRuleArray.join(',');
                paramArray['layoutrules'] = layoutRuleCache.join(',');

                if (groupName != '')
                {
                    canSubmit = true;
                    paramArray['groupname'] = groupName;
                }
            }
            else
            {
                Ext.MessageBox.show(errorDialogConfig);
            }

            // No problems found, all the form to be submitted.
            if (canSubmit)
            {
                Ext.taopix.formPanelPost(fp, form, paramArray, pSubmitUrl, "{/literal}{#str_MessageSaving#}{literal}", saveCallback);			
            }
        }
    }

    function addsaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminProductGroups.add&ref={/literal}{$ref}{literal}';

		// Trigger the save handler with the correct endpoint.
		saveHandler(submitURL);
	}

    function editSaveHandler(btn, ev)
	{
		var submitURL = 'index.php?fsaction=AdminProductGroups.edit&ref={/literal}{$ref}{literal}&id=' + groupID;

		// Trigger the save handler with the correct endpoint.
		saveHandler(submitURL);
	}

    

    var errorDialogConfig =
	{
		title: "{/literal}{#str_TitleError#}{literal}",
		msg: "{/literal}{#str_ExtJsCheckboxGroupBlank#}{literal}",
		buttons:
		{
			ok: "{/literal}{#str_ButtonOk#}{literal}"
		},
		icon: Ext.MessageBox.WARNING
	};

    var topPanel = new Ext.Panel({
		id: 'topPanel',
		layout: 'form',
		style: 'background: #c9d8ed; padding: 5px 5px 3px 5px; border:1px solid #8ca9cf; margin: 0px',
		plain: true,
		bodyBorder: false,
		border: false,
		labelWidth: 80,
		items:
		[
            {
				xtype: 'textfield',
				id: 'groupname',
				name: 'groupname',
				allowBlank:false,
				maxLength: 100,
				width: 600,
				fieldLabel: "{/literal}{#str_LabelGroupName#}{literal}",
				post: true
                {/literal}{if $isedit == 1}
                ,value: productGroupCode
                {/if}
                {literal}
			}
		]
	});


    var collectionGridDataStoreObj = new Ext.data.Store({
		remoteSort: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminProductGroups.getCollectionGridData&ref={/literal}{$ref}{literal}&groupid=' + groupID }),
		reader: new Ext.taopix.PagedArrayReader({
				idIndex: 0
			},
			Ext.data.Record.create([
                {name: 'id', mapping: 0},
				{name: 'selected', mapping: 1},
				{name: 'code', mapping: 2},
                {name: 'name', mapping: 3},
                {name: 'iscollection', mapping: 4},
                {name: 'parentid', mapping: 5},
                {name: 'hidden', mapping: 6, defaultValue: false}
			])
		),
        listeners: {
			'load': {
				fn: function(store, records, options)
                {
                    var selectedRowIndices = [];
                    store.each(function(record)
                    {
                        if (record.data.selected == 1)
                        {
                            if (record.data.iscollection == 1)
                            {
                                collectionGridIndexCache.set(record.data.code + '.*', store.indexOf(record));
                            }
                            else
                            {
                                collectionGridIndexCache.set(store.getById(record.data.parentid).data.code + '.' + record.data.code, store.indexOf(record.data.id));
                            }

                            selectedRowIndices.push(record.store.indexOf(record));
                        }
                    });

                    Ext.getCmp("collectiongrid").getSelectionModel().selectRows(selectedRowIndices);

                    deferPreviewRender = false;
				}
			}}
    });


    collectionGridDataStoreObj.load();


    function columnRenderer(value, p, record, rowIndex, colIndex, store)
	{
		return '<span>' + value + '</span>';
	};

    var collectionGridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
			sortable: false,
			resizable: true
		},
		columns: [
			gridCheckBoxSelectionModelObj,
			{header: "{/literal}{#str_LabelCode#}{literal}", width: 200, dataIndex: 'code', renderer: columnRenderer},
			{header: "{/literal}{#str_LabelName#}{literal}", dataIndex: 'name', renderer: columnRenderer}
		]
	});

    var previewGridView = new Ext.grid.GridView({
        getRowClass: function(record, rowIndex, rowParams, dataStore)
        {
            var cssClass = '';

            if (record.data.iscollection == false)
            {
                cssClass =  'tpx-components-subgrid';

                if (record.data.code == "{/literal}{#str_LabelAllLayouts#}{literal}")
                {
                    cssClass = cssClass + " tpx-components-alllayoutstext";
                }

                return cssClass;
            }
        }
    });

    var collectionGridView = new Ext.grid.GridView({
        getRowClass: function(record, rowIndex, rowParams, dataStore)
        {
            if (record.data.hidden == true)
            {
                return 'tpx-components-collapsed';
            }
            else if (record.data.iscollection == false)
            {
                return 'tpx-components-subgrid';
            }
        }
    });

    var collectionGrid = new Ext.grid.GridPanel({
		autoExpandMax: 5000, // Set the max width an auto expand column can be over the 1000px default.
		autoExpandColumn: 2,
		id: 'collectiongrid',
		store: collectionGridDataStoreObj,
		cm: collectionGridColumnModelObj,
        view: collectionGridView,
		enableColLock: false,
		draggable: false,
        height:360,
		enableColumnHide: false,
		enableColumnMove: false,
		enableHdMenu: false,
		trackMouseOver: false,
		stripeRows: true,
		columnLines: true,
        selModel: gridCheckBoxSelectionModelObj,
		ctCls: 'grid',
		anchor: '100% 100%',
        listeners: {
			'viewready': {
				fn: function(theGrid)
                {
                    {/literal}{*there is a chrome bug where the checked css class is not always applied to the checked collection grid items thus we need to refresh the gridview here
                        this is the earliest event where refreshing applies the missing CSS*}{literal}
                    theGrid.getView().refresh();	
                }
            }
        }
	});
    

    var deleteColRenderer = function(pRecord)
	{
        var delPic = "{/literal}{$webroot}{literal}/utils/ext/images/silk/delete.png";
		var button = '<div style="overflow: hidden;"><div style="float: right; margin-right: 3px" onClick="onDeleteRecord(\'' + pRecord.data.id + '\'); return false;" OnMouseOver="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\') + \' x-btn-over\';" OnMouseOut="var el = this.getElementsByTagName(\'table\')[0]; el.className = el.className.replace(\' x-btn-over\',\'\');"><table cellspacing="0" class="x-btn  x-btn-icon"><tbody class="x-btn-small x-btn-icon-small-left"><tr><td class="x-btn-tl"><i>&nbsp;</i></td><td class="x-btn-tc"></td><td class="x-btn-tr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-ml"><i>&nbsp;</i></td><td class="x-btn-mc"><em unselectable="on" class=""><button type="button" class="x-btn-text " style="background-image: url(&quot;'+delPic+'&quot;);">&nbsp;</button></em></td><td class="x-btn-mr"><i>&nbsp;</i></td></tr><tr><td class="x-btn-bl"><i>&nbsp;</i></td><td class="x-btn-bc"></td><td class="x-btn-br"><i>&nbsp;</i></td></tr></tbody></table></div></div>';
		return button;
	};

    var layoutGridDataStoreObj = new Ext.data.Store({
		remoteSort: true,
		proxy: new Ext.data.HttpProxy({url: 'index.php?fsaction=AdminProductGroups.getLayoutGridData&ref={/literal}{$ref}{literal}&groupid=' + groupID}),
		reader: new Ext.taopix.PagedArrayReader({
				idIndex: 0
			},
			layoutCodeRecordTemplate
		),
        listeners: {
			'load': {
				fn: function(store, records, options){
					cumulativeLayoutRecordID = store.getCount();
                    
                    store.each(function(record)
                        {
                            var paramArray = [];
                            paramArray['layoutcode'] = record.data.code;
                            layoutRuleCache.push(record.data.code);

                            Ext.taopix.formPost(gDialogObj, paramArray, 'index.php?fsaction=AdminProductGroups.getLayoutPreviewData&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageUpdating#}{literal}", onLayoutGridLoadCallback);
                        });
				}
			}}
    });

    layoutGridDataStoreObj.load();

    var layoutGridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
			sortable: false,
			resizable: true
		},
		columns: [
			{header: "{/literal}{#str_LabelLayoutCode#}{literal}", width: 200, dataIndex: 'code', renderer: columnRenderer},
			{header: "{/literal}{#str_LabelResultsSummary#}{literal}", dataIndex: 'summary', renderer: columnRenderer},
            {header: "", width: 13, sortable: false, dataIndex: 'controlsRow', menuDisabled: true, renderer: function(value, p, record, rowIndex, colIndex, store){ return deleteColRenderer(record); }}
		]
	});

    var layoutGrid = new Ext.grid.GridPanel({
		autoExpandMax: 5000, // Set the max width an auto expand column can be over the 1000px default.
		autoExpandColumn: 2,
		id: 'layoutgrid',
        height:320,
		store: layoutGridDataStoreObj,
		cm: layoutGridColumnModelObj,
        style:'border:1px solid #99BBE8;',
		enableColLock: false,
		draggable: false,
		enableColumnHide: false,
		enableColumnMove: false,
		enableHdMenu: false,
		trackMouseOver: false,
		stripeRows: true,
		columnLines: true,
		ctCls: 'grid',
		anchor: '100% 100%',
	});

    onLayoutAddCallback = function(pUpdated, pTheForm, pActionData)
	{
		if (pUpdated)
		{
			if (pActionData.result.layoutsfound == false)
			{
				Ext.MessageBox.show({ title: pActionData.result.title,	msg: "{/literal}{#str_ErrorEnteredCodeMatchesNoLayouts#}{literal}", buttons: Ext.MessageBox.OK, icon: Ext.MessageBox.INFO	});
			}
            else
            {
                let layouts =  pActionData.result.previews;
                let layoutCount = layouts.length;
                let summaryString = '';

                if (layoutCount == 1)
                {
                    summaryString = "{/literal}{#str_LabelMatchesOneLayout#}{literal}";
                }
                else
                {
                    summaryString = "{/literal}{#str_LabelMatchesLayouts#}{literal}";
                    summaryString = summaryString.replace("^0", layoutCount);
                }

                cumulativeLayoutRecordID++;
                let layoutCodeRecord = new layoutCodeRecordTemplate({'id':cumulativeLayoutRecordID, 'code':pActionData.result.layoutcode, 'summary':summaryString}, cumulativeLayoutRecordID); 
                Ext.getCmp("layoutgrid").store.add(layoutCodeRecord);
                layoutRuleCache.push(pActionData.result.layoutcode);

                for (let i = 0; i < layoutCount; i++)
                {
                    let theLayout = layouts[i];
                    addToPreviewFromCodes(theLayout.collectioncode, theLayout.collectionname, theLayout.productcode, theLayout.productname);
                }

                Ext.getCmp("layoutCodeEntry").setValue("");
                Ext.getCmp("layoutgrid").getView().refresh();
            }
		}
	};

    onLayoutGridLoadCallback = function(pUpdated, pTheForm, pActionData)
    {
        var previewGrid = Ext.getCmp("previewgrid");
        if (pUpdated)
		{
			if (pActionData.result.layoutsfound == true)
			{
                let layouts =  pActionData.result.previews;
                let layoutCount = layouts.length;

                for (let i = 0; i < layoutCount; i++)
                {
                    let theLayout = layouts[i];
                    addToPreviewFromCodes(theLayout.collectioncode, theLayout.collectionname, theLayout.productcode, theLayout.productname);
                }

                previewGrid.getView().refresh();
            }
		}
	};

    var onLayoutAdd = function(button, clickEvent)
    {
        var paramArray = [];
        var layoutCode = Ext.getCmp("layoutCodeEntry").getValue();
        layoutCode = layoutCode.trim();
        Ext.getCmp("layoutCodeEntry").setValue(layoutCode);

        {/literal}{*Check we haven't already added the code, if we have we want to ignore it*}{literal}
        if (checkLayoutCodeAdded(layoutCode) == false)
        {
            paramArray['layoutcode'] = layoutCode;
            Ext.taopix.formPost(gDialogObj, paramArray, 'index.php?fsaction=AdminProductGroups.getLayoutPreviewData&ref={/literal}{$ref}{literal}', "{/literal}{#str_MessageUpdating#}{literal}", onLayoutAddCallback);
        }
        else
        {
            {/literal}{*we want to clear the input field here since it has already been added*}{literal}
            Ext.getCmp("layoutCodeEntry").setValue("");
        }
    };

    function onCollectionSearch(button, clickEvent)
    {
        var currentParentRecord;
        var collectionGrid = Ext.getCmp('collectiongrid');
        var collectionGridStore = collectionGrid.store;
        var searchParam = Ext.getCmp("collectionSearchField").getValue();
        var searchRegex = new RegExp('\\.*' + searchParam + '.*', 'i');

        collectionGridStore.each(function(record)
            {
                if (record.data.iscollection == 1)
                {
                    currentParentRecord = record;

                    if (searchRegex.test(record.data.code))
                    {
                        record.data.hidden = false;
                    }
                    else
                    {
                        record.data.hidden = true;
                    }
                }
                else
                {
                    if (searchRegex.test(record.data.code))
                    {
                        record.data.hidden = false;
                        currentParentRecord.data.hidden = false;
                    }
                    else
                    {
                        record.data.hidden = true;
                    }
                }
            });

        collectionGrid.getView().refresh();
    }

    function panelItems(pPanel)
	{
        var items = [];

        if (pPanel === 'layouts')
        {

            var layoutCodeDescription = {
                html: "{/literal}{#str_MessageIncludeLayoutsDescription#}{literal}",
                style: 'padding: 10px 0px 20px; text-align: left;'
            };

            var layoutEntryFieldSet = {
                xtype: 'fieldset',
                id: 'layoutentryfieldset',
                collapsible: false,
                style: 'position: relative;',
                autoHeight: true,
                layout: 'hbox',
                border: false,
                items:
                ([
                    { xtype: 'label', text: "{/literal}{#str_LabelLayoutCode#}{literal}", style: 'padding: 3px'},
                    { xtype: 'spacer', width: 25 },
                    { xtype: 'textfield', id: 'layoutCodeEntry', name: 'layoutCodeEntry', width: 250, maxLength: 50, listeners:{	blur:{	fn: forceAlphaNumericLayoutCode }	}, post: true},
                    { xtype: 'spacer', width: 5 },
                    { xtype: 'button', id:'addLayoutCodeButton', text: '{/literal}{#str_LabelAdd#}{literal}', ctCls:'x-toolbar-standardbutton', handler: onLayoutAdd}
                ])
            };

            items.push(layoutCodeDescription);
            items.push(layoutEntryFieldSet);
            items.push(layoutGrid);
        }
        else
        {
            var collectionSearchFieldSet = {
                xtype: 'fieldset',
                id: 'collectionsearchfieldset',
                collapsible: false,
                style: 'position: relative;',
                autoHeight: true,
                layout: 'hbox',
                border: false,
                height: 40,
                items:
                ([
                    { xtype: 'label', text: "{/literal}{#str_ButtonSearch#}{literal}", style: 'padding: 3px'},
                    { xtype: 'spacer', width: 10 },
                    { xtype: 'textfield', id: 'collectionSearchField', name: 'collectionSearchField', width: 250, maxLength: 50, listeners:{	blur:{	fn: forceAlphaNumericCollectionSearch }	}, post: true},
                    { xtype: 'spacer', width: 5 },
                    { xtype: 'button', id:'collectionSearchButton', text: '{/literal}{#str_ButtonSearch#}{literal}', ctCls:'x-toolbar-standardbutton', handler: onCollectionSearch}
                ])
            };

            items.push(collectionSearchFieldSet);
            items.push(collectionGrid);
        }

    return items;
    }

    var collectionPanel = new Ext.Panel({
		xtype: 'fieldset',
		id: 'collectionPanel',
		title: '{/literal}{#str_LabelAddSpecificCollectionsOrLayouts#}{literal}',
		collapsible: false,
		autoHeight: true,
		style: 'position: relative;',
		items: panelItems('collections'),
		cellCls: 'top-align'
	});

    var layoutPanel = new Ext.Panel({
		xtype: 'fieldset',
		id: 'layoutPanel',
		title: '{/literal}{#str_LabelAddByProductCode#}{literal}',
		collapsible: false,
		autoHeight: true,
		style: 'position: relative;',
		items: panelItems('layouts')
	});

    var previewGridColumnModelObj = new Ext.grid.ColumnModel({
		defaults: {
			sortable: false,
			resizable: true
		},
		columns: [
			{header: "{/literal}{#str_LabelCode#}{literal}", width: 200, dataIndex: 'code', renderer: columnRenderer},
			{header: "{/literal}{#str_LabelName#}{literal}", width: 200, dataIndex: 'name', renderer: columnRenderer}
		]
	});

    var previewGridDataStoreObj = new Ext.data.Store({
    remoteSort: true,
    reader: new Ext.taopix.PagedArrayReader({
            idIndex: 0
        },
        previewRecordTemplate
    ),
    listeners: {
			'load': {
				fn: function(){
					cumulativeRecordID = previewGridDataStoreObj.getCount();
				}
			}}
    });



    var previewGridPanel = new Ext.grid.GridPanel({
        autoExpandMax: 5000, // Set the max width an auto expand column can be over the 1000px default.
		autoExpandColumn: 1,
		id: 'previewgrid',
        height:440,
        width: 450,
		store: previewGridDataStoreObj,
		cm: previewGridColumnModelObj,
        view: previewGridView,
        disableSelection: true,
        style:'border:1px solid #99BBE8; margin-top:6px;',
        title: '{/literal}{#str_LabelGroupPreview#}{literal}',
		enableColLock: false,
		draggable: false,
		enableColumnHide: false,
		enableColumnMove: false,
		enableHdMenu: false,
		trackMouseOver: false,
		stripeRows: true,
		columnLines: true,
		ctCls: 'grid'
    });

    var tabPanel = {
		xtype: 'tabpanel',
		id: 'maintabpanel',
		deferredRender: false,
		enableTabScroll: true,
		activeTab: 0,
		height: 455,
        width: 500,
		shadow: true,
		plain: true,
		bodyBorder: false,
		border: false,
		style: 'margin-top:6px;',
		bodyStyle: 'border-right: 1px solid #96bde7; border-left: 1px solid #96bde7; border-bottom: 1px solid #96bde7; background-color: #dee9f6;',
		defaults: {
			frame: false,
			autoScroll: true,
			hideMode:'offsets',
			labelWidth: 230,
			bodyStyle: 'padding:5px 10px 0 10px; border-top: 0px;'
		},
		items:
		[
			collectionPanel,
			layoutPanel
		]
	}


    var settingsPanel = new Ext.Panel({
		id: 'settingsPanel',
		style: 'padding: 5px 5px 3px 5px; border:1px none #8ca9cf; margin: 4px 0px 0px 0px',
		plain: true,
		bodyBorder: false,
        layout: 'hbox',
		border: false,
		labelWidth: 80,
		items:
		[
			tabPanel,
            previewGridPanel
		]
	});

    var dialogFormPanelObj = new Ext.FormPanel(
	{
		id: 'productGroupForm',
        labelAlign: 'left',
		labelWidth: 130,
		autoHeight: true,
        frame: true,
        bodyStyle: 'padding-left:5px;',
        items: [
			topPanel,
			settingsPanel
		]
    });

    /* create modal window for add and edit */
	var gDialogObj = new Ext.Window({
		id: 'dialog',
		closable: false,
		plain: true,
		modal: true,
		draggable: true,
		title: "{/literal}{$title}{literal}",
		resizable: false,
		layout: 'fit',
		height: 'auto',
		width: 1000,
		items: dialogFormPanelObj,
        title: {/literal}{if $isedit == 0}
                "{#str_LabelNewProductGroup#}"
				{else}
                "{#str_LabelEditProductGroup#}"
				{/if}{literal},
		listeners: {
			'close': {
				fn: function(){
					groupEditWindowExists = false;
				}
			}
		},
		buttons:
		[
			{
                id: 'cancelButton',
				text: "{/literal}{#str_ButtonCancel#}{literal}",
                cls: 'x-btn-right',
				handler: function()
				{
					gDialogObj.close();
				}
			},
			{
				id: 'addEditButton',
                cls: 'x-btn-right',
				{/literal}{if $isedit == 0}
					handler: addsaveHandler,
					text: "{#str_ButtonAdd#}"
				{else}
					handler: editSaveHandler,
					text: "{#str_ButtonUpdate#}"
				{/if}{literal}
			}
		]
	});

	gDialogObj.show();
}

{/literal}