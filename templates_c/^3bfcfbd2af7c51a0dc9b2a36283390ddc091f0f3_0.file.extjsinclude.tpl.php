<?php
/* Smarty version 4.5.3, created on 2026-03-06 05:33:24
  from 'C:\TAOPIX\MediaAlbumWeb\templates\includes\extjsinclude.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_69aa67244b5643_06091615',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3bfcfbd2af7c51a0dc9b2a36283390ddc091f0f3' => 
    array (
      0 => 'C:\\TAOPIX\\MediaAlbumWeb\\templates\\includes\\extjsinclude.tpl',
      1 => 1729602708,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aa67244b5643_06091615 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\TAOPIX\\MediaAlbumWeb\\libs\\internal\\smarty\\plugins\\function.asset.php','function'=>'smarty_function_asset',),));
$_smarty_tpl->_assignInScope('extjs_folder', 'ext-3.3.0');?>

<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/<?php echo $_smarty_tpl->tpl_vars['extjs_folder']->value;?>
/resources/css/ext-all.css">
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/silk/silk.css">
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/images/taopix/taopix.css">
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/gridsearch-1.1/css/Ext.ux.grid.RowActions.css">
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/gridsearch-1.1/css/empty.css" id="theme">
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/gridsearch-1.1/css/gridsearch.css">
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/fileuploadfield/css/fileuploadfield.css">

<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/<?php echo $_smarty_tpl->tpl_vars['extjs_folder']->value;?>
/adapter/ext/ext-base.js"><?php echo '</script'; ?>
>

<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/<?php echo $_smarty_tpl->tpl_vars['extjs_folder']->value;?>
/ext-all.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/gridsearch-1.1/js/Ext.ux.ThemeCombo.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/gridsearch-1.1/js/Ext.ux.IconMenu.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/gridsearch-1.1/js/Ext.ux.Toast.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/gridsearch-1.1/js/Ext.ux.grid.Search.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/gridsearch-1.1/js/Ext.ux.grid.RowActions.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/fileuploadfield/FileUploadField.js"><?php echo '</script'; ?>
>

<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/cookies.js'),$_smarty_tpl);?>
"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/ext-taopix.js'),$_smarty_tpl);?>
"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/datetime.js'),$_smarty_tpl);?>
"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['webroot']->value;
echo smarty_function_asset(array('file'=>'/utils/md5.js'),$_smarty_tpl);?>
"><?php echo '</script'; ?>
>

<?php echo '<script'; ?>
 type="text/javascript">
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
    Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator"><?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsAlertLoading');?>
</div>';
    Ext.BLANK_IMAGE_URL = "<?php echo $_smarty_tpl->tpl_vars['webroot']->value;?>
/utils/ext/<?php echo $_smarty_tpl->tpl_vars['extjs_folder']->value;?>
/resources/images/default/s.gif";

    

    if(Ext.DataView){
    Ext.DataView.prototype.emptyText = "";
    }

    if(Ext.grid.GridPanel){
    Ext.grid.GridPanel.prototype.ddText = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsSelectedRow');?>
";
    }

    if(Ext.LoadMask){
    Ext.LoadMask.prototype.msg = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsAlertLoading');?>
";
    }

    Date.monthNames = [
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsMonthJanuary');?>
",
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsMonthFebruary');?>
",
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsMonthMarch');?>
",
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsMonthApril');?>
",
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsMonthMay');?>
",
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsMonthJune');?>
",
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsMonthJuly');?>
",
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsMonthAugust');?>
",
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsMonthSeptember');?>
",
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsMonthOctober');?>
",
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsMonthNovember');?>
",
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsMonthDecember');?>
"
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
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDaySunday');?>
",
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDayMonday');?>
",
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDayTuesday');?>
",
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDayWednesday');?>
",
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDayThursday');?>
",
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDayFriday');?>
",
    "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDaySaturday');?>
"
    ];

    Date.getShortDayName = function(day) {
    return Date.dayNames[day].substring(0, 3);
    };

    Date.parseCodes.S.s = "(?:st|nd|rd|th)";

    if(Ext.MessageBox){
    Ext.MessageBox.buttonText = {
        ok     : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonOk');?>
",
        cancel : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
",
        yes    : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonYes');?>
",
        no     : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonNo');?>
"
    };
    }

    if(Ext.util.Format){
    Ext.util.Format.date = function(v, format){
        if(!v) return "";
        if(!(v instanceof Date)) v = new Date(Date.parse(v));
        return v.dateFormat(format || "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDateFormat');?>
");
    };
    }

    if(Ext.taopix.ComponentPricePanel){
        Ext.apply(Ext.taopix.ComponentPricePanel.prototype, {
            columnLabels : {
                    'bp': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeBasePrice');?>
",
                    'up': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PriceRangeUnitPrice');?>
",
                    'ls': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelLineSubtract');?>
",
                    'qrs': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeStart');?>
",
                    'qre': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_QtyPriceRangeEnd');?>
",
                    'crs': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ComponentPriceRangeStart');?>
",
                    'cre': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ComponentPriceRangeEnd');?>
",
                    'prs': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PageCountRangeStart');?>
",
                    'pre': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PageCountRangeEnd');?>
",
                    'srs': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PageCountRangeStart');?>
",
                    'sre': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_PageCountRangeEnd');?>
"
            }
        });
    }

    if(Ext.DatePicker){
    Ext.apply(Ext.DatePicker.prototype, {
        todayText         : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDatePickerToday');?>
",
        minText           : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDatePickerMin');?>
",
        maxText           : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDatePickerMax');?>
",
        disabledDaysText  : "",
        disabledDatesText : "",
        monthNames        : Date.monthNames,
        dayNames          : Date.dayNames,
        nextText          : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDatePickerNext');?>
",
        prevText          : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDatePickerPrev');?>
",
        monthYearText     : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDatePickerMonthYear');?>
",
        todayTip          : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDatePickerTodayTip');?>
",
        format            : "m/d/y",
        okText            : "&#160;<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonOk');?>
&#160;",
        cancelText        : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
",
        startDay          : 0
    });
    }

    if(Ext.PagingToolbar){
    Ext.apply(Ext.PagingToolbar.prototype, {
        beforePageText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsPagingToolbarBeforePage');?>
",
        afterPageText  : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsPagingToolbarAfterPage');?>
",
        firstText      : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsPagingToolbarFirst');?>
",
        prevText       : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsPagingToolbarPrev');?>
",
        nextText       : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsPagingToolbarNext');?>
",
        lastText       : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsPagingToolbarLast');?>
",
        refreshText    : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsPagingToolbarRefresh');?>
",
        displayMsg     : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsPagingToolbarDisplay');?>
",
        emptyMsg       : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsPagingToolbarEmpty');?>
"
    });
    }

    if(Ext.ux.grid.Search){
    Ext.apply(Ext.ux.grid.Search.prototype, {
        searchText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonSearch');?>
",
        selectAllText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelSelectAll');?>
"
    });
    }

    if(Ext.form.BasicForm){
        Ext.form.BasicForm.prototype.waitTitle = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessagePleaseWait');?>
";
    }

    if(Ext.form.Field){
    Ext.form.Field.prototype.invalidText = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorFieldValueInvalid');?>
";
    }

    if(Ext.form.TextField){
    Ext.apply(Ext.form.TextField.prototype, {
        minLengthText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTextFieldMinLength');?>
",
        maxLengthText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTextFieldMaxLength');?>
",
        blankText     : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTextFieldBlank');?>
",
        regexText     : "",
        emptyText     : null
    });
    }

    if(Ext.form.NumberField){
    Ext.apply(Ext.form.NumberField.prototype, {
        decimalSeparator : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDecimalSeparator');?>
",
        decimalPrecision : <?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsNumberFieldDecimalPrecision');?>
,
        minText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsNumberFieldMin');?>
",
        maxText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsNumberFieldMax');?>
",
        nanText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsNumberFieldNan');?>
"
    });
    }

    if(Ext.form.DateField){
    Ext.apply(Ext.form.DateField.prototype, {
        disabledDaysText  : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDateFieldDisabledDays');?>
",
        disabledDatesText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDateFieldDisabledDates');?>
",
        minText           : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDateFieldMin');?>
",
        maxText           : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDateFieldMax');?>
",
        invalidText       : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDateFieldInvalid');?>
",
        format            : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDateFormat');?>
",
        altFormats        : "m/d/y|m-d-y|m-d-Y|m/d|m-d|md|mdy|mdY|d|Y-m-d"
    });
    }

    if(Ext.form.ComboBox){
    Ext.apply(Ext.form.ComboBox.prototype, {
        loadingText       : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsAlertLoading');?>
",
        valueNotFoundText : undefined
    });
    }

    if(Ext.form.VTypes){
    Ext.apply(Ext.form.VTypes, {
        emailText    : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsVTypesEmail');?>
",
        urlText      : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsVTypesUrl');?>
",
        alphaText    : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsVTypesAlpha');?>
",
        alphanumText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsVTypesAlphaNum');?>
"
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
        sortAscText  : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsGridViewSortAsc');?>
",
        sortDescText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsGridViewSortDesc');?>
",
        columnsText  : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsGridViewColumns');?>
"
    });
    }

    if(Ext.grid.GroupingView){
    Ext.apply(Ext.grid.GroupingView.prototype, {
        emptyGroupText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsGroupingViewEmptyGroup');?>
",
        groupByText    : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsGroupingViewGroupBy');?>
",
        showGroupsText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsGroupingViewShowGroups');?>
"
    });
    }

    if(Ext.grid.PropertyColumnModel){
    Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
        nameText   : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsPropertyColumnModelName');?>
",
        valueText  : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsPropertyColumnModelValue');?>
",
        dateFormat : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDateFormat');?>
"
    });
    }

    if(Ext.grid.BooleanColumn){
    Ext.apply(Ext.grid.BooleanColumn.prototype, {
        trueText  : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsBooleanColumnTrue');?>
",
        falseText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsBooleanColumnFalse');?>
",
        undefinedText: '&#160;'
    });
    }

    if(Ext.grid.NumberColumn){
        Ext.apply(Ext.grid.NumberColumn.prototype, {
            format : "0<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsThousandsSeparator');?>
000<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDecimalSeparator');?>
00"
        });
    }

    if(Ext.grid.DateColumn){
        Ext.apply(Ext.grid.DateColumn.prototype, {
            format : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsDateFormat');?>
"
        });
    }

    if(Ext.layout.BorderLayout && Ext.layout.BorderLayout.SplitRegion){
    Ext.apply(Ext.layout.BorderLayout.SplitRegion.prototype, {
        splitTip            : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsBorderLayoutSplit');?>
",
        collapsibleSplitTip : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsBorderLayoutCollapsibleSplit');?>
"
    });
    }

    if(Ext.form.TimeField){
    Ext.apply(Ext.form.TimeField.prototype, {
        minText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTimeFieldMin');?>
",
        maxText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTimeFieldMax');?>
",
        invalidText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTimeFieldInvalid');?>
",
        format : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsTimeFormat');?>
",
        altFormats : "g:ia|g:iA|g:i a|g:i A|h:i|g:i|H:i|ga|ha|gA|h a|g a|g A|gi|hi|gia|hia|g|H"
    });
    }

    if(Ext.form.CheckboxGroup){
    Ext.apply(Ext.form.CheckboxGroup.prototype, {
        blankText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsCheckboxGroupBlank');?>
"
    });
    }

    if(Ext.form.RadioGroup){
    Ext.apply(Ext.form.RadioGroup.prototype, {
        blankText : "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ExtJsRadioGroupBlank');?>
"
    });
    }

    /* Taopix */
    if(Ext.Msg)
    {
    Ext.Msg.taopixErrorText = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleError');?>
";
    Ext.Msg.taopixErrorTextClientInvalid = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorClientInvalid');?>
";
    Ext.Msg.taopixErrorTextConnectFailure = "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ErrorConnectFailure');?>
";
    }

    Ext.Ajax.timeout = 550000;

	/* Reauthentication dialog. */
	if (Ext.taopix.ReauthenticationDialog)
	{
        Ext.apply(Ext.taopix.ReauthenticationDialog,
		{
            strings:
			{
				'titleAuthenticateToSave': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_TitleAuthenticateToSave');?>
",
				'titleAuthenticate': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAuthenticate');?>
",
				'buttonAuthenticate': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonAuthenticate');?>
",
				'buttonOK': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonOk');?>
",
				'buttonCancel': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_ButtonCancel');?>
",
				'labelPassword': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_LabelReenterPassword');?>
",
				'messageSaving': "<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'str_MessageSaving');?>
"
            }
        });
    }


    //]]>
<?php echo '</script'; ?>
>
<?php }
}
