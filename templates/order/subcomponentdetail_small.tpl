<div class="contentNavigation" id="contentNavigationSubComponentDetail">

    <div class="btnDoneTop" data-decorator="fnDoneFromComponent" data-subcomponent="true">
       <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
       <div class="btnDone">{#str_ButtonDone#}</div>
       <div class="clear"></div>
    </div>

</div>

<div id="contentRightScrollSubComponentDetail_{$orderline.orderlineid}" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">
            {#str_LabelOptionsAndExtras#}
        </div>


     {foreach from=$orderline.sections item=section}

         {if $section.showcomponentname == true}

            <!-- SUBCOMPONENT -->

            {foreach from=$section.subsections item=subsection}

                 {if $subsection.showcomponentname == true}

                     {if (($subsection.pricingmodel == 7) || ($subsection.pricingmodel == 8))
                         || ($subsection.itemcomponentbuttons|@sizeof > 0) || ($subsection.metadatahtml)}

         <div class="componentDetail outerBox outerBoxMarginBottom" id="subcomponentDetail_{$subsection.orderlineid}" style="display:none;">

             <div>

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

                     <div id="componentRowDetail_{$subsection.orderlineid}" class="componentBloc outerBoxPadding" >

                         {if $subsection.haspreview > 0}

                         <img class="componentPreview" src="{$subsection.componentpreviewsrc|escape}" alt=""/>
                         <div class="componentDetailContentText">

                         {else} {* else {if $section.haspreview > 0} *}

                         <div class="componentDetailContentTextLong">

                         {/if} {* end {if $section.haspreview > 0} *}

                         {if !empty($subsection.info)}

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

                         </div> <!-- componentDetailContentTextLong OR componentDetailContentText -->

                         <div class="clear"></div>

                     </div> <!-- componentBloc innerBoxPadding -->

                     {if ($subsection.pricingmodel == 7) || ($subsection.pricingmodel == 8)}

                     <div class="itemSection outerBoxPadding">
                         <div class="itemSectionLabel">
                            <div class="formLine1">
                                <label for="itemqty_{$subsection.orderlineid}">{#str_LabelQuantity#}</label>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="itemQuantityNumber">

                             <div class="formLine2">

                                <input id="hiddeqty_{$subsection.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$subsection.quantity}"/>

                        {if empty($subsection.itemqtydropdown)}

                                <input id="itemqty_{$subsection.orderlineid}" type="number" class="quantityInput" maxlength="8" value="{$subsection.quantity}" data-decorator="fnUpdateComponentQty" data-trigger="keyup" data-lineid="{$subsection.orderlineid}" data-itemqty="{$subsection.itemqty}" />
                                <img class="refresh" src="{$brandroot}/images/icons/refresh.png" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" data-decorator="fnUpdateComponentQty" data-trigger="click" data-lineid="{$subsection.orderlineid}" data-itemqty="{$subsection.itemqty}" />
                                <div class="clear"></div>

                        {else} {* else {if empty($section.itemqtydropdown)} *}

                                <select id="itemqty_{$subsection.orderlineid}" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="{$subsection.orderlineid}" data-itemqty="{$subsection.itemqty}">

                            {foreach from=$subsection.itemqtydropdown item=qtyValue}

                                    <option {if $qtyValue == $subsection.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>

                            {/foreach} {* end {foreach from=$section.itemqtydropdown item=qtyValue} *}

                                </select>

                        {/if} {* end {if empty($section.itemqtydropdown)} *}

                            </div>
                            <div class="clear"></div>

                         </div> <!-- itemQuantityNumber -->

                         <div class="clear"></div>

                     </div> <!-- itemSection -->

                     {/if}

                 </div>

                {if ($subsection.itemcomponentbuttons|@sizeof > 0)}

                <div class="contentChangeBtn outerBoxPadding" data-decorator="fnChangeComponent" data-orderlineid="{$orderline.orderlineid}" data-sectionlineid="{$subsection.orderlineid}">
                    <div class="changeBtnText">
                        {#str_LabelChange#}
                    </div>
                    <div class="changeBtnImg">
                        <img alt="&gt;" src="{$webroot}/images/icons/change-arrow.png" class="navigationArrow">
                    </div>
                    <div class="clear"></div>
                </div>

                {/if} {* end {if ($subsection.itemcomponentbuttons|@sizeof > 0)}*}

                 {if $subsection.metadatahtml}

                 <div class="componentMetadata outerBoxPadding metadataId{$section.orderlineid}_{$subsection.orderlineid}">
                     {$subsection.metadatahtml}
                 </div>

                 {/if}

             </div> <!-- componentContentDetail_XXXXX -->

         </div>

             {/if}

         {/if}

     {/foreach}

         <!-- END SUBCOMPONENT -->

         <!-- CHECKBOXES -->

     {foreach from=$section.checkboxes item=checkbox}

         {if $checkbox.showcomponentname==true}

             {if ($checkbox.checked == 1) && (($checkbox.metadatahtml) || ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8))}

         <div class="componentDetail outerBox outerBoxMarginBottom" id="subcomponentDetail_{$checkbox.orderlineid}" style="display:none;">

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

                         <div class="componentDescription">
                             {$checkbox.itemcomponentinfo}
                         </div>

                     {/if}{* end {if !empty($section.itemcomponentinfo)} *}

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

                            <input id="itemqty_{$checkbox.orderlineid}" type="number" class="quantityInput" maxlength="8" value="{$checkbox.quantity}" data-decorator="fnUpdateComponentQty" data-trigger="keyup" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" />
                            <img class="refresh" src="{$brandroot}/images/icons/refresh.png" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" data-decorator="fnUpdateComponentQty" data-trigger="click" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" />
                            <div class="clear"></div>

                             {else} {* else {if empty($section.itemqtydropdown)} *}

                            <select id="itemqty_{$checkbox.orderlineid}" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}">

                        {foreach from=$checkbox.itemqtydropdown item=qtyValue}

                                <option {if $qtyValue == $checkbox.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>

                        {/foreach} {* end {foreach from=$section.itemqtydropdown item=qtyValue} *}

                            </select>

                    {/if} {* end {if empty($section.itemqtydropdown)} *}

                        </div>
                        <div class="clear"></div>

                     </div> <!-- itemQuantityNumber -->

                     <div class="clear"></div>

                 </div> <!-- itemSection -->

                 {/if}

                 {if $checkbox.metadatahtml}

                 <div class="componentMetadata outerBoxPadding metadataId{$section.orderlineid}_{$checkbox.orderlineid}">
                     {$checkbox.metadatahtml}
                 </div>

                 {/if}

             </div> <!-- componentContentDetail_XXXXX -->

         </div>

                     {/if}

                 {/if}

             {/foreach}

             <!-- END CHECKBOXES -->

         {/if}

     {/foreach}

     <!-- START LINEFOOTER SECTIONS -->

     {if $orderline.orderlineid != -1}

         {if $orderline.linefootersections|@sizeof > 0}

            {foreach from=$orderline.linefootersections item=section}

                {if $section.showcomponentname == true}

                   <!-- SUBCOMPONENT -->

                   {foreach from=$section.subsections item=subsection}

                        {if $subsection.showcomponentname == true}

                            {if (($subsection.pricingmodel == 7) || ($subsection.pricingmodel == 8))
                                || ($subsection.itemcomponentbuttons|@sizeof > 0) || ($subsection.metadatahtml)}

                <div class="componentDetail outerBox outerBoxMarginBottom" id="subcomponentDetail_{$subsection.orderlineid}" style="display:none;">

                    <div>

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

                            <div id="componentRowDetail_{$subsection.orderlineid}" class="componentBloc outerBoxPadding" >

                                {if $subsection.haspreview > 0}

                                <img class="componentPreview" src="{$subsection.componentpreviewsrc|escape}" alt=""/>
                                <div class="componentDetailContentText">

                                {else} {* else {if $section.haspreview > 0} *}

                                <div class="componentDetailContentTextLong">

                                {/if} {* end {if $section.haspreview > 0} *}

                                {if !empty($subsection.info)}

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

                                </div> <!-- componentDetailContentTextLong OR componentDetailContentText -->

                                <div class="clear"></div>

                            </div> <!-- componentBloc innerBoxPadding -->

                            {if ($subsection.pricingmodel == 7) || ($subsection.pricingmodel == 8)}

                            <div class="itemSection outerBoxPadding">
                                <div class="itemSectionLabel">
                                    <div class="formLine1">
                                        <label for="itemqty_{$subsection.orderlineid}">{#str_LabelQuantity#}</label>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="itemQuantityNumber">

                                     <div class="formLine2">

                                        <input id="hiddeqty_{$subsection.orderlineid}" type="hidden" class="hiddeqtyCpt" value="{$subsection.quantity}"/>

                                {if empty($subsection.itemqtydropdown)}

                                        <input id="itemqty_{$subsection.orderlineid}" type="number" class="quantityInput" maxlength="8" value="{$subsection.quantity}" data-decorator="fnUpdateComponentQty" data-trigger="keyup" data-lineid="{$subsection.orderlineid}" data-itemqty="{$subsection.itemqty}" />
                                        <img class="refresh" src="{$brandroot}/images/icons/refresh.png" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" data-decorator="fnUpdateComponentQty" data-trigger="click" data-lineid="{$subsection.orderlineid}" data-itemqty="{$subsection.itemqty}" />
                                        <div class="clear"></div>

                                {else} {* else {if empty($section.itemqtydropdown)} *}

                                        <select id="itemqty_{$subsection.orderlineid}" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="{$subsection.orderlineid}" data-itemqty="{$subsection.itemqty}">

                                    {foreach from=$subsection.itemqtydropdown item=qtyValue}

                                            <option {if $qtyValue == $subsection.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>

                                    {/foreach} {* end {foreach from=$section.itemqtydropdown item=qtyValue} *}

                                        </select>

                                {/if} {* end {if empty($section.itemqtydropdown)} *}

                                    </div>
                                    <div class="clear"></div>

                                </div> <!-- itemQuantityNumber -->

                                <div class="clear"></div>

                            </div> <!-- itemSection -->

                            {/if}

                        </div>

                       {if ($subsection.itemcomponentbuttons|@sizeof > 0)}

                       <div class="contentChangeBtn outerBoxPadding" data-decorator="fnChangeComponent" data-orderlineid="{$orderline.orderlineid}" data-sectionlineid="{$subsection.orderlineid}">
                           <div class="changeBtnText">
                               {#str_LabelChange#}
                           </div>
                           <div class="changeBtnImg">
                               <img alt="&gt;" src="{$webroot}/webroot/images/icons/change-arrow.png" class="navigationArrow">
                           </div>
                           <div class="clear"></div>
                       </div>

                       {/if} {* end {if ($subsection.itemcomponentbuttons|@sizeof > 0)}*}

                        {if $subsection.metadatahtml}

                        <div class="componentMetadata outerBoxPadding metadataId{$section.orderlineid}_{$subsection.orderlineid}">
                            {$subsection.metadatahtml}
                        </div>

                        {/if}

                    </div> <!-- componentContentDetail_XXXXX -->

                </div>

                    {/if}

                {/if}

            {/foreach}

                <!-- END SUBCOMPONENT -->

                <!-- CHECKBOXES -->

            {foreach from=$section.checkboxes item=checkbox}

                {if $checkbox.showcomponentname==true}

                    {if ($checkbox.checked == 1) && (($checkbox.metadatahtml) || ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8))}

                <div class="componentDetail outerBox outerBoxMarginBottom" id="subcomponentDetail_{$checkbox.orderlineid}" style="display:none;">

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

                                <div class="componentDescription">
                                    {$checkbox.itemcomponentinfo}
                                </div>

                            {/if}{* end {if !empty($section.itemcomponentinfo)} *}

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

                                    <input id="itemqty_{$checkbox.orderlineid}" type="number" class="quantityInput" maxlength="8" value="{$checkbox.quantity}" data-decorator="fnUpdateComponentQty" data-trigger="keyup" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" />
                                    <img class="refresh" src="{$brandroot}/images/icons/refresh.png" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" data-decorator="fnUpdateComponentQty" data-trigger="click" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" />
                                    <div class="clear"></div>

                            {else} {* else {if empty($section.itemqtydropdown)} *}

                                    <select id="itemqty_{$checkbox.orderlineid}" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}">

                                {foreach from=$checkbox.itemqtydropdown item=qtyValue}

                                        <option {if $qtyValue == $checkbox.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>

                                {/foreach} {* end {foreach from=$section.itemqtydropdown item=qtyValue} *}

                                    </select>

                            {/if} {* end {if empty($section.itemqtydropdown)} *}

                                </div>
                                <div class="clear"></div>

                            </div> <!-- itemQuantityNumber -->

                            <div class="clear"></div>

                        </div> <!-- itemSection -->

                        {/if}

                        {if $checkbox.metadatahtml}

                        <div class="componentMetadata outerBoxPadding metadataId{$section.orderlineid}_{$checkbox.orderlineid}">
                            {$checkbox.metadatahtml}
                        </div>

                        {/if}

                    </div> <!-- componentContentDetail_XXXXX -->

                </div>

                            {/if}

                        {/if}

                    {/foreach}

                    <!-- END CHECKBOXES -->

                {/if}

            {/foreach}

         {/if}

    {/if}

     <!-- END LINEFOOTER SECTIONS -->

     <!-- LINEFOOTER CHECKBOXES -->

     {foreach from=$section.linefootercheckboxes item=checkbox}

         {if $checkbox.showcomponentname==true}

             {if ($checkbox.checked == 1) && (($checkbox.metadatahtml) || ($checkbox.pricingmodel == 7 || $checkbox.pricingmodel == 8))}

         <div class="componentDetail outerBox outerBoxMarginBottom" id="subcomponentDetail_{$checkbox.orderlineid}" style="display:none;">

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

                         <div class="componentDescription">
                             {$checkbox.itemcomponentinfo}
                         </div>

                     {/if}{* end {if !empty($section.itemcomponentinfo)} *}

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

                            <input id="itemqty_{$checkbox.orderlineid}" type="number" class="quantityInput" maxlength="8" value="{$checkbox.quantity}" data-decorator="fnUpdateComponentQty" data-trigger="keyup" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" />
                            <img class="refresh" src="{$brandroot}/images/icons/refresh.png" alt="{#str_LabelOrderUpdateItemTotal#}" title="{#str_LabelOrderUpdateItemTotal#}" data-decorator="fnUpdateComponentQty" data-trigger="click" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}" />
                            <div class="clear"></div>

                    {else} {* else {if empty($section.itemqtydropdown)} *}

                            <select id="itemqty_{$checkbox.orderlineid}" data-decorator="fnUpdateComponentQty" data-trigger="change" data-lineid="{$checkbox.orderlineid}" data-itemqty="{$checkbox.itemqty}">

                        {foreach from=$checkbox.itemqtydropdown item=qtyValue}

                                <option {if $qtyValue == $checkbox.quantity}selected="selected"{/if} value="{$qtyValue}">{$qtyValue}</option>

                        {/foreach} {* end {foreach from=$section.itemqtydropdown item=qtyValue} *}

                            </select>

                    {/if} {* end {if empty($section.itemqtydropdown)} *}

                        </div>
                        <div class="clear"></div>

                     </div> <!-- itemQuantityNumber -->

                     <div class="clear"></div>

                 </div> <!-- itemSection -->

                 {/if}

                 {if $checkbox.metadatahtml}

                 <div class="componentMetadata outerBoxPadding metadataId{$section.orderlineid}_{$checkbox.orderlineid}">
                     {$checkbox.metadatahtml}
                 </div>

                 {/if}

             </div> <!-- componentContentDetail_XXXXX -->

         </div>

                     {/if}

                 {/if}

             {/foreach}

        <!-- END LINEFOOTER CHECKBOXES -->

    </div> <!-- contentVisible -->

</div>  <!-- contentScrollCart -->