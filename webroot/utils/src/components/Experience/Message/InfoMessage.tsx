import React, { ReactElement, useId, useState } from 'react';
import { Icon, Theme, ThemeName } from '@taopix/taopix-design-system';
import { useTranslation } from 'react-i18next';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}

export interface InfoMessageProps {
  warningText: string;
  themeName: ThemeName | 'none';
  icon: ReactElement;
}

export const InfoMessage = ({ warningText, themeName, icon }: InfoMessageProps) => {
  const { t } = useTranslation();

  const id = 'infoMessage-' + useId().replaceAll(':', '');

  return (
    <>
      {warningText !== '' && (
        <Theme id={id} name={themeName} allowCorners={false} className={'flex p-xs rounded-xs group/message'}>
          <Icon className={'mr-2'} size="xsmall" icon={icon} />
          <span className={'group-hover/message:line-clamp-none line-clamp-1 overflow-ellipsis leading-5'}>
            {t(warningText, { ns: 'AdminExperience' })}
          </span>
        </Theme>
      )}
    </>
  );
};
