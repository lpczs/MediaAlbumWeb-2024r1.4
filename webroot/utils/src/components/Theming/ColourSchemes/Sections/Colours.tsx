import React, { useCallback } from 'react';
import classNames from 'classnames';
import { colord } from 'colord';
import { Heading, Theme } from '@taopix/taopix-design-system';
import SectionEntryWrapper from './SectionEntryWrapper';
import SectionContainer from '../../Container/SectionContainer';
import { SectionProps } from '.';
import ThemedContainer from '../../Container/ThemedContainer';
import { ThemeColours } from '../../../../types';
import { useColourEditor } from '../../Context/ColourEditorContext';
import { EditorActions } from '../../Actions/EditorActions';
import NumberEditor from '../Editors/Number/NumberEditor';
import { useTranslation } from 'react-i18next';

const Colours = ({ shadowRoot, props, section, subTheme, editable, contentStyle }: SectionProps<ThemeColours>) => {
  const { t } = useTranslation();
  const { dispatch: editorDispatch } = useColourEditor();

  const generatePath = useCallback(
    (key: string) => {
      if (!contentStyle) {
        return `${subTheme}.colours.${key}`;
      }
      return `${subTheme}.contentStyles.${contentStyle}.colours.${key}`;
    },
    [subTheme, contentStyle]
  );

  const onEdit = useCallback((name: string, props: string, path: string) => {
    editorDispatch(
      EditorActions.openEditor({
        section,
        open: true,
        name,
        props,
        subTheme,
        contentStyle,
        path,
      })
    );
  }, []);

  let swatchClasses = classNames([
    'w-full',
    'h-full',
    'flex',
    'justify-center',
    'items-center',
    'h-xl',
    'rounded-xs',
    'border border-gray-300',
  ]);

  const parentThemeClasses = classNames(
    'flex',
    'w-full',
    'h-full',
    editable && 'cursor-pointer',
    // Chequerboard background pattern:
    'bg-[conic-gradient(lightgray_90deg,white_90deg_180deg,lightgray_180deg_270deg,white_270deg)]',
    'bg-repeat',
    'bg-[length:20px_20px]',
    'bg-left-top'
  );

  const textFieldClasses = classNames('h-xl', 'w-[100px]', 'flex', 'items-center');

  const SectionContent = ({ value, name }: { value: number | string; name: keyof ThemeColours }) => {
    const path = generatePath(name);

    if (!['border-width', 'rounded-corners'].includes(name)) {
      return (
        <SectionEntryWrapper
          name={name}
          editable={editable}
          section={section}
          subTheme={subTheme}
          contentStyle={contentStyle}
          onEdit={
            editable
              ? (event: any) => {
                  onEdit(name, colord(getComputedStyle(event.target as HTMLElement)?.backgroundColor).toHex(), path);
                }
              : undefined
          }
        >
          <Theme
            name="tpx-theme-main"
            className={parentThemeClasses}
            allowBackground={false}
            allowBorder={false}
            allowCorners={false}
            onClick={
              editable
                ? (event: any) => {
                    onEdit(name, colord(getComputedStyle(event.target as HTMLElement)?.backgroundColor).toHex(), path);
                  }
                : undefined
            }
          >
            <ThemedContainer
              subTheme={subTheme}
              contentStyle={contentStyle}
              className={swatchClasses}
              style={{
                background: `var(--tds-theme-${name}-colour)`,
              }}
              allowBorder={false}
              allowBackground={false}
              allowCorners={false}
            />
          </Theme>
        </SectionEntryWrapper>
      );
    }

    const defaultValue = ['', 'unset'].includes(String(value)) ? '1': String(value);
    console.log('SUP: ', value, defaultValue, path)
    // else we assume a number
    return (
      <SectionEntryWrapper
        name={name}
        editable={editable}
        section={section}
        subTheme={subTheme}
        contentStyle={contentStyle}
      >
        <NumberEditor name={name} path={path} props={defaultValue} className={textFieldClasses} editable={editable} />
      </SectionEntryWrapper>
    );
  };

  return (
    <>
      <SectionContainer>
        <Heading level={2} className="mb-xl">
          {t('AdminTheming:str_LabelGeneralStyles')}
        </Heading>
        {Object.keys(props).map((key: keyof ThemeColours) => {
          return <SectionContent key={`${key}-${props[key]}`} value={props[key]} name={key} />;
        })}
      </SectionContainer>
    </>
  );
};

export default Colours;
