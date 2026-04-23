import React from 'react';
import { Label, NumberInput } from '@taopix/taopix-design-system';
import { useTranslation } from 'react-i18next';
import { ExperienceInputProps } from '../../../../types';
import { SettingsDataType } from '../../../../Enums';
import classNames from 'classnames';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}

export const ExperienceNumber = ({
  disabled,
  dataPath,
  theKey,
  parentKey,
  value,
  experienceId,
  item,
  dependenciesControl,
  className,
  changeEvent,
  ...props
}: ExperienceInputProps) => {
  const { t } = useTranslation();

  const numberValueChange = (value: number, interacting: boolean, event: React.FormEvent) => {
    const e = { target: { type: SettingsDataType.Number, value: value } };

    if (value !== 0) {
      changeEvent(false, e, dataPath, []);
    }
  };

  const labelClasses = classNames(disabled.disabled && 'opacity-40');

  return (
    <Label className={labelClasses} htmlFor={item.label + '_' + experienceId} label={t(item.label, { ns: 'AdminExperience' })}>
      <NumberInput
        max={item.max}
        min={item.min}
        onValueChange={numberValueChange}
        value={value as number}
        allowDecimal={item.allowDecimal}
        id={dataPath + '_' + theKey + '_' + experienceId}
        disabled={disabled.disabled}
        debounceDelay={500}
      />
    </Label>
  );
};
