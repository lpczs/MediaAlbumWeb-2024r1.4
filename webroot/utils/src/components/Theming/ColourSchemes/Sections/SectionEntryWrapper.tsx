import React, { ReactNode, useCallback, useMemo, useState } from 'react';
import { omit, cloneDeep, get } from 'lodash';
import { useTranslation } from 'react-i18next';
import classNames from 'classnames';
import { Button } from '@taopix/taopix-design-system';
import { formatTitle } from '../../../../common';
import { useTheming } from '../../Context/ThemeContext';
import { ThemeActions } from '../../Actions/ThemeActions';
import { OwnerType } from '../../../../Enums';
import { ThemeProps } from '../../../../types';

export type SectionEntryWrapperProps = {
  children: ReactNode;
  name: string;
  section: string;
  subTheme: string;
  editable: boolean;
  contentStyle?: string;
  className?: string;
  onEdit?: (event: any) => void;
};

const SectionEntryWrapper = ({
  children,
  name,
  section,
  subTheme,
  contentStyle,
  editable,
  className,
  onEdit,
}: SectionEntryWrapperProps) => {
  const { dispatch, state: { selectedSchemeId, colourSchemes } } = useTheming();

  const { t } = useTranslation(['*', 'AdminTheming']);
  
  const [editing, setEditing] = useState(false);

  // get the selected theme
  const selectedScheme = useMemo(() => {
    if (!selectedSchemeId) {
      return null;
    }
    return colourSchemes[selectedSchemeId];
  }, [selectedSchemeId, colourSchemes]);

  // build the path to the property value(s)
  const objectPath = useMemo(() => {
    if (!contentStyle) {
      return `${subTheme}.${section}.${name}`;
    }

    if ('contentStyles' === section) {
      return `${subTheme}.${section}.${contentStyle}`;
    }

    if ('contentStyles' !== section && contentStyle) {
      return `${subTheme}.contentStyles.${contentStyle}.${section}.${name}`;
    }

    return `${subTheme}.${section}.${contentStyle}.${name}`;
  }, [subTheme, contentStyle, section, name]);

  // so we have diff values
  const hasDiff = useMemo(() => {
    if (!selectedScheme) {
      return null;
    }
    return get(selectedScheme.diff, objectPath, false);
  }, [selectedScheme, objectPath]);

  const showChildren = useMemo(() => {
    // if the selected theme is the default theme, always show the child components
    if (selectedScheme && OwnerType.System === selectedScheme.type) {
      return true;
    }

    if (editing && !onEdit) {
      return true;
    }

    return hasDiff !== false;
  }, [hasDiff, onEdit, editing]);

  const onAttemptEdit = (event: React.MouseEvent) => {
    if (onEdit) {
      onEdit(event);
    }
    setEditing(true);
  };

  // remove the path from the diff
  const onReset = useCallback(() => {
    console.log('resetting');
    if (!selectedScheme) {
      return null;
    }

    // create the new diff by omitting the requested path
    let diff = omit(cloneDeep(selectedScheme.diff), [objectPath]);
    
    // pop off the last element of the path to get the parent path
    let parentPath = objectPath.split('.').slice(0, -1).join('.');

    // loop through the all the different path parts for empty values
    while ('' !== parentPath) {
      // get the parentValue
      const parentValue = get(diff, parentPath, null);

      // if the parentValue is empty (we've removed all values from it), remove the parent
      if (parentValue && 'object' === typeof parentValue && Object.keys(parentValue).length < 1) {
        diff = omit(diff, parentPath);
      }

      if (!parentValue) {
        diff = omit(diff, parentPath);
      }

      parentPath = parentPath.split('.').slice(0, -1).join('.');
    }

    // Ext has modified the array prototype, so we need to remove the functions
    Object.keys(diff).forEach((key: keyof ThemeProps) => {
      if ('function' === typeof diff[key]) {
        delete diff[key];
      }
    });

    dispatch(
      ThemeActions.updateColourScheme({
        ...selectedScheme,
        diff,
        dirty: true,
      })
    );
  }, [selectedScheme, objectPath]);

  const placeholderClasses = classNames(
    'flex',
    'h-xl',
    'w-full',
    'items-center',
    'justify-center',
    'text-themeTextColour30',
    'text-sm',
    'text-italic',
    'border',
    'border-dashed',
    'border-gray-300',
    'rounded-xs',
    'hover:bg-gray-200'
  );

  return (
    <div className={classNames(['flex', 'space-x-sm', 'items-center', 'mb-sm', className])}>
      <div className={classNames('flex', 'items-center', 'w-[200px]', 'h-full')}>{formatTitle(name)}</div>
      <div className={classNames('flex', 'items-center', 'w-[210px]', 'h-full', 'space-x-sm')}>
        {showChildren ? (
          // Only output the themed element if it has been configured for this theme
          <>{children}</>
        ) : (
          // Otherwise, just show a placeholder
          <button className={placeholderClasses} onClick={onAttemptEdit}>
            {t('AdminTheming:str_LabelNotSet')}
          </button>
        )}
      </div>
      {hasDiff && editable && (
        <div className={classNames('flex', 'items-center', 'flex-1', 'h-full', 'space-x-sm')}>
          <Button
            buttonStyle={'tertiary'}
            label={t('str_ButtonReset', { ns: '*' })}
            onClick={onReset}
            corners="theme"
            size="small"
          />
        </div>
      )}
    </div>
  );
};

export default SectionEntryWrapper;
