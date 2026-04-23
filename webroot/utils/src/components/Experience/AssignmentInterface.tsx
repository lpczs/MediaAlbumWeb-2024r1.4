import React, { useState, useEffect, useRef } from 'react';
import axios, { AxiosResponse } from 'axios';
import {
  Button,
  ChevronLeftIcon,
  ChevronLeftSmallIcon,
  ChevronRightSmallIcon,
  Heading,
  HelpIcon,
  Icon,
  LoadingLosenge,
  MoreIcon,
  Panel,
  SpeechBubbleIcon,
  Theme,
} from '@taopix/taopix-design-system';
import { AssignmentColumnId, AssignmentType, ExperienceAssignMode, ExperienceType, ProductType } from '../../Enums';
import { useTranslation } from 'react-i18next';
import { OverviewTable } from './Assignment/Table/OverviewTable';
import {
  Selections,
  Template,
  ExperienceOverviewServerResponse,
  TemplateSelect,
  ProductTypeData,
  AssignmentColumn,
  Brand,
} from '../../types';
import { ExperienceOverviewProductTypeTabs } from './Assignment/Tabs/ExperienceOverviewProductTypeTabs';
import { AssignModeAndSearch } from './Assignment/Tabs/AssignModeAndSearch';
import { AssignmentMessage } from './Assignment/Message/AssignmentMessage';
import { useErrorBoundary } from 'react-error-boundary';
import { EditMultipleButton } from './Assignment/Button/EditMultipleButton';
import ReactPaginate from 'react-paginate';
import classNames from 'classnames';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}

export interface AssignmentInterfaceProps {
  sessionRef: number;
  userId: string;
  documentRoot: Document | ShadowRoot | Element;
  type: ExperienceType;
}

