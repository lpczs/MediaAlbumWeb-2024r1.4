import React, { useState, useEffect, useRef } from 'react';
import axios, { AxiosResponse } from 'axios';
import {
  Button,
  EditorIcon,
  GearIcon,
  Heading,
  HelpIcon,
  Panel,
  SpeechBubbleIcon,
  Theme,
  ThemeName,
  UploadIcon,
  WandIcon,
} from '@taopix/taopix-design-system';
import { DisplayEdit } from './Edit/DisplayEdit';
import { ExperienceList } from './List/ExperienceList';
import { ExperienceSystemType, ExperienceType } from '../../Enums';
import { Experience, ExperienceServerResponse, ConfirmMessagePositiveFunction, ConfirmMessage, Features, ExperienceSaveServerResponse, ExperienceError } from '../../types';
import { useTranslation } from 'react-i18next';
import { Confirmation } from './Message/Confirmation';
import { useErrorBoundary } from 'react-error-boundary';
import NewExperienceButton from './NewExperienceButton';
import ImportExperience from './Import/ImportExperience';

declare global {
  interface Window {
    logOut: () => void;
    google: any;
    gMessageLoading: string;
    gLangCode: string;
  }
}

export interface ManageInterfaceProps {
  sessionRef: number;
  documentRoot: Document | ShadowRoot;
  type: ExperienceType;
}

