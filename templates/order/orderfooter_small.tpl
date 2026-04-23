{if ($orderfootersections|@sizeof > 0) || ($orderfootercheckboxes|@sizeof > 0)}

    {if $call_action == 'init'}

        {if $stage == 'qty'}

<div class="orderFooter" id="orderFooter">

        {else}

<div class="orderFooter">

        {/if}

    {/if} {* end {if $call_action == 'init'} *}

    <!-- START ORDERFOOTER COMPONENTS -->

    {foreach from=$orderfootersections item=section} {* order footer sections *}

        {if $section.showcomponentname == true}

            {if !isset($bTitleOrder)}

    <div class="sectionLabelLegendFooter outerBoxPadding">
        {#str_LabelAdditionalItems#}
    </div>

                {if $stage == 'qty'}

    <div id="contentFooter" class="outerBoxPadding outerBoxNoPaddingTop">

                {else}

    <div class="outerBoxPadding outerBoxNoPaddingTop">

                {/if}


                {if ($stage == 'payment')}

    <div class="showHideComponents" data-decorator="fnShowHideComponents" data-orderlineid="orderfooter" data-isfooter="true">
        <span id="linkToggle_orderfooter" class="showHideTitle hidden">{#str_LabelShowDetails#}</span>
    </div>

    <div id="contentCustomise_orderfooter" style="display:none;">

                {/if}

                {assign var="bTitleOrder" value="true"}

            {/if} {* end {if !isset($bTitleOrder)} *}

            {if ($stage == 'qty')}

        <div  class="innerBox" id="componentContent_{$section.orderlineid}">

            {else}

        <div  class="innerBox">

            {/if}

            <div class="sectionLabel innerBoxPadding">

                <div class="componentLabel">

                    {$section.sectionlabel}

                </div> <!-- componentLabel -->

                <div class="componentPrice">
                    {$section.itemcomponenttotalsell}
                </div>

                <div class="clear"></div>

            </diV> <!-- sectionLabel innerBoxPadding -->

            {if ($stage == 'qty')}

            <div id="componentrow_{$section.orderlineid}" class="componentBloc innerBoxPadding" >

            {else}

            <div class="componentBloc innerBoxPadding">

            {/if}

            {if ($section.haspreview > 0) && ($stage == 'qty')}

                <img class="componentPreview" src="{$section.componentpreviewsrc|escape}" alt=""/>
                <div class="componentContentText">

            {else} {* else {if ($section.haspreview > 0) && ($stage == 'qty')} *}

                <div class="componentContentTextLong">

            {/if} {* end {if ($section.haspreview > 0) && ($stage == 'qty')} *}

                    <div class="componentTitle">
                        {$section.itemcomponentname}
                    </div>

            {if !empty($section.itemcomponentinfo) && ($stage == 'qty')}

                    <div class="componentDescription">
                        {$section.itemcomponentinfo}
                    </div>

            {/if} {* end {if !empty($section.itemcomponentinfo) && ($stage == 'qty')} *}

            {if ! empty($section.itemcomponentmoreinfolinkurl)}

                    <div class="componentDescription">
                      <a href="{$section.itemcomponentmoreinfolinkurl}" target="_blank">{$section.itemcomponentmoreinfolinktext}</a>
                    </div>

            {/if} {* end {if !empty($section.itemcomponentmoreinfolinkurl)} *}

            {if !empty($section.itemcomponentpriceinfo) && ($stage == 'qty')}

                    <div class="componentDescription">
                        {$section.itemcomponentpriceinfo}
                    </div>

            {/if} {* end {if !empty($section.itemcomponentpriceinfo) && ($stage == 'qty')} *}

            {if ($section.pricingmodel == 7) || ($section.pricingmodel == 8)}

                    <ul class="componentList">
                        <li>{#str_LabelQuantity#}: <span class="componentListNumber">{$section.quantity}</span></li>
                    </ul>

            {/if} {* {if ($section.pricingmodel == 7) || ($section.pricingmodel == 8)} *}

            {if ($section.subsections|@sizeof > 0) || ($section.checkboxes|@sizeof > 0)}

                {assign var='ulopen' value='false' scope='root'}

                {foreach from=$section.subsections item=subsection} {* subsections of a section *}

                        {if $subsection.showcomponentname==true}

                            {if $ulopen == "false"}

                            {assign var="ulopen" value="true"}

                    <ul class="componentList">

                        {/if} {* end {if $ulopen == "false"} *}

                        <li>{$subsection.itemcomponentname}</li>

                        {if ($subsection.pricingmodel == 7) || ($subsection.pricingmodel == 8)}

                        <ul class="componentList">
                            <li>{#str_LabelQuantity#}: <span class="componentListNumber">{$subsection.quantity}</span></li>
                        </ul>

                        {/if} {* {if ($subsection.pricingmodel == 7) || ($subsection.pricingmodel == 8)} *}

                        {if $stage == 'payment'}

                            {if $subsection.metadatahtml}

                                {$subsection.metadatahtml}

                            {/if} {* end {if $subsection.metadatahtml} *}

                        {/if} {* end {if $stage == 'payment'} *}

                    {/if} {* end {if $subsection.showcomponentname==true} *}

                {/foreach} {* end {foreach from=$section.subsections item=subsection} *}

                {foreach from=$section.checkboxes item=checkbox} {* checkboxes of a section *}

                    {if ($checkbox.showcomponentname == true) && ($checkbox.checked == 1)}

                        {if $ulopen == "false"}

                            {assign var="ulopen" value="true"}

                    <ul class="componentList">

                        {/if} {* end {if $ulopen == "false"} *}

                        <li>{$checkbox.itemcomponentcategoryname}</li>

                        {if ($checkbox.pricingmodel == 7) || ($checkbox.pricingmodel == 8)}

                        <ul class="componentList">
                            <li>{#str_LabelQuantity#}: <span class="componentListNumber">{$checkbox.quantity}</span></li>
                        </ul>

                        {/if} {* {if ($checkbox.pricingmodel == 7) || ($checkbox.pricingmodel == 8)} *}

                        {if $stage == 'payment'}

                            {if $checkbox.metadatahtml}

                                {$checkbox.metadatahtml}

                            {/if} {* end {if $checkbox.metadatahtml} *}

                        {/if} {* end {if $stage == 'payment'} *}

                    {/if} {* end {if ($checkbox.showcomponentname == true) && ($checkbox.checked == 1)} *}

                {/foreach} {* end {foreach from=$section.checkboxes item=checkbox} *}

                {if $ulopen == "true"}

                    </ul>

                {/if} {* end {if $ulopen == "true"} *}

            {/if} {* end {if ($section.subsections|@sizeof > 0) || ($section.checkboxes|@sizeof) > 0} *}

            {if $stage == 'payment'}

                {if $section.metadatahtml}

                    {$section.metadatahtml}

                {/if} {* end {if $section.metadatahtml} *}

            {/if} {* end {if $stage == 'payment'} *}

                </div>  <!-- componentContentText  or componentContentTextLong-->

                <div class="clear"></div>

            </div> <!-- componentBloc innerBoxPadding -->

            {if $stage == 'qty'}

                {if (($section.pricingmodel == 7) || ($section.pricingmodel == 8)) || ($section.subsections|@sizeof > 0)
                    || ($section.itemcomponentbuttons|@sizeof > 0) || ($section.metadatahtml) || ($section.checkboxes|@sizeof > 0)
                    || ($subsection.metadatahtml)}

            <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="componentView|-1|{$section.orderlineid}">

                <div class="changeBtnText">
                    {#str_LabelOptionsAndExtras#}

                    {if $section.isonekeywordmandatory == true}

                    <img class="valueRequiredImg" src="{$brandroot}/images/asterisk.png" alt="*" />

                    {/if} {* end {if $section.isonekeywordmandatory == true} *}

                </div>

                <div class="changeBtnImg">
                    <img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt= ">" />
                </div>
                <div class="clear"></div>

            </div> <!-- contentChangeBtn outerBoxPadding -->

                {/if} {* end {if (($section.pricingmodel == 7) || ($section.pricingmodel == 8)) || ($section.subsections|@sizeof > 0)
                    || ($section.itemcomponentbuttons|@sizeof > 0) || ($section.metadatahtml) || ($section.checkboxes|@sizeof > 0)
                    || ($subsection.metadatahtml)} *}

            {/if} {* end {if $stage == 'qty'} *}

        </div>  <!-- innerBox -->

        {/if} {* end {if $section.showcomponentname == true} *}

    {/foreach} {* end {foreach from=$orderfootersections item=section} *}

    <!-- END ORDERFOOTER COMPONENTS -->

    <!-- ORDERFOOTER CHECKBOXES START -->

    {foreach from=$orderfootercheckboxes item=checkbox}

        {if $checkbox.showcomponentname==true}

            {if !isset($bTitleOrder) && (($stage=='qty') || ($stage=='payment' && $checkbox.checked == 1))}

    <div class="sectionLabelLegendFooter outerBoxPadding">
        {#str_LabelAdditionalItems#}
    </div>

                {if ($stage == 'qty')}

    <div id="contentFooter" class="outerBoxPadding">

                {else}

    <div class="outerBoxPadding">

                {/if}

                {if ($stage == 'payment')}

        <div class="showHideComponents" data-decorator="fnShowHideComponents" data-orderlineid="orderfooter" data-isfooter="true">
            <span id="linkToggle_orderfooter" class="showHideTitle hidden">{#str_LabelShowDetails#}</span>
        </div>

        <div id="contentCustomise_orderfooter" style="display:none;">

                {/if}

                {assign var="bTitleOrder" value="true"}

            {/if} {* end {if !isset($bTitleOrder) && (($stage=='qty') || ($stage=='payment' && $checkbox.checked == 1))} *}

            {if ($stage=='qty') || ($stage=='payment' && $checkbox.checked == 1)}


                {if ($stage == 'qty')}

        <div id="componentContent_{$checkbox.orderlineid}" class="innerBox">

                {else}

        <div class="innerBox">

                {/if}

            <div class="sectionLabel innerBoxPadding">

                <div class="componentLabel">

                    {$checkbox.itemcomponentcategoryname}

                </div> <!-- componentLabel -->

                {if $checkbox.checked || $checkbox.totalsell == #str_LabelNotAvailable#}

                <div class="componentPrice">
                    {$checkbox.totalsell}
                </div>

                {/if}

                <div class="clear"></div>

            </diV> <!-- sectionLabel innerBoxPadding -->

            <div class="componentBloc innerBoxPadding">

                    {if ($stage == 'qty')}

                <div class="checkboxBloc">

                    {else}

                <div>

                    {/if}

                    {if ($checkbox.haspreview > 0) && ($stage == 'qty')}

                    <img class="componentPreview" src="{$checkbox.componentpreviewsrc|escape}" alt=""/>
                    <div class="componentContentText">

                    {else}  {* else {if ($checkbox.haspreview > 0) && ($stage == 'qty')} *}

                    <div class="componentContentTextLong">

                    {/if} {* end {if ($checkbox.haspreview > 0) && ($stage == 'qty')} *}

                        <div class="componentTitle">
                            {$checkbox.itemcomponentname}
                        </div>

                    {if !empty($checkbox.itemcomponentinfo)}

                        <div class="componentDescription">
                            {$checkbox.itemcomponentinfo}
                        </div>

                    {/if} {* end {if !empty($checkbox.itemcomponentinfo)} *}

                    {if ! empty($checkbox.itemcomponentmoreinfolinkurl)}

                      <div class="componentDescription">
                        <a href="{$checkbox.itemcomponentmoreinfolinkurl}" target="_blank">{$checkbox.itemcomponentmoreinfolinktext}</a>
                      </div>

                    {/if} {* end {if !empty($checkbox.itemcomponentmoreinfolinkurl)} *}

                    {if !empty($checkbox.itemcomponentpriceinfo) && ($stage == 'qty')}

                        <div class="componentDescription">
                            {$checkbox.itemcomponentpriceinfo}
                        </div>

                    {/if} {* end {if !empty($checkbox.itemcomponentpriceinfo) && ($stage == 'qty')} *}

                    {if ($stage == 'qty') && (!$checkbox.checked)}

                        <div class="componentCheckBoxPrice">
                            ({$checkbox.totalsell})
                        </div>

                    {/if} {* end {if ($stage == 'qty') && (!$checkbox.checked)} *}

                    {if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8) && ($checkbox.checked == 1)}

                        <ul class="componentList">
                            <li>{#str_LabelQuantity#}: <span class="componentListNumber">{$checkbox.quantity}</span></li>
                        </ul>

                    {/if} {* end {if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8) && ($checkbox.checked == 1)} *}

                    {if $stage == 'payment'}

                        {if $checkbox.metadatahtml}

                            {$checkbox.metadatahtml}

                        {/if} {* {if $checkbox.metadatahtml} *}

                    {/if} {* end {if $stage == 'payment'} *}


                    </div> <!-- componentContentText  or componentContentTextLong-->

                    <div class="clear"></div>

                </div> <!-- checkboxBloc -->

                    {if $stage=='qty'}

                        {foreach from=$checkbox.itemcomponentbuttons item=button}

                <div class="checkboxBtn">

                    <div class="onOffSwitch">
                        <input type="checkbox" id="onOffSwitch_{$checkbox.orderlineid}" class="onOffSwitchCheckbox" name="onOffSwitch" data-decorator="fnCheckboxEffectAction" data-orderlineid="-1" data-checkboxlineid="{$checkbox.orderlineid}" data-trigger="change"{if $checkbox.checked}checked="checked"{/if} />
                        <label for="onOffSwitch_{$checkbox.orderlineid}" class="onOffSwitchLabel">
                            <div class="onOffSwitchInner" ontxt="" offtxt=""></div>
                            <div id="checkbox_{$checkbox.orderlineid}" class="onOffSwitchButton"></div>
                        </label>
                    </div>

                </div> <!-- checkboxBtn -->

                        {/foreach} {* end {foreach from=$checkbox.itemcomponentbuttons item=button} *}

                    {/if} {* end {if $stage=='qty'} *}

                <div class="clear"></div>

            </div>  <!-- componentBloc innerBoxPadding -->

            <div class="clear"></div>

                    {if $stage == 'qty'}

                        {if ($checkbox.checked == 1) && (($checkbox.metadatahtml) || ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8))}

            <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="componentView|-1|{$checkbox.orderlineid}">

                <div class="changeBtnText">
                    {#str_LabelOptionsAndExtras#}

                    {if $checkbox.isonekeywordmandatory == true}

                    <img class="valueRequiredImg" src="{$brandroot}/images/asterisk.png" alt="*" />

                    {/if} {* end {if $checkbox.isonekeywordmandatory == true} *}

                </div>

                <div class="changeBtnImg">
                    <img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt= ">" />
                </div>

                <div class="clear"></div>

            </div> <!-- contentChangeBtn outerBoxPadding -->


                        {/if} {* end {if ($subsection.metadatahtml) && ($checkbox.checked == 1)} *}

                    {/if} {* end {if $stage == 'qty'} *}

        </div> <!-- innerBox -->

                {/if}  {* end  {if (($stage=='payment' && $checkbox.checked == 1) || $stage=='qty')} *}

            {/if} {* end {if $checkbox.showcomponentname==true} *}

        {/foreach} {* end {foreach from=$orderline.checkboxes item=checkbox} *}

    {if isset($bTitleOrder)}

        {if ($stage == 'payment')}

    </div> <!-- contentCustomise_orderfooter -->

        {/if}

    </div> <!-- contentFooter -->

    {/if} {* end {if isset($bTitleOrder)} *}

    <!-- END ORDER FOOTER CHECKBOXES -->

    <div class="footerPriceSection">

        {if $stage=='qty'}

            {if ($showpriceswithtax == false)}

         <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel">{$orderfootersubtotalname}:</div>
            <div class="itemTotalNumber">{$orderfooteritemstotalsell}</div>
            <div class="clear"></div>
        </div>

            {else} {* else {if ($showpriceswithtax == false)} *}

         <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel">{$orderfootersubtotalname}:</div>
            <div class="itemTotalNumber">{$orderfootertotal}</div>
            <div class="clear"></div>
        </div>

            {/if} {* end {if ($showpriceswithtax == false)} *}

        <div class="clear"></div>

        {/if} {* end {if $stage=='qty'} *}


        {if $stage=='payment'}

            {if ($showpriceswithtax == false)}

                {if (($showzerotax) || ((!$showzerotax) && ($orderfootertaxtotalraw > 0)))}

                    {if ($differenttaxrates)}
         <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel">{$orderfootersubtotalname}:</div>
            <div class="itemTotalNumber">{$orderfootersubtotal}</div>
            <div class="clear"></div>
        </div>
                    {/if}

                    {if ($showtaxbreakdown)}

                        {if ($differenttaxrates)}

                            {if ($footertaxratesequal == 1)}

         <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel">{$orderfootertaxname} ({$orderfootertaxrate}%):</div>
            <div class="itemTotalNumber">{$orderfootertaxtotal}</div>
            <div class="clear"></div>
        </div>
                            {else}

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel">{$orderfootertaxname}:</div>
            <div class="itemTotalNumber">{$orderfootertaxtotal}</div>
            <div class="clear"></div>
        </div>
                            {/if}

                        {/if}

                    {/if}

                {/if}

            {/if}

            {if ($showpriceswithtax == false) && (($showzerotax) || ((!$showzerotax) && ($orderfootertaxtotalraw > 0))) && ($differenttaxrates)}

         <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel">{$orderfootertotalname}:</div>
            <div class="itemTotalNumber">{$orderfootertotal}</div>
            <div class="clear"></div>
        </div>

            {else}

         <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel">{$orderfootersubtotalname}:</div>
            <div class="itemTotalNumber">{$orderfootersubtotal}</div>
            <div class="clear"></div>
        </div>

            {/if}

            {if ($showpriceswithtax)}

                {if (($showzerotax) || ((!$showzerotax) && ($orderfootertaxtotalraw > 0)))}

                    {if ($showtaxbreakdown)}

                        {if ($differenttaxrates)}

        <div class="itemTotalNumber outerBoxPadding">
            {$includesorderfootertaxtext}
        </div>
                        {/if}

                    {/if}

                {/if}

            {/if}

        {/if}

    </div> <!-- footerPriceSection -->

    {if $call_action == 'init'}

</div> <!-- orderFooter -->

    {/if} {* end {if $call_action == 'init'} *}

{/if} {* end footer total*}