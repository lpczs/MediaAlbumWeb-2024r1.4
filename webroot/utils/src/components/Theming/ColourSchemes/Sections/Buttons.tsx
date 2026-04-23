import React from 'react';
import { useTranslation } from 'react-i18next';
import { Button, Heading, PictureIcon, Theme } from '@taopix/taopix-design-system';
import { buttonStyles } from '@taopix/taopix-design-system/dist/types/Components/Button/Button';
import SectionEntryWrapper from './SectionEntryWrapper';
import ButtonEditor from '../Editors/Button/ButtonEditor';
import SectionContainer from '../../Container/SectionContainer';
import { SectionProps } from '.';
import ThemedContainer from '../../Container/ThemedContainer';
import { ThemeButtons, Selector } from '../../../../types';
import useDialogState from '../../Hooks/useDialogState';
import classNames from 'classnames';

const Buttons = ({ props, shadowRoot, section, subTheme, editable, contentStyle }: SectionProps<ThemeButtons>) => {
  const { t } = useTranslation();
  const [dialogState, onEdit, onClose] = useDialogState<Selector>();

  return (
    <>
      <SectionContainer>
        <Heading level={2} className="mb-xl">
          {t('AdminTheming:str_LabelButtonStyles')}
        </Heading>
        {Object.keys(props).map((key: keyof ThemeButtons, index: number) => {
          return (
            <SectionEntryWrapper
              name={key}
              editable={editable}
              section={section}
              subTheme={subTheme}
              contentStyle={contentStyle}
              key={`${key}`}
              onEdit={editable ? () => onEdit(key, props[key]) : undefined}
            >
              <Theme
                name="tpx-theme-main"
                allowBackground={false}
                allowBorder={false}
                allowCorners={false}
                className={'h-full w-[210px]'}
              >
                <ThemedContainer
                  subTheme={subTheme}
                  contentStyle={contentStyle}
                  allowBackground={false}
                  allowBorder={false}
                  allowCorners={false}
                  className={'h-full w-full'}
                >
                  <Button
                    role="button"
                    label={t('AdminTheming:str_LabelExample')}
                    buttonStyle={key as buttonStyles}
                    startIcon={<PictureIcon />}
                    size="small"
                    className={classNames('w-full h-full', !editable && '!cursor-default')}
                    onClick={editable ? () => onEdit(key, props[key]) : undefined}
                  />
                </ThemedContainer>
              </Theme>
            </SectionEntryWrapper>
          );
        })}
      </SectionContainer>

      <ButtonEditor
        shadowRoot={shadowRoot}
        contentStyle={contentStyle}
        subTheme={subTheme}
        editable={editable}
        section={section}
        onClose={onClose}
        {...dialogState}
      />
    </>
  );
};

export default Buttons;
