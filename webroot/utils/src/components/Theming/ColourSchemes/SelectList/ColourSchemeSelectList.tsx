import React from 'react';
import { ColourScheme, ThemeType, useTheming } from '../../Context/ThemeContext';
import { useTranslation } from 'react-i18next';
import useSelectedTheme from '../../Hooks/useSelectedTheme';
import { SelectList } from '@taopix/taopix-design-system';
import { ThemeActions } from '../../Actions/ThemeActions';
import { OwnerType } from '../../../../Enums';

export type ColourSchemeSelectListProps = {
  selectedId: number | string;
  documentRoot: ShadowRoot | Document
  onChange: (scheme: ColourScheme) => void;
  id: string;
}

const ColourSchemeSelectList = ({selectedId, id, documentRoot, onChange}: ColourSchemeSelectListProps) => {
  const {state: {colourSchemes}} = useTheming();
  const selectedTheme = useSelectedTheme();
  const {t} = useTranslation();

  // build a list of themes that can be used as a dark theme
  const items = [{
    label: t('*:str_LabelNone'),
    value: String(0)
  }].concat(Object.values(colourSchemes)
    .map(t => {
      return {
        label: t.name,
        value: String(t.id),
      };
    }));

  const onListChange = (event: any) => {
    const {value} = event.target;
    if (value && colourSchemes[Number(value)]) {
      onChange(colourSchemes[Number(value)])
    }
  }

  const selectedIndex = items.findIndex(i => i.value === String(selectedId)) || 0;

  return (
    <SelectList
      id={id}
      onChange={onListChange}
      selectedIndex={selectedIndex}
      items={items}
      labelledBy={''}
      shadowRoot={documentRoot}
      size={'medium'}
      className="w-[300px]"
      disabled={OwnerType.System === selectedTheme.type || items.length === 0}
    />
  );
}

export default ColourSchemeSelectList;