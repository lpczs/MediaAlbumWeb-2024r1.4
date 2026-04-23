import React, { KeyboardEvent, useMemo } from 'react';
import { useTranslation } from 'react-i18next';
import { Theme, ThemeName, Button, SaveIcon, TextInput, ArrowDownIcon } from '@taopix/taopix-design-system';

export type ViewHeaderProps<T extends { name: string; dirty: boolean }> = {
  onClickSave: () => void;
  onChangeName: (name: string) => void;
  onExport?: (payload: T) => void;
  saving: boolean;
  error?: string;
  readOnly: boolean;
  entry: T;
};

const ViewHeader = <T extends { name: string; dirty: boolean }>({
  onClickSave,
  onChangeName,
  onExport,
  saving,
  error,
  entry,
  readOnly,
}: ViewHeaderProps<T>) => {
  const { t } = useTranslation();

  return (
    <Theme name={ThemeName.Container} className="flex items-start relative p-sm mb-xxl justify-between">
      <div className="flex items-start relative justify-between">
        {useMemo(() => {
          return (
            <TextInput
              className="mr-5"
              key={entry.name}
              label={'Name'}
              id={'themeName'}
              name={'theme_name'}
              size={50}
              defaultValue={entry.name}
              onKeyUp={(event: KeyboardEvent<HTMLInputElement>) => {
                onChangeName((event.target as HTMLInputElement).value);
              }}
              autoFocus
              error={error}
              readOnly={readOnly}
            />
          );
        }, [entry.name, error])}
      </div>
      <div className="flex items-start relative justify-between">
        <Button
          onClick={onClickSave}
          buttonStyle="primary"
          corners="theme"
          label={t('*:str_ButtonSave')}
          startIcon={<SaveIcon />}
          disabled={!entry?.dirty || error.length > 0}
          loading={saving}
          size="small"
        />
        {onExport && (
          <Button
            label={t('AdminTheming:str_LabelExport')}
            size={'small'}
            startIcon={<ArrowDownIcon />}
            onClick={() => onExport(entry)}
            buttonStyle="standard"
          />
        )}
      </div>
    </Theme>
  );
};

export default ViewHeader;
