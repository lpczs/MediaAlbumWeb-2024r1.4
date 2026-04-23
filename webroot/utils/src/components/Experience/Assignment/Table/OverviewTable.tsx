import React, { CSSProperties, useCallback, useEffect, useLayoutEffect, useMemo, useRef, useState } from 'react';
import {
  AssignmentColumnId,
  AssignmentType,
  ExperienceAssignMode,
  ExperienceType,
  ProductType,
} from '../../../../Enums';
import { useTranslation } from 'react-i18next';
import {
  AssignmentColumn,
  AssignmentColumnData,
  AssignmentTypeData,
  ExperienceOverviewServerResponse,
  ProductTypeData,
  Selections,
  TemplateSelect,
} from '../../../../types';
import { TableBody } from './TableBody';
import { TableHead } from './TableHead';
import { ExperienceTemplateAssignList } from '../../List/ExperienceTemplateAssignList';
import { LoadingLosenge } from '@taopix/taopix-design-system';
import { EditMultipleButton } from '../Button/EditMultipleButton';
import classNames from 'classnames';
import { buttonSizeTypes } from '@taopix/taopix-design-system/dist/types/Components/Button/Button';
import axios, { AxiosResponse } from 'axios';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}

export interface OverviewTableProps {
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
  closeMessage: Function;
  changeAndDisplayMessage: Function;
  deleteAssignment: Function;
  selectAll: Function;
  isSelected: Function;
  isSelectAllSelected: Function;
  templateList: TemplateSelect[];
  componentMountPoint: Element;
  applyExperience: Function;
  selections: Selections;
  selectTemplate: Function;
  saving: boolean;
  multiSelect?: boolean;
  rowHeight?: buttonSizeTypes;
  userId: string;
  userColumnData: AssignmentColumnId[];
}

