<form id="mainform" name="mainform" method="POST" accept-charset="utf-8" onsubmit="return acceptDataEntry(document.mainform);">
<table border="0" cellpadding="5" cellspacing="0" width="500" style="empty-cells:show">
<tr class="orderstage"><td colspan="2">{$addresstitle}</td></tr>
<tr class="text"><td align="right" width="150">{#str_LabelFirstName#}:</td><td><input type="TEXT" id="contactfname" name="contactfname" class="text" size="30" value="{$contactfname}"></td></tr>
<tr class="text"><td align="right" width="150">{#str_LabelLastName#}:</td><td><input type="TEXT" id="contactlname" name="contactlname" class="text" size="30" value="{$contactlname}"></td></tr>
<tr class="text"><td align="right" width="150">{#str_LabelCompanyName#}:</td><td>{$companyname}</td></tr>
<tr class="text"><td align="right" width="150">{#str_LabelAddressLine1#}:</td><td>{$address1}</td></tr>
<tr class="text"><td align="right" width="150">{#str_LabelAddressLine2#}:</td><td>{$address2}</td></tr>
<tr class="text"><td align="right" width="150">{#str_LabelAddressLine3#}:</td><td>{$address3}</td></tr>
<tr class="text"><td align="right" width="150">{#str_LabelAddressLine4#}:</td><td>{$address4}</td></tr>
<tr class="text"><td align="right" width="150">{#str_LabelTownCity#}:</td><td>{$city}</td></tr>
<tr class="text"><td align="right" width="150">{#str_LabelCounty#}:</td><td>{$county}</td></tr>
<tr class="text"><td align="right" width="150">{#str_LabelState#}:</td><td>{$state}</td></tr>
<tr class="text"><td align="right" width="150">{#str_LabelPostCode#}:</td><td>{$postcode}</td></tr>
<tr class="text"><td align="right" width="150">{#str_LabelCountry#}:</td><td>{$countryname}</td></tr>
</table>