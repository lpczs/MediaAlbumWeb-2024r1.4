import {action} from 'typesafe-actions';
import { ColourEditorContextState } from '../Context/ColourEditorContext';

export enum EditorActionTypes {
  OPEN_EDITOR = 'OPEN_EDITOR',
  CLOSE_EDITOR = 'CLOSE_EDITOR',
  SET_DOCUMENT_ROOT = 'SET_DOCUMENT_ROOT',
}

export const EditorActions = {
  closeEditor: <T>(state: Partial<ColourEditorContextState<T>>) => action(EditorActionTypes.CLOSE_EDITOR, state),
  openEditor: <T>(state: Partial<ColourEditorContextState<T>>) => action(EditorActionTypes.OPEN_EDITOR, state),
  setDocumentRoot: (root: Document | ShadowRoot) => action(EditorActionTypes.SET_DOCUMENT_ROOT, root),
};