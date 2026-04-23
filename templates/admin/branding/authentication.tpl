<!DOCTYPE html>
<html>
    <head>
        <style>
            .hidden { display: none; }
        </style>
    </head>
    <body>
        <p id="wait-msg">Please wait</p>
        <p id="complete-msg" class="hidden"><button id="close-window">Close</button></p>
        <script type="text/javascript">
            (function() {
              if (null === window.opener) {
                console.log('Some error happened');
                return;
              }

              var extJs = window.opener.Ext;

              // Set the appropriate details in the main window.
              extJs.getCmp('smtpsysfromaddress').setValue("{$authEmailAddress}");
              extJs.getCmp('smtpsysfromname').setValue("{$authName}");
              extJs.getCmp('oauthrefreshtoken').setValue("{$authToken}");
              extJs.getCmp('oauthrefreshtokenid').setValue("{$tokenId}");
              var completeMsg = document.getElementById('complete-msg');
              completeMsg.classList.remove('hidden');
              document.getElementById('wait-msg').classList.add('hidden');

              // The window should close after
              window.close()
            })();
        </script>
    </body>
</html>
