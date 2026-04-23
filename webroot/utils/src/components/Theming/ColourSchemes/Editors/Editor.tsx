import React, { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { Button, Heading } from '@taopix/taopix-design-system';
import { ucfirst } from '../../../../common';
import ThemeSectionFactory from '../Sections/ThemeSectionFactory';
import { ThemeProps, SubTheme, ThemeSection } from '../../../../types';
import ColourEditor from './Colour/ColourEditor';

export type EditorProps = {
  props: ThemeProps;
  shadowRoot: ShadowRoot | Document;
  editable: boolean;
};

export const subThemeNameStrings = {
  main: 'str_LabelBaseStyles',
  header: 'str_LabelHeader',
  workarea: 'str_LabelWorkArea',
  toolpaletteClosed: 'str_LabelToolpaletteClosed',
  toolpaletteOpen: 'str_LabelToolpaletteOpen',
  toolpaletteContent: 'str_LabelToolpaletteContent',
  dialogs: 'str_LabelDialogs',
};

const Editor = ({ props, shadowRoot, editable }: EditorProps) => {
  const { t } = useTranslation();

  const [subTheme, setSubTheme] = useState<SubTheme>('main');

  const nameFormatter = (str: string) => {
    return t(`str_Label${ucfirst(str)}`, { ns: 'AdminTheming' });
  };

  const descriptionStrings = {
    main: 'str_MessageDescriptionMain',
    header: 'str_MessageDescriptionHeader',
    workarea: 'str_MessageDescriptionWorkarea',
    toolpaletteClosed: 'str_MessageDescriptionToolpaletteClosed',
    toolpaletteOpen: 'str_MessageDescriptionToolpaletteOpen',
    toolpaletteContent: 'str_MessageDescriptionToolpaletteContent',
    dialogs: 'str_MessageDescriptionDialogs',
  };

  return (
    <div className={'flex min-h-0'}>
      <div className="flex flex-col min-w-[160px]">
        <Button
          aria-pressed={subTheme === 'main'}
          onClick={() => setSubTheme('main')}
          buttonStyle="special"
          label={'Base styles'}
          key={'main'}
          labelAlignment="left"
          corners="square"
        />

        <div className='flex space-y-xs flex-col pl-sm mt-xl'>
          <h2 className={'font-bold mb-sm'}>{t('AdminTheming:str_TitleSubThemes')}:</h2>
          
          {Object.keys(props).filter(subKey => subKey !== 'main').map((subKey: keyof ThemeProps) => {
            return (
              <Button
                aria-pressed={subTheme === subKey}
                onClick={() => setSubTheme(subKey)}
                buttonStyle="special"
                label={nameFormatter(subKey)}
                key={subKey}
                labelAlignment="left"
                corners="square"
              />
            );
          })}
        </div>
      </div>
      <div className="ml-xxl flex-1 flex flex-col overflow-y-auto pb-lg">
        <Heading className={'mb-sm'}>{subTheme === 'main' ? 'Base styles' : nameFormatter(subTheme)}</Heading>
        {editable && <p className="mb-xl">{t(descriptionStrings[subTheme], { ns: 'AdminTheming' })}</p>}
        {Object.keys(props[subTheme]).map((section: keyof ThemeSection, index: number) => {
          return (
            <ThemeSectionFactory
              props={props[subTheme][section]}
              subTheme={subTheme}
              index={index}
              section={section}
              shadowRoot={shadowRoot}
              key={index}
              editable={editable}
            />
          );
        })}
      </div>
      <ColourEditor />
    </div>
  );
};

export default Editor;
