import {
  Button,
  ChevronDownSmallIcon,
  ChevronRightSmallIcon,
  Heading,
  MinusIcon,
  PlusIcon,
  Theme,
  ThemeName,
} from '@taopix/taopix-design-system';
import React, { useEffect, useRef, useState } from 'react';
import { useTranslation } from 'react-i18next';
import classNames from 'classnames';

export interface EditSectionProps {
  experienceId: number;
  dataPath: string;
  label: string;
  helpText: string;
  sections: Object;
  renderItem: Function;
  parentKey: string;
  theKey: string;
  className?: string;
  subSection: boolean;
  expandable?: boolean;
  expanded?: boolean;
  toggleSectionVisibility?: Function;
  hasError?: boolean;
  disabled: { disabled: boolean; disabledText: string };
}

export const EditSection = ({
  subSection,
  experienceId,
  dataPath,
  label,
  helpText,
  sections,
  renderItem,
  parentKey,
  theKey,
  className = '',
  expandable,
  expanded,
  toggleSectionVisibility,
  hasError,
  disabled,
  ...props
}: EditSectionProps) => {
  const { t } = useTranslation();
  const [isExpanded, setIsExpanded] = useState(expanded);

  const panelClasses = classNames(subSection ? 'mb-xl' : 'p-md mb-md', className);
  const headingClasses = classNames('flex', 'items-center', 'space-x-sm', disabled.disabled && 'opacity-40');
  const sectionRef = useRef(null);
  const contentClasses = classNames(subSection ? 'pl-lg mt-sm' : 'mt-lg', expandable && !isExpanded && 'hidden');
  const descriptionClasses = classNames('pb-sm',disabled.disabled && 'opacity-40');

  useEffect(() => {
    if (hasError) {
      setIsExpanded(true);
    }
  }, [hasError]);

  return (
    <Theme
      id={'subSection-' + experienceId + '_section_' + dataPath + '_' + parentKey + '_' + theKey}
      element={'section'}
      name={subSection ? undefined : ThemeName.Section}
      allowBorder={!subSection}
      className={panelClasses}
      key={experienceId + '_section_' + dataPath + '_' + parentKey + '_' + theKey}
      ref={sectionRef}
    >
      <Heading size={subSection ? 3 : 2} className={headingClasses}>
        {expandable ? (
          <>
            <Button
              label={isExpanded ? t('str_LabelHide') : t('str-LabelShow')}
              hideLabel
              startIcon={isExpanded ? <ChevronDownSmallIcon /> : <ChevronRightSmallIcon />}
              buttonStyle={'tertiary'}
              size={'small'}
              onClick={() => {
                toggleSectionVisibility(theKey);
              }}
              disabled={hasError}
            />
            {!hasError ? (
              <a
                href={'javscript:void(0)'}
                onClick={() => {
                  toggleSectionVisibility(theKey);
                }}
                className={'flex-1'}
              >
                {label}
              </a>
            ) : (
              <span>{label}</span>
            )}
          </>
        ) : (
          <span>{label}</span>
        )}
      </Heading>
      <div className={contentClasses}>
        {helpText && <p className={descriptionClasses}>{helpText}</p>}
        {Object.entries(sections).map(([subKey, subItem]: any) => {
          return (
            <React.Fragment key={experienceId + '_section_' + dataPath + '_' + parentKey + '_' + theKey + '_' + subKey}>
              {renderItem(subKey, subItem, parentKey + '.' + theKey)}
            </React.Fragment>
          );
        })}
      </div>
    </Theme>
  );
};
