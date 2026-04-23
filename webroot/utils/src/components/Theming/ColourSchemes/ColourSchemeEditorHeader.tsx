import React, { useCallback, useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { ColourScheme, useTheming } from '../Context/ThemeContext';
import { ThemeActions } from '../Actions/ThemeActions';
import { OwnerType } from '../../../Enums';
import { useIssuesDialog } from '../Context/IssuesContext';
import { IssueActions } from '../Actions/IssuesActions';
import ViewHeader from '../Shared/ViewHeader';

export type ColourSchemeEditorHeaderProps = {
  onSaveScheme: (scheme: ColourScheme) => Promise<any>;
  onExportScheme: (scheme: ColourScheme) => void;
};

const ColourSchemeEditorHeader = ({ onSaveScheme, onExportScheme }: ColourSchemeEditorHeaderProps) => {
  const {
    state: { colourSchemes, selectedSchemeId },
    dispatch,
  } = useTheming();

  const { dispatch: dispatchIssue } = useIssuesDialog();
  const [saving, setIsSaving] = useState(false);
  const [error, setError] = useState('');

  // get the selected theme or undefined
  const selectedScheme = useMemo(() => {
    if (!selectedSchemeId) {
      return undefined;
    }
    return colourSchemes[selectedSchemeId];
  }, [colourSchemes, selectedSchemeId]);

  const { t } = useTranslation();

  /**
   * Save Callback
   */
  const onSave = useCallback((): void => {
    if (!selectedScheme.dirty) {
      return void 0;
    }

    if (
      Object.values(colourSchemes)
        .filter(t => t.id !== selectedScheme.id)
        .map(t => t.name.trim())
        .includes(selectedScheme.name.trim())
    ) {
      dispatchIssue(
        IssueActions.toggleIssue({
          issue: t('AdminTheming:str_LabelDuplicateName').replace('^0', selectedScheme.name.trim()),
          open: true,
        })
      );
      return void 0;
    }

    setIsSaving(true);

    onSaveScheme(selectedScheme).finally(() => {
      setIsSaving(false);
    });
  }, [selectedScheme]);

  /**
   * Change name callback
   */
  const onChangeName = useCallback(
    (name: string): void => {
      if (!selectedScheme) {
        return void 0;
      }

      if (OwnerType.System === selectedScheme.type) {
        return void 0;
      }

      if ('' === name.trim()) {
        setError(t('*:str_ExtJsTextFieldBlank'));
        return void 0;
      }

      // if we have an existing error, remove it
      if (error.length) {
        setError('');
      }

      dispatch(
        ThemeActions.updateColourScheme({
          ...selectedScheme,
          name: name.trim(),
          dirty: true,
        })
      );
    },
    [colourSchemes, selectedScheme, error]
  );


  return (
    <ViewHeader 
      onClickSave={onSave} 
      onChangeName={onChangeName} 
      onExport={onExportScheme}
      saving={saving} 
      readOnly={OwnerType.System === selectedScheme?.type} 
      entry={selectedScheme} 
      error={error}
    />  
  );
};

export default ColourSchemeEditorHeader;
