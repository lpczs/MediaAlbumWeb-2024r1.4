import React, {
  createContext,
  useContext,
  useEffect,
  useMemo,
  useReducer,
} from "react";
import { ActionType } from "typesafe-actions";
import axios from "axios";
import { useErrorBoundary } from "react-error-boundary";
import { ThemeActionTypes, ThemeActions } from "../Actions/ThemeActions";
import { ThemeProps } from "../../../types";

type RootAction = ActionType<typeof import("../Actions/ThemeActions")>;

export enum EditorType {
  Theme,
  ColourScheme,
}

export type ColourScheme = {
  id: number;
  name: string;
  type: number;
  data: ThemeProps;
  dataLength: number;
  hash: string;
  dirty: boolean;
  diff?: Partial<ThemeProps>;
}

export type ThemeType = {
  id: number;
  name: string;
  type: number;
  dateCreated: string;
  hash: string;
  darkSchemeId: number;
  defaultSchemeId: number;
  dirty: boolean;
};

export type ThemesResponse = {
  payload : {
    colourSchemeList: ColourScheme[],
    themeList: ThemeType[],
  }
  schema: ThemeProps;
}

export type ThemeState = {
  selectedThemeId: number;
  selectedSchemeId: number;
  activeEditor: EditorType;
  themes: Record<number, ThemeType>;
  colourSchemes: Record<number, ColourScheme>;
  schema: ThemeProps;
  loading: boolean;
};

export type ContextState = {
  state: ThemeState;
  dispatch: React.Dispatch<RootAction>;
};

const initialState: ThemeState = {
  selectedThemeId: null,
  selectedSchemeId: null,
  activeEditor: EditorType.Theme,
  themes: {},
  colourSchemes: {},
  schema: {} as ThemeProps,
  loading: true,
};

export const ThemeContext = createContext<ContextState>({} as ContextState);

const ThemeReducer = (state: ThemeState, action: RootAction): ThemeState => {
  switch (action.type) {
    case ThemeActionTypes.SET_SCHEMA: {
      return {
        ...state,
        schema: action.payload
      }
    }
    case ThemeActionTypes.SET_THEME_LIST: {
      return {
        ...state,
        themes: action.payload.reduce(
          (accumulator: Record<number, ThemeType>, theme: ThemeType) => {
            if (!accumulator[theme.id]) {
              accumulator[theme.id] = theme;
            }
            return accumulator;
          },
          {}
        ),
      };
    }
    case ThemeActionTypes.SET_COLOUR_SCHEMES: {
      return {
        ...state,
        colourSchemes: action.payload.reduce(
          (accumulator: Record<number, ColourScheme>, scheme: ColourScheme) => {
            if (!accumulator[scheme.id]) {
              accumulator[scheme.id] = scheme;
            }
            return accumulator;
          },
          {}
        ),
      };
    }
    case ThemeActionTypes.ADD_THEME:
    case ThemeActionTypes.UPDATE_THEME:
      return {
        ...state,
        themes: {
          ...state.themes,
          [action.payload.id]: action.payload,
        },
      };
    case ThemeActionTypes.ADD_COLOUR_SCHEME:
    case ThemeActionTypes.UPDATE_COLOUR_SCHEME:
      return {
        ...state,
        colourSchemes: {
          ...state.colourSchemes,
          [action.payload.id]: action.payload,
        },
      };
    case ThemeActionTypes.DELETE_THEME: {
      const themes = { ...state.themes };
      delete themes[action.payload.id];
      return {
        ...state,
        themes: {
          ...themes,
        },
      };
    }
    case ThemeActionTypes.DELETE_COLOUR_SCHEME: {
      const schemes = { ...state.colourSchemes };
      delete schemes[action.payload.id];
      return {
        ...state,
        colourSchemes: {
          ...schemes,
        },
      };
    }
    case ThemeActionTypes.SET_SELECTED_THEME: {
      return {
        ...state,
        selectedThemeId: action.payload,
      };
    }
    case ThemeActionTypes.SET_SELECTED_COLOUR_SCHEME: {
      return {
        ...state,
        selectedSchemeId: action.payload
      }
    }
    case ThemeActionTypes.SET_IS_LOADING: {
      return {
        ...state,
        loading: action.payload,
      };
    }
    default: {
      return {
        ...state,
      };
    }
  }
};

export type ThemeListProviderProps = {
  children: any;
};

export const ThemeListProvider = ({ children }: ThemeListProviderProps) => {
  const [state, dispatch] = useReducer(ThemeReducer, {
    ...initialState,
  });

  const { showBoundary } = useErrorBoundary();

  useEffect(() => {
    axios
      .get<ThemesResponse>("/api/theme/list")
      .then(({ data }) => {
        dispatch(ThemeActions.setSchema(data.schema));
        dispatch(ThemeActions.setThemeList(data.payload.themeList));
        dispatch(ThemeActions.setColourSchemes(data.payload.colourSchemeList))
        dispatch(ThemeActions.setIsLoading(false));
      })
      .catch((error) => {
        showBoundary({
          message: error,
        });
      });
  }, []);

  const contextValue = useMemo(
    () => ({
      state,
      dispatch,
    }),
    [state]
  );

  return (
    <ThemeContext.Provider value={contextValue}>
      {children}
    </ThemeContext.Provider>
  );
};

export const useTheming = () => {
  const context = useContext(ThemeContext);
  if (context === undefined) {
    throw new Error("useTheming must be used within a ThemeListProvider");
  }
  return context;
};
