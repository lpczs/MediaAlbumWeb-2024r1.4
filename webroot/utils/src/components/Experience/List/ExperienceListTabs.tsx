import React, { useEffect, useState } from 'react';
import { Button } from '@taopix/taopix-design-system';
import { useTranslation } from 'react-i18next';
import { ProductType } from '../../../Enums';
import { ProductTypeData } from '../../../types';
import classNames from 'classnames';

export interface ExperienceListTabsProps {
  productType: ProductTypeData;
  retroPrintsEnabled: boolean;
  onSetAssignProductType: Function
}

export const ExperienceListTabs = ({
  productType,
  retroPrintsEnabled,
  onSetAssignProductType,
  ...props
}: ExperienceListTabsProps) => {
  const { t } = useTranslation();

  const selectedButtonClasses = classNames('mb-[-1px]')

  return (
    <>
      {
       <div className="flex w-full justify-start space-x-xs p-sm border-b border-b-themeBorderColour sticky top-0 overflow-x-auto">
        <Button
          aria-pressed={productType.type === ProductType.Any}
          onClick={() => onSetAssignProductType({ type: ProductType.Any, retroPrint: false })}
          buttonStyle="standard"
          size={'small'}
          label={t('str_LabelAll', { ns: '*' })}
          className={productType.type === ProductType.Any && selectedButtonClasses}
        />
        <Button
          aria-pressed={productType.type === ProductType.PhotoBook && !productType.retroPrint}
          onClick={() => onSetAssignProductType({ type: ProductType.PhotoBook, retroPrint: false })}
          buttonStyle="standard"
          size={'small'}
          label={t('str_ProductTypePhotobooks', { ns: 'AdminExperience' })}
          className={productType.type === ProductType.PhotoBook && !productType.retroPrint && selectedButtonClasses}
        />
        <Button
          aria-pressed={productType.type === ProductType.Calendar && !productType.retroPrint}
          onClick={() => onSetAssignProductType({ type: ProductType.Calendar, retroPrint: false })}
          buttonStyle="standard"
          size={'small'}
          label={t('str_ProductTypeCalendars', { ns: 'AdminExperience' })}
          className={productType.type === ProductType.Calendar && !productType.retroPrint && selectedButtonClasses}
        />
        {retroPrintsEnabled ? (
          <Button
            aria-pressed={productType.type === ProductType.PhotoBook && productType.retroPrint}
            onClick={() => onSetAssignProductType({ type: ProductType.PhotoBook, retroPrint: true })}
            buttonStyle="standard"
            size={'small'}
            label={t('str_ProductTypeRetroPrints', { ns: 'AdminExperience' })}
            className={productType.type === ProductType.PhotoBook && productType.retroPrint && selectedButtonClasses}
          />
        ) : null}
    </div>
      }
    </>
  );
};
