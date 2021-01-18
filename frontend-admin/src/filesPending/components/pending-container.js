import React from 'react';

import FileListingContainer from "../../common/containers/fileListingContent";
import {FileListingBox} from "../../common/containers/fileListingBox";

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
