import React, { useCallback, useMemo } from 'react';
import { useTranslation } from 'react-i18next';
import { v4 as uuidv4 } from 'uuid';
import { colord } from 'colord';
import classNames from 'classnames';
import { cloneDeep, get, omit, omitBy, set } from 'lodash';
import { Button, Heading, PictureIcon, TextInput, Theme, ThemeName } from '@taopix/taopix-design-system';
import { buttonStyles } from '@taopix/taopix-design-system/dist/types/Components/Button/Button';
import { ButtonEditorProps } from './ButtonEditor';
import { ucfirst } from '../../../../../common';
import ThemedContainer from '../../../Container/ThemedContainer';
import { Selector, Sizes, States } from '../../../../../types';
import { ThemeActions } from '../../../Actions/ThemeActions';
import { useTheming } from '../../../Context/ThemeContext';
import { useColourEditor } from '../../../Context/ColourEditorContext';
import { EditorActions } from '../../../Actions/EditorActions';
import NumberEditor from '../Number/NumberEditor';

export type ButtonEditorContentProps = Omit<ButtonEditorProps, 'open' | 'onClose' | 'onSave'> & {
  state: keyof States;
  onChangeState: (state: keyof States) => void;
};

const ButtonEditorContent = ({
  props,
  state,
  shadowRoot,
  name,
  editable,
  subTheme,
  section,
  contentStyle,
  onChangeState,
}: ButtonEditorContentProps) => {
  const {
    state: { colourSchemes, selectedSchemeId },
    dispatch,
  } = useTheming();
  const { dispatch: editorDispatch } = useColourEditor();
  const { t } = useTranslation();

  const selectedScheme = useMemo(() => {
    if (!selectedSchemeId) {
      return false;
    }
    return colourSchemes[selectedSchemeId];
  }, [selectedSchemeId, colourSchemes]);

  const changes = useMemo(() => {
    if (!selectedScheme) {
      return null;
    }
    return selectedScheme.diff;
  }, [selectedScheme]);

  const generatePath = useCallback(
    (key: string) => {
      if (!contentStyle) {
        return `${subTheme}.buttons.${name}.${key}.${state}`;
      }
      return `${subTheme}.contentStyles.${contentStyle}.buttons.${name}.${key}.${state}`;
    },
    [state, subTheme, contentStyle]
  );

  const onReset = useCallback(
    (path: string) => {
      if (!selectedScheme) {
        return null;
      }

      // create the new diff by omitting the requested path
      let diff = omit(cloneDeep(selectedScheme.diff), [path]);

      // pop off the last element of the path to get the parent path
      let parentPath = path.split('.').slice(0, -1).join('.');
      while ('' !== parentPath) {
        // get the parentValue
        const parentValue = get(diff, parentPath, null);

        // if the parentValue is empty (we've removed all values from it), remove the parent
        if (parentValue && 'object' === typeof parentValue && Object.keys(parentValue).length < 1) {
          diff = omit(diff, parentPath);
        }

        if (!parentValue) {
          diff = omit(diff, parentPath);
        }

        parentPath = parentPath.split('.').slice(0, -1).join('.');
      }

      dispatch(
        ThemeActions.updateColourScheme({
          ...selectedScheme,
          diff,
          dirty: true,
        })
      );
    },
    [selectedScheme]
  );

  const onOpenColourEditor = (name: string, props: string, path: string) => {
    editorDispatch(
      EditorActions.openEditor({
        section: 'buttons',
        open: true,
        name,
        props,
        subTheme,
        contentStyle,
        path,
      })
    );
  };

  const rowClasses = classNames('flex', 'space-x-sm', 'items-center', 'm-sm');

  const nameColClasses = classNames('flex', 'items-center', 'w-[200px]', 'h-full');

  const contentColClasses = classNames('flex', 'items-center', 'space-x-sm');

  const buttonColClasses = classNames('flex', 'items-center', 'flex-1', 'h-full', 'space-x-sm');

  const parentThemeClasses = classNames(
    'flex',
    'overflow-hidden',
    'w-[210px]',
    'h-xl',
    'rounded-xs',
    'border border-gray-300',
    'overflow-hidden',
    editable && 'cursor-pointer',
    // Chequerboard background pattern:
    'bg-[conic-gradient(lightgray_90deg,white_90deg_180deg,lightgray_180deg_270deg,white_270deg)]',
    'bg-repeat',
    'bg-[length:20px_20px]',
    'bg-left-top'
  );

  const placeholderClasses = classNames(
    'flex',
    'h-xl',
    'w-[210px]',
    'items-center',
    'justify-center',
    'text-themeTextColour30',
    'text-sm',
    'text-italic',
    'border',
    'border-dashed',
    'border-gray-300',
    'rounded-xs',
    'hover:bg-gray-200',
    editable && 'cursor-pointer'
  );

  const templateStyle = ['hover', 'pressed'].includes(state)
    ? {
        background: `var(--tds-button-background-colour-${state})`,
      }
    : {};

  const onClick = (event: any, key: string, path: string) => {
    onOpenColourEditor(
      key,
      colord(getComputedStyle(event.target as HTMLElement)?.backgroundColor).toHex() || '#fff',
      path
    );
  };

  const stateLabels = {
    default: 'str_LabelDefaultState',
    hover: 'str_LabelHoverState',
    pressed: 'str_LabelPressedState',
    disabled: 'str_LabelDisabledState',
  };

  return (
    <>
      <Theme
        name="tpx-theme-main"
        className="flex flex-auto justify-center items-center space-x-sm mb-5"
        allowCorners={false}
        allowBackground={false}
        allowBorder={false}
      >
        <ThemedContainer subTheme={subTheme} contentStyle={contentStyle} className="relative p-lg flex justify-center" allowBorder={false} allowCorners={false}>
          <p className={'absolute top-sm left-sm filter invert mix-blend-difference text-black'}>
            {t('AdminTheming:str_LabelPreview')}
          </p>
          <Button
            labelAlignment={'centre'}
            buttonStyle={name as buttonStyles}
            label={t('str_ButtonExampleLabel', { ns: 'AdminTheming' })}
            startIcon={<PictureIcon />}
            corners="theme"
            size="large"
            disabled={'disabled' === state}
            className="pointer-events-none"
            style={templateStyle}
          />
        </ThemedContainer>
      </Theme>
      <Theme name={ThemeName.Section} className={'p-lg mb-lg'}>
        <div className={'flex items-start'}>
          <Heading level={2} className={'w-[220px] mb-lg'}>
            {t('AdminTheming:str_LabelColours')}
          </Heading>
          <div className="flex-1 flex space-x-xs">
            {['default', 'hover', 'pressed', 'disabled'].map((entry: keyof States) => {
              return (
                <Button
                  aria-pressed={state === entry}
                  onClick={() => onChangeState(entry)}
                  className={classNames('flex-none', {
                    'mb-[-1px]': entry === state,
                  })}
                  buttonStyle="standard"
                  size={'small'}
                  label={t(stateLabels[entry], { ns: 'AdminTheming' })}
                  key={entry}
                />
              );
            })}
          </div>
        </div>
        {Object.keys(props)
          .filter(key => 'sizing' !== key)
          .map((key: keyof Omit<Selector, 'sizing'>) => {
            const colour = props[key][state];
            // generate the object path
            const path = generatePath(key);

            // do we have a value for the path in the changes?
            const entryDiff = get(changes, path, null);

            return (
              <div className={rowClasses} key={uuidv4()}>
                <div className={nameColClasses}>{ucfirst(key)}</div>

                {entryDiff ? (
                  <>
                    <div className={contentColClasses}>
                      <div
                        className={parentThemeClasses}
                        onClick={(event: any) => (editable ? onClick(event, key, path) : undefined)}
                      >
                        <div style={{ backgroundColor: colour }} className={'h-full w-full'} />
                      </div>
                    </div>
                    <div className={buttonColClasses}>
                      <Button
                        buttonStyle={'distinct'}
                        label={t('str_ButtonReset', { ns: '*' })}
                        onClick={() => onReset(path)}
                        corners="theme"
                        size="small"
                      />
                    </div>
                  </>
                ) : (
                  <div className={contentColClasses}>
                    <div className={placeholderClasses} onClick={(event: any) => onClick(event, key, path)}>
                      Not set
                    </div>
                  </div>
                )}
              </div>
            );
          })}
      </Theme>

      <Theme name={ThemeName.Section} className={'p-lg'}>
        <Heading level={2} className={'mb-lg'}>
          {t('AdminTheming:str_LabelBorderSizes')}
        </Heading>

        {Object.keys(props)
          .filter(key => 'sizing' === key)
          .map((key: keyof Pick<Selector, 'sizing'>) => {
            return Object.keys(props[key]).map((entry: keyof Sizes) => {
              // generate the object path (sizing doesn't have a state so we remove it)
              const path = generatePath(key).split('.').slice(0, -1).concat(entry).join('.');

              // do we have a value for the path in the changes?
              const entryDiff = get(changes, path, null);
              
              return (
                <div className={rowClasses} key={entry}>
                  <div className={nameColClasses}>{ucfirst(entry)}</div>
                  <div className={contentColClasses}>
                    <NumberEditor path={path} props={null === entryDiff ? 0: entryDiff} name={entry} editable={editable} className={'h-xl'} />
                  </div>
                  <div className={buttonColClasses}>
                    {entryDiff && editable ? (
                      <Button
                        buttonStyle={'distinct'}
                        label={t('str_ButtonReset', { ns: '*' })}
                        onClick={() => onReset(path)}
                        corners="theme"
                        size="small"
                      />
                    ) : <></>}
                  </div>
                </div>
              );
            });
          })}
      </Theme>
    </>
  );
};

export default ButtonEditorContent;
