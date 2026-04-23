import React from 'react';
import { ThemeType, useTheming } from '../Context/ThemeContext';
import {
  getTheme,
  ThemeName,
  Theme,
  Button,
  CopyIcon,
  Icon,
  LockIcon,
  TrashIcon,
  LinkPanel,
  Heading,
  RadioButtonCheckedIcon,
  DotMixed,
  DotLight,
  DotDark,
} from '@taopix/taopix-design-system';
import classNames from 'classnames';
import { t } from 'i18next';
import { OwnerType } from '../../../Enums';

export type ThemeListItemProps = {
  theme: ThemeType;
  selected: boolean;
  onDeleteTheme: (theme: ThemeType) => void;
  onSelectTheme: (theme: ThemeType) => void;
  onCopyTheme: (theme: ThemeType) => void;
};

const ThemeListItem = ({ theme, selected, onDeleteTheme, onSelectTheme, onCopyTheme }: ThemeListItemProps) => {
  const {
    state: { colourSchemes },
  } = useTheming();

  const rowClasses = classNames('flex', 'cursor-pointer', 'group/experience-row', 'rounded-themeCornerSize');

  const themeClasses = classNames(
    selected && getTheme(ThemeName.Prominent, true, false, false),
    'flex',
    'flex-1',
    'flex-col',
    'relative',
    'p-sm',
    'mb-xs',
    'justify-between',
    'bg-black/5'
  );

  return (
    <li key={theme.id} className={rowClasses}>
      <Theme
        name={ThemeName.Section}
        className={themeClasses}
        allowBackground={false}
        onClick={() => onSelectTheme(theme)}
      >
        <div className={'flex items-center mb-xs h-xl'}>
          <Heading size={4} className={'flex-1 line-clamp-1 overflow-ellipsis'}>
            {theme.name}
          </Heading>
          {OwnerType.System !== theme.type ? (
            <Button
              label={t('str_ButtonCopy')}
              hideLabel
              size={'small'}
              startIcon={<CopyIcon />}
              onClick={() => onCopyTheme(theme)}
              buttonStyle="standard"
              className={'mouse:!hidden group-hover/experience-row:!flex'}
            />
          ) : (
            <></>
          )}
          {OwnerType.System === theme.type ? (
            <Icon icon={<LockIcon />} size={'small'} className={'mx-xs'} />
          ) : (
            <Button
              label={t('str_ButtonDelete')}
              hideLabel
              size={'small'}
              startIcon={<TrashIcon />}
              onClick={() => onDeleteTheme(theme)}
              buttonStyle="standard"
              className={'mouse:!hidden group-hover/experience-row:!flex'}
            />
          )}
        </div>
        <ul className="flex flex-col space-y-xs">
          {colourSchemes[theme.defaultSchemeId] && (
            <li className={'flex-1 flex items-center line-clamp-1 overflow-ellipsis'}>
              <Icon icon={colourSchemes[theme.darkSchemeId] ? <DotLight /> : <DotMixed />} size={'small'} />
              {colourSchemes[theme.defaultSchemeId].name}
            </li>
          )}
          {colourSchemes[theme.darkSchemeId] && (
            <li className={'flex-1 flex items-center line-clamp-1 overflow-ellipsis'}>
              <Icon icon={<DotDark />} size={'small'} />
              {colourSchemes[theme.darkSchemeId].name}
            </li>
          )}
        </ul>
      </Theme>
    </li>
  );
};

export default ThemeListItem;
