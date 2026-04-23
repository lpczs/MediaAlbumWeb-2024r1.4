import React from "react";
import { useErrorBoundary } from "react-error-boundary";
import {
  Button,
  CurvedArrowLeftIcon,
  Heading,
  Theme,
  ThemeName,
} from "@taopix/taopix-design-system";
import { useTranslation } from "react-i18next";

type ErrorFallbackProps = {
  error: Error;
};

const ErrorFallback = ({ error }: ErrorFallbackProps) => {
  const { resetBoundary } = useErrorBoundary();
  const { t } = useTranslation();

  return (
    <Theme
      name={ThemeName.Standard}
      className="h-full w-full flex items-center justify-center"
    >
      <Theme
        name={ThemeName.Critical}
        className="rounded-md h-auto w-[min(800px,calc(100vw-40px))] p-3"
      >
        <Heading level={1}>{t("*:str_Error")}</Heading>
        <div className="p-2">
          <div>{error.message}</div>
          <Button
            onClick={resetBoundary}
            buttonStyle="primary"
            corners="theme"
            label={t("*:str_ButtonReset")}
            startIcon={<CurvedArrowLeftIcon />}
            className="mr-2"
            size="small"
          />
        </div>
      </Theme>
    </Theme>
  );
};

export default ErrorFallback;
