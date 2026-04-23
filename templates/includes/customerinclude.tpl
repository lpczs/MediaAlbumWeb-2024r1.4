{if $faviconpath != ''}
    <link rel="shortcut icon" href="{$faviconpath}" type="image/x-icon" />
{/if}

<link rel="stylesheet" type="text/css" href="{$webroot}{asset file='/css/autosuggest.css'}" media="screen"/>
<link rel="stylesheet" type="text/css" href="{$webroot}{asset file='/css/lightboxpaymentdialog.css'}" media="screen"/>
<link rel="stylesheet" type="text/css" href="{$webroot}{asset file='/css/responsivedialog.css'}" media="screen"/>
<script type="text/javascript" src="{$webroot}{asset file='/utils/functions.js'}" {$nonce}></script>
<script type="text/javascript" src="{$webroot}{asset file='/utils/md5.js'}" {$nonce}></script>
<script type="text/javascript" src="{$webroot}{asset file='/utils/cookies.js'}" {$nonce}></script>
<script type="text/javascript" src="{$webroot}{asset file='/utils/autosuggest.js'}" {$nonce}></script>
<script type="text/javascript" src="{$webroot}{asset file='/utils/zxcvbn.js'}" {$nonce}></script>
<script type="text/javascript" src="{$webroot}{asset file='/utils/listeners.js'}" {$nonce}></script>
<script type="text/javascript" src="{$webroot}{asset file='/utils/responsiveDialog.js'}" {$nonce}></script>