export const AssignmentInterface = ({ type = 0, ...props }: AssignmentInterfaceProps) => {
  const { t } = useTranslation();
  const { showBoundary } = useErrorBoundary();

  const [loading, setLoading] = useState<boolean>(true);
  const [data, setData] = useState<ExperienceOverviewServerResponse>(null);
  const [templateList, setTemplateList] = useState<TemplateSelect[]>([]);
  const [selections, setSelections] = useState<Selections>({
    keys: [],
    templates: [],
    assignmentType: null,
  });
  const [selectAllSelected, setSelectAllSelected] = useState<Array<string>>([]);
  const [saving, setSaving] = useState(false);
  const [mode, setMode] = useState(ExperienceAssignMode.BrandAndKey);
  const [productType, setProductType] = useState<ProductTypeData>({
    type: ProductType.Any,
    retroPrint: false,
  });
  const [searchTerm, setSearchTerm] = useState('');
  const [expanded, setExpanded] = useState<Array<string>>((localStorage.getItem('expanded')) ? JSON.parse(localStorage.getItem('expanded')) : []);
  const [brandAndKeyFilter, setBrandAndKeyFilter] = useState<string>('');
  const [abortController, setAbortController] = useState<AbortController>(undefined);
  const [messageOpen, setMessageOpen] = useState<boolean>(false);
  const [messageBoxProps, setMessageBoxProps] = useState<any>({
    open: false,
    positiveClick: () => {},
    negativeClick: () => {},
    positiveLabel: '',
    negativeLabel: '',
    message: '',
    heading: '',
    afterClose: () => {},
  });
  const [multiSelect, setMultiSelect] = useState<boolean>(false);
  const [userColumnData, setUserColumnData] = useState<AssignmentColumnId[]>([]);

  const toggleMultiSelect = () => {
    setMultiSelect(!multiSelect);
  };

  const changeAndDisplayMessage = (newProps: Object) => {
    setMessageBoxProps(newProps);
    setMessageOpen(true);
  };

  const closeMessage = () => {
    setMessageOpen(false);
  };

  const onSetExpanded = (expand: Array<string>) => {
    setExpanded(expand);
    localStorage.setItem('expanded', JSON.stringify(expand));
  };

  const updateData = (newData: ExperienceOverviewServerResponse) => {
    let currentDataClone = { ...data };
    currentDataClone.collections = newData.collections;
    currentDataClone.page = newData.page;
    return currentDataClone;
  };

  /**
   * Calls the server to load the Experience data.
   */
  const getExperienceOverviewData = (
    searchCriteria: string = '',
    assignMode: ExperienceAssignMode = mode,
    productTypeData: ProductTypeData = productType,
    pageNumber: number = 1
  ): void => {
    if (searchCriteria.length < 3 && searchCriteria.length > 0) {
      return;
    }

    setLoading(true);

    const newSignal = new AbortController();

    if (abortController !== undefined) {
      abortController.abort();
    }

    setAbortController(newSignal);

    axios
      .get('/api/experience/getOverviewListData', {
        params: {
          ref: props.sessionRef,
          userId: props.userId,
          productType: productTypeData.type,
          retroPrint: productTypeData.retroPrint,
          mode: assignMode,
          search: searchCriteria,
          page: pageNumber,
        },
        signal: newSignal.signal,
      })
      .then(function (response: AxiosResponse<any>) {
        if (response.data.hasOwnProperty('success') && !response.data.success) {
          window.logOut();
        } else {
          let newData: ExperienceOverviewServerResponse = response.data as any as ExperienceOverviewServerResponse;

          if (pageNumber === 1) {
            setTemplateList(localiseTemplateList(response.data.templates));
            setUserColumnData(response.data.userPrefs);
            setData(newData);
          } else {
            setData(updateData(newData));
          }

          setAbortController(undefined);
          setLoading(false);
        }
      })
      .catch((error: any) => {
        if (!axios.isCancel(error)) {
          showBoundary({
            message: t(error.response.data.error.fullMessage, { ns: 'AdminExperience' }),
          });
        }
      });
  };

  const nextPage = (pageNumber: number) => {
    getExperienceOverviewData(searchTerm, mode, productType, pageNumber);
  };

  const localiseTemplateList = (data: any) => {
    for (const [key, templateArray] of Object.entries(data)) {
      for (const [tkey, template] of Object.entries(templateArray)) {
        if (template.label.slice(0, 4) === 'str_') {
          template.label = t(template.label, { ns: 'AdminExperience' });
        }
      }
    }
    return data;
  };

  const interfaceRef = React.useRef();

  const deleteAssignment = (theKey: string, assignmentType: AssignmentType, finalFunction: Function = () => {}) => {
    const [assignmentKey, templateType, productType, retroPrint] = theKey.split('|');

    let formParams = new FormData();
    formParams.append('ref', props.sessionRef.toString());
    formParams.append('assignmentKey', assignmentKey);
    formParams.append('templateType', templateType.toString());
    formParams.append('productType', productType.toString());
    formParams.append('retroPrint', retroPrint.toString());
    formParams.append('assignmentType', assignmentType.toString());

    axios
      .post('/api/experience/deleteAssignment', formParams)
      .then(function (response: AxiosResponse<any>) {
        if (response.data.hasOwnProperty('success') && !response.data.success) {
          window.logOut();
        } else {
          removeExperienceAssignmentData(theKey, assignmentType);
        }
      })
      .catch((error: any) => {
        showBoundary({
          message: t(error.response.data.error.fullMessage, { ns: 'AdminExperience' }),
        });
      })
      .finally(() => {
        finalFunction();
      });

    closeMessage();
  };

  const applyExperience = (
    column: AssignmentColumn = null,
    selectionsIn: Selections = selections,
    globalSaving: boolean = true,
    finalFunction: Function = () => {}
  ) => {
    let theSelections = { ...selectionsIn };

    if (column !== null) {
      theSelections.templates = selectionsIn.templates.filter(
        template =>
          template.type === column.type.subType &&
          template.productType === column.productType.type &&
          template.retroPrint === column.productType.retroPrint
      );
      theSelections.assignmentType = column.type.subType === 0 ? AssignmentType.Theme : AssignmentType.Experience;
    }

    if (theSelections.templates.length < 1 || theSelections.keys.length < 1) {
      const messageProps = {
        open: true,
        negativeClick: () => {},
        positiveClick: closeMessage,
        positiveLabel: t('str_ButtonClose', { ns: '*' }),
        negativeLabel: '',
        message:
          theSelections.templates.length < 1
            ? t('str_MessagePleaseSelectTemplate', { ns: 'AdminExperience' })
            : t('str_MessagePleaseSelectItems', { ns: 'AdminExperience' }),
        heading: t('str_LabelInformation', { ns: '*' }),
        afterClose: () => {},
      };
      changeAndDisplayMessage(messageProps);
      return false;
    }

    if (globalSaving) {
      setSaving(true);
    }

    let formParams = new FormData();
    formParams.append('ref', props.sessionRef.toString());
    formParams.append('data', JSON.stringify(theSelections));

    axios
      .post('/api/experience/saveAssignmentData', formParams)
      .then(function (response: AxiosResponse<any>) {
        if (response.data.hasOwnProperty('success') && !response.data.success) {
          window.logOut();
        } else {
          updateExperienceAssignmentData(theSelections);
          //setSelections({ keys: selectionsIn.keys, templates: [], assignmentType: null });
        }
      })
      .catch((error: any) => {
        showBoundary({
          message: t(error.response.data.error.fullMessage, { ns: 'AdminExperience' }),
        });
      })
      .finally(() => {
        if (globalSaving) {
          setSaving(false);
        } else {
          finalFunction();
        }
      });
  };

  const removeExperienceAssignmentData = (theKey: string, assignmentType: AssignmentType) => {
    let currentDataClone: ExperienceOverviewServerResponse = { ...data };
    delete currentDataClone.assignment[assignmentType][theKey];
    setData(currentDataClone);
  };

  const updateExperienceAssignmentData = (selections: Selections) => {
    let currentDataClone: ExperienceOverviewServerResponse = { ...data };

    for (let i = 0; i < selections.keys.length; i++) {
      const thisKey = selections.keys[i];

      for (let index = 0; index < selections.templates.length; index++) {
        const theKey = thisKey;
        const template = selections.templates[index];
        const [brandCode, keyCode, collectionCode, layoutCode] = theKey.split('.');

        const assignIndex = [theKey, template.type, template.productType, template.retroPrint ? '1' : '0'].join('|');

        if (template.templateId === -100) {
          delete currentDataClone.assignment[selections.assignmentType][assignIndex];
        } else {
          currentDataClone.assignment[selections.assignmentType][assignIndex] = {
            id: 0,
            objectType: template.type,
            productCode: [collectionCode, layoutCode].join('.'),
            theKey: thisKey,
            templateId: template.templateId,
            productType: template.productType,
            retroPrint: template.retroPrint,
          };
        }
      }
    }

    setData(currentDataClone);
  };

  const isSelected = (theKey: string) => {
    return selections.keys.indexOf(theKey) > -1;
  };

  const isSelectAllSelected = (theKey: string) => {
    return selectAllSelected.indexOf(theKey) > -1;
  };

  const selectAll = (theKey: string, select: boolean = true) => {
    let selectAllSelectedClone = [...selectAllSelected];
    let selectAllIndex = selectAllSelected.indexOf(theKey);

    if (selectAllIndex === -1 && select) {
      selectAllSelectedClone.push(theKey);
    } else if (selectAllIndex > -1 && !select) {
      selectAllSelectedClone.splice(selectAllIndex, 1);
    }

    let selectionsClone = { ...selections };
    const keyArray = theKey.split('.');
    const productCode = [keyArray[2], keyArray[3]].join('.');

    /* need to select the any brand any key row also */
    let anyBrandAnyKey: Brand = {};
    anyBrandAnyKey['*'] = {
        code: '*', 
        name: '',
        licenseKeys:{}
    }

    Object.entries({...data.brands, ...anyBrandAnyKey}).forEach(([bkey, brand]) => {
      let myKey = [brand.code, productCode].join('.*.');
      let selectIndex = selectionsClone.keys.indexOf(myKey);
      if (selectIndex === -1 && select) {
        selectionsClone.keys.push(myKey);
      } else if (selectAllIndex > -1 && !select) {
        selectionsClone.keys.splice(selectIndex, 1);
      }
      Object.entries(brand.licenseKeys).forEach(([lkey, licenseKey]) => {
        myKey = [brand.code, licenseKey.code, productCode].join('.');
        selectIndex = selectionsClone.keys.indexOf(myKey);
        if (selectIndex === -1 && select) {
          selectionsClone.keys.push(myKey);
        } else if (selectAllIndex > -1 && !select) {
          selectionsClone.keys.splice(selectIndex, 1);
        }
      });
    });

    setSelectAllSelected(selectAllSelectedClone);
    setSelections(selectionsClone);
    trackExpanded(theKey, false);
  };

  const selectItem = (checked: boolean, e: React.FormEvent) => {
    let selectionsClone = { ...selections };
    const theKey = (e.target as HTMLInputElement).id;
    const index = selectionsClone.keys.indexOf(theKey);

    if (checked && index === -1) {
      selectionsClone.keys.push(theKey);
    } else if (!checked && index > -1) {
      selectionsClone.keys.splice(index, 1);
    }
    setSelections(selectionsClone);
  };

  const selectTemplate = (assignmentType: AssignmentType, selectedTemplate: Template) => {
    let selectionsClone = { ...selections };

    for (let index = 0; index < selectionsClone.templates.length; index++) {
      if (
        selectionsClone.templates[index].type === selectedTemplate.type &&
        selectionsClone.templates[index].productType === selectedTemplate.productType &&
        selectionsClone.templates[index].retroPrint === selectedTemplate.retroPrint
      ) {
        selectionsClone.templates.splice(index, 1);
      }
    }

    if (selectedTemplate.templateId !== -1) {
      selectionsClone.templates.push(selectedTemplate);
      selectionsClone.assignmentType = assignmentType;
    }

    setSelections(selectionsClone);
  };

  const selectBrandKeyFilter = (e: React.FormEvent) => {
    setBrandAndKeyFilter((e.target as HTMLInputElement).value);
  };

  const search = (e: React.FormEvent) => {
    const searchTerm = (e.target as HTMLInputElement).value;
    if (searchTerm.length > 2 || searchTerm.length === 0) {
      getExperienceOverviewData(searchTerm, mode, productType, 1);
    }
    setSearchTerm(searchTerm);
  };

  const cancelSearch = () => {
    setSearchTerm('');
    getExperienceOverviewData('', mode, productType, 1);
  };

  const setAssignProductType = (type: ProductTypeData) => {
    setLoading(true);
    setProductType(type);
    setMode(type.type === ProductType.Any ? ExperienceAssignMode.BrandAndKey : ExperienceAssignMode.Product);
    setSelections({ keys: [], templates: [], assignmentType: null });
    setSearchTerm('');
    getExperienceOverviewData(
      '',
      type.type === ProductType.Any ? ExperienceAssignMode.BrandAndKey : ExperienceAssignMode.Product,
      type,
      1
    );
    if (type.type !== ProductType.Any) {
      setBrandAndKeyFilter('');
    }
  };

  const isExpanded = (key: string) => {
    return expanded.indexOf(key) > -1;
  };

  const trackExpanded = (key: string, closeIfOpen: boolean = true) => {
    let expandedClone = [...expanded];
    const index = expandedClone.indexOf(key);
    if (index > -1) {
      if (closeIfOpen) {
        expandedClone.splice(index, 1);
      }
    } else {
      expandedClone.push(key);
    }
    onSetExpanded(expandedClone);
  };

  useEffect(() => {
    //First Load open brand/license key tab
    nextPage(1);
  }, []);

  const PAGE_LIMIT = 100;
  const paginationLinkClasses = classNames('flex', 'w-xl', 'h-xl', 'justify-center', 'items-center', 'rounded-xs');
  const standardLinkClasses = classNames(paginationLinkClasses, 'bg-gray-100', 'hover:bg-themeAccentColour20');
  const activeLinkClasses = classNames(paginationLinkClasses, 'bg-themeAccentColour20');
  const disabledLinkClassNames = classNames(paginationLinkClasses, 'bg-gray-100', 'pointer-events-none', 'opacity-30');

  return (
    <div ref={interfaceRef} id="experienceOverviewInterface" className="flex flex-col p-lg flex-1 w-full">
      <AssignmentMessage
        sessionRef={props.sessionRef}
        theRef={interfaceRef}
        open={messageOpen}
        componentMountPoint={interfaceRef.current}
        positiveClick={messageBoxProps.positiveClick}
        negativeClick={messageBoxProps.negativeClick}
        positiveLabel={messageBoxProps.positiveLabel}
        negativeLabel={messageBoxProps.negativeLabel}
        message={messageBoxProps.message}
        heading={messageBoxProps.heading}
        afterClose={messageBoxProps.afterClose}
      />
      <Heading className={'mb-lg'}>{t('str_SectionTitleBulkConfiguration', { ns: '*' })}</Heading>

      <div className="flex h-xxl w-full">
        <ExperienceOverviewProductTypeTabs
          productType={productType}
          setAssignProductType={setAssignProductType}
          sessionRef={props.sessionRef}
          componentMountPoint={interfaceRef.current}
          retroPrintsEnabled={data !== null ? data.features.retroPrints : false}
          selectBrandKeyFilter={selectBrandKeyFilter}
          mode={mode}
          searchTerm={searchTerm}
          search={search}
          cancelSearch={cancelSearch}
        />
        <Button
          label={t('*:str_LabelHelp')}
          startIcon={<HelpIcon />}
          buttonStyle="standard"
          size={'small'}
          className={'ml-auto self-start'}
          onClick={() => window.open('https://support.taopix.com/hc/en-gb/sections/17501048966045', '_blank')}
        />
        <Button
          label={t('*:str_LabelFeedback')}
          startIcon={<SpeechBubbleIcon />}
          buttonStyle="standard"
          size={'small'}
          className={'self-start'}
          onClick={() => window.open('mailto:feedback@taopix.com?subject=Feedback for Bulk Configuration')}
        />
      </div>

      <Panel className={'flex-1 flex flex-col !rounded-tl-0 !rounded-b-themeCornerSize min-h-0'}>
        <div id="optionsBar" className={'flex py-lg px-sm sticky justify-between items-center top-0 z-10 h-[70px]'}>
          <AssignModeAndSearch
            selectBrandKeyFilter={selectBrandKeyFilter}
            brands={data !== null ? data.brands : {}}
            sessionRef={props.sessionRef}
            componentMountPoint={interfaceRef.current}
            mode={mode}
            search={search}
            cancelSearch={cancelSearch}
            searchTerm={searchTerm}
            loading={loading}
          />
          <EditMultipleButton
            sessionRef={props.sessionRef}
            toggleMultiSelect={toggleMultiSelect}
            multiSelect={multiSelect}
          />
        </div>

        <OverviewTable
          isSelectAllSelected={isSelectAllSelected}
          isSelected={isSelected}
          selectAll={selectAll}
          deleteAssignment={deleteAssignment}
          closeMessage={closeMessage}
          changeAndDisplayMessage={changeAndDisplayMessage}
          brandAndKeyFilter={brandAndKeyFilter}
          trackExpanded={trackExpanded}
          isExpanded={isExpanded}
          searchTerm={searchTerm}
          productType={productType}
          mode={mode}
          selectItem={selectItem}
          sessionRef={props.sessionRef}
          componentMountPoint={interfaceRef.current}
          data={data}
          loading={loading}
          onSetExpanded={onSetExpanded}
          templateList={templateList}
          applyExperience={applyExperience}
          selections={selections}
          selectTemplate={selectTemplate}
          saving={saving}
          multiSelect={multiSelect}
          userId={props.userId}
          userColumnData={userColumnData}
        />

        {!loading && mode === ExperienceAssignMode.Product && data.totalRecords > PAGE_LIMIT && (
          <ReactPaginate
            breakLabel={<Icon icon={<MoreIcon />} />}
            nextLabel={<Icon icon={<ChevronRightSmallIcon />} />}
            onPageChange={e => {
              nextPage(e.selected + 1);
            }}
            pageCount={data !== null ? Math.ceil(data.totalRecords / PAGE_LIMIT) : 0}
            previousLabel={<Icon icon={<ChevronLeftSmallIcon />} />}
            forcePage={data !== null ? data.page - 1 : 0}
            renderOnZeroPageCount={null}
            containerClassName="flex p-2 list-none items-center space-x-xs"
            pageClassName=""
            pageLinkClassName={standardLinkClasses}
            activeClassName=""
            activeLinkClassName={activeLinkClasses}
            previousClassName=""
            previousLinkClassName={standardLinkClasses}
            nextClassName=""
            nextLinkClassName={standardLinkClasses}
            disabledClassName=""
            disabledLinkClassName={disabledLinkClassNames}
            breakClassName=""
            breakLinkClassName={standardLinkClasses}
          />
        )}
      </Panel>
    </div>
  );
};
