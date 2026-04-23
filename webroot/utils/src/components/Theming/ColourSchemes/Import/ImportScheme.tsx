import React, { ChangeEvent, forwardRef, useCallback, useState, useEffect } from 'react';
import { AxiosResponse } from 'axios';
import { useTranslation } from 'react-i18next';
import { PopOut, DialogContent, DialogHeader, DialogFooter, Button } from '@taopix/taopix-design-system';
import { ColourScheme, useTheming } from '../../Context/ThemeContext';
import { OwnerType } from '../../../../Enums';
import SchemeValidator from '../../../../utils/Theming/SchemeValidator';
import { ThemeActions } from '../../Actions/ThemeActions';
import { ErrorObject } from 'ajv';

export type ImportSchemeProps = {
  documentRoot: Document | ShadowRoot;
  onSave: (scheme: ColourScheme) => Promise<AxiosResponse<ColourScheme>>;
};

export type ImportFeedback = Record<string, string[]>;

const ImportScheme = forwardRef<HTMLInputElement, ImportSchemeProps>(({ onSave, documentRoot }, ref) => {
  const [importing, setIsImporting] = useState(false);
  const [feedback, setFeedback] = useState<ImportFeedback>({});
  const [files, setFiles] = useState<File[]>([]);
  const [processing, setProcessing] = useState(0);

  const {
    state: { colourSchemes, schema },
    dispatch,
  } = useTheming();

  const { t } = useTranslation();

  useEffect(() => {
    if (0 !== files.length) {
      setIsImporting(true);
    }
  }, [files, importing]);

  const onCreateScheme = useCallback(
    (file: File, index: number) => {
      setFeedback(current => {
        return {
          ...current,
          [file.name]: [t('AdminTheming:str_TitleImportingFile').replace('^0', file.name)],
        };
      });
      const url = URL.createObjectURL(file);
      return fetch(url)
        .then(payload => {
          return payload.json().then(scheme => {
            return SchemeValidator(scheme)
            .then(() => {
                let name: string = scheme.name
                const count = Object.values(colourSchemes).filter(scheme => scheme.name.startsWith(name)).length;

                Object.values(colourSchemes).forEach(colourScheme => {
                  if (colourScheme.name === scheme.name){
                    // 'name' already exists add 'name copy'
                    name = t('AdminTheming:str_LabelThemeNameCopy').replace('^0', scheme.name) 
                  } else if (colourScheme.name === t('AdminTheming:str_LabelThemeNameCopy').replace('^0', scheme.name)) {
                    // 'name copy' already exists add 'name copy (count)'
                    name = `${t('AdminTheming:str_LabelThemeNameCopy').replace('^0', scheme.name)} (${count})`
                  }
                })

                return onSave({
                  id: -(Object.keys(colourSchemes).length + (index + 1)),
                  name: name,
                  data: schema,
                  dataLength: 0,
                  hash: '',
                  type: OwnerType.User,
                  diff: scheme.data,
                  dirty: true,
                }).then(({ data }) => {
                  dispatch(ThemeActions.addColourScheme(data));
                  URL.revokeObjectURL(url);
                  setFeedback(current => {
                    return {
                      ...current,
                      [file.name]: [
                        ...current[file.name],
                        t('AdminTheming:str_TitleImportSuccess').replace('^0', file.name),
                      ],
                    };
                  });
                  return file;
                });
              })
              .catch(error => {
                console.error(error);
                setFeedback(current => {
                  return {
                    ...current,
                    [file.name]: [
                      ...current[file.name],
                      t('AdminTheming:str_TitleErrorImportingScheme').replace('^0', file.name),
                    ],
                  };
                });
                setFiles(current => current.filter(f => f !== file));
              });
          });
        })
        .catch(error => {
          setFeedback(current => {
            return {
              ...current,
              [file.name]: [
                ...current[file.name],
                t('AdminTheming:str_TitleErrorImportingScheme').replace('^0', file.name),
              ],
            };
          });
          setFiles(current => current.filter(f => f !== file));
        });
    },
    [colourSchemes]
  );

  const onImportSchemes = (event: ChangeEvent<HTMLInputElement>) => {
    const input = event.target as HTMLInputElement;
    const payload = Array.from(input.files ?? undefined ?? []);
    setFiles(payload);
    setProcessing(payload.length);
    payload.forEach((file, index) => {
      onCreateScheme(file, index)
        .then(read => {
          setFiles(current => current.filter(f => f !== read));
        })
        .finally(() => {
          setProcessing(current => current - 1);
        });
    });

    // empty the input
    try {
      input.value = null;
    } catch (e) {
      // do nothing
    }
  };

  const onCloseFeedback = () => {
    setIsImporting(false);
    setFiles([]);
  };

  return (
    <>
      <input
        type="file"
        name="import"
        accept={'application/json'}
        multiple
        style={{ display: 'none' }}
        ref={ref}
        onChange={event => onImportSchemes(event)}
      />

      <PopOut
        open={importing}
        id={'colour-scheme-processing'}
        className={'flex-col w-[400px]'}
        shadowRoot={documentRoot as ShadowRoot}
        role="dialog"
        displayMode={'modal'}
        afterClose={() => {
          setFeedback({});
        }}
      >
        <DialogHeader>Import Progress</DialogHeader>
        <DialogContent>
          <div className="p-md">
            {Object.values(feedback).map((f, index) => {
              return (
                <ul className="list-disc mb-4">
                  {f.map((entry, idx) => (
                    <li key={`${index}-${idx}`}>{entry}</li>
                  ))}
                </ul>
              );
            })}
          </div>
        </DialogContent>
        <DialogFooter>
          <Button
            label={t('*:str_ButtonClose')}
            buttonStyle={'negative'}
            onClick={onCloseFeedback}
            disabled={processing > 0}
            loading={processing > 0}
          />
        </DialogFooter>
      </PopOut>
    </>
  );
});

export default ImportScheme;
