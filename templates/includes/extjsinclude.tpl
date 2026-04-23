{assign var=extjs_folder value='ext-3.3.0'}

<link rel="stylesheet" type="text/css" href="{$webroot}/utils/ext/{$extjs_folder}/resources/css/ext-all.css">
<link rel="stylesheet" type="text/css" href="{$webroot}/utils/ext/images/silk/silk.css">
<link rel="stylesheet" type="text/css" href="{$webroot}/utils/ext/images/taopix/taopix.css">
<link rel="stylesheet" type="text/css" href="{$webroot}/utils/ext/gridsearch-1.1/css/Ext.ux.grid.RowActions.css">
<link rel="stylesheet" type="text/css" href="{$webroot}/utils/ext/gridsearch-1.1/css/empty.css" id="theme">
<link rel="stylesheet" type="text/css" href="{$webroot}/utils/ext/gridsearch-1.1/css/gridsearch.css">
<link rel="stylesheet" type="text/css" href="{$webroot}/utils/ext/fileuploadfield/css/fileuploadfield.css">

<script type="text/javascript" src="{$webroot}/utils/ext/{$extjs_folder}/adapter/ext/ext-base.js"></script>

<script type="text/javascript" src="{$webroot}/utils/ext/{$extjs_folder}/ext-all.js"></script>
<script type="text/javascript" src="{$webroot}/utils/ext/gridsearch-1.1/js/Ext.ux.ThemeCombo.js"></script>
<script type="text/javascript" src="{$webroot}/utils/ext/gridsearch-1.1/js/Ext.ux.IconMenu.js"></script>
<script type="text/javascript" src="{$webroot}/utils/ext/gridsearch-1.1/js/Ext.ux.Toast.js"></script>
<script type="text/javascript" src="{$webroot}/utils/ext/gridsearch-1.1/js/Ext.ux.grid.Search.js"></script>
<script type="text/javascript" src="{$webroot}/utils/ext/gridsearch-1.1/js/Ext.ux.grid.RowActions.js"></script>
<script type="text/javascript" src="{$webroot}/utils/ext/fileuploadfield/FileUploadField.js"></script>

<script type="text/javascript" src="{$webroot}{asset file='/utils/cookies.js'}"></script>
<script type="text/javascript" src="{$webroot}{asset file='/utils/ext-taopix.js'}"></script>
<script type="text/javascript" src="{$webroot}{asset file='/utils/datetime.js'}"></script>
<script type="text/javascript" src="{$webroot}{asset file='/utils/md5.js'}"></script>

