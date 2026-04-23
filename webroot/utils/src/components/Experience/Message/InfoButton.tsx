import React, { ReactElement, useEffect, useId, useRef, useState } from 'react';
import { Button, Horizontal, InfoIcon, PopOut, ThemeName, Vertical } from '@taopix/taopix-design-system';
import { useTranslation } from 'react-i18next';
import { getScrollParent } from '../../../common';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}

export interface InfoButtonProps {
  warningText: string;
  themeName: ThemeName;
  icon: ReactElement;
  componentMountPoint?: Element | Document | ShadowRoot;
}

export const InfoButton = ({ warningText, themeName, icon, componentMountPoint }: InfoButtonProps) => {
  const { t } = useTranslation();

  const buttonRef = useRef<HTMLButtonElement>()
  const [showHelpTip, setShowHelpTip] = useState<boolean>(false);

  const id = 'helpMessage-' + useId().replaceAll(':', '');

  useEffect(() => {
    const scrollElement = getScrollParent(buttonRef?.current);
    // Add a scroll listener to the scrollParent to close the popout when it is scrolled.
    if (showHelpTip) {
      scrollElement?.addEventListener('scroll', () => setShowHelpTip(false));
    }
    return () => {
      // Remove the handler
      scrollElement?.removeEventListener('scroll', () => setShowHelpTip(false));
    };
  },[showHelpTip])

  return (
    <>
      {warningText !== '' && (
        <>
          <Button
            ref={buttonRef}
            label={t('str_LabelHelp')}
            hideLabel
            size={'small'}
            boxless
            className={'p-xs'}
            startIcon={<InfoIcon />}
            buttonStyle={'standard'}
            onClick={() => setShowHelpTip(!showHelpTip)}
            id={id}
          />
          <PopOut
            open={showHelpTip}
            anchorId={id}
            anchorOrigin={{ horizontal: Horizontal.Right, vertical: Vertical.Middle }}
            transformOrigin={{ horizontal: Horizontal.Left, vertical: Vertical.Middle }}
            theme={ThemeName.Information}
            popDirection="right"
            shadowRoot={componentMountPoint}
            className={'text-md p-xs max-w-[500px] leading-tight'}
            onClickOutside={() => setShowHelpTip(false)}
            excludeIdFromClickOutside={id}
            edgeBehaviour="flip"
          >
            {t(warningText, { ns: 'AdminExperience' })}
          </PopOut>
        </>
      )}
    </>
  );
};
