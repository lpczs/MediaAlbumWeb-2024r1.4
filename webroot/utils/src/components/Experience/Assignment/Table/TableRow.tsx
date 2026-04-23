import React, { useEffect } from 'react';
import { Button, Checkbox, ChevronDownSmallIcon, ChevronRightSmallIcon } from '@taopix/taopix-design-system';
import { useTranslation } from 'react-i18next';
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

export interface TableRowProps {
  selectItem: (isChecked: boolean, event: React.FormEvent) => void;
  isExpanded: Function;
  expandCollapseRow: Function;
  trackExpanded: Function;
  buildSettingsColumns: Function;
  parentKey: string;
  theKey: string;
  label: string;
  enableExpandCollapse?: boolean;
  checkParentExpanded?: boolean;
  isParent?: boolean;
  selectAll?: Function;
  isSelected: Function;
  isSelectAllSelected?: Function;
  multiSelect: boolean;
  rowHeight?: buttonSizeTypes;
}

export const TableRow = ({
  multiSelect,
  isSelectAllSelected = () => {},
  isSelected,
  selectAll = () => {},
  isParent = false,
  enableExpandCollapse = false,
  checkParentExpanded = true,
  parentKey,
  theKey,
  label,
  selectItem,
  trackExpanded,
  buildSettingsColumns,
  isExpanded,
  expandCollapseRow,
  rowHeight,
  ...props
}: TableRowProps) => {
  const { t } = useTranslation();
  const isChecked = isSelected(theKey);
  const rowHeightClass = rowHeight === 'small' ? 'h-xl' : rowHeight === 'medium' ? 'h-xxl' : 'h-xxxl';

  const trElement = (isChecked: boolean, children: any) => {
    const rowClasses = classNames(
      rowHeightClass,
      'flex',
      'group/row',

      // Width is need to ensure sticky columns don't get scrolled out of view,
      'w-[max(100%,calc(var(--tableWidth)*1px))]',

      // Background colours
      isChecked && !isParent
        ? 'bg-themeAccentColour20solid'
        : [isParent ? 'bg-white' : 'bg-[#f6f6f6]', 'hover:bg-themeAccentColour10solid']

      // Hide this row if it is a child and the parent is not expanded
      //!isParent && !isExpanded(parentKey) && checkParentExpanded && 'hidden'
    );

    return (
      <div {...(isParent && { 'data-parentkey': parentKey })} className={rowClasses}>
        {children}
      </div>
    );
  };

  const cellClasses = classNames(
    'border-b',
    'border-b-themeTextColour20',
    'first:border-l-0',
    'border-l border-l-themeTextColour20'
  );
  const nameColClasses = classNames(
    cellClasses,
    'flex',
    'sticky',
    'left-0',
    isParent ? 'cursor-pointer' : 'pl-[55px]',
    // Background colour is set on the cell because it can appear above other cells due to position sticky
    isChecked && !isParent
      ? 'bg-themeAccentColour20solid'
      : [isParent ? 'bg-white' : 'bg-[#f6f6f6]', 'group-hover/row:bg-themeAccentColour10solid'],
    'basis-[500px]',
    'min-w-[260px]',
    'items-center',
    'space-x-xs',
    // Add a shadow to the right hand edge of the name heading when horizontally scrolled
    'after:hidden',
    '[.showFirstColumnShadow_&]:after:block',
    'after:w-sm',
    'after:h-full',
    'after:absolute',
    'after:left-full',
    'after:top-0',
    'after:bg-[linear-gradient(90deg,rgba(0,0,0,0.1),transparent)]'
  );
  const selectAllRowClasses = classNames(
    'flex',
    'w-[max(100%,calc(var(--tableWidth)*1px))]',
    'bg-[#f6f6f6]',
    'items-center',
    'border-b',
    'border-b-themeTextColour20',
    rowHeightClass
  );
  const addColumnCellClasses = classNames(
    cellClasses,
    'flex-1',
    'sticky',
    'right-0',
    'min-w-[160px]',
    isParent && 'cursor-pointer',
    // Background colour is set on the cell because it can appear above other cells due to position sticky
    isChecked && !isParent
      ? 'bg-themeAccentColour20solid'
      : [isParent ? 'bg-white' : 'bg-[#f6f6f6]', 'group-hover/row:bg-themeAccentColour10solid'],
    'border-l',
    'border-l-themeBorderColour',
    // Add a shadow to the left hand edge of the Add Column cell when the table is too narrow for all of its comlumns
    'before:hidden',
    '[.showLastColumnShadow_&]:before:block',
    'before:w-sm',
    'before:h-full',
    'before:absolute',
    'before:right-full',
    'before:top-0',
    'before:bg-[linear-gradient(90deg,transparent,rgba(0,0,0,0.1))]'
  );

  const nameTextClasses = classNames(
    rowHeight === 'small' ? 'text-sm' : 'text-md',
    'overflow-hidden',
    'overflow-ellipsis',
    'whitespace-nowrap'
  );

  return (
    <>
      {
        /* Select all option to appear first if multi select in use */
        theKey.slice(0, 3) === '*.*' && multiSelect && isExpanded(theKey) && !isParent ? (
          <div className={selectAllRowClasses}>
            <div className={'sticky left-0 pl-[55px]'}>
              <Checkbox
                label={t('str_LabelSelectAll', { ns: '*' })}
                textOverflow={'ellipsis'}
                onChange={e => {
                  selectAll(theKey, !isSelectAllSelected(theKey));
                }}
                id={'selectAllCheckbox-' + theKey}
              />
            </div>
          </div>
        ) : null
      }
      {trElement(
        isChecked,
        <>
          <div className={nameColClasses} onClick={isParent ? () => trackExpanded(theKey) : undefined}>
            {enableExpandCollapse ? (
              <Button
                onClick={e => {
                  trackExpanded(theKey);
                  //expandCollapseRow(e, theKey);
                }}
                startIcon={isExpanded(theKey) ? <ChevronDownSmallIcon /> : <ChevronRightSmallIcon />}
                buttonStyle="standard"
                size={rowHeight}
                corners={'square'}
                hideLabel
              />
            ) : (
              <></>
            )}
            {multiSelect ? (
              <Checkbox
                onChange={selectItem}
                label={label}
                textOverflow={'ellipsis'}
                id={theKey}
                defaultChecked={isChecked}
                key={parentKey + '_' + theKey + '_' + isChecked + '_' + crypto.randomUUID()}
              />
            ) : (
              <p className={nameTextClasses}>{label}</p>
            )}
          </div>
          {buildSettingsColumns(theKey, isParent)}
          <div className={addColumnCellClasses} onClick={isParent ? () => trackExpanded(theKey) : undefined}>
            {/* Empty cell for the plus column */}
          </div>
        </>
      )}
    </>
  );
};
