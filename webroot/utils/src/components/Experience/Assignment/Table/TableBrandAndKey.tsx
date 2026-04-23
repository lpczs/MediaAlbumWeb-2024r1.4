import React from 'react';
import { useTranslation } from 'react-i18next';
import {
  AssignmentColumn,
  AssignmentColumnData,
  Brand,
  ExperienceOverviewServerResponse,
  ProductTypeData,
} from '../../../../types';
import { TableRow } from './TableRow';
import { buttonSizeTypes } from '@taopix/taopix-design-system/dist/types/Components/Button/Button';
import { AssignmentColumnId } from '../../../../Enums';
import classNames from 'classnames';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}

export interface TableBrandAndKeyProps {
  sessionRef: number;
  data: ExperienceOverviewServerResponse;
  selectItem: (isChecked: boolean, event: React.FormEvent) => void;
  defaultString: Function;
  trackExpanded: Function;
  isExpanded: Function;
  expandCollapseRow: Function;
  buildSettingsColumns: Function;
  searchTerm: string;
  isSelected: Function;
  productType: ProductTypeData;
  assignmentColumnData: AssignmentColumnData;
  multiSelect: boolean;
  rowHeight?: buttonSizeTypes;
}

export const TableBrandAndKey = ({
  multiSelect,
  assignmentColumnData,
  productType,
  isSelected,
  searchTerm,
  defaultString,
  selectItem,
  buildSettingsColumns,
  trackExpanded,
  isExpanded,
  expandCollapseRow,
  data,
  rowHeight,
  ...props
}: TableBrandAndKeyProps) => {
  const { t } = useTranslation();

  const noKeysMessageRowClasses = classNames(
    'flex',
    'items-center',
    'italic',
    'min-w-[260px]',
    'bg-[#f6f6f6]',
    'w-[max(100%,calc(var(--tableWidth)*1px))]',
    rowHeight == 'small' ? 'h-xxl' : 'h-xxxl'
  );

  const noKeysMessageClasses = classNames('sticky', 'left-0', 'pl-[55px]');

  return (
    <React.Fragment>
      {Object.entries(data.brands).length === 0 ? (
        <div key={'*.*.*.*'} className={'flex'}>
          <div>
            {t('str_MessageNoResultsFound', { ns: 'AdminExperience' })}
          </div>
        </div>
      ) : (
        Object.entries(data.brands)
          .sort(([i, a]: any, [i2, b]: any) => {
            return a.code.localeCompare(b.code);
          })
          .map(([brandKey, brand]) => {
            return (
              <React.Fragment key={brandKey}>
                <TableRow
                  multiSelect={false}
                  isSelected={isSelected}
                  isParent={true}
                  enableExpandCollapse={true}
                  selectItem={selectItem}
                  isExpanded={isExpanded}
                  expandCollapseRow={expandCollapseRow}
                  trackExpanded={trackExpanded}
                  buildSettingsColumns={buildSettingsColumns}
                  parentKey={brandKey}
                  theKey={brand.code + '.*.*.*'}
                  label={(brand.code === '' ? t('str_LabelDefault', { ns: '*' }) : brand.code) + ' - ' + brand.name}
                  rowHeight={rowHeight}
                />
                {
                  /* any key row */
                  (isExpanded(brand.code + '.*.*.*'))
                  &&
                  <TableRow
                    multiSelect={multiSelect}
                    isSelected={isSelected}
                    isParent={false}
                    enableExpandCollapse={false}
                    selectItem={selectItem}
                    isExpanded={isExpanded}
                    expandCollapseRow={expandCollapseRow}
                    trackExpanded={trackExpanded}
                    buildSettingsColumns={buildSettingsColumns}
                    parentKey={brandKey}
                    theKey={brand.code + '.*.*.*'}
                    label={t('str_LabelAnyKey', { ns: 'AdminExperience' })}
                    rowHeight={rowHeight}
                  />
                }
                {
                  (isExpanded(brand.code + '.*.*.*'))
                    &&
                    Object.entries(brand.licenseKeys).length === 0 ? (
                    <div
                      key={brand.code + '.*.*.*'}
                      data-parentkey={brandKey}
                      className={classNames(noKeysMessageRowClasses, isExpanded(brand.code + '.*.*.*') ? '' : 'hidden')}
                    >
                      <p className={noKeysMessageClasses}>{t('str_MessageNoKeys', { ns: 'AdminExperience' })}</p>
                    </div>
                  ) : 
                  (isExpanded(brand.code + '.*.*.*'))
                    &&
                    Object.entries(brand.licenseKeys).map(([keyIndex, key]) => {
                      return (
                        <TableRow
                          multiSelect={multiSelect}
                          key={brand.code + '.' + key.code + '.*.*'}
                          isSelected={isSelected}
                          selectItem={selectItem}
                          isExpanded={isExpanded}
                          expandCollapseRow={expandCollapseRow}
                          trackExpanded={trackExpanded}
                          buildSettingsColumns={buildSettingsColumns}
                          parentKey={brand.code + '.*.*.*'}
                          theKey={brand.code + '.' + key.code + '.*.*'}
                          label={key.code + ' - ' + key.name}
                          rowHeight={rowHeight}
                        />
                      );
                    })
                  }
              </React.Fragment>
            );
          })
      )}
    </React.Fragment>
  );
};
