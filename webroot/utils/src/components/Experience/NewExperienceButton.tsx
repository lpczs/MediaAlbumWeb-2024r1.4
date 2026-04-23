import React, { useRef, useState } from 'react';
import { PopOut, Vertical, Horizontal, List, ListItem, Button, PlusIcon } from '@taopix/taopix-design-system';
import { t } from 'i18next';
import { ListItemDataType } from '@taopix/taopix-design-system/dist/types/Components/SelectList/SelectList';
import { useTranslation } from 'react-i18next';
import { ExperienceType } from '../../Enums';
import { PopOutProps } from '@taopix/taopix-design-system/dist/types/Components/PopOut/PopOut';

type NewExperienceButtonPropTypes = { getIcon: Function; onCreateNewExperience: Function, disabled: boolean } & Required<
  Pick<PopOutProps, 'shadowRoot'>
>;

const NewExperienceButton = ({ getIcon, shadowRoot, onCreateNewExperience, disabled }: NewExperienceButtonPropTypes) => {
  const { t } = useTranslation();
  const [typePopOutOpen, setTypePopOutOpen] = useState(false);

  const getLabelFromExperienceType = (experienceType: ExperienceType) => {
    switch (experienceType) {
      case ExperienceType.EDITOR:
        return t('str_LabelEditorConfiguration', { ns: 'AdminExperience' });

      case ExperienceType.WIZARD:
        return t('str_LabelDesignAssistantConfiguration', { ns: 'AdminExperience' });

      case ExperienceType.SETTINGS:
        return t('str_LabelExperienceSettingsConfiguration', { ns: 'AdminExperience' });

      default:
        return '';
    }
  };

  const selectListExperienceTypes = () => {
    let returnArray: ListItemDataType[] = [];
    for (var enumMember in ExperienceType) {
      var isValueProperty = Number(enumMember) >= 0;
      if (isValueProperty && Number(enumMember) !== ExperienceType.FULL) {
        returnArray.push({ label: getLabelFromExperienceType(Number(enumMember)), value: enumMember });
      }
    }

    returnArray.sort((a: ListItemDataType, b: ListItemDataType) => {
      const aType = (parseInt(a.value) === 1) ? 4 : parseInt(a.value)
      const bType = (parseInt(b.value) === 1) ? 4 : parseInt(b.value)

      return (aType - bType)
    });

    return returnArray;
  };

  const onToggleExperienceTypeList = () => {
    setTypePopOutOpen(!typePopOutOpen);
  };

  const typeRef = useRef(null);
  return (
    <>
      <Button
        id="newExperienceButton"
        onClick={() => setTypePopOutOpen(true)}
        buttonStyle="primary"
        corners="theme"
        label={t('str_ButtonNew', { ns: '*' })}
        startIcon={<PlusIcon />}
        size="small"
        disabled={disabled}
      />
      <PopOut
        id={'newExperienceDropDown'}
        className={'flex-col'}
        open={typePopOutOpen}
        contentRef={typeRef}
        anchorId={'newExperienceButton'}
        anchorOrigin={{ vertical: Vertical.Bottom, horizontal: Horizontal.Left }}
        transformOrigin={{ vertical: Vertical.Top, horizontal: Horizontal.Left }}
        onClickOutside={onToggleExperienceTypeList}
        shadowRoot={shadowRoot}
      >
        <List>
          {selectListExperienceTypes().map(experienceType => {
            return (
              <ListItem key={'listItem-' + experienceType.value}>
                <Button
                  label={experienceType.label}
                  corners={'square'}
                  labelAlignment={'left'}
                  buttonStyle={'standard'}
                  startIcon={getIcon(Number(experienceType.value))}
                  onClick={
                    experienceType.value !== '-1'
                      ? e => {
                          onCreateNewExperience(parseInt(experienceType.value));
                          onToggleExperienceTypeList();
                        }
                      : () => {}
                  }
                  key={experienceType.value}
                  id={'experienceType-' + experienceType.value}
                />
              </ListItem>
            );
          })}
        </List>
      </PopOut>
    </>
  );
};

export default NewExperienceButton;
