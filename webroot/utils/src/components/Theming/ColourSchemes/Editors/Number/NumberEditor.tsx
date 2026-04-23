import React, { ChangeEvent, useCallback, useEffect, useMemo, useState } from 'react';
import { cloneDeep, get, set } from 'lodash';
import { TextInput } from '@taopix/taopix-design-system';
import { useTheming } from '../../../Context/ThemeContext';
import { ThemeActions } from '../../../Actions/ThemeActions';
import classNames from 'classnames';
import { useTranslation } from 'react-i18next';
import { OwnerType } from '../../../../../Enums';

export type NumberEditorProps = {
  props: string;
  name: string;
  path: string;
  editable: boolean;
  max?: number;
  className?: string;
};

const NumberEditor = ({ name, path, props, editable, className, max = 25 }: NumberEditorProps) => {
  console.log(props);
  const [internalValue, setInternalValue] = useState(props);
  const [error, setError] = useState('');

  const {
    state: { colourSchemes, selectedSchemeId },
    dispatch,
  } = useTheming();

  const { t } = useTranslation();

  const selectedScheme = useMemo(() => {
    if (!selectedSchemeId) {
      return null;
    }
    return colourSchemes[selectedSchemeId];
  }, [selectedSchemeId, colourSchemes]);

  const onBlur = useCallback(() => {
    if (!selectedScheme) {
      return void 0;
    }

    if (internalValue === props) {
      return void 0;
    }

    dispatch(
      ThemeActions.updateColourScheme({
        ...selectedScheme,
        diff: set(cloneDeep(selectedScheme.diff), path, internalValue),
        dirty: true,
      })
    );
  }, [path, internalValue, props, max, selectedScheme]);

  useEffect(() => {
    if (!selectedScheme) {
      return void 0;
    }

    if (OwnerType.System === selectedScheme.type) {
      return void 0;
    }

    setInternalValue(current => {
      const currentSavedValue = get(selectedScheme.diff, path);
      if (currentSavedValue && currentSavedValue === current) {
        return current;
      }

      dispatch(
        ThemeActions.updateColourScheme({
          ...selectedScheme,
          diff: set(cloneDeep(selectedScheme.diff), path, props),
          dirty: true,
        })
      );

      setError('')
    });
  }, [props]);

  return (
    <div className={'w-[210px]'}>
      <TextInput
        id={`text-input-${name}`}
        name={`text-input-${name}`}
        value={internalValue}
        onChange={(event: ChangeEvent) => {
          console.log(event);
          // Convert an empty string to 'unset'
          const value = (event.target as HTMLInputElement).value;
          if (Number(value) > max) {
            return setError(t('str_ExtJsNumberFieldMax').replace('{0}', String(max)));
          }
          setError('');
          setInternalValue(('' === value || 0 === Number(value)) ? 'unset' : value);
        }}
        onBlur={onBlur}
        className={classNames('w-full !h-xl', className)}
        readOnly={!editable}
        error={error}
      />
    </div>
  );
};

export default NumberEditor;
