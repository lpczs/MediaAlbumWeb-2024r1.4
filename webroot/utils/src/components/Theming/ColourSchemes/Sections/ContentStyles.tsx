import React, { useCallback } from 'react';
import SectionEntryWrapper from './SectionEntryWrapper';
import { Heading, Theme } from '@taopix/taopix-design-system';
import { useTranslation } from 'react-i18next';
import SectionContainer from '../../Container/SectionContainer';
import classNames from 'classnames';
import ContentStyleEditor from '../Editors/ContentStyle/ContentStyleEditor';
import { SectionProps } from '.';
import ThemedContainer from '../../Container/ThemedContainer';
import { ThemeSection, ThemeContentStyles } from '../../../../types';
import useDialogState from '../../Hooks/useDialogState';

const ContentStyles = ({
  props,
  shadowRoot,
  section,
  subTheme,
  editable,
}: SectionProps<Record<string, ThemeSection>>) => {
  const { t } = useTranslation();
  const [dialogState, onEdit, onCloseEditor] = useDialogState<ThemeSection>();

  const swatchClasses = classNames(['w-full', 'h-full', 'flex', 'justify-center', 'items-center', 'h-xl']);

  const parentThemeClasses = classNames('w-full', 'h-full', editable && 'cursor-pointer');

  return (
    <>
      <SectionContainer>
        <Heading level={2} className="mb-xl">
          {t('str_LabelContentStyles', { ns: 'AdminTheming' })}
        </Heading>
        {Object.keys(props).map((key: keyof ThemeContentStyles) => {
          return (
            <SectionEntryWrapper
              name={key}
              editable={editable}
              section={section}
              subTheme={subTheme}
              contentStyle={key}
              key={`${key}`}
              onEdit={editable ? () => onEdit(key, props[key]) : undefined}
            >
              <Theme
                name="tpx-theme-main"
                className={parentThemeClasses}
                allowBackground={false}
                allowBorder={false}
                allowCorners={false}
                onClick={editable ? () => onEdit(key, props[key]) : undefined}
              >
                <ThemedContainer
                  subTheme={subTheme}
                  contentStyle={key}
                  allowBorder={false}
                  allowCorners={false}
                  className={swatchClasses}
                >
                  <span>Example</span>
                </ThemedContainer>
              </Theme>
            </SectionEntryWrapper>
          );
        })}
      </SectionContainer>

      <ContentStyleEditor
        shadowRoot={shadowRoot}
        subTheme={subTheme}
        editable={editable}
        onClose={() => onCloseEditor()}
        onSave={() => onCloseEditor()}
        {...dialogState}
      />
    </>
  );
};

export default ContentStyles;
