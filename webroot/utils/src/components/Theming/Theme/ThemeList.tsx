import React, { useCallback, useMemo, useState } from 'react';
import axios from 'axios';
import { useErrorBoundary } from 'react-error-boundary';
import { useTranslation } from 'react-i18next';
import {
  Button,
  DialogContent,
  DialogFooter,
  LoadingLosenge,
  PopOut
} from '@taopix/taopix-design-system';
import { ThemeType, ThemesResponse, useTheming } from '../Context/ThemeContext';
import { ThemeActions } from '../Actions/ThemeActions';
import { OwnerType } from '../../../Enums';
import { IssueActions } from '../Actions/IssuesActions';
import { useIssuesDialog } from '../Context/IssuesContext';
import ThemeListItem from './ThemeListItem';

export type ThemeListProps = {
  documentRoot: ShadowRoot | Document;
  onDeleteTheme: (theme: ThemeType) => void;
  onSaveTheme: (theme: ThemeType) => Promise<ThemeType | void>;
};

const ThemeList = ({ documentRoot, onDeleteTheme, onSaveTheme }: ThemeListProps) => {
  const {
    state: { themes, selectedThemeId, loading },
    dispatch,
  } = useTheming();
  const { dispatch: dispatchIssue } = useIssuesDialog();

  const { t } = useTranslation(['*', 'AdminTheming']);
  const { showBoundary } = useErrorBoundary();

  const [showSaveAlert, setShowSaveAlert] = useState<number>(null);

  const selectedTheme = useMemo(() => {
    if (!selectedThemeId) {
      return null;
    }
    return themes[selectedThemeId];
  }, [themes, selectedThemeId]);

 
  /**
   * Copy a theme
   * 
   * @param theme 
   */
  const onCopyTheme = (theme: ThemeType): void => {
    const tempId = -(Object.values(themes).length + 1);

    const newTheme: ThemeType = {
      ...theme,
      id: tempId,
      name: t('AdminTheming:str_LabelThemeNameCopy').replace('^0', theme.name),
      type: OwnerType.User,
      hash: '',
      dirty: true,
    };

    onSaveTheme(newTheme).catch(error => {
      showBoundary(t('AdminTheming:str_TitleErrorCopyTheme'));
    });
  };

  /**
   * Change the selected theme
   *
   * @param theme
   * @returns void
   */
  const onSelectTheme = (theme: ThemeType): void => {
    // if we have changes on the current theme, ask the user if they want to save before leaving
    if (selectedTheme && selectedTheme.dirty) {
      return setShowSaveAlert(theme.id);
    }
    dispatch(ThemeActions.setSelectedTheme(theme.id));
  };

  /**
   * Close the changes warning dialog
   *
   * @returns void
   */
  const onCancelThemeChange = (): void => {
    setShowSaveAlert(null);
  };

  /**
   * Discard the current changes and switch theme
   *
   * @returns void
   */
  const onDiscardChanges = useCallback((): void => {
    if (0 > selectedTheme.id) {
      // switch to the requested theme
      setShowSaveAlert(current => {
        dispatch(ThemeActions.setSelectedTheme(current));
        return null;
      });
      return void 0;
    }

    dispatch(ThemeActions.setIsLoading(true));
    // reload the theme from the server
    axios
      .get<ThemesResponse>('/api/theme/list')
      .then(({ data }) => {
        const target = data.payload.themeList.find(t => t.id === selectedThemeId);
        if (target) {
          dispatch(ThemeActions.updateTheme({ ...target, dirty: false }));
        }
        dispatch(ThemeActions.setIsLoading(false));

        // switch to the requested theme
        setShowSaveAlert(current => {
          dispatch(ThemeActions.setSelectedTheme(current));
          return null;
        });
      })
      .catch(error => showBoundary({ message: error }));
  }, [selectedTheme, selectedThemeId]);

  const onSave = useCallback((): void => {
    if (!selectedTheme) {
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

    onSaveTheme(selectedTheme).then(() => {
      setShowSaveAlert(current => {
        dispatch(ThemeActions.setSelectedTheme(current));
        return null;
      });
    });
  }, [selectedTheme, themes]);

  if (loading) {
    return (
      <div className={'m-xs'}>
        <LoadingLosenge label={t('str_MessageLoading')} />
      </div>
    );
  }

  return (
    <>
      <ul className="flex flex-col list-none m-sm">
        {Object.values(themes).map(theme => {
          return (
            <ThemeListItem
              key={theme.id}
              theme={theme}
              selected={selectedThemeId === theme.id}
              onSelectTheme={onSelectTheme}
              onDeleteTheme={onDeleteTheme}
              onCopyTheme={onCopyTheme}
            />
          );
        })}
      </ul>

      <PopOut
        open={null !== showSaveAlert}
        id={'theming-save-warning'}
        className={'flex-col'}
        shadowRoot={documentRoot as ShadowRoot}
        role="dialog"
        displayMode={'modal'}
      >
        <DialogContent>
          <p>{t('str_LabelConfirmChange', { ns: 'AdminTheming' })}</p>
        </DialogContent>
        <DialogFooter>
          <Button label={t('str_ButtonCancel')} buttonStyle={'negative'} onClick={onCancelThemeChange} />
          <Button label={t('str_ButtonDontSave')} buttonStyle={'secondary'} onClick={onDiscardChanges} />
          <Button label={t('str_ButtonSave')} onClick={onSave} />
        </DialogFooter>
      </PopOut>
    </>
  );
};

export default ThemeList;
