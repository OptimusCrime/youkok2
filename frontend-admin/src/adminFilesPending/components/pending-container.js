import React from 'react';

import FileListingContainer from "../../adminCommon/containers/fileListingContent";
import {FileListingBox} from "../../adminCommon/containers/fileListingBox";

export const PendingContainer = ({pending, disabled, course}) => {

  return (
    <FileListingBox
      content={
        <FileListingContainer
          files={pending}
          disabled={disabled}
          course={course}
        />
      }
    >
      Elementer som venter på godkjenning
    </FileListingBox>
  );
};
