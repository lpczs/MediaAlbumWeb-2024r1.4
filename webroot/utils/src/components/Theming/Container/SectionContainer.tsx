import React, { ReactNode } from 'react';
import classNames from 'classnames';
import { Theme, ThemeName } from '@taopix/taopix-design-system';

export type SectionContainerProps = {
  children: ReactNode;
  className?: string;
};

const SectionContainer = ({ children, className = '' }: SectionContainerProps) => {
  return (
    <Theme name={ThemeName.Section} className={classNames('p-lg mb-xxl', className)}>
      {children}
    </Theme>
  );
};

export default SectionContainer;
