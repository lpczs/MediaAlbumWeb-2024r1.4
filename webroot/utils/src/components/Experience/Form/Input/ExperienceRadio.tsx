import React from 'react';
import { Heading, InfoIcon, RadioButton, ThemeName } from '@taopix/taopix-design-system';
import { useTranslation } from 'react-i18next';
import { ExperienceInputProps } from '../../../../types';
import { InfoMessage } from '../../Message/InfoMessage';
import { ExperienceSystemType } from '../../../../Enums';
import classNames from 'classnames';
import { InfoButton } from '../../Message/InfoButton';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}

export const ExperienceRadio = ({
  features,
  isDisabled,
  className,
  disabled,
  dataPath,
  theKey,
  parentKey,
  value,
  experienceId,
  item,
  dependenciesControl,
  changeEvent,
  productType,
  retroPrint,
  systemType,
  componentMountPoint,
  ...props
}: ExperienceInputProps) => {
  const { t } = useTranslation();

  const headingClasses = classNames('mb-2', disabled.disabled && 'opacity-40');

  return (
    <div key={experienceId + dataPath + parentKey + theKey} role="radiogroup">
      {item.label && (
        <Heading className={headingClasses} size={4}>
          {t(item.label, { ns: 'AdminExperience' })}
        </Heading>
      )}
      {Object.entries(item.options).map(([optionKey, option]: any) => {
        // Don't add this option if it is not for this product type
        if (option.hasOwnProperty('productType') && option.productType !== productType) {
          return null;
        }

        // Disable the option if set as disabled or is a retro print and retroPrint=false
        // This allows us to show the options but disable (rather than hide) the irrelevant ones for context
        let optionDisabled = disabled.disabled || option.hasOwnProperty('retroPrint') && option.retroPrint !== retroPrint;

        let disabledHelpText = ''; //radio group will display the text once, not all radios need to show it

        if (
          !optionDisabled &&
          option.hasOwnProperty('condition') &&
          option.condition.hasOwnProperty('disableControl')
        ) {
          const disabledData = isDisabled(option.condition.disableControl, dataPath);
          optionDisabled = disabledData.disabled;
          disabledHelpText = disabledData.disabled ?? '';
        }

        //if the section is only enabled when a feature exists on license key
        if (option.hasOwnProperty('condition') && option.condition.hasOwnProperty('featureControl')) {
          if (option.condition.featureControl.hasOwnProperty('aiEnabled') && !features.ai) {
            optionDisabled = true;
            disabledHelpText = option.condition.featureControl.hasOwnProperty('helpText')
              ? option.condition.featureControl.helpText
              : '';
          }
        }

        const radioClasses = classNames(option.hasOwnProperty('hidden') && option.hidden && 'hidden');
        const inputContainerClasses = classNames('flex w-[250px]', item.label && 'pl-[25px]'); // Apply an indent if there is a title for this group

        return (
          <div key={dataPath + '_' + optionKey + '_' + option.key} className={'flex items-center mb-sm'}>
            <div className={inputContainerClasses}>
              <RadioButton
                id={dataPath + '_' + experienceId + '_' + optionKey + '_' + option.key}
                name={dataPath + '_' + experienceId + '_' + optionKey + '_' + option.key}
                value={option.key}
                label={t(option.label, {
                  ns: 'AdminExperience',
                })}
                groupName={experienceId + parentKey + theKey}
                defaultChecked={option.key === value}
                onChange={e => {
                  changeEvent(false, e, dataPath, dependenciesControl);
                }}
                disabled={optionDisabled}
                className={radioClasses}
              />
            </div>
            <div className={'flex w-[calc(100%-250px)]'}>
              {!optionDisabled && option.hasOwnProperty('helpText') && (
                <InfoButton
                  warningText={option.helpText}
                  themeName={ThemeName.Container}
                  icon={<InfoIcon />}
                  componentMountPoint={componentMountPoint}
                />
              )}
              {optionDisabled && systemType === ExperienceSystemType.CUSTOM && (
                <InfoMessage warningText={disabledHelpText} themeName={ThemeName.Container} icon={<InfoIcon />} />
              )}
            </div>
          </div>
        );
      })}
    </div>
  );
};
