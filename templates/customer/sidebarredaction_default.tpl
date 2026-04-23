{if $redactionmode >= 2}
    <div class="side-panel section">
        <h2 class="title-bar title-bar-panel">
            <div class="textIcon">{#str_LabelDataDeletion#}</div>
            <img src="{$accounticon}" alt="" />
            <div class="clear"></div>
        </h2>
		<div class="content">
			<div class="sidebaraccount_text">
                <a href="#" id="dataDeletionOptionLink">
                    {$redactionmodeoptiontext}
                </a>
			</div>
		</div>
    </div>
{/if}