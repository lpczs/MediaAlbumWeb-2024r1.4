/**
 * Class to manage a responsive dialog.
 * 
 * @param pSettings External settings.
 */
var responsiveDialog = function(pSettings) {
    
    // Force default value if not defined.
    if (pSettings === undefined) {
        pSettings = {};
    }

    /**
    * Build the settings.
    */
    this.settings = {
        contentClass: (pSettings.contentClass) ?  pSettings.contentClass : '',
        htmlContent: (pSettings.htmlContent) ? pSettings.htmlContent : ''
    };

    /**
     * Open the dialog if it exist ealready if not byuild it first.
     * The content get recreated each time the dialog get shown.
     */
    this.open = function() {
        var dialogContentContainer = document.getElementById('tpx-dialog-box');
        var dialogContainer = document.getElementById('tpx-dialog-container');

        // Check if the dialog exists, if not build it dynamically.
        if (! dialogContentContainer) {

            // Main container
            dialogContainer = document.createElement('div');
            dialogContainer.id = 'tpx-dialog-container';
            dialogContainer.setAttribute('class', 'tpx-dialog-container');

            // Content container.
            dialogContentContainer = document.createElement('div');
            dialogContentContainer.id = 'tpx-dialog-box';

            // Add them to the DOM.
            dialogContainer.appendChild(dialogContentContainer);
            document.body.appendChild(dialogContainer);
        }

        // Add the correct class and content.
        dialogContainer.classList.add('tpx-dialog-show');
        dialogContentContainer.setAttribute('class', this.settings.contentClass + ' tpx-dialog-box tpx-dialog-show');
        dialogContentContainer.innerHTML = this.settings.htmlContent;
    }

    /**
     * Close the dialog.
     */
    this.close = function() {
        document.getElementById('tpx-dialog-container').classList.remove('tpx-dialog-show');
    }
}
