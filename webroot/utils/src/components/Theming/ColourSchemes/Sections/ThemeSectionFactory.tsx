import React, { useMemo } from 'react';
import Buttons from './Buttons';
import Colours from './Colours';
import ContentStyles from './ContentStyles';
import { ThemeSection } from '../../../../types';

export type EditorSectionFactoryProps<K extends keyof ThemeSection> = {
  section: K;
  subTheme: string;
  props: ThemeSection[K];
  index: number;
  shadowRoot: ShadowRoot | Document;
  editable: boolean;
  contentStyle?: string;
};

const components: Record<keyof ThemeSection, any> = {
  contentStyles: ContentStyles,
  colours: Colours,
  buttons: Buttons,
};

const ThemeSectionFactory = <K extends keyof ThemeSection>(props: EditorSectionFactoryProps<K>) => {
  if (!components.hasOwnProperty(props.section)) {
    return <></>;
  }

  const Component = components[props.section];
  return <Component {...props} />;
};

export default ThemeSectionFactory;
