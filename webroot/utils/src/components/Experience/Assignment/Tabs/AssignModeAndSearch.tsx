import React, { useEffect, useRef } from 'react';
import {
  Button,
  CloseIcon,
  DecendantIcon,
  Icon,
  SearchIcon,
  SelectList,
  TextInput,
} from '@taopix/taopix-design-system';
import { ExperienceAssignMode } from '../../../../Enums';
import { useTranslation } from 'react-i18next';
import { Brand } from '../../../../types';
import { ListItemDataType } from '@taopix/taopix-design-system/dist/types/Components/SelectList/SelectList';

export interface AssignModeAndSearchProps {
  sessionRef: number;
  mode: ExperienceAssignMode;
  search: Function;
  cancelSearch: Function;
  searchTerm: string;
  componentMountPoint: Element;
  brands: Brand;
  selectBrandKeyFilter: React.FormEventHandler<HTMLUListElement>;
  loading?: boolean;
}

export const AssignModeAndSearch = ({
  selectBrandKeyFilter,
  brands,
  componentMountPoint,
  searchTerm,
  cancelSearch,
  search,
  mode,
  loading,
  ...props
}: AssignModeAndSearchProps) => {
  const { t } = useTranslation();
  const isLoading = useRef(loading);
  const buildBrandKeySelectOptions = () => {
    let listItems: Array<ListItemDataType> = [];
    listItems.push({ label: t('str_LabelAll', { ns: '*' }), value: '' });

    if (Object.entries(brands).length > 0) {
      Object.entries(brands).map(([brandIndex, brand]) => {
        const brandCode = brand.code !== '' ? brand.code : t('str_LabelDefault', { ns: '*' });
        listItems.push({ label: brandCode, value: brand.code + '.*' });
        Object.entries(brand.licenseKeys).map(([licenseKeyIndex, licenseKey]) => {
          listItems.push({
            label: licenseKey.code,
            value: brand.code + '.' + licenseKey.code,
            icon: <DecendantIcon />,
          });
        });
      });
    }
    return listItems;
  };

  const buildBrandSelectList = () => {
    return (
      <SelectList
        onChange={selectBrandKeyFilter}
        placeholder={t('str_LabelFilterByBrandLicenseKey', { ns: 'AdminExperience' })}
        items={[...buildBrandKeySelectOptions()]}
        labelledBy={''}
        shadowRoot={componentMountPoint}
        size={'small'}
        className="w-[300px]"
        disabled={(Object.entries(brands).length === 0)}
      />
    );
  };

  const ref = useRef();

  if (ref.current !== undefined && ref.current !== null) {
    (ref.current as any).value = searchTerm;
  }

  const delaySearch = (e: any) => {
    search(e);
  };

  useEffect(() => {
    isLoading.current = loading;
  }, [loading]);

  return (
    <div className={'flex space-x-sm'}>
      <div className="flex relative items-center">
        <Icon icon={<SearchIcon />} size={'small'} className="absolute left-sm" />
        <TextInput
          ref={ref}
          onChangeDelay={500}
          defaultValue={searchTerm}
          className="w-[200px] indent-[25px] focus:placeholder-transparent"
          onChange={e => delaySearch(e)}
          id="search"
          name="search"
          placeholder={t('str_ButtonSearch', { ns: '*' })}
          inputSize="small"
        />
        {searchTerm !== '' ? (
          <Button
            label={t('*:str_LabelClear')}
            hideLabel
            className="absolute right-sm"
            size="small"
            boxless
            buttonStyle="standard"
            startIcon={<CloseIcon />}
            corners="round"
            onClick={e => {
              cancelSearch();
            }}
          />
        ) : null}
      </div>
      {mode === ExperienceAssignMode.Product ? (
        <div className="flex relative ml-sm">{buildBrandSelectList()}</div>
      ) : null}
    </div>
  );
};
