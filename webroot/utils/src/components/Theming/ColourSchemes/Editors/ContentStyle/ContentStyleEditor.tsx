import React, { useCallback } from 'react';
import { PopOut, DialogHeader, DialogContent, DialogFooter, Button } from '@taopix/taopix-design-system';
import { formatTitle } from '../../../../../common';
import { useTranslation } from 'react-i18next';
import ThemeSectionFactory from '../../Sections/ThemeSectionFactory';
import { ThemeSection } from '../../../../../types';

export type ContentStyleEditorProps = {
  name: string;
  props: ThemeSection;
  open: boolean;
  shadowRoot: Document | ShadowRoot;
  subTheme: string;
  editable: boolean;
  onClose: () => void;
  onSave: (payload: any) => void;
};

const ContentStyleEditor = ({
  props,
  name,
  open,
  shadowRoot,
  subTheme,
  editable,
  onClose,
}: ContentStyleEditorProps) => {
  const { t } = useTranslation();

  const onCancel = () => {
    onClose();
  };

  if (!open) {
    return <></>;
  }

  return (
    <PopOut
      open={open}
      id={'content-style-editor'}
      className={'flex-col  h-[min(800px,calc(100vh-40px))] w-[min(800px,calc(100vw-40px))]'}
      shadowRoot={shadowRoot as ShadowRoot}
      role="dialog"
      displayMode={'modal'}
    >
      <DialogHeader>{t('str_LabelContentStyle', { ns: 'AdminTheming' }).replace('^0', formatTitle(name))}</DialogHeader>
      <DialogContent>
        {Object.keys(props).map((section: keyof ThemeSection, index: number) => {
          return (
            <ThemeSectionFactory
              props={props[section]}
              subTheme={subTheme}
              index={index}
              section={section}
              shadowRoot={shadowRoot}
              key={index}
              contentStyle={name}
              editable={editable}
            />
          );
        })}
      </DialogContent>
      <DialogFooter>
        <Button label={t('str_ButtonCancel')} buttonStyle={'negative'} onClick={onCancel} />
        <Button label={t('str_ButtonSave')} onClick={onCancel} />
      </DialogFooter>
    </PopOut>
  );
};

export default ContentStyleEditor;
