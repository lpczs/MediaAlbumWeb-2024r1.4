<div class="headerLeft">
    {if $mainwebsiteurl == ""}
        <img src="{$headerlogoasset}" alt=""/>
    {else}
        <a href="{$mainwebsiteurl}" border="0">
            <img src="{$headerlogoasset}" alt=""/>
        </a>
    {/if}
</div>

<div class="headerRight">

    <div class="headerSeparator separatorLeft"></div>
    <div class="headerSeparator separatorRight"></div>

	{if $systemlanguagelist|@count_characters > 0}

    <div class="headerSeparator separatorLeft"></div>
    <div class="headerSeparator separatorRight"></div>

    <div class="languageSection" id="languageSelector" data-decorator="toggleLanguageOption">
        <img src="{$brandroot}/images/icons/language_icon_v2.png" alt="" class="imgLanguage" />
        <div class="languageImgPopup" id="img-language-popup"></div>
    </div> <!-- languageSection -->

	{/if}


    <div class="clear"></div>

</div> <!-- headerRight -->

<div class="clear"></div>