<table id="{$localizednamestablename}" name="{$localizednamestablename}" codesvar="{$localizedinfocodesvar}" maxchars="{$localizedinfomaxchars}" class="adminTableEntryBorder" border="0" cellpadding="5" cellspacing="0">
<tbody class="text1">
<tr><td colspan="4"></td></tr>
<tr>
<td width="1"></td>
<td class="tableHeadBorderLeft" width="100">{#str_LabelLanguageName#}</td>
<td class="tableHeadBorderMiddle" width="{$localizedinfowidth}">{$localizednamelabel}</td>
<td width="1"></td>
</tr>
<tr>
<td colspan="4">
<select id="{$localizednameslistname}" name="{$localizednameslistname}" class="text"></select><input type="button" id="{$localizednamestablename}_addlanguage" name="{$localizednamestablename}_addlanguage" class="button1" value="{#str_ButtonAdd#}" onClick="return addSelectedLanguageToList('{$localizednamestablename}', {$localizedinfocodesvar});" />
</td>
</tr>
</table>