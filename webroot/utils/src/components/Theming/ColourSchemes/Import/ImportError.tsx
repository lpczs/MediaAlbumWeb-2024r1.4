import { ErrorObject } from 'ajv';
import React from 'react';

export type ImportErrorProps = {
  error: ErrorObject
}

const ImportError = ({error}: ImportErrorProps) => {
  return (
    <li></li>
  );
}

export default ImportError;
