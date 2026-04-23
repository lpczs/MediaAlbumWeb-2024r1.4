import { Button, MoreIcon, PopOut, Vertical, Horizontal, CloseIcon } from "@taopix/taopix-design-system";
import { t } from "i18next";
import React, { useState } from "react";
import { AssignmentColumnId } from "../../../../Enums";

type ColumnOptionsButtonProps = {
  columnId: AssignmentColumnId;
  componentMountPoint: Element;
  onSetsAssignmentColumnData: Function
}

const ColumnOptionsButton = ({onSetsAssignmentColumnData,columnId,componentMountPoint}:ColumnOptionsButtonProps) => {
  const [showColumnOptionsPopout, setShowColumnOptionsPopout] = useState<boolean>(false);

  const removeColumn = () => {
    setShowColumnOptionsPopout(false);
    onSetsAssignmentColumnData([],[columnId]);
  }

  return (
    <>
      <Button
        id={'columnOptionsButton-' + columnId}
        label={t('*:str_ButtonClose')}
        hideLabel
        buttonStyle="standard"
        startIcon={<MoreIcon />}
        onClick={() => setShowColumnOptionsPopout(!showColumnOptionsPopout)}
        aria-pressed={showColumnOptionsPopout}
        size={'xsmall'}
        className={'absolute top-xs right-xs'}
      />
      <PopOut
        open={showColumnOptionsPopout}
        anchorOrigin={{ vertical: Vertical.Bottom, horizontal: Horizontal.Right }}
        transformOrigin={{ vertical: Vertical.Top, horizontal: Horizontal.Right }}
        excludeIdFromClickOutside={'columnOptionsButton-' + columnId}
        shadowRoot={componentMountPoint}
        anchorId={'columnOptionsButton-' + columnId}
        onClickOutside={() => setShowColumnOptionsPopout(false)}
      >
        <Button
          label={t('AdminExperience:str_LabelRemoveColumn')}
          startIcon={<CloseIcon />}
          buttonStyle="standard"
          onClick={removeColumn}
        />
      </PopOut>
    </>
  );
};

export default ColumnOptionsButton;
