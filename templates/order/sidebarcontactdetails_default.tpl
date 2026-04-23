{if $supporttelephonenumber != '' || $supportemailaddress != ''}
    <div class="side-panel section">
        <h2 class="title-bar title-bar-panel">
            <div class="textIcon">{#str_LabelCustomerSupport#}</div>
            <img src="{$webroot}/images/icons/contact_icon.png" alt="" />
            <div class="clear"></div>
        </h2>
        {if $supporttelephonenumber != ''}
            <div class="contentDotted">
                <div class="titleDetailPanel">
                    {#str_LabelCustomerSupportLine#}:
                </div>
                <div class="sidebaraccount_text">
                    {$supporttelephonenumber}
                </div>
                <div class="contentDottedImage"></div>
            </div>
        {/if}
        {if $supportemailaddress != ''}
            <div class="content">
                <div class="titleDetailPanel">
                    {#str_LabelCustomerSupportEmail#}
                </div>
                <div class="sidebaraccount_text">
                    <a href="mailto:{$supportemailaddress}">
                        {$supportemailaddress}
                    </a>
                </div>
            </div>
        {/if}
    </div>
{/if}