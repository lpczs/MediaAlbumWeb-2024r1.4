<div class="headerLeftCustomer">
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

    <div class="headerSeparator separatorLeft"></div>
    <div class="headerSeparator separatorRight"></div>

    <div class="languageSection" data-decorator="toggleLanguageOption">
        <img src="{$brandroot}/images/icons/language_icon_v2.png" alt="" class="imgLanguage" />
        <div class="languageImgPopup" id="img-language-popup"></div>
    </div>

    <div class="headerSeparator separatorLeft"></div>
    <div class="headerSeparator separatorRight"></div>

    <div class="logoutSection" data-decorator="fnLogout">
        <form id="headerform" method="post" action="#">
            <input type="hidden" id="header_ref" name="ref" value="{$session}" />
            <input type="hidden" id="header_ssotoken" name="ssotoken" value="{$ssotoken}" />
            <input type="hidden" id="header_fsaction" name="fsaction" value="{$logoutfsaction}" />
            <input type="hidden" id="header_basketref" name="basketref" value="{$basketref}" />
            <input type="hidden" id="header_webbrandcode" name="webbrandcode" value="{$webbrandcode}" />
            <input type="hidden" id="csrf_token" name="csrf_token" value="{csrf_token}" />
        </form>
        <img src="{$brandroot}/images/icons/logout_v2.png" alt="" class="imgAbout" />
    </div>

    <div class="clear"></div>

</div> <!-- headerRight -->

<div class="clear"></div>