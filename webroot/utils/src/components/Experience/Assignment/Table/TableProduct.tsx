import React from 'react';
import { Button } from '@taopix/taopix-design-system';
import { useTranslation } from 'react-i18next';
import { AssignmentColumnData, ExperienceOverviewServerResponse } from '../../../../types';
import { getCurrentLocaleString } from '../../../../common';
import { TableRow } from './TableRow';
import { buttonSizeTypes } from '@taopix/taopix-design-system/dist/types/Components/Button/Button';
import classNames from 'classnames';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}

export interface TableProductProps {
  sessionRef: number;
  data: ExperienceOverviewServerResponse;
  selectItem: (isChecked: boolean, event: React.FormEvent) => void;
  defaultString: Function;
  isExpanded: Function;
  expandCollapseRow: Function;
  trackExpanded: Function;
  buildSettingsColumns: Function;
  brandAndKeyFilter: string;
  selectAll: Function;
  isSelected: Function;
  isSelectAllSelected: Function;
  assignmentColumnData: AssignmentColumnData;
  multiSelect: boolean;
  rowHeight?: buttonSizeTypes;
  loading: boolean;
}

export const TableProduct = ({
  multiSelect,
  assignmentColumnData,
  isSelectAllSelected,
  isSelected,
  selectAll,
  brandAndKeyFilter,
  defaultString,
  selectItem,
  trackExpanded,
  buildSettingsColumns,
  isExpanded,
  expandCollapseRow,
  data,
  rowHeight,
  loading,
  ...props
}: TableProductProps) => {
  const { t } = useTranslation();

  const showBrandKey = (displayKey: string): boolean => {
    let show = true;
    const [displayBrandCode, displayLicenseCode] = displayKey.split('.');

    if (brandAndKeyFilter !== '') {
      const [filterBrandCode, filterLicenseCode] = brandAndKeyFilter.split('.');
      if (
        ((filterLicenseCode !== displayLicenseCode || filterLicenseCode === '*') &&
          filterBrandCode !== displayBrandCode) ||
        (filterLicenseCode !== '*' && filterLicenseCode !== displayLicenseCode && filterBrandCode === displayBrandCode)
      ) {
        show = false;
      }
    }
    return show;
  };

  return (
    <React.Fragment>
      {data !== null ? (
        (data.hasOwnProperty('collections') && Object.entries(data.collections).length) > 0 ? (
          Object.entries(data.collections).map(([collectionKey, collection]) => {
            return (
              <React.Fragment key={collectionKey}>
                <div
                  className={
                    'flex h-xxxl border-b border-b-themeTextColour20 w-[max(100%,calc(var(--tableWidth)*1px))]'
                  }
                >
                  <div className={'sticky left-0 items-end flex'}>
                    <p className={'font-bold ml-sm mb-sm'}>
                      {collectionKey + ' - ' + getCurrentLocaleString(collection.collectionName as string, true)}
                    </p>
                  </div>
                </div>
                {Object.entries(collection.products).map(([productKey, product]) => {
                  return ( 
                    <React.Fragment key={productKey}>
                      <TableRow
                        multiSelect={false}
                        isSelectAllSelected={isSelectAllSelected}
                        isSelected={isSelected}
                        selectAll={selectAll}
                        checkParentExpanded={false}
                        enableExpandCollapse={true}
                        selectItem={selectItem}
                        isExpanded={isExpanded}
                        expandCollapseRow={expandCollapseRow}
                        trackExpanded={trackExpanded}
                        buildSettingsColumns={buildSettingsColumns}
                        isParent={true}
                        parentKey={collectionKey}
                        theKey={'*.*.' + collectionKey + '.' + productKey}
                        label={product.code + ' - ' + getCurrentLocaleString(product.name as string, true)}
                        rowHeight={rowHeight}
                      />
                      {
                        /* any brand any key row */
                        (isExpanded('*.*.' + collectionKey + '.' + productKey))
                        &&
                        <TableRow
                          multiSelect={multiSelect}
                          isSelectAllSelected={isSelectAllSelected}
                          isSelected={isSelected}
                          selectAll={selectAll}
                          checkParentExpanded={false}
                          enableExpandCollapse={false}
                          selectItem={selectItem}
                          isExpanded={isExpanded}
                          expandCollapseRow={expandCollapseRow}
                          trackExpanded={trackExpanded}
                          buildSettingsColumns={buildSettingsColumns}
                          isParent={false}
                          parentKey={collectionKey}
                          theKey={'*.*.' + collectionKey + '.' + productKey}
                          label={
                            t('str_LabelAnyBrand', { ns: 'AdminExperience' }) 
                            + ' - ' +
                            t('str_LabelAnyKey', { ns: 'AdminExperience' })
                          }
                          rowHeight={rowHeight}
                        />
                      }
                      {
                        (isExpanded('*.*.' + collectionKey + '.' + productKey))
                        &&
                        Object.entries(data.brands).map(([brandIndex, brand]) => {
                          return (
                            <React.Fragment key={brandIndex}>
                              {showBrandKey(brand.code + '.*') ? (
                                <TableRow
                                  multiSelect={multiSelect}
                                  isSelected={isSelected}
                                  selectItem={selectItem}
                                  isExpanded={isExpanded}
                                  expandCollapseRow={expandCollapseRow}
                                  trackExpanded={trackExpanded}
                                  buildSettingsColumns={buildSettingsColumns}
                                  parentKey={'*.*.' + collectionKey + '.' + productKey}
                                  theKey={brand.code + '.*.' + collectionKey + '.' + product.code}
                                  label={
                                    (brand.code === '' ? t('str_LabelDefault', { ns: '*' }) : brand.code) +
                                    ' - ' +
                                    t('str_LabelAnyKey', { ns: 'AdminExperience' })
                                  }
                                  rowHeight={rowHeight}
                                />
                              ) : null}
                              {Object.entries(brand.licenseKeys).map(([keyIndex, key]) => {
                                return (
                                  <React.Fragment key={keyIndex}>
                                    {showBrandKey(brand.code + '.' + key.code) ? (
                                      <TableRow
                                        multiSelect={multiSelect}
                                        isSelected={isSelected}
                                        selectItem={selectItem}
                                        isExpanded={isExpanded}
                                        expandCollapseRow={expandCollapseRow}
                                        trackExpanded={trackExpanded}
                                        buildSettingsColumns={buildSettingsColumns}
                                        parentKey={'*.*.' + collectionKey + '.' + productKey}
                                        theKey={brand.code + '.' + key.code + '.' + collectionKey + '.' + product.code}
                                        label={
                                          (brand.code === '' ? t('str_LabelDefault', { ns: '*' }) : brand.code) +
                                          ' - ' +
                                          key.code
                                        }
                                        rowHeight={rowHeight}
                                      />
                                    ) : null}
                                  </React.Fragment>
                                );
                              })}
                            </React.Fragment>
                          );
                        })}
                    </React.Fragment>
                  );
                })}
              </React.Fragment>
            );
          })
        ) : !loading && data.page === 1 ? (
          <div>
            <div>{t('str_MessageNoResultsFound', { ns: 'AdminExperience' })}</div>
          </div>
        ) : null
      ) : null}
    </React.Fragment>
  );
};
