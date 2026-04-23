{if $orderline.orderlineid != -1}
    <span class="txt_linebreak"></span>

    <div class="order-line">

        <table width="600" style="margin-bottom:10px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
            <tr style="color: #575757; font-family: Lucida Grande;">
				<td valign="top" bgcolor="#FFFFFF" height="65" width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
					{if $orderline.projectthumbnail !== '' && $orderline.parentorderitemid === 0} 
					<img class="componentPreview thumbnail" src="{$orderline.projectthumbnail|escape}" width="75" />
					{elseif $orderline.assetrequest !== '' && $showthumbnail !== 0}
					<img class="componentPreview thumbnail" src="{$orderline.assetrequest|escape}" height="65" width="75">
					{else}
					<img class="componentPreview thumbnail" src="../resources/thumbnail_placeholder.jpg" alt="" width="75" />
					{/if}
				</td>

				<td bgcolor="#FFFFFF" style="word-break:break-word; padding: 10px;" width="200">
					{$orderline.itemproductname}
				</td>
				<td bgcolor="#FFFFFF" style="word-break:break-all; padding: 10px;" width="130">
					{$orderline.itemqty}
				</td>
				<td bgcolor="#FFFFFF" align="right" style="word-break:break-all;  padding: 10px;" width="130">
					{if $orderline.itemshowproductsell == 1}
						{$orderline.itemproducttotalsell}
					{/if}
				</td>
            </tr>
            <tr style="color: #575757; font-family: Lucida Grande;">
                <td style="word-break:break-word; padding: 10px;" colspan="3">
                    {#str_LabelProjectName#}: {$orderline.projectname} <br />
                    {if $orderline.itemproducttype != 2}
						<div style="padding-top: 5px; color: #888;">{$orderline.itempagecount} {if $orderline.itempagecount == 1} {#str_LabelPage#} {else} {#str_LabelPages#}{/if}</div>
                    {/if}
				</td>
            </tr>
        </table>

        {if $orderline.itemexternalassets|@count > 0 && $orderline.displayassets == true}
        <table width="600" style="margin-bottom: 10px; width: 600px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
        {foreach from=$orderline.itemexternalassets item=asset }
            <tr style="color: #575757; font-family: Lucida Grande;">
                <td style="word-break:break-all; padding: 10px;" width="295">
                     {$asset.pagename}: {$asset.name}
                </td>
                <td width="130" style="word-break:break-all; padding: 10px;">
                </td>
                <td  width="130" style="word-break:break-all; padding: 10px;" align="right">
                     {$asset.charge}
                </td>
            </tr>
        {/foreach}
        </table>
        {/if}

        {if $orderline.itempictures|@count > 0}
         <!-- Photo print start -->
        <table width="600" style="margin-bottom: 10px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
        {foreach from=$orderline.itempictures item=sizegroup }
            <tr style="color: #575757; font-family: Lucida Grande;">
                <td style="background-color:#e8e9ed; border-bottom: 2px solid #FFFFFF; word-break:break-all; padding: 10px; width: 295px;">
                    {$sizegroup.groupdisplayname}
				</td>
				<td style="background-color:#e8e9ed; border-bottom: 2px solid #FFFFFF; word-break:break-all; padding: 10px; width: 130px;">
					{$sizegroup.picturecount}
				</td>
				<td style="background-color:#e8e9ed; border-bottom: 2px solid #FFFFFF; word-break:break-all; padding: 10px; width: 130px; text-align: right;">
					{$sizegroup.formatedgrouptotalsell}
                </td>
            </tr>
        {/foreach}
        </table>
        {/if}

        {if $orderline.calendarcustomisations|@count > 0}
    <!-- Caledar Customisations -->
        <table width="600" style="margin-bottom: 10px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
    {foreach from=$orderline.calendarcustomisations item=calendarcustomisations }
            <tr style="color: #575757; font-family: Lucida Grande;">
                <td style="word-break:break-all; padding: 10px;" width="470">
                     {$calendarcustomisations.name}
                </td>
                <td  width="130" style="word-break:break-all; padding: 10px;" align="right">
                     {$calendarcustomisations.formattedtotalsell}
                </td>
            </tr>
    {/foreach}
        </table>
        {/if}

		{if $orderline.aicomponent|@sizeof > 0}
			<table width="600" style="margin-bottom: 10px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
                <tr style="color:#575757; font-family:Lucida Grande">
                    <td valign="top" bgcolor="#FFFFFF" height="65" width="75" rowspan="2" style="word-break:break-all; padding:10px">
                        <img class="aicomponent-preview"  src="{$orderline.aicomponent.previewsrc|escape}" height="65" width="75">
                    </td>
                    <td valign="top" bgcolor="#FFFFFF" width="200" style="word-break:break-word; padding:10px">
                        <span class="x_txt_componentname">
                            <span class="x_section-title" style="font-weight:bold">{$orderline.aicomponent.name}</span>
                        </span>
                    </td>
                    <td valign="top" bgcolor="#FFFFFF" width="130" style="word-break:break-all; padding:10px">
                    </td>
                    <td valign="top" bgcolor="#FFFFFF" align="right" width="130" style="word-break:break-all; padding:10px">
                        <span class="x_component-price" style="color:#333333; float:right">
                            {$orderline.aicomponent.formattedtotalsell}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" width="500" style="word-break:break-word; padding:0 10px">
                        <div class="x_section-info" style="word-break:break-word; line-height: 1.6; margin-bottom:5px">
                            {$orderline.aicomponent.componentinfo}
                        </div>
                    </td>
                </tr>
            </table>
		{/if}

        <!-- checkboxes start -->
        {if $orderline.orderlineid != -1}
            {foreach from=$orderline.checkboxes item=checkbox}
                {if $checkbox.showcomponentname==true}
                    {if $checkbox.checked}
                        <table width="600" style="margin-bottom: 10px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
                            <tr style="color: #575757; font-family: Lucida Grande;">
                                {if $checkbox.haspreview > 0 && $showthumbnail != 0}
                                    <td valign="top" height="65" width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
                                        <img class="componentPreview"  src="{$checkbox.componentpreviewsrc|escape}" height="65" width="75">
                                    </td>

                                    <td style="word-break:break-all; padding: 10px;" width="200">
                                        {$checkbox.itemcomponentprompt} - {$checkbox.itemcomponentname}
                                    </td>
                                    <td style="word-break:break-all; padding: 10px;" width="130">
                                        {$checkbox.quantity}
                                    </td>
                                    <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                                        {$checkbox.totalsell}
                                    </td>
                                {else}
                                    <td style="word-break:break-all; padding: 10px;" width="295">
                                        {$checkbox.itemcomponentprompt} - {$checkbox.itemcomponentname}
                                    </td>
                                    <td style="word-break:break-all; padding: 10px;" width="130">
                                        {$checkbox.quantity}
                                    </td>
                                    <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                                        {$checkbox.totalsell}
                                    </td>
                                {/if}
                            </tr>
                            {if ($checkbox.itemcomponentinfo != '' || $checkbox.itemcomponentpriceinfo != '')}
                                <tr>
                                    {if $checkbox.haspreview > 0 && $showthumbnail != 0}
                                        <td colspan="3" width="460" style="word-break:break-all; padding: 10px;">
                                    {else}
                                        <td colspan="3" width="535" style="word-break:break-all; padding: 10px;">
                                    {/if}
                                            {if $checkbox.itemcomponentinfo != ''}
                                                <div class="checkbox-info">
                                                    <span class="txt_itemcomponentinfo">{$checkbox.itemcomponentinfo}</span>
                                                </div>
                                            {/if}
                                            {if $checkbox.itemcomponentpriceinfo != ''}
                                                <div class="checkbox-info">
                                                    <span class="txt_itemcomponentpriceinfo">{$checkbox.itemcomponentpriceinfo}</span>
                                                </div>
                                            {/if}
                                        </td>
                                </tr>
                            {/if}
                        </table>

                        {if $checkbox.metadatahtml}
                            <table width="600" style="border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
                                <tr>
                                    <td width="600" style="word-break:break-all; padding: 10px;">
                                        <span class="component-metadata{if not $checkbox.checked} invisible{/if}">{$checkbox.metadatahtml}</span>
                                    </td>
                                </tr>

                            </table>
                        {/if}
                    {/if}
                {/if}
            {/foreach}
        {/if}
        <!-- checkboxes end -->




        <!-- sections start -->
        {foreach from=$orderline.sections item=section} {* sections *}
        {if $section.showcomponentname==true}
            <table width="600" style="margin-bottom:10px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
                <tr style="color: #575757; font-family: Lucida Grande;">
                    {if $section.haspreview > 0 && $showthumbnail != 0}
                        <td valign="top" bgcolor="#FFFFFF" height="65" width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
                            <img class="productpreview-container"  src="{$section.componentpreviewsrc|escape}" height="65" width="75">
                        </td>

                        <td valign="top" bgcolor="#FFFFFF" style="word-break:break-all; padding: 10px;" width="200">
                            <span class="txt_componentname">
                            {if $section.count <= 1 || $section.prompt == ''}
                                <span class="section-title">{$section.sectionlabel}</span> <span> - </span>
                                <span>{$section.itemcomponentname}</span>
                            {else}
                                <span class="section-title">{$section.sectionlabel}</span>
                                <br>
                                <span>{$section.itemcomponentname}</span>
                            {/if}
                            </span>
                        </td>
                        <td valign="top" bgcolor="#FFFFFF" style="word-break:break-all; padding: 10px;" width="130">
                            {if $section.pricingmodel == 7 || $section.pricingmodel == 8}
                                <div class="paymentcomponentquantity">
                                    <span class="txt_qty">{$section.quantity}</span>
                                </div>
                            {/if}
                        </td>
                        <td valign="top" bgcolor="#FFFFFF" align="right" style="word-break:break-all; padding: 10px;" width="130">
                            <span class="component-price"><span class="txt_price">{$section.totalsell}</span></span>
                        </td>
                    {else}
                        <td valign="top" style="word-break:break-all; padding: 10px;" width="295">
                            <span class="txt_componentname">
                            {if $section.count <= 1 || $section.prompt == ''}
                                <span class="section-title">{$section.sectionlabel}</span> <span> - </span>
                                <span>{$section.itemcomponentname}</span>
                            {else}
                                <span class="section-title">{$section.sectionlabel}</span>
                                <br>
                                <span>{$section.itemcomponentname}</span>
                            {/if}
                            </span>
                        </td>
                        <td valign="top" style="word-break:break-all;" width="130">
                            {if $section.pricingmodel == 7 || $section.pricingmodel == 8}
                                <div class="paymentcomponentquantity">
                                    <span class="txt_qty">{$section.quantity}</span>
                                </div>
                            {/if}
                        </td>
                        <td valign="top" align="right" style="word-break:break-all; padding: 10px;" width="130">
                            <span class="component-price"><span class="txt_price">{$section.totalsell}</span></span>
                        </td>
                    {/if}
                </tr>

                {if ($section.itemcomponentinfo != '' || $section.itemcomponentpriceinfo != '')}
                <tr>
                    {if $section.haspreview > 0 && $showthumbnail != 0}
                        <td colspan="3" width="500" style="word-break:break-all; padding: 10px;">
                    {else}
                        <td colspan="3" width="500" style="word-break:break-all; padding: 10px;">
                    {/if}
                    {if $section.itemcomponentinfo != ''}
                        <div class="section-info" style="word-break:break-all; padding: 10px;">
                            <span class="txt_itemcomponentinfo">{$section.itemcomponentinfo}</span>
                        </div>
                    {/if}
                    {if $section.itemcomponentpriceinfo != ''}
                        <div class="section-info" style="word-break:break-all; padding: 10px;">
                            <span class="txt_itemcomponentpriceinfo">{$section.itemcomponentpriceinfo}</span>
                        </div>
                    {/if}
                    </td>
                </tr>
                {/if}
            </table>

            {if $section.metadatahtml}
                <span class="component-metadata">{$section.metadatahtml}</span>
            {/if}

        {/if}

    <!-- sub-sections of component start -->
    {foreach from=$section.subsections item=subsection} {* subsections of a section *}
        {if $subsection.showcomponentname==true}
            <table width="600" style="margin-bottom: 10px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
                <tr style="color: #575757; font-family: Lucida Grande;">
                    {if $subsection.haspreview > 0 && $showthumbnail != 0}
                        <td valign="top" height="65" width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
                            <img class="componentPreview"  src="{$subsection.componentpreviewsrc|escape}" height="65" width="75">
                        </td>

                        <td style="word-break:break-all; padding: 10px;" width="200" >
                            <span class="txt_subcomponentname">
                                {if $subsection.count <= 1 || $subsection.prompt == ''}
                                    <span class="section-title">{$subsection.sectionlabel}</span><span> - </span>
                                    <span>{$subsection.itemcomponentname}</span>
                                {else}
                                    <span class="section-title">{$subsection.sectionlabel}</span>
                                    <br>
                                    <span>{$subsection.itemcomponentname}</span>
                                {/if}
                            </span>
                        </td>
                        <td style="word-break:break-all; padding: 10px;" width="130">
                            {if $subsection.pricingmodel == 7 || $subsection.pricingmodel == 8}
                                <div class="paymentsubcomponentquantity">
                                    <span class="txt_subqty">{$subsection.quantity}</span>
                                </div>
                            {/if}
                        </td>
                        <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                            <span class="component-price"><span class="txt_price">{$subsection.totalsell}</span></span>
                        </td>
                    {else}
                        <td style="word-break:break-all; padding: 10px;" width="295">
                            <span class="txt_subcomponentname">
                                {if $subsection.count <= 1 || $subsection.prompt == ''}
                                    <span class="section-title">{$subsection.sectionlabel}</span><span> - </span>
                                    <span>{$subsection.itemcomponentname}</span>
                                {else}
                                    <span class="section-title">{$subsection.sectionlabel}</span>
                                    <br>
                                    <span>{$subsection.itemcomponentname}</span>
                                {/if}
                            </span>
                        </td>
                        <td style="word-break:break-all;padding: 10px;" width="130">
                            {if $subsection.pricingmodel == 7 || $subsection.pricingmodel == 8}
                                <div class="paymentsubcomponentquantity">
                                    <span class="txt_subqty">{$subsection.quantity}</span>
                                </div>
                            {/if}
                        </td>
                        <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                            <span class="component-price"><span class="txt_price">{$subsection.totalsell}</span></span>
                        </td>
                    {/if}

                </tr>
                 {if ($subsection.itemcomponentinfo != '' || $subsection.itemcomponentpriceinfo != '')}
                <tr>
                    {if $subsection.haspreview > 0 && $showthumbnail != 0}
                        <td colspan="3" width="500" style="word-break:break-all; padding: 10px;">
                    {else}
                     <td colspan="3" width="600" style="word-break:break-all; padding: 10px;">
                    {/if}
                        {if $subsection.itemcomponentinfo != ''}
                            <div class="section-info">
                                <span class="txt_itemsubcomponentinfo">{$subsection.itemcomponentinfo}</span>
                            </div>
                        {/if}
                        {if $subsection.itemcomponentpriceinfo != ''}
                            <div class="section-info">
                                <span class="txt_itemsubcomponentpriceinfo">{$subsection.itemcomponentpriceinfo}</span>
                            </div>
                        {/if}
                    </td>
                </tr>
                {/if}
            </table>

        {if $subsection.metadatahtml}
            <span id="metadatarow_{$subsection.orderlineid}" class="component-metadata">
                <span class="txt_subComponentMetaData">
                    {$subsection.metadatahtml}
                </span>
            </span>
        {/if}
    {/if}
    {/foreach} {* subsections of a section *}

    <!-- sub-sections of component end -->

    <div class="clear"></div>

    <!-- checkboxes inside component start-->
    {foreach from=$section.checkboxes item=checkbox} {* checkboxes of a section *}
        {if $checkbox.showcomponentname==true}
            {if $checkbox.checked == 1}
                <table width="600" style="margin-Bottom:10px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
                    <tr style="color: #575757; font-family: Lucida Grande;">
                        {if $checkbox.haspreview > 0 && $showthumbnail != 0}
                            <td bgcolor="#FFFFFF" valign="top" height="65" width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
                                <img class="componentPreview"  src="{$checkbox.componentpreviewsrc}" height="65" width="75">
                            </td>

                            <td valign="top" bgcolor="#FFFFFF" style="word-break:break-all; padding: 10px;" width="200">
                                <span class="txt_subcomponentname">
                                    <span class="component-name">{$checkbox.itemcomponentprompt}</span>
                                    <span> - {$checkbox.itemcomponentname}</span>
                                </span>
                            </td>
                            <td valign="top" bgcolor="#FFFFFF" style="word-break:break-all; padding: 10px;" width="130">
                                {if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8)}
                                    <div class="paymentsubcomponentquantity">
                                        <span class="txt_subqty">{$checkbox.quantity}</span>
                                    </div>
                                {/if}
                            </td>
                            <td valign="top" bgcolor="#FFFFFF" align="right" style="word-break:break-all; padding: 10px;" width="130">
                                <span class="component-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
                            </td>
                        {else}
                            <td valign="top" style="word-break:break-all; padding: 10px;" width="295">
                                <span class="txt_subcomponentname">
                                    <span class="component-name">{$checkbox.itemcomponentprompt}</span>
                                    <span> - {$checkbox.itemcomponentname}</span>
                                </span>
                            </td>
                            <td valign="top" style="word-break:break-all; padding: 10px;" width="130">
                                {if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8)}
                                    <div class="paymentsubcomponentquantity">
                                        <span class="txt_subqty">{$checkbox.quantity}</span>
                                    </div>
                                {/if}
                            </td>
                            <td valign="top" align="right" style="word-break:break-all; padding: 10px;" width="130">
                                <span class="component-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
                            </td>
                        {/if}

                    </tr>
                     {if ($checkbox.itemcomponentinfo != '' || $checkbox.itemcomponentpriceinfo)}
                    <tr>
                        {if $subsection.haspreview > 0 && $showthumbnail != 0}
                        <td colspan="3" width="500" style="word-break:break-all; padding: 10px;">
                        {else}
                        <td colspan="3" width="600" style="word-break:break-all; padding: 10px;">
                        {/if}
                        {if $checkbox.itemcomponentinfo != '' }
                            <div style="background-color:white; padding: 10px;" class="checkbox-info">
                                <span class="txt_itemsubcomponentinfo">
                                    {$checkbox.itemcomponentinfo}
                                </span>
                            </div>
                        {/if}
                        {if $checkbox.itemcomponentpriceinfo != ''}
                            <div style="background-color:white; word-wrap: break-word; padding: 10px;" class="checkbox-info">
                                <span class="txt_itemsubcomponentpriceinfo">
                                    {$checkbox.itemcomponentpriceinfo}
                                </span>
                            </div>
                        {/if}
                        </td>
                    </tr>
                    {/if}
                </table>

                <div class="clear"></div>
                {if $checkbox.metadatahtml}
                    <span id="metadatarow_{$checkbox.orderlineid}" class="component-metadata{if not $checkbox.checked} invisible{/if}"><span class="txt_subComponentMetaData">{$checkbox.metadatahtml}</span></span>
                {/if}
            {/if}
        {/if}
    {/foreach} {* checkboxes of a section *}
    <!-- checkboxes inside component end -->

