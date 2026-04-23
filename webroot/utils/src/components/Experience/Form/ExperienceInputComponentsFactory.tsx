import React, { useDeferredValue } from 'react';
import { useTranslation } from 'react-i18next';
import { get } from 'lodash';
import { ConditionControl, Experience, ExperienceError, ExperienceInputProps, Features } from '../../../types';
import { ExperienceSystemType, SettingsDataType } from '../../../Enums';
import { ExperienceRadio } from './Input/ExperienceRadio';
import { ExperienceCheckbox } from './Input/ExperienceCheckbox';
import { ExperienceNumber } from './Input/ExperienceNumber';
import { ExperienceTextLocalised } from './Input/ExperienceTextLocalised';
import { ExperienceText } from './Input/ExperienceText';
import { InfoIcon, ThemeName } from '@taopix/taopix-design-system';
import { InfoMessage } from '../Message/InfoMessage';
import classNames from 'classnames';
import { InfoButton } from '../Message/InfoButton';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}
export interface ExperienceInputComponentsFactoryProps {
  item: any;
  experience: Experience;
  dataPath: string;
  dependenciesControl: Array<ConditionControl>;
  theKey: string;
  parentKey: string;
  onChangeExperience: (
    value: boolean,
    event: any,
    datapath: string,
    dependenciesControl: Array<ConditionControl>
  ) => void;
  className: string;
  componentMountPoint: Element;
  errorArray: { [dataPath: string]: ExperienceError };
  currentKey: string;
  disabled: { disabled: boolean; disabledText: string };
  disabledHelpText: string;
  onSetErrorArray: Function;
  isDisabled: Function;
  features: Features;
}

export const ExperienceInputComponentsFactory = ({
  features,
  isDisabled,
  onSetErrorArray,
  disabledHelpText,
  disabled,
  onChangeExperience,
  errorArray,
  currentKey,
  componentMountPoint,
  dataPath,
  dependenciesControl,
  theKey,
  parentKey,
  experience,
  item,
  className,
  ...props
}: ExperienceInputComponentsFactoryProps) => {
  const { t } = useTranslation();

  const renderItem = (): React.ReactElement => {
    let inputProps: ExperienceInputProps = {
      theKey: theKey,
      item: item,
      experienceId: experience.id,
      dependenciesControl: dependenciesControl,
      dataPath: 'data.' + dataPath.substring(1),
      parentKey: parentKey,
      changeEvent: onChangeExperience,
      className: className,
      value: '',
      errorArray: errorArray,
      currentKey: currentKey,
      componentMountPoint: componentMountPoint,
      disabled: disabled,
      onSetErrorArray: onSetErrorArray,
      isDisabled: isDisabled,
      features: features,
      productType: experience.productType,
      retroPrint: experience.retroPrint,
      systemType: experience.systemType,
    };
    let componentType;

    let theValue = get(experience.data, dataPath.substring(1), '');
    theValue = (theValue !== null) ? theValue.toString() : '';

    switch (item.dataType) {
      case SettingsDataType.Checkbox:
        inputProps.value = (theValue === 'true');
        componentType = ExperienceCheckbox;
        break;

      case SettingsDataType.Radio:
        inputProps.value = theValue;
        componentType = ExperienceRadio;
        break;

      case SettingsDataType.Number:
        inputProps.value = (theValue != '') ? parseInt(theValue) : 0;
        componentType = ExperienceNumber;
        break;

      case SettingsDataType.Text:
      default:
        inputProps.value = useDeferredValue(theValue);
        if (item.hasOwnProperty('localized') && item.localized) {
          componentType = ExperienceTextLocalised;
        } else {
          componentType = ExperienceText;
        }
        break;
    }

    return React.createElement(componentType, {
      ...inputProps,
      key: parentKey + theKey + '_' + experience.id + '_' + dataPath,
    });
  };

  const showMessageColumn =
    (!disabled.disabled && item.hasOwnProperty('helpText')) ||
    (disabled.disabled && experience.systemType === ExperienceSystemType.CUSTOM && disabledHelpText);

  const itemRowClasses = classNames('flex mb-sm', item.hasOwnProperty('hidden') && item.hidden && 'hidden');

  const inputContainerClasses = classNames(
    'flex',
    'items-start',
    'mt-xs',
    'leading-5',
    item.level === 2 && 'pl-[25px]',
    item.level === 3 && 'pl-[50px]',
    item.level === 4 && 'pl-[75px]',
    showMessageColumn && 'w-[250px]' // Limit the width if there is an info message
  );

  const messageContainerClasses = classNames('flex', 'items-start', 'leading-5', 'w-[calc(100%-250px)]');

  return (
    <div className={itemRowClasses}>
      <div className={inputContainerClasses}>{renderItem()}</div>
      {showMessageColumn && (
        <div className={messageContainerClasses}>
          {!disabled.disabled && item.hasOwnProperty('helpText') && (
            <InfoButton
              warningText={item.helpText}
              themeName={ThemeName.Container}
              icon={<InfoIcon />}
              componentMountPoint={componentMountPoint}
            />
          )}
          {disabled.disabled && experience.systemType === ExperienceSystemType.CUSTOM && disabledHelpText && (
            <InfoMessage warningText={disabledHelpText} themeName={ThemeName.Container} icon={<InfoIcon />} />
          )}
        </div>
      )}
    </div>
  );
};
