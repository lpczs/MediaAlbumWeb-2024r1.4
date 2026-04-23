/**
* Class to manipulate a light box form.
*/
var lightBoxFormPopup = function(pSettings) {

    // Force default value if not defined.
    if (pSettings === undefined) {
        pSettings = {};
    }

    /**
    * Default fields settings.
    */
    var defaultFields = {
        cardNumber: {
            show: true, 
            required: true
        },
        expiry: {
            show: true, 
            required: true
        },
        nameOnCard: {
            show: true, 
            required: true
        },
        securityCode: {
            show: true, 
            required: true
        }
    }

    {literal}

    /**
    * Build the settings.
    */
    this.settings = {
        contentClass: ((pSettings.contentClass) ? pSettings.contentClass : ''),
        fields: (pSettings.fields) ? extend(defaultFields, pSettings.fields) : defaultFields,
        validateCardNumber: (pSettings.validateCardNumber) ?  (pSettings.validateCardNumber) : function(){},
        validateExpiryDate: (pSettings.validateExpiryDate) ?  (pSettings.validateExpiryDate) : function(){},
        validateSecurityCode: (pSettings.validateSecurityCode) ?  (pSettings.validateSecurityCode) : function(){},
        executePayment: (pSettings.executePayment) ?  (pSettings.executePayment) : function(pObject){}
    };

    this.dialog = null;
    this.formSubmitted = false;

    {/literal}

    /**
    * Object to store the user data.
    */
    this.formData = {
        cardNumber: '',
        expiry: '',
        nameOnCard: '',
        securityCode: ''
    };

    /**
    * Build a popup and it's content.
    *
    * @param pContent Content of the dialog.
    */
    this.createDialog = function(pContent) {
        var dialogContent = '<div class="tpx-dialog-box-header">';
            dialogContent += "{#str_OrderPaymentDetails#}";
            dialogContent += '</div>';
            dialogContent += '<div class="tpx-dialog-box-content">';
            dialogContent += pContent;
            dialogContent += '</div>';
            dialogContent += '<div class="tpx-dialog-box-footer">';
            dialogContent += '<button type="button" class="tpx-dialog-box__footer-text-link" data-object="lightBoxFormPopup" data-decorator="closeLightBox">{#str_ButtonCancel#}</button>';
            dialogContent += '</div>';

        this.dialog = new responsiveDialog({
            contentClass: 'thirdPartyPaymentScreen lightbox-payment-dialog ' + this.settings.contentClass,
            htmlContent: dialogContent
        });
    }

    /**
    * Show the light box popup with a from content.
    * 
    * @param pPaymentForm HTML form.
    * @param pHTMLFormID Id if the HTML form element.
    */
    this.show = function(pPaymentForm, pHTMLFormID) {
        var self = this;

        // Reset form data when teh open gets opened.
        this.formData = {
            cardNumber: '',
            expiry: '',
            nameOnCard: '',
            securityCode: ''
        };
        this.formSubmitted = false;

        // Build the popup if never been used.
        if (this.dialog === null)
        {
            // Build the dialog.
            this.createDialog(pPaymentForm);

            // Show the dialog
            this.dialog.open();
        }
        else
        {
           // Show the dialog.
            this.dialog.open();
        }

        // Intercept the submission.
        document.getElementById(pHTMLFormID).addEventListener('submit', function(pEvent) {
            pEvent.preventDefault();

            // Flag the form has submitted.
            self.formSubmitted = true;

            // Check if the form is valid.
            if (self.validateForm()) {

                // Show the spinner on submit button.
                self.toggleSumbmitSpinner();

                // Blank out the fields before submitting the request to the server.
                self.clearFrom();

                // Run the caller submit action.
                self.settings.executePayment(self.formData);
            }
        });
    }    

    /**
    * Show the light box popup with the default form.
    */
    this.showGenericForm = function() {
        // check if all order metadata fields are validated before trying to initialise the payment
        if (lightBoxGatewayCheckOrderMetadataValidity()) {
            // Add the spinner to the order button.
            toggleWaitingSpinner();

            var paymentForm = '';
        
            // Build the popup.
            if (this.dialog === null)
            {
                paymentForm = this.getGenericPaymentForm();
            }

            // Show the form.
            this.show(paymentForm, 'paymentForm');

            // Add fields specific listeners.
            this.addListeners();
        }
    }

    /**
    * Build a generic form, respecting the class settings.
    *
    * @returns HTML form.
    */
    this.getGenericPaymentForm = function() {
        var paymentForm = '<form id="paymentForm" method="POST" action="" class="formWrap lightbox-payment-form">';

        // Add card number field if visible.
        if (this.settings.fields.cardNumber.show) {
            paymentForm += '<div class="lightbox-payment-form__field-wrapper">';
            paymentForm += '<label id="labelCardNumber" for="cardNumber" class="lightbox-payment-form__label lightbox-payment-form__label--card-number">{#str_OrderCardNumber#|escape}</label>';
            paymentForm += '<input type="text" name="cardNumber" id="cardNumber" data-object="lightBoxFormPopup" data-decorator="formatCardNumberInput" />';
            paymentForm += '</div>';
        }

        // Add expiry field if visible.
        if (this.settings.fields.expiry.show) {
            paymentForm += '<div class="lightbox-payment-form__field-wrapper expiry">';
            paymentForm +='<label id="labelExpiry" for="expiry" class="lightbox-payment-form__label">{#str_OrderExpiry#|escape}</label>';
            paymentForm +='<input type="text" name="expiry" class="expiryfield" id="expiry" placeholder="{#str_OrderExpirationDateMMYY#}" data-object="lightBoxFormPopup" data-decorator="formatExpiryInput" />';
            paymentForm += '</div>';
        }

        // Add name on card field if visible.
        if (this.settings.fields.nameOnCard.show) {
            paymentForm += '<div class="lightbox-payment-form__field-wrapper">';
            paymentForm +='<label id="labelNameOnCard" for="nameOnCard" class="lightbox-payment-form__label">{#str_OrderNameOnCard#|escape}</label>';
            paymentForm +='<input type="text" name="nameOnCard" id="nameOnCard"/>';
            paymentForm += '</div>';
        }

        // Add security code field if visible.
        if (this.settings.fields.securityCode.show) {
            paymentForm += '<div class="lightbox-payment-form__field-wrapper securitycode">';
            paymentForm +='<label id="labelSecurityCode" for="securityCode" class="lightbox-payment-form__label">{#str_OrderSecurityCode#|escape}</label>';
            paymentForm +='<input type="text" name="securityCode" class="securitycodefield" id="securityCode" data-object="lightBoxFormPopup" data-decorator="formatSecurityCodeInput" />';
            paymentForm +='<a class="lightbox-payment-form__help-link" data-object="lightBoxFormPopup" data-decorator="showSecurityCodeHelp" data-trigger="click" >Help</a>';
            paymentForm += '</div>';
        }

        paymentForm +='<button id="paymentFormSubmit" class="lightbox-payment-form__submit-button" type="submit">{#str_OrderPay#|replace:'^0':$formattedPrice}</button>';
        paymentForm +='</form>';
        paymentForm += '<div class="lightbox-payment-form__error-container"><p id="formErrorMessage" class="lightbox-payment-form__error-message"></p></div>';

        // Add help for the security code.
        if (this.settings.fields.securityCode.show) {
            paymentForm += '<div id="securityCodeHelp" class="lightbox-payment-form__help-popup" data-object="lightBoxFormPopup" data-trigger="click" data-decorator="preventHelpToClose">';
            paymentForm += '<div class="help-popup__content"> <div id="commonCardHelper">{#str_OrderSucrityCodeHelpCommonCard#|escape} <img src="/images/shopping-cart/cvv-help-standard.png" class="help-popup__cvv-image"></div>';
            paymentForm += '<div id="amexHelper">{#str_OrderSucrityCodeHelpAMEXCard#|escape} <img src="/images/shopping-cart/cvv-help-amex.png" class="help-popup__cvv-image"></div>';
            paymentForm +='<a href="#" class="help-popup__close-link" data-trigger="click" data-object="lightBoxFormPopup" data-decorator="handleCloseSecurityCodeHelp"></a>';
            paymentForm += '</div></div>';
        }

        return paymentForm;
    }

    /**
    * Add listeners specific from each fields visible.
    */
    this.addListeners = function () {
        var self = this;
        // Add card Number litener.
        if (this.settings.fields.cardNumber.show) {
            var inputCardNumber = document.getElementById('cardNumber');

            // Blur
            inputCardNumber.addEventListener('blur', function() {
                // Validate fields when the user click out of it.
                self.validatePopulatedFields()
            });
            // Paste
           inputCardNumber.addEventListener('paste', function(pEvent) {
                // Format the pasted number.
                pEvent.target.value = self.formatCardNumber(self.formatPasteAction(pEvent));
                // Store the value.
                self.validateCardInput(self.getUnformattedInput(pEvent.target.value));
            });
            // Autofill
            inputCardNumber.addEventListener('input', function(pEvent) {
                // Check if it is an autofill.
                if (self.isAutofill(pEvent)) {
                    // Format the auto completed number.
                    var rawData = self.getUnformattedInput(pEvent.target.value);
                    pEvent.target.value = self.formatCardNumber(rawData);
                    // Store the value.
                    self.validateCardInput(rawData);
                    // Set the focus.
                    self.setFocus(inputCardNumber, pEvent.target.value.length);
                }
            });
        }
        // Add expiry listener.
        if (this.settings.fields.expiry.show) {
            var inputExpiry = document.getElementById('expiry');

            // Blur
            inputExpiry.addEventListener('blur', function() {
                // Validate fields when the user click out of it.
                self.validatePopulatedFields()
            });
            // Paste
            inputExpiry.addEventListener('paste', function(pEvent) {
                // Format the pasted number.
                var correctedData = self.correctExpiry(self.formatPasteAction(pEvent));
                pEvent.target.value = self.formatExpiry(correctedData);
                // Store the value.
                self.formData.expiry = self.getUnformattedInput(pEvent.target.value);
            });

            // Autofill
            inputExpiry.addEventListener('input', function(pEvent) {
                // Check if it is an autofill.
                if (self.isAutofill(pEvent)) {
                    // Format the auto completed number.
                    var correctedData = self.correctExpiry(self.getUnformattedInput(pEvent.target.value));
                    pEvent.target.value = self.formatExpiry(correctedData);
                    // Store the value.
                    self.formData.expiry = self.getUnformattedInput(pEvent.target.value);
                    // Set the focus.
                    self.setFocus(inputExpiry, pEvent.target.value.length);
                }
            });
        }
        // Add name on card field listener.
        if (this.settings.fields.nameOnCard.show) {
            // Blur
            document.getElementById('nameOnCard').addEventListener('blur', function() {
                // Validate fields when the user click out of it.
                self.validatePopulatedFields()
            });
        }
        // Add security code field listener.
        if (this.settings.fields.securityCode.show) {
            var inputSecurityCode = document.getElementById('securityCode');
            // Blur
            inputSecurityCode.addEventListener('blur', function() {
                // Validate fields when the user click out of it.
                self.validatePopulatedFields()
            });
            // Paste
            inputSecurityCode.addEventListener('paste', function(pEvent) {
                // Format the pasted number.
                pEvent.target.value = self.formatSecurityCode(self.formatPasteAction(pEvent));
                // Store the value.
                self.formData.securityCode = self.formatSecurityCode(pEvent.target.value);
            });
            // Autofill
            inputSecurityCode.addEventListener('input', function(pEvent) {
                // Check if it is an autofill.
                if (self.isAutofill(pEvent)) {
                    // Format the auto completed number.
                    pEvent.target.value = self.formatSecurityCode(self.getUnformattedInput(pEvent.target.value));
                    // Store the value.
                    self.formData.securityCode = self.formatSecurityCode(pEvent.target.value);
                    // Set the focus.
                    self.setFocus(inputSecurityCode, pEvent.target.value.length);
                }
            });
        }
    }

    /**
    * Detect if it's an autifll action from the browser.
    * Autofill for a payment is not supported on IE.
    *
    * @param pEvent Event triggred on autofill action.
    */
    this.isAutofill = function(pEvent) {
        var isAutofill = false;
        var browserOk = true;
        var browserUA = navigator.userAgent;

        // Exclude Internet Explorer as Autofill is not supported.
        if (((browserUA.match('msie') == 'msie') || (browserUA.match('MSIE') == 'MSIE')) && ((browserUA.match('opera') == 'opera') == false)) {
            // Internet Explorer versions before 11.
            browserOk = false;
        }

        // Internet Explorer version 11.
        if ((browserUA.match('Trident') == 'Trident') && (browserUA.match('rv:') == 'rv:')) {
            browserOk = false;
        }

        // Check if it is an autofill.
        // Chrome return data has undefined and Firefox return data has populated.
        if ((browserOk) && ((pEvent.data === undefined) || (pEvent.data != null))) {
            isAutofill = true;
        }

        return isAutofill;
    }

    /**
    * Position the cursor correctly.
    *
    * @param pInput Input to get the cursor.
    * @param pCursorPosition Place to set the cursor.
    */
    this.setFocus = function(pInput, pCursorPosition) {
        window.setTimeout(function() {
            pInput.focus();
            pInput.setSelectionRange(pCursorPosition, pCursorPosition);
        }, 0);
    }

    /**
    * Catch the data from a paste action and remove all characters except numbers.
    *
    * @param pEvent Event triggred on paste action.
    */
    this.formatPasteAction = function(pEvent) {
        var pasteDate = (pEvent.clipboardData || window.clipboardData).getData("text/plain");
        var correctData = this.getUnformattedInput(pasteDate);
        pEvent.preventDefault();

        return correctData;
    }

    /**
    * Return the expiry date corrected.
    *
    * @param pValue Expiry date.
    * @returns Corrected date string.
    */
    this.correctExpiry = function(pValue) {
        var expiry = pValue;
        var expiryLength = expiry.length;

        // If the date has 6 or more characters only use the first and last two digits.
        if (expiry.length >= 6) {
            expiry = expiry.substr(0, 2) + expiry.substr(-2);
        }

        return expiry;
    }

    /**
    * Close the light box popup.
    */
    this.closeLightBox = function() {
        this.dialog.close();
        toggleWaitingSpinner();
    }

    /**
    * Clear the form user data.
    */
    this.clearFrom = function() {
        if (this.settings.fields.cardNumber.show) {
            document.getElementById("cardNumber").value = "";
        }

        if (this.settings.fields.expiry.show) {
            document.getElementById("expiry").value = "";
        }

        if (this.settings.fields.nameOnCard.show) {
            document.getElementById("nameOnCard").value = "";
        }

        if (this.settings.fields.securityCode.show) {
            document.getElementById("securityCode").value = "";
        }
    }

    /**
    * Rebuild the content of the form after an error has happened.
    */
    this.rebuildFormContent = function () {

        // Remove spinner from button.
        this.toggleSumbmitSpinner();

        if (this.settings.fields.cardNumber.show) {
            document.getElementById("cardNumber").value = this.formatCardNumber(this.formData.cardNumber);
        }

        if (this.settings.fields.expiry.show) {
            document.getElementById("expiry").value = this.formatExpiry(this.formData.expiry);
        }

        if (this.settings.fields.nameOnCard.show) {
            document.getElementById("nameOnCard").value = this.formData.nameOnCard;
        }

        if (this.settings.fields.securityCode.show) {
            document.getElementById("securityCode").value = this.formData.securityCode;
        }
    }

    /**
    * Show or hide a spinner to the submit button.
    */
    this.toggleSumbmitSpinner = function() {
        var confirmButton = document.getElementById('paymentFormSubmit');
        var existingClass = confirmButton.className;
        
        if (existingClass.indexOf('waiting-spinner') == -1)
        {
            confirmButton.className += ' waiting-spinner';
        }
        else
        {
            confirmButton.className = confirmButton.className.replace(' waiting-spinner', '');
        }
    }

    /**
    * Set the error for the card input.
    */
    this.setCardNumberError = function() {
        this.setInputError('labelCardNumber', "{#str_OrderInvalidCardNumber#}", true);
    }

    /**
    * Set the error for the expiry date input.
    *
    * @param pAddTextText True if the error text needs to be displayed.
    */
    this.setExpiryError = function(pAddTextText) {
        this.setInputError('labelExpiry', "{#str_OrderInvalidExpiry#}", pAddTextText);
    }

    /**
    * Set the error for the security code input.
    *
    * @param pAddTextText True if the error text needs to be displayed.
    */
    this.setSecurityCodeError = function(pAddTextText) {
        this.setInputError('labelSecurityCode', "{#str_OrderInvalidSecurityCode#}", pAddTextText);
    }

    /**
    * Flag an input error and add a text error.
    *
    * @param pInputName Input name to be flagged.
    * @param pErrorText Text to be displayed.
    * @param pAddTextText True if the error text needs to be displayed.
    */
    this.setInputError = function(pInputName, pErrorText, pAddTextText) {
        document.getElementById(pInputName).classList.add('lightbox-payment-form__label--error');

        // Check if the error text needs to be displayed.
        if (pAddTextText) {
            this.setErrorMessage(pErrorText);
        }
    }

    /**
    * Add a generic error into the form.
    */
    this.setGenericError = function(pResponse) {
        this.setErrorMessage(pResponse.errormessage);
    }

    /**
    * Add a message error into the form if no error already displayed.
    *
    * @param pErrorText Text to be displayed.
    */
    this.setErrorMessage = function(pErrorText) {
        var errorContainer = document.getElementById('formErrorMessage');

        if (errorContainer.innerHTML === '') {
            errorContainer.innerHTML = pErrorText;
            errorContainer.classList.add('lightbox-payment-form__error-message--visible');
        }
    }

    /**
    * Clear an input error class.
    *
    * @param pInputLabel Id of the label to be cleared.
    */
    this.clearLabelError = function(pInputLabel) {
        document.getElementById(pInputLabel).classList.remove('lightbox-payment-form__label--error');
    }

    /**
    * Clear the error container.
    */
    this.clearError = function() {
        document.getElementById('formErrorMessage').innerHTML = '';
        document.getElementById('formErrorMessage').classList.remove('lightbox-payment-form__error-message--visible');
    }

    /**
    * Test all populated fields.
    *
    * @param pIgnoreField Name of field to not be checked.
    */
    this.validatePopulatedFields = function(pIgnoreField) {
        var isValidForm = true;

        // Clear all error.
        this.clearError()

        // Check card number if not empty or if the user has tried to post the form.
        if ((pIgnoreField !== 'labelCardNumber') && ((this.formData.cardNumber !== '') || (this.formSubmitted))) {
            isValidForm = this.checkCardNumber(true);
        }

        // Check expiry date if not empty or if the user has tried to post the form.
        if ((pIgnoreField !== 'labelExpiry') && ((this.formData.expiry !== '') || (this.formSubmitted))) {
            isValidForm = (! this.checkExpiry(isValidForm, true)) ? false : isValidForm;
        }

        // Check security code if not empty or if the user has tried to post the form.
        if ((pIgnoreField != 'labelSecurityCode') && ((this.formData.securityCode != '') || (this.formSubmitted))) {
            isValidForm = (! this.checkSecurityCode(isValidForm, true)) ? false : isValidForm;
        }
    }

    /**
    * Validate the form data.
    *
    * @returns True if all fileds are populated as expected.
    */
    this.validateForm = function() {
        var isValidForm = true;

        // Clear all error.
        this.clearError();

        // Card number.
        if ((this.settings.fields.cardNumber.show) && (this.settings.fields.cardNumber.required)) {
            isValidForm = this.checkCardNumber(false);

            // Remove error class if it is valid.
            if (isValidForm) {
                // Remove the error from internal validation.
                this.clearLabelError('labelCardNumber');

                // If the card respect the expected format check it using the paymentgateway.
                this.settings.validateCardNumber(document.getElementById("cardNumber").value);
            }
        }

        // Expiry date.
        if ((this.settings.fields.expiry.show) && (this.settings.fields.expiry.required)) {
            // Remove error class if it is valid.
            if (this.checkExpiry(isValidForm, false)) {
                // Remove the error from internal validation.
                this.clearLabelError('labelExpiry');
                // Check using external validation.
                this.settings.validateExpiryDate(this.getUnformattedInput(document.getElementById("expiry").value));
            } else {
                isValidForm = false;
            }
        }

        // Name on card.
        if ((this.settings.fields.nameOnCard.show) && (this.settings.fields.nameOnCard.required)) {
            // Remove error class if it is valid.
            if (document.getElementById("nameOnCard").value !== '') {
                // Remove the error from internal validation.
                this.clearLabelError('labelNameOnCard');
            } else {
                isValidForm = false;
            }
        }

        // Security code.
        if ((this.settings.fields.securityCode.show) && (this.settings.fields.securityCode.required)) {
            // Remove error class if it is valid.
            if (this.checkSecurityCode(isValidForm, false)) {

                // Remove the error from internal validation.
                this.clearLabelError('labelSecurityCode');
                
                // Check using external validation.
                this.settings.validateSecurityCode(document.getElementById("securityCode").value);
            } else {
                isValidForm = false;
            }
        }

        return isValidForm;
    }

    /**
    * Test if the card number is from a mastercard card.
    *
    * @returns True if it's a mastercard number.
    */
    this.isMasterCard = function(pFirstTwoDigits) {
        return ((pFirstTwoDigits > 50) && (pFirstTwoDigits < 56));
    }

    /**
    * Test if the card number is from an AMEX card.
    *
    * @returns True if it's a AMEX number.
    */
    this.isAMEX = function(pFirstTwoDigits) {
        return ((pFirstTwoDigits === '34') || (pFirstTwoDigits === '37'));
    }

    /**
    * Test if the card number is from a Visa card.
    *
    * @returns True if it's a Visa number.
    */
    this.isVisa = function(pCardNumber) {
        return (pCardNumber.charAt(0) === '4');
    }

    /**
    * Return the card type compare to it's number.
    * 
    * @param pCardNumber Card number to be tested.
    * @returns A string that represent the card type. 
    */
    this.getCardType = function(pCardNumber) {
        var cardType = '';

        if (pCardNumber !== '') {
            var firstTwoDigit = pCardNumber.substr(0, 2);

            // Check the card type.
            if (this.isVisa(pCardNumber)) {
               cardType = 'visa';
            } else if (this.isAMEX(firstTwoDigit)) {
                cardType = 'amex';
            }
            else if (this.isMasterCard(firstTwoDigit)) {
                cardType = 'mastercard';
            }
        }

        return cardType;
    }

    /**
    * Remove all formating from a number string.
    * 
    * @returns A string wth only numbers characters.
    */
    this.getUnformattedInput = function(pValue) {
        return pValue.replace(/[^0-9]/g, '');
    }

    /**
    * Check if the card number is valid or show an error, if the card is valid we trigger the external validation function from the paymentgateway.
    * 
    * @param pProcessExternalCheck Run the external validator.
    * @returns True if no internal error have been found.
    */
    this.checkCardNumber = function(pProcessExternalCheck) {
        var cardNumber = document.getElementById("cardNumber").value;
        var isValid = this.validateCardNumber(cardNumber);

        // Invalid format, set the field as an error.
        if (! isValid) {
            this.setCardNumberError();
        } else if(pProcessExternalCheck) {
            // If the card respect the expected format check it using the paymentgateway.
            this.settings.validateCardNumber(cardNumber);
        }

        return isValid;
    }

    /**
    * Check if the expiry date is valid or show an error.
    * 
    * @param pSetMessage True if the internal error message needs to be displayed. 
    * @param pProcessExternalCheck Run the external validator.
    * @returns True if no internal error have been found.
    */
    this.checkExpiry = function(pSetMessage, pProcessExternalCheck) {
        var expiry = document.getElementById("expiry").value;
        var isValid = this.validateExpiry(expiry);

        if (! isValid) {
            this.setExpiryError(pSetMessage);
        } else if (pProcessExternalCheck) {
            this.settings.validateExpiryDate(this.getUnformattedInput(expiry));
        }

        return isValid;
    }

    /**
    * Check if the security code is valid or show an error.
    *
    * @param pSetMessage True if the internal error message needs to be displayed. 
    * @param pProcessExternalCheck Run the external validator.
    * @returns True if no internal error have been found.
    */
    this.checkSecurityCode = function(pSetMessage, pProcessExternalCheck) {
        var securitycode = document.getElementById("securityCode").value;
        var isValid = this.validateSecurityCode(securitycode);

        if (! isValid) {
            this.setSecurityCodeError(pSetMessage);
        } else if (pProcessExternalCheck) {
            this.settings.validateSecurityCode(securitycode);
        }

        return isValid;
    }

    {literal}

    /**
    * Validate the card number format.
    *
    * @param pCardNumber Card number to be tested.
    * @returns True if the card number is a valid card number.
    */
   this.validateCardNumber = function(pCardNumber) {
        var fieldIsValid = false;
        var cardNumber = this.getUnformattedInput(pCardNumber);
        var cardType = this.getCardType(cardNumber);
        var cardRegEx = '';
        var testMatch = true;

        switch (cardType) {
            case 'visa': {
                cardRegEx = /^(?:4[0-9]{12}(?:[0-9]{3})?)$/;
            }
            break;
            case 'amex': {
                cardRegEx = /^(?:3[47][0-9]{13})$/;
            }
            break;
            case 'mastercard': {
               cardRegEx = /^(?:5[1-5][0-9]{14})$/;
            }
            break;
            deafault :
                // Unknown card doesn't get tested.
                testMatch = false;
        }

        // Run a test if needed.
        if ((! testMatch) || ((cardRegEx !== '') && (cardNumber.match(cardRegEx)))) {
            fieldIsValid = true;
        } 

        return fieldIsValid;
    }

    {/literal}

    /**
    * Validate an expiry date.
    *
    * @param pExpiry Expiry date to be tested.
    * @returns True if it's a valid date.
    */
    this.validateExpiry = function(pExpiry) {
        var fieldIsValid = (pExpiry !== '');
        var currentDate = new Date();

        // Make sure the field is populated.
        if (fieldIsValid) {
            var year = pExpiry.substr(5, 2);
            var month = pExpiry.substr(0, 2);

            // Make sure the month is between 1 and 12. 
            if ((month > 12) || (month < 1)) {
                fieldIsValid = false;
            }

            // Test if the year is valid compare to the current one.
            if (fieldIsValid) {
                if ((! year) || (year < currentDate.getFullYear().toString().substr(-2))) {
                    fieldIsValid = false;
                }
            }

            // Make sure the date is in the future.
            if (fieldIsValid) {
                var expiryDate = new Date('20' + year, month, '01');

                if (expiryDate.getTime() < currentDate.getTime()) {
                    fieldIsValid = false;
                }
            }
        }

        return fieldIsValid;
    }

    /**
    * Validate a security code.
    *
    * @param pValue Security code to be tested.
    * @returns True if it's a valid security code.
    */
    this.validateSecurityCode = function(pValue) {
        return pValue.match(/^[0-9]{literal}{3,4}{/literal}$/);
    }

    /**
    * Test the validity of an input on different events, this function will alos change the inout value if from functions passed by parameters.
    * Idf it's not a number into the field, the charaters will not be accepted.
    * 
    * @param pObject Object to be tested.
    * @param pEvent Event to be tested.
    * @param pInputLabel Input label id to be tested.
    * @param pOnKeyPress Function executed on keypesss event.
    * @param pOnKeyUp Function executed on keyup event.
    */
    this.testNumberInput = function(pObject, pEvent, pInputLabel, pOnKeyPress, pOnKeyUp) {
        if (pEvent.type === 'keypress') {
            // Number only, exclude other key.
            if ((pEvent.which < 48) || (pEvent.which > 57)) {
                pEvent.preventDefault();
            } else {
                // Don't do anything if it's a keybord remove action.
                if ((pEvent.which !== 8) && (pEvent.which !== 0)) {
                    // Get the current cursor position.
                    var cursorSelection = pEvent.target.selectionStart; 
                    var keyBoardValue = '';

                    if (pEvent.key !== undefined) {
                        keyBoardValue = pEvent.key;
                    } else if (pEvent.keyCode !== undefined) {
                        keyBoardValue = String.fromCharCode(pEvent.keyCode);
                    }

                    // Build the input content with the new character at the correct position.
                    pObject.value = pObject.value.substr(0, cursorSelection) + keyBoardValue + pObject.value.substr(pEvent.target.selectionEnd, pObject.value.length);

                    // Format the number as expected by the field.
                    cursorSelection += pOnKeyPress(pObject, cursorSelection);

                    // Position the cursor correctly.
                    this.setFocus(pObject, cursorSelection);

                    // Prevent the new character to be added by the browser.
                    pEvent.preventDefault();
                }
            }
        } else if (pEvent.type === 'keyup') {
            pEvent.preventDefault();

             // Remove error for the active field;
            this.clearLabelError(pInputLabel);

            // Check other fields error;
            this.validatePopulatedFields(pInputLabel);

            // Specific field action.
            pOnKeyUp(pObject);
        }
    }

    /**
    * Format the card number and validate it while user is typing.
    * 
    * @param pObject Input object.
    * @param pEvent Event user action.
    */
    this.formatCardNumberInput = function(pObject, pEvent) {
        var self = this;

        this.testNumberInput(pObject, pEvent, 'labelCardNumber', 
            function(pObject, pCursorSelection) {
                // Format the number as user type into the field and return the new cursor position after formating.

                pObject.value = self.formatCardNumber(pObject.value);

                var cursorPosition = 1;
                if (pObject.value.charAt(pCursorSelection + 1) === ' ') {
                    cursorPosition++;
                }

                return cursorPosition;
            }, 
            function(pObject) {
                // Test the validity of the text inisde the input on fly, run errors only if the string is complete.
                var cardNumber = self.getUnformattedInput(pObject.value);
                var cardType  = self.validateCardInput(cardNumber);
                var limit = 0;

                // Format input and save data.
                switch (cardType) {
                    case 'visa':
                    case 'mastercard': {
                        limit = 16;
                    }
                    break;
                    case 'amex': {
                        limit = 15;
                    }
                    break;
                }

                if ((limit != 0) && (cardNumber.length === limit)) {
                    return self.checkCardNumber(true);
                }

                return true;
            }
        );
    }

    /**
    * Update UI compare to the card type and save the data.
    *
    * @param pValue Input value.
    * @return String Card type.
    */
    this.validateCardInput = function(pValue) {
        var cardType  = this.getCardType(pValue);

        // Remove any existing card type classes
        document.getElementById('labelCardNumber').classList.remove('visa','amex','mastercard');

        // If there is a card type, add it to the label class so we can show the relevant card icon
        if (cardType != '') {
            document.getElementById('labelCardNumber').classList.add(cardType);
        }

        // Store the value to be able to test it later.
        this.formData.cardNumber = pValue;

        return cardType;
    } 

    /**
    * Format the expiry date.
    *
    * @param pObject Input object.
    * @param pEvent Event user action.
    */
    this.formatExpiryInput = function(pObject, pEvent) {
        var self = this;

        this.testNumberInput(pObject, pEvent, 'labelExpiry', 
            function(pObject, pCursorSelection) {
                var cursorPosition = 1;
                // Format the number as user type into the field.
                var expiry = self.getUnformattedInput(pObject.value);

                // Add a 0 in front of 2 to 9 for the month.
                if ((expiry.length === 1) && (expiry.charAt(0) > 1)) {
                    expiry = "0" + expiry;
                    cursorPosition ++;
                }

                pObject.value = self.formatExpiry(expiry);

                // Move cursor at the correct place.
                if (pObject.value.charAt(pCursorSelection + cursorPosition) === ' ') {
                    cursorPosition += 3;
                }

                return cursorPosition;
            },
            function(pObject) {
                // Store the formatted date.
                self.formData.expiry = self.getUnformattedInput(pObject.value);

                if (pObject.value.length === 7) {
                    return self.checkExpiry(true, true);
                }
                return true;
            }
        );
    }

    /**
    * Test the user data for the security code field.
    *
    * @param pObject Input object.
    * @param pEvent Event user action.
    */
    this.formatSecurityCodeInput = function(pObject, pEvent) {
        var self = this;

        this.testNumberInput(pObject, pEvent, 'labelSecurityCode', 
            function() {
                pObject.value = pObject.value.substr(0, 4);
                return 1;
            },
            function(pObject){
                // Store the input value
                self.formData.securityCode = self.formatSecurityCode(pObject.value);

                if (pObject.value.length === 4) {
                    return self.checkSecurityCode(true, true);
                }

                return true;
            }
        );
    }

    /**
    * Format a card number.
    * 
    * @param pValue Card number to be formatted.
    * @returns Formatted user data.
    */
    this.formatCardNumber = function(pValue) {
        var cardNumber = this.getUnformattedInput(pValue);
        var cardType = this.getCardType(cardNumber);
        var formattedCardNumber = '';

        switch (cardType) {
            case 'visa': {
                formattedCardNumber = this.formatInput(cardNumber, [4,4,4,4], ' ');
            }
            break;
            case 'amex': {
                formattedCardNumber = this.formatInput(cardNumber, [4,4,4,3], ' ');
            }
            break;
            case 'mastercard': {
                formattedCardNumber = this.formatInput(cardNumber, [4,4,4,4], ' ');
            }
            break;
            default:
                formattedCardNumber = cardNumber;
        }

        return formattedCardNumber;
    }

    /**
    * Return the formatted expiry date.
    *
    * @param pValue Expiry date to be formatted.
    * @returns formatted date string.
    */
    this.formatExpiry = function(pValue) {
        return this.formatInput(pValue, [2,2], ' / ');
    }

    /**
    * Return the formatted security code.
    *
    * @param pValue Security code to be formatted.
    * @returns formatted date string.
    */
    this.formatSecurityCode = function(pValue) {
        return this.formatInput(pValue, [4], '');
    }

    /**
    * Format a string respecting some rules by applying a separator.
    *
    * @param pValue Value to be formatted.
    * @param pRules Rules to be applied
    * @param pSeparator Separtor to be applied.
    * @returns Formatted string.
    */
    this.formatInput = function(pValue, pRules, pSeparator) {
        var formattedString = '';
        var stringValue = pValue.replace(/ /g,'');
        var stringValueLength = stringValue.length;
        var endSection = 0;
        var rulesLength = (pRules.length -1);

        // Format the string section by section.
        for (var i = 0; i <= rulesLength; i++) {
            var startSection = endSection;
            var countExpected = parseInt(pRules[i], 10);
            endSection += countExpected;
            var sectionString = stringValue.substr(startSection, countExpected);
            formattedString += sectionString

            // Add the separator when the rule is completed and it is not the last string on delete action.
            if ((sectionString.length === countExpected) && (i < rulesLength)) {
                formattedString += pSeparator;
            }
        }

        return formattedString;
    }

    /**
    * Show the security code help popup.
    * 
    * @param pObject Not used
    * @param pEvent Event triggered on element.
    */
    this.showSecurityCodeHelp = function(pObject, pEvent) {
        pEvent.preventDefault();
        pEvent.stopPropagation();

        var self = this;
        var amexVisible = true;
        var commonCardVisible = true;
        var cardNumber = this.formData.cardNumber;

        // Check which help need to be shown.
        if (cardNumber !== ''){

            // Detect AMEX card.
            var firstTwoDigit = cardNumber.substr(0, 2);
            if (this.isAMEX(firstTwoDigit)) {
                commonCardVisible = false
            } else {
                amexVisible = false;
            }
        }

        // Add correct class to containers.
        this.setCardHelpClass("commonCardHelper", commonCardVisible);
        this.setCardHelpClass("amexHelper", amexVisible);

        // show the popup.
        document.getElementById("securityCodeHelp").classList.toggle('lightbox-payment-form__help-popup--visible');

        // Add listener to close the popup when click on background.
        window.addEventListener('click', function _listener() {
            self.closeSecurityCodeHelp();

            // Remove the listeners when it has been used.
            window.removeEventListener('click', _listener);
        });
    }

    /**
    * Show or hide a container by adding or removing a class.
    *
    * @param pContainerName Container to be modified.
    * @param pContainerVisible True if the conatinare needs to be visible.
    */
    this.setCardHelpClass = function(pContainerName, pContainerVisible) {
        var containerHelper = document.getElementById(pContainerName);
        if (pContainerVisible){
            containerHelper.classList.remove('help_content_hidden');
        } else {
            containerHelper.classList.add('help_content_hidden');
        }
    }

    /**
    * Trigger an action to close the security code help popup.
    * 
    * @param pObject Not used
    * @param pEvent Event triggered on element.
    */
    this.handleCloseSecurityCodeHelp = function(pObject, pEvent) {
        pEvent.preventDefault();
        pEvent.stopPropagation();

        // Trigger a click on background to close the popup.
        window.dispatchEvent(new Event('click'));
    }

    /**
    * Hide the security code help popup.
    */
    this.closeSecurityCodeHelp = function() {
        var securityCodeHelp = document.getElementById("securityCodeHelp");

        securityCodeHelp.classList.remove('lightbox-payment-form__help-popup--visible');
    }

    /**
    * Prevent a click inside the popup to close it.
    * 
    * @param pObject Not used
    * @param pEvent Event triggered on element.
    */
    this.preventHelpToClose = function(pObject, pEvent) {
        pEvent.preventDefault();
        pEvent.stopPropagation();
    }
}