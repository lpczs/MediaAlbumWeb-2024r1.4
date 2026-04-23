<!-- page flip -->
{if ($displaytype == 1) && ($previewlicensekey != '')}

	<div id="pageflip">

		{foreach from=$pages key=pageName item=pageInfo name=pagedata}

			{if ($pageName == "fcfr") || ($pageName == "fcbk") || ($pageName == "fc")  || ($pageName == "fcsp") || ($pageName == "fcff") || ($pageName == "fcbf") || ($pageName == "bc")}

				<div class="cover" data-thumbnail-image="{$thumbnailpath}/{$uploadref|escape:'url'}/{$pageName|escape:'url'}.jpg" data-page-name="{$pageInfo.pagename}" data-page-label="{$pageName}" data-transparent-page="true">
					<img class="imgcover" src="{$thumbnailpath}/{$uploadref|escape:'url'}/{$pageName|escape:'url'}.jpg" alt="Preview" />
				</div>

			<!-- if it's none accessible page just use the default picture -->
			{elseif ($pageName == "noinsideleft") || ($pageName == "nooutsideright")}

					<div class="page" data-thumbnail-image="/images/Thumbnails/blank.png" data-page-number="0">
						<img class="imgcover" src="/images/Thumbnails/blank.png" alt="Preview" />
					</div>

			<!-- if it's a spread page the second page name is empty so for the page number to be 0 this will prevent the title to be displayed -->
			{elseif $pageInfo.pagename == ''}

				<div class="page" data-thumbnail-image="{$thumbnailpath}/{$uploadref|escape:'url'}/{$pageName|escape:'url'}.jpg" data-page-number="0">
					<img class="imgpage" src="{$thumbnailpath}/{$uploadref|escape:'url'}/{$pageName|escape:'url'}.jpg" alt="Preview" />
				</div>

			{else}

				<div class="page" data-thumbnail-image="{$thumbnailpath}/{$uploadref|escape:'url'}/{$pageName|escape:'url'}.jpg" data-page-name="{$pageInfo.pagename}">
					<img class="imgpage" src="{$thumbnailpath}/{$uploadref|escape:'url'}/{$pageName|escape:'url'}.jpg" alt="Preview" />
				</div>

			{/if}

		{/foreach}

		{include file="includes/pageflip_controlbar.tpl"}

	</div>

{else}

	<!-- slide show -->
	
	<div id="slideshow" class="slideshow">

		<div id="slideImageContainer" class="slideImageContainer">

			 <div id="slideImageVisible" class="slideImageVisible">

				<div id="listPagePreview" class="listPagePreview" style="display:none">

					{assign var="theindex" value=0}

					{foreach from=$pages key=pageName item=index name=pageLoop}

						{if ($pageName != "noinsideleft") && ($pageName != "nooutsideright")}

							<div class="pagePreviewImage">
								<img id="imageSlide{$theindex}" src="{$thumbnailpath}/{$uploadref}/{$pageName}.jpg" alt="{$pageName}" />
							</div>

							{assign var="theindex" value=$theindex+1}

						{/if}

					{/foreach}

					<div class="clear"></div>

				</div>

			</div>

			<span id="navPrev" class="prev"></span>
			<span id="navNext" class="next"></span>

		</div> <!-- slideImageContainer -->

		<div class="clear"></div>

		<div id="thumbnailContainer" class="thumbnailContainer">

			<div class="thumbnailPreviewPrev thumbnailPreviewDisabled" id="thumbnailPreviewPrev"></div>

			<div id="thumbnailPreviewVisible" class="thumbnailPreviewVisible" style="display:none">

				<div id="listTumbnail" class="listTumbnail">

					{assign var="theindex" value=0}

					{foreach from=$pages key=pageName item=index name=pageLoop}

						{if ($pageName != "noinsideleft") && ($pageName != "nooutsideright")}

							{if $smarty.foreach.pageLoop.index == 0}

								<div class="previewThumbnail thumbnailActive" id="thumbnail{$theindex}" data-decorator="fnShowCurrent" data-index={$theindex}>

							{else} {* else {if $smarty.foreach.pageLoop.index == 0} *}

								<div class="previewThumbnail" id="thumbnail{$theindex}" data-decorator="fnShowCurrent" data-index={$theindex}>

							{/if} {* end {if $smarty.foreach.pageLoop.index == 0} *}

									<img src="{$thumbnailpath}/{$uploadref}/{$pageName}.jpg" alt="{$pageName}" />

								</div> <!-- previewThumbnail -->

							{assign var="theindex" value=$theindex+1}

						{/if}

					{/foreach}

					<div class="clear"></div>

				</div> <!-- listTumbnail -->

			</div> <!-- thumbnailPreviewVisible -->

			<div class="thumbnailPreviewNext" id="thumbnailPreviewNext"></div>

		</div> <!-- thumbnailContainer -->

	</div> <!-- slideshow -->
	
{/if}