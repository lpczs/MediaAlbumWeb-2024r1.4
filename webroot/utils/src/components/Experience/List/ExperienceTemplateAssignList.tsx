import React from 'react';
import {
  Button,

  LoadingLosenge,
  PlusIcon,
  SelectList,
} from '@taopix/taopix-design-system';
import {
  AssignmentTypeData,
  ExperienceAssignment,
  ExperienceOverviewServerResponse,
  ProductTypeData,
  TemplateSelect,
} from '../../../types';
import { useTranslation } from 'react-i18next';
import { AssignmentType, ExperienceAssignMode, ExperienceType, ProductType } from '../../../Enums';
import classNames from 'classnames';
import { OverviewTableProps } from '../Assignment/Table/OverviewTable';
import { buttonSizeTypes } from '@taopix/taopix-design-system/dist/types/Components/Button/Button';

export interface ExperienceTemplateAssignListProps {
  componentMountPoint: Element;
  theKey: string;
  typeData: AssignmentTypeData;
  productType: ProductTypeData;
  disabled?: boolean;
  templateList: TemplateSelect[];
  data: ExperienceOverviewServerResponse;
  onApplyExperience: Function;
  onSelectTemplate: Function;
  mode: ExperienceAssignMode;
  displayAsSelect: Function;
  individualSaving?: Boolean;
  onSetDisplaySelectList: Function;
  changeAndDisplayMessage: Function;
  onDeleteAssignment: Function;
  closeMessage: Function;
  rowHeight?: buttonSizeTypes;
  id?: string;
}

