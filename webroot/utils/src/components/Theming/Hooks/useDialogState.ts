import React, { useState } from 'react';
import { EditorDialogState } from '../../../types';

const useDialogState = <T>(): [EditorDialogState<T>, (name: string, props:T) => void, () => void] => {
  const [dialogState, setDialogState] = useState<EditorDialogState<T>>({
    open: false,
    name: "",
    props: null
  });

  const onOpen = (name: string, props: T) => {
    setDialogState({
      open: true,
      name,
      props
    })
  };

  const onClose = () => {
    setDialogState({
      open: false,
      name: "",
      props: null
    });
  }

  return [dialogState, onOpen, onClose];
}

export default useDialogState;