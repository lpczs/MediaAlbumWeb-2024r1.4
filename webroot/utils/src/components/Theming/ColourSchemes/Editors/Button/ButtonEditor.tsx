import React, { useCallback, useState } from 'react';
import { useTranslation } from 'react-i18next';
import {
  PopOut,
  DialogHeader,
  DialogContent,
  DialogFooter,
  Button,
  Dialog,
  ChevronRightIcon,
  Icon,
} from '@taopix/taopix-design-system';
import { formatTitle } from '../../../../../common';
import ButtonEditorContent from './ButtonEditorContent';
import { Selector, States } from '../../../../../types';
import { subThemeNameStrings } from '../Editor';

export type ButtonEditorProps = {
  name: string;
  props: Selector;
  open: boolean;
  shadowRoot: Document | ShadowRoot;
  subTheme: string;
  editable: boolean;
  section: string;
  contentStyle?: string;
  onClose: () => void;
};

const ButtonEditor = ({
  props,
  name,
  open,
  shadowRoot,
  subTheme,
  section,
  editable,
  contentStyle,
  onClose,
}: ButtonEditorProps) => {
  const { t } = useTranslation();

  const [state, setState] = useState<keyof States>('default');

  const onCancel = () => {
    if ('default' !== state) {
      setState('default');
    }
    onClose();
  };

  if (!open) {
    return <></>;
  }

  return (
    <PopOut
      open={open}
      id={'button-editor'}
      className={'flex-col w-[min(800px,calc(100vw-40px))]'}
      shadowRoot={shadowRoot as ShadowRoot}
      role="dialog"
      displayMode="modal"
      theme={'cc-theme-main'}
      contentTheme={'cc-theme-dialog'}
    >
      <DialogHeader>
        <div className={'flex items-center '}>
          <span className={'opacity-50'}>
            {t(subThemeNameStrings[subTheme as keyof typeof subThemeNameStrings], { ns: 'AdminTheming' })}
          </span>
          <Icon icon={<ChevronRightIcon />} size={'small'} className={'mx-xs'} />
          {contentStyle && (
            <>
              <span className={'opacity-50'}>
                {t('str_LabelContentStyle', { ns: 'AdminTheming' }).replace('^0', formatTitle(contentStyle))}
              </span>
              <Icon icon={<ChevronRightIcon />} size={'small'} className={'mx-xs'} />
            </>
          )}
          {t('str_LabelButtonStyle', { ns: 'AdminTheming' }).replace('^0', formatTitle(name))}
        </div>
      </DialogHeader>
      <DialogContent className="flex">
        <ButtonEditorContent
          state={state}
          onChangeState={setState}
          shadowRoot={shadowRoot}
          contentStyle={contentStyle}
          name={name}
          editable={editable}
          subTheme={subTheme}
          section={section}
          props={props}
        />
      </DialogContent>
      <DialogFooter>
        <Button label={t('str_ButtonClose')} buttonStyle={'negative'} onClick={onCancel} />
      </DialogFooter>
    </PopOut>
  );
};

export default ButtonEditor;
