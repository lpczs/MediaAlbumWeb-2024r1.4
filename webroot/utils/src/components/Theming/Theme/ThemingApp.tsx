import React, { useCallback, useState, MouseEvent } from 'react';
import { useTranslation } from 'react-i18next';
import axios from 'axios';
import { useErrorBoundary } from 'react-error-boundary';
import { Button, DialogContent, DialogFooter, LoadingOverlay, PlusIcon, PopOut, Theme, ThemeName } from '@taopix/taopix-design-system';
import { ThemeType, useTheming } from '../Context/ThemeContext';
import ThemeList from './ThemeList';
import ThemeEditor from './ThemeEditor';
import { ThemeActions } from '../Actions/ThemeActions';
import { OwnerType } from '../../../Enums';

export type ThemingAppProps = {
  documentRoot: Document | ShadowRoot;
};

const ThemingApp = ({ documentRoot }: ThemingAppProps) => {
  const [loading, setLoading] = useState(false);
  const [deletingTheme, setDeletingTheme] = useState<ThemeType>(null);
  const [showDeleteConfirmation, setShowDeleteConfirmation] = useState(false);
  const {dispatch, state: {themes, selectedThemeId}} = useTheming();
  const {showBoundary} = useErrorBoundary();
  const { t } = useTranslation(['AdminTheming', '*']);

  // default scheme name
  const DEFAULT_NAME = t('AdminTheming:str_LabelUntitled');

  const onAttemptDelete = (theme: ThemeType) => {
    setShowDeleteConfirmation(true);
    setDeletingTheme(theme);
  };

  const onCancelDeleteTheme = () => {
    setShowDeleteConfirmation(false);
    setDeletingTheme(null);
  };

  const onCreateTheme = useCallback(
    (event: MouseEvent): void => {
      let name = DEFAULT_NAME;
      const untitledCount = Object.values(themes).filter(t => t.name.includes(DEFAULT_NAME)).length;
      if (untitledCount > 0) {
        name = `${name} ${untitledCount + 1}`;
      }

      setLoading(true);

      const theme: ThemeType = {
        id: -(Object.keys(themes).length + 1),
        name: name,
        dateCreated: new Date().toDateString(),
        hash: '',
        defaultSchemeId: 1,
        darkSchemeId: 0,
        type: OwnerType.User,
        dirty: false,
      };

      const { id, defaultSchemeId, darkSchemeId } = theme;

      axios.post<ThemeType>('/api/theme/save-theme', {
        id,
        name,
        defaultSchemeId,
        darkSchemeId
      })
      .then(({ data }) => {
        const payload = { ...theme, ...data, dirty: false };
        dispatch(ThemeActions.addTheme(payload));
        return payload;
      })
      .then(res => {
        dispatch(ThemeActions.setSelectedTheme(res.id));
      })
      .finally(() => {
        setLoading(false);
      })
      .catch(error => {
        showBoundary({
          message: t('AdminTheming:str_TitleErrorSavingScheme'),
        });
      });
    },
    [themes]
  );

  /**
   * Delete a theme
   *
   * @param theme
   * @returns void
   */
  const onDeleteScheme = useCallback((): void => {
    if (OwnerType.System === deletingTheme.type) {
      return void 0;
    }

    // if this theme hasn't been saved, just remove it from the store...
    if (0 > deletingTheme.id) {
      dispatch(ThemeActions.setSelectedTheme(null));
      dispatch(ThemeActions.deleteTheme(deletingTheme));
      setShowDeleteConfirmation(false);
      return setDeletingTheme(null);
    }

    setShowDeleteConfirmation(false);
    setLoading(true);

    axios
      .post<{ result: boolean }>('/api/theme/delete-theme', {
        id: deletingTheme.id,
      })
      .then(() => {
        // if we are deleting the selected theme, de-select it.
        if (selectedThemeId === deletingTheme.id) {
          dispatch(ThemeActions.setSelectedTheme(null));
        }
        dispatch(ThemeActions.deleteTheme(deletingTheme));
      })
      .catch(error => {
        showBoundary({
          message: error.response.data,
        });
      })
      .finally(() => {
        setLoading(false);
        setDeletingTheme(null);
      });
  }, [deletingTheme, selectedThemeId]);

  const onSaveTheme = (theme: ThemeType) => {
    if (!theme.dirty) {
      return Promise.reject('Nothing to save');
    }

    const { id, name, defaultSchemeId, darkSchemeId } = theme;

    setLoading(true);

    return axios.post<ThemeType>('/api/theme/save-theme', {
      id,
      name,
      defaultSchemeId,
      darkSchemeId
    })
    .then(({ data }) => {
      // if this is a new theme, remove the template
      if (data.id !== theme.id) {
        dispatch(ThemeActions.deleteTheme(theme));
      }
      // select the new theme
      dispatch(ThemeActions.setSelectedTheme(data.id));
      // set the changes
      dispatch(ThemeActions.updateTheme({ ...data, dirty: false }));

      return data;
    })
    .catch(error => {
      showBoundary({
        message: t('AdminTheming:str_TitleErrorSavingTheme'),
      });
    })
    .finally(() => {
      setLoading(false);
    });
  };

  return (
    <div id="schemes" className="flex flex-col flex-1">
      <LoadingOverlay open={loading} id={'theming-busy-overlay'} usePortal={false} />
      <div className="flex w-full p-sm border-b border-b-themeBorderColour">
        <Button
          onClick={onCreateTheme}
          buttonStyle="primary"
          corners="theme"
          label={t('str_ButtonNewTheme', { ns: '*' })}
          startIcon={<PlusIcon />}
          size="small"
        />
      </div>
      <div className="flex flex-1 overflow-hidden">
        <Theme
          name={ThemeName.Container}
          className="w-[250px] overflow-y-auto"
          allowBorder={false}
          allowCorners={false}
        >
          <ThemeList documentRoot={documentRoot} onDeleteTheme={onAttemptDelete} onSaveTheme={onSaveTheme} />
        </Theme>
        <div className="flex flex-1 flex-col pt-lg px-lg">
          <ThemeEditor documentRoot={documentRoot} />
        </div>
      </div>
      <PopOut
        open={showDeleteConfirmation}
        id={'theming-save-warning'}
        className={'flex-col w-[600px]'}
        shadowRoot={documentRoot as ShadowRoot}
        role="dialog"
        displayMode={'modal'}
      >
        <DialogContent>
          <p>{t('AdminTheming:str_TitleConfirmDelete')}</p>
        </DialogContent>
        <DialogFooter>
          <Button label={t('*:str_ButtonCancel')} buttonStyle={'negative'} onClick={onCancelDeleteTheme} />
          <Button label={t('*:str_ButtonDelete')} onClick={onDeleteScheme} />
        </DialogFooter>
      </PopOut>
    </div>
  );
};

export default ThemingApp;
