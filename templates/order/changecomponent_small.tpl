<div class="pageLabel">
    {#str_LabelOptions#}
</div>

<div class="outerBox">
    <ul id="choiceList">

    {foreach from=$componentlist item=row name=previews key=index}

        {if $row.code == $componentcode}

            {if $index > 0}

        <li class="optionSelected outerBoxPadding optionListBorder">

            {else}

        <li class="optionSelected outerBoxPadding optionListNoBorder">

            {/if}

        {else} {* else {if $row.code == $componentcode} *}

            {if $index > 0}

        <li class="optionListBorder outerBoxPadding">

            {else}

        <li class="optionListNoBorder outerBoxPadding">

            {/if}

        {/if} {* end {if $row.code == $componentcode} *}

            <div class="checkboxImage"></div>

        {if $row.code == $componentcode}

            <input type="radio" id="components_{$orderlineid}_{$smarty.foreach.previews.index}" style="display:none;" localcode="{$row.localcode}" name="componentsChoice" value="{$row.code}" data-decorator="fnComponentChoiceClick" data-sectionlineid="{$sectionorderlineid}" checked="checked" />

        {else} {* else {if $row.code == $componentcode} *}

            <input type="radio" id="components_{$orderlineid}_{$smarty.foreach.previews.index}" style="display:none;" localcode="{$row.localcode}" name="componentsChoice" value="{$row.code}" data-decorator="fnComponentChoiceClick" data-sectionlineid="{$sectionorderlineid}" />

        {/if} {* end {if $row.code == $componentcode} *}

            <div class="choiceContentClick">
                <label class="listLabelChoice" for="components_{$orderlineid}_{$smarty.foreach.previews.index}">

        {if $row.assetrequest != ''}

                    <img class="componentPreview" src="{$row.assetrequest|escape}" alt=""/>

        {else} {* else {if $row.assetrequest!=''} *}

                    <img  class="componentPreview" src="{$brandroot}/images/no_image-2x.jpg" alt="" />

        {/if} {* end {if $row.assetrequest!=''} *}

                    <div class="choiceDescription">{$row.name}</div>
                    <div class="clear"></div>
                    <br />
                    <span class="valuePriceComponent">{$row.pricedifference}</span>
                </label>
            </div>

        {if $row.info != ''}

            <div class="imgInfo" id="img_info_{$smarty.foreach.previews.index}" data-decorator="fnShowInfoComponent" data-name="{$row.name|escape}" data-description="{$row.info|escape}"></div>

        {else} {* else {if $row.info != ''} *}

            <div class="imgInfo" id="img_info_{$smarty.foreach.previews.index}" data-decorator="fnShowInfoComponent" data-name="{$row.name|escape}" data-description="{#str_MessageNoAdditionalInformation#|escape}"></div>

        {/if} {* end {if $row.info != ''} *}

            <div class="clear"></div>

        </li>

    {/foreach} {* end {foreach from=$componentlist item=row name=previews} *}

    </ul>
</div> <!-- outerBox -->