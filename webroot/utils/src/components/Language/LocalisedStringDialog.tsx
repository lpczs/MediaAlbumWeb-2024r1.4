import React, {
  CSSProperties,
  MutableRefObject,
  createRef,
  useCallback,
  useEffect,
  useLayoutEffect,
  useRef,
  useState,
} from 'react';
import {
  Button,
  DialogContent,
  DialogFooter,
  DialogHeader,
  Heading,
  List,
  ListItem,
  PopOut,
  SelectList,
  TextInput,
  Theme,
  ThemeName,
  TrashIcon,
  getTheme,
} from '@taopix/taopix-design-system';
import { useTranslation } from 'react-i18next';
import { ListItemDataType } from '@taopix/taopix-design-system/dist/types/Components/SelectList/SelectList';
import classNames from 'classnames';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}

export interface LocalisedStringDialogProps {
  sessionRef?: number;
  componentMountPoint: Element;
  valueString?: string;
  header: string;
  onSaveString: Function;
  identifier: string;
  focusKey: string;
  theKey: string;
  parentKey: string;
  openIn: boolean;
  refsArray: Record<string, any>;
  langSelectList: ListItemDataType[];
}

export const LocalisedStringDialog = ({
  componentMountPoint,
  header,
  onSaveString,
  identifier,
  valueString = '',
  focusKey = '',
  theKey,
  parentKey,
  openIn,
  refsArray,
  langSelectList,
  ...props
}: LocalisedStringDialogProps) => {
  const { t } = useTranslation();
  const [open, setOpen] = useState(openIn);
  const [isDirty, setIsDirty] = useState(false);
  const [valueArray, setValueArray] = useState((valueString === '') ? [] : valueString.split('<p>'));
  const [currentLang, setCurrentLang] = useState(window.gLangCode);

  const getStringFromLanguage = (lang: string) => {
    for (let index = 0; index < valueArray.length; index++) {
      const miniVal = valueArray[index].split(/ (.*)/, 2);
      if (miniVal[0] === lang) {
        return miniVal[1];
      }
    }
    return '';
  };

  const popoutID = 'localisedStringPopOutID';
  const headerID = 'localisedStringPopOutHeaderID';
  const contentID = 'localisedStringPopOutContentID';
  const ariaLabelledByAttr = { 'aria-labelledby': `${headerID}` };
  const ariaDescribedByAttr = { 'aria-describedby': `${contentID}` };
  const ariaAttributes = { ...ariaLabelledByAttr, ...ariaDescribedByAttr };

  const saveTextInput = (e: any, langCode: string, remove: boolean = false) => {
    if (e.target.value === '') {
      remove = true;
    }

    let valueArrayClone = [...valueArray];

    for (let index = 0; index < valueArrayClone.length; index++) {
      const miniVal = valueArrayClone[index].split(/ (.*)/, 2);

      if (miniVal[0] === langCode) {
        valueArrayClone.splice(index, 1);
        break;
      }
    }

    if (!remove) {
      valueArrayClone.push(langCode + ' ' + e.target.value);
    }

    setValueArray(valueArrayClone);
    setIsDirty(true);
  };

  const save = (e: any) => {
    e.preventDefault();
    setIsDirty(false);
    onSaveString(identifier, valueArray.join('<p>'), toggle);
  };

  const cancel = (e: any) => {
    e.preventDefault();
    setIsDirty(false);
    onSaveString(identifier, valueString, toggle);
  };

  const tableClasses = classNames('flex', 'flex-col', 'h-full', 'border', 'border-themeTextColour20');
  const tableHeaderClasses = classNames('flex');
  const tableRowClasses = classNames('flex', 'border-b', 'border-b-themeTextColour20');
  const tableBodyClasses = classNames('flex-1', 'flex', 'flex-col', 'overflow-auto');

  const nameHeadingCellClasses = classNames('p-sm', 'w-[200px]');
  const valueHeadingCellClasses = classNames('p-sm');

  const cellClasses = classNames('flex', 'p-sm', 'items-center');
  const nameCellClasses = classNames(cellClasses, 'w-[200px]');
  const valueCellClasses = classNames(cellClasses);

  const toggle = () => {
    setOpen(!open);
  };

  useEffect(() => {
    if (!open && openIn) {
      setTimeout(() => {
        if (refsArray[`langRef_${currentLang}`].current) {
          refsArray[`langRef_${currentLang}`].current.focus();
        }
      }, 200);
    }
    setOpen(openIn);
  }, [openIn]);

  return (
    <PopOut
      id={popoutID}
      onClickOutside={() => {}}
      open={open}
      className={'flex-col max-h-full max-w-full w-[600px] h-[600px]'}
      role="dialog"
      afterClose={() => {}}
      shadowRoot={componentMountPoint}
      displayMode={'modal'}
      {...ariaAttributes}
    >
      <DialogHeader id={headerID}>{header}</DialogHeader>
      <DialogContent id={contentID}>
        <div className={tableClasses}>
          <Theme className={tableHeaderClasses} name={ThemeName.Container} allowCorners={false}>
            <div className={nameHeadingCellClasses}>{t('str_LabelLanguageName', { ns: '*' })}</div>
            <div className={valueHeadingCellClasses}>{t('str_LabelText', { ns: 'AdminExperience' })}</div>
          </Theme>
          <div className={tableBodyClasses}>
            {langSelectList.map((lang, i) => {
              const thisValue = getStringFromLanguage(lang.value);
              const thisKey = parentKey + '_' + theKey + '_langInput_' + lang.value;

              return (
                <div key={'outer' + thisKey} className={tableRowClasses}>
                  <div className={nameCellClasses}>{lang.label}</div>
                  <div className={valueCellClasses}>
                    <TextInput
                      ref={refsArray[`langRef_${lang.value}`]}
                      key={thisKey}
                      id={thisKey}
                      name={thisKey}
                      defaultValue={thisValue}
                      onChange={e => {
                        saveTextInput(e, lang.value);
                        setCurrentLang(lang.value);
                      }}
                      onChangeDelay={100}
                      autoFocus={currentLang === lang.value}
                      className={'w-[260px]'}
                    />
                    {thisValue !== '' && (
                      <Button
                        key={'trash_' + lang.value}
                        label={t('*:str_LabelClear')}
                        hideLabel
                        onClick={e => {
                          saveTextInput({ target: { value: '' } }, lang.value, true);
                          refsArray[`langRef_${lang.value}`].current.value = '';
                          refsArray[`langRef_${lang.value}`].current.focus();
                        }}
                        buttonStyle="standard"
                        startIcon={<TrashIcon />}
                        className={'ml-sm'}
                      />
                    )}
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      </DialogContent>
      <DialogFooter>
        <Button role="button" buttonStyle="negative" label={t('str_ButtonCancel', { ns: '*' })} onClick={cancel} />
        <Button
          role="button"
          buttonStyle="primary"
          label={t('str_ButtonSave', { ns: '*' })}
          onClick={save}
          disabled={!isDirty}
        />
      </DialogFooter>
    </PopOut>
  );
};
