import React, { ChangeEvent, forwardRef, useCallback, useState, useEffect } from 'react';
import { AxiosResponse } from 'axios';
import { useTranslation } from 'react-i18next';
import { PopOut, DialogContent, DialogHeader, DialogFooter, Button } from '@taopix/taopix-design-system';
import Ajv, { ErrorObject } from 'ajv';
import { Experience, ExperienceSaveServerResponse } from '../../../types';
import editorSchema from '../../../utils/Experience/editorSchema.json';
import settingsSchema from '../../../utils/Experience/settingsSchema.json';
import wizardSchema from '../../../utils/Experience/wizardSchema.json';
import { ExperienceType } from '../../../Enums';

export type ImportExperienceProps = {
  documentRoot: Document | ShadowRoot;
  onSave: (experience: Experience) => Promise<AxiosResponse<ExperienceSaveServerResponse>>;
  experiences: Experience[];
  isNameUnique: Function
  onUpdateExperience: Function
};

export type ImportFeedback = Record<string, string[]>;

const ImportExperience = forwardRef<HTMLInputElement, ImportExperienceProps>(({ onSave, onUpdateExperience, documentRoot, experiences, isNameUnique }, ref) => {
  const [importing, setIsImporting] = useState(false);
  const [feedback, setFeedback] = useState<ImportFeedback>({});
  const [files, setFiles] = useState<File[]>([]);
  const [processing, setProcessing] = useState(0);

  type ValidatorFn = (experience: Experience) => Promise<void>;

  const ExperienceValidator = (experience: Experience): Promise<void[]> => {
    return schemeValidator(experience, [
      experienceNameValidator,
      experienceDataValidator
    ]);
  }

  const experienceNameValidator = (experience: Experience): Promise<void> => {
    return new Promise((resolve, reject) => {
      if ('' === experience.name) {
        reject('Experience name cannot be blank');
      }
      if (!isNameUnique(experience.name,true)) {
        reject('Experience name should be unique');
      }
      resolve(void 0);
    })
  }

  const getSchema = (type: ExperienceType) => {
    let schema;
    switch (type) {
      case ExperienceType.EDITOR:
        schema = editorSchema;
        break;
      case ExperienceType.WIZARD:
        schema = wizardSchema;
        break;
      case ExperienceType.SETTINGS:
        schema = settingsSchema;
        break;
      default:
        schema = {};
        break;
    }
    return schema;
  }

  const experienceDataValidator = (experience: Experience): Promise<void> => {
    return new Promise((resolve, reject) => {
      const { data } = experience;

      // check if it's a valid object
      if ('object' !== typeof data) {
        reject('not an object');
      }

      // check if it's valid json
      if (!JSON.parse(JSON.stringify(data))) {
        reject('not a valid json object');
      }

      // if the data is an empty object, allow it through
      if (0 === Object.keys(data).length) {
        resolve(void 0);
      }

      // perform some schema validation
      const ajv = new Ajv();
      const validator = ajv.compile(getSchema(experience.experienceType));
      if (!validator(data)) {
        reject(validator.errors);
      }

      resolve(void 0);
    })
  }

  const schemeValidator = (experience: Experience, validators: Array<ValidatorFn>): Promise<void[]> => {
    return Promise.all([
      ...validators.map(validator => validator(experience))
    ]);
  }

  const { t } = useTranslation();

  useEffect(() => {
    if (0 !== files.length) {
      setIsImporting(true);
    }
  }, [files, importing]);

  const onCreateExperience = useCallback(
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
          return payload.json().then((experience: Experience) => {
            let date = new Date(Date.now());
            let timestamp = date.getFullYear() + '-' +
                ('00' + (date.getMonth()+1)).slice(-2) + '-' +
                ('00' + date.getDate()).slice(-2) + ' ' + 
                ('00' + date.getHours()).slice(-2) + ':' + 
                ('00' + date.getMinutes()).slice(-2) + ':' + 
                ('00' + date.getSeconds()).slice(-2);

            experience.name = experience.name + ' - ' + timestamp;

            return ExperienceValidator(experience)
              .then(() => {
                return onSave({
                  id: -1,
                  name: experience.name,
                  data: experience.data,
                  dataLength: 0,
                  isdirty: true,
                  experienceType: experience.experienceType,
                  productType: experience.productType,
                  retroPrint: experience.retroPrint,
                  assignment: [],
                  systemType: experience.systemType,
                  code: ''
                }).then((response) => {
                  onUpdateExperience(
                    {
                      ...experience,
                      ['id']: response.data.data.experienceid,
                    },
                    false
                  );
                }).then(() => {
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
    [experiences]
  );

  const onImportExperiences = (event: ChangeEvent<HTMLInputElement>) => {
    const input = event.target as HTMLInputElement;
    const payload = Array.from(input.files ?? undefined ?? []);
    setFiles(payload);
    setProcessing(payload.length);
    payload.forEach((file, index) => {
      onCreateExperience(file, index)
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
        onChange={event => onImportExperiences(event)}
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
        <DialogHeader>{t('AdminExperience:str_LabelImportProgress')}</DialogHeader>
        <DialogContent>
          <div className="p-md">
            {Object.values(feedback).map((f, index) => {
              return (
                <ul key={`${index}`} className="list-disc mb-4">
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

export default ImportExperience;
