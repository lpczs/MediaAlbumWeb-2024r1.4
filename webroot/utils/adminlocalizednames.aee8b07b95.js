function initializeLocalizedNames(pTableName, pLocalizedCodesArray, pLocalizedNamesArray)
{
    // build the list of localized strings
	var tableObj = document.getElementById(pTableName);
    
    for (var i =0; i < pLocalizedCodesArray.length; i++)
    {
    	
    	var languageName = "";
        var languageNameIndex = ArrayIndexOf(gAllLanguageCodesArray, pLocalizedCodesArray[i]);

        if (languageNameIndex > -1)
        {
            languageName = gAllLanguageNamesArray[languageNameIndex];
        }
        
        addLanguageToTable(pTableName, tableObj, pLocalizedCodesArray[i], languageName, pLocalizedNamesArray[i]);
    }
    
    buildLanguageList(pTableName, pLocalizedCodesArray);
}

function addLanguageToTable(pTableName, tableObj, languageCode, languageName, contents)
{
	var codesVarName = tableObj.getAttribute("codesvar");
    var maxChars = tableObj.getAttribute("maxchars");
    
    contents = contents.replace(/\"/g, "&quot;");
    
    var lastRow = tableObj.rows.length;
    var row = tableObj.insertRow(lastRow - 1);
    
    var cell = row.insertCell(0);
    
    cell = row.insertCell(1);
    var textNode = document.createTextNode(languageName);
    cell.appendChild(textNode);
    cell.className = "tableColBorderLeft";
    cell.style.fontWeight = "normal";
    
    cell = row.insertCell(2);
    var textNode = document.createTextNode("");
    cell.appendChild(textNode);
    cell.innerHTML = "<input type=\"TEXT\" id=" + pTableName + "_name" + languageCode + " name=" + pTableName + "_name" + languageCode + " class=\"text\" size=\"" + maxChars + "\" value=\"" + contents + "\">";
    cell.className = "tableColBorderMiddle";
    cell.style.fontWeight = "normal";
    
    cell = row.insertCell(3);
    var textNode = document.createTextNode("");
    cell.appendChild(textNode);
    cell.innerHTML = "<a href=\"\" id=" + pTableName + "_delete" + languageCode + " name=" + pTableName + "_delete" + languageCode + " onclick=\"return deleteLanguageFromList('" + pTableName + "', " + codesVarName + ", this, '" + languageCode + "');\">" + gDeleteLabel;
    cell.className = "text";
}

function buildLanguageList(pTableName, pLocalizedCodesArray)
{
    var listIndex = 0;
    var theList = document.getElementById(pTableName + "_languagelist");
    
    theList.options.length = 0;
    for (i=0; i< gAllLanguageCodesArray.length; i++)
    {
        var languageCodeIndex = ArrayIndexOf(pLocalizedCodesArray, gAllLanguageCodesArray[i]);
        if (languageCodeIndex == -1)
        {
            theList.options[listIndex] = new Option(gAllLanguageNamesArray[i], gAllLanguageCodesArray[i]);
            listIndex++;
        }
    }
}

function addSelectedLanguageToList(pTableName, pLocalizedCodesArray)
{
    var theForm = document.mainform;
    var theList = document.getElementById(pTableName + "_languagelist");
    
    if (theList.selectedIndex > -1)
    {
        var languageCode = theList.options[theList.selectedIndex].value;
        var languageName = theList.options[theList.selectedIndex].innerHTML;
        
        pLocalizedCodesArray.push(languageCode);
        
        var tableObj = document.getElementById(pTableName);
        addLanguageToTable(pTableName, tableObj, languageCode, languageName, "");
        buildLanguageList(pTableName, pLocalizedCodesArray);
    }
    
    return false;
}

function deleteLanguageFromList(pTableName, pLocalizedCodesArray, link, languageCode)
{
    var languageCodeIndex = ArrayIndexOf(pLocalizedCodesArray, languageCode);
    if (languageCodeIndex > -1)
    {
        var temp = pLocalizedCodesArray.splice(languageCodeIndex, 1);
    }
    
    var tableObj = document.getElementById(pTableName);
    var tableRow = link.parentNode.parentNode;
    tableObj.deleteRow(tableRow.rowIndex);
    
    buildLanguageList(pTableName, pLocalizedCodesArray);
    
    return false;
}

function validateLocalizedTable(pTableName, pLocalizedCodesArray)
{
    var valid = true;
    if (pLocalizedCodesArray.length > 0)
    {
        for (i in pLocalizedCodesArray)
        {
            var theValue = document.getElementById(pTableName + "_name" + pLocalizedCodesArray[i]).value;
            if (theValue.length == 0)
            {
                valid = false;
                break;
            }
        }
    }
    else
    {
        valid = false;
    }
    
    return valid;
}

function convertLocalizedTableToString(pTableName, pLocalizedCodesArray)
{
    var localizedString = "";
    for (i in pLocalizedCodesArray)
    {
        var theValue = document.getElementById(pTableName + "_name" + pLocalizedCodesArray[i]).value;
        localizedString = localizedString + pLocalizedCodesArray[i] + " " + theValue;
        if (i < (pLocalizedCodesArray.length - 1))
        {
            localizedString = localizedString + "<p>";
        }
    }
    
    return localizedString;
}

function enableLocalizedTable(pTableName, pLocalizedCodesArray, pEnabled)
{
    var localizedString = "";
    for (i in pLocalizedCodesArray)
    {
    	document.getElementById(pTableName + "_name" + pLocalizedCodesArray[i]).disabled = ! pEnabled;
    	document.getElementById(pTableName + "_delete" + pLocalizedCodesArray[i]).style.visibility = pEnabled ? "visible" : "hidden";
    }
    
    document.getElementById(pTableName + "_languagelist").disabled = ! pEnabled;
    document.getElementById(pTableName + "_addlanguage").disabled = ! pEnabled;
    
    return localizedString;
}

function setDeleteLink(pTableName)
{
    var TabObj = document.getElementById(pTableName);

	for (var i=2; i < TabObj.rows.length-1; i++)
	{
		var row = TabObj.rows[i];
		var cell = row.cells[3];
		
		if (TabObj.rows.length > 4)
		{
			cell.innerHTML = "<a href=\"\" id=" + pTableName + "_delete" + (i - 1) + " onclick=\"return deleteAddressFromList('" + pTableName + "', " + (i - 1) + ");\">" + gDeleteLabel;
		}
		else
		{
			cell.innerHTML = "";
		}
		cell.className = "text";
	}
}

function addEmailAddress(pTableName, pName, pAddress)
{
    var tableObj = document.getElementById(pTableName);
    var lastRow = tableObj.rows.length;
    var nextRow = tableObj.rows.length - 2;
    var row = tableObj.insertRow(lastRow - 1);
    
    var cell = row.insertCell(0);
    
    cell = row.insertCell(1);
    var textNode = document.createTextNode("");
    cell.appendChild(textNode);
    cell.innerHTML = '<input type="TEXT" id="' + pTableName + '_name' + nextRow + '" class="tableColBorderLeft" value="' + pName + '" style="width:100%;">';
    cell.className = "tableColBorderLeft";
    cell.style.fontWeight = "normal";
    
    cell = row.insertCell(2);
    var textNode = document.createTextNode("");
    cell.appendChild(textNode);
    cell.innerHTML = '<input type="TEXT" id="' + pTableName + '_address' + nextRow + '" class="tableColBorderMiddle" value="' + pAddress + '" style="width:100%;">';
    cell.className = "tableColBorderMiddle";
    cell.style.fontWeight = "normal";
    
    cell = row.insertCell(3);
    var textNode = document.createTextNode("");
    cell.appendChild(textNode);
    if (pName + pAddress == "")
    {
    	cell.innerHTML = "";
    }
    cell.className = "text";
    
    setDeleteLink(pTableName);
}

function deleteAddressFromList(pTableName, pLine)
{
    var tableObj = document.getElementById(pTableName);
    var NameObj = document.getElementById(pTableName+"_name"+pLine);
	// 1 copy everything below the deleted row up
	// 2 delete last row
	// this is to keep continuous indices (convertEmailTableToString relies on this)
	// 1
	for (var i=pLine; i < tableObj.rows.length-3; i++)
	{
		Obj1 = document.getElementById(pTableName+"_name"+i);
		Obj2 = document.getElementById(pTableName+"_name"+(i+1));
		Obj1.value = Obj2.value;
		Obj1 = document.getElementById(pTableName+"_address"+i);
		Obj2 = document.getElementById(pTableName+"_address"+(i+1));
		Obj1.value = Obj2.value;
	}
	// 2
    tableObj.deleteRow(tableObj.rows.length-2);
    
	setDeleteLink(pTableName);
    
    return false;
}

function convertEmailTableToString(pTableName, pPart)
{
    var nameString = "";
    var NameObj = "";
    var TabObj = document.getElementById(pTableName);

	for (var i=1; i < TabObj.rows.length-2; i++)
	{
		NameObj = document.getElementById(pTableName + pPart + i);
		nameString = nameString + NameObj.value;
		if (i < TabObj.rows.length-3)
		{
			nameString = nameString + ";";
		}
	}

    return trim(nameString,' ');
}

