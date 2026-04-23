import React from 'react';
import { useTranslation } from 'react-i18next';
import { useErrorBoundary } from 'react-error-boundary';
import axios from 'axios';
import useSelectedTheme from '../Hooks/useSelectedTheme';
import { ColourScheme, ThemeType, useTheming } from '../Context/ThemeContext';
import { InfoIcon, Label, ThemeName } from '@taopix/taopix-design-system';
import ColourSchemeSelectList from '../ColourSchemes/SelectList/ColourSchemeSelectList';
import { ThemeActions } from '../Actions/ThemeActions';
import ThemEditorHeader from './ThemeEditorHeader';
import { InfoButton } from '../../Experience/Message/InfoButton';

export type ThemeEditorProps = {
  documentRoot: Document | ShadowRoot;
};

const ThemeEditor = ({ documentRoot }: ThemeEditorProps) => {
  const selectedTheme = useSelectedTheme();
  const { dispatch } = useTheming();
  const { showBoundary } = useErrorBoundary();

  const { t } = useTranslation();

  const onChange = (scheme: ColourScheme, key: keyof Pick<ThemeType, 'defaultSchemeId' | 'darkSchemeId'>) => {
    dispatch(
      ThemeActions.updateTheme({
        ...selectedTheme,
        [key]: scheme.id,
        dirty: true,
      })
    );
  };

  const onSaveTheme = (theme: ThemeType): Promise<ThemeType | void> => {
    if (!theme.dirty) {
      return Promise.reject('Nothing to save');
    }

    const { id, name, defaultSchemeId, darkSchemeId } = theme;

    return axios
      .post<ThemeType>('/api/theme/save-theme', {
        id,
        name,
        defaultSchemeId,
        darkSchemeId,
      })
      .then(({ data }) => {
        // if this is a new theme, remove the template
        if (data.id !== theme.id) {
          dispatch(ThemeActions.deleteTheme(theme));
        }
        // select the new theme
        dispatch(ThemeActions.setSelectedTheme(data.id));
        // set the changes
        dispatch(ThemeActions.updateTheme({ ...data, dirty: false }));

        return data;
      })
      .catch(error => {
        showBoundary({
          message: t('AdminTheming:str_TitleErrorSavingTheme'),
        });
      });
  };

  const stringsMap = {
    defaultSchemeId: 'str_LabelStandardScheme',
    darkSchemeId: 'str_LabelDarkModeScheme',
  };

  if (!selectedTheme) {
    return <p className={'italic'}>{t('str_MessageNoThemeSelected', { ns: 'AdminTheming' })}</p>;
  }

  return (
    <>
      <ThemEditorHeader onSaveTheme={onSaveTheme} />
      <div className={'flex flex-col min-h-0'}>
        {Object.keys(stringsMap).map((key: keyof Pick<ThemeType, 'defaultSchemeId' | 'darkSchemeId'>) => {
          return (
            <div className="mb-lg" key={key}>
              <div className={'flex'}>
                <Label
                  htmlFor={`scheme-selector-${key}`}
                  label={t(stringsMap[key], { ns: 'AdminTheming' })}
                  className={'mr-sm'}
                />
                {key === 'darkSchemeId' && (
                  <InfoButton
                    warningText={t('AdminTheming:str_HelpTextDarkSchemeDisplaysWhenDarkModeActivated')}
                    themeName={ThemeName.Container}
                    icon={<InfoIcon />}
                    componentMountPoint={documentRoot}
                  />
                )}
              </div>
              <ColourSchemeSelectList
                documentRoot={documentRoot}
                selectedId={selectedTheme[key]}
                id={`scheme-selector-${key}`}
                onChange={(scheme: ColourScheme) => onChange(scheme, key)}
              />
            </div>
          );
        })}
      </div>
    </>
  );
};

export default ThemeEditor;
