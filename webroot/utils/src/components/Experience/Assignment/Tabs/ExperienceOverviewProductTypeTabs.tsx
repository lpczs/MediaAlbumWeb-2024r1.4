import React, { FormEventHandler } from 'react';
import { Button } from '@taopix/taopix-design-system';
import { useTranslation } from 'react-i18next';
import { ExperienceOverviewServerResponse, ProductTypeData } from '../../../../types';
import { ExperienceAssignMode, ProductType } from '../../../../Enums';
import { AssignModeAndSearch } from './AssignModeAndSearch';
import classNames from 'classnames';

export interface ExperienceOverviewProductTypeTabsProps {
  sessionRef: number;
  componentMountPoint: Element;
  setAssignProductType: Function;
  productType: ProductTypeData;
  retroPrintsEnabled: boolean;
  selectBrandKeyFilter: FormEventHandler<HTMLUListElement>;
  mode: ExperienceAssignMode;
  searchTerm: string;
  search: Function;
  cancelSearch: Function;
}

export const ExperienceOverviewProductTypeTabs = ({
  searchTerm,
  search,
  cancelSearch,
  mode,
  selectBrandKeyFilter,
  retroPrintsEnabled,
  setAssignProductType,
  productType,
  componentMountPoint,
  ...props
}: ExperienceOverviewProductTypeTabsProps) => {
  const { t } = useTranslation();

  const selectedButtonClasses = classNames('mb-[-1px]')

  return (
    <div className="flex self-end justify-start space-x-xs z-10">
        <Button
          aria-pressed={productType.type === ProductType.Any}
          onClick={() => setAssignProductType({ type: ProductType.Any, retroPrint: false })}
          buttonStyle="tab"
          tabPosition="top"
          size={'small'}
          label={t('str_LabelBrandsAndKeys', { ns: 'AdminExperience' })}
          className={productType.type === ProductType.Any && selectedButtonClasses}
        />
        <Button
          aria-pressed={productType.type === ProductType.PhotoBook && !productType.retroPrint}
          onClick={() => setAssignProductType({ type: ProductType.PhotoBook, retroPrint: false })}
          buttonStyle="tab"
          tabPosition="top"
          size={'small'}
          label={t('str_ProductTypePhotobooks', { ns: 'AdminExperience' })}
          className={productType.type === ProductType.PhotoBook && !productType.retroPrint && selectedButtonClasses}
        />
        <Button
          aria-pressed={productType.type === ProductType.Calendar && !productType.retroPrint}
          onClick={() => setAssignProductType({ type: ProductType.Calendar, retroPrint: false })}
          buttonStyle="tab"
          tabPosition="top"
          size={'small'}
          label={t('str_ProductTypeCalendars', { ns: 'AdminExperience' })}
          className={productType.type === ProductType.Calendar && !productType.retroPrint && selectedButtonClasses}
        />
        {retroPrintsEnabled ? (
          <Button
            aria-pressed={productType.type === ProductType.PhotoBook && productType.retroPrint}
            onClick={() => setAssignProductType({ type: ProductType.PhotoBook, retroPrint: true })}
            buttonStyle="tab"
            tabPosition="top"
            size={'small'}
            label={t('str_ProductTypeRetroPrints', { ns: 'AdminExperience' })}
            className={productType.type === ProductType.PhotoBook && productType.retroPrint && selectedButtonClasses}
          />
        ) : null}
        
      {/* <div className="flex flex-auto justify-end pb-sm">
        <AssignModeAndSearch
          selectBrandKeyFilter={selectBrandKeyFilter}
          brands={data !== null ? data.brands : {}}
          sessionRef={props.sessionRef}
          componentMountPoint={componentMountPoint}
          mode={mode}
          search={search}
          cancelSearch={cancelSearch}
          searchTerm={searchTerm}
        />
      </div> */}
    </div>
  );
};
