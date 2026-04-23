<div class="dialogTop">
    <h2 class="title-bar">
        {#str_LabelSelectComponent#|replace:'^0':$sectionname}
    </h2>
</div>
<div class="dialogContentContainer">
	<div class="content">
		<div id="previewHolderCartHolder" class="lessPaddingTop">
			<div id="previewHolderCart">
				{foreach from=$componentlist item=row name=previews}
					<div class="previewItemHolder {if $row.code==$componentcode}selected{/if}" id="holder_components_{$orderlineid}_{$smarty.foreach.previews.index}">
						<div class="pointer componentPreviewWrapper" data-decorator="fnSetItemActive">
							<span class="previewItemImg">
								{if $row.code==$componentcode}
									<input type="radio" id="components_{$orderlineid}_{$smarty.foreach.previews.index}" data-decorator="fnSetComponentActive" name="components_{$orderlineid}" value="{$row.code}" localcode="{$row.localcode}" checked="checked" />
								{else}
									<input type="radio" id="components_{$orderlineid}_{$smarty.foreach.previews.index}" data-decorator="fnSetComponentActive" name="components_{$orderlineid}" value="{$row.code}" localcode="{$row.localcode}" />
								{/if}
								<label for="components_{$orderlineid}_{$smarty.foreach.previews.index}">
									{if $row.assetrequest != ''}
										<img class="previewItemImg" src="{$row.assetrequest}" alt=""/>
									{else}
										<img src="{$brandroot}/images/no_image-2x.jpg" alt="" />
									{/if}
								</label>
							</span>
							<span class="previewItemText">
								<label for="components_{$orderlineid}_{$smarty.foreach.previews.index}" class="labelTitleComponent">
									{$row.name}
								</label>
								<span class="clear"></span>
								<span class="previewItemPrice">
									{#str_LabelPriceDifference#}:&nbsp;<span class="valuePriceComponent">{$row.pricedifference}</span>
								</span>
							</span>
						</div>
						<div class="borderRadioStore"></div>
						<div class="imgInfo" data-decorator="fnShowInfo" id="img_info_{$smarty.foreach.previews.index}" data-previewindex="{$smarty.foreach.previews.index}"></div>
						<div class="clear"></div>
						<div class="previewItemDescImg" id="description_component_{$smarty.foreach.previews.index}">
							<label for="components_{$orderlineid}_{$smarty.foreach.previews.index}">
								{if $row.info != ''}
									{$row.info}
								{else}
									<span class="no-additional-infromation">{#str_MessageNoAdditionalInformation#}</span>
								{/if}
							</label>
						</div>
					</div>
				{/foreach}
			</div>
		</div>
	</div>
</div>
<div class="buttonBottomInside">
	<div class="btnLeft">
		<div class="contentBtn" data-decorator="fnCloseWindow">
			<div class="btn-red-cross-left" ></div>
			<div class="btn-red-middle">{#str_ButtonCancel#}</div>
			<div class="btn-red-right"></div>
		</div>
	</div>
	<div class="btnRight">
		<div class="contentBtn" data-decorator="fnSelectComponent" data-lineid="{$orderlineid}" data-section="{$section}">
			<div class="btn-green-left" ></div>
			<input type="submit" value="{#str_ButtonChange#}" class="btn-submit-green-middle"/>
			<div class="btn-accept-right"></div>
		</div>
	</div>
	<div class="clear"></div>
</div>
