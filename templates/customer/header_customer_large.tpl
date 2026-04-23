<div class="headerLeft">
    {if $mainwebsiteurl == ""}
        <img src="{$headerlogoasset}" alt=""/>
    {else}
        <a href="{$mainwebsiteurl}" border="0">
            <img src="{$headerlogoasset}" alt=""/>
        </a>
    {/if}
</div>
<div class="headerRight">
    {$systemlanguagelist}
</div>
<div class="clear"></div>