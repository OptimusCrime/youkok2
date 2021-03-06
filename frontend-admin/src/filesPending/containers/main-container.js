import React from 'react';
import {connect} from 'react-redux';

import {PendingFileListingRow} from "../components/pending-file-listing-row";
import FileListingWrapper from "../../common/containers/fileListingWrapper";

const AdminFilesPendingMainContainer = ({data}) => (
  <FileListingWrapper>
    {data.map(entry => (
      <PendingFileListingRow
        key={entry.content.id}
        content={entry.content}
        pending={entry.pending}
        disabled={entry.disabled}
        course={entry.course}
      />
    ))}
  </FileListingWrapper>
);

const mapStateToProps = ({files}) => ({
  data: files.data,
});

export default connect(mapStateToProps)(AdminFilesPendingMainContainer);
