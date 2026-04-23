import React, { MouseEventHandler } from 'react';
import { Button, EditBoxIcon, OptionsIcon, ToggleSwitch } from '@taopix/taopix-design-system';

import { useTranslation } from 'react-i18next';

export interface EditMultipleButtonProps {
  sessionRef: number;
  toggleMultiSelect: () => void;
  multiSelect: boolean;
}

export const EditMultipleButton = ({ toggleMultiSelect, multiSelect, ...props }: EditMultipleButtonProps) => {
  const { t } = useTranslation();

  return (
    <ToggleSwitch
      label={t('str_ButtonEditMultiple', { ns: 'AdminExperience' })}
      id={'toggleEditMultiple'}
      defaultChecked={multiSelect}
      onChange={toggleMultiSelect}
      labelPosition="right"
      labelAlignment="left"
    />
  );
};
