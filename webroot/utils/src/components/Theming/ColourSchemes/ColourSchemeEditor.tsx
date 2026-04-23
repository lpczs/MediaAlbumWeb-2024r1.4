import React, { useCallback, useEffect, useMemo } from 'react';
import { useTranslation } from 'react-i18next';
import { merge } from 'lodash';
import { ColourScheme, useTheming } from '../Context/ThemeContext';
import { addDefaultStyle, addStylesheetRules, convertTheme } from '../../../common';
import Editor from './Editors/Editor';
import { OwnerType } from '../../../Enums';
import ColourSchemeEditorHeader from './ColourSchemeEditorHeader';

export type ColourEditorProps = {
  scheme?: ColourScheme;
  onSaveScheme: (scheme: ColourScheme) => Promise<ColourScheme | void>;
  onExportScheme: (scheme: ColourScheme) => void;
  shadowRoot: Document | ShadowRoot;
};

const ColourSchemeEditor = ({ shadowRoot, onSaveScheme, onExportScheme, scheme}: ColourEditorProps) => {
  const { state: { colourSchemes } } = useTheming();
  const { t } = useTranslation(['AdminTheming', '*']);

  // memo the default theme
  const defaultScheme = useMemo(() => {
    if (!Object.keys(colourSchemes).length) {
      return null;
    }
    return colourSchemes[1];
  }, [colourSchemes]);

  // merge any edits with the current theme data,to update in real time
  const payload = useMemo(() => {
    if (scheme) {
      return merge({ ...scheme.data }, { ...scheme.diff });
    }
    return null;
  }, [scheme]);

  /**
   * Apply the theme to the editor
   */
  const onApplyTheme = useCallback(() => {
    if (null === payload) {
      return void 0;
    }
    // attempt to apply the theme
    try {
      const converted = convertTheme(payload);
      addStylesheetRules(converted, scheme, shadowRoot);
    } catch (e) {
      console.error(e);
    }
  }, [payload]);

  useEffect(() => {
    if (!defaultScheme) {
      return void 0;
    }

    try {
      const converted = convertTheme(defaultScheme.data);
      addDefaultStyle(converted, defaultScheme, shadowRoot);
    } catch (e) {
      console.error(e);
    }
  }, [defaultScheme]);

  // if we have a theme, apply it
  useEffect(() => {
    if (!scheme) {
      return;
    }
    onApplyTheme();
  }, [scheme]);

  // whenever the payload changes, apply the theme
  useEffect(() => {
    onApplyTheme();
  }, [payload]);

  // if no theme has been selected, return a message
  if (!scheme) {
    return <p className={'italic'}>{t('str_MessageNoSchemeSelected', { ns: 'AdminTheming' })}</p>;
  }

  return (
    <>
      <ColourSchemeEditorHeader onSaveScheme={onSaveScheme} onExportScheme={onExportScheme} />
      <Editor editable={OwnerType.User === scheme.type} shadowRoot={shadowRoot} props={payload} />
    </>
  );
};

export default ColourSchemeEditor;
