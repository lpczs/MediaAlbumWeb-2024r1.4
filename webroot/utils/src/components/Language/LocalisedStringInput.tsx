import React, { CSSProperties, MutableRefObject, createRef, useCallback, useEffect, useLayoutEffect, useRef, useState } from 'react';
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
  ThemeName,
  TrashIcon,
  getTheme,
} from '@taopix/taopix-design-system';
import { useTranslation } from 'react-i18next';
import { ListItemDataType } from '@taopix/taopix-design-system/dist/types/Components/SelectList/SelectList';
import classNames from 'classnames';
import { LocalisedStringDialog } from './LocalisedStringDialog';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}

export interface LocalisedStringInputProps {
  sessionRef?: number;
  componentMountPoint: Element;
  valueString?: string;
  header: string;
  onSaveString: Function;
  identifier: string;
  focusKey: string;
  editLabel: string;
  disabled: {disabled: boolean, disabledText: string};
  theKey: string;
  parentKey: string;
}

export const LocalisedStringInput = ({
  componentMountPoint,
  header,
  onSaveString,
  identifier,
  valueString = '',
  focusKey = '',
  editLabel,
  disabled,
  theKey,
  parentKey,
  ...props
}: LocalisedStringInputProps) => {
  const { t } = useTranslation();
  const [open, setOpen] = useState(false);

  // const listComponent = (
  //   <List size={'small'}>
  //     {valueArray.map((value: string) => {
  //       if (value === '') {
  //         return false;
  //       }

  //       const [langCode, string] = value.split(/ (.*)/, 2);
  //       const languageName = getLanguageNameFromCode(langSelectList, langCode);
  //       return (
  //         <ListItem key={langCode}>
  //           <div className="flex">
  //             <div className="flex-none w-5/6">
  //               <Button
  //                 key={'language_' + langCode}
  //                 labelAlignment={'left'}
  //                 onClick={() => { }}
  //                 buttonStyle="standard"
  //                 label={languageName + ' - ' + string}
  //               />
  //             </div>
  //           </div>
  //         </ListItem>
  //       );
  //     })}
  //   </List>
  // );

  const languageListToArray = (languageList: string) => {
    const languageArray = languageList.split(',');
    let languageKeyArray: ListItemDataType[] = [];

    for (let i = 0; i < languageArray.length; i++) {
      const [code, name] = languageArray[i].split(/ (.*)/, 2);
      languageKeyArray.push({ label: name, value: code });
    }

    return languageKeyArray;
  };

  const langSelectList: ListItemDataType[] = languageListToArray(t('str_LanguageList', { ns: '*' }));

  let refsArray: Record<string,any> = {};
  langSelectList.forEach(lang => {
    refsArray[`langRef_${lang.value}`] = React.createRef()
  });

  const toggle = () => {
    setOpen(!open);
  };

  return (
    <div>
      {/*listComponent*/}
      <Button
        disabled={disabled.disabled}
        label={editLabel}
        onClick={toggle}
      />
      <LocalisedStringDialog 
        componentMountPoint={componentMountPoint} 
        header={header} 
        onSaveString={onSaveString} 
        identifier={identifier} 
        focusKey={focusKey} 
        theKey={theKey} 
        parentKey={parentKey}
        valueString={valueString}
        openIn={open}
        refsArray={refsArray}
        langSelectList={langSelectList}
      />
    </div>
  );
};
