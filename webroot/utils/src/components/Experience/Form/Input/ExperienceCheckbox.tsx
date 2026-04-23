import React from 'react';
import { Checkbox } from '@taopix/taopix-design-system';
import { useTranslation } from 'react-i18next';
import { ExperienceInputProps } from '../../../../types';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}

export const ExperienceCheckbox = ({
  disabled,
  dataPath,
  dependenciesControl,
  theKey,
  parentKey,
  value,
  experienceId,
  item,
  className,
  changeEvent,
  ...props
}: ExperienceInputProps) => {
  const { t } = useTranslation();

  const checkboxChange = (isChecked: boolean, event: React.FormEvent) => {
    changeEvent(isChecked, event, dataPath, dependenciesControl);
  };

  return (
    <Checkbox
      label={t(item.label, { ns: 'AdminExperience' })}
      id={dataPath + '_' + theKey + '_' + experienceId}
      key={theKey + '_' + experienceId + value}
      name={theKey}
      defaultChecked={value === true}
      onChange={checkboxChange}
      disabled={(item.hasOwnProperty('disabled') && item.disabled === true) || disabled.disabled}
    />
  );
};
