import React, { ChangeEvent, MouseEvent, useCallback, useMemo, useRef, useState } from 'react';
import { useTranslation } from 'react-i18next';
import axios, { AxiosResponse } from 'axios';
import { omit } from 'lodash';
import { useErrorBoundary } from 'react-error-boundary';
import {
  Button,
  DialogContent,
  DialogFooter,
  LoadingOverlay,
  PlusIcon,
  PopOut,
  Theme,
  ThemeName,
  UploadIcon,
} from '@taopix/taopix-design-system';
import ColourSchemeList from './ColourSchemeList';
import ColourSchemeEditor from './ColourSchemeEditor';
import { ColourScheme, useTheming } from '../Context/ThemeContext';
import { ThemeActions } from '../Actions/ThemeActions';
import { OwnerType } from '../../../Enums';
import SchemeValidator from '../../../utils/Theming/SchemeValidator';
import { useIssuesDialog } from '../Context/IssuesContext';
import ImportScheme from './Import/ImportScheme';

export type ColourSchemesProps = {
  documentRoot: Document | ShadowRoot;
};

const ColourSchemesApp = ({ documentRoot }: ColourSchemesProps) => {
  const { t } = useTranslation(['AdminTheming', '*']);
  const {
    state: { colourSchemes, selectedSchemeId, schema },
    dispatch,
  } = useTheming();
  const { showBoundary } = useErrorBoundary();

  const [loading, setLoading] = useState(false);
  const [deletingScheme, setDeletingScheme] = useState<ColourScheme>(null);
  const [showDeleteConfirmation, setShowDeleteConfirmation] = useState(false);
  const { dispatch: dispatchIssue } = useIssuesDialog();

  const uploadRef = useRef<HTMLInputElement>(null);

  // default scheme name
  const DEFAULT_NAME = t('AdminTheming:str_LabelUntitled');

  const onCreateScheme = useCallback(
    (event: MouseEvent): void => {
      let name = DEFAULT_NAME;
      const untitledCount = Object.values(colourSchemes).filter(t => t.name.includes(DEFAULT_NAME)).length;
      if (untitledCount > 0) {
        name = `${name} ${untitledCount + 1}`;
      }

      setLoading(true);

      const scheme = {
        id: -(Object.keys(colourSchemes).length + 1),
        name: name,
        dateCreated: new Date().toDateString(),
        data: schema,
        dataLength: 0,
        hash: '',
        type: OwnerType.User,
        diff: {},
        dirty: false,
      };

      axios
        .post<ColourScheme>('/api/theme/save-colour-scheme', {
          id: scheme.id,
          name: scheme.name,
          changes: scheme.diff,
        })
        .then(({ data }) => {
          const payload = { ...scheme, ...data, dirty: false };
          dispatch(ThemeActions.addColourScheme(payload));
          return payload;
        })
        .then(res => {
          dispatch(ThemeActions.setSelectedColourScheme(res.id));
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
    [colourSchemes]
  );

  const onAttemptDelete = (scheme: ColourScheme) => {
    setShowDeleteConfirmation(true);
    setDeletingScheme(scheme);
  };

  const onCancelDeleteTheme = () => {
    setShowDeleteConfirmation(false);
    setDeletingScheme(null);
  };

  /**
   * Delete a theme
   *
   * @param theme
   * @returns void
   */
  const onDeleteScheme = useCallback((): void => {
    if (OwnerType.System === deletingScheme.type) {
      return void 0;
    }

    // if this theme hasn't been saved, just remove it from the store...
    if (0 > deletingScheme.id) {
      dispatch(ThemeActions.setSelectedColourScheme(null));
      dispatch(ThemeActions.deleteColourScheme(deletingScheme));
      setShowDeleteConfirmation(false);
      return setDeletingScheme(null);
    }

    setShowDeleteConfirmation(false);
    setLoading(true);

    axios
      .post<{ result: boolean }>('/api/theme/delete-colour-scheme', {
        id: deletingScheme.id,
      })
      .then(() => {
        // if we are deleting the selected theme, de-select it.
        if (selectedSchemeId === deletingScheme.id) {
          dispatch(ThemeActions.setSelectedColourScheme(null));
        }
        dispatch(ThemeActions.deleteColourScheme(deletingScheme));
      })
      .catch(error => {
        showBoundary({
          message: error.response.data,
        });
      })
      .finally(() => {
        setLoading(false);
        setDeletingScheme(null);
      });
  }, [deletingScheme, selectedSchemeId]);

  const onSave = (scheme: ColourScheme): Promise<AxiosResponse<ColourScheme>> => {
    if (!scheme.dirty) {
      return Promise.reject('Nothing to save');
    }

    const { id, name, diff } = scheme;

    return axios
      .post<ColourScheme>('/api/theme/save-colour-scheme', {
        id,
        name,
        changes: diff,
      })
  };

  const onSaveScheme = (scheme: ColourScheme): Promise<ColourScheme | void> => {
    setLoading(true);
    return onSave(scheme)
      .then(({ data }) => {
        // if this is a new theme, remove the template
        if (data.id !== scheme.id) {
          dispatch(ThemeActions.deleteColourScheme(scheme));
        }
        // select the new theme
        dispatch(ThemeActions.setSelectedColourScheme(data.id));
        // set the changes
        dispatch(ThemeActions.updateColourScheme({ ...data, dirty: false }));

        return data;
      })
      .catch(error => {
        showBoundary({
          message: t('AdminTheming:str_TitleErrorSavingTheme'),
        });
      }).finally(() => {
        setLoading(false);
      });
  };

  const onOpenFileBrowser = () => {
    if (uploadRef.current) {
      uploadRef.current.click();
    }
  };

  const onImportSchemes = useCallback(
    (event: ChangeEvent<HTMLInputElement>) => {
      setLoading(true);
      const input = event.target as HTMLInputElement;
      const files = Array.from(input.files ?? undefined ?? []);
      console.log(files);
      if (files.length > 0) {
        files.forEach((file, index) => {
          const url = URL.createObjectURL(file);
          fetch(url).then(payload => {
            payload.json().then(scheme => {
              SchemeValidator(scheme)
                .then(() => {
                  const entry = {
                    id: -(Object.keys(colourSchemes).length + (index + 1)),
                    name: t('AdminTheming:str_LabelThemeNameCopy').replace('^0', scheme.name),
                    dateCreated: new Date().toDateString(),
                    data: schema,
                    dataLength: 0,
                    hash: '',
                    type: OwnerType.User,
                    diff: scheme.data,
                    dirty: true,
                  };
                  onSave(entry).then(({ data }) => {
                    dispatch(ThemeActions.addColourScheme(data));
                    URL.revokeObjectURL(url);
                  });
                })
                .catch(error => {
                  console.log('not added', error);
                });
            });
          }).catch(error => {
            // some feedback
          });
        });
      }
      
      try {
        input.value = null
      } catch (e) {
        // do nothing
      }

      if(input.value) {
        input.parentNode.replaceChild(input.cloneNode(true), input);
      }

      setLoading(false);
    },
    [colourSchemes]
  );

  const onExportScheme = (scheme: ColourScheme) => {
    const output = omit({...scheme, data: scheme.diff}, ['id', 'dirty', 'type', 'dataLength', 'hash', 'diff']);
    const blob = new Blob([JSON.stringify(output, null, 2)], {
      type: 'application/json',
    });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${scheme.name}.json`;
    a.click();
    URL.revokeObjectURL(url);
  };

  // grab the selected theme
  const selectedScheme = useMemo(() => {
    if (!selectedSchemeId) {
      return null;
    }
    return colourSchemes[selectedSchemeId];
  }, [colourSchemes, selectedSchemeId]);

  return (
    <div id="schemes" className="flex flex-col flex-1">
      <LoadingOverlay open={loading} id={'colour-scheme-busy-overlay'} usePortal={false} />
      <div className="flex w-full p-sm border-b border-b-themeBorderColour">
        <ImportScheme onSave={onSave} ref={uploadRef} documentRoot={documentRoot} />
        <Button
          onClick={onCreateScheme}
          buttonStyle="primary"
          corners="theme"
          label={t('str_ButtonNewColourScheme', { ns: '*' })}
          startIcon={<PlusIcon />}
          size="small"
        />
        <Button
          onClick={onOpenFileBrowser}
          buttonStyle="special"
          corners="theme"
          label={t('AdminTheming:str_LabelImport')}
          startIcon={<UploadIcon />}
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
          <ColourSchemeList 
            shadowRoot={documentRoot} 
            onDeleteScheme={onAttemptDelete} 
            onSaveScheme={onSaveScheme} 
            onExportScheme={onExportScheme}
          />
        </Theme>
        <div className="flex flex-1 flex-col pt-lg px-lg">
          <ColourSchemeEditor 
            shadowRoot={documentRoot} 
            scheme={selectedScheme} 
            onSaveScheme={onSaveScheme} 
            onExportScheme={onExportScheme}
          />
        </div>
      </div>
      <PopOut
        open={showDeleteConfirmation}
        id={'colour-scheme-save-warning'}
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

export default ColourSchemesApp;
