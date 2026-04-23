{if $orderline.orderlineid != -1}

    {if $call_action == 'init'} {* remove not needed html for ajax call*}

<div class="outerBox outerBoxWithTab">

    <div class="itemTitle">
        {#str_LabelHeaderItem#} {$orderline.orderlineid}
    </div>

    <div class="clear"></div>

    <div>

        {if $stage == 'qty'}

        <div class="orderLine" id="ordertableobj_{$orderline.orderlineid}">

        {else}

        <div class="orderLine">

        {/if}

    {/if} {* ends {if $call_action == 'init'}*}

<!-- START ITEM SECTION -->

    <div class="contentTextInside">

        <div class="itemDetail">

            <div class="outerBoxPadding">

                <div class="productTitle">
                    <div id="productName{$stage}" class="productName">
                        {$orderline.projectname}
                    </div> <!-- componentLabel -->

                    <div id="productPrice{$stage}" class="productPrice">
                        {if $orderline.itemshowproductsell == 1}
                			{$orderline.itemproducttotalsell}
                		{/if}
                    </div>

                    <div class="clear"></div>
                </div> <!-- productTitle -->

                <div class="productCollectionTitle">
                    {$orderline.itemproductname}
                </div>
            {if $orderline.itemproducttype != 2}
                <div class="product-collection-page-count">
                    {$orderline.itempagecount}
                    {if $orderline.itempagecount == 1}
                         {#str_LabelPage#}
                    {else}
                         {#str_LabelPages#}
                    {/if}
                </div>
            {/if}

    {if $orderline.itemproductinfo != ""}

                <div class="product-info">{$orderline.itemproductinfo}</div>

    {/if} {* end {if $orderline.itemproductinfo != ""} *}

            <!-- EXTERNAL ASSETS -->

    {if ($orderline.displayassets == true) && ($orderline.itemexternalassets|@sizeof > 0)}

            <div class="innerBox">

                <div class="sectionLabel innerBoxPadding">

                    <div class="componentLabel">
                        {#str_TitleExternalAssets#}
                    </div> <!-- componentLabel -->

                    <div class="componentPrice">
                        {$orderline.totalprice}
                    </div> <!-- componentPrice -->

                    <div class="clear"></div>

                </div> <!-- sectionLabel innerBoxPadding -->

                <div class="componentBloc innerBoxPadding">

		{foreach from=$orderline.itemexternalassets item=asset}

                    <div class="componentLabel">
                        {$asset.pagename}:{$asset.name}
                    </div> <!-- componentLabel -->

                    <div class="componentPrice">
                        {$asset.charge}
                    </div> <!-- componentPrice -->

                    <div class="clear"></div>

		{/foreach} {* end {foreach from=$orderline.itemexternalassets item=asset } *}

                </div> <!-- componentBloc -->

                <div class="clear"></div>

            </div> <!-- innerBox -->

	{/if} {* end {if $orderline.displayassets == true} *}

            <!-- END EXTERNAL ASSETS -->

            <!-- Calendar Customisations -->
    {if $orderline.calendarcustomisations|@count > 0}

                <div class="sectionLabel">

    {foreach from=$orderline.calendarcustomisations item=calendarcustomisations }

                    <div class="calComponentLabel">{$calendarcustomisations.name}</div>
                    <div class="componentPrice calComponentPrice">{$calendarcustomisations.formattedtotalsell}</div>

                    <div class="clear"></div>
    {/foreach}
                </div> <!-- componentBloc -->

                <div class="clear"></div>

    {/if}
			<!--  AI COMPONENT -->
	{if $orderline.aicomponent|@sizeof > 0}

        <div class="componentContainer smart-design-component">
            <div class="innerBox">
                <div class="sectionLabel innerBoxPadding">
                    <div class="componentLabel">
                        {$orderline.aicomponent.name}
                    </div> 
                    <div class="componentPrice">
                        {$orderline.aicomponent.formattedtotalsell}
                    </div> 
                    <div class="clear"></div>
                </div>
                <div class="componentBloc innerBoxPadding">
                    <img class="componentPreview" src="{$orderline.aicomponent.previewsrc|escape}"/>
                    <div class="componentContentText">
                        <div class="componentDescription">{$orderline.aicomponent.componentinfo}</div>
                    </div> <!-- componentContentText  or componentContentTextLong-->
                    <div class="clear"></div>
                </div>
            </div>
        </div>

		<div class="clear"></div>
			<!-- END AI COMPONENT -->
	{/if}
            <!-- SINGLE PRINTSs -->

    {foreach from=$orderline.itempictures item=sizegroup name=singleprints}

                <div class="innerBox">

                    <div class="sectionLabel innerBoxPadding">

                        <div class="componentLabel">
                            {$sizegroup.groupdisplayname}
                        </div> <!-- componentLabel -->
                        <div class="componentPrice">
                            {$sizegroup.formatedgrouptotalsell}
                        </div>
                        <div class="clear"></div>

                    </div> <!-- sectionLabel innerBoxPadding -->

                    <div class="componentBloc innerBoxPadding">

						<div class="singlePrintBlocDetailsCount">
							{if $sizegroup.picturecount > 1}
								{#str_LabelPrints#|replace:'^0':$sizegroup.picturecount}
							{else}
								{#str_LabelPrint#|replace:'^0':$sizegroup.picturecount}
							{/if}
						</div>
						<div class="clear"></div>

                   </div> <!-- componentBloc innerBoxPadding -->

                <div class="clear"></div>

            </div> <!-- innerBox -->


    {/foreach} {*  end {foreach from=$orderline.itempictures item=sizegroup } *}

            <!-- END SINGLE PRINTSs -->

            <!-- CHECKBOXES START -->

    {if $orderline.checkboxes|@sizeof > 0}

        {foreach from=$orderline.checkboxes item=checkbox}

            {if $checkbox.showcomponentname==true}

                {if (($stage=='payment' && $checkbox.checked == 1) || $stage == 'qty')}

                    {if !isset($bTitleComponent) && ($stage == 'payment')}

                        {assign var="bTitleComponent" value="true"}

            <div class="showHideComponents" data-decorator="fnShowHideComponents" data-orderlineid="{$orderline.orderlineid}" data-isfooter="false">
                <span id="linkToggle_{$orderline.orderlineid}" class="showHideTitle hidden">{#str_LabelShowOptions#}</span>
            </div>

            <div id="contentCustomise_{$orderline.orderlineid}" {if $stage=='payment'}style="display:none;"{/if}>

                    {/if}

                    {if $stage == 'qty'}

            <div id="componentContent_{$checkbox.orderlineid}" class="innerBox">

                    {else}

            <div class="innerBox">

                    {/if}

                <div class="sectionLabel innerBoxPadding">

                    <div class="componentLabel">

                        {$checkbox.itemcomponentcategoryname}

                    </div> <!-- componentLabel -->

                        {if ($checkbox.checked) || ($checkbox.totalsell == #str_LabelNotAvailable#)}

                    <div class="componentPrice">
                        {$checkbox.totalsell}
                    </div>

                        {/if} {* end {if $checkbox.checked || $checkbox.totalsell == #str_LabelNotAvailable#} *}

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

                        {else} {* else {if ($checkbox.haspreview > 0) && ($stage == 'qty')} *}

                        <div class="componentContentTextLong">

                        {/if} {* end {if ($checkbox.haspreview > 0) && ($stage == 'qty')} *}

                            <div class="componentTitle">
                                {$checkbox.itemcomponentname}
                            </div>

                        {if !empty($checkbox.itemcomponentinfo) && ($stage == 'qty')}

                            <div class="componentDescription">
                                {$checkbox.itemcomponentinfo}
                            </div>

                        {/if} {* end {if !empty($checkbox.itemcomponentinfo)} *}

                        {if ! empty($checkbox.itemcomponentmoreinfolinkurl)}

                          <div class="componentDescription">
                            <a href="{$checkbox.itemcomponentmoreinfolinkurl}" target="_blank">{$checkbox.itemcomponentmoreinfolinktext}</a>
                          </div>

                        {/if} {* end {if ! empty($checkbox.itemcomponentmoreinfolinkurl)} *}

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
                            <input type="checkbox" id="onOffSwitch_{$checkbox.orderlineid}" class="onOffSwitchCheckbox" name="onOffSwitch" data-decorator="fnCheckboxEffectAction" data-orderlineid="{$orderline.orderlineid}" data-checkboxlineid="{$checkbox.orderlineid}" data-trigger="change" {if $checkbox.checked}checked="checked"{/if} />
                            <label for="onOffSwitch_{$checkbox.orderlineid}" class="onOffSwitchLabel">
                                <div class="onOffSwitchInner" ontxt="" offtxt=""></div>
                                <div id="checkbox_{$checkbox.orderlineid}" class="onOffSwitchButton"></div>
                            </label>
                        </div>

                    </div> <!-- checkboxBtn -->

                            {/foreach} {* end {foreach from=$checkbox.itemcomponentbuttons item=button} *}

                        {/if} {* end {if $stage=='qty'} *}

                    <div class="clear"></div>

                </div> <!-- componentBloc innerBoxPadding -->

                <div class="clear"></div>

                        {if $stage == 'qty'}

                            {if ($checkbox.checked == 1) && (($checkbox.metadatahtml) || ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8))}

                <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="componentView|{$orderline.orderlineid}|{$checkbox.orderlineid}">

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

        {/if} {* end {if $orderline.checkboxes|@sizeof > 0} *}


	<!-- END CHECKBOXES -->

    <!-- START COMPONENTS -->

        {if !isset($bTitleComponent) && ($orderline.sections|@sizeof > 0) && ($stage == 'payment')}

            {assign var="bTitleComponent" value="true"}

            <div class="showHideComponents" data-decorator="fnShowHideComponents" data-orderlineid="{$orderline.orderlineid}" data-isfooter="false">
                <span id="linkToggle_{$orderline.orderlineid}" class="showHideTitle hidden">{#str_LabelShowOptions#}</span>
            </div>

            <div id="contentCustomise_{$orderline.orderlineid}" {if $stage=='payment'}style="display:none;"{/if}>

        {/if}

        <div class="componentContainer">
            {foreach from=$orderline.sections item=section} {* sections *}

                {if $section.showcomponentname == true}

                    {if $stage == 'qty'}

            <div id="componentContent_{$section.orderlineid}" class="innerBox">

                    {else}

            <div class="innerBox">

                    {/if}

                        <div class="sectionLabel innerBoxPadding">

                            <div class="componentLabel">

                                {$section.sectionlabel}

                            </div> <!-- componentLabel -->

                            <div class="componentPrice">

                                {$section.itemcomponenttotalsell}

                            </div> <!-- componentPrice -->

                            <div class="clear"></div>

                        </diV> <!-- sectionLabel innerBoxPadding -->

                    {if $stage == 'qty'}

                        <div id="componentrow_{$section.orderlineid}" class="componentBloc innerBoxPadding">

                    {else}

                        <div class="componentBloc innerBoxPadding">

                    {/if}

                    {if ($section.haspreview > 0) && ($stage == 'qty')}

                            <img class="componentPreview" src="{$section.componentpreviewsrc|escape}" alt=""/>
                            <div class="componentContentText">

                    {else} {* else {if ($section.haspreview > 0) && ($stage == 'qty')} *}

                            <div class="componentContentTextLong">

                    {/if} {* end {if ($section.haspreview > 0) && ($stage == 'qty')} *}


                                <div class="componentTitle">{$section.itemcomponentname}</div>

                    {if !empty($section.itemcomponentinfo) && ($stage == 'qty')}

                                <div class="componentDescription">

                                    {$section.itemcomponentinfo}

                                </div>

                    {/if} {* end {if !empty($section.itemcomponentinfo) && ($stage == 'qty')} *}

                    {if ! empty($section.itemcomponentmoreinfolinkurl)}

                                <div class="componentDescription">
                                  <a href="{$section.itemcomponentmoreinfolinkurl}" target="_blank">{$section.itemcomponentmoreinfolinktext}</a>
                                </div>

                    {/if} {* end {if ! empty($section.itemcomponentmoreinfolinkurl)} *}

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

                    {if ($section.subsections|@sizeof > 0) || ($section.checkboxes|@sizeof) > 0}

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

                                    {/if} {* {if ($section.pricingmodel == 7) || ($section.pricingmodel == 8)} *}


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

                                    {/if} {* {if ($section.pricingmodel == 7) || ($section.pricingmodel == 8)} *}

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

                            </div> <!-- componentContentText  or componentContentTextLong-->

                            <div class="clear"></div>

                        </div> <!-- componentBloc innerBoxPadding -->

                    {if $stage == 'qty'}

                        {if (($section.pricingmodel == 7) || ($section.pricingmodel == 8)) || ($section.subsections|@sizeof > 0)
                            || ($section.itemcomponentbuttons|@sizeof > 0) || ($section.metadatahtml) || ($section.checkboxes|@sizeof > 0)
                            || ($subsection.metadatahtml)}

                        <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="componentView|{$orderline.orderlineid}|{$section.orderlineid}">

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

                    </div> <!-- innerBox -->

                {/if} {* end {if $section.showcomponentname == true} *}

            {/foreach} {* end {foreach from=$orderline.sections item=section} *}
        </div>
        <!-- END COMPONENTS -->

        <!-- START LINEFOOTER SECTIONS -->

        {if $orderline.linefootersections|@sizeof > 0}

            {if !isset($bTitleComponent) && ($stage == 'payment')}

                {assign var="bTitleComponent" value="true"}

        <div class="showHideComponents" data-decorator="fnShowHideComponents" data-orderlineid="{$orderline.orderlineid}" data-isfooter="false">
            <span id="linkToggle_{$orderline.orderlineid}" class="showHideTitle hidden">{#str_LabelShowOptions#}</span>
        </div>

        <div id="contentCustomise_{$orderline.orderlineid}" {if $stage=='payment'}style="display:none;"{/if}>

            {/if}

            {foreach from=$orderline.linefootersections item=section} {* linefooter sections *}

                {if $stage == 'qty'}

                <div id="componentContent_{$section.orderlineid}" class="innerBox">

                {else}

                <div class="innerBox">

                {/if}

                    <div class="sectionLabel innerBoxPadding">

                        <div class="componentLabel">
                            {$section.sectionlabel}
                        </div>

                        <div class="componentPrice">
                            {$section.itemcomponenttotalsell}
                        </div>

                        <div class="clear"></div>

                    </div> <!-- sectionLabel innerBoxPadding -->

                    {if $section.showcomponentname==true}

                        {if $stage == 'qty'}

                    <div id="componentrow_{$section.orderlineid}" class="componentBloc innerBoxPadding">

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

                            {foreach from=$section.checkboxes item=checkbox} {* subsections of a section *}

                                {if ($checkbox.showcomponentname == true) && ($checkbox.checked == 1)}

                                    {if $ulopen == "false"}

                                        {assign var="ulopen" value="true"}

                            <ul class="componentList">

                                    {/if} {* end {if $ulopen == "false"} *}

                                <li>{$checkbox.itemcomponentname}</li>

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

                            {/foreach} {* end {foreach from=$section.subsections item=subsection} *}

                            {if $ulopen == "true"}

                            </ul> <!-- componentList -->

                            {/if} {* end {if $ulopen == "true"} *}

                        {/if} {* end {if ($section.subsections|@sizeof > 0) || ($section.checkboxes|@sizeof > 0)} *}

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

                        <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="componentView|{$orderline.orderlineid}|{$section.orderlineid}">

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

                    {/if} {* end {if $section.showcomponentname == true} *}

                </div>

                {/foreach} {* end {foreach from=$orderline.linefootersections item=section} *}

            {/if} {* end {if $orderline.linefootersections|@sizeof > 0} *}


        <!-- END LINEFOOTER SECTIONS -->

        <!-- LINEFOOTER CHECKBOXES -->

        {if ($orderline.linefootercheckboxes|@sizeof > 0) && !isset($bTitleComponent) && ($stage == 'payment')}

            {assign var="bTitleComponent" value="true"}

        <div class="showHideComponents" data-decorator="fnShowHideComponents" data-orderlineid="{$orderline.orderlineid}" data-isfooter="false">
            <span id="linkToggle_{$orderline.orderlineid}" class="showHideTitle hidden">{#str_LabelShowOptions#}</span>
        </div>

        <div id="contentCustomise_{$orderline.orderlineid}" {if $stage=='payment'}style="display:none;"{/if}>

        {/if}


        {foreach from=$orderline.linefootercheckboxes item=checkbox}

            {if $checkbox.showcomponentname==true}

                {if $stage=='qty' || ($stage=='payment' && $checkbox.checked == 1)}

                    {if $stage == 'qty'}

                <div id="componentContent_{$checkbox.orderlineid}" class="innerBox">

                    {else}

                <div class="innerBox">

                    {/if}

                    <div class="sectionLabel innerBoxPadding">

                        <div class="componentLabel">
                            {$checkbox.itemcomponentcategoryname}
                        </div>

                    {if $checkbox.checked || $checkbox.totalsell == #str_LabelNotAvailable#}

                        <div class="componentPrice">
                            {$checkbox.totalsell}
                        </div>

                    {/if} {* end  {if $checkbox.checked || $checkbox.totalsell == #str_LabelNotAvailable#} *}

                        <div class="clear"></div>

                    </div> <!-- sectionLabel innerBoxPadding-->

                    <div class="componentBloc innerBoxPadding">

                    {if ($stage == 'qty')}

                        <div class="checkboxBloc">

                    {else}

                        <div>

                    {/if}

                    {if ($checkbox.haspreview > 0) && ($stage == 'qty')}

                            <img class="componentPreview" src="{$checkbox.componentpreviewsrc|escape}" alt=""/>
                            <div class="componentContentText">

                    {else} {* else {if ($checkbox.haspreview > 0) && ($stage == 'qty')} *}

                            <div class="componentContentTextLong">

                    {/if} {* end {if ($checkbox.haspreview > 0) && ($stage == 'qty')} *}

                                <div class="componentTitle">
                                    {$checkbox.itemcomponentname}
                                </div>

                    {if !empty($checkbox.itemcomponentinfo) && ($stage == 'qty')}

                                <div class="componentDescription">
                                    {$checkbox.itemcomponentinfo}
                                </div>

                    {/if} {* end {if !empty($checkbox.itemcomponentinfo) && ($stage == 'qty')} *}

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

                    {if (($checkbox.pricingmodel == 7) || ($checkbox.pricingmodel == 8)) && ($checkbox.checked == 1)}

                                <ul class="componentList">
                                    <li>{#str_LabelQuantity#}: <span class="componentListNumber">{$checkbox.quantity}</span></li>
                                </ul>

                    {/if} {* end {if (($checkbox.pricingmodel == 7) || ($checkbox.pricingmodel == 8)) && ($checkbox.checked == 1)} *}

                    {if $stage == 'payment'}

                        {if $checkbox.metadatahtml}

                            {$checkbox.metadatahtml}

                        {/if} {* end {if $checkbox.metadatahtml} *}

                    {/if} {* end {if $stage == 'payment'} *}

                            </div> <!-- componentContentText  or componentContentTextLong-->

                            <div class="clear"></div>

                        </div> <!-- checkboxBloc -->

                    {if $stage=='qty'}

                        {foreach from=$checkbox.itemcomponentbuttons item=button}

                        <div class="checkboxBtn">

                            <div class="onOffSwitch">
                                <input type="checkbox" id="onOffSwitch_{$checkbox.orderlineid}" class="onOffSwitchCheckbox" name="onOffSwitch" data-decorator="fnCheckboxEffectAction" data-orderlineid="{$orderline.orderlineid}" data-checkboxlineid="{$checkbox.orderlineid}" data-trigger="change" {if $checkbox.checked}checked="checked"{/if} />
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

                    <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="componentView|{$orderline.orderlineid}|{$checkbox.orderlineid}">

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

                {/if} {* end {if $stage=='qty' || ($stage=='payment' && $checkbox.checked == 1)} *}

            {/if} {* end {if $checkbox.showcomponentname==true} *}

        {/foreach} {* end {foreach from=$orderline.linefootercheckboxes item=checkbox} *}

        <!-- END LINEFOOTER CHECKBOXES -->

        {if isset($bTitleComponent) && ($stage == 'payment')}

            {assign var="bTitleComponent" value="true"}

        </div>

        {/if}


            </div> <!-- outerBoxPadding -->

    <!-- ITEM TOTAL SETCION -->

            <div class="itemSection outerBoxPadding">

    {if (($orderline.$lockqty != true) && ($stage != 'payment')) && empty($orderline.itemqtydropdown)}

                <div class="itemSectionLabelInput">

    {else}

                <div class="itemSectionLabel">

    {/if}

                    {#str_LabelItemQuantity#|replace:'^0':$orderline.orderlineid}
                </div>

                <div class="itemQuantityNumber">

    {if ($orderline.$lockqty==true) || ($stage == 'payment')}

                    <span class="quantityText">{$orderline.itemqty}</span>

    {else} {* else {if ($orderline.$lockqty==true) || ($stage == 'payment')} *}

                    <input id="hiddeqty_{$orderline.orderlineid}" type="hidden" class="hiddeqty" value="{$orderline.itemqty}"/>

        {if empty($orderline.itemqtydropdown)}

                    <input id="itemqty_{$orderline.orderlineid}" type="number" class="quantityInput" maxlength="8" value="{$orderline.itemqty}" data-decorator="fnUpdateOrderQty" data-lineid="{$orderline.orderlineid}" data-trigger="keyup" />
                    <img class="itemQuantityRefresh" src="{$brandroot}/images/icons/refresh.png" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" data-decorator="fnUpdateOrderQty" data-trigger="click" data-lineid="{$orderline.orderlineid}" />
                    <div class="clear"></div>

        {else} {* else {if empty($orderline.itemqtydropdown)} *}

                    <select id="itemqty_{$orderline.orderlineid}" class="" data-decorator="fnUpdateOrderQty" data-trigger="change" data-lineid="{$orderline.orderlineid}" >

                {foreach from=$orderline.itemqtydropdown item=qtyValue}

                        <option {if $qtyValue==$orderline.itemqty}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>

                {/foreach} {* end {foreach from=$orderline.itemqtydropdown item=qtyValue} *}

                    </select>

        {/if} {* end {if empty($orderline.itemqtydropdown)} *}

    {/if} {* end {if ($orderline.$lockqty==true) || ($stage == 'payment')} *}

                </div> <!-- itemQuantityNumber -->

                <div class="clear"></div>

            </div> <!-- itemSection outerBoxPadding -->

    {if $stage == 'qty'}

            <div class="itemSection outerBoxPadding">

                <div class="itemSectionLabel itemSectionTotalLabel">
                    {#str_LabelItemTotal#|replace:'^0':$orderline.orderlineid}
                </div>

                <div class="itemTotalNumber">
                    {$orderline.itemcompletetotal}
                </div>

                <div class="clear"></div>

            </div> <!-- itemSection outerBoxPadding -->
    {/if}
            <div class="clear"></div>

    <!--  END ITEM TOTAL SECTION -->

        </div> <!-- itemDetailcontentTextInside -->

        <div class="clear"></div>

   </div> <!-- contentTextInside -->

<!-- END ITEM SECTION -->

<!-- TOTAL SECTION -->

    <div class="lineTotal">

    <!-- PAYMENT  -->

    {if ($stage == 'payment')}

        <!-- NO VOUCHER  -->

        {if (!$orderline.itemvoucherapplied) || ($vouchersection=='SHIPPING') || (($vouchersection == 'TOTAL') && !(($differenttaxrates) && (!$specialvouchertype)))}

            <!-- DIFFERNETTAXRATES AND SHOWPRICES WITH TAX  -->

            {if ($differenttaxrates) && ($showpriceswithtax)}

                {if ($vouchersection == 'TOTAL') && ($specialvouchertype)}

                    {if ($orderline.itemdiscountvalueraw > 0)}

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{#str_LabelSubTotal#}</div>
            <div class="itemTotalNumber">{$orderline.itemcompletetotal}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{$orderline.itemdiscountname}</div>
            <div class="itemTotalNumber">{$orderline.itemdiscountvalue}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel">{#str_LabelItemTotal#|replace:'^0':$orderline.orderlineid}</div>
            <div class="itemTotalNumber">{$orderline.itemdiscountedvalue}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    {else} {* else {if ($orderline.itemdiscountvalueraw > 0)} *}

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{#str_LabelSubTotal#}</div>
            <div class="itemTotalNumber">{$orderline.itemcompletetotal}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    {/if} {* end {if ($orderline.itemdiscountvalueraw > 0)} *}

                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->

                    {if (($showzerotax) || ((!$showzerotax) && ($orderline.itemtaxtotalraw > 0)))}

        <div class="line-sub-total-small-bottom">
            {$orderline.includesitemtaxtext}
        </div>

                    {/if} {* end {if (($showzerotax) || ((!$showzerotax) && ($orderline.itemtaxtotalraw > 0)))} *}

                {else} {* else {if (($vouchersection == 'TOTAL') && ($specialvouchertype))} *}

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel">{#str_LabelItemTotal#|replace:'^0':$orderline.orderlineid}</div>
            <div class="itemTotalNumber">{$orderline.itemcompletetotal}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    <!-- SHOWTAXBREAKDOWN  -->

                    {if $showtaxbreakdown}

                        {if (($showzerotax) || ((!$showzerotax) && ($orderline.itemtaxtotalraw>0)))}

        <div class="line-sub-total-small-bottom">
            {$orderline.includesitemtaxtext}
        </div>

                        {/if} {* end {if (($showzerotax) || ((!$showzerotax) && ($orderline.itemtaxtotalraw>0)))} *}

                    {/if} {* end {if $showtaxbreakdown}*}

                {/if} {* end {if (($vouchersection == 'TOTAL') && ($specialvouchertype))} *}

            {/if} {* end {if ($differenttaxrates) && ($showpriceswithtax)} *}

            <!-- DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX  -->

            {if ($differenttaxrates) && (!$showpriceswithtax)}

                <!-- VALUE SET TOTAL VOUCHER  -->

                {if ($vouchersection=='TOTAL') && ($specialvouchertype)}

                    {if $orderline.itemdiscountvalueraw > 0}

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{#str_LabelSubTotal#}</div>
            <div class="itemTotalNumber">{$orderline.itemcompletetotal}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{$orderline.itemdiscountname}</div>
            <div class="itemTotalNumber">{$orderline.itemdiscountvalue}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    {/if} {* end {if $orderline.itemdiscountvalueraw > 0} *}

                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->

                    {if (($showzerotax) || ((!$showzerotax) && ($orderline.itemtaxtotalraw > 0)))}

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{#str_LabelSubTotal#}</div>
            <div class="itemTotalNumber">{$orderline.itemdiscountedvalue}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{$orderline.itemtaxratename} ({$orderline.itemtaxrate}%):</div>
            <div class="itemTotalNumber">{$orderline.itemtaxtotal}</div>
            <div class="clear"></div>
        </div> <!-- itemSection -->

                    {/if} {* end  {if (($showzerotax) || ((!$showzerotax) && ($orderline.itemtaxtotalraw > 0)))} *}

                {else} {* else {if ($vouchersection=='TOTAL') && ($specialvouchertype)} *}

                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->

                    {if (($showzerotax) || ((!$showzerotax) && ($orderline.itemtaxtotalraw>0)))}

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{#str_LabelSubTotal#}</div>
            <div class="itemTotalNumber">{$orderline.itemcompletetotal}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{$orderline.itemtaxratename} ({$orderline.itemtaxrate}%)</div>
            <div class="itemTotalNumber">{$orderline.itemtaxtotal}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    {/if} {* end {if (($showzerotax) || ((!$showzerotax) && ($orderline.itemtaxtotalraw>0)))} *}

                {/if} {* end {if ($vouchersection=='TOTAL') && ($specialvouchertype)} *}

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel">{#str_LabelItemTotal#|replace:'^0':$orderline.orderlineid}</div>
            <div class="itemTotalNumber">{$orderline.itemtotal}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

            {/if} {* {if ($differenttaxrates) && (!$showpriceswithtax)} *}

            <!-- NOT DIFFERNETTAXRATES  -->
            <!-- NOT DIFFERNET TAX-RATES  -->
            {if (!$differenttaxrates)}
                    {if ($orderline.itemdiscountvalueraw > 0)}
        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{#str_LabelSubTotal#}</div>
            <div class="itemTotalNumber">{$orderline.itemcompletetotal}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{$orderline.itemdiscountname}</div>
            <div class="itemTotalNumber">{$orderline.itemdiscountvalue}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->
                    {/if}
        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel">{#str_LabelItemTotal#|replace:'^0':$orderline.orderlineid}</div>
            <div class="itemTotalNumber">{$orderline.itemtotal}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

            {/if} {* end {if (!$differenttaxrates)} *}

        {else} {* else {if (!$orderline.itemvoucherapplied) || ($vouchersection=='SHIPPING') || (($vouchersection == 'TOTAL') && !(($differenttaxrates) && (!$specialvouchertype)))} *}

            <!-- PRODUCT VOUCHER  -->

            {if $vouchersection == 'PRODUCT'}

                {if $orderline.itemdiscountvalueraw > 0}

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{#str_LabelSubTotal#}</div>
            <div class="itemTotalNumber">{$orderline.itemcompletetotal}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{$orderline.itemdiscountname}</div>
            <div class="itemTotalNumber">{$orderline.itemdiscountvalue}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                {/if} {* end {if ($orderline.itemdiscountvalueraw > 0)} *}

                <!-- DIFFERNETTAXRATES AND SHOWPRICES WITH TAX -->

                {if (($differenttaxrates) && ($showpriceswithtax))}

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel">{#str_LabelItemTotal#|replace:'^0':$orderline.orderlineid}</div>
            <div class="itemTotalNumber">{$orderline.itemdiscountedvalue}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    <!-- SHOWTAXBREAKDOWN  -->

                    {if ($showtaxbreakdown)}

                        {if (($showzerotax) || ((!$showzerotax) && ($orderline.itemtaxtotalraw>0)))}

        <div class="line-sub-total-small-bottom">
            {$orderline.includesitemtaxtext}
        </div>

                        {/if} {* end {if (($showzerotax) || ((!$showzerotax) && ($orderline.itemtaxtotalraw>0)))} *}

                    {/if} {* end {if ($showtaxbreakdown)}*}

                {/if} {* end {if (($differenttaxrates) && ($showpriceswithtax))}*}

                <!-- DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX -->

                {if (($differenttaxrates) && (!$showpriceswithtax))}

                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 -->

                    {if (($showzerotax) || ((!$showzerotax) && ($orderline.itemtaxtotalraw>0)))}

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{#str_LabelSubTotal#}</div>
            <div class="itemTotalNumber">{$orderline.itemdiscountedvalue}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{$orderline.itemtaxratename} ({$orderline.itemtaxrate}%)</div>
            <div class="itemTotalNumber">{$orderline.itemtaxtotal}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    {/if} {* end {if (($showzerotax) || ((!$showzerotax) && ($orderline.itemtaxtotalraw>0)))} *}

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel">{#str_LabelItemTotal#|replace:'^0':$orderline.orderlineid}</div>
            <div class="itemTotalNumber">{$orderline.itemtotal}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                {/if} {* end {if (($differenttaxrates) && (!$showpriceswithtax))} *}

                <!-- NOT DIFFERNETTAXRATES -->
                <!-- NOT DIFFERNET TAX RATES -->

                {if (!$differenttaxrates)}


        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel">{#str_LabelItemTotal#|replace:'^0':$orderline.orderlineid}</div>
            <div class="itemTotalNumber">{$orderline.itemdiscountedvalue}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                {/if} {* end {if (!$differenttaxrates)} *}

            {/if} {* end {if $vouchersection == 'PRODUCT'} *}

            <!-- VALUE OFF TOTAL VOUCHER  -->

            {if (($vouchersection=='TOTAL') && (($differenttaxrates) && (!$specialvouchertype)))}

                {if $orderline.itemdiscountvalueraw > 0}

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{#str_LabelSubTotal#}</div>
            <div class="itemTotalNumber">{$orderline.itemcompletetotal}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{$orderline.itemdiscountname}</div>
            <div class="itemTotalNumber">{$orderline.itemdiscountvalue}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                {/if}{* end {if $orderline.itemdiscountvalueraw > 0} *}

                {if (!$showpriceswithtax)}

                    <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->

                    {if (($showzerotax) || ((!$showzerotax) && ($orderline.itemtaxtotalraw>0)))}

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{#str_LabelSubTotal#}</div>
            <div class="itemTotalNumber">{$orderline.itemdiscountedvalue}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel">{$orderline.itemtaxratename} ({$orderline.itemtaxrate}%)</div>
            <div class="itemTotalNumber">{$orderline.itemtaxtotal}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel">{#str_LabelItemTotal#|replace:'^0':$orderline.orderlineid}</div>
            <div class="itemTotalNumber">{$orderline.itemtotal}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    {else} {* else {if (($showzerotax) || ((!$showzerotax) && ($orderline.itemtaxtotalraw>0)))} *}

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel">{#str_LabelItemTotal#|replace:'^0':$orderline.orderlineid}</div>
            <div class="itemTotalNumber">{$orderline.itemdiscountedvalue}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                    {/if} {* end {if (($showzerotax) || ((!$showzerotax) && ($orderline.itemtaxtotalraw>0)))} *}

                {else} {* else {if (!$showpriceswithtax)} *}

        <div class="itemSection outerBoxPadding">
            <div class="itemSectionLabel itemSectionTotalLabel">{#str_LabelItemTotal#|replace:'^0':$orderline.orderlineid}</div>
            <div class="itemTotalNumber">{$orderline.itemtotal}</div>
            <div class="clear"></div>
        </div> <!-- itemSection outerBoxPadding -->

                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->

                    {if (($showzerotax) || ((!$showzerotax) && ($orderline.itemtaxtotalraw>0)))}

        <div class="line-sub-total-small-bottom">
            {$orderline.includesitemtaxtext}
        </div>

                    {/if} {* end {if (($showzerotax) || ((!$showzerotax) && ($orderline.itemtaxtotalraw>0)))}*}

                {/if} {* end {if (!$showpriceswithtax)} *}

            {/if} {* end {if (($vouchersection=='TOTAL') && (($differenttaxrates) && (!$specialvouchertype)))} *}

        {/if} {* end {if (!$orderline.itemvoucherapplied) || ($vouchersection=='SHIPPING') || (($vouchersection == 'TOTAL') && !(($differenttaxrates) && (!$specialvouchertype)))} *}

    {/if} {* end {if ($stage == 'payment')} *}

	</div> <!-- lineTotal -->

<!-- END TOTAL SECTION -->

{if $call_action == 'init'} {*  not needed html on ajax call*}

        </div> <!-- orderLine -->

    </div>

</div> <!-- itemOrderLine -->

    {/if} {* {if $call_action == 'init'} *}

{/if} {* {if $orderline.orderlineid != -1} *}