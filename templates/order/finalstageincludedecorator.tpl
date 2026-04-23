<script type="text/javascript" src="{$webroot}{asset file='/utils/listeners.js'}" {$nonce}></script>
<script type="text/javascript" {$nonce}>

    window.addEventListener('DOMContentLoaded', function(event) {
        document.body.addEventListener('click', decoratorListener);
    });

    // Wrapper for window redirection.
    function fnRedirect(pElement)
    {
        window.location.replace(pElement.getAttribute('data-url'));
    }

</script>