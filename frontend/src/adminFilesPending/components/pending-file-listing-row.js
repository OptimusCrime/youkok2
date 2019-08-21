import React from 'react';

import FileListingCourseContainer from '../../adminCommon/containers/fileListingCourseContainer';
import {PendingContainer} from "./pending-container";

export const PendingFileListingRow = ({ content, disabled, pending, course }) => (
  <div className="row">
    <div className="col-md-6">
      <FileListingCourseContainer
        content={content}
        disabled={disabled}
      />
    </div>
    <div className="col-md-6">
      <PendingContainer
        pending={pending}
        disabled={disabled}
        course={course}
      />
    </div>
  </div>
);

