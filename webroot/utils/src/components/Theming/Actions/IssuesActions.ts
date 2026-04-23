import {action} from 'typesafe-actions';

export type IssueState = {
  open: boolean;
  issue: string;
}

export enum IssueActionTypes {
  SHOW_DIALOG = 'SHOW_DIALOG',
}

export const IssueActions = {
  toggleIssue: (state: IssueState) => action(IssueActionTypes.SHOW_DIALOG, state),
}