<div id="contentNavigationPreview" class="contentNavigationPreview">

    <div class="buttonTopSection">

        <div class="btnLeftSection" id="backButton" data-decorator="fnShowOrderPreview" data-show="false" data-hide-header="true">
            <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone">{#str_ButtonDone#}</div>
            <div class="clear"></div>
        </div>

{if !$ordercancelled}

    {if (($canOrder == 1) or ($previewowner == 0)) && ($temporder == 0)}

        <div class="btnRightSectionPreview">

        {if ($previewowner == 0)}

            {if $canOrder == 1}

            <div class="btnPreviewLeft" data-decorator="fnExecuteButtonAction" data-target="0" data-project-name="{$projectname|escape}" data-application-name="{$webbrandapplicationname|escape}">
                <div class="btnUpdate">{#str_LabelShare#}</div>
            </div>

            {else}

            <div class="btnPreviewRight" data-decorator="fnExecuteButtonAction" data-target="0" data-project-name="{$projectname|escape}" data-application-name="{$webbrandapplicationname|escape}">
                <div class="btnUpdate">{#str_LabelShare#}</div>
            </div>

            {/if}

        {/if} {* end {if ($previewowner == 0)} *}

        {if $canOrder == 1}

            <div class="btnPreviewRight" data-decorator="fnExecuteButtonAction" data-target="1" data-project-name="">
                <div class="btnUpdate">{#str_LabelOrder#}</div>
            </div>

        {/if} {* end {if $canOrder == 1} *}

        </div>

    {else} {* else {if (($canOrder == 1) or ($previewowner == 0)) && ($temporder == 0)}*}

          {if ($ordersource == 1) && ($previewowner == -1)}

        <div class="btnRightSectionPreview" data-decorator="fnExecuteButtonAction" data-target="0" data-project-name="{$projectname|escape}" data-application-name="{$webbrandapplicationname|escape}">
            <div class="btnUpdate">{#str_LabelShare#}</div>
        </div>

        {/if} {* end {if ($ordersource == 1) && (($previewowner == 0) || ($previewowner == -1))} *}

    {/if}  {* end {if (($canOrder == 1) or ($previewowner == 0)) && ($temporder == 0)}*}

	<div class="clear"></div>

{/if}

    </div> <!-- buttonTopSection -->

</div> <!-- contentNavigation -->

<div id="contentRightScrollPreview" class="contentScrollCart">

{if $displaytype == 4}

        <iframe id="onlinePreviewFrame" style="border:0;" scrolling="no" seamless="seamless" src="{$externalpreviewurl}"></iframe>

{else}

    {include file="share/preview2_small.tpl"}

{/if}

</div> <!-- contentScrollCart -->