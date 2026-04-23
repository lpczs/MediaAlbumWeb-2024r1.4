import React, { useRef, useState } from 'react';
import {
  Button,
  CircleIcon,
  CopyIcon,
  DateIcon,
  FiltersIcon,
  Horizontal,
  List,
  ListItem,
  PlusIcon,
  PopOut,
  RadioButton,
  Vertical,
} from '@taopix/taopix-design-system';
import { useTranslation } from 'react-i18next';
import { ExperienceType } from '../../../Enums';
import { ListItemDataType } from '@taopix/taopix-design-system/dist/types/Components/SelectList/SelectList';

export interface ExperienceListMenuProps {
  sessionRef: number;
  onCreateNewExperience: Function;
  getIcon: Function;
  experienceType: ExperienceType;
  componentMountPoint: Element;
  sortList: Function;
}

export const ExperienceListMenu = ({
  componentMountPoint,
  experienceType,
  onCreateNewExperience,
  getIcon,
  sortList,
  ...props
}: ExperienceListMenuProps) => {
  const { t } = useTranslation();
  const [typePopOutOpen, setTypePopOutOpen] = useState(false);
  const [sortPopOutOpen, setSortPopOutOpen] = useState(false);
  const [sortDirection, setSortDirection] = useState('asc');

  const getLabelFromExperienceType = (experienceType: ExperienceType) => {
    switch (experienceType) {
      case ExperienceType.EDITOR:
        return t('str_LabelEditor', { ns: 'AdminExperience' });

      case ExperienceType.WIZARD:
        return t('str_LabelDesignAssistant', { ns: 'AdminExperience' });

      case ExperienceType.SETTINGS:
        return t('str_LabelExperienceSettings', { ns: 'AdminExperience' });

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

    return returnArray;
  };

  const selectSortItems = (): ListItemDataType[] => {
    return [
      { label: t('str_LabelName', { ns: '*' }), value: 'name', icon: <CircleIcon /> },
      { label: t('str_LabelDateCreated', { ns: '*' }), value: 'id', icon: <DateIcon /> },
    ];
  };

  const onToggleExperienceTypeList = () => {
    setTypePopOutOpen(!typePopOutOpen);
  };

  const onToggleSortList = () => {
    setSortPopOutOpen(!sortPopOutOpen);
  };

  const typeRef = useRef(null);
  const sortRef = useRef(null);

  return (
    <div className="flex">
      <Button
        role="button"
        startIcon={<PlusIcon />}
        label={t('str_ButtonNew', { ns: '*' })}
        onClick={
          ExperienceType.FULL === experienceType ? event => onToggleExperienceTypeList() : () => onCreateNewExperience()
        }
        aria-pressed={true}
        id={'newExperienceButton'}
      />

      <PopOut
        repositionOnResize={true}
        offset={{ x: 0, y: 0 }}
        id={'newExperienceDropDown'}
        className={'flex-col mt-10'}
        open={typePopOutOpen}
        contentRef={typeRef}
        anchorId={'newExperienceButton'}
        anchorOrigin={{ vertical: Vertical.Top, horizontal: Horizontal.Left }}
        transformOrigin={{ vertical: Vertical.Top, horizontal: Horizontal.Left }}
        onClickOutside={onToggleExperienceTypeList}
        displayMode={'positioned'}
        shadowRoot={componentMountPoint}
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
                          onCreateNewExperience(experienceType.value);
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

      <Button
        role="button"
        startIcon={<FiltersIcon />}
        label={t('str_LabelSort', { ns: '*' })}
        onClick={onToggleSortList}
        aria-pressed={true}
        id={'sortExperienceButton'}
        className={'ml-2'}
      />

      <PopOut
        repositionOnResize={true}
        offset={{ x: 0, y: 0 }}
        id={'sortExperienceDropDown'}
        className={'flex-col mt-10'}
        open={sortPopOutOpen}
        contentRef={sortRef}
        anchorId={'sortExperienceButton'}
        anchorOrigin={{ vertical: Vertical.Top, horizontal: Horizontal.Left }}
        transformOrigin={{ vertical: Vertical.Top, horizontal: Horizontal.Left }}
        onClickOutside={onToggleSortList}
        displayMode={'positioned'}
        shadowRoot={componentMountPoint}
      >
        <List>
          <ListItem key={'listItem-ascdesc'}>
            <div className="pt-4" role="radiogroup">
              <RadioButton
                key={'asc'}
                id={'asc'}
                name={'asc'}
                value={'asc'}
                label={t('str_LabelSortAscending', { ns: 'AdminExperience' })}
                groupName={'sorting'}
                defaultChecked={sortDirection === 'asc'}
                onChange={e => {
                  setSortDirection('asc');
                }}
                className={''}
              />
              <RadioButton
                key={'desc'}
                id={'desc'}
                name={'desc'}
                value={'desc'}
                label={t('str_LabelSortDescending', { ns: 'AdminExperience' })}
                groupName={'sorting'}
                defaultChecked={sortDirection === 'desc'}
                onChange={e => {
                  setSortDirection('desc');
                }}
                className={''}
              />
            </div>
            <hr className={'border-themeBorderColour'} />
          </ListItem>
          {selectSortItems().map(sort => {
            return (
              <ListItem key={'listItem-' + sort.value}>
                <Button
                  label={sort.label}
                  corners={'square'}
                  labelAlignment={'left'}
                  buttonStyle={'standard'}
                  startIcon={sort.icon}
                  onClick={
                    sort.value !== '-1'
                      ? e => {
                          sortList(sort.value, sortDirection);
                          onToggleSortList();
                        }
                      : () => {}
                  }
                  key={sort.value}
                  id={'experienceSort-' + sort.value}
                />
              </ListItem>
            );
          })}
          {/*
          <ListItem>
            <Button
              role="button"
              label={t('str_LabelApply', { ns: 'AdminExperience' })}
              onClick={()=>{}}
              id={'applySortExperienceButton'}
              className={''}
            />
          </ListItem>
        */}
        </List>
      </PopOut>
    </div>
  );
};
