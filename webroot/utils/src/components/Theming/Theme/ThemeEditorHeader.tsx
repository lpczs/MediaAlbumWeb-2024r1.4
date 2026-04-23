import React, { useCallback, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { ThemeType, useTheming } from '../Context/ThemeContext';
import { ThemeActions } from '../Actions/ThemeActions';
import { OwnerType } from '../../../Enums';
import { useIssuesDialog } from '../Context/IssuesContext';
import { IssueActions } from '../Actions/IssuesActions';
import ViewHeader from '../Shared/ViewHeader';
import useSelectedTheme from '../Hooks/useSelectedTheme';

export type ThemeEditorHeader = {
  onSaveTheme: (theme: ThemeType) => Promise<any>;
};

const ThemEditorHeader = ({ onSaveTheme }: ThemeEditorHeader) => {
  const {dispatch, state: {themes}} = useTheming();
  const selectedTheme = useSelectedTheme();

  const { dispatch: dispatchIssue } = useIssuesDialog();
  const [saving, setIsSaving] = useState(false);
  const [error, setError] = useState('');


  const { t } = useTranslation();

  /**
   * Save Callback
   */
  const onSave = useCallback((): void => {
    if (!selectedTheme.dirty) {
      return void 0;
    }

    if (
      Object.values(themes)
        .filter(t => t.id !== selectedTheme.id)
        .map(t => t.name.trim())
        .includes(selectedTheme.name.trim())
    ) {
      dispatchIssue(
        IssueActions.toggleIssue({
          issue: t('AdminTheming:str_LabelDuplicateName').replace('^0', selectedTheme.name.trim()),
          open: true,
        })
      );
      return void 0;
    }

    setIsSaving(true);

    onSaveTheme(selectedTheme).finally(() => {
      setIsSaving(false);
    });
  }, [selectedTheme]);

  /**
   * Change name callback
   */
  const onChangeName = useCallback(
    (name: string): void => {
      if (!selectedTheme) {
        return void 0;
      }

      if (OwnerType.System === selectedTheme.type) {
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
        ThemeActions.updateTheme({
          ...selectedTheme,
          name: name.trim(),
          dirty: true,
        })
      );
    },
    [themes, selectedTheme, error]
  );


  return (
    <ViewHeader 
      onClickSave={onSave} 
      onChangeName={onChangeName} 
      saving={saving} 
      readOnly={OwnerType.System === selectedTheme?.type} 
      entry={selectedTheme} 
      error={error}
    />  
  );
};

export default ThemEditorHeader;
