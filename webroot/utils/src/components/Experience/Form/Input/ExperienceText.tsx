import React, { useEffect, useCallback, useRef } from 'react';
import { TextInput } from '@taopix/taopix-design-system';
import { useTranslation } from 'react-i18next';
import { ExperienceInputProps } from '../../../../types';
import validator from 'validator';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}

export const ExperienceText = ({
  onSetErrorArray,
  currentKey,
  errorArray,
  dataPath,
  theKey,
  dependenciesControl,
  parentKey,
  value,
  experienceId,
  item,
  className,
  changeEvent,
  ...props
}: ExperienceInputProps) => {
  const { t } = useTranslation();

  const textValueChange = useCallback((event: any, dataPath: string, validatorName: string = '') => {
    let errorMessage = '';

    switch (validatorName) {
      case 'url':
        if (!validator.isURL(event.target.value) && event?.target?.value !== '') {
          errorMessage = t('str_ErrorNotValidURL', { ns: 'AdminExperience' });
        }
        break;

      default:
        break;
    }

    let changes = false;

    if (item.required && (event === null || event.target.value === '')) {
      errorMessage = t('str_ExtJsTextFieldBlank', { ns: '*' });
      changes = true;
    }

    let errorArrayClone = errorArray;

    if (errorMessage !== '') {
      errorArrayClone[dataPath] = { dataPath: dataPath, message: errorMessage };
      changes = true;
    } else {
      if (errorArray.hasOwnProperty(dataPath)) {
        delete errorArrayClone[dataPath];
        changes = true;
      }
    }

    if (changes) {
      onSetErrorArray(errorArrayClone);
    }

    if (event !== null) {
      changeEvent(false, event, dataPath, []);
      const savedPos = event.target.selectionStart;

      setTimeout(() => {
        // restore cursor position
        const shadowDiv = document.getElementById('shadow');
        if (shadowDiv) {
          const input = shadowDiv.shadowRoot.getElementById(dataPath + '_' + theKey + '_' + experienceId) as HTMLInputElement;
          if (input) {
            input.setSelectionRange(savedPos, savedPos);
          }
        }
      }, 0);
    }
  }, []);

  useEffect(() => {
    if (value === '') {
      textValueChange(null, dataPath, '');
    }
  }, []);

  return (
      <TextInput
        key={dataPath + '_' + theKey + '_' + experienceId}
        placeholder={t(item.label, { ns: 'AdminExperience' })}
        id={dataPath + '_' + theKey + '_' + experienceId}
        name={item.label}
        defaultValue={value as string}
        onChange={e => textValueChange(e, dataPath, item.hasOwnProperty('validator') ? item.validator : '')}
        autoFocus={currentKey === dataPath}
        required={item.hasOwnProperty('required') && item.required}
        error={errorArray.hasOwnProperty(dataPath) ? errorArray[dataPath].message : ''}
        onChangeDelay={500}
        className={'w-[200px]'}
      />
  );
};
