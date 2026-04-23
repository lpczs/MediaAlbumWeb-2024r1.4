import React, { useCallback, useMemo, useState } from 'react';
import classNames from 'classnames';
import axios from 'axios';
import { useErrorBoundary } from 'react-error-boundary';
import { useTranslation } from 'react-i18next';
import {
  ArrowDownIcon,
  Button,
  CopyIcon,
  DialogContent,
  DialogFooter,
  Icon,
  LoadingLosenge,
  LockIcon,
  PopOut,
  ThemeName,
  TrashIcon,
  getTheme,
} from '@taopix/taopix-design-system';
import { ColourScheme, ThemesResponse, useTheming } from '../Context/ThemeContext';
import { ThemeActions } from '../Actions/ThemeActions';
import { OwnerType } from '../../../Enums';
import { IssueActions } from '../Actions/IssuesActions';
import { useIssuesDialog } from '../Context/IssuesContext';
import { omit } from 'lodash';

export type ColourSchemeListProps = {
  shadowRoot: ShadowRoot | Document;
  onDeleteScheme: (scheme: ColourScheme) => void;
  onSaveScheme: (scheme: ColourScheme) => Promise<ColourScheme | void>;
  onExportScheme: (scheme: ColourScheme) => void;
};

const ColourSchemeList = ({ shadowRoot, onDeleteScheme, onSaveScheme, onExportScheme }: ColourSchemeListProps) => {
  const {
    state: { colourSchemes, selectedSchemeId, loading },
    dispatch,
  } = useTheming();
  const { dispatch: dispatchIssue } = useIssuesDialog();

  const { t } = useTranslation(['*', 'AdminTheming']);
  const { showBoundary } = useErrorBoundary();

  const [showSaveAlert, setShowSaveAlert] = useState<number>(null);

  const selectedScheme = useMemo(() => {
    if (!selectedSchemeId) {
      return null;
    }
    return colourSchemes[selectedSchemeId];
  }, [colourSchemes, selectedSchemeId]);

  // the default theme name
  const onCopyScheme = (scheme: ColourScheme): void => {
    const tempId = -(Object.values(colourSchemes).length + 1);

    const newScheme = {
      ...scheme,
      id: tempId,
      name: t('AdminTheming:str_LabelThemeNameCopy').replace('^0', scheme.name),
      type: OwnerType.User,
      dirty: true,
    };

    if (
      Object.values(colourSchemes)
        .filter(t => t.id !== newScheme.id)
        .map(t => t.name.trim())
        .includes(newScheme.name.trim())
    ) {
      dispatchIssue(
        IssueActions.toggleIssue({
          issue: t('AdminTheming:str_LabelDuplicateName').replace('^0', newScheme.name.trim()),
          open: true,
        })
      );
      return void 0;
    }

    onSaveScheme(newScheme).catch(error => {
      showBoundary(t('AdminTheming:str_TitleErrorCopyTheme'));
    });
  };

  /**
   * Change the selected theme
   *
   * @param theme
   * @returns void
   */
  const onSelectScheme = (scheme: ColourScheme): void => {
    // if we have changes on the current theme, ask the user if they want to save before leaving
    if (selectedScheme && selectedScheme.dirty) {
      return setShowSaveAlert(scheme.id);
    }
    dispatch(ThemeActions.setSelectedColourScheme(scheme.id));
  };

  /**
   * Close the changes warning dialog
   *
   * @returns void
   */
  const onCancelThemeChange = (): void => {
    setShowSaveAlert(null);
  };

  /**
   * Discard the current changes and switch theme
   *
   * @returns void
   */
  const onDiscardChanges = useCallback((): void => {
    if (0 > selectedScheme.id) {
      // switch to the requested theme
      setShowSaveAlert(current => {
        dispatch(ThemeActions.setSelectedColourScheme(current));
        return null;
      });
      return void 0;
    }

    dispatch(ThemeActions.setIsLoading(true));
    // reload the theme from the server
    axios
      .get<ThemesResponse>('/api/theme/list')
      .then(({ data }) => {
        const target = data.payload.colourSchemeList.find(t => t.id === selectedSchemeId);
        if (target) {
          dispatch(ThemeActions.updateColourScheme({ ...target, dirty: false }));
        }
        dispatch(ThemeActions.setIsLoading(false));

        // switch to the requested theme
        setShowSaveAlert(current => {
          dispatch(ThemeActions.setSelectedColourScheme(current));
          return null;
        });
      })
      .catch(error => showBoundary({ message: error }));
  }, [selectedScheme, selectedSchemeId]);

  const onSave = useCallback((): void => {
    if (!selectedScheme) {
      return void 0;
    }

    if (
      Object.values(colourSchemes)
        .filter(t => t.id !== selectedScheme.id)
        .map(t => t.name.trim())
        .includes(selectedScheme.name.trim())
    ) {
      dispatchIssue(
        IssueActions.toggleIssue({
          issue: t('AdminTheming:str_LabelDuplicateName').replace('^0', selectedScheme.name.trim()),
          open: true,
        })
      );
      return void 0;
    }

    onSaveScheme(selectedScheme).then(() => {
      setShowSaveAlert(current => {
        dispatch(ThemeActions.setSelectedColourScheme(current));
        return null;
      });
    });
  }, [selectedScheme]);

  if (loading) {
    return (
      <div className={'m-xs'}>
        <LoadingLosenge label={t('str_MessageLoading')} />
      </div>
    );
  }

  return (
    <>
      <ul className="flex flex-col list-none m-xs">
        {Object.values(colourSchemes).map(scheme => {
          const rowClasses = classNames(
            selectedSchemeId === scheme.id ? getTheme(ThemeName.Prominent, true, false, false) : 'hover:bg-black/5',
            'flex',
            'h-xxl',
            'items-center',
            'px-sm',
            'cursor-pointer',
            'group/experience-row',
            'rounded-themeCornerSize'
          );
          return (
            <li key={scheme.id} className={rowClasses}>
              <span
                className={'w-[250px] flex-1 pl-xs line-clamp-1 overflow-ellipsis'}
                onClick={() => onSelectScheme(scheme)}
              >
                {scheme.name}
              </span>
              <div className={'flex items-center '}>
                <Button
                  label={t('str_ButtonCopy')}
                  hideLabel
                  size={'small'}
                  startIcon={<CopyIcon />}
                  onClick={() => onCopyScheme(scheme)}
                  buttonStyle="standard"
                  className={'mouse:!hidden group-hover/experience-row:!flex'}
                />
                <Button
                  label={t('str_ButtonCopy')}
                  hideLabel
                  size={'small'}
                  startIcon={<ArrowDownIcon />}
                  onClick={() => onExportScheme(scheme)}
                  buttonStyle="standard"
                  className={'mouse:!hidden group-hover/experience-row:!flex'}
                />
                {OwnerType.System === scheme.type ? (
                  <Icon icon={<LockIcon />} size={'small'} className={'mx-xs'} />
                ) : (
                  <Button
                    label={t('str_ButtonDelete')}
                    hideLabel
                    size={'small'}
                    startIcon={<TrashIcon />}
                    onClick={() => onDeleteScheme(scheme)}
                    buttonStyle="standard"
                    className={'mouse:!hidden group-hover/experience-row:!flex'}
                  />
                )}
              </div>
            </li>
          );
        })}
      </ul>

      <PopOut
        open={null !== showSaveAlert}
        id={'theming-save-warning'}
        className={'flex-col'}
        shadowRoot={shadowRoot as ShadowRoot}
        role="dialog"
        displayMode={'modal'}
      >
        <DialogContent>
          <p>{t('str_LabelConfirmChange', { ns: 'AdminTheming' })}</p>
        </DialogContent>
        <DialogFooter>
          <Button label={t('str_ButtonCancel')} buttonStyle={'negative'} onClick={onCancelThemeChange} />
          <Button label={t('str_ButtonDontSave')} buttonStyle={'secondary'} onClick={onDiscardChanges} />
          <Button label={t('str_ButtonSave')} onClick={onSave} />
        </DialogFooter>
      </PopOut>
    </>
  );
};

export default ColourSchemeList;
