import React, { createContext, useContext, useEffect, useMemo, useReducer } from 'react';
import { ActionType } from 'typesafe-actions';
import { IssueActionTypes, IssueActions, IssueState } from '../Actions/IssuesActions';
import { PopOut, DialogHeader, DialogContent, DialogFooter, Button } from '@taopix/taopix-design-system';
import { t } from 'i18next';

type RootAction = ActionType<typeof import('../Actions/IssuesActions')>;

export type ContextState = {
  state: IssueState;
  dispatch: React.Dispatch<RootAction>;
};

const initialState: IssueState = {
  open: false,
  issue: '',
};

export const IssueContext = createContext<ContextState>({} as ContextState);

const ThemeReducer = (state: IssueState, action: RootAction): IssueState => {
  switch (action.type) {
    case IssueActionTypes.SHOW_DIALOG: {
      return {
        ...state,
        ...action.payload,
      };
    }
    default: {
      return {
        ...state,
      };
    }
  }
};

export type IssueProviderProps = {
  children: any;
  documentRoot: Document | Element | ShadowRoot
};

export const IssueProvider = ({ children, documentRoot}: IssueProviderProps) => {
  const [state, dispatch] = useReducer(ThemeReducer, {
    ...initialState,
  });

  const contextValue = useMemo(
    () => ({
      state,
      dispatch,
    }),
    [state]
  );

  return (
    <IssueContext.Provider value={contextValue}>
      {children}
      <PopOut
        id={'themeValidationFail'}
        onClickOutside={() => {}}
        open={state.open}
        className={'flex-col max-h-bounds max-w-bounds'}
        role="dialog"
        shadowRoot={documentRoot}
        afterClose={() => {
          dispatch(
            IssueActions.toggleIssue({
              ...state,
              issue: '',
            })
          );
        }}
        displayMode={'modal'}
      >
        <DialogHeader>{t('str_TitleWarning', { ns: '*' })}</DialogHeader>
        <DialogContent>
          <p>{state.issue}</p>
        </DialogContent>
        <DialogFooter>
          <Button
            role="button"
            buttonStyle="negative"
            label={t('str_ButtonOk', { ns: '*' })}
            onClick={() => {
              dispatch(
                IssueActions.toggleIssue({
                  ...state,
                  open: false,
                })
              );
            }}
          />
        </DialogFooter>
      </PopOut>
    </IssueContext.Provider>
  );
};

export const useIssuesDialog = () => {
  const context = useContext(IssueContext);
  if (context === undefined) {
    throw new Error('useIssuesDialog must be used within a IssueProvider');
  }
  return context;
};
