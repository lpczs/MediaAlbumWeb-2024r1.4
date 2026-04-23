import React, { useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { LocalisedStringInput } from '../../../Language/LocalisedStringInput';
import { ExperienceInputProps } from '../../../../types';
import { SettingsDataType } from '../../../../Enums';
import { ThemeName, WarningIcon } from '@taopix/taopix-design-system';
import { InfoMessage } from '../../Message/InfoMessage';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}

export const ExperienceTextLocalised = ({
  errorArray,
  onSetErrorArray,
  componentMountPoint,
  currentKey,
  dataPath,
  dependenciesControl,
  theKey,
  parentKey,
  value,
  experienceId,
  item,
  className,
  changeEvent,
  disabled,
  ...props
}: ExperienceInputProps) => {
  const { t } = useTranslation();

  const checkValid = (item: any, value: string) => {
    let errorArrayClone = { ...errorArray };
    let changes: boolean = false;

    if (item.required && value === '' && !disabled) {
      errorArrayClone[dataPath] = { dataPath: dataPath, message: t('str_ExtJsTextFieldBlank', { ns: '*' }) };
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
  };

  const onSaveLocalisedString = (identifier: string, value: string, callbackFunction: Function) => {
    const event = { target: { type: SettingsDataType.Text, value: value, dataPath: identifier } };

    checkValid(item, value);

    callbackFunction();
    changeEvent(false, event, dataPath, []);
  };

  useEffect(() => {
    if ( (!errorArray.hasOwnProperty(dataPath) && value === '' || errorArray.hasOwnProperty(dataPath) && (value !== '' || disabled) )   ) {
      checkValid(item, value.toString());
    }
  }, [value]);

  return (
    <>
      <LocalisedStringInput
        identifier={dataPath}
        onSaveString={onSaveLocalisedString}
        componentMountPoint={componentMountPoint}
        header={t(item.label, { ns: 'AdminExperience' })}
        editLabel={(item.hasOwnProperty('editLabel') && item.editLabel) ? t(item.editLabel,{ns:'AdminExperience'}) : t('str_ButtonEdit',{ns:'*'})}
        valueString={value as string}
        focusKey={currentKey}
        disabled={disabled}
        theKey={theKey}
        parentKey={parentKey}
      />
      {errorArray.hasOwnProperty(dataPath) && (
        <InfoMessage warningText={errorArray[dataPath].message} themeName={ThemeName.Warning} icon={<WarningIcon />} />
      )}
    </>
  );
};
