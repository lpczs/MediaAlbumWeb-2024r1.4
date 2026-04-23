import React, { useEffect, useRef, useState } from 'react';
import {
  ArrowDownIcon,
  Button,
  CopyIcon,
  Icon,
  LoadingLosenge,
  LockIcon,
  ThemeName,
  TrashIcon,
  getTheme,
} from '@taopix/taopix-design-system';
import { Experience, Features, ProductTypeData } from '../../../types';
import { useTranslation } from 'react-i18next';
import { ExperienceType, ExperienceSystemType, ProductType } from '../../../Enums';
import classNames from 'classnames';
import { ExperienceListTabs } from './ExperienceListTabs';
import { omit } from 'lodash';

export interface ExperienceListProps {
  sessionRef: number;
  experienceList: Array<Experience>;
  onCreateNewExperience: Function;
  getIcon: Function;
  selectedExperienceId: number;
  onSetExperienceState: Function;
  onDeleteExperienceData: Function;
  busy: { busy: boolean; msg: string };
  experienceType: ExperienceType;
  componentMountPoint: Element;
  features: Features;
}

export const ExperienceList = ({
  componentMountPoint,
  experienceType,
  experienceList,
  onCreateNewExperience,
  getIcon,
  selectedExperienceId,
  onSetExperienceState,
  onDeleteExperienceData,
  features,
  busy,
  ...props
}: ExperienceListProps) => {
  const { t } = useTranslation();
  const [sortedExperienceList, setSortedExperienceList] = useState<Array<Experience>>(experienceList);
  const [productType, setProductType] = useState<ProductTypeData>({ type: ProductType.Any, retroPrint: false });

  const onSetAssignProductType = (productType: ProductTypeData) => {
    setProductType(productType);
  };

  const sortList = (sortby: string, direction: string) => {
    let cloneSortedExperienceList = [...sortedExperienceList];

    switch (sortby) {
      case 'name':
        cloneSortedExperienceList = cloneSortedExperienceList.sort((a: Experience, b: Experience) => {
          return a.name.localeCompare(b.name);
        });
        break;

      case 'id':
      default:
        cloneSortedExperienceList = cloneSortedExperienceList.sort((a: Experience, b: Experience) => {
          return b.id - a.id;
        });
        break;
    }

    if (direction === 'desc') {
      cloneSortedExperienceList.reverse();
    }

    setSortedExperienceList(cloneSortedExperienceList);
  };

  const localiseName = (name: string) => {
    return name.slice(0, 4) === 'str_' ? t(name, { ns: 'AdminExperience' }) : name;
  };

  const onExport = (experience: Experience) => {
    const output = omit({...experience}, ['id', 'isdirty', 'dataLength', 'assignment']);
    const blob = new Blob([JSON.stringify(output, null, 2)], {
      type: 'application/json',
    });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${experience.name}.json`;
    a.click();
    URL.revokeObjectURL(url);
  };

  useEffect(() => {
    //First Time in Load data
    setSortedExperienceList(experienceList);
  }, [experienceList]);

  const height =
    experienceType !== ExperienceType.FULL && experienceType !== ExperienceType.SETTINGS
      ? 'h-[calc(100%-4rem)]'
      : 'h-full';
  const classes = classNames('overflow-y-auto', height);

  const selectedRef = useRef(null);

  const executeScroll = () => {
    if (selectedRef.current !== null) {
      selectedRef.current.scrollIntoView();
    }
  }

  useEffect(() => {
    if (selectedExperienceId !== null) {
      setTimeout(() => {
        executeScroll();
      },1000);
    }
  }, [selectedExperienceId]);

  return (
    <>
      {/* <ExperienceListMenu sortList={sortList} sessionRef={props.sessionRef} onCreateNewExperience={onCreateNewExperience} getIcon={getIcon} componentMountPoint={componentMountPoint} experienceType={experienceType} /> */}
      {(experienceType === ExperienceType.EDITOR || experienceType === ExperienceType.WIZARD) && (
        <ExperienceListTabs
          productType={productType}
          retroPrintsEnabled={features.retroPrints}
          onSetAssignProductType={onSetAssignProductType}
        />
      )}

      {busy.busy ? (
        <div className={'p-sm'}>
          <LoadingLosenge label={busy.msg} />
        </div>
      ) : (
        <div className={classes}>
          <ul className="list-none m-xs">
            {sortedExperienceList
              .map((ex: Experience) => {
                const rowClasses = classNames(
                  ex.id === selectedExperienceId
                    ? getTheme(ThemeName.Prominent, true, false, false)
                    : 'hover:bg-black/5',
                  'flex',
                  'h-xxl',
                  'items-center',
                  'px-sm',
                  'cursor-pointer',
                  'group/experience-row',
                  'rounded-themeCornerSize'
                );
                return ex.systemType !== ExperienceSystemType.LEGACY &&
                  ((productType.type === ex.productType && productType.retroPrint === ex.retroPrint) ||
                    experienceType === ExperienceType.SETTINGS ||
                    experienceType === ExperienceType.FULL ||
                    productType.type === ProductType.Any) ? (
                  <React.Fragment key={ex.id + '_Frag'}>
                    {(experienceType === 0 || experienceType === ex.experienceType) && (
                      <li
                        ref={(ex.id === selectedExperienceId) ? selectedRef : null}
                        key={ex.id}
                        className={rowClasses}
                        onClick={
                          ex.id !== selectedExperienceId
                            ? () => {
                                onSetExperienceState(ex);
                              }
                            : () => {}
                        }
                      >
                        <Icon icon={getIcon(Number(ex.experienceType))} />
                        <span className={'w-[250px] flex-1 pl-sm line-clamp-1 overflow-ellipsis'}>
                          {localiseName(ex.name)}
                        </span>
                        <div className={'flex items-center'}>
                          <Button
                            size={'small'}
                            startIcon={<CopyIcon />}
                            onClick={e => {
                              e.preventDefault();
                              e.stopPropagation();
                              onCreateNewExperience(null, ex);
                            }}
                            buttonStyle="standard"
                            label={t('str_ButtonDuplicate')}
                            hideLabel
                            corners="square"
                            className={'mouse:!hidden group-hover/experience-row:!flex'}
                          />

                          {ex.systemType !== ExperienceSystemType.CUSTOM ? (
                            <Icon icon={<LockIcon />} size={'small'} className={'mx-xs'} />
                          ) : (
                            <>
                              <Button
                                label={t('str_ButtonCopy')}
                                hideLabel
                                size={'small'}
                                startIcon={<ArrowDownIcon />}
                                onClick={() => onExport(ex)}
                                buttonStyle="standard"
                                className={'mouse:!hidden group-hover/experience-row:!flex'}
                              />
                              <Button
                                size={'small'}
                                startIcon={<TrashIcon />}
                                onClick={e => {
                                  e.preventDefault();
                                  e.stopPropagation();
                                  onDeleteExperienceData(ex);
                                }}
                                buttonStyle="standard"
                                label={t('str_ButtonDelete')}
                                hideLabel
                                corners="square"
                                className={'mouse:!hidden group-hover/experience-row:!flex'}
                              />
                            </>
                          )}
                        </div>
                      </li>
                    )}
                  </React.Fragment>
                ) : null;
              })
              .filter(c => c)}
          </ul>
        </div>
      )}
    </>
  );
};
