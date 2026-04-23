import React, { LegacyRef } from 'react';
import { Button, DialogContent, DialogFooter, DialogHeader, PopOut} from '@taopix/taopix-design-system';
import { useTranslation } from 'react-i18next';
import { ExperienceType } from '../../../Enums';
import { ConfirmMessagePositiveFunction } from '../../../types';

declare global {
  interface Window { logOut: () => void, google: any; gMessageLoading: string; gLangCode: string }
}

export interface ConfirmationProps {
  sessionRef: number
  theRef: LegacyRef<HTMLDivElement>
  open: boolean
  afterClose: () => void
  componentMountPoint: Element
  positiveClick: ConfirmMessagePositiveFunction;
  negativeClick: () => void;
  positiveLabel: string
  negativeLabel: string
  message: string
  heading: string
  switchExperienceTypeState: ExperienceType
}

const popoutID = 'experienceConfirmationPopOutID';
const headerID = 'experienceConfirmationDialogHeader';
const contentID = 'experienceConfirmationDialogContent'

const ariaLabelledByAttr = { 'aria-labelledby': `${headerID}` }
const ariaDescribedByAttr = { 'aria-describedby': `${contentID}` }
const ariaAttributes = { ...ariaLabelledByAttr, ...ariaDescribedByAttr };

export const Confirmation = ({ theRef, open, afterClose, componentMountPoint, positiveClick, negativeClick, positiveLabel, negativeLabel, message, heading, switchExperienceTypeState, ...props }: ConfirmationProps) => {

  const { t } = useTranslation();

  return (
    <PopOut
      id={popoutID}
      onClickOutside={() => { }}
      open={open}
      className={'flex-col max-h-bounds max-w-bounds'}
      role="dialog"
      afterClose={afterClose}
      shadowRoot={componentMountPoint}
      displayMode={'modal'}
      {...ariaAttributes}
    >
      <DialogHeader id={headerID}>{heading}</DialogHeader>
      <DialogContent id={contentID}>
        <p>{message}</p>
      </DialogContent>
      <DialogFooter>
        <Button role="button" buttonStyle="negative" label={negativeLabel} onClick={negativeClick} />
        {
          (positiveClick.function !== null)
          ?
          <Button role="button" buttonStyle="primary" label={positiveLabel} onClick={()=>positiveClick.function(positiveClick.param[0], positiveClick.param[1])} />
          :
          null
        }
      </DialogFooter>
    </PopOut>
  )
};