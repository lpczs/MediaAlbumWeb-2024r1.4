import React, { CSSProperties, ReactNode } from "react";
import classNames from "classnames";
import { Theme } from "@taopix/taopix-design-system";

export type ThemedContainerProps = {
  subTheme: string;
  children?: ReactNode;
  contentStyle?: string;
  style?: CSSProperties;
  className?: string;
  allowBorder?: boolean;
  allowCorners?: boolean;
  allowBackground?: boolean;
  onClick?: () => void;
};

const ThemedContainer = ({
  subTheme,
  children,
  contentStyle,
  className,
  allowBorder = true,
  allowCorners = true,
  allowBackground = true,
  style = {},
  onClick,
}: ThemedContainerProps) => {
  const combinedClassNames = classNames(["w-full", "h-full"], className);
  if (contentStyle) {
    return (
      <Theme
        name={`tpx-theme-${subTheme}`}
        className="w-full h-full"
        allowBorder={allowBorder}
        allowCorners={allowCorners}
        allowBackground={allowBackground}
        onClick={onClick}
      >
        <Theme
          name={`tds-theme-${contentStyle}`}
          allowBorder={allowBorder}
          allowCorners={allowCorners}
          allowBackground={allowBackground}
          className={combinedClassNames}
          style={style}
        >
          {children}
        </Theme>
      </Theme>
    );
  }

  return (
    <Theme
      name={`tpx-theme-${subTheme}`}
      className={combinedClassNames}
      style={style}
      allowBorder={allowBorder}
      allowCorners={allowCorners}
      allowBackground={allowBackground}
    >
      {children}
    </Theme>
  );
};

export default ThemedContainer;
