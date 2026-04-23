import React, { useEffect } from 'react';
import { AssignmentColumnId, ExperienceAssignMode, ExperienceType, ProductType } from '../../../../Enums';
import { useTranslation } from 'react-i18next';
import {
  AssignmentColumn,
  AssignmentColumnData,
  Brand,
  ExperienceOverviewServerResponse,
  ProductTypeData,
  TemplateSelect,
} from '../../../../types';
import { TableBrandAndKey } from './TableBrandAndKey';
import { TableProduct } from './TableProduct';
import classNames from 'classnames';
import { buttonSizeTypes } from '@taopix/taopix-design-system/dist/types/Components/Button/Button';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}

export interface TableBodyProps {
  componentMountPoint: Element;
  sessionRef: number;
  data: ExperienceOverviewServerResponse;
  selectItem: (isChecked: boolean, event: React.FormEvent) => void;
  mode: ExperienceAssignMode;
  productType: ProductTypeData;
  loading: boolean;
  searchTerm: string;
  onSetExpanded: Function;
  trackExpanded: Function;
  isExpanded: Function;
  brandAndKeyFilter: string;
  changeAndDisplayMessage: Function;
  closeMessage: Function;
  onDeleteAssignment: Function;
  selectAll: Function;
  isSelected: Function;
  isSelectAllSelected: Function;
  templateList: TemplateSelect[];
  assignmentColumnData: AssignmentColumnData;
  multiSelect: boolean;
  buildAssignmentSelectList: Function;
  saving: boolean;
  rowHeight?: buttonSizeTypes;
}

export const TableBody = ({
  componentMountPoint,
  saving,
  buildAssignmentSelectList,
  multiSelect,
  assignmentColumnData,
  templateList,
  isSelectAllSelected,
  isSelected,
  selectAll,
  onDeleteAssignment,
  closeMessage,
  changeAndDisplayMessage,
  brandAndKeyFilter,
  isExpanded,
  trackExpanded,
  onSetExpanded,
  searchTerm,
  loading,
  productType,
  mode,
  selectItem,
  data,
  rowHeight,
  ...props
}: TableBodyProps) => {
  const { t } = useTranslation();

  const expandAllBrands = (brands: Brand) => {
    let brandsToExpand = [];

    if (brands !== null) {
      for (const brandKey in brands) {
        brandsToExpand.push(brandKey + '.*.*.*');
      }
    }

    return brandsToExpand;
  };

  const defaultString = (type: ExperienceType) => {
    let defaultString = '';
    let typeString = '';

    if (productType.retroPrint) {
      typeString = 'RetroPrint';
    } else {
      typeString = ProductType.Calendar === productType.type ? 'Calendar' : 'Photobook';
    }

    switch (type) {
      case ExperienceType.SETTINGS:
        defaultString = t('str_LabelDefault' + typeString + 'ExperienceSettings', { ns: 'AdminExperience' });
        break;

      case ExperienceType.WIZARD:
        defaultString = t('str_LabelDefault' + typeString + 'ExperienceAssistant', { ns: 'AdminExperience' });
        break;

      case ExperienceType.EDITOR:
        defaultString = t('str_LabelDefault' + typeString + 'ExperienceEditor', { ns: 'AdminExperience' });
        break;

      default:
        break;
    }

    return defaultString;
  };

  const expandCollapseRow = (e: any, parentKey: string) => {
    const rowElements: NodeListOf<Element> = componentMountPoint.querySelectorAll(
      "[data-parentkey='" + parentKey + "']"
    );
    const collapseClass = 'hidden';

    for (let index = 0; index < rowElements.length; index++) {
      const element = rowElements[index];
      if (element.classList.contains(collapseClass)) {
        element.classList.remove(collapseClass);
      } else {
        element.classList.add(collapseClass);
      }
    }
  };

  useEffect(() => {
    //First Time in Load data
    if (ExperienceAssignMode.BrandAndKey === mode && data !== null && searchTerm !== '') {
      onSetExpanded(expandAllBrands(data.brands));
    }
  }, [data]);

  const buildSettingsColumns = (theKey: string, isParent?: boolean) => {
    return assignmentColumnData.columns.map((column: AssignmentColumn) => {
      const selectedIndex = assignmentColumnData.selected.findIndex(columnID => column.id === columnID);

      if (selectedIndex === -1) {
        return;
      }

      const settingColClasses = classNames(
        'border-b',
        'border-b-themeTextColour20',
        'first:border-l-0',
        'border-l border-l-themeTextColour20',
        'w-[260px]',
        'shrink-0',
        isParent && 'cursor-pointer'
      );

      return (productType.type === column.productType.type &&
        productType.retroPrint === column.productType.retroPrint) ||
        column.productType.type === ProductType.Any ||
        mode === ExperienceAssignMode.BrandAndKey ? (
        <div
          key={[
            theKey,
            column.type.type,
            column.type.subType,
            column.productType.type,
            column.productType.retroPrint,
          ].join('|')}
          className={settingColClasses}
          onClick={isParent ? () => trackExpanded(theKey) : undefined}
        >
          {/* isParent signifies this is the product row which we are no longer showing settings against  */}
          {(!isParent) && buildAssignmentSelectList(theKey, column.type, column.productType, false, rowHeight)}
        </div>
      ) : null;
    });
  };

  return (
    <div className="flex flex-col">
      {ExperienceAssignMode.BrandAndKey === mode ? (
        <TableBrandAndKey
          multiSelect={multiSelect}
          assignmentColumnData={assignmentColumnData}
          isSelected={isSelected}
          searchTerm={searchTerm}
          buildSettingsColumns={buildSettingsColumns}
          trackExpanded={trackExpanded}
          isExpanded={isExpanded}
          expandCollapseRow={expandCollapseRow}
          sessionRef={props.sessionRef}
          data={data}
          selectItem={selectItem}
          defaultString={defaultString}
          productType={productType}
          rowHeight={rowHeight}
        />
      ) : (
        <TableProduct
          multiSelect={multiSelect}
          assignmentColumnData={assignmentColumnData}
          isSelectAllSelected={isSelectAllSelected}
          isSelected={isSelected}
          selectAll={selectAll}
          brandAndKeyFilter={brandAndKeyFilter}
          buildSettingsColumns={buildSettingsColumns}
          trackExpanded={trackExpanded}
          isExpanded={isExpanded}
          expandCollapseRow={expandCollapseRow}
          sessionRef={props.sessionRef}
          data={data}
          selectItem={selectItem}
          defaultString={defaultString}
          rowHeight={rowHeight}
          loading={loading}
        />
      )}
    </div>
  );
};
