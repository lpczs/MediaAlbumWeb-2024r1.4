<table id="{$localizednamestablename2}" name="{$localizednamestablename2}" codesvar="{$localizedinfocodesvar2}" maxchars="{$localizedinfomaxchars2}" class="adminTableEntryBorder" border="0" cellpadding="5" cellspacing="0">
<tbody class="text1">
<tr><td colspan="4"></td></tr>
<tr><td colspan="4"></td></tr>
<tr>
<td width="1"></td>
<td class="tableHeadBorderLeft" width="100">{#str_LabelLanguageName#}</td>
<td class="tableHeadBorderMiddle" width="{$localizedinfowidth2}">{$localizednamelabel2}</td>
<td width="1"></td>
</tr>
<tr>
<td colspan="4">
<select id="{$localizednameslistname2}" name="{$localizednameslistname2}" class="text"></select><input type="button" id="{$localizednamestablename}_addlanguage" name="{$localizednamestablename}_addlanguage" class="button1" value="{#str_ButtonAdd#}" onClick="return addSelectedLanguageToList('{$localizednamestablename2}', {$localizedinfocodesvar2});" />
</td>
</tr>
</table>