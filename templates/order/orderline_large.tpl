{if $orderline.parentorderitemid == 0}
    {assign var="isCompanion" value=false}
{else}
    {assign var="isCompanion" value=true}
{/if}

{if $orderline.orderlineid != -1}
    {assign var="multilinedesc" value="false"}
    {if $call_action == 'init'}
<div class="orderLineItem {if $isCompanion==true}companion-line-item{/if}">
	{if $isCompanion==true}
		<div class="companion-paper-clip-container">
		</div>
	{/if}
    <div class="item-list-title-bar">
	{if $isCompanion==true}
        <div class="title-current">{#str_TitleCompanionAlbum#}</div>
	{else}
        <div class="title-current">{#str_LabelHeaderItem#} {$orderline.orderlineid}</div>
	{/if}
	    <div class="title-quantity">{#str_LabelQuantity#}</div>
        <div class="title-price">{#str_LabelPrice#}</div>

        <div class="clear"></div>
    </div>
    <div class="content contentBloc">
        <div class="order-line {if $isCompanion==true}companion-order-line{/if}" id="ordertableobj_{$orderline.orderlineid}">
    {/if}
    <div class="contentTextInside">
		{if $isCompanion === true && $orderline.assetrequest !== ''}
        <div class="product-preview-wrap">
		    <img class="product-preview-image" src="{$orderline.assetrequest|escape}" alt="" />
        </div>
		{assign var="productHeaderClass" value=" companion-product-header"}
		{else if $isCompanion === true && $orderline.assetrequest === ''}
        <div class="product-preview-wrap">
		    <img class="product-preview-image" src="{$brandroot}/images/companion_placeholder.png" alt="" />
        </div>
		{assign var="productHeaderClass" value=" companion-product-header"}
		{elseif $isCompanion == false && $orderline.projectthumbnail !== ''}
        <div class="product-preview-wrap">
		    <img class="product-preview-image" src="{$orderline.projectthumbnail|escape}" data-asset="{$orderline.assetrequest|escape}" alt="" />
        </div>
		{assign var="productHeaderClass" value=""}
		{elseif $isCompanion == false && $orderline.assetrequest !== ''}
        <div class="product-preview-wrap">
		    <img class="product-preview-image" src="{$orderline.assetrequest|escape}" alt="" />
        </div>
		{assign var="productHeaderClass" value=""}
		{else}
		<div class="product-preview-wrap">
			<img class="product-preview-image" src="{$brandroot}/images/no_image-2x.jpg" alt="" />
		</div>
		{/if}
		<div class="product-header{$productHeaderClass}">
			<div class="product-bloc">
	
	{if $isCompanion==true}
                <h3 class="product-title">
                    {$orderline.itemproductname}
                </h3>
	{else}
                <h3 class="product-title">
                {#str_LabelProjectName#}: {$orderline.projectname}
                </h3>
                <div class="product-collection-title">
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
    {/if}
    {if $orderline.itemproductinfo != ""}
                <span class="product-info">{$orderline.itemproductinfo}</span>
    {/if}
            </div>
            <div class="quantity">
    {if ($orderline.$lockqty==true) || ($stage=='payment')}
                <span class="quantityText">{$orderline.itemqty}</span>
    {else}
        <input id="hiddeqty_{$orderline.orderlineid}" type="hidden" class="hiddeqty" value="{$orderline.itemqty}"/>
        {if empty($orderline.itemqtydropdown)}
                <input id="itemqty_{$orderline.orderlineid}" type="text" class="quantity" data-decorator="fnUpdateOrderQty" maxlength="8" value="{$orderline.itemqty}" data-lineid="{$orderline.orderlineid}" />
                <img class="refresh" data-decorator="fnUpdateOrderQty" src="{$brandroot}/images/icons/refresh.png" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" data-lineid="{$orderline.orderlineid}" data-trigger="click" />
        {else}
                <select id="itemqty_{$orderline.orderlineid}" data-decorator="fnUpdateOrderQty" data-trigger="change" data-lineid="{$orderline.orderlineid}">
                {foreach from=$orderline.itemqtydropdown item=qtyValue}
                    <option {if $qtyValue==$orderline.itemqty}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>
                {/foreach}
                </select>
        {/if}
    {/if}
            </div>
            <div class="component-price priceBig">
                {if $orderline.itemshowproductsell == 1}
                	{$orderline.itemproducttotalsell}
                {/if}
            </div>
            <div class="clear"></div>

    {if $orderline.displayassets == true}
		{foreach from=$orderline.itemexternalassets item=asset }
				<div class="product-bloc">
					{$asset.pagename}: {$asset.name}
				</div>
				<div class="component-price-asset">
					{$asset.charge}
				</div>
				<div class="clear"></div>
		{/foreach}
	{/if}

    {foreach from=$orderline.itempictures item=sizegroup }
				<div class="photoPrintGroup">
					<div class="photoPrintGroupName" >
						{$sizegroup.groupdisplayname}
					</div>

					<div class="photoPrintGroupCount">
						{if $sizegroup.picturecount > 1}
							{#str_LabelPrints#|replace:'^0':$sizegroup.picturecount}
						{else}
							{#str_LabelPrint#|replace:'^0':$sizegroup.picturecount}
						{/if}
					</div>

					<div class="photoPrintTotalSell">
						{$sizegroup.formatedgrouptotalsell}
					</div>
					<div class="clear"></div>
				</div>
    {/foreach}

    {if $orderline.calendarcustomisations|@sizeof > 0}

            <div class = "calblock" >

    {foreach from=$orderline.calendarcustomisations item=calendarcustomisations }

                    <div class = "calcomponentrow" >
                        <div class="calendarcomp-bloc">
                            {$calendarcustomisations.name}
                        </div>

                        <div class="calendarcomp_price">
                            {$calendarcustomisations.formattedtotalsell}
                        </div>
                    </div>
                    <div class="clear"></div>
    {/foreach}

            </div>

    {/if}


		</div>
		<div class="clear"></div>
   </div>

	{if $orderline.aicomponent|@sizeof > 0}
        <div class="customisationOption smart-design-component">
            <div class="componentbloc">
                <div class="section-title-header">
                    <span class="section-category-name">{$orderline.aicomponent.name}</span>
                </div>
                <div class="componentrow">
                    <img class="component-preview" src="{$orderline.aicomponent.previewsrc|escape}"/>
                    <div class="componentSectionTitle ">
                        <div class="component-name" >
                            <div class="componentContentText">
                                {$orderline.aicomponent.componentinfo}
                            </div>
                        </div>
                    </div>
                    <div class="component-price">
                        {$orderline.aicomponent.formattedtotalsell}
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
	{/if}

{/if}

<!-- checkboxes start -->
{if $orderline.orderlineid != -1}
    {if $orderline.checkboxes|@sizeof > 0}
        {foreach from=$orderline.checkboxes item=checkbox}
            {if $checkbox.showcomponentname==true}
                {if !isset($bTitleComponent) && (($stage=='qty') || ($stage=='payment' && $checkbox.checked == 1))}
    <div class="customisationsHeader">
	{if $isCompanion==true}
		{#str_LabelCompanionAlbumOptions#}
	{else}
        {#str_LabelProductOptions#}
	{/if}
        <span class="linkToggle">
                    {if $stage=='payment'}
            <span id="link_{$orderline.orderlineid}" class="hide-product-options-link" data-decorator="fnToggleGeneric" data-lineid="{$orderline.orderlineid}" data-idelm="contentCustomise_{$orderline.orderlineid}" data-colour="grey">{#str_OrderShow#}</span>
                    {else}
            <span id="link_{$orderline.orderlineid}" class="hide-product-options-link" data-decorator="fnToggleGeneric" data-lineid="{$orderline.orderlineid}" data-idelm="contentCustomise_{$orderline.orderlineid}" data-colour="grey">{#str_OrderHide#}</span>
                    {/if}
        </span>
        <div class="clear"></div>
		{assign var="bTitleComponent" value="true"}
    </div>

    <div id="contentCustomise_{$orderline.orderlineid}" {if $stage=='payment'}style="display:none;"{/if}>
                {/if}
                {if (($stage=='payment' && $checkbox.checked == 1) || $stage=='qty')}
		<div class="customisationOption {if $isCompanion==true}companion-customisationOption{/if}">
			<div id="componentrow_{$checkbox.orderlineid}" class="checkbox">
					<div class="section-title-header">
					{if ($checkbox.itemcomponentcategoryname != '')}
						<span class="section-category-name">{$checkbox.itemcomponentcategoryname}:</span> <span class="section-category-prompt">{$checkbox.itemcomponentprompt}</span>
					{/if}
					</div>
				<div class="checkboxBloc">
						{if $checkbox.haspreview > 0}
					<img class="component-preview" src="{$checkbox.componentpreviewsrc|escape}" alt=""/>
					<div class="component-name {if $isCompanion==true}companion-component-name{/if}">
						{else}
					<div class="component-name-long {if $isCompanion==true}companion-component-name-long{/if}">
						{/if}
						<div class="componentContentText">
							<div class="section-title">
							{if ($checkbox.itemcomponentname != '')}
								<span class="section-category-name">{$checkbox.itemcomponentname}</span>
							{/if}
							</div>
							{if !empty($checkbox.itemcomponentinfo)}
							<div class="checkbox-info">
								{$checkbox.itemcomponentinfo}
							</div>
								{assign var="multilinedesc" value="true"}
							{/if}
							{if ! empty($checkbox.itemcomponentmoreinfolinkurl)}
								<div class="subsection-moreinfo">
									<a href="{$checkbox.itemcomponentmoreinfolinkurl}" target="_blank">{$checkbox.itemcomponentmoreinfolinktext}</a>
								</div>
								{assign var="multilinedesc" value="true"}
							{/if}
							{if !empty($checkbox.itemcomponentpriceinfo)}
							<div class="checkbox-info">
								{$checkbox.itemcomponentpriceinfo}
							</div>
								{assign var="multilinedesc" value="true"}
							{/if}
							{if $stage=='qty'}
							({$checkbox.totalsell})
							{/if}
							<div class="clear"></div>
						</div>
							{if $stage=='qty'}
								{foreach from=$checkbox.itemcomponentbuttons item=button}
						<div class="contentBtn btnRight {if $multilinedesc == "true"}paddingCenter{/if}" data-decorator="{$button.action}" data-orderlineid="{$orderline.orderlineid}" data-sectionlineid="{$checkbox.orderlineid}">
							<div class="btn-white-left" ></div>
							<div class="btn-white-middle {$button.class}">{$button.label}</div>
							<div class="btn-white-right"></div>
						</div>
						<div class="clear"></div>
								{/foreach}
							{/if}
					</div>
						{if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8) && $checkbox.checked == 1}
					<div class="quantity {if $multilinedesc == "true"}paddingCenter{/if}">
							{if $stage=='payment'}
						<span class="quantityText {if $isCompanion==true}companion-quantityText{/if}">{$checkbox.quantity}</span>
							{else}
						<input id="hiddeqty_{$checkbox.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$checkbox.quantity}"/>
								{if empty($checkbox.itemqtydropdown)}
						<input id="itemqty_{$checkbox.orderlineid}" type="text" class="quantity" data-decorator="fnUpdateComponentQty" data-trigger="keypress" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" maxlength="8" value="{$checkbox.quantity}" />
						<img class="refresh" data-decorator="fnUpdateComponentQty" src="{$brandroot}/images/icons/refresh.png" data-trigger="click" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" />
								{else}
						<select id="itemqty_{$checkbox.orderlineid}" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}">
									{foreach from=$checkbox.itemqtydropdown item=qtyValue}
							<option {if $qtyValue==$checkbox.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>
									{/foreach}
						</select>
								{/if}
							{/if}
					</div>
						{/if}
						{if $checkbox.checked || $checkbox.totalsell == #str_LabelNotAvailable#}
					<div class="component-price {if $multilinedesc == "true"}paddingCenter{/if}">
						{$checkbox.totalsell}
					</div>
						{/if}
						{if $multilinedesc}
							{assign var="multilinedesc" value="false"}
						{/if}
					<div class="clear"></div>
				</div>
						{if ($checkbox.metadatahtml) && ($checkbox.checked)}
				<div id="metadatarow_{$checkbox.orderlineid}" class="component-metadata">
					{$checkbox.metadatahtml}
				</div>
						{/if}
			</div>
		</div>
        <div class="clear"></div>
                {/if}
            {/if}
        {/foreach}
    {/if}
{/if}
<!-- checkboxes end -->

<!-- sections start -->
{if $orderline.sections|@sizeof > 0 && !isset($bTitleComponent)}
    <div class="customisationsHeader {if $isCompanion==true}companion-customisations{/if}">
	{if $isCompanion==true}
		{#str_LabelCompanionAlbumOptions#}
	{else}
        {#str_LabelProductOptions#}
	{/if}
		<span class="linkToggle">
    {if $stage=='payment'}
			<span id="link_{$orderline.orderlineid}" class="hide-product-options-link" data-decorator="fnToggleGeneric" data-lineid="{$orderline.orderlineid}" data-idelm="contentCustomise_{$orderline.orderlineid}" data-colour="grey">{#str_OrderShow#}</span>
    {else}
			<span id="link_{$orderline.orderlineid}" class="hide-product-options-link" data-decorator="fnToggleGeneric" data-lineid="{$orderline.orderlineid}" data-idelm="contentCustomise_{$orderline.orderlineid}" data-colour="grey">{#str_OrderHide#}</span>
    {/if}
		</span>
        <div class="clear"></div>
    </div>
    <div id="contentCustomise_{$orderline.orderlineid}" {if $stage=='payment'}style="display:none;"{/if}>
	{assign var="bTitleComponent" value="true"}
{/if}
    {foreach from=$orderline.sections item=section} {* sections *}
	<div class="customisationOption {if $isCompanion==true}companion-customisationOption{/if}">
		<div id="componentContent_{$section.orderlineid}">
			{if $section.showcomponentname==true}
			<div id="componentrow_{$section.orderlineid}" class="componentbloc" >
				<div class="section-title-header">
				{if ($section.sectionlabel != '')}
					<span class="section-category-name">{$section.sectionlabel}:</span> <span class="section-category-prompt">{$section.prompt}</span>
				{/if}
				</div>
				<div class="componentrow">
				{if $section.haspreview > 0}
					<img class="component-preview" src="{$section.componentpreviewsrc|escape}" alt=""/>
					<div class="componentSectionTitle {if $isCompanion==true}companion-componentSectionTitle{/if}">
				{else}
					<div class="componentSectionTitleLong {if $isCompanion==true}companion-componentSectionTitleLong{/if}">
				{/if}
						<div class="componentContentText">
							<div class="section-title">{$section.itemcomponentname}</div>
				{if $section.haspreview > 0}
					{if !empty($section.itemcomponentinfo)}
							<div class="section-info">
								{$section.itemcomponentinfo}
							</div>
							{assign var="multilinedesc" value="true"}
					{/if}
					{if ! empty($section.itemcomponentmoreinfolinkurl)}
							<div class="section-moreinfo">
								<a href="{$section.itemcomponentmoreinfolinkurl}" target="_blank">{$section.itemcomponentmoreinfolinktext}</a>
							</div>
							{assign var="multilinedesc" value="true"}
					{/if}
					{if !empty($section.itemcomponentpriceinfo)}
							<div class="section-info">
								{$section.itemcomponentpriceinfo}
							</div>
							{assign var="multilinedesc" value="true"}
					{/if}
				{else}
					{if !empty($section.itemcomponentinfo)}
							<div class="section-info-long">
								{$section.itemcomponentinfo}
							</div>
							{assign var="multilinedesc" value="true"}
					{/if}
					{if ! empty($section.itemcomponentmoreinfolinkurl)}
							<div class="section-moreinfo-long">
								<a href="{$section.itemcomponentmoreinfolinkurl}" target="_blank">{$section.itemcomponentmoreinfolinktext}</a>
							</div>
							{assign var="multilinedesc" value="true"}
					{/if}
					{if !empty($section.itemcomponentpriceinfo)}
							<div class="clear"></div>
							<div class="section-info-long">
								{$section.itemcomponentpriceinfo}
							</div>
							{assign var="multilinedesc" value="true"}
					{/if}
				{/if}
							<div class="clear"></div>
						</div>
				{if $stage=='qty'}
					{foreach from=$section.itemcomponentbuttons item=button}
						<div class="contentBtn btnRight {if $multilinedesc == "true"}paddingCenter{/if}" data-decorator="{$button.action}" data-orderlineid="{$orderline.orderlineid}" data-sectionlineid="{$section.orderlineid}">
							<div class="btn-white-left" ></div>
							<div class="btn-white-middle {$button.class}">{$button.label}</div>
							<div class="btn-white-right"></div>
						</div>
						<div class="clear"></div>
					{/foreach}
				{/if}
				</div>
				{if $section.pricingmodel == 7 || $section.pricingmodel == 8}
					<div class="quantity {if $multilinedesc == "true"}paddingCenter{/if}">
					{if $stage=='payment'}
						<span class="quantityText {if $isCompanion==true}companion-quantityText{/if}">{$section.quantity}</span>
					{else}
						<input id="hiddeqty_{$section.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$section.quantity}"/>
						{if empty($section.itemqtydropdown)}
						<input id="itemqty_{$section.orderlineid}" type="text" class="quantity" data-decorator="fnUpdateComponentQty" data-trigger="keypress" data-lineid="{$section.orderlineid}" data-itemqty="{$section.itemqty}" maxlength="8" value="{$section.quantity}" />
						<img class="refresh" data-decorator="fnUpdateComponentQty" src="{$brandroot}/images/icons/refresh.png" data-trigger="click" data-lineid="{$section.orderlineid}" data-itemqty="{$section.itemqty}" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" />
						{else}
						<select id="itemqty_{$section.orderlineid}" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="{$section.orderlineid}" data-itemqty="{$section.itemqty}">
							{foreach from=$section.itemqtydropdown item=qtyValue}
							<option {if $qtyValue==$section.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>
							{/foreach}
						</select>
						{/if}
					{/if}
					</div>
				{/if}
					<div class="component-price {if $multilinedesc == "true"}paddingCenter{/if}">
						{$section.totalsell}
					</div>
					{if $multilinedesc}
						{assign var="multilinedesc" value="false"}
					{/if}
					<div class="clear"></div>
				</div>
					{if $section.metadatahtml}
				<div class="component-metadata">
					{$section.metadatahtml}
				</div>
				{/if}
			</div>
			{/if}
			<!-- sub-sections of component start -->
			{foreach from=$section.subsections item=subsection} {* subsections of a section *}
				{if $subsection.showcomponentname==true}
			<div id="componentrow_{$subsection.orderlineid}" class="subsection">
				<div class="subsectionBloc">
					<div class="section-title-header">
					{if ($subsection.sectionlabel != '')}
						<span class="section-category-name">{$subsection.sectionlabel}:</span> <span class="section-category-prompt">{$subsection.prompt}</span>
					{/if}
					</div>
					{if $subsection.haspreview > 0}
					<img class="component-preview" src="{$subsection.componentpreviewsrc|escape}" alt=""/>
					<div class="componentSubSectionTitle {if $isCompanion==true}companion-componentSubSectionTitle{/if}">
					{else}
					<div class="componentSubSectionTitleLong {if $isCompanion==true}companion-componentSubSectionTitleLong{/if}">
					{/if}
						<div class="componentContentText">
							<div class="section-title">{$subsection.itemcomponentname}</div>
					<!-- START add-edit-change-remove links -->

					{if $subsection.haspreview > 0}
						{if !empty($subsection.itemcomponentinfo)}
							<div class="subsection-info">
							{$subsection.itemcomponentinfo}
							</div>
							{assign var="multilinedesc" value="true"}
						{/if}
						{if ! empty($subsection.itemcomponentmoreinfolinkurl)}
							<div class="subsection-moreinfo">
								<a href="{$subsection.itemcomponentmoreinfolinkurl}" target="_blank">{$subsection.itemcomponentmoreinfolinktext}</a>
							</div>
							{assign var="multilinedesc" value="true"}
						{/if}
						{if !empty($subsection.itemcomponentpriceinfo)}
							<div class="subsection-info">
							{$subsection.itemcomponentpriceinfo}
							</div>
							{assign var="multilinedesc" value="true"}
						{/if}
					{else}
						{if !empty($subsection.itemcomponentinfo)}
							<div class="subsection-info-long">
							{$subsection.itemcomponentinfo}
							</div>
							{assign var="multilinedesc" value="true"}
						{/if}
						{if ! empty($subsection.itemcomponentmoreinfolinkurl)}
							<div class="subsection-moreinfo-long">
								<a href="{$subsection.itemcomponentmoreinfolinkurl}" target="_blank">{$subsection.itemcomponentmoreinfolinktext}</a>
							</div>
						{assign var="multilinedesc" value="true"}
						{/if}
						{if !empty($subsection.itemcomponentpriceinfo)}
							<div class="subsection-info-long">
						{$subsection.itemcomponentpriceinfo}
							</div>
							 {assign var="multilinedesc" value="true"}
						{/if}
					{/if}
					<div class="clear"></div>
					</div>
					{if $stage=='qty'}
						{foreach from=$subsection.itemcomponentbuttons item=button}
						<div class="contentBtn btnRight {if $multilinedesc == "true"}paddingCenter{/if}" data-decorator="{$button.action}" data-orderlineid="{$orderline.orderlineid}" data-sectionlineid="{$subsection.orderlineid}">
							<div class="btn-white-left" ></div>
							<div class="btn-white-middle {$button.class}">{$button.label}</div>
							<div class="btn-white-right"></div>
						</div>
						<div class="clear"></div>
						{/foreach}
					{/if}
					<!-- END add-edit-change-remove links -->
					</div>

					{if $subsection.pricingmodel == 7 || $subsection.pricingmodel == 8}
					<div class="quantity {if $multilinedesc == "true"}paddingCenter{/if}">
						{if ($stage=='payment')}
						<span class="quantityText">
							{$subsection.quantity}
						</span>
						{else}
						<input id="hiddeqty_{$subsection.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$subsection.quantity}"/>
							{if empty($subsection.itemqtydropdown)}
						<input id="itemqty_{$subsection.orderlineid}" type="text" class="quantity" data-decorator="fnUpdateComponentQty" data-trigger="keypress" data-lineid="{$subsection.orderlineid}" data-itemqty="{$subsection.itemqty}" maxlength="8" value="{$subsection.quantity}" />
						<img class="refresh" data-decorator="fnUpdateComponentQty" src="{$brandroot}/images/icons/refresh.png" data-trigger="click" data-lineid="{$subsection.orderlineid}" data-itemqty="{$subsection.itemqty}" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" />
							{else}
						<select id="itemqty_{$subsection.orderlineid}" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="{$subsection.orderlineid}" data-itemqty="{$subsection.itemqty}">
								{foreach from=$subsection.itemqtydropdown item=qtyValue}
							<option {if $qtyValue==$subsection.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>
								{/foreach}
						</select>
							{/if}
						{/if}
					</div>
					{/if}
					<div class="component-price {if $multilinedesc == "true"}paddingCenter{/if}">
						{$subsection.totalsell}
					</div>
					{if $multilinedesc}
						{assign var="multilinedesc" value="false"}
					{/if}
					<div class="clear"></div>
				</div>
					{if $subsection.metadatahtml}
				<div class="subcomponent-metadata">
					{$subsection.metadatahtml}
				</div>
					{/if}
			</div>
				{/if}
			{/foreach} {* subsections of a section *}
			 <!-- sub-sections of component end -->

			<!-- checkboxes inside component start -->
			{foreach from=$section.checkboxes item=checkbox} {* checkboxes of a section *}
				{if $checkbox.showcomponentname==true}
					{if ($stage=='qty') || ($stage=='payment' && $checkbox.checked == 1)}
		<div ><!--class="customisationOption {if $isCompanion==true}companion-customisationOption{/if}"> -->
			<div id="componentrow_{$checkbox.orderlineid}" class="subsection">
				<div class="subcheckboxBloc">
					<div class="section-title-header">
						{if ($checkbox.itemcomponentcategoryname != '')}
						<span class="section-category-name">{$checkbox.itemcomponentcategoryname}:</span> <span class="section-category-prompt">{$checkbox.itemcomponentprompt}</span>
						{/if}
					</div>
						{if $checkbox.haspreview > 0}
					<img class="component-preview" src="{$checkbox.componentpreviewsrc|escape}" alt=""/>
					<div class="componentSubSectionTitle {if $isCompanion==true}companion-componentSubSectionTitle{/if}">
						{else}
					<div class="componentSubSectionTitleLong {if $isCompanion==true}companion-componentSubSectionTitleLong{/if}">
						{/if}
						 <div class="componentContentText">
							<div class="section-title">{$checkbox.itemcomponentname}</div>
						{if !empty($checkbox.itemcomponentinfo)}
						<div class="checkbox-info">
							{$checkbox.itemcomponentinfo}
						</div>
							{assign var="multilinedesc" value="true"}
						{/if}
						{if ! empty($checkbox.itemcomponentmoreinfolinkurl)}
							<div class="checkbox-moreinfo">
								<a href="{$checkbox.itemcomponentmoreinfolinkurl}" target="_blank">{$checkbox.itemcomponentmoreinfolinktext}</a>
							</div>
							{assign var="multilinedesc" value="true"}
						{/if}
						{if !empty($checkbox.itemcomponentpriceinfo)}
						<div class="checkbox-info">
							{$checkbox.itemcomponentpriceinfo}
						</div>
							{assign var="multilinedesc" value="true"}
						{/if}
						{if $stage=='qty' && $checkbox.totalsell != #str_LabelNotAvailable#}
							({$checkbox.totalsell})
						{/if}
							<div class="clear"></div>
						</div>
						{if $stage=='qty' && $checkbox.totalsell != #str_LabelNotAvailable#}
							{foreach from=$checkbox.itemcomponentbuttons item=button}
							<div class="contentBtn btnRight {if $multilinedesc == "true"}paddingCenter{/if}" data-decorator="{$button.action}" data-orderlineid="{$orderline.orderlineid}" data-sectionlineid="{$checkbox.orderlineid}">
								<div class="btn-white-left" ></div>
								<div class="btn-white-middle {$button.class}">{$button.label}</div>
								<div class="btn-white-right"></div>
							</div>
							<div class="clear"></div>
							{/foreach}
						{/if}
					</div>
						{if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8) && $checkbox.checked == 1}
					<div class="quantity {if $multilinedesc == "true"}paddingCenter{/if}">
							{if $stage=='payment'}
						<span class="quantityText {if $isCompanion==true}companion-quantityText{/if}">{$checkbox.quantity}</span>
							{else}
						<input id="hiddeqty_{$checkbox.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$checkbox.quantity}"/>
								{if empty($checkbox.itemqtydropdown)}
						<input id="itemqty_{$checkbox.orderlineid}" type="text" class="quantity" data-decorator="fnUpdateComponentQty" data-trigger="keypress" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" maxlength="8" value="{$checkbox.quantity}" />
						<img class="refresh" data-decorator="fnUpdateComponentQty" src="{$brandroot}/images/icons/refresh.png" data-trigger="click" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" />
								{else}
						<select id="itemqty_{$checkbox.orderlineid}" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}">
									{foreach from=$checkbox.itemqtydropdown item=qtyValue}
							<option {if $qtyValue==$checkbox.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>
									{/foreach}
						</select>
								{/if}
							{/if}
					</div>
						{/if}
						{if $checkbox.checked || $checkbox.totalsell == #str_LabelNotAvailable#}
					<div class="component-price {if $multilinedesc == "true"}paddingCenter{/if}">
						{$checkbox.totalsell}
					</div>
						{/if}
						{if $multilinedesc}
							{assign var="multilinedesc" value="false"}
						{/if}
					<div class="clear"></div>
				</div>
						{if (($checkbox.metadatahtml) && ($checkbox.checked))}
				<div id="metadatarow_{$checkbox.orderlineid}" class="subcomponent-metadata{if not $checkbox.checked} invisible{/if}">
					{$checkbox.metadatahtml}
				</div>
						{/if}
			</div>
					</div>

			<div class="clear"></div>
					{/if}
				{/if}
			{/foreach} {* checkboxes of a section *}
			<!-- checkboxes inside component end -->
		</div>
    </div>
    {/foreach} {* sections *}
	<!-- sections end -->

	<!-- linefooter sections start -->
    {if $orderline.orderlineid != -1}
        {if $orderline.linefootersections|@sizeof > 0}

            {if !isset($bTitleComponent)}
 <div class="customisationsHeader {if $isCompanion==true}companion-customisations{/if}">
	{if $isCompanion==true}
		{#str_LabelCompanionAlbumOptions#}
	{else}
        {#str_LabelProductOptions#}
	{/if}
		<span class="linkToggle">
    {if $stage=='payment'}
			<span id="link_{$orderline.orderlineid}" class="hide-product-options-link" data-decorator="fnToggleGeneric" data-lineid="{$orderline.orderlineid}" data-idelm="contentCustomise_{$orderline.orderlineid}" data-colour="grey">{#str_OrderShow#}</span>
    {else}
			<span id="link_{$orderline.orderlineid}" class="hide-product-options-link" data-decorator="fnToggleGeneric" data-lineid="{$orderline.orderlineid}" data-idelm="contentCustomise_{$orderline.orderlineid}" data-colour="grey">{#str_OrderHide#}</span>
    {/if}
		</span>
        <div class="clear"></div>
    </div>

    <div id="contentCustomise_{$orderline.orderlineid}" {if $stage=='payment'}style="display:none;"{/if}>
				{assign var="bTitleComponent" value="true"}
            {/if}

            {foreach from=$orderline.linefootersections item=section} {* linefooter sections *}
		<div class="customisationOption {if $isCompanion==true}companion-customisationOption{/if}">
                {if $section.showcomponentname==true}
    <div id="componentrow_{$section.orderlineid}" class="componentbloc">
		<div class="section-title-header">
					{if ($section.sectionlabel != '')}
			<span class="section-category-name">{$section.sectionlabel}:</span> <span class="section-category-prompt">{$section.prompt}</span>
					{/if}
		</div>
		<div class="componentrow" >
                    {if $section.haspreview > 0}
            <img class="component-preview" src="{$section.componentpreviewsrc|escape}" alt=""/>
            <div class="componentSectionTitle {if $isCompanion==true}companion-componentSectionTitle{/if}">
                    {else}
            <div class="componentSectionTitleLong {if $isCompanion==true}companion-componentSectionTitleLong{/if}">
                    {/if}
                <div class="componentContentText">
                    <div class="section-title">
                        {$section.itemcomponentname}
                    </div>
                    {if $section.haspreview > 0}
                        {if !empty($section.itemcomponentinfo)}
                    <div class="section-info">
                        {$section.itemcomponentinfo}
                    </div>
                            {assign var="multilinedesc" value="true"}
                        {/if}
						{if ! empty($section.itemcomponentmoreinfolinkurl)}
					<div class="section-moreinfo">
						<a href="{$section.itemcomponentmoreinfolinkurl}" target="_blank">{$section.itemcomponentmoreinfolinktext}</a>
					</div>
							{assign var="multilinedesc" value="true"}
						{/if}
                        {if !empty($section.itemcomponentpriceinfo)}
                    <div class="section-info">
                        {$section.itemcomponentpriceinfo}
                    </div>
                            {assign var="multilinedesc" value="true"}
                        {/if}
                    {else}
                        {if !empty($section.itemcomponentinfo)}
                    <div class="section-info-long">
                        {$section.itemcomponentinfo}
                    </div>
                            {assign var="multilinedesc" value="true"}
                        {/if}
						{if ! empty($section.itemcomponentmoreinfolinkurl)}
					<div class="section-moreinfo-long">
						<a href="{$section.itemcomponentmoreinfolinkurl}" target="_blank">{$section.itemcomponentmoreinfolinktext}</a>
					</div>
						{assign var="multilinedesc" value="true"}
						{/if}
                        {if !empty($section.itemcomponentpriceinfo)}
                    <div class="section-info-long">
                        {$section.itemcomponentpriceinfo}
                    </div>
                            {assign var="multilinedesc" value="true"}
                        {/if}
                    {/if}
                    <div class="clear"></div>
                </div>
                    <!-- START add-edit-change-remove links -->
                    {if $stage=='qty'}
                        {foreach from=$section.itemcomponentbuttons item=button}
                <div class="contentBtn btnRight {if $multilinedesc == "true"}paddingCenter{/if}" data-decorator="{$button.action}" data-orderlineid="{$orderline.orderlineid}" data-sectionlineid="{$section.orderlineid}">
                    <div class="btn-white-left" ></div>
                    <div class="btn-white-middle {$button.class}">{$button.label}</div>
                    <div class="btn-white-right"></div>
                </div>
                <div class="clear"></div>
                        {/foreach}
                    {/if}
					<!-- END add-edit-change-remove links -->
            </div>
                    {if $section.pricingmodel == 7 || $section.pricingmodel == 8}
            <div class="quantity {if $multilinedesc == "true"}paddingCenter{/if}">
                        {if ($stage=='payment')}
                <span class="quantityText {if $isCompanion==true}companion-quantityText{/if}">{$section.quantity}</span>
                        {else}
                <input id="hiddeqty_{$section.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$section.quantity}"/>
                            {if empty($section.itemqtydropdown)}
                <input id="itemqty_{$section.orderlineid}" type="text" class="quantity" data-decorator="fnUpdateComponentQty" data-trigger="keypress" data-lineid="{$section.orderlineid}" data-itemqty="{$section.itemqty}" maxlength="8" value="{$section.quantity}" />
                <img class="refresh" data-decorator="fnUpdateComponentQty" src="{$brandroot}/images/icons/refresh.png" data-trigger="click" data-lineid="{$section.orderlineid}" data-itemqty="{$section.itemqty}" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" />
                            {else}
                <select id="itemqty_{$section.orderlineid}" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="{$section.orderlineid}" data-itemqty="{$section.itemqty}">
                                {foreach from=$section.itemqtydropdown item=qtyValue}
                    <option {if $qtyValue==$section.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>
                                {/foreach}
                </select>
                            {/if}

                        {/if}
                <div class="clear"></div>
            </div>
                    {/if}
            <div class="component-price {if $multilinedesc == "true"}paddingCenter{/if}">
                {$section.totalsell}
            </div>
            {if $multilinedesc}
                {assign var="multilinedesc" value="false"}
            {/if}
            <div class="clear"></div>
        </div>
                    {if $section.metadatahtml}
        <div id="metadatarow_{$section.orderlineid}" class="component-metadata">
            {$section.metadatahtml}
        </div>
                    {/if}
    </div>
                {/if}
                <!-- sub-sections of linefooter component start -->

                {foreach from=$section.subsections item=subsection} {* subsections of a section *}
                    {if $subsection.showcomponentname==true}
    <div id="componentrow_{$subsection.orderlineid}" class="subsection">
        <div class="subsectionBloc">
			<div class="section-title-header">
						{if ($section.sectionlabel != '')}
				<span class="section-category-name">{$subsection.sectionlabel}:</span> <span class="section-category-prompt">{$subsection.prompt}</span>
						{/if}
			</div>
						{if $subsection.haspreview > 0}
                <img class="component-preview" src="{$subsection.componentpreviewsrc|escape}" alt=""/>
                <div class="componentSubSectionTitle {if $isCompanion==true}companion-componentSubSectionTitle{/if}">
                        {else}
                <div class="componentSubSectionTitleLong {if $isCompanion==true}companion-componentSubSectionTitleLong{/if}">
                        {/if}
                    <div class="componentContentText">
                        <div class="section-title">{$subsection.itemcomponentname}</div>
                        {if $subsection.haspreview > 0}
                            {if !empty($subsection.itemcomponentinfo)}
                        <div class="subsection-info">
                            {$subsection.itemcomponentinfo}
                        </div>
                                {assign var="multilinedesc" value="true"}
                            {/if}
							{if ! empty($subsection.itemcomponentmoreinfolinkurl)}
						<div class="subsection-moreinfo">
							<a href="{$subsection.itemcomponentmoreinfolinkurl}" target="_blank">{$subsection.itemcomponentmoreinfolinktext}</a>
						</div>
								{assign var="multilinedesc" value="true"}
							{/if}
                            {if !empty($subsection.itemcomponentpriceinfo)}
                        <div class="subsection-info">
                            {$subsection.itemcomponentpriceinfo}
                        </div>
                                {assign var="multilinedesc" value="true"}
                            {/if}
                        {else}
                            {if !empty($subsection.itemcomponentinfo)}
                        <div class="subsection-info-long">
                            {$subsection.itemcomponentinfo}
                        </div>
                                {assign var="multilinedesc" value="true"}
                            {/if}
                            {if ! empty($subsection.itemcomponentmoreinfolinkurl)}
                        <div class="subsection-moreinfo-long">
                            <a href="{$subsection.itemcomponentmoreinfolinkurl}" target="_blank">{$subsection.itemcomponentmoreinfolinktext}</a>
                        </div>
                              {assign var="multilinedesc" value="true"}
                            {/if}
                            {if !empty($subsection.itemcomponentpriceinfo)}
                        <div class="subsection-info-long">
                            {$subsection.itemcomponentpriceinfo}
                        </div>
                                {assign var="multilinedesc" value="true"}
                            {/if}
                        {/if}
                        <div class="clear"></div>
                    </div>
                            <!-- START add-edit-change-remove links -->
                            {if $stage=='qty'}
                                {foreach from=$subsection.itemcomponentbuttons item=button}
                    <div class="contentBtn btnRight {if $multilinedesc == "true"}paddingCenter{/if}" data-decorator="{$button.action}" data-orderlineid="{$orderline.orderlineid}" data-sectionlineid="{$subsection.orderlineid}">
                        <div class="btn-white-left" ></div>
                        <div class="btn-white-middle {$button.class}">{$button.label}</div>
                        <div class="btn-white-right"></div>
                    </div>
                    <div class="clear"></div>
                                {/foreach}
                            {/if}
                            <!-- END add-edit-change-remove links -->
                </div>
                        {if $subsection.pricingmodel == 7 || $subsection.pricingmodel == 8}
                <div class="quantity {if $multilinedesc == "true"}paddingCenter{/if}">
                            {if ($stage=='payment')}
                    <span class="quantityText {if $isCompanion==true}companion-quantityText{/if}">{$subsection.quantity}</span>
                            {else}
                    <input id="hiddeqty_{$subsection.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$subsection.quantity}"/>
                                {if empty($subsection.itemqtydropdown)}
                    <input id="itemqty_{$subsection.orderlineid}" type="text" class="quantity" data-decorator="fnUpdateComponentQty" data-trigger="keypress" data-lineid="{$subsection.orderlineid}" data-itemqty="{$subsection.itemqty}" maxlength="8" value="{$subsection.quantity}" />
                    <img class="refresh" data-decorator="fnUpdateComponentQty" src="{$brandroot}/images/icons/refresh.png" data-trigger="click" data-lineid="{$subsection.orderlineid}" data-itemqty="{$subsection.itemqty}" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" />
                                {else}
                    <select id="itemqty_{$subsection.orderlineid}" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="{$subsection.orderlineid}" data-itemqty="{$subsection.itemqty}">
                                    {foreach from=$subsection.itemqtydropdown item=qtyValue}
                        <option {if $qtyValue==$subsection.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>
                                    {/foreach}
                    </select>
                                {/if}
                            {/if}
                    <div class="clear"></div>
                </div>
                        {/if}
                <div class="component-price {if $multilinedesc == "true"}paddingCenter{/if}">
                    {$subsection.totalsell}
                </div>
                {if $multilinedesc}
                    {assign var="multilinedesc" value="false"}
                {/if}
                <div class="clear"></div>
            </div>
                        {if $subsection.metadatahtml}
            <div id="metadatarow_{$subsection.orderlineid}" class="subcomponent-metadata">
                {$subsection.metadatahtml}
            </div>
                        {/if}
        </div>
                    {/if}
                {/foreach} {* subsections of a section *}
                <!-- sub-sections of linefooter component end -->

                <!-- checkboxes inside linefooter component start -->
                {foreach from=$section.checkboxes item=checkbox} {* checkboxes of a section *}
                    {if $checkbox.showcomponentname==true}
                        {if $stage=='qty' || ($stage=='payment' && $checkbox.checked == 1)}
    <div id="componentrow_{$checkbox.orderlineid}" class="subsection">
        <div class="subcheckboxBloc">
			<div class="section-title-header">
			{if ($checkbox.itemcomponentcategoryname != '')}
				<span class="section-category-name">{$checkbox.itemcomponentcategoryname}:</span> <span class="section-category-prompt">{$checkbox.itemcomponentprompt}</span>
			{/if}
			</div>
                            {if $checkbox.haspreview > 0}
            <img class="component-preview" src="{$checkbox.componentpreviewsrc|escape}" alt=""/>
            <div class="componentSubSectionTitle {if $isCompanion==true}companion-componentSubSectionTitle{/if}">
                            {else}
            <div class="componentSubSectionTitleLong {if $isCompanion==true}companion-componentSubSectionTitleLong{/if}">
                            {/if}
                <div class="componentContentText">
                    <div class="section-title">{$checkbox.itemcomponentname}</div>
                    <!-- START add-edit-change-remove links -->
                        {if !empty($checkbox.itemcomponentinfo)}
                    <div class="checkbox-info">
                        {$checkbox.itemcomponentinfo}
                    </div>
                            {assign var="multilinedesc" value="true"}
                        {/if}
						{if ! empty($checkbox.itemcomponentmoreinfolinkurl)}
                    <div class="checkbox-moreinfo">
                        <a href="{$checkbox.itemcomponentmoreinfolinkurl}" target="_blank">{$checkbox.itemcomponentmoreinfolinktext}</a>
                    </div>
							{assign var="multilinedesc" value="true"}
                		{/if}
                        {if !empty($checkbox.itemcomponentpriceinfo)}
                    <div class="checkbox-info">
                        {$checkbox.itemcomponentpriceinfo}
                    </div>
                            {assign var="multilinedesc" value="true"}
                        {/if}
                        {if $stage=='qty' && $checkbox.totalsell != #str_LabelNotAvailable#}
                            ({$checkbox.totalsell})
                        {/if}
                    <div class="clear"></div>
                </div>
                        {if $stage=='qty' && $checkbox.totalsell != #str_LabelNotAvailable#}
                            {foreach from=$checkbox.itemcomponentbuttons item=button}
                <div class="contentBtn btnRight {if $multilinedesc == "true"}paddingCenter{/if}" data-decorator="{$button.action}" data-orderlineid="{$orderline.orderlineid}" data-sectionlineid="{$checkbox.orderlineid}">
                    <div class="btn-white-left" ></div>
                    <div class="btn-white-middle {$button.class}">{$button.label}</div>
                    <div class="btn-white-right"></div>
                </div>
                <div class="clear"></div>
                            {/foreach}
                        {/if}
                            <!-- END add-edit-change-remove links -->
            </div>
                            {if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8) && $checkbox.checked == 1}
            <div class="quantity {if $multilinedesc == "true"}paddingCenter{/if}">
                                {if $stage=='payment'}
                <span class="quantityText {if $isCompanion==true}companion-quantityText{/if}">{$checkbox.quantity}</span>
                                {else}
                <input id="hiddeqty_{$checkbox.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$checkbox.quantity}"/>
                                    {if empty($checkbox.itemqtydropdown)}
                <input id="itemqty_{$checkbox.orderlineid}" type="text" class="quantity" data-decorator="fnUpdateComponentQty" data-trigger="keypress" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" maxlength="8" value="{$checkbox.quantity}" />
                <img class="refresh" data-decorator="fnUpdateComponentQty" src="{$brandroot}/images/icons/refresh.png" data-trigger="click" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" />
                                    {else}
                <select id="itemqty_{$checkbox.orderlineid}" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}">
                                        {foreach from=$checkbox.itemqtydropdown item=qtyValue}
                    <option {if $qtyValue==$checkbox.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>
                                        {/foreach}
                </select>
                                    {/if}
                                {/if}
            </div>
                            {/if}
                            {if $checkbox.checked || $checkbox.totalsell == #str_LabelNotAvailable#}
            <div class="component-price {if $multilinedesc == "true"}paddingCenter{/if}">
                {$checkbox.totalsell}
            </div>
                            {/if}
                            {if $multilinedesc}
                                {assign var="multilinedesc" value="false"}
                            {/if}
            <div class="clear"></div>
        </div>
                            {if (($checkbox.metadatahtml) && ($checkbox.checked)) }
        <div id="metadatarow_{$checkbox.orderlineid}" class="subcomponent-metadata{if not $checkbox.checked} invisible{/if}">
            {$checkbox.metadatahtml}
        </div>
                            {/if}
    </div>
    <div class="clear"></div>
                        {/if}
                    {/if}
                {/foreach} {* checkboxes of a section *}
					</div>
            {/foreach} {* linefooter sections *}
        {/if}
	<!-- linefooter sections end -->
    {/if}
	<!-- linefooter checkboxes start -->
        {if $orderline.linefootercheckboxes|@sizeof > 0}

            {if !isset($bTitleComponent)}
 <div class="customisationsHeader {if $isCompanion==true}companion-customisations{/if}">
	{if $isCompanion==true}
		{#str_LabelCompanionAlbumOptions#}
	{else}
        {#str_LabelProductOptions#}
	{/if}
		<span class="linkToggle">
    {if $stage=='payment'}
			<span id="link_{$orderline.orderlineid}" class="hide-product-options-link" data-decorator="fnToggleGeneric" data-lineid="{$orderline.orderlineid}" data-idelm="contentCustomise_{$orderline.orderlineid}" data-colour="grey">{#str_OrderShow#}</span>
    {else}
			<span id="link_{$orderline.orderlineid}" class="hide-product-options-link" data-decorator="fnToggleGeneric" data-lineid="{$orderline.orderlineid}" data-idelm="contentCustomise_{$orderline.orderlineid}" data-colour="grey">{#str_OrderHide#}</span>
    {/if}
		</span>
        <div class="clear"></div>
    </div>

    <div id="contentCustomise_{$orderline.orderlineid}" {if $stage=='payment'}style="display:none;"{/if}>
				{assign var="bTitleComponent" value="true"}
            {/if}




	{foreach from=$orderline.linefootercheckboxes item=checkbox}
        {if $checkbox.showcomponentname==true}
            {if $stage=='qty' || ($stage=='payment' && $checkbox.checked == 1)}
		<div class="customisationOption {if $isCompanion==true}companion-customisationOption{/if}">

    <div id="componentrow_{$checkbox.orderlineid}" class="checkbox">
		<div class="section-title-header">
		{if ($checkbox.itemcomponentcategoryname != '')}
			<span class="section-category-name">{$checkbox.itemcomponentcategoryname}:</span> <span class="section-category-prompt">{$checkbox.itemcomponentprompt}</span>
		{/if}
		</div>
		<div class="checkboxBloc">
                {if $checkbox.haspreview > 0}
            <img src="{$checkbox.componentpreviewsrc|escape}" class="component-preview" alt=""/>
            <div class="component-name {if $isCompanion==true}companion-component-name{/if}">
                {else}
            <div class="component-name-long {if $isCompanion==true}companion-component-name-long{/if}">
                {/if}
                <div class="componentContentText">
                    <div class="section-title">{$checkbox.itemcomponentname}</div>
            <!-- START add-edit-change-remove links -->
                {if !empty($checkbox.itemcomponentinfo)}
                    <div class="checkbox-info">
                        {$checkbox.itemcomponentinfo}
                    </div>
                {/if}
                {if ! empty($checkbox.itemcomponentmoreinfolinkurl)}
                    <div class="checkbox-moreinfo">
                        <a href="{$checkbox.itemcomponentmoreinfolinkurl}" target="_blank">{$checkbox.itemcomponentmoreinfolinktext}</a>
                    </div>
                {/if}
                {if !empty($checkbox.itemcomponentpriceinfo)}
                    <div class="checkbox-info">
                        {$checkbox.itemcomponentpriceinfo}
                    </div>
                {/if}
                {if $stage=='qty' && $checkbox.totalsell != #str_LabelNotAvailable#}
                ({$checkbox.totalsell})
                {/if}
			    <div class="clear"></div>
                </div>
                {if $stage=='qty' && $checkbox.totalsell != #str_LabelNotAvailable#}
                    {foreach from=$checkbox.itemcomponentbuttons item=button}
                <div class="contentBtn btnRight {if $multilinedesc == "true"}paddingCenter{/if}" data-decorator="{$button.action}" data-orderlineid="{$orderline.orderlineid}" data-sectionlineid="{$checkbox.orderlineid}">
                    <div class="btn-white-left" ></div>
                    <div class="btn-white-middle {$button.class}">{$button.label}</div>
                    <div class="btn-white-right"></div>
                </div>
                <div class="clear"></div>
                    {/foreach}
                {/if}
            </div>
        <!--    <div class="clear"></div> -->
                <!-- END add-edit-change-remove links -->
                {if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8) && $checkbox.checked == 1}
            <div class="quantity {if $multilinedesc == "true"}paddingCenter{/if}">
                    {if $stage=='payment'}
                <span class="quantityText {if $isCompanion==true}companion-quantityText{/if}">{$checkbox.quantity}</span>
                    {else}
                <input id="hiddeqty_{$checkbox.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$checkbox.quantity}"/>
                        {if empty($checkbox.itemqtydropdown)}
                <input id="itemqty_{$checkbox.orderlineid}" type="text" class="quantity" data-decorator="fnUpdateComponentQty" data-trigger="keypress" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" maxlength="8" value="{$checkbox.quantity}" />
                <img class="refresh" data-decorator="fnUpdateComponentQty" src="{$brandroot}/images/icons/refresh.png" data-trigger="click" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" />
                        {else}
                <select id="itemqty_{$checkbox.orderlineid}" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}">
                            {foreach from=$checkbox.itemqtydropdown item=qtyValue}
                    <option {if $qtyValue==$checkbox.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>
                            {/foreach}
                </select>
                        {/if}
                    {/if}
            </div>
                {/if}
                {if $checkbox.checked || $checkbox.totalsell == #str_LabelNotAvailable#}
            <div class="component-price {if $multilinedesc == "true"}paddingCenter{/if}">
                {$checkbox.totalsell}
            </div>
                {/if}
                {if $multilinedesc}
                    {assign var="multilinedesc" value="false"}
                {/if}
            <div class="clear"></div>
        </div>
                {if (($checkbox.metadatahtml) && ($checkbox.checked)) }
        <div id="metadatarow_{$checkbox.orderlineid}" class="component-metadata{if not $checkbox.checked} invisible{/if}">
            {$checkbox.metadatahtml}
        </div>
                {/if}
    </div>
	</div>
            {/if}
        {/if}
    {/foreach}
	{/if}
    {if isset($bTitleComponent)}
    </div>
    {/if}
    <!-- linefooter checkboxes end -->
    {if $orderline.orderlineid != -1}
	<div class="line-total">
        <!-- QTY  -->
        {if ($stage=='qty')}
            <div class="line-sub-total">
				{if $isCompanion==true}
                <span class="total-heading companion-total-heading">{#str_LabelOrderCompanionItemListItemTotal#}:</span>
				{else}
                <span class="total-heading">{#str_LabelOrderItemListItemTotal#}:</span>
				{/if}
                <span class="discount-price">{$orderline.itemcompletetotal}</span>
                <div class="clear"></div>
            </div>
        {/if}

        <!-- PAYMENT  -->
        {if ($stage=='payment')}
            <!-- NO VOUCHER  -->
            {if (!$orderline.itemvoucherapplied) || ($vouchersection=='SHIPPING') || (($vouchersection=='TOTAL') && !(($differenttaxrates) && (!$specialvouchertype)))}

                <!-- DIFFERNETTAXRATES AND SHOWPRICES WITH TAX  -->
                {if (($differenttaxrates) && ($showpriceswithtax)) }

                    {if (($vouchersection=='TOTAL') && ($specialvouchertype))}
                        {if ($orderline.itemdiscountvalueraw > 0)}
                            <div class="line-sub-total">
                                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                                <span class="discount-price">{$orderline.itemcompletetotal}</span>
                            </div>
                            <div class="line-sub-total-nopadding">
                                <span class="discount-heading">{$orderline.itemdiscountname}:</span>
                                <span class="discount-price">{$orderline.itemdiscountvalue}</span>
                            </div>
                            <div class="line-sub-total-small-top">
                                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                                <span class="discount-price">{$orderline.itemdiscountedvalue}</span>
                            </div>
                        {else}
                            <div class="line-sub-total">
                                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                                <span class="discount-price">{$orderline.itemcompletetotal}</span>
                            </div>
                        {/if}
                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->
                        {if (($showzerotax) || ( (!$showzerotax) && ($orderline.itemtaxtotalraw > 0) ) )}
                            <div class="line-sub-total-small-bottom">{$orderline.includesitemtaxtext} </div>
                        {/if}
                    {else}
                        <div class="line-sub-total">
                            <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                            <span class="discount-price">{$orderline.itemcompletetotal}</span>
                        </div>

                        <!-- SHOWTAXBREAKDOWN  -->
                        {if ($showtaxbreakdown)}
        					{if (($showzerotax) || ( (!$showzerotax) && ($orderline.itemtaxtotalraw>0) ) )}
        						<div class="line-sub-total-small-bottom">{$orderline.includesitemtaxtext} </div>
        					{/if}
                        {/if}
                    {/if}

                {/if}

                <!-- DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX  -->
                {if (($differenttaxrates) && (!$showpriceswithtax)) }

                    <!-- VALUE SET TOTAL VOUCHER  -->
                    {if (($vouchersection=='TOTAL') && ($specialvouchertype))}
                        {if ($orderline.itemdiscountvalueraw > 0)}
                            <div class="line-sub-total">
                                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                                <span class="discount-price">{$orderline.itemcompletetotal}</span>
                            </div>
                            <div class="line-sub-total-nopadding">
                                <span class="discount-heading">{$orderline.itemdiscountname}:</span>
                                <span class="discount-price">{$orderline.itemdiscountvalue}</span>
                            </div>
                        {/if}
                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->
                        {if (($showzerotax) || ( (!$showzerotax) && ($orderline.itemtaxtotalraw > 0) ) )}
                            <div class="line-sub-total">
                                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                                <span class="discount-price">{$orderline.itemdiscountedvalue}</span>
                            </div>
                            <div class="line-sub-total-nopadding">
                                <span class="discount-heading">{$orderline.itemtaxratename} ({$orderline.itemtaxrate}%):</span>
                                <span class="discount-price">{$orderline.itemtaxtotal}</span>
                            </div>
                        {/if}
                    {else}
                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->
                        {if (($showzerotax) || ( (!$showzerotax) && ($orderline.itemtaxtotalraw>0) ) )}
                            <div class="line-sub-total">
                                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                                <span class="discount-price">{$orderline.itemcompletetotal}</span>
                            </div>
                            <div class="line-sub-total-nopadding">
                                <span class="discount-heading">{$orderline.itemtaxratename} ({$orderline.itemtaxrate}%):</span>
                                <span class="discount-price">{$orderline.itemtaxtotal}</span>
                            </div>
                        {/if}
                    {/if}

					<div class="line-sub-total">
						<span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
						<span class="discount-price">{$orderline.itemtotal}</span>
					</div>

                {/if}

                <!-- NOT DIFFERNETTAXRATES  -->
                {if (!$differenttaxrates)}
                    {if ($orderline.itemdiscountvalueraw > 0)}
                    <div class="line-sub-total">
                        <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                        <span class="discount-price">{$orderline.itemcompletetotal}</span>
                    </div>
                    <div class="line-sub-total-nopadding">
                        <span class="discount-heading">{$orderline.itemdiscountname}:</span>
                        <span class="discount-price">{$orderline.itemdiscountvalue}</span>
                    </div>
                    {/if}
                    <div class="line-sub-total">
                        <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                        <span class="discount-price">{$orderline.itemtotal}</span>
                    </div>
                {/if}

            {else}

                <!-- PRODUCT VOUCHER  -->
                {if ($vouchersection=='PRODUCT')}
                    {if ($orderline.itemdiscountvalueraw > 0)}
                    <div class="line-sub-total">
                        <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                        <span class="discount-price">{$orderline.itemcompletetotal}</span>
                    </div>
                    <div class="line-sub-total-nopadding">
                        <span class="discount-heading">{$orderline.itemdiscountname}:</span>
                        <span class="discount-price">{$orderline.itemdiscountvalue}</span>
                    </div>
                    {/if}

                    <!-- DIFFERNETTAXRATES AND SHOWPRICES WITH TAX   -->
                    {if (($differenttaxrates) && ($showpriceswithtax)) }

                        <div class="line-sub-total">
                            <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                            <span class="discount-price">{$orderline.itemdiscountedvalue}</span>
                        </div>

                        <!-- SHOWTAXBREAKDOWN  -->
                        {if ($showtaxbreakdown)}
	                        {if (($showzerotax) || ( (!$showzerotax) && ($orderline.itemtaxtotalraw>0) ) )}
	                            <div class="line-sub-total-small-bottom">{$orderline.includesitemtaxtext}</div>
                            {/if}
                        {/if}
                    {/if}

                    <!-- DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX   -->
                    {if (($differenttaxrates) && (!$showpriceswithtax)) }

                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->
                        {if (($showzerotax) || ( (!$showzerotax) && ($orderline.itemtaxtotalraw>0) ) )}
                            <div class="line-sub-total">
                                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                                <span class="discount-price">{$orderline.itemdiscountedvalue}</span>
                            </div>
                            <div class="line-sub-total-nopadding">
                                <span class="discount-heading">{$orderline.itemtaxratename} ({$orderline.itemtaxrate}%):</span>
                                <span class="discount-price">{$orderline.itemtaxtotal}</span>
                            </div>
                        {/if}
						<div class="line-sub-total">
							<span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
							<span class="discount-price">{$orderline.itemtotal}</span>
						</div>

                    {/if}

                    <!-- NOT DIFFERNETTAXRATES  -->
                    {if (!$differenttaxrates)}

                        <div class="line-sub-total">
                            <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                            <span class="discount-price">{$orderline.itemdiscountedvalue}</span>
                        </div>

                    {/if}

                {/if}

                <!-- VALUE OFF TOTAL VOUCHER  -->
                {if (($vouchersection=='TOTAL') && (($differenttaxrates) && (!$specialvouchertype)))}
                    {if ($orderline.itemdiscountvalueraw > 0)}
                    <div class="line-sub-total">
                        <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                        <span class="discount-price">{$orderline.itemcompletetotal}</span>
                    </div>
                    <div class="line-sub-total-nopadding">
                        <span class="discount-heading">{$orderline.itemdiscountname}:</span>
                        <span class="discount-price">{$orderline.itemdiscountvalue}</span>
                    </div>
                    {/if}
                    {if (!$showpriceswithtax)}
                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->
                        {if (($showzerotax) || ( (!$showzerotax) && ($orderline.itemtaxtotalraw>0) ) )}
                            <div class="line-sub-total">
                                <span class="discount-heading">{#str_LabelSubTotal#}:</span>
                                <span class="discount-price">{$orderline.itemdiscountedvalue}</span>
                            </div>
                            <div class="line-sub-total-nopadding">
                                <span class="discount-heading">{$orderline.itemtaxratename} ({$orderline.itemtaxrate}%):</span>
                                <span class="discount-price">{$orderline.itemtaxtotal}</span>
                            </div>
                            <div class="line-sub-total">
                                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                                <span class="discount-price">{$orderline.itemtotal}</span>
                            </div>
                        {else}
                            <div class="line-sub-total">
                                <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                                <span class="discount-price">{$orderline.itemdiscountedvalue}</span>
                            </div>
                        {/if}
                    {else}
                        <div class="line-sub-total">
                            <span class="discount-heading">{#str_LabelOrderItemListItemTotal#}:</span>
                            <span class="discount-price">{$orderline.itemtotal}</span>
                        </div>

                        <!-- SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0  -->

                        {if (($showzerotax) || ( (!$showzerotax) && ($orderline.itemtaxtotalraw>0) ) )}
                            <div class="line-sub-total-small-bottom">{$orderline.includesitemtaxtext}</div>
                        {/if}
                    {/if}
                {/if}

            {/if}

        {/if}
	</div>
	{/if}
{if $call_action == 'init'}
        </div>
    </div>
</div>
{/if}