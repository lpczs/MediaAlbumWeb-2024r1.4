import React, { useState, useEffect, useRef } from 'react';
import axios, { AxiosResponse } from 'axios';
import {
  Button,
  Label,
  LoadingLosenge,
  SaveIcon,
  SelectList,
  TextInput,
  Theme,
  ThemeName,
} from '@taopix/taopix-design-system';
import { useTranslation } from 'react-i18next';
import { set } from 'lodash';
import {
  ConditionActions,
  ConditionControl,
  Experience,
  ExperienceError,
  ExperienceSaveServerResponse,
  Features,
} from '../../../types';
import { ExperienceSystemType, ExperienceType, ProductType, SettingsDataType } from '../../../Enums';
import { ExperienceForm } from '../Form/ExperienceForm';
import { useErrorBoundary } from 'react-error-boundary';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}
export interface DisplayEditProps {
  sessionRef: number;
  experience?: Experience;
  onUpdateExperience?: Function;
  onDeleteExperienceData?: Function;
  schema?: Object;
  setIsDirty: Function;
  componentMountPoint: Element;
  baseExperience: Array<Record<string, Object>>;
  features: Features;
  isDirty: boolean;
  dialog: { showFunc: Function; toggleFunc: Function };
  isNameUnique: Function;
  busy: { busy: boolean; msg: string };
  onSetBusy: Function;
  onSaveExperienceData: Function;
  onSetCurrentKey: Function;
  currentKey: string;
  onSetErrorArray: Function;
  errorArray: { [dataPath: string]: ExperienceError }
}

