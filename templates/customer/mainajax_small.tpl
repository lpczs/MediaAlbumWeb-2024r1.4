<!-- YOUR ORDERS -->

{if $section == 'yourorders'}

<div id="orderMainPanel" class="productPanel">

    <div id="contentNavigationForm" class="contentNavigation">

        <div class="btnDoneTop" id="backButton" data-decorator="fnShowPanelAccount" data-visible="false" data-div-id="">
            <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone">{#str_TitleMyAccount#}</div>
            <div class="clear"></div>
        </div>

    </div> <!-- contentNavigation -->

    <div id="contentRightScrollForm" class="contentScrollCart">

        <div class="contentVisible">

            <div class="pageLabel">
                {#str_MenuTitleYourOrders#}
            </div>

        <!-- TEMP ORDERS -->

        {if $tempordercount > 0}

            {foreach from=$temporderlist item=row name=orders}

             <div class="outerBox outerBoxMarginBottom">

                <div class="outerBoxPadding">

                    <div class="orderDate">
                        <span class="orderLabelMedium">{#str_LabelOrderNum#}:</span> {$row.ordernumber}<br />
                        <span class="orderLabelMedium">{#str_LabelOrderDate#}:</span> {$row.formattedorderdate}
                    </div> <!-- ordernumber -->

                {foreach name=productloop item=product key=index from=$row.product}

                    <div class="clickable innerBox" data-decorator="fnShowOrderDetails" data-show="true" data-product-id="{$product.id}">

                        <div class="orderLabel">

                            <div class="orderProductLabel">
                                {$product.projectname}
                            </div> <!-- componentLabel -->

                            <div class="orderProductBtnDetail">
                            </div>

                            <div class="clear"></div>

                        </div> <!-- orderLabel -->

                        <div class="contentDescription">

                            <div class="descriptionProduct">
                                {$product.productname}
                            </div>

                            <div class="descriptionStatus">

                    {if $product.status==0 && $product.source == 0}

                                <span class="orderLabelStatus">{#str_LabelStatus#}:</span>
                                <span class="statusWaitForFiles">{#str_LabelStatusWaitingForFiles#}<span class="statusWarning"> / {#str_MenuTitlePayLaterOrders#}</span></span>

                    {elseif $product.status == 0 && $product.source == 1}

                                <span class="orderLabelStatus">{#str_LabelStatus#}:</span>
                                <span class="statusWarning">{#str_LabelStatusWaitingForPayment#}</span>

                    {elseif $product.status < 60}

                                <span class="orderLabelStatus">{#str_LabelStatus#}:</span>
                                <span class="statusWarning">{#str_LabelStatusWaitingForPayment#}</span>

                    {/if}

                            </div> <!-- descriptionStatus -->

                        </div> <!-- contentDescription -->

                    </div> <!-- clickable innerBox -->

                {/foreach} {* end {foreach item=product from=$row.product} *}

                </div> <!-- outerBoxPadding -->

                <div class="itemSection outerBoxPadding">

                    <div class="itemSectionLabel itemSectionTotalLabel">
                        {#str_LabelOrderTotal#}
                    </div>

                    <div class="itemTotalNumber">
                        {$row.formattedordertotal}
                    </div>

                    <div class="clear"></div>

                </div> <!-- itemSection outerBoxPadding -->

            </div> <!-- outerBox outerBoxMarginTop -->

            {/foreach} {* end {foreach from=$temporderlist item=row name=orders} *}

        {/if} {* end {if $tempordercount > 0} *}

        <!-- END TEMP ORDERS -->

        <!-- ORDERS -->

        {if $ordercount > 0}

            {foreach from=$orderlist item=row name=orders}

             <div class="outerBox outerBoxMarginBottom">

                <div class="outerBoxPadding">

                    <div class="orderDate">
                        <span class="orderLabelMedium">{#str_LabelOrderNum#}:</span> {$row.ordernumber}<br />
                        <span class="orderLabelMedium">{#str_LabelOrderDate#}:</span> {$row.formattedorderdate}
                    </div> <!-- ordernumber -->

                {foreach name=productloop item=product key=index from=$row.product}

                    <div class="clickable innerBox" data-decorator="fnShowOrderDetails" data-show="true" data-product-id="{$product.id}">

                        <div class="orderLabel">

                            <div class="orderProductLabel">
                                {$product.projectname}
                            </div> <!-- componentLabel -->

                            <div class="orderProductBtnDetail">
                            </div>

                            <div class="clear"></div>

                        </div> <!-- orderLabel -->

                        <div class="contentDescription">

                            <div>
                                {$product.productname}
                            </div>

                            <div class="descriptionStatus">

                    {if $product.orderstatus == 0}

                        {if $product.status == 0 && $product.source == 0}

                                <span class="orderLabelStatus">{#str_LabelStatus#}:</span>
                                <span class="statusWaitForFiles">{#str_LabelStatusWaitingForFiles#}</span>

                        {elseif $product.status == 0 && $product.source == 1}

                                <span class="orderLabelStatus">{#str_LabelStatus#}:</span>
                                <span class="statusInProduction">{#str_LabelStatusInProduction#}</span>

                        {elseif $product.status == 60}

                                <span class="orderLabelStatus">{#str_LabelStatus#}:</span>
                                <span class="statusShipped">{#str_LabelStatusShipped#}</span>

						{elseif $product.status == 65}

								<span class="orderLabelStatus">{#str_LabelStatus#}:</span>
								<span class="statusReadyToCollect">{#str_LabelStatusReadyToCollectAtStore#}</span>

						{elseif $product.status == 66}

								<span class="orderLabelStatus">{#str_LabelStatus#}:</span>
								<span class="statusCompleted">{#str_LabelStatusCompleted#}</span>

                        {else}

								<span class="orderLabelStatus">{#str_LabelStatus#}:</span>
                                <span class="statusInProduction">{#str_LabelStatusInProduction#}</span>

                        {/if}

                    {elseif $product.orderstatus == 1} {* else {if $product.orderstatus == 0} *}

                                <span class="orderLabelStatus">{#str_LabelStatus#}:</span>
                                <span class="statusCancelled">{#str_LabelStatusCancelled#}</span>

                    {else} {* else {if $product.orderstatus == 1} *}

                                <span class="orderLabelStatus">{#str_LabelStatus#}:</span>
                                <span class="statusCompleted">{#str_LabelStatusCompleted#}</span>

                    {/if} {* end {if $product.orderstatus == 0} *}

                            </div> <!-- descriptionStatus -->

                        </div> <!-- contentDescription -->

                    </div> <!-- clickable innerBox -->

                {/foreach} {* end {foreach item=product from=$row.product} *}

                </div> <!-- outerBoxPadding -->

                <div class="itemSection outerBoxPadding">

                    <div class="itemSectionLabel itemSectionTotalLabel">
                        {#str_LabelOrderTotal#}
                    </div>

                    <div class="itemTotalNumber">
                        {$row.formattedordertotal}
                    </div>

                    {if  $row.showpaymentstatus == 1}
                        {if $row.paymentreceived == 1}
                            <p class="paymentstatus paid">{#str_LabelStatusPaymentReceived#}</p>
                        {else}
                            <p class="paymentstatus waitingforpayment">{#str_LabelStatusWaitingForPayment#}</p>
                        {/if}
                    {/if}

                    <div class="clear"></div>

                </div> <!-- itemSection outerBoxPadding -->

            </div> <!-- outerBox outerBoxMarginTop -->

            {/foreach} {* end {foreach from=$orderlist item=row name=orders} *}

        {else}

            {if $tempordercount == 0}

            <div class="outerBox outerBoxPadding">
                {#str_LabelNoOrders#}
            </div>

            {/if} {* end {if $tempordercount == 0} *}

        {/if}

        <!-- END ORDERS -->

        </div> <!-- contentVisible -->

    </div> <!-- contentScrollCart -->

</div> <!-- orderMainPanel -->

<div id="orderDetailPanel" class="productPanel">

    <div id="contentNavigationDetail" class="contentNavigation">

        <div class="btnDoneTop" id="backButton" data-decorator="fnShowOrderDetails" data-show="false" data-product-id="">
            <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone">{#str_MenuTitleYourOrders#}</div>
            <div class="clear"></div>
        </div>

    </div> <!-- contentNavigation -->

    <div id="contentRightScrollDetail" class="contentScrollCart">

        <div class="contentVisible">

        {if ($tempordercount > 0) || ($ordercount > 0)}

            {if $tempordercount > 0}

                {foreach from=$temporderlist item=row name=orders}

                    {foreach name=productloop item=product key=index from=$row.product}

            <div id="productDetail{$product.id}" style="display: none;">

                <div class="pageLabel">
                    {$product.projectname}
                </div>

                <div class="outerBox outerBoxPadding outerBoxMarginBottom productDetailBloc">

                    <div class="descriptionProduct">
                        {$product.productname}
                    </div>

                    <div class="orderDetail">
                        <span class="orderLabelMedium">{#str_LabelOrderNum#}:</span> {$row.ordernumber}<br />
                        <span class="orderLabelMedium">{#str_LabelOrderDate#}:</span> {$row.formattedorderdate}<br />

                        {if $product.orderstatus == 0}

                            {if $product.status == 0 && $product.source == 0}

                        <span class="orderLabelMedium">{#str_LabelStatus#}:</span>
                        <span class="statusWaitForFiles">{#str_LabelStatusWaitingForFiles#}</span>

                            {elseif $product.status == 0 && $product.source == 1}

                        <span class="orderLabelMedium">{#str_LabelStatus#}:</span>
                        <span class="statusInProduction">{#str_LabelStatusInProduction#}</span>

                            {elseif $product.status == 60}

						<span class="orderLabelMedium">{#str_LabelStatus#}:</span>
                        <span class="statusShipped">{#str_LabelStatusShipped#}</span>

							{elseif $product.status == 65}

						<span class="orderLabelMedium">{#str_LabelStatus#}:</span>
						<span class="statusReadyToCollect">{#str_LabelStatusReadyToCollectAtStore#}</span>

							{elseif $product.status == 66}

						<span class="orderLabelMedium">{#str_LabelStatus#}:</span>
                        <span class="statusCompleted">{#str_LabelStatusCompleted#}</span>

                            {else}

						<span class="orderLabelMedium">{#str_LabelStatus#}:</span>
                        <span class="statusInProduction">{#str_LabelStatusInProduction#}</span>

                            {/if}

                        {elseif $product.orderstatus == 1} {* else {if $product.orderstatus == 0} *}

                        <span class="orderLabelMedium">{#str_LabelStatus#}:</span>
                        <span class="statusCancelled">{#str_LabelStatusCancelled#}</span>

                        {else} {* else {if $product.orderstatus == 1} *}

                        <span class="orderLabelMedium">{#str_LabelStatus#}:</span>
                        <span class="statusCompleted">{#str_LabelStatusCompleted#}</span>

                        {/if} {* end {if $product.orderstatus == 0} *}

                    </div> <!-- orderDetail -->

                </div> <!-- outerBox outerBoxPadding outerBoxMarginBottom productDetailBloc -->

                        {if ($row.status > 0) && ($product.parentorderitemid == 0)}

                <div class="paddingReorderBtn">

                    <div class="btnAction btnContinue" data-decorator="fnExecutePayNow" data-session-id="{$row.sessionid}">
                        <div class="btnContinueContent">{#str_LabelPayNow#}</div>
                    </div>

                </div>

                        {/if} {* end {if $row.status > 0} *}

                        {if ($product.previewsonline == 1) && ($product.parentorderitemid == 0)}

                <div class="linkAction" data-decorator="fnShowPreview" data-url="{$webbrandweburl}{$product.previewurl|escape}&amp;ref={$session}&amp;id={$product.id}">

                    <div class="changeBtnText">
                        {#str_ButtonPreview#}
                    </div>

                    <div class="changeBtnImg">
                        <img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt= ">" />
                    </div>

                    <div class="clear"></div>

                </div> <!-- linkAction -->

                        {/if} {* end {if $row.previewsonline == 1} *}

            </div> <!-- productDetailXXX -->

                    {/foreach} {* end {foreach item=product from=$row.product} *}

                {/foreach}

            {/if}

            {if $ordercount > 0}

                {foreach from=$orderlist item=row name=orders}

                    {foreach name=productloop item=product key=index from=$row.product}

            <div id="productDetail{$product.id}" style="display: none;">

				<input type="hidden" id="onlineProjectOrderDetail{$product.projectref}" data-productident="{$product.productindent}"
                data-workflowtype="{$product.workflowtype}" />

				<input type="hidden" id="onlineProjectOrderLabel{$product.projectref}" data-projectname="{$product.projectname}" />

                <div class="pageLabel">
                    {$product.projectname}
                </div>

                <div class="outerBox outerBoxPadding outerBoxMarginBottom productDetailBloc">

                    <div class="nameProduct">
                        {$product.productname}
                    </div>

                    <div class="orderDetail">
                        <span class="orderLabelMedium">{#str_LabelOrderNum#}:</span> {$row.ordernumber}<br />
                        <span class="orderLabelMedium">{#str_LabelOrderDate#}:</span> {$row.formattedorderdate}<br />

                        {if $product.orderstatus == 0}

                            {if $product.status == 0 && $product.source == 0}

                        <span class="orderLabelMedium">{#str_LabelStatus#}:</span>
                        <span class="statusWaitForFiles">{#str_LabelStatusWaitingForFiles#}</span>

                            {elseif $product.status == 0 && $product.source == 1}

                        <span class="orderLabelMedium">{#str_LabelStatus#}:</span>
                        <span class="statusInProduction">{#str_LabelStatusInProduction#}</span>

                            {elseif $product.status == 60}

						<span class="orderLabelMedium">{#str_LabelStatus#}:</span>
						<span class="statusShipped">{#str_LabelStatusShipped#}</span>

							{elseif $product.status == 65}

						<span class="orderLabelMedium">{#str_LabelStatus#}:</span>
						<span class="statusReadyToCollect">{#str_LabelStatusReadyToCollectAtStore#}</span>

							{elseif $product.status == 66}

						<span class="orderLabelMedium">{#str_LabelStatus#}:</span>
						<span class="statusCompleted">{#str_LabelStatusCompleted#}</span>

                            {else}

						<span class="orderLabelMedium">{#str_LabelStatus#}:</span>
                        <span class="statusInProduction">{#str_LabelStatusInProduction#}</span>

                            {/if}

                        {elseif $product.orderstatus == 1} {* else {if $product.orderstatus == 0} *}

                        <span class="orderLabelMedium">{#str_LabelStatus#}:</span>
                        <span class="statusCancelled">{#str_LabelStatusCancelled#}</span>

                        {else} {* else {if $product.orderstatus == 1} *}

                        <span class="orderLabelMedium">{#str_LabelStatus#}:</span>
                        <span class="statusCompleted">{#str_LabelStatusCompleted#}</span>

                        {/if} {* end {if $product.orderstatus == 0} *}

                    </div> <!-- orderDetail -->

                </div> <!-- outerBox outerBoxPadding outerBoxMarginBottom productDetailBloc -->

					{if ($row.status > 0) && ($product.source == 1) && ($product.parentorderitemid == 0) && ($row.orderstatus == 0) && ($product.canmodify == 1) && ($product.isowner == 1)}

						<div class="linkAction" data-decorator="fnExecuteButtonAction" data-target="3" data-project-name="{$product.projectname|escape}" data-application-name="{$webbrandapplicationname|escape}" data-project-ref ="{$product.projectref}" data-workflow-type="{$product.workflowtype}" data-product-ident="{$product.productindent}" data-wizard-mode="{$product.wizardmode}">
							<div class="changeBtnText">{#str_ButtonContinueEditing#}</div>
							<div class="clear"></div>
						</div>

					{/if}

					{if $product.parentorderitemid == 0}
						{if (((($product.source == 1) && ($ishighlevel == 0) && ($product.isowner == 1)) || (($product.source == 1) && ($ishighlevel == 1) && ($basketref != '') && ($basketref != 'tpxgnbr') && ($product.isowner == 1))))}

							<div class="linkAction" data-decorator="fnExecuteButtonAction" data-target="4" data-project-name="{$product.projectname|escape}" data-application-name="{$webbrandapplicationname|escape}" data-project-ref="{$product.projectref}" data-workflow-type="{$product.workflowtype}" data-product-ident="{$product.productindent}" data-wizard-mode="{$product.wizardmode}">
								<div class="changeBtnText">{#str_ButtonDuplicateProject#}</div>
								<div class="clear"></div>
							</div>

						{/if}
					{/if}

                    {if ($row.status > 0) && ($product.canreorder == $kCanReorder) && ($product.parentorderitemid == 0)}
						<div class="paddingReorderBtn">
							<div class="btnAction btnContinue" data-decorator="fnExecuteButtonAction" data-target="1" data-project-name="{$product.projectname|escape}" data-application-name="{$webbrandapplicationname|escape}" data-project-ref="{$product.projectref}" data-workflow-type="{$product.workflowtype}" data-product-ident="{$product.productindent}" data-wizard-mode="{$product.wizardmode}">
								<div class="btnContinueContent">{#str_LabelReorder#}</div>
							</div>
						</div>
                    {/if}


                    {if $row.orderstatus != 1 && ($product.parentorderitemid == 0)}
                        {if ($product.dataavailable == 1)}
                            {if $row.status != 0}

                                {if $product.isShared == true}
                                    <div class="linkAction" data-decorator="fnExecuteButtonAction" data-target="2" data-project-name="{$product.projectname|escape}" data-application-name="{$webbrandapplicationname|escape}" data-project-ref="{$product.projectref}" data-workflow-type="{$product.workflowtype}" data-product-ident="{$product.productindent}" data-wizard-mode="{$product.wizardmode}">
                                        <div class="changeBtnText">{#str_LabelUnshare#}</div>
                                        <div class="clear"></div>
                                    </div> <!-- linkAction -->
                                {else} {* else {if $product.isShared == true} *}
                                    <div class="linkAction" data-decorator="fnExecuteButtonAction" data-target="2" data-project-name="{$product.projectname|escape}" data-application-name="{$webbrandapplicationname|escape}" data-project-ref="{$product.projectref}" data-workflow-type="{$product.workflowtype}" data-product-ident="{$product.productindent}" data-wizard-mode="{$product.wizardmode}"style="display:none;">
                                        <div class="changeBtnText">{#str_LabelUnshare#}</div>
                                        <div class="clear"></div>
                                    </div> <!-- linkAction -->
                                {/if} {* end {if $product.isShared == true} *}

                                {if $row.origorderid == 0}
                                    <div class="linkAction" data-decorator="fnExecuteButtonAction" data-target="0" data-project-name="{$product.projectname|escape}" data-application-name="{$webbrandapplicationname|escape}" data-project-ref="{$product.projectref}" data-workflow-type="{$product.workflowtype}" data-product-ident="{$product.productindent}" data-wizard-mode="{$product.wizardmode}">
                                        <div class="changeBtnText">{#str_LabelShare#}</div>
                                        <div class="changeBtnImg"><img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt= ">" /></div>
                                        <div class="clear"></div>
                                    </div> <!-- linkAction -->
                                {/if} {* end {if $row.origorderid == 0} *}

                            {elseif $product.source == 1} {* else  {if $row.status != 0} *}

                                {if $product.isShared == true}
                                    <div class="linkAction" data-decorator="fnExecuteButtonAction" data-target="2" data-project-name="{$product.projectname|escape}" data-application-name="{$webbrandapplicationname|escape}" data-project-ref="{$product.projectref}" data-workflow-type="{$product.workflowtype}" data-product-ident="{$product.productindent}" data-wizard-mode="{$product.wizardmode}">
                                        <div class="changeBtnText">{#str_LabelUnshare#}</div>
                                        <div class="clear"></div>
                                    </div> <!-- linkAction -->
                                {else} {* else {if $product.isShared == true} *}
                                    <div class="linkAction" data-decorator="fnExecuteButtonAction" data-target="2" data-project-name="{$product.projectname|escape}" data-application-name="{$webbrandapplicationname|escape}" data-project-ref="{$product.projectref}" data-workflow-type="{$product.workflowtype}" data-product-ident="{$product.productindent}" data-wizard-mode="{$product.wizardmode}" style="display:none;">
                                        <div class="changeBtnText">{#str_LabelUnshare#}</div>
                                        <div class="clear"></div>
                                    </div> <!-- linkAction -->
                                {/if} {* end {if $product.isShared == true} *}

                                <div class="linkAction" data-decorator="fnExecuteButtonAction" data-target="0" data-project-name="{$product.projectname|escape}" data-application-name="{$webbrandapplicationname|escape}" data-project-ref="{$product.projectref}" data-workflow-type="{$product.workflowtype}" data-product-ident="{$product.productindent}" data-wizard-mode="{$product.wizardmode}">
                                    <div class="changeBtnText">{#str_LabelShare#}</div>
                                    <div class="changeBtnImg"><img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt= ">" /></div>
                                    <div class="clear"></div>
                                </div> <!-- linkAction -->

                            {/if} {* end {if $row.status != 0} *}

                        {/if} {* end {if $product.dataavailable == 1 } *}

                        {if $product.previewsonline == 1}
                            <div class="linkAction" data-decorator="fnShowPreview" data-url="{$webbrandweburl}{$product.previewurl|escape}&amp;ref={$session}&amp;id={$product.id}">
                                <div class="changeBtnText">{#str_ButtonPreview#}</div>
                                <div class="changeBtnImg"><img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt= ">" /></div>
                                <div class="clear"></div>
                            </div> <!-- linkAction -->
                        {/if} {* end {if $product.previewsonline == 1} *}

                    {/if} {* end {if $row.orderstatus !=1 } *}

                {if ($row.orderstatus > 0)}
                    <div class="linkAction btnWarning deleteOrderButton" id="deleteOrderButton" data-decorator="fnDeleteOrderLine" data-orderid="{$row.orderid}" data-ordernumber="{$row.ordernumber}" data-ref="{$session}" data-ssotoken="{$ssotoken}">
                        <div class="changeBtnText">{#str_ButtonDelete#}</div>
                        <div class="changeBtnImg"></div>
                        <div class="clear"></div>
                    </div>
                {/if}



            </div> <!-- productDetailXXX -->

                    {/foreach} {* end {foreach item=product from=$row.product} *}

                {/foreach} {* end {foreach from=$orderlist item=row name=orders} *}

            {/if} {* end {if $ordercount > 0} *}

            <form id="submitform" name="submitform" method="post" action="#" accept-charset="utf-8">
                <input type="hidden" id="ref" name="ref" value="{$session}" />
                <input type="hidden" id="ssotoken" name="ssotoken" value="{$ssotoken}" />
                <input type="hidden" id="fsaction" name="fsaction" value="" />
                <input type="hidden" id="orderitemid" name="orderitemid" value="" />
                <input type="hidden" id="action" name="action" value="" />
                <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
            </form>

        {/if} {* end {if ($tempordercount > 0) || ($ordercount > 0)} *}

        </div> <!-- contentVisible -->

    </div> <!-- contentScrollCart -->

</div> <!-- orderDetailPanel -->

<div id="orderPreviewPanel" class="previewPanel">

</div> <!-- previewPanel -->

{/if} {* end {if $section == 'yourorders'} *}

<!-- END YOUR ORDERS -->

<!-- ACCOUNT DETAILS -->

{if $section=='accountdetails'}

    {if $addressupdated != 0}

<div id="contentNavigationForm" class="contentNavigation">

    <div class="btnDoneTop" data-decorator="fnShowPanelAccount" data-visible="false" data-div-id="" id="backButton">
        <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
        <div class="btnDone">{#str_ButtonCancel#}</div>
        <div class="clear"></div>
    </div>

</div> <!-- contentNavigation -->

    {/if}

<div id="contentRightScrollForm" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">
            {#str_MenuTitleAccountDetails#}
        </div>

        {if $message != ''}

        <div class="subLabelMessage">
            {$message}
        </div>

        {/if} {* end {if $message != ''} *}

        <div id="changeAccountDetailForm">
            {if $customerupdateauthrequired }
                <div id="verifyPasswordFormContainer" style="display: none">
                    <div class="formLine1">
                        <label for="password">{#str_LabelRenterPassword#}:</label>
                        <img src="{$brandroot}/images/asterisk.png" alt="*"/>
                    </div>

                    <div class="formLine2">
                        <input type="password" id="password" name="password" value="" class="middle" style="width:100%"/>
                        <img class="error_form_image" id="passwordcompulsory" src="{$brandroot}/images/asterisk.png" alt="*" />
                        <div class="clear"></div>
                    </div>
                </div>
            {/if}
            <div class="outerBox outerBoxPadding account-section">
                <div class="formLine1">
                    <label for="email">{#str_LabelEmailAddress#}:</label>
                    <img src="{$brandroot}/images/asterisk.png" alt="*"/>
                </div>

                <div class="formLine2">
                    <input type="email" id="email_account" name="email_account" value="{$email}" class="middle" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"/>
                    <img class="error_form_image" id="emailcompulsory" src="{$brandroot}/images/asterisk.png" alt="*"/>
                    <div class="clear"></div>
                </div>

				{if $showPendingMessage==1}
				<div class="informationContainer">
					<p class="informationHeader">{#str_TitleEmailChangePending#}</p>
					<p class="informationMessage">{#str_MessageEmailChangePending#}</p>
				</div>
				{/if}
            </div>

            <div class="outerBox outerBoxPadding">

                <div id="ajaxdiv"></div>

                <div class="top_gap">

                    <div class="formLine1">
                        <label for="telephonenumber">{#str_LabelTelephoneNumber#}:</label>
                        <img src="{$brandroot}/images/asterisk.png" alt="*"/>
                    </div>

                    <div class="formLine2">
                        <input type="tel" id="telephonenumber_account" name="telephonenumber_account" data-decorator="fnCJKHalfWidthFullWidthToASCII" value="{$telephonenumber}" autocorrect="off" autocapitalize="off" spellcheck="false"/>
                        <img class="error_form_image" id="telephonenumbercompulsory" src="{$brandroot}/images/asterisk.png" alt="*"/>
                        <div class="clear"></div>
                    </div>

                </div>
            </div>

        </div> <!-- outerBox outerBoxPadding -->

         <div class="paddingBtnBottomPage">

            <div class="btnAction btnContinue" data-decorator="fnVerifyAddress">
                <div class="btnContinueContent">{#str_ButtonUpdate#}</div>
            </div>

        </div>

    </div <!-- contentVisible -->

</div> <!-- contentScrollCart -->

{/if}

<!-- END ACCOUNT DETAILS -->

<!-- CHANGE PASSWORD -->

{if $section=='changepassword'}

<div id="contentNavigationForm" class="contentNavigation">

    <div class="btnDoneTop" data-decorator="fnShowPanelAccount" data-visible="false" data-div-id="" id="backButton">
        <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
        <div class="btnDone">{#str_ButtonCancel#}</div>
        <div class="clear"></div>
    </div>

</div> <!-- contentNavigation -->

<div id="contentRightScrollForm" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">
            {#str_MenuTitleChangePassword#}
        </div>

        <div id="changePasswordForm" class="outerBox outerBoxPadding">

            <div>

                <div class="formLine1">
                    <label for="oldpassword">{#str_LabelCurrentPassword#}:</label>
                    <img src="{$brandroot}/images/asterisk.png" alt="*"/>
                </div>

                <div class="formLine2">
                    <input type="password" id="oldpassword" name="oldpassword" value="" class="middle" />
                    <img class="error_form_image" id="oldpasswordcompulsory" src="{$brandroot}/images/asterisk.png" alt="*" />
                    <div class="clear"></div>
                </div>

            </div>

            <div class="top_gap">

                <div class="formLine1">
                    <label for="newpassword">{#str_LabelNewPassword#}: </label>
                    <img src="{$brandroot}/images/asterisk.png" alt="*"/>
                </div>

                <div class="formLine2">
                    <div class="password-input-wrap">
                        <input type="password" id="newpassword" name="newpassword" value="" class="middle" data-decorator="fnHandlePasswordStrength"/>
						<button type="button" class="password-visibility password-show" data-decorator="fnToggleNewPasword"></button>
                        <div class="progress-wrap">
                            <progress id="strengthvalue" value="0" min="0" max="5"></progress>
							<p>{#str_LabelPasswordStrength#}: <span id="strengthtext">{#str_LabelStartTyping#}</span></p>
                        </div>
                    </div>
                    <img class="error_form_image" id="newpasswordcompulsory" src="{$brandroot}/images/asterisk.png" alt="*" />
                    <div class="clear"></div>
                </div>

            </div>

        </div> <!-- outerBox outerBoxPadding -->

        <div class="paddingBtnBottomPage">

            <div class="btnAction btnContinue" data-decorator="fnCheckFormChangePassword">
                <div class="btnContinueContent">{#str_ButtonUpdate#}</div>
            </div>

        </div>

    </div> <!-- contentVisible -->

</div> <!-- contentScrollCart -->

{/if}

<!-- END CHANGE PASSWORD -->

<!-- CHANGE PREFERENCES -->

{if $section=='changepreferences'}

<div id="contentNavigationForm" class="contentNavigation">

    <div class="btnDoneTop" id="backButton" data-decorator="fnCheckFormChangePreferences">
        <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
        <div class="btnDone">{#str_ButtonDone#}</div>
        <div class="clear"></div>
    </div>

</div> <!-- contentNavigation -->

<div id="contentRightScrollForm" class="contentScrollCart">

    <div class="contentVisible">

        <div class="pageLabel">
            {#str_MenuTitleChangePreferences#}
        </div>

        <div class="outerBox">

            <ul class="marketingInfo">
                <li class="optionListNoBorder outerBoxPadding">
                    <input type="checkbox" name="sendmarketinginfo" id="subscribed" value="1" {if $sendmarketinginfo == 1} checked="checked"{/if} />

                        <label class="listLabel" for="subscribed">
                            <span>{#str_LabelMarketingSubscribe#}</span>
                        </label>
                    <div class="clear"></div>
                </li>
            </ul>

        </div> <!-- outerBox -->

    </div> <!-- contentVisible -->

</div> <!-- contentScrollCart -->

{/if}

<!-- END CHANGE PREFERENCES -->

<!-- OPEN EXISTING PROJECT -->

{if $section == 'existingonlineprojects'}

<div id="onlineMainPanel" class="onlinePanel">

    <div id="contentNavigationForm" class="contentNavigation">

        <div class="btnDoneTop" data-decorator="fnShowPanelAccount" data-visible="false" data-div-id="" id="backButton">
            <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone">{#str_TitleMyAccount#}</div>
            <div class="clear"></div>
        </div>

    </div> <!-- contentNavigation -->

    <div id="contentRightScrollForm" class="contentScrollCart">

        <div class="contentVisible" id="contentContainer">

            <div class="pageLabel">
                {#str_LabelOnlineProjects#}
            </div>

            {if $maintenancemode eq true}

            <div class="outerBox outerBoxPadding">
               {#str_ErrorMaintenanceMode#}
            </div>

            {else}

                {if $projects|@sizeof > 0}
                    {if $showpurgeall}
                        <div id="purgeAllMessage" class="purgeAllMessage">
                            <p>{#str_MessageProjectsFlaggedForPurge#} <a href="#" id="purgeAllLink" data-decorator="purgeFlaggedProjects">{#str_MessageDeleteAllFlaggedProjects#}</a></p>
                        </div>
                    {/if}
                <div id="contentExistingProject">

                    {foreach from=$projects item=row name=project}

                    <div class="clickable" id="contentItemBloc{$row.projectref}" data-decorator="fnShowOnlineOptions" data-show="true" data-product-id="{$row.projectref}">

                         <div class="outerBox outerBoxMarginBottom">

                            <div class="projectLabel">

                                <div class="orderProductLabel" id="orderProductLabel{$row.projectref}">
                                    {$row.name}
                                </div> <!-- componentLabel -->

                                <div class="orderProductBtnDetail">
                                </div>

                                <div class="clear"></div>

                            </div> <!-- projectLabel -->

                            <div class="contentDescription">

                                <div class="descriptionProduct">
                                    {$row.productname}
                                </div>

                                <div class="orderDetail" id="orderDetail">
                                    {if $row.dateofpurge != ''}
                                        <span class="dateofpurge">
                                            <span class="label-purge-date">{#str_MessageProjectDueToBePurged#} {$row.dateofpurge}</span> <a href="#" class="keepProjectLink" data-decorator="fnKeepOnlineProject" data-projectref="{$row.projectref}">{#str_MessageKeepProject#}</a>
                                            <br />
                                        </span>
                                    {/if}
                                    <span class="orderLabelMedium">{#str_LabelCreated#}</span>{$row.datecreated}

                        {if $row.statusdescription != ''}

                                    <br />
                                    <span class="orderLabelMedium">{#str_LabelStatus#}:</span>
                                    <span id="statusDescription{$row.projectref}" class="statusInProduction">{$row.statusdescription}</span>

                        {/if}

                                </div> <!-- descriptionStatus -->

                            </div> <!-- contentDescription -->

                        </div> <!-- projectLine -->

                    </div> <!-- clickable -->

                    {/foreach} {* end {foreach from=$orderlist item=row name=orders} *}

                </div>

                {else}

                <div class="outerBox outerBoxPadding">
                    {#str_LabelNoOnlineProject#}
                </div>

                {/if}

            {/if}

        </div> <!-- contentVisible -->

    </div> <!-- contentScrollCart -->

</div> <!-- onlinePanel -->

<div id="onlineDetailPanel" class="onlinePanel">

    <div id="contentNavigationDetail" class="contentNavigation">

        <div class="btnDoneTop" id="backButton" data-decorator="fnShowOnlineOptions" data-show="false" data-product-id="">
            <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone">{#str_LabelOnlineProjects#}</div>
            <div class="clear"></div>
        </div>

    </div> <!-- contentNavigation -->

    <div id="contentRightScrollDetail" class="contentScrollCart">

        <div class="contentVisible" id="contentRightScrollDetailVisible">

        {foreach from=$projects item=row name=project}

            <div id="onlineProjectDetail{$row.projectref}"
            data-projectname="{$row.name}"
            data-productident="{$row.productident}"
                data-workflowtype="{$row.workflowtype}" style="display: none;">

                <div class="pageLabel" id="pageLabel{$row.projectref}">
                    {$row.name}
                </div>

                <div class="outerBox outerBoxPadding outerBoxMarginBottom productDetailBloc">

                    <div class="nameProduct">
                        {$row.productname}
                    </div>

                    <div class="orderDetail" id="detailOrderDetail">
                         <span class="orderLabelMedium">{#str_LabelCreated#}</span>{$row.datecreated}

            {if $row.statusdescription != ''}

                        <br />
                        <span class="orderLabelMedium">{#str_LabelStatus#}:</span>
                        <span id="detailStatusDescription{$row.projectref}" class="statusInProduction">{$row.statusdescription}</span>
            {/if}

                    </div> <!-- orderDetail -->

                 </div> <!-- outerBox outerBoxPadding outerBoxMarginBottom productDetailBloc -->

            {if $row.cancompleteorder == 1}

                <div id="completeOrderButton">

                    <div class="btnAction btnContinue btnComplete" data-decorator="fnOnlineProjectsButtonAction" data-button="completeorder" data-wizard-mode="{$row.wizardmode}" data-work-type="{$row.workflowtype}">
                        <div class="btnContinueContent" >{#str_ButtonCompleteOrder#}</div>
                    </div>

                </div>

            {else}

				{if $row.canedit == 1}

                <div id="continueOrderButton">

                    <div class="btnAction btnContinue btnComplete" data-decorator="fnOnlineProjectsButtonAction" data-button="continueediting" data-wizard-mode="{$row.wizardmode}" data-work-type="{$row.workflowtype}">
                        <div class="btnContinueContent">{#str_ButtonContinueEditing#}</div>
                    </div>

                </div>

				{/if}

			{/if}

                <div class="linkOnlineAction">

            {if ($row.cancompleteorder == 1) && ($row.canedit == 1)}

                    <div id="continueOrderButton" class="linkAction" data-decorator="fnOnlineProjectsButtonAction" data-button="continueediting" data-wizard-mode="{$row.wizardmode}" data-work-type="{$row.workflowtype}">

                        <div class="changeBtnText">
                            {#str_ButtonContinueEditing#}
                        </div>

                        <div class="changeBtnImg">
                            <img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt= ">" />
                        </div>

                        <div class="clear"></div>

                    </div> <!-- linkAction -->

            {/if}

                    <div class="linkAction" data-decorator="fnOnlineProjectsButtonAction" data-button="duplicate" data-wizard-mode="{$row.wizardmode}" data-work-type="{$row.workflowtype}">

                        <div class="changeBtnText">
                            {#str_ButtonDuplicateProject#}
                        </div>

                        <div class="changeBtnImg">
                            <img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt= ">" />
                        </div>

                        <div class="clear"></div>

                    </div> <!-- linkAction -->

                    <div class="linkAction" data-decorator="fnOnlineProjectsButtonAction" data-button="rename" data-wizard-mode="{$row.wizardmode}" data-work-type="{$row.workflowtype}">

                        <div class="changeBtnText">
                            {#str_ButtonRenameProject#}
                        </div>

                        <div class="changeBtnImg">
                            <img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt= ">" />
                        </div>

                        <div class="clear"></div>

                    </div> <!-- linkAction -->

                {if $row.canedit == 1}
                    <div class="linkAction" data-decorator="fnOnlineProjectsButtonAction" data-button="share" data-wizard-mode="{$row.wizardmode}" data-work-type="{$row.workflowtype}">

                        <div class="changeBtnText">
                            {#str_ButtonShareProject#}
                        </div>

                        <div class="changeBtnImg">
                            <img class="navigationArrow" src="{$webroot}/images/icons/change-arrow.png" alt= ">" />
                        </div>

                        <div class="clear"></div>

                    </div> <!-- linkAction -->
                {/if}

                </div>

                    {if $row.candelete == 1}

                <div id="deleteOrderButton" class="linkAction" data-decorator="fnOnlineProjectsButtonAction" data-button="delete" data-wizard-mode="{$row.wizardmode}" data-work-type="{$row.workflowtype}">

                   <div class="deleteBtnText">
                       {#str_ButtonDeleteProject#}
                   </div>

               </div> <!-- linkAction -->

                    {/if}

            </div> <!-- productDetailXXX -->

            {/foreach} {* end {foreach from=$projects item=row name=project} *}

        </div> <!-- contentVisible -->

    </div> <!-- contentScrollCart -->

</div> <!-- orderDetailPanel -->

{/if}


{if ($section == 'existingonlineprojects') || ($section == 'yourorders')}

<div id="onlineNameFormPanel" class="onlinePanel">

    <div id="contentNavigationNameForm" class="contentNavigation">

        <div class="btnDoneTop" id="backButton" data-decorator="fnShowOPActionPanel" data-show="false">
            <img class="backImage" src="{$webroot}/images/icons/back-arrow.png" alt="<" />
            <div class="btnDone">{#str_ButtonBack#}</div>
            <div class="clear"></div>
        </div>

    </div> <!-- contentNavigation -->

    <div id="contentRightScrollNameForm" class="contentScrollCart">

        <div class="contentVisible">

            <div class="pageLabel" id="opActionPanelTitle">
            </div>

            <div class="outerBox outerBoxPadding">

                <div id="opActionPanelLabel">
                </div>

                <div id="sharelink-tip" class="tip-popout">
                    <p>{#str_ToolTipLinkCopied#}</p>
                </div>
                <div class="containerInputForm">
                    <input type="text" name="projectname" id="projectname" value="" maxlength="75" />
                </div>

            </div> <!-- outerBox outerBoxPadding -->

            <div class="paddingBtnBottomPage">
                <div class="btnAction btnContinue" id="opActionPanelBtnAction">
                    <div class="btnContinueContent" id="opActionPanelBtn"></div>
                </div>
            </div>

        </div> <!-- contentVisible -->

    </div> <!-- contentScrollCart -->

</div> <!-- onlineNameFormPanel -->

{/if}

<!-- END OPEN EXISTING PROJECT -->
