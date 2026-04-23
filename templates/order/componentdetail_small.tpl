<div class="contentNavigation">
    <div class="btnDoneTop" id="contentNavigationComponentDetail" data-decorator="fnDoneFromComponent" data-subcomponent="false">
       <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
       <div class="btnDone">{#str_ButtonDone#}</div>
       <div class="clear"></div>
    </div>
</div>

<div id="contentRightScrollComponentDetail_{$orderline.orderlineid}" class="contentScrollCart">

    <div class="contentVisible">
    <div class="pageLabel">
        {#str_LabelOptionsAndExtras#}
    </div>

    {if $stage == 'qty'}

     {foreach from=$orderline.checkboxes item=checkbox}

         {if $checkbox.showcomponentname==true}

             {if ($checkbox.checked == 1) && (($checkbox.metadatahtml) || ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8))}

         <div class="componentDetail outerBox" id="componentDetail_{$checkbox.orderlineid}" style="display:none;">

             <div class="sectionLabel outerBoxPadding">
                 <div class="subcomponentLabel">
                     {$checkbox.sectionlabel}
                 </div>
                 <div class="subcomponentPrice">
                     {$checkbox.totalsell}
                 </div>
                 <div class="clear"></div>
             </diV>

             <div>

                 <div id="componentRowDetail_{$checkbox.orderlineid}" class="componentBloc outerBoxPadding" >

                     {if $checkbox.haspreview > 0}

                     <img class="componentPreview" src="{$checkbox.componentpreviewsrc|escape}" alt=""/>
                     <div class="componentDetailContentText">

                     {else} {* else {if $section.haspreview > 0} *}

                     <div class="componentDetailContentTextLong">

                     {/if} {* end {if $section.haspreview > 0} *}

                     {if !empty($checkbox.info)}

                         <div class="componentDetailDescription">
                             {$checkbox.itemcomponentinfo}
                         </div>

                         {if ! empty($checkbox.itemcomponentmoreinfolinkurl)}

                          <div class="componentDescription">
                            <a href="{$checkbox.itemcomponentmoreinfolinkurl}" target="_blank">{$checkbox.itemcomponentmoreinfolinktext}</a>
                          </div>

                        {/if} {* end {if ! empty($checkbox.itemcomponentmoreinfolinkurl) *}

                         {if !empty($checkbox.itemcomponentpriceinfo)}

                         <div class="componentDescription">
                             {$checkbox.itemcomponentpriceinfo}
                         </div>

                         {/if} {* end {if !empty($section.itemcomponentpriceinfo)} *}

                     {else} {* else {if !empty($section.itemcomponentinfo)} *}

                         {if ! empty($checkbox.itemcomponentmoreinfolinkurl)}

                          <div class="componentDescription">
                            <a href="{$checkbox.itemcomponentmoreinfolinkurl}" target="_blank">{$checkbox.itemcomponentmoreinfolinktext}</a>
                          </div>

                         {/if} {* end {if ! empty($checkbox.itemcomponentmoreinfolinkurl) *}

                         {if !empty($checkbox.itemcomponentpriceinfo)}

                             <div class="componentDetailDescription">
                                 {$checkbox.itemcomponentpriceinfo}
                             </div>

                         {/if} {* end {if !empty($section.itemcomponentpriceinfo)} *}

                     {/if} {* end {if !empty($section.itemcomponentinfo)} *}

                     </div> <!-- componentDetailContentTextLong OR componentDetailContentText -->

                     <div class="clear"></div>

                 </div> <!-- componentBloc outerBoxPadding -->

                 {if ($checkbox.pricingmodel == 7) || ($checkbox.pricingmodel == 8)}

                 <div class="itemSection outerBoxPadding">
                    <div class="itemSectionLabel">
                        <div class="formLine1">
                             <label for="itemqty_{$checkbox.orderlineid}">{#str_LabelQuantity#}</label>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="itemQuantityNumber">

                         <div class="formLine2">

                            <input id="hiddeqty_{$checkbox.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$checkbox.quantity}"/>

                    {if empty($checkbox.itemqtydropdown)}

                            <input id="itemqty_{$checkbox.orderlineid}" type="number" class="quantityInput" maxlength="8" value="{$checkbox.quantity}" data-decorator="fnUpdateComponentQty" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" data-trigger="keyup" />
                            <img class="refresh" src="{$brandroot}/images/icons/refresh.png" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" data-decorator="fnUpdateComponentQty" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" data-trigger="click" />
                            <div class="clear"></div>

                    {else} {* else {if empty($section.itemqtydropdown)} *}

                            <select id="itemqty_{$checkbox.orderlineid}" data-decorator="fnUpdateComponentQty" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" data-trigger="change">

                        {foreach from=$checkbox.itemqtydropdown item=qtyValue}

                                <option {if $qtyValue == $checkbox.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>

                        {/foreach} {* end {foreach from=$section.itemqtydropdown item=qtyValue} *}

                            </select>

                    {/if} {* end {if empty($section.itemqtydropdown)} *}

                        </div>
                        <div class="clear"></div>

                     </div> <!-- itemQuantityNumber -->

                     <div class="clear"></div>

                 </div> <!-- itemSection outerBoxPadding -->

                 {/if}

                 {if $checkbox.metadatahtml}

                 <div class="componentMetadata outerBoxPadding metadataId{$checkbox.orderlineid}">
                     {$checkbox.metadatahtml}
                 </div>

                 {/if}

             </div> <!-- componentContentDetail_XXXXX -->

         </div> <!-- componentDetail -->

             {/if}

         {/if}

     {/foreach} {* end {foreach from=$orderline.checkboxes item=checkbox} *}

     <!-- END CHECKBOX -->


     <!-- COMPONENT DETAIL -->

     {foreach from=$orderline.sections item=section} {* sections *}

         {if $section.showcomponentname == true}

             {if (($section.pricingmodel == 7) || ($section.pricingmodel == 8)) || ($section.subsections|@sizeof > 0)
                 || ($section.itemcomponentbuttons|@sizeof > 0) || ($section.metadatahtml) || ($section.checkboxes|@sizeof > 0)
                 || ($subsection.metadatahtml)}

     <div id="componentDetail_{$section.orderlineid}" class="componentDetail">

         <div class="outerBox">

             <div>

                 <div class="sectionLabel outerBoxPadding">
                     <div class="subcomponentLabel">
                         {$section.itemcomponentname}
                     </div>
                     <div class="subcomponentPrice">
                         {$section.totalsell}
                     </div>
                     <div class="clear"></div>
                 </diV>

                 <div id="componentContentDetail_{$section.orderlineid}">

                     <div id="componentRowDetail_{$section.orderlineid}" class="componentBloc outerBoxPadding">

                     {if $section.haspreview > 0}

                         <img class="componentPreview" src="{$section.componentpreviewsrc|escape}" alt=""/>
                         <div class="componentDetailContentText">

                     {else} {* else {if $section.haspreview > 0} *}

                         <div class="componentDetailContentTextLong">

                     {/if} {* end {if $section.haspreview > 0} *}

                     {if !empty($section.itemcomponentinfo)}

                             <div class="componentDetailDescription">
                                 {$section.itemcomponentinfo}
                             </div>

                         {if ! empty($section.itemcomponentmoreinfolinkurl)}

                            <div class="componentDescription">
                              <a href="{$section.itemcomponentmoreinfolinkurl}" target="_blank">{$section.itemcomponentmoreinfolinktext}</a>
                            </div>

                         {/if} {* end {if ! empty($section.itemcomponentmoreinfolinkurl) *}

                         {if !empty($section.itemcomponentpriceinfo)}

                             <div class="componentDescription">
                                 {$section.itemcomponentpriceinfo}
                             </div>

                         {/if} {* end {if !empty($section.itemcomponentpriceinfo)} *}

                     {else} {* else {if !empty($section.itemcomponentinfo)} *}

                          {if ! empty($section.itemcomponentmoreinfolinkurl)}

                            <div class="componentDescription">
                              <a href="{$section.itemcomponentmoreinfolinkurl}" target="_blank">{$section.itemcomponentmoreinfolinktext}</a>
                            </div>

                         {/if} {* end {if ! empty($section.itemcomponentmoreinfolinkurl) *}

                         {if !empty($section.itemcomponentpriceinfo)}

                             <div class="componentDetailDescription">
                                 {$section.itemcomponentpriceinfo}
                             </div>

                         {/if} {* end {if !empty($section.itemcomponentpriceinfo)} *}

                     {/if} {* end {if !empty($section.itemcomponentinfo)} *}

                         </div> <!-- componentDetailContentTextLong OR componentDetailContentText -->

                         <div class="clear"></div>

                     </div> <!-- componentBloc outerBoxPadding -->

                     {if ($section.pricingmodel == 7) || ($section.pricingmodel == 8)}

                     <div class="itemSection outerBoxPadding">
                        <div class="itemSectionLabel">
                            <div class="formLine1">
                                  <label for="itemqty_{$section.orderlineid}">{#str_LabelQuantity#}</label>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="itemQuantityNumber">

                             <div class="formLine2">

                                <input id="hiddeqty_{$section.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$section.quantity}"/>

                        {if empty($section.itemqtydropdown)}

                                <input id="itemqty_{$section.orderlineid}" type="number" class="quantityInput" maxlength="8" value="{$section.quantity}" data-decorator="fnUpdateComponentQty" data-lineid="{$section.orderlineid}" data-itemqty="{$section.itemqty}" data-trigger="keyup" />
                                <img class="refresh" src="{$brandroot}/images/icons/refresh.png" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" data-decorator="fnUpdateComponentQty" data-lineid="{$section.orderlineid}" data-itemqty="{$section.itemqty}" data-trigger="click" />
                                <div class="clear"></div>

                        {else} {* else {if empty($section.itemqtydropdown)} *}

                                <select id="itemqty_{$section.orderlineid}" data-decorator="fnUpdateComponentQty" data-lineid="{$section.orderlineid}" data-itemqty="{$section.itemqty}" data-trigger="change" >

                            {foreach from=$section.itemqtydropdown item=qtyValue}

                                    <option {if $qtyValue == $section.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>

                            {/foreach} {* end {foreach from=$section.itemqtydropdown item=qtyValue} *}

                                </select>

                        {/if} {* end {if empty($section.itemqtydropdown)} *}

                            </div>
                            <div class="clear"></div>

                         </div> <!-- itemQuantityNumber -->

                         <div class="clear"></div>

                     </div> <!-- itemSection outerBoxPadding -->

                 {/if}

                 </div> <!-- componentContentDetail_XXXXX -->

             </div>

                {if ($section.itemcomponentbuttons|@sizeof > 0)}

            <div class="contentChangeBtn outerBoxPadding" data-decorator="fnChangeComponent" data-orderlineid="{$orderline.orderlineid}" data-sectionlineid="{$section.orderlineid}">
                <div class="changeBtnText">
                    {#str_LabelChange#}
                </div>
                <div class="changeBtnImg">
                    <img alt="&gt;" src="{$webroot}/images/icons/change-arrow.png" class="navigationArrow">
                </div>
                <div class="clear"></div>
            </div>

                {/if} {* end {if ($section.itemcomponentbuttons|@sizeof > 0)} *}

                 {if $section.metadatahtml}

             <div class="componentMetadata outerBoxPadding metadataId{$section.orderlineid}">
                 {$section.metadatahtml}
             </div>

                 {/if}

         </div> <!-- componentDetail -->

         {if ($section.subsections|@sizeof > 0) || ($section.checkboxes|@sizeof > 0)}

         <div class="contenSubComponent">

             <!-- SUBCOMPONENT -->

             {foreach from=$section.subsections item=subsection} {* subsections of a section *}

                 {if $subsection.showcomponentname == true}

             <div id="componentContent_{$section.orderlineid}_{$subsection.orderlineid}" class="contentSubComponentBloc outerBox outerBoxMarginBottom outerBoxMarginTop">
                 <div class="sectionLabel outerBoxPadding">
                     <div class="subcomponentLabel">
                         {$subsection.sectionlabel}
                     </div>
                     <div class="subcomponentPrice">
                         {$subsection.totalsell}
                     </div>
                     <div class="clear"></div>
                 </diV>

                 <div>

                     <div id="componentrow_{$subsection.orderlineid}" class="componentBloc outerBoxPadding" >

                     {if $subsection.haspreview > 0}

                         <img class="componentPreview" src="{$subsection.componentpreviewsrc|escape}" alt=""/>
                         <div class="componentDetailContentText">

                     {else} {* else {if $section.haspreview > 0} *}

                         <div class="componentDetailContentTextLong">

                     {/if} {* end {if $section.haspreview > 0} *}


                             <div class="componentTitle">{$subsection.itemcomponentname}</div>

                     {if !empty($subsection.itemcomponentinfo)}

                             <div class="componentDescription">
                                 {$subsection.itemcomponentinfo}
                             </div>

                     {/if}{* end {if !empty($section.itemcomponentinfo)} *}

                     {if ! empty($subsection.itemcomponentmoreinfolinkurl)}

                            <div class="componentDescription">
                              <a href="{$subsection.itemcomponentmoreinfolinkurl}" target="_blank">{$subsection.itemcomponentmoreinfolinktext}</a>
                            </div>

                     {/if} {* end {if ! empty($subsection.itemcomponentmoreinfolinkurl) *}

                     {if !empty($subsection.itemcomponentpriceinfo)}

                             <div class="componentDescription">
                                 {$subsection.itemcomponentpriceinfo}
                             </div>

                     {/if} {* end {if !empty($section.itemcomponentpriceinfo)} *}

                     {if ($subsection.pricingmodel == 7) || ($subsection.pricingmodel == 8)}

                             <ul class="componentList">
                                 <li>{#str_LabelQuantity#}: <span class="componentListNumber">{$subsection.quantity}</span></li>
                             </ul>

                     {/if} {* {if ($section.pricingmodel == 7) || ($section.pricingmodel == 8)} *}

                         </div>
                         <div class="clear"></div>
                     </div>

                     {if $stage == 'qty'}

                         {if (($subsection.pricingmodel == 7) || ($subsection.pricingmodel == 8))
                             || ($subsection.itemcomponentbuttons|@sizeof > 0) || ($subsection.metadatahtml)}

                     <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="subComponentView|{$orderline.orderlineid}|{$section.orderlineid}|{$subsection.orderlineid}">
                         <div class="changeBtnText">
                             {#str_LabelOptionsAndExtras#}

                             {if $subsection.isonekeywordmandatory == true}

                             <img class="valueRequiredImg" src="{$brandroot}/images/asterisk.png" alt="*" />

                            {/if} {*end {if $subsection.isonekeywordmandatory == true} *}

                         </div>
                         <div class="changeBtnImg">
                             <img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt= ">" />
                         </div>
                         <div class="clear"></div>
                     </div>

                         {/if} {* end {if $stage == 'qty'} *}

                     {/if} {* end {if (($section.pricingmodel == 7) || ($section.pricingmodel == 8))
                                 || ($section.itemcomponentbuttons|@sizeof > 0) || ($subsection.metadatahtml)} *}

                 </div> <!-- componentBloc outerBoxPadding -->

             </div>

                 {/if} {* end {if $section.showcomponentname == true} *}

             {/foreach} {* end {foreach from=$section.subsections item=subsection} *}

             <!-- END SUBCOMPONENT -->

             <!-- CHECKBOXES INSIDE COMPONENT -->

             {foreach from=$section.checkboxes item=checkbox} {* checkboxes of a section *}

                 {if $checkbox.showcomponentname==true}

                     {if ((($stage == 'payment') && ($checkbox.checked == 1)) || $stage == 'qty')}

             <div id="componentContent_{$section.orderlineid}_{$checkbox.orderlineid}" class="contentSubComponentBloc outerBox outerBoxMarginBottom outerBoxMarginTop">
                 <div class="sectionLabel outerBoxPadding">
                     <div class="subcomponentLabel">
                         {$checkbox.itemcomponentcategoryname}
                     </div>

                         {if ($checkbox.checked) || ($checkbox.totalsell == #str_LabelNotAvailable#)}

                     <div class="subcomponentPrice">
                         {$checkbox.totalsell}
                     </div>

                         {/if} {* end {if $checkbox.checked || $checkbox.totalsell == #str_LabelNotAvailable#} *}

                     <div class="clear"></div>
                 </diV>

                 <div class="componentBloc outerBoxPadding">

                        {if ($stage == 'qty')}

                     <div class="checkboxBloc">

                        {else}

                    <div>

                        {/if}

                         {if $checkbox.haspreview > 0}

                         <img class="componentPreview" src="{$checkbox.componentpreviewsrc|escape}" alt=""/>
                         <div class="componentDetailContentText">

                         {else} {* else {if $checkbox.haspreview > 0} *}

                         <div class="componentDetailContentTextLong">

                         {/if} {* end {if $checkbox.haspreview > 0} *}

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

                         {/if} {* end {if ! empty($checkbox.itemcomponentmoreinfolinkurl) *}

                         {if !empty($checkbox.itemcomponentpriceinfo)}

                             <div class="componentDescription">
                                 {$checkbox.itemcomponentpriceinfo}
                             </div>

                         {/if}

                         {if ($stage == 'qty') && (!$checkbox.checked)}

                             <div class="componentCheckBoxPrice">
                                 ({$checkbox.totalsell})
                             </div>

                         {/if} {* end {if ($stage == 'qty') && (!$checkbox.checked)} *}

                         {if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8) && $checkbox.checked == 1}

                             <ul class="componentList">
                                 <li>{#str_LabelQuantity#}: <span class="componentListNumber">{$checkbox.quantity}</span></li>
                             </ul>

                         {/if} {* end {if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8) && $checkbox.checked == 1} *}

                         </div>
                         <div class="clear"></div>
                     </div>

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
                     </div>

                             {/foreach} {* end {foreach from=$checkbox.itemcomponentbuttons item=button} *}

                         {/if} {* end {if $stage=='qty'} *}

                     <div class="clear"></div>
                 </div>
                 <div class="clear"></div>

                         {if $stage == 'qty'}

                             {if ($checkbox.checked == 1) && (($checkbox.metadatahtml) || ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8))}

                 <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="subComponentView|{$orderline.orderlineid}|{$section.orderlineid}|{$checkbox.orderlineid}">
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
                 </div>

                             {/if} {* end {if ($subsection.metadatahtml) && ($checkbox.checked == 1)} *}

                         {/if} {* end {if $stage == 'qty'} *}

             </div>

                     {/if}  {* end  {if (($stage=='payment' && $checkbox.checked == 1) || $stage=='qty')} *}

                 {/if}

             {/foreach} {* end {foreach from=$section.checkboxes item=checkbox} *}

             <!-- END CHECKBOXES INSIDE COMPONENT -->

         </div> <!-- contentSubComponent -->

             {/if} {* end {if ($section.subsections|@sizeof > 0) || ($section.checkboxes|@sizeof > 0)} *}

     </div> <!-- componentDetail_XXXXX -->

             {/if} {* end {if (($section.pricingmodel == 7) || ($section.pricingmodel == 8)) || ($section.subsections|@sizeof > 0)
                     || ($section.itemcomponentbuttons|@sizeof > 0) || ($section.metadatahtml) || ($section.checkboxes|@sizeof > 0)
                     || ($subsection.metadatahtml)} *}

         {/if} {* end {if $section.showcomponentname == true} *}

     {/foreach} {* sections *}

     <!-- LINEFOOTER SECTIONS -->

     {if $orderline.orderlineid != -1}

         {if $orderline.linefootersections|@sizeof > 0}

             {foreach from=$orderline.linefootersections item=section} {* linefooter sections *}

                 {if $section.showcomponentname==true}

                     {if (($section.pricingmodel == 7) || ($section.pricingmodel == 8)) || ($section.subsections|@sizeof > 0)
                         || ($section.itemcomponentbuttons|@sizeof > 0) || ($section.metadatahtml) || ($section.checkboxes|@sizeof > 0)}

    <div id="componentDetail_{$section.orderlineid}" class="componentDetail">

         <div class="outerBox">

             <div>

                 <div class="sectionLabel outerBoxPadding">
                     <div class="subcomponentLabel">
                         {$section.itemcomponentname}
                     </div>
                     <div class="subcomponentPrice">
                         {$section.totalsell}
                     </div>
                     <div class="clear"></div>
                 </diV>

                 <div id="componentContentDetail_{$section.orderlineid}">

                     <div id="componentRowDetail_{$section.orderlineid}" class="componentBloc outerBoxPadding" >

                     {if $section.haspreview > 0}

                         <img class="componentPreview" src="{$section.componentpreviewsrc|escape}" alt=""/>
                         <div class="componentDetailContentText">

                     {else} {* else {if $section.haspreview > 0} *}

                         <div class="componentDetailContentTextLong">

                     {/if} {* end {if $section.haspreview > 0} *}

                     {if !empty($section.itemcomponentinfo)}

                             <div class="componentDetailDescription">
                                 {$section.itemcomponentinfo}
                             </div>

                         {if ! empty($section.itemcomponentmoreinfolinkurl)}

                            <div class="componentDescription">
                              <a href="{$section.itemcomponentmoreinfolinkurl}" target="_blank">{$section.itemcomponentmoreinfolinktext}</a>
                            </div>

                         {/if} {* end {if ! empty($section.itemcomponentmoreinfolinkurl) *}

                         {if !empty($section.itemcomponentpriceinfo)}

                             <div class="componentDescription">
                                 {$section.itemcomponentpriceinfo}
                             </div>

                         {/if} {* end {if !empty($section.itemcomponentpriceinfo)} *}

                     {else} {* else {if !empty($section.itemcomponentinfo)} *}

                          {if ! empty($section.itemcomponentmoreinfolinkurl)}

                            <div class="componentDescription">
                              <a href="{$section.itemcomponentmoreinfolinkurl}" target="_blank">{$section.itemcomponentmoreinfolinktext}</a>
                            </div>

                         {/if} {* end {if ! empty($section.itemcomponentmoreinfolinkurl) *}

                         {if !empty($section.itemcomponentpriceinfo)}

                                 <div class="componentDetailDescription">
                                     {$section.itemcomponentpriceinfo}
                                 </div>

                         {/if} {* end {if !empty($section.itemcomponentpriceinfo)} *}

                     {/if} {* end {if !empty($section.itemcomponentinfo)} *}

                         </div> <!-- componentDetailContentTextLong OR componentDetailContentText -->

                         <div class="clear"></div>

                     </div> <!-- componentBloc outerBoxPadding -->

                     {if ($section.pricingmodel == 7) || ($section.pricingmodel == 8)}

                     <div class="itemSection outerBoxPadding">
                         <div class="itemSectionLabel">
                            <div class="formLine1">
                                <label for="itemqty_{$section.orderlineid}">{#str_LabelQuantity#}</label>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="itemQuantityNumber">

                             <div class="formLine2">

                                <input id="hiddeqty_{$section.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$section.quantity}"/>

                        {if empty($section.itemqtydropdown)}

                                <input id="itemqty_{$section.orderlineid}" type="number" class="quantityInput" maxlength="8" value="{$section.quantity}" data-decorator="fnUpdateComponentQty" data-lineid="{$section.orderlineid}" data-itemqty="{$section.itemqty}" data-trigger="keyup" />
                                <img class="refresh" src="{$brandroot}/images/icons/refresh.png" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" data-decorator="fnUpdateComponentQty" data-lineid="{$section.orderlineid}" data-itemqty="{$section.itemqty}" data-trigger="click"/>
                                <div class="clear"></div>

                        {else} {* else {if empty($section.itemqtydropdown)} *}

                                <select id="itemqty_{$section.orderlineid}" data-decorator="fnUpdateComponentQty" data-lineid="{$section.orderlineid}" data-itemqty="{$section.itemqty}" data-trigger="change">

                            {foreach from=$section.itemqtydropdown item=qtyValue}

                                    <option {if $qtyValue == $section.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>

                            {/foreach} {* end {foreach from=$section.itemqtydropdown item=qtyValue} *}

                                </select>

                        {/if} {* end {if empty($section.itemqtydropdown)} *}

                            </div>
                            <div class="clear"></div>

                         </div> <!-- itemQuantityNumber -->

                         <div class="clear"></div>

                     </div> <!-- itemSection outerBoxPadding -->

                 {/if}

                 </div> <!-- componentContentDetail_XXXXX -->

             </div>

                {if ($section.itemcomponentbuttons|@sizeof > 0)}

            <div class="contentChangeBtn outerBoxPadding" data-decorator="fnChangeComponent" data-orderlineid="{$orderline.orderlineid}" data-sectionlineid="{$section.orderlineid}">
                <div class="changeBtnText">
                    {#str_LabelChange#}
                </div>
                <div class="changeBtnImg">
                    <img alt="&gt;" src="{$webroot}/images/icons/change-arrow.png" class="navigationArrow">
                </div>
                <div class="clear"></div>
            </div>

                {/if} {* end {if ($section.itemcomponentbuttons|@sizeof > 0)} *}

                {if $section.metadatahtml}

             <div class="componentMetadata outerBoxPadding metadataId{$section.orderlineid}">
                 {$section.metadatahtml}
             </div>

                {/if}

         </div> <!-- componentDetail -->

         {if ($section.subsections|@sizeof > 0) || ($section.checkboxes|@sizeof > 0)}

         <div class="contenSubComponent">

             <!-- SUBCOMPONENT -->

             {foreach from=$section.subsections item=subsection} {* subsections of a section *}

                 {if $subsection.showcomponentname == true}

             <div id="componentContent_{$section.orderlineid}_{$subsection.orderlineid}" class="contentSubComponentBloc outerBox outerBoxMarginBottom outerBoxMarginTop">
                 <div class="sectionLabel outerBoxPadding">
                     <div class="subcomponentLabel">
                         {$subsection.sectionlabel}
                     </div>

                     <div class="subcomponentPrice">
                         {$subsection.totalsell}
                     </div>
                     <div class="clear"></div>
                 </diV>

                 <div>

                     <div id="componentrow_{$subsection.orderlineid}" class="componentBloc outerBoxPadding" >

                     {if $subsection.haspreview > 0}

                         <img class="componentPreview" src="{$subsection.componentpreviewsrc|escape}" alt=""/>
                         <div class="componentDetailContentText">

                     {else} {* else {if $section.haspreview > 0} *}

                         <div class="componentDetailContentTextLong">

                     {/if} {* end {if $section.haspreview > 0} *}


                             <div class="componentTitle">{$subsection.itemcomponentname}</div>

                     {if !empty($subsection.itemcomponentinfo)}

                             <div class="componentDescription">
                                 {$subsection.itemcomponentinfo}
                             </div>

                     {/if}{* end {if !empty($section.itemcomponentinfo)} *}

                     {if ! empty($subsection.itemcomponentmoreinfolinkurl)}

                            <div class="componentDescription">
                              <a href="{$subsection.itemcomponentmoreinfolinkurl}" target="_blank">{$subsection.itemcomponentmoreinfolinktext}</a>
                            </div>

                     {/if} {* end {if ! empty($subsection.itemcomponentmoreinfolinkurl) *}

                     {if !empty($subsection.itemcomponentpriceinfo)}

                             <div class="componentDescription">
                                 {$subsection.itemcomponentpriceinfo}
                             </div>

                     {/if} {* end {if !empty($section.itemcomponentpriceinfo)} *}

                     {if ($subsection.pricingmodel == 7) || ($subsection.pricingmodel == 8)}

                             <ul class="componentList">
                                 <li>{#str_LabelQuantity#}: <span class="componentListNumber">{$subsection.quantity}</span></li>
                             </ul>

                     {/if} {* {if ($section.pricingmodel == 7) || ($section.pricingmodel == 8)} *}

                         </div>
                         <div class="clear"></div>
                     </div>

                     {if $stage == 'qty'}

                         {if (($subsection.pricingmodel == 7) || ($subsection.pricingmodel == 8))
                             || ($subsection.itemcomponentbuttons|@sizeof > 0) || ($subsection.metadatahtml)}

                     <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="subComponentView|{$orderline.orderlineid}|{$section.orderlineid}|{$subsection.orderlineid}">
                         <div class="changeBtnText">
                             {#str_LabelOptionsAndExtras#}

                             {if $subsection.isonekeywordmandatory == true}

                             <img class="valueRequiredImg" src="{$brandroot}/images/asterisk.png" alt="*" />

                            {/if} {* end {if $subsection.isonekeywordmandatory == true} *}

                         </div>
                         <div class="changeBtnImg">
                             <img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt= ">" />
                         </div>
                         <div class="clear"></div>
                     </div>

                         {/if} {* end {if $stage == 'qty'} *}

                     {/if} {* end {if (($section.pricingmodel == 7) || ($section.pricingmodel == 8))
                                 || ($section.itemcomponentbuttons|@sizeof > 0) || ($subsection.metadatahtml)} *}

                 </div> <!-- componentBloc outerBoxPadding -->

             </div>

                 {/if} {* end {if $section.showcomponentname == true} *}

             {/foreach} {* end {foreach from=$section.subsections item=subsection} *}

             <!-- END SUBCOMPONENT -->

             <!-- CHECKBOXES INSIDE COMPONENT -->

             {foreach from=$section.checkboxes item=checkbox} {* checkboxes of a section *}

                 {if $checkbox.showcomponentname==true}

                     {if ((($stage == 'payment') && ($checkbox.checked == 1)) || $stage == 'qty')}

             <div id="componentContent_{$section.orderlineid}_{$checkbox.orderlineid}" class="contentSubComponentBloc outerBox outerBoxMarginBottom outerBoxMarginTop">
                 <div class="sectionLabel outerBoxPadding">
                     <div class="subcomponentLabel">
                        {$checkbox.itemcomponentcategoryname}
                     </div>

                         {if ($checkbox.checked) || ($checkbox.totalsell == #str_LabelNotAvailable#)}

                     <div class="subcomponentPrice">
                         {$checkbox.totalsell}
                     </div>

                         {/if} {* end {if $checkbox.checked || $checkbox.totalsell == #str_LabelNotAvailable#} *}

                     <div class="clear"></div>
                 </diV>

                 <div class="componentBloc outerBoxPadding">

                        {if ($stage == 'qty')}

                     <div class="checkboxBloc">

                        {else}

                    <div>

                        {/if}

                         {if $checkbox.haspreview > 0}

                         <img class="componentPreview" src="{$checkbox.componentpreviewsrc|escape}" alt=""/>
                         <div class="componentDetailContentText">

                         {else} {* else {if $checkbox.haspreview > 0} *}

                         <div class="componentDetailContentTextLong">

                         {/if} {* end {if $checkbox.haspreview > 0} *}

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

                         {/if} {* end {if ! empty($checkbox.itemcomponentmoreinfolinkurl) *}

                         {if !empty($checkbox.itemcomponentpriceinfo)}

                             <div class="componentDescription">
                                 {$checkbox.itemcomponentpriceinfo}
                             </div>

                         {/if}

                         {if ($stage == 'qty') && (!$checkbox.checked)}

                             <div class="componentCheckBoxPrice">
                                 ({$checkbox.totalsell})
                             </div>

                         {/if} {* end {if ($stage == 'qty') && (!$checkbox.checked)} *}

                         {if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8) && $checkbox.checked == 1}

                             <ul class="componentList">
                                 <li>{#str_LabelQuantity#}: <span class="componentListNumber">{$checkbox.quantity}</span></li>
                             </ul>

                         {/if} {* end {if ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8) && $checkbox.checked == 1} *}

                         </div>
                         <div class="clear"></div>
                     </div>

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
                     </div>

                             {/foreach} {* end {foreach from=$checkbox.itemcomponentbuttons item=button} *}

                         {/if} {* end {if $stage=='qty'} *}

                     <div class="clear"></div>
                 </div>
                 <div class="clear"></div>

                         {if $stage == 'qty'}

                             {if ($checkbox.checked == 1) && (($checkbox.metadatahtml) || ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8))}

                 <div class="contentChangeBtn outerBoxPadding" data-decorator="fnSetHashUrl" data-hash-url="subComponentView|{$orderline.orderlineid}|{$section.orderlineid}|{$checkbox.orderlineid}">
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
                 </div>

                             {/if} {* end {if ($subsection.metadatahtml) && ($checkbox.checked == 1)} *}

                         {/if} {* end {if $stage == 'qty'} *}

             </div>

                     {/if}  {* end  {if (($stage=='payment' && $checkbox.checked == 1) || $stage=='qty')} *}

                 {/if}

             {/foreach} {* end {foreach from=$section.checkboxes item=checkbox} *}

             <!-- END CHECKBOXES INSIDE COMPONENT -->

         </div> <!-- contentSubComponent -->

             {/if} {* end {if ($section.subsections|@sizeof > 0) || ($section.checkboxes|@sizeof > 0)} *}

     </div> <!-- componentDetail_XXXXX -->

         {/if} {* end {if (($section.pricingmodel == 7) || ($section.pricingmodel == 8)) || ($section.subsections|@sizeof > 0)
                     || ($section.itemcomponentbuttons|@sizeof > 0) || ($section.metadatahtml) || ($section.checkboxes|@sizeof > 0)
                     || ($subsection.metadatahtml)} *}

     {/if} {* end {if $section.showcomponentname == true} *}

    {/foreach} {* sections *}

    {/if}

    {/if}

     <!-- END LINEFOOTER SECTIONS -->

    <!-- LINEFOOTER CHECKBOXES -->

    {foreach from=$orderline.linefootercheckboxes item=checkbox}

         {if $checkbox.showcomponentname==true}

             {if ($checkbox.checked == 1) && (($checkbox.metadatahtml) || ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8))}

         <div class="componentDetail outerBox" id="componentDetail_{$checkbox.orderlineid}" style="display:none;">

             <div class="sectionLabel outerBoxPadding">
                 <div class="subcomponentLabel">
                     {$checkbox.sectionlabel}tttt
                 </div>
                 <div class="subcomponentPrice">
                     {$checkbox.totalsell}
                 </div>
                 <div class="clear"></div>
             </diV>

             <div>

                 <div id="componentRowDetail_{$checkbox.orderlineid}" class="componentBloc outerBoxPadding" >

                     {if $checkbox.haspreview > 0}

                     <img class="componentPreview" src="{$checkbox.componentpreviewsrc|escape}" alt=""/>
                     <div class="componentDetailContentText">

                     {else} {* else {if $section.haspreview > 0} *}

                     <div class="componentDetailContentTextLong">

                     {/if} {* end {if $section.haspreview > 0} *}

                     {if !empty($checkbox.info)}

                         <div class="componentDetailDescription">
                             {$checkbox.itemcomponentinfo}
                         </div>

                         {if ! empty($checkbox.itemcomponentmoreinfolinkurl)}

                            <div class="componentDescription">
                              <a href="{$checkbox.itemcomponentmoreinfolinkurl}" target="_blank">{$checkbox.itemcomponentmoreinfolinktext}</a>
                            </div>

                         {/if} {* end {if ! empty($checkbox.itemcomponentmoreinfolinkurl) *}

                         {if !empty($checkbox.itemcomponentpriceinfo)}

                         <div class="componentDescription">
                             {$checkbox.itemcomponentpriceinfo}
                         </div>

                         {/if} {* end {if !empty($section.itemcomponentpriceinfo)} *}

                     {else} {* else {if !empty($section.itemcomponentinfo)} *}

                          {if ! empty($checkbox.itemcomponentmoreinfolinkurl)}

                            <div class="componentDetailDescription">
                              <a href="{$checkbox.itemcomponentmoreinfolinkurl}" target="_blank">{$checkbox.itemcomponentmoreinfolinktext}</a>
                            </div>

                         {/if} {* end {if ! empty($checkbox.itemcomponentmoreinfolinkurl) *}

                         {if !empty($checkbox.itemcomponentpriceinfo)}

                             <div class="componentDetailDescription">
                                 {$checkbox.itemcomponentpriceinfo}
                             </div>

                         {/if} {* end {if !empty($section.itemcomponentpriceinfo)} *}

                     {/if} {* end {if !empty($section.itemcomponentinfo)} *}

                     </div> <!-- componentDetailContentTextLong OR componentDetailContentText -->

                     <div class="clear"></div>

                 </div> <!-- componentBloc outerBoxPadding -->

                 {if ($checkbox.pricingmodel == 7) || ($checkbox.pricingmodel == 8)}

                 <div class="itemSection outerBoxPadding">
                     <div class="itemSectionLabel">
                        <div class="formLine1">
                             <label for="itemqty_{$checkbox.orderlineid}">{#str_LabelQuantity#}</label>
                        </div>
                        <div class="clear"></div>
                     </div>
                     <div class="itemQuantityNumber">

                         <div class="formLine2">

                            <input id="hiddeqty_{$checkbox.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$checkbox.quantity}"/>

                    {if empty($checkbox.itemqtydropdown)}

                            <input id="itemqty_{$checkbox.orderlineid}" type="number" class="quantityInput" maxlength="8" value="{$checkbox.quantity}" data-decorator="fnUpdateComponentQty" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" data-trigger="keyup" />
                            <img class="refresh" src="{$brandroot}/images/icons/refresh.png" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}"  data-decorator="fnUpdateComponentQty" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" data-trigger="click" />
                            <div class="clear"></div>

                                {else} {* else {if empty($section.itemqtydropdown)} *}

                            <select id="itemqty_{$checkbox.orderlineid}" data-decorator="fnUpdateComponentQty" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" data-trigger="change">

                                    {foreach from=$checkbox.itemqtydropdown item=qtyValue}

                                <option {if $qtyValue == $checkbox.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>

                                    {/foreach} {* end {foreach from=$section.itemqtydropdown item=qtyValue} *}

                            </select>

                    {/if} {* end {if empty($section.itemqtydropdown)} *}

                        </div>
                        <div class="clear"></div>

                     </div> <!-- itemQuantityNumber -->

                     <div class="clear"></div>

                 </div> <!-- itemSection outerBoxPadding -->

                 {/if}

                 {if $checkbox.metadatahtml}

                 <div class="componentMetadata outerBoxPadding metadataId{$checkbox.orderlineid}">
                     {$checkbox.metadatahtml}
                 </div>

                 {/if}

             </div> <!-- componentContentDetail_XXXXX -->

         </div> <!-- componentDetail -->

             {/if}

         {/if}

     {/foreach} {* end {foreach from=$orderline.linefootercheckboxes item=checkbox} *}

    <!-- END LINEFOOTER CHECKBOXES -->


    {/if} {* end {if $stage == 'qty'} *}

    </div> <!-- contentVisible -->

</div>  <!-- contentScrollCart -->

<!-- END COMPONENT DETAIL -->