{/foreach} {* sections *}

    <!-- sections end -->

    <div class="clear"></div>
{/if}
    <!-- linefooter sections start -->
    {if $orderline.orderlineid != -1}
    {foreach from=$orderline.linefootersections item=section} {* linefooter sections *}
    {if $section.showcomponentname == true}
            <table width="600" style="margin-bottom:10px; border: solid 1px white; font-size: 12px; color: #575757; font-family: Lucida Grande;">
                <tr style="color: #575757; font-family: Lucida Grande;">
                    {if $section.haspreview > 0 && $showthumbnail != 0}
                        <td valign="top" height="65" width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
                            <img class="componentPreview"  src="{$section.componentpreviewsrc|escape}" height="65" width="75">
                        </td>
                        <td valign="top" style="word-break:break-all; padding: 10px;" width="200">
                             <span class="txt_componentname">
                                {if $section.count <= 1 || $section.prompt == ''}
                                    <span class="section-title">{$section.sectionlabel}</span> <span> - </span>
                                    <span>{$section.itemcomponentname}</span>
                                {else}
                                    <span class="section-title">{$section.sectionlabel}</span>
                                    <br>
                                    <span>{$section.itemcomponentname}</span>
                                {/if}
                            </span>
                        </td>
                        <td valign="top" style="word-break:break-all; padding: 10px;" width="130">
                            {if $section.pricingmodel == 7 || $section.pricingmodel == 8}
                                <div class="paymentcomponentquantity">
                                    <span class="txt_qty">{$section.quantity}</span>
                                </div>
                            {/if}
                        </td>
                        <td valign="top" align="right" style="word-break:break-all; padding: 10px;" width="130">
                            <span class="component-price"><span class="txt_price">{$section.totalsell}</span></span>
                        </td>
                    {else}
                        <td valign="top" style="word-break:break-all; padding: 10px;" width="295">
                             <span class="txt_componentname">
                                {if $section.count <= 1 || $section.prompt == ''}
                                    <span class="section-title">{$section.sectionlabel}</span> <span> - </span>
                                    <span>{$section.itemcomponentname}</span>
                                {else}
                                    <span class="section-title">{$section.sectionlabel}</span>
                                    <br>
                                    <span>{$section.itemcomponentname}</span>
                                {/if}
                            </span>
                        </td>
                        <td valign="top" style="word-break:break-all; padding: 10px;" width="130">
                            {if $section.pricingmodel == 7 || $section.pricingmodel == 8}
                                <div class="paymentcomponentquantity">
                                    <span class="txt_qty">{$section.quantity}</span>
                                </div>
                            {/if}
                        </td>
                        <td valign="top" align="right" style="word-break:break-all; padding: 10px;" width="130">
                            <span class="component-price"><span class="txt_price">{$section.totalsell}</span></span>
                        </td>
                    {/if}

                </tr>
                {if ($section.itemcomponentinfo != '' || $section.itemcomponentpriceinfo != '')}
                <tr>
                    {if $section.haspreview > 0 && $showthumbnail != 0}
                        <td valign="top" colspan="3" width="500" style="word-break:break-all; padding: 10px;">
                    {else}
                        <td valign="top" colspan="3" width="600" style="word-break:break-all; padding: 10px;">
                    {/if}
                            {if $section.itemcomponentinfo != ''}
                                <div class="section-info">
                                    <span class="txt_itemcomponentinfo">{$section.itemcomponentinfo}</span>
                                </div>
                            {/if}
                            {if $section.itemcomponentpriceinfo != '' }
                                <div class="section-info">
                                    <span class="txt_itemcomponentpriceinfo">{$section.itemcomponentpriceinfo}</span>
                                </div>
                            {/if}
                        </td>
                </tr>
                {/if}
            </table>

        <div>
            {if $section.metadatahtml}
                <span class="component-metadata">{$section.metadatahtml}</span>
            {/if}
        </div>
        {/if}

        <!-- sub-sections of linefooter component start -->
        {foreach from=$section.subsections item=subsection} {* subsections of a section *}
                {if $subsection.showcomponentname==true}
                    <table width="600" border="0px" style="margin-bottom:10px; font-size: 12px; color: white; font-family: Lucida Grande;">
                    <tr style="color: #575757; font-family: Lucida Grande;">
                        {if $subsection.haspreview > 0 && $showthumbnail != 0}
                            <td valign="top" height="65" width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
                                <img class="componentPreview"  src="{$subsection.componentpreviewsrc|escape}" height="65" width="75">
                            </td>

                            <td style="word-break:break-all;" width="200" style="word-break:break-all; padding: 10px;">
                                <span class="txt_subcomponentname">
                                    {if $subsection.count <= 1 || $subsection.prompt == ''}
                                        <span class="section-title">{$subsection.sectionlabel}</span><span> - </span>
                                        <span>{$subsection.itemcomponentname}</span>
                                    {else}
                                        <span class="section-title">{$subsection.sectionlabel}</span>
                                        <br>
                                        <span>{$subsection.itemcomponentname}</span>
                                    {/if}
                                </span>
                            </td>
                            <td style="word-break:break-all; padding: 10px;" width="130">
                                {if $subsection.pricingmodel == 7 || $subsection.pricingmodel == 8}
                                    <div class="paymentsubcomponentquantity">
                                        <span class="txt_subqty">{$subsection.quantity}</span>
                                    </div>
                                {/if}
                            </td>
                            <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                                <span class="txt_price"><span class="component-price">{$subsection.totalsell}</span></span>
                            </td>
                        {else}
                            <td style="word-break:break-all; padding: 10px;" width="295">
                                <span class="txt_subcomponentname">
                                    {if $subsection.count <= 1 || $subsection.prompt == ''}
                                        <span class="section-title">{$subsection.sectionlabel}</span><span> - </span>
                                        <span>{$subsection.itemcomponentname}</span>
                                    {else}
                                        <span class="section-title">{$subsection.sectionlabel}</span>
                                        <br>
                                        <span>{$subsection.itemcomponentname}</span>
                                    {/if}
                                </span>
                            </td>
                            <td style="word-break:break-all; padding: 10px;" width="130">
                                {if $subsection.pricingmodel == 7 || $subsection.pricingmodel == 8}
                                    <div class="paymentsubcomponentquantity">
                                        <span class="txt_subqty">{$subsection.quantity}</span>
                                    </div>
                                {/if}
                            </td>
                            <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                                <span class="txt_price"><span class="component-price">{$subsection.totalsell}</span></span>
                            </td>
                        {/if}

                    </tr>
                    {if ($subsection.itemcomponentinfo != '' || $subsection.itemcomponentpriceinfo != '')}
                    <tr>
                        {if $subsection.haspreview > 0 && $showthumbnail != 0}
                            <td colspan="3" width="500" style="word-break:break-all; padding: 10px;">
                        {else}
                            <td colspan="3" width="600" style="word-break:break-all; padding: 10px;">
                        {/if}
                            {if $subsection.itemcomponentinfo != ''}
                                <div class="section-info">
                                    <span class="txt_itemsubcomponentinfo">{$subsection.itemcomponentinfo}</span>
                                </div>
                            {/if}
                            {if $subsection.itemcomponentpriceinfo != ''}
                                <div class="section-info">
                                    <span class="txt_itemsubcomponentpriceinfo">{$subsection.itemcomponentpriceinfo}</span>
                                </div>
                            {/if}
                            </td>
                    </tr>
                    {/if}
                </table>

                {if $subsection.metadatahtml}
                    <span id="metadatarow_{$subsection.orderlineid}" class="component-metadata">
                        <span class="txt_subComponentMetaData">
                            {$subsection.metadatahtml}
                        </span>
                    </span>
                {/if}
        {/if}
    {/foreach} {* subsections of a section *}

    <!-- sub-sections of linefooter component end -->

    <div class="clear"></div>

    <!-- checkboxes inside linefooter component start -->
        {foreach from=$section.checkboxes item=checkbox} {* checkboxes of a section *}
            {if $checkbox.showcomponentname==true}
                {if $checkbox.checked == 1}
                <table width="600" border="0px" style="margin-bottom:10px; font-size: 12px; color: white; font-family: Lucida Grande;">
                    <tr style="color: #575757; font-family: Lucida Grande;">
                        {if $checkbox.haspreview > 0 && $showthumbnail != 0}
                            <td valign="top"  width="65px" rowspan="2" style="word-break:break-all;">
                                <img class="componentPreview"  src="{$checkbox.componentpreviewsrc}" height="65" width="75">
                            </td>

                            <td style="word-break:break-all; padding: 10px;" width="200">
                                <span class="txt_subcomponentname">
                                    <span class="component-name">{$checkbox.itemcomponentprompt}</span>
                                    <span> - {$checkbox.itemcomponentname}</span>
                                </span>
                            </td>
                            <td style="word-break:break-all; padding: 10px;" width="130">
                                {if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8)}
                                    <div class="paymentsubcomponentquantity">
                                        <span class="txt_subqty">{$checkbox.quantity}</span>
                                    </div>
                                {/if}
                            </td>
                            <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                                <span class="component-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
                            </td>
                        {else}
                            <td style="word-break:break-all; padding: 10px;" width="295">
                                <span class="txt_subcomponentname">
                                    <span class="component-name">{$checkbox.itemcomponentprompt}</span>
                                    <span> - {$checkbox.itemcomponentname}</span>
                                </span>
                            </td>
                            <td style="word-break:break-all; padding: 10px;" width="130">
                                {if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8)}
                                    <div class="paymentsubcomponentquantity">
                                        <span class="txt_subqty">{$checkbox.quantity}</span>
                                    </div>
                                {/if}
                            </td>
                            <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                                <span class="component-price"><span class="txt_price">{$checkbox.totalsell}</span></span>
                            </td>
                        {/if}

                    </tr>
                     {if ($checkbox.itemcomponentinfo != '' || $checkbox.itemcomponentpriceinfo)}
                    <tr>
                        {if $subsection.haspreview > 0 && $showthumbnail != 0}
                        <td colspan="3" width="500" style="word-break:break-all; padding: 10px;">
                        {else}
                        <td colspan="3" width="600" style="word-break:break-all; padding: 10px;">
                        {/if}
                        {if $checkbox.itemcomponentinfo != '' }
                            <div style="background-color:white; word-wrap: break-word" class="checkbox-info">
                                <span class="txt_itemsubcomponentinfo">
                                    {$checkbox.itemcomponentinfo}
                                </span>
                            </div>
                        {/if}
                        {if $checkbox.itemcomponentpriceinfo != ''}
                            <div style="background-color:white; word-wrap: break-word" class="checkbox-info">
                                <span class="txt_itemsubcomponentpriceinfo">
                                    {$checkbox.itemcomponentpriceinfo}
                                </span>
                            </div>
                        {/if}
                        </td>
                    </tr>
                    {/if}
                </table>

                    {if $checkbox.metadatahtml}
                        <span id="metadatarow_{$checkbox.orderlineid}" class="component-metadata{if not $checkbox.checked} invisible{/if}"><span class="txt_subComponentMetaData">{$checkbox.metadatahtml}</span></span>
                    {/if}

            {/if}
        {/if}
    {/foreach} {* checkboxes of a section *}

    <!-- checkboxes inside linefooter component end -->

    {/foreach} {* linefooter sections *}
    {/if}
    <!-- linefooter sections end -->

    <div class="clear"></div>

    <!-- linefooter checkboxes start -->
    {foreach from=$orderline.linefootercheckboxes item=checkbox}
    {if $checkbox.showcomponentname==true}
        {if $checkbox.checked == 1}
            <table width="600" border="0px" style="margin-bottom:10px; font-size: 12px; color: white; font-family: Lucida Grande;">
                <tr style="color: #575757; font-family: Lucida Grande;">
                    {if $checkbox.haspreview > 0 && $showthumbnail != 0}
                        <td valign="top" height="65" width="75" rowspan="2" style="word-break:break-all; padding: 10px;">
                            <img class="componentPreview"  src="{$checkbox.componentpreviewsrc|escape}" height="65" width="75">
                        </td>

                        <td style="word-break:break-all; padding: 10px;" width="200">
                            <span class="txt_subcomponentname">
                                <span class="component-name">{$checkbox.itemcomponentprompt}</span>  <span> - {$checkbox.itemcomponentname} </span>
                            </span>
                        </td>
                        <td style="word-break:break-all; padding: 10px;" width="130">
                            {if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8)}
                                <div class="paymentsubcomponentquantity">
                                    <span class="txt_subqty">{$checkbox.quantity}</span>
                                </div>
                            {/if}
                        </td>
                        <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                            <span class="txt_price"><span class="component-price">{$checkbox.totalsell}</span></span>
                        </td>
                    {else}
                        <td style="word-break:break-all; padding: 10px;" width="295">
                            <span class="txt_subcomponentname">
                                <span class="component-name">{$checkbox.itemcomponentprompt}</span>  <span> - {$checkbox.itemcomponentname} </span>
                            </span>
                        </td>
                        <td style="word-break:break-all; padding: 10px;" width="130">
                            {if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8)}
                                <div class="paymentsubcomponentquantity">
                                    <span class="txt_subqty">{$checkbox.quantity}</span>
                                </div>
                            {/if}
                        </td>
                        <td align="right" style="word-break:break-all; padding: 10px;" width="130">
                            <span class="txt_price"><span class="component-price">{$checkbox.totalsell}</span></span>
                        </td>
                    {/if}

                </tr>
                {if $checkbox.itemcomponentinfo != '' || $checkbox.itemcomponentpriceinfo != ''}
                <tr>
                    {if $checkbox.haspreview > 0 && $showthumbnail != 0}
                    <td colspan="3" width="500" style="word-break:break-all; padding: 10px;">
                    {else}
                    <td colspan="3" width="600" style="word-break:break-all; padding: 10px;">
                    {/if}
                        {if $checkbox.itemcomponentinfo != ''}<div class="checkbox-info"><span class="txt_itemcomponentinfo">{$checkbox.itemcomponentinfo}</span></div>{/if}
                        {if $checkbox.itemcomponentpriceinfo != ''}<div class="checkbox-info"><span class="txt_itemcomponentpriceinfo">{$checkbox.itemcomponentpriceinfo}</span></div>{/if}
                    </td>
                </tr>
                {/if}
            </table>

            {if $checkbox.metadatahtml}
                <span id="metadatarow_{$checkbox.orderlineid}" class="component-metadata{if not $checkbox.checked} invisible{/if}"><span class="txt_subComponentMetaData">{$checkbox.metadatahtml}</span></span>
            {/if}

            {/if}
        {/if}
    {/foreach}

    <!-- linefooter checkboxes end -->


    </div>
{if $orderline.orderlineid != -1}
    <div class="line-total">
    <span class="txt_linebreak"></span>
        {* PAYMENT *}

            {* NO VOUCHER *}

            {if (!$orderline.itemvoucherapplied) || ($vouchersection=='SHIPPING') || (($vouchersection=='TOTAL') && !(($differenttaxrates) && (!$specialvouchertype)))}

                {* DIFFERNETTAXRATES AND SHOWPRICES WITH TAX *}
                {if (($differenttaxrates) && ($showpriceswithtax)) }

                    {if (($vouchersection=='TOTAL') && ($specialvouchertype))}
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal">{#str_LabelSubTotal#}:</span></span>
                                <span class="order-line-price"><span class="txt_price">{$orderline.itemcompletetotal}</span></span>
                            </div>

                        {if ($orderline.itemdiscountvalueraw > 0)}
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal">{$orderline.itemdiscountname}:</span></span>
                                <span class="order-line-price"><span class="txt_price">{$orderline.itemdiscountvalue}</span></span>
                            </div>
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
                                <span class="order-line-price"><span class="txt_price">{$orderline.itemdiscountedvalue}</span></span>
                            </div>
                        {/if}
                        {* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
                        {if (($showzerotax) || ( (!$showzerotax) && ($orderline.itemtaxtotalraw>0) ) )}
                            <div class="line-sub-total"><span class="txt_includetaxtext">{$orderline.includesitemtaxtext}</span></div>
                        {/if}
                    {else}
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
                                <span class="order-line-price"><span class="txt_price">{$orderline.itemcompletetotal}</span></span>
                            </div>

                        {* SHOWTAXBREAKDOWN *}
                        {if ($showtaxbreakdown)}
                            {if (($showzerotax) || ( (!$showzerotax) && ($orderline.itemtaxtotalraw>0) ) )}
                            <div class="line-sub-total"><span class="txt_includetaxtext">{$orderline.includesitemtaxtext}</span></div>
                            {/if}
                        {/if}
                    {/if}
                {/if}

                {* DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX *}
                {if (($differenttaxrates) && (!$showpriceswithtax)) }

                    {* VALUE SET TOTAL VOUCHER *}
                    {if (($vouchersection=='TOTAL') && ($specialvouchertype))}
                            {if ($orderline.itemdiscountvalueraw > 0)}
                                <div class="line-sub-total">
                                    <span class="total-heading"><span class="txt_linetotal">{#str_LabelSubTotal#}:</span></span>
                                    <span class="order-line-price"><span class="txt_price">{$orderline.itemcompletetotal}</span></span>
                                </div>
                                <div class="line-sub-total">
                                    <span class="total-heading"><span class="txt_linetotal">{$orderline.itemdiscountname}:</span></span>
                                    <span class="order-line-price"><span class="txt_price">{$orderline.itemdiscountvalue}</span></span>
                                </div>
                            {/if}
                            {* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
                            {if (($showzerotax) || ( (!$showzerotax) && ($orderline.itemtaxtotalraw > 0) ) )}
                                <div class="line-sub-total">
                                    <span class="total-heading"><span class="txt_linetotal">{#str_LabelSubTotal#}:</span></span>
                                    <span class="order-line-price"><span class="txt_price">{$orderline.itemdiscountedvalue}</span></span>
                                </div>
                                <div class="line-sub-total">
                                    <span class="total-heading"><span class="txt_linetotal">{$orderline.itemtaxratename} ({$orderline.itemtaxrate}%):</span></span>
                                    <span class="order-line-price"><span class="txt_price">{$orderline.itemtaxtotal}</span></span>
                                </div>
                            {/if}

                    {else}

                        {* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
                        {if (($showzerotax) || ( (!$showzerotax) && ($orderline.itemtaxtotalraw>0) ) )}

                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal">{#str_LabelSubTotal#}:</span></span>
                                <span class="order-line-price"><span class="txt_price">{$orderline.itemcompletetotal}</span></span>
                            </div>
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal">{$orderline.itemtaxratename} ({$orderline.itemtaxrate}%):</span></span>
                                <span class="order-line-price"><span class="txt_price">{$orderline.itemtaxtotal}</span></span>
                            </div>
                        {/if}
                    {/if}

                        <div class="line-sub-total">
                            <span class="total-heading"><span class="txt_linetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
                            <span class="order-line-price"><span class="txt_price">{$orderline.itemtotal}</span></span>
                        </div>
                {/if}

                {* NOT DIFFERNETTAXRATES *}
                {if (!$differenttaxrates)}
                    {if (($orderline.itemvoucherapplied == 1) && ($applyVoucherAsLineDiscount == true))}
                    <div class="line-sub-total">
                        <span class="total-heading"><span class="txt_linetotal">{#str_LabelSubTotal#}:</span></span>
                        <span class="order-line-price"><span class="txt_price">{$orderline.itemsubtotal}</span></span>
                    </div>
                    <div class="line-sub-total">
                        <span class="total-heading"><span class="txt_linetotal">{$orderline.itemdiscountname}:</span></span>
                        <span class="order-line-price"><span class="txt_price">{$orderline.itemdiscountvalue}</span></span>
                    </div>
                    {/if}
                    <div class="line-sub-total">
                        <span class="total-heading"><span class="txt_linetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
                        <span class="order-line-price"><span class="txt_price">{$orderline.itemtotal}</span></span>
                    </div>

                {/if}

            {else}

                {* PRODUCT VOUCHER *}
                {if ($vouchersection=='PRODUCT')}
                    {if ($orderline.itemdiscountvalueraw > 0)}
                    <div class="line-sub-total">
                        <span class="total-heading"><span class="txt_linetotal">{#str_LabelSubTotal#}:</span></span>
                        <span class="order-line-price"><span class="txt_price">{$orderline.itemcompletetotal}</span></span>
                    </div>
                    <div class="line-sub-total">
                        <span class="total-heading"><span class="txt_linetotal">{$orderline.itemdiscountname}:</span></span>
                        <span class="order-line-price"><span class="txt_price">{$orderline.itemdiscountvalue}</span></span>
                    </div>
                    {/if}
                    {* DIFFERNETTAXRATES AND SHOWPRICES WITH TAX  *}
                    {if (($differenttaxrates) && ($showpriceswithtax)) }

                        <div class="line-sub-total">
                            <span class="total-heading"><span class="txt_linetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
                            <span class="order-line-price"><span class="txt_price">{$orderline.itemdiscountedvalue}</span></span>
                        </div>

                        {* SHOWTAXBREAKDOWN *}
                        {if ($showtaxbreakdown)}
                            {if (($showzerotax) || ( (!$showzerotax) && ($orderline.itemtaxtotalraw>0) ) )}
                            <div class="line-sub-total"><span class="txt_includetaxtext">{$orderline.includesitemtaxtext}</span></div>
                            {/if}
                        {/if}
                    {/if}

                    {* DIFFERNETTAXRATES AND DONT SHOWPRICESWITHTAX  *}
                    {if (($differenttaxrates) && (!$showpriceswithtax)) }

                        {* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
                        {if (($showzerotax) || ( (!$showzerotax) && ($orderline.itemtaxtotalraw>0) ) )}

                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal">{#str_LabelSubTotal#}:</span></span>
                                <span class="order-line-price"><span class="txt_price">{$orderline.itemdiscountedvalue}</span></span>
                            </div>
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal">{$orderline.itemtaxratename} ({$orderline.itemtaxrate}%):</span></span>
                                <span class="order-line-price"><span class="txt_price">{$orderline.itemtaxtotal}</span></span>
                            </div>
                        {/if}
                        <div class="line-sub-total">
                            <span class="total-heading"><span class="txt_linetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
                            <span class="order-line-price"><span class="txt_price">{$orderline.itemtotal}</span></span>
                        </div>
                    {/if}

                    {* NOT DIFFERNETTAXRATES *}
                    {if (!$differenttaxrates)}

                        <div class="line-sub-total">
                            <span class="total-heading"><span class="txt_linetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
                            <span class="order-line-price"><span class="txt_price">{$orderline.itemdiscountedvalue}</span></span>
                        </div>

                    {/if}

                {/if}

                {* VALUE OFF TOTAL VOUCHER *}
                {if (($vouchersection=='TOTAL') && ((($differenttaxrates) && (!$specialvouchertype)) || ($applyVoucherAsLineDiscount == true)))}
                    {if ($orderline.itemdiscountvalueraw > 0)}
                        <div class="line-sub-total">
                            <span class="total-heading"><span class="txt_linetotal">{#str_LabelSubTotal#}:</span></span>
                            <span class="order-line-price"><span class="txt_price">{$orderline.itemcompletetotal}</span></span>
                        </div>
                        <div class="line-sub-total">
                            <span class="total-heading"><span class="txt_linetotal">{$orderline.itemdiscountname}:</span></span>
                            <span class="order-line-price"><span class="txt_price">{$orderline.itemdiscountvalue}</span></span>
                        </div>
                    {/if}
                    {if (!$showpriceswithtax)}
                        {* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
                        {if (($showzerotax) || ( (!$showzerotax) && ($orderline.itemtaxtotalraw>0) ) )}
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal">{#str_LabelSubTotal#}:</span></span>
                                <span class="order-line-price"><span class="txt_price">{$orderline.itemdiscountedvalue}</span></span>
                            </div>
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal">{$orderline.itemtaxratename} ({$orderline.itemtaxrate}%):</span></span>
                                <span class="order-line-price"><span class="txt_price">{$orderline.itemtaxtotal}</span></span>
                            </div>
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
                                <span class="order-line-price"><span class="txt_price">{$orderline.itemtotal}</span></span>
                            </div>
                        {else}
                            <div class="line-sub-total">
                                <span class="total-heading"><span class="txt_linetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
                                <span class="order-line-price"><span class="txt_price">{$orderline.itemdiscountedvalue}</span></span>
                            </div>
                        {/if}
                    {else}
                        <div class="line-sub-total">
                            <span class="total-heading"><span class="txt_linetotal">{#str_LabelOrderItemListItemTotal#}:</span></span>
                            <span class="order-line-price"><span class="txt_price">{$orderline.itemtotal}</span></span>
                        </div>

                        {* SHOWZEROTAX OR DONT SHOWZEROTAX AND THE TAX > 0 *}
                        {if (($showzerotax) || ( (!$showzerotax) && ($orderline.itemtaxtotalraw>0) ) )}
                            <div class="line-sub-total"><span class="txt_includetaxtext">{$orderline.includesitemtaxtext}</span></div>
                        {/if}
                    {/if}
                {/if}
            {/if}
    </div>
    {/if}
    <span class="txt_singleLineDivider"></span>

    <div class="clear"></div>