export const DisplayEdit = ({
  isNameUnique,
  onSetBusy,
  busy,
  features,
  isDirty,
  dialog,
  baseExperience,
  experience,
  setIsDirty,
  componentMountPoint,
  onDeleteExperienceData = () => {},
  onUpdateExperience = () => {},
  schema = null,
  onSaveExperienceData,
  onSetCurrentKey,
  currentKey,
  onSetErrorArray,
  errorArray,
  ...props
}: DisplayEditProps) => {
  const { t } = useTranslation();
  const { showBoundary } = useErrorBoundary();

  const [formState, setFormState] = useState<Experience>(experience);

  const changeDropDown = (e: any, dataPath: string) => {
    const baseIndex: any = e.target.value;
    const [productType, retroPrint] = baseIndex.split('|').map(Number);

    let formStateCopy = JSON.parse(JSON.stringify(formState));
    formStateCopy.data = baseExperience[baseIndex][ExperienceType[experience.experienceType] as keyof Object];
    formStateCopy.productType = productType;
    formStateCopy.retroPrint = retroPrint === 1;

    setFormState(formStateCopy);
  };

  const onChangeExperience = (
    isChecked: boolean,
    event: any,
    dataPath: string,
    dependenciesControls: Array<ConditionControl> = [{ keys: [], action: [] }]
  ) => {
    if (event.preventDefault) {
      event.preventDefault();
    }
    // clone the state & update the dirty flag
    let formStateCopy = JSON.parse(JSON.stringify(formState));

    if (dataPath !== 'name') {
      setIsDirty(true);
    }

    let value: string | boolean = false;
    const type = event.target.type ? event.target.type : SettingsDataType.Text;

    switch (type) {
      case SettingsDataType.Checkbox:
        value = isChecked;
        //dependencies
        for (let i2 = 0; i2 < dependenciesControls.length; i2++) {
          const dependenciesControl = dependenciesControls[i2];
          for (let index = 0; index < dependenciesControl.action.length; index++) {
            const action: ConditionActions = dependenciesControl.action[index];
            if (isChecked.toString() === action.parentValue.toString()) {
              for (let i = 0; i < dependenciesControl.keys.length; i++) {
                set(formStateCopy, 'data.' + dependenciesControl.keys[i], action.value);
              }
            }
          }
        }
        break;
      case SettingsDataType.Radio:
        //dependencies
        value = event.target.value;

        for (let i2 = 0; i2 < dependenciesControls.length; i2++) {
          const dependenciesControl = dependenciesControls[i2];
          for (let index = 0; index < dependenciesControl.action.length; index++) {
            const action: ConditionActions = dependenciesControl.action[index];
            for (let i = 0; i < dependenciesControl.keys.length; i++) {
              if (action.parentValue.indexOf(value.toString()) > -1) {
                set(formStateCopy, 'data.' + dependenciesControl.keys[i], action.value);
              }
            }
          }
        }
        break;
      case SettingsDataType.Text:
      case SettingsDataType.Number:
      default:
        value = event.target.value;
        break;
    }

    formStateCopy = set(formStateCopy, dataPath, value);
    onSetCurrentKey(dataPath);
    setFormState(formStateCopy);
  };

  const productTypeSelectItems = [
    { label: t('str_ProductTypePhotobook', { ns: 'AdminConnectors' }), value: [ProductType.PhotoBook.toString(),'0'].join('|') },
    { label: t('str_ProductTypeCalendar', { ns: 'AdminConnectors' }), value: [ProductType.Calendar.toString(),'0'].join('|') },
  ];

  if (features.retroPrints) {
    productTypeSelectItems.push({
      label: t('str_ProductTypeRetroPrints', { ns: 'AdminExperience' }),
      value: [ProductType.PhotoBook.toString(),'1'].join('|'),
    });
  }

  const nameRef = useRef();

  useEffect(() => {
    let theExperience: Experience = null;

    if (experience !== null) {
      //if the experience has changed clear the errors
      onSetErrorArray({});

      //productType not applicable if SETTINGS template
      theExperience = { ...experience };
      if (parseInt(experience.experienceType.toString()) === ExperienceType.SETTINGS) {
        theExperience.productType = ProductType.Any;
      }      
    }
    setFormState(theExperience);
  }, [experience]);

  if (nameRef.current !== undefined && nameRef.current !== null && formState !== null) {
    (nameRef.current as any).value = formState.name.slice(0, 4) === 'str_' ? t(formState.name, { ns: 'AdminExperience' }) : formState.name;
  }

  return (
    <>
      {formState ? (
        busy.busy ? (
          <LoadingLosenge label={busy.msg} />
        ) : (
          <>
            <Theme name={ThemeName.Container} className="flex items-start p-sm mb-lg">
              <TextInput
                disabled={formState.systemType !== ExperienceSystemType.CUSTOM}
                label={t('str_LabelName', { ns: '*' })}
                defaultValue={formState.name.slice(0, 4) === 'str_' ? t(formState.name, { ns: 'AdminExperience' }) : formState.name}
                id={'experienceName' + formState.id}
                name={'experienceName' + formState.id}
                onChange={e => onChangeExperience(false, {target: {value: (nameRef.current as any).value}}, 'name')}
                error={formState.name === '' ? t('str_ExtJsTextFieldBlank', { ns: '*' }) : ''}
                className={'w-[300px]'}
                autoFocus
                ref={nameRef}
                onChangeDelay={1000}
                key={((experience !== null) ? experience.id : 0 ) + '_name'}
              />
              {parseInt(formState.experienceType.toString()) !== ExperienceType.SETTINGS && (
                <Label
                  id="experienceProductTypeLabel"
                  className={'ml-sm'}
                  htmlFor={'experienceProductType'}
                  label={t('str_LabelProductType', { ns: 'AdminExperience' })}
                >
                  <SelectList
                    onChange={e => changeDropDown(e, 'productType')}
                    items={productTypeSelectItems}
                    labelledBy={'experienceProductTypeLabel'}
                    shadowRoot={componentMountPoint}
                    id={'experienceProductType'}
                    selectedIndex={ProductType.Calendar === formState.productType ? 1 : formState.retroPrint ? 2 : 0}
                    disabled={isDirty || formState.id > 0}
                  />
                </Label>
              )}
              <Button
                disabled={formState.systemType !== ExperienceSystemType.CUSTOM}
                className="flex-none ml-auto"
                startIcon={<SaveIcon />}
                label={t('str_ButtonSave', { ns: '*' })}
                onClick={()=>onSaveExperienceData(formState)}
                size={'small'}
              />
            </Theme>
            <div className={'overflow-y-auto'}>
              <ExperienceForm
                features={features}
                errorArray={errorArray}
                onSetErrorArray={onSetErrorArray}
                componentMountPoint={componentMountPoint}
                currentKey={currentKey}
                sessionRef={props.sessionRef}
                schema={schema}
                onChangeExperience={onChangeExperience}
                experience={formState}
                key={'ExperienceDisplayRenderFormComponents' + ((formState !== null) ? formState.id : 0)}
              />
            </div>
          </>
        )
      ) : (
        <p className="italic">{t('str_MessageSelectExperienceConfiguration', { ns: 'AdminExperience' })}</p>
      )}
    </>
  );
};
