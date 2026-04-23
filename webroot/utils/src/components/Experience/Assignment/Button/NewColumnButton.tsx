import React, { useEffect, useRef, useState } from 'react';
import { PopOut, Vertical, Horizontal, List, ListItem, Button, PlusIcon } from '@taopix/taopix-design-system';
import { t } from 'i18next';
import { useTranslation } from 'react-i18next';
import { AssignmentColumn, AssignmentColumnData, ProductTypeData } from '../../../../types';
import { ExperienceAssignMode, ProductType } from '../../../../Enums';

type NewColumnButtonProps = {
  onAddColumn: Function;
  componentMountPoint: Element;
  assignmentColumnData: AssignmentColumnData;
  productType: ProductTypeData;
  mode: ExperienceAssignMode;
};

const NewColumnButton = ({
  productType,
  mode,
  assignmentColumnData,
  componentMountPoint,
  onAddColumn,
  ...props
}: NewColumnButtonProps) => {
  const { t } = useTranslation();
  const [popOutOpen, setpPopOutOpen] = useState(false);
  const [selectListColumns, setSelectListColumns] = useState<AssignmentColumn[]>([]);

  const buildSelectListColumns = (assignmentColumnData: AssignmentColumnData) => {
    let returnArray: AssignmentColumn[] = [];
    for (const column of assignmentColumnData.columns) {
      //if not already displayed
      if (
        !assignmentColumnData.selected.includes(column.id) &&
        ((productType.type === column.productType.type && productType.retroPrint === column.productType.retroPrint) ||
          column.productType.type === ProductType.Any ||
          mode === ExperienceAssignMode.BrandAndKey)
      ) {
        returnArray.push(column);
      }
    }
    return returnArray;
  };

  const toggleColumnList = () => {
    setpPopOutOpen(!popOutOpen);
  };

  const addAllColumns = () => {
    let arrayOfIds = [];
    for (const column of selectListColumns) {
      arrayOfIds.push(column.id);
    }
    onAddColumn(arrayOfIds);
  };

  const typeRef = useRef(null);

  useEffect(() => {
    //First Time in Load data
    setSelectListColumns(buildSelectListColumns(assignmentColumnData));
  }, [assignmentColumnData]);

  return (
    <>
      <Button
        label={t('AdminExperience:str_ButtonAddAColumn')}
        id="addColumnButton"
        onClick={() => {
          toggleColumnList();
        }}
        startIcon={<PlusIcon />}
        size="small"
        disabled={selectListColumns.length === 0}
      />
      <PopOut
        id={'addColumnDropDown'}
        className={'flex-col min-w-[var(--anchorElementWidth)]'}
        open={popOutOpen}
        contentRef={typeRef}
        anchorId={'addColumnButton'}
        anchorOrigin={{ vertical: Vertical.Bottom, horizontal: Horizontal.Left }}
        transformOrigin={{ vertical: Vertical.Top, horizontal: Horizontal.Left }}
        onClickOutside={toggleColumnList}
        shadowRoot={componentMountPoint}
        
      >
        <List>
          {selectListColumns.map(column => {
            const typeLabel = mode === ExperienceAssignMode.BrandAndKey && column.typeLabel ? ` (${column.typeLabel})` : '';
            const label = column.label + typeLabel;
            return (
              <ListItem key={'listItem-' + column.id}>
                <Button
                  label={label}
                  corners={'square'}
                  labelAlignment={'left'}
                  buttonStyle={'standard'}
                  onClick={e => {
                    onAddColumn([column.id]);
                    toggleColumnList();
                  }}
                  key={column.id}
                  id={'columnType-' + column.id}
                />
              </ListItem>
            );
          })}
        </List>
      </PopOut>
    </>
  );
};

export default NewColumnButton;
