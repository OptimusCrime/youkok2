import React from 'react';
import {connect} from 'react-redux';

import FileListingWrapper from "../../adminCommon/containers/fileListingWrapper";
import FileListingCourseContainer from "../../adminCommon/containers/fileListingCourseContainer";

const AdminFilesMainContainer = ({data}) => (
  <FileListingWrapper>
    {data.map(entry => (
      <div
        className="col-md-6"
        key={entry.content.id}
      >
        <FileListingCourseContainer
          content={entry.content}
          disabled={entry.disabled}
        />
      </div>
    ))}
  </FileListingWrapper>
);

const mapStateToProps = ({files}) => ({
  data: files.data,
});

export default connect(mapStateToProps)(AdminFilesMainContainer);