export const OverviewTable = ({
  saving,
  selectTemplate,
  selections,
  applyExperience,
  componentMountPoint,
  templateList,
  isSelectAllSelected,
  isSelected,
  selectAll,
  deleteAssignment,
  closeMessage,
  changeAndDisplayMessage,
  brandAndKeyFilter,
  trackExpanded,
  isExpanded,
  onSetExpanded,
  searchTerm,
  loading,
  productType,
  mode,
  selectItem,
  data,
  multiSelect,
  rowHeight = 'medium',
  userColumnData,
  ...props
}: OverviewTableProps) => {
  const { t } = useTranslation();
  const [showFirstColumnShadow, setShowFirstColumnShadow] = useState<boolean>(false);
  const [showLastColumnShadow, setShowLastColumnShadow] = useState<boolean>(false);
  const [tableWidth, setTableWidth] = useState<number>(0);
  const nameColumnWidth = 260;
  const settingsColumnWidth = 260;
  const addButtonColumnWidth = 160;

  const getDisplayColumns = (): AssignmentColumn[] => {
    return [
      {
        id: AssignmentColumnId.PhotobookDesignAssistant,
        label: t('str_LabelDesignAssistant', { ns: 'AdminExperience' }),
        typeLabel: t('str_ProductTypePhotobooks', { ns: 'AdminExperience' }),
        productType: { type: ProductType.PhotoBook, retroPrint: false },
        type: { type: AssignmentType.Experience, subType: ExperienceType.WIZARD },
      },
      {
        id: AssignmentColumnId.CalendarDesignAssistant,
        label: t('str_LabelDesignAssistant', { ns: 'AdminExperience' }),
        typeLabel: t('str_ProductTypeCalendars', { ns: 'AdminExperience' }),
        productType: { type: ProductType.Calendar, retroPrint: false },
        type: { type: AssignmentType.Experience, subType: ExperienceType.WIZARD },
      },
      {
        id: AssignmentColumnId.RetroPrintDesignAssistant,
        label: t('str_LabelDesignAssistant', { ns: 'AdminExperience' }),
        typeLabel: t('str_ProductTypeRetroPrints', { ns: 'AdminExperience' }),
        productType: { type: ProductType.PhotoBook, retroPrint: true },
        type: { type: AssignmentType.Experience, subType: ExperienceType.WIZARD },
      },
      {
        id: AssignmentColumnId.PhotobookEditor,
        label: t('str_LabelEditor', { ns: 'AdminExperience' }),
        typeLabel: t('str_ProductTypePhotobooks', { ns: 'AdminExperience' }),
        productType: { type: ProductType.PhotoBook, retroPrint: false },
        type: { type: AssignmentType.Experience, subType: ExperienceType.EDITOR },
      },
      {
        id: AssignmentColumnId.CalendarEditor,
        label: t('str_LabelEditor', { ns: 'AdminExperience' }),
        typeLabel: t('str_ProductTypeCalendars', { ns: 'AdminExperience' }),
        productType: { type: ProductType.Calendar, retroPrint: false },
        type: { type: AssignmentType.Experience, subType: ExperienceType.EDITOR },
      },
      {
        id: AssignmentColumnId.RetroPrintEditor,
        label: t('str_LabelEditor', { ns: 'AdminExperience' }),
        typeLabel: t('str_ProductTypeRetroPrints', { ns: 'AdminExperience' }),
        productType: { type: ProductType.PhotoBook, retroPrint: true },
        type: { type: AssignmentType.Experience, subType: ExperienceType.EDITOR },
      },
      {
        id: AssignmentColumnId.Settings,
        label: t('str_LabelExperienceSettings', { ns: 'AdminExperience' }),
        productType: { type: ProductType.Any, retroPrint: false },
        type: { type: AssignmentType.Experience, subType: ExperienceType.SETTINGS },
      },
      {
        id: AssignmentColumnId.Theme,
        label: t('str_LabelUITheme', { ns: 'AdminExperience' }),
        productType: { type: ProductType.Any, retroPrint: false },
        type: { type: AssignmentType.Theme, subType: 0 },
      },
    ];
  };

  const [displaySelectList, setDisplaySelectList] = useState<
    Array<{ theKey: string; type: ExperienceType; productType: ProductTypeData }>
  >([]);
  const [assignmentColumnData, setAssignmentColumnData] = useState<AssignmentColumnData>({
    columns: getDisplayColumns(),
    selected: [...userColumnData],
  });
  const [individualSaving, setIndividualSaving] = useState<Boolean>(false);

  const displayAsSelect = (selected: { theKey: string; type: ExperienceType; productType: ProductTypeData }) => {
    return displaySelectList.findIndex(
      x =>
        x.theKey === selected.theKey &&
        x.type === selected.type &&
        x.productType.type === selected.productType.type &&
        x.productType.retroPrint === selected.productType.retroPrint
    );
  };

  const onSelectTemplate = (assignmentType: AssignmentType, template: Object) => {
    selectTemplate(assignmentType, template);
  };

  const onSetDisplaySelectList = (select: [{ theKey: string; type: ExperienceType; productType: ProductTypeData }]) => {
    setDisplaySelectList(select);
  };

  const onApplyExperience = (e: any, selection: Selections) => {
    setIndividualSaving(true);
    applyExperience(null, selection, false, () => {
      setDisplaySelectList([]);
      setIndividualSaving(false);
    });
  };

  const onDeleteAssignment = (
    theKey: string,
    assignmentType: AssignmentType,
    select: [{ theKey: string; type: ExperienceType; productType: ProductTypeData }]
  ) => {
    setIndividualSaving(true);
    setDisplaySelectList(select);
    deleteAssignment(theKey, assignmentType, () => {
      setIndividualSaving(false);
      setDisplaySelectList([]);
    });
  };

  const onSetsAssignmentColumnData = (selected: AssignmentColumnId[], removed: AssignmentColumnId[] = []) => {
    const cloneAssignmentColumnData = { ...assignmentColumnData };

    for (const columnId of selected) {
      cloneAssignmentColumnData.selected.push(columnId);
      cloneAssignmentColumnData.recentlyAddedId = columnId;
    }

    for (const columnId of removed) {
      const index = cloneAssignmentColumnData.selected.indexOf(columnId);
      if (index > -1) {
        cloneAssignmentColumnData.selected.splice(index, 1);
      }
      cloneAssignmentColumnData.recentlyAddedId = null;
    }

    setAssignmentColumnData(cloneAssignmentColumnData);
    saveAssignmentColumnDataToDB(cloneAssignmentColumnData.selected);
  };

  const saveAssignmentColumnDataToDB = (assignmentColumnData: AssignmentColumnId[]) => {
    let formParams = new FormData();
    formParams.append('ref', props.sessionRef.toString());
    formParams.append('userId', props.userId.toString());
    formParams.append('data', JSON.stringify(assignmentColumnData));

    axios.post('/api/experience/saveAssignmentColumnDataToDB', formParams).catch((error: any) => {
      console.log(error);
    });
  };

  const buildAssignmentSelectList = (
    theKey: string,
    typeData: AssignmentTypeData,
    productType: ProductTypeData,
    disabled: boolean = false,
    size: buttonSizeTypes = 'medium',
    id: string
  ) => {
    return (
      <ExperienceTemplateAssignList
        id={id}
        componentMountPoint={componentMountPoint}
        theKey={theKey}
        typeData={typeData}
        onApplyExperience={onApplyExperience}
        onSelectTemplate={onSelectTemplate}
        mode={mode}
        displayAsSelect={displayAsSelect}
        individualSaving={individualSaving}
        onSetDisplaySelectList={onSetDisplaySelectList}
        changeAndDisplayMessage={changeAndDisplayMessage}
        onDeleteAssignment={onDeleteAssignment}
        closeMessage={closeMessage}
        productType={productType}
        templateList={templateList}
        data={data}
        disabled={disabled}
        rowHeight={size}
      />
    );
  };

  const tableContainerClasses = classNames(
    'flex-1',
    'overflow-auto',
    showFirstColumnShadow && 'showFirstColumnShadow',
    showLastColumnShadow && 'showLastColumnShadow'
  );
  const tableClasses = classNames(
    'flex',
    'flex-col',
    'border-b',
    'border-b-gray-200',
    'w-[max(100%,calc(var(--tableWidth)*1px))]',
    '[&_td]:p-0'
  );
  const scrollContainerRef = useRef<HTMLDivElement>(null);
  const tableRef = useRef<HTMLDivElement>(null);

  let updateShadowsTimeout: ReturnType<typeof setTimeout>;

  const settingsColumnCount = useMemo((): number => {
    if (productType.type === -1) {
      // If we're currently on the brand/key tab, all selected columns are valid
      return assignmentColumnData.selected.length;
    } else {
      // Otherwise only return the number of selected columns that are relevant to the current tab
      let validColumns = assignmentColumnData.columns.filter(column => {
        return (
          assignmentColumnData.selected.includes(column.id) &&
          ((column.productType.type === productType.type && column.productType.retroPrint === productType.retroPrint) ||
            column.productType.type === ProductType.Any ||
            mode === ExperienceAssignMode.BrandAndKey)
        );
      });

      return validColumns.length;
    }
  }, [assignmentColumnData]);

  const updateTableProperties = () => {
    const tableContainerScrollDistance =
      scrollContainerRef.current.scrollWidth - scrollContainerRef.current.getBoundingClientRect().width;
    const tableContainerScrollLeft = scrollContainerRef.current.scrollLeft;

    // If the table is scrolled horizontally, show the shadow on the first column
    setShowFirstColumnShadow(tableContainerScrollLeft > 0);

    // If the table isn't scrolled all the way to the right, show the shadow on the last column
    setShowLastColumnShadow(tableContainerScrollLeft < tableContainerScrollDistance);
  };

  useEffect(() => {
    // Add event listener to check the table size on resize
    window.addEventListener('resize', () => {
      clearTimeout(updateShadowsTimeout);
      updateShadowsTimeout = setTimeout(() => updateTableProperties(), 500);
    });

    // Remove the event listener when the component unmounts
    return () => {
      window.removeEventListener('resize', updateTableProperties);
    };
  }, []);

  useEffect(() => {
    //If we're switching tabs reset the selections to prevent select lists opening
    onSetDisplaySelectList([{ theKey: '', type: 0, productType: { type: -1, retroPrint: false } }]);

    //If we're switching tabs remove the recentlyaddedid for the column
    if (assignmentColumnData.recentlyAddedId) {
      const cloneAssignmentColumnData = { ...assignmentColumnData };
      cloneAssignmentColumnData.recentlyAddedId = null;
      setAssignmentColumnData(cloneAssignmentColumnData);
    }

  }, [mode]);

  useEffect(() => {
    //when search is performed we can  remove the recentlyAddedId
    if (assignmentColumnData.recentlyAddedId) {
      const cloneAssignmentColumnData = { ...assignmentColumnData };
      cloneAssignmentColumnData.recentlyAddedId = null;
      setAssignmentColumnData(cloneAssignmentColumnData);
    }
  }, [searchTerm, brandAndKeyFilter]);

  useEffect(() => {
    const cloneAssignmentColumnData = { ...assignmentColumnData };
    cloneAssignmentColumnData.selected = [...userColumnData];
    setAssignmentColumnData(cloneAssignmentColumnData);
  }, [userColumnData]);

  useEffect(() => {
    // When columns are added or removed, or when changing tabs...

    // Update the tableWidth css variable for use by the header and rows
    const allSettingsColumnsWidth = settingsColumnCount * settingsColumnWidth;
    const tableWidth = nameColumnWidth + allSettingsColumnsWidth + addButtonColumnWidth;
    setTableWidth(tableWidth);

    // Add or remove the column shadows based on the table width
    updateTableProperties();
  }, [assignmentColumnData, mode]);

  useEffect(() => {
    // Reset the scroll position of the table container and remove the column shadows so they aren't visible while loading
    scrollContainerRef.current.scrollTo(0, 0);
    setShowFirstColumnShadow(false);
    setShowLastColumnShadow(false);
  }, [loading]);

  return (
    <div
      className={tableContainerClasses}
      ref={scrollContainerRef}
      id={'tableContainer'}
      onScroll={updateTableProperties}
      style={{ '--tableWidth': tableWidth } as CSSProperties}
    >
      {loading || saving || data === null ? (
        <div className={'m-sm'}>
          <LoadingLosenge label={t('str_Message' + (saving ? 'Saving' : 'Loading'), { ns: '*' })} />
        </div>
      ) : (
        <div>
          <div className={tableClasses} ref={tableRef}>
            <TableHead
              selectTemplate={selectTemplate}
              onSetsAssignmentColumnData={onSetsAssignmentColumnData}
              componentMountPoint={componentMountPoint}
              applyExperience={applyExperience}
              selections={selections}
              multiSelect={multiSelect}
              buildAssignmentSelectList={buildAssignmentSelectList}
              mode={mode}
              productType={productType}
              assignmentColumnData={assignmentColumnData}
              sessionRef={props.sessionRef}
              loading={loading || data === null}
            />
            <TableBody
              componentMountPoint={componentMountPoint}
              saving={saving}
              buildAssignmentSelectList={buildAssignmentSelectList}
              multiSelect={multiSelect}
              assignmentColumnData={assignmentColumnData}
              templateList={templateList}
              isSelectAllSelected={isSelectAllSelected}
              isSelected={isSelected}
              selectAll={selectAll}
              onDeleteAssignment={onDeleteAssignment}
              closeMessage={closeMessage}
              changeAndDisplayMessage={changeAndDisplayMessage}
              brandAndKeyFilter={brandAndKeyFilter}
              trackExpanded={trackExpanded}
              isExpanded={isExpanded}
              onSetExpanded={onSetExpanded}
              searchTerm={searchTerm}
              loading={loading}
              productType={productType}
              mode={mode}
              selectItem={selectItem}
              sessionRef={props.sessionRef}
              data={data}
              rowHeight={rowHeight}
            />
          </div>
        </div>
      )}
    </div>
  );
};
