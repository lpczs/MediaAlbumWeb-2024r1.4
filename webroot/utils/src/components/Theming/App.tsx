import React, { useRef, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { Button, Heading, HelpIcon, Panel, SpeechBubbleIcon } from '@taopix/taopix-design-system';
import { EditorType } from './Context/ThemeContext';
import ColourSchemesApp from './ColourSchemes/ColourSchemesApp';
import ThemingApp from './Theme/ThemingApp';

type ThemingAppProps = {
  documentRoot: Document | ShadowRoot;
};

const App = ({ documentRoot }: ThemingAppProps) => {
  const [editorType, setEditorType] = useState(EditorType.Theme);

  const {t} = useTranslation();

  const onChangeEditorType = (type: EditorType) => {
    setEditorType(type);
  };

  const ref = useRef<HTMLDivElement>();

  return (
    <div id="theming" className="flex flex-col p-lg flex-1 w-full" ref={ref}>
      <Heading className={'mb-lg'}>{t('str_TitleTheming', { ns: 'AdminTheming' })}</Heading>
      <div className="flex h-xxl w-full">
        <div className="flex self-end justify-start space-x-xs z-10">
          <Button
            aria-pressed={EditorType.Theme === editorType}
            onClick={() => onChangeEditorType(EditorType.Theme)}
            buttonStyle="tab"
            tabPosition="top"
            size={'small'}
            label={t('str_LabelThemes', { ns: 'AdminTheming' })}
            className={EditorType.Theme === editorType && 'mb-[-1px]'}
          />
          <Button
            aria-pressed={EditorType.ColourScheme === editorType}
            onClick={() => onChangeEditorType(EditorType.ColourScheme)}
            buttonStyle="tab"
            tabPosition="top"
            size={'small'}
            label={t('str_LabelColourSchemes', { ns: 'AdminTheming' })}
            className={EditorType.ColourScheme === editorType && 'mb-[-1px]'}
          />
        </div>
        <Button
          label={t('*:str_LabelHelp')}
          startIcon={<HelpIcon />}
          buttonStyle="standard"
          size={'small'}
          className={'ml-auto'}
          onClick={() => window.open('https://support.taopix.com/hc/en-gb/articles/17270978310941', '_blank')}
        />
        <Button
          label={t('*:str_LabelFeedback')}
          startIcon={<SpeechBubbleIcon />}
          buttonStyle="standard"
          size={'small'}
          onClick={() => window.open('mailto:feedback@taopix.com?subject=Feedback for UI Themes')}
        />
      </div>

      <Panel className={'flex-1 flex flex-col !rounded-tl-0 !rounded-b-themeCornerSize'}>
        {EditorType.Theme === editorType && <ThemingApp documentRoot={documentRoot} />}
        {EditorType.ColourScheme === editorType && <ColourSchemesApp documentRoot={documentRoot} />}
      </Panel>
    </div>
  );
};

export default App;
