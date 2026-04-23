import {action} from 'typesafe-actions';
import { ColourScheme, ThemeType } from '../Context/ThemeContext';
import { ThemeProps } from '../../../types';

export enum ThemeActionTypes {
  SET_SCHEMA = 'SET_SCHEMA',
  SET_COLOUR_SCHEMES = 'SET_COLOUR_SCHEMES',
  SET_THEME_LIST = 'SET_THEME_LIST',
  ADD_THEME = 'ADD_THEME',
  ADD_COLOUR_SCHEME = 'ADD_COLOUR_SCHEME',
  DELETE_THEME = 'DELETE_THEME',
  DELETE_COLOUR_SCHEME = 'DELETE_COLOUR_SCHEME',
  UPDATE_THEME = 'UPDATE_THEME',
  UPDATE_COLOUR_SCHEME = 'UPDATE_COLOUR_SCHEME',
  SET_SELECTED_THEME = 'SET_SELECTED_THEME',
  SET_SELECTED_COLOUR_SCHEME = 'SET_SELECTED_COLOUR_SCHEME',
  SET_IS_LOADING = 'SET_IS_LOADING',
  ON_AFTER_SAVE = 'ON_AFTER_SAVE',
  SET_DIRTY_STATE = 'SET_DIRTY_STATE'
}

export const ThemeActions = {
  setSchema: (schema: ThemeProps) => action(ThemeActionTypes.SET_SCHEMA, schema),
  setColourSchemes:(schemes: ColourScheme[]) => action(ThemeActionTypes.SET_COLOUR_SCHEMES, schemes),
  setThemeList: (themes: ThemeType[]) => action(ThemeActionTypes.SET_THEME_LIST, themes),
  addTheme: (theme: ThemeType) => action(ThemeActionTypes.ADD_THEME, theme),
  addColourScheme:(scheme: ColourScheme) => action(ThemeActionTypes.ADD_COLOUR_SCHEME, scheme),
  deleteTheme: (theme: ThemeType) => action(ThemeActionTypes.DELETE_THEME, theme),
  deleteColourScheme:(scheme: ColourScheme) => action(ThemeActionTypes.DELETE_COLOUR_SCHEME, scheme),
  updateTheme: (theme: ThemeType) => action(ThemeActionTypes.UPDATE_THEME, theme),
  updateColourScheme:(scheme: ColourScheme) => action(ThemeActionTypes.UPDATE_COLOUR_SCHEME, scheme),
  setSelectedTheme: (themeId: number) => action(ThemeActionTypes.SET_SELECTED_THEME, themeId),
  setSelectedColourScheme: (schemeId: number) => action(ThemeActionTypes.SET_SELECTED_COLOUR_SCHEME, schemeId),
  setIsLoading: (truthy: boolean) => action(ThemeActionTypes.SET_IS_LOADING, truthy),
  onAfterSave: () => action(ThemeActionTypes.ON_AFTER_SAVE),
  setDirtyState: (dirty: boolean) => action(ThemeActionTypes.SET_DIRTY_STATE, dirty),
};