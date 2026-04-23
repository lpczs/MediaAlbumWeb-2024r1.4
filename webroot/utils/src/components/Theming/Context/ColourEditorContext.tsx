import React, {createContext, useContext, useEffect, useMemo, useReducer, useState} from 'react';
import { ActionType } from 'typesafe-actions';
import { EditorActionTypes, EditorActions } from '../Actions/EditorActions';

type RootAction = ActionType<typeof import("../Actions/EditorActions")>;

export type ColourEditorContextState<T> = {
  section: null | 'colours' | 'buttons' | 'contentStyles'
  name: string;
  props: T;
  open: boolean;
  documentRoot: Document | ShadowRoot;
  editable: boolean;
  subTheme: string;
  contentStyle: string;
  path: string;
  onClose?: null | (() => void);
  onSave?: null | ((payload: Partial<T> | T) => void)
};

export type ContextState<T> = {
  state: ColourEditorContextState<T>;
  dispatch: React.Dispatch<RootAction>;
};

const initialState: ColourEditorContextState<string> = {
  section: null,
  name: '',
  props: '',
  open: false,
  documentRoot: null,
  editable: true,
  subTheme: '',
  path: '',
  contentStyle: '',
  onClose: null,
  onSave: null,
}

export const ColourEditorContext = createContext<ContextState<string>>({} as ContextState<string>);

const ColourEditorReducer = (state: ColourEditorContextState<any>, action: RootAction): ColourEditorContextState<any> => {
  switch (action.type) {
    case EditorActionTypes.OPEN_EDITOR: {
      return {
        ...state,
        ...action.payload
      }
    }
    case EditorActionTypes.CLOSE_EDITOR: {
      return {
        ...state,
        ...action.payload
      }
    }
    case EditorActionTypes.SET_DOCUMENT_ROOT: {
      return {
        ...state,
        documentRoot: action.payload
      }
    }
  }
}

export type ColourEditorProviderProps = {
  children: any;
  documentRoot: Document | ShadowRoot;
};

export const ColourEditorProvider = ({ children, documentRoot }: ColourEditorProviderProps) => {
  const [state, dispatch] = useReducer(ColourEditorReducer, {
    ...initialState,
  });

  useEffect(() => {
    dispatch(EditorActions.setDocumentRoot(documentRoot));
  }, [])

  const contextValue = useMemo(
    () => ({
      state,
      dispatch,
    }),
    [state]
  );

  return (
    <ColourEditorContext.Provider value={contextValue}>
      {children}
    </ColourEditorContext.Provider>
  );
};

export const useColourEditor = () => {
  const context = useContext(ColourEditorContext);
  if (context === undefined) {
    throw new Error("useColourEditor must be used within a ColourEditorProvider");
  }
  return context;
};
