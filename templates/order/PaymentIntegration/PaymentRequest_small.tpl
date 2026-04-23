<div class="paymentContent">
    <div class="paymentMessage">
        {#str_MessageTransferring#}<br />
        {#str_MessagePleaseWait#}
    </div>
    <div id="progress" class="paymentProgressBar"></div>
</div>

<form id="requestform" name="requestform" action="" method="{$method}" accept-charset="utf-8">

    {foreach from=$parameter key=name item=value}

        <input type="hidden" name="{$name}" value="{$value}">

    {/foreach}
    
</form>