<script type="text/javascript">
    //<![CDATA[
    /*!
    * Ext JS Library 3.0.3
    * Copyright(c) 2006-2009 Ext JS, LLC
    * licensing@extjs.com
    * http://www.extjs.com/license
    */
    /**
    * List compiled by mystix on the extjs.com forums.
    * Thank you Mystix!
    *
    * English Translations
    * updated to 2.2 by Condor (8 Aug 2008)
    */
    Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">{#str_ExtJsAlertLoading#}</div>';
    Ext.BLANK_IMAGE_URL = "{$webroot}/utils/ext/{$extjs_folder}/resources/images/default/s.gif";

    {literal}

    if(Ext.DataView){
    Ext.DataView.prototype.emptyText = "";
    }

    if(Ext.grid.GridPanel){
    Ext.grid.GridPanel.prototype.ddText = "{/literal}{#str_ExtJsSelectedRow#}{literal}";
    }

    if(Ext.LoadMask){
    Ext.LoadMask.prototype.msg = "{/literal}{#str_ExtJsAlertLoading#}{literal}";
    }

    Date.monthNames = [
    "{/literal}{#str_ExtJsMonthJanuary#}{literal}",
    "{/literal}{#str_ExtJsMonthFebruary#}{literal}",
    "{/literal}{#str_ExtJsMonthMarch#}{literal}",
    "{/literal}{#str_ExtJsMonthApril#}{literal}",
    "{/literal}{#str_ExtJsMonthMay#}{literal}",
    "{/literal}{#str_ExtJsMonthJune#}{literal}",
    "{/literal}{#str_ExtJsMonthJuly#}{literal}",
    "{/literal}{#str_ExtJsMonthAugust#}{literal}",
    "{/literal}{#str_ExtJsMonthSeptember#}{literal}",
    "{/literal}{#str_ExtJsMonthOctober#}{literal}",
    "{/literal}{#str_ExtJsMonthNovember#}{literal}",
    "{/literal}{#str_ExtJsMonthDecember#}{literal}"
    ];

    Date.getShortMonthName = function(month) {
    return Date.monthNames[month].substring(0, 3);
    };

    Date.monthNumbers = {
    Jan : 0,
    Feb : 1,
    Mar : 2,
    Apr : 3,
    May : 4,
    Jun : 5,
    Jul : 6,
    Aug : 7,
    Sep : 8,
    Oct : 9,
    Nov : 10,
    Dec : 11
    };

    Date.getMonthNumber = function(name) {
    return Date.monthNumbers[name.substring(0, 1).toUpperCase() + name.substring(1, 3).toLowerCase()];
    };

    Date.dayNames = [
    "{/literal}{#str_ExtJsDaySunday#}{literal}",
    "{/literal}{#str_ExtJsDayMonday#}{literal}",
    "{/literal}{#str_ExtJsDayTuesday#}{literal}",
    "{/literal}{#str_ExtJsDayWednesday#}{literal}",
    "{/literal}{#str_ExtJsDayThursday#}{literal}",
    "{/literal}{#str_ExtJsDayFriday#}{literal}",
    "{/literal}{#str_ExtJsDaySaturday#}{literal}"
    ];

    Date.getShortDayName = function(day) {
    return Date.dayNames[day].substring(0, 3);
    };

    Date.parseCodes.S.s = "(?:st|nd|rd|th)";

    if(Ext.MessageBox){
    Ext.MessageBox.buttonText = {
        ok     : "{/literal}{#str_ButtonOk#}{literal}",
        cancel : "{/literal}{#str_ButtonCancel#}{literal}",
        yes    : "{/literal}{#str_ButtonYes#}{literal}",
        no     : "{/literal}{#str_ButtonNo#}{literal}"
    };
    }

    if(Ext.util.Format){
    Ext.util.Format.date = function(v, format){
        if(!v) return "";
        if(!(v instanceof Date)) v = new Date(Date.parse(v));
        return v.dateFormat(format || "{/literal}{#str_ExtJsDateFormat#}{literal}");
    };
    }

    if(Ext.taopix.ComponentPricePanel){
        Ext.apply(Ext.taopix.ComponentPricePanel.prototype, {
            columnLabels : {
                    'bp': "{/literal}{#str_PriceRangeBasePrice#}{literal}",
                    'up': "{/literal}{#str_PriceRangeUnitPrice#}{literal}",
                    'ls': "{/literal}{#str_LabelLineSubtract#}{literal}",
                    'qrs': "{/literal}{#str_QtyPriceRangeStart#}{literal}",
                    'qre': "{/literal}{#str_QtyPriceRangeEnd#}{literal}",
                    'crs': "{/literal}{#str_ComponentPriceRangeStart#}{literal}",
                    'cre': "{/literal}{#str_ComponentPriceRangeEnd#}{literal}",
                    'prs': "{/literal}{#str_PageCountRangeStart#}{literal}",
                    'pre': "{/literal}{#str_PageCountRangeEnd#}{literal}",
                    'srs': "{/literal}{#str_PageCountRangeStart#}{literal}",
                    'sre': "{/literal}{#str_PageCountRangeEnd#}{literal}"
            }
        });
    }

    if(Ext.DatePicker){
    Ext.apply(Ext.DatePicker.prototype, {
        todayText         : "{/literal}{#str_ExtJsDatePickerToday#}{literal}",
        minText           : "{/literal}{#str_ExtJsDatePickerMin#}{literal}",
        maxText           : "{/literal}{#str_ExtJsDatePickerMax#}{literal}",
        disabledDaysText  : "",
        disabledDatesText : "",
        monthNames        : Date.monthNames,
        dayNames          : Date.dayNames,
        nextText          : "{/literal}{#str_ExtJsDatePickerNext#}{literal}",
        prevText          : "{/literal}{#str_ExtJsDatePickerPrev#}{literal}",
        monthYearText     : "{/literal}{#str_ExtJsDatePickerMonthYear#}{literal}",
        todayTip          : "{/literal}{#str_ExtJsDatePickerTodayTip#}{literal}",
        format            : "m/d/y",
        okText            : "&#160;{/literal}{#str_ButtonOk#}{literal}&#160;",
        cancelText        : "{/literal}{#str_ButtonCancel#}{literal}",
        startDay          : 0
    });
    }

    if(Ext.PagingToolbar){
    Ext.apply(Ext.PagingToolbar.prototype, {
        beforePageText : "{/literal}{#str_ExtJsPagingToolbarBeforePage#}{literal}",
        afterPageText  : "{/literal}{#str_ExtJsPagingToolbarAfterPage#}{literal}",
        firstText      : "{/literal}{#str_ExtJsPagingToolbarFirst#}{literal}",
        prevText       : "{/literal}{#str_ExtJsPagingToolbarPrev#}{literal}",
        nextText       : "{/literal}{#str_ExtJsPagingToolbarNext#}{literal}",
        lastText       : "{/literal}{#str_ExtJsPagingToolbarLast#}{literal}",
        refreshText    : "{/literal}{#str_ExtJsPagingToolbarRefresh#}{literal}",
        displayMsg     : "{/literal}{#str_ExtJsPagingToolbarDisplay#}{literal}",
        emptyMsg       : "{/literal}{#str_ExtJsPagingToolbarEmpty#}{literal}"
    });
    }

    if(Ext.ux.grid.Search){
    Ext.apply(Ext.ux.grid.Search.prototype, {
        searchText : "{/literal}{#str_ButtonSearch#}{literal}",
        selectAllText : "{/literal}{#str_LabelSelectAll#}{literal}"
    });
    }

    if(Ext.form.BasicForm){
        Ext.form.BasicForm.prototype.waitTitle = "{/literal}{#str_MessagePleaseWait#}{literal}";
    }

    if(Ext.form.Field){
    Ext.form.Field.prototype.invalidText = "{/literal}{#str_ErrorFieldValueInvalid#}{literal}";
    }

    if(Ext.form.TextField){
    Ext.apply(Ext.form.TextField.prototype, {
        minLengthText : "{/literal}{#str_ExtJsTextFieldMinLength#}{literal}",
        maxLengthText : "{/literal}{#str_ExtJsTextFieldMaxLength#}{literal}",
        blankText     : "{/literal}{#str_ExtJsTextFieldBlank#}{literal}",
        regexText     : "",
        emptyText     : null
    });
    }

    if(Ext.form.NumberField){
    Ext.apply(Ext.form.NumberField.prototype, {
        decimalSeparator : "{/literal}{#str_ExtJsDecimalSeparator#}{literal}",
        decimalPrecision : {/literal}{#str_ExtJsNumberFieldDecimalPrecision#}{literal},
        minText : "{/literal}{#str_ExtJsNumberFieldMin#}{literal}",
        maxText : "{/literal}{#str_ExtJsNumberFieldMax#}{literal}",
        nanText : "{/literal}{#str_ExtJsNumberFieldNan#}{literal}"
    });
    }

    if(Ext.form.DateField){
    Ext.apply(Ext.form.DateField.prototype, {
        disabledDaysText  : "{/literal}{#str_ExtJsDateFieldDisabledDays#}{literal}",
        disabledDatesText : "{/literal}{#str_ExtJsDateFieldDisabledDates#}{literal}",
        minText           : "{/literal}{#str_ExtJsDateFieldMin#}{literal}",
        maxText           : "{/literal}{#str_ExtJsDateFieldMax#}{literal}",
        invalidText       : "{/literal}{#str_ExtJsDateFieldInvalid#}{literal}",
        format            : "{/literal}{#str_ExtJsDateFormat#}{literal}",
        altFormats        : "m/d/y|m-d-y|m-d-Y|m/d|m-d|md|mdy|mdY|d|Y-m-d"
    });
    }

    if(Ext.form.ComboBox){
    Ext.apply(Ext.form.ComboBox.prototype, {
        loadingText       : "{/literal}{#str_ExtJsAlertLoading#}{literal}",
        valueNotFoundText : undefined
    });
    }

    if(Ext.form.VTypes){
    Ext.apply(Ext.form.VTypes, {
        emailText    : "{/literal}{#str_ExtJsVTypesEmail#}{literal}",
        urlText      : "{/literal}{#str_ExtJsVTypesUrl#}{literal}",
        alphaText    : "{/literal}{#str_ExtJsVTypesAlpha#}{literal}",
        alphanumText : "{/literal}{#str_ExtJsVTypesAlphaNum#}{literal}"
    });
    }

    if(Ext.form.HtmlEditor){
    Ext.apply(Ext.form.HtmlEditor.prototype, {
        createLinkText : 'Please enter the URL for the link:',
        buttonTips : {
        bold : {
            title: 'Bold (Ctrl+B)',
            text: 'Make the selected text bold.',
            cls: 'x-html-editor-tip'
        },
        italic : {
            title: 'Italic (Ctrl+I)',
            text: 'Make the selected text italic.',
            cls: 'x-html-editor-tip'
        },
        underline : {
            title: 'Underline (Ctrl+U)',
            text: 'Underline the selected text.',
            cls: 'x-html-editor-tip'
        },
        increasefontsize : {
            title: 'Grow Text',
            text: 'Increase the font size.',
            cls: 'x-html-editor-tip'
        },
        decreasefontsize : {
            title: 'Shrink Text',
            text: 'Decrease the font size.',
            cls: 'x-html-editor-tip'
        },
        backcolor : {
            title: 'Text Highlight Color',
            text: 'Change the background color of the selected text.',
            cls: 'x-html-editor-tip'
        },
        forecolor : {
            title: 'Font Color',
            text: 'Change the color of the selected text.',
            cls: 'x-html-editor-tip'
        },
        justifyleft : {
            title: 'Align Text Left',
            text: 'Align text to the left.',
            cls: 'x-html-editor-tip'
        },
        justifycenter : {
            title: 'Center Text',
            text: 'Center text in the editor.',
            cls: 'x-html-editor-tip'
        },
        justifyright : {
            title: 'Align Text Right',
            text: 'Align text to the right.',
            cls: 'x-html-editor-tip'
        },
        insertunorderedlist : {
            title: 'Bullet List',
            text: 'Start a bulleted list.',
            cls: 'x-html-editor-tip'
        },
        insertorderedlist : {
            title: 'Numbered List',
            text: 'Start a numbered list.',
            cls: 'x-html-editor-tip'
        },
        createlink : {
            title: 'Hyperlink',
            text: 'Make the selected text a hyperlink.',
            cls: 'x-html-editor-tip'
        },
        sourceedit : {
            title: 'Source Edit',
            text: 'Switch to source editing mode.',
            cls: 'x-html-editor-tip'
        }
        }
    });
    }

    if(Ext.grid.GridView){
    Ext.apply(Ext.grid.GridView.prototype, {
        sortAscText  : "{/literal}{#str_ExtJsGridViewSortAsc#}{literal}",
        sortDescText : "{/literal}{#str_ExtJsGridViewSortDesc#}{literal}",
        columnsText  : "{/literal}{#str_ExtJsGridViewColumns#}{literal}"
    });
    }

    if(Ext.grid.GroupingView){
    Ext.apply(Ext.grid.GroupingView.prototype, {
        emptyGroupText : "{/literal}{#str_ExtJsGroupingViewEmptyGroup#}{literal}",
        groupByText    : "{/literal}{#str_ExtJsGroupingViewGroupBy#}{literal}",
        showGroupsText : "{/literal}{#str_ExtJsGroupingViewShowGroups#}{literal}"
    });
    }

    if(Ext.grid.PropertyColumnModel){
    Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
        nameText   : "{/literal}{#str_ExtJsPropertyColumnModelName#}{literal}",
        valueText  : "{/literal}{#str_ExtJsPropertyColumnModelValue#}{literal}",
        dateFormat : "{/literal}{#str_ExtJsDateFormat#}{literal}"
    });
    }

    if(Ext.grid.BooleanColumn){
    Ext.apply(Ext.grid.BooleanColumn.prototype, {
        trueText  : "{/literal}{#str_ExtJsBooleanColumnTrue#}{literal}",
        falseText : "{/literal}{#str_ExtJsBooleanColumnFalse#}{literal}",
        undefinedText: '&#160;'
    });
    }

    if(Ext.grid.NumberColumn){
        Ext.apply(Ext.grid.NumberColumn.prototype, {
            format : "0{/literal}{#str_ExtJsThousandsSeparator#}{literal}000{/literal}{#str_ExtJsDecimalSeparator#}{literal}00"
        });
    }

    if(Ext.grid.DateColumn){
        Ext.apply(Ext.grid.DateColumn.prototype, {
            format : "{/literal}{#str_ExtJsDateFormat#}{literal}"
        });
    }

    if(Ext.layout.BorderLayout && Ext.layout.BorderLayout.SplitRegion){
    Ext.apply(Ext.layout.BorderLayout.SplitRegion.prototype, {
        splitTip            : "{/literal}{#str_ExtJsBorderLayoutSplit#}{literal}",
        collapsibleSplitTip : "{/literal}{#str_ExtJsBorderLayoutCollapsibleSplit#}{literal}"
    });
    }

    if(Ext.form.TimeField){
    Ext.apply(Ext.form.TimeField.prototype, {
        minText : "{/literal}{#str_ExtJsTimeFieldMin#}{literal}",
        maxText : "{/literal}{#str_ExtJsTimeFieldMax#}{literal}",
        invalidText : "{/literal}{#str_ExtJsTimeFieldInvalid#}{literal}",
        format : "{/literal}{#str_ExtJsTimeFormat#}{literal}",
        altFormats : "g:ia|g:iA|g:i a|g:i A|h:i|g:i|H:i|ga|ha|gA|h a|g a|g A|gi|hi|gia|hia|g|H"
    });
    }

    if(Ext.form.CheckboxGroup){
    Ext.apply(Ext.form.CheckboxGroup.prototype, {
        blankText : "{/literal}{#str_ExtJsCheckboxGroupBlank#}{literal}"
    });
    }

    if(Ext.form.RadioGroup){
    Ext.apply(Ext.form.RadioGroup.prototype, {
        blankText : "{/literal}{#str_ExtJsRadioGroupBlank#}{literal}"
    });
    }

    /* Taopix */
    if(Ext.Msg)
    {
    Ext.Msg.taopixErrorText = "{/literal}{#str_TitleError#}{literal}";
    Ext.Msg.taopixErrorTextClientInvalid = "{/literal}{#str_ErrorClientInvalid#}{literal}";
    Ext.Msg.taopixErrorTextConnectFailure = "{/literal}{#str_ErrorConnectFailure#}{literal}";
    }

    Ext.Ajax.timeout = 550000;

	/* Reauthentication dialog. */
	if (Ext.taopix.ReauthenticationDialog)
	{
        Ext.apply(Ext.taopix.ReauthenticationDialog,
		{
            strings:
			{
				'titleAuthenticateToSave': "{/literal}{#str_TitleAuthenticateToSave#}{literal}",
				'titleAuthenticate': "{/literal}{#str_ButtonAuthenticate#}{literal}",
				'buttonAuthenticate': "{/literal}{#str_ButtonAuthenticate#}{literal}",
				'buttonOK': "{/literal}{#str_ButtonOk#}{literal}",
				'buttonCancel': "{/literal}{#str_ButtonCancel#}{literal}",
				'labelPassword': "{/literal}{#str_LabelReenterPassword#}{literal}",
				'messageSaving': "{/literal}{#str_MessageSaving#}{literal}"
            }
        });
    }

{/literal}
    //]]>
</script>