export const ExperienceTemplateAssignList = ({
  templateList,
  theKey,
  typeData,
  productType,
  onApplyExperience,
  onSelectTemplate,
  mode,
  data,
  disabled = false,
  displayAsSelect,
  individualSaving,
  onSetDisplaySelectList,
  changeAndDisplayMessage,
  onDeleteAssignment,
  closeMessage,
  componentMountPoint,
  rowHeight,
  id,
  ...props
}: ExperienceTemplateAssignListProps) => {
  const { t } = useTranslation();

  const buildKeyArray = (
    key: string,
    experienceType: ExperienceType,
    productType: ProductType,
    retroPrint: boolean
  ): Array<keyof ExperienceAssignment> => {
    const wildCard = '*';
    const delimiter = '.';
    const keyTypeDelimiter = '|';
    const [brand, licenseKey, collectionCode, layoutCode] = key.split(delimiter);
    const productCode = [collectionCode, layoutCode].join(delimiter);
    const typeInfo = [experienceType, productType, Number(retroPrint)].join(keyTypeDelimiter);

    let keyArray: string[] = [
      [[brand, licenseKey, productCode].join(delimiter), typeInfo].join(keyTypeDelimiter),
      [[brand, wildCard, productCode].join(delimiter), typeInfo].join(keyTypeDelimiter),
      [[wildCard, wildCard, productCode].join(delimiter), typeInfo].join(keyTypeDelimiter),
      [[brand, licenseKey, wildCard, wildCard].join(delimiter), typeInfo].join(keyTypeDelimiter),
      [[brand, wildCard, wildCard, wildCard].join(delimiter), typeInfo].join(keyTypeDelimiter),
    ];

    let returnArray: string[] = [];
    for (let i = 0; i < keyArray.length; i++) {
      const thisKey = keyArray[i];

      if (returnArray.indexOf(thisKey) === -1) {
        returnArray.push(thisKey);
      }
    }

    return returnArray as Array<keyof ExperienceAssignment>;
  };

  const showDeleteConfirmationMessage = (
    theKey: string,
    assignmentType: AssignmentType,
    selection: [{ theKey: string; type: ExperienceType; productType: ProductTypeData }]
  ) => {
    const messageProps = {
      open: true,
      positiveClick: () => {
        onDeleteAssignment(theKey, assignmentType, selection);
      },
      negativeClick: closeMessage,
      positiveLabel: t('str_ButtonDelete', { ns: '*' }),
      negativeLabel: t('str_ButtonCancel', { ns: '*' }),
      message: [
        t('str_MessageAreYouSureYouWishToDeleteAssignment', { ns: 'AdminExperience' }),
        inheritanceString(theKey),
      ].join(' '),
      heading: t('str_LabelInformation', { ns: '*' }),
      afterClose: () => {},
    };
    changeAndDisplayMessage(messageProps);
  };

  const showInheritMessage = (message: string) => {
    const messageProps = {
      open: true,
      negativeClick: () => {},
      positiveClick: closeMessage,
      positiveLabel: t('str_ButtonClose', { ns: '*' }),
      negativeLabel: '',
      message: message,
      heading: t('str_LabelInformation', { ns: '*' }),
      afterClose: () => {},
    };
    changeAndDisplayMessage(messageProps);
  };

  const getTemplateName = (templateId: number, experienceType: ExperienceType, type: AssignmentType) => {
    let label = '';
    if (AssignmentType.Experience === type) {
      label = templateList[experienceType][templateId].label;
    } else {
      let theme = Object.values(data.themes).find(theme => theme.id === templateId);
      label = theme?.name || 'unknown';
    }

    return label.slice(0, 4) === 'str_' ? t(label, { ns: 'AdminExperience' }) : label;
  };

  const inheritanceString = (theKey: string) => {
    const [keyString, exType] = theKey.split('|');
    const [brandCode, licenseKeyCode, collectionCode, productCode] = keyString.split('.');

    if (brandCode !== '*' && licenseKeyCode === '*' && collectionCode === '*' && productCode === '*') {
      return t('str_MessageBrandSettingToolTip', { ns: 'AdminExperience' }) + ' ' + brandCode;
    }

    if (brandCode !== '*' && licenseKeyCode !== '*' && collectionCode === '*' && productCode === '*') {
      return (
        t('str_MessageBrandAndKeySettingToolTip', { ns: 'AdminExperience' }) + ' ' + brandCode + ' - ' + licenseKeyCode
      );
    }

    if (brandCode === '*' && licenseKeyCode === '*' && collectionCode !== '*' && productCode !== '*') {
      return (
        t('str_MessageProductSettingToolTip', { ns: 'AdminExperience' }) + ' ' + collectionCode + ' - ' + productCode
      );
    }

    if (brandCode !== '*' && licenseKeyCode === '*' && collectionCode !== '*' && productCode !== '*') {
      return t('str_MessageBrandAndAnyKeyAndProductSettingToolTip', { ns: 'AdminExperience' }) + ' ' + brandCode;
    }

    return '';
  };

  const getInheritLabel = () => {
    return isTopLevel() ? getDefaultTemplatesString() : t('str_LabelAuto', { ns: 'AdminExperience' });
  };

  const getDefaultTemplatesString = () => {
    return AssignmentType.Theme === typeData.type
      ? t('str_LabelTaopixTheme', { ns: 'AdminTheming' })
      : t('str_LabelDefaultTemplates', { ns: 'AdminExperience' });
  };

  const getAssignmentData = (
    theKey: string,
    type: AssignmentType,
    experienceType: ExperienceType,
    productType: ProductType,
    retroPrint: boolean
  ) => {
    let value = 0;
    let name = getDefaultTemplatesString();
    const keyArray: Array<keyof ExperienceAssignment> = buildKeyArray(theKey, experienceType, productType, retroPrint);
    let inherited = true;
    let inheritedFrom = '';
    const keyTypeCombo = [theKey, experienceType, productType, Number(retroPrint)].join('|');

    for (let i = 0; i < keyArray.length; i++) {
      const checkKey: keyof ExperienceAssignment = keyArray[i];
      if (data.assignment.hasOwnProperty(type) && data.assignment[type].hasOwnProperty(checkKey)) {
        value = data.assignment[type][checkKey].templateId;
        name = getTemplateName(value, experienceType, type);
        if (checkKey === keyTypeCombo) {
          inherited = false;
        } else {
          inheritedFrom = inheritanceString(checkKey);
        }
        break;
      }
    }

    if (0 === value) {
      inheritedFrom = getDefaultTemplatesString();
    }

    if (inherited) {
      name = t('str_LabelAuto', { ns: 'AdminExperience' }) + ' (' + name + ')';
    }

    return { id: value, inherited: inherited, inheritedFrom: inheritedFrom, name: name };
  };

  const createNewConfiguration = () => {
    //find the experience or theme button and click it
    const faction = (typeData.type === AssignmentType.Theme) ? 'AdminExperienceTheme': 'AdminExperienceEditing';
    const selector = `[faction="${faction}"]`;
    const configUIButton = document.querySelectorAll(selector);
      for (let index = 0; index < configUIButton.length; index++) {
        const element: any = configUIButton[index];
        element.click();
      }
  }

  const chooseTemplate = (e: any, theKey: string, typeData: AssignmentTypeData, productType: ProductTypeData) => {
    if (parseInt(e.target.value) !== -1) {
      //inherit is a special type as it deletes the assignment data
      const selectedTemplate =
        parseInt(e.target.value) === -100
          ? { label: '', value: -100, systemType: 0, productType: productType.type, retroPrint: productType.retroPrint }
          : AssignmentType.Experience === typeData.type
          ? Object.values(templateList[typeData.subType]).find(t => t.value === (e.target as HTMLInputElement).value)
          : {
              label: '',
              value: Object.values(data.themes).find(t => t.id === parseInt((e.target as HTMLInputElement).value)).id,
              systemType: 0,
              productType: productType.type,
              retroPrint: productType.retroPrint,
            };

      const template = {
        templateId: parseInt(selectedTemplate.value.toString()),
        type: typeData.subType,
        productType: productType.type,
        retroPrint: productType.retroPrint,
      };
      if (theKey !== '') {
        onApplyExperience(e, {
          keys: [theKey],
          templates: [template],
          assignmentType: typeData.type,
        });
      } else {
        onSelectTemplate(typeData.type, template);
      }
    }
  };

  const getTemplatesForType = (typeData: AssignmentTypeData) => {
    switch (typeData.type) {
      case AssignmentType.Experience:
        return (templateList.hasOwnProperty(typeData.subType))
        ?
          mode === ExperienceAssignMode.BrandAndKey
          ? Object.values(templateList[typeData.subType]).filter(template => {
              return (
                (template.productType === productType.type && template.retroPrint === productType.retroPrint) ||
                typeData.subType === ExperienceType.SETTINGS
              );
            })
          : Object.values(templateList[typeData.subType])
        :
        []
        break;

      case AssignmentType.Theme:
        return data !== null
          ? Object.values(data.themes).map(theme => {
              return {
                label: theme.name,
                value: theme.id.toString(),
              };
            })
          : [];
        break;

      default:
        return [];
        break;
    }
  };

  const isTopLevel = (): Boolean => {
    return theKey.slice(-6) === '.*.*.*' && mode === ExperienceAssignMode.BrandAndKey;
  };

  const buildAssignmentSelectList = () => {
    const systemItems = [
      {
        label: t('str_LabelAuto', { ns: 'AdminExperience' }),
        value: '-100',
      },
    ];
    const createNew = [
      {
        label: t('str_LabelCreateNew', { ns: 'AdminExperience' }),
        value: '-50',
        icon: <PlusIcon/>
      }
    ]
    const customTemplates = getTemplatesForType(typeData).sort((a: any, b: any) => {
      return a.label.localeCompare(b.label);
    });;

    const items = [
      ...systemItems, 
      ...customTemplates,
      ...createNew
    ];

    const assignmentData =
      theKey !== ''
        ? getAssignmentData(theKey, typeData.type, typeData.subType, productType.type, productType.retroPrint)
        : { id: 0, inherited: false, inheritedFrom: '', name: '' };

    const selectedIndex =
      theKey === ''
        ? -1 // Shows the placeholder
        : assignmentData.id > 0
        ? items.findIndex(el => {
            return el.value.toString() === assignmentData.id.toString();
          }) // Selects the assigned value
        : 0; // Selects the 'Auto' option

    return (
      <>
        {displayAsSelect({ theKey: theKey, type: typeData.subType, productType: productType }) > -1 || theKey === '' ? (
          individualSaving && theKey !== '' ? (
            <LoadingLosenge label={t('str_MessageSaving', { ns: '*' })} />
          ) : (
            <SelectList
              type={'button'}
              open={theKey !== ''} // Open automatically when appearing in a row
              buttonProps={
                theKey === ''
                  ? {
                      className: 'w-full',
                      labelAlignment: 'left',
                      justified: true,
                    }
                  : {
                      corners: 'square',
                      className: 'w-full',
                      labelAlignment: 'left',
                      justified: true,
                    }
              }
              className={'w-full'}
              buttonStyle={theKey === '' ? 'distinct' : 'standard'}
              onChange={e => {
                  //if this is the createnew button we need to naviate away
                  if (e.target.value === '-50') {
                    createNewConfiguration();
                  } else {
                    chooseTemplate(e, theKey, typeData, productType)
                  }
                }
              }
              items={items}
              labelledBy={''}
              shadowRoot={componentMountPoint}
              id={
                'select_' + productType.type + productType.retroPrint + typeData.subType + theKey.replace(/[*. ]/g, '')
              }
              popDirection={'down'}
              edgeBehaviour={'best-fit'}
              displayMode={'positioned'}
              disabled={disabled}
              selectedIndex={selectedIndex}
              onCloseWithoutChanging={(selectedValue: any) => {
                onSetDisplaySelectList([]);
              }}
              size={rowHeight}
              placeholder={theKey === '' ? t('AdminExperience:str_LabelApplyToSelectedRows') : ''}
            />
          )
        ) : (
          // <>{assignmentData.name}</>
          <Button
            buttonStyle="standard"
            corners="square"
            arrow={'down'}
            labelAlignment="left"
            labelLines={1}
            size={rowHeight}
            onClick={() =>
              onSetDisplaySelectList([
                {
                  theKey: theKey,
                  type: typeData.subType,
                  productType: {
                    type: productType.type,
                    retroPrint: productType.retroPrint,
                  },
                },
              ])
            }
            label={assignmentData.name}
            className={classNames('w-full', assignmentData.inherited && 'italic !text-themeBtnTextColDisabled')}
          />
        )}
      </>
    );
  };

  return buildAssignmentSelectList();
};
