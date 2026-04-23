import React, { useEffect, useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { get } from 'lodash';
import { EditSection } from '../Edit/EditSection';
import { ConditionControl, Experience, ExperienceError, Features } from '../../../types';
import { ExperienceSystemType, SettingsDataType } from '../../../Enums';
import { ExperienceInputComponentsFactory } from './ExperienceInputComponentsFactory';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}
export interface ExperienceFormProps {
  sessionRef: number;
  experience: Experience;
  schema: Object;
  currentKey?: string;
  onChangeExperience: (
    value: boolean,
    event: any,
    datapath: string,
    dependenciesControl: Array<ConditionControl>
  ) => void;
  componentMountPoint: Element;
  onSetErrorArray: Function;
  errorArray: { [dataPath: string]: ExperienceError };
  features: Features;
}

export const ExperienceForm = ({
  features,
  onSetErrorArray,
  errorArray,
  experience,
  schema,
  currentKey = '',
  onChangeExperience,
  ...props
}: ExperienceFormProps) => {
  const { t } = useTranslation();

  const isDisabled = (disableControls: ConditionControl[], altDataPath: string = '') => {
    let disabled = false;
    let disabledText = '';

    for (let x = 0; x < disableControls.length; x++) {
      const disableControl = disableControls[x];

      if (
        !disableControl.hasOwnProperty('productType') ||
        (disableControl.hasOwnProperty('productType') &&
          disableControl.productType === experience.productType &&
          (!disableControl.hasOwnProperty('retroPrint') || disableControl.retroPrint === experience.retroPrint))
      ) {
        if (disableControl.keys.length > 0) {
          for (let i = 0; i < disableControl.keys.length; i++) {
            for (let i2 = 0; i2 < disableControl.action.length; i2++) {
              for (let i3 = 0; i3 < disableControl.action[i2].parentValue.length; i3++) {
                if (
                  get(experience.data, disableControl.keys[i], false).toString() ===
                  disableControl.action[i2].parentValue[i3].toString()
                ) {
                  disabled = true;
                  disabledText = disableControl.helpText ?? '';

                  if (disableControl.action[i2].hasOwnProperty('alternateSelection') && altDataPath !== '') {
                    const current = get(experience, altDataPath, false).toString();
                    const alternateSelection = disableControl.action[i2].alternateSelection;
                    if (current === alternateSelection.from) {
                      const event = {
                        target: { type: SettingsDataType.Radio, value: alternateSelection.to, dataPath: altDataPath },
                      };
                      onChangeExperience(false, event, altDataPath, []);
                    }
                  }
                }
              }
            }
          }
        } else {
          if (disableControl.action.length > 0) {
            disabled = disableControl.action[0].value.toString() === 'true';
            disabledText = disableControl.helpText ?? '';
          }
        }
      }
    }

    return { disabled: disabled, disabledText: disabledText };
  };

  const generateInitialListState = (schemaObject: Record<string, any>) => {
    const initialState: Record<string, boolean> = {};

    Object.keys(schemaObject).forEach(key => {
      initialState[key] = false;
    });

    return initialState;
  };

  const [listState, setListState] = useState(generateInitialListState(schema));

  const toggleSectionVisibility = (key: string) => {
    setListState(prevState => ({
      ...prevState,
      [key]: !prevState[key],
    }));
  };

  const renderItem = (key: any, item: any, parentKey: string): React.ReactElement => {
    //if the section is only for a particular product type check if it matches the current type (& if its allowed on a retro print)
    //don't render if its not
    if (
      (item.hasOwnProperty('productType') && item.productType !== experience.productType) ||
      (item.hasOwnProperty('productType') &&
        item.productType === experience.productType &&
        item.hasOwnProperty('retroPrint') &&
        item.retroPrint !== experience.retroPrint) ||
      (!item.hasOwnProperty('productType') &&
        item.hasOwnProperty('retroPrint') &&
        item.retroPrint !== experience.retroPrint)
    ) {
      return <></>;
    }

    const dataType = !item.hasOwnProperty('dataType') && item.hasOwnProperty('sections') ? 'section' : item.dataType;
    const dataPath = (parentKey !== '' ? parentKey + '.' : '') + key;

    let disableControl: Array<ConditionControl> = [{ keys: [], action: [], helpText: '' }];
    let dependenciesControl: Array<ConditionControl> = [];

    let visible = true;
    let hasParent = false;
    let disabled = { disabled: false, disabledText: '' };
    let level: number = 1;
    let indent: string = 'ml-4';
    let subSection = false;
    let featureInstalled = true;

    if (item.hasOwnProperty('condition')) {
      if (item.condition.hasOwnProperty('disableControl')) {
        disableControl = item.condition.disableControl;
      }

      if (item.condition.hasOwnProperty('dependencies')) {
        dependenciesControl = item.condition.dependencies;
      }

      //currently only checking imageScaleBeforeUploadEnabled here to hide from interface
      //Ai featurecontrol still need to render but disable the setting 
      if (item.condition.hasOwnProperty('featureControl')) {
        if (item.condition.featureControl.hasOwnProperty('imageScaleBeforeUploadEnabled') && !features.scaleBeforeUpload) {
          featureInstalled = false;
        }
      }
    }

    //if a required feature is not enabled do not render this item (i.e. imageScaleBeforeUploadEnabled)
    if (!featureInstalled) {
      return <></>;
    }

    if (item.hasOwnProperty('parent')) {
      hasParent = true;
    }

    if (item.hasOwnProperty('level')) {
      level = item.level;

      switch (level) {
        case 2:
          indent = 'ml-8';
          break;

        case 3:
          indent = 'ml-12';
          break;

        case 4:
          indent = 'ml-16';
          break;

        default:
          indent = 'ml-4';
          break;
      }
    }

    disabled = isDisabled(disableControl);

    if (experience.systemType !== ExperienceSystemType.CUSTOM) {
      disabled.disabled = true;
    }

    if (item.hasOwnProperty('subSection')) {
      subSection = item.subSection;
    }

    switch (dataType) {
      case SettingsDataType.Section:
        return (
          <EditSection
            experienceId={experience.id}
            dataPath={dataPath}
            label={item.label ? t(item.label, { ns: 'AdminExperience' }) : item.label}
            helpText={item.hasOwnProperty('helpText') ? t(item.helpText, { ns: 'AdminExperience' }) : ''}
            hasError={Object.keys(errorArray).some(key => key.includes(dataPath))}
            sections={item.sections}
            renderItem={renderItem}
            parentKey={parentKey}
            theKey={key}
            key={'ExperienceDisplayEditSection_' + experience.id + '_' + parentKey + '_' + key}
            className={!visible || (item.hasOwnProperty('hidden') && item.hidden) ? 'hidden' : ''}
            subSection={subSection}
            expandable={!subSection} // Some of the schema should be re-organised if we want to collapse sub sections (see workarea section)
            expanded={listState[key]}
            toggleSectionVisibility={toggleSectionVisibility}
            disabled={disabled}
          />
        );
      case SettingsDataType.Radio:
      case SettingsDataType.Checkbox:
      case SettingsDataType.Number:
      case SettingsDataType.Text:
      default:
        return (
          <ExperienceInputComponentsFactory
            theKey={key}
            item={item}
            experience={experience}
            dataPath={dataPath}
            dependenciesControl={dependenciesControl}
            parentKey={parentKey}
            onChangeExperience={onChangeExperience}
            className={indent}
            componentMountPoint={props.componentMountPoint}
            errorArray={errorArray}
            currentKey={currentKey}
            disabled={disabled}
            disabledHelpText={disabled.disabledText ?? ''}
            onSetErrorArray={onSetErrorArray}
            isDisabled={isDisabled}
            features={features}
          />
        );
    }
  };

  return (
    <>
      {schema !== null &&
        Object.entries(schema).map(([key, item]) => {
          return (
            <React.Fragment key={experience.id + '_' + key + '_' + Math.random()}>
              {renderItem(key, item, '')}
            </React.Fragment>
          );
        })}
    </>
  );
};
