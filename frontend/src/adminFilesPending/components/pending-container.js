import React from 'react';

import FilesListingContainer from "../../adminCommon/containers/fileListingContent";
import {FileListingBox} from "../../adminCommon/containers/fileListingBox";

export const PendingContainer = ({pending}) => {

  return (
    <FileListingBox
      content={
        <FilesListingContainer
          files={pending}
        />
      }
    >
      Elementer som venter på godkjenning
    </FileListingBox>
  );
};
