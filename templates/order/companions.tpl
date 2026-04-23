<div class="companionSectionWrap">

	<div class="companionSectionHeader">{$companions.title}.</div>

	<div class="companionSectionMessage">{$companions.description}</div>

	<div class="companionProductOptions">

{foreach from=$companions.items key=theKey item=theAlbum}
	<div class='companionOption' id='targetuniquecompanionid_{$companions.parentorderlineid}_{$theKey}' data-companionorderlineid={$theAlbum.companionorderlineid} data-companionqtyisdropdown={$theAlbum.quantityisdropdown} data-lowestqty={$theAlbum.lowestqtyvalue} data-qtydropdownvalues={$theAlbum.itemqtydropdown}>
		<div class="companionOptionImageContainer">
	{if $theAlbum.assetrequest !== ''}
			<img class="companionOptionImage" src="{$theAlbum.assetrequest}" alt=""/>
	{else}
			<img class="companionOptionImage" src="{$brandroot}/images/companion_placeholder.png" alt="" />
	{/if}
		</div>
		<div class="companionOptionText companionOptionName">{$theAlbum.productname}</div>
		<div class="companionOptionText companionOptionPrice">{$theAlbum.totalsell}</div>

		<div class="addBtnContainer {if $theAlbum.qty != 0} addBtnQtyContainer {/if}" id="addBtnContainer_{$companions.parentorderlineid}_{$theKey}">
			<div class="companionBtn companionAddButton" data-productcode="{$theAlbum.productcode}" data-parentlineitem="{$companions.parentorderlineid}" data-companionid="{$companions.parentorderlineid}_{$theKey}" id="addBtn_{$companions.parentorderlineid}_{$theKey}" >
				<span class="companionBtnAddText">Add</span>
				<span class="companionBtnAddSign">+</span>
			</div>
		</div>

		<div class="addBtnContainer {if $theAlbum.qty == 0} addBtnQtyContainer {/if}" id="setQtyContainer_{$companions.parentorderlineid}_{$theKey}">
			<div class="companionBtn companionQtyOptions">
				<div class="companionBtnQtyChange" data-productcode="{$theAlbum.productcode}" data-parentlineitem="{$companions.parentorderlineid}" data-companionid="{$companions.parentorderlineid}_{$theKey}" data-mode="-1" id="decBtn_{$companions.parentorderlineid}_{$theKey}">-</div>
				<div class="companionBtnQty">
					<input class="companionQtyValue" type="text" name="compainionCount" data-productcode="{$theAlbum.productcode}" data-parentlineitem="{$companions.parentorderlineid}" data-companionid="{$companions.parentorderlineid}_{$theKey}" id="qty_{$companions.parentorderlineid}_{$theKey}" value="{$theAlbum.qty}">
				</div>
				<div class="companionBtnQtyChange" data-productcode="{$theAlbum.productcode}" data-parentlineitem="{$companions.parentorderlineid}" data-companionid="{$companions.parentorderlineid}_{$theKey}" data-mode="1" id="incBtn_{$companions.parentorderlineid}_{$theKey}">+</div>
			</div>

			<div>
				<ul class="checkmark">
					<li id="inCart_{$companions.parentorderlineid}_{$theKey}">{$theAlbum.qtyincartmessage}</li>
				</ul>
			</div>
		</div>


	</div>

{/foreach}
	</div>

</div>