export const ManageInterface = ({ type = 0, documentRoot, ...props }: ManageInterfaceProps) => {
  const [experienceListState, setExperienceListState] = useState<Array<Experience>>([]);
  const [selectedExperienceState, setSelectedExperienceState] = useState<Experience>(null);
  const [schema, setSchema] = useState<Object>(null);
  const [baseExperience, setBaseExperience] = useState<Array<Record<string, Object>>>(null);
  const [selectedExperienceTypeState, setSelectedExperienceTypeState] = useState<ExperienceType>(type);
  const [switchExperienceTypeState, setSwitchExperienceTypeState] = useState<ExperienceType>(type);
  const [isDirty, setIsDirty] = useState<boolean>(false);
  const [confirmOpen, setConfirmOpen] = useState<boolean>(false);
  const [confirmMessageBox, setConfirmMessageBox] = useState<ConfirmMessage>(null);
  const [busy, setBusy] = useState<{ busy: boolean; msg: string }>({ busy: true, msg: '' });
  const [features, setFeatures] = useState<Features>({ ai: false, retroPrints: false, scaleBeforeUpload: false });
  const [abortController, setAbortController] = useState<AbortController>(undefined);
  const [currentKey, setCurrentKey] = useState('');
  const [errorArray, setErrorArray] = useState<{ [dataPath: string]: ExperienceError }>({});

  const onSetErrorArray = (errorArray: { [dataPath: string]: ExperienceError }) => {
    setErrorArray(errorArray);
  };

  const formRef = useRef<HTMLFormElement>(null);

  const { t } = useTranslation('AdminExperience');
  const { showBoundary } = useErrorBoundary();

  const onSetSchema = (schema: object) => {
    setSchema(schema);
  };

  const onSetCurrentKey = (setkey: string) => {
    setCurrentKey(setkey)
  }

  const onSetBusy = (busy: { busy: boolean; msg: string }) => {
    setBusy(busy);
  };

  const onUpdateExperience = (experience: Experience, deletedFlag: Boolean = false) => {
    let experienceList = [...experienceListState];

    //check if the experience is new or updated
    const existingExperienceIndex = experienceList.findIndex(x => x.id === experience.id);

    if (typeof existingExperienceIndex !== 'undefined' && existingExperienceIndex > -1) {
      if (deletedFlag) {
        experienceList.splice(existingExperienceIndex, 1);
      } else {
        experienceList[existingExperienceIndex] = experience;
      }
    } else {
      experienceList.unshift(experience);

      experienceList = experienceList.sort((a: Experience, b: Experience) => {
        const aType = (a.experienceType === 1) ? 4 : a.experienceType
        const bType = (b.experienceType === 1) ? 4 : b.experienceType
        const typeOrder = (aType - bType)
        const nameOrder = a.name.localeCompare(b.name)

        return typeOrder || nameOrder
      });
    }

    setExperienceListState(experienceList);
    setSelectedExperienceState(deletedFlag ? null : experience);
  };

  const onCreateNewExperience = (
    experienceType: ExperienceType = null,
    experienceToDuplicate: Experience = {} as Experience
  ) => {
    if (isDirty) {
      showDialog('str_MessageConfirmNotSaved', createNewExperience, [experienceType, experienceToDuplicate]);
    } else {
      createNewExperience(experienceType, experienceToDuplicate);
    }
  };

  const createNewExperience = (
    experienceType: ExperienceType = null,
    experienceToDuplicate: Experience = {} as Experience
  ) => {
    let experienceList = [...experienceListState];
    let newExperience: Experience;
    const existingExperienceIndex = experienceList.findIndex(x => x.id === experienceToDuplicate.id);

    if (typeof existingExperienceIndex !== 'undefined' && existingExperienceIndex > -1) {
      newExperience = { ...experienceList[existingExperienceIndex] };
      const localisedName = newExperience.name.slice(0, 4)
        ? t(newExperience.name, { ns: 'AdminExperience' })
        : newExperience.name;
      newExperience.id = -1;
      newExperience.name =
        t('str_DuplicateExperienceDefaultTitlePrefix', {
          ns: 'AdminExperience',
        }) +
        ' ' +
        localisedName;
      newExperience.systemType = ExperienceSystemType.CUSTOM;
      newExperience.code = '';
    } else {
      const baseIndex: any = '0|0';
      newExperience = {
        id: -1,
        experienceType: experienceType !== null ? experienceType : selectedExperienceTypeState,
        name: t('str_NewConfigurationDefaultTitle', { ns: 'AdminExperience' }),
        productType: 0,
        retroPrint: false,
        data: baseExperience[baseIndex][
          ExperienceType[experienceType !== null ? experienceType : selectedExperienceTypeState] as keyof Object
        ],
        dataLength: 0,
        assignment: [],
        systemType: ExperienceSystemType.CUSTOM,
        code: ''
      };
    }

    setSelectedExperienceState(newExperience);
    setConfirmOpen(false);
    setIsDirty(false);
  };

  const onDeleteExperienceData = (experience: Experience): void => {
    showDialog('str_MessageConfirmExperienceDeletion', proceedToDeleteExperienceData, [experience]);
    setConfirmOpen(true);
  };

  const proceedToDeleteExperienceData = (experience: Experience): void => {
    setBusy({ busy: true, msg: t('str_MessagePleaseWait', { ns: '*' }) });
    setConfirmOpen(false);
    let formParams = new FormData();
    formParams.append('ref', props.sessionRef.toString());
    formParams.append('fsaction', 'AdminExperienceEditing.deleteExperience');
    formParams.append('experienceIdArray', JSON.stringify([experience.id]));

    axios
      .post('/api/experience/delete', formParams)
      .then(function (response: AxiosResponse<ExperienceServerResponse>) {
        if (response.data.hasOwnProperty('success') && !response.data.success) {
          window.logOut();
        } else {
          onUpdateExperience(
            {
              ...experience,
            },
            true
          );
        }
      })
      .catch((error: any) => {
        showBoundary({
          message: t(error.response.data.error.fullMessage, { ns: 'AdminExperience' }),
        });
      })
      .finally(() => {
        setBusy({ busy: false, msg: '' });
      });
  };

  const onSetExperienceState = (experience: Experience) => {
    if (selectedExperienceState !== null && experience.id === selectedExperienceState.id) {
      //already on this experience
      return false;
    }

    if (isDirty) {
      showDialog('str_MessageConfirmNotSaved', changeExperience, [experience]);
    } else {
      changeExperience(experience);
    }
  };

  const changeExperience = (experience: Experience) => {
    setIsDirty(false);
    setSelectedExperienceState(experience);
    setConfirmOpen(false);
  };

  const onSetIsDirty = (isDirty: boolean) => {
    setIsDirty(isDirty);
  };

  const getIcon = (experienceType: ExperienceType) => {
    let icon;

    switch (experienceType) {
      case ExperienceType.EDITOR:
        icon = <EditorIcon />;
        break;
      case ExperienceType.SETTINGS:
        icon = <GearIcon />;
        break;
      case ExperienceType.WIZARD:
      default:
        icon = <WandIcon />;
        break;
    }

    return icon;
  };

  const showDialog = (
    message: string,
    positiveClickFunction: Function | null,
    positiveParams: Array<Experience | ExperienceType>,
    negativeLabel: string = t('str_ButtonNo', { ns: '*' })
  ) => {
    setConfirmMessageBox({
      message: t(message, { ns: 'AdminExperience' }),
      positiveFunction: {
        function: positiveClickFunction,
        param: positiveParams,
      },
      negativeLabel: negativeLabel,
    });
    setConfirmOpen(true);
  };

  const switchExperienceType = (experienceType: ExperienceType) => {
    setSwitchExperienceTypeState(experienceType);
    if (selectedExperienceState && (selectedExperienceState.id === -1 || isDirty)) {
      showDialog('str_MessageConfirmNotSaved', switchExperienceTypeConfirm, [experienceType]);
    } else {
      if (experienceType !== selectedExperienceTypeState) {
        setSelectedExperienceTypeState(experienceType);
        setSelectedExperienceState(null);
      }
    }
  };

  const switchExperienceTypeConfirm = (experienceType: ExperienceType) => {
    if (experienceType !== selectedExperienceTypeState) {
      setSelectedExperienceState(null);
      setSelectedExperienceTypeState(experienceType);
    }

    setConfirmOpen(false);
    setIsDirty(false);
  };

  /**
   * Calls the server to load the Experience data.
   */
  const getExperienceData = (experienceType: ExperienceType = ExperienceType.FULL): void => {
    setBusy({ busy: true, msg: t('str_MessageLoading', { ns: '*' }) });
    if (experienceType !== selectedExperienceTypeState) {
      setSelectedExperienceState(null);
    }

    const newSignal = new AbortController();

    if (abortController !== undefined) {
      abortController.abort();
    }

    setAbortController(newSignal);

    if (experienceType !== selectedExperienceTypeState) {
      setSelectedExperienceTypeState(experienceType);
    }

    axios
      .get('/api/experience/getListData', {
        params: {
          ref: props.sessionRef,
          experiencetype: experienceType,
        },
        signal: newSignal.signal,
      })
      .then(function (response: AxiosResponse<ExperienceServerResponse>) {
        if (response.data.hasOwnProperty('success') && !response.data.success) {
          window.logOut();
        } else {
          setSchema(response.data.schema);
          setBaseExperience(response.data.baseExperience);
          setExperienceListState(response.data.data);
          setFeatures(response.data.features);
          setBusy({ busy: false, msg: '' });
        }
      })
      .catch((error: any) => {
        if (!axios.isCancel(error)) {
          showBoundary({
            message: t(error.response.data.error.fullMessage, { ns: 'AdminExperience' }),
          });
          setBusy({ busy: false, msg: '' });
        }
      });

    setSelectedExperienceState(null);
  };

  useEffect(() => {
    //First Time in Load data
    getExperienceData(selectedExperienceTypeState);
  }, []);

  const toggle = () => {
    setConfirmOpen(!confirmOpen);
  };

  const isNameUnique = (experienceName: string, importMode: boolean = false) => {
    const filteredArray = experienceListState.filter(item => item.name === experienceName);

    //if one item found check its not the current template itself - if it isn't fail unique check
    if (filteredArray.length === 1) {
      if (importMode) {
        return false;
      }
      
      if (selectedExperienceState.id === -1 || selectedExperienceState.id !== filteredArray[0].id) {
        return false;
      }
    }

    //if more than one item found its definitely not a unique name
    if (filteredArray.length > 1) {
      return false;
    }

    //if it wasn't found or its the current template then pass unique check
    return true;
  };

  const onOpenFileBrowser = () => {
    if (uploadRef.current) {
      uploadRef.current.click();
    }
  };

  const interfaceRef = React.useRef();
  const uploadRef = useRef<HTMLInputElement>(null);

  const isValid = () => {
    let valid = true;
    let setKey = '';

    if (Object.keys(errorArray).length > 0) {
      //we have errors so not valid
      valid = false;
      setKey = Object.keys(errorArray)[0];
    }

    setCurrentKey(setKey);
    return valid;
  };

  /**
   * Calls the server to save the Experience data.
   */
  const onSaveExperienceData = (experience: Experience): void => {
    if (experience.name === '') {
      return;
    }

    if (isNameUnique(experience.name)) {
      if (isValid()) {
        onSetBusy({ busy: true, msg: t('str_MessageSaving', { ns: '*' }) });
        saveData(experience)
        .then((response: AxiosResponse<ExperienceSaveServerResponse>) => {
          if (response.data.hasOwnProperty('success') && !response.data.success) {
            window.logOut();
          } else {
            onUpdateExperience(
              {
                ...experience,
                ['id']: response.data.data.experienceid,
              },
              false
            );
            onSetBusy({ busy: false, msg: '' });
          }
        })
        .catch((error: any) => {
          showBoundary({
            message: t(error.response.data.error.fullMessage, {ns:'AdminExperience'})
          });
          onSetBusy({ busy: false, msg: '' });
        });
      } else {
        showDialog(
          t('str_MessageFailedValidation', { ns: 'AdminExperience' }),
          null,
          [],
          t('str_LabelClose', { ns: '*' })
        );
      }
    } else {
      setCurrentKey('name');
      showDialog(
        t('str_MessageNameNotUnique', { ns: 'AdminExperience' }),
        null,
        [],
        t('str_LabelClose', { ns: '*' })
      );
    }
  };

  const saveData = (experience: Experience): Promise<AxiosResponse<ExperienceSaveServerResponse>> => {
    let formParams = new FormData();
    formParams.append('ref', props.sessionRef.toString());
    formParams.append('fsaction', 'AdminExperienceEditing.saveExperience');
    formParams.append('experience', JSON.stringify(experience));

    setIsDirty(false);

    return axios
      .post('/api/experience/saveData', formParams);
  }

  return (
    <div ref={interfaceRef} id="experienceInterface" className="flex flex-col p-lg flex-1">
      {confirmMessageBox !== null ? (
        <Confirmation
          sessionRef={props.sessionRef}
          theRef={interfaceRef}
          open={confirmOpen}
          afterClose={() => {}}
          componentMountPoint={interfaceRef.current}
          positiveClick={confirmMessageBox.positiveFunction}
          negativeClick={toggle}
          positiveLabel={t('str_ButtonYes', { ns: '*' })}
          negativeLabel={confirmMessageBox.negativeLabel}
          message={confirmMessageBox.message}
          heading={t('str_TitleWarning', { ns: '*' })}
          switchExperienceTypeState={switchExperienceTypeState}
        />
      ) : (
        <></>
      )}

      <Heading level={1} className={'mb-lg'}>
        {t('str_TitleManageUIConfigurations', { ns: '*' })}
      </Heading>

      <div className="flex mb-sm w-full">
        <ImportExperience onUpdateExperience={onUpdateExperience} onSave={saveData} ref={uploadRef} documentRoot={documentRoot} experiences={experienceListState} isNameUnique={isNameUnique}/>
        <NewExperienceButton
          getIcon={getIcon}
          onCreateNewExperience={createNewExperience}
          shadowRoot={interfaceRef.current}
          disabled={busy.busy}
        />
        <Button
          onClick={onOpenFileBrowser}
          buttonStyle="standard"
          corners="theme"
          label={t('AdminTheming:str_LabelImport')}
          startIcon={<UploadIcon />}
          size="small"
          className={'ml-2'}
          disabled={busy.busy}
        />
        <Button
          label={t('*:str_LabelHelp')}
          startIcon={<HelpIcon />}
          buttonStyle="standard"
          size={'small'}
          className={'ml-auto'}
          onClick={() => window.open('https://support.taopix.com/hc/en-gb/sections/17394897165469', '_blank')}
        />
        <Button
          label={t('*:str_LabelFeedback')}
          startIcon={<SpeechBubbleIcon />}
          buttonStyle="standard"
          size={'small'}
          onClick={() => window.open('mailto:feedback@taopix.com?subject=Feedback for UI Configurations')}
        />
      </div>
      {
        <>
          <Panel className={'flex-1 flex flex-col overflow-hidden !rounded-themeCornerSize'}>
            <div className="flex w-full justify-start space-x-xs p-sm border-b border-b-themeBorderColour">
              <Button
                aria-pressed={selectedExperienceTypeState === ExperienceType.FULL}
                onClick={() => switchExperienceType(ExperienceType.FULL)}
                buttonStyle="standard"
                label={t('str_LabelAll', { ns: '*' })}
                size={'small'}
              />
              <Button
                // startIcon={getIcon(ExperienceType.WIZARD)}
                aria-pressed={selectedExperienceTypeState === ExperienceType.WIZARD}
                onClick={() => switchExperienceType(ExperienceType.WIZARD)}
                buttonStyle="standard"
                label={t('str_LabelDesignAssistants', {
                  ns: 'AdminExperience',
                })}
                size={'small'}
              />
              <Button
                // startIcon={getIcon(ExperienceType.EDITOR)}
                aria-pressed={selectedExperienceTypeState === ExperienceType.EDITOR}
                onClick={() => switchExperienceType(ExperienceType.EDITOR)}
                buttonStyle="standard"
                label={t('str_LabelEditors', { ns: 'AdminExperience' })}
                size={'small'}
              />
              <Button
                // startIcon={getIcon(ExperienceType.SETTINGS)}
                aria-pressed={selectedExperienceTypeState === ExperienceType.SETTINGS}
                onClick={() => switchExperienceType(ExperienceType.SETTINGS)}
                buttonStyle="standard"
                label={t('str_LabelExperienceSettings', { ns: 'AdminExperience' })}
                size={'small'}
              />
            </div>
            <div className={'flex-1 flex min-h-0'}>
              <Theme
                name={ThemeName.Container}
                className="w-[320px]"
                allowBorder={false}
                allowCorners={false}
              >
                <ExperienceList
                  componentMountPoint={interfaceRef.current}
                  experienceType={selectedExperienceTypeState}
                  busy={busy}
                  sessionRef={props.sessionRef}
                  experienceList={experienceListState}
                  onCreateNewExperience={onCreateNewExperience}
                  getIcon={getIcon}
                  selectedExperienceId={selectedExperienceState?.id}
                  onSetExperienceState={onSetExperienceState}
                  onDeleteExperienceData={onDeleteExperienceData}
                  features={features}
                />
              </Theme>
              <div className="flex-1 flex flex-col pt-lg px-lg">
                <DisplayEdit
                  features={features}
                  baseExperience={baseExperience}
                  componentMountPoint={interfaceRef.current}
                  isDirty={isDirty}
                  setIsDirty={onSetIsDirty}
                  onDeleteExperienceData={onDeleteExperienceData}
                  onUpdateExperience={onUpdateExperience}
                  sessionRef={props.sessionRef}
                  experience={selectedExperienceState}
                  isNameUnique={isNameUnique}
                  busy={busy}
                  onSetBusy={onSetBusy}
                  schema={
                    selectedExperienceState !== null
                      ? schema[ExperienceType[selectedExperienceState.experienceType] as keyof Object]
                      : {}
                  }
                  dialog={{
                    showFunc: showDialog,
                    toggleFunc: toggle,
                  }}
                  onSaveExperienceData={onSaveExperienceData}
                  onSetCurrentKey={onSetCurrentKey}
                  currentKey={currentKey}
                  onSetErrorArray={onSetErrorArray}
                  errorArray={errorArray}
                />
              </div>
            </div>
          </Panel>
        </>
      }
    </div>
  );
};
