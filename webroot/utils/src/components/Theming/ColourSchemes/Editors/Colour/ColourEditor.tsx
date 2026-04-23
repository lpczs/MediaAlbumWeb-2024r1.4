import React, { useCallback, useEffect, useMemo, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { RGBColor, SketchPicker } from 'react-color';
import { RgbaColor, colord, extend } from 'colord';
import namesPlugin from 'colord/plugins/names';
import { cloneDeep, set } from 'lodash';
import { DialogContent, DialogFooter, Button, DialogHeader, PopOut } from '@taopix/taopix-design-system';
import { formatTitle } from '../../../../../common';
import { useTheming } from '../../../Context/ThemeContext';
import { ThemeActions } from '../../../Actions/ThemeActions';
import { useColourEditor } from '../../../Context/ColourEditorContext';
import { EditorActions } from '../../../Actions/EditorActions';

extend([namesPlugin]);

const ColourEditor = () => {
  const {
    state: { colourSchemes, selectedSchemeId },
    dispatch,
  } = useTheming();
  const { state, dispatch: editorDispatch } = useColourEditor();
  const { t } = useTranslation();
  const [shade, setShade] = useState(colord(state.props).toRgb());

  const defaultColour = useMemo(() => {
    if (null === state.props) {
      return '';
    }
    return state.props;
  }, [state.props]);

  const selectedScheme = useMemo(() => {
    if (!selectedSchemeId) {
      return null;
    }
    return colourSchemes[selectedSchemeId];
  }, [selectedSchemeId, colourSchemes]);

  const onSaveProperty = useCallback((): void => {
    const { path } = state;
    if (!path || '' === path) {
      return void 0;
    }

    const diff = cloneDeep({ ...selectedScheme.diff });
    const update = set(diff, path, colord(shade).toRgbString());

    dispatch(
      ThemeActions.updateColourScheme({
        ...selectedScheme,
        diff: update,
        dirty: true,
      })
    );
    return onCancel();
  }, [state, selectedScheme, shade]);

  const onCancel = () => {
    editorDispatch(EditorActions.closeEditor({ open: false, props: null }));
  };

  const onSetColour = (colour: RGBColor) => {
    setShade(current => colour as RgbaColor);
  };

  useEffect(() => {
    if ('' !== defaultColour) {
      setShade(colord(defaultColour).toRgb());
    }
  }, [defaultColour]);

  return (
    <PopOut
      open={state.open}
      id={'colour-editor'}
      className={'flex-col m-5'}
      shadowRoot={state.documentRoot as ShadowRoot}
      role="dialog"
      displayMode={'modal'}
    >
      <DialogHeader>Edit {formatTitle(state.name)} colour</DialogHeader>
      <DialogContent className="flex items-center justify-center p-1">
        <SketchPicker 
          color={shade} 
          onChange={value => onSetColour(value.rgb)}
          width={'250px'}
        />
      </DialogContent>
      <DialogFooter>
        <Button label={t('str_ButtonCancel')} buttonStyle={'negative'} onClick={onCancel} />
        <Button label={t('str_ButtonSave')} onClick={onSaveProperty} />
      </DialogFooter>
    </PopOut>
  );
};

export default ColourEditor;
