import React, { useEffect, useRef } from 'react';
import { useTranslation } from 'react-i18next';
import { AssignmentColumn, AssignmentColumnData, ProductTypeData, Selections } from '../../../../types';
import { AssignmentColumnId, AssignmentType, ExperienceAssignMode, ProductType } from '../../../../Enums';
import { Button, CheckSmallIcon, Heading, Theme, ThemeName, getTheme } from '@taopix/taopix-design-system';
import NewColumnButton from '../Button/NewColumnButton';
import classNames from 'classnames';
import ColumnOptionsButton from '../Button/ColumnOptionsButton';

export interface TableHeadProps {
  sessionRef: number;
  assignmentColumnData: AssignmentColumnData;
  productType: ProductTypeData;
  mode: ExperienceAssignMode;
  buildAssignmentSelectList: Function;
  multiSelect: boolean;
  selections: Selections;
  applyExperience: Function;
  componentMountPoint: Element;
  onSetsAssignmentColumnData: Function;
  selectTemplate: Function;
  loading: boolean;
  isScrolled?: boolean;
  isCompressed?: boolean;
}

export const TableHead = ({
  loading,
  selectTemplate,
  onSetsAssignmentColumnData,
  componentMountPoint,
  applyExperience,
  selections,
  multiSelect,
  buildAssignmentSelectList,
  mode,
  productType,
  assignmentColumnData,
  isScrolled,
  isCompressed,
  ...props
}: TableHeadProps) => {
  const { t } = useTranslation();

  const onAssignExperience = (column: AssignmentColumn) => {
    applyExperience(column);
  };

  const tableHeadClasses = classNames(
    'sticky',
    'top-0',
    'z-10',
    'flex',
    'w-[max(100%,calc(var(--tableWidth)*1px))]' // Width is need to ensure sticky columns don't get scrolled out of view
  );
  const headingCellClasses = classNames(
    'flex',
    'items-end',
    'relative',
    'group/headerCell',
    'whitespace-nowrap',
    'border-y',
    'border-y-themeTextColour20',
    'first:border-l-0',
    'border-l border-l-themeTextColour20'
  );
  const nameHeadingCellClasses = classNames(
    headingCellClasses,
    'sticky',
    'left-0',
    'z-10',
    'p-sm',
    'basis-[500px]',
    'min-w-[260px]',
    'align-bottom',
    // Add a shadow to the right hand edge of the name heading when horizontally scrolled
    'after:hidden',
    '[.showFirstColumnShadow_&]:after:block',
    'after:w-sm',
    'after:h-full',
    'after:absolute',
    'after:left-full',
    'after:top-0',
    'after:bg-[linear-gradient(90deg,rgba(0,0,0,0.1),transparent)]',
    // Background colour is set on the cell because it can appear above other cells due to position sticky
    getTheme(ThemeName.Container, true, false, false)
  );
  const settingsHeaderClasses = classNames(headingCellClasses, 'p-sm', 'w-[260px]', 'shrink-0');
  const addColumnCellClasses = classNames(
    headingCellClasses,
    'flex-1',
    'sticky',
    'right-0',
    'p-sm',
    'items-end',
    'min-w-[160px]',
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

  const newColumnRef = useRef<HTMLDivElement>(null);

  const scrollToNewColumn = () => {
    if (newColumnRef.current) {
      newColumnRef.current.scrollIntoView({ behavior: 'smooth', inline: 'center' });
    }
  };

  useEffect(() => {
    setTimeout(() => {
      scrollToNewColumn();
    }, 500);
  }, [assignmentColumnData.recentlyAddedId]);

  return (
    <Theme name={ThemeName.Container} className={tableHeadClasses} allowBorder={false} allowCorners={false}>
      <div className={nameHeadingCellClasses}>
        <Heading className={'font-bold'} size={4}>
          {t('*:str_LabelName')}
        </Heading>
      </div>
      {assignmentColumnData.columns.map((column: AssignmentColumn) => {
        const selectedIndex = assignmentColumnData.selected.findIndex(columnID => column.id === columnID);

        if (selectedIndex === -1) {
          return;
        }

        let theSelections = { ...selections };

        if (column !== null) {
          theSelections.templates = selections.templates.filter(
            template =>
              template.type === column.type.subType &&
              template.productType === column.productType.type &&
              template.retroPrint === column.productType.retroPrint
          );
        }

        // Flash this column if it has just been added
        let flashColumnClass = column.id === assignmentColumnData.recentlyAddedId ? 'tds-animate-glow' : '';

        return (
          ((productType.type === column.productType.type && productType.retroPrint === column.productType.retroPrint) ||
            column.productType.type === ProductType.Any ||
            mode === ExperienceAssignMode.BrandAndKey) && (
            <Theme
              name={ThemeName.Container}
              key={[column.type.type, column.type.subType, column.productType.type, column.productType.retroPrint].join(
                '|'
              )}
              className={classNames(settingsHeaderClasses, flashColumnClass)}
              allowBorder={false}
              allowCorners={false}
              ref={column.id === assignmentColumnData.recentlyAddedId ? newColumnRef : undefined}
            >
              <div className={'flex flex-col space-y-xs w-[calc(100%-35px)]'}>
                <Heading className={'leading-normal mt-sm'} size={4}>
                  <span className={'font-bold'}>{column.label}</span>
                  {mode !== ExperienceAssignMode.Product && column.typeLabel && <span>{` (${column.typeLabel})`}</span>}
                </Heading>

                {multiSelect && (
                  <div className={'flex space-x-xs'}>
                    {buildAssignmentSelectList('', column.type, column.productType, false, 'small', 'applyToAllValue')}
                    <Button
                      disabled={theSelections.templates.length < 1 || selections.keys.length < 1}
                      onClick={e => onAssignExperience(column)}
                      label={t('str_LabelApply', { ns: 'AdminExperience' })}
                      hideLabel
                      startIcon={<CheckSmallIcon />}
                      size={'small'}
                      labelLines={1}
                    />
                  </div>
                )}
              </div>
              <ColumnOptionsButton
                columnId={column.id}
                componentMountPoint={componentMountPoint}
                onSetsAssignmentColumnData={onSetsAssignmentColumnData}
              />
            </Theme>
          )
        );
      })}
      <Theme name={ThemeName.Container} allowBorder={false} allowCorners={false} className={addColumnCellClasses}>
        {!loading ? (
          <>
            <NewColumnButton
              productType={productType}
              mode={mode}
              componentMountPoint={componentMountPoint}
              onAddColumn={onSetsAssignmentColumnData}
              assignmentColumnData={assignmentColumnData}
            />
          </>
        ) : null}
      </Theme>
    </Theme>
  );
};
