import React, { LegacyRef } from 'react';
import { Button, DialogContent, DialogFooter, DialogHeader, PopOut} from '@taopix/taopix-design-system';
import { useTranslation } from 'react-i18next';

declare global {
  interface Window { logOut: () => void, google: any; gMessageLoading: string; gLangCode: string }
}

export interface AssignmentMessageProps {
  sessionRef: number
  theRef: LegacyRef<HTMLDivElement>
  open: boolean
  afterClose: () => void
  componentMountPoint: Element
  positiveClick: () => void;
  negativeClick: () => void;
  positiveLabel: string
  negativeLabel: string
  message: string
  heading: string
}

const popoutID = 'experienceOverviewPopOutID';
const headerID = 'experienceOverviewDialogHeader';
const contentID = 'experienceOverviewDialogContent'

const ariaLabelledByAttr = { 'aria-labelledby': `${headerID}` }
const ariaDescribedByAttr = { 'aria-describedby': `${contentID}` }
const ariaAttributes = { ...ariaLabelledByAttr, ...ariaDescribedByAttr };

export const AssignmentMessage = ({ theRef, open, afterClose, componentMountPoint, positiveClick, negativeClick, positiveLabel, negativeLabel, message, heading, ...props }: AssignmentMessageProps) => {

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
        
        {
          (negativeLabel !== '')
          ?
          <Button role="button" buttonStyle="negative" label={negativeLabel} onClick={() => negativeClick() } />
          :
          <></>
        }

        <Button role="button" buttonStyle="primary" label={positiveLabel} onClick={() => positiveClick() } />
      </DialogFooter>
    </PopOut>
  )
};