<body>

<form id="submitform" name="submitform" method="POST" accept-charset="utf-8" action="{$homeurl}">
<table align="center" border="0" cellpadding="5" cellspacing="0" width="800" style="border-top-width:1px; border-bottom-width:1px; border-left-width:1px; border-right-width:1px; border-style:solid; empty-cells:show">
<tr><td class="text3" align="center"><b>{#str_MessagePaymentCancelled1#}<b></td></tr>
<tr><td class="text">&nbsp;</td></tr>
<tr><td class="text1" align="center">{#str_MessagePaymentCancelled2#}</td></tr>
<tr><td class="text3">&nbsp;</td></tr>
<tr><td><input type="submit" id="ok" class="button1" value="{#str_ButtonContinue#}"/></td></tr>
</table>
<input type="HIDDEN" id="ref" name="ref" value="{$ref}">
<input type="HIDDEN" id="fsaction" name="fsaction" value="Order.ccResume">
</form>
